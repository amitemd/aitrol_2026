@extends('layouts.adminlte')

@section('title', $titulo)

@push('styles')
    <link rel="stylesheet" href="{{ asset('archivo-documentos.css') }}?v={{ filemtime(public_path('archivo-documentos.css')) }}">
@endpush

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => $titulo, 'subtitulo' => ucfirst($directorio).' / Documentos', 'icono' => 'bi-file-earmark-text',
    ])

    <div class="app-content archivo-documentos">
        <div class="container-fluid">
            <form method="GET" action="{{ route($ruta) }}" class="card card-body mb-3">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
                @endif
                <div class="row g-3 align-items-end">
                    <div class="col-sm-12 col-lg-5">
                        <label class="form-label" for="archivo-busqueda">Buscar</label>
                        <input id="archivo-busqueda" type="search" name="q" maxlength="120" class="form-control"
                            placeholder="Número o {{ strtolower(config('comercial.'.$modulo.'.tercero')) }}" value="{{ $filtros['q'] ?? '' }}">
                    </div>
                    <div class="col-sm-5 col-lg-2">
                        <label class="form-label" for="archivo-desde">Desde</label>
                        <input id="archivo-desde" type="date" name="desde" class="form-control" value="{{ $filtros['desde'] ?? '' }}">
                    </div>
                    <div class="col-sm-5 col-lg-2">
                        <label class="form-label" for="archivo-hasta">Hasta</label>
                        <input id="archivo-hasta" type="date" name="hasta" class="form-control" value="{{ $filtros['hasta'] ?? '' }}">
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button class="btn btn-outline-primary" type="submit">Filtrar</button>
                        @if (isset($filtros['q']) || isset($filtros['desde']) || isset($filtros['hasta']))
                            <a class="btn btn-outline-secondary" href="{{ route($ruta) }}">Limpiar</a>
                        @endif
                    </div>
                </div>
            </form>

            <p class="small text-body-secondary mb-2">{{ $documentos->total() }} {{ $documentos->total() === 1 ? 'documento' : 'documentos' }}</p>
            <div class="d-grid gap-2">
                @forelse ($documentos as $documento)
                    <details class="card archivo-documentos__ficha mb-0">
                        <summary class="archivo-documentos__resumen">
                            <span class="archivo-documentos__icono" aria-hidden="true"><i class="bi bi-file-earmark-text"></i></span>
                            <span class="archivo-documentos__identidad">
                                <strong class="d-block text-break">{{ $documento['numero'] }}</strong>
                                <span class="text-body-secondary text-break">{{ $documento['tercero'] }}</span>
                            </span>
                            <span class="archivo-documentos__fecha text-body-secondary">
                                <time datetime="{{ $documento['fecha'] }}">{{ \Illuminate\Support\Carbon::parse($documento['fecha'])->format('d/m/Y') }}</time>
                            </span>
                            <span class="badge rounded-pill {{ $documento['anulado_at'] ? 'text-bg-secondary' : 'text-bg-success' }}">
                                {{ $documento['anulado_at'] ? 'Anulado' : 'Vigente' }}
                            </span>
                            <strong class="archivo-documentos__total text-end">USD {{ $documento['total'] }}</strong>
                            <i class="bi bi-chevron-down archivo-documentos__flecha" aria-hidden="true"></i>
                        </summary>
                        <div class="archivo-documentos__visor">
                            <article class="archivo-documentos__hoja" data-bs-theme="light" aria-label="{{ $documento['numero'] }}">
                                <header class="archivo-documentos__encabezado">
                                    <h2 class="h5 mb-0">{{ $tipoDocumento }}</h2>
                                </header>
                                @include('comercial::contenido-documento')
                            </article>
                        </div>
                    </details>
                @empty
                    <div class="card">
                        <p class="card-body text-center text-body-secondary mb-0">No hay documentos para los filtros y el contexto seleccionados.</p>
                    </div>
                @endforelse
            </div>
            @if ($documentos->hasPages())
                <div class="mt-3">{{ $documentos->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>
@endsection
