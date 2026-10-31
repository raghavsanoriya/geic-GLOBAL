<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SQLite3;
use Tests\TestCase;

class CrmDatabaseBackupTest extends TestCase
{
    private ?string $fixture = null;

    protected function tearDown(): void
    {
        DB::purge('backup_fixture');
        if ($this->fixture !== null && is_file($this->fixture.DIRECTORY_SEPARATOR.'owned-test-fixture')) {
            File::deleteDirectory($this->fixture);
        }
        parent::tearDown();
    }

    public function test_verified_backup_restores_original_ids_and_preserves_private_uploads_and_existing_key(): void
    {
        $source = $this->fixture();
        $database = new SQLite3($source);
        $database->exec('CREATE TABLE leads (id INTEGER PRIMARY KEY, name TEXT); INSERT INTO leads VALUES (281, "Original student");');
        $database->close();
        File::ensureDirectoryExists(storage_path('app/private/documents'), 0700);
        File::put(storage_path('app/private/documents/test.pdf'), 'private-original-fixture');
        File::put(app()->environmentFilePath(), 'APP_KEY=unchanged-test-key');

        $this->artisan('crm:backup-database', ['--label' => 'verified-fixture', '--include-files' => true])->assertSuccessful();

        $backupRoot = storage_path('crm-backups/verified-fixture');
        $restored = new SQLite3($backupRoot.'/database.sqlite', SQLITE3_OPEN_READONLY);
        $this->assertSame(['id' => 281, 'name' => 'Original student'], $restored->querySingle('SELECT * FROM leads', true));
        $this->assertSame('ok', $restored->querySingle('PRAGMA integrity_check'));
        $restored->close();
        $manifest = json_decode(File::get($backupRoot.'/verified.json'), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame(['leads' => 1], $manifest['tables']);
        $this->assertSame(hash_file('sha256', $backupRoot.'/database.sqlite'), $manifest['database_sha256']);
        $this->assertSame(['database' => hash_file('sha256', $source), 'wal' => null], $manifest['source_state']);
        $this->assertSame('private-original-fixture', File::get($backupRoot.'/app/private/documents/test.pdf'));
        $this->assertSame('APP_KEY=unchanged-test-key', File::get($backupRoot.'/.env'));
        $this->assertSame('APP_KEY=unchanged-test-key', File::get(app()->environmentFilePath()));
        $this->assertSame(1, DB::table('leads')->count());
    }

    public function test_repeated_label_cannot_overwrite_a_verified_snapshot(): void
    {
        $source = $this->fixture();
        $database = new SQLite3($source);
        $database->exec('CREATE TABLE leads (id INTEGER PRIMARY KEY); INSERT INTO leads VALUES (7);');
        $database->close();
        $this->artisan('crm:backup-database', ['--label' => 'same-label'])->assertSuccessful();
        $before = hash_file('sha256', storage_path('crm-backups/same-label/database.sqlite'));
        DB::table('leads')->insert(['id' => 9]);

        $this->artisan('crm:backup-database', ['--label' => 'same-label'])->assertFailed();

        $this->assertSame($before, hash_file('sha256', storage_path('crm-backups/same-label/database.sqlite')));
        $this->assertSame(2, DB::table('leads')->count());
    }

    public function test_invalid_labels_or_non_file_databases_are_rejected_without_a_snapshot(): void
    {
        $source = $this->fixture();
        $database = new SQLite3($source);
        $database->exec('CREATE TABLE leads (id INTEGER PRIMARY KEY);');
        $database->close();

        $this->artisan('crm:backup-database', ['--label' => '../escape'])->assertFailed();
        config(['database.connections.backup_fixture.database' => ':memory:']);
        DB::purge('backup_fixture');
        $this->artisan('crm:backup-database', ['--label' => 'memory'])->assertFailed();

        $this->assertDirectoryDoesNotExist(storage_path('crm-backups'));
    }

    public function test_failed_file_backup_never_claims_a_verified_snapshot(): void
    {
        $source = $this->fixture();
        $database = new SQLite3($source);
        $database->exec('CREATE TABLE leads (id INTEGER PRIMARY KEY); INSERT INTO leads VALUES (42);');
        $database->close();

        $this->artisan('crm:backup-database', ['--label' => 'missing-environment', '--include-files' => true])
            ->expectsOutputToContain('required_backup_file_unavailable')
            ->assertFailed();

        $this->assertFileDoesNotExist(storage_path('crm-backups/missing-environment/verified.json'));
        $this->assertSame(1, DB::table('leads')->count());
    }

    private function fixture(): string
    {
        $this->fixture = storage_path('framework/testing/crm-backup-'.Str::uuid());
        File::ensureDirectoryExists($this->fixture.'/storage', 0700);
        File::ensureDirectoryExists($this->fixture.'/environment', 0700);
        File::put($this->fixture.'/owned-test-fixture', 'Disposable fixture created by CrmDatabaseBackupTest');
        app()->useStoragePath($this->fixture.'/storage');
        app()->useEnvironmentPath($this->fixture.'/environment');
        $source = $this->fixture.'/original.sqlite';
        touch($source);
        config(['database.default' => 'backup_fixture', 'database.connections.backup_fixture' => ['driver' => 'sqlite', 'database' => $source, 'prefix' => '']]);

        return $source;
    }
}
