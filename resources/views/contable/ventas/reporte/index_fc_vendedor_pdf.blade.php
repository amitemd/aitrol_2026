<!DOCTYPE html>
<html>





<head>
    <title>Reporte Facturas Vendedor</title>
    <style>
        @page {
            margin: 15 0;
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

        .bg-celeste {
            background-color: #00BFFF;
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



        ol {
            list-style-type: decimal;
            margin-left: 20px;
        }
    </style>
</head>

<body>
    <header>

    </header>
    <main>
        <div class="box-body card" style="text-align: center">
            @if(!is_null($empresa))
            <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:250px;height: 75px">
            @endif
        </div>
        <br>
        <table style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 18px;"><b> Reporte Facturas por Vendedor </b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>FECHA DESDE: &nbsp;&nbsp;&nbsp; {{$fecha_desde}} </td>
                    <td>FECHA HASTA: &nbsp;&nbsp;&nbsp; {{$fecha_hasta}}</td>

                </tr>
            </tbody>
        </table>
        <br>
        <br>

        <table cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <th style="height: 25px;" class="text-center td-border"> <b>ID</b> </th>
                    <th class="text-center td-border"> <b>Fecha</b> </th>
                    <th class="text-center td-border"> <b>Cliente</b> </th>
                    <th class="text-center td-border"> <b>Comprobante</b> </th>
                    <th class="text-center td-border"> <b>Valor Neto</b> </th>
                    <th class="text-center td-border"> <b>Iva</b> </th>
                    <th class="text-center td-border"> <b>Total</b> </th>
                    <th class="text-center td-border"> <b>Responsable</b> </th>
                </tr>
                @php
                $totalVentas = 0;
                @endphp
                @foreach ($ventas as $venta)
                @php
                $subtotal = round(($venta->subtotal_0 + $venta->subtotal_12) - $venta->descuento,2);
                $totalVentas += $subtotal;
                @endphp
                <tr>
                    <td class="td-border">{{ $venta->id }}</td>
                    <td class="td-border">{{ $venta->fecha }}</td>
                    <td class="td-border">{{ $venta->nombre_cliente }}</td>
                    <td class="td-border">{{ $venta->nro_comprobante }}</td>
                    <td class="td-border">$ {{ $subtotal }}</td>
                    <td class="td-border">$ {{ $venta->impuesto }}</td>
                    <td class="td-border">$ {{ $venta->total_final }}</td>
                    <td class="td-border">{{ $venta->vendedor }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="8">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td style="font-size: 10px;"> <b>Total Ventas Netas:</b></td>
                    <td style="font-size: 17px;"><b> $ {{ $totalVentas }} </b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>