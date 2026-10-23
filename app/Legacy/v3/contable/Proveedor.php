<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use PDO;

class Proveedor extends Model
{
    private static $modelo = 'Proveedor';
    protected $table = 'proveedor';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public static function armarProveedor($r)
    {

        $html = '';

        $proveedor = self::find($r->id_proveedor);
        if ($proveedor != '') {
            $html .= '<option  value="' . $proveedor->id . '" selected>' . $proveedor->razonsocial . '</option>';
        }
        return $html;
    }

    public static function CarteraPagar($r)
    {

        //dd('entra');
        try {
            $id_empresa = session("id_empresa");
            $deudas = DB::table('proveedor as p')
                ->leftJoin('ct_compras as c', 'p.id', '=', 'c.proveedor')
                ->leftJoin('ct_termino as t', 'c.termino', '=', 't.id')
                ->select(
                    'c.id',
                    'c.fecha',
                    't.dias',
                    'p.razonsocial',
                    'p.id as proveedor_id',
                    DB::raw('DATE_ADD(c.fecha, INTERVAL COALESCE(t.dias, 0) DAY) AS vencimiento'),
                    DB::raw('DATEDIFF(NOW(), DATE_ADD(c.fecha, INTERVAL COALESCE(t.dias, 0) DAY)) AS dias_transcurridos'),
                    'c.observacion',
                    DB::raw('ROUND(c.total_final, 2) AS saldo'),
                    DB::raw('CASE WHEN DATEDIFF(NOW(), DATE_ADD(c.fecha, INTERVAL COALESCE(t.dias, 0) DAY)) > t.dias THEN ROUND(c.total_final, 2) ELSE 0 END AS valor_vencido'),
                    DB::raw('CASE WHEN DATE(NOW()) = DATE(DATE_ADD(c.fecha, INTERVAL COALESCE(t.dias, 0) DAY)) THEN ROUND(c.total_final, 2) ELSE 0 END AS periodo'),
                    DB::raw('CASE WHEN DATEDIFF(NOW(), DATE_ADD(c.fecha, INTERVAL COALESCE(t.dias, 0) DAY)) <= t.dias THEN ROUND(c.total_final, 2) ELSE 0 END AS valor_por_vencer')
                )
                ->where('c.estado', '<>', 0)
                ->where(function ($q) use ($r) {
                    if ($r->id_proveedor != '') {
                        $q->where('p.id', $r->id_proveedor);
                    }
                })
                ->where('c.id_empresa', $id_empresa)
                ->whereBetween('c.fecha', [$r->desde . ' 00:00:00', $r->hasta . ' 23:59:59'])
                ->where('c.valor_contable', '>', 0)
                ->groupBy('p.id', 'c.id')
                ->get();

            //dd($deudas);

            $resultados = [];
            foreach ($deudas as $deuda) {
                $proveedorId = $deuda->proveedor_id;
                if (!isset($resultados[$proveedorId])) {
                    $resultados[$proveedorId] = [
                        'codigo' => $deuda->proveedor_id,
                        'cuenta' => $deuda->razonsocial,
                        'valor_vencido_total' => 0,
                        'valor_periodo_total' => 0,
                        'valor_por_vencer_total' => 0,
                        'detalles' => []
                    ];
                }
                $resultados[$proveedorId]['valor_vencido_total'] += $deuda->valor_vencido;
                $resultados[$proveedorId]['valor_periodo_total'] += $deuda->periodo;
                $resultados[$proveedorId]['valor_por_vencer_total'] += $deuda->valor_por_vencer;
                $resultados[$proveedorId]['detalles'][] = [
                    'fecha' => $deuda->fecha,
                    'vencimiento' => $deuda->vencimiento,
                    'observacion' => $deuda->observacion,
                    'saldo' => $deuda->saldo
                ];
            }


            // Convertir el array a un formato adecuado para mostrar en la vista
            $data = array_values($resultados);

            //dd($data);

            return $data;
        } catch (Exception $e) {
            return response()->json(['html' => '', "status" => "error", "message" => $e->getMessage()]);
        }
    }


    public static function getChequesGirados($r)
    {
        $egreso = Egresos::select(
            'ct_comprobante_egreso.fecha_comprobante as fecha_comprobante',
            'ct_comprobante_egreso.fecha_cheque as fecha_cheque',
            'ct_comprobante_egreso.no_cheque as cheque',
            'ct_comprobante_egreso.id_asiento_cabecera as id_asiento_cabecera',
            'ct_comprobante_egreso.beneficiario as id_proveedor',
            'ct_comprobante_egreso.id_caja_banco',
            'ct_comprobante_egreso.valor as valor',
            'ct_comprobante_egreso.estado as estado_egreso',
            'ct_comprobante_egreso.secuencia as secuencia_egreso',
            'ct_comprobante_egreso.check as check',
            'ct_comprobante_egreso.id_pago as id_pago',
            'p.razonsocial',
            'b.nombre as banco'
        )
            ->join('proveedor as p', 'ct_comprobante_egreso.beneficiario', '=', 'p.id')
            ->join('ct_caja_banco as b', 'ct_comprobante_egreso.id_caja_banco', '=', 'b.id')
            ->where('ct_comprobante_egreso.id_empresa', session('id_empresa'))
            ->where('ct_comprobante_egreso.id_pago', 2)
            ->where(function ($q) use ($r) {
                if ($r->id_proveedor != '') {
                    $q->where('ct_comprobante_egreso.beneficiario', $r->id_proveedor);
                }
            })
            ->wherebetween('ct_comprobante_egreso.fecha_cheque', [$r->d . ' 00:00:00', $r->h . ' 23:59:59']);
        $sumaEgreso = Egresos::where('id_empresa', session('id_empresa'))
            ->where('id_pago', 2)
            ->where('ct_comprobante_egreso.id_pago', 2)
            ->where('ct_comprobante_egreso.estado', '!=', 0)
            ->where(function ($q) use ($r) {
                if ($r->id_proveedor != '') {
                    $q->where('ct_comprobante_egreso.beneficiario', $r->id_proveedor);
                }
            })
            ->wherebetween('ct_comprobante_egreso.fecha_cheque', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
            ->sum('valor');
        $egresov = EgresosVarios::select(
            'ct_comprobante_egreso_varios.fecha_comprobante as fecha_comprobante',
            'ct_comprobante_egreso_varios.fecha_cheque as fecha_cheque',
            'ct_comprobante_egreso_varios.nro_cheque as cheque',
            'ct_comprobante_egreso_varios.id_asiento_cabecera as id_asiento_cabecera',
            'ct_comprobante_egreso_varios.id_proveedor as id_proveedor',
            'ct_comprobante_egreso_varios.id_caja_banco',
            'ct_comprobante_egreso_varios.valor as valor',
            'ct_comprobante_egreso_varios.estado as estado_egreso',
            'ct_comprobante_egreso_varios.secuencia as secuencia_egreso',
            'ct_comprobante_egreso_varios.check as check',
            'ct_comprobante_egreso_varios.id_pago as id_pago',
            'p.razonsocial',
            'b.nombre as banco'
        )
            ->join('proveedor as p', 'ct_comprobante_egreso_varios.id_proveedor', '=', 'p.id')
            ->join('ct_caja_banco as b', 'ct_comprobante_egreso_varios.id_caja_banco', '=', 'b.id')
            ->where('ct_comprobante_egreso_varios.id_empresa', session('id_empresa'))
            ->where('ct_comprobante_egreso_varios.id_pago', 2)
            ->where(function ($q) use ($r) {
                if ($r->id_proveedor != '') {
                    $q->where('ct_comprobante_egreso_varios.id_proveedor', $r->id_proveedor);
                }
            })
            ->wherebetween('ct_comprobante_egreso_varios.fecha_cheque', [$r->d . ' 00:00:00', $r->h . ' 23:59:59']);
        $sumaEgresoVarios = EgresosVarios::where('id_empresa', session('id_empresa'))
            ->where('id_pago', 2)
            ->where('ct_comprobante_egreso_varios.id_pago', 2)
            ->where('ct_comprobante_egreso_varios.estado', '!=', 0)
            ->where(function ($q) use ($r) {
                if ($r->id_proveedor != '') {
                    $q->where('ct_comprobante_egreso_varios.id_proveedor', $r->id_proveedor);
                }
            })
            ->wherebetween('ct_comprobante_egreso_varios.fecha_cheque', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
            ->sum('valor');
        $sumaTotal = $sumaEgreso + $sumaEgresoVarios;
        $data = $egreso->union($egresov)->get();
        $data->each(function ($item) use ($sumaTotal) {
            $item->total_global = round($sumaTotal, 2);
        });
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        return json_encode($results);
    }
    public static function InformeSaldos($r)
    {
        try {
            $id_empresa = session("id_empresa");

            $data = DB::table('proveedor as p')
                ->join('ct_compras as c', 'p.id', '=', 'c.proveedor')
                ->select(
                    'p.id as proveedor_id',
                    'p.razonsocial',
                    DB::raw('ROUND(SUM(IFNULL(c.total_final, 0) - IFNULL((
                        SELECT 
                            SUM(IFNULL(valor_fuente, 0) + IFNULL(valor_iva, 0)) 
                        FROM 
                            ct_retenciones 
                        WHERE 
                            id_compra = c.id AND estado != 0
                    ), 0)), 2) AS deuda'),
                    DB::raw('IFNULL(SUM((
                        SELECT SUM(abono)
                        FROM ct_detalle_comprobante_egreso ddce
                        JOIN ct_comprobante_egreso cce ON ddce.id_comprobante = cce.id
                        WHERE cce.id_proveedor = p.id
                        AND ddce.id_compra = c.id
                    )), 0) AS anticipos')
                )
                ->where('c.estado', '!=', 0)
                ->where(function ($q) use ($r) {
                    if ($r->proveedor != null) {
                        $q->where('p.id', $r->proveedor);
                    }
                })
                ->where('c.id_empresa', $id_empresa)
                ->wherebetween('c.fecha', [$r->desde . ' 00:00:00', $r->hasta . ' 23:59:59'])
                ->groupBy('p.id', 'p.razonsocial')
                ->get();

            // Calcular los totales
            $totalDeuda = 0;
            $totalAnticipos = 0;

            foreach ($data as $detalle) {
                $totalDeuda += $detalle->deuda;
                $totalAnticipos += $detalle->anticipos;
            }

            $data[] = (object) [
                'proveedor_id' => '',
                'razonsocial' => 'Totales',
                'deuda' => round($totalDeuda, 2),
                'anticipos' => round($totalAnticipos, 2),
                'saldo' => round($totalDeuda - $totalAnticipos, 2)
            ];

            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );

            return response()->json($results);
        } catch (Exception $e) {
            return response()->json(['html' => '', "status" => "error", "message" => $e->getMessage()]);
        }
    }
    public static function allComprobantesVarios($r)
    {
        if (isset($r->d) && isset($r->h)) {
            return ComprobanteEgresoVarios::leftjoin("users as us1", "ct_comprobante_egreso_varios.id_usuariocrea", "us1.id")
                ->leftjoin("users as us2", "ct_comprobante_egreso_varios.id_usuariomod", "us2.id")
                ->where('id_empresa', session("id_empresa"))
                ->whereBetween('ct_comprobante_egreso_varios.created_at', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
                ->select('ct_comprobante_egreso_varios.*', DB::raw('IF(`ct_comprobante_egreso_varios`.estado=0,CONCAT(SUBSTRING(us2.nombre1,1,1),". ", us2.apellido1),"") as nombre_modi'), DB::raw('CONCAT(SUBSTRING(us1.nombre1,1,1),". ", us1.apellido1) as nombre_crea'))
                ->get();
        } else if (isset($r["id"])) {
            return ComprobanteEgresoVarios::find($r["id"]);
        }
    }

    public static function cambiarEstadoProveedor($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (Proveedor::where('id', $r->id)->update(['estado' => $estado])) {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro guardado correctamente',
                    'data' => $r->input(),
                ];
            } else {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Error al actualizar el estado del ',
                    'data' => $r->input(),
                ];
            }
        } catch (Exception $e) {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
        }
        return json_encode($arrayRespuesta);
    }
    public static function getAllProveedores()
    {
        return Proveedor::where("estado", 1)->get();
    }
    public static function getAllProveedor($term)
    {
        return Proveedor::where("estado", 1)->get();
    }
    public static function getAllBancos($term)
    {
        return ConfigBancos::selectRaw('id as id,nombre as text')->where('nombre', 'like', '%' . $term . '%')->get();
    }

    public static function getProveedorCombo($term)
    {
        return Proveedor::selectRaw('id, CONCAT(id," | ",nombrecomercial) as text')
            ->where(function ($q) use ($term) {
                $q->orWhere('nombrecomercial', 'like', '%' . $term . '%');
                $q->orWhere('id', 'like', '%' . $term . '%');
            })->get();
    }
    public static function  buscarProveedor($term = '')
    {
        DB::enableQueryLog();
        return Proveedor::leftjoin('new_db_ciudades as c', 'c.id_ciudad', 'proveedor.id_ciudad')
            ->select(
                'proveedor.id',
                'proveedor.razonsocial',
                'proveedor.nombrecomercial',
                'proveedor.email',
                'proveedor.email2',
                'proveedor.estado',
                'proveedor.telefono1',
                'proveedor.telefono2',
                'proveedor.logo',
                'proveedor.id_ciudad',
                'proveedor.id_tipoproveedor',
                'proveedor.tipo',
                'proveedor.id_cuentas',
                'proveedor.id_configuracion',
                'proveedor.tipo_cuenta',
                'proveedor.identificacion',
                'proveedor.beneficiario',
                'c.id_provincia_ciudad',
                'c.id_pais_ciudad',
                'proveedor.direccion',
                'proveedor.urbanizacion',
                'proveedor.etapa',
                'proveedor.manzana',
                'proveedor.villa',
                'proveedor.referencia'
            )->where(function ($q) use ($term) {
                $q->orWhere('razonsocial', 'like', '%' . $term . '%');
                $q->orWhere('nombrecomercial', 'like', '%' . $term . '%');
                $q->orWhere('id', 'like', '%' . $term . '%');
            })
            ->get();
    }
    public static function getProveedores($id = '')
    {
        if ($id == '') {
            return Proveedor::leftjoin('tipoproveedor as tp', 'proveedor.id_tipoproveedor', 'tp.id')
                ->join('users as uc', 'proveedor.id_usuariocrea', '=', 'uc.id')
                ->get([
                    'proveedor.id as id',
                    'proveedor.nombrecomercial',
                    'proveedor.razonsocial',
                    'proveedor.logo',
                    'proveedor.email',
                    'proveedor.estado',
                    'tp.nombre as tipo',
                    DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                    'proveedor.created_at',
                    'proveedor.provisiones'
                ]);
        } else {
            return Proveedor::leftjoin('new_db_ciudades as c', 'c.id_ciudad', 'proveedor.id_ciudad')
                ->leftjoin('new_db_archivos as ruc', function ($q) {
                    $q->on('proveedor.id', 'ruc.id_proveedor_archivo')
                        ->where('ruc.tipo_archivo', 'proveedorRuc');
                })
                ->leftjoin('new_db_archivos as cedula', function ($q) {
                    $q->on('proveedor.id', 'cedula.id_proveedor_archivo')
                        ->where('cedula.tipo_archivo', 'proveedorCedula');
                })
                ->leftjoin('new_db_archivos as certificado', function ($q) {
                    $q->on('proveedor.id', 'certificado.id_proveedor_archivo')
                        ->where('certificado.tipo_archivo', 'proveedorVotacion');
                })
                ->leftjoin('plan_cuentas as p', 'proveedor.id_cuentas', 'p.id')
                ->where('proveedor.id', $id)
                ->first([
                    'proveedor.id',
                    'proveedor.razonsocial',
                    'proveedor.nombrecomercial',
                    'proveedor.email',
                    'proveedor.email2',
                    'proveedor.estado',
                    'proveedor.telefono1',
                    'proveedor.telefono2',
                    'proveedor.logo',
                    'proveedor.ciudad',
                    'proveedor.id_tipoproveedor',
                    'proveedor.tipo',
                    'proveedor.id_cuentas',
                    'proveedor.id_configuracion',
                    'proveedor.tipo_cuenta',
                    'proveedor.identificacion',
                    'proveedor.beneficiario',
                    'c.id_provincia_ciudad',
                    'c.id_pais_ciudad',
                    'proveedor.id_ciudad',
                    'proveedor.direccion',
                    'proveedor.urbanizacion',
                    'proveedor.etapa',
                    'proveedor.manzana',
                    'proveedor.villa',
                    'proveedor.referencia',
                    'proveedor.cuenta',
                    'ruc.ruta_archivo as ruta_ruc',
                    'cedula.ruta_archivo as ruta_cedula',
                    'certificado.ruta_archivo as ruta_certificado',
                    'proveedor.provisiones',
                    'p.id as id_plan',
                    'p.nombre as nombre_plan'
                ]);
        }
    }
    public static function saveProveedor($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            $proveedor = Proveedor::find($data['id']);
            $data['provisiones'] = json_encode($r->provisiones);
            if ($proveedor == '') {
                $data['id_usuariocrea'] = Auth::user()->id;
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_creacion'] = $ip;
                $data['ip_modificacion'] = $ip;
                $id = Proveedor::insertGetId($data);
                $r->c = 'Proveedores';
                $r->s = 'saveProveedor';
                $r->d = $origin['d'];
                $r->m = Proveedor::$modelo;
                $r->o = 'Se creo el proveedor con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                Proveedor::where('id', $id)->update($data);
                $r->c = 'Proveedores';
                $r->s = 'saveProveedor';
                $r->d = $origin['d'];
                $r->m = Proveedor::$modelo;
                $r->o = 'Se actualizo el proveedor con id.: ' . $id;
                $cont++;
            }
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
            $r->c = 'Proveedores';
            $r->s = 'saveProveedor';
            $r->d = $origin['d'];
            $r->m = Proveedor::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => ''
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function getreporteDeudasPendientes($r, $type = '')
    {
        DB::enableQueryLog();
        $empresa = Empresas::where('estado',1)->where('prioridad',1)->first();
        $results = DB::table('ct_compras')
            ->select(
                DB::raw('0 AS orden'),
                DB::raw("'Factura' AS tipo"),
                'ct_compras.id',
                'ct_compras.serie',
                'ct_compras.secuencia_f',
                'ct_compras.fecha_termino',
                'ct_compras.f_caducidad',
                'ct_compras.fecha AS fecha_compra',
                DB::raw('NULL AS fecha_documento'),
                'proveedor.id AS identificacion',
                'proveedor.razonsocial',
                'tp.id as id_tipo_provedor',
                'tp.nombre as tipo_provedor',
                'ct_compras.subtotal_0',
                'ct_compras.subtotal_12',
                'ct_compras.subtotal',
                'ct_compras.iva_total',
                'ct_compras.total_final',
                'ct_compras.valor_contable',
                'ct_compras.id_empresa',
                'ct_compras.tipo',
                DB::raw("IF(ct_compras.tipo = 1 , 'COM-FA', 'COM-FACT')  AS tipo_factura"),
                'ct_compras.numero',
                'ct_compras.tipo_comprobante',
                'ct_compras.observacion',
                DB::raw("CONCAT(proveedor.razonsocial, ' Fact: # ', ct_compras.numero, ' Ref: ', ct_compras.id, ' -- Asiento ', ct_compras.id_asiento_cabecera) AS detalle"),
                'ct_compras.id_asiento_cabecera AS id_asiento',
                DB::raw("CONCAT(ct_asientos_detalle.id_plan_cuenta, ' | ', ct_asientos_detalle.descripcion) AS cuenta"),
                DB::raw("CONCAT('COM-FA ', ct_compras.secuencia_f) AS comprobante"),
                DB::raw("
            COALESCE((
                SELECT SUM(COALESCE(valor, 0)) 
                FROM ct_comprobante_egreso c
                JOIN ct_detalle_comprobante_egreso d ON c.id = d.id_comprobante
                WHERE c.id_empresa = ct_compras.id_empresa
                  AND c.estado != 0
                  AND d.id_compra = ct_compras.id
            ),0) AS egreso
        "),
                DB::raw("
            COALESCE((
                SELECT SUM(COALESCE(valor_fuente, 0) + COALESCE(valor_iva, 0))  
                FROM ct_retenciones 
                WHERE id_empresa = ct_compras.id_empresa
                  AND estado != 0 
                  AND id_compra = ct_compras.id
            ),0) AS retencion
        "),
                DB::raw("
            COALESCE((
                SELECT SUM(COALESCE(total_disponible, 0)) 
                FROM ct_cruce_valores
                JOIN ct_detalle_cruce ON ct_cruce_valores.id = ct_detalle_cruce.id_comprobante
                WHERE ct_cruce_valores.id_empresa = ct_compras.id_empresa
                  AND ct_cruce_valores.estado != 0 
                  AND ct_detalle_cruce.id_factura = ct_compras.id
            ),0) AS cruce_valores
        "),
                DB::raw("
            COALESCE((
                SELECT SUM(COALESCE(valor_contable, 0)) 
                FROM ct_credito_acreedores
                JOIN ct_detalle_credito_acreedores ON ct_credito_acreedores.id = ct_detalle_credito_acreedores.id_debito
                WHERE ct_credito_acreedores.estado != 0 
                  AND ct_credito_acreedores.id_compra = ct_compras.id
            ),0) AS credito_acreedores
        "),
                DB::raw("
            COALESCE((
                SELECT SUM(COALESCE(valor, 0)) 
                FROM ct_debito_bancario 
                JOIN ct_debito_bancario_detalle ON ct_debito_bancario.id = ct_debito_bancario_detalle.id_debito
                WHERE ct_debito_bancario.estado != 0 
                  AND ct_debito_bancario_detalle.id_compra = ct_compras.id
            ),0) AS debito_bancario
        "),
                DB::raw("
            COALESCE((
                SELECT SUM(COALESCE(valor_pago, 0)) 
                FROM ct_comprobante_egreso_masivo
                JOIN ct_detalle_comprobante_egreso_masivo ON ct_comprobante_egreso_masivo.id = ct_detalle_comprobante_egreso_masivo.id_comprobante
                WHERE ct_comprobante_egreso_masivo.estado != 0 
                  AND ct_detalle_comprobante_egreso_masivo.id_compra = ct_compras.id
            ),0) AS comprobante_egreso_masivo
        "),
                DB::raw("
            COALESCE((
                SELECT SUM(COALESCE(valor_contable, 0)) 
                FROM ct_debito_acreedores 
                JOIN ct_detalle_debito_acreedores ON ct_debito_acreedores.id = ct_detalle_debito_acreedores.id_debito_acreedores
                WHERE ct_debito_acreedores.estado <> 0 
                  AND ct_detalle_debito_acreedores.id_factura = ct_compras.id   
            ),0) AS debito_acreedores
        ")
            )
            ->join('proveedor', 'proveedor.id', '=', 'ct_compras.proveedor')
            ->leftJoin('tipoproveedor as tp','tp.id', 'proveedor.id_tipoproveedor')
            ->leftJoin('ct_asientos_detalle', function ($join) {
                $join->on('ct_compras.id_asiento_cabecera', '=', 'ct_asientos_detalle.id_asiento_cabecera')
                    ->where('ct_asientos_detalle.descripcion', 'LIKE', '%PROVEE%');
            })
            ->leftJoin('plan_cuentas', 'ct_asientos_detalle.id_plan_cuenta', '=', 'plan_cuentas.id')
            ->leftJoin('plan_cuentas_empresa', function ($join) {
                $join->on('plan_cuentas.id', '=', 'plan_cuentas_empresa.plan')
                    ->on('plan_cuentas_empresa.id_empresa', '=', 'ct_compras.id_empresa');
            })
            ->where('ct_compras.estado', '>', 0)
            ->where(function ($q) use ($r) {
                if ($r->p !== null) {
                    $q->where("ct_compras.proveedor", $r->p);
                }
            })
            ->where(function ($q) use ($r) {
                if ($r->d == '') {
                    $q->where('ct_compras.fecha', '<=', $r->h . ' 23:59:59');
                } else {
                    $q->whereBetween('ct_compras.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59']);
                }
            })
            ->where(function ($q) use ($r) {
                if (!filter_var($r->input('excluir_grupo_robles', false), FILTER_VALIDATE_BOOLEAN)) {
                    return;
                }

                $rucsGrupo = $r->input('rucs_grupo_robles', []);
                if (!is_array($rucsGrupo)) {
                    return;
                }

                $rucsGrupo = array_values(array_unique(array_filter(array_map(function ($ruc) {
                    return is_scalar($ruc) ? trim((string) $ruc) : '';
                }, $rucsGrupo), function ($ruc) {
                    return $ruc !== '';
                })));

                if (!empty($rucsGrupo)) {
                    $q->whereNotIn('proveedor.id', $rucsGrupo);
                }
            })
            ->where(function ($q) use ($type) {
                if ($type != '') {
                    $q->where("ct_compras.valor_contable", '>', 0);
                }
            })
            ->where('ct_compras.id_empresa', $empresa->id)
            ->orderBy('ct_compras.fecha', 'ASC')
            ->orderBy('proveedor','ASC' )
            ->get();

        if ($type != '') {
            return $results;
        }
        $html = "";
        $tfoot = "";
        $arrayR = [];
        //VALORES TOTALES
        $valor1 = 0;
        $valor2 = 0;
        $valor3 = 0;
        $valor4 = 0;
        $valor5 = 0;
        $valor6 = 0;
        $valor7 = 0;
        foreach ($results as $key => $value) {
            $days = "0 dias";
            $daysf = 0;
            if (!is_null($value->fecha_termino)) {
                $fec = new DateTime($value->f_caducidad);
                $fec2 = new DateTime($value->fecha_termino);
                $diff = $fec->diff($fec2);
                $days = $diff->days . ' Dias ';
                $fech = new DateTime($value->fecha_termino);
                $fech2 = new DateTime($r->h);
                $diff2 = $fech2->diff($fech);
                $daysf = $diff2->format("%r%a");
            }
            if (!in_array($value->razonsocial, $arrayR)) {
                $html .= "
                    <tr>
                        <td colspan='15' ><span style='font-weight:bold;'>{$value->razonsocial}</span></td>
                    </tr>
                ";
            }
            $tipo = "";
            if (($value->tipo) == 1) {
                $tipo =  "COM-FA";
            } else {
                $tipo = "COM-FACT";
            }
            $html .= "<tr>
                    <td><span>{$value->fecha_compra}</span></td>
                    <td><span>{$days}</span></td>
                    <td><span>" . (date("d-m-Y", strtotime($value->f_caducidad))) . "</span></td>
                    <td><span>{$daysf}</span></td>
                    <td><span>" . ($tipo) . "</span></td>
                    <td><span>{$value->numero}</span></td>
                    <td><span>Fact:" . ($value->secuencia_f . "Ref:" . $value->numero . " " . $value->observacion . "<br> <label style='font-size:11px;' class='label label-success'>Id. Asiento:" . $value->id_asiento . "ID:" . $value->id . "</label>") . "</span></td>
                    <td>";
            $masterTipo = MasterTipos::where("codigo", $value->tipo_comprobante)->first();
            if (isset($masterTipo)) {
                $html .= $masterTipo->nombre;
            }
            $html .= "</td>";

            $html .= "<td style='text-align:right;'>" . (number_format($value->total_final, 2, '.', ',')) . "</td>
                <td style='text-align:right;'>" . (number_format($value->egreso ?? 0, 2, '.', ',')) . "</td>
                <td style='text-align:right;'>" . (number_format($value->retencion ?? 0, 2, '.', ',')) . "</td>
                <td style='text-align:right;'>" . (number_format($value->debito_acreedores ?? 0, 2, '.', ',')) . "</td>
                <td style='text-align:right;'>" . (number_format($value->cruce_valores ?? 0, 2, '.', ',')) . "</td>
                <td style='text-align:right;'>" . (number_format($value->credito_acreedores ?? 0, 2, '.', ',')) . "</td>
                <td style='text-align:right;'>" . (number_format($value->valor_contable ?? 0, 2, '.', ',')) . "</td>
                </tr>";
            array_push($arrayR, $value->razonsocial);

            // CALCULOS
            $valor1 += $value->total_final;
            $valor2 += $value->egreso ?? 0;
            $valor3 += $value->retencion ?? 0;
            $valor4 += $value->debito_acreedores ?? 0;
            $valor5 += $value->cruce_valores ?? 0;
            $valor6 += $value->credito_acreedores ?? 0;
            $valor7 += $value->valor_contable ?? 0;

            if ($key < count($results) - 1) {

                $nextItem = $results[$key + 1];
                if ($nextItem->identificacion != $value->identificacion) {
                    $html .= "
                <tr style='background-color:#FFFBCB'>
                    <td colspan='8' style='text-align:right'>Totales</td>
                    <td style='text-align:right'>$valor1</td>
                    <td style='text-align:right'>$valor2</td>
                    <td style='text-align:right'>$valor3</td>
                    <td style='text-align:right'>$valor4</td>
                    <td style='text-align:right'>$valor5</td>
                    <td style='text-align:right'>$valor6</td>
                    <td style='text-align:right'>$valor7</td>
                </tr>";
                    $valor1 = 0;
                    $valor2 = 0;
                    $valor3 = 0;
                    $valor4 = 0;
                    $valor5 = 0;
                    $valor6 = 0;
                    $valor7 = 0;
                }
            } else {

                $html .= "
                <tr style='background-color:#FFFBCB'>
                    <td colspan='8' style='text-align:right'>Totales</td>
                    <td style='text-align:right'>$valor1</td>
                    <td style='text-align:right'>$valor2</td>
                    <td style='text-align:right'>$valor3</td>
                    <td style='text-align:right'>$valor4</td>
                    <td style='text-align:right'>$valor5</td>
                    <td style='text-align:right'>$valor6</td>
                    <td style='text-align:right'>$valor7</td>
                </tr>";
            }
        }
        return  json_encode(['body' => $html]);
    }
    public static function buscar_detalleAcreedores($r)
    {
        $productos = Proveedor::where('id', $r)->first();
        if (!is_null($productos)) {
            $detalle = DetalleAcreedores::where('id_proveedor', $productos->id)->get();
            $compras = Compras::where("proveedor", $productos->id)
                ->where("id_empresa", session('id_empresa'))
                ->where("estado", "!=", "0")
                ->where('valor_contable', '>', 0)
                ->get();
            $num_mayor = 0;
            foreach ($compras as $compra) {
                if (intval($compra->secuencia_factura) > $num_mayor) {
                    $num_mayor =  intval($compra->secuencia_factura);
                }
            }
            if (count($detalle) == 0) {
                return response()->json(['nombreProveedor' => $productos->razonsocial, 'idProveedor' => $productos->id, 'value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => "", 'autorizacion' => "", 'caducidad' => '']);
            }
            foreach ($detalle as $value) {
                if ($num_mayor >= intval($value->sinicia)  and $num_mayor <= intval($value->sfin)) {
                    return response()->json(['nombreProveedor' => $productos->razonsocial, 'idProveedor' => $productos->id, 'value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => $value->serie, 'autorizacion' => $value->autorizacion, 'caducidad' => '']);
                }
            }
            return response()->json(['nombreProveedor' => $productos->razonsocial, 'idProveedor' => $productos->id, 'value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => "", 'autorizacion' => "", 'caducidad' => '']);
        } else {
            return response()->json(['value' => 'no']);
        }
    }
    public static function buscar_proveedor($r)
    {
        $facturas = AsientosDetalles::join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
            ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
            ->join('proveedor as p', 'p.id', 'co.proveedor')
            ->where('co.proveedor', $r)
            ->where('c.id_empresa', session("id_empresa"))
            ->where('co.estado', '<', '3')
            ->where('co.valor_contable', '>', '0')
            ->select(
                "co.total_final",
                'co.valor_contable',
                'co.tipo',
                'co.secuencia_f',
                'c.observacion',
                'ct_asientos_detalle.id',
                'c.fecha_asiento',
                "c.sucursal"
            )
            ->get();
        $deudas = AsientosCabecera::join('ct_compras as co', 'co.id_asiento_cabecera', 'ct_asientos_cabecera.id')
            ->where('co.proveedor', $r)
            ->where('co.id_empresa',  session("id_empresa"))
            ->where('co.estado', '>', 0)
            ->where('co.valor_contable', '>', 0)
            ->select(
                'co.total_final',
                'co.valor_contable',
                'co.secuencia_f',
                'co.tipo',
                'ct_asientos_cabecera.observacion',
                'co.f_caducidad as fecha_asiento',
                'co.numero',
                'co.proveedor',
                'co.id',
                'co.sucursal'
            )
            ->orderby('ct_asientos_cabecera.id', 'desc')
            ->get();
        foreach ($deudas as $val) {
            $retenciones = Retenciones::where("id_compra", $val->id)->get([DB::raw("valor_fuente+valor_iva as total ")]);
            $suma = array_sum($retenciones->pluck("total")->all());
            $val["valor_nuevo"] = $suma;
        }
        if ($facturas->isNotEmpty()) {
            $data = [$facturas[0]->valor_contable, $facturas[0]->secuencia_f, $facturas[0]->observacion, $facturas[0]->id, $facturas[0]->fecha_asiento, $deudas, $facturas[0]->tipo];

            return response()->json($data);
        } else {
            return response()->json(['value' => 'no resultados']);
        }
    }


    public static function getSecuencia($tipo, $tipo_comprobante = "1")
    {
        $id_empresa = session("id_empresa");
        $idusuario  = Auth::user()->id;
        $ct_secuencia = ComprobanteSecuencia::where('empresa', $id_empresa)->where('tipo', $tipo)->where("tipo_comprobante", $tipo_comprobante)->first();
        if (is_null($ct_secuencia)) {
            LogAsiento::secuenciaEmpresa($id_empresa, $tipo, $tipo_comprobante);
            return "0000000001";
        } else {
            $nro_secuencia = intval($ct_secuencia->secuencia);
            $nro_secuencia++;
            $nro_secuencia = str_pad($nro_secuencia, 10, "0", STR_PAD_LEFT);
            $ct_secuencia->secuencia = $nro_secuencia;
            $ct_secuencia->id_usuariomod = $idusuario;
            $ct_secuencia->save();
            return $nro_secuencia;
        }
    }


    public static function guardarComprobante($r)
    {

        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            if ($data['id'] == '') {
                DB::enableQueryLog();
                $cheque = 0;
                if (isset($data["cheque_entregado"])) {
                    $cheque = 1;
                }
                $superavit = (int) $data['superavit'];
                $numero_factura = 0;
                if ($superavit != 0) {
                    $numero_factura =  LogAsiento::getSecuencia(1, 1);
                    if (!is_null($data['total_favor'])) {

                        $nombreProveedor = Proveedor::find($data["nombre_acreedores"]);
                        $saldos_ret = 0;
                        $rt_estado = 0;
                        $suma_de_retenciones =  0;
                        for ($i = 0; $i < $data['contador_total']; $i++) {
                            $suma_de_retenciones += $data["valorasumir" . $i];
                        }
                        if ($suma_de_retenciones > 0) {
                            $rt_estado = 1;
                            $saldos_ret = $data['total_favor'] - $suma_de_retenciones;
                        } else {
                            $saldos_ret = $data['total_favor'];
                        }
                        $input_comprobante = [
                            'descripcion'     => $data['concepto'] . ' REF: ' . $numero_factura,
                            'estado'          => '1',
                            'beneficiario'    => $nombreProveedor->nombrecomercial,
                            'tipo'            => '2',
                            'id_asiento_cabecera' => '',
                            'id_secuencia'    => $numero_factura,
                            'id_pago'         => $data["forma_pago"],
                            'check'           => $cheque,
                            'fecha_cheque'    => $data['fecha_cheque'],
                            'id_caja_banco'   => $data['banco'],
                            'no_cheque'       => $data['num_cheque'],
                            'fecha_comprobante' => $data['fecha'],
                            'secuencia'       => $numero_factura,
                            'id_empresa'      => session("id_empresa"),
                            'id_proveedor'    => $data["nombre_acreedores"],
                            'valor_pago'      => $data['total_favor'],
                            'valor'           => $data['total_favor'],
                            'comentarios'     => $data['nota'],
                            'id_usuariocrea'  => Auth::user()->id,
                            'id_usuariomod'   => Auth::user()->id,
                            'ip_creacion'     => $ip,
                            'ip_modificacion' => $ip,
                            'rt_asumida_estado' => $rt_estado,
                            'rt_asumida_valor' => $suma_de_retenciones,
                            'fecha_entregado'  => $cheque == 1 ? date("Y-m-d") : NULL
                        ];
                        $id_comprobante = ComprobanteEgreso::insertGetId($input_comprobante);
                        DetalleComprobanteEgreso::insert([
                            'id_comprobante'                 => $id_comprobante,
                            'id_secuencia'                   => $numero_factura,
                            'saldo_base'                     => $data['total_favor'],
                            'abono'                          => $data['total_favor'],
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip,
                            'ip_modificacion'                => $ip,
                            'id_usuariocrea'                 => Auth::user()->id,
                            'id_usuariomod'                  => Auth::user()->id,
                        ]);
                    }
                } else {

                    if ($data['contador_total'] > 0) {
                        $rt_asimida_creado = 0;
                        $numero_factura = LogAsiento::getSecuencia(1, 1);
                        $nombreProveedor = Proveedor::find($data["nombre_acreedores"]);
                        // asiento cabecera
                        $cod_sucurs = PuntoEmision::where('estado', '1')
                            ->where('id', $r->sucursal)->first();
                        $c_sucursal = $cod_sucurs->codigo_sucursal;
                        $arrayAsientoCabecera = [
                            'sucursal' => $c_sucursal,
                            'fecha_asiento' => $r->fecha,
                            'fact_numero' => $numero_factura,
                            'id_empresa' => session('id_empresa'),
                            'observacion' => $r->concepto,
                            'valor' => $data["valor"],
                            'id_usuariocrea' => Auth::user()->id,
                            'id_usuariomod' => Auth::user()->id,
                            'ip_creacion' => $ip,
                            'ip_modificacion' => $ip,
                        ];

                        $id_asiento_cabecera = Contabilidad::crearAsiento('proveedores', 'comprobanteegreso', $arrayAsientoCabecera,  $r, "cajaBanco");
                        // dd($id_asiento_cabecera);
                        //dd(AsientosCabecera::find($id_asiento_cabecera), AsientosDetalles::where("id_asiento_cabecera", $id_asiento_cabecera)->get());
                        if ($id_asiento_cabecera['status'] == 'error') {
                            throw new Exception($id_asiento_cabecera['message']);
                        }
                        $cheque = 0;
                        if (isset($data["cheque_entregado"])) {
                            $cheque = 1;
                        }
                        $input_comprobante = [
                            'descripcion'     => $data['concepto'],
                            'estado'          => '1',
                            'beneficiario'    => $nombreProveedor->nombrecomercial,
                            'fecha_cheque'    => $data['fecha_cheque'],
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'check'           => $cheque,
                            'id_secuencia'    => $numero_factura,
                            'id_pago'         => $data['forma_pago'],
                            'id_caja_banco'   => $data['banco'],
                            'no_cheque'       => $data['num_cheque'],
                            'fecha_comprobante' => $data['fecha'],
                            'secuencia'       => $numero_factura,
                            'id_empresa'      => session("id_empresa"),
                            'id_proveedor'    => $data["nombre_acreedores"],
                            'valor_pago'      => $data['valor'],
                            'valor'      => $data['valor'],
                            'comentarios'     => $data['nota'],
                            'id_usuariocrea'  => Auth::user()->id,
                            'id_usuariomod'   => Auth::user()->id,
                            'ip_creacion'     => $ip,
                            'ip_modificacion' => $ip,
                            'fecha_entregado'  => $cheque == 1 ? date("Y-m-d") : NULL,
                        ];
                        $id_comprobante = ComprobanteEgreso::insertGetId($input_comprobante);
                        $retencionesAsumidas = 0;
                        for ($i = 0; $i < $data['contador_total']; $i++) {
                            if (!is_null($data['abono' . $i])) {
                                if ($data['abono' . $i] > 0) {

                                    if (!is_null($nombreProveedor)) {
                                        $nuevo_saldof = $data['abono' . $i];

                                        if (isset($data['valorasumir' . $i])) {
                                            if ($rt_asimida_creado == 0) {
                                                $rt_asimida_creado = 1;
                                                $retencionesAsumidas += $data['valorasumir' . $i];
                                                $nuevo_saldof = $data["valor"] - $data['valorasumir' . $i];
                                                $rt_asimida_creado = 1;
                                            }
                                        }
                                    }
                                    $consulta_compra = Compras::where('id', $data['id_actualiza' . $i])->first();
                                    if (!is_null($consulta_compra)) {
                                        $valor_compra = $data['abono' . $i];
                                        if ($rt_asimida_creado == 1) {
                                            $valor_compra = $data["valor"];
                                        }
                                        DetalleComprobanteEgreso::insert([
                                            'id_comprobante'                 => $id_comprobante,
                                            'observacion'                    => $data['concepto'],
                                            'id_compra'                      => $consulta_compra->id,
                                            'id_secuencia'                   => $data['numero' . $i],
                                            'saldo_base'                     => $data['saldo' . $i],
                                            'abono'                          => $valor_compra,
                                            'estado'                         => '1',
                                            'ip_creacion'                    => $ip,
                                            'ip_modificacion'                => $ip,
                                            'id_usuariocrea'                 => Auth::user()->id,
                                            'id_usuariomod'                  => Auth::user()->id,
                                        ]);
                                    }
                                }
                            }
                        }
                        if ($rt_asimida_creado == 1) {
                            // Guardar en el ct_asientos_Detalle
                            $rt_asimida_creado = 1;
                        }
                        if ($data['comprobarx'] == 1) {
                            if (!isset($data["retencionIn0"])) {
                                $aux_numero_factura = LogAsiento::getSecuencia(2, 1);
                                $input_comprobante = [
                                    'descripcion'     => $data['concepto'] . ' REF: ' . $aux_numero_factura,
                                    'estado'          => '1',
                                    'beneficiario'    => $nombreProveedor->nombrecomercial,
                                    'fecha_cheque'    => $data['fecha_cheque'],
                                    'id_asiento_cabecera' => '',
                                    'check'           => $cheque,
                                    'id_secuencia'    => $aux_numero_factura,
                                    'id_pago'         => $data['forma_pago'],
                                    'id_caja_banco'   => $data['banco'],
                                    'no_cheque'       => $data['num_cheque'],
                                    'fecha_comprobante' => $data['fecha'],
                                    'secuencia'       => $aux_numero_factura,
                                    'id_empresa'      => session("id_empresa"),
                                    'id_proveedor'    => $data["nombre_acreedores"],
                                    'valor_pago'      => $data['valor'],
                                    'valor'      => $data['valor'],
                                    'comentarios'     => $data['nota'],
                                    'id_usuariocrea'  => Auth::user()->id,
                                    'id_usuariomod'   => Auth::user()->id,
                                    'ip_creacion'     => $ip,
                                    'ip_modificacion' => $ip,
                                    'fecha_entregado'  => $cheque == 1 ? date("Y-m-d") : NULL,
                                ];
                                $id_comprobante = ComprobanteEgreso::insertGetId($input_comprobante);
                                DetalleComprobanteEgreso::insert([
                                    'id_comprobante'                 => $id_comprobante,
                                    'observacion'                    => $data['concepto'],
                                    'id_compra'                      => $consulta_compra->id,
                                    'id_secuencia'                   => $aux_numero_factura,
                                    'saldo_base'                     => $data['saldo' . $i],
                                    'abono'                          => $valor_compra,
                                    'estado'                         => '1',
                                    'ip_creacion'                    => $ip,
                                    'ip_modificacion'                => $ip,
                                    'id_usuariocrea'                 => Auth::user()->id,
                                    'id_usuariomod'                  => Auth::user()->id,
                                ]);
                            }
                        }
                        for ($i = 0; $i < $data['contador_total']; $i++) {
                            if (!is_null($data['abono' . $i]) && $data['abono' . $i] > 0) {
                                $nuevo_saldo = 0;
                                $consulta_compra = Compras::where('id', $data['id_actualiza' . $i])->where('id_empresa', session("id_empresa"))->first();
                                if (!empty($consulta_compra) || $consulta_compra != null) {
                                    if ($data['abono' . $i] > 0) {
                                        $new_valor = $data['abono' . $i];
                                        if ($data['valorasumir' . $i] > 0) {
                                            $new_valor = $data->valor_cheque - $data['valorasumir' . $i];
                                        }
                                        if ($new_valor > ($consulta_compra->valor_contable)) {
                                            $nuevo_saldo = $new_valor - $consulta_compra->valor_contable;
                                        } else {
                                            $nuevo_saldo = $consulta_compra->valor_contable - $new_valor;
                                        }
                                        if ($nuevo_saldo != 0) {
                                            $consulta_compra->estado          = '2';
                                            $consulta_compra->valor_contable  = $nuevo_saldo;
                                            $consulta_compra->ip_modificacion = $ip;
                                            $consulta_compra->id_usuariomod   = Auth::user()->id;
                                        } else {

                                            $consulta_compra->estado          = '3';
                                            $consulta_compra->valor_contable  = $nuevo_saldo;
                                            $consulta_compra->ip_modificacion = $ip;
                                            $consulta_compra->id_usuariomod   = Auth::user()->id;
                                        }
                                        $consulta_compra->save();
                                    }
                                }
                            }
                        }
                    }
                }
                $cont++;
                $r->c = 'ComprobanteEgreso';
                $r->s = 'guardarComprobanteEgreso';
                $r->d = $origin['d'];
                $r->m = ComprobanteEgreso::$modelo;
                $r->o = 'Se guardo el comprobante';
                $cont++;
            } else {
                $cheque = 0;
                if (isset($data["cheque_entregado"])) {
                    $cheque = 1;
                }
                $array = [
                    'ip_modificacion' => $ip,
                    'id_usuariomod' => Auth::user()->id,
                    'nota' => $data["nota"],
                    'descripcion' => $data["concepto"],
                    'check' => $cheque,
                ];
                ComprobanteEgreso::where("id", $data["id"])->update($array);
                $r->c = 'ComprobanteEgreso';
                $r->s = 'editarComprobanteEgreso';
                $r->d = $origin['d'];
                $r->m = ComprobanteEgreso::$modelo;
                $r->o = 'Se actualizo el comprobante con id.: ' . $data["id"];
                $cont++;
            }
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
                'return' => '',
            ];
            $r->c = 'comprobante de egreso';
            $r->s = 'saveComprobanteEgreso';
            $r->d =  $origin['d'];
            $r->m = Proveedor::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'return' => 'proveedores/comprobantedeegreso',
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function buscarAntAnticipos($r)
    {
        try {
            $sql = DetalleComprobanteEgreso::join('ct_comprobante_egreso as egr', 'egr.id', 'ct_detalle_comprobante_egreso.id_comprobante')
                ->where('egr.estado', '<>', '0')
                ->where('egr.valor_pago', '>', 0)
                ->where('id_empresa', session("id_empresa"))
                ->where('tipo', '2')
                ->where('egr.id_proveedor', $r->id_proveedor)
                ->get();
            $html = "";
            foreach ($sql as $value) {
                $html .= "<tr>
                        <td style='text-align:center;'>{$value->fecha_comprobante}</td>
                        <td style='text-align:center;'>ACR-EG</td>
                        <td style='text-align:center;'>{$value->id_secuencia}</td>
                        <td style='text-align:center;color: #F3785A'>{$value->descripcion}</td>
                        <td style='text-align:center;'>{$value->valor_pago}</td>
                        <td style='text-align:center;'>{$value->valor}</td>
                    </tr>";
            }
            return ['html' => $html, "status" => "success"];
        } catch (Exception $e) {
            return ['html' => $html, "status" => "error"];
        }
    }
    public static function allComprobanteEgreso($r)
    {
        if (isset($r->d) && isset($r->h)) {
            $data = ComprobanteEgreso::join('proveedor', 'ct_comprobante_egreso.id_proveedor', 'proveedor.id')
                ->leftjoin("ct_asientos_cabecera", "ct_comprobante_egreso.id_asiento_cabecera", "ct_asientos_cabecera.id")
                ->leftjoin("users as us1", "ct_comprobante_egreso.id_usuariocrea", "us1.id")
                ->leftjoin("users as us2", "ct_comprobante_egreso.id_usuariomod", "us2.id")
                ->whereBetween('ct_comprobante_egreso.created_at', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
                ->where('ct_comprobante_egreso.id_empresa', session('id_empresa'))
                ->orderBy('ct_comprobante_egreso.id', 'desc')
                ->select(DB::raw('IF(`ct_comprobante_egreso`.estado=0,CONCAT(SUBSTRING(us2.nombre1,1,1),". ", us2.apellido1),"") as nombre_modi'), DB::raw('CONCAT(SUBSTRING(us1.nombre1,1,1),". ", us1.apellido1) as nombre_crea'), 'ct_asientos_cabecera.valor as valorfinal', 'proveedor.razonsocial as razonsocial', 'ct_comprobante_egreso.*')->get();

            return $data;
        } else if (isset($r["id"])) {
            $egreso = ComprobanteEgreso::find($r["id"]);
            //dd($egreso);
            return $egreso;
        }
    }
    public static function cambiarEstado($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (ComprobanteEgreso::where('id', $r->id)->update(['estado' => $estado])) {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro guardado correctamente',
                    'data' => $r->input(),
                ];
            } else {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Error al actualizar el estado del ',
                    'data' => $r->input(),
                ];
            }
        } catch (Exception $e) {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
        }
        return json_encode($arrayRespuesta);
    }
    public static function cambiarEstadoCruce($r)
    {
        $ip = Utilidades::getRealIP();
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (CruceValores::where('id', $r->id)->update(['estado' => $estado, 'observacion' => $r->text])) {

                // ACUTALIZAR VALORES DEVUELTOS
                $comprobante_ingreso = CruceValores::where('id', $r->id)->where('id_empresa', session("id_empresa"))->first();
                $estado_ingreso = CruceValores::where('id', $r->id)->where('estado', '<>', 0)->where('id_empresa', session("id_empresa"))->first();
                $detalle_ingreso = DetalleCruce::where('id_comprobante', $r->id)->where('estado', '1')->get();
                $pago_cruce = DetallePagoCruceProv::where('id_comprobante', $r->id)->where('estado', '1')->get();
                if (!empty($estado_ingreso)) {

                    $estado_ingreso->estado = 0;
                    $estado_ingreso->ip_creacion = $ip;
                    $estado_ingreso->ip_modificacion = $ip;
                    $estado_ingreso->id_usuariomod = Auth::user()->id;

                    $comprobante_ingreso->save();

                    if (!is_null($detalle_ingreso)) {

                        foreach ($detalle_ingreso as $det_ing) {
                            $detalle_ing = DetalleCruce::where('id', $det_ing->id)->where('estado', '1')->first();

                            $compras = Compras::where('id', $det_ing->id_factura)->where('estado', '>', '0')->where('id_empresa', session("id_empresa"))->first();

                            if (!is_null($compras)) {

                                $valor = $compras->valor_contable;
                                $suma = ($det_ing->total) + $valor;

                                $compras->valor_contable = $suma;
                                $compras->ip_creacion = $ip;
                                $compras->ip_modificacion = $ip;
                                $compras->id_usuariomod = Auth::user()->id;
                                $compras->save();

                                $detalle_ing->estado = 0;
                                $detalle_ingreso->ip_creacion = $ip;
                                $detalle_ingreso->ip_modificacion = $ip;
                                $detalle_ingreso->id_usuariocrea = Auth::user()->id;
                                $detalle_ingreso->id_usuariomod = Auth::user()->id;
                                $detalle_ingreso->save();

                                if (!is_null($pago_cruce)) {
                                    foreach ($pago_cruce as $pag_cruce) {

                                        $comprobante_egreso = ComprobanteEgreso::where('id', $pag_cruce->id_comp_ingreso)->first();
                                        $val_ant = $pag_cruce->valor_ant;
                                        if (!is_null($comprobante_egreso)) {

                                            $comprobante_egreso->valor_pago = $val_ant;
                                            $comprobante_egreso->ip_modificacion = $ip;
                                            $comprobante_egreso->id_usuariomod = Auth::user()->id;

                                            $comprobante_egreso->save();
                                        }
                                    }
                                }
                                $asiento = AsientosCabecera::find($comprobante_ingreso->id_asiento_cabecera);

                                if (!is_null($asiento)) {
                                    $asiento->estado = 1;
                                    $asiento->id_usuariomod =  Auth::user()->id;
                                    $asiento->save();

                                    $detalles = $asiento->detalles;
                                    $id_asiento = AsientosCabecera::insertGetId([
                                        'observacion'     => strtoupper($r->text),
                                        'fecha_asiento'   => $asiento->fecha_asiento,
                                        'id_empresa'      => session("id_empresa"),
                                        'fact_numero'     => $comprobante_ingreso->secuencia,
                                        'valor'           => $asiento->valor,
                                        'ip_creacion'     => $ip,
                                        'ip_modificacion' => $ip,
                                        'id_usuariocrea'  => Auth::user()->id,
                                        'id_usuariomod'   => Auth::user()->id,
                                        'sucursal'        => $asiento->sucursal,
                                    ]);
                                    foreach ($detalles as $value) {
                                        AsientosDetalles::create([
                                            'id_asiento_cabecera' => $id_asiento,
                                            'id_plan_cuenta'      => $value->id_plan_cuenta,
                                            'debe'                => $value->haber,
                                            'haber'               => $value->debe,
                                            'descripcion'         => $value->descripcion,
                                            'fecha'               => $asiento->fecha_asiento,
                                            'ip_creacion'         => $ip,
                                            'ip_modificacion'     => $ip,
                                            'id_usuariocrea'      => Auth::user()->id,
                                            'id_usuariomod'       => Auth::user()->id,
                                        ]);
                                    }
                                    LogAsiento::anulacion("AC-CV", $id_asiento, $asiento->id);
                                }
                            }
                        }
                    }
                }
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro guardado correctamente',
                    'data' => $r->input(),
                ];
            } else {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Error al actualizar el estado del ',
                    'data' => $r->input(),
                ];
            }
        } catch (Exception $e) {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
        }
        return json_encode($arrayRespuesta);
    }
    public static function mostrarPdf($id)
    {
        $data = ComprobanteEgreso::find($id);
        $contenidoHtml = 'Sin datos';
        if (!is_null($data)) {
            $contenidoHtml .= '<div class="row">
                    <div class="col-12">
                        <h4>
                            <i class="fas fa-globe"></i> ' . config('data.info')->nombre_info . ' V' . config('data.info')->version_info . '.
                            <small class="float-right">Fecha: ' . date('d/m/Y', strtotime($data->fecha_comprobante)) . '</small>
                        </h4>
                    </div>
                </div>
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>' . $data->proveedor->razonsocial . '</strong><br>
                            ' . $data->proveedor->proveedor . '<br>
                            ' . $data->proveedor->direccion . '<br>
                            Tel�fono: ' . $data->proveedor->telefono1 . ' ' . $data->proveedor->telefono2 . '<br>
                            E-mail: <a href="mailto:' . $data->proveedor->email . '">' . $data->proveedor->email . '</a>
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>' . config('data.empresa')->razonsocial . '</strong><br>
                            ' . config('data.empresa')->id . '<br>
                            ' . config('data.empresa')->direccion . '<br>
                            Tel�fono: ' . config('data.empresa')->telefono1 . ' ' . config('data.empresa')->telefono2 . '<br>
                            E-mail: <a href="mailto:' . config('data.empresa')->email . '">' . config('data.empresa')->email . '</a>
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <b>No:' . $data->secuencia . '</b><br>
                        <br>
                        <b>POR ID:</b> ' . $data->valor . '<br>
                        <b>Account:</b> 968-34567
                    </div>
                </div>
                <div class="row"><div class="col-12 table-responsive"><table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Secuencia</th>
                            <th>Saldo</th>
                            <th>Div</th>
                            <th>Valor</th>
                            <th>Comprobante</th>
                            <th>Observaci�n</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach ($data->detalles as $row) {
                $contenidoHtml .= '<tr>
                <td>' . $row->id_secuencia . '</td>
                <td>' . $row->saldo_base . '</td>
                <td align="right">$</td>
                <td align="left">' . $row->abono . '</td>
                <td align="center">' . $row->id_comprobante . '</td>
                <td align="right">' . $row->observacion . '</td>
                </tr>';
            }
            $contenidoHtml .= '</tbody>
                </table></div></div>
                    <div class="row">
                        <div class="col-6">
                            <p class="lead">Payment Methods:</p>
                            <img src="' . url('v3/') . '/dist/img/credit/visa.png" alt="Visa">
                            <img src="' . url('v3/') . '/dist/img/credit/mastercard.png" alt="Mastercard">
                            <img src="' . url('v3/') . '/dist/img/credit/american-express.png" alt="American Express">
                            <img src="' . url('v3/') . '/dist/img/credit/paypal2.png" alt="Paypal">
                            <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                              ' . nl2br($data->descripcion) . '
                            </p>
                        </div>
                    </div> 
                    <div class="row no-print">
                        <div class="col-12">
                            <a href="' . url('v3/') . '/compras/printFactura?id=12545" rel="noopener" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> <span class="botones">Imprimir</span></a>
                            <button type="button" class="btn btn-success float-right"><i class="far fa-file-code"></i>  <span class="botones">Generar XML</span></button>
                            <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;"><i class="fas fa-download"></i>  <span class="botones">Generar PDF</span></button>
                        </div>
                    </div>';
        }
        return $contenidoHtml;
    }
    public static function compraBuscarProveedor($r)
    {
        $id = $r->id;
        $productos = DB::table('proveedor')->where('id', $id)->first();
        if (!is_null($productos)) {
            $detalle = DetalleAcreedores::where('id_proveedor', $productos->id)->get();
            $compras = Compras::where("proveedor", $productos->id)->where("id_empresa", session('id_empresa'))->where("estado", ">", "0")->get();
            $num_mayor = 0;
            foreach ($compras as $compra) {
                if (intval($compra->secuencia_factura) > $num_mayor) {
                    $num_mayor =  intval($compra->secuencia_factura);
                }
            }
            if (count($detalle) == 0) {
                return ['value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => "", 'autorizacion' => "", 'caducidad' => ''];
            }
            foreach ($detalle as $value) {

                if ($num_mayor >= intval($value->sinicia)  and $num_mayor <= intval($value->sfin)) {
                    return ['value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => $value->serie, 'autorizacion' => $value->autorizacion, 'caducidad' => ''];
                }
            }
            return ['value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => "", 'autorizacion' => "", 'caducidad' => ''];
        } else {
            return ['value' => 'no'];
        }
    }
    public static function obtener_anticipos($r)
    {
        $id_proveedor = $r->proveedor;
        $deudas = DB::table('ct_asientos_cabecera as c')
            ->join('ct_comprobante_egreso as co', 'co.id_asiento_cabecera', 'c.id')
            ->where('co.id_proveedor', $id_proveedor)
            ->where('c.id_empresa', session("id_empresa"))
            ->where('co.tipo', 2)
            ->where('co.valor_pago', '>', 0)
            ->where('co.estado', 1)
            ->select('co.secuencia', 'c.observacion', 'c.fecha_asiento', 'co.id_referencia', 'co.id_proveedor', 'co.valor_pago as valor_abono', 'co.id')
            ->orderby('co.id', 'desc')
            ->get();
        if (!empty($deudas)) {
            return $deudas;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public static function saveCruceValores($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            if ($data['id'] == '') {
                DB::enableQueryLog();
                // secuencia
                $numero_factura = LogAsiento::getSecuencia(6, 2);
                // armar la cabecera del asiento
                $cabecera = [
                    'observacion'                   => $data['concepto'],
                    'fecha_asiento'                 => $data["fecha"],
                    'fact_numero'                   => $numero_factura,
                    'valor'                         => $data["valores"],
                    'id_empresa'                    => session("id_empresa"),
                    'estado'                        => '1',
                    'ip_creacion'                   => $ip,
                    'ip_modificacion'               => $ip,
                    'id_usuariocrea'                => Auth::user()->id,
                    'id_usuariomod'                 => Auth::user()->id,
                    'sucursal'                      => $data['sucursalc'],
                ];
                // guardar en asiento cabecera
                $inputCruce = [
                    'detalle'                       => $data['concepto'],
                    'id_asiento_cabecera'           => '0', // cambiar es provisional
                    'fecha_pago'                    => $data["fecha"],
                    'id_proveedor'                  => $data['proveedor'],
                    'observacion'                   => $data["concepto"],
                    'secuencia'                     => $numero_factura,
                    'total_disponible'              => $data['valores'],
                    'id_empresa'                    => session("id_empresa"),
                    'estado'                        => '1',
                    'ip_creacion'                   => $ip,
                    'ip_modificacion'               => $ip,
                    'id_usuariocrea'                => Auth::user()->id,
                    'id_usuariomod'                 => Auth::user()->id,
                    'sucursal'                      => $data['sucursalc'],
                ];
                $id_comprobante = CruceValores::insertGetId($inputCruce);
                if ($data['contador_total'] != null && $data['contador'] != null) {
                    for ($i = 0; $i <= $data['contador_total']; $i++) {
                        if (!is_null($data['numero_a' . $i])) {
                            $consulta_anticipo = ComprobanteEgreso::where('id', $data['id_act' . $i])->where('id_empresa', session("id_empresa"))->first();
                            if (!is_null($consulta_anticipo)) {
                                $saldo_redondeado1 = $data['abono_a' . $i];
                                $saldo_redondeado2 = $data['saldo_a' . $i];
                                $saldo_redondeado = $saldo_redondeado2 - $saldo_redondeado1;
                                if (($data['abono_a' . $i]) > 0) {
                                    if ($saldo_redondeado >= 0) {
                                        $consulta_anticipo->valor_pago  = $saldo_redondeado;
                                        $consulta_anticipo->estado = '1';
                                        $consulta_anticipo->id_usuariomod = Auth::user()->id;
                                        $consulta_anticipo->ip_modificacion = $ip;
                                    } else {
                                        $consulta_anticipo->valor_pago = '0.00';
                                        $consulta_anticipo->estado = '1';
                                        $consulta_anticipo->id_usuariomod = Auth::user()->id;
                                        $consulta_anticipo->ip_modificacion = $ip;
                                    }
                                    $consulta_anticipo->save();
                                    DetallePagoCruceProv::insert([
                                        'id_comprobante' => $id_comprobante,
                                        'id_comp_ingreso' => $consulta_anticipo->id,
                                        'numero' => $data['numero_a' . $i],
                                        'fecha' =>  $data['fecha'],
                                        'valor_ant' => $data['saldo_a' . $i],
                                        'valor' => $data['abono_a' . $i],
                                        'concepto' => $data['concepto' . $i],
                                        'estado' => '1',
                                        'ip_creacion' => $ip,
                                        'ip_modificacion' => $ip,
                                        'id_usuariocrea' => Auth::user()->id,
                                        'id_usuariomod' => Auth::user()->id,
                                    ]);
                                }
                            }
                        }
                    }
                    for ($i = 0; $i <= $data['contador']; $i++) {
                        if (!is_null($data['abono' . $i])) {
                            if (($data['abono' . $i]) > 0) {
                                if (!is_null($data['id_actualiza' . $i])) {
                                    $consulta_facturas = Compras::where('id', $data['id_actualiza' . $i])->where('estado', '<>', '0')->first();
                                    if (!is_null($consulta_facturas)) {
                                        if ($data['abono' . $i] > $consulta_facturas->valor_contable) {
                                            $total = $data['abono' . $i] - $consulta_facturas->valor_contable;
                                            $saldo_redondeado = $total;
                                        } else {
                                            $total = $consulta_facturas->valor_contable - $data['abono' . $i];
                                            $saldo_redondeado = $total;
                                        }
                                        if ($total > 0) {
                                            //!! no actualizao el estado de la factura porque tiene que segui saliendo en las deudas del proveedor
                                            $consulta_facturas->valor_contable = $saldo_redondeado;
                                            $consulta_facturas->id_usuariomod = Auth::user()->id;
                                            $consulta_facturas->ip_modificacion = $ip;
                                        } else {
                                            $consulta_facturas->estado = '3'; // este estado es paara que ya no salga en la consulta solo si cumple el total de la factura pagada
                                            $consulta_facturas->valor_contable = '0.00';
                                            $consulta_facturasid_usuariomod = Auth::user()->id;
                                            $consulta_facturas->ip_modificacion = $ip;
                                        }
                                        $consulta_facturas->save();
                                    }
                                    DetalleCruce::insert([
                                        'id_comprobante'                 => $id_comprobante,
                                        'fecha'                          => $data['fecha'],
                                        'tipo'                           => $data['tipo' . $i],
                                        'observaciones'                  => $data['concepto_a' . $i],
                                        'id_factura'                     => $consulta_facturas->id,
                                        'secuencia_factura'              => $data['numero' . $i],
                                        'total_factura'                  => $data['saldo' . $i],
                                        'total'                          => $data['abono' . $i],
                                        'estado'                         => '1',
                                        'ip_creacion'                    => $ip,
                                        'ip_modificacion'                => $ip,
                                        'id_usuariocrea'                 =>  Auth::user()->id,
                                        'id_usuariomod'                  =>  Auth::user()->id,
                                    ]);
                                }
                            }
                        }
                    }
                    $consulta_en_proveedor = Proveedor::where('id', $data["proveedor"])->first();
                    if (($consulta_en_proveedor) != null) {
                        $desc_cuenta = PlanCuentas::where('id', $consulta_en_proveedor->id_cuentas)->first();
                        //  buscar por el plan --actualmente esta esto
                        // $id_plan_config = LogConfig::busqueda('1.01.04.03.01');
                        // $cuenta_ant_prov = PlanCuentas::where('id', $id_plan_config)->first();
                        // verific que tanto el registro cuenta_ant_prov y  desc_cuenta dea diferente de null
                        //crear los detalle
                    }
                }
                $cont++;
                $r->c = 'Cruce Valor';
                $r->s = 'saveCruceValor';
                $r->d = $origin['d'];
                $r->m = CruceValores::$modelo;
                $r->o = 'Se guardo el cruce con el ';
                $cont++;
            }
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
            $r->c = 'Cruce de valores';
            $r->s = 'saveCruceValor';
            $r->d =  $origin['d'];
            $r->m = Proveedor::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function allCruce($r)
    {
        if (isset($r->d) && isset($r->h)) {
            return CruceValores::where('ct_cruce_valores.id_empresa', session("id_empresa"))
                ->join("proveedor", "proveedor.id", "ct_cruce_valores.id_proveedor")
                ->leftjoin("users as us1", "ct_cruce_valores.id_usuariocrea", "us1.id")
                ->leftjoin("users as us2", "ct_cruce_valores.id_usuariomod", "us2.id")
                ->whereBetween("ct_cruce_valores.created_at", [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
                ->get([
                    'ct_cruce_valores.*',
                    'proveedor.nombrecomercial',
                    DB::raw('IF(ct_cruce_valores.estado=0,CONCAT(SUBSTRING(us2.nombre1,1,1),". ", us2.apellido1),"") as nombre_modi'),
                    DB::raw('CONCAT(SUBSTRING(us1.nombre1,1,1),". ", us1.apellido1) as nombre_crea'),
                ]);
        } else if (isset($r["id"])) {
            return  CruceValores::find($r["id"]);
        }
    }
    public static function datosPdf($id)
    {
        $datos = DB::table('ct_cruce_valores as crv')
            ->leftjoin('ct_detalle_cruce as cdc', 'cdc.id_comprobante', 'crv.id')
            ->where('crv.id', $id)
            ->select('crv.*', 'cdc.*')
            ->first();
        $nombre = Proveedor::where('id', $datos->id_proveedor)->first();
        $datos2 = DetalleCruce::where('id_comprobante', $id)->get();
        $details = DetallePagoCruceProv::where('id_comprobante', $id)->get();
        $empresa = Empresas::find(session("id_empresa"));
        return compact("datos", "nombre", "datos2", "details", "empresa");
    }


    public static function datosPdfingreso($id)
    {

        $id_empresa = session()->get('id_empresa');
        $comp_egreso = ComprobanteEgresoVarios::where('id_empresa', $id_empresa)->where('id', $id)->first();
        //dd($comp_egreso->usuario);
        $empresa = Empresas::where('id', $comp_egreso->id_empresa)->first();
        $letras = new Numeros_Letras();
        $asiento_cabecera = AsientosCabecera::where('id', $comp_egreso->id_asiento_cabecera)->first();
        $total_str = $letras->convertir($asiento_cabecera->valor, "DOLARES", "CTVS");
        $asiento_detalle = AsientosDetalles::where('estado', '1')->where('id_asiento_cabecera', $asiento_cabecera->id)->get();
        return compact("comp_egreso", "empresa", "letras", "asiento_cabecera", "total_str", "asiento_detalle");
    }

    public static function getLastNumberChe($r)
    {
        $numero = ComprobanteEgreso::where("id_caja_banco", $r->idBanco)->orderBy(DB::raw('CAST(no_cheque AS UNSIGNED)'))->first([DB::raw('IFNULL(no_cheque,"") as no_cheque')]);
        return json_encode($numero);
    }

    public static function dataFactura($r)
    {
        $compras = Compras::where('id_empresa', session()->get('id_empresa'))
            ->where('estado', '>', 0)->where('valor_contable', '>', 0)->where('tipo', '<>', 3);
        if ($r->desde != null && $r->hasta != null) {
            $compras = $compras->whereBetween('fecha', [$r->desde . ' 00:00:00', $r->hasta . ' 23:59:59']);
        }
        if ($r->hasta != null && $r->desde == null) {
            $compras = $compras->where('fecha', '<=', $r->hasta);
        }
        if ($r->id_proveedor != null) {
            $compras = $compras->where('proveedor', $r->id_proveedor);
        }
        $compras = $compras->groupBy('termino');
        return $compras;
    }
}
