<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class Bancos extends Model
{
    private static $modelo = 'Bancos';
    protected $table = 'ct_bancos';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';
    public static function getAllBancos($term)
    {
        return ConfigBancos::selectRaw('id as id,nombre as text')->where('nombre', 'like', '%' . $term . '%')->get();
    }

    public static function getBancos($id = '')
    {
        if ($id == '') {
            return Bancos::where("estado", 1)->get();
        } else {
            return Bancos::find($id);
        }
    }
    public static function getListabancos($id = '')
    {
        if ($id == '') {
            return Bancos::join('users as uc', 'ct_bancos.id_usuariocrea', 'uc.id')
                ->join('users as um', 'ct_bancos.id_usuariomod', 'um.id')
                ->get([
                    'ct_bancos.id',
                    'ct_bancos.nombre',
                    'ct_bancos.estado',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'ct_bancos.created_at',
                    'ct_bancos.updated_at',
                ]);
        } else {
            return Bancos::join('users as uc', 'ct_bancos.id_usuariocrea', 'uc.id')
                ->join('users as um', 'ct_bancos.id_usuariomod', 'um.id')
                ->where('ct_bancos.id', $id)
                ->first([
                    'ct_bancos.id',
                    'ct_bancos.nombre',
                    'ct_bancos.estado',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'ct_bancos.created_at',
                    'ct_bancos.updated_at',
                ]);
        }
    }
    public static function saveBancos($r)
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
                $id = Bancos::insertGetId($data);
                $r->c = 'Bancos';
                $r->s = 'saveBancos';
                $r->d = $origin['d'];
                $r->m = Bancos::$modelo;
                $r->o = 'Se agrego el nuevo banco.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                Bancos::where('id', $id)->update($data);
                $r->c = 'Bancos';
                $r->s = 'saveBancos';
                $r->d = $origin['d'];
                $r->m = Bancos::$modelo;
                $r->o = 'Se actualizo el nuevo banco.: ' . $id;
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
            $r->c = 'Bancos';
            $r->s = 'saveBancos';
            $r->d =  $origin['d'];
            $r->m = Bancos::$modelo;
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
    public static function cambiarEstado($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (Bancos::where('id', $r->id)->update(['estado' => $estado])) {
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

    public static function verificarbanco($r){
        $arrayRespuesta = [];
        $codBanco = Bancos::where('nombre', $r->nombre)->where('estado', '1')->first([
            'nombre'
        ]);
        if ($codBanco != '') {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => 'El banco ya se encuentra creado...',
                'data' => $r->input(),
            ];
        } else {
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => '',
                'data' => $r->input(),
            ];
        }
        return json_encode($arrayRespuesta);
    }

}
