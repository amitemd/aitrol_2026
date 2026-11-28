<div class="app-content-header clientes-cabecera">
    <div class="container-fluid d-flex flex-nowrap flex-md-wrap align-items-center justify-content-between gap-2 gap-md-3">
        <div class="d-flex align-items-center gap-2 gap-md-3 listado-cabecera-titulo">
            <span class="clientes-icono" aria-hidden="true"><i class="bi {{ $icono }}"></i></span>
            <div>
                <h1 class="mb-0 fs-3 fw-semibold">{{ __($titulo) }}</h1>
                <p class="mb-0 text-body-secondary">{{ __($subtitulo) }}</p>
            </div>
        </div>
        @isset($crearUrl)
            <a href="{{ $crearUrl }}" class="btn btn-primary listado-cabecera-alta"
                @isset($crearWorkspace) data-workspace-link="{{ $crearWorkspace }}" data-workspace-label="{{ __($crearTexto) }}" data-workspace-forward
                @else data-ajax-open="{{ __($crearTexto) }}" @if ($abrirAlta ?? false) data-ajax-open-on-load="nueva" @endif
                @endisset aria-label="{{ __($crearTexto) }}">
                <i class="bi bi-plus-lg me-md-1" aria-hidden="true"></i><span class="d-none d-md-inline">{{ __($crearTexto) }}</span>
            </a>
        @endisset
    </div>
    @if (request()->routeIs('configuracion.personas.importaciones.index', 'configuracion.importaciones.productos.index',
        'configuracion.sucursales.index', 'inventarios.bodegas.index',
        'clientes.index', 'proveedores.index', 'contabilidad.integraciones.index'))
        @can('administrar-configuracion')
            <div class="container-fluid mt-2">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('configuracion.importaciones.index') }}">
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Volver a Importaciones
                </a>
            </div>
        @endcan
    @endif
</div>
