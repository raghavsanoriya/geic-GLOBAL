@extends('admin.layout')

@section('title', 'Edit '.$managedUser->name.' | Trans Globe Indore LMS')
@section('crumb', 'Edit access')
@section('backUrl', route('admin.users.index'))
@section('backLabel', 'Back to team access')

@section('content')
    <section class="page-head"><div><span class="eyebrow">Access &amp; permissions</span><h1>{{ $managedUser->name }}</h1></div><p>Update this team member’s account, role, permissions, status, or password.</p></section>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
    <section class="account-layout"><form class="panel account-card account-form" method="post" action="{{ route('admin.users.update', $managedUser) }}">@csrf @method('PUT')<div class="account-card__head"><div><h2>Account &amp; access</h2><p>Changes take effect on the team member’s next request.</p></div><span class="status-dot {{ $managedUser->is_active ? '' : 'status-dot--inactive' }}">{{ $managedUser->is_active ? 'Active' : 'Inactive' }}</span></div>@include('admin.users._form')<div class="account-actions"><a class="button button--quiet" href="{{ route('admin.users.index') }}">Cancel</a><button class="button" type="submit">Save access</button></div></form><aside class="panel account-summary"><span class="account-summary__avatar">{{ strtoupper(substr($managedUser->name, 0, 1)) }}</span><div><h2>{{ $managedUser->name }}</h2><p>{{ $managedUser->email }}</p></div><div class="account-facts"><div class="account-fact"><span>Role</span><strong>{{ $managedUser->adminRoleLabel() }}</strong></div><div class="account-fact"><span>Created</span><strong>{{ $managedUser->created_at->format('d M Y') }}</strong></div><div class="account-fact"><span>Updated</span><strong>{{ $managedUser->updated_at->format('d M Y') }}</strong></div></div></aside></section>
@endsection
