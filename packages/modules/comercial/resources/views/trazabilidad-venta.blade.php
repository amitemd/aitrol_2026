@extends('layouts.adminlte')

@section('title', 'Trazabilidad de la factura')
@section('content')
    <div class="app-content py-3">
        <div class="container-fluid">
            <section class="card" data-ajax-form>
                <h1 class="card-header fs-5">Trazabilidad de la factura</h1>
                <div class="card-body">
                    <p class="fw-semibold">{{ $factura->numero }} · {{ $factura->tercero }}</p>
                    <dl class="row">
                        <dt class="col-sm-4">Registro</dt><dd class="col-sm-8">{{ $factura->created_at }}</dd>
                        <dt class="col-sm-4">Asiento</dt><dd class="col-sm-8">{{ $factura->numero_asiento ?? 'Pendiente de contabilizar' }}</dd>
                        @if ($factura->anulado_at)
                            <dt class="col-sm-4">Anulación</dt><dd class="col-sm-8">{{ $factura->anulado_at }} · {{ $factura->motivo_anulacion }}</dd>
                            <dt class="col-sm-4">Asiento de reversión</dt><dd class="col-sm-8">{{ $factura->numero_asiento_reversion ?? '—' }}</dd>
                        @endif
                        @if ($factura->lineas->contains('id_operacion', '!=', null))
                            <dt class="col-sm-4">Movimientos de inventario</dt>
                            <dd class="col-sm-8">
                                {{ $factura->lineas->pluck('id_operacion')->filter()->unique()->implode(', ') }}
                                @if ($factura->lineas->pluck('id_operacion_reversa')->filter()->isNotEmpty())
                                    <span class="d-block">Reversiones: {{ $factura->lineas->pluck('id_operacion_reversa')->filter()->unique()->implode(', ') }}</span>
                                @endif
                            </dd>
                        @endif
                    </dl>
                    <h2 class="h6">Documentos vinculados</h2>
                    <ul class="list-group list-group-flush">
                        @forelse ($vinculados as $documento)
                            <li class="list-group-item px-0">
                                <strong>{{ $documento->numero }}</strong> · {{ config('comercial.ventas.secciones.'.$documento->tipo.'.0') }}
                                <small class="d-block text-body-secondary">{{ $documento->fecha }} · {{ $documento->anulado_at ? 'Anulado' : 'Vigente' }}
                                    @if ($documento->numero_asiento) · Asiento {{ $documento->numero_asiento }} @endif
                                </small>
                            </li>
                        @empty
                            <li class="list-group-item px-0 text-body-secondary">Sin notas ni retenciones vinculadas.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('ventas.documentos.index') }}" class="btn btn-outline-secondary" data-ajax-close>Cerrar</a>
                </div>
            </section>
        </div>
    </div>
@endsection
