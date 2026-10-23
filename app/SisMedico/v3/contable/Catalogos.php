<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class Catalogos extends Model
{
    private static $modelo = 'Catalogos';
    protected $table = 'new_db_catalogo';
    protected $primaryKey  = 'id_catalogo';
    protected $keyType = 'string';

    public static function getSubCatalogos($id)
    {
        $nuevo = Catalogos::join('users as uc', 'new_db_catalogo.id_usuariocrea', 'uc.id')
            ->join('users as um', 'new_db_catalogo.id_usuariomod', 'um.id')
            ->where('id_catalogo_pertenece', $id)
            ->get([
                'new_db_catalogo.id_catalogo',
                'new_db_catalogo.catalogo',
                'new_db_catalogo.valor',
                'new_db_catalogo.created_at',
                'new_db_catalogo.updated_at',
                'uc.nombre1 as ucnombre1',
                'uc.apellido1 as ucapellido1',
                'um.nombre1 as umnombre1',
                'um.apellido1 as umapellido1',
                'new_db_catalogo.estado_catalogo',
            ]);
            //dd($nuevo);

            return $nuevo;
    }
    public static function estadoCatalogo($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (Catalogos::where('id_catalogo', $r->id)->update(['estado_catalogo' => $estado])) {
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
    public static function saveCatalogo($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            unset($data['id_subcatalogo']);
            $ip = Utilidades::getRealIP();
            if ($data['id_catalogo'] == '') {
                if ($origin['id_subcatalogo'] != '') {
                    $data['id_catalogo_pertenece'] = $origin['id_subcatalogo'];
                }
                $data['id_usuariocrea'] = Auth::user()->id;
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_creacion'] = $ip;
                $data['ip_modificacion'] = $ip;
                $data['created_at'] = date('Y-m-d H:i:s');
                $id = Catalogos::insertGetId($data);
                $r->c = 'Configuraciones';
                $r->s = 'saveCatalogo';
                $r->d = $origin['d'];
                $r->m = Catalogos::$modelo;
                $r->o = 'Se agrego el nuevo tipo de configuracion con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id_catalogo'];
                unset($data['id_catalogo']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                $data['updated_at'] = date('Y-m-d H:i:s');
                Catalogos::where('id_catalogo', $id)->update($data);
                $r->c = 'Configuraciones';
                $r->s = 'saveCatalogo';
                $r->d = $origin['d'];
                $r->m = Catalogos::$modelo;
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
            $r->s = 'saveCatalogo';
            $r->d =  $origin['d'];
            $r->m = Catalogos::$modelo;
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
    public static function getCatalogos($id = '')
    {
        if ($id == '') {
            return Catalogos::join('users as uc', 'new_db_catalogo.id_usuariocrea', 'uc.id')
                ->join('users as um', 'new_db_catalogo.id_usuariomod', 'um.id')
                ->whereNull('id_catalogo_pertenece')
                ->get([
                    'new_db_catalogo.id_catalogo',
                    'new_db_catalogo.catalogo',
                    'new_db_catalogo.valor',
                    'new_db_catalogo.created_at',
                    'new_db_catalogo.updated_at',
                    'uc.nombre1 as ucnombre1',
                    'uc.apellido1 as ucapellido1',
                    'um.nombre1 as umnombre1',
                    'um.apellido1 as umapellido1',
                    'new_db_catalogo.estado_catalogo',
                ]);
        } else {
            return Catalogos::where('id_catalogo', $id)->first();
        }
    }
}
