<nav class="app-header navbar navbar-expand" aria-label="Barra de herramientas">
    <div class="container-fluid aitrol-toolbar">
        <div class="aitrol-toolbar-group">
            <button class="nav-link aitrol-tool" type="button" data-lte-toggle="sidebar"
                aria-label="Mostrar u ocultar menú" title="Menú">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>
        </div>

        <form class="aitrol-header-search" role="search" aria-label="Buscar módulos" data-workspace-search hidden>
            <label class="visually-hidden" for="navbar-search-input">Buscar módulos</label>
            <i class="bi bi-search" aria-hidden="true"></i>
            <input class="form-control" id="navbar-search-input" type="search" maxlength="120"
                placeholder="Buscar módulos…" autocomplete="off" aria-controls="workspace-search-results">
            <div class="aitrol-search-results" id="workspace-search-results" hidden>
                <p class="aitrol-search-empty mb-0" data-search-empty>Sin coincidencias.</p>
                <ul class="list-unstyled mb-0" aria-label="Resultados de búsqueda" data-search-links></ul>
            </div>
            <span class="visually-hidden" role="status" data-search-status></span>
        </form>

        <div class="workspace-tabs" data-workspace-tabs role="group" aria-label="Módulos abiertos" hidden></div>

        <ul class="navbar-nav aitrol-toolbar-group aitrol-tools">
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
                    <i class="bi bi-sun" data-lte-theme-icon="light" aria-hidden="true"></i>
                    <i class="bi bi-moon-stars d-none" data-lte-theme-icon="dark" aria-hidden="true"></i>
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
                @php
                    $cuenta = auth()->user();
                    $iniciales = Str::of($cuenta->name)->squish()->explode(' ')->take(2)
                        ->map(fn (string $parte): string => Str::upper(Str::substr($parte, 0, 1)))->implode('');
                @endphp
                <li class="nav-item dropdown">
                    <button id="aitrol-account-toggle" type="button" class="nav-link aitrol-account" data-bs-toggle="dropdown"
                        aria-expanded="false" aria-label="Menú de usuario">
                        <span class="aitrol-avatar" aria-hidden="true">{{ $iniciales }}</span>
                        <span class="aitrol-account-name d-none d-sm-inline">{{ $cuenta->name }}</span>
                        <i class="bi bi-chevron-down aitrol-account-chevron" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end aitrol-profile-menu" aria-labelledby="aitrol-account-toggle">
                        <li class="aitrol-profile-identity">
                            <span class="aitrol-avatar aitrol-profile-avatar" aria-hidden="true">{{ $iniciales }}</span>
                            <strong class="aitrol-profile-name">{{ $cuenta->name }}</strong>
                            <span class="aitrol-profile-role">
                                <i class="bi {{ $cuenta->is_admin ? 'bi-shield-check' : 'bi-person' }}" aria-hidden="true"></i>
                                {{ $cuenta->is_admin ? 'Administrador' : 'Usuario' }}
                            </span>
                        </li>
                        <li class="aitrol-profile-email">
                            <i class="bi bi-envelope" aria-hidden="true"></i>
                            <div>
                                <span class="d-block small text-body-secondary">Correo electrónico</span>
                                {{ $cuenta->email }}
                            </div>
                        </li>
                        <li class="aitrol-profile-actions">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item aitrol-profile-signout">
                                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i> Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            @endauth
        </ul>
    </div>
</nav>
