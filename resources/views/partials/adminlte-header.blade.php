<nav class="app-header navbar navbar-expand" aria-label="Barra de herramientas">
    <div class="container-fluid aitrol-toolbar">
        <div class="aitrol-toolbar-group">
            <button class="nav-link aitrol-tool" type="button" data-lte-toggle="sidebar"
                aria-label="Mostrar u ocultar menú" title="Menú">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>
            <a class="nav-link aitrol-home" href="{{ route('dashboard') }}" data-workspace-link="dashboard"
                aria-label="Panel general" title="Panel general">
                <i class="bi bi-grid-1x2" aria-hidden="true"></i>
                <span class="d-none d-sm-inline">Inicio</span>
            </a>
        </div>

        <ul class="navbar-nav aitrol-toolbar-group ms-auto">
            <li class="nav-item d-none d-md-block">
                <button class="nav-link aitrol-tool" type="button" data-lte-toggle="fullscreen"
                    aria-label="Cambiar a pantalla completa" title="Pantalla completa">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen" aria-hidden="true"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none" aria-hidden="true"></i>
                </button>
            </li>
            <li class="nav-item dropdown">
                <button class="nav-link aitrol-tool" type="button" id="bd-theme" data-bs-toggle="dropdown"
                    aria-label="Cambiar tema visual" aria-expanded="false" title="Tema visual">
                    <i class="bi bi-sun-fill" data-lte-theme-icon="light" aria-hidden="true"></i>
                    <i class="bi bi-moon-fill d-none" data-lte-theme-icon="dark" aria-hidden="true"></i>
                    <i class="bi bi-circle-half d-none" data-lte-theme-icon="auto" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme">
                    @foreach (['light' => ['Claro', 'bi-sun'], 'dark' => ['Oscuro', 'bi-moon'], 'auto' => ['Del sistema', 'bi-circle-half']] as $tema => [$nombre, $icono])
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center gap-2"
                                data-bs-theme-value="{{ $tema }}" aria-pressed="false">
                                <i class="bi {{ $icono }}" aria-hidden="true"></i>
                                {{ $nombre }}
                                <i class="bi bi-check-lg ms-auto d-none" aria-hidden="true"></i>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </li>

            @auth
                @php($cuenta = auth()->user())
                <li class="nav-item dropdown">
                    <button type="button" class="nav-link aitrol-account" data-bs-toggle="dropdown"
                        aria-expanded="false" aria-label="Menú de usuario">
                        <span class="aitrol-avatar" aria-hidden="true">{{ Str::upper(Str::substr(trim($cuenta->name), 0, 1)) }}</span>
                        <span class="aitrol-account-name d-none d-sm-inline">{{ $cuenta->name }}</span>
                        <i class="bi bi-chevron-down" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2 text-break">
                            <strong class="d-block">{{ $cuenta->name }}</strong>
                            @if ($cuenta->is_admin)
                                <small class="text-body-secondary">Administrador</small>
                            @endif
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i> Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            @endauth
        </ul>
    </div>
</nav>
