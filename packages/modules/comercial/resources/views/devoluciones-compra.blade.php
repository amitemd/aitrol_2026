@extends('layouts.adminlte')

@section('title', 'Devoluciones · '.$factura['numero'])
@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Devoluciones de mercadería', 'subtitulo' => $factura['numero'].' · '.$factura['proveedor'], 'icono' => 'bi-box-arrow-up'])
    <div class="app-content">
        <div class="container-fluid">
            @include('comercial::errores')
            @if (session('status'))
                <p class="alert alert-success" role="status">{{ session('status') }}</p>
            @endif
            @if (!$factura['anulada'] && $lineas->contains(fn ($linea) => $linea['cantidad_disponible'] !== '0.000')
                && Gate::allows('permiso', ['compras.notas-credito.index', 'escribir'])
                && Gate::allows('permiso', ['inventarios.movimientos.index', 'escribir']))
                <form class="card mb-3" method="POST" action="{{ route('compras.documentos.devoluciones.store', $factura['id']) }}">
                    @csrf
                    <div class="card-body">
                        <p>La salida se registra hoy en la bodega de la recepción. Ingresa el número de la nota de crédito del proveedor.</p>
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label for="devolucion-numero" class="form-label">Número de nota de crédito</label>
                                <input id="devolucion-numero" name="numero" value="{{ old('numero') }}" class="form-control" maxlength="60" required>
                            </div>
                            <div class="col-md-7">
                                <label for="devolucion-motivo" class="form-label">Motivo</label>
                                <input id="devolucion-motivo" name="motivo" value="{{ old('motivo') }}" class="form-control" maxlength="250" required>
                            </div>
                            @foreach ($lineas->where('cantidad_disponible', '!=', '0.000') as $linea)
                                <div class="col-md-6 col-xl-4">
                                    <label class="form-label" for="devolver-{{ $linea['id_linea_recepcion'] }}">
                                        {{ $linea['descripcion'] }} · {{ $linea['bodega'] }} · Recepción #{{ $linea['id_recepcion'] }}
                                    </label>
                                    <input type="hidden" name="lineas[{{ $loop->index }}][id_linea_recepcion]" value="{{ $linea['id_linea_recepcion'] }}">
                                    <input type="number" id="devolver-{{ $linea['id_linea_recepcion'] }}" name="lineas[{{ $loop->index }}][cantidad]"
                                        class="form-control" min="0.001" step="0.001" max="{{ $linea['cantidad_disponible'] }}" placeholder="Máx. {{ $linea['cantidad_disponible'] }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Registrar devolución y nota de crédito</button></div>
                </form>
            @endif
            <section class="card">
                <h2 class="card-header h5">Notas de crédito por devoluciones</h2>
                <ul class="list-group list-group-flush">
                    @forelse ($notas as $nota)
                        <li class="list-group-item">
                            {{ $nota['numero'] }} · {{ $nota['fecha'] }} · USD {{ $nota['total'] }} · {{ $nota['anulada'] ? 'Anulada' : 'Vigente' }}
                            <small class="d-block text-body-secondary">{{ $nota['motivo'] }}</small>
                            @if (!$nota['anulada'] && Gate::allows('permiso', ['compras.notas-credito.index', 'actualizar'])
                                && Gate::allows('permiso', ['inventarios.movimientos.index', 'escribir']))
                                <a href="{{ route('compras.notas-credito.confirmar', $nota['id']) }}" data-ajax-modal>Anular nota y devolver existencias a bodega</a>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item text-body-secondary">Todavía no se han registrado devoluciones.</li>
                    @endforelse
                </ul>
                @if ($notas->hasPages())
                    <div class="card-footer">{{ $notas->links('pagination::bootstrap-5') }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection
