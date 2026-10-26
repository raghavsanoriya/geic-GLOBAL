<?php

namespace App\Http\Controllers;

use App\Models\AdminRolePreset;
use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminRolePresetController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('users.manage');
        $roles = AdminRolePreset::query()->withCount(['users'])->orderBy('label')->paginate(25);

        return response()->view('admin.users.roles.index', compact('roles'), 200, ['Cache-Control' => 'private, no-store']);
    }

    public function create(): Response
    {
        Gate::authorize('users.manage');

        return $this->form(new AdminRolePreset(['is_active' => true, 'permissions' => []]));
    }

    public function show(int $role): Response
    {
        Gate::authorize('users.manage');

        return $this->form(AdminRolePreset::findOrFail($role));
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->save($request, new AdminRolePreset);
    }

    public function update(Request $request, int $role): RedirectResponse
    {
        return $this->save($request, AdminRolePreset::findOrFail($role));
    }

    private function save(Request $request, AdminRolePreset $role): RedirectResponse
    {
        Gate::authorize('users.manage');
        $reserved = array_keys(array_diff_key(AdminAccess::roles(), AdminRolePreset::query()->pluck('id', 'key')->all()));
        $data = $request->validate([
            'version' => $role->exists ? ['required', 'integer', 'min:1'] : ['missing'],
            'key' => $role->exists ? ['missing'] : ['required', 'string', 'max:40', 'regex:/^[a-z][a-z0-9_]{2,39}$/', Rule::notIn($reserved), Rule::unique('admin_role_presets', 'key')],
            'label' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['required', Rule::in(AdminAccess::permissionKeys())],
            'is_active' => ['required', 'boolean'],
        ]);
        DB::transaction(function () use ($request, $role, $data): void {
            if ($role->exists) {
                $role = AdminRolePreset::whereKey($role->id)->lockForUpdate()->firstOrFail();
                abort_unless($role->version === (int) $data['version'], 409, 'This role changed. Reload before saving.');
                $role->version++;
            } else {
                $role->key = Str::lower($data['key']);
            }
            $role->fill(['label' => $data['label'], 'description' => $data['description'], 'permissions' => AdminAccess::normalizePermissions($data['permissions'] ?? []), 'is_active' => $data['is_active']])->save();
            User::query()->where('admin_role', $role->key)->update(['admin_permissions' => json_encode($role->is_active ? $role->permissions : [], JSON_THROW_ON_ERROR), 'updated_at' => now()]);
            DB::table('admin_role_preset_events')->insert(['role_id' => $role->id, 'actor_id' => $request->user()->id, 'action' => $role->wasRecentlyCreated ? 'created' : 'updated', 'created_at' => now()]);
        });

        return to_route('admin.roles.show', $role->id)->with('status', 'Reusable role saved and assigned users synchronized.');
    }

    private function form(AdminRolePreset $role): Response
    {
        return response()->view('admin.users.roles.form', ['role' => $role, 'permissions' => AdminAccess::permissions()], 200, ['Cache-Control' => 'private, no-store']);
    }
}
