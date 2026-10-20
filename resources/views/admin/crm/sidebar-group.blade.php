<details class="crm-sidebar-group" data-menu="{{$menu['label']}}" data-active="{{$menu['active']?'true':'false'}}" @if($menu['active']) open @endif>
    <summary title="{{$menu['label']}}">
        @if(isset($menu['icon']))<svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{$menu['icon']}}"/></svg>@endif
        <span>{{$menu['label']}}</span><svg class="crm-sidebar-chevron" viewBox="0 0 16 16" aria-hidden="true"><path d="m6 3 5 5-5 5"/></svg>
    </summary>
    <div class="crm-sidebar-submenu">
        @foreach($menu['children'] as $child)
            @if(isset($child['children'])) @include('admin.crm.sidebar-group',['menu'=>$child])
            @else <a href="{{$child['url']}}" @if($child['active']) aria-current="page" @endif>{{$child['label']}}</a>
            @endif
        @endforeach
    </div>
</details>
