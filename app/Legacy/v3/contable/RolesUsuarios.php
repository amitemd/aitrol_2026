<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\User;

class RolesUsuarios extends Model
{
    private static $modelo = 'RolesUsuarios';
    protected $table = 'new_db_roles_usuarios';
    protected $primaryKey  = 'id_rol';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public static function getRolesUsuarios($id = '')
    {
        if ($id == '') {
            return RolesUsuarios::all();
        } else {
            return RolesUsuarios::find($id);
        }
    }
    public static function saveRolUsuarios($r)
    {
        $arrayRespuesta = [];
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $arraySubmodulosRol = [];
            $arrayPermisosRol = [];
            $data = $r->input();
            $origin = $r->input();
            unset($data['_token']);
            unset($data['d']);
            $ip = Utilidades::getRealIP();
            foreach ($data as $tex => $val) {
                if ($tex != 'id_rol' && $tex != 'nombre_rol') {
                    $dato = explode('|', $tex);
                    array_push($arraySubmodulosRol, $dato[0]);
                    array_push($arrayPermisosRol, $dato[1]);
                }
            }
            $arrayPermisosRol = array_unique($arrayPermisosRol);
            $reindexedArray = array_values($arrayPermisosRol);
            $array = [];
            $count = 0;
            foreach ($reindexedArray as $key => $value) {
                $array[$count] = $value;
                $count++;
            }
            $arrayPermisosRol = $array;
            if ($data['id_rol'] == '') {
                unset($data['id_rol']);
                $arrayRoles = [
                    'nombre_rol' => $r->nombre_rol,
                    'id_submodulos_rol' => json_encode($arraySubmodulosRol),
                    'id_permisos_rol' => json_encode($arrayPermisosRol),
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $id = RolesUsuarios::insertGetId($arrayRoles);
                $r->c = 'Configuraciones';
                $r->s = 'saveRolUsuarios';
                $r->d = $origin['d'];
                $r->m = RolesUsuarios::$modelo;
                $r->o = 'Se creo el rol de usuario con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id_rol'];
                unset($data['id_rol']);
                $arrayRoles = [
                    'nombre_rol' => $r->nombre_rol,
                    'id_submodulos_rol' => json_encode($arraySubmodulosRol),
                    'id_permisos_rol' => json_encode($arrayPermisosRol),
                    'id_usuariomod' => Auth::user()->id,
                    'ip_modificacion' => $ip,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                RolesUsuarios::where('id_rol', $id)->update($arrayRoles);
                $r->c = 'Configuraciones';
                $r->s = 'saveRolUsuarios';
                $r->d = $origin['d'];
                $r->m = RolesUsuarios::$modelo;
                $r->o = 'Se actualizo el rol de usuario con id.: ' . $id;
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
            $r->s = 'saveRolUsuarios';
            $r->d =  $origin['d'];
            $r->m = RolesUsuarios::$modelo;
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
                'reload' => 'v3/configuraciones/roles',
                'data' => $r->input(),
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function validarNombreRol($r)
    {
        $arrayRespuesta = [];
        $rolUsuario = RolesUsuarios::where('nombre_rol', $r->nombre)->first([
            'nombre_rol'
        ]);
        if ($rolUsuario != '') {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => 'El nombre del rol ya se encuentra registrado...',
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
