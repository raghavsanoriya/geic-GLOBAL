<?php

namespace App\Console\Commands;

use App\Support\ForisLeadSync;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ForisSync extends Command
{
    protected $signature = 'crm:foris-sync {--backfill : Schedule a bounded chunk of existing website leads}';

    protected $description = 'Run the bounded Foris CRM queue worker, prioritising live enquiries';

    public function handle(ForisLeadSync $sync): int
    {
        $lock = Cache::lock('foris-sync-worker', 75);
        if (! $lock->get()) {
            $this->info('The Foris worker is already running.');

            return self::SUCCESS;
        }
        try {
            Cache::put('foris-worker-heartbeat', now()->toIso8601String(), now()->addDays(2));
            if ($this->option('backfill') && $sync->configured()) {
                $this->call('crm:foris-backfill', ['--limit' => 200]);
            }
            $count = $sync->enqueuePending();
            if (! $sync->configured()) {
                $this->info('Foris delivery is paused. Pending enquiries remain safely stored.');

                return self::SUCCESS;
            }
            $this->info($count.' pending deliveries scheduled.');

            return $this->call('queue:work', [
                'connection' => 'foris',
                '--queue' => 'foris-live,foris-backfill',
                '--stop-when-empty' => true,
                '--max-time' => 45,
                '--timeout' => 15,
                '--sleep' => 0,
                '--tries' => 0,
            ]);
        } finally {
            $lock->release();
        }
    }
}
