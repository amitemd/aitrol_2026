@extends('contable.ventas.base')

@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Entrega {{ $entrega->numero }}</h3>
            <div class="pull-right">
                <a href="{{ route('entregas_mercaderia.index') }}" class="btn btn-default">Listado</a>
                @if (!$entrega->id_venta && $entrega->estado !== 'ANULADA')
                    <a href="{{ route('entregas_mercaderia.facturar', $entrega->id) }}" class="btn btn-success"><i class="fa fa-file-text"></i> Generar factura comercial</a>
                @endif
                @if (!$entrega->id_venta && $entrega->estado === 'ENTREGADA')
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-anular-entrega"><i class="fa fa-ban"></i> Anular entrega</button>
                @endif
            </div>
        </div>
        <div class="box-body">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
            <div class="row">
                <div class="col-md-4"><strong>Cliente</strong><br>{{ $entrega->cliente ? $entrega->cliente->nombre : '' }}<br>{{ $entrega->id_cliente }}</div>
                <div class="col-md-4"><strong>Traslado</strong><br>{{ $entrega->fecha_entrega }} a {{ $entrega->fecha_fin_traslado }}<br>{{ $entrega->direccion_partida }} → {{ $entrega->direccion_destino }}</div>
                <div class="col-md-4"><strong>Transportista</strong><br>{{ $entrega->transportista_nombre }}<br>{{ $entrega->transportista_identificacion }} · {{ $entrega->placa }}</div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <strong>Guía</strong><br>#{{ $entrega->id_guia_remision }} ·
                    @if($entrega->guia && $entrega->guia->estado == 5)
                        <span class="label label-success">AUTORIZADA SRI</span><br>
                        <a target="_blank" href="{{ asset('/') }}api_doc_electronico?opcion=generarPdf&clave={{ $entrega->guia->clave_acceso }}">Ver guía PDF</a>
                    @elseif($entrega->guia && $entrega->guia->estado == -2)
                        <span class="label label-danger">ANULADA</span>
                    @elseif($entrega->guia && in_array($entrega->guia->estado, [7, 9]))
                        <span class="label label-danger">ERROR SRI</span>
                    @else
                        <span class="label label-warning">EN PROCESO SRI</span>
                    @endif
                </div>
                <div class="col-md-3"><strong>Movimiento inventario</strong><br>#{{ $entrega->id_movimiento_inventario }}</div>
                <div class="col-md-3"><strong>Asiento de costo</strong><br>#{{ $entrega->id_asiento_costo }}@if($entrega->id_asiento_reversion)<br><span class="text-danger">Reversión #{{ $entrega->id_asiento_reversion }}</span>@endif</div>
                <div class="col-md-3"><strong>Factura</strong><br>{{ $entrega->id_venta ? '#'.$entrega->id_venta : 'Pendiente' }}</div>
            </div>
            @if($entrega->estado === 'ANULADA')
                <div class="alert alert-danger" style="margin-top:20px">
                    <strong><i class="fa fa-ban"></i> Entrega anulada</strong><br>
                    {{ $entrega->motivo_anulacion }}<br>
                    <small>Fecha: {{ $entrega->fecha_anulacion }} · Usuario: {{ $entrega->id_usuario_anulacion }}</small>
                </div>
            @endif
            <h4 style="margin-top:25px">Productos entregados</h4>
            <table class="table table-bordered"><thead><tr><th>Código</th><th>Producto</th><th>Bodega</th><th>Serie</th><th>Lote</th><th>Vencimiento</th><th class="text-right">Existencia anterior</th><th class="text-right">Cantidad</th><th class="text-right">Precio de venta</th><th class="text-right">Total comercial</th></tr></thead><tbody>
                @foreach($entrega->detalles as $detalle)<tr><td>{{ $detalle->codigo_producto }}</td><td>{{ $detalle->nombre_producto }}</td><td>{{ $detalle->bodegaInventario ? $detalle->bodegaInventario->nombre : $detalle->id_bodega_inventario }}</td><td>{{ $detalle->serie ?: 'N/A' }}</td><td>{{ $detalle->lote ?: 'N/A' }}</td><td>{{ $detalle->fecha_vence ?: 'N/A' }}</td><td class="text-right">{{ number_format($detalle->existencia_antes, 0) }}</td><td class="text-right">{{ number_format($detalle->cantidad, 0) }}</td><td class="text-right">$ {{ number_format($detalle->precio_venta, 2) }}</td><td class="text-right">$ {{ number_format($detalle->precio_venta * $detalle->cantidad, 2) }}</td></tr>@endforeach
            </tbody></table>
            <h4 style="margin-top:25px">Trazabilidad</h4>
            <table class="table table-striped"><thead><tr><th>Fecha</th><th>Evento</th><th>Documento</th><th>Detalle</th><th>Usuario</th></tr></thead><tbody>
                @foreach($entrega->trazabilidad as $evento)<tr><td>{{ $evento->created_at }}</td><td>{{ $evento->evento }}</td><td>{{ $evento->documento_tipo }} #{{ $evento->documento_id }}</td><td>{{ $evento->detalle }}</td><td>{{ $evento->id_usuario }}</td></tr>@endforeach
            </tbody></table>
        </div>
    </div>
</section>

@if (!$entrega->id_venta && $entrega->estado === 'ENTREGADA')
<div class="modal fade" id="modal-anular-entrega" tabindex="-1" role="dialog" aria-labelledby="titulo-anular-entrega">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('entregas_mercaderia.anular', $entrega->id) }}" id="form-anular-entrega">
                {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titulo-anular-entrega"><i class="fa fa-exclamation-triangle text-danger"></i> Anular entrega {{ $entrega->numero }}</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        Esta acción restaurará las existencias en inventario y series, anulará el movimiento y generará un asiento inverso del costo. No puede deshacerse desde esta pantalla.
                    </div>
                    <div class="form-group">
                        <label for="motivo_anulacion">Motivo de anulación</label>
                        <textarea id="motivo_anulacion" name="motivo_anulacion" class="form-control" rows="4" minlength="5" maxlength="500" required placeholder="Explique por qué se anula la entrega"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger" id="confirmar-anulacion"><i class="fa fa-ban"></i> Confirmar anulación</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(function () {
    $('#form-anular-entrega').on('submit', function () {
        $('#confirmar-anulacion').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Revirtiendo...');
    });
});
</script>
@endif
@endsection
