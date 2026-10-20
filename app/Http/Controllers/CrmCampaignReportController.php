<?php

namespace App\Http\Controllers;

use App\Models\CrmCampaign;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmCampaignReportController extends Controller
{
    public const STATES = ['pending' => 'Pending', 'queued' => 'Queued', 'processing' => 'Processing', 'accepted' => 'Provider accepted', 'blocked' => 'Blocked', 'uncertain' => 'Needs reconciliation', 'suppressed' => 'Opted out', 'cancelled' => 'Cancelled'];

    public function index(Request $request): Response|JsonResponse|StreamedResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        Gate::authorize('crm.marketing.manage');
        $filters = $request->validate(['campaign' => ['nullable', 'integer', 'min:1'], 'channel' => ['nullable', Rule::in(['email', 'sms'])],
            'status' => ['nullable', Rule::in(array_keys(self::STATES))], 'from' => ['nullable', 'date_format:Y-m-d'], 'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'format' => ['nullable', Rule::in(['csv', 'print'])], 'page' => ['nullable', 'integer', 'between:1,100000']]);
        $campaigns = CrmCampaign::visibleTo($request->user());
        if ($request->filled('campaign')) {
            $campaigns->whereKey($filters['campaign']);
            abort_unless((clone $campaigns)->exists(), 404);
        }
        if ($request->filled('channel')) {
            $campaigns->where('channel', $filters['channel']);
        }
        $query = DB::table('crm_campaign_recipients as r')->joinSub($campaigns->select('id', 'name', 'channel'), 'c', 'c.id', '=', 'r.campaign_id');
        if ($request->filled('status')) {
            $query->where('r.status', $filters['status']);
        }
        if ($request->filled('from')) {
            $query->where('r.attempted_at', '>=', CarbonImmutable::parse($filters['from'], 'Asia/Kolkata')->startOfDay()->utc());
        }
        if ($request->filled('to')) {
            $query->where('r.attempted_at', '<', CarbonImmutable::parse($filters['to'], 'Asia/Kolkata')->startOfDay()->addDay()->utc());
        }
        $counts = (clone $query)->selectRaw('r.status,COUNT(*) as total')->groupBy('r.status')->pluck('total', 'status')->all();
        $query->select('r.id', 'r.campaign_id', 'r.address', 'r.name as recipient_name', 'r.status', 'r.error_code', 'r.provider_reference', 'r.attempted_at', 'c.name as campaign_name', 'c.channel');
        $headings = ['Reference', 'Campaign', 'Channel', 'Recipient', 'Address', 'Status', 'Attempted (IST)', 'Provider reference', 'Safe error'];
        $row = fn ($record): array => [$record->id, $record->campaign_name, strtoupper($record->channel), $record->recipient_name, $record->address, self::STATES[$record->status] ?? $record->status,
            $record->attempted_at ? CarbonImmutable::parse($record->attempted_at, 'UTC')->timezone('Asia/Kolkata')->format('Y-m-d H:i:s') : 'Not attempted', $record->provider_reference, $record->error_code];
        if ($request->filled('format')) {
            Gate::authorize('crm.export');
            if ($filters['format'] === 'print') {
                abort_if((clone $query)->count() > 1000, 422, 'Narrow the filters to at most 1,000 recipients before printing.');

                return response()->view('admin.crm.print-table', ['title' => 'Campaign recipient report', 'headings' => $headings, 'rows' => $query->orderBy('r.id')->get()->map($row)], 200, ['Cache-Control' => 'private, no-store']);
            }

            return response()->streamDownload(function () use ($query, $row, $headings): void {
                $stream = fopen('php://output', 'wb');
                fputcsv($stream, $headings, ',', '"', '');
                foreach ($query->lazyById(200, 'r.id', 'id') as $record) {
                    fputcsv($stream, array_map(fn ($value) => preg_match('/^[\s]*[=+\-@]|^[\t\r\n]/u', (string) $value) ? "'".$value : (string) $value, $row($record)), ',', '"', '');
                }fclose($stream);
            }, 'geic-campaign-recipients.csv', ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
        }
        $recipients = $query->orderByDesc('r.id')->paginate(30)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json(['counts' => $counts, 'recipients' => $recipients], 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.campaigns.reports', ['counts' => $counts, 'recipients' => $recipients, 'states' => self::STATES,
            'campaignOptions' => CrmCampaign::visibleTo($request->user())->orderByDesc('id')->limit(200)->get(['id', 'name'])], 200, ['Cache-Control' => 'private, no-store']);
    }
}
