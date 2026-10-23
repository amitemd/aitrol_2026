<?php

use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_ventas;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\De_Empresa;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\EmisionDocumentosController;
use Sis_medico\Paciente;
use Sis_medico\Seguro;

$logo = '/storage/app/logo/' . $empresa->logo;
$logo = '<img src="' . base_path() . $logo . '" style="width:200px;" />';

$establecimiento = $venta->punto_emision;;
$emision = $venta->sucursal;;
$secuencial = $venta->numero;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
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
            <table style="width: 100%;">
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
                                <td align="center"><b><?= $empresa->razonsocial; ?></b></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">R.U.C.:</b> <span style="font-size: 12px;"><?= $empresa->id; ?></span></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">Dir Matriz:</b> <span style="font-size: 12px;"><?= $empresa->direccion; ?></span></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:50%;">
                        <table style="width:100%;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <table style="font-size: 0.800em;" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center" style="font-size: 26px;" colspan="3"><b><?= $title; ?></b></td>
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
                                <td>{{ $venta->cliente->nombre }}</td>
                                <td>CI/RUC:</td>
                                <td>{{ $venta->cliente->identificacion }}</td>
                            </tr>
                            <tr>
                                <td>Fecha emisión:</td>
                                <td>{{ $venta->fecha }}</td>
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
                    <th>Cod auxiliar</th>
                    <th>Descripción</th>
                    <th align="center">Cantidad</th>
                </tr>
                <?php
                $subtotal0 = 0;
                $subtotal12 = 0;
                $contador = 0;
                if (isset($venta->detalles)) {

                    foreach ($venta->detalles as $row) {

                        $codigoAuxiliar = '';
                        $descripcionOtros = '';
                        $otros = json_decode($row->otros);
                        try {
                            if (count($otros) > 0) {
                                $codigoAuxiliar = $otros->codigo_publico;
                                $descripcionOtros = $otros->descripcion_publico;
                            }
                            echo '<tr style="font-size:10px;">
                            <td>' . $row->id_ct_productos . '</td>
                            <td>' . $codigoAuxiliar . '</td>
                            <td valign="top"><p>' . nl2br($row->nombre) . '<br/>' . nl2br($row->detalle) . '<br/>' . $descripcionOtros . '</p></td>
                            <td align="center">' . $row->cantidad . '</td>
                                    </tr>';
                            $contador++;
                        } catch (Exception $e) {
                        }
                    }
                }
                ?>
            </table>
            <table style="width:100%;" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
            </table>
            <table style="width:100%;text-align:center;" cellspacing="0" cellpadding="0">
                <tr>
                    <td>Entegado por<br /><br /><br /><br /></td>
                    <td>Recibido por<br /><br /><br /><br /></td>
                    <td>Fecha de entrega<br /><br /><br /><br /></td>
                </tr>
                <tr>
                    <td>___________________________________</td>
                    <td>___________________________________</td>
                    <td>___________________________________</td>
                </tr>
            </table>

        </div>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>