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
			font-size: 10px;
			width: 100%;

		}

		.number {
			padding-right: 9px;
			text-align: right;
		}

		.number_total {
			padding-right: 10px;
			text-align: right;
			font-weight: bold;
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
		}

		.cabecera_css {
			font-weight: bold;
			font-size: 9px;
		}
	</style>

</head>

<body>
	<header class="header">

	</header>
	<footer>
		<div style="text-align: center;width: 100%;" class="ftr">

		</div>
	</footer>

	<main>

		<table border="0.1px" cellspacing="0" cellpadding="0" class="table">
			<tr>
				<td colspan="1" class="cabecera_css" style="width: 20%;"> <b>Nombre del Prestador :</b> </td>
				<td colspan="9" class="css_descripcion" style="width: 80%;">{{ $empresa->razonsocial }} </td>
			</tr>
			<tr>
				<td colspan="1" class="cabecera_css" style="width: 20%;"> <b>Seguro</b> </td>
				<td colspan="9" class="css_descripcion" style="width: 80%;">INSTITUTO ECUATORIANO DE SEGURIDAD SOCIAL</td>
			</tr>
			<tr>
				<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Nombre del Paciente</b></td>
				<td colspan="9" class="css_descripcion" style="width: 80%;">{{ $archivo_plano_first->paciente->apellido1 . ' ' . $archivo_plano_first->paciente->apellido2 . ' ' . $archivo_plano_first->paciente->nombre1 . ' ' . $archivo_plano_first->paciente->nombre2 }}</td>
			</tr>
			<tr>
				<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Cedula de identidad</b></td>
				<td colspan="9" class="css_descripcion" style="width: 80%;">{{ $archivo_plano_first->id_paciente }}</td>
			</tr>
			<tr>
				<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Historia Clinica</b></td>
				<td colspan="9" class="css_descripcion" style="width: 80%;">{{ substr($archivo_plano_first->id_paciente,5,10) }}</td>
			</tr>
			<tr>
				<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Fecha de Ingreso</b></td>
				@php
				$fecha_ing_inv = date("d/m/Y", strtotime(substr($archivo_plano_first->fecha_ing, 0, 10)));
				@endphp
				<td colspan="9" class="css_descripcion" style="width: 80%;">{{ $fecha_ing_inv }}</td>
			</tr>
			<tr>
				<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Fecha de Egreso</b></td>
				@php
				$fecha_egr_inv = date("d/m/Y", strtotime(substr($archivo_plano_last->fecha_alt, 0, 10)));				
				@endphp
				<td colspan="9" class="css_descripcion" style="width: 80%;">{{ $fecha_egr_inv }}</td>
			</tr>
			<tr>
				<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Procedimiento</b></td>
				<td colspan="9" class="css_descripcion" style="width: 80%;">{{ $txt_texto1 }}</td>
			</tr>
			<tr>
				<td colspan="1" class="cabecera_css" style="width: 20%;"><b>Diagnostico</b></td>
				<td colspan="9" class="css_descripcion" style="width: 80%;">{{ $txt_cie10 }}</td>
			</tr>
			<tr>
				<td colspan="10" class="titulo_css"  style="width: 100%;"><b>PLANILLA DE CARGOS DEL PROVEEDOR(CONSULTA EXTERNA, HOSPITALIZACION Y EMERGENCIA)</b></td>
			</tr>

		</table>
		<table border="0.1px" cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th colspan="10" class="titulo_css">HONORARIOS MEDICOS</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="5%" class="cabecera_css">Cie10</td>
					<td width="5%" class="cabecera_css">Codigo</td>
					<td width="45%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="9%" class="cabecera_css">Total</td>
				</tr>
				@php
				$total_honor = 0;
				@endphp
				@foreach ($archivo_plano_reg as $value1)
				@php
				$total = 0;
				$honor_medicos = Illuminate\Support\Facades\DB::table('archivo_plano_detalle as apd')
				->where('id_ap_cabecera', $value1->id)
				->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
				->orderby('apd.porcentaje_honorario', 'desc')
				->orderby('apt.secuencia', 'asc')
				->where('apt.tipo_ex', 'HME')
				->where('apd.estado', '1')
				->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')
				->get();
				@endphp
				@foreach ($honor_medicos as $value)
				<tr>
					{{ $total += $value->total }}
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td>{{ $value1->cie10 }}</td>
					<td>{{ $value->codigo }}</td>
					<td class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($value->valor, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->subtotal, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->porcentaje10, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->iva, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->total, 2), 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				@php
				$total_honor += $total;
				@endphp
				@endforeach
				<tr>
					<td width="5%" colspan="4" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="5" class="subtitulo_css">HONORARIOS MEDICOS</td>
					<td width="5%" colspan="1" class="number_total">${{ number_format($total_honor, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.1px" cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th colspan="10" class="titulo_css">MEDICINAS - VALOR AL ORIGEN</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="5%" class="cabecera_css">Cie10</td>
					<td width="5%" class="cabecera_css">Codigo</td>
					<td width="45%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="9%" class="cabecera_css">Total</td>
				</tr>
				@php $total2 = 0; @endphp
				@foreach ($archivo_plano_reg as $value)
				@php
				$totalmed = 0;
				$medicinas = Illuminate\Support\Facades\DB::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $value->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();
				@endphp
				@foreach ($medicinas as $medicina)
				@php $totalmed += $medicina->total; @endphp
				<tr>
					<td class="css_fecha">{{ substr($medicina->fecha, 0, 10) }}</td>
					<td>{{ $value->cie10 }}</td>
					<td></td>
					<td class="css_descripcion">{{ $medicina->descripcion }}</td>
					<td class="number">{{ $medicina->cantidad }}</td>
					<td class="number">${{ number_format($medicina->valor, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($medicina->subtotal, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($medicina->porcentaje10, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($medicina->iva, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($medicina->total, 2), 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				@php $total2 = $total2 + $totalmed; @endphp
				@endforeach

				<tr>
					<td width="5%" colspan="4" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="5" class="subtitulo_css">MEDICINAS - VALOR AL ORIGEN</td>
					<td width="5%" colspan="1" class="number_total">${{ number_format($total2, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.1px" cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th colspan="10" class="titulo_css">INSUMOS - VALOR AL ORIGEN</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="5%" class="cabecera_css">Cie10</td>
					<td width="5%" class="cabecera_css">Codigo</td>
					<td width="45%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="9%" class="cabecera_css">Total</td>
				</tr>
				@php $total_ins_reg = 0; @endphp
				@foreach ($archivo_plano_reg as $value1)
				@php
				$totalins = 0;
				$total_item = 0;
				$insumos = Illuminate\Support\Facades\DB::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $value1->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();
				@endphp
				@foreach ($insumos as $insumo)
				@php
					$total_item = round($insumo->subtotal, 2) + round($insumo->porcentaje10, 2) + ($insumo->subtotal * $insumo->porcentaje_iva);
					$totalins += $total_item; 
				@endphp
					<tr>
						<td class="css_fecha">{{ substr($insumo->fecha, 0, 10) }}</td>
						<td>{{ $value1->cie10 }}</td>
						<td></td>
						<td class="css_descripcion">{{ $insumo->descripcion }}</td>
						<td class="number">{{ $insumo->cantidad }}</td>
						<td class="number">${{ number_format($insumo->valor_unitario, 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($insumo->subtotal, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($insumo->porcentaje10, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($insumo->subtotal * $insumo->porcentaje_iva, 2), 2, ',', ' ') }}</td>
						@php 
						$xvtotal = round($insumo->subtotal, 2) + round($insumo->porcentaje10, 2) + ($insumo->subtotal * $insumo->porcentaje_iva);
						@endphp
						<td class="number">${{ number_format(round($xvtotal, 2), 2, ',', ' ') }}</td>
					</tr>
				@endforeach
				@php $total_ins_reg = $total_ins_reg + $totalins; @endphp
				@endforeach
				<tr>
					<td width="5%" colspan="4" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="5" class="subtitulo_css">INSUMOS - VALOR AL ORIGEN</td>
					<td width="5%" colspan="1" class="number_total">${{ number_format(round($total_ins_reg,2), 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.1px" cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th colspan="10" class="titulo_css">LABORATORIO</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="5%" class="cabecera_css">Cie10</td>
					<td width="5%" class="cabecera_css">Codigo</td>
					<td width="45%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="9%" class="cabecera_css">Total</td>
				</tr>
				@php $total_lab_reg = 0; @endphp
				@foreach ($archivo_plano_reg as $value2)
				@php
				$total_lab = 0;
				$laboratorios = Illuminate\Support\Facades\DB::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $value2->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();
				@endphp
				@foreach ($laboratorios as $laboratorio)
				@php $total_lab += $laboratorio->total; @endphp
				<tr>
					<td class="css_fecha">{{ substr($laboratorio->fecha, 0, 10) }}</td>
					<td>{{ $value2->cie10 }}</td>
					<td>{{ $laboratorio->codigo }}</td>
					<td class="css_descripcion">{{ $laboratorio->descripcion }}</td>
					<td class="number">{{ $laboratorio->cantidad }}</td>
					<td class="number">${{ number_format($laboratorio->valor, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($laboratorio->subtotal, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($laboratorio->porcentaje10, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($laboratorio->iva, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($laboratorio->total, 2), 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				@php $total_lab_reg = $total_lab_reg + $total_lab; @endphp
				@endforeach
				<tr>
					<td width="5%" colspan="4" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="5" class="subtitulo_css">&nbsp;LABORATORIO</td>
					<td width="5%" colspan="1" class="number_total">${{ number_format($total_lab_reg, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.1px" cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th colspan="10" class="titulo_css">IMAGEN(*)</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="5%" class="cabecera_css">Cie10</td>
					<td width="5%" class="cabecera_css">Codigo</td>
					<td width="45%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="9%" class="cabecera_css">Total</td>
				</tr>
				@php $total_ima_reg = 0; @endphp
				@foreach ($archivo_plano_reg as $value)
				@php
				$total_imagen = 0;
				$imagenes = Illuminate\Support\Facades\DB::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $value->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();
				@endphp
				@foreach ($imagenes as $imagen)
				@php 
					$idUsuario = Auth::user()->id;
					if($idUsuario == '0950457978'){
						//dd($archivo_plano_reg,$imagenes);
					} 
					$total_imagen += $imagen->total; 
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($imagen->fecha, 0, 10) }}</td>
					<td>{{ $value->cie10 }}</td>
					<td>{{ $imagen->codigo }}</td>
					<td class="css_descripcion">{{ $imagen->descripcion }}</td>
					<td class="number">{{ $imagen->cantidad }}</td>
					<td class="number">${{ number_format($imagen->valor, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($imagen->subtotal, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($imagen->porcentaje10, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($imagen->iva, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($imagen->total, 2), 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				@php $total_ima_reg = $total_ima_reg + $total_imagen; @endphp
				@endforeach
				<tr>
					<td width="5%" colspan="4" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="5" class="subtitulo_css">IMAGEN(*)</td>
					<td width="5%" colspan="1" class="number_total">${{ number_format($total_ima_reg, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.1px" cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th colspan="10" class="titulo_css">SERVICIOS INSTITUCIONALES</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="5%" class="cabecera_css">Cie10</td>
					<td width="5%" class="cabecera_css">Codigo</td>
					<td width="45%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="9%" class="cabecera_css">Total</td>
				</tr>
				@php $total_serv_reg = 0; @endphp

				@foreach ($archivo_plano_reg as $value)
				@php
				$total_serv = 0;
				$servicios_ins = Illuminate\Support\Facades\DB::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $value->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();
				@endphp
				@foreach ($servicios_ins as $servicio)
				@php $total_serv += $servicio->total @endphp
				<tr>
					<td class="css_fecha">{{ substr($servicio->fecha, 0, 10) }}</td>
					<td>{{ $value->cie10 }}</td>
					<td>{{ $servicio->codigo }}</td>
					<td class="css_descripcion">{{ $servicio->descripcion }}</td>
					<td class="number">{{ $servicio->cantidad }}</td>
					<td class="number">${{ number_format($servicio->valor, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($servicio->subtotal, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($servicio->porcentaje10, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($servicio->iva, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($servicio->total, 2), 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				@php $total_serv_reg = $total_serv_reg + $total_serv; @endphp
				@endforeach
				<tr>
					<td width="5%" colspan="4" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="5" class="subtitulo_css">SERVICIOS INSTITUCIONALES</td>
					<td width="5%" colspan="1" class="number_total">${{ number_format($total_serv_reg, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="0.1px" cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th colspan="10" class="titulo_css">EQUIPOS ESPECIALES</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="7%" class="cabecera_css">Fecha</td>
					<td width="5%" class="cabecera_css">Cie10</td>
					<td width="5%" class="cabecera_css">Codigo</td>
					<td width="45%" class="cabecera_css">Descripcion</td>
					<td width="5%" class="cabecera_css">Cantidad</td>
					<td width="7%" class="cabecera_css">Valor Unitario</td>
					<td width="7%" class="cabecera_css">Subtotal</td>
					<td width="5%" class="cabecera_css">10%</td>
					<td width="5%" class="cabecera_css">Iva</td>
					<td width="9%" class="cabecera_css">Total</td>
				</tr>
				@php $total_equi_reg = 0; @endphp
				@foreach ($archivo_plano_reg as $value)
				@php
				$total_equi = 0;
				$equipos = Illuminate\Support\Facades\DB::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $value->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();
				@endphp
				@foreach ($equipos as $equipo)
				@php $total_equi += $equipo->total @endphp
				<tr>
					<td class="css_fecha">{{ substr($equipo->fecha, 0, 10) }}</td>
					<td>{{ $value->cie10 }}</td>
					<td>{{ $equipo->codigo }}</td>
					<td class="css_descripcion">{{ $equipo->descripcion }}</td>
					<td class="number">{{ $equipo->cantidad }}</td>
					<td class="number">${{ number_format($equipo->valor, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($equipo->subtotal, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($equipo->porcentaje10, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($equipo->iva, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($equipo->total, 2), 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				@php $total_equi_reg = $total_equi_reg + $total_equi; @endphp
				@endforeach
				<tr>
					<td width="5%" colspan="4" class="total_css">Total&nbsp;</td>
					<td width="5%" colspan="5" class="subtitulo_css">EQUIPOS ESPECIALES</td>
					<td width="5%" colspan="1" class="number_total">${{ number_format($total_equi_reg, 2, ',', ' ') }}</td>
				</tr>
				@php
				$liquidacion_total = $total_equi_reg + $total_serv_reg + $total_ima_reg + $total_lab_reg + $total_ins_reg + $total2 + $total_honor;
				@endphp
				<tr>
					<td width="5%" colspan="9" class="total_css">TOTAL LIQUIDACION</td>
					<td width="5%" colspan="1" class="number_total">${{ number_format($liquidacion_total, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>

		<div style="align-content: right;margin-right: 30px;margin-top: 20px;">
			<img width=200 height=100 src="{{base_path().'/storage/app/logo/firma_gastroclinica.png'}}" align=right hspace=12><br>
		</div>
	</main>
</body>

</html>