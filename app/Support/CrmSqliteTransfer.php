<?php

namespace App\Support;

use Illuminate\Database\Connection;
use RuntimeException;
use SQLite3;
use Throwable;

class CrmSqliteTransfer
{
    private const BASE_TABLES = ['users', 'password_reset_tokens', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'counselling_enquiries', 'site_contents', 'site_events', 'cms_page_states', 'cms_pages', 'cms_forms', 'media_assets', 'media_folders', 'blog_posts', 'ad_accounts', 'ad_campaigns', 'ad_performance', 'foris_lead_deliveries', 'lead_contact_keys', 'lead_duplicate_reviews'];

    public static function isApplicationTable(string $name): bool
    {
        return preg_match('/\A[a-z][a-z0-9_]{0,62}\z/D', $name) === 1
            && (in_array($name, self::BASE_TABLES, true) || str_starts_with($name, 'crm_'));
    }

    /** @return array<string, array{rows: int, sha256: string}> */
    public function copy(string $snapshotPath, Connection $target): array
    {
        if (! is_file($snapshotPath) || filesize($snapshotPath) > 100 * 1024 * 1024) {
            throw new RuntimeException('Use a verified SQLite snapshot no larger than 100 MB.');
        }
        $before = hash_file('sha256', $snapshotPath);
        $source = new SQLite3($snapshotPath, SQLITE3_OPEN_READONLY);
        $source->enableExceptions(true);
        try {
            if ($source->querySingle('PRAGMA integrity_check') !== 'ok') {
                throw new RuntimeException('The source snapshot did not pass its integrity check.');
            }
            $tables = [];
            $rows = $source->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name <> 'migrations' ORDER BY rowid");
            while ($row = $rows->fetchArray(SQLITE3_ASSOC)) {
                if (! self::isApplicationTable($row['name'])) {
                    throw new RuntimeException('An unexpected source table requires manual review.');
                }
                $tables[] = $row['name'];
            }
            if ($tables === []) {
                throw new RuntimeException('The source snapshot has no application tables.');
            }
            $definitions = [];
            foreach ($tables as $table) {
                $columns = [];
                $primary = [];
                $foreign = [];
                $rows = $source->query('PRAGMA table_info('.$this->quote($table).')');
                while ($column = $rows->fetchArray(SQLITE3_ASSOC)) {
                    $columns[] = $column['name'];
                    if ($column['pk'] > 0) {
                        $primary[$column['pk']] = $column['name'];
                    }
                }
                ksort($primary);
                $rows = $source->query('PRAGMA foreign_key_list('.$this->quote($table).')');
                while ($key = $rows->fetchArray(SQLITE3_ASSOC)) {
                    if ($key['table'] !== $table && in_array($key['table'], $tables, true)) {
                        $foreign[] = $key['table'];
                    }
                }
                $schema = $target->getSchemaBuilder();
                if (! $schema->hasTable($table) || array_diff($columns, $schema->getColumnListing($table)) !== []) {
                    throw new RuntimeException('The target schema is missing source columns.');
                }
                $json = array_column(array_filter($schema->getColumns($table), fn (array $column): bool => in_array($column['type_name'], ['json', 'jsonb'], true)), 'name');
                $definitions[$table] = compact('columns', 'primary', 'foreign', 'json');
            }
            $ordered = [];
            while (count($ordered) < count($tables)) {
                $progress = false;
                foreach ($tables as $table) {
                    if (! in_array($table, $ordered, true) && array_diff($definitions[$table]['foreign'], $ordered) === []) {
                        $ordered[] = $table;
                        $progress = true;
                    }
                }
                if (! $progress) {
                    throw new RuntimeException('Circular table dependencies require manual review.');
                }
            }

            return $target->transaction(function () use ($source, $target, $ordered, $definitions, $snapshotPath, $before): array {
                $expected = [];
                $occupied = false;
                foreach ($ordered as $table) {
                    $definition = $definitions[$table];
                    $expected[$table] = $this->fingerprint($this->sourceRows($source, $table, $definition['primary']), $definition['columns'], $definition['json']);
                    $occupied = $occupied || $target->table($table)->exists();
                }
                if ($occupied) {
                    foreach ($ordered as $table) {
                        $definition = $definitions[$table];
                        if ($expected[$table] !== $this->fingerprint($target->table($table)->select($definition['columns'])->cursor(), $definition['columns'], $definition['json'])) {
                            throw new RuntimeException('The target contains different records. Nothing was overwritten.');
                        }
                    }

                    return $expected;
                }
                foreach ($ordered as $table) {
                    $batch = [];
                    foreach ($this->sourceRows($source, $table, $definitions[$table]['primary']) as $row) {
                        $batch[] = $row;
                        if (count($batch) === 100) {
                            $target->table($table)->insert($batch);
                            $batch = [];
                        }
                    }
                    if ($batch !== []) {
                        $target->table($table)->insert($batch);
                    }
                }
                foreach ($ordered as $table) {
                    $definition = $definitions[$table];
                    $actual = $this->fingerprint($target->table($table)->select($definition['columns'])->cursor(), $definition['columns'], $definition['json']);
                    if ($actual !== $expected[$table]) {
                        throw new RuntimeException('Copied records did not pass verification.');
                    }
                }
                if ($before !== hash_file('sha256', $snapshotPath)) {
                    throw new RuntimeException('The source snapshot changed during verification.');
                }

                return $expected;
            });
        } catch (Throwable) {
            throw new RuntimeException('Verified database copy stopped. Target changes were rolled back; source records were not changed.');
        } finally {
            $source->close();
        }
    }

    /** @param array<int, string> $primary
     * @return \Generator<int, array<string, mixed>>
     */
    private function sourceRows(SQLite3 $source, string $table, array $primary): \Generator
    {
        $order = $primary === [] ? 'rowid' : implode(', ', array_map($this->quote(...), $primary));
        $rows = $source->query('SELECT * FROM '.$this->quote($table).' ORDER BY '.$order);
        while ($row = $rows->fetchArray(SQLITE3_ASSOC)) {
            yield $row;
        }
        $rows->finalize();
    }

    /** @param iterable<array<string, mixed>|object> $rows
     * @param  list<string>  $columns
     * @param  list<string>  $jsonColumns
     * @return array{rows: int, sha256: string}
     */
    private function fingerprint(iterable $rows, array $columns, array $jsonColumns): array
    {
        $hashes = [];
        foreach ($rows as $row) {
            $row = (array) $row;
            $values = [];
            foreach ($columns as $column) {
                $value = $row[$column];
                $values[$column] = $value === null ? null : (string) $value;
                if ($value !== null && in_array($column, $jsonColumns, true)) {
                    $values[$column] = $this->canonicalJson(json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING));
                }
            }
            $hashes[] = hash('sha256', serialize($values));
        }
        sort($hashes, SORT_STRING);

        return ['rows' => count($hashes), 'sha256' => hash('sha256', implode('', $hashes))];
    }

    private function canonicalJson(mixed $value): mixed
    {
        if (is_array($value)) {
            if (! array_is_list($value)) {
                ksort($value);
            }

            return array_map($this->canonicalJson(...), $value);
        }

        return $value;
    }

    private function quote(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }
}
