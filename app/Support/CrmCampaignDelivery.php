<?php

namespace App\Support;

use App\Models\CrmCampaign;
use App\Models\CrmIntegrationSetting;
use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CrmCampaignDelivery
{
    public function __construct(private CrmProviderTest $providers) {}

    public function token(int $recipientId): string
    {
        return hash_hmac('sha256', 'geic-marketing-optout:'.$recipientId, (string) config('app.key'));
    }

    public function deliver(int $recipientId): void
    {
        if (! config('crm.enabled') || ! config('crm.campaigns_enabled')) {
            DB::table('crm_campaign_recipients')->where('id', $recipientId)->where('status', 'queued')->update(['status' => 'pending', 'error_code' => 'delivery_disabled']);

            return;
        }
        $recipient = DB::table('crm_campaign_recipients')->find($recipientId);
        if (! $recipient || ! in_array($recipient->status, ['pending', 'queued'], true)) {
            return;
        }
        $campaign = CrmCampaign::find($recipient->campaign_id);
        $actor = $campaign ? User::find($campaign->created_by) : null;
        if (! $campaign || $campaign->status !== 'scheduled' || ! $campaign->consent_confirmed || ! $campaign->scheduled_at || $campaign->scheduled_at->isFuture()
            || ! $actor || ! $actor->hasAdminPermission('crm.marketing.manage') || ! $actor->hasAdminPermission('crm.view')
            || ($recipient->student_id && ! CrmStudent::visibleTo($actor)->whereKey($recipient->student_id)->where('is_active', true)->exists())) {
            $this->pendingStatus($recipientId, 'cancelled', 'access_or_campaign_changed');

            return;
        }
        if (DB::table('crm_marketing_suppressions')->where('channel', $campaign->channel)->where('address', $recipient->address)->exists()) {
            $this->pendingStatus($recipientId, 'suppressed', 'recipient_opted_out');

            return;
        }
        $provider = CrmIntegrationSetting::where('provider', $campaign->channel)->first();
        if ($campaign->channel !== 'email' || ! $provider?->ready()) {
            $this->pendingStatus($recipientId, 'blocked', 'provider_not_configured');

            return;
        }
        try {
            $mailer = $this->providers->mailer($provider);
            $attachments = [];
            foreach (DB::table('crm_campaign_attachments')->where('campaign_id', $campaign->id)->where('is_active', true)->get() as $file) {
                if (! preg_match('/^'.$campaign->id.'\/[a-f0-9-]{36}\.(pdf|jpg|png)$/D', $file->path) || $file->bytes > 5242880) {
                    throw new \RuntimeException('Invalid attachment.');
                }
                $contents = Storage::disk('crm_campaign_attachments')->get($file->path);
                if (! is_string($contents) || strlen($contents) !== (int) $file->bytes || ! hash_equals($file->sha256, hash('sha256', $contents))) {
                    throw new \RuntimeException('Attachment integrity failed.');
                }
                $attachments[] = ['bytes' => $contents, 'name' => 'GEIC-attachment-'.$file->id.'.'.pathinfo($file->path, PATHINFO_EXTENSION), 'mime' => $file->mime];
            }
        } catch (Throwable) {
            $this->pendingStatus($recipientId, 'blocked', 'provider_or_attachment_unavailable');

            return;
        }
        $claimed = DB::transaction(function () use ($recipient, $campaign, $provider): bool {
            $current = CrmCampaign::whereKey($campaign->id)->lockForUpdate()->first();
            $configuration = CrmIntegrationSetting::whereKey($provider->id)->lockForUpdate()->first();
            if (! config('crm.campaigns_enabled') || $current?->status !== 'scheduled' || ! $configuration?->ready()
                || ! hash_equals($configuration->fingerprint(), $provider->fingerprint())) {
                return false;
            }

            return DB::table('crm_campaign_recipients')->where('id', $recipient->id)->whereIn('status', ['pending', 'queued'])
                ->update(['status' => 'processing', 'attempted_at' => now(), 'error_code' => null]) === 1;
        });
        if (! $claimed) {
            return;
        }
        $url = route('crm.marketing.unsubscribe', ['recipient' => $recipientId, 'token' => $this->token($recipientId)]);
        try {
            $sent = $mailer->raw($campaign->body."\n\nGEIC Indore\nStop marketing messages: ".$url,
                function (Message $message) use ($recipient, $campaign, $provider, $url, $attachments): void {
                    $message->to($recipient->address, $recipient->name ?: null)->from($provider->configuration['from_address'], $provider->configuration['from_name'])->subject($campaign->subject);
                    $headers = $message->getSymfonyMessage()->getHeaders();
                    $headers->addTextHeader('List-Unsubscribe', '<'.$url.'>');
                    $headers->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
                    foreach ($attachments as $attachment) {
                        $message->attachData($attachment['bytes'], $attachment['name'], ['mime' => $attachment['mime']]);
                    }
                });
            DB::table('crm_campaign_recipients')->where('id', $recipientId)->where('status', 'processing')->update([
                'status' => $sent ? 'accepted' : 'uncertain', 'provider_reference' => $sent ? mb_substr($sent->getMessageId(), 0, 250) : null,
                'error_code' => $sent ? null : 'delivery_not_confirmed']);
        } catch (Throwable) {
            DB::table('crm_campaign_recipients')->where('id', $recipientId)->where('status', 'processing')->update(['status' => 'uncertain', 'error_code' => 'delivery_confirmation_required']);
        }
    }

    private function pendingStatus(int $id, string $status, string $reason): void
    {
        DB::table('crm_campaign_recipients')->where('id', $id)->whereIn('status', ['pending', 'queued'])->update(['status' => $status, 'error_code' => $reason]);
    }
}
