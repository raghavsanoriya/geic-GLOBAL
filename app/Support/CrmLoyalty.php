<?php

namespace App\Support;

use App\Models\CrmLoyaltyMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CrmLoyalty
{
    public const MILESTONES = ['application_created' => 'Application created', 'visa_approved' => 'Visa approved', 'invoice_paid' => 'Invoice paid in full'];

    public function adjust(User $actor, int $memberId, int $points, string $key, string $reason): int
    {
        return DB::transaction(function () use ($actor, $memberId, $points, $key, $reason): int {
            $member = CrmLoyaltyMember::visibleTo($actor)->whereKey($memberId)->lockForUpdate()->firstOrFail();
            $existing = DB::table('crm_loyalty_points')->where('event_key', 'manual:'.$key)->first();
            if ($existing) {
                abort_unless((int) $existing->member_id === $member->id && (int) $existing->actor_id === $actor->id, 409);

                return (int) $existing->id;
            }
            if (! $member->is_active) {
                throw ValidationException::withMessages(['points' => 'Activate the member before changing points.']);
            }
            $balance = (int) DB::table('crm_loyalty_points')->where('member_id', $member->id)->sum('points');
            if ($points === 0 || $balance + $points < 0) {
                throw ValidationException::withMessages(['points' => 'Use a nonzero adjustment that does not make the balance negative.']);
            }
            $id = DB::table('crm_loyalty_points')->insertGetId(['member_id' => $member->id, 'actor_id' => $actor->id, 'event_key' => 'manual:'.$key, 'points' => $points, 'reason' => $reason, 'created_at' => now()]);
            $this->event($member->id, $actor->id, 'points.adjusted', ['ledger:'.$id]);

            return $id;
        });
    }

    public function awardForStudent(int $studentId, string $milestone, string $reference): int
    {
        if (! config('crm.enabled') || ! array_key_exists($milestone, self::MILESTONES)) {
            return 0;
        }
        $memberId = DB::table('crm_loyalty_referrals')->where('student_id', $studentId)->value('member_id');
        if (! $memberId) {
            return 0;
        }

        return DB::transaction(function () use ($memberId, $milestone, $reference): int {
            $member = CrmLoyaltyMember::whereKey($memberId)->lockForUpdate()->first();
            if (! $member?->is_active) {
                return 0;
            }
            $awards = 0;
            foreach (DB::table('crm_loyalty_rules')->where('milestone', $milestone)->where('is_enabled', true)->get() as $rule) {
                $inserted = DB::table('crm_loyalty_points')->insertOrIgnore(['member_id' => $member->id, 'actor_id' => null,
                    'event_key' => 'rule:'.$rule->id.':'.$milestone.':'.$reference, 'points' => $rule->points, 'reason' => $rule->name.' · '.$reference, 'created_at' => now()]);
                if ($inserted) {
                    $awards++;
                    $this->event($member->id, null, 'points.awarded', ['rule:'.$rule->id, 'milestone:'.$milestone]);
                }
            }

            return $awards;
        });
    }

    public function event(?int $memberId, ?int $actorId, string $action, array $fields): void
    {
        DB::table('crm_loyalty_events')->insert(['member_id' => $memberId, 'actor_id' => $actorId, 'action' => $action, 'fields' => json_encode($fields, JSON_THROW_ON_ERROR), 'created_at' => now()]);
    }
}
