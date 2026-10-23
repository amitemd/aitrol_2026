<?php

namespace Sis_medico\v3\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Devolucion_Productos;

class Clientes extends Model
{
    protected $table = 'ct_clientes';

    private static $modelo = 'Clientes';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function searchClient($term = '')
    {
        return self::select('identificacion as id', DB::raw("CONCAT(identificacion, ' | ', nombre) as text"))
            ->where('estado', 1)
            ->where(function ($query) use ($term) {
                $query->where('identificacion', 'like', '%' . $term . '%')
                    ->orWhere('nombre', 'like', '%' . $term . '%');
            })
            ->orderBy('nombre')
            ->limit(50)
            ->get();
    }

    public static function getCuentasxCobrar($r)
    {
        $fechaDesde = $r->input('d');
        $fechaHasta = $r->input('h');
        $clientes = array_values(array_filter((array) $r->input('clientes', [])));
        $cliente = trim((string) $r->input('cliente', $r->input('c', $r->input('id_cliente', ''))));
        $buscar = trim((string) $r->input('buscar', ''));
        $tipoSeguro = strtolower(trim((string) $r->input('tipo_seguro', '')));
        $seguros = array_values(array_unique(array_filter(array_map(function ($seguro) {
            return is_scalar($seguro) ? trim((string) $seguro) : '';
        }, (array) $r->input('seguros', [])), function ($seguro) {
            return $seguro !== '';
        })));
        $excluirGrupoRobles = filter_var($r->input('excluir_grupo_robles', false), FILTER_VALIDATE_BOOLEAN);
        $rucsGrupoRobles = array_values(array_unique(array_filter(array_map(function ($ruc) {
            return is_scalar($ruc) ? trim((string) $ruc) : '';
        }, (array) $r->input('rucs_grupo_robles', [])), function ($ruc) {
            return $ruc !== '';
        })));

        return DB::table('ct_ventas as v')
            ->join('ct_clientes as c', 'v.id_cliente', '=', 'c.identificacion')
            ->leftJoin('seguros as s', 'v.seguro_paciente', '=', 's.id')
            ->select(
                'v.id',
                'c.identificacion',
                'c.nombre',
                's.id as id_seguro',
                's.nombre as nombre_seguro',
                's.tipo as tipo_seguro',
                'v.fecha',
                DB::raw('DATE_ADD(v.fecha, INTERVAL COALESCE(v.dias_plazo, 0) DAY) as vencimiento'),
                DB::raw('DATEDIFF(NOW(), DATE_ADD(v.fecha, INTERVAL COALESCE(v.dias_plazo, 0) DAY)) AS dias_transcurridos'),
                'v.tipo',
                'v.id_asiento',
                'v.nro_comprobante',
                'v.concepto as detalle',
                DB::raw('ROUND(v.total_final, 2) as total_final'),
                DB::raw('IFNULL((SELECT SUM(COALESCE(di.total, 0)) FROM ct_detalle_comprobante_ingreso di JOIN ct_comprobante_ingreso ci ON di.id_comprobante = ci.id WHERE di.id_factura = v.id AND ci.estado != 0), 0) as ingresos'),
                DB::raw('IFNULL((SELECT SUM(COALESCE(valor_fuente, 0) + COALESCE(valor_iva, 0)) FROM ct_cliente_retencion WHERE id_factura = v.id AND estado != 0), 0) as retenciones'),
                DB::raw('IFNULL((SELECT SUM(COALESCE(valor_contable, 0)) FROM ct_debito_clientes WHERE id_factura = v.id AND estado != 0), 0) as debitos'),
                DB::raw('IFNULL((SELECT SUM(COALESCE(dc.total, 0)) FROM ct_detalle_cruce_clientes dc JOIN ct_cruce_valores_cliente cv ON dc.id_comprobante = cv.id WHERE dc.id_factura = v.id AND cv.estado != 0), 0) + IFNULL((SELECT SUM(COALESCE(total, 0)) FROM ct_cruce_cuentas_clientes WHERE id_factura = v.id AND estado != 0), 0) as cruces'),
                DB::raw('IFNULL((SELECT SUM(total_credito) FROM ct_nota_credito_clientes WHERE id_factura = v.id AND estado != 0), 0) as n_c'),
                DB::raw('ROUND(v.valor_contable, 2) as valor_contable')
            )
            ->when($fechaDesde && $fechaHasta, function ($query) use ($fechaDesde, $fechaHasta) {
                return $query->whereBetween('v.fecha', [$fechaDesde . ' 00:00:00', $fechaHasta . ' 23:59:59']);
            })
            ->when(count($clientes) > 0, function ($query) use ($clientes) {
                return $query->whereIn('c.identificacion', $clientes);
            })
            ->when(count($clientes) === 0 && $cliente !== '', function ($query) use ($cliente) {
                return $query->where('c.identificacion', explode('|', $cliente)[0]);
            })
            ->when($tipoSeguro === 'publico', function ($query) {
                return $query->where('s.tipo', 0);
            })
            ->when($tipoSeguro === 'privado', function ($query) {
                return $query->whereIn('s.tipo', [1, 2]);
            })
            ->when(count($seguros) > 0, function ($query) use ($seguros) {
                return $query->whereIn('v.seguro_paciente', $seguros);
            })
            ->when($excluirGrupoRobles && !empty($rucsGrupoRobles), function ($query) use ($rucsGrupoRobles) {
                return $query->whereNotIn('c.identificacion', $rucsGrupoRobles);
            })
            ->when($buscar !== '', function ($query) use ($buscar) {
                return $query->where(function ($subquery) use ($buscar) {
                    $texto = '%' . $buscar . '%';
                    $subquery->where('v.nro_comprobante', 'like', $texto)
                        ->orWhere('v.concepto', 'like', $texto)
                        ->orWhere('v.id_asiento', 'like', $texto)
                        ->orWhere('v.id', 'like', $texto);
                });
            })
            ->where('v.valor_contable', '>', 0)
            ->where('v.tipo', '!=', 'N-D')
            ->where('v.estado', '!=', 0)
            ->orderBy('c.nombre')
            ->get();
    }

    public static function verificarCliente($id)
    {
        return Clientes::where('identificacion', $id)->first(['identificacion']);
    }
    public static function getreporteDeudasvsPagos($r)
    {
        $sql =  'call sp_clientes_deudas_pagos(?,?,?,?)';
        $SqlCliente = '';
        $SqlCliente2 = '';
        if ($r->cliente != "") {
            $cliente = explode('|', $r->cliente);
            $SqlCliente = $cliente[0];
            $SqlCliente2 = " and dvp.identificacion = '$cliente[0]'";
        }
        /*echo '<pre>'; print_r($sql);
        print_r([session('id_empresa'),$r->d.' 00:00:00',$r->h .' 23:59:59',$SqlCliente]);
        exit();*/

        return DB::select($sql, [session('id_empresa'), $r->d . ' 00:00:00', $r->h . ' 23:59:59', $SqlCliente]);
        //dd($r->all());
        //DB::enableQueryLog();
        $id_empresa = session('id_empresa');
        $SqlCliente = '';
        $SqlCliente2 = '';
        if ($r->cliente != "") {
            $cliente = explode('|', $r->cliente);
            $SqlCliente = " and c.identificacion = '$cliente[0]'";
            $SqlCliente2 = " and dvp.identificacion = '$cliente[0]'";
        }
        //dd($SqlCliente);
        $s = "SELECT *,
        SUM(CASE WHEN tipo IN ('Factura', 'NC') THEN total_final ELSE 0 END) OVER (PARTITION BY id_factura ORDER BY orden) AS deudas,
        SUM(CASE WHEN tipo NOT IN ('Factura', 'NC') THEN total_final ELSE 0 END) OVER (PARTITION BY id_factura ORDER BY orden) AS abonos,
        SUM(CASE WHEN tipo IN ('Factura', 'ND') THEN total_final ELSE -total_final END) OVER (PARTITION BY id_factura ORDER BY orden) AS saldo
        FROM ((SELECT 0 as orden,concat('FACT #',v.nro_comprobante) as detalle,'Factura' as tipo,v.id as id_factura,v.fecha,c.identificacion,c.nombre,v.id_asiento,concat(pe.plan,'|',p.nombre) as detalle_cuenta,v.nro_comprobante,v.subtotal_0,v.subtotal_12,v.iva_total,v.total_final
        from ct_clientes c
        join ct_ventas v on c.identificacion = v.id_cliente and v.tipo != 'N-D'
        join ct_asientos_detalle ad on v.id_asiento = ad.id_asiento_cabecera 
        JOIN plan_cuentas_empresa pe ON ad.id_plan_cuenta=pe.id_plan AND pe.id_empresa= $id_empresa and pe.plan like '4.%'
        JOIN plan_cuentas p ON ad.id_plan_cuenta=p.id 
        and v.fecha BETWEEN '$r->d 00:00:00' AND '$r->h 23:59:59'
        and v.estado != 0
        and pe.plan like '4.%'
        and v.id_empresa = $id_empresa
        $SqlCliente)
        UNION ALL
        (select 1 as orden,concat('COM-IN #',ci.secuencia) as detalle,'ING' as tipo,dci.id_factura,v.fecha,c.identificacion,c.nombre,ci.id_asiento_cabecera,concat(pe.plan,'|',p.nombre) as detalle_cuenta,ci.secuencia,0,0,0,dci.total 
        from ct_detalle_comprobante_ingreso dci
        join ct_comprobante_ingreso ci on dci.id_comprobante=ci.id
        join ct_ventas v on dci.id_factura = v.id 
        join ct_clientes c on ci.id_cliente=c.identificacion
        join ct_asientos_detalle ad on v.id_asiento = ad.id_asiento_cabecera 
        JOIN plan_cuentas_empresa pe ON ad.id_plan_cuenta=pe.id_plan AND pe.id_empresa= $id_empresa and pe.plan like '1.%'
        JOIN plan_cuentas p ON ad.id_plan_cuenta=p.id 
        where v.estado != 0
        and ci.estado != 0
        $SqlCliente)
        UNION ALL
        (SELECT 2 as orden,concat('CR-V #',cv.secuencia) as detalle, 'CV' as tipo,dc.id_factura,v.fecha,c.identificacion,c.nombre,cv.id_asiento_cabecera,concat(pe.plan,'|',p.nombre) as detalle_cuenta,cv.secuencia,0,0,0,cv.total_disponible
        from ct_detalle_cruce_clientes dc
        join ct_cruce_valores_cliente cv on dc.id_comprobante = cv.id
        join ct_ventas v on dc.id_factura = v.id
        join ct_clientes c on cv.id_cliente = c.identificacion
        join ct_asientos_detalle ad on v.id_asiento = ad.id_asiento_cabecera 
        JOIN plan_cuentas_empresa pe ON ad.id_plan_cuenta=pe.id_plan AND pe.id_empresa= $id_empresa and pe.plan like '1.%'
        JOIN plan_cuentas p ON ad.id_plan_cuenta=p.id 
        where v.estado != 0
        and cv.estado != 0
        $SqlCliente)
        UNION ALL(SELECT 3 as orden,concat('CR-C #',ccc.secuencia) as detalle, 'CC' as tipo,ccc.id_factura,v.fecha,c.identificacion,c.nombre,ccc.id_asiento_cabecera,concat(pe.plan,'|',p.nombre) as detalle_cuenta,ccc.secuencia,0,0,0,ccc.total
        from ct_detalle_cruce_cuentas_clientes dcc 
        join ct_cruce_cuentas_clientes ccc on dcc.id_comprobante = ccc.id
        join ct_ventas v on ccc.id_factura = v.id
        join ct_clientes c on ccc.id_cliente =c.identificacion
        join ct_asientos_detalle ad on v.id_asiento = ad.id_asiento_cabecera 
        JOIN plan_cuentas_empresa pe ON ad.id_plan_cuenta=pe.id_plan AND pe.id_empresa= $id_empresa and pe.plan like '1.%'
        JOIN plan_cuentas p ON ad.id_plan_cuenta=p.id 
        where v.estado != 0
        and ccc.estado != 0
        $SqlCliente)
        UNION ALL(SELECT 4 as orden,concat('RET #',cr.nro_comprobante) as detalle, 'R' as tipo,cr.id_factura,v.fecha,c.identificacion,c.nombre,cr.id_asiento_cabecera,concat(pe.plan,'|',p.nombre) as detalle_cuenta,cr.secuencia,0,0,0,cr.total
        from ct_detalle_cliente_retencion dcr
        join ct_cliente_retencion cr on dcr.id_cliente_retencion = cr.id
        join ct_ventas v on cr.id_factura= v.id
        join ct_clientes c on cr.id_cliente = c.identificacion
        join ct_asientos_detalle ad on v.id_asiento = ad.id_asiento_cabecera 
        JOIN plan_cuentas_empresa pe ON ad.id_plan_cuenta=pe.id_plan AND pe.id_empresa= $id_empresa and pe.plan like '1.%'
        JOIN plan_cuentas p ON ad.id_plan_cuenta=p.id 
        where v.estado != 0
        and cr.estado != 0
        $SqlCliente)
        UNION ALL(SELECT 5 as orden,concat('NC #',nc.nro_comprobante) as detalle, 'NC' as tipo,dnc.id_factura,v.fecha,c.identificacion,c.nombre,nc.id_asiento_cabecera,concat(pe.plan,'|',p.nombre) as detalle_cuenta,nc.secuencia,0,0,0,nc.total_abonos 
        from ct_detalle_credito_clientes dnc 
        join ct_nota_credito_clientes nc on dnc.id_not_cred = nc.id
        join ct_ventas v on dnc.id_factura = v.id 
        join ct_clientes c on nc.id_cliente = c.identificacion
        join ct_asientos_detalle ad on v.id_asiento = ad.id_asiento_cabecera 
        JOIN plan_cuentas_empresa pe ON ad.id_plan_cuenta=pe.id_plan AND pe.id_empresa= $id_empresa and pe.plan like '1.%'
        JOIN plan_cuentas p ON ad.id_plan_cuenta=p.id 
        where v.estado != 0
        and nc.estado != 0
        $SqlCliente)
        union ALL (SELECT 6 as orden,concat('ND #',v.nro_comprobante) as detalle,'ND' as tipo,v.id as id_factura,v.fecha,c.identificacion,c.nombre,v.id_asiento,concat(pe.plan,'|',p.nombre) as detalle_cuenta,v.nro_comprobante,v.subtotal_0,v.subtotal_12,v.iva_total,v.total_final
        from ct_clientes c
        join ct_ventas v on c.identificacion = v.id_cliente and v.tipo = 'N-D'
        join ct_asientos_detalle ad on v.id_asiento = ad.id_asiento_cabecera 
        JOIN plan_cuentas_empresa pe ON ad.id_plan_cuenta=pe.id_plan AND pe.id_empresa= $id_empresa and pe.plan like '1.%'
        JOIN plan_cuentas p ON ad.id_plan_cuenta=p.id 
        and v.fecha BETWEEN '$r->d 00:00:00' AND '$r->h 23:59:59'
        and v.estado != 0
        and v.id_empresa = $id_empresa
        $SqlCliente)
        ) as dvp where dvp.fecha BETWEEN '$r->d 00:00:00' AND '$r->h 23:59:59' $SqlCliente2 order by dvp.id_factura,dvp.orden;
        ";


        //echo "<pre>";
        //print_r($sql);exit(); 
        return $sql;
    }

    public static function getClienteCombo($term)
    {
        return Clientes::selectRaw('CONCAT(identificacion,"|",IFNULL(telefono1_representante,""),"|",IFNULL(telefono2_representante,""),"|",IFNULL(direccion_representante,""),"|",IFNULL(email_representante,""), "|",IFNULL(nombre,"")) as id, CONCAT(identificacion," ",nombre) as text')
            ->where(function ($q) use ($term) {
                $q->orWhere('nombre', 'like', '%' . $term . '%');
                $q->orWhere('identificacion', 'like', '%' . $term . '%');
            })->get();
    }
    public static function getClientes($id = '', $r)
    {
        if ($id == '') {
            $columns = ['identificacion', 'nombre', 'nombre_representante', 'cedula_representante', 'email_representante', 'direccion', 'pais'];
            $query = self::query();
            if ($r->input('search.value')) {
                $search = $r->input('search.value');
                $query->where(function ($q) use ($search, $columns) {
                    foreach ($columns as $column) {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                });
            }
            $totalFiltered = $query->count();
            if ($r->input('order.0.column') !== null) {
                $orderColumn = $columns[$r->input('order.0.column')];
                $orderDirection = $r->input('order.0.dir');
                $query->orderBy($orderColumn, $orderDirection);
            }
            $start = $r->input('start');
            $length = $r->input('length');
            $data = $query->offset($start)->limit($length)->get();
            return response()->json([
                'draw' => $r->input('draw'),
                'recordsTotal' => self::count(),
                'recordsFiltered' => $totalFiltered,
                'data' => $data
            ]);
        } else {
            return Clientes::leftjoin('new_db_ciudades as c', 'c.id_ciudad', 'ct_clientes.id_ciudad')
                ->leftjoin('new_db_tipo_identificacion as ti', 'ct_clientes.tipo', 'ti.cod_sri_tipo_identificacion')
                ->where('identificacion', $id)
                ->first([
                    'ct_clientes.identificacion',
                    'ct_clientes.nombre',
                    'ct_clientes.estado',
                    'ct_clientes.tipo',
                    'ct_clientes.clase',
                    'ct_clientes.direccion',
                    'ct_clientes.direccion_representante',
                    'ct_clientes.nombre_representante',
                    'ct_clientes.cedula_representante',
                    'ct_clientes.email_representante',
                    'ct_clientes.telefono1_representante',
                    'ct_clientes.telefono2_representante',
                    'ct_clientes.pais_representante',
                    'ct_clientes.provincia_representante',
                    'ct_clientes.ciudad_representante',
                    'c.id_pais_ciudad as pais',
                    'c.id_provincia_ciudad as provincia',
                    'ct_clientes.id_ciudad',
                    'ct_clientes.urbanizacion',
                    'ct_clientes.etapa',
                    'ct_clientes.manzana',
                    'ct_clientes.villa',
                    'ct_clientes.referencia',
                ]);
        }
    }

    public static function saveCliente($r)
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
            $cli = Clientes::where('identificacion', $data['identificacion'])->first();
            $pais = Paises::find($data['pais']);
            if ($cli == '') {
                $arregloSave = [
                    'identificacion' => $data["identificacion"],
                    'nombre' => $data["nombre"],
                    'tipo' => $data["tipo_identificacion"],
                    'clase' => $data["clase"],
                    'pais' => isset($pais->nombre) ? ucwords(strtolower($pais->nombre)) : null,
                    'estado' =>  $data["estado"],
                    'nombre_representante' => $data["nombre_representante"],
                    'cedula_representante' => $data["cedula"],
                    'email_representante' => $data["email_representante"],
                    'direccion' => $data["direccion"],
                    'direccion_representante' => $data["direccion"],
                    'id_ciudad' => $data["ciudad"],
                    'telefono1_representante' => $data['telefono1_representante'],
                    'telefono2_representante' => $data['telefono2_representante'],
                    'urbanizacion' => $data["urbanizacion"],
                    'etapa' => $data["etapa"],
                    'manzana' => $data["manzana"],
                    'villa' => $data["villa"],
                    'referencia' => $data["referencia"],
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $idCliente = Clientes::insert($arregloSave);
            } else {
                $arregloSave = [
                    'nombre' => $data["nombre"],
                    'tipo' => $data["tipo_identificacion"],
                    'clase' => $data["clase"],
                    'pais' => isset($pais->nombre) ? ucwords(strtolower($pais->nombre)) : null,
                    'estado' =>  $data["estado"],
                    'nombre_representante' => $data["nombre_representante"],
                    'cedula_representante' => $data["cedula"],
                    'email_representante' => $data["email_representante"],
                    'direccion_representante' => $data["direccion"],
                    'id_ciudad' => $data["ciudad"],
                    'telefono1_representante' => $data['telefono1_representante'],
                    'telefono2_representante' => $data['telefono2_representante'],
                    'id_ciudad' => $data["ciudad"],
                    'urbanizacion' => $data["urbanizacion"],
                    'etapa' => $data["etapa"],
                    'manzana' => $data["manzana"],
                    'villa' => $data["villa"],
                    'referencia' => $data["referencia"],
                    'id_usuariomod' => Auth::user()->id,
                    'ip_modificacion' => $ip,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                Clientes::where('identificacion', $data['identificacion'])->update($arregloSave);
            }
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
            $r->c = 'Clientes';
            $r->s = 'saveClientes';
            $r->d =  $origin['d'];
            $r->m = Clientes::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            if (isset($data['viene'])) {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro guardado correctamente',
                    'data' => $r->input(),
                    'reload' => 'v3/ventas/facturar/facturaventa?i=' . $data['identificacion'],
                    'viene' => $data['viene']
                ];
            } else {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro guardado correctamente',
                    'data' => $r->input(),
                    'reload' => '',
                ];
            }
            //DB::rollBack();
            return json_encode($arrayRespuesta);
        }
    }

    public static function estadoCliente($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            Clientes::where('identificacion', $r->id)->update(['estado' => $estado]);
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro actualizado correctamente',
                'data' => $r->input(),
            ];
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
    public static function retenciones($r)
    {
        $data =   ClientesRetenciones::leftjoin('ct_clientes as c', 'c.identificacion', 'ct_cliente_retencion.id_cliente')
            ->join('ct_ventas as v', 'v.id', 'ct_cliente_retencion.id_factura')
            ->leftJoin("users as usuacr", "usuacr.id", "ct_cliente_retencion.id_usuariocrea")
            ->where('ct_cliente_retencion.id_empresa', session('id_empresa'))
            ->orderby('ct_cliente_retencion.id', 'desc');
        if (!empty($r->d)) {
            $data = $data->where('ct_cliente_retencion.fecha', '>=', $r->d);
        }
        if (!empty($r->h)) {
            $data = $data->where('ct_cliente_retencion.fecha', '<=', $r->h);
        }
        $id_cliente = explode('|', $r->id_cliente);
        if (!empty($r->id_cliente)) {
            $data = $data->where('c.identificacion', $id_cliente[0]);
        }
        DB::enableQueryLog();
        $data = $data->orderBy('ct_cliente_retencion.fecha', 'ASC')->get([
            'ct_cliente_retencion.*',
            'ct_cliente_retencion.created_at',
            'v.nro_comprobante as n',
            'ct_cliente_retencion.fecha',
            'ct_cliente_retencion.descripcion',
            'ct_cliente_retencion.secuencia',
            'ct_cliente_retencion.valor_fuente',
            'c.nombre',
            'ct_cliente_retencion.valor_iva',
            'ct_cliente_retencion.estado',
            DB::raw('CONCAT(usuacr.nombre1, " ", usuacr.apellido1) as nombre_crea'),
        ]);
        $data->each(function ($item) {
            $item->total = $item->valor_fuente + $item->valor_iva;
        });
        return $data;
        /*$consultaOriginal = DB::table('ct_cliente_retencion as ct_cliente_retencion_original')
            ->select(
                'ct_cliente_retencion_original.fecha',
                'c.nombre',
                'ct_cliente_retencion_original.descripcion',
                'ct_cliente_retencion_original.created_at',
                'v.nro_comprobante AS n',
                'ct_cliente_retencion_original.secuencia',
                'ct_cliente_retencion_original.valor_fuente',
                'ct_cliente_retencion_original.valor_iva',
                'ct_cliente_retencion_original.estado',
                DB::raw('CONCAT(usuacr.nombre1, " ", usuacr.apellido1) AS nombre_crea')
            )
            ->leftJoin('ct_clientes as c', 'c.identificacion', '=', 'ct_cliente_retencion_original.id_cliente')
            ->join('ct_ventas as v', 'v.id', '=', 'ct_cliente_retencion_original.id_factura')
            ->leftJoin('users as usuacr', 'usuacr.id', '=', 'ct_cliente_retencion_original.id_usuariocrea')
            ->where('ct_cliente_retencion_original.fecha', '>=', '2024-05-01 00:00:00')
            ->where('ct_cliente_retencion_original.fecha', '<=', '2024-05-31 23:59:59')
            ->groupBy('ct_cliente_retencion_original.id_cliente', 'ct_cliente_retencion_original.id')
            ->orderByDesc('ct_cliente_retencion_original.id')
            ->orderBy('ct_cliente_retencion_original.fecha', 'asc');

        $totales = DB::table('ct_cliente_retencion')
            ->select(
                DB::raw('SUM(valor_fuente) AS total_valor_fuente'),
                DB::raw('SUM(valor_iva) AS total_valor_iva'),
                DB::raw('SUM(valor_fuente + valor_iva) AS total_suma')
            )
            ->where('fecha', '>=', '2024-05-01 00:00:00')
            ->where('fecha', '<=', '2024-05-31 23:59:59')
            ->first();

        $resultado = DB::table(DB::raw("({$consultaOriginal->toSql()}) as consulta_original"))
            ->mergeBindings($consultaOriginal->getQuery())
            ->join(DB::raw("({$totales->toSql()}) as totales"), function ($join) {
                $join->on(DB::raw(1), '=', DB::raw(1));
            })
            ->select('consulta_original.*', 'totales.total_valor_fuente', 'totales.total_valor_iva', 'totales.total_suma')
            ->get();
        return $resultado;*/
    }

    public static function nota_credito($r)
    {
        $data = NotaCreditoClientes::select(
            'ct_nota_credito_clientes.nro_comprobante',
            'ct_nota_credito_clientes.id',
            'id_asiento_cabecera',
            'ct_clientes.nombre as nombre_cliente',
            'ct_nota_credito_clientes.fecha',
            'ct_nota_credito_clientes.tipo',
            'ct_nota_credito_clientes.concepto',
            'ct_nota_credito_clientes.total_credito',
            'ct_nota_credito_clientes.total_deudas',
            'ct_nota_credito_clientes.total_abonos',
            DB::raw('SUM(ct_nota_credito_clientes.total_credito) as total_credito'),
            DB::raw('SUM(ct_nota_credito_clientes.total_deudas) as total_deudas'),
            DB::raw('SUM(ct_nota_credito_clientes.total_abonos) as total_abonos'),
            DB::raw('CONCAT(users.nombre1, " ", users.apellido1) as nombre_crea'),
            'ct_nota_credito_clientes.estado',
            'ct_nota_credito_clientes.electronica'
        )
            ->join("ct_clientes", "ct_clientes.identificacion", "ct_nota_credito_clientes.id_cliente")
            ->join("users", "users.id", "ct_nota_credito_clientes.id_usuariocrea")
            ->where('ct_nota_credito_clientes.id_empresa', session('id_empresa'))
            ->where(function ($q) use ($r) {
                if ($r->id_cliente != '') {
                    $id_cliente = explode('|', $r->id_cliente);
                    $q->where("ct_nota_credito_clientes.id_cliente", $id_cliente[0]);
                }
                if ($r->d != null && $r->h != null) {
                    $q->whereBetween("ct_nota_credito_clientes.created_at", [
                        $r->d . ' 00:00:00',
                        $r->h . ' 23:59:59'
                    ]);
                }
            })
            ->orderBy('ct_nota_credito_clientes.id', 'desc')
            ->get();
        if (isset($data[0]->nro_comprobante))
            return $data;
        else
            return [];
    }

    public static function getnombrecliente($r)
    {
        $clientes = [];
        $data = self::when($r, function ($query) use ($r) {
            $query->where('identificacion', 'like', '%' . $r . '%')
                ->orWhere('nombre', 'like', '%' . $r . '%');
        })->get();
        foreach ($data as $value) {
            $clientes[] = ['id' => $value->identificacion, 'nombre' => $value->nombre . ' | ' . $value->identificacion];
        }

        return response()->json($clientes);
    }


    public static function getComprobanteCliente($r)
    {
        $nombreCliente = trim(explode("|", $r->nombreCliente)[0]);
        $productos  = [];
        $productos = Ventas::when($nombreCliente, function ($q) use ($nombreCliente) {
            $q->where(function ($query) use ($nombreCliente) {
                $query->where('nombre_cliente', 'LIKE', "%{$nombreCliente}%");
            })
                ->where('id_empresa', session("id_empresa"))
                ->select('ct_ventas.id as id', DB::raw('CONCAT(nombre_cliente, " | ", nro_comprobante) as text'))
                ->where('estado', '<>', 0)
                ->where('valor_contable', '>', 0);
        })->get();

        return response()->json($productos);
    }

    public static function newData2Cliente($r)
    {

        $venta = Ventas::find($r->idFactura);
        $table = "";
        $total = 0;
        $contLinea = 0;
        foreach ($venta->detalles as $key => $value) {
            $cantidad = 0;
            $codigoProduto = "";
            $validate = DevolucionProductos::where('id_factura', $venta->id)->where('codigo', $value->id_ct_productos)->where('estado', 1)
                ->groupBy('codigo')->select(DB::raw('COUNT(cantidad) as cantidad'), 'codigo')->first();
            if (!is_null($validate)) {
                $cantidad = $validate->cantidad;
                $codigoProduto = $validate->codigo;
            }
            for ($i = 0; $i < $value->cantidad; $i++) {
                $iva_producto = 0;
                $iva_porcentaje = 0;
                $checked = "";
                if ($value->check_iva == 1) {
                    $iva_producto = ($value->extendido / $value->cantidad) * $value->porcentaje;
                    $iva_porcentaje = $value->porcentaje;
                    $checked = 'checked';
                }
                $totalProducto = (($value->extendido / $value->cantidad) + $iva_producto);
                $precio = $value->precio;
                $descuento = $value->descuento / $value->cantidad;
                $table .= "<tr >
                                    <td> 
                                        <input type='text' class='form-control' value='{$value->id_ct_productos}' readonly/>
                                        <input class='fe' type='text' name='id[]' value='{$value->id}'> 
                                        <input type='hidden' name='codigo[]' value='{$value->id_ct_productos}'>
                                        <input class='cantidades cantidad' type='hidden' name='cantidad[]' value='1'>
                                        <textarea class='form-control  ocultosp' id='ocultosp{$key}' style='margin-top:5px' name='descripcion[]' placeholder='Ingrese descripcion' ></textarea> 
                                        <input type='hidden' name='nombre[]' value='{$value->nombre}'> 
                                        <input type='hidden' class='iva_producto' id='iva_producto$contLinea' name='iva_producto[]' value='{$iva_producto}'>
                                        <input type='hidden' id='porct_iva_$contLinea' class='iva_porct' name='iva_porct[]' value='{$iva_porcentaje}'>
                                        <input type='hidden' id='total_producto$contLinea' class='totalProducto' name='totalProducto[]' value='{$totalProducto}'>
                                        <input type='hidden'  class='descuento' name='descuento[]' value='{$descuento}'>
                                    </td>
                                    <td> <input type='text' class='form-control' value='1' readonly /></td>
                                    <td><input class='form-control' type='text' value='{$value->nombre}' readonly/></td>
                                    <td><input class='precios precioProducto form-control' id='precio_$contLinea' onchange='calcIva($contLinea)' type='text' name='precio[]' value='{$precio}'></td>
                                    <td><input type='checkbox' disabled {$checked} value='0'> </td>
                                    <td>
                                        <input class='verificar' id='verificar{$key}' onclick='adder(this); sumaGlobal();' type='checkbox' name='verificarx[]' checked value='0'> 
                                        <input class='vercheckbox checkProducto' type='hidden' name='verificar[]' value='1'> 
                                    </td>
                            </tr>";

                $total += $totalProducto;
                $contLinea++;
            }
        }
        return ['table' => $table, 'total' => $venta->total_final, "saldo" => $total, "valor_contable" => $venta->valor_contable];
    }

    private static function generarNumeroDocumento($datos)
    {
        $numero = '';
        $num = InfoTributaria::where('id_empresa', session('id_empresa'))
            ->where('id_maestro_documentos', $datos['tipo'])
            ->where('cod_sucursal', $datos['sucursal'])
            ->where('cod_caja', $datos['punto_emision'])
            ->first([
                'cod_sucursal',
                'cod_caja',
                'secuencial_nro'
            ]);
        if ($num != '') {
            $numero = $num->secuencial_nro + 1;
            $numero = $num->cod_sucursal . '-' . $num->cod_caja . '-' . str_pad($numero, 9, 0, STR_PAD_LEFT);
        }
        return $numero;
    }

    public static function obtener_numero_factura($idempresa, $sucursal, $punto_emision)
    {
        $contador_ntc = NotaCreditoClientes::where('id_empresa', $idempresa)
            ->where('sucursal', $sucursal)
            ->where('punto_emision', $punto_emision)->get()->count();

        if ($contador_ntc == 0) {

            $num            = '1';
            $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            return $numero_factura;
        } else {

            $max_id = NotaCreditoClientes::where('id_empresa', $idempresa)->where('sucursal', $sucursal)
                ->where('punto_emision', $punto_emision)->latest()->first();
            $max_id = intval($max_id->secuencia);

            if (($max_id >= 1) && ($max_id < 10)) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                return $numero_factura;
            }

            if (($max_id >= 10) && ($max_id < 100)) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                return $numero_factura;
            }

            if (($max_id >= 100) && ($max_id < 1000)) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                return $numero_factura;
            }

            if ($max_id == 1000) {
                $numero_factura = $max_id;
                return $numero_factura;
            }
        }
    }

    public static function guardarNotaCreditoCliente($r)
    {

        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            //dd($data);
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            $idusuario      = Auth::user()->id;
            $id_empresa     = session("id_empresa");
            $venta  = Ventas::find($data['facturas']);
            if (!is_null($venta)) {
                if ($venta->valor_contable < $data['total1']) {
                    throw new Exception("El valor contable de la factura es menor al valor de la N/C");
                }
            } else {
                throw new Exception("No se encontro la factura");
            }
            $sucursal = Sucursales::where('id', $data['sucursal'])->first();
            $punto_emision = Caja::where('id', $data['punto_emision'])->first();
            $datos = [
                'sucursal' => $sucursal->codigo_sucursal, //1
                'punto_emision' => $punto_emision->codigo_caja, //100
                'tipo' => 1
            ];
            $num_comprobante = self::generarNumeroDocumento($datos);
            if ($num_comprobante == '') {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'La empresa no tiene configurada la información tributaria',
                    'footer' => '<a href="javascript:;" onclick="crearInfoTributaria();">¿Desea crear está información?</a>',
                    'reload' => ''
                ];
                DB::rollBack();
                return json_encode($arrayRespuesta);
            }
            // asientos cabecera
            $arrayP = [];
            $ids = [];
            for ($i = 0; $i < count($data['precio']); $i++) {
                if ($data['verificar'][$i] == 1) {
                    // Busca en el arreglo arrayP si existe
                    $indexB = array_search($data['codigo'][$i], array_column($arrayP, 'codigo'));
                    try {
                        if (is_numeric($indexB)) {
                            $arrayP[$indexB]['precio'] = $arrayP[$indexB]['precio'] + $data["precio"][$i];
                            $arrayP[$indexB]['cantidad'] = $arrayP[$indexB]['cantidad'] + $data["cantidad"][$i];
                            $ids[$indexB]['id'] = $data[$indexB]['id'];
                        } else {
                            $arrayP[] = [
                                'codigo' => $data['codigo'][$i],
                                'precio' => $data['precio'][$i],
                                'cantidad' => $data['cantidad'][$i],
                            ];
                            array_push($ids, $data['id'][$i]);
                        }
                    } catch (Exception $e) {
                        array_push($ids, $data['id'][$i]);
                    }
                }
            }
            $ventauP = DetalleVenta::where('id_ct_ventas', $venta->id)->whereIn('id', $ids)->get();
            foreach ($ventauP as $key => $value) {
                if (isset($arrayP[$key])) {
                    $actualizar = DetalleVenta::where('id', $value->id)->first();
                    $actualizar->valor_contable = $value->valor_contable - $arrayP[$key]['precio'];
                    $actualizar->save();
                }
            }

            $nfactura   = self::obtener_numero_factura($id_empresa, $sucursal->codigo_sucursal, $punto_emision->codigo_caja);

            $arrayAsientoCabecera = [
                'sucursal' => $sucursal->codigo_sucursal,
                'punto_emision' => $punto_emision->codigo_caja,
                'fecha_asiento' => $r->fecha,
                'fact_numero' => $num_comprobante,
                'id_empresa' => session('id_empresa'),
                'observacion' => $r->concepto,
                'valor' => $r->total1,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ];
            $id_asiento_cabecera = Contabilidad::crearAsiento('cliente', 'notacreditoclientes', $arrayAsientoCabecera,  $r);
            //dd(AsientosCabecera::find($id_asiento_cabecera),AsientosDetalles::where("id_asiento_cabecera",$id_asiento_cabecera)->get());
            $input               = [
                'id_empresa'          => $id_empresa,
                'id_cliente'          => $venta->id_cliente,
                'check_sri'           => isset($data['sri_v']) ? 1 : 0,
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'sucursal'            => $sucursal->codigo_sucursal,
                'punto_emision'       => $punto_emision->codigo_caja,
                'numero_factura'      => $venta->sucursal . '-' . $venta->punto_emision . '-' . $venta->numero,
                'id_factura'          => $venta->id,
                'nro_comprobante'     => $num_comprobante,
                'secuencia'           => str_pad($nfactura, 9, "0", STR_PAD_LEFT),
                'fecha'               => $data['fecha'],
                'tipo'                => "CLI-NC",
                'concepto'            => $data['concepto'],
                'subtotal'            => $data['subtotal1'],
                'descuento'           => $data['descuento1'],
                'impuesto'            => $data['impuesto1'],
                'sub_sin_imp'         => $data['subtotal1'],
                'subtotal0'           => $data['subtotal01'],
                'tar_iva_12'          => $data['impuesto1'],
                'subtotal'            => $data['subtotal1'],
                'subtotal12'          => $data['subtotal121'], //bien
                'total_credito'       => $data['total1'],
                'total_deudas'        => $data['total1'],
                'total_abonos'        => $data['total1'],
                'total_nuevo_saldo'   => $data['total1'],
                'observacion'         => $data['observaciones'],
                'electronica'         => $punto_emision->codigo_caja == 100 ? 1 : $punto_emision->codigo_caja['electronica'],
                'doc_electronico'     => '0',
                'estado'              => '1',
                'tipo_nota'           => '2',
                'ip_creacion'         => $ip,
                'ip_modificacion'     => $ip,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];

            $id_credito = NotaCreditoClientes::insertGetId($input);


            if (isset($data->codigo)) {
                $ventas = Ventas::where('id', $data['facturas'])->first();
                for ($j = 0; $j < count($data['verificar']); $j++) {
                    if ($data['verificar'][$j] == 1) {
                        $detalle = Ct_Devolucion_Productos::where('id_factura', $ventas->id)->where('codigo', $r['codigo'][$j])->where('estado', 1)->first();
                        if (is_null($detalle)) {
                            DevolucionProductos::insert([
                                'id_nota_credito' => $id_credito,
                                'descripcion'     => $r['descripcion'][$j],
                                'id_factura'      => $venta->id_ct_ventas,
                                'codigo'          => $r['codigo'][$j],
                                'cantidad'        => 1,
                                'precio'          => $r['precio'][$j],
                                'valor_iva'       => $r["iva_producto"][$j],
                                'iva'             => $r["iva_producto"][$j] > 0 ? 1 : 0,
                                'descuento'       => $r['descuento'][$j],
                                'total'           => $r['totalProducto'][$j],
                                'estado'          => $r['verificar'][$j],
                                'nombre'          => $r['nombre'][$j],
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ]);
                        } else {
                            if ($r['totalProducto'][$j] <= $detalle->totalProducto) {
                                Ct_Devolucion_Productos::create([
                                    'id_nota_credito' => $id_credito,
                                    'descripcion'     => $r['descripcion'][$j],
                                    'id_factura'      => $venta->id_ct_ventas,
                                    'codigo'          => $r['codigo'][$j],
                                    'cantidad'        => 1,
                                    'precio'          => $r['precio'][$j],
                                    'valor_iva'       => $r["iva_producto"][$j],
                                    'iva'             => $r["iva_producto"][$j] > 0 ? 1 : 0,
                                    'descuento'       => $r['descuento'][$j],
                                    'total'           => $r['totalProducto'][$j],
                                    'estado'          => $r['verificar'][$j],
                                    'nombre'          => $r['nombre'][$j],
                                    'id_usuariocrea'  => $idusuario,
                                    'id_usuariomod'   => $idusuario,
                                ]);
                            }
                        }
                    }
                }
            }
            $cab_venta = Ventas::find($venta->id);
            $valor_contable = $cab_venta->valor_contable - $r['total1'];
            if ($valor_contable < 0) {
                throw new Exception("El total de la nota de credito no debe ser mayor al saldo de la factura");
            }

            $cab_venta->valor_contable = $cab_venta->valor_contable - $r['total1'];
            $cab_venta->save();

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
            $r->c = 'Clientes';
            $r->s = 'saveNotaDebitoCliente';
            $r->d =  $origin['d'];
            $r->m = Clientes::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            if (isset($data['viene'])) {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro guardado correctamente',
                    'data' => $r->input(),
                    'reload' => 'v3/clientes/notadecredito',
                ];
            } else {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro guardado correctamente',
                    'data' => $r->input(),
                    'reload' => '',
                ];
            }
            //DB::rollBack();
            return json_encode($arrayRespuesta);
        }
    }


    public static function getCuentascobradas(Request $request)
    {
        $empresa = trim((string) $request->input('id_empresa'));
        $esCrm = preg_replace('/\D+/', '', $empresa) === '1307189140001';
        $reglas = [
            'id_empresa' => 'required',
            'fecha_desde' => 'required|date_format:Y-m-d' . ($esCrm ? '|after_or_equal:2024-01-01' : ''),
            'fecha_hasta' => 'required|date_format:Y-m-d|after_or_equal:fecha_desde',
        ];
        $validator = Validator::make($request->all(), $reglas, [
            'fecha_desde.after_or_equal' => 'Para CRM solo se permiten consultas desde el 1 de enero de 2024.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'msj' => $validator->errors()->first(),
            ], 422);
        }

        $desde = $request->input('fecha_desde');
        $hasta = Carbon::parse($request->input('fecha_hasta'))
            ->addDay()->toDateString();
        $cliente = $request->input('cliente');
        $tipoSeguro = strtolower(trim((string) $request->input('tipo_seguro', '')));
        $seguros = array_values(array_unique(array_filter(array_map(function ($seguro) {
            return is_scalar($seguro) ? trim((string) $seguro) : '';
        }, (array) $request->input('seguros', [])), function ($seguro) {
            return $seguro !== '';
        })));
        $excluirGrupoRobles = filter_var($request->input('excluir_grupo_robles', false), FILTER_VALIDATE_BOOLEAN);
        $rucsGrupoRobles = array_values(array_unique(array_filter(array_map(function ($ruc) {
            return is_scalar($ruc) ? trim((string) $ruc) : '';
        }, (array) $request->input('rucs_grupo_robles', [])), function ($ruc) {
            return $ruc !== '';
        })));
        $clientesPublicos = [
            '1760004650001', // IESS
            '1760001120001', // MSP
            '1768061090001', // ISSPOL
            '1768022190001', // ISSFA
        ];
        $clientesPermitidosCrm = [
            '0968594650001', // MSP
            '1760004650001', // IESS
            '1791257049001', // SALUD
        ];
        $filtrarPorAseguradora = count($seguros) > 0;

        $query = DB::table('ct_comprobante_ingreso as i')
            ->leftJoin('ct_clientes as c', 'i.id_cliente', '=', 'c.identificacion');

        if ($filtrarPorAseguradora) {
            $query->join('ct_detalle_comprobante_ingreso as di', 'di.id_comprobante', '=', 'i.id')
                ->join('ct_ventas as v', 'v.id', '=', 'di.id_factura')
                ->join('seguros as s', 's.id', '=', 'v.seguro_paciente')
                ->select(
                    'i.id',
                    'i.fecha',
                    'i.id_cliente',
                    'c.nombre as cliente',
                    DB::raw('SUM(di.total) as total'),
                    DB::raw("GROUP_CONCAT(DISTINCT s.nombre ORDER BY s.nombre SEPARATOR ', ') as nombre_seguro")
                )
                ->where('v.estado', '!=', 0)
                ->whereIn('s.id', $seguros)
                ->groupBy('i.id', 'i.fecha', 'i.id_cliente', 'c.nombre');
        } else {
            $query->select(
                'i.id',
                'i.fecha',
                'i.id_cliente',
                'c.nombre as cliente',
                'i.total_ingreso as total'
            );
        }

        $query
            ->where('i.id_empresa', $empresa)
            ->where('i.estado', 1)
            ->where('i.fecha', '>=', $desde)
            ->where('i.fecha', '<', $hasta)
            ->whereRaw("
            NOT (
                COALESCE(i.tipo, 0) = 2
                AND EXISTS (
                    SELECT 1
                    FROM ct_comprobante_ingreso original
                    WHERE original.id_asiento_cabecera = i.id_asiento_cabecera
                      AND original.id <> i.id
                      AND original.tipo <> 2
                      AND original.estado = 1
                )
            )
        ");

        if ($esCrm) {
            $query->whereIn('i.id_cliente', $clientesPermitidosCrm);
        }

        if ($tipoSeguro === 'publico') {
            $query->whereIn('i.id_cliente', $clientesPublicos);
        } elseif ($tipoSeguro === 'privado') {
            $query->where(function ($subquery) use ($clientesPublicos) {
                $subquery->whereNull('i.id_cliente')
                    ->orWhereNotIn('i.id_cliente', $clientesPublicos);
            });
        }

        if ($cliente !== null && $cliente !== '') {
            $query->where('i.id_cliente', $cliente);
        }

        if ($excluirGrupoRobles && !empty($rucsGrupoRobles)) {
            $query->where(function ($subquery) use ($rucsGrupoRobles) {
                $subquery->whereNull('i.id_cliente')
                    ->orWhereNotIn('i.id_cliente', $rucsGrupoRobles);
            });
        }

        $ingresos = $query->get();

        $varios = collect();
        if (!$esCrm && ($cliente === null || $cliente === '') && !$filtrarPorAseguradora && $tipoSeguro !== 'publico') {
            $varios = DB::table('ct_comprobante_ingreso_varios')
                ->select(
                    'id',
                    'fecha',
                    DB::raw('NULL as id_cliente'),
                    DB::raw('NULL as cliente'),
                    'total_ingreso as total'
                )
                ->where('id_empresa', $empresa)
                ->where('estado', 1)
                ->where('fecha', '>=', $desde)
                ->where('fecha', '<', $hasta)
                ->get();
        }

        $registros = $ingresos->merge($varios)->sortByDesc('fecha')->values();

        return response()->json([
            'total_ingresado' => round($registros->sum('total'), 2),
            'registros' => $registros,
        ]);
    }
}
