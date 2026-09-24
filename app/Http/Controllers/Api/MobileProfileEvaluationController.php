<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\DestinationCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MobileProfileEvaluationController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'academicPercentage' => ['required', 'numeric', 'between:35,100'],
            'studyLevel' => ['required', Rule::in(['Undergraduate', 'Postgraduate', 'Diploma or pathway', 'Research'])],
            'preferredDestinations' => ['required', 'array', 'between:1,3'],
            'preferredDestinations.*' => ['required', 'string', 'max:80'],
            'englishTest' => ['required', Rule::in(['IELTS', 'PTE', 'TOEFL', 'Duolingo', 'Planning to take a test', 'Not sure yet'])],
            'englishScore' => ['nullable', 'string', 'max:30'],
            'preferredIntake' => ['nullable', 'string', 'max:80'],
        ]);

        $destinations = collect($data['preferredDestinations'])
            ->map(function (string $value): ?array {
                $destination = DestinationCatalog::find(Str::slug($value));

                return $destination ? [
                    'id' => $destination['slug'],
                    'name' => $destination['name'],
                    'tagline' => $destination['tagline'],
                    'nextIntake' => $destination['intakes'][0][0] ?? 'Confirm with a counsellor',
                ] : null;
            })
            ->filter()
            ->values();

        if ($destinations->count() !== count($data['preferredDestinations'])) {
            return response()->json([
                'message' => 'Choose at least one destination from the current GEIC catalogue.',
                'errors' => ['preferredDestinations' => ['The selected destination is not available.']],
            ], 422);
        }

        $hasConfirmedTest = ! in_array($data['englishTest'], ['Planning to take a test', 'Not sure yet'], true);
        $score = (int) round(((float) $data['academicPercentage'] * 0.55)
            + ($hasConfirmedTest ? 22 : 10)
            + (filled($data['englishScore'] ?? null) ? 8 : 0)
            + (filled($data['preferredIntake'] ?? null) ? 8 : 4)
            + min(7, $destinations->count() * 3));
        $readinessScore = min(100, max(35, $score));

        $summary = match (true) {
            $readinessScore >= 78 => 'Your profile is ready for a focused shortlist and an official requirements review.',
            $readinessScore >= 60 => 'You have a useful starting profile; strengthen the missing evidence before finalising applications.',
            default => 'Begin with a counsellor-led profile review and build your academic, test and budget plan step by step.',
        };

        return response()->json([
            'readinessScore' => $readinessScore,
            'summary' => $summary,
            'matches' => $destinations->map(fn (array $destination): array => [
                ...$destination,
                'fit' => $readinessScore >= 78 ? 'Strong starting fit' : ($readinessScore >= 60 ? 'Worth exploring' : 'Needs profile review'),
            ]),
            'strengths' => array_values(array_filter([
                (float) $data['academicPercentage'] >= 70 ? 'A solid academic foundation for shortlisting.' : null,
                $hasConfirmedTest ? 'An English-test route has already been selected.' : null,
                $destinations->count() <= 2 ? 'A focused destination preference.' : 'Multiple destination options for comparison.',
            ])),
            'actionItems' => array_values(array_filter([
                'Confirm official entry requirements for each shortlisted course.',
                ! $hasConfirmedTest ? 'Choose an accepted English test and set a target score.' : null,
                ! filled($data['englishScore'] ?? null) ? 'Add your current or target English-test score.' : null,
                ! filled($data['preferredIntake'] ?? null) ? 'Choose a preferred intake and application timeline.' : null,
                'Review tuition, living costs and financial evidence with a GEIC counsellor.',
            ])),
            'disclaimer' => 'This is planning guidance, not an admission, scholarship or visa guarantee. Confirm current requirements with the institution and relevant authorities.',
        ]);
    }
}
