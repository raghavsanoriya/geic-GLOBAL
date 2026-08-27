<?php

namespace App\Support;

class ServiceCatalog
{
    public static function find(string $slug): ?array
    {
        $service = self::services()[$slug] ?? null;

        return $service ? array_merge(self::defaults($slug), $service) : null;
    }

    public static function all(): array
    {
        return array_map(fn (string $slug) => self::find($slug), array_keys(self::services()));
    }

    private static function defaults(string $slug): array
    {
        return [
            'slug' => $slug,
            'image' => 'assets/services/expert-counselling.jpg',
            'image_alt' => 'Students preparing for international education',
            'results' => ['Personalised planning', 'Expert document support', 'A clear next step'],
            'process' => [
                ['Understand your profile', 'We begin with your academic background, goals, timeline and preferred destinations.'],
                ['Build the right plan', 'Your counsellor maps the requirements, documents and decisions specific to your journey.'],
                ['Move forward with confidence', 'Receive focused support at each milestone and know exactly what happens next.'],
            ],
            'faqs' => [
                ['When should I start?', 'Starting early gives you the widest choice of courses, scholarships and appointment times. We can still help if your preferred intake is closer.'],
                ['Is the first counselling session free?', 'Yes. Your first conversation is an opportunity to understand your options and decide the most practical next step.'],
                ['Can I use this service online?', 'Yes. Guidance can be arranged online or with the Trans Globe Indore team, depending on what works best for you.'],
            ],
        ];
    }

    private static function services(): array
    {
        return [
            'expert-counselling' => [
                'number' => '01', 'title' => 'Expert Counselling', 'eyebrow' => 'Start with the right direction',
                'summary' => 'Navigate your international education journey with a plan that fits your academic profile, career ambitions and budget.',
                'overview' => 'Choosing a country, course and university can feel overwhelming. Our counsellors help you turn those choices into a clear, personalised roadmap — based on what genuinely fits you, not a one-size-fits-all shortlist.',
                'image' => 'assets/services/expert-counselling.jpg', 'image_alt' => 'Students and advisers planning their education journey',
                'results' => ['A profile-led study plan', 'Course and country shortlisting', 'A realistic budget and timeline'],
                'process' => [
                    ['Tell us your goals', 'Share your academic record, career direction, preferred countries and the things you are unsure about.'],
                    ['Explore your best-fit options', 'Compare courses, institutions, costs, intakes and post-study opportunities with an expert.'],
                    ['Leave with a roadmap', 'Know which tests, documents and application milestones will move you towards your preferred intake.'],
                ],
                'faqs' => [
                    ['What happens in a counselling session?', 'Your counsellor reviews your profile, listens to your goals and explains the country, course and application paths that fit.'],
                    ['Do I need to know my preferred country already?', 'No. Many students begin with a broad idea. We help you compare destinations before you decide.'],
                    ['Can parents join the session?', 'Yes. Parents are welcome to join so that costs, timelines and next steps are clear for everyone.'],
                ],
            ],
            'sop-documentation' => [
                'number' => '02', 'title' => 'SOP & Documentation', 'eyebrow' => 'Make every application count',
                'summary' => 'Present your academic story clearly with a persuasive SOP, polished documents and a complete application file.',
                'overview' => 'Admission teams see thousands of applications. We help you create a focused Statement of Purpose, organise transcripts and prepare supporting documents that reflect your motivation, preparation and potential.',
                'image' => 'assets/services/sop-documentation.jpg', 'image_alt' => 'Writing an academic statement and supporting documents',
                'results' => ['Purposeful SOP and CV guidance', 'A complete document checklist', 'Application-ready supporting material'],
                'process' => [
                    ['Review your academic story', 'Identify the experience, achievements and goals that give your application a coherent narrative.'],
                    ['Prepare the key documents', 'Structure your SOP, CV, references and academic records to match university requirements.'],
                    ['Final quality check', 'Review every required item before it is uploaded, submitted or shared with an institution.'],
                ],
            ],
            'university-admissions' => [
                'number' => '03', 'title' => 'University Admissions', 'eyebrow' => 'Apply with confidence',
                'summary' => 'Manage university shortlisting, portals, deadlines and submissions with end-to-end admissions support.',
                'overview' => 'A strong application is more than a form. We help you choose realistic and aspirational options, track requirements, submit complete applications and understand each offer as it arrives.',
                'image' => 'assets/services/university-admissions.jpg', 'image_alt' => 'Students reviewing university options together',
                'results' => ['Thoughtful university shortlisting', 'Deadline and portal management', 'Offer-letter support'],
                'process' => [
                    ['Shortlist strategically', 'Build a list of universities that suits your profile, budget, course preferences and career goals.'],
                    ['Prepare and submit', 'Complete forms, upload documents and meet each institution’s deadlines with support.'],
                    ['Review your offers', 'Understand conditions, fees, scholarships and the next action needed to secure your place.'],
                ],
            ],
            'scholarship-guidance' => [
                'number' => '04', 'title' => 'Scholarship Guidance', 'eyebrow' => 'Reduce the cost of your ambition',
                'summary' => 'Find scholarships you may qualify for and prepare applications that make the most of your academic strengths.',
                'overview' => 'Scholarships can be merit-based, need-based, program-specific or offered by external organisations. Our team helps you identify relevant opportunities early and prepare the evidence, essays and timelines they require.',
                'image' => 'assets/services/scholarship-guidance.jpg', 'image_alt' => 'University library resources for scholarship research',
                'results' => ['Relevant scholarship search', 'Eligibility and deadline guidance', 'Essay and supporting-document support'],
                'process' => [
                    ['Match available awards', 'Identify university, government and external awards that suit your course and profile.'],
                    ['Prepare the evidence', 'Collect grades, achievements, financial documents and written statements requested by each scholarship.'],
                    ['Apply on time', 'Submit a complete application before scholarship and university deadlines close.'],
                ],
            ],
            'test-preparation' => [
                'number' => '05', 'title' => 'Test Preparation', 'eyebrow' => 'Reach the score your plan needs',
                'summary' => 'Prepare for IELTS, PTE, TOEFL, GRE, GMAT, SAT and German language exams with structured expert coaching.',
                'overview' => 'The right score can open more course, university and scholarship options. Our training combines structured study plans, targeted feedback and practice across the skills your test measures.',
                'image' => 'assets/services/test-preparation.jpg', 'image_alt' => 'Classroom prepared for English and entrance test coaching',
                'results' => ['Test and score-target planning', 'Expert coaching and practice', 'Confidence for test day'],
                'process' => [
                    ['Set your target', 'Choose the right test and score based on your preferred universities, course and intake.'],
                    ['Build skill by skill', 'Practise speaking, listening, reading and writing with guided feedback and test strategies.'],
                    ['Track your readiness', 'Use mock tests and progress reviews to decide when you are ready to book your exam.'],
                ],
            ],
            'visa-assistance' => [
                'number' => '06', 'title' => 'Visa Assistance', 'eyebrow' => 'Prepare every important detail',
                'summary' => 'Navigate visa documentation, financial evidence and interview preparation with country-aware expert support.',
                'overview' => 'Visa applications require careful, consistent evidence. Our specialists explain the current requirements, help you prepare a complete file and ensure you understand the next step before you submit.',
                'image' => 'assets/services/visa-assistance.jpg', 'image_alt' => 'Passport and travel documents at an airport',
                'results' => ['Document and financial-proof guidance', 'Visa application preparation', 'Interview readiness where required'],
                'process' => [
                    ['Understand the requirements', 'Review the documents, financial proof and eligibility requirements for your destination.'],
                    ['Build a complete file', 'Prepare and check every form and supporting document before it is submitted.'],
                    ['Submit with clarity', 'Understand timelines, appointments and interview expectations for the final stage.'],
                ],
            ],
            'health-insurance' => [
                'number' => '07', 'title' => 'Health Insurance', 'eyebrow' => 'Stay protected abroad',
                'summary' => 'Arrange the student health cover and international insurance needed for your destination and provider.',
                'overview' => 'Health cover is often an important part of student-visa compliance and settling in abroad. We help you understand what your destination and university need, then guide you towards an appropriate option.',
                'image' => 'assets/services/health-insurance.jpg', 'image_alt' => 'Healthcare professional representing student health cover',
                'results' => ['Country and university requirement guidance', 'Student cover comparison support', 'Visa-ready insurance documentation'],
            ],
            'loans-financial-guide' => [
                'number' => '08', 'title' => 'Loans & Financial Guide', 'eyebrow' => 'Plan the money side with confidence',
                'summary' => 'Understand education loans, forex, international banking and required financial evidence before you travel.',
                'overview' => 'Funding an international degree involves more than tuition. We help you build a practical plan for education loans, living costs, forex and country-specific financial documentation.',
                'image' => 'assets/services/loans-financial-guide.jpg', 'image_alt' => 'Education finance and budget planning',
                'results' => ['Education-loan pathway guidance', 'Financial-document preparation', 'Forex and banking orientation'],
            ],
            'accommodation-assistance' => [
                'number' => '09', 'title' => 'Accommodation Assistance', 'eyebrow' => 'Find your home away from home',
                'summary' => 'Explore safe accommodation options around your university before you arrive in your new city.',
                'overview' => 'A secure place to stay makes the move abroad easier. We help you understand the options around your campus — including on-campus housing, private rentals, shared apartments, homestays and student accommodation.',
                'image' => 'assets/services/accommodation-assistance.jpg', 'image_alt' => 'Students together in a shared residence',
                'results' => ['Accommodation-option guidance', 'Budget and location comparison', 'A smoother arrival plan'],
            ],
            'pre-post-departure' => [
                'number' => '10', 'title' => 'Pre & Post Departure', 'eyebrow' => 'Feel ready before you fly',
                'summary' => 'Get practical support for packing, arrival, local essentials and settling into your new student life.',
                'overview' => 'Your support should not end when the visa is approved. Pre-departure guidance helps you prepare for the move, while arrival support makes those first important days in a new country feel more manageable.',
                'image' => 'assets/services/pre-post-departure.jpg', 'image_alt' => 'World map and aeroplane for an overseas journey',
                'results' => ['Pre-departure orientation', 'Arrival and local-essentials guidance', 'Support through your transition'],
                'process' => [
                    ['Prepare before you leave', 'Review essential documents, packing, travel planning and cultural expectations.'],
                    ['Land with a plan', 'Know what to arrange first — accommodation, SIM card, banking, transport and university check-in.'],
                    ['Settle into your new rhythm', 'Use practical advice to adapt to student life and your new local environment.'],
                ],
            ],
        ];
    }
}
