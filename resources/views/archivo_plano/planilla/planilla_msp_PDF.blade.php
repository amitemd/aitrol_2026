<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Planilla MSP</title>
    <style>
        @page {
            margin: 8px 10px 8px 10px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .sheet {
            width: 100%;
        }

        .titulo-empresa {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            border-bottom: 0;
            padding: 3px 0 2px 0;
        }

        .titulo-planilla {
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            padding: 3px 0;
            margin-bottom: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta td {
            padding: 3px 4px;
            font-size: 8px;
            vertical-align: middle;
        }

        .meta .label {
            font-weight: bold;
            white-space: nowrap;
        }

        .b-l { border-left: 1px solid #000; }
        .b-r { border-right: 1px solid #000; }
        .b-t { border-top: 1px solid #000; }
        .b-b { border-bottom: 1px solid #000; }

        .detalle {
            width: 100%;
            border-collapse: collapse;
        }

        .detalle thead {
            display: table-header-group;
        }

        .detalle tbody tr {
            page-break-inside: avoid;
        }

        .detalle th,
        .detalle td {
            padding: 4px 5px;
            font-size: 7px;
            line-height: 1.05;
            vertical-align: top;
        }

        .detalle th {
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
        }

        .detalle td:nth-child(1),
        .detalle td:nth-child(2),
        .detalle td:nth-child(3),
        .detalle td:nth-child(4),
        .detalle td:nth-child(5),
        .detalle td:nth-child(6),
        .detalle td:nth-child(7),
        .detalle td:nth-child(8),
        .detalle td:nth-child(9),
        .detalle td:nth-child(10) {
            padding: 2px;
            text-align: center;
            white-space: nowrap;
            vertical-align: top;
        }

        .detalle td:nth-child(4) {
            text-align: left;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .total-row td {
            font-weight: bold;
            padding-top: 4px;
        }

        .total-label {
            text-align: right !important;
            white-space: nowrap;
        }

        .firma-wrap {
            margin-top: 50px !important;
            page-break-inside: avoid;
        }

        .firma-tabla {
            width: 100%;
            border-collapse: collapse;
        }

        .firma-tabla td {
            font-size: 8px;
            padding: 3px 4px;
            vertical-align: middle;
        }

        .firma-label {
            width: 8%;
            font-weight: bold;
            white-space: nowrap;
        }

        .firma-value {
            width: 50%;
            border-bottom: 1px dotted #000;
            text-align: center;
        }

        .firma-space {
            width: 25%;
        }

        .firma-sello {
            width: 14%;
            text-align: center;
            border-bottom: 1px solid #000;
        }
    </style>
</head>
<body>
<div class="sheet">

    <div class="titulo-empresa">{{ $empresa_nombre }}</div>
    <div class="titulo-planilla">{{ isset($titulo_planilla) ? $titulo_planilla : 'PLANILLA DE CARGOS INDIVIDUAL' }}</div>

    <table class="meta">
        <tr>
            <td class="label" style="width: 16%;">Tipo Servicio:</td>
            <td style="width: 16%;">{{ $tipo_servicio }}</td>
            <td class="label" style="width: 16%;">Fecha de Elaboración:</td>
            <td style="width: 14%;">{{ $fecha_elaboracion }}</td>
            <td style="width: 16%;"></td>
            <td style="width: 22%;"></td>
        </tr>
        <tr>
            <td class="label">Monto Solicitado:</td>
            <td>$ {{ number_format((float)$monto_solicitado, 2, '.', ',') }}</td>
            <td class="label">Mes y Año de Servicio:</td>
            <td>{{ $mes_anio_servicio }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="label">Nro. CV:</td>
            <td>{{ $nro_cv }}</td>
            <td class="label">CIE10:</td>
            <td>{{ $cie10 }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="label">CC No:</td>
            <td>{{ $paciente_identificacion }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="label">Beneficiario:</td>
            <td colspan="5">{{ $paciente_nombre }}</td>
        </tr>
        <tr>
            <td class="label">Detalle de Servicio Desde:</td>
            <td>{{ $fecha_desde }}</td>
            <td class="label">Hasta:</td>
            <td>{{ $fecha_hasta }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="detalle" border="1">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Código<br/>TSNS</th>
                <th>Clasificador</th>
                <th style="width: 50%;">Descripción</th>
                <th>Cantidad<br/>#</th>
                <th>Valor Unitario<br/>USD</th>
                <th>Clasificador<br/>%</th>
                <th>Subtotal<br/>USD</th>
                <th>Valor por<br/>Modificador USD</th>
                <th>Total Solicitado<br/>USD</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalles as $item)

                @php 
               
                 $tipos = ['P','AN','S'];
                 $codigos = ['240159','911111'];
                 
                @endphp
                <tr>
                    <td style="text-align: center">{{ $item['fecha'] }}</td>
                    <td style="text-align: center">
                        @if(strlen($item["codigo"]) <= 10)
                            @if(in_array($item["tipo"], $tipos) || in_array($item["codigo"], $codigos))
                                @if($item["codigo"] == 16)
                                    911111 
                                @else
                                    {{$item["codigo"]}} 
                                @endif
                            @endif
                        @endif
                    </td> 
                    <td style="text-align: center">{{ $item['clasificador'] }}</td>
                    <td style="text-align:center">{{ $item['descripcion'] }}</td>
                    <td style="text-align: center">{{ $item['cantidad'] }}</td>
                    <td style="text-align: center">$ {{ number_format((float)$item['valor_unitario'], 2, '.', '') }}</td>
                    <td style="text-align: center">{{ number_format((float)$item['clasificador_porcentaje'], 2, '.', '') }}</td>
                    <td style="text-align: center">$ {{ number_format((float)$item['subtotal_usd'], 2, '.', '') }}</td>
                    <td style="text-align: center">{{ number_format((float)$item['valor_modificador'], 2, '.', '') }}</td>
                    <td style="text-align: center">$ {{ number_format((float)$item['total_solicitado'], 2, '.', '') }}</td>
                </tr>
            @endforeach

            <tr class="total-row">
                <td colspan="9" class="total-label">Valor Total Solicitado</td>
                <td>$ {{ number_format((float)$total, 2, '.', ',') }}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <br>
    <div class="firma-wrap">
        <table class="firma-tabla">
            <tr >
                <td class="firma-label">Firma:</td>
                <td class="firma-value"></td>
                <td class="firma-space"></td>
                <td class="firma-space"></td>
            </tr>
            <tr>
                <td class="firma-label">Nombre:</td>
                <td class="firma-value">{{ $firma_nombre }}</td>
                <td class="firma-space"></td>
                <td></td>
            </tr>
            <tr>
                <td class="firma-label">N° CC:</td>
                <td class="firma-value">{{ $firma_cc }}</td>
                <td class="firma-space"></td>
                <td class="firma-value"></td>
            </tr>
            <tr>
                <td class="firma-label">Cargo:</td>
                <td class="firma-value">{{ $firma_cargo }}</td>
                <td class="firma-space"></td>
                <td style="text-align: center;">Sello</td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
