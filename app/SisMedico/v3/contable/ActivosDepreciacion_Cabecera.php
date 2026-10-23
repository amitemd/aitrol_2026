<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class ActivosDepreciacion_Cabecera extends Model
{
    private static $modelo = 'ActivosDepreciacion_Cabecera';
    protected $table = 'af_depreciacion_cabecera';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getDepreciaciones($id = '')
    {
        return ActivosDepreciacion_Cabecera::select(
            'af_depreciacion_cabecera.id',
            'd.activo_id',
            'a.codigo',
            'a.nombre',
            'a.tasa',
            'a.vida_util',
            'af_depreciacion_cabecera.id_empresa',
            DB::raw('SUM(IFNULL(d.valordepreciacion,0)) as total_depreciado'),
            DB::raw('SUM(IFNULL(d.valordepreciacion,0)) + IFNULL(a.depreciacion_acum,0) as acumulado'),
            'a.depreciacion_acum as saldo_inicial',
            'a.costo',
            DB::raw('(IFNULL(a.costo,0) - IFNULL(a.depreciacion_acum,0) - SUM(IFNULL(d.valordepreciacion,0))) as valor_residual')
        )
            ->join('af_depreciacion_detalle as d', 'af_depreciacion_cabecera.id', '=', 'd.depreciacion_cabecera_id')
            ->join('af_activo as a', 'd.activo_id', '=', 'a.id')
            ->where('af_depreciacion_cabecera.estado', 1)
            ->where('af_depreciacion_cabecera.id_empresa', session('id_empresa'))
            ->groupBy('d.activo_id')
            ->get();
    }

    public static function getHistorialDepreciaciones()
    {
        return ActivosDepreciacion_Cabecera::where('id_empresa', session('id_empresa'))->get();
    }

    public static function getActivosDepreciacion($r)
    {

        $tipo = $r->tipo;
        $periodo = $r->periodo;
        $periodo = $periodo . '-' . date('t', strtotime($periodo));
        return ActivosFijos::select(
            'af_activo.fecha_compra',
            'af_activo.id as id_activo',
            'af_activo.codigo',
            'af_activo.nombre',
            't.nombre as tipo',
            'af_activo.costo',
            'af_activo.tasa',
            'af_activo.vida_util',
            DB::raw('IF(IFNULL((select dda.activo_id from af_depreciacion_cabecera cca join af_depreciacion_detalle dda on cca.id=dda.depreciacion_cabecera_id where cca.estado!=0 and cca.anio=' . date('Y', strtotime($periodo)) . ' and cca.mes=' . date('m', strtotime($periodo)) . ' and dda.activo_id=`af_activo`.id ),0)=0,"NO","SI") as dep'),
            DB::raw("IF(af_activo.factura IS NULL, 'SI', 'FC') AS documento"),
            DB::raw("IF(DATEDIFF('$periodo', af_activo.fecha_compra) >= 30, 30, DATEDIFF('$periodo', af_activo.fecha_compra)) AS dias_transcurridos"),
            DB::raw("IFNULL((SELECT SUM(IFNULL(d.valordepreciacion,0)) + IFNULL(ac.depreciacion_acum,0) FROM af_depreciacion_cabecera c JOIN af_depreciacion_detalle d ON c.id = d.depreciacion_cabecera_id JOIN af_activo ac ON d.activo_id = ac.id WHERE c.estado = 1 AND ac.id = af_activo.id),0) AS acumulado"),
            DB::raw("if(IFNULL((SELECT (IFNULL(ac1.costo,0) - IFNULL(ac1.depreciacion_acum,0) - SUM(IFNULL(d.valordepreciacion,0))) FROM af_depreciacion_cabecera c JOIN af_depreciacion_detalle d ON c.id = d.depreciacion_cabecera_id JOIN af_activo ac1 ON d.activo_id = ac1.id WHERE c.estado = 1 AND ac1.id = af_activo.id),0) = 0,  af_activo.costo, IFNULL((SELECT (IFNULL(ac1.costo,0) - IFNULL(ac1.depreciacion_acum,0) - SUM(IFNULL(d.valordepreciacion,0))) FROM af_depreciacion_cabecera c JOIN af_depreciacion_detalle d ON c.id = d.depreciacion_cabecera_id JOIN af_activo ac1 ON d.activo_id = ac1.id WHERE c.estado = 1 AND ac1.id = af_activo.id),0) ) AS saldo_actual"),
            DB::raw("(((af_activo.costo*(af_activo.tasa/100))/360)*IF(DATEDIFF('$periodo', af_activo.fecha_compra) >= 30, 30, DATEDIFF('$periodo', af_activo.fecha_compra))) AS valor_dep_mes"),
            DB::raw("IF(IFNULL((SELECT (IFNULL(ac1.costo,0) - IFNULL(ac1.depreciacion_acum,0) - SUM(IFNULL(d.valordepreciacion,0))) FROM af_depreciacion_cabecera c JOIN af_depreciacion_detalle d ON c.id = d.depreciacion_cabecera_id JOIN af_activo ac1 ON d.activo_id = ac1.id WHERE c.estado = 1 AND ac1.id = af_activo.id) - ROUND((((af_activo.costo*(af_activo.tasa/100))/360)*IF(DATEDIFF('$periodo', af_activo.fecha_compra) >= 30, 30, DATEDIFF('$periodo', af_activo.fecha_compra))), 2),0) =0, af_activo.costo - (ROUND((((af_activo.costo*(af_activo.tasa/100))/360)*IF(DATEDIFF('$periodo', af_activo.fecha_compra) >= 30, 30, DATEDIFF('$periodo', af_activo.fecha_compra))), 2)), IFNULL((SELECT (IFNULL(ac1.costo,0) - IFNULL(ac1.depreciacion_acum,0) - SUM(IFNULL(d.valordepreciacion,0))) FROM af_depreciacion_cabecera c JOIN af_depreciacion_detalle d ON c.id = d.depreciacion_cabecera_id JOIN af_activo ac1 ON d.activo_id = ac1.id WHERE c.estado = 1 AND ac1.id = af_activo.id) - ROUND((((af_activo.costo*(af_activo.tasa/100))/360)*IF(DATEDIFF('$periodo', af_activo.fecha_compra) >= 30, 30, DATEDIFF('$periodo', af_activo.fecha_compra))), 2),0)) AS saldo_despues")
        )
            ->join('af_tipo as t', 'af_activo.tipo_id', 't.id')
            ->where('af_activo.estado', 1)
            ->where('af_activo.empresa', session('id_empresa'))
            ->where('af_activo.fecha_compra', '<=', $periodo)
            ->where('af_activo.costo', '>', 0)
            ->where(function ($q) use ($tipo) {
                if (!is_null($tipo))
                    $q->where('af_activo.tipo_id', $tipo);
            })
            ->get();
    }
    public static function saveDepreciacion($r)
    {
        DB::beginTransaction();
        $cont = 0;
        $arrayRespuesta = [];
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            $sucursal = Sucursales::where('id', $data['sucursal'])->first();
            $fecha_periodo = $r->periodo . '-' . date('t', strtotime($r->periodo));
            $aux['anio'] = date('Y', strtotime($r->periodo));
            $aux['mes'] = date('m', strtotime($r->periodo));
            $aux['fecha'] = $fecha_periodo;
            $aux['id_asiento'] = null;
            $aux['sucursal'] = $sucursal->codigo_sucursal;
            $aux['id_empresa'] = session('id_empresa');
            $aux['observacion'] = $r->observacion;
            $aux['id_usuariocrea'] = Auth::user()->id;
            $aux['id_usuariomod'] = Auth::user()->id;
            $aux['ip_creacion'] = $ip;
            $aux['ip_modificacion'] = $ip;
            $id = ActivosDepreciacion_Cabecera::insertGetId($aux);
            $r->c = 'Depreciaciones';
            $r->s = 'saveDepreciacion';
            $r->d = $origin['d'];
            $r->m = ActivosDepreciacion_Cabecera::$modelo;
            $r->o = 'Se agrego la nueva Depreciacion.: ' . $id;
            $cont++;
            if (count($r->input()) > 0) {
                foreach ($r->input() as $key => $row) {
                    $cadena = $key;

                    $buscar = "id_check";
                    $resultado = strpos($cadena, $buscar);
                    if ($resultado !== FALSE) {
                        /*
                        [0] => id_check
                            [1] => 2 | id activo
                            [2] => 10  | Vida util
                            [3] => 10 | Tasa o porcentaje
                            [4] => 6521_91 | Costo
                            [5] => 760_90 | valor acumulado
                            [6] => 54_35     valor a depresiar valor mes
                            [7] => 5706_66 | Saldo
                        */
                        $dato = explode('|', $key);
                        $det['depreciacion_cabecera_id'] = $id;
                        $det['activo_id'] = $dato[1];
                        $det['costo'] = str_replace('_', '.', $dato[4]);
                        $det['porcentaje'] = $dato[3];
                        $det['vidautil'] = $dato[2];
                        $det['valordepreciacion'] = str_replace('_', '.', $dato[6]);
                        $det['totaldepreciado'] =  str_replace('_', '.', $dato[5]) +  str_replace('_', '.', $dato[6]);
                        $det['saldo'] =  str_replace('_', '.', $dato[7]);
                        $det['id_usuariocrea'] = Auth::user()->id;
                        $det['id_usuariomod'] = Auth::user()->id;
                        $det['ip_creacion'] = $ip;
                        $det['ip_modificacion'] = $ip;
                        ActivosDepreciacion_Detalle::insert($det);
                    }
                }
            } else {
                $arrayRespuesta = [
                    'status' => 'info',
                    'code' => 204,
                    'message' => 'Todos los activos estan depreciados para este periodo',
                    'data' => $r->input(),
                    'reload' => ''
                ];
            }
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
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
            $r->c = 'Depreciaciones';
            $r->s = 'saveDepreciacion';
            $r->d =  $origin['d'];
            $r->m = ActivosDepreciacion_Cabecera::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
        }
        if ($cont > 0) {
            if (ActivosDepreciacion_Cabecera::asientoDepreciacion($id, $fecha_periodo, $r->observacion)) {
                DB::commit();
            }

            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => ''
            ];
        }
        return json_encode($arrayRespuesta);
    }
    public static function cambiarEstado($r)
    {
        try {
            $actualizar = ActivosDepreciacion_Cabecera::find($r->id);
            $actualizar->estado = 0;
            $actualizar->save();
            $asiento   = AsientosCabecera::find($actualizar->id_asiento);
            $asiento->estado = 1;
            $asiento->save();
            $id_empresa     = session('id_empresa');
            $ip_cliente      = $_SERVER["REMOTE_ADDR"];
            $idusuario       = Auth::user()->id;
            $id_asiento = AsientosCabecera::insertGetId([
                'observacion'     => $r->observacion,
                'fecha_asiento'   => date("Y-m-d H:i:s"),
                'id_empresa'      => $id_empresa,
                'valor'           => $asiento->valor,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'sucursal'        => $actualizar->sucursal,
            ]);
            $detasiento = AsientosDetalles::where('id_asiento_cabecera', $asiento->id)->orderBy('id', 'desc')->get();
            foreach ($detasiento as $row) {
                $asiento_detalle = [
                    'id_asiento_cabecera' => $id_asiento,
                    'id_plan_cuenta'      => $row->id_plan_cuenta,
                    'descripcion'         => $row->descripcion,
                    'fecha'               => date("Y-m-d H:i:s"),
                    'debe'                => $row->haber,
                    'haber'               => $row->debe,
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ];
                AsientosDetalles::insert($asiento_detalle);
            }
            LogAsiento::anulacion('AF-DEP', $actualizar->id_asiento, $id_asiento);
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
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
        return $arrayRespuesta;
    }
    public static function asientoDepreciacion($id_cab_depreciacion, $fecha_asiento, $det_asiento)
    {
        $estado = false;
        try {
            $cabecera_dep   = ActivosDepreciacion_Cabecera::where('id', $id_cab_depreciacion)->first();
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $id_empresa = session('id_empresa');
            $cabecera   = [
                'observacion'     => $det_asiento,
                'fecha_asiento'   => $fecha_asiento,
                'valor'           => 0,
                'estado'          => '2',
                'id_empresa'      => $id_empresa,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            $id_asiento_cabecera = AsientosCabecera::insertGetId($cabecera);
            if (!is_null($cabecera_dep)) {
                $dep_cab = [
                    'id_asiento' => $id_asiento_cabecera
                ];
                ActivosDepreciacion_Cabecera::where('id', $id_cab_depreciacion)->update($dep_cab);
            }
            $detalles = ActivosDepreciacion_Detalle::where('depreciacion_cabecera_id', $id_cab_depreciacion)
                ->join('af_activo as a', 'af_depreciacion_detalle.activo_id', 'a.id')
                ->join('af_tipo as t', 'a.tipo_id', 't.id')
                ->select('af_depreciacion_detalle.depreciacion_cabecera_id', 't.cuantadepreciacion', 't.cuentagastos', 't.id as id_tipo', DB::raw('SUM(valordepreciacion) as valordepreciacion'))
                ->groupBy('t.id')
                ->get();
            $acumvalor = 0;

            foreach ($detalles as $value) {
                $cuenta = Plan_Cuentas::where('id', $value->cuantadepreciacion)->first();
                if (!is_null($cuenta)) {
                    $acumvalor += $value->valordepreciacion;

                    $arrayDetalle = [
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta' => $value->cuantadepreciacion,
                        'descripcion' => $cuenta->nombre, //aqui puse el nombre de la cuenta del acreedor
                        'fecha' => date('Y-m-d H:i:s'),
                        'debe' => '0',
                        'haber' => $value->valordepreciacion,
                        'estado' => '1',
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $idusuario,
                        'ip_modificacion' => $idusuario,
                    ];
                    AsientosDetalles::insert($arrayDetalle);
                }
            }
            foreach ($detalles as $value) {
                $cuenta = Plan_Cuentas::where('id', $value->cuentagastos)->first();
                if (!is_null($cuenta)) {
                    $arrayDetalle_ = [
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $value->cuentagastos,
                        'descripcion'         => $cuenta->nombre, //aqui puse el nombre de la cuenta del acreedor
                        'fecha'               => date('Y-m-d H:i:s'),
                        'debe'                => $value->valordepreciacion,
                        'haber'               => 0,
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $idusuario,
                        'ip_modificacion'     => $idusuario,
                    ];
                    AsientosDetalles::insert($arrayDetalle_);
                }
            }
            AsientosCabecera::where('id', $id_asiento_cabecera)->update(['valor' => $acumvalor]);
            DB::commit();
            $estado = true;
        } catch (Exception $e) {
            DB::rollBack();
            $estado = false;
            dd($e->getMessage() . ' | ' . $e->getLine() . ' | ' . $e->getFile());
            echo $e->getMessage() . ' | ' . $e->getLine() . ' | ' . $e->getFile();
            exit;
        }
        return $estado;
    }
}
