<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>


    <style>
        @page {}

        /** Define now the real margins of every page in the PDF **/
        body {

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

        .bg-blue {
            background-color: #DDEBF7;
        }

        .bg-gris {
            background-color: #DDE0E7;
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

        table {
            width: 100%;
            border-collapse: collapse;
        }


        .texto-izquierda-arriba {
            text-align: left;
            vertical-align: top;
        }



        .page-break-before {
            page-break-before: always;
        }
    </style>
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
                <td style="font-size: 12; height: 35px;" class="bg-gris"> &nbsp; Activos</td>
                <td style="font-size: 12; height: 35px; text-align: right;" class="bg-gris"> &nbsp; {{$totalActivos_}} </td>
            </tr>
            <tr>
                <td style="font-size: 12; height: 35px;"> &nbsp; Pasivos</td>
                <td style="font-size: 12; height: 35px; text-align: right;"> &nbsp; {{$totalPasivo_}} </td>
            </tr>
            <tr>
                <td style="font-size: 12; height: 35px;" class="bg-gris"> &nbsp; Patrimonio</td>
                <td style="font-size: 12; height: 35px; text-align: right;" class="bg-gris"> &nbsp; {{$totalPatrimonio_}} </td>
            </tr>
            <tr>
                <td style="font-size: 12; height: 35px;"> &nbsp; Diferencia</td>
                <td style="font-size: 12; height: 35px; text-align: right;"> &nbsp; {{$total_diferencia}} </td>
            </tr>
        </tbody>
    </table>
    <br>
    {!! $htmlActivos !!}
    <br>
    {!! $htmlPasivos !!}
    <br>
    {!! $htmlPatrimonio !!}
    <br>
    <table>
        <tr>
            <td>
                @if(!is_null($empresa->id_representante))
                @if($empresa->persona_nat_jur == '2')
                @if(is_null($empresa->tipo_representante))
                {{$empresa->pref_repre->titulo_prefijo}} {{$empresa->usuario_representante->nombre1}} {{$empresa->usuario_representante->nombre2}} {{$empresa->usuario_representante->apellido1}} {{$empresa->usuario_representante->apellido2}}
                <br>
                C.I. {{$empresa->id_representante}}<br>
                @else
                {{$empresa->pref_repre->titulo_prefijo}} {{$empresa->usuario_representante->nombre1}} {{$empresa->usuario_representante->nombre2}} {{$empresa->usuario_representante->apellido1}} {{$empresa->usuario_representante->apellido2}} <br> EN REPRESENTACION DE <br> {{$empresa->empresa_representante}}
                @endif
                @endif
                @endif
            </td>
            <td></td>
            <td style="text-align: right;">
                @if(!is_null($empresa->id_contador))
                {{$empresa->pref_cont->titulo_prefijo}} {{$empresa->usuario_contador->nombre1}} {{$empresa->usuario_contador->nombre2}} {{$empresa->usuario_contador->apellido1}} {{$empresa->usuario_contador->apellido2}} <br>
                C.I. {{$empresa->id_contador}} <br>
                Registro # {{$empresa->num_registro_contador}} <br>
                @endif
            </td>
        </tr>
    </table>
</body>


</html>