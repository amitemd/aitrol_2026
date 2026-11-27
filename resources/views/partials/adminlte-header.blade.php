<nav class="app-header navbar navbar-expand" aria-label="{{ __('Barra de herramientas') }}"
    @if (session('avatarActualizado')) data-avatar-guardado="{{ session('avatarActualizado') }}" @endif>
    <div class="container-fluid aitrol-toolbar">
        <div class="aitrol-toolbar-group">
            <button class="nav-link aitrol-tool" type="button" data-lte-toggle="sidebar"
                aria-label="{{ __('Mostrar u ocultar menú') }}" title="{{ __('Menú') }}">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>
        </div>

        <form class="aitrol-header-search" role="search" aria-label="{{ __('Buscar módulos') }}" data-workspace-search hidden>
            <label class="visually-hidden" for="navbar-search-input">{{ __('Buscar módulos') }}</label>
            <i class="bi bi-search" aria-hidden="true"></i>
            <input class="form-control" id="navbar-search-input" type="search" maxlength="120"
                placeholder="Buscar módulos…" autocomplete="off" aria-controls="workspace-search-results">
            <div class="aitrol-search-results" id="workspace-search-results" hidden>
                <p class="aitrol-search-empty mb-0" data-search-empty>{{ __('Sin coincidencias.') }}</p>
                <ul class="list-unstyled mb-0" aria-label="{{ __('Resultados de búsqueda') }}" data-search-links></ul>
            </div>
            <span class="visually-hidden" role="status" data-search-status></span>
        </form>

        <div class="workspace-tabs" data-workspace-tabs role="group" aria-label="{{ __('Módulos abiertos') }}" hidden></div>

        <ul class="navbar-nav aitrol-toolbar-group aitrol-tools">
            @isset($empresaOperativa)
                <li class="nav-item dropdown">
                    <button id="aitrol-contexto-toggle" class="nav-link aitrol-contexto" type="button"
                        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"
                        aria-label="{{ $empresaOperativa }}, sucursal {{ $sucursalOperativa->nombre }}, período {{ $periodoOperativo }}">
                        <i class="bi bi-buildings" aria-hidden="true"></i>
                        <span class="aitrol-contexto-empresa">{{ $empresaOperativa }}</span>
                        <span class="aitrol-contexto-sucursal">{{ $sucursalOperativa->nombre }}</span>
                        <span class="aitrol-contexto-periodo">{{ $periodoOperativo }}</span>
                        <i class="bi bi-chevron-down d-none d-lg-inline" aria-hidden="true"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end aitrol-contexto-menu p-3"
                        aria-labelledby="aitrol-contexto-toggle" data-select2-contenedor>
                        <span class="text-body-secondary small">{{ __('Empresa') }}</span>
                        <strong class="d-block text-break mb-3">{{ $empresaOperativa }}</strong>
                        <form method="POST" action="{{ route('configuracion.contexto.update') }}">
                            @csrf
                            <label class="form-label" for="aitrol-contexto-sucursal">{{ __('Sucursal') }}</label>
                            <select name="id_sucursal" id="aitrol-contexto-sucursal" class="form-select mb-3" data-select2-nativo required>
                                @foreach ($sucursalesOperativas as $opcion)
                                    <option value="{{ $opcion->id }}" @selected($sucursalOperativa->id === $opcion->id)>{{ $opcion->nombre }}</option>
                                @endforeach
                            </select>
                            <label class="form-label" for="aitrol-contexto-periodo">{{ __('Año del período') }}</label>
                            <div class="mb-3">
                                <select class="form-select" id="aitrol-contexto-periodo" name="periodo" required>
                                    @unless (in_array($periodoOperativo, $aniosPeriodoOperativo, true))
                                        <option value="" selected disabled>{{ __('Selecciona un año') }}</option>
                                    @endunless
                                    @foreach ($aniosPeriodoOperativo as $anio)
                                        <option value="{{ $anio }}" @selected($periodoOperativo === $anio)>{{ $anio }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-primary w-100" type="submit">{{ __('Aplicar contexto') }}</button>
                        </form>
                    </div>
                </li>
            @endisset
            <li class="nav-item d-none d-md-block">
                <button class="nav-link aitrol-tool" type="button" data-lte-toggle="fullscreen"
                    aria-label="{{ __('Cambiar a pantalla completa') }}" title="{{ __('Pantalla completa') }}">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen" aria-hidden="true"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none" aria-hidden="true"></i>
                </button>
            </li>
            <li class="nav-item dropdown">
                <button class="nav-link aitrol-tool" type="button" id="bd-theme" data-bs-toggle="dropdown"
                    aria-label="{{ __('Cambiar tema visual') }}" aria-expanded="false" title="{{ __('Tema visual') }}">
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
                                {{ __($nombre) }}
                                <i class="bi bi-check-lg ms-auto d-none" aria-hidden="true"></i>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </li>

            @auth
                @php
                    $cuenta = auth()->user();
                    $fotoPerfil = $cuenta->foto ? route('configuracion.usuarios.foto', $cuenta, absolute: false) : null;
                    $iniciales = Str::of($cuenta->name)->squish()->explode(' ')->take(2)
                        ->map(fn (string $parte): string => Str::upper(Str::substr($parte, 0, 1)))->implode('');
                @endphp
                <li class="nav-item dropdown">
                    <button id="aitrol-account-toggle" type="button" class="nav-link aitrol-account" data-bs-toggle="dropdown"
                        aria-expanded="false" aria-label="{{ __('Menú de usuario') }}">
                        @include('partials.avatar-cuenta')
                        <span class="aitrol-account-name d-none d-sm-inline">{{ $cuenta->name }}</span>
                        <i class="bi bi-chevron-down aitrol-account-chevron" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end aitrol-profile-menu" aria-labelledby="aitrol-account-toggle">
                        <li class="aitrol-profile-identity">
                            @include('partials.avatar-cuenta', ['claseAvatar' => 'aitrol-profile-avatar'])
                            <strong class="aitrol-profile-name">{{ $cuenta->name }}</strong>
                            <span class="aitrol-profile-role">
                                <i class="bi {{ $cuenta->is_admin ? 'bi-shield-check' : 'bi-person' }}" aria-hidden="true"></i>
                                {{ __($cuenta->is_admin ? 'Administrador' : 'Usuario') }}
                            </span>
                        </li>
                        <li class="aitrol-profile-email">
                            <i class="bi bi-envelope" aria-hidden="true"></i>
                            <div>
                                <span class="d-block small text-body-secondary">{{ __('Correo electrónico') }}</span>
                                {{ $cuenta->email }}
                            </div>
                        </li>
                        <li class="aitrol-profile-language">
                            <label for="aitrol-profile-language" class="aitrol-profile-language-title">
                                <i class="bi bi-translate" aria-hidden="true"></i>
                                {{ __('Idioma') }}
                            </label>
                            <form class="mt-2" method="POST" action="{{ route('idioma.actualizar') }}">
                                @csrf
                                <select id="aitrol-profile-language" class="form-select form-select-sm" name="idioma"
                                    data-select2-nativo data-idioma-inmediato>
                                    @foreach (config('app.idiomas') ?? ['es' => 'Español'] as $codigo => $nombreIdioma)
                                        <option value="{{ $codigo }}" lang="{{ str_replace('_', '-', $codigo) }}"
                                            @selected(app()->getLocale() === $codigo)>{{ $nombreIdioma }}</option>
                                    @endforeach
                                </select>
                                <noscript><button type="submit" class="btn btn-sm btn-outline-secondary mt-2">{{ __('Aplicar') }}</button></noscript>
                            </form>
                        </li>
                        <li class="aitrol-profile-actions">
                            <form method="POST" action="{{ route('logout') }}" data-accion-confirmar="¿Deseas salir del sistema?"
                                data-confirmar-titulo="{{ __('Cerrar sesión') }}" data-confirmar-boton="Sí, cerrar sesión" data-confirmar-cancelar="Seguir trabajando">
                                @csrf
                                <button type="submit" class="dropdown-item aitrol-profile-signout">
                                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i> {{ __('Cerrar sesión') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            @endauth
        </ul>
    </div>
</nav>
