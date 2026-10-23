<?php

namespace Sis_medico\Http\Controllers\v3\contable\Apis;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Guia_Remision_Cabecera;
use Sis_medico\Ct_Nota_Credito_Clientes;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_ventas;
use Sis_medico\v3\contable\MaestroDocumentos;

class ApiInicioController extends Controller
{
    const CLAVES_CONTRATO = [
        'facturas',
        'link_factura',
        'emitido_factura',
        'retenciones',
        'link_retencion',
        'emitido_retencion',
        'liquidaciones',
        'link_liquidaciones',
        'nota_credito',
        'link_nota_credito',
        'guia_remision',
        'link_guia_remision',
    ];

    public function show(Request $r)
    {
        $idEmpresa = session('id_empresa');
        if (empty($idEmpresa)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No existe una empresa activa en la sesion.',
            ], 403);
        }

        $maestros = MaestroDocumentos::whereIn('codigo', ['01', '03', '04', '06', '07'])
            ->pluck('id', 'codigo');

        $facturas = Ct_ventas::where('ct_ventas.estado', 1)->where('ct_ventas.id_empresa', $idEmpresa);
        $facturas_dia = Ct_ventas::where('ct_ventas.estado', 1)->where('ct_ventas.id_empresa', $idEmpresa);
        $facturas_mes = Ct_ventas::where('ct_ventas.estado', 1)->where('ct_ventas.id_empresa', $idEmpresa);
        $facturas_anio = Ct_ventas::where('ct_ventas.estado', 1)->where('ct_ventas.id_empresa', $idEmpresa);
        $facturas_todo = Ct_ventas::where('ct_ventas.estado', 1)->where('ct_ventas.id_empresa', $idEmpresa);

        $retenciones = Ct_Retenciones::where('ct_retenciones.estado', 1)->where('ct_retenciones.id_empresa', $idEmpresa);
        $retenciones_dia = Ct_Retenciones::where('ct_retenciones.estado', 1)->where('ct_retenciones.id_empresa', $idEmpresa);
        $retenciones_mes = Ct_Retenciones::where('ct_retenciones.estado', 1)->where('ct_retenciones.id_empresa', $idEmpresa);
        $retenciones_anio = Ct_Retenciones::where('ct_retenciones.estado', 1)->where('ct_retenciones.id_empresa', $idEmpresa);
        $retenciones_todo = Ct_Retenciones::where('ct_retenciones.estado', 1)->where('ct_retenciones.id_empresa', $idEmpresa);
        $arrayRespuesta = [
            'facturas' => $facturas->leftjoin('de_pasos as p', 'ct_ventas.doc_electronico', 'p.id')
                ->leftjoin('de_documentos_electronicos as d', function ($join) use ($idEmpresa, $maestros) {
                    $join->on('ct_ventas.id', '=', 'd.id_documento')
                        ->where('d.ruc_emisor', '=', $idEmpresa)
                        ->where('d.id_maestro_documento', '=', $maestros->get('01'));
                })
                ->leftjoin('de_log_error as e', 'd.id', 'e.id_de_documentos_electronicos')
                ->whereYear('ct_ventas.fecha', date('Y'))
                ->where('ct_ventas.doc_electronico', '<>', 5)
                ->get([
                    'ct_ventas.id_empresa',
                    'ct_ventas.id',
                    'ct_ventas.doc_electronico',
                    'ct_ventas.fecha',
                    'ct_ventas.sucursal',
                    'ct_ventas.punto_emision',
                    'ct_ventas.nro_comprobante',
                    'p.nombre as estado',
                    'd.respuestaSriRecepcion',
                    'd.respuestaSriAutorizacion',
                    'e.descripcion_error'
                ]),
            'link_factura' => [
                'verificar_antes_envio' => [
                    'url' => 'tributacion/verComprobanteAntes',
                    'metodo' => 'POST',
                    'parametros' => [
                        'id' => 'agregar: id',
                        'tabla' => 'ct_ventas'
                    ]
                ],
            ],
            'emitido_factura' => [
                'hoy' => $facturas_dia->where('fecha', date('Y-m-d'))->count(),
                'mes' => $facturas_mes->whereYear('fecha', date('Y'))->whereMonth('fecha', date('m'))->count(),
                'anio' => $facturas_anio->whereYear('fecha', date('Y'))->count(),
                'todo' => $facturas_todo->count()
            ],
            'retenciones' => $retenciones
                ->leftjoin('de_pasos as p', 'ct_retenciones.doc_electronico', 'p.id')
                ->leftjoin('de_documentos_electronicos as d', function ($join) use ($idEmpresa, $maestros) {
                    $join->on('ct_retenciones.id', '=', 'd.id_documento')
                        ->where('d.ruc_emisor', '=', $idEmpresa)
                        ->where('d.id_maestro_documento', '=', $maestros->get('07'));
                })
                ->leftjoin('de_log_error as e', 'd.id', 'e.id_de_documentos_electronicos')
                ->whereYear('ct_retenciones.fecha', date('Y'))
                ->where('ct_retenciones.doc_electronico', '<>', 5)
                ->get([
                    'ct_retenciones.id_empresa',
                    'ct_retenciones.id',
                    'ct_retenciones.doc_electronico',
                    'ct_retenciones.fecha',
                    'ct_retenciones.sucursal',
                    'ct_retenciones.punto_emision',
                    'ct_retenciones.nro_comprobante',
                    'p.nombre as estado',
                    'd.respuestaSriRecepcion',
                    'd.respuestaSriAutorizacion',
                    'e.descripcion_error'
                ]),
            'link_retencion' => [
                'verificar_antes_envio' => [
                    'url' => 'tributacion/verComprobanteAntes',
                    'metodo' => 'POST',
                    'parametros' => [
                        'id' => 'agregar: id',
                        'tabla' => 'ct_retenciones'
                    ]
                ]
            ],
            'emitido_retencion' => [
                'hoy' => $retenciones_dia->where('fecha', date('Y-m-d'))->count(),
                'mes' => $retenciones_mes->whereYear('fecha', date('Y'))->whereMonth('fecha', date('m'))->count(),
                'anio' => $retenciones_anio->whereYear('fecha', date('Y'))->count(),
                'todo' => $retenciones_todo->count()
            ],
            'liquidaciones' => Ct_compras::where('ct_compras.estado', 1)
                ->where('ct_compras.id_empresa', $idEmpresa)
                ->leftjoin('de_pasos as p', 'ct_compras.doc_electronico', 'p.id')
                ->leftjoin('de_documentos_electronicos as d', function ($join) use ($idEmpresa, $maestros) {
                    $join->on('ct_compras.id', '=', 'd.id_documento')
                        ->where('d.ruc_emisor', '=', $idEmpresa)
                        ->where('d.id_maestro_documento', '=', $maestros->get('03'));
                })
                ->leftjoin('de_log_error as e', 'd.id', 'e.id_de_documentos_electronicos')
                ->whereYear('ct_compras.fecha', date('Y'))
                ->where('ct_compras.doc_electronico', '<>', 5)
                ->where('ct_compras.tipo', 2)
                ->get([
                    'ct_compras.id_empresa',
                    'ct_compras.id',
                    'ct_compras.doc_electronico',
                    'ct_compras.fecha',
                    'ct_compras.sucursal',
                    'ct_compras.punto_emision',
                    'ct_compras.nro_comprobante',
                    'p.nombre as estado',
                    'd.respuestaSriRecepcion',
                    'd.respuestaSriAutorizacion',
                    'e.descripcion_error'
                ]),
            'link_liquidaciones' => [
                'verificar_antes_envio' => [
                    'url' => 'tributacion/verComprobanteAntes',
                    'metodo' => 'POST',
                    'parametros' => [
                        'id' => 'agregar: id',
                        'tabla' => 'ct_compras'
                    ]
                ]
            ],
            'nota_credito' => Ct_Nota_Credito_Clientes::where('ct_nota_credito_clientes.estado', 1)
                ->where('ct_nota_credito_clientes.id_empresa', $idEmpresa)
                ->leftjoin('de_pasos as p', 'ct_nota_credito_clientes.doc_electronico', 'p.id')
                ->leftjoin('de_documentos_electronicos as d', function ($join) use ($idEmpresa, $maestros) {
                    $join->on('ct_nota_credito_clientes.id', '=', 'd.id_documento')
                        ->where('d.ruc_emisor', '=', $idEmpresa)
                        ->where('d.id_maestro_documento', '=', $maestros->get('04'));
                })
                ->leftjoin('de_log_error as e', 'd.id', 'e.id_de_documentos_electronicos')
                ->whereYear('ct_nota_credito_clientes.fecha', date('Y'))
                ->where('ct_nota_credito_clientes.doc_electronico', '<>', 5)
                ->get([
                    'ct_nota_credito_clientes.id_empresa',
                    'ct_nota_credito_clientes.id',
                    'ct_nota_credito_clientes.doc_electronico',
                    'ct_nota_credito_clientes.fecha',
                    'ct_nota_credito_clientes.sucursal',
                    'ct_nota_credito_clientes.punto_emision',
                    'ct_nota_credito_clientes.nro_comprobante',
                    'p.nombre as estado',
                    'd.respuestaSriRecepcion',
                    'd.respuestaSriAutorizacion',
                    'e.descripcion_error'
                ]),
            'link_nota_credito' => [
                'verificar_antes_envio' => [
                    'url' => 'tributacion/verComprobanteAntes',
                    'metodo' => 'POST',
                    'parametros' => [
                        'id' => 'agregar: id',
                        'tabla' => 'ct_nota_credito_clientes'
                    ]
                ]
            ],
            'guia_remision' => Ct_Guia_Remision_Cabecera::where('ct_cabecera_remision.estado', '!=', 5)
                ->where('ct_cabecera_remision.id_empresa', $idEmpresa)
                ->leftjoin('de_pasos as p', 'ct_cabecera_remision.estado', 'p.id')
                ->leftjoin('de_documentos_electronicos as d', function ($join) use ($idEmpresa, $maestros) {
                    $join->on('ct_cabecera_remision.id', '=', 'd.id_documento')
                        ->where('d.ruc_emisor', '=', $idEmpresa)
                        ->where('d.id_maestro_documento', '=', $maestros->get('06'));
                })
                ->leftjoin('de_log_error as e', 'd.id', 'e.id_de_documentos_electronicos')
                ->whereYear('ct_cabecera_remision.fecha_ini', date('Y'))
                ->get([
                    'ct_cabecera_remision.id_empresa',
                    'ct_cabecera_remision.id',
                    'ct_cabecera_remision.estado',
                    'ct_cabecera_remision.fecha_ini',
                    'ct_cabecera_remision.establecimiento',
                    'ct_cabecera_remision.punto_emision',
                    'ct_cabecera_remision.num_secuencial',
                    'p.nombre as estado',
                    'd.respuestaSriRecepcion',
                    'd.respuestaSriAutorizacion',
                    'e.descripcion_error'
                ]),
            'link_guia_remision' => [
                'verificar_antes_envio' => [
                    'url' => 'tributacion/verComprobanteAntes',
                    'metodo' => 'POST',
                    'parametros' => [
                        'id' => 'agregar: id',
                        'tabla' => 'ct_cabecera_remision'
                    ]
                ]
            ],
        ];
        return response()->json($arrayRespuesta, 200)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, private');
    }
}
