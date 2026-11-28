@extends('layouts.adminlte')

@section('title', 'Importaciones')

@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Importaciones', 'subtitulo' => 'Configuración', 'icono' => 'bi-cloud-arrow-up'])

    <div class="app-content">
        <div class="container-fluid">
            <div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
                <div class="col">
                    <section class="card h-100">
                        <div class="card-body d-flex flex-column align-items-start gap-2">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <h2 class="h5 mb-0"><i class="bi bi-box-seam me-2" aria-hidden="true"></i>Productos y servicios</h2>
                                <span class="badge text-bg-{{ $productos > 0 ? 'success' : 'warning' }}">
                                    {{ $productos > 0 ? trans_choice(':count registro activo|:count registros activos', $productos) : 'Sin registros activos' }}
                                </span>
                            </div>
                            <p class="text-body-secondary">Llena la plantilla Excel con los códigos, unidades y precios del catálogo de tu empresa.</p>
                            <a class="btn btn-primary mt-auto" href="{{ route('configuracion.importaciones.productos.index') }}">Importar productos</a>
                        </div>
                    </section>
                </div>
                @can('administrar-personas')
                    <div class="col">
                        <section class="card h-100">
                            <div class="card-body d-flex flex-column align-items-start gap-2">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <h2 class="h5 mb-0"><i class="bi bi-person-vcard me-2" aria-hidden="true"></i>Personas</h2>
                                    <span class="badge text-bg-{{ $personas ? 'success' : 'warning' }}">
                                        {{ $personas ? 'Catálogo con registros' : 'Sin personas identificadas' }}
                                    </span>
                                </div>
                                <p class="text-body-secondary">Carga el catálogo global de personas desde Excel o revisa el avance de una importación.</p>
                                <a class="btn btn-primary mt-auto" href="{{ route('configuracion.personas.importaciones.index') }}">Importar personas</a>
                            </div>
                        </section>
                    </div>
                @endcan
            </div>

            <section class="card">
                <div class="card-header"><h2 class="h5 mb-0">Datos para empezar a operar</h2></div>
                <div class="list-group list-group-flush">
                    @foreach ($pasos as [$titulo, $listo, $descripcion, $ruta])
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3"
                            href="{{ route($ruta, $ruta === 'configuracion.contexto.index' ? ['desde' => 'importaciones'] : []) }}">
                            <span><strong class="d-block">{{ $titulo }}</strong><small class="text-body-secondary">{{ $descripcion }}</small></span>
                            <span class="d-flex align-items-center gap-2">
                                <span class="badge text-bg-{{ $listo ? 'success' : 'warning' }}">{{ $listo ? 'Configurado' : 'Pendiente' }}</span>
                                <i class="bi bi-chevron-right" aria-hidden="true"></i>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection
