<?php

use Illuminate\Support\Facades\DB;
use Sis_medico\Proveedor;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_compras;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\De_Empresa;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\EmisionDocumentosController;

$xml = simplexml_load_file($xml);
try {
    $xmlComprobamte = simplexml_load_string($xml->autorizaciones->autorizacion->comprobante);
} catch (Exception $e) {
    //$xmlComprobamte = simplexml_load_string((string)$xml->autorizaciones->autorizacion->comprobante);

    try {
        $nxml = EmisionDocumentosController::obtenercomprobanteSRI($xml->claveAccesoConsultada, '');
        $xmlComprobamte = simplexml_load_string($nxml->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->comprobante);
    } catch (Exception $e) {
        $nxml = EmisionDocumentosController::obtenercomprobanteSRI($xml->claveAccesoConsultada, '');
        $xmlComprobamte = simplexml_load_string($nxml->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->comprobante);
    }
}
EmisionDocumentosController::crearcarpeta(base_path() . '/storage/app/facturaelectronica/barcode/' . $xmlComprobamte->infoTributaria->ruc . '/factura/');
$barcode = EmisionDocumentosController::setBarcode($xmlComprobamte->infoTributaria->claveAcceso, '/storage/app/facturaelectronica/barcode/' . $xmlComprobamte->infoTributaria->ruc . '/factura/');
$barcode = '<img src="' . base_path() . $barcode . '" />';
$empresa = Empresa::where('id', $xmlComprobamte->infoTributaria->ruc)->first();
$deEmpresa = De_Empresa::where('id_empresa', $xmlComprobamte->infoTributaria->ruc)->first();
$proveedor = Proveedor::where('id', $xmlComprobamte->infoLiquidacionCompra->identificacionProveedor)->first();
$xmlDetalles = $xmlComprobamte->detalles;
$logo = '/storage/app/logo/' . $empresa->logo;
$logo = '<img src="' . base_path() . $logo . '" style="width:200px;" />';
$totalSinImpuesto = 0;
$validPerido = new EmisionDocumentosController();
$fe = (string)$xmlComprobamte->infoLiquidacionCompra->fechaEmision;
$fe = str_replace('/', '-', $fe);
$fe = date('Y-m-d', strtotime($fe));
$validPerido = $validPerido->validarPeriodo($fe);
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

$compras = Ct_compras::where('id_empresa', $empresa->id)
    ->where('sucursal', str_pad($establecimiento, 3, 0, STR_PAD_LEFT))
    ->where('punto_emision',  str_pad($emision, 3, 0, STR_PAD_LEFT))
    ->where('secuencia_f', str_pad($secuencial, 9, 0, STR_PAD_LEFT)) //str_pad($secuencial, 9, 0, STR_PAD_LEFT)
    ->whereNotNull('nro_autorizacion')
    ->first();
// $formaPago = Ct_Forma_Pago::join('ct_tipo_pago as t', 'ct_forma_pago.tipo', '=', 't.id')
//     ->where('id_ct_ventas', $ventas->id)
//     ->get();
//dd(str_pad($establecimiento, 3, 0, STR_PAD_LEFT),str_pad($emision, 3, 0, STR_PAD_LEFT),str_pad($secuencial, 9, 0, STR_PAD_LEFT),$empresa->id);
?>
<style>
    body {
        margin: 0;
        padding: 0;
        background-color: transparent;
        font: 10pt "Tahoma";
    }

    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    .page {
        width: 21cm;
        padding: 1cm;
        margin: 1cm auto;
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
<div class="book">
    <div class="page">
        <div style="width:90%;">
            <table style="width:100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width:50%;">
                        <table style="font-size: 0.800em;width:100%;" cellpadding="0" cellspacing="0">
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
                                <td><b style="font-size: 10;">R.U.C.:</b> <span style="font-size: 12;"><?= $xmlComprobamte->infoTributaria->ruc; ?></span></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">Dirrección: </b><br /> Matriz <?= $xmlComprobamte->infoTributaria->dirMatriz; ?></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">Obligado a llevar contabilidad:</b> <?= $xmlComprobamte->infoLiquidacionCompra->obligadoContabilidad; ?></td>
                            </tr>
                            <?php
                            if (isset($xmlComprobamte->infoLiquidacionCompra->contribuyenteEspecial))
                                echo '<tr><td><b style="font-size: 10;">Contribuyente especial:</b> ' . $xmlComprobamte->infoLiquidacionCompra->contribuyenteEspecial . '</td></tr>';
                            if (isset($xmlComprobamte->infoTributaria->contribuyenteRimpe)) {
                                $tipoRimpe = 'CONTRIBUYENTE RÉGIMEN RIMPE';
                                if ($deEmpresa->tipo_rimpe == 0)
                                    $tipoRimpe = 'CONTRIBUYENTE RÉGIMEN RIMPE POPULAR';
                                elseif ($deEmpresa->tipo_rimpe == 1)
                                    $tipoRimpe = 'CONTRIBUYENTE RÉGIMEN RIMPE EMPRENDEDOR';
                                echo '<tr><td>' . $tipoRimpe . '</td></tr>';
                            }
                            if ($deEmpresa->agente_retencion != 0)
                                echo '<td><b style="font-size: 10;">Agente de Retención:</b> 1</td>';
                            else
                                echo '<td><b style="font-size: 10;">Agente de Retención:</b> NO</td>';
                            ?>
                        </table>
                    </td>
                    <td style="width:50%;" align="right">
                        <table style="width:100%;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <table style="font-size: 0.800em;" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center" style="font-size: 26px;" colspan="3"><b>Liquidación de Compra</b></td>
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
                                <td>Nombres y Apellidos:</td>
                                <td>{{ $proveedor->razonsocial }}</td>
                                <td>Identificación:</td>
                                <td>{{ $proveedor->id }}</td>
                            </tr>
                            <tr>
                                <td>Fecha emisión:</td>
                                <td>{{ $xmlComprobamte->infoLiquidacionCompra->fechaEmision }}</td>
                                <td>Dirección:</td>
                                <td>{{ $xmlComprobamte->infoLiquidacionCompra->direccionProveedor}} </td>
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
                    <td style="width: 100%;" colspan="2">
                        <table style="width:100%;">
                            <tr>
                                <th>Cod principal</th>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Descuento</th>
                                <th>Precio total</th>
                            </tr>
                            <?php
                            $subtotal0 = 0;
                            $subtotal12 = 0;
                            foreach ($xmlDetalles->detalle as $row) {
                                if ((float)$row->impuestos->impuesto->tarifa == 15.00 || (float)$row->impuestos->impuesto->tarifa == 12.00) {
                                    $subtotal12 = $subtotal12 + ($row->cantidad * (float)$row->precioUnitario);
                                } else {
                                    $subtotal0 = $subtotal0 + ($row->cantidad * (float)$row->precioUnitario);
                                }
                                echo '<tr>
                                        <td align="center">' . $row->codigoPrincipal . '</td>
                                        <td align="center">' . $row->descripcion . '</td>
                                        <td align="right">' . $row->cantidad . '</td>
                                        <td align="right">' . $row->precioUnitario . '</td>
                                        <td align="right">' . $row->descuento . '</td>
                                        <td align="right">' . (float) $row->precioTotalSinImpuesto . '</td>
                                    </tr>';
                                $totalSinImpuesto = (float)$subtotal12 + (float)$subtotal0 - (float)$row->descuento;
                                $iva =  $xmlComprobamte->infoLiquidacionCompra->totalConImpuestos->totalImpuesto->valor != 0.00 ? $xmlComprobamte->infoLiquidacionCompra->totalConImpuestos->totalImpuesto->valor : number_format(0, 2);
                                $totalFinal = (float) $totalSinImpuesto +  (float)$iva;
                            }
                            ?>
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
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 70%;" valign="top">
                                    <table>
                                        <?php
                                        $cont = 0;

                                        $adicionales = De_Documentos_Electronicos::getInfoAdicional($compras->id, $xml->claveAccesoConsultada);
                                        if ($adicionales != '') { ?>
                                            <tr>
                                                <td>Información adicional</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table>
                                                        <?php
                                                        $detAdic = json_decode($adicionales->infoAdicional);
                                                        foreach ($detAdic as $row) {
                                                            echo '<tr>
                                                            <td>' . ucwords(strtolower($row->nombre)) . ': </td>
                                                            <td>' . $row->valor . '</td>
                                                        </tr>';
                                                            $cont++;
                                                        }
                                                        ?>
                                                    </table>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <td><br /></td>
                                        </tr>
                                        <tr>
                                            <td><br /></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td valign="bootom">
                                                <table width="100%" border="1" cellspacing="0" cellpadding="0">
                                                    <tr style="text-align: center;">
                                                        <td width="40%"> <b> Forma de pago </b> </td>
                                                        <td width="15%"> <b> Valor </b> </td>
                                                        <td width="15%"> <b> Plazo </b> </td>
                                                        <td width="30%"> <b> Unidad de Tiempo </b> </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="50%" align="center">
                                                            <p> Otros con utilizacion del sistema financiero </p>
                                                        </td>
                                                        <td style="text-align: center;width: 15%;">{{'$ ' .  $totalFinal }}</td>
                                                        <td style="text-align: center;width: 15%;">{{ $xmlComprobamte->infoLiquidacionCompra->pagos->pago->plazo }}</td>
                                                        <td style="text-align: center;width: 30%;">{{$xmlComprobamte->infoLiquidacionCompra->pagos->pago->unidadTiempo }} </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 30%;">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td align="right">Subtotal {{$porcentajeIva}}%</td>
                                            <td align="right">{{ number_format((float)$subtotal12,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Subtotal 0%</td>
                                            <td align="right">{{ number_format((float)$subtotal0,2) }}</td>
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
                                            <td align="right">Descuento</td>
                                            <td align="right">
                                                @if(isset($xmlComprobamte->infoLiquidacionCompra->totalDescuento))
                                                {{ number_format((float) $xmlComprobamte->infoLiquidacionCompra->totalDescuento,2) }}
                                                @else
                                                {{ number_format(0,2) }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">Total sin impuestos</td>
                                            <td align="right">{{ number_format((float)$totalSinImpuesto,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">ICE</td>
                                            <td align="right">{{ number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">IVA {{$porcentajeIva}}%</td>
                                            <!--<td align="right">{{ $xmlComprobamte->infoLiquidacionCompra->totalConImpuestos->totalImpuesto->tarifa==12.00?$xmlComprobamte->infoLiquidacionCompra->totalConImpuestos->totalImpuesto->valor:number_format(0,2) }}</td>-->
                                            <td align="right">{{ $xmlComprobamte->infoLiquidacionCompra->totalConImpuestos->totalImpuesto->valor!=0.00?$xmlComprobamte->infoLiquidacionCompra->totalConImpuestos->totalImpuesto->valor:number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">IRBPNR</td>
                                            <td align="right">{{ number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Valor total</td>
                                            <td align="right">{{ number_format($totalFinal,2) }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>