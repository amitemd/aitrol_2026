<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Tipo;

class Especialidad extends Model
{
    private static $modelo = 'Especialidad';
    protected $table = 'especialidad';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function  getEspecialidad($id = '')
    {
        if ($id == '') {
            return Especialidad::all();
        } else {
            return Especialidad::find($id);
        }
    }

    public static function saveEspecialidad($r)
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
                $id = Especialidad::insertGetId($data);
                $r->c = 'configuracionessistema';
                $r->s = 'saveEspecialidad';
                $r->d = $origin['d'];
                $r->m = Especialidad::$modelo;
                $r->o = 'Se agrego la nueva Especialidad.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                Especialidad::where('id', $id)->update($data);
                $r->c = 'configuracionessistema';
                $r->s = 'saveEspecialidad';
                $r->d = $origin['d'];
                $r->m = Especialidad::$modelo;
                $r->o = 'Se actualizo la nueva Especialidad.: ' . $id;
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
            $r->c = 'configuracionessistema';
            $r->s = 'saveEspecialidad';
            $r->d = $origin['d'];
            $r->m = Especialidad::$modelo;
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
            if (Especialidad::where('id', $r->id)->update(['estado' => $estado])) {
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
                    'message' => 'Error al actualizar el estado de la Especialidad ',
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
