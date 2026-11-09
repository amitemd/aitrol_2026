<!doctype html>
<html lang="es"><head><meta charset="utf-8"><title>Cotizacion #{{ $q->id }}</title>
<style>
@page { margin: 32px 32px 44px; }
body { font-family: Helvetica, Arial, sans-serif; font-size: 9px; color: #25374a; }
h1 { font-size: 21px; color: #204c78; margin: 0 0 6px; }
.brand { font-size: 12px; margin-bottom: 16px; color: #425b73; }
.meta { width:100%; margin:12px 0 18px; border-collapse:collapse; }
.meta td { padding:5px 0; vertical-align:top; }
.label { color:#62758a; font-size:8px; }
.items { border-collapse:collapse; width:100%; table-layout:fixed; }
.items th { background:#eaf0f7; color:#204c78; text-align:left; font-size:8px; }
.items th, .items td { border:1px solid #d2dce6; padding:8px 6px; vertical-align:top; word-wrap:break-word; }
.items tbody tr { page-break-inside:avoid; }
thead { display:table-header-group; }
.amount { text-align:right; }
.total { text-align:right; font-size:16px; color:#204c78; margin:18px 0 10px; }
.note { font-size:9px; color:#62758a; margin-top:8px; }
.summary { page-break-inside:avoid; }
.footer { position:fixed; bottom:-26px; left:0; color:#62758a; font-size:8px; }
</style></head><body>
<h1>Cotizacion odontologica #{{ $q->id }}</h1>
@if($empresa)<div class="brand">{{ !empty($empresa->nombre_form) ? $empresa->nombre_form : $empresa->nombrecomercial }}</div>@endif
<table class="meta"><tr><td style="width:60%"><span class="label">PACIENTE</span><br><strong>{{ $paciente ? trim($paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2) : $q->id_paciente }}</strong></td><td><span class="label">FECHA DE COTIZACION</span><br>{{ substr($q->created_at,0,10) }}</td></tr>
<tr><td><span class="label">IDENTIFICACION</span><br>{{ $q->id_paciente }}</td><td><span class="label">ESTADO</span><br>{{ $q->estado }}</td></tr></table>
<table class="items"><thead><tr><th style="width:3%">#</th><th style="width:10%">Fecha</th><th style="width:5%">Pieza</th><th style="width:16%">Caras</th><th style="width:28%">Diagnostico (CIE-10)</th><th style="width:25%">Procedimiento asociado</th><th style="width:13%">Precio</th></tr></thead><tbody>
@foreach($filas as $i => $fila)<tr><td>{{ $i+1 }}</td><td>{{ $fila['fecha'] }}</td><td>{{ $fila['pieza'] }}</td><td>{{ $fila['caras'] }}</td><td>{{ $fila['diagnosticos'] }}</td><td>{{ $fila['procedimiento'] }}</td><td class="amount">{{ isset($fila['precio']) && $fila['precio'] !== '' ? '$ '.number_format($fila['precio'],2) : 'Precio pendiente' }}</td></tr>@endforeach
</tbody></table>
<div class="summary"><p class="total"><strong>{{ $pendientes ? 'Subtotal con precio' : 'Total' }}: $ {{ number_format($q->total,2) }}</strong></p>
@if($pendientes)<p class="note">Esta cotizacion tiene {{ $pendientes }} procedimiento(s) con precio pendiente.</p>@endif
<p class="note">Valores expresados en dolares estadounidenses. Este documento corresponde a la cotizacion guardada.</p></div>
<div class="footer">Cotizacion #{{ $q->id }} · {{ $q->estado }}</div>
</body></html>
