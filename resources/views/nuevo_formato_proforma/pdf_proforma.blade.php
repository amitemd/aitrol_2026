<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Assistant&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

    <style>
        * {
            margin: 0px ! important;
        }

        .fa-exclamation-triangle:before {
            font-family: DejaVu Sans;
            content: "\2709";
            color: black;
            font-size: 1.2rem;
            vertical-align: 5px;
        }

        #factura_head,
        #factura_cliente,
        #factura_detalle {
            width: 100%;
            margin-bottom: 10px;
            font-size: 0.7em
        }

        #detalle_productos tr:nth-child(even) {
            background: #ededed;
            border-radius: 10px;
            border: 1px solid #3d7ba8;
            overflow: hidden;
            padding-bottom: 15px;

        }

        .container_client {
            display: grid;
            grid-template-columns: 0.5fr 0.5fr;
        }

        .cont {
            background-color: black !important;
        }

        #container {
            page-break-inside: initial;
        }

        #container .left {
            width: 50%;
            float: left;
            font-size: 0.7em;
        }

        #container .right {
            width: 50%;
            float: right;
            font-size: 0.7em;
        }

        #container2 .left {
            width: 50%;
            float: left;
            font-size: 0.7em;
        }

        #container2 .right {
            width: 50%;
            float: right;
            font-size: 0.7em;
        }

        #container_right .rightr {
            width: 50%;
            float: right;
        }

        #container_right .leftr {
            width: 50%;
            float: left;
        }

        #container_left .leftr {
            width: 50%;
            float: left;
        }

        #container_left .rightr {
            width: 50%;
            float: right;
        }

        .left_border {
            font-weight: bold;
            margin-top: 14px !important;
            text-transform: uppercase;

        }

        .right_border {
            font-weight: normal;
            margin-top: 14px !important;
        }

        .lf {
            text-transform: uppercase;
        }

        .border {
            border: 2px solid black !important;
            text-align: center;
        }

        .header_table {
            vertical-align: super !important;
            /* background-color: green; */
            height: 100px !important;
        }

        .details_product {
            max-height: 20px;


        }

        .padd {
            vertical-align: super !important;
            /* background-color: green; */
            height: 45px !important;
            max-heigth: 50px;
        }

        .details_products {
            max-height: 100px !important;
        }

        .proforma_texto {
            font-weight: bold;

        }

        table {
            width: 100%;
        }

        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        .texto_f1 {
            font-size: 13px;
            font-weight: 100;
        }

        .texto_f2 {
            font-size: 13px;
            font-weight: normal;
        }

        .margenes {

            margin-left: 20px !important;
            margin-right: 20px !important;
        }

        #imagenCentrada {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .footer_img {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 200px;
            background-color: #D0DAEE;
        }

        .footer_img img {
            width: 100%;
            height: 200px;

        }

        .equalDivide tr td {
            width: 25%;
        }
    </style>

</head>

<body>
    <div id="page_pdf">

        {{-- IMAGEN EN EL MEDIO --}}
        <div>
            <img id="imagenCentrada" src="{{ base_path() . '/storage/app/logo/imagen_medio.png' }}" style="width:100%;height:280px">
        </div>

        <table id="factura_head">
            <tr>
                <td class="info_empresa">
                    <img src="{{ base_path() . '/storage/app/logo/cabecera_logo.png' }}" style="width:100%;height:320px">
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="width: 50%">
                    &nbsp;
                </td>
                <td style="border:4px solid gray;border-radius:10px;width: 50%;">

                    <div class="row" style="padding:5px;">
                        <span class="proforma_texto" style="width: 32%;float:left;">
                            PROFORMA N:
                        </span>
                        <span class="proforma_texto" style="width: 68%;float:right;text-align:left;">
                            @if(isset($proforma->secuencia)) {{ $proforma->secuencia }} @endif
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <table style="border-spacing: 1em 0.5em;">
            <tr>
                <td style="width: 70%;border:4px solid gray;border-radius:15px;">
                    <table class="equalDivide">
                        <tr>
                            <td>
                                <span class="texto_f1">FECHA:</span>
                            </td>
                            <td>
                                @php
                                    $fecha = date_format($proforma->created_at, 'Y-m-d');
                                @endphp
                                <span class="texto_f1">{{ $fecha }}</span>
                            </td>

                            <td>
                                <span class="texto_f1">TIPO NECESIDAD:</span>
                            </td>
                            <td>
                                <span class="texto_f1">{{ $proforma->tipo_necesidad }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:20%">
                                <span class="texto_f1">CLIENTE:</span>
                            </td>
                            <td style="width:20%">
                                <span class="texto_f1">@if(isset($proforma->cliente->nombre)) {{ $proforma->cliente->nombre }} @endif</span>
                            </td>
                            <td style="width:20%">
                                <span class="texto_f1">CORREO: @if(isset($proforma->cliente->email_representante)) {{ $proforma->cliente->email_representante }} @endif</span>
                            </td>
                            <td style="width:20%">
                                <span class="texto_f1">@if(isset($proforma->cliente->email_representante)) {{ $proforma->cliente->email_representante }} @endif</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:20%">
                                <span class="texto_f1">RUC:</span>
                            </td>
                            <td style="width:20%">
                                <span class="texto_f1"> @if(isset($proforma->empresa)) {{ $proforma->empresa->id }} @endif</span>
                            </td>

                            <td style="width:20%">
                                <span class="texto_f1">CODIGO CPC:</span>
                            </td>
                            <td style="width:20%">
                                <span class="texto_f1">{{ $proforma->codigo_cpc }}</span>
                            </td>
                        </tr>

                        <tr>
                            <td style="width:20%">
                                <span class="texto_f1">DIRECCION:</span>
                            </td>

                            <td style="width:20%">
                                <span class="texto_f1">@if(isset($proforma->cliente->direccion_representante)) {{ $proforma->cliente->direccion_representante }} @endif</span>
                            </td>

                            <td style="width:20%">
                                <span class="texto_f1">COD. DE CONTRATACION</span>:
                            </td>

                            <td style="width:20%">
                                <span class="texto_f1">{{ $proforma->cod_contratacion }}</span>
                            </td>
                        </tr>


                        <tr>
                            <td style="width:20%">
                                <span class="texto_f1">CIUDAD:</span>
                            </td>

                            <td style="width:20%">
                                <span class="texto_f1">@if(isset($proforma->cliente->ciudad_representante)) {{ $proforma->cliente->ciudad_representante }} @endif</span>
                            </td>

                            <td style="width:20%">
                                <span class="texto_f1">&nbsp;</span>
                            </td>
                            <td style="width:20%">
                                <span class="texto_f1">&nbsp;</span>
                            </td>
                        </tr>


                        <tr>
                            <td style="width:20%">
                                <span class="texto_f1">OBJETO DE <br> COMPRA:</span>
                            </td>
                        </tr>

                    </table>


                </td>
                <td style="width: 30%;border:4px solid gray;border-radius:15px;">
                    <table style="border-spacing: 20px;">
                        <tr style="margin-top: -15px !important">
                            <td style="text-align: center;">
                                <span style="text-decoration:underline;" class="texto_f1">
                                    DATOS DEL CONTACTO
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="texto_f1">RESPONSABLE:
                                    @if(isset($proforma->vendedor->nombre1))  {{ $proforma->vendedor->nombre1 . ' ' . $proforma->vendedor->apellido1 }} @endif</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="texto_f1">CELULAR: @if(isset($proforma->vendedor->telefono1)) {{ $proforma->vendedor->telefono1 }} @endif</span>
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align: center">
                                <span class="texto_f1">CORREO:</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center">
                                <span class="texto_f1"
                                    style="color:rgb(40, 157, 241);text-decoration:underline">@if(isset($proforma->vendedor->email)) {{ $proforma->vendedor->email }} @endif</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="margenes" style="border:4px solid gray;border-radius:10px;width: 100%;">
            <thead>
                <tr>
                    <td class="texto_f2" style="width: 12.8%">
                        ITEM
                    </td>
                    <td class="texto_f2" style="width: 12.8%">
                        NOMBRE GENERICO
                    </td>
                    <td class="texto_f2" style="width: 12.8%">
                        ESPECIFICACION TECNICA
                    </td>
                    <td class="texto_f2" style="width: 12.8%">
                        ESPECIFICACION TECNICA OFERTADA
                    </td>
                    <td class="texto_f2" style="width: 12.8%">
                        REGISTRO SANITARIO
                    </td>

                    <td class="texto_f2" style="width: 12.8%">
                        CANT.
                    </td>
                    <td class="texto_f2" style="width: 12.8%">
                        PRECIO USD.
                    </td>
                    <td class="texto_f2" style="width: 12.8%">
                        PRECIO USB
                    </td>
                </tr>
            </thead>
        </table>


        <div class="margenes">
            <table id="factura_detalle"
                style="border-top-color:#FFFFFF;border-left-color:#FFFFFF;border-right-color:#FFFFFF;padding-top:5px;padding-bottom:15px; page-break-after:auto">
                @php
                    $subtotal0 = 0;
                    $subtotal12 = 0;
                    $descuento = 0;
                    $total = 0;
                    $iva_total = 0;
                @endphp

                @foreach ($proforma->detalles as $key => $det)
                    @php

                        $precio_total = $det->cantidad * $det->precio;

                    @endphp
                    <tr>
                        <th class="padd" style="width: 12.8%;">
                            <div class="details_product"
                                style="font-weight:normal; text-align: left; text-transform: uppercase;font-size:13px;">
                                {{ $key + 1 }}</div>
                        </th>
                        <th class="padd" style="width: 12.8%">
                            <div class="details_product"
                                style="font-weight:normal; text-align: left; text-transform: uppercase;font-size:13px">
                                {{--@if(isset($det->inv_serie->producto->nombre)) {{ $det->inv_serie->producto->nombre }} @endif</div>--}}
                                {{$det->nombre_generico}}
                        </th>
                        <th class="padd" style="width: 12.8%">
                            <div class="details_product" style="font-weight:normal; text-align: left;font-size:13px">
                                @if ($det->especificacion_tecnica != null)
                                    {{ $det->especificacion_tecnica }}
                                @else
                                    @if(isset($det->inv_serie->producto->descripcion)) {{ $det->inv_serie->producto->descripcion }} @endif
                                @endif
                            </div>
                        </th>
                        <th class="padd" style="width: 12.8%">
                            <div class="details_product" style="font-weight:normal; text-align: right;font-size:13px">
                                @if ($det->especificacion_tecnica_ofertada != null)
                                    {{ $det->especificacion_tecnica_ofertada }}
                                @else
                                    @if(isset($det->inv_serie->producto->descripcion)) {{ $det->inv_serie->producto->descripcion }} @endif
                                @endif
                            </div>
                        </th>
                        <th class="padd" style="width: 12.8%">
                            <div class="details_product"
                                style="font-weight:normal; text-align: center;font-size:13px">
                                {{ $det->registro_sanitario }}
                        </th>
                        <th class="padd" style="width: 12.8%">
                            <div class="details_product" style="font-weight:normal;text-align:left;font-size:13px">
                                {{ $det->cantidad }}</div>
                        </th>
                        <th class="padd" style="width: 12.8%">
                            <div class="details_product" style="font-weight:normal; text-align: left;font-size:13px">
                                @php
                                    $numero_formateado = number_format($det->precio, 2, ',', '.');
                                @endphp
                                {{ "$ " . $numero_formateado }}
                            </div>
                        </th>
                        <th class="padd" style="width: 12.8%">
                            @php
                                $precio_total = number_format($precio_total, 2, ',', '.');
                            @endphp
                            <div class="details_product" style="font-weight:normal; text-align: left;font-size:13px">
                                {{ "$ " . $precio_total }}
                            </div>
                        </th>
                    </tr>


                    <tr>
                        <td style="border:1px solid gray;border-radius:10px;width:100%" colspan="8">
                            <div style="text-align:center">
                                {{--@if(isset($det->inv_serie->producto->codigo)) {{ $det->inv_serie->producto->codigo }}  @endif --}}
                                {{ $det->codigo }}
                            </div>
                        </td>
                    </tr>

                    @php
                        $subtotal12 += $det->base12;
                        $subtotal0 += $det->base0;
                        $descuento += $det->descuento;
                        $total += $det->total;
                        if ($det->iva == 1) {
                            $iva_total += $det->base12 * $det->porct_iva;
                        }

                    @endphp
                @endforeach
            </table>
        </div>
        <div class="margenes">
            <table style="width: 100%;border:4px solid gray;border-radius:10px;">
                <tr>
                    <td>
                        <span class="texto_f2">FORMA DE PAGO: {{ $proforma->forma_pago }}</span>
                    </td>
                    <td>
                        <span class="texto_f2">PROCEDENCIA:NANJING-CHINA</span>
                    </td>
                    <td style="text-align: right">
                        @php
                            $subtotal12_t = number_format($subtotal12, 2, ',', '.');
                        @endphp
                        <span class="texto_f2">SUBTOTAL {{ "$ " . $subtotal12_t }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="texto_f2">TIEMPO DE ENTREGA: {{ $proforma->tiempo_entrega }} DIAS HABILES</span>
                    </td>
                    <td>
                        <span class="texto_f2">MARCA: MICROTECH</span>
                    </td>

                    <td style="text-align: right">
                        @php
                            $iva_12t = number_format($subtotal12, 2, ',', '.');
                        @endphp
                        <span class="texto_f2">IVA 12% {{ "$" . $iva_total }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="texto_f2">GARANTIA: {{ $proforma->garantia }}</span>
                    </td>
                    <td>
                        <span class="texto_f2">VIGENCIA DE LA OFERTA: {{ $proforma->vigencia_oferta }}</span>
                    </td>
                    <td style="text-align: right">
                        <span class="texto_f2">TOTAL {{ "$" . number_format($total, 2, ',', '.') }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="margenes" style="margin-top: 15px !important;">
            <table>
                <tr>
                    <td style="text-align: center">
                        <span style="font-size: 14px">ATENTAMENTE</span>
                    </td>
                </tr>
            </table>



            <table style="margin-top: 23px !important">
                <tr>
                    <td style="text-align: center">
                        <span style="font-size: 14px">________________________________</span>
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <td style="text-align: center">
                        <span style="font-size: 14px">ING. JUAN MANUEL DE LEON</span>
                    </td>
                </tr>
                <tr>

                    <td style="text-align: center">
                        <span style="font-size: 14px">CI: 0930790720</span>
                    </td>
                </tr>

                <tr>
                    <td style="text-align: center">
                        <span style="font-size: 14px">GERENTE GENERAL</span>
                    </td>
                </tr>

                <tr>
                    <td style="text-align: center">
                        <span style="font-size: 14px">GASTRITECH</span>
                    </td>
                </tr>
            </table>

        </div>

        <div class="footer_img">
            <img src="{{ base_path() . '/storage/app/logo/imagen_footer.png' }}" style="object-fit: cover">
        </div>
    </div>
</body>

</html>
