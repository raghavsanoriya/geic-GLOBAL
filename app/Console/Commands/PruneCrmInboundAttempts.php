<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('crm:prune-inbound-attempts')]
#[Description('Remove at most 1000 inbound audit records older than 30 days')]
class PruneCrmInboundAttempts extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $ids = DB::table('crm_inbound_attempts')->where('created_at', '<', now()->subDays(30))->orderBy('id')->limit(1000)->pluck('id');
        $count = DB::table('crm_inbound_attempts')->whereIn('id', $ids)->delete();
        $this->info("Removed {$count} expired inbound audit records. Accepted lead receipts were retained.");

        return self::SUCCESS;
    }
}
