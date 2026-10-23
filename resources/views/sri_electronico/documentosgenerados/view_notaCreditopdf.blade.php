<?php

use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Nota_Credito_Clientes;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\De_Empresa;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\EmisionDocumentosController;

$xml = simplexml_load_file($xml);
$porcentajeIva = $porIva->iva * 100;
$valorIva = $porIva->iva;
$xmlComprobamte = simplexml_load_string($xml->autorizaciones->autorizacion->comprobante);
EmisionDocumentosController::crearcarpeta(base_path() . '/storage/app/facturaelectronica/barcode/' . $xmlComprobamte->infoTributaria->ruc . '/factura/');
$barcode = EmisionDocumentosController::setBarcode($xmlComprobamte->infoTributaria->claveAcceso, '/storage/app/facturaelectronica/barcode/' . $xmlComprobamte->infoTributaria->ruc . '/factura/');
$barcode = '<img src="' . base_path() . $barcode . '" />';
$empresa = Empresa::where('id', $xmlComprobamte->infoTributaria->ruc)->first();
$deEmpresa = De_Empresa::where('id_empresa', $xmlComprobamte->infoTributaria->ruc)->first();
$cliente = Ct_Clientes::getCliente($xmlComprobamte->infoNotaCredito->identificacionComprador);
$xmlDetalles = $xmlComprobamte->detalles->detalle;
$logo = '/storage/app/logo/' . $empresa->logo;
$logo = '<img src="' . base_path() . $logo . '" style="width:200px;" />';
$totalSinImpuesto = 0;
$validPerido = new EmisionDocumentosController();
$fechaEmisionDoc = date('Y-m-d', strtotime(str_replace('/', '-', (string)$xmlComprobamte->infoNotaCredito->fechaEmision)));
$validPerido = $validPerido->validarPeriodo($fechaEmisionDoc);
if (!$validPerido) {
    $porcentajeIva = 0.12 * 100;
    $valorIva = 0.12;
} else {
    $porcentajeIva = $porIva->iva * 100;
    $valorIva = $porIva->iva;
}
$establecimiento = (int)$xmlComprobamte->infoTributaria->estab;
$emision = (int)$xmlComprobamte->infoTributaria->ptoEmi;
$secuencial = (int)$xmlComprobamte->infoTributaria->secuencial;
$notaCredito = Ct_Nota_Credito_Clientes::where('id_empresa', $empresa->id)->where('sucursal', str_pad($establecimiento, 3, 0, STR_PAD_LEFT))->where('punto_emision',  str_pad($emision, 3, 0, STR_PAD_LEFT))->where('secuencia', $secuencial)->first(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $xmlComprobamte->infoTributaria->claveAcceso; ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        body {
            margin: 0;
            margin-left: -20px;
            padding: 0;
            background-color: white;
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
            <table style="width:100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center">
                        <table align="center" style="font-size: 0.800em;width:100%;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" valign="top"><?= $logo ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center"><b><?= $xmlComprobamte->infoTributaria->razonSocial; ?></b></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">R.U.C.:</b> <?= $xmlComprobamte->infoTributaria->ruc; ?></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">Dir Matriz:</b> <?= $xmlComprobamte->infoTributaria->dirMatriz; ?></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">Obligado a llevar contabilidad:</b> <?= $xmlComprobamte->infoNotaCredito->obligadoContabilidad; ?></td>
                            </tr>
                            <tr><?php if (isset($xmlComprobamte->infoNotaCredito->contribuyenteEspecial)) {
                                    echo '<td><b style="font-size: 10;">Contribuyente especial:</b> ' . $xmlComprobamte->infoNotaCredito->contribuyenteEspecial . '</td>';
                                } else {
                                    echo '<td></td>';
                                } ?></tr><?php if ($deEmpresa->agente_retencion != 0) {
                                                echo '<td><b style="font-size: 10;">Agente de Retención:</b> 1</td>';
                                            } ?>
                        </table>
                    </td>
                    <td align="center">
                        <table style="width:100%;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center">
                                    <table style="font-size: 0.800em;" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center" style="font-size: 26px;" colspan="3"><b>Nota de Credito</b></td>
                                        </tr>
                                        <tr>
                                            <td align="center" style="font-size: 18px;" colspan="3"><?= $xmlComprobamte->infoTributaria->estab; ?>-<?= $xmlComprobamte->infoTributaria->ptoEmi; ?>-<?= $xmlComprobamte->infoTributaria->secuencial; ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="font-size: 10;"><b>Autorización número:</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="font-size: 10;"><small><?= $xml->claveAccesoConsultada; ?></small></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10;"><b>Fecha y Hora:</b></td>
                                            <td><?= date('d/m/Y H:i:s', strtotime($xml->autorizaciones->autorizacion->fechaAutorizacion)); ?></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10;"><b>Ambiente:</b></td>
                                            <td colspan="2"><?= $xml->autorizaciones->autorizacion->ambiente; ?></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10;"><b>Emisión:</b></td>
                                            <td colspan="2">NORMAL</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="font-size: 10;"><b>Clave de Acceso:</b></td>
                                        </tr>
                                        <tr>
                                            <td align="center" colspan="3" style="font-size: 10;"><?= $barcode; ?><small>{{ $xmlComprobamte->infoTributaria->claveAcceso }}</small></td>
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
            </table>
            <table style="width:100%;" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="4" style="width: 100%;">
                        <table style="width:100%;" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>Razón social:</td>
                                <td>{{ $cliente->nombre }}</td>
                                <td>CI/RUC:</td>
                                <td>{{ $cliente->identificacion }}</td>
                            </tr>
                            <tr>
                                <td>Fecha emisión:</td>
                                <td>{{ $xmlComprobamte->infoNotaCredito->fechaEmision }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <hr>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="width: 100%;">
                        <table style="width:100%;" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>Comprobante que se modifica:</td>
                                <td>{{ $xmlComprobamte->infoNotaCredito->numDocModificado }}</td>
                                <td>Fecha emisión comprobante:</td>
                                <td>{{ $xmlComprobamte->infoNotaCredito->fechaEmisionDocSustento }}</td>
                            </tr>
                            <tr>
                                <td>Razón de modificación:</td>
                                <td>{{ $xmlComprobamte->infoNotaCredito->motivo }}</td>
                                <td></td>
                                <td></td>
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
                    <th>Precio unitario</th>
                    <th>Descuento</th>
                    <th>Precio total</th>
                </tr><?php $subtotal0 = 0;
                        $subtotal12 = 0;
                        $descu = 0;
                        $iva = 0;
                        foreach ($xmlDetalles as $row) {
                            if ($row->impuestos->impuesto->tarifa > 0) {
                                $subtotal12 = $subtotal12 + ($row->cantidad * (float)$row->precioUnitario);
                                $iva = $iva + (float)$xmlComprobamte->infoNotaCredito->totalConImpuestos->totalImpuesto->valor;
                            } else {
                                $subtotal0 = $subtotal0 + ($row->cantidad * (float)$row->precioUnitario);
                            }
                            $descu += $row->descuento;
                            echo '<tr><td align="center">' . $row->codigoInterno . '</td><td align="center">' . $row->descripcion . '</td><td align="center">' . $row->cantidad . '</td><td align="center">' . $row->precioUnitario . '</td><td align="center">' . $row->descuento . '</td><td align="center">' . $row->precioTotalSinImpuesto . '</td></tr>';
                            $totalSinImpuesto = (float)$subtotal12 + (float)$subtotal0 - (float)$descu;
                        } ?>
            </table>
            <table style="width: 100%;" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td style="width: 70%;" valign="top">
                        <table>
                            <tr>
                                <td>Información adicional</td>
                            </tr>
                            <tr>
                                <td>
                                    <table><?php $cont = 0;
                                            $adicionales = De_Documentos_Electronicos::getInfoAdicional($notaCredito->id, $xml->claveAccesoConsultada);
                                            $detAdic = json_decode($adicionales->infoAdicional);
                                            foreach ($detAdic as $row) {
                                                echo '<tr><td>' . ucwords(strtolower($row->nombre)) . ': </td><td>' . $row->valor . '</td></tr>';
                                                $cont++;
                                            } ?></table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 40%;">
                        <table style="width: 100%;">
                            <tr>
                                <td align="right">Subtotal {{$porcentajeIva}}%</td>
                                <td align="right">{{ number_format($subtotal12,2) }}</td>
                            </tr>
                            <tr>
                                <td align="right">Subtotal 0%</td>
                                <td align="right">{{ number_format($subtotal0,2) }}</td>
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
                                <td align="right">{{ number_format($totalSinImpuesto,2) }}</td>
                            </tr>
                            <tr>
                                <td align="right">Descuento</td>
                                <td align="right"><?= number_format($descu, 2); ?></td>
                            </tr>
                            <tr>
                                <td align="right">ICE</td>
                                <td align="right">{{ number_format(0,2) }}</td>
                            </tr>
                            <?php
                            //if (Auth::user()->id == '0921605895') {
                            $det = $notaCredito->detallesDP->where('iva', 1)->sum('valor_iva');
                            //}
                            ?>
                            <tr>
                                <td align="right">IVA {{$porcentajeIva}}%</td>
                                <!--<td align="right">{{ $xmlComprobamte->infoNotaCredito->totalConImpuestos->totalImpuesto->valor!=0.00?$xmlComprobamte->infoNotaCredito->totalConImpuestos->totalImpuesto->valor:number_format(0,2) }}</td>-->
                                <td align="right">{{ number_format($det,2) }}</td>
                            </tr>
                            <tr>
                                <td align="right">IRBPNR</td>
                                <td align="right">{{ number_format(0,2) }}</td>
                            </tr>
                            <tr>
                                <td align="right">Propina</td>
                                <td align="right">@if(isset($xmlComprobamte->infoNotaCredito->totalDescuento)){{ $xmlComprobamte->infoNotaCredito->propina }}@else{{ number_format(0,2) }}@endif</td>
                            </tr>
                            <tr><?php $descuento_ = isset($xmlComprobamte->detalles->detalle->descuento) ? $xmlComprobamte->detalles->detalle->descuento : 0; ?>
                            <td align="right">Valor total</td>
                                <td align="right">{{ number_format($subtotal0+$subtotal12+($xmlComprobamte->infoNotaCredito->totalConImpuestos->totalImpuesto->valor!=0.00?$xmlComprobamte->infoNotaCredito->totalConImpuestos->totalImpuesto->valor:0)-$descu,2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>