<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Projects Laravel content into the complete imported mobile screen contract. */
class MobileStudioCatalog
{
    private const UNKNOWN = 'Not provided';

    public static function all(Request $request): array
    {
        $home = self::page('home');
        $about = self::page('pages.about');
        $destinations = array_map(fn (string $slug): array => DestinationCatalog::find($slug), DestinationCatalog::slugs());
        $services = ServiceCatalog::all();
        $events = EventCatalog::all();
        $upcoming = collect($events)->first(fn (array $event): bool => $event['status'] !== 'Past event' && strtotime($event['date']) !== false && strtotime($event['date']) >= strtotime('today'));
        $universities = [];
        $partners = [];
        $roadmaps = [];
        $mobileDestinations = [];
        $asset = fn (string $path): string => preg_match('#^https?://#', $path) ? $path : '/'.ltrim($path, '/');

        foreach ($destinations as $destination) {
            $code = strtoupper(pathinfo($destination['flag'] ?? '', PATHINFO_FILENAME));
            $flag = strlen($code) === 2 ? mb_chr(127397 + ord($code[0])).mb_chr(127397 + ord($code[1])) : '';
            $cost = fn (string $pattern): string => collect($destination['costs'] ?? [])->first(fn (array $row): bool => (bool) preg_match($pattern, $row[0]))[1] ?? self::UNKNOWN;
            $intakes = array_column($destination['intakes'] ?? [], 0);
            $mobileDestinations[] = [
                'id' => $destination['slug'], 'name' => $destination['name'], 'code' => $code,
                'tagline' => $destination['tagline'], 'description' => $destination['overview'] ?? $destination['tagline'],
                'highlights' => array_column($destination['benefits'] ?? [], 0),
                'flagEmoji' => $flag, 'flagUrl' => $asset('assets/transglobe/destinations/flags/'.($destination['flag'] ?? '')),
                'imageUrl' => $asset($destination['card']), 'popularCourses' => array_column($destination['courses'] ?? [], 0),
                'avgTuitionYear' => $cost('/tuition|universities/i'), 'livingExpensesYear' => $cost('/living/i'),
                'postStudyWorkVisa' => collect($destination['stats'] ?? [])->first(fn (array $stat): bool => (bool) preg_match('/post.study|PGWP|graduate route/i', $stat[1]))[0] ?? self::UNKNOWN, 'prPathway' => self::UNKNOWN,
                'intakes' => $intakes, 'topUniversities' => array_column($destination['universities'], 'name'),
                'languageRequirements' => collect($destination['requirements'])->first(fn (string $item): bool => (bool) preg_match('/English|language/i', $item)) ?? self::UNKNOWN,
            ];
            foreach ($destination['universities'] as $university) {
                $id = $destination['slug'].'-'.Str::slug($university['name']);
                $universities[] = [
                    'id' => $id, 'name' => $university['name'], 'shortName' => $university['name'],
                    'country' => $destination['name'], 'countryCode' => $code, 'flagEmoji' => $flag,
                    'city' => self::UNKNOWN, 'globalRank' => self::UNKNOWN, 'globalRankNumber' => null,
                    'acceptanceRate' => self::UNKNOWN, 'acceptanceRatePercentage' => null,
                    'annualTuition' => self::UNKNOWN, 'annualTuitionINR' => self::UNKNOWN,
                    'annualLivingCost' => self::UNKNOWN, 'annualLivingCostINR' => self::UNKNOWN,
                    'ieltsRequirement' => self::UNKNOWN, 'pteRequirement' => self::UNKNOWN,
                    'toeflRequirement' => self::UNKNOWN, 'greGmatRequirement' => self::UNKNOWN,
                    'minGpaPercentage' => self::UNKNOWN, 'popularPrograms' => [], 'intakes' => [],
                    'postStudyWorkVisa' => self::UNKNOWN, 'scholarships' => self::UNKNOWN,
                    'maxScholarshipAmount' => self::UNKNOWN, 'type' => self::UNKNOWN,
                    'logoUrl' => $asset($university['logo']), 'campusImage' => $asset($destination['card']),
                    'transGlobeHighlights' => 'Listed in the GEIC '.$destination['name'].' catalogue. Confirm course-specific requirements with the institution.',
                ];
                $partners[] = [
                    'id' => $id, 'name' => $university['name'], 'country' => $destination['name'],
                    'location' => self::UNKNOWN, 'logoUrl' => $asset($university['logo']),
                    'heroImage' => $asset($destination['card']), 'globalRank' => self::UNKNOWN,
                    'programsOffered' => [], 'popularDegrees' => [], 'tuitionRange' => self::UNKNOWN,
                    'intakes' => [], 'ieltsMin' => self::UNKNOWN, 'greGmatRequired' => null,
                    'scholarshipsAvailable' => self::UNKNOWN, 'description' => $university['name'].' - '.$destination['name'],
                ];
            }
            $roadmaps[$destination['name']] = [
                'country' => $destination['name'], 'flag' => $flag, 'visaName' => $destination['visa_title'] ?? self::UNKNOWN,
                'processingTime' => self::UNKNOWN, 'transGlobeSuccessRate' => self::UNKNOWN,
                'fundsRequirement' => $cost('/blocked|living/i'), 'workRights' => self::UNKNOWN,
                'overview' => $destination['visa_copy'] ?? self::UNKNOWN,
                'requiredDocumentsChecklist' => array_map(fn (string $item, int $index): array => [
                    'id' => $destination['slug'].'-document-'.$index, 'name' => $item,
                    'category' => 'Consular & Legal', 'isMandatory' => false,
                    'notes' => 'Confirm applicability for your course and destination with your counsellor.',
                ], $destination['requirements'], array_keys($destination['requirements'])),
                'steps' => array_map(fn (array $step, int $index): array => [
                    'stepNumber' => $index + 1, 'title' => $step[1], 'shortDesc' => $step[0],
                    'duration' => self::UNKNOWN, 'details' => $step[2], 'requiredDocuments' => [],
                    'indoreCounsellorTip' => 'Confirm the requirements for your own application.',
                ], $destination['journey'], array_keys($destination['journey'])),
            ];
        }

        $mobileEvents = array_map(fn (array $event): array => [
            'id' => $event['slug'], 'title' => $event['title'], 'subtitle' => $event['summary'],
            'date' => $event['date'], 'time' => $event['time'], 'venue' => $event['location'], 'city' => 'Indore',
            'badge' => $event['status'], 'description' => $event['overview'], 'highlights' => $event['highlights'],
            'bannerUrl' => $asset($event['image']), 'category' => 'Summit',
            'freeEntry' => null, 'registrationOpen' => $event['status'] !== 'Past event' && strtotime($event['date']) >= strtotime('today'),
            'agenda' => [], 'registrationPerks' => [], 'targetAudience' => self::UNKNOWN,
            'faqs' => self::faqs($event['faqs']),
        ], $events);

        $posts = BlogPost::query()->where('status', 'published')->whereNotNull('published_at')
            ->where('published_at', '<=', now())->orderByDesc('published_at')->orderByDesc('id')->get();
        $stats = [];
        foreach (['students', 'universities', 'visas', 'years'] as $key) {
            $stats[] = ['label' => $about['proof_'.$key.'_label'] ?? self::UNKNOWN,
                'value' => $about['proof_'.$key.'_value'] ?? self::UNKNOWN, 'highlight' => 'GEIC published catalogue'];
        }

        return [
            'schemaVersion' => 1, 'source' => 'laravel', 'loadedAt' => now()->toIso8601String(),
            'GEIC_BRAND' => [
                'name' => 'Trans Globe Indore', 'managedBy' => 'Managed by GEIC', 'shortName' => 'GEIC Global',
                'tagline' => $home['hero_title'] ?? 'Study abroad with GEIC',
                'establishedYear' => null, 'yearsOfExcellence' => $about['proof_years_value'] ?? self::UNKNOWN,
                'phone' => $home['contact_phone'] ?? '', 'displayPhone' => $home['contact_phone'] ?? '',
                'email' => $home['contact_email'] ?? '', 'website' => 'https://geic.in',
                'address' => $home['contact_address'] ?? self::UNKNOWN, 'shortAddress' => $home['contact_address'] ?? self::UNKNOWN,
                'landmark' => self::UNKNOWN, 'headquarters' => 'Indore', 'googleRating' => null,
                'googleReviewCount' => null, 'officesCount' => null, 'networkCities' => self::UNKNOWN,
                'officeHours' => self::UNKNOWN, 'logoUrl' => $asset('assets/transglobe/trans-globe-logo.png'),
            ],
            'GEIC_STATS' => $stats, 'STUDY_DESTINATIONS' => $mobileDestinations,
            'GLOBAL_UNIVERSITIES' => $universities, 'UNIVERSITY_PARTNERS' => $partners,
            'GEIC_SERVICES' => array_map(fn (array $service): array => [
                'id' => $service['slug'], 'stepNumber' => $service['number'], 'title' => $service['title'],
                'category' => match ($service['slug']) {
                    'test-preparation' => 'Test Preparation', 'visa-assistance' => 'Visas',
                    'scholarship-guidance', 'loans-financial-guide' => 'Finance & Grants',
                    'pre-post-departure', 'accommodation-assistance' => 'Departure & Housing', default => 'Admissions',
                },
                'tagline' => $service['eyebrow'], 'shortDesc' => $service['summary'], 'fullDesc' => $service['overview'],
                'iconName' => 'GraduationCap', 'bannerImage' => $asset($service['image']), 'keyBenefits' => $service['results'],
                'detailedProcess' => array_map(fn (array $step, int $i): array => ['step' => (string) ($i + 1), 'title' => $step[0], 'description' => $step[1]], $service['process'], array_keys($service['process'])),
                'deliverables' => $service['results'], 'whoNeedsThis' => self::UNKNOWN, 'popularDestinations' => [],
                'faqs' => self::faqs($service['faqs']),
            ], $services),
            'ENGLISH_TESTS' => array_map(fn (array $test): array => [
                'id' => $test['slug'], 'name' => $test['title'], 'code' => strtoupper($test['slug']),
                'category' => match ($test['slug']) {
                    'gre', 'gmat' => 'Graduate Aptitude', 'sat' => 'Undergraduate', default => 'English Proficiency',
                },
                'tagline' => $test['eyebrow'], 'acceptedBy' => self::UNKNOWN, 'testFormat' => $test['summary'],
                'duration' => collect($test['facts'])->first(fn (array $fact): bool => (bool) preg_match('/time|duration/i', $fact[1]))[0] ?? self::UNKNOWN,
                'scoringScale' => collect($test['facts'])->first(fn (array $fact): bool => (bool) preg_match('/score/i', $fact[1]))[0] ?? self::UNKNOWN,
                'minTargetBand' => self::UNKNOWN, 'transGlobeHighlights' => array_column($test['support'], 1),
                'batchTimings' => self::UNKNOWN, 'courseFeeINR' => self::UNKNOWN, 'testCenterIndore' => self::UNKNOWN,
                'bannerImage' => $asset($test['image']), 'overview' => $test['overview'],
                'modules' => array_map(fn (array $module): array => ['title' => $module[0], 'duration' => self::UNKNOWN, 'description' => $module[1], 'scoringTips' => self::UNKNOWN], $test['modules']),
                'preparationTips' => array_column($test['support'], 1), 'faqs' => self::faqs($test['faqs']),
            ], TestPrepCatalog::all()),
            'UPCOMING_EVENTS_LIST' => $mobileEvents,
            'UPCOMING_EXPO' => [
                'id' => $upcoming['slug'] ?? '', 'title' => $upcoming['title'] ?? 'No upcoming event announced',
                'subtitle' => $upcoming['summary'] ?? 'Contact our counsellors for the next event.',
                'date' => $upcoming['date'] ?? 'To be announced', 'time' => $upcoming['time'] ?? self::UNKNOWN,
                'location' => $upcoming['location'] ?? 'To be announced', 'city' => 'Indore',
                'venue' => $upcoming['location'] ?? 'To be announced', 'highlights' => $upcoming['highlights'] ?? [],
                'participatingUniversities' => null, 'freeEntry' => null,
                'bannerImage' => isset($upcoming['image']) ? $asset($upcoming['image']) : '', 'registrationOpen' => $upcoming !== null,
            ],
            'HERO_SLIDES' => array_map(fn (array $destination): array => [
                'id' => $destination['id'], 'badge' => 'Study destination', 'statBadge' => 'GEIC guide',
                'title' => 'Study in', 'titleHighlight' => $destination['name'], 'description' => $destination['tagline'],
                'imageUrl' => $destination['imageUrl'], 'primaryCta' => 'Free 1-on-1 session', 'primaryAction' => 'booking',
                'secondaryCta' => 'Explore', 'secondaryAction' => 'explore',
            ], array_slice($mobileDestinations, 0, 4)),
            'GEIC_BLOGS' => $posts->map(fn (BlogPost $post): array => [
                'id' => $post->slug, 'title' => $post->title, 'slug' => $post->slug, 'category' => $post->category,
                'coverImage' => $asset($post->image ?? ''), 'readTime' => $post->read_time,
                'publishedDate' => $post->published_at->format('j F Y'),
                'author' => ['name' => $post->author, 'role' => '', 'avatar' => ''],
                'excerpt' => $post->excerpt, 'tags' => $post->tags ?? [],
                'content' => array_merge([$post->intro], array_map(fn (array $section): string => ($section['title'] ?? '')."\n".($section['copy'] ?? ''), $post->sections ?? [])),
                'keyTakeaways' => [],
            ])->all(),
            'STUDENT_REVIEWS' => [],
            'NOTIFICATIONS' => array_values(array_map(fn (array $event): array => [
                'id' => $event['id'], 'title' => $event['title'], 'category' => 'expo',
                'categoryLabel' => 'Upcoming event', 'urgency' => 'info', 'date' => $event['date'],
                'summary' => $event['description'], 'actionText' => 'View events', 'actionType' => 'expo', 'isRead' => false,
            ], array_filter($mobileEvents, fn (array $event): bool => $event['registrationOpen']))),
            'FOUR_STEPS' => array_map(fn (string $word, int $index): array => [
                'step' => (string) ($index + 1), 'title' => $about['process_'.$word.'_title'] ?? self::UNKNOWN,
                'desc' => $about['process_'.$word.'_copy'] ?? self::UNKNOWN, 'icon' => ['UserCheck', 'MessageSquare', 'Send', 'Plane'][$index],
            ], ['one', 'two', 'three', 'four'], [0, 1, 2, 3]),
            'FAQS' => array_map(fn (string $word): array => [
                'question' => $home['faq_'.$word.'_question'] ?? self::UNKNOWN,
                'answer' => $home['faq_'.$word.'_answer'] ?? self::UNKNOWN,
            ], ['one', 'two', 'three', 'four', 'five']),
            'ABOUT_INFO' => [
                'headline' => $about['hero_title'] ?? 'About GEIC', 'foundedYear' => self::UNKNOWN,
                'placedCount' => $about['proof_students_value'] ?? self::UNKNOWN,
                'universitiesCount' => $about['proof_universities_value'] ?? self::UNKNOWN,
                'visaRate' => $about['proof_visas_value'] ?? self::UNKNOWN, 'scholarshipsTotal' => self::UNKNOWN,
                'story' => ($about['story_copy'] ?? '')."\n\n".($about['story_copy_2'] ?? ''),
                'accreditations' => [], 'pillars' => array_map(fn (array $service): array => ['title' => $service['title'], 'desc' => $service['summary']], array_slice($services, 0, 4)),
            ],
            'VISA_ROADMAP_DATA' => $roadmaps,
        ];
    }

    private static function page(string $key): array
    {
        $defaults = collect(CmsPageCatalog::find($key)['fields'] ?? [])->pluck('default', 'key')->all();

        return array_replace($defaults, SiteContent::publicValuesForPage($key));
    }

    private static function faqs(array $faqs): array
    {
        return array_map(fn (array $faq): array => ['question' => $faq[0], 'answer' => $faq[1]], $faqs);
    }
}
