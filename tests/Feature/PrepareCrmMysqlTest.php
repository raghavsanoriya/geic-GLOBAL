<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\CrmSqliteTransfer;
use Dotenv\Dotenv;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SQLite3;
use Tests\TestCase;

class PrepareCrmMysqlTest extends TestCase
{
    private ?string $fixture = null;

    private ?string $mysqlDatabase = null;

    private ?string $mysqlOwner = null;

    protected function tearDown(): void
    {
        if ($this->mysqlDatabase !== null && app()->environment('testing')
            && preg_match('/\Ageic_cutover_fixture_[a-z0-9]{12}\z/D', $this->mysqlDatabase) === 1
            && DB::connection('cutover_fixture_admin')->getDatabaseName() === 'geic_crm_tests'
            && DB::connection('cutover_fixture_target')->table('wplb_options')->where('option_name', 'owned-test-fixture')->value('option_value') === $this->mysqlOwner) {
            DB::purge('crm_mysql_cutover');
            DB::purge('cutover_fixture_target');
            DB::connection('cutover_fixture_admin')->statement('DROP DATABASE `'.$this->mysqlDatabase.'`');
        }
        foreach (['cutover_fixture_admin', 'cutover_fixture_target', 'cutover_source'] as $connection) {
            DB::purge($connection);
        }
        if ($this->fixture !== null && is_file($this->fixture.'/owned-test-fixture')
            && str_starts_with((string) realpath($this->fixture), realpath(base_path('storage/framework/testing')).DIRECTORY_SEPARATOR)) {
            File::deleteDirectory($this->fixture);
        }
        parent::tearDown();
    }

    public function test_missing_private_settings_cannot_change_the_existing_environment(): void
    {
        $this->fixture();
        $before = File::get(app()->environmentFilePath());

        $this->artisan('crm:prepare-mysql')->assertFailed();

        $this->assertSame($before, File::get(app()->environmentFilePath()));
        $report = json_decode(File::get(storage_path('crm-mysql/report.json')), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('stopped', $report['state']);
        $this->assertSame('preflight', $report['stage']);
    }

    public function test_cutover_without_maintenance_mode_is_refused_before_any_database_connection(): void
    {
        $this->fixture();
        $before = File::get(app()->environmentFilePath());

        $this->artisan('crm:prepare-mysql', ['--activate' => true, '--snapshot' => 'not-available'])->assertFailed();

        $this->assertSame($before, File::get(app()->environmentFilePath()));
        $this->assertFileDoesNotExist(storage_path('crm-mysql/ownership.json'));
    }

    public function test_mysql_preparation_is_repeatable_and_keeps_wordpress_and_live_configuration_unchanged(): void
    {
        $this->mysqlFixture();
        $before = File::get(app()->environmentFilePath());

        $this->prepareMysql();
        $this->prepareMysql();

        $this->assertSame($before, File::get(app()->environmentFilePath()));
        $this->assertSame('https://geic.in', DB::connection('cutover_fixture_target')->table('wplb_options')->where('option_name', 'siteurl')->value('option_value'));
        $this->assertSame(0, DB::connection('cutover_fixture_target')->table('users')->count());
        $this->assertTrue(DB::connection('cutover_fixture_target')->getSchemaBuilder()->hasTable('crm_visas'));
        $report = json_decode(File::get(storage_path('crm-mysql/report.json')), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('prepared', $report['state']);
        $this->assertSame(1, $report['protected_table_count']);
    }

    public function test_verified_maintenance_mode_cutover_preserves_source_and_application_key(): void
    {
        $this->mysqlFixture();
        $source = $this->fixture.'/source.sqlite';
        $user = User::factory()->make(['id' => 17, 'is_admin' => true, 'admin_role' => 'super_admin'])->getAttributes();
        $database = new SQLite3($source);
        $columns = array_keys($user);
        $database->exec('CREATE TABLE users ('.implode(', ', array_map(fn (string $column): string => '"'.$column.'" '.($column === 'id' ? 'INTEGER PRIMARY KEY' : 'TEXT'), $columns)).')');
        $statement = $database->prepare('INSERT INTO users VALUES ('.implode(', ', array_fill(0, count($user), '?')).')');
        foreach (array_values($user) as $index => $value) {
            $statement->bindValue($index + 1, $value, $value === null ? SQLITE3_NULL : (is_int($value) || is_bool($value) ? SQLITE3_INTEGER : SQLITE3_TEXT));
        }
        $statement->execute()->finalize();
        $statement->close();
        $database->close();
        $hash = hash_file('sha256', $source);
        config(['database.default' => 'cutover_source', 'database.connections.cutover_source' => ['driver' => 'sqlite', 'database' => $source, 'prefix' => '']]);
        $this->artisan('crm:backup-database', ['--label' => 'cutover-fixture', '--include-files' => true])->assertSuccessful();
        app()->maintenanceMode()->activate(['retry' => 60]);

        $this->prepareMysql(['--activate' => true, '--snapshot' => 'cutover-fixture']);

        $environment = Dotenv::parse(File::get(app()->environmentFilePath()));
        $this->assertSame('original-fixture-key', $environment['APP_KEY']);
        $this->assertSame('mysql', $environment['DB_CONNECTION']);
        $this->assertSame($this->mysqlDatabase, $environment['DB_DATABASE']);
        $this->assertSame($hash, hash_file('sha256', $source));
        $this->assertSame($user['password'], DB::connection('cutover_fixture_target')->table('users')->where('id', 17)->value('password'));
        $this->assertSame('https://geic.in', DB::connection('cutover_fixture_target')->table('wplb_options')->where('option_name', 'siteurl')->value('option_value'));
        $report = json_decode(File::get(storage_path('crm-mysql/report.json')), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('activated', $report['state']);
        $this->assertSame(1, $report['tables']['users']['rows']);
    }

    private function fixture(): void
    {
        $this->fixture = base_path('storage/framework/testing/crm-mysql-'.Str::uuid());
        File::ensureDirectoryExists($this->fixture.'/storage/framework', 0700);
        File::ensureDirectoryExists($this->fixture.'/storage/app/private', 0700);
        File::ensureDirectoryExists($this->fixture.'/environment', 0700);
        File::put($this->fixture.'/owned-test-fixture', 'Disposable PrepareCrmMysqlTest fixture');
        app()->useStoragePath($this->fixture.'/storage');
        app()->useEnvironmentPath($this->fixture.'/environment');
        File::put(app()->environmentFilePath(), "APP_KEY=original-fixture-key\nDB_CONNECTION=sqlite\n");
    }

    public function test_mysql_backup_restores_exact_records_without_exporting_or_changing_wordpress_tables(): void
    {
        $this->mysqlFixture();
        $this->prepareMysql();
        $target = DB::connection('cutover_fixture_target');
        $user = User::factory()->make(['id' => 17, 'name' => "O'Reilly; DROP TABLE wplb_options; --\nUnicode ✓"])->getAttributes();
        $target->table('users')->insert($user);
        config(['database.default' => 'cutover_fixture_target']);
        $beforeEnvironment = File::get(app()->environmentFilePath());

        $this->artisan('crm:backup-database', ['--label' => 'mysql-roundtrip', '--include-files' => true])->assertSuccessful();

        $root = storage_path('crm-backups/mysql-roundtrip');
        $manifest = json_decode(File::get($root.'/verified.json'), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('mysql', $manifest['driver']);
        $this->assertSame(1, $manifest['tables']['users']);
        $this->assertArrayNotHasKey('wplb_options', $manifest['tables']);
        $this->assertSame(hash_file('sha256', $root.'/database.sql'), $manifest['database_sha256']);
        $sql = File::get($root.'/database.sql');
        $this->assertStringNotContainsString('CREATE TABLE `wplb_', $sql);
        $this->assertStringNotContainsString('INSERT INTO `wplb_', $sql);
        $this->assertSame($this->mysqlOwner, $target->table('wplb_options')->where('option_name', 'owned-test-fixture')->value('option_value'));
        $this->assertSame($this->mysqlDatabase, $target->getDatabaseName());
        $this->assertTrue(app()->environment('testing'));
        $target->getSchemaBuilder()->disableForeignKeyConstraints();
        foreach (array_keys($manifest['tables']) as $table) {
            $this->assertTrue($table === 'migrations' || CrmSqliteTransfer::isApplicationTable($table));
            $target->getSchemaBuilder()->drop($table);
        }

        $target->unprepared($sql);

        $this->assertSame($user['name'], $target->table('users')->where('id', 17)->value('name'));
        $this->assertSame($user['password'], $target->table('users')->where('id', 17)->value('password'));
        $this->assertSame('https://geic.in', $target->table('wplb_options')->where('option_name', 'siteurl')->value('option_value'));
        $this->assertSame($beforeEnvironment, File::get($root.'/.env'));
        $this->assertSame($beforeEnvironment, File::get(app()->environmentFilePath()));
        foreach ($manifest['tables'] as $table => $count) {
            $this->assertSame($count, $target->table($table)->count());
        }
    }

    private function mysqlFixture(): void
    {
        if (! app()->environment('testing') || config('database.default') !== 'mysql'
            || config('database.connections.mysql.database') !== 'geic_crm_tests'
            || config('database.connections.mysql.host') !== '127.0.0.1') {
            $this->markTestSkipped('Only the isolated MySQL CI service can create this disposable database.');
        }
        $this->fixture();
        $configuration = config('database.connections.mysql');
        config(['database.connections.cutover_fixture_admin' => $configuration]);
        $this->mysqlDatabase = 'geic_cutover_fixture_'.Str::lower(Str::random(12));
        DB::connection('cutover_fixture_admin')->statement('CREATE DATABASE `'.$this->mysqlDatabase.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        config(['database.connections.cutover_fixture_target' => array_replace($configuration, ['database' => $this->mysqlDatabase])]);
        $target = DB::connection('cutover_fixture_target');
        $target->statement('CREATE TABLE wplb_options (option_name VARCHAR(80) PRIMARY KEY, option_value TEXT NOT NULL)');
        $this->mysqlOwner = (string) Str::uuid();
        $target->table('wplb_options')->insert([['option_name' => 'siteurl', 'option_value' => 'https://geic.in'], ['option_name' => 'owned-test-fixture', 'option_value' => $this->mysqlOwner]]);
        File::put(dirname(app()->environmentFilePath()).'/crm-mysql.json', json_encode(['host' => '127.0.0.1', 'port' => 3306, 'database' => $this->mysqlDatabase, 'username' => $configuration['username'], 'password' => $configuration['password']], JSON_THROW_ON_ERROR));
        chmod(dirname(app()->environmentFilePath()).'/crm-mysql.json', 0600);
    }

    private function prepareMysql(array $options = []): void
    {
        $exit = Artisan::call('crm:prepare-mysql', $options);
        $report = File::exists(storage_path('crm-mysql/report.json')) ? File::get(storage_path('crm-mysql/report.json')) : 'No preparation report';
        $this->assertSame(0, $exit, Artisan::output().$report);
    }
}
