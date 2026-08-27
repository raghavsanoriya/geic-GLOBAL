@extends('admin.layout')

@section('title', 'Create user | Trans Globe Indore LMS')
@section('crumb', 'Create user')
@section('backUrl', route('admin.users.index'))
@section('backLabel', 'Back to team access')

@section('content')
    <section class="page-head"><div><span class="eyebrow">Access &amp; permissions</span><h1>Create dashboard user</h1></div><p>Add a team member and choose only the features required for their work.</p></section>
    <section class="account-layout"><form class="panel account-card account-form" method="post" action="{{ route('admin.users.store') }}">@csrf<div class="account-card__head"><div><h2>Account details</h2><p>Use the role presets or choose Custom access for individual permissions.</p></div></div>@include('admin.users._form', ['managedUser' => null])<div class="account-actions"><a class="button button--quiet" href="{{ route('admin.users.index') }}">Cancel</a><button class="button" type="submit">Create user</button></div></form><aside class="panel account-summary"><span class="account-summary__avatar" aria-hidden="true">+</span><div><h2>Safe access by default</h2><p>Every account is protected by authentication and server-side permission checks.</p></div><div class="account-facts"><div class="account-fact"><span>Password</span><strong>10+ characters</strong></div><div class="account-fact"><span>Default state</span><strong>Active</strong></div><div class="account-fact"><span>Permissions</span><strong>Role controlled</strong></div></div></aside></section>
@endsection
