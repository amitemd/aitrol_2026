<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Carro de paro {{ $filters['mes'] }}</title><style>
@page{margin:20px}body{font-family:DejaVu Sans,sans-serif;color:#19394b;font-size:9px}h1{font-size:19px;margin-bottom:5px}h2{font-size:13px}.cart-table{width:100%;border-collapse:collapse;table-layout:fixed}.cart-table th,.cart-table td{border:1px solid #c8d5dc;padding:4px;overflow-wrap:break-word}.cart-table th{background:#e8f2f5}.cart-matrix td,.cart-matrix th{text-align:center}.cart-matrix td:first-child,.cart-matrix th:first-child{width:210px;text-align:left}.cart-muted{color:#526f7e;font-size:8px}thead{display:table-header-group}tr{page-break-inside:avoid}.section{page-break-before:always}.detail td,.detail th{width:auto} .empty{padding:20px}
</style></head><body>
@if(!isset($pdfPart) || $pdfPart === 'matrix')
<h1>Control de carro de paro · {{ $filters['mes'] }}</h1><p>{{ $companyName }} · Emitido: {{ date('Y-m-d H:i') }} · {{ $records->count() }} controles activos</p><p>Los valores corresponden al conteo diario. «—» significa sin control; «0» corresponde a conteo cero.</p>
@if(isset($matrixTotal) && $matrixTotal > 12)<p>Filas {{ $matrixOffset + 1 }} a {{ $matrixOffset + count($matrix) }} de {{ $matrixTotal }}</p>@endif
@include('v3.clinical.crash_cart._matrix')
@endif
@if(!isset($pdfPart) || $pdfPart === 'detail')
@foreach($records as $record)
<div class="{{ isset($pdfPart) ? '' : 'section' }}">
    <h2>{{ $record->fecha }} · Control #{{ $record->id }} · {{ $record->tipo == 1 ? 'Medicamentos' : 'Insumos' }}{{ !empty($detailContinuation) ? ' · Continuación' : '' }}</h2>
    <p>Bodega: {{ $record->bodega_nombre }} · Servicio: {{ $record->servicio }} · Responsable: {{ $record->responsable_nombre }}<br>Referencia: {{ $record->reference_basis === 'declared' ? 'Declarada para fecha anterior' : 'Inventario al registrar' }} · Versión {{ $record->version }}</p>
    <p>{{ $record->observacion }}</p>
    <table class="cart-table detail">
        <thead><tr><th>Producto</th><th>Lote / serie</th><th>Presentación / concentración</th><th>Vencimiento</th><th>Referencia</th><th>Conteo</th><th>Diferencia</th><th>Observación</th></tr></thead>
        <tbody>
        @foreach($reportDetails->get($record->id, collect()) as $line)
            <tr><td>{{ $line->producto_nombre }}</td><td>{{ $line->lote }} / {{ $line->serie }}</td><td>{{ $line->presentacion }} / {{ $line->concentracion }}</td><td>{{ $line->fecha_caducidad }}</td><td>{{ $line->referencia_cantidad }}</td><td>{{ $line->stock }}</td><td>{{ $line->referencia_cantidad - $line->stock }}</td><td>{{ $line->observacion }}</td></tr>
        @endforeach
        </tbody>
    </table>
    <p class="cart-muted">La diferencia requiere revisión; este control no genera egresos de inventario.</p>
    @if(!isset($detailLast) || $detailLast)<div style="margin-top:30px">
        @if(isset($signatures[$record->responsable_id]))<img src="{{ $signatures[$record->responsable_id] }}" alt="Firma registrada del responsable" style="max-width:140px;max-height:60px"><br>@else<br><br>@endif
        __________________________________<br>{{ $record->responsable_nombre }}<br>Firma del responsable
    </div>@endif
</div>
@endforeach
@endif
</body></html>
