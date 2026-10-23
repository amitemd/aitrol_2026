<!DOCTYPE html>
<html>
	<head>

	    <title>HCU_007</title>

	  	<style>
	  		@page { margin: 20px 20px;}
		    #header { position: fixed; left: 20px; top: -10px; right: 20px; height: 150px; text-align: center;}
		   /* #content { position: fixed; left: 20px; top: 10px; right: 20px; height: 150px; } */
		    #footer { position: fixed; left: 20px; bottom: -90px; right: 20px; height: 150px; }
		    p {
    			font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 10px !important;
    			text-align:justify;

			}
			.dg > p {
				font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 12px;
    			text-align:justify;
    			font-style: italic;
			}
			table {
				font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 11px;
    			text-align:justify;
    			width: 103.4%;
    			margin-left: -15px;


			}
			table, th, td {
			  border: 1px solid black;
			  border-collapse: collapse;
			  text-align: center;
			}



		</style>
	</head>
	<body style="border: 1px solid black;">


		<div id="" style="padding-left: 19px; padding-right: 19px; " >
			<b style="font-size: 11px;">FORMULARIO 007</b>
			<table class="table1">
				<thead >
					<tr>
						<th >ESTABLECIMIENTO SOLICITANTE</th>
						<th >NOMBRE</th>
						<th >APELLIDO</th>
						<th >SEXO (F-M)</th>
						<th >EDAD</th>
						<th >N HISTORIA CLINICA</th>
					</tr>
				</thead>
				<tbody>
					<tr style="font-size: 10px !important;">
						@php
							$nombre_empresa = $evolucion->historiaclinica->agenda->empresa->nombrecomercial;

							$procedimiento = Sis_medico\hc_procedimientos::find($evolucion->hc_id_procedimiento);

							if($procedimiento->id_empresa!=null){

								$nombre_empresa = Sis_medico\Empresa::find($procedimiento->id_empresa)->nombrecomercial;

							}

						@endphp
						<td>{{$nombre_empresa}}</td>
						<td>{{$evolucion->historiaclinica->paciente->nombre1}} @if($evolucion->historiaclinica->paciente->nombre2!='(N/A)'){{$evolucion->historiaclinica->paciente->nombre2}}@endif</td>
						<td>{{$evolucion->historiaclinica->paciente->apellido1}} @if($evolucion->historiaclinica->paciente->apellido2!='(N/A)'){{$evolucion->historiaclinica->paciente->apellido2}}@endif</td>
						<td>@if($evolucion->historiaclinica->paciente->sexo=='1') M @elseif($evolucion->historiaclinica->paciente->sexo=='2') F @endif</td>
						<td>{{$age}}</td>
						<td>{{$evolucion->historiaclinica->paciente->id}}</td>
					</tr>
					<tr>
						<td colspan="6"><br></td>
					</tr>
					<tr>
						<td colspan="6" style="text-align: left;"><b>1 CARACTERISTICAS DE LA SOLICITUD Y MOTIVO</b></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table style="font-size: 9px !important;">
				<tbody>
					<tr >
						<td style="width: 10%"><b>ESTABLECIMIENTO DEL DESTINO</b></td>
						<td style="width: 20%">{{$nombre_empresa}}</td>
						<td style="width: 10%"><b>SERVICIO CONSULTADO</b></td>
						<td style="width: 10%">CARDIOLOGÍA</td>
						<td style="width: 10%"><b>SERVICIO QUE SOLICITA</b></td>
						<td style="width: 10%">CARDIOLOGÍA</td>
						<td style="width: 10%"><b>SALA</b></td>
						<td style="width: 5%"></td>
						<td style="width: 10%"><b>CAMA</b></td>
						<td style="width: 5%"></td>
					</tr>

				</tbody>
			</table>
			<table style="font-size: 9px !important;">
				<tbody>
					<tr>
						<td style="width: 5%"><b>NORMAL</b></td>
						<td style="width: 5%">X</td>
						<td style="width: 5%"><b>URGENTE</b></td>
						<td style="width: 5%"></td>
						<td style="width: 10%"><b>MEDICO INTERCONSULTADO</b></td>
						<td style="width: 30%">Dr. Cesar Salazar Bowen</td>
						<td style="width: 10%"><b>DESCRIPCION DEL MOTIVO</b></td>
						<td style="width: 30%">VALORACIÓN PRE-QUIRURGICA</td>
					</tr>
					<tr>
						<td colspan="8" ><br></td>
					</tr>
					<tr>
						<td colspan="8" style="font-size: 12px !important;text-align: left;"><b>2 CUADRO CLINICO ACTUAL</b></td>
					</tr>
					<tr>
						<td colspan="8" style="font-size: 12px !important;height: 200px; "><?php echo $cardio->cuadro_actual; ?></td>
					</tr>
					<tr>
						<td colspan="8" style="font-size: 12px !important;"><br></td>
					</tr>
					<tr>
						<td colspan="8" style="font-size: 12px !important;text-align: left;"><b>3 RESULTADOS DE EXAMENES Y PROCEDIMIENTOS DIAGNOSTICOS</b></td>
					</tr>
					<tr>
						<td colspan="8" style="font-size: 12px !important;height: 200px; "><?php echo $cardio->resultados; ?></td>
					</tr>

				</tbody>
			</table>
			<br>
			<table style="width: 75% !important;">
				<thead >
					<tr>
						<th width="30" style="font-size: 12px">4 DIAGNOSTICO</th>
						<th width="10" style="font-size: 10px" >PRE - PRESUNTIVO DEF - DEFINITIVO</th>
						<th width="10">CIE</th>
						<th width="10">PRE</th>
						<th width="10">DEF</th>
					</tr>
				</thead>
				<tbody>
					@foreach($c10_arr as $val)
					<tr>
						<td colspan="2"  style="text-align: left;">{{$val['descripcion']}}</td>
						<td>{{$val['cie10']}}</td>
						<td>@if($val['pre_def']=='PRESUNTIVO') X @endif</td>
						<td>@if($val['pre_def']=='DEFINITIVO') X @endif</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<br>
			<table style="font-size: 9px !important;">
				<tbody>
					<tr>
						<td style="font-size: 12px !important;text-align: left;"><b>5 PLANES TERAPEUTICOS  Y EDUCACIONALES REALIZADOS</b></td>
					</tr>
					<tr>
						<td style="font-size: 12px !important; height: 100px"><?php echo $cardio->examenes_realizar; ?></td>
					</tr>

				</tbody>
			</table>
			<br>
			<div id="footer">

				<table style="font-size: 9px !important;width: 103.7%;">
					<tbody>
						<tr>
							<td colspan="6"></td>
							<td><b>código</b></td>
							<td colspan="4"></td>
						</tr>
						<tr>
							<td style="width: 5%;"><b>Fecha</b></td>
							<td style="width: 10%;">{{substr($cardio->fecha_formato,0,10)}}</td>
							<td style="width: 5%;"><b>Hora</b></td>
							<td style="width: 10%;">{{substr($cardio->fecha_formato,10,10)}}</td>
							<td style="width: 5%;"><b>Profesional</b></td>
							<td style="width: 20%;">Dr. Cesar Salazar Bowen</td>
							<td style="width: 5%;">3582</td>
							<td style="width: 5%;"><b>Firma</b></td>
							<td style="width: 15%;"></td>
							<td style="width: 10%;"><b>Número de Hoja</b></td>
							<td style="width: 10%;">111-0048</td>
						</tr>
						<tr>
							<td colspan="11"><div style="width: 75%;float: left;text-align: left;">SNS-MSP / HCU-form.007 / 2008</div><div><b>INTERCONSULTA - SOLICITUD</b></div></td>
						</tr>
					</tbody>	
				</table> 
				<img src="{{base_path().'/storage/app/avatars/firma_salazar.png'}}" width="30%" style="float: right;margin-top: -100px;margin-right: 100px;">
				
				
			</div> 
		</div>

			<div style="page-break-after:always;"></div>

		<div id="" style="padding-left: 19px; padding-right: 19px; " >
			<table class="table1">
				<thead >
					<tr>
						<th >ESTABLECIMIENTO SOLICITANTE</th>
						<th >NOMBRE</th>
						<th >APELLIDO</th>
						<th >SEXO (F-M)</th>
						<th >EDAD</th>
						<th >N HISTORIA CLINICA</th>
					</tr>
				</thead>
				<tbody>
					<tr style="font-size: 10px !important;">
						<td>{{$nombre_empresa}}</td>
						<td>{{$evolucion->historiaclinica->paciente->nombre1}} @if($evolucion->historiaclinica->paciente->nombre2!='(N/A)'){{$evolucion->historiaclinica->paciente->nombre2}}@endif</td>
						<td>{{$evolucion->historiaclinica->paciente->apellido1}} @if($evolucion->historiaclinica->paciente->apellido2!='(N/A)'){{$evolucion->historiaclinica->paciente->apellido2}}@endif</td>
						<td>@if($evolucion->historiaclinica->paciente->sexo=='1') M @elseif($evolucion->historiaclinica->paciente->sexo=='2') F @endif</td>
						<td>{{$age}}</td>
						<td>{{$evolucion->historiaclinica->paciente->id}}</td>
					</tr>

				</tbody>
			</table>
			<br>
			<table style="font-size: 9px !important;">
				<tbody>
					<tr>
						<td style="font-size: 12px !important;text-align: left;"><b>6 CUADRO CLINICO DE INTERCONSULTA</b></td>
					</tr>
					<tr>
						<td style="font-size: 12px !important; height: 100px"><?php echo $evolucion->cuadro_clinico; ?></td>
					</tr>

				</tbody>
			</table>
			<br>
			<table style="font-size: 9px !important;">
				<tbody>
					<tr>
						<td style="font-size: 12px !important;text-align: left;"><b>7 RESUMEN DEL CRITERIO CLINICO</b></td>
					</tr>
					<tr>
						<td style="font-size: 10px !important; height: 50px;text-align: left;"><div style="width: 100%;"><?php echo str_replace('<', '&lt;', $cardio->resumen); ?></div></td>
					</tr>

				</tbody>
			</table>
			<br>
			<table style="width: 75% !important;">
				<thead >
					<tr>
						<th width="30">8 DIAGNOSTICO</th>
						<th width="10">PRE - PRESUNTIVO DEF - DEFINITIVO</th>
						<th width="10">CIE</th>
						<th width="10">PRE</th>
						<th width="10">DEF</th>
					</tr>
				</thead>
				<tbody>
					@foreach($c10_arr_2 as $val)
					<tr>
						<td colspan="2" style="text-align: left;">{{$val['descripcion']}}</td>
						<td>{{$val['cie10']}}</td>
						<td>@if($val['pre_def']=='PRESUNTIVO') X @endif</td>
						<td>@if($val['pre_def']=='DEFINITIVO') X @endif</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<br>
			<table style="font-size: 9px !important;">
				<tbody>
					<tr>
						<td style="font-size: 12px !important;text-align: left;"><b>9 PLAN DIAGNOSTICO PROPUESTO	<b></td>
					</tr>
					<tr>
						<td style="font-size: 10px !important; height: 50px;text-align: left;"><div style="width: 100%;"><?php echo $cardio->plan_diagnostico; ?></div></td>
					</tr>

				</tbody>
			</table>
			<br>
			<table style="font-size: 9px !important;">
				<tbody>
					<tr>
						<td style="font-size: 12px !important;text-align: left;"><b>10 PLAN DE TRATAMIENTO PROPUESTO<b></td>
					</tr>
					<tr>
						<td style="font-size: 10px !important; height: 50px;text-align: left;"><div style="width: 100%;"><?php echo $cardio->plan_tratamiento; ?></div></td>
					</tr>
				</tbody>
			</table>
			<div id="footer">
				<table style="font-size: 9px !important;width: 103.7%;">
					<tbody>
						<tr>
							<td colspan="6"></td>
							<td><b>código</b></td>
							<td colspan="4"></td>
						</tr>
						<tr>
							<td style="width: 5%;"><b>Fecha</b></td>
							<td style="width: 10%;">{{substr($cardio->fecha_formato,0,10)}}</td>
							<td style="width: 5%;"><b>Hora</b></td>
							<td style="width: 10%;">{{substr($cardio->fecha_formato,10,10)}}</td>
							<td style="width: 5%;"><b>Profesional</b></td>
							<td style="width: 20%;">Dr. Cesar Salazar Bowen</td>
							<td style="width: 5%;">3582</td>
							<td style="width: 5%;"><b>Firma</b></td>
							<td style="width: 15%;"></td>
							<td style="width: 10%;"><b>Número de Hoja</b></td>
							<td style="width: 10%;">111-0048</td>
						</tr>
						<tr>
							<td colspan="11"><div style="width: 75%;float: left;text-align: left;">SNS-MSP / HCU-form.007 / 2008</div><div><b>INTERCONSULTA - INFORME</b></div></td>
						</tr>
					</tbody>
				</table>
				<img src="{{base_path().'/storage/app/avatars/firma_salazar.png'}}" width="30%" style="float: right;margin-top: -100px;margin-right: 100px;">

			</div>

		</div>
	</body>
</html>