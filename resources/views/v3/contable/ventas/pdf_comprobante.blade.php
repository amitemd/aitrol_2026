
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura de Venta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Assistant&display=swap" rel="stylesheet">
    <style>
        #page_pdf {
            width: 100%;
            font-family: 'Assistant', sans-serif;
        }

        /* importante */
        .factura_head {
            width: 100%;
            margin-bottom: 10px;
            font-size: 0.7em
        }



        .container_client {
            display: grid;
            grid-template-columns: 0.5fr 0.5fr;
        }

        .cont {
            background-color: black !important;
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

        .espacio {
            border: 1px solid;
            padding: 5px
        }

        .letra {
            font-size: 10px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .signature-table td {
            width: 33%;
            vertical-align: bottom;
        }

        .signature-line {
            border-bottom: 1px solid black;
            display: inline-block;
            width: 80%;
            margin-bottom: 5px;
        }

        .signature-label {
            display: block;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: white;
            border-top: 1px solid #000;
            text-align: left;
            padding: 10px;
            box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.1);
        }

        .page-number {
            float: right;
        }

        .double-line {
            position: relative;
            height: 1px;
            background-color: transparent;
            margin: 20px 0;
        }

        .double-line::before,
        .double-line::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            height: 1px;
            background-color: black;
        }

        .double-line::before {
            top: -2px;
        }

        .double-line::after {
            top: 2px;
        }
    </style>

</head>
<?php
$fecha = date("Y/m/d h:i:s");
$protocol = isset($_SERVER['HTTPS']) &&
    $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$base_url = $protocol . $_SERVER['HTTP_HOST'] . '/';
?>

<body>
    <div id="page_pdf">
        <table class="factura_head">
            <tr>
                <td class="info_empresa" style="width:33.33%">
                    <?php
                    if ($venta->empresa != null) {
                        echo '<img src="' . base_path() . '/storage/app/logo/' . $venta->empresa->logo . '" style="width:150px;height:40px">';
                    }
                    ?>
                </td>
                <td class="info_empresa" style="width:33.33%"></td>

                <td class="info_empresa" style="width:33.33%">
                    <table>
                        <tr>
                            <td style="font-size: 10px;">
                                <b>{{$venta->empresa->razonsocial}}</b>
                            </td>
                        </tr>
                        <!-- <tr>
                            <td>
                                <span style="font-size:10px">{{$user->nombre1}} {{$user->nombre2}} {{$user->apellido1}} {{$user->apellido2}}</span>
                            </td>
                        </tr> -->
                        <tr>
                            <td>
                                <span style="font-size: 10px;">{{$venta->empresa->id}}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-size: 10px;">Fecha de impresion: {{$fecha}}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div class="double-line"></div>
        <table class="factura_head">
            <tr>
                <td style="text-align: center;">
                    <h2><b>Comprobante de venta: {{$venta->nro_comprobante}}</b></h2>
                </td>
            </tr>
        </table>
        <table style="width: 83%;">
            <tr>
                <td style="width:25%">
                    <span class="letra"><b>Fecha de emisión:</b></span>
                </td>
                <td>
                    <span class="letra">{{$venta->fecha}}</span>
                </td>
                <td style="width:25%">
                    <span class="letra"><b>Vencimiento:</b></span>
                </td>
                <td>
                    <span class="letra">{{$venta->dias_plazo}} días</span>
                </td>
            </tr>
            <tr>
                <td style="width:25%">
                    <span class="letra"><b>Documento:</b></span>
                </td>
                <td>
                    <span class="letra">FAC {{$venta->nro_comprobante}}</span>
                </td>
            </tr>
        </table>
        <table style="width: 83%;">
            <tr>
                <td style="width:25%">
                    <span class="letra"><b>Cliente:</b></span>
                </td>
                <td style="width: 75%;">
                    <span class="letra">{{$venta->cliente->nombre}}</span>
                </td>
            </tr>
        </table>
        <table style="width: 83%;">
            <tr>
                <td style="width:25%">

                    <span class="letra"><b>RUC: </b></span>
                </td>
                <td>
                    <span class="letra">{{$venta->cliente->identificacion}}</span>
                </td>
            </tr>
        </table>
        <table style="width: 83%;">
            <tr>
                <td style="width:25%">
                    <span class="letra"><b>Teléfonos:</b></span>
                </td>
                <td>
                    <span class="letra">{{$venta->cliente->telefono1_representante }}</span>
                </td>
            </tr>
            <tr>
                <td style="width:25%">
                    <span class="letra"><b>Estado:</b></span>
                </td>
                <td>
                    <span class="letra">{{$venta->pasos->nombre}}</span>
                </td>
            </tr>
            <tr>
                <td style="width:25%">
                    <span class="letra"><b>Dirección:</b></span>
                </td>
                <td>
                    <span class="letra">@if($venta->cliente->direccion_cliente == null) N/A @else{{$venta->cliente->direccion_cliente}} @endif</span>
                </td>
            </tr>
        </table>
        <table class="factura_head">
            <tr>
                <td>
                    <h4 style="text-decoration: underline;"><i><b>Detalle bienes/servicios</b></i></h4>
                </td>
            </tr>
        </table>
        <table class="factura_head" style="border: 1px solid; border-collapse: collapse; width: 100%;">
            <tr>
                <td class="espacio">
                    <b>Cantidad</b>
                </td>
                <td class="espacio">
                    <b>Código</b>
                </td>
                <td class="espacio">
                    <b>Bien/Servicio</b>
                </td>
                <td class="espacio">
                    <b>Detalle</b>
                </td>
                <td class="espacio">
                    <b>Precio</b>
                </td>
                <td class="espacio">
                    <b>% desc</b>
                </td>
                <td class="espacio">
                    <b>Desc</b>
                </td>
                <td class="espacio">
                    <b>Subtotal</b>
                </td>
            </tr>
            @foreach($venta->detalles as $val)
            <tr>
                <td class="espacio">
                    <span class="letra">{{$val->cantidad}}</span>
                </td>
                <td class="espacio">
                    <span class="letra">{{$val->id_ct_productos}}</span>
                </td>
                <td class="espacio">
                    <span class="letra">{{$val->nombre}}</span>
                </td>
                <td class="espacio">
                    <span class="letra">{{$val->descripcion_producto}}</span>
                </td>
                <td class="espacio">
                    <span class="letra">{{$val->precio}}</span>
                </td>
                <td class="espacio">
                    <span class="letra">{{$val->descuento_porcentaje}}</span>
                </td>
                <td class="espacio">
                    <span class="letra">{{$val->descuento}}</span>
                </td>
                <td class="espacio">
                    <span class="letra">{{$val->precio}}</span>
                </td>
            </tr>
            @endforeach
        </table>
        <table class="factura_head">
            <tr>
                <td>
                    <h4 style="text-decoration: underline;"><i><b>Asiento venta</b></i></h4>
                </td>
            </tr>
        </table>
        <table class="factura_head" style="border: 1px solid; border-collapse: collapse; width: 100%;">
            <tr>
                <td class="espacio">
                    <b>Id</b>
                </td>
                <td class="espacio">
                    <b>Cuenta</b>
                </td>
                <td class="espacio" style="text-align: right;">
                    <b>Debe</b>
                </td>
                <td class="espacio" style="text-align: right;">
                    <b>Haber</b>
                </td>
                <td class="espacio">
                    <b>Centro de Costo</b>
                </td>
            </tr>
            @foreach($queryAsientos as $val)
            <tr>
                <td class="espacio">
                    <span class="letra">{{$val->plan}}</span>
                </td>
                <td class="espacio">
                    <span class="letra">{{$val->nombre}}</span>
                </td>
                <td class="espacio" style="text-align:right;">
                    <span class="letra">{{$val->debe}}</span>
                </td>
                <td class="espacio" style="text-align: right;">
                    <span class="letra">{{$val->haber}}</span>
                </td>
                <td class="espacio">
                    <span class="letra"></span>
                </td>
            </tr>
            @endforeach
        </table>
        @if($retencion!=null)
        <table class="factura_head">
            <tr>
                <td>
                    <h4 style="text-decoration: underline;"><i><b>Detalle retención</b></i></h4>
                </td>
            </tr>
        </table>
        <table class="factura_head" style="width: 100%;">
            <tr>
                <td><b>Fecha:</b> {{$retencion->fecha}}</td>
                <td><b>Comprobante:</b> {{$retencion->nro_comprobante}}</td>
            </tr>
            <tr>
                <td colspan="2"><b>Autorizacón:</b> {{$retencion->autorizacion}}</td>
            </tr>
        </table>
        <table class="factura_head" style="border: 1px solid; border-collapse: collapse; width: 100%;">
            <tr>
                <td class="espacio" style="text-align: right;">
                    <b>Tipo</b>
                </td>
                <td class="espacio" style="text-align: right;">
                    <b>Cod. SRI</b>
                </td>
                <td class="espacio">
                    <b>Base</b>
                </td>
                <td class="espacio">
                    <b>%</b>
                </td>
                <td class="espacio">
                    <b>Valor</b>
                </td>
            </tr>
            @foreach($retencion->detalle as $val)
            <tr>
                <td class="espacio">
                    <span class="letra">{{$val->tipo}}</span>
                </td>
                <td class="espacio" style="text-align: right;">
                    <span class="letra">{{$val->codigo}}</span>
                </td>
                <td class="espacio" style="text-align: right;">
                    <span class="letra">{{$val->base_imponible}}</span>
                </td>
                <td class="espacio" style="text-align: right;">
                    <span class="letra">{{$val->porcentaje}}</span>
                </td>
                <td class="espacio" style="text-align: right;">
                    <span class="letra">{{$val->totales}}</span>
                </td>
            </tr>
            @endforeach
        </table>
        @endif
        @if($retencionAsiento !== null)
            @if(count($retencionAsiento)>0)
            <table class="factura_head">
                <tr>
                    <td>
                        <h4 style="text-decoration: underline;"><i><b>Asiento retención</b></i></h4>
                    </td>
                </tr>
            </table>
            <table class="factura_head" style="border: 1px solid; border-collapse: collapse; width: 100%;">
                <tr>
                    <td class="espacio">
                        <b>Id</b>
                    </td>
                    <td class="espacio">
                        <b>Cuenta</b>
                    </td>
                    <td class="espacio" style="text-align: right;">
                        <b>Debe</b>
                    </td>
                    <td class="espacio" style="text-align: right;">
                        <b>Haber</b>
                    </td>
                    <td class="espacio">
                        <b>Centro de Costo</b>
                    </td>
                </tr>
                @foreach($retencionAsiento as $val)
                <tr>
                    <td class="espacio">
                        <span class="letra">{{$val->plan}}</span>
                    </td>
                    <td class="espacio">
                        <span class="letra">{{$val->nombre}}</span>
                    </td>
                    <td class="espacio" style="text-align:right;">
                        <span class="letra">{{$val->debe}}</span>
                    </td>
                    <td class="espacio" style="text-align: right;">
                        <span class="letra">{{$val->haber}}</span>
                    </td>
                    <td class="espacio">
                        <span class="letra"></span>
                    </td>
                </tr>
                @endforeach
            </table>
            @endif
        @endif

  

        @if(count($queryMovimiento)>0)
            <table class="factura_head">
                <tr>
                    <td>
                        <h4 style="text-decoration: underline;"><i><b>Asiento inventario</b></i></h4>
                    </td>
                </tr>
            </table>
            <table class="factura_head" style="border: 1px solid; border-collapse: collapse; width: 100%;">
                <tr>
                    <td class="espacio">
                        <b>Id</b>
                    </td>
                    <td class="espacio">
                        <b>Cuenta</b>
                    </td>
                    <td class="espacio" style="text-align: right;">
                        <b>Debe</b>
                    </td>
                    <td class="espacio" style="text-align: right;">
                        <b>Haber</b>
                    </td>
                    <td class="espacio">
                        <b>Centro de Costo</b>
                    </td>
                </tr>
                @foreach($queryMovimiento as $val)
                <tr>
                    <td class="espacio">
                        <span class="letra">{{$val->plan}}</span>
                    </td>
                    <td class="espacio">
                        <span class="letra">{{$val->nombre}}</span>
                    </td>
                    <td class="espacio" style="text-align:right;">
                        <span class="letra">{{$val->debe}}</span>
                    </td>
                    <td class="espacio" style="text-align: right;">
                        <span class="letra">{{$val->haber}}</span>
                    </td>
                    <td class="espacio">
                        <span class="letra"></span>
                    </td>
                </tr>
                @endforeach
            </table>
        @endif
        <table class="factura_head">
            <tr>
                <td>
                    <h4 style="text-decoration: underline;"><i><b>Formas de Pago</b></i></h4>
                </td>
            </tr>
        </table>
        <table class="factura_head" style="border: 1px solid; border-collapse: collapse; width: 100%;">
            <tr>
                <td class="espacio">
                    <b>Forma de pago</b>
                </td>
                <td class="espacio" style="text-align: right;">
                    <b>Plazo</b>
                </td>
                <td class="espacio" style="text-align: right;">
                    <b>Unid. tiempo</b>
                </td>
                <td class="espacio" style="text-align: right;">
                    <b>Valor</b>
                </td>
            </tr>
            @foreach($formasPago as $val)
            <tr>
                <td class="espacio">
                    <span class="letra">{{$val->tipoPago->nombre}}</span>
                </td>
                <td class="espacio" style="text-align: right;">
                    <span class="letra">{{$venta->dias_plazo}}</span>
                </td>
                <td class="espacio" style="text-align: right;">
                    <span class="letra">días</span>
                </td>
                <td class="espacio" style="text-align: right;">
                    <span class="letra">{{$val->valor}}</span>
                </td>
            </tr>
            @endforeach
        </table>
        <table class="factura_head">
            <tr style="line-height: 10px;">
                <td style="width: 10%;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"><b>Descripción:</b></h5>
                </td>
                <td style="width: 65%; line-height: 10px;">{{$venta->concepto}}</td>
                <td style="line-height: 10px;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"><b>Subtotal 15%:</b></h5>
                </td>
                <td style="line-height: 10px;text-align:right">$ {{number_format($subtotal15,2)}}</td>
            </tr>
            <tr style="line-height: 10px;">
                <td style="width: 10%;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"></b></h5>
                </td>
                <td style="width: 65%; line-height: 10px;"></td>
                <td style="line-height: 10px;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"><b>Subtotal 5%:</b></h5>
                </td>
                <td style="line-height: 10px;text-align:right">
                    {{'$' . number_format($subtotal5, 2)}}
                </td>
            </tr>
            <tr style="line-height: 10px;">
                <td style="width: 10%;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"></b></h5>
                </td>
                <td style="width: 65%; line-height: 10px;"></td>
                <td style="line-height: 10px;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"><b>Subtotal 0%:</b></h5>
                </td>
                <td style="line-height: 10px;text-align:right">
                    {{'$' . number_format($subtotal, 2)}}
                </td>
            </tr>
            <tr style="line-height: 10px;">
                <td style="width: 10%;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"></b></h5>
                </td>
                <td style="width: 65%; line-height: 10px;"></td>
                <td style="line-height: 10px;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"><b>Descuento:</b></h5>
                </td>
                <td style="line-height: 10px;text-align:right">
                    {{'$' . number_format($descuento, 2)}}
                </td>
            </tr>
            <tr style="line-height: 10px;">
                <td style="width: 10%;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"></b></h5>
                </td>
                <td style="width: 65%; line-height: 10px;"></td>
                <td style="line-height: 10px;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"><b>Subtotal:</b></h5>
                </td>
                <td style="line-height: 10px;text-align:right">
                    {{'$' . number_format($resTotal, 2)}}
                </td>
            </tr>
            <tr style="line-height: 10px;">
                <td style="width: 10%;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"></b></h5>
                </td>
                <td style="width: 65%; line-height: 10px;"></td>
                <td style="line-height: 10px;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"><b>IVA 15%:</b></h5>
                </td>
                <td style="line-height: 10px;text-align:right">
                    {{'$' . number_format($iva15, 2)}}
                </td>
            </tr>
            <tr style="line-height: 10px;">
                <td style="width: 10%;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"></b></h5>
                </td>
                <td style="width: 65%; line-height: 10px;"></td>
                <td style="line-height: 10px;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"><b>IVA 5%:</b></h5>
                </td>
                <td style="line-height: 10px;text-align:right">
                    {{'$' . number_format($iva5, 2)}}
                </td>
            </tr>
            <tr style="line-height: 10px;">
                <td style="width: 10%;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"></b></h5>
                </td>
                <td style="width: 65%; line-height: 10px;text-align:right"></td>
                <td style="line-height: 10px;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"><b>Total:</b></h5>
                </td>
                <td style="line-height: 10px;text-align:right">
                    {{'$' . number_format($venta->total_final, 2)}}
                </td>
            </tr>
            <tr style="line-height: 10px;">
                <td style="width: 10%;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"></b></h5>
                </td>
                <td style="width: 65%; line-height: 10px;"></td>
                <td style="line-height: 10px;">
                    <h5 style="margin: 0; padding: 0; line-height: 10px;"><b>Saldo:</b></h5>
                </td>
                <td style="line-height: 10px;text-align:right">
                    {{'$' . number_format($venta->saldo, 2)}}
                </td>
            </tr>
        </table>
        <table class="signature-table" style="margin-top: 40px;">
            <tr>
                <td>
                    <span class="signature-line" style="font: 11px;">{{$venta->usuario->nombre1}} {{$venta->usuario->apellido1}}</span>
                    <span class="signature-label" style="font-size:10px;text-align:left">Elaborado por:</span>
                </td>
                <td>
                    <span class="signature-line"></span>
                    <span class="signature-label" style="font-size:10px;text-align:left">Aprobado por:</span>
                </td>
                <td>
                    <span class="signature-line"></span>
                    <span class="signature-label" style="font-size:10px;text-align:left">Revisado por:</span>
                </td>
            </tr>
        </table>
        <footer class="footer">
            <span style="font-size: 12px;"><a href="{{$base_url}}">Aitrol v3</a> | Contabilidad en línea.</span> <span class="page-number" style="font-size: 12px;">Página 1</span>
        </footer>
    </div>
</body>

</html>