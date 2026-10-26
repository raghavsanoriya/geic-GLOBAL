<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmImport extends Model
{
    public const FINANCIAL_TYPES = ['invoice', 'receipt', 'instalment'];

    public const LOYALTY_TYPES = ['loyalty_member', 'loyalty_referral', 'loyalty_points'];

    public const REFERENCE_KINDS = ['country', 'university', 'campus', 'agent', 'qualification', 'category', 'reference', 'application_status', 'visa_status', 'level_status'];

    public const TYPES = ['lead' => 'Leads', 'contact' => 'Business contacts', 'program' => 'Program catalogue', 'invoice' => 'Historical invoices', 'receipt' => 'Historical receipts / reversals', 'instalment' => 'Historical instalments',
        'application' => 'Applications', 'visa' => 'Visa records', 'follow_up' => 'Follow-ups', 'education' => 'Education',
        'employment' => 'Employment', 'test_score' => 'Test scores', 'preference' => 'Study preferences',
        'loyalty_member' => 'Historical loyalty members', 'loyalty_referral' => 'Historical loyalty referrals', 'loyalty_points' => 'Historical loyalty points',
        'reference' => 'Reference dictionaries', 'task' => 'Historical tasks'];

    public const TIMESTAMP_PATTERN = '/^\d{4}-\d{2}-\d{2}(?:[T ]\d{2}:\d{2}(?::\d{2}(?:\.\d{1,6})?)?(?:Z|[+-]\d{2}:\d{2})?)?$/D';

    public static function writePermission(string $type): string
    {
        if (in_array($type, self::LOYALTY_TYPES, true)) {
            return 'crm.loyalty.manage';
        }

        return in_array($type, self::FINANCIAL_TYPES, true) ? 'crm.finance.manage' : (in_array($type, ['program', 'reference'], true) ? 'crm.configure' : ($type === 'lead' ? 'crm.imports.manage' : 'crm.manage'));
    }

    public static function viewPermission(string $type): string
    {
        if ($type === 'reference') {
            return 'crm.configure';
        }

        return in_array($type, self::LOYALTY_TYPES, true) ? 'crm.loyalty.view' : (in_array($type, self::FINANCIAL_TYPES, true) ? 'crm.finance.view' : 'crm.view');
    }

    public static function fields(string $type): array
    {
        if ($type === 'lead') {
            return self::FIELDS + ['foris_lead_id' => 'Optional confirmed Foris lead ID (reference-first existing GEIC link)',
                'stage' => 'Original current stage (lead/application/visa/billing; new profiles only, no inferred transition history)',
                'is_active' => 'Original active state (0/1; blank defaults active, new profiles only)'];
        }
        $base = ['source_id' => 'Stable source record ID', 'submitted_at' => 'Original creation date (ISO 8601)'];
        if ($type === 'reference') {
            return $base + ['kind' => 'Dictionary kind (GEIC code)', 'code' => 'Stable GEIC dictionary code', 'name' => 'Original name',
                'parent_code' => 'Parent university/level GEIC code (campus/level_status only)', 'country' => 'Country', 'description' => 'Description',
                'outcome' => 'Original outcome for application/visa status (progress/success/failure)', 'is_active' => 'Original active state (0/1; required)'];
        }
        if ($type === 'loyalty_points') {
            return $base + ['member_source_id' => 'Parent loyalty-member source ID', 'member_geic_id' => 'Or explicit GEIC member ID',
                'points' => 'Original signed integer points', 'reason' => 'Original ledger reason'];
        }
        if (in_array($type, ['receipt', 'instalment'], true)) {
            $base += ['invoice_source_id' => 'Parent invoice source ID (same source name)', 'invoice_geic_id' => 'Or explicit GEIC invoice ID',
                'currency' => 'Original currency (must match invoice)', 'amount' => 'Original amount (decimal text)'];

            return $base + ($type === 'instalment' ? ['due_on' => 'Original due date (YYYY-MM-DD)'] : [
                'paid_on' => 'Original payment date (YYYY-MM-DD)', 'reference' => 'Original receipt reference',
                'instalment_source_id' => 'Optional instalment source ID', 'instalment_geic_id' => 'Or explicit GEIC instalment ID',
                'reversal_source_id' => 'Original positive receipt source ID (for a negative reversal)', 'reversal_geic_id' => 'Or original GEIC positive receipt ID',
                'reason' => 'Original reversal reason (required for reversals)']);
        }
        if ($type === 'visa') {
            return $base + ['application_source_id' => 'Parent application source ID (same source name)',
                'application_geic_id' => 'Or existing GEIC application ID', 'status_code' => 'Configured GEIC visa status code',
                'visa_type' => 'Visa type', 'lodged_on' => 'Lodged on (YYYY-MM-DD)', 'decision_on' => 'Decision on (YYYY-MM-DD)',
                'admission_on' => 'Admission on (YYYY-MM-DD)', 'census_on' => 'Census on (YYYY-MM-DD)',
                'ready_to_bill' => 'Original ready-to-bill value (0/1; required)', 'remark' => 'Original remark'];
        }
        if ($type === 'program') {
            return $base + ['code' => 'GEIC program code (optional; otherwise generated)', 'name' => 'Program name', 'country' => 'Country',
                'university_code' => 'Configured GEIC university code', 'campus_code' => 'Configured GEIC campus code', 'qualification_code' => 'Configured GEIC qualification code',
                'study_level' => 'Study level', 'subject' => 'Subject', 'ranking' => 'Ranking', 'ielts' => 'IELTS requirement', 'toefl' => 'TOEFL legacy 0–120 requirement',
                'pte' => 'PTE requirement', 'description' => 'Description', 'is_active' => 'Active (0/1; blank defaults active)'];
        }
        if ($type === 'contact') {
            foreach (CrmContact::FIELDS as $field => [$label]) {
                $base[$field] = $label;
            }

            return $base + ['is_active' => 'Active (0/1; blank defaults active)'];
        }
        $base += ['student_source_id' => 'Student source ID (same source name as lead import)', 'student_geic_id' => 'Or existing GEIC student ID'];
        if ($type === 'task') {
            return $base + ['title' => 'Original task title', 'description' => 'Original task description (plain text)',
                'status' => 'Original state (new/in_progress/pending/completed/cancelled)', 'priority' => 'Priority (low/normal/high/urgent)',
                'starts_at' => 'Original start timestamp', 'due_at' => 'Original due timestamp', 'completed_at' => 'Original completed timestamp (required when completed)',
                'assignee_geic_ids' => 'Optional mapped GEIC staff IDs separated by semicolons', 'remark' => 'Original remark'];
        }
        if ($type === 'loyalty_member') {
            return $base + ['name' => 'Original member name', 'is_active' => 'Original active state (0/1; required)'];
        }
        if ($type === 'loyalty_referral') {
            return $base + ['member_source_id' => 'Referring loyalty-member source ID', 'member_geic_id' => 'Or explicit GEIC member ID'];
        }
        if ($type === 'invoice') {
            return $base + ['application_source_id' => 'Optional parent application source ID', 'application_geic_id' => 'Or explicit GEIC application ID',
                'number' => 'Original invoice number (unique)', 'bill_to' => 'Original bill-to name', 'description' => 'Original description',
                'currency' => 'Currency (ISO code)', 'base_amount' => 'Base amount (decimal text)', 'tax_bps' => 'Tax rate in basis points (1800 = 18%)',
                'tax_amount' => 'Original tax amount (decimal text)', 'total_amount' => 'Original total amount (decimal text)',
                'status' => 'Original state (draft/issued/void)', 'due_on' => 'Due date (YYYY-MM-DD)', 'issued_at' => 'Original issued timestamp',
                'voided_at' => 'Original voided timestamp', 'void_reason' => 'Original void reason'];
        }
        if ($type === 'application') {
            return $base + ['university_code' => 'Configured GEIC university code', 'campus_code' => 'Configured GEIC campus code',
                'agent_code' => 'Configured GEIC agent code', 'program_code' => 'Optional GEIC program code',
                'status_code' => 'Configured GEIC application status code', 'course' => 'Course', 'intake' => 'Intake',
                'submitted_on' => 'Application submitted on (YYYY-MM-DD)', 'remark' => 'Original remark'];
        }

        return $base + ($type === 'follow_up' ? ['subject' => 'Subject', 'channel' => 'Channel (phone/whatsapp/email/meeting/other)',
            'status' => 'Status (new/pending/ended)', 'due_at' => 'Due at (ISO 8601)', 'ended_at' => 'Ended at (required for ended status)',
            'owner_geic_id' => 'Optional mapped GEIC owner ID (blank stays unassigned)', 'remark' => 'Original remark']
            : ((CrmProfileEntry::FIELDS[$type] ?? []) + ['is_active' => 'Active (0/1; blank defaults active)']));
    }

    public static function requiredFields(string $type): array
    {
        return ['source_id', ...match ($type) {
            'lead' => ['full_name', 'phone'],
            'contact' => ['name', 'contact_type'],
            'program' => ['name', 'country', 'university_code', 'study_level', 'subject'],
            'application' => ['university_code', 'status_code', 'course', 'intake'],
            'visa' => ['status_code', 'visa_type', 'ready_to_bill'],
            'invoice' => ['submitted_at', 'number', 'bill_to', 'description', 'currency', 'base_amount', 'tax_bps', 'tax_amount', 'total_amount', 'status', 'due_on'],
            'receipt' => ['submitted_at', 'currency', 'amount', 'paid_on', 'reference'],
            'instalment' => ['submitted_at', 'currency', 'amount', 'due_on'],
            'loyalty_member' => ['submitted_at', 'name', 'is_active'],
            'loyalty_referral' => ['submitted_at'],
            'loyalty_points' => ['submitted_at', 'points', 'reason'],
            'reference' => ['submitted_at', 'kind', 'code', 'name', 'is_active'],
            'task' => ['submitted_at', 'title', 'status', 'priority', 'starts_at', 'due_at'],
            'follow_up' => ['subject', 'channel', 'status', 'due_at'],
            'education' => ['institution', 'qualification'],
            'employment' => ['employer', 'designation'],
            'test_score' => ['test', 'overall'],
            'preference' => ['country', 'course'],
            default => [],
        }];
    }

    public const FIELDS = ['source_id' => 'Stable source record ID', 'full_name' => 'Full name', 'email' => 'Email', 'phone' => 'Phone', 'city' => 'City', 'country' => 'Preferred country', 'course' => 'Course', 'study_level' => 'Study level', 'intake' => 'Intake', 'english_test' => 'English test', 'qualification' => 'Qualification', 'passing_year' => 'Passing year', 'percentage' => 'Percentage / CGPA', 'lead_category' => 'Lead category', 'source_reference' => 'Enquiry reference', 'message' => 'Remarks', 'submitted_at' => 'Original submission date (ISO 8601)'];

    protected $fillable = ['creation_key', 'created_by', 'source', 'entity_type', 'status', 'mapping', 'headers', 'row_count', 'version'];

    protected function casts(): array
    {
        return ['mapping' => 'array', 'headers' => 'array', 'version' => 'integer', 'row_count' => 'integer'];
    }
}
