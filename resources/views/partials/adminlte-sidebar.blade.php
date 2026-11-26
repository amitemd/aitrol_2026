<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link" data-workspace-link="dashboard">
            @include('partials.aitrol-mark')
            <span class="brand-text">Aitrol</span>
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
                    @include('partials.adminlte-menu-opcion', ['item' => $item])
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
