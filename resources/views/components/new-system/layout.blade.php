@props([
    'title',
    'user',
    'companies',
    'currentCompany' => null,
])

@php
    $company = $currentCompany ?? $companies->first();
    $companyName = $company?->nombrecomercial ?: config('app.name', 'Sistema Médico');
    $companyShortName = $company?->nombre_corto ?: 'SM';
    $avatarFilename = trim((string) ($user->imagen_url ?? ''));
    $avatarPath = $avatarFilename !== '' ? public_path('avatars/'.$avatarFilename) : null;
    $avatarUrl = $avatarPath !== null && is_file($avatarPath) ? asset('avatars/'.$avatarFilename) : null;
    $userName = trim(implode(' ', array_filter([$user->nombre1, $user->apellido1])));
    $userInitials = mb_strtoupper(mb_substr((string) $user->nombre1, 0, 1).mb_substr((string) $user->apellido1, 0, 1));
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | {{ $companyName }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/app-template.css') }}?v={{ filemtime(public_path('css/app-template.css')) }}">
    <link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/new-system-legacy.css') }}?v={{ filemtime(public_path('css/new-system-legacy.css')) }}">
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <header class="main-header new-system-header">
            <a href="{{ route('new-system.dashboard') }}" class="logo" aria-label="Ir al inicio">
                <span class="logo-mini"><b>{{ mb_substr($companyShortName, 0, 1) }}</b>{{ mb_substr($companyShortName, 1, 1) }}</span>
                <span class="logo-lg">{{ $companyName }}</span>
            </a>

            <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Mostrar u ocultar navegación</span>
                </a>

                <a href="{{ route('new-system.dashboard') }}" class="new-system-brand" aria-label="AITROL">
                    <strong>AITROL</strong>
                    <span>Sistema Médico</span>
                </a>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        @if (Route::has('change_lang'))
                            <li class="dropdown ieced-language-dropdown">
                                <a href="#" class="dropdown-toggle ieced-language-trigger" data-toggle="dropdown" role="button" aria-expanded="false">
                                    <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
                                    @lang('main.idioma') <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu ieced-language-menu" role="menu">
                                    @foreach (['es' => 'Español', 'en' => 'English', 'pt' => 'Português'] as $locale => $language)
                                        <li class="{{ App::getLocale() === $locale ? 'active' : '' }}">
                                            <a href="{{ route('change_lang', ['lang' => $locale]) }}">
                                                <span class="ieced-language-code">{{ mb_strtoupper($locale) }}</span>
                                                {{ $language }}
                                                @if (App::getLocale() === $locale)
                                                    <span class="glyphicon glyphicon-ok pull-right" aria-hidden="true"></span>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif

                        <li class="dropdown user user-menu ieced-user-dropdown">
                            <a href="#" class="dropdown-toggle ieced-user-trigger" data-toggle="dropdown">
                                @if ($avatarUrl)
                                    <img src="{{ $avatarUrl }}" class="user-image" alt="Foto de {{ $userName }}">
                                @else
                                    <span class="new-system-avatar new-system-avatar-small" aria-hidden="true">{{ $userInitials }}</span>
                                @endif
                                <span class="hidden-xs">{{ $userName }}</span>
                            </a>
                            <ul class="dropdown-menu ieced-user-menu">
                                <li class="user-header ieced-user-card">
                                    @if ($avatarUrl)
                                        <img src="{{ $avatarUrl }}" class="img-circle ieced-user-avatar" alt="Foto de {{ $userName }}">
                                    @else
                                        <span class="new-system-avatar new-system-avatar-menu" aria-hidden="true">{{ $userInitials }}</span>
                                    @endif
                                    <p class="ieced-user-name">Hola {{ $userName }}</p>
                                    <small>{{ $user->email }}</small>
                                </li>
                                <li class="user-footer ieced-user-footer">
                                    @if (Route::has('gateway_tickets.create') && (int) $user->id_tipo_usuario !== 2)
                                        <a href="{{ route('gateway_tickets.create') }}" class="btn btn-default btn-flat ieced-user-action ieced-user-ticket">
                                            <span class="fa fa-ticket" aria-hidden="true"></span> Enviar ticket
                                        </a>
                                    @endif
                                    @if (Route::has('perfil.editar'))
                                        <a href="{{ route('perfil.editar') }}" class="btn btn-default btn-flat ieced-user-action">
                                            <span class="glyphicon glyphicon-user" aria-hidden="true"></span> Perfil
                                        </a>
                                    @endif
                                    <button type="submit" form="new-system-logout" class="btn btn-default btn-flat ieced-user-action ieced-user-signout">
                                        <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Cerrar sesión
                                    </button>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <form id="new-system-logout" action="{{ route('new-system.logout') }}" method="POST" class="hidden">
            @csrf
        </form>

        <aside class="main-sidebar">
            <section class="sidebar">
                <div class="user-panel">
                    <div class="pull-left image">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" class="img-circle" alt="Foto de {{ $userName }}">
                        @else
                            <span class="new-system-avatar new-system-avatar-sidebar" aria-hidden="true">{{ $userInitials }}</span>
                        @endif
                    </div>
                    <div class="pull-left info">
                        <p>{{ $userName }}</p>
                        <a href="#"><i class="fa fa-circle text-success"></i> @lang('tsidebar.online')</a>
                    </div>
                </div>

                <form action="{{ route('new-system.dashboard') }}" method="GET" class="sidebar-form" onsubmit="return false;">
                    <div class="input-group">
                        <input type="search" id="new-system-menu-search" class="form-control" placeholder="Buscador accesos directos...">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-flat" aria-label="Buscar"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>

                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">NAVEGACIÓN</li>
                    <li class="{{ request()->routeIs('new-system.dashboard') ? 'active' : '' }}" data-menu-entry>
                        <a href="{{ route('new-system.dashboard') }}"><i class="fa fa-dashboard"></i> <span>Inicio</span></a>
                    </li>
                    <li class="{{ request()->routeIs('new-system.companies.*') ? 'active' : '' }}" data-menu-entry>
                        <a href="{{ route('new-system.companies.index') }}"><i class="fa fa-building"></i> <span>Empresas</span></a>
                    </li>
                    @can('manage-new-system-users')
                        <li class="{{ request()->routeIs('new-system.users.*') ? 'active' : '' }}" data-menu-entry>
                            <a href="{{ route('new-system.users.index') }}"><i class="fa fa-users"></i> <span>Usuarios</span></a>
                        </li>
                        @if (Route::has('deployment.index'))
                            <li class="{{ request()->routeIs('deployment.*') ? 'active' : '' }}" data-menu-entry>
                                <a href="{{ route('deployment.index') }}"><i class="fa fa-cogs"></i> <span>Despliegue del sistema</span></a>
                            </li>
                        @endif
                    @endcan

                    <li class="header">MÓDULOS DEL SISTEMA</li>
                    <li class="treeview" data-menu-entry>
                        <a href="#"><i class="fa fa-medkit"></i> <span>@lang('tsidebar.atencionAlPaciente')</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu">
                            <li><a href="{{ url('orden_ingresada_formato012') }}"><i class="fa fa-check-square-o"></i> @lang('tsidebar.orden_Formato_012')</a></li>
                            <li><a href="{{ url('agenda') }}"><i class="fa fa-calendar"></i> @lang('tsidebar.agenda')</a></li>
                            <li><a href="{{ url('consultam') }}"><i class="fa fa-calendar-minus-o"></i> @lang('tsidebar.consultas_procedimientos')</a></li>
                            <li><a href="{{ url('paciente') }}"><i class="fa fa-users"></i> @lang('tsidebar.pacientes')</a></li>
                            <li><a href="{{ url('hospital') }}"><i class="fa fa-hospital-o"></i> @lang('tsidebar.Hospital')</a></li>
                        </ul>
                    </li>
                    <li class="treeview" data-menu-entry>
                        <a href="#"><i class="fa fa-user-md"></i> <span>@lang('tsidebar.personalMedico')</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu"><li><a href="{{ url('agenda') }}"><i class="fa fa-calendar"></i> @lang('tsidebar.agenda')</a></li></ul>
                    </li>
                    <li class="treeview" data-menu-entry>
                        <a href="#"><i class="fa fa-briefcase"></i> <span>@lang('tsidebar.administracion')</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu">
                            <li><a href="{{ url('orden') }}"><i class="fa fa-flask"></i> @lang('tsidebar.administracion_laboratorio')</a></li>
                            <li><a href="{{ url('examen') }}"><i class="fa fa-list"></i> @lang('tsidebar.examenes')</a></li>
                        </ul>
                    </li>
                    <li class="treeview" data-menu-entry>
                        <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>@lang('tsidebar.comercial')</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu"><li><a href="{{ url('insumos') }}"><i class="fa fa-cubes"></i> Insumos</a></li></ul>
                    </li>
                    <li class="treeview" data-menu-entry>
                        <a href="#"><i class="fa fa-usd"></i> <span>@lang('tsidebar.cuentasyFinanzas')</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu"><li><a href="{{ url('contable') }}"><i class="glyphicon glyphicon-book"></i> @lang('tsidebar.contable')</a></li></ul>
                    </li>
                    <li class="treeview" data-menu-entry>
                        <a href="#"><i class="fa fa-television"></i> <span>@lang('tsidebar.procedimientos')</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                        <ul class="treeview-menu"><li><a href="{{ url('procedimientos_dr') }}"><i class="fa fa-desktop"></i> @lang('tsidebar.control')</a></li></ul>
                    </li>
                    <li data-menu-entry>
                        <a href="{{ url('/dashboard') }}"><i class="fa fa-external-link"></i> <span>Abrir dashboard legado</span></a>
                    </li>
                </ul>
            </section>
        </aside>

        <div class="content-wrapper">
            <section class="content-header">
                <h1>{{ $title }} <small>{{ $companyName }}</small></h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('new-system.dashboard') }}"><i class="fa fa-dashboard"></i> Inicio</a></li>
                    @if (! request()->routeIs('new-system.dashboard'))
                        <li class="active">{{ $title }}</li>
                    @endif
                </ol>
            </section>

            <section class="content">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fa fa-check"></i> {{ session('status') }}
                    </div>
                @endif

                {{ $slot }}
            </section>
        </div>

        <footer class="main-footer">
            <strong>Copyright &copy; {{ date('Y') }} AITROL.</strong> Todos los derechos reservados.
            <span class="pull-right hidden-xs">Laravel 13</span>
        </footer>
    </div>

    <script src="{{ asset('js/jquery-2.1.4.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script>
        window.AdminLTEOptions = {
            enableControlTreeView: true
        };
    </script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        (function () {
            var search = document.getElementById('new-system-menu-search');

            if (!search) {
                return;
            }

            search.addEventListener('input', function () {
                var value = this.value.toLocaleLowerCase('es').trim();

                document.querySelectorAll('[data-menu-entry]').forEach(function (entry) {
                    entry.style.display = value === '' || entry.textContent.toLocaleLowerCase('es').includes(value) ? '' : 'none';
                });
            });
        }());
    </script>
</body>
</html>
