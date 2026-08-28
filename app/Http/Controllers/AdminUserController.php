<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('users.manage');

        $query = User::query()->where('is_admin', true)->latest();

        if ($request->filled('q')) {
            $search = '%'.$request->string('q')->trim().'%';
            $query->where(fn ($builder) => $builder->where('name', 'like', $search)->orWhere('email', 'like', $search));
        }

        if ($request->filled('role')) {
            $query->where('admin_role', $request->string('role'));
        }

        return view('admin.users.index', [
            'users' => $query->paginate(12)->withQueryString(),
            'roles' => AdminAccess::roles(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('users.manage');

        return view('admin.users.create', [
            'roles' => AdminAccess::roles(),
            'permissions' => AdminAccess::permissions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('users.manage');

        $validated = $this->validateUser($request);
        $role = $validated['admin_role'];

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => true,
            'admin_role' => $role,
            'admin_permissions' => $this->permissionsForRole($role, $validated['permissions'] ?? []),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.users.edit', $user)->with('status', 'Team member created. Their dashboard access is ready.');
    }

    public function edit(User $user): View
    {
        Gate::authorize('users.manage');
        abort_unless($user->is_admin, 404);

        return view('admin.users.edit', [
            'managedUser' => $user,
            'roles' => AdminAccess::roles(),
            'permissions' => AdminAccess::permissions(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('users.manage');
        abort_unless($user->is_admin, 404);

        $validated = $this->validateUser($request, $user);
        $role = $validated['admin_role'];
        $isActive = $request->boolean('is_active');

        if ($user->is($request->user()) && ! $isActive) {
            return back()->withErrors(['is_active' => 'You cannot deactivate your own account.'])->withInput();
        }

        if ($this->wouldRemoveLastSuperAdministrator($user, $role, $isActive)) {
            return back()->withErrors(['admin_role' => 'At least one active Super administrator must remain.'])->withInput();
        }

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'admin_role' => $role,
            'admin_permissions' => $this->permissionsForRole($role, $validated['permissions'] ?? []),
            'is_active' => $isActive,
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('status', 'Access settings updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:160', Rule::unique('users', 'email')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(10)->mixedCase()->numbers()],
            'admin_role' => ['required', Rule::in(AdminAccess::roleKeys())],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [Rule::in(AdminAccess::permissionKeys())],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * @param  array<int, string>  $permissions
     * @return list<string>
     */
    private function permissionsForRole(string $role, array $permissions): array
    {
        if ($role === 'custom') {
            return AdminAccess::normalizePermissions($permissions);
        }

        return AdminAccess::roles()[$role]['permissions'];
    }

    private function wouldRemoveLastSuperAdministrator(User $user, string $newRole, bool $isActive): bool
    {
        if ($user->admin_role !== 'super_admin' || ! $user->is_active || ($newRole === 'super_admin' && $isActive)) {
            return false;
        }

        return User::query()
            ->where('is_admin', true)
            ->where('is_active', true)
            ->where('admin_role', 'super_admin')
            ->count() <= 1;
    }
}
