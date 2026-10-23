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
    <title>Laravel 8 PDF</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
</head>

<body>
    <div class="container mt-4">
        <div class="page">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    foreach ($xmlComprobamte->destinatarios as $row) {
                    ?>
                        <?php
                        foreach ($row->destinatario->detalles as $de) {
                        ?>
                            <table class="table">
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
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>