<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmIntegrationSetting extends Model
{
    public const PROVIDERS = ['email' => 'Email / SMTP', 'onesignal' => 'OneSignal'];

    protected $fillable = ['provider', 'configuration', 'secret', 'is_enabled', 'verified_fingerprint', 'verified_at', 'last_test_result', 'version'];

    protected $hidden = ['secret', 'verified_fingerprint'];

    protected function casts(): array
    {
        return ['configuration' => 'array', 'secret' => 'encrypted', 'is_enabled' => 'boolean', 'verified_at' => 'immutable_datetime', 'version' => 'integer'];
    }

    public function fingerprint(): string
    {
        return hash_hmac('sha256', json_encode([$this->provider, $this->configuration, $this->secret], JSON_THROW_ON_ERROR), (string) config('app.key'));
    }

    public function ready(): bool
    {
        return $this->is_enabled && $this->verified_at && $this->verified_fingerprint && hash_equals($this->verified_fingerprint, $this->fingerprint());
    }
}
