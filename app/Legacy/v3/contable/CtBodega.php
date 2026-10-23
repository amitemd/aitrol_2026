<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class CtBodega extends Model
{
    private static $modelo = 'CtBodega';
    protected $table = 'ct_bodegas';
    protected $primaryKey  = 'id';

    public static function getBodega($id = '')
    {
        $id_empresa = session('id_empresa');
        if ($id == '') {
            return CtBodega::where('id_empresa',$id_empresa)
            ->where('estado',1)
            ->get();
        } else {
            return CtBodega::find($id);
        }
    }
    public static function getBodegas($id = '')
    {
        $id_empresa = session('id_empresa');
        if ($id == '') {
            return CtBodega::join('users as uc', 'ct_bancos.id_usuariocrea', 'uc.id')
                ->join('users as um', 'ct_bodegas.id_usuariomod', 'um.id')
                ->join('users as enc', 'ct_bancos.encargado', 'enc.id')
                ->leftjoin('hospital as h', 'ct_bodegas.id_hospital', 'h.id')
                ->leftjoin('empresa as e', 'ct_bancos.id_empresa', 'e.id')
                ->leftjoin('ct_clientes as c', 'ct_bancos.id_cliente', 'c.identificacion')
                ->where('ct_bodegas.id_empresa', $id_empresa)
                ->get([
                    'ct_bodegas.id',
                    'ct_bodegas.nombre',
                    'ct_bodegas.color',
                    'ct_bodegas.mostrar',
                    'ct_bodegas.prioridad',
                    'ct_bodegas.tipo',
                    'ct_bodegas.prioridad_farmacia',
                    'ct_bodegas.estado',
                    'c.nombre as nombre_cliente',
                    'e.razonsocial',
                    'e.nombrecomercial',
                    'h.nombre as nombre_hospital',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'um.nombre1 as ucnombre',
                    'um.apellido1 as ucapellido',
                    'enc.nombre1 as encargado_nombre',
                    'enc.apellido1 as encargado_apellido',
                    'ct_bodegas.created_at',
                    'ct_bodegas.updated_at',
                ]);
        } else {
            return CtBodega::join('users as uc', 'ct_bancos.id_usuariocrea', 'uc.id')
                ->join('users as um', 'ct_bodegas.id_usuariomod', 'um.id')
                ->join('users as enc', 'ct_bancos.encargado', 'enc.id')
                ->leftjoin('hospital as h', 'ct_bodegas.id_hospital', 'h.id')
                ->leftjoin('empresa as e', 'ct_bancos.id_empresa', 'e.id')
                ->leftjoin('ct_clientes as c', 'ct_bancos.id_cliente', 'c.identificacion')
                ->where('ct_bodegas.id', $id)
                ->where('ct_bodegas.id_empresa', $id_empresa)
                ->get([
                    'ct_bodegas.id',
                    'ct_bodegas.nombre',
                    'ct_bodegas.color',
                    'ct_bodegas.mostrar',
                    'ct_bodegas.prioridad',
                    'ct_bodegas.tipo',
                    'ct_bodegas.prioridad_farmacia',
                    'ct_bodegas.estado',
                    'c.nombre as nombre_cliente',
                    'e.razonsocial',
                    'e.nombrecomercial',
                    'h.nombre as nombre_hospital',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'um.nombre1 as ucnombre',
                    'um.apellido1 as ucapellido',
                    'enc.nombre1 as encargado_nombre',
                    'enc.apellido1 as encargado_apellido',
                    'ct_bodegas.created_at',
                    'ct_bodegas.updated_at',
                ]);
        }
    }
    public static function getBodegaCombo($term)
    {
        $id_empresa = session('id_empresa');
        return CtBodega::selectRaw('id, nombre as text')
            ->where(function ($q) use ($term) {
                $q->orWhere('nombre', 'like', '%' . $term . '%');
                $q->orWhere('id', 'like', '%' . $term . '%');
            })
            ->where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
    }
    public static function saveBodega($r)
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
                $data['id_usuariocrea'] = Auth::user()->id;
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_creacion'] = $ip;
                $data['ip_modificacion'] = $ip;
                $id = CtBodega::insertGetId($data);
                $r->c = 'CtBodega';
                $r->s = 'saveCtBodega';
                $r->d = $origin['d'];
                $r->m = CtBodega::$modelo;
                $r->o = 'Se agrego la nueva bodega.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                CtBodega::where('id', $id)->update($data);
                $r->c = 'CtBodega';
                $r->s = 'saveCtBodega';
                $r->d = $origin['d'];
                $r->m = CtBodega::$modelo;
                $r->o = 'Se actualizo la bodega.: ' . $id;
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
            $r->c = 'CtBodega';
            $r->s = 'saveCtBodega';
            $r->d =  $origin['d'];
            $r->m = CtBodega::$modelo;
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
            if (CtBodega::where('id', $r->id)->update(['estado' => $estado])) {
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
