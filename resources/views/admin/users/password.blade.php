@extends('admin.layout')

@section('title', 'Reset password | Trans Globe Indore LMS')
@section('crumb', 'Reset password')
@section('backUrl', route('admin.users.edit', $managedUser))
@section('backLabel', 'Back to team member')

@section('content')
    <section class="page-head"><div><span class="eyebrow">Team access</span><h1>Reset password</h1></div><p>Set a new password directly for {{ $managedUser->name }}. No email is sent.</p></section>
    <section class="account-layout">
        <form class="panel account-card account-form" method="post" action="{{ route('admin.users.password.update', $managedUser) }}">
            @csrf
            @method('PUT')
            <div class="account-card__head"><div><h2>New sign-in password</h2><p>The team member can use this password immediately after you save it.</p></div></div>
            @if($errors->any())<div class="error-summary account-form__full" role="alert" tabindex="-1" data-error-summary><strong>Please review the highlighted information.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <div class="account-form__grid">
                <div class="field"><label for="reset-password">New password</label><input class="input" id="reset-password" type="password" name="password" autocomplete="new-password" required><small>At least 10 characters with uppercase, lowercase, and a number.</small>@error('password')<small role="alert" style="color:#9f2029">{{ $message }}</small>@enderror</div>
                <div class="field"><label for="reset-password-confirmation">Confirm new password</label><input class="input" id="reset-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></div>
            </div>
            <div class="account-actions"><a class="button button--quiet" href="{{ route('admin.users.edit', $managedUser) }}">Cancel</a><button class="button" type="submit">Reset password</button></div>
        </form>
        <aside class="panel account-summary"><span class="account-summary__avatar">{{ strtoupper(substr($managedUser->name, 0, 1)) }}</span><div><h2>{{ $managedUser->name }}</h2><p>{{ $managedUser->email }}</p></div><div class="account-facts"><div class="account-fact"><span>Email message</span><strong>Not sent</strong></div><div class="account-fact"><span>Access</span><strong>{{ $managedUser->is_active ? 'Active' : 'Inactive' }}</strong></div></div></aside>
    </section>
@endsection

@push('scripts')
<script>document.querySelector('[data-error-summary]')?.focus();</script>
@endpush