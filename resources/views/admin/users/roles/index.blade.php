@extends('admin.layout')
@section('title','Reusable roles | GEIC')
@section('crumb','Reusable roles')
@section('backUrl',route('admin.users.index'))
@section('backLabel','Back to team access')
@section('content')
<section class="page-head"><div><span class="eyebrow">Access &amp; permissions</span><h1>Reusable roles</h1><p>Create permission presets that stay consistent across multiple team members.</p></div><a class="button" href="{{route('admin.roles.create')}}">Create role</a></section>
@if(session('status'))<div class="notice" role="status">{{session('status')}}</div>@endif
<section class="panel access-table"><div class="table-wrap"><table><thead><tr><th>Role</th><th>Key</th><th>Users</th><th>Status</th><th></th></tr></thead><tbody>@forelse($roles as $role)<tr><td><span class="student">{{$role->label}}</span><span class="sub">{{$role->description}}</span></td><td><code>{{$role->key}}</code></td><td>{{$role->users_count}}</td><td>{{$role->is_active?'Active':'Inactive'}}</td><td><a class="button button--quiet" href="{{route('admin.roles.show',$role->id)}}">Edit role</a></td></tr>@empty<tr><td colspan="5"><div class="empty"><h3>No reusable custom roles</h3><p>Built-in roles remain available. Create a preset for a team-specific permission set.</p></div></td></tr>@endforelse</tbody></table></div>{{$roles->links()}}</section>
@endsection
