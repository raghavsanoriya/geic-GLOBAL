<?php

namespace App\Support;

use App\Models\User;

class CrmAccess
{
    public static function viewsAll(User $user): bool
    {
        return $user->is_admin && $user->is_active
            && (in_array($user->admin_role, ['super_admin', 'administrator'], true)
                || $user->hasAdminPermission('crm.view_all'));
    }
}
