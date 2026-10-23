<!DOCTYPE html>
<html>
<head>

	<title>Resumen Procedimiento</title>
	<style type="text/css">
	@page { margin: 110px 60px; }
    #header { position: fixed; left: 0px; top: -100px; right: 0px; height: 150px; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: -315px; right: 0px; height: 350px; font-size: 10px !important;}
			body{
				margin: 0;
				padding: 0;
			}
		  #encabezado td{
		  	border-style: solid;
		  	border-width: thin;
		  	margin: 0;
		  	padding: 0px 10px;
		  }
		  #contenido{
		  	margin-bottom: 10px;
		  }
		  .final td{
		  	border-style: solid;
		  	border-width: thin;
		  	margin: 0;
		  	padding: 2px 10px;
		  }
		  table{
		  	border-spacing: 0px;
		  	margin: 0;
		  	padding: 0;
		  	margin-left: -15px;
		  }
		  div
		  {
		  	margin: 0;
		  	padding: 0;
		  }
		  #hallazgos table{
		  	margin-left: 10px;
		  }
	</style>
</head>
<body>
	<div id="header">
		<table>
			<tr>
				@php
					$agenda = Sis_medico\Agenda::find($historia->id_agenda);
					if($protocolo->procedimiento->id_empresa!=null){
						$empresa = $protocolo->procedimiento->empresa;
					}else{
						$empresa = $agenda->empresa;
					}
				@endphp
				<td style="width: 150px;"><img style="margin: 0;width: 180px;" src="{{base_path().'/storage/app/logo/'.$empresa->logo_form}}"	></td>

				@if($historia->id_seguro != 5)
					@if(!is_null($empresa->logo_form))
						<!--td style="width: 150px;"><img style="margin: 0;width: 180px;" src="{{base_path().'/storage/app/logo/'.$empresa->logo_form}}"	></td-->
						<td colspan="2">
							<p  style="font-size: 18px; text-align: center; width: 500px; margin-left:0px; "><b style="text-align: center;">{{$empresa->nombre_form}}</b></p>
						</td>
					@else
						<td colspan="2">
							<p  style="font-size: 19px; text-align: center; width: 350px; margin-left:180px; "><b style="text-align: center;">{{$empresa->nombre_form}}</b></p>
						</td>
					@endif
				@else
					<td colspan="2">
						<p  style="font-size: 18px; text-align: center; width: 500px; margin-left:0px; "><b style="text-align: center;">{{$empresa->nombre_form}}</b></p>
					</td>
				@endif



			</tr>
		</table>
	</div>
	<div id="contenido">
		<table id="encabezado">
			<tr>
				<td style="width: 80px;"><b>Paciente:</b></td>
				<td style="width: 250px;"><b style="font-size: 11px;">{{ $paciente->apellido1}} @if($paciente->apellido2 != '(N/A)'){{ $paciente->apellido2}}@endif {{ $paciente->nombre1}} @if($paciente->nombre2 != '(N/A)' ){{ $paciente->nombre2}}@endif</b></td>
				<td style="width: 25px;"><b>Edad:</b></td>
				<td style="width: 40px; font-size: 12px;" >{{$edad}} años</td>
				<td style="width: 70px;"><b>Identificacion:</b></td>
				<td><b style="font-size: 12px;">{{$paciente->id}}</b></td>
			</tr>
			<tr>
				<td><b>Procedimiento:</b></td>
				<td colspan="5"><b style="font-size: 12px;">@if($procedimiento_completo != null){{$procedimiento_completo->nombre_completo}}@else<?php
$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $protocolo->id_hc_procedimientos)->get();

$mas   = true;
$texto = "";

foreach ($adicionales as $value2) {
    if ($mas == true) {
        $texto = $texto . $value2->procedimiento->nombre;
        $mas   = false;
    } else {
        $texto = $texto . ' + ' . $value2->procedimiento->nombre;
    }
}
?><?php echo e($texto); ?>@endif</b></td>
			</tr>
			<tr>
				<td><b>Referido por:</b></td>
				<td style="font-size: 12px;"> @if(!is_null($protocolo->referido_por)) {{$protocolo->referido_por}} @endif</td>
				<td colspan="2"><b>Fecha:</b></td>
				<td colspan="2" style="font-size: 12px;">@if($protocolo->fecha == null){{date('d/m/Y', strtotime($historia->fecha_atencion))}}@else{{date('d/m/Y', strtotime($protocolo->fecha))}}@endif</td>
			</tr>
		</table>
		<br>
		<table style="margin-left: -23px;padding: 0;" >

			@php  $i = 0;   $j = 0;@endphp
			@foreach($imagenes as $value)
				@if($i < 2)
					@if($j == 0)
					<tr style="padding: 0;">
					@endif
						@if($value->seleccionado_recortada == 1)
						<td style="margin: 0;padding: 0 2px 0px 0px;">
							<div style="overflow: hidden; width: 372px; height: 372px; @if($j != 1) @if($j != 0){{"margin-left: -171px;"}}@else{{"margin-left: 0;"}}@endif @else @if($imagenes[0]->seleccionado_recortada == 1){{'margin-left: -171px;'}}@else{{'margin-left: -13px;'}}@endif @endif  ">
								<img style="width: 530px; height: 372px;" src="{{base_path().'/storage/app/hc_ima/'.$value->nombre}}" hspace=12>
							</div>
						</td>
						@else
						<td style="margin: 0;padding: 0 2px 0px 0px;">
							<div style="overflow: hidden; width: 372px; height: 372px; @if($j == 0){{'margin-left: 0px;'}}@elseif($j==1) @if($imagenes[0]->seleccionado_recortada == 1){{'margin-left: -171px;'}}@else{{'margin-left: -13px;'}}@endif @endif ">
								<img style="width: 360px; height: 372px;position: absolute;" src="{{base_path().'/storage/app/hc_ima/'.$value->nombre}}" hspace=12>
							</div>
						</td>
						@endif
					@if($j >= 1)
					</tr>
					@endif
					@php $i = $i+1;$j = $j+1; @endphp
				@endif
			@endforeach


			</tr>
		</table>
		<hr>
		<!--table width="716px;" class="final">
			<tr>
				<td >Hallazgos</td>
			</tr>
			<tr>
				<td style="font-size: 12px !important;"><?php echo $protocolo->hallazgos; ?></td>
			</tr>
		</table-->
		<div style="width: 107%;margin-left: -17px;border: 1px black solid;">
			<span>Hallazgos</span>
		</div>
		<div id="hallazgos" style="width: 107%;margin-left: -17px;border: 1px black solid;font-size: 11px !important;">
			<?php echo $protocolo->hallazgos; ?>
		</div>
		<hr>
		<table width="716px;" class="final">
			<tr>
				<td >Conclusiones </td>
			</tr>
			<tr>
				<td style="font-size: 10px !important;font-family: 'Source Sans Pro',sans-serif;" ><?php echo $protocolo->conclusion; ?></td>
			</tr>
		</table>
	</div>

	<div id="footer">
		<br>
		<br>
		<div style="width: 100%;">
			@if(!is_null($protocolo->procedimiento->id_doctor_responsable))
				@php
					if(!is_null($protocolo->procedimiento->id_doctor_responsable)){
			            $firma_2 =  \Sis_medico\Firma_Usuario::where('id_usuario', $protocolo->procedimiento->id_doctor_responsable)->get();
			        }else{
			            $firma_2 = null;
			        }
			        $responsable = \Sis_medico\User::find($protocolo->procedimiento->id_doctor_responsable);
			        //dd($responsable);
				@endphp
				<div style="width: 325px; display: inline-block;">
					<p style="text-align: center;">
						@if((!is_null($protocolo->procedimiento->id_doctor_examinador2)))
							@if($firma != "[]")
							<img width=150 height=100 src="{{base_path().'/storage/app/avatars/'.$firma[0]->nombre}}" style="" align=center hspace=12><br>
							<b>DR. {{$protocolo->procedimiento->doctor_firma->nombre1}} {{$protocolo->procedimiento->doctor_firma->apellido1}} {{$protocolo->procedimiento->doctor_firma->apellido2}}</b><br>
							@else
								<br><br><br><br><br>
								<b>DR.{{$protocolo->procedimiento->doctor_firma->nombre1}} {{$protocolo->procedimiento->doctor_firma->apellido1}} {{$protocolo->procedimiento->doctor_firma->apellido2}}</b><br>
							@endif
						@else
							<br><br><br><br><br>
							<b>DR. CARLOS ROBLES MEDRANDA</b><br>
						@endif
					</p>
				</div>
				<div style="width: 325px;display: inline-block;">
					<p style="text-align: center;">
						@if((!is_null($protocolo->procedimiento->id_doctor_responsable)))
							@if($firma_2 != "[]")
							<img width=150 height=100 src="{{base_path().'/storage/app/avatars/'.$firma_2[0]->nombre}}" style="" align=center hspace=12><br>
							<b>DR. {{$responsable->nombre1}} {{$responsable->apellido1}} {{$responsable->apellido2}} (RESPONSABLE)</b><br>
							@else
								<br><br><br><br><br>
								<b>DR.{{$responsable->nombre1}} {{$responsable->apellido1}} {{$responsable->apellido2}} (RESPONSABLE)</b><br>
							@endif
						@endif
					</p>
				</div>
			@else
			<div style="width: 650px; display: inline-block;margin-top:-30px">
				<p style="text-align: center;">
					@if((!is_null($protocolo->procedimiento->id_doctor_examinador2)))
						@if($firma != "[]")
						<img width=150 height=100 src="{{base_path().'/storage/app/avatars/'.$firma[0]->nombre}}" style="" align=center hspace=12><br>
							@if($firma[0]->id_usuario=='094346835')
								<b>Dra. ISABEL FARIA URDANETA</b><br>
							@else
								<b>DR. {{$protocolo->procedimiento->doctor_firma->nombre1}} {{$protocolo->procedimiento->doctor_firma->apellido1}} {{$protocolo->procedimiento->doctor_firma->apellido2}}</b><br>
							@endif

						@else
							<br><br><br><br><br>
							<b>DR.{{$protocolo->procedimiento->doctor_firma->nombre1}} {{$protocolo->procedimiento->doctor_firma->apellido1}} {{$protocolo->procedimiento->doctor_firma->apellido2}}</b><br>
						@endif
					@else
						<br><br><br><br><br>
						<b>DR. CARLOS ROBLES MEDRANDA</b><br>
					@endif
				</p>
			</div>

			@endif
		</div>
	</div>

</body>
</html>