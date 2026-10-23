<!DOCTYPE html>
<html lang="es">
@php
    use Carbon\Carbon;
    $edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
    <meta charset="utf-8" />
    <title>Control PHmetría Esofágica 24 horas con sonda</title>
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

        .color_texto_principal {

            color: #01718F;
            font-size: 20px;
        }

        .color_texto_resaltado {
            color: #01718F;
            font-size: 20px;
            font-weight: 300;
        }

        .color_texto_label {
            color: #479BB7;
            font-weight: bolder;
            font-size: 15px;
        }

        .border_color {
            border-bottom: 1px solid #71B9DD;
        }

        .borde_diestro {
            border-bottom: 1px solid #108AC7;
            border-right: 1px solid #108AC7
        }

        .fondo_footer {
            background-color: #479BB7;
        }
    </style>
</head>

<body>
    <div id="container">
        <div>
            <span class="color_texto_principal">CONTROL <span class="color_texto_resaltado">PHMETRÍA ESOFÁGICA 24
                    HORAS</span> CON
                SONDA</span>
        </div>

        <table class="table" style="width: 100%;margin-left:35px">
            <tr>
                <td class="td"> <span class="color_texto_label">Paciente: </span></td>
                <td class="border_color">
                    {{ $paciente->nombre1 }} {{ $paciente->nombre2 }}
                    {{ $paciente->apellido1 }} {{ $paciente->apellido2 }}
                </td>
            </tr>
            <tr>
                <td class="td"> <span class="color_texto_label">Cedula: </span></td>
                <td class="border_color">{{ $paciente->id }}</td>
            </tr>
            <tr>
                <td class="td"> <span class="color_texto_label">Fecha de estudio: </span></td>
                <td class="border_color"></td>
            </tr>
            <tr>
                <td class="td"><span class="color_texto_label">Hora de Inicio (colocación de sonda):</span></td>
                <td class="border_color"></td>
            </tr>
            <tr>
                <td class="td"> <span class="color_texto_label">Hora de retiro: </span></td>
                <td class="border_color"></td>
            </tr>
            <tr>
                <td class="td"> <span class="color_texto_label">Numero de equipo: </span></td>
                <td class="border_color"></td>
            </tr>
        </table>

        <div style="margin-top:10px;">
            <span class="color_texto_resaltado">Registro de Comidas:</span>
        </div>
        <div style="width: 50px;height:50px;x;position: absolute; top: 198px;left:385px">
            <img src="{{ base_path() . '/storage/app/logo/icono_uno.png' }}" alt="texto_alternativo"
                style='height: 100%; width: 100%; object-fit: contain'>
        </div>

        <table class="table" style="width: 100%;margin-left:35px">
            <tr style="text-align: center !important;font-weight:bold">
                <td class="td"></td>
                <td style="width: 30%;" class="td" "><span style="font-weight: bold;color:#108AC7;font-size:15px;">Inicio</span> <br />
                        <span class="color_texto_label">(Presione el botón cubiertos)</span></td>
    
                    <td style="width: 30%;" class="td"> <span  style="font-weight: bold;color:#108AC7;font-size:15px;">Fin</span> <br />
                        <span class="color_texto_label">(Presione el botón cubiertos)</span></td>
                </tr>
    
                <tr>
                    <td  class="color_texto_label" style="width:10%">Desayuno:</td>
                    <td  class="borde_diestro"></td>
                    <td  class="border_color">&nbsp;</td>
    
    
                </tr>
                <tr>
                    <td  class="color_texto_label">Almuerzo:</td>
                    <td class="borde_diestro"></td>
                    <td  class="border_color">&nbsp;</td>
    
    
                </tr>
                <tr>
                    <td class="color_texto_label">Merienda:</td>
                    <td class="borde_diestro"></td>
                    <td  class="border_color">&nbsp;</td>
                </tr>
                <tr>
                    <td class="color_texto_label">Comida adicional:</td>
                    <td class="borde_diestro"></td>
                    <td  class="border_color">&nbsp;</td>
                </tr>
                <tr>
                    <td class="color_texto_label">Comida adicional:</td>
                    <td class="borde_diestro"></td>
                    <td  class="border_color">&nbsp;</td>
                </tr>
                <tr>
                    <td class="color_texto_label">Desayuno:</td>
                    <td class="borde_diestro"></td>
                    <td  class="border_color">&nbsp;</td>
                </tr>
            </table>





            <div style="margin-top:10px;">
                <span class="color_texto_resaltado">Registro de Sueño:</span>
            </div>
            <div
                style=";width: 50px;height:50px;position: absolute; top: 458px;left:385px">
                <img src="{{ base_path() . '/storage/app/logo/icono_dos.png' }}" alt="texto_alternativo"
                    style='height: 100%; width: 100%; object-fit: contain'>
            </div>
    
            <table class="table" style="width: 100%;margin-left:35px">
                <tr style="text-align: center !important;font-weight:bold">
                    <td style="width:10%" class="td"></td>
                    <td style="width: 30%;" class="td" "><span
                        style="font-weight: bold;color:#108AC7;font-size:15px;">Hora de acostarse</span> <br />
                    <span class="color_texto_label">(Presione el botón cama)</span>
                </td>

                <td style="width: 30%;" class="td"> <span
                        style="font-weight: bold;color:#108AC7;font-size:15px;">Hora de levantarse</span> <br />
                    <span class="color_texto_label">(Presione el botón cama)</span>
                </td>
            </tr>

            <tr>
                <td class="td">&nbsp;</td>
                <td class="borde_diestro"></td>
                <td class="border_color">&nbsp;</td>


            </tr>
            <tr>
                <td class="td"></td>
                <td class="borde_diestro"></td>
                <td class="border_color">&nbsp;</td>


            </tr>
            <tr>
                <td class="td"></td>
                <td class="borde_diestro"></td>
                <td class="border_color">&nbsp;</td>
            </tr>
            <tr>
                <td class="td"></td>
                <td class="borde_diestro"></td>
                <td class="border_color">&nbsp;</td>
            </tr>
            <tr>
                <td class="td"></td>
                <td class="borde_diestro"></td>
                <td class="border_color">&nbsp;</td>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="borde_diestro"></td>
                <td class="border_color"></td>
            </tr>
        </table>


        <div style="margin-top:10px;">
            <span class="color_texto_resaltado">Registro de Síntomas:</span>
        </div>
        <div style="width: 50px;height:50px;border-radius:50%;position: absolute; top: 648px;left:385px">
            <img src="{{ base_path() . '/storage/app/logo/iconos_siete.png' }}" alt="texto_alternativo"
                style='height: 100%; width: 100%; object-fit: contain'>
        </div>

        <table class="table" style="width: 100%;margin-left:35px">
            <tr style="text-align: center !important;font-weight:bold">
                <td style="width:10%" class="td"></td>
                <td style="width: 30%;" class="td" "><span
                    style="font-weight: bold;color:#108AC7;font-size:15px;">Describa el Síntoma</span> <br />
                <span class="color_texto_label">(Presione el botón corazón)</span> <br>
                <span class="color_texto_label">Ejemplo:Tos,reflujo,acidez,ardor,hipo</span>
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
                
                <img src="{{ base_path() . '/storage/app/logo/iconos_siete.png' }}" alt="texto_alternativo"
                    style='height: 30px; width: 30px; object-fit: contain'>
            
            </td>

            <td class="fondo_footer">
                <span style="color:white"><span style="font-weight: bolder;">Cuando se presenten síntomas </span>como dolor en el pecho, tos, sensación que se regresan
                    los alimentos o ardor en el pecho o reflujo, <span style="font-weight: bolder">deberá presionar una sola vez el botón del corazón.</span></span>
            </td>
        </tr>
        <tr>
            <td >
                <img src="{{ base_path() . '/storage/app/logo/icono_ocho.png' }}" alt="texto_alternativo"
                style='height: 30px; width: 30px; object-fit: contain'>
        
            </td>

            <td class="fondo_footer">
                <span style="color:white"><span style="font-weight: bolder">No debe comer </span>chicles, gaseosas, cítricos. <span style="font-weight:bolder">Debe registrar en el dispositivo todo </span>lo acontecido
                    durante 24 horas</span>
            </td>
        </tr>

        <tr>
            <td >
                <img src="{{ base_path() . '/storage/app/logo/icono_nueve.png' }}" alt="texto_alternativo"
                style='height: 30px; width: 30px; object-fit: contain'>
            </td>

            <td class="fondo_footer">
                <span style="color:white"><span style="font-weight: bolder">Debe retornar 24 horas después </span>para el retiro de la sonda a nuestras instalaciones.
                    </span>
            </td>
        </tr>
        <tr>
            <td>
                <img src="{{ base_path() . '/storage/app/logo/icono_diez.png' }}" alt="texto_alternativo"
                style='height: 30px; width: 30px; object-fit: contain'>
            </td>

            <td class="fondo_footer">
                <span style="color:white"><span style="font-weight: bolder">No olvidar este documento </span>, el cual nos ayudará a realizar el estudio de manera más eficaz.
                    </span>
            </td>

            
        </tr>
    </table>













    </div>
</body>

</html>
