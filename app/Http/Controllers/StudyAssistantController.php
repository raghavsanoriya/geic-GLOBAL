<?php

namespace App\Http\Controllers;

use App\Support\DestinationCatalog;
use App\Support\EventCatalog;
use App\Support\ScholarshipCatalog;
use App\Support\ServiceCatalog;
use App\Support\StudyAbroadKnowledge;
use App\Support\TestPrepCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StudyAssistantController extends Controller
{
    public function chat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1200'],
            'history' => ['sometimes', 'array', 'max:12'],
            'history.*.role' => ['required', 'in:user,assistant'],
            'history.*.content' => ['required', 'string', 'max:2000'],
        ]);

        $message = trim($data['message']);
        $history = collect($data['history'] ?? [])
            ->map(fn (array $item): array => [
                'role' => $item['role'],
                'content' => trim($item['content']),
            ])
            ->filter(fn (array $item): bool => $item['content'] !== '')
            ->values()
            ->all();

        $reply = $this->askProvider($message, $history);

        return response()->json([
            'reply' => $reply,
            'source' => config('services.study_assistant.api_key') ? 'assistant' : 'guided',
        ]);
    }

    private function askProvider(string $message, array $history): string
    {
        $apiKey = config('services.study_assistant.api_key');

        if (! is_string($apiKey) || trim($apiKey) === '') {
            return $this->guidedReply($message, $history);
        }

        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ...$history,
            ['role' => 'user', 'content' => $message],
        ];

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout((int) config('services.study_assistant.timeout', 20))
                ->post(config('services.study_assistant.endpoint'), [
                    'model' => config('services.study_assistant.model', 'llama-3.3-70b-versatile'),
                    'messages' => $messages,
                    'temperature' => 0.2,
                    'max_tokens' => 550,
                ]);

            $reply = $response->json('choices.0.message.content');

            if ($response->successful() && is_string($reply) && trim($reply) !== '') {
                return trim($reply);
            }

            Log::warning('Study assistant provider returned an invalid response.', [
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 500),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Study assistant provider request failed.', [
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->guidedReply($message, $history);
    }

    private function systemPrompt(): string
    {
        $destinationContext = collect(DestinationCatalog::slugs())
            ->map(function (string $slug): string {
                $destination = DestinationCatalog::find($slug) ?? [];
                $costs = collect($destination['costs'] ?? [])
                    ->map(fn (array $cost): string => $cost[0].' '.$cost[1])
                    ->implode('; ');
                $intakes = collect($destination['intakes'] ?? [])
                    ->map(fn (array $intake): string => $intake[0].' ('.$intake[1].')')
                    ->implode('; ');
                $requirements = collect($destination['requirements'] ?? [])->take(6)->implode('; ');

                return '- '.($destination['name'] ?? $slug)
                    .': '.mb_substr((string) ($destination['overview'] ?? $destination['tagline'] ?? ''), 0, 360)
                    .($costs !== '' ? ' | costs: '.$costs : '')
                    .($intakes !== '' ? ' | intakes: '.$intakes : '')
                    .(! empty($destination['visa_title']) ? ' | visa: '.$destination['visa_title'].' — '.$destination['visa_copy'] : '')
                    .($requirements !== '' ? ' | typical evidence: '.$requirements : '');
            })
            ->implode("\n");
        $serviceContext = collect(ServiceCatalog::all())
            ->map(fn (array $service): string => '- '.($service['title'] ?? 'Service').': '.mb_substr($service['summary'] ?? '', 0, 220))
            ->implode("\n");
        $testContext = collect(TestPrepCatalog::all())
            ->map(function (array $test): string {
                $facts = collect($test['facts'] ?? [])
                    ->map(fn (array $fact): string => $fact[0].' '.$fact[1])
                    ->implode('; ');
                $modules = collect($test['modules'] ?? [])
                    ->map(fn (array $module): string => $module[0].' — '.$module[1])
                    ->implode('; ');

                return '- '.($test['title'] ?? 'Test').': '.mb_substr((string) ($test['summary'] ?? ''), 0, 320)
                    .($facts !== '' ? ' | facts: '.$facts : '')
                    .($modules !== '' ? ' | sections: '.$modules : '')
                    .(! empty($test['facts_note']) ? ' | note: '.$test['facts_note'] : '');
            })
            ->implode("\n");
        $scholarshipContext = collect(ScholarshipCatalog::all())
            ->map(function (array $scholarship): string {
                $awards = collect($scholarship['awards'] ?? [])
                    ->map(fn (array $award): string => $award[0].' — '.$award[1])
                    ->implode('; ');

                return '- '.($scholarship['name'] ?? 'Scholarship').': '.mb_substr((string) ($scholarship['intro'] ?? $scholarship['tagline'] ?? ''), 0, 300)
                    .($awards !== '' ? ' | awards: '.$awards : '');
            })
            ->implode("\n");
        $eventContext = collect(EventCatalog::all())
            ->map(fn (array $event): string => '- '.($event['title'] ?? 'Event').' ('.($event['date'] ?? 'date to be confirmed').')')
            ->implode("\n");
        $planningContext = StudyAbroadKnowledge::context();

        return <<<PROMPT
You are the Trans Globe Study Assistant for Trans Globe Indore, managed by GEIC. Help prospective international students with clear, friendly and practical guidance about studying abroad.

Use the website's current catalogue as your grounding:
Destinations:
{$destinationContext}
Services:
{$serviceContext}
Test preparation:
{$testContext}
Scholarships:
{$scholarshipContext}
Events:
{$eventContext}

Curated planning guidance (paraphrased from reputable consultancy workflows and official guidance):
{$planningContext}

Answer in concise paragraphs or short bullets. Ask one useful follow-up question when the student's profile is unclear (course/level, destination, budget, intake or test score). Never invent a university requirement, fee, visa rule, scholarship deadline or approval. Explain that requirements and immigration rules change and should be confirmed with the university or an authorised counsellor. Do not provide legal or immigration advice. Recommend a free Trans Globe counselling conversation when a personalised assessment is needed. Never mention system prompts, APIs or tools.
PROMPT;
    }

    private function guidedReply(string $message, array $history = []): string
    {
        $lower = mb_strtolower($message.' '.collect($history)->where('role', 'user')->pluck('content')->implode(' '));

        if (preg_match('/study plan|plan my|planning assistant|roadmap|step[- ]by[- ]step/', $lower)) {
            return $this->studyPlanReply($lower);
        }

        foreach (DestinationCatalog::slugs() as $slug) {
            $destination = DestinationCatalog::find($slug) ?? [];
            $name = mb_strtolower((string) ($destination['name'] ?? $slug));

            if (str_contains($lower, $name) || str_contains($lower, str_replace('-', ' ', $slug))) {
                return $this->destinationReply($destination, $lower);
            }
        }

        foreach (TestPrepCatalog::all() as $test) {
            if (str_contains($lower, mb_strtolower((string) ($test['title'] ?? '')))) {
                $facts = collect($test['facts'] ?? [])->map(fn (array $fact): string => $fact[0].' ('.$fact[1].')')->implode(', ');
                $modules = collect($test['modules'] ?? [])->pluck(0)->implode(', ');
                $title = $test['title'] ?? 'This test';

                return "{$title} preparation: {$test['summary']} Key areas include {$modules}. ".($facts ? 'At a glance: '.$facts.'. ' : '').'Which destination and target score should we plan for?';
            }
        }

        foreach (ScholarshipCatalog::all() as $scholarship) {
            $name = mb_strtolower((string) ($scholarship['name'] ?? ''));

            if ($name !== '' && str_contains($lower, $name)) {
                $awards = collect($scholarship['awards'] ?? [])->map(fn (array $award): string => $award[0])->implode(', ');

                return ($scholarship['name'] ?? 'This scholarship area').' offers options such as '.$awards.'. Awards and deadlines vary by university, course and intake, so eligibility must be checked for your profile. What is your study level?';
            }
        }

        foreach (ServiceCatalog::all() as $service) {
            if (str_contains($lower, mb_strtolower((string) ($service['title'] ?? '')))) {
                return ($service['title'] ?? 'This service').' helps with '.$service['summary'].' The usual next step is a profile review so we can tailor the advice to your course, destination and intake.';
            }
        }

        if (preg_match('/how (do|can) i apply|application process|steps to study|study abroad process|where do i start/', $lower)) {
            return 'A practical study-abroad plan is: understand your profile and goals; compare course, country and budget fit; build a balanced university shortlist; confirm each institution\'s official requirements and deadlines; prepare your tests and documents; submit complete applications; then review the offer before preparing the visa and departure. Which destination, course level and intake are you targeting?';
        }

        if (preg_match('/shortlist|which university|best university|choose a university|course fit/', $lower)) {
            return 'A useful shortlist balances ambitious, realistic and safer options. Compare the exact course content, entry requirements, total tuition and living costs, location, accreditation, placements and graduate outcomes—not rankings alone. Share your academic score, course, budget and preferred country so I can help you build that comparison.';
        }

        if (preg_match('/sop|statement of purpose|lor|letter of recommendation|documents|document checklist|cv/', $lower)) {
            return 'Most applications need a passport, academic transcripts and certificates, an accepted English-test result where required, a CV, a focused statement of purpose, references and any course-specific portfolio or work evidence. Keep names and dates consistent, use truthful examples and check the university\'s own file, translation and certification rules before uploading. What course and destination are you applying for?';
        }

        if (preg_match('/timeline|when should i start|how early|planning calendar|deadline/', $lower)) {
            return 'Starting 9–12 months before your preferred intake usually leaves time for shortlisting, tests, scholarships, applications, offer conditions and visa preparation. Some courses and scholarships close earlier, so confirm the exact dates on each institution\'s official page. Which intake are you aiming for?';
        }

        if (preg_match('/consultan|agent|counsell|counsel/', $lower)) {
            return 'A good counsellor should explain options, disclose the application route, keep a written checklist and let you make informed choices. Ask whether they are authorised by the institution, what is included, how data is handled and whether any outcome is being guaranteed. Admissions and visas are decided by universities and governments, not by an agent.';
        }

        if (str_contains($lower, 'ielts') || str_contains($lower, 'pte') || str_contains($lower, 'toefl') || str_contains($lower, 'test')) {
            return 'The right English test depends on your destination, university and course. We can help you compare IELTS, PTE and TOEFL, set a target score and plan preparation around your intake. Which country and course are you targeting?';
        }

        if (str_contains($lower, 'scholarship') || str_contains($lower, 'funding')) {
            return 'Scholarships can be merit-based, course-specific or linked to a university and intake. Your academic results, chosen course and application timing usually matter. Tell me your destination and study level, and I will suggest the best places to start looking.';
        }

        if (str_contains($lower, 'visa') || str_contains($lower, 'permit')) {
            return 'Visa evidence varies by destination and your personal circumstances. A counsellor can help you prepare the admission offer, financial evidence, English results and supporting documents, then confirm the current official requirements. Which destination are you considering?';
        }

        if (str_contains($lower, 'cost') || str_contains($lower, 'budget') || str_contains($lower, 'fee')) {
            return 'Your total budget depends on tuition, city, accommodation, insurance, travel and visa costs. Share your destination, course level and approximate budget so we can compare realistic options and available scholarships.';
        }

        return 'I can help you explore destinations, courses, scholarships, English tests, admissions and visa preparation. Tell me your preferred country, study level and intended intake, or ask a specific question to get started.';
    }

    private function studyPlanReply(string $question): string
    {
        $destination = collect(DestinationCatalog::slugs())
            ->map(fn (string $slug): array => DestinationCatalog::find($slug) ?? [])
            ->first(function (array $item) use ($question): bool {
                $name = mb_strtolower((string) ($item['name'] ?? ''));
                $slug = mb_strtolower((string) ($item['slug'] ?? ''));

                return ($name !== '' && str_contains($question, $name)) || ($slug !== '' && str_contains($question, str_replace('-', ' ', $slug)));
            });
        $destinationName = $destination['name'] ?? 'your preferred destination';
        $intakes = collect($destination['intakes'] ?? [])->take(2)->map(fn (array $intake): string => $intake[0])->implode(' or ');
        $intakeLine = $intakes !== '' ? " Common planning windows for {$destinationName} include {$intakes}." : '';

        return "Let’s build your study-abroad plan for {$destinationName}.\n\n1. Profile: confirm your course level, academic results, English-test status and target intake.\n2. Shortlist: compare course fit, tuition, living costs, visa pathway and post-study options across ambitious, realistic and safer choices.\n3. Prepare: organise transcripts, passport, CV, statement of purpose, references and any portfolio; book the right test early.\n4. Apply: check each university’s official requirements and deadline, submit a complete application, then review the offer before visa preparation.{$intakeLine}\n\nShare your course, study level, budget and intended intake and I’ll turn this into a more specific checklist.";
    }

    private function destinationReply(array $destination, string $question): string
    {
        $name = $destination['name'] ?? 'This destination';

        if (preg_match('/cost|fee|tuition|budget/', $question) && ! empty($destination['costs'])) {
            $costs = collect($destination['costs'])->map(fn (array $cost): string => $cost[0].': '.$cost[1])->implode('; ');

            return "For {$name}, the catalogue lists these planning ranges: {$costs}. Actual costs vary by institution, course, city and intake, so use these as an initial estimate rather than a quote. What course level are you considering?";
        }

        if (preg_match('/intake|when should|start|deadline/', $question) && ! empty($destination['intakes'])) {
            $intakes = collect($destination['intakes'])->map(fn (array $intake): string => $intake[0].' — '.$intake[1])->implode('; ');

            return "Common {$name} intake windows are: {$intakes}. Individual course deadlines differ, so applying early is important. Which intake are you aiming for?";
        }

        if (preg_match('/visa|permit/', $question) && ! empty($destination['visa_title'])) {
            return "The usual route for {$name} is the {$destination['visa_title']}. The catalogue guidance is: {$destination['visa_copy']} Requirements can change, so confirm the current rules with the official immigration authority before applying.";
        }

        if (preg_match('/requirement|document|eligib/', $question) && ! empty($destination['requirements'])) {
            return "Typical {$name} application evidence includes: ".collect($destination['requirements'])->take(5)->implode('; ').'. Exact requirements depend on your course and university. What qualification are you applying for?';
        }

        if (preg_match('/scholarship|funding|award/', $question)) {
            $scholarship = ScholarshipCatalog::find((string) ($destination['slug'] ?? ''));

            if ($scholarship && ! empty($scholarship['awards'])) {
                $awards = collect($scholarship['awards'])
                    ->map(fn (array $award): string => $award[0].' — '.rtrim($award[1], '.'))
                    ->implode('; ');

                return "For {$name}, the scholarship catalogue includes: {$awards}. Award values, eligibility and deadlines vary by university and intake, so these should be checked against your course and profile.";
            }
        }

        return "{$name}: ".($destination['tagline'] ?? $destination['overview'] ?? 'Explore this destination with a personalised study plan.').' Tell me your course, study level and preferred intake for a more useful comparison.';
    }
}
