<?php

namespace Sis_medico\Http\Controllers\v3\contable\Apis;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\DeEmpresas;
use Sis_medico\v3\contable\InfoTributaria;
use Sis_medico\v3\contable\MaestroDocumentos;
use Sis_medico\v3\contable\Retenciones;
use Sis_medico\v3\contable\sri\Comprobante;
use Sis_medico\v3\contable\Ventas;

class ApiEmisionDocumentosController extends Controller
{
    public function index(Request $r)
    {
        return response()->json([
            'status' => 'disabled',
            'code' => 410,
            'message' => 'La emision automatica v3 no esta habilitada. Use Gestion de documentos.',
        ], 410);
    }
    private function retenciones()
    {
        $tipoDoc = '07';
        $retenciones = Retenciones::where('estado', 1)
            ->where('doc_electronico', 0)
            ->limit(5)
            ->get();
        $arrayRespuestas = [];
        $arrayRespuesta = [];
        if ($retenciones->count() > 0) {
            foreach ($retenciones as $row) {
                $deEmpresa = DeEmpresas::where('id_empresa', $row->id_empresa)->first();
                if ($deEmpresa == '') {
                    $arrayRespuesta = [
                        'code' => 500,
                        'status' => 'error',
                        'message' => 'No existe la empresa',
                        'tipoDoc' => 'Retención',
                        'num' => $row->nro_comprobante,
                    ];
                    array_push($arrayRespuestas, $arrayRespuesta);
                } else {
                    $idDoc = MaestroDocumentos::getIdMaestrosDocumentos($tipoDoc);
                    $datosTributarios = InfoTributaria::getDatos($row->id_empresa, $row->sucursal, $row->punto_emision, $idDoc);
                    if ($datosTributarios == '') {
                        $array = [
                            'idUsuario' => $row->id_usuariocrea,
                            'id_empresa' => $row->id_empresa,
                            's' => $row->sucursal . '-' . $row->punto_emision,
                            'e' => $row->sucursal,
                            'p' => $row->punto_emision
                        ];
                        InfoTributaria::crearInfoTributaria((object)$array);
                    } else {
                        //echo '<pre>';print_r($row);exit;
                        $comprobante = new Comprobante($row, $tipoDoc, $deEmpresa->ambiente, 1);
                        $comp = $comprobante->generarXml();
                        echo '<pre>';
                        print_r($comprobante);
                        exit;
                    }
                }
            }
        } else {
            $arrayRespuesta = [
                'code' => 201,
                'status' => 'success',
                'message' => 'No hay documentos por emitir',
                'tipoDoc' => 'Retención',
            ];
            array_push($arrayRespuestas, $arrayRespuesta);
        }
        echo json_encode($arrayRespuestas);
    }
    private function facturas()
    {
        $tipoDoc = '01';
        $facturas = Ventas::where('estado', 1)
            ->where('tipo', '!=', 'ND')
            ->where('estado', 1)
            ->where('doc_electronico', 0)
            ->limit(5)
            ->get();
        $arrayRespuestas = [];
        $arrayRespuesta = [];
        if ($facturas->count() > 0) {
            foreach ($facturas as $row) {
                $deEmpresa = DeEmpresas::where('id_empresa', $row->id_empresa)->first();
                if ($deEmpresa == '') {
                    $arrayRespuesta = [
                        'code' => 500,
                        'status' => 'error',
                        'message' => 'No existe la empresa',
                        'tipoDoc' => 'Factura',
                        'num' => $row->nro_comprobante,
                    ];
                    array_push($arrayRespuestas, $arrayRespuesta);
                } else {
                    $idDoc = MaestroDocumentos::getIdMaestrosDocumentos($tipoDoc);
                    $datosTributarios = InfoTributaria::getDatos($row->id_empresa, $row->sucursal, $row->punto_emision, $idDoc);
                    if ($datosTributarios == '') {
                        $arrayRespuesta = [
                            'code' => 500,
                            'status' => 'error',
                            'message' => 'No se han configurado los parametros tributarios para la empresa: ' . $deEmpresa->id_empresa,
                            'tipoDoc' => 'Factura',
                            'num' => $row->nro_comprobante,
                        ];
                        array_push($arrayRespuestas, $arrayRespuesta);
                    } else {
                        $comprobante = new Comprobante($row, $tipoDoc, $deEmpresa->ambiente, 1);
                        $comp = $comprobante->generarXml();
                        if (isset($comp['isValidoXsd']) && !$comp['isValidoXsd']) {
                            $arrayRespuesta = [
                                'code' => 500,
                                'status' => 'error',
                                'message' => $comp['mensajes'],
                                'tipoDoc' => 'Factura',
                                'num' => $row->nro_comprobante,
                            ];
                            array_push($arrayRespuestas, $arrayRespuesta);
                        } else {
                            $arrayRespuesta = [
                                'code' => 200,
                                'status' => 'success',
                                'message' => 'Documento generado',
                                'generado' => 'Ok',
                                'validadoXSD' => 'Ok',
                                'document' => $row->nro_comprobante,
                                'tipoDoc' => 'Factura',
                                'num' => $row->nro_comprobante,
                            ];
                            array_push($arrayRespuestas, $arrayRespuesta);
                            $firmado = $comprobante->firmarXml($comp);
                            if (isset($firmado['isValido']) && $firmado['isValido'] == 1) {
                                $respuesta = $comprobante->validarSri($firmado['comprobante']);
                                echo '<pre>';
                                print_r($respuesta);
                                $autorizado = $comprobante->autorizarSri($respuesta);
                                echo '<pre>';
                                print_r($autorizado);
                                exit;
                            }
                        }
                    }
                }
            }
        } else {
            $arrayRespuesta = [
                'code' => 201,
                'status' => 'success',
                'message' => 'No hay documentos por emitir',
                'tipoDoc' => 'Factura',
            ];
            array_push($arrayRespuestas, $arrayRespuesta);
        }
        echo json_encode($arrayRespuestas);
    }
}
