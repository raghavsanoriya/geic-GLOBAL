@php
    $navLink = static fn($label, $route, $permission, $active, $params = []) => ['label'=>$label, 'url'=>route($route,$params), 'visible'=>auth()->user()->can($permission), 'active'=>$active];
    $referenceLinks = [];
    foreach (\App\Models\CrmReference::KINDS as $referenceKey=>$referenceLabel) {
        $referenceLinks[]=$navLink($referenceLabel,'admin.crm.references.index','crm.configure',request()->routeIs('admin.crm.references.*') && ($kind ?? request('kind','university'))===$referenceKey,['kind'=>$referenceKey]);
    }
    $leadLinks=[$navLink('All student profiles','admin.crm.students.index','crm.view',request()->routeIs('admin.crm.students.*')&&!request('stage'))];
    foreach (\App\Support\CrmStudentFields::stages() as $stageKey=>$stageLabel) {
        $leadLinks[]=$navLink($stageLabel,'admin.crm.students.index','crm.view',request()->routeIs('admin.crm.students.index')&&request('stage')===$stageKey,['stage'=>$stageKey]);
    }
    $leadLinks[]=$navLink('Create lead','admin.crm.leads.create','crm.manage',request()->routeIs('admin.crm.leads.*'));
    $menus=[
        ['label'=>'Lead Level','icon'=>'M4 5h16v14H4V5Zm4 4h8m-8 4h5','children'=>$leadLinks],
        ['label'=>'Finance','icon'=>'M5 3h14v18l-4-2-3 2-3-2-4 2V3Zm4 5h6m-6 4h6','children'=>[
            $navLink('Invoices','admin.crm.invoices.index','crm.finance.view',request()->routeIs('admin.crm.invoices.*')),
            $navLink('Transactions','admin.crm.transactions.index','crm.finance.view',request()->routeIs('admin.crm.transactions.*')),
        ]],
        ['label'=>'Loyalty Management','icon'=>'M8 3h8v6a4 4 0 0 1-8 0V3ZM8 5H4v3a4 4 0 0 0 4 4m8-7h4v3a4 4 0 0 1-4 4m-4 1v6m-4 2h8','children'=>[
            $navLink('Members / Search','admin.crm.loyalty.index','crm.loyalty.view',request()->routeIs('admin.crm.loyalty.*')&&!request()->routeIs('admin.crm.loyalty.points','admin.crm.loyalty.rules*')),
            $navLink('Points ledger','admin.crm.loyalty.points','crm.loyalty.view',request()->routeIs('admin.crm.loyalty.points')),
            $navLink('Award rules','admin.crm.loyalty.rules','crm.configure',request()->routeIs('admin.crm.loyalty.rules*')),
        ]],
        ['label'=>'Marketing','icon'=>'m3 10 17-6v16L3 14v-4Zm4 6 2 5h4','children'=>[
            $navLink('Campaigns / Delivery reports','admin.crm.campaigns.index','crm.marketing.manage',request()->routeIs('admin.crm.campaigns.*')),
            $navLink('Campaign Reports','admin.crm.campaign-reports','crm.marketing.manage',request()->routeIs('admin.crm.campaign-reports')),
            $navLink('Media library','admin.media.index','media.manage',request()->routeIs('admin.media.*')),
            $navLink('Inbound Forms & APIs','admin.crm.inbound.index','crm.inbound.manage',request()->routeIs('admin.crm.inbound.*')),
        ]],
        ['label'=>'Config','icon'=>'M4 7h16M4 17h16M8 3v8m8 2v8','children'=>[
            ['label'=>'Lead Management','children'=>$referenceLinks],
            ['label'=>'Settings','children'=>[
                ['label'=>'Branches','url'=>route('admin.crm.branches.index'),'visible'=>auth()->user()->can('crm.configure')&&auth()->user()->can('crm.sensitive'),'active'=>request()->routeIs('admin.crm.branches.*')],
                $navLink('Email / SMTP','admin.crm.integrations.show','crm.integrations.manage',request()->is('admin/crm/settings/email'),['provider'=>'email']),
                $navLink('OneSignal','admin.crm.integrations.show','crm.integrations.manage',request()->is('admin/crm/settings/onesignal'),['provider'=>'onesignal']),
            ]],
        ]],
    ];
    $filterMenus=function($items) use (&$filterMenus) {
        $visible=[];
        foreach($items as $item) {
            if(isset($item['children'])) {
                $item['children']=$filterMenus($item['children']);
                if($item['children']===[]) {continue;}
                $item['active']=in_array(true,array_column($item['children'],'active'),true);
            } elseif(!$item['visible']) {continue;}
            $visible[]=$item;
        }
        return $visible;
    };
    $menus=$filterMenus($menus);
@endphp
<span class="admin-sidebar__section">Indore CRM</span>
<nav class="admin-nav crm-sidebar-nav" aria-label="Indore CRM navigation">
    @foreach([
        ['CRM Dashboard','admin.crm.dashboard','crm.view','admin.crm.dashboard','M3 3h7v7H3V3Zm11 0h7v7h-7V3ZM3 14h7v7H3v-7Zm11 0h7v7h-7v-7Z'],
        ['Reports','admin.crm.reports.index','crm.view','admin.crm.reports.*','M4 21V3h12l4 4v14H4Zm4-4v-3m4 3V9m4 8v-5'],
        ['Search workspace','admin.crm.search','crm.view','admin.crm.search','M10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12Zm5-1 6 6'],
        ['My reminders','admin.crm.reminders.index','crm.view','admin.crm.reminders.*','M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4'],
        ['Follow-ups','admin.crm.follow-ups.index','crm.view','admin.crm.follow-ups.*','M3 5h18v16H3V5Zm5-2v4m8-4v4M3 11h18m-13 4h3'],
        ['Applications','admin.crm.applications.index','crm.view','admin.crm.applications.*','M6 3h9l4 4v14H6V3Zm8 0v5h5m-10 4h7m-7 4h7'],
        ['Visa','admin.crm.visas.index','crm.view','admin.crm.visas.*','M4 3h16v18H4V3Zm4 14h8M12 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6'],
        ['Programs','admin.crm.programs.index','crm.view','admin.crm.programs.*','m3 8 9-5 9 5-9 5-9-5Zm3 3v7l6 3 6-3v-7'],
        ['Tasks','admin.crm.tasks.index','crm.view','admin.crm.tasks.*','M3 3h18v18H3V3Zm4 9 3 3 7-7'],
        ['Contacts','admin.crm.contacts.index','crm.view','admin.crm.contacts.*','M4 3h16v18H4V3Zm4 14v-2a4 4 0 0 1 8 0v2M12 7a2 2 0 1 0 0 4 2 2 0 0 0 0-4'],
        ['Activity logs','admin.crm.logs.index','crm.view','admin.crm.logs.*','M5 3h14v18H5V3Zm4 5h6m-6 4h6m-6 4h6'],
        ['Lead upload','admin.crm.imports.index','crm.imports.manage','admin.crm.imports.*','M12 17V3m-5 5 5-5 5 5M4 15v6h16v-6'],
    ] as [$label,$routeName,$permission,$pattern,$icon])
        @can($permission)<a href="{{route($routeName)}}" title="{{$label}}" @if(request()->routeIs($pattern)) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{$icon}}"/></svg><span class="admin-nav__label">{{$label}}</span></a>@endcan
        @if($loop->index===1)
            @foreach($menus as $menu) @if($menu['label']==='Lead Level') @include('admin.crm.sidebar-group',['menu'=>$menu]) @endif @endforeach
        @endif
    @endforeach
    @foreach($menus as $menu) @if($menu['label']!=='Lead Level') @include('admin.crm.sidebar-group',['menu'=>$menu]) @endif @endforeach
</nav>
<style>
.crm-sidebar-group{min-width:0;margin:1px 0}.crm-sidebar-group>summary{display:flex;align-items:center;gap:12px;list-style:none;min-height:44px;padding:10px 20px 10px 32px;color:var(--admin-muted);font-size:14px;cursor:pointer;line-height:1.5;border-radius:8px}.crm-sidebar-group>summary::-webkit-details-marker{display:none}
.crm-sidebar-group>summary>svg:first-child{width:19px;height:19px;flex:0 0 19px;fill:none;stroke:currentColor;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round}.crm-sidebar-group>summary>span{flex:1;min-width:0;overflow-wrap:anywhere}.crm-sidebar-group>summary>.crm-sidebar-chevron{width:15px;height:15px;flex:0 0 15px;fill:none;stroke:currentColor;stroke-width:1.8}.crm-sidebar-group[open]>summary>.crm-sidebar-chevron{transform:rotate(90deg)}
.crm-sidebar-group>summary:hover{background:var(--admin-hover,var(--admin-primary-soft));color:var(--admin-ink)}.crm-sidebar-group[data-active=true]>summary{color:var(--admin-primary-dark);background:var(--admin-primary-soft);font-weight:600}
.crm-sidebar-submenu{display:grid;gap:3px;margin:5px 14px 9px 41px;padding-left:13px;border-left:1px solid var(--admin-line)}.admin-nav .crm-sidebar-submenu>a{min-height:40px;padding:8px 10px;font-size:12px;line-height:1.5;border-radius:8px;overflow-wrap:anywhere;white-space:normal;gap:8px}.admin-nav .crm-sidebar-submenu>a:before{content:"";position:static;width:4px;height:4px;flex:0 0 4px;border-radius:50%;background:currentColor;opacity:.6}.admin-nav .crm-sidebar-submenu>a[aria-current=page]{background:var(--admin-primary-soft);color:var(--admin-primary-dark);font-weight:650;box-shadow:none}
.crm-sidebar-submenu .crm-sidebar-group>summary{min-height:40px;padding:8px 10px;font-size:12px;gap:8px}.crm-sidebar-submenu .crm-sidebar-submenu{margin-left:13px;margin-right:0}.crm-sidebar-nav :is(a,summary):focus-visible{outline:2px solid var(--admin-primary-dark);outline-offset:-2px}
@media(min-width:761px){.sidebar-collapsed .crm-sidebar-group>summary{justify-content:center;padding:10px 0}.sidebar-collapsed .crm-sidebar-group>summary>span,.sidebar-collapsed .crm-sidebar-chevron{position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%)}.sidebar-collapsed .crm-sidebar-group>.crm-sidebar-submenu{display:none}}
@media(max-width:760px){.crm-sidebar-group>summary{min-height:46px}.admin-nav .crm-sidebar-submenu>a{min-height:44px}}
</style>
@pushOnce('scripts','crm-sidebar-submenus')
<script>
document.querySelector('.crm-sidebar-nav')?.addEventListener('click', event => {
    if (!event.target.closest('summary') || window.matchMedia('(max-width:760px)').matches) return;
    if (document.querySelector('[data-admin-shell]')?.classList.contains('sidebar-collapsed')) document.querySelector('[data-sidebar-collapse]')?.click();
});
</script>
@endPushOnce
