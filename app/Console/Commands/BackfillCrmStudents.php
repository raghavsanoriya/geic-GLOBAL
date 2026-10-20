<?php

namespace App\Console\Commands;

use App\Support\CaptureCrmStudent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BackfillCrmStudents extends Command
{
    protected $signature = 'crm:backfill-students {--after=0} {--limit=200}';

    protected $description = 'Capture a bounded batch of original GEIC enquiries without resending to Foris or changing submissions';

    public function handle(CaptureCrmStudent $capture): int
    {
        if (! config('crm.enabled')) {
            $this->error('The CRM is disabled. Enable the verified CRM release before backfilling.');

            return self::FAILURE;
        }
        $after = filter_var($this->option('after'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 500]]);
        if ($after === false || $limit === false) {
            $this->error('Use --after=0 or a positive enquiry ID and --limit=1..500.');

            return self::FAILURE;
        }
        $lock = Cache::lock('crm-student-backfill', 60);
        if (! $lock->get()) {
            $this->error('Another student backfill is running.');

            return self::FAILURE;
        }
        try {
            $ids = DB::table('counselling_enquiries')->where('id', '>', $after)->orderBy('id')->limit($limit)->pluck('id');
            $captured = 0;
            $pending = 0;
            $cursor = $after;
            $deadline = microtime(true) + 30;
            foreach ($ids as $id) {
                if (microtime(true) >= $deadline) {
                    break;
                }
                $capture->capture((int) $id) === null ? $pending++ : $captured++;
                $cursor = $id;
            }
            $this->info("Linked {$captured}; awaiting duplicate review {$pending}; next cursor {$cursor}. Original submissions and Foris deliveries were not changed.");
        } finally {
            $lock->release();
        }

        return self::SUCCESS;
    }
}
