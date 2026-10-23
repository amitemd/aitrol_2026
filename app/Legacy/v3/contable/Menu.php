<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    private static $modelo = 'Menu';
    protected $table = 'new_db_menu';
    protected $primaryKey  = 'id_menu';
    const CREATED_AT = 'created_at_menu';
    const UPDATED_AT = 'updated_at_menu';

    public static function getMenuRoles($r)
    {

        /*$sql = 'select new_db_menu.id_menu, new_db_menu.nombre_menu, new_db_menu.icon_menu, s.nombre_menu as nombre_submenu, s.id_menu as id_submenu,r.id_submodulos_rol,
        r.id_permisos_rol,new_db_menu.id_pertenece_menu,id_submodulos_rol
        from new_db_menu 
        inner join new_db_menu as s on new_db_menu.id_menu = s.id_pertenece_menu 
        left join new_db_roles_usuarios r on s.id_pertenece_menu in(51,2,5)
        where new_db_menu.estado_menu = 1 and new_db_menu.id_pertenece_menu is null and r.id_rol = '.$r->id.' order by new_db_menu.orden_menu asc';
        return DB::select($sql);*/

        if (!isset($r->rol)) {
            return self::leftjoin('new_db_menu as s', 'new_db_menu.id_menu', 's.id_pertenece_menu')
                ->where('new_db_menu.estado_menu', 1)
                ->whereNull('new_db_menu.id_pertenece_menu')
                ->orderBy('new_db_menu.orden_menu', 'asc')
                ->get([
                    'new_db_menu.id_menu',
                    'new_db_menu.nombre_menu',
                    'new_db_menu.icon_menu',
                    's.nombre_menu as nombre_submenu',
                    's.id_menu as id_submenu'
                ]);
        } else {
            $new_db_roles_usuarios = Menu::where("estado_menu", 1)->pluck("id_menu");
            $trr = implode(",", $new_db_roles_usuarios->toArray());
                $sql = 'select new_db_menu.id_menu, new_db_menu.nombre_menu, new_db_menu.icon_menu, s.nombre_menu as nombre_submenu, s.id_menu as id_submenu,r.id_submodulos_rol,
        r.id_permisos_rol,new_db_menu.id_pertenece_menu,id_submodulos_rol,r.nombre_rol
        from new_db_menu 
        inner join new_db_menu as s on new_db_menu.id_menu = s.id_pertenece_menu 
        left join new_db_roles_usuarios r on s.id_pertenece_menu in(' . $trr . ')
        where new_db_menu.estado_menu = 1 and new_db_menu.id_pertenece_menu is null and r.id_rol = ' . $r->rol . ' order by new_db_menu.orden_menu asc';
            $res =  DB::select($sql);
            foreach ($res as $key => $value) {
                $menu = json_decode($value->id_permisos_rol);
                foreach ($menu as $value2) { //los menus cabeza
                    $submenu = json_decode($value->id_submodulos_rol);
                    foreach ($submenu as $value3) {
                        $idM = explode("_", $value3)[1]; //submodulo que tengo permiso

                        if ($value->id_menu == $value2 && $value->id_submenu == $idM) {
                            $letraM = explode("_", $value3)[0];
                            switch ($letraM) {
                                case "crear":
                                    $res[$key]->crear = 1;
                                    break;
                                case "actualizar":
                                    $res[$key]->actualizar = 1;
                                    break;
                                case "leer":
                                    $res[$key]->leer = 1;
                                    break;
                                default:
                            }
                        }
                    }
                }
            }
            return $res;
        }
    }
    public static function menuCombo($id)
    {
        $combo = '';
        $menu = Menu::find($id);
        if ($menu != '') {
            $combo = '<option value="' . $menu->id_menu . '" selected>' . $menu->nombre_menu . '</option>';
        }
        return $combo;
    }
    public static function estadoMenu($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (Menu::where('id_menu', $r->id)->update(['estado_menu' => $estado])) {
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
    public static function saveMenu($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            if ($data['id_menu'] == '') {
                unset($data['id_menu']);
                $data['id_usuario_creacion_menu'] = Auth::user()->id;
                $data['id_usuario_modificacion_menu'] = Auth::user()->id;
                $data['created_at_menu'] = date('Y-m-d H:i:s');
                $id = Menu::insertGetId($data);
                $r->c = 'Configuraciones';
                $r->s = 'saveMenu';
                $r->d = $origin['d'];
                $r->m = Menu::$modelo;
                $r->o = 'Se creo el menu con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id_menu'];
                unset($data['id_menu']);
                $data['id_usuario_modificacion_menu'] = Auth::user()->id;
                $data['updated_at_menu'] = date('Y-m-d H:i:s');
                Menu::where('id_menu', $id)->update($data);
                $r->c = 'Configuraciones';
                $r->s = 'saveMenu';
                $r->d = $origin['d'];
                $r->m = Menu::$modelo;
                $r->o = 'Se actualizo el menu con id.: ' . $id;
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
            $r->s = 'saveMenu';
            $r->d =  $arrayRespuesta;
            $r->m = Menu::$modelo;
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
                'reload' => '',
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public function submenu()
    {
        return $this->hasMany('Sis_medico\v3\contable\Menu', 'id_pertenece_menu');
    }
    public static function getMenuTotal($id = '')
    {
        if ($id == '')
            return Menu::orderBy('orden_menu', 'asc')->whereNull('id_pertenece_menu')->get();
        else
            return Menu::orderBy('id_menu', $id)->first();
    }
    public static function getMenu($id = '')
    {
        $arrayMensajesGenerales = [];
        try {
            if ($id == '')
                return Menu::where('estado_menu', 1)->whereNull('id_pertenece_menu')->orderBy('orden_menu', 'asc')->get();
            else
                return Menu::where('id_menu', $id)->whereNull('id_pertenece_menu')->first();
        } catch (\Exception $e) {
            $dato = explode('\'', $e->getMessage());
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'model' => Menu::$modelo,
                'table' => $dato[1],
                'message' => 'La empresa no tiene configurado el catalogo general',
            ];
            array_push($arrayMensajesGenerales, $arrayRespuesta);
        }
        $data['mensajesGenerales'] = $arrayMensajesGenerales;
    }
    public static function getMenus($term = '')
    {
        return Menu::selectRaw('id_menu as id,nombre_menu as text')
            ->where('estado_menu', 1)
            ->whereNull('id_pertenece_menu')
            ->where('nombre_menu', 'like', '%' . $term . '%')->get();
    }
}
