<!DOCTYPE html>
<html>
<head>

	<title>Resumen Procedimiento Auditoria</title>
	<style type="text/css">
	@page { margin: 30px 60px; }
    #header { position: fixed; left: 0px; top: 0; right: 0px; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: -300px; right: 0px; height: 350px; font-size: 10px !important;}
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
		  	margin-top: 100px;
		  	
		  	font-size: 15px;
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
		  p{
		  	
		  	margin: 0 !important;

		  }
		  
	</style>
</head>
<body>
	<div id="header">
		<table>
			<tr>
				@php
					$agenda = Sis_medico\Agenda::find($historia->id_agenda);
					if($protocolo->auditoria_procedimiento->id_empresa!=null){
						$empresa = $protocolo->auditoria_procedimiento->empresa;
					}else{
						$empresa = $agenda->empresa;
					}
				@endphp
				
				@if(!is_null($empresa->logo_form))
				<td style="width: 150px;"><img style="width: 100px;" src="{{base_path().'/storage/app/logo/'.$empresa->logo_form}}"	></td>
				@else
				<!--td style="width: 150px;font-size: 12px;"><b>{{$empresa->nombre_form}}</b></td-->
				@endif
				
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
				
			</tr>
		</table>
	</div>
	<div id="contenido">
		@php
			if((!is_null($protocolo->auditoria_procedimiento->id_doctor_examinador2))){
				$nombre = "DR. ".$protocolo->auditoria_procedimiento->doctor_firma->nombre1." ".$protocolo->auditoria_procedimiento->doctor_firma->apellido1." ".$protocolo->auditoria_procedimiento->doctor_firma->apellido2;
				if($firma->count() > 0){
					if($firma[0]->id_usuario=='094346835'){
						$nombre = "DRA. ISABEL CRISTINA FARIA URDANETA";
					}
				}
				
			}else{
				$nombre = "DR. CARLOS ROBLES MEDRANDA";
			}
		@endphp
					
		<div style="float: left;width: 45%;border-bottom: 1px solid black;"><b>Estudio</b></div>
		<div style="float: left;width: 10%;"></div>
		<div style="float: left;width: 45%;border-bottom: 1px solid black;"><b>Médico</b></div>
		<div style="clear:both;"></div>
		<div style="float: left;width: 45%;">{{$elasto->procedimiento->nombre}}</div>
		<div style="float: left;width: 10%;"></div>
		<div style="float: left;width: 45%;">{{$nombre}}</div>
		<div style="clear:both;"></div>
		<div style="width: 100%;border-bottom: 1px solid black;"><b>Datos del Paciente</b></div>
		<div style="float: left;width: 20%;"><b>Nombres : </b></div>
		<div style="float: left;width: 25%;">{{ $paciente->nombre1}} @if($paciente->nombre2 != '(N/A)' ){{ $paciente->nombre2}}@endif</div>
		<div style="float: left;width: 10%;"></div>
		<div style="float: left;width: 20%;"><b>Apellidos : </b></div>
		<div style="float: left;width: 25%;">{{ $paciente->apellido1}} @if($paciente->apellido2 != '(N/A)'){{ $paciente->apellido2}}@endif</div>
		<div style="clear:both;"></div>
		<div style="float: left;width: 20%;"><b>Cédula : </b></div>
		<div style="float: left;width: 25%;">{{ $paciente->id}}</div>
		<div style="float: left;width: 10%;"></div>
		<div style="float: left;width: 20%;"><b>Fecha Nacimiento : </b></div>
		<div style="float: left;width: 25%;">{{ $paciente->fecha_nacimiento}}</div>
		<div style="clear:both;"></div>
		<div style="float: left;width: 20%;"><b>Sexo : </b></div>
		<div style="float: left;width: 25%;">@if($paciente->sexo=='1')MASCULINO @else FEMENINO @endif</div>
		<div style="float: left;width: 10%;"></div>
		<div style="float: left;width: 20%;"><b>Fecha Estudio : </b></div>
		<div style="float: left;width: 25%;">@if($protocolo->fecha == null){{date('d/m/Y', strtotime($historia->fecha_atencion))}}@else{{date('d/m/Y', strtotime($protocolo->fecha))}}@endif</div>
		<div style="clear:both;"></div>
		<br>
		<div style="width: 100%;border-bottom: 1px solid black;"><b>Hallazgos:</b></div>
		<div id="hallazgos" style="width: 98%;font-size: 14px !important;line-height: 1.2 !important;">
			<span style='font-size:14px !important;line-height: 1.2 !important;'><?php echo $protocolo->hallazgos; ?></span>
		</div>
		<br>
		<div style="width: 100%;border-bottom: 1px solid black;"><b>Conclusiones:</b></div>
		<div id="conclusiones" style="width: 98%;font-size: 14px !important;line-height: 1.2 !important;">
			<span style='font-size:14px !important;line-height: 1.2 !important;'><?php echo $protocolo->conclusion; ?></span>
		</div>
		<div id="footer">
			<div style="width: 100%;">
				@if(!is_null($protocolo->auditoria_procedimiento->id_doctor_responsable))
					@php
						
						if(!is_null($protocolo->auditoria_procedimiento->id_doctor_responsable)){
				            $firma_2 =  \Sis_medico\Firma_Usuario::where('id_usuario', $protocolo->auditoria_procedimiento->id_doctor_responsable)->get();
				        }else{
				            $firma_2 = null;
				        }
				        $responsable = \Sis_medico\User::find($protocolo->auditoria_procedimiento->id_doctor_responsable);
					@endphp
					<div style="width: 325px; display: inline-block;">
						<p style="text-align: center;">
							@if((!is_null($protocolo->auditoria_procedimiento->id_doctor_examinador2)))
								@if($firma != "[]")

								<img width=150 height=100 src="{{base_path().'/storage/app/avatars/'.$firma[0]->nombre}}" style="" align=center hspace=12><br>
								<b>DR. {{$protocolo->auditoria_procedimiento->doctor_firma->nombre1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido2}}</b><br>
								@else
									<br><br><br><br><br>
									<b>DR.{{$protocolo->auditoria_procedimiento->doctor_firma->nombre1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido2}}</b><br>
								@endif
							@else
								<br><br><br><br><br>
								<b>DR. CARLOS ROBLES MEDRANDA</b><br>
							@endif
						</p>
					</div>
					<div style="width: 325px;display: inline-block;">
						<p style="text-align: center;">
							@if((!is_null($protocolo->auditoria_procedimiento->id_doctor_responsable)))
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
				<div style="width: 650px; display: inline-block;">
					<p style="text-align: center;">
						@if((!is_null($protocolo->auditoria_procedimiento->id_doctor_examinador2)))
							@if($firma != "[]")
							<img width=150 height=100 src="{{base_path().'/storage/app/avatars/'.$firma[0]->nombre}}" style="" align=center hspace=12><br>
							<b>DR. {{$protocolo->auditoria_procedimiento->doctor_firma->nombre1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido2}}</b><br>
							@else
								<br><br><br><br><br>
								<b>DR.{{$protocolo->auditoria_procedimiento->doctor_firma->nombre1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido2}}</b><br>
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
		<div style="page-break-after:always;"></div>
		<div style="width: 100%;border-bottom: 1px solid black;margin-top: 100px;"><b>Imágenes:</b></div>
		<br>
		@php $x = 0;$cont=0; @endphp
		@foreach($imagenes as $value)
			@if($value->seleccionado_recortada == 1)
				@php $x++; @endphp
				<div style="width: 40%;float: left;">
					<div style="position:relative;overflow: hidden; width: 362px; height: 270px;margin: 0; padding: 0;  ">
						<img style="margin: 0;width: 530px; height: 270px;" src="{{base_path().'/storage/app/hc_ima/'.$value->nombre}}" hspace=12>
					</div>
				</div>	
			@else
				@php $x++; @endphp
				
				<div style="float: left;position:relative;overflow: hidden; width: 45%; height: 270px; margin: 0; padding: 0;  ">
					<img style="margin: 0;width: 362px; height: 270px;position: absolute; " src="{{base_path().'/storage/app/hc_ima/'.$value->nombre}}" hspace=12>
				</div>
				
					
			@endif
			@if($x==2)
				@php $x=0; @endphp
				<div style="clear: both;"></div>
			@endif
			@php $cont++; @endphp	
			@if($cont == 6)
				<div ><br><br><br><br><br><br></div>
			@endif
		@endforeach

	</div>
	<div id="footer">
		<div style="width: 100%;">
			@if(!is_null($protocolo->auditoria_procedimiento->id_doctor_responsable))
				@php
					
					if(!is_null($protocolo->auditoria_procedimiento->id_doctor_responsable)){
			            $firma_2 =  \Sis_medico\Firma_Usuario::where('id_usuario', $protocolo->auditoria_procedimiento->id_doctor_responsable)->get();
			        }else{
			            $firma_2 = null;
			        }
			        $responsable = \Sis_medico\User::find($protocolo->auditoria_procedimiento->id_doctor_responsable);
			        //dd($responsable);
				@endphp
				<div style="width: 325px; display: inline-block;">
					<p style="text-align: center;">
						@if((!is_null($protocolo->auditoria_procedimiento->id_doctor_examinador2)))
							@if($firma != "[]")

							<img width=150 height=100 src="{{base_path().'/storage/app/avatars/'.$firma[0]->nombre}}" style="" align=center hspace=12><br>
							<b>DR. {{$protocolo->auditoria_procedimiento->doctor_firma->nombre1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido2}}</b><br>
							@else
								<br><br><br><br><br>
								<b>DR.{{$protocolo->auditoria_procedimiento->doctor_firma->nombre1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido2}}</b><br>
							@endif
						@else
							<br><br><br><br><br>
							<b>DR. CARLOS ROBLES MEDRANDA</b><br>
						@endif
					</p>
				</div>
				<div style="width: 325px;display: inline-block;">
					<p style="text-align: center;">
						@if((!is_null($protocolo->auditoria_procedimiento->id_doctor_responsable)))
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
			<div style="width: 650px; display: inline-block;">
				<p style="text-align: center;">
					@if((!is_null($protocolo->auditoria_procedimiento->id_doctor_examinador2)))
						@if($firma != "[]")
						<img width=150 height=100 src="{{base_path().'/storage/app/avatars/'.$firma[0]->nombre}}" style="" align=center hspace=12><br>
						<b>DR. {{$protocolo->auditoria_procedimiento->doctor_firma->nombre1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido2}}.</b><br>
						@else
							<br><br><br><br><br>
							<b>DR.{{$protocolo->auditoria_procedimiento->doctor_firma->nombre1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido1}} {{$protocolo->auditoria_procedimiento->doctor_firma->apellido2}}</b><br>
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