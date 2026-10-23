<!DOCTYPE html>
<html>

<head>
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

$fechaNacimiento = new DateTime($procedimiento->historia->paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($procedimiento->historia->paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($procedimiento->historia->paciente->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');

?>

<body>
    <main>
        <div class="box-body card" style="text-align: center; padding:0; margin:0;">
			@if(!is_null($procedimiento->empresa))
				<img style="width:150px;height: 50px" src="{{base_path().'/storage/app/logo/'.$procedimiento->empresa->logo}}" >
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
                <td colspan="3" height="15">{{$procedimiento->historia->seguro->nombre}}</td>
                <td colspan="2">{{$procedimiento->empresa->unicodigo}}</td>
                <td colspan="5">@if(isset($procedimiento->empresa)){{$procedimiento->empresa->nombrecomercial}}@endif</td>
                <td colspan="2">{{$procedimiento->historia->paciente->id}} </td>
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
                <td height="20" colspan="3">{{$procedimiento->historia->paciente->apellido1}}</td>
                <td colspan="3">{{$procedimiento->historia->paciente->apellido2}}</td>
                <td colspan="3">{{$procedimiento->historia->paciente->nombre1}}</td>
                <td colspan="3">{{$procedimiento->historia->paciente->nombre2}}</td>
                <td colspan="1">@if($procedimiento->historia->paciente->sexo == 1) Masculino @else Femenino @endif</td>
                <td colspan="1">{{$edad}}</td>
                <td colspan="1"></td>
                <td colspan="1"></td>
                <td colspan="1"></td>
                <td colspan="1"></td>
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
            <tr>
                <td style="font-size:10px">{{date("d/m/Y", strtotime($fecha_operacion))}}</td>
                <td style="font-size:10px">{{date ( 'H:i' , strtotime ( '-120 minute' , strtotime ($hora_ini) )) }}</td>
                <td style="font-size:10px !important;"><?php echo $evolucion[0]->cuadro_clinico ?>  <?php echo $evolucion[0]->laboratorio ?> </td>
                <td style="font-size:10px"><br>1.- CSV <br> 2.- PREPARAR PARA PROCEDIMIENTO <br> 3.- SOL SAL 0,9% 1000cc a 30gts x min <br> 4.-SEGUIR INDICACIONES DE MEDICO RESIDENTE <br> 5.-SE SOLICITA EXAMENES PRE-QUIRURGICOS @if($cardiologia!=null) <br> 6.- VALORACIÓN CARDIOLÓGICA @endif <br><br> </td>
                <td style="font-size:10px">&nbsp; </td>
            </tr>
            @if($cardiologia!=null)
            <tr>
                <td style="font-size:10px">{{date("d/m/Y", strtotime($fecha_operacion))}}</td>
                <td style="font-size:10px">{{date ( 'H:i' , strtotime ( '-15 minute' , strtotime ($hora_ini) )) }}</td>
                <td style="font-size:10px !important;"><?php echo str_replace('<', '&lt;', $cardiologia->resumen); ?>  <?php echo $evolucion[0]->laboratorio; ?> </td>
                <td style="font-size:10px">&nbsp; </td>
                <td style="font-size:10px"> &nbsp;</td>
            </tr>
            @endif
            <tr>
                <td style="font-size:10px">{{date("d/m/Y", strtotime($fecha_operacion))}}</td>
                <td style="font-size:10px">{{$hora_media}}</td>
                <td style="font-size:10px">PASE A SALA DE RECUPERACION DESPUES DE PROCEDIMIENTO CON SIGNOS VITALES ESTABLES. </td>
                <td style="font-size:10px"> 1.- BUTIL BROMURO DE HIOSCINBA IV PRN <br> 2.- SEGUIR INDICACIONES </td>
                <td style="font-size:10px"> &nbsp;</td>
            </tr>

            <tr>
                <td style="font-size:10px">{{date("d/m/Y", strtotime($fecha_operacion))}}</td>
                <td style="font-size:10px">{{$hora_fin}}</td>
                <td style="font-size:10px !important;"><?php echo $evolucion[1]->cuadro_clinico ?> <?php echo $evolucion[1]->laboratorio ?> </td>
                <td style="font-size:10px"> &nbsp;</td>
                <td style="font-size:10px"> &nbsp;</td>
            </tr>




		</table>
        <br>
        <div style="padding: 0;">
            <p style="text-align: center;">
                @if(!is_null($firma))
                <img width=180 height=75 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center hspace=12>
                @endif
            </p>
        </div>
        <footer>
            <p style="font-size: 12px"><b>SNS-MSP / HCU_form.005/2021&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;EVOLUCIÓN Y PRESCRIPCIONES(1)</b></p>
        </footer>

    </main>
</body>

</html>