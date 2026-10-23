<?php

namespace Sis_medico\v3\configuracion_sistema;


use Illuminate\Database\Eloquent\Model;
use Sis_medico\v3\contable\Utilidades;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\Auditorias;

class Hospital extends Model
{
    private static $modelo = 'Hospital';
    protected $table = 'hospital';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getUbicaciones($id = '')
    {
        if ($id == '') {
            return Hospital::get();
        } else {
            return Hospital::find($id);
        }
    }
    public static function saveUbicacion($r)
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
                $id = Hospital::insertGetId($data);
                $r->c = 'configuracionessistema';
                $r->s = 'saveUbicacion';
                $r->d = $origin['d'];
                $r->m = Hospital::$modelo;
                $r->o = 'Se agrego la nueva ubicacion.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                Hospital::where('id', $id)->update($data);
                $r->c = 'configuracionessistema';
                $r->s = 'saveUbicacion';
                $r->d = $origin['d'];
                $r->m = Hospital::$modelo;
                $r->o = 'Se actualizo la nueva ubicacion.: ' . $id;
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
            $r->s = 'saveUbicacion';
            $r->d = $origin['d'];
            $r->m = Hospital::$modelo;
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
            if (Hospital::where('id', $r->id)->update(['estado' => $estado])) {
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
                    'message' => 'Error al actualizar el estado de la ubicacion ',
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
