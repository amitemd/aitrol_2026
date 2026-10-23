<?php

namespace Sis_medico\v3\contable;

use Carbon\Carbon;
use DateTime;
use DOMDocument;
use Exception;
use Excel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDO;
use PHPExcel_Style_Alignment;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Guia_Remision_Cabecera;
use Sis_medico\Ct_Guia_Remision_Detalle;
use Sis_medico\Ct_Retenciones;
use Sis_medico\De_Documentos_Electronicos as DeDocumentosElectronicos;
use Sis_medico\Http\Controllers\EmisionDocumentosController;

class Tributario extends Model
{
    private static $modelo = 'Tributario';

    public static function formualrio104($r)
    {
        $arrayVentas = [];
        $arrayCompras = [];
        $ventas = Ventas::join('ct_asientos_cabecera as c', 'ct_ventas.id_asiento', 'c.id')
            ->where('ct_ventas.id_empresa', session('id_empresa'))
            ->whereYear('ct_ventas.fecha', $r->anio)
            ->whereMonth('ct_ventas.fecha', $r->mes)
            ->where('ct_ventas.estado', '!=', 0)
            ->get([
                'ct_ventas.subtotal_12',
                'ct_ventas.subtotal_0',
                'ct_ventas.id',
            ]);
        if (count($ventas) > 0) {
            $notaCredito = NotaCreditoClientes::whereYear('ct_nota_credito_clientes.fecha', $r->anio)
                ->whereMonth('ct_nota_credito_clientes.fecha', $r->mes)
                ->where('ct_nota_credito_clientes.estado', '!=', 0)
                ->get();
            $arrayVentas = [
                '401' => $ventas->sum('subtotal_12'),
                '411' => $ventas->sum('subtotal_12') - $notaCredito->sum('subtotal'),
            ];
        }
        $compras = Compras::join('ct_asientos_cabecera as c', 'ct_compras.id_asiento_cabecera', 'c.id')
            ->where('ct_compras.id_empresa', session('id_empresa'))
            ->whereYear('ct_compras.fecha', $r->anio)
            ->whereMonth('ct_compras.fecha', $r->mes)
            ->get([
                'ct_compras.subtotal_12',
                'ct_compras.subtotal_0',
                'ct_compras.tipo',
                'ct_compras.tipo_gasto',
                'ct_compras.id',
                'c.aparece_sri'
            ]);
        if (count($compras) > 0) {
            $arrayCompras = [
                '500' => $compras->where('tipo', 1)->where('aparece_sri', 1)->sum('subtotal_12'),
                '507' => $compras->where('tipo', 2)->sum('subtotal_0'),
                '501' => $compras->where('tipo', 2)->where('aparece_sri', 1)->where('tipo_gasto', '!=', null)->sum('subtotal_12')
            ];
        }
        $arrayFormulario104 = [
            'ventas' => $arrayVentas,
            'compras' => $arrayCompras
        ];
        return $arrayFormulario104;
    }
    public static function exportarXml($id)
    {
        try {
            $ats = Ats::getAts($id);
            $detalleAts = DetalleAts::where('ats_id', $id)
                ->get();
            $atsVentas = $detalleAts->where('tipo_comprobante', 'VEN-FA');
            $tiposDoc = MasterTipos::where('estado', 1)
                ->where('tipo', 1)
                ->pluck('codigo')
                ->toArray();
            $atsCompras = $detalleAts->whereIn('tipo_comprobante', $tiposDoc);
            $totalVentas = 0;
            if (count($atsVentas) > 0) {
                $totalVentas = 0;
            }
            $tipoID = "";
            if (strlen($ats->id_empresa) == 13) {
                $tipoID = "R"; // para ruc
            } elseif (strlen($ats->id_empresa) == 10) {
                $tipoID = "C"; // para cedula
            } elseif ($ats->id_empresa == "9999999999") {
                $tipoID = "F"; // para consumidor final
            } else {
                $tipoID = "P";
            }
            $separador = substr($ats->periodo, 4, -2);
            list($anio, $mes) = explode($separador, $ats->periodo);
            $xml = new DOMDocument('1.0', 'UTF-8');
            $comprobante = 'iva';
            $root = $xml->createElement($comprobante);
            $TipoIDInformante = $xml->createElement('TipoIDInformante', $tipoID);
            $root->appendChild($TipoIDInformante);
            $IdInformante = $xml->createElement('IdInformante', $ats->id_empresa);
            $root->appendChild($IdInformante);
            $razonSocial = $xml->createElement('razonSocial', str_replace('.', '', $ats->razonsocial));
            $root->appendChild($razonSocial);
            $Anio = $xml->createElement('Anio', $anio);
            $root->appendChild($Anio);
            $Mes = $xml->createElement('Mes', $ats->periodo1 !== null && (substr($ats->periodo1, 5, 2) == '06' || substr($ats->periodo1, 5, 2) == "12") ? substr($ats->periodo1, 5, 2) : substr($ats->periodo, 5, 2));
            $root->appendChild($Mes);
            if ($ats->periodo1 !== null) {
                $semestral = $xml->createElement("regimenMicroempresa", "SI");
                $root->appendChild($semestral);
            }
            $numEstabRuc = $xml->createElement('numEstabRuc', '001');
            $root->appendChild($numEstabRuc);
            $totalVentas = $xml->createElement('totalVentas', $totalVentas);
            $root->appendChild($totalVentas);
            $codigoOperativo = $xml->createElement('codigoOperativo', 'IVA');
            $root->appendChild($codigoOperativo);
            $detalleCompras = self::compras($xml, $atsCompras);
            if (count($detalleCompras) > 0) {
                $Com = $xml->createElement('compras');
                $root->appendChild($Com);
                for ($i = 0; $i < count($detalleCompras); $i++) {
                    $Com->appendChild($detalleCompras[$i]);
                }
            }
            $xml->appendChild($root);
            $xml->formatOutput = true;
            $comprobante = $xml->saveXML();
            /*header('Content-type: text/xml');
        echo $comprobante;
        exit;*/
            // Configura los encabezados para forzar la descarga del archivo
            header('Content-Type: application/xml');
            header('Content-Disposition: attachment; filename="' . $anio . '-' . $mes . '.xml"');
            header('Content-Length: ' . strlen($comprobante));

            // Envía el contenido del XML
            echo $comprobante;
            exit;
        } catch (Exception $t) {
            dd($t);
        }
    }
    public static function retencionesHechas($idCompra)
    {
        return Retenciones::join('ct_detalle_retenciones as d', 'ct_retenciones.id', 'd.id_retenciones')
            ->join('ct_porcentaje_retenciones as p', 'd.id_porcentaje', 'p.id')
            ->where('ct_retenciones.id_compra', $idCompra)
            ->where('ct_retenciones.estado', 1)
            ->get([
                'p.valor as porcentaje',
                'd.tipo',
                'd.totales',
                'd.id_porcentaje',
                'ct_retenciones.sucursal',
                'ct_retenciones.punto_emision',
                'ct_retenciones.autorizacion',
                'ct_retenciones.fecha',
                'd.base_imponible'
            ]);
    }
    public static function reenviar($r)
    {
        $documentoActual = self::consultaDocumento($r->tipo)
            ->where('id', $r->id)->where('id_empresa', session('id_empresa'))->first();
        if ($documentoActual === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'El documento no existe o no pertenece a la empresa activa.',
            ], 404);
        }

        $claveAnterior = self::claveAccesoDocumento($r->tipo, $documentoActual);
        if (strlen($claveAnterior) === 49 && ctype_digit($claveAnterior)) {
            $consultaSri = self::consultarEstadoSri($r);
            $resultadoSri = $consultaSri->getData(true);
            $estadoSri = isset($resultadoSri['estado_sri']) ? $resultadoSri['estado_sri'] : '';
            if (isset($resultadoSri['status']) && $resultadoSri['status'] === 'error') {
                return $consultaSri;
            }
            if (!in_array($estadoSri, ['SIN REGISTRO', 'NO AUTORIZADO'], true)) {
                return $consultaSri;
            }
        }

        DB::beginTransaction();
        try {
            $consulta = self::consultaDocumento($r->tipo);
            $documento = $consulta->where('id', $r->id)
                ->where('id_empresa', session('id_empresa'))
                ->lockForUpdate()
                ->first();

            if ($documento === null) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'El documento no existe o no pertenece a la empresa activa.',
                ], 404);
            }

            $validacion = self::validarParaReproceso($r->tipo, $documento);
            if (!$validacion['valido']) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se puede reprocesar: ' . implode(' ', array_slice($validacion['errores'], 0, 3)),
                    'errores' => $validacion['errores'],
                ], 422);
            }

            $intento = $validacion['intentos'] + 1;
            $ahora = date('Y-m-d H:i:s');
            DeLogFacturacion::insert([
                'tipo_documento' => self::codigoDocumento($r->tipo),
                'id_pasos' => self::estadoElectronicoDocumento($r->tipo, $documento),
                'id_documento' => $documento->id,
                'clave_acceso' => self::claveAccesoDocumento($r->tipo, $documento),
                'informacion' => 'Reproceso manual solicitado. Intento ' . $intento . ' de 3.',
                'comentario' => 'REPROCESO_MANUAL',
                'id_usuariomod' => Auth::id(),
                'id_usuariocrea' => Auth::id(),
                'ip_creacion' => $r->ip(),
                'ip_modificacion' => $r->ip(),
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);

            self::asignarEstadoElectronico($r->tipo, $documento, 0);
            $documento->save();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Documento validado y enviado para reproceso. Intento ' . $intento . ' de 3.',
                'intento' => $intento,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'No fue posible registrar el reproceso del documento.',
            ], 500);
        }
    }

    public static function consultarEstadoSri($r)
    {
        $documento = self::consultaDocumento($r->tipo)
            ->where('id', $r->id)->where('id_empresa', session('id_empresa'))->first();
        if ($documento === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'El documento no existe o no pertenece a la empresa activa.',
            ], 404);
        }

        $claveAcceso = self::claveAccesoDocumento($r->tipo, $documento);
        if (strlen($claveAcceso) !== 49 || !ctype_digit($claveAcceso)) {
            return response()->json([
                'status' => 'error',
                'message' => 'El documento todavía no tiene una clave de acceso válida para consultar al SRI.',
            ], 422);
        }

        $configuracion = DeEmpresas::where('id_empresa', $documento->id_empresa)->first();
        if ($configuracion === null || !in_array((int) $configuracion->ambiente, [1, 2], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'La empresa no tiene un ambiente válido para consultar al SRI.',
            ], 422);
        }

        try {
            $emision = new EmisionDocumentosController();
            $respuesta = $emision->autorizacion_sri($claveAcceso, (int) $configuracion->ambiente);
            $contenedor = isset($respuesta->RespuestaAutorizacionComprobante)
                ? $respuesta->RespuestaAutorizacionComprobante
                : null;
            if ($contenedor === null || (int) $contenedor->numeroComprobantes === 0
                || !isset($contenedor->autorizaciones->autorizacion)) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'El SRI todavía no registra un comprobante para esta clave de acceso.',
                    'estado_sri' => 'SIN REGISTRO',
                ]);
            }

            $autorizacion = $contenedor->autorizaciones->autorizacion;
            if (is_array($autorizacion)) {
                $autorizacion = reset($autorizacion);
            }
            $estadoSri = isset($autorizacion->estado) ? strtoupper(trim((string) $autorizacion->estado)) : 'DESCONOCIDO';
            $mensajeSri = self::mensajeAutorizacionSri($autorizacion);
            $tipoDocumento = self::codigoDocumento($r->tipo);

            if ($estadoSri === 'AUTORIZADO') {
                DB::transaction(function () use ($r, $documento, $claveAcceso, $respuesta, $tipoDocumento) {
                    $establecimiento = substr($claveAcceso, 24, 3);
                    $puntoEmision = substr($claveAcceso, 27, 3);
                    $secuencial = substr($claveAcceso, 30, 9);
                    if ($r->tipo === 'Factura') {
                        Ventas::where('id', $documento->id)->update([
                            'doc_electronico' => 5,
                            'estado_electronica' => 3,
                            'sucursal' => $establecimiento,
                            'punto_emision' => $puntoEmision,
                            'numero' => $secuencial,
                            'nro_comprobante' => $establecimiento . '-' . $puntoEmision . '-' . $secuencial,
                            'nro_autorizacion' => $claveAcceso,
                            'fecha_envio' => date('Y-m-d H:i:s'),
                        ]);
                    } elseif ($r->tipo === 'Retención') {
                        Retenciones::where('id', $documento->id)->update([
                            'doc_electronico' => 5,
                            'sucursal' => $establecimiento,
                            'punto_emision' => $puntoEmision,
                            'nro_secuencial' => $secuencial,
                            'secuencia' => $secuencial,
                            'nro_comprobante' => $establecimiento . '-' . $puntoEmision . '-' . $secuencial,
                            'nro_autorizacion' => $claveAcceso,
                            'autorizacion' => $claveAcceso,
                        ]);
                    } elseif ($r->tipo === 'Liquidación de compra') {
                        Compras::where('id', $documento->id)->update([
                            'doc_electronico' => 5,
                            'sucursal' => $establecimiento,
                            'punto_emision' => $puntoEmision,
                            'secuencia_f' => $secuencial,
                            'numero' => $establecimiento . '-' . $puntoEmision . '-' . $secuencial,
                            'nro_comprobante' => $establecimiento . '-' . $puntoEmision . '-' . $secuencial,
                            'nro_autorizacion' => $claveAcceso,
                            'autorizacion' => $claveAcceso,
                            'serie' => $establecimiento . '-' . $puntoEmision,
                        ]);
                    } elseif ($r->tipo === 'Nota de crédito') {
                        NotaCreditoClientes::where('id', $documento->id)->update([
                            'doc_electronico' => 5,
                            'sucursal' => $establecimiento,
                            'punto_emision' => $puntoEmision,
                            'secuencia' => $secuencial,
                            'nro_comprobante' => $establecimiento . '-' . $puntoEmision . '-' . $secuencial,
                            'nro_autorizacion' => $claveAcceso,
                        ]);
                    } else {
                        Ct_Guia_Remision_Cabecera::where('id', $documento->id)->update([
                            'estado' => 5,
                            'establecimiento' => $establecimiento,
                            'punto_emision' => $puntoEmision,
                            'num_secuencial' => $secuencial,
                            'clave_acceso' => $claveAcceso,
                        ]);
                    }
                    DocumentosElectronicos::where('id_documento', $documento->id)
                        ->where('id_maestro_documento', self::maestroDocumento($r->tipo))
                        ->update([
                            'id_de_pasos' => 5,
                            'respuestaSriAutorizacion' => json_encode($respuesta),
                        ]);

                    $yaRegistrado = DeLogFacturacion::where('id_documento', $documento->id)
                        ->where('tipo_documento', $tipoDocumento)
                        ->where('id_pasos', 5)
                        ->exists();
                    if (!$yaRegistrado) {
                        $ahora = date('Y-m-d H:i:s');
                        DeLogFacturacion::insert([
                            'tipo_documento' => $tipoDocumento,
                            'id_pasos' => 5,
                            'id_documento' => $documento->id,
                            'clave_acceso' => $claveAcceso,
                            'informacion' => 'Documento autorizado y sincronizado mediante consulta manual al SRI.',
                            'comentario' => 'CONSULTA_MANUAL_SRI',
                            'id_usuariomod' => Auth::id(),
                            'id_usuariocrea' => Auth::id(),
                            'ip_creacion' => $r->ip(),
                            'ip_modificacion' => $r->ip(),
                            'created_at' => $ahora,
                            'updated_at' => $ahora,
                        ]);
                    }
                });

                return response()->json([
                    'status' => 'success',
                    'message' => 'El SRI confirma que el documento está autorizado. El estado local fue sincronizado.',
                    'estado_sri' => $estadoSri,
                ]);
            }

            if ($estadoSri === 'EN PROCESO') {
                self::asignarEstadoElectronico($r->tipo, $documento, 12);
                $documento->save();
            } elseif ($estadoSri === 'NO AUTORIZADO') {
                self::asignarEstadoElectronico($r->tipo, $documento, 10);
                $documento->save();
            }

            return response()->json([
                'status' => 'info',
                'message' => 'Estado informado por el SRI: ' . $estadoSri . ($mensajeSri !== '' ? '. ' . $mensajeSri : ''),
                'estado_sri' => $estadoSri,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No fue posible consultar al SRI. Verifique la conexión e intente nuevamente.',
            ], 503);
        }
    }
    public static function verLog($r)
    {
        $html = '';
        $tipoMaestro = self::maestroDocumento($r->tipo);
        if (!self::documentoPerteneceAEmpresa($r->tipo, $r->id)) {
            return self::mensajeDocumentoNoDisponible();
        }

        $deDoc = DocumentosElectronicos::where('id_documento', $r->id)
            ->where('id_maestro_documento', $tipoMaestro)
            ->orderBy('id', 'desc')
            ->first();
        if ($deDoc !== null) {
            $log = DeLogErrores::where('id_de_documentos_electronicos', $deDoc->id)
                ->orderBy('id', 'desc')
                ->limit(100)
                ->get();
            if (count($log) > 0) {
                $html .= '<table style="width: 100%;" class="table">';
                $html .= '<tr>
                        <td>Id</td>
                        <td>Fecha</td>
                        <td>Descripción</td>
                        <td>Ip</td>
                        <td>...</td>
                    </tr>';
                foreach ($log as $l) {
                    $html .= '<tr>
                            <td>' . self::escapar($l->id) . '</td>
                            <td style="text-align:center">' . self::escapar($l->created_at) . '</td>
                            <td>' . self::escapar($l->descripcion_error) . '</td>
                            <td style="text-align:center">' . self::escapar($l->ip_creacion) . '</td>
                            <td style="text-align:center"><button type="button" class="btn btn-sm btn-warning js-reenviar-documento" data-id="' . (int) $r->id . '" data-tipo="' . self::escapar($r->tipo) . '" title="Reenviar"><i class="fa fa-refresh"></i></button></td>
                        </tr>';
                }
                $html .= '</table>';
            }
        }
        return $html !== '' ? $html : '<div class="alert alert-info">No existen errores registrados para este documento.</div>';
    }

    public static function verLogDocumento($r)
    {
        if (!self::documentoPerteneceAEmpresa($r->tipo, $r->id)) {
            return self::mensajeDocumentoNoDisponible();
        }

        $documento = self::consultaDocumento($r->tipo)
            ->where('id', $r->id)->where('id_empresa', session('id_empresa'))->first();
        $validacion = self::validarParaReproceso($r->tipo, $documento);
        $html = self::panelValidacionReproceso($r->tipo, $r->id, $validacion);
        $tipo = self::maestroDocumento($r->tipo);
        $tipodoc = self::codigoDocumento($r->tipo);
        $logFac = DeLogFacturacion::where('id_documento', $r->id)
            ->where('tipo_documento', $tipodoc)
            ->orderBy('id', 'desc')
            ->limit(100)
            ->get();
        if (count($logFac) > 0) {
            $html .= '<table style="width: 100%;" class="table">';
            $html .= '<tr>
                <td width="10%"><b>Id</b></td>
                <td width="20%"><b>Fecha</b></td>
                <td width="20%"><b>Descripción</b></td>
                <td width="50%"><b>Clave Acceso</b></td>
            </tr>';
            foreach ($logFac as $l) {
                $html .= '<tr>
                    <td>' . self::escapar($l->id) . '</td>
                    <td style="text-align:center">' . self::escapar($l->created_at) . '</td>
                    <td>' . self::escapar($l->informacion) . '</td>
                    <td style="text-align:center">' . self::escapar($l->clave_acceso) . '</td>
                    <td></td>
                </tr>';
            }
            $html .= '</table>';
        }
        
        $deDoc = DocumentosElectronicos::where('id_documento', $r->id)
            ->where('id_maestro_documento', $tipo)
            ->orderBy('id', 'desc')
            ->first();
        if ($deDoc !== null) {
            $log = DeLogErrores::where('id_de_documentos_electronicos', $deDoc->id)->orderBy('id', 'desc')->limit(100)->get();
            if (count($log) > 0) {
                    $html .= '<span class="badge badge-danger" style="font-size: 20px">Log Errores</span> <table style="width: 100%;" class="table">';
                    $html .= '<tr>
                        <td width="10%"><b>Id</b></td>
                        <td width="20%"><b>Fecha</b></td>
                        <td width="20%"><b>Estado</b></td>
                        <td width="50%"><b>Descripción</b></td>
                    </tr>';
                    foreach ($log as $lg) {
                        list($estado, $msj) = self::resumenErrorSri($lg->descripcion_error);
                        $html .= '<tr>
                            <td>' . self::escapar($lg->id) . '</td>
                            <td style="text-align:center">' . self::escapar($lg->created_at) . '</td>
                            <td>' . self::escapar($estado) . '</td>
                            <td>' . self::escapar($msj) . '</td>
                            <td style="text-aling:center"></td>
                        </tr>';
                    }
                    $html .= '</table>';
            }
        }
        return $html !== '' ? $html : '<div class="alert alert-info">No existen eventos registrados para este documento.</div>';
    }

    private static function validarParaReproceso($tipo, $documento)
    {
        $errores = [];
        $prevalidacion = null;
        $tipoDocumento = self::codigoDocumento($tipo);
        $estadoElectronico = self::estadoElectronicoDocumento($tipo, $documento);
        $intentos = DeLogFacturacion::where('id_documento', $documento->id)
            ->where('tipo_documento', $tipoDocumento)
            ->where('comentario', 'REPROCESO_MANUAL')
            ->count();

        $autorizado = $estadoElectronico === 5
            || DeLogFacturacion::where('id_documento', $documento->id)
                ->where('tipo_documento', $tipoDocumento)
                ->where('id_pasos', 5)
                ->exists();
        if ($autorizado) {
            $errores[] = 'El documento ya está autorizado y no puede volver a enviarse.';
        } elseif (!in_array($estadoElectronico, [6, 7, 8, 9, 10], true)) {
            $errores[] = 'Solo se reprocesan documentos detenidos en un estado de error.';
        }

        if ($intentos >= 3) {
            $errores[] = 'Ya se utilizaron los tres intentos manuales permitidos.';
        }
        if ($tipo !== 'Guía de remisión'
            && (($tipo === 'Liquidación de compra' && (int) $documento->estado === 0)
                || ($tipo !== 'Liquidación de compra' && (int) $documento->estado !== 1))) {
            $errores[] = 'El documento está anulado o inactivo.';
        }
        if ($tipo !== 'Guía de remisión' && (int) $documento->electronica !== 1) {
            $errores[] = 'El documento no está marcado como electrónico.';
        }
        $claveAcceso = self::claveAccesoDocumento($tipo, $documento);
        $sucursalDocumento = $tipo === 'Guía de remisión' ? $documento->establecimiento : $documento->sucursal;
        if ((empty($sucursalDocumento) || empty($documento->punto_emision))
            && !(strlen($claveAcceso) === 49 && ctype_digit($claveAcceso))) {
            $errores[] = 'Falta el establecimiento o el punto de emisión.';
        }
        $establecimientoDocumento = strlen($claveAcceso) === 49 && ctype_digit($claveAcceso)
            ? substr($claveAcceso, 24, 3)
            : $sucursalDocumento;
        $puntoEmisionDocumento = strlen($claveAcceso) === 49 && ctype_digit($claveAcceso)
            ? substr($claveAcceso, 27, 3)
            : $documento->punto_emision;
        $numeroComprobante = $tipo === 'Liquidación de compra'
            ? ($documento->nro_comprobante ?: $documento->numero)
            : ($tipo === 'Guía de remisión'
                ? $documento->num_secuencial
                : $documento->nro_comprobante);
        if (empty($numeroComprobante)
            && !(strlen($claveAcceso) === 49 && ctype_digit($claveAcceso))) {
            $errores[] = 'Falta el número de comprobante.';
        }

        $deEmpresa = DeEmpresas::where('id_empresa', $documento->id_empresa)->first();
        if ($deEmpresa === null) {
            $errores[] = 'La empresa no tiene configuración de facturación electrónica.';
        } else {
            if (!in_array((int) $deEmpresa->ambiente, [1, 2], true)) {
                $errores[] = 'El ambiente de facturación electrónica no es válido.';
            }
            if (empty($deEmpresa->ruta_firma)) {
                $errores[] = 'No se ha configurado la firma electrónica.';
            } elseif (!file_exists(base_path() . '/storage/app/facturaelectronica/p12/' . $deEmpresa->ruta_firma)) {
                $errores[] = 'No se encuentra el archivo de la firma electrónica.';
            }
        }

        $sucursalExiste = Sucursales::where('id_empresa', $documento->id_empresa)
            ->where('codigo_sucursal', $establecimientoDocumento)
            ->where('estado', 1)
            ->exists();
        if (!$sucursalExiste) {
            $errores[] = 'La sucursal del documento no existe o está inactiva.';
        }

        $maestro = MaestroDocumentos::where('codigo', $tipoDocumento)->first(['id']);
        $configuracionTributaria = $maestro !== null
            && InfoTributaria::where('id_empresa', $documento->id_empresa)
                ->where('cod_sucursal', $establecimientoDocumento)
                ->where('cod_caja', $puntoEmisionDocumento)
                ->where('id_maestro_documentos', $maestro->id)
                ->where('estado', 1)
                ->exists();
        if (!$configuracionTributaria) {
            $errores[] = 'No existe configuración tributaria para la sucursal y punto de emisión.';
        }

        if ($tipo === 'Factura') {
            if ($documento->tipo === 'ND') {
                $errores[] = 'El registro corresponde a una nota de débito, no a una factura.';
            }
            if (!Clientes::where('identificacion', $documento->id_cliente)->exists()) {
                $errores[] = 'El cliente de la factura no está registrado.';
            }
            if (!DetalleVenta::where('id_ct_ventas', $documento->id)->exists()) {
                $errores[] = 'La factura no tiene detalle de productos o servicios.';
            }
        } elseif ($tipo === 'Retención') {
            if (!Proveedor::where('id', $documento->id_proveedor)->exists()) {
                $errores[] = 'El proveedor de la retención no está registrado.';
            }
            if (!DetalleRetenciones::where('id_retenciones', $documento->id)->exists()) {
                $errores[] = 'La retención no tiene detalle.';
            }
        } elseif ($tipo === 'Liquidación de compra') {
            if ((string) $documento->tipo_comprobante !== '03') {
                $errores[] = 'El registro no corresponde a una liquidación de compra.';
            }
            if (!Proveedor::where('id', $documento->proveedor)->exists()) {
                $errores[] = 'El proveedor de la liquidación no está registrado.';
            }
            if (!DetalleCompras::where('id_ct_compras', $documento->id)->exists()) {
                $errores[] = 'La liquidación de compra no tiene detalle.';
            }
        } elseif ($tipo === 'Nota de crédito') {
            if (!Clientes::where('identificacion', $documento->id_cliente)->exists()) {
                $errores[] = 'El cliente de la nota de crédito no está registrado.';
            }
            $tieneDetalle = DetalleCreditoClientes::where('id_not_cred', $documento->id)->exists()
                || DB::table('ct_devolucion_productos')->where('id_nota_credito', $documento->id)->exists();
            if (!$tieneDetalle) {
                $errores[] = 'La nota de crédito no tiene detalle.';
            }
            if (empty($documento->id_factura)) {
                $errores[] = 'La nota de crédito no tiene una factura relacionada.';
            }
        } else {
            if (empty($documento->ci_ruc_trasnportista)) {
                $errores[] = 'La guía no tiene transportista.';
            }
            if (empty($documento->ci_destinatario) || empty($documento->razon_social_destinatario)) {
                $errores[] = 'La guía no tiene destinatario completo.';
            }
            if (!Ct_Guia_Remision_Detalle::where('id_cabecera_remision', $documento->id)->exists()) {
                $errores[] = 'La guía de remisión no tiene detalle de mercadería.';
            }
            if (empty($documento->fecha_ini) || empty($documento->fecha_fin)) {
                $errores[] = 'La guía no tiene fechas de transporte completas.';
            }
        }

        if (!$autorizado && in_array($estadoElectronico, [6, 7, 8, 9, 10], true)) {
            $emision = new EmisionDocumentosController();
            $prevalidacion = $emision->validarDocumentoGestion($tipo, $documento->id);
            if (!$prevalidacion['valido']) {
                $errores[] = 'El documento no supera la prevalidación de datos y esquema XSD.';
            }
        }

        return [
            'valido' => count($errores) === 0,
            'errores' => array_values(array_unique($errores)),
            'intentos' => $intentos,
            'disponibles' => max(0, 3 - $intentos),
            'estado_actual' => $estadoElectronico,
            'estado_nombre' => self::nombreEstadoDocumento($estadoElectronico),
            'clave_acceso' => self::claveAccesoDocumento($tipo, $documento),
            'puede_consultar_sri' => strlen(self::claveAccesoDocumento($tipo, $documento)) === 49,
            'prevalidacion' => $prevalidacion,
        ];
    }

    private static function claveAccesoDocumento($tipo, $documento)
    {
        if ($tipo === 'Factura') {
            $candidatos = [$documento->nro_autorizacion, $documento->clave_acceso];
        } elseif ($tipo === 'Retención') {
            $candidatos = [$documento->autorizacion, $documento->nro_autorizacion, $documento->clave_acceso];
        } elseif ($tipo === 'Liquidación de compra') {
            $candidatos = [$documento->nro_autorizacion, $documento->autorizacion, $documento->clave_acceso];
        } elseif ($tipo === 'Nota de crédito') {
            $candidatos = [$documento->nro_autorizacion, $documento->clave_acceso];
        } else {
            $candidatos = [$documento->clave_acceso];
        }
        foreach ($candidatos as $clave) {
            $clave = trim((string) $clave);
            if ($clave !== '') {
                return $clave;
            }
        }

        // En documentos antiguos la clave solo quedó registrada en la
        // bitácora electrónica y no en la tabla principal del comprobante.
        $claveGuardada = DeDocumentosElectronicos::where('id_documento', $documento->id)
            ->where('id_maestro_documento', self::maestroDocumento($tipo))
            ->where('ruc_emisor', session('id_empresa'))
            ->orderBy('id', 'desc')
            ->value('clave_acceso');
        $claveGuardada = trim((string) $claveGuardada);
        if ($claveGuardada !== '') {
            return $claveGuardada;
        }
        return '';
    }

    private static function nombreEstadoDocumento($estado)
    {
        $estados = [
            0 => 'En cola para emisión',
            1 => 'XML generado',
            2 => 'XML validado',
            3 => 'XML firmado',
            4 => 'Recibido por el SRI',
            5 => 'Autorizado',
            6 => 'Error de firma',
            7 => 'Error de generación o validación',
            8 => 'Error de datos',
            9 => 'Devuelto o sin respuesta',
            10 => 'No autorizado',
            11 => 'Notificado al cliente',
            12 => 'En proceso en el SRI',
        ];
        return isset($estados[$estado]) ? $estados[$estado] : 'Estado desconocido';
    }

    private static function mensajeAutorizacionSri($autorizacion)
    {
        if (!isset($autorizacion->mensajes->mensaje)) {
            return '';
        }
        $mensajes = $autorizacion->mensajes->mensaje;
        if (!is_array($mensajes)) {
            $mensajes = [$mensajes];
        }
        $resultado = [];
        foreach (array_slice($mensajes, 0, 3) as $mensaje) {
            $texto = isset($mensaje->mensaje) ? trim((string) $mensaje->mensaje) : '';
            $adicional = isset($mensaje->informacionAdicional) ? trim((string) $mensaje->informacionAdicional) : '';
            if ($texto !== '') {
                $resultado[] = $texto . ($adicional !== '' ? ': ' . $adicional : '');
            }
        }
        return implode(' ', $resultado);
    }

    private static function panelValidacionReproceso($tipo, $id, array $validacion)
    {
        $clase = $validacion['valido'] ? 'alert-success' : 'alert-warning';
        $html = '<div class="alert ' . $clase . '"><h5><i class="fa fa-clipboard"></i> Validación previa al reproceso</h5>';
        $html .= '<p>Estado actual: <b>' . self::escapar($validacion['estado_actual'] . ' - ' . $validacion['estado_nombre']) . '</b>.</p>';
        $html .= '<p>Intentos manuales usados: <b>' . (int) $validacion['intentos'] . ' de 3</b>. Disponibles: <b>' . (int) $validacion['disponibles'] . '</b>.</p>';
        $claveAcceso = trim((string) ($validacion['clave_acceso'] ?? ''));
        if (strlen($claveAcceso) === 49 && ctype_digit($claveAcceso)) {
            $urlReconstruir = url('api_doc_electronico') . '?opcion=reconstruirDocumento&clave=' . rawurlencode($claveAcceso) . '&forzar=1&archivo=';
            $html .= '<div class="mb-3"><b><i class="fa fa-download"></i> Recuperar archivos</b>'
                . '<p class="mb-2">Si los archivos fueron eliminados, tráigalos nuevamente desde el SRI para descargarlos.</p>'
                . '<a target="_blank" rel="noopener" class="btn btn-success mr-2" href="' . self::escapar($urlReconstruir . 'xml') . '"><i class="fa fa-file-code-o"></i> Traer XML</a>'
                . '<a target="_blank" rel="noopener" class="btn btn-info" href="' . self::escapar($urlReconstruir . 'pdf') . '"><i class="fa fa-file-pdf-o"></i> Traer PDF</a></div>';
        }
        if ($validacion['prevalidacion'] !== null) {
            $html .= '<button type="button" class="btn btn-secondary mr-2 js-validar-xsd-documento" data-id="' . (int) $id . '" data-tipo="' . self::escapar($tipo) . '"><i class="fa fa-file-code-o"></i> Validar nuevamente XML/XSD</button>';
        }
        if ($validacion['puede_consultar_sri']) {
            $html .= '<button type="button" class="btn btn-info mr-2 js-consultar-sri-documento" data-id="' . (int) $id . '" data-tipo="' . self::escapar($tipo) . '"><i class="fa fa-cloud"></i> Consultar y sincronizar con SRI</button>';
        }
        if ($validacion['prevalidacion'] !== null) {
            $prevalidacion = $validacion['prevalidacion'];
            $html .= '<hr><h6><i class="fa fa-code"></i> Resultado de datos y esquema XSD</h6>';
            if ($prevalidacion['valido']) {
                $html .= '<div class="alert alert-success mb-2">El XML de prueba fue generado con la información actual y cumple el esquema XSD del SRI. No fue firmado ni enviado.</div>';
            } else {
                $etapa = isset($prevalidacion['etapa']) ? strtoupper($prevalidacion['etapa']) : 'VALIDACIÓN';
                $html .= '<div class="alert alert-danger mb-2"><b>Etapa: ' . self::escapar($etapa) . '</b><ul class="mb-0">';
                foreach (array_slice($prevalidacion['errores'], 0, 25) as $errorXsd) {
                    $html .= '<li>' . self::escapar($errorXsd) . '</li>';
                }
                if (count($prevalidacion['errores']) > 25) {
                    $html .= '<li>Existen más errores. Corrija los anteriores y vuelva a validar.</li>';
                }
                $html .= '</ul></div>';
            }
        }
        if ($validacion['valido']) {
            $html .= '<p>La información mínima fue validada. El documento puede volver a colocarse en la cola de emisión.</p>';
            $html .= '<button type="button" class="btn btn-warning js-reenviar-documento" data-id="' . (int) $id . '" data-tipo="' . self::escapar($tipo) . '"><i class="fa fa-refresh"></i> Reprocesar documento</button>';
        } else {
            $html .= '<p>Corrija primero los siguientes puntos:</p><ul>';
            foreach ($validacion['errores'] as $error) {
                $html .= '<li>' . self::escapar($error) . '</li>';
            }
            $html .= '</ul>';
        }
        $html .= '</div>';
        return $html;
    }

    public static function verComprobante($r)
    {
        $html = '';
        $subtotal = 0;
        if ($r->tipo == 'Factura') {
            $tipodoc = '01';
            $venta = Ventas::join('ct_detalle_venta as d', 'ct_ventas.id', 'd.id_ct_ventas')
                ->join('ct_clientes as c', 'ct_ventas.id_cliente', 'c.identificacion')
                ->where('ct_ventas.id', $r->id)
                ->where('ct_ventas.id_empresa', session('id_empresa'))
                ->get([
                    'ct_ventas.id',
                    'ct_ventas.fecha',
                    'ct_ventas.nro_comprobante',
                    'ct_ventas.id_cliente',
                    'c.nombre as cliente',
                    'ct_ventas.nro_autorizacion',
                    'ct_ventas.dias_plazo',
                    'd.id_ct_productos',
                    'd.cantidad',
                    'd.descuento',
                    'd.nombre',
                    'd.precio',
                    'ct_ventas.total_final',
                    DB::raw('COALESCE(ct_ventas.subtotal_0,0) as subtotal_0'),
                    DB::raw('COALESCE(ct_ventas.subtotal_12,0) as subtotal_12'),
                    DB::raw('COALESCE(ct_ventas.iva_total,0) as iva_total'),
                    DB::raw('COALESCE(ct_ventas.descuento,0) as descuento_'),
                ]);
            if (count($venta) > 0) {
                $subtotal = ($venta[0]->subtotal_0 + $venta[0]->subtotal_12) - $venta[0]->descuento_;
                $logFac = DeLogFacturacion::where('id_documento', $venta[0]->id)->where('tipo_documento', $tipodoc)->orderBy('id', 'desc')->limit(100)->get();

                $generado = '<span class="text-danger"> <i class="fa-solid fa-ban"></i> </span>';
                $firmado = '<span class="text-danger"> <i class="fa-solid fa-ban"></i> </span>';
                $autorizado = '<span class="text-danger"> <i class="fa-solid fa-ban"></i> </span>';
                $entregado = '<span class="text-danger"> <i class="fa-solid fa-ban"></i> </span>';
                foreach ($logFac as $lfac) {
                    if ($lfac->id_pasos == 1) {
                        $generado = '<span class="text-success"> <i class="fa-regular fa-circle-check"></i> </span>';
                    }
                    if ($lfac->id_pasos == 3) {
                        $firmado = '<span class="text-success"> <i class="fa-regular fa-circle-check"></i> </span>';
                    }
                    if ($lfac->id_pasos == 5) {
                        $autorizado = '<span class="text-success"> <i class="fa-regular fa-circle-check"></i> </span>';
                    }
                    if ($lfac->id_pasos == 11) {
                        $entregado = '<span class="text-success"> <i class="fa-regular fa-circle-check"></i> </span>';
                    }

                }

                $html .= '<table style="width: 100%;" class="table">';
                $html .= '<tr>
                        <td>Fecha emisión: </td>
                        <td>' . self::escapar($venta[0]->fecha) . '</td>
                        <td>'.$generado. 'Generado</td>
                        <td>'.$firmado. 'Firmado</td>
                        <td>'.$autorizado. 'Autorizado</td>
                        <td>'.$entregado. 'Entregado</td>
                    </tr>
                    <tr>
                        <td>Tipo de comprobante: </td>
                        <td>Factura</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Número de comprobante: </td>
                        <td>' . self::escapar($venta[0]->nro_comprobante) . '</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Cliente: </td>
                        <td>' . self::escapar($venta[0]->cliente) . '</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Autorización: </td>
                        <td>' . self::escapar($venta[0]->nro_autorizacion) . '</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Plazo: </td>
                        <td>' . self::escapar($venta[0]->dias_plazo) . ' días</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>';
                $html .= '<tr><td colspan="6"><hr></td></tr>';
                $html .= '<tr>
                        <td colspan="6"><b>Detalle de la factura</b></td>
                    </tr>';
                $html .= '<tr>
                    <td colspan="6" style="width: 100%;">
                        <table style="width: 100%;">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th style="text-align: right;">Cantidad</th>
                            <th style="text-align: right;">Precio</th>
                            <th style="text-align: right;">Descuento</th>
                            <th style="text-align: right;">Total</th>
                        </tr>';
                foreach ($venta as $row) {
                    $total = ($row->precio * $row->cantidad) - $row->descuento;
                    $html .= '<tr>
                                <td>' . self::escapar($row->id_ct_productos) . '</td>
                                <td>' . self::escapar($row->nombre) . '</td>
                                <td style="text-align: right;">' . $row->cantidad . '</td>
                                <td style="text-align: right;">' . $row->precio . '</td>
                                <td style="text-align: right;">' . $row->descuento . '</td>
                                <td style="text-align: right;">' . number_format($total, 2, '.', ',') . '</td>
                            </tr>';
                }
                $html .= '</table>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Subtotal sin impuesto: </td>
                    <td style="text-align: right;">' . $venta[0]->subtotal_0 . '</td>
                </tr>
                 <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Subtotal con impuesto: </td>
                    <td style="text-align: right;">' . $venta[0]->subtotal_12 . '</td>
                </tr>
                 <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Descuento: </td>
                    <td style="text-align: right;">' . $venta[0]->descuento_ . '</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Subtotal: </td>
                    <td style="text-align: right;">' . number_format($subtotal, 2, '.', ', ') . '</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Iva: </td>
                    <td style="text-align: right;">' . number_format($venta[0]->iva_total, 2, '.', ', ') . '</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total a cobrar: </td>
                    <td style="text-align: right;">' . number_format($venta[0]->total_final, 2, '.', ', ') . '</td>
                </tr>';
                $html .= '</table>';
            }
        } elseif ($r->tipo === 'Liquidación de compra') {
            $liquidacion = Compras::leftJoin('proveedor as p', 'ct_compras.proveedor', 'p.id')
                ->where('ct_compras.id', $r->id)
                ->where('ct_compras.id_empresa', session('id_empresa'))
                ->where('ct_compras.tipo_comprobante', '03')
                ->first([
                    'ct_compras.id',
                    'ct_compras.fecha',
                    'ct_compras.numero',
                    'ct_compras.nro_comprobante',
                    'ct_compras.nro_autorizacion',
                    'ct_compras.subtotal_0',
                    'ct_compras.subtotal_12',
                    'ct_compras.descuento',
                    'ct_compras.iva_total',
                    'ct_compras.total_final',
                    DB::raw("COALESCE(p.razonsocial, p.nombrecomercial, 'Proveedor no registrado') as nombre_prov"),
                ]);
            if ($liquidacion !== null) {
                $detalles = DetalleCompras::where('id_ct_compras', $liquidacion->id)->get();
                $html .= '<table style="width:100%;" class="table">';
                $html .= '<tr><td>Fecha emisión:</td><td>' . self::escapar($liquidacion->fecha) . '</td></tr>';
                $html .= '<tr><td>Tipo de comprobante:</td><td>Liquidación de compra</td></tr>';
                $html .= '<tr><td>Número:</td><td>' . self::escapar($liquidacion->nro_comprobante ?: $liquidacion->numero) . '</td></tr>';
                $html .= '<tr><td>Proveedor:</td><td>' . self::escapar($liquidacion->nombre_prov) . '</td></tr>';
                $html .= '<tr><td>Autorización:</td><td>' . self::escapar($liquidacion->nro_autorizacion) . '</td></tr>';
                $html .= '<tr><td colspan="2"><b>Detalle de la liquidación</b></td></tr>';
                $html .= '<tr><td colspan="2"><table style="width:100%;"><tr><th>Código</th><th>Descripción</th><th class="text-right">Cantidad</th><th class="text-right">Precio</th><th class="text-right">Total</th></tr>';
                foreach ($detalles as $detalle) {
                    $totalDetalle = isset($detalle->extendido)
                        ? $detalle->extendido
                        : (((float) $detalle->precio * (float) $detalle->cantidad) - (float) $detalle->descuento);
                    $html .= '<tr><td>' . self::escapar($detalle->codigo) . '</td><td>' . self::escapar($detalle->nombre ?: $detalle->detalle) . '</td><td class="text-right">' . self::escapar($detalle->cantidad) . '</td><td class="text-right">' . number_format((float) $detalle->precio, 2, '.', ',') . '</td><td class="text-right">' . number_format((float) $totalDetalle, 2, '.', ',') . '</td></tr>';
                }
                $html .= '</table></td></tr>';
                $html .= '<tr><td class="text-right"><b>Subtotal 0:</b></td><td class="text-right">' . number_format((float) $liquidacion->subtotal_0, 2, '.', ',') . '</td></tr>';
                $html .= '<tr><td class="text-right"><b>Subtotal IVA:</b></td><td class="text-right">' . number_format((float) $liquidacion->subtotal_12, 2, '.', ',') . '</td></tr>';
                $html .= '<tr><td class="text-right"><b>Descuento:</b></td><td class="text-right">' . number_format((float) $liquidacion->descuento, 2, '.', ',') . '</td></tr>';
                $html .= '<tr><td class="text-right"><b>IVA:</b></td><td class="text-right">' . number_format((float) $liquidacion->iva_total, 2, '.', ',') . '</td></tr>';
                $html .= '<tr><td class="text-right"><b>Total:</b></td><td class="text-right">' . number_format((float) $liquidacion->total_final, 2, '.', ',') . '</td></tr></table>';
            }
        } elseif ($r->tipo === 'Nota de crédito') {
            $nota = NotaCreditoClientes::leftJoin('ct_clientes as c', 'ct_nota_credito_clientes.id_cliente', 'c.identificacion')
                ->where('ct_nota_credito_clientes.id', $r->id)
                ->where('ct_nota_credito_clientes.id_empresa', session('id_empresa'))
                ->first([
                    'ct_nota_credito_clientes.*',
                    DB::raw("COALESCE(c.nombre, 'Cliente no registrado') as nombre_cliente"),
                ]);
            if ($nota !== null) {
                $html .= '<table style="width:100%;" class="table">';
                $html .= '<tr><td>Fecha emisión:</td><td>' . self::escapar($nota->fecha) . '</td></tr>';
                $html .= '<tr><td>Tipo:</td><td>Nota de crédito</td></tr>';
                $html .= '<tr><td>Número:</td><td>' . self::escapar($nota->nro_comprobante) . '</td></tr>';
                $html .= '<tr><td>Cliente:</td><td>' . self::escapar($nota->nombre_cliente) . '</td></tr>';
                $html .= '<tr><td>Factura relacionada:</td><td>' . self::escapar($nota->numero_factura) . '</td></tr>';
                $html .= '<tr><td>Motivo:</td><td>' . self::escapar($nota->concepto ?: $nota->observacion) . '</td></tr>';
                $html .= '<tr><td>Autorización:</td><td>' . self::escapar($nota->nro_autorizacion) . '</td></tr>';
                $html .= '<tr><td class="text-right"><b>Subtotal:</b></td><td class="text-right">' . number_format((float) $nota->subtotal, 2, '.', ',') . '</td></tr>';
                $html .= '<tr><td class="text-right"><b>Impuesto:</b></td><td class="text-right">' . number_format((float) $nota->impuesto, 2, '.', ',') . '</td></tr>';
                $html .= '<tr><td class="text-right"><b>Total crédito:</b></td><td class="text-right">' . number_format((float) $nota->total_credito, 2, '.', ',') . '</td></tr></table>';
            }
        } elseif ($r->tipo === 'Guía de remisión') {
            $guia = Ct_Guia_Remision_Cabecera::where('id', $r->id)
                ->where('id_empresa', session('id_empresa'))->first();
            if ($guia !== null) {
                $detalles = Ct_Guia_Remision_Detalle::where('id_cabecera_remision', $guia->id)->get();
                $html .= '<table style="width:100%;" class="table">';
                $html .= '<tr><td>Fecha emisión:</td><td>' . self::escapar($guia->fecha_emision_documento ?: $guia->created_at) . '</td></tr>';
                $html .= '<tr><td>Tipo:</td><td>Guía de remisión</td></tr>';
                $html .= '<tr><td>Número:</td><td>' . self::escapar($guia->establecimiento . '-' . $guia->punto_emision . '-' . str_pad($guia->num_secuencial, 9, '0', STR_PAD_LEFT)) . '</td></tr>';
                $html .= '<tr><td>Transportista:</td><td>' . self::escapar($guia->ci_ruc_trasnportista) . '</td></tr>';
                $html .= '<tr><td>Destinatario:</td><td>' . self::escapar($guia->razon_social_destinatario . ' - ' . $guia->ci_destinatario) . '</td></tr>';
                $html .= '<tr><td>Traslado:</td><td>' . self::escapar($guia->fecha_ini . ' a ' . $guia->fecha_fin) . '</td></tr>';
                $html .= '<tr><td>Motivo:</td><td>' . self::escapar($guia->motivo_traslado_destinatario) . '</td></tr>';
                $html .= '<tr><td>Clave de acceso:</td><td>' . self::escapar($guia->clave_acceso) . '</td></tr>';
                $html .= '<tr><td colspan="2"><b>Detalle de mercadería</b><table style="width:100%;"><tr><th>Código</th><th>Descripción</th><th class="text-right">Cantidad</th></tr>';
                foreach ($detalles as $detalle) {
                    $html .= '<tr><td>' . self::escapar($detalle->cod_principal) . '</td><td>' . self::escapar($detalle->descripcion ?: $detalle->observacion) . '</td><td class="text-right">' . self::escapar($detalle->cantidad) . '</td></tr>';
                }
                $html .= '</table></td></tr></table>';
            }
        } elseif ($r->tipo === 'Retención') {
            $tipodoc = '07';
            $retencion = Retenciones::join('ct_detalle_retenciones as d', 'ct_retenciones.id', 'd.id_retenciones')
                ->join('proveedor as p', 'ct_retenciones.id_proveedor', '=', 'p.id')
                ->where('ct_retenciones.id', $r->id)
                ->where('ct_retenciones.id_empresa', session('id_empresa'))
                ->get([
                    'ct_retenciones.id',
                    'ct_retenciones.fecha',
                    'ct_retenciones.nro_comprobante',
                    'ct_retenciones.id_proveedor',
                    'ct_retenciones.nro_autorizacion',
                    'p.razonsocial as nombre_prov',
                    'd.base_imponible',
                    'd.porcentaje',
                    'd.tipo',
                    'd.codigo',
                    'd.totales'
                ]);

            if (count($retencion) > 0) {
                $logFac = DeLogFacturacion::where('id_documento', $retencion[0]->id)->where('tipo_documento', $tipodoc)->orderBy('id', 'desc')->limit(100)->get();

                $generado = '<span class="text-danger"> <i class="fa-solid fa-ban"></i> </span>';
                $firmado = '<span class="text-danger"> <i class="fa-solid fa-ban"></i> </span>';
                $autorizado = '<span class="text-danger"> <i class="fa-solid fa-ban"></i> </span>';
                $entregado = '<span class="text-danger"> <i class="fa-solid fa-ban"></i> </span>';
                foreach ($logFac as $lfac) {
                    if ($lfac->id_pasos == 1) {
                        $generado = '<span class="text-success"> <i class="fa-regular fa-circle-check"></i> </span>';
                    }
                    if ($lfac->id_pasos == 3) {
                        $firmado = '<span class="text-success"> <i class="fa-regular fa-circle-check"></i> </span>';
                    }
                    if ($lfac->id_pasos == 5) {
                        $autorizado = '<span class="text-success"> <i class="fa-regular fa-circle-check"></i> </span>';
                    }
                    if ($lfac->id_pasos == 11) {
                        $entregado = '<span class="text-success"> <i class="fa-regular fa-circle-check"></i> </span>';
                    }

                }

                $html .= '<table style="width: 100%;" class="table">';
                $html .= '<tr>
                        <td>Fecha emisión: </td>
                        <td>' . self::escapar($retencion[0]->fecha) . '</td>
                        <td>'.$generado. 'Generado</td>
                        <td>'.$firmado. 'Firmado</td>
                        <td>'.$autorizado. 'Autorizado</td>
                        <td>'.$entregado. 'Entregado</td>
                    </tr>
                    <tr>
                        <td>Tipo de comprobante: </td>
                        <td>Retención</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Número de comprobante: </td>
                        <td>' . self::escapar($retencion[0]->nro_comprobante) . '</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Proveedor: </td>
                        <td>' . self::escapar($retencion[0]->nombre_prov) . '</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Autorización: </td>
                        <td>' . self::escapar($retencion[0]->nro_autorizacion) . '</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    ';
                $html .= '<tr><td colspan="6"><hr></td></tr>';
                $html .= '<tr>
                        <td colspan="6"><b>Detalle de la Retención</b></td>
                    </tr>';
                $html .= '<tr>
                <td colspan="6" style="width: 100%;">
                    <table style="width: 100%;">
                    <tr>
                        <th> Base Imponible </th>
                        <th> Impuesto </th>
                        <th> Código </th>
                        <th> Porcentaje Retención </th>
                        <th> Valor Retenido </th>
                    </tr>';
                $sum_totales = 0;
                foreach ($retencion as $row) {
                    $sum_totales += $row->totales;
                    $html .= '<tr>
                                <td>' . $row->base_imponible . '</td>
                                <td>' . self::escapar($row->tipo) . '</td>
                                <td>' . self::escapar($row->codigo) . '</td>
                                <td>' . $row->porcentaje . '%</td>
                                <td>' . number_format($row->totales, 2, '.', ',') . '</td>
                            </tr>';
                }

                $html .= '</table>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total retenido: </td>
                    <td >' . $sum_totales . '</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>';

                $html .= '</table>';
            }
        }
        return $html !== '' ? $html : self::mensajeDocumentoNoDisponible();
    }
    public static function buscarDocumentos($r)
    {
        $desde = Carbon::createFromFormat('Y-m-d', $r->desde)->startOfDay()->format('Y-m-d H:i:s');
        $hasta = Carbon::createFromFormat('Y-m-d', $r->hasta)->addDay()->startOfDay()->format('Y-m-d H:i:s');

        $facturas = Ventas::leftJoin('ct_clientes as c', 'ct_ventas.id_cliente', 'c.identificacion')
            ->leftJoin('de_pasos as p', 'ct_ventas.doc_electronico', 'p.id')
            ->where('ct_ventas.id_empresa', session('id_empresa'))
            ->where('ct_ventas.fecha', '>=', $desde)
            ->where('ct_ventas.fecha', '<', $hasta)
            ->where(function ($q) use ($r) {
                if ($r->estado != '') {
                    $q->where('ct_ventas.doc_electronico', $r->estado);
                }
            })
            ->select(
                'p.id as id_paso',
                'ct_ventas.doc_electronico',
                'ct_ventas.id',
                DB::raw("COALESCE(p.nombre, 'sin estado') as estado"),
                DB::raw("'Factura' as tipo"),
                'ct_ventas.fecha',
                'ct_ventas.nro_comprobante as comprobante',
                DB::raw('ROUND(((COALESCE(ct_ventas.subtotal_0, 0)+COALESCE(ct_ventas.subtotal_12, 0))-COALESCE(ct_ventas.descuento, 0))+COALESCE(ct_ventas.iva_total, 0),2) as total'),
                'ct_ventas.id_cliente as ruc',
                DB::raw("COALESCE(c.nombre, 'Cliente no registrado') as nombre"),
                DB::raw('(SELECT MAX(dlf.created_at) FROM de_log_facturacion dlf WHERE dlf.id_documento = ct_ventas.id AND dlf.tipo_documento = 1 AND dlf.id_pasos = 5) as fecha_autorizacion'),
                'ct_ventas.clave_acceso',
                'ct_ventas.nro_autorizacion as autorizacion',
                'ct_ventas.estado as estado_comprobante'
            );

        $retenciones = Retenciones::leftJoin('proveedor as pr', 'ct_retenciones.id_proveedor', 'pr.id')
            ->leftJoin('de_pasos as p', 'ct_retenciones.doc_electronico', 'p.id')
            ->where('ct_retenciones.id_empresa', session('id_empresa'))
            ->where('ct_retenciones.fecha', '>=', $desde)
            ->where('ct_retenciones.fecha', '<', $hasta)
            ->where(function ($q) use ($r) {
                if ($r->estado != '') {
                    $q->where('ct_retenciones.doc_electronico', $r->estado);
                }
            })
            ->select(
                'p.id as id_paso',
                'ct_retenciones.doc_electronico',
                'ct_retenciones.id',
                DB::raw("COALESCE(p.nombre, 'sin estado') as estado"),
                DB::raw("'Retención' as tipo"),
                'ct_retenciones.fecha',
                'ct_retenciones.nro_comprobante as comprobante',
                DB::raw('ROUND(COALESCE(ct_retenciones.valor_fuente, 0)+COALESCE(ct_retenciones.valor_iva, 0), 2) as total'),
                'pr.id as ruc',
                DB::raw("COALESCE(pr.razonsocial, pr.nombrecomercial, 'Proveedor no registrado') as nombre"),
                DB::raw('(SELECT MAX(dlf.created_at) FROM de_log_facturacion dlf WHERE dlf.id_documento = ct_retenciones.id AND dlf.tipo_documento = 7 AND dlf.id_pasos = 5) as fecha_autorizacion'),
                'ct_retenciones.autorizacion as clave_acceso',
                'ct_retenciones.nro_autorizacion as autorizacion',
                'ct_retenciones.estado as estado_comprobante'
            );
        $liquidaciones = Compras::leftJoin('proveedor as pr', 'ct_compras.proveedor', 'pr.id')
            ->leftJoin('de_pasos as p', 'ct_compras.doc_electronico', 'p.id')
            ->where('ct_compras.id_empresa', session('id_empresa'))
            ->where('ct_compras.tipo_comprobante', '03')
            ->where('ct_compras.electronica', 1)
            ->where('ct_compras.fecha', '>=', $desde)
            ->where('ct_compras.fecha', '<', $hasta)
            ->where(function ($q) use ($r) {
                if ($r->estado != '') {
                    $q->where('ct_compras.doc_electronico', $r->estado);
                }
            })
            ->select(
                'p.id as id_paso',
                'ct_compras.doc_electronico',
                'ct_compras.id',
                DB::raw("COALESCE(p.nombre, 'sin estado') as estado"),
                DB::raw("'Liquidación de compra' as tipo"),
                'ct_compras.fecha',
                DB::raw("COALESCE(NULLIF(ct_compras.nro_comprobante, ''), ct_compras.numero) as comprobante"),
                DB::raw('ROUND(COALESCE(ct_compras.total_final, 0), 2) as total'),
                'pr.id as ruc',
                DB::raw("COALESCE(pr.razonsocial, pr.nombrecomercial, 'Proveedor no registrado') as nombre"),
                DB::raw('(SELECT MAX(dlf.created_at) FROM de_log_facturacion dlf WHERE dlf.id_documento = ct_compras.id AND dlf.tipo_documento = 3 AND dlf.id_pasos = 5) as fecha_autorizacion'),
                'ct_compras.nro_autorizacion as clave_acceso',
                'ct_compras.autorizacion',
                'ct_compras.estado as estado_comprobante'
            );
        $notasCredito = NotaCreditoClientes::leftJoin('ct_clientes as c', 'ct_nota_credito_clientes.id_cliente', 'c.identificacion')
            ->leftJoin('de_pasos as p', 'ct_nota_credito_clientes.doc_electronico', 'p.id')
            ->where('ct_nota_credito_clientes.id_empresa', session('id_empresa'))
            ->where('ct_nota_credito_clientes.fecha', '>=', $desde)
            ->where('ct_nota_credito_clientes.fecha', '<', $hasta)
            ->where(function ($q) use ($r) {
                if ($r->estado != '') {
                    $q->where('ct_nota_credito_clientes.doc_electronico', $r->estado);
                }
            })
            ->select(
                'p.id as id_paso',
                'ct_nota_credito_clientes.doc_electronico',
                'ct_nota_credito_clientes.id',
                DB::raw("COALESCE(p.nombre, 'sin estado') as estado"),
                DB::raw("'Nota de crédito' as tipo"),
                'ct_nota_credito_clientes.fecha',
                'ct_nota_credito_clientes.nro_comprobante as comprobante',
                DB::raw('ROUND(COALESCE(ct_nota_credito_clientes.total_credito, 0), 2) as total'),
                'ct_nota_credito_clientes.id_cliente as ruc',
                DB::raw("COALESCE(c.nombre, 'Cliente no registrado') as nombre"),
                DB::raw('(SELECT MAX(dlf.created_at) FROM de_log_facturacion dlf WHERE dlf.id_documento = ct_nota_credito_clientes.id AND dlf.tipo_documento = 4 AND dlf.id_pasos = 5) as fecha_autorizacion'),
                'ct_nota_credito_clientes.nro_autorizacion as clave_acceso',
                'ct_nota_credito_clientes.nro_autorizacion as autorizacion',
                'ct_nota_credito_clientes.estado as estado_comprobante'
            );
        $guias = Ct_Guia_Remision_Cabecera::leftJoin('de_pasos as p', 'ct_cabecera_remision.estado', 'p.id')
            ->where('ct_cabecera_remision.id_empresa', session('id_empresa'))
            ->where('ct_cabecera_remision.created_at', '>=', $desde)
            ->where('ct_cabecera_remision.created_at', '<', $hasta)
            ->where(function ($q) use ($r) {
                if ($r->estado != '') {
                    $q->where('ct_cabecera_remision.estado', $r->estado);
                }
            })
            ->select(
                'p.id as id_paso',
                'ct_cabecera_remision.estado as doc_electronico',
                'ct_cabecera_remision.id',
                DB::raw("COALESCE(p.nombre, 'sin estado') as estado"),
                DB::raw("'Guía de remisión' as tipo"),
                DB::raw('COALESCE(ct_cabecera_remision.fecha_emision_documento, ct_cabecera_remision.created_at) as fecha'),
                DB::raw("CONCAT(ct_cabecera_remision.establecimiento, '-', ct_cabecera_remision.punto_emision, '-', LPAD(COALESCE(ct_cabecera_remision.num_secuencial, 0), 9, '0')) as comprobante"),
                DB::raw('0 as total'),
                'ct_cabecera_remision.ci_destinatario as ruc',
                DB::raw("COALESCE(ct_cabecera_remision.razon_social_destinatario, 'Destinatario no registrado') as nombre"),
                DB::raw('(SELECT MAX(dlf.created_at) FROM de_log_facturacion dlf WHERE dlf.id_documento = ct_cabecera_remision.id AND dlf.tipo_documento = 6 AND dlf.id_pasos = 5) as fecha_autorizacion'),
                'ct_cabecera_remision.clave_acceso',
                'ct_cabecera_remision.clave_acceso as autorizacion',
                DB::raw('1 as estado_comprobante')
            );
        if ($r->tipo == '01') {
            return $facturas->orderBy('fecha', 'desc')->limit(1000)->get();
        } elseif ($r->tipo == '03') {
            return $liquidaciones->orderBy('fecha', 'desc')->limit(1000)->get();
        } elseif ($r->tipo == '04') {
            return $notasCredito->orderBy('fecha', 'desc')->limit(1000)->get();
        } elseif ($r->tipo == '06') {
            return $guias->orderBy('fecha', 'desc')->limit(1000)->get();
        } elseif ($r->tipo == '07') {
            return $retenciones->orderBy('fecha', 'desc')->limit(1000)->get();
        } else {
            return $facturas->unionAll($liquidaciones)->unionAll($notasCredito)->unionAll($guias)->unionAll($retenciones)
                ->orderBy('fecha', 'desc')
                ->limit(1000)
                ->get();
        }
    }

    private static function documentoPerteneceAEmpresa($tipo, $id)
    {
        $consulta = self::consultaDocumento($tipo);
        return $consulta->where('id', $id)
            ->where('id_empresa', session('id_empresa'))
            ->exists();
    }

    private static function consultaDocumento($tipo)
    {
        if ($tipo === 'Factura') {
            return Ventas::query();
        }
        if ($tipo === 'Retención') {
            return Retenciones::query();
        }
        if ($tipo === 'Liquidación de compra') {
            return Compras::query()->where('tipo_comprobante', '03');
        }
        if ($tipo === 'Nota de crédito') {
            return NotaCreditoClientes::query();
        }
        return Ct_Guia_Remision_Cabecera::query();
    }

    private static function codigoDocumento($tipo)
    {
        if ($tipo === 'Factura') {
            return '01';
        }
        if ($tipo === 'Retención') {
            return '07';
        }
        if ($tipo === 'Liquidación de compra') {
            return '03';
        }
        return $tipo === 'Nota de crédito' ? '04' : '06';
    }

    private static function maestroDocumento($tipo)
    {
        $idMaestro = MaestroDocumentos::where('codigo', self::codigoDocumento($tipo))->value('id');
        if ($idMaestro !== null) {
            return $idMaestro;
        }
        $predeterminados = ['01' => 1, '03' => 2, '07' => 3, '04' => 4, '06' => 6];
        return $predeterminados[self::codigoDocumento($tipo)];
    }

    private static function estadoElectronicoDocumento($tipo, $documento)
    {
        return (int) ($tipo === 'Guía de remisión' ? $documento->estado : $documento->doc_electronico);
    }

    private static function asignarEstadoElectronico($tipo, $documento, $estado)
    {
        if ($tipo === 'Guía de remisión') {
            $documento->estado = $estado;
        } else {
            $documento->doc_electronico = $estado;
        }
    }

    private static function mensajeDocumentoNoDisponible()
    {
        return '<div class="alert alert-warning">El documento no existe o no pertenece a la empresa activa.</div>';
    }

    private static function escapar($valor)
    {
        return e((string) $valor);
    }

    private static function resumenErrorSri($valor)
    {
        $descripcion = json_decode($valor, true);
        if (!is_array($descripcion) || !isset($descripcion['RespuestaAutorizacionComprobante'])) {
            return ['Error', (string) $valor];
        }

        $autorizacion = isset($descripcion['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion'])
            ? $descripcion['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']
            : [];
        if (isset($autorizacion[0]) && is_array($autorizacion[0])) {
            $autorizacion = $autorizacion[0];
        }
        if (!is_array($autorizacion)) {
            return ['Error', (string) $valor];
        }
        $estado = isset($autorizacion['estado']) ? $autorizacion['estado'] : 'Error';
        $mensaje = isset($autorizacion['mensajes']['mensaje'])
            ? $autorizacion['mensajes']['mensaje']
            : [];

        if (isset($mensaje[0]) && is_array($mensaje[0])) {
            $mensaje = $mensaje[0];
        }

        if (!is_array($mensaje)) {
            return [$estado, (string) $valor];
        }

        $partes = [];
        foreach (['identificador', 'mensaje', 'informacionAdicional'] as $campo) {
            if (isset($mensaje[$campo]) && $mensaje[$campo] !== '') {
                $partes[] = $mensaje[$campo];
            }
        }

        return [$estado, count($partes) > 0 ? implode(' -- ', $partes) : (string) $valor];
    }
    public static function  verificarDocumento($idAutorizacion)
    {
        $docCod = substr($idAutorizacion, 8, 2);
        $variable = null;
        switch ($docCod) {
            case '01':
                $variable = Ct_compras::where("autorizacion", $docCod)->first();
                break;
            case '03':
                $variable = Ct_compras::where("autorizacion", $docCod)->first();
                break;
            case '04':
                $variable = Ct_compras::where("autorizacion", $docCod)->first();
                break;
            case '06':
                $variable =  GuiaRemision::where("autorizacion", $docCod)->first();
                break;
            case '07':
                $variable =  Ct_Retenciones::where("autorizacion", $docCod)->first();
                break;
            default:
                $variable = null;
                break;
        }
        return $variable;
    }
    public static function subirArchivoSri($r)
    {
        ini_set('max_execution_time', '300');
        $arrayDatos = [];
        $guardado = $_FILES[$r->tipo_archivo]['tmp_name'];
        $cont = 0;
        $contFila = 0;
        DB::table('log_importacion_sri')
            ->where('id_empresa', session('id_empresa'))
            ->delete();
        if ($da = fopen($guardado, "r")) {
            while ($aux = fgets($da, 1024)) {
                if ($cont > 0) {
                    $lineaArchivo = explode('	', $aux);
                    if (isset($lineaArchivo[4])) {
                        $rucReceptor = strlen($lineaArchivo[7]) == 10 ? $lineaArchivo[7] . '001' : $lineaArchivo[7];
                        if ($rucReceptor != session('id_empresa')) {
                            return [];
                        }
                        $contFila++;
                        $tipo = '';
                        $text = "Por subir";
                        $docCod = substr($lineaArchivo[4], 8, 2);
                        if ($docCod == '01') {
                            $tipo = 'Factura';
                            $verifarDocumento = self::verificarDocumento($lineaArchivo[4], 8, 2);
                            if (!is_null($verifarDocumento)) {
                                $text = "Subido";
                            }
                        } elseif ($docCod == '03') {
                            $tipo = 'Liquidación de compra';
                            $verifarDocumento = self::verificarDocumento($lineaArchivo[4], 8, 2);
                            if (!is_null($verifarDocumento)) {
                                $text = "Subido";
                            }
                        } elseif ($docCod == '04') {
                            $tipo = 'Nota de crédito';
                            $verifarDocumento = self::verificarDocumento($lineaArchivo[4], 8, 2);
                            if (!is_null($verifarDocumento)) {
                                $text = "Subido";
                            }
                        } elseif ($docCod == '05') {
                            $tipo = 'Nota de débito';
                            $verifarDocumento = self::verificarDocumento($lineaArchivo[4], 8, 2);
                            if (!is_null($verifarDocumento)) {
                                $text = "Subido";
                            }
                        } elseif ($docCod == '06') {
                            $tipo = 'Guia de remisión';
                            $verifarDocumento = self::verificarDocumento($lineaArchivo[4], 8, 2);
                            if (!is_null($verifarDocumento)) {
                                $text = "Subido";
                            }
                        } elseif ($docCod == '07') {
                            $tipo = 'Comprobante de retención'; //ct_retencion
                            $verifarDocumento = self::verificarDocumento($lineaArchivo[4], 8, 2);
                            if (!is_null($verifarDocumento)) {
                                $text = "Subido";
                            }
                        }
                        $ip_cliente = $_SERVER["REMOTE_ADDR"];
                        $arrayComprobante = [
                            'no' => $contFila,
                            'estado' => $text,
                            'tipo' => $tipo,
                            'num_comprobante' => $lineaArchivo[3], //1
                            'ruc_emisior' => $lineaArchivo[0], //2
                            'razon_social' => utf8_encode($lineaArchivo[1]), //3
                            'fecha_emision' => $lineaArchivo[6], //4
                            'fecha_autorizacion' => $lineaArchivo[5], //5
                            'clave_acceso' => $lineaArchivo[4], //9
                            'num_autorizacion' => $lineaArchivo[4], //10
                            'total' => $lineaArchivo[10], //11
                            'id_empresa' => session('id_empresa'),
                            'id_usuariocrea' => Auth::user()->id,
                            'id_usuariomod' => Auth::user()->id,
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente
                        ];
                        array_push($arrayDatos, $arrayComprobante);
                    }
                }
                $cont++;
            }
            fclose($da);
            return json_encode($arrayDatos);
        } else {
            echo "Error: no se ha podido leer el archivo <strong>" . $_FILES['docXML']['name'] . "</strong>";
        }
    }
    public static function getComprasMes($r)
    {
        return Compras::join('proveedor as p', 'ct_compras.proveedor', 'p.id')
            ->leftjoin('ct_master_tipos as m', function ($q) {
                $q->on('ct_compras.tipo_comprobante', 'm.codigo')
                    ->where('m.estado', '!=', 0)
                    ->where('m.tipo', 1);
            })
            ->where(function ($q) use ($r) {
                if (isset($r->anio) && $r->anio != '' && isset($r->mes) && $r->mes != '') {
                    $q->whereYear('ct_compras.fecha', $r->anio)
                        ->whereMonth('ct_compras.fecha', $r->mes);
                } elseif (isset($r->checkSemestral) && $r->checkSemestral != '') {
                    $semestral = $r->reservation;
                    list($start_date, $end_date) = explode(' - ', $semestral);
                    $start_date = Carbon::createFromFormat('d/m/Y', $start_date);
                    $end_date = Carbon::createFromFormat('d/m/Y', $end_date);
                    $q->whereBetween('ct_compras.fecha', [$start_date, $end_date])->get();
                }
            })
            ->where('ct_compras.estado', '!=', 0)
            ->where('ct_compras.id_empresa', session('id_empresa'))
            ->orderBy('ct_compras.fecha', 'asc')
            ->get([
                'ct_compras.id',
                'm.nombre',
                'ct_compras.tipo_comprobante',
                'ct_compras.id_asiento_cabecera',
                'ct_compras.fecha',
                'p.razonsocial',
                'ct_compras.proveedor',
                'ct_compras.observacion',
                'ct_compras.autorizacion',
                'ct_compras.numero',
                'ct_compras.subtotal_0',
                'ct_compras.subtotal_12',
                'ct_compras.descuento',
                DB::Raw('(ct_compras.subtotal_0+ct_compras.subtotal_12-ct_compras.descuento) as subtotal'),
                'ct_compras.iva_total as impuesto',
                DB::Raw('(ct_compras.subtotal_0+ct_compras.subtotal_12-ct_compras.descuento+ct_compras.iva_total) as total')
            ]);
    }
    public static function getVentasMes($r)
    {
        return Ventas::join('ct_clientes as c', 'ct_ventas.id_cliente', 'c.identificacion')
            ->leftJoin('ct_cliente_retencion as r', function ($q) {
                $q->on('ct_ventas.id', 'r.id_factura')
                    ->where('r.estado', '!=', 0);
            })
            ->leftJoin('ct_detalle_cliente_retencion as dr', 'r.id', 'dr.id_cliente_retencion')
            ->leftJoin('ct_porcentaje_retenciones as pr', 'dr.id_porcentaje', 'pr.id')
            ->where(function ($q) use ($r) {
                if (isset($r->anio) && $r->anio != '' && isset($r->mes) && $r->mes != '') {
                    $q->whereYear('ct_ventas.fecha', $r->anio)
                        ->whereMonth('ct_ventas.fecha', $r->mes);
                } elseif (isset($r->checkSemestral) && $r->checkSemestral != '') {
                    $semestral = $r->reservation;
                    list($start_date, $end_date) = explode(' - ', $semestral);
                    $start_date = Carbon::createFromFormat('d/m/Y', $start_date);
                    $end_date = Carbon::createFromFormat('d/m/Y', $end_date);
                    $q->whereBetween('ct_ventas.fecha', [$start_date, $end_date])->get();
                }
            })
            ->where('ct_ventas.estado', '!=', 0)
            ->where('ct_ventas.id_empresa', session('id_empresa'))
            ->orderBy('ct_ventas.nro_comprobante')
            ->groupBy('ct_ventas.id_asiento')
            ->get([
                'ct_ventas.id',
                'ct_ventas.tipo',
                'ct_ventas.id_asiento',
                'ct_ventas.fecha',
                'c.nombre',
                'ct_ventas.id_cliente',
                'ct_ventas.nro_autorizacion',
                'ct_ventas.concepto',
                'ct_ventas.nro_comprobante',
                'ct_ventas.subtotal_0',
                'ct_ventas.subtotal_12',
                'ct_ventas.descuento',
                DB::Raw('((ct_ventas.subtotal_0+ct_ventas.subtotal_12)-ct_ventas.descuento) as subtotal'),
                'impuesto',
                DB::Raw('((ct_ventas.subtotal_0+ct_ventas.subtotal_12)+ct_ventas.impuesto)-ct_ventas.descuento as total'),
                'dr.tipo as tiporet',
                'dr.id_porcentaje',
                DB::Raw('IFNULL(dr.totales,0) as totales'),
                DB::Raw('IFNULL(pr.valor,0) as valor')
            ]);
    }
    public static function compras($xmlDocument, $compras)
    {
        $codCompra = 0;
        $cont = 0;
        $detalles = [];
        $fechaEmision = '';
        foreach ($compras as $row) {
            $compra = null;
            if ($row->tipo_mov == "2") {
                $compra = Compras::find($row->mov_id);
            } elseif ($row->tipo_mov == "1") {
                $compra = Ventas::find($row->mov_id);
            }
            $fechaEmision = $row->emision;
            $comprobante = explode('-', $compra->numero);
            $cabecerasCompras = $xmlDocument->createElement('detalleCompras');
            $codCompra++;
            if ($compra->tipo_comprobante == '01' || $compra->tipo_comprobante == '')
                $cabecerasCompras->appendChild($xmlDocument->createElement('codSustento', $compra->credito_tributario));
            elseif ($compra->tipo_comprobante == '03')
                $cabecerasCompras->appendChild($xmlDocument->createElement('codSustento', '02'));
            else
                $cabecerasCompras->appendChild($xmlDocument->createElement('codSustento', $compra->credito_tributario));
            if ($compra->tipo_comprobante == '03') {
                $cabecerasCompras->appendChild($xmlDocument->createElement('tpIdProv', '02'));
            } else {
                $cabecerasCompras->appendChild($xmlDocument->createElement('tpIdProv', '01'));
            }
            $cabecerasCompras->appendChild($xmlDocument->createElement('idProv', $compra->proveedor));
            $cabecerasCompras->appendChild($xmlDocument->createElement('tipoComprobante', $compra->tipo_comprobante));
            $cabecerasCompras->appendChild($xmlDocument->createElement('parteRel', 'NO'));
            $cabecerasCompras->appendChild($xmlDocument->createElement('fechaRegistro', Utilidades::getDate($compra->fecha)));
            $cabecerasCompras->appendChild($xmlDocument->createElement('establecimiento', $comprobante[0]));
            $cabecerasCompras->appendChild($xmlDocument->createElement('puntoEmision', $comprobante[1]));
            $cabecerasCompras->appendChild($xmlDocument->createElement('secuencial', (int)$comprobante[2]));
            $cabecerasCompras->appendChild($xmlDocument->createElement('fechaEmision', Utilidades::getDate($compra->fecha)));
            $cabecerasCompras->appendChild($xmlDocument->createElement('autorizacion', str_replace(' ', '', $compra->autorizacion)));
            $cabecerasCompras->appendChild($xmlDocument->createElement('baseNoGraIva', Utilidades::getDecimal('0')));
            $cabecerasCompras->appendChild($xmlDocument->createElement('baseImponible', Utilidades::getDecimal($compra->subtotal_0)));
            $cabecerasCompras->appendChild($xmlDocument->createElement('baseImpGrav', Utilidades::getDecimal(($compra->subtotal_12))));
            $cabecerasCompras->appendChild($xmlDocument->createElement('baseImpExe', Utilidades::getDecimal('0')));
            $cabecerasCompras->appendChild($xmlDocument->createElement('montoIce', Utilidades::getDecimal($compra->ice_total)));
            $cabecerasCompras->appendChild($xmlDocument->createElement('montoIva', Utilidades::getDecimal($compra->iva_total)));
            $datoretenciones = self::retencionesHechas($compra->id);
            $iva10 = 0;
            $iva20 = 0;
            $iva30 = 0;
            $iva50 = 0;
            $iva70 = 0;
            $iva100 = 0;
            if (count($datoretenciones) > 0) {
                foreach ($datoretenciones as $rowq) {
                    if (number_format($rowq->porcentaje, 3) == '10.000' && $rowq->tipo == 'IVA')
                        $iva10 += $rowq->totales;
                    elseif (number_format($rowq->porcentaje, 3) == '20.000' && $rowq->tipo == 'IVA')
                        $iva20 += $rowq->totales;
                    elseif (number_format($rowq->porcentaje, 3) == '30.000' && $rowq->tipo == 'IVA')
                        $iva30 += $rowq->totales;
                    elseif (number_format($rowq->porcentaje, 3) == '50.000' && $rowq->tipo == 'IVA')
                        $iva50 += $rowq->totales;
                    elseif (number_format($rowq->porcentaje, 3) == '70.000' && $rowq->tipo == 'IVA')
                        $iva70 += $rowq->totales;
                    elseif (number_format($rowq->porcentaje, 3) == '100.000' && $rowq->tipo == 'IVA')
                        $iva100 += $rowq->totales;
                }
            }
            $cabecerasCompras->appendChild($xmlDocument->createElement('valRetBien10', Utilidades::getDecimal($iva10)));
            $cabecerasCompras->appendChild($xmlDocument->createElement('valRetServ20', Utilidades::getDecimal($iva20)));
            $cabecerasCompras->appendChild($xmlDocument->createElement('valorRetBienes', Utilidades::getDecimal($iva30)));
            $cabecerasCompras->appendChild($xmlDocument->createElement('valRetServ50', Utilidades::getDecimal($iva50)));
            $cabecerasCompras->appendChild($xmlDocument->createElement('valorRetServicios', Utilidades::getDecimal($iva70)));
            $cabecerasCompras->appendChild($xmlDocument->createElement('valRetServ100', Utilidades::getDecimal($iva100)));
            $cabecerasCompras->appendChild($xmlDocument->createElement('totbasesImpReemb', Utilidades::getDecimal('0')));
            $pagoExterior = $xmlDocument->createElement('pagoExterior');
            $pagoExterior->appendChild($xmlDocument->createElement('pagoLocExt', '01'));
            $pagoExterior->appendChild($xmlDocument->createElement('paisEfecPago', 'NA'));
            $pagoExterior->appendChild($xmlDocument->createElement('aplicConvDobTrib', 'NA'));
            $pagoExterior->appendChild($xmlDocument->createElement('pagExtSujRetNorLeg', 'NA'));
            $cabecerasCompras->appendChild($pagoExterior);
            $total = $compra->subtotal_0 + $compra->subtotal_12 + $compra->iva_total + $compra->ice_total;
            if ($total >= 1000) {
                $cabecerasCompra = $xmlDocument->createElement('formasDePago');
                $cabecerasCompra->appendChild($xmlDocument->createElement('formaPago', '20'));
                $cabecerasCompras->appendChild($cabecerasCompra);
            }
            $Air = $xmlDocument->createElement('air');
            $contRetencion = 0;
            $autorizacion_retencion = '9999999999';
            if (count($datoretenciones) > 0) {
                foreach ($datoretenciones as $retencion) {
                    $datoRetencion = PorcentajeRetenciones::where('id', $retencion->id_porcentaje)
                        ->where('tipo', 2)
                        ->first();
                    if ($datoRetencion != '') {
                        $contRetencion++;
                        if ($datoRetencion->codigo != '') {
                            // $codRetAir = $datoRetencion->codigo_nombre == '303A' ? '3030' : $datoRetencion->codigo_nombre;
                            $codRetAir = $datoRetencion->codigo_nombre;
                            $establecimiento_retencion = '001';
                            $conceproAir = $xmlDocument->createElement('detalleAir');
                            $basei = $retencion->base_imponible;
                            $conceproAir->appendChild($xmlDocument->createElement('codRetAir', $codRetAir));
                            $conceproAir->appendChild($xmlDocument->createElement('baseImpAir', Utilidades::getDecimal($basei)));
                            $conceproAir->appendChild($xmlDocument->createElement('porcentajeAir', number_format($datoRetencion->valor, 2)));
                            if ($codRetAir == '332')
                                $conceproAir->appendChild($xmlDocument->createElement('valRetAir', str_replace(',', '', number_format(0, 2))));
                            else
                                $conceproAir->appendChild($xmlDocument->createElement('valRetAir', str_replace(',', '', number_format($retencion->totales, 2))));
                            $Air->appendChild($conceproAir);
                            $cabecerasCompras->appendChild($Air);
                            if ($retencion->sucursal != '')
                                $establecimiento_retencion = $retencion->sucursal;
                            if ($retencion->autorizacion != '')
                                $autorizacion_retencion = str_replace(' ', '', $retencion->autorizacion);
                            $retencionset1 = (int)$retencion->secuencia;
                        }
                    }
                }
                if ($datoretenciones[0]->codigo_renta != '' && $datoretenciones[0]->num_retencion != '') {
                    $cabecerasCompras->appendChild($xmlDocument->createElement('estabRetencion1', $establecimiento_retencion));
                    $cabecerasCompras->appendChild($xmlDocument->createElement('ptoEmiRetencion1', $retencion->punto_emision));
                    $cabecerasCompras->appendChild($xmlDocument->createElement('secRetencion1', $retencionset1));
                    $cabecerasCompras->appendChild($xmlDocument->createElement('autRetencion1', str_replace(' ', '', $autorizacion_retencion)));
                    $cabecerasCompras->appendChild($xmlDocument->createElement('fechaEmiRet1', Utilidades::getDate($retencion->fecha)));
                }
            }
            $detalles[] = $cabecerasCompras;
            $iva10 = 0;
            $iva20 = 0;
            $iva30 = 0;
            $iva50 = 0;
            $iva70 = 0;
            $iva100 = 0;
        }
        $codCompraNC = 0;
        $docMismoPeriodo = CreditoAcreedores::whereYear('fecha', date('Y', strtotime($fechaEmision)))
            ->whereMonth('fecha', date('m', strtotime($fechaEmision)))
            ->where('estado', 1)
            ->get();
        if (count($docMismoPeriodo) > 0) {
            foreach ($docMismoPeriodo as $nc) {
                $comprobante = explode('-', $nc->serie . '-' . $nc->secuencia);
                $cabecerasCompras = $xmlDocument->createElement('detalleCompras');
                $codCompraNC++;

                $cabecerasCompras->appendChild($xmlDocument->createElement('codSustento', $nc->id_credito_tributario));
                if (strlen($nc->id_proveedor) == 13)
                    $cabecerasCompras->appendChild($xmlDocument->createElement('tpIdProv', '01'));
                else
                    $cabecerasCompras->appendChild($xmlDocument->createElement('tpIdProv', '02'));

                $cabecerasCompras->appendChild($xmlDocument->createElement('idProv', $nc->id_proveedor));
                $cabecerasCompras->appendChild($xmlDocument->createElement('tipoComprobante', '04'));
                $cabecerasCompras->appendChild($xmlDocument->createElement('parteRel', 'NO'));
                $cabecerasCompras->appendChild($xmlDocument->createElement('fechaRegistro', Utilidades::getDate($nc->fecha)));
                $cabecerasCompras->appendChild($xmlDocument->createElement('establecimiento', $comprobante[0]));
                $cabecerasCompras->appendChild($xmlDocument->createElement('puntoEmision', $comprobante[1]));
                $cabecerasCompras->appendChild($xmlDocument->createElement('secuencial', (int)$comprobante[2]));
                $cabecerasCompras->appendChild($xmlDocument->createElement('fechaEmision', Utilidades::getDate($nc->fecha)));
                $cabecerasCompras->appendChild($xmlDocument->createElement('autorizacion', str_replace(' ', '', $nc->autorizacion)));
                $cabecerasCompras->appendChild($xmlDocument->createElement('baseNoGraIva', Utilidades::getDecimal('0')));
                $cabecerasCompras->appendChild($xmlDocument->createElement('baseImponible', Utilidades::getDecimal($nc->subtotal_0)));
                $cabecerasCompras->appendChild($xmlDocument->createElement('baseImpGrav', Utilidades::getDecimal(($nc->subtotal_12))));
                $cabecerasCompras->appendChild($xmlDocument->createElement('baseImpExe', Utilidades::getDecimal('0')));
                $cabecerasCompras->appendChild($xmlDocument->createElement('montoIce', Utilidades::getDecimal(0)));
                $cabecerasCompras->appendChild($xmlDocument->createElement('montoIva', Utilidades::getDecimal($nc->impuesto)));
                $iva10 = 0;
                $iva20 = 0;
                $iva30 = 0;
                $iva50 = 0;
                $iva70 = 0;
                $iva100 = 0;
                $cabecerasCompras->appendChild($xmlDocument->createElement('valRetBien10', Utilidades::getDecimal($iva10)));
                $cabecerasCompras->appendChild($xmlDocument->createElement('valRetServ20', Utilidades::getDecimal($iva20)));
                $cabecerasCompras->appendChild($xmlDocument->createElement('valorRetBienes', Utilidades::getDecimal($iva30)));
                $cabecerasCompras->appendChild($xmlDocument->createElement('valRetServ50', Utilidades::getDecimal($iva50)));
                $cabecerasCompras->appendChild($xmlDocument->createElement('valorRetServicios', Utilidades::getDecimal($iva70)));
                $cabecerasCompras->appendChild($xmlDocument->createElement('valRetServ100', Utilidades::getDecimal($iva100)));
                $cabecerasCompras->appendChild($xmlDocument->createElement('totbasesImpReemb', Utilidades::getDecimal('0')));
                $pagoExterior = $xmlDocument->createElement('pagoExterior');
                $pagoExterior->appendChild($xmlDocument->createElement('pagoLocExt', '01'));
                $pagoExterior->appendChild($xmlDocument->createElement('paisEfecPago', 'NA'));
                $pagoExterior->appendChild($xmlDocument->createElement('aplicConvDobTrib', 'NA'));
                $pagoExterior->appendChild($xmlDocument->createElement('pagExtSujRetNorLeg', 'NA'));
                $cabecerasCompras->appendChild($pagoExterior);
                $total = $compra->subtotal_0 + $compra->subtotal_12 + $compra->impuesto;
                if ($total >= 1000) {
                    $cabecerasCompra = $xmlDocument->createElement('formasDePago');
                    $cabecerasCompra->appendChild($xmlDocument->createElement('formaPago', '20'));
                    $cabecerasCompras->appendChild($cabecerasCompra);
                }
                $compraNC = Compras::find($nc->id_compra);
                $numModificado = $compraNC->autorizacion != '' ? $compraNC->autorizacion : '9999999999';
                $serie = explode('-', $compraNC->serie);
                $cabecerasCompras->appendChild($xmlDocument->createElement('docModificado', $compraNC->tipo_comprobante));
                $cabecerasCompras->appendChild($xmlDocument->createElement('estabModificado', $serie[0]));
                $cabecerasCompras->appendChild($xmlDocument->createElement('ptoEmiModificado', $serie[1]));
                $cabecerasCompras->appendChild($xmlDocument->createElement('secModificado', $compraNC->secuencia_factura));
                $cabecerasCompras->appendChild($xmlDocument->createElement('autModificado', str_replace(' ', '', $numModificado)));
                $detalles[] = $cabecerasCompras;
            }
        }
        return $detalles;
    }

    public static function guardarDeclaracion($r)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        $cont = 0;
        $ip = Utilidades::getRealIP();
        $idusuario = Auth::user()->id;
        $id_empresa = session('id_empresa');
        try {
            $fecha1 = null;
            $periodo = $r->anio . '/' . (strlen($r->mes) == 1 ? "0" . $r->mes : $r->mes);
            if (isset($r->checkSemestral)) {
                $f = explode("-", $r->reservation);
                $periodo = str_replace("-", "/", date("Y-d", strtotime(trim($f[0]))));
                $fecha1 = str_replace("-", "/", date("Y-d", strtotime(trim($f[1]))));
            }
            $cabecera = [
                'periodo'         => $periodo,
                'periodo1'        => $fecha1,
                'id_empresa'      => $id_empresa,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_creacion'     => $ip,
                'ip_modificacion' => $ip,
            ];
            $ats_id = Ats::insertGetId($cabecera);
            $exp = explode("-", $r->reservation);
            if ($r->seleccionVentas != null) {
                for ($i = 0; $i < count($r->seleccionVentas); $i++) {

                    $ventas = Ventas::where('estado', '1')
                        ->where('id', $r['seleccionVentas'][$i])
                        ->where('id_empresa', $id_empresa)
                        ->where(function ($q) use ($r, $exp) {
                            if ($r->mes !== null && $r->anio !== null) {
                                $q->whereMonth('fecha', $r->mes)->whereYear('fecha', $r->anio);
                            } else {
                                $q->whereBetween("fecha", [DateTime::createFromFormat('d/m/Y', trim($exp[0]))->format('Y-m-d') . ' 00:00:00', DateTime::createFromFormat('d/m/Y', trim($exp[1]))->format('Y-m-d') . ' 23:59:59']);
                            }
                        })->first();

                    $detalle_ventas = [
                        'tipo_mov'               => 1,
                        'mov_id'                 => $ventas->id,
                        'ats_id'                 => $ats_id,
                        'identificacion'         => $ventas->id_cliente,
                        'nombre'                 => $ventas->cliente->nombre,
                        'emision'                => $ventas->fecha,
                        'tipo_comprobante'       => $ventas->tipo,
                        'num_comprobante'        => $ventas->nro_comprobante,
                        'comp_vent_autorizacion' => $ventas->nro_autorizacion,
                        'comp_diario'            => $ventas->id_asiento,
                        'id_empresa'             => $id_empresa,
                        'id_usuariomod'          => $idusuario,
                        'id_usuariocrea'         => $idusuario,
                        'ip_creacion'            => $ip,
                        'ip_modificacion'        => $ip,
                    ];
                    DetalleAts::insertGetId($detalle_ventas);
                }
            }

            if ($r->seleccionCompras != null) {
                for ($i = 0; $i < count($r->seleccionCompras); $i++) {
                    $compras = Compras::where('estado', '<>', '0')
                        ->where('id', $r['seleccionCompras'][$i])
                        ->where('tipo', '<>', '3')
                        ->where('id_empresa', $id_empresa)
                        ->where(function ($q) use ($r, $exp) {
                            if ($r->mes !== null && $r->anio !== null) {
                                $q->whereMonth('fecha', $r->mes)->whereYear('fecha', $r->anio);
                            } else {
                                $q->whereBetween("fecha", [DateTime::createFromFormat('d/m/Y', trim($exp[0]))->format('Y-m-d') . ' 00:00:00', DateTime::createFromFormat('d/m/Y', trim($exp[1]))->format('Y-m-d') . ' 23:59:59']);
                            }
                        })->first();


                    $detalle_compras = [
                        'tipo_mov'               => 2,
                        'mov_id'                 => $compras->id,
                        'ats_id'                 => $ats_id,
                        'identificacion'         => $compras->proveedor,
                        'nombre'                 => $compras->proveedor,
                        'emision'                => $compras->fecha,
                        'tipo_comprobante'       => $compras->tipo_comprobante,
                        'num_comprobante'        => $compras->numero,
                        'comp_vent_autorizacion' => $compras->autorizacion,
                        'comp_diario'            => $compras->id_asiento_cabecera,
                        'id_empresa'             => $id_empresa,
                        'id_usuariomod'          => $idusuario,
                        'id_usuariocrea'         => $idusuario,
                        'ip_creacion'            => $ip,
                        'ip_modificacion'        => $ip,
                    ];
                    DetalleAts::insertGetId($detalle_compras);
                }
            }
            $r->c = 'Tributario';
            $r->s = 'guardarDeclaracion';
            $r->m = Tributario::$modelo;
            $r->o = 'Se agrego la declaracion del periodo: ' . $r->anio . '/' . $r->mes;
            $cont++;
            Auditorias::saveAuditoria($r, DB::getQueryLog());
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Tributario';
            $r->s = 'guardarDeclaracion';
            $r->m = Tributario::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return $arrayRespuesta;
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                //'anio' => $r->anio,
                //'mes'  => $r->mes,
                'tipo' => 'tributario',
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
            ];
            return $arrayRespuesta;
        }
    }
    public static function excelDeclararion($r)
    {
        return self::excelCompras($r);
    }

    public static function excelCompras($r)
    {
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO");

        $titulos = [
            "No",
            "Asiento",
            "Fecha",
            "Cliente",
            "Ruc	",
            "Autorización",
            "Observación",
            "Tipo",
            "Documento",
            "Base 0%",
            "Base diferente 0%",
            "Descuento",
            "Subtotal",
            "Iva",
            "Total"
        ];

        $porcentajes = PorcentajeRetenciones::getPorcentajes();

        if (count($porcentajes) > 0) {

            foreach ($porcentajes as $v) {
                array_push($titulos, $v->nombre . ' ' . $v->valor);
            }
        }

        array_push($titulos, "Total a recibir", "# Retención", "Autorización");

        $ventas = self::getVentasMes($r);
        $compras = Tributario::getComprasMes($r);
        Excel::create('REPORTES DE TRIBUTACION', function ($excel) use ($titulos, $posicion, $ventas, $porcentajes, $r, $compras) {
            $excel->sheet('VENTAS', function ($sheet)  use ($titulos, $posicion, $ventas, $porcentajes, $r, $compras) {
                $comienzo = 1;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell($posicion[$i] . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setAlignment('center');
                        $cell->setBackground("#34495E");
                        $cell->setFontColor("#FFFFFF");
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                }
                // aumentamos 1
                $comienzo++;
                $totalBase0 = 0;
                $totalBaseDiferente0 = 0;
                $totalDescuento = 0;
                $totalSubtotal = 0;
                $totalImpuestos = 0;
                $totalTotal = 0;
                $count = 1;
                $datos_excel = [];
                foreach ($ventas as $v) {
                    $datos = Retenciones_Clientes::join('ct_detalle_cliente_retencion as dr', 'ct_cliente_retencion.id', '=', 'dr.id_cliente_retencion')
                        ->where('ct_cliente_retencion.id_factura', $v->id)
                        ->get();
                    $datos_excel = [];
                    array_push(
                        $datos_excel,
                        $count,
                        $v->id_asiento,
                        $v->fecha,
                        $v->nombre,
                        $v->id_cliente,
                        $v->nro_autorizacion,
                        $v->concepto,
                        $v->tipo,
                        $v->nro_comprobante,
                        $v->subtotal_0,
                        $v->subtotal_12,
                        $v->descuento,
                        $v->subtotal,
                        $v->impuesto,
                        $v->total
                    );

                    $totalRecibir = $v->total;
                    $numRetencion = '';
                    $numAutorizacion = '';

                    if (count($datos) > 0) {
                        foreach ($porcentajes as $row) {
                            $valor = 0;
                            foreach ($datos as $d) {
                                if ($row->id == $d->id_porcentaje) {
                                    $valor = $d->totales;
                                    $numRetencion = $d->nro_comprobante;
                                    $numAutorizacion = $d->autorizacion;
                                    $totalRecibir -= $valor;
                                }
                            }
                            array_push($datos_excel, number_format($valor, 4));
                        }
                    } else {
                        foreach ($porcentajes as $row) {
                            array_push($datos_excel, number_format(0, 4));
                        }
                    }

                    array_push(
                        $datos_excel,
                        number_format($totalRecibir, 4),
                        $numRetencion,
                        $numAutorizacion // Coma extra eliminada aquí
                    );

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell($posicion[$i] . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }

                    $comienzo++;
                    $totalBase0 += $v->subtotal_0;
                    $totalBaseDiferente0 += $v->subtotal_12;
                    $totalDescuento += $v->descuento;
                    $totalSubtotal += $v->subtotal;
                    $totalImpuestos += $v->impuesto;
                    $totalTotal += $v->total;
                    $count++;
                }
                $datos_tfoot = [];
                array_push(
                    $datos_tfoot,
                    "",
                    "Totales",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    number_format($totalBase0, 2),
                    number_format($totalBaseDiferente0, 2),
                    number_format($totalDescuento, 2),
                    number_format($totalSubtotal, 2),
                    number_format($totalImpuestos, 2),
                    number_format($totalTotal, 2)

                );

                foreach ($porcentajes as $row) {
                    array_push($datos_tfoot, number_format(Retenciones_Clientes::sumaRetenciones($row, $r->anio, $r->mes), 2));
                }
                if (count($datos_excel) > count($datos_tfoot)) {
                    $resta = count($datos_excel) - count($datos_tfoot);
                    for ($i = 0; $i < $resta; $i++) {
                        array_push($datos_tfoot, "");
                    }
                }
                for ($i = 0; $i < count($datos_tfoot); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_tfoot, $i) {
                        $cell->setValue($datos_tfoot[$i]);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                }

                $cab = [
                    "TOTALES" => "VALOR",
                    "Base 0%" => number_format($totalBase0, 2),
                    "Base diferente 0%" => number_format($totalBaseDiferente0, 2),
                    "Descuento" => number_format($totalDescuento, 2),
                    "Subtotal" => number_format($totalSubtotal, 2),
                    "Iva" => number_format($totalImpuestos, 2),
                    "Total" => number_format($totalTotal, 2),
                ];
                $comienzo += 2;
                foreach ($cab as $x => $y) {
                    $sheet->cell('A' . $comienzo, function ($cell) use ($x) {
                        $cell->setValue($x);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('B' . $comienzo, function ($cell) use ($y) {
                        $cell->setValue($y);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('left');
                    });
                    $comienzo++;
                }
                //RESUMENES RETENCIONES


                $contAuxiliar = 3;
                $comienzo -= 7;
                foreach ($porcentajes as $v => $valor) {
                    if ($v == 0) {
                        $sheet->cell($posicion[$contAuxiliar] . $comienzo, function ($cell) use ($valor) {
                            $cell->setValue($valor->nombre);
                            $cell->setBackground("#34495E");
                            $cell->setFontColor("#FFFFFF");
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontWeight('bold');
                        });
                    } else {
                        $sheet->cell($posicion[$contAuxiliar] . $comienzo, function ($cell) use ($valor) {
                            $cell->setValue($valor->nombre);
                            $cell->setBackground("#34495E");

                            $cell->setFontColor("#FFFFFF");
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontWeight('bold');
                        });
                    }

                    $contAuxiliar++;
                }
            });

            $alineacionLetrasi = ["A", "B", "C", "D", "E", "F", "G", "H", "AN"];
            $alineacionLetrasd = ["J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL"];
            foreach ($alineacionLetrasi as $v) {
                $rango = $v . '2:' . $v . intval(count($ventas) + 1);
                $excel->getActiveSheet()->getStyle($rango)->applyFromArray(
                    array(
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        )
                    )
                );
            }
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(35)->setAutosize(false);
            foreach ($alineacionLetrasd as $v) {
                $rango = $v . '2:' . $v . intval(count($compras) + 1);
                $excel->getActiveSheet()->getColumnDimension("A")->setWidth(15)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("B")->setWidth(15)->setAutosize(false);
                $excel->getActiveSheet()->getStyle($rango)->applyFromArray(
                    array(
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                        )
                    )
                );
            }
            self::venta($excel, $titulos, $posicion, $r, $porcentajes, $compras);

            foreach ($alineacionLetrasi as $v) {
                $rango = $v . '2:' . $v . intval(count($compras) + 1);
                $excel->getActiveSheet()->getStyle($rango)->applyFromArray(
                    array(
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        )
                    )
                );
            }
            foreach ($alineacionLetrasd as $v) {
                $rango = $v . '2:' . $v . intval(count($compras) + 1);
                $excel->getActiveSheet()->getColumnDimension("A")->setWidth(15)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("B")->setWidth(15)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("H")->setWidth(35)->setAutosize(false);
                $excel->getActiveSheet()->getStyle($rango)->applyFromArray(
                    array(
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                        )
                    )
                );
            }
        })->export('xlsx');
    }


    public static function venta($excel, $titulos, $posicion, $r, $porcentajes, $compras)
    {
        $excel->sheet('COMPRAS', function ($sheet) use ($compras, $titulos, $posicion, $porcentajes, $r) {
            $comienzo = 1;
            for ($i = 0; $i < count($titulos); $i++) {
                $sheet->cell($posicion[$i] . $comienzo, function ($cell) use ($titulos, $i) {
                    $cell->setValue($titulos[$i]);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
            }
            $comienzo++;

            $totalBase0 = 0;
            $totalBaseDiferente0 = 0;
            $totalDescuento = 0;
            $totalSubtotal = 0;
            $totalImpuestos = 0;
            $totalTotal = 0;
            $count = 1;
            $datos_excel = [];
            foreach ($compras as $v) {
                $datos = Retenciones::join('ct_detalle_retenciones as dr', 'ct_retenciones.id', 'dr.id_retenciones')
                    ->where('ct_retenciones.id_compra', $v->id)
                    ->get();

                $totalBase0 += $v->subtotal_0;
                $totalBaseDiferente0 += $v->subtotal_12;
                $totalDescuento += $v->descuento;
                $totalSubtotal += $v->subtotal;
                $totalImpuestos += $v->impuesto;
                $totalTotal += $v->total;

                $datos_excel = [
                    $count,
                    $v->nombre,
                    $v->id_asiento_cabecera,
                    $v->fecha,
                    $v->razonsocial,
                    $v->proveedor,
                    $v->autorizacion,
                    $v->observacion,
                    $v->numero,
                    number_format($v->subtotal_0, 2),
                    number_format($v->subtotal_12, 2),
                    number_format($v->descuento, 2),
                    number_format($v->subtotal, 2),
                    number_format($v->impuesto, 2),
                    number_format($v->total, 2),
                ];

                $totalRecibir = $v->total;
                $numRetencion = '';
                $numAutorizacion = '';

                if (count($datos) > 0) {
                    foreach ($porcentajes as $row) {
                        $valor = 0;
                        foreach ($datos as $d) {
                            if ($row->id == $d->id_porcentaje) {
                                $valor = $d->totales;
                                $numRetencion = $d->nro_comprobante;
                                $numAutorizacion = $d->autorizacion;
                                $totalRecibir -= $valor;
                            }
                        }
                        array_push($datos_excel, number_format($valor, 4));
                    }
                } else {
                    foreach ($porcentajes as $row) {
                        array_push($datos_excel, number_format(0, 4));
                    }
                }

                array_push($datos_excel, number_format($totalRecibir, 4), $numRetencion, $numAutorizacion);

                for ($i = 0; $i < count($datos_excel); $i++) {
                    $sheet->cell($posicion[$i] . $comienzo, function ($cell) use ($datos_excel, $i) {
                        $cell->setValue($datos_excel[$i]);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                }

                $comienzo++;
                $count++;
            }

            $datos_tfoot = [
                "",
                "Totales",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                number_format($totalBase0, 2),
                number_format($totalBaseDiferente0, 2),
                number_format($totalDescuento, 2),
                number_format($totalSubtotal, 2),
                number_format($totalImpuestos, 2),
                number_format($totalTotal, 2),
                "",
                "",
                "",
                "",
            ];

            foreach ($porcentajes as $row) {
                array_push($datos_tfoot, number_format(Retenciones_Clientes::sumaRetenciones($row, $r->anio, $r->mes), 2));
            }

            if (count($datos_excel) > count($datos_tfoot)) {
                $resta = count($datos_excel) - count($datos_tfoot);
                for ($i = 0; $i < $resta; $i++) {
                    array_push($datos_tfoot, "");
                }
            }

            for ($i = 0; $i < count($datos_tfoot); $i++) {
                $sheet->cell($posicion[$i] . $comienzo, function ($cell) use ($datos_tfoot, $i) {
                    $cell->setValue($datos_tfoot[$i]);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                });
            }

            $cab = [
                "TOTALES" => "VALOR",
                "Base 0%" => number_format($totalBase0, 2),
                "Base diferente 0%" => number_format($totalBaseDiferente0, 2),
                "Descuento" => number_format($totalDescuento, 2),
                "Subtotal" => number_format($totalSubtotal, 2),
                "Iva" => number_format($totalImpuestos, 2),
                "Total" => number_format($totalTotal, 2),
            ];
            $comienzo += 2;
            foreach ($cab as $x => $y) {
                $sheet->cell('A' . $comienzo, function ($cell) use ($x) {
                    $cell->setValue($x);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $comienzo, function ($cell) use ($y) {
                    $cell->setValue($y);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('left');
                });
                $comienzo++;
            }
        });
    }
}
