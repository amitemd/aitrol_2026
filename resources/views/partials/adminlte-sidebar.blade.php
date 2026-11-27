<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link" data-workspace-link="dashboard">
            @include('partials.aitrol-mark')
            <span class="brand-text aitrol-wordmark">Aitrol</span>
        </a>
    </div>
    <div class="sidebar-search" role="search">
        <label for="sidebar-search-input" class="visually-hidden">{{ __('Buscar en el menú') }}</label>
        <div class="aitrol-menu-search">
            <i class="bi bi-search" aria-hidden="true"></i>
            <input type="search" id="sidebar-search-input" class="form-control form-control-sm"
                placeholder="{{ __('Buscar en el menú') }}" autocomplete="off" data-lte-toggle="sidebar-search" data-lte-target="#navigation">
        </div>
        <p class="fs-7 text-secondary mt-2 mb-0" data-lte-search-empty role="status" hidden>{{ __('Sin coincidencias.') }}</p>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2" aria-label="{{ __('Menú principal') }}">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" data-accordion="false" id="navigation">
                @foreach ($navegacion[0] ?? [] as $item)
                    @include('partials.adminlte-menu-opcion', ['item' => $item])
                @endforeach
            </ul>
        </nav>
    </div>
</aside>
