<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Sis_medico\Empresa;

class Planes extends Model
{
    private static $modelo = 'Planes';
    protected $table = 'new_db_planes';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getSelectPlan()
    {

        $empresa  = Empresa::where('estado', '1')->get();

        return ["planes" => Planes::get(), "empresa" => $empresa];
    }
    public static function getPlanes($id = '')
    {

        if ($id == '') {
            //dd('entra');

            return Planes::get();
        } else {
            return Planes::find($id);
        }
    }

    public static function savePlan($r)
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
                $data['created_at'] = date('Y-m-d H:i:s');
                $id = Planes::insertGetId($data);
                $r->c = 'Configuraciones';
                $r->s = 'savePlan';
                $r->d = $origin['d'];
                $r->m = Planes::$modelo;
                $r->o = 'Se agrego el nuevo tipo de configuracion con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                $data['updated_at'] = date('Y-m-d H:i:s');
                Planes::where('id', $id)->update($data);
                $r->c = 'Configuraciones';
                $r->s = 'savePlan';
                $r->d = $origin['d'];
                $r->m = Planes::$modelo;
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
            $r->c = 'Configuraciones';
            $r->s = 'savePlan';
            $r->d =  $origin['d'];
            $r->m = Planes::$modelo;
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

    public static function validarnombreplan($r)
    {

        $plan = planes::where('nombre', $r->nombre)->first();
        if (!is_null($plan)) {
            return ["status" => true];
        }
        return ["status" => false];
    }

    public static function cambiarEstadoPlan($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (Planes::where('id', $r->id)->update(['estado' => $estado])) {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro editado correctamente',
                    'data' => $r->input(),
                ];
            } else {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Error al actualizar el estado de comprobante de ingreso',
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
