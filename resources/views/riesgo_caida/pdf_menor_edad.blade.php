<!DOCTYPE html>
@php $data = date("Y-m-d"); @endphp
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Anexo 12. Escala de riesgo de caída de MACDEMS </title>
    <style>
        * {
            font-size: 18px;
            padding: -1px;

        }

        .table {
            border-collapse: collapse;
            padding: -1px;

        }

        .table,
        .td {
            border: 1px solid black;
            text-align: center;
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
    <table class="table" style="width: 100%;">
        <tr style="padding: 20px;">
            <td>
                <img src="{{ public_path('imagenes/logo_riesgo.png')}}" alt="logo" />
            </td>
            <th style="text-align: center !important;width:50%;font-size:30px !important;">
                ESCALA DE RIESGO DE CAIDA DE MACDEMS
            </th>
            <th style="width:25%;padding:10px;">
                <table class="table" style="padding: none;width:100%;">
                    <tr>
                        <th style="border-bottom:1px solid black;border-right:1px solid black;">Version</th>
                        <td style="border-bottom:1px solid black;">1</td>
                    </tr>
                    <tr>
                        <th style="border-bottom:1px solid black;border-right:1px solid black;">Codigo</th>
                        <td style="border-bottom:1px solid black;">DNCSS-MSP-008</td>
                    </tr>
                    <tr>
                        <th style="border-right:1px solid black;">Fecha</th>
                        <td>{{$data}}</td>
                    </tr>
                </table>
            </th>
        </tr>
    </table>
    <table class="table" style="width: 100%;">
        <tr>
            <th class="th" style="background-color: #EAF2F5;height:45px !important;">
                De 0 años a 12 años
            </th>
        </tr>
    </table>
    <table class="table" style="width: 100%;">
        <tr>
            <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;height:55px !important;">
                Variables
            </th>
            <th class="th" style="background-color: #C1E1E0;height:55px !important;">
                Puntajes
            </th>
        </tr>
    </table>
    <table class="table" style="width: 100%;">
        <tr>
            <td rowspan="5" style="border-right:1px solid black;height:25%">
                1.EDAD
            </td>
            <td style="border-right:1px solid black;border-bottom:1px solid black;width:55%;">
                Recien Nacido
            </td>
            <td style="border-bottom:1px solid black;">
                2
            </td>
        </tr>
        <tr>
            <td style="border-right:1px solid black;border-bottom:1px solid black;width:55%;">
                Lactante Menor
            </td>
            <td style="border-bottom:1px solid black;">
                2
            </td>
        </tr>
        <tr >
            <td style="border-right:1px solid black;border-bottom:1px solid black;width:55%;">
                Lactante Mayor
            </td>
            <td style="border-bottom:1px solid black;">
                3
            </td>
        </tr>
        <tr>
            <td style="border-right:1px solid black;border-bottom:1px solid black;width:55%;">
                Pre-Escolar
            </td>
            <td style="border-bottom:1px solid black;">
                3
            </td>
        </tr>
        <tr>
            <td style="border-right:1px solid black;border-bottom:1px solid black;width:55%;">
                Escolar
            </td>
            <td >
                1
            </td>
        </tr>
        <tr>

        </tr>
    </table>
    <table class="table" style="width: 100%;">
        <tr>
            <th  style="background-color: #C1E1E0;width:58%;border-right:1px solid black;height:60px !important;">
                RIESGO
            </th>
            <th  style="background-color: #C1E1E0;height:60px !important;border-right:1px solid black">
                PUNTAJES
            </th>
            <th  style="background-color: #C1E1E0;height:60px !important;">
                ACCION
            </th>
        </tr>
    </table>
    <table class="table" style="width:100%;">
        @if($camilla_gestion->nivel_riesgo>=0 && $camilla_gestion->nivel_riesgo <= 1) <tr>
            <th style="height:60px !important;width:60%!important;" class="th"><img src="{{ public_path('imagenes/bajo_flecha.png')}}" style="margin-top:15px;" alt="logo" /></th>
            <th class="th" style="width:20.7%;">
                <div style="margin-top:10 !important;">{{$camilla_gestion->nivel_riesgo}}</div>
            </th>
            <th class="th">
                <div style="margin-top:10 !important;">CUIDADOS BAJO ENFERMERIA</div>
            </th>
            </tr>
            @endif
            @if($camilla_gestion->nivel_riesgo >=2 && $camilla_gestion->nivel_riesgo<=3) <tr>
                <th style="height:80px !important;width:60%;" class="th"><img src="{{ public_path('imagenes/medio_flecha.png')}}" style="margin-top:15px;" alt="logo" /></th>
                <th class="th" style="width:10.7%;">
                    <div style="margin-top:10 !important;">{{$camilla_gestion->nivel_riesgo}}</div>
                </th>
                <th class="th">
                    <div style="margin-top:10 !important;font-size:10px !important;">IMPLEMENTACION DEL PLAN DE PREVENCION</div>
                </th>
                </tr>
                @endif
                @if($camilla_gestion->nivel_riesgo >=4 && $camilla_gestion->nivel_riesgo<=6) <tr>
                    <th style="height:80px !important;width:58%;" class="th"><img src="{{ public_path('imagenes/alto_flecha.png')}}" style="margin-top:15px;height:50px;" alt="logo" /></th>
                    <th class="th" style="width:22%;">
                        <div style="margin-top:10 !important;font-size:15px">{{$camilla_gestion->nivel_riesgo}}</div>
                    </th>
                    <th class="th">
                        <div style="margin-top:10 !important;font-size:15px !important;">IMPLEMENTACION DE MEDIDAS ESPECIALES</div>
                    </th>
                    </tr>
                    @endif
    </table>
    @php
    use Carbon\Carbon;
    $edad = Carbon::parse($datosUsuarios->fecha_nacimiento)->age;
    @endphp
    <table class="table" style="width:100%;">
        <tr>
            <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;height:60px !important;">
                Nombres
            </th>
            <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;height:60px !important;">
                Apellidos
            </th>
            <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;height:60px !important;">
                Fecha Nacimiento
            </th>
            <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;height:60px !important;">
                Edad
            </th>
        </tr>
        <tr>
            <td style="border-right:1px solid black;height:60px !important;">
                {{$datosUsuarios->nombre1}} {{$datosUsuarios->nombre2}}
            </td>
            <td style="border-right:1px solid black;height:60px !important;">
                {{$datosUsuarios->apellido1}} {{$datosUsuarios->apellido2}}
            </td>
            <td style="border-right:1px solid black;height:60px !important;">
                {{$datosUsuarios->fecha_nacimiento}}
            </td>
            <td style="border-right:1px solid black;height:60px !important;">
                {{$edad}}
            </td>
        </tr>
    </table>
</body>

</html>