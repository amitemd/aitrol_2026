<?php

namespace Sis_medico\v3\contable\sri;

use DOMDocument;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Sis_medico\v3\contable\Clientes;
use Sis_medico\v3\contable\CodigoImpuestos;
use Sis_medico\v3\contable\Compras;
use Sis_medico\v3\contable\Configuraciones;
use Sis_medico\v3\contable\DeEmpresas;
use Sis_medico\v3\contable\DetalleCompras;
use Sis_medico\v3\contable\DetalleRetenciones;
use Sis_medico\v3\contable\DetalleVenta;
use Sis_medico\v3\contable\DocumentosElectronicos;
use Sis_medico\v3\contable\Empresas;
use Sis_medico\v3\contable\FormaPago;
use Sis_medico\v3\contable\LogErrores;
use Sis_medico\v3\contable\MaestroDocumentos;
use Sis_medico\v3\contable\PorcentajeRetenciones;
use Sis_medico\v3\contable\Proveedor;
use Sis_medico\v3\contable\Sucursales;
use Sis_medico\v3\contable\Utilidades;
use Sis_medico\v3\contable\Ventas;
use SoapClient;

class Comprobante extends Model
{
    public static $tiposDocumentos = array('01' => 'factura', '07' => 'comprobanteRetencion', '04' => 'notaCredito', '03' => 'notaDebito', '06' => 'guiaRemision');
    const TIPO_AMBIENTE_PRUEBAS = 1;
    const TIPO_AMBIENTE_PRODUCCION = 2;
    const TIPO_EMISION_NORMAL = 1;
    const TIPO_EMISION_CONTINGENCIA = 2;
    private $model = null;
    private $tipoDocumento = null;
    private $tipoAmbiente = null;
    private $tipoEmision = null;
    private $cliente = null;
    private $proveedor = null;
    private $cod_doc = null;
    private $sucursal = null;
    private $empresa = null;
    private $deEmpresa = null;
    private $usuario = null;
    private $versionDocumento = null;
    private $motivo = null;
    private $tipoDoc = null;
    public function __construct($model = '', $tipoDocumento = null, $tipoAmbiente = null, $tipoEmision = null, $motivo = null)
    {
        if ($model != '') {
            $this->model = $model;
            $this->empresa = Empresas::find($this->model->id_empresa);
            $this->deEmpresa = DeEmpresas::where('id_empresa', $this->empresa->id)->first();
            $this->sucursal = Sucursales::where('codigo_sucursal', $this->model->sucursal)->where('id_empresa', $this->empresa->id)->first();
            $this->cliente = Clientes::where('identificacion', $this->model->id_cliente)->first();
            $this->tipoAmbiente = $tipoAmbiente;
            $this->tipoEmision = $tipoEmision;
            $this->tipoDoc = $tipoDocumento;
            $tipoDocumento = self::$tiposDocumentos[$tipoDocumento];
            $this->tipoDocumento = $tipoDocumento;
            $this->motivo = $motivo;
            $this->porIva = Configuraciones::getImpuestoIva();
            /*$data['impuestoIvaPorcentaje'] = $arrayImpuestos->iva * 100;
            $data['impuestoIva'] = $arrayImpuestos->iva;
            $data['impuestoIcePorcentaje'] = $arrayImpuestos->ice * 100;
            $data['impuestoIce'] = $arrayImpuestos->ice;*/
        }
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
                    DocumentosElectronicos::where('clave_acceso', $clave)
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
            Utilidades::crearcarpeta($r_);
            $ruta = $r_ . $nombreArchivo . '_' . $estado . '.xml';
            $xml->save($ruta);
            return $estado;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            echo '<br/>' . $ex->getLine();
            return;
        }
    }
    function autorizarSri($comprobante)
    {
        ini_set('max_execution_time', 300);
        $docAnte = simplexml_load_string($comprobante, "SimpleXMLElement", LIBXML_NOCDATA);
        $docAnte = json_encode($docAnte);
        $docAnte = json_decode($docAnte, TRUE);
        sleep(3);
        switch ($docAnte['infoTributaria']['ambiente']) {
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
            'claveAccesoComprobante' => $docAnte['infoTributaria']['claveAcceso']
        );
        $estadoAutorizacion = $this->generarXmlAutorizacion($comprobante);
        echo '<pre>';
        print_r($estadoAutorizacion);
        exit;
        De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
        Ct_Guia_Remision_Cabecera::updateXmlAutorizacion($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id, $docAnte->infoTributaria->claveAcceso, $secuancialDocumento);
        $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
        $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
        return $client->autorizacionComprobante($param);
    }
    function validarSri($comprobante)
    {
        $docAnte = simplexml_load_string($comprobante, "SimpleXMLElement", LIBXML_NOCDATA);
        $docAnte = json_encode($docAnte);
        $docAnte = json_decode($docAnte, TRUE);
        ini_set('max_execution_time', 300);
        $url = "";
        switch ($docAnte['infoTributaria']['ambiente']) {
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
        DocumentosElectronicos::updateRecibidoSri($docAnte['infoTributaria']['claveAcceso'], $result);
        Ventas::updateRecibidoSri($this->model->id);
        return $result;
    }
    function firmarXml($comprobante)
    {
        ini_set('max_execution_time', 300);
        $docAnte = simplexml_load_string($comprobante, "SimpleXMLElement", LIBXML_NOCDATA);
        $docAnte = json_encode($docAnte);
        $docAnte = json_decode($docAnte, TRUE);
        $fileName = ($docAnte['infoTributaria']['claveAcceso']) . "_" . uniqid();
        $filenameXml = tempnam(sys_get_temp_dir(), "");
        $carpeta = base_path() . '/storage/app/facturaelectronica/firmados/' . $docAnte['infoTributaria']['ruc'] . '/' . $this->tipoDocumento;
        Utilidades::crearcarpeta($carpeta);
        $filenameXml .=  $fileName . ".xml";
        if ($comprobante != '') {
            $file = fopen($filenameXml, "w");
            fwrite($file, $comprobante);
            fclose($file);
        }
        $dataDoc = $filenameXml;
        $dataDoc = escapeshellarg($dataDoc);
        $isPathXml = "1";
        $isExite = null;
        $isExite = file_exists(base_path() . '/storage/app/facturaelectronica/p12/' . $docAnte['infoTributaria']['ruc'] . '/' . $this->deEmpresa->ruta_firma) ? true : false;
        $certificado = escapeshellarg(base_path() . '/storage/app/facturaelectronica/p12/' . $docAnte['infoTributaria']['ruc'] . '/' . $this->deEmpresa->ruta_firma);
        if (!$isExite) return null;
        $clave = $this->deEmpresa->clave_firma;
        $clave = escapeshellarg($clave);
        $jar = base_path() . '/CompelJar/dist/CompelJar.jar';
        $jar = escapeshellarg($jar);
        $command = "java -jar $jar $certificado $clave $dataDoc $isPathXml 2>&1";
        $respuestaCmd = exec($command, $output, $return_value);
        $resultado = strpos($respuestaCmd, 'El certificado firmante ha caducado');
        if ($resultado !== FALSE) {
            echo 'El certificado firmante ha caducado: ' . $docAnte['infoTributaria']['ruc'] . ' para firmar: ' . $this->tipoDocumento . ' - No.: ' . $docAnte['infoTributaria']['secuencial']  . ' <br/>';
            $mns = 'El certificado firmante ha caducado: ' . $docAnte['infoTributaria']['ruc'];
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
            $xmlFirmado = simplexml_load_string($response['comprobante']);
            $r_ = base_path() . '/storage/app/facturaelectronica/firmados/' . $docAnte['infoTributaria']['ruc'] . '/' . $this->tipoDocumento . '/';
            Utilidades::crearcarpeta($r_);
            $xmlFirmado->saveXML($r_ . $this->tipoDocumento . '-' . $docAnte['infoTributaria']['estab'] . '-' .  $docAnte['infoTributaria']['ptoEmi'] . '-' . str_pad($docAnte['infoTributaria']['secuencial'], 9, 0, STR_PAD_LEFT) . '.xml');
            if ($response['isValido'] == 1) {
                DocumentosElectronicos::updateXmlFirmado($docAnte['infoTributaria']['claveAcceso']);
                Ventas::updateXmlFirmado($this->model->id);
            } else {
                $arrayLog = [
                    'id_de_documentos_electronicos' => $id_doc,
                    'descripcion_error' => json_encode($result),
                    'id_usuariomod' => $idUsuario,
                    'id_usuariocrea' =>  $idUsuario,
                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                ];
                LogErrores::setErrorLog($arrayLog);
            }
        }
        return $response;
    }
    function generarXml()
    {
        $campos = $this->obtenerCampos();
        $respuestaError = [];
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $root = $xml->createElement($this->tipoDocumento);
        $root->setAttribute('id', 'comprobante');
        switch ($this->tipoDocumento) {
            case 'factura':
                $this->versionDocumento = '2.1.0';
                $root->setAttribute('version', '2.1.0');
                $infoTributaria = $this->getInfoTributaria($xml, $campos);
                $root->appendChild($infoTributaria);
                $infoFactura = $this->getInfoFactura($xml, $campos);
                $root->appendChild($infoFactura);
                $detalles = $this->getDetalles($xml, $campos);
                $root->appendChild($detalles);
                if (isset($campos["retenciones"]) && count($campos["retenciones"])) {
                    $retenciones = $this->getRetencionesFactura($xml, $campos);
                    $root->appendChild($retenciones);
                }
                $infoAdicional = $this->getInfoAdicional($xml, $campos);
                $root->appendChild($infoAdicional);
                break;
            case 'comprobanteRetencion':
                $root->setAttribute('version', '2.0.0');
                $infoTributaria = $this->getInfoTributaria($xml, $campos);
                $root->appendChild($infoTributaria);
                $infoCompRetencion = $this->getInfoCompRetencion($xml, $campos);
                $root->appendChild($infoCompRetencion);
                $docsSustento = $this->getDocsSustento($xml, $campos);
                $root->appendChild($docsSustento);

                $xml->appendChild($root);
                $xml->formatOutput = true;
                $comprobante = $xml->saveXML();
                $xmlData = simplexml_load_string($comprobante, "SimpleXMLElement", LIBXML_NOCDATA);
                echo $comprobante;
                exit;

                $infoAdicional = $this->getInfoAdicional($xml, $campos);
                $root->appendChild($infoAdicional);
                break;
            case 'notaCredito':
                $root->setAttribute('version', '1.1.0');
                $infoTributaria = $this->getInfoTributaria($xml, $campos);
                $root->appendChild($infoTributaria);
                $infoNotaCredito = $this->getInfoNotaCredito($xml, $campos);
                $root->appendChild($infoNotaCredito);
                $detalles = $this->getDetallesNotaCredito($xml, $campos);
                $root->appendChild($detalles);
                $infoAdicional = $this->getInfoAdicional($xml, $campos);
                $root->appendChild($infoAdicional);
                break;
            case 'notaDebito':
                $root->setAttribute('version', '1.0.0');
                $infoTributaria = $this->getInfoTributaria($xml, $campos);
                $root->appendChild($infoTributaria);
                $infoNotaDebito = $this->getInfoNotaDebito($xml, $campos);
                $root->appendChild($infoNotaDebito);
                $motivos = $this->getMotivos($xml, $campos);
                $root->appendChild($motivos);
                $infoAdicional = $this->getInfoAdicional($xml, $campos);
                $root->appendChild($infoAdicional);
                break;
            case 'guiaRemision':
                $root->setAttribute('version', '1.0.0');
                $infoTributaria = $this->getInfoTributaria($xml, $campos);
                $root->appendChild($infoTributaria);
                $infoGuiaRemision = $this->getInfoGuiaRemision($xml, $campos);
                $root->appendChild($infoGuiaRemision);
                $destinatarios = $this->getDestinatarios($xml, $campos);
                $root->appendChild($destinatarios);
                $infoAdicional = $this->getInfoAdicional($xml, $campos);
                $root->appendChild($infoAdicional);
                break;
        }
        $xml->appendChild($root);
        $xml->formatOutput = true;
        $comprobante = $xml->saveXML();
        $xmlData = simplexml_load_string($comprobante, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xmlData);
        $array = json_decode($json, TRUE);
        $firma['ruta_firma'] = $this->deEmpresa->ruta_firma;
        $firma['clave_firma'] = $this->deEmpresa->clave_firma;
        $array['datosfirma'] = $firma;
        $errores = $this->validarData($array);
        if (count($errores) > 0) {
            $respuestaError['isValidoXsd'] = 0;
            $respuestaError['mensajes'] = $errores;
            $arrayDocElec = [
                'id_de_pasos' => 7,
                'infoTributaria' => json_encode($array['infoTributaria']),
                'infoFactura' => json_encode($array['infoFactura']),
                'detalles' => json_encode($array['detalles']),
                'infoAdicional' => json_encode($array['infoAdicional']),
                'establecimiento' => $array['infoTributaria']['estab'],
                'emision' => $array['infoTributaria']['ptoEmi'],
                'secuencial' => $array['infoTributaria']['secuencial'],
                'ruc_receptor' => $array['infoFactura']['identificacionComprador'],
                'ruc_emisor' => $array['infoTributaria']['ruc'],
                'id_usuariomod' => $this->model->id_usuariomod,
                'id_usuariocrea' =>  $this->model->id_usuariocrea,
                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                'clave_acceso' => $array['infoTributaria']['claveAcceso'],
                'id_maestro_documento' => MaestroDocumentos::getIdMaestrosDocumentos($array['infoTributaria']['codDoc']),
                'id_documento' => $this->model->id,
            ];
            $id_doc = DocumentosElectronicos::setDocElectronico($arrayDocElec, 7);
            $arrayLog = [
                'id_de_documentos_electronicos' => $id_doc,
                'descripcion_error' => json_encode($errores),
                'id_usuariomod' => $this->model->id_usuariomod,
                'id_usuariocrea' =>  $this->model->id_usuariocrea,
                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                'id_documento' => $this->model->id,
            ];
            LogErrores::setErrorLog($arrayLog);
            Ventas::updateSinGenerarXML($this->model->id);
            return $respuestaError;
        } else {
            $arrayLogError = [
                'id_de_pasos' => 1,
                'infoTributaria' => json_encode($array['infoTributaria']),
                'infoFactura' => json_encode($array['infoFactura']),
                'detalles' => json_encode($array['detalles']),
                'infoAdicional' => json_encode($array['infoAdicional']),
                'establecimiento' => $array['infoTributaria']['estab'],
                'emision' => $array['infoTributaria']['ptoEmi'],
                'secuencial' => $array['infoTributaria']['secuencial'],
                'ruc_receptor' =>  $array['infoFactura']['identificacionComprador'],
                'ruc_emisor' => $array['infoTributaria']['ruc'],
                'id_usuariomod' => $this->model->id_usuariomod,
                'id_usuariocrea' =>  $this->model->id_usuariocrea,
                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                'clave_acceso' => $array['infoTributaria']['claveAcceso'],
                'id_maestro_documento' => MaestroDocumentos::getIdMaestrosDocumentos($array['infoTributaria']['codDoc']),
                'id_documento' => $this->model->id
            ];
            $id_doc = DocumentosElectronicos::setDocElectronico($arrayLogError, 1);
            Ventas::updateGenerarXML($this->model->id, $array['infoTributaria']['claveAcceso']);
        }
        $validacionXSD = $this->validarXmlToXsd($comprobante);
        Ventas::updateValidacionXSD($this->model->id);
        if ($validacionXSD['isValidoXsd'] == 0) {
            $arrayLog = [
                'id_de_documentos_electronicos' => $id_doc,
                'descripcion_error' => json_encode($validacionXSD),
                'id_usuariomod' => $this->model->id_usuariomod,
                'id_usuariocrea' =>  $this->model->id_usuariocrea,
                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
            ];
            LogErrores::setErrorLog($arrayLog);

            return $validacionXSD;
        }
        if ($this->tipoDocumento == 'factura') {
            $carpeta = storage_path() . '/app/facturaelectronica/sinfirmar/' . trim($this->empresa->id) . '/factura/';
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }
            $carpeta = $carpeta . 'Factura-' . $campos['estab'] . '-' . $campos['ptoEmi'] . '-' . str_pad($campos['secuencial'], 9, '0', STR_PAD_LEFT) . '.xml';
        } elseif ($this->tipoDocumento == 'notaCredito') {
            $carpeta = 'NotaCreditoE/' . str_replace('.', '', str_replace(' ', '', strtolower($this->empresa->empresa))) . '/';
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }
            $carpeta = $carpeta . 'NotaCredito-' . $campos['estab'] . '-' . $campos['ptoEmi'] . '-' . str_pad($campos['secuencial'], 9, '0', STR_PAD_LEFT) . '.xml';
        } elseif ($this->tipoDocumento == 'guiaRemision') {
            $carpeta = 'GuiaRemisionE/' . str_replace('.', '', str_replace(' ', '', strtolower($this->empresa->empresa))) . '/';
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }
            $carpeta = $carpeta . 'GuiaRemision-' . $campos['estab'] . '-' . $campos['ptoEmi'] . '-' . str_pad($campos['secuencial'], 9, '0', STR_PAD_LEFT) . '.xml';
        } elseif ($this->tipoDocumento == 'comprobanteRetencion') {
            $carpeta = 'RetencionE/' . str_replace('.', '', str_replace(' ', '', strtolower($this->empresa->empresa))) . '/';
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }
            $carpeta = $carpeta . 'Retencion-' . $campos['estab'] . '-' . $campos['ptoEmi'] . '-' . str_pad($campos['secuencial'], 9, '0', STR_PAD_LEFT) . '.xml';
        }
        $flujo = fopen($carpeta, 'w');
        fputs($flujo, $comprobante);
        fclose($flujo);
        $xml->save($carpeta);
        return $comprobante;
    }
    public function getDocsSustento($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('docsSustento');
        $docsSustento = $campos["docsSustento"];
        foreach ($docsSustento as $docSustento) {
            echo '<pre>';print_r($docSustento);exit;
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
    private function obtenerCampos()
    {
        //echo '<pre>';print_r($this->model);exit;
        $campos_['ambiente'] = $this->tipoAmbiente;
        $campos_['tipoEmision'] = $this->tipoEmision;
        $campos_['razonSocial'] = $this->empresa->razonsocial;
        $campos_['nombreComercial'] = $this->empresa->nombrecomercial;
        $campos_['ruc'] = $this->empresa->id;
        $campos_['claveAcceso'] = $this->model->clave_acceso;
        $campos_['codDoc'] = $this->tipoDoc;
        $campos_['estab'] = $this->model->sucursal;
        $campos_['ptoEmi'] = $this->model->punto_emision;
        $campos_['secuencial'] = isset($this->model->numero) ? $this->model->numero : $this->model->secuencia;
        $campos_['dirMatriz'] = $this->empresa->direccion;
        $campos['id_empresa'] = $this->empresa->id;
        $campos['direccion'] = $this->sucursal->direccion_sucursal;
        $campos_['dirEstablecimiento'] = $this->sucursal->direccion_sucursal;
        $campos['telefono'] = $this->sucursal->telefono_sucursal;
        $campos['email'] = $this->sucursal->email_sucursal;
        $campos['infoTributaria'] = $campos_;
        switch ($this->tipoDocumento) {
            case 'factura':
                $campos['fechaEmision'] = trim(str_replace(' ', '', $this->getDate($this->model->fecha)));
                $this->obtenerFactura($campos);
                break;
            case 'comprobanteRetencion':
                $this->obtenerRetencion($campos);
                break;
            case 'notaCredito':
                $this->obtenerNotaCredito($campos);
                break;
            case 'notaDebito':
                $this->obtenerNotaDebito($campos);
                break;
            case 'guiaRemision':
                $this->obtenerGuiaRemision($campos);
                break;
        }
        return $campos;
    }
    private function getDate($valor)
    {
        if (trim($valor) == "") return "";
        $result = date('d/m/Y', strtotime($valor));
        return $result;
    }
    private function getBool($valor)
    {
        $result = $valor == true || $valor == 1 || $valor == "1" ? "SI" : "NO";
        return $result;
    }
    private function getDecimal($valor = 0, $digitos = 2, $formato = false)
    {
        if (trim($valor) == "") $valor = 0;
        $resultado = round($valor, $digitos, PHP_ROUND_HALF_UP);
        $resultado = number_format($resultado, $digitos, ".", $formato ? "," : "");
        return $resultado;
    }
    private function tipoImpuesto($id)
    {
        if ($id == -1 || $id == -0 || $id == null)
            return null;
        return CodigoImpuestos::where('id', $id)->first();
    }
    private function isExisteImpuesto($listado, $valor)
    {
        $idx = -1;
        $isExiste = false;
        foreach ($listado as $item) {
            $idx++;
            if (@$item["codigo"] == $valor["codigo"] && @$item["codigoPorcentaje"] == $valor["codigoPorcentaje"]) {
                $isExiste = true;
                break;
            }
        }
        if (!$isExiste) $idx = -1;
        return $idx;
    }
    private function obtenerRetencion(&$campos)
    {
        //echo '<pre>';print_r($this->model);exit;
        $email = '';
        $telefono = '';
        $direccion = '';
        $compras = Compras::where('id', $this->model->id_compra)->first();
        $infoCompRetencion['fechaEmision'] =  date('d/m/Y', strtotime($this->model->fecha));
        $infoCompRetencion['dirEstablecimiento'] =  $this->sucursal->direccion_sucursal;
        if ($this->deEmpresa->contribuyente_especial != 'NO')
            $infoCompRetencion['contribuyenteEspecial'] = $this->deEmpresa->contribuyente_especial;
        $infoCompRetencion['obligadoContabilidad'] = $this->getBool($this->deEmpresa->contabilidad);
        $proveedor = Proveedor::where('id', $this->model->id_proveedor)->first();
        $infoCompRetencion['tipoIdentificacionSujetoRetenido'] = str_pad($proveedor->tipo, 2, 0, STR_PAD_LEFT);
        if ($infoCompRetencion['tipoIdentificacionSujetoRetenido'] == '08') {
            $infoCompRetencion['tipoSujetoRetenido'] = '03'; //compras - identificacion del exterior
        }
        $infoCompRetencion['parteRel'] = 'NO';
        $infoCompRetencion['razonSocialSujetoRetenido'] = str_replace('&', 'Y', $proveedor->razonsocial);
        $infoCompRetencion['identificacionSujetoRetenido'] = $proveedor->id;
        $infoCompRetencion['periodoFiscal'] = date('Y-m-d', strtotime($this->model->fecha));
        $campos['infoCompRetencion'] = $infoCompRetencion;

        $docsSustento = [];
        $docSustento = [];
        $conDetalle = 0;
        $docSustento['docSustento'] = '01';
        if ($this->tipoDoc != '03')
            $docSustento['codDocSustento'] = '18';
        else
            $docSustento['codDocSustento'] = '03';
        $datoDocSus = explode('-', $this->model->nro_comprobante);
        $numDocSus = $datoDocSus[0] . $datoDocSus[1] . str_pad((int)$datoDocSus[2], 9, 0, STR_PAD_LEFT);
        $docSustento['numDocSustento'] = $numDocSus;
        $docSustento['fechaEmisionDocSustento'] = $this->model->fecha;
        $docSustento['pagoLocExt'] = '01'; //Pago a residente / Establecimiento permanente  -- Pago a no residente
        if ($docSustento['pagoLocExt'] == '02') {
            $docSustento['tipoRegi'] = '01'; //obligatorio pagoLocExt sea 02/tabla 19 
            $docSustento['paisEfecPago'] = '593'; //obligatorio pagoLocExt sea 02
            $docSustento['aplicConvDobTrib'] = 'NO'; //obligatorio pagoLocExt sea 02
            $docSustento['pagExtSujRetNorLeg'] = 'NO'; //obligatorio aplicConvDobTrib sea NO
            $docSustento['pagoRegFis'] = 'NO'; //obligatorio pagoLocExt sea 02
        }
        $docSustento['totalSinImpuestos'] = $this->getDecimal(($compras->subtotal_0 != null ? $compras->subtotal_0 : 0) + ($compras->subtotal_12 != null ? $compras->subtotal_12 : 0), 2);
        $docSustento['importeTotal'] = $compras->total_final;
        $impuestoDocSustento = [];
        $impuestosDocSustento = [];
        $impuestoDocSustento = [];
        $impuestosDocSustento = [];
        $detalleCompra = DetalleCompras::where('id_ct_compras', $compras->id)->get();
        if (count($detalleCompra) > 0) {
            foreach ($detalleCompra as $key => $impuesto) {
                if ($impuesto->iva == 1) {
                    $impuestoDocSustento['codImpuestoDocSustento'] = '2';
                    $impuestoDocSustento['codigoPorcentaje'] = !Comprobante::validarPeriodo($this->model->fecha) ? 2 : 4;
                    $impuestoDocSustento['baseImponible'] = $impuesto->extendido;
                    $impuestoDocSustento['tarifa'] = !Comprobante::validarPeriodo($this->model->fecha) ? 12 : ($this->porIva->iva * 100);
                    $impuestoDocSustento['valorImpuesto'] = !Comprobante::validarPeriodo($this->model->fecha) ? $this->getDecimal(($impuesto->extendido * 0.12), 2) : $this->getDecimal(($impuesto->extendido * $this->porIva->iva), 2);
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
        $detalleReten = DetalleRetenciones::getDetalles($this->model->id);
        if (count($detalleReten) > 0) {
            $contRet = 0;
            foreach ($detalleReten as $deta) {
                $porcentajeRetencion = PorcentajeRetenciones::where('id', $deta->id_porcentaje)->first([
                    'tipo as codigo_impuesto',
                    'codigo_nombre as codigo',
                    'valor as porcentaje_retencion'
                ]);
                $porRete = $porcentajeRetencion->porcentaje_retencion;
                $porcentajeRetencion = CodigoImpuestos::where('codigo', $porcentajeRetencion->codigo)
                    ->where('porcentaje_retencion', $porcentajeRetencion->porcentaje_retencion)
                    ->first();
                if ($porcentajeRetencion == '') {
                    $porcentajeRetencion = CodigoImpuestos::where('porcentaje_retencion', number_format($porRete, 2))
                        ->first();
                }
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
            $detalleReten = DetalleRetenciones::getDetalles($this->model->id);
            if (count($detalleReten) > 0) {
                foreach ($detalleReten as $deta) {
                    $porcentajeRetencion = PorcentajeRetenciones::where('id', $deta->id_porcentaje)->first();
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
        $campos['docsSustento'] = $docsSustento;
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
        $campos['infoAdicional'] = $informacion_adicional;
        return $campos;
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
    private function obtenerFactura(&$campos)
    {
        $totalIva = 0;
        if ($this->deEmpresa->contribuyente_especial != 'NO')
            $campos['contribuyenteEspecial'] = $this->deEmpresa->contribuyente_especial;
        $tiempocredito = 0;
        $tiempocredito = $this->model->dias_plazo ? $this->model->dias_plazo : 0;
        $campos['fechaEmision'] = $campos['fechaEmision'];
        $campos['obligadoContabilidad'] = $this->getBool($this->deEmpresa->contabilidad);
        $campos['tipoIdentificacionComprador'] = $this->cliente->tipo;
        $campos['guiaRemision'] = isset($campos['guia_remision']) ? $campos['guia_remision'] : '';
        $campos['razonSocialComprador'] = $this->cliente->nombre;
        $campos['identificacionComprador'] = $this->cliente->identificacion;
        $campos['totalSinImpuestos'] = $this->getDecimal(($this->model->subtotal_0 + $this->model->subtotal_12) - $this->model->descuento);
        $campos['totalDescuento'] = $this->getDecimal($this->model->descuento);
        $formaPago = json_decode(json_encode(FormaPago::where('id_ct_ventas', $this->model->id)
            ->join('ct_tipo_pago as tp', 'ct_forma_pago.tipo', 'tp.id')
            ->get([
                'codigo_sri',
                'valor'
            ])
            ->toArray()), true);
        foreach ($formaPago as $key => $row) {
            $formaPago[$key]['plazo'] = $tiempocredito;
            $formaPago[$key]['unidadTiempo'] = 'dias';
        }
        $impuestoIva = [];
        $campos['formaPago'] = $formaPago;
        $detalle = [];
        $campos['totalConImpuestos'] = [];
        $detalleVenta = DetalleVenta::where('id_ct_ventas', $this->model->id)->get();
        if ($detalleVenta->count() > 0) {
            foreach ($detalleVenta as $row) {
                $baseImponible = $this->getDecimal($row->cantidad * $row->precio);
                if ($row->check_iva == 1) {
                    $impuestoIva = $this->tipoImpuesto(1);
                    $detalle['codigo'] = $impuestoIva->codigo_impuesto;
                    $detalle['codigoPorcentaje'] = $impuestoIva->codigo;
                    $detalle['valor'] = $this->getDecimal($this->getDecimal(($row->cantidad * $row->precio) - $row->descuento) * $this->getDecimal($row->porcentaje));
                    $totalIva += $this->getDecimal($detalle['valor']);
                } else {
                    $impuestoIva = $this->tipoImpuesto(2);
                    $detalle['codigo'] = $impuestoIva->codigo_impuesto;
                    $detalle['codigoPorcentaje'] = 0;
                    $detalle['valor'] = $this->getDecimal('0.00');
                }
                $detalle['baseImponible'] = $this->getDecimal(($baseImponible - $campos['totalDescuento']), 2);
                $detalle['descuentoAdicional'] = $this->getDecimal($campos['totalDescuento']);
                $isExiste = $this->isExisteImpuesto($campos['totalConImpuestos'], $detalle);
                if ($isExiste > -1) {
                    $campos['totalConImpuestos'][$isExiste]["descuentoAdicional"] += $detalle['descuentoAdicional'];
                    $campos['totalConImpuestos'][$isExiste]["baseImponible"] += $detalle['baseImponible'];
                    $campos['totalConImpuestos'][$isExiste]["valor"] += $this->getDecimal($detalle['valor']);
                } else {
                    array_push($campos['totalConImpuestos'], $detalle);
                }
            }
            $campos['propina'] = $this->getDecimal($this->model->propina);
            $campos['valor_iva'] = $totalIva;
            $campos['importeTotal'] = $this->getDecimal($campos['totalSinImpuestos'] + $campos['valor_iva']);
            $campos['moneda'] = 'DOLLAR';
            $detalle['codigoPrincipal'] = $row->id_ct_productos;
            $detalle['codigoAuxiliar'] = $row->id_ct_productos;
            $detalle['descripcion'] = $row->nombre;
            $detalle['cantidad'] = $this->getDecimal($row->cantidad);
            $detalle['precioUnitario'] = $this->getDecimal($row->precio);
            $detalle['descuento'] = $this->getDecimal($row->descuento);
            $detalle['precioTotalSinImpuesto'] = $this->getDecimal($row->cantidad * $row->precio - ($row->descuento));
            $detalle['detallesAdicionales'] = [];
            $detAdicional = [];
            $detAdicional['detalle'] = $row->detalle != '' ? $row->detalle : 'sin detalle';
            array_push($detalle['detallesAdicionales'], $detAdicional);
            $detalle['impuestos'] = [];
            $campos['detalles'] = [];
            $valor_ice = 0;
            if ($impuestoIva != '') {
                $detalleImpuestos = [];
                $detalleImpuestos['codigo'] = $impuestoIva->codigo_impuesto;
                $baseImponible = ($detalle['cantidad'] * $detalle['precioUnitario']) + $valor_ice -  $detalle['descuento'];
                $detalleImpuestos['baseImponible'] = $this->getDecimal($baseImponible - $detalle['descuento']);
                if ($campos['valor_iva'] > 0) {
                    $detalleImpuestos['tarifa'] = $this->getDecimal($impuestoIva->porcentaje);
                    $detalleImpuestos['codigoPorcentaje'] = $impuestoIva->codigo;
                    $detalleImpuestos['valor'] = $this->getDecimal($this->getDecimal($detalleImpuestos['baseImponible']) * $this->getDecimal($impuestoIva->porcentaje / 100));
                } else {
                    $detalleImpuestos['tarifa'] = $this->getDecimal(0);
                    $detalleImpuestos['codigoPorcentaje'] = 0;
                    $detalleImpuestos['valor'] = $this->getDecimal(0);
                }
                array_push($detalle['impuestos'], $detalleImpuestos);
            }
        }
        array_push($campos['detalles'], $detalle);
        return $campos;
    }
    private function getInfoCompRetencion($xmlDocument, $campos)
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
    private function getInfoTributaria($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('infoTributaria');
        $nodoDetalle->appendChild($xmlDocument->createElement('ambiente', $this->tipoAmbiente));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoEmision', $this->tipoEmision));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocial', $campos['infoTributaria']["razonSocial"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('nombreComercial', str_replace('&', 'Y', $campos['infoTributaria']["nombreComercial"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('ruc', $campos['infoTributaria']["ruc"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('claveAcceso', $campos['infoTributaria']["claveAcceso"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('codDoc', $campos['infoTributaria']["codDoc"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('estab', $campos['infoTributaria']["estab"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('ptoEmi', $campos['infoTributaria']["ptoEmi"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('secuencial', $campos['infoTributaria']["secuencial"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirMatriz', $campos['infoTributaria']["dirMatriz"]));
        if ($this->empresa->agente_retencion != 0)
            $nodoDetalle->appendChild($xmlDocument->createElement('agenteRetencion', "201"));
        if ($this->empresa->rimpe_emprendedor == 1)
            $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteRimpe', utf8_decode("CONTRIBUYENTE RÉGIMEN RIMPE")));
        elseif ($this->empresa->rimpe_popular == 1)
            $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteRimpe', utf8_decode("CONTRIBUYENTE RÉGIMEN RIMPE")));
        return $nodoDetalle;
    }
    private function getInfoFactura($xmlDocument, $campos)
    {
        $identificadorruc = 0;
        $dirEstablecimiento = $this->sucursal->direccion_sucursal;
        $nodoDetalle = $xmlDocument->createElement('infoFactura');
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $campos['fechaEmision']));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $dirEstablecimiento));
        if (isset($campos["contribuyenteEspecial"]))
            $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos["contribuyenteEspecial"]));
        if (isset($campos["obligadoContabilidad"]))
            $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos["obligadoContabilidad"]));
        else
            $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', 'NO'));
        if (isset($this->model->id_cliente)) {
            if (strlen($this->model->id_cliente) == 10) {
                $identificadorruc = '05';
            } elseif (strlen($this->model->id_cliente) == 13)
                $identificadorruc = '04';
            $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionComprador', $identificadorruc));
        } else {
            $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionComprador', $campos["tipoIdentificacionComprador"]));
        }
        if (isset($campos["guiaRemision"])) {
            if ($campos["guiaRemision"] != 0) {
                if ($campos["guiaRemision"] != '')
                    $nodoDetalle->appendChild($xmlDocument->createElement('guiaRemision', $campos['estab'] . '-' . $campos['ptoEmi'] . '-' . str_pad($campos["guiaRemision"], 9, '0', STR_PAD_LEFT)));
            }
        }
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialComprador', str_replace("'", "", str_replace('&', '&amp;', $campos["razonSocialComprador"]))));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionComprador', $campos["identificacionComprador"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalSinImpuestos', $this->getDecimal($campos["totalSinImpuestos"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalDescuento', $this->getDecimal($campos["totalDescuento"])));
        $totalConImpuestos = $xmlDocument->createElement('totalConImpuestos');
        $detalles = $campos["totalConImpuestos"];
        foreach ($detalles as $detalle) {
            $item = $xmlDocument->createElement('totalImpuesto');
            $item->appendChild($xmlDocument->createElement('codigo', $detalle["codigo"]));
            $item->appendChild($xmlDocument->createElement('codigoPorcentaje', $detalle["codigoPorcentaje"]));
            $item->appendChild($xmlDocument->createElement('baseImponible', $this->getDecimal($detalle["baseImponible"])));
            $item->appendChild($xmlDocument->createElement('valor', $this->getDecimal($detalle["valor"])));
            $totalConImpuestos->appendChild($item);
        }
        $nodoDetalle->appendChild($totalConImpuestos);
        $nodoDetalle->appendChild($xmlDocument->createElement('propina', $this->getDecimal($campos["propina"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('importeTotal', $this->getDecimal($campos["importeTotal"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('moneda', $campos["moneda"]));
        $nodoDetalle->appendChild($this->formaPago($xmlDocument, $campos));
        return $nodoDetalle;
    }
    private function formaPago($xmlDocument, $detalles)
    {
        foreach ($detalles['formaPago'] as $detalle) {
            $pago = $detalle["plazo"];
            if ($pago == '')
                $pago = 0;
            $nodeFormaPago = $xmlDocument->createElement('pagos');
            $item = $xmlDocument->createElement('pago');
            $item->appendChild($xmlDocument->createElement('formaPago', $detalle["codigo_sri"]));
            $item->appendChild($xmlDocument->createElement('total', $this->getDecimal($detalles["totalConImpuestos"][0]['baseImponible'] + $detalles["totalConImpuestos"][0]['valor'])));
            $item->appendChild($xmlDocument->createElement('plazo', $pago));
            $item->appendChild($xmlDocument->createElement('unidadTiempo', $detalle["unidadTiempo"]));
            $nodeFormaPago->appendChild($item);
        }
        return $nodeFormaPago;
    }
    private function getDetalles($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('detalles');
        $detalles = $campos["detalles"];
        foreach ($detalles as $detalle) {
            $item = $xmlDocument->createElement('detalle');
            $item->appendChild($xmlDocument->createElement('codigoPrincipal', $detalle["codigoPrincipal"]));
            $item->appendChild($xmlDocument->createElement('codigoAuxiliar', $detalle["codigoAuxiliar"]));
            $item->appendChild($xmlDocument->createElement('descripcion', str_replace('\'', ' ', str_replace('&', 'y', $detalle["descripcion"]))));
            $item->appendChild($xmlDocument->createElement('cantidad', $detalle["cantidad"]));
            $item->appendChild($xmlDocument->createElement('precioUnitario', $this->getDecimal($detalle["precioUnitario"], 6)));
            $item->appendChild($xmlDocument->createElement('descuento', $this->getDecimal($detalle["descuento"])));
            $item->appendChild($xmlDocument->createElement('precioTotalSinImpuesto', $this->getDecimal($detalle["precioTotalSinImpuesto"])));
            if (count($detalle["detallesAdicionales"])) {
                $detallesAdicionales = $this->getDetallesAdicionales($xmlDocument);
                $item->appendChild($detallesAdicionales);
            }
            $impuestos = $this->getImpuestos($xmlDocument, $detalle["impuestos"]);
            $item->appendChild($impuestos);
            $nodoDetalle->appendChild($item);
        }
        return $nodoDetalle;
    }
    private function getDetallesAdicionales($xmlDocument)
    {
        $nodoDetalle = $xmlDocument->createElement('detallesAdicionales');
        $dati = str_replace('ñ', 'n', strtolower($this->cliente->nombre));
        $dati = str_replace('ñ', 'n', $dati);
        $item = $xmlDocument->createElement('detAdicional');
        $item->setAttribute('nombre', utf8_encode(str_replace('ñ', 'n', ($dati))));
        $item->setAttribute('valor', isset($this->cliente->email_representante) ? $this->cliente->email_representante : 'NN');
        $item->setAttribute('valor', isset($this->cliente->direccion_representante) ? $this->cliente->direccion_representante : 'NN');
        $nodoDetalle->appendChild($item);
        return $nodoDetalle;
    }
    private function getImpuestos($xmlDocument, $detalles)
    {
        $nodoDetalle = $xmlDocument->createElement('impuestos');
        foreach ($detalles as $detalle) {
            $item = $xmlDocument->createElement('impuesto');
            $item->appendChild($xmlDocument->createElement('codigo', $detalle["codigo"]));
            $item->appendChild($xmlDocument->createElement('codigoPorcentaje', $detalle["codigoPorcentaje"]));
            $item->appendChild($xmlDocument->createElement('tarifa', $detalle["tarifa"]));
            $item->appendChild($xmlDocument->createElement('baseImponible', $this->getDecimal($detalle["baseImponible"])));
            $item->appendChild($xmlDocument->createElement('valor', $this->getDecimal($detalle["valor"])));
            $nodoDetalle->appendChild($item);
        }
        return $nodoDetalle;
    }
    private function getInfoAdicional($xmlDocument, $campos)
    {
        $resolucion = 0;
        $adicional = '';
        $detalles = [];
        if (isset($campos['razonSocialComprador']))
            $cliente = $campos['razonSocialComprador'];
        if (isset($campos['razonSocialSujetoRetenido']))
            $cliente = $campos['razonSocialSujetoRetenido'];
        if (isset($campos['razonSocialTransportista']))
            $cliente = $campos['razonSocialTransportista'];
        if ($campos['codDoc'] == '07') {
            if ($cliente != "") array_push($detalles, array("nombre" => 'Proveedor', "valor" => $cliente));
            $direccion = strtolower($this->proveedor->direccion);
            $telefono = $this->proveedor->telefono;
            $telefono = $this->proveedor->telefono;
            $email = strtolower($this->proveedor->email);
        } else {
            if ($cliente != "") array_push($detalles, array("nombre" => 'Cliente', "valor" => $cliente));
            $direccion = isset($this->cliente->direccion) ? strtolower($this->cliente->direccion) : 'NN';
            $telefono = $this->cliente->telefono_representante;
            $email = strtolower($this->cliente->email_representante);
            $resolucion = $this->empresa->agente_retencion;
            $adicional = json_decode($campos['adicional']);
        }
        array_unshift($detalles, array(
            "nombre" => 'RUC Proveedor',
            "valor" => '0993094072001'
        ));
        if (count($adicional) > 0) array_push($detalles, array("nombre" => 'Adicional', "valor" => $adicional));
        if ($email != "") array_push($detalles, array("nombre" => 'Email', "valor" => strtoupper($email)));
        if ($telefono != "") array_push($detalles, array("nombre" => utf8_decode('Teléfono'), "valor" => str_replace(' ', '', str_replace('(', '', str_replace(')', '', str_replace('-', '', $telefono))))));
        if ($resolucion != 0) array_push($detalles, array("nombre" =>  utf8_decode('Agente Retención'), "valor" => 'NAC-DNCRASC20-00000001'));
        $nodoDetalle = $xmlDocument->createElement('infoAdicional');
        $count = 0;
        foreach ($detalles as $item) {
            $count++;
            if ($count > 5) break;
            $campoAdicional = $xmlDocument->createElement('campoAdicional', isset($item["valor"]) ? str_replace("'", "", str_replace('&', '&amp;', $item["valor"])) : 'NN');
            $campoAdicional->setAttribute('nombre', $item["nombre"]);
            $campoAdicional->setAttribute('nombre', $item["nombre"]);
            $nodoDetalle->appendChild($campoAdicional);
        }
        return $nodoDetalle;
    }
    private function validarXmlToXsd($comprobante)
    {
        $result = array();
        $isValidoXsd = false;
        $mensajes = array();
        $xsd = "";
        switch ($this->tipoDocumento) {
            case 'factura':
                $xsd = storage_path() . '/app/facturaelectronica/SriXsd/Factura/factura_V' . $this->versionDocumento . '.xsd';
                break;
            case 'comprobanteRetencion': //1.0.0
                $xsd = base_url() . "SriXsd/version 1.0.0/comprobanteRetencion.xsd";
                break;
            case 'notaCredito':
                $xsd = base_url() . "SriXsd/version 1.0.0/notaCredito.xsd";
                break;
            case 'notaDebito':
                $xsd = app_path() . "\\helpers\\helper\\SriXsd\\version 1.0.0\\notaDebito.xsd";
                break;
            case 'guiaRemision':
                $xsd = base_url() . "SriXsd/version 1.0.0/guiaRemision.xsd";
                break;
        }
        $doc = new DOMDocument();
        $doc->loadXML($comprobante);
        libxml_clear_errors();
        libxml_use_internal_errors(true);
        $isValidoXsd = $doc->schemaValidate($xsd);
        $errors = libxml_get_errors();
        if ($errors) {
            foreach ($errors as $error) {
                array_push($mensajes, trim("($error->code) $error->message"));
            }
        }
        $result["isValidoXsd"] = $isValidoXsd;
        $result["mensajes"] = $mensajes;
        return $result;
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
                if (!file_exists(base_path() . '/storage/app/facturaelectronica/p12/' . $data['infoTributaria']['ruc'] . '/' . $data['datosfirma']['ruta_firma'])) {
                    $error[$cont] =  'Error: ' . ($cont + 1) . ': el archivo de la firma no existe (' . $data['datosfirma']['ruta_firma'] . ')';
                    $cont++;
                } else {
                    if (!$this->verificar_token(base_path() . '/storage/app/facturaelectronica/p12/' .  $data['infoTributaria']['ruc'] . '/' . $data['datosfirma']['ruta_firma'], $data['datosfirma']['clave_firma'])) {
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
            $empresa = Empresas::where('id', $data['infoTributaria']['ruc'])->first();
            if (is_null($empresa)) {
                $error[$cont] =  "Error: " . ($cont + 1) . ": no existe la empresa";
                $cont++;
            } else {
                //InfoFactura
                //validar si la empresa esta habilitada como facturacion electronica
                $deEmpresa = DeEmpresas::where('id_empresa', $empresa->id)->first();
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
                            $error = $this->validarInfoFactura($data['infoFactura'], $cont, $error, $data);
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
    private function validarInfoFactura($data, &$cont, $error, $data_ = null)
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
                    $error = $this->validarTotalImpuestos($data['totalConImpuestos'], $data_['infoTributaria']['codDoc'], $cont, $error);
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
            $error[$cont] = "Error " . ($cont + 1) . ': ' . $ex->getMessage();
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
                    $deCodigo = CodigoImpuestos::where('codigo_impuesto', $codigo)->where('codigo', $codigoPorcentaje)->first();

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
    public function obtenerRespuestaXml($firmado = false, $comprobante = "", $mensajes = null)
    {
        $xml = new \DOMDocument("1.0", "UTF-8");
        $root = $xml->appendChild($xml->createElement('sentinel'));
        $root->appendChild($xml->createElement('firmado', $firmado ? "VALIDO" : "INVALIDO"));
        if ($comprobante) {
            $nodoComprobante = $root->appendChild($xml->createElement('comprobante'));
            $nodoComprobante->appendChild($xml->createCDATASection($comprobante));
        } else {
            $root->appendChild($xml->createElement('comprobante', $comprobante));
        }
        if ($mensajes) {
            $nodoMensajes = $xml->createElement('mensajes');
            foreach ($mensajes as $mensaje) {
                $nodoItem = $nodoMensajes->appendChild($xml->createElement('mensaje'));
                $nodoItem->appendChild($xml->createCDATASection($mensaje));
            }
            $root->appendChild($nodoMensajes);
        } else {
            $root->appendChild($xml->createElement('mensajes', $comprobante));
        }
        $xml->xmlStandalone = false;
        $xml->formatOutput = true;
        $response = $xml->saveXML();
        return $response;
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
    public function getDatePeriodoFiscal($valor)
    {
        if (trim($valor) == "") return "";
        $result = date('m/Y', strtotime($valor));
        return $result;
    }
}
