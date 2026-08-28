<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

class PromotionPageRenderer
{
    public static function render(array $page, array $values, string $formEndpoint): HtmlString
    {
        $html = file_get_contents(base_path('landing-page/index.html'));
        $content = collect($page['fields'])
            ->mapWithKeys(fn (array $field): array => [$field['key'] => $values[$field['key']] ?? $field['default']]);
        $scopedPatterns = [
            'journey_one_title' => '~(<li[^>]*>\s*<span>01</span>\s*<div><h3>).*?(</h3>)~su',
            'journey_two_title' => '~(<li[^>]*>\s*<span>02</span>\s*<div><h3>).*?(</h3>)~su',
            'journey_three_title' => '~(<li[^>]*>\s*<span>03</span>\s*<div><h3>).*?(</h3>)~su',
            'journey_four_title' => '~(<li[^>]*>\s*<span>04</span>\s*<div><h3>).*?(</h3>)~su',
            'team_title' => '~(<header class="team-heading".*?<h2>).*?(</h2>)~su',
            'team_one_name' => '~(<article class="team-profile team-profile-one".*?<h3>).*?(</h3>)~su',
            'team_one_role' => '~(<article class="team-profile team-profile-one".*?<p>).*?(</p>)~su',
            'team_two_name' => '~(<article class="team-profile team-profile-two".*?<h3>).*?(</h3>)~su',
            'team_two_role' => '~(<article class="team-profile team-profile-two".*?<p>).*?(</p>)~su',
            'team_three_name' => '~(<article class="team-profile team-profile-three".*?<h3>).*?(</h3>)~su',
            'team_three_role' => '~(<article class="team-profile team-profile-three".*?<p>).*?(</p>)~su',
        ];

        foreach ($page['fields'] as $field) {
            $default = $field['default'];
            $value = $content[$field['key']] ?? $default;

            if ($default !== $value) {
                if (isset($scopedPatterns[$field['key']])) {
                    $html = preg_replace_callback(
                        $scopedPatterns[$field['key']],
                        fn (array $matches): string => $matches[1].e($value).$matches[2],
                        $html,
                        1
                    ) ?? $html;

                    continue;
                }

                // Image fields are stored as either a Media Library path
                // (`assets/...`) or an already-public URL (`/landing/assets/...`).
                // Match both forms so changing an image in the editor updates
                // every occurrence in the promotional template.
                $candidates = [$default];
                if ($field['type'] === 'image') {
                    $candidates[] = ltrim((string) $default, '/');
                    $candidates[] = preg_replace('~^/?landing/assets/~', 'assets/', (string) $default);
                }

                $replacement = self::assetUrl((string) $value, $field['type'] === 'image');
                foreach (array_unique(array_filter($candidates, 'is_string')) as $candidate) {
                    $pattern = preg_replace('/\s+/', '\\\\s+', preg_quote($candidate, '~'));
                    $html = preg_replace('~'.$pattern.'~u', e($replacement), $html) ?? $html;
                }
            }
        }

        $html = str_replace(
            ['url(\'assets/', 'src="assets/'],
            ['url(\'/landing/assets/', 'src="/landing/assets/'],
            $html
        );
        $html = str_replace(
            '<form class="evaluation-form" id="profile-form" data-profile-form',
            '<form class="evaluation-form" id="profile-form" data-profile-form data-endpoint="'.e($formEndpoint).'"',
            $html
        );
        $html = str_replace(
            '</head>',
            '<meta name="csrf-token" content="'.e(csrf_token()).'" />'.PHP_EOL.'  </head>',
            $html
        );

        return new HtmlString($html);
    }

    private static function assetUrl(string $value, bool $image): string
    {
        if (! $image || $value === '') {
            return $value;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '//')) {
            return $value;
        }

        if (str_starts_with($value, '/landing/assets/')) {
            return $value;
        }

        if (str_starts_with($value, 'landing/assets/')) {
            return '/'.$value;
        }

        if (str_starts_with($value, 'assets/')) {
            return '/landing/'.$value;
        }

        if (str_starts_with($value, '/storage/')) {
            return $value;
        }

        if (str_starts_with($value, 'storage/')) {
            return '/'.$value;
        }

        return $value;
    }
}
