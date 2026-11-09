<?php

namespace Sis_medico\Http\Controllers\v3\contable\Apis;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Guia_Remision_Cabecera;
use Sis_medico\Ct_Nota_Credito_Clientes;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_ventas;

class ApiInicioController extends Controller
{
    public function show(Request $r)
    {

        $arrayRespuesta = [
            'facturas' => Ct_ventas::where('ct_ventas.estado', 1)
                ->leftjoin('de_pasos as p', 'ct_ventas.doc_electronico', 'p.id')
                ->leftjoin('de_documentos_electronicos as d', 'ct_ventas.id', 'd.id_documento')
                ->leftjoin('de_log_error as e', 'd.id', 'e.id_de_documentos_electronicos')
                ->whereYear('ct_ventas.fecha', date('Y'))
                ->where('ct_ventas.doc_electronico', '<>', 5)
                ->get([
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
                ]
            ],
            'retenciones' => Ct_Retenciones::where('ct_retenciones.estado', 1)
                ->leftjoin('de_pasos as p', 'ct_retenciones.doc_electronico', 'p.id')
                ->leftjoin('de_documentos_electronicos as d', 'ct_retenciones.id', 'd.id_documento')
                ->leftjoin('de_log_error as e', 'd.id', 'e.id_de_documentos_electronicos')
                ->whereYear('ct_retenciones.fecha', date('Y'))
                ->where('ct_retenciones.doc_electronico', '<>', 5)
                ->get([
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
            'liquidaciones' => Ct_compras::where('ct_compras.estado', 1)
                ->leftjoin('de_pasos as p', 'ct_compras.doc_electronico', 'p.id')
                ->leftjoin('de_documentos_electronicos as d', 'ct_compras.id', 'd.id_documento')
                ->leftjoin('de_log_error as e', 'd.id', 'e.id_de_documentos_electronicos')
                ->whereYear('ct_compras.fecha', date('Y'))
                ->where('ct_compras.doc_electronico', '<>', 5)
                ->where('ct_compras.tipo', 2)
                ->get([
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
                ->leftjoin('de_pasos as p', 'ct_nota_credito_clientes.doc_electronico', 'p.id')
                ->leftjoin('de_documentos_electronicos as d', 'ct_nota_credito_clientes.id', 'd.id_documento')
                ->leftjoin('de_log_error as e', 'd.id', 'e.id_de_documentos_electronicos')
                ->whereYear('ct_nota_credito_clientes.fecha', date('Y'))
                ->where('ct_nota_credito_clientes.doc_electronico', '<>', 5)
                ->get([
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
                ->leftjoin('de_pasos as p', 'ct_cabecera_remision.estado', 'p.id')
                ->leftjoin('de_documentos_electronicos as d', 'ct_cabecera_remision.id', 'd.id_documento')
                ->leftjoin('de_log_error as e', 'd.id', 'e.id_de_documentos_electronicos')
                ->whereYear('ct_cabecera_remision.fecha_ini', date('Y'))
                ->get([
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
        return response()->json($arrayRespuesta, 200);
    }
}
