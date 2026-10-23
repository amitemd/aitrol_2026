<!DOCTYPE html>
<html>
	<head>
	  
	    <title>ORDEN 012 {{$paciente->id_paciente}}</title>

	  	<style>	
	  		@page { margin: 0px 20px;}
		    #header { position: fixed; left: 20px; top: -10px; right: 20px; height: 150px; text-align: center;}
		    #content { position: fixed; left: 20px; top: 40px; right: 20px; height: 150px; }
		    #footer { position: fixed; left: 20px; bottom: -100px; right: 20px; height: 150px; }
		    p {
    			font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 14px;
    			text-align:justify;
    			font-style: italic;
			}
			.table, .td{
				font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 9px;
    			text-align:center;
    			font-style: italic;
    			border-collapse: collapse;
    			border: solid 1px black;
    		
    			
			}
			.table, .th{
				font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 7px;
    			text-align:center;
    			font-style: italic;
    			border-collapse: collapse;
    			border: solid 1px black;

    			
			}

			.t2, .t2 th {

				font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 7px !important;
    			text-align:center;
    			font-style: italic;
    			
    			border-collapse: collapse;
    			border: solid 1px black;

    			
    					

			}

			.t2, .t2 td {

				font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 8px !important;
    			text-align:center;
    			font-style: italic;
    			
    			border-collapse: collapse;
    			border: solid 1px black;
    					

			}

			.t3,.t3 th, .t3 td {

				font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 9px !important;
    			text-align:justify;
    			font-style: italic;
    			background-color: green;
    			border-collapse: collapse;
    			border: solid 1px black;
    					

			}
			.t3{
					
				float: left;
			}

			.compar{
				font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 12px;
    			text-align:justify;
    			font-style: italic;
    			border-collapse: collapse;
    			border: solid 1px black;
    			width: 49.7%;
    			float: left;
			}

			.compar2{
				font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 12px;
    			text-align:justify;
    			font-style: italic;
    			border: solid 1px black;
    			width: 49.7%;
    			float: left;
    			height: 10%;
			}


			p {

				font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 12px !important;
    			text-align:justify;
    			font-style: italic;	
    				

			}

			.subcla {

				font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 9px !important;
    			text-align: justify;
    			font-style: italic;	
    			border-top: solid 1px black;
    			border-right: solid 1px black;
    			float: left;	

			}

			.subcla2 {

				font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 9px !important;
    			text-align:justify;
    			font-style: italic;	
    			border-top: solid 1px black;
				border-bottom: solid 1px black;
    			float: left;	

			}	

			.lmp{
				clear: both;
				/*border-bottom: solid 1px red;*/

			}

			#contenedor {
				
				
				
				
			}

			#tabla1 {
				float: left;
				width: 49.5%;
				border: solid 1px black;
				
			}

			#tabla1 table {
				text-align: center;
			}

			#tabla1 table {
				
				border-collapse: collapse;
			}

			#tabla2 {
				float: left;
				width: 50%;
				

				
			}

			#tabla3 {
				
				
						
			}
			#tabla3 table {
				
				font-size: 9px;
				border-collapse: collapse;
				
						
			}

			#tabla2 table {
				text-align: center;
				
				
			}

			#tabla2 table {
				
				font-size: 10px;
			}

			#tabla2 table th {
				font-size: 8px;
			}			


			
			
		</style>
	</head>
	<body >	  
		

		<div id="content" > 	
			<table class="table">
				<tr>
					<th class="th" >INSTITUCION DEL SISTEMA</th>
					<th class="th" >UNIDAD OPERATIVA</th>
					<th class="th" >COD UO</th>
					<th class="th"  colspan="3">COD LOCALIZACION</th>
					<th class="th" >HISTORIA CLINICA</th>
				</tr>
				<tr>
					<td style="width: 120px;" class="td" rowspan="2">{{$seguro->nombre}}</td>
					<td style="width: 150px;" class="td" rowspan="2">{{$empresa->nombrecomercial}}</td>
					<td style="width: 90px;" class="td" rowspan="2">&nbsp;</td>
					<td style="width: 75px;" class="td">PARROQUIA</td>
					<td style="width: 75px;" class="td" >CANTON</td>
					<td style="width: 75px;" class="td">PROVINCIA</td>
					<td style="width: 130px;" class="td" rowspan="2">{{substr($paciente->id,5,5)}}</td>
				</tr>
				<tr>
					<td class="td">TARQUI</td>
					<td class="td" >GUAYAQUIL</td>
					<td class="td">GUAYAS</td>	
				</tr>
			</table>
			<table class="table">
				<tr>
					<th class="th">APELLIDO PATERNO</th>
					<th class="th">APELLIDO MATERNO</th>
					<th class="th">PRIMER NOMBRE</th>
					<th class="th">SEGUNDO NOMBRE</th>
					<th class="th">EDAD</th>
					<th class="th">CEDULA CIUDADANIA</th>
				</tr>
				<tr>
					<td style="width: 120px;" class="td">{{$paciente->apellido1}}</td>
					<td style="width: 120px;" class="td">@if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif</td>
					<td style="width: 120px;" class="td">{{$paciente->nombre1}}</td>
					<td style="width: 118px;" class="td">@if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif</td>
					<td style="width: 120px;" class="td">{{$age}}</td>
					<td style="width: 120px;" class="td">{{$paciente->id}}</td>
				</tr>
			</table>
			<table class="t2">
				<tr>
					<th >PERSONA QUE REFIERE</th>
					<th >PROFESIONAL SOLICITANTE</th>
					<th >SERVICIO</th>
					<th >SALA</th>
					<th >CAMA</th>
					<th colspan="6">PRIORIDAD</th>
					<th >FECHA DE TOMA</th>
				</tr>
				<tr>
					<td style="width: 100px;">&nbsp;</td>
					<td style="width: 140px;">Dr(a). {{$doctor->apellido1}} {{$doctor->nombre1}}</td>
					<td style="width: 130px;">GASTROENTEROLOGIA</td>
					<td style="width: 40px;">&nbsp;</td>
					<td style="width: 40px;">&nbsp;</td>
					<td style="width: 40px;">URGENTE</td>
					<td style="width: 20px;">&nbsp;</td>
					<td style="width: 35px;">RUTINA</td>
					<td style="width: 20px;">X</td>
					<td style="width: 45px;">CONTROL</td>
					<td style="width: 20px;">&nbsp;</td>
					<td style="width: 70px;">&nbsp;</td>
				</tr>
				
			</table>
			<p><b>1. ESTUDIO SOLICITADO</b></p>
			<table class="t2">
				<tr>
					<td style="width: 150px;">RX CONVENCIONAL</td>
					<td style="width: 30px;">&nbsp;</td>
					<td style="width: 50px;">TOMOGRAFIA</td>
					<td style="width: 30px;">&nbsp;</td>
					<td style="width: 50px;">RESONANCIA</td>
					<td style="width: 30px;">&nbsp;</td>
					<td style="width: 50px;">ECOGRAFIA</td>
					<td style="width: 30px;">&nbsp;</td>
					<td style="width: 50px;">PROCEDIMIENTO</td>
					<td style="width: 30px;">&nbsp;</td>
					<td style="width: 50px;">OTROS</td>
					<td style="width: 30px;">X</td>
					<td style="width: 92px;">&nbsp;</td>
				</tr>
				<tr>
					<td>DESCRIPCION</td>
					<td></td>
					<td colspan="11"></td>
				</tr>
				
			</table>
			<table class="t2">
				<tr>
					<td style="width: 81px;">PUEDE MOVILIZARSE</td>
					<td style="width: 12px;"><b>X</b></td>
					<td style="width: 100px;">PUEDE RETIRARSE VENDAS, APOSITOS O YESOS</td>
					<td style="width: 30px;">&nbsp;</td>
					<td style="width: 100px;">EL MEDICO ESTARÁ PRESENTE EN EL EXAMEN</td>
					<td style="width: 30px;">&nbsp;</td>
					<td style="width: 130px;">TOMA DE RADIOGRAFIA EN LA CAMA</td>
					<td style="width: 30px;">&nbsp;</td>
				</tr>
				<tr>
					<td>DESCRIPCION</td>
					<td></td>
					<td colspan="6" style="text-align: left;">{{$orden_012->descripcion}}</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan="6">&nbsp;</td>
				</tr>
				
			</table>
			<p><b>2. MOTIVO DE LA SOLICITUD</b></p>
			<table class="t2">
				<tr>
					<td style="width: 734px;text-align: left;">
						<br><br>
						&nbsp;<?php echo strip_tags($orden_012->motivo); ?>
						<br><br><br>
					</td>
				</tr>
			</table>
			<br>
			<div class="compar">
				<b>3. RESUMEN CLINICO</b>		
			</div>
			<div class="compar">
				<b>4. DIAGNOSTICOS</b>		
			</div>
			<div style="clear:both;"></div>	
			
			
			<div style="clear:both;"></div>	
			<div id="contenedor">
				<div id="tabla1">
					<table>
						<tr>
							<td style="text-align: left;font-size: 9px;">
								<br><br>
									&nbsp;<?php echo strip_tags($orden_012->cuadro_clinico); ?>
								<br><br><br>
							</td>
						</tr>

					</table>
				</div>
				<div id="tabla2">
					<table border="1">
						<tr>
							<th style="width: 50%;">CIE=CLASIFICACION INTERNACIONAL DE ENFERMEDADES PRE:PRESUNTIVO DEF:DEFINITIVO</td>
							<th style="width: 10%;">CIE</td>
							<th style="width: 5%;">PRE</td>
							<th style="width: 5%;">DEF</td>
						</tr>
						@php $cont = 0; @endphp
						@foreach($cie10 as $val)
						@php
							$c10 = Sis_medico\Cie_10_3::find($val->cie10);
                            if(is_null($c10)){
                                $c10 = Sis_medico\Cie_10_4::find($val->cie10);
                            }
                            $cont ++;
						@endphp
							<tr>
								<td style="text-align: left;">{{$cont}}.- {{$c10->descripcion}}</td>
								<td >{{$val->cie10}}</td>
								<td >@if($val->presuntivo_definitivo=='PRESUNTIVO') X @endif</td>
								<td >@if($val->presuntivo_definitivo=='DEFINITIVO') X @endif</td>
							</tr>
						@endforeach
					</table>
					
						
				</div>

			</div>
			<br>
			<br>
			<div style="clear:both;"></div>	
			<br>
				<div id="tabla3" style="z-index: 999;">
					<table border="1">
						<tr>
							<td style="width: 30px !important;"> Fecha</td>
							<td style="width: 45px !important;"> {{substr($orden_012->fecha_orden,0,10)}}</td>
							<td style="width: 10px !important;"> hora</td>
							<td style="width: 10px !important;">&nbsp;&nbsp;</td>
							<td style="width: 100px !important;"> NOMBRE DEL PROFESIONAL</td>
							<td style="width: 150px !important;"> Dr(a). {{$doctor->apellido1}} {{$doctor->nombre1}} </td>
							<td style="width: 45px !important;"> CODIGO</td>
							<td style="width: 45px !important;"> 16203</td>
							<td style="width: 45px !important;"> FIRMA</td>
							<td style="width: 90px !important;">&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td style="width: 45px !important;"> NÚMERO DE HOJA</td>
							<td style="width: 10px !important;">&nbsp;</td>	
						</tr>
					</table>

				</div>
				<div style="font-size: 8px;width: 50%;float: left;">SNS-MSP / HCU-form.0.12a/2008</div>
				<div style="font-size: 8px;text-align: right;width: 48%;float: left;"><b>IMAGENOLOGIA - SOLICITUD</b></div>
				<div style="clear:both;"></div>	
				@php
			        $nom = "";
			        if(!is_null($firma)){
			          $nom = "/storage/app/avatars/$firma->nombre";
			        }
			      @endphp

			    
			    @if($nom != "")
			    
			      <p class=MsoNormal align=center style='margin-bottom:0;margin-bottom:0;text-align:center;line-height:normal'><span style='font-size:6.0pt;mso-bidi-font-size:11.0pt'><o:p><img style='position: absolute;top: -50px;margin-left: 350pt;padding-left: 0px;' width=200 height=75 src="{{base_path().$nom}}" align=center hspace=12></o:p></span>
			      </p>
			    
			    @endif
			
				
		</div>	

		<div id="footer">
			
		</div> 	
	</body>
</html>		