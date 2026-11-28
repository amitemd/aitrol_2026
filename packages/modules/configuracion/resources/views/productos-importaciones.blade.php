@extends('layouts.adminlte')

@section('title', 'Importar productos')

@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Importar productos', 'subtitulo' => 'Configuración · Importaciones', 'icono' => 'bi-box-seam'])

    <div class="app-content">
        <div class="container-fluid">
            @if (session('estado'))
                <p class="alert alert-success" role="status">{{ session('estado') }}</p>
            @endif
            <section class="card mb-4">
                <div class="card-body">
                    <a class="btn btn-outline-primary mb-3" href="{{ route('configuracion.importaciones.productos.plantilla') }}" download>
                        <i class="bi bi-download me-1" aria-hidden="true"></i> Descargar plantilla Excel
                    </a>
                    <form action="{{ route('configuracion.importaciones.productos.store') }}" method="post" enctype="multipart/form-data" data-importacion-carga>
                        @csrf
                        <label for="archivo-productos" class="form-label fw-semibold">Archivo de productos</label>
                        <div class="d-flex flex-wrap flex-md-nowrap gap-2">
                            <input type="file" id="archivo-productos" name="archivo" accept=".xlsx,.csv" required
                                class="form-control @error('archivo') is-invalid @enderror" aria-describedby="formato-productos">
                            <button type="submit" class="btn btn-primary text-nowrap">
                                <i class="bi bi-upload me-1" aria-hidden="true"></i> Importar en segundo plano
                            </button>
                        </div>
                        @error('archivo') <p class="text-danger small mt-2 mb-0">{{ $message }}</p> @enderror
                        <p id="formato-productos" class="form-text mb-0">Llena la hoja Productos y sube el mismo archivo .xlsx. Completa código, nombre y unidad.
                            Las unidades y marcas nuevas se crean en tu empresa. Si indicas una categoría, créala primero en
                            <a href="{{ route('configuracion.importaciones.index') }}">Importaciones</a>.
                            Puedes incluir la fecha de vencimiento, el registro sanitario y las referencias externas de la plantilla.
                            Hasta 4 MB y 20.000 filas. Los códigos existentes se conservan. También se aceptan CSV UTF-8 separados por punto y coma.</p>
                        <div class="mt-3" data-importacion-carga-estado hidden>
                            <p class="small mb-1" id="productos-carga-etiqueta" role="status">Subiendo el archivo…</p>
                            <progress class="importacion-barra w-100 d-block" aria-labelledby="productos-carga-etiqueta"></progress>
                        </div>
                    </form>
                </div>
            </section>
            <div data-importacion-estado="{{ route('configuracion.importaciones.productos.estado') }}">
                @include('configuracion::importaciones-estado', ['tareas' => $tareas])
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('importaciones.css') }}?v={{ filemtime(public_path('importaciones.css')) }}">
@endpush

@push('scripts')
    <script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('importaciones.js') }}?v={{ filemtime(public_path('importaciones.js')) }}" defer></script>
@endpush
