<?php

namespace App\Console\Commands;

use App\Models\CrmImport;
use App\Support\CrmImports;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProcessCrmImports extends Command
{
    protected $signature = 'crm:process-imports {--limit=100}';

    protected $description = 'Process a bounded, resumable batch of confirmed historical records without forwarding to Foris';

    public function handle(CrmImports $service): int
    {
        if (! config('crm.enabled')) {
            $this->error('CRM is disabled.');

            return self::FAILURE;
        }
        $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 500]]);
        if ($limit === false) {
            $this->error('Use --limit=1..500.');

            return self::FAILURE;
        }
        $lock = Cache::lock('crm-historical-import-worker', 90);
        if (! $lock->get()) {
            $this->info('Another import worker is running.');

            return self::SUCCESS;
        }
        $processed = 0;
        $deadline = microtime(true) + 40;
        try {
            $ids = DB::table('crm_import_rows')->where('status', 'pending')->whereIn('import_id', CrmImport::where('status', 'queued')->select('id'))->orderBy('id')->limit($limit)->pluck('id');
            foreach ($ids as $id) {
                if (microtime(true) >= $deadline) {
                    break;
                }$service->process((int) $id);
                $processed++;
            }
            foreach (CrmImport::where('status', 'queued')->orderBy('id')->limit(100)->get() as $import) {
                if (! DB::table('crm_import_rows')->where('import_id', $import->id)->where('status', 'pending')->exists()) {
                    CrmImport::whereKey($import->id)->where('status', 'queued')->update(['status' => 'completed', 'version' => DB::raw('version+1'), 'updated_at' => now()]);
                }
            }
        } finally {
            $lock->release();
        }
        $this->info("Processed {$processed} rows. Invalid and duplicate-review records remain visible.");

        return self::SUCCESS;
    }
}
