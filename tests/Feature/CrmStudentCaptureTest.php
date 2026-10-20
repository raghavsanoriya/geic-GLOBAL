<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\CaptureCrmStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CrmStudentCaptureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    #[DataProvider('websiteForms')]
    public function test_website_forms_save_students_but_keep_duplicate_submissions_for_review_without_sending_http(string $path, array $payload): void
    {
        config(['foris.enabled' => true, 'foris.endpoint' => 'https://crm.foriseducation.com/public/api/v1/forms/submit/test', 'foris.api_key' => 'test-only-secret']);
        Http::preventStrayRequests();

        $this->assertLessThan(400, $this->postJson($path, $payload)->status());
        $this->assertLessThan(400, $this->postJson($path, $payload)->status());

        $this->assertDatabaseCount('counselling_enquiries', 2);
        $this->assertDatabaseCount('crm_students', 1);
        $this->assertDatabaseCount('crm_student_enquiries', 1);
        $this->assertDatabaseCount('foris_lead_deliveries', 2);
        $this->assertDatabaseCount('jobs', 2);
        $this->assertDatabaseHas('lead_duplicate_reviews', ['status' => 'pending']);
        Http::assertNothingSent();
    }

    public static function websiteForms(): array
    {
        return array_map(fn (array $case) => [$case[0], $case[1]], ForisLeadSyncTest::websiteForms());
    }

    #[DataProvider('reviewDecisions')]
    public function test_duplicate_review_links_or_separates_profiles_without_changing_original_submissions(string $decision, int $students): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $case = ForisLeadSyncTest::websiteForms()['contact'];
        $this->postJson($case[0], $case[1]);
        $this->postJson($case[0], $case[1]);
        $review = DB::table('lead_duplicate_reviews')->first();
        $firstEnquiryId = DB::table('counselling_enquiries')->orderBy('id')->value('id');
        $originals = DB::table('counselling_enquiries')->orderBy('id')->get()->toJson();
        $deliveries = DB::table('foris_lead_deliveries')->orderBy('id')->get()->toJson();

        $this->actingAs($super)->patch('/admin/duplicate-review/'.$review->id, [
            'decision' => $decision, 'linked_enquiry_id' => $decision === 'confirmed_duplicate' ? $firstEnquiryId : null,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseCount('crm_students', $students);
        $this->assertDatabaseCount('crm_student_enquiries', 2);
        $this->assertSame($originals, DB::table('counselling_enquiries')->orderBy('id')->get()->toJson());
        $this->assertSame($deliveries, DB::table('foris_lead_deliveries')->orderBy('id')->get()->toJson());
    }

    public static function reviewDecisions(): array
    {
        return ['link after confirmation' => ['confirmed_duplicate', 1], 'keep separate' => ['distinct', 2]];
    }

    public function test_bounded_backfill_is_resumable_and_preserves_incomplete_records_and_failed_deliveries(): void
    {
        Http::preventStrayRequests();
        $first = $this->original(['phone' => 'Not provided', 'email' => 'not-provided@legacy.invalid']);
        $second = $this->original(['email' => 'other@example.com']);
        DB::table('foris_lead_deliveries')->insert(['enquiry_id' => $first, 'status' => 'failed', 'created_at' => now(), 'updated_at' => now()]);
        $originals = DB::table('counselling_enquiries')->orderBy('id')->get()->toJson();

        $this->artisan('crm:backfill-students', ['--limit' => 1])->assertSuccessful();
        $this->assertDatabaseCount('crm_students', 1);
        $this->artisan('crm:backfill-students', ['--after' => $first, '--limit' => 1])->assertSuccessful();
        $this->artisan('crm:backfill-students')->assertSuccessful();

        $this->assertDatabaseCount('crm_students', 2);
        $this->assertDatabaseCount('crm_audit_events', 2);
        $this->assertDatabaseCount('crm_student_enquiries', 2);
        $this->assertDatabaseHas('crm_students', ['original_enquiry_id' => $first, 'phone' => null, 'email' => null]);
        $this->assertDatabaseHas('crm_students', ['original_enquiry_id' => $second]);
        $this->assertDatabaseHas('foris_lead_deliveries', ['enquiry_id' => $first, 'status' => 'failed']);
        $this->assertSame($originals, DB::table('counselling_enquiries')->orderBy('id')->get()->toJson());
        Http::assertNothingSent();
    }

    public function test_disabled_capture_and_invalid_backfill_options_do_not_create_profiles(): void
    {
        $id = $this->original();
        config(['crm.enabled' => false]);

        $this->assertNull(app(CaptureCrmStudent::class)->capture($id));
        config(['crm.enabled' => true]);
        $this->artisan('crm:backfill-students', ['--limit' => 501])->assertFailed();
        $this->artisan('crm:backfill-students', ['--after' => -1])->assertFailed();
        $this->assertDatabaseCount('crm_students', 0);
    }

    public function test_landing_marks_qualification_and_passing_year_map_to_profile_without_rewriting_the_submission(): void
    {
        $id = $this->original(['preferred_intake' => 'Passing year: 2026', 'metadata' => json_encode([
            'form_fields' => ['qualification' => 'Bachelors', 'passing_year' => '2026', 'score' => '8.1 CGPA'],
        ], JSON_THROW_ON_ERROR)]);
        $before = DB::table('counselling_enquiries')->where('id', $id)->first();

        $student = app(CaptureCrmStudent::class)->capture($id);

        $this->assertSame('Bachelors', $student->profile['qualification']);
        $this->assertSame('2026', $student->profile['passing_year']);
        $this->assertSame('8.1 CGPA', $student->profile['percentage']);
        $this->assertArrayNotHasKey('intake', $student->profile);
        $this->assertSame((array) $before, (array) DB::table('counselling_enquiries')->where('id', $id)->first());
    }

    #[DataProvider('intakeSources')]
    public function test_only_actual_study_intakes_are_copied_to_the_student_intake_field(array $fields, string $storedIntake, ?string $expected): void
    {
        $id = $this->original(['preferred_intake' => $storedIntake, 'metadata' => json_encode(['form_fields' => $fields], JSON_THROW_ON_ERROR)]);

        $student = app(CaptureCrmStudent::class)->capture($id);

        $this->assertSame($expected, $student->profile['intake'] ?? null);
    }

    public static function intakeSources(): array
    {
        return [
            'event registration is not an intake' => [['event_date' => '2026-10-24'], 'Global Uni Expo 2026 - 24 October 2026', null],
            'passing year is not an intake' => [['passing_year' => 2026], 'Passing year: 2026', null],
            'contact intake remains available' => [[], 'February intake', 'February intake'],
            'explicit intake with passing year' => [['passing_year' => 2026, 'intake' => 'July 2027'], 'Passing year: 2026', 'July 2027'],
        ];
    }

    private function original(array $changes = []): int
    {
        return DB::table('counselling_enquiries')->insertGetId(array_replace([
            'full_name' => 'Original test student', 'email' => 'original@example.com', 'phone' => '9876543210',
            'destination' => 'Germany', 'city' => 'Indore', 'study_level' => 'Postgraduate',
            'preferred_intake' => 'Next available intake', 'english_test' => 'Not sure yet',
            'source_page' => '/contact', 'source' => 'wordpress', 'created_at' => now(), 'updated_at' => now(),
        ], $changes));
    }
}
