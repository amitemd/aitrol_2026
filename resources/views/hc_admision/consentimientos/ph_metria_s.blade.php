<!DOCTYPE html>
<html lang="es">
@php
    use Carbon\Carbon;
    $edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
    <meta charset="utf-8" />
    <title>CONTROL PHMETRÍA ESOFÁGICA 24 HORAS CON SONDA</title>
    <style>
        * {
            font-size: 12px;
            padding: 0px;

        }

        .table {
            border-collapse: collapse;
            padding: -1px;

        }

        .table,
        .td {
            border: 1px solid black;
        }

        .th {
            border: 1px solid black;
            text-align: center;
        }

        table tr td {

            padding: 3px;

        }

        #container {
            margin: 0px auto;
        }
    </style>
</head>

<body>
    <div id="container">


        <div style="text-align:center">
            <span style="font-weight:bold;font-size:15px;">CONTROL PHMETRÍA ESOFÁGICA 24 HORAS CON SONDA</span>
        </div>

        <table class="table" style="width: 100%;margin-top:25px">
            <tr>
                <td class="td" style="font-weight: bold;">Paciente: {{$paciente->nombre1}} {{$paciente->nombre2}}  {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
            </tr>
            <tr>
                <td class="td" style="font-weight: bold;">Cedula: {{$paciente->id}}</td>
            </tr>
            <tr>
                <td class="td" style="font-weight: bold;">Fecha de estudio:</td>
            </tr>
            <tr>
                <td class="td" style="font-weight: bold;">Hora de Inicio (colocación de sonda):</td>
            </tr>
            <tr>
                <td class="td" style="font-weight: bold;">Hora de retiro:</td>
            </tr>
            <tr>
                <td class="td" style="font-weight: bold;">Numero de equipo:</td>
            </tr>
        </table>

        <div style="text-align:left;margin-top:10px;">
            <span style="color:#0070C0;font-size:14px;">Registro de Comidas:</span>
        </div>

        <table class="table" style="width: 100%;">

            <tr style="text-align: center !important;font-weight:bold">
                <td style="width: 30%;" class="td"> Inicio <br />
                    (Presiona los cubiertos)</td>

                <td style="width: 30%;" class="td">  Fin <br />
                    (Presiona los cubiertos)</td>
            </tr>

            <tr>
                <td  class="td">Desayuno:</td>
                <td  class="td"></td>


            </tr>
            <tr>
                <td class="td">Almuerzo:</td>
                <td class="td"></td>


            </tr>
            <tr>
                <td class="td">Merienda:</td>
                <td class="td"></td>
            </tr>
            <tr>
                <td class="td">Comida adicional:</td>
                <td class="td"></td>
            </tr>
            <tr>
                <td class="td">Comida adicional:</td>
                <td class="td"></td>
            </tr>
            <tr>
                <td class="td">Desayuno:</td>
                <td class="td"></td>
            </tr>
        </table>


        <div style="text-align:left;margin-top:10px;">
            <span style="color:#0070C0;font-size:14px;">Registro de Sueño:</span>
        </div>


        <table class="table" style="width: 100%;">

            <tr style="text-align: center !important;font-weight:bold">
                <td style="width: 30%;" class="td" ">Hora de acostarse<br />
                    (Presione la cama)
                </td>

                <td style="width: 30%;" class="td">Hora de levantarse<br/>
                    (Presione la cama)
                </td>
            </tr>

            <tr>
                <td  class="td">&nbsp;</td>
                <td  class="td">&nbsp;</td>


            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>


            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
            </tr>
        </table>

        <div style="text-align:left;margin-top:10px;">
            <span style="color:#0070C0;font-size:14px;">Registro de Síntomas:</span>
        </div>


        <table class="table" style="width: 100%;">

            <tr style="text-align: center !important;">
                <td style="width: 30%;" class="td" ">
                  <b>Describa el síntoma</b>
                  Ejemplo: tos, reflujo, acidez, ardor, hipo
                </td>

                <td style="width: 30%;" class="td"><b>Hora del síntoma</b>
                  <br/>
                  (presione la letra S)
                </td>
            </tr>

            <tr>
                <td  class="td">&nbsp;</td>
                <td  class="td">&nbsp;</td>


            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>


            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
            </tr>
        </table>

        <div style="text-align:center">
            <span>
                Cuando se presenten síntomas como dolor en el pecho, tos, sensación que se regresan los alimentos o ardor en el pecho o reflujo,<b> deberá presionar </b> una sola vez el botón con la letra <b>S</b> (síntomas).
            </span>
        </div>

        <div style="margin-top: 8px">
              <b>
              No debe comer chicles, gaseosas, cítricos. Debe registrar en el dispositivo todo lo acontecido durante 24 horas.
              Debe retornar 24 horas después para el retiro de la sonda a nuestras instalaciones.
              No olvidar este documento, el cual nos ayudará a realizar el estudio de manera más eficaz.
            </b>
        </div>







    </div>
</body>

</html>
