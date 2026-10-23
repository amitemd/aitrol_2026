<?php

use Google\Service\Bigquery\HparamTuningTrial;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_transportista;
use Sis_medico\Ct_ventas;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\De_Empresa;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\EmisionDocumentosController;
use Sis_medico\Paciente;
use Sis_medico\Seguro;

$xml = simplexml_load_file($xml);
$xmlComprobamte = simplexml_load_string($xml->autorizaciones->autorizacion->comprobante);

EmisionDocumentosController::crearcarpeta(base_path() . '/storage/app/facturaelectronica/barcode/' . $xmlComprobamte->infoTributaria->ruc . '/factura/');
$barcode = EmisionDocumentosController::setBarcode($xmlComprobamte->infoTributaria->claveAcceso, '/storage/app/facturaelectronica/barcode/' . $xmlComprobamte->infoTributaria->ruc . '/factura/');
$barcode = '<img src="' . base_path() . $barcode . '" />';
$empresa = Empresa::where('id', $xmlComprobamte->infoTributaria->ruc)->first();
$deEmpresa = De_Empresa::where('id_empresa', $xmlComprobamte->infoTributaria->ruc)->first();
$cliente = Ct_Clientes::getCliente($xmlComprobamte->infoFactura->identificacionComprador);
$xmlDetalles = $xmlComprobamte->detalles->detalle;
$logo = '/storage/app/logo/' . $empresa->logo;
$logo = '<img src="' . base_path() . $logo . '" style="width:200px;" />';
$establecimiento = (int)$xmlComprobamte->infoTributaria->estab;
$emision = (int)$xmlComprobamte->infoTributaria->ptoEmi;
$secuencial = (int)$xmlComprobamte->infoTributaria->secuencial;
$ventas = Ct_ventas::where('id_empresa', $empresa->id)
    ->where('sucursal', str_pad($establecimiento, 3, 0, STR_PAD_LEFT))
    ->where('punto_emision',  str_pad($emision, 3, 0, STR_PAD_LEFT))
    ->where('numero', str_pad($secuencial, 9, 0, STR_PAD_LEFT))
    ->where('estado', 1)
    ->first();
/*
echo '<pre>';
print_r(DB::getQueryLog());
exit;
*/
$paciente = '';
$paciente = Paciente::where('id', $ventas->id_paciente)->first();
$seguros = '';
if ($paciente != '') {
    $seguros = Seguro::where('id', $paciente->id_seguro)->first();
}
$formaPago = Ct_Forma_Pago::join('ct_tipo_pago as t', 'ct_forma_pago.tipo', '=', 't.id')
    ->where('id_ct_ventas', $ventas->id)
    ->get();


?>
<style>
    body {
        margin: 0;
        padding: 0;
        background-color: #FAFAFA;
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
                                <td><b style="font-size: 10;">R.U.C.:</b> <?= $xmlComprobamte->infoTributaria->ruc; ?></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">Dir Matriz:</b> <?= $xmlComprobamte->infoTributaria->dirMatriz; ?></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">Obligado a llevar contabilidad:</b> <?= $xmlComprobamte->infoFactura->obligadoContabilidad; ?></td>
                            </tr>
                            <tr>
                                <?php
                                if (isset($xmlComprobamte->infoFactura->contribuyenteEspecial)) {
                                    echo '<td><b style="font-size: 10;">Contribuyente especial:</b> Resolución Nro. NAC-DNCRASC20-00000001</td>';
                                } else {
                                    echo '<td></td>';
                                }
                                ?>
                            </tr>
                            <?php
                            if ($deEmpresa->agente_retencion != 0) {
                                echo '<td><b style="font-size: 10;">Agente de Retención:</b> ' . $xmlComprobamte->infoFactura->contribuyenteEspecial . '</td>';
                            }
                            if (isset($xmlComprobamte->infoTributaria->contribuyenteRimpe)) {
                                $tipoRimpe = 'CONTRIBUYENTE RÉGIMEN RIMPE';
                                if ($deEmpresa->tipo_rimpe == 0)
                                    $tipoRimpe = 'CONTRIBUYENTE RÉGIMEN RIMPE POPULAR';
                                elseif ($deEmpresa->tipo_rimpe == 1)
                                    $tipoRimpe = 'CONTRIBUYENTE RÉGIMEN RIMPE EMPRENDEDOR';
                                echo '<td>' . $tipoRimpe . '</td>';
                            }
                            ?>
                        </table>
                    </td>
                    <td style="width:50%;">
                        <table style="width:100%;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <table style="font-size: 0.800em;" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center" style="font-size: 26px;" colspan="3"><b>Factura</b></td>
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
                                <td>Razón social:</td>
                                <td>{{ $cliente->nombre }}</td>
                                <td>CI/RUC:</td>
                                <td>{{ $cliente->identificacion }}</td>
                            </tr>
                            <tr>
                                <td>Fecha emisión:</td>
                                <td>{{ $xmlComprobamte->infoFactura->fechaEmision }}</td>
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
                            foreach ($xmlDetalles as $row) {
                                if ($row->impuestos->impuesto->tarifa == 12.00)
                                    $subtotal12 = $subtotal12 + floatval($row->precioTotalSinImpuesto);
                                else
                                    $subtotal0 = $subtotal0 + floatval($row->precioTotalSinImpuesto);
                                echo '<tr>
                                        <td align="center">' . $row->codigoPrincipal . '</td>
                                        <td>' . $row->descripcion . '</td>
                                        <td align="right">' . $row->cantidad . '</td>
                                        <td align="right">' . $row->precioUnitario . '</td>
                                        <td align="right">' . $row->descuento . '</td>
                                        <td align="right">' . $row->precioTotalSinImpuesto . '</td>
                                    </tr>';
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
                                        $adicionales = De_Documentos_Electronicos::getInfoAdicional($ventas->id, $xml->claveAccesoConsultada);
                                        if ($adicionales != '') {
                                        ?>
                                            <tr>
                                                <td>Información adicional</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table>
                                                        <?php
                                                        $cont = 0;
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
                                            <td>
                                                <table>
                                                    <tr>
                                                        <td>Forma de pago:</td>
                                                    </tr>
                                                    <?php
                                                    if (count($formaPago) > 0) {
                                                        foreach ($formaPago as $row) {
                                                            echo '<tr>
                                                            <td>' . ucwords(strtolower($row->nombre)) . ' - $' . number_format($row->valor, 2) . ' - ' . $xmlComprobamte->infoFactura->pagos->pago->plazo . ' ' . $xmlComprobamte->infoFactura->pagos->pago->unidadTiempo . '</td>
                                                            </tr>';
                                                        }
                                                    }
                                                    ?>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>

                                </td>
                                <td style="width: 30%;">
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
    </div>
</div>