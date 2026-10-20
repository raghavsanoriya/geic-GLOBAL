<?php

namespace App\Jobs;

use App\Support\CrmCampaignDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeliverCrmCampaignRecipient implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 25;

    public function __construct(public int $recipientId)
    {
        $this->onConnection('crm_campaigns');
        $this->onQueue('crm-campaigns');
    }

    public function handle(CrmCampaignDelivery $delivery): void
    {
        $delivery->deliver($this->recipientId);
    }

    public function failed(?Throwable $exception): void
    {
        DB::table('crm_campaign_recipients')->where('id', $this->recipientId)->where('status', 'queued')
            ->update(['status' => 'pending', 'error_code' => 'worker_stopped_before_delivery']);
        DB::table('crm_campaign_recipients')->where('id', $this->recipientId)->where('status', 'processing')
            ->update(['status' => 'uncertain', 'error_code' => 'worker_interrupted_reconcile']);
    }
}
