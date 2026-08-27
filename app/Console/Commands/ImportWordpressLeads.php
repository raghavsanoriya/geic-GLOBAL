<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ImportWordpressLeads extends Command
{
    protected $signature = 'legacy:import-wordpress-leads
        {--wp-config= : Absolute path to the legacy WordPress wp-config.php}
        {--prefix=wplb_ : WordPress table prefix}
        {--new-only : Import only submissions newer than each form\'s latest imported data ID}
        {--dry-run : Read and validate without saving anything}';

    protected $description = 'Idempotently import historical Contact Form 7 leads into the GEIC lead database';

    public function handle(): int
    {
        try {
            $this->configureLegacyConnection();
            $prefix = $this->safePrefix((string) $this->option('prefix'));
            $query = DB::connection('legacy_wordpress')
                ->table($prefix.'cf7anyapi_entries')
                ->whereIn('form_id', $this->formIds());

            if ($this->option('new-only')) {
                $latestIds = $this->latestImportedIds();
                $query->where(function ($query) use ($latestIds): void {
                    foreach ($this->formIds() as $formId) {
                        $query->orWhere(function ($query) use ($formId, $latestIds): void {
                            $query->where('form_id', $formId)
                                ->where('data_id', '>', $latestIds[$formId]);
                        });
                    }
                });
            }

            $rows = $query
                ->orderBy('data_id')
                ->get(['form_id', 'data_id', 'field_name', 'field_value', 'date']);
        } catch (\Throwable $exception) {
            $this->error('Legacy database could not be read: '.$exception->getMessage());

            return self::FAILURE;
        }

        $submissions = $rows->groupBy(fn (object $row): string => $row->form_id.':'.$row->data_id);
        $created = 0;
        $updated = 0;

        foreach ($submissions as $sourceId => $submissionRows) {
            $fields = $submissionRows->pluck('field_value', 'field_name')->map(fn ($value) => trim((string) $value))->all();
            $formId = (int) $submissionRows->first()->form_id;
            $payload = $this->normalise($formId, $fields, (string) $submissionRows->max('date'));

            if ($this->option('dry-run')) {
                continue;
            }

            $existing = DB::table('counselling_enquiries')
                ->where('source', 'wordpress')
                ->where('external_id', $sourceId)
                ->exists();

            DB::table('counselling_enquiries')->updateOrInsert(
                ['source' => 'wordpress', 'external_id' => $sourceId],
                [...$payload, 'updated_at' => now()],
            );

            $existing ? $updated++ : $created++;
        }

        if ($this->option('dry-run')) {
            $this->info("Validated {$submissions->count()} historical submissions. No records were changed.");
        } else {
            $this->info("Historical lead import complete: {$created} created, {$updated} refreshed, {$submissions->count()} total.");
        }

        return self::SUCCESS;
    }

    private function configureLegacyConnection(): void
    {
        $path = trim((string) $this->option('wp-config'));
        if ($path === '') {
            if (! config('database.connections.legacy_wordpress.database')) {
                throw new RuntimeException('Provide --wp-config or LEGACY_WP_DB_* environment values.');
            }

            return;
        }

        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException('The supplied wp-config.php is not readable.');
        }

        $contents = file_get_contents($path);
        $values = [];
        foreach (['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD'] as $constant) {
            if (! preg_match("/define\\(\\s*['\"]{$constant}['\"]\\s*,\\s*['\"](.*?)['\"]\\s*\\)/", $contents, $match)) {
                throw new RuntimeException("{$constant} was not found in wp-config.php.");
            }
            $values[$constant] = stripcslashes($match[1]);
        }

        [$host, $port] = array_pad(explode(':', $values['DB_HOST'], 2), 2, '3306');
        config(['database.connections.legacy_wordpress' => [
            ...config('database.connections.legacy_wordpress'),
            'host' => $host,
            'port' => $port,
            'database' => $values['DB_NAME'],
            'username' => $values['DB_USER'],
            'password' => $values['DB_PASSWORD'],
        ]]);
        DB::purge('legacy_wordpress');
    }

    /** @param array<string, string> $fields */
    private function normalise(int $formId, array $fields, string $fallbackDate): array
    {
        $submittedAt = $fields['submit_time'] ?? $fallbackDate;
        $sourceUrl = $fields['submitted_from'] ?? '/';
        $sourcePath = parse_url($sourceUrl, PHP_URL_PATH);
        $sourcePath = is_string($sourcePath) && $sourcePath !== '' ? Str::limit($sourcePath, 160, '') : '/';

        $mapped = match ($formId) {
            1946 => [
                'name' => $fields['name'] ?? null,
                'email' => $fields['email'] ?? null,
                'phone' => $fields['mobile'] ?? null,
                'city' => null,
                'destination' => $fields['country_interested'] ?? null,
                'message' => null,
                'course' => null,
                'english_test' => $fields['proficiency_exam'] ?? null,
            ],
            2174 => [
                'name' => $fields['text-136'] ?? null,
                'email' => $fields['email-171'] ?? null,
                'phone' => $fields['text-139'] ?? null,
                'city' => $fields['text-138'] ?? null,
                'destination' => $fields['menu-510'] ?? null,
                'message' => $fields['text-140'] ?? null,
                'course' => null,
                'english_test' => null,
            ],
            3270 => [
                'name' => $fields['your-name'] ?? null,
                'email' => $fields['your-email'] ?? null,
                'phone' => $fields['tel-795'] ?? null,
                'city' => null,
                'destination' => 'General enquiry',
                'message' => $fields['your-message'] ?? null,
                'course' => $fields['your-subject'] ?? null,
                'english_test' => null,
            ],
            default => [
                'name' => 'Email subscriber',
                'email' => $fields['your-email'] ?? null,
                'phone' => null,
                'city' => null,
                'destination' => 'Newsletter',
                'message' => 'Imported from the historical blog email form.',
                'course' => null,
                'english_test' => null,
            ],
        };

        $metadata = collect($fields)
            ->except(['User_IP', 'submit_time', 'submitted_from', 'mc4wp_checkbox'])
            ->filter(fn (string $value): bool => $value !== '')
            ->all();

        if (filled($fields['User_IP'] ?? null)) {
            $metadata['legacy_ip_hash'] = hash('sha256', $fields['User_IP']);
        }

        return [
            'destination' => $this->value($mapped['destination'], 'Not specified', 80),
            'full_name' => $this->value($mapped['name'], 'Name not provided', 120),
            'email' => $this->value($mapped['email'], 'not-provided@legacy.invalid', 160),
            'phone' => $this->value($mapped['phone'], 'Not provided', 24),
            'city' => $this->value($mapped['city'], 'Not provided', 100),
            'study_level' => 'Not provided',
            'preferred_intake' => 'Not provided',
            'preferred_course' => filled($mapped['course']) ? Str::limit($mapped['course'], 160, '') : null,
            'english_test' => $this->value($mapped['english_test'], 'Not provided', 50),
            'message' => filled($mapped['message']) ? $mapped['message'] : null,
            'source_page' => $sourcePath,
            'source_form' => match ($formId) {
                1946 => 'New leading form',
                2174 => 'Contact Form Home',
                3270 => 'Contact form',
                default => 'Blog Email',
            },
            'metadata' => json_encode($metadata, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'created_at' => Carbon::parse($submittedAt),
        ];
    }

    private function value(?string $value, string $fallback, int $length): string
    {
        return Str::limit(filled($value) ? trim($value) : $fallback, $length, '');
    }

    private function safePrefix(string $prefix): string
    {
        if (! preg_match('/^[a-zA-Z0-9_]+$/', $prefix)) {
            throw new RuntimeException('The WordPress table prefix is invalid.');
        }

        return $prefix;
    }

    /** @return array<int> */
    private function formIds(): array
    {
        return [1946, 2174, 3270, 3787];
    }

    /** @return array<int, int> */
    private function latestImportedIds(): array
    {
        $latestIds = array_fill_keys($this->formIds(), 0);

        DB::table('counselling_enquiries')
            ->where('source', 'wordpress')
            ->whereNotNull('external_id')
            ->pluck('external_id')
            ->each(function (string $externalId) use (&$latestIds): void {
                if (! preg_match('/^(\d+):(\d+)$/', $externalId, $matches)) {
                    return;
                }

                $formId = (int) $matches[1];
                if (array_key_exists($formId, $latestIds)) {
                    $latestIds[$formId] = max($latestIds[$formId], (int) $matches[2]);
                }
            });

        return $latestIds;
    }
}
