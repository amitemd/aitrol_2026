<?php

namespace Sis_medico\Http\Controllers;

use Excel;
use DOMDocument;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Barcode;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Guia_Remision_Cabecera;
use Sis_medico\Ct_Guia_Remision_Detalle;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_transportista;
use Sis_medico\De_Codigo_Impuestos;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\De_Empresa;
use Sis_medico\De_Info_Tributaria;
use Sis_medico\De_Log_Error;
use Sis_medico\De_Maestro_Documentos;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\sri_electronico\ClaveAcceso;
use SoapClient;
use Sis_medico\Ct_Sucursales;
use Sis_medico\De_Estado_Sri;
use Mail;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Comprobante_Egreso_Masivo;
use Sis_medico\Ct_Comprobante_Ingreso;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Masivo;
use Sis_medico\Ct_Detalle_Comprobante_Ingreso;
use Sis_medico\Ct_Detalle_Debito_Clientes;
use Sis_medico\Ct_Detalle_Pago_Ingreso;
use Sis_medico\Ct_detalle_retenciones;
use Sis_medico\Ct_Detalle_Rubro_Credito;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_Detalle_Venta_Conglomerada;
use Sis_medico\Ct_Devolucion_Productos;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_Nota_Credito_Clientes;
use Sis_medico\Ct_Nota_Debito_Cliente;
use Sis_medico\Ct_Porcentaje_Retenciones;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Ven_Orden;
use Sis_medico\Ct_ventas;
use Sis_medico\De_Parametros;
use Sis_medico\Examen_Orden;
use Sis_medico\Labs_Factura_Agrupada_Cab;
use Sis_medico\Log_Api;
use Sis_medico\Log_usuario;
use Sis_medico\LogConfig;
use Sis_medico\Paciente;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\Proveedor;
use Sis_medico\TransaccionPago;
use Sis_medico\User;
use Sis_medico\Utilidades;
use Sis_medico\v3\contable\Clientes;
use Sis_medico\v3\contable\ComprobanteIngreso;
use Sis_medico\v3\contable\Contabilidad;
use Sis_medico\v3\contable\DeEmpresas;
use Sis_medico\v3\contable\DetalleComprobanteIngreso;
use Sis_medico\v3\contable\DetallePagoIngreso;
use Sis_medico\v3\contable\FormaPago;
use Sis_medico\v3\contable\InfoTributaria;
use Sis_medico\v3\contable\Productos;
use Sis_medico\v3\contable\Ventas;
use Sis_medico\v3\contable\VentasDetalle;
use Sis_medico\Validate_Decimals;
use stdClass;

class EmisionDocumentosController extends Controller
{
    public $errorGeneral = '';
    private $tipoDocumento = null;
    private $porIva = '';
    public function __construct()
    {
        $this->porIva = Utilidades::porcentajeIva();
    }

    public function index(Request $req)
    {
        if ($req->opcion == '') {
            if (strtolower((string) $req->formato) === 'resumen') {
                return $this->emitirDocumentosConResumen($req);
            }
            if (isset($req->idUsuario)) {

                $this->getGuiaRemision(null, $req->idUsuario);
                $this->getNotaCredito(null, $req->idUsuario);
                $this->getFactura(null, $req->idUsuario);
                $this->getRetencion(null, $req->idUsuario);
                //$this->getNotaDebito(null, $req->idUsuario);
                $this->getLiquidacion(null, $req->idUsuario);
                $this->getCrearFacturaPago(null, $req->idUsuario);
            } else {

                $this->getGuiaRemision(null);
                $this->getNotaCredito(null);
                $this->getFactura(null);
                $this->getRetencion(null);
                //$this->getNotaDebito(null);
                $this->getLiquidacion(null);
                $this->getCrearFacturaPago(null);
            }
        } elseif ($req->opcion == 'leerxml') {
            $this->leerXml($req->clave, $req->ambiente);
        } elseif ($req->opcion == 'barcode') {
            $this->setBarcode($req->clave, '');
        } elseif ($req->opcion == 'generarPdf') {
            return  $this->generarPdf($req->clave, $req->clave);
        } elseif ($req->opcion == 'reconstruirDocumento') {
            return $this->reconstruirDocumento($req);
        } elseif ($req->opcion == 'enviar_correo') {
            $param = [
                'nombre' => $req->nombre,
                'apellido' => $req->apellido,
                'email' => $req->email,
                'estab' => $req->estab,
                'ptoEmi' => $req->ptoEmi,
                'secuencial' => $req->secuencial,
                'tipoDoc' => $req->tipoDoc,
                'claveAcceso' => $req->claveAcceso,
                'fechaEmision' => $req->fechaEmision,
                'valorPagar' => $req->valorPagar,
            ];
            $this->enviar_correo($param);
        } elseif ($req->opcion == 'descargarXML') {
            $this->descargarXML($req->clave);
        } elseif ($req->opcion == 'descargarXMLC') {
            $xmlRespuesta = EmisionDocumentosController::obtenercomprobanteSRI($req->clave);
            $xml = new \DOMDocument('1.0', 'UTF-8');
            $root = $xml->createElement('RespuestaAutorizacionComprobante');
            $clave = $xml->createElement('claveAccesoConsultada', $xmlRespuesta->RespuestaAutorizacionComprobante->claveAccesoConsultada);
            $root->appendChild($clave);
            $claveAcceso = $xmlRespuesta->RespuestaAutorizacionComprobante->claveAccesoConsultada;
            $numeroComprobante = $xml->createElement('numeroComprobantes', $xmlRespuesta->RespuestaAutorizacionComprobante->numeroComprobantes);
            $root->appendChild($numeroComprobante);
            $nodoAutorizaciones = $xml->createElement('autorizaciones');
            $autorizaciones = $xmlRespuesta->RespuestaAutorizacionComprobante->autorizaciones;
            foreach ($autorizaciones as $autorizacion) {
                $nodoAutorizacion = $xml->createElement('autorizacion');
                $nodoAutorizacion->appendChild($xml->createElement('estado', $autorizacion->estado));
                $nodoAutorizacion->appendChild($xml->createElement('fechaAutorizacion', $autorizacion->fechaAutorizacion));
                $nodoAutorizacion->appendChild($xml->createElement('ambiente', $autorizacion->ambiente));
                $nodoAutorizacion->appendChild($xml->createElement('comprobante', $autorizacion->comprobante));
                if (!is_null($autorizacion->mensajes)) {
                    $nodoMensajes = $this->getMensajesAutorizacion($xml, $autorizacion->mensajes);
                    $nodoAutorizacion->appendChild(($nodoMensajes));
                } else {
                    $nodoMensajes = $xml->createElement('mensajes');
                    $nodoAutorizacion->appendChild(($nodoMensajes));
                }
                $nodoAutorizaciones->appendChild($nodoAutorizacion);
            }
            $root->appendChild($nodoAutorizaciones);
            $xml->appendChild($root);
            $xml->formatOutput = true;
            $docCod = substr($claveAcceso, 8, 2);
            $esta = substr($claveAcceso, 24, 3);
            $emis = substr($claveAcceso, 27, 3);
            $num = substr($claveAcceso, 30, 9);
            $nombre_archivo = '';
            if ($docCod == '01')
                $nombre_archivo = 'Factura';
            if ($docCod == '03')
                $nombre_archivo = 'Liquidacion de compras';
            if ($docCod == '04')
                $nombre_archivo = 'Nota de credito';
            if ($docCod == '05')
                $nombre_archivo = 'Nota de debito';
            if ($docCod == '06')
                $nombre_archivo = 'Guia de remision';
            if ($docCod == '07')
                $nombre_archivo = 'Comprobante de retencion';
            header('Content-Type: text/xml');
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$nombre_archivo-$esta-$emis-$num.xml");
            header("Content-Transfer-Encoding: binary");
            print_r($xml->saveXML());
        } elseif ($req->opcion == 'ValidarFuera') {
            echo $this->ValidarFuera('<?xml version="1.0" encoding="UTF-8"?>
            <guiaRemision id="comprobante" version="1.1.0">
              <infoTributaria>
                <ambiente>1</ambiente>
                <tipoEmision>1</tipoEmision>
                <razonSocial>EDUARDO FAUSTOS NIVELO</razonSocial>
                <nombreComercial>EDUARDO FAUSTOS NIVELO</nombreComercial>
                <ruc>0922729587001</ruc>
                <claveAcceso>0509202206092272958700110010010000000950000009517</claveAcceso>
                <codDoc>06</codDoc>
                <estab>001</estab>
                <ptoEmi>001</ptoEmi>
                <secuencial>000000095</secuencial>
                <dirMatriz>SAUCES 6 MZ 259 F43 V1</dirMatriz>
              </infoTributaria>
              <infoGuiaRemision>
                <dirEstablecimiento>Av de los Shiris</dirEstablecimiento>
                <dirPartida>La 21 Entre Oriente y sedalana</dirPartida>
                <razonSocialTransportista>Walter Alarcon</razonSocialTransportista>
                <tipoIdentificacionTransportista>05</tipoIdentificacionTransportista>
                <rucTransportista>0951561075</rucTransportista>
                <obligadoContabilidad>NO</obligadoContabilidad>
                <fechaIniTransporte>05/09/2022</fechaIniTransporte>
                <fechaFinTransporte>05/09/2022</fechaFinTransporte>
                <placa>dfr454</placa>
              </infoGuiaRemision>
              <destinatarios>
                <destinatario>
                  <identificacionDestinatario>0921605895</identificacionDestinatario>
                  <razonSocialDestinatario>Alex Mite</razonSocialDestinatario>
                  <dirDestinatario>los esteros</dirDestinatario>
                  <motivoTraslado>prueba</motivoTraslado>
                  <codEstabDestino>000</codEstabDestino>
                  <ruta>Sur - oeste</ruta>
                  <detalles>
                    <detalle>
                      <codigoInterno>6969</codigoInterno>
                      <codigoAdicional>6969</codigoAdicional>
                      <descripcion>PRUEBA 1</descripcion>
                      <cantidad>1</cantidad>
                      <detallesAdicionales>
                        <detAdicional nombre="observacion" valor="prueba 2"/>
                        <detAdicional nombre="descripcion" valor="hdfgh"/>
                      </detallesAdicionales>
                    </detalle>
                  </detalles>
                </destinatario>
              </destinatarios>
              <infoAdicional>
                <campoAdicional nombre="email">walarcon95@hotmail.com</campoAdicional>
                <campoAdicional nombre="telefono">0980631943</campoAdicional>
              </infoAdicional>
            </guiaRemision>
            ', 'guiaRemision');
        } elseif ($req->opcion == 'firmarFuera') {
            echo $this->firmarFuera('<?xml version="1.0" encoding="UTF-8"?>
            <guiaRemision id="comprobante" version="1.1.0">
              <infoTributaria>
                <ambiente>1</ambiente>
                <tipoEmision>1</tipoEmision>
                <razonSocial>EDUARDO FAUSTOS NIVELO</razonSocial>
                <nombreComercial>EDUARDO FAUSTOS NIVELO</nombreComercial>
                <ruc>0922729587001</ruc>
                <claveAcceso>0509202206092272958700110010010000000950000009517</claveAcceso>
                <codDoc>06</codDoc>
                <estab>001</estab>
                <ptoEmi>001</ptoEmi>
                <secuencial>000000095</secuencial>
                <dirMatriz>SAUCES 6 MZ 259 F43 V1</dirMatriz>
              </infoTributaria>
              <infoGuiaRemision>
                <dirEstablecimiento>Av de los Shiris</dirEstablecimiento>
                <dirPartida>La 21 Entre Oriente y sedalana</dirPartida>
                <razonSocialTransportista>Walter Alarcon</razonSocialTransportista>
                <tipoIdentificacionTransportista>05</tipoIdentificacionTransportista>
                <rucTransportista>0951561075</rucTransportista>
                <obligadoContabilidad>NO</obligadoContabilidad>
                <fechaIniTransporte>05/09/2022</fechaIniTransporte>
                <fechaFinTransporte>05/09/2022</fechaFinTransporte>
                <placa>dfr454</placa>
              </infoGuiaRemision>
              <destinatarios>
                <destinatario>
                  <identificacionDestinatario>0921605895</identificacionDestinatario>
                  <razonSocialDestinatario>Alex Mite</razonSocialDestinatario>
                  <dirDestinatario>los esteros</dirDestinatario>
                  <motivoTraslado>prueba</motivoTraslado>
                  <codEstabDestino>000</codEstabDestino>
                  <ruta>Sur - oeste</ruta>
                  <detalles>
                    <detalle>
                      <codigoInterno>6969</codigoInterno>
                      <codigoAdicional>6969</codigoAdicional>
                      <descripcion>PRUEBA 1</descripcion>
                      <cantidad>1</cantidad>
                      <detallesAdicionales>
                        <detAdicional nombre="observacion" valor="prueba 2"/>
                        <detAdicional nombre="descripcion" valor="hdfgh"/>
                      </detallesAdicionales>
                    </detalle>
                  </detalles>
                </destinatario>
              </destinatarios>
              <infoAdicional>
                <campoAdicional nombre="email">walarcon95@hotmail.com</campoAdicional>
                <campoAdicional nombre="telefono">0980631943</campoAdicional>
              </infoAdicional>
            </guiaRemision>
            ', 'guiaRemision', 'firma.p12', '3duard0faustos');
        } elseif ($req->opcion == 'pedirArchivoFuera') {
            return $this->pedirArchivoFuera($req->e, $req->c, $req->t);
        } elseif ($req->opcion == 'traerOrdenExamen') {
            return Examen_Orden::join('users as u', 'examen_orden.id_paciente', 'u.id')
                ->leftjoin('seguros as s', 'examen_orden.id_seguro', 's.id')
                ->where('comprobante', $req->n)
                ->where('examen_orden.id_empresa', $req->e)
                ->first([
                    'examen_orden.id',
                    'u.nombre1',
                    'u.nombre2',
                    'u.apellido1',
                    'u.apellido2',
                    's.nombre'
                ]);
        } elseif ($req->opcion == 'generarClaveAcceso') {
            return $this->generarClaveAcceso($req);
        } elseif ($req->opcion == 'descargarPDFC') {
            $xmlRespuesta = EmisionDocumentosController::obtenercomprobanteSRI($req->clave);
            $docCod = substr($req->clave, 8, 2);
            $esta = substr($req->clave, 24, 3);
            $emis = substr($req->clave, 27, 3);
            $num = substr($req->clave, 30, 9);
            $clave = $req->clave;
            $tipoText = '';
            $pdf_ = '';
            try {
                if ($docCod == '01') {
                    $pdf_ = 'sri_electronico.documentosgenerados.view_factura_sri';
                    $tipoText = 'Factura-';
                } elseif ($docCod == '03') {
                    $pdf_ = 'sri_electronico.documentosgenerados.view_liquidacioncompra_sri';
                    $tipoText = 'Liquidacion de compra-';
                } elseif ($docCod == '04') {
                    $pdf_ = 'sri_electronico.documentosgenerados.view_notaCredito_sri';
                    $tipoText = 'Nota de credito-';
                } elseif ($docCod == '05') {
                    $pdf_ = 'sri_electronico.documentosgenerados.view_notaDebito_sri';
                    $tipoText = 'Nota de debito-';
                } elseif ($docCod == '06') {
                    $pdf_ = 'sri_electronico.documentosgenerados.view_guiaremision_sri';
                    $tipoText = 'Guia de remision-';
                } elseif ($docCod == '07') {
                    $pdf_ = 'sri_electronico.documentosgenerados.view_compRetencion_sri';
                    $tipoText = 'Retencion-';
                }
                $data['xml'] = $xmlRespuesta;
                $view = \Illuminate\Support\Facades\View::make($pdf_, $data)->render();
                $pdf = \Illuminate\Support\Facades\App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'landscape');
                //return $pdf->download($tipoText . $esta . '-' . $emis . '-' . $num . '.pdf');
                return $pdf->stream($tipoText . $esta . '-' . $emis . '-' . $num . '.pdf');
            } catch (Exception $e) {
                echo $e->getMessage() . 'Linea: ' . $e->getLine();
                exit;
            }
        } elseif ($req->opcion == 'verificarFacturasHuman') {
            $sucursal = Ct_Sucursales::join('ct_caja as c', 'ct_sucursales.id', 'c.id_sucursal')->get();
            //dd($sucursal);
            echo '<form method="get" action="api_doc_electronico">
            <table>
            <tr>
                <td>
                    <input type="hidden" id="opcion" name="opcion" value="verificarFacturasHuman" />
                </td>
                <td>
                    <input type="date" id="d" name="d" value="' . $req->d . '" />
                </td>
                <td>
                    <input type="date" id="h" name="h" value="' . $req->h . '" />
                </td>
            </tr>
            <tr>
                <td><input type="submit" value="Enviar" /></td>
            </tr>
            </table></form>';
            if ($req->d != '' && $req->h != '') {

                $desde = date('Y-m-d', strtotime($req->d));
                $hasta = date('Y-m-d', strtotime($req->h));

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

                $urllink = 'https://ieced.siaam.ec/sis_medico/public';

                $url = $urllink . '/api_doc_electronico?opcion=listaOrdenesExamen&d=' . $desde . '&h=' . $hasta . '&s=' . $req->sucursal;

                $context = stream_context_create($options);
                try {
                    $orden = file_get_contents($url, false, $context);
                    if ($orden != '') {
                    }
                } catch (Exception $e) {
                    $orden = '';
                }
                if ($orden != '') {
                    $html = '';
                    $orden = json_decode($orden);

                    if (count($orden) > 0) {

                        $ventas = Ct_ventas::where('fecha', '>=', $desde)
                            ->where('fecha', '<=', $hasta)
                            ->where('estado', '!=', 0)
                            ->get();

                        if (count($ventas) > 0) {
                            $html = '<table>
                            <tr>
                            <td>No Orden</td>
                            <td>Fecha Orden</td>
                            <td>No Comprobante Orden</td>
                            <td>No Comprobante Factura</td>
                            <td>Valor Orden</td>
                            <td>Desc Orden</td>
                            <td>Total orden</td>
                            <td>Total Facturado</td>
                            <td>Saldo</td>
                            </tr>';
                            foreach ($ventas as $key => $row) {
                                foreach ($orden as $or) {
                                    if ($row->nro_comprobante == $or->comprobante) {
                                        $saldo = $or->total_valor - $row->total_final;
                                        $html .= '<tr>
                                        <td>' . $or->id . '</td>
                                        <td>' . $or->fecha_orden . '</td>
                                        <td>' . $or->comprobante . '</td>
                                        <td>' . $row->nro_comprobante . '</td>
                                        <td>' . $or->valor . '</td>
                                        <td>' . $or->descuento_valor . '</td>
                                        <td>' . $or->total_valor . '</td>
                                        <td>' . $row->total_final . '</td>
                                        <td>' . $saldo . '</td>
                                        </tr>';
                                        unset($orden[$key]);
                                    }
                                }
                            }
                            dd($orden);
                            $html .= '</table>';
                        }
                        if ($req->ex == 'ex') {
                            header("Expires: 0");
                            header("Content-type: application/x-msdownload");
                            header("Content-Disposition: attachment; filename=reporteOrdenes$desde-$hasta.xls");
                            header("Pragma: no-cache");
                            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                        }
                        echo $html;
                        exit;

                        $html = '<table>';
                        foreach ($orden as $row) {

                            $ventas = Ct_ventas::where('nro_comprobante', $row->comprobante)->where('estado', '!=', 0)->first();
                            if ($ventas != '') {
                                $html .= '<tr>
                                <td>' . $row->fecha_orden . '</td>
                                <td>' . $row->comprobante . '</td>
                                <td>' . $ventas->nro_comprobante . '</td>
                                <td>' . $row->valor . '</td>
                                <td>' . $row->descuento_valor . '</td>
                                <td>' . $row->total_valor . '</td>
                                <td>' . $ventas->total_final . '</td>
                                </tr>';
                            }
                        }
                        $html .= '</table>';
                    }
                    echo $html;
                }
            }
        } elseif ($req->opcion == 'masivoNotaCreditoCliente') {
            $this->masivo_asientos();
        } elseif ($req->opcion == 'apis') {
            $arrayApis = [
                'Factura' => [
                    'ReprocesarFacturaConglomerada' => [
                        'link' => 'https://dominio/apifacturaopciones/rep_conglomerada',
                        'type' => 'GET',
                        'data' => '{
                        "sucursal": "001", //Sucursal
                        "punto_emision": "002", //Punto de emision
                        "secuencial": "3690", //Secuencial
                        "empresa": "1391914857001", //Empresa
                        "desde": "2023-02-23", //Fecha un dia antes de la factura
                        "hasta": "2023-02-25", //Fecha un dia despues de la factura
                        "sku": "LABS-1216" //Codigo de un producto perteneciente a esa factura,
                        "verificardatos": 1 //Opcional si se desea ver el arreglo formado antes de generar la factura, quitar este campo para que el proceso continue
                    }',
                        'descipcion' => 'Api para reprocesar una factura conglomerada, cuando se autoriza en el sri pero no se genera el registro de la venta ni asientos',
                        'observaciones' => 'Solo esta validado si la forma de pago es pendiente de pago, no actualiza el numero de autorizacion'
                    ],
                ],
                'Retencion' => [
                    'GenerarSecuenciaRetenciones' => [
                        'link' => 'https://dominio/apiretencionopciones/generarsecuenciaretencion',
                        'type' => 'GET',
                        'data' => '{}',
                        'descipcion' => 'Api para agregar secuencia en tabla',
                        'observaciones' => 'Cuando no salen asientos en deudas vs pagos, es porque no tienen el numero secuencias de la retencion'
                    ]
                ],
                'NotaCreditoCliente' => [
                    'MasivoAsientoNotaCreditoCliente' => [
                        'link' => 'https://dominio/apinotacreditoclienteopciones/masivoNotaCreditoCliente',
                        'type' => 'GET',
                        'data' => '{
                            "anio":2023,
                            "mes":1,
                            "usuario":"0945878569"
                        }',
                        'descipcion' => 'Api para revisar asientos de notas de credito clientes',
                        'observaciones' => 'Cuando no se va el iva a la cuenta de la misma en el momento que se hace una nota de credito de clientes'
                    ]
                ]
            ];
            echo '<pre>';
            print_r($arrayApis);
            exit;
        } elseif ($req->opcion == 'revisarDocumentosElectronicos') {
            $data = [];
            $facturas = Ct_ventas::join('ct_clientes as c', 'ct_ventas.id_cliente', 'c.identificacion')
                ->join('de_pasos as d', 'ct_ventas.doc_electronico', 'd.id')
                ->where('ct_ventas.estado', '!=', 0)
                ->where('doc_electronico', '!=', 5)
                ->where('electronica', 1)
                ->where('revision_electronica', 3)
                ->get([
                    'ct_ventas.id',
                    'id_cliente',
                    'nro_comprobante',
                    'c.nombre',
                    'ct_ventas.fecha',
                    'ct_ventas.id_empresa',
                    'd.nombre as estado'
                ]);
            if (count($facturas) > 0) {
                $empresa = Empresa::find($facturas[0]->id_empresa);
            }
            $retenciones = Ct_Retenciones::join('proveedor as p', 'ct_retenciones.id_proveedor', 'p.id')
                ->join('de_pasos as d', 'ct_retenciones.doc_electronico', 'd.id')
                ->where('doc_electronico', '!=', 5)
                ->where('ct_retenciones.estado', '!=', 0)
                ->where('electronica', 1)
                ->get([
                    'ct_retenciones.id',
                    'id_proveedor',
                    'nro_comprobante',
                    'p.razonsocial',
                    'ct_retenciones.fecha',
                    'ct_retenciones.id_empresa',
                    'd.nombre as estado'
                ]);
            if (count($retenciones) > 0) {
                $empresa = Empresa::find($retenciones[0]->id_empresa);
            }
            if (isset($empresa) && $empresa != '') {
                $data = [
                    'empresa' => $empresa->razonsocial,
                    'facturas' => $facturas,
                    'retenciones' => $retenciones
                ];
            }
            return json_encode($data);
        } elseif ($req->opcion == 'verificarDocumentos') {
            $url = 'https://ieced.siaam.ec/sis_medico/public/api_doc_electronico?opcion=traerInfoServer';
            $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $nonce = EmisionDocumentosController::getNonce(12);
            $appId     = date('YmdHis') . '-GYE';
            $appSecret = 'jdjdjKJOS(/68759856498465';
            $date      = date('c');
            $Token     = base64_encode(sha1($nonce . $date . $appSecret));
            $options = [
                'http' => [
                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                    'method'  => 'GET',
                    'content' => 'ConexionApiDocumentosElectronicos',
                ],
                "ssl"  => [
                    "verify_peer"      => false,
                    "verify_peer_name" => false,
                ],
            ];
            $context = stream_context_create($options);
            try {
                $hosts = file_get_contents($url, false, $context);
                if ($hosts != '') {
                }
            } catch (Exception $e) {
                $hosts = '';
            }
            $html = '';
            if ($hosts != '') {
                $hosts_ = json_decode($hosts);
                for ($i = 0; $i < count($hosts_); $i++) {
                    $url_ = $hosts_[$i]->host_server . 'api_doc_electronico?opcion=revisarDocumentosElectronicos';
                    $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $nonce = EmisionDocumentosController::getNonce(12);
                    $appId     = date('YmdHis') . '-GYE';
                    $appSecret = 'jdjdjKJOS(/68759856498465';
                    $date      = date('c');
                    $Token     = base64_encode(sha1($nonce . $date . $appSecret));
                    $options = [
                        'http' => [
                            'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                            'method'  => 'GET',
                            'content' => 'ConexionApiDocumentosElectronicos',
                        ],
                        "ssl"  => [
                            "verify_peer"      => false,
                            "verify_peer_name" => false,
                        ],
                    ];
                    $context_ = stream_context_create($options);
                    $objF = file_get_contents($url_, false, $context_);

                    //decofico el json   
                    $hosts = json_decode($objF);

                    if (isset($hosts->empresa)) {
                        $html .= '<table width="100%">
                            <tr>
                                <td>Empresa: ' . $hosts->empresa . '</td>
                            </tr>
                            <tr>
                                <td width="100%">
                                    <table width="100%">
                                        <tr>
                                            <td>Id</td>
                                            <td>Tipo</td>
                                            <td>Comprobante</td>
                                            <td>Fecha</td>
                                            <td>Identificacion</td>
                                            <td>Cliente</td>
                                            <td>Estado</td>
                                        </tr>';
                    }
                    if ($hosts != '') {
                        if (isset($hosts->facturas)) {
                            foreach ($hosts->facturas as $row) {
                                $html .= '<tr>
                                <td>' . $row->id . '</td>
                                <td>Factura</td>
                                <td>' . $row->nro_comprobante . '</td>
                                <td>' . $row->fecha . '</td>
                                <td>' . $row->id_cliente . '</td>
                                <td>' . $row->nombre . '</td>
                                <td>' . $row->estado . '</td>
                                </tr>';
                            }
                        }
                        if (isset($hosts->retenciones)) {
                            foreach ($hosts->retenciones as $row) {
                                $html .= '<tr>
                            <td>' . $row->id . '</td>
                            <td>Retención</td>
                            <td>' . $row->nro_comprobante . '</td>
                            <td>' . $row->fecha . '</td>
                            <td>' . $row->id_proveedor . '</td>
                            <td>' . $row->razonsocial . '</td>
                            <td>' . $row->estado . '</td>
                            </tr>';
                            }
                        }
                    }
                    if (isset($hosts->empresa)) {
                        $html .= '</table>
                        </td>
                    </tr>
                    </table><br/>';
                    }
                }
                echo $html;
            }
        } elseif ($req->opcion == 'traerInfoServer') {
            return DB::table('log_info_server')->get();
        } elseif ($req->opcion == 'construirConglomerada') {
            echo '<pre>';
            print_r($_REQUEST);
            exit;
        } elseif ($req->opcion == 'envioCorreoPrueba') {
            try {
                $correoEnvio = '';
                $id_empresa = session('id_empresa');
                $empresa_nombre = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
                $comprobante_egreso_m  = Ct_Comprobante_Egreso_Masivo::where('id', 45)->first();
                $detalle_comprobante_m = Ct_Detalle_Comprobante_Egreso_Masivo::where('id_comprobante', $comprobante_egreso_m->id)->get();
                for ($i = 0; $i < count($detalle_comprobante_m); $i++) {
                    $empresa = Empresa::where('id', $comprobante_egreso_m->id_empresa)->first();
                    $correo  = Proveedor::where('id', $detalle_comprobante_m[$i]->id_proveedor)->first();
                    $array   = [
                        'abono' => $detalle_comprobante_m[$i]->abono,
                        'fecha' => $detalle_comprobante_m[$i]->created_at,
                        'concepto' => $detalle_comprobante_m[$i]->observacion,
                        'secuencia' => $detalle_comprobante_m[$i]->id_secuencia,
                        'saldo' => $detalle_comprobante_m[$i]->saldo_base
                    ];
                    if (!is_null($correo->email)) {
                        $correoEnvio = $correo->email;
                    } else {
                        $correoEnvio = $correo->email2;
                    }
                    $result = Mail::send('contable.comp_egreso_masivo.emision_varios', $array, function ($msj) use ($empresa_nombre) {
                        $msj->subject('Notificación de pago: ' . $empresa_nombre->razonsocial);
                        $msj->to("manzanita3434@gmail.com");
                        $msj->from("no-reply@mdconsgroup.com", "Documento Electrónico - MdConsgroup");
                        $msj->bcc('alexo8ec@hotmail.com');


                        /*$msj->subject('Detalle Proovedor', $empresa_nombre->razonsocial);
                        $msj->to("manzanita3434@gmail.com");
                        $msj->from('no-reply@mdconsgroup.com', "Notificación de Pago - $empresa_nombre->razonsocial");
                        $msj->bcc('torbi10@hotmail.com', 'fortiz@mdconsgroup.com', 'alexo8ec@hotmail.com');*/
                    });
                }
                return ['status' => true, 'msj' => 'Enviando Correctamente'];
            } catch (\Exception $e) {

                return ['status' => false, 'msj' => $e->getMessage()];
            }
        } elseif ($req->opcion == 'traerFactSeguros') {
            if (count($req->ordenes) > 0) {
                $arrayFactura = [];
                foreach ($req->ordenes as $row) {
                    $valor0 = explode('.', $row['saldo']);
                    $factura = Ct_ventas::whereYear('fecha', $row['anio'])
                        ->where('seguro_paciente', $row['id_seguro'])
                        ->where('subtotal_0', 'like', '%' . $valor0[0] . '%')
                        ->where('tipo', 'VENFA-CO')
                        ->where('estado', 1)
                        ->get();

                    if (count($factura) > 0) {

                        foreach ($factura as $value) {
                            $arrayDetalleFactura = [];
                            $arrayDetalleFactura = [
                                'id' => $value->id,
                                'sucursal' => $value->sucursal,
                                'punto_emision' => $value->punto_emision,
                                'numero' => $value->numero,
                                'nro_comprobante' => $value->nro_comprobante,
                                'nro_autorizacion' => $value->nro_autorizacion,
                                'subtotal_0' => $value->subtotal_0,
                                'subtotal_12' => $value->subtotal_12,
                                'total_final' => $value->total_final,
                                'id_orden' => $row['id'],
                                'saldo' => $row['saldo'],
                                'fecha' => $row['fecha']
                            ];

                            array_push($arrayFactura, $arrayDetalleFactura);
                        }
                        return $arrayFactura;
                    }
                }
            }
        } elseif ($req->opcion == 'masivoOrdenVenta') {
            $ordenes = Examen_Orden::selectRaw(
                'examen_orden.id,
                examen_orden.id_paciente as id_paciente_orden,
                examen_orden.anio,
                examen_orden.mes,
                examen_orden.comprobante,
                examen_orden.fecha_envio,
                c.id_cliente as id_cliente_ven_orden,
                c.id_paciente as id_paciente_ven_orden,
                s.id as id_seguro,
                s.nombre,
                c.nro_comprobante,
                c.nro_autorizacion,
                c.id_ct_venta,
                c.fecha,
                examen_orden.cobrar_pac_pct,
                examen_orden.total_valor,
                examen_orden.valor_con_oda,
                examen_orden.total_con_oda,
                examen_orden.total_valor-examen_orden.total_con_oda as saldo'
            )
                ->join('seguros as s', 'examen_orden.id_seguro', 's.id')
                ->join('ct_ven_orden as c', 'examen_orden.id', '=', 'c.orden_venta')
                ->where('examen_orden.estado', 1)
                //->whereNotNull('examen_orden.comprobante')
                ->where('cobrar_pac_pct', '<', 100)
                ->whereYear('c.fecha', $req->anio)
                ->where('c.revisado', 0)
                ->where('c.tipo', 'VEN-LABS')
                ->limit(30)
                ->get();
            $datos = [];
            if (count($ordenes) > 0) {
                $datos['ordenes'] = $ordenes;
            }
            $nonce = EmisionDocumentosController::getNonce(12);
            $appId = date('YmdHis') . '-C0n5ult4F4ctVr45';
            $appSecret = '198309100921605895' . date('YmdHis');
            $date = date('c');
            $Token = base64_encode(sha1($nonce . $date . $appSecret));
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                    'method'  => 'GET',
                    'content' => json_encode($datos),
                ),
                "ssl"  => array(
                    "verify_peer"      => false,
                    "verify_peer_name" => false,
                ),
            );
            $url = 'https://humanlabs.aitrol.com/api_doc_electronico?opcion=traerFactSeguros';
            $context = stream_context_create($options);
            $orden = '';
            try {
                $orden = file_get_contents($url, false, $context);

                if ($orden != '') {
                    $orden = json_decode($orden);
                }
            } catch (Exception $e) {
                $orden = [];
            }
            //echo '<pre>';print_r($orden);exit; 
            if (count($orden) > 0) {
                $html = '<table width="100%">
                    <tr>
                        <td>Id Venta</td>
                        <td>Id Orden</td>
                        <td>Fecha Ven_Orden</td>
                        <td>Sucursal</td>
                        <td>Emision</td>
                        <td>Numero</td>
                        <td>Comprobante</td>
                        <td>Autorizacion</td>
                        <td>Valor 0</td>
                        <td>Valor ' . ($this->porIva->iva * 100) . '</td>
                        <td>Total</td>
                        <td>Valor facturado</td>
                        <td>...</td>
                    </tr>';
                foreach ($orden as $row) {
                    $html .= '<tr>
                        <td>' . $row->id . '</td>
                        <td>' . $row->id_orden . '</td>
                        <td>' . $row->fecha . '</td>
                        <td>' . $row->sucursal . '</td>
                        <td>' . $row->punto_emision . '</td>
                        <td>' . str_pad($row->numero, 9, '0', STR_PAD_LEFT) . '</td>
                        <td>' . $row->nro_comprobante . '</td>
                        <td>' . $row->nro_autorizacion . '</td>
                        <td>' . $row->subtotal_0 . '</td>
                        <td>' . $row->subtotal_12 . '</td>
                        <td>' . $row->total_final . '</td>
                        <td>' . $row->saldo . '</td>
                        <td>
                            <input type="button" value="Actualizar" onclick="actualizarOrdenVenta(\'' . $row->id_orden . '\',\'' . $row->id . '\',\'' . $row->sucursal . '\',\'' . $row->punto_emision . '\',\'' .  str_pad($row->numero, 9, '0', STR_PAD_LEFT) . '\',\'' . $row->nro_autorizacion . '\',1)" />
                            <input type="button" value="Saltar" onclick="actualizarOrdenVenta(\'' . $row->id_orden . '\',\'' . $row->id . '\',\'' . $row->sucursal . '\',\'' . $row->punto_emision . '\',\'' .  str_pad($row->numero, 9, '0', STR_PAD_LEFT) . '\',\'' . $row->nro_autorizacion . '\',2)" />
                        </td>
                    </tr>
                    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
                    <script>
                    function actualizarOrdenVenta(id_orden,id_venta,sucursal,emision,numero,autorizacion,estado)
                    {
                        $.ajax({
                            url:"' . url('/') . '/api_doc_electronico?opcion=actualizarVenOrden",
                            type:"get",
                            data:{
                                id_orden:id_orden,
                                id_venta:id_venta,
                                sucursal:sucursal,
                                emision:emision,
                                numero:numero,
                                autorizacion:autorizacion,
                                estado:estado
                            },
                            dataType:"json",
                            success:function(data){
                                if(data.msj=="ok")
                                location.reload();
                            }
                        });
                    }
                    </script>';
                }
                echo $html;
            }
        } elseif ($req->opcion == 'actualizarVenOrden') {
            if ($req['estado'] == 1) {
                $arrayOrden = [
                    'nro_comprobante' => $req['sucursal'] . '-' . $req['emision'] . '-' . str_pad($req['numero'], 9, '0', STR_PAD_LEFT),
                    'nro_autorizacion' => $req['autorizacion'],
                    'id_ct_venta' => $req['id_venta'],
                    'revisado' => $req['estado']
                ];
            } elseif ($req['estado'] == 2) {
                $arrayOrden = [
                    'revisado' => $req['estado']
                ];
            }
            Ct_Ven_Orden::where('orden_venta', $req['id_orden'])->update($arrayOrden);
            return json_encode(['msj' => 'ok']);
        } elseif ($req->opcion == 'enviocorreos') {

            $correos =  DB::table("new_db_estado_correos")->where('procesado_estado_correo', 0)
                ->limit(1)
                ->get();
            if (count($correos) > 0) {

                foreach ($correos as $row) {
                    $paciente = Paciente::join("users", "paciente.id", "users.id")
                        ->where("paciente.id", $row->id_paciente_estado_correo)
                        ->first([
                            'paciente.nombre1',
                            'paciente.nombre2',
                            'paciente.apellido1',
                            'paciente.apellido2',
                            'users.email'
                        ]);
                    $messageData = [
                        'paciente' => $paciente,
                        'msj' => $row->mensaje_estado_correo
                    ];
                    $subject = 'Feliz cumpleaños | ' . $paciente->nombre1 . ' ' . $paciente->nombre2 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2;
                    try {
                        Mail::send('paciente/view_paciente_cumpleanos', $messageData, function ($mail) use ($subject, $paciente) {
                            $mail->from('noreply@aitrol.com', 'Gestor de correos - Aitrol V3.0');
                            $mail->bcc('alexo8ec@hotmail.com', 'Gestor de correos - Aitrol V3.0');
                            $mail->bcc('fortiz@mdconsgroup.com', 'Gestor de correos - Aitrol V3.0');
                            $mail->to('amite@mdconsgroup.com', $paciente->nombre1 . ' ' . $paciente->apellido1);
                            $mail->subject($subject);
                        });
                        $arrayResuldato = [
                            'procesado_estado_correo' => 1,
                            'updated_at_estado_correo' => date('Y-m-d H:m:i'),
                            'fecha_envio_estado_correo' => date('Y-m-d H:m:i'),
                        ];
                        DB::table("new_db_estado_correos")->where('id_estado_correo', $row->id_estado_correo)->update($arrayResuldato);
                    } catch (Exception $e) {
                        $arrayResuldato = [
                            'procesado_estado_correo' => 2,
                            'updated_at_estado_correo' => date('Y-m-d H:m:i'),
                            'respuesta_estado_correo' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
                        ];
                        DB::table("new_db_estado_correos")->where('id_estado_correo', $row->id_estado_correo)->update($arrayResuldato);
                    }
                }
            }
        }
    }

    private function emitirDocumentosConResumen(Request $req)
    {
        $idUsuario = isset($req->idUsuario) ? $req->idUsuario : 'FACELECTRO';
        $emisores = [
            'Guía de remisión' => function () use ($idUsuario) {
                return $this->getGuiaRemision(null, $idUsuario);
            },
            'Nota de crédito' => function () use ($idUsuario) {
                return $this->getNotaCredito(null, $idUsuario);
            },
            'Factura' => function () use ($idUsuario) {
                return $this->getFactura(null, $idUsuario);
            },
            'Retención' => function () use ($idUsuario) {
                return $this->getRetencion(null, $idUsuario);
            },
            'Liquidación' => function () use ($idUsuario) {
                return $this->getLiquidacion(null, $idUsuario);
            },
            'Factura de pago' => function () use ($idUsuario) {
                return $this->getCrearFacturaPago(null, $idUsuario);
            },
        ];

        $documentos = [];
        foreach ($emisores as $tipoDocumento => $emisor) {
            $documentos[] = $this->ejecutarEmisorParaResumen($tipoDocumento, $emisor);
        }

        $resumen = [
            'total' => count($documentos),
            'completados' => 0,
            'sin_pendientes' => 0,
            'avisos' => 0,
            'errores' => 0,
        ];
        foreach ($documentos as $documento) {
            if ($documento['estado'] === 'completado') {
                $resumen['completados']++;
            } elseif ($documento['estado'] === 'sin_pendientes') {
                $resumen['sin_pendientes']++;
            } elseif ($documento['estado'] === 'error') {
                $resumen['errores']++;
            } else {
                $resumen['avisos']++;
            }
        }

        return response()->json([
            'ok' => $resumen['errores'] === 0,
            'resumen' => $resumen,
            'documentos' => $documentos,
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function ejecutarEmisorParaResumen($tipoDocumento, callable $emisor)
    {
        $nivelBuffer = ob_get_level();
        ob_start();
        try {
            $retorno = call_user_func($emisor);
            $salida = trim(ob_get_clean());
        } catch (Exception $ex) {
            while (ob_get_level() > $nivelBuffer) {
                ob_end_clean();
            }
            return [
                'tipo' => $tipoDocumento,
                'estado' => 'error',
                'codigo' => 500,
                'mensaje' => 'Ocurrió un error al procesar el documento.',
                'detalle' => $ex->getMessage(),
                'linea' => $ex->getLine(),
            ];
        }

        if ($salida === '' && is_string($retorno)) {
            $salida = trim($retorno);
        }
        if ($salida === '') {
            return [
                'tipo' => $tipoDocumento,
                'estado' => 'sin_pendientes',
                'codigo' => 204,
                'mensaje' => 'No existen documentos para emitir.',
            ];
        }

        $respuesta = json_decode($salida, true);
        if (!is_array($respuesta)) {
            return [
                'tipo' => $tipoDocumento,
                'estado' => 'aviso',
                'codigo' => null,
                'mensaje' => 'El proceso devolvió una respuesta no estructurada.',
                'detalle' => $salida,
            ];
        }

        $codigo = isset($respuesta['code']) ? (int) $respuesta['code'] : null;
        $mensaje = isset($respuesta['message']) ? $respuesta['message'] : 'Proceso ejecutado.';
        $estado = 'aviso';
        if ($codigo !== null && $codigo >= 500) {
            $estado = 'error';
        } elseif ($codigo === 204 && stripos($mensaje, 'No existen documentos') !== false) {
            $estado = 'sin_pendientes';
        } elseif ($codigo !== null && $codigo >= 200 && $codigo < 300) {
            $estado = 'completado';
        }

        $resultado = [
            'tipo' => isset($respuesta['tipoDoc']) ? $respuesta['tipoDoc'] : $tipoDocumento,
            'estado' => $estado,
            'codigo' => $codigo,
            'mensaje' => $mensaje,
        ];
        if (isset($respuesta['data']) && $respuesta['data'] !== '' && $respuesta['data'] !== []) {
            $resultado['detalle'] = $respuesta['data'];
        }

        return $resultado;
    }

    public function getCrearFacturaPago($dinfo = null, $idUsuario = 'FACELECTRO')
    {
        DB::beginTransaction();
        $idusuario =  $idUsuario;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $transaccion = TransaccionPago::where('estado', 1)
            ->where('facturado', 0)
            ->get();
        if (count($transaccion) > 0) {
            $datos = [];
            foreach ($transaccion as $row) {
                $datos = json_decode($row->data_request);
                $ip_cliente = $_SERVER["REMOTE_ADDR"];
                $fecha_as = $row->create_at;
                $id_empresa = $row->id_empresa;
                $llevaOrden = false;
                $numero1 = null;
                $nfactura = 0;
                $id_paciente = null;
                $num_comprobante = 0;
                $empresa = Empresa::find($id_empresa);
                $cliente = Clientes::where('identificacion', $datos->datos_factura->id_usuario)->count();
                try {
                    if ($cliente == 0) {
                        $nombre_cliente = strtoupper($datos->datos_factura->nombre1 . ' ' . $datos->datos_factura->nombre2 . ' ' . $datos->datos_factura->apellido1 . ' ' . $datos->datos_factura->apellido2);
                        $arrayCliente = [
                            'nombre' => $nombre_cliente,
                            'tipo' => strlen($datos->datos_factura->id_usuario) == 13 ? 4 : 5,
                            'identificacion' => $datos->datos_factura->id_usuario,
                            'clase' => 1,
                            'nombre_representante' => $nombre_cliente,
                            'cedula_representante' => $datos->datos_factura->id_usuario,
                            'ciudad_representante' => $datos->datos_factura->ciudad,
                            'direccion_representante' => $datos->datos_factura->direccion,
                            'telefono1_representante' => $datos->datos_factura->telefono_factura,
                            'email_representante' => $datos->datos_factura->email_factura,
                            'estado' => 1,
                            'id_usuariocrea' => $idUsuario,
                            'id_usuariomod' => $idUsuario,
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                        ];
                        Clientes::insert($arrayCliente);
                        $cliente = Clientes::where('identificacion', $datos->datos_factura->id_usuario)->first();
                    }
                    $array = [
                        'sucursal' => "001", //001
                        'punto_emision' => "100", //100
                        'tipo' => 1
                    ];
                    $num = InfoTributaria::where('cod_sucursal', '001')
                        ->where('cod_caja', '100')
                        ->where('id_empresa', $row->id_empresa)
                        ->first([
                            'secuencial_nro'
                        ]);
                    if ($num != '') {
                        $num = $num->secuencial_nro + 1;
                    } else {
                        $num = 1;
                    }
                    $num = $array['sucursal'] . '-' . $array['punto_emision'] . '-' . str_pad($num, 9, '0', STR_PAD_LEFT);
                    //$numCompro = app('Sis_medico\v3\contable\Ventas')->callPrivateStisGenerarNumero($array);
                    $arrayAsientoCabecera = [
                        'sucursal' => "001",
                        'punto_emision' => "100",
                        'fecha_asiento' => date("Y-m-d"),
                        'fact_numero' => $num,
                        'id_empresa' => $row->id_empresa,
                        'observacion' => $datos->cart_items[0]->description,
                        'valor' => $datos->total,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    $numComprobante = explode('-', $num);
                    $claveObj = new ClaveAcceso();
                    $id_asiento_cabecera = Contabilidad::apiCrearAsiento('ventas', 'ingreso', $arrayAsientoCabecera,  $datos, "venta", 0, '', $id_empresa);
                    if ($id_asiento_cabecera["status"] == 'error') {
                        DB::rollBack();
                        throw new Exception("No se encontro la cuenta de clientes");
                    }
                    // dd(AsientosCabecera::find($id_asiento_cabecera),AsientosDetalles::where("id_asiento_cabecera",$id_asiento_cabecera)->get());
                    $deEmpresa = DeEmpresas::where('id_empresa', $id_empresa)->first();
                    $campos = [
                        'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime(date("Y-m-d")))),
                        'tipo_comprobante' => '01',
                        'ruc' => $id_empresa,
                        'tipo_ambiente' => $deEmpresa->ambiente,
                        'punto_establecimiento' => $numComprobante[0],
                        'punto_emision' => $numComprobante[1],
                        'numero_comprobante' => $numComprobante[2],
                        'codigo_numerico' => $numComprobante[2],
                        'tipo_emision' => 1
                    ];
                    $claveObj->llenarDatos($campos);
                    $pacis = Paciente::find($datos->usuario->id);
                    $pac = "";
                    if (is_null($pacis)) {
                        $id_paciente = '9999999999';
                        $pac = $datos->usuario->apellido1 . ' ' . $datos->usuario->apellido2 . ' ' . $datos->usuario->nombre1;
                    } else {
                        $id_paciente = $datos->usuario->id;
                        $pac = $pacis->apellido1 . ' ' . $pacis->apellido2 . ' ' . $pacis->nombre1;
                    }
                    $arrayCabeceraVenta = [
                        'sucursal' => "001",
                        'punto_emision' => "100",
                        'numero' => $numComprobante[2],
                        'nro_comprobante' => $num,
                        'id_asiento' => $id_asiento_cabecera,
                        'id_empresa' => $id_empresa,
                        'tipo' => "VEN-FA",
                        'fecha' => date("Y-m-d"),
                        'divisas' => 1,
                        'nombre_cliente' => $cliente->nombre,
                        'tipo_consulta' => "",
                        'id_cliente' => $datos->identificacion,
                        'direccion_cliente' => $datos->direccion_cliente == $cliente->direccion_representante ? $cliente->direccion_representante : $datos->direccion_cliente,
                        'ruc_id_cliente' => $datos->identificacion,
                        'telefono_cliente' => $datos->telefono,
                        'email_cliente' => $datos->email == $cliente->email_representante ? $cliente->email_representante : $datos->email,
                        'orden_venta' => "",
                        'nro_autorizacion' => $claveObj->clave_acceso,
                        'id_paciente' => $id_paciente,
                        'nombres_paciente' => $pac,
                        'seguro_paciente' => 1,
                        'id_hc_procedimiento' => "",
                        'concepto' => $datos->cart_items[0]->description,
                        'copago' => 0,
                        'electronica' => 1,
                        'subtotal_0' => $datos->subtotal_0,
                        'subtotal_12' => $datos->subtotal_12,
                        'descuento' => 0,
                        'base_imponible' => $datos->total,
                        'impuesto' => $datos->valor_iva,
                        'total_final' => $datos->total,
                        'valor_contable' => 0,
                        'dias_plazo' => 0,
                        'nota_electronica' => json_encode([]),
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'mensaje_kardex' => "",
                        'nota_array' => json_encode([]),
                        'doc_electronico' => 0,
                        'emisor' => 'MD',
                        'contraentrega' => "",
                    ];
                    $idVenta = Ventas::insertGetId($arrayCabeceraVenta);
                    $arrayAsientoCabecera = [
                        'sucursal' => "001",
                        'punto_emision' => "100",
                        'fecha_asiento' => date("Y-m-d"),
                        'fact_numero' => $num,
                        'id_empresa' => $id_empresa,
                        'observacion' => 'COMPROBANTE DE INGRESO FACT:' . $num,
                        'valor' => $datos->total,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    $id_asiento_cabecera = Contabilidad::apiCrearAsiento('ventas', 'ingreso', $arrayAsientoCabecera, $datos, "comprobante_ingreso", 0, '', $id_empresa);
                    if ($id_asiento_cabecera['status'] == 'error') {
                        DB::rollBack();
                        throw new Exception($id_asiento_cabecera['message']);
                    }
                    for ($j = 0; $j < count($datos->cart_items); $j++) {
                        $ctProductos = Productos::where('codigo', $datos->cart_items[$j]->name)->first();
                        if ($ctProductos == '') {
                            $arrayProducto = [
                                'codigo' => str_replace(' ', '', strtoupper($datos->cart_items[$j]->name)),
                                'nombre' => $datos->cart_items[$j]->name,
                                'descripcion' => $datos->cart_items[$j]->name,
                                'grupo' => 3,
                                'cta_gastos' => '1.01.03.01.03',
                                'cta_ventas' => '4.1.01.01',
                                'cta_costos' => '5.1.01.07',
                                'cta_devolucion' => '4.1.07',
                                'reg_serie' => 0,
                                'mod_precio' => 0,
                                'mod_desc' => 0,
                                'impuesto_iva_compras' => '1.01.01.1.01',
                                'impuesto_iva_ventas' => '1.01.01.1.01',
                                'impuesto_servicio' => 1,
                                'impuesto_ice' => 1,
                                'clasificacion_impuesto_ice' => 1,
                                'promedio' => 0,
                                'reposicion' => 0,
                                'lista' => 0,
                                'ultima_compra' => 0,
                                'ident_paquete' => 0,
                                'valor_total_paq' => $datos->cart_items[$j]->price,
                                'estado_tabla' => 1,
                                'id_usuariocrea' => $idusuario,
                                'id_usuariomod' => $idusuario,
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'created_at' => date('Y-m-d H:is'),
                                'updated_at' => date('Y-m-d H:is'),
                                'iva' => 0,
                                'id_empresa' => $id_empresa,
                                'tipo_objeto' => 'P',
                                'tipo_producto' => 0
                            ];
                            $idP = Productos::insertGetId($arrayProducto);
                            $ctProductos = Productos::find($idP);
                        }
                        $detalle = [
                            'id_ct_ventas' => $idVenta,
                            'id_ct_productos' => $ctProductos->id,
                            'nombre' => $ctProductos->nombre,
                            'descripcion_producto' =>  $ctProductos->descripcion,
                            'bodega' => "",
                            'cantidad' => 1,
                            'precio' => $datos->cart_items[$j]->price,
                            'descuento_porcentaje' => 0,
                            'descuento' => 0,
                            'extendido' => $datos->cart_items[$j]->price,
                            'detalle' => "",
                            'check_iva' => 0,
                            'porcentaje' => config('data.impuestoIvaPorcentaje'),
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'otros' => json_encode([]),
                        ];
                        VentasDetalle::insert($detalle);
                    }
                    /*echo '<pre>';
                    print_r($datos);
                    DB::rollBack();
                    exit;*/
                    $arrayComprobante = [
                        'observaciones' => 'COMPROBANTE DE INGRESO FACT: ' . $num,
                        'estado' => '1',
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'fecha' => date("Y-m-d"),
                        'secuencia' =>  $num,
                        'divisas' => '1',
                        'id_empresa' => $id_empresa,
                        'total_ingreso' => $datos->total,
                        'id_cliente' => $datos->identificacion,
                        'autollenar' => "Cancela FV : " .  $num,
                        'id_vendedor' =>  isset($datos->datos_orden->membresia) ? $datos->datos_orden->membresia : null,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    $id_comprobante = ComprobanteIngreso::insertGetId($arrayComprobante);
                    for ($i = 0; $i < count($datos->cart_items); $i++) {
                        try {
                            $arrayDetalleComprobante = [
                                'id_comprobante'  => $id_comprobante,
                                'fecha' => date("Y-m-d"),
                                'id_tipo' => "10",
                                'total' => $datos->cart_items[$i]->price,
                                'estado' => '1',
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea' => $idusuario,
                                'id_usuariomod' => $idusuario,
                            ];
                            DetallePagoIngreso::insert($arrayDetalleComprobante);
                        } catch (Exception $e) {
                            DB::rollBack();
                            $arrayRespuesta = [
                                'status' => 'error',
                                'code' => 500,
                                'message' => $e->getMessage(),
                                'linea' => $e->getLine(),
                                'file' => $e->getFile(),
                            ];
                            echo '<pre>';
                            print_r($arrayRespuesta);
                        }
                        try {
                            $arrayFormaPago = [
                                'id_ct_ventas' => $idVenta,
                                'tipo' => "10",
                                'fecha' => date("Y-m-d"),
                                'valor' => $datos->cart_items[$i]->price,
                                'valor_base' => $datos->cart_items[$i]->price,
                                'id_usuariocrea' => $idusuario,
                                'id_usuariomod' => $idusuario,
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                            ];
                            FormaPago::insert($arrayFormaPago);
                        } catch (Exception $e) {
                            DB::rollBack();
                            $arrayRespuesta = [
                                'status' => 'error',
                                'code' => 500,
                                'message' => $e->getMessage(),
                                'linea' => $e->getLine(),
                                'file' => $e->getFile(),
                            ];
                            echo '<pre>';
                            print_r($arrayRespuesta);
                        }
                    }
                    try {
                        $arrayDetalleComprobanteIngreso = [
                            'id_comprobante' => $id_comprobante,
                            'fecha' => date("Y-m-d"),
                            'observaciones' => "Cancela FV : " . $num,
                            'id_factura' => $idVenta,
                            'secuencia_factura' => $num,
                            'total_factura' => $datos->total,
                            'total' => $datos->total,
                            'estado' => '1',
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                        ];
                        DetalleComprobanteIngreso::insert($arrayDetalleComprobanteIngreso);
                    } catch (Exception $e) {
                        DB::rollBack();
                        $arrayRespuesta = [
                            'status' => 'error',
                            'code' => 500,
                            'message' => $e->getMessage(),
                            'linea' => $e->getLine(),
                            'file' => $e->getFile(),
                        ];
                        echo '<pre>';
                        print_r($arrayRespuesta);
                    }
                    $arrayInfoTributaria = [
                        'secuencial_nro' => (int)$numComprobante[2],
                        'numero_factura' => $numComprobante[0] . '-' . $numComprobante[1] . '-' . str_pad($numComprobante[2], 9, 0, STR_PAD_LEFT),
                    ];
                    InfoTributaria::where('id_empresa', $id_empresa)
                        ->where('id_maestro_documentos', 1)
                        ->where('cod_sucursal', "001")
                        ->where('cod_caja', "100")
                        ->update($arrayInfoTributaria);
                    $arrayTransaccion = [
                        'facturado' => 1,
                        'ride' => $numComprobante[0] . '-' . $numComprobante[1] . '-' . str_pad($numComprobante[2], 9, 0, STR_PAD_LEFT)
                    ];
                    TransaccionPago::where('id', $row->id)->update($arrayTransaccion);
                    /*$venta = Ventas::find($idVenta);
                    $transa = TransaccionPago::find($row->id);*/
                    DB::commit();
                    //DB::rollBack();
                    $arrayRespuesta = [
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Registro guardado correctamente',
                    ];
                    echo '<pre>';
                    print_r($arrayRespuesta);
                } catch (Exception $e) {
                    DB::rollBack();
                    $arrayRespuesta = [
                        'status' => 'error',
                        'code' => 500,
                        'message' => $e->getMessage(),
                        'linea' => $e->getLine(),
                        'file' => $e->getFile(),
                    ];
                    echo '<pre>';
                    print_r($arrayRespuesta);
                }
            }
        }
    }
    public function examenventas(Request $r)
    {
        $selectGatroclinica = '';
        $selectGatroquito = '';
        $selectGastrosanto = '';
        $selectGastromanta = '';
        $selectGastromilagro = '';
        $selectGastromachala = '';
        $selectGastrobabahoyo = '';
        if ($r->url == 'https://ieced.siaam.ec/sis_medico/public/examenventas')
            $selectGatroclinica = 'selected';
        elseif ($r->url == 'https://gastroquito.siaam.ec/public/examenventas')
            $selectGatroquito = 'selected';
        elseif ($r->url == 'https://gastrosanto.aitrol.com/public/examenventas')
            $selectGastrosanto = 'selected';
        elseif ($r->url == 'https://gastromanta.aitrol.com/public/examenventas')
            $selectGastromanta = 'selected';
        elseif ($r->url == 'https://gastromilagro.aitrol.com/public/examenventas')
            $selectGastromilagro = 'selected';
        elseif ($r->url == 'http://gastromachala.aitrol.com/public/examenventas')
            $selectGastromachala = 'selected';
        elseif ($r->url == 'https://gastrobabahoyo.aitrol.com/public/examenventas')
            $selectGastrobabahoyo = 'selected';
        $html = '<button onclick="exportTableToExcel(\'Reporte\', \'Reporte Ventas y ordenes examenes' . date('Y-m-d H:i:s') . '\')">Exportar archivo en excel</button>
        <form method="post" action="https://humanlabs.aitrol.com/app/examenventas">
        Desde: <input type="date" name="desde" value="' . $r->desde . '" />
        Hasta: <input type="date" name="hasta" value="' . $r->hasta . '" />
        <select id="url" name="url">
        <option value="">--Seleccionar sucursal--</option>
        <option value="https://ieced.siaam.ec/sis_medico/public/examenventas" ' . $selectGatroclinica . ' >Gastroclinica</option>
        <option value="https://gastroquito.siaam.ec/public/examenventas" ' . $selectGatroquito . ' >Gastroquito</option>
        <option value="https://gastrosanto.aitrol.com/public/examenventas" ' . $selectGastrosanto . ' >Gastrosanto</option>
        <option value="https://gastromanta.aitrol.com/public/examenventas" ' . $selectGastromanta . ' >Gastromanta</option>
        <option value="https://gastromilagro.aitrol.com/public/examenventas" ' . $selectGastromilagro . ' >Gastromilagro</option>
        <option value="http://gastromachala.aitrol.com/public/examenventas" ' . $selectGastromachala . ' >Gastromachala</option>
        <option value="https://gastrobabahoyo.aitrol.com/public/examenventas" ' . $selectGastrobabahoyo . '>Gastrobabahoyo</option>
        </select>
        <input type="submit" value="Buscar">
        </form>
        <br/>
        <table id="Reporte" style="width:100%;font-size:10px;">
        <tr>
        <td>Fecha</td>
        <td>Sucursal</td>
        <td>Local</td>
        <td>Identificacion</td>
        <td>Cliente</td>
        <td>Paciente</td>
        <td>Orden</td>
        <td>Tipo</td>
        <td>Documento</td>
        <td>Seguro</td>
        <td>Id agrupador</td>
        <td>Agrupador</td>
        <td>Código</td>
        <td>Producto</td>
        <td>Precio</td>
        <td>Descuento</td>
        <td>Total</td>
        </tr>';
        if ($r->url != '') {
            $appId = "wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=";
            $json_request = [
                'fecha_desde' => $r->desde,
                'fecha_hasta' => $r->hasta,
            ];
            $elemento = json_encode($json_request);
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                    'method'  => 'POST',
                    'content' => $elemento,
                    'timeout' => 600,
                ),
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $context = stream_context_create($options);
            $response = file_get_contents($r->url, false, $context);
            $respuesta = json_decode($response, true);
            ini_set('max_execution_time', 300);
            $arrayComprobante = [];
            if (count($respuesta) > 0) {
                foreach ($respuesta as $row) {
                    array_push($arrayComprobante, $row['comprobante']);
                }
                $ventas = Ct_ventas::join('seguros as s', 'ct_ventas.seguro_paciente', 's.id')
                    ->join('ct_detalle_venta as dv', 'ct_ventas.id', 'dv.id_ct_ventas')
                    ->leftjoin('examen as e', function ($q) {
                        $q->on(DB::Raw('REPLACE(dv.id_ct_productos,"LABS-","")'), 'e.id');
                    })
                    ->leftjoin('ct_sucursales as su', function ($q) {
                        $q->on('ct_ventas.sucursal', 'su.codigo_sucursal')
                            ->where('su.id_empresa', '0993075000001');
                    })
                    ->leftjoin('examen_agrupador as ea', 'e.id_agrupador', 'ea.id')
                    ->whereIn('ct_ventas.nro_comprobante', $arrayComprobante)
                    ->where('ct_ventas.estado', '1')
                    ->where('ct_ventas.id_empresa', '0993075000001')
                    ->get([
                        'ct_ventas.fecha',
                        'ct_ventas.id_cliente',
                        'ct_ventas.nombre_cliente',
                        'ct_ventas.nombres_paciente',
                        'ct_ventas.sucursal',
                        'ct_ventas.tipo',
                        'ct_ventas.nro_comprobante',
                        'dv.precio',
                        DB::Raw('ifnull(dv.descuento,0) as descuento'),
                        'dv.nombre as produto',
                        'dv.id_ct_productos',
                        's.nombre as seguro',
                        'e.id_agrupador as id',
                        'ea.nombre as agrupador',
                        'su.nombre_sucursal'
                    ])
                    ->toArray();
                foreach ($ventas as $key => $vali) {
                    foreach ($respuesta as $val) {
                        if ($vali['nro_comprobante'] == $val['comprobante']) {
                            $ventas[$key]["orden"] = $val["id"];
                        }
                    }
                }
                $total = 0;
                foreach ($ventas as $row) {
                    $total = $row['precio'] - $row['descuento'];
                    $html .= '<tr>
                <td>' . $row['fecha'] . '</td>
                <td style="mso-number-format:\'@\'">' . $row['sucursal'] . '</td>
                <td>' . $row['nombre_sucursal'] . '</td>
                <td style="mso-number-format:\'@\'">' . $row['id_cliente'] . '</td>
                <td>' . $row['nombre_cliente'] . '</td>
                <td>' . $row['nombres_paciente'] . '</td>
                <td>' . $row['orden'] . '</td>
                <td>' . $row['tipo'] . '</td>
                <td>' . $row['nro_comprobante'] . '</td>
                <td>' . $row['seguro'] . '</td>
                <td>' . $row['id'] . '</td>
                <td>' . $row['agrupador'] . '</td>
                <td>' . $row['id_ct_productos'] . '</td>
                <td>' . $row['produto'] . '</td>
                <td align="right">' . str_replace(',', '', number_format($row['precio'], 4)) . '</td>
                <td align="right">' . str_replace(',', '', number_format($row['descuento'], 4)) . '</td>
                <td align="right">' . str_replace(',', '', number_format($total, 4)) . '</td>
                </tr>';
                }
                $html .= '</table>
            <script>
                function exportTableToExcel(tableID, filename = \'\'){
                    var downloadLink;
                    var dataType = \'application/vnd.ms-excel\';
                    var tableSelect = document.getElementById(tableID);
                    var tableHTML = tableSelect.outerHTML.replace(/ /g, \'%20\');                    
                    filename = filename?filename+\'.xls\':\'excel_data.xls\';                    
                    downloadLink = document.createElement("a");                    
                    document.body.appendChild(downloadLink);                    
                    if(navigator.msSaveOrOpenBlob){
                        var blob = new Blob([\'ufeff\', tableHTML], {
                            type: dataType
                        });
                        navigator.msSaveOrOpenBlob( blob, filename);
                    }else{
                        downloadLink.href = \'data:\' + dataType + \', \' + tableHTML;                    
                        downloadLink.download = filename;                        
                        downloadLink.click();
                    }
                }
                </script>';
            }
        }
        echo $html;
    }
    public function opcionesNotasCreditoCliente(Request $r)
    {
        if ($r->opcion == 'masivoNotaCreditoCliente') {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $consulta = Ct_Asientos_Cabecera::where('estado', 1)
                ->whereYear('fecha_asiento', $r->anio)
                ->whereMonth('fecha_asiento', $r->mes)
                ->get();
            DB::beginTransaction();
            try {
                $cont = 0;
                $arrayTermino = [];
                foreach ($consulta as $value) {
                    $nota_credito = Ct_Nota_Credito_Clientes::where('id_asiento_cabecera', $value->id)
                        ->first();
                    if ($nota_credito != '') {
                        if ($nota_credito['impuesto'] > 0) {
                            $detalle = Ct_Asientos_Detalle::where('id_asiento_cabecera', $value->id)
                                ->get();
                            $existe = 0;
                            foreach ($detalle as $port) {
                                if ($port->id_plan_cuenta == '2.01.07.01.01') {
                                    $existe = 1;
                                }
                            }
                            if ($existe == 0) {
                                if ($port['id_plan_cuenta'] == '4.1.07.01') {
                                    $cuentaU = Ct_Asientos_Detalle::find($port->id);
                                    $opereacion =  $cuentaU->debe - $nota_credito['impuesto'];
                                    $cuentaU->debe = $opereacion;
                                    $cuentaU->save();
                                }
                                $id_plan_confg = LogConfig::busqueda('2.01.07.01.01');
                                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();
                                $arrayAsiento = [
                                    'id_asiento_cabecera' => $value->id,
                                    'id_plan_cuenta'      => $plan_cuentas->id,
                                    'descripcion'         => $plan_cuentas->nombre,
                                    'fecha'               => date('Y-m-d'),
                                    'debe'                => $nota_credito['impuesto'],
                                    'haber'               => '0',
                                    'id_usuariocrea'      => $r->usuario,
                                    'id_usuariomod'       => $r->usuario,
                                    'ip_creacion'         => 'masivoApi' . date('Ymd'),
                                    'ip_modificacion'     => $ip_cliente,
                                ];
                                Ct_Asientos_Detalle::insert($arrayAsiento);
                                $cont++;
                                $arrayInfo = [
                                    'idAsiento' => $value->id,
                                    'dato' => 'Asiento corregido correctamente',
                                    'fila' => $arrayAsiento
                                ];
                                array_push($arrayTermino, $arrayInfo);
                            }
                            DB::commit();
                        } else {
                            $arrayInfo = [
                                'dato' => 'No existen asientos de notas de credito de clientes con errores...',
                            ];
                            array_push($arrayTermino, $arrayInfo);
                        }
                    }
                }
                if ($cont == 0) {
                    $arrayDato = [
                        'dato' => 'No existen asientos de notas de credito con errores...'
                    ];
                    return json_encode($arrayDato);
                } else {
                    return json_encode($arrayTermino);
                }
            } catch (Exception $e) {
                DB::rollBack();
                $arrayError = [
                    'error' => $e->getMessage(),
                    'linea' => $e->getLine()
                ];
                return json_encode($arrayError);
            }
        } else {
            echo 'aun no existe opcion para este nombre: ' . $r->opcion;
        }
    }
    public function opcionesPaciente(Request $request)
    {
        if ($request->opcion == '') {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            if ($request->opcion == 'mails') {
                $dayOfBirthay = Paciente::join("users", "paciente.id", "users.id")
                    ->whereNotNull("users.email")
                    ->whereMonth("paciente.fecha_nacimiento", date("m"))->whereDay("paciente.fecha_nacimiento", date("d"))
                    ->get([
                        "users.email",
                        "users.nombre1",
                        "users.apellido1",
                        "paciente.fecha_nacimiento",
                        "users.id"
                    ]);

                foreach ($dayOfBirthay as $value) {
                    $data["datos"] = $value;
                    $data["msj"] = "Fecicidades vas a recibir un bono de 10000000";
                    if (filter_var($value->email, FILTER_VALIDATE_EMAIL)) {
                        $variable = explode("@", $value->email);
                        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
                        if (!is_numeric($variable[0])) {
                            if (preg_match($regex, $value->email)) {
                                $arrayEmails = [
                                    "email_estado_correo" => $value->email,
                                    'mensaje_estado_correo' => $data['msj'],
                                    "estado_correo" => 1,
                                    "tipo_estado_correo" => "cumpleanos_paciente",
                                    "created_ad_estado_correo" => date("Y-m-d H:i:s"),
                                    "procesado_estado_correo" => 0,
                                    "id_paciente_estado_correo" => $value->id,
                                    "id_usuariocrea" => $idusuario,
                                    "ip_creacion" => $ip_cliente,
                                    "ip_modificacion" => $ip_cliente,
                                ];
                                DB::table("new_db_estado_correos")->insert($arrayEmails);
                                exit;
                            }
                        }
                    }
                }
            }
        } elseif ($request->opcion == 'vistaImportar') {
            return view("masivo.index");
        } elseif ($request->opcion = 'subirArchivo') {

            // $path = $request->file($request->archivoExcel)->getRealPath();
            DB::beginTransaction();
            $path = $_FILES['archivoExcel']['tmp_name'];
            try {
                Excel::filter('chunk')->load($path)->chunk(100, function ($filas) {
                    foreach ($filas as $value) {
                        $arrayPlan = [
                            "nombre" => $value->nombre,
                            "descripcion" => $value->descripcion == null ? '' : $value->descripcion,
                            "estado" => 1,
                            "id_padre" => $value->id_padre,
                            "id_usuariocrea" => Auth::user()->id,
                            "id_usuariomod" => Auth::user()->id,
                            "ip_creacion" => "CARGA_MASIVA",
                            "ip_modificacion" => "CARGA_MASIVA",
                            "created_at" => date('Y-m-d H:i:s'),
                            "updated_at" => date('Y-m-d H:i:s'),
                            "naturaleza" => $value->naturaleza,
                            "naturaleza_2" => $value->naturaleza_2,
                            "id_empresa" => '0992704152001',
                            "id_plan" => $value->id_plan,
                            "plan" => $value->plan,
                            "estado_cierre_ano" => $value->estado_cierre_ano,
                        ];
                        Plan_Cuentas_Empresa::insert($arrayPlan);
                    }
                    DB::commit();
                });
            } catch (Exception $e) {
                DB::rollBack();
                echo $e->getMessage();
            }
        }
    }
    public function opcionesRetenciones(Request $r)
    {
        if ($r->opcion == 'generarsecuenciaretencion') {
            $retenciones = Ct_Retenciones::where('estado', '<>', 0)
                ->whereNull('secuencia')
                ->get([
                    'id',
                    'nro_comprobante'
                ]);
            try {
                if (count($retenciones) > 0) {
                    $cont = 0;
                    foreach ($retenciones as $row) {
                        $cont++;
                        $nro_comprobante = explode('-', $row->nro_comprobante);
                        if (strlen($nro_comprobante[2]) == 9) {
                            $arrayRetencion = [
                                'secuencia' => $nro_comprobante[2],
                                'ip_modificacion' => 'APIRETENCIONES-' . date('Ymd')
                            ];
                            Ct_Retenciones::where('id', $row->id)->update($arrayRetencion);
                            echo $cont . ' Retencion No.: ' . $nro_comprobante[2] . ' | Id: ' . $row->id . '<br/>';
                        }
                    }
                } else
                    echo 'No existen retenciones sin secuencia';
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage() . ' | Linea: ' . $e->getLine();
            }
        } else {
            echo 'aun no existe opcion para este nombre: ' . $r->opcion;
        }
    }
    public function opcionesFactura(Request $r)
    {
        if ($r->opcion == 'rep_conglomerada') {
            $sucursal = '';
            $punto_emision = '';
            $emisor = '';
            $establecimiento = '';
            $ptoEmision = '';
            $externo = 0;
            $documento = Labs_Factura_Agrupada_Cab::where('comprobante', $r->sucursal . '-' . $r->punto_emision . '-' . str_pad($r->secuencial, 9, 0, STR_PAD_LEFT))
                ->first();
            $cliente = Ct_Clientes::where('identificacion', $documento->cedula_factura)->first();
            $datosEnviadosPorApi = Log_Api::where('dato1', 'like', '%' . $r->empresa . '%')
                ->where('dato1', 'like', '%"establecimiento":"' . $r->sucursal . '","ptoEmision":"' . $r->punto_emision . '"%')
                ->where(function ($q) use ($r) {
                    $q->where('created_at', '>=', $r->desde);
                    $q->where('created_at', '<', $r->hasta);
                })
                ->where('dato1', 'like', '%"sku":"' . $r->sku . '%')
                ->first();
            $productos = [];
            $datosEnviados = json_decode($datosEnviadosPorApi->dato1);
            if (isset($datosEnviados->items) && count($datosEnviados->items) > 0) {
                foreach ($datosEnviados->items as $row) {
                    $producto['sku'] = $row->sku;
                    $producto['nombre'] = $row->name;
                    $producto['cantidad'] = $row->qty;
                    $producto['precio'] = $row->price;
                    $producto['descuento'] = $row->discount;
                    $producto['subtotal'] = $row->subtotal;
                    $producto['tax'] = $row->tax;
                    $producto['total'] = $row->total;
                    if ($row->name != '') {
                        array_push($productos, $producto);
                    }
                }
            }
            $formaPago = '';
            if (isset($datosEnviados->billingParameters->formaPago) && $datosEnviados->billingParameters->formaPago != '') {
                $formaPago = $datosEnviados->billingParameters->formaPago;
            }
            $diasPlazo = '';
            if (isset($datosEnviados->billingParameters->plazoDias) && $datosEnviados->billingParameters->plazoDias != '') {
                $diasPlazo = $datosEnviados->billingParameters->plazoDias;
            }
            $infoAdicionales = [];
            if (isset($datosEnviados->billingParameters->infoAdicional) && $datosEnviados->billingParameters->infoAdicional != '') {
                $cont = 0;
                foreach ($datosEnviados->billingParameters->infoAdicional as $row) {
                    $infoAdicional[$cont][$row->key] = $row->value;
                    $cont++;
                }
                array_push($infoAdicionales, $infoAdicional);
            }
            $establecimiento = Ct_Sucursales::find($documento->sucursal);
            $puntoEmision = Ct_Caja::find($documento->punto_emision);
            if ($puntoEmision->codigo_caja != '002') {
                $sucursal = $establecimiento->id;
                $punto_emision = $puntoEmision->id;
                $emisor = 'MD';
            }
            $establecimiento = $establecimiento->codigo_sucursal;
            $ptoEmision = $puntoEmision->codigo_caja;
            $externo = 1;
            $arrayData = [
                'empresa' => $r->empresa,
                'cliente' => [
                    'tipo' => $cliente->tipo,
                    'cedula' => $cliente->identificacion,
                    'nombre' => $cliente->nombre,
                    'apellido' => null,
                    'email' => $cliente->email_representante,
                    'telefono' => $cliente->telefono_representante,
                    'direccion' => [
                        'calle' => $cliente->direccion_representante,
                        'ciudad' => $cliente->ciudad_representante,
                    ]
                ],
                'productos' => $productos,
                'pago' => [
                    'forma_pago' => $formaPago,
                    'dias_plazo' => $diasPlazo,
                    'informacion_adicional' => $infoAdicionales,
                    'info_banco' => ''
                ],
                'seguro' => '',
                'sucursal' => $sucursal,
                'punto_emision' => $punto_emision,
                'emisor' => $emisor,
                'establecimiento' => $establecimiento,
                'ptoEmision' => $ptoEmision,
                'externo' => $externo,
                'fecha_emision' => $documento->fecha_envio,
            ];
            if ($r->verificardatos == 1) {
                dd($arrayData);
            } else {
                $response = json_encode([
                    'status' => [
                        'status' => 'success',
                        'message' => 'Factura con MD',
                        'reason' => '',
                        'date' => date('Y-m-d H:i:s'),
                    ],
                    'requestId' => 1,
                    'comprobante' => $r->sucursal . '-' . $r->punto_emision . '-' . str_pad($r->secuencial, 9, 0, STR_PAD_LEFT)
                ]);
                $respuesta_array = json_decode($response);
                EmisionDocumentosController::crea_factura($arrayData, $respuesta_array, $datosEnviadosPorApi->dato1);
            }
        } elseif ($r->opcion == 'construirConglomerada') {
            try {
                $ip_cliente = $_SERVER["REMOTE_ADDR"];
                $idusuario  = $r->factura_venta['id_usuariocrea'];
                if ($r->input_cabecera['id_empresa'] == '0993075000001')
                    $data['empresa'] = '0993075000001'; // humanlabs
                else
                    $data['empresa'] = $r->input_cabecera['id_empresa'];
                $dcliente = Ct_Clientes::find($r->factura_venta['id_cliente']);
                $cliente['cedula']   = $r->factura_venta['id_cliente'];
                $cliente['tipo']     = $dcliente->tipo; //eduardo dice q el lo calcula
                $cliente['nombre']   = $dcliente->nombre;
                $cliente['apellido'] = '';
                $explode = explode(" ", $dcliente->nombre);
                if (count($explode) >= 4) {
                    $cliente['nombre'] = $explode[0] . ' ' . $explode[1];
                    for ($i = 2; $i < count($explode); $i++) {
                        $cliente['apellido'] = $cliente['apellido'] . ' ' . $explode[$i];
                    }
                }
                if (count($explode) == 3) {
                    $cliente['nombre']   = $explode[0];
                    $cliente['apellido'] = $explode[1] . ' ' . $explode[2];
                }
                if (count($explode) == 2) {
                    $cliente['nombre']   = $explode[0];
                    $cliente['apellido'] = $explode[1];
                }
                $cliente['email']     = $r->factura_venta['email_cliente'];
                $cliente['telefono']  = $r->factura_venta['telefono_cliente'];
                $cliente['direccion']['calle'] = $r->factura_venta['direccion_cliente'];
                $cliente['direccion']['ciudad'] = '';
                $data['paciente'] = '9999999999';
                $data['cliente'] = $cliente;
                $msn_error  = '';
                $flag_error = false;
                if ($cliente['cedula'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en cedula';
                }
                if ($cliente['nombre'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en Nombre';
                }
                if ($cliente['email'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en email';
                }
                if ($cliente['telefono'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en telefono';
                }
                if ($cliente['direccion'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en direccion';
                }
                $cant = 0;
                for ($i = 0; $i < count($r['arr_total2']); $i++) {
                    $producto_precio   = $r['arr_total2'][$i]['precio'];
                    $producto_subtotal =  $r['arr_total2'][$i]['precio'] -  $r['arr_total2'][$i]['descuento'];
                    $producto['sku'] = "LABS-" .  $r['arr_total2'][$i]['id_orden'];
                    $producto['nombre'] = $r['arr_total2'][$i]['nombre'] === null ? $r['arr_total2'][$i]['codigo'] : $r['arr_total2'][$i]['nombre'];
                    $producto['cantidad']  = $r['arr_total2'][$i]['cantidad'];
                    $producto['precio']    = $producto_precio;
                    $producto['descuento'] = $r['arr_total2'][$i]['descuento'];
                    $producto['subtotal']  = $producto_subtotal;
                    $producto['tax']       = "0";
                    $producto['total']     = $producto_subtotal;
                    $producto['copago']    = "0";
                    $productos[$cant]      = $producto;
                    $cant++;
                    //Ct_Detalle_Venta_Conglomerada::create($detalles);
                }
                $data['productos'] = $productos;
                $info_adicional['nombre'] = "AGENTES_RETENCION";
                $info_adicional['valor']  = "Resolucion 1";
                $info[0]                  = $info_adicional;
                $info_adicional['nombre'] = "SEGURO";
                $info_adicional['valor']  = $dcliente->nombre; //SEGURO
                $info[6] = $info_adicional;
                //dd($cuenta_forma); 
                $cuenta_forma = count($r['forma_pago']);
                if ($cuenta_forma > 1) {
                    $pago['forma_pago'] = '20';
                    $info_adicional['nombre'] = "FORMA_PAGO";
                    $texto = '';
                    foreach ($r['forma_pago'] as $fp) {
                        $tipoPago = Ct_Tipo_Pago::find($fp['id_tipo']);
                        $total = $fp->valor;
                        $total = round($total, 2);
                        $texto = $texto . ' ' . $tipoPago->nombre . ': ' . $total;
                    }
                    $info_adicional['valor'] = $texto;
                    $info[7] = $info_adicional;
                } else {
                    $tipo = $r['forma_pago'][0]['id_tipo'];
                    if ($tipo == '1') {
                        $pago['forma_pago'] = '01';
                    } elseif ($tipo == '2') {
                        $pago['forma_pago'] = '20';
                    } elseif ($tipo == '3') {
                        $pago['forma_pago'] = '20';
                    } elseif ($tipo == '4') {
                        $pago['forma_pago'] = '19';
                    } elseif ($tipo == '5') {
                        $pago['forma_pago'] = '20';
                    } elseif ($tipo == '7') {
                        $pago['forma_pago'] = '01';
                    } else {
                        $pago['forma_pago'] = '16';
                    }
                }
                $pago['informacion_adicional'] = $info;
                $pago['dias_plazo']            = '10';
                $data['pago']                  = $pago;
                $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
                $data['laboratorio']           = 1;
                $data['concepto']              = 'Factura Electronica -' . $dcliente->nombre;
                $data['copago']                = 0;
                $data['id_seguro']             = $r['factura_venta']['seguro_paciente']; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
                $data['sucursal'] = $r['factura_venta']['sucursal'];
                $data['punto_emision'] = $r['factura_venta']['punto_emision'];
                $data['emisor'] = 'MD';
                $data['establecimiento'] = $r['factura_venta']['establecimiento'];
                $data['ptoEmision'] = $r['factura_venta']['emision'];
                $data['externo'] = 0;
                $data['formas_pago'] = $r['formas_pago'];
                $data['seguro'] = $r['seguro'];
                $data['idUsuario'] = $r['idUsuario'];
                $data['detalleConglomerada'] = $r['detalleConglomerada'];
                $data['conglomerada'] = 1;
                $data['nota_electronica'] = $r['factura_venta']['nota_electronica'];
                if ($flag_error == true) {
                    Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "LABORATORIO",
                        'dato_ant1'   => '',
                        'dato1'       => 'Error al validar factura conglomerada',
                        'dato_ant2'   => "ERROR AL ENVIAR AL SRI",
                        'dato_ant4'   => $msn_error,
                    ]);
                    return json_encode(['mensaje' => $msn_error]);
                }
                try {
                    if ($data['empresa'] == '0993075000001') {
                        $envio = ApiFacturacionController::envio_factura_externo($data);
                    } else {
                        $envio = ApiFacturacionController::envio_factura($data);
                    }
                    //$envio = json_decode($envio);
                    //$envio = ApiFacturacionController::envio_factura_externo($data);
                    $envio = json_decode($envio);
                    $manage = $envio->status->status . '-' . $envio->status->message . '-' . $envio->status->reason;
                    Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "LABORATORIO",
                        'dato_ant1'   => '',
                        'dato1'       => 'Conglomerada desde Guayaquil Produccion',
                        'dato_ant2'   => "ENVIAR AL SRI",
                        'dato_ant4'   => $manage,
                    ]);
                    return json_encode($envio);
                } catch (Exception $e) {
                    $arrayError = [
                        'Try' => 'Secundario',
                        'Controlador' => 'EmisionDocumentosController',
                        'error' => $e->getMessage(),
                        'linea' => $e->getLine()
                    ];
                    return $arrayError;
                }
            } catch (Exception $e) {
                $arrayError = [
                    'Try' => 'Principal',
                    'Controlador' => 'EmisionDocumentosController',
                    'error' => $e->getMessage(),
                    'linea' => $e->getLine()
                ];
                return $arrayError;
            }
        } elseif ($r->opcion == 'revisionSinEmitir') {
            $respuesta = [];
            $ventas = Ct_ventas::where('id_empresa', session('id_empresa'))
                ->where('estado', '!=', 0)
                ->where('tipo', 'like', '%VEN%')
                ->where('doc_electronico', '!=', 5)
                ->where('doc_electronico', '!=', -1)
                ->where('electronica', 1)
                ->where('estado_electronica', 0)
                ->whereYear('fecha', '>', 2022)
                ->get([
                    'id',
                    'fecha',
                    'nro_comprobante'
                ]);
            if (count($ventas) > 0) {
                $respuesta['ventas'] = $ventas;
            }
            $guias = Ct_Guia_Remision_Cabecera::where('id_empresa', session('id_empresa'))
                ->where('estado', '!=', 5)
                ->whereYear('fecha_emision_documento', '>', 2022)
                ->where('estado', '!=', -1)
                ->get([
                    'id',
                    'fecha_emision_documento',
                    'establecimiento',
                    'punto_emision',
                    'num_secuencial'
                ]);
            if (count($guias) > 0) {
                $respuesta['guias'] = $guias;
            }
            $liquidaciones = Ct_compras::where('id_empresa', session('id_empresa'))
                ->where('doc_electronico', '!=', 5)
                ->where('estado', '!=', 0)
                ->where('electronica', 1)
                ->whereYear('fecha', '>', 2022)
                ->get([
                    'id',
                    'fecha',
                    'nro_comprobante'
                ]);
            if (count($liquidaciones) > 0) {
                $respuesta['liquidaciones'] = $liquidaciones;
            }
            $retenciones = Ct_Retenciones::where('id_empresa', session('id_empresa'))
                ->where('doc_electronico', '!=', 5)
                ->where('doc_electronico', '!=', -1)
                ->where('electronica', 1)
                ->where('estado', '!=', 0)
                ->whereYear('fecha', '>', 2022)
                ->get([
                    'id',
                    'fecha',
                    'nro_comprobante'
                ]);
            if (count($retenciones) > 0) {
                $respuesta['retenciones'] = $retenciones;
            }
            $nc = Ct_ventas::where('id_empresa', session('id_empresa'))
                ->where('estado', '!=', 0)
                ->where('tipo', 'N-D')
                ->where('doc_electronico', '!=', 5)
                ->get([
                    'id',
                    'fecha',
                    'nro_comprobante'
                ]);
            if (count($nc) > 0) {
                $respuesta['nc'] = $nc;
            }
            return response()->json($respuesta);
        } elseif ($r->opcion == 'opcionesFactura') {

            dd($r->all());
            $nombreExcel = $r["nombreExcel"];
            Excel::filter('chunk')->load($nombreExcel)->chunk(600, function ($registros) {

                foreach ($registros as $book) {
                }
            });
        }
    }
    function convert_object_to_array($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (is_array($data)) {
            return array_map(__FUNCTION__, $data);
        } else {
            return $data;
        }
    }
    public function generarClaveAcceso($r)
    {
        $claveObj = new ClaveAcceso();
        $campos = [
            'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($r->fechaEmision))),
            'tipo_comprobante' => $r->codDoc,
            'ruc' => $r->ruc,
            'tipo_ambiente' => $r->ambiente,
            'punto_establecimiento' => $r->estab,
            'punto_emision' => $r->ptoEmi,
            'numero_comprobante' => $r->secuencial,
            'codigo_numerico' => $r->secuencial,
            'tipo_emision' => $r->tipoEmision
        ];
        $claveObj->llenarDatos($campos);
        echo '<pre>';
        print_r($claveObj);
        exit;
    }
    public function pedirArchivoFuera(string $empresa, string $comprobante, string $tipo)
    {
        $partes = explode('-', trim($comprobante));
        if (!ctype_digit($empresa) || count($partes) !== 3 || !ctype_digit($partes[0]) || !ctype_digit($partes[1]) || !ctype_digit($partes[2])) {
            abort(404, 'El comprobante solicitado no tiene un formato valido.');
        }
        $numComprobante = str_pad((int) $partes[0], 3, '0', STR_PAD_LEFT)
            . '-' . str_pad((int) $partes[1], 3, '0', STR_PAD_LEFT)
            . '-' . str_pad((int) $partes[2], 9, '0', STR_PAD_LEFT);
        $venta = Ct_ventas::where('id_empresa', $empresa)
            ->where('nro_comprobante', $numComprobante)
            ->where('estado', '<>', 0)
            ->first();
        if (is_null($venta) || empty($venta->nro_autorizacion)) {
            abort(404, 'No se encontro una venta activa con autorizacion para el comprobante solicitado.');
        }
        return $this->generarPdf($venta->nro_autorizacion, 'descargar');
    }
    public function leerXml($clave = '', $ambiente = 1)
    {
        if ($ambiente == 1) {
            $servicio = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl'; //prueba
        } else {
            $servicio = "https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl"; //produccion
        }
        $parametros = array();
        $parametros['claveAccesoComprobante'] = $clave;
        $client = new SoapClient($servicio, $parametros);
        $result = $client->autorizacionComprobante($parametros);
        $dato = '';
        $estado = $this->generarXmlAutorizacion($result);
        $messaje = 'Sin Procesar';
        if ($estado == 'AUTORIZADO') {
            $messaje = '';
            $messaje .= '<br/>Se extrajo el comprobante del SRI';
            $messaje .= '<br/>Se creo el archivo xml en el servidor';
            $docCod = substr($clave, 8, 2);
            $esta = substr($clave, 24, 3);
            $emis = substr($clave, 27, 3);
            $num = substr($clave, 30, 9);
            $ruc = substr($clave, 10, 13);
            if ($docCod == '07') { //Retenciones
                $arrayRetencion = [
                    'nro_comprobante' => $esta . '-' . $emis . '-' . $num,
                    'autorizacion' => $clave,
                    'doc_electronico' => 5
                ];
                Ct_Retenciones::where('nro_autorizacion', $clave)
                    //->where('nro_secuencial', $num)
                    ->where('estado', '<>', 0)
                    ->update($arrayRetencion);
                $messaje .= '<br/>Se actualizo la retencion ' . $esta . '-' . $emis . '-' . $num;
            } elseif ($docCod == '06') { //Guias
                $arrayGuia = [
                    'num_secuencial' => (int)$num,
                    'estado' => 5
                ];
                Ct_Guia_Remision_Cabecera::where('clave_acceso', $clave)
                    ->update($arrayGuia);
                $messaje .= '<br/>Se actualizo la Guia de remision ' . $esta . '-' . $emis . '-' . $num;
            } elseif ($docCod == '03') { //Liquidaciones
                $arrayGuia = [
                    'serie' => $esta . '-' . $emis,
                    'autorizacion' => $clave,
                    'doc_electronico' => 5,
                    'numero' => $esta . '-' . $emis . '-' . $num,
                    'secuencia_f' => $num
                ];
                Ct_compras::where('nro_autorizacion', $clave)
                    ->update($arrayGuia);
                $messaje .= '<br/>Se actualizo la Liquidacion ' . $esta . '-' . $emis . '-' . $num;
            } elseif ($docCod == '01') { //Facturas
                $arrayGuia = [
                    'doc_electronico' => 5,
                    'estado_electronica' => 3,
                    'nro_autorizacion' => $clave,
                    'numero' =>  $num,
                    'nro_comprobante' => $esta . '-' . $emis . '-' . $num,
                ];
                Ct_ventas::where('nro_comprobante', $esta . '-' . $emis . '-' . $num)
                    ->where('id_empresa', $ruc)
                    ->update($arrayGuia);
                $messaje .= '<br/>Se actualizo la Factura ' . $esta . '-' . $emis . '-' . $num;
            } elseif ($docCod == '04') {
                $arrayNC = [
                    'doc_electronico' => 5,
                    'estado_electronica' => 3,
                ];
                Ct_Nota_Credito_Clientes::where('id_empresa', $ruc)
                    ->where('nro_comprobante', $esta . '-' . $emis . '-' . $num)
                    ->where('nro_autorizacion', $clave)
                    ->where('electronica', 1)
                    ->update($arrayNC);
                $messaje .= '<br/>Se actualizo la Nota de credito ' . $esta . '-' . $emis . '-' . $num;
            }
            $dato = json_encode([
                'message' => $messaje,
                'estado' => $estado
            ]);
        }
        return ['respuesta' => $dato];
    }
    public function errorGeneral(Request $req)
    {
        return De_Log_Error::where('id_documento', $req->id)->orderBy('id', 'desc')->first();
    }
    public function traererrorTributario(Request $r)
    {
        $archivoXml = base_path() . '/storage/app/facturaelectronica/respuestaSri/recepcion/' . $r->clave . '.xml';
        if (file_exists($archivoXml)) {
            $xml = simplexml_load_file($archivoXml);

            echo json_encode([
                'xml' => $xml
            ]);
        } else {
            echo json_encode([
                'Error' => 'Archivo no enocntrado'
            ]);
        }
    }
    public function sanear_string($string)
    {
        $string = trim($string);
        $string = str_replace(array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string);
        $string = str_replace(array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string);
        $string = str_replace(array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string);
        $string = str_replace(array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string);
        $string = str_replace(array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string);
        $string = str_replace(array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string);
        $string = str_replace(array("\\", "¨", "º", "~", "#", "@", "|", "!", "\"", "·", "$", "%", "&", "/", "(", ")", "?", "'", "¡", "¿", "[", "^", "`", "]", "+", "}", "{", "¨", "´", ">", "< ", ";", ",", ":"), '', $string);
        return $string;
    }
    public function getGuiaRemision($dinfo = null, $idUsuario = 'FACELECTRO', $verificar = [])
    {
        $esVerificacion = count($verificar) > 0 && isset($verificar['id']);
        $idGuia = $esVerificacion ? $verificar['id'] : '';
        $data = [];
        $id_doc = 0;
        try {
            if (is_null($dinfo)) {
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                $guiaCabecera = Ct_Guia_Remision_Cabecera::getGuiaCabecera($idGuia);
                if (count($guiaCabecera) > 0) {
                    if (!$esVerificacion && !$this->consultarEstadoSRI($guiaCabecera[0]->id_empresa)) {
                        $arrayLog = [
                            'id_de_documentos_electronicos' => $id_doc,
                            'descripcion_error' => json_encode(['Error:' => 'SRI sin servicio']),
                            'id_usuariomod' => $idUsuario,
                            'id_usuariocrea' =>  $idUsuario,
                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                            'id_documento' => $id_doc
                        ];
                        De_Log_Error::setErrorLog($arrayLog);
                        $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $arrayLog], 'message' => 'Error');
                        echo json_encode($result);
                        return;
                    } else {
                        $cont = 0;
                        foreach ($guiaCabecera as $row) {
                            $cont++;
                            $empresa = Empresa::getEmpresa($row->id_empresa);
                            $emailTransportista = '';
                            if ($empresa != '') {
                                $datosFirma = De_Empresa::getDatos($row->id_empresa);
                                $firma['ruta_firma'] = $datosFirma->ruta_firma;
                                $firma['clave_firma'] = $datosFirma->clave_firma;
                                $data['datosfirma'] = $firma;
                                $infoTributaria['fecha_emision'] = date('d/m/Y', strtotime($row->fecha_emision_documento));
                                $infoTributaria['ambiente'] = $datosFirma->ambiente;
                                $infoTributaria['tipoEmision'] = 1;
                                $infoTributaria['razonSocial'] = $empresa->razonsocial;
                                $infoTributaria['nombreComercial'] = $empresa->nombrecomercial;
                                $infoTributaria['ruc'] = $empresa->id;
                                $infoTributaria['codDoc'] = '06';
                                $infoTributaria['estab'] = str_pad($row->establecimiento, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['ptoEmi'] = str_pad($row->punto_emision, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['dirMatriz'] = $empresa->direccion;
                                $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                                if ($esVerificacion) {
                                    $datosTributarios = De_Info_Tributaria::getDatos($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                                } elseif (strlen((string) $row->clave_acceso) === 49 && ctype_digit((string) $row->clave_acceso)) {
                                    $datosTributarios = De_Info_Tributaria::sincronizarSecuencialAsignado(
                                        $row->id_empresa,
                                        substr($row->clave_acceso, 24, 3),
                                        substr($row->clave_acceso, 27, 3),
                                        $idDoc,
                                        substr($row->clave_acceso, 30, 9)
                                    );
                                } else {
                                    $datosTributarios = De_Info_Tributaria::reservarSecuencial($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                                }
                                if ($datosTributarios == '') {
                                    $result = array('code' => 204, 'state' => true, 'data' => ['error:' => 'no se ha configurado los datos tributarios para esta empresa: ' . $empresa->id], 'message' => 'no existen datos tributarios para esta empresa');
                                    echo json_encode($result);
                                    return;
                                }
                                $infoTributaria['ambiente'] = $datosFirma->ambiente;
                                $secuencial = $esVerificacion
                                    ? ((int) $datosTributarios->secuencial_nro + 1)
                                    : $datosTributarios->secuencial_nro;
                                if (strlen((string) $row->clave_acceso) === 49 && ctype_digit((string) $row->clave_acceso)) {
                                    $infoTributaria['estab'] = substr($row->clave_acceso, 24, 3);
                                    $infoTributaria['ptoEmi'] = substr($row->clave_acceso, 27, 3);
                                    $secuencial = substr($row->clave_acceso, 30, 9);
                                }
                                $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                                $claveObj = new ClaveAcceso();
                                $campos = [
                                    'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($row->fecha_emision_documento))),
                                    'tipo_comprobante' => $infoTributaria['codDoc'],
                                    'ruc' => $infoTributaria['ruc'],
                                    'tipo_ambiente' => $infoTributaria['ambiente'],
                                    'punto_establecimiento' => $infoTributaria['estab'],
                                    'punto_emision' => $infoTributaria['ptoEmi'],
                                    'numero_comprobante' => $infoTributaria['secuencial'],
                                    'codigo_numerico' => $infoTributaria['secuencial'],
                                    'tipo_emision' => $infoTributaria['tipoEmision']
                                ];
                                $claveObj->llenarDatos($campos);
                                $infoTributaria['claveAcceso'] = strlen((string) $row->clave_acceso) === 49
                                    ? $row->clave_acceso
                                    : $claveObj->clave_acceso;
                                if ($datosFirma->agente_retencion == 1) {
                                    $infoTributaria['agenteRetencion'] = 1;
                                }
                                $data['infoTributaria'] = $infoTributaria;
                                $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                                    ->where('codigo_sucursal', $infoTributaria['estab'])
                                    ->first();
                                $caja = Ct_Caja::where('id_sucursal', $sucursal->id)
                                    ->where('codigo_caja', $infoTributaria['ptoEmi'])
                                    ->first();
                                if (isset($caja->sucursal->direccion_sucursal)) {
                                    $infoGuiaRemision['dirEstablecimiento'] = $caja->sucursal->direccion_sucursal;
                                } else {
                                    $infoGuiaRemision['dirEstablecimiento'] = $empresa->direccion;
                                }
                                $telefonoTransportista = '';
                                $transportista = Ct_transportista::getTransportista($row->ci_ruc_trasnportista);
                                if ($transportista != '') {
                                    $emailTransportista = $transportista->email;
                                    $telefonoTransportista = $transportista->telefono1;
                                    $infoGuiaRemision['dirPartida'] = $row->direccion_partida;
                                    $infoGuiaRemision['razonSocialTransportista'] = $transportista->razon_social;
                                    $infoGuiaRemision['tipoIdentificacionTransportista'] = str_pad($transportista->tipo_documento, 2, 0, STR_PAD_LEFT);
                                    $infoGuiaRemision['rucTransportista'] = $transportista->ci_ruc;
                                    if ($transportista->rise == 1)
                                        $infoGuiaRemision['rise'] = 'Contribuyente Regimen Simplificado RISE';
                                    $infoGuiaRemision['obligadoContabilidad'] = 'NO'; //Opcional
                                    if ($datosFirma->contabilidad == 1)
                                        $infoGuiaRemision['obligadoContabilidad'] = 'SI'; //Opcional
                                    if ($datosFirma->contribuyente_especial != 'NO')
                                        $infoGuiaRemision['contribuyenteEspecial'] = $datosFirma->contribuyente_especial; //Opcional
                                    $infoGuiaRemision['fechaIniTransporte'] =  str_replace('/', '-', date('d/m/Y', strtotime($row->fecha_ini)));
                                    $infoGuiaRemision['fechaFinTransporte'] =  str_replace('/', '-', date('d/m/Y', strtotime($row->fecha_fin)));
                                    $infoGuiaRemision['placa'] = $row->placa;
                                } else {
                                    $infoGuiaRemision['razonSocialTransportista'] = null;
                                    $infoGuiaRemision['tipoIdentificacionTransportista'] = null;
                                    $infoGuiaRemision['rucTransportista'] = null;
                                    $infoGuiaRemision['rise'] = null;
                                    $infoGuiaRemision['obligadoContabilidad'] = null; //Opcional
                                    $infoGuiaRemision['contribuyenteEspecial'] = null; //Opcional
                                }
                                $data['infoGuiaRemision'] = $infoGuiaRemision;
                                $destinatario['identificacionDestinatario'] = $row->ci_destinatario;
                                $destinatario['razonSocialDestinatario'] = str_replace('&', 'Y', $row->razon_social_destinatario);
                                $destinatario['dirDestinatario'] = $row->direccion_destinatario;
                                $destinatario['motivoTraslado'] = $row->motivo_traslado_destinatario;
                                $destinatario['docAduaneroUnico'] = null;
                                $destinatario['codEstabDestino'] = $row->codigo_est_destino != '' ? $row->codigo_est_destino : null;
                                $destinatario['ruta'] = $row->ruta;
                                if (isset($row->tipo_documento_destinatario) && $row->tipo_documento_destinatario != '') {
                                    $destinatario['codDocSustento'] = str_pad($row->tipo_documento_destinatario, 2, 0, STR_PAD_LEFT);
                                    $destinatario['numDocSustento'] = $row->num_doc_destino;
                                    $destinatario['numAutDocSustento'] = $row->num_autorizacion_sustento;
                                    $destinatario['fechaEmisionDocSustento'] = str_replace('-', '/', date('d/m/Y', strtotime($row->fecha_autorizacion_destinatario)));
                                }
                                //detalles
                                $detalle = [];
                                $detalles = [];
                                $conDetalle = 0;
                                $detalleGuia = Ct_Guia_Remision_Detalle::getDetalles($row->id);
                                if (count($detalleGuia) > 0) {
                                    foreach ($detalleGuia as $detaGuia) {
                                        $detalle['codigoInterno'] = $detaGuia->cod_principal;
                                        $detalle['codigoAdicional'] = $detaGuia->cod_adicional;
                                        $detalle['descripcion'] = str_replace('&', 'Y', Ct_productos::getNombreProducto($detaGuia->cod_principal, $detaGuia->tipo_objeto));
                                        $detalle['cantidad'] = $detaGuia->cantidad;
                                        $contAdicional = 0;
                                        if ($detaGuia->observacion != '') {
                                            $detAdicional = [
                                                'nombre' => 'detalle1',
                                                'valor' => $detaGuia->observacion
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($detaGuia->descripcion != '') {
                                            $detAdicional_ = [
                                                'nombre' => 'detalle2',
                                                'valor' => $detaGuia->descripcion
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($detaGuia->detalle3 != '') {
                                            $detAdicional_1 = [
                                                'nombre' => 'detalle3',
                                                'valor' => $detaGuia->detalle3
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($contAdicional > 0) {
                                            $detallesAdicionales = [isset($detAdicional_) ? $detAdicional_ : [], isset($detAdicional) ? $detAdicional : [], isset($detAdicional_1) ? $detAdicional_1 : []];
                                            $detalle['detallesAdicionales'] = $detallesAdicionales;
                                        }
                                        $detalles[$conDetalle] = $detalle;
                                        $conDetalle++;
                                    }
                                }
                                $destinatario['detalles'] = $detalles;
                                $campoAdicional['nombre'] = "email";
                                $campoAdicional['valor']  = $emailTransportista;
                                $informacion_adicional[0] = $campoAdicional;
                                $campoAdicional['nombre'] = "telefono";
                                $campoAdicional['valor'] = $telefonoTransportista != '-' ? $telefonoTransportista : '9999999999';
                                $informacion_adicional[1] = $campoAdicional;
                                $infoAdicional['campoAdicional'] = $informacion_adicional;
                                $destinatarios['destinatario'] = $destinatario;
                                $data['destinatarios'] = $destinatarios;
                                $data['infoAdicional'] = $informacion_adicional;
                            } else {
                                $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existe la empresa');
                                echo json_encode($result);
                                return;
                            }
                            $errores = $this->validarData($data);
                            if ($esVerificacion) {
                                return ['data' => $data, 'errors' => $errores];
                            }
                            /*if ($empresa->id == '0993069299001') {
                                echo '<pre>';
                                print_r($data);
                                print_r($errores);
                                exit;
                            }*/
                            if (count($errores) > 0) {
                                $arrayDocElec = [
                                    'id_de_pasos' => 7,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoGuiaRemision' => json_encode($data['infoGuiaRemision']),
                                    'destinatarios' => json_encode($data['destinatarios']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' => $destinatario['identificacionDestinatario'],
                                    'ruc_emisor' => $infoTributaria['ruc'],
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                    'id_documento' => $row->id,
                                ];
                                $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayDocElec, 7);
                                Ct_Guia_Remision_Cabecera::updateSinGenerarXML($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id, $data['infoTributaria']['claveAcceso']);
                                $idInsertados .= $id_doc . ',';
                                $arrayLog = [
                                    'id_de_documentos_electronicos' => $id_doc,
                                    'descripcion_error' => json_encode($errores),
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'id_documento' => $row->id
                                ];
                                De_Log_Error::setErrorLog($arrayLog);
                            } else {
                                //Creacion XML
                                $arrayLogError = [
                                    'id_de_pasos' => 1,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoGuiaRemision' => json_encode($data['infoGuiaRemision']),
                                    'destinatarios' => json_encode($data['destinatarios']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' => $destinatario['identificacionDestinatario'],
                                    'ruc_emisor' => $infoTributaria['ruc'],
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                    'id_documento' => $row->id
                                ];
                                $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayLogError, 1);
                                if (is_numeric($id_doc)) {
                                    $idInsertados .= $id_doc . ',';
                                    $comprobante = $this->generarDocElectronicoXml($data);
                                    Ct_Guia_Remision_Cabecera::updateGenerarXML($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id, $data['infoTributaria']['claveAcceso']);
                                    /*header('Content-type: text/xml');
                                    echo $comprobante;
                                    exit;*/
                                    if ($comprobante != '') {
                                        $docAnte = simplexml_load_string($comprobante);
                                        if (!empty($docAnte)) {
                                            //validar xsd
                                            $validacion = $this->validarXmlToXsd($comprobante);
                                            if ($validacion['isValidoXsd'] != 1) {
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($validacion),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                    'id_documento' => $row->id
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $result = array('code' => 204, 'state' => true, 'data' => json_encode($validacion), 'message' => 'ok|No existen documentos para emitir', 'tipoDoc' => 'Guia de remision');
                                                echo json_encode($result);
                                                return;
                                            }
                                        }
                                        De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                        Ct_Guia_Remision_Cabecera::updateValidacionXSD($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id);
                                        //firmar xml
                                        $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $datosFirma->ruta_firma; //firma.p12
                                        $password = $datosFirma->clave_firma; //'3duard0faustos';
                                        $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                                        if (!empty($result)) {
                                            $docAnte = simplexml_load_string($comprobante);
                                            $dom = new DOMDocument();
                                            $dom->loadXML($result);
                                            $this->crearcarpeta(base_path() . '/storage/app/facturaelectronica/firmados/' . $docAnte->infoTributaria->ruc . '/' . $this->tipoDocumento . '/');
                                            $dom->save(base_path() . '/storage/app/facturaelectronica/firmados/' . $docAnte->infoTributaria->ruc . '/' . $this->tipoDocumento . '/' . $this->tipoDocumento . '-' . $docAnte->infoTributaria->estab . '-' . $docAnte->infoTributaria->ptoEmi . '-' . $docAnte->infoTributaria->secuencial . '.xml');
                                            De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                            Ct_Guia_Remision_Cabecera::updateXmlFirmado($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id);
                                            //recepcion sri
                                            $respuesta = $this->recibirWs($result, $data['infoTributaria']['ambiente']);
                                            De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                            Ct_Guia_Remision_Cabecera::updateRecibidoSri($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id);
                                            //dd($respuesta['comprobantes']);
                                            if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                                //$this->procesarRespuestaXml($respuesta);
                                                $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                                $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                                De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                //autorzacion sri
                                                $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso, $data['infoTributaria']['ambiente']);
                                                $this->generarXmlAutorizacion($respuestaAutorizacion);
                                                De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                                Ct_Guia_Remision_Cabecera::updateXmlAutorizacion($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id, $docAnte->infoTributaria->claveAcceso, $secuancialDocumento);
                                                $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
                                                $param = [
                                                    'email' => $row->email_traslado_destinatario,
                                                    'nombre' => str_replace('&', 'Y', $row->razon_social_destinatario),
                                                    'claveAcceso' => $docAnte->infoTributaria->claveAcceso,
                                                    'estab' => $data['infoTributaria']['estab'],
                                                    'ptoEmi' =>  $data['infoTributaria']['ptoEmi'],
                                                    'secuencial' =>  $data['infoTributaria']['secuencial'],
                                                    'tipoDoc' => $data['infoTributaria']['codDoc'],
                                                    'fechaEmision' => $data['infoTributaria']['fecha_emision'],
                                                ];
                                                $this->enviar_correo($param);
                                            } else {
                                                $this->generarXmlRespuesta($respuesta, $data['infoTributaria']['claveAcceso']);
                                                De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                                Ct_Guia_Remision_Cabecera::updateXmlNoRecepcion($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id);
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($respuesta),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                    'id_documento' => $row->id
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                //dd($respuesta);
                                                //return $respuesta['mensajesWs'];
                                            }
                                        } else {
                                            //Log error al firmar XML
                                            $arrayLog = [
                                                'id_de_documentos_electronicos' => $id_doc,
                                                'descripcion_error' => json_encode($result),
                                                'id_usuariomod' => $idUsuario,
                                                'id_usuariocrea' =>  $idUsuario,
                                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                'id_documento' => $row->id
                                            ];
                                            De_Log_Error::setErrorLog($arrayLog);
                                        }
                                    }
                                } else {
                                    //log No genero XML
                                    $arrayLog = [
                                        'id_de_documentos_electronicos' => $id_doc,
                                        'descripcion_error' => 'Error al crear el documento XML',
                                        'id_usuariomod' => $idUsuario,
                                        'id_usuariocrea' =>  $idUsuario,
                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    ];
                                    De_Log_Error::setErrorLog($arrayLog);
                                }
                            }
                        }
                    }
                } else {
                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existen documentos para emitir', 'tipoDoc' => 'Guia de remision');
                    echo json_encode($result);
                    return;
                }
                $infoErrores = De_Documentos_Electronicos::revisionEstadosDocumentosErrores($idInsertados);
                $infoAutorizados = '';
                if ($claveInsertados != '') {
                    $infoAutorizados = De_Documentos_Electronicos::revisionEstadosDocumentosAutorizados($claveInsertados);
                }
                $result = array('code' => 200, 'state' => true, 'data' => ['errores:' => $infoErrores, 'autorizado:' => $infoAutorizados], 'message' => 'proceso ejecutado correctamente', 'tipoDoc' => 'Guia de remision');
                echo json_encode($result);
            } else {
                echo 'a terceros';
            }
        } catch (Exception $ex) {
            if ($id_doc != 0) {
                $arrayLog = [
                    'id_de_documentos_electronicos' => $id_doc,
                    'descripcion_error' => json_encode($ex->getMessage()),
                    'id_usuariomod' => $idUsuario,
                    'id_usuariocrea' =>  $idUsuario,
                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                ];
                De_Log_Error::setErrorLog($arrayLog);
            }
            $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $ex->getMessage(), 'linea' => $ex->getLine()], 'message' => 'Error');
            echo json_encode($result);
            return;
        }
    }
    public function getFactura($dinfo = null, $idUsuario = 'FACELECTRO', $verificar = [])
    {
        $idFactura = '';
        $esVerificacion = count($verificar) > 0 && isset($verificar['id']);
        if ($esVerificacion) {
            $idFactura = $verificar['id'];
        }
        $data = [];
        $id_doc = 0;
        $numDocumento = '';
        $respuestaNo = new stdClass();
        try {
            if ($dinfo == null) {
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                $facturaCabecera = Ct_ventas::getVentasCabecera($idFactura);
                if (count($facturaCabecera) > 0) {
                    if (!$esVerificacion && !$this->consultarEstadoSRI($facturaCabecera[0]->id_empresa)) {
                        $arrayLog = [
                            'id_de_documentos_electronicos' => $id_doc,
                            'descripcion_error' => json_encode(['Error:' => 'SRI sin servicio']),
                            'id_usuariomod' => $idUsuario,
                            'id_usuariocrea' =>  $idUsuario,
                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                            'id_documento' => $id_doc
                        ];
                        De_Log_Error::setErrorLog($arrayLog);
                        $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $arrayLog], 'message' => 'Error');
                        $arrayEstadoCron = ['valor' => 0];
                        De_Parametros::where('id', 1)->update($arrayEstadoCron);
                        echo json_encode($result);
                        return;
                    } else {
                        $cont = 0;
                        foreach ($facturaCabecera as $row) {
                            $cont++;
                            $empresa = Empresa::getEmpresa($row->id_empresa);
                            if ($empresa != '') {
                                $datosFirma = De_Empresa::getDatos($row->id_empresa);
                                $firma['ruta_firma'] = $datosFirma->ruta_firma;
                                $firma['clave_firma'] = $datosFirma->clave_firma;
                                $data['datosfirma'] = $firma;
                                $infoTributaria['fecha_emision'] = date('d/m/Y', strtotime($row->fecha));
                                $infoTributaria['ambiente'] = 1;
                                $infoTributaria['tipoEmision'] = 1;
                                $infoTributaria['razonSocial'] = str_replace('&', 'Y', str_replace('.', '', $empresa->razonsocial));
                                $infoTributaria['nombreComercial'] = str_replace('.', '', $empresa->nombrecomercial);
                                $infoTributaria['ruc'] = $empresa->id;
                                $infoTributaria['codDoc'] = '01';
                                $infoTributaria['estab'] = str_pad($row->sucursal, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['ptoEmi'] = str_pad($row->punto_emision, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['dirMatriz'] = $empresa->direccion;
                                if ($infoTributaria['codDoc'] == '01') {
                                    if (isset($datosFirma->agente_retencion) && $datosFirma->agente_retencion != 0)
                                        $infoTributaria['agenteRetencion'] = '1';
                                    if (isset($datosFirma->tipo_rimpe) && $datosFirma->tipo_rimpe == 1)
                                        $infoTributaria['rimpe_emprendedor'] = $datosFirma->tipo_rimpe;
                                    elseif (isset($datosFirma->tipo_rimpe) && $datosFirma->tipo_rimpe == 0)
                                        $infoTributaria['rimpe_popular'] = $datosFirma->tipo_rimpe;
                                }
                                $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                                if ($esVerificacion) {
                                    $datosTributarios = De_Info_Tributaria::getDatos($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                                } else {
                                    $datosTributarios = $row->numero == ''
                                        ? De_Info_Tributaria::reservarSecuencial($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc)
                                        : De_Info_Tributaria::sincronizarSecuencialAsignado($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc, $row->numero);
                                }
                                if ($datosTributarios == '') {
                                    $result = array('code' => 204, 'state' => true, 'data' => ['error:' => 'no se ha configurado los datos tributarios para esta empresa: ' . $empresa->id . ' Factura'], 'message' => 'no existen datos tributarios para esta empresa', 'tipoDoc' => 'Factura');
                                    echo json_encode($result);
                                    return;
                                }
                                if ($row->numero == '')
                                    $secuencial = $esVerificacion ? ((int) $datosTributarios->secuencial_nro + 1) : $datosTributarios->secuencial_nro;
                                else
                                    $secuencial = $row->numero;
                                $infoTributaria['ambiente'] = $datosFirma->ambiente;
                                $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                                $claveObj = new ClaveAcceso();
                                if ($row->nro_autorizacion == '') {
                                    $campos = [
                                        'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($row->fecha))),
                                        'tipo_comprobante' => $infoTributaria['codDoc'],
                                        'ruc' => $infoTributaria['ruc'],
                                        'tipo_ambiente' => $infoTributaria['ambiente'],
                                        'punto_establecimiento' => $infoTributaria['estab'],
                                        'punto_emision' => $infoTributaria['ptoEmi'],
                                        'numero_comprobante' => $infoTributaria['secuencial'],
                                        'codigo_numerico' => $infoTributaria['secuencial'],
                                        'tipo_emision' => $infoTributaria['tipoEmision']
                                    ];
                                    $claveObj->llenarDatos($campos);
                                } else {
                                    $xml = $row->nro_autorizacion;
                                    $esta = substr($xml, 24, 3);
                                    $emis = substr($xml, 27, 3);
                                    $num = substr($xml, 30, 9);
                                    $infoTributaria['estab'] = $esta;
                                    $infoTributaria['ptoEmi'] = $emis;
                                    $infoTributaria['secuencial'] = $num;
                                }
                                $infoTributaria['claveAcceso'] = $row->nro_autorizacion != '' ? $row->nro_autorizacion : $claveObj->clave_acceso;
                                $data['infoTributaria'] = $infoTributaria;
                                //Info Factura
                                $infoFactura['fechaEmision'] = $row->fecha;
                                $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                                    ->where('codigo_sucursal', $infoTributaria['estab'])
                                    ->first();
                                if (isset($sucursal->direccion_sucursal)) {
                                    $infoFactura['dirEstablecimiento'] = $sucursal->direccion_sucursal;
                                } else {
                                    $infoFactura['dirEstablecimiento'] = $empresa->direccion;
                                }
                                if ($datosFirma->contribuyente_especial != '' && $datosFirma->contribuyente_especial != 0) {
                                    $infoFactura['contribuyenteEspecial'] = $datosFirma->contribuyente_especial; //Opcional
                                }
                                $infoFactura['obligadoContabilidad'] = 'NO'; //Opcional
                                if ($datosFirma->contabilidad == 1)
                                    $infoFactura['obligadoContabilidad'] = 'SI'; //Opcional
                                $cliente = Ct_Clientes::getCliente($row->ruc_id_cliente);
                                if ($cliente != '') {
                                    $toClie = str_pad($cliente->tipo, 2, 0, STR_PAD_LEFT);
                                    if (strlen($cliente->identificacion) == 10) {
                                        if (!EmisionDocumentosController::validarCedula($cliente->identificacion)) {
                                            $toClie = '06';
                                        }
                                    } elseif (strlen($cliente->identificacion) == 13) {
                                        if ($cliente->identificacion == '9999999999999')
                                            $toClie = '07';
                                    }
                                    $infoFactura['tipoIdentificacionComprador'] = $toClie;
                                    $infoFactura['identificacionComprador'] = isset($row->ruc_id_cliente) ? $row->ruc_id_cliente : $cliente->identificacion;
                                    $infoFactura['razonSocialComprador'] = isset($row->nombre_cliente) ? $row->nombre_cliente : $cliente->nombre;
                                    $infoFactura['direccionComprador'] = isset($row->direccion_cliente) ? ($row->direccion_cliente == '' ? '.' : $row->direccion_cliente) : $cliente->direccion_representante;
                                } else {
                                    $infoFactura['tipoIdentificacionComprador'] = null;
                                    $infoFactura['identificacionComprador'] = null;
                                    $infoFactura['razonSocialComprador'] = null;
                                    $infoFactura['direccionComprador'] = null; //Opcional
                                }
                                if ($row->guiaRemision != '')
                                    $infoFactura['guiaRemision'] = $row->guiaRemision;
                                /*if ($empresa->id == '1391927177001') { //Manta
                                    echo '<pre>';
                                    print_r($row);
                                    exit;
                                }*/
                                $infoFactura['totalSinImpuestos'] = $this->getDecimal(($row->subtotal_0 + $row->subtotal_12) - $row->descuento);
                                //$infoFactura['totalSinImpuestos'] = ($row->subtotal_0 + $row->subtotal_12);
                                //$infoFactura['totalConImpuestos'] = $row->total_final;
                                $infoFactura['totalDescuento'] = $this->getDecimal($row->descuento, 2);
                                $totalConImpuestos = [];
                                $totalConImpuesto = [];
                                $conImpuesto = 0;
                                $valortotal = 0;
                                $descut = 0;
                                $baseI = 0;
                                $ivaT = 0;
                                $impuestos = Ct_detalle_venta::getDetalles($row->id);
                                if (count($impuestos) > 0) {
                                    $descut = 0;
                                    $baseI = 0;
                                    $ivaT = 0;
                                    foreach ($impuestos as $conimpuesto) {
                                        if ($conimpuesto->copago > 0) {
                                            $descuento_ = $conimpuesto->descuento == '' ? 0 : $conimpuesto->descuento;
                                            if ($conimpuesto->check_iva == 1) {
                                                $codigoImpuesto = De_Codigo_Impuestos::where('porcentaje', ($this->porIva->iva * 100))->where('codigo_impuesto', 2)->first();
                                                $totalConImpuesto['codigo'] = $codigoImpuesto->codigo_impuesto;
                                                $totalConImpuesto['codigoPorcentaje'] = $codigoImpuesto->codigo;
                                                $descut += $descuento_;
                                                $totalConImpuesto['descuentoAdicional'] = $this->getDecimal(0, 2);
                                                $baseI += $conimpuesto->extendido;
                                                $totalConImpuesto['baseImponible'] = $this->getDecimal($baseI, 2);
                                                $ivaT += (($conimpuesto->cantidad * $conimpuesto->precio) - ($conimpuesto->descuento + $conimpuesto->copago)) * $conimpuesto->porcentaje;
                                                $totalConImpuesto['valor'] = $this->getDecimal($ivaT);
                                            }
                                        } else {
                                            $descuento_ = $conimpuesto->descuento == '' ? 0 : $conimpuesto->descuento;
                                            if ($conimpuesto->check_iva == 1) {
                                                $codigoImpuesto = De_Codigo_Impuestos::where('porcentaje', ($this->porIva->iva * 100))->where('codigo_impuesto', 2)->first();
                                                $totalConImpuesto['codigo'] = $codigoImpuesto->codigo_impuesto;
                                                $totalConImpuesto['codigoPorcentaje'] = $codigoImpuesto->codigo;
                                                $descut += $descuento_;
                                                $totalConImpuesto['descuentoAdicional'] = $this->getDecimal(0, 2);
                                                $baseI += (($conimpuesto->cantidad * $conimpuesto->precio) - $conimpuesto->descuento) - $conimpuesto->copago;
                                                $totalConImpuesto['baseImponible'] = $this->getDecimal($baseI, 2);
                                                $ivaT += (($conimpuesto->cantidad * $conimpuesto->precio) - ($conimpuesto->descuento + $conimpuesto->copago)) * $conimpuesto->porcentaje;
                                                $totalConImpuesto['valor'] = $this->getDecimal($ivaT);
                                            }
                                        }
                                    }
                                    if (count($totalConImpuesto) > 0) {
                                        array_push($totalConImpuestos, $totalConImpuesto);
                                    }
                                    $totalConImpuesto = [];
                                    /*  if (count($totalConImpuesto) > 0 && $conimpuesto->check_iva == 1)
                                        //array_push($totalConImpuestos, $totalConImpuesto);
                                        $totalConImpuestos[0] = $totalConImpuesto;*/
                                }
                                if (count($impuestos) > 0) {
                                    $descut = 0;
                                    $baseI = 0;
                                    $ivaT = 0;
                                    foreach ($impuestos as $sinimpuesto) {
                                        if ($sinimpuesto->copago > 0) {
                                            $descuento_ = $sinimpuesto->descuento == '' ? 0 : $sinimpuesto->descuento;
                                            if ($sinimpuesto->check_iva != 1) {
                                                $codigoImpuesto = De_Codigo_Impuestos::where('id', 2)->first();
                                                $totalConImpuesto['codigo'] = $codigoImpuesto->codigo_impuesto;
                                                $totalConImpuesto['codigoPorcentaje'] = $codigoImpuesto->codigo;
                                                $descut += $descuento_;
                                                $totalConImpuesto['descuentoAdicional'] = $this->getDecimal(0, 2);
                                                $baseI += ($sinimpuesto->extendido);
                                                $totalConImpuesto['baseImponible'] = $this->getDecimal($baseI, 2);
                                                $totalConImpuesto['valor'] = $this->getDecimal(0, 2);
                                            }
                                        } else {
                                            $descuento_ = $sinimpuesto->descuento == '' ? 0 : $sinimpuesto->descuento;
                                            if ($sinimpuesto->check_iva != 1) {
                                                $codigoImpuesto = De_Codigo_Impuestos::where('id', 2)->first();
                                                $totalConImpuesto['codigo'] = $codigoImpuesto->codigo_impuesto;
                                                $totalConImpuesto['codigoPorcentaje'] = $codigoImpuesto->codigo;
                                                $descut += $descuento_;
                                                $totalConImpuesto['descuentoAdicional'] = $this->getDecimal(0, 2);
                                                //$baseI += ($sinimpuesto->extendido);
                                                $baseI += (($sinimpuesto->cantidad * $sinimpuesto->precio) - $sinimpuesto->descuento) - $sinimpuesto->copago;
                                                $totalConImpuesto['baseImponible'] = $this->getDecimal($baseI, 2);
                                                $totalConImpuesto['valor'] = $this->getDecimal(0, 2);
                                            }
                                        }
                                    }
                                    /*if (count($totalConImpuesto) > 0 && $sinimpuesto->check_iva != 1)
                                        //array_push($totalConImpuestos, $totalConImpuesto);
                                        $totalConImpuestos[1] = $totalConImpuesto;*/
                                    if (count($totalConImpuesto) > 0) {
                                        array_push($totalConImpuestos, $totalConImpuesto);
                                    }
                                }
                                $infoFactura['totalConImpuestos'] = $totalConImpuestos;
                                $infoFactura['propina'] = $this->getDecimal(0, 2);
                                $infoFactura['importeTotal'] = $this->getDecimal($row->total_final, 2);
                                $infoFactura['moneda'] = Ct_Divisas::getMoneda();
                                $detallePagos = Ct_detalle_venta::getDetalles($row->id);
                                //Formas de pago
                                $pagos = [];
                                $pago = [];
                                $conPagos = 0;
                                $formasPagos = Ct_Forma_Pago::join('ct_tipo_pago as t', 'ct_forma_pago.tipo', '=', 't.id')
                                    ->where('id_ct_ventas', $row->id)->get([
                                        't.codigo_sri',
                                        'ct_forma_pago.valor'
                                    ]);
                                if (count($formasPagos) > 0) {
                                    foreach ($formasPagos as $formaPago) {
                                        $codPago = $formaPago->codigo_sri;
                                        $pago['formaPago'] = str_pad($codPago, 2, 0, STR_PAD_LEFT);
                                        $pago['total'] = $this->getDecimal($formaPago->valor, 2);
                                        $pago['plazo'] = $row->dias_plazo;
                                        $pago['unidadTiempo'] = 'dias';
                                        array_push($pagos, $pago);
                                    }
                                }
                                $infoFactura['pagos'] = $pagos;
                                $data['infoFactura'] = $infoFactura;
                                //detalles
                                $detalle = [];
                                $detalles = [];
                                $conDetalle = 0;
                                $detalleFactura = Ct_detalle_venta::getDetalles($row->id);
                                //FACTURA DETALLE
                                if (count($detalleFactura) > 0) {
                                    foreach ($detalleFactura as $detaFactura) {
                                        $descuenotdetalle_ = $detaFactura->descuento == '' ? 0 : $detaFactura->descuento;
                                        $descuenotdetalle_ = str_replace(',', '', number_format($descuenotdetalle_, 2));
                                        $detalle['p_impuesto'] = $detaFactura->check_iva != null ? ($this->porIva->iva * 100) : 0;
                                        $detalle['codigoPrincipal'] = substr($detaFactura->id_ct_productos, 0, 25);
                                        $otros = json_decode($detaFactura->otros);
                                        if (count($otros) > 0) {
                                            try {
                                                if ($otros->descripcion_publico != '') {
                                                    $detalle['codigoPrincipal'] = substr($otros->codigo_publico, 0, 25);
                                                    $detalle['codigoAuxiliar'] = substr($otros->codigo_publico, 0, 25);
                                                    $detalle['descripcion'] = $otros->descripcion_publico;
                                                } else {
                                                    $detalle['codigoPrincipal'] = substr($detaFactura->id_ct_productos, 0, 25);
                                                    $detalle['codigoAuxiliar'] = substr($detaFactura->id_ct_productos, 0, 25);
                                                    $detalle['descripcion'] = $detaFactura->descripcion_producto == '' ? $detaFactura->nombre : $detaFactura->descripcion_producto;
                                                }
                                            } catch (Exception $e) {
                                                $detalle['codigoPrincipal'] = substr($detaFactura->id_ct_productos, 0, 25);
                                                $detalle['codigoAuxiliar'] = substr($detaFactura->id_ct_productos, 0, 25);
                                                $detalle['descripcion'] = $detaFactura->descripcion_producto == '' ? $detaFactura->nombre : $detaFactura->descripcion_producto;
                                            }
                                        } else {
                                            $detalle['codigoPrincipal'] = substr($detaFactura->id_ct_productos, 0, 25);
                                            $detalle['codigoAuxiliar'] = substr($detaFactura->id_ct_productos, 0, 25);
                                            $detalle['descripcion'] = $detaFactura->descripcion_producto == '' ? $detaFactura->nombre : $detaFactura->descripcion_producto;
                                        }
                                        $detalle['cantidad'] = $detaFactura->cantidad;
                                        //$detalle['precioUnitario'] = $this->getDecimal($detaFactura->precio - $detaFactura->copago, 2);
                                        //$detalle['precioUnitario'] = $this->getDecimal((($detaFactura->precio * $detaFactura->cantidad) - $detaFactura->copago) / $detaFactura->cantidad, 4);
                                        if ($detaFactura->copago > 0) {
                                            $detalle['precioUnitario'] = $this->getDecimal((($detaFactura->extendido + $detaFactura->descuento) / $detaFactura->cantidad), 6);
                                        } else {
                                            $detalle['precioUnitario'] = $this->getDecimal(((($detaFactura->precio * $detaFactura->cantidad)) / $detaFactura->cantidad), 4);
                                        }
                                        $detalle['descuento'] = $descuenotdetalle_;
                                        //$detalle['precioTotalSinImpuesto'] = ($row->subtotal_0 + $row->subtotal_12) - $detaFactura->descuento;
                                        //echo '<pre>';print_r($detaFactura->precio-$detaFactura->descuento);exit;
                                        //$preciototalsinimpuesto = (($detaFactura->cantidad * $detaFactura->precio) -  $detaFactura->descuento) - $detaFactura->copago;
                                        if ($detaFactura->copago > 0) {
                                            $preciototalsinimpuesto = $detaFactura->extendido;
                                        } else {
                                            $preciototalsinimpuesto = (($detaFactura->cantidad * $detaFactura->precio) -  $detaFactura->descuento);
                                        }
                                        $detalle['precioTotalSinImpuesto'] = $this->getDecimal($preciototalsinimpuesto);
                                        $contAdicional = 0;
                                        if ($detaFactura->detalle != '') {
                                            $detAdicional = [
                                                'nombre' => 'detalle',
                                                'valor' => $detaFactura->detalle
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($detaFactura->adicional != '') {
                                            $detAdicional_ = [
                                                'nombre' => 'adicional',
                                                'valor' => $detaFactura->adicional
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($contAdicional > 0) {
                                            $detallesAdicionales = [isset($detAdicional) ? $detAdicional : [], isset($detAdicional_) ? $detAdicional_ : []];
                                            $detalle['detallesAdicionales'] = $detallesAdicionales;
                                        }
                                        $detalles[$conDetalle] = $detalle;
                                        $conDetalle++;
                                    }
                                }
                                $data['detalles'] = $detalles;
                                $infoAdicional = [];
                                $campoAdicional['nombre'] = "Dirección";
                                $campoAdicional['valor']  = $row->direccion_cliente == '' ? '.' : $row->direccion_cliente;
                                $informacion_adicional[0] = $campoAdicional;

                                $campoAdicional['nombre'] = "Teléfono";
                                $campoAdicional['valor']  = $row->telefono_cliente;
                                $informacion_adicional[1] = $campoAdicional;

                                $campoAdicional['nombre'] = "Correo";
                                $campoAdicional['valor']  = $row->email_cliente;
                                $informacion_adicional[2] = $campoAdicional;
                                if ($row->nota_electronica != '') {
                                    $campoAdicional['nombre'] = "Observaciones";
                                    $campoAdicional['valor']  = $row->nota_electronica != '' ? $row->nota_electronica : 'No requiere datos adicionales';
                                    $informacion_adicional[3] = $campoAdicional;
                                }
                                if ($row->nota_electronica != '') {
                                    $infoAdicional = [$informacion_adicional[0], $informacion_adicional[1], $informacion_adicional[2], $informacion_adicional[3]];
                                } else {
                                    $infoAdicional = [$informacion_adicional[0], $informacion_adicional[1], $informacion_adicional[2]];
                                }
                                if ($row->nota_array != '') {
                                    $cont = 0;
                                    $contAdicional = 2;
                                    if ($row->nota_electronica != '') {
                                        $contAdicional = 3;
                                    }
                                    try {
                                        foreach (json_decode($row->nota_array) as $nrow) {
                                            if ($nrow->nombre != 'MAIL' && $nrow->nombre != 'DIRECCION') {
                                                $contAdicional++;
                                                $campoAdicional['nombre'] = $nrow->nombre;
                                                $campoAdicional['valor']  = str_replace(' ', '', $nrow->valor) != '' ? $nrow->valor : '-';
                                                $informacion_adicional[$contAdicional] = $campoAdicional;
                                                if ($nrow->nombre != '')
                                                    array_push($infoAdicional, $informacion_adicional[$contAdicional]);
                                            }
                                        }
                                        /*foreach (json_decode($row->nota_array) as $nrow) {
                                                if ($cont > 0) {
                                                    $contAdicional++;
                                                    $campoAdicional['nombre'] = "Detalle " . $cont;
                                                    $campoAdicional['valor']  = $nrow;
                                                    $informacion_adicional[$contAdicional] = $campoAdicional;
                                                    array_push($infoAdicional, $informacion_adicional[$contAdicional]);
                                                }
                                                $cont++;
                                            }*/
                                    } catch (Exception $e) {
                                        echo $e->getMessage();
                                        foreach (json_decode($row->nota_array) as $nrow) {
                                            if ($nrow->nombre != 'MAIL' && $nrow->nombre != 'DIRECCION') {
                                                $contAdicional++;
                                                $campoAdicional['nombre'] = $nrow->nombre;
                                                $campoAdicional['valor']  = str_replace(' ', '', $nrow->valor) != '' ? $nrow->valor : '-';
                                                $informacion_adicional[$contAdicional] = $campoAdicional;
                                                if ($nrow->nombre != '')
                                                    array_push($infoAdicional, $informacion_adicional[$contAdicional]);
                                            }
                                        }
                                    }
                                }
                                //$infoAdicional['campoAdicional'] = $informacion_adicional;
                                $data['infoAdicional'] = $infoAdicional;
                            } else {
                                $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existe la empresa', 'TipoDoc' => 'Factura');
                                echo json_encode($result);
                                return;
                            }
                            $errores = $this->validarData($data);
                            if (count($verificar) > 0 && isset($verificar['id'])) {
                                $dataError = [
                                    'data' => $data,
                                    'errors' => $errores
                                ];
                                return $dataError;
                            }
                            /*echo '<pre>';
                            print_r($data);
                            print_r($errores);
                            exit;*/
                            /*if (Auth::user()->id == '0921605895') {
                                echo '<pre>';
                                print_r($data);
                                print_r($errores);
                                exit;
                            }*/
                            /*if ($empresa->id == '1314490929001' || $empresa->id == '1307189140001' || $empresa->id == '0993366635001') { //Manta
                                exit;
                            }*/
                            /*if ($empresa->id == '0993075000001') {
                                echo '<pre>';
                                print_r($data);
                                print_r($errores);
                                exit;
                            }*/
                            /*if ($empresa->id == '2390058384001') {
                                echo '<pre>';
                                print_r($data);
                                print_r($errores);
                                exit;
                            }*/
                            /*if ($empresa->id == '0993094072001') { //Manta
                                echo '<pre>';
                                print_r($data);
    
                                print_r($errores);
                                exit;
                                }*/
                            /*if ($infoTributaria['ruc'] == '0993378620001') {
                                    echo '<pre>';
                                    print_r($data);
                                    print_r($errores);
                                    print_r($cliente);
                                    $param = [
                                        'email' => $cliente->email_representante,
                                        'nombre' => $cliente->nombre != '' ? $cliente->nombre : $cliente->nombre_representante,
                                        'claveAcceso' => $data['infoTributaria']['claveAcceso'],
                                        'estab' => $data['infoTributaria']['estab'],
                                        'ptoEmi' =>  $data['infoTributaria']['ptoEmi'],
                                        'secuencial' =>  $data['infoTributaria']['secuencial'],
                                        'tipoDoc' => $data['infoTributaria']['codDoc'],
                                        'fechaEmision' => $data['infoTributaria']['fecha_emision'],
                                        'valorPagar' => $data['infoFactura']['importeTotal'],
                                    ];
                                    print_r($param);
                                    exit;
                                }*/
                            if (count($errores) > 0) {
                                $arrayDocElec = [
                                    'id_de_pasos' => 7,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoFactura' => json_encode($data['infoFactura']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' => $infoTributaria['identificacionComprador'],
                                    'ruc_emisor' => $infoTributaria['ruc'],
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                    'id_documento' => $row->id,
                                ];
                                $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayDocElec, 7);
                                Ct_ventas::updateSinGenerarXML($row->id);
                                $arrayDato = [
                                    'tipo_documento' => $infoTributaria['codDoc'],
                                    'id_pasos' => 7,
                                    'id_documento' => $row->id,
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'informacion' => 'Error en validacion de datos',
                                    'comentario' => json_encode($errores),
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' => $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                ];
                                Ct_ventas::setLogDocElectronico($arrayDato);
                                $idInsertados .= $id_doc . ',';
                                $arrayLog = [
                                    'id_de_documentos_electronicos' => $id_doc,
                                    'descripcion_error' => json_encode($errores),
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'id_documento' => $row->id
                                ];
                                De_Log_Error::setErrorLog($arrayLog);
                            } else {
                                //Creacion XML
                                $arrayLogError = [
                                    'id_de_pasos' => 1,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoFactura' => json_encode($data['infoFactura']),
                                    'detalles' => json_encode($data['detalles']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' =>  $infoTributaria['identificacionComprador'],
                                    'ruc_emisor' => $infoTributaria['ruc'],
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                    'id_documento' => $row->id
                                ];
                                $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayLogError, 1);
                                if (is_numeric($id_doc)) {
                                    $idInsertados .= $id_doc . ',';
                                    $comprobante = $this->generarDocElectronicoXml($data);
                                    /*if ($empresa->id == '2390058384001') {
                                        header('Content-type: text/xml');
                                        echo $comprobante;
                                        exit;
                                        
                                    }*/
                                    Ct_ventas::updateGenerarXML($row->id, $data['infoTributaria']['claveAcceso']);
                                    $arrayDato = [
                                        'tipo_documento' => $infoTributaria['codDoc'],
                                        'id_pasos' => 1,
                                        'id_documento' => $row->id,
                                        'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                        'informacion' => 'Documento generado',
                                        'id_usuariomod' => $idUsuario,
                                        'id_usuariocrea' => $idUsuario,
                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                        'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                    ];
                                    Ct_ventas::setLogDocElectronico($arrayDato);
                                    if ($comprobante != '') {
                                        $docAnte = simplexml_load_string($comprobante);
                                        /*  echo '<pre>';
                                            print_r($docAnte);
                                            exit; */
                                        if (!empty($docAnte)) {
                                            //validar xsd
                                            $validacion = $this->validarXmlToXsd($comprobante);
                                            /*echo '<pre>';
                                                echo $comprobante;
                                                print_r($validacion);
                                                exit;*/
                                            if ($validacion['isValidoXsd'] != 1) {
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($validacion),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $result = array('code' => 204, 'state' => true, 'data' => json_encode($validacion), 'message' => 'ok|No existen documentos para emitir', 'tipoDoc' => 'factura');
                                                $arrayDato = [
                                                    'tipo_documento' => $infoTributaria['codDoc'],
                                                    'id_pasos' => 8,
                                                    'id_documento' => $row->id,
                                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                    'informacion' => json_encode($validacion),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' => $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                ];
                                                Ct_ventas::setLogDocElectronico($arrayDato);
                                                echo json_encode($result);
                                                return;
                                            }
                                            De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                            Ct_ventas::updateValidacionXSD($row->id);
                                            $arrayDato = [
                                                'tipo_documento' => $infoTributaria['codDoc'],
                                                'id_pasos' => 2,
                                                'id_documento' => $row->id,
                                                'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                'informacion' => 'Documento validado por XSD',
                                                'id_usuariomod' => $idUsuario,
                                                'id_usuariocrea' => $idUsuario,
                                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                            ];
                                            Ct_ventas::setLogDocElectronico($arrayDato);
                                            //firmar xml
                                            $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $datosFirma->ruta_firma; //firma.p12
                                            $password = $datosFirma->clave_firma; //'3duard0faustos';
                                            $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                                            /*echo '<pre>';
                                            print_r($result);
                                            exit;*/
                                            if (!empty($result)) {
                                                De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                                Ct_ventas::updateXmlFirmado($row->id);
                                                $arrayDato = [
                                                    'tipo_documento' => $infoTributaria['codDoc'],
                                                    'id_pasos' => 3,
                                                    'id_documento' => $row->id,
                                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                    'informacion' => 'Documento firmado',
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' => $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                ];
                                                Ct_ventas::setLogDocElectronico($arrayDato);
                                                //recepcion sri
                                                $respuesta = $this->recibirWs($result, $data['infoTributaria']['ambiente']);
                                                if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                                    De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                                    Ct_ventas::updateRecibidoSri($row->id);
                                                    $arrayDato = [
                                                        'tipo_documento' => $infoTributaria['codDoc'],
                                                        'id_pasos' => 4,
                                                        'id_documento' => $row->id,
                                                        'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                        'informacion' => 'Documento recibido SRI',
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' => $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    Ct_ventas::setLogDocElectronico($arrayDato);
                                                    $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                                    $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                                    De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                    //autorzacion sri
                                                    $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso, $data['infoTributaria']['ambiente']);
                                                    /*echo '<pre>';
                                                    print_r($respuestaAutorizacion->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->mensajes);
                                                    $arrayDoc = [
                                                        'doc_electronico' => 0,
                                                    ];
                                                    Ct_ventas::where('id', $row->id)
                                                        ->update($arrayDoc);
                                                    exit;*/
                                                    $estadoAutorizacion = $this->generarXmlAutorizacion($respuestaAutorizacion);
                                                    if ($estadoAutorizacion == 'AUTORIZADO') {
                                                        De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                                        Ct_ventas::updateXmlAutorizacion($row->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $data['infoTributaria']['secuencial'], $data['infoTributaria']['claveAcceso']);
                                                        $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                        $this->generarPdf($docAnte->infoTributaria->claveAcceso);
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => 5,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Documento autorizado SRI',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        Ct_ventas::setLogDocElectronico($arrayDato);
                                                        $param = [
                                                            'email' => $cliente->email_representante,
                                                            'nombre' => $cliente->nombre != '' ? $cliente->nombre : $cliente->nombre_representante,
                                                            'claveAcceso' => $data['infoTributaria']['claveAcceso'],
                                                            'estab' => $data['infoTributaria']['estab'],
                                                            'ptoEmi' =>  $data['infoTributaria']['ptoEmi'],
                                                            'secuencial' =>  $data['infoTributaria']['secuencial'],
                                                            'tipoDoc' => $data['infoTributaria']['codDoc'],
                                                            'fechaEmision' => $data['infoTributaria']['fecha_emision'],
                                                            'valorPagar' => $data['infoFactura']['importeTotal'],
                                                        ];

                                                        if ($cliente->email_representante != '')
                                                            $this->enviar_correo($param);
                                                        $arrayVentaAct = [
                                                            'numero' => $data['infoTributaria']['secuencial']
                                                        ];
                                                        $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => 11,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Enviado al cliente',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        Ct_ventas::setLogDocElectronico($arrayDato);
                                                    } else {
                                                        De_Documentos_Electronicos::updateXmlNoAutorizacion($comprobante, $respuestaAutorizacion);
                                                        Ct_ventas::updateXmlNoAutorizacion($row->id);
                                                        $arrayLog = [
                                                            'id_de_documentos_electronicos' => $id_doc,
                                                            'descripcion_error' => json_encode($respuestaAutorizacion),
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' =>  $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                            'id_documento' => $row->id,
                                                        ];
                                                        De_Log_Error::setErrorLog($arrayLog);
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => $estadoAutorizacion == 'EN PROCESO' ? 12 : 10,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Documento no autorizado',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        Ct_ventas::setLogDocElectronico($arrayDato);
                                                    }
                                                } else {
                                                    if ($this->recuperarFacturaAutorizada($row, $data['infoTributaria']['claveAcceso'], $data['infoTributaria']['ambiente'], $idUsuario)) {
                                                        $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                        continue;
                                                    }
                                                    $this->generarXmlRespuesta($respuesta);
                                                    De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                                    Ct_ventas::updateXmlNoRecepcion($row->id);
                                                    $arrayLog = [
                                                        'id_de_documentos_electronicos' => $id_doc,
                                                        'descripcion_error' => json_encode($respuesta),
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' =>  $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    De_Log_Error::setErrorLog($arrayLog);
                                                    $arrayDato = [
                                                        'tipo_documento' => $infoTributaria['codDoc'],
                                                        'id_pasos' => 9,
                                                        'id_documento' => $row->id,
                                                        'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                        'informacion' => json_encode($respuesta),
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' => $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    Ct_ventas::setLogDocElectronico($arrayDato);
                                                    //return $respuesta['mensajesWs'];
                                                }
                                            } else {
                                                //Log error al firmar XML
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($result),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $arrayDato = [
                                                    'tipo_documento' => $infoTributaria['codDoc'],
                                                    'id_pasos' => 6,
                                                    'id_documento' => $row->id,
                                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                    'informacion' => json_encode($result),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' => $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                ];
                                                Ct_ventas::setLogDocElectronico($arrayDato);
                                            }
                                        }
                                    } else {
                                        //log No genero XML
                                        $arrayLog = [
                                            'id_de_documentos_electronicos' => $id_doc,
                                            'descripcion_error' => 'Error al crear el documento XML',
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' =>  $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                        ];
                                        De_Log_Error::setErrorLog($arrayLog);
                                        $arrayDato = [
                                            'tipo_documento' => $infoTributaria['codDoc'],
                                            'id_pasos' => 7,
                                            'id_documento' => $row->id,
                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                            'informacion' => 'Error al crear el documento XML',
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' => $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                        ];
                                        Ct_ventas::setLogDocElectronico($arrayDato);
                                    }
                                } else {
                                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'error|' . $id_doc);
                                    echo json_encode($result);
                                    return;
                                }
                            }
                        }
                    }
                } else {
                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existen documentos para emitir', 'tipoDoc' => 'Factura');
                    echo json_encode($result);
                    return;
                }
                $infoErrores = De_Documentos_Electronicos::revisionEstadosDocumentosErrores($idInsertados);
                $infoAutorizados = '';
                if ($claveInsertados != '')
                    $infoAutorizados = De_Documentos_Electronicos::revisionEstadosDocumentosAutorizados($claveInsertados);
                $result = array('code' => 200, 'state' => true, 'data' => ['errores:' => $infoErrores, 'autorizado:' => $infoAutorizados], 'message' => 'proceso ejecutado correctamente', 'tipoDoc' => 'Factura');
                echo json_encode($result);
            } else {
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                if (!$this->consultarEstadoSRI(session('id_empresa'))) {
                    $arrayLog = [
                        'id_de_documentos_electronicos' => $id_doc,
                        'descripcion_error' => json_encode(['Error:' => 'SRI sin servicio']),
                        'id_usuariomod' => $idUsuario,
                        'id_usuariocrea' =>  $idUsuario,
                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                        'id_documento' => $id_doc
                    ];
                    De_Log_Error::setErrorLog($arrayLog);
                    $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $arrayLog], 'message' => 'Error');
                    return json_encode($result);
                } else {
                    $cont = 0;
                    $empresa = Empresa::getEmpresa($dinfo['empresa']);
                    if ($empresa != '') {
                        $datosFirma = De_Empresa::getDatos($dinfo['empresa']);
                        $venta = Ct_ventas::where('id', $dinfo['idVenta'])->first();
                        $firma['ruta_firma'] = $datosFirma->ruta_firma;
                        $firma['clave_firma'] = $datosFirma->clave_firma;
                        $data['datosfirma'] = $firma;
                        $infoTributaria['fecha_emision'] = date('d/m/Y');
                        $infoTributaria['ambiente'] = 1;
                        $infoTributaria['tipoEmision'] = 1;
                        $infoTributaria['razonSocial'] = $empresa->razonsocial;
                        $infoTributaria['nombreComercial'] = $empresa->nombrecomercial;
                        $infoTributaria['ruc'] = $empresa->id;
                        $infoTributaria['codDoc'] = '01';
                        $infoTributaria['estab'] = str_pad($venta->sucursal, 3, 0, STR_PAD_LEFT);
                        $infoTributaria['ptoEmi'] = str_pad($venta->punto_emision, 3, 0, STR_PAD_LEFT);
                        $infoTributaria['dirMatriz'] = $empresa->direccion;
                        $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                        $datosTributarios = De_Info_Tributaria::reservarSecuencial(session('id_empresa'), $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);

                        if ($datosTributarios == '') {
                            $result = array('code' => 204, 'state' => true, 'data' => ['error:' => 'no se ha configurado los datos tributarios para esta empresa: ' . $empresa->id], 'message' => 'no existen datos tributarios para esta empresa');
                            echo json_encode($result);
                            return;
                        }
                        $secuencial = $datosTributarios->secuencial_nro;
                        $infoTributaria['ambiente'] = $datosFirma->ambiente;
                        $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                        $claveObj = new ClaveAcceso();
                        $campos = [
                            'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($venta->fecha))),
                            'tipo_comprobante' => $infoTributaria['codDoc'],
                            'ruc' => $infoTributaria['ruc'],
                            'tipo_ambiente' => $infoTributaria['ambiente'],
                            'punto_establecimiento' => $infoTributaria['estab'],
                            'punto_emision' => $infoTributaria['ptoEmi'],
                            'numero_comprobante' => $infoTributaria['secuencial'],
                            'codigo_numerico' => $infoTributaria['secuencial'],
                            'tipo_emision' => $infoTributaria['tipoEmision']
                        ];
                        $claveObj->llenarDatos($campos);
                        $infoTributaria['claveAcceso'] = $claveObj->clave_acceso;
                        $data['infoTributaria'] = $infoTributaria;
                        //InfoTributaria
                        $infoFactura['fechaEmision'] = $venta->fecha;
                        $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                            ->where('codigo_sucursal', $infoTributaria['estab'])
                            ->first();
                        if (isset($sucursal->direccion_sucursal)) {
                            $infoFactura['dirEstablecimiento'] = $sucursal->direccion_sucursal;
                        } else {
                            $infoFactura['dirEstablecimiento'] = $empresa->direccion;
                        }
                        if ($datosFirma->contribuyente_especial != '' && $datosFirma->contribuyente_especial != 0) {
                            $infoFactura['contribuyenteEspecial'] = $datosFirma->contribuyente_especial; //Opcional
                        }
                        $infoFactura['obligadoContabilidad'] = 'NO'; //Opcional
                        if ($datosFirma->contabilidad == 1)
                            $infoFactura['obligadoContabilidad'] = 'SI'; //Opcional
                        $cliente = Ct_Clientes::getCliente($venta->id_cliente);
                        if ($cliente != '') {
                            $email = $cliente->email_representante;
                            $telefonoCliente = $cliente->telefono1;
                            $infoFactura['tipoIdentificacionComprador'] = str_pad($cliente->tipo, 2, 0, STR_PAD_LEFT);
                            $infoFactura['identificacionComprador'] = $cliente->identificacion;
                            $infoFactura['razonSocialComprador'] = $cliente->nombre;
                            $infoFactura['direccionComprador'] = $cliente->direccion_representante;
                        } else {
                            $infoFactura['tipoIdentificacionComprador'] = null;
                            $infoFactura['identificacionComprador'] = null;
                            $infoFactura['razonSocialComprador'] = null;
                            $infoFactura['direccionComprador'] = null; //Opcional
                        }
                        $infoFactura['totalSinImpuestos'] = $venta->subtotal_0 + $venta->subtotal_12;
                        $infoFactura['totalConImpuestos'] = $venta->total_final;
                        $totalConImpuestos = [];
                        $totalConImpuesto = [];
                        $conImpuesto = 0;
                        $impuestos = Ct_detalle_venta::getDetalles($venta->id);
                        if (count($impuestos) > 0) {
                            foreach ($impuestos as $impuesto) {
                                if ($impuesto->check_iva == 1)
                                    $codigoImpuesto = De_Codigo_Impuestos::where('porcentaje', ($this->porIva->iva * 100))->where('codigo_impuesto', 2)->first();
                                else
                                    $codigoImpuesto = De_Codigo_Impuestos::where('id', 2)->first();
                                $totalConImpuesto['codigo'] = $codigoImpuesto->codigo_impuesto;
                                $totalConImpuesto['codigoPorcentaje'] = $codigoImpuesto->codigo;
                                $totalConImpuesto['descuentoAdicional'] = $impuesto->descuento;
                                $totalConImpuesto['baseImponible'] = ($impuesto->cantidad * $impuesto->precio) - $impuesto->descuento;
                                if ($impuesto->check_iva == 1)
                                    $totalConImpuesto['valor'] = $this->getDecimal(($impuesto->precio * $impuesto->cantidad) * $impuesto->porcentaje);
                                else
                                    $totalConImpuesto['valor'] = $this->getDecimal(0);
                                array_push($totalConImpuestos, $totalConImpuesto);
                                $conImpuesto++;
                            }
                        }
                        $infoFactura['totalConImpuestos'] = $totalConImpuestos;
                        $infoFactura['propina'] = $this->getDecimal(0);
                        $infoFactura['importeTotal'] = $venta->total_final;
                        $infoFactura['moneda'] = Ct_Divisas::getMoneda();
                        $detallePagos = Ct_detalle_venta::getDetalles($venta->id);
                        //Formas de pago
                        $pagos = [];
                        $pago = [];
                        $conPagos = 0;
                        $formasPagos = Ct_Forma_Pago::join('ct_tipo_pago as t', 'ct_forma_pago.tipo', '=', 't.id')
                            ->where('id_ct_ventas', $venta->id)->get([
                                't.codigo'
                            ]);
                        if (count($formasPagos) > 0) {
                            foreach ($formasPagos as $formaPago) {
                                $pago['formaPago'] = str_pad($formaPago->codigo, 2, 0, STR_PAD_LEFT);
                                $pago['total'] = $venta->total_final;
                                $pago['plazo'] = $venta->dias_plazo;
                                $pago['unidadTiempo'] = 'dias';
                                array_push($pagos, $pago);
                            }
                        }
                        $infoFactura['pagos'] = $pagos;
                        $data['infoFactura'] = $infoFactura;
                        //detalles
                        $detalle = [];
                        $detalles = [];
                        $conDetalle = 0;
                        $detalleFactura = Ct_detalle_venta::getDetalles($venta->id);
                        if (count($detalleFactura) > 0) {
                            foreach ($detalleFactura as $detaFactura) {
                                $detalle['p_impuesto'] = $detaFactura->check_iva !== null ? ($this->porIva->iva * 100) : 0;
                                $detalle['codigoPrincipal'] = $detaFactura->id_ct_productos;
                                $detalle['codigoAuxiliar'] = $detaFactura->id_ct_productos;
                                $detalle['descripcion'] = $detaFactura->nombre;
                                $detalle['cantidad'] = $detaFactura->cantidad;
                                $detalle['precioUnitario'] = $detaFactura->precio;
                                $detalle['descuento'] = $detaFactura->descuento;
                                $detalle['precioTotalSinImpuesto'] = $venta->subtotal_0 + $venta->subtotal_12;
                                $contAdicional = 0;
                                if ($detaFactura->detalle != '') {
                                    $detAdicional = [
                                        'nombre' => 'detalle',
                                        'valor' => $detaFactura->detalle
                                    ];
                                    $contAdicional++;
                                }
                                /*if ($detaFactura->adicional != '') {
                                    $detAdicional_ = [
                                        'nombre' => 'adicional',
                                        'valor' => $detaFactura->adicional
                                    ];
                                    $contAdicional++;
                                }*/
                                if ($contAdicional > 0) {
                                    $detallesAdicionales = [isset($detAdicional) ? $detAdicional : []];
                                    $detalle['detallesAdicionales'] = $detallesAdicionales;
                                }
                                $detalles[$conDetalle] = $detalle;
                                $conDetalle++;
                            }
                        }
                        $data['detalles'] = $detalles;
                        $campoAdicional['nombre'] = "detalle";
                        $campoAdicional['valor']  = $venta->nota_electronica;
                        $informacion_adicional[0] = $campoAdicional;
                        $infoAdicional['campoAdicional'] = $informacion_adicional;
                        $data['infoAdicional'] = $informacion_adicional;
                        $errores = $this->validarData($data);
                        if (count($errores) > 0) {
                            $arrayDocElec = [
                                'id_de_pasos' => 7,
                                'infoTributaria' => json_encode($data['infoTributaria']),
                                'infoFactura' => json_encode($data['infoFactura']),
                                'infoAdicional' => json_encode($data['infoAdicional']),
                                'establecimiento' => $infoTributaria['estab'],
                                'emision' => $infoTributaria['ptoEmi'],
                                'secuencial' => $infoTributaria['secuencial'],
                                'ruc_receptor' => $infoTributaria['identificacionComprador'],
                                'ruc_emisor' => $infoTributaria['ruc'],
                                'id_usuariomod' => $idUsuario,
                                'id_usuariocrea' =>  $idUsuario,
                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                'id_documento' => $venta->id,
                            ];
                            $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayDocElec, 7);
                            Ct_ventas::updateSinGenerarXML($venta->id);
                            $idInsertados .= $id_doc . ',';
                            $arrayLog = [
                                'id_de_documentos_electronicos' => $id_doc,
                                'descripcion_error' => json_encode($errores),
                                'id_usuariomod' => $idUsuario,
                                'id_usuariocrea' =>  $idUsuario,
                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                'id_documento' => $venta->id
                            ];
                            De_Log_Error::setErrorLog($arrayLog);
                            $infoErrores = De_Documentos_Electronicos::revisionEstadosDocumentosErrores($idInsertados);
                            $infoAutorizados = '';
                            if ($claveInsertados != '')
                                $infoAutorizados = De_Documentos_Electronicos::revisionEstadosDocumentosAutorizados($claveInsertados);
                            $result = array('code' => 200, 'state' => true, 'data' => ['errores:' => $infoErrores, 'autorizado:' => $infoAutorizados], 'message' => 'proceso ejecutado correctamente');
                            return json_encode($result);
                        } else {
                            //Creacion XML
                            $arrayLogError = [
                                'id_de_pasos' => 1,
                                'infoTributaria' => json_encode($data['infoTributaria']),
                                'infoFactura' => json_encode($data['infoFactura']),
                                'detalles' => json_encode($data['detalles']),
                                'infoAdicional' => json_encode($data['infoAdicional']),
                                'establecimiento' => $infoTributaria['estab'],
                                'emision' => $infoTributaria['ptoEmi'],
                                'secuencial' => $infoTributaria['secuencial'],
                                'ruc_receptor' =>  $infoFactura['identificacionComprador'],
                                'ruc_emisor' => $infoTributaria['ruc'],
                                'id_usuariomod' => $idUsuario,
                                'id_usuariocrea' =>  $idUsuario,
                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                'id_documento' => $venta->id
                            ];
                            $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayLogError, 1);
                            if (is_numeric($id_doc)) {
                                $idInsertados .= $id_doc . ',';
                                $comprobante = $this->generarDocElectronicoXml($data);
                                Ct_ventas::updateGenerarXML($venta->id, $data['infoTributaria']['claveAcceso']);
                                if ($comprobante != '') {
                                    $docAnte = simplexml_load_string($comprobante);
                                    if (!empty($docAnte)) {
                                        //validar xsd
                                        $validacion = $this->validarXmlToXsd($comprobante);
                                        if ($validacion['isValidoXsd'] != 1) {
                                            $arrayLog = [
                                                'id_de_documentos_electronicos' => $id_doc,
                                                'descripcion_error' => json_encode($validacion),
                                                'id_usuariomod' => $idUsuario,
                                                'id_usuariocrea' =>  $idUsuario,
                                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                'id_documento' => $venta->id
                                            ];
                                            De_Log_Error::setErrorLog($arrayLog);
                                            $result = array('code' => 500, 'state' => true, 'data' => json_encode($validacion), 'message' => 'Error: al validar documento xml.');
                                            return json_encode($result);
                                        }
                                        De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                        Ct_ventas::updateValidacionXSD($venta->id);
                                        //firmar xml
                                        $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $datosFirma->ruta_firma; //firma.p12
                                        $password = $datosFirma->clave_firma; //'3duard0faustos';
                                        $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                                        if (!empty($result)) {
                                            De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                            Ct_ventas::updateXmlFirmado($venta->id);
                                            //recepcion sri
                                            $respuesta = $this->recibirWs($result, $data['infoTributaria']['ambiente']);
                                            De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                            Ct_ventas::updateRecibidoSri($venta->id);
                                            if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                                //$this->procesarRespuestaXml($respuesta);
                                                $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                                $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                                De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                //autorzacion sri
                                                $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso, $data['infoTributaria']['ambiente']);
                                                $estadoAutorizacion = $this->generarXmlAutorizacion($respuestaAutorizacion);
                                                if ($estadoAutorizacion == 'AUTORIZADO') {
                                                    De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                                    Ct_ventas::updateXmlAutorizacion($venta->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $data['infoTributaria']['secuencial']);
                                                    $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                    $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
                                                    $param = [
                                                        'email' => $venta->email_representante,
                                                        'nombre' => $venta->nombre,
                                                        'claveAcceso' => $docAnte->infoTributaria->claveAcceso,
                                                        'estab' => $data['infoTributaria']['estab'],
                                                        'ptoEmi' =>  $data['infoTributaria']['ptoEmi'],
                                                        'secuencial' =>  $data['infoTributaria']['secuencial'],
                                                        'tipoDoc' => $this->tipoDocumento
                                                    ];
                                                    $this->enviar_correo($param);
                                                    $arrayVentaAct = [
                                                        'numero' => $data['infoTributaria']['secuencial']
                                                    ];
                                                    Ct_ventas::where('id', $venta->id)->update($arrayVentaAct);
                                                } else {
                                                    De_Documentos_Electronicos::updateXmlNoAutorizacion($comprobante, $respuestaAutorizacion);
                                                    Ct_ventas::updateXmlNoAutorizacion($venta->id);
                                                    $arrayLog = [
                                                        'id_de_documentos_electronicos' => $id_doc,
                                                        'descripcion_error' => json_encode($respuestaAutorizacion),
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' =>  $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                        'id_documento' => $venta->id,
                                                    ];
                                                    De_Log_Error::setErrorLog($arrayLog);
                                                }
                                            } else {
                                                $this->generarXmlRespuesta($respuesta);
                                                De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                                Ct_ventas::updateXmlNoRecepcion($venta->id);
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($respuesta),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                    'id_documento' => $venta->id,
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                //return $respuesta['mensajesWs'];
                                            }
                                        } else {
                                            //Log error al firmar XML
                                            $arrayLog = [
                                                'id_de_documentos_electronicos' => $id_doc,
                                                'descripcion_error' => json_encode($result),
                                                'id_usuariomod' => $idUsuario,
                                                'id_usuariocrea' =>  $idUsuario,
                                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                'id_documento' => $venta->id,
                                            ];
                                            De_Log_Error::setErrorLog($arrayLog);
                                        }
                                    }
                                } else {
                                    //log No genero XML
                                    $arrayLog = [
                                        'id_de_documentos_electronicos' => $id_doc,
                                        'descripcion_error' => 'Error al crear el documento XML',
                                        'id_usuariomod' => $idUsuario,
                                        'id_usuariocrea' =>  $idUsuario,
                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                        'id_documento' => $venta->id,
                                    ];
                                    De_Log_Error::setErrorLog($arrayLog);
                                }
                            } else {
                                $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'error|' . $id_doc);
                                echo json_encode($result);
                                return;
                            }
                        }
                        $respuestaNo->nro_comprobante = $numDocumento;
                        return $respuestaNo;
                    }
                }
            }
        } catch (Exception $ex) {
            if (isset($row) && isset($row->id)) {
                Ct_ventas::where('id', $row->id)
                    ->where('doc_electronico', '<>', 5)
                    ->update(['doc_electronico' => 9]);
            }
            if ($id_doc != 0) {
                $arrayLog = [
                    'id_de_documentos_electronicos' => $id_doc,
                    'descripcion_error' => json_encode($ex->getMessage()),
                    'id_usuariomod' => $idUsuario,
                    'id_usuariocrea' =>  $idUsuario,
                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                ];
                De_Log_Error::setErrorLog($arrayLog);
            }
            $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $ex->getMessage(), 'linea' => $ex->getLine()], 'message' => 'Error');
            return json_encode($result);
        }
    }

    private function recuperarFacturaAutorizada($factura, $claveAcceso, $ambiente, $idUsuario)
    {
        if (empty($claveAcceso) || strlen($claveAcceso) !== 49) {
            return false;
        }

        try {
            $respuesta = $this->autorizacion_sri($claveAcceso, $ambiente);
            $estado = $this->generarXmlAutorizacion($respuesta);
            if ($estado !== 'AUTORIZADO') {
                return false;
            }

            $establecimiento = substr($claveAcceso, 24, 3);
            $puntoEmision = substr($claveAcceso, 27, 3);
            $secuencial = substr($claveAcceso, 30, 9);
            De_Documentos_Electronicos::where('clave_acceso', $claveAcceso)->update([
                'id_de_pasos' => 5,
                'respuestaSriAutorizacion' => json_encode($respuesta),
            ]);
            Ct_ventas::updateXmlAutorizacion(
                $factura->id,
                $establecimiento,
                $puntoEmision,
                $secuencial,
                $claveAcceso
            );
            Ct_ventas::setLogDocElectronico([
                'tipo_documento' => '01',
                'id_pasos' => 5,
                'id_documento' => $factura->id,
                'clave_acceso' => $claveAcceso,
                'informacion' => 'Documento autorizado SRI y recuperado después de una falla de conexión.',
                'id_usuariomod' => $idUsuario,
                'id_usuariocrea' => $idUsuario,
                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getNonce($n)
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }
    public static function envio_factura($data)
    {
        if (Auth::check()) {
            $id_usuario = Auth::user()->id;
        } else {
            $id_usuario       = 'FACELECTRO';
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $nonce      = EmisionDocumentosController::getNonce(12);
        $empresa    = Empresa::findorfail($data['empresa']);
        if ($empresa->electronica != 1 && !is_null($empresa->appid) && !is_null($empresa->appsecret) && !is_null($empresa->url) && !is_null($empresa->establecimiento) && !is_null($empresa->punto_emision)) {
            return "empresa no permite facturacion electronica";
        } elseif ($empresa->electronica != 1) {
            return "empresa no permite facturacion electronica";
        }
        $appId     = $empresa->appid;
        $appSecret = $empresa->appsecret;
        $date      = date('c');
        $Token     = base64_encode(sha1($nonce . $date . $appSecret));
        $envio['company'] = $data['empresa'];
        //VALIDO SI LA CEDULA O RUC ES VALIDO
        $valida_cedula = true;
        if ($data['cliente']['tipo'] != 6) {
            $valida_cedula = EmisionDocumentosController::validarCedula($data['cliente']['cedula']);
        }
        if (!$valida_cedula && ($data['cliente']['tipo'] == 4 || $data['cliente']['tipo'] == 5)) {
            return "numero de cedula incorrecto";
        }
        //INGRESO LOS DATOS DE LA PERSONA
        $person['document'] = $data['cliente']['cedula'];
        if ($data['cliente']['tipo'] == 6) {
            $tipo = "06";
        } elseif ($data['cliente']['tipo'] == 8) {
            $tipo = "08";
        } elseif (strlen($data['cliente']['cedula']) == 13 && substr($data['cliente']['cedula'], -3) == '001') {
            $tipo = "04";
        } else {
            $tipo = "05";
        }
        $person['documentType']       = $tipo;
        $person['name']               = $data['cliente']['nombre'];
        $person['surname']            = $data['cliente']['apellido'];
        $person['email']              = $data['cliente']['email'];
        $person['mobile']             = $data['cliente']['telefono'];
        $person['address']['street']  = $data['cliente']['direccion']['calle'];
        $person['address']['city']    = $data['cliente']['direccion']['ciudad'];
        $person['address']['country'] = 'EC';
        $envio['person']              = $person;
        if (count($data['productos']) <= 0) {
            return "no existen productos";
        }
        //ingreso y creo los productos
        $productos = array();
        foreach ($data['productos'] as $key => $value) {
            $nombre_p            = "";
            $nombre_p            = substr($value['sku'], 0, 20);
            $nombre_p            = str_replace(" ", "_", $nombre_p);
            $arreglo             = array();
            $arreglo['sku']      = $nombre_p;
            $arreglo['name']     = $value['nombre'];
            $arreglo['qty']      = intval($value['cantidad']);
            $arreglo['price']    = floatval(number_format($value['precio'], 2, '.', ''));
            $arreglo['discount'] = floatval(number_format($value['descuento'], 2, '.', ''));
            $arreglo['subtotal'] = floatval(number_format($value['subtotal'], 2, '.', ''));
            $arreglo['tax']      = floatval(number_format($value['tax'], 2, '.', ''));
            $arreglo['total']    = floatval(number_format($value['total'], 2, '.', ''));
            array_push($productos, $arreglo);
        }
        $envio['items'] = $productos;
        //parametros de pago
        if (isset($data['externo'])) {
            $pago['establecimiento'] = $data['establecimiento'];
            $pago['ptoEmision']      = $data['ptoEmision'];
        } else {
            $pago['establecimiento'] = $empresa->establecimiento;
            $pago['ptoEmision']      = $empresa->punto_emision;
        }
        if ($empresa->externo == 1) {
            $data['establecimiento'] = $empresa->establecimiento;
            $data['ptoEmision']      = $empresa->punto_emision;
        }
        $info_adicional          = array();
        foreach ($data['pago']['informacion_adicional'] as $key => $value) {
            $nombre_p         = "";
            $nombre_p         = substr($value['nombre'], 0, 20);
            $nombre_p         = str_replace(" ", "_", $nombre_p);
            $arreglo          = array();
            $arreglo['key']   = $nombre_p;
            $arreglo['value'] = $value['valor'];
            array_push($info_adicional, $arreglo);
        }
        $pago['infoAdicional']      = $info_adicional;
        $pago['formaPago']          = $data['pago']['forma_pago'];
        $pago['plazoDias']          = $data['pago']['dias_plazo'];
        $envio['billingParameters'] = $pago;
        $envio['userAgent']         = "SIAAM SOFTWARE/1";
        $envio_2 = json_encode($envio);
        if ($empresa->externo == 1) {
            $url     = $empresa->url;
        } else {
            $url     = $empresa->url . 'billing/create/';
        }
        EmisionDocumentosController::crearcarpeta($url);
        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API",
            'url'         => $url,
            'dato1'       => $envio_2,
            'dato2'       => "Inicia api",
        ]);
        if ($empresa->externo == 0) {
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                    'method'  => 'POST',
                    'content' => $envio_2,
                ),
            );
        } else {
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                    'method'  => 'POST',
                    'content' => json_encode($data),
                ),
            );
        }
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API",
            'url'         => $url,
            'dato1'       => $response,
            'dato2'       => "FIN API",
        ]);
        /*$response        = '{"status":{"status":"success","message":"","reason":"","date":"2020-12-30T10:12:54-05:00"},"requestId":"39","comprobante":"004-002-000000013"}';*/
        $respuesta_array = json_decode($response);
        if ($data['contable'] == 1 && $empresa->externo == 0) {
            EmisionDocumentosController::crea_factura($data, $respuesta_array);
        }
        return $respuesta_array;
    }
    public static function crea_factura($data, $info_comprobante, $datosEnviados = '')
    {
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = 'FACELECTRO';
        $fecha_as        = $data['fecha_emision'];
        $id_empresa      = $data['empresa'];
        $llevaOrden      = false;
        $numero          = $info_comprobante->comprobante;
        $numero1         = $numero;
        $num_comprobante = 0;
        $cliente = Ct_Clientes::where('identificacion', '=', $data['cliente']['cedula'])->first();
        $cliente_datos = $data['cliente'];
        DB::beginTransaction();
        try {
            if (is_null($cliente)) {
                // cliente
                Ct_Clientes::create([
                    'nombre'                  => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
                    'tipo'                    => $cliente_datos['tipo'],
                    'identificacion'          => $cliente_datos['cedula'],
                    'clase'                   => '1',
                    'nombre_representante'    => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
                    'cedula_representante'    => $cliente_datos['cedula'],
                    'ciudad_representante'    => $cliente_datos['direccion']['ciudad'],
                    'direccion_representante' => $cliente_datos['direccion']['calle'],
                    'telefono1_representante' => $cliente_datos['telefono'],
                    'email_representante'     => $cliente_datos['email'],
                    'estado'                  => '1',
                    'id_usuariocrea'          => $idusuario,
                    'id_usuariomod'           => $idusuario,
                    'ip_creacion'             => $ip_cliente,
                    'ip_modificacion'         => $ip_cliente,
                ]);
            }
            $partes          = explode("-", $info_comprobante->comprobante);
            $c_sucursal      = $partes['0'];
            $c_caja          = $partes['1'];
            $num_comprobante = $info_comprobante->comprobante;
            $nfactura        = $partes['2'];
            $proced          = ' ';
            if (isset($data['laboratorio']) && $data['laboratorio'] == 1) {
                $proced = 'Examenes de Laboratorio';
            }
            $pacis = null;
            if (isset($data['paciente']))
                $pacis = Paciente::find($data['paciente']);

            $pac = "";
            if (!is_null($pacis)) {
                $pac = $pacis->apellido1 . ' ' . $pacis->apellido2 . ' ' . $pacis->nombre1;
            }

            $text        = 'Fact #' . ':' . $num_comprobante . '-' . $proced . ' | ' . $pac;
            $id_paciente = isset($data['paciente']) ? $data['paciente'] : null;

            if (is_null($id_paciente)) {
                $id_paciente = '9999999999';
            }
            //fix contranstrain
            if (is_null($pacis)) {
                $id_paciente = '9999999999';
            }
            //***GUARDADO EN LA TABLA LOG FACTURAS VENTA***
            //7******GUARDAdo TABLA ASIENTO CABECERA********

            $total1       = 0;
            $subtotal0    = 0;
            $subtotal12   = 0;
            $descuento    = 0;
            $descuento_0  = 0;
            $descuento_12 = 0;
            $iva          = 0;
            foreach ($data['productos'] as $value) {
                $total1 += $value['total'];
                $descuento += $value['descuento'];
                $iva += $value['tax'];
                if ($value['tax'] == 0) {
                    // $subtotal0 += $value['subtotal'];
                    $subtotal0 += ($value['precio'] * $value['cantidad']);
                    $descuento_0 += $value['descuento'];
                } else {
                    // $subtotal12 += $value['subtotal'];
                    $subtotal12 += ($value['precio'] * $value['cantidad']);
                    $descuento_12 += $value['descuento'];
                }
            }
            $base_imponible = $subtotal0 + $subtotal12;

            $input_cabecera = [
                'fecha_asiento'   => $fecha_as,
                'fact_numero'     => $nfactura,
                'id_empresa'      => $id_empresa,
                'observacion'     => $text,
                'valor'           => $total1,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            //$id_asiento_cabecera = 0;
            //GUARDAdo TABLA CT_VENTA.
            $factura_venta = [
                'sucursal'          => $c_sucursal,
                'punto_emision'     => $c_caja,
                'numero'            => $nfactura,
                'nro_comprobante'   => $num_comprobante,
                'id_asiento'        => $id_asiento_cabecera,
                'id_empresa'        => $id_empresa,
                'tipo'              => 'VEN-FA',
                'fecha'             => $fecha_as,
                'fecha_envio'       => $fecha_as,
                'divisas'           => 1,
                'nombre_cliente'    => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
                'id_cliente'        => $cliente_datos['cedula'], //nombre_cliente
                'direccion_cliente' => $cliente_datos['direccion']['calle'],
                'ruc_id_cliente'    => $cliente_datos['cedula'],
                'telefono_cliente'  => $cliente_datos['telefono'],
                'email_cliente'     => $cliente_datos['email'],
                'id_paciente'       => $id_paciente,
                'nombres_paciente'  => $pac,
                'seguro_paciente'   => isset($data['id_seguro']) ? $data['id_seguro'] : null,
                'concepto'          => isset($data['concepto']) ? $data['concepto'] : null,
                'copago'            => isset($data['copago']) ? $data['copago'] : null,
                'subtotal_0'        => $subtotal0,
                'subtotal_12'       => $subtotal12,
                'descuento'         => $descuento,
                'base_imponible'    => $base_imponible,
                'impuesto'          => $iva,
                'total_final'       => $total1,
                'valor_contable'    => $total1,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'electronica'       => 1,
                'emisor' => isset($data['emisor']) ? ($data['emisor'] == 'MD' ? 'MD' : null) : null,
                'doc_electronico' => isset($data['emisor']) ? ($data['emisor'] == 'MD' ? 0 : null) : null,
            ];
            $id_venta = Ct_ventas::insertGetId($factura_venta);
            $arr_total      = [];
            $total_iva      = 0;
            $total_impuesto = 0;
            $total_0        = 0;
            //kardex
            foreach ($data['productos'] as $valor) {
                $datos_iva = 0;
                if ($valor['tax'] > 0) {
                    $datos_iva = 1;
                }
                $detalle = [
                    'id_ct_ventas'    => $id_venta,
                    'id_ct_productos' => $valor['sku'],
                    'nombre'          => $valor['nombre'],
                    'cantidad'        => $valor['cantidad'],
                    'precio'          => $valor['precio'],
                    'descuento'       => $valor['descuento'],
                    'extendido'       => $valor['subtotal'],
                    'detalle'         => '',
                    'copago'          => isset($valor['copago']) ? $valor['copago'] : null,
                    'check_iva'       => $datos_iva,
                    'porcentaje'      => $this->porIva->iva,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
                Ct_detalle_venta::create($detalle);
            }

            //***MODULO CUENTA POR COBRAR***

            //cUENTAS X COBRAR CLIENTES

            $val_tol = $total1;

            if ($val_tol > 0) {
                $plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => '1.01.02.05.01',
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => $val_tol,
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            //    2.01.07.01.01 iva sobre ventas
            if ($iva > 0) {
                $plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => '2.01.07.01.01',
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $iva,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }
            // 4.1.01.02    Ventas Mercaderia Tarifa 12%
            if ($subtotal12 > 0) {
                $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => '4.1.01.02',
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $subtotal12,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            // 4.1.01.01    Ventas Mercaderia Tarifa 0%
            if ($subtotal0 > 0) {
                if ($data['empresa'] == '1391914857001') {
                    $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => '4.1.01.01',
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => $fecha_as,
                        'debe'                => '0',
                        'haber'               => $subtotal0,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);
                } else {
                    $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => '4.1.01.01',
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => $fecha_as,
                        'debe'                => '0',
                        'haber'               => $subtotal0,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);
                }
            }

            if ($descuento > 0) {

                $plan_cuentas = Plan_Cuentas::where('id', '4.1.06.01')->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => '4.1.06.01',
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'haber'               => '0',
                    'debe'                => $descuento,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            $forma_pago = [];
            if ($datosEnviados != '') {
                $datosEnviados = json_decode($datosEnviados);
                if (isset($datosEnviados->billingParameters->infoAdicional) && $datosEnviados->billingParameters->infoAdicional != '') {
                    $cont = 0;
                    foreach ($datosEnviados->billingParameters->infoAdicional as $row) {
                        if ($row->key == 'FORMA_PAGO') {
                            $forma_pago[$cont] = $row->value;

                            $cont++;
                        }
                    }
                }
            }
            $arr_p       = [];
            $total_pagos = 0;
            for ($i = 0; $i < count($forma_pago); $i++) {
                $cadena = $forma_pago[$i];
                $buscar = "PENDIENTE PAGO";
                $resultado = strpos($cadena, $buscar);
                if ($resultado !== FALSE) {
                    $dformaPago = explode(':', $forma_pago[$i]);
                    Ct_Forma_Pago::create([
                        'id_ct_ventas'    => $id_venta,
                        'tipo'            => 7,
                        'fecha'           => $fecha_as,
                        'tipo_tarjeta'    => null,
                        'numero'          => null,
                        'banco'           => null,
                        'cuenta'          => null,
                        'giradoa'         => null,
                        'valor'           => $dformaPago[1],
                        'valor_base'      => $dformaPago[1],
                        'id_usuariocrea'  => 'FACELECTRO',
                        'id_usuariomod'   => 'FACELECTRO',
                        'ip_creacion'     => $_SERVER["REMOTE_ADDR"],
                        'ip_modificacion' => $_SERVER["REMOTE_ADDR"],

                    ]);
                } else {
                }
            }
            $erf = EmisionDocumentosController::crearComprobante($nfactura, $data, $arr_p, $id_venta, $id_empresa, $total_pagos);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            $arrayError = [
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ];
            echo json_encode($arrayError);
            return false;
        }
    }
    public static function crearComprobante($nfactura, $data, $array_pagos, $factura_id, $id_empresa, $total_pagos)
    {
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = 'FACELECTRO';
        $contador_ctv   = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->get()->count();
        $numero_factura = 0;
        if (is_null($nfactura)) {
            $nfactura = $contador_ctv;
            if ($contador_ctv == 0) {
                //return 'No Retorno nada';
                $num            = '1';
                $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            } else {
                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id    = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->lastest()->first();
                $secuencia = intval($max_id->secuencia);
                if (strlen($secuencia) < 10) {
                    $nu             = $secuencia + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }
            }
            $nfactura = $numero_factura;
        }
        $objeto_validar = new Validate_Decimals();
        $id_comprobante = 0;
        if (sizeOf($array_pagos) > 0) {

            // yo ya tengo el numero de factura
            //falta el total del metodo de pago

            $input_cabecera = [
                'observacion'     => 'COMPROBANTE DE INGRESO FACT:' . $nfactura . ' POR LA CANTIDAD DE ' . $objeto_validar->set_round($total_pagos),
                'fecha_asiento'   => date('Y-m-d'),
                'fact_numero'     => $nfactura,
                'valor'           => $objeto_validar->set_round($total_pagos),
                'id_empresa'      => $id_empresa,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];

            //1.01.02.05.01
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => '1.01.02.05.01',
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => date('Y-m-d'),
                'haber'               => $objeto_validar->set_round($total_pagos),
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            $input_comprobante = [
                'observaciones'       => 'COMPROBANTE DE INGRESO REF: ' . $nfactura . ' POR LA CANTIDAD DE ' . $total_pagos,
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => date('Y-m-d'),
                'secuencia'           => $nfactura,
                'divisas'             => '1',
                'id_empresa'          => $id_empresa,
                'total_ingreso'       => $objeto_validar->set_round($total_pagos),
                'id_cliente'          => $data['cliente']['cedula'],
                'autollenar'          => "Cancela FV : " . $nfactura,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];
            $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
            //formas de pago detalle
            foreach ($array_pagos as $valor) {
                $val = "";
                if (!is_null($valor['valor'])) {
                    $val = $valor['valor'];
                } else {
                    $val = $valor['valor_base'];
                }
                $fecha_pago = $valor['fecha_pago'] != "" ? $valor['fecha_pago'] : date('Y-m-d');
                Ct_Detalle_Pago_Ingreso::create([
                    'id_comprobante'  => $id_comprobante,
                    'fecha'           => $fecha_pago,
                    'numero'          => $valor['numero_pago'],
                    'id_banco'        => $valor['id_banco_pago'],
                    'id_tipo_tarjeta' => $valor['tipo_tarjeta'],
                    'id_tipo'         => $valor['id_tip_pago'],
                    'total'           => $val,
                    'cuenta'          => $valor['id_cuenta_pago'],
                    'girador'         => $valor['giradoa'],
                    'estado'          => '1',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }
            //contador_a son las faturas en este caso es 1
            //for($i=0;$i<$request['contador_a'];$i++){

            //   if (!is_null($request['abono_a'.$i])) {
            //if($request['id_cliente']!=null){
            //$nuevo_saldof= $objeto_validar->set_round($request['abono_a'.$i]);
            $desc_cuenta = Plan_Cuentas::where('id', '1.01.01.1.01')->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => '1.01.01.1.01',
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => date('Y-m-d'),
                'debe'                => $total_pagos, //$nuevo_saldof,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            //}
            $consulta_venta    = null;
            $input_comprobante = null;

            if (floatval($total_pagos) > 0) {
                Ct_Detalle_Comprobante_Ingreso::create([
                    'id_comprobante'    => $id_comprobante,
                    'fecha'             => date('Y-m-d'),
                    'observaciones'     => "Cancela FV : " . $nfactura,
                    'id_factura'        => $factura_id, ////$consulta_venta->id,
                    'secuencia_factura' => $nfactura, //$request['numero'.$i],
                    'total_factura'     => $data['total_factura'], //$request['saldo_a'.$i],
                    'total'             => $total_pagos, //$request['abono_a'.$i],
                    'estado'            => '1',
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                ]);
            }
            if ($total_pagos < $data['total_factura']) {
                //aqui creo una orden de venta nueva

            }

            //$orden_id = $this->ordenVenta($request['id_venta'], $request);

            $valor_actual = Ct_ventas::where('id', $factura_id)->first();
            $id__ventas   = [

                'valor_contable' => floatVal($valor_actual->valor_contable) - $total_pagos,
                'estado_pago'    => 2,
            ];

            Ct_ventas::where('id', $factura_id)->update($id__ventas);
            $consulta_venta  = null;
            $input_actualiza = null;

            return $id__ventas;
        } else {
            return 0;
        }
    }
    public function getRetencion($dinfo = null, $idUsuario = 'FACELECTRO', $verificar = [])
    {
        $esVerificacion = count($verificar) > 0 && isset($verificar['id']);
        $idRetencion = $esVerificacion ? $verificar['id'] : '';
        $data = [];
        $id_doc = 0;
        try {
            if (is_null($dinfo)) {
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                $cabecera = Ct_Retenciones::getRetenciones($idRetencion);
                if (count($cabecera) > 0) {
                    if (!$esVerificacion && !$this->consultarEstadoSRI($cabecera[0]->id_empresa)) {
                        $arrayLog = [
                            'id_de_documentos_electronicos' => $id_doc,
                            'descripcion_error' => json_encode(['Error:' => 'SRI sin servicio']),
                            'id_usuariomod' => $idUsuario,
                            'id_usuariocrea' =>  $idUsuario,
                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                            'id_documento' => $id_doc
                        ];
                        De_Log_Error::setErrorLog($arrayLog);
                        $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $arrayLog], 'message' => 'Error');
                        echo json_encode($result);
                        return;
                    } else {
                        $cont = 0;
                        foreach ($cabecera as $row) {
                            $cont++;
                            $empresa = Empresa::getEmpresa($row->id_empresa);
                            $email = '';
                            $telefono = '';
                            $direccion = '';
                            if ($empresa != '') {
                                $deEmpresa = De_Empresa::getDatos($row->id_empresa);
                                $firma['ruta_firma'] = $deEmpresa->ruta_firma;
                                $firma['clave_firma'] = $deEmpresa->clave_firma;
                                $data['datosfirma'] = $firma;
                                $infoTributaria['fecha_emision'] = date('d/m/Y', strtotime($row->fecha));
                                $infoTributaria['ambiente'] = $deEmpresa->ambiente;
                                $infoTributaria['tipoEmision'] = 1;
                                $infoTributaria['razonSocial'] = str_replace('&', 'Y', $empresa->razonsocial);
                                $infoTributaria['nombreComercial'] = str_replace('&', 'Y', $empresa->nombrecomercial);
                                $infoTributaria['ruc'] = $empresa->id;
                                $infoTributaria['codDoc'] = '07';
                                $infoTributaria['estab'] = str_pad($row->sucursal, 3, 0, STR_PAD_LEFT);
                                $subString = explode('-', $row->punto_emision);
                                if (count($subString) == 2) {
                                    $infoTributaria['ptoEmi'] = str_pad($subString[1], 3, 0, STR_PAD_LEFT);
                                } else {
                                    $infoTributaria['ptoEmi'] = str_pad($subString[0], 3, 0, STR_PAD_LEFT);
                                }
                                $infoTributaria['dirMatriz'] = $empresa->direccion;
                                $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                                $datosTributarios = $esVerificacion
                                    ? De_Info_Tributaria::getDatos($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc)
                                    : De_Info_Tributaria::reservarSecuencial($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                                if ($datosTributarios == '') {
                                    $result = array('code' => 204, 'state' => true, 'data' => ['error:' => 'no se ha configurado los datos tributarios para esta empresa: ' . $empresa->id], 'message' => 'no existen datos tributarios para esta empresa');
                                    echo json_encode($result);
                                    return;
                                }
                                $secuencial = $esVerificacion
                                    ? ((int) $datosTributarios->secuencial_nro + 1)
                                    : $datosTributarios->secuencial_nro;
                                $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                                $claveObj = new ClaveAcceso();
                                $campos = [
                                    'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($row->fecha))),
                                    'tipo_comprobante' => $infoTributaria['codDoc'],
                                    'ruc' => $infoTributaria['ruc'],
                                    'tipo_ambiente' => $infoTributaria['ambiente'],
                                    'punto_establecimiento' => $infoTributaria['estab'],
                                    'punto_emision' => $infoTributaria['ptoEmi'],
                                    'numero_comprobante' => $infoTributaria['secuencial'],
                                    'codigo_numerico' => $infoTributaria['secuencial'],
                                    'tipo_emision' => $infoTributaria['tipoEmision']
                                ];
                                $claveObj->llenarDatos($campos);
                                $infoTributaria['claveAcceso'] = $claveObj->clave_acceso;
                                $data['infoTributaria'] = $infoTributaria;
                                $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                                    ->where('codigo_sucursal', $infoTributaria['estab'])->first();
                                $caja = Ct_Caja::where('id_sucursal', $sucursal->id)
                                    ->where('codigo_caja', $infoTributaria['ptoEmi'])->first();
                                $infoCompRetencion['fechaEmision'] = $row->fecha;
                                if (isset($caja->sucursal->direccion_sucursal)) {
                                    $infoCompRetencion['dirEstablecimiento'] = $caja->sucursal->direccion_sucursal;
                                } else {
                                    $infoCompRetencion['dirEstablecimiento'] = $empresa->direccion;
                                }
                                $infoCompRetencion['contribuyenteEspecial'] = null; //Opcional   
                                if ($deEmpresa->contribuyente_especial != 0)
                                    $infoCompRetencion['contribuyenteEspecial'] = $deEmpresa->contribuyente_especial;
                                $infoCompRetencion['obligadoContabilidad'] = 'NO';
                                if ($deEmpresa->contabilidad == 1)
                                    $infoCompRetencion['obligadoContabilidad'] = 'SI';
                                $compras = Ct_compras::where('id', $row->id_compra)->first();
                                if (!is_null($compras)) {
                                    $proveedor = Proveedor::where('id', $compras->proveedor)->first();
                                    if (!is_null($proveedor)) {
                                        $email = $proveedor->email;
                                        $telefono = $proveedor->telefono1;
                                        $direccion = $proveedor->direccion;
                                        $infoCompRetencion['tipoIdentificacionSujetoRetenido'] = str_pad($proveedor->tipo, 2, 0, STR_PAD_LEFT);
                                        $infoCompRetencion['razonSocialSujetoRetenido'] = str_replace('&', 'Y', $proveedor->razonsocial);
                                        $infoCompRetencion['identificacionSujetoRetenido'] = $proveedor->id;
                                        if ($infoCompRetencion['tipoIdentificacionSujetoRetenido'] == '08') {
                                            $infoCompRetencion['tipoSujetoRetenido'] = '03'; //compras - identificacion del exterior
                                        }
                                    } else {
                                        $infoCompRetencion['tipoIdentificacionSujetoRetenido'] = '';
                                        $infoCompRetencion['razonSocialSujetoRetenido'] = '';
                                        $infoCompRetencion['identificacionSujetoRetenido'] = '';
                                    }
                                } else {
                                    $proveedor = Proveedor::where('id', $row->id_proveedor);
                                    $infoCompRetencion['tipoIdentificacionSujetoRetenido'] = str_pad($proveedor->tipo, 2, 0, STR_PAD_LEFT);
                                    $infoCompRetencion['razonSocialSujetoRetenido'] = $this->sanear_string(str_replace('&', 'Y', $proveedor->razonsocial));
                                    $infoCompRetencion['identificacionSujetoRetenido'] = $proveedor->id;
                                    if ($infoCompRetencion['tipoIdentificacionSujetoRetenido'] == '08') {
                                        $infoCompRetencion['tipoSujetoRetenido'] = '03'; //compras - identificacion del exterior
                                    }
                                }
                                $infoCompRetencion['parteRel'] = 'NO';
                                $infoCompRetencion['periodoFiscal'] = date('Y-m-d', strtotime($infoCompRetencion['fechaEmision']));
                                $data['infoCompRetencion'] = $infoCompRetencion;
                                //DocsSustento
                                $docsSustento = [];
                                $docSustento = [];
                                $conDetalle = 0;
                                //if (count($detalleReten) > 0) {
                                //foreach ($detalleReten as $deta) {
                                $docSustento['codSustento'] = '01';
                                if ($compras->tipo_comprobante != '03')
                                    $docSustento['codDocSustento'] = '18';
                                else
                                    $docSustento['codDocSustento'] = '03';
                                $datoDocSus = explode('-', $compras->numero);
                                $numDocSus = $datoDocSus[0] . $datoDocSus[1] . str_pad((int)$datoDocSus[2], 9, 0, STR_PAD_LEFT);
                                $docSustento['numDocSustento'] = $numDocSus;
                                $docSustento['fechaEmisionDocSustento'] = $compras->fecha;
                                //$docSusteneto['fechaRegistroContable'] = '';//opcional
                                //$docSusteneto['numAutDocSustento'] = '';//opcional
                                $docSustento['pagoLocExt'] = '01'; //Pago a residente / Establecimiento permanente  -- Pago a no residente
                                if ($docsSustento['pagoLocExt'] == '02') {
                                    $docSustento['tipoRegi'] = '01'; //obligatorio pagoLocExt sea 02/tabla 19 
                                    $docSustento['paisEfecPago'] = '593'; //obligatorio pagoLocExt sea 02
                                    $docSustento['aplicConvDobTrib'] = 'NO'; //obligatorio pagoLocExt sea 02
                                    $docSustento['pagExtSujRetNorLeg'] = 'NO'; //obligatorio aplicConvDobTrib sea NO
                                    $docSustento['pagoRegFis'] = 'NO'; //obligatorio pagoLocExt sea 02
                                }
                                //$docSustento['totalComprobantesReembolso'] = '';//Obligatorio, si <codDocSustento> es igua a 41, corresponde a la suma de <totalBaseImponibleReembolso> y <totalImpuestoReembolso>
                                //$docSustento['totalBaseImponibleReembolso'] = '';//Obligatorio, si <codDocSustento> es igua a 41,  corresponde a la sumatoria de los tags <baseImponibleReembolso>, el cual es mayor o igual a la sumatoria
                                //$docSustento['totalImpuestoReembolso'] = 'SI';//Obligatorio, si <codDocSustento> es igua a 41,  corresponde a la sumatoria de los tags <impuestoReembolso>, el cual es mayor o igual a la sumatoria
                                $docSustento['totalSinImpuestos'] = $this->getDecimal(($compras->subtotal_0 != null ? $compras->subtotal_0 : 0) + ($compras->subtotal_12 != null ? $compras->subtotal_12 : 0), 2);
                                $docSustento['importeTotal'] = $compras->total_final;
                                //impuestos
                                $impuestoDocSustento = [];
                                $impuestosDocSustento = [];
                                $detalleCompra = Ct_detalle_compra::where('id_ct_compras', $compras->id)->get();
                                if (count($detalleCompra) > 0) {
                                    foreach ($detalleCompra as $key => $impuesto) {
                                        if ($impuesto->iva == 1) {
                                            $impuestoDocSustento['codImpuestoDocSustento'] = '2';
                                            $impuestoDocSustento['codigoPorcentaje'] = 2; //!EmisionDocumentosController::validarPeriodo($row->fecha) ? 2 : 4;
                                            $impuestoDocSustento['baseImponible'] = $impuesto->extendido;
                                            $impuestoDocSustento['tarifa'] = !EmisionDocumentosController::validarPeriodo($row->fecha) ? 12 : ($this->porIva->iva * 100);
                                            $impuestoDocSustento['valorImpuesto'] = !EmisionDocumentosController::validarPeriodo($row->fecha) ? $this->getDecimal(($impuesto->extendido * 0.12), 2) : $this->getDecimal(($impuesto->extendido * $this->porIva->iva), 2);
                                        } else {
                                            $impuestoDocSustento['codImpuestoDocSustento'] = '2';
                                            $impuestoDocSustento['codigoPorcentaje'] = '0';
                                            $impuestoDocSustento['baseImponible'] = $impuesto->extendido;
                                            $impuestoDocSustento['tarifa'] = '0';
                                            $impuestoDocSustento['valorImpuesto'] = $this->getDecimal(0, 2);
                                        }
                                        if ($impuesto->ice != '') {
                                            $impuestoDocSustento['codImpuestoDocSustento'] = '3';
                                            $impuestoDocSustento['codigoPorcentaje'] = '2'; //codigo retencion
                                            $impuestoDocSustento['baseImponible'] = $impuesto->precio;
                                            $impuestoDocSustento['tarifa'] = '0'; //porcentaje retencion
                                            $impuestoDocSustento['valorImpuesto'] = $this->getDecimal(($impuesto->precio * ($impuestoDocSustento['tarifa'] / 100)), 2);
                                        }
                                        $impuestosDocSustento[$key] = $impuestoDocSustento;
                                    }
                                }
                                $docSustento['impuestosDocSustento'] = $impuestosDocSustento;
                                $retenciones = []; //opcional
                                $retencion = [];
                                $detalleReten = Ct_detalle_retenciones::getDetalles($row->id);
                                if (count($detalleReten) > 0) {
                                    $contRet = 0;
                                    foreach ($detalleReten as $deta) {
                                        $porcentajeRetencion = Ct_Porcentaje_Retenciones::where('id', $deta->id_porcentaje)->first([
                                            'tipo as codigo_impuesto',
                                            'codigo_interno as codigo',
                                            'valor as porcentaje_retencion'
                                        ]);

                                        $porRete = $porcentajeRetencion->porcentaje_retencion;
                                        $porcentajeRetencion = De_Codigo_Impuestos::where('codigo', $porcentajeRetencion->codigo)
                                            ->where('porcentaje_retencion', $porcentajeRetencion->porcentaje_retencion)
                                            ->first();
                                        if ($porcentajeRetencion == '') {
                                            $porcentajeRetencion = De_Codigo_Impuestos::where('porcentaje_retencion', number_format($porRete, 2))
                                                ->first();
                                        } /*else {
                                            
                                        }*/
                                        //echo '2<pre>';print_r($porcentajeRetencion);exit;
                                        $retencion['codigo'] = $porcentajeRetencion->codigo_impuesto;
                                        $retencion['codigoRetencion'] = $porcentajeRetencion->codigo;
                                        $retencion['baseImponible'] = $deta->base_imponible;
                                        $retencion['porcentajeRetener'] = $porcentajeRetencion->porcentaje_retencion;
                                        $retencion['valorRetenido'] = ($deta->base_imponible * $porcentajeRetencion->porcentaje_retencion) / 100;

                                        if ($docSustento['codDocSustento'] == 10) {
                                            $dividendos = []; //Obligatorio cuando el tag <codSustento> es igual a 10.
                                            $dividendos['fechaPagoDiv'] = ''; //Obligatorio cuando el tag <codSustento> es igual a 10.
                                            $dividendos['imRentaSoc'] = ''; //Obligatorio cuando el tag <codSustento> es igual a 10.
                                            $dividendos['ejerFisUtDiv'] = ''; //Obligatorio cuando el tag <codSustento> es igual a 10.    
                                            $retencion['dividendos'] = $dividendos; //Obligatorio cuando el tag <codSustento> es igual a 10.
                                        }
                                        // $compraCajBanano = []; //OPCIONAL
                                        // $compraCajBanano['NumCajBan'] = '';
                                        // $compraCajBanano['PrecCajBan'] = '';
                                        // $retencion['compraCajBanano'] = $compraCajBanano;
                                        $retenciones[$contRet] = $retencion;
                                        if (!is_null($compras)) {
                                            $numDocSus = str_replace('-', '', $compras->numero);
                                            $impuesto['numDocSustento'] = $numDocSus;
                                            $impuesto['fechaEmisionDocSustento'] = $compras->fecha;
                                        }
                                        $impuestos[$conDetalle] = $impuesto;
                                        $conDetalle++;
                                        $contRet++;
                                    }
                                }
                                // }
                                $docSustento['retenciones'] = $retenciones;
                                if ($docSustento['codDocSustento'] == 41) {
                                    $reembolso = []; //Obligatorio cuando <codDocSustento> sea igual a 41
                                    $reembolsoDetalle = [];
                                    $reembolsoDetalle['tipoIdentificacionProveedorReembolso'] = $proveedor->tipo; //Obligatorio cuando <codDocSustento> sea igual a 41
                                    $reembolsoDetalle['identificacionProveedorReembolso'] =  $proveedor->id; //Obligatorio cuando <codDocSustento> sea igual a 41
                                    $reembolsoDetalle['codPaisPagoProveedorReembolso'] =  '593'; //Obligatorio cuando <codDocSustento> sea igual a 41
                                    $reembolsoDetalle['tipoProveedorReembolso'] =  '02'; //Obligatorio cuando <codDocSustento> sea igual a 41/01-persona natural; 02 - sociedad 
                                    $reembolsoDetalle['codDocReembolso'] =  '01'; //Obligatorio cuando <codDocSustento> sea igual a 41
                                    $reembolsoDetalle['estabDocReembolso'] =  ''; //Obligatorio cuando <codDocSustento> sea igual a 41
                                    $reembolsoDetalle['ptoEmiDocReembolso'] =  ''; //Obligatorio cuando <codDocSustento> sea igual a 41
                                    $reembolsoDetalle['secuencialDocReembolso'] =  ''; //Obligatorio cuando <codDocSustento> sea igual a 41
                                    $reembolsoDetalle['fechaEmisionDocReembolso'] =  ''; //Obligatorio cuando <codDocSustento> sea igual a 41
                                    $reembolsoDetalle['numeroAutorizacionDocReemb'] =  ''; //Obligatorio cuando <codDocSustento> sea igual a 41    
                                    $detalleImpuestos = [];
                                    $detalleImpuesto = [];
                                    $detalleReten = Ct_detalle_retenciones::getDetalles($row->id);
                                    if (count($detalleReten) > 0) {
                                        foreach ($detalleReten as $deta) {
                                            $porcentajeRetencion = Ct_Porcentaje_Retenciones::where('id', $deta->id_porcentaje)->first();
                                            if ($porcentajeRetencion->tipo == 2) {
                                                $detalleImpuesto['codigo'] = '1'; //RENTA
                                            } elseif ($porcentajeRetencion->tipo == 1) {
                                                $detalleImpuesto['codigo'] = '2'; //IVA
                                            } else {
                                                $detalleImpuesto['codigo'] = '6'; //ISD
                                            }
                                            $detalleImpuesto['codigoPorcentaje'] = $porcentajeRetencion->codigo;
                                            $detalleImpuesto['baseImponibleReembolso'] = $deta->base_imponible;
                                            $detalleImpuesto['tarifa'] = $porcentajeRetencion->valor;
                                            $detalleImpuesto['impuestoReembolso'] = $deta->totales;
                                            /*$impuestoDocSustento['codDocSustento'] = '01'; //al momento de desarrollo solo se realizan retenciones x facturas
                                                    if (!is_null($compras)) {
                                                        $numDocSus = str_replace('-', '', $compras->numero);
                                                        $impuestoDocSustento['numDocSustento'] = $numDocSus;
                                                        $impuestoDocSustento['fechaEmisionDocSustento'] = $compras->fecha;
                                                    }*/
                                            $detalleImpuestos[$conDetalle] = $detalleImpuesto;
                                            $conDetalle++;
                                        }
                                        $docSustento['impuestosDocSustento'] = $impuestosDocSustento;
                                        $reembolsoDetalle['detalleImpuestos'] = $detalleImpuestos;
                                        $reembolso[0] = $reembolsoDetalle;
                                        $docSusteneto['reembolso'] = $reembolso;
                                    }
                                }
                                $pagos = [];
                                $pago = [];
                                $pago['formaPago'] = '20';
                                $pago['total'] = $compras->total_final;
                                $pagos[0] = $pago;
                                $docSustento['pagos'] = $pagos;
                                $docsSustento[0] = $docSustento;
                                $data['docsSustento'] = $docsSustento;
                                //$conDetalle++;
                                //}
                                //}
                                $campoAdicional['nombre'] = 'email';
                                if ($email != '') {
                                    $campoAdicional['valor']  = $email;
                                } else {
                                    $campoAdicional['valor']  = 'no tiene';
                                }
                                $informacion_adicional[0] = $campoAdicional;
                                $campoAdicional['nombre'] = 'telefono';
                                if ($telefono != '') {
                                    $campoAdicional['valor'] = $telefono;
                                } else {
                                    $campoAdicional['valor']  = 'no tiene';
                                }
                                $informacion_adicional[1] = $campoAdicional;
                                $campoAdicional['nombre'] = 'direccion';
                                if ($direccion != '') {
                                    $campoAdicional['valor'] = $direccion;
                                } else {
                                    $campoAdicional['valor']  = 'no tiene';
                                }
                                $informacion_adicional[2] = $campoAdicional;
                                $data['infoAdicional'] = $informacion_adicional;
                            } else {
                                $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existe la empresa');
                                echo json_encode($result);
                                return;
                            }
                            $errores = $this->validarData($data);
                            if ($esVerificacion) {
                                return [
                                    'data' => $data,
                                    'errors' => $errores,
                                ];
                            }
                            /*echo '<pre>';
                            print_r($data);
                            print_r($errores);
                            exit;*/
                            /*if ($empresa->id == '1793135579001') {
                                echo '<pre>';
                                print_r($data);
                                print_r($errores);
                                exit;
                            }*/

                            if (count($errores) > 0) {
                                $arrayDocElec = [
                                    'id_de_pasos' => 7,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoCompRetencion' => json_encode($data['infoCompRetencion']),
                                    'impuestos' => json_encode($data['impuestos']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' => $infoCompRetencion['identificacionSujetoRetenido'],
                                    'ruc_emisor' => $infoTributaria['ruc'],
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                    'id_documento' => $row->id,
                                ];
                                $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayDocElec, 7);
                                Ct_Retenciones::updateSinGenerarXML($row->id, $data['infoTributaria']['claveAcceso']);
                                $idInsertados .= $id_doc . ',';
                                $arrayLog = [
                                    'id_de_documentos_electronicos' => $id_doc,
                                    'descripcion_error' => json_encode($errores),
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'id_documento' => $row->id
                                ];
                                De_Log_Error::setErrorLog($arrayLog);
                            } else {
                                //Creacion XML
                                $arrayLogError = [
                                    'id_de_pasos' => 1,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoCompRetencion' => json_encode($data['infoCompRetencion']),
                                    'impuestos' => json_encode($data['impuestos']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' => $infoCompRetencion['identificacionSujetoRetenido'],
                                    'ruc_emisor' => $infoTributaria['ruc'],
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                    'id_documento' => $row->id
                                ];
                                $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayLogError, 1);
                                if (is_numeric($id_doc)) {
                                    $idInsertados .= $id_doc . ',';
                                    $comprobante = $this->generarDocElectronicoXml($data);
                                    /*header('Content-type: text/xml');
                                    echo $comprobante;
                                    exit;*/
                                    /*if ($empresa->id == '0993069299001') {
                                        header('Content-type: text/xml');
                                        echo $comprobante;
                                        echo '<pre>';
                                        print_r($data);
                                        exit;
                                    }*/
                                    Ct_Retenciones::updateGenerarXML($row->id, $data['infoTributaria']['claveAcceso']);
                                    $arrayDato = [
                                        'tipo_documento' => $infoTributaria['codDoc'],
                                        'id_pasos' => 1,
                                        'id_documento' => $row->id,
                                        'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                        'informacion' => 'Documento generado',
                                        'id_usuariomod' => $idUsuario,
                                        'id_usuariocrea' => $idUsuario,
                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                        'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                    ];
                                    Ct_Retenciones::setLogDocElectronico($arrayDato);
                                    if ($comprobante != '') {
                                        $docAnte = simplexml_load_string($comprobante);
                                        if (!empty($docAnte)) {
                                            //validar xsd
                                            $validacion = $this->validarXmlToXsd($comprobante); //$this->ValidarFuera($comprobante, $this->tipoDocumento);
                                            /*if ($empresa->id == '0993075000001') {
                                                echo '<pre>';
                                                print_r($validacion);
                                                print_r($data);
                                                exit;
                                            }*/
                                            //dd($validacion);
                                            //1: ok; 0: error
                                            if ($validacion['isValidoXsd'] == 0) {
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($validacion),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $result = array('code' => 204, 'state' => true, 'data' => json_encode($validacion), 'message' => 'ok|Error al validar XML con XSD');
                                                echo json_encode($result);
                                                return;
                                            }
                                            De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                            Ct_Retenciones::updateValidacionXSD($row->id);
                                            $arrayDato = [
                                                'tipo_documento' => $infoTributaria['codDoc'],
                                                'id_pasos' => 2,
                                                'id_documento' => $row->id,
                                                'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                'informacion' => 'Documento validado por XSD',
                                                'id_usuariomod' => $idUsuario,
                                                'id_usuariocrea' => $idUsuario,
                                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                            ];
                                            Ct_Retenciones::setLogDocElectronico($arrayDato);
                                            //firmar xml
                                            $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $deEmpresa->ruta_firma; //firma.p12
                                            //$ruta_p12 = $deEmpresa->ruta_firma; //firma.p12
                                            $password = $deEmpresa->clave_firma; //'3duard0faustos';
                                            $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                                            if ($result != '') {
                                                $docAnte = simplexml_load_string($comprobante);
                                                $dom = new DOMDocument();
                                                $dom->loadXML($result);
                                                $this->crearcarpeta(base_path() . '/storage/app/facturaelectronica/firmados/' . $docAnte->infoTributaria->ruc . '/' . $this->tipoDocumento . '/');
                                                $dom->save(base_path() . '/storage/app/facturaelectronica/firmados/' . $docAnte->infoTributaria->ruc . '/' . $this->tipoDocumento . '/' . $this->tipoDocumento . '-' . $docAnte->infoTributaria->estab . '-' . $docAnte->infoTributaria->ptoEmi . '-' . $docAnte->infoTributaria->secuencial . '.xml');
                                                De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                                Ct_Retenciones::updateXmlFirmado($row->id);
                                                $arrayDato = [
                                                    'tipo_documento' => $infoTributaria['codDoc'],
                                                    'id_pasos' => 3,
                                                    'id_documento' => $row->id,
                                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                    'informacion' => 'Documento firmado',
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' => $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                ];
                                                Ct_Retenciones::setLogDocElectronico($arrayDato);
                                                //recepcion sri
                                                $respuesta = $this->recibirWs($result, $data['infoTributaria']['ambiente']);
                                                if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                                    //$this->procesarRespuestaXml($respuesta);
                                                    De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                                    Ct_Retenciones::updateRecibidoSri($row->id);
                                                    $arrayDato = [
                                                        'tipo_documento' => $infoTributaria['codDoc'],
                                                        'id_pasos' => 4,
                                                        'id_documento' => $row->id,
                                                        'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                        'informacion' => 'Documento recibido',
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' => $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    Ct_Retenciones::setLogDocElectronico($arrayDato);
                                                    $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                                    $secuancialDocumento = $data['infoTributaria']['secuencial'];
                                                    De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                    //autorzacion sri
                                                    $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso, $data['infoTributaria']['ambiente']);
                                                    /*if ($empresa->id == '0993075000001') {
                                                        echo '<pre>';
                                                        print_r($respuestaAutorizacion);
                                                        exit;
                                                    }*/
                                                    $estadoAutorizacion = $this->generarXmlAutorizacion($respuestaAutorizacion);
                                                    if ($estadoAutorizacion == 'AUTORIZADO') {
                                                        De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                                        Ct_Retenciones::updateXmlAutorizacion($row->id, $docAnte->infoTributaria->claveAcceso, $secuancialDocumento, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi']);
                                                        $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => 5,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Documento autorizado',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR']
                                                        ];
                                                        Ct_Retenciones::setLogDocElectronico($arrayDato);
                                                        $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
                                                        $param = [
                                                            'email' => $proveedor->email != '' ? $proveedor->email : $proveedor->email2,
                                                            'nombre' => $proveedor->razonsocial != '' ? $proveedor->razonsocial : $proveedor->nombrecomercial,
                                                            'claveAcceso' => $docAnte->infoTributaria->claveAcceso,
                                                            'estab' => $data['infoTributaria']['estab'],
                                                            'ptoEmi' =>  $data['infoTributaria']['ptoEmi'],
                                                            'secuencial' =>  $data['infoTributaria']['secuencial'],
                                                            'tipoDoc' => $data['infoTributaria']['codDoc'],
                                                            'fechaEmision' => $data['infoTributaria']['fecha_emision'],
                                                        ];
                                                        $this->enviar_correo($param);
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => 11,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Documento notificado',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        Ct_Retenciones::setLogDocElectronico($arrayDato);
                                                    } else {
                                                        De_Documentos_Electronicos::updateXmlNoAutorizacion($comprobante, $respuestaAutorizacion);
                                                        Ct_Retenciones::updateXmlNoAutorizacion($row->id);
                                                        $arrayLog = [
                                                            'id_de_documentos_electronicos' => $id_doc,
                                                            'descripcion_error' => json_encode($respuestaAutorizacion),
                                                            'id_documento' => $row->id,
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' =>  $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        De_Log_Error::setErrorLog($arrayLog);
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => 10,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Documento no autorizado',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        Ct_Retenciones::setLogDocElectronico($arrayDato);
                                                    }
                                                } else {
                                                    $this->generarXmlRespuesta($respuesta);
                                                    De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                                    Ct_Retenciones::updateXmlNoRecepcion($row->id);
                                                    $arrayLog = [
                                                        'id_de_documentos_electronicos' => $id_doc,
                                                        'descripcion_error' => json_encode($respuesta),
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' =>  $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    De_Log_Error::setErrorLog($arrayLog);
                                                    //return $respuesta['mensajesWs'];
                                                    $arrayDato = [
                                                        'tipo_documento' => $infoTributaria['codDoc'],
                                                        'id_pasos' => 9,
                                                        'id_documento' => $row->id,
                                                        'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                        'informacion' => 'Documento devuelto',
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' => $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    Ct_Retenciones::setLogDocElectronico($arrayDato);
                                                }
                                            } else {
                                                //Log error al firmar XML
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($result),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $arrayDato = [
                                                    'tipo_documento' => $infoTributaria['codDoc'],
                                                    'id_pasos' => 6,
                                                    'id_documento' => $row->id,
                                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                    'informacion' => 'Documento sin firmar',
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' => $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                ];
                                                Ct_Retenciones::setLogDocElectronico($arrayDato);
                                            }
                                        }
                                    } else {
                                        //log No genero XML
                                        $arrayLog = [
                                            'id_de_documentos_electronicos' => $id_doc,
                                            'descripcion_error' => 'Error al crear el documento XML',
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' =>  $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                        ];
                                        De_Log_Error::setErrorLog($arrayLog);
                                        $arrayDato = [
                                            'tipo_documento' => $infoTributaria['codDoc'],
                                            'id_pasos' => 7,
                                            'id_documento' => $row->id,
                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                            'informacion' => 'Documento sin generar xml',
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' => $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                        ];
                                        Ct_Retenciones::setLogDocElectronico($arrayDato);
                                    }
                                } else {
                                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'error|' . $id_doc);
                                    echo json_encode($result);
                                    return;
                                }
                            }
                        }
                    }
                } else {
                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existen documentos para emitir', 'tipoDoc' => 'retenciones');
                    echo json_encode($result);
                    return;
                }
                $infoErrores = De_Documentos_Electronicos::revisionEstadosDocumentosErrores($idInsertados);
                $infoAutorizados = '';
                if ($claveInsertados != '')
                    $infoAutorizados = De_Documentos_Electronicos::revisionEstadosDocumentosAutorizados($claveInsertados);
                $result = array('code' => 200, 'state' => true, 'data' => ['errores:' => $infoErrores, 'autorizado:' => $infoAutorizados], 'message' => 'proceso ejecutado correctamente', 'tipoDoc' => 'retenciones');
                echo json_encode($result);
            } else {
                echo 'a terceros';
            }
        } catch (Exception $ex) {
            if ($id_doc != 0) {
                $arrayLog = [
                    'id_de_documentos_electronicos' => $id_doc,
                    'descripcion_error' => json_encode($ex->getMessage()),
                    'id_usuariomod' => $idUsuario,
                    'id_usuariocrea' =>  $idUsuario,
                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                ];
                De_Log_Error::setErrorLog($arrayLog);
            }
            $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $ex->getMessage(), 'linea' => $ex->getLine()], 'message' => 'Error');
            echo json_encode($result);
            return;
        }
    }
    public function getNotaCredito($dinfo = null, $idUsuario = 'FACELECTRO', $verificar = [])
    {
        $esVerificacion = count($verificar) > 0 && isset($verificar['id']);
        $idNotaCredito = $esVerificacion ? $verificar['id'] : '';
        $data = [];
        $id_doc = 0;
        $numDocumento = '';
        try {
            if ($dinfo == null) {
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                $notaCreditoCabecera = Ct_Nota_Credito_Clientes::getNotaCredito($idNotaCredito);
                if (count($notaCreditoCabecera) > 0) {
                    if (!$esVerificacion && !$this->consultarEstadoSRI($notaCreditoCabecera[0]->id_empresa)) {
                        $arrayLog = [
                            'id_de_documentos_electronicos' => $id_doc,
                            'descripcion_error' => json_encode(['Error:' => 'SRI sin servicio']),
                            'id_usuariomod' => $idUsuario,
                            'id_usuariocrea' =>  $idUsuario,
                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                            'id_documento' => $id_doc
                        ];
                        De_Log_Error::setErrorLog($arrayLog);
                        $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $arrayLog], 'message' => 'Error');
                        echo json_encode($result);
                        return;
                    } else {
                        $cont = 0;
                        foreach ($notaCreditoCabecera as $row) {
                            $cont++;
                            $empresa = Empresa::getEmpresa($row->id_empresa);
                            if ($empresa != '') {
                                $datosFirma = De_Empresa::getDatos($row->id_empresa);
                                $firma['ruta_firma'] = $datosFirma->ruta_firma;
                                $firma['clave_firma'] = $datosFirma->clave_firma;
                                $data['datosfirma'] = $firma;
                                $infoTributaria['fecha_emision'] = date('d/m/Y', strtotime($row->fecha));
                                $infoTributaria['ambiente'] = 1;
                                $infoTributaria['tipoEmision'] = 1;
                                $infoTributaria['razonSocial'] = $empresa->razonsocial;
                                $infoTributaria['nombreComercial'] = $empresa->nombrecomercial;
                                $infoTributaria['ruc'] = $empresa->id;
                                $infoTributaria['codDoc'] = '04';
                                $infoTributaria['estab'] = str_pad($row->sucursal, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['ptoEmi'] = str_pad($row->punto_emision, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['dirMatriz'] = $empresa->direccion;
                                $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                                if ($idDoc == '') {
                                    $result = array(
                                        'code' => 204,
                                        'state' => true,
                                        'data' => ['error:' => 'No esiste el maestro de documentos para esta empresa: ' . $empresa->id],
                                        'message' => 'no existen el maestro de documentos para esta empresa'
                                    );
                                    echo json_encode($result);
                                    return;
                                }
                                if ($esVerificacion) {
                                    $datosTributarios = De_Info_Tributaria::getDatos($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                                } elseif (strlen((string) $row->nro_autorizacion) === 49 && ctype_digit((string) $row->nro_autorizacion)) {
                                    $datosTributarios = De_Info_Tributaria::sincronizarSecuencialAsignado(
                                        $row->id_empresa,
                                        substr($row->nro_autorizacion, 24, 3),
                                        substr($row->nro_autorizacion, 27, 3),
                                        $idDoc,
                                        substr($row->nro_autorizacion, 30, 9)
                                    );
                                } else {
                                    $datosTributarios = De_Info_Tributaria::reservarSecuencial($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                                }
                                if ($datosTributarios == '') {
                                    $result = array(
                                        'code' => 204,
                                        'state' => true,
                                        'data' => ['error:' => 'no se ha configurado los datos tributarios para esta empresa: ' . $empresa->id],
                                        'message' => 'no existen datos tributarios para esta empresa'
                                    );
                                    echo json_encode($result);
                                    return;
                                }
                                $secuencial = $esVerificacion
                                    ? ((int) $datosTributarios->secuencial_nro + 1)
                                    : $datosTributarios->secuencial_nro;
                                if (strlen((string) $row->nro_autorizacion) === 49 && ctype_digit((string) $row->nro_autorizacion)) {
                                    $infoTributaria['estab'] = substr($row->nro_autorizacion, 24, 3);
                                    $infoTributaria['ptoEmi'] = substr($row->nro_autorizacion, 27, 3);
                                    $secuencial = substr($row->nro_autorizacion, 30, 9);
                                }
                                $infoTributaria['ambiente'] = $datosFirma->ambiente;
                                $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                                $claveObj = new ClaveAcceso();
                                $campos = [
                                    'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($row->fecha))),
                                    'tipo_comprobante' => $infoTributaria['codDoc'],
                                    'ruc' => $infoTributaria['ruc'],
                                    'tipo_ambiente' => $infoTributaria['ambiente'],
                                    'punto_establecimiento' => $infoTributaria['estab'],
                                    'punto_emision' => $infoTributaria['ptoEmi'],
                                    'numero_comprobante' => $infoTributaria['secuencial'],
                                    'codigo_numerico' => $infoTributaria['secuencial'],
                                    'tipo_emision' => $infoTributaria['tipoEmision']
                                ];
                                $claveObj->llenarDatos($campos);
                                $infoTributaria['claveAcceso'] = strlen((string) $row->nro_autorizacion) === 49
                                    ? $row->nro_autorizacion
                                    : $claveObj->clave_acceso;
                                if ($datosFirma->agente_retencion == 1) {
                                    $infoTributaria['agenteRetencion'] = 1;
                                }
                                $data['infoTributaria'] = $infoTributaria;
                                //Info Nota Credito
                                $infoNotaCredito['fechaEmision'] = $row->fecha;
                                $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                                    ->where('codigo_sucursal', $infoTributaria['estab'])
                                    ->first();
                                if (isset($sucursal->direccion_sucursal)) {
                                    $infoNotaCredito['dirEstablecimiento'] = $sucursal->direccion_sucursal;
                                } else {
                                    $infoNotaCredito['dirEstablecimiento'] = $empresa->direccion;
                                }
                                $telefonoCliente = '';
                                $telefonoCliente2 = '';
                                $direccionCliente = '';
                                $emailCliente = '';
                                $cliente = Ct_Clientes::getCliente($row->id_cliente);
                                if ($cliente != '') {
                                    if (isset($cliente->email_representante)) {
                                        $emailCliente = $cliente->email_representante;
                                    }
                                    if (isset($cliente->telefono1_representante)) {
                                        if ($cliente->telefono1_representante != '') {
                                            $telefonoCliente = $cliente->telefono1_representante;
                                        }
                                    }
                                    if (isset($cliente->telefono2_representante)) {
                                        if ($cliente->telefono2_representante != '') {
                                            $telefonoCliente2 = $cliente->telefono2_representante;
                                        }
                                    }
                                    if (isset($cliente->direccion_representante)) {
                                        $direccionCliente = $cliente->direccion_representante;
                                    }
                                    $infoNotaCredito['tipoIdentificacionComprador'] = str_pad($cliente->tipo, 2, 0, STR_PAD_LEFT);
                                    $infoNotaCredito['razonSocialComprador'] = isset($row->nombre_representante) ? str_replace('&', 'Y', $row->nombre_representante) :  str_replace('&', 'Y', $cliente->nombre);
                                    $infoNotaCredito['identificacionComprador'] = $cliente->identificacion;
                                } else {
                                    $infoNotaCredito['tipoIdentificacionComprador'] = null;
                                    $infoNotaCredito['razonSocialComprador'] = null;
                                    $infoNotaCredito['identificacionComprador'] = null;
                                }
                                if ($datosFirma->contribuyente_especial != '' && $datosFirma->contribuyente_especial != 0) {
                                    $infoNotaCredito['contribuyenteEspecial'] = $datosFirma->contribuyente_especial; //Opcional
                                }
                                $infoNotaCredito['obligadoContabilidad'] = 'NO';
                                if ($datosFirma->contabilidad == 1)
                                    $infoNotaCredito['obligadoContabilidad'] = 'SI'; //Opcional

                                if ($datosFirma->rise != '')
                                    $infoNotaCredito['rise'] = $datosFirma->rise; //Opcional

                                $infoNotaCredito['codDocModificado'] = '01';
                                $factura = null;
                                $idFactura = $row->id_factura;
                                $factura = Ct_ventas::find($idFactura);
                                if (!is_null($factura)) {
                                    $infoNotaCredito['fechaEmisionDocSustento'] = $factura->fecha;
                                    $infoNotaCredito['numDocModificado']  = $factura->nro_comprobante;
                                    $infoNotaCredito['fechaEmisionDocSustento'] = $factura->fecha;
                                }
                                $infoNotaCredito['totalSinImpuestos'] = $this->getDecimal(($row->sub_sin_imp - $row->descuento), 2);
                                $infoNotaCredito['valorModificacion'] = $this->getDecimal(($row->sub_sin_imp - $row->descuento) + $row->impuesto, 2);
                                $divisa = Ct_Divisas::getMoneda($factura->divisas); //Ct_Divisas::where('id',$factura->divisas);
                                $infoNotaCredito['moneda'] = $divisa;
                                $totalConImpuestos = [];
                                $totalConImpuesto = [];
                                $conImpuesto = 0;
                                if (!is_null($idFactura)) {
                                    $detalleFactura = Ct_Devolucion_Productos::where('id_nota_credito', $row->id)->get();
                                    if (count($detalleFactura) > 0) {
                                        foreach ($detalleFactura as $detail) {
                                            $codigoImpuesto = De_Codigo_Impuestos::where('porcentaje', ($this->porIva->iva * 100))->where('codigo_impuesto', 2)->first();
                                            $totalConImpuesto['codigo'] = $codigoImpuesto->codigo_impuesto;
                                            if ($detail->iva == 1) {
                                                $totalConImpuesto['codigoPorcentaje'] = !EmisionDocumentosController::validarPeriodo($row->fecha) ? 2 : $codigoImpuesto->codigo; //como se cuando son los otros tipos de iva 
                                                $totalConImpuesto['valor'] = $totalConImpuesto['valor'] +  $this->getDecimal($detail->valor_iva);
                                                //$totalConImpuesto['descuentoAdicional'] = $detail->descuento;
                                                $totalConImpuesto['baseImponible'] = $totalConImpuesto['baseImponible'] + ($detail->cantidad * $detail->precio) - $detail->descuento;
                                                $totalConImpuestos[0] = $totalConImpuesto;
                                            } else {
                                                $totalConImpuesto['codigoPorcentaje'] = '0';
                                                $totalConImpuesto['valor'] = $this->getDecimal(0, 2);
                                                //$totalConImpuesto['descuentoAdicional'] = $detail->descuento;
                                                $totalConImpuesto['baseImponible'] = $this->getDecimal($totalConImpuesto['baseImponible'] + ($detail->cantidad * $detail->precio) - $detail->descuento, 2); // - $detail->descuento se quita hasta arreglar la nota de credito
                                                $totalConImpuestos[1] = $totalConImpuesto;
                                            }
                                        }
                                    }
                                    $infoNotaCredito['totalConImpuestos'] = $totalConImpuestos;
                                    $infoNotaCredito['motivo'] = str_replace('&', 'Y', $row->concepto);
                                    $data['infoNotaCredito'] = $infoNotaCredito;
                                    //detalles
                                    $detalle = [];
                                    $detalles = [];
                                    $conDetalle = 0;
                                    if (count($detalleFactura) > 0) {
                                        foreach ($detalleFactura as $deta) {
                                            $detalle['p_impuesto'] = $deta->iva != 0 ? (!EmisionDocumentosController::validarPeriodo($row->fecha) ? 12 : ($this->porIva->iva * 100)) : 0;
                                            $detalle['codigoInterno'] = $deta->codigo;
                                            $detalle['codigoAdicional'] = $deta->codigo;
                                            if ($deta->descripcion != '')
                                                $detalle['descripcion'] = $deta->descripcion;
                                            else
                                                $detalle['descripcion'] = $deta->codigo . ' | ' . $deta->nombre;
                                            $detalle['cantidad'] = $deta->cantidad;
                                            $detalle['precioUnitario'] = $this->getDecimal($deta->precio, 2);
                                            $detalle['descuento'] = $deta->descuento;
                                            $detalle['precioTotalSinImpuesto'] = $this->getDecimal(($deta->cantidad * $deta->precio) - $deta->descuento, 2);
                                            $contAdicional = 0;

                                            if ($deta->nombre != '') {
                                                $detAdicional_ = [
                                                    'nombre' => 'nombre',
                                                    'valor' => $deta->nombre
                                                ];
                                                $contAdicional++;
                                            }
                                            if ($contAdicional > 0) {
                                                $detallesAdicionales = [isset($detAdicional_) ? $detAdicional_ : []];
                                                $detalle['detallesAdicionales'] = $detallesAdicionales;
                                            }
                                            $detalles[$conDetalle] = $detalle;
                                            $conDetalle++;
                                        }
                                    }
                                    $data['detalles'] = $detalles;
                                }
                                // $details       = Ct_Detalle_Credito_Clientes::where('id_not_cred', $row->id)->first();
                                $cont = 0;
                                if (isset($cliente->identificacion) && $cliente->identificacion != '') {
                                    $campoAdicional['nombre'] = "Código cliente";
                                    $campoAdicional['valor'] =  substr($cliente->identificacion, 0, 10);
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }
                                if (isset($empresa->nombrecomercial) && $empresa->nombrecomercial != '') {
                                    $campoAdicional['nombre'] = "Nombre comercial";
                                    $campoAdicional['valor'] =  $empresa->nombrecomercial;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }
                                if ($direccionCliente != '') {
                                    $campoAdicional['nombre'] = "direccion";
                                    $campoAdicional['valor'] =  $direccionCliente;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }
                                if ($emailCliente != '') {
                                    $campoAdicional['nombre'] = "email";
                                    $campoAdicional['valor']  =  $emailCliente;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }

                                if ($telefonoCliente != '') {
                                    $campoAdicional['nombre'] = "telefono";
                                    $campoAdicional['valor'] =  $telefonoCliente;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }
                                if ($telefonoCliente2 != '') {
                                    $campoAdicional['nombre'] = "celular";
                                    $campoAdicional['valor'] =  $telefonoCliente2;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }
                                if ($row->id_usuariocrea != '') {
                                    $campoAdicional['nombre'] = "vendedor";
                                    $campoAdicional['valor'] =  $row->id_usuariocrea;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }
                                if ($empresa->ciudad != '') {
                                    $campoAdicional['nombre'] = "ciudad";
                                    $campoAdicional['valor'] =  $empresa->ciudad;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }
                                $infoAdicional['campoAdicional'] = $informacion_adicional;

                                $data['infoAdicional'] = $informacion_adicional;
                                //dd($data);
                            } else {
                                $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existe la empresa');
                                echo json_encode($result);
                                return;
                            }
                            $errores = $this->validarData($data);
                            if ($esVerificacion) {
                                return ['data' => $data, 'errors' => $errores];
                            }
                            /*if ($row->id_empresa == '0993069299001') {
                                echo '<pre>';
                                print_r($data);
                                print_r($errores);
                                exit;
                            }*/
                            if (count($errores) > 0) {
                                $arrayDocElec = [
                                    'id_de_pasos' => 7,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoNotaCredito' => json_encode($data['infoNotaCredito']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => str_pad($infoTributaria['estab'], 3, 0, STR_PAD_LEFT),
                                    'emision' => str_pad($infoTributaria['ptoEmi'], 3, 0, STR_PAD_LEFT),
                                    'secuencial' => str_pad($infoTributaria['secuencial'], 9, 0, STR_PAD_LEFT),
                                    'ruc_receptor' => $infoTributaria['identificacionComprador'],
                                    'ruc_emisor' => $infoTributaria['ruc'],
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                    'id_documento' => $row->id,
                                ];
                                $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayDocElec, 7);
                                Ct_Nota_Credito_Clientes::updateSinGenerarXML($row->id);
                                $idInsertados .= $id_doc . ',';
                                $arrayLog = [
                                    'id_de_documentos_electronicos' => $id_doc,
                                    'descripcion_error' => json_encode($errores),
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'id_documento' => $row->id
                                ];
                                De_Log_Error::setErrorLog($arrayLog);
                            } else {
                                //Creacion XML
                                $arrayLogError = [
                                    'id_de_pasos' => 1,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoNotaCredito' => json_encode($data['infoNotaCredito']),
                                    'detalles' => json_encode($data['detalles']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => str_pad($infoTributaria['estab'], 3, 0, STR_PAD_LEFT),
                                    'emision' => str_pad($infoTributaria['ptoEmi'], 3, 0, STR_PAD_LEFT),
                                    'secuencial' => str_pad($infoTributaria['secuencial'], 9, 0, STR_PAD_LEFT),
                                    'ruc_receptor' =>  $infoTributaria['identificacionComprador'],
                                    'ruc_emisor' => $infoTributaria['ruc'],
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                    'id_documento' => $row->id
                                ];
                                $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayLogError, 1);
                                if (is_numeric($id_doc)) {
                                    $idInsertados .= $id_doc . ',';
                                    $comprobante = $this->generarDocElectronicoXml($data);
                                    /*if ($empresa->id == '0992454407001') {
                                        header('Content-type: text/xml');
                                        echo $comprobante;
                                        exit;
                                    }*/
                                    Ct_Nota_Credito_Clientes::updateGenerarXML($row->id, $data['infoTributaria']['claveAcceso']);
                                    $arrayDato = [
                                        'tipo_documento' => $infoTributaria['codDoc'],
                                        'id_pasos' => 1,
                                        'id_documento' => $row->id,
                                        'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                        'informacion' => 'Documento generado',
                                        'id_usuariomod' => $idUsuario,
                                        'id_usuariocrea' => $idUsuario,
                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                        'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                    ];
                                    Ct_Nota_Credito_Clientes::setLogDocElectronico($arrayDato);
                                    if ($comprobante != '') {
                                        $docAnte = simplexml_load_string($comprobante);
                                        //dd($docAnte);
                                        if (!empty($docAnte)) {
                                            //validar xsd
                                            $validacion = $this->validarXmlToXsd($comprobante);
                                            if ($validacion['isValidoXsd'] != 1) {
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($validacion),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $result = array('code' => 204, 'state' => true, 'data' => json_encode($validacion), 'message' => 'ok|No existen documentos para emitir', 'tipoDoc' => 'Nota de credito');
                                                echo json_encode($result);
                                                return;
                                            }
                                            De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                            Ct_Nota_Credito_Clientes::updateValidacionXSD($row->id);
                                            $arrayDato = [
                                                'tipo_documento' => $infoTributaria['codDoc'],
                                                'id_pasos' => 2,
                                                'id_documento' => $row->id,
                                                'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                'informacion' => 'Documento validado por XSD',
                                                'id_usuariomod' => $idUsuario,
                                                'id_usuariocrea' => $idUsuario,
                                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                            ];
                                            Ct_Nota_Credito_Clientes::setLogDocElectronico($arrayDato);
                                            //firmar xml
                                            $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $datosFirma->ruta_firma; //firma.p12
                                            $password = $datosFirma->clave_firma; //'3duard0faustos';
                                            $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                                            if (!empty($result)) {
                                                De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                                Ct_Nota_Credito_Clientes::updateXmlFirmado($row->id);
                                                $arrayDato = [
                                                    'tipo_documento' => $infoTributaria['codDoc'],
                                                    'id_pasos' => 3,
                                                    'id_documento' => $row->id,
                                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                    'informacion' => 'Documento firmado',
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' => $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                ];
                                                Ct_Nota_Credito_Clientes::setLogDocElectronico($arrayDato);
                                                //recepcion sri
                                                $respuesta = $this->recibirWs($result, $data['infoTributaria']['ambiente']);
                                                De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                                Ct_Nota_Credito_Clientes::updateRecibidoSri($row->id);
                                                $arrayDato = [
                                                    'tipo_documento' => $infoTributaria['codDoc'],
                                                    'id_pasos' => 4,
                                                    'id_documento' => $row->id,
                                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                    'informacion' => 'Documento recibido',
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' => $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                ];
                                                Ct_Nota_Credito_Clientes::setLogDocElectronico($arrayDato);
                                                if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                                    //$this->procesarRespuestaXml($respuesta);
                                                    $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                                    $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                                    De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                    //autorzacion sri
                                                    $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso, $data['infoTributaria']['ambiente']);
                                                    $estadoAutorizacion = $this->generarXmlAutorizacion($respuestaAutorizacion);
                                                    if ($estadoAutorizacion == 'AUTORIZADO') {
                                                        De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                                        Ct_Nota_Credito_Clientes::updateXmlAutorizacion($row->id, $docAnte->infoTributaria->claveAcceso, $secuancialDocumento, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi']);
                                                        De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                        $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => 5,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Documento autorizado',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        Ct_Nota_Credito_Clientes::setLogDocElectronico($arrayDato);
                                                        $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
                                                        $param = [
                                                            'email' => $cliente->email_representante,
                                                            'nombre' => $cliente->nombre != '' ? $cliente->nombre : $cliente->nombre_representante,
                                                            'claveAcceso' => $docAnte->infoTributaria->claveAcceso,
                                                            'establecimiento' => str_pad($infoTributaria['estab'], 3, 0, STR_PAD_LEFT),
                                                            'emision' => str_pad($infoTributaria['ptoEmi'], 3, 0, STR_PAD_LEFT),
                                                            'secuencial' => str_pad($infoTributaria['secuencial'], 9, 0, STR_PAD_LEFT),
                                                            'tipoDoc' => $data['infoTributaria']['codDoc'],
                                                            'fechaEmision' => $data['infoTributaria']['fecha_emision'],
                                                        ];
                                                        $this->enviar_correo($param);
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => 11,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Documento notificado',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        Ct_Nota_Credito_Clientes::setLogDocElectronico($arrayDato);
                                                    } else {
                                                        De_Documentos_Electronicos::updateXmlNoAutorizacion($comprobante, $respuestaAutorizacion);
                                                        Ct_Nota_Credito_Clientes::updateXmlNoAutorizacion($row->id);
                                                        $arrayLog = [
                                                            'id_de_documentos_electronicos' => $id_doc,
                                                            'descripcion_error' => json_encode($respuestaAutorizacion),
                                                            'id_documento' => $row->id,
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' =>  $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        De_Log_Error::setErrorLog($arrayLog);
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => 10,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Documento no autorizado',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        Ct_Nota_Credito_Clientes::setLogDocElectronico($arrayDato);
                                                    }
                                                } else {
                                                    $this->generarXmlRespuesta($respuesta);
                                                    De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                                    Ct_Nota_Credito_Clientes::updateXmlNoRecepcion($row->id);
                                                    $arrayLog = [
                                                        'id_de_documentos_electronicos' => $id_doc,
                                                        'descripcion_error' => json_encode($respuesta),
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' =>  $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    De_Log_Error::setErrorLog($arrayLog);
                                                    //return $respuesta['mensajesWs'];
                                                    $arrayDato = [
                                                        'tipo_documento' => $infoTributaria['codDoc'],
                                                        'id_pasos' => 9,
                                                        'id_documento' => $row->id,
                                                        'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                        'informacion' => 'Documento devuelto',
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' => $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    Ct_Nota_Credito_Clientes::setLogDocElectronico($arrayDato);
                                                }
                                            } else {
                                                //Log error al firmar XML
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($result),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $arrayDato = [
                                                    'tipo_documento' => $infoTributaria['codDoc'],
                                                    'id_pasos' => 6,
                                                    'id_documento' => $row->id,
                                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                    'informacion' => 'Documento sin firmar',
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' => $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                ];
                                                Ct_Nota_Credito_Clientes::setLogDocElectronico($arrayDato);
                                            }
                                        }
                                    } else {
                                        //log No genero XML
                                        $arrayLog = [
                                            'id_de_documentos_electronicos' => $id_doc,
                                            'descripcion_error' => 'Error al crear el documento XML',
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' =>  $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                        ];
                                        De_Log_Error::setErrorLog($arrayLog);
                                        $arrayDato = [
                                            'tipo_documento' => $infoTributaria['codDoc'],
                                            'id_pasos' => 7,
                                            'id_documento' => $row->id,
                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                            'informacion' => 'Documento sin generar xml',
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' => $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                        ];
                                        Ct_Nota_Credito_Clientes::setLogDocElectronico($arrayDato);
                                    }
                                } else {
                                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'error|' . $id_doc);
                                    echo json_encode($result);
                                    return;
                                }
                            }
                        }
                    }
                } else {
                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existen documentos para emitir', 'tipoDoc' => 'Nota de credito');
                    echo json_encode($result);
                    return;
                }
                $infoErrores = De_Documentos_Electronicos::revisionEstadosDocumentosErrores($idInsertados);
                $infoAutorizados = '';
                if ($claveInsertados != '')
                    $infoAutorizados = De_Documentos_Electronicos::revisionEstadosDocumentosAutorizados($claveInsertados);
                $result = array('code' => 200, 'state' => true, 'data' => ['errores:' => $infoErrores, 'autorizado:' => $infoAutorizados], 'message' => 'proceso ejecutado correctamente', 'tipoDoc' => 'Nota de credito');
                echo json_encode($result);
            } else {
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                if (!$this->consultarEstadoSRI(session('id_empresa'))) {
                    $arrayLog = [
                        'id_de_documentos_electronicos' => $id_doc,
                        'descripcion_error' => json_encode(['Error:' => 'SRI sin servicio']),
                        'id_usuariomod' => $idUsuario,
                        'id_usuariocrea' =>  $idUsuario,
                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                        'id_documento' => $id_doc
                    ];
                    De_Log_Error::setErrorLog($arrayLog);
                    $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $arrayLog], 'message' => 'Error');
                    return json_encode($result);
                } else {
                    $cont = 0;
                    $empresa = Empresa::getEmpresa($dinfo['empresa']);
                    if ($empresa != '') {
                        $datosFirma = De_Empresa::getDatos($dinfo['empresa']);

                        $notaCredito = Ct_Nota_Credito_Clientes::where('id', $dinfo['idVenta'])->first();

                        $firma['ruta_firma'] = $datosFirma->ruta_firma;
                        $firma['clave_firma'] = $datosFirma->clave_firma;
                        $data['datosfirma'] = $firma;
                        $infoTributaria['fecha_emision'] = date('d/m/Y');
                        $infoTributaria['ambiente'] = 1;
                        $infoTributaria['tipoEmision'] = 1;
                        $infoTributaria['razonSocial'] = $empresa->razonsocial;
                        $infoTributaria['nombreComercial'] = $empresa->nombrecomercial;
                        $infoTributaria['ruc'] = $empresa->id;
                        $infoTributaria['codDoc'] = '01';
                        $infoTributaria['estab'] = str_pad($notaCredito->sucursal, 3, 0, STR_PAD_LEFT);
                        $infoTributaria['ptoEmi'] = str_pad($notaCredito->punto_emision, 3, 0, STR_PAD_LEFT);
                        $infoTributaria['dirMatriz'] = $empresa->direccion;
                        $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                        $datosTributarios = De_Info_Tributaria::reservarSecuencial(session('id_empresa'), $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                        if ($datosTributarios == '') {
                            $result = array('code' => 204, 'state' => true, 'data' => ['error:' => 'no se ha configurado los datos tributarios para esta empresa: ' . $empresa->id], 'message' => 'no existen datos tributarios para esta empresa');
                            echo json_encode($result);
                            return;
                        }
                        $secuencial = $datosTributarios->secuencial_nro;
                        $infoTributaria['ambiente'] = $datosFirma->ambiente;
                        $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                        $claveObj = new ClaveAcceso();
                        $campos = [
                            'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($notaCredito->fecha))),
                            'tipo_comprobante' => $infoTributaria['codDoc'],
                            'ruc' => $infoTributaria['ruc'],
                            'tipo_ambiente' => $infoTributaria['ambiente'],
                            'punto_establecimiento' => $infoTributaria['estab'],
                            'punto_emision' => $infoTributaria['ptoEmi'],
                            'numero_comprobante' => $infoTributaria['secuencial'],
                            'codigo_numerico' => $infoTributaria['secuencial'],
                            'tipo_emision' => $infoTributaria['tipoEmision']
                        ];
                        $claveObj->llenarDatos($campos);
                        $infoTributaria['claveAcceso'] = $claveObj->clave_acceso;
                        $data['infoTributaria'] = $infoTributaria;
                        //InfoTributaria
                        $infoNotaCredito['fechaEmision'] = $notaCredito->fecha;
                        $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                            ->where('codigo_sucursal', $infoTributaria['estab'])
                            ->first();

                        if (isset($sucursal->direccion_sucursal)) {
                            $infoNotaCredito['dirEstablecimiento'] = $sucursal->direccion_sucursal;
                        } else {
                            $infoNotaCredito['dirEstablecimiento'] = $empresa->direccion;
                        }
                        if ($datosFirma->contribuyente_especial != '' && $datosFirma->contribuyente_especial != 0) {
                            $infoNotaCredito['contribuyenteEspecial'] = $datosFirma->contribuyente_especial; //Opcional
                        }
                        $infoNotaCredito['obligadoContabilidad'] = 'NO'; //Opcional
                        if ($datosFirma->contabilidad == 1)
                            $infoNotaCredito['obligadoContabilidad'] = 'SI'; //Opcional
                        $cliente = Ct_Clientes::getCliente($notaCredito->id_cliente);

                        if ($cliente != '') {
                            $email = $cliente->email_representante;
                            $telefonoCliente = $cliente->telefono1;
                            $infoNotaCredito['tipoIdentificacionComprador'] = str_pad($cliente->tipo, 2, 0, STR_PAD_LEFT);
                            $infoNotaCredito['identificacionComprador'] = $cliente->identificacion;
                            $infoNotaCredito['razonSocialComprador'] = $cliente->nombre;
                            $infoNotaCredito['direccionComprador'] = $cliente->direccion_representante;
                        } else {
                            $infoNotaCredito['tipoIdentificacionComprador'] = null;
                            $infoNotaCredito['identificacionComprador'] = null;
                            $infoNotaCredito['razonSocialComprador'] = null;
                            $infoNotaCredito['direccionComprador'] = null; //Opcional
                        }
                        $infoNotaCredito['totalSinImpuestos'] = $notaCredito->subtotal_0 + $notaCredito->subtotal_12;
                        $infoNotaCredito['codDocModificado'] = '01';
                        $factura = null;
                        $idFactura = $notaCredito->numero_factura; //numero_factura es id_factura cuando es completa; id_factura para parcial
                        $factura = Ct_ventas::find($idFactura)->first();

                        if (!is_null($factura)) {
                            $infoNotaCredito['fechaEmisionDocSustento'] = $factura->fecha;
                            $infoNotaCredito['numDocModificado']  = $factura->nro_comprobante;
                        }
                        $infoNotaCredito['totalSinImpuestos'] = $notaCredito->subtotal0;
                        $infoNotaCredito['valorModificacion'] = $notaCredito->total_final;
                        $divisa = Ct_Divisas::getMoneda($factura->divisas); //Ct_Divisas::where('id',$factura->divisas);
                        $infoNotaCredito['moneda'] = $divisa;
                        $totalConImpuestos = [];
                        $totalConImpuesto = [];
                        $conImpuesto = 0;
                        // $details       = Ct_Detalle_Credito_Clientes::where('id_not_cred', $notaCredito->id)->first();
                        $venta_detalle = Ct_detalle_venta::where('id_ct_ventas', $idFactura)->get();
                        if (count($venta_detalle) > 0) {
                            foreach ($venta_detalle as $detail) {
                                $codigoImpuesto = De_Codigo_Impuestos::where('porcentaje', ($this->porIva->iva * 100))->where('codigo_impuesto', 2)->first();
                                $totalConImpuesto['codigo'] = $codigoImpuesto->codigo_impuesto; //IVA
                                if ($detail->check_iva == 1) {
                                    $totalConImpuesto['codigoPorcentaje'] = $codigoImpuesto->codigo;
                                    $totalConImpuesto['valor'] = $this->getDecimal(($detail->precio * $detail->cantidad) * $detail->porcentaje);
                                } else {
                                    $totalConImpuesto['codigoPorcentaje'] = '0';
                                    $totalConImpuesto['valor'] = $this->getDecimal(0);
                                }
                                $totalConImpuesto['descuentoAdicional'] = $detail->descuento;
                                $totalConImpuesto['baseImponible'] = ($detail->cantidad * $detail->precio) - $detail->descuento;

                                $totalConImpuestos[$conImpuesto] = $totalConImpuesto;
                                $conImpuesto++;
                            }
                        }
                        $infoNotaCredito['totalConImpuestos'] = $totalConImpuestos;
                        $infoNotaCredito['motivo'] = $notaCredito->concepto;
                        $data['infoNotaCredito'] = $infoNotaCredito;

                        //detalles
                        $detalle = [];
                        $detalles = [];
                        $conDetalle = 0;
                        if (count($venta_detalle) > 0) {
                            foreach ($venta_detalle as $deta) {
                                $detalle['p_impuesto'] = $deta->check_iva !== null ? ($this->porIva->iva * 100) : 0;
                                $detalle['codigoInterno'] = $deta->id_ct_productos;
                                $detalle['codigoAdicional'] = $deta->id_ct_productos;
                                $detalle['descripcion'] = $deta->nombre;
                                $detalle['cantidad'] = $deta->cantidad;
                                $detalle['precioUnitario'] = $deta->precio;
                                $detalle['descuento'] = $deta->descuento;
                                $detalle['precioTotalSinImpuesto'] = $notaCredito->sub_sin_imp;
                                $contAdicional = 0;
                                if ($deta->detalle != '') {
                                    $detAdicional = [
                                        'nombre' => 'detalle',
                                        'valor' => $deta->detalle
                                    ];
                                    $contAdicional++;
                                }
                                if ($deta->adicional != '') {
                                    $detAdicional_ = [
                                        'nombre' => 'adicional',
                                        'valor' => $deta->adicional
                                    ];
                                    $contAdicional++;
                                }
                                if ($contAdicional > 0) {
                                    $detallesAdicionales = [isset($detAdicional) ? $detAdicional : [], isset($detAdicional_) ? $detAdicional_ : []];
                                    $detalle['detallesAdicionales'] = $detallesAdicionales;
                                }
                                $detalles[$conDetalle] = $detalle;
                                $conDetalle++;
                            }
                        }
                        $data['detalles'] = $detalles;
                        $campoAdicional['nombre'] = "detalle";
                        $campoAdicional['valor']  = $notaCredito->nota_electronica;
                        $informacion_adicional[0] = $campoAdicional;
                        $infoAdicional['campoAdicional'] = $informacion_adicional;
                        $data['infoAdicional'] = $informacion_adicional;
                        $errores = $this->validarData($data);
                        if (count($errores) > 0) {
                            $arrayDocElec = [
                                'id_de_pasos' => 7,
                                'infoTributaria' => json_encode($data['infoTributaria']),
                                'infoFactura' => json_encode($data['infoFactura']),
                                'infoAdicional' => json_encode($data['infoAdicional']),
                                'establecimiento' => str_pad($infoTributaria['estab'], 3, 0, STR_PAD_LEFT),
                                'emision' => str_pad($infoTributaria['ptoEmi'], 3, 0, STR_PAD_LEFT),
                                'secuencial' => str_pad($infoTributaria['secuencial'], 9, 0, STR_PAD_LEFT),
                                'ruc_receptor' => $infoTributaria['identificacionComprador'],
                                'ruc_emisor' => $infoTributaria['ruc'],
                                'id_usuariomod' => $idUsuario,
                                'id_usuariocrea' =>  $idUsuario,
                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                'id_documento' => $notaCredito->id,
                            ];
                            $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayDocElec, 7);
                            Ct_Nota_Credito_Clientes::updateSinGenerarXML($notaCredito->id);
                            $idInsertados .= $id_doc . ',';
                            $arrayLog = [
                                'id_de_documentos_electronicos' => $id_doc,
                                'descripcion_error' => json_encode($errores),
                                'id_usuariomod' => $idUsuario,
                                'id_usuariocrea' =>  $idUsuario,
                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                'id_documento' => $notaCredito->id
                            ];
                            De_Log_Error::setErrorLog($arrayLog);
                        }

                        //Creacion XML
                        $arrayLogError = [
                            'id_de_pasos' => 1,
                            'infoTributaria' => json_encode($data['infoTributaria']),
                            'infoFactura' => json_encode($data['infoFactura']),
                            'detalles' => json_encode($data['detalles']),
                            'infoAdicional' => json_encode($data['infoAdicional']),
                            'establecimiento' => str_pad($infoTributaria['estab'], 3, 0, STR_PAD_LEFT),
                            'emision' => str_pad($infoTributaria['ptoEmi'], 3, 0, STR_PAD_LEFT),
                            'secuencial' => str_pad($infoTributaria['secuencial'], 9, 0, STR_PAD_LEFT),
                            'ruc_receptor' =>  $infoTributaria['identificacionComprador'],
                            'ruc_emisor' => $infoTributaria['ruc'],
                            'id_usuariomod' => $idUsuario,
                            'id_usuariocrea' =>  $idUsuario,
                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                            'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                            'id_documento' => $notaCredito->id
                        ];
                        $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayLogError, 1);
                        if (is_numeric($id_doc)) {
                            $idInsertados .= $id_doc . ',';
                            $comprobante = $this->generarDocElectronicoXml($data);
                            Ct_ventas::updateGenerarXML($notaCredito->id, $data['infoTributaria']['claveAcceso']);
                            if ($comprobante != '') {
                                $docAnte = simplexml_load_string($comprobante);
                                if (!empty($docAnte)) {
                                    //validar xsd
                                    $validacion = $this->validarXmlToXsd($comprobante);
                                    if ($validacion['isValidoXsd'] != 1) {
                                        $arrayLog = [
                                            'id_de_documentos_electronicos' => $id_doc,
                                            'descripcion_error' => json_encode($validacion),
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' =>  $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                        ];
                                        De_Log_Error::setErrorLog($arrayLog);
                                        $result = array('code' => 500, 'state' => true, 'data' => json_encode($validacion), 'message' => 'Error: al validar documento xml.');
                                        return json_encode($result);
                                    }
                                    De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                    Ct_ventas::updateValidacionXSD($notaCredito->id);
                                    //firmar xml
                                    $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $datosFirma->ruta_firma; //firma.p12
                                    $password = $datosFirma->clave_firma; //'3duard0faustos';
                                    $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                                    if (!empty($result)) {
                                        De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                        Ct_ventas::updateXmlFirmado($notaCredito->id);
                                        //recepcion sri
                                        $respuesta = $this->recibirWs($result, $data['infoTributaria']['ambiente']);
                                        De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                        Ct_ventas::updateRecibidoSri($notaCredito->id);
                                        if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                            //$this->procesarRespuestaXml($respuesta);
                                            $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                            $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                            De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                            //autorzacion sri
                                            $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso, $data['infoTributaria']['ambiente']);
                                            $this->generarXmlAutorizacion($respuestaAutorizacion);
                                            De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                            Ct_ventas::updateXmlAutorizacion($notaCredito->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $data['infoTributaria']['secuencial'], $docAnte->infoTributaria->claveAcceso);
                                            $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                            $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
                                            $param = [
                                                'email' => $notaCredito->email_traslado_destinatario,
                                                'nombre' => $notaCredito->razon_social_destinatario,
                                                'claveAcceso' => $docAnte->infoTributaria->claveAcceso,
                                                'estab' => $data['infoTributaria']['estab'],
                                                'ptoEmi' =>  $data['infoTributaria']['ptoEmi'],
                                                'secuencial' =>  $data['infoTributaria']['secuencial'],
                                                'tipoDoc' => $this->tipoDocumento
                                            ];
                                            $this->enviar_correo($param);
                                        } else {
                                            $this->generarXmlRespuesta($respuesta);
                                            De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                            Ct_ventas::updateXmlNoRecepcion($notaCredito->id);
                                            $arrayLog = [
                                                'id_de_documentos_electronicos' => $id_doc,
                                                'descripcion_error' => json_encode($respuesta),
                                                'id_usuariomod' => $idUsuario,
                                                'id_usuariocrea' =>  $idUsuario,
                                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                            ];
                                            De_Log_Error::setErrorLog($arrayLog);
                                            //return $respuesta['mensajesWs'];
                                        }
                                    } else {
                                        //Log error al firmar XML
                                        $arrayLog = [
                                            'id_de_documentos_electronicos' => $id_doc,
                                            'descripcion_error' => json_encode($result),
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' =>  $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                        ];
                                        De_Log_Error::setErrorLog($arrayLog);
                                    }
                                }
                            } else {
                                //log No genero XML
                                $arrayLog = [
                                    'id_de_documentos_electronicos' => $id_doc,
                                    'descripcion_error' => 'Error al crear el documento XML',
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                ];
                                De_Log_Error::setErrorLog($arrayLog);
                            }
                        } else {
                            $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'error|' . $id_doc);
                            echo json_encode($result);
                            return;
                        }
                    }
                }
                $respuesta = new stdClass();
                $respuesta->nro_comprobante = $numDocumento;
                return $respuesta;
            }
        } catch (Exception $ex) {
            if ($id_doc != 0) {
                $arrayLog = [
                    'id_de_documentos_electronicos' => $id_doc,
                    'descripcion_error' => json_encode($ex->getMessage()),
                    'id_usuariomod' => $idUsuario,
                    'id_usuariocrea' =>  $idUsuario,
                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                ];
                De_Log_Error::setErrorLog($arrayLog);
            }
            $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $ex->getMessage(), 'linea' => $ex->getLine()], 'message' => 'Error', 'tipoDoc' => 'Nota de credito');
            echo json_encode($result);
            return;
        }
    }
    public function getNotaDebito($dinfo = null, $idUsuario = 'FACELECTRO')
    {
        $data = [];
        $id_doc = 0;
        $numDocumento = '';
        $secuencial_faltante = false;
        try {
            if ($dinfo == null) {
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                $notaDebitoCabecera = Ct_Nota_Debito_Cliente::getNotaDebito();
                if (count($notaDebitoCabecera) > 0) {
                    if (!$this->consultarEstadoSRI($notaDebitoCabecera[0]->id_empresa)) {
                        $arrayLog = [
                            'id_de_documentos_electronicos' => $id_doc,
                            'descripcion_error' => json_encode(['Error:' => 'SRI sin servicio']),
                            'id_usuariomod' => $idUsuario,
                            'id_usuariocrea' =>  $idUsuario,
                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                            'id_documento' => $id_doc
                        ];
                        De_Log_Error::setErrorLog($arrayLog);
                        $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $arrayLog], 'message' => 'Error');
                        echo json_encode($result);
                        return;
                    } else {
                        $cont = 0;
                        foreach ($notaDebitoCabecera as $row) {
                            $cont++;
                            $empresa = Empresa::getEmpresa($row->id_empresa);
                            if ($empresa != '') {
                                $datosFirma = De_Empresa::getDatos($row->id_empresa);
                                $firma['ruta_firma'] = $datosFirma->ruta_firma;
                                $firma['clave_firma'] = $datosFirma->clave_firma;
                                $data['datosfirma'] = $firma;
                                $infoTributaria['fecha_emision'] = date('d/m/Y', strtotime($row->fecha));
                                $infoTributaria['ambiente'] = 1;
                                $infoTributaria['tipoEmision'] = 1;
                                $infoTributaria['razonSocial'] = $empresa->razonsocial;
                                $infoTributaria['nombreComercial'] = $empresa->nombrecomercial;
                                $infoTributaria['ruc'] = $empresa->id;
                                $infoTributaria['codDoc'] = '05';
                                $infoTributaria['estab'] = str_pad($row->sucursal, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['ptoEmi'] = str_pad($row->punto_emision, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['dirMatriz'] = $empresa->direccion;
                                $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                                if ($idDoc == '') {
                                    $result = array(
                                        'code' => 204,
                                        'state' => true,
                                        'data' => ['error:' => 'No esiste el maestro de documentos para esta empresa: ' . $empresa->id],
                                        'message' => 'no existen el maestro de documentos para esta empresa'
                                    );
                                    echo json_encode($result);
                                    return;
                                }
                                $datosTributarios = De_Info_Tributaria::reservarSecuencial($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                                if ($datosTributarios == '') {
                                    $result = array(
                                        'code' => 204,
                                        'state' => true,
                                        'data' => ['error:' => 'no se ha configurado los datos tributarios para esta empresa: ' . $empresa->id],
                                        'message' => 'no existen datos tributarios para esta empresa'
                                    );
                                    echo json_encode($result);
                                    return;
                                }
                                $secuencial = $datosTributarios->secuencial_nro;
                                $infoTributaria['ambiente'] = $datosFirma->ambiente;
                                $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                                $claveObj = new ClaveAcceso();
                                $campos = [
                                    'fecha_emision' => $row->fecha,
                                    'tipo_comprobante' => $infoTributaria['codDoc'],
                                    'ruc' => $infoTributaria['ruc'],
                                    'tipo_ambiente' => $infoTributaria['ambiente'],
                                    'punto_establecimiento' => $infoTributaria['estab'],
                                    'punto_emision' => $infoTributaria['ptoEmi'],
                                    'numero_comprobante' => $infoTributaria['secuencial'],
                                    'codigo_numerico' => $infoTributaria['secuencial'],
                                    'tipo_emision' => $infoTributaria['tipoEmision']
                                ];
                                $claveObj->llenarDatos($campos);
                                //echo  $claveObj->clave_acceso;exit;
                                $infoTributaria['claveAcceso'] = $claveObj->clave_acceso;
                                if ($datosFirma->agente_retencion == 1) {
                                    $infoTributaria['agenteRetencion'] = 1;
                                }
                                $data['infoTributaria'] = $infoTributaria;
                                //Info Nota Debito
                                $infoNotaDebito['fechaEmision'] = $row->fecha;
                                $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                                    ->where('codigo_sucursal', $infoTributaria['estab'])
                                    ->first();
                                if (isset($sucursal->direccion_sucursal)) {
                                    $infoNotaDebito['dirEstablecimiento'] = $sucursal->direccion_sucursal;
                                } else {
                                    $infoNotaDebito['dirEstablecimiento'] = $empresa->direccion;
                                }
                                $telefonoCliente = '';
                                $telefonoCliente2 = '';
                                $direccionCliente = '';
                                $emailCliente = '';
                                $cliente = Ct_Clientes::getCliente($row->id_cliente);
                                if ($cliente != '') {
                                    if (isset($cliente->email_representante)) {
                                        $emailCliente = $cliente->email_representante;
                                    }

                                    if (isset($cliente->telefono1_representante)) {
                                        if ($cliente->telefono1_representante != '') {
                                            $telefonoCliente = $cliente->telefono1_representante;
                                        }
                                    }

                                    if (isset($cliente->telefono2_representante)) {
                                        if ($cliente->telefono2_representante != '') {
                                            $telefonoCliente2 = $cliente->telefono2_representante;
                                        }
                                    }

                                    if (isset($cliente->direccion_representante)) {
                                        $direccionCliente = $cliente->direccion_representante;
                                    }
                                    $infoNotaDebito['tipoIdentificacionComprador'] = str_pad($cliente->tipo, 2, 0, STR_PAD_LEFT);
                                    $infoNotaDebito['razonSocialComprador'] = isset($row->nombre_representante) ? $row->nombre_representante : $cliente->nombre;
                                    $infoNotaDebito['identificacionComprador'] = $cliente->identificacion;
                                } else {
                                    $infoNotaDebito['tipoIdentificacionComprador'] = null;
                                    $infoNotaDebito['razonSocialComprador'] = null;
                                    $infoNotaDebito['identificacionComprador'] = null;
                                }
                                if ($datosFirma->contribuyente_especial != '' && $datosFirma->contribuyente_especial != 0) {
                                    $infoNotaDebito['contribuyenteEspecial'] = $datosFirma->contribuyente_especial; //Opcional
                                }
                                $infoNotaDebito['obligadoContabilidad'] = 'NO';
                                if ($datosFirma->contabilidad == 1)
                                    $infoNotaDebito['obligadoContabilidad'] = 'SI'; //Opcional

                                if ($datosFirma->rise != '')
                                    $infoNotaDebito['rise'] = $datosFirma->rise; //Opcional

                                $infoNotaDebito['codDocModificado'] = '01';
                                $factura = null;
                                $idFactura = $row->id_factura;
                                $factura = Ct_ventas::find($idFactura);
                                if (!is_null($factura)) {
                                    $infoNotaDebito['codDocModificado'] = '01';
                                    $infoNotaDebito['numDocModificado']  = $factura->nro_comprobante;
                                    $infoNotaDebito['fechaEmisionDocSustento'] = date('d/m/Y', strtotime($factura->fecha));
                                }
                                $infoNotaDebito['totalSinImpuestos'] = $row->valor_contable;
                                //$infoNotaDebito['valorModificacion'] = $row->sub_sin_imp;
                                //$divisa = Ct_Divisas::getMoneda($factura->divisas); //Ct_Divisas::where('id',$factura->divisas);
                                //$infoNotaDebito['moneda'] = $divisa;
                                $Impuestos = [];
                                $Impuesto = [];
                                $conImpuesto = 0;
                                if (!is_null($row->id)) {
                                    $detalleDebito = Ct_Detalle_Debito_Clientes::where('id_debito', $row->id)->get();
                                    if (count($detalleDebito) > 0) {
                                        foreach ($detalleDebito as $detail) {
                                            $codigoImpuesto = De_Codigo_Impuestos::where('porcentaje', ($this->porIva->iva * 100))->where('codigo_impuesto', 2)->first();
                                            $Impuesto['p_impuesto'] = $detail->iva == 1 ? ($this->porIva->iva * 100) : 0;
                                            $Impuesto['codigo'] = $codigoImpuesto->codigo; //IVA
                                            if ($detail->iva == 1) {
                                                $Impuesto['codigoPorcentaje'] = $codigoImpuesto->codigo; //como se cuando son los otros tipos de iva 
                                                $Impuesto['valor'] = $this->getDecimal($detail->valor * $this->porIva->iva);
                                                $Impuesto['tarifa'] = $this->porIva->iva * 100;
                                                $Impuesto['baseImponible'] =  $this->getDecimal($detail->valor, 2);
                                                $Impuestos[$conImpuesto] = $Impuesto;
                                            } else {
                                                $Impuesto['codigoPorcentaje'] = $codigoImpuesto->codigo;
                                                $Impuesto['tarifa'] = '0';
                                                $Impuesto['valor'] = $this->getDecimal(0, 2);
                                                $Impuesto['baseImponible'] = $this->getDecimal($detail->valor, 2); // - $detail->descuento se quita hasta arreglar la nota de credito
                                                $Impuestos[$conImpuesto] = $Impuesto;
                                            }
                                            $conImpuesto++;
                                        }
                                    }

                                    $infoNotaDebito['impuestos'] = $Impuestos;
                                    $infoNotaDebito['valorTotal'] = $this->getDecimal($row->valor_contable); // * 1.12
                                    //pagos
                                    $pagos = [];
                                    $pago = [];
                                    $codPago = '20';
                                    $pago['formaPago'] = str_pad($codPago, 2, 0, STR_PAD_LEFT);
                                    $pago['total'] = $infoNotaDebito['valorTotal'];
                                    $pago['plazo'] = 1;
                                    $pago['unidadTiempo'] = 'dias';
                                    array_push($pagos, $pago);
                                    $infoNotaDebito['pagos'] = $pagos;
                                }
                                $data['infoNotaDebito'] = $infoNotaDebito;

                                //motivos
                                $motivos = [];
                                $motivo = [];

                                $motivo['razon'] = $row->concepto;
                                $motivo['valor'] = $infoNotaDebito['valorTotal'];
                                $motivos[0] = $motivo;
                                $data['motivos'] = $motivos;
                                $cont = 0;
                                if (isset($cliente->identificacion) && $cliente->identificacion != '') {
                                    $campoAdicional['nombre'] = "Código cliente";
                                    $campoAdicional['valor'] =  substr($cliente->identificacion, 0, 10);
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }

                                if (isset($empresa->nombrecomercial) && $empresa->nombrecomercial != '') {
                                    $campoAdicional['nombre'] = "Nombre comercial";
                                    $campoAdicional['valor'] =  $empresa->nombrecomercial;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }

                                if ($direccionCliente != '') {
                                    $campoAdicional['nombre'] = "direccion";
                                    $campoAdicional['valor'] =  $direccionCliente;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }

                                if ($emailCliente != '') {
                                    $campoAdicional['nombre'] = "email";
                                    $campoAdicional['valor']  =  $emailCliente;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }

                                if ($telefonoCliente != '') {
                                    $campoAdicional['nombre'] = "telefono";
                                    $campoAdicional['valor'] =  $telefonoCliente;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }

                                if ($telefonoCliente2 != '') {
                                    $campoAdicional['nombre'] = "celular";
                                    $campoAdicional['valor'] =  $telefonoCliente2;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }

                                if ($row->id_usuariocrea != '') {
                                    $campoAdicional['nombre'] = "vendedor";
                                    $campoAdicional['valor'] =  $row->id_usuariocrea;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }

                                if ($empresa->ciudad != '') {
                                    $campoAdicional['nombre'] = "ciudad";
                                    $campoAdicional['valor'] =  $empresa->ciudad;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }

                                $infoAdicional['campoAdicional'] = $informacion_adicional;

                                $data['infoAdicional'] = $informacion_adicional;
                            } else {
                                $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existe la empresa');
                                echo json_encode($result);
                                return;
                            }
                            // dd($data);
                            $errores = $this->validarData($data);
                            // dd($errores);
                            if (count($errores) > 0) {
                                $arrayDocElec = [
                                    'id_de_pasos' => 7,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoNotaDebito' => json_encode($data['infoNotaDebito']),
                                    'motivos' => json_encode($data['motivos']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => str_pad($infoTributaria['estab'], 3, 0, STR_PAD_LEFT),
                                    'emision' => str_pad($infoTributaria['ptoEmi'], 3, 0, STR_PAD_LEFT),
                                    'secuencial' => str_pad($infoTributaria['secuencial'], 9, 0, STR_PAD_LEFT),
                                    'ruc_receptor' => $infoTributaria['identificacionComprador'],
                                    'ruc_emisor' => $infoTributaria['ruc'],
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                    'id_documento' => $row->id,
                                ];
                                $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayDocElec, 7);
                                Ct_Nota_Debito_Cliente::updateSinGenerarXML($row->id);
                                $idInsertados .= $id_doc . ',';
                                $arrayLog = [
                                    'id_de_documentos_electronicos' => $id_doc,
                                    'descripcion_error' => json_encode($errores),
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'id_documento' => $row->id
                                ];
                                De_Log_Error::setErrorLog($arrayLog);
                            } else {
                                //Creacion XML
                                $arrayLogError = [
                                    'id_de_pasos' => 1,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoNotaDebito' => json_encode($data['infoNotaDebito']),
                                    'motivos' => json_encode($data['motivos']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => str_pad($infoTributaria['estab'], 3, 0, STR_PAD_LEFT),
                                    'emision' => str_pad($infoTributaria['ptoEmi'], 3, 0, STR_PAD_LEFT),
                                    'secuencial' => str_pad($infoTributaria['secuencial'], 9, 0, STR_PAD_LEFT),
                                    'ruc_receptor' =>  $infoTributaria['identificacionComprador'],
                                    'ruc_emisor' => $infoTributaria['ruc'],
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                    'id_documento' => $row->id
                                ];
                                $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayLogError, 1);
                                if (is_numeric($id_doc)) {
                                    $idInsertados .= $id_doc . ',';
                                    $comprobante = $this->generarDocElectronicoXml($data);
                                    /* header('Content-type: text/xml');
                                    echo $comprobante;
                                    exit; */
                                    Ct_Nota_Debito_Cliente::updateGenerarXML($row->id, $data['infoTributaria']['claveAcceso']);
                                    $arrayDato = [
                                        'tipo_documento' => $infoTributaria['codDoc'],
                                        'id_pasos' => 1,
                                        'id_documento' => $row->id,
                                        'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                        'informacion' => 'Documento generado',
                                        'id_usuariomod' => $idUsuario,
                                        'id_usuariocrea' => $idUsuario,
                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                        'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                    ];
                                    Ct_Nota_Debito_Cliente::setLogDocElectronico($arrayDato);
                                    if ($comprobante != '') {
                                        $docAnte = simplexml_load_string($comprobante);
                                        if (!empty($docAnte)) {
                                            //validar xsd
                                            $validacion = $this->validarXmlToXsd($comprobante);
                                            if ($validacion['isValidoXsd'] != 1) {
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($validacion),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $result = array('code' => 204, 'state' => true, 'data' => json_encode($validacion), 'message' => 'ok|No existen documentos para emitir', 'tipoDoc' => 'Nota de credito');
                                                echo json_encode($result);
                                                return;
                                            }
                                            De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                            Ct_Nota_Debito_Cliente::updateValidacionXSD($row->id);
                                            $arrayDato = [
                                                'tipo_documento' => $infoTributaria['codDoc'],
                                                'id_pasos' => 2,
                                                'id_documento' => $row->id,
                                                'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                'informacion' => 'Documento validado por XSD',
                                                'id_usuariomod' => $idUsuario,
                                                'id_usuariocrea' => $idUsuario,
                                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                            ];
                                            Ct_Nota_Debito_Cliente::setLogDocElectronico($arrayDato);
                                            //firmar xml
                                            $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $datosFirma->ruta_firma; //firma.p12
                                            $password = $datosFirma->clave_firma; //'3duard0faustos';
                                            $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                                            if (!empty($result)) {
                                                De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                                Ct_Nota_Debito_Cliente::updateXmlFirmado($row->id);
                                                $arrayDato = [
                                                    'tipo_documento' => $infoTributaria['codDoc'],
                                                    'id_pasos' => 3,
                                                    'id_documento' => $row->id,
                                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                    'informacion' => 'Documento firmado',
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' => $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                ];
                                                Ct_Nota_Debito_Cliente::setLogDocElectronico($arrayDato);
                                                //recepcion sri
                                                $respuesta = $this->recibirWs($result, $data['infoTributaria']['ambiente']);
                                                De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                                Ct_Nota_Debito_Cliente::updateRecibidoSri($row->id);
                                                $arrayDato = [
                                                    'tipo_documento' => $infoTributaria['codDoc'],
                                                    'id_pasos' => 4,
                                                    'id_documento' => $row->id,
                                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                    'informacion' => 'Documento recibido',
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' => $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                ];
                                                Ct_Nota_Debito_Cliente::setLogDocElectronico($arrayDato);
                                                if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                                    //$this->procesarRespuestaXml($respuesta);
                                                    $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                                    $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                                    De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                    //autorzacion sri
                                                    $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso, $data['infoTributaria']['ambiente']);
                                                    $estadoAutorizacion = $this->generarXmlAutorizacion($respuestaAutorizacion);
                                                    if ($estadoAutorizacion == 'AUTORIZADO') {
                                                        De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                                        Ct_Nota_Debito_Cliente::updateXmlAutorizacion($row->id, $docAnte->infoTributaria->claveAcceso, str_pad($secuancialDocumento, 9, 0, STR_PAD_LEFT), $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi']);
                                                        De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, str_pad($secuancialDocumento, 9, 0, STR_PAD_LEFT), $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                        $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => 5,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Documento autorizado',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        Ct_Nota_Debito_Cliente::setLogDocElectronico($arrayDato);
                                                        $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
                                                        $param = [
                                                            'email' => $row->email_traslado_destinatario,
                                                            'nombre' => $row->razon_social_destinatario,
                                                            'claveAcceso' => $docAnte->infoTributaria->claveAcceso,
                                                            'establecimiento' => str_pad($infoTributaria['estab'], 3, 0, STR_PAD_LEFT),
                                                            'emision' => str_pad($infoTributaria['ptoEmi'], 3, 0, STR_PAD_LEFT),
                                                            'secuencial' => str_pad($infoTributaria['secuencial'], 9, 0, STR_PAD_LEFT),
                                                            'tipoDoc' => $data['infoTributaria']['codDoc'],
                                                            'fechaEmision' => $data['infoTributaria']['fecha_emision'],
                                                        ];
                                                        $this->enviar_correo($param);
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => 11,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Documento notificado',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        Ct_Nota_Debito_Cliente::setLogDocElectronico($arrayDato);
                                                    } else {
                                                        De_Documentos_Electronicos::updateXmlNoAutorizacion($comprobante, $respuestaAutorizacion);
                                                        Ct_Nota_Debito_Cliente::updateXmlNoAutorizacion($row->id);
                                                        $arrayLog = [
                                                            'id_de_documentos_electronicos' => $id_doc,
                                                            'descripcion_error' => json_encode($respuestaAutorizacion),
                                                            'id_documento' => $row->id,
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' =>  $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        De_Log_Error::setErrorLog($arrayLog);
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => 10,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Documento no autorizado',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        Ct_Nota_Debito_Cliente::setLogDocElectronico($arrayDato);
                                                    }
                                                } else {
                                                    $this->generarXmlRespuesta($respuesta);
                                                    De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                                    Ct_Nota_Debito_Cliente::updateXmlNoRecepcion($row->id);
                                                    $arrayLog = [
                                                        'id_de_documentos_electronicos' => $id_doc,
                                                        'descripcion_error' => json_encode($respuesta),
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' =>  $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    De_Log_Error::setErrorLog($arrayLog);
                                                    //return $respuesta['mensajesWs'];
                                                    $arrayDato = [
                                                        'tipo_documento' => $infoTributaria['codDoc'],
                                                        'id_pasos' => 9,
                                                        'id_documento' => $row->id,
                                                        'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                        'informacion' => 'Documento devuelto',
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' => $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    Ct_Nota_Debito_Cliente::setLogDocElectronico($arrayDato);
                                                }
                                            } else {
                                                //Log error al firmar XML
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($result),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $arrayDato = [
                                                    'tipo_documento' => $infoTributaria['codDoc'],
                                                    'id_pasos' => 6,
                                                    'id_documento' => $row->id,
                                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                    'informacion' => 'Documento sin firmar',
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' => $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                ];
                                                Ct_Nota_Debito_Cliente::setLogDocElectronico($arrayDato);
                                            }
                                        }
                                    } else {
                                        //log No genero XML
                                        $arrayLog = [
                                            'id_de_documentos_electronicos' => $id_doc,
                                            'descripcion_error' => 'Error al crear el documento XML',
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' =>  $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                        ];
                                        De_Log_Error::setErrorLog($arrayLog);
                                        $arrayDato = [
                                            'tipo_documento' => $infoTributaria['codDoc'],
                                            'id_pasos' => 7,
                                            'id_documento' => $row->id,
                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                            'informacion' => 'Documento sin generar xml',
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' => $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                        ];
                                        Ct_Nota_Debito_Cliente::setLogDocElectronico($arrayDato);
                                    }
                                } else {
                                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'error|' . $id_doc);
                                    echo json_encode($result);
                                    return;
                                }
                            }
                        }
                    }
                } else {
                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existen documentos para emitir', 'tipoDoc' => 'Nota de credito');
                    echo json_encode($result);
                    return;
                }
                $infoErrores = De_Documentos_Electronicos::revisionEstadosDocumentosErrores($idInsertados);
                $infoAutorizados = '';
                if ($claveInsertados != '')
                    $infoAutorizados = De_Documentos_Electronicos::revisionEstadosDocumentosAutorizados($claveInsertados);
                $result = array('code' => 200, 'state' => true, 'data' => ['errores:' => $infoErrores, 'autorizado:' => $infoAutorizados], 'message' => 'proceso ejecutado correctamente');
                echo json_encode($result);
            } else {
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                if (!$this->consultarEstadoSRI(session('id_empresa'))) {
                    $arrayLog = [
                        'id_de_documentos_electronicos' => $id_doc,
                        'descripcion_error' => json_encode(['Error:' => 'SRI sin servicio']),
                        'id_usuariomod' => $idUsuario,
                        'id_usuariocrea' =>  $idUsuario,
                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                        'id_documento' => $id_doc
                    ];
                    De_Log_Error::setErrorLog($arrayLog);
                    $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $arrayLog], 'message' => 'Error');
                    return json_encode($result);
                } else {
                    $cont = 0;
                    $empresa = Empresa::getEmpresa($dinfo['empresa']);
                    if ($empresa != '') {
                        $datosFirma = De_Empresa::getDatos($dinfo['empresa']);

                        $notaDebito = Ct_Nota_Debito_Cliente::where('id', $dinfo['idVenta'])->first();

                        $firma['ruta_firma'] = $datosFirma->ruta_firma;
                        $firma['clave_firma'] = $datosFirma->clave_firma;
                        $data['datosfirma'] = $firma;
                        $infoTributaria['fecha_emision'] = date('d/m/Y');
                        $infoTributaria['ambiente'] = 1;
                        $infoTributaria['tipoEmision'] = 1;
                        $infoTributaria['razonSocial'] = $empresa->razonsocial;
                        $infoTributaria['nombreComercial'] = $empresa->nombrecomercial;
                        $infoTributaria['ruc'] = $empresa->id;
                        $infoTributaria['codDoc'] = '01';
                        $infoTributaria['estab'] = str_pad($notaDebito->sucursal, 3, 0, STR_PAD_LEFT);
                        $infoTributaria['ptoEmi'] = str_pad($notaDebito->punto_emision, 3, 0, STR_PAD_LEFT);
                        $infoTributaria['dirMatriz'] = $empresa->direccion;
                        $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                        $datosTributarios = De_Info_Tributaria::reservarSecuencial(session('id_empresa'), $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                        if ($datosTributarios == '') {
                            $result = array('code' => 204, 'state' => true, 'data' => ['error:' => 'no se ha configurado los datos tributarios para esta empresa: ' . $empresa->id], 'message' => 'no existen datos tributarios para esta empresa');
                            echo json_encode($result);
                            return;
                        }
                        $secuencial = $datosTributarios->secuencial_nro;
                        $infoTributaria['ambiente'] = $datosFirma->ambiente;
                        $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                        $claveObj = new ClaveAcceso();
                        $campos = [
                            'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($notaDebito->fecha))),
                            'tipo_comprobante' => $infoTributaria['codDoc'],
                            'ruc' => $infoTributaria['ruc'],
                            'tipo_ambiente' => $infoTributaria['ambiente'],
                            'punto_establecimiento' => $infoTributaria['estab'],
                            'punto_emision' => $infoTributaria['ptoEmi'],
                            'numero_comprobante' => $infoTributaria['secuencial'],
                            'codigo_numerico' => $infoTributaria['secuencial'],
                            'tipo_emision' => $infoTributaria['tipoEmision']
                        ];
                        $claveObj->llenarDatos($campos);
                        $infoTributaria['claveAcceso'] = $claveObj->clave_acceso;
                        $data['infoTributaria'] = $infoTributaria;
                        //InfoTributaria
                        $infoNotaDebito['fechaEmision'] = $notaDebito->fecha;
                        $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                            ->where('codigo_sucursal', $infoTributaria['estab'])
                            ->first();

                        if (isset($sucursal->direccion_sucursal)) {
                            $infoNotaDebito['dirEstablecimiento'] = $sucursal->direccion_sucursal;
                        } else {
                            $infoNotaDebito['dirEstablecimiento'] = $empresa->direccion;
                        }
                        if ($datosFirma->contribuyente_especial != '' && $datosFirma->contribuyente_especial != 0) {
                            $infoNotaDebito['contribuyenteEspecial'] = $datosFirma->contribuyente_especial; //Opcional
                        }
                        $infoNotaDebito['obligadoContabilidad'] = 'NO'; //Opcional
                        if ($datosFirma->contabilidad == 1)
                            $infoNotaDebito['obligadoContabilidad'] = 'SI'; //Opcional
                        $cliente = Ct_Clientes::getCliente($notaDebito->id_cliente);

                        if ($cliente != '') {
                            $email = $cliente->email_representante;
                            $telefonoCliente = $cliente->telefono1;
                            $infoNotaDebito['tipoIdentificacionComprador'] = str_pad($cliente->tipo, 2, 0, STR_PAD_LEFT);
                            $infoNotaDebito['identificacionComprador'] = $cliente->identificacion;
                            $infoNotaDebito['razonSocialComprador'] = $cliente->nombre;
                            $infoNotaDebito['direccionComprador'] = $cliente->direccion_representante;
                        } else {
                            $infoNotaDebito['tipoIdentificacionComprador'] = null;
                            $infoNotaDebito['identificacionComprador'] = null;
                            $infoNotaDebito['razonSocialComprador'] = null;
                            $infoNotaDebito['direccionComprador'] = null; //Opcional
                        }
                        $infoNotaDebito['totalSinImpuestos'] = $notaDebito->subtotal_0 + $notaDebito->subtotal_12;
                        $infoNotaDebito['codDocModificado'] = '01';
                        $factura = null;
                        $idFactura = $notaDebito->id_factura;
                        $factura = Ct_Nota_Debito_Cliente::find($idFactura)->first();

                        if (!is_null($factura)) {
                            $infoNotaDebito['fechaEmisionDocSustento'] = $factura->fecha;
                            $infoNotaDebito['numDocModificado']  = $factura->nro_comprobante;
                        }
                        $infoNotaDebito['totalSinImpuestos'] = $notaDebito->subtotal0;
                        $infoNotaDebito['valorModificacion'] = $notaDebito->total_final;
                        $divisa = Ct_Divisas::getMoneda($factura->divisas); //Ct_Divisas::where('id',$factura->divisas);
                        $infoNotaDebito['moneda'] = $divisa;
                        $totalConImpuestos = [];
                        $totalConImpuesto = [];
                        $conImpuesto = 0;
                        // $details       = Ct_Detalle_Credito_Clientes::where('id_not_cred', $notaDebito->id)->first();
                        $detalleDebito = Ct_Detalle_Debito_Clientes::where('id_debito', $notaDebito->id)->get();
                        if (count($detalleDebito) > 0) {
                            foreach ($detalleDebito as $detail) {
                                $Impuesto['codigo'] = '2'; //IVA
                                if ($detail->iva == 1) {
                                    $Impuesto['codigoPorcentaje'] = '2'; //como se cuando son los otros tipos de iva 
                                    $Impuesto['valor'] = $this->getDecimal($detail->valor * $this->porIva->iva);
                                    $Impuesto['tarifa'] = '12';
                                    $Impuesto['baseImponible'] =  $this->getDecimal($detail->valor, 2);
                                    $Impuestos[$conImpuesto] = $Impuesto;
                                } else {
                                    $Impuesto['codigoPorcentaje'] = '0';
                                    $Impuesto['tarifa'] = '0';
                                    $Impuesto['valor'] = $this->getDecimal(0, 2);
                                    $Impuesto['baseImponible'] = $this->getDecimal($detail->valor, 2); // - $detail->descuento se quita hasta arreglar la nota de credito
                                    $Impuestos[$conImpuesto] = $Impuesto;
                                }
                                $conImpuesto++;
                            }
                        }
                        $infoNotaDebito['totalConImpuestos'] = $totalConImpuestos;
                        $infoNotaDebito['motivo'] = $notaDebito->concepto;
                        $data['infoNotaDebito'] = $infoNotaDebito;

                        //detalles
                        $detalle = [];
                        $detalles = [];
                        $conDetalle = 0;
                        if (count($detalleDebito) > 0) {
                            foreach ($detalleDebito as $deta) {
                                $detalle['p_impuesto'] = $deta->check_iva !== null ? ($this->porIva->iva * 100) : 0;
                                $detalle['codigoInterno'] = $deta->id_ct_productos;
                                $detalle['codigoAdicional'] = $deta->id_ct_productos;
                                $detalle['descripcion'] = $deta->nombre;
                                $detalle['cantidad'] = $deta->cantidad;
                                $detalle['precioUnitario'] = $deta->precio;
                                $detalle['descuento'] = $deta->descuento;
                                $detalle['precioTotalSinImpuesto'] = $notaDebito->sub_sin_imp;
                                $contAdicional = 0;
                                if ($deta->detalle != '') {
                                    $detAdicional = [
                                        'nombre' => 'detalle',
                                        'valor' => $deta->detalle
                                    ];
                                    $contAdicional++;
                                }
                                if ($deta->adicional != '') {
                                    $detAdicional_ = [
                                        'nombre' => 'adicional',
                                        'valor' => $deta->adicional
                                    ];
                                    $contAdicional++;
                                }
                                if ($contAdicional > 0) {
                                    $detallesAdicionales = [isset($detAdicional) ? $detAdicional : [], isset($detAdicional_) ? $detAdicional_ : []];
                                    $detalle['detallesAdicionales'] = $detallesAdicionales;
                                }
                                $detalles[$conDetalle] = $detalle;
                                $conDetalle++;
                            }
                        }
                        $data['detalles'] = $detalles;
                        $campoAdicional['nombre'] = "detalle";
                        $campoAdicional['valor']  = $notaDebito->nota_electronica;
                        $informacion_adicional[0] = $campoAdicional;
                        $infoAdicional['campoAdicional'] = $informacion_adicional;
                        $data['infoAdicional'] = $informacion_adicional;

                        $errores = $this->validarData($data);
                        if (count($errores) > 0) {
                            $arrayDocElec = [
                                'id_de_pasos' => 7,
                                'infoTributaria' => json_encode($data['infoTributaria']),
                                'infoNotaDebito' => json_encode($data['infoNotaDebito']),
                                'motivos' => json_encode($data['motivos']),
                                'infoAdicional' => json_encode($data['infoAdicional']),
                                'establecimiento' => str_pad($infoTributaria['estab'], 3, 0, STR_PAD_LEFT),
                                'emision' => str_pad($infoTributaria['ptoEmi'], 3, 0, STR_PAD_LEFT),
                                'secuencial' => str_pad($infoTributaria['secuencial'], 9, 0, STR_PAD_LEFT),
                                'ruc_receptor' => $infoTributaria['identificacionComprador'],
                                'ruc_emisor' => $infoTributaria['ruc'],
                                'id_usuariomod' => $idUsuario,
                                'id_usuariocrea' =>  $idUsuario,
                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                'id_documento' => $notaDebito->id,
                            ];
                            $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayDocElec, 7);
                            Ct_Nota_Debito_Cliente::updateSinGenerarXML($notaDebito->id);
                            $idInsertados .= $id_doc . ',';
                            $arrayLog = [
                                'id_de_documentos_electronicos' => $id_doc,
                                'descripcion_error' => json_encode($errores),
                                'id_usuariomod' => $idUsuario,
                                'id_usuariocrea' =>  $idUsuario,
                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                'id_documento' => $notaDebito->id
                            ];
                            De_Log_Error::setErrorLog($arrayLog);
                        }

                        //Creacion XML
                        $arrayLogError = [
                            'id_de_pasos' => 1,
                            'infoTributaria' => json_encode($data['infoTributaria']),
                            'infoFactura' => json_encode($data['infoFactura']),
                            'detalles' => json_encode($data['detalles']),
                            'infoAdicional' => json_encode($data['infoAdicional']),
                            'establecimiento' => str_pad($infoTributaria['estab'], 3, 0, STR_PAD_LEFT),
                            'emision' => str_pad($infoTributaria['ptoEmi'], 3, 0, STR_PAD_LEFT),
                            'secuencial' => str_pad($infoTributaria['secuencial'], 9, 0, STR_PAD_LEFT),
                            'ruc_receptor' =>  $infoTributaria['identificacionComprador'],
                            'ruc_emisor' => $infoTributaria['ruc'],
                            'id_usuariomod' => $idUsuario,
                            'id_usuariocrea' =>  $idUsuario,
                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                            'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                            'id_documento' => $notaDebito->id
                        ];
                        $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayLogError, 1);
                        if (is_numeric($id_doc)) {
                            $idInsertados .= $id_doc . ',';
                            $comprobante = $this->generarDocElectronicoXml($data);
                            Ct_Nota_Debito_Cliente::updateGenerarXML($notaDebito->id, $data['infoTributaria']['claveAcceso']);
                            if ($comprobante != '') {
                                $docAnte = simplexml_load_string($comprobante);
                                if (!empty($docAnte)) {
                                    //validar xsd
                                    $validacion = $this->validarXmlToXsd($comprobante);
                                    if ($validacion['isValidoXsd'] != 1) {
                                        $arrayLog = [
                                            'id_de_documentos_electronicos' => $id_doc,
                                            'descripcion_error' => json_encode($validacion),
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' =>  $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                        ];
                                        De_Log_Error::setErrorLog($arrayLog);
                                        $result = array('code' => 500, 'state' => true, 'data' => json_encode($validacion), 'message' => 'Error: al validar documento xml.');
                                        return json_encode($result);
                                    }
                                    De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                    Ct_Nota_Debito_Cliente::updateValidacionXSD($notaDebito->id);
                                    //firmar xml
                                    $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $datosFirma->ruta_firma; //firma.p12
                                    $password = $datosFirma->clave_firma; //'3duard0faustos';
                                    $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                                    if (!empty($result)) {
                                        De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                        Ct_Nota_Debito_Cliente::updateXmlFirmado($notaDebito->id);
                                        //recepcion sri
                                        $respuesta = $this->recibirWs($result, $data['infoTributaria']['ambiente']);
                                        De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                        Ct_Nota_Debito_Cliente::updateRecibidoSri($notaDebito->id);
                                        if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                            //$this->procesarRespuestaXml($respuesta);
                                            $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                            $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                            De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                            //autorzacion sri
                                            $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso, $data['infoTributaria']['ambiente']);
                                            $this->generarXmlAutorizacion($respuestaAutorizacion);
                                            De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                            Ct_Nota_Debito_Cliente::updateXmlAutorizacion($notaDebito->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $data['infoTributaria']['secuencial'], $docAnte->infoTributaria->claveAcceso);
                                            $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                            $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
                                            $param = [
                                                'email' => $notaDebito->email_traslado_destinatario,
                                                'nombre' => $notaDebito->razon_social_destinatario,
                                                'claveAcceso' => $docAnte->infoTributaria->claveAcceso,
                                                'estab' => $data['infoTributaria']['estab'],
                                                'ptoEmi' =>  $data['infoTributaria']['ptoEmi'],
                                                'secuencial' =>  $data['infoTributaria']['secuencial'],
                                                'tipoDoc' => $this->tipoDocumento
                                            ];
                                            $this->enviar_correo($param);
                                        } else {
                                            $this->generarXmlRespuesta($respuesta);
                                            De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                            Ct_Nota_Debito_Cliente::updateXmlNoRecepcion($notaDebito->id);
                                            $arrayLog = [
                                                'id_de_documentos_electronicos' => $id_doc,
                                                'descripcion_error' => json_encode($respuesta),
                                                'id_usuariomod' => $idUsuario,
                                                'id_usuariocrea' =>  $idUsuario,
                                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                            ];
                                            De_Log_Error::setErrorLog($arrayLog);
                                            //return $respuesta['mensajesWs'];
                                        }
                                    } else {
                                        //Log error al firmar XML
                                        $arrayLog = [
                                            'id_de_documentos_electronicos' => $id_doc,
                                            'descripcion_error' => json_encode($result),
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' =>  $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                        ];
                                        De_Log_Error::setErrorLog($arrayLog);
                                    }
                                }
                            } else {
                                //log No genero XML
                                $arrayLog = [
                                    'id_de_documentos_electronicos' => $id_doc,
                                    'descripcion_error' => 'Error al crear el documento XML',
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                ];
                                De_Log_Error::setErrorLog($arrayLog);
                            }
                        } else {
                            $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'error|' . $id_doc);
                            echo json_encode($result);
                            return;
                        }
                    }
                }
                $respuesta = new stdClass();
                $respuesta->nro_comprobante = $numDocumento;
                return $respuesta;
            }
        } catch (Exception $ex) {
            if ($id_doc != 0) {
                $arrayLog = [
                    'id_de_documentos_electronicos' => $id_doc,
                    'descripcion_error' => json_encode($ex->getMessage()),
                    'id_usuariomod' => $idUsuario,
                    'id_usuariocrea' =>  $idUsuario,
                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                ];
                De_Log_Error::setErrorLog($arrayLog);
            }
            $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $ex->getMessage(), 'linea' => $ex->getLine()], 'message' => 'Error');
            echo json_encode($result);
            return;
        }
    }
    public function getLiquidacion($dinfo = null, $idUsuario = 'FACELECTRO', $verificar = [])
    {
        $idLiquidacion = '';
        $esVerificacion = count($verificar) > 0 && isset($verificar['id']);
        if ($esVerificacion) {
            $idLiquidacion = $verificar['id'];
        }
        $data = [];
        $id_doc = 0;
        $numDocumento = '';
        try {
            if ($dinfo == null) {
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                $liquidacionCabecera = Ct_compras::getCompras($idLiquidacion);
                if (count($liquidacionCabecera) > 0) {
                    if (!$esVerificacion && !$this->consultarEstadoSRI($liquidacionCabecera[0]->id_empresa)) {
                        $arrayLog = [
                            'id_de_documentos_electronicos' => $id_doc,
                            'descripcion_error' => json_encode(['Error:' => 'SRI sin servicio']),
                            'id_usuariomod' => $idUsuario,
                            'id_usuariocrea' =>  $idUsuario,
                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                            'id_documento' => $id_doc
                        ];
                        De_Log_Error::setErrorLog($arrayLog);
                        $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $arrayLog], 'message' => 'Error');
                        echo json_encode($result);
                        return;
                    } else {
                        $cont = 0;
                        foreach ($liquidacionCabecera as $row) {
                            $cont++;
                            $empresa = Empresa::getEmpresa($row->id_empresa);
                            if ($empresa != '') {
                                $datosFirma = De_Empresa::getDatos($row->id_empresa);
                                $firma['ruta_firma'] = $datosFirma->ruta_firma;
                                $firma['clave_firma'] = $datosFirma->clave_firma;
                                $data['datosfirma'] = $firma;
                                $infoTributaria['fecha_emision'] = date('d/m/Y', strtotime($row->fecha));
                                $infoTributaria['ambiente'] = $datosFirma->ambiente;
                                $infoTributaria['tipoEmision'] = 1;
                                $infoTributaria['razonSocial'] = str_replace('&', 'Y', $empresa->razonsocial);
                                $infoTributaria['nombreComercial'] = str_replace('&', 'Y', $empresa->nombrecomercial);
                                $infoTributaria['ruc'] = $empresa->id;
                                $infoTributaria['codDoc'] = '03';
                                $infoTributaria['estab'] = str_pad($row->sucursal, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['ptoEmi'] = str_pad($row->punto_emision, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['dirMatriz'] = $empresa->direccion;
                                if (isset($datosFirma->tipo_rimpe) && $datosFirma->tipo_rimpe == 1)
                                    $infoTributaria['rimpe_emprendedor'] = $datosFirma->tipo_rimpe;
                                elseif (isset($datosFirma->tipo_rimpe) && $datosFirma->tipo_rimpe == 0)
                                    $infoTributaria['rimpe_popular'] = $datosFirma->tipo_rimpe;
                                $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                                if ($idDoc == '') {
                                    $result = array('code' => 204, 'state' => true, 'data' => ['error:' => 'No existe el documento Liquidacion de compra en el sistema'], 'message' => 'no existen datos tributarios para esta empresa');
                                    echo json_encode($result);
                                    return;
                                }
                                if ($esVerificacion) {
                                    $establecimientoValidar = strlen((string) $row->nro_autorizacion) === 49
                                        ? substr($row->nro_autorizacion, 24, 3)
                                        : $infoTributaria['estab'];
                                    $emisionValidar = strlen((string) $row->nro_autorizacion) === 49
                                        ? substr($row->nro_autorizacion, 27, 3)
                                        : $infoTributaria['ptoEmi'];
                                    $datosTributarios = De_Info_Tributaria::getDatos($row->id_empresa, $establecimientoValidar, $emisionValidar, $idDoc);
                                } elseif (strlen((string) $row->nro_autorizacion) === 49 && ctype_digit((string) $row->nro_autorizacion)) {
                                    $datosTributarios = De_Info_Tributaria::sincronizarSecuencialAsignado(
                                        $row->id_empresa,
                                        substr($row->nro_autorizacion, 24, 3),
                                        substr($row->nro_autorizacion, 27, 3),
                                        $idDoc,
                                        substr($row->nro_autorizacion, 30, 9)
                                    );
                                } else {
                                    $datosTributarios = De_Info_Tributaria::reservarSecuencial($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                                }
                                if ($datosTributarios == '') {
                                    $result = array('code' => 204, 'state' => true, 'data' => ['error:' => 'no se ha configurado los secuenciales para el documento ' . $infoTributaria['codDoc'] . ' para esta empresa: ' . $empresa->id], 'message' => 'no se ha configurado los secuenciales para el documento ' . $infoTributaria['codDoc']);
                                    echo json_encode($result);
                                    return;
                                }
                                $secuencial = $esVerificacion
                                    ? ((int) $datosTributarios->secuencial_nro + 1)
                                    : $datosTributarios->secuencial_nro;
                                $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                                $claveObj = new ClaveAcceso();
                                if ($row->nro_autorizacion == '') {
                                    $campos = [
                                        'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($row->fecha))),
                                        'tipo_comprobante' => $infoTributaria['codDoc'],
                                        'ruc' => $infoTributaria['ruc'],
                                        'tipo_ambiente' => $infoTributaria['ambiente'],
                                        'punto_establecimiento' => $infoTributaria['estab'],
                                        'punto_emision' => $infoTributaria['ptoEmi'],
                                        'numero_comprobante' => $infoTributaria['secuencial'],
                                        'codigo_numerico' => $infoTributaria['secuencial'],
                                        'tipo_emision' => $infoTributaria['tipoEmision']
                                    ];
                                    $claveObj->llenarDatos($campos);
                                } else {
                                    $xml = $row->nro_autorizacion;
                                    $esta = substr($xml, 24, 3);
                                    $emis = substr($xml, 27, 3);
                                    $num = substr($xml, 30, 9);
                                    $infoTributaria['estab'] = $esta;
                                    $infoTributaria['ptoEmi'] = $emis;
                                    $infoTributaria['secuencial'] = $num;
                                }
                                $infoTributaria['claveAcceso'] = $row->nro_autorizacion != '' ? $row->nro_autorizacion : $claveObj->clave_acceso;
                                $data['infoTributaria'] = $infoTributaria;
                                //Info LiquidacionCompra
                                $infoLiquidacionCompra['fechaEmision'] = $row->fecha;
                                $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                                    ->where('codigo_sucursal', $infoTributaria['estab'])
                                    ->first();
                                if (isset($sucursal->direccion_sucursal)) {
                                    $infoLiquidacionCompra['dirEstablecimiento'] = $sucursal->direccion_sucursal;
                                } else {
                                    $infoLiquidacionCompra['dirEstablecimiento'] = $empresa->direccion;
                                }
                                if ($datosFirma->contribuyente_especial != '' && $datosFirma->contribuyente_especial != 0) {
                                    $infoLiquidacionCompra['contribuyenteEspecial'] = $datosFirma->contribuyente_especial; //Opcional
                                }
                                $infoLiquidacionCompra['obligadoContabilidad'] = 'NO';
                                if ($datosFirma->contabilidad == 1)
                                    $infoLiquidacionCompra['obligadoContabilidad'] = 'SI'; //Opcional
                                $telefonoCliente = '';
                                $direccionCliente = '';
                                $emailCliente = '';
                                $proveedor = Proveedor::getProveedor($row->proveedor);
                                if ($proveedor != '') {
                                    if (isset($proveedor->email)) {
                                        $emailproveedor = $proveedor->email;
                                    }
                                    if (isset($proveedor->telefono1)) {
                                        $telefonoproveedor = $proveedor->telefono1;
                                    }
                                    if (isset($proveedor->direccion)) {
                                        $direccionproveedor = $proveedor->direccion;
                                    }
                                    $infoLiquidacionCompra['tipoIdentificacionProveedor'] = $proveedor->tipo != '' ? str_pad($proveedor->tipo, 2, 0, STR_PAD_LEFT) : '05';
                                    $infoLiquidacionCompra['razonSocialProveedor'] = $proveedor->nombrecomercial;
                                    $infoLiquidacionCompra['identificacionProveedor'] = $proveedor->id;
                                    if ($direccionproveedor != '') {
                                        $infoLiquidacionCompra['direccionProveedor'] = $direccionproveedor;
                                    }
                                } else {
                                    $infoLiquidacionCompra['tipoIdentificacionProveedor'] = '05';
                                    $infoLiquidacionCompra['razonSocialProveedor'] = null;
                                    $infoLiquidacionCompra['identificacionProveedor'] = null;
                                }
                                $infoLiquidacionCompra['totalSinImpuestos'] = $row->subtotal;
                                $infoLiquidacionCompra['totalDescuento'] = $row->descuento;
                                if (isset($infoLiquidacionCompra['codDocReembolso'])) {
                                    $infoLiquidacionCompra['codDocReembolso'] = '41';
                                    $infoLiquidacionCompra['totalComprobantesReembolso'] = '0';
                                    $infoLiquidacionCompra['totalBaseImponibleReembolso'] = '0';
                                    $infoLiquidacionCompra['totalImpuestoReembolso'] = '0';
                                }
                                $totalConImpuestos = [];
                                $totalConImpuesto = [];
                                $conImpuesto = 0;
                                // $details       = Ct_Detalle_Credito_Clientes::where('id_not_cred', $row->id)->first();
                                $detalle_liquidacion = Ct_detalle_compra::where('id_ct_compras', $row->id)->get();
                                if (count($detalle_liquidacion) > 0) {
                                    $baseI = 0;
                                    $ivaT = 0;
                                    $imp = 0;
                                    foreach ($detalle_liquidacion as $conimpuesto) {
                                        if ($conimpuesto->iva == 1) {
                                            $imp++;
                                            $codigoImpuesto = De_Codigo_Impuestos::where('porcentaje', ($this->porIva->iva * 100))->where('codigo_impuesto', 2)->first();
                                            $totalConImpuesto['codigo'] = !EmisionDocumentosController::validarPeriodo($row->fecha) ? 2 : $codigoImpuesto->codigo_impuesto;
                                            $totalConImpuesto['codigoPorcentaje'] = !EmisionDocumentosController::validarPeriodo($row->fecha) ? 2 : $codigoImpuesto->codigo;
                                            $baseI += ($conimpuesto->cantidad * $conimpuesto->precio) - $conimpuesto->descuento;
                                            $totalConImpuesto['baseImponible'] = $this->getDecimal($baseI);
                                            $totalConImpuesto['tarifa'] =  !EmisionDocumentosController::validarPeriodo($row->fecha) ? 12 : ($this->porIva->iva * 100);
                                            $ivaT += (($conimpuesto->cantidad * $conimpuesto->precio) - $conimpuesto->descuento) * $conimpuesto->porcentaje;
                                            $totalConImpuesto['valor'] = $this->getDecimal($ivaT);
                                        }
                                    }
                                    if (count($totalConImpuesto) > 0 && $imp > 0)
                                        array_push($totalConImpuestos, $totalConImpuesto);
                                }
                                if (count($detalle_liquidacion) > 0) {
                                    $baseI = 0;
                                    $ivaT = 0;
                                    $imp = 0;
                                    $codigoImpuesto = '';
                                    $totalConImpuesto = [];
                                    $impuesto['codigoPorcentaje'] = "0";
                                    foreach ($detalle_liquidacion as $sinimpuesto) {
                                        if ($sinimpuesto->iva == 0) {
                                            $imp++;
                                            $totalConImpuesto['codigo'] = 2;
                                            $totalConImpuesto['codigoPorcentaje'] = "0";
                                            $baseI += ($sinimpuesto->cantidad * $sinimpuesto->precio) - $sinimpuesto->descuento;
                                            $totalConImpuesto['baseImponible'] = $this->getDecimal($baseI);
                                            $totalConImpuesto['tarifa'] = 0;
                                            $totalConImpuesto['valor'] = $this->getDecimal(0, 2);
                                        }
                                    }
                                    if (count($totalConImpuesto) > 0 && $imp > 0)
                                        array_push($totalConImpuestos, $totalConImpuesto);
                                }
                                $infoLiquidacionCompra['totalConImpuestos'] = $totalConImpuestos;
                                $infoLiquidacionCompra['importeTotal'] = $row->total_final;
                                $infoLiquidacionCompra['moneda'] = 'dolar'; //$divisa;
                                $pagos = [];
                                $pago = [];
                                $codPago = isset($row->forma_pago) != '' ? ($row->forma_pago != '' ? $row->forma_pago : '20') : '20';
                                $pago['formaPago'] = str_pad($codPago, 2, 0, STR_PAD_LEFT);
                                $pago['total'] = $this->getDecimal($row->total_final, 2);
                                $pago['plazo'] = (string)$row->dias;
                                $pago['unidadTiempo'] = 'dias';
                                array_push($pagos, $pago);
                                $infoLiquidacionCompra['pagos'] = $pagos;
                                $data['infoLiquidacionCompra'] = $infoLiquidacionCompra;
                                //detalles
                                $detalle = [];
                                $detalles = [];
                                $impuestos = [];
                                $impuesto = [];
                                $conDetalle = 0;
                                $detalle_liquidacion = Ct_detalle_compra::getDetalles($row->id);
                                $contImpuestos = 0;
                                if (count($detalle_liquidacion) > 0) {
                                    foreach ($detalle_liquidacion as $deta) {
                                        $detalle['p_impuesto'] = $deta->iva != null ? (!EmisionDocumentosController::validarPeriodo($row->fecha) ? 12 : $this->porIva->iva * 100) : 0;
                                        $detalle['codigoPrincipal'] = $deta->codigo;
                                        $detalle['codigoAuxiliar'] = $deta->codigo;
                                        $detalle['descripcion'] = preg_replace("[\n|\r|\n\r]", "", $deta->descripcion != '' ? $deta->descripcion : $deta->nombre);
                                        $detalle['cantidad'] = $deta->cantidad;
                                        $detalle['precioUnitario'] = $deta->precio;
                                        $detalle['descuento'] = $deta->descuento;
                                        $detalle['precioTotalSinImpuesto'] = $deta->extendido;
                                        $contAdicional = 0;
                                        if ($deta->detalle != '') {
                                            $detAdicional = [
                                                'nombre' => 'detalle',
                                                'valor' => str_replace('&', 'Y', $deta->detalle)
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($deta->codigoref != '') {
                                            $detAdicional_ = [
                                                'nombre' => 'adicional1',
                                                'valor' => str_replace('&', 'Y', $deta->codigoref)
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($deta->nombre != '') {
                                            $detAdicional_ = [
                                                'nombre' => 'nombre',
                                                'valor' => str_replace('&', 'Y', $deta->nombre)
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($contAdicional < 3) {
                                            if ($deta->bodega != '') {
                                                $detAdicional_ = [
                                                    'nombre' => 'bodega',
                                                    'valor' => str_replace('&', 'Y', $deta->bodega)
                                                ];
                                                $contAdicional++;
                                            }
                                        }
                                        if ($contAdicional > 0) {
                                            $detallesAdicionales = [isset($detAdicional) ? $detAdicional : [], isset($detAdicional_) ? $detAdicional_ : []];
                                            $detalle['detallesAdicionales'] = $detallesAdicionales;
                                        }
                                        if (!is_null($deta->iva)) {
                                            if ($deta->iva == 1) {
                                                $codigoImpuesto = !EmisionDocumentosController::validarPeriodo($row->fecha) ? De_Codigo_Impuestos::where('porcentaje', (0.12 * 100))->where('codigo_impuesto', 2)->first() : De_Codigo_Impuestos::where('porcentaje', ($this->porIva->iva * 100))->where('codigo_impuesto', 2)->first();
                                                $impuesto['codigo'] = $deta->iva != 1 && $deta->iva != null  ? 0 : $codigoImpuesto->codigo_impuesto;
                                                $impuesto['codigoPorcentaje'] = $codigoImpuesto->codigo;
                                                $impuesto['tarifa'] = !EmisionDocumentosController::validarPeriodo($row->fecha) ? 12 : ($this->porIva->iva * 100);
                                                $impuesto['baseImponible'] = $deta->extendido;
                                                $impuesto['valor'] = $this->getDecimal($deta->extendido * (!EmisionDocumentosController::validarPeriodo($row->fecha) ? 0.12 : ($this->porIva->iva)));
                                                $impuestos[0] = $impuesto;
                                                $contImpuestos++;
                                            } else {
                                                $impuesto['codigo'] = '2';
                                                $impuesto['codigoPorcentaje'] = '0';
                                                $impuesto['tarifa'] = '0';
                                                $impuesto['baseImponible'] = $deta->extendido;
                                                $impuesto['valor'] = '0';
                                                $impuestos[0] = $impuesto;
                                            }
                                        } else {
                                            $impuesto['codigo'] = '2';
                                            $impuesto['codigoPorcentaje'] = '0';
                                            $impuesto['tarifa'] = '0';
                                            $impuesto['baseImponible'] = $deta->extendido;
                                            $impuesto['valor'] = '0';
                                            $impuestos[0] = $impuesto;
                                        }
                                        $detalle['impuestos'] = $impuestos;
                                        $detalles[$conDetalle] = $detalle;
                                        $conDetalle++;
                                    }
                                }
                                $data['detalles'] = $detalles;
                                $cont = 0;
                                if (isset($row->observacion) && $row->observacion != '') {
                                    $campoAdicional['nombre'] = "observacion";
                                    $campoAdicional['valor'] =  $row->observacion;
                                    $informacion_adicional[0] = $campoAdicional;
                                    $cont++;
                                }
                                if ($emailproveedor != '') {
                                    $campoAdicional['nombre'] = 'email';
                                    $campoAdicional['valor']  = $emailproveedor;
                                    $informacion_adicional[1] = $campoAdicional;
                                    $cont++;
                                }
                                $infoAdicional['campoAdicional'] = $informacion_adicional;
                                $data['infoAdicional'] = $informacion_adicional;
                            } else {
                                $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existe la empresa', 'tipoDoc' => 'Liquidacion');
                                echo json_encode($result);
                                return;
                            }
                            $errores = $this->validarData($data);
                            if (count($verificar) > 0 && isset($verificar['id'])) {
                                $dataError = [
                                    'data' => $data,
                                    'errors' => $errores
                                ];
                                return $dataError;
                            }
                            /*echo '<pre>';
                            print_r($data);
                            print_r($errores);
                            exit;*/
                            /*if ($empresa->id == '0993075000001') {
                                echo '<pre>';
                                print_r($data);
                                print_r($errores);
                                exit;
                            }*/
                            if (count($errores) > 0) {
                                $arrayDocElec = [
                                    'id_de_pasos' => 7,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoLiquidacionCompra' => json_encode($data['infoLiquidacionCompra']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' => $infoTributaria['identificacionComprador'],
                                    'ruc_emisor' => $infoTributaria['ruc'],
                                    'reembolsos' => $data['reembolsos'],
                                    'maquiFiscal' => $data['maquiFiscal'],
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                    'id_documento' => $row->id,
                                ];
                                $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayDocElec, 7);
                                Ct_compras::updateSinGenerarXML($row->id);
                                $idInsertados .= $id_doc . ',';
                                $arrayLog = [
                                    'id_de_documentos_electronicos' => $id_doc,
                                    'descripcion_error' => json_encode($errores),
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'id_documento' => $row->id
                                ];
                                De_Log_Error::setErrorLog($arrayLog);
                            } else {
                                //Creacion XML
                                $arrayLogError = [
                                    'id_de_pasos' => 1,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoFactura' => json_encode($data['infoFactura']),
                                    'detalles' => json_encode($data['detalles']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' =>  $infoTributaria['identificacionComprador'],
                                    'ruc_emisor' => $infoTributaria['ruc'],
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                    'id_documento' => $row->id
                                ];
                                $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayLogError, 1);
                                if (is_numeric($id_doc)) {
                                    $idInsertados .= $id_doc . ',';
                                    $comprobante = $this->generarDocElectronicoXml($data);
                                    /*header('Content-type: text/xml');
                                    echo $comprobante;
                                    exit;*/
                                    Ct_compras::updateGenerarXML($row->id, $data['infoTributaria']['claveAcceso']);
                                    $arrayDato = [
                                        'tipo_documento' => $infoTributaria['codDoc'],
                                        'id_pasos' => 1,
                                        'id_documento' => $row->id,
                                        'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                        'informacion' => 'Documento generado',
                                        'id_usuariomod' => $idUsuario,
                                        'id_usuariocrea' => $idUsuario,
                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                        'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                    ];
                                    Ct_compras::setLogDocElectronico($arrayDato);
                                    if ($comprobante != '') {
                                        $docAnte = simplexml_load_string($comprobante);
                                        //dd($docAnte);
                                        if (!empty($docAnte)) {
                                            //validar xsd
                                            $validacion = $this->validarXmlToXsd($comprobante);
                                            if ($validacion['isValidoXsd'] != 1) {
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($validacion),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $result = array('code' => 204, 'state' => true, 'data' => json_encode($validacion), 'message' => 'ok|No existen documentos para emitir', 'tipoDoc' => 'Liquidacion');
                                                echo json_encode($result);
                                                return;
                                            }
                                            De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                            Ct_compras::updateValidacionXSD($row->id);
                                            $arrayDato = [
                                                'tipo_documento' => $infoTributaria['codDoc'],
                                                'id_pasos' => 2,
                                                'id_documento' => $row->id,
                                                'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                'informacion' => 'Documento validado por XSD',
                                                'id_usuariomod' => $idUsuario,
                                                'id_usuariocrea' => $idUsuario,
                                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                            ];
                                            Ct_compras::setLogDocElectronico($arrayDato);
                                            //firmar xml
                                            $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $datosFirma->ruta_firma; //firma.p12
                                            $password = $datosFirma->clave_firma; //'3duard0faustos';
                                            $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                                            if (!empty($result)) {
                                                De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                                Ct_compras::updateXmlFirmado($row->id);
                                                $arrayDato = [
                                                    'tipo_documento' => $infoTributaria['codDoc'],
                                                    'id_pasos' => 3,
                                                    'id_documento' => $row->id,
                                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                    'informacion' => 'Documento firmado',
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' => $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                ];
                                                Ct_compras::setLogDocElectronico($arrayDato);
                                                //recepcion sri
                                                $respuesta = $this->recibirWs($result, $data['infoTributaria']['ambiente']);
                                                De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                                Ct_compras::updateRecibidoSri($row->id);
                                                $arrayDato = [
                                                    'tipo_documento' => $infoTributaria['codDoc'],
                                                    'id_pasos' => 4,
                                                    'id_documento' => $row->id,
                                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                    'informacion' => 'Documento recibido',
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' => $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                ];
                                                /*if ($empresa->id == '0993075000001') {
                                                    echo '<pre>';
                                                    print_r($data);
                                                    print_r($arrayDato);
                                                    exit;
                                                }*/
                                                Ct_compras::setLogDocElectronico($arrayDato);
                                                if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                                    //$this->procesarRespuestaXml($respuesta);
                                                    $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                                    $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                                    De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                    //autorzacion sri
                                                    $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso, $data['infoTributaria']['ambiente']);
                                                    $estadoAutorizacion = $this->generarXmlAutorizacion($respuestaAutorizacion);
                                                    if ($estadoAutorizacion == 'AUTORIZADO') {
                                                        De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                                        Ct_compras::updateXmlAutorizacion($row->id, $docAnte->infoTributaria->claveAcceso, str_pad($secuancialDocumento, 9, 0, STR_PAD_LEFT), $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi']);
                                                        $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => 5,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Documento autorizado',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        Ct_compras::setLogDocElectronico($arrayDato);
                                                        $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
                                                        $param = [
                                                            'email' => $row->email,
                                                            'nombre' => $row->razonsocial,
                                                            'claveAcceso' => $docAnte->infoTributaria->claveAcceso,
                                                            'estab' => $data['infoTributaria']['estab'],
                                                            'ptoEmi' =>  $data['infoTributaria']['ptoEmi'],
                                                            'secuencial' =>  $data['infoTributaria']['secuencial'],
                                                            'tipoDoc' => $data['infoTributaria']['codDoc'],
                                                            'fechaEmision' => $data['infoTributaria']['fecha_emision'],
                                                        ];
                                                        $this->enviar_correo($param);
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => 11,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Documento notificado',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        Ct_compras::setLogDocElectronico($arrayDato);
                                                    } else {
                                                        De_Documentos_Electronicos::updateXmlNoAutorizacion($comprobante, $respuestaAutorizacion);
                                                        Ct_compras::updateXmlNoAutorizacion($row->id);
                                                        $arrayLog = [
                                                            'id_de_documentos_electronicos' => $id_doc,
                                                            'descripcion_error' => json_encode($respuestaAutorizacion),
                                                            'id_documento' => $row->id,
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' =>  $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        De_Log_Error::setErrorLog($arrayLog);
                                                        $arrayDato = [
                                                            'tipo_documento' => $infoTributaria['codDoc'],
                                                            'id_pasos' => 10,
                                                            'id_documento' => $row->id,
                                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                            'informacion' => 'Documento no autorizado',
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' => $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        Ct_compras::setLogDocElectronico($arrayDato);
                                                    }
                                                } else {
                                                    $this->generarXmlRespuesta($respuesta);
                                                    De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                                    Ct_compras::updateXmlNoRecepcion($row->id);
                                                    $arrayLog = [
                                                        'id_de_documentos_electronicos' => $id_doc,
                                                        'descripcion_error' => json_encode($respuesta),
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' =>  $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    De_Log_Error::setErrorLog($arrayLog);
                                                    //return $respuesta['mensajesWs'];
                                                    $arrayDato = [
                                                        'tipo_documento' => $infoTributaria['codDoc'],
                                                        'id_pasos' => 9,
                                                        'id_documento' => $row->id,
                                                        'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                        'informacion' => 'Documento devuelto',
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' => $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    Ct_compras::setLogDocElectronico($arrayDato);
                                                }
                                            } else {
                                                //Log error al firmar XML
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($result),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $arrayDato = [
                                                    'tipo_documento' => $infoTributaria['codDoc'],
                                                    'id_pasos' => 6,
                                                    'id_documento' => $row->id,
                                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                                    'informacion' => 'Documento sin firmar',
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' => $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                                ];
                                                Ct_compras::setLogDocElectronico($arrayDato);
                                            }
                                        }
                                    } else {
                                        //log No genero XML
                                        $arrayLog = [
                                            'id_de_documentos_electronicos' => $id_doc,
                                            'descripcion_error' => 'Error al crear el documento XML',
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' =>  $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                        ];
                                        De_Log_Error::setErrorLog($arrayLog);
                                        $arrayDato = [
                                            'tipo_documento' => $infoTributaria['codDoc'],
                                            'id_pasos' => 7,
                                            'id_documento' => $row->id,
                                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                            'informacion' => 'Documento sin generar xml',
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' => $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' => $_SERVER['REMOTE_ADDR'],
                                        ];
                                        Ct_compras::setLogDocElectronico($arrayDato);
                                    }
                                } else {
                                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'error|' . $id_doc);
                                    echo json_encode($result);
                                    return;
                                }
                            }
                        }
                    }
                } else {
                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existen documentos para emitir', 'tipoDoc' => 'Liquidacion');
                    echo json_encode($result);
                    return;
                }
                $infoErrores = De_Documentos_Electronicos::revisionEstadosDocumentosErrores($idInsertados);
                $infoAutorizados = '';
                if ($claveInsertados != '')
                    $infoAutorizados = De_Documentos_Electronicos::revisionEstadosDocumentosAutorizados($claveInsertados);
                $result = array('code' => 200, 'state' => true, 'data' => ['errores:' => $infoErrores, 'autorizado:' => $infoAutorizados], 'message' => 'proceso ejecutado correctamente');
                echo json_encode($result);
            } else {
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                if (!$this->consultarEstadoSRI(session('id_empresa'))) {
                    $arrayLog = [
                        'id_de_documentos_electronicos' => $id_doc,
                        'descripcion_error' => json_encode(['Error:' => 'SRI sin servicio']),
                        'id_usuariomod' => $idUsuario,
                        'id_usuariocrea' =>  $idUsuario,
                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                        'id_documento' => $id_doc
                    ];
                    De_Log_Error::setErrorLog($arrayLog);
                    $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $arrayLog], 'message' => 'Error');
                    return json_encode($result);
                }
                $respuesta = new stdClass();
                $respuesta->nro_comprobante = $numDocumento;
                return $respuesta;
            }
        } catch (Exception $ex) {
            if ($id_doc != 0) {
                $arrayLog = [
                    'id_de_documentos_electronicos' => $id_doc,
                    'descripcion_error' => json_encode($ex->getMessage()),
                    'id_usuariomod' => $idUsuario,
                    'id_usuariocrea' =>  $idUsuario,
                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                ];
                De_Log_Error::setErrorLog($arrayLog);
            }
            $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $ex->getMessage(), 'linea' => $ex->getLine()], 'message' => 'Error');
            echo json_encode($result);
            return;
        }
    }
    public static function obtenercomprobanteSRI($clave = '', $para = '')
    {
        $html = '';
        $servicio = "https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl";
        $parametros = array();
        $parametros['claveAccesoComprobante'] = $clave;
        try {
            $client = new SoapClient($servicio, $parametros);
            $result = $client->autorizacionComprobante($parametros);
        } catch (Exception $e) {
            $client = new SoapClient($servicio, $parametros);
            $result = $client->autorizacionComprobante($parametros);
        }
        if ($para == '') {
            return $result;
        } else {
            try {
                $docCod = substr($clave, 8, 2);
                $esta = substr($clave, 24, 3);
                $emis = substr($clave, 27, 3);
                $num = substr($clave, 30, 9);
                $empresa = Empresa::where('id', session('id_empresa'))->first();
                if ($result->RespuestaAutorizacionComprobante->numeroComprobantes > 0) {
                    $cuerpo = '';
                    $comprobante = simplexml_load_string($result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->comprobante);
                    $tipoDoc = '';
                    if ($docCod == '01') {
                        $tipoDoc = 'Factura';
                        $totalPagar = 0;
                        $cuerpo = '<tr><td colspan="2"><hr/></td></tr><tr><td colspan="2" align="center"><table width="98%">';
                        $cuerpo .= '<tr><td>Emisor: </td><td>' . $comprobante->infoTributaria->razonSocial . '</td></tr>
                        <tr><td>Ruc: </td><td colspan="6">' . $comprobante->infoTributaria->ruc . '</td></tr>
                        <tr><td>Comprador: </td><td colspan="6">' . $comprobante->infoFactura->razonSocialComprador . '</td></tr>
                        <tr><td>Fecha Emision: </td><td>' . $comprobante->infoFactura->fechaEmision . '</td></tr>';
                        $cuerpo .= '<tr><td width="100%" colspan="2"><table width="100%"><tr><td>Codigo P</td>';
                        if (isset($comprobante->detalles->detalle[0]->codigoAuxiliar)) {
                            $cuerpo .= '<td>Codigo A</td>';
                        }
                        $cuerpo .= '<td>Descripción</td>
                        <td>Cantidad</td>
                        <td>Precio</td>
                        <td>Descuento</td>
                        <td>Total</td>';
                        $cuerpo .= '</tr>';
                        $subtotal0 = 0;
                        $subtotal12 = 0;
                        $iva = 0;
                        $codAux = '';
                        foreach ($comprobante->detalles->detalle as $row) {
                            $codAux = '';
                            if (isset($row->codigoAuxiliar)) {
                                $codAux = '<td style="border-bottom:1px solid gray;">' . $row->codigoAuxiliar . '</td>';
                            }
                            if ($row->impuestos->impuesto->tarifa > 0) {
                                $subtotal12 += (float)$row->impuestos->impuesto->baseImponible;
                                $iva += ((float)$row->impuestos->impuesto->baseImponible * (float)$row->impuestos->impuesto->tarifa) / 100;
                            } else {
                                $subtotal0 += $row->impuestos->impuesto->baseImponible;
                            }
                            $cuerpo .= '<tr>
                            <td style="border-bottom:1px solid gray;">' . $row->codigoPrincipal . '</td>
                            ' . $codAux . '
                            <td style="border-bottom:1px solid gray;">' . $row->descripcion . '</td>
                            <td style="text-align:right;border-bottom:1px solid gray;">' . $row->cantidad . '</td>
                            <td style="text-align:right;border-bottom:1px solid gray;">$ ' . $row->precioUnitario . '</td>
                            <td style="text-align:right;border-bottom:1px solid gray;">$ ' . $row->descuento . '</td>
                            <td style="text-align:right;border-bottom:1px solid gray;">$ ' . $row->precioTotalSinImpuesto . '</td>
                            </tr>';
                        }
                        $tr = '';
                        if ($codAux != '')
                            $tr = '<td></td>';
                        $apagar = (float)$subtotal12 + (float)$subtotal0 + (float)$comprobante->infoFactura->totalDescuento + (float)$iva;
                        $cuerpo .= '<tr>' . $tr . '<td colspan="4"></td><td>Tarifa 0%: </td><td style="text-align:right;">$ ' . (float)$subtotal0 . '</td></tr>
                        <tr>' . $tr . '<td colspan="4"></td><td>Tarifa: </td><td style="text-align:right;">$ ' . number_format((float)$subtotal12, 2) . '</td></tr>
                        <tr>' . $tr . '<td colspan="4"></td><td>Subtotal: </td><td style="text-align:right;">$ ' . number_format(((float)$subtotal12 + (float)$subtotal0), 2) . '</td></tr>
                        <tr>' . $tr . '<td colspan="4"></td><td>Descuento: </td><td style="text-align:right;">$ ' . number_format((float)$comprobante->infoFactura->totalDescuento, 2) . '</td></tr>
                        <tr>' . $tr . '<td colspan="4"></td><td>Impuesto: </td><td style="text-align:right;">$ ' . number_format($iva, 2) . '</td></tr>
                        <tr>' . $tr . '<td colspan="4"></td><td>A pagar: </td><td style="text-align:right;">$ ' . number_format($apagar, 2) . '</td></tr>';
                        $cuerpo .= '</table></td></tr>';
                        //$cuerpo .= '<tr><td colspan="">&nbsp;</td><td>Total a Pagar: </td><td style="text-align:right;">$ ' . number_format($totalPagar, 2) . '</td></tr></table></td></tr>';
                    }
                    if ($docCod == '03')
                        $tipoDoc = 'Liquidación de compra';
                    if ($docCod == '04') {
                        $tipoDoc = 'Nota de crédito';
                        $cuerpo = '<tr><td colspan="2"><hr/></td></tr><tr><td colspan="2" align="center"><table width="98%">';
                        $cuerpo .= '<tr><td>Emisor: </td><td>' . $comprobante->infoTributaria->razonSocial . '</td></tr>
                        <tr><td>Ruc: </td><td colspan="6">' . $comprobante->infoTributaria->ruc . '</td></tr>
                        <tr><td>Comprador: </td><td colspan="6">' . $comprobante->infoFactura->razonSocialComprador . '</td></tr>
                        <tr><td>Fecha Emision: </td><td>' . $comprobante->infoNotaCredito->fechaEmision . '</td></tr>';

                        $cuerpo .= '<tr><td>Comprador: </td><td colspan="6">' . $comprobante->infoNotaCredito->razonSocialComprador . '</td></tr>
                        <tr><td>Documento mod: </td><td>' . $comprobante->infoNotaCredito->numDocModificado . '</td></tr>';
                    }
                    if ($docCod == '05')
                        $tipoDoc = 'Nota de débito';
                    if ($docCod == '06')
                        $tipoDoc = 'Guía de remisión';
                    if ($docCod == '07') {
                        $tipoDoc = 'Retención';
                        $totalRetenido = 0;
                        $cuerpo = '<tr><td colspan="2"><hr/></td></tr><tr><td colspan="2" align="center"><table width="80%">';
                        try {
                            foreach ($comprobante->docsSustento->docSustento->retenciones->retencion as $row) {
                                $totalRetenido += (float)$row->valorRetenido;
                                $cuerpo .= '<tr><td>Codigo: </td><td style="text-align:center;">' . $row->codigoRetencion . '</td><td>Base Imponible: </td><td style="text-align:right;">$ ' . $row->baseImponible . '</td><td>Procentaje: </td><td style="text-align:center;">' . $row->porcentajeRetener . '</td><td>Retenido: </td><td style="text-align:right;">$ ' . $row->valorRetenido . '</td></tr>';
                            }
                        } catch (Exception $e) {
                            foreach ($comprobante->impuestos->impuesto as $row) {
                                $totalRetenido += (float)$row->valorRetenido;
                                $cuerpo .= '<tr><td>Codigo: </td><td style="text-align:center;">' . $row->codigoRetencion . '</td><td>Base Imponible: </td><td style="text-align:right;">$ ' . $row->baseImponible . '</td><td>Procentaje: </td><td style="text-align:center;">' . $row->porcentajeRetener . '</td><td>Retenido: </td><td style="text-align:right;">$ ' . $row->valorRetenido . '</td></tr>';
                            }
                        }
                        $cuerpo .= '<tr><td colspan="6">&nbsp;</td><td>Total retenido: </td><td style="text-align:right;">$ ' . number_format($totalRetenido, 2) . '</td></tr></table></td></tr>';
                    }
                    $html = '<table width="100%">
                <tr>
                <td>Documento:</td>
                <td>' . $tipoDoc . '</td>
                </tr>
                <tr>
                <td>Número comprobante:</td>
                <td>' . $esta . '-' . $emis . '-' . $num . '</td>
                </tr>                
                <tr>
                <td>Clave Consultada:</td>
                <td>' . $result->RespuestaAutorizacionComprobante->claveAccesoConsultada . '</td>
                </tr>
                <tr>
                <td>Estado:</td>
                <td>' . $result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado . '</td>
                </tr>
                <tr>
                <td>Ambiente:</td>
                <td>' . $result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente . '</td>
                </tr>
                <tr>
                <td>Fecha autorización:</td>
                <td>' . date('Y-m-d H:i:s', strtotime($result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion)) . '</td>
                </tr>
                <tr>
                <td>Número de autorización:</td>
                <td>' . $result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion . '</td>
                </tr>
                <tr>
                <td>
                </td>
                </tr>
                ' . $cuerpo . '
                </table>';
                } else {
                    $html = '<table width="100%">
                    <tr>
                    <td colspan="2">Documento no autorizado por el SRI</td>
                    </tr>
                    <tr>
                    <td>Clave consultada:</td>
                    <td>' . $clave . '</td>
                    </tr>
                    </table>';
                }
            } catch (Exception $e) {
                $html = '<table width="100%">
                <tr>
                <td colspan="2">Documento no autorizado por el SRI</td>
                </tr>
                <tr>
                <td>Clave consultada:</td>
                <td>' . $clave . '</td>
                </tr>
                <tr><td>' . $e->getMessage() . '</td></tr>
                <tr><td>' . $e->getLine() . '</td></tr>
                </table>';
            }
            return $html;
        }
    }
    public function reprocesarDocumentos()
    {
        $documentos = De_Documentos_Electronicos::where('id_de_pasos', 12)->get();
        $clave = '';
        if (count($documentos) > 0) {
            foreach ($documentos as $value) {
                $clave = $value->clave_acceso;
                $result = $this->obtenercomprobanteSRI($clave);
                $this->generarXmlAutorizacion($result);
                $arrayDoc = [
                    'id_de_pasos' => 5,
                ];
                $value->update($arrayDoc);
                $maestro = De_Maestro_Documentos::find($value->id_maestro_documento);
                switch (strtoupper($maestro->nombre)) {
                    case 'FACTURA':
                        $arrayDocElectronico = [
                            'doc_electronico' => 5,
                            'numero' => $value->secuencial,
                            'nro_autorizacion' => $clave,
                            'nro_comprobante' => $value->establecimiento . '-' . $value->emision . '-' . $value->secuencial,
                        ];
                        Ct_ventas::find($value->id_documento)->update($arrayDocElectronico);
                        break;
                    case 'GUÍA DE REMISIÓN':
                        $arrayDocElectronico = [
                            'estado' => 5,
                            'num_secuencial' => $value->secuencial,
                        ];
                        Ct_Guia_Remision_Cabecera::find($value->id_documento)->update($arrayDocElectronico);
                        break;
                    case 'COMPROBANTE DE RETENCION':
                        $arrayDocElectronico = [
                            'doc_electronico' => 5,
                            'nro_secuencial' => $value->secuencial,
                            'nro_autorizacion' => $clave,
                            'nro_comprobante' => $value->establecimiento . '-' . $value->emision . '-' . $value->secuencial,
                        ];
                        Ct_Retenciones::find($value->id_documento)->update($arrayDocElectronico);
                        break;
                    case 'NOTA DE CRÉDITO':
                        $arrayDocElectronico = [
                            'doc_electronico' => 5,
                            'secuencia' => $value->secuencial,
                            'nro_autorizacion' => $clave,
                            'nro_comprobante' => $value->establecimiento . '-' . $value->emision . '-' . $value->secuencial,
                        ];
                        Ct_Nota_Credito_Clientes::find($value->id_documento)->update($arrayDocElectronico);
                        break;
                    case 'NOTA DE DÉBITO':
                        $arrayDocElectronico = [
                            'doc_electronico' => 5,
                            'secuencia' => $value->secuencial,
                            'nro_autorizacion' => $clave,
                            'numero' => $value->establecimiento . '-' . $value->emision . '-' . $value->secuencial,
                        ];
                        Ct_Nota_Debito_Cliente::find($value->id_documento)->update($arrayDocElectronico);
                        break;
                    case 'LIQUIDACIÓN DE COMPRA':
                        $arrayDocElectronico = [
                            'doc_electronico' => 5,
                            'secuencia_f' => $value->secuencial,
                            'nro_autorizacion' => $clave,
                            'nro_comprobante' => $value->establecimiento . '-' . $value->emision . '-' . $value->secuencial,
                        ];
                        Ct_compras::find($value->id_documento)->update($arrayDocElectronico);
                        break;
                }
            }
        }
    }
    public static function setBarcode($clave, $ruta)
    {
        $im = imagecreatetruecolor(300, 80);
        $black = ImageColorAllocate($im, 0x00, 0x00, 0x00);
        $white = ImageColorAllocate($im, 0xff, 0xff, 0xff);
        imagefilledrectangle($im, 0, 0, 300, 300, $white);
        EmisionDocumentosController::crearcarpeta(base_path() . $ruta);
        Barcode::gd($im, $black, 150, 40, 0, "code128", $clave, 2, 50);
        imagepng($im, base_path() . $ruta . $clave . '.png');
        imagedestroy($im);
        return $ruta . $clave . '.png';
    }
    public function pingDomain($domain)
    {
        ini_set('max_execution_time', '300');
        error_reporting(0);
        $estado = 0;
        try {
            $result = file($domain);
            if ($result == false) {
                $estado = 0;
            } elseif (count($result) <= 1) {
                $estado = 0;
            } else {
                $estado = 1;
            }
            return $estado;
        } catch (Exception $ex) {
            $estado = 0;
        }
        return $estado;
    }
    function ValidarUrl($url)
    {
        //fsockopen -> Abrir una conexión de sockets de dominio de Internet o Unix
        //resource fsockopen ( string destino, int puerto [, int errno [, string errstr [, float tiempo_espera]]])
        $validar = @fsockopen($url, 80, $errno, $errstr, 15);
        if ($validar) {
            fclose($validar);
            return true;
        } else
            return false;
    }
    public function verificar_token($ruta, $clave)
    {
        $file = $ruta;
        $pass = $clave;
        $fd = fopen($file, 'r');
        $p12buf = fread($fd, filesize($file));
        fclose($fd);
        $p12cert = array();
        openssl_pkcs12_read($p12buf, $p12cert, $pass);
        if (openssl_pkcs12_read($p12buf, $p12cert, $pass))
            return true;
        else
            return false;
    }
    public function validarPeriodo($fecha)
    {
        $hoy = date("Y-m-d", strtotime('2024-04-01'));
        if ($hoy <= date('Y-m-d', strtotime($fecha))) {
            return true;
        } else {
            return false;
        }
    }
    private function validarData($data)
    {
        $error = [];
        $cont = 0;
        $valida_cedula = false;
        $codDoc = '';
        $codigo = '';
        $codigoRetencion = '';
        $porcentajeRetener = '';
        if (!isset($data['datosfirma']['clave_firma'])) {
            $error[$cont] =  'Error: ' . ($cont + 1) . ': clave de firma no registrada (' . $data['datosfirma']['clave_firma'] . ')';
            $cont++;
        } else {
            if ($data['datosfirma']['clave_firma'] == '') {
                $error[$cont] =  'Error: ' . ($cont + 1) . ': clave de firma no registrada (' . $data['datosfirma']['clave_firma'] . ')';
                $cont++;
            }
        }
        if (!isset($data['datosfirma']['ruta_firma'])) {
            $error[$cont] =  "Error: " . ($cont + 1) . ": firma no registrada";
            $cont++;
        } else {
            if ($data['datosfirma']['ruta_firma'] == '') {
                $error[$cont] =  "Error: " . ($cont + 1) . ": firma no registrada";
                $cont++;
            } else {
                if (!file_exists(base_path() . '/storage/app/facturaelectronica/p12/' . $data['datosfirma']['ruta_firma'])) {
                    $error[$cont] =  'Error: ' . ($cont + 1) . ': el archivo de la firma no existe (' . $data['datosfirma']['ruta_firma'] . ')';
                    $cont++;
                } else {
                    if (!$this->verificar_token(base_path() . '/storage/app/facturaelectronica/p12/' . $data['datosfirma']['ruta_firma'], $data['datosfirma']['clave_firma'])) {
                        $error[$cont] =  "Error: " . ($cont + 1) . ": la clave no corresponde al archivo de la firma";
                        $cont++;
                    }
                }
            }
        }
        if (isset($data['infoTributaria']['ruc'])) {
            if ($data['infoTributaria']['ruc'] == '') {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc no puede estar vacio';
                $cont++;
            }
            if (strlen($data['infoTributaria']['ruc']) != 13) {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc debe contener 13 caracteres';
                $cont++;
            }
            $empresa = Empresa::where('id', $data['infoTributaria']['ruc'])->first();
            if (is_null($empresa)) {
                $error[$cont] =  "Error: " . ($cont + 1) . ": no existe la empresa";
                $cont++;
            } else {
                //InfoFactura
                //validar si la empresa esta habilitada como facturacion electronica
                $deEmpresa = De_Empresa::where('id_empresa', $empresa->id)->first();
                //dd($deEmpresa,$empresa);
                if (is_null($deEmpresa)) {
                    $error[$cont] =  "Error: " . ($cont + 1) . ": No se encuentra informacion de facturacion electronica";
                    $cont++;
                }
                //validar ambiente
                if (isset($data['infoTributaria']['ambiente'])) {
                    if ($data['infoTributaria']['ambiente'] == '') {
                        $error[$cont] =  "Error " . ($cont + 1) . ': el campo ambiente es obligatorio';
                        $cont++;
                    }
                    if (strlen($data['infoTributaria']['ambiente']) != 1) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el ambiente debe contener un digito';
                        $cont++;
                    }
                    if ($data['infoTributaria']['ambiente'] != 1 && $data['infoTributaria']['ambiente'] != 2) {
                        $error[$cont] = 'Error ' . ($cont + 1) . ': el ambiente solo puede ser 1 o 2';
                        $cont++;
                    }
                    if (!is_numeric($data['infoTributaria']['ambiente'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el ambiente debe ser numerico';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo ambiente es obligatorio';
                    $cont++;
                }
                //validar tipo Emision
                if (isset($data['infoTributaria']['tipoEmision'])) {
                    if ($data['infoTributaria']['tipoEmision'] == '') {
                        $error[$cont] =  "Error " . ($cont + 1) . ': el campo tipoEmision es obligatorio';
                        $cont++;
                    }
                    if (strlen($data['infoTributaria']['tipoEmision']) != 1) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el tipoEmision debe contener un digito';
                        $cont++;
                    }
                    if ($data['infoTributaria']['tipoEmision'] != 1) {
                        $error[$cont] = 'Error ' . ($cont + 1) . ': el tipoEmision solo puede ser 1';
                        $cont++;
                    }
                    if (!is_numeric($data['infoTributaria']['tipoEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el tipoEmision debe ser numerico';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoEmision es obligatorio';
                    $cont++;
                }
                //Validar razonSocial
                if (isset($data['infoTributaria']['razonSocial'])) {
                    if ($data['infoTributaria']['razonSocial'] == '') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocial debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                    if (strlen($data['infoTributaria']['razonSocial']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocial debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocial es obligatorio';
                    $cont++;
                }
                //Validar nombreComercial
                if (isset($data['infoTributaria']['nombreComercial'])) {
                    if ($data['infoTributaria']['nombreComercial'] != '') {
                        if (strlen($data['infoTributaria']['nombreComercial']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo nombreComercial debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo nombreComercial no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo nombreComercial es obligatorio';
                    $cont++;
                }
                //validar ruc
                if (isset($data['infoTributaria']['ruc'])) {
                    if ($data['infoTributaria']['ruc'] == '') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc debe contener 13 caracteres';
                        $cont++;
                    }
                    if (strlen($data['infoTributaria']['ruc']) != 13) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc debe contener 13 caracteres';
                        $cont++;
                    }
                    //else {
                    //     $correcto = $this->validarCedula($data['infoTributaria']['ruc']);
                    //     if (!$correcto) {
                    //         $error[$cont] = "Error " . ($cont + 1) . ': numero de ruc incorrecto';
                    //         $cont++;
                    //     }
                    // }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc es obligatorio';
                    $cont++;
                }
                //Validar si puede emitir documentos electronicos
                if ($deEmpresa == '') {
                    //if ($empresa->electronica != 1) {
                    $error[$cont] = "Error " . ($cont + 1) . ": empresa no permite facturacion electronica";
                    $cont++;
                }
                //validar ClaveAcceso
                if (isset($data['infoTributaria']['claveAcceso'])) {
                    if (!$data['infoTributaria']['claveAcceso'] == '') {
                        if (strlen($data['infoTributaria']['claveAcceso']) != 49) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo claveAcceso debe contener 49 caracteres (' . $data['infoTributaria']['claveAcceso'] . ' No. de caracteres ingresados: ' . strlen($data['infoTributaria']['claveAcceso']) . ' )';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['claveAcceso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo claveAcceso debe ser numerico (' . $data['infoTributaria']['claveAcceso'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo claveAcceso no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo claveAcceso es obligatorio';
                    $cont++;
                }
                //validar codDoc
                if (isset($data['infoTributaria']['codDoc'])) {
                    if ($data['infoTributaria']['codDoc'] != '') {
                        if (strlen($data['infoTributaria']['codDoc']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc debe contener 2 caracteres (' . $data['infoTributaria']['codDoc'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['codDoc'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc debe ser numerico (' . $data['infoTributaria']['codDoc'] . ')';
                            $cont++;
                        }
                        if ($data['infoTributaria']['codDoc'] != '01' && $data['infoTributaria']['codDoc'] != '03' && $data['infoTributaria']['codDoc'] != '04' && $data['infoTributaria']['codDoc'] != '05' && $data['infoTributaria']['codDoc'] != '06' && $data['infoTributaria']['codDoc'] != '07') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc solo pueden ser una de siguientes opciones 01 - 03 - 04 - 05 - 06 - 07 (' . $data['infoTributaria']['codDoc'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc es obligatorio';
                    $cont++;
                }
                //validar estab
                if (isset($data['infoTributaria']['estab'])) {
                    if (strlen($data['infoTributaria']['estab']) != '') {
                        if (strlen($data['infoTributaria']['estab']) != 3) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo estab debe contener 3 caracteres (' . $data['infoTributaria']['estab'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['estab'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo estab debe ser numerico (' . $data['infoTributaria']['estab'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo estab no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo estab es obligatorio';
                    $cont++;
                }
                //validar ptoEmi
                if (isset($data['infoTributaria']['ptoEmi'])) {
                    if (strlen($data['infoTributaria']['ptoEmi']) != '') {
                        if (strlen($data['infoTributaria']['ptoEmi']) != 3) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmi debe contener 3 caracteres (' . $data['infoTributaria']['ptoEmi'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['ptoEmi'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmi debe ser numerico (' . $data['infoTributaria']['ptoEmi'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmi no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmi es obligatorio';
                    $cont++;
                }
                //validar secuencial
                if (isset($data['infoTributaria']['secuencial'])) {
                    if (strlen($data['infoTributaria']['secuencial']) != '') {
                        if (strlen($data['infoTributaria']['secuencial']) != 9) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo secuencial debe contener 9 caracteres (' . $data['infoTributaria']['secuencial'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['secuencial'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo secuencial debe ser numerico (' . $data['infoTributaria']['secuencial'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo secuencial no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo secuencial es obligatorio';
                    $cont++;
                }
                //validar dirMatriz
                if (isset($data['infoTributaria']['dirMatriz'])) {
                    if ($data['infoTributaria']['dirMatriz'] != '') {
                        if (strlen($data['infoTributaria']['dirMatriz']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo dirMatriz debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirMatriz no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirMatriz es obligatorio';
                    $cont++;
                }
                $codDoc = $data['infoTributaria']['codDoc'];
                if ($codDoc == '06') { //GuiaRemision
                    //validarInfoGuiaRemision
                    if (isset($data['infoGuiaRemision'])) {
                        if ($data['infoGuiaRemision'] != '') {
                            $error = $this->validarInfoGuiaRemision($data['infoGuiaRemision'], $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo infoGuiaRemision no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo infoGuiaRemision es obligatorio';
                        $cont++;
                    }
                    //validar destinatarios
                    if (isset($data['destinatarios'])) {
                        if ($data['destinatarios'] != '') {
                            $error = $this->validarDestinatarios($data['destinatarios'], $codDoc, $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo destinatarios no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo destinatarios es obligatorio';
                        $cont++;
                    }
                } elseif ($codDoc == '01') { //Factura
                    //validarInfoFactura
                    if (isset($data['infoFactura'])) {
                        if ($data['infoFactura'] != '') {
                            $error = $this->validarInfoFactura($data['infoFactura'], $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo infoFactura no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo infoFactura es obligatorio';
                        $cont++;
                    }
                    //ValidarDetalle
                    if (isset($data['detalles'])) {
                        if ($data['detalles'] != '') {
                            $error = $this->validarDetalles($data['detalles'], $codDoc, $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles es obligatorio';
                        $cont++;
                    }
                } elseif ($codDoc == '03') { //LiquidacionCompra
                    //validarInfoLiquidacionCompra
                    if (isset($data['infoLiquidacionCompra'])) {
                        if ($data['infoLiquidacionCompra'] != '') {
                            $error = $this->validarInfoLiquidacion($data['infoLiquidacionCompra'], $codDoc, $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo infoLiquidacionCompra no puede ser vacio';
                            $cont++;
                        }
                        //ValidarDetalles
                        if (isset($data['detalles'])) {
                            if ($data['detalles'] != '') {
                                $error = $this->validarDetalles($data['detalles'], $codDoc, $cont, $error);
                            } else {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles no puede ser vacio';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles es obligatorio';
                            $cont++;
                        }
                        if ($codDoc == '41') {
                            //ValidarReembolsos
                            if (isset($data['reembolsos'])) {
                                if ($data['reembolsos'] != '') {
                                    $error = $this->validarReembolsos($data['reembolsos'], $codDoc, $cont, $error);
                                } else {
                                    $error[$cont] = "Error " . ($cont + 1) . ': el campo reembolsos no puede ser vacio';
                                    $cont++;
                                }
                            } else {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo reembolsos es obligatorio';
                                $cont++;
                            }
                        }
                        //ValidarMaquinaFiscal
                        if (isset($data['maquinaFiscal'])) {
                            if ($data['maquinaFiscal'] != '') {
                                $error = $this->validarMaquinaFiscal($data['maquinaFiscal'], $codDoc, $cont, $error);
                            } else {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo maquinaFiscal no puede ser vacio';
                                $cont++;
                            }
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo infoLiquidacion es obligatorio';
                        $cont++;
                    }
                } elseif ($codDoc == '04') { //NotaCredito
                    //validarInfoNotaCredito
                    if (isset($data['infoNotaCredito'])) {
                        if ($data['infoNotaCredito'] != '') {
                            $error = $this->validarInfoNotaCredito($data['infoNotaCredito'], $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo infoNotaCredito no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo infoNotaCredito es obligatorio';
                        $cont++;
                    }
                    //ValidarDetalle
                    if (isset($data['detalles'])) {
                        if ($data['detalles'] != '') {
                            $error = $this->validarDetalles($data['detalles'], $codDoc, $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles es obligatorio';
                        $cont++;
                    }
                } elseif ($codDoc == '05') { //NotaDebito
                    //validarInfoNotaDebito
                    if (isset($data['infoNotaDebito'])) {
                        if ($data['infoNotaDebito'] != '') {
                            $error = $this->validarInfoNotaDebito($data['infoNotaDebito'], $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo infoNotaDebito no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo infoNotaDebito es obligatorio';
                        $cont++;
                    }
                } elseif ($codDoc == '07') { //Retencion
                    //validarInfoCompRetencion
                    if (isset($data['infoCompRetencion'])) {
                        if ($data['infoCompRetencion'] != '') {
                            $error = $this->validarInfoCompRetencion($data['infoCompRetencion'], $cont, $error);
                            $cont++;
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo infoCompRetencion no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo infoCompRetencion es obligatorio';
                        $cont++;
                    }
                    //validar DocsSustento
                    if (isset($data['docsSustento'])) {
                        if (count($data['docsSustento']) > 0) {
                            $error = $this->validarDocsSustento($data['docsSustento'], $cont, $error);
                            $cont++;
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo docsSustento no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo docsSustento es obligatorio';
                        $cont++;
                    }
                }
                //validarInfoAdicional
                if (isset($data['infoAdicional'])) {
                    if ($data['infoAdicional'] != '') {
                        $this->validarInfoAdicional($data['infoAdicional'], $cont, $error);
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo infoAdicional no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo infoAdicional es obligatorio';
                    $cont++;
                }
            }
        } else {
            $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionSujetoRetenido es obligatorio';
            $cont++;
        }
        return $error;
    }
    private function validarInfoFactura($data, &$cont, $error)
    {
        try {
            //fechaEmision
            if (isset($data['fechaEmision'])) {
                if ($data['fechaEmision'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no es valida (' . $data['fechaEmision'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no tiene el formato correcto dd/mm/yyyy (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision es obligatorio';
                $cont++;
            }
            //validar dirEstablecimiento
            if (isset($data['dirEstablecimiento'])) {
                if ($data['dirEstablecimiento'] != '') {
                    if (strlen($data['dirEstablecimiento']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento es obligatorio';
                $cont++;
            }
            //validar contribuyenteEspecial
            if (isset($data['contribuyenteEspecial'])) {
                if ($data['contribuyenteEspecial'] != '') {
                    if (strlen($data['contribuyenteEspecial']) >= 3 && strlen($data['contribuyenteEspecial']) <= 13) {
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo contribuyenteEspecial debe contener entre 3 y 13 caracteres (' . $data['contribuyenteEspecial'] . ')';
                        $cont++;
                    }
                }
            }
            //validar obligadoContabilidad
            if (isset($data['obligadoContabilidad'])) {
                if ($data['obligadoContabilidad'] != '') {
                    if (strtoupper($data['obligadoContabilidad']) != 'SI' && strtoupper($data['obligadoContabilidad']) != 'NO') {
                        $data['obligadoContabilidad'] = strtoupper($data['obligadoContabilidad']);
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad Solo puiede ser SI o NO (' . $data['obligadoContabilidad'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad no puede ser vacio';
                    $cont++;
                }
            }
            //validar tipoIdentificacionComprador
            if (isset($data['tipoIdentificacionComprador'])) {
                if ($data['tipoIdentificacionComprador'] != '') {
                    if (strlen($data['tipoIdentificacionComprador']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador debe contener 2 caracteres (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['tipoIdentificacionComprador'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador debe ser numerico (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    } else {
                        if (strlen($data['identificacionComprador']) == 10) {
                            if ($data['tipoIdentificacionComprador'] != '05' && $data['tipoIdentificacionComprador'] != '08' && $data['tipoIdentificacionComprador'] != '06') {
                                $error[$cont] = "Error " . ($cont + 1) . ': el numero de identificacion (' . $data['identificacionComprador'] . ' - {' . strlen($data['identificacionComprador']) . '}) no corresponde al tipo de identificacion ' . $data['tipoIdentificacionComprador'];
                                $cont++;
                            }
                        } else if (strlen($data['identificacionComprador']) == 13) {
                            if ($data['identificacionComprador'] == '9999999999999') {
                                if ($data['tipoIdentificacionComprador'] != '07') {
                                    $error[$cont] = "Error " . ($cont + 1) . ': el numero de identificacion (' . $data['identificacionComprador'] . ' - {' . strlen($data['identificacionComprador']) . '}) no corresponde al tipo de identificacion ' . $data['tipoIdentificacionComprador'];
                                    $cont++;
                                }
                            } elseif ($data['tipoIdentificacionComprador'] != '04' && strlen($data['identificacionComprador']) != 13) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el numero de identificacion (' . $data['identificacionComprador'] . ' - {' . strlen($data['identificacionComprador']) . '}) no corresponde al tipo de identificacion ' . $data['tipoIdentificacionComprador'];
                                $cont++;
                            }
                        }
                    }
                    if ($data['tipoIdentificacionComprador'] != '04' && $data['tipoIdentificacionComprador'] != '05' && $data['tipoIdentificacionComprador'] != '06' && $data['tipoIdentificacionComprador'] != '07' && $data['tipoIdentificacionComprador'] != '08') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador solo puede ser 04, 05, 06, 07, 08 (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador es obligatorio';
                $cont++;
            }
            //validar guiaRemision
            if (isset($data['guiaRemision'])) {
                if ($data['guiaRemision'] != '') {
                    if (strlen($data['guiaRemision']) != 15) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo guiaRemision debe contener 15 caracteres';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo guiaRemision no puede ser vacio';
                    $cont++;
                }
            }
            //validar razonSocial
            if (isset($data['razonSocialComprador'])) {
                if ($data['razonSocialComprador'] != '') {
                    if (strlen($data['razonSocialComprador']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador es obligatorio';
                $cont++;
            }
            //validar identificacionComprador
            if (isset($data['identificacionComprador'])) {
                if ($data['identificacionComprador'] != '') {
                    if (strlen($data['identificacionComprador']) > 20) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionComprador debe contener hasta 20 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario es obligatorio';
                $cont++;
            }
            //validar direccionComprador
            if (isset($data['direccionComprador'])) {
                if ($data['direccionComprador'] != '') {
                    if (strlen($data['direccionComprador']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo direccionComprador debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo direccionComprador no puede ser vacio';
                    $cont++;
                }
            }
            //validar totalSinImpuestos
            if (isset($data['totalSinImpuestos'])) {
                if ($data['totalSinImpuestos'] != '') {
                    $digits = str_replace(array('.', ','), array('', ''), strrev($data['totalSinImpuestos']));
                    if (!ctype_digit($digits)) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos debe contener solo numeros';
                        $cont++;
                    }
                    if (strlen($data['totalSinImpuestos']) > 14) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos debe contener hasta 14 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos no puede ser vacio';
                    $cont++;
                }
            }
            //totalConImpuestos
            if (isset($data['totalConImpuestos'])) {
                if (count($data['totalConImpuestos']) > 0) {
                    $error = $this->validarTotalImpuestos($data['totalConImpuestos'], $data['infoTributario']['codDoc'], $cont, $error);
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo totalConImpuestos debe contener al menos un registro';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo totalConImpuestos es obligatorio';
                $cont++;
            }
            //validar propina
            if (isset($data['propina'])) {
                if ($data['propina'] != '') {
                    //$digits = str_replace(array('.',','), array('',''), strrev($data['propina']));
                    if (!is_numeric($data['propina'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo propina debe ser numerico';
                        $cont++;
                    }
                    if (strlen($data['propina']) > 14) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo propina debe contener hasta 14 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo propina no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo propina es obligatorio';
                $cont++;
            }
            //validar importeTotal
            if (isset($data['importeTotal'])) {
                if ($data['importeTotal'] != '') {
                    if (!is_numeric($data['importeTotal'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo propina debe ser numerico';
                        $cont++;
                    }
                    if (strlen($data['importeTotal']) > 14) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal debe contener hasta 14 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal es obligatorio';
                $cont++;
            }
            //validar Pagos
            if (isset($data['pagos'])) {
                if (count($data['pagos']) > 0) {
                    $error = $this->validarPagos($data['pagos'], $cont, $error);
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo pagos no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo pagos es obligatorio';
                $cont++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarPagos($data, &$cont, $error)
    {
        try {
            $conPago = 0;
            foreach ($data as $pago) {
                //formaPago
                if (isset($pago['formaPago'])) {
                    if ($pago['formaPago'] != '') {
                        if (strlen($pago['formaPago']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' debe contener 2 caracteres (' . $pago['formaPago'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($pago['formaPago'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' debe ser numerico (' . $pago['formaPago'] . ')';
                            $cont++;
                        }
                        if ($pago['formaPago'] != '01' && $pago['formaPago'] != '15' && $pago['formaPago'] != '16' && $pago['formaPago'] != '17' && $pago['formaPago'] != '18' && $pago['formaPago'] != '19' && $pago['formaPago'] != '20' && $pago['formaPago'] != '21') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' solo pueden ser una de siguientes opciones 01 - 15 - 16 - 17 - 18 - 19 - 20 - 21(' . $pago['formaPago'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' es obligatorio';
                    $cont++;
                }
                //total
                if (isset($pago['total'])) {
                    if ($pago['total'] != '') {
                        if (!is_numeric($pago['total'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo total del Pago ' . ($conPago + 1) . ' debe ser numerico (' . $pago['total'] . ')';
                            $cont++;
                        }
                        if (strlen($pago['total']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo total del Pago ' . ($conPago + 1) . ' debe contener hasta 14 digitos maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo total del Pago ' . ($conPago + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo total del Pago ' . ($conPago + 1) . ' es obligatorio';
                    $cont++;
                }
                //plazo
                if (isset($pago['plazo'])) {
                    if ($pago['plazo'] != '') {
                        if (!is_numeric($pago['plazo'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo plazo del Pago ' . ($conPago + 1) . ' debe contener solo numeros';
                            $cont++;
                        }

                        if (strlen($pago['plazo']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo plazo del Pago ' . ($conPago + 1) . ' debe contener hasta 14 digitos maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo plazo del Pago ' . ($conPago + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                //unidaadTiempo
                if (isset($pago['unidadTiempo'])) {
                    if ($pago['unidadTiempo'] != '') {
                        if (strlen($pago['unidadTiempo']) > 10) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo unidadTiempo del Pago ' . ($conPago + 1) . ' debe contener hasta 10 digitos maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo unidadTiempo del Pago ' . ($conPago + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                $conPago++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . ': ' . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarInfoGuiaRemision($data, &$cont, $error)
    {
        $ruc = '';
        $codIdentificacion = '';
        try {
            //validar dirEstablecimiento
            if (isset($data['dirEstablecimiento'])) {
                if ($data['dirEstablecimiento'] != '') {
                    if (strlen($data['dirEstablecimiento']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento es obligatorio';
                $cont++;
            }
            //validar razonSocialTransportista
            if (isset($data['razonSocialTransportista'])) {
                if ($data['razonSocialTransportista'] != '') {
                    if (strlen($data['razonSocialTransportista']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialTransportista debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialTransportista no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialTransportista es obligatorio';
                $cont++;
            }
            //validar dirPartida
            if (isset($data['dirPartida'])) {
                if ($data['dirPartida'] != '') {
                    if (strlen($data['dirPartida']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirPartida debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirPartida no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo dirPartida es obligatorio';
                $cont++;
            }
            //validar tipoIdentificacionTransportista
            if (isset($data['tipoIdentificacionTransportista'])) {
                if ($data['tipoIdentificacionTransportista'] != '') {
                    $codIdentificacion = $data['tipoIdentificacionTransportista'];
                    if (strlen($data['tipoIdentificacionTransportista']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista debe contener 2 caracteres (' . $data['tipoIdentificacionTransportista'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['tipoIdentificacionTransportista'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista debe ser numerico (' . $data['tipoIdentificacionTransportista'] . ')';
                        $cont++;
                    }
                    /*AOMS
                    if ($data['tipoIdentificacionTransportista'] != '04' && $data['tipoIdentificacionTransportista'] != '05') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista solo puede ser 04 o 05 (' . $data['tipoIdentificacionTransportista'] . ')';
                        $cont++;
                    }*/
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista es obligatorio';
                $cont++;
            }
            //validar rucTransportista
            if (isset($data['rucTransportista'])) {
                if (strlen($data['rucTransportista']) != 10 && strlen($data['rucTransportista']) != 13) {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo rucTransportista debe contener 10 o 13 caracteres maximo (No. de caracteres ingresados ' . strlen($data['rucTransportista']) . ')';
                    $cont++;
                } else {
                    $ruc = $data['rucTransportista'];
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo rucTransportista es obligatorio';
                $cont++;
            }
            //validar identificacion/tipo
            if ($ruc != '' && $codIdentificacion != '') {
                if ($codIdentificacion == '04' && strlen($ruc) != 13) {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo rucTransportista debe contener 13 caracteres con el tipoIdentificacion 04';
                    $cont++;
                } elseif ($codIdentificacion == '05' && strlen($ruc) != 10) {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo rucTransportista debe contener 10 caracteres con el tipoIdentificacion 05';
                    $cont++;
                } else {
                    /*$correcto = $this->validarCedula($data['rucTransportista']);
                    if (!$correcto) {
                        $error[$cont] = "Error " . ($cont + 1) . ': numero de ruc del Transportista incorrecto';
                        $cont++;
                    }*/
                }
            }

            //validar Rise
            if (isset($data['rise'])) {
                if ($data['rise'] != '') {
                    if (strlen($data['rise']) > 40) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo rise debe contener 40 caracteres (' . $data['rise'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo rise no puede ser vacio';
                    $cont++;
                }
            }
            //validar obligadoContabilidad
            if (isset($data['obligadoContabilidad'])) {
                if ($data['obligadoContabilidad'] != '') {
                    if (strtoupper($data['obligadoContabilidad']) != 'SI' && strtoupper($data['obligadoContabilidad']) != 'NO') {
                        $data['obligadoContabilidad'] = strtoupper($data['obligadoContabilidad']);
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad Solo puiede ser SI o NO (' . $data['obligadoContabilidad'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad no puede ser vacio';
                    $cont++;
                }
            }
            //validar contribuyenteEspecial
            if (isset($data['contribuyenteEspecial'])) {
                if ($data['contribuyenteEspecial'] != '') {
                    if (strlen($data['contribuyenteEspecial']) >= 3 && strlen($data['contribuyenteEspecial']) <= 13) {
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo contribuyenteEspecial debe contener entre 3 y 13 caracteres (' . $data['contribuyenteEspecial'] . ')';
                        $cont++;
                    }
                }
            }
            //validar fechaIniTransporte
            if (isset($data['fechaIniTransporte'])) {
                if ($data['fechaIniTransporte'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaIniTransporte'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte no es valida (' . $data['fechaIniTransporte'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaIniTransporte'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte no tiene el formato correcto dd/mm/yyyy (' . $data['fechaIniTransporte'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFechaInicio($data['fechaIniTransporte'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte debe ser igual o mayor a la fecha actual (' . $data['fechaIniTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte es obligatorio';
                $cont++;
            }
            //validar fechaFinTransporte
            if (isset($data['fechaFinTransporte'])) {
                if ($data['fechaFinTransporte'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaFinTransporte'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte no es valida (' . $data['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaFinTransporte'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte no tiene el formato correcto dd/mm/yyyy (' . $data['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFechaInicio($data['fechaFinTransporte'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte debe ser igual o mayor a la fecha actual (' . $data['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFechaFinal($data['fechaIniTransporte'], $data['fechaFinTransporte'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte debe ser igual o mayor a la fecha de inicio (' . $data['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte es obligatorio';
                $cont++;
            }
            //validar placa
            if (isset($data['placa'])) {
                if ($data['placa'] != '') {
                    if (strlen($data['placa']) > 20) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo placa debe contener hasta 20 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo placa no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo placa es obligatorio';
                $cont++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    public function validarDestinatarios($data, $codDoc, &$cont, $error)
    {
        try {
            foreach ($data as $destinatario) {
                if (isset($destinatario['identificacionDestinatario'])) {
                    if ($destinatario['identificacionDestinatario'] != '') {
                        if (strlen($destinatario['identificacionDestinatario']) > 20) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario debe contener hasta 20 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario es obligatorio';
                    $cont++;
                }
                //validar razonSocialDestinatario
                if (isset($destinatario['razonSocialDestinatario'])) {
                    if ($destinatario['razonSocialDestinatario'] != '') {
                        if (strlen($destinatario['razonSocialDestinatario']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialDestinatario debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialDestinatario no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialDestinatario es obligatorio';
                    $cont++;
                }
                //validar dirDestinatario
                if (isset($destinatario['dirDestinatario'])) {
                    if ($destinatario['dirDestinatario'] != '') {
                        if (strlen($destinatario['dirDestinatario']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo dirDestinatario debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirDestinatario no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirDestinatario es obligatorio';
                    $cont++;
                }
                //Validar motivoTraslado
                if (isset($destinatario['motivoTraslado'])) {
                    if ($destinatario['motivoTraslado'] != '') {
                        if (strlen($destinatario['motivoTraslado']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo motivoTraslado debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo motivoTraslado no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo motivoTraslado es obligatorio';
                    $cont++;
                }
                //validar docAduaneroUnico
                if (isset($destinatario['docAduaneroUnico'])) {
                    if ($destinatario['docAduaneroUnico'] != '') {
                        if (strlen($destinatario['docAduaneroUnico']) > 20) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo docAduaneroUnico debe contener hasta 20 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo docAduaneroUnico no puede ser vacio';
                        $cont++;
                    }
                }
                //validar codEstabDestino
                if (isset($destinatario['codEstabDestino'])) {
                    if ($destinatario['codEstabDestino'] != '') {
                        if (strlen($destinatario['codEstabDestino']) != 3) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codEstabDestino debe contener 3 caracteres';
                            $cont++;
                        }
                        if (!is_numeric($destinatario['codEstabDestino'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codEstabDestino debe ser numerico (' . $destinatario['codEstabDestino'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codEstabDestino no puede ser vacio';
                        $cont++;
                    }
                }
                //validar ruta
                if (isset($destinatario['ruta'])) {
                    if ($destinatario['ruta'] != '') {
                        if (strlen($destinatario['ruta']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo ruta debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ruta no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo ruta no puede ser vacio';
                    $cont++;
                }
                //validar codDocSustento
                if (isset($destinatario['codDocSustento'])) {
                    if ($destinatario['codDocSustento'] != '') {
                        if (strlen($destinatario['codDocSustento']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento debe contener 2 caracteres';
                            $cont++;
                        }
                        if ($destinatario['codDocSustento'] != '01' && $destinatario['codDocSustento'] != '03' && $destinatario['codDocSustento'] != '04' && $data['infoTributaria']['codDoc'] != '05' && $destinatario['codDocSustento'] != '06' && $destinatario['codDocSustento'] != '07') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento solo pueden ser una de siguientes opciones 01 - 03 - 04 - 05 - 06 - 07 (' . $destinatario['codDocSustento'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($destinatario['codDocSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento debe ser numerico (' . $destinatario['codDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento no puede ser vacio';
                        $cont++;
                    }
                }
                //validar numDocSustento
                if (isset($destinatario['numDocSustento'])) {
                    if ($destinatario['numDocSustento'] != '') {
                        $destinatario['numDocSustento'] = str_replace('-', '', $destinatario['numDocSustento']);
                        if (strlen($destinatario['numDocSustento']) != 15) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento debe contener 15 caracteres (No. de caracteres ' . strlen($destinatario['numDocSustento']) . ')';
                            $cont++;
                        }
                        if (!is_numeric($destinatario['numDocSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento debe ser numerico (' . $destinatario['numDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento no puede ser vacio';
                        $cont++;
                    }
                }
                //Validar numAutDocSustento
                if (isset($destinatario['numAutDocSustento'])) {
                    if ($destinatario['numAutDocSustento'] != '') {
                        if (strlen($destinatario['numAutDocSustento']) != 10 && strlen($destinatario['numAutDocSustento']) != 37 && strlen($destinatario['numAutDocSustento']) != 49) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento debe contener 10, 37 o 49 caracteres (numero de caracteres ingresados ' . strlen($destinatario['numAutDocSustento']) . ')';
                            $cont++;
                        }
                        if (!is_numeric($destinatario['numAutDocSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento debe ser numerico (' . $destinatario['numAutDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento no puede ser vacio';
                        $cont++;
                    }
                }
                //validar fechaEmisionDocSustento
                if (isset($destinatario['fechaEmisionDocSustento'])) {
                    if ($destinatario['fechaEmisionDocSustento'] != '') {
                        $nfecha = $this->getdate(str_replace('/', '-', $destinatario['fechaEmisionDocSustento']));
                        $destinatario['fechaEmisionDocSustento'] = $nfecha;
                        if (!$this->validarFecha($nfecha)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento no tiene el formato correcto dd/mm/yyyy (' . $destinatario['fechaEmisionDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento no puede ser vacio';
                        $cont++;
                    }
                }
                //validar detalles
                if (isset($destinatario['detalles'])) {
                    if (count($destinatario['detalles']) == 0) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles no puede estar vacio';
                        $cont++;
                    } else {
                        $error = $this->validarDetalles($destinatario['detalles'], $codDoc, $cont, $error);
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles es obligatorio';
                    $cont++;
                }
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    public function validarInfoLiquidacion($data, $codDoc, &$cont, $error)
    {
        try {
            //fechaEmision
            if (isset($data['fechaEmision'])) {
                if ($data['fechaEmision'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no es valida (' . $data['fechaEmision'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no tiene el formato correcto dd/mm/yyyy (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision es obligatorio';
                $cont++;
            }
            //validar dirEstablecimiento
            if (isset($data['dirEstablecimiento'])) {
                if ($data['dirEstablecimiento'] != '') {
                    if (strlen($data['dirEstablecimiento']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento es obligatorio';
                $cont++;
            }
            //validar contribuyenteEspecial
            if (isset($data['contribuyenteEspecial'])) {
                if ($data['contribuyenteEspecial'] != '') {
                    if (strlen($data['contribuyenteEspecial']) >= 3 && strlen($data['contribuyenteEspecial']) <= 13) {
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo contribuyenteEspecial debe contener entre 3 y 13 caracteres (' . $data['contribuyenteEspecial'] . ')';
                        $cont++;
                    }
                }
            }
            //validar obligadoContabilidad
            if (isset($data['obligadoContabilidad'])) {
                if ($data['obligadoContabilidad'] != '') {
                    if (strtoupper($data['obligadoContabilidad']) != 'SI' && strtoupper($data['obligadoContabilidad']) != 'NO') {
                        $data['obligadoContabilidad'] = strtoupper($data['obligadoContabilidad']);
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad Solo puiede ser SI o NO (' . $data['obligadoContabilidad'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad no puede ser vacio';
                    $cont++;
                }
            }
            //validar tipoIdentificacionProveedor
            if (isset($data['tipoIdentificacionProveedor'])) {
                if ($data['tipoIdentificacionProveedor'] != '') {
                    if (strlen($data['tipoIdentificacionProveedor']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionProveedor debe contener 2 caracteres (' . $data['tipoIdentificacionProveedor'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['tipoIdentificacionProveedor'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionProveedor debe ser numerico (' . $data['tipoIdentificacionProveedor'] . ')';
                        $cont++;
                    } else {
                        if (strlen($data['tipoIdentificacionProveedor']) == 10) {
                            if ($data['tipoIdentificacionProveedor'] != '05' && $data['tipoIdentificacionProveedor'] != '08' && $data['tipoIdentificacionProveedor'] != '06') {
                                $error[$cont] = "Error " . ($cont + 1) . ': el numero de identificacion (' . $data['tipoIdentificacionProveedor'] . ' - {' . strlen($data['tipoIdentificacionProveedor']) . '}) no corresponde al tipo de identificacion ' . $data['tipoIdentificacionProveedor'];
                                $cont++;
                            }
                        } else if (strlen($data['identificacionProveedor']) == 13) {
                            if ($data['identificacionProveedor'] == '9999999999999') {
                                if ($data['tipoidentificacionProveedor'] != '07') {
                                    $error[$cont] = "Error " . ($cont + 1) . ': el numero de identificacion (' . $data['identificacionProveedor'] . ' - {' . strlen($data['identificacionProveedor']) . '}) no corresponde al tipo de identificacion ' . $data['tipoIdentificacionProveedor'];
                                    $cont++;
                                }
                            } elseif ($data['tipoIdentificacionProveedor'] != '04' && strlen($data['identificacionProveedor']) != 13) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el numero de identificacion (' . $data['identificacionProveedor'] . ' - {' . strlen($data['identificacionProveedor']) . '}) no corresponde al tipo de identificacion ' . $data['tipoIdentificacionProveedor'];
                                $cont++;
                            }
                        }
                    }
                    if ($data['tipoIdentificacionProveedor'] != '04' && $data['tipoIdentificacionProveedor'] != '05' && $data['tipoIdentificacionProveedor'] != '06' && $data['tipoIdentificacionProveedor'] != '07' && $data['tipoIdentificacionProveedor'] != '08') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionProveedor solo puede ser 04, 05, 06, 07, 08 (' . $data['tipoIdentificacionProveedor'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionProveedor no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionProveedor es obligatorio';
                $cont++;
            }
            //validar razonSocialProveedor
            if (isset($data['razonSocialProveedor'])) {
                if ($data['razonSocialProveedor'] != '') {
                    if (strlen($data['razonSocialProveedor']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialProveedor debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialProveedor no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialProveedor es obligatorio';
                $cont++;
            }
            //validar identificacionProveedor
            if (isset($data['identificacionProveedor'])) {
                if ($data['identificacionProveedor'] != '') {
                    if (strlen($data['identificacionProveedor']) > 20) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionProveedor debe contener hasta 20 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionProveedor no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario es obligatorio';
                $cont++;
            }
            //validar direccionProveedor
            if (isset($data['direccionProveedor'])) {
                if ($data['direccionProveedor'] != '') {
                    if (strlen($data['direccionProveedor']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo direccionProveedor debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo direccionProveedor no puede ser vacio';
                    $cont++;
                }
            }
            //validar totalSinImpuestos
            if (isset($data['totalSinImpuestos'])) {
                if ($data['totalSinImpuestos'] != '') {
                    $digits = str_replace(array('.', ','), array('', ''), strrev($data['totalSinImpuestos']));
                    if (!ctype_digit($digits)) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos debe contener solo numeros';
                        $cont++;
                    }
                    if (strlen($data['totalSinImpuestos']) > 14) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos debe contener hasta 14 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos no puede ser vacio';
                    $cont++;
                }
            }
            if ($codDoc == '41') {
                //validar totalDescuento
                if (isset($data['totalDescuento'])) {
                    if ($data['totalDescuento'] != '') {
                        $digits = str_replace(array('.', ','), array('', ''), strrev($data['totalDescuento']));
                        if (!ctype_digit($digits)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalDescuento debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($data['totalDescuento']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalDescuento debe contener hasta 14 digitos maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalDescuento no puede ser vacio';
                        $cont++;
                    }
                }
                //validar codDocReembolso
                if (isset($data['codDocReembolso'])) {
                    if ($data['codDocReembolso'] != '') {
                        if (strlen($data['codDocReembolso']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocReembolso debe contener 2 caracteres (' . $data['codDocReembolso'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['codDocReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocReembolso debe ser numerico (' . $data['codDocReembolso'] . ')';
                            $cont++;
                        }
                        if ($data['codDocReembolso'] != '01' && $data['codDocReembolso'] != '03' && $data['codDocReembolso'] != '04' && $data['codDocReembolso'] != '05' && $data['codDocReembolso'] != '06' && $data['codDocReembolso'] != '07') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocReembolso solo pueden ser una de siguientes opciones 01 - 03 - 04 - 05 - 06 - 07 (' . $data['codDocReembolso'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocReembolso no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocReembolso es obligatorio';
                    $cont++;
                }
                //totalComprobantesReembolso
                if (isset($data['totalComprobantesReembolso'])) {
                    if ($data['totalComprobantesReembolso'] != '') {
                        if (strlen($data['totalComprobantesReembolso']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalComprobantesReembolso debe tener una longitud maxima de 14';
                            $cont++;
                        }
                        if (!is_numeric($data['totalComprobantesReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalComprobantesReembolso debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalComprobantesReembolso no puede estar vacio';
                        $cont++;
                    }
                }
                //totalBaseImponibleReembolso
                if (isset($data['totalBaseImponibleReembolso'])) {
                    if ($data['totalBaseImponibleReembolso'] != '') {
                        if (strlen($data['totalBaseImponibleReembolso']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalBaseImponibleReembolso debe tener una longitud maxima de 14';
                            $cont++;
                        }
                        if (!is_numeric($data['totalBaseImponibleReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalBaseImponibleReembolso debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalBaseImponibleReembolso no puede estar vacio';
                        $cont++;
                    }
                }
                //totalImpuestoReembolso
                if (isset($data['totalImpuestoReembolso'])) {
                    if ($data['totalImpuestoReembolso'] != '') {
                        if (strlen($data['totalImpuestoReembolso']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalImpuestoReembolso debe tener una longitud maxima de 14';
                            $cont++;
                        }
                        if (!is_numeric($data['totalImpuestoReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalImpuestoReembolso debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalImpuestoReembolso no puede estar vacio';
                        $cont++;
                    }
                }
            }
            //totalConImpuestos
            if (isset($data['totalConImpuestos'])) {
                if (count($data['totalConImpuestos']) > 0) {
                    $error = $this->validarTotalImpuestos($data['totalConImpuestos'], $data['infoTributario']['codDoc'], $cont, $error);
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo totalConImpuestos debe contener al menos un registro';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo totalConImpuestos es obligatorio';
                $cont++;
            }
            //validar importeTotal
            if (isset($data['importeTotal'])) {
                if ($data['importeTotal'] != '') {
                    if (!is_numeric($data['importeTotal'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal debe ser numerico';
                        $cont++;
                    }
                    if (strlen($data['importeTotal']) > 14) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal debe contener hasta 14 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal es obligatorio';
                $cont++;
            }
            //validar moneda
            if (isset($data['moneda'])) {
                if ($data['moneda'] != '') {
                    //$digits = str_replace(array('.',','), array('',''), strrev($data['moneda']));
                    if (strlen($data['moneda']) > 14) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo moneda debe contener hasta 14 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo moneda no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo moneda es obligatorio';
                $cont++;
            }
            //validar Pagos
            if (isset($data['pagos'])) {
                if (count($data['pagos']) > 0) {
                    $error = $this->validarPagos($data['pagos'], $cont, $error);
                    $cont++;
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo pagos no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo pagos es obligatorio';
                $cont++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    public function validarInfoNotaCredito($data, &$cont, $error)
    {
        try {
            //fechaEmision
            if (isset($data['fechaEmision'])) {
                if ($data['fechaEmision'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no es valida (' . $data['fechaEmision'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no tiene el formato correcto dd/mm/yyyy (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision es obligatorio';
                $cont++;
            }
            //validar dirEstablecimiento
            if (isset($data['dirEstablecimiento'])) {
                if ($data['dirEstablecimiento'] != '') {
                    if (strlen($data['dirEstablecimiento']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento es obligatorio';
                $cont++;
            }
            //validar tipoIdentificacionComprador
            if (isset($data['tipoIdentificacionComprador'])) {
                if ($data['tipoIdentificacionComprador'] != '') {
                    if (strlen($data['tipoIdentificacionComprador']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador debe contener 2 caracteres (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['tipoIdentificacionComprador'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador debe ser numerico (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                    if ($data['tipoIdentificacionComprador'] != '04' && $data['tipoIdentificacionComprador'] != '05') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador solo puede ser 04 o 05 (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador es obligatorio';
                $cont++;
            }
            //validar razonSocial
            if (isset($data['razonSocialComprador'])) {
                if ($data['razonSocialComprador'] != '') {
                    if (strlen($data['razonSocialComprador']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador es obligatorio';
                $cont++;
            }
            //validar identificacionComprador
            if (isset($data['identificacionComprador'])) {
                if ($data['identificacionComprador'] != '') {
                    if (strlen($data['identificacionComprador']) > 20) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionComprador debe contener hasta 20 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario es obligatorio';
                $cont++;
            }
            //validar contribuyenteEspecial
            if (isset($data['contribuyenteEspecial'])) {
                if ($data['contribuyenteEspecial'] != '') {
                    if (strlen($data['contribuyenteEspecial']) >= 3 && strlen($data['contribuyenteEspecial']) <= 13) {
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo contribuyenteEspecial debe contener entre 3 y 13 caracteres (' . $data['contribuyenteEspecial'] . ')';
                        $cont++;
                    }
                }
            }
            //validar obligadoContabilidad
            if (isset($data['obligadoContabilidad'])) {
                if ($data['obligadoContabilidad'] != '') {
                    if (strtoupper($data['obligadoContabilidad']) != 'SI' && strtoupper($data['obligadoContabilidad']) != 'NO') {
                        $data['obligadoContabilidad'] = strtoupper($data['obligadoContabilidad']);
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad Solo puiede ser SI o NO (' . $data['obligadoContabilidad'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad no puede ser vacio';
                    $cont++;
                }
            }
            //validar rise
            if (isset($data['rise'])) {
                if ($data['rise'] != '') {
                    if (strlen($data['rise']) > 40) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo rise debe contener hasta 40 caracteres';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo rise no puede ser vacio';
                    $cont++;
                }
            }
            //validar codDocModificado
            if (isset($data['codDocModificado'])) {
                if ($data['codDocModificado'] != '') {
                    if (strlen($data['codDocModificado']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado debe contener 2 caracteres (' . $data['codDocModificado'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['codDocModificado'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado debe ser numerico (' . $data['codDocModificado'] . ')';
                        $cont++;
                    }
                    if ($data['codDocModificado'] != '01' && $data['codDocModificado'] != '03' && $data['codDocModificado'] != '04' && $data['codDocModificado'] != '05' && $data['codDocModificado'] != '06' && $data['codDocModificado'] != '07') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado solo pueden ser una de siguientes opciones 01 - 03 - 04 - 05 - 06 - 07 (' . $data['codDocModificado'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado es obligatorio';
                $cont++;
            }

            //validar numDocModificado
            $numDocM = str_replace('-', '', $data['numDocModificado']);
            if (isset($numDocM)) {
                if ($numDocM != '') {
                    if (strlen($numDocM) != 15) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocModificado debe contener 15 caracteres (' . $numDocM . ')';
                        $cont++;
                    }
                    if (!is_numeric($numDocM)) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocModificado debe ser numerico (' . $data['numDocModificado'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocModificado no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocModificado es obligatorio';
                $cont++;
            }

            //validar fechaEmisionDocSustento
            if (isset($data['fechaEmisionDocSustento'])) {
                if ($data['fechaEmisionDocSustento'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaEmisionDocSustento'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no es valida (' . $data['infoFactura']['fechaEmision'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaEmisionDocSustento'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no tiene el formato correcto dd/mm/yyyy (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento es obligatorio';
                $cont++;
            }

            //validar totalSinImpuestos
            if (isset($data['totalSinImpuestos'])) {
                if ($data['totalSinImpuestos'] != '') {
                    $digits = str_replace(array('.', ','), array('', ''), strrev($data['totalSinImpuestos']));
                    if (!is_numeric($digits)) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos debe contener solo numeros';
                        $cont++;
                    }
                    if (strlen($data['totalSinImpuestos']) > 14) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos debe contener hasta 14 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos no puede ser vacio';
                    $cont++;
                }
            }
            //validar valorModificacion
            if (isset($data['valorModificacion'])) {
                if ($data['valorModificacion'] != '') {
                    $digits = str_replace(array('.', ','), array('', ''), strrev($data['valorModificacion']));
                    if (!is_numeric($digits)) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo valorModificacion debe contener solo numeros';
                        $cont++;
                    }
                    if (strlen($data['valorModificacion']) > 14) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo valorModificacion debe contener hasta 14 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo valorModificacion no puede ser vacio';
                    $cont++;
                }
            }
            //totalConImpuestos
            if (isset($data['totalConImpuestos'])) {
                if (count($data['totalConImpuestos']) > 0) {
                    $error = $this->validarTotalImpuestos($data['totalConImpuestos'], $data['infoTributario']['codDoc'], $cont, $error);
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo totalConImpuestos debe contener al menos un registro';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo totalConImpuestos es obligatorio';
                $cont++;
            }
            //validar motivo
            if (isset($data['motivo'])) {
                if ($data['motivo'] != '') {
                    if (strlen($data['motivo']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo motivo debe contener hasta 300 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo motivo no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo motivo es obligatorio';
                $cont++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    public function validarInfoNotaDebito($data, &$cont, $error)
    {
        try {
            //fechaEmision
            if (isset($data['fechaEmision'])) {
                if ($data['fechaEmision'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no es valida (' . $data['fechaEmision'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no tiene el formato correcto dd/mm/yyyy (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision es obligatorio';
                $cont++;
            }
            //validar dirEstablecimiento
            if (isset($data['dirEstablecimiento'])) {
                if ($data['dirEstablecimiento'] != '') {
                    if (strlen($data['dirEstablecimiento']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento no puede ser vacio';
                    $cont++;
                }
            } else {

                $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento es obligatorio';
                $cont++;
            }
            //validar tipoIdentificacionComprador
            if (isset($data['tipoIdentificacionComprador'])) {
                if ($data['tipoIdentificacionComprador'] != '') {
                    if (strlen($data['tipoIdentificacionComprador']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador debe contener 2 caracteres (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['tipoIdentificacionComprador'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador debe ser numerico (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                    if ($data['tipoIdentificacionComprador'] != '04' && $data['tipoIdentificacionComprador'] != '05') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador solo puede ser 04 o 05 (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador es obligatorio';
                $cont++;
            }
            //validar razonSocial
            if (isset($data['razonSocialComprador'])) {
                if ($data['razonSocialComprador'] != '') {
                    if (strlen($data['razonSocialComprador']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador es obligatorio';
                $cont++;
            }
            //validar identificacionComprador
            if (isset($data['identificacionComprador'])) {
                if ($data['identificacionComprador'] != '') {
                    if (strlen($data['identificacionComprador']) > 20) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionComprador debe contener hasta 20 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario es obligatorio';
                $cont++;
            }
            //validar contribuyenteEspecial
            if (isset($data['contribuyenteEspecial'])) {
                if ($data['contribuyenteEspecial'] != '') {
                    if (strlen($data['contribuyenteEspecial']) >= 3 && strlen($data['contribuyenteEspecial']) <= 13) {
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo contribuyenteEspecial debe contener entre 3 y 13 caracteres (' . $data['contribuyenteEspecial'] . ')';
                        $cont++;
                    }
                }
            }
            //validar codDocModificado
            if (isset($data['codDocModificado'])) {
                if ($data['codDocModificado'] != '') {
                    if (strlen($data['codDocModificado']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado debe contener 2 caracteres (' . $data['codDocModificado'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['codDocModificado'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado debe ser numerico (' . $data['codDocModificado'] . ')';
                        $cont++;
                    }
                    if ($data['codDocModificado'] != '01' && $data['codDocModificado'] != '03' && $data['codDocModificado'] != '04' && $data['infoNotaCredito']['codDocModificado'] != '05' && $data['infoNotaCredito']['codDocModificado'] != '06' && $data['infoNotaCredito']['codDocModificado'] != '07') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado solo pueden ser una de siguientes opciones 01 - 03 - 04 - 05 - 06 - 07 (' . $data['infoNotaCredito']['codDocModificado'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado es obligatorio';
                $cont++;
            }
            //validar numDocModificado
            if (isset($data['numDocModificado'])) {
                if ($data['numDocModificado'] != '') {
                    if (strtoupper($data['numDocModificado']) != 'SI' && strtoupper($data['numDocModificado']) != 'NO') {
                        $data['numDocModificado'] = strtoupper($data['numDocModificado']);
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocModificado Solo puiede ser SI o NO (' . $data['numDocModificado'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocModificado no puede ser vacio';
                    $cont++;
                }
            }
            //validar moneda
            if (isset($data['moneda'])) {
                if ($data['moneda'] != '') {
                    if (strlen($data['moneda']) > 15) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo moneda debe contener max 15 caracteres (' . $data['moneda'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo moneda no puede ser vacio';
                    $cont++;
                }
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    public function validarInfoCompRetencion($data, &$cont, $error)
    {
        try {
            //fechaEmision
            if (isset($data['fechaEmision'])) {
                if ($data['fechaEmision'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no es valida (' . $data['fechaEmision'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no tiene el formato correcto dd/mm/yyyy (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision es obligatorio';
                $cont++;
            }
            //validar dirEstablecimiento
            if (isset($data['dirEstablecimiento'])) {
                if ($data['dirEstablecimiento'] != '') {
                    if (strlen($data['dirEstablecimiento']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento es obligatorio';
                $cont++;
            }
            //validar contribuyenteEspecial
            if (isset($data['contribuyenteEspecial'])) {
                if ($data['contribuyenteEspecial'] != '') {
                    if (strlen($data['contribuyenteEspecial']) >= 3 && strlen($data['contribuyenteEspecial']) <= 13) {
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo contribuyenteEspecial debe contener entre 3 y 13 caracteres (' . $data['contribuyenteEspecial'] . ')';
                        $cont++;
                    }
                }
            }
            //validar obligadoContabilidad
            if (isset($data['obligadoContabilidad'])) {
                if ($data['obligadoContabilidad'] != '') {
                    if (strtoupper($data['obligadoContabilidad']) != 'SI' && strtoupper($data['obligadoContabilidad']) != 'NO') {
                        $data['obligadoContabilidad'] = strtoupper($data['obligadoContabilidad']);
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad Solo puiede ser SI o NO (' . $data['obligadoContabilidad'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad no puede ser vacio';
                    $cont++;
                }
            }
            //validar tipoIdentificacionSujetoRetenido
            if (isset($data['tipoIdentificacionSujetoRetenido'])) {
                if ($data['tipoIdentificacionSujetoRetenido'] != '') {
                    if (strlen($data['tipoIdentificacionSujetoRetenido']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionSujetoRetenido debe contener 2 caracteres (' . $data['tipoIdentificacionSujetoRetenido'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['tipoIdentificacionSujetoRetenido'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionSujetoRetenido debe ser numerico (' . $data['tipoIdentificacionSujetoRetenido'] . ')';
                        $cont++;
                    }
                    if ($data['tipoIdentificacionSujetoRetenido'] != '04' && $data['tipoIdentificacionSujetoRetenido'] != '05' && $data['tipoIdentificacionSujetoRetenido'] != '06' && $data['tipoIdentificacionSujetoRetenido'] != '08') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionSujetoRetenido solo puede ser 04, 05, 06, o 08 (' . $data['tipoIdentificacionSujetoRetenido'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionSujetoRetenido no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionSujetoRetenido es obligatorio';
                $cont++;
            }
            //validar razonSocialSujetoRetenido
            if (isset($data['razonSocialSujetoRetenido'])) {
                if ($data['razonSocialSujetoRetenido'] != '') {
                    if (strlen($data['razonSocialSujetoRetenido']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialSujetoRetenido debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialSujetoRetenido no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialSujetoRetenido es obligatorio';
                $cont++;
            }
            //validar identificacionSujetoRetenido
            if (isset($data['identificacionSujetoRetenido'])) {
                if ($data['identificacionSujetoRetenido'] != '') {
                    if (strlen($data['identificacionSujetoRetenido']) > 20) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionSujetoRetenido debe contener hasta 20 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionSujetoRetenido no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionSujetoRetenido es obligatorio';
                $cont++;
            }
            //periodoFiscal
            if (isset($data['periodoFiscal'])) {
                if ($data['periodoFiscal'] != '') {
                    if (!$this->validarFechaCorrecta($data['periodoFiscal'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo periodoFiscal no es valida (' . $data['periodoFiscal'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['periodoFiscal'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo periodoFiscal no tiene el formato correcto dd/mm/yyyy (' . $data['periodoFiscal'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo periodoFiscal no puede ser vacio';
                    $cont++;
                }
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarTotalImpuestos($data, $codDoc = '', &$cont, $error = '')
    {
        try {
            $conImp = 0;
            foreach ($data as $impuesto) {
                //codigo
                if (isset($impuesto['codigo'])) {
                    if ($impuesto['codigo'] != '') {
                        $codigo = $impuesto['codigo'];
                        if ($impuesto['codigo'] != '2' && $impuesto['codigo'] != '3' && $impuesto['codigo'] != '5') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del totalImpuesto ' . ($conImp + 1) . ' debe ser 1,2 o 6';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del totalImpuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del totalImpuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //codigoPorcentaje
                if (isset($impuesto['codigoPorcentaje'])) {
                    if ($impuesto['codigoPorcentaje'] != '') {
                        $codigoPorcentaje = $impuesto['codigoPorcentaje'];
                        if (strlen($impuesto['codigoPorcentaje']) < 1 || strlen($impuesto['codigoPorcentaje']) > 5) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del totalImpuesto ' . ($conImp + 1) . ' debe contener entre 1 y 5 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del totalImpuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del totalImpuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                if ($codDoc == '03') {
                    //descuentoAdicional
                    if (isset($impuesto['descuentoAdicional'])) {
                        if ($impuesto['descuentoAdicional'] != '') {
                            $digitsCod = str_replace(array('.', ','), array('', ''), strrev($impuesto['descuentoAdicional']));
                            if (!is_numeric($digitsCod)) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo descuentoAdicional del totalImpuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                                $cont++;
                            }
                            if (strlen($impuesto['descuentoAdicional']) > 14) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo descuentoAdicional del totalImpuesto ' . ($conImp + 1) . ' debe tener una longitud maxima de 14';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo descuentoAdicional del totalImpuesto ' . ($conImp + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    }
                    //tarifa
                    if (isset($impuesto['tarifa'])) {
                        if ($impuesto['tarifa'] != '') {
                            $digitsCod = str_replace(array('.', ','), array('', ''), strrev($impuesto['tarifa']));
                            if (!is_numeric($digitsCod)) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa del totalImpuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                                $cont++;
                            }
                            if (strlen($impuesto['tarifa']) < 1 && strlen($impuesto['tarifa']) > 4) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa del totalImpuesto ' . ($conImp + 1) . ' debe tener una longitud entre 1 y 4';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa del totalImpuesto ' . ($conImp + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa del totalImpuesto ' . ($conImp + 1) . ' es obligatorio';
                        $cont++;
                    }
                }
                //baseImponible
                if (isset($impuesto['baseImponible'])) {
                    if ($impuesto['baseImponible'] != '') {
                        $digitsCod = str_replace(array('.', ','), array('', ''), strrev($impuesto['baseImponible']));
                        if (!is_numeric($digitsCod)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($impuesto['baseImponible']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' debe tener una longitud maxima de 14';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del totalImpuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //valor
                if (isset($impuesto['valor'])) {
                    if ($impuesto['valor'] != '') {
                        if (!is_numeric($impuesto['valor'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($impuesto['valor']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del impuesto ' . ($conImp + 1) . ' debe tener una longitud maxima de 14';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del totalImpuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //validar Informacion impuesto
                if ($codigo != '' && $codigoPorcentaje != '') {
                    $deCodigo = De_Codigo_Impuestos::where('codigo_impuesto', $codigo)->where('codigo', $codigoPorcentaje)->first();

                    if ($deCodigo != '') {
                        if ($codigoPorcentaje != $deCodigo->codigo) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del totalImpuesto ' . ($conImp + 1) . ' no puede ser vacio';
                            $cont++;
                        }

                        if ($codigo != $deCodigo->codigo_impuesto) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del totalImpuesto ' . ($conImp + 1) . ' no corresponde al codigo del impuesto';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el codigoPorcentaje (' . $codigoPorcentaje . ') del impuesto ' . ($conImp + 1) . ' se encuentra desactualizado o es incorrecto';
                        $cont++;
                    }
                }
                $conImp++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . ': ' . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarDocsSustento($data, &$cont, $error)
    {
        try {
            $conDoc = 0;
            foreach ($data as $docSustento) {
                //codSustento
                if (isset($docSustento['codSustento'])) {
                    if ($docSustento['codSustento'] != '') {
                        if (!is_numeric($docSustento['codSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codSustento del docSustento ' . ($conDoc + 1) . ' solo puede ser numerico';
                            $cont++;
                        }
                        if (strlen($docSustento['codSustento']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codSustento del docSustento ' . ($conDoc + 1) . ' puede ser maximo de 2 digitos';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codSustento del docSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codSustento del docSustento ' . ($conDoc + 1) . ' es obligatorio';
                    $cont++;
                }
                //codDocSustento
                if (isset($docSustento['codDocSustento'])) {
                    if ($docSustento['codDocSustento'] != '') {
                        $codDocSustento = $docSustento['codDocSustento'];
                        if (strlen($docSustento['codDocSustento']) < 2 && strlen($docSustento['codDocSustento']) > 3) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento debe contener entre 2 y 3 caracteres (' . $docSustento['codDocSustento'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($docSustento['codDocSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento del docSustento ' . ($conDoc + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento del docSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento del docSustento ' . ($conDoc + 1) . ' es obligatorio';
                    $cont++;
                }
                //numDocSustento
                if (isset($docSustento['numDocSustento'])) {
                    if ($docSustento['numDocSustento'] != '') {
                        if (!is_numeric($docSustento['numDocSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento del DocSustento ' . ($conDoc + 1) . ' debe ser numerico';
                            $cont++;
                        }
                        if (strlen($docSustento['numDocSustento']) != 15) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento del DocSustento ' . ($conDoc + 1) . ' debe tener una longitud de 15';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento del DocSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                //fechaEmisionDocSustento
                if (isset($docSustento['fechaEmisionDocSustento'])) {
                    if ($docSustento['fechaEmisionDocSustento'] != '') {
                        if (!$this->validarFechaCorrecta($docSustento['fechaEmisionDocSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento no es valida (' . $docSustento['fechaEmisionDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento del docSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento del docSustento ' . ($conDoc + 1) . ' es obligatorio';
                    $cont++;
                }
                //fechaRegistroContable
                if (isset($docSustento['fechaRegistroContable'])) {
                    if ($docSustento['fechaRegistroContable'] != '') {
                        if (!$this->validarFechaCorrecta($docSustento['fechaRegistroContable'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaRegistroContable no es valida (' . $docSustento['fechaRegistroContable'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaRegistroContable del docSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                //numAutDocSustento
                if (isset($docSustento['numAutDocSustento'])) {
                    if ($docSustento['numAutDocSustento'] != '') {
                        if (!is_numeric($docSustento['numAutDocSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento del DocSustento ' . ($conDoc + 1) . ' debe ser numerico';
                            $cont++;
                        }
                        if (strlen($docSustento['numAutDocSustento']) != 10 || strlen($docSustento['numAutDocSustento']) != 37 || strlen($docSustento['numAutDocSustento']) != 49) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento del DocSustento ' . ($conDoc + 1) . ' debe tener una longitud de 10, 37 o 49';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento del DocSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                //pagoLocExt
                if (isset($docSustento['pagoLocExt'])) {
                    if ($docSustento['pagoLocExt'] != '') {
                        if (!is_numeric($docSustento['pagoLocExt'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo pagoLocExt del DocSustento ' . ($conDoc + 1) . ' debe ser numerico';
                            $cont++;
                        }
                        if (strlen($docSustento['pagoLocExt']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo pagoLocExt del DocSustento ' . ($conDoc + 1) . ' debe tener una longitud de 10, 37 o 49';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo pagoLocExt del DocSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                //tipoRegi
                if (isset($docSustento['tipoRegi'])) {
                    if ($docSustento['tipoRegi'] != '') {
                        if (!is_numeric($docSustento['tipoRegi'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoRegi del DocSustento ' . ($conDoc + 1) . ' debe ser numerico';
                            $cont++;
                        }
                        if (strlen($docSustento['tipoRegi']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoRegi del DocSustento ' . ($conDoc + 1) . ' debe tener una longitud de 2';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoRegi del DocSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                //paisEfecPago
                if (isset($docSustento['paisEfecPago'])) {
                    if ($docSustento['paisEfecPago'] != '') {
                        if (!is_numeric($docSustento['paisEfecPago'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo paisEfecPago del DocSustento ' . ($conDoc + 1) . ' debe ser numerico';
                            $cont++;
                        }
                        if (strlen($docSustento['paisEfecPago']) != 3) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo paisEfecPago del DocSustento ' . ($conDoc + 1) . ' debe tener una longitud de 3';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo paisEfecPago del DocSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                //aplicConvDobTrib
                if (isset($docSustento['aplicConvDobTrib'])) {
                    if ($docSustento['aplicConvDobTrib'] != '') {
                        if ($docSustento['aplicConvDobTrib'] != 'SI' || $docSustento['aplicConvDobTrib'] != 'NO') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo aplicConvDobTrib del DocSustento ' . ($conDoc + 1) . ' debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo aplicConvDobTrib del DocSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                //pagExtSujRetNorLeg
                if (isset($docSustento['pagExtSujRetNorLeg'])) {
                    if ($docSustento['pagExtSujRetNorLeg'] != '') {
                        if ($docSustento['pagExtSujRetNorLeg'] != 'SI' || $docSustento['pagExtSujRetNorLeg'] != 'NO') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo pagExtSujRetNorLeg del DocSustento ' . ($conDoc + 1) . ' debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo pagExtSujRetNorLeg del DocSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                //totalComprobantesReembolso
                if (isset($docSustento['totalComprobantesReembolso'])) {
                    if ($docSustento['totalComprobantesReembolso'] != '') {
                        if (strlen($docSustento['totalComprobantesReembolso']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalComprobantesReembolso del DocSustento ' . ($conDoc + 1) . ' debe tener una longitud maxima de 14';
                            $cont++;
                        }
                        if (!is_numeric($docSustento['totalComprobantesReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalComprobantesReembolso del DocSustento ' . ($conDoc + 1) . ' debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalComprobantesReembolso del DocSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                //totalBaseImponibleReembolso
                if (isset($docSustento['totalBaseImponibleReembolso'])) {
                    if ($docSustento['totalBaseImponibleReembolso'] != '') {
                        if (strlen($docSustento['totalBaseImponibleReembolso']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalBaseImponibleReembolso del DocSustento ' . ($conDoc + 1) . ' debe tener una longitud maxima de 14';
                            $cont++;
                        }
                        if (!is_numeric($docSustento['totalBaseImponibleReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalBaseImponibleReembolso del DocSustento ' . ($conDoc + 1) . ' debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalBaseImponibleReembolso del DocSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                //totalImpuestoReembolso
                if (isset($docSustento['totalImpuestoReembolso'])) {
                    if ($docSustento['totalImpuestoReembolso'] != '') {
                        if (strlen($docSustento['totalImpuestoReembolso']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalImpuestoReembolso del DocSustento ' . ($conDoc + 1) . ' debe tener una longitud maxima de 14';
                            $cont++;
                        }
                        if (!is_numeric($docSustento['totalImpuestoReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalImpuestoReembolso del DocSustento ' . ($conDoc + 1) . ' debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalImpuestoReembolso del DocSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                //totalSinImpuestos
                if (isset($docSustento['totalSinImpuestos'])) {
                    if ($docSustento['totalSinImpuestos'] != '') {
                        if (strlen($docSustento['totalSinImpuestos']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos del DocSustento ' . ($conDoc + 1) . ' debe tener una longitud maxima de 14';
                            $cont++;
                        }
                        if (!is_numeric($docSustento['totalSinImpuestos'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos del DocSustento ' . ($conDoc + 1) . ' debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos del DocSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos del DocSustento ' . ($conDoc + 1) . ' es obligatorio';
                    $cont++;
                }
                //importeTotal
                if (isset($docSustento['importeTotal'])) {
                    if ($docSustento['importeTotal'] != '') {
                        if (strlen($docSustento['importeTotal']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal del DocSustento ' . ($conDoc + 1) . ' debe tener una longitud maxima de 14';
                            $cont++;
                        }
                        if (!is_numeric($docSustento['importeTotal'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal del DocSustento ' . ($conDoc + 1) . ' debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal del DocSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal del DocSustento ' . ($conDoc + 1) . ' es obligatorio';
                    $cont++;
                }
                //impuestosDocSustento
                if (isset($docSustento['impuestosDocSustento'])) {
                    if ($docSustento['impuestosDocSustento'] != '') {
                        $error = $this->validarImpuestosDocSustento($docSustento['impuestosDocSustento'], $cont, $error);
                        $cont++;
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal del DocSustento ' . ($conDoc + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal del DocSustento ' . ($conDoc + 1) . ' es obligatorio';
                    $cont++;
                }
                if ($docSustento['codDocSustento'] == '41') {
                    //validar Retenciones
                    if (isset($docSustento['retenciones'])) {
                        if (count($docSustento['retenciones']) > 0) {
                            $error = $this->validarRetenciones($docSustento['retenciones'], $cont, $docSustento['codDocSustento'], $error);
                            $cont++;
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo retenciones no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo retenciones es obligatorio';
                        $cont++;
                    }
                    //validar reembolsos
                    if (isset($docSustento['reembolsos'])) {
                        if (count($docSustento['reembolsos']) > 0) {
                            $error = $this->validarReembolsos($docSustento['reembolsos'], '07', $cont, $error);
                            $cont++;
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo reembolsos no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo reembolsos es obligatorio';
                        $cont++;
                    }
                }
                //validar Pagos
                if (isset($docSustento['pagos'])) {
                    if (count($docSustento['pagos']) > 0) {
                        $error = $this->validarPagosRetenciones($docSustento['pagos'], $cont, $error);
                        $cont++;
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo pagos no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo pagos es obligatorio';
                    $cont++;
                }
                $conDoc++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarImpuestosDocSustento($data, &$cont, $error)
    {
        try {
            $conImp = 0;
            foreach ($data as $impuesto) {
                //codImpuestoDocSustento
                if (isset($impuesto['codImpuestoDocSustento'])) {
                    if ($impuesto['codImpuestoDocSustento'] != '') {
                        $codImpuestoDocSustento = $impuesto['codImpuestoDocSustento'];
                        if ($impuesto['codImpuestoDocSustento'] != '2' && $impuesto['codImpuestoDocSustento'] != '3' && $impuesto['codImpuestoDocSustento'] != '5') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codImpuestoDocSustento del impuesto ' . ($conImp + 1) . ' debe ser 1,2 o 5';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codImpuestoDocSustento del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codImpuestoDocSustento del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //codigoPorcentaje
                if (isset($impuesto['codigoPorcentaje'])) {
                    if ($impuesto['codigoPorcentaje'] != '') {
                        $codigoPorcentaje = $impuesto['codigoPorcentaje'];
                        if (strlen($impuesto['codigoPorcentaje']) < 1 || strlen($impuesto['codigoPorcentaje']) > 4) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' debe contener entre 1 y 5 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //baseImponible
                if (isset($impuesto['baseImponible'])) {
                    if ($impuesto['baseImponible'] != '') {
                        $digitsCod = str_replace(array('.', ','), array('', ''), strrev($impuesto['baseImponible']));
                        if (!is_numeric($digitsCod)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($impuesto['baseImponible']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible debe contener un maximo de 14 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //tarifa
                if (isset($impuesto['tarifa'])) {
                    if ($impuesto['tarifa'] != '') {
                        if (!is_numeric($impuesto['tarifa'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($impuesto['tarifa']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa debe contener un maximo de 14 caracteres';
                            $cont++;
                        }
                        $tarifa = $impuesto['tarifa'];
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //valorImpuesto
                if (isset($impuesto['valorImpuesto'])) {
                    if ($impuesto['valorImpuesto'] != '') {
                        if (!is_numeric($impuesto['valorImpuesto'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo valorImpuesto del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($impuesto['valorImpuesto']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo valorImpuesto debe contener un maximo de 14 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo valorImpuesto del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo valorImpuesto del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //validar Informacion impuesto
                if ($codImpuestoDocSustento != '' && $codigoPorcentaje != '') {
                    $deCodigo = De_Codigo_Impuestos::where('codigo_impuesto', $codImpuestoDocSustento)->where('codigo', $codigoPorcentaje)->first();
                    if ($deCodigo != '') {
                        if ($codigoPorcentaje != $deCodigo->codigo) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' no puede ser vacio';
                            $cont++;
                        }
                        if ($codImpuestoDocSustento != $deCodigo->codigo_impuesto) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' no corresponde al codigo del impuesto';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el codigoPorcentaje (' . $codigoPorcentaje . ') del impuesto ' . ($conImp + 1) . ' se encuentra desactualizado o es incorrecto';
                        $cont++;
                    }
                }
                $conImp++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarRetenciones($data, &$cont, $codDoc, $error)
    {
        try {
            $conImp = 0;
            foreach ($data as $retencion) {
                //codImpuestoDocSustento
                if (isset($retencion['codigo'])) {
                    if ($retencion['codigo'] != '') {
                        $codigo = $retencion['codigo'];
                        if ($retencion['codigo'] != '2' && $retencion['codigo'] != '3' && $retencion['codigo'] != '5') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo de la retencion ' . ($conImp + 1) . ' debe ser 1,2 o 6';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo de la retencion ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo de la retencion ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //codigoRetencion
                if (isset($retencion['codigoRetencion'])) {
                    if ($retencion['codigoRetencion'] != '') {
                        $codigoRetencion = $retencion['codigoRetencion'];
                        if (strlen($retencion['codigoRetencion']) < 1 || strlen($retencion['codigoRetencion']) > 5) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoRetencion de la retencion ' . ($conImp + 1) . ' debe contener entre 1 y 5 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoRetencion de la retencion ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoRetencion de la retencion ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //baseImponible
                if (isset($retencion['baseImponible'])) {
                    if ($retencion['baseImponible'] != '') {
                        $digitsCod = str_replace(array('.', ','), array('', ''), strrev($retencion['baseImponible']));
                        if (!is_numeric($digitsCod)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible de la retencion ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($retencion['baseImponible']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible debe contener un maximo de 14 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible de la retencion ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible de la retencion ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //porcentajeRetener
                if (isset($retencion['porcentajeRetener'])) {
                    if ($retencion['porcentajeRetener'] != '') {
                        if (!is_numeric($retencion['porcentajeRetener'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo porcentajeRetener de la retencion ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($retencion['porcentajeRetener']) < 1 || strlen($retencion['porcentajeRetener']) > 5) {

                            $error[$cont] = "Error " . ($cont + 1) . ': el campo porcentajeRetener debe contener un maximo de 14 caracteres';
                            $cont++;
                        }
                        // $porcentajeRetener = $retencion['porcentajeRetener'];
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo porcentajeRetener de la retencion ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo porcentajeRetener de la retencion ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //valorRetenido
                if (isset($retencion['valorRetenido'])) {
                    if ($retencion['valorRetenido'] != '') {
                        if (!is_numeric($retencion['valorRetenido'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo valorRetenido de la retencion ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($retencion['valorRetenido']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo valorRetenido debe contener un maximo de 14 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo valorRetenido de la retencion ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo valorRetenido del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //validar Informacion impuesto
                if ($codigo != '' && $codigoRetencion != '') {
                    $deCodigo = De_Codigo_Impuestos::where('codigo_impuesto', $codigo)->where('codigo', $codigoRetencion)->first();
                    if ($deCodigo != '') {
                        if ($codigoRetencion != $deCodigo->codigo) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoRetencion del impuesto ' . ($conImp + 1) . ' no puede ser vacio';
                            $cont++;
                        }

                        if ($codigo != $deCodigo->codigo_impuesto) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoRetencion del impuesto ' . ($conImp + 1) . ' no corresponde al codigo del impuesto';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el codigoRetencion (' . $codigoRetencion . ') del impuesto ' . ($conImp + 1) . ' se encuentra desactualizado o es incorrecto';
                        $cont++;
                    }
                }
                //dividendos
                if ($codDoc == '10') {
                    if (isset($retencion['dividendos'])) {
                        if (count($retencion['dividendos']) > 0) {
                            $error = $this->validarDividendos($retencion['dividendos'], $cont, $error);
                            $cont++;
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo dividendos no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dividendos es obligatorio';
                        $cont++;
                    }
                }
                //compraCajBanano
                if (isset($retencion['compraCajBanano'])) {
                    if (count($retencion['compraCajBanano']) > 0) {
                        $error = $this->validarCompraCajBanano($retencion['compraCajBanano'], $cont, $error);
                        $cont++;
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo compraCajBanano no puede ser vacio';
                        $cont++;
                    }
                }
                $conImp++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarDividendos($data, &$cont, $error)
    {
        try {
            //fechaPagoDiv
            if (isset($data['fechaPagoDiv'])) {
                if ($data['fechaPagoDiv'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaPagoDiv'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaPagoDiv debe ser 1,2 o 6';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaPagoDiv no puede estar vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaPagoDiv es obligatorio';
                $cont++;
            }
            //imRentaSoc
            if (isset($data['imRentaSoc'])) {
                if ($data['imRentaSoc'] != '') {
                    if (strlen($data['imRentaSoc']) > 15) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo imRentaSoc debe tener maximo 15 digios';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo imRentaSoc no puede estar vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo imRentaSoc es obligatorio';
                $cont++;
            }
            //ejerFisUtDiv
            if (isset($data['ejerFisUtDiv'])) {
                if ($data['ejerFisUtDiv'] != '') {
                    if (strlen($data['ejerFisUtDiv']) != 4) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ejerFisUtDiv debe tener 4 caracteres';
                        $cont++;
                    }
                    if (!is_numeric($data['ejerFisUtDiv'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ejerFisUtDiv debe contener solo numeros';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo ejerFisUtDiv no puede estar vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo ejerFisUtDiv es obligatorio';
                $cont++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarCompraCajBanano($data, &$cont, $error)
    {
        try {
            //NumCajBan
            if (isset($data['NumCajBan'])) {
                if ($data['NumCajBan'] != '') {
                    if ($data['NumCajBan']) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo NumCajBan debe ser 1,2 o 6';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo NumCajBan no puede estar vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo NumCajBan es obligatorio';
                $cont++;
            }
            //PrecCajBan
            if (isset($data['PrecCajBan'])) {
                if ($data['PrecCajBan'] != '') {
                    if (strlen($data['PrecCajBan']) > 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo PrecCajBan debe tener maximo 2 digios';
                        $cont++;
                    }
                    if (!is_numeric($data['PrecCajBan'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo PrecCajBan debe ser numerico';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo PrecCajBan no puede estar vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo PrecCajBan es obligatorio';
                $cont++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
    }
    private function validarReembolsos($data, $codDoc, &$cont, $error)
    {
        try {
            foreach ($data as $reembolsos) {
                //reembolsoDetalle
                if (isset($reembolsos['reembolsoDetalle'])) {
                    if (count($reembolsos['reembolsoDetalle']) > 0) {
                        $error = $this->validarReembolsoDetalle($reembolsos['reembolsoDetalle'], $codDoc, $cont, $error);
                        $cont++;
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo reembolsoDetalle no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo reembolsoDetalle es obligatorio';
                    $cont++;
                }
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarReembolsoDetalle($data, $codDoc, &$cont, $error)
    {
        try {
            $codDocSustento = '';
            foreach ($data as $reembolsoDetalle) {
                //tipoIdentificacionProveedorReembolso
                if (isset($reembolsoDetalle['tipoIdentificacionProveedorReembolso'])) {
                    if ($reembolsoDetalle['tipoIdentificacionProveedorReembolso'] != '') {
                        if (!is_numeric($reembolsoDetalle['tipoIdentificacionProveedorReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionProveedorReembolso debe ser numerico';
                            $cont++;
                        }
                        if (strlen($reembolsoDetalle['tipoIdentificacionProveedorReembolso']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionProveedorReembolso debe tener una longitud de 2';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionProveedorReembolso no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionProveedorReembolso es obligatorio';
                    $cont++;
                }
                //identificacionProveedorReembolso
                if (isset($reembolsoDetalle['identificacionProveedorReembolso'])) {
                    if ($reembolsoDetalle['identificacionProveedorReembolso'] != '') {
                        if (strlen($reembolsoDetalle['identificacionProveedorReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionProveedorReembolso debe tener maximo 20 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionProveedorReembolso no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionProveedorReembolso es obligatorio';
                    $cont++;
                }
                //codPaisPagoProveedorReembolso
                if (isset($reembolsoDetalle['codPaisPagoProveedorReembolso'])) {
                    if ($reembolsoDetalle['codPaisPagoProveedorReembolso'] != '') {
                        if (strlen($reembolsoDetalle['codPaisPagoProveedorReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codPaisPagoProveedorReembolso debe tener maximo 20 caracteres';
                            $cont++;
                        }
                        if (!is_numeric($reembolsoDetalle['codPaisPagoProveedorReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codPaisPagoProveedorReembolso debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codPaisPagoProveedorReembolso no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codPaisPagoProveedorReembolso es obligatorio';
                    $cont++;
                }
                //tipoProveedorReembolso
                if (isset($reembolsoDetalle['tipoProveedorReembolso'])) {
                    if ($reembolsoDetalle['tipoProveedorReembolso'] != '') {
                        if ($reembolsoDetalle['tipoProveedorReembolso'] != '01' || $reembolsoDetalle['tipoProveedorReembolso'] != '02') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoProveedorReembolso debe ser 01 o 02';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoProveedorReembolso no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoProveedorReembolso es obligatorio';
                    $cont++;
                }
                //codDocReembolso
                if (isset($reembolsoDetalle['codDocReembolso'])) {
                    if ($reembolsoDetalle['codDocReembolso'] != '') {
                        $codDocSustento = $reembolsoDetalle['codDocReembolso'];
                        if (strlen($reembolsoDetalle['codDocReembolso']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocReembolso debe tener una longitud de 2';
                            $cont++;
                        }
                        if (!is_numeric($reembolsoDetalle['codPaisPagoProveedorReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codPaisPagoProveedorReembolso debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocReembolso no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocReembolso es obligatorio';
                    $cont++;
                }
                //estabDocReembolso
                if (isset($reembolsoDetalle['estabDocReembolso'])) {
                    if ($reembolsoDetalle['estabDocReembolso'] != '') {
                        if (strlen($reembolsoDetalle['estabDocReembolso']) != 3) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo estabDocReembolso debe tener una longitud de 3';
                            $cont++;
                        }
                        if (!is_numeric($reembolsoDetalle['estabDocReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo estabDocReembolso debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo estabDocReembolso no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo estabDocReembolso es obligatorio';
                    $cont++;
                }
                //ptoEmiDocReembolso
                if (isset($reembolsoDetalle['ptoEmiDocReembolso'])) {
                    if ($reembolsoDetalle['ptoEmiDocReembolso'] != '') {
                        if (strlen($reembolsoDetalle['ptoEmiDocReembolso']) != 3) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmiDocReembolso debe tener una longitud de 3';
                            $cont++;
                        }
                        if (!is_numeric($reembolsoDetalle['ptoEmiDocReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmiDocReembolso debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmiDocReembolso no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmiDocReembolso es obligatorio';
                    $cont++;
                }
                //
                if ($codDoc == '03' && $codDocSustento == '41') {
                }
                //fechaEmisionDocReembolso
                if (!$this->validarFechaCorrecta($reembolsoDetalle['fechaEmisionDocReembolso'])) {
                    if ($reembolsoDetalle['fechaEmisionDocReembolso'] != '') {
                        if (strlen($reembolsoDetalle['fechaEmisionDocReembolso']) != 3) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocReembolso debe tener una longitud de 3';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocReembolso no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocReembolso es obligatorio';
                    $cont++;
                }
                //numeroAutorizacionDocReemb
                if (isset($reembolsoDetalle['numeroAutorizacionDocReemb'])) {
                    if ($reembolsoDetalle['numeroAutorizacionDocReemb'] != '') {
                        if (strlen($reembolsoDetalle['numeroAutorizacionDocReemb']) != 10 || strlen($reembolsoDetalle['numeroAutorizacionDocReemb']) != 37 || strlen($reembolsoDetalle['numeroAutorizacionDocReemb']) != 49) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numeroAutorizacionDocReemb debe tener una longitud de 10, 37 o 49';
                            $cont++;
                        }
                        if (!is_numeric($reembolsoDetalle['numeroAutorizacionDocReemb'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numeroAutorizacionDocReemb debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numeroAutorizacionDocReemb no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo numeroAutorizacionDocReemb es obligatorio';
                    $cont++;
                }
                //validar detalleImpuestos
                if (isset($docSustento['detalleImpuestos'])) {
                    if (count($docSustento['detalleImpuestos']) > 0) {
                        $error = $this->validarDetalleImpuestos($docSustento['detalleImpuestos'], $cont, $error);
                        $cont++;
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo detalleImpuestos no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo detalleImpuestos es obligatorio';
                    $cont++;
                }
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarDetalleImpuestos($data, &$cont, $error)
    {
        try {
            foreach ($data as $impuestos) {
                //detalleImpuesto
                if (isset($impuestos['detalleImpuesto'])) {
                    if (count($impuestos['detalleImpuesto']) > 0) {
                        $error = $this->validarDetalleImpuesto($impuestos['detalleImpuesto'], $cont, $error);
                        $cont++;
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo detalleImpuesto no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo detalleImpuesto es obligatorio';
                    $cont++;
                }
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarDetalleImpuesto($data, &$cont, $error)
    {
        try {
            $conImp = 0;
            foreach ($data as $detalleImpuesto) {
                //codigo
                if (isset($detalleImpuesto['codigo'])) {
                    if ($detalleImpuesto['codigo'] != '') {
                        $codigo = $detalleImpuesto['codigo'];
                        if ($detalleImpuesto['codigo'] != '1' && $detalleImpuesto['codigo'] != '2' && $detalleImpuesto['codigo'] != '6') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del detalleImpuesto ' . ($conImp + 1) . ' debe ser 1,2 o 6';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del detalleImpuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del detalleImpuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //codigoPorcentaje
                if (isset($detalleImpuesto['codigoPorcentaje'])) {
                    if ($detalleImpuesto['codigoPorcentaje'] != '') {
                        $codigoPorcentaje = $detalleImpuesto['codigoPorcentaje'];
                        if (strlen($detalleImpuesto['codigoPorcentaje']) < 1 || strlen($detalleImpuesto['codigoPorcentaje']) > 5) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del detalleImpuesto ' . ($conImp + 1) . ' debe contener entre 1 y 5 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del detalleImpuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del detalleImpuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //tarifa
                if (isset($detalleImpuesto['tarifa'])) {
                    if ($detalleImpuesto['tarifa'] != '') {
                        if (strlen($detalleImpuesto['tarifa']) < 1 || strlen($detalleImpuesto['tarifa']) > 4) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa debe tener entre 1 y 4 caracteres';
                            $cont++;
                        }
                        if (!is_numeric($detalleImpuesto['tarifa'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa es obligatorio';
                    $cont++;
                }
                //baseImponibleReembolso
                if (isset($detalleImpuesto['baseImponibleReembolso'])) {
                    if ($detalleImpuesto['baseImponibleReembolso'] != '') {
                        $digitsCod = str_replace(array('.', ','), array('', ''), strrev($detalleImpuesto['baseImponibleReembolso']));
                        if (!is_numeric($digitsCod)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponibleReembolso del detalleImpuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($detalleImpuesto['baseImponibleReembolso']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponibleReembolso del detalleImpuesto ' . ($conImp + 1) . ' debe tener maximo 14 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponibleReembolso del detalleImpuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponibleReembolso del detalleImpuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //impuestoReembolso
                if (isset($detalleImpuesto['codDocReembolso'])) {
                    if ($detalleImpuesto['codDocReembolso'] != '') {
                        if (strlen($detalleImpuesto['codDocReembolso']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocReembolso debe tener una longitud de 2';
                            $cont++;
                        }
                        if (!is_numeric($detalleImpuesto['codPaisPagoProveedorReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codPaisPagoProveedorReembolso debe ser numerico';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocReembolso no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocReembolso es obligatorio';
                    $cont++;
                }
                //estabDocReembolso
                if (isset($detalleImpuesto['impuestoReembolso'])) {
                    if ($detalleImpuesto['impuestoReembolso'] != '') {
                        if (!is_numeric($detalleImpuesto['impuestoReembolso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo impuestoReembolso del detalleImpuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($detalleImpuesto['impuestoReembolso']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo impuestoReembolso del detalleImpuesto ' . ($conImp + 1) . ' debe tener maximo 14 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo impuestoReembolso del detalleImpuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo impuestoReembolso del detalleImpuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarMaquinaFiscal($data, &$cont, $error)
    {
        try {
            $conImp = 0;
            foreach ($data as $maquinaFiscal) {
                //codigo
                if (isset($maquinaFiscal['marca'])) {
                    if ($maquinaFiscal['marca'] != '') {
                        if (strlen($maquinaFiscal['marca']) < 1 || strlen($maquinaFiscal['marca'])  > 100) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo marca de maquinaFiscal ' . ($conImp + 1) . ' debe ser minimo de 1 y maximo de 100 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo marca de maquinaFiscal ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del maquinaFiscal ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //codigoPorcentaje
                if (isset($maquinaFiscal['codigoPorcentaje'])) {
                    if ($maquinaFiscal['codigoPorcentaje'] != '') {
                        $codigoPorcentaje = $maquinaFiscal['codigoPorcentaje'];
                        if (strlen($maquinaFiscal['codigoPorcentaje']) > 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del maquinaFiscal ' . ($conImp + 1) . ' debe ser maximo de 2 caracteres';
                            $cont++;
                        }
                        if (!is_numeric($maquinaFiscal['codigoPorcentaje'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del maquinaFiscal ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del maquinaFiscal ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del maquinaFiscal ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //baseImponible
                if (isset($maquinaFiscal['baseImponible'])) {
                    if ($maquinaFiscal['baseImponible'] != '') {
                        $digitsCod = str_replace(array('.', ','), array('', ''), strrev($maquinaFiscal['baseImponible']));
                        if (!is_numeric($digitsCod)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del maquinaFiscal ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($maquinaFiscal['baseImponible']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del maquinaFiscal ' . ($conImp + 1) . ' debe ser maximo de 14 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del maquinaFiscal ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del maquinaFiscal ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //valor
                if (isset($impuesto['valor'])) {
                    if ($impuesto['valor'] != '') {
                        if (!is_numeric($impuesto['valor'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($impuesto['valor']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del impuesto ' . ($conImp + 1) . ' debe ser maximo de 14 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //validar Informacion impuesto
                if ($codigo != '' && $codigoPorcentaje != '') {
                    $deCodigo = De_Codigo_Impuestos::where('codigo', $codigoPorcentaje)->first();
                    if ($deCodigo != '') {
                        if ($codigoPorcentaje != $deCodigo->codigo) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' no corresponde al codigoRetencion del impuesto';
                            $cont++;
                        }
                        if ($codigo != $deCodigo->codigo_impuesto) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del impuesto ' . ($conImp + 1) . ' no corresponde al codigo del impuesto';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el codigo (' . $codigoPorcentaje . ') del impuesto ' . ($conImp + 1) . ' se encuentra desactualizado o es incorrecto';
                        $cont++;
                    }
                }
                $conImp++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarPagosRetenciones($data, &$cont, $error)
    {
        try {
            $conPago = 0;
            foreach ($data as $pago) {
                //formaPago
                if (isset($pago['formaPago'])) {
                    if ($pago['formaPago'] != '') {
                        if (strlen($pago['formaPago']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' debe contener 2 caracteres (' . $pago['formaPago'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($pago['formaPago'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' debe ser numerico (' . $pago['formaPago'] . ')';
                            $cont++;
                        }
                        if ($pago['formaPago'] != '01' && $pago['formaPago'] != '15' && $pago['formaPago'] != '16' && $pago['formaPago'] != '17' && $pago['formaPago'] != '18' && $pago['formaPago'] != '19' && $pago['formaPago'] != '20' && $pago['formaPago'] != '21') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' solo pueden ser una de siguientes opciones 01 - 15 - 16 - 17 - 18 - 19 - 20 - 21(' . $pago['formaPago'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' es obligatorio';
                    $cont++;
                }
                //total
                if (isset($pago['total'])) {
                    if ($pago['total'] != '') {
                        if (!is_numeric($pago['total'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo total del Pago ' . ($conPago + 1) . ' debe ser numerico (' . $pago['total'] . ')';
                            $cont++;
                        }
                        if (strlen($pago['total']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo total del Pago ' . ($conPago + 1) . ' debe contener hasta 14 digitos maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo total del Pago ' . ($conPago + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo total del Pago ' . ($conPago + 1) . ' es obligatorio';
                    $cont++;
                }
                $conPago++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . ': ' . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarImpuestos($data, $codDoc, &$cont, $error)
    {
        try {
            $conImp = 0;
            foreach ($data as $impuesto) {
                //codigo
                if (isset($impuesto['codigo'])) {
                    if ($impuesto['codigo'] != '') {
                        $codigo = $impuesto['codigo'];
                        if ($impuesto['codigo'] != '1' && $impuesto['codigo'] != '2' && $impuesto['codigo'] != '6') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del impuesto ' . ($conImp + 1) . ' debe ser 1,2 o 6';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //codigoPorcentaje
                if (isset($impuesto['codigoPorcentaje'])) {
                    if ($impuesto['codigoPorcentaje'] != '') {
                        $codigoPorcentaje = $impuesto['codigoPorcentaje'];
                        if (strlen($impuesto['codigoPorcentaje']) > 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' debe ser maximo de 2 caracteres';
                            $cont++;
                        }
                        if (!is_numeric($impuesto['codigoPorcentaje'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                if ($codDoc == '41') {
                    //tarifa
                    if (isset($impuesto['tarifa'])) {
                        if ($impuesto['tarifa'] != '') {
                            if (strlen($impuesto['tarifa']) < 1 || strlen($impuesto['tarifa']) > 4) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa debe tener entre 1 y 4 caracteres';
                                $cont++;
                            }
                            if (!is_numeric($impuesto['tarifa'])) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa debe ser numerico';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa no puede estar vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tarifa es obligatorio';
                        $cont++;
                    }
                }
                //baseImponible
                if (isset($impuesto['baseImponible'])) {
                    if ($impuesto['baseImponible'] != '') {
                        $digitsCod = str_replace(array('.', ','), array('', ''), strrev($impuesto['baseImponible']));
                        if (!is_numeric($digitsCod)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($impuesto['baseImponible']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' debe ser maximo de 14 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //valor
                if (isset($impuesto['valor'])) {
                    if ($impuesto['valor'] != '') {
                        if (!is_numeric($impuesto['valor'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if (strlen($impuesto['valor']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del impuesto ' . ($conImp + 1) . ' debe ser maximo de 14 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //validar Informacion impuesto
                if ($codigo != '' && $codigoPorcentaje != '') {
                    $deCodigo = De_Codigo_Impuestos::where('codigo', $codigoPorcentaje)->first();
                    if ($deCodigo != '') {
                        if ($codigoPorcentaje != $deCodigo->codigo) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' no corresponde al codigoRetencion del impuesto';
                            $cont++;
                        }

                        if ($codigo != $deCodigo->codigo_impuesto) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del impuesto ' . ($conImp + 1) . ' no corresponde al codigo del impuesto';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el codigo (' . $codigoPorcentaje . ') del impuesto ' . ($conImp + 1) . ' se encuentra desactualizado o es incorrecto';
                        $cont++;
                    }
                }
                $conImp++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarDetalles($data, $codDoc, &$cont, $error)
    {
        try {
            $contDeta = 0;
            foreach ($data as $detalle) {
                if ($codDoc == '01' || $codDoc == '03') {
                    //validarCodigoPrincipal
                    if (isset($detalle['codigoPrincipal'])) {
                        if ($detalle['codigoPrincipal'] != '') {
                            if (strlen($detalle['codigoPrincipal']) > 25) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPrincipal del detalle ' . ($contDeta + 1) . ' puede tener maximo 25 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPrincipal del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    }
                    //validarCodigoAuxiliar
                    if (isset($detalle['codigoAuxiliar'])) {
                        if ($detalle['codigoAuxiliar'] != '') {
                            if (strlen($detalle['codigoAuxiliar']) > 25) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoAuxiliar del detalle ' . ($contDeta + 1) . ' puede tener maximo 25 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoAuxiliar del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    }
                } else {
                    //validarCodigoInterno
                    if (isset($detalle['codigoInterno'])) {
                        if ($detalle['codigoInterno'] != '') {
                            if (strlen($detalle['codigoInterno']) > 25) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoInterno del detalle ' . ($contDeta + 1) . ' puede tener maximo 25 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoInterno del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    }
                    //validarCodigoAdicional
                    if (isset($detalle['codigoAdicional'])) {
                        if ($detalle['codigoAdicional'] != '') {
                            if (strlen($detalle['codigoAdicional']) > 25) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoAdicional del detalle ' . ($contDeta + 1) . ' puede tener maximo 25 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoAdicional del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoAdicional del detalle ' . ($contDeta + 1) . ' es obligatorio';
                        $cont++;
                    }
                }
                //validarDescripcion
                if (isset($detalle['descripcion'])) {
                    if ($detalle['descripcion'] != '') {
                        if (strlen($detalle['descripcion']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo descripcion del detalle ' . ($contDeta + 1) . ' puede tener maximo 300 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo descripcion del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo descripcion del detalle ' . ($contDeta + 1) . ' es obligatorio';
                    $cont++;
                }
                //validarCantidad
                if (isset($detalle['cantidad'])) {
                    if ($detalle['cantidad'] != '') {
                        if (!is_numeric($detalle['cantidad'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo cantidad del detalle ' . ($contDeta + 1) . ' solo puede contener numeros';
                            $cont++;
                        }
                        if (strlen($detalle['cantidad']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo cantidad del detalle ' . ($contDeta + 1) . ' puede tener maximo 14 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo cantidad del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo cantidad del detalle ' . ($contDeta + 1) . ' es obligatorio';
                    $cont++;
                }
                if ($codDoc != 06) {
                    //validarPrecioUnitario
                    if (isset($detalle['precioUnitario'])) {
                        if ($detalle['precioUnitario'] != '') {
                            if (!is_numeric($detalle['precioUnitario'])) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo precioUnitario del detalle ' . ($contDeta + 1) . ' solo puede contener numeros';
                                $cont++;
                            }
                            if (strlen($detalle['precioUnitario']) > 14) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo precioUnitario del detalle ' . ($contDeta + 1) . ' puede tener maximo 14 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo precioUnitario del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo precioUnitario del detalle ' . ($contDeta + 1) . ' es obligatorio';
                        $cont++;
                    }
                    //validarDescuento
                    if (isset($detalle['descuento'])) {
                        if ($detalle['descuento'] != '') {
                            if (!is_numeric($detalle['descuento'])) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo descuento del detalle ' . ($contDeta + 1) . ' solo puede contener numeros';
                                $cont++;
                            }
                            if (strlen($detalle['descuento']) > 14) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo descuento del detalle ' . ($contDeta + 1) . ' puede tener maximo 14 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo descuento del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo descuento del detalle ' . ($contDeta + 1) . ' es obligatorio';
                        $cont++;
                    }
                    //validarPrecioTotalSinImpuesto
                    if (isset($detalle['precioTotalSinImpuesto'])) {
                        if ($detalle['precioTotalSinImpuesto'] != '') {
                            if (!is_numeric($detalle['precioTotalSinImpuesto'])) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo precioTotalSinImpuesto del detalle ' . ($contDeta + 1) . ' solo puede contener numeros';
                                $cont++;
                            }
                            if (strlen($detalle['precioTotalSinImpuesto']) > 14) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo precioTotalSinImpuesto del detalle ' . ($contDeta + 1) . ' puede tener maximo 14 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo precioTotalSinImpuesto del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo precioTotalSinImpuesto del detalle ' . ($contDeta + 1) . ' es obligatorio';
                        $cont++;
                    }
                }
                if ($codDoc == '03') {
                    //unidadMedida
                    if (isset($detalle['unidadMedida'])) {
                        if ($detalle['unidadMedida'] != '') {
                            if (strlen($detalle['unidadMedida']) > 50) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo unidadMedida del detalle ' . ($contDeta + 1) . ' puede tener maximo 50 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo unidadMedida del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    }
                    //precioUnitario
                    if (isset($detalle['precioUnitario'])) {
                        if ($detalle['precioUnitario'] != '') {
                            if (!is_numeric($detalle['precioUnitario'])) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo precioUnitario del detalle ' . ($contDeta + 1) . ' solo puede contener numeros';
                                $cont++;
                            }
                            if (strlen($detalle['precioUnitario']) > 14) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo precioUnitario del detalle ' . ($contDeta + 1) . ' puede tener maximo 14 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo precioUnitario del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo precioUnitario del detalle ' . ($contDeta + 1) . ' es obligatorio';
                        $cont++;
                    }
                    //precioTotalSinImpuesto
                    if (isset($detalle['precioTotalSinImpuesto'])) {
                        if ($detalle['precioTotalSinImpuesto'] != '') {
                            if (!is_numeric($detalle['precioTotalSinImpuesto'])) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo precioTotalSinImpuesto del detalle ' . ($contDeta + 1) . ' solo puede contener numeros';
                                $cont++;
                            }
                            if (strlen($detalle['precioTotalSinImpuesto']) > 14) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo precioTotalSinImpuesto del detalle ' . ($contDeta + 1) . ' puede tener maximo 14 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo precioTotalSinImpuesto del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo precioTotalSinImpuesto del detalle ' . ($contDeta + 1) . ' es obligatorio';
                        $cont++;
                    }
                    //validarDetallesAdicionales
                    if (isset($detalle['detallesAdicionales'])) {
                        if (count($detalle['detallesAdicionales']) > 0) {
                            $error = $this->validarDetAdicionales($detalle['detallesAdicionales'], $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo detallesAdicionales del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    }
                    //validarImpuestos
                    if (isset($detalle['impuestos'])) {
                        if (count($detalle['impuestos']) > 0) {
                            $error = $this->validarImpuestos($detalle['impuestos'], $codDoc, $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo impuestos del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo impuestos del detalle ' . ($contDeta + 1) . ' es obligatorio';
                        $cont++;
                    }
                }
                $contDeta++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    public function validarDetAdicionales($data, &$cont, $error)
    {
        try {
            $contAdi = 0;
            foreach ($data as $detAdicional) {
                if (isset($detAdicional['nombre'])) {
                    if ($detAdicional['nombre'] != '') {
                        if (strlen($detAdicional['nombre']) > 100) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el detAdicional nombre ' . ($contAdi + 1) . ' debe tener maximo 100 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el detAdicional nombre ' . ($contAdi + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                    if ($detAdicional['valor'] != '') {
                        if (strlen($detAdicional['valor']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el detAdicional valor ' . ($contAdi + 1) . ' debe tener maximo 300 caracteres (' . strlen($detAdicional['valor']) . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el detAdicional valor ' . ($contAdi + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                $contAdi++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    public function validarInfoAdicional($data, &$cont, $error)
    {
        try {
            $contAdi = 0;
            foreach ($data as $campoAdicional) {
                if (isset($campoAdicional['nombre'])) {
                    if ($campoAdicional['nombre'] != '') {
                        if (strlen($campoAdicional['nombre']) > 100) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campoAdicional nombre ' . ($contAdi + 1) . ' debe tener maximo 100 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campoAdicional nombre ' . ($contAdi + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                    if ($campoAdicional['valor'] != '') {
                        if (strlen($campoAdicional['valor']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campoAdicional valor ' . ($contAdi + 1) . ' debe tener maximo 300 caracteres';
                            $cont++;
                        }
                    }
                }
                $contAdi++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function getDate($valor)
    {
        if (trim($valor) == "") return "";
        $result = date('d/m/Y', strtotime($valor));
        return $result;
    }
    public function getDatePeriodoFiscal($valor)
    {
        if (trim($valor) == "") return "";
        $result = date('m/Y', strtotime($valor));
        return $result;
    }
    private function validarFechaFinal($fechainicial, $fechafinal)
    {
        $fechainicial = date('Y-m-d', strtotime($fechainicial));
        $fechafinal = date('Y-m-d', strtotime($fechafinal));
        $respuesta = false;
        $fecha_actual = strtotime(date("d-m-Y"));
        $fecha_entrada = strtotime($fechafinal);
        if ($fecha_entrada >= $fecha_actual)
            $respuesta = true;
        return $respuesta;
    }
    private function validarFechaInicio($fecha)
    {
        $respuesta = false;
        if ($this->validarFecha($fecha)) {
            try {
                $date = date('Y-m-d', strtotime($fecha));
                if ($date != '') {
                    if ($date >= date('Y-m-d'))
                        $respuesta = true;
                } else
                    $respuesta = false;
            } catch (Exception $e) {
                $respuesta = false;
            }
        } else {
            $respuesta = false;
        }
        return $respuesta;
    }
    private function validarFechaCorrecta($fecha)
    {
        $respuesta = true;
        $fecha = str_replace('-', '/', $fecha);
        if ($fecha == '31/12/1969') {
            $respuesta = false;
        }
        return $respuesta;
    }
    private function validarFecha($fecha)
    {
        $respuesta = true;
        try {
            //$fecha = str_replace('-', '/', $fecha);
            $fecha = date('Y-m-d', strtotime($fecha));
            $valores = explode('-', $fecha);
            if (count($valores) != 3 && strlen($valores[0]) != 4 && strlen($valores[1]) != 2 && strlen($valores[2]) != 2) {
                $respuesta = false;
            }
        } catch (Exception $e) {
            $respuesta = false;
        }
        return $respuesta;
    }
    private function getInfoTributaria($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('infoTributaria');
        $nodoDetalle->appendChild($xmlDocument->createElement('ambiente', $campos['infoTributaria']['ambiente']));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoEmision', $campos['infoTributaria']['tipoEmision']));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocial', $campos['infoTributaria']["razonSocial"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('nombreComercial', str_replace('&', 'Y', $campos['infoTributaria']["nombreComercial"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('ruc', $campos['infoTributaria']["ruc"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('claveAcceso', $campos['infoTributaria']["claveAcceso"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('codDoc', str_pad($campos['infoTributaria']["codDoc"], 2, 0, STR_PAD_LEFT)));
        $nodoDetalle->appendChild($xmlDocument->createElement('estab', str_pad($campos['infoTributaria']["estab"], 3, 0, STR_PAD_LEFT)));
        $nodoDetalle->appendChild($xmlDocument->createElement('ptoEmi', str_pad($campos['infoTributaria']["ptoEmi"], 3, 0, STR_PAD_LEFT)));
        $nodoDetalle->appendChild($xmlDocument->createElement('secuencial', str_pad($campos['infoTributaria']["secuencial"], 9, 0, STR_PAD_LEFT)));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirMatriz', $campos['infoTributaria']["dirMatriz"]));
        if (isset($campos['infoTributaria']['agenteRetencion']))
            $nodoDetalle->appendChild($xmlDocument->createElement('agenteRetencion', "1"));
        elseif (isset($campos['infoTributaria']['rimpe_emprendedor']))
            $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteRimpe', "CONTRIBUYENTE RÉGIMEN RIMPE"));
        elseif (isset($campos['infoTributaria']['rimpe_popular']))
            $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteRimpe', "CONTRIBUYENTE RÉGIMEN RIMPE"));
        return $nodoDetalle;
    }
    private  function getInfoGuiaRemision($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('infoGuiaRemision');
        //$nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $campos["fecha_emision"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoGuiaRemision']["dirEstablecimiento"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirPartida', $campos['infoGuiaRemision']["dirPartida"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialTransportista', $campos['infoGuiaRemision']["razonSocialTransportista"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionTransportista', $campos['infoGuiaRemision']['tipoIdentificacionTransportista']));
        $nodoDetalle->appendChild($xmlDocument->createElement('rucTransportista', $campos['infoGuiaRemision']["rucTransportista"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos['infoGuiaRemision']["obligadoContabilidad"]));
        if ($campos['infoGuiaRemision']["contribuyenteEspecial"]) $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos['infoGuiaRemision']["contribuyenteEspecial"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaIniTransporte', $this->getDate($campos['infoGuiaRemision']["fechaIniTransporte"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaFinTransporte', $this->getDate($campos['infoGuiaRemision']["fechaFinTransporte"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('placa', $campos['infoGuiaRemision']["placa"]));
        return $nodoDetalle;
    }
    private  function getDetalles($xmlDocument, $campos)
    {
        $codDoc = $campos['infoTributaria']['codDoc'];
        $nodoDetalle = $xmlDocument->createElement('detalles');
        $detalles = $campos["detalles"];
        foreach ($detalles as $detalle) {
            $precioTotalSinImpuesto = $detalle["precioTotalSinImpuesto"];
            $item = $xmlDocument->createElement('detalle');
            if ($codDoc == '01' || $codDoc == '03') {
                $item->appendChild($xmlDocument->createElement('codigoPrincipal', $detalle["codigoPrincipal"]));
                $item->appendChild($xmlDocument->createElement('codigoAuxiliar', $detalle["codigoAuxiliar"]));
            } elseif ($codDoc == '04' || $codDoc == '06') {
                $item->appendChild($xmlDocument->createElement('codigoInterno', $detalle["codigoInterno"]));
                $item->appendChild($xmlDocument->createElement('codigoAdicional', $detalle["codigoAdicional"]));
            }
            $item->appendChild($xmlDocument->createElement('descripcion', str_replace('&', 'y', $detalle["descripcion"])));
            if (isset($detalle["unidadMedida"])) $item->appendChild($xmlDocument->createElement('unidadMedida', $detalle["unidadMedida"]));
            $item->appendChild($xmlDocument->createElement('cantidad', $this->getDecimal($detalle["cantidad"], 2)));
            $item->appendChild($xmlDocument->createElement('precioUnitario', $this->getDecimal($detalle["precioUnitario"], 2)));
            if (isset($detalle["descuento"])) $item->appendChild($xmlDocument->createElement('descuento', $this->getDecimal($detalle["descuento"], 2)));
            $item->appendChild($xmlDocument->createElement('precioTotalSinImpuesto', $this->getDecimal($precioTotalSinImpuesto, 2)));
            if (count($detalle["detallesAdicionales"]) > 0) {
                $detallesAdicionales = $this->getDetallesAdicionales($xmlDocument, $detalle["detallesAdicionales"]);
                $item->appendChild($detallesAdicionales);
            }
            $impuestos = $this->getImpuestos($xmlDocument, $detalle);
            $item->appendChild($impuestos);
            $nodoDetalle->appendChild($item);
        }
        return $nodoDetalle;
    }
    private  function getImpuestos($xmlDocument, $detalle)
    {
        $nodoDetalle = $xmlDocument->createElement('impuestos');
        //foreach ($detalles as $detalle) {
        $item = $xmlDocument->createElement('impuesto');
        $codigoImpuesto = De_Codigo_Impuestos::where('porcentaje', ($this->porIva->iva * 100))->where('codigo_impuesto', 2)->first();
        if ($detalle['p_impuesto'] == 12)
            $sri_tipo_impuesto_iva_id = 1;
        elseif ($detalle['p_impuesto'] == 15)
            $sri_tipo_impuesto_iva_id = $codigoImpuesto->id;
        else
            $sri_tipo_impuesto_iva_id = 2;
        $impuestoIva = De_Codigo_Impuestos::where('id', $sri_tipo_impuesto_iva_id)->first();
        $baseImponible = $detalle["precioTotalSinImpuesto"];
        $impuesto = ($baseImponible * $impuestoIva->porcentaje) / 100;
        $item->appendChild($xmlDocument->createElement('codigo', $impuestoIva->codigo_impuesto));
        $item->appendChild($xmlDocument->createElement('codigoPorcentaje', $impuestoIva->codigo));
        $item->appendChild($xmlDocument->createElement('tarifa', $impuestoIva->porcentaje));
        $item->appendChild($xmlDocument->createElement('baseImponible', $this->getDecimal($baseImponible)));
        $item->appendChild($xmlDocument->createElement('valor', $this->getDecimal($impuesto)));
        $nodoDetalle->appendChild($item);
        //}
        return $nodoDetalle;
    }
    private  function getDestinatarios($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('destinatarios');
        $destinatarios = $campos["destinatarios"];
        foreach ($destinatarios as $destinatario) {
            $email = '';
            $item = $xmlDocument->createElement('destinatario');
            $item->appendChild($xmlDocument->createElement('identificacionDestinatario', $destinatario["identificacionDestinatario"]));
            $item->appendChild($xmlDocument->createElement('razonSocialDestinatario', $destinatario["razonSocialDestinatario"]));
            $item->appendChild($xmlDocument->createElement('dirDestinatario', $destinatario["dirDestinatario"]));
            $item->appendChild($xmlDocument->createElement('motivoTraslado', $destinatario["motivoTraslado"]));
            $item->appendChild($xmlDocument->createElement('codEstabDestino', str_pad($destinatario["codEstabDestino"], 3, 0, STR_PAD_LEFT)));
            $item->appendChild($xmlDocument->createElement('ruta', $destinatario["ruta"]));
            if (isset($destinatario["codDocSustento"])) {
                $item->appendChild($xmlDocument->createElement('codDocSustento', str_pad($destinatario["codDocSustento"], 2, 0, STR_PAD_LEFT)));
                if (isset($destinatario["numDocSustento"])) {
                    if ($destinatario["numDocSustento"] != '') {
                        $item->appendChild($xmlDocument->createElement('numDocSustento', $destinatario["numDocSustento"]));
                        if ($destinatario["numAutDocSustento"] != '0')
                            $item->appendChild($xmlDocument->createElement('numAutDocSustento', $destinatario["numAutDocSustento"]));
                        else
                            $item->appendChild($xmlDocument->createElement('numAutDocSustento', '9999999999'));
                        if ($email != "") array_push($destinatarios, array("nombre" => 'Email', "valor" => strtoupper($email)));
                    }
                }
                $item->appendChild($xmlDocument->createElement('fechaEmisionDocSustento', $destinatario["fechaEmisionDocSustento"]));
            }
            $nodoDetalleMercancia = $xmlDocument->createElement('detalles');
            $detalles = $destinatario['detalles'];
            foreach ($detalles as $detalle) {
                $itemMercancia = $xmlDocument->createElement('detalle');
                $itemMercancia->appendChild($xmlDocument->createElement('codigoInterno', $detalle['codigoInterno']));
                $itemMercancia->appendChild($xmlDocument->createElement('codigoAdicional',  $detalle['codigoAdicional']));
                $itemMercancia->appendChild($xmlDocument->createElement('descripcion', $detalle['descripcion']));
                $itemMercancia->appendChild($xmlDocument->createElement('cantidad', $detalle['cantidad']));
                $nodoDetalleMercancia->appendChild($itemMercancia);
                if (count($detalle["detallesAdicionales"]) > 0) {
                    $detallesAdicionales = $this->getDetallesAdicionales($xmlDocument, $detalle["detallesAdicionales"]);
                    $itemMercancia->appendChild($detallesAdicionales);
                }
                $item->appendChild($nodoDetalleMercancia);
                $nodoDetalle->appendChild($item);
            }
        }
        return $nodoDetalle;
    }
    private  function getDetallesAdicionales($xmlDocument, $detalles)
    {
        $nodoDetalle = $xmlDocument->createElement('detallesAdicionales');
        $count = 0;
        foreach ($detalles as $detalle) {
            if (count($detalle) > 0) {
                $count++;
                if ($count > 3) break;
                $item = $xmlDocument->createElement('detAdicional');
                $item->setAttribute('nombre', $detalle['nombre']);
                $item->setAttribute('valor', $detalle['valor']);
                $nodoDetalle->appendChild($item);
            }
        }
        return $nodoDetalle;
    }
    private  function getInfoAdicional($xmlDocument, $campos)
    {
        $detalles = array();
        $valor = '';
        $cantidadInfoAdicional = 0;
        $informacionAdicional = $campos['infoAdicional'];
        $nodoDetalle = $xmlDocument->createElement('infoAdicional');
        if (count($informacionAdicional) > 0) {
            $cantidadInfoAdicional = count($informacionAdicional);
        }
        for ($i = 0; $i < $cantidadInfoAdicional; $i++) {
            if (isset($informacionAdicional[$i]['nombre']))
                $nombre = $informacionAdicional[$i]['nombre'];
            if (isset($informacionAdicional[$i]['valor']))
                $valor = $informacionAdicional[$i]['valor'];
            if (strcasecmp(trim($nombre), 'RUC Proveedor') !== 0) {
                if ($campos['infoTributaria']['codDoc'] == '06')
                    array_push($detalles, array("nombre" => $nombre, "valor" => str_replace(' ', '', str_replace('(', '', str_replace(')', '', str_replace('-', '', $valor))))));
                else
                    array_push($detalles, array("nombre" => $nombre, "valor" => $valor));
            }
        }
        array_push($detalles, array(
            "nombre" => 'RUC Proveedor',
            "valor" => '0993094072001'
        ));
        foreach ($detalles as $item) {
            if ($item["nombre"] != '') {
                $campoAdicional = $xmlDocument->createElement('campoAdicional', isset($item["valor"]) ? str_replace("'", "", str_replace('&', '&amp;', $item["valor"])) : 'NN');
                $campoAdicional->setAttribute('nombre', $item["nombre"]);
                $nodoDetalle->appendChild($campoAdicional);
            }
        }
        return $nodoDetalle;
    }
    private  function getDecimal($valor = 0, $digitos = 2, $formato = false)
    {
        if (trim($valor) == "") $valor = 0;
        $resultado = round($valor, $digitos, PHP_ROUND_HALF_UP);
        $resultado = number_format($resultado, $digitos, ".", $formato ? "," : "");
        return $resultado;
    }
    public function validarDocumentoGestion($tipo, $id)
    {
        try {
            if ($tipo === 'Factura') {
                $resultado = $this->getFactura(null, 'VALIDACION_XSD', ['id' => $id]);
            } elseif ($tipo === 'Retención') {
                $resultado = $this->getRetencion(null, 'VALIDACION_XSD', ['id' => $id]);
            } elseif ($tipo === 'Liquidación de compra') {
                $resultado = $this->getLiquidacion(null, 'VALIDACION_XSD', ['id' => $id]);
            } elseif ($tipo === 'Nota de crédito') {
                $resultado = $this->getNotaCredito(null, 'VALIDACION_XSD', ['id' => $id]);
            } else {
                $resultado = $this->getGuiaRemision(null, 'VALIDACION_XSD', ['id' => $id]);
            }

            if (!is_array($resultado) || !isset($resultado['data'], $resultado['errors'])) {
                return [
                    'valido' => false,
                    'etapa' => 'generacion',
                    'errores' => ['No fue posible reconstruir la información actual del documento.'],
                ];
            }
            if (count($resultado['errors']) > 0) {
                return [
                    'valido' => false,
                    'etapa' => 'datos',
                    'errores' => array_values($resultado['errors']),
                ];
            }

            $xml = $this->generarDocElectronicoXml($resultado['data'], false);
            if (empty($xml)) {
                return [
                    'valido' => false,
                    'etapa' => 'generacion',
                    'errores' => ['La información pasó las validaciones básicas, pero no se pudo construir el XML.'],
                ];
            }

            $validacionXsd = $this->validarXmlToXsd($xml);
            if (!is_array($validacionXsd)) {
                return [
                    'valido' => false,
                    'etapa' => 'xsd',
                    'errores' => [(string) $validacionXsd],
                ];
            }

            return [
                'valido' => !empty($validacionXsd['isValidoXsd']),
                'etapa' => 'xsd',
                'errores' => isset($validacionXsd['mensajes']) ? array_values($validacionXsd['mensajes']) : [],
            ];
        } catch (\Throwable $e) {
            return [
                'valido' => false,
                'etapa' => 'generacion',
                'errores' => ['No fue posible reconstruir el XML: ' . $e->getMessage()],
            ];
        }
    }

    private function generarDocElectronicoXml($campos, $guardar = true)
    {
        $comprobante = null;
        if ($campos['infoTributaria']['codDoc'] == 01)
            $this->tipoDocumento = 'factura';
        elseif ($campos['infoTributaria']['codDoc'] == 03)
            $this->tipoDocumento = 'liquidacionCompra';
        elseif ($campos['infoTributaria']['codDoc'] == 04)
            $this->tipoDocumento = 'notaCredito';
        elseif ($campos['infoTributaria']['codDoc'] == 05)
            $this->tipoDocumento = 'notaDebito';
        elseif ($campos['infoTributaria']['codDoc'] == 06)
            $this->tipoDocumento = 'guiaRemision';
        elseif ($campos['infoTributaria']['codDoc'] == 07)
            $this->tipoDocumento = 'comprobanteRetencion';
        $xml = new \DOMDocument('1.0', 'UTF-8');
        if ($this->tipoDocumento == 'guiaRemision') {
            $root = $xml->createElement($this->tipoDocumento);
            $root->setAttribute('id', 'comprobante');
            $root->setAttribute('version', '1.1.0');
            $infoTributaria = $this->getInfoTributaria($xml, $campos);
            $root->appendChild($infoTributaria);
            $infoGuiaRemision = $this->getInfoGuiaRemision($xml, $campos);
            $root->appendChild($infoGuiaRemision);
            $destinatarios = $this->getDestinatarios($xml, $campos);
            $root->appendChild($destinatarios);
            $infoAdicional = $this->getInfoAdicional($xml, $campos);
            $root->appendChild($infoAdicional);
        } else if ($this->tipoDocumento == 'factura') {
            $root = $xml->createElement($this->tipoDocumento);
            $root->setAttribute('id', 'comprobante');
            $root->setAttribute('version', '2.1.0');
            $infoTributaria = $this->getInfoTributaria($xml, $campos);
            $root->appendChild($infoTributaria);
            $infoFactura = $this->getInfoFactura($xml, $campos);
            $root->appendChild($infoFactura);
            $detalles = $this->getDetalles($xml, $campos);
            $root->appendChild($detalles);
            if (isset($campos["retenciones"]) && count($campos["retenciones"]) > 0) {
                $retenciones = $this->getRetencionesFactura($xml, $campos);
                $root->appendChild($retenciones);
            }
            $infoAdicional = $this->getInfoAdicional($xml, $campos);
            $root->appendChild($infoAdicional);
        } else if ($this->tipoDocumento == 'notaCredito') {
            $root = $xml->createElement($this->tipoDocumento);
            $root->setAttribute('id', 'comprobante');
            $root->setAttribute('version', '1.1.0');
            $infoTributaria = $this->getInfoTributaria($xml, $campos);
            $root->appendChild($infoTributaria);
            $infoNotaCredito = $this->getInfoNotaCredito($xml, $campos);
            $root->appendChild($infoNotaCredito);
            $detalles = $this->getDetalles($xml, $campos);
            $root->appendChild($detalles);
            $infoAdicional = $this->getInfoAdicional($xml, $campos);
            $root->appendChild($infoAdicional);
        } else if ($this->tipoDocumento == 'notaDebito') {
            $root = $xml->createElement($this->tipoDocumento);
            $root->setAttribute('id', 'comprobante');
            $root->setAttribute('version', '1.1.0');
            $infoTributaria = $this->getInfoTributaria($xml, $campos);
            $root->appendChild($infoTributaria);
            $infoNotaDebito = $this->getInfoNotaDebito($xml, $campos);
            $root->appendChild($infoNotaDebito);
            $motivos = $this->getMotivos($xml, $campos);
            $root->appendChild($motivos);
            $infoAdicional = $this->getInfoAdicional($xml, $campos);
            $root->appendChild($infoAdicional);
        } else if ($this->tipoDocumento == 'comprobanteRetencion') {
            $root = $xml->createElement($this->tipoDocumento);
            $root->setAttribute('id', 'comprobante');
            $root->setAttribute('version', '2.0.0');
            $infoTributaria = $this->getInfoTributaria($xml, $campos);
            $root->appendChild($infoTributaria);
            $infoCompRetencion = $this->getInfoCompRetencion($xml, $campos);
            $root->appendChild($infoCompRetencion);
            $docsSustento = $this->getDocsSustento($xml, $campos);
            $root->appendChild($docsSustento);
            $infoAdicional = $this->getInfoAdicional($xml, $campos);
            $root->appendChild($infoAdicional);
        } else if ($this->tipoDocumento == 'liquidacionCompra') {
            $root = $xml->createElement($this->tipoDocumento);
            $root->setAttribute('id', 'comprobante');
            $root->setAttribute('version', '1.1.0');
            $infoTributaria = $this->getInfoTributaria($xml, $campos);
            $root->appendChild($infoTributaria);
            $infoLiquidacionCompra = $this->getInfoLiquidacionCompra($xml, $campos);
            $root->appendChild($infoLiquidacionCompra);
            $detalles = $this->getDetalles($xml, $campos);
            $root->appendChild($detalles);
            if ($campos['infoTributaria']['codDoc'] == 41) {
                $reembolsos = $this->getReembolsos($xml, $campos);
                $root->appendChild($reembolsos);
            }
            if (isset($campos['maquinaFiscal'])) {
                $maquinaFiscal = $this->getMaquinaFiscal($xml, $campos);
                $root->appendChild($maquinaFiscal);
            }
            $infoAdicional = $this->getInfoAdicional($xml, $campos);
            $root->appendChild($infoAdicional);
        } else {
        }
        if ($this->tipoDocumento != '') {
            //Guarda XML
            $xml->appendChild($root);
            $xml->formatOutput = true;
            $comprobante = $xml->saveXML();
            //print_r($comprobante);exit;
            if ($guardar) {
                $r_ = base_path() . '/storage/app/facturaelectronica/sinfirmar/' . $campos['infoTributaria']['ruc'] . '/' . $this->tipoDocumento . '/';
                $this->crearcarpeta($r_);
                $ruta = $r_ . $this->tipoDocumento . '-' . $campos['infoTributaria']['estab'] . '-' .  $campos['infoTributaria']['ptoEmi'] . '-' . str_pad($campos['infoTributaria']['secuencial'], 9, 0, STR_PAD_LEFT) . '.xml';
                $xml->save($ruta);
            }
        }
        return $comprobante;
    }
    public function getDocsSustento($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('docsSustento');
        $docsSustento = $campos["docsSustento"];
        foreach ($docsSustento as $docSustento) {
            $item = $xmlDocument->createElement('docSustento');
            $item->appendChild($xmlDocument->createElement('codSustento', $docSustento['codSustento']));
            $item->appendChild($xmlDocument->createElement('codDocSustento', $docSustento['codDocSustento']));
            if (isset($docSustento['numDocSustento'])) {
                if ($docSustento['numDocSustento'] != '') {
                    $item->appendChild($xmlDocument->createElement('numDocSustento', $docSustento['numDocSustento']));
                }
            }
            $item->appendChild($xmlDocument->createElement('fechaEmisionDocSustento', $this->getDate($docSustento['fechaEmisionDocSustento'])));
            if (isset($docSustento['fechaRegistroContable'])) {
                if ($docSustento['fechaRegistroContable'] != '') {
                    $item->appendChild($xmlDocument->createElement('fechaRegistroContable', $docSustento['fechaRegistroContable']));
                }
            }
            if (isset($docSustento['numAutDocSustento'])) {
                if ($docSustento['numAutDocSustento'] != '') {
                    $item->appendChild($xmlDocument->createElement('numAutDocSustento', $docSustento['numAutDocSustento']));
                }
            }
            $item->appendChild($xmlDocument->createElement('pagoLocExt', $docSustento['pagoLocExt']));
            if ($docSustento['pagoLocExt'] == '02') {
                $item->appendChild($xmlDocument->createElement('tipoRegi', $docSustento['tipoRegi']));
                $item->appendChild($xmlDocument->createElement('paisEfecPago', $docSustento['paisEfecPago']));
                $item->appendChild($xmlDocument->createElement('aplicConvDobTrib', $docSustento['aplicConvDobTrib']));
                if ($docSustento['aplicConvDobTrib'] == 'NO') {
                    $item->appendChild($xmlDocument->createElement('pagExtSujRetNorLeg', $docSustento['pagExtSujRetNorLeg']));
                }
                $item->appendChild($xmlDocument->createElement('pagoRegFis', $docSustento['pagoRegFis']));
                if ($docSustento['codDocSustento'] == '41') {
                    $item->appendChild($xmlDocument->createElement('totalComprobantesReembolso', $this->getDecimal($docSustento['totalComprobantesReembolso'])));
                    $item->appendChild($xmlDocument->createElement('totalBaseImponibleReembolso', $this->getDecimal($docSustento['totalBaseImponibleReembolso'])));
                    $item->appendChild($xmlDocument->createElement('totalImpuestoReembolso', $this->getDecimal($docSustento['totalImpuestoReembolso'])));
                }
            }
            $item->appendChild($xmlDocument->createElement('totalSinImpuestos', $this->getDecimal($docSustento['totalSinImpuestos'])));
            $item->appendChild($xmlDocument->createElement('importeTotal', $this->getDecimal($docSustento['importeTotal'])));
            $impuestosDocSustento = $docSustento['impuestosDocSustento'];
            $item->appendChild($this->getImpuestosDocSustento($xmlDocument, $impuestosDocSustento));
            if (isset($docSustento['retenciones'])) {
                $retenciones = $docSustento['retenciones'];
                $item->appendChild($this->getRetenciones($xmlDocument, $retenciones, $docSustento['codDocSustento']));
            }
            $pagos = $docSustento['pagos'];
            $item->appendChild($this->getPagos($xmlDocument, $pagos));
            $nodoDetalle->appendChild($item);
        }
        return $nodoDetalle;
    }
    public function getImpuestosDocSustento($xmlDocument, $impuestos) //Retenciones
    {
        $nodoImpuestosDocSustento = $xmlDocument->createElement('impuestosDocSustento');
        foreach ($impuestos as $impuesto) {
            $impuestoDocSustento = $xmlDocument->createElement('impuestoDocSustento');
            $impuestoDocSustento->appendChild($xmlDocument->createElement('codImpuestoDocSustento', $impuesto["codImpuestoDocSustento"]));
            $impuestoDocSustento->appendChild($xmlDocument->createElement('codigoPorcentaje', $impuesto["codigoPorcentaje"]));
            $impuestoDocSustento->appendChild($xmlDocument->createElement('baseImponible', $this->getDecimal($impuesto["baseImponible"])));
            $impuestoDocSustento->appendChild($xmlDocument->createElement('tarifa', $impuesto["tarifa"]));
            $impuestoDocSustento->appendChild($xmlDocument->createElement('valorImpuesto', $this->getDecimal($impuesto["valorImpuesto"], '2')));
            $nodoImpuestosDocSustento->appendChild($impuestoDocSustento);
        }
        return $nodoImpuestosDocSustento;
    }
    public function getRetenciones($xmlDocument, $retenciones, $codSustento) //Retenciones
    {
        $nodoRetenciones = $xmlDocument->createElement('retenciones');
        foreach ($retenciones as $retencion) {
            $nodoRetencion = $xmlDocument->createElement('retencion');
            $nodoRetencion->appendChild($xmlDocument->createElement('codigo', $retencion["codigo"]));
            $nodoRetencion->appendChild($xmlDocument->createElement('codigoRetencion', $retencion["codigoRetencion"]));
            $nodoRetencion->appendChild($xmlDocument->createElement('baseImponible', $this->getDecimal($retencion["baseImponible"])));
            $nodoRetencion->appendChild($xmlDocument->createElement('porcentajeRetener', $retencion["porcentajeRetener"]));
            $nodoRetencion->appendChild($xmlDocument->createElement('valorRetenido', $this->getDecimal($retencion["valorRetenido"], '2')));
            if ($codSustento == '10') {
                $dividendos = $retenciones['dividendos'];
                $nodoRetencion->appendChild('dividendos', $this->getDividendos($xmlDocument, $dividendos));
            }
            if (isset($retenciones['compraCajBanano'])) {
                if ($retenciones['compraCajBanano'] != '') {
                    $nodoRetencion->appendChild('compraCajBanano', $this->getCompraCajBanano($xmlDocument, $retenciones['compraCajBanano']));
                }
            }
            $nodoRetenciones->appendChild($nodoRetencion);
        }
        return $nodoRetenciones;
    }
    public function getDividendos($xmlDocument, $dividendos)
    {
        $nodoDividendos = $xmlDocument->createElement('dividendos');
        $nodoDividendos->appendChild($xmlDocument->createElement('fechaPagoDiv', $this->getDate($dividendos['fechaPagoDiv'])));
        $nodoDividendos->appendChild($xmlDocument->createElement('imRentaSoc', $this->getDecimal($dividendos['imRentaSoc'])));
        $nodoDividendos->appendChild($xmlDocument->createElement('ejerFisUtDiv', $dividendos['ejerFisUtDiv']));
        return $nodoDividendos;
    }
    public function getCompraCajBanano($xmlDocument, $compraCajBanano)
    {
        $nodoCompraCajBanano = $xmlDocument->createElement('compraCajBanano');
        $nodoCompraCajBanano->appendChild($xmlDocument->createElement('NumCajBan', $compraCajBanano['NumCajBan']));
        $nodoCompraCajBanano->appendChild($xmlDocument->createElement('PrecCajBan', $this->getDecimal($compraCajBanano['PrecCajBan'])));
        return $nodoCompraCajBanano;
    }
    public function getImpuestosRetencion($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('impuestos');
        $impuestos = $campos["impuestos"];
        foreach ($impuestos as $impuesto) {
            $item = $xmlDocument->createElement('impuesto');
            $item->appendChild($xmlDocument->createElement('codigo', $impuesto['codigo']));
            $item->appendChild($xmlDocument->createElement('codigoRetencion', $impuesto['codigoRetencion']));
            $item->appendChild($xmlDocument->createElement('baseImponible', $this->getDecimal($impuesto['baseImponible'])));
            $item->appendChild($xmlDocument->createElement('porcentajeRetener', $impuesto['porcentajeRetener']));
            $item->appendChild($xmlDocument->createElement('valorRetenido', $this->getDecimal($impuesto['valorRetenido'])));
            $item->appendChild($xmlDocument->createElement('codDocSustento', $impuesto['codDocSustento']));
            if (isset($impuesto['numDocSustento'])) {
                if ($impuesto['numDocSustento'] != '') {
                    $item->appendChild($xmlDocument->createElement('numDocSustento', $impuesto['numDocSustento']));
                }
            }
            if (isset($impuesto['fechaEmisionDocSustento'])) $item->appendChild($xmlDocument->createElement('fechaEmisionDocSustento', $this->getDate($impuesto['fechaEmisionDocSustento'])));
            $nodoDetalle->appendChild($item);
        }
        return $nodoDetalle;
    }
    public static function crearcarpeta($ruta)
    {
        if (!file_exists($ruta)) {
            mkdir($ruta, 777, true);
        }
    }
    public function validarXmlToXsd($dataDoc)
    {
        ini_set('max_execution_time', 300);
        $result = array();
        $isValidoXsd = false;
        $mensajes = array();
        $xsd = "";
        try {
            $xsd = "";
            switch ($this->tipoDocumento) {
                case 'factura': //'1.1.0'
                    $xsd =  base_path() . '/storage/app/facturaelectronica/SriXsd/factura_V2.1.0.xsd';
                    break;
                case 'comprobanteRetencion': //1.0.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/ComprobanteRetencion_V2.0.0.xsd';
                    break;
                case 'notaCredito': //1.1.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/NotaCredito_V1.1.0.xsd';
                    break;
                case 'notaDebito': //1.0.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/.xsd';
                    break;
                case 'guiaRemision': //1.1.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/GuiaRemision_V1.1.0.xsd';
                    break;
                case 'liquidacionCompra': //1.1.0, con compatibilidad para instalaciones anteriores
                    $xsdLiquidacion = [
                        base_path() . '/storage/app/facturaelectronica/SriXsd/LiquidacionCompra_V1.1.0.xsd',
                        base_path() . '/storage/app/facturaelectronica/SriXsd/liquidacionCompra_V1.1.0.xsd',
                        base_path() . '/storage/app/facturaelectronica/SriXsd/LiquidacionCompra_V1.0.0.xsd',
                    ];
                    $xsd = $xsdLiquidacion[0];
                    foreach ($xsdLiquidacion as $archivoXsd) {
                        if (is_file($archivoXsd)) {
                            $xsd = $archivoXsd;
                            break;
                        }
                    }
                    break;
            }
            if ($xsd === '' || !is_file($xsd)) {
                return [
                    'isValidoXsd' => false,
                    'mensajes' => ['No se encuentra configurado el archivo XSD para ' . $this->tipoDocumento . '.'],
                ];
            }
            $doc = new DOMDocument();
            $doc->loadXML($dataDoc);
            libxml_clear_errors();
            libxml_use_internal_errors(true);
            $isValidoXsd = $doc->schemaValidate($xsd);
            $errors = libxml_get_errors();
            if ($errors) {
                foreach ($errors as $error) {
                    $ubicacion = 'Línea ' . (int) $error->line;
                    if ((int) $error->column > 0) {
                        $ubicacion .= ', columna ' . (int) $error->column;
                    }
                    array_push($mensajes, $ubicacion . ' - (' . $error->code . ') ' . trim($error->message));
                }
            }
            $result["isValidoXsd"] = $isValidoXsd;
            $result["mensajes"] = $mensajes;
            return $result;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
    public function getPagos($xmlDocument, $pagos)
    {
        $nodePagos = $xmlDocument->createElement('pagos');
        foreach ($pagos as $pago) {
            $item = $xmlDocument->createElement('pago');
            $item->appendChild($xmlDocument->createElement('formaPago', $pago["formaPago"]));
            $item->appendChild($xmlDocument->createElement('total', $this->getDecimal($pago['total'])));
            $nodePagos->appendChild($item);
        }
        return $nodePagos;
    }
    public function mostrarError($errors): string
    {
        $msg = '';
        if ($errors == NULL) {
            return '';
        }
        foreach ($errors as $error) {
            switch ($error->level) {
                case LIBXML_ERR_WARNING:
                    $nivel = 'Warning';
                    break;
                case LIBXML_ERR_ERROR:
                    $nivel = 'Error';
                    break;
                case LIBXML_ERR_FATAL:
                    $nivel = 'Fatal Error';
                    break;
            }
            $msg .= "<b>Error $error->code [$nivel]:</b><br>"
                . str_repeat('&nbsp;', 6) . "Linea: $error->line<br>"
                . str_repeat('&nbsp;', 6) . "Mensaje: $error->message<br>";
        }
        //Limpia el buffer de errores de libxml
        libxml_clear_errors();
        return $msg;
    }
    public function validarXmlToXsd_($dataDoc)
    {
        ini_set('max_execution_time', 300);
        $respuestaCmdXSD = "";
        $docAnte = simplexml_load_string($dataDoc);
        $pathxml = base_path() . '/storage/app/facturaelectronica/sinfirmar/' . $docAnte->infoTributaria->ruc . '/' . $this->tipoDocumento . '/' . $this->tipoDocumento . '-' . $docAnte->infoTributaria->estab . '-' . $docAnte->infoTributaria->ptoEmi . '-' . $docAnte->infoTributaria->secuencial . '.xml';
        try {
            $xsd = "";
            switch ($this->tipoDocumento) {
                case 'factura': //'1.1.0'
                    $xsd =  base_path() . '/storage/app/facturaelectronica/SriXsd/factura_V1.1.0.xsd';
                    break;
                case 'comprobanteRetencion': //1.0.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/.xsd';
                    break;
                case 'notaCredito': //1.1.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/.xsd';
                    break;
                case 'notaDebito': //1.0.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/.xsd';
                    break;
                case 'guiaRemision': //1.1.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/GuiaRemision_V1.1.0.xsd';
                    break;
                case 'liquidacionCompra': //1.0.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/LiquidacionCompra_V1.0.0.xsd';
                    break;
            }
            $jarXSD = base_path() . '/ValidadorXSD/dist/ValidadorXSD.jar';
            $commandXSD = "java -jar $jarXSD $xsd $pathxml 2>&1";
            $respuestaCmdXSD = exec($commandXSD, $output, $return_value);
            return $respuestaCmdXSD;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
    public function generarXmlSignJar($dataDoc, $dir = '', $ruta_token = '', $pin_token = '', $clave_acceso = '', $id_factura = '', $empresa_Id)
    {
        $archivo_firmado = '';
        ini_set('max_execution_time', 300);
        $docAnte = simplexml_load_string($dataDoc);
        //$pathxml = base_path() . '/storage/app/facturaelectronica/firmados/'.$docAnte->infoTributaria->ruc.'/'.$this->tipoDocumento.'/'.$this->tipoDocumento.'-'.$docAnte->infoTributaria->estab.'-'.$docAnte->infoTributaria->ptoEmi.'-'.$docAnte->infoTributaria->secuencial.'.xml';
        $fileName = ($clave_acceso) . "_" . uniqid();
        $filenameXml = tempnam(sys_get_temp_dir(), "");
        $carpeta = base_path() . '/storage/app/facturaelectronica/temp/';
        $this->crearcarpeta($carpeta);
        if ($dir != '') {
            $carpeta = base_path() . '/storage/app/facturaelectronica/firmados/' . $empresa_Id . '/' . $this->tipoDocumento;
            $this->crearcarpeta($carpeta);
            $filenameXml .=  $fileName . ".xml";
        }
        if ($dataDoc != '') {
            $file = fopen($filenameXml, "w");
            fwrite($file, $dataDoc);
            fclose($file);
        }
        $dataDoc = $filenameXml;
        $dataDoc = escapeshellarg($dataDoc);
        $isPathXml = "1";
        $isExite = null;
        $vcontribuyente = $pin_token;
        $certificado_path = $ruta_token;
        $isExite = file_exists($certificado_path) ? true : false;
        $certificado = escapeshellarg($certificado_path);
        if (!$isExite) return null;
        $clave = $vcontribuyente;
        $clave = escapeshellarg($clave);
        $jar = base_path() . '/CompelJar/dist/CompelJar.jar';
        $jar = escapeshellarg($jar);
        $command = "java -jar $jar $certificado $clave $dataDoc $isPathXml 2>&1";
        $respuestaCmd = exec($command, $output, $return_value);
        $resultado = strpos($respuestaCmd, 'El certificado firmante ha caducado');
        /*if ($empresa_Id == '2390058384001') {
            echo '<pre>';
            print_r($respuestaCmd);
            print_r($command);
            print_r($output);
            print_r($return_value);
            exit;
        }*/
        if ($resultado !== FALSE) {
            echo 'El certificado firmante ha caducado: ' . $empresa_Id . ' para firmar: ' . $dir . ' - No.: ' . $id_factura . ' <br/>';
            $mns = 'El certificado firmante ha caducado: ' . $empresa_Id;
            if (file_exists($filenameXml)) unlink($filenameXml);
            $mensaje = $mns;
            return $mensaje;
        }
        $response = "";
        if ($return_value == -1) {
            $response = "";
            foreach ($output as $line)
                $response .= $line . "\n";
        } else {
            if (file_exists($filenameXml)) {
                $file = fopen($filenameXml, "r");
                $response = fread($file, filesize($filenameXml));
                fclose($file);
            }
        }
        if (file_exists($filenameXml))
            unlink($filenameXml);
        $response = trim($response);
        $pos = strpos($response, '<?xml');
        if ($pos !== false) {
            $response = substr($response, $pos);
        } else {
            $pos = strpos($response, '<html');
            $response = substr($response, $pos);
            if ($response == "") $response = $this->obtenerRespuestaXml(false, "", array("No se obtuvo respuesta de firmado"));
            $response = $response = $this->obtenerRespuestaXml(false, "", array($response));
        }
        $mensajes = array('0', $output);
        $error = '';
        $cadena = $response;
        $buscar = "El certificado firmante ha caducado:";
        $resultado = strpos($cadena, $buscar);
        if ($resultado !== FALSE) {
            echo '<pre>';
            print_r($response);
            exit;
        } else {
            $response = $this->procesarRespuestaXml($response);
            $archivo_firmado = $response['comprobante'];
            $mensajes = isset($response["mensajes"][0]) ? $response["mensajes"][0] : '';
            $xmlFirmado = simplexml_load_string($response['comprobante']);
            $r_ = base_path() . '/storage/app/facturaelectronica/firmados/' . $docAnte->infoTributaria->ruc . '/' . $this->tipoDocumento . '/';
            $this->crearcarpeta($r_);
            //echo $r_ . $this->tipoDocumento . '-' . $docAnte->infoTributaria->estab . '-' .  $docAnte->infoTributaria->ptoEmi . '-' . str_pad($docAnte->infoTributaria->secuencial, 9, 0, STR_PAD_LEFT) . '.xml';exit;
            $xmlFirmado = $xmlFirmado->saveXML($r_ . $this->tipoDocumento . '-' . $docAnte->infoTributaria->estab . '-' .  $docAnte->infoTributaria->ptoEmi . '-' . str_pad($docAnte->infoTributaria->secuencial, 9, 0, STR_PAD_LEFT) . '.xml');
        }
        /*if ($empresa_Id == '1793135579001') {
            echo '<pre>';
            print_r($archivo_firmado);
            exit;
        }*/
        return $archivo_firmado;
    }
    public function procesarRespuestaXml($xmlGenerado)
    {
        $compelXml = simplexml_load_string($xmlGenerado);
        $isValido = false;
        $comprobante = "";
        $mensajes = array();
        $isValido = isset($compelXml->firmado) ? ($compelXml->firmado == "VALIDO" ? true : false) : false;
        if ($isValido) {
            $comprobanteRespuesta = (string)$compelXml->comprobante;
            $lineas = explode("\n", $comprobanteRespuesta);
            foreach ($lineas as $linea) {
                if (strlen(trim($linea)) > 0) $comprobante .= $linea . "\n";
            }
        }
        if (isset($compelXml->mensajes)) {
            if ($compelXml->mensajes) {
                if (isset($compelXml->mensajes->mensaje)) {
                    foreach ($compelXml->mensajes->mensaje as $mensaje) {
                        $mensaje = $this->htmlspecial($mensaje);
                        array_push($mensajes, "<br>- " . $mensaje);
                    }
                }
            }
        }
        $result = array();
        $result["isValido"] = $isValido;
        $result["mensajes"] = $mensajes;
        $result["comprobante"] = $comprobante;
        return $result;
    }
    public function htmlspecial($valor)
    {
        $result = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
        return $result;
    }
    public function recibirWs($comprobante, $tipoAmbiente = 1)
    {
        ini_set('max_execution_time', 300);
        $url = "";
        switch ($tipoAmbiente) {
            case 1:
                $url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';
                break;
            case 2:
                $url = 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';
                break;
        }
        $params = array("xml" => $comprobante);
        $client = new SoapClient($url);
        $result = $client->validarComprobante($params);
        if ($result) {
            if ($result->RespuestaRecepcionComprobante) {
                $result->isRecibida = $result->RespuestaRecepcionComprobante->estado === "RECIBIDA" ? true : false;
                if ($result->RespuestaRecepcionComprobante->comprobantes) {
                    if (isset($result->RespuestaRecepcionComprobante->comprobantes->comprobante)) {
                        $comprobantes = $result->RespuestaRecepcionComprobante->comprobantes->comprobante;
                        $result->RespuestaRecepcionComprobante->comprobantes = array();
                        if (is_array($comprobantes)) {
                            $result->RespuestaRecepcionComprobante->comprobantes = $comprobantes;
                        } else {
                            $result->RespuestaRecepcionComprobante->comprobantes[0] = $comprobantes;
                        }
                        $result->RespuestaRecepcionComprobante->mensajesWs = array();
                        $result->RespuestaRecepcionComprobante->mensajesDb = array();
                        for ($idxComprobante = 0; $idxComprobante < count($result->RespuestaRecepcionComprobante->comprobantes); $idxComprobante++) {
                            $comprobante = $result->RespuestaRecepcionComprobante->comprobantes[$idxComprobante];
                            if ($comprobante->mensajes) {
                                if (isset($comprobante->mensajes->mensaje)) {
                                    $mensajes = $comprobante->mensajes->mensaje;
                                    $comprobante->mensajes = array();
                                    if (is_array($mensajes)) {
                                        $comprobante->mensajes = $mensajes;
                                    } else {
                                        $comprobante->mensajes[0] = $mensajes;
                                    }
                                }
                                for ($idxMensaje = 0; $idxMensaje < count($comprobante->mensajes); $idxMensaje++) {
                                    $item = $comprobante->mensajes[$idxMensaje];
                                    $informacionAdicional = isset($item->informacionAdicional) ? $item->informacionAdicional : "";
                                    $estado_recibido = $result->RespuestaRecepcionComprobante->estado;
                                    $tipo = isset($item->tipo) ? $item->tipo : "";
                                    $identificador = isset($item->identificador) ? $item->identificador : "";
                                    $mensaje = $item->mensaje;
                                    $identificador = $item->identificador;
                                    $tipo = $item->tipo;
                                    $mensajeDB = trim("({$tipo}-{$identificador}) {$mensaje} {$informacionAdicional}");
                                    $mensajesWs = trim("({$tipo}-{$identificador}) {$mensaje} {$informacionAdicional}");
                                    array_push($result->RespuestaRecepcionComprobante->mensajesDb, $mensajeDB);
                                    array_push($result->RespuestaRecepcionComprobante->mensajesWs, $mensajesWs);
                                    $comprobante->mensajes[$idxMensaje] = (array)$comprobante->mensajes[$idxMensaje];
                                }
                            }
                            $result->RespuestaRecepcionComprobante->comprobantes[$idxComprobante] = (array)$result->RespuestaRecepcionComprobante->comprobantes[$idxComprobante];
                        }
                    }
                    $isRecibida = $result->isRecibida;
                    $result = (array)$result->RespuestaRecepcionComprobante;
                    $result["isRecibida"] = $isRecibida;
                }
            }
        }
        return $result;
    }
    public function generarXmlRespuesta($xmlRespuesta, $clave = '')
    {
        try {
            $xml = new \DOMDocument('1.0', 'UTF-8');
            $root = $xml->createElement('RespuestaRecepcioComprobante');
            $estado = $xmlRespuesta['estado'];
            $mensajes = [];
            $nombreArchivo = "noName";
            if ($estado == 'DEVUELTA') {
                $mensajes = $xmlRespuesta['mensajesWs'];
                $estado = $xml->createElement('estado', $xmlRespuesta['estado']);
                $root->appendChild($estado);
                $nodoComprobantes = $xml->createElement('comprobantes');
                $comprobantes = $xmlRespuesta['comprobantes'];
                foreach ($comprobantes as $comprobante) {
                    $nodoComprobante = $xml->createElement('comprobante');
                    $nodoComprobante->appendChild($xml->createElement('claveAcceso', $comprobante['claveAcceso']));
                    $nombreArchivo =  $comprobante['claveAcceso'];
                    $nodoMensajes = $xml->createElement('Mensajes');
                    $mensajes = $comprobante['mensajes'];
                    $nodoMensajes = $this->getMensajes($xml, $mensajes);
                    $nodoComprobante->appendChild($nodoMensajes);
                    $nodoComprobantes->appendChild($nodoComprobante);
                    $root->appendChild($nodoComprobantes);
                }
                $mensajesWs = $xmlRespuesta['mensajesWs'];
                $nodoMensajesWs = $xml->createElement('MensajesWs');
                foreach ($mensajesWs as $mensajeWs) {
                    $nodoMensajesWs->appendChild($xml->createElement('mensajeWs',  $mensajeWs));
                    $root->appendChild($nodoMensajesWs);
                    $nodoMensajesWs->appendChild($xml->createElement('mensajeWs',  $mensajeWs));
                    $root->appendChild($nodoMensajesWs);
                }
                $mensajesDb = $xmlRespuesta['mensajesDb'];
                $nodoMensajesDb = $xml->createElement('MensajesDb');
                foreach ($mensajesDb as $mensajeDb) {
                    $nodoMensajesDb->appendChild($xml->createElement('mensajeDb',  $mensajeDb));
                    $root->appendChild($nodoMensajesDb);
                }
                $recibido = 'true';
                if ($xmlRespuesta['isRecibida'] == false)
                    $recibido = 'false';
                $recibida = $xml->createElement('isRecibida', $recibido);
                $root->appendChild($recibida);
                $xml->appendChild($root);
                $xml->formatOutput = true;
                $comprobante = $xml->saveXML();
                $r_ = base_path() . '/storage/app/facturaelectronica/respuestaSri/recepcion/';
                $this->crearcarpeta($r_);
                $ruta = $r_ . $nombreArchivo . '.xml';
                $xml->save($ruta);
                //return $comprobante;
            } elseif ($estado == 'RECIBIDA') {
            } else {
                $mensajes = $xmlRespuesta['mensajesWs'];
                $estado = $xml->createElement('estado', $xmlRespuesta['estado']);
                $root->appendChild($estado);
                $nodoComprobantes = $xml->createElement('comprobantes');
                $comprobantes = $xmlRespuesta['comprobantes'];
                foreach ($comprobantes as $comprobante) {
                    $nodoComprobante = $xml->createElement('comprobante');
                    $nodoComprobante->appendChild($xml->createElement('claveAcceso', $clave));
                    $nombreArchivo =  $clave;
                    $nodoMensajes = $xml->createElement('Mensajes');
                    $mensajes = $comprobante['mensajes'];
                    $nodoMensajes = $this->getMensajes($xml, $mensajes);
                    $nodoComprobante->appendChild($nodoMensajes);
                    $nodoComprobantes->appendChild($nodoComprobante);
                    $root->appendChild($nodoComprobantes);
                }
                $mensajesWs = $xmlRespuesta['mensajesWs'];
                $nodoMensajesWs = $xml->createElement('MensajesWs');
                foreach ($mensajesWs as $mensajeWs) {
                    $nodoMensajesWs->appendChild($xml->createElement('mensajeWs',  $mensajeWs));
                    $root->appendChild($nodoMensajesWs);
                    $nodoMensajesWs->appendChild($xml->createElement('mensajeWs',  $mensajeWs));
                    $root->appendChild($nodoMensajesWs);
                }
                $mensajesDb = $xmlRespuesta['mensajesDb'];
                $nodoMensajesDb = $xml->createElement('MensajesDb');
                foreach ($mensajesDb as $mensajeDb) {
                    $nodoMensajesDb->appendChild($xml->createElement('mensajeDb',  $mensajeDb));
                    $root->appendChild($nodoMensajesDb);
                }
                $recibido = 'false';
                $recibida = $xml->createElement('isRecibida', $recibido);
                $root->appendChild($recibida);
                $xml->appendChild($root);
                $xml->formatOutput = true;
                $r_ = base_path() . '/storage/app/facturaelectronica/respuestaSri/recepcion/';
                $this->crearcarpeta($r_);
                $ruta = $r_ . $nombreArchivo . '.xml';
                $comprobante = $xml->saveXML();
                $xml->save($ruta);
            }
        } catch (Exception $ex) {
            return json_encode(['Error: ' => $ex->getMessage(), 'Linea: ' => $ex->getLine()]);
        }
    }
    public function getMensajes($xmlDocument, $mensajes)
    {
        $nodoMensajes = $xmlDocument->createElement('mensajes');
        foreach ($mensajes as $mensaje) {
            $nodoMensaje = $xmlDocument->createElement('mensaje');
            $nodoMensaje->appendChild($xmlDocument->createElement('dentificador',  $mensaje['identificador']));
            $nodoMensaje->appendChild($xmlDocument->createElement('mensaje',  $mensaje['mensaje']));
            if (isset($mensaje['informacionAdicional'])) {
                $nodoMensaje->appendChild($xmlDocument->createElement('informacionAdicional',  $mensaje['informacionAdicional']));
            }

            $nodoMensaje->appendChild($xmlDocument->createElement('tipo',  $mensaje['tipo']));
            $nodoMensajes->appendChild($nodoMensaje);
        }
        return $nodoMensajes;
    }
    public function autorizacion_sri($claveAcceso, $ambiente = 1)
    {
        ini_set('max_execution_time', 300);
        sleep(3);

        switch ($ambiente) {
            case 1:
                //$url = "https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl"; //Pruebas Autorizacioin de Documentos
                $url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';
                break;
            case 2:
                //$url="https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl"; //Prodccion Atorizacion de Documentos
                $url = 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';
                break;
        }
        $client = new SoapClient($url);
        $param = array(
            'claveAccesoComprobante' => $claveAcceso
        );
        return $client->autorizacionComprobante($param);
    }
    public function generarXmlAutorizacion($xmlRespuesta)
    {
        $nombreArchivo = "noName";
        $estado = "sin_procesar";
        try {
            $xml = new \DOMDocument('1.0', 'UTF-8');
            $root = $xml->createElement('RespuestaAutorizacionComprobante');
            $clave = $xml->createElement('claveAccesoConsultada', $xmlRespuesta->RespuestaAutorizacionComprobante->claveAccesoConsultada);
            $root->appendChild($clave);
            $nombreArchivo = $xmlRespuesta->RespuestaAutorizacionComprobante->claveAccesoConsultada;
            $numeroComprobante = $xml->createElement('numeroComprobantes', $xmlRespuesta->RespuestaAutorizacionComprobante->numeroComprobantes);
            $root->appendChild($numeroComprobante);

            $nodoAutorizaciones = $xml->createElement('autorizaciones');
            $autorizaciones = $xmlRespuesta->RespuestaAutorizacionComprobante->autorizaciones;
            foreach ($autorizaciones as $autorizacion) {
                $nodoAutorizacion = $xml->createElement('autorizacion');
                $estado = $autorizacion->estado;
                if ($estado == 'EN PROCESO') {
                    $arrayDoc = [
                        'id_de_pasos' => 12,
                    ];
                    De_Documentos_Electronicos::where('clave_acceso', $clave)
                        ->update($arrayDoc);
                }
                $nodoAutorizacion->appendChild($xml->createElement('estado', $autorizacion->estado));
                $nodoAutorizacion->appendChild($xml->createElement('fechaAutorizacion', $autorizacion->fechaAutorizacion));
                $nodoAutorizacion->appendChild($xml->createElement('ambiente', $autorizacion->ambiente));
                $nodoAutorizacion->appendChild($xml->createElement('comprobante', $autorizacion->comprobante));

                if (!is_null($autorizacion->mensajes)) {
                    $nodoMensajes = $this->getMensajesAutorizacion($xml, $autorizacion->mensajes);
                    $nodoAutorizacion->appendChild(($nodoMensajes));
                } else {
                    $nodoMensajes = $xml->createElement('mensajes');
                    $nodoAutorizacion->appendChild(($nodoMensajes));
                }
                $nodoAutorizaciones->appendChild($nodoAutorizacion);
            }
            $root->appendChild($nodoAutorizaciones);
            $xml->appendChild($root);
            $xml->formatOutput = true;
            $comprobante = $xml->saveXML();
            $r_ = base_path() . '/storage/app/facturaelectronica/respuestaSri/autorizacion/';
            $this->crearcarpeta($r_);
            $ruta = $r_ . $nombreArchivo . '_' . $estado . '.xml';
            $xml->save($ruta);
            return $estado;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            echo '<br/>' . $ex->getLine();
            return;
        }
    }
    public function generarXmlNoAutorizado($data)
    {
        $nombreArchivo = "noName";
        $estado = "sin procesar";
        try {
            $xmlRespuesta = simplexml_load_string($data);
            $xml = new \DOMDocument('1.0', 'UTF-8');
            $root = $xml->createElement('RespuestaAutorizacionComprobante');
            $clave = $xml->createElement('claveAccesoConsultada', $xmlRespuesta->claveAccesoConsultada);
            $root->appendChild($clave);
            $nombreArchivo = $xmlRespuesta->claveAccesoConsultada;
            $numeroComprobante = $xml->createElement('numeroComprobantes', $xmlRespuesta->numeroComprobantes);
            $root->appendChild($numeroComprobante);
            $nodoAutorizaciones = $xml->createElement('autorizaciones');
            $autorizaciones = $xmlRespuesta->autorizaciones;

            foreach ($autorizaciones as $autorizacion) {
                $nodoAutorizacion = $xml->createElement('autorizacion');
                $estado = $autorizacion->autorizacion->estado;
                $nodoAutorizacion->appendChild($xml->createElement('estado', $autorizacion->autorizacion->estado));
                $nodoAutorizacion->appendChild($xml->createElement('fechaAutorizacion', $autorizacion->autorizacion->fechaAutorizacion));
                $nodoAutorizacion->appendChild($xml->createElement('ambiente', $autorizacion->autorizacion->ambiente));
                $nodoAutorizacion->appendChild($xml->createElement('comprobante', $autorizacion->autorizacion->comprobante));
                $mensajes = $autorizacion->autorizacion->mensajes;
                if (count($mensajes) > 0) {
                    $nodoMensajes = $this->getMensajesAutorizacion($xml, $mensajes->mensaje);
                    $nodoAutorizacion->appendChild(($nodoMensajes));
                } else {
                    $nodoMensajes = $xml->createElement('mensajes');
                    $nodoAutorizacion->appendChild(($nodoMensajes));
                }
                $nodoAutorizaciones->appendChild($nodoAutorizacion);
            }
            $root->appendChild($nodoAutorizaciones);
            $xml->appendChild($root);
            $xml->formatOutput = true;
            $comprobante = $xml->saveXML();
            $r_ = base_path() . '/storage/app/facturaelectronica/respuestaSri/Autorizacion' . '/';
            $this->crearcarpeta($r_);
            $ruta = $r_ . $nombreArchivo . '_' . $estado . '.xml';
            $xml->save($ruta);
            return $comprobante;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            echo '<br/>' . $ex->getLine();
        }
    }
    public function getMensajesAutorizacion($xmlDocument, $mensajes)
    {
        $nodoMensajes = $xmlDocument->createElement('mensajes');
        foreach ($mensajes as $mensaje) {
            $nodoMensaje = $xmlDocument->createElement('mensaje');
            $nodoMensaje->appendChild($xmlDocument->createElement('Identificador',  $mensaje->identificador));
            $nodoMensaje->appendChild($xmlDocument->createElement('mensaje',  $mensaje->mensaje));
            if (isset($mensaje->informacionAdicional)) {
                $nodoMensaje->appendChild($xmlDocument->createElement('informacionAdicional',  $mensaje->informacionAdicional));
            }
            $nodoMensaje->appendChild($xmlDocument->createElement('tipo',  $mensaje->tipo));
            $nodoMensajes->appendChild($nodoMensaje);
        }
        return $nodoMensajes;
    }

    public function reconstruirDocumento(Request $request)
    {
        $clave = trim((string) $request->clave);
        $archivo = strtolower(trim((string) $request->archivo));
        $archivo = $archivo === '' ? 'pdf' : $archivo;
        $forzar = (int) $request->forzar === 1;

        if (strlen($clave) !== 49 || !ctype_digit($clave)) {
            return response()->json([
                'status' => 'error',
                'message' => 'La clave de acceso debe contener exactamente 49 dígitos.',
            ], 422);
        }
        if (!in_array($archivo, ['pdf', 'xml'], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'El archivo solicitado debe ser pdf o xml.',
            ], 422);
        }

        $codigoDocumento = substr($clave, 8, 2);
        $documentosSoportados = ['01', '03', '04', '05', '06', '07'];
        if (!in_array($codigoDocumento, $documentosSoportados, true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'El tipo de documento de la clave no está soportado.',
            ], 422);
        }
        if ($archivo === 'pdf' && $codigoDocumento === '05') {
            return response()->json([
                'status' => 'error',
                'message' => 'No existe una plantilla PDF disponible para notas de débito.',
            ], 422);
        }

        $rucEmisor = substr($clave, 10, 13);
        if (!Empresa::where('id', $rucEmisor)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'La clave no pertenece a una empresa registrada en el sistema.',
            ], 404);
        }
        $empresaSesion = session('id_empresa');
        if (!empty($empresaSesion) && (string) $empresaSesion !== $rucEmisor) {
            return response()->json([
                'status' => 'error',
                'message' => 'La clave no pertenece a la empresa activa.',
            ], 403);
        }

        $directorio = base_path() . '/storage/app/facturaelectronica/respuestaSri/autorizacion/';
        $xmlAutorizado = $directorio . $clave . '_AUTORIZADO.xml';
        $pdfAutorizado = $directorio . $clave . '_AUTORIZADO.pdf';

        try {
            if ($forzar || !$this->xmlAutorizadoValido($xmlAutorizado, $clave)) {
                $ambiente = (int) substr($clave, 23, 1);
                if (!in_array($ambiente, [1, 2], true)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'La clave de acceso no contiene un ambiente válido.',
                    ], 422);
                }

                $respuesta = null;
                $origen = '';
                try {
                    $respuestaSri = $this->autorizacion_sri($clave, $ambiente);
                    $respuesta = $this->normalizarRespuestaAutorizada($respuestaSri, $clave);
                    if ($respuesta !== null) {
                        $origen = 'sri';
                    }
                } catch (Exception $consultaSriError) {
                    $respuesta = null;
                }

                if ($respuesta === null) {
                    $respuesta = $this->respuestaAutorizadaGuardada($clave);
                    if ($respuesta !== null) {
                        $origen = 'base_de_datos';
                    }
                }

                if ($respuesta === null) {
                    // Si la respuesta del SRI tampoco está guardada, todavía
                    // podemos reconstruir el comprobante con la información
                    // persistida en de_documentos_electronicos.
                    if ($this->reconstruirDesdeDatosGuardados($clave)) {
                        $origen = 'datos_de_documentos_electronicos';
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'No se encontró información suficiente para reconstruir el documento en la base ni en el SRI.',
                        ], 404);
                    }
                }

                if ($respuesta !== null) {
                    $estado = $this->generarXmlAutorizacion($respuesta);
                }
                if (!$this->xmlAutorizadoValido($xmlAutorizado, $clave)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'La respuesta recuperada no permitió reconstruir un XML autorizado válido.',
                    ], 422);
                }
            } else {
                $origen = 'archivo_local';
            }

            if ($archivo === 'xml') {
                return response()->download($xmlAutorizado, basename($xmlAutorizado), [
                    'X-Documento-Reconstruido-Desde' => $origen,
                ]);
            }

            if (file_exists($pdfAutorizado)) {
                return response()->download($pdfAutorizado, basename($pdfAutorizado), [
                    'X-Documento-Reconstruido-Desde' => $origen,
                ]);
            }

            return $this->generarPdf($clave, 'descargar');
        } catch (Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => 'No fue posible reconstruir el documento.',
                'detalle' => $ex->getMessage(),
                'linea' => $ex->getLine(),
            ], 500);
        }
    }

    private function respuestaAutorizadaGuardada($clave)
    {
        $documentos = De_Documentos_Electronicos::where('clave_acceso', $clave)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($documentos as $documento) {
            // Algunas versiones guardaron la respuesta con distinto formato o
            // incluso como XML. Probamos ambas respuestas persistidas.
            foreach (['respuestaSriAutorizacion', 'respuestaSriRecepcion'] as $campo) {
                $contenido = isset($documento->{$campo}) ? trim((string) $documento->{$campo}) : '';
                if ($contenido === '') {
                    continue;
                }

                $respuesta = $this->decodificarRespuestaAutorizacion($contenido);
                $normalizada = $this->normalizarRespuestaAutorizada($respuesta, $clave);
                if ($normalizada !== null) {
                    return $normalizada;
                }
            }
        }

        return null;
    }

    private function decodificarRespuestaAutorizacion($contenido)
    {
        $respuesta = $contenido;
        for ($i = 0; $i < 3 && is_string($respuesta); $i++) {
            $json = json_decode($respuesta);
            if (json_last_error() === JSON_ERROR_NONE && $json !== null) {
                $respuesta = $json;
                continue;
            }

            $xml = @simplexml_load_string($respuesta);
            if ($xml !== false) {
                return $xml;
            }
            break;
        }

        if (is_array($respuesta)) {
            $respuesta = json_decode(json_encode($respuesta));
        }
        return $respuesta;
    }

    private function reconstruirDesdeDatosGuardados($clave)
    {
        $documento = De_Documentos_Electronicos::where('clave_acceso', $clave)
            ->where('ruc_emisor', substr($clave, 10, 13))
            ->orderBy('id', 'desc')
            ->first();
        if ($documento === null) {
            return false;
        }

        $campos = [];
        foreach (['infoTributaria', 'infoFactura', 'infoNotaCredito', 'infoNotaDebito', 'infoCompRetencion', 'infoLiquidacionCompra', 'infoGuiaRemision', 'detalles', 'infoAdicional', 'docsSustento', 'destinatarios', 'motivos', 'retenciones', 'reembolsos', 'maquinaFiscal'] as $campo) {
            if (!isset($documento->{$campo}) || trim((string) $documento->{$campo}) === '') {
                continue;
            }
            $valor = $this->decodificarDatosDocumento((string) $documento->{$campo});
            if ($valor !== null) {
                $campos[$campo] = $valor;
            }
        }

        if (!isset($campos['infoTributaria']) || !is_array($campos['infoTributaria'])) {
            return false;
        }

        $campos['infoTributaria']['claveAcceso'] = $clave;
        $campos['infoTributaria']['codDoc'] = isset($campos['infoTributaria']['codDoc'])
            ? str_pad((string) $campos['infoTributaria']['codDoc'], 2, '0', STR_PAD_LEFT)
            : substr($clave, 8, 2);
        $campos['infoTributaria']['ruc'] = $campos['infoTributaria']['ruc'] ?? substr($clave, 10, 13);
        $campos['infoTributaria']['estab'] = $campos['infoTributaria']['estab'] ?? substr($clave, 24, 3);
        $campos['infoTributaria']['ptoEmi'] = $campos['infoTributaria']['ptoEmi'] ?? substr($clave, 27, 3);
        $campos['infoTributaria']['secuencial'] = $campos['infoTributaria']['secuencial'] ?? substr($clave, 30, 9);

        // Para facturas antiguas puede faltar el JSON de detalles en la tabla
        // electrónica, pero el detalle contable permanece en ct_detalle_venta.
        if ($campos['infoTributaria']['codDoc'] === '01' && empty($campos['detalles'])) {
            $campos['detalles'] = $this->detallesFacturaDesdeBase($documento->id_documento);
        }
        if ($campos['infoTributaria']['codDoc'] === '01' && empty($campos['detalles'])) {
            return false;
        }

        try {
            $comprobante = $this->generarDocElectronicoXml($campos, false);
            if (empty($comprobante) || @simplexml_load_string($comprobante) === false) {
                return false;
            }

            $directorio = base_path() . '/storage/app/facturaelectronica/respuestaSri/autorizacion/';
            $this->crearcarpeta($directorio);
            $xml = new DOMDocument('1.0', 'UTF-8');
            $xml->formatOutput = true;
            $root = $xml->createElement('RespuestaAutorizacionComprobante');
            $root->appendChild($xml->createElement('claveAccesoConsultada', $clave));
            $root->appendChild($xml->createElement('numeroComprobantes', '1'));
            $autorizaciones = $xml->createElement('autorizaciones');
            $autorizacion = $xml->createElement('autorizacion');
            $autorizacion->appendChild($xml->createElement('estado', 'AUTORIZADO'));
            $autorizacion->appendChild($xml->createElement('fechaAutorizacion', date('d/m/Y H:i:s')));
            $autorizacion->appendChild($xml->createElement('ambiente', (string) ($campos['infoTributaria']['ambiente'] ?? '1')));
            $autorizacion->appendChild($xml->createElement('comprobante', $comprobante));
            $autorizaciones->appendChild($autorizacion);
            $root->appendChild($autorizaciones);
            $xml->appendChild($root);
            return $xml->save($directorio . $clave . '_AUTORIZADO.xml');
        } catch (Exception $e) {
            return false;
        }
    }

    private function detallesFacturaDesdeBase($idVenta)
    {
        $detalles = [];
        foreach (Ct_detalle_venta::getDetalles($idVenta) as $detalleBase) {
            $codigo = substr((string) $detalleBase->id_ct_productos, 0, 25);
            $descripcion = $detalleBase->descripcion_producto ?: $detalleBase->nombre;
            $otros = json_decode((string) $detalleBase->otros);
            if ($otros !== null && !empty($otros->codigo_publico)) {
                $codigo = substr((string) $otros->codigo_publico, 0, 25);
                $descripcion = !empty($otros->descripcion_publico)
                    ? (string) $otros->descripcion_publico
                    : $descripcion;
            }

            $descuento = $detalleBase->descuento === '' ? 0 : (float) $detalleBase->descuento;
            $cantidad = (float) $detalleBase->cantidad;
            $precio = (float) $detalleBase->precio;
            $precioUnitario = $detalleBase->copago > 0
                ? (((float) $detalleBase->extendido + $descuento) / max($cantidad, 1))
                : $precio;
            $total = $detalleBase->copago > 0
                ? (float) $detalleBase->extendido
                : (($cantidad * $precio) - $descuento);

            $detallesAdicionales = [];
            if ((string) $detalleBase->detalle !== '') {
                $detallesAdicionales[] = ['nombre' => 'detalle', 'valor' => $detalleBase->detalle];
            }
            if ((string) $detalleBase->adicional !== '') {
                $detallesAdicionales[] = ['nombre' => 'adicional', 'valor' => $detalleBase->adicional];
            }

            $detalles[] = [
                'p_impuesto' => $detalleBase->check_iva !== null ? ($this->porIva->iva * 100) : 0,
                'codigoPrincipal' => $codigo,
                'codigoAuxiliar' => $codigo,
                'descripcion' => $descripcion,
                'cantidad' => $cantidad,
                'precioUnitario' => $this->getDecimal($precioUnitario, 4),
                'descuento' => number_format($descuento, 2, '.', ''),
                'precioTotalSinImpuesto' => $this->getDecimal($total),
                'detallesAdicionales' => $detallesAdicionales,
            ];
        }
        return $detalles;
    }

    private function decodificarDatosDocumento($contenido)
    {
        $valor = trim($contenido);
        for ($i = 0; $i < 3 && is_string($valor); $i++) {
            $json = json_decode($valor, true);
            if (json_last_error() !== JSON_ERROR_NONE || $json === null) {
                return null;
            }
            $valor = $json;
        }
        return is_array($valor) ? $valor : null;
    }

    private function normalizarRespuestaAutorizada($respuesta, $clave)
    {
        if (!is_object($respuesta) || !isset($respuesta->RespuestaAutorizacionComprobante)) {
            return null;
        }
        $contenedor = $respuesta->RespuestaAutorizacionComprobante;
        if (!isset($contenedor->autorizaciones)) {
            return null;
        }

        $autorizacionesNodo = $contenedor->autorizaciones;
        $autorizaciones = isset($autorizacionesNodo->autorizacion)
            ? $autorizacionesNodo->autorizacion
            : $autorizacionesNodo;
        if (!is_array($autorizaciones)) {
            $autorizaciones = [$autorizaciones];
        }
        foreach ($autorizaciones as $autorizacion) {
            if (
                !is_object($autorizacion)
                || !isset($autorizacion->estado, $autorizacion->comprobante)
                || strtoupper(trim((string) $autorizacion->estado)) !== 'AUTORIZADO'
            ) {
                continue;
            }

            $comprobante = @simplexml_load_string((string) $autorizacion->comprobante);
            if (
                $comprobante === false
                || !isset($comprobante->infoTributaria->claveAcceso)
                || trim((string) $comprobante->infoTributaria->claveAcceso) !== $clave
            ) {
                continue;
            }
            if (!isset($autorizacion->mensajes)) {
                $autorizacion->mensajes = null;
            }

            $normalizada = new stdClass();
            $normalizada->RespuestaAutorizacionComprobante = new stdClass();
            $normalizada->RespuestaAutorizacionComprobante->claveAccesoConsultada = $clave;
            $normalizada->RespuestaAutorizacionComprobante->numeroComprobantes = 1;
            $normalizada->RespuestaAutorizacionComprobante->autorizaciones = new stdClass();
            $normalizada->RespuestaAutorizacionComprobante->autorizaciones->autorizacion = $autorizacion;
            return $normalizada;
        }
        return null;
    }

    private function xmlAutorizadoValido($ruta, $clave)
    {
        if (!file_exists($ruta) || !is_readable($ruta)) {
            return false;
        }

        $usarErroresInternos = libxml_use_internal_errors(true);
        $xml = simplexml_load_file($ruta);
        libxml_clear_errors();
        libxml_use_internal_errors($usarErroresInternos);
        if (
            $xml === false
            || trim((string) $xml->claveAccesoConsultada) !== $clave
            || !isset($xml->autorizaciones->autorizacion)
        ) {
            return false;
        }

        foreach ($xml->autorizaciones->autorizacion as $autorizacion) {
            if (
                !isset($autorizacion->estado, $autorizacion->comprobante)
                || strtoupper(trim((string) $autorizacion->estado)) !== 'AUTORIZADO'
            ) {
                continue;
            }
            $comprobante = @simplexml_load_string((string) $autorizacion->comprobante);
            if (
                $comprobante !== false
                && isset($comprobante->infoTributaria->claveAcceso)
                && trim((string) $comprobante->infoTributaria->claveAcceso) === $clave
            ) {
                return true;
            }
        }
        return false;
    }

    public function generarPdf($xml, $accion = '')
    {
        $docCod = substr($xml, 8, 2);
        $esta = substr($xml, 24, 3);
        $emis = substr($xml, 27, 3);
        $num = substr($xml, 30, 9);
        $clave = $xml;
        $tipoText = '';
        $pdf_ = '';
        try {
            $xml = base_path() . '/storage/app/facturaelectronica/respuestaSri/autorizacion/' . $clave . '_AUTORIZADO.xml';
            if ($docCod == '01') {
                $pdf_ = 'sri_electronico.documentosgenerados.view_facturapdf';
                $tipoText = 'Factura-';
            } elseif ($docCod == '03') {
                $pdf_ = 'sri_electronico.documentosgenerados.view_liquidacioncomprapdf';
                $tipoText = 'Liquidacion de compra-';
            } elseif ($docCod == '04') {
                $pdf_ = 'sri_electronico.documentosgenerados.view_notaCreditopdf';
                $tipoText = 'Nota de credito-';
            } elseif ($docCod == '05') {
                $pdf_ = 'sri_electronico.documentosgenerados.view_notaDebitopdf';
                $tipoText = 'Nota de debito-';
            } elseif ($docCod == '06') {
                $pdf_ = 'sri_electronico.documentosgenerados.view_guiaremisionpdf';
                $tipoText = 'Guia de remision-';
            } elseif ($docCod == '07') {
                $pdf_ = 'sri_electronico.documentosgenerados.view_compRetencionPDF';
                $tipoText = 'Retencion-';
            }
            if (file_exists($xml)) {
                $data['xml'] = $xml;
                $data['porIva'] = $this->porIva;
                $view = \Illuminate\Support\Facades\View::make($pdf_, $data)->render();
                $pdf = \Illuminate\Support\Facades\App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->setPaper('a4', 'landscape');
                if ($accion == '') {
                    $pdf->save(base_path() . '/storage/app/facturaelectronica/respuestaSri/autorizacion/' . $clave . '_AUTORIZADO.pdf');
                    return $pdf->stream($clave . '.pdf');
                } else {
                    return $pdf->download($tipoText . $esta . '-' . $emis . '-' . $num . '.pdf');
                }
            } else {
                echo 'The file does not exist.';
            }
        } catch (Exception $e) {
            echo $e->getMessage() . 'Linea: ' . $e->getLine();
            exit;
        }
    }
    public function descargarXML($clave)
    {
        //dd($clave);
        if (!empty($clave)) {
            $xml = '/storage/app/facturaelectronica/respuestaSri/autorizacion/' . $clave . '_AUTORIZADO.xml';
            $fileName = basename($xml);
            $filePath =  base_path() . $xml;
            if (!empty($fileName) && file_exists($filePath)) {
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$fileName");
                header("Content-Type: application/zip");
                header("Content-Transfer-Encoding: binary");
                readfile($filePath);
                exit;
            } else {
                echo 'The file does not exist.';
            }
        } else {
            die("Invalid file name!");
        }
    }
    public function enviar_correo($param)
    {
        $nombre = $param['nombre'];
        $apellido = $param['apellido'];
        $email = strtolower($param['email']);
        $valorPagar = $param['valorPagar'];
        $proveedor = '';
        $num = $param['estab'] . '-' . $param['ptoEmi'] . '-' . $param['secuencial'];
        $param['num'] = $num;
        $tipoDoc = '';
        if ($param['tipoDoc'] == '01')
            $tipoDoc = 'Factura';
        if ($param['tipoDoc'] == '03')
            $tipoDoc = 'Liquidación de compra';
        if ($param['tipoDoc'] == '04')
            $tipoDoc = 'Nota de crédito';
        if ($param['tipoDoc'] == '05')
            $tipoDoc = 'Nota de débito';
        if ($param['tipoDoc'] == '06')
            $tipoDoc = 'Guía de remisón';
        if ($param['tipoDoc'] == '07')
            $tipoDoc = 'Comprobante de retención';
        $param['tipoDoc'] = $tipoDoc;
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $mes = str_replace('/', '-', $param['fechaEmision']);
        $fechaEmision = date('d', strtotime($mes)) . ' de ' . $meses[date('n', strtotime($mes)) - 1] . ' ' . date('Y', strtotime($mes));
        $mes = $meses[date('n', strtotime($mes)) - 1];
        $param['mes'] = $mes;
        $param['fechaEmision'] = $fechaEmision;
        $no_reply_email = "no-reply@mdconsgroup.com";
        $nombre_emisor = "MdConsgroup";
        $ruc = substr($param['claveAcceso'], 10, 13);
        if ($ruc == '0993069299001') {
            $no_reply_email = 'no-reply@gastritech.com';
            $nombre_emisor = "Gastritech";
        }
        try {
            $archivoXml = base_path() . '/storage/app/facturaelectronica/respuestaSri/autorizacion/' . $param['claveAcceso'] . '_AUTORIZADO.xml';
            $archivoPdf = base_path() . '/storage/app/facturaelectronica/respuestaSri/autorizacion/' . $param['claveAcceso'] . '_AUTORIZADO.pdf';
            $txtAsunto = $tipoDoc . ' No.: ' . $param['estab'] . '-' . $param['ptoEmi'] . '-' . $param['secuencial'] . ' || ' . $nombre . ' ' . $apellido;
            Mail::send('sri_electronico.view_plantilla_email', $param, function ($msj) use ($nombre_emisor, $no_reply_email, $txtAsunto, $email, $archivoXml, $archivoPdf) {
                $msj->subject($txtAsunto);
                $msj->to($email);
                $msj->from($no_reply_email, "Documento Electrónico - " . $nombre_emisor);
                $msj->attach($archivoXml);
                $msj->attach($archivoPdf);
                $msj->bcc('alexo8ec@hotmail.com');
            });
            echo response()->json([
                'Message ' => 'Correo enviado a ' . $nombre . ' ' . $apellido,
                'Email' => $email
            ], 200);
        } catch (Exception $e) {
            echo json_encode(['Error: ' => $e->getMessage(), 'Linea: ' => $e->getLine()]);
        }
    }
    public function getInfoFactura($xmlDocument, $campos)
    {
        //echo '<pre>';print_r($campos);exit;
        $moneda = 'DOLAR';
        $nodoDetalle = $xmlDocument->createElement('infoFactura');
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $this->getDate(isset($campos['infoFactura']['fecha_emision']) ? $campos['infoFactura']['fecha_emision'] : $campos['infoFactura']['fechaEmision'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoFactura']['dirEstablecimiento']));
        if ($campos['infoFactura']['contribuyenteEspecial']) $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos['infoFactura']['contribuyenteEspecial']));
        $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos['infoFactura']['obligadoContabilidad']));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionComprador', $campos['infoFactura']['tipoIdentificacionComprador']));
        if ($campos['infoFactura']['guiaRemision']) $nodoDetalle->appendChild($xmlDocument->createElement('guiaRemision', $campos['infoFactura']['guiaRemision']));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialComprador', str_replace('&', 'Y', $campos['infoFactura']['razonSocialComprador'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionComprador', $campos['infoFactura']['identificacionComprador']));
        if ($campos['infoFactura']['direccionComprador']) $nodoDetalle->appendChild($xmlDocument->createElement('direccionComprador', $campos['infoFactura']['direccionComprador']));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalSinImpuestos', $this->getDecimal($campos['infoFactura']['totalSinImpuestos'], 2)));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalDescuento', $this->getDecimal($campos['infoFactura']['totalDescuento'], 2)));
        $totalConImpuestos = $xmlDocument->createElement('totalConImpuestos');
        $detalles = $campos['infoFactura']["totalConImpuestos"];
        foreach ($detalles as $detalle) {
            $item = $xmlDocument->createElement('totalImpuesto');
            $item->appendChild($xmlDocument->createElement('codigo', $detalle["codigo"]));
            $item->appendChild($xmlDocument->createElement('codigoPorcentaje', $detalle["codigoPorcentaje"]));
            $item->appendChild($xmlDocument->createElement('descuentoAdicional', $detalle["descuentoAdicional"]));
            $item->appendChild($xmlDocument->createElement('baseImponible', $detalle["baseImponible"]));
            $item->appendChild($xmlDocument->createElement('valor', $this->getDecimal($detalle["valor"], 2)));
            $totalConImpuestos->appendChild($item);
        }
        $nodoDetalle->appendChild($totalConImpuestos);
        $nodoDetalle->appendChild($xmlDocument->createElement('propina', $this->getDecimal($campos['infoFactura']['propina'], 2)));
        $nodoDetalle->appendChild($xmlDocument->createElement('importeTotal', $this->getDecimal($campos['infoFactura']['importeTotal'], 2)));
        $nodoDetalle->appendChild($xmlDocument->createElement('moneda', $moneda));
        $pagos = $campos['infoFactura']['pagos'];
        $nodoDetalle->appendChild($this->formaPago($xmlDocument, $campos));
        if ($campos['infoFactura']['valorRetIva']) $nodoDetalle->appendChild($xmlDocument->createElement('valorRetIva', $campos['infoFactura']['valorRetIva']));
        if ($campos['infoFactura']['valorRetRenta']) $nodoDetalle->appendChild($xmlDocument->createElement('valorRetRenta', $campos['infoFactura']['valorRetRenta']));
        return $nodoDetalle;
    }
    public function getInfoNotaCredito($xmlDocument, $campos)
    {
        $moneda = 'DOLAR';
        $nodoDetalle = $xmlDocument->createElement('infoNotaCredito');
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision',  $this->getDate($campos['infoNotaCredito']['fechaEmision'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoNotaCredito']['dirEstablecimiento']));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionComprador', $campos['infoNotaCredito']['tipoIdentificacionComprador']));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialComprador', $campos['infoNotaCredito']['razonSocialComprador']));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionComprador', $campos['infoNotaCredito']['identificacionComprador']));
        if ($campos['infoNotaCredito']['contribuyenteEspecial']) $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos['infoNotaCredito']['contribuyenteEspecial']));
        $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos['infoNotaCredito']['obligadoContabilidad']));
        if ($campos['infoNotaCredito']['rise']) $nodoDetalle->appendChild($xmlDocument->createElement('rise', $campos['infoNotaCredito']['rise']));
        $nodoDetalle->appendChild($xmlDocument->createElement('codDocModificado', $campos['infoNotaCredito']['codDocModificado']));
        $nodoDetalle->appendChild($xmlDocument->createElement('numDocModificado', $campos['infoNotaCredito']['numDocModificado']));
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmisionDocSustento', $this->getDate($campos['infoNotaCredito']['fechaEmisionDocSustento'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalSinImpuestos', $campos['infoNotaCredito']['totalSinImpuestos']));
        $nodoDetalle->appendChild($xmlDocument->createElement('valorModificacion', $campos['infoNotaCredito']['valorModificacion']));
        $nodoDetalle->appendChild($xmlDocument->createElement('moneda', $moneda));
        $totalConImpuestos = $campos['infoNotaCredito']['totalConImpuestos'];
        $nodoDetalle->appendChild($this->totalConImpuestos($xmlDocument, $totalConImpuestos));
        $nodoDetalle->appendChild($xmlDocument->createElement('motivo', $campos['infoNotaCredito']['motivo']));
        return $nodoDetalle;
    }
    public function getInfoNotaDebito($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('infoNotaDebito');
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $campos['infoNotaDebito']['fecha_emision']));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoNotaDebito']['dirEstablecimiento']));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionComprador', $campos['infoNotaDebito']['tipoIdentificacionComprador']));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialComprador', $campos['infoNotaDebito']['razonSocialComprador']));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionComprador', $campos['infoNotaDebito']['identificacionComprador']));
        if ($campos['infoNotaDebito']['contribuyenteEspecial']) $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos['infoNotaDebito']['contribuyenteEspecial']));
        $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos['infoNotaDebito']['obligadoContabilidad']));
        $nodoDetalle->appendChild($xmlDocument->createElement('codDocModificado', $this->getDate($campos['infoNotaDebito']['codDocModificado'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('numDocModificado', $this->getDate($campos['infoNotaDebito']['numDocModificado'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmisionDocSustento', $campos['infoNotaDebito']['fechaEmisionDocSustento']));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalSinImpuestos', $campos['infoNotaDebito']['totalSinImpuestos']));
        $impuestos = $campos['infoNotaDebito']['impuestos'];
        $nodoDetalle->appendChild('impuestos', $this->getImpuestos($xmlDocument, $impuestos));
        $pagos = $campos['infoNotaDebito']['pagos'];
        $nodoDetalle->appendChild('Pagos', $this->formaPago($xmlDocument, $pagos));

        $nodoDetalle->appendChild($xmlDocument->createElement('motivo', $campos['infoNotaDebito']['motivo']));
        return $nodoDetalle;
    }
    public function getInfoCompRetencion($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('infoCompRetencion');
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $this->getDate($campos['infoCompRetencion']['fechaEmision'])));
        if ($campos['infoCompRetencion']['dirEstablecimiento']) $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoCompRetencion']['dirEstablecimiento']));
        if (isset($campos['infoCompRetencion']['contribuyenteEspecial'])) {
            if ($campos['infoCompRetencion']['contribuyenteEspecial'] != '08') {
                //$nodoDetalle->appendChild($xmlDocument->createElement('tipoSujetoRetenido', '03'));
            }
        }
        if ($campos['infoCompRetencion']['obligadoContabilidad'])
            $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', strtoupper($campos['infoCompRetencion']['obligadoContabilidad'])));
        if ($campos['infoCompRetencion']['tipoIdentificacionSujetoRetenido']) {
            $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionSujetoRetenido', $campos['infoCompRetencion']['tipoIdentificacionSujetoRetenido']));
            if ($campos['infoCompRetencion']['tipoIdentificacionSujetoRetenido'] == '08') {
                $nodoDetalle->appendChild($xmlDocument->createElement('tipoSujetoRetenido', '01'));
            }
        }
        if (isset($campos['infoCompRetencion']['parteRel']))
            $nodoDetalle->appendChild($xmlDocument->createElement('parteRel', $campos['infoCompRetencion']['parteRel']));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialSujetoRetenido', $campos['infoCompRetencion']['razonSocialSujetoRetenido']));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionSujetoRetenido', $campos['infoCompRetencion']['identificacionSujetoRetenido']));
        $nodoDetalle->appendChild($xmlDocument->createElement('periodoFiscal', $this->getDatePeriodoFiscal($campos['infoCompRetencion']['periodoFiscal'])));
        return $nodoDetalle;
    }
    public function getInfoLiquidacionCompra($xmlDocument, $campos)
    {
        $moneda = 'DOLAR';
        $nodoDetalle = $xmlDocument->createElement('infoLiquidacionCompra');
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $this->getDate($campos['infoLiquidacionCompra']['fechaEmision'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoLiquidacionCompra']['dirEstablecimiento']));
        if ($campos['infoLiquidacionCompra']['contribuyenteEspecial']) $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos['infoLiquidacionCompra']['contribuyenteEspecial']));
        $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos['infoLiquidacionCompra']['obligadoContabilidad']));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionProveedor', $campos['infoLiquidacionCompra']['tipoIdentificacionProveedor']));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialProveedor', $campos['infoLiquidacionCompra']['razonSocialProveedor']));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionProveedor', $campos['infoLiquidacionCompra']['identificacionProveedor']));
        if ($campos['infoLiquidacionCompra']['direccionProveedor']) $nodoDetalle->appendChild($xmlDocument->createElement('direccionProveedor', $campos['infoLiquidacionCompra']['direccionProveedor']));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalSinImpuestos', $this->getDecimal($campos['infoLiquidacionCompra']['totalSinImpuestos'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalDescuento', $this->getDecimal($campos['infoLiquidacionCompra']['totalDescuento'])));
        if ($campos['infoLiquidacionCompra']['codDocReembolso'] == '41') {
            $nodoDetalle->appendChild($xmlDocument->createElement('codDocReembolso', $campos['infoLiquidacionCompra']['codDocReembolso']));
            $nodoDetalle->appendChild($xmlDocument->createElement('totalComprobantesReembolso', $campos['infoLiquidacionCompra']['totalComprobantesReembolso']));
            $nodoDetalle->appendChild($xmlDocument->createElement('totalBaseImponibleReembolso', $campos['infoLiquidacionCompra']['totalBaseImponibleReembolso']));
            $nodoDetalle->appendChild($xmlDocument->createElement('totalImpuestoReembolso', $campos['infoLiquidacionCompra']['totalImpuestoReembolso']));
        }
        $totalConImpuestos = $campos['infoLiquidacionCompra']['totalConImpuestos'];
        $nodoDetalle->appendChild($this->totalConImpuestos($xmlDocument, $totalConImpuestos));
        $nodoDetalle->appendChild($xmlDocument->createElement('importeTotal', $this->getDecimal($campos['infoLiquidacionCompra']['importeTotal'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('moneda', $moneda));
        $pagos = $campos['infoLiquidacionCompra']['pagos'];
        $nodoDetalle->appendChild($this->formaPagoLiquidacion($xmlDocument, $pagos));
        return $nodoDetalle;
    }
    public function totalConImpuestos($xmlDocument, $totalConImpuestos)
    {
        $nodoTotalConImpuestos = $xmlDocument->createElement('totalConImpuestos');
        foreach ($totalConImpuestos as $totalImpuesto) {
            $nodoTotalImpuesto = $xmlDocument->createElement('totalImpuesto');
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('codigo', $totalImpuesto["codigo"]));
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('codigoPorcentaje', $totalImpuesto["codigoPorcentaje"]));
            if (isset($totalImpuesto["descuentoAdicional"])) {
                $nodoTotalImpuesto->appendChild($xmlDocument->createElement('descuentoAdicional', $totalImpuesto["descuentoAdicional"]));
            }
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('baseImponible', $totalImpuesto["baseImponible"]));
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('valor', $this->getDecimal($totalImpuesto["valor"], '2')));
            $nodoTotalConImpuestos->appendChild($nodoTotalImpuesto);
        }
        return $nodoTotalConImpuestos;
    }
    public function getReembolsos($xmlDocument, $reembolsos)
    {
        $nodoReembolsos = $xmlDocument->createElement('reembolsos');
        foreach ($reembolsos as $reembolso) {
            $nodoReembolso = $xmlDocument->createElement('reembolsoDetalle');
            $nodoReembolso->appendChild($xmlDocument->createElement('tipoIdentificacionProveedorReembolso', $reembolso['tipoIdentificacionProveedorReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('identificacionProveedorReembolso', $this->getDecimal($reembolso['identificacionProveedorReembolso'])));
            $nodoReembolso->appendChild($xmlDocument->createElement('codPaisPagoProveedorReembolso', $reembolso['codPaisPagoProveedorReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('tipoProveedorReembolso', $reembolso['tipoProveedorReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('codDocReembolso', $reembolso['codDocReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('estabDocReembolso', $reembolso['estabDocReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('ptoEmiDocReembolso', $reembolso['ptoEmiDocReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('secuencialDocReembolso', $reembolso['secuencialDocReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('fechaEmisionDocReembolso', $reembolso['fechaEmisionDocReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('numeroautorizacionDocReemb', $reembolso['numeroautorizacionDocReemb']));
            $nodoReembolso->appendChild($this->getDetallesImpuestos($xmlDocument, $reembolso['detalleImpuestos']));
            $nodoReembolsos->appendChild($nodoReembolso);
        }
        return $nodoReembolsos;
    }
    public function getMaquinaFiscal($xmlDocument, $maquinaFiscal)
    {
        $nodoMaquinaFiscal = $xmlDocument->createElement('maquinaFiscal');
        $nodoMaquinaFiscal->appendChild($xmlDocument->createElement('marca', $maquinaFiscal['marca']));
        $nodoMaquinaFiscal->appendChild($xmlDocument->createElement('modelo', $this->getDecimal($maquinaFiscal['modelo'])));
        $nodoMaquinaFiscal->appendChild($xmlDocument->createElement('serie', $maquinaFiscal['serie']));
        return $nodoMaquinaFiscal;
    }
    public function getDetallesImpuestos($xmlDocument, $detalles) //Liquidacion compra
    {
        $nodoDetallesImpuestos = $xmlDocument->createElement('detalleImpuestos');
        foreach ($detalles as $detalle) {
            $detalleImpuesto = $xmlDocument->createElement('detalleImpuesto');
            $detalleImpuesto->appendChild($xmlDocument->createElement('codigo', $detalle["codigo"]));
            $detalleImpuesto->appendChild($xmlDocument->createElement('codigoPorcentaje', $detalle["codigoPorcentaje"]));
            $detalleImpuesto->appendChild($xmlDocument->createElement('tarifa', $detalle["tarifa"]));
            $detalleImpuesto->appendChild($xmlDocument->createElement('baseImponible', $detalle["baseImponible"]));
            $detalleImpuesto->appendChild($xmlDocument->createElement('impuestoReembolso', $this->getDecimal($detalle["impuestoReembolso"], '2')));
            $nodoDetallesImpuestos->appendChild($detalleImpuesto);
        }
        return $nodoDetallesImpuestos;
    }
    public function formaPago($xmlDocument, $detalles)
    {
        $nodeFormaPago = $xmlDocument->createElement('pagos');
        foreach ($detalles['infoFactura']['pagos'] as $row) {
            $item = $xmlDocument->createElement('pago');
            $item->appendChild($xmlDocument->createElement('formaPago', $row["formaPago"]));
            $item->appendChild($xmlDocument->createElement('total', $row['total']));
            $item->appendChild($xmlDocument->createElement('plazo', $row['plazo']));
            $item->appendChild($xmlDocument->createElement('unidadTiempo', $row["unidadTiempo"]));
            $nodeFormaPago->appendChild($item);
        }
        return $nodeFormaPago;
    }
    public function formaPagoLiquidacion($xmlDocument, $detalles)
    {
        $nodeFormaPago = $xmlDocument->createElement('pagos');
        foreach ($detalles as $row) {
            $item = $xmlDocument->createElement('pago');
            $item->appendChild($xmlDocument->createElement('formaPago', $row["formaPago"]));
            $item->appendChild($xmlDocument->createElement('total', $row['total']));
            $item->appendChild($xmlDocument->createElement('plazo', $row['plazo']));
            $item->appendChild($xmlDocument->createElement('unidadTiempo', $row["unidadTiempo"]));
            $nodeFormaPago->appendChild($item);
        }
        return $nodeFormaPago;
    }
    public function consultarEstadoSRI($idEmpresa)
    {
        $url = '';
        $infoTributaria = De_Empresa::where('id_empresa', $idEmpresa)->first();
        $tipoAmbiente = $infoTributaria->ambiente;
        $estadoSri = 1;
        switch ($tipoAmbiente) {
            case 1:
                $url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl'; //'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl';
                break;
            case 2:
                $url = 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl'; //'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl';
                break;
        }
        $emision = new EmisionDocumentosController;
        $estadoSri = $emision->pingDomain($url);
        $estado = De_Estado_Sri::getEstado();
        if ($estadoSri != $estado) {
            De_Estado_Sri::updateEstado($estadoSri);
        }
        return $estadoSri;
    }
    private static function validarCedula($cedula)
    {
        if ((strlen($cedula) == 10) || (strlen($cedula) == 13)) {
            $numero           = $cedula;
            $suma             = 0;
            $residuo          = 0;
            $pri              = false;
            $pub              = false;
            $nat              = false;
            $numeroProvincias = 24;
            $modulo           = 11;

            /* Verifico que el campo no contenga letras */
            $ok = 1;
            $i  = substr($numero, 0, 2);
            if ($i > $numeroProvincias) {
                return false;
            }

            /* Aqui almacenamos los digitos de la cedula en variables. */
            $d1  = substr($numero, 0, 1);
            $d2  = substr($numero, 1, 1);
            $d3  = substr($numero, 2, 1);
            $d4  = substr($numero, 3, 1);
            $d5  = substr($numero, 4, 1);
            $d6  = substr($numero, 5, 1);
            $d7  = substr($numero, 6, 1);
            $d8  = substr($numero, 7, 1);
            $d9  = substr($numero, 8, 1);
            $d10 = substr($numero, 9, 1);

            /* El tercer digito es: */
            /* 9 para sociedades privadas y extranjeros */
            /* 6 para sociedades $publicas */
            /* menor que 6 (0,1,2,3,4,5) para personas $naturales */

            if ($d3 == 7 || $d3 == 8) {
                return false;
            }

            /* Solo para personas $naturales ($modulo 10) */
            if ($d3 < 6) {
                $nat = true;
                $p1  = $d1 * 2;
                if ($p1 >= 10) {
                    $p1 -= 9;
                }

                $p2 = $d2 * 1;
                if ($p2 >= 10) {
                    $p2 -= 9;
                }
                $p3 = $d3 * 2;
                if ($p3 >= 10) {
                    $p3 -= 9;
                }
                $p4 = $d4 * 1;
                if ($p4 >= 10) {
                    $p4 -= 9;
                }
                $p5 = $d5 * 2;
                if ($p5 >= 10) {
                    $p5 -= 9;
                }
                $p6 = $d6 * 1;
                if ($p6 >= 10) {
                    $p6 -= 9;
                }
                $p7 = $d7 * 2;
                if ($p7 >= 10) {
                    $p7 -= 9;
                }

                $p8 = $d8 * 1;
                if ($p8 >= 10) {
                    $p8 -= 9;
                }

                $p9 = $d9 * 2;
                if ($p9 >= 10) {
                    $p9 -= 9;
                }

                $modulo = 10;
            }

            /* Solo para sociedades $publicas ($modulo 11) */
            /* Aqui el digito verficador esta en la posicion 9, en las otras 2 en la pos. 10 */ else if ($d3 == 6) {
                $pub = true;
                $p1  = $d1 * 3;
                $p2  = $d2 * 2;
                $p3  = $d3 * 7;
                $p4  = $d4 * 6;
                $p5  = $d5 * 5;
                $p6  = $d6 * 4;
                $p7  = $d7 * 3;
                $p8  = $d8 * 2;
                $p9  = 0;
            }

            /* Solo para entidades privadas ($modulo 11) */ else if ($d3 == 9) {
                $pri = true;
                $p1  = $d1 * 4;
                $p2  = $d2 * 3;
                $p3  = $d3 * 2;
                $p4  = $d4 * 7;
                $p5  = $d5 * 6;
                $p6  = $d6 * 5;
                $p7  = $d7 * 4;
                $p8  = $d8 * 3;
                $p9  = $d9 * 2;
            }

            $suma    = $p1 + $p2 + $p3 + $p4 + $p5 + $p6 + $p7 + $p8 + $p9;
            $residuo = $suma % $modulo;

            /* Si $residuo=0, dig.ver.=0, caso contrario 10 - $residuo*/
            $digitoVerificador = $residuo == 0 ? 0 : $modulo - $residuo;

            /* ahora comparamos el elemento de la posicion 10 con el dig. ver.*/
            if ($pub == true) {
                if ($digitoVerificador != $d9) {
                    return false;
                }
                /* El ruc de las empresas del sector $publico terminan con 0001*/
                if (substr($numero, 9, 4) != '0001') {
                    return false;
                }
            } else if ($pri == true) {
                if ($digitoVerificador != $d10) {
                    return false;
                }
                if (substr($numero, 10, 3) != '001') {
                    return false;
                }
            } else if ($nat == true) {
                if ($digitoVerificador != $d10) {
                    return false;
                }
                if (strlen($numero) > 10 && substr($numero, 10, 3) != '001') {
                    return false;
                }
            }
        }
        return true;
    }
}
