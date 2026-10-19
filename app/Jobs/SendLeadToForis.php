<?php

namespace App\Jobs;

use App\Support\ForisLeadSync;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Throwable;

class SendLeadToForis implements ShouldQueue
{
    use Queueable;

    public int $timeout = 15;

    public int $tries = 0;

    public function __construct(public int $deliveryId) {}

    public function handle(ForisLeadSync $sync): void
    {
        $delay = $sync->deliver($this->deliveryId);
        if ($delay !== null) {
            $this->release($delay);
        }
    }

    public function failed(?Throwable $exception): void
    {
        DB::table('foris_lead_deliveries')->where('id', $this->deliveryId)
            ->where('status', 'processing')->update([
                'status' => 'uncertain',
                'last_error' => 'The worker stopped before delivery was confirmed. Check Foris by GEIC reference before any retry.',
                'updated_at' => now(),
            ]);
    }
}
