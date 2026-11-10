@php
    // Las pantallas heredadas que no vienen desde una opción concreta del menú
    // (_v3_menu=0) no deben intentar adivinar la rama activa: sus nombres
    // internos pueden coincidir con más de un ítem del árbol.
    $legacyUnmappedMenu = (bool) config('data.v3_legacy_shell')
        && (int) config('data.menu_id') === 0;
    $activeCompanyV3 = config('data.empresa');
    $activeCompanyNameV3 = trim((string) data_get($activeCompanyV3, 'nombre_corto'));
    if ($activeCompanyNameV3 === '') {
        $activeCompanyNameV3 = trim((string) data_get($activeCompanyV3, 'nombrecomercial', 'AITROL'));
    }
@endphp
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('v3/admin') }}" class="brand-link">
        <img src="{{ url('v3/') }}/dist/img/AdminLTELogo.png" alt="AITROL" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ $activeCompanyNameV3 }}</span>
    </a>
    <div class="sidebar">
        <div class="form-inline px-2 pt-2">
            <div class="input-group input-group-sm w-100 v3-menu-search" data-widget="sidebar-search">
                <div class="input-group-prepend">
                    <button class="btn btn-sidebar" type="button" aria-label="{{ trans('v3/accessibility.search_menu') }}"><i class="fas fa-search fa-fw" aria-hidden="true"></i></button>
                </div>
                <input class="form-control form-control-sidebar" type="search" placeholder="{{ trans('v3/new_menu.buscar_menu') }}" aria-label="{{ trans('v3/new_menu.buscar_menu') }}">
            </div>
        </div>
        <nav class="mt-2 TourMenu inicio_todo hero-menu v3-sidebar-nav" id="TourMenu" aria-label="{{ trans('v3/accessibility.main_menu') }}">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" data-animation-speed="0" data-current-menu-id="{{ (int) config('data.menu_id') }}" data-legacy-unmapped="{{ $legacyUnmappedMenu ? '1' : '0' }}" role="menu" data-accordion="true">
                <li class="nav-item">
                    <a href="{{ url('v3/admin') }}" class="nav-link {{ !$legacyUnmappedMenu && (int) config('data.menu_id') === 0 && config('data.submodulo') == '' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i><p>{{ trans('v3/new_menu.inicio') }}</p>
                    </a>
                </li>
                {{--
                @if(Auth::user()->canManageWaitingRoomDisplay())
                    <li class="nav-item">
                        <a href="{{ route('waiting_room_display.index') }}" class="nav-link {{ request()->is('sala/espera/pacientes*') || request()->is('sala/espera/pantalla*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tv"></i><p>Pantallas de sala de espera</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('waiting_room_display.videos') }}" class="nav-link {{ request()->is('sala/espera/videos*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-film"></i><p>Videos de sala de espera</p>
                        </a>
                    </li>
                @endif
                --}}
                <?php
                ini_set('max_execution_time', 300);
                $menus = config('data.menu');
                $permissions = config('data.permisos');
                $menuPermissionIds = $permissions && isset($permissions->id_submenu_permiso)
                    ? $permissions->id_submenu_permiso
                    : '[]';
                $submenuBuilder = new Sis_medico\v3\contable\SubMenu;
                ?>
                @if ($menus != null && $permissions !== null)
                    @foreach ($menus as $menu)
                        <?php
                        $sub = $submenuBuilder->crear_menu($menu->id_menu, '', $menu->controlador_menu, '', '', $menuPermissionIds);
                        $descendantActive = strpos($sub, 'nav-link active') !== false;
                        $configuredMenuId = (int) config('data.menu_id');
                        $menuActive = !$legacyUnmappedMenu && ($configuredMenuId > 0
                            ? ($configuredMenuId === (int) $menu->id_menu || $descendantActive)
                            : ((config('data.active_controller') ?: config('data.controlador')) == $menu->controlador_menu || $descendantActive));
                        $menuLabel = Sis_medico\v3\contable\Menu::translatedLabel($menu);
                        ?>
                        @if ($sub != '')
                            <li class="nav-item v3-menu-root {{ $menuActive ? 'menu-open' : '' }}" data-menu-id="{{ (int) $menu->id_menu }}">
                                <a href="#" class="nav-link {{ $menuActive ? 'active' : '' }}" aria-expanded="{{ $menuActive ? 'true' : 'false' }}">
                                    {!! Sis_medico\v3\contable\Menu::iconHtml($menu) !!}<p>{{ $menuLabel }}<i class="fas fa-angle-left right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">{!! $sub !!}</ul>
                            </li>
                        @endif
                    @endforeach
                @endif
            </ul>
        </nav>
    </div>
</aside>
<script>
(function($){
    'use strict';

    function closeBranch($item){
        $item.removeClass('menu-open').children('a.nav-link').attr('aria-expanded','false');
        $item.children('ul.nav-treeview').hide();
        $item.find('li.menu-open').removeClass('menu-open');
        $item.find('ul.nav-treeview').hide();
        $item.find('a.nav-link[aria-expanded]').attr('aria-expanded','false');
    }

    function openBranch($item){
        $item.addClass('menu-open');
        $item.children('a.nav-link').attr('aria-expanded','true');
        $item.children('ul.nav-treeview').show();
    }

    function activateCurrentBranch(){
        var $tree=$('#TourMenu > ul.nav-sidebar');
        if(!$tree.length)return;
        var currentId=parseInt($tree.attr('data-current-menu-id'),10)||0;
        if(!currentId && $tree.attr('data-legacy-unmapped') === '1'){
            $tree.children('li.v3-menu-root').each(function(){closeBranch($(this))});
            $tree.find('a.nav-link.active').removeClass('active');
            return;
        }
        var $serverTarget=$tree.find('a.nav-link.active').last().parent('li');
        var $target=currentId?$tree.find('li[data-menu-id="'+currentId+'"]').first():$serverTarget;

        // Si el id no existe en el catalogo local, conservar el calculo del
        // servidor y, como ultimo respaldo, resolver la opcion por su URL.
        if(!$target.length)$target=$serverTarget;
        if(!$target.length){
            $tree.find('a.nav-link[href]').each(function(){
                var raw=$(this).attr('href');
                if(!raw||raw==='#')return;
                try{
                    var candidate=new URL(raw,window.location.origin);
                    if(candidate.origin===window.location.origin
                        && candidate.pathname.replace(/\/+$/,'')===window.location.pathname.replace(/\/+$/,'')){
                        $target=$(this).parent('li');
                        return false;
                    }
                }catch(ignore){}
            });
        }
        if(!$target.length)return;

        $tree.children('li.v3-menu-root').each(function(){closeBranch($(this))});
        $tree.find('a.nav-link.active').removeClass('active').removeAttr('aria-current');
        var $branch=$target.add($target.parentsUntil($tree,'li.nav-item'));
        $branch.each(function(){
            var $item=$(this);
            $item.children('a.nav-link').addClass('active');
            if($item.children('ul.nav-treeview').length){
                openBranch($item);
            }
        });
        $target.children('a.nav-link').attr('aria-current','page');
    }

    $('#TourMenu').attr('data-v3-accordion-ready','true');
    $(window).on('load',function(){window.setTimeout(activateCurrentBranch,0)});
    $(document).off('click.v3MenuAccordion','#TourMenu li.nav-item > a.nav-link')
        .on('click.v3MenuAccordion','#TourMenu li.nav-item > a.nav-link',function(event){
        var $link=$(this);
        if(!$link.next('ul.nav-treeview').length)return;
        event.preventDefault();
        event.stopImmediatePropagation();

        var $item=$link.parent('li.nav-item');
        var wasOpen=$item.hasClass('menu-open');
        $item.siblings('li.nav-item').has('> ul.nav-treeview').each(function(){closeBranch($(this))});

        if(wasOpen){
            closeBranch($item);
        }else{
            openBranch($item);
        }
    });
})(jQuery);
</script>
