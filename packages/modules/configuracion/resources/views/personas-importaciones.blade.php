@extends('layouts.adminlte')

@section('title', 'Importar personas')

@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Importar personas', 'subtitulo' => 'Configuración · Personas', 'icono' => 'bi-person-vcard'])

    <div class="app-content">
        <div class="container-fluid">
            @if (session('estado'))
                <p class="alert alert-success" role="status">{{ session('estado') }}</p>
            @endif

            <section class="card mb-4">
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data" action="{{ route('configuracion.personas.importaciones.store') }}">
                        @csrf
                        <label for="archivo-personas" class="form-label fw-semibold">Archivo Excel de personas</label>
                        <div class="d-flex flex-wrap flex-md-nowrap gap-2">
                            <input type="file" class="form-control @error('archivo') is-invalid @enderror"
                                id="archivo-personas" name="archivo" accept=".xlsx" aria-describedby="formato-personas" required>
                            <button class="btn btn-primary text-nowrap" type="submit">
                                <i class="bi bi-upload me-1" aria-hidden="true"></i> Importar en segundo plano
                            </button>
                        </div>
                        @error('archivo') <p class="text-danger small mt-2 mb-0">{{ $message }}</p> @enderror
                        <p id="formato-personas" class="form-text mb-0">
                            Formato .xlsx de hasta 16 MB. Incluya identificación y nombres en los encabezados.
                            Las personas ya registradas se conservan.
                        </p>
                    </form>
                </div>
            </section>

            <section class="card mb-4">
                <div class="card-body">
                    <h2 class="h5">Token para la API de Personas</h2>
                    <form method="post" action="{{ route('configuracion.personas.importaciones.token') }}" data-personas-token>
                        @csrf
                        <label for="nombre-integracion-personas" class="form-label">Nombre de la integración</label>
                        <div class="d-flex flex-wrap flex-md-nowrap gap-2">
                            <input type="text" class="form-control" name="nombre" id="nombre-integracion-personas"
                                maxlength="100" placeholder="Por ejemplo, Gastroclínica" required>
                            <button type="submit" class="btn btn-outline-primary text-nowrap">Generar token</button>
                        </div>
                        <p class="form-text mb-0">Permite consultar personas por identificación durante 90 días.</p>
                        <p class="text-danger small my-2" role="alert" data-personas-token-error hidden></p>
                        <div class="mt-3" data-personas-token-resultado hidden>
                            <label for="valor-token-personas" class="form-label">Copia el token antes de salir de la pantalla</label>
                            <input type="text" class="form-control font-monospace" id="valor-token-personas" readonly autocomplete="off">
                        </div>
                    </form>
                </div>
            </section>

            <div data-personas-estado="{{ route('configuracion.personas.importaciones.estado') }}">
                @include('configuracion::personas-importaciones-estado')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('personas-importaciones.js') }}?v={{ filemtime(public_path('personas-importaciones.js')) }}" defer></script>
@endpush
