@extends('layouts.adminlte')

@section('title', 'Documento '.$documento['numero'])
@section('content')
    <div class="app-content py-3">
        <div class="container-fluid">
            <section class="card" data-ajax-form>
                <h1 class="card-header fs-5">Documento {{ $documento['numero'] }}</h1>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Número</dt><dd class="col-sm-8">{{ $documento['numero'] }}</dd>
                        @if ($documento['numero_asiento'] || $documento['numero_asiento_reversion'])
                            <dt class="col-sm-4">{{ __('Contabilidad') }}</dt><dd class="col-sm-8">@include('partials.referencia-asiento', ['registroAsiento' => $documento])</dd>
                        @endif
                        <dt class="col-sm-4">{{ __('Fecha') }}</dt><dd class="col-sm-8">{{ $documento['fecha'] }}</dd>
                        <dt class="col-sm-4">{{ config('comercial.'.$modulo.'.tercero') }}</dt><dd class="col-sm-8">{{ $documento['tercero'] }}</dd>
                        @if ($documento['numero_origen'])
                            <dt class="col-sm-4">Documento de origen</dt><dd class="col-sm-8">{{ $documento['numero_origen'] }}</dd>
                        @endif
                        <dt class="col-sm-4">{{ __('Estado') }}</dt><dd class="col-sm-8">{{ $documento['anulado_at'] ? 'Anulado' : 'Vigente' }}</dd>
                        @if ($documento['anulado_at'])
                            <dt class="col-sm-4">Motivo de anulación</dt><dd class="col-sm-8">{{ $documento['motivo_anulacion'] }}</dd>
                            <dt class="col-sm-4">Fecha de anulación</dt><dd class="col-sm-8">{{ $documento['anulado_at'] }}</dd>
                        @endif
                        @if ($documento['observacion'])
                            <dt class="col-sm-4">Observación</dt><dd class="col-sm-8">{{ $documento['observacion'] }}</dd>
                        @endif
                    </dl>
                    @php $retencion = $documento['tipo'] === 'retenciones'; @endphp
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <caption class="visually-hidden">Detalle del documento</caption>
                            <thead><tr>
                                <th>Concepto</th>
                                <th class="text-end">{{ $retencion ? 'Base' : 'Cantidad' }}</th>
                                <th class="text-end">{{ $retencion ? 'Retención (%)' : 'Precio unitario' }}</th>
                                <th class="text-end">{{ __('Total') }}</th>
                            </tr></thead>
                            <tbody>
                                @foreach ($documento['lineas'] as $linea)
                                    <tr>
                                        <th scope="row">{{ $linea['descripcion'] }}
                                            @if ($linea['nombre_bodega'])<small class="d-block text-body-secondary">Bodega: {{ $linea['nombre_bodega'] }}</small>@endif
                                            @if ($retencion)<small class="d-block text-body-secondary">{{ Str::upper($linea['tipo_retencion']) }} · {{ $linea['codigo_retencion'] }}</small>@endif
                                        </th>
                                        <td class="text-end">{{ $retencion ? $linea['base_retencion'] : $linea['cantidad'] }}</td>
                                        <td class="text-end">{{ $retencion ? $linea['porcentaje_retencion'] : $linea['precio'] }}</td>
                                        <td class="text-end">{{ $linea['total'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @if (!$retencion)
                                    <tr><th scope="row" colspan="3" class="text-end">Subtotal</th><td class="text-end">{{ $documento['subtotal'] }}</td></tr>
                                    <tr><th scope="row" colspan="3" class="text-end">{{ __('Impuesto') }}</th><td class="text-end">{{ $documento['impuesto'] }}</td></tr>
                                    @if ((float) $documento['propina'] > 0)
                                        <tr><th scope="row" colspan="3" class="text-end">Propina</th><td class="text-end">{{ $documento['propina'] }}</td></tr>
                                    @endif
                                @endif
                                <tr><th scope="row" colspan="3" class="text-end">{{ $retencion ? 'Total retenido' : 'Total' }}</th><td class="text-end fw-semibold">{{ $documento['total'] }}</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route($modulo.'.'.$seccion.'.index') }}" class="btn btn-outline-secondary" data-ajax-close>{{ __('Cerrar') }}</a>
                </div>
            </section>
        </div>
    </div>
@endsection
