<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\Utilidades;

class OpcionesDocumentosController extends Controller
{
    private $controlador = 'EmisionDocumentos';
    public function index(Request $r)
    {
        if (session(Auth::user()->id) == '') {
            return redirect('/');
        }
        if ($r->submodulo == 'descargarXML') {
            $xmlRespuesta = Utilidades::obtenercomprobanteSRI($r->clave);
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
                    $nodoMensajes = Utilidades::getMensajesAutorizacion($xml, $autorizacion->mensajes);
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
        } elseif ($r->submodulo == 'descargarPDF') {
            $xmlRespuesta = Utilidades::obtenercomprobanteSRI($r->clave);
            $docCod = substr($r->clave, 8, 2);
            $esta = substr($r->clave, 24, 3);
            $emis = substr($r->clave, 27, 3);
            $num = substr($r->clave, 30, 9);
            $clave = $r->clave;
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
                return $pdf->download($tipoText . $esta . '-' . $emis . '-' . $num . '.pdf');
                //return $pdf->stream($tipoText . $esta . '-' . $emis . '-' . $num . '.pdf');
            } catch (Exception $e) {
                echo $e->getMessage() . 'Linea: ' . $e->getLine();
                exit;
            }
        }
    }
}
