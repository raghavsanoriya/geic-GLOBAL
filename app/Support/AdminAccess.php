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
            'enquiries.view' => [
                'label' => 'View student enquiries',
                'description' => 'See enquiry totals, contact details, destinations, and study plans.',
            ],
            'enquiries.export' => [
                'label' => 'Export student enquiries',
                'description' => 'Download enquiry records as a CSV file.',
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
        return [
            'super_admin' => [
                'label' => 'Super administrator',
                'description' => 'Full access to every dashboard feature, including team access.',
                'permissions' => array_keys(self::permissions()),
            ],
            'administrator' => [
                'label' => 'Administrator',
                'description' => 'Manage enquiries, website content, and media without controlling team access.',
                'permissions' => ['enquiries.view', 'enquiries.export', 'content.manage', 'media.manage'],
            ],
            'content_editor' => [
                'label' => 'Content editor',
                'description' => 'Update website pages and media without access to student enquiries.',
                'permissions' => ['content.manage', 'media.manage'],
            ],
            'counsellor' => [
                'label' => 'Counsellor',
                'description' => 'Review and export student enquiries without changing website content.',
                'permissions' => ['enquiries.view', 'enquiries.export'],
            ],
            'custom' => [
                'label' => 'Custom access',
                'description' => 'Choose individual permissions for this team member.',
                'permissions' => [],
            ],
        ];
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
