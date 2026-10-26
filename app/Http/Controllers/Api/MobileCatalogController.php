<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\DestinationCatalog;
use App\Support\EventCatalog;
use App\Support\MobileStudioCatalog;
use App\Support\ScholarshipCatalog;
use App\Support\ServiceCatalog;
use App\Support\TestPrepCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileCatalogController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'version' => '2026-09-04',
            'updatedAt' => now()->toIso8601String(),
            'studio' => MobileStudioCatalog::all($request),
            'data' => [
                'destinations' => collect(DestinationCatalog::slugs())
                    ->map(fn (string $slug): array => $this->destination($request, $slug))
                    ->values(),
                'services' => collect(ServiceCatalog::all())
                    ->map(fn (array $service): array => [
                        'id' => $service['slug'],
                        'title' => $service['title'],
                        'subtitle' => $service['eyebrow'],
                        'description' => $service['summary'],
                        'overview' => $service['overview'],
                        'imageUrl' => $this->assetUrl($request, $service['image']),
                        'icon' => $this->serviceIcon($service['slug']),
                        'highlights' => $service['results'],
                        'process' => $service['process'],
                    ])->values(),
                'events' => collect(EventCatalog::all())
                    ->map(fn (array $event): array => [
                        'id' => $event['slug'],
                        'title' => $event['title'],
                        'type' => $event['status'],
                        'date' => $event['date'],
                        'time' => $event['time'],
                        'location' => $event['location'],
                        'destination' => $event['destination'],
                        'description' => $event['summary'],
                        'overview' => $event['overview'],
                        'imageUrl' => $this->assetUrl($request, $event['image']),
                        'highlights' => $event['highlights'],
                    ])->values(),
                'tests' => collect(TestPrepCatalog::all())
                    ->map(fn (array $test): array => [
                        'id' => $test['slug'],
                        'title' => $test['title'],
                        'subtitle' => $test['eyebrow'],
                        'description' => $test['summary'],
                        'overview' => $test['overview'],
                        'imageUrl' => $this->assetUrl($request, $test['image']),
                        'facts' => $test['facts'],
                        'modules' => $test['modules'],
                        'note' => $test['facts_note'] ?? null,
                    ])->values(),
                'scholarships' => collect(ScholarshipCatalog::all())
                    ->map(fn (array $scholarship): array => [
                        'id' => $scholarship['slug'],
                        'name' => $scholarship['name'],
                        'tagline' => $scholarship['tagline'],
                        'description' => $scholarship['intro'],
                        'imageUrl' => $this->assetUrl($request, $scholarship['image']),
                        'awards' => $scholarship['awards'],
                    ])->values(),
                'testimonials' => [],
                'contact' => [
                    'office' => 'Trans Globe Indore',
                    'email' => 'info@geic.in',
                    'website' => 'https://geic.in',
                ],
            ],
        ]);
    }

    private function destination(Request $request, string $slug): array
    {
        $destination = DestinationCatalog::find($slug) ?? [];

        return [
            'id' => $slug,
            'name' => $destination['name'] ?? $slug,
            'tagline' => $destination['tagline'] ?? '',
            'detail' => $destination['overview'] ?? $destination['tagline'] ?? '',
            'imageUrl' => $this->assetUrl($request, $destination['card'] ?? ''),
            'stat' => $destination['stats'][0][1] ?? $destination['facts'][0][1] ?? 'Explore options',
            'highlights' => collect($destination['benefits'] ?? [])->take(3)->pluck(0)->values(),
            'facts' => $destination['facts'] ?? [],
            'costs' => $destination['costs'] ?? [],
            'intakes' => $destination['intakes'] ?? [],
            'universities' => collect($destination['universities'] ?? [])->pluck('name')->values(),
        ];
    }

    private function assetUrl(Request $request, string $path): string
    {
        return $request->getSchemeAndHttpHost().'/'.ltrim($path, '/');
    }

    private function serviceIcon(string $slug): string
    {
        return match ($slug) {
            'expert-counselling' => 'account-voice',
            'sop-documentation' => 'file-document-edit-outline',
            'university-admissions' => 'school-outline',
            'scholarship-guidance' => 'medal-outline',
            'test-preparation' => 'notebook-edit-outline',
            'visa-assistance' => 'passport',
            'health-insurance' => 'shield-check-outline',
            'loans-financial-guide' => 'cash-multiple',
            'accommodation-assistance' => 'home-city-outline',
            'pre-post-departure' => 'airplane-takeoff',
            default => 'briefcase-outline',
        };
    }
}
