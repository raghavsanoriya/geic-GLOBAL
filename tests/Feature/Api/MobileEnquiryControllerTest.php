<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class MobileEnquiryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_payload_creates_an_enquiry_and_returns_201(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 25));
        $this->postJson(route('api.mobile.enquiries.store'), [
            'kind' => 'event',
            'fullName' => 'Mobile Student',
            'email' => 'mobile@example.com',
            'phone' => '+91 98765 43210',
            'city' => 'Indore',
            'destination' => 'Australia',
            'studyLevel' => 'Postgraduate',
            'preferredIntake' => 'Next available intake',
            'referenceId' => 'meet-eu-business-school-2026',
            'platform' => 'android',
            'appointmentDate' => '2026-10-12',
            'timeSlot' => '11:30 AM',
            'meetingMode' => 'Online (Video Call)',
            'emailProvided' => true,
            'consent' => true,
            'unexpected' => 'must not be stored',
        ])->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'message', 'reference']);

        $this->assertDatabaseHas('counselling_enquiries', [
            'full_name' => 'Mobile Student',
            'source' => 'mobile',
            'source_form' => 'event',
            'source_page' => '/mobile/event',
        ]);
        $metadata = json_decode((string) DB::table('counselling_enquiries')->value('metadata'), true);
        $this->assertSame('2026-10-12', $metadata['appointment_date']);
        $this->assertSame('11:30 AM', $metadata['time_slot']);
        $this->assertSame('Online (Video Call)', $metadata['meeting_mode']);
        $this->assertTrue($metadata['email_provided']);

        $this->assertDatabaseHas('site_events', [
            'event_type' => 'form_submit',
            'path' => '/mobile/event',
            'label' => 'meet-eu-business-school-2026',
        ]);
    }

    public function test_returns_422_and_creates_nothing_without_contact_consent(): void
    {
        $this->postJson(route('api.mobile.enquiries.store'), [
            'kind' => 'counselling',
            'fullName' => 'Mobile Student',
            'email' => 'mobile@example.com',
            'phone' => '+91 98765 43210',
            'consent' => false,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['consent']);

        $this->assertDatabaseCount('counselling_enquiries', 0);
        $this->assertDatabaseCount('site_events', 0);
    }

    public function test_booking_without_email_retains_the_optional_email_state(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 25));
        $this->postJson(route('api.mobile.enquiries.store'), [
            'kind' => 'counselling',
            'fullName' => 'Phone Only Student',
            'email' => 'no-email-1@example.invalid',
            'emailProvided' => false,
            'phone' => '+91 98765 43210',
            'appointmentDate' => '2026-10-12',
            'timeSlot' => '11:30 AM',
            'meetingMode' => 'Online (Video Call)',
            'consent' => true,
        ])->assertCreated();

        $metadata = json_decode((string) DB::table('counselling_enquiries')->value('metadata'), true);
        $this->assertFalse($metadata['email_provided']);
        $this->assertSame('2026-10-12', $metadata['appointment_date']);
    }

    public function test_past_appointment_returns_422_without_creating_an_enquiry(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 25));
        $this->postJson(route('api.mobile.enquiries.store'), [
            'kind' => 'counselling', 'fullName' => 'Test Student',
            'email' => 'student@example.com', 'phone' => '+91 90000 00000',
            'appointmentDate' => '2026-09-24', 'consent' => true,
        ])->assertUnprocessable()->assertJsonValidationErrors([
            'appointmentDate' => 'Choose today or a future date for your counselling request.',
        ]);
        $this->assertDatabaseCount('counselling_enquiries', 0);
    }

    public function test_phone_symbols_without_digits_return_422(): void
    {
        $this->postJson(route('api.mobile.enquiries.store'), [
            'kind' => 'counselling', 'fullName' => 'Test Student',
            'email' => 'student@example.com', 'phone' => '-------', 'consent' => true,
        ])->assertUnprocessable()->assertJsonValidationErrors([
            'phone' => 'Enter a valid phone number using digits and standard phone symbols.',
        ]);
        $this->assertDatabaseCount('counselling_enquiries', 0);
    }

    public function test_missing_fields_return_422_and_do_not_write_records(): void
    {
        $this->postJson(route('api.mobile.enquiries.store'), [])
            ->assertUnprocessable()->assertJsonValidationErrors(['kind', 'fullName', 'email', 'phone', 'consent']);
        $this->assertDatabaseCount('counselling_enquiries', 0);
        $this->assertDatabaseCount('site_events', 0);
    }

    #[TestWith(['counselling', 'STUDY_DESTINATIONS'])]
    #[TestWith(['service', 'GEIC_SERVICES'])]
    #[TestWith(['event', 'UPCOMING_EVENTS_LIST'])]
    #[TestWith(['expo', 'UPCOMING_EVENTS_LIST'])]
    public function test_catalogue_to_enquiry_funnel_preserves_context(string $kind, string $collection): void
    {
        $catalogue = $this->getJson(route('api.mobile.catalog'))->assertOk();
        $reference = $catalogue->json('studio.'.$collection.'.0.id');
        $this->assertNotEmpty($reference);

        $response = $this->postJson(route('api.mobile.enquiries.store'), [
            'kind' => $kind, 'fullName' => 'Funnel Test Student',
            'email' => 'funnel@example.com', 'phone' => '+91 90000 00000',
            'referenceId' => $reference, 'destination' => 'Australia',
            'preferredCourse' => 'Data Science', 'englishTest' => 'IELTS',
            'preferredIntake' => 'September 2027', 'message' => 'Please review my shortlist.',
            'platform' => 'android', 'consent' => true,
        ])->assertCreated()->assertJsonPath('success', true);

        $enquiry = DB::table('counselling_enquiries')->first();
        $this->assertSame('GEIC-'.$enquiry->id, $response->json('reference'));
        $this->assertSame($kind, $enquiry->source_form);
        $this->assertSame('Data Science', $enquiry->preferred_course);
        $this->assertSame($reference, json_decode($enquiry->metadata, true)['reference_id']);
        $this->assertDatabaseCount('counselling_enquiries', 1);
        $this->assertDatabaseHas('site_events', ['path' => '/mobile/'.$kind, 'label' => $reference]);
    }

    public function test_tracking_failure_returns_500_and_rolls_back_the_enquiry(): void
    {
        DB::unprepared("CREATE TRIGGER reject_test_tracking BEFORE INSERT ON site_events BEGIN SELECT RAISE(ABORT, 'Simulated tracking failure'); END");

        $this->postJson(route('api.mobile.enquiries.store'), [
            'kind' => 'counselling', 'fullName' => 'Rollback Test',
            'email' => 'rollback@example.com', 'phone' => '+91 90000 00000', 'consent' => true,
        ])->assertInternalServerError();

        $this->assertDatabaseCount('counselling_enquiries', 0);
        $this->assertDatabaseCount('site_events', 0);
    }
}
