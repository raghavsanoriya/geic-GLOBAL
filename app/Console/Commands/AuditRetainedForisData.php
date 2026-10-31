<?php

namespace App\Console\Commands;

use App\Models\CrmImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use JsonException;
use Throwable;

class AuditRetainedForisData extends Command
{
    protected $signature = 'crm:audit-retained-foris {--json : Emit machine-readable structural metadata}';

    protected $description = 'Report retained Foris import columns and non-empty counts without exposing row values';

    public function handle(): int
    {
        if (! config('crm.enabled')) {
            $this->error('CRM is disabled.');

            return self::FAILURE;
        }

        $batches = CrmImport::query()
            ->where('source', 'Foris historical records')
            ->where('entity_type', 'lead')
            ->orderBy('id')
            ->get();
        $report = [];

        foreach ($batches as $batch) {
            $headers = collect($batch->headers)->filter(fn ($header): bool => is_string($header) && $header !== '')->values();
            $counts = array_fill_keys($headers->all(), 0);
            $unreadable = 0;

            DB::table('crm_import_rows')->where('import_id', $batch->id)->orderBy('id')->lazyById(200)->each(
                function ($row) use (&$counts, &$unreadable): void {
                    try {
                        $payload = json_decode(Crypt::decryptString($row->payload), true, 512, JSON_THROW_ON_ERROR);
                        if (! is_array($payload)) {
                            $unreadable++;

                            return;
                        }
                        foreach ($counts as $header => $count) {
                            if (isset($payload[$header]) && trim((string) $payload[$header]) !== '') {
                                $counts[$header] = $count + 1;
                            }
                        }
                    } catch (Throwable) {
                        $unreadable++;
                    }
                }
            );

            $mapping = collect($batch->mapping)->filter()->flip();
            $columns = $headers->map(fn (string $header): array => [
                'header' => $this->safeLabel($header),
                'mapped_to' => $mapping->get($header),
                'non_empty' => $counts[$header],
                'sensitive_label' => preg_match('/password|credential|secret|api[ _-]?key|access[ _-]?token/i', $header) === 1,
            ])->all();
            $report[] = [
                'batch_id' => $batch->id,
                'rows' => $batch->row_count,
                'status' => $batch->status,
                'unreadable_rows' => $unreadable,
                'columns' => $columns,
            ];
        }

        if ($this->option('json')) {
            try {
                $this->line(json_encode(['batches' => $report], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
            } catch (JsonException) {
                $this->error('The structural report could not be encoded.');

                return self::FAILURE;
            }

            return self::SUCCESS;
        }

        foreach ($report as $batch) {
            $this->info("Batch {$batch['batch_id']}: {$batch['rows']} rows; {$batch['unreadable_rows']} unreadable.");
            $this->table(['Header', 'Mapped to', 'Non-empty', 'Sensitive label'], array_map(fn (array $column): array => [
                $column['header'], $column['mapped_to'] ?? 'unmapped', $column['non_empty'], $column['sensitive_label'] ? 'yes' : 'no',
            ], $batch['columns']));
        }

        return self::SUCCESS;
    }

    private function safeLabel(string $header): string
    {
        $header = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $header) ?? '';

        return mb_substr(trim(preg_replace('/\s+/u', ' ', $header) ?? ''), 0, 120);
    }
}
