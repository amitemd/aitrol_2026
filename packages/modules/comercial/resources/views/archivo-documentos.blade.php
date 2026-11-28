@extends('layouts.adminlte')

@section('title', $titulo)

@push('scripts')
    <script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('archivo-documentos.js') }}?v={{ filemtime(public_path('archivo-documentos.js')) }}" defer></script>
@endpush

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => $titulo, 'subtitulo' => ucfirst($directorio).' / Documentos', 'icono' => 'bi-file-earmark-text',
    ])

    <div class="app-content archivo-documentos" data-archivo-documentos>
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
            <div class="archivo-documentos__cuadricula">
                @forelse ($documentos as $documento)
                    <div class="archivo-documentos__ficha">
                        <button type="button" class="card archivo-documentos__tarjeta mb-0"
                            data-archivo-abrir="{{ $tipoDocumento }} {{ $documento['numero'] }}" aria-haspopup="dialog">
                            <span class="archivo-documentos__portada" aria-hidden="true">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </span>
                            <span class="archivo-documentos__datos">
                                <span class="small archivo-documentos__tipo">{{ $tipoDocumento }}</span>
                                <strong class="d-block text-break">{{ $documento['numero'] }}</strong>
                                <span class="small text-body-secondary text-break">{{ $documento['tercero'] }}</span>
                                <span class="small text-body-secondary">
                                    Emisión <time datetime="{{ $documento['fecha'] }}">{{ \Illuminate\Support\Carbon::parse($documento['fecha'])->format('d/m/Y') }}</time>
                                </span>
                                <span class="archivo-documentos__pie">
                                    <span class="badge rounded-pill {{ $documento['anulado_at'] ? 'text-bg-secondary' : 'text-bg-success' }}">
                                        {{ $documento['anulado_at'] ? 'Anulado' : 'Vigente' }}
                                    </span>
                                    <strong class="archivo-documentos__total">USD {{ $documento['total'] }}</strong>
                                </span>
                            </span>
                        </button>
                        <template>
                            @if ($visorFactura)
                                @include('comercial::visor-factura')
                            @else
                                <article class="archivo-documentos__hoja" data-bs-theme="light">
                                    @include('comercial::estado-sri', ['documentoSri' => $documento])
                                    @include('comercial::contenido-documento')
                                </article>
                            @endif
                        </template>
                    </div>
                @empty
                    <div class="card archivo-documentos__vacio">
                        <p class="card-body text-center text-body-secondary mb-0">No hay documentos para los filtros y el contexto seleccionados.</p>
                    </div>
                @endforelse
            </div>
            @if ($documentos->isNotEmpty())
                @if (!empty($logoFactura))
                    <template data-archivo-logo>
                        <img class="visor-factura__logo" src="{{ $logoFactura }}" alt="">
                    </template>
                @endif
                <dialog class="cliente-dialog cliente-dialog-redisenado archivo-documentos__dialogo" data-archivo-dialogo
                    aria-labelledby="archivo-documentos-titulo">
                    <div class="d-flex align-items-center justify-content-between gap-3 cliente-dialog-cabecera">
                        <h2 id="archivo-documentos-titulo" class="fs-5 fw-semibold mb-0"></h2>
                        <form method="dialog">
                            <button type="submit" class="cliente-dialog-cerrar" aria-label="Cerrar" autofocus>
                                <i class="bi bi-x-lg" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                    <div class="cliente-dialog-cuerpo archivo-documentos__visor" data-archivo-contenido></div>
                </dialog>
            @endif
            @if ($documentos->hasPages())
                <div class="mt-3">{{ $documentos->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>
@endsection
