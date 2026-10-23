<!DOCTYPE html>
<html>

<head>
    <meta http-equiv=”Content-Type” content=”text/html; charset=UTF-8″ />
    <style>
        table {
            font-size: 8px;
            font-family: Arial, Helvetica, sans-serif;
            width: 100%;
        }

        .template {
            background-color: antiquewhite;
            display: flex;

        }

        .columna1 {
            width: 48%;
        }

        .columna2 {
            margin-left: 50% width: 48%;
        }

        .tabla1 {
            border: 1.5px solid rgb(63, 63, 63);
        }

        .tabla2 {
            font-size: 8px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .titulo th {
            background-color: rgb(92, 190, 225);
            text-align: left;
        }

        .subtitulo td {
            background-color: rgb(156, 240, 195);
            border: 0.5px solid black;
            text-align: center;
        }

        .campos {
            text-align: center;
            border: 0.5px solid black;
            font-size: 9px !important;
        }

        .campos1 {
            border: 0.5px solid black;
        }

        .subtitulo2 {
            border: 0.5px solid black;
            text-align: center;
            background-color: rgb(156, 240, 195);
            font-size: 6px;

        }

        .subtitulo3 {
            border: 0.5px solid black;
            text-align: center;
            background-color: rgb(156, 240, 195);
            font-size: 6px;

        }

        .subtitulo4 {
            border: 0.5px solid black;
            text-align: center;
            background-color: rgb(156, 240, 195);
            font-size: 6px;
            color: rgb(156, 240, 195)
        }

        .tituloc {
            border: 0.5px solid black;
            background-color: rgb(92, 190, 225);
            font-size: 5px;
        }

        .tituloc1 {
            border: 0.5px solid black;
            background-color: rgb(92, 190, 225);
            text-align: center;
            font-size: 5px;
        }

        .tituloc2 {
            border: 0.5px solid black;
            background-color: rgb(92, 190, 225);
            text-align: left;
            font-size: 8px;
        }

        .tituloc3 {
            border: 0.5px solid black;
            background-color: rgb(92, 190, 225);
            text-align: right;
            font-size: 4px;
        }

        .titulodoble1 {
            background-color: rgb(92, 190, 225);
            font-size: 8px;
            font-family: Arial, Helvetica, sans-serif;
            text-align: left;
            border: 0.5px solid black;
        }

        .titulodoble2 {
            background-color: rgb(92, 190, 225);
            font-size: 8px;
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
            border: 0.5px solid black;
        }

        .campos {
            font-size: 7px;
        }

        .observacion_p3 {
            height: 50px;
            font-size: 6px;
            line-height: 0.5px;

        }

        .observaciones-span {
            font-size: 5px;
            text-align: left;
            background-color: rgb(156, 240, 195);
            border: 0.5px solid black;
            padding-top: 30px;
            height: 20px;
            width: 45px;
            font-family: Arial, Helvetica, sans-serif;
        }

        @page {
            size: A4; 
            margin: 1cm;
            counter-increment: page;
        }

        body {
            counter-reset: page;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            padding: 10px;
        }
        .pageNumber:after {
            content: counter(page);
        }
    </style>
    <title>Formulario 008</title>
</head>

<body>
    {{-- TABLA A --}}
    <table class="tabla1" cellspacing="0">
        @php$fecha = date('Y-m-d H:i:s');
        $triaje = $solicitudemer->manchester->first();
        $fecha_dia = date_format(new DateTime($fecha), 'Y-m-d');
        $fecha_hora = date_format(new DateTime($fecha), 'H:i');
        @endphp
        <thead>
            <tr class="titulo">
                <th colspan="65">
                    A. DATOS DEL ESTABLECIMIENTO
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="subtitulo">
                <th class="subtitulo2" colspan="15">INSTITUCIÓN DEL SISTEMA</th>
                <th class="subtitulo2" colspan="6">UNICÓDIGO</th>
                <th class="subtitulo2" colspan="15">ESTABLECIMIENTO DE SALUD</th>
                <th class="subtitulo2" colspan="18">NÚMERO DE HISTORIA CLINICA UNICA</th>
                <th class="subtitulo2" colspan="11">NÚMERO DE ARCHIVO</th>
            </tr>
            <tr class="datos">
                <td class="campos" colspan="15">
                    @if (!is_null($empresa))
                    {{ $empresa->nombrecomercial }}
                    @endif
                </td>
                <td class="campos" colspan="6">{{ $empresa->unicodigo}}</td>
                <td class="campos" colspan="15">{{$empresa->establecimiento}}</td>
                <td class="campos" colspan="18">{{$paciente->id}}</td>
                <td class="campos" colspan="11">@if($solicitudemer->archivo){{$solicitudemer->archivo->id}}@endif</td>
            </tr>
        </tbody>
    </table>

    {{-- TABLA B --}}
    <div style="margin-top: 10px">
        <table class="tabla1" cellspacing="0">
            <thead>
                <tr class="titulo">
                    <th colspan="65">
                        B. REGISTRO DE ADMISIÓN
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="subtitulo">
                    <td class="subtitulo2" colspan="29">FECHA DE ADMISIÓN<br>(aaaa-mm-dd)</td>
                    <td class="subtitulo2" colspan="26">NOMBRE Y APELLIDO DEL ADMISIONISTA </td>
                    <td class="subtitulo2" colspan="10">HISTORIA CLÍNICA EN ESTABLECIMIENTO</td>
                </tr>

                <tr class="datos">
                    @php $fecha_tabla_b_dia = date_format(new DateTime($solicitudemer->fecha_ingreso), 'Y-m-d'); @endphp
                    <td colspan="29" rowspan="" class="campos">
                        @if ($solicitudemer->fecha_ingreso == null)
                        {{ $fecha_dia }}@else{{ $fecha_tabla_b_dia }}
                        @endif
                    </td>
                    <td colspan="26" rowspan="" class="campos">{{$user_crea->nombre1}} {{$user_crea->apellido1}} {{$user_crea->apellido2}}</td>
                    <td colspan="2" rowspan="" class="subtitulo2">SI</td>
                    <td colspan="3" rowspan="" class="campos">@if(!is_null($hc))X @endif</td>
                    <td colspan="2" rowspan="" class="subtitulo2">NO</td>
                    <td colspan="3" rowspan="" class="campos">@if(is_null($hc->estado==0))X @endif</td>
                </tr>

                <tr class="subtitulo">
                    <td class="subtitulo2" colspan="15" rowspan="2">PRIMER APELLIDO</td>
                    <td class="subtitulo2" colspan="14" rowspan="2">SEGUNDO APELLIDO</td>
                    <td class="subtitulo2" colspan="12" rowspan="2">PRIMER NOMBRE</td>
                    <td class="subtitulo2" colspan="12" rowspan="2">SEGUNDO NOMBRE</td>
                    <td class="subtitulo2" colspan="12" rowspan="1">TIPO DE DOCUMENTO DE IDENTIFICACIÓN</td>
                </tr>
                <tr>
                    <td class="subtitulo2" rowspan="1" colspan="3">CC/CI</td>
                    <td class="subtitulo2" rowspan="1" colspan="3">PAS.</td>
                    <td class="subtitulo2" rowspan="1" colspan="3">CARNÉ</td>
                    <td class="subtitulo2" rowspan="1" colspan="3">S/D</td>
                </tr>
                <tr>
                    <td class="campos" colspan="15">{{ $paciente->apellido1 }}</td>
                    <td class="campos" colspan="14">{{ $paciente->apellido2 }}</td>
                    <td class="campos" colspan="12">{{ $paciente->nombre1 }}</td>
                    <td class="campos" colspan="12">{{ $paciente->nombre2 }}</td>
                    <td class="campos" colspan="3">@if($paciente->tipo_documento == 1 ) X @endif</td>
                    <td class="campos" colspan="3">@if($paciente->tipo_documento == 2 ) X @endif</td>
                    <td class="campos" colspan="3">@if($paciente->tipo_documento == 3 ) X @endif</td>
                    <td class="campos" colspan="3">@if($paciente->tipo_documento == 4 ) X @endif</td>
                </tr>

                <tr class="subtitulo">
                    <td class="subtitulo2" colspan="14" rowspan="1">ESTADO CIVIL</td>
                    <td class="subtitulo2" colspan="5" rowspan="2">SEXO</td>
                    <td class="subtitulo2" colspan="12" rowspan="2">N° TELEFONO FIJO</td>
                    <td class="subtitulo2" colspan="15" rowspan="2">N° TELEFONO CELULAR</td>
                    <td class="subtitulo2" colspan="19" rowspan="2">FECHA DE NACIMIENTO<br>(aaaa-mm-dd)</td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="2" rowspan="1">SOL</td>
                    <td class="subtitulo2" colspan="2" rowspan="1">CAS</td>
                    <td class="subtitulo2" colspan="2" rowspan="1">DIV</td>
                    <td class="subtitulo2" colspan="2" rowspan="1">VIU</td>
                    <td class="subtitulo2" colspan="2" rowspan="1">UN<< /td>
                    <td class="subtitulo2" colspan="2" rowspan="1">U-H</td>
                    <td class="subtitulo2" colspan="2" rowspan="1">NA</td>
                </tr>
                <tr>
                    <td class="campos" colspan="2" rowspan="1">
                        @if ($paciente->estadocivil == 1)
                        <span> X</span>
                        @endif
                    </td>
                    <td class="campos" colspan="2" rowspan="1">
                        @if($paciente->estadocivil == 2)
                        <span> X</span>
                        @endif
                    </td>
                    <td class="campos" colspan="2" rowspan="1">
                        @if($paciente->estadocivil == 4)
                        <span> X</span>
                        @endif
                    </td>
                    <td class="campos" colspan="2" rowspan="1">
                        @if($paciente->estadocivil == 3)
                        <span>X </span>
                        @endif
                    </td>
                    <td class="campos" colspan="2" rowspan="1">
                        @if($paciente->estadocivil == 5)
                        <span>X </span>
                        @endif
                    </td>
                    <td class="campos" colspan="2" rowspan="1">
                        @if($paciente->estadocivil == 6)
                        <span>X </span>
                        @endif
                    </td>
                    <td class="campos" colspan="2" rowspan="1">
                        @if(is_null($paciente->estadocivil))
                        <span>X </span>
                        @endif
                    </td>

                    <td class="campos" rowspan="1" colspan="5">
                        @if ($paciente->sexo == '1')
                        <span>M</span>
                        @elseif($paciente->sexo == '2')
                        <span> F</span>
                        @endif
                    </td>
                    <td class="campos" rowspan="1" colspan="12">{{ $paciente->telefono1 }}</td>
                    <td class="campos" rowspan="1" colspan="15">{{ $paciente->telefono2 }}</td>
                    <td class="campos" rowspan="1" colspan="19">{{ $paciente->fecha_nacimiento }}</td>
                </tr>

                <tr>
                    <td class="subtitulo2" rowspan="2" colspan="17">LUGAR DE NACIMIENTO</td>
                    <td class="subtitulo2" rowspan="2" colspan="16">NACIONALIDAD</td>
                    <td class="subtitulo2" rowspan="2" colspan="5">EDAD</td>
                    <td class="subtitulo2" rowspan="1" colspan="8">CONDICIÓN EDAD<br>(MARCAR)</td>
                    <td class="subtitulo2" rowspan="1" colspan="11">GRUPO PRIORITARIO</td>
                    <td class="subtitulo2" rowspan="1" colspan="2">SI</td>
                    <td class="campos" rowspan="1" colspan="2">@if($datos_pac->grupo_prioritario == 1) X @endif</td>
                    <td class="subtitulo2" rowspan="1" colspan="2">NO</td>
                    <td class="campos" rowspan="1" colspan="2">@if($datos_pac->grupo_prioritario == 0 or is_null($datos_pac->grupo_prioritario)) X @endif</td>
                </tr>
                <tr>
                    <td class="subtitulo2" rowspan="1" colspan="2">H</td>
                    <td class="subtitulo2" rowspan="1" colspan="2">D</td>
                    <td class="subtitulo2" rowspan="1" colspan="2">M</td>
                    <td class="subtitulo2" rowspan="1" colspan="2">A</td>
                    <td class="subtitulo2" rowspan="1" colspan="6">ESPECIFIQUE</td>
                    <td class="campos" rowspan="1" colspan="13">{{$datos_pac->especifique_grupo}}</td>
                </tr>
                <tr>
                    <td class="campos" rowspan="1" colspan="17">{{$paciente->lugar_nacimiento}}</td>
                    <td class="campos" rowspan="1" colspan="16">{{ $datos_pac->nacionalidad }}</td>
                    <td class="campos" rowspan="1" colspan="5">{{ $datos_pac->edad }}</td>
                    <td class="campos" rowspan="1" colspan="2"></td>
                    <td class="campos" rowspan="1" colspan="2"></td>
                    <td class="campos" rowspan="1" colspan="2"></td>
                    <td class="campos" rowspan="1" colspan="2"></td>
                    <td class="campos" rowspan="1" colspan="19"></td>
                </tr>

                <tr class="subtitulo">
                    <td class="subtitulo2" colspan="14">AUTOIDENTIFICACIÓN ETNICA</td>
                    <td class="subtitulo2" colspan="19">NACIONALIDAD ÉTNICA</td>
                    <td class="subtitulo2" colspan="16">PUEBLOS</td>
                    <td class="subtitulo2" colspan="16">NIVEL DE EDUCACIÓN</td>
                </tr>
                <tr height="30px">
                    <td class="campos" rowspan="1" colspan="14">{{$datos_pac->autoidentificacion_etnica}}</td>
                    <td class="campos" rowspan="1" colspan="19">{{$datos_pac->nacionalidad_etnica}}</td>
                    <td class="campos" rowspan="1" colspan="16">{{$datos_pac->pueblos}}</td>
                    <td class="campos" rowspan="1" colspan="16">
                        {{ $datos_pac->nivel_educacion }}
                    </td>
                </tr>
                <tr class="subtitulo">
                    <td class="subtitulo2" rowspan="2" colspan="15">ESTADO DEL NIVEL DE EDUCACIÓN</td>
                    <td class="subtitulo2" rowspan="2" colspan="15">TIPO DE EMPRESA DE TRABAJO</td>
                    <td class="subtitulo2" rowspan="2" colspan="17">OCUPACIÓN/PROFESIÓN</td>
                    <td class="subtitulo2" rowspan="1" colspan="18">SEGURO SALUD PRINCIPAL</td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="3">IESS</td>
                    <td class="subtitulo2" colspan="3">IESS-C</td>
                    <td class="subtitulo2" colspan="3">ISSPOL</td>
                    <td class="subtitulo2" colspan="3">ISSFA</td>
                    <td class="subtitulo2" colspan="3">PRIV.</td>
                    <td class="subtitulo2" colspan="3">NING.</td>
                </tr>
                <tr>
                    <td class="campos" colspan="15">{{$paciente->ho_datos_paciente->estado_nivel_educacion}}</td>
                    <td class="campos" style="height:8px" colspan="15">{{ $paciente->ho_datos_paciente->empresa_trabajo }}</td>
                    <td class="campos" colspan="17">{{ $paciente->ocupacion }}</td>
                    <td class="campos" colspan="3">@if ($solicitudemer->seguro->nombre == 'IESS') X @endif</td>
                    <td class="campos" colspan="3">@if ($solicitudemer->seguro->nombre == 'IESS-C') X @endif</td>
                    <td class="campos" colspan="3">@if ($solicitudemer->seguro->nombre == 'ISSPOL') X @endif</td>
                    <td class="campos" colspan="3">@if ($solicitudemer->seguro->nombre == 'ISSFA') X @endif</td>
                    <td class="campos" colspan="3">@if ($solicitudemer->seguro->nombre != 'IESS' && $solicitudemer->seguro->nombre != 'IESS-C' && $solicitudemer->seguro->nombre != 'ISSPOL' && $solicitudemer->seguro->nombre != 'ISSFA' && $solicitudemer->seguro->nombre != 'PARTICULAR' ) X @endif</td>
                    <td class="campos" colspan="3">@if ($solicitudemer->seguro->nombre == 'PARTICULAR') X @endif</td>

                </tr>
                <tr>
                    <td rowspan="4" colspan="6" class="subtitulo3">RESIDENCIA</th>
                    <td class="subtitulo2" rowspan="1" colspan="16">PROVINCIA</td>
                    <td class="subtitulo2" rowspan="1" colspan="12">CANTÓN</td>
                    <td class="subtitulo2" rowspan="1" colspan="11">PARROQUIA</td>
                    <td class="subtitulo2" rowspan="1" colspan="20">BARRIO O SECTOR</td>
                </tr>
                <tr>
                    <td class="campos" style="height:8px" rowspan="1" colspan="16">{{ $paciente->ho_datos_paciente->provincia }}</td>
                    <td class="campos" rowspan="1" colspan="12">{{ $paciente->ho_datos_paciente->canton }}</td>
                    <td class="campos" rowspan="1" colspan="11">{{ $paciente->ho_datos_paciente->parroquia }} </td>
                    <td class="campos" rowspan="1" colspan="20">{{ $paciente->ho_datos_paciente->barrio }} </td>
                </tr>
                <tr>
                    <td class="subtitulo2" rowspan="1" colspan="19">CALLE PRINCIPAL</td>
                    <td class="subtitulo2" rowspan="1" colspan="19">CALLE SECUNDARIA</td>
                    <td class="subtitulo2" rowspan="1" colspan="21">REFERENCIA</td>
                </tr>
                <tr>
                    <td class="campos" rowspan="1" colspan="19" style="height:8px">{{$datos_pac->calle_principal}}</td>
                    <td class="campos" rowspan="1" colspan="19">{{$datos_pac->calle_secundaria}}</td>
                    <td class="campos" rowspan="1" colspan="21">{{$datos_pac->referencia}}</td>
                </tr>

                <tr>
                    <td class="subtitulo2" rowspan="1" colspan="21">EN CASO NECESARIO LLAMAR A:</td>
                    <td class="subtitulo2" rowspan="1" colspan="9">PARENTESCO</td>
                    <td class="subtitulo2" rowspan="1" colspan="23">DIRECCIÓN</td>
                    <td class="subtitulo2" rowspan="1" colspan="12">N° TELÉFONO</td>
                </tr>
                <tr>
                    <td class="campos" style="height:8px" rowspan="1" colspan="21">{{ $paciente->ho_datos_paciente->llamar_a }}</td>
                    <td class="campos" rowspan="1" colspan="9">{{$paciente->ho_datos_paciente->parentesco_afinidad}}</td>
                    <td class="campos" rowspan="1" colspan="23">{{ $paciente->ho_datos_paciente->direccion_familiar }}
                    </td>
                    <td class="campos" rowspan="1" colspan="12">
                        {{ $paciente->ho_datos_paciente->telefono_inst_per_paci }}
                    </td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="23">FORMA DE LLEGADA</td>
                    <td class="subtitulo2" colspan="13">FUENTES DE INFORMACIÒN</td>
                    <td class="subtitulo2" colspan="18">INSTUCIÓN O PERSONA QUE ENTREGA AL PACIENTE</td>
                    <td class="subtitulo2" colspan="11">N° TELÉFONO</td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="6">AMBULATORIO</td>
                    <td class="campos" colspan="2">
                        @if ($paciente->ho_datos_paciente->forma_llegada == 'Ambulatorio')
                        <span>X</span>
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6">AMBULANCIA</td>
                    <td class="campos" colspan="2">
                        @if($paciente->ho_datos_paciente->forma_llegada == 'Ambulancia')
                        <span>X</span>
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="5">OTRO TRANSPORTE</td>
                    <td class="campos" colspan="2">
                        @if($paciente->ho_datos_paciente->forma_llegada == 'Otro')
                        <span> X </span>
                        @endif
                    </td>
                    <td class="campos" colspan="13">{{$paciente->ho_datos_paciente->fuente_informacion}}</td>
                    <td class="campos" colspan="18">{{$paciente->ho_datos_paciente->inst_per_paci_entrega}}</td>
                    <td class="campos" colspan="11">{{ $paciente->telefono3 }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- TABLA C --}}
    <div style="margin-top: 10px">
        <table class="tabla1" cellspacing="0">
            <thead>
                <tr class="titulo">
                    <th colspan="65">
                        C. INICIO DE ATENCIÓN
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    {{-- Modificacion entre fecha y hora en inicio de atencion --}}
                    <td class="subtitulo2" colspan="7">FECHA<br>(aaaa-mm-dd)</td>
                    @php $fecha_1 = date_format(new DateTime($solicitudemer->fecha_ingreso), 'Y-m-d') @endphp
                    <td class="campos" colspan="10">
                        @if ($solicitudemer->fecha_ingreso == null)
                        {{ $fecha_dia }}
                        @else
                        {{ $fecha_1 }}
                        @endif
                    </td>
                    @php $hora = date_format(new DateTime($solicitudemer->fecha_ingreso), 'H:i') @endphp
                    <td class="subtitulo2" colspan="5">HORA<br>(hh:mm)</td>
                    <td class="campos" colspan="8">
                        @if ($solicitudemer->fecha_ingreso == null)
                        {{ $fecha_hora }}@else{{ $hora }}
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="8"><b>CONDICIÓN DE LLEGADA</b></td>
                    <td class="subtitulo2" colspan="5">ESTABLE</td>
                    <td class="campos" colspan="2">@if($form008->condicion == 1) X @endif</td>
                    <td class="subtitulo2" colspan="7">INESTABLE</td>
                    <td class="campos" colspan="2">@if($form008->condicion == 2) X @endif</td>
                    <td class="subtitulo2" colspan="9">FALLECIDO</td>
                    <td class="campos" colspan="2">@if($form008->condicion == 3) X @endif</td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="7">MOTIVO DE <br> ATENCIÓN</td>
                    <td class="campos" colspan="58">{{$form008->motivo}}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- TABLA D --}}
    <div style="margin-top: 10px">
        <table class="tabla1" cellspacing="0">
            <thead>
                <tr class="titulo">
                    <th colspan="65">
                        D. ACCIDENTE, VIOLENCIA, INTOXICACIÓN
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="subtitulo">
                    <td class="subtitulo2" colspan="7">FECHA<br>(aaaa-mm-dd)</td>
                    <td class="subtitulo2" colspan="7">HORA <br>(hh:mm)</td>
                    <td class="subtitulo2" colspan="16">LUGAR DEL EVENTO</td>
                    <td class="subtitulo2" colspan="27">DIRECCIÓN DEL EVENTO</td>
                    <td class="subtitulo2" colspan="8">CUSTODIA POLICIAL</td>
                </tr>
                <tr>
                    {{-- Modificacion de separar entre la fecha y la hora de la fecha del evento/accidente --}}
                    @php $fecha_tablad_dia = date_format(new DateTime($form008->fecha_evento), 'Y-m-d') @endphp
                    <td class="campos" colspan="7" rowspan="1">
                        @if ($form008->fecha_evento == null)
                        {{ $fecha_dia }}
                        @else
                        {{ $fecha_tablad_dia }}
                        @endif
                    </td>
                    @php $fecha_tablad_hora = date_format(new DateTime($form008->fecha_evento), 'H:i') @endphp
                    <td class="campos" colspan="7" rowspan="1">
                        @if ($form008->fecha_evento == null)
                        {{ $fecha_hora }}
                        @else
                        {{ $fecha_tablad_hora }}
                        @endif
                    </td>
                    <td class="campos" colspan="16" rowspan="1">{{ $form008->lugar_evento }}</td>
                    <td class="campos" colspan="27" rowspan="1">{{ $form008->direccion_evento }}</td>
                    <td class="subtitulo2" colspan="2" rowspan="1">SI</td>
                    <td class="campos" colspan="2" rowspan="1">
                        @if ($form008->custodia_policial == 1)
                        <span>X</span>
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="2" rowspan="1">NO</td>
                    <td class="campos" colspan="2" rowspan="1">
                        @if ($form008->custodia_policial == 0)
                        <span>X</span>
                        @endif
                    </td>

                </tr>
                <tr>
                    <td class="subtitulo2" colspan="6">ACCIDENTE DE<br>TRANSITO</td>
                    <td class="campos" colspan="2">
                        @if($form008->accidente_transito == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6">CAÍDA</td>
                    <td class="campos" colspan="2">
                        @if($form008->caida == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6">QUEMADURA</td>
                    <td class="campos" colspan="2">
                        @if($form008->quemadura == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6">MORDEDURA</td>
                    <td class="campos" colspan="2">
                        @if($form008->mordedura == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6">AHOGAMIENTO</td>
                    <td class="campos" colspan="2">
                        @if($form008->ahogamiento == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6">CUERPO EXTRAÑO</td>
                    <td class="campos" colspan="2">
                        @if($form008->cuerpo_extrano == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="7">APLASTAMIENTO</td>
                    <td class="campos" colspan="2">
                        @if($form008->aplastamiento == 1)
                        X
                        @endif
                    <td class="subtitulo2" colspan="6">OTRO ACCIDENTE </td>
                    <td class="campos" colspan="2">
                        @if($form008->otro_accidente == 1)
                        X
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="6" rowspan="2">VIOLENCIA POR ARMA DE FUEGO</td>
                    <td class="campos" colspan="2" rowspan="2">
                        @if($form008->violencia_armf == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6" rowspan="2">VIOLENCIA POR ARMA C. PUNZANTE</td>
                    <td class="campos" colspan="2" rowspan="2">
                        @if($form008->violencia_armcp == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6" rowspan="2">VIOLENCIA POR RINA</td>
                    <td class="campos" colspan="2" rowspan="2">
                        @if($form008->violencia_rina == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6" rowspan="2">VIOLENCIA FAMILIAR</td>
                    <td class="campos" colspan="2" rowspan="2">
                        @if($form008->violencia_familiar == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6" rowspan="2">PRESUNTA VIOLENCIA FISICA</td>
                    <td class="campos" colspan="2" rowspan="2">
                        @if($form008->abuso_fisico == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6" rowspan="2">PRESUNTA VIOLENCIA PSICOLÓGICA </td>
                    <td class="campos" colspan="2" rowspan="2">
                        @if($form008->abuso_psicologico == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="7" rowspan="2">PRESUNTA VIOLENCIA SEXUAL </td>
                    <td class="campos" colspan="2" rowspan="2">
                        @if($form008->abuso_sexual == 1)
                        X
                        @endif
                    </td>

                    <td class="subtitulo2" rowspan="1" colspan="8">NOTIFICACIÓN </td>
                </tr>

                <tr>
                    <td class="subtitulo2" colspan="2" rowspan="1">SI</td>
                    <td class="campos" colspan="2" rowspan="1">
                        @if($form008->acc_notificacion=='1') X @endif
                    <td class="subtitulo2" colspan="2" rowspan="1">NO</td>
                    <td class="campos" colspan="2" rowspan="1">
                        @if($form008->acc_notificacion=='0') X @endif
                    </td>
                </tr>

                <tr>
                    <td class="subtitulo2" colspan="6">INTOXICACIÓN ALCOHOLICA</td>
                    <td class="campos" colspan="2">
                        @if($form008->intoxicacion_alcoholica == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6">INTOXICACIÓN ALIMENTARIA</td>
                    <td class="campos" colspan="2">
                        @if($form008->intoxicacion_alimentaria == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6">INTOXICACIÓN POR DROGAS</td>
                    <td class="campos" colspan="2">
                        @if($form008->intoxicacion_drogas == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6">INHALACIÓN DE GASES</td>
                    <td class="campos" colspan="2">
                        @if($form008->intoxicacion_gases == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6">OTRA INTOXICACIÓN</td>
                    <td class="campos" colspan="2">
                        @if($form008->otra_intoxicacion == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="6">PICADURA</td>
                    <td class="campos" colspan="2">
                        @if($form008->picadura == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="7">ENVENENAMIENTO</td>
                    <td class="campos" colspan="2">
                        @if($form008->envenenamiento == 1)
                        X
                        @endif
                    </td>

                    <td class="subtitulo2" colspan="6">ANAFILAXIA </td>
                    <td class="campos" colspan="2">
                        @if($form008->anafilaxia == 1)
                        X
                        @endif
                    </td>
                </tr>

                <tr>
                    <td class="subtitulo2" colspan="8">OBSERVACIONES </td>
                    <td class="campos" style="height:10px" colspan="57">{{$form008->observacion_p3}}</td>
                </tr>
                <tr>
                    <td class="campos" colspan="54"></td>
                    <td class="subtitulo2" colspan="9">SUGESTIVO DE ALIENTO ALCOHÓLICO</td>
                    <td class="campos" colspan="2">
                        @if ($form008->aliento_etilico == '1')
                        X
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- tabla E --}}
    <div style="margin-top: 10px">

        <table class="tabla1" cellspacing="0">
            <thead>
                <tr>
                    <th class="tituloc2" colspan="43">
                        E. ANTECEDENTES PATOLÓGICOS PERSONALES Y FAMILIARES
                    </th>
                    <td colspan="17" class="tituloc1">MARCAR CON X EL NUMERO CORRESPONDIENTE Y DESCRIBIR SEÑALANDO EL NUMERO</td>
                    <td class="tituloc" colspan="3">NO<br>APLICA</td>
                    <td colspan="2" class="campos">
                        @if (($alergias->isEmpty()) && (is_null($datos_pac->ginecologico) && is_null($datos_pac->ginecologico) ))
                        X
                        @endif
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="subtitulo2" colspan="9">1.ALÉRGICOS</td>
                    <td class="campos" colspan="2">
                        @if (!($alergias->isEmpty()))
                        X
                        @endif

                    </td>
                    <td class="subtitulo2" colspan="10">3.GINECOLÓGICOS</td>
                    <td class="campos" colspan="2">
                        @if (!(is_null($datos_pac->ginecologico)))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="12">5.PEDIÁTRICOS</td>
                    <td class="campos" colspan="2">
                        @if (!(is_null($datos_pac->antecedentes_pediatricos)))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="13">7.FARMACOLÓGICOS</td>
                    <td class="campos" colspan="2">
                        @if (!(is_null($datos_pac->farmacologico)))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="11">9.FAMILIARES</td>
                    <td class="campos" colspan="2">
                        @if (!(is_null($paciente->antecedentes_fam)))
                        X
                        @endif
                    </td>
                </tr>

                <tr>
                    <td class="subtitulo2" colspan="9">2.CLÍNICOS</td>
                    <td class="campos" colspan="2">
                        @if (!(is_null($datos_pac->clinico)))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="10">4.TRAUMATOLÓGICOS</td>
                    <td class="campos" colspan="2">
                        @if (!(is_null($datos_pac->traumatologico)))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="12">6.QUIRÚRGICOS</td>
                    <td class="campos" colspan="2">
                        @if (!(is_null($paciente->antecedentes_quir)))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="13">8.HÁBITOS</td>
                    <td class="campos" colspan="2">
                        @if (!(is_null($datos_pac->habitos)))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="11">10.OTROS</td>
                    <td class="campos" colspan="2">
                        @if (!(is_null($datos_pac->otros_antecedente)))
                        X
                        @endif
                    </td>
                </tr>
                @if (!($alergias->isEmpty()))
                <tr>
                    <td class="campos" height="10px" colspan="65">
                        Alergias:
                        @foreach($alergias as $alergia)
                        1. {{$alergia->principio_activo->nombre." "}}
                        @endforeach
                    </td>
                </tr>
                @endif
                @if (!(is_null($datos_pac->clinico)))
                <tr>
                    <td class="campos" height="10px" colspan="65">
                        2. {{$datos_pac->clinico}}
                    </td>
                </tr>
                @endif
                @if (!(is_null($datos_pac->ginecologico)))
                <tr>
                    <td class="campos" height="10px" colspan="65">
                        3. {{$datos_pac->ginecologico}}
                    </td>
                </tr>
                @endif
                @if (!(is_null($datos_pac->traumatologico)))
                <tr>
                    <td class="campos" height="10px" colspan="65">
                        4. {{$datos_pac->traumatologico}}
                    </td>
                </tr>
                @endif
                @if (!(is_null($datos_pac->antecedentes_pediatricos)))
                <tr>
                    <td class="campos" height="10px" colspan="65">
                        5. {{$datos_pac->antecedentes_pediatricos}}
                    </td>
                </tr>
                @endif
                @if (!(is_null($paciente->antecedentes_quir)))
                <tr>
                    <td class="campos" height="10px" colspan="65">
                        6. {{$paciente->antecedentes_quir}}
                    </td>
                </tr>
                @endif
                @if (!(is_null($datos_pac->farmacologico)))
                <tr>
                    <td class="campos" height="10px" colspan="65">
                        7. {{$datos_pac->farmacologico}}
                    </td>
                </tr>
                @endif
                @if (!(is_null($datos_pac->habitos)))
                <tr>
                    <td class="campos" height="10px" colspan="65">
                        8. {{$datos_pac->habitos}}
                    </td>
                </tr>
                @endif
                @if (!(is_null($paciente->antecedentes_fam)))
                <tr>
                    <td class="campos" height="10px" colspan="65">
                        9. {{$paciente->antecedentes_fam}}
                    </td>
                </tr>
                @endif
                @if (!(is_null($datos_pac->otros_antecedente)))
                <tr>
                    <td class="campos" height="10px" colspan="65">
                        10. {{$datos_pac->otros_antecedente}}
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- tabla F --}}
    <div style="margin-top: 10px">
        <table class="tabla1" cellspacing="0">
            <thead>

                <tr class="titulo" rowspan="3">
                    <th colspan="39">
                        F. ENFERMEDAD O PROBLEMA ACTUAL
                    </th>
                    <th colspan="26">CRONOLOGIA-LOCALIZACION-CARACTERISTICAS-INTENSIDAD-FRECUENCIA-FACTORES
                        AGRAVANTES</th>
                </tr>
            </thead>
            <tbody>
            @if(!is_null($form008->observacion_quintop))
                <tr>
                    <td class="campos" style="text-align: left" colspan="65">
                        {{$form008->observacion_quintop}}
                    </td>
                </tr>
                @else
                <tr>
                    <td class="campos" height=10px colspan="65"></td>
                </tr>
                <tr>
                    <td class="campos" height=10px colspan="65"></td>
                </tr>
                <tr>
                    <td class="campos" height=10px colspan="65"></td>
                </tr>
            @endif

            </tbody>
        </table>
    </div>
    <div class="footer">
        <table cellspacing="0" border="none">
            <tr>
                <td>SNS-MSP/HCU-form.008/2021</td>
                <td style="text-align:right">EMERGENCIA (1)</td>
            </tr>
        </table>
    </div>
    <div style="page-break-after: always;"></div>

    {{-- TABLA G --}}
    <div style="margin-top: 10px">
        <table class="tabla1" rowspan="2" cellspacing="0">
            <thead>
                <tr class="titulo">
                    <th colspan="65">
                        G. CONSTANTES VITALES Y ANTROPOMETRIA
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="11" class="subtitulo2">SIN CONSTANTES VITALES</td>
                    <td colspan="3" class="campos">
                        @if($form008->sin_constantes !== null){{$form008->sin_constantes}} @endif
                    </td>
                    <td colspan="8" class="subtitulo2">PRESIÓN <br> ARTERIAL(mmHg)</td>
                    <td colspan="9" class="campos">
                        @if($hc->presion==0)@if($triaje != null) {{$triaje->presion_sistolica}} / {{$triaje->presion_diastolica}} @endif @else{{$hc->presion}}@endif
                    </td>
                    <td colspan="9" class="subtitulo2">PULSO/min</td>
                    <td colspan="6" class="campos">
                        @if($hc->pulso==null)@if($triaje != null){{$triaje->frec_cardiaca}} @endif @else {{$hc->pulso}} @endif
                    </td>
                    <td colspan="12" class="subtitulo2">FRECUENCIA RESPIRATORIA/min</td>
                    <td colspan="7" class="campos">
                        @if($form008->frec_respiratoria == null)@if($triaje != null){{$triaje->frec_resp}}@endif @else{{$form008->frec_respiratoria}} @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="10" class="subtitulo2">PULSIOXIMETRIA(%)</td>
                    <td colspan="4" class="campos">
                        @if($form008->pulsioximetria !== null){{$form008->pulsioximetria}} @endif
                    </td>
                    <td colspan="8" class="subtitulo2">PERÍMETRO CEFALICO(cm)</td>
                    <td colspan="9" class="campos">
                        @if($form008->perimetro_cefalico !== null){{$form008->perimetro_cefalico}} @endif
                    </td>
                    <td colspan="4" class="subtitulo2">PESO(kg)</td>
                    <td colspan="5" class="campos">
                        @if($hc->peso == 0)@if($triaje != null){{$triaje->peso}}@endif @else{{$hc->peso}} @endif
                    </td>
                    <td colspan="3" class="subtitulo2">TALLA<br>(m)</td>
                    <td colspan="8" class="campos">
                        @if($hc->altura == 0)@if($triaje != null){{$triaje->talla}}@endif @else{{ $hc->altura}}@endif
                    </td>
                    <td colspan="7" class="subtitulo2">GLICEMIA CAPILAR(mg/dl)</td>
                    <td colspan="7" class="campos">
                        @if($form008->glicemia_capilar !== null){{$form008->glicemia_capilar}} @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="7" class="subtitulo2">GLASGOW INICIAL</td>
                    <td colspan="4" class="subtitulo2">OCULAR(4)</td>
                    <td colspan="6" class="campos">
                        {{$form008->ocular}}
                    </td>
                    <td colspan="4" class="subtitulo2">VERBAL(5)</td>
                    <td colspan="6" class="campos">
                        {{$form008->verbal}}
                    </td>
                    <td colspan="4" class="subtitulo2">MOTORA(6)</td>
                    <td colspan="6" class="campos">
                        {{$form008->motora}}
                    </td>
                    <td colspan="5" class="subtitulo2">REACCIÓN<br>PUPILA DER</td>
                    <td colspan="3" class="campos">
                        {{$form008->reac_pupila_der}}
                    </td>
                    <td colspan="5" class="subtitulo2">REACCIÓN<br>PUPILA IZQ</td>
                    <td colspan="3" class="campos">
                        {{$form008->reac_pupila_izq}}
                    </td>
                    <td colspan="5" class="subtitulo2">T.LLENADO<br>CAPILAR</td>
                    <td colspan="7" class="campos">
                        @if($form008->t_llenado_capilar == null)@if($triaje != null) {{$triaje->llenado_capilar}}@endif @else {{$form008->t_llenado_capilar}} @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    {{-- TABLA H --}}
    <div style="margin-top: 10px">
        <table class="tabla1" cellspacing="0">
            <thead>
                <tr class="titulo">
                    <th colspan="46">H. EXAMEN FÍSICO</th>
                    <th class="tituloc3" colspan="19">MARCAR "X" CUANDO PRESENTE PATOLOGÍA Y DESCRIBA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="subtitulo2" colspan="2">1.</td>
                    <td class="subtitulo2" colspan="8">PIEL-FANERAS</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->piel_faneras))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="2">4.</td>
                    <td class="subtitulo2" colspan="8">OÍDOS</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->oidos))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="2">7.</td>
                    <td class="subtitulo2" colspan="9">ORO FARINGE</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->oro_faringe))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="2">10.</td>
                    <td class="subtitulo2" colspan="9">TÓRAX</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->torax))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="2">13.</td>
                    <td class="subtitulo2" colspan="11">INGLE-PERINÉ</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->ingle_perine))
                        X
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="2">2.</td>
                    <td class="subtitulo2" colspan="8">CABEZA</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->cabeza))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="2">5.</td>
                    <td class="subtitulo2" colspan="8">NARIZ</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->nariz))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="2">8.</td>
                    <td class="subtitulo2" colspan="9">CUELLO</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->cuello))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="2">11.</td>
                    <td class="subtitulo2" colspan="9">ABDOMEN</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->abdomen))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="2">14.</td>
                    <td class="subtitulo2" colspan="11">MIEMBROS SUPERIORES</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->miembros_superiores))
                        X
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="2">3.</td>
                    <td class="subtitulo2" colspan="8">OJOS</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->ojos))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="2">6.</td>
                    <td class="subtitulo2" colspan="8">BOCA</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->boca))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="2">9.</td>
                    <td class="subtitulo2" colspan="9">AXILAS-MAMAS</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->axilas_mamas))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="2">12.</td>
                    <td class="subtitulo2" colspan="9">COLUMNA VERTEBRAL</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->columna))
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="2">15.</td>
                    <td class="subtitulo2" colspan="11">MIEMBROS INFERIORES</td>
                    <td class="campos" colspan="2">
                        @if(!is_null($form008->miembros_inferiores))
                        X
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="campos" height=10px colspan="65">
                        @if(!is_null($form008->piel_faneras)) 1. {{$form008->piel_faneras}} <br> @endif
                        @if(!is_null($form008->cabeza)) 2. {{$form008->cabeza}} <br> @endif
                        @if(!is_null($form008->ojos)) 3. {{$form008->ojos}} <br> @endif
                        @if(!is_null($form008->oidos)) 4. {{$form008->oidos}} <br> @endif
                        @if(!is_null($form008->nariz)) 5. {{$form008->nariz}} <br> @endif
                        @if(!is_null($form008->boca)) 6. {{$form008->boca}} <br> @endif
                        @if(!is_null($form008->oro_faringe)) 7. {{$form008->oro_faringe}} <br> @endif
                        @if(!is_null($form008->cuello)) 8. {{$form008->cuello}} <br> @endif
                        @if(!is_null($form008->axilas_mamas)) 9. {{$form008->axilas_mamas}} <br> @endif
                        @if(!is_null($form008->torax)) 10. {{$form008->torax}} <br> @endif
                        @if(!is_null($form008->abdomen)) 11. {{$form008->abdomen}} <br> @endif
                        @if(!is_null($form008->columna)) 12. {{$form008->columna}} <br> @endif
                        @if(!is_null($form008->ingle_perine)) 13. {{$form008->ingle_perine}} <br> @endif
                        @if(!is_null($form008->miembros_superiores)) 14. {{$form008->miembros_superiores}} <br> @endif
                        @if(!is_null($form008->miembros_inferiores)) 15. {{$form008->miembros_inferiores}} <br> @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    {{-- TABLA I --}}
    <div style="margin-top: 10px">
        <table class="tabla1" cellspacing="0">
            <thead>
                <tr class="titulo">
                    <th colspan="65">
                        I. EXAMEN FÍSICO DE TRAUMA / CRÍTICO
                    </th>
                </tr>
            </thead>
            <tbody>
                @if(is_null($form008->examen_fisico_trauma))
                <tr>
                    <td class="campos" height=10px colspan="65">{{$form008->examen_fisico_obs}}</td>
                </tr>
                @else
                <tr>
                    <td class="campos" height=10px colspan="65"></td>
                </tr>
                <tr>
                    <td class="campos" height=10px colspan="65"></td>
                </tr>
                <tr>
                    <td class="campos" height=10px colspan="65"></td>
                </tr>
                
                @endif
            </tbody>
        </table>
    </div>
    {{-- TABLA J --}}
    <div style="margin-top: 10px">
        <table class="tabla1" cellspacing="0">
            <thead>
                <tr>
                    <th class="tituloc2" colspan="54">J. EMBARAZO - PARTO</th>
                    <td class="tituloc" colspan="9">NO APLICA</td>
                    <td class="campos" colspan="2">
                        @if ($paciente->sexo == '1')
                        X
                        @endif
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="subtitulo2" colspan="5">NUMERO GESTAS</td>
                    <td class="campos" colspan="4">
                        {{$form008->gestas}}
                    </td>
                    <td class="subtitulo2" colspan="5">NUMERO PARTOS</td>
                    <td class="campos" colspan="4">
                        {{$form008->partos}}
                    </td>
                    <td class="subtitulo2" colspan="4">NUMERO ABORTOS</td>
                    <td class="campos" colspan="4">
                        {{$form008->abortos}}
                    </td>
                    <td class="subtitulo2" colspan="5">NUMERO CESAREAS</td>
                    <td class="campos" colspan="4">
                        {{$form008->cesareas}}
                    </td>
                    <td class="subtitulo2" colspan="5">FUM</td>
                    <td class="campos" colspan="4">
                        {{$form008->ultima_menstruacion}}
                    </td>
                    <td class="subtitulo2" colspan="6">SEMANAS <br> GESTACIÓN</td>
                    <td class="campos" colspan="4">
                        {{$form008->semanas_gestacion}}
                    </td>

                    <td class="subtitulo2" colspan="7">MOVIMIENTO FETAL</td>
                    <td class="campos" colspan="4">
                        @if($form008->movimiento_fetal == 1)
                        X
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="9">FRECUENCIA CARDIACA FETAL</td>
                    <td class="campos" colspan="5">
                        {{$form008->frecuencia_fetal}}
                    </td>
                    <td class="subtitulo2" colspan="8">RUPTURA DE MEMBRANAS</td>
                    <td class="campos" colspan="4">
                        @if($form008->membranas_rotas == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="5">TIEMPO</td>
                    <td class="campos" colspan="4">
                        {{$form008->tiempo_ruptura}}

                    </td>
                    <td class="subtitulo2" colspan="5">AFU</td>
                    <td class="campos" colspan="4">
                        {{$form008->altura_uterina}}
                    </td>
                    <td class="subtitulo2" colspan="7">PRESENTACIÓN</td>
                    <td class="campos" colspan="14">
                        {{$form008->presentacion}}
                    </td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="9">DILATACIÓN</td>
                    <td class="campos" colspan="5">
                        {{$form008->dilatacion}}
                    </td>
                    <td class="subtitulo2" colspan="8">BORRAMIENTO</td>
                    <td class="campos" colspan="4">
                        {{$form008->borramiento}}
                    </td>
                    <td class="subtitulo2" colspan="5">PLANO</td>
                    <td class="campos" colspan="4">
                        {{$form008->plano}}
                    </td>
                    <td class="subtitulo2" colspan="5">PELVIS VIABLE</td>
                    <td class="campos" colspan="4">
                        @if($form008->pelvis_util == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="5">SANGRADO VAGINAL</td>
                    <td class="campos" colspan="4">
                        @if($form008->sangrado_vaginal == 1)
                        X
                        @endif
                    </td>
                    <td class="subtitulo2" colspan="8">CONTRACCIONES</td>
                    <td class="campos" colspan="4">
                        {{$form008->contracciones}}
                    </td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="9">SCORE MAMÁ</td>
                    <td class="campos" colspan="56">{{$form008->score_mama}}</td>
                </tr>
                <tr>
                    <td class="campos" height=10px colspan="65"></td>
                </tr>
                <tr>
                    <td class="campos" height=10px colspan="65"></td>
                </tr>
                <tr>
                    <td class="campos" height=10px colspan="65"></td>
                </tr>


            </tbody>
        </table>
    </div>
    {{-- TABLA K --}}
    <div style="margin-top: 10px">
        <table class="tabla1" cellspacing="0">
            <thead>
                <tr>
                    <th class="tituloc2" colspan="59">
                        K. EXÁMENES COMPLEMENTARIOS
                    </th>
                    <th class="tituloc" colspan="4">
                        NO APLICA
                    </th>
                    <th class="campos" colspan="2">

                    </th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="subtitulo2" colspan="6">1.BIOMETRÍA</td>
                    <td class="campos" colspan="2">@if( $form008->biometria) X @endif</td>
                    <td class="subtitulo2" colspan="6">3.QUÍMICA SANGUÍNEA</td>
                    <td class="campos" colspan="2">@if( $form008->quimica_sanguinea) X @endif</td>
                    <td class="subtitulo2" colspan="6">5.GASOMETRÍA</td>
                    <td class="campos" colspan="2">@if( $form008->gasometria) X @endif</td>
                    <td class="subtitulo2" colspan="6">7.ENDOSCOPIA</td>
                    <td class="campos" colspan="2">@if( $form008->endoscopia) X @endif</td>
                    <td class="subtitulo2" colspan="5">9.RX ABDOMEN</td>
                    <td class="campos" colspan="2">@if( $form008->rx_abdomen) X @endif</td>
                    <td class="subtitulo2" colspan="6">11.ECOGRAFÍA <br> ABDOMEN</td>
                    <td class="campos" colspan="2">@if( $form008->ecografia_abdomen) X @endif</td>
                    <td class="subtitulo2" colspan="7">13.TOMOGRAFÍA</td>
                    <td class="campos" colspan="2">@if( $form008->tomografia) X @endif</td>
                    <td class="subtitulo2" colspan="7">15.INTERCONSULTA</td>
                    <td class="campos" colspan="2">@if( $form008->interconsulta) X @endif</td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="6">2.UROÁNALISIS</td>
                    <td class="campos" colspan="2">@if($form008->uroanalisis)X @endif</td>
                    <td class="subtitulo2" colspan="6">4.ELECTROLITOS</td>
                    <td class="campos" colspan="2">@if($form008->electrolitos )X @endif</td>
                    <td class="subtitulo2" colspan="6">6.ELECTRO CARDIOGRAMA</td>
                    <td class="campos" colspan="2">@if($form008->electro_cardiograma )X @endif</td>
                    <td class="subtitulo2" colspan="6">8.RX TÓRAX</td>
                    <td class="campos" colspan="2">@if($form008->rx_torax )X @endif</td>
                    <td class="subtitulo2" colspan="5">10.RX OSEA</td>
                    <td class="campos" colspan="2">@if($form008->rx_osea )X @endif</td>
                    <td class="subtitulo2" colspan="6">12.ECOGRAFÍA PÉLVICA</td>
                    <td class="campos" colspan="2">@if($form008->ecografia_pelvica )X @endif</td>
                    <td class="subtitulo2" colspan="7">14.RESONANCIA</td>
                    <td class="campos" colspan="2">@if($form008->resonancia )X @endif</td>
                    <td class="subtitulo2" colspan="7">16.OTROS</td>
                    <td class="campos" colspan="2">@if($form008->otros )X @endif</td>
                </tr>
                @php $arr_obs_exm = explode ( '<br />', $form008->observacion_exm_comp ); $cont_obs_exm=1; @endphp
                @if(!is_null($form008->observacion_exm_comp))
                @foreach($arr_obs_exm as $item)
                @if($cont_obs_exm==1)
                <tr>
                    <td style="border:none" colspan="6">OBSERVACIONES:</td>
                    <td class="campos" colspan="59">{{$item}}</td>
                </tr>
                @else
                <tr>
                    <td class="campos" style="height: 10px" colspan="65">{{$item}}</td>
                </tr>
                @endif
                @php $cont_obs_exm++ @endphp
                @endforeach
                @else
                <tr>
                    <td style="border:none" colspan="6">OBSERVACIONES:</td>
                    <td class="campos" colspan="59"></td>
                </tr>

                <tr>
                    <td class="campos" style="height: 10px" colspan="65"></td>
                </tr>

                <tr>
                    <td class="campos" style="height: 10px" colspan="65"></td>
                </tr>

                <tr>
                    <td class="campos" style="height: 10px" colspan="65"></td>
                </tr>
                @endif


            </tbody>
        </table>
    </div>

    <div class="template">
        <div class="columna1">

            {{-- TABLA L --}}
            <table class="tabla1" cellspacing="0">
                <thead>
                    <tr>
                        <th class="titulodoble1" colspan="28">L. DIAGNÓSTICOS PRESUNTIVOS</th>
                        <th class="titulodoble2" colspan="4">CIE</th>
                    </tr>
                </thead>
                <tbody>
                    @if($hc_cie10_ingreso->isEmpty())
                    <tr>
                        <td class="subtitulo2" style="height: 10px" style="font-size: 8px" colspan="1">1</td>
                        <td class="campos1" style="height: 10px" style="font-size: 8px" colspan="27"></td>
                        <td class="campos1" style="height: 10px" style="font-size: 8px" colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="subtitulo2" style="height: 10px" style="font-size: 8px" colspan="1">2</td>
                        <td class="campos1" style="height: 10px" style="font-size: 8px" colspan="27"></td>
                        <td class="campos1" style="height: 10px" style="font-size: 8px" colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="subtitulo2" style="height: 10px" style="font-size: 8px" colspan="1">3</td>
                        <td class="campos1" style="height: 10px" style="font-size: 8px" colspan="27"></td>
                        <td class="campos1" style="height: 10px" style="font-size: 8px" colspan="4"></td>
                    </tr>
                    @endif
                    @php $cont1 = 1; @endphp
                    @foreach ($hc_cie10_ingreso as $cie)
                    @if($cie->presuntivo_definitivo=='PRESUNTIVO')
                    <tr>
                        <td class="subtitulo2" style="font-size: 8px" colspan="1">{{$cont1}}</td>
                        @if(strlen($cie->cie10)==4)
                        <td class="campos1" style="font-size: 8px" colspan="27">@if(isset($cie->cie_10_4)) {{ $cie->cie_10_4->descripcion}} @endif </td>
                        @elseif(strlen($cie->cie10)==3)
                        <td class="campos1" style="font-size: 8px" colspan="27">@if(isset($cie->cie_10_3)) {{ $cie->cie_10_3->descripcion}} @endif</td>
                        @endif
                        <td class="campos1" style="font-size: 8px" colspan="4">{{ $cie->cie10 }}</td>
                    </tr>
                    @php $cont1 ++; @endphp
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- TABLA M --}}
        <div class="columna2">

            <table class="tabla1" cellspacing="0">
                <thead>
                    <tr>
                        <th class="titulodoble1" colspan="28">M. DIAGNÓSTICOS DEFINITIVOS</th>
                        <th class="titulodoble2" colspan="4">CIE</th>
                    </tr>
                    @php $cont = 1; @endphp
                </thead>
                <tbody>
                    @if($hc_cie10_ingreso->isEmpty())
                    <tr>
                        <td class="subtitulo2" style="height: 10px" style="font-size: 8px" colspan="1">1</td>
                        <td class="campos1" style="height: 10px" style="font-size: 8px" colspan="27"></td>
                        <td class="campos1" style="height: 10px" style="font-size: 8px" colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="subtitulo2" style="height: 10px" style="font-size: 8px" colspan="1">2</td>
                        <td class="campos1" style="height: 10px" style="font-size: 8px" colspan="27"></td>
                        <td class="campos1" style="height: 10px" style="font-size: 8px" colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="subtitulo2" style="height: 10px" style="font-size: 8px" colspan="1">3</td>
                        <td class="campos1" style="height: 10px" style="font-size: 8px" colspan="27"></td>
                        <td class="campos1" style="height: 10px" style="font-size: 8px" colspan="4"></td>
                    </tr>
                    @endif
                    @php $cont2 = 1; @endphp
                    @foreach ($hc_cie10_ingreso as $cie)
                    @if($cie->presuntivo_definitivo=='DEFINITIVO')
                    <tr>
                        <td class="subtitulo2" style="font-size: 8px" colspan="1">{{$cont2}}</td>
                        @if(strlen($cie->cie10)==4)
                        <td class="campos1" style="font-size: 8px" colspan="27">@if(isset($cie->cie_10_4)) {{ $cie->cie_10_4->descripcion}} @endif</td>
                        @elseif(strlen($cie->cie10)==3)
                        <td class="campos1" style="font-size: 8px" colspan="27">@if(isset($cie->cie_10_3)) {{ $cie->cie_10_3->descripcion}} @endif</td>
                        @endif
                        <td class="campos1" style="font-size: 8px" colspan="4">{{ $cie->cie10 }}</td>
                    </tr>
                    @php $cont2 ++; @endphp
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
    {{-- TABLA N --}}
    <div style="margin-top: 10px">
        <table class="tabla1" cellspacing="0">
            <thead>
                <tr class="titulo">
                    <th class="tituloc2" colspan="65">
                        N. PLAN DE TRATAMIENTO
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th class="subtitulo2" colspan="33">MEDICAMENTOS</th>
                    <th class="subtitulo2" colspan="7">VIA</th>
                    <th class="subtitulo2" colspan="7">DOSIS</th>
                    <th class="subtitulo2" colspan="11">POSOLOGIA</th>
                    <th class="subtitulo2" colspan="7">DIAS</th>
                </tr>
                @if($detalles->isEmpty())
                <tr>
                    <td class="subtitulo2" colspan="1">1</td>
                    <td class="campos" colspan="32"></td>
                    <td class="campos" colspan="7"></td>
                    <td class="campos" colspan="7"></td>
                    <td class="campos" colspan="11"></td>
                    <td class="campos" colspan="7"></td>
                </tr>
                <tr>
                    <td colspan="65" class="campos" style="height: 10px"></td>
                </tr>

                @endif
                @php $j=1; @endphp
                @foreach ($detalles as $detalle)
                <tr>
                    <td class="subtitulo2" colspan="1">{{($j)}}</td>
                    <td class="campos" colspan="32">{{ $detalle->nombre }}</td>
                    <td class="campos" colspan="7">
                        <!-- dosis  -->
                    </td>
                    <td class="campos" colspan="7">{{$detalle->cantidad}}</td>
                    <td class="campos" colspan="11">{{$detalle->dosis}}</td>
                    <td class="campos" colspan="7">{{$detalle->dias}}</td>
                </tr>
                @php $j++; @endphp
                @endforeach
                @foreach ($detalles as $detalle)
                @if(!is_null($detalle->detalle_cantidad) || !is_null($detalle->detalle_cantidad) || !is_null($detalle->detalle_cantidad))
                <tr>
                    <td colspan="65" class="campos" style="height: 10px"> <b>{{ $detalle->nombre }}:</b>
                        @if(!is_null($detalle->detalle_cantidad)) detalle cantidad={{$detalle->detalle_cantidad}}; @endif
                        @if(!is_null($detalle->detalle_cantidad)) descargo={{$detalle->descargo}}; @endif
                        @if(!is_null($detalle->detalle_cantidad)) serie={{$detalle->serie}} @endif
                    </td>
                </tr>
                @endif
                @endforeach

            </tbody>
        </table>
    </div>

    {{-- TABLA O --}}
    <div style="margin-top: 10px">
        <table class="tabla1" cellspacing="0">
            <thead>
                <tr class="titulo">
                    <th colspan="65">
                        O. CONDICIÓN AL EGRESO DE EMERGENCIA
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="subtitulo2" colspan="8">VIVO</td>
                    <td class="campos" colspan="3">
                        @if(!is_null($verificacion)) @if($verificacion->seccion == 8) X @endif @endif
                    </td>

                    <td class="subtitulo2" colspan="6">ESTABLE</td>
                    <td class="campos" colspan="3">
                        @if(!is_null($verificacion)) @if($verificacion->seccion == 9) X @endif @endif
                    </td>
                    <td class="subtitulo2" colspan="6">INESTABLE</td>
                    <td class="campos" colspan="3">
                        @if(!is_null($verificacion)) @if($verificacion->seccion == 10) X @endif @endif
                    </td>
                    <td class="subtitulo2" colspan="6">FALLECIDO</td>
                    <td class="campos" colspan="3">
                        @if(!is_null($verificacion)) @if($verificacion->seccion == 11) X @endif @endif
                    </td>
                    <td class="subtitulo2" colspan="5">ALTA DEFINITIVA</td>
                    <td class="campos" colspan="3">
                        @if(!is_null($verificacion)) @if($verificacion->seccion == 1) X @endif @endif
                    </td>
                    <td class="subtitulo2" colspan="5">CONSULTA EXTERNA</td>
                    <td class="campos" colspan="3">
                        @if(!is_null($verificacion)) @if($verificacion->seccion == 2) X @endif @endif

                    </td>
                    <td class="subtitulo2" colspan="8">OBSERVACIÓN DE<br>EMERGENCIA</td>
                    <td class="campos" colspan="3">
                        @if(!is_null($verificacion)) @if($verificacion->seccion == 3) X @endif @endif

                    </td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="8">HOSPITALIZACIÓN</td>
                    <td class="campos" colspan="3">
                        @if(!is_null($verificacion)) @if($verificacion->seccion == 4) X @endif @endif

                    </td>
                    <td class="subtitulo2" colspan="6">REFERENCIA</td>
                    <td class="campos" colspan="3">
                        @if(!is_null($verificacion)) @if($verificacion->seccion == 5) X @endif @endif

                    </td>
                    <td class="subtitulo2" colspan="6">REFERENCIA INVERSA</td>
                    <td class="campos" colspan="3">
                        @if(!is_null($verificacion)) @if($verificacion->seccion == 6) X @endif @endif
                    </td>

                    <td class="subtitulo2" colspan="6">DERIVACIÓN</td>
                    <td class="campos" colspan="3">
                        @if(!is_null($verificacion)) @if($verificacion->seccion == 7) X @endif @endif
                    </td>

                    <td class="subtitulo2" colspan="8">ESTABLECIMIENTO</td>
                    <td class="campos" colspan="19">
                        @if (!empty($verificacion->establecimiento))
                        {{$verificacion->establecimiento}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="subtitulo2" rowspan="2" colspan="9">OBSERVACIONES
                    </td>
                    <td class="campos" rowspan="2" colspan="51">
                        @if(empty($verificacion)) @else {{$verificacion->observaciones}} @endif
                    </td>
                    <td class="subtitulo2" colspan="5">DÍAS DE<br>REPOSO</td>
                </tr>
                <tr>
                    <td class="campos" height="10px" colspan="5">
                        @if(empty($verificacion->dias_reposo)) @else {{$verificacion->dias_reposo}}@endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- TABLA P --}}
    <div style="margin-top: 10px">
        <table class="tabla1" cellspacing="0">
            <thead>
                @php

                @endphp
                <tr class="titulo">
                    <th colspan="65">
                        P. DATOS DEL PROFESIONAL RESPONSABLE
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="subtitulo2" colspan="8">FECHA<br>(aaaa-mm-dd)</td>
                    <td class="subtitulo2" colspan="6">HORA<br>(hh:mm)</td>
                    <td class="subtitulo2" colspan="19">PRIMER NOMBRE</td>
                    <td class="subtitulo2" colspan="17">PRIMER APELLIDO</td>
                    <td class="subtitulo2" colspan="15">SEGUNDO APELLIDO</td>
                </tr>
                <tr>
                    @php $fecha_tablap_dia = date_format(new DateTime($form008->created_at), 'Y-m-d') @endphp
                    @php $fecha_tablap_hora = date_format(new DateTime($form008->created_at), 'H:i') @endphp
                    <td class="campos" colspan="8">{{ $fecha_tablap_dia }}</td>
                    <td class="campos" colspan="6">{{ $fecha_tablap_hora }}</td>
                    <td class="campos" colspan="19">{{ $usuario->nombre1 }}</td>
                    <td class="campos" colspan="17">{{ $usuario->apellido1 }}</td>
                    <td class="campos" colspan="15">{{ $usuario->apellido2 }}</td>
                </tr>
                <tr>
                    <td class="subtitulo2" colspan="14">NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</td>
                    <td class="subtitulo2" colspan="23">FIRMA</td>
                    <td class="subtitulo2" colspan="28">SELLO</td>
                </tr>
                <tr>
                    <td class="campos" rowspan="1" height='20px' colspan="14">{{$form008->id_usuariocrea}}</td>
                    <td class="campos" rowspan="1" height='20px' colspan="23">{{substr($usuario->nombre1 , 0, 1) }}.
                        {{ $usuario->apellido1 }} {{ $usuario->apellido2 }}
                    </td>
                    <td class="campos" rowspan="1" height='20px' colspan="28">
                        @if(!is_null($firma))
                        <img width=180 height=130 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="position:relative; z-index: 999;" align=center hspace=12><br>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    
</body>

</html>