<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Orden de compra {{ $orden->numero }}</title>
    <style>
        @page {
            margin: 18px 22px 20px 22px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .sheet {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .sheet td,
        .sheet th {
            border: 1px solid #4b5563;
            padding: 6px 7px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .title-main {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: .8px;
            color: #111827;
        }

        .version-box {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            color: #374151;
            background: #f3f4f6;
        }

        .table-head th {
            background: #1f3b5b;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .top-box {
            background: #f8fafc;
            font-weight: bold;
            color: #111827;
        }

        .provider-row {
            background: #f9fafb;
            font-weight: bold;
        }

        .money {
            text-align: right;
            white-space: nowrap;
        }

        .qty,
        .code,
        .fr {
            text-align: center;
        }

        .row-item {
            height: 28px;
        }

        .totals-label {
            background: #dbeafe;
            color: #1e3a8a;
            font-weight: bold;
        }

        .subtotal-label {
            background: #e0f2fe;
            color: #0c4a6e;
            font-weight: bold;
            text-align: center;
        }

        .subtotal-value {
            background: #f8fafc;
            font-weight: bold;
            text-align: right;
            color: #111827;
        }

        .deliver-row td {
            background: #f9fafb;
            font-size: 12px;
            font-weight: bold;
            color: #111827;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .note-row td {
            background: #fff7f7;
            color: #c62828;
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            padding-top: 10px;
            padding-bottom: 10px;
            letter-spacing: .2px;
        }

        .footer-space td {
            height: 80px;
            border-top: none;
            border-bottom: none;
            background: #ffffff;
        }

        .footer-brand td {
            border-top: none;
            border-bottom: none;
            text-align: center;
            background: #ffffff;
            font-size: 15px;
            color: #374151;
        }

        .footer-line td {
            border-top: none;
            border-bottom: none;
            background: #ffffff;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .line-sign {
            width: 70%;
            margin: 0 auto;
            border-top: 1px solid #374151;
            height: 1px;
        }

        .footer-data td {
            border-top: none;
            background: #ffffff;
            text-align: center;
            font-size: 11px;
            color: #111827;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .logo-box {
            text-align: center;
            padding: 8px;
            background: #ffffff;
        }

        .logo-box img {
            max-width: 190px;
            max-height: 58px;
        }

        .h-header {
            height: 28px;
        }
    </style>
</head>

<body>

    <table class="sheet">
        <colgroup>
            <col style="width: 15%">
            <col style="width: 11%">
            <col style="width: 34%">
            <col style="width: 10%">
            <col style="width: 12%">
            <col style="width: 10%">
            <col style="width: 8%">
        </colgroup>

        <tr>
            <td colspan="2" rowspan="3" class="logo-box">
                @if(!empty($logoPath) && file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="Logo">
                @else
                <div class="bold">LOGO</div>
                @endif
            </td>
            <td colspan="4" rowspan="3" class="center title-main">ORDEN DE COMPRA</td>
            <td rowspan="3" class="version-box">Vers. 0.1</td>
        </tr>
        <tr></tr>
        <tr></tr>

        <tr class="h-header">
            <td colspan="2" class="top-box">
                FECHA: {{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y') }}
            </td>
            <td colspan="5" class="top-box">
                NÚMERO DE ORDEN DE COMPRA: {{ $orden->numero }}
            </td>
        </tr>

        <tr>
            <td colspan="7" class="provider-row">
                PROVEEDOR: {{ strtoupper($proveedor->nombrecomercial ?? $proveedor->nombre ?? 'N/D') }}
            </td>
        </tr>

        <tr class="table-head">
            <th>CÓDIGO</th>
            <th>CANTIDAD EN UNIDADES</th>
            <th>ORDEN DE COMPRA</th>
            <th>DIAMETER (FR)</th>
            <th>PRECIO UNITARIO</th>
            <th>TOTAL</th>
            <th>OBSERVACIÓN</th>
        </tr>



        @foreach($lineas as $item)
        <tr class="row-item">
            <td class="code">{{ $item['codigo'] }}</td>
            <td class="qty">{{ number_format((float)$item['cantidad'], 0, '.', '') }}</td>
            <td>{{ $item['descripcion'] }}</td>
            <td class="fr">{{ $item['diameter_fr'] }}</td>
            <td class="money">$ {{ number_format((float)$item['precio_unitario'], 4, '.', ',') }}</td>
            <td class="money">$ {{ number_format((float)$item['total'], 2, '.', ',') }}</td>
            <td>{{ $item['observacion'] }}</td>
        </tr>
        @endforeach

        @for($i = 0; $i < $faltantes; $i++)
            <tr class="row-item">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            </tr>
            @endfor

            <tr>
                <td class="totals-label">TOTAL EN CANTIDADES :</td>
                <td class="totals-label qty">
                    {{ number_format((float)$orden->total_cantidades, 0, '.', '') }}
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="subtotal-label">SUBTOTAL</td>
                <td class="subtotal-value">$ {{ number_format((float)$orden->subtotal, 2, '.', ',') }}</td>
                <td>&nbsp;</td>
            </tr>

            <tr class="deliver-row">
                <td colspan="7">ENTREGAR A: Departamento de Compras </td>
            </tr>

            <tr class="note-row">
                <td colspan="7">NOTA: NO SE ACEPTARÁN FACTURAS CON FECHAS POSTERIOR AL 24 DE CADA MES</td>
            </tr>

            <tr class="footer-space">
                <td colspan="7"></td>
            </tr>

            <tr class="footer-brand">
                <td colspan="7">{{$empresa->nombrecomercial}}</td>
            </tr>

            <tr class="footer-line">
                <td colspan="7">
                    <div class="line-sign"></div>
                </td>
            </tr>

            <tr class="footer-data">
                <td colspan="7">

                    {{$empresa->direccion}} / {{$empresa->ciudad}}

                    @if(isset($orden) && $orden->entrega_a == 'TORRE 1 6TO PISO 618')
                    Av. Abel Romeo Castillo y Av. J.T. Marengo, Torre Vitalis 1 - piso 6, oficina 618 Guayaquil
                    @elseif(isset($orden) && $orden->entrega_a == 'TORRE 2 SUBSUELO')
                    Av. Abel Romeo Castillo y Av. J.T. Marengo, Torre Medica 2 - subsuelo, Guayaquil
                    @endif
                </td>
            </tr>
            <tr class="footer-data">
                <td colspan="7">Telef: {{$empresa->telefono1}} - {{$empresa->telefono2}} / {{$empresa->email}}</td>
            </tr>
    </table>

</body>

</html>