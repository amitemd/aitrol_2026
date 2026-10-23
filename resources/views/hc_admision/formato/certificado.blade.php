@php
	$empresa = 0;
	$datos = \Sis_medico\Empresa::where('id', $agenda->id_empresa)->first();
	if($datos->prioridad == '0'){
		$empresa = 1;
	}
@endphp
<!DOCTYPE html> 
<html>
	<head>

	    <title>CERTIFICADO {{$agenda->id_paciente}}</title>

	  	<style>
	  		@page { margin: 20px 20px;}
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

		</style>
	</head>
	<body style="border: 1.5px solid #009999;">
		<div id="header">

			<div align="right">
				@if($empresa == 1)
				<img style="margin: 15px;width: 180px;" src="{{base_path().'/storage/app/logo/'.$datos->logo}}">
				@else
				<img style="margin: 15px;width: 180px;" src="{{base_path().'/storage/app/logo/endoscopynet_gastroquito.png'}}">
				<!-- <img style="margin: 15px;width: 180px;" src="{{base_path().'/storage/app/logo/logo1391707460001.png'}}"	> -->
				@endif
			</div>
			<!--p align="left">{{$datos->ciudad}}, {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{substr($date, 0, 4)}}</p-->
			<p align="left">{{$datos->ciudad}}, {{substr($cfecha, 8, 2)}}  de <?php $mes  = substr($cfecha, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{substr($cfecha, 0, 4)}}</p>

			@if($agenda->id_doctor1 == '4444444444')
				<h4 align="center" >CERTIFICADO DE ASISTENCIA</h4>
			@else
				<h4 align="center" >CERTIFICADO MÉDICO</h4>
			@endif
		</div>

		<div id="content" >
			<p >A quien interese:</p>
			<p>El que suscribe, @if($doctor->id == '3596988777') Dr. Cesar Salazar Bowen @else {{$doctor->apellido1}} @if($doctor->apellido2!='(N/A)') {{$doctor->apellido2}} @endif {{$doctor->nombre1}} @endif, @if($doctor->id_tipo_usuario=='3') médico @endif {{$especialidad->nombre}} en ejercicio legal de su profesión certifica:</p>
			<p>Por medio del presente, certifico que el paciente {{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}} con C.I: {{$paciente->id}}, quien se acerca a nuestra institución para realizar <b>{{$tipo}}</b>, el día {{substr($cfecha, 8, 2)}}  de <?php $mes = substr($cfecha, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{substr($cfecha, 0, 4)}} @if($desde!=null && $hasta!=null) , desde: {{$desde}} hasta: {{$hasta}} @endif. <br>
			<b>Dirección de Domicilio:     </b> {{$paciente->direccion}}<br>
			<b>Teléfonos:                  </b> {{$paciente->telefono1}}<br>
			<b>Institución/Empresa:        </b> {{$institucion}}<br>
			<b>Puesto del Paciente:        </b> {{$paciente->ocupacion}}<br>
			<b>Numero de Historia clinica: </b> {{$paciente->id}}<br>
			</p>
			@if($agenda->id_doctor1 != '4444444444')
			<p>
				<b>Presenta Sintomas:</b> SI ( @if($sintoma == 'si') X @endif) &nbsp;&nbsp;&nbsp; NO ( @if($sintoma == 'no') X @endif)<br>
				Aislamiento ( @if($reposo == 'aislamiento') X @endif) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Reposo Absoluto ( @if($reposo == 'reposo_absoluto') X @endif) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Teletrabajo ( @if($reposo == 'teletrabajo') X @endif) <br>
				<b>Causa: &nbsp;&nbsp; </b> Enfermedad General ( @if($causa == 'enfermedad_general') X @endif) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Accidente de Trabajo ( @if($causa == 'a_trabajo') X @endif) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Accidente de Transito ( @if($causa == 'a_transito') X @endif) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Maternidad ( @if($causa == 'maternidad') X @endif) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Enf. Catastrofica ( @if($causa == 'e_catastrofica') X @endif)
			</p>
			@endif

			@if($descanso>0)
				<p>Dándosele descanso médico {{$descanso}}({{$letras}}) días desde el día {{strtolower(\Sis_medico\Observers\NumberFormat::milmillon(substr($cfecha, 8, 2)))}}  de <?php $mes = substr($cfecha, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{strtolower(\Sis_medico\Observers\NumberFormat::milmillon(substr($cfecha, 0, 4)))}} ({{date('d-m-Y', strtotime($cfecha))}}) hasta el día {{strtolower(\Sis_medico\Observers\NumberFormat::milmillon(substr($fecha_hasta, 8, 2)))}} de <?php $mes = substr($fecha_hasta, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{strtolower(\Sis_medico\Observers\NumberFormat::milmillon(substr($fecha_hasta, 0, 4)))}} ({{date('d-m-Y', strtotime($fecha_hasta))}}) para su total recuperación.</p>
			@endif
			<p>Certificado que extiendo profesionalmente a {{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}} @if($familiar!=null) , acompañado por {{$familiar}} @endif y puede hacer uso del mismo como considere necesario.</p>
			@if($diagnostico!=null)

			<div class="dg" >
				<span ><b>Diagnóstico CIE10:</b></span>
				<?php echo $diagnostico; ?>
			</div>
			@endif
			<p>Atentamente,</p>
			<p style="text-align: left;">
				@if(!is_null($firma))
					<img width=150 height=100 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center hspace=12><br>
					<span style="font-size: 12px;"> @if($doctor->id == '0908414246') Pat. @elseif($doctor->id_tipo_usuario=='3') Dr(a). @elseif($doctor->id_tipo_usuario=='10') Lcdo. @else Lcdo. @endif {{$doctor->apellido1}} @if($doctor->apellido2!='(N/A)') {{$doctor->apellido2}} @endif {{$doctor->nombre1}} @if($doctor->nombre2!='(N/A)') {{$doctor->nombre2}} @endif</span><br>
					<span style="font-size: 12px;"><strong>Cédula: </strong>{{$doctor->id}}</span>
				@else
					<br><br><br><br><br>
					<b> @if($doctor->id == '0908414246') Pat. @elseif($doctor->id_tipo_usuario=='3') Dr(a). @elseif($doctor->id_tipo_usuario=='10') Lcdo. @else Lcdo. @endif {{$doctor->apellido1}} @if($doctor->apellido2!='(N/A)') {{$doctor->apellido2}} @endif {{$doctor->nombre1}} @if($doctor->nombre2!='(N/A)') {{$doctor->nombre2}} @endif</b><br>
					<span style="font-size: 12px;"><strong>Cédula: </strong>{{$doctor->id}}</span>
				@endif
			</p>
		</div>

		<!-- <div id="footer">
			@if($empresa == 1)
			<p style="font-size: 12px;">Telf: (02) 4772310 / 0967658270 / <span style="color: blue;text-decoration: underline;">www.endoscopynet.com</span> MARIANA DE JESUS Y  ÑUÑO DE VALDERRAMA PISO 2 CONSULTORIO 213, y piso 6 Consul. 605 y 606, Quito</p>
			@else
			<p style="font-size: 12px;">Telf: (04) 2109180 / Fax: (04) 2109180 / iecedgye@gmail.com / <span style="color: blue;text-decoration: underline;">www.ieced.com.ec</span> Av. Abel Romeo Castillo y Av. Juan Tanca Marengo, Torre Vitales 1 -Mezanine 3, Guayaquil</p>
			@endif
		</div> -->
		<div id="footer">
			<p style="font-size: 12px;">Telf: @if($datos->telefono1!='(N/A)') {{$datos->telefono1}} @endif / @if($datos->telefono2!='(N/A)') {{$datos->telefono2}} @endif / <span style="color: blue;text-decoration: underline;">@if($datos->email!='(N/A)') {{$datos->email}} @endif</span> @if($datos->direccion!='(N/A)') {{$datos->direccion}} @endif, @if($datos->ciudad!='(N/A)') {{$datos->ciudad}} @endif</p>
		</div>
	</body>
</html>