<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\EnquiryPhoneNumber;
use App\Support\LeadDuplicateReview;
use App\Support\RecordWebsiteEnquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LeadDuplicateReviewTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('websiteForms')]
    public function test_every_website_form_keeps_repeat_submissions_and_queues_both_without_waiting_for_foris(string $path, array $payload, string $source): void
    {
        config(['foris.enabled' => true, 'foris.endpoint' => 'https://crm.foriseducation.com/public/api/v1/forms/submit/test', 'foris.api_key' => 'test-secret']);
        Http::preventStrayRequests();

        $this->assertLessThan(400, $this->postJson($path, $payload)->status());
        $this->assertLessThan(400, $this->postJson($path, $payload)->status());

        $this->assertDatabaseCount('counselling_enquiries', 2);
        $this->assertDatabaseCount('lead_contact_keys', 2);
        $this->assertDatabaseHas('lead_duplicate_reviews', ['enquiry_id' => 2, 'status' => 'pending']);
        $this->assertSame([['enquiry_id' => 1, 'reasons' => ['email', 'phone']]], json_decode(DB::table('lead_duplicate_reviews')->value('matches'), true));
        $this->assertDatabaseCount('foris_lead_deliveries', 2);
        $this->assertDatabaseCount('jobs', 2);
        Http::assertNothingSent();
    }

    public static function websiteForms(): array
    {
        return ForisLeadSyncTest::websiteForms();
    }

    public function test_normalized_phone_and_email_can_match_different_earlier_enquiries(): void
    {
        config(['foris.enabled' => false]);
        $first = $this->record(['email' => 'Person@Example.com', 'phone' => '9876543210']);
        $second = $this->record(['email' => 'other@example.com', 'phone' => '+971 50 123 4567']);
        $third = $this->record(['email' => ' person@example.com ', 'phone' => '+971501234567']);

        $review = DB::table('lead_duplicate_reviews')->where('enquiry_id', $third)->first();
        $this->assertSame([
            ['enquiry_id' => $first, 'reasons' => ['email']],
            ['enquiry_id' => $second, 'reasons' => ['phone']],
        ], json_decode($review->matches, true));
        $this->assertStringNotContainsString('person@example.com', json_encode(DB::table('lead_contact_keys')->get()));
        $this->assertStringNotContainsString('9876543210', json_encode(DB::table('lead_contact_keys')->get()));
        app(LeadDuplicateReview::class)->capture($third);
        $this->assertDatabaseCount('lead_duplicate_reviews', 1);
    }

    public function test_different_formats_of_the_same_indian_number_are_reviewed_but_names_alone_are_not(): void
    {
        $first = $this->record();
        $this->record(['email' => 'second@example.com', 'phone' => '+91 (98765) 43210']);
        $this->record(['email' => 'third@example.com', 'phone' => '8765432190']);

        $this->assertDatabaseCount('lead_duplicate_reviews', 1);
        $this->assertSame([['enquiry_id' => $first, 'reasons' => ['phone']]], json_decode(DB::table('lead_duplicate_reviews')->value('matches'), true));
    }

    public function test_historical_indexing_is_resumable_and_does_not_change_incomplete_records_or_delivery_statuses(): void
    {
        config(['foris.enabled' => false]);
        $id = $this->record(['source' => 'wordpress', 'phone' => 'Not provided', 'email' => 'not-provided@legacy.invalid']);
        DB::table('lead_contact_keys')->where('enquiry_id', $id)->delete();
        DB::table('foris_lead_deliveries')->where('enquiry_id', $id)->update(['status' => 'failed']);
        $before = DB::table('counselling_enquiries')->find($id);

        $this->artisan('crm:index-enquiry-contacts')->assertSuccessful();
        $this->artisan('crm:index-enquiry-contacts')->assertSuccessful();

        $this->assertSame((array) $before, (array) DB::table('counselling_enquiries')->find($id));
        $this->assertDatabaseHas('lead_contact_keys', ['enquiry_id' => $id, 'email_key' => null, 'phone_key' => null]);
        $this->assertDatabaseHas('foris_lead_deliveries', ['enquiry_id' => $id, 'status' => 'failed']);
        $this->assertDatabaseCount('lead_duplicate_reviews', 0);
        $this->assertDatabaseCount('lead_contact_keys', 1);
    }

    public function test_new_submission_matches_a_contact_indexed_from_existing_history(): void
    {
        $oldId = $this->record(['source' => 'wordpress']);
        DB::table('lead_contact_keys')->where('enquiry_id', $oldId)->delete();
        $this->artisan('crm:index-enquiry-contacts')->assertSuccessful();

        $newId = $this->record(['phone' => '+919876543210']);

        $this->assertDatabaseHas('lead_duplicate_reviews', ['enquiry_id' => $newId, 'status' => 'pending']);
        $this->assertDatabaseCount('counselling_enquiries', 2);
    }

    public function test_missing_phone_and_placeholder_email_never_match_each_other(): void
    {
        $this->record(['phone' => 'Not provided', 'email' => 'not-provided@legacy.invalid']);
        $this->record(['phone' => 'Not provided', 'email' => 'not-provided@legacy.invalid']);

        $this->assertDatabaseCount('lead_duplicate_reviews', 0);
    }

    public function test_duplicate_queue_and_enquiry_are_rolled_back_together_on_failure(): void
    {
        $this->record();
        Schema::drop('lead_duplicate_reviews');

        $this->postJson('/landing/form-handler.php', ForisLeadSyncTest::websiteForms()['landing forms'][1])->assertServerError();

        $this->assertDatabaseCount('counselling_enquiries', 1);
        $this->assertDatabaseCount('lead_contact_keys', 1);
        $this->assertDatabaseCount('foris_lead_deliveries', 1);
    }

    public function test_invalid_number_is_rejected_before_either_crm_queue_is_created(): void
    {
        $payload = ForisLeadSyncTest::websiteForms()['landing forms'][1];
        $payload['phone'] = '0000000000';

        $this->postJson('/landing/form-handler.php', $payload)->assertUnprocessable()
            ->assertJsonValidationErrors(['phone'])->assertJsonPath('errors.phone.0', EnquiryPhoneNumber::MESSAGE);

        $this->assertDatabaseCount('counselling_enquiries', 0);
        $this->assertDatabaseCount('lead_duplicate_reviews', 0);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
    }

    public function test_expo_rejects_a_ten_digit_placeholder_before_registration(): void
    {
        $payload = ForisLeadSyncTest::websiteForms()['expo'][1];
        $payload['mobile'] = '9999999999';

        $this->postJson('/_geic_release/uniexpo-dubai-europe/register', $payload)->assertUnprocessable()
            ->assertJsonValidationErrors(['mobile'])->assertJsonPath('errors.mobile.0', EnquiryPhoneNumber::MESSAGE);

        $this->assertDatabaseCount('counselling_enquiries', 0);
    }

    public function test_contact_and_hero_forms_return_inline_phone_errors_without_saving(): void
    {
        $contact = ForisLeadSyncTest::websiteForms()['contact'][1];
        $hero = ForisLeadSyncTest::websiteForms()['service hero'][1];

        $this->post('/contact/enquire', array_replace($contact, ['phone' => '-------']))
            ->assertRedirect('/contact#enquiry')->assertSessionHasErrors(['phone' => EnquiryPhoneNumber::MESSAGE]);
        $this->post('/hero-enquire', array_replace($hero, ['phone' => 'not-a-number']))
            ->assertRedirect('/services/university-admissions#overview')->assertSessionHasErrors(['phone' => EnquiryPhoneNumber::MESSAGE]);

        $this->assertDatabaseCount('counselling_enquiries', 0);
    }

    public function test_review_screen_is_protected_and_read_only_counsellors_cannot_make_decisions(): void
    {
        $this->get('/admin/duplicate-review')->assertRedirect('/admin/login');
        $counsellor = User::factory()->create(['is_admin' => true, 'is_active' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['enquiries.view']]);
        $this->record();
        $this->record();

        $this->actingAs($counsellor)->get('/admin/duplicate-review')->assertOk()->assertSee('Matching earlier enquiries')->assertDontSee('Keep as separate enquiry');
        $this->actingAs($counsellor)->patch('/admin/duplicate-review/1', ['decision' => 'distinct'])->assertForbidden();
        $this->assertFalse(Gate::forUser($counsellor)->allows('enquiries.review'));
        $this->assertDatabaseHas('lead_duplicate_reviews', ['id' => 1, 'status' => 'pending']);
    }

    public function test_confirms_only_a_known_match_without_merging_or_resending_and_audits_the_reviewer(): void
    {
        $this->freezeTime();
        $super = $this->superAdmin();
        $first = $this->record();
        $newId = $this->record();
        $beforeLeads = DB::table('counselling_enquiries')->get()->map(fn ($row): array => (array) $row)->all();
        $beforeDeliveries = DB::table('foris_lead_deliveries')->get()->map(fn ($row): array => (array) $row)->all();

        $this->actingAs($super)->patch('/admin/duplicate-review/1', ['decision' => 'confirmed_duplicate', 'linked_enquiry_id' => $first, 'review_note' => 'Same contact confirmed.', 'reviewed_by' => 999])->assertRedirect();

        $this->assertDatabaseHas('lead_duplicate_reviews', ['enquiry_id' => $newId, 'status' => 'confirmed_duplicate', 'linked_enquiry_id' => $first, 'reviewed_by' => $super->id, 'review_note' => 'Same contact confirmed.']);
        $this->assertSame($beforeLeads, DB::table('counselling_enquiries')->get()->map(fn ($row): array => (array) $row)->all());
        $this->assertSame($beforeDeliveries, DB::table('foris_lead_deliveries')->get()->map(fn ($row): array => (array) $row)->all());
    }

    public function test_cannot_link_an_unmatched_record_or_submit_an_unknown_decision(): void
    {
        $super = $this->superAdmin();
        $this->record();
        $this->record();
        $other = $this->record(['phone' => '8765432190', 'email' => 'someoneelse@example.com']);

        $this->actingAs($super)->patchJson('/admin/duplicate-review/1', ['decision' => 'confirmed_duplicate', 'linked_enquiry_id' => $other])->assertUnprocessable()->assertJsonValidationErrors(['linked_enquiry_id']);
        $this->actingAs($super)->patchJson('/admin/duplicate-review/1', ['decision' => 'delete'])->assertUnprocessable()->assertJsonValidationErrors(['decision']);
        $this->assertDatabaseHas('lead_duplicate_reviews', ['id' => 1, 'status' => 'pending', 'reviewed_by' => null]);
    }

    public function test_keeps_separate_without_a_link_and_does_not_overwrite_a_completed_review(): void
    {
        $super = $this->superAdmin();
        $first = $this->record();
        $this->record();

        $this->actingAs($super)->patch('/admin/duplicate-review/1', ['decision' => 'distinct'])->assertRedirect();
        $this->actingAs($super)->patch('/admin/duplicate-review/1', ['decision' => 'confirmed_duplicate', 'linked_enquiry_id' => $first])->assertRedirect();

        $this->assertDatabaseHas('lead_duplicate_reviews', ['id' => 1, 'status' => 'distinct', 'linked_enquiry_id' => null]);
        $this->assertDatabaseCount('counselling_enquiries', 2);
    }

    public function test_review_screen_escapes_student_details_and_notes_and_rejects_invalid_filters(): void
    {
        $super = $this->superAdmin();
        $this->record(['full_name' => '<script>alert("old")</script>']);
        $this->record(['full_name' => '<script>alert("new")</script>']);
        $this->actingAs($super)->patch('/admin/duplicate-review/1', ['decision' => 'distinct', 'review_note' => '<img src=x onerror=alert(1)>']);

        $this->actingAs($super)->get('/admin/duplicate-review?status=distinct')
            ->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert("old")</script>', false)
            ->assertDontSee('<script>alert("new")</script>', false)->assertDontSee('<img src=x onerror=alert(1)>', false);
        $this->actingAs($super)->getJson('/admin/duplicate-review?status=unknown')->assertUnprocessable();
    }

    public function test_auto_fetch_is_authenticated_authorized_and_never_shared_cached(): void
    {
        $this->getJson('/admin/duplicate-review')->assertUnauthorized();
        $editor = User::factory()->create(['is_admin' => true, 'is_active' => true, 'admin_role' => 'content_editor', 'admin_permissions' => ['content.manage']]);
        $this->actingAs($editor)->getJson('/admin/duplicate-review')->assertForbidden();
        $super = $this->superAdmin();
        $this->record(['full_name' => '<script>alert("original")</script>']);
        $this->record(['full_name' => '<script>alert("repeat")</script>']);

        $response = $this->actingAs($super)->getJson('/admin/duplicate-review?status=pending');
        $response->assertOk()->assertJsonPath('unchanged', false)->assertHeader('Vary', 'Accept');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $html = $response->json('html');
        $this->assertStringContainsString('data-review-feed', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('Admin navigation', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_auto_fetch_returns_only_a_revision_when_unchanged_and_detects_reviews_in_the_same_second(): void
    {
        $this->freezeTime();
        $super = $this->superAdmin();
        $first = $this->record();
        $this->record();
        $response = $this->actingAs($super)->getJson('/admin/duplicate-review');
        $revision = $response->json('revision');

        $this->getJson('/admin/duplicate-review?revision='.$revision)->assertExactJson(['revision' => $revision, 'unchanged' => true]);
        $this->actingAs($super)->patch('/admin/duplicate-review/1', ['decision' => 'confirmed_duplicate', 'linked_enquiry_id' => $first])->assertRedirect();
        $changed = $this->getJson('/admin/duplicate-review?revision='.$revision)->assertJsonPath('unchanged', false);
        $this->assertNotSame($revision, $changed->json('revision'));
        $this->assertStringContainsString('No pending duplicates', $changed->json('html'));
    }

    public function test_auto_fetch_keeps_search_status_and_page_without_propagating_revision_into_navigation(): void
    {
        $super = $this->superAdmin();
        $this->record();
        for ($i = 0; $i < 17; $i++) {
            $this->record(['full_name' => 'Matched Student '.$i]);
        }
        $response = $this->actingAs($super)->getJson('/admin/duplicate-review?status=pending&q=Matched&page=2&revision='.str_repeat('0', 64));

        $response->assertOk()->assertJsonPath('unchanged', false);
        $html = $response->json('html');
        $this->assertStringContainsString('Matched Student 1', $html);
        $this->assertStringNotContainsString('Matched Student 16', $html);
        $this->assertStringNotContainsString('?revision=', $html);
        $this->assertStringNotContainsString('&amp;revision=', $html);
        $this->assertStringContainsString('status=pending', $html);
        $this->assertStringContainsString('q=Matched', $html);
        $this->getJson('/admin/duplicate-review?revision=bad')->assertUnprocessable()->assertJsonValidationErrors('revision');
        $this->getJson('/admin/duplicate-review?page=0')->assertUnprocessable()->assertJsonValidationErrors('page');
    }

    private function superAdmin(): User
    {
        return User::factory()->create(['is_admin' => true, 'is_active' => true, 'admin_role' => 'super_admin']);
    }

    private function record(array $overrides = []): int
    {
        return app(RecordWebsiteEnquiry::class)->store(array_replace([
            'destination' => 'Germany', 'full_name' => 'Test Student', 'email' => 'student@example.com',
            'phone' => '9876543210', 'city' => 'Indore', 'study_level' => 'Postgraduate',
            'preferred_intake' => 'October intake', 'preferred_course' => 'Data Science',
            'english_test' => 'IELTS', 'message' => 'Test enquiry.', 'source_page' => '/contact',
            'source' => 'website', 'created_at' => now(), 'updated_at' => now(),
        ], $overrides));
    }
}
