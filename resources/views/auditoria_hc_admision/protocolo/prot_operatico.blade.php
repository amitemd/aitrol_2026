<!DOCTYPE html>
<html>
	<head>
	  
	    <title>PROTOCOLO OPERATORIO AUDITORÍA</title>

	  	<style>	
	  		@page { margin: 80px 30px; }
		    #header { position: fixed; left: 0px; top: -50px; right: 0px; height: 150px; text-align: center; }
		    #content { position: fixed; left: 0px; top: 20px; right: 0px; height: 150px; }
		    #footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 150px; }
		</style>
	</head>
	<body>	  
		@php
			if($protocolo->auditoria_procedimiento->id_empresa!=null){
				$empresa = $protocolo->auditoria_procedimiento->empresa;
			}else{
				$empresa = $agenda->empresa;
			}

		@endphp
		<div id="header">
			@if($protocolo->historiaclinica->id_seguro != 5)
				@if(!is_null($empresa->logo_form))
				<div style="width: 100px;float:left;"><img style="margin: 0;width: 100px;" src="{{base_path().'/storage/app/logo/'.$empresa->logo_form}}"	></div>
				@else
				<div style="width: 200px;float:left;"><span><b>{{$empresa->nombre_form}}</b></span></div>
				@endif
			@endif
			<h2 align="center" >PROTOCOLO OPERATORIO</h2>
		</div>

		<div id="content"> 	
			<div >
				<table style="font-size: 11px;border: solid 1px;width: 100.8%;">
					<tbody>
						<tr >
							<td width="85"><b># PARTE: </b></td>
							<td width="85">{{substr($protocolo->historiaclinica->id_paciente,6,4)}}</td>
							<td width="85"><b>HC: </b></td>
							<td width="85">{{substr($protocolo->historiaclinica->id_paciente,5,5)}}</td>
							<td width="85"><b>CEDULA</b></td>
							<td width="85">{{$protocolo->historiaclinica->id_paciente}}</td>
						</tr>
						<tr >
							<td colspan="1" style="border-top: solid 1px;"><b>PACIENTE: </b></td>
							<td colspan="5" style="border-top: solid 1px;">{{$protocolo->historiaclinica->paciente->apellido1}} {{$protocolo->historiaclinica->paciente->apellido2}} {{$protocolo->historiaclinica->paciente->nombre1}} {{$protocolo->historiaclinica->paciente->nombre2}}</td>
							
						</tr>
					</tbody>
				</table>
				
			</div>
			<div style="font-size: 11px;border-left: solid 1px;border-right: solid 1px;width: 100.5%">
				<br>
				<table >
					<tbody>
						<tr >
							<td width="85"><b>SEXO: </b></td>
							<td width="40">@if($protocolo->historiaclinica->paciente->sexo=='1')M @else F @endif</td>
							<td width="50"><b>GRUPO SANGUINEO: </b></td>
							<td width="150">{{$protocolo->historiaclinica->paciente->gruposanguineo}}</td>
							<td width="85"><b>EDAD</b></td>
							<td width="85">{{$age}}</td>
						</tr>
						<tr>
							<td width="85"></td>
							<td width="40"></td>
							<td width="50"></td>
							<td width="150"></td>
							<td width="85"></td>
							<td width="85"></td>	
						</tr>
						<tr>
							<td width="85"></td>
							<td width="40"></td>
							<td width="50"></td>
							<td width="150"></td>
							<td width="85"></td>
							<td width="85"></td>	
						</tr>
						<tr >
							<td width="85"><b>XPRE-QX</b></td>
							<td width="40"></td>
							<td width="50"></td>
							<td width="150"></td>
							<td width="85"></td>
							<td width="85"></td>
						</tr>
						<tr >
							<td width="85"><b>PROGRAMADA</b></td>
							<td width="40"></td>
							<td width="50"></td>
							<td width="150"></td>
							<td width="85"></td>
							<td width="85"><b>EMERGENCIA</b></td>
						</tr>
						<tr >
							<td width="85"><b>CIRUGIA</b></td>
							<td width="40"><b>X</b></td>
							<td width="50"><b>SOLICITANTE: </b></td>
							<td width="150">{{$empresa->nombrecomercial}}</td>
							<td width="85"><b>FECHA PROG: </b></td>
							<td width="85">{{$protocolo->fecha_operacion}}</td>
						</tr>
						<tr>
							<td width="85"></td>
							<td width="40"></td>
							<td width="50"></td>
							<td width="150"></td>
							<td width="85"></td>
							<td width="85"></td>	
						</tr>
						<tr>
							<td width="85"></td>
							<td width="40"></td>
							<td width="50"></td>
							<td width="150"></td>
							<td width="85"></td>
							<td width="85"></td>	
						</tr>
						<tr>
							<td colspan="6"><b>OPERACION/PROCEDIMIENTOS REALIZADOS</b></td>
						</tr>
						
						
						
					</tbody>
				</table>
				<div style="width: 95%;padding-left: 10px;font-size: 11px !important;"><?php echo $protocolo->conclusion; ?></div>
				
			</div>
			<div style="font-size: 11px;border-left: solid 1px;border-right: solid 1px;border-top: solid 1px;width: 100.5%">
				<table >
					<tbody>
						<tr >
							<td width="85"><b>EQUIPOS OPERATIVOS: </b></td>
							<td width="85"></td>
							<td width="100"></td>
							<td width="85"></td>
							<td width="85"></td>
							<td width="85"></td>
						</tr>
						<tr>
							<td width="85"><b>CIRUJANO: </b></td>
							<td colspan="5">Dr(a). {{$protocolo->auditoria_procedimiento->doctor_firma->apellido1}} @if($protocolo->auditoria_procedimiento->doctor_firma->apellido2!='N/A'){{$protocolo->auditoria_procedimiento->doctor_firma->apellido2}}@endif {{$protocolo->auditoria_procedimiento->doctor_firma->nombre1}} @if($protocolo->auditoria_procedimiento->doctor_firma->nombre2!='N/A'){{$protocolo->auditoria_procedimiento->doctor_firma->nombre2}}@endif</td>
						</tr>
                        <tr>
							<td width="85"><b>ANESTESIOLOGO: </b></td>
							@if(!is_null($protocolo->usuario_anestesiologo))
							 <td colspan="5">Dr(a). {{$protocolo->usuario_anestesiologo->apellido1}} @if($protocolo->usuario_anestesiologo->apellido2!='N/A'){{$protocolo->usuario_anestesiologo->apellido2}}@endif {{$protocolo->usuario_anestesiologo->nombre1}} @if($protocolo->usuario_anestesiologo->nombre2!='N/A'){{$protocolo->usuario_anestesiologo->nombre2}}@endif</td>
							@else
							  <td colspan="5"></td>
							@endif
							<!--<td colspan="5">Dr(a). VARGAS MARIO</td>-->
						</tr>
						<tr>
							<td width="85"><b>AYUDANTE: </b></td>
							<td colspan="5">@if(!is_null($protocolo->auditoria_procedimiento->doctor_ayudante))  Dr(a). {{$protocolo->auditoria_procedimiento->doctor_ayudante->apellido1}} @if($protocolo->auditoria_procedimiento->doctor_ayudante->apellido2!='N/A'){{$protocolo->auditoria_procedimiento->doctor_ayudante->apellido2}}@endif {{$protocolo->auditoria_procedimiento->doctor_ayudante->nombre1}} @if($protocolo->auditoria_procedimiento->doctor_ayudante->nombre2!='N/A'){{$protocolo->auditoria_procedimiento->doctor_ayudante->nombre2}}@endif @endif</td>
						</tr>
						
						<tr >
							<td width="85"><b>FECHA DE OPERACION</b></td>
							<td width="85">{{$protocolo->fecha_operacion}}</td>
							<td width="100"><b>TIPO DE ANESTESIA: </b></td>
							<td width="85">@if($protocolo->tipo_anestesia=='GENERAL90' || $protocolo->tipo_anestesia=='GENERAL120' || $protocolo->tipo_anestesia=='GENERAL150' || $protocolo->tipo_anestesia=='GENERAL180' || $protocolo->tipo_anestesia=='GENERAL210') GENERAL @else {{$protocolo->tipo_anestesia}} @endif</td>
							<td width="85"></td>
							<td width="85"></td>
						</tr>
						<tr >
							<td width="85"><b>HORA DE INICIO</b></td>
							<td width="85">{{$protocolo->hora_inicio}}</td>
							<td width="100"></td>
							<td width="85"></td>
							<td width="85"></td>
							<td width="85"></td>
						</tr>
						<tr >
							<td width="85"><b>HORA TERMINACION</b></td>
							<td width="85">{{$protocolo->hora_fin}}</td>
							<td width="100"><b>DURACION</b></td>
							<td width="85">{{$protocolo->intervalo_anestesia}} MIN</td>
							<td width="85"></td>
							<td width="85"></td>
						</tr>
					</tbody>
				</table>
				
				
			</div>

			<div style="font-size: 11px;border: solid 1px;width: 100.5%">
				
				<h4 style="padding-left: 35px;">HALLAZGOS QUIRURGICOS</h4>
				<h4 style="padding-left: 5px;">TECNICAS QUIRURGICAS</h4>
				<div style="width: 95%;padding-left: 10px;font-size: 11px !important;"><?php echo $protocolo->hallazgos; ?></div>
				<table >
					<tbody>
						<tr >
							<td colspan="3" width="85"><b>COMPLICACIONES TRANSOPERATORIAS: </b></td>
							<td width="105">{{$protocolo->complicacion}}</td>
							<td width="50"></td>
							<td width="85"></td>
						</tr>
						<tr >
							<td colspan="3" width="85"><b>ESTADO DEL PACIENTE AL TERMINAR LA OPERACION: </b></td>
							<td width="105">I{{$protocolo->estado_paciente}}</td>
							<td width="50"></td>
							<td width="85"></td>
						</tr>
						<tr>
							<td width="85"></td>
							<td width="85"></td>
							<td width="50"></td>
							<td width="85"></td>
							<td width="85"></td>
							<td width="85"></td>	
						</tr>
						<tr>
							<td width="85"></td>
							<td width="85"></td>
							<td width="50"></td>
							<td width="85"></td>
							<td width="85"></td>
							<td width="85"></td>	
						</tr>
						
						<tr >
							<td width="85"><b>FIRMA DEL CIRUJANO: </b></td>
							<td colspan="2"  width="105">Dr(a). {{$protocolo->auditoria_procedimiento->doctor_firma->apellido1}} @if($protocolo->auditoria_procedimiento->doctor_firma->apellido2!='N/A'){{$protocolo->auditoria_procedimiento->doctor_firma->apellido2}}@endif {{$protocolo->auditoria_procedimiento->doctor_firma->nombre1}} @if($protocolo->auditoria_procedimiento->doctor_firma->nombre2!='N/A'){{$protocolo->auditoria_procedimiento->doctor_firma->nombre2}}@endif</td>
							<td width="50"><b>REALIZADO POR: </b></td>
							<td colspan="2" width="85">Dr(a). {{$protocolo->auditoria_procedimiento->doctor_firma->apellido1}} @if($protocolo->auditoria_procedimiento->doctor_firma->apellido2!='N/A'){{$protocolo->auditoria_procedimiento->doctor_firma->apellido2}}@endif {{$protocolo->auditoria_procedimiento->doctor_firma->nombre1}} @if($protocolo->auditoria_procedimiento->doctor_firma->nombre2!='N/A'){{$protocolo->auditoria_procedimiento->doctor_firma->nombre2}}@endif</td>
						</tr>
						<tr >
							<td ><b>LIBRO: </b>@if(!is_null($firma)){{$firma->libro}}@endif</td>
							<td ><b>FOLIO: </b>@if(!is_null($firma)){{$firma->folio}}@endif</td>
							<td ><b>NUM: </b>@if(!is_null($firma)){{$firma->num}}@endif</td>
							<td ></td>
							<td ></td>
							<td ></td>
						</tr>
						
						
						
						
					</tbody>
				</table>	
				
			</div>
		</div>	

		<div id="footer">
		<br>
		<span><i><b>Firma:</b></i></span>
		<br>
		<p style="text-align: center;">
			@if(!is_null($firma))
				<img width=150 height=60 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center hspace=12><br>
				<b style="font-size: 10px;">Dr(a). {{$protocolo->auditoria_procedimiento->doctor_firma->apellido1}} @if($protocolo->auditoria_procedimiento->doctor_firma->apellido2!='N/A'){{$protocolo->auditoria_procedimiento->doctor_firma->apellido2}}@endif {{$protocolo->auditoria_procedimiento->doctor_firma->nombre1}}</b><br>
			@else
				<br><br><br>
				<b style="font-size: 10px;">Dr(a). {{$protocolo->auditoria_procedimiento->doctor_firma->apellido1}} @if($protocolo->auditoria_procedimiento->doctor_firma->apellido2!='N/A'){{$protocolo->auditoria_procedimiento->doctor_firma->apellido2}}@endif {{$protocolo->auditoria_procedimiento->doctor_firma->nombre1}}</b><br>
			@endif
		</p>
	</div> 	
	</body>
</html>			