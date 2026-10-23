<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class Seleccion_planes extends Model
{
    private static $modelo = 'Seleccion_planes';
    protected $table = 'new_db_pago_planes';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getPlanEmpresa()
    {
        return DB::table('new_db_pago_planes as np')
            ->Join('empresa as e', 'np.id_empresa', '=', 'e.id')
            ->Join('new_db_planes as p', 'p.id', '=', 'np.id_plan')
            ->where('id_empresa', session('id_empresa'))
            ->where('np.estado', 1)
            ->orderBy('id', 'desc')
            ->select(
                'np.*',
                'e.id as empresa_id',
                'e.razonsocial',
                'p.nombre',
                'p.tiempo',
                'p.valor',
                'p.estado as estado_planes'
            )
            ->first();
    }
    public static function getSeleccionPlanes()
    {
        return DB::table('new_db_pago_planes as np')
            ->Join('empresa as e', 'np.id_empresa', '=', 'e.id')
            ->Join('new_db_planes as p', 'p.id', '=', 'np.id_plan')
            ->where('np.estado', 1)
            ->select(
                'np.*',
                'e.id as empresa_id',
                'e.razonsocial',
                'p.nombre',
                'p.tiempo',
                'p.valor',
                'p.estado as estado_planes'
            )
            ->get();
    }
    public static function saveSeleccionPlan($r)
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
                $id = Seleccion_planes::insertGetId($data);
                $r->c = 'Configuraciones';
                $r->s = 'saveselecionarPlan';
                $r->d = $origin['d'];
                $r->m = Seleccion_planes::$modelo;
                $r->o = 'Se agrego el nuevo tipo de configuracion con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                $data['updated_at'] = date('Y-m-d H:i:s');
                Seleccion_planes::where('id', $id)->update($data);
                $r->c = 'Configuraciones';
                $r->s = 'saveselecionarPlan';
                $r->d = $origin['d'];
                $r->m = Seleccion_planes::$modelo;
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
            $r->s = 'saveselecionarPlan';
            $r->d =  $origin['d'];
            $r->m = Seleccion_planes::$modelo;
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
}
