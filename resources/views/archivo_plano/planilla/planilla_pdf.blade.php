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
			margin-bottom: 0.8cm;
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
	</style>

</head>

<body>
	<header>

	</header>


	<main>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table" style="width: 100% !important">
			<tbody>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"> <b>Nombre del Prestador :</b> </td>
					<td colspan="8" class="css_descripcion" style="width: 80%;"> {{ $archivo_plano->empresa->razonsocial }}</td>
				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"> <b>Seguro</b> </td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">INSTITUTO ECUATORIANO DE SEGURIDAD SOCIAL</td>

				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Nombre del Paciente</b></td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">{{ $archivo_plano->paciente->apellido1 . ' ' . $archivo_plano->paciente->apellido2 . ' ' . $archivo_plano->paciente->nombre1 . ' ' . $archivo_plano->paciente->nombre2 }}</td>
				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Cedula de identidad</b></td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">{{ $archivo_plano->id_paciente }}</td>
				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Historia Clinica</b></td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">{{ substr($archivo_plano->id_paciente,5,10) }}</td>
				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Fecha de Ingreso</b></td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">{{ substr($archivo_plano->fecha_ing, 0, 10) }}</td>
				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Fecha de Egreso</b></td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">{{ substr($archivo_plano->fecha_alt, 0, 10) }}</td>
				</tr>
				<tr>
					<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Procedimiento</b></td>
					<td colspan="8" class="css_descripcion" style="width: 80%;">{{ $archivo_plano->nom_procedimiento }}</td>
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
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table" style="width: 98% !important">
			<thead>
				<tr>
					<th colspan="10" class="titulo_css">HONORARIOS MÉDICOS</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="7%" class="cabecera_css">Codigo</td>
					<td colspan="2" width="52%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="5%" class="cabecera_css">Total</td>
				</tr>
				@php
				$total = 0;
				$vt_sum_st = 0;
				$vt_sum_p10 = 0;
				$vt_sum_iva = 0;
				@endphp
				@foreach ($honor_medicos as $value)
				@php
				$valor_total_unitario = round($value->valor, 2);
				$valor_unitario = round($value->valor_unitario, 2);
				$valor_10p_unitario = $valor_total_unitario - $valor_unitario;
				$vt_subt = $valor_unitario * $value->cantidad;
				$vt_p10 = $valor_10p_unitario * $value->cantidad;
				$vt_iva = round($value->iva, 4);
				$vt_total = $vt_subt + $vt_p10 + $vt_iva;
				$total += $vt_total;
				$vt_sum_st += $vt_subt;
				$vt_sum_p10 += $vt_p10;
				$vt_sum_iva += $vt_iva;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td class="css_descripcion">{{ $value->codigo }}</td>
					<td colspan="2" class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($valor_unitario, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($vt_subt, 4), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($vt_p10, 4), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->iva, 4), 4, ',', ' ') }}</td>
					<td class="number">${{ number_format($vt_total, 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				<tr>
					<td colspan="3" class="total_css">Total&nbsp;</td>
					<td colspan="6" class="subtitulo_css">&nbsp;HONORARIOS MÉDICOS</td>
					<td colspan="1" class="number">${{ number_format($total, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table" style="width: 98.5% !important">
			<thead>
				<tr>
					<th colspan="10" class="titulo_css">MEDICINAS - VALOR AL ORIGEN</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="7%" class="cabecera_css">Codigo</td>
					<td colspan="2" width="52%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="5%" class="cabecera_css">Total</td>
				</tr>
				@php $total2 = 0; @endphp
				@foreach ($medicinas as $value)
				@php
				$valor_total_unitario = round($value->valor, 2);
				$valor_unitario = round($value->valor_unitario, 2);
				$valor_10p_unitario = $valor_total_unitario - $valor_unitario;
				$vt_subt = $valor_unitario * $value->cantidad;
				$vt_p10 = $valor_10p_unitario * $value->cantidad;
				$vt_iva = round($value->iva, 4);
				$vt_total = $vt_subt + $vt_p10 + $vt_iva;
				$total2 += $vt_total;
				$vt_sum_st += $vt_subt;
				$vt_sum_p10 += $vt_p10;
				$vt_sum_iva += $vt_iva;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td class="css_descripcion">{{ $value->codigo_medicina }}</td>
					<td colspan="2" class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($valor_unitario, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($vt_subt, 4), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($vt_p10, 4), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->iva, 4), 4, ',', ' ') }}</td>
					<td class="number">${{ number_format($vt_total, 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				<tr>
					<td width="5%" colspan="3" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="6" class="subtitulo_css">&nbsp;MEDICINAS - VALOR AL ORIGEN</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total2, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table" style="width: 97.3% !important">
			<thead>
				<tr>
					<th colspan="10" class="titulo_css">INSUMOS - VALOR AL ORIGEN</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha </td>
					<td width="7%" class="cabecera_css">Codigo</td>
					<td colspan="2" width="52%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="5%" class="cabecera_css">Total</td>
				</tr>
				@php $totalins = 0; @endphp
				@foreach ($insumos as $value)
				@php
				//$total_item = $value->subtotal + $value->porcentaje10 + ($value->subtotal * $value->porcentaje_iva);
				//$nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
				$valor_unitario = round($value->valor_unitario, 2);
				$valor_iva_unitario = round($value->valor, 2);
				$valor_10p_unitario = $valor_unitario * $value->porcent_10;
				$vt_subt = $valor_unitario * $value->cantidad;
				$vt_p10 = $valor_10p_unitario * $value->cantidad;
				$nuevo_iva = round(($valor_iva_unitario * $value->porcentaje_iva) * $value->cantidad, 4);
				$total_item_exacto = round($value->subtotal, 4) + round($value->porcentaje10, 4) + round($nuevo_iva, 4);
				//$total_item = round(($value->cantidad * $value->valor),4) + round(($value->cantidad * $nuevo_iva),4);
				$total_item = round($vt_subt, 2) + round($vt_p10, 2) + round($nuevo_iva, 4);
				//$total_item = round($total_item,4);
				$totalins += $total_item_exacto;
				$vt_sum_st += round($vt_subt, 2);
				$vt_sum_p10 += round($vt_p10, 2);
				$vt_sum_iva += round($nuevo_iva, 4);
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td class="css_descripcion"></td>
					<td colspan="2" class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($valor_unitario, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($vt_subt, 4), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($vt_p10, 4), 2, ',', ' ') }}</td>
					<!--td class="number">${{ number_format(round($value->subtotal * $value->porcentaje_iva, 4), 2, ',', ' ') }}</td-->
					<td class="number">${{ number_format($nuevo_iva, 4, ',', ' ') }}</td>
					<td class="number">${{ number_format($total_item, 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				<tr>
					<td width="5%" colspan="3" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="6" class="subtitulo_css">&nbsp;INSUMOS - VALOR AL ORIGEN</td>
					<td width="5%" colspan="1" class="number">${{ number_format($totalins, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table" style="width: 98.5% !important">
			<thead>
				<tr>
					<th colspan="10" class="titulo_css">LABORATORIO</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="7%" class="cabecera_css">Codigo</td>
					<td colspan="2" width="52%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="5%" class="cabecera_css">Total</td>
				</tr>
				@php $total_lab = 0; @endphp
				@foreach ($laboratorio as $value)
				@php
				$valor_total_unitario = round($value->valor, 2);
				$valor_unitario = round($value->valor_unitario, 2);
				$valor_10p_unitario = $valor_total_unitario - $valor_unitario;
				$vt_subt = $valor_unitario * $value->cantidad;
				$vt_p10 = $valor_10p_unitario * $value->cantidad;
				$vt_iva = round($value->iva, 4);
				$vt_total = $vt_subt + $vt_p10 + $vt_iva;
				$total_lab += $vt_total;
				$vt_sum_st += $vt_subt;
				$vt_sum_p10 += $vt_p10;
				$vt_sum_iva += $vt_iva;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td class="css_descripcion">{{ $value->codigo }}</td>
					<td colspan="2" class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($valor_unitario, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($vt_subt, 4), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($vt_p10, 4), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->iva, 4), 4, ',', ' ') }}</td>
					<td class="number">${{ number_format($vt_total, 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				<tr>
					<td width="5%" colspan="3" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="6" class="subtitulo_css">&nbsp;LABORATORIO</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total_lab, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table" style="width: 99.5% !important">
			<thead>
				<tr>
					<th colspan="10" class="titulo_css">IMAGEN(*)</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="7%" class="cabecera_css">Codigo</td>
					<td colspan="2" width="52%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="5%" class="cabecera_css">Total</td>
				</tr>
				@php $total_imagen = 0; @endphp
				@foreach ($imagen as $value)
				@php
				$valor_total_unitario = round($value->valor, 2);
				$valor_unitario = round($value->valor_unitario, 2);
				$valor_10p_unitario = $valor_total_unitario - $valor_unitario;
				$vt_subt = $valor_unitario * $value->cantidad;
				$vt_p10 = $valor_10p_unitario * $value->cantidad;
				$vt_iva = round($value->iva, 4);
				$vt_total = $vt_subt + $vt_p10 + $vt_iva;
				$total_imagen += $vt_total;
				$vt_sum_st += $vt_subt;
				$vt_sum_p10 += $vt_p10;
				$vt_sum_iva += $vt_iva;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td class="css_descripcion">{{ $value->codigo }}</td>
					<td colspan="2" class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($valor_unitario, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($vt_subt, 4), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($vt_p10, 4), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->iva, 4), 4, ',', ' ') }}</td>
					<td class="number">${{ number_format($vt_total, 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				<tr>
					<td width="5%" colspan="3" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="6" class="subtitulo_css">&nbsp;IMAGEN(*)</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total_imagen, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table" style="width: 98% !important">
			<thead>
				<tr>
					<th colspan="10" class="titulo_css">SERVICIOS INSTITUCIONALES</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="7%" class="cabecera_css">Codigo</td>
					<td colspan="2" width="52%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="5%" class="cabecera_css">Total</td>
				</tr>
				@php $total_serv = 0; @endphp
				@foreach ($servicios_ins as $value)
				@php
				$valor_total_unitario = round($value->valor, 2);
				$valor_unitario = round($value->valor_unitario, 2);
				$valor_10p_unitario = $valor_total_unitario - $valor_unitario;
				$vt_subt = $valor_unitario * $value->cantidad;
				$vt_p10 = $valor_10p_unitario * $value->cantidad;
				$vt_iva = round($value->iva, 4);
				$vt_total = $vt_subt + $vt_p10 + $vt_iva;
				$total_serv += $vt_total;
				$vt_sum_st += $vt_subt;
				$vt_sum_p10 += $vt_p10;
				$vt_sum_iva += $vt_iva;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td class="css_descripcion">{{ $value->codigo }}</td>
					<td colspan="2" class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($valor_unitario, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($vt_subt, 4), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($vt_p10, 4), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->iva, 4), 4, ',', ' ') }}</td>
					<td class="number">${{ number_format($vt_total, 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				<tr>
					<td width="5%" colspan="3" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="6" class="subtitulo_css">&nbsp;SERVICIOS INSTITUCIONALES</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total_serv, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.8px" cellspacing="0" cellpadding="0" class="table" style="width: 99.5% !important">
			<thead>
				<tr>
					<th colspan="10" class="titulo_css">EQUIPOS ESPECIALES</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="7%" class="cabecera_css">Codigo</td>
					<td colspan="2" width="52%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="5%" class="cabecera_css">Total</td>
				</tr>
				@php $total_equi = 0; @endphp
				@foreach ($equipos as $value)
				@php
				$valor_total_unitario = round($value->valor, 2);
				$valor_unitario = round($value->valor_unitario, 2);
				$valor_10p_unitario = $valor_total_unitario - $valor_unitario;
				$vt_subt = $valor_unitario * $value->cantidad;
				$vt_p10 = $valor_10p_unitario * $value->cantidad;
				$vt_iva = round($value->iva, 4);
				$vt_total = $vt_subt + $vt_p10 + $vt_iva;
				$total_equi += $vt_total;
				$vt_sum_st += $vt_subt;
				$vt_sum_p10 += $vt_p10;
				$vt_sum_iva += $vt_iva;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td class="css_descripcion">{{ $value->codigo }}</td>
					<td colspan="2" class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($valor_unitario, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($vt_subt, 4), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($vt_p10, 4), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->iva, 4), 4, ',', ' ') }}</td>
					<td class="number">${{ number_format($vt_total, 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				<tr>
					<td width="5%" colspan="3" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="6" class="subtitulo_css">&nbsp;EQUIPOS ESPECIALES</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total_equi, 2, ',', ' ') }}</td>
				</tr>
				@php
				$liquidacion_total = $total_equi + $total_serv + $total_imagen + $total_lab + $totalins + $total2 + $total;
				@endphp
				<tr>
					<td width="5%" colspan="6" class="total_css">TOTAL LIQUIDACION&nbsp;</td>
					<td width="11%" colspan="1" class="number">${{ number_format($vt_sum_st, 2, ',', ' ') }}</td>
					<td width="9%" colspan="1" class="number">${{ number_format($vt_sum_p10, 2, ',', ' ') }}</td>
					<td width="9%" colspan="1" class="number">${{ number_format($vt_sum_iva, 4, ',', ' ') }}</td>
					<td width="11%" colspan="1" class="number">${{ number_format($liquidacion_total, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<div style="align-content: right;margin-right: 30px;margin-top: 20px;">
			<img width=170 height=80 src="{{base_path().'/storage/app/logo/firma_gastroclinica.png'}}" align=right hspace=12><br>
		</div>
	</main>
</body>

</html>
