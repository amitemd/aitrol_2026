<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class Rh_Configuraciones extends Model
{
    private static $modelo = 'Rh_Configuraciones';
    protected $table = 'ct_rh_valores';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getConfiguraciones($id = '')
    {
        if ($id == '') {
            return Rh_Configuraciones::join('new_db_catalogo as c', 'ct_rh_valores.tipo', 'c.valor')
                ->join('new_db_catalogo as p', 'c.id_catalogo_pertenece', 'p.id_catalogo')
                ->join('users as uc', 'ct_rh_valores.id_usuariocrea', 'uc.id')
                ->leftJoin('users as um', 'ct_rh_valores.id_usuariomod', 'um.id')
                ->where('p.catalogo', 'Configuracion Nomina')
                ->get([
                    'ct_rh_valores.id',
                    'c.catalogo as tipo',
                    'ct_rh_valores.valor',
                    'ct_rh_valores.estado',
                    DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                    DB::raw('IF(ct_rh_valores.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                ]);
        } else {
            return Rh_Configuraciones::where('id', $id)->first([
                'id',
                'tipo',
                'valor',
                'estado'
            ]);
        }
    }
    public static function getAportesPersonal($id = '')
    {

        if ($id == '') {
            return Rh_Configuraciones::join('new_db_catalogo as c', 'ct_rh_valores.tipo', 'c.valor')
                ->join('new_db_catalogo as p', 'c.id_catalogo_pertenece', 'p.id_catalogo')
                ->where('p.catalogo', 'Configuracion Nomina')
                ->where('ct_rh_valores.tipo', '1')
                ->where('ct_rh_valores.estado', '1')
                ->where('ct_rh_valores.id_empresa', session('id_empresa'))
                ->get([
                    'ct_rh_valores.id',
                    'ct_rh_valores.valor',
                    'c.catalogo as tipo',
                ]);
        } else {
            return Rh_Configuraciones::where('id', $id)->first([
                'ct_rh_valores.id',
                'c.catalogo as tipo',
                'ct_rh_valores.valor',
            ]);
        }
    }
    public static function saveConfiguracion($r)
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
                $id = Rh_Configuraciones::insertGetId($data);
                $r->c = 'RecursosHumanos';
                $r->s = 'saveConfiguracion';
                $r->d = $origin['d'];
                $r->m = Rh_Configuraciones::$modelo;
                $r->o = 'Se agrego el nuevo tipo de configuracion con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                Rh_Configuraciones::where('id', $id)->update($data);
                $r->c = 'RecursosHumanos';
                $r->s = 'saveConfiguracion';
                $r->d = $origin['d'];
                $r->m = Rh_Configuraciones::$modelo;
                $r->o = 'Se actualizo el tipo de configuracion con id.: ' . $id;
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
            $r->c = 'RecursosHumanos';
            $r->s = 'saveConfiguracion';
            $r->d =  $origin['d'];
            $r->m = Rh_Configuraciones::$modelo;
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
            if (Rh_Configuraciones::where('id', $r->id)->update(['estado' => $estado])) {
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
