<?php

use Sis_medico\Http\Controllers\EmisionDocumentosController;

$xmlComprobamte = simplexml_load_string($xml->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->comprobante);
//dd($xml);
$xmlDetalles = $xmlComprobamte->detalles->detalle;
//dd($xmlComprobamte);
$barcode = EmisionDocumentosController::setBarcode($xmlComprobamte->infoTributaria->claveAcceso, '/storage/app/facturaelectronica/barcode/');
$barcode = '<img src="' . base_path() . $barcode . '" />';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $xmlComprobamte->infoTributaria->claveAcceso; ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        body {
            margin: 0;
            margin-left: -20px;
            padding: 0;
            background-color: transparent;
            font: 9pt "Tahoma";
            font-size: 9px;
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .page {
            width: 21cm;
            padding: 1cm;
            margin: 1cm auto;
            margin-left: -20px;
        }

        .subpage {
            padding: 1cm;
            border: 5px red solid;
            height: 256mm;
            outline: 2cm #FFEAEA solid;
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {
            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="col-md-12">
            <br />
            <table style="width: 100%;">
                <tr>
                    <td style="width:50%;">
                        <table style="width:100%;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center"><label style="font-family: 'Arial Black', 'Arial Bold', Gadget, sans-serif;font-size: 38px;color: red;font-weight: bold;">NO TIENE LOGO</label></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="left" style="width: 100%;">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td colspan="2"><?= $xmlComprobamte->infoTributaria->razonSocial; ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><?= $xmlComprobamte->infoTributaria->nombreComercial; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Dirercción:</td>
                                            <td><?= substr($xmlComprobamte->infoTributaria->dirMatriz, 0, 54); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Dirercción: </td>
                                            <td><?= $xmlComprobamte->infoFactura->dirEstablecimiento; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Obligado a llevar contabilidad: </td>
                                            <td><?= $xmlComprobamte->infoFactura->obligadoContabilidad; ?></td>
                                        </tr>
                                        <?php
                                        if (isset($xmlComprobamte->infoTributaria->agenteRetencion)) {
                                        ?>
                                            <tr>
                                                <td>Agente de Retención Resolución: </td>
                                                <td><?= $xmlComprobamte->infoTributaria->agenteRetencion; ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:50%;">
                        <table style="width:100%;" border="1">
                            <tr>
                                <td>
                                    <table cellpadding="0" cellspacing="0" style="font-size: 14px;font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;">
                                        <tr>
                                            <td style="padding: 0px 10px 0px 0px;">R.U.C.: </td><!-- top-->
                                            <td><?= $xmlComprobamte->infoTributaria->ruc; ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">FACTURA</td>
                                        </tr>
                                        <tr>
                                            <td>No.</td>
                                            <td><?= $xmlComprobamte->infoTributaria->estab . '-' . $xmlComprobamte->infoTributaria->ptoEmi . '-' . $xmlComprobamte->infoTributaria->secuencial; ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">NÚMERO DE AUTORIZACIÓN</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><?= $xml->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion; ?></td>
                                        </tr>
                                        <tr>
                                            <td>FECHA Y HORA DE AUTORIZACIÓN:</td>
                                            <td><?= date('d/m/Y H:i:s', strtotime($xml->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion)); ?></td>
                                        </tr>
                                        <tr>
                                            <td>AMBIENTE:</td>
                                            <td><?= $xml->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente; ?></td>
                                        </tr>
                                        <tr>
                                            <td>EMISIÓN:</td>
                                            <td>NORMAL</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">CLAVE DE ACCESO</td>
                                        </tr>
                                        <tr>
                                            <td align="center" colspan="3" style="font-size: 10;">
                                                <?= $barcode; ?>
                                                <small>{{ $xmlComprobamte->infoTributaria->claveAcceso }}</small>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%;" colspan="2">
                        <table style="width:100%;">
                            <tr>
                                <td>Razón Social / Nombres y:</td>
                                <td>{{ $xmlComprobamte->infoFactura->razonSocialComprador }}</td>
                                <td>CI/RUC:</td>
                                <td>{{ $xmlComprobamte->infoFactura->identificacionComprador }}</td>
                            </tr>
                            <tr>
                                <td>Fecha emisión:</td>
                                <td>{{ $xmlComprobamte->infoFactura->fechaEmision }}</td>
                                <?php
                                if (isset($xmlComprobamte->infoFactura->placa)) {
                                ?>
                                    <td>Placa:</td>
                                    <td><?= $xmlComprobamte->infoFactura->placa; ?></td>
                                <?php
                                }
                                if (isset($xmlComprobamte->infoFactura->guiaRemision)) {
                                ?>
                                    <td>Guia:</td>
                                    <td><?= $xmlComprobamte->infoFactura->guiaRemision; ?></td>
                                <?php
                                }
                                ?>
                            </tr>
                            <tr>
                                <td>Dirección:</td>
                                <td colspan="4"><?= $xmlComprobamte->infoFactura->direccionComprador; ?></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <hr>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table style="width:100%;" cellspacing="0" cellpadding="0">
                <tr>
                    <th>Cod principal</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>P. unitario</th>
                    <th>Descuento</th>
                    <th>P. total</th>
                </tr>
                <?php
                $subtotal0 = 0;
                $subtotal12 = 0;
                foreach ($xmlDetalles as $row) {
                    if ($row->impuestos->impuesto->tarifa == 12.00)
                        $subtotal12 = $subtotal12 + floatval($row->precioTotalSinImpuesto);
                    else
                        $subtotal0 = $subtotal0 + floatval($row->precioTotalSinImpuesto);
                    $deadi = '';
                    $detaAdi = '';
                    echo '<tr style="font-size:10px;">
                            <td>' . $row->codigoPrincipal . '</td>
                            <td valign="top"><p>' . $row->descripcion . $detaAdi . '</p></td>
                            <td align="right">' . $row->cantidad . '</td>
                            <td align="right">' . $row->precioUnitario . '</td>
                            <td align="right">' . $row->descuento . '</td>
                            <td align="right">' . $row->precioTotalSinImpuesto . '</td>
                        </tr>';
                }
                ?>
            </table>
            <table style="width:100%;" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%;" colspan="2">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 70%;" valign="top">

                                    <table>
                                        <tr>
                                            <td>
                                                <table>
                                                    <tr>
                                                        <td><b>Forma de pago:</b></td>
                                                    </tr>

                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 40%;">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td align="right">Subtotal 12%</td>
                                            <td align="right">{{ number_format($subtotal12,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Subtotal 0%</td>
                                            <td align="right">{{number_format($subtotal0,2)}}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Subtotal no objeto de IVA</td>
                                            <td align="right">{{ number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Subtotal Exento de IVA</td>
                                            <td align="right">{{ number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Total sin impuestos</td>
                                            <td align="right">{{ $xmlComprobamte->infoFactura->totalSinImpuestos }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Descuento</td>
                                            <td align="right">{{ $xmlComprobamte->infoFactura->totalDescuento }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">ICE</td>
                                            <td align="right">{{ number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">IVA 12%</td>
                                            <!--<td align="right">{{ $xmlComprobamte->infoFactura->totalConImpuestos->totalImpuesto->tarifa==12.00?$xmlComprobamte->infoFactura->totalConImpuestos->totalImpuesto->valor:number_format(0,2) }}</td>-->
                                            <td align="right">{{ $xmlComprobamte->infoFactura->totalConImpuestos->totalImpuesto->valor!=0.00?$xmlComprobamte->infoFactura->totalConImpuestos->totalImpuesto->valor:number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Total devolución IVA</td>
                                            <td align="right">{{ number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">IRBPNR</td>
                                            <td align="right">{{ number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Propina</td>
                                            <td align="right">{{ $xmlComprobamte->infoFactura->propina }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Valor total</td>
                                            <td align="right">{{ $xmlComprobamte->infoFactura->importeTotal }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>