<!DOCTYPE html>
@php $data = date("Y-m-d"); @endphp
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Anexo 11. Escala de riesgo de caída de Morse</title>
    <style>
        * {
            font-size: 12px;
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
                ESCALA DE RIESGO DE CAIDA DE MORSE
            </th>
            <th style="width:15%;padding:10px;">
                <table border="1" style="padding: none;width:100%;">
                    <tr>
                        <th>Version</th>
                        <td style="width: 65px;">1</td>
                    </tr>
                    <tr>
                        <th>Codigo</th>
                        <td>DNCSS-MSP-008</td>
                    </tr>
                    <tr>
                        <th>Fecha</th>
                        <td>{{$data}}</td>
                    </tr>
                </table>
            </th>
        </tr>
    </table>
    <table class="table" style="width: 100%;">
        <tr>
            <th class="th" style="background-color: #EAF2F5;height:45px !important;">
                De 13 años a 18 años y adultos
            </th>
        </tr>
    </table>
    <table class="table" style="width: 100%;">
        <tr>
            <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;height:45px !important;">
                Variables
            </th>
            <th class="th" style="background-color: #C1E1E0;height:45px !important;">
                Puntajes
            </th>
        </tr>
        <tr>
            <th style="height:60px !important;" class="th">1. CAIDA PREVIA</th>
            <th class="th">@if(($camilla_gestion->caida_previa)== 0) NO @elseif(($camilla_gestion->caida_previa)== 25) SI @endif</th>
        </tr>
        <tr>
            <th style="height:60px !important;" class="th">2. COMORBILIDADES</th>
            <th class="th">@if(($camilla_gestion->comorbilidades)== 0) NO @elseif(($camilla_gestion->comorbilidades)== 15) SI @endif</th>
        </tr>
        <tr>
            <th style="height:60px !important;" class="th">3. AYUDA PARA DEAMBULAR</th>
            <th class="th">@if(($camilla_gestion->deambular)== 0) NINGUNA / REPOSO EN CAMA / ASISTENCIA @elseif(($camilla_gestion->deambular)== 15) BASTON / MULETA / CAMINADOS @elseif(($camilla_gestion->deambular)== 30) SE APOYA EN LOS MUEBLES @endif</th>
        </tr>
        <tr>
            <th style="height:60px !important;" class="th">4. VENOCLISIS</th>
            <th class="th">@if(($camilla_gestion->venoclisis)== 0) NO @elseif(($camilla_gestion->venoclisis)== 20) SI @endif</th>
        </tr>
        <tr>
            <th style="height:60px !important;" class="th">5. MARCHA</th>
            <th class="th">@if(($camilla_gestion->marcha)== 0) NORMAL / REPOSO EN CAMA / SILLA DE RUEDAS @elseif(($camilla_gestion->marcha)== 10) DEBIL @elseif(($camilla_gestion->marcha)== 20) LIMITADA @endif</th>
        </tr>
        <tr>
            <th style="height:60px !important;" class="th">6. ESTADO MENTAL</th>
            <th class="th">@if(($camilla_gestion->estado_mental )== 0) RECONOCE SUS LIMITACIONES @elseif(($camilla_gestion->estado_mental)== 15) SOBREESTIMA U OLVIDA SUS LIMITACIONES @endif</th>
        </tr>
        <tr>
            <th style="height:60px !important;" class="th">7. PUNTUACION FINAL Y NIVEL DEL RIESGO</th>
            <th class="th">{{$camilla_gestion->nivel_riesgo}}</th>
        </tr>
    </table>
    <table class="table" style="width: 100%;">
        <tr>
            <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;height:45px !important;">
                RIESGO
            </th>
            <th class="th" style="background-color: #C1E1E0;height:45px !important;">
                PUNTAJES
            </th>
            <th class="th" style="background-color: #C1E1E0;height:45px !important;">
                ACCION
            </th>
        </tr>
    </table>
    <table class="table" style="width:100%;">
        @if($camilla_gestion->nivel_riesgo>=0  && $camilla_gestion->nivel_riesgo < 25) <tr>
            <th style="height:60px !important;width:80%;" class="th"><img src="{{ public_path('imagenes/bajo_flecha.png')}}" style="margin-top:15px;" alt="logo" /></th>
            <th class="th" style="width:10.7%;">
                <div style="margin-top:10 !important;">{{$camilla_gestion->nivel_riesgo}}</div>
            </th>
            <th class="th">
                <div style="margin-top:10 !important;">CUIDADOS BAJO ENFERMERIA</div>
            </th>
            </tr>
            @endif
            @if($camilla_gestion->nivel_riesgo >= 25 && $camilla_gestion->nivel_riesgo<=50) <tr>
                <th style="height:60px !important;width:80%;" class="th"><img src="{{ public_path('imagenes/medio_flecha.png')}}" style="margin-top:15px;" alt="logo" /></th>
                <th class="th" style="width:10.7%;">
                    <div style="margin-top:10 !important;">{{$camilla_gestion->nivel_riesgo}}</div>
                </th>
                <th class="th">
                    <div style="margin-top:10 !important;font-size:10px !important;">IMPLEMENTACION DEL PLAN DE PREVENCION</div>
                </th>
                </tr>
                @endif
                @if($camilla_gestion->nivel_riesgo>=50)
                <tr>
                    <th style="height:60px !important;width:80%;" class="th"><img src="{{ public_path('imagenes/alto_flecha.png')}}" style="margin-top:15px;" alt="logo" /></th>
                    <th class="th" style="width:10.7%;">
                        <div style="margin-top:10 !important;">{{$camilla_gestion->nivel_riesgo}}</div>
                    </th>
                    <th class="th">
                        <div style="margin-top:10 !important;font-size:10px !important;">IMPLEMENTACION DE MEDIDAS ESPECIALES</div>
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