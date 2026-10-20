<?php

namespace App\Console\Commands;

use App\Support\CrmMysqlBackup;
use App\Support\CrmSqliteTransfer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SQLite3;
use Throwable;

class BackupCrmDatabase extends Command
{
    protected $signature = 'crm:backup-database {--label= : Unique safe backup label} {--include-files : Preserve the existing environment and application uploads too}';

    protected $description = 'Verify a private SQLite or application-only MySQL snapshot before release migrations';

    public function handle(CrmMysqlBackup $mysqlBackup): int
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        if (! in_array($driver, ['sqlite', 'mysql'], true) || ($driver === 'sqlite' && ! class_exists(SQLite3::class))) {
            $this->error('A supported SQLite or MySQL connection is required. No migration may run without a verified database backup.');

            return self::FAILURE;
        }
        $sourcePath = $driver === 'sqlite' ? realpath($connection->getDatabaseName()) : null;
        if ($driver === 'sqlite' && ($sourcePath === false || ! is_file($sourcePath))) {
            $this->error('A file-backed SQLite database is required. The live database was not changed.');

            return self::FAILURE;
        }
        $label = (string) ($this->option('label') ?: gmdate('Ymd\THis\Z').'-'.bin2hex(random_bytes(6)));
        if (preg_match('/\A[A-Za-z0-9][A-Za-z0-9_-]{0,79}\z/D', $label) !== 1) {
            $this->error('Use a backup label of 1–80 letters, numbers, hyphens or underscores.');

            return self::FAILURE;
        }
        $storageRoot = realpath(storage_path());
        $backupRoot = storage_path('crm-backups');
        if ($storageRoot === false || is_link($backupRoot)) {
            $this->error('The private backup directory is unsafe.');

            return self::FAILURE;
        }
        if (! is_dir($backupRoot) && ! mkdir($backupRoot, 0700)) {
            $this->error('The private backup directory could not be created.');

            return self::FAILURE;
        }
        if (realpath($backupRoot) !== $storageRoot.DIRECTORY_SEPARATOR.'crm-backups') {
            $this->error('The backup directory must remain inside application storage.');

            return self::FAILURE;
        }
        $destination = $backupRoot.DIRECTORY_SEPARATOR.$label;
        if (file_exists($destination) || is_link($destination) || ! mkdir($destination, 0700)) {
            $this->error('That backup label already exists or cannot be created. Existing backups were not overwritten.');

            return self::FAILURE;
        }
        $source = null;
        $backup = null;
        try {
            $sourceState = $driver === 'sqlite' ? CrmSqliteTransfer::sourceState($sourcePath) : null;
            $databasePath = $destination.DIRECTORY_SEPARATOR.($driver === 'sqlite' ? 'database.sqlite' : 'database.sql');
            if ($driver === 'mysql') {
                $tables = $mysqlBackup->create($connection, $databasePath);
            } else {
                $handle = fopen($databasePath, 'xb');
                if ($handle === false) {
                    throw new RuntimeException('Cannot create backup.');
                }
                fclose($handle);
                chmod($databasePath, 0600);
                $source = new SQLite3($sourcePath, SQLITE3_OPEN_READONLY);
                $source->enableExceptions(true);
                $source->busyTimeout(5000);
                $backup = new SQLite3($databasePath, SQLITE3_OPEN_READWRITE);
                $backup->enableExceptions(true);
                if (! $source->backup($backup) || $backup->querySingle('PRAGMA integrity_check') !== 'ok') {
                    throw new RuntimeException('Backup integrity check failed.');
                }
                $tables = [];
                $result = $backup->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
                while ($table = $result->fetchArray(SQLITE3_ASSOC)) {
                    $identifier = '"'.str_replace('"', '""', $table['name']).'"';
                    $tables[$table['name']] = (int) $backup->querySingle('SELECT COUNT(*) FROM '.$identifier);
                }
                $backup->close();
                $backup = null;
                $source->close();
                $source = null;
            }
            $files = [];
            if ($this->option('include-files')) {
                $this->copyVerified(app()->environmentFilePath(), $destination.DIRECTORY_SEPARATOR.'.env');
                $files['.env'] = hash_file('sha256', $destination.DIRECTORY_SEPARATOR.'.env');
                $uploads = storage_path('app');
                if (is_link($uploads)) {
                    throw new RuntimeException('Application upload root must not be a symbolic link.');
                }
                if (is_dir($uploads)) {
                    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploads, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
                    foreach ($iterator as $entry) {
                        if ($entry->isLink()) {
                            throw new RuntimeException('A linked upload requires a separate verified backup.');
                        }
                        $relative = 'app'.DIRECTORY_SEPARATOR.substr($entry->getPathname(), strlen($uploads) + 1);
                        $target = $destination.DIRECTORY_SEPARATOR.$relative;
                        if ($entry->isDir()) {
                            if (! is_dir($target) && ! mkdir($target, 0700, true)) {
                                throw new RuntimeException('Cannot preserve upload directory.');
                            }
                        } elseif ($entry->isFile()) {
                            if (! is_dir(dirname($target)) && ! mkdir(dirname($target), 0700, true)) {
                                throw new RuntimeException('Cannot preserve upload directory.');
                            }
                            $this->copyVerified($entry->getPathname(), $target);
                            $files[str_replace(DIRECTORY_SEPARATOR, '/', $relative)] = hash_file('sha256', $target);
                        } else {
                            throw new RuntimeException('Unsupported upload entry.');
                        }
                    }
                }
            }
            if ($driver === 'sqlite' && $sourceState !== CrmSqliteTransfer::sourceState($sourcePath)) {
                throw new RuntimeException('The original source changed during backup. Retry after active writers finish.');
            }
            $manifest = ['format' => $driver === 'sqlite' ? 1 : 2, 'driver' => $driver, 'source_state' => $sourceState, 'created_at' => gmdate(DATE_ATOM),
                'database_sha256' => hash_file('sha256', $databasePath), 'tables' => $tables, 'files' => $files];
            $manifestPath = $destination.DIRECTORY_SEPARATOR.'verified.json';
            if (file_put_contents($manifestPath, json_encode($manifest, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT), LOCK_EX) === false) {
                throw new RuntimeException('Cannot verify backup manifest.');
            }
            chmod($manifestPath, 0600);
            $this->info('Verified private backup '.$label.'; '.count($tables).' tables. No original records or credentials were changed.');

            return self::SUCCESS;
        } catch (Throwable) {
            $this->error('Backup verification failed. Partial files were retained privately; stop deployment and inspect them before retrying with a new label.');

            return self::FAILURE;
        } finally {
            $backup?->close();
            $source?->close();
        }
    }

    private function copyVerified(string $source, string $destination): void
    {
        if (! is_file($source) || ! is_readable($source)) {
            throw new RuntimeException('A required backup file is unavailable.');
        }
        $before = hash_file('sha256', $source);
        $input = fopen($source, 'rb');
        $output = fopen($destination, 'xb');
        if ($input === false || $output === false) {
            throw new RuntimeException('Cannot preserve file.');
        }
        try {
            chmod($destination, 0600);
            if (stream_copy_to_stream($input, $output) === false) {
                throw new RuntimeException('Cannot preserve file.');
            }
        } finally {
            fclose($input);
            fclose($output);
        }
        if ($before === false || $before !== hash_file('sha256', $destination) || $before !== hash_file('sha256', $source)) {
            throw new RuntimeException('File changed during backup.');
        }
    }
}
