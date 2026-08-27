<?php

namespace Tests\Feature;

use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_admin_sign_in_page(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_administrator_can_sign_in_and_view_enquiries(): void
    {
        $admin = User::create([
            'name' => 'Trans Globe Indore Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-safe-password'),
            'is_admin' => true,
        ]);

        $this->post('/contact/enquire', [
            'full_name' => 'Dashboard Student',
            'email' => 'student@example.com',
            'phone' => '+91 98765 43210',
            'city' => 'Indore',
            'study_level' => 'Postgraduate',
            'preferred_intake' => 'Next available intake',
            'preferred_course' => 'Business Analytics',
            'english_test' => 'Planning to take a test',
            'consent' => '1',
        ]);

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'a-safe-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Trans Globe Indore LMS')
            ->assertSee('data-admin-shell', false)
            ->assertSee('assets/admin/trans-globe-indore-logo-horizontal.svg', false)
            ->assertSee('assets/admin/trans-globe-indore-icon.svg', false)
            ->assertSee('Gilroy-Regular.woff2', false)
            ->assertSee('--admin-primary:#e31e24', false)
            ->assertSee('--admin-hover:#f3951e', false)
            ->assertDontSee('fonts.googleapis.com', false)
            ->assertSee('Dashboard Student');
    }

    public function test_non_administrators_cannot_sign_in_to_the_dashboard(): void
    {
        User::create([
            'name' => 'Student',
            'email' => 'student@example.com',
            'password' => Hash::make('a-safe-password'),
        ]);

        $this->post('/admin/login', [
            'email' => 'student@example.com',
            'password' => 'a-safe-password',
        ])->assertSessionHasErrors('email');
    }

    public function test_administrator_can_edit_geic_homepage_content(): void
    {
        $admin = User::create([
            'name' => 'Trans Globe Indore Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-safe-password'),
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/pages')
            ->assertOk()
            ->assertSee('Manage website content');

        $this->actingAs($admin)
            ->get('/admin/pages')
            ->assertOk()
            ->assertSee('Landing pages')
            ->assertSee('Destinations')
            ->assertSee('Services')
            ->assertSee('Home page')
            ->assertDontSee('Destination · Australia');

        $this->actingAs($admin)
            ->get('/admin/pages?group=destinations')
            ->assertOk()
            ->assertSee('Destination · Australia')
            ->assertSee('assets/transglobe/destinations/australia-detail-hero.jpg', false)
            ->assertSee('content-card__thumbnail', false)
            ->assertDontSee('Home page');

        $this->actingAs($admin)
            ->get('/admin/pages/home')
            ->assertOk()
            ->assertSee('Step 1 of 16')
            ->assertSee('University network')
            ->assertSee('Google reviews')
            ->assertSee('Frequently asked questions')
            ->assertSee('Conversion CTA')
            ->assertSee('Media usage')
            ->assertSee('Study-destination card gallery')
            ->assertSee('Save draft')
            ->assertSee('Publish changes');

        $this->actingAs($admin)
            ->put('/admin/pages/home', ['content' => [
                'hero_eyebrow' => 'Trusted since 1992',
                'hero_title' => 'A practical international future',
                'hero_copy' => 'Content managed from the GEIC dashboard.',
                'hero_image' => 'assets/transglobe/services/services-team.avif',
                'header_nav_services' => 'Support services',
                'hero_primary_cta_label' => 'Start my study plan',
                'journey_step_two_title' => 'Build your shortlist',
                'universities_title' => 'Our global university network',
                'faq_one_question' => 'How does GEIC support students?',
                'footer_cta_label' => 'Plan with our team',
            ], 'intent' => 'publish'])
            ->assertRedirect('/admin/pages/home');

        $this->assertDatabaseHas('site_contents', [
            'page_key' => 'home',
            'field_key' => 'hero_title',
            'value' => 'A practical international future',
        ]);
        $this->assertDatabaseHas('site_contents', [
            'page_key' => 'home',
            'field_key' => 'footer_cta_label',
            'value' => 'Plan with our team',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('A practical international future')
            ->assertSee('Support services')
            ->assertSee('Start my study plan')
            ->assertSee('Build your shortlist')
            ->assertSee('Our global university network')
            ->assertSee('How does GEIC support students?')
            ->assertSee('Plan with our team');
    }

    public function test_administrator_can_create_and_publish_a_new_page_but_cannot_replace_home(): void
    {
        $admin = User::create([
            'name' => 'Trans Globe Indore Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-safe-password'),
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/pages/create?group=tests')
            ->assertOk()
            ->assertSee('Add a new page')
            ->assertSee('Home stays protected');

        $this->actingAs($admin)
            ->post('/admin/pages', [
                'name' => 'Duolingo English Test',
                'slug' => 'duolingo-english-test',
                'group' => 'tests',
                'description' => 'A new test preparation page.',
                'hero_title' => 'Prepare for the Duolingo English Test',
                'hero_copy' => 'Build your language confidence with a practical preparation plan.',
                'hero_image' => 'assets/services/university-admissions.jpg',
            ])
            ->assertRedirect('/admin/pages/test.duolingo-english-test');

        $this->assertDatabaseHas('cms_pages', [
            'page_key' => 'test.duolingo-english-test',
            'group' => 'tests',
            'path' => 'tests/duolingo-english-test',
        ]);

        $this->get('/tests/duolingo-english-test')->assertNotFound();

        $this->actingAs($admin)
            ->put('/admin/pages/test.duolingo-english-test', [
                'content' => [
                    'hero_title' => 'Prepare for the Duolingo English Test',
                    'content_title' => 'A focused preparation pathway',
                ],
                'intent' => 'publish',
            ])
            ->assertRedirect('/admin/pages/test.duolingo-english-test');

        $this->get('/tests/duolingo-english-test')
            ->assertOk()
            ->assertSee('Prepare for the Duolingo English Test')
            ->assertSee('A focused preparation pathway');

        $this->actingAs($admin)
            ->from('/admin/pages/create')
            ->post('/admin/pages', [
                'name' => 'Replacement Home',
                'slug' => 'home',
                'group' => 'landing',
                'hero_title' => 'Replacement Home',
                'hero_copy' => 'This must not be created.',
            ])
            ->assertRedirect('/admin/pages/create')
            ->assertSessionHasErrors('slug');

        $this->assertDatabaseMissing('cms_pages', ['slug' => 'home']);
    }

    public function test_destination_editor_controls_complete_content_and_uploaded_images(): void
    {
        Storage::fake('public');
        $admin = User::create([
            'name' => 'Trans Globe Indore Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-safe-password'),
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/pages/destination.australia')
            ->assertOk()
            ->assertSee('Page identity')
            ->assertSee('Lifestyle gallery')
            ->assertSee('Requirements &amp; visa', false)
            ->assertSee('Careers &amp; intakes', false)
            ->assertSee('Universities')
            ->assertSee('Frequently asked questions')
            ->assertSee('Drag &amp; drop or', false)
            ->assertSee('Choose from library');

        $this->actingAs($admin)
            ->put('/admin/pages/destination.australia', [
                'content' => [
                    'hero_title' => 'A new Australia study journey',
                    'overview_title' => 'Australia overview managed in CMS',
                    'benefit_1_title' => 'Editable destination benefit',
                    'cost_1_value' => 'AUD 30K–42K',
                    'faq_1_question' => 'Is every section editable?',
                ],
                'content_images' => [
                    'hero_image' => UploadedFile::fake()->createWithContent(
                        'australia-new.png',
                        base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
                    ),
                ],
                'intent' => 'publish',
            ])
            ->assertRedirect('/admin/pages/destination.australia');

        $heroImage = SiteContent::query()
            ->where('page_key', 'destination.australia')
            ->where('field_key', 'hero_image')
            ->value('value');

        $this->assertNotNull($heroImage);
        $this->assertStringStartsWith('storage/cms/pages/', $heroImage);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $heroImage));

        $this->actingAs($admin)
            ->get('/admin/pages/destination.australia')
            ->assertOk()
            ->assertSee('Choose from media library')
            ->assertSee('australia-new.png')
            ->assertSee($heroImage);

        $this->get('/destinations/australia')
            ->assertOk()
            ->assertSee('A new Australia study journey')
            ->assertSee('Australia overview managed in CMS')
            ->assertSee('Editable destination benefit')
            ->assertSee('AUD 30K–42K')
            ->assertSee('Is every section editable?')
            ->assertSee($heroImage);
    }

    public function test_draft_homepage_content_stays_private_until_it_is_published(): void
    {
        $admin = User::create([
            'name' => 'Trans Globe Indore Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-safe-password'),
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->put('/admin/pages/home', ['content' => [
                'hero_title' => 'Private draft headline',
            ], 'intent' => 'draft'])
            ->assertRedirect('/admin/pages/home');

        $this->assertDatabaseHas('cms_page_states', [
            'page_key' => 'home',
            'status' => 'draft',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Private draft headline');
    }

    public function test_administrator_can_unpublish_a_published_cms_page_version(): void
    {
        $admin = User::create([
            'name' => 'Trans Globe Indore Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-safe-password'),
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->put('/admin/pages/home', ['content' => [
                'hero_title' => 'Published before unpublishing',
            ], 'intent' => 'publish'])
            ->assertRedirect('/admin/pages/home');

        $this->actingAs($admin)
            ->delete('/admin/pages/home/published')
            ->assertRedirect('/admin/pages/home');

        $this->assertDatabaseHas('cms_page_states', [
            'page_key' => 'home',
            'status' => 'unpublished',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Published before unpublishing');
    }
}
