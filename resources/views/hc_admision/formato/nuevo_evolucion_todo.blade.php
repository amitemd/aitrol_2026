<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
	<title> Evolución </title>
    <style>
        table,
        td,
        th {
            border: 1px solid;
        }

        table {
            width: 100%;
            height: 50px;
            border: 1.5px solid;
            border-collapse: collapse;
            white-space: normal;

        }

        @page {
            margin: 10px 10px;

        }

        /** Define now the real margins of every page in the PDF **/
        body {}

        header {}

        table {}

        .titulo {}

        span {}

        p {
            padding: 0;
            margin: 0;
        }

        footer {

            /*background-image: url(""); 
	    background-size: 50%; */
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1cm;

        }

        .table1 {
            font-size: 13px;
            text-align: center;
            width: 100%;
        }

        .table2 {
            font-size: 13px;
            text-align: center;
            word-wrap: break-word;
            width: 98%;
            padding: 0;
        }

        .table3 {
            font-size: 13px;
            text-align: center;
        }

        .table4 {
            font-size: 13px;
            text-align: center;
        }

        .table5 {
            font-size: 13px;
            text-align: center;
        }

        .table6 {
            font-size: 13px;
            text-align: center;
        }

        .table7 {
            font-size: 10px;
            text-align: center;
        }

        .table8 {
            font-size: 10px;
            text-align: center;
        }

        .table9 {
            font-size: 10px;
            text-align: center;
            word-wrap: break-word;
            width: 100%;

        }

        .table10 {
            font-size: 10px;
            text-align: center;
        }

        .texto-vertical-3 {
            width: 50px;
            word-wrap: break-word;
            text-align: center;
        }

        .texto-vertical-2 {
            writing-mode: vertical-lr;
            transform: rotate(-90deg);
        }
    </style>
</head>
<?php
$paciente = $historiaclinica->paciente;
$fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');
$agenda = $historiaclinica->agenda;
?>

<body>
    <main>
        <div class="box-body card" style="text-align: center; padding:0; margin:0;">
			@if(!is_null($agenda->empresa))
				<img style="width:150px;height: 50px" src="{{base_path().'/storage/app/logo/'.$agenda->empresa->logo}}" >
            @endif
        </div>
        <table class="table9">
            <tr>
                <th bgcolor="#B4A7FD" ; colspan="18" align="left" style="font-size:15px"><b>A. DATOS DEL ESTABLECIMIENTO USUARIO / PACIENTE </b> </th>
            </tr>

            <tr>
                <td colspan="3" bgcolor="#BBFDAC" align="center" height="10" style="font-size:10px"> <b> INSTITUCIÓN DEL SISTEMA </b></td>
                <td colspan="2" bgcolor="#BBFDAC" align="center" height="10" style="font-size:10px"> <b>UNICODIGO</b></td>
                <td colspan="5" bgcolor="#BBFDAC" align="center" height="10" style="font-size:10px"><b> ESTABLECIMIENTO DE SALUD</b></td>
                <td colspan="2" bgcolor="#BBFDAC" align="center" height="10" style="font-size:10px"><b> NUMERO DE HISTORIA CLINICA </b></td>
                <td colspan="3" bgcolor="#BBFDAC" align="center" height="10" style="font-size:10px"><b> NUMERO DE ARCHIVO</b></td>
                <td colspan="3" bgcolor="#BBFDAC" align="center" height="10" style="font-size:10px"> <b>No.HOJA</b></td>
            </tr>
            
            <tr>
                <td colspan="3" height="15">{{$historiaclinica->seguro->nombre}}</td>
                <td colspan="2">@if(isset($agenda->empresa)){{$agenda->empresa->unicodigo}}@endif</td>
                <td colspan="5">{{$agenda->empresa->nombrecomercial}}</td>
                <td colspan="2">{{$paciente->id}} </td>
                <td colspan="3"></td>
                <td colspan="3"></td>
            </tr>

            <tr>
                <td rowspan="2" colspan="3" bgcolor="#BBFDAC" align="center" height="8" style="font-size:10px"><b>PRIMER APELLIDO</b></td>
                <td rowspan="2" colspan="3" bgcolor="#BBFDAC" align="center" height="8" style="font-size:10px"><b> SEGUNDO APELLIDO</b></td>
                <td rowspan="2" colspan="3" bgcolor="#BBFDAC" align="center" height="8" style="font-size:10px"><b>PRIMER NOMBRE</b> </td>
                <td rowspan="2" colspan="3" bgcolor="#BBFDAC" align="center" height="8" style="font-size:10px"> <b>SEGUNDO NOMBRE </b></td>
                <td rowspan="2" colspan="1" bgcolor="#BBFDAC" align="center" height="8" style="font-size:10px"> <b> SEXO </b></td>
                <td rowspan="2" colspan="1" bgcolor="#BBFDAC" align="center" height="8" style="font-size:10px"> <b>EDAD </b></td>
                <td colspan="4" bgcolor="#BBFDAC" align="center" height="8" style="font-size:10px"> <b> CONDICIÓN EDAD (MARCAR) </b></td>
            </tr>
            <tr>
                <td colspan="1"><b>H</b></td>
                <td colspan="1"><b>D</b></td>
                <td colspan="1"><b>M</b></td>
                <td colspan="1"><b>A</b></td>
            </tr>
            <tr>
                <td height="20" colspan="3">{{$paciente->apellido1}}</td>
                <td colspan="3">{{$paciente->apellido2}}</td>
                <td colspan="3">{{$paciente->nombre1}}</td>
                <td colspan="3">{{$paciente->nombre2}}</td>
                <td colspan="1">@if($paciente->sexo == 1) Maculino @else Femenino @endif</td>
                <td colspan="1">{{$edad}}</td>
                <td colspan="1"></td>
                <td colspan="1">{{$diaNacimiento}}</td>
                <td colspan="1">{{$mesNacimiento}}</td>
                <td colspan="1">{{ date('Y', strtotime($historiaclinica->paciente->fecha_nacimiento))}}</td>
            </tr>



        </table>

        <table class="table2">
            <tr>
                <th bgcolor="#B4A7FD" ; colspan="3" align="left" style="border: #B4A7FD; border-bottom: outset; border-top: outset; font-size:15px;">B. EVOLUCIÓN Y PRESCRIPCIONES </th>
                <th bgcolor="#B4A7FD" ; colspan="1" align="center" style="border: #B4A7FD; border-bottom: outset; border-top: outset; font-size:5px;">FIRMAR AL PIE DE CADA EVOLUCIÓN Y PREESCRIPCIÓN </th>
                <th bgcolor="#B4A7FD" ; colspan="1" align="right" style="border: #B4A7FD; border-bottom: outset; border-top: outset; font-size:5px;">REGISTRAR CON ROJO LA ADMINISTRACIÓN DE FARMACOS Y COLOCACIÓN DE DISPOSITIVOS MÉDICOS</th>
            </tr>
            <tr>
                <th bgcolor="#B4A7FD" colspan="3" align="center" style="font-size:15px;">1. EVOLUCIÓN </th>
                <th bgcolor="#B4A7FD" colspan="2" align="center" style="font-size:15px;">2. PRESCRIPCIONES</th>
            </tr>
            <tr>
                <td color="#BBFDAC" align="center" class="texto-vertical-3" height="5" style="font-size:8px"><b> FECHA (aaaa- mm -dd)</b></td>
                <td color="#BBFDAC" align="center" class="texto-vertical-3" style="font-size:8px"> <b> HORA (hh:mm) </b> </td>
                <td gcolor="#BBFDAC" align="center" style="font-size:10px"> <b> NOTAS DE EVOLUCIÓN </b></td>
                <td gcolor="#BBFDAC" align="center" style="font-size:10px"> <b> FARMACOTERAPIA E INDICACIONES(Para enfermería y otro profesional de salud) </b> </td>
                <td color="#BBFDAC" align="center" class="texto-vertical-3" style="font-size: 7px"> <b> ADMINISTR. FARMACOS DISPOSITIVOS </b> </td>
            </tr>
            @foreach($evoluciones as $evolucion)
            @php 
                $receta = Sis_medico\hc_receta::where('id_hc',$evolucion->hcid)->first(); 
                
            @endphp
            <tr>
                <td style="font-size:10px">@if($evolucion->fecha_doctor != null) {{date("d/m/Y", strtotime($evolucion->fecha_doctor))}} @else {{date("d/m/Y", strtotime($evolucion->created_at))}} @endif</td>
                <td style="font-size:10px">@if($evolucion->fecha_doctor != null) {{date("H:i", strtotime($evolucion->fecha_doctor))}} @else {{date("H:i", strtotime($evolucion->created_at))}} @endif</td>
                <td style="font-size:10px !important;"><?php echo $evolucion->cuadro_clinico ?>  <?php echo $evolucion->laboratorio ?> </td>
                <td style="font-size:10px"> @foreach($receta->detalles as $detalle) {{$detalle->nombre}} <b>cantidad:</b>{{$detalle->cantidad}} <b>dosis:</b>{{$detalle->dosis}} <br> @endforeach</td>
                <td style="font-size:10px">&nbsp; </td>
            </tr>
            @endforeach

		</table>
        <br>
        
        <footer>
            <p style="font-size: 12px"><b>SNS-MSP / HCU_form.005/2021&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;EVOLUCIÓN Y PRESCRIPCIONES(1)</b></p>
        </footer>

    </main>
</body>

</html>
