<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmIntegrationSettingRequest;
use App\Models\CrmIntegrationSetting;
use App\Support\CrmProviderTest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class CrmIntegrationSettingController extends Controller
{
    public function show(Request $request, string $provider): Response|JsonResponse
    {
        $this->authorizeProvider($provider);
        $setting = CrmIntegrationSetting::where('provider', $provider)->first();
        if ($request->expectsJson()) {
            return response()->json(['data' => $setting, 'has_secret' => filled($setting?->secret), 'ready' => $setting?->ready() ?? false], 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.integrations.settings', ['setting' => $setting, 'provider' => $provider, 'providers' => CrmIntegrationSetting::PROVIDERS], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function update(CrmIntegrationSettingRequest $request, string $provider): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $setting = DB::transaction(function () use ($request, $provider, $data): CrmIntegrationSetting {
            DB::table('users')->where('id', $request->user()->id)->lockForUpdate()->first();
            $setting = CrmIntegrationSetting::where('provider', $provider)->lockForUpdate()->first();
            abort_unless(($setting?->version ?? 0) === (int) $data['version'], 409, 'These settings changed. Reload before saving.');
            $setting ??= new CrmIntegrationSetting(['provider' => $provider, 'version' => 0, 'is_enabled' => false]);
            $oldFingerprint = $setting->exists ? $setting->fingerprint() : null;
            $setting->configuration = $data['configuration'];
            if ($request->boolean('clear_secret')) {
                $setting->secret = null;
            } elseif (filled($data['secret'] ?? null)) {
                $setting->secret = $data['secret'];
            }
            if ($oldFingerprint !== $setting->fingerprint()) {
                $setting->verified_at = null;
                $setting->verified_fingerprint = null;
                $setting->last_test_result = null;
            }
            if ($request->boolean('is_enabled') && (! $setting->verified_at || ! $setting->verified_fingerprint || ! hash_equals($setting->verified_fingerprint, $setting->fingerprint()))) {
                throw ValidationException::withMessages(['is_enabled' => 'Save with sending disabled, then pass a connection test before enabling.']);
            }
            $setting->is_enabled = $request->boolean('is_enabled');
            $setting->version++;
            $setting->save();
            $this->event($setting, $request->user()->id, 'configuration.saved');

            return $setting;
        });
        if ($request->expectsJson()) {
            return response()->json(['data' => $setting, 'has_secret' => filled($setting->secret)], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.integrations.show', $provider)->with('status', 'Settings saved. Secret values are not shown.');
    }

    public function test(Request $request, string $provider, CrmProviderTest $test): RedirectResponse|JsonResponse
    {
        $this->authorizeProvider($provider);
        $data = $request->validate(['version' => ['required', 'integer', 'min:1']]);
        $setting = CrmIntegrationSetting::where('provider', $provider)->firstOrFail();
        abort_unless($setting->version === (int) $data['version'], 409, 'Reload settings before testing.');
        $fingerprint = $setting->fingerprint();
        $passed = $test->verify($setting);
        $setting = DB::transaction(function () use ($request, $setting, $fingerprint, $passed): CrmIntegrationSetting {
            $record = CrmIntegrationSetting::whereKey($setting->id)->lockForUpdate()->firstOrFail();
            abort_unless($record->version === $setting->version && hash_equals($record->fingerprint(), $fingerprint), 409, 'Settings changed during the test. Retry with the current configuration.');
            $record->verified_at = $passed ? now() : null;
            $record->verified_fingerprint = $passed ? $fingerprint : null;
            $record->last_test_result = $passed ? 'Connection verified. No campaign was sent.' : 'Connection could not be verified. Check the credentials, provider account and network configuration.';
            if (! $passed) {
                $record->is_enabled = false;
            }$record->version++;
            $record->save();
            $this->event($record, $request->user()->id, $passed ? 'connection.verified' : 'connection.failed');

            return $record;
        });
        if ($request->expectsJson()) {
            return response()->json(['passed' => $passed, 'message' => $setting->last_test_result, 'data' => $setting], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.integrations.show', $provider)->with('status', $setting->last_test_result);
    }

    private function authorizeProvider(string $provider): void
    {
        abort_unless(config('crm.enabled') && array_key_exists($provider, CrmIntegrationSetting::PROVIDERS), 404);
        Gate::authorize('crm.integrations.manage');
    }

    private function event(CrmIntegrationSetting $setting, int $actor, string $action): void
    {
        DB::table('crm_integration_events')->insert(['setting_id' => $setting->id, 'actor_id' => $actor, 'action' => $action, 'created_at' => now()]);
    }
}
