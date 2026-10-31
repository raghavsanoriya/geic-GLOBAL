<?php

namespace App\Support;

use Illuminate\Database\Connection;
use RuntimeException;

class CrmMysqlBackup
{
    /** @return array<string, int> */
    public function create(Connection $connection, string $path): array
    {
        if ($connection->getDriverName() !== 'mysql' || $connection->getTablePrefix() !== '') {
            throw new RuntimeException('The verified GEIC MySQL connection is required.');
        }
        $tables = [];
        foreach ($connection->getSchemaBuilder()->getTableListing($connection->getDatabaseName(), schemaQualified: false) as $table) {
            if ($table === 'migrations' || CrmSqliteTransfer::isApplicationTable($table)) {
                $tables[] = $table;
            } elseif (! str_starts_with($table, 'wplb_')) {
                $safeTable = preg_match('/\A[a-z][a-z0-9_]{0,62}\z/D', $table) === 1 ? $table : 'invalid_identifier';

                throw new RuntimeException('Unexpected database table: '.$safeTable.'.');
            }
        }
        if (! in_array('counselling_enquiries', $tables, true) || ! in_array('users', $tables, true)) {
            throw new RuntimeException('The application tables are missing.');
        }
        $stream = fopen($path, 'xb');
        if ($stream === false) {
            throw new RuntimeException('Cannot create private MySQL backup.');
        }
        chmod($path, 0600);
        try {
            $counts = $connection->transaction(function () use ($connection, $tables, $stream): array {
                $this->write($stream, "-- GEIC application-only backup; no DROP statements and no WordPress tables.\nSET NAMES utf8mb4;\nSET SESSION time_zone = '+00:00';\nSET SESSION sql_mode = 'NO_AUTO_VALUE_ON_ZERO,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';\nSET FOREIGN_KEY_CHECKS = 0;\n");
                $counts = [];
                foreach ($tables as $table) {
                    $identifier = $connection->getQueryGrammar()->wrapTable($table);
                    $definition = (array) $connection->selectOne('SHOW CREATE TABLE '.$identifier);
                    $this->write($stream, (string) array_values($definition)[1].";\n");
                    $columns = $connection->getSchemaBuilder()->getColumnListing($table);
                    $columnSql = implode(', ', array_map($connection->getQueryGrammar()->wrap(...), $columns));
                    $count = 0;
                    foreach ($connection->table($table)->select($columns)->cursor() as $row) {
                        $values = [];
                        foreach ($columns as $column) {
                            $value = $row->{$column};
                            $quoted = $value === null ? 'NULL' : $connection->getPdo()->quote((string) $value);
                            if ($quoted === false) {
                                throw new RuntimeException('A backup value could not be encoded.');
                            }
                            $values[] = $quoted;
                        }
                        $this->write($stream, 'INSERT INTO '.$identifier.' ('.$columnSql.') VALUES ('.implode(', ', $values).");\n");
                        $count++;
                    }
                    $counts[$table] = $count;
                }
                $this->write($stream, "SET FOREIGN_KEY_CHECKS = 1;\n-- GEIC backup completed\n");

                return $counts;
            });
        } finally {
            fclose($stream);
        }
        $observed = array_fill_keys(array_keys($counts), 0);
        $complete = false;
        $stream = fopen($path, 'rb');
        if ($stream === false) {
            throw new RuntimeException('Cannot verify private MySQL backup.');
        }
        try {
            while (($line = fgets($stream)) !== false) {
                if (preg_match('/\AINSERT INTO `([a-z][a-z0-9_]*)` /', $line, $match) === 1) {
                    if (! array_key_exists($match[1], $observed)) {
                        throw new RuntimeException('Unexpected table in the MySQL backup.');
                    }
                    $observed[$match[1]]++;
                }
                $complete = trim($line) === '-- GEIC backup completed';
            }
            if (! feof($stream) || ! $complete || $counts !== $observed) {
                throw new RuntimeException('The MySQL backup did not pass its completion and record-count checks.');
            }
        } finally {
            fclose($stream);
        }

        return $counts;
    }

    /** @param resource $stream */
    private function write(mixed $stream, string $contents): void
    {
        if (fwrite($stream, $contents) !== strlen($contents)) {
            throw new RuntimeException('MySQL backup storage is unavailable.');
        }
    }
}
