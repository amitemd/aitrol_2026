<!DOCTYPE html>
<html>
<head>
    <title> Formulario 053</title>
    <style>
        @page {
            margin: 15 0;
        }

        body {
            margin-top:    120px;
            margin-left:   30px;
            margin-right:  30px;
            margin-bottom: 1px;
            font-size: 17px;
        }


        header {
            position: fixed;
            top:        0cm;
            left:       0cm;
            right:      0cm;
            height:     1cm;
        }

        footer {
            position: fixed; 
            bottom:     0cm; 
            left:       0cm; 
            right:      0cm;
            height:     2cm;
        }
        td {
          border: 1px solid black;
        }
        table {
          border-collapse: collapse;
        } 
        .table2d{
            border: none;
            text-align: center;
        }
        .titulo{
            background-color: #C7C3C2;
        }
        p{
        	padding-top: 0;padding-bottom: 0;margin: 0;
        }
        div{
        	padding-top: 0;padding-bottom: 0;
        }
    </style>
</head>
<body>
    <header>
    	<div style="position: relative;">
	        <div style="text-align: center;">
	            <p>MINISTERIO DE SALUD PUBLICA</p>
	            <p>FORMULARIO DE REFERENCIA, DERIVACIÓN, CONTRAREFERENCIA Y REFERENCIA INVERSA   </p>
	        </div> 
	        <div style="text-align: center;position: absolute;top: 0;left: 5%;">
	            <img src="{{base_path().'/storage/app/logo/imagen002.png'}}" width="10%">
	        </div>
	        <div style="text-align: center;position: absolute;top: 0;left: 85%;">
	            <img src="{{base_path().'/storage/app/logo/iess_logo.png'}}" width="60%">
	        </div>
	    </div>     
    </header>
    <main>
	    <p>I. DATOS DEL USUARIO/USUARIA</p>
	    <table class="table" style="width: 100%">
	        <tbody>
	            <tr>
	                <td colspan="3"><b>APELLIDO PATERNO</b></td>
	                <td colspan="3"><b>APELLIDO MATERNO</b></td>
	                <td colspan="3"><b>NOMBRES</b></td>
	                <td colspan="3"><b>FECHA DE NACIMIENTO</b></td>
	                <td ><b>EDAD</b></td>
	                <td ><b>SEXO</b></td>
	            </tr>
	            <tr>
						
	                <td colspan="3" rowspan="2" style="text-align: center;"> {{$paciente->apellido1}} </td>
	                <td colspan="3" rowspan="2" style="text-align: center;">  {{$paciente->apellido2}}  </td>
	                <td colspan="3" rowspan="2" style="text-align: center;"> {{$paciente->nombre1}}  {{$paciente->nombre2}}  </td>
	                <td style="text-align: center;"> {{date('d', strtotime($paciente->fecha_nacimiento))}} </td>
	                <td style="text-align: center;"> {{date('m', strtotime($paciente->fecha_nacimiento))}} </td>
	                <td style="text-align: center;"> {{date('Y', strtotime($paciente->fecha_nacimiento))}} </td>
	                <td style="text-align: center;"> {{$edad}} </td>
	      

	                <td style="text-align: center;"> {{$paciente->sexo}} </td>
				</tr>
	            <tr>
	                <td >día</td>
	                <td >mes</td>
	                <td >año</td>
	                <td >d-m-a</td>
	                <td >1=H/2=M</td>
	            </tr>
	            <tr>
	                <td ><b>Nacionalidad</b></td>
	                <td ><b>Pais</b></td>
	                <td colspan="3"><b>Cédula de<br>Ciudadanía o<br>Pasaporte</b></td>
	                <td colspan="3"><b>Lugar de residencia actual</b></td>
	                <td colspan="4"><b>Direccion Domicilio</b></td>
	                <td colspan="2"><b>N° Telefónico</b></td>
	            </tr>
	            

	            <tr>
	                <td style="text-align: center;"> {{$paciente->lugar_nacimiento}} </td>
	                <td style="text-align: center;"> {{$pais->nombre}} </td>
	                <td colspan="3" style="text-align: center;"> {{$paciente->id}}</td>
	                <td style="text-align: center;"> {{$paciente->ciudad}} </td>
	                <td style="text-align: center;"> </td>
	                <td style="text-align: center;"> </td>
	                <td colspan="4" style="text-align: center;"> {{$paciente->direccion}} </td>
	                <td colspan="2"  style="text-align: center;"> {{$paciente->telefono1}} - {{$paciente->telefono2}}</td>
	            </tr>
	            <tr>
	                <td >Ver Instructivo</td>
	                <td >Describir País</td>
	                <td colspan="3">Cédula diez dígitos</td>
	                <td >Provincia</td>
	                <td >Cantón</td>
	                <td >Parroquia</td>
	                <td colspan="4">Calle Principal y Secundaria</td>
	                <td colspan="2">Convencional/Celular</td>
	            </tr>
	        </tbody>
	    </table>
	    <table class="table" style="width: 50%">
	        <tbody>
	            <tr>
	                <td ><b>REFERENCIA</b></td>
	                <td ><b>1</b></td>
	                <td >&nbsp;</td>
	                <td ><b>DERIVACIÓN</b></td>
	                <td ><b>2</b></td>
	                <td ><b>X</b></td>
	            </tr>
	        </tbody>
	    </table>
	    <p>1. Datos Institucionales</p>
	    <table class="table" style="width: 100%">
	        <tbody>
	            <tr>
	                <td colspan="2"><b>Entidad del Sistema</b></td>
	                <td ><b>Hist. Clínica No.</b></td>
	                <td colspan="5"><b>Establecimiento de Salud</b></td>
	                <td ><b>Tipo</b></td>
	                <td colspan="3"><b>Distrito/Área</b></td>
	            </tr>
	            <tr>
	                <td colspan="2"><b>IESS</b></td>
	                <td style="text-align: center;">{{$form_053->id_paciente}} </td>
	                <td colspan="5">{{$form_053->empresa->razonsocial}} </td>
	                <td ><b>II</b></td>
	                <td colspan="3"> {{$form_053->distrito}} </td>
	            </tr>
	            <tr>
	                <td colspan="9"><b> Refiere  o Deriva a:</b></td>
	                <td colspan="3"><b>Fecha</b></td>
	            </tr>
	            <tr>
	                <td colspan="3"><b>IESS</b></td>
	                <td colspan="2"><b>{{$form_053->establecimiento_salud}} </b></td>
	                <td colspan="2"><b>{{$form_053->servicio}} </b></td>
	                <td colspan="2"><b>{{$form_053->especialidad}}  </b></td>

	                <td > {{date('d', strtotime($form_053->fecha))}}  </td>
	                <td > {{date('m', strtotime($form_053->fecha))}} </td>
	                <td > {{date('Y', strtotime($form_053->fecha))}}  </td>
	            </tr>
	            <tr>
	                <td colspan="3"><b>Entidad del sistema</b></td>
	                <td colspan="2"><b>Establecimiento de Salud</b></td>
	                <td colspan="2"><b>Servicio</b></td>
	                <td colspan="2"><b>Especialidad</b></td>
	                <td ><b>día</b></td>
	                <td ><b>mes</b></td>
	                <td ><b>año</b></td>
	            </tr>
	        </tbody>
	    </table>
	    <p>2.Motivo de la Referencia o Derivación</p>
		<table class="table" style="width: 100%">
	        <tbody>
	            <tr>
	                <td ><b>Limitada  capacidad  resolutiva</b></td>
	                <td ><b>1</b></td>
	                <td > @php 
						$x =[];
						if ($form_053->limitada_capacidad == 1){
							$x = 'X';
						}
						elseif(!is_null($form_053->limitada_capacidad == null or $form_053->limitada_capacidad == 0)){
							$x = '';
						}
						@endphp 
						<b>&nbsp;
				        {{$x}}
						</b>	
						
					</td>
					
	                <td ><b>Saturación de capacidad  instalada</b></td>
	                <td ><b>4</b></td>
	                <td>  @php 
					    $x ="";
						if ($form_053->saturacion_capacidad == 1){
							$x = 'X';
						}
						@endphp 
						<b>&nbsp;{{$x}}</b></td>
	            </tr>
	            <tr>
	                <td ><b>Ausencia temporal del profesional</b></td>
	                <td ><b>2</b></td>
	                <td > @php 
					    $x ="";
						if ($form_053->ausencia_temporal == 1){
							$x = 'X';
						}
						@endphp 
						<b>&nbsp;{{$x}}</b></td>
	                <td ><b>Otros /Especifique:</b></td>
	                <td ><b>5</b></td>
	                <td > @php 
					    $x ="";
						if ($form_053->otros == 1){
							$x = 'X';
						}
						@endphp 
						<b>&nbsp;{{$x}}</b></td>
	            </tr>
	            <tr>
	                <td ><b>Falta de profesional</b></td>
	                <td ><b>3</b></td>
	                <td > @php 
					    $x ="";
						if ($form_053->falta_profesional == 1){
							$x = 'X';
						}
						@endphp 
						<b>&nbsp;{{$x}}</b></td>
	                <td colspan="3">{{$form_053->texto_otros}}</td>
	            </tr>
	        </tbody>
	    </table>
	    <p>3. Resumen del cuadro clínico  </p>

	    <div style="border: 1px solid black;">  <?php echo $id_orden_12->cuadro_clinico ?> </div>
	    <p>4. Hallazgos relevantes de exámenes y procedimientos diagnósticos</p>
	    <div style="border: 1px solid black;">&nbsp;</div>

	    <table class="table" style="width: 100%">

	        <tbody>
	
	            <tr>
	                <td colspan="5"><b>5. Diagnostico</b></td>

					
	                <td ><b>CIE-10</b></td>
	                <td ><b>PRE</b></td>
	                <td ><b>DEF</b></td>
	

	            </tr>
                @foreach($diagnostico as $value)
	            <tr>
	                <td >@if(!is_null($value)){{$value->cie10}} @endif</td>
	                <td colspan="4"></td>
	                <td > @if(!is_null($value)){{$value->cie10}} @endif </td>
	                
	                <td > @if(!is_null($value)){{$value->presuntivo_definitivo}} @endif </td>
	                <td > @if(!is_null($value)){{$value->presuntivo_definitivo}} @endif   </td>
	            </tr>
                @endforeach
	        </tbody>
	    </table>
	    <table class="table" style="width: 100%">
	        <tbody>
	            <tr>
				
	                <td colspan="5"><b>6. Exámenes/ procedimientos requeridos</b></td>
	                <td colspan="3"><b>Código Tarifario</b></td>
	            </tr>
	         
	            <tr>
	                <td colspan="5">  </td>
	                <td colspan="3"> </td>
	            </tr>
	            
	        </tbody>
	    </table>
	    <table class="table" style="width: 100%">
	        <tbody>
	            <tr>
	                <td ><b>Nombre  del profesional:</b></td>
	                <td > {{$form_053->usuario->nombre1}} {{$form_053->usuario->apellido1}} </td>
	                <td ><b>Código MSP: </b></td>
	                <td > {{$form_053->id_doctor}} </td>
	                <td ><b>Firma</b></td>
					@php $firma     = Sis_medico\Firma_Usuario::where('id_usuario', $form_053->id_doctor)->first(); @endphp
					<td style="text-align: center;position: absolute;top: 0; width:50%; left: 40%;">@if(!is_null($firma))<img width=180 height=130 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" ><br></td>
					@endif
	            </tr>
	        </tbody>
	    </table>
	    <table class="table" style="width: 50%">
	        <tbody>
	            <tr>
	                <td ><b>III. CONTRAREFERENCIA</b></td>
	                <td ><b>3</b></td>
	                <td style="height: 50px; border: 1px solid black;" ><b>&nbsp;&nbsp;</b></td>
	                <td ><b>REFERENCIA INVERSA</b></td>
	                <td ><b>4</b></td>
	                <td  style="height: 50px; border: 1px solid black;">&nbsp;&nbsp;</td>
	            </tr>
	        </tbody>
	    </table>
	    <p>1. Datos Institucionales</p>
	    <table class="table" style="width: 100%">
	        <tbody>
	            <tr>
	                <td colspan="2"><b>Entidad  del sistema</b></td>
	                <td colspan="2"><b>Hist. Clínica No.</b></td>
	                <td colspan="3"><b>Establecimiento de Salud</b></td>
	                <td ><b>Tipo</b></td>
	                <td ><b>Servicio</b></td>
	                <td colspan="4"><b>Especialidad de Servicio</b></td>
	            </tr>
	            <tr>
	                <td colspan="2" style="height: 30px;"></td>
	                <td colspan="2" style="height: 30px;"></td>
	                <td colspan="3" style="height: 30px;"></td>
	                <td style="height: 30px;"></td>
	                <td style="height: 30px;"></td>
	                <td colspan="4" style="height: 30px;"></td>
	            </tr>
	            <tr>
	                <td colspan="9">Contrarefiere o  Referencia inversa  a: </td>
	                <td colspan="4">Fecha</td>
	            </tr>
	            <tr>
	                <td colspan="4" style="height: 30px; "></td>
	                <td colspan="2" style="height: 30px; "></td>
	                <td colspan="2" style="height: 30px; "></td>
	                <td colspan="2" style="height: 30px; "></td>
	                <td ></td>
	                <td ></td>
	                <td ></td>
	            </tr>
	            <tr>
	                <td colspan="4"><b>Entidad del sistema</b></td>
	                <td colspan="2"><b>Establecimiento de Salud</b></td>
	                <td colspan="2"><b>Tipo</b></td>
	                <td colspan="2"><b>Distrito/Área</b></td>
	                <td ></td>
	                <td ></td>
	                <td ></td>
	            </tr>
	        </tbody>
	    </table>
	    <p>2. Resumen del cuadro clínico</p>
	    <div style="height: 65px; border: 1px solid black;"><br></div>
	    <p>3. Hallazgos relevantes de exámenes y procedimientos diagnósticos</p>
	    <div style="height: 65px; border: 1px solid black;"><br></div>
	    <p>4. Tratamientos y procedimientos terapéuticos realizados</p>
	    <div style="height: 65px; border: 1px solid black;"><br></div>
	    <table class="table" style="width: 100%">
	        <tbody>
	            <tr>
	                <td colspan="5"><b>5. Diagnostico</b></td>
	                <td ><b>CIE-10</b></td>
	                <td ><b>PRE</b></td>
	                <td ><b>DEF</b></td>
	            </tr>
	            <tr>
	                <td ></td>
	                <td colspan="4"></td>
	                <td style="height: 30px; border: 1px solid black;"></td>
	                <td style="height: 30px; border: 1px solid black;"></td>
	                <td style="height: 30px; border: 1px solid black;"></td>
	            </tr>
	        </tbody>
	    </table>
	    <p>6. Tratamiento recomendado a seguir en el establecimiento de salud de menor nivel de atención y/o de complejidad</p>
	    <div style="height: 65px; border: 1px solid black;"></div>
	    <table class="table" style="width: 100%">
	        <tbody>
	            <tr>
	                <td ><b>Nombre  del profesional:  </b></td>
	                <td >  </td>
	                <td ><b>Código MSP: </b></td>
	                <td ></td>
	                <td ><b>Firma</b></td>
	                <td ></td>
	            </tr>
	        </tbody>
	    </table>
		<table class="table" style="width: 100%">
	        <tbody>
	            <tr>
	                <td >MSP/DNISCG/form. 053/dic/2013</td>
	                <td >7. Referencia</td>
	                <td >&nbsp;</td>
	                <td ></td>
	            </tr>
	        </tbody>
	    </table>
	</main>    
</body>
</html>