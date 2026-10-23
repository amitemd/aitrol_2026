<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class BodegasContable extends Model
{
    private static $modelo = 'Bodegas_Contables';
    protected $table = 'ct_bodegas';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

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
                $id = BodegasContable::insertGetId($data);
                $r->c = 'Configuraciones';
                $r->s = 'saveBodegas';
                $r->d = $origin['d'];
                $r->m = BodegasContable::$modelo;
                $r->o = 'Se agrego el nueva bodega.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                BodegasContable::where('id', $id)->update($data);
                $r->c = 'Configuraciones';
                $r->s = 'saveBodegas';
                $r->d = $origin['d'];
                $r->m = BodegasContable::$modelo;
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
            $r->c = 'Configuraciones';
            $r->s = 'saveBodegas';
            $r->d =  $origin['d'];
            $r->m = BodegasContable::$modelo;
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
    public static function getBodegas($id = '')
    {
        if ($id == '') {
            return BodegasContable::get();
        } else {
            return BodegasContable::find($id);
        }
    }
}
