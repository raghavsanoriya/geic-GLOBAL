<?php

namespace App\Support;

use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

/**
 * Public blog read model.
 *
 * Published database posts are the source of truth. The original catalogue is
 * retained as a safe fallback for installs that have not run the migration.
 */
class BlogCatalog
{
    public static function find(string $slug): ?array
    {
        if (self::databaseAvailable()) {
            $record = BlogPost::query()
                ->where('slug', $slug)
                ->where('status', 'published')
                ->first();

            if ($record) {
                return self::fromModel($record);
            }
        }

        $post = self::posts()[$slug] ?? null;

        return $post ? array_merge(['slug' => $slug], $post) : null;
    }

    public static function all(): array
    {
        if (self::databaseAvailable()) {
            $records = BlogPost::query()
                ->where('status', 'published')
                ->orderByDesc('is_featured')
                ->orderByDesc('published_at')
                ->get();

            if ($records->isNotEmpty()) {
                return $records->map(fn (BlogPost $post): array => self::fromModel($post))->all();
            }
        }

        return array_map(fn (string $slug) => self::find($slug), array_keys(self::posts()));
    }

    public static function featured(int $limit = 5): array
    {
        return array_slice(self::all(), 0, $limit);
    }

    /** Seed the original catalogue once so existing content remains editable. */
    public static function seedDefaults(): void
    {
        if (! self::databaseAvailable() || BlogPost::query()->exists()) {
            return;
        }

        foreach (self::posts() as $slug => $post) {
            BlogPost::create([
                'slug' => $slug,
                'title' => $post['title'],
                'category' => $post['category'],
                'excerpt' => $post['excerpt'],
                'image' => $post['image'],
                'published_at' => Carbon::parse($post['date']),
                'read_time' => $post['read_time'],
                'author' => $post['author'],
                'intro' => $post['intro'],
                'sections' => $post['sections'],
                'tags' => $post['tags'],
                'status' => 'published',
                'is_featured' => in_array($slug, array_slice(array_keys(self::posts()), 0, 3), true),
            ]);
        }
    }

    private static function fromModel(BlogPost $post): array
    {
        return [
            'slug' => $post->slug,
            'title' => $post->title,
            'category' => $post->category,
            'excerpt' => $post->excerpt,
            'image' => $post->image,
            'date' => optional($post->published_at)->format('j F Y') ?? 'Draft',
            'read_time' => $post->read_time,
            'author' => $post->author,
            'intro' => $post->intro,
            'sections' => $post->sections ?: [],
            'tags' => $post->tags ?: [],
        ];
    }

    private static function databaseAvailable(): bool
    {
        try {
            return Schema::hasTable('blog_posts');
        } catch (QueryException) {
            return false;
        }
    }

    private static function posts(): array
    {
        return [
            'is-it-safe-to-study-abroad-2026-indian-students' => [
                'title' => 'Is It Still Safe to Study Abroad in 2026?',
                'category' => 'Student guidance',
                'excerpt' => 'A calm, practical guide for Indian students and parents weighing travel, visa and support questions before the next intake.',
                'image' => 'assets/transglobe/destinations/australia/campus-students.jpg',
                'date' => '12 August 2026',
                'read_time' => '6 min read',
                'author' => 'Trans Globe Indore team',
                'intro' => 'Safety is not one single statistic. It is the confidence that comes from understanding your destination, your support network and the decisions you can control before you leave.',
                'sections' => [
                    ['title' => 'Look beyond headlines', 'copy' => 'Compare official travel guidance, university support, accommodation options and local student services rather than relying on a single social post or news headline.'],
                    ['title' => 'Plan the first 90 days', 'copy' => 'A written arrival plan covering housing, transport, emergency contacts, insurance and university orientation makes the move feel structured and manageable.'],
                    ['title' => 'Ask destination-specific questions', 'copy' => 'A counsellor can help you check current visa conditions, intake timelines and the practical support available for your chosen city and institution.'],
                ],
                'tags' => ['Student life', 'Planning', '2026 intakes'],
            ],
            'university-admissions-guide-for-international-students-2025' => [
                'title' => 'What University Admissions Officers Look For',
                'category' => 'Admissions',
                'excerpt' => 'Understand what matters beyond grades, from a focused SOP to a well-rounded international student profile.',
                'image' => 'assets/transglobe/destinations/europe-card.jpg',
                'date' => '4 August 2026',
                'read_time' => '7 min read',
                'author' => 'Trans Globe Indore team',
                'intro' => 'Admissions teams want to see a student who understands the course, can contribute to the learning community and has a credible plan for the years ahead.',
                'sections' => [
                    ['title' => 'A clear academic fit', 'copy' => 'Connect your previous study and strengths to the modules, teaching style and outcomes of the course you are applying for.'],
                    ['title' => 'Evidence of motivation', 'copy' => 'Use specific projects, work experience, volunteering or independent learning to show why this field matters to you.'],
                    ['title' => 'Complete, consistent documents', 'copy' => 'A polished application is accurate, easy to review and consistent across your CV, SOP, transcripts and references.'],
                ],
                'tags' => ['University applications', 'SOP', 'Profile building'],
            ],
            'student-visa-mistakes-2025' => [
                'title' => 'Common Study Visa Mistakes and How to Avoid Them',
                'category' => 'Student visa',
                'excerpt' => 'Learn how weak SOPs, inconsistent finances and incomplete documents can affect a visa application.',
                'image' => 'store/1/default_images/blogs/blog2.jpg',
                'date' => '28 July 2026',
                'read_time' => '5 min read',
                'author' => 'Trans Globe Indore team',
                'intro' => 'Visa officers look for a complete, credible story. Most avoidable issues come from small inconsistencies that could have been caught with a final review.',
                'sections' => [
                    ['title' => 'Keep every date consistent', 'copy' => 'Check that dates in your forms, SOP, CV, offer letter and financial evidence tell the same academic and employment story.'],
                    ['title' => 'Explain your funding clearly', 'copy' => 'Prepare a simple evidence trail for tuition, living costs and the source of funds required for your destination.'],
                    ['title' => 'Prepare for questions', 'copy' => 'Know your course, university, destination and post-study plan well enough to answer naturally and honestly.'],
                ],
                'tags' => ['Visa guidance', 'Documents', 'Financial planning'],
            ],
            'student-visa-2025-usa-vs-uk-vs-canada' => [
                'title' => 'USA vs UK vs Canada: Which Visa Process Is Easiest?',
                'category' => 'Visa comparison',
                'excerpt' => 'Compare student visa requirements, timelines and practical considerations for three popular destinations.',
                'image' => 'store/1/default_images/blogs/blog4.jpg',
                'date' => '18 July 2026',
                'read_time' => '8 min read',
                'author' => 'Trans Globe Indore team',
                'intro' => 'There is no universally easiest visa. The most suitable route depends on your profile, course, finances, intake and how clearly you can evidence your plans.',
                'sections' => [
                    ['title' => 'Compare the full timeline', 'copy' => 'Allow time for tests, offers, deposits, financial evidence and appointment availability—not just the final visa decision.'],
                    ['title' => 'Look at the complete cost', 'copy' => 'Compare tuition, living costs, health cover, deposits and travel alongside the visa fee before choosing a destination.'],
                    ['title' => 'Choose the route that fits', 'copy' => 'A strong application is built around a genuine academic plan and consistent evidence, not simply a perceived processing speed.'],
                ],
                'tags' => ['USA', 'UK', 'Canada'],
            ],
            'best-study-abroad-destination-career-goals' => [
                'title' => 'Choose the Best Study Destination for Your Career Goals',
                'category' => 'Career planning',
                'excerpt' => 'Match your country choice with your budget, career direction and post-study opportunities.',
                'image' => 'assets/transglobe/destinations/dubai-card.jpg',
                'date' => '9 July 2026',
                'read_time' => '6 min read',
                'author' => 'Trans Globe Indore team',
                'intro' => 'The best destination is the one that supports the career you want while remaining realistic for your budget, academic profile and preferred lifestyle.',
                'sections' => [
                    ['title' => 'Start with the role, not the map', 'copy' => 'Define the skills, industry and work environment you want after graduation, then shortlist courses and destinations that build towards it.'],
                    ['title' => 'Balance ambition and value', 'copy' => 'Compare employability, tuition, living costs, scholarships and post-study work options as one connected decision.'],
                    ['title' => 'Keep your plan flexible', 'copy' => 'A thoughtful shortlist includes an aspirational option, a strong-fit option and a practical option with clear next steps for each.'],
                ],
                'tags' => ['Career outcomes', 'Destinations', 'Course choice'],
            ],
        ];
    }
}
