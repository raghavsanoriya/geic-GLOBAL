<?php

namespace App\Console\Commands;

use App\Support\CrmSqliteTransfer;
use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class PrepareCrmMysql extends Command
{
    protected $signature = 'crm:prepare-mysql {--snapshot= : Verified SQLite backup label for a maintenance-mode cutover} {--activate : Copy verified records and switch only the database environment settings}';

    protected $description = 'Prepare separate Laravel tables in the existing GEIC MySQL database without changing WordPress tables';

    public function handle(CrmSqliteTransfer $transfer): int
    {
        $originalEnvironment = null;
        $environmentPath = realpath(app()->environmentFilePath());
        $root = storage_path('crm-mysql');
        $lock = null;
        $environmentChanged = false;
        $stage = 'preflight';
        try {
            if ($environmentPath === false || is_link($root)) {
                throw new RuntimeException('Unsafe environment or report location.');
            }
            if (! is_dir($root) && ! mkdir($root, 0700)) {
                throw new RuntimeException('Cannot create the private preparation directory.');
            }
            if (realpath($root) !== realpath(storage_path()).DIRECTORY_SEPARATOR.'crm-mysql') {
                throw new RuntimeException('Unsafe preparation directory.');
            }
            $lock = fopen($root.'/prepare.lock', 'c+b');
            if ($lock === false || ! flock($lock, LOCK_EX | LOCK_NB)) {
                throw new RuntimeException('Another preparation is running.');
            }
            chmod($root.'/prepare.lock', 0600);
            $activate = (bool) $this->option('activate');
            $label = (string) $this->option('snapshot');
            if ($activate !== ($label !== '') || ($activate && ! app()->isDownForMaintenance())) {
                throw new RuntimeException('A data cutover requires maintenance mode and a verified snapshot label.');
            }
            $settingsPath = dirname($environmentPath).DIRECTORY_SEPARATOR.'crm-mysql.json';
            if (! is_file($settingsPath) || is_link($settingsPath)
                || (DIRECTORY_SEPARATOR === '/' && (fileperms($settingsPath) & 0077) !== 0)) {
                throw new RuntimeException('A private 0600 MySQL settings file is required beside the existing environment.');
            }
            $settings = json_decode(file_get_contents($settingsPath), true, flags: JSON_THROW_ON_ERROR);
            if (! is_array($settings) || ! in_array($settings['host'] ?? '', ['localhost', '127.0.0.1'], true)
                || preg_match('/\A[A-Za-z0-9_]{1,64}\z/D', (string) ($settings['database'] ?? '')) !== 1
                || preg_match('/\A[A-Za-z0-9_]{1,64}\z/D', (string) ($settings['username'] ?? '')) !== 1
                || ! is_string($settings['password'] ?? null) || $settings['password'] === ''
                || strlen($settings['password']) > 1024
                || filter_var($settings['port'] ?? 3306, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]) === false) {
                throw new RuntimeException('Invalid protected MySQL settings.');
            }
            if (app()->isProduction() && $settings['database'] !== 'geicii6q_wp459') {
                throw new RuntimeException('The target must be the verified GEIC main website database.');
            }
            config(['database.connections.crm_mysql_cutover' => array_replace(config('database.connections.mysql'), [
                'url' => null, 'unix_socket' => '', 'host' => $settings['host'], 'port' => $settings['port'] ?? 3306,
                'database' => $settings['database'], 'username' => $settings['username'], 'password' => $settings['password'],
                'prefix' => '', 'isolation_level' => 'REPEATABLE READ',
            ])]);
            DB::purge('crm_mysql_cutover');
            $target = DB::connection('crm_mysql_cutover');
            $stage = 'connection';
            $target->getPdo();
            $ownershipPath = $root.'/ownership.json';
            $existing = $target->getSchemaBuilder()->getTableListing($target->getDatabaseName(), schemaQualified: false);
            if (is_file($ownershipPath)) {
                $ownership = json_decode(file_get_contents($ownershipPath), true, flags: JSON_THROW_ON_ERROR);
                if (($ownership['database'] ?? null) !== $settings['database'] || ! is_array($ownership['protected_tables'] ?? null)) {
                    throw new RuntimeException('Existing preparation belongs to a different target.');
                }
            } else {
                foreach ($existing as $table) {
                    if (! str_starts_with($table, 'wplb_')) {
                        throw new RuntimeException('A new target must contain only the verified existing WordPress namespace.');
                    }
                }
                if ($existing === []) {
                    throw new RuntimeException('The expected GEIC WordPress tables are missing.');
                }
                $ownership = ['database' => $settings['database'], 'created_at' => gmdate(DATE_ATOM), 'protected_tables' => $this->protectedTables($target, $existing)];
                $handle = fopen($ownershipPath, 'xb');
                if ($handle === false) {
                    throw new RuntimeException('Cannot establish preparation ownership.');
                }
                chmod($ownershipPath, 0600);
                $contents = json_encode($ownership, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
                $written = fwrite($handle, $contents);
                fclose($handle);
                if ($written !== strlen($contents) || hash_file('sha256', $ownershipPath) !== hash('sha256', $contents)) {
                    throw new RuntimeException('Preparation ownership could not be verified.');
                }
            }
            $protected = $ownership['protected_tables'];
            if ($this->protectedTables($target, array_keys($protected)) !== $protected) {
                throw new RuntimeException('Existing WordPress data changed and requires a refreshed backup/review.');
            }
            foreach ($existing as $table) {
                if (! isset($protected[$table]) && $table !== 'migrations' && ! CrmSqliteTransfer::isApplicationTable($table)) {
                    throw new RuntimeException('An unexpected target table requires review.');
                }
            }
            $stage = 'schema';
            $this->report($root, ['state' => 'preparing', 'stage' => $stage, 'database' => $settings['database']]);
            if ($this->call('migrate', ['--database' => 'crm_mysql_cutover', '--force' => true, '--no-interaction' => true]) !== self::SUCCESS) {
                throw new RuntimeException('Target migrations did not complete.');
            }
            $report = ['state' => 'prepared', 'database' => $settings['database'], 'protected_table_count' => count($protected), 'checked_at' => gmdate(DATE_ATOM)];
            if ($activate) {
                $stage = 'snapshot';
                if (preg_match('/\A[A-Za-z0-9][A-Za-z0-9_-]{0,79}\z/D', $label) !== 1) {
                    throw new RuntimeException('Invalid backup label.');
                }
                $snapshotRoot = realpath(storage_path('crm-backups/'.$label));
                if ($snapshotRoot === false || $snapshotRoot !== realpath(storage_path()).DIRECTORY_SEPARATOR.'crm-backups'.DIRECTORY_SEPARATOR.$label) {
                    throw new RuntimeException('A private verified snapshot is required.');
                }
                $manifest = json_decode(file_get_contents($snapshotRoot.'/verified.json'), true, flags: JSON_THROW_ON_ERROR);
                if (($manifest['driver'] ?? null) !== 'sqlite'
                    || ($manifest['database_sha256'] ?? null) !== hash_file('sha256', $snapshotRoot.'/database.sqlite')
                    || ($manifest['files']['.env'] ?? null) !== hash_file('sha256', $environmentPath)
                    || ! is_file($snapshotRoot.'/.env')
                    || hash_file('sha256', $snapshotRoot.'/.env') !== hash_file('sha256', $environmentPath)) {
                    throw new RuntimeException('Snapshot or unchanged environment verification failed.');
                }
                $stage = 'copy';
                $report['tables'] = $transfer->copy($snapshotRoot.'/database.sqlite', $target);
                $report['snapshot'] = $label;
                $report['state'] = 'copied';
                $this->report($root, $report);
                if ($this->protectedTables($target, array_keys($protected)) !== $protected) {
                    throw new RuntimeException('Protected WordPress tables did not pass verification.');
                }
                $stage = 'environment';
                $originalEnvironment = file_get_contents($environmentPath);
                $values = ['DB_CONNECTION' => 'mysql', 'DB_HOST' => $settings['host'], 'DB_PORT' => (string) ($settings['port'] ?? 3306), 'DB_DATABASE' => $settings['database'], 'DB_USERNAME' => $settings['username'], 'DB_PASSWORD' => $settings['password'], 'DB_URL' => ''];
                $next = preg_replace('/^(?:DB_CONNECTION|DB_HOST|DB_PORT|DB_DATABASE|DB_USERNAME|DB_PASSWORD|DB_URL)=.*\R?/m', '', $originalEnvironment);
                foreach ($values as $key => $value) {
                    $quoted = '"'.str_replace(['\\', '"', '$', "\r", "\n"], ['\\\\', '\\"', '\\$', '\\r', '\\n'], $value).'"';
                    $next .= PHP_EOL.$key.'='.$quoted;
                }
                $parsed = Dotenv::parse($next);
                $previous = Dotenv::parse($originalEnvironment);
                foreach ($values as $key => $value) {
                    if (($parsed[$key] ?? null) !== $value) {
                        throw new RuntimeException('Environment encoding did not pass verification.');
                    }
                }
                if (($parsed['APP_KEY'] ?? null) !== ($previous['APP_KEY'] ?? null)) {
                    throw new RuntimeException('The application key must remain unchanged.');
                }
                $this->writePrivate($environmentPath, $next.PHP_EOL);
                $environmentChanged = true;
                $report['state'] = 'activated';
            }
            if ($this->protectedTables($target, array_keys($protected)) !== $protected) {
                throw new RuntimeException('Protected WordPress verification failed.');
            }
            $this->report($root, $report);
            $this->info($activate ? 'Verified MySQL copy activated. Original SQLite data and failed Foris deliveries were preserved.' : 'MySQL schema prepared. Live connection and all WordPress tables were not changed.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            if ($environmentChanged && is_string($originalEnvironment) && is_string($environmentPath)) {
                $this->writePrivate($environmentPath, $originalEnvironment);
            }
            if (is_dir($root) && ! is_link($root) && realpath($root) === realpath(storage_path()).DIRECTORY_SEPARATOR.'crm-mysql') {
                $this->report($root, ['state' => 'stopped', 'stage' => $stage, 'exception' => class_basename($exception), 'code' => preg_match('/\A[A-Z0-9]{1,10}\z/D', (string) $exception->getCode()) ? (string) $exception->getCode() : 'CHECK_REQUIRED', 'checked_at' => gmdate(DATE_ATOM)]);
            }
            $this->error('MySQL preparation stopped at '.$stage.'. No source data or WordPress tables were changed. Check the private preparation report.');

            return self::FAILURE;
        } finally {
            if (is_resource($lock)) {
                flock($lock, LOCK_UN);
                fclose($lock);
            }
            DB::purge('crm_mysql_cutover');
        }
    }

    /** @param list<string> $tables
     * @return array<string, array{rows: int, schema_sha256: string}>
     */
    private function protectedTables(Connection $target, array $tables): array
    {
        $result = [];
        foreach ($tables as $table) {
            if (preg_match('/\Awplb_[a-zA-Z0-9_]+\z/D', $table) !== 1) {
                throw new RuntimeException('Invalid protected table namespace.');
            }
            $definition = (array) $target->selectOne('SHOW CREATE TABLE '.$target->getQueryGrammar()->wrapTable($table));
            $result[$table] = ['rows' => $target->table($table)->count(), 'schema_sha256' => hash('sha256', (string) array_values($definition)[1])];
        }
        ksort($result);

        return $result;
    }

    /** @param array<string, mixed> $report */
    private function report(string $root, array $report): void
    {
        $this->writePrivate($root.'/report.json', json_encode($report, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }

    private function writePrivate(string $path, string $contents): void
    {
        if (is_link($path)) {
            throw new RuntimeException('Refusing to replace a linked private file.');
        }
        $temporary = $path.'.next-'.bin2hex(random_bytes(8));
        $handle = fopen($temporary, 'xb');
        if ($handle === false) {
            throw new RuntimeException('Cannot write private preparation state.');
        }
        chmod($temporary, 0600);
        $written = fwrite($handle, $contents);
        fclose($handle);
        if ($written !== strlen($contents) || hash_file('sha256', $temporary) !== hash('sha256', $contents) || ! rename($temporary, $path)) {
            throw new RuntimeException('Private preparation state failed verification.');
        }
    }
}
