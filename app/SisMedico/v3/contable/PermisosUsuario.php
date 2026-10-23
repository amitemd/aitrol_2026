<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermisosUsuario extends Model
{
    private static $modelo = 'PermisosUsuario';
    protected $table = 'new_db_permisos_usuario';
    protected $primaryKey  = 'id_permiso';
    const CREATED_AT = 'created_at_permiso';
    const UPDATED_AT = 'updated_at_permiso';

    public static function groupByIdPertenencia($data, $parentId = null)
    {
        $result = array();
        foreach ($data as $item) {
            if ($item['id_pertenece_submenu'] === $parentId) {
                $submenu = PermisosUsuario::groupByIdPertenencia($data, $item['id_menu']);
                if (!empty($submenu)) {
                    $item['submenus'] = $submenu;
                }
                $result[] = $item;
            }
        }
        return $result;
    }
    public static function mostrarMenu()
    {
        $sql = "
        select m.id_menu,m.nombre_menu,m1.id_menu id_submenu,m1.id_pertenece_menu id_pertenece_submenu,m1.nombre_menu nombre_submenu, 
        m2.id_menu id_sub_menu_menu,m2.id_pertenece_menu id_pertenece_submenu_submenu,m2.nombre_menu nombre_submenu_submenu
        from new_db_menu m
        join new_db_menu m1 on m.id_menu=m1.id_pertenece_menu
        left join new_db_menu m2 on m1.id_menu=m2.id_pertenece_menu
        where m.estado_menu=1
        and m.id_pertenece_menu is null
        order by m.orden_menu,m.nombre_menu,m1.orden_menu,m1.nombre_menu,m2.orden_menu,m2.nombre_menu;";
        $result = DB::select($sql);
        $result = json_decode(json_encode($result), true);
        $menu_array = array();
        if (count($result) > 0) {
            foreach ($result as $row) {
                $id_menu = $row['id_menu'];
                $id_pertenencia_submenu = $row['id_pertenece_submenu'];
                $id_pertenencia_submenu_submenu = $row['id_pertenece_submenu_submenu'];
                if (!isset($menu_array[$id_menu])) {
                    $menu_array[$id_menu] = array(
                        'id_menu' => $id_menu,
                        'nombre_menu' => $row['nombre_menu'],
                        'submenus' => array()
                    );
                }
                if (!isset($menu_array[$id_menu]['submenus'][$id_pertenencia_submenu])) {
                    $menu_array[$id_menu]['submenus'][$id_pertenencia_submenu] = array(
                        'id_menu' => $id_pertenencia_submenu,
                        'nombre_menu' => $row['nombre_submenu'],
                        'submenus' => array()
                    );
                }
                $menu_array[$id_menu]['submenus'][$id_pertenencia_submenu]['submenus'][$id_pertenencia_submenu_submenu] = array(
                    'id_menu' => $row['id_sub_menu_menu'],
                    'nombre_menu' => $row['nombre_submenu_submenu']
                );
            }
        } else {
            echo "No se encontraron resultados.";
        }
        echo '<pre>';
        print_r($menu_array);
        exit;
    }
    public static function savePermisos($r)
    {
        $cont = 0;
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $datos = $r->input();
            $origin = $datos;
            unset($datos['_token']);
            unset($datos['d']);
            $permisos = Menu::whereIn("id_menu", $datos['id_submenu_permiso'])->get(["id_pertenece_menu"]);;
            $idPerteneces = array_column($permisos->toArray(), 'id_pertenece_menu');
            $mergedArray = array_merge($datos['id_submenu_permiso'], $idPerteneces);
            $uniqueArray = array_unique($mergedArray);
            $dad = json_encode(array_values($uniqueArray),true);
            $datos['id_submenu_permiso'] = $dad;
            $datos['id_empresa_permiso'] = session('id_empresa');
            if ($datos['id_permiso'] != '') {
                $datos['id_usuario_creacion_permiso'] = Auth::user()->id;
                PermisosUsuario::where('id_permiso', $datos['id_permiso'])
                    ->update($datos);
                $r->c = 'Configuraciones';
                $r->s = 'savePermisos';
                $r->d = $origin['d'];
                $r->m = PermisosUsuario::$modelo;
                $r->o = 'Se actualiaron los permisos del usuario No.: ' . $datos['id_usuario_permiso'];
                $cont++;
            } else {
                $datos['id_usuario_modificacion_permiso'] = Auth::user()->id;
                PermisosUsuario::insert($datos);
                $r->c = 'Configuraciones';
                $r->s = 'savePermisos';
                $r->d = $origin['d'];
                $r->m = PermisosUsuario::$modelo;
                $r->o = 'Se creo los permisos del usuario';
                $cont++;
            }
            Auditorias::saveAuditoria($r, DB::getQueryLog());
        } catch (Exception $e) {
            DB::rollback();
            $r->c = 'Configuraciones';
            $r->s = 'savePermisos';
            $r->d = isset($origin['d']) ? $origin['d'] : '';
            $r->m = PermisosUsuario::$modelo;
            $r->o = 'Error al guardarlos permisos: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'reload' => 'v3/configuraciones/permisos',
                'data' => $r->input(),
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function getPermisos($id)
    {
        $arrayMensajesGenerales = [];
        try {
            return PermisosUsuario::where('id_usuario_permiso', $id)
                ->where('id_empresa_permiso', session('id_empresa'))
                ->first();
        } catch (Exception $e) {
            $dato = explode('\'', $e->getMessage());
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'model' => PermisosUsuario::$modelo,
                'table' => $dato[1],
                'message' => 'La empresa no tiene configurado el catalogo general',
            ];
            array_push($arrayMensajesGenerales, $arrayRespuesta);
        }
        $data['mensajesGenerales'] = $arrayMensajesGenerales;
    }
}
