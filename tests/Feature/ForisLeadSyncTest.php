<?php

namespace Tests\Feature;

use App\Models\CmsPage;
use App\Models\CmsPageState;
use App\Models\User;
use App\Support\ForisLeadSync;
use App\Support\RecordWebsiteEnquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ForisLeadSyncTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'https://crm.foriseducation.com/public/api/v1/forms/submit/test-form';

    #[DataProvider('websiteForms')]
    public function test_every_website_form_saves_locally_and_queues_without_waiting_for_foris(string $path, array $payload, string $source): void
    {
        $this->enableForis();
        Http::preventStrayRequests();
        Http::fake([self::ENDPOINT => Http::response([], 503)]);

        $response = $this->postJson($path, $payload);

        $this->assertLessThan(400, $response->status());
        $lead = DB::table('counselling_enquiries')->first();
        $this->assertSame($source, $lead->source_page);
        $this->assertDatabaseHas('foris_lead_deliveries', ['enquiry_id' => $lead->id, 'status' => 'pending', 'is_historical' => 0]);
        $this->assertDatabaseHas('jobs', ['queue' => 'foris-live']);
        Http::assertNothingSent();
    }

    public static function websiteForms(): array
    {
        $contact = [
            'full_name' => 'Test Student', 'email' => 'student@example.com', 'phone' => '+91 98765 43210',
            'city' => 'Indore', 'study_level' => 'Postgraduate', 'preferred_intake' => 'Next available intake',
            'preferred_course' => 'Business Analytics', 'english_test' => 'IELTS', 'consent' => '1',
        ];
        $hero = ['full_name' => 'Hero Student', 'email' => 'hero@example.com', 'phone' => '9876543210', 'consent' => '1'];
        $expo = ['name' => 'Expo Student', 'email' => 'expo@example.com', 'mobile' => '9876543210', 'country' => 'Dubai', 'interest' => 'Indore Expo'];
        $landing = ['name' => 'Landing Student', 'email' => 'landing@example.com', 'phone' => '9876543210', 'qualification' => 'Bachelors', 'passing_year' => 2026, 'score' => '8.1 CGPA', 'country' => 'Germany'];

        return [
            'contact' => ['/contact/enquire', $contact, '/contact'],
            'australia' => ['/destinations/australia/enquire', $contact, '/destinations/australia'],
            'other destination' => ['/destinations/germany/enquire', $contact, '/destinations/germany'],
            'service hero' => ['/hero-enquire', $hero + ['source_context' => 'Admissions', 'return_to' => '/services/university-admissions#overview'], '/services/university-admissions'],
            'test hero' => ['/hero-enquire', $hero + ['source_context' => 'IELTS', 'return_to' => '/tests/ielts#overview'], '/tests/ielts'],
            'scholarship hero' => ['/hero-enquire', $hero + ['source_context' => 'Scholarships', 'return_to' => '/scholarships/australia#overview'], '/scholarships/australia'],
            'landing forms' => ['/landing/form-handler.php', $landing, '/landing'],
            'expo' => ['/uniexpo-dubai-europe/register', $expo, '/uniexpo-dubai-europe'],
            'release expo alias' => ['/_geic_release/uniexpo-dubai-europe/register', $expo, '/uniexpo-dubai-europe'],
        ];
    }

    public function test_published_promotion_uses_the_same_delivery_path_and_preserves_academic_details(): void
    {
        $this->enableForis();
        CmsPage::create(['page_key' => 'promotion.test', 'group' => 'promotions', 'name' => 'Test campaign', 'slug' => 'test', 'path' => 'promotions/test']);
        CmsPageState::create(['page_key' => 'promotion.test', 'status' => 'published']);
        $data = self::websiteForms()['landing forms'][1];

        $this->postJson('/promotions/test/form-handler.php', $data)->assertCreated();

        $lead = DB::table('counselling_enquiries')->first();
        $this->assertSame('/promotions/test', $lead->source_page);
        $this->assertSame('8.1 CGPA', json_decode($lead->metadata, true)['form_fields']['score']);
        $this->assertDatabaseCount('foris_lead_deliveries', 1);
    }

    public function test_invalid_and_honeypot_submissions_never_create_a_delivery(): void
    {
        $this->enableForis();

        $this->postJson('/landing/form-handler.php', [])->assertUnprocessable();
        $this->postJson('/landing/form-handler.php', ['website' => 'spam'])->assertOk();

        $this->assertDatabaseCount('counselling_enquiries', 0);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        $this->assertDatabaseCount('jobs', 0);
    }

    public function test_lead_and_delivery_are_rolled_back_together_when_the_database_queue_cannot_save(): void
    {
        $this->enableForis();
        Schema::drop('jobs');

        $this->postJson('/landing/form-handler.php', self::websiteForms()['landing forms'][1])->assertServerError();

        $this->assertDatabaseCount('counselling_enquiries', 0);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
    }

    public function test_maps_details_and_delivers_once_without_exposing_internal_metadata(): void
    {
        $this->enableForis();
        Http::preventStrayRequests();
        Http::fake([self::ENDPOINT => Http::response(['success' => true, 'lead_id' => 123, 'submission_id' => 456], 201)]);
        $id = $this->record(['destination' => 'Dubai'], [
            'qualification' => 'Bachelors', 'passing_year' => 2026, 'score' => '8.1 CGPA',
            'event_date' => '2026-10-24', 'password' => 'private-password', 'legacy_ip_hash' => 'private-hash',
        ]);
        $deliveryId = (int) DB::table('foris_lead_deliveries')->where('enquiry_id', $id)->value('id');
        $sync = app(ForisLeadSync::class);

        $sync->deliver($deliveryId);
        $sync->deliver($deliveryId);

        $this->assertDatabaseHas('foris_lead_deliveries', ['id' => $deliveryId, 'status' => 'delivered', 'remote_lead_id' => '123', 'remote_submission_id' => '456']);
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request): bool => $request->hasHeader('X-API-KEY', 'test-secret')
            && $request['contact_number'] === '9876543210'
            && $request['phonecode'] === '91'
            && $request['pref_country'] === 'United Arab Emirates'
            && $request['name'] === 'Test Student'
            && $request['email'] === 'student@example.com'
            && $request['city'] === 'Indore'
            && $request['interest_area'] === 'Data Science'
            && ! array_key_exists('intake', $request->data())
            && ! array_key_exists('remark', $request->data())
            && ! array_key_exists('intended_level_qua', $request->data())
            && ! array_key_exists('intended_year_departure', $request->data())
            && ! str_contains(json_encode($request->data()), 'private-password')
            && ! str_contains(json_encode($request->data()), 'private-hash'));
        $saved = DB::table('counselling_enquiries')->find($id);
        $this->assertSame('8.1 CGPA', json_decode($saved->metadata, true)['form_fields']['score']);
        $this->assertSame('Please contact me.', $saved->message);
    }

    #[DataProvider('responses')]
    public function test_classifies_acknowledgements_and_failures_without_losing_the_geic_lead(int $httpStatus, array $body, string $expected, ?int $delay): void
    {
        $this->enableForis();
        Http::preventStrayRequests();
        Http::fake([self::ENDPOINT => Http::response($body, $httpStatus)]);
        $this->record();

        $actualDelay = app(ForisLeadSync::class)->deliver(1);

        $this->assertSame($delay, $actualDelay);
        $this->assertDatabaseHas('foris_lead_deliveries', ['status' => $expected, 'attempts' => 1, 'last_http_status' => $httpStatus]);
        $this->assertDatabaseCount('counselling_enquiries', 1);
        Http::assertSentCount(1);
    }

    public static function responses(): array
    {
        return [
            'duplicate queue' => [202, ['success' => true, 'status' => 'duplicate_queue', 'submission_id' => 7], 'duplicate_review', null],
            'live Foris duplicate response' => [200, ['success' => true, 'message' => 'Thank you! Your inquiry has been received.', 'queued_review' => true, 'submission_id' => 54], 'duplicate_review', null],
            'rate limit' => [429, ['success' => false], 'pending', 60],
            'invalid key' => [401, ['success' => false], 'failed', null],
            'validation failure' => [422, ['success' => false], 'failed', null],
            'server outcome uncertain' => [500, [], 'uncertain', null],
            'malformed successful response' => [200, [], 'uncertain', null],
        ];
    }

    public function test_timeout_is_held_for_reconciliation_instead_of_being_retried_blindly(): void
    {
        $this->enableForis();
        Http::preventStrayRequests();
        Http::fake([self::ENDPOINT => Http::failedConnection('cURL error 28: Operation timed out')]);
        $this->record();

        app(ForisLeadSync::class)->deliver(1);

        $this->assertDatabaseHas('foris_lead_deliveries', ['status' => 'uncertain']);
        $this->assertFalse(app(ForisLeadSync::class)->retry(1));
    }

    public function test_historical_import_is_resumable_and_keeps_live_enquiries_in_the_priority_queue(): void
    {
        config(['foris.enabled' => false]);
        $id = $this->record(['source' => 'wordpress']);
        DB::table('foris_lead_deliveries')->where('enquiry_id', $id)->delete();
        $this->enableForis();

        $this->artisan('crm:foris-backfill', ['--all' => true])->assertSuccessful();
        $this->artisan('crm:foris-backfill', ['--all' => true])->assertSuccessful();
        $this->record(['email' => 'live@example.com']);

        $this->assertDatabaseCount('foris_lead_deliveries', 2);
        $this->assertDatabaseHas('foris_lead_deliveries', ['enquiry_id' => $id, 'is_historical' => 1]);
        $this->assertDatabaseHas('jobs', ['queue' => 'foris-backfill']);
        $this->assertDatabaseHas('jobs', ['queue' => 'foris-live']);
        $this->assertDatabaseCount('jobs', 2);
    }

    public function test_newsletter_is_saved_but_missing_phone_is_visible_as_a_correction_instead_of_invented(): void
    {
        $this->enableForis();
        Http::preventStrayRequests();
        Http::fake([self::ENDPOINT => Http::response(['success' => true], 201)]);

        $this->postJson('/newsletters', ['newsletter_email' => 'subscriber@example.com'])->assertOk()->assertJson(['code' => 200]);
        app(ForisLeadSync::class)->deliver(1);

        $this->assertDatabaseHas('counselling_enquiries', ['email' => 'subscriber@example.com', 'source_form' => 'Website footer newsletter', 'phone' => 'Not provided']);
        $this->assertDatabaseHas('foris_lead_deliveries', ['status' => 'failed', 'attempts' => 0]);
        Http::assertNothingSent();
    }

    public function test_stopped_worker_is_marked_uncertain_and_paused_delivery_stays_pending(): void
    {
        $this->enableForis();
        $this->record();
        DB::table('foris_lead_deliveries')->where('id', 1)->update(['status' => 'processing', 'attempted_at' => now()->subMinutes(3)]);
        config(['foris.enabled' => false]);

        app(ForisLeadSync::class)->enqueuePending();
        $this->record(['email' => 'paused@example.com']);

        $this->assertDatabaseHas('foris_lead_deliveries', ['id' => 1, 'status' => 'uncertain']);
        $this->assertDatabaseHas('foris_lead_deliveries', ['id' => 2, 'status' => 'pending', 'queued_at' => null]);
    }

    public function test_monitor_and_retry_actions_are_restricted_to_super_administrators(): void
    {
        $this->get('/admin/integrations/foris')->assertRedirect('/admin/login');
        $counsellor = User::factory()->create(['is_admin' => true, 'is_active' => true, 'admin_role' => 'counsellor']);
        $this->actingAs($counsellor)->get('/admin/integrations/foris')->assertForbidden();
        $super = User::factory()->create(['is_admin' => true, 'is_active' => true, 'admin_role' => 'super_admin']);
        $this->record(['full_name' => '<script>bad()</script>']);

        $this->actingAs($super)->get('/admin/integrations/foris')->assertOk()
            ->assertSee('Foris CRM Sync')->assertDontSee('<script>bad()</script>', false)->assertDontSee('test-secret');

        DB::table('foris_lead_deliveries')->where('id', 1)->update(['status' => 'uncertain']);
        $this->actingAs($super)->post('/admin/integrations/foris/1/retry')->assertRedirect();
        $this->assertDatabaseHas('foris_lead_deliveries', ['id' => 1, 'status' => 'uncertain']);
        DB::table('foris_lead_deliveries')->where('id', 1)->update(['status' => 'failed']);
        $this->actingAs($super)->post('/admin/integrations/foris/1/retry')->assertRedirect();
        $this->assertDatabaseHas('foris_lead_deliveries', ['id' => 1, 'status' => 'pending']);
    }

    public function test_worker_delivers_serialized_jobs_without_database_cache_writes(): void
    {
        $this->enableForis();
        config(['cache.default' => 'database']);
        DB::statement("CREATE TRIGGER reject_crm_cache_upsert BEFORE INSERT ON cache BEGIN SELECT RAISE(ABORT, 'Legacy SQLite cache writes unavailable'); END");
        Http::preventStrayRequests();
        Http::fake([self::ENDPOINT => Http::response(['success' => true, 'lead_id' => 901], 201)]);
        $id = $this->record();

        $this->artisan('crm:foris-sync')->assertSuccessful();

        $this->assertDatabaseHas('foris_lead_deliveries', ['enquiry_id' => $id, 'status' => 'delivered', 'remote_lead_id' => '901']);
        $this->assertDatabaseCount('jobs', 0);
        $this->assertNotNull(Cache::store('array')->get('foris-worker-heartbeat'));
        Http::assertSentCount(1);
    }

    public function test_authentication_is_header_only_and_never_part_of_lead_data(): void
    {
        $this->enableForis();
        Http::preventStrayRequests();
        Http::fake([self::ENDPOINT => Http::response(['success' => true, 'lead_id' => 902], 201)]);
        $this->record();

        app(ForisLeadSync::class)->deliver(1);

        Http::assertSent(fn (Request $request): bool => ! isset($request['api_key'])
            && $request->hasHeader('X-API-KEY', 'test-secret')
            && ! str_contains(json_encode($request->data()), 'test-secret'));
        $this->assertDatabaseHas('foris_lead_deliveries', ['status' => 'delivered', 'remote_lead_id' => '902']);
        $this->assertStringNotContainsString('test-secret', DB::table('counselling_enquiries')->value('metadata'));
    }

    #[DataProvider('indianDialPrefixes')]
    public function test_normalizes_indian_dial_prefixes_before_forwarding(string $phone): void
    {
        $this->enableForis();
        Http::preventStrayRequests();
        Http::fake([self::ENDPOINT => Http::response(['success' => true, 'lead_id' => 903], 201)]);
        $id = $this->record(['phone' => $phone]);

        app(ForisLeadSync::class)->deliver(1);

        Http::assertSent(fn (Request $request): bool => $request['contact_number'] === '9876543210' && $request['phonecode'] === '91');
        $this->assertDatabaseHas('counselling_enquiries', ['id' => $id, 'phone' => $phone]);
        $this->assertDatabaseHas('foris_lead_deliveries', ['status' => 'delivered']);
    }

    public static function indianDialPrefixes(): array
    {
        return ['trunk prefix' => ['09876543210'], 'international prefix' => ['00919876543210']];
    }

    #[DataProvider('structuredStudyFields')]
    public function test_forwards_verified_study_fields_without_guessing_from_passing_years_or_event_dates(string $studyLevel, string $intake, array $fields, ?string $expectedLevel, ?string $expectedIntake): void
    {
        $this->enableForis();
        Http::preventStrayRequests();
        Http::fake([self::ENDPOINT => Http::response(['success' => true, 'lead_id' => 904], 201)]);
        $id = $this->record(['study_level' => $studyLevel, 'preferred_intake' => $intake], $fields);

        app(ForisLeadSync::class)->deliver(1);

        Http::assertSent(function (Request $request) use ($expectedLevel, $expectedIntake): bool {
            $data = $request->data();

            return ($data['intended_level_qua'] ?? null) === $expectedLevel
                && ($data['intake'] ?? null) === $expectedIntake
                && ! array_key_exists('remark', $data)
                && ! array_key_exists('intended_year_departure', $data)
                && array_diff(array_keys($data), ['name', 'contact_number', '_hp_check', 'phonecode', 'email', 'city', 'pref_country', 'interest_area', 'intended_level_qua', 'intake']) === [];
        });
        $this->assertDatabaseHas('counselling_enquiries', ['id' => $id, 'study_level' => $studyLevel, 'preferred_intake' => $intake]);
        $this->assertDatabaseHas('foris_lead_deliveries', ['enquiry_id' => $id, 'status' => 'delivered']);
        Http::assertSentCount(1);
    }

    public static function structuredStudyFields(): array
    {
        return [
            'masters February' => ['Postgraduate', 'February intake', [], '1', '2'],
            'bachelors July' => ['Undergraduate', 'July intake', [], '3', '7'],
            'research October' => ['Research', 'October intake', [], '4', '10'],
            'unspecified intake' => ['Postgraduate', 'Next available intake', [], '1', null],
            'event date is not an intake' => ['Not sure yet', 'Global Uni Expo 2026 - 24 October 2026', ['event_date' => '2026-10-24'], null, null],
            'passing year is not departure year' => ['Bachelors', 'Passing year: 2026', ['qualification' => 'Bachelors', 'passing_year' => 2026], null, null],
        ];
    }

    private function enableForis(): void
    {
        config(['foris.enabled' => true, 'foris.endpoint' => self::ENDPOINT, 'foris.api_key' => 'test-secret', 'foris.cache_store' => 'array']);
    }

    private function record(array $overrides = [], array $fields = []): int
    {
        return app(RecordWebsiteEnquiry::class)->store(array_merge([
            'destination' => 'Germany', 'full_name' => 'Test Student', 'email' => 'student@example.com',
            'phone' => '+91 98765 43210', 'city' => 'Indore', 'study_level' => 'Postgraduate',
            'preferred_intake' => 'October intake', 'preferred_course' => 'Data Science', 'english_test' => 'IELTS',
            'message' => 'Please contact me.', 'source_page' => '/contact', 'source' => 'website',
            'created_at' => now(), 'updated_at' => now(),
        ], $overrides), $fields);
    }
}
