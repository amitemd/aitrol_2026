<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>


    <style>
        @page {
            margin: 5 0;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 40px;
            margin-left: 20px;
            margin-right: 20px;
            margin-bottom: 1px;
            font-size: 10px;
        }

        /** Define the header rules **/
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1cm;
        }

        /** Define the footer rules **/
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
        }

        table {
            width: 100px;
            border-collapse: collapse;
        }

        .table2d {
            border: none;
            text-align: center;
        }

        .titulo {
            background-color: #C7C3C2;
        }

        p {
            padding-top: 0;
            padding-bottom: 0;
            margin: 0;
        }

        div {
            padding-top: 0;
            padding-bottom: 0;
        }

        .txt_vertical {
            writing-mode: vertical-lr;
            transform: rotate(-90deg);
            letter-spacing: 2px;
            word-wrap: break-word;
        }

        .td-border {
            border: 1px solid;
        }


        .td-not-border-top {
            border-top: 1px solid;
            border-left: 1px solid;
            border-right: 1px solid;
        }

        .td-not-border-bottom {
            border-bottom: 1px solid;
            border-left: 1px solid;
            border-right: 1px solid;
        }

        .td-not-border-right {
            border-left: 1px solid;
            border-right: 1px solid;
            border-bottom: 1px solid;
        }

        .td-not-border-left {
            border-bottom: 1px solid;
            border-top: 1px solid;
            border-right: 1px solid;
        }

        .td-border-top {
            border-top: 1px solid;
        }

        .td-border-bottom {
            border-bottom: 1px solid;
        }

        .td-border-left {
            border-left: 1px solid;
        }



        .td-border-right {
            border-right: 1px solid;
        }


        .bg-blue {
            background-color: #DDEBF7;
        }

        .bg-green {
            background-color: #BADAC8;
        }

        .bg-yellow {
            background-color: #F1FD77;
        }

        .bg-purple {
            background-color: #CCCCFF;
        }

        .bg-turquoise {
            background-color: #40E0D0;
        }

        .bg-gris {
            background-color: #EAEAEA;
        }

        .text-center {
            text-align: center;
        }


        .texto-izquierda-arriba {
            text-align: left;
            vertical-align: top;
        }

        .justify-text {
            text-align: justify;
        }

        .align-left {
            text-align: left;
        }

        td.contenedor-texto {
            padding: 20px;
            text-align: left;
        }

        .page-break-before {
            page-break-before: always;
        }

        .page-break-after {
            page-break-after: always;
        }

        ol {
            list-style-type: decimal;
            margin-left: 20px;
        }
    </style>

    <div class="box-body card" style="text-align: center">
        @if(!is_null($empresa))
        <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:250px;height: 60px">
        @endif
    </div><br>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td class="text-center" style="font-size: 16;">{{$empresa->razonsocial}}</td>
            </tr>
            <tr>
                <td class="text-center" style="font-size: 16;"> Balance de resultados</td>
            </tr>
            <tr>
                <td class="text-center" style="font-size: 16;">DESDE: {{$desde}} HASTA: {{$hasta}}</td>
            </tr>
            <tr>
                <td class="text-center" style="font-size: 16;">Resumen</td>
            </tr>
        </tbody>
    </table>

    <table border="1" style="width: 50%;" align="center">
        <tbody>
            <tr>
                <td style="font-size: 12; height: 35px;"> &nbsp; Descripción</td>
                <td style="font-size: 12; height: 35px;"> &nbsp; Valor</td>
            </tr>
            <tr>
                <td style="font-size: 12; height: 35px;" class="bg-gris"> &nbsp; Utilidad/Perdida del ejercicio</td>
                <td style="font-size: 12; height: 35px; text-align: right;" class="bg-gris"> &nbsp; {{$utilidadBruta}}</td>
            </tr>
            <tr>
                <td style="font-size: 12; height: 35px;"> &nbsp; Participación a trabajadores</td>
                <td style="font-size: 12; height: 35px; text-align: right;"> &nbsp; {{$participacionTrabajadores}}</td>
            </tr>
            <tr>
                <td style="font-size: 12; height: 35px;" class="bg-gris"> &nbsp; Utilidad contable</td>
                <td style="font-size: 12; height: 35px; text-align: right;" class="bg-gris"> &nbsp; {{$utilidadContable}}</td>
            </tr>
            <tr>
                <td style="font-size: 12; height: 35px;"> &nbsp; Impuesto generado</td>
                <td style="font-size: 12; height: 35px; text-align: right;"> &nbsp; {{$impuestoGenerado}}</td>
            </tr>
            <tr>
                <td style="font-size: 12; height: 35px;" class="bg-gris"> &nbsp; Utilidad/Perdida neta</td>
                <td style="font-size: 12; height: 35px; text-align: right;" class="bg-gris"> &nbsp; {{$utilidadPerdida}}</td>
            </tr>
        </tbody>
    </table>

    <br>

    <table style="width: 100%;" align="center">
        <tbody>
            <tr>
                <td class="text-center bg-gris" style="font-size: 18;">Ingresos: $ {{$totalIngresos}}</td>
            </tr>
        </tbody>
    </table>
    <br>



    <table border="1" style="width: 100%;" align="center">
        <tbody>
            <tr>
                <td style="font-size: 12; height: 35px; width: 25%;" class="bg-gris"> &nbsp; Cuenta</td>
                <td style="font-size: 12; height: 35px; width: 50%;" class="bg-gris"> &nbsp; Nombre</td>
                <td style="font-size: 12; height: 35px; width: 25%;" class="bg-gris"> &nbsp; Saldo</td>
            </tr>
            @php
            $ingreso = [];
            @endphp
            @foreach ($datos as $data)
            @if ($data->tipo == "INGRESOS" && $data->saldo != 0)
            @php $ingreso[] = $data; @endphp
            <tr>
                <td> &nbsp; &nbsp;{{ $data->plan }}</td>
                <td>&nbsp; &nbsp; {{ $data->nombre }}</td>
                <td style="text-align: right; height: 25px;">{{ $data->saldo }}</td>
            </tr>
            @endif
            @endforeach
            <tr>
                <td style="height: 25px; font-size: 12px;">&nbsp;Total ingresos</td>
                <td></td>
                <td style="text-align: right;"><b>{{$totalIngresos}}</b></td>
            </tr>
        </tbody>
    </table>

    <br>

    <table style="width: 100%;" align="center">
        <tbody>
            <tr>
                <td class="text-center bg-gris" style="font-size: 18;">Costos: $ {{$totalCostos}}</td>
            </tr>
        </tbody>
    </table>
    <br>

    <table border="1" style="width: 100%;" align="center">
        <tbody>
            <tr>
                <td style="font-size: 12; height: 35px; width: 25%;" class="bg-gris"> &nbsp; Cuenta</td>
                <td style="font-size: 12; height: 35px; width: 50%;" class="bg-gris"> &nbsp; Nombre</td>
                <td style="font-size: 12; height: 35px; width: 25%;" class="bg-gris"> &nbsp; Saldo</td>
            </tr>
            @php
            $costos = [];
            @endphp
            @foreach ($datos as $data)
            @if ($data->tipo == "COSTOS" && $data->saldo != 0)
            @php $costos[] = $data; @endphp
            <tr>
                <td>&nbsp; &nbsp;{{ $data->plan }}</td>
                <td>&nbsp; &nbsp;{{ $data->nombre }}</td>
                <td style="text-align: right; height: 25px;">{{ $data->saldo }}</td>
            </tr>
            @endif
            @endforeach
            <tr>
                <td style="height: 25px; font-size: 12px;">&nbsp;Total costos</td>
                <td></td>
                <td style="text-align: right;"><b>{{$totalCostos}}</b></td>
            </tr>
        </tbody>
    </table>

    <br>

    <table style="width: 100%;" align="center">
        <tbody>
            <tr>
                <td class="text-center bg-gris" style="font-size: 18;">Gastos: $ {{$totalGastos}}</td>
            </tr>
        </tbody>
    </table>
    <br>

    <table border="1" style="width: 100%;" align="center">
        <tbody>
            <tr>
                <td style="font-size: 12; height: 35px; width: 25%;" class="bg-gris"> &nbsp; Cuenta</td>
                <td style="font-size: 12; height: 35px; width: 50%;" class="bg-gris"> &nbsp; Nombre</td>
                <td style="font-size: 12; height: 35px; width: 25%;" class="bg-gris"> &nbsp; Saldo</td>
            </tr>
            @php
            $gastos = [];
            @endphp
            @foreach ($datos as $data)
            @if ($data->tipo == "GASTOS" && $data->saldo != 0)
            @php $gastos[] = $data; @endphp
            <tr>
                <td>&nbsp; &nbsp;{{ $data->plan }}</td>
                <td>&nbsp; &nbsp;{{ $data->nombre }}</td>
                <td style="text-align: right; height: 25px;">{{ $data->saldo }}</td>
            </tr>
            @endif
            @endforeach
            <tr>
                <td style="height: 25px; font-size: 12px;">&nbsp;Total Gastos</td>
                <td></td>
                <td style="text-align: right;"><b>{{$totalGastos}}</b></td>
            </tr>
        </tbody>
    </table>


</body>

</html>