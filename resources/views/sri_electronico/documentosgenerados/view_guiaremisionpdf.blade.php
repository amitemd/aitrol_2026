<?php

use Sis_medico\Ct_transportista;
use Sis_medico\De_Empresa;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\EmisionDocumentosController;

$xml = simplexml_load_file($xml);
$xmlComprobamte = simplexml_load_string($xml->autorizaciones->autorizacion->comprobante);
EmisionDocumentosController::crearcarpeta(base_path() . '/storage/app/facturaelectronica/barcode/' . $xmlComprobamte->infoTributaria->ruc . '/guiaRemision/');
$barcode = EmisionDocumentosController::setBarcode($xmlComprobamte->infoTributaria->claveAcceso, '/storage/app/facturaelectronica/barcode/' . $xmlComprobamte->infoTributaria->ruc . '/guiaRemision/');
$barcode = '<img src="' . base_path() . $barcode . '" />';
$empresa = Empresa::where('id', $xmlComprobamte->infoTributaria->ruc)->first();
$deEmpresa = De_Empresa::where('id_empresa', $xmlComprobamte->infoTributaria->ruc)->first();
$transportista = Ct_transportista::getTransportista($xmlComprobamte->infoGuiaRemision->rucTransportista);
$logo = '/storage/app/logo/' . $empresa->logo;
$logo = '<img src="' . base_path() . $logo . '" style="width:200px;" />';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía de remisión <?= $xmlComprobamte->infoTributaria->estab; ?>-<?= $xmlComprobamte->infoTributaria->ptoEmi; ?>-<?= $xmlComprobamte->infoTributaria->secuencial; ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        body {
            margin: 0;
            margin-left: -20px;
            padding: 0;
            background-color: white;
            font: 10pt "Tahoma";
            font-size: 10px;
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
            <table>
                <tr>
                    <td>&nbsp;<br /><br /></td>
                </tr>
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
                                <td><b style="font-size: 10;">R.U.C.:</b> <span style="font-size: 12px;"><?= $xmlComprobamte->infoTributaria->ruc; ?></span></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">Dir Matriz:</b> <span style="font-size: 12px;"><?= $xmlComprobamte->infoTributaria->dirMatriz; ?></span></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">Obligado a llevar contabilidad:</b> <span style="font-size: 12px;"><?= $xmlComprobamte->infoGuiaRemision->obligadoContabilidad; ?></span></td>
                            </tr>
                            <tr>
                                <?php
                                if (isset($xmlComprobamte->infoGuiaRemision->contribuyenteEspecial)) {
                                    echo '<td><b style="font-size: 10;">Contribuyente especial:</b> <span style="font-size: 12px;">' . $xmlComprobamte->infoGuiaRemision->contribuyenteEspecial . '</span></td>';
                                } else {
                                    echo '<td></td>';
                                }
                                ?>
                            </tr>
                            <?php
                            if ($deEmpresa->agente_retencion == 1) {
                                echo '<tr><td><b style="font-size: 10;">Agente de Retención:</b> <span style="font-size: 12px;">Resolución Nro. NAC-DNCRASC20-00000001</span></td></tr>';
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
                                            <td align="center" style="font-size: 26px;" colspan="3"><b>Guía de Remisión</b></td>
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
                                            <td><span style="font-size: 12px;"> <?= date('d/m/Y H:i:s', strtotime($xml->autorizaciones->autorizacion->fechaAutorizacion)); ?></span></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10;"><b>Ambiente:</b></td>
                                            <td colspan="2"><span style="font-size: 12px;"><?= $xml->autorizaciones->autorizacion->ambiente; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10;"><b>Emisión:</b></td>
                                            <td colspan="2"><span style="font-size: 12px;">NORMAL</span></td>
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
                        <hr />
                        <b style="font-size: 16px;">Información del transportista:</b>
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td style="width:100%;" colspan="2">
                        <table style="width:100%;font-size: 0.800em;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="font-size: 10;"><b>Información (Transportista)</b></td>
                                <td style="font-size: 10;"><?= $xmlComprobamte->infoGuiaRemision->rucTransportista; ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 10;"><b>Razón Social/Nombres y Apellidos</b></td>
                                <td style="font-size: 10;"><?= $xmlComprobamte->infoGuiaRemision->razonSocialTransportista; ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 10;"><b>Placa</b></td>
                                <td style="font-size: 10;"><?= $xmlComprobamte->infoGuiaRemision->placa; ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 10;"><b>Punto de Partida</b></td>
                                <td style="font-size: 10;"><?= $xmlComprobamte->infoGuiaRemision->dirPartida; ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 10;"><b>Fecha Inicio Transporte</b></td>
                                <td style="font-size: 10;"><?= $xmlComprobamte->infoGuiaRemision->fechaIniTransporte; ?></td>
                                <td style="font-size: 10;"><b>Fecha Fin Transporte</b></td>
                                <td style="font-size: 10;"><?= $xmlComprobamte->infoGuiaRemision->fechaFinTransporte; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr />
                        <b style="font-size: 16px;">Información del comprobante de venta:</b>
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td style="width:100%;" colspan="2">
                        <table style="width:100%;font-size: 0.800em;" cellpadding="0" cellspacing="0">
                            <?php
                            if (isset($xmlComprobamte->destinatarios->destinatario->numDocSustento)) {
                            ?>
                                <tr>
                                    <td style="font-size: 10;"><b>Comprobante de Venta</b></td>
                                    <td style="font-size: 10;">FACTURA</td>
                                    <td style="font-size: 10;">{{ $xmlComprobamte->destinatarios->destinatario->numDocSustento }}</td>
                                    <td style="font-size: 10;"><b>Fecha de Emisión:</b></td>
                                    <td style="font-size: 10;">{{ $xmlComprobamte->destinatarios->destinatario->fechaEmisionDocSustento }}</td>
                                </tr>
                                <tr>
                                    <td><b>Número de Autorización:</b></td>
                                    <td colspan="4">{{ $xmlComprobamte->destinatarios->destinatario->numAutDocSustento }}</td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <hr />
                                    </td>
                                </tr>
                            <?php
                            }
                            foreach ($xmlComprobamte->destinatarios as $row) {
                            ?>
                                <tr>
                                    <td style="font-size: 10;"><b>Motivo de Traslado: </b> <?= $row->destinatario->motivoTraslado; ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10;"><b>Destino (Punto de Llegada): </b><?= $row->destinatario->dirDestinatario; ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10;"><b>Identificación (Destinatario): </b><?= $row->destinatario->identificacionDestinatario; ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10;"><b>Razón Social/Nombres y Apellidos: </b><?= $row->destinatario->razonSocialDestinatario; ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10;"><b>Documento Aduanero: </b></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10;"><b>Código Establecimiento Destino: </b><?= $row->destinatario->codEstabDestino; ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10;"><b>Ruta: </b><?= $row->destinatario->ruta; ?></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </td>
                </tr>
            </table>
            <?php
            foreach ($xmlComprobamte->destinatarios as $row) {
            ?>
                <?php
                foreach ($row->destinatario->detalles as $de) {
                ?>
                    <table class="table" style="width: 98%;">
                        <thead>
                            <tr>
                                <td style="font-size: 10;">No.</td>
                                <td style="font-size: 10;">Cantidad</td>
                                <td style="font-size: 10;">Descripción</td>
                                <td style="font-size: 10;">Código Principal</td>
                                <td style="font-size: 10;">Código Auxiliar</td>
                                <?= isset($de->detalle->detallesAdicionales->detAdicional[0]['valor'][0]) ? '<td style="font-size: 10;">Detalle 1</td>' : '' ?>
                                <?= isset($de->detalle->detallesAdicionales->detAdicional[1]['valor'][0]) ? '<td style="font-size: 10;">Detalle 2</td>' : '' ?>
                                <?= isset($de->detalle->detallesAdicionales->detAdicional[2]['valor'][0]) ? '<td style="font-size: 10;">Detalle 3</td>' : '' ?>
                            </tr>
                        </thead>
                        <tbody>
                            {{ $cont=0 }}
                            @foreach ($de as $de_)
                            {{ $cont++ }}
                            <tr>
                                <th scope="row">{{ $cont }}</th>
                                <th>{{ $de_->cantidad }}</th>
                                <td>{{ $de_->descripcion }}</td>
                                <td>{{ $de_->codigoInterno }}</td>
                                <td>{{ $de_->codigoAdicional }}</td>
                                <?php
                                if (isset($de_->detallesAdicionales->detAdicional[0]['valor'][0]))
                                ?>
                                <td style="font-size: 10;"><?= $de_->detallesAdicionales->detAdicional[0]['valor'][0]; ?></td>
                                <?php
                                ?>
                                <?php
                                if (isset($de_->detallesAdicionales->detAdicional[1]['valor'][0]))
                                ?>
                                <td style="font-size: 10;"><?= $de_->detallesAdicionales->detAdicional[1]['valor'][0] ?></td>
                                <?php
                                ?>
                                <?php
                                if (isset($de_->detallesAdicionales->detAdicional[2]['valor'][0]))
                                ?>
                                <td style="font-size: 10;"><?= $de_->detallesAdicionales->detAdicional[2]['valor'][0] ?></td>
                                <?php
                                ?>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                <?php
                }
                ?>
            <?php
            }
            ?>
            <table style="width:100%;">
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr />
                        <b style="font-size: 16px;">Información adicional:</b>
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 10;"><b>Correo transportista: </b> <?= $transportista->email; ?></td>
                </tr>
                <tr>
                    <td style="font-size: 10;"><b>Dirección transportista: </b> <?= $transportista->direccion; ?></td>
                </tr>
            </table>
        </div>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>