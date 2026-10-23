<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LISTADO CONCILIACION BANCARIA</title>
</head>

<style>
    #page_pdf {
        width: 100%;
        margin: 15px auto 10px auto;
    }

    #importaciones_head {
        width: 100%;
        margin-bottom: 10px;
    }

    .color {
        background-color: #CDE4F1;
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

    .td-abajo {
        border-bottom: 1px solid black;
        padding: 8px;
        text-align: left;
    }



    .info_empresa {
        width: 50%;
        text-align: left;
    }



    table {
        border-collapse: collapse;
        font-size: 12pt;
        font-family: 'sans-serif';
        width: 100%;
    }


    table tr:nth-child(odd) {
        background: #FFF;
    }

    table td {
        text-align: center;
        color: #000000;
        padding: 10px;
    }

    table th {
        text-align: center;
        color: #000000;
        font-size: 14px;
    }

    * {
        font-family: 'sans-serif' !important;
    }

    .table_1 {

        margin-right: 0px;
        margin-top: 0px;
        margin-left: 0px;
        margin-bottom: 0px;
        font-size: 12px;
        width: 100%;

    }

    .table_2 {

        margin-right: 0px;
        margin-top: 0px;
        margin-left: 0px;
        margin-bottom: 0px;
        font-size: 20px;
        width: 100%;
    }

    .table_3 {

        margin: 15px auto;
        margin-top: 20px;
        font-size: 12px;
        width: 20%;
    }


    .titulo_css {

        text-align: center;
        color: black;
    }

    .page_break {
        page-break-before: always;
    }
</style>

<body>
    <div>
        <table border=1 style="font-size: 12px" width="100%">
            <thead>
                <tr>
                    <td style="width: 20%;">
                        @if(!is_null($empresa))
                        <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:150px;height: 60px">
                        @endif
                    </td>

                    <td colspan="4" class="color" style="font-size: 18px; align=" center"><b>{{$x}}</b></td>

                </tr>
            </thead>
        </table>





        <table style="font-size: 11px" width="100%">
            <thead>

                <tr>
                    <td class="td-border color" colspan="5" style="font-size: 18px; text-align: center;"><b> CONCILIACION BANCARIA &nbsp; @if(isset($consulta_mes[0]->caja_banco)) {{$consulta_mes[0]->caja_banco->nombre}} @elseif(isset($libro_mes[0]->caja_banco)) {{$libro_mes[0]->caja_banco->nombre}} @endif </b></td>

                </tr>
                <tr>
                    <td class="td-border color" colspan="5" style="font-size: 18px; align=" center"><b>{{$mes2. ' '.$ani2}}</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border color" colspan="2"><b> SALDO EN BANCOS </b> </td>
                    <td></td>
                    <td class="td-border color" colspan="2"><b> SALDO EN LIBROS </b></td>
                </tr>

                @foreach($libro_mes as $lm)
                @if(count($consulta_mes) > 0)
                @foreach ($consulta_mes as $value)
                <tr>
                    <td class="td-border" align="center">Saldo Anterior</td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$value->saldo_anterior}}</td>
                    <td style="width: 10%;"></td>
                    <td class="td-border" align="center">Saldo Anterior</td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$lm->saldo_anterior}}</td>

                </tr>
                <tr>
                    <td class="td-border" align="center"> (+) Depositos </td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$value->valor_depositos}}</td>
                    <td></td>
                    <td class="td-border" align="center"> (+) Depositos </td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$lm->valor_depositos}}</td>

                </tr>
                <tr>
                    <td class="td-border" align="center"> (+) Valor Acreditado </td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$value->valor_acreditado}}</td>
                    <td></td>
                    <td class="td-border" align="center"> (+) Valor Acreditado </td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$lm->valor_acreditado}}</td>
                </tr>
                <tr>
                    <td class="td-border" align="center"> (-) Cheques Pagado </td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$value->valor_cheques}}</td>
                    <td></td>
                    <td class="td-border" align="center"> (-) Cheques Pagado </td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$lm->valor_cheques}}</td>
                </tr>
                <tr>
                    <td class="td-border" align="center"> (-) Valores Debitados</td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$value->valor_debitado}}</td>
                    <td></td>
                    <td class="td-border" align="center"> (-) Valores Debitados</td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$lm->valor_debitado}}</td>

                </tr>
                <tr>
                    <td class="td-border color" align="center"> Saldo Según Bancos </td>
                    <td class="td-border color" align="center" style="width: 20%">$&nbsp;&nbsp;&nbsp;&nbsp;{{$value->saldo_actual}}</td>
                    <td></td>
                    <td class="td-border color" align="center"> Saldo Según Libros </td>
                    <td class="td-border color" align="center" style="width: 20%">$&nbsp;&nbsp;&nbsp;&nbsp;{{$lm->saldo_actual}}</td>
                </tr>

                @endforeach
                @else
                <tr>
                    <td class="td-border" align="center">Saldo Anterior</td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{ isset($value->saldo_anterior) ? $value->saldo_anterior : '0.0' }}</td>
                    <td style="width: 10%;"></td>
                    <td class="td-border" align="center">Saldo Anterior</td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$lm->saldo_anterior}}</td>

                </tr>
                <tr>
                    <td class="td-border" align="center"> (+) Depositos </td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{ isset($value->valor_depositos) ? $value->valor_depositos : '0.0' }}</td>
                    <td></td>
                    <td class="td-border" align="center"> (+) Depositos </td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$lm->valor_depositos}}</td>

                </tr>
                <tr>
                    <td class="td-border" align="center"> (+) Valor Acreditado </td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp; {{ isset($value->valor_acreditado) ? $value->valor_acreditado : '0.0' }}</td>
                    <td></td>
                    <td class="td-border" align="center"> (+) Valor Acreditado </td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$lm->valor_acreditado}}</td>
                </tr>
                <tr>
                    <td class="td-border" align="center"> (-) Cheques Pagado </td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp; {{ isset($value->valor_acreditado) ? $value->valor_acreditado : '0.0' }}</td>
                    <td></td>
                    <td class="td-border" align="center"> (-) Cheques Pagado </td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$lm->valor_cheques}}</td>
                </tr>
                <tr>
                    <td class="td-border" align="center"> (-) Valores Debitados </td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp; {{ isset($value->valor_debitado) ? $value->valor_debitado : '0.0' }}</td>
                    <td></td>
                    <td class="td-border" align="center"> (-) Valores Debitados</td>
                    <td class="td-border" align="center">$&nbsp;&nbsp;&nbsp;&nbsp;{{$lm->valor_debitado}}</td>

                </tr>
                <tr>
                    <td class="td-border color" align="center"> Saldo Según Bancos </td>
                    <td class="td-border color" align="center" style="width: 20%">$&nbsp;&nbsp;&nbsp;&nbsp; {{ isset($value->saldo_actual) ? $value->saldo_actual : '0.0' }}</td>
                    <td></td>
                    <td class="td-border color" align="center"> Saldo Según Libros </td>
                    <td class="td-border color" align="center" style="width: 20%">$&nbsp;&nbsp;&nbsp;&nbsp;{{$lm->saldo_actual}}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        <br>
        <br>
        <table border="1" style="width: 100%;">
            <tbody>
                <tr>
                    <td class="color" colspan="8" style="font-size: 15px; align=" center"><b> CHEQUES GIRADOS Y NO COBRADOS </b> </td>
                </tr>
                <tr>
                    <td class="color">Fecha</td>
                    <td class="color"> Tipo </td>
                    <td class="color"> Id Asiento </td>
                    <td class="color"> Detalle </td>
                    <td class="color"> Valor </td>
                    <td class="color"> Secuencia </td>
                    <td class="color"> Cheque </td>
                    <td class="color"> Beneficiario </td>

                </tr>
                @foreach ($pendientes as $pen)
                <tr style="font-size: 10px;">
                    <td align="center">{{$pen->fecha}}</td>
                    <td align="center">{{$pen->tipo}}</td>
                    <td align="center">{{$pen->id_asiento}}</td>
                    <td style="text-align: center;">{{ $pen->detalle }}</td>
                    <td align="center">{{$pen->valor}}</td>
                    <td align="center">{{$pen->secuencia}}</td>
                    <td align="center">{{$pen->cheque}}</td>
                    <td align="center">{{$pen->beneficiario}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <br>

        @php
        $nombre = '';

        if(isset($consulta_mes[0])){
        $nombre = isset($consulta_mes[0]) ? '' : $consulta_mes[0]->nombre->apellido1. ' '.$consulta_mes[0]->nombre->apellido1. ' '.$consulta_mes[0]->nombre->nombre1. ' '.$consulta_mes[0]->nombre->nombre2;
        } else {
        if(isset($libro_mes[0])){
        $nombre = isset($libro_mes[0]->nombre) ?
        $libro_mes[0]->nombre->apellido1 . ' ' .
        $libro_mes[0]->nombre->apellido2 . ' ' .
        $libro_mes[0]->nombre->nombre1 . ' ' .
        $libro_mes[0]->nombre->nombre2 :
        '';
        }
        }
        @endphp
        <table style="width: 100%;">
            <tfoot>
                <tr>
                    <td style="text-align:center; width: 2%;">_______________________</td>
                </tr>

                <tr>
                    <td style="text-align:center;">Elaborado Por:</td>
                </tr>
                <tr>
                    <td style="text-align:center;">{{$nombre}}</td>
                </tr>

            </tfoot>
        </table>
    </div>

</body>

</html>