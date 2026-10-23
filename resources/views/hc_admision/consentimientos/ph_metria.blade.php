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
            border: none;
            border-collapse: collapse;
            border-spacing: 0;

        }

        table tr td {
            margin: 0;
            padding: 3px;

        }

        #container {
            margin-left: 30px;
            margin-right: 30px;
        }
    </style>
</head>

<body>
    <div id="container">
        <div>
            <span style="color:#F6B24B;font-size:20px">CONTROL <span
                    style="font-size:20px;color:#F39200;font-weight:900;">PHMETRÍA ESOFÁGICA 24 HORAS</span> CON
                SONDA</span>
        </div>

        <table class="table" style="width: 100%;margin-left:35px">
            <tr>
                <td class="td" style="font-weight: bold;color:#108AC7;font-size:15px;">Paciente: </td>
                <td style="border-bottom: 1px solid #108AC7">
                    {{ $paciente->nombre1 }} {{ $paciente->nombre2 }}
                    {{ $paciente->apellido1 }} {{ $paciente->apellido2 }}
                </td>
            </tr>
            <tr>
                <td class="td" style="font-weight: bold;color:#108AC7;font-size:15px;">Cedula: </td>
                <td style="border-bottom: 1px solid #108AC7">{{ $paciente->id }}</td>
            </tr>
            <tr>
                <td class="td" style="font-weight: bold;color:#108AC7;font-size:15px;">Fecha de estudio:</td>
                <td class="td" style="border-bottom: 1px solid #108AC7"></td>
            </tr>
            <tr>
                <td class="td" style="font-weight: bold;color:#108AC7;font-size:15px;">Hora de Inicio (colocación de
                    sonda):</td>
                <td class="td" style="border-bottom: 1px solid #108AC7"></td>
            </tr>
            <tr>
                <td class="td" style="font-weight: bold;color:#108AC7,font-size:15px;">Hora de retiro:</td>
                <td class="td" style="border-bottom: 1px solid #108AC7"></td>
            </tr>
            <tr>
                <td class="td" style="font-weight: bold;color:#108AC7;font-size:15px;">Numero de equipo:</td>
                <td class="td" style="border-bottom: 1px solid #108AC7"></td>
            </tr>
        </table>

        <div style="margin-top:10px;">
            <span style="font-size:20px;color:#F39200;font-weight:900;">Registro de Comidas:</span>
        </div>
        <div
            style="width: 50px;height:50px;border-radius:50%;position: absolute; top: 188px;left:385px">
            <img src="{{ base_path() . '/storage/app/logo/icono_viola_1.png' }}" alt="texto_alternativo"
                style='height: 100%; width: 100%; object-fit: contain'>
        </div>

        <table class="table" style="width: 100%;margin-left:35px">
            <tr style="text-align: center !important;font-weight:bold">
                <td class="td"></td>
                <td style="width: 30%;" class="td" "><span style="font-weight: bold;color:#108AC7;font-size:15px;">Inicio</span> <br />
                        <span style="font-size:12px;color:#F39200;font-weight:900;">(Presione el botón cubiertos)</span></td>
    
                    <td style="width: 30%;" class="td"> <span  style="font-weight: bold;color:#108AC7;font-size:15px;">Fin</span> <br />
                        <span style="font-size:12px;color:#F39200;font-weight:900;">(Presione el botón cubiertos)</span></td>
                </tr>
    
                <tr>
                    <td   class="td" style="font-weight: bold;color:#108AC7;font-size:15px;width:10%">Desayuno:</td>
                    <td   class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
                    <td  class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>
    
    
                </tr>
                <tr>
                    <td class="td" style="font-weight: bold;color:#108AC7;font-size:15px;;">Almuerzo:</td>
                    <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
                    <td  class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>
    
    
                </tr>
                <tr>
                    <td class="td" style="font-weight: bold;color:#108AC7;font-size:15px;">Merienda:</td>
                    <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
                    <td  class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>
                </tr>
                <tr>
                    <td class="td" style="font-weight: bold;color:#108AC7;font-size:15px;">Comida adicional:</td>
                    <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
                    <td  class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>
                </tr>
                <tr>
                    <td class="td" style="font-weight: bold;color:#108AC7;font-size:15px;">Comida adicional:</td>
                    <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
                    <td  class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>
                </tr>
                <tr>
                    <td class="td"  style="font-weight: bold;color:#108AC7;font-size:15px;">Desayuno:</td>
                    <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
                    <td  class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>
                </tr>
            </table>





            <div style="margin-top:10px;">
                <span style="font-size:20px;color:#F39200;font-weight:900;">Registro de Sueño:</span>
            </div>
            <div
                style=";width: 50px;height:50px;border-radius:50%;position: absolute; top: 438px;left:385px">
                <img src="{{ base_path() . '/storage/app/logo/icono_viola_2.png' }}" alt="texto_alternativo"
                    style='height: 100%; width: 100%; object-fit: contain'>
            </div>
    
            <table class="table" style="width: 100%;margin-left:35px">
                <tr style="text-align: center !important;font-weight:bold">
                    <td style="width:10%" class="td"></td>
                    <td style="width: 30%;" class="td" "><span
                        style="font-weight: bold;color:#108AC7;font-size:15px;">Hora de acostarse</span> <br />
                    <span style="font-size:12px;color:#F39200;font-weight:900;">(Presione el botón cama)</span>
                </td>

                <td style="width: 30%;" class="td"> <span
                        style="font-weight: bold;color:#108AC7;font-size:15px;">Hora de levantarse</span> <br />
                    <span style="font-size:12px;color:#F39200;font-weight:900;">(Presione el botón cama)</span>
                </td>
            </tr>

            <tr>
                <td class="td">&nbsp;</td>
                <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
                <td class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>


            </tr>
            <tr>
                <td class="td"></td>
                <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
                <td class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>


            </tr>
            <tr>
                <td class="td"></td>
                <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
                <td class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>
            </tr>
            <tr>
                <td class="td"></td>
                <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
                <td class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>
            </tr>
            <tr>
                <td class="td"></td>
                <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
                <td class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
                <td class="td" style="border-bottom: 1px solid #108AC7"></td>
            </tr>
        </table>


        <div style="margin-top:10px;">
            <span style="font-size:20px;color:#F39200;font-weight:900;">Registro de Síntomas:</span>
        </div>
        <div
            style="width: 50px;height:50px;border-radius:50%;position: absolute; top: 648px;left:385px">
            <img src="{{ base_path() . '/storage/app/logo/icono_tres.png' }}" alt="texto_alternativo"
                style='height: 100%; width: 100%; object-fit: contain'>
        </div>

        <table class="table" style="width: 100%;margin-left:35px">
            <tr style="text-align: center !important;font-weight:bold">
                <td style="width:10%" class="td"></td>
                <td style="width: 30%;" class="td" "><span
                    style="font-weight: bold;color:#108AC7;font-size:15px;">Describa el Síntoma</span> <br />
                <span style="font-size:12px;color:#F39200;font-weight:900;">(Presione el botón con la letra S)</span> <br>
                <span style="font-size:12px;color:#F39200;font-weight:900;margin-t">Ejemplo:Tos,reflujo,acidez,ardor,hipo</span>
            </td>

            <td style="width: 30%;" class="td"> <span
                    style="font-weight: bold;color:#108AC7;font-size:15px;">Hora del síntoma</span> <br />
            </td>
        </tr>

        <tr>
            <td class="td">&nbsp;</td>
            <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
            <td class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>


        </tr>
        <tr>
            <td class="td"></td>
            <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
            <td class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>


        </tr>
        <tr>
            <td class="td"></td>
            <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
            <td class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>
        </tr>
        <tr>
            <td class="td"></td>
            <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
            <td class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>
        </tr>
        <tr>
            <td class="td"></td>
            <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
            <td class="td" style="border-bottom: 1px solid #108AC7">&nbsp;</td>
        </tr>
        <tr>
            <td class="td">&nbsp;</td>
            <td class="td" style="border-bottom: 1px solid #108AC7;border-right:1px solid #108AC7"></td>
            <td class="td" style="border-bottom: 1px solid #108AC7"></td>
        </tr>
    </table>

    <table class="table" style="width: 100%;margin-left:35px;margin-top:15px;">

        <tr>
            <td class="td">
                
                <img src="{{ base_path() . '/storage/app/logo/icono_tres.png' }}" alt="texto_alternativo"
                    style='height: 30px; width: 30px; object-fit: contain'>
            
            </td>

            <td style="background-color: #F39200">
                <span style="color:white">Cuando se presente el síntoma principal como dolor en el pecho, tos, sensación que se regresan los
                    alimentos o ardor en el pecho o reflujo, deberá presionar una sola vez el botón con la letra S (Síntomas)</span>
            </td>
        </tr>
        <tr>
            <td>
                <img src="{{ base_path() . '/storage/app/logo/icono_cuatro.png' }}" alt="texto_alternativo"
                style='height: 30px; width: 30px; object-fit: contain'>
        
            </td>

            <td style="background-color: #F39200">
                <span style="color:white">Otros síntomas como nauseas, vómitos, mareos, etc. </span>
            </td>
        </tr>

        <tr>
            <td>
                <img src="{{ base_path() . '/storage/app/logo/icono_cinco.png' }}" alt="texto_alternativo"
                style='height: 30px; width: 30px; object-fit: contain'>
            </td>

            <td style="background-color: #F39200">
                <span style="color:white">No debe comer chicles, gaseosas, cítricos. Debe registrar en el dispositivo todo lo acontecido durante
                    24 horas.
                    </span>
            </td>
        </tr>

        <tr>
            <td>
                <img src="{{ base_path() . '/storage/app/logo/icono_seis.png' }}" alt="texto_alternativo"
                style='height: 30px; width: 30px; object-fit: contain'>
            </td>

            <td style="background-color: #F39200">
                <span style="color:white">Debe retornar 24 horas después para el retiro de la sonda a nuestras instalaciones.
                    </span>
            </td>

            
        </tr>

        <tr>
            <td>
                <img src="{{ base_path() . '/storage/app/logo/icon 7.png' }}" alt="texto_alternativo"
                style='height: 30px; width: 30px; object-fit: contain'>
            </td>

            <td style="background-color: #F39200">
                <span style="color:white">No olvidar este documento, el cual nos ayudará a realizar el estudio de manera más eficaz.
                    </span>
            </td>

            
        </tr>
      

    </table>
    </div>
</body>

</html>
