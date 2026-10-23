<!DOCTYPE html>
<html>
<head>
	<title>Procedimiento vs publico</title>
	<style>
		#page_pdf {
			width: 95%;
			margin: 15px auto 10px auto;
		}

		#factura_head,
		#factura_cliente,
		#factura_detalle {
			width: 100%;
			margin-bottom: 10px;
		}

		#detalle_productos tr:nth-child(even) {
			background: #ededed;
			border-radius: 10px;
			border: 1px solid #3d7ba8;
			overflow: hidden;
			padding-bottom: 15px;

		}

		#detalle_totales span {
			font-family: 'BrixSansBlack';
			text-align: right;
		}

		.logo_factura {
			width: 25%;
		}

		.info_empresa {
			width: 50%;
			text-align: center;
		}

		.info_factura {
			width: 31%;
		}

		.info_cliente {
			width: 69%;
		}

		.textright {
			padding-left: 3;
		}


		.h3 {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 12px;
			display: block;
			background: #3d7ba8;
			color: #FFF;
			text-align: center;
			padding: 5px;
			padding-bottom: 5px;
			margin-bottom: 5px;
			margin-top: 5px;
		}

		.round {
			border-radius: 10px;
			border: 1px solid #3d7ba8;
			overflow: hidden;
			padding-bottom: 15px;
		}

		table {
			border-collapse: collapse;
			font-size: 12pt;
			font-family: 'arial';
			width: 100%;
		}


		table tr:nth-child(odd) {
			background: #FFF;
		}

		table td {
			padding: 4px;
		}

		table th {
			text-align: left;
			color: #3d7ba8;
			font-size: 15px;
		}

		.totales {
			color: #0000;
			font-size: 20px;
			font-weight: bold;
		}

		.datos_cliente {
			font-size: 0.8em;
		}

		.datos_cliente label {
			width: 75px;
			display: inline-block;
		}

		.lab {
			font-size: 18px;
			font-family: 'arial';
		}

		* {
			font-family: 'Arial' !important;
		}

		.mLabel {
			width: 20%;
			display: inline-block;
			vertical-align: top;
			font-weight: bold;
			padding-left: 15px;
			font-size: 0.9em;

		}

		.mValue {
			width: 79%;
			display: inline-block;
			vertical-align: top;
			padding-left: 7px;
			font-size: 0.9em;
		}

		.totals_wrapper {
			width: 100%;
		}

		.totals_label {
			display: inline-block;
			vertical-align: top;
			width: 85%;
			text-align: right;
			font-size: 0.7em;
			font-weight: bold;
			font-family: 'Arial';
		}

		.totals_value {
			display: inline-block;
			vertical-align: top;
			width: 14%;
			text-align: right;
			font-size: 0.7em;
			font-weight: normal;
			font-family: 'Arial';
		}

		.totals_separator {
			width: 100%;
			height: 1px;
			clear: both;
		}

		.separator {
			width: 100%;
			height: 60px;
			clear: both;
		}

		.details_title_border_left {
			background: #3d7ba8;
			border-top-left-radius: 10px;
			color: #FFF;
			padding: 10px;
			padding-left: 10px;
		}

		.details_title_border_right {
			background: #3d7ba8;
			border-top-right-radius: 10px;
			color: #FFF;
			padding: 10px;
			padding-right: 3px;
		}

		.details_title {
			background: #3d7ba8;
			color: #FFF;
			padding: 10px;
		}

		.h3 {
			font-family: 'BrixSansBlack';
			font-size: 12pt;
			display: block;
			background: #3d7ba8;
			color: #FFF;
			text-align: center;
			padding: 3px;
			margin-bottom: 5px;
		}

		.table_vt {

			margin-right: 0px;
			margin-top: 0px;
			margin-left: 0px;
			margin-bottom: 0px;
			font-size: 10px;
			width: 100%;

		}

		.css_descripcion {
			font-size: 8px;
		}

		.css_fecha {
			font-size: 8px;
		}

		.titulo_css {

			text-align: center;
			background-color: #3d7ba8;
			color: white;
		}

		.total_css {

			text-align: center;
			font-weight: bold;
		}

		.number {

			padding-right: 1px;
			text-align: right;
		}
	</style>

</head>

<body>
<div id="page_pdf">
		<table id="factura_head">
			<tr>
				<td class="info_empresa" style="width: 45%;">
					<div style="text-align: center">
						<img src="{{public_path().'/imagenes/endoscopynet_gastroquito.png'}}" style="width:250px;height:100px">
					</div>
					<div style="text-align: center; font-size:0.8em">
						R.U.C.: @if(isset($archivo_plano)) {{$archivo_plano->empresa->id}} @endif<br />
						Nombre Comercial: <br><strong>@if(isset($archivo_plano)){{$archivo_plano->empresa->nombrecomercial}}@endif</strong><br />
						Teléfono: @if(isset($archivo_plano)){{$archivo_plano->empresa->telefono1}}@endif<br />
						Dir.Matriz: <br><strong>@if(isset($archivo_plano)){{$archivo_plano->empresa->direccion}}@endif</strong><br />
						<br />
					</div>
				</td>
				@php 
						if(isset($archivo_plano)) {$paciente = $archivo_plano->paciente;} @endphp
				<td class="info_factura" style="width: 55%;">
					<div class="round">
						<span class="h3" style="padding:20px"><strong>DATOS DEL PACIENTE</strong></span>
						<p style="padding-left: 10px;font-size: 15px;">
							Paciente :<strong> {{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</strong><br />
							Procedimiento:<br><strong> @if(isset($archivo_plano)) {{ $archivo_plano->nom_procedimiento }} @endif</strong><br />
							Fecha: <strong>@if(isset($archivo_plano))  {{ substr($archivo_plano->fecha_ing, 0, 10) }} @endif</strong><br />
						</p>
					</div>
				</td>
			</tr>
		</table>

		<!------------------------------------------------PLANILLA DE COSTOS------------------------------------------------------------->


		<div class="modal-content">
      @php  $acumtc = 0;  $cabeceras = array(1=>'HONORARIOS MEDICOS COSTOS', 2=>'MEDICINAS VALOR AL ORIGEN COSTOS', 3=>'INSUMOS VALOR AL ORIGEN COSTOS', 4=>'IMAGEN (*) COSTOS', 5=>'SERVICIOS INSTITUCIONALES COSTOS', 6=>'EQUIPOS ESPECIALES COSTOS',0=>'OTROS'); @endphp
      @if ($detallesc!='[]') 
      @foreach ($cabeceras as $key => $row)
      <div class="h3" style="width:100%"> {{$row}}</div>
          <div class="table-responsive col-md-12">
                <table style="border: 1px solid;max-width:100%;!important">
                  {{-- <caption><b>{{$row}}</b></caption> --}}
                  <thead>
                  <tr>
                    <th width="10%">Fecha</th>
                    <th width="10%">C&oacute;digo</th>
                    <th width="30%">Descripci&oacute;n</th>
                    <th width="10%">Cantidad</th>
                    <th width="10%">Costo Uni.</th>
                    {{-- <th width="10%">Subtotal</th> --}}
                    {{-- <th width="10%">10%</th>
                    <th width="10%">IVA</th> --}}
                    <th width="10%">TOTAL</th>
                  </tr>
                  </thead> 
                  @php $acumtotalc = 0; @endphp

                  <tbody> 
                    @if ($detallesc!='[]') 
                      @foreach ($detallesc as $item) 
                      @php if ($item->tipo_plantilla==null) $item->tipo_plantilla=0; @endphp
                        @if (isset($item->producto)  and $item->producto->tipo == $key)
                          @if ($item->check==1)
                            <tr>
                              <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;">{{date('d/m/Y COSTOS', strtotime($item->created_at))}}</td>
                              <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;">{{$item->codigo}}</td>
                              <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;"> 
                                @php 
                                
                                if(isset($item->producto)){
                                  $producto_contable = $item->producto->producto_contable(); 
                                } else {
                                  $producto_contable ='[]';
                                }

                                @endphp
                                @if($producto_contable!='[]' or $producto_contable!=null or count($producto_contable) > 0)
                                  @if (isset($producto_contable->nombre))
                                    {{($producto_contable->nombre)}}
                                  @else 
                                    @if(isset($item->producto))
                                    [{{$item->producto->nombre}}]
                                    @endif
                                  @endif
                                @endif
                                
                              </td>
                              <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;">{{$item->cantidad}}</td>
                              <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format($item->precio, 2, '.', ' ')}}</td>
                              @php $subt = $item->cantidad * $item->precio; @endphp 
                              {{-- <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format($subt, 2, '.', ' ')}}</td> --}}
                              {{-- <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">@php $porcent = ((($item->cantidad * $item->precio)*10)/100); @endphp {{number_format($porcent, 2, '.', ' ')}}</td> --}}
                              @php  $imp = 0; $iva = 0; $porcent =0; /*$iva = ($subt*$imp);*/ 
                                    // if (isset($item->producto->iva) && $item->producto->iva==1) {
                                    //     $conf = \Sis_medico\Ct_Configuraciones::find(3);
                                    //     $iva  = ($subt+$porcent) * $conf->iva;
                                    // }
                                    $totalc = ($subt+$porcent+$iva); 
                              @endphp
                           
                              {{-- <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format($iva, 2, '.', ' ')}}</td> --}}
                              <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format($totalc, 2, '.', ' ')}}</td>
                            </tr>  
                            @php $acumtotalc += $totalc;  $acumtc += $totalc; @endphp
                          @endif
                         
                        @endif
                      @endforeach
                    @endif
                  </tbody>

                  <tfoot>
                   <tr>
	                  <td style="border-right: 1px solid;border-top: 1px solid; font-size: 16px;font-weight: bold;text-align: right;" colspan="5">TOTAL {{$row}}</td>
                    <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;text-align: right;">{{ number_format($acumtotalc, 2, '.', ' ') }}</td>
                   </tr>
                  </tfoot>
                </table>
              </div>  
		  <br>
      @endforeach
      <br>
      <table style="border: 1px solid; width: 100%;">
        <tfoot>
          <tr>
           <td style="border-right: 1px solid;border-top: 1px solid; font-size: 16px;font-weight: bold;text-align:right" colspan="6">TOTAL PLANILLA COSTOS</td>
           <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;text-align:right">{{number_format($acumtc, 2, '.', ' ') }}</td>
          </tr>
         </tfoot>
       </table>
    </div>
    @else 
    <h3>PLANILLA NO APROBADA</h3>
    @endif


		<!-------------------------------------------------FIN DE COSTOS--------------------------------------------------->
		<br>
		@php $liquidacion_total = 0; @endphp
		<div>
			<h3 class="h3">PLANILLA PUBLICA</h3>
		</div>
		@if($mensajep=="")
		
		<table border="1" cellspacing="0" cellpadding="0" class="table_vt">
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">DETALLES DE VENTAS PUBLICAS</th>
				</tr>
			</thead>
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">HONORARIOS MEDICOS</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="5%">Fecha</td>
					<td width="5%">Codigo</td>
					<td width="30%">Descripcion</td>
					<td width="5%">Cantidad</td>
					<td width="10%">Valor Unitario</td>
					<td width="10%">Subtotal</td>
					<td width="10%">10%</td>
					<td width="10%">Iva</td>
					<td width="10%">Total</td>
				</tr>
				@php $total = 0; @endphp
				@foreach ($honor_medicos as $value)
				@php $total += $value->total; @endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
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
				<tr>
					<td colspan="4" class="total_css">Total</td>
					<td colspan="4" class="total_css">HONORARIOS MEDICOS</td>
					<td colspan="1" class="number">${{ number_format($total, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="1" cellspacing="0" cellpadding="0" class="table_vt">
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">MEDICINAS - VALOR AL ORIGEN</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="5%">Fecha</td>
					<td width="5%">Codigo</td>
					<td width="30%">Descripcion</td>
					<td width="5%">Cantidad</td>
					<td width="10%">Valor Unitario</td>
					<td width="10%">Subtotal</td>
					<td width="10%">10%</td>
					<td width="10%">Iva</td>
					<td width="10%">Total</td>
				</tr>
				@php $total2 = 0; @endphp
				@foreach ($medicinas as $value)
				@php $total2 += $value->total; @endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td></td>
					<td class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($value->valor, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->subtotal, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->porcentaje10, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->iva, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->total, 2), 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				<tr>
					<td width="5%" colspan="4" class="total_css">Total</td>
					<td width="5%" colspan="4" class="total_css">MEDICINAS - VALOR AL ORIGEN</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total2, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="1" cellspacing="0" cellpadding="0" class="table_vt">
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">INSUMOS - VALOR AL ORIGEN</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="5%">Fecha</td>
					<td width="5%">Codigo</td>
					<td width="30%">Descripcion</td>
					<td width="5%">Cantidad</td>
					<td width="10%">Valor Unitario</td>
					<td width="10%">Subtotal</td>
					<td width="10%">10%</td>
					<td width="10%">Iva</td>
					<td width="10%">Total</td>
				</tr>
				@php $totalins = 0; @endphp
				@foreach ($insumos as $value)
				@php
				$total_item = round($value->subtotal, 2) + round($value->porcentaje10, 2) + ($value->subtotal * $value->porcentaje_iva);
				$totalins += $total_item;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
					<td></td>
					<td class="css_descripcion">{{ $value->descripcion }}</td>
					<td class="number">{{ $value->cantidad }}</td>
					<td class="number">${{ number_format($value->valor_unitario, 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->subtotal, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->porcentaje10, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($value->subtotal * $value->porcentaje_iva, 2), 2, ',', ' ') }}</td>
					<td class="number">${{ number_format(round($total_item, 2), 2, ',', ' ') }}</td>
				</tr>
				@endforeach
				<tr>
					<td width="5%" colspan="4" class="total_css">Total</td>
					<td width="5%" colspan="4" class="total_css">INSUMOS - VALOR AL ORIGEN</td>
					<td width="5%" colspan="1" class="number">${{ number_format($totalins, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="1" cellspacing="0" cellpadding="0" class="table_vt">
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">LABORATORIO</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="5%">Fecha</td>
					<td width="5%">Codigo</td>
					<td width="30%">Descripcion</td>
					<td width="5%">Cantidad</td>
					<td width="10%">Valor Unitario</td>
					<td width="10%">Subtotal</td>
					<td width="10%">10%</td>
					<td width="10%">Iva</td>
					<td width="10%">Total</td>
				</tr>
				@php $total_lab = 0; @endphp
				@foreach ($laboratorio as $value)
				@php
				$total_lab += $value->total;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
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
				<tr>
					<td width="5%" colspan="4" class="total_css">Total</td>
					<td width="5%" colspan="4" class="total_css">LABORATORIO</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total_lab, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="1" cellspacing="0" cellpadding="0" class="table_vt">
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">IMAGEN(*)</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="5%">Fecha</td>
					<td width="5%">Codigo</td>
					<td width="30%">Descripcion</td>
					<td width="5%">Cantidad</td>
					<td width="10%">Valor Unitario</td>
					<td width="10%">Subtotal</td>
					<td width="10%">10%</td>
					<td width="10%">Iva</td>
					<td width="10%">Total</td>
				</tr>
				@php $total_imagen = 0; @endphp
				@foreach ($imagen as $value)
				@php
				$total_imagen += $value->total;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
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
				<tr>
					<td width="5%" colspan="4" class="total_css">Total</td>
					<td width="5%" colspan="4" class="total_css">IMAGEN(*)</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total_imagen, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="1" cellspacing="0" cellpadding="0" class="table_vt">
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">SERVICIOS INSTITUCIONALES</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="5%">Fecha</td>
					<td width="5%">Codigo</td>
					<td width="30%">Descripcion</td>
					<td width="5%">Cantidad</td>
					<td width="10%">Valor Unitario</td>
					<td width="10%">Subtotal</td>
					<td width="10%">10%</td>
					<td width="10%">Iva</td>
					<td width="10%">Total</td>
				</tr>
				@php $total_serv = 0; @endphp
				@foreach ($servicios_ins as $value)
				@php
				$total_serv += $value->total;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
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
				<tr>
					<td width="5%" colspan="4" class="total_css">Total</td>
					<td width="5%" colspan="4" class="total_css">SERVICIOS INSTITUCIONALES</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total_serv, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		<table border="1" cellspacing="0" cellpadding="0" class="table_vt">
			<thead>
				<tr>
					<th colspan="9" class="titulo_css">EQUIPOS ESPECIALES</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="5%">Fecha</td>
					<td width="5%">Codigo</td>
					<td width="30%">Descripcion</td>
					<td width="5%">Cantidad</td>
					<td width="10%">Valor Unitario</td>
					<td width="10%">Subtotal</td>
					<td width="10%">10%</td>
					<td width="10%">Iva</td>
					<td width="10%">Total</td>
				</tr>
				@php $total_equi = 0; @endphp
				@foreach ($equipos as $value)
				@php
				$total_equi += $value->total;
				@endphp
				<tr>
					<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
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
				<tr>
					<td width="5%" colspan="4" class="total_css">Total</td>
					<td width="5%" colspan="4" class="total_css">EQUIPOS ESPECIALES</td>
					<td width="5%" colspan="1" class="number">${{ number_format($total_equi, 2, ',', ' ') }}</td>
				</tr>
				@php
				$liquidacion_total = $total_equi + $total_serv + $total_imagen + $total_lab + $totalins + $total2 + $total;
				@endphp
				<tr>
					<td width="5%" colspan="8" class="total_css">TOTAL LIQUIDACION</td>
					<td width="5%" colspan="1" class="number">${{ number_format($liquidacion_total, 2, ',', ' ') }}</td>
				</tr>
			</tbody>
		</table>
		@else
		<div style="text-align: center;">
			<h3>{{$mensajep}}</h3>
		</div>
		
		@endif
		<br>
		<table border="1" cellspacing="0" cellpadding="0" class="table_vt">  
        	<thead>
			<tr>
			<br>
			<th colspan="9" class="titulo_css">RESUMEN</th>
			</tr>
		</thead>
		<tbody>
			<tr>  
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;font-weight: bold;">Total Ventas (v)</td>
                <td style="width="5%" colspan="8" class="total_css">${{ number_format($liquidacion_total, 2, ',', ' ') }}</td>  
            </tr>  
            <tr>  
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;font-weight: bold;">Total Costos (c)</td>
                <td style="width="5%" colspan="8" class="total_css">${{ number_format($acumtc, 2, ',', ' ') }}</td>  
            </tr>
		@php
			$total_vs = $liquidacion_total - $acumtc;
		@endphp
			<tr>  
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;font-weight: bold;">TOTAL (v) - (c)</td>
                <td style="width="5%" colspan="8" class="total_css">${{ number_format($total_vs, 2, ',', ' ') }}</td>  
            </tr>     
        </tbody>  
    </table>

	</div>
</body>

</html>