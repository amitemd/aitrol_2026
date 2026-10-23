<!DOCTYPE html>
<html>

<head>
	<title>PLANILLA PUBLICA</title>
	<style>
		@page {
			margin: 0 0;
		}

		body {
			margin-top: 0.2cm;
			margin-left: 0.5cm;
			margin-right: 0.5cm;
			margin-bottom: 0.1cm;
			font-size: 9px;
		}

		header {
			position: fixed;
			top: 0.2cm;
			left: 0.5cm;
			right: 0cm;
			height: 3cm;
			align-content: center;
		}

		.table {

			margin-right: 0px;
			margin-top: 0px;
			margin-left: 0.5px;
			margin-bottom: 2px;
			font-size: 9px;
			width: 100%;

		}

		.number {

			padding-right: 10px;
			text-align: right;
		}

		/** Define the footer rules **/
		footer {
			position: fixed;
			bottom: 2cm;
			left: 0.5cm;
			right: 0cm;
			height: 2cm;
		}

		.ftr {
			height: 2cm;
		}

		div {
			padding: 1px;
		}

		.css_descripcion {
			font-size: 9px;
		}

		.css_fecha {
			font-size: 9px;
		}

		.titulo_css {
			text-align: center;
		}

		.total_css {

			text-align: right;
			font-weight: bold;
		}

		.subtitulo_css {
			text-align: left;
			font-weight: bold;
			font-size: 9px;
		}

		.cabecera_css {
			font-weight: bold;
			font-size: 9px;
		}

		.sello {
			text-align: right;
			position: fixed;
			bottom: 100px;
			width: 100%;
			font-size: 14px;
			font-weight: bold;
			font-family: "Courier New", Courier, monospace, "Arial Black";
			letter-spacing: -2px;
			/*transform: rotate(-5deg);*/
		}

		.sello2 {
			text-align: right;
			position: fixed;
			bottom: 200px;
			width: 100%;
			font-size: 14px;
			font-weight: bold;
			font-family: "Courier New", Courier, monospace, "Arial Black";
			letter-spacing: -2px;
			/*transform: rotate(-5deg);*/
		}
	</style>

</head>

<body>
	<header>

	</header>


	<main>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table">
			<tbody>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"> <b>Nombre del Prestador :</b> </td>
					<td colspan="8" class="css_descripcion" style="width: 80%;"> {{ $cabeceras[0]->empresa->razonsocial }}</td>
				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"> <b>Seguro</b> </td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">INSTITUTO ECUATORIANO DE SEGURIDAD SOCIAL</td>

				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Nombre del Paciente</b></td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">{{ $cabeceras[0]->paciente->apellido1 . ' ' . $cabeceras[0]->paciente->apellido2 . ' ' . $cabeceras[0]->paciente->nombre1 . ' ' . $cabeceras[0]->paciente->nombre2 }}</td>
				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Cedula de identidad</b></td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">{{ $cabeceras[0]->id_paciente }}</td>
				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Historia Clinica</b></td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">{{ substr($cabeceras[0]->id_paciente,5,10) }}</td>
				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Fecha de Ingreso</b></td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">{{ substr($fecha_ingreso, 0, 10) }}</td>
				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Fecha de Egreso</b></td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">{{ substr($fecha_egreso, 0, 10) }}</td>
				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Procedimiento</b></td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">{{ $nom_proc }}</td>
				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Diagnostico</b></td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">{{ $txt_cie10 }}</td>
				</tr>
				<tr>
					<th colspan="9" class="titulo_css" style="width: 100%;">PLANILLA DE CARGOS DEL PROVEEDOR(CONSULTA EXTERNA, HOSPITALIZACION Y EMERGENCIA)</th>
				</tr>

			</tbody>
		</table>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">HONORARIOS MEDICOS</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="5%" class="cabecera_css">Codigo</td>
					<td width="51%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="8%" class="cabecera_css">Total</td>
				</tr>
				@php $total = 0;$total_hm = 0 @endphp
				@foreach ($arr_hm as $item)
				@foreach ($item as $value)
				@php $total += $value->total; @endphp
				@php
				$valor_total_unitario = round($value->valor,2);
				$valor_unitario = round($value->valor_unitario,2);
				$valor_10p_unitario = $valor_total_unitario - $valor_unitario;
				$subtotal = $valor_unitario * $value->cantidad;
				$valor10p = $valor_10p_unitario * $value->cantidad;
				$total_item = $subtotal + $valor10p + $value->iva;
				$total_hm += $total_item;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td>{{ $value->codigo }}</td>
					<td class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($valor_unitario, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($subtotal, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($valor10p, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($value->iva, 4, ',', ' ') }}</td>
					<td class="number">${{ number_format($total_item, 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				@endforeach
				<tr>
					<td colspan="3" class="total_css">Total&nbsp;</td>
					<td colspan="5" class="subtitulo_css">&nbsp;HONORARIOS MEDICOS</td>
					<td colspan="1" class="number">${{ number_format($total_hm, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">MEDICINAS - VALOR AL ORIGEN</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="7%" class="cabecera_css">Codigo</td>
					<td width="49%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="8%" class="cabecera_css">Total</td>
				</tr>
				@php $total2 = 0;$total_med = 0; @endphp
				@foreach ($arr_med as $item)
				@foreach ($item as $value)
				@php $total2 += $value->total; @endphp
				@php
				$valor_total_unitario = round($value->valor,2);
				$valor_unitario = round($value->valor_unitario,2);
				$valor_10p_unitario = $valor_total_unitario - $valor_unitario;
				$subtotal = $valor_unitario * $value->cantidad;
				$valor10p = $valor_10p_unitario * $value->cantidad;
				$total_item = $subtotal + $valor10p + $value->iva;
				$total_med += $total_item;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td></td>
					<td class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($valor_unitario, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($subtotal, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($valor10p, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($value->iva, 4, ',', ' ') }}</td>
					<td class="number">${{ number_format($total_item, 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				@endforeach
				<tr>
					<td width="5%" colspan="3" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="5" class="subtitulo_css">&nbsp;MEDICINAS - VALOR AL ORIGEN</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total_med, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">INSUMOS - VALOR AL ORIGEN</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha </td>
					<td width="5%" class="cabecera_css">Codigo</td>
					<td width="51%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="8%" class="cabecera_css">Total</td>
				</tr>
				@php $totalins = 0;$total_ins = 0; @endphp
				@foreach ($arr_ins as $item)
				@foreach ($item as $value)
				@php
				//$total_item = $value->subtotal + $value->porcentaje10 + ($value->subtotal * $value->porcentaje_iva);
				//$nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
				$nuevo_iva = $value->iva_unitario * $value->cantidad;
				//$total_item = round(($value->cantidad * $value->valor),4) + round(($value->cantidad * $nuevo_iva),4);
				$total_item = $value->cantidad * ($value->valor_unitario + $value->iva_unitario + $value->p10_unitario);
				//$total_item = round($total_item,4);
				$totalins += $total_item;
				@endphp
				@php
				$valor_total_unitario = round($value->valor_unitario,2);
				$valor_unitario = round($value->valor,2);
				$valoru = round($value->valor_unitario,2);
				$valor_10p_unitario = $valor_total_unitario * $value->porcent_10;
				$subtotal = $valor_unitario * $value->cantidad;
				$valor10p = $valor_10p_unitario * $value->cantidad;
                $new_iva = ($valor_unitario *   $value->porcentaje_iva) * $value->cantidad ;
				$total_item = $subtotal + $new_iva;
				$total_ins += $total_item;
				@endphp

				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td></td>
					<td class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($valoru, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($subtotal, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($valor10p, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($new_iva, 4, ',', ' ') }}</td>
					<td class="number">${{ number_format($total_item, 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				@endforeach
				<tr>
					<td width="5%" colspan="3" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="5" class="subtitulo_css">&nbsp;INSUMOS - VALOR AL ORIGEN</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total_ins, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">LABORATORIO</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="5%" class="cabecera_css">Codigo</td>
					<td width="51%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="8%" class="cabecera_css">Total</td>
				</tr>
				@php $total_lab = 0;$total_labo = 0; @endphp
				@foreach ($arr_lab as $item)
				@foreach ($item as $value)
				@php
				$total_lab += $value->total;
				@endphp
				@php
				$valor_total_unitario = round($value->valor,2);
				$valor_unitario = round($value->valor_unitario,2);
				$valor_10p_unitario = $valor_total_unitario - $valor_unitario;
				$subtotal = $valor_unitario * $value->cantidad;
				$valor10p = $valor_10p_unitario * $value->cantidad;
				$total_item = $subtotal + $valor10p + $value->iva;
				$total_labo += $total_item;
				@endphp

				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td>{{ $value->codigo }}</td>
					<td class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($valor_unitario, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($subtotal, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($valor10p, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($value->iva, 4, ',', ' ') }}</td>
					<td class="number">${{ number_format($total_item, 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				@endforeach
				<tr>
					<td width="5%" colspan="3" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="5" class="subtitulo_css">&nbsp;LABORATORIO</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total_labo, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">IMAGEN(*)</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="5%" class="cabecera_css">Codigo</td>
					<td width="51%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="8%" class="cabecera_css">Total</td>
				</tr>
				@php $total_imagen = 0;$total_ima = 0; @endphp
				@foreach ($arr_im as $item)
				@foreach ($item as $value)
				@php
				$total_imagen += $value->total;
				@endphp
				@php
				$valor_total_unitario = round($value->valor,2);
				$valor_unitario = round($value->valor_unitario,2);
				$valor_10p_unitario = $valor_total_unitario - $valor_unitario;
				$subtotal = $valor_unitario * $value->cantidad;
				$valor10p = $valor_10p_unitario * $value->cantidad;
				$total_item = $subtotal + $valor10p + $value->iva;
				$total_ima += $total_item;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td>{{ $value->codigo }}</td>
					<td class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($valor_unitario, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($subtotal, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($valor10p, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($value->iva, 4, ',', ' ') }}</td>
					<td class="number">${{ number_format($total_item, 2, ',', ' ') }}</td>
				</tr>

				@endforeach
				@endforeach
				<tr>
					<td width="5%" colspan="3" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="5" class="subtitulo_css">&nbsp;IMAGEN(*)</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total_ima, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">SERVICIOS INSTITUCIONALES</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="5%" class="cabecera_css">Codigo</td>
					<td width="51%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="8%" class="cabecera_css">Total</td>
				</tr>
				@php $total_serv = 0;$total_servi = 0; @endphp
				@foreach ($arr_serv as $item)
				@foreach ($item as $value)
				@php
				$total_serv += $value->total;
				@endphp
				@php
				$valor_total_unitario = round($value->valor,2);
				$valor_unitario = round($value->valor_unitario,2);
				$valor_10p_unitario = $valor_total_unitario - $valor_unitario;
				$subtotal = $valor_unitario * $value->cantidad;
				$valor10p = $valor_10p_unitario * $value->cantidad;
				$total_item = $subtotal + $valor10p + $value->iva;
				$total_servi += $total_item;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td>{{ $value->codigo }}</td>
					<td class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($valor_unitario, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($subtotal, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($valor10p, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($value->iva, 4, ',', ' ') }}</td>
					<td class="number">${{ number_format($total_item, 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				@endforeach
				<tr>
					<td width="5%" colspan="3" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="5" class="subtitulo_css">&nbsp;SERVICIOS INSTITUCIONALES</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total_servi, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">EQUIPOS ESPECIALES</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="5%" class="cabecera_css">Codigo</td>
					<td width="51%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="8%" class="cabecera_css">Total</td>
				</tr>
				@php $total_equi = 0;$total_equipo = 0; @endphp
				@foreach ($arr_eq as $item)
				@foreach($item as $value)
				@php
				$total_equi += $value->total;
				@endphp
				@php
				$valor_total_unitario = round($value->valor,2);
				$valor_unitario = round($value->valor_unitario,2);
				$valor_10p_unitario = $valor_total_unitario - $valor_unitario;
				$subtotal = $valor_unitario * $value->cantidad;
				$valor10p = $valor_10p_unitario * $value->cantidad;
				$total_item = $subtotal + $valor10p + $value->iva;
				$total_equipo += $total_item;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td>{{ $value->codigo }}</td>
					<td class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($valor_unitario, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($subtotal, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($valor10p, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format($value->iva, 4, ',', ' ') }}</td>
					<td class="number">${{ number_format($total_item, 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				@endforeach
				<tr>
					<td width="5%" colspan="3" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="5" class="subtitulo_css">&nbsp;EQUIPOS ESPECIALES</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total_equipo, 2, ',', ' ') }}</td>
				</tr>
				@php
				$liquidacion_total = $total_equi + $total_serv + $total_imagen + $total_lab + $totalins + $total2 + $total;
				$ntotal = $total_equipo + $total_servi + $total_ima + $total_labo + $total_ins + $total_med + $total_hm;
				@endphp
				<tr>
					<td width="5%" colspan="8" class="total_css">TOTAL LIQUIDACION&nbsp;</td>
					<td width="5%" colspan="1" class="number">${{ number_format($ntotal, 2, ',', ' ') }}</td>
				</tr>

			</tbody>
		</table>
		<!--div>
			@if($cabeceras[0]->id_empresa == '0992704152001')
				@php
				$fechaCabecera = \Carbon\Carbon::parse($cabeceras[0]->fecha_ing);
				$fecha_limite = \Carbon\Carbon::create(2024, 11, 1);
				@endphp

				@if($fechaCabecera < $fecha_limite)
					<div class="sello2">
						<img width="100" height="auto" src="{{ base_path().'/storage/app/logo/firma_gastroclinica_old.png' }}" align="right">
					</div>
					<div class="sello">
						.............................<br>
						DR. ANGEL LEONEL VELEZ CHINGA<br>
						C.I. 0917743734<br>
						JEFE DE CONVENIOS PÚBLICOS<br>
						<span class="ruc">GASTROCLÍNICA S.A.</span>
					</div>
				@else
					<div class="sello2">
						<img width="100" height="auto" src="{{ base_path().'/storage/app/logo/firma_gastroclinica.png' }}" align="right">
					</div>
					<div class="sello">
						.............................<br>
						DRA. ANDREA LORENA ESTRELLA HIDALGO<br>
						C.I. 0918552092<br>
						JEFE DE CONVENIOS PÚBLICOS<br>
						<span class="ruc">GASTROCLÍNICA S.A.</span>
					</div>
				@endif
			@endif
			{{-- @if($cabeceras[0]->id_empresa == '1307189140001')
				<img width="200" height="auto" src="{{base_path().'/storage/app/logo/firma_robles.png'}}" align="right" hspace="12">
			@endif --}}

			@if($cabeceras[0]->id_empresa == '1307189140001')
				@php
				$fechaCabecera = \Carbon\Carbon::parse($cabeceras[0]->fecha_ing);
				$fecha_limite = \Carbon\Carbon::create(2024, 11, 1);
				@endphp

				@if($fechaCabecera < $fecha_limite)
					<div class="sello2">
					<img width="100" height="auto" src="{{ base_path().'/storage/app/logo/firma_gastroclinica_old.png' }}" align="right">
					</div>
					<div class="sello">
						.............................<br>
						DR. ANGEL LEONEL VELEZ CHINGA<br>
						C.I. 0917743734<br>
						JEFE DE CONVENIOS PÚBLICOS<br>
						<span class="ruc">DR. CARLOS ANTONIO ROBLES MEDRANDA</span> <br>
						<span class="ruc">PRESTADOR EXTERNO</span>
					</div>
				@else
					<div class="sello2">
						<img width="100" height="auto" src="{{ base_path().'/storage/app/logo/firma_gastroclinica.png' }}" align="right">
					</div>
					<div class="sello">
						.............................<br>
						DRA. ANDREA LORENA ESTRELLA HIDALGO<br>
						C.I. 0918552092<br>
						JEFE DE CONVENIOS PÚBLICOS<br>
						<span class="ruc">DR. CARLOS ANTONIO ROBLES MEDRANDA</span> <br>
						<span class="ruc">PRESTADOR EXTERNO</span>
					</div>
				@endif
			@endif
		</div-->
        <div style="align-content: right;margin-right: 30px;margin-top: 20px;">
			@if($cabeceras[0]->id_empresa == '0992704152001')
				{{-- @php
				$fechaCabecera = \Carbon\Carbon::parse($cabeceras[0]->fecha_ing);
				$fecha_limite = \Carbon\Carbon::create(2023, 12, 1);
				$fecha_limite_new  = \Carbon\Carbon::create(2024, 01, 01);

				@endphp

				@if($fechaCabecera < $fecha_limite)
					<div class="sello2">
						<img width="100" height="auto" src="{{ base_path().'/storage/app/logo/firma_gastroclinica_old.png' }}" align="right">
					</div>
					<div class="sello">
						.............................<br>
						DR. ANGEL LEONEL VELEZ CHINGA<br>
						C.I. 0917743734<br>
						JEFE DE CONVENIOS PÚBLICOS<br>
						<span class="ruc">GASTROCLÍNICA S.A.</span>
					</div>
				@elseif($fechaCabecera < $fecha_limite_new)
					<div class="sello2">
						<img width="100" height="auto" src="{{ base_path().'/storage/app/logo/firma_gastroclinica1.png' }}" align="right">
					</div>
					<div class="sello">
						.......................................<br>
						DRA. ANDREA LORENA ESTRELLA HIDALGO<br>
						C.I. 0918552092<br>
						JEFE DE CONVENIOS PÚBLICOS<br>
						<span class="ruc">GASTROCLÍNICA S.A.</span>
					</div>
				@else --}}
					<div class="sello2">
						<img width="100" height="auto" src="{{ base_path().'/storage/app/logo/firma_gastroclinicaaura.png' }}" align="right">
					</div>
					<div class="sello">
						.......................................<br>
						DRA. AURA EDITH ZAMBRANO PAREDES<br>
						C.I. 0926862152<br>
						JEFE DE CONVENIOS PÚBLICOS<br>
						<span class="ruc">GASTROCLÍNICA S.A.</span>
					</div>

				{{-- @endif --}}
			@endif
			{{-- @if($archivo_plano->id_empresa == '1307189140001')
				<img width="200" height="auto" src="{{base_path().'/storage/app/logo/firma_robles.png'}}" align="right" hspace="12">
			@endif --}}

			@if($cabeceras[0]->id_empresa == '1307189140001')
				{{-- @php
				$fechaCabecera = \Carbon\Carbon::parse($cabeceras[0]->fecha_ing);
				$fecha_limite = \Carbon\Carbon::create(2023,12, 1);
				$fecha_limite_new  = \Carbon\Carbon::create(2024, 01, 01);
				@endphp

				@if($fechaCabecera < $fecha_limite)
					<div class="sello2">
					<img width="100" height="auto" src="{{ base_path().'/storage/app/logo/firma_gastroclinica_old.png' }}" align="right">
					</div>
					<div class="sello">
						.............................<br>
						DR. ANGEL LEONEL VELEZ CHINGA<br>
						C.I. 0917743734<br>
						JEFE DE CONVENIOS PÚBLICOS<br>
						<span class="ruc">DR. CARLOS ANTONIO ROBLES MEDRANDA</span> <br>
						<span class="ruc">PRESTADOR EXTERNO</span>
					</div>
				@elseif($fechaCabecera < $fecha_limite_new)
					<div class="sello2">
						<img width="100" height="auto" src="{{ base_path().'/storage/app/logo/firma_gastroclinica1.png' }}" align="right">
					</div>
					<div class="sello">
						.......................................<br>
						DRA. ANDREA LORENA ESTRELLA HIDALGO<br>
						C.I. 0918552092<br>
						JEFE DE CONVENIOS PÚBLICOS<br>
						<span class="ruc">DR. CARLOS ANTONIO ROBLES MEDRANDA</span> <br>
						<span class="ruc">PRESTADOR EXTERNO</span>
					</div>
				@else --}}
					<div class="sello2">
						<img width="100" height="auto" src="{{ base_path().'/storage/app/logo/firma_gastroclinicaaura.png' }}" align="right">
					</div>
					<div class="sello">
						.......................................<br>
						DRA. AURA EDITH ZAMBRANO PAREDES<br>
						C.I. 0926862152<br>
						JEFE DE CONVENIOS PÚBLICOS<br>
						<span class="ruc">DR. CARLOS ANTONIO ROBLES MEDRANDA</span> <br>
						<span class="ruc">PRESTADOR EXTERNO</span>
					</div>
				{{-- @endif --}}
			@endif
		</div>

	</main>
</body>

</html>