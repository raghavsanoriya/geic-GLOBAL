<?php

namespace App\Support;

use App\Models\CmsPage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

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
                self::field('stat_students_value', 'Students placed value', '70,250+', 'text', 'Highlights'),
                self::field('stat_students_label', 'Students placed label', 'Students Placed', 'text', 'Highlights'),
                self::field('stat_universities_value', 'Partner universities value', '800+', 'text', 'Highlights'),
                self::field('stat_universities_label', 'Partner universities label', 'Partner Universities', 'text', 'Highlights'),
                self::field('stat_visas_value', 'Visa success value', '98.7%', 'text', 'Highlights'),
                self::field('stat_visas_label', 'Visa success label', 'Visa Success Rate', 'text', 'Highlights'),
                self::field('stat_branches_value', 'Branches value', '16+', 'text', 'Highlights'),
                self::field('stat_branches_label', 'Branches label', 'Branches in India & Nepal', 'text', 'Highlights'),
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
                self::field('service_one_title', 'Service 1 title', 'Expert Counselling', 'text', 'Service cards'),
                self::field('service_one_copy', 'Service 1 description', 'Find the country, university and course that genuinely fit your goals—without being pushed toward a particular institution.', 'textarea', 'Service cards'),
                self::field('service_two_title', 'Service 2 title', 'University Admissions', 'text', 'Service cards'),
                self::field('service_two_copy', 'Service 2 description', 'Build a strong, error-free application with the right SOP, recommendation letters, documents and submission timeline.', 'textarea', 'Service cards'),
                self::field('service_three_title', 'Service 3 title', 'Scholarship Guidance', 'text', 'Service cards'),
                self::field('service_three_copy', 'Service 3 description', 'Discover scholarships and bursaries you may not know you qualify for. More than 2,000 Trans Globe students receive awards each year.', 'textarea', 'Service cards'),
                self::field('service_four_title', 'Service 4 title', 'IELTS, PTE, TOEFL & More', 'text', 'Service cards'),
                self::field('service_four_copy', 'Service 4 description', 'Prepare for IELTS, PTE, TOEFL, GRE, GMAT and SAT with expert trainers, realistic practice and proven score-improvement support.', 'textarea', 'Service cards'),
                self::field('service_five_title', 'Service 5 title', 'Visa Assistance', 'text', 'Service cards'),
                self::field('service_five_copy', 'Service 5 description', 'Avoid incomplete or inconsistent applications with detailed document checks, financial guidance and interview preparation.', 'textarea', 'Service cards'),
                self::field('service_six_title', 'Service 6 title', 'Pre & Post Departure Support', 'text', 'Service cards'),
                self::field('service_six_copy', 'Service 6 description', 'Get practical help with packing, banking, arrival and settling into your new university city—you are never doing this alone.', 'textarea', 'Service cards'),
                self::field('destinations_eyebrow', 'Section eyebrow', 'Where will you thrive?', 'text', 'Destinations section'),
                self::field('destinations_title', 'Section title', 'Explore the World’s Best Study Destinations', 'text', 'Destinations section'),
                self::field('destinations_copy', 'Section description', 'From research-led universities to affordable public education and strong post-study pathways, discover the destination that best fits your future.', 'textarea', 'Destinations section'),
                self::field('work_visa_badge', 'Section badge', 'Work visa pathways', 'text', 'Work visa pathways'),
                self::field('work_visa_title', 'Section title', 'Build Your Career Abroad', 'text', 'Work visa pathways'),
                self::field('work_visa_copy', 'Section description', 'Explore skilled-work opportunities with practical guidance on eligibility, documentation and the right pathway for your profile.', 'textarea', 'Work visa pathways'),
                self::field('work_visa_cta_label', 'Section button label', 'Free Profile Assessment', 'text', 'Work visa pathways'),
                self::field('work_visa_cta_url', 'Section button link', '#contact', 'text', 'Work visa pathways'),
                self::field('universities_kicker', 'Section badge', 'Global partner network', 'text', 'University network'),
                self::field('universities_title', 'Section title', '800+ University Tie-Ups Worldwide', 'text', 'University network'),
                self::field('universities_copy', 'Section description', 'Explore opportunities across a trusted global network of leading universities and find the institution that fits your ambitions.', 'textarea', 'University network'),
                self::field('universities_cta_label', 'Section button label', 'View All Universities', 'text', 'University network'),
                self::field('universities_cta_url', 'Section button link', '#contact', 'text', 'University network'),
                self::field('why_eyebrow', 'Section eyebrow', 'Why it matters', 'text', 'Why it matters'),
                self::field('why_title', 'Section title', 'Why the Right Consultants in India', 'text', 'Why it matters'),
                self::field('why_title_accent', 'Highlighted title', 'Make All the Difference', 'text', 'Why it matters'),
                self::field('why_scale_title', 'Opportunity card title', 'The Scale of the Opportunity', 'text', 'Why it matters'),
                self::field('why_scale_copy', 'Opportunity card text', "Every year, over 1.3 million Indian students study abroad—to build genuinely global careers, access research facilities that don't exist in India, and earn degrees that open doors everywhere. Visa policies, deadlines and scholarship requirements change every year, making experienced guidance genuinely important.", 'textarea', 'Why it matters'),
                self::field('why_consultant_title', 'Consultant card title', 'What Makes a Good Consultant', 'text', 'Why it matters'),
                self::field('why_consultant_copy', 'Consultant card text', 'Not all study-abroad consultants are the same. Trans Globe counsellors are specialists, not generalists—each focuses on specific countries and visa processes. With 800+ university affiliations, recommendations focus on what is right for the student.', 'textarea', 'Why it matters'),
                self::field('why_india_title', 'India coverage card title', 'Covering All of India', 'text', 'Why it matters'),
                self::field('why_india_copy', 'India coverage card text', 'Trans Globe has 16 offices across India plus an international office in Kathmandu, Nepal and online counselling. This reach helps our counsellors understand students from different cities, academic systems and backgrounds.', 'textarea', 'Why it matters'),
                self::field('why_countries_title', 'Countries card title', 'The Countries We Specialise In', 'text', 'Why it matters'),
                self::field('why_countries_copy', 'Countries card text', 'We help students study in Australia, Canada, the USA, the UK, Germany, New Zealand, Ireland, Singapore, Dubai & the UAE, and across Europe. Our specialists continuously track destination-specific requirements and post-study opportunities.', 'textarea', 'Why it matters'),
                self::field('why_track_title', 'Track record title', 'Our Track Record', 'text', 'Why it matters'),
                self::field('why_track_copy', 'Track record text', 'Since 1992, Trans Globe has placed more than 70,250 students at universities in over 10 countries. Its 98.7% visa success rate and strong scholarship outcomes are built on decades of experience and a clear understanding of what universities and visa officers require.', 'textarea', 'Why it matters'),
                self::field('blog_eyebrow', 'Section eyebrow', 'From the blog', 'text', 'Blog section'),
                self::field('blog_title', 'Section title', 'Fresh Study-Abroad Insights, Without the Jargon', 'text', 'Blog section'),
                self::field('blog_copy', 'Section description', 'Recent guidance from Trans Globe on university admissions, student visas and choosing the right destination for your future.', 'textarea', 'Blog section'),
                self::field('blog_cta_label', 'Section button label', 'Explore all articles', 'text', 'Blog section'),
                self::field('blog_cta_url', 'Section button link', 'https://transglobeedu.com/blogs', 'text', 'Blog section'),
                self::field('reviews_eyebrow', 'Section eyebrow', 'Student experiences', 'text', 'Google reviews'),
                self::field('reviews_title', 'Section title', 'Real Students. Real Google Reviews.', 'text', 'Google reviews'),
                self::field('reviews_copy', 'Section description', 'Hear directly from students who trusted Trans Globe Indore, managed by GEIC, for counselling, admissions, visa support and test preparation.', 'textarea', 'Google reviews'),
                self::field('reviews_score', 'Google rating', '4.8', 'text', 'Google reviews'),
                self::field('reviews_count_label', 'Google review count', '495 Google reviews', 'text', 'Google reviews'),
                self::field('reviews_url', 'Google reviews link', 'https://www.google.com/search?q=geic+indore#lrd=0x3962fd400e5c61eb:0x6db8cf73bcf20625,1,,,,', 'text', 'Google reviews'),
                self::field('faq_eyebrow', 'Section eyebrow', 'Questions, answered', 'text', 'Frequently asked questions'),
                self::field('faq_title', 'Section title', 'Frequently Asked Questions', 'text', 'Frequently asked questions'),
                self::field('faq_copy', 'Section description', 'Clear answers to the questions students and families ask before beginning their international education journey.', 'textarea', 'Frequently asked questions'),
                self::field('faq_one_question', 'Question 1', 'What does a study-abroad consultant do?', 'text', 'Frequently asked questions'),
                self::field('faq_one_answer', 'Answer 1', 'A consultant helps you choose a country and course, shortlist universities, strengthen applications, find scholarships, prepare your student visa and get ready for life abroad. At Trans Globe Indore, this support is free for students.', 'textarea', 'Frequently asked questions'),
                self::field('faq_two_question', 'Question 2', 'How early should I start the application process?', 'text', 'Frequently asked questions'),
                self::field('faq_two_answer', 'Answer 2', 'For most destinations, begin 12 to 18 months before your intended intake. This leaves enough time for language tests, university applications, scholarships and visa processing.', 'textarea', 'Frequently asked questions'),
                self::field('faq_three_question', 'Question 3', 'Can Indian students get scholarships?', 'text', 'Frequently asked questions'),
                self::field('faq_three_answer', 'Answer 3', 'Yes. Universities, governments and private organisations offer awards based on merit, field of study, financial need and other criteria. Trans Globe Indore helps identify and apply for suitable options.', 'textarea', 'Frequently asked questions'),
                self::field('faq_four_question', 'Question 4', 'What is Trans Globe’s visa success rate?', 'text', 'Frequently asked questions'),
                self::field('faq_four_answer', 'Answer 4', 'The Trans Globe network reports a 98.7% visa success rate, built on decades of experience preparing complete, consistent applications for students across 10+ countries.', 'textarea', 'Frequently asked questions'),
                self::field('faq_five_question', 'Question 5', 'Can I get counselling if I cannot visit the Indore office?', 'text', 'Frequently asked questions'),
                self::field('faq_five_answer', 'Answer 5', 'Yes. Trans Globe Indore offers online counselling sessions with the same detailed, specialist guidance available at our Indore office.', 'textarea', 'Frequently asked questions'),
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
            $pages[] = self::page('destination.'.$slug, 'Destination · '.$item['name'], 'Complete destination details, visuals, requirements, costs, careers, universities and FAQs.', self::destinationFields($item));
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

        try {
            if (Schema::hasTable('cms_pages')) {
                foreach (CmsPage::query()->orderBy('group')->orderBy('name')->get() as $customPage) {
                    $pages[] = self::customPage($customPage);
                }
            }
        } catch (QueryException) {
            // The fixed catalogue remains available during setup or migration.
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

    /** @return array<string, array{label: string, description: string}> */
    public static function groups(): array
    {
        return [
            'landing' => ['label' => 'Landing pages', 'description' => 'Main website pages and conversion journeys'],
            'destinations' => ['label' => 'Destinations', 'description' => 'Country-specific study destination pages'],
            'services' => ['label' => 'Services', 'description' => 'Individual counselling and student-support services'],
            'scholarships' => ['label' => 'Scholarships', 'description' => 'Country scholarship and funding pages'],
            'tests' => ['label' => 'Test preparation', 'description' => 'IELTS, PTE, TOEFL and preparation pages'],
        ];
    }

    public static function groupFor(string $pageKey): string
    {
        return match (true) {
            str_starts_with($pageKey, 'destination.') => 'destinations',
            str_starts_with($pageKey, 'service.') => 'services',
            str_starts_with($pageKey, 'scholarship.') => 'scholarships',
            str_starts_with($pageKey, 'test.') => 'tests',
            default => 'landing',
        };
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

    private static function destinationFields(array $item): array
    {
        $isAustralia = $item['slug'] === 'australia';
        $name = $item['name'];
        $stats = $item['stats'] ?? [['42', 'Universities nationwide'], ['Go8', 'Plus regional, ATN and IRU networks'], ['2–3 years', 'Common post-study work range'], ['160+', 'Nationalities represented']];
        $benefits = $item['benefits'] ?? [
            ['World-class universities', 'Research-intensive institutions, including the Group of Eight, with globally respected qualifications.'],
            ['Work while studying', 'Eligible international students can work during teaching periods and more during scheduled breaks.'],
            ['Safe and welcoming', 'Strong student protections, multicultural cities and a high standard of living support international students.'],
            ['Wide course choice', 'Flexible pathways across business, engineering, IT, health, hospitality, creative arts and more.'],
            ['Practical learning', 'Internships, industry projects, placements and work-integrated learning build career-ready experience.'],
            ['Strong value', 'Internationally recognised education with scholarships and practical pathways that support long-term outcomes.'],
        ];
        $overview = $item['overview'] ?? 'Australia combines research-focused universities with teaching designed around industry. Students can study across the Group of Eight, technology-focused institutions and strong regional university networks.';
        $overviewTwo = $item['overview_2'] ?? 'Sydney, Melbourne, Brisbane, Perth, Adelaide, Canberra and other student cities offer reliable infrastructure, healthcare, public transport and multicultural communities.';
        $requirements = $isAustralia ? [
            'Valid passport and an updated resume for postgraduate applicants',
            'Class 10, Class 12 and previous degree transcripts and certificates',
            'IELTS, PTE or TOEFL results required by the chosen institution',
            'Academic or professional recommendation letters where applicable',
            'Work experience evidence for selected postgraduate and MBA programs',
        ] : $item['requirements'];
        $costs = $item['costs'] ?? [['Undergraduate tuition', 'AUD 24K–40K'], ['Postgraduate tuition', 'AUD 25K–45K'], ['Living & accommodation', 'AUD 29,710'], ['Airfare planning', 'AUD 2,000']];
        $careers = $item['careers'] ?? ['Commerce & Analytics', 'Machine Learning & AI', 'Nursing & Paramedical', 'Accounting & Finance', 'Hospitality & Tourism', 'Education & Teaching', 'Psychology & Social Sciences', 'Environmental Science'];
        $intakes = $item['intakes'] ?? [['February', 'Main intake with the widest course selection.'], ['July', 'Strong mid-year intake across many institutions.'], ['October', 'Selected programs at participating universities.']];
        $faqs = $item['faqs'];
        $universities = $item['universities'];

        $fields = [
            self::field('destination_name', 'Destination name', $name, 'text', 'Page identity'),
            self::field('hero_label', 'Hero badge', 'Expert guidance from GEIC Indore'),
            self::field('hero_title', 'Hero title', 'Study in '.$name),
            self::field('hero_copy', 'Hero description', $item['tagline'], 'textarea'),
            self::field('hero_image', 'Hero image URL', $item['hero'], 'image'),
            self::field('hero_image_alt', 'Hero image alt text', 'Study in '.$name),
            self::field('hero_image_position', 'Hero image position', $item['hero_position']),
            self::field('flag_image', 'Country flag image URL', 'assets/transglobe/destinations/flags/'.$item['flag'], 'image'),
            self::field('flag_alt', 'Country flag alt text', $name.' flag'),
            self::field('hero_primary_cta_label', 'Primary button label', 'Free consultation'),
            self::field('hero_secondary_cta_label', 'Secondary button label', 'See the complete process'),
        ];
        foreach ($stats as $index => [$value, $label]) {
            $number = $index + 1;
            $fields[] = self::field("stat_{$number}_value", "Statistic {$number} value", $value, 'text', 'Key statistics');
            $fields[] = self::field("stat_{$number}_label", "Statistic {$number} label", $label, 'text', 'Key statistics');
        }
        array_push($fields,
            self::field('overview_kicker', 'Section eyebrow', $name.' at a glance', 'text', 'Overview'),
            self::field('overview_title', 'Section title', $isAustralia ? 'Academic prestige meets real-world learning' : 'A closer look at your study destination', 'text', 'Overview'),
            self::field('overview_copy', 'Overview paragraph 1', $overview, 'textarea', 'Overview'),
            self::field('overview_copy_2', 'Overview paragraph 2', $overviewTwo, 'textarea', 'Overview'),
            self::field('overview_image', 'Overview image URL', $item['card'], 'image', 'Overview'),
            self::field('overview_image_badge', 'Overview image badge', 'Explore '.$name, 'text', 'Overview'),
            self::field('overview_cta_label', 'Overview button label', 'Check requirements', 'text', 'Overview'),
            self::field('benefits_kicker', 'Section eyebrow', 'Why '.$name, 'text', 'Why this destination'),
            self::field('benefits_title', 'Section title', 'A study destination built for ambitious students', 'text', 'Why this destination'),
            self::field('benefits_lead', 'Section description', 'Strong academics, practical experience and an inclusive student environment work together to support both education and employability.', 'textarea', 'Why this destination'),
            self::field('gallery_kicker', 'Section eyebrow', 'Beyond the classroom', 'text', 'Lifestyle gallery'),
            self::field('gallery_title', 'Gallery section title', 'Discover student life in '.$name, 'text', 'Lifestyle gallery'),
            self::field('gallery_lead', 'Gallery section description', 'See the campus, city and student experience behind your study plan.', 'textarea', 'Lifestyle gallery'),
        );
        foreach ($benefits as $index => [$title, $copy]) {
            $number = $index + 1;
            $fields[] = self::field("benefit_{$number}_title", "Benefit {$number} title", $title, 'text', 'Why this destination');
            $fields[] = self::field("benefit_{$number}_copy", "Benefit {$number} description", $copy, 'textarea', 'Why this destination');
        }
        foreach ($item['gallery'] as $index => $image) {
            $number = $index + 1;
            $fields[] = self::field("gallery_{$number}_image", "Gallery image {$number} URL", $image['src'], 'image', 'Lifestyle gallery');
            $fields[] = self::field("gallery_{$number}_alt", "Gallery image {$number} alt text", $image['alt'], 'text', 'Lifestyle gallery');
            $fields[] = self::field("gallery_{$number}_label", "Gallery image {$number} caption", $image['label'], 'text', 'Lifestyle gallery');
        }
        $fields[] = self::field('facts_title', 'At-a-glance title', $name.' at a glance', 'text', 'At a glance');
        $fields[] = self::field('facts_intro', 'At-a-glance introduction', $item['facts_intro'], 'textarea', 'At a glance');
        foreach ($item['facts'] as $index => [$label, $value]) {
            $number = $index + 1;
            $fields[] = self::field("fact_{$number}_label", "Fact {$number} label", $label, 'text', 'At a glance');
            $fields[] = self::field("fact_{$number}_value", "Fact {$number} value", $value, 'text', 'At a glance');
        }
        array_push($fields,
            self::field('journey_kicker', 'Section eyebrow', 'The complete journey', 'text', 'Study journey'),
            self::field('journey_title', 'Section title', 'One connected path from counselling to '.$name, 'text', 'Study journey'),
            self::field('journey_lead', 'Section description', 'Follow one clear route through every milestone, document and decision, with Trans Globe Indore supporting you all the way.', 'textarea', 'Study journey'),
            self::field('journey_count_label', 'Milestone count label', 'Guided milestones', 'text', 'Study journey'),
            self::field('journey_outcome_label', 'Outcome eyebrow', 'Destination reached', 'text', 'Study journey'),
            self::field('journey_outcome_copy', 'Outcome message', 'Arrive informed, prepared and ready for student life in '.$name.'.', 'textarea', 'Study journey'),
            self::field('journey_outcome_cta_label', 'Outcome button label', 'Start my journey', 'text', 'Study journey'),
        );
        foreach ($item['journey'] as $index => [$stage, $title, $copy]) {
            $number = $index + 1;
            $fields[] = self::field("journey_{$number}_stage", "Step {$number} stage", $stage, 'text', 'Study journey');
            $fields[] = self::field("journey_{$number}_title", "Step {$number} title", $title, 'text', 'Study journey');
            $fields[] = self::field("journey_{$number}_copy", "Step {$number} description", $copy, 'textarea', 'Study journey');
        }
        array_push($fields,
            self::field('requirements_kicker', 'Section eyebrow', 'Prepare with confidence', 'text', 'Requirements & visa'),
            self::field('requirements_title', 'Section title', 'Admission and student visa essentials', 'text', 'Requirements & visa'),
            self::field('requirements_lead', 'Section description', 'Exact requirements vary by program and institution; we help you build a complete, well-organised application.', 'textarea', 'Requirements & visa'),
            self::field('requirements_list_title', 'Admission panel title', 'Typical admission documents', 'text', 'Requirements & visa'),
            self::field('requirements_cta_label', 'Eligibility button label', 'Discuss my eligibility', 'text', 'Requirements & visa'),
        );
        foreach ($requirements as $index => $requirement) {
            $number = $index + 1;
            $fields[] = self::field("requirement_{$number}", "Requirement {$number}", $requirement, 'textarea', 'Requirements & visa');
        }
        array_push($fields,
            self::field('visa_kicker', 'Visa eyebrow', 'Student visa', 'text', 'Requirements & visa'),
            self::field('visa_title', 'Visa heading', $item['visa_title'] ?? 'Subclass 500 essentials', 'text', 'Requirements & visa'),
            self::field('visa_copy', 'Visa guidance', $item['visa_copy'] ?? 'Prepare English proficiency, Genuine Student, financial-capacity, Confirmation of Enrolment and health-cover evidence.', 'textarea', 'Requirements & visa'),
            self::field('visa_note', 'Visa disclaimer', 'Visa rules, work rights and financial thresholds can change. Confirm current requirements with the relevant government authority before applying.', 'textarea', 'Requirements & visa'),
            self::field('costs_kicker', 'Section eyebrow', 'Plan your budget', 'text', 'Costs'),
            self::field('costs_title', 'Section title', 'Indicative financial planning', 'text', 'Costs'),
            self::field('costs_lead', 'Section description', 'Use these planning ranges as a starting point. Your course, city, lifestyle and current government charges determine the final amount.', 'textarea', 'Costs'),
            self::field('costs_note', 'Costs disclaimer', 'Figures are indicative and can change. Confirm current tuition, living-cost evidence and visa charges with your counsellor before applying.', 'textarea', 'Costs'),
        );
        foreach ($costs as $index => [$label, $value]) {
            $number = $index + 1;
            $fields[] = self::field("cost_{$number}_label", "Cost {$number} label", $label, 'text', 'Costs');
            $fields[] = self::field("cost_{$number}_value", "Cost {$number} value", $value, 'text', 'Costs');
        }
        array_push($fields,
            self::field('careers_kicker', 'Section eyebrow', 'Future ready', 'text', 'Careers & intakes'),
            self::field('careers_title', 'Section title', 'Build your course and intake shortlist', 'text', 'Careers & intakes'),
            self::field('careers_list_title', 'Career panel title', 'Popular career-focused fields', 'text', 'Careers & intakes'),
            self::field('intakes_title', 'Intake panel title', 'Common intakes', 'text', 'Careers & intakes'),
        );
        foreach ($careers as $index => $career) {
            $number = $index + 1;
            $fields[] = self::field("career_{$number}", "Career field {$number}", $career, 'text', 'Careers & intakes');
        }
        foreach ($intakes as $index => [$title, $copy]) {
            $number = $index + 1;
            $fields[] = self::field("intake_{$number}_title", "Intake {$number} name", $title, 'text', 'Careers & intakes');
            $fields[] = self::field("intake_{$number}_copy", "Intake {$number} description", $copy, 'textarea', 'Careers & intakes');
        }
        $fields[] = self::field('support_image', 'Career and intake image URL', $item['support_image']['src'], 'image', 'Careers & intakes');
        $fields[] = self::field('support_image_alt', 'Career and intake image alt text', $item['support_image']['alt'], 'text', 'Careers & intakes');
        $fields[] = self::field('support_image_caption', 'Career and intake image caption', 'Build experience, networks and career confidence in '.$name.'.', 'text', 'Careers & intakes');
        $fields[] = self::field('universities_kicker', 'Section eyebrow', 'University network', 'text', 'Universities');
        $fields[] = self::field('universities_title', 'Section title', 'Explore institutions in '.$name, 'text', 'Universities');
        $fields[] = self::field('universities_lead', 'Section description', 'Country-specific university options sourced from the Trans Globe destination network.', 'textarea', 'Universities');
        foreach ($universities as $index => $university) {
            $number = $index + 1;
            $fields[] = self::field("university_{$number}_name", "University {$number} name", $university['name'], 'text', 'Universities');
            $fields[] = self::field("university_{$number}_logo", "University {$number} logo URL", $university['logo'], 'image', 'Universities');
        }
        $fields[] = self::field('faq_kicker', 'Section eyebrow', 'Questions, answered', 'text', 'Frequently asked questions');
        $fields[] = self::field('faq_title', 'Section title', 'Study in '.$name.' FAQs', 'text', 'Frequently asked questions');
        foreach ($faqs as $index => [$question, $answer]) {
            $number = $index + 1;
            $fields[] = self::field("faq_{$number}_question", "Question {$number}", $question, 'text', 'Frequently asked questions');
            $fields[] = self::field("faq_{$number}_answer", "Answer {$number}", $answer, 'textarea', 'Frequently asked questions');
        }
        array_push($fields,
            self::field('cta_kicker', 'CTA eyebrow', 'Free counselling', 'text', 'Conversion CTA'),
            self::field('cta_title', 'CTA title', 'Ready to build your '.$name.' shortlist?', 'text', 'Conversion CTA'),
            self::field('cta_copy', 'CTA description', 'Bring your academic history, preferred course and budget. We’ll help you understand realistic university, intake and visa options.', 'textarea', 'Conversion CTA'),
            self::field('cta_label', 'CTA button label', 'Speak to a Counsellor', 'text', 'Conversion CTA'),
            self::field('contact_phone_label', 'Contact phone display', '+91 98266 66886', 'text', 'Conversion CTA'),
            self::field('contact_phone_link', 'Contact phone link', '+919826666886', 'text', 'Conversion CTA'),
            self::field('contact_email', 'Contact email', 'info@geic.in', 'text', 'Conversion CTA'),
            self::field('contact_address', 'Contact address', '503, THE VIEW Tower 1, Yeshwant Niwas Rd, Indore 452001', 'textarea', 'Conversion CTA'),
            self::field('form_disclaimer', 'Form disclaimer', 'University, visa and work-right rules can change; our counsellors will help you verify current official requirements.', 'textarea', 'Conversion CTA'),
        );

        return $fields;
    }

    private static function customPage(CmsPage $page): array
    {
        return self::page($page->page_key, $page->name, $page->description ?: 'Custom website page', [
            self::field('hero_title', 'Hero title', $page->name),
            self::field('hero_copy', 'Hero description', $page->description ?: 'Add a clear introduction for this page.', 'textarea'),
            self::field('hero_image', 'Hero image URL', 'assets/services/expert-counselling.jpg', 'image'),
            self::field('content_title', 'Content heading', 'How we can help', 'text', 'Main content'),
            self::field('content_copy', 'Content text', 'Add the key information visitors need to understand this page and confidently take their next step.', 'textarea', 'Main content'),
            self::field('cta_title', 'Call-to-action heading', 'Ready to take the next step?', 'text', 'Call to action'),
            self::field('cta_copy', 'Call-to-action text', 'Speak with our Indore counselling team for clear, personal guidance.', 'textarea', 'Call to action'),
            self::field('cta_label', 'Button label', 'Speak to a Counsellor', 'text', 'Call to action'),
        ]);
    }

    private static function field(string $key, string $label, string $default, string $type = 'text', string $section = 'Hero'): array
    {
        return compact('key', 'label', 'default', 'type', 'section');
    }
}
