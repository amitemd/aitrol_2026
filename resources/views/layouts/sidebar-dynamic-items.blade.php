@foreach($menuItems as $menuItem)
    @if(count($menuItem['children']))
        <li class="treeview dynamic-sidebar-item {{ $menuItem['active'] ? 'active' : '' }}" data-menu-key="{{ $menuItem['key'] }}">
            <a href="#">
                <i class="{{ $menuItem['icon'] }}"></i>
                <span>{{ $menuItem['label'] }}</span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                @include('layouts.sidebar-dynamic-items', ['menuItems' => $menuItem['children']])
            </ul>
        </li>
    @else
        <li class="container-clip dynamic-sidebar-item {{ $menuItem['active'] ? 'active' : '' }}" data-menu-key="{{ $menuItem['key'] }}">
            @if($menuItem['pinnable'] && $menuItem['url'] !== '#')
                <button type="button" style="background:transparent;border:0" aria-label="Fijar {{ $menuItem['label'] }}">
                    <i onclick="anclarTarget(this, '{{ $menuItem['url'] }}', 'Menú', '{{ addslashes($menuItem['label']) }}')" style="color:white" class="fa fa-thumb-tack" aria-hidden="true"></i>
                </button>
            @endif
            <a href="{{ $menuItem['url'] }}">
                <i class="{{ $menuItem['icon'] }}"></i>
                <span>{{ $menuItem['label'] }}</span>
            </a>
        </li>
    @endif
@endforeach
