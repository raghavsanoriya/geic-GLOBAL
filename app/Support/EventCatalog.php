<?php

namespace App\Support;

class EventCatalog
{
    public static function find(string $slug): ?array
    {
        return collect(self::all())->firstWhere('slug', $slug);
    }

    public static function all(): array
    {
        return [
            self::event(
                slug: 'meet-eu-business-school-2026',
                title: 'Meet EU Business School',
                date: '27 August 2026',
                time: '4:00 PM',
                destination: 'Europe',
                image: 'assets/transglobe/events/meet-eu-business-school-2026.jpg',
                status: 'Upcoming',
                summary: 'Explore business programmes, international campuses and the application route to EU Business School with guidance from Trans Globe Indore.',
                overview: 'This focused university interaction is an opportunity to understand EU Business School programmes, entry expectations and the decisions that shape a strong application. Bring your academic profile and questions so our counsellors can help you turn the conversation into a practical next step.',
                highlights: ['Explore programme and campus options', 'Understand entry and document expectations', 'Discuss scholarships, timelines and next steps'],
            ),
            self::event(
                slug: 'indore-usa-uk-admission-day-2026',
                title: 'Indore USA & UK Admission Day 2026',
                date: '22 April 2026',
                time: 'Event archive',
                destination: 'USA & United Kingdom',
                image: 'assets/transglobe/events/indore-usa-uk-admission-day-2026.jpg',
                summary: 'An Indore admission day created to help students compare study options, admissions and career pathways across the USA and UK.',
                overview: 'The admission day brought destination and application guidance into one focused conversation. Students could compare two leading study destinations, review suitable courses and understand how their profile, budget and preferred intake shaped the strongest route forward.',
                highlights: ['Compare USA and UK study pathways', 'Review profile-led university options', 'Plan admissions, funding and visa milestones'],
            ),
            self::event(
                slug: 'southern-queensland-indore-2026',
                title: 'University of Southern Queensland Visit Indore 2026',
                date: '13 April 2026',
                time: 'Event archive',
                destination: 'Australia',
                image: 'assets/transglobe/events/southern-queensland-indore-2026.jpg',
                summary: 'A direct university interaction for students exploring programmes and admission pathways at the University of Southern Queensland.',
                overview: 'This Indore visit gave prospective students a clearer view of programme choices, admission expectations and the experience of studying in Australia. Trans Globe counsellors supported students in relating university information to their own academic and career goals.',
                highlights: ['Meet a university representative', 'Explore Australian programme options', 'Clarify admissions and application preparation'],
            ),
            self::event(
                slug: 'indore-australia-admission-day-2026',
                title: 'Indore Australia Admission Day 2026',
                date: '5 April 2026',
                time: 'Event archive',
                destination: 'Australia',
                image: 'assets/transglobe/events/indore-australia-admission-day-2026.jpg',
                summary: 'A dedicated Australia admission day covering university choices, application planning and the student journey from Indore.',
                overview: 'Students used this focused admission day to compare Australian universities and courses, understand common application requirements and build a realistic study plan. The session connected destination research with the practical decisions required for the next intake.',
                highlights: ['Shortlist courses and universities', 'Understand documents and intake timing', 'Build a practical Australia study plan'],
            ),
            self::event(
                slug: 'charles-darwin-indore-2026',
                title: 'Charles Darwin University Visit Indore 2026',
                date: '24 March 2026',
                time: 'Event archive',
                destination: 'Australia',
                image: 'assets/transglobe/events/charles-darwin-indore-2026.jpg',
                summary: 'A university visit for students interested in Charles Darwin University programmes and Australian study opportunities.',
                overview: 'The visit created space for students to ask direct questions about programmes, admissions and studying in Australia. With support from the Indore counselling team, attendees could connect university information with their individual profile and preferred career direction.',
                highlights: ['Learn about Charles Darwin University', 'Discuss programme and entry options', 'Map the next application steps'],
            ),
            self::event(
                slug: 'indore-global-uni-expo-2026',
                title: 'Indore Global Uni Expo 2026',
                date: '17 January 2026',
                time: 'Event archive',
                destination: 'Multiple destinations',
                image: 'assets/transglobe/events/indore-global-uni-expo-2026.jpg',
                summary: 'A multi-destination university expo bringing international study choices and expert guidance together in Indore.',
                overview: 'The Indore Global Uni Expo helped students explore multiple destinations in one place. It was designed for side-by-side course and country comparison, direct conversations and a clearer understanding of the admissions, funding and visa steps ahead.',
                highlights: ['Explore multiple study destinations', 'Compare courses and institutions', 'Get one-to-one guidance for your profile'],
            ),
            self::event(
                slug: 'global-uni-expo-2023',
                title: "Global UNIExpo 2023 – Gujarat's Biggest Study Abroad Event",
                date: '19 February 2023',
                time: 'Event archive',
                destination: 'Multiple destinations',
                image: 'assets/transglobe/events/global-uni-expo-2023.jpg',
                summary: 'A landmark Trans Globe study-abroad expo connecting students with international education opportunities and expert advice.',
                overview: 'Global UNIExpo brought a broad international education network together for students researching their future abroad. The event combined destination discovery, university conversations and practical counselling so attendees could leave with better questions and a clearer direction.',
                highlights: ['Discover global education options', 'Connect with university networks', 'Turn research into an action plan'],
            ),
        ];
    }

    private static function event(
        string $slug,
        string $title,
        string $date,
        string $time,
        string $destination,
        string $image,
        string $summary,
        string $overview,
        array $highlights,
        string $status = 'Past event',
    ): array {
        return [
            'slug' => $slug,
            'title' => $title,
            'date' => $date,
            'time' => $time,
            'destination' => $destination,
            'location' => 'Trans Globe Indore',
            'image' => $image,
            'image_alt' => $title.' event artwork',
            'status' => $status,
            'summary' => $summary,
            'overview' => $overview,
            'highlights' => $highlights,
            'steps' => [
                ['Share your profile', 'Tell us your academic background, intended intake and the questions you want answered.'],
                ['Explore the event', 'Use the session to compare programmes, requirements and study pathways.'],
                ['Review your fit', 'Discuss what the information means for your profile, budget and career direction.'],
                ['Plan the next step', 'Leave with a focused action list for shortlisting, documents and applications.'],
            ],
            'faqs' => [
                ['Who should attend?', 'Students and families researching international education, comparing destinations or preparing for an upcoming intake can benefit from the event.'],
                ['What should I bring?', 'Bring your latest academic documents and a short list of questions. These help the counsellor give you more relevant guidance.'],
                ['How do I confirm the venue or attendance mode?', 'Contact the Trans Globe Indore team. We will confirm the latest venue, availability and joining instructions for active events.'],
            ],
        ];
    }
}
