<?php

use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\De_Empresa;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\EmisionDocumentosController;

$xml = simplexml_load_file($xml);
$xmlComprobamte = simplexml_load_string($xml->autorizaciones->autorizacion->comprobante);
$barcode = EmisionDocumentosController::setBarcode($xmlComprobamte->infoTributaria->claveAcceso, '/storage/app/facturaelectronica/barcode/' . $xmlComprobamte->infoTributaria->ruc . '/comprobanteRetencion/');
$barcode = '<img src="' . base_path() . $barcode . '" />';
$empresa = Empresa::where('id', $xmlComprobamte->infoTributaria->ruc)->first();
$deEmpresa = De_Empresa::where('id_empresa', $xmlComprobamte->infoTributaria->ruc)->first();
$logo = '/storage/app/logo/' . $empresa->logo;
if ($empresa->resolucion == '')
    $logo = '<img src="' . base_path() . $logo . '" style="width:50%;" />';
else {
    $res = explode('x', $empresa->resolucion);
    $logo = '<img src="' . base_path() . $logo . '" style="width:200px" />';
}
?>
<style>
    body {
        margin: 0;
        padding: 0;
        background-color: white;
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
                                <td><b style="font-size: 10;">Obligado a llevar contabilidad:</b> <?= $xmlComprobamte->infoCompRetencion->obligadoContabilidad; ?></td>
                            </tr>
                            <tr>
                                <?php
                                if (isset($xmlComprobamte->infoCompRetencion->contribuyenteEspecial)) {
                                    echo '<td><b style="font-size: 10;">Contribuyente especial:</b> ' . $xmlComprobamte->infoCompRetencion->contribuyenteEspecial . '</td>';
                                } else {
                                    echo '<td></td>';
                                }
                                ?>
                            </tr>
                            <?php
                            if ($deEmpresa->agente_retencion != 0)
                                echo '<td><b style="font-size: 10;">Agente de Retención:</b> 1</td>';
                            else
                                echo '<td><b style="font-size: 10;">Agente de Retención:</b> NO</td>';
                            ?>
                        </table>
                    </td>
                    <td style="width:50%;">
                        <table style="width:100%;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <table style="font-size: 0.800em;" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center" style="font-size: 26px;" colspan="3"><b>Comprobante de retención</b></td>
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
                                                <small><?= $xmlComprobamte->infoTributaria->claveAcceso; ?></small>
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
                                <td style="font-size: 10;"><b>Razón Social/Nombres y Apellidos</b></td>
                                <td style="font-size: 10;"><?= $xmlComprobamte->infoCompRetencion->razonSocialSujetoRetenido; ?></td>
                                <td style="font-size: 10;"><b>Identificación</b></td>
                                <td style="font-size: 10;"><?= $xmlComprobamte->infoCompRetencion->identificacionSujetoRetenido; ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 10;"><b>Fecha Emisión</b></td>
                                <td style="font-size: 10;"><?= $xmlComprobamte->infoCompRetencion->fechaEmision; ?></td>
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
                                <td align="center" style="font-size: 10;text-align:center;">Comprobante</td>
                                <td align="center" style="font-size: 10;">Número</td>
                                <td align="center" style="font-size: 10;">Fecha Emisión</td>
                                <td align="center" style="font-size: 10;">Ejericio Fiscal</td>
                                <td align="center" style="font-size: 10;">Base imponible</td>
                                <td align="center" style="font-size: 10;">IMPUESTO</td>
                                <td align="center" style="font-size: 10;">Codigo</td>
                                <td align="center" style="font-size: 10;">Porcentaje Retención</td>
                                <td align="center" style="font-size: 10;">Valor Retenido</td>
                            </tr>
                            <?php
                            $cont = 0;
                            $totalretenido = 0;
                            foreach ($xmlComprobamte->docsSustento->docSustento->retenciones->retencion as $row) {
                                $cont++;
                                $totalretenido += (float)$row->valorRetenido;
                                echo '<tr>
                                    <td align="center" style="font-size: 10;">FACTURA</td>
                                    <td align="left" style="font-size: 10;">' . $xmlComprobamte->docsSustento->docSustento->numDocSustento . '</td>
                                    <td align="center" style="font-size: 10;">' . $xmlComprobamte->docsSustento->docSustento->fechaEmisionDocSustento . '</td>
                                    <td align="center" style="font-size: 10;">' . $xmlComprobamte->infoCompRetencion->periodoFiscal . '</td>
                                    <td align="center" style="font-size: 10;">' . $row->baseImponible . '</td>
                                    <td align="center" style="font-size: 10;">';
                                if ($row->codigo == 2)
                                    echo 'IVA';
                                elseif ($row->codigo == 1)
                                    echo 'RENTA';
                                else
                                    echo 'IBR';
                                echo '</td>
                                    <td align="center" style="font-size: 10;">' . $row->codigoRetencion . '</td>

                                    <td align="center" style="font-size: 10;">' . $row->porcentajeRetener . '%</td>
                                    <td align="center" style="font-size: 10;">' . $row->valorRetenido . '</td>
                                </tr>';
                            }
                            ?>
                            <tr>
                                <td colspan="9">
                                    <hr>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="font-size: 10;text-align:center;">&nbsp;</td>
                                <td align="center" style="font-size: 10;">&nbsp;</td>
                                <td align="center" style="font-size: 10;">&nbsp;</td>
                                <td align="center" style="font-size: 10;">&nbsp;</td>
                                <td align="center" style="font-size: 10;">&nbsp;</td>
                                <td align="center" style="font-size: 10;">&nbsp;</td>
                                <td align="center" style="font-size: 10;">&nbsp;</td>
                                <td align="center" style="font-size: 10;">Total retenido</td>
                                <td align="center" style="font-size: 10;"><?= number_format($totalretenido, 2); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%;" colspan="2">
                        <table style="width: 100%;">
                            <tr>
                                <td>Información adicional</td>
                            </tr>
                            <?php
                            if (isset($xmlComprobamte->infoAdicional->campoAdicional[0])) {
                            ?>
                                <tr>
                                    <td style="font-size: 10;"><b>Correo: </b> <?= $xmlComprobamte->infoAdicional->campoAdicional[0]; ?></td>
                                </tr>
                            <?php
                            }

                            if (isset($xmlComprobamte->infoAdicional->campoAdicional[1])) {
                            ?>
                                <tr>
                                    <td style="font-size: 10;"><b>Telefono: </b> <?= $xmlComprobamte->infoAdicional->campoAdicional[1]; ?></td>
                                </tr>
                            <?php
                            }
                            if (isset($xmlComprobamte->infoAdicional->campoAdicional[2])) {
                            ?>
                                <tr>
                                    <td style="font-size: 10;"><b>Direccion: </b> <?= $xmlComprobamte->infoAdicional->campoAdicional[2]; ?></td>
                                </tr>
                            <?php
                            }
                            ?>
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