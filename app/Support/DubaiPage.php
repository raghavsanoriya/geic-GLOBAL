<?php

namespace App\Support;

use App\Models\SiteContent;
use Illuminate\Support\Str;

/**
 * Content model for the Study in Dubai landing page at /dubai.
 *
 * Every visible text, image and list on the page is a CMS field, so editors
 * manage it from Website content. Repeating content (journey steps, reviews,
 * FAQs…) is stored one item per line with `|` between columns, which lets
 * editors add or remove items without a code change. The `sections` field
 * controls which sections appear and in what order.
 */
class DubaiPage
{
    public const PAGE_KEY = 'dubai';

    public const PUBLIC_PATH = '/dubai';

    public const TRACKING_KEY = 'integration.dubai_tracking';

    /** Matches the existing /destinations/dubai enquiries so filters and CRM country stay unified. */
    public const DESTINATION = 'Dubai & UAE';

    /** Approximate yearly budget choices on the enquiry form (tuition + living). */
    public const BUDGETS = [
        '₹6–10 Lakhs – Budget-Friendly Universities',
        '₹10–15 Lakhs – Value-for-Money Universities',
        '₹15–25 Lakhs – Preferred University Options',
        '₹25–35 Lakhs – Premium & High-Ranking Universities',
    ];

    /** Editors type this alone in a field to hide that item (blank fields fall back to defaults). */
    public const HIDE_TOKEN = '-';

    /** Sections the page knows how to render, in their default order. */
    public const SECTIONS = [
        'hero', 'credentials', 'why', 'internships', 'journey', 'industries', 'costs',
        'photos', 'consultation', 'reviews', 'requirements', 'intakes', 'faq', 'contact',
    ];

    /** @var array<string, string> */
    public const TRACKING_DEFAULTS = [
        'enabled' => '0',
        'meta_pixel_id' => '',
        'google_ads_id' => '',
        'google_ads_conversion_label' => '',
    ];

    /**
     * CMS field definitions consumed by CmsPageCatalog.
     *
     * @return array<int, array{key: string, label: string, default: string, type: string, section: string}>
     */
    public static function fields(): array
    {
        $unsplash = fn (string $id, int $w, int $h): string => "https://images.unsplash.com/{$id}?auto=format&fit=crop&crop=entropy&w={$w}&h={$h}&q=80";

        return [
            self::field('meta_title', 'Browser title', 'Study in Dubai | Trans Globe Indore – GEIC', 'text', 'Page identity'),
            self::field('meta_description', 'Search description', 'Study at reputed universities in Dubai with Trans Globe Indore, managed by GEIC. Free counselling, admissions, scholarships, internships and UAE student visa support since 1992.', 'textarea', 'Page identity'),
            self::field('sections', 'Sections shown, in order (one per line; delete a line to hide that section)', implode("\n", self::SECTIONS), 'textarea', 'Page layout'),

            self::field('highlights', 'Top scrolling bar (one item per line)', implode("\n", ['Study in Dubai', 'Internship Opportunities', 'Scholarships Available', 'UAE Student Visa Support', 'Free Counselling', 'Since 1992', '98.7% Visa Success Rate']), 'textarea', 'Header & contact'),
            self::field('header_cta', 'Header button label', 'Free Consultation', 'text', 'Header & contact'),
            self::field('whatsapp_number', 'WhatsApp number (country code and number, digits only)', '919826666886', 'text', 'Header & contact'),
            self::field('whatsapp_message', 'WhatsApp pre-filled message', 'I want to book a consultation for Dubai', 'text', 'Header & contact'),
            self::field('phone', 'Phone number', '+91 98266 66886', 'text', 'Header & contact'),
            self::field('email', 'Email address', 'info@geic.in', 'text', 'Header & contact'),
            self::field('address', 'Office address', 'Office No. 503, THE VIEW Tower 1, Yeshwant Niwas Rd, above Jade Blue Showroom, Nehru Park 2, Lad Colony, Indore, Madhya Pradesh 452001', 'textarea', 'Header & contact'),
            self::field('office_hours', 'Office hours', 'Mon to Sat: 10:00 AM – 6:30 PM', 'text', 'Header & contact'),

            self::field('hero_title', 'Hero title', 'Study In Dubai With', 'text', 'Hero'),
            self::field('hero_title_accent', 'Hero highlighted title', 'Trans Globe', 'text', 'Hero'),
            self::field('hero_copy', 'Hero description', 'Dubai is more than just a degree, it\'s a step towards your future. Study at reputed universities, explore internship opportunities and gain practical industry experience while pursuing your degree.', 'textarea', 'Hero'),
            self::field('hero_cta', 'Hero button label', 'Book Free Counselling', 'text', 'Hero'),
            self::field('hero_image', 'Hero image', 'assets/dubai-landing/img/hero-2.png', 'image', 'Hero'),
            self::field('hero_counter_prefix', 'Hero stat label (above)', 'More than', 'text', 'Hero'),
            self::field('hero_counter_value', 'Hero stat number', '70,250+', 'text', 'Hero'),
            self::field('hero_counter_label', 'Hero stat label (below)', 'Students Placed', 'text', 'Hero'),

            self::field('credentials', 'Credential cards (one per line: icon | title | text)', implode("\n", [
                'award | Since 1992 | Over three decades of guiding Indian students abroad.',
                'user-graduate | 70,250+ Students | Placed at universities in more than 10 countries.',
                'university | 800+ Universities | Direct partner tie-ups, including leading Dubai campuses.',
                'passport | 98.7% Visa Success | Detailed document checks and interview preparation.',
                'map-marker-alt | 16+ Branches | Offices across India and Nepal, plus online counselling.',
            ]), 'textarea', 'Credentials'),

            self::field('why_eyebrow', 'Why Dubai eyebrow', 'Why Dubai', 'text', 'Why Dubai'),
            self::field('why_title', 'Why Dubai title', 'A Global Education Hub, Close To Home', 'text', 'Why Dubai'),
            self::field('why_copy', 'Why Dubai description', 'Dubai hosts branch campuses of UK, Australian and European universities. It is safe, multicultural, business-focused and a few hours\' flight from India. Aap Dubai ki reputed universities mein study kar sakte hain, aur apni degree ke saath practical industry experience bhi gain kar sakte hain.', 'textarea', 'Why Dubai'),
            self::field('why_cta', 'Why Dubai button label', 'Book Free Counselling', 'text', 'Why Dubai'),
            self::field('why_items', 'Why Dubai tiles (one per line: icon | title | text)', implode("\n", [
                'graduation-cap | Globally Recognised Degrees | UK, Australian, European and UAE qualifications to choose from.',
                'wallet | Affordable Education | Tuition and living costs generally lower than the USA, UK and Australia.',
                'passport | Simple Visa Process | Guided UAE student visa process with support at each step.',
                'plane | Close To Home | Only a few hours\' flight from India, so family is never far.',
                'shield-check | Safe & Multicultural | One of the safest cities in the world, with students from across the globe.',
                'briefcase | Work While You Study | Master\'s students may work full-time in evening-class schedules; bachelor\'s part-time, subject to rules.',
                'city | 11.57 Million People | A fast-growing, cosmopolitan city and business hub.',
                'users | 2,30,000+ Students | International students already study in the UAE.',
                'sun | 18°C – 42°C | Warm and sunny all year round.',
            ]), 'textarea', 'Why Dubai'),

            self::field('internships_eyebrow', 'Internships eyebrow', 'Internships & Scholarships', 'text', 'Internships'),
            self::field('internships_title', 'Internships title', 'Graduate With A Degree And Real Experience', 'text', 'Internships'),
            self::field('internships_copy', 'Internships description', 'Explore university admission along with additional scholarship opportunities through our direct university connections. We also facilitate internship opportunities for students going to Dubai, so you add industry experience to your CV before you graduate.', 'textarea', 'Internships'),
            self::field('internships_note', 'Internships note', 'Graduation ke time aapke paas sirf ek degree nahi, balki practical experience bhi hoga.', 'textarea', 'Internships'),
            self::field('internships_counter_value', 'Internships stat number', '2,000+', 'text', 'Internships'),
            self::field('internships_counter_label', 'Internships stat label', 'Scholarship awards a year', 'text', 'Internships'),
            self::field('internships_counter_text', 'Internships stat description', 'Trans Globe students receive scholarships and bursaries every year.', 'text', 'Internships'),
            self::field('internships_points', 'Internships checklist (one per line)', "Reputed universities\nScholarship opportunities\nInternship experience\nCareer advantage", 'textarea', 'Internships'),
            self::field('internships_cta', 'Internships button label', 'DM Us Today', 'text', 'Internships'),
            self::field('internships_image_main', 'Internships main image', $unsplash('photo-1518684079-3c830dcef090', 640, 900), 'image', 'Internships'),
            self::field('internships_image_side', 'Internships side image', $unsplash('photo-1634007626524-f47fa37810a7', 640, 720), 'image', 'Internships'),
            self::field('internships_image_small', 'Internships small image', $unsplash('photo-1628859017536-c2f1d69f3c84', 400, 400), 'image', 'Internships'),

            self::field('journey_eyebrow', 'Journey eyebrow', 'Your Journey', 'text', 'Journey'),
            self::field('journey_title', 'Journey title', 'From First Call To Landing In Dubai', 'text', 'Journey'),
            self::field('journey_steps', 'Journey steps (one per line: tab | card label | title | description | image URL)', implode("\n", [
                'Admission | Counselling | Free Counselling | Profile evaluation, career guidance and planning. | '.$unsplash('photo-1752650732081-8f61e81813ed', 640, 440),
                'Admission | Apply | Shortlist And Apply | Choose universities and programs, prepare documents and apply. | '.$unsplash('photo-1515378960530-7c0da6231fb1', 640, 440),
                'Admission | Offer Letter | Receive Your Offer Letter | Review admission details, scholarship and fee structure. | '.$unsplash('photo-1648994605501-fe0a391d2653', 640, 440),
                'Admission | Enrolment | Accept And Pay Tuition | Initial payment and enrolment requirements. | '.$unsplash('photo-1563013544-824ae1b704d3', 640, 440),
                'Visa | Visa Filing | Student Visa Application | Assisted preparation and submission of your visa file. | '.$unsplash('photo-1454496406107-dc34337da8d6', 640, 440),
                'Visa | Approval | Visa Approval | Receive the UAE student visa and complete formalities. | '.$unsplash('photo-1581553673739-c4906b5d0de8', 640, 440),
                'Arrival | Pre-Departure | Accommodation & Pre-Departure | Housing, airport pickup, travel and a pre-departure briefing. | '.$unsplash('photo-1504150558240-0b4fd8946624', 640, 440),
                'Arrival | Fly To Dubai | Fly To Dubai | Begin your studies with ongoing support from our team. | '.$unsplash('photo-1650137978636-5c6abecd702d', 640, 440),
            ]), 'textarea', 'Journey'),
            self::field('visa_steps', 'Student visa cards, shown in the Visa tab (one per line: card label | title | description | image URL)', implode("\n", [
                'Offer | Valid Admission Offer | An unconditional offer is needed before the visa process starts. | '.$unsplash('photo-1562564055-71e051d33c19', 640, 440),
                'Tuition | Pay Tuition | The fee receipt is required for the visa. | '.$unsplash('photo-1733503747506-773e56e4078f', 640, 440),
                'Documents | Prepare Documents | Passport, digital photos, visa forms and any university requirements. | '.$unsplash('photo-1553697388-94e804e2f0f6', 640, 440),
                'E-Visa | Entry Permit (E-Visa) | Issued once requirements are met; the residence visa is completed after arrival. | '.$unsplash('photo-1655722725332-9925c96dd627', 640, 440),
            ]), 'textarea', 'Journey'),
            self::field('journey_note', 'Journey footnote', 'Visa processing typically takes around 3 to 4 weeks after the offer letter and documents are submitted.', 'text', 'Journey'),

            self::field('industries', 'Industries scrolling bar (one per line)', implode("\n", ['Business & Management', 'Finance & Banking', 'IT & AI', 'Engineering', 'Aviation & Aerospace', 'Hospitality & Tourism', 'Logistics & Supply Chain', 'Media & Digital Marketing']), 'textarea', 'Industries'),

            self::field('costs_eyebrow', 'Costs eyebrow', 'Financial Planning', 'text', 'Costs'),
            self::field('costs_title', 'Costs title', 'Estimated Annual Costs Of Studying In Dubai', 'text', 'Costs'),
            self::field('costs_copy', 'Costs description', 'Cost of living is around AED 35K – 60K a year. Merit-based scholarships and early-application discounts may apply.', 'textarea', 'Costs'),
            self::field('costs_items', 'Cost bars in AED thousands (one per line: label | minimum | maximum | note)', implode("\n", [
                'Undergraduate tuition | 42 | 60 | Per year, by university and program',
                'Postgraduate tuition | 45 | 80 | Per year for master\'s programs',
                'Living & accommodation | 35 | 50 | Per year for housing and daily living',
            ]), 'textarea', 'Costs'),
            self::field('costs_extras', 'Other costs (one per line: item | amount)', "Airfare from India (one-way, indicative) | AED 800 – 2,000\nStudent visa fees, varies by university | AED 4,000 – 8,000 / yr", 'textarea', 'Costs'),
            self::field('costs_total', 'Budget summary', 'Typical yearly budget (tuition + living): undergraduate AED 77K – 110K, postgraduate AED 80K – 130K, before scholarships.', 'textarea', 'Costs'),
            self::field('costs_note', 'Costs footnote', 'Estimates only; actual fees vary by university and program.', 'text', 'Costs'),

            self::field('photos', 'Photo strip (one per line: image URL | caption)', implode("\n", [
                $unsplash('photo-1518684079-3c830dcef090', 520, 680).' | Burj Al Arab',
                $unsplash('photo-1510665724063-f77a01074aa2', 520, 680).' | Burj Khalifa',
                $unsplash('photo-1628859017536-c2f1d69f3c84', 520, 680).' | Dubai Frame',
                $unsplash('photo-1786991810391-e28ee367c3aa', 520, 680).' | Palm Jumeirah',
                $unsplash('photo-1528702748617-c64d49f918af', 520, 680).' | Desert coast',
                $unsplash('photo-1772090042075-6336af674f01', 520, 680).' | Jumeirah',
                $unsplash('photo-1772090049995-6116febe0d60', 520, 680).' | Dubai waterfront',
                $unsplash('photo-1543579596-2c11997c7706', 520, 680).' | Downtown Dubai',
            ]), 'textarea', 'Photo strip'),

            self::field('consultation_eyebrow', 'Consultation banner eyebrow', 'Free, No-Pressure Guidance', 'text', 'Consultation banner'),
            self::field('consultation_title', 'Consultation banner title', 'DM Us Today, And Let\'s Plan Your Journey To Dubai!', 'text', 'Consultation banner'),
            self::field('consultation_button', 'Consultation banner button label', 'Book on WhatsApp', 'text', 'Consultation banner'),
            self::field('consultation_points', 'Consultation banner checklist (one per line)', "Free profile evaluation\nUniversity shortlisting\nScholarship & internship guidance", 'textarea', 'Consultation banner'),

            self::field('reviews_eyebrow', 'Reviews eyebrow', 'Student Reviews', 'text', 'Reviews'),
            self::field('reviews_title', 'Reviews title', 'Real Students. Real Google Reviews.', 'text', 'Reviews'),
            self::field('reviews_rating', 'Google rating', '4.8', 'text', 'Reviews'),
            self::field('reviews_count', 'Number of Google reviews', '495', 'text', 'Reviews'),
            self::field('reviews', 'Google reviews (one per line: name | review text)', implode("\n", [
                'Arun Rawat | I had a great experience taking classes at TransGlobe. The teachers were knowledgeable, supportive, and always willing to help. The teaching methods were clear and practical, which made learning easy and enjoyable. The staff was friendly, and the overall environment was positive and motivating. I truly appreciate the guidance and support.',
                'anuj joshi | Excellent service and a very supportive team throughout my journey. They provided clear guidance for university admissions, documentation, and the visa process. They were always available to answer my questions and made the entire process smooth and stress-free. Their professionalism and genuine support gave me confidence at every step. Thank you for everything!!',
                'Gurshan Singh | had an incredible experience with Transglobe indore while preparing to move abroad. From our very first consultation, their team demonstrated deep expertise, complete transparency, and a genuine commitment to my goals.',
                'Siddhi Janve | A professional and supportive study abroad consultancy. The team offers clear guidance and takes time to explain each step of the process, making it easier to plan applications with confidence. Their transparent and student-focused approach makes them a reliable choice.',
                'Mustafa Nalwala | Trans globe managed by GEIC, Indore exceeded my expectations! Helpful and attentive counselling team made the whole process seamless. Quick responses and hassle-free experience. Highly recommend for anyone looking abroad!!!....',
                'Kundan Sharma | I had a really good experience with Trans Globe .The team is very supportive and gives genuine guidance at every step. From university selection to application processing, financial guidance, visa processing, and even pre-departure counselling, everything was handled smoothly and professionally. Thank you very much.',
            ]), 'textarea', 'Reviews'),

            self::field('requirements_title', 'Requirements title', 'What You Need To Apply', 'text', 'Requirements'),
            self::field('requirements_copy', 'Requirements description', 'Keep these documents ready and our team will check every one before your application goes to the university.', 'textarea', 'Requirements'),
            self::field('requirements', 'Requirement tiles (one per line: icon | title | text)', implode("\n", [
                'passport | Valid Passport | Sufficient validity for admission and visa processing.',
                'file-alt | Academic Transcripts | Grade 10, Grade 12, diploma if applicable, and bachelor\'s for postgraduates.',
                'language | English Proficiency | IELTS, PTE, TOEFL or other accepted tests. Some accept an MOI certificate.',
                'pen-nib | Statement Of Purpose | Your background, goals and reasons for the program and university.',
                'id-card | Resume / CV | Generally required for postgraduate programs.',
                'folder-open | Supporting Documents | Work experience, recommendation letters or a portfolio for design programs.',
            ]), 'textarea', 'Requirements'),

            self::field('intakes_eyebrow', 'Intakes eyebrow', 'Intakes', 'text', 'Intakes'),
            self::field('intakes_title', 'Intakes title', 'When Can You Start?', 'text', 'Intakes'),
            self::field('intakes_cta', 'Intakes button label', 'Plan My Intake', 'text', 'Intakes'),
            self::field('intakes', 'Intake cards (one per line: month | tag | text | image URL)', implode("\n", [
                'September | Main intake | The widest range of courses across Dubai universities. | '.$unsplash('photo-1634007626524-f47fa37810a7', 720, 480),
                'January | Mid-year intake | Most courses are available for a mid-year start. | '.$unsplash('photo-1628859017536-c2f1d69f3c84', 720, 480),
                'May | Limited intake | Available for selected programs only. | '.$unsplash('photo-1786991810391-e28ee367c3aa', 720, 480),
            ]), 'textarea', 'Intakes'),

            self::field('faq_eyebrow', 'FAQ eyebrow', 'FAQs', 'text', 'FAQs'),
            self::field('faq_title', 'FAQ title', 'Common Questions', 'text', 'FAQs'),
            self::field('faqs', 'Questions (one per line: question | answer)', implode("\n", [
                'Is Dubai good for international students? | Yes. It is a fast-growing education hub with strong infrastructure and career prospects.',
                'Can I work while studying? | Yes, subject to visa rules and university policies. Undergraduates may need university approval; postgraduates generally have more flexibility.',
                'Is IELTS always required? | No. Some universities accept a Medium of Instruction certificate or other qualifications.',
                'How long does the visa take? | About 3 to 4 weeks after the offer letter and documents are submitted.',
                'Are scholarships available? | Yes, including merit-based, early-application and fee discounts, varying by institution.',
                'Can I get an internship? | Trans Globe facilitates internship opportunities for students going to Dubai. Speak to a counsellor for current options.',
            ]), 'textarea', 'FAQs'),

            self::field('contact_eyebrow', 'Contact eyebrow', 'Talk To A Counsellor', 'text', 'Contact form'),
            self::field('contact_title', 'Contact title', 'Start Your Dubai Journey Today', 'text', 'Contact form'),
            self::field('contact_copy', 'Contact description', 'Share a few details and our Indore counsellor will call you back for a free profile evaluation and university shortlist.', 'textarea', 'Contact form'),
            self::field('form_title', 'Form title', 'Get Free Counselling', 'text', 'Contact form'),
            self::field('form_button', 'Form button label', 'Send My Details', 'text', 'Contact form'),

            self::field('footer_copy', 'Footer introduction', 'Trans Globe Indore, managed by GEIC. Your trusted partner for global education since 1992.', 'textarea', 'Footer'),
            self::field('footer_brand', 'Large footer wordmark', 'Trans Globe', 'text', 'Footer'),
        ];
    }

    /**
     * Published CMS values merged over the defaults, with list fields parsed.
     *
     * @return array<string, mixed>
     */
    public static function content(): array
    {
        $saved = SiteContent::publicValuesForPage(self::PAGE_KEY);
        $text = [];

        foreach (self::fields() as $field) {
            $value = $saved[$field['key']] ?? null;
            $text[$field['key']] = match (true) {
                is_string($value) && trim($value) === self::HIDE_TOKEN => '',
                is_string($value) && trim($value) !== '' => $value,
                default => $field['default'],
            };
        }

        $whatsappDigits = preg_replace('/\D+/', '', $text['whatsapp_number']) ?: '919826666886';
        $journeySteps = self::rows($text['journey_steps'], 5, fn (array $c): array => [
            'tab' => $c[0], 'tabKey' => Str::slug($c[0]) ?: 'journey', 'short' => $c[1], 'title' => $c[2], 'text' => $c[3], 'image' => self::imageUrl($c[4]),
        ]);
        $visaSteps = self::rows($text['visa_steps'], 4, fn (array $c): array => [
            'tab' => 'Visa', 'tabKey' => 'visa', 'short' => $c[0], 'title' => $c[1], 'text' => $c[2], 'image' => self::imageUrl($c[3]),
        ]);
        $costs = self::rows($text['costs_items'], 4, fn (array $c): array => [
            'label' => $c[0], 'min' => max(0, (float) $c[1]), 'max' => max(0, (float) $c[2]), 'note' => $c[3],
        ]);

        return [
            ...$text,
            'sections' => self::sections($text['sections']),
            'whatsappUrl' => 'https://wa.me/'.$whatsappDigits.'?text='.rawurlencode($text['whatsapp_message']),
            'telUrl' => 'tel:'.(preg_replace('/[^\d+]/', '', $text['phone']) ?: '+919826666886'),
            'heroImage' => self::imageUrl($text['hero_image']),
            'internshipImages' => [
                'main' => self::imageUrl($text['internships_image_main']),
                'side' => self::imageUrl($text['internships_image_side']),
                'small' => self::imageUrl($text['internships_image_small']),
            ],
            'highlightList' => self::lines($text['highlights']),
            'industryList' => self::lines($text['industries']),
            'internshipPoints' => self::lines($text['internships_points']),
            'consultationPoints' => self::lines($text['consultation_points']),
            'credentialItems' => self::iconRows($text['credentials']),
            'whyItems' => self::iconRows($text['why_items']),
            'requirementItems' => self::iconRows($text['requirements']),
            'journeySteps' => $journeySteps,
            'visaSteps' => $visaSteps,
            'journeyTabs' => collect([...$journeySteps, ...$visaSteps])->pluck('tab', 'tabKey')->all(),
            'costItems' => $costs,
            'costScale' => self::costScale($costs),
            'costExtras' => self::rows($text['costs_extras'], 2, fn (array $c): array => ['label' => $c[0], 'value' => $c[1]]),
            'photoItems' => self::rows($text['photos'], 2, fn (array $c): array => ['image' => self::imageUrl($c[0]), 'caption' => $c[1]]),
            'reviewItems' => self::rows($text['reviews'], 2, fn (array $c): array => ['name' => $c[0], 'text' => $c[1]]),
            'intakeItems' => self::rows($text['intakes'], 4, fn (array $c): array => ['month' => $c[0], 'tag' => $c[1], 'text' => $c[2], 'image' => self::imageUrl($c[3])]),
            'faqItems' => self::rows($text['faqs'], 2, fn (array $c): array => ['question' => $c[0], 'answer' => $c[1]]),
            'logo' => asset('assets/transglobe/trans-globe-logo.png'),
            'logoWhite' => asset('assets/transglobe/trans-globe-logo-white.png'),
        ];
    }

    /** @return array<string, string> */
    public static function tracking(): array
    {
        return array_merge(self::TRACKING_DEFAULTS, SiteContent::valuesForPage(self::TRACKING_KEY));
    }

    /**
     * Valid section keys in the editor's order; unknown lines are ignored.
     *
     * @return list<string>
     */
    public static function sections(string $value): array
    {
        $sections = collect(self::lines($value))
            ->map(fn (string $line): string => Str::of($line)->lower()->squish()->toString())
            ->filter(fn (string $line): bool => in_array($line, self::SECTIONS, true))
            ->unique()
            ->values()
            ->all();

        return $sections === [] ? self::SECTIONS : $sections;
    }

    /** @return list<string> */
    public static function lines(string $value): array
    {
        return collect(preg_split('/\R/u', $value) ?: [])
            ->map(fn (string $line): string => trim($line))
            ->filter(fn (string $line): bool => $line !== '')
            ->values()
            ->all();
    }

    /**
     * Split each line on `|`, pad to the expected column count and map it.
     *
     * @param  callable(list<string>): array<string, mixed>  $map
     * @return list<array<string, mixed>>
     */
    public static function rows(string $value, int $columns, callable $map): array
    {
        $rows = [];

        foreach (self::lines($value) as $line) {
            $cells = array_map('trim', explode('|', $line, $columns));
            $cells = array_pad($cells, $columns, '');

            if ($cells[0] === '' && $cells[1] === '') {
                continue;
            }

            $rows[] = $map($cells);
        }

        return $rows;
    }

    /** @return list<array{icon: string, title: string, text: string}> */
    private static function iconRows(string $value): array
    {
        return self::rows($value, 3, fn (array $c): array => [
            'icon' => self::iconClass($c[0]),
            'title' => $c[1],
            'text' => $c[2],
        ]);
    }

    /** Accept `award`, `fa-award` or `fas fa-award` and return a safe Font Awesome class list. */
    public static function iconClass(string $value): string
    {
        $value = Str::of($value)->lower()->replaceMatches('/[^a-z0-9\- ]/', '')->squish()->toString();

        if ($value === '') {
            return 'far fa-check-circle';
        }

        if (preg_match('/\b(fas|far|fal|fab|fad)\b/', $value)) {
            return $value;
        }

        return 'far fa-'.Str::after($value, 'fa-');
    }

    /** Media Library paths become site URLs; absolute http(s) URLs pass through. */
    public static function imageUrl(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        if (preg_match('~^https?://~i', $value)) {
            return $value;
        }

        return asset(ltrim($value, '/'));
    }

    /** @param list<array{min: float, max: float}> $costs */
    private static function costScale(array $costs): float
    {
        // Editors may type the range either way round, so scale to the larger end.
        $highest = collect($costs)->map(fn (array $cost): float => max($cost['min'], $cost['max']))->max() ?: 80;

        return (float) (ceil($highest / 20) * 20);
    }

    /** @return array{key: string, label: string, default: string, type: string, section: string} */
    private static function field(string $key, string $label, string $default, string $type, string $section): array
    {
        return compact('key', 'label', 'default', 'type', 'section');
    }
}
