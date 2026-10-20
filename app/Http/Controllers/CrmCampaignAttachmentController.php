<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmCampaignAttachmentRequest;
use App\Models\CrmCampaign;
use App\Support\CrmCampaigns;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmCampaignAttachmentController extends Controller
{
    public function store(CrmCampaignAttachmentRequest $request, int $campaign, CrmCampaigns $events): JsonResponse|RedirectResponse
    {
        $file = $request->file('file');
        $hash = hash_file('sha256', $file->getRealPath());
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file->getRealPath());
        $id = DB::transaction(function () use ($request, $campaign, $file, $hash, $mime, $events): int {
            $record = CrmCampaign::visibleTo($request->user())->whereKey($campaign)->lockForUpdate()->firstOrFail();
            abort_unless($record->status === 'draft' && $record->channel === 'email', 409, 'Only draft email campaigns can change attachments.');
            $existing = DB::table('crm_campaign_attachments')->where('creation_key', $request->validated('creation_key'))->first();
            if ($existing) {
                abort_unless((int) $existing->campaign_id === $campaign && hash_equals($existing->sha256, $hash), 409);

                return (int) $existing->id;
            }
            abort_unless($record->version === (int) $request->validated('version'), 409, 'This campaign changed. Reload before uploading.');
            abort_if(DB::table('crm_campaign_attachments')->where('campaign_id', $campaign)->where('is_active', true)->count() >= 3, 422, 'A campaign can have at most three active attachments.');
            $bytes = (int) DB::table('crm_campaign_attachments')->where('campaign_id', $campaign)->where('is_active', true)->sum('bytes');
            abort_if($bytes + $file->getSize() > 10485760, 422, 'Keep total campaign attachments below 10 MB.');
            $extension = ['application/pdf' => 'pdf', 'image/png' => 'png', 'image/jpeg' => 'jpg'][$mime] ?? null;
            abort_unless($extension, 422);
            $path = $campaign.'/'.Str::uuid().'.'.$extension;
            abort_unless(Storage::disk('crm_campaign_attachments')->putFileAs((string) $campaign, $file, basename($path), ['visibility' => 'private']), 503, 'Attachment storage is unavailable.');
            $id = DB::table('crm_campaign_attachments')->insertGetId(['campaign_id' => $campaign, 'uploaded_by' => $request->user()->id, 'creation_key' => $request->validated('creation_key'),
                'label' => $request->validated('label'), 'path' => $path, 'mime' => $mime, 'bytes' => $file->getSize(), 'sha256' => $hash, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
            $record->version++;
            $record->save();
            $events->record($record, 'attachment.added', $request->user());

            return $id;
        });

        return $request->expectsJson() ? response()->json(['attachment_id' => $id, 'message' => 'Campaign attachment saved.'], 200, ['Cache-Control' => 'private, no-store'])
            : redirect()->route('admin.crm.campaigns.show', $campaign)->with('status', 'Campaign attachment saved.');
    }

    public function retire(Request $request, int $campaign, int $attachment, CrmCampaigns $events): JsonResponse|RedirectResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        Gate::authorize('crm.marketing.manage');
        $data = $request->validate(['version' => ['required', 'integer', 'min:1']]);
        DB::transaction(function () use ($request, $campaign, $attachment, $data, $events): void {
            $record = CrmCampaign::visibleTo($request->user())->whereKey($campaign)->lockForUpdate()->firstOrFail();
            abort_unless($record->status === 'draft' && $record->version === (int) $data['version'], 409, 'Only a current draft can change attachments.');
            $file = DB::table('crm_campaign_attachments')->where('campaign_id', $campaign)->where('id', $attachment)->first();
            abort_unless($file, 404);
            DB::table('crm_campaign_attachments')->where('id', $attachment)->update(['is_active' => false, 'updated_at' => now()]);
            $record->version++;
            $record->save();
            $events->record($record, 'attachment.retired', $request->user());
        });

        return $request->expectsJson() ? response()->json(['message' => 'Attachment retired.']) : redirect()->route('admin.crm.campaigns.show', $campaign)->with('status', 'Attachment retired; history retained.');
    }

    public function download(Request $request, int $campaign, int $attachment): StreamedResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        Gate::authorize('crm.marketing.manage');
        CrmCampaign::visibleTo($request->user())->findOrFail($campaign);
        $file = DB::table('crm_campaign_attachments')->where('campaign_id', $campaign)->where('id', $attachment)->where('is_active', true)->first();
        abort_unless($file && preg_match('/^'.$campaign.'\/[a-f0-9-]{36}\.(pdf|jpg|png)$/D', $file->path), 404);
        abort_unless(Storage::disk('crm_campaign_attachments')->exists($file->path), 404);

        return Storage::disk('crm_campaign_attachments')->download($file->path, 'GEIC-campaign-attachment-'.$file->id.'.'.pathinfo($file->path, PATHINFO_EXTENSION),
            ['Content-Type' => 'application/octet-stream', 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store']);
    }
}
