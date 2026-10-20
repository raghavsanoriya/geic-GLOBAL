<?php

namespace App\Http\Controllers;

use App\Support\CrmCampaignDelivery;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CrmMarketingUnsubscribeController extends Controller
{
    public function handle(Request $request, int $recipient, string $token, CrmCampaignDelivery $delivery): Response
    {
        abort_unless(config('crm.enabled') && hash_equals($delivery->token($recipient), $token), 404);
        $record = DB::table('crm_campaign_recipients as recipient')->join('crm_campaigns as campaign', 'campaign.id', '=', 'recipient.campaign_id')
            ->where('recipient.id', $recipient)->first(['recipient.id', 'recipient.address', 'campaign.channel']);
        abort_unless($record, 404);
        if ($request->isMethod('POST')) {
            DB::transaction(function () use ($record): void {
                DB::table('crm_marketing_suppressions')->updateOrInsert(['channel' => $record->channel, 'address' => $record->address], ['reason' => 'Recipient opted out', 'created_at' => now(), 'updated_at' => now()]);
                DB::table('crm_campaign_recipients')->where('address', $record->address)->whereIn('status', ['pending', 'queued'])->whereIn('campaign_id', DB::table('crm_campaigns')->where('channel', $record->channel)->select('id'))
                    ->update(['status' => 'suppressed', 'error_code' => 'recipient_opted_out']);
            });
        }

        return response()->view('admin.crm.campaigns.unsubscribe', ['done' => $request->isMethod('POST'), 'recipient' => $recipient, 'token' => $token], 200, ['Cache-Control' => 'private, no-store', 'Referrer-Policy' => 'no-referrer']);
    }
}
