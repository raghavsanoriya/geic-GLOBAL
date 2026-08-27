@php
    $selectedRole = old('admin_role', $managedUser?->admin_role ?? 'administrator');
    $selectedPermissions = old('permissions', $managedUser?->admin_permissions ?? ($roles[$selectedRole]['permissions'] ?? []));
@endphp

@if($errors->any())
    <div class="error-summary account-form__full" role="alert" tabindex="-1" data-error-summary><strong>Please review the highlighted information.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<div class="account-form__grid">
    <div class="field"><label for="team-name">Full name</label><input class="input" id="team-name" name="name" value="{{ old('name', $managedUser?->name) }}" maxlength="120" autocomplete="name" required>@error('name')<small role="alert" style="color:#9f2029">{{ $message }}</small>@enderror</div>
    <div class="field"><label for="team-email">Email address</label><input class="input" id="team-email" type="email" name="email" value="{{ old('email', $managedUser?->email) }}" maxlength="160" autocomplete="email" required>@error('email')<small role="alert" style="color:#9f2029">{{ $message }}</small>@enderror</div>
    <div class="field"><label for="team-password">{{ $managedUser ? 'New password (optional)' : 'Temporary password' }}</label><input class="input" id="team-password" type="password" name="password" autocomplete="new-password" @required(! $managedUser)><small>Minimum 10 characters with uppercase, lowercase, and a number.</small>@error('password')<small role="alert" style="color:#9f2029">{{ $message }}</small>@enderror</div>
    <div class="field"><label for="team-password-confirmation">Confirm password</label><input class="input" id="team-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" @required(! $managedUser)></div>
    <div class="field account-form__full"><label for="admin-role">Access role</label><select class="select" id="admin-role" name="admin_role" data-role-select required>@foreach($roles as $key => $role)<option value="{{ $key }}" @selected($selectedRole === $key)>{{ $role['label'] }}</option>@endforeach</select></div>
    <div class="role-preview account-form__full" data-role-preview><strong>{{ $roles[$selectedRole]['label'] }}</strong><span>{{ $roles[$selectedRole]['description'] }}</span></div>
    <fieldset class="account-form__full" style="margin:0;padding:0;border:0" data-permission-group>
        <legend style="margin-bottom:10px;color:var(--admin-ink);font-size:13px;font-weight:700">Permissions</legend>
        <div class="permission-grid">
            @foreach($permissions as $key => $permission)
                <label class="permission-option"><input type="checkbox" name="permissions[]" value="{{ $key }}" @checked(in_array($key, $selectedPermissions, true))><span><strong>{{ $permission['label'] }}</strong><span>{{ $permission['description'] }}</span></span></label>
            @endforeach
        </div>
        @error('permissions')<small role="alert" style="display:block;margin-top:7px;color:#9f2029">{{ $message }}</small>@enderror
    </fieldset>
    <label class="access-switch account-form__full"><span><strong>Account active</strong><span>Inactive accounts cannot sign in or access dashboard data.</span></span><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $managedUser?->is_active ?? true))></label>
    @error('is_active')<small role="alert" class="account-form__full" style="color:#9f2029">{{ $message }}</small>@enderror
</div>

@push('scripts')
<script>
    (() => {
        const role = document.querySelector('[data-role-select]');
        const preview = document.querySelector('[data-role-preview]');
        const group = document.querySelector('[data-permission-group]');
        const roles = @json($roles);
        if (!role || !preview || !group) return;
        const syncRole = () => {
            const definition = roles[role.value];
            preview.querySelector('strong').textContent = definition.label;
            preview.querySelector('span').textContent = definition.description;
            const isCustom = role.value === 'custom';
            group.querySelectorAll('input').forEach((input) => {
                if (!isCustom) input.checked = definition.permissions.includes(input.value);
                input.disabled = !isCustom;
            });
            group.style.opacity = isCustom ? '1' : '.68';
        };
        role.addEventListener('change', syncRole);
        syncRole();
        document.querySelector('[data-error-summary]')?.focus();
    })();
</script>
@endpush
