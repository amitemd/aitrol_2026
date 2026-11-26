<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <img src="{{ asset('adminlte/assets/img/AdminLTELogo.png') }}" alt="" class="brand-image opacity-75 shadow">
            <span class="brand-text fw-light">Aitrol</span>
        </a>
    </div>
    <div class="sidebar-search" role="search">
        <label for="sidebar-search-input" class="visually-hidden">Filtrar menú</label>
        <input type="search" id="sidebar-search-input" class="form-control form-control-sm"
            placeholder="Filtrar menú…" autocomplete="off" data-lte-toggle="sidebar-search" data-lte-target="#navigation">
        <p class="fs-7 text-secondary mt-2 mb-0" data-lte-search-empty role="status" hidden>Sin coincidencias.</p>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2" aria-label="Menú principal">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" data-accordion="false" id="navigation">
                @foreach ($navegacion[0] ?? [] as $item)
                    @if ($item->ruta === null)
                        @php
                            $hijos = collect($navegacion[$item->id] ?? [])->filter(fn ($hijo) => Route::has($hijo->ruta));
                            $abierto = $hijos->contains(fn ($hijo) => request()->routeIs($hijo->ruta));
                        @endphp
                        <li @class(['nav-item', 'menu-open' => $abierto])>
                            @if ($hijos->isNotEmpty())
                                <a href="#" @class(['nav-link', 'active' => $abierto]) aria-expanded="{{ $abierto ? 'true' : 'false' }}">
                                    <i class="nav-icon bi {{ $item->icono }}" aria-hidden="true"></i>
                                    <p class="text-wrap text-break pe-3">{{ $item->nombre }} <i class="nav-arrow bi bi-chevron-right" aria-hidden="true"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @foreach ($hijos as $hijo)
                                        @include('partials.adminlte-menu-enlace', ['enlace' => $hijo])
                                    @endforeach
                                </ul>
                            @else
                                <div class="nav-link">
                                    <i class="nav-icon bi {{ $item->icono }}" aria-hidden="true"></i>
                                    <p class="text-wrap text-break">{{ $item->nombre }}</p>
                                </div>
                            @endif
                        </li>
                    @elseif (Route::has($item->ruta))
                        @include('partials.adminlte-menu-enlace', ['enlace' => $item])
                    @endif
                @endforeach
                @auth
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link border-0 bg-transparent text-start w-100">
                                <i class="nav-icon bi bi-box-arrow-right" aria-hidden="true"></i>
                                <span>Cerrar sesión</span>
                            </button>
                        </form>
                    </li>
                @endauth
            </ul>
        </nav>
    </div>
</aside>
