@extends('admin.layout')

@section('title', 'Team access | Trans Globe Indore LMS')
@section('crumb', 'Team access')
@section('backUrl', route('admin.dashboard'))
@section('backLabel', 'Back to dashboard')

@section('content')
    <section class="page-head"><div><span class="eyebrow">Access &amp; permissions</span><h1>Team access</h1></div><p>Create administrator accounts and control exactly which dashboard areas each person can use.</p></section>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
    <section class="panel access-table">
        <div class="panel__head"><div><h2>Administrators</h2><span>{{ $users->total() }} dashboard account{{ $users->total() === 1 ? '' : 's' }}</span></div><a class="button" href="{{ route('admin.users.create') }}"><svg viewBox="0 0 24 24" aria-hidden="true" style="width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:2"><path d="M12 5v14M5 12h14"/></svg>Create user</a></div>
        <div class="access-toolbar" style="padding:16px 22px 0"><form method="get" action="{{ route('admin.users.index') }}"><input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search name or email" aria-label="Search team members"><select class="select" name="role" aria-label="Filter by role"><option value="">All roles</option>@foreach($roles as $key => $role)<option value="{{ $key }}" @selected(request('role') === $key)>{{ $role['label'] }}</option>@endforeach</select><button class="button button--quiet" type="submit">Filter</button></form></div>
        <div class="table-wrap"><table><thead><tr><th>Team member</th><th>Role</th><th>Status</th><th>Last updated</th><th></th></tr></thead><tbody>@forelse($users as $teamUser)<tr><td><div class="access-user"><span class="avatar">{{ strtoupper(substr($teamUser->name, 0, 1)) }}</span><div><span class="student">{{ $teamUser->name }}</span><span class="sub">{{ $teamUser->email }}</span></div></div></td><td><span class="pill">{{ $teamUser->adminRoleLabel() }}</span></td><td><span class="status-dot {{ $teamUser->is_active ? '' : 'status-dot--inactive' }}">{{ $teamUser->is_active ? 'Active' : 'Inactive' }}</span></td><td>{{ $teamUser->updated_at->format('d M Y') }}</td><td style="text-align:right"><a class="button button--quiet" href="{{ route('admin.users.edit', $teamUser) }}">Edit access</a></td></tr>@empty<tr><td colspan="5"><div class="empty"><h3>No team members found</h3><p>Change the filters or create a new administrator.</p></div></td></tr>@endforelse</tbody></table></div>
        <div class="pagination">{{ $users->onEachSide(1)->links() }}</div>
    </section>
@endsection
