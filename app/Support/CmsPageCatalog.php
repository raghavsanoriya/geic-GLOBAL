<?php

namespace App\Support;

class CmsPageCatalog
{
    public static function all(): array
    {
        $pages = [
            self::page('home', 'Home page', 'The main GEIC / Trans Globe landing page', [
                self::field('hero_eyebrow', 'Hero eyebrow', 'Since 1992 · Built on trust'),
                self::field('hero_title', 'Hero title', 'Shape Your Ambition Into International Success'),
                self::field('hero_copy', 'Hero description', 'At Trans Globe Indore, managed by GEIC, every student and every dream matters.' , 'textarea'),
                self::field('hero_image', 'Hero image URL', 'store/landing_builder/landing_13/371/hero_image_j5t.png', 'image'),
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

    private static function field(string $key, string $label, string $default, string $type = 'text'): array
    {
        return compact('key', 'label', 'default', 'type');
    }
}
