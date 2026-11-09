<!DOCTYPE html>
<html>
	<head>

	    <title>CERTIFICADO </title>

	  	<style>
	  		@page { margin: 20px 20px; font-size: 14px;}
		    #header { position: fixed; left: 20px; top: -10px; right: 20px; height: 150px; text-align: center;}
		    #content { position: fixed; left: 20px; top: 200px; right: 20px; height: 150px; }
		    #footer { position: fixed; left: 20px; bottom: -90px; right: 20px; height: 150px; }
		    p {
    			font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 16px;
    			text-align:justify;
    			font-style: italic;
			}
			.dg > p {
				font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 12px;
    			text-align:justify;
    			font-style: italic;
			}
			table{
				width: 100%;
			}

		</style>
	</head>
	<body >
		<div id="header">
			<div align="center"><img style="width: 180px;" src="{{base_path().'/storage/app/logo/logo1391707460001manabi.png'}}"></div>
			<h4 align="center" style="margin: 0;" >{{$empresa->razonsocial}}</h4>
			<h4 align="center" style="margin: 0;">www.ieced.com.ec &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; www.ieced-med.com</h4>
			<h4 align="center" style="margin: 0;font-size: 10px;" >{{$empresa->direccion}}</h4>
			<h4 align="center" style="margin: 0;font-size: 10px;" >Teléfonos: {{$empresa->telefono1}} / {{$empresa->telefono2}}</h4>
			<h4 align="center" style="border-bottom: 1px solid #3A8CAA;">SOLICITUD DE ESTUDIO HISTOLÓGICO Y/O CITOLÓGICO</h4>
		</div>
		@php $edad = Carbon\Carbon::parse($biopsia_ptv->paciente->fecha_nacimiento)->age; @endphp
		<div id="content" >
			<h4 align="center">DATOS DEL PACIENTE</h4>
			<div>
				<span><b>PACIENTE :</b> {{$biopsia_ptv->paciente->apellido1}} {{$biopsia_ptv->paciente->apellido2}} {{$biopsia_ptv->paciente->nombre1}} {{$biopsia_ptv->paciente->nombre2}}</span>
			</div>
			<table>
				<tr>
					<td style="width: 20%;"><b>EDAD:</b></td>
					<td style="width: 20%;">{{$edad}} años</td>
					<td style="width: 20%;"><b>FECHA:</b></td>
					 @if(is_null($biopsia_ptv->fecha_imprimible))
					<td style="width: 40%;">{{$biopsia_ptv->created_at}}</td>
					@else
					<td style="width: 40%;">{{$biopsia_ptv->fecha_imprimible}}</td>
					@endif
				</tr>
				<tr>
					<td style="border-bottom: 1px solid #3A8CAA;"><b>No. H.CLINICA:</b></td>
					<td style="border-bottom: 1px solid #3A8CAA;">{{$biopsia_ptv->paciente->id}}</td>
					<td style="border-bottom: 1px solid #3A8CAA;"><b>MEDICO:</b></td>
					<td style="border-bottom: 1px solid #3A8CAA;">{{$biopsia_ptv->doctor->apellido1}} {{$biopsia_ptv->doctor->apellido2}} {{$biopsia_ptv->doctor->nombre1}} {{$biopsia_ptv->doctor->nombre2}}</td>
				</tr>
			</table>
			<h4 align="center">{{$biopsia_ptv->tipo->descripcion}}</h4>
			@php $count = 0; @endphp
			<div style="float: left;width: 80%">
			@foreach($biopsia_ptv->detalles as $detalle)
				<div style="float: @if($count == 0)left;@else right; @endif width: 40%"><b>{{$detalle->descripcion}} : </b>{{$detalle->detalle}}</div>
				@php $count = $count + 1; @endphp
				@if($count == 2)
					<div style="clear: both;"></div>
					@php $count = $count = 0; @endphp
				@endif
			@endforeach
			</div>
			<div style="float: right;width: 20%">
				<div ><img style="width: 80%" src="{{base_path().'/public/images'}}/{{$biopsia_ptv->tipo->imagen}}"></div>
			</div>

			<div style="clear: both;"></div>
			<br>
			<h4 align="center" style="border-top: 1px solid #3A8CAA;">OTRAS LOCALIZACIONES</h4>
			<div>
				<span>{{$biopsia_ptv->otras_localizaciones}}</span>
			</div>
			<br>
			<h4 align="center" style="border-top: 1px solid #3A8CAA;">OTROS ÓRGANOS</h4>
			<div>
				<span>{{$biopsia_ptv->otros_organos}}</span>
			</div>
			<br>
			<h4 align="center" style="border-top: 1px solid #3A8CAA;">DATOS CLÍNICOS - ENDOSCÓPICOS Y DE ORIENTACIÓN DIAGNÓSTICA</h4>
			<div>
				<span style="font-size: 12px !important;">{!!html_entity_decode($biopsia_ptv->datos_clinicos)!!}</span>
			</div>
			<br>
			<h4 align="center" style="border-top: 1px solid #3A8CAA;">DIAGNÓSTICO</h4>
			<div style="border-bottom: 1px solid #3A8CAA;">
				<span>{{$biopsia_ptv->diagnostico}}</span>
			</div>
		</div>

		<div id="footer">
			<p style="font-size: 12px;">Dr. {{$biopsia_ptv->doctor->apellido1}} {{$biopsia_ptv->doctor->apellido2}} {{$biopsia_ptv->doctor->nombre1}} {{$biopsia_ptv->doctor->nombre2}}</p>
		</div>
	</body>
</html>
