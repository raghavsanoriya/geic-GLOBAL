@extends('admin.layout')
@section('title','Contacts | GEIC Indore')
@section('crumb','Business contacts')
@section('content')
<div class="crm-workspace">
<section class="page-head"><div><span class="eyebrow">Indore · Business directory</span><h1>Contacts</h1><p class="sub">Your university, agent and business relationships, in one place.</p></div>@can('crm.manage')<a class="button" href="{{ route('admin.crm.contacts.create') }}">Add contact</a>@endcan</section>
@include('admin.crm.validation-feedback')
@include('admin.crm.summary-cards', ['cards'=>[
    ['Matching contacts', $summary->total, 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M20 8v6 M17 11h6'],
    ['Active contacts', $summary->active, 'M9 12l2 2 4-4 M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0'],
    ['Companies', $summary->companies, 'M3 21h18 M5 21V3h14v18 M9 7h1 M14 7h1 M9 11h1 M14 11h1 M10 21v-5h4v5'],
    ['Countries', $summary->countries, 'M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0 M3 12h18 M12 3a16 16 0 0 1 0 18 16 16 0 0 1 0-18']
]])
<form class="panel crm-profile-form" method="get" action="{{ route('admin.crm.contacts.index') }}"><h2 class="rocket-filters-title">Find a business contact</h2><div class="crm-form-grid">@foreach(['q'=>'Name, company or email','contact_type'=>'Contact type','country'=>'Country','state'=>'State / region','city'=>'City'] as $key=>$label)<label>{{ $label }}<input class="input" name="{{ $key }}" maxlength="{{ in_array($key,['country','state','city']) ? 100 : ($key==='contact_type' ? 80 : 160) }}" value="{{ request($key) }}"></label>@endforeach</div><div class="rocket-toolbar"><div><button class="button" type="submit">Search contacts</button><a class="button button--quiet" href="{{ route('admin.crm.contacts.index') }}">Reset</a></div>@can('crm.export')<div><button class="button button--quiet" name="format" value="csv">Export CSV</button><button class="button button--quiet" name="format" value="print">Print / PDF</button></div>@endcan</div></form>
<section class="panel crm-student-list" aria-label="Business contact list"><header class="rocket-section-heading"><div><h2>Your contacts</h2><p>{{ number_format($contacts->total()) }} matching records · access restricted to your permissions</p></div><span class="rocket-state rocket-state--muted">Business directory</span></header>
@forelse($contacts as $contact)<article class="crm-student-row"><div class="rocket-row-identity"><span class="rocket-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($contact->name,0,1)) }}</span><div><strong>{{ $contact->name }}</strong><p>{{ $contact->company ?: 'Company not provided' }}</p><span class="rocket-record-reference">CONTACT-{{ $contact->id }}</span></div></div><div><p>{{ $contact->email ?: 'Email not provided' }}</p><p>{{ $contact->phone ?: 'Phone not provided' }}</p></div><div><span class="rocket-state {{ $contact->is_active ? '' : 'rocket-state--muted' }}">{{ $contact->is_active ? 'Active' : 'Inactive' }}</span><p>{{ $contact->contact_type }}</p><p>{{ collect([$contact->city,$contact->country])->filter()->join(', ') ?: 'Location not provided' }}</p></div><a class="button button--quiet" href="{{ route('admin.crm.contacts.show',$contact->id) }}">Open contact</a></article>
@empty<div class="rocket-empty"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M10 40v-6a9 9 0 0 1 9-9h10a9 9 0 0 1 9 9v6 M32 13a8 8 0 1 1-16 0 8 8 0 0 1 16 0"/></svg><h2>No matching contacts</h2><p>Add verified business details or try a broader search. Student enquiries stay in the student workspace.</p></div>@endforelse</section>{{ $contacts->links() }}</div>
@endsection
@include('admin.crm.students.styles')
@include('admin.crm.rocket-styles')
