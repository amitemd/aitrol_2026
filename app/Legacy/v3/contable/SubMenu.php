<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubMenu extends Model
{
    private static $modelo = 'SubMenu';
    protected $table = 'new_db_submenu';
    protected $primaryKey  = 'id_submenu';
    const CREATED_AT = 'created_at_submenu';
    const UPDATED_AT = 'updated_at_submenu';

    public function crear_menu($id_padre, $controlador = '', $control = '', $espacio = '', $plantilla = '', $permisos = null)
    {
        $menu = "";
        $consulta = Menu::where('estado_menu', 1)
            ->where('id_pertenece_menu', $id_padre)
            ->orderBy('orden_menu')
            ->get();
        $veri = $permisos != null ? $permisos : '';
        if ($veri != '') {
            $consulta = $consulta->whereIn('id_menu', json_decode($veri, true));
        }
        if ($veri != '') {
            $indice = 0;
            foreach ($consulta as $row) {
                $indice++;
                $flecha = '';
                $activo_ = '';
                if ($control != '') {
                    if (config('data.controlador') == $control && strtolower(config('data.submodulo')) == strtolower($row->funcion_menu))
                        $activo_ = 'active';
                }
                $submenu = $this->crear_menu($row->id_menu, $row->controlador_menu, '', '', '', $permisos);
                if ($submenu !== "") {
                    $flecha = '<i class="fas fa-angle-left right"></i>';
                }
                $menu .= '<li class="nav-item">';
                $menu .= '<a href="v3/' . ($controlador == "" ? "" : $controlador . '/') . $row->controlador_menu . '/' . $row->funcion_menu . '" class="nav-link ' . $activo_ . '">';
                $menu .= '&nbsp;&nbsp;&nbsp;' . $espacio . $row->icon_menu . '<p>' . trans('v3/new_submodulo.' . $row->funcion_menu) . $flecha . '</p></a>';
                if (!empty($submenu)) {
                    $menu .= '<ul class="nav nav-treeview">' . $submenu . '</ul>';
                }
                $menu .= '</li>';
            }
        }
        return $menu;
    }
    public static function getSubMenusExt($id = '')
    {
        return Menu::where('id_pertenece_menu', $id)->get();
    }
    public static function getSubMenu($id)
    {
        return Menu::leftjoin('new_db_menu as m', 'new_db_menu.id_pertenece_menu', 'm.id_menu')
            ->where('new_db_menu.id_menu', $id)
            ->first([
                'new_db_menu.id_menu',
                'new_db_menu.nombre_menu as nombre_submenu',
                'm.nombre_menu',
                'new_db_menu.icon_menu',
                'new_db_menu.controlador_menu',
                'new_db_menu.funcion_menu',
                'new_db_menu.orden_menu',
                'new_db_menu.estado_menu',
            ]);
    }
    public static function getSubMenus($id, $permisos = '')
    {
        return Menu::leftjoin('new_db_menu as m', 'new_db_menu.id_pertenece_menu', 'm.id_menu')
            // ->where('new_db_menu.estado_menu', 1)
            ->where('new_db_menu.id_pertenece_menu', $id)
            ->where(function ($q) use ($permisos) {
                if ($permisos != '') {
                    $q->whereIn('new_db_menu.id_menu', $permisos);
                }
            })
            ->get([
                'new_db_menu.id_menu',
                'new_db_menu.nombre_menu as nombre_submenu',
                'm.nombre_menu',
                'new_db_menu.icon_menu',
                'new_db_menu.controlador_menu',
                'new_db_menu.funcion_menu',
                'new_db_menu.orden_menu',
                'new_db_menu.estado_menu',
            ]);
    }
    public static function saveSubMenu($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        try {
            $data = $r->input();
            $origin = $r->input();
            $ip = Utilidades::getRealIP();
            unset($data['d']);
            unset($data['_token']);
            $nombreMenu = Menu::where('id_menu', $data['id_pertenece_menu'])->first(['nombre_menu']);
            $data['controlador_menu'] = Utilidades::sanear_string(strtolower($nombreMenu->nombre_menu));
            if ($data['id_menu'] == '') {
                unset($data['id_menu']);
                $data['id_usuario_creacion_menu'] = Auth::user()->id;
                $data['id_usuario_modificacion_menu'] = Auth::user()->id;
                $data['ip_creacion_menu'] = $ip;
                $id = Menu::insertGetId($data);
                $r->c = 'Configuraciones';
                $r->s = 'saveSubMenu';
                $r->d = $origin['d'];
                $r->m = SubMenu::$modelo;
                $r->o = 'Se creo el submenu con id.: ' . $id;
            } else {
                $id = $data['id_menu'];
                unset($data['id_menu']);
                $data['id_usuario_modificacion_menu'] = Auth::user()->id;
                $data['ip_modificacion_menu'] = $ip;
                Menu::where('id_menu', $id)->update($data);
                $r->c = 'Configuraciones';
                $r->s = 'saveSubMenu';
                $r->d = $origin['d'];
                $r->m = SubMenu::$modelo;
                $r->o = 'Se actualizo el submenu con id.: ' . $id;
            }
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => '',
            ];
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::commit();
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
            $r->s = 'saveSubMenu';
            $r->d = $origin['d'];
            $r->m = SubMenu::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
        }
        return json_encode($arrayRespuesta);
    }
}
