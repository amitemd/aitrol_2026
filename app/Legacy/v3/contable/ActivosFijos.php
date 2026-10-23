<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Sis_medico\AfGrupo;
use Sis_medico\AfSubTipo;
use Sis_medico\AfTipo;
class ActivosFijos extends Model
{
    private static $modelo = 'ActivosFijos';
    protected $table = 'af_activo';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';
    public static function traerSubtipo($r)
    {
        return AF_Subtipo::where('tipo_id', $r->id)->get();
    }
    public static function obtenerImagenes($r)
    {
        $imagenes = ActivosFijosArchivos::where('estado', 1)->where('id_af_activo', $r->id)
            ->where('tipo', $r->t)
            ->get([
                'imagen as name',
                DB::raw('"12552" as size'),
                'id',
                DB::raw('CONCAT("avatars/", imagen) as url'),
            ]);

        return json_encode($imagenes);
    }
    public static function saveActivoFijo($r)
    {

        //dd($r->all());

        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $arrayErrores = [];
            $data = $r->input();


            if (empty($data['vida_util'])) {
                $arrayErrores[] = ['vida_util' => 'Vida útil requerida'];
            }
            if (empty($data['codigo'])) {
                $arrayErrores[] = ['codigo' => 'Código requerido'];
            }
            if (empty($data['nombre'])) {
                $arrayErrores[] = ['nombre' => 'Nombre requerido'];
            }
            if (empty($data['grupo_id'])) {
                $arrayErrores[] = ['grupo_id' => 'Grupo ID requerido'];
            }

            if (!empty($arrayErrores)) {
                return json_encode($arrayErrores);
            }



            $arrayActivo = [
                'codigo' => $data['codigo'],
                'nombre' => $data['nombre'],
                'descripcion' => $data["descripcion"],
                'responsable' => $data["responsable"],
                'acreedor' => $data["acreedor"],
                'marca'  => $data["marca"],
                'tipo_id' => $data["tipo"],
                'subtipo_id' => $data["subtipo"],
                'color' => $data["color"],
                'factura' => $data["factura"],
                'modelo' => $data["modelo"],
                'serie' => $data["serie"],
                'procedencia' => $data["procedencia"],
                'estado_activo' => $data["estado_activo"],
                'costo' => $data["costo"],
                'estado' => $data["estado"],
                'ubicacion' => $data["ubicacion"],
                'fecha_compra' => date('Y-m-d', strtotime($data["fecha_compra"])),
                'depreciacion_acum' => $data["dep_acum"],
                'tasa' => $data["tasa"],
                'tipo_tasa' => $data["tipo_tasa"],
                'dias_depreciacion' => $data["dias_depreciacion"],
                'valor_residual' => $data["valor_residual"],
                'nota' => $data["nota"],
                'vida_util' => $data["vida_util"],


            ];
            $arrayTipo = [
                'codigo' => $data['codigo1'],
                'grupo_id' => $data['grupo_id'],
                'nombre' => $data["tipo"],
                'vidautil' => $data["vida_util"],
                'tipo_tasa' => $data["tipo_tasa"],
                'tasa' => $data["tasa"],
            ];

            $ip = Utilidades::getRealIP();
            $userId = Auth::user()->id;
            $origin = $r->input();
            unset($data['d'], $data['_token']);

            if (empty($data['id'])) {

                $arrayActivo['id_usuariocrea'] = $userId;
                $arrayActivo['id_usuariomod'] = $userId;
                $arrayActivo['ip_creacion'] = $ip;
                $arrayActivo['ip_modificacion'] = $ip;
                $arrayTipo['id_usuariocrea'] = $userId;
                $arrayTipo['id_usuariomod'] = $userId;
                $arrayTipo['ip_creacion'] = $ip;
                $arrayTipo['ip_modificacion'] = $ip;
                $id = ActivosFijos::insertGetId($arrayActivo);
                AfTipo::insertGetId($arrayTipo);



                $r->o = 'Se agregó el nuevo transportista con id: ' . $id;
            } else {

                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = $userId;
                $data['ip_modificacion'] = $ip;
                ActivosFijos::where('id', $id)->update($arrayActivo);
                AfTipo::where('codigo', $data['codigo'])->update($arrayTipo);

                $r->o = 'Se actualizó el transportista con id: ' . $id;
            }

            $r->c = 'saveActivoFijo';
            $r->s = 'saveActivoFijo';
            $r->d = $origin['d'] ?? null;
            $r->m = ActivosFijos::$modelo;
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
            $r->c = 'saveActivoFijo';
            $r->s = 'saveActivoFijo';
            $r->d = $origin['d'] ?? null;
            $r->m = ActivosFijos::$modelo;
            $r->o = 'Ocurrió un error: ' . $e->getMessage();
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

        return json_encode($arrayErrores);
    }
    public static function getActivosFijosSucursales($r)
    {
        return DB::table('af_activo as a')
            ->leftJoin('af_factura_activo_det as d', 'a.id', '=', 'd.activo_id')
            ->leftJoin('af_factura_activo_cab as c', 'd.fact_activo_id', '=', 'c.id')
            ->leftJoin('ct_compras as co', 'c.id_asiento', '=', 'co.id_asiento_cabecera')
            ->where('a.empresa', session('id_empresa'))
            ->where('a.estado', 1)
            ->select(
                'a.id',
                'a.fecha_compra',
                'a.codigo',
                'a.nombre',
                'co.numero',
                'a.color',
                'a.modelo',
                'a.serie',
                'a.costo',
                'co.sucursal'
            )
            ->get();
    }
    public static function buscarCuenta($term)
    {
        $Plan = PlanCuentas::selectRaw('id_plan as plan,CONCAT(plan, " | ",nombre) as text')
            ->where(function ($q) use ($term) {
                $q->where('plan', 'like', '%' . $term . '%')
                    ->orWhere('nombre', 'like', '%' . $term . '%')
                    ->orWhere('descripcion', 'like', '%' . $term . '%');
            })
            ->where('id_empresa', session('id_empresa'))
            ->where('estado', 2)
            ->get();
        if (count($Plan) == 0) {
            $Plan = PlanCuentas::selectRaw('id_plan as plan,CONCAT(plan, " | ",nombre) as text')
                ->where(function ($q) use ($term) {
                    $q->where('id_plan', 'like', '%' . $term . '%')
                        ->orWhere('nombre', 'like', '%' . $term . '%')
                        ->orWhere('descripcion', 'like', '%' . $term . '%');
                })
                ->where('id_empresa', session('id_empresa'))
                ->where('estado', 2)
                ->get();
        }
        return $Plan;
    }
    public static function getActivosFijos($id = '')
    {
        if ($id == '') {
            return ActivosFijos::join('af_tipo as at', 'af_activo.tipo_id', 'at.id')
                ->join('af_subtipo as as', 'af_activo.subtipo_id', 'as.id')
                ->where('af_activo.empresa', session('id_empresa'))
                ->get([
                    'af_activo.id',
                    'af_activo.codigo',
                    'at.codigo as codigo1',
                    'af_activo.nombre',
                    'at.nombre as tipo',
                    'as.nombre as categoria',
                    'af_activo.responsable',
                    'af_activo.ubicacion',
                    'af_activo.estado',
                    'af_activo.descripcion',
                    'af_activo.responsable',
                    'af_activo.acreedor',
                    'af_activo.marca',
                    'af_activo.color',
                    'at.grupo_id',
                    'af_activo.factura',
                    'af_activo.modelo',
                    'af_activo.serie',
                    'af_activo.procedencia',
                    'af_activo.estado_activo',
                    'af_activo.costo',
                    'af_activo.estado',
                    'af_activo.ubicacion',
                    'af_activo.fecha_compra',
                    'af_activo.depreciacion_acum',
                    'af_activo.tasa',
                    'af_activo.tipo_tasa',
                    'af_activo.dias_depreciacion',
                    'af_activo.valor_residual',
                    'af_activo.nota',
                    'af_activo.vida_util',
                    'af_activo.subtipo_id',
                ]);
        } else {
            return ActivosFijos::join('af_tipo as at', 'af_activo.tipo_id', 'at.id')
                ->join('af_subtipo as as', 'af_activo.subtipo_id', 'as.id')
                ->where('af_activo.id', $id)
                ->where('af_activo.empresa', session('id_empresa'))
                ->first([
                    'af_activo.id',
                    'af_activo.codigo',
                    'at.codigo as codigo1',
                    'af_activo.nombre',
                    'at.nombre as tipo',
                    'as.nombre as categoria',
                    'af_activo.responsable',
                    'af_activo.ubicacion',
                    'af_activo.estado',
                    'af_activo.descripcion',
                    'af_activo.responsable',
                    'af_activo.acreedor',
                    'af_activo.marca',
                    'af_activo.color',
                    'at.grupo_id',
                    'af_activo.factura',
                    'af_activo.modelo',
                    'af_activo.serie',
                    'af_activo.procedencia',
                    'af_activo.estado_activo',
                    'af_activo.costo',
                    'af_activo.estado',
                    'af_activo.ubicacion',
                    DB::raw('DATE_FORMAT(af_activo.fecha_compra, "%Y-%m-%d") as fecha_compra'),
                    'af_activo.depreciacion_acum',
                    'af_activo.tasa',
                    'af_activo.tipo_tasa',
                    'af_activo.dias_depreciacion',
                    'af_activo.valor_residual',
                    'af_activo.nota',
                    'af_activo.vida_util',
                    'af_activo.tipo_id',
                    'af_activo.subtipo_id',
                ]);
        }
    }
    public static function getListaDepreciacion()
    {
        return ActivosFijos::join('af_tipo as at', 'af_activo.tipo_id', 'at.id')
            ->join('af_subtipo as as', 'af_activo.subtipo_id', 'as.id')
            ->get([
                'af_activo.*',
                'at.nombre as tipo',
                'as.nombre as categoria',
            ]);
    }
    public static function getActivosDepreciacion()
    {
        return ActivosFijos::join('af_tipo as at', 'af_activo.tipo_id', 'at.id')
            ->join('af_subtipo as as', 'af_activo.subtipo_id', 'as.id')
            ->get([
                'af_activo.*',
                'at.nombre as tipo',
                'as.nombre as categoria',
            ]);
    }
    public static function saveTransportista($r)
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
                unset($data['id']);
                $data['id_usuariocrea'] = Auth::user()->id;
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_creacion'] = $ip;
                $data['ip_modificacion'] = $ip;
                $id = ActivosFijos::insertGetId($data);
                $r->c = 'GuiadeRemision';
                $r->s = 'saveTransportista';
                $r->d = $origin['d'];
                $r->m = ActivosFijos::$modelo;
                $r->o = 'Se agrego el nuevo transportista con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                ActivosFijos::where('id', $id)->update($data);
                $r->c = 'GuiadeRemision';
                $r->s = 'saveTransportista';
                $r->d = $origin['d'];
                $r->m = ActivosFijos::$modelo;
                $r->o = 'Se actualizo el transportista con id.: ' . $id;
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
            $r->c = 'GuiadeRemision';
            $r->s = 'saveTransportista';
            $r->d =  $origin['d'];
            $r->m = ActivosFijos::$modelo;
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
    public static function cambiarEstado($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (ActivosFijos::where('id', $r->id)->update(['estado' => $estado])) {
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
}
