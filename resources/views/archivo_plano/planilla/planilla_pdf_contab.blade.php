<!DOCTYPE html>
<html>
<head>
	<title>PLANILLA PUBLICA</title>
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
      color:#0000;
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

    .h3{
      font-family: 'BrixSansBlack';
      font-size: 12pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

    .table_vt{
        	
    	margin-right:0px;
    	margin-top: 0px; 
    	margin-left: 0px; 
    	margin-bottom: 0px; 
    	font-size: 10px;
    	width: 100%;

    } 

    .css_descripcion{
    	font-size: 8px;
   	}  
   	.css_fecha{
   		font-size: 8px;
   	}
   	.titulo_css{

   		text-align: center;
   		background-color: #3d7ba8;
   		color:  white;
   	}	
   	.total_css{

   		text-align: center;
			font-weight: bold;
   	}

   	.number{
        
    	padding-right: 1px;
    	text-align:right;
    }  

  </style>
       
</head>
<body>
	<div id="page_pdf">              
		<table id="factura_head">
	      <tr>
	        <td class="info_empresa" style="width: 45%;">
	          <div style="text-align: center">
	            <img src="{{base_path().'/storage/app/logo/'.$archivo_plano->empresa->logo}}"  style="width:200px;height: 100px">
	          </div>
	          <div style="text-align: center; font-size:11px">
	            R.U.C.: @if($archivo_plano != null and isset($archivo_plano->empresa)){{$archivo_plano->empresa->id}}@endif<br/>
	            Nombre Comercial: <br><strong>@if($archivo_plano != null and isset($archivo_plano->empresa)){{$archivo_plano->empresa->nombrecomercial}}@endif</strong><br/>
	            Teléfono: @if($archivo_plano != null and isset($archivo_plano->empresa)){{$archivo_plano->empresa->telefono1}}@endif<br/>
	            Dir.Matriz: <br><strong>@if($archivo_plano != null and isset($archivo_plano->empresa)){{$archivo_plano->empresa->direccion}}@endif</strong><br/>
	            <br/>
	          </div>
	        </td>
	        @php if(!is_null($archivo_plano)) {$paciente = $archivo_plano->paciente;} else {$paciente = null;} @endphp
	        <td class="info_factura" style="width: 55%;">
	          <div class="round">
	            <span class="h3" style="padding:20px"><strong>DETALLE DE PLANILLA PÚBLICA</strong></span>
	            <p style="padding-left: 10px;font-size: 15px;">
	              Paciente :<strong> @if(!is_null($paciente)){{$paciente->id}}<br>{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}}  {{$paciente->nombre2}} @endif</strong><br />
	              Procedimiento:<br><strong> @if($archivo_plano != null and isset($archivo_plano->id)){{ $archivo_plano->nom_procedimiento }}@endif</strong><br />
	              Fecha: <strong>@if($archivo_plano != null and isset($archivo_plano->id)){{ substr($archivo_plano->fecha_ing, 0, 10) }}@endif</strong><br />
	            </p>
	          </div>
	        </td>
	      </tr>
	    </table>
		@if($mensaje=="")	
		<table border="1" cellspacing="0" cellpadding="0" class="table_vt">
			<thead>
			    <tr>
				   <th colspan="9" class="titulo_css" >HONORARIOS MEDICOS</th>
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
						<td >{{ $value->codigo }}</td>
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
						<td ></td>
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
			    		$total_item = $value->subtotal + $value->porcentaje10 + ($value->subtotal * $value->porcentaje_iva);
						$total_item = round($total_item,2);
			    		$totalins += $total_item; 
			    	@endphp
                    <tr>
                    	<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
						<td ></td>
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
						<td >{{ $value->codigo }}</td>
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
						<td >{{ $value->codigo }}</td>
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
						<td >{{ $value->codigo }}</td>
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
						<td >{{ $value->codigo }}</td>
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
					if($archivo_plano->valor == 0){
						$archivo_plano->update(['valor'=>$liquidacion_total]);	
					}
			    @endphp
			    <tr>
			    	<td width="5%" colspan="8" class="total_css">TOTAL LIQUIDACION</td>
			    	<td width="5%" colspan="1" class="number">${{ number_format($liquidacion_total, 2, ',', ' ') }}</td>
			    </tr>
			</tbody>
		</table>
		@else 
			<h3>{{strtoupper($mensaje)}}</h3>
		@endif

	</div>
</body>
</html>	