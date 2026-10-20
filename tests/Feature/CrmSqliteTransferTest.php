<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use App\Support\BlogCatalog;
use App\Support\CrmSqliteTransfer;
use App\Support\CrmSqliteTransferException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use RuntimeException;
use SQLite3;
use Tests\TestCase;

class CrmSqliteTransferTest extends TestCase
{
    use RefreshDatabase;

    private ?string $fixture = null;

    protected function tearDown(): void
    {
        if ($this->fixture !== null && is_file($this->fixture.'/owned-test-fixture')
            && str_starts_with((string) realpath($this->fixture), realpath(storage_path('framework/testing')).DIRECTORY_SEPARATOR)) {
            File::deleteDirectory($this->fixture);
        }
        parent::tearDown();
    }

    public function test_copy_preserves_original_ids_credentials_metadata_and_failed_deliveries_without_resending(): void
    {
        Http::preventStrayRequests();
        Queue::fake();
        $this->freezeTime();
        $user = User::factory()->make(['id' => 7, 'is_admin' => true, 'admin_role' => 'super_admin'])->getAttributes();
        $enquiry = ['id' => 281, 'full_name' => 'Original transfer fixture', 'email' => 'transfer@example.test', 'phone' => '+919876543210', 'destination' => 'Germany', 'city' => 'Indore', 'study_level' => 'Postgraduate', 'preferred_intake' => 'July 2027', 'english_test' => 'IELTS', 'source_page' => '/contact', 'source' => 'website', 'metadata' => '{"qualification":"Bachelor","passing_year":"2026"}', 'created_at' => '2026-10-20 08:00:00', 'updated_at' => '2026-10-20 08:00:00'];
        $snapshot = $this->snapshot(['users' => [$user], 'counselling_enquiries' => [$enquiry], 'foris_lead_deliveries' => [['id' => 44, 'enquiry_id' => 281, 'status' => 'failed', 'attempts' => 3, 'remote_lead_id' => 'original-foris-reference']]]);
        $before = hash_file('sha256', $snapshot);

        $report = app(CrmSqliteTransfer::class)->copy($snapshot, DB::connection());

        $this->assertDatabaseHas('users', ['id' => 7, 'email' => $user['email'], 'password' => $user['password'], 'is_admin' => 1, 'admin_role' => 'super_admin']);
        $this->assertDatabaseHas('counselling_enquiries', ['id' => 281, 'full_name' => 'Original transfer fixture', 'created_at' => '2026-10-20 08:00:00']);
        $metadata = json_decode(DB::table('counselling_enquiries')->where('id', 281)->value('metadata'), true);
        $this->assertSame('Bachelor', $metadata['qualification']);
        $this->assertSame('2026', $metadata['passing_year']);
        $this->assertDatabaseHas('foris_lead_deliveries', ['id' => 44, 'enquiry_id' => 281, 'status' => 'failed', 'attempts' => 3, 'remote_lead_id' => 'original-foris-reference']);
        $this->assertSame(1, $report['users']['rows']);
        $this->assertSame(1, $report['counselling_enquiries']['rows']);
        $this->assertSame($before, hash_file('sha256', $snapshot));
        Queue::assertNothingPushed();
        Http::assertNothingSent();
    }

    public function test_an_exact_repeat_is_idempotent_without_updating_existing_records(): void
    {
        $user = User::factory()->make(['id' => 31])->getAttributes();
        $snapshot = $this->snapshot(['users' => [$user]]);
        $transfer = app(CrmSqliteTransfer::class);
        $first = $transfer->copy($snapshot, DB::connection());

        $second = $transfer->copy($snapshot, DB::connection());

        $this->assertSame($first, $second);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['id' => 31, 'password' => $user['password']]);
    }

    public function test_a_populated_destination_is_not_overwritten_or_merged(): void
    {
        $existing = User::factory()->create(['id' => 9]);
        $snapshot = $this->snapshot(['users' => [User::factory()->make(['id' => 31])->getAttributes()]]);

        try {
            app(CrmSqliteTransfer::class)->copy($snapshot, DB::connection());
            $this->fail('Conflicting target records must not be overwritten.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Verified database copy stopped. Target changes were rolled back; source records were not changed.', $exception->getMessage());
        }

        $this->assertDatabaseCount('users', 1);
        $this->assertModelExists($existing);
        $this->assertDatabaseMissing('users', ['id' => 31]);
    }

    public function test_a_foreign_key_failure_rolls_back_the_entire_copy(): void
    {
        $blogs = $this->migrationBlogs();
        $originals = $blogs;
        $originals[0]['title'] = 'Original edited article';
        $snapshot = $this->snapshot(['users' => [User::factory()->make(['id' => 31])->getAttributes()], 'blog_posts' => $originals, 'crm_students' => [['id' => 15, 'original_enquiry_id' => 9999, 'full_name' => 'Invalid relationship fixture']]]);

        try {
            app(CrmSqliteTransfer::class)->copy($snapshot, DB::connection(), restoreBlogDefaults: true);
            $this->fail('A missing parent must abort the whole data copy.');
        } catch (CrmSqliteTransferException $exception) {
            $this->assertDatabaseCount('users', 0);
            $this->assertDatabaseCount('crm_students', 0);
            $this->assertSame('insert', $exception->diagnostics['phase']);
            $this->assertSame('crm_students', $exception->diagnostics['table']);
            $this->assertStringNotContainsString('Invalid relationship fixture', json_encode($exception->diagnostics));
            $this->assertNull($exception->getPrevious());
            $this->assertSame($blogs, DB::table('blog_posts')->orderBy('id')->get()->map(fn (object $row): array => (array) $row)->all());
        }
    }

    public function test_only_known_migration_blog_defaults_are_replaced_by_the_complete_original_records(): void
    {
        $blogs = $this->migrationBlogs();
        $blogs[0]['title'] = 'Original CMS-edited article';
        $blogs[0]['created_at'] = '2026-08-29 08:00:00';
        $blogs[0]['updated_at'] = '2026-09-01 08:00:00';
        $user = User::factory()->make(['id' => 31])->getAttributes();
        $snapshot = $this->snapshot(['users' => [$user], 'blog_posts' => $blogs]);

        $report = app(CrmSqliteTransfer::class)->copy($snapshot, DB::connection(), restoreBlogDefaults: true);

        $this->assertDatabaseHas('users', ['id' => 31, 'password' => $user['password']]);
        $this->assertDatabaseHas('blog_posts', ['id' => 1, 'title' => 'Original CMS-edited article', 'created_at' => '2026-08-29 08:00:00', 'updated_at' => '2026-09-01 08:00:00']);
        $this->assertDatabaseCount('blog_posts', count($blogs));
        $this->assertSame(count($blogs), $report['blog_posts']['rows']);
        $this->assertSame($report, app(CrmSqliteTransfer::class)->copy($snapshot, DB::connection(), restoreBlogDefaults: true));
    }

    public function test_edited_destination_articles_cannot_be_treated_as_migration_defaults(): void
    {
        $blogs = $this->migrationBlogs();
        $snapshot = $this->snapshot(['users' => [User::factory()->make(['id' => 31])->getAttributes()], 'blog_posts' => $blogs]);
        DB::table('blog_posts')->where('id', 1)->update(['title' => 'Existing destination edit']);

        try {
            app(CrmSqliteTransfer::class)->copy($snapshot, DB::connection(), restoreBlogDefaults: true);
            $this->fail('Edited target content must require review.');
        } catch (CrmSqliteTransferException) {
            $this->assertDatabaseCount('users', 0);
            $this->assertDatabaseHas('blog_posts', ['id' => 1, 'title' => 'Existing destination edit']);
        }
    }

    public function test_blog_identity_differences_cannot_remove_or_replace_destination_rows(): void
    {
        $blogs = $this->migrationBlogs();
        $originalCount = count($blogs);
        array_pop($blogs);
        $snapshot = $this->snapshot(['users' => [User::factory()->make(['id' => 31])->getAttributes()], 'blog_posts' => $blogs]);

        try {
            app(CrmSqliteTransfer::class)->copy($snapshot, DB::connection(), restoreBlogDefaults: true);
            $this->fail('Different article identities must require review.');
        } catch (CrmSqliteTransferException) {
            $this->assertDatabaseCount('users', 0);
            $this->assertDatabaseCount('blog_posts', $originalCount);
        }
    }

    /** @return list<array<string, mixed>> */
    private function migrationBlogs(): array
    {
        DB::table('blog_posts')->delete();
        foreach (BlogCatalog::defaultAttributes() as $index => $attributes) {
            DB::table('blog_posts')->insert(['id' => $index + 1] + (new BlogPost($attributes))->getAttributes() + ['created_at' => '2026-10-20 08:00:00', 'updated_at' => '2026-10-20 08:00:00']);
        }

        return DB::table('blog_posts')->orderBy('id')->get()->map(fn (object $row): array => (array) $row)->all();
    }

    public function test_wordpress_or_unknown_source_tables_are_never_written(): void
    {
        $snapshot = $this->snapshot(['users' => [User::factory()->make(['id' => 31])->getAttributes()], 'wplb_options' => [['id' => 1, 'option_value' => 'must not copy']]]);

        try {
            app(CrmSqliteTransfer::class)->copy($snapshot, DB::connection());
            $this->fail('WordPress tables must remain outside the Laravel copy.');
        } catch (RuntimeException) {
            $this->assertDatabaseCount('users', 0);
        }
    }

    public function test_mysql_connection_uses_utc_and_strict_dates_without_server_admin_privileges(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped('Executed by the isolated MySQL CRM CI service.');
        }

        $settings = DB::selectOne('SELECT @@session.time_zone AS zone, @@session.sql_mode AS modes, CURRENT_USER() AS account');

        $this->assertSame('+00:00', $settings->zone);
        $this->assertStringContainsString('NO_ZERO_DATE', $settings->modes);
        if (app()->environment('testing') && config('database.connections.mysql.database') === 'geic_crm_tests') {
            $this->assertStringStartsWith('geic_crm_ci@', $settings->account);
        }
    }

    /** @param array<string, list<array<string, mixed>>> $tables */
    private function snapshot(array $tables): string
    {
        $this->fixture = storage_path('framework/testing/crm-transfer-'.Str::uuid());
        File::ensureDirectoryExists($this->fixture, 0700);
        File::put($this->fixture.'/owned-test-fixture', 'Disposable CrmSqliteTransferTest fixture');
        $path = $this->fixture.'/source.sqlite';
        $database = new SQLite3($path);
        foreach ($tables as $table => $rows) {
            $columns = array_keys($rows[0]);
            $declarations = array_map(fn (string $column): string => '"'.$column.'" '.($column === 'id' ? 'INTEGER PRIMARY KEY' : 'TEXT'), $columns);
            $database->exec('CREATE TABLE "'.$table.'" ('.implode(', ', $declarations).')');
            $statement = $database->prepare('INSERT INTO "'.$table.'" ('.implode(', ', array_map(fn (string $column): string => '"'.$column.'"', $columns)).') VALUES ('.implode(', ', array_fill(0, count($columns), '?')).')');
            foreach ($rows as $row) {
                foreach (array_values($row) as $index => $value) {
                    $statement->bindValue($index + 1, $value, $value === null ? SQLITE3_NULL : (is_int($value) || is_bool($value) ? SQLITE3_INTEGER : SQLITE3_TEXT));
                }
                $statement->execute()->finalize();
                $statement->reset();
            }
            $statement->close();
        }
        $database->close();

        return $path;
    }
}
