<?php

namespace App\Support;

class CmsPageCatalog
{
    public static function all(): array
    {
        $pages = [
            self::page('home', 'Home page', 'Manage the header, hero, main content, conversion CTA and footer of the landing page.', [
                self::field('header_about_label', 'About link label', 'About GEIC Indore', 'text', 'Header & navigation'),
                self::field('header_contact_label', 'Contact link label', 'Contact', 'text', 'Header & navigation'),
                self::field('header_cta_label', 'Header button label', 'Speak to a Counsellor', 'text', 'Header & navigation'),
                self::field('header_nav_home', 'Home navigation label', 'Home', 'text', 'Header & navigation'),
                self::field('header_nav_destinations', 'Destinations navigation label', 'Destinations', 'text', 'Header & navigation'),
                self::field('header_nav_services', 'Services navigation label', 'Services', 'text', 'Header & navigation'),
                self::field('header_nav_scholarships', 'Scholarships navigation label', 'Scholarships', 'text', 'Header & navigation'),
                self::field('header_nav_tests', 'Test-prep navigation label', 'Test Prep', 'text', 'Header & navigation'),
                self::field('hero_eyebrow', 'Hero eyebrow', 'Since 1992', 'text', 'Hero'),
                self::field('hero_trust_line', 'Hero trust line', 'Built on trust. Driven by student success.', 'text', 'Hero'),
                self::field('hero_title', 'Hero title', 'Shape Your Ambition Into International Success', 'text', 'Hero'),
                self::field('hero_copy', 'Hero description', 'At Trans Globe Indore, managed by GEIC, every student and every dream matters. From choosing the right course to securing your visa, our specialists guide you through every step of studying abroad.', 'textarea', 'Hero'),
                self::field('hero_image', 'Hero image URL', 'store/landing_builder/landing_13/371/hero_image_j5t.png', 'image', 'Hero'),
                self::field('hero_primary_cta_label', 'Primary button label', 'Book Free Counselling', 'text', 'Hero CTA'),
                self::field('hero_primary_cta_url', 'Primary button link', '#contact', 'text', 'Hero CTA'),
                self::field('hero_secondary_cta_label', 'Secondary button label', 'Explore Destinations', 'text', 'Hero CTA'),
                self::field('hero_secondary_cta_url', 'Secondary button link', '/destinations', 'text', 'Hero CTA'),
                self::field('hero_proof_title', 'Trust badge headline', '70,250+ students placed worldwide', 'text', 'Hero CTA'),
                self::field('hero_proof_copy', 'Trust badge description', 'Across leading universities in 10+ countries', 'text', 'Hero CTA'),
                self::field('journey_eyebrow', 'Section eyebrow', 'How it works', 'text', 'Journey section'),
                self::field('journey_title', 'Section title', 'Four Simple Steps to Study Abroad', 'text', 'Journey section'),
                self::field('journey_copy', 'Section description', 'From your first conversation to the day you board your flight, Trans Globe Indore makes the process clear, personal and manageable.', 'textarea', 'Journey section'),
                self::field('journey_step_one_title', 'Step 1 title', 'Tell Us About Yourself', 'text', 'Journey section'),
                self::field('journey_step_one_copy', 'Step 1 description', 'Share your academic background, interests, preferred countries and career goals. This first conversation helps us understand where you want to go.', 'textarea', 'Journey section'),
                self::field('journey_step_two_title', 'Step 2 title', 'Meet a Specialist', 'text', 'Journey section'),
                self::field('journey_step_two_copy', 'Step 2 description', 'Work with a country-and-course specialist to shortlist universities, explore scholarships and build a plan that suits your profile and budget.', 'textarea', 'Journey section'),
                self::field('journey_step_three_title', 'Step 3 title', 'Apply With Confidence', 'text', 'Journey section'),
                self::field('journey_step_three_copy', 'Step 3 description', 'We help with your SOP, recommendations, transcripts, documents and application forms so every submission is complete and compelling.', 'textarea', 'Journey section'),
                self::field('journey_step_four_title', 'Step 4 title', 'Get Your Visa & Go', 'text', 'Journey section'),
                self::field('journey_step_four_copy', 'Step 4 description', 'Our visa team prepares your documents, finances and interview answers so you can travel knowing everything is in order.', 'textarea', 'Journey section'),
                self::field('services_eyebrow', 'Section eyebrow', 'Our services', 'text', 'Services section'),
                self::field('services_title', 'Section title', 'Everything You Need, Under One Roof', 'text', 'Services section'),
                self::field('services_copy', 'Section description', 'Trans Globe Indore supports your complete journey—from your first exam and university application to your arrival in a new country.', 'textarea', 'Services section'),
                self::field('services_cta_label', 'Section button label', 'Discuss Your Study Plan', 'text', 'Services section'),
                self::field('services_cta_url', 'Section button link', '#contact', 'text', 'Services section'),
                self::field('destinations_eyebrow', 'Section eyebrow', 'Where will you thrive?', 'text', 'Destinations section'),
                self::field('destinations_title', 'Section title', 'Explore the World’s Best Study Destinations', 'text', 'Destinations section'),
                self::field('destinations_copy', 'Section description', 'From research-led universities to affordable public education and strong post-study pathways, discover the destination that best fits your future.', 'textarea', 'Destinations section'),
                self::field('contact_eyebrow', 'CTA badge', 'Free, no-pressure guidance', 'text', 'Conversion CTA'),
                self::field('contact_title', 'CTA title', 'Your Journey Starts With One Conversation', 'text', 'Conversion CTA'),
                self::field('contact_copy', 'CTA description', 'You do not need to have everything figured out. Tell us where you are today, and we will explain your options honestly and help you take the next step.', 'textarea', 'Conversion CTA'),
                self::field('contact_button_label', 'CTA button label', 'Speak to Our Indore Counsellor', 'text', 'Conversion CTA'),
                self::field('contact_phone', 'Phone number', '+91 98266 66886', 'text', 'Conversion CTA'),
                self::field('contact_email', 'Email address', 'info@geic.in', 'text', 'Conversion CTA'),
                self::field('contact_address', 'Office address', 'Office No. 503, THE VIEW Tower 1, Yeshwant Niwas Rd, above Jade Blue Showroom, Nehru Park 2, Lad Colony, Indore, Madhya Pradesh 452001', 'textarea', 'Conversion CTA'),
                self::field('footer_newsletter_title', 'Newsletter title', 'Stay in the Study-Abroad Loop', 'text', 'Footer'),
                self::field('footer_newsletter_copy', 'Newsletter description', 'Get visa updates, scholarship alerts and honest study-abroad advice in your inbox.', 'textarea', 'Footer'),
                self::field('footer_newsletter_button', 'Newsletter button label', 'Join', 'text', 'Footer'),
                self::field('footer_badge', 'Footer badge text', 'Your journey with Trans Globe Indore starts here', 'text', 'Footer'),
                self::field('footer_title', 'Footer title', 'Start With GEIC Indore', 'text', 'Footer'),
                self::field('footer_cta_label', 'Footer button label', 'Book Free Counselling', 'text', 'Footer'),
                self::field('footer_cta_url', 'Footer button link', '/contact#enquiry', 'text', 'Footer'),
                self::field('footer_contact_heading', 'Contact column heading', 'Contact Us', 'text', 'Footer'),
                self::field('footer_hours', 'Office hours', 'Mon to Sat: 10:00 AM–6:30 PM', 'text', 'Footer'),
                self::field('footer_copyright', 'Footer copyright', '© 2026 Trans Globe Indore, managed by GEIC. Your trusted partner for global education.', 'textarea', 'Footer'),
            ]),
            self::page('destinations', 'Destinations', 'Country listing and destination discovery page', [
                self::field('hero_title', 'Hero title', 'Find the country that fits your ambition'),
                self::field('hero_copy', 'Hero description', 'Compare leading study destinations, understand what makes each one different and choose your next step with guidance from Trans Globe Indore.', 'textarea'),
                self::field('hero_image', 'Primary hero image URL', 'assets/transglobe/destinations/australia.jpg', 'image'),
            ]),
            self::page('services', 'Services', 'Service list and counselling support overview', [
                self::field('hero_title', 'Hero title', 'Every expert step for your global future.'),
                self::field('hero_copy', 'Hero description', 'From your first shortlist to your first day abroad, get one joined-up team for every important decision, document and deadline.', 'textarea'),
                self::field('hero_image', 'Primary hero image URL', 'assets/transglobe/services/services-team.avif', 'image'),
            ]),
            self::page('scholarships', 'Scholarships', 'Scholarship listing and funding guidance', [
                self::field('hero_title', 'Hero title', 'Fund your future, without the guesswork.'),
                self::field('hero_copy', 'Hero description', 'Studying abroad does not have to feel out of reach. Discover scholarship opportunities that match your profile, then apply with a clear, well-prepared plan.', 'textarea'),
                self::field('hero_image', 'Hero image URL', 'assets/transglobe/destinations/australia-detail-hero.jpg', 'image'),
            ]),
            self::page('tests', 'Test preparation', 'IELTS, PTE, TOEFL and other test-prep overview', [
                self::field('hero_title', 'Hero title', 'Prepare with purpose. Test with confidence.'),
                self::field('hero_copy', 'Hero description', 'Choose the right test for your destination, build the skills it measures and move into your university application with a score plan that makes sense.', 'textarea'),
                self::field('hero_image', 'Hero image URL', 'assets/services/university-admissions.jpg', 'image'),
            ]),
            self::page('contact', 'Contact', 'Contact information and enquiry page', [
                self::field('hero_title', 'Hero title', 'Let’s make your next step clear.'),
                self::field('hero_copy', 'Hero description', 'Speak with the Trans Globe Indore team about study destinations, applications, scholarships, test preparation or your student-visa pathway.', 'textarea'),
                self::field('hero_image', 'Hero image URL', 'assets/services/expert-counselling.jpg', 'image'),
            ]),
        ];

        foreach (DestinationCatalog::slugs() as $slug) {
            $item = DestinationCatalog::find($slug);
            $pages[] = self::detail('destination.'.$slug, 'Destination · '.$item['name'], 'Destination details page', $item['name'], $item['tagline'], $item['hero']);
        }
        foreach (ServiceCatalog::all() as $item) {
            $pages[] = self::detail('service.'.$item['slug'], 'Service · '.$item['title'], 'Service details page', $item['title'], $item['summary'], $item['image']);
        }
        foreach (ScholarshipCatalog::all() as $item) {
            $pages[] = self::detail('scholarship.'.$item['slug'], 'Scholarship · '.$item['name'], 'Scholarship details page', 'Find your '.$item['name'].' funding path.', $item['tagline'], $item['image']);
        }
        foreach (TestPrepCatalog::all() as $item) {
            $pages[] = self::detail('test.'.$item['slug'], 'Test · '.$item['title'], 'Test preparation details page', $item['title'], $item['summary'], $item['image']);
        }

        return $pages;
    }

    public static function find(string $key): ?array
    {
        foreach (self::all() as $page) {
            if ($page['key'] === $key) {
                return $page;
            }
        }

        return null;
    }

    /**
     * Return non-editable visual groups used by a public page.
     *
     * These groups sit alongside image fields in the CMS so editors can see
     * the complete visual footprint of a page, not just its editable hero.
     * `usageCount` is the number of individual assets rendered in that group.
     *
     * @return array<int, array{label: string, section: string, path: string, usageCount: int}>
     */
    public static function mediaGroupsForPage(string $pageKey): array
    {
        if ($pageKey === 'home') {
            return [
                ['label' => 'Hero global-orbit graphic', 'section' => 'Hero', 'path' => 'assets/transglobe/geic-revolver.svg?v=20260825b', 'usageCount' => 1],
                ['label' => 'Hero supporting overlay', 'section' => 'Hero', 'path' => 'store/landing_builder/landing_13/371/hero_overlay_UGc.png', 'usageCount' => 1],
                ['label' => 'Statistics background texture', 'section' => 'Highlights', 'path' => 'store/landing_builder/landing_13/372/statistics_bg_T0k.png', 'usageCount' => 1],
                ['label' => 'Study-destination card gallery', 'section' => 'Destinations section', 'path' => 'assets/transglobe/destinations/australia.jpg', 'usageCount' => 24],
                ['label' => 'Work-visa pathway cards', 'section' => 'Work visa pathways', 'path' => 'assets/transglobe/destinations/canada.jpg', 'usageCount' => 6],
                ['label' => 'Partner-university logo carousel', 'section' => 'Partner universities', 'path' => 'assets/transglobe/universities/australian-national-university.png', 'usageCount' => 10],
                ['label' => 'Study-abroad blog cards', 'section' => 'Blog', 'path' => 'assets/transglobe/destinations/australia/campus-students.jpg', 'usageCount' => 5],
                ['label' => 'Google-review profile images', 'section' => 'Student reviews', 'path' => 'assets/geic/reviewers/arun-rawat.jpg', 'usageCount' => 10],
            ];
        }

        if (str_starts_with($pageKey, 'destination.')) {
            $destination = DestinationCatalog::find(substr($pageKey, strlen('destination.')));

            if (! $destination) {
                return [];
            }

            return [
                ['label' => 'Country flag', 'section' => 'Hero', 'path' => 'assets/transglobe/destinations/flags/'.$destination['flag'], 'usageCount' => 1],
                ['label' => 'Destination lifestyle gallery', 'section' => 'Discover '.$destination['name'], 'path' => $destination['gallery'][0]['src'], 'usageCount' => count($destination['gallery'])],
                ['label' => 'Career and intake visual', 'section' => 'Courses & careers', 'path' => $destination['support_image']['src'], 'usageCount' => 1],
                ['label' => 'University logo network', 'section' => 'Universities', 'path' => $destination['universities'][0]['logo'] ?? 'assets/transglobe/geic-revolver.svg', 'usageCount' => count($destination['universities'])],
            ];
        }

        return [];
    }

    private static function page(string $key, string $name, string $description, array $fields): array
    {
        return compact('key', 'name', 'description', 'fields');
    }

    private static function detail(string $key, string $name, string $description, string $title, string $copy, string $image): array
    {
        return self::page($key, $name, $description, [
            self::field('hero_title', 'Hero title', $title),
            self::field('hero_copy', 'Hero description', $copy, 'textarea'),
            self::field('hero_image', 'Hero image URL', $image, 'image'),
        ]);
    }

    private static function field(string $key, string $label, string $default, string $type = 'text', string $section = 'Hero'): array
    {
        return compact('key', 'label', 'default', 'type', 'section');
    }
}
