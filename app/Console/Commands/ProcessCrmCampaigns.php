<?php

namespace App\Console\Commands;

use App\Jobs\DeliverCrmCampaignRecipient;
use App\Models\CrmCampaign;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProcessCrmCampaigns extends Command
{
    protected $signature = 'crm:process-campaigns {--limit=20}';

    protected $description = 'Queue a bounded set of consented campaign recipients; never resend uncertain attempts';

    public function handle(): int
    {
        if (! config('crm.enabled') || ! config('crm.campaigns_enabled')) {
            $this->info('Campaign delivery is disabled; no messages were queued.');

            return self::SUCCESS;
        }
        $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100]]);
        if ($limit === false) {
            $this->error('Use --limit=1..100.');

            return self::FAILURE;
        }
        $lock = Cache::lock('crm-campaign-dispatch', 60);
        if (! $lock->get()) {
            return self::SUCCESS;
        }
        $count = 0;
        try {
            DB::table('crm_campaign_recipients')->where('status', 'processing')->where('attempted_at', '<', now()->subMinutes(2))
                ->update(['status' => 'uncertain', 'error_code' => 'worker_interrupted_reconcile']);
            $ids = DB::table('crm_campaign_recipients')->where('status', 'pending')
                ->whereIn('campaign_id', CrmCampaign::where('status', 'scheduled')->where('consent_confirmed', true)->where('scheduled_at', '<=', now())->select('id'))
                ->orderBy('id')->limit($limit)->pluck('id');
            foreach ($ids as $id) {
                DB::transaction(function () use ($id, &$count): void {
                    if (DB::table('crm_campaign_recipients')->where('id', $id)->where('status', 'pending')->update(['status' => 'queued']) === 1) {
                        Bus::dispatch(new DeliverCrmCampaignRecipient((int) $id));
                        $count++;
                    }
                });
            }
            foreach (CrmCampaign::where('status', 'scheduled')->where('scheduled_at', '<=', now())->orderBy('id')->limit(50)->get() as $campaign) {
                if (! DB::table('crm_campaign_recipients')->where('campaign_id', $campaign->id)->whereIn('status', ['pending', 'queued', 'processing'])->exists()) {
                    $review = DB::table('crm_campaign_recipients')->where('campaign_id', $campaign->id)->whereIn('status', ['blocked', 'uncertain'])->exists();
                    CrmCampaign::whereKey($campaign->id)->where('status', 'scheduled')->update(['status' => $review ? 'needs_review' : 'completed', 'completed_at' => now(), 'version' => DB::raw('version+1')]);
                }
            }
        } finally {
            $lock->release();
        }
        $this->info("Queued {$count} campaign recipients. SMTP acceptance does not confirm inbox delivery.");

        return self::SUCCESS;
    }
}
