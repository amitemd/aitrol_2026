<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#192b39">
    <title>@yield('title', 'Panel general') · Aitrol</title>
    <link rel="stylesheet" href="{{ asset('dashboard.css') }}">
    <script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('dashboard.js') }}" defer></script>
</head>
<body>
    @include('partials.dashboard-icons')

    <a class="skip-link" href="#contenido">Ir al contenido</a>
    <div class="app-shell">
        <aside class="sidebar" id="navigation" aria-label="Barra lateral">
            <a class="brand" href="{{ route('dashboard.previous') }}">
                <span class="brand-mark"><svg class="icon" aria-hidden="true"><use href="#icon-pulse"/></svg></span>
                <span>Aitrol<small>Gestión hospitalaria</small></span>
            </a>
            <nav class="navigation" aria-label="Navegación principal">
                <span class="nav-label">Operación</span>
                <a class="nav-link is-current" href="{{ route('dashboard.previous') }}" aria-current="page"><svg class="icon" aria-hidden="true"><use href="#icon-grid"/></svg> Panel general</a>
                <a class="nav-link" href="{{ route('dashboard.previous') }}#pacientes"><svg class="icon" aria-hidden="true"><use href="#icon-users"/></svg> Pacientes</a>
                <a class="nav-link" href="{{ route('dashboard.previous') }}#agenda"><svg class="icon" aria-hidden="true"><use href="#icon-calendar"/></svg> Agenda</a>
                <a class="nav-link" href="{{ route('dashboard.previous') }}#balance"><svg class="icon" aria-hidden="true"><use href="#icon-wallet"/></svg> Finanzas</a>
                <a class="nav-link" href="{{ route('dashboard.previous') }}#habitaciones"><svg class="icon" aria-hidden="true"><use href="#icon-bed"/></svg> Habitaciones</a>
                <span class="nav-label">Seguimiento</span>
                <a class="nav-link" href="{{ route('dashboard.previous') }}#estadisticas"><svg class="icon" aria-hidden="true"><use href="#icon-chart"/></svg> Estadísticas</a>
                <a class="nav-link" href="{{ route('dashboard.previous') }}#reportes"><svg class="icon" aria-hidden="true"><use href="#icon-flag"/></svg> Reportes</a>
            </nav>
            <div class="sidebar-footer">
                <span class="status-dot"></span>
                <span>Modo demostración</span>
            </div>
        </aside>

        <div class="workspace">
            <header class="topbar">
                <button class="icon-button mobile-menu" type="button" aria-label="Menú de navegación" aria-controls="navigation" aria-expanded="false">
                    <svg class="icon" aria-hidden="true"><use href="#icon-menu"/></svg>
                </button>
                <div class="section-search">
                    <label for="section-search"><svg class="icon" aria-hidden="true"><use href="#icon-search"/></svg><span class="sr-only">Buscar una sección</span></label>
                    <input id="section-search" type="search" placeholder="Buscar una sección" autocomplete="off" aria-controls="search-results">
                    <ul class="search-results" id="search-results" aria-label="Secciones encontradas" hidden></ul>
                </div>
                <div class="top-actions">
                    <button class="primary-button" type="button" data-dialog-open="patient-dialog"><svg class="icon" aria-hidden="true"><use href="#icon-plus"/></svg> <span>Nuevo paciente</span></button>
                    <a class="icon-button notifications-link" href="{{ route('dashboard.previous') }}#reportes" aria-label="Ir a los reportes recientes"><svg class="icon" aria-hidden="true"><use href="#icon-bell"/></svg><span class="notification-dot"></span></a>
                    <span class="avatar" aria-label="Administración" title="Administración">AD</span>
                </div>
            </header>

            <main id="contenido" class="main-content">
                @yield('content')
            </main>
        </div>
    </div>

    <dialog id="patient-dialog" class="modal" aria-labelledby="patient-dialog-title">
        <h2 id="patient-dialog-title">Nuevo paciente</h2>
        <p>El registro de pacientes estará disponible cuando se conecte el módulo clínico.</p>
        <form method="dialog"><button class="primary-button" type="submit">Cerrar</button></form>
    </dialog>
</body>
</html>
