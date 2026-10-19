<?php

namespace App\Support;

use App\Jobs\SendLeadToForis;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use InvalidArgumentException;
use Throwable;

class ForisLeadSync
{
    public const STATUSES = ['pending', 'processing', 'delivered', 'duplicate_review', 'failed', 'uncertain'];

    public function __construct(private ForisLeadPayload $payload) {}

    public function configured(): bool
    {
        return config('foris.enabled')
            && filled(config('foris.api_key'))
            && preg_match('~^https://crm\.foriseducation\.com/public/api/v1/forms/submit/[A-Za-z0-9_-]+$~', (string) config('foris.endpoint')) === 1;
    }

    public function register(int $enquiryId, bool $historical = false): bool
    {
        return DB::transaction(function () use ($enquiryId, $historical): bool {
            $inserted = DB::table('foris_lead_deliveries')->insertOrIgnore([
                'enquiry_id' => $enquiryId,
                'is_historical' => $historical,
                'status' => 'pending',
                'attempts' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            if ($inserted && $this->configured()) {
                $this->enqueue((int) DB::table('foris_lead_deliveries')->where('enquiry_id', $enquiryId)->value('id'));
            }

            return (bool) $inserted;
        });
    }

    public function enqueue(int $deliveryId): void
    {
        DB::transaction(function () use ($deliveryId): void {
            $claimed = DB::table('foris_lead_deliveries')->where('id', $deliveryId)
                ->where('status', 'pending')->whereNull('queued_at')
                ->update(['queued_at' => now(), 'updated_at' => now()]);
            if (! $claimed) {
                return;
            }
            $historical = DB::table('foris_lead_deliveries')->where('id', $deliveryId)->value('is_historical');
            Queue::connection('foris')->push(new SendLeadToForis($deliveryId), '', $historical ? 'foris-backfill' : 'foris-live');
        });
    }

    public function enqueuePending(): int
    {
        DB::table('foris_lead_deliveries')->where('status', 'processing')
            ->where('attempted_at', '<', now()->subMinutes(2))->update([
                'status' => 'uncertain',
                'last_error' => 'A worker stopped during delivery. Reconcile the GEIC reference in Foris before retrying.',
                'updated_at' => now(),
            ]);
        if (! $this->configured()) {
            return 0;
        }
        $ids = DB::table('foris_lead_deliveries')->where('status', 'pending')->whereNull('queued_at')
            ->orderBy('is_historical')->orderBy('id')->limit(200)->pluck('id');
        foreach ($ids as $id) {
            $this->enqueue((int) $id);
        }

        return $ids->count();
    }

    public function retry(int $deliveryId): bool
    {
        return DB::transaction(function () use ($deliveryId): bool {
            $updated = DB::table('foris_lead_deliveries')->where('id', $deliveryId)->where('status', 'failed')
                ->update(['status' => 'pending', 'attempts' => 0, 'queued_at' => null, 'last_error' => null, 'updated_at' => now()]);
            if ($updated && $this->configured()) {
                $this->enqueue($deliveryId);
            }

            return (bool) $updated;
        });
    }

    public function deliver(int $deliveryId): ?int
    {
        $delivery = DB::table('foris_lead_deliveries')->find($deliveryId);
        if (! $delivery || $delivery->status !== 'pending') {
            return null;
        }
        if (! $this->configured()) {
            return 300;
        }
        $limit = max(1, min(60, (int) config('foris.rate_limit')));
        if (RateLimiter::tooManyAttempts('foris-api', $limit)) {
            return max(1, RateLimiter::availableIn('foris-api'));
        }
        $lead = DB::table('counselling_enquiries')->find($delivery->enquiry_id);
        if (! $lead) {
            $this->finish($deliveryId, 'failed', ['last_error' => 'The original GEIC enquiry could not be found.']);

            return null;
        }
        try {
            $payload = $this->payload->make($lead);
        } catch (InvalidArgumentException $error) {
            $this->finish($deliveryId, 'failed', ['last_error' => $error->getMessage()]);

            return null;
        }
        $claimed = DB::table('foris_lead_deliveries')->where('id', $deliveryId)->where('status', 'pending')
            ->update(['status' => 'processing', 'attempts' => DB::raw('attempts + 1'), 'attempted_at' => now(), 'updated_at' => now()]);
        if (! $claimed) {
            return null;
        }
        RateLimiter::hit('foris-api', 60);

        try {
            $response = Http::acceptJson()->asJson()->withoutRedirecting()
                ->withHeaders(['X-API-KEY' => config('foris.api_key'), 'Origin' => 'https://geic.in'])
                ->connectTimeout(3)->timeout(10)->post(config('foris.endpoint'), $payload);
        } catch (ConnectionException $error) {
            if (preg_match('/cURL error [67]:/', $error->getMessage()) === 1) {
                return $this->temporaryFailure($deliveryId, (int) $delivery->attempts + 1, 'Foris could not be reached before the request was sent.');
            }
            $this->finish($deliveryId, 'uncertain', ['last_error' => 'Delivery timed out or the connection ended without confirmation. Check Foris using GEIC-'.$lead->id.'.']);

            return null;
        } catch (Throwable $error) {
            $this->finish($deliveryId, 'uncertain', ['last_error' => 'Delivery ended without a confirmed response. Check the Foris audit trail before retrying.']);
            Log::warning('Foris delivery ended without confirmation.', ['delivery_id' => $deliveryId]);

            return null;
        }

        $body = $response->json();
        $body = is_array($body) ? $body : [];
        $status = $body['status'] ?? $body['action'] ?? data_get($body, 'data.status', '');
        $status = is_string($status) ? strtolower($status) : '';
        $message = is_string($body['message'] ?? null) ? strtolower($body['message']) : '';
        $references = [
            'remote_lead_id' => $this->reference($body['lead_id'] ?? data_get($body, 'data.lead_id')),
            'remote_submission_id' => $this->reference($body['submission_id'] ?? $body['queue_id'] ?? data_get($body, 'data.submission_id')),
            'last_http_status' => $response->status(),
            'last_error' => null,
        ];
        $queued = in_array($status, ['duplicate_queue', 'duplicate_review', 'queued', 'queued_for_review'], true)
            || (($body['success'] ?? false) === true && str_contains($message, 'duplicate') && str_contains($message, 'review'));
        if ($queued && in_array($response->status(), [200, 201, 202, 409], true)) {
            $this->finish($deliveryId, 'duplicate_review', $references + ['delivered_at' => now()]);

            return null;
        }
        if ($response->successful() && (($body['success'] ?? false) === true || $status === 'success')) {
            $this->finish($deliveryId, 'delivered', $references + ['delivered_at' => now()]);

            return null;
        }
        if ($response->status() === 429) {
            return $this->temporaryFailure($deliveryId, (int) $delivery->attempts + 1, 'Foris rate limit reached.', 429, (int) $response->header('Retry-After'));
        }
        if ($response->status() >= 500 || $response->successful()) {
            $this->finish($deliveryId, 'uncertain', ['last_http_status' => $response->status(), 'last_error' => 'Foris returned no reliable acknowledgement. Check the GEIC reference in its audit trail before retrying.']);

            return null;
        }
        $this->finish($deliveryId, 'failed', [
            'last_http_status' => $response->status(),
            'last_error' => in_array($response->status(), [401, 403], true)
                ? 'Foris authentication or allowed-origin check failed. Verify the protected integration configuration.'
                : 'Foris rejected this submission (HTTP '.$response->status().'). Review the lead details and CRM audit trail.',
        ]);

        return null;
    }

    private function temporaryFailure(int $id, int $attempts, string $message, ?int $httpStatus = null, int $retryAfter = 0): ?int
    {
        $this->finish($id, $attempts >= 8 ? 'failed' : 'pending', ['last_error' => $message, 'last_http_status' => $httpStatus]);
        if ($attempts >= 8) {
            return null;
        }

        return min(3600, max($retryAfter, 60 * (2 ** min(5, $attempts - 1))));
    }

    /** @param array<string, mixed> $attributes */
    private function finish(int $id, string $status, array $attributes = []): void
    {
        DB::table('foris_lead_deliveries')->where('id', $id)->update($attributes + ['status' => $status, 'updated_at' => now()]);
    }

    private function reference(mixed $value): ?string
    {
        return is_scalar($value) ? mb_substr((string) $value, 0, 100) : null;
    }
}
