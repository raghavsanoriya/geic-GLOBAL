<?php

namespace App\Support;

class AdminAccess
{
    /**
     * @return array<string, array{label: string, description: string}>
     */
    public static function permissions(): array
    {
        return [
            'crm.export' => [
                'label' => 'Export accessible operational student profiles',
                'description' => 'Download scoped CRM contact lists or open printable lists. Restricted profile fields are excluded.',
            ],
            'crm.imports.manage' => [
                'label' => 'Manage historical CRM lead imports',
                'description' => 'Stage, validate, process and reconcile source exports. Also requires all-case visibility.',
            ],
            'crm.documents.view' => [
                'label' => 'View private student documents',
                'description' => 'Download private documents for currently accessible students. Does not grant public media access.',
            ],
            'crm.documents.manage' => [
                'label' => 'Manage private student documents',
                'description' => 'Upload and retire verified student documents while retaining the audit history.',
            ],
            'crm.marketing.manage' => [
                'label' => 'Manage accessible CRM marketing campaigns',
                'description' => 'Compose campaigns, preview consented audiences and review recipient delivery. Provider configuration is separate.',
            ],
            'crm.inbound.manage' => [
                'label' => 'Manage inbound CRM forms and API keys',
                'description' => 'Create scoped server-side form APIs, rotate credentials, configure field maps and review submission logs.',
            ],
            'crm.integrations.manage' => [
                'label' => 'Manage CRM provider credentials',
                'description' => 'Configure and test encrypted SMTP and OneSignal credentials. Sending requires a verified configuration.',
            ],
            'crm.loyalty.view' => [
                'label' => 'View accessible loyalty memberships',
                'description' => 'Read loyalty members and points for accessible student profiles.',
            ],
            'crm.loyalty.manage' => [
                'label' => 'Manage accessible loyalty memberships and points',
                'description' => 'Link referrals and record auditable point adjustments. Rules require configuration permission.',
            ],
            'crm.finance.view' => [
                'label' => 'View accessible CRM finance records',
                'description' => 'Read invoices and payment ledgers for students this user is allowed to access.',
            ],
            'crm.finance.manage' => [
                'label' => 'Manage accessible CRM finance records',
                'description' => 'Create and issue invoices, record receipts and make auditable reversals. Does not transfer money.',
            ],
            'crm.configure' => [
                'label' => 'Configure CRM reference data',
                'description' => 'Manage Indore CRM dictionaries, universities, campuses and lifecycle transition rules.',
            ],
            'crm.view' => [
                'label' => 'View assigned CRM students',
                'description' => 'Open the Indore CRM and view cases assigned to this staff member.',
            ],
            'crm.manage' => [
                'label' => 'Edit accessible CRM students',
                'description' => 'Update accessible student profiles and lifecycle stages.',
            ],
            'crm.view_all' => [
                'label' => 'View all Indore CRM students',
                'description' => 'View all Indore cases, including unassigned cases and original enquiry lists.',
            ],
            'crm.assign' => [
                'label' => 'Assign CRM cases',
                'description' => 'Change the counsellor and RE on an accessible case.',
            ],
            'crm.sensitive' => [
                'label' => 'Access restricted student details',
                'description' => 'View or update optional family, demographic and passport-status fields on accessible cases.',
            ],
            'enquiries.view' => [
                'label' => 'View student enquiries',
                'description' => 'See enquiry totals, contact details, destinations, and study plans.',
            ],
            'enquiries.review' => [
                'label' => 'Review duplicate enquiries',
                'description' => 'Confirm matching contacts or keep submissions separate without deleting leads.',
            ],
            'enquiries.export' => [
                'label' => 'Export student enquiries',
                'description' => 'Download enquiry records as a CSV file.',
            ],
            'ads.view' => [
                'label' => 'View advertising performance',
                'description' => 'Monitor ad accounts, campaign spend, leads, and lead quality.',
            ],
            'ads.manage' => [
                'label' => 'Manage advertising workspace',
                'description' => 'Add ad accounts, campaigns, and daily performance data.',
            ],
            'content.manage' => [
                'label' => 'Manage website content',
                'description' => 'Create pages, edit page sections, and publish or unpublish content.',
            ],
            'media.manage' => [
                'label' => 'Manage media library',
                'description' => 'Upload images and select media inside page editors.',
            ],
            'users.manage' => [
                'label' => 'Manage team access',
                'description' => 'Create administrators and change roles, permissions, or account status.',
            ],
        ];
    }

    /**
     * @return array<string, array{label: string, description: string, permissions: list<string>}>
     */
    public static function roles(): array
    {
        $roles = [
            'super_admin' => [
                'label' => 'Super administrator',
                'description' => 'Full access to every dashboard feature, including team access.',
                'permissions' => array_keys(self::permissions()),
            ],
            'administrator' => [
                'label' => 'Administrator',
                'description' => 'Manage enquiries, website content, and media without controlling team access.',
                'permissions' => array_merge(config('crm.enabled') ? ['crm.view', 'crm.manage', 'crm.view_all', 'crm.assign', 'crm.sensitive'] : [], ['enquiries.view', 'enquiries.review', 'enquiries.export', 'ads.view', 'ads.manage', 'content.manage', 'media.manage']),
            ],
            'content_editor' => [
                'label' => 'Content editor',
                'description' => 'Update website pages and media without access to student enquiries.',
                'permissions' => ['content.manage', 'media.manage'],
            ],
            'counsellor' => [
                'label' => 'Counsellor',
                'description' => 'Review and export student enquiries without changing website content.',
                'permissions' => array_merge(config('crm.enabled') ? ['crm.view', 'crm.manage'] : [], ['enquiries.view', 'enquiries.export']),
            ],
            'relationship_executive' => [
                'label' => 'RE',
                'description' => 'Manage assigned Indore student cases without changing assignments or restricted details.',
                'permissions' => ['crm.view', 'crm.manage'],
            ],
            'ads_manager' => [
                'label' => 'Ads manager',
                'description' => 'Connect advertising tracking and review campaign performance without access to student enquiries or website content.',
                'permissions' => ['ads.view', 'ads.manage'],
            ],
            'custom' => [
                'label' => 'Custom access',
                'description' => 'Choose individual permissions for this team member.',
                'permissions' => [],
            ],
        ];
        if (! config('crm.enabled')) {
            unset($roles['relationship_executive']);
        }

        return $roles;
    }

    /**
     * @return list<string>
     */
    public static function permissionKeys(): array
    {
        return array_keys(self::permissions());
    }

    /**
     * @return list<string>
     */
    public static function roleKeys(): array
    {
        return array_keys(self::roles());
    }

    /**
     * @param  array<int, string>|null  $permissions
     * @return list<string>
     */
    public static function normalizePermissions(?array $permissions): array
    {
        return array_values(array_intersect(self::permissionKeys(), $permissions ?? []));
    }
}
