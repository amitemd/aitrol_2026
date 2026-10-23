<?php

namespace Sis_medico\Http\Controllers\sri_electronico;

use DOMDocument;
use Excel;
use Datetime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\Http\Controllers\ApiFacturacionController;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\De_Detalle_Documento;
use Sis_medico\Empresa;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\De_Empresa;
use Sis_medico\De_Maestro_Documentos;
use Sis_medico\De_Codigo_Impuestos;
use Sis_medico\De_Estado_Sri;
use Sis_medico\De_Info_Tributaria;
use Sis_medico\De_Log_Error;
use Sis_medico\Http\Controllers\EmisionDocumentosController;
use SoapClient;
use ValidacionXSD;

class SriElectronicoController extends Controller
{
    public $tipoDocumento;
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function errorGeneral(Request $req)
    {
        return De_Log_Error::where('id_documento', $req->id)->orderBy('id', 'desc')->first();
    }
    public function traererrorTributario(Request $r)
    {
        $archivoXml = base_path() . '/storage/app/facturaelectronica/respuestaSri/recepcion/' . $r->clave . '.xml';
        $xml = simplexml_load_file($archivoXml);

        echo json_encode([
            'xml' => $xml
        ]);
    }
    public function traeInfoTributaria(Request $r)
    {
        $archivoXml = base_path() . '/storage/app/facturaelectronica/respuestaSri/autorizacion/' . $r->clave . '_AUTORIZADO.xml';
        $xml = simplexml_load_file($archivoXml);
        $comprobante = simplexml_load_string($xml->autorizaciones->autorizacion->comprobante);
        echo json_encode([
            'claveAccesoConsultada' => $xml->claveAccesoConsultada,
            'estado' => $xml->autorizaciones->autorizacion->estado,
            'fechaAutorizacion' => $xml->autorizaciones->autorizacion->fechaAutorizacion,
            'numeroDocumento' => $comprobante->infoTributaria->estab . '-' . $comprobante->infoTributaria->ptoEmi . '-' . $comprobante->infoTributaria->secuencial,
        ]);
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 21, 22)) == false) {
            return true;
        }
    }
    public function pruebas_sri()
    {
        $empresa['ruc'] = '1803954195001';
        $empresa['nombreComercial'] = 'Mavica';
        $empresa['razonSocial'] = 'Mavica';
        $empresa['dirMatriz'] = 'venezuela y calle 16'; //cambiar
        $empresa['telefono1'] = '0000000';
        $empresa['telefono2'] = '0000000';
        $empresa['ciudad'] = 'Guayaquil';
        $empresa['email'] = 'jorgezarama@hotmail.com';
        $data['empresa'] = $empresa;
        $infoTributaria['ambiente'] = 1; //agregar
        $infoTributaria['ruc'] = $empresa['ruc']; //1803954195001/eduardo/
        $infoTributaria['codDoc'] = 6;
        $infoTributaria['tipoEmision'] = 1;
        $infoTributaria['nombreComercial'] = 'Mavica';
        $infoTributaria['razonSocial'] = 'Mavica';
        $infoTributaria['secuencial'] = 52;
        $infoTributaria['dirMatriz'] = 'venezuela y calle 16';
        $infoTributaria['fecha_emision'] = date('Y-m-d H:i:s');
        $infoTributaria['estab'] = '001'; //cambiar eliminar sucursal agregar a data directamente
        $infoTributaria['ptoEmi'] = '001'; //cambiar eliminar sucursal agregar a data directamente
        $claveObj = new ClaveAcceso();
        $campos = [
            'fecha_emision' => $infoTributaria['fecha_emision'],
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
        //dd($claveObj->clave_acceso);
        $infoTributaria['claveAcceso'] = $claveObj->clave_acceso;
        $infoTributaria['agenteRetencion'] = 0;
        $infoTributaria['rimpe_emprendedor'] = 0;
        $infoTributaria['rimpe_popular'] = 0;
        $data['infoTributaria'] = $infoTributaria;
        //factura
        // $cliente = [];
        // $cliente['tipoIdentificacionComprador'] = '05';
        // $cliente['cedula'] = '0950457978';
        // $cliente['nombre'] = 'JORGE AARON';
        // $cliente['apellido'] = 'ZARAMA HEREDIA';
        // $cliente['email'] = 'jorgezarama@hotmail.com';
        // $cliente['telefono'] = '0967938107';
        // $direccion['calle'] = 'sauces 9';
        // $direccion['ciudad'] = 'GUAYAQUIL';
        // $cliente['direccion'] = $direccion['ciudad'] .', ' .$direccion['calle'];
        // $data['cliente'] = $cliente;

        // $totalImpuesto['porc_imp'] = '12';
        // $totalImpuesto['valor_imp'] = '2.04';
        // $totalImpuestos = [];
        // $totalImpuestos[0] = $totalImpuesto;
        // $data['total_impuestos'] = $totalImpuestos;//total_impuestos

        $detalles = []; //productos

        $detalle['codigoPrincipal'] = 'LABS-649';
        $detalle['codigoAdicional'] = 'LABS-649';
        $detalle['descripcion'] = 'descripcion de producto';
        $detalle['cantidad'] = '1';
        $detalle['precioUnitario'] = '3.13';
        $detalle['porc_imp'] = 12;
        $detalle['valor_iva'] = 0.0; //tax
        $detalle['descuento'] = 0.0;
        $detalle['precioTotalSinImpuesto'] = 3.13; //subtotal
        $detalle['total'] = 3.13;
        $detallesAdicionales = [];
        $detAdicional['nombre'] = 'marca';
        $detAdicional['valor'] = 'marca prueba';
        $detallesAdicionales[0] = $detAdicional;
        $detAdicional['nombre'] = 'lote';
        $detAdicional['valor'] = '1205';
        $detallesAdicionales[1] = $detAdicional;
        //max 3 detAdicionales...
        $detalle['detallesAdicionales'] = $detallesAdicionales;
        $detalles[0] = $detalle;

        if ($data['infoTributaria']['codDoc'] == 1)
            $data['detalles'] = $detalles;

        //factura
        // $pagos = [];
        // $pago['valor'] = 3.13;
        // $pago['forma_pago'] = '01';
        // $pago['plazo'] = '30';
        // $pago['unidad_tiempo'] = 'dias';
        // $pagos[0] = $pago;
        // $data['pagos'] = $pagos;

        // $impuesto['porc_imp'] = '12';
        // $impuesto['valor_imp'] = '2.04';
        // $impuestos=[];
        // $impuestos[0] = $impuesto;

        // $data['valorRetIva'] = '10620.00';//opcional
        // $data['valorRetRenta'] = '10620.00';//opcional
        // $data['concepto'] = 'Documento Electronico - ZARAMA JORGE';
        // $data['totalSinImpuestos'] = 3.13; 
        // $data['totalDescuento'] = 0.00; 
        // $data["propina"] = 0.00; 
        // $data["moneda"] = 'DOLAR'; 

        $guiaRemision['dirEstablecimiento'] = "venezuela y calle 16"; //cambiado de sucursal a guia remision
        $guiaRemision['dirPartida'] = 'Av. Eloy Alfaro 34 y Av. Libertad Esq';
        $guiaRemision['razonSocialTransportista'] = 'Transportes S.A.';
        $guiaRemision['tipoIdentificacionTransportista'] = '04';
        $guiaRemision['rucTransportista'] = '0950458000001';
        $guiaRemision['telefonoTransportista'] = '09679308107';
        $guiaRemision['rise'] = null;
        $guiaRemision['emailTransportista'] = 'jorgezarama@hotmail.com';
        $guiaRemision['fechaIniTransporte'] = '25-08-2022';
        $guiaRemision['fechaFinTransporte'] = '26-08-2022';
        $guiaRemision['placa'] = 'MCL0827';
        $guiaRemision['contribuyenteEspecial'] = null;
        $guiaRemision['obligadoContabilidad'] = 'NO';
        $data['infoGuiaRemision'] = $guiaRemision;

        $destinatario['identificacionDestinatario'] = '0950457978';
        $destinatario['razonSocialDestinatario'] = 'Alvarez Mina John Henry';
        $destinatario['dirDestinatario'] = 'Av. Simón Bolívar S/N Intercambiador';
        $destinatario['motivoTraslado'] = 'Venta de Maquinaria de Impresión';
        $destinatario['docAduaneroUnico'] = ''; //opcional
        $destinatario['codEstabDestino'] = 1;
        $destinatario['ruta'] = 'Quito - Cayambe - Otavalo';
        $destinatario['codDocSustento'] = 1;
        $destinatario['numDocSustento'] = '001-001-000000008';
        $destinatario['numAutDocSustento'] = '1108202201180395419500110010010000002000000020012'; //2110201116302517921467390011234567891
        $destinatario['fechaEmisionDocSustento'] = '16/08/2022';
        if ($data['infoTributaria']['codDoc'] == 6)
            $destinatario['detalles'] = $detalles;
        $destinatarios[0] = $destinatario;
        $data['destinatarios'] = $destinatarios;

        $info_adicional['nombre'] = "AGENTES_RETENCION";
        $info_adicional['valor']  = "Resolucion 1";
        $informacion_adicional[0] = $info_adicional;

        $info_adicional['nombre'] = "PACIENTE";
        $info_adicional['valor']  = 'Zarama Heredia Jorge';
        $informacion_adicional[1] = $info_adicional;

        $info_adicional['nombre'] = "MAIL";
        $info_adicional['valor']  = 'jorgezarama@hotmail.com';
        $informacion_adicional[2] = $info_adicional;

        $info_adicional['nombre'] = "DIRECCION";
        $info_adicional['valor']  = 'sauces 9 mz 528 v24';
        $informacion_adicional[3] = $info_adicional;
        $data['informacion_adicional'] = $informacion_adicional;
        //dd($data);

        $est = $this->consultarEstadoSRI($empresa['ruc']);
        $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/firma3.p12'; //firma.p12
        $password = 'integra123456'; //'3duard0faustos';
        //generar xml

        //$this->llamarApiGuiaRemision();

        // $validacion = $this->validarData($data);
        // if(!empty($validacion)){
        //     return $validacion;
        // }
        //$this->envio_documento($data);
        ini_set('max_execution_time', '300');
        $comprobante = $this->generarDocElectronicoXml($data);
        //dd($comprobante);
        if (!empty($comprobante)) {
            $docAnte = simplexml_load_string($comprobante);

            if (!empty($docAnte)) {
                //validar xsd
                $validacion = $this->validarXmlToXsd($comprobante);
                if (!empty($validacion)) {
                    return $validacion;
                }

                //firmar xml
                $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                //dd($result);
                if (!empty($result)) {
                    //recepcion sri

                    $respuesta = $this->recibirWs($result, 1);
                    $this->generarXmlRespuesta($respuesta);
                    //dd($respuesta['estado']);
                    if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                        //$this->procesarRespuestaXml($respuesta);
                        //autorzacion sri
                        $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso);
                        //dd($respuestaAutorizacion);
                        $this->generarXmlAutorizacion($respuestaAutorizacion);
                        //dd($respuestaAutorizacion);
                        return $respuestaAutorizacion;
                    } else {
                        $this->generarXmlRespuesta($respuesta);
                        return $respuesta['mensajesWs'];
                    }
                }
            }
        }
        exit;
    }

    public function parshearXML($xml)
    {

        $content = simplexml_load_string($xml);
    }

    public function generarXmlAutorizacion($xmlRespuesta)
    {
        $nombreArchivo = "noName";
        $estado = "sin procesar";
        try {
            //dd($xmlRespuesta);
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
                $nodoAutorizacion->appendChild($xml->createElement('estado', $autorizacion->estado));
                $nodoAutorizacion->appendChild($xml->createElement('fechaAutorizacion', $autorizacion->fechaAutorizacion));
                $nodoAutorizacion->appendChild($xml->createElement('ambiente', $autorizacion->ambiente));
                $nodoAutorizacion->appendChild($xml->createElement('comprobante', $autorizacion->comprobante));
                if (is_null($autorizacion->mensajes)) {
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
            $r_ = base_path() . '/storage/app/facturaelectronica/respuestaSri/Autorizacion' . '/';
            $this->crearcarpeta($r_);
            $ruta = $r_ . $nombreArchivo . '_' . $estado . '.xml';
            $xml->save($ruta);
            dd($comprobante);
            return $comprobante;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            echo '<br/>' . $ex->getLine();
        }
    }

    public function generarXmlRespuesta($xmlRespuesta)
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
                //dd($comprobantes);
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

                $recibida = $xml->createElement('isRecibida', $xmlRespuesta['isRecibida']);
                $root->appendChild($recibida);
                $xml->appendChild($root);
                $xml->formatOutput = true;
                $comprobante = $xml->saveXML();
                $r_ = base_path() . '/storage/app/facturaelectronica/respuestaSri/Recepcion' . '/';
                $this->crearcarpeta($r_);
                $ruta = $r_ . $nombreArchivo . '.xml';
                $xml->save($ruta);
                //return $comprobante;
            } elseif ($estado == 'RECIBIDA') {
            }
        } catch (Exception $ex) {
            //throw $th;
        }
    }

    public function getMensajes($xmlDocument, $mensajes)
    {
        $nodoMensajes = $xmlDocument->createElement('mensajes');

        foreach ($mensajes as $mensaje) {
            $nodoMensaje = $xmlDocument->createElement('mensaje');
            $nodoMensaje->appendChild($xmlDocument->createElement('Identificador',  $mensaje['identificador']));
            $nodoMensaje->appendChild($xmlDocument->createElement('mensaje',  $mensaje['mensaje']));
            if (isset($mensaje['informacionAdicional'])) {
                $nodoMensaje->appendChild($xmlDocument->createElement('informacionAdicional',  $mensaje['informacionAdicional']));
            }
            $nodoMensaje->appendChild($xmlDocument->createElement('tipo',  $mensaje['tipo']));
            $nodoMensajes->appendChild($nodoMensaje);
        }
        return $nodoMensajes;
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

    public function validarXmlToXsd($dataDoc)
    {
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
            }

            $jarXSD = base_path() . '/ValidadorXSD/dist/ValidadorXSD.jar';
            $commandXSD = "java -jar $jarXSD $xsd $pathxml 2>&1";
            $respuestaCmdXSD = exec($commandXSD, $output, $return_value);
            return $respuestaCmdXSD;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function firmar_xml($comprobante, $password, $p12)
    {
        $config = array('firmar' => true, 'pass' => $password, 'file' => $p12);
        $firmar = new FirmaElectronica($config);
        $firmar->signXML($comprobante, '', null, false);
    }

    public function enviar_sri()
    {
        $url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';
        $client = new SoapClient($url); //dd($client);

        $directorio    = base_path() . '/storage/app/facturaelectronica/firmados/';

        $fichero       = "facturaFirmada.xml";
        $fichero       = file_get_contents($directorio . "/" . $fichero); //dd($fichero);
        $decodeContent = base64_encode($fichero); //dd($decodeContent);
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                    <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://ec.gob.sri.ws.recepcion">
                      <SOAP-ENV:Body>
                        <ns1:validarComprobante>
                          <xml>' . $decodeContent . '</xml>
                        </ns1:validarComprobante>
                      </SOAP-ENV:Body>
                </SOAP-ENV:Envelope>';
        $xml = $fichero;
        $param = array(
            'xml' => $xml
        );
        $result = $client->validarComprobante($param);

        // $claveAcceso = "2806202201092272958700110010010000000031234567811";
        // $this->autorizacion_sri($claveAcceso);
    }

    function recibirWs($comprobante, $tipoAmbiente = 1)
    {
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

    public function autorizacion_sri($claveAcceso)
    {
        sleep(3);
        $url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';
        $client = new SoapClient($url);
        $param = array(
            'claveAccesoComprobante' => $claveAcceso
        );
        return $client->autorizacionComprobante($param);
    }

    public function consultarEstadoSRI($idEmpresa)
    {
        $url = '';
        $tipoAmbiente = 1;
        $deEmpresa = De_Empresa::where('id_empresa', $idEmpresa)->first();
        if (!is_null($deEmpresa)) {
            $tipoAmbiente = $deEmpresa->ambiente;
        }

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
        if ($estadoSri == 1) {
            echo 'service up';
        } else {
            echo 'service down';
        }

        //dd($estadoSri,$estado);
        if ($estadoSri != $estado) {
            De_Estado_Sri::updateEstado($estadoSri);
        }
        return $estadoSri;
    }

    public function jobFacturacion()
    {
        $respuesta = $this->consultarEstadoSRI('1803954195001');
        if ($respuesta == 1) {
            $idUsuario = Auth::user()->id;
            $emision = new EmisionDocumentosController();
            $emision->getGuiaRemision(null, $idUsuario);
        } else {
        }
    }

    private function validaCedula($cedula)
    {
        $valida_cedula = new ApiFacturacionController();
        return $valida_cedula->llamarValidarCedula($cedula);
    }

    public function validarData($data)
    {
        $flag_error = false;
        $error = "";
        $tipoIdentificacionCliente = 0;
        $valida_cedula = false;
        $empresa = Empresa::where('id', $data['empresa'])->first();
        if (is_null($empresa)) {
            $flag_error = true;
            $error = "no existe la empresa";
        } else {
            $codDoc = $data['infoTributaria']['codDoc'];

            //validar si la empresa esta habilitada como facturacion electronica
            if ($empresa->electronica != 1 && !is_null($empresa->appid) && !is_null($empresa->appsecret) && !is_null($empresa->url) && !is_null($empresa->establecimiento) && !is_null($empresa->punto_emision)) {
                $flag_error = true;
                $error = $error . "empresa no permite facturacion electronica";
            } elseif ($empresa->electronica != 1) {
                $flag_error = true;
                $error = $error . "empresa no permite facturacion electronica";
            }

            //validar si existe un registro de de_empresa con la informacion del ruc para facturacion electronica
            $deEmpresa = De_Empresa::where('id_empresa', $empresa);
            if (is_null($deEmpresa)) {
                $flag_error = true;
                $error('No se encuentra informacion de facturacion electronica');
            }

            $caja = $data['caja'];

            //validar si existe un registro de infoTributaria
            // $infoTributaria = De_Info_Tributaria::where('id_empresa', $empresa->id)->where('id_caja', $caja['id'])->where('id_maestro_documentos', '');
            // if (is_null($infoTributaria)) {
            //     $flag_error = true;
            //     $error('No se encuentra informacion tributaria');
            // }

            //validar codigo documento si no esta en el arreglo no es un codigo valido
            if (in_array($codDoc, array(1, 4, 5, 6, 7)) == false) {
                $flag_error = true;
                $error = $error . '/error codigo de documento no valido';
            }

            //validacion x factura
            if ($codDoc != 6) {
                $productos = $data['productos'];
                $cliente = $data['cliente'];
                $direccion = $cliente['direccion'];
                $pagos = $data['pagos'];
                //validaciones de cliente
                if ($cliente != null) {
                    if ($cliente['nombre'] == null) {
                        $flag_error = true;
                        $msn_error  = $error . '/Error en Nombre, vacio';
                    } else {
                        if (strlen($cliente['nombre']) > 300) {
                            $flag_error = true;
                            $msn_error  = $error . '/Error en Nombre, longitud mayor a 300';
                        }
                    }

                    if ($cliente['email'] == null) {
                        $flag_error = true;
                        $error  = $error . '/Error en email, vacio';
                    } else {
                        if (strlen($cliente['email']) > 300) {
                            $flag_error = true;
                            $error  = $error . '/Error en email, longitud mayor a 300';
                        }
                    }

                    if ($cliente['telefono'] == null) {
                        $flag_error = true;
                        $error  = $error . '/Error en telefono, vacio';
                    } else {
                        if (strlen($cliente['telefono']) > 300) {
                            $flag_error = true;
                            $error  = $error . '/Error en telefono, longitud mayor a 300';
                        }
                    }

                    if ($direccion['calle'] == null) {
                        $flag_error = true;
                        $error  = $error . '/Error en calle';
                    } else {
                        if (strlen($direccion['calle']) > 300) {
                            $flag_error = true;
                            $error  = $error . '/Error en calle';
                        }
                    }

                    if ($direccion['ciudad'] == null) {
                        $flag_error = true;
                        $error  = $error . '/Error en direccion Ciudad, vacio';
                    } else {
                        if (strlen($direccion['ciudad']) > 300) {
                            $flag_error = true;
                            $error  = $error . '/Error en direccion Ciudad, longitud mayor a 300';
                        }
                    }

                    $direccion_full = $direccion['ciudad'] . ', ' . $direccion['calle'];

                    if (strlen($direccion_full) > 300) {
                        $flag_error = true;
                        $error  = $error . '/Error en direccion completa, longitud mayor a 300';
                    }

                    //VALIDO SI LA CEDULA O RUC ES VALIDO

                    if (!empty($cliente['cedula'])) {
                        $valida_cedula = $this->validaCedula($cliente['cedula']);

                        if ($cliente['cedula'] == '9999999999999') { //consumidor final
                            $tipoIdentificacionCliente = 7;
                        } elseif (strlen($cliente['cedula']) == 13) { //ruc
                            $tipoIdentificacionCliente = 4;
                        } elseif (strlen($cliente['cedula']) == 10) { //cedula
                            $tipoIdentificacionCliente = 5;
                        } elseif (strlen($cliente['cedula']) > 13 && strlen($cliente['cedula']) <= 20) { //pasaporte
                            $tipoIdentificacionCliente = 6;
                        } else {
                            //validacion x IDENTIFICACION DELEXTERIOR 8
                            //validacion x PLACA 9
                        }
                    } else {
                        $flag_error = true;
                        $error = $error . '/numero de Identificacion Cliente no puede estar vacio';
                    }

                    if (!$valida_cedula && ($tipoIdentificacionCliente == 4 || $tipoIdentificacionCliente == 5)) {
                        $flag_error = true;
                        $error = $error . '/numero de Identificacion Cliente incorrecto';
                    }
                }

                //validaciones de productos
                if (count($productos) <= 0) {
                    $flag_error = true;
                    $error = $error . '/no existen productos';
                }

                foreach ($productos as $producto) {
                    if ($producto['sku'] == null) {
                        $flag_error = true;
                        $error = $error . '/error en codigo interno de producto, vacio';
                    } else {
                        if (strlen($producto['sku']) > 25) {
                            $flag_error = true;
                            $error = $error . '/error en nombre de producto, longitud mayor a 25';
                        }

                        if (str_contains($producto['sku'], '/')) {
                            $producto['sku'] = str_replace('/', '-', $producto['sku']);
                        }
                    }

                    if ($producto['descripcion'] == null) {
                        $flag_error = true;
                        $error = $error . '/error en nombre de producto, vacio';
                    } else {
                        if (strlen($producto['descripcion']) > 300) {
                            $flag_error = true;
                            $error = $error . '/error en nombre de producto, longitud mayor a 300';
                        }

                        if (str_contains($producto['descripcion'], '/')) {
                            $producto['descripcion'] = str_replace('/', '-', $producto['descripcion']);
                        }
                    }
                }

                if (count($pagos) <= 0) {
                    $error = $error . '/no existe ningun pago';
                }
                //dd($pagos);
                if (count($pagos) > 0) {
                    for ($i = 0; $i < count($pagos); $i++) {
                        if (in_array($pagos[$i]['forma_pago'], array('01', '15', '16', '17', '18', '19', '20', '21')) == false) {
                            $flag_error = true;
                            $error = $error . '/error cod de forma de pago no valido';
                        }
                    }
                }

                $total = 0;
                foreach ($productos as $producto) {
                    $total = $total + $producto['subtotal'];
                }

                $impuestoTotal = 0;
                foreach ($productos as $producto) {
                    $impuestoTotal = $impuestoTotal + ($producto['subtotal'] * $producto['tax']);
                }

                if ($impuestoTotal != $producto['tax']) {
                    $flag_error = true;
                    $error = $error . '/error calculando el impuesto';
                }

                if ($total != $data['total']) {
                    $flag_error = true;
                    $error = $error . '/error el total no coincide con el calculo';
                }
            }

            //validacion guia_remision
            if ($codDoc == 6) {

                $guiaRemision = $data['guiaRemision'];
                if (!is_null($guiaRemision)) {
                    //validaciones transportista

                    if (!empty($guiaRemision['rucTransportista'])) {


                        if (strlen($guiaRemision['rucTransportista']) == 13) { //ruc
                            $tipoIdentificacionTransportista = 4;
                        } elseif (strlen($guiaRemision['rucTransportista']) == 10) { //cedula
                            $tipoIdentificacionTransportista = 5;
                        } elseif (strlen($guiaRemision['rucTransportista']) > 13 && strlen($guiaRemision['rucTransportista']) <= 20) { //pasaporte
                            $tipoIdentificacionTransportista = 6;
                        } else {
                            //validacion x IDENTIFICACION DELEXTERIOR 8
                            //validacion x PLACA 9
                        }
                    } else {
                        $flag_error = true;
                        $error = $error . '/numero de Identificacion Transportista no puede estar vacio';
                    }

                    if ($tipoIdentificacionTransportista != 6) {
                        $valida_cedula = $this->validaCedula($guiaRemision['rucTransportista']);
                    }

                    if (!$valida_cedula && ($tipoIdentificacionTransportista == 4 || $tipoIdentificacionTransportista == 5)) {
                        $flag_error = true;
                        $error = $error . '/numero de Identificacion Transportista incorrecto';
                    }

                    if ($guiaRemision['razonSocialTransportista'] == null) {
                        $flag_error = true;
                        $msn_error  = $error . '/Error en razon Social Transportista, vacio';
                    } else {
                        if (strlen($guiaRemision['razonSocialTransportista']) > 300) {
                            $flag_error = true;
                            $error  = $error . '/Error en razon Social Transportista, longitud mayor a 300';
                        }
                    }

                    if ($guiaRemision['rise'] != null) {
                        if (strlen($guiaRemision['rise']) > 40) {
                            $flag_error = true;
                            $error  = $error . '/Error en rise, longitud mayor a 40';
                        }
                    }

                    if ($guiaRemision['fechaIniTransporte'] == null) {
                        $flag_error = true;
                        $msn_error  = $error . '/Error en fecha Inicial Transporte, vacio';
                    }

                    if ($guiaRemision['fechaFinTransporte'] == null) {
                        $flag_error = true;
                        $msn_error  = $error . '/Error en fecha final Transporte, vacio';
                    }

                    if ($guiaRemision['placa'] == null) {
                        $flag_error = true;
                        $msn_error  = $error . '/Error en placa, vacio';
                    } else {
                        if (strlen($guiaRemision['placa']) > 20) {
                            $flag_error = true;
                            $error  = $error . '/Error en placa, longitud mayor a 20';
                        }
                    }
                }

                //validaciones destinatario
                $destinatarios = $data['destinatarios'];
                foreach ($destinatarios as $destinatario) {
                    if ($destinatario != null) {
                        if ($destinatario['razonSocialDestinatario'] == null) {
                            $flag_error = true;
                            $msn_error  = $error . '/Error en razon Social Destinatario, vacio';
                        } else {
                            if (strlen($destinatario['razonSocialDestinatario'] > 300)) {
                                $flag_error = true;
                                $msn_error  = $error . '/Error en razon Social Destinatario, longitud mayor a 300';
                            }
                        }

                        if ($destinatario['dirDestinatario'] == null) {
                            $flag_error = true;
                            $error  = $error . '/Error en direccion destinatario, vacio';
                        } else {
                            if (strlen($destinatario['dirDestinatario'] > 300)) {
                                $flag_error = true;
                                $error  = $error . '/Error en direccion destinatario, longitud mayor a 300';
                            }

                            if (str_contains($destinatario['dirDestinatario'], '/')) {
                                $destinatario['dirDestinatario'] = str_replace('/', '-', $destinatario['dirDestinatario']);
                            }
                        }

                        if ($destinatario['motivoTraslado'] == null) {
                            $flag_error = true;
                            $error  = $error . '/Error en motivo Traslado, vacio';
                        } else {
                            if (strlen($destinatario['motivoTraslado'] > 300)) {
                                $flag_error = true;
                                $error  = $error . '/Error en motivo Traslado, longitud mayor a 300';
                            }

                            if (str_contains($destinatario['motivoTraslado'], '/')) {
                                $destinatario['motivoTraslado'] = str_replace('/', '-', $destinatario['motivoTraslado']);
                            }
                        }

                        if ($destinatario['codDocSustento'] == null) {
                            $flag_error = true;
                            $error  = $error . '/Error en codigo documento sustento, vacio';
                        }

                        if ($destinatario['numDocSustento'] == null) {
                            $flag_error = true;
                            $error  = $error . '/Error en numero documento sustento, vacio';
                        }

                        if ($destinatario['numAutDocSustento'] == null) {
                            $flag_error = true;
                            $error  = $error . '/Error en numero autenticacion documento sustento, vacio';
                        }

                        if ($destinatario['docAduaneroUnico'] == null) {
                            $flag_error = true;
                            $error  = $error . '/Error en codigo Aduanero Unico, vacio';
                        } else {
                            if (strlen($destinatario['docAduaneroUnico']) > 20) {
                                $flag_error = true;
                                $error  = $error . '/Error en codigo Aduanero Unico, longitud mayor a 20';
                            }
                        }

                        if ($destinatario['ruta'] == null) {
                            $flag_error = true;
                            $error  = $error . '/Error en ruta, vacio';
                        } else {
                            if (strlen($destinatario['ruta'] > 300)) {
                                $flag_error = true;
                                $error  = $error . '/Error en ruta, longitud mayor a 300';
                            }

                            if (str_contains($destinatario['ruta'], '/')) {
                                $destinatario['ruta'] = str_replace('/', '-', $destinatario['ruta']);
                            }
                        }

                        if ($destinatario['fechaEmisionDocSustento'] == null) {
                            $flag_error = true;
                            $error  = $error . '/Error en fecha Emision Documento Sustento, vacio';
                        }

                        $valida_cedula = $this->validaCedula($destinatario['identificacionDestinatario']);
                        if (strlen($destinatario['identificacionDestinatario']) == 13) { //ruc
                            $tipoIdentificacionDestinatario = 4;
                        } elseif (strlen($destinatario['identificacionDestinatario']) == 10) { //cedula
                            $tipoIdentificacionDestinatario = 5;
                        } elseif (strlen($destinatario['identificacionDestinatario']) > 13 && strlen($destinatario['identificacionDestinatario']) <= 20) { //pasaporte
                            $tipoIdentificacionDestinatario = 6;
                        } else {
                            //validacion x IDENTIFICACION DELEXTERIOR 8
                            //validacion x PLACA 9
                        }
                        //VALIDO SI LA CEDULA O RUC DESTINATARIO ES VALIDO
                        $valida_cedula = true;
                        // if ($destinatario['tipo'] != 6) {
                        //     //$valida_cedula_destinatario = ApiFacturacionController::validarCedula($destinatario->cedula);
                        // }

                        if (!$valida_cedula && ($tipoIdentificacionDestinatario == 4 || $tipoIdentificacionDestinatario == 5)) {
                            $flag_error = true;
                            $error = $error . '/numero de cedula de destinatario incorrecto';
                        }
                    }
                }
            }
        }
        return $error;
    }

    public function validarXSD()
    {
        $filexml = '';
        $xsd = '';
        try {
            $doc = new \DOMDocument('1.0', 'utf-8');
            if (!file_exists($filexml) || !file_exists($xsd)) {
                echo "Archivo <b>$filexml</b> o <b>$xsd</b> no existe.";
                return false;
            }

            //Habilita/Deshabilita errores libxml y permite al usuario extraer 
            //información de errores según sea necesario
            libxml_use_internal_errors(true);
            //lee archivo XML
            $myfile = fopen($filexml, "r");
            $contents = fread($myfile, filesize($filexml));
            $doc->loadXML($contents, LIBXML_NOBLANKS);
            fclose($myfile);
            // Valida un documento basado en un esquema
            if (!$doc->schemaValidate($xsd)) {
                //Recupera un array de errores
                $errors = libxml_get_errors();
                return false;
            }
            return true;
        } catch (Exception $ex) {
        }
    }

    public function no_autorizados()
    {
    }

    public function notificacion_sri()
    {
    }

    function buscarDocumentosElectronicos()
    {
        $data = [];
        $de_decumentos_electronicos = De_Documentos_Electronicos::where('estado', '1')->get();
        foreach ($de_decumentos_electronicos as $documento) {
            $data['empresa'] = '1803954195001'; //1803954195001/eduardo/0916293723001
            $data['infoTributaria']['codDoc'] = 6;
            $data['secuencial'] = 3;
            $data['fecha_emision'] = date('Y-m-d H:i:s'); //Agregado 08-2022
            $caja['id'] = 10;
            $caja['id_sucursal'] = 1;
            $caja['id_empresa'] = 1;
            $caja['cod_caja'] = '001';
            $caja['cod_sucursal'] = '001';
            $caja['dirEstablecimiento'] = 'Av.l venezuela y calle 16';
            $data['caja'] = $caja;
            $cliente = [];

            $cliente['cedula'] = '0950457978';
            $cliente['tipo'] = '5';
            $cliente['nombre'] = 'JORGE AARON';
            $cliente['apellido'] = 'ZARAMA HEREDIA';
            $cliente['email'] = 'jorgezarama@hotmail.com';
            $cliente['telefono'] = '0967938107';
            $direccion['calle'] = 'sauces 9';
            $direccion['ciudad'] = 'GUAYAQUIL';
            $cliente['direccion'] = $direccion;
            $data['cliente'] = $cliente;
            $guiaRemision['dirPartida'] = 'Av. Eloy Alfaro 34 y Av. Libertad Esq';
            $guiaRemision['razonSocialTransportista'] = 'Transportes S.A.';
            $guiaRemision['tipoIdentificacionTransportista'] = '04';
            $guiaRemision['rucTransportista'] = '0950458000';
            $guiaRemision['telefonoTransportista'] = '09679308107';
            $guiaRemision['emailTransportista'] = 'jorgezarama@hotmail.com';
            //$guiaRemision['rise'] = 'Contribuyente Regimen Simplificado RISE';
            $guiaRemision['fechaIniTransporte'] = '21-10-2011';
            $guiaRemision['fechaFinTransporte'] = '22-10-2011';
            $guiaRemision['placa'] = 'MCL0827';
            $guiaRemision['dirEstablecimiento'] = 'prueba';
            $guiaRemision['contribuyenteEspecial'] = false;
            $guiaRemision['obligadoContabilidad'] = 'NO';
            $data['guiaRemision'] = $guiaRemision;
            $productos = [];
            for ($i = 0; $i < 1; $i++) {
                $producto['sku'] = 'LABS-649';
                $producto['nombre'] = 'HEMOGLOBINA';
                $producto['descripcion'] = 'descripcion de producto';
                $producto['cantidad'] = '1';
                $producto['precio'] = '3.13';
                $producto['p_impuesto'] = 12;
                $producto['descuento'] = 0.0;
                $producto['subtotal'] = 3.13;
                $producto['tax'] = 0.0;
                $producto['total'] = 3.13;
                $producto['copago'] = '0';
                $producto['lote'] = 1; //agregar
                $producto['detAdicionales'] = [
                    '0' => 'detalle 1',
                    '1' => 'detalle 2',
                    '2' => 'detalle 3'
                ];
                $productos[$i] = $producto;
            }
            $pagos = [];
            $data['productos'] = $productos;
            if ($data['infoTributaria']['codDoc'] == 1) {
                for ($j = 0; $j < 1; $j++) {
                    $pago['valor'] = 3.13;
                    $pago['forma_pago'] = '01';
                    $pago['plazo'] = '30';
                    $pago['unidad_tiempo'] = 'dias';
                    $pagos[$j] = $pago;
                }
            }
            $data['pagos'] = $pagos;
            $destinatario['tipo'] = 4;
            $destinatario['identificacionDestinatario'] = '0950457978';
            $destinatario['razonSocialDestinatario'] = 'Alvarez Mina John Henry';
            $destinatario['dirDestinatario'] = 'Av. Simón Bolívar S/N Intercambiador';
            $destinatario['motivoTraslado'] = 'Venta de Maquinaria de Impresión';
            $destinatario['docAduaneroUnico'] = '0041324846887'; //opcional
            $destinatario['codEstabDestino'] = 1;
            $destinatario['ruta'] = 'Quito - Cayambe - Otavalo';
            $destinatario['codDocSustento'] = 1;
            $destinatario['numDocSustento'] = '002-001-000000001';
            $destinatario['numAutDocSustento'] = '2110201116302517921467390011234567891';
            $destinatario['fechaEmisionDocSustento'] = '15/08/2022';

            $destinatarios[0] = $destinatario;

            $data['destinatarios'] = $destinatarios;
            $info_adicional['nombre'] = "AGENTES_RETENCION";
            $info_adicional['valor']  = "Resolucion 1";
            $informacion_adicional[0] = $info_adicional;

            $info_adicional['nombre'] = "PACIENTE";
            $info_adicional['valor']  = 'Zarama Heredia Jorge';
            $informacion_adicional[1] = $info_adicional;

            $info_adicional['nombre'] = "MAIL";
            $info_adicional['valor']  = 'jorgezarama@hotmail.com';
            $informacion_adicional[2] = $info_adicional;

            $info_adicional['nombre'] = "DIRECCION";
            $info_adicional['valor']  = 'sauces 9 mz 528 v24';
            $informacion_adicional[3] = $info_adicional;
            $data['informacion_adicional'] = $informacion_adicional;
            $data['total'] = 3.41;
            $data['concepto'] = 'Documento Electronico - ZARAMA JORGE';
            $data['copago'] = 0;
            $data['totalSinImpuestos'] = 3.13; //Agregado 08-2022
            $data['totalDescuento'] = 0.00; //Agregado 08-2022
            $data["propina"] = 0.00; //Agregado 08-2022
            $data["moneda"] = 'DOLAR'; //Agregado 08-2022
        }
    }

    function cargarDataEmpresa($data)
    {
        $empresa = $data['empresa'];
        $deEmpresa = De_Empresa::where('id_empresa', $data['empresa']['ruc'])->get()->first();
        $ambiente = $data['infoTributaria']['ambiente']; //1pruebas - 2 produccion  

        $dataClave = [
            'fecha_emision' => date('Y-m-d H:i:s'),
            'tipo_comprobante' => $data['infoTributaria']['codDoc'],
            'ruc' => $empresa['ruc'],
            'tipo_ambiente' => $ambiente,
            'punto_establecimiento' => $data['infoTributaria']['estab'],
            'punto_emision' => $data['infoTributaria']['ptoEmi'],
            'numero_comprobante' => $data['infoTributaria']['secuencial'],
            'tipo_emision' => 1,
        ];

        $claveObj = new ClaveAcceso();
        $claveObj->llenarDatos($dataClave);

        return $dataEmpresa = [
            'tipoAmbiente' => $ambiente,
            'ruc' => $empresa['ruc'],
            'tipoEmision' => 1,
            'razonSocial' => $empresa['razonSocial'],
            'nombreComercial' => $empresa['nombreComercial'],
            'claveAcceso' => $claveObj->clave_acceso,
            'codDoc' => $data['infoTributaria']['codDoc'],
            'estab' => $data['infoTributaria']['estab'],
            'ptoEmi' => $data['infoTributaria']['ptoEmi'],
            'secuencial' => $data['infoTributaria']['secuencial'],
            'dirMatriz' => $empresa['dirMatriz'],
            'agenteRetencion' => 0,
            'rimpe_emprendedor' => 0,
            'rimpe_popular' => 0,
            'contribuyenteEspecial' => $deEmpresa['contribuyente_especial'] == null ? 'NO' : 'SI',
            'obligadoContabilidad' => $deEmpresa['contabilidad'] == null ? 'NO' : 'SI',
        ];
    }

    function generarDocElectronicoXml($campos)
    {
        $comprobante = null;
        if ($campos['infoTributaria']['codDoc'] == 01)
            $this->tipoDocumento = 'factura';
        elseif ($campos['infoTributaria']['codDoc'] == 06)
            $this->tipoDocumento = 'guiaRemision';

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
            $root->setAttribute('version', '1.1.0');
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
        }
        //Guarda XML
        $xml->appendChild($root);
        $xml->formatOutput = true;
        $comprobante = $xml->saveXML();

        if ($this->tipoDocumento == 'factura') {
            $r_ = base_path() . '/storage/app/facturaelectronica/sinfirmar/' . $campos['infoTributaria']['ruc'] . '/' . $this->tipoDocumento . '/';
            $this->crearcarpeta($r_);
            $ruta = $r_ . $this->tipoDocumento . '-' . $campos['infoTributaria']['estab'] . '-' .  $campos['infoTributaria']['ptoEmi'] . '-' . str_pad($campos['infoTributaria']['secuencial'], 9, 0, STR_PAD_LEFT) . '.xml';
            $xml->save($ruta);
        } elseif ($this->tipoDocumento == 'guiaRemision') {
            $r_ = base_path() . '/storage/app/facturaelectronica/sinfirmar/' . $campos['infoTributaria']['ruc'] . '/' . $this->tipoDocumento . '/';
            $this->crearcarpeta($r_);
            $ruta = $r_ . $this->tipoDocumento . '-' . $campos['infoTributaria']['estab'] . '-' .  $campos['infoTributaria']['ptoEmi'] . '-' . str_pad($campos['infoTributaria']['secuencial'], 9, 0, STR_PAD_LEFT) . '.xml';
            $xml->save($ruta);
        }
        return $comprobante;
    }

    function procesarRespuestaXml($xmlGenerado)
    {
        $compelXml = simplexml_load_string($xmlGenerado);
        //dd($compelXml);
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
        //dd($result);
        return $result;
    }

    function procesarAutorizar($model,  $id_empresa = '')
    {
        $reaul = '';
        $error = "";
        $claveAcceso = "";
        $tipoModel = '';
        try {
            if ($model) {
                if ($model['tipo_comprobante'] == 'factura') {
                    $tipoModel = 'Factura';
                    $datos['tipo_comprobante'] = 'factura';
                }
                if ($model['tipo_comprobante'] == 'comprobanteRetencion') {
                    $tipoModel = 'ComprobanteRetencion';
                    $datos['tipo_comprobante'] = 'retencion';
                }
                if ($model['tipo_comprobante'] == 'notaCredito') {
                    $tipoModel = 'NotaCredito';
                    $datos['tipo_comprobante'] = 'nota de credito';
                }
                if ($model['tipo_comprobante'] == 'guiaRemision') {
                    $tipoModel = 'GuiaRemision';
                    $datos['tipo_comprobante'] = 'guiaRemision';
                }
                $claveAcceso = $model['clave_acceso'];
                $tipoAmbiente = $model['tipo_ambiente'];
                $autorizacionComprobante = $this->autorizarWs($claveAcceso, $tipoAmbiente);
                $datosPostEsta = isset($_POST['establecimiento']) ? $_POST['establecimiento'] : '';
                $datosPostEmis = isset($_POST['emision']) ? $_POST['emision'] : '';
                $datosPostNum = isset($_POST['num']) ? $_POST['num'] : '';
                if ($datosPostEsta == '' && $datosPostEmis == '' && $datosPostNum == '') {
                    echo '<br/><pre>';
                    echo 'Clave Consultada: ' . $autorizacionComprobante['claveAccesoConsultada'] . '<br/>';
                }
                //print_r($autorizacionComprobante);
                if ($autorizacionComprobante) {
                    $numeroComprobantes = isset($autorizacionComprobante["numeroComprobantes"]) ? (int)$autorizacionComprobante["numeroComprobantes"] : -1;
                    $isAutorizado = isset($autorizacionComprobante["isAutorizado"]) ? $autorizacionComprobante["isAutorizado"] : false;
                    $mensajesWsUltimoEnvio = isset($autorizacionComprobante["ultimoComprobanteEnviado"]) ? (isset($autorizacionComprobante["ultimoComprobanteEnviado"]["mensajesWs"]) ? $autorizacionComprobante["ultimoComprobanteEnviado"]["mensajesWs"] : '') : '';
                    $mensajesDbUltimoEnvio = isset($autorizacionComprobante["ultimoComprobanteEnviado"]) ? (isset($autorizacionComprobante["ultimoComprobanteEnviado"]["mensajesDb"]) ? $autorizacionComprobante["ultimoComprobanteEnviado"]["mensajesDb"] : '') : '';
                    $mensajesWs = isset($autorizacionComprobante["mensajesWs"]) ? $autorizacionComprobante["mensajesWs"] : '';
                    $mensajesDb = isset($autorizacionComprobante["mensajesDb"]) ? $autorizacionComprobante["mensajesDb"] : '';
                    $comprobanteAutorizado = isset($autorizacionComprobante["comprobanteAutorizado"]) ? $autorizacionComprobante["comprobanteAutorizado"] : '';
                    $fechaAutorizacion = isset($autorizacionComprobante["fechaAutorizacion"]) ? $autorizacionComprobante["fechaAutorizacion"] : null;
                    $numeroAutorizacion = isset($autorizacionComprobante["numeroAutorizacion"]) ? $autorizacionComprobante["numeroAutorizacion"] : null;
                    $numeroAutorizacion = str_pad($numeroAutorizacion, 37, '0', STR_PAD_LEFT);
                    if ($isAutorizado || $numeroComprobantes == 0) {
                        $mensajesDbUltimoEnvio = json_encode($mensajesDbUltimoEnvio);
                        $mensajesWsUltimoEnvio = json_encode($mensajesWsUltimoEnvio);
                        $mensajesDb = json_encode($mensajesDb);
                        $mensajesWs = json_encode($mensajesWs);

                        if ($isAutorizado) {
                            $numeroAutorizacion = str_pad($autorizacionComprobante['autorizaciones'][0]['numeroAutorizacion'], 37, '0', STR_PAD_LEFT);
                            $estado_autorizado = $autorizacionComprobante['autorizaciones'][0]['estado'];
                            $sql = "UPDATE bm_estado_archivos SET

                            mensajesDbUltimoEnvio='$mensajesDbUltimoEnvio',
                            mensajesWsUltimoEnvio='$mensajesWsUltimoEnvio',
                            mensajesWs='$mensajesWs',
                            mensajesDb='$mensajesDb',
                            estado_sri = 'T',
                            archivo_autorizado = '$comprobanteAutorizado',
                            fecha_autorizacion = '$fechaAutorizacion',
                            numeroAutorizacion = '$numeroAutorizacion',
                            estado_recibido = 'RECIBIDA',
                            estado_autorizado = '$estado_autorizado'
                            WHERE claveAcceso = '$claveAcceso';";

                            if ($tipoModel == 'Factura') {
                                $sql = "UPDATE bm_venta SET numeroAutorizacion = '$numeroAutorizacion',fecha_autorizacion = '$fechaAutorizacion', estado_sri='T' WHERE clave_acceso = '$claveAcceso';";
                            } elseif ($tipoModel == 'GuiaRemision')
                                $sql = "UPDATE bm_guiaremision SET numeroAutorizacion = '$numeroAutorizacion',fecha_autorizacion = '$fechaAutorizacion',estado_sri = 'T',archivo_autorizado = '$comprobanteAutorizado' WHERE claveAcceso = '$claveAcceso';";
                            elseif ($tipoModel == 'ComprobanteRetencion') {
                                $sql = "UPDATE bm_retenciones SET archivo_autorizado = '$comprobanteAutorizado',fecha_generado = NOW(),estado_sri = 'T',fecha_autorizacion = '$fechaAutorizacion',numeroAutorizacion = '$numeroAutorizacion' WHERE clave_acceso = '$claveAcceso';";

                                $sql = "UPDATE bm_compras SET estado_sri = 'T',autorizacion_retencion='$numeroAutorizacion' WHERE clave_acceso_retencion='$claveAcceso';";
                            } elseif ($tipoModel == 'NotaCredito')
                                $sql = "UPDATE bm_venta SET archivo_autorizado = '$comprobanteAutorizado',numeroAutorizacion = '$numeroAutorizacion',fecha_autorizacion = '$fechaAutorizacion', estado_sri='T' WHERE clave_acceso = '$claveAcceso';";

                            //echo $sql.'<br/>';
                            if ($tipoModel == 'Factura') {
                                $establecimiento_ = substr($claveAcceso, 24, 3);
                                $emision_ = substr($claveAcceso, 27, 3);
                                $num_factura = (int)substr($claveAcceso, 30, 9);
                                $sql = "UPDATE bm_ventash SET estado_sri = 'T' WHERE num_factura = '$num_factura' AND establecimiento='$establecimiento_' AND emision='$emision_' AND id_empresa='$id_empresa';";
                                //echo $sql.'<br/>';

                            }
                            return 'Ok';
                        } else {
                            $reaul = array('claveAcceso' => $claveAcceso, 'msg' => "Pendiente de autorización", 'css' => 'info');
                        }
                    } else {
                        if (isset($autorizacionComprobante['autorizaciones'][0]['numeroAutorizacion'])) {
                            if ($autorizacionComprobante['autorizaciones'][0]['numeroAutorizacion'] != '')
                                $numeroAutorizacion = str_pad($autorizacionComprobante['autorizaciones'][0]['numeroAutorizacion'], 37, '0', STR_PAD_LEFT);
                            else
                                $numeroAutorizacion = '';
                        } else
                            $numeroAutorizacion = str_pad($numeroAutorizacion, 37, '0', STR_PAD_LEFT);
                        echo 'Identificador: ' . $autorizacionComprobante['autorizaciones'][0]['mensajes'][0]['identificador'] . '<br/>';
                        echo 'Mensaje: ' . $autorizacionComprobante['autorizaciones'][0]['mensajes'][0]['mensaje'] . '<br/>';
                        echo 'Tipo: ' . $autorizacionComprobante['autorizaciones'][0]['mensajes'][0]['tipo'] . '<br/>';
                        echo 'Infor. Adicional: ' . $autorizacionComprobante['autorizaciones'][0]['mensajes'][0]['informacionAdicional'] . '<br/>';
                        $estado_recibido = '';
                        $estado_autorizado = $autorizacionComprobante['autorizaciones'][0]['estado'];
                        $mensaje_error = $autorizacionComprobante['autorizaciones'][0]['mensajes'][0]['mensaje'] . '<br/>Info. Adicional: ' . $autorizacionComprobante['autorizaciones'][0]['mensajes'][0]['informacionAdicional'];
                        $identificador = $autorizacionComprobante['autorizaciones'][0]['mensajes'][0]['identificador'];

                        $sql = "UPDATE bm_estado_archivos SET
                        estado_sri = 'F',
                        archivo_autorizado = '',
                        fecha_autorizacion = '',
                        numeroAutorizacion = '$numeroAutorizacion',
                        estado_recibido = 'RECIBIDA',
                        estado_autorizado = '$estado_autorizado',
                        mensaje_error='$mensaje_error',
                        identificador='$identificador'
                        WHERE claveAcceso = '$claveAcceso';";

                        return $autorizacionComprobante['autorizaciones'][0]['mensajes'][0]['informacionAdicional'];
                    }
                } else {
                    return $autorizacionComprobante;
                }
            }
        } catch (Exception $e) {
            $error .= "<br>- " . $e->getMessage();
        }
        if ($error != "");
        return $reaul;
    }

    function htmlspecial($valor)
    {
        $result = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
        return $result;
    }

    function crearcarpeta($ruta)
    {
        if (!file_exists($ruta)) {
            mkdir($ruta, 0777, true);
        }
    }

    function generarXmlSignJar($dataDoc, $dir = '', $ruta_token = '', $pin_token = '', $clave_acceso = '', $id_factura = '', $empresa_Id)
    {
        $docAnte = simplexml_load_string($dataDoc);
        //$pathxml = base_path() . '/storage/app/facturaelectronica/firmados/'.$docAnte->infoTributaria->ruc.'/'.$this->tipoDocumento.'/'.$this->tipoDocumento.'-'.$docAnte->infoTributaria->estab.'-'.$docAnte->infoTributaria->ptoEmi.'-'.$docAnte->infoTributaria->secuencial.'.xml';
        $fileName = ($clave_acceso) . "_" . uniqid();
        $filenameXml = tempnam(sys_get_temp_dir(), "");
        $carpeta = base_path() . '/storage/app/facturaelectronica/temp/';
        $this->crearcarpeta($carpeta);
        if ($dir == 'factura') {
            $carpeta = base_path() . '/storage/app/facturaelectronica/firmados/' . $empresa_Id . '/' . $this->tipoDocumento;
            $this->crearcarpeta($carpeta);
            $filenameXml .=  $fileName . ".xml";
        }
        if ($dir == 'guiaRemision') {
            $carpeta = base_path() . '/storage/app/facturaelectronica/firmados/' . $empresa_Id . '/' . $this->tipoDocumento;
            $this->crearcarpeta($carpeta);
            $filenameXml .=  $fileName . ".xml";
        }
        if ($dir == 'comprobanteRetencion') {
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
            $xmlFirmado = simplexml_load_string($response['comprobante'], 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
            $r_ = base_path() . '/storage/app/facturaelectronica/firmados/' . $docAnte->infoTributaria->ruc . '/' . $this->tipoDocumento . '/';
            $this->crearcarpeta($r_);
            $xmlFirmado = $xmlFirmado->saveXML($r_ . $this->tipoDocumento . '-' . $docAnte->infoTributaria->estab . '-' .  $docAnte->infoTributaria->ptoEmi . '-' . str_pad($docAnte->infoTributaria->secuencial, 9, 0, STR_PAD_LEFT) . '.xml');
        }
        return $archivo_firmado;
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

    function getInfoTributaria($xmlDocument, &$campos)
    {
        // $empresa = [
        //     'tipoAmbiente' => 1,
        //     'tipoEmision' => 1,
        //     'razonSocial' => $campos['infoTributaria']['razonSocial'],
        //     'nombreComercial' => $campos['infoTributaria']['nombreComercial'],
        //     'ruc' => $campos['infoTributaria']['ruc'],
        //     'claveAcceso' => $campos['infoTributaria']['claveAcceso'],
        //     'codDoc' => $campos['infoTributaria']['codDoc'],
        //     'estab' => $campos['infoTributaria']['estab'],
        //     'ptoEmi' => $campos['infoTributaria']['ptoEmi'],
        //     'secuencial' => $campos['infoTributaria']['secuencial'],
        //     'dirMatriz' => $campos['infoTributaria']['dirMatriz'],
        //     'agenteRetencion' => 0,
        //     'rimpe_emprendedor' => 0,
        //     'rimpe_popular' => 0,
        // ];

        //dd($empresa);
        $empresa = $this->cargarDataEmpresa($campos);
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
        if ($campos['infoTributaria']['agenteRetencion'] != 0)
            $nodoDetalle->appendChild($xmlDocument->createElement('agenteRetencion', "201"));
        elseif ($campos['infoTributaria']['rimpe_emprendedor'] == 1)
            $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteRimpe', "CONTRIBUYENTE RÉGIMEN RIMPE"));
        elseif ($campos['infoTributaria']['rimpe_popular'] == 1)
            $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteRimpe', "CONTRIBUYENTE RÉGIMEN POPULAR"));
        return $nodoDetalle;
    }

    public function getInfoGuiaRemision($xmlDocument, $campos)
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

    public function getInfoFactura($xmlDocument, $campos)
    {
        $moneda = 'DOLAR';
        $nodoDetalle = $xmlDocument->createElement('infoFactura');
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $this->getDate($campos['infoFactura']["fecha_emision"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoFactura']["dirEstablecimiento"]));
        if ($campos['infoFactura']["contribuyenteEspecial"]) $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos['infoFactura']["contribuyenteEspecial"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos['infoFactura']["obligadoContabilidad"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionComprador', $campos['infoFactura']["tipoIdentificacionComprador"]));
        if ($campos['infoFactura']["guiaRemision"]) $nodoDetalle->appendChild($xmlDocument->createElement('guiaRemision', $campos['infoFactura']["guiaRemision"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialComprador', $campos['infoFactura']["razonSocialComprador"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionComprador', $campos['infoFactura']["identificacionComprador"]));
        if ($campos['infoFactura']["direccionComprador"]) $nodoDetalle->appendChild($xmlDocument->createElement('direccionComprador', $campos['infoFactura']["direccionComprador"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalSinImpuestos', $this->getDecimal($campos['infoFactura']["totalSinImpuestos"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalDescuento', $this->getDecimal($campos['infoFactura']["totalDescuento"])));
        $totalConImpuestos = $campos['infoFactura']["totalConImpuestos"];
        $nodoDetalle->appendChild('totalConImpuestos', $this->totalConImpuestos($xmlDocument, $totalConImpuestos));
        $nodoDetalle->appendChild($xmlDocument->createElement('propina', $this->getDecimal($campos['infoFactura']["propina"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('importeTotal', $this->getDecimal($campos['infoFactura']["importeTotal"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('moneda', $moneda));
        if ($campos['infoFactura']["moneda"]) $nodoDetalle->appendChild($xmlDocument->createElement('moneda', $campos['infoFactura']["moneda"]));
        $pagos = $campos['infoFactura']["pagos"];
        $nodoDetalle->appendChild($this->formaPago($xmlDocument, $pagos));
        if ($campos['infoFactura']["valorRetIva"]) $nodoDetalle->appendChild($xmlDocument->createElement('valorRetIva', $campos['infoFactura']["valorRetIva"]));
        if ($campos['infoFactura']["valorRetRenta"]) $nodoDetalle->appendChild($xmlDocument->createElement('valorRetRenta', $campos['infoFactura']["valorRetRenta"]));
        return $nodoDetalle;
    }

    public function getInfoNotaCredito($xmlDocument, $campos)
    {
        $moneda = 'DOLAR';
        $nodoDetalle = $xmlDocument->createElement('infoNotaCredito');
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $campos['infoNotaCredito']["fecha_emision"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoNotaCredito']["dirEstablecimiento"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionComprador', $campos['infoNotaCredito']["tipoIdentificacionComprador"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialComprador', $campos['infoNotaCredito']["razonSocialComprador"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionComprador', $campos['infoNotaCredito']["identificacionComprador"]));
        if ($campos['infoNotaCredito']["contribuyenteEspecial"]) $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos['infoNotaCredito']["contribuyenteEspecial"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos['infoNotaCredito']["obligadoContabilidad"]));
        if ($campos['infoNotaCredito']["rise"]) $nodoDetalle->appendChild($xmlDocument->createElement('rise', $campos['infoNotaCredito']["rise"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('codDocModificado', $this->getDate($campos['infoNotaCredito']["codDocModificado"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('numDocModificado', $this->getDate($campos['infoNotaCredito']["numDocModificado"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmisionDocSustento', $campos['infoNotaCredito']["fechaEmisionDocSustento"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalSinImpuestos', $campos['infoNotaCredito']["totalSinImpuestos"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('valorModificacion', $campos['infoNotaCredito']["valorModificacion"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('moneda', $moneda));
        $totalConImpuestos = $campos['infoNotaCredito']["totalConImpuestos"];
        $nodoDetalle->appendChild('totalConImpuestos', $this->totalConImpuestos($xmlDocument, $totalConImpuestos));
        $nodoDetalle->appendChild('motivo', $campos['infoNotaCredito']["motivo"]);
        return $nodoDetalle;
    }

    public function getInfoNotaDebito($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('infoNotaDebito');
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $campos['infoNotaDebito']["fecha_emision"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoNotaDebito']["dirEstablecimiento"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionComprador', $campos['infoNotaDebito']["tipoIdentificacionComprador"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialComprador', $campos['infoNotaDebito']["razonSocialComprador"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionComprador', $campos['infoNotaDebito']["identificacionComprador"]));
        if ($campos['infoNotaDebito']["contribuyenteEspecial"]) $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos['infoNotaDebito']["contribuyenteEspecial"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos['infoNotaDebito']["obligadoContabilidad"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('codDocModificado', $this->getDate($campos['infoNotaDebito']["codDocModificado"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('numDocModificado', $this->getDate($campos['infoNotaDebito']["numDocModificado"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmisionDocSustento', $campos['infoNotaDebito']["fechaEmisionDocSustento"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalSinImpuestos', $campos['infoNotaDebito']["totalSinImpuestos"]));
        $impuestos = $campos['infoNotaDebito']["impuestos"];
        $nodoDetalle->appendChild('impuestos', $this->getImpuestos($xmlDocument, $impuestos));
        $pagos = $campos['infoNotaDebito']["pagos"];
        $nodoDetalle->appendChild('Pagos', $this->formaPago($xmlDocument, $pagos));

        $nodoDetalle->appendChild($xmlDocument->createElement('motivo', $campos['infoNotaDebito']["motivo"]));
        return $nodoDetalle;
    }

    public function totalConImpuestos($xmlDocument, $totalConImpuestos)
    {
        $nodoTotalConImpuestos = $xmlDocument->createElement('totalConImpuestos');
        foreach ($totalConImpuestos as $totalImpuesto) {
            $nodoTotalImpuesto = $xmlDocument->createElement('totalImpuesto');
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('formaPago', $totalImpuesto["formaPago"]));
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('total', $this->getDecimal($totalImpuesto["total"])));
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('plazo', $totalImpuesto["plazo"]));
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('unidadTiempo', $totalImpuesto["unidadTiempo"]));
            $nodoTotalConImpuestos->appendChild('totalImpuesto', $nodoTotalImpuesto);
        }
        return $nodoTotalConImpuestos;
    }

    public function formaPago($xmlDocument, $pagos)
    {
        $nodoPagos = $xmlDocument->createElement('Pagos');
        foreach ($pagos as $pago) {
            $nodoPago = $xmlDocument->createElement('pago');
            $nodoPago->appendChild($xmlDocument->createElement('formaPago', $pago["formaPago"]));
            $nodoPago->appendChild($xmlDocument->createElement('total', $this->getDecimal($pago["total"])));
            $nodoPago->appendChild($xmlDocument->createElement('plazo', $pago["plazo"]));
            $nodoPago->appendChild($xmlDocument->createElement('unidadTiempo', $pago["unidadTiempo"]));
            $nodoPagos->appendChild($xmlDocument->createElement('pago', $nodoPago));
        }
        return $nodoPagos;
    }

    public function motivos($xmlDocument, $motivos)
    {
        $nodoMotivos = $xmlDocument->createElement('motivos');
        foreach ($motivos as $motivo) {
            $nodoMotivo = $xmlDocument->createElement('motivo');
            $nodoMotivo->appendChild($xmlDocument->createElement('razon', $motivo["razon"]));
            $nodoMotivo->appendChild($xmlDocument->createElement('valor', $this->getDecimal($motivo["valor"])));
            $nodoMotivos->appendChild($xmlDocument->createElement('motivo', $nodoMotivo));
        }
        return $nodoMotivos;
    }

    public function getDetalles($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('detalles');
        $detalles = $campos["detalles"];
        foreach ($detalles as $detalle) {
            $precioTotalSinImpuesto = $detalle["cantidad"] * $detalle["precio"];
            $item = $xmlDocument->createElement('detalle');
            $item->appendChild($xmlDocument->createElement('codigoPrincipal', $detalle["sku"]));
            $item->appendChild($xmlDocument->createElement('codigoAdicional', $detalle["sku"]));
            $item->appendChild($xmlDocument->createElement('descripcion', str_replace('&', 'y', $detalle["nombre"])));
            $item->appendChild($xmlDocument->createElement('cantidad', $this->getDecimal($detalle["cantidad"])));
            $item->appendChild($xmlDocument->createElement('precioUnitario', $this->getDecimal($detalle["precio"])));
            $item->appendChild($xmlDocument->createElement('descuento', $this->getDecimal($detalle["descuento"])));
            $item->appendChild($xmlDocument->createElement('precioTotalSinImpuesto', $this->getDecimal($precioTotalSinImpuesto)));
            if (count($detalle["detAdicionales"]) > 0) {
                $detallesAdicionales = $this->getDetallesAdicionales($xmlDocument, $detalle["detAdicionales"]);
                $item->appendChild($detallesAdicionales);
            }
            $impuestos = $this->getImpuestos($xmlDocument, $detalles);
            $item->appendChild($impuestos);
            $nodoDetalle->appendChild($item);
        }
        return $nodoDetalle;
    }

    public function getImpuestos($xmlDocument, $detalles)
    {
        $nodoDetalle = $xmlDocument->createElement('impuestos');
        foreach ($detalles as $detalle) {
            $item = $xmlDocument->createElement('impuesto');
            if ($detalle['tarifa'] == 12)
                $sri_tipo_impuesto_iva_id = 1;
            $impuestoIva = De_Codigo_Impuestos::where('id', $sri_tipo_impuesto_iva_id)->first();
            $baseImponible = $detalle["cantidad"] * $detalle["precio"];
            $impuesto = ($baseImponible * $detalle['tarifa']) / 100;
            $item->appendChild($xmlDocument->createElement('codigo', $impuestoIva->codigo_impuesto));
            $item->appendChild($xmlDocument->createElement('codigoPorcentaje', $impuestoIva->codigo));
            $item->appendChild($xmlDocument->createElement('tarifa', $detalle["tarifa"]));
            $item->appendChild($xmlDocument->createElement('baseImponible', $this->getDecimal($baseImponible)));
            $item->appendChild($xmlDocument->createElement('valor', $this->getDecimal($impuesto)));
            $nodoDetalle->appendChild($item);
        }
        return $nodoDetalle;
    }

    public function getDestinatarios($xmlDocument, $campos)
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
            $item->appendChild($xmlDocument->createElement('codDocSustento', str_pad($destinatario["codDocSustento"], 2, 0, STR_PAD_LEFT)));
            if (isset($destinatario["numDocSustento"])) {
                if ($destinatario["numDocSustento"] != '') {
                    $item->appendChild($xmlDocument->createElement('numDocSustento', $destinatario["numDocSustento"]));
                    if ($destinatario["numAutDocSustento"] != '0')
                        $item->appendChild($xmlDocument->createElement('numAutDocSustento', $destinatario["numAutDocSustento"]));
                    else
                        $item->appendChild($xmlDocument->createElement('numAutDocSustento', '9999999999'));
                    if ($campos['infoTributaria']['codDoc'] == 1) {
                        $cliente = $campos['cliente'];
                        $direccion = strtolower($cliente['direccion']);
                        $telefono = $cliente['telefono'];
                        $email = $cliente['email'];
                    } elseif ($campos['infoTributaria']['codDoc'] == 6) {
                    }
                    if ($email != "") array_push($destinatarios, array("nombre" => 'Email', "valor" => strtoupper($email)));
                }
            }
            $item->appendChild($xmlDocument->createElement('fechaEmisionDocSustento', $destinatario["fechaEmisionDocSustento"]));
            $nodoDetalleMercancia = $xmlDocument->createElement('detalles');
            $detalles = $destinatario['detalles'];

            foreach ($detalles as $detalle) {
                $itemMercancia = $xmlDocument->createElement('detalle');
                $itemMercancia->appendChild($xmlDocument->createElement('codigoInterno', $detalle['codigoPrincipal']));
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

    public function getDetallesAdicionales($xmlDocument, $detalles)
    {
        $nodoDetalle = $xmlDocument->createElement('detallesAdicionales');
        $count = 0;

        foreach ($detalles as $detalle) {
            $count++;
            if ($count > 3) break;
            $item = $xmlDocument->createElement('detAdicional');
            $item->setAttribute('nombre', $detalle['nombre']);
            $item->setAttribute('valor', $detalle['valor']);
            $nodoDetalle->appendChild($item);
        }
        return $nodoDetalle;
    }

    function getInfoAdicional($xmlDocument, $campos)
    {
        $resolucion = 0;
        $detalles = array();
        $cont = 0;
        $valor = '';
        $cantidadInfoAdicional = 0;
        $informacionAdicional = $campos['informacion_adicional'];
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
                array_push($detalles, array("nombre" => $nombre, "valor" => str_replace(' ', '', str_replace('(', '', str_replace(')', '', str_replace('-', '', $valor))))));
            }
        }

        array_push($detalles, array(
            "nombre" => 'RUC Proveedor',
            "valor" => '0993094072001'
        ));

        foreach ($detalles as $item) {
            $campoAdicional = $xmlDocument->createElement('campoAdicional', isset($item["valor"]) ? str_replace("'", "", str_replace('&', '&amp;', $item["valor"])) : 'NN');
            $campoAdicional->setAttribute('nombre', $item["nombre"]);
            $nodoDetalle->appendChild($campoAdicional);
        }
        // dd($nodoDetalle);
        return $nodoDetalle;
    }

    public function getDecimal($valor = 0, $digitos = 2, $formato = false)
    {
        if (trim($valor) == "") $valor = 0;
        $resultado = round($valor, $digitos, PHP_ROUND_HALF_UP);
        $resultado = number_format($resultado, $digitos, ".", $formato ? "," : "");
        return $resultado;
    }

    public function getDate($valor)
    {
        if (trim($valor) == "") return "";
        $result = date('d/m/Y', strtotime($valor));
        return $result;
    }

    public function getBool($valor)
    {
        $result = $valor == true || $valor == 1 || $valor == "1" ? "SI" : "NO";
        return $result;
    }
}
