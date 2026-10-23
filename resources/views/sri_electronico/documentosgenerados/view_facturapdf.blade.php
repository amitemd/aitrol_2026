<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\ApiFacturacion;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_ventas;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\De_Empresa;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\EmisionDocumentosController;
use Sis_medico\Paciente;
use Sis_medico\Seguro;

$xml = simplexml_load_file($xml);
$porcentajeIva = $porIva->iva * 100;
$valorIva = $porIva->iva;
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
$cliente = Ct_Clientes::getCliente($xmlComprobamte->infoFactura->identificacionComprador);
$xmlDetalles = $xmlComprobamte->detalles->detalle;
$logo = '';
if ($empresa->logo != '') {
    $logo = '/storage/app/logo/' . $empresa->logo;
    $logo = '<img src="' . base_path() . $logo . '" style="width:200px;" />';
}
$establecimiento = (int)$xmlComprobamte->infoTributaria->estab;
$emision = (int)$xmlComprobamte->infoTributaria->ptoEmi;
$secuencial = (int)$xmlComprobamte->infoTributaria->secuencial;
DB::enableQueryLog();
$ventas = Ct_ventas::where('id_empresa', $empresa->id)
    ->where('sucursal', str_pad($establecimiento, 3, 0, STR_PAD_LEFT))
    ->where('punto_emision',  str_pad($emision, 3, 0, STR_PAD_LEFT))
    ->where('numero', str_pad($secuencial, 9, 0, STR_PAD_LEFT))
    ->where('nro_autorizacion', (string)$xml->claveAccesoConsultada)
    //->where('estado', 1)
    ->first();
if ($ventas == '') {
    echo '<center>Factura Anulada</center>';
    exit;
}
$paciente = '';
if (isset($ventas->id_paciente)) {
    $paciente = Paciente::where('id', $ventas->id_paciente)->first();
}
$seguros = '';
if ($ventas->nombres_paciente != '') {
    $seguros = Seguro::where('id', $ventas->seguro_paciente)->first();
}
$formaPago = Ct_Forma_Pago::join('ct_tipo_pago as t', 'ct_forma_pago.tipo', '=', 't.id')
    ->where('id_ct_ventas', $ventas->id)
    ->get();
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
                                <td align="center"><b><?= $xmlComprobamte->infoTributaria->razonSocial; ?></b></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">R.U.C.:</b> <span style="font-size: 12px;"><?= $xmlComprobamte->infoTributaria->ruc; ?></span></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">Dir Matriz:</b> <span style="font-size: 12px;"><?= $xmlComprobamte->infoTributaria->dirMatriz; ?></span></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">Obligado a llevar contabilidad:</b> <span style="font-size: 12px;"><?= $xmlComprobamte->infoFactura->obligadoContabilidad; ?></span></td>
                            </tr>

                            <?php
                            if (isset($xmlComprobamte->infoFactura->contribuyenteEspecial)) {
                                echo '<tr><td><b style="font-size: 10;">Contribuyente especial:</b> ' . $xmlComprobamte->infoFactura->contribuyenteEspecial . '</td> </tr>';
                            }
                            if ($deEmpresa->agente_retencion != 0)
                                echo '<tr><td><b style="font-size: 10;">Agente de Retención:</b> 1</td></tr>';
                            else
                                echo '<tr><td><b style="font-size: 10;">Agente de Retención:</b> NO</td></tr>';
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
                $contador = 0;
                $detaAdi = '';
                $keySave = '';
                if ($deEmpresa->leer_xml == 1) {
                    foreach ($ventas->detalles as $detalle) {
                        if ($detalle->check_iva == 1)
                            $subtotal12 = $subtotal12 + floatval($detalle->extendido);
                        else
                            $subtotal0 = $subtotal0 + floatval($detalle->extendido);
                        $deadi = '';

                        try {
                            $adinuevo = $detalle->detalle;
                            $detaAdi = $adinuevo;
                        } catch (Exception $e) {
                        }
                        echo '<tr style="font-size:10px;">
                            <td>' . $detalle->id_ct_productos . '</td>
                            <td valign="top"><p>' . nl2br($detalle->nombre) . '<br/>' . nl2br($detaAdi) . '</p></td>
                            <td align="right">' . $detalle->cantidad . '</td>
                            <td align="right">$ ' . number_format((float)$detalle->precio, 2) . '</td>
                            <td align="right">$ ' . number_format((float)$detalle->descuento, 2) . '</td>
                            <td align="right">$ ' . number_format((float)$detalle->extendido, 2) . '</td>
                                    </tr>';
                        $contador++;
                    }
                } else {
                    foreach ($xmlDetalles as $row) {
                        if ($row->impuestos->impuesto->tarifa == 15.00)
                            $subtotal12 = $subtotal12 + floatval($row->precioTotalSinImpuesto);
                        else
                            $subtotal0 = $subtotal0 + floatval($row->precioTotalSinImpuesto);
                        $deadi = '';
                        $detaAdi = '';
                        try {
                            $detaAdi = isset($row->detallesAdicionales->detAdicional) ? '<br/>' . $row->detallesAdicionales->detAdicional->attributes()->valor : '';
                            $adinuevo = '';
                            $deta_ = explode('|', $detaAdi);
                            if (is_array($deta_)) {
                                for ($i = 0; $i < count($deta_); $i++) {
                                    $deta_[$i] = str_replace('{', '', $deta_[$i]);
                                    $deta_[$i] = str_replace('}', '', $deta_[$i]);
                                    $deta_[$i] = str_replace('"', '', $deta_[$i]);
                                    $adinuevo .= $deta_[$i] . '<br/>';
                                }
                            }
                            $detaAdi = $adinuevo;
                        } catch (Exception $e) {
                        }
                        echo '<tr style="font-size:10px;">
                                <td>' . $row->codigoPrincipal . '</td>
                                <td valign="top"><p>' . nl2br($row->descripcion) . nl2br($detaAdi) . '</p></td>
                                <td align="right">' . $row->cantidad . '</td>
                                <td align="right">$ ' . number_format((float)$row->precioUnitario, 4) . '</td>
                                <td align="right">$ ' . number_format((float)$row->descuento, 4) . '</td>
                                <td align="right">$ ' . number_format((float)$row->precioTotalSinImpuesto, 4) . '</td>
                                        </tr>';
                        $contador++;
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
                <tr>
                    <td style="width: 100%;" colspan="2">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 70%;" valign="top">

                                    <table>
                                        <?php
                                        $adicionales_ = false;
                                        if ($ventas->nota_array != '' && $ventas->nota_array != '[null]') {
                                            $detalles = json_decode($ventas->nota_array);
                                            if (is_array($detalles) || is_object($detalles)) {
                                                $adicionales = $detalles;
                                                $adicionales_ = true;
                                            } else {
                                                $adicionales = De_Documentos_Electronicos::getInfoAdicional($ventas->id, $xml->claveAccesoConsultada);
                                            }
                                        } else {
                                            $adicionales = De_Documentos_Electronicos::getInfoAdicional($ventas->id, $xml->claveAccesoConsultada);
                                        }
                                        if ($adicionales != '') {
                                        ?>
                                            <tr>
                                                <td><b>Información adicional</b></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table>
                                                        <?php
                                                        try {
                                                            $cont = 0;
                                                            if ($adicionales_) {
                                                                $detAdic = $adicionales;
                                                            } else {
                                                                $detAdic = json_decode($adicionales->infoAdicional);
                                                            }
                                                            foreach ($detAdic as $row) {
                                                                if ($row != '') {
                                                                    echo '<tr>
                                                                        <td><b>' . ucwords(strtolower($row->nombre)) . ': </b></td>
                                                                        <td>' . $row->valor . '</td>
                                                                    </tr>';
                                                                    $cont++;
                                                                }
                                                            }
                                                        } catch (Exception $e) {
                                                            $cont = 0;
                                                            $adicionales = De_Documentos_Electronicos::getInfoAdicional($ventas->id, $xml->claveAccesoConsultada);
                                                            $detAdic = $adicionales;
                                                            $detAdic = json_decode($adicionales->infoAdicional);
                                                            foreach ($detAdic as $row) {
                                                                if ($row != '') {
                                                                    echo '<tr>
                                                                        <td><b>' . ucwords(strtolower($row->nombre)) . ': </b></td>
                                                                        <td>' . $row->valor . '</td>
                                                                    </tr>';
                                                                    $cont++;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </table>
                                                </td>
                                            </tr>
                                        <?php
                                            if ($ventas->nombres_paciente != '' && $paciente == '') {
                                                echo '<tr>
                                                <td><b>Paciente:</b> ' . $ventas->nombres_paciente . '</td>
                                                </tr>';
                                            }
                                            if ($seguros != '' && $paciente == '') {
                                                echo '<tr>
                                                <td><b>Seguro:</b> ' . $seguros->nombre . '</td>
                                                </tr>';
                                            }
                                            if ($ventas->procedimientos != '') {
                                                echo '<tr>
                                                <td><b>Procedimiento:</b> ' . $ventas->procedimientos . '</td>
                                                </tr>
                                                <tr>
                                                <td><b>Fecha proc:</b> ' . date('Y-m-d', strtotime($ventas->fecha_procedimiento)) . '</td>
                                                </tr>';
                                            }
                                        } else {

                                            /*if (Auth::user()->id == '0921605895') {
                                                echo '<pre>';
                                                print_r($xmlComprobamte->infoAdicional->campoAdicional);
                                                foreach ($xmlComprobamte->infoAdicional as $nodo) {
                                                    $atributos = $nodo->attributes();
                                                    //echo $nodo->valor . '<br/>';
                                                    echo '<pre>';
                                                    print_r($atributos['Correo']);
                                                    exit;
                                                }
                                                exit;
                                            }*/
                                        }

                                        /*if ($xmlComprobamte->infoTributaria->ruc == '0993075000001') {
                                            $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                                            $randomString = '';
                                            for ($i = 0; $i < 12; $i++) {
                                                $index = rand(0, strlen($characters) - 1);
                                                $randomString .= $characters[$index];
                                            }
                                            $nonce = $randomString;
                                            $appId     = date('YmdHis') . '-Humanlabs';
                                            $appSecret = 'jdjdjKJOS(/68759856498465';
                                            $date      = date('c');
                                            $Token     = base64_encode(sha1($nonce . $date . $appSecret));
                                            $options = array(
                                                'http' => array(
                                                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                                                    'method'  => 'GET',
                                                    'content' => 'prueba',
                                                ),
                                                "ssl"  => array(
                                                    "verify_peer"      => false,
                                                    "verify_peer_name" => false,
                                                ),
                                            );
                                            if ($xmlComprobamte->infoTributaria->estab == '001')
                                                $urllink = 'https://ieced.siaam.ec/sis_medico/public';
                                            elseif ($xmlComprobamte->infoTributaria->estab == '003')
                                                $urllink = 'https://gastrosanto.aitrol.com/public';
                                            elseif ($xmlComprobamte->infoTributaria->estab == '004')
                                                $urllink = 'https://gastroquito.siaam.ec/public';
                                            elseif ($xmlComprobamte->infoTributaria->estab == '006')
                                                $urllink = 'https://gastromanta.aitrol.com/public';
                                            elseif ($xmlComprobamte->infoTributaria->estab == '007')
                                                $urllink = 'https://ieced.siaam.ec/sis_medico/public';
                                            else $urllink = 'https://ieced.siaam.ec/sis_medico/public';

                                            $url = $urllink . '/api_doc_electronico?opcion=traerOrdenExamen&n=' . $xmlComprobamte->infoTributaria->estab . '-' . $xmlComprobamte->infoTributaria->ptoEmi . '-' . $xmlComprobamte->infoTributaria->secuencial . '&e=' . $xmlComprobamte->infoTributaria->ruc;

                                            $context = stream_context_create($options);
                                            try {
                                                $orden = file_get_contents($url, false, $context);
                                                if ($orden != '') {
                                                }
                                            } catch (Exception $e) {
                                                $orden = '';
                                            }
                                            if ($orden != '') {
                                                $orden = json_decode($orden);

                                                echo '<tr>
                                                <td><b>Paciente:</b> ' . $orden->nombre1 . ' ' . $orden->nombre2 . ' ' . $orden->apellido1 . ' ' . $orden->apellido2 . '</td>
                                                </tr>
                                                <tr>
                                                <td><b>Orden No.:</b> ' . $orden->id . '</td>
                                                </tr>
                                                <tr>
                                                <td><b>Seguro:</b> ' . $orden->nombre . '</td>
                                                </tr>';
                                            }
                                        }*/
                                        ?>

                                        <tr>
                                            <td>
                                                <table>
                                                    <tr>
                                                        <td><b>Forma de pago:</b></td>
                                                    </tr>
                                                    <?php
                                                    $nombre = '';
                                                    if (count($formaPago) > 0) {
                                                        $otros = 0;
                                                        foreach ($formaPago as $row) {
                                                            if ($row->nombre == 'PENDIENTE PAGO') {
                                                                $nombre = 'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO';
                                                            } else {
                                                                $nombre = $row->nombre;
                                                            }
                                                            $otros += $row->valor;
                                                            if ($ventas->contraentrega != 1) {
                                                                echo '<tr>
                                                            <td>' . ucwords(strtolower($nombre)) . ' - $' . number_format($row->valor, 2) . ' - ' . $xmlComprobamte->infoFactura->pagos->pago->plazo . ' ' . $xmlComprobamte->infoFactura->pagos->pago->unidadTiempo . '</td>
                                                            </tr>';
                                                            }
                                                        }

                                                        if ($ventas->contraentrega == 1) {
                                                            echo '<tr>
                                                            <td>' . ucwords(strtolower($row->nombre)) . ' - $' . number_format($row->valor, 2) . '</td>
                                                            </tr>';
                                                        }
                                                    }
                                                    if ($ventas->contraentrega == 1) {
                                                        echo '<tr>
                                                        <td><b>Contra entrega</b></td>
                                                        </tr>';
                                                    }
                                                    ?>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 40%;">
                                    <table style="width: 100%;">
                                        <?php
                                        $subtotal12 = 0;
                                        $subtotal0 = 0;
                                        foreach ($xmlComprobamte->infoFactura->totalConImpuestos->totalImpuesto as $row) {
                                            if ((int)$row->codigoPorcentaje == 4) {
                                                $subtotal12 = (float)$row->baseImponible;
                                            } else {
                                                $subtotal0 = (float)$row->baseImponible;
                                            }
                                        }

                                        ?>
                                        <tr>
                                            <td align="right">Subtotal {{$porcentajeIva}}%</td>
                                            <td align="right">$ {{ number_format($subtotal12,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Subtotal 0%</td>
                                            <td align="right">$ {{number_format($subtotal0,2)}}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Subtotal no objeto de IVA</td>
                                            <td align="right">$ {{ number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Subtotal Exento de IVA</td>
                                            <td align="right">$ {{ number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Total sin impuestos</td>
                                            <td align="right">$ {{ $xmlComprobamte->infoFactura->totalSinImpuestos }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Descuento</td>
                                            <td align="right">$ {{ $xmlComprobamte->infoFactura->totalDescuento }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">ICE</td>
                                            <td align="right">$ {{ number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">IVA {{$porcentajeIva}}%</td>
                                            <!--<td align="right">{{ $xmlComprobamte->infoFactura->totalConImpuestos->totalImpuesto->tarifa==12.00?$xmlComprobamte->infoFactura->totalConImpuestos->totalImpuesto->valor:number_format(0,2) }}</td>-->
                                            <td align="right">$ {{ $xmlComprobamte->infoFactura->totalConImpuestos->totalImpuesto->valor!=0.00?$xmlComprobamte->infoFactura->totalConImpuestos->totalImpuesto->valor:number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Total devolución IVA</td>
                                            <td align="right">$ {{ number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">IRBPNR</td>
                                            <td align="right">$ {{ number_format(0,2) }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Propina</td>
                                            <td align="right">$ {{ $xmlComprobamte->infoFactura->propina }}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Valor total</td>
                                            <td align="right">$ {{ $xmlComprobamte->infoFactura->importeTotal }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <?php
                    if (isset($empresa->leyenda) && $empresa->leyenda != '') {
                        echo '<table style="width:100%;font-size:10px"><tr><td>&nbsp;</td></tr><tr><td colspan="2" style="text-align: justify;">' . nl2br($empresa->leyenda) . '</td></tr></table>';
                    }
                    ?>
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