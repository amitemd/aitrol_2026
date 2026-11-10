<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    private static $modelo = 'Menu';

    private static $activeTreeRows;

    protected $table = 'new_db_menu';

    protected $primaryKey = 'id_menu';

    const CREATED_AT = 'created_at_menu';

    const UPDATED_AT = 'updated_at_menu';

    /**
     * Devuelve la etiqueta localizada de un elemento del menú V3.
     *
     * Los grupos jerárquicos no siempre tienen controlador o función, por lo
     * que su id es la única clave estable. Para conservar compatibilidad con
     * los módulos antiguos se intenta después la traducción por función y por
     * controlador, y finalmente se usa el nombre almacenado en la base.
     */
    public static function translatedLabel($menu)
    {
        if (! $menu) {
            return '';
        }

        $keys = [];

        if (! empty($menu->id_menu)) {
            $keys[] = 'v3/menu_items.'.$menu->id_menu;
        }

        if (! empty($menu->funcion_menu)) {
            $keys[] = 'v3/new_submodulo.'.$menu->funcion_menu;
        }

        if (! empty($menu->controlador_menu)
            && ! in_array($menu->controlador_menu, ['legacy-route', 'legacy-url'])) {
            $keys[] = 'v3/new_menu.'.$menu->controlador_menu;
        }

        foreach ($keys as $key) {
            $label = trans($key);
            if ($label !== $key) {
                return $label;
            }
        }

        if (! empty($menu->nombre_menu)) {
            return $menu->nombre_menu;
        }

        if (! empty($menu->funcion_menu)) {
            return $menu->funcion_menu;
        }

        return ! empty($menu->controlador_menu) ? $menu->controlador_menu : '';
    }

    /**
     * Genera un icono compatible con Font Awesome 5 para el menú V3.
     *
     * Parte del catálogo histórico tiene iconos vacíos o clases de Font
     * Awesome 6. Se prioriza el significado de la opción y, para elementos
     * nuevos, se normaliza el icono almacenado antes de usar un fallback.
     */
    public static function iconHtml($menu)
    {
        $label = self::translatedLabel($menu);
        $search = implode(' ', [
            $label,
            isset($menu->nombre_menu) ? $menu->nombre_menu : '',
            isset($menu->controlador_menu) ? $menu->controlador_menu : '',
            isset($menu->funcion_menu) ? $menu->funcion_menu : '',
        ]);
        $search = mb_strtolower($search, 'UTF-8');
        $search = strtr($search, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ü' => 'u', 'ñ' => 'n',
        ]);

        $semanticIcons = [
            'recursos humanos' => 'fa-users-cog',
            'talento humano' => 'fa-users-cog',
            'servicios generales' => 'fa-tools',
            'limpieza' => 'fa-broom',
            'estadistic' => 'fa-chart-bar',
            'encuesta' => 'fa-poll',
            'transportista' => 'fa-truck',
            'ordenes' => 'fa-clipboard-list',
            'sub tipos' => 'fa-folder-open',
            'pentax' => 'fa-tv',
            'cruce de cuentas' => 'fa-exchange-alt',
            'cruce de valores' => 'fa-exchange-alt',
            'guia de remision' => 'fa-truck',
            'flujo de caja' => 'fa-chart-line',
            'caja y bancos' => 'fa-university',
            'caja banco' => 'fa-university',
            'nota de credito' => 'fa-file-invoice',
            'nota de debito' => 'fa-file-invoice-dollar',
            'comprobante' => 'fa-file-invoice',
            'retencion' => 'fa-percent',
            'cheque' => 'fa-money-check-alt',
            'rubros' => 'fa-tags',
            'rubro' => 'fa-tags',
            'contabilidad' => 'fa-book',
            'acreedores' => 'fa-handshake',
            'acreedor' => 'fa-handshake',
            'proveedores' => 'fa-truck-loading',
            'proveedor' => 'fa-truck-loading',
            'clientes' => 'fa-users',
            'cliente' => 'fa-users',
            'activos fijos' => 'fa-building',
            'compras' => 'fa-shopping-cart',
            'compra' => 'fa-shopping-cart',
            'inventario' => 'fa-boxes',
            'ventas' => 'fa-money-bill-wave',
            'venta' => 'fa-money-bill-wave',
            'declaracion' => 'fa-file-invoice-dollar',
            'impuesto' => 'fa-file-invoice-dollar',
            'importaciones' => 'fa-ship',
            'importacion' => 'fa-ship',
            'nomina' => 'fa-users-cog',
            'financiero' => 'fa-chart-pie',
            'lista' => 'fa-list',
            'saldo' => 'fa-balance-scale',
            'factura' => 'fa-file-invoice-dollar',
            'configuracion' => 'fa-cogs',
            'reporte' => 'fa-print',
        ];

        foreach ($semanticIcons as $term => $icon) {
            if (strpos($search, $term) !== false) {
                return '<i class="nav-icon fas '.$icon.'" aria-hidden="true"></i>';
            }
        }

        $stored = isset($menu->icon_menu) ? trim((string) $menu->icon_menu) : '';
        $aliases = [
            'fa-chart-simple' => 'fa-chart-bar',
            'fa-location-dot' => 'fa-map-marker-alt',
            'fa-person' => 'fa-user',
            'fa-circle-plus' => 'fa-plus-circle',
            'fa-circle-xmark' => 'fa-times-circle',
            'fa-pen-to-square' => 'fa-edit',
            'fa-floppy-disk' => 'fa-save',
            'fa-building-columns' => 'fa-university',
            'fa-money-bill-transfer' => 'fa-exchange-alt',
            'fa-arrow-right-arrow-left' => 'fa-exchange-alt',
            'fa-magnifying-glass' => 'fa-search',
            'fa-trash-can' => 'fa-trash-alt',
            'fa-xmark' => 'fa-times',
            'fa-area-chart' => 'fa-chart-area',
            'fa-bank' => 'fa-university',
            'fa-bar-chart' => 'fa-chart-bar',
            'fa-child-reaching' => 'fa-users-cog',
            'fa-commenting-o' => 'fa-comments',
            'fa-exchange' => 'fa-exchange-alt',
            'fa-file-archive-o' => 'fa-file-archive',
            'fa-file-lines' => 'fa-file-alt',
            'fa-file-text-o' => 'fa-file-alt',
            'fa-folder-tree' => 'fa-folder-open',
            'fa-handshake-o' => 'fa-handshake',
            'fa-hospital-o' => 'fa-hospital',
            'fa-money-check-dollar' => 'fa-money-check-alt',
            'fa-pie-chart' => 'fa-chart-pie',
            'fa-rectangle-list' => 'fa-list-alt',
            'fa-shuffle' => 'fa-random',
            'fa-table-list' => 'fa-table',
            'fa-television' => 'fa-tv',
            'fa-thumb-tack' => 'fa-thumbtack',
            'fa-truck-arrow-right' => 'fa-truck',
            'fa-user-circle-o' => 'fa-user-circle',
            'fa-volume-control-phone' => 'fa-phone-volume',
            'fa-window-close-o' => 'fa-times-circle',
        ];

        if (preg_match('/fa-[a-z0-9-]+/', $stored, $match)) {
            $icon = isset($aliases[$match[0]]) ? $aliases[$match[0]] : $match[0];

            return '<i class="nav-icon fas '.e($icon).'" aria-hidden="true"></i>';
        }

        return '<i class="nav-icon fas fa-circle" aria-hidden="true"></i>';
    }

    public static function getMenuRoles($r)
    {

        /*$sql = 'select new_db_menu.id_menu, new_db_menu.nombre_menu, new_db_menu.icon_menu, s.nombre_menu as nombre_submenu, s.id_menu as id_submenu,r.id_submodulos_rol,
        r.id_permisos_rol,new_db_menu.id_pertenece_menu,id_submodulos_rol
        from new_db_menu
        inner join new_db_menu as s on new_db_menu.id_menu = s.id_pertenece_menu
        left join new_db_roles_usuarios r on s.id_pertenece_menu in(51,2,5)
        where new_db_menu.estado_menu = 1 and new_db_menu.id_pertenece_menu is null and r.id_rol = '.$r->id.' order by new_db_menu.orden_menu asc';
        return DB::select($sql);*/

        if (! isset($r->rol)) {
            return self::leftjoin('new_db_menu as s', 'new_db_menu.id_menu', 's.id_pertenece_menu')
                ->where('new_db_menu.estado_menu', 1)
                ->whereNull('new_db_menu.id_pertenece_menu')
                ->orderBy('new_db_menu.orden_menu', 'asc')
                ->get([
                    'new_db_menu.id_menu',
                    'new_db_menu.nombre_menu',
                    'new_db_menu.icon_menu',
                    's.nombre_menu as nombre_submenu',
                    's.id_menu as id_submenu',
                ]);
        } else {
            $new_db_roles_usuarios = Menu::where('estado_menu', 1)->pluck('id_menu');
            $trr = implode(',', $new_db_roles_usuarios->toArray());
            $sql = 'select new_db_menu.id_menu, new_db_menu.nombre_menu, new_db_menu.icon_menu, s.nombre_menu as nombre_submenu, s.id_menu as id_submenu,r.id_submodulos_rol,
        r.id_permisos_rol,new_db_menu.id_pertenece_menu,id_submodulos_rol,r.nombre_rol
        from new_db_menu 
        inner join new_db_menu as s on new_db_menu.id_menu = s.id_pertenece_menu 
        left join new_db_roles_usuarios r on s.id_pertenece_menu in('.$trr.')
        where new_db_menu.estado_menu = 1 and new_db_menu.id_pertenece_menu is null and r.id_rol = '.$r->rol.' order by new_db_menu.orden_menu asc';
            $res = DB::select($sql);
            foreach ($res as $key => $value) {
                $menu = json_decode($value->id_permisos_rol);
                foreach ($menu as $value2) { // los menus cabeza
                    $submenu = json_decode($value->id_submodulos_rol);
                    foreach ($submenu as $value3) {
                        $idM = explode('_', $value3)[1]; // submodulo que tengo permiso

                        if ($value->id_menu == $value2 && $value->id_submenu == $idM) {
                            $letraM = explode('_', $value3)[0];
                            switch ($letraM) {
                                case 'crear':
                                    $res[$key]->crear = 1;
                                    break;
                                case 'actualizar':
                                    $res[$key]->actualizar = 1;
                                    break;
                                case 'leer':
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
            $combo = '<option value="'.$menu->id_menu.'" selected>'.$menu->nombre_menu.'</option>';
        }

        return $combo;
    }

    public static function estadoMenu($r)
    {
        $estado = 1;
        if ($r->estado == 1) {
            $estado = 0;
        }
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
                $r->o = 'Se creo el menu con id.: '.$id;
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
                $r->o = 'Se actualizo el menu con id.: '.$id;
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
            $r->d = $arrayRespuesta;
            $r->m = Menu::$modelo;
            $r->o = 'Ocurrio un error: '.$e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());

            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            self::$activeTreeRows = null;
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                // 'reload' => '',
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
        if ($id == '') {
            return Menu::orderBy('orden_menu', 'asc')->whereNull('id_pertenece_menu')->get();
        } else {
            return Menu::orderBy('id_menu', $id)->first();
        }
    }

    public static function getMenu($id = '')
    {
        $arrayMensajesGenerales = [];
        try {
            if ($id == '') {
                return self::activeTreeRows()->filter(function ($menu) {
                    return is_null($menu->id_pertenece_menu);
                })->values();
            } else {
                return Menu::where('id_menu', $id)->whereNull('id_pertenece_menu')->first();
            }
        } catch (Exception $e) {
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

    /**
     * Comparte la carga del arbol activo durante una misma solicitud.
     * El layout y el constructor recursivo del submenu dejan de consultar la
     * misma tabla por separado.
     */
    public static function activeTreeRows()
    {
        if (self::$activeTreeRows === null) {
            self::$activeTreeRows = Menu::where('estado_menu', 1)
                ->orderBy('orden_menu', 'asc')
                ->orderBy('id_menu', 'asc')
                ->get();
        }

        return self::$activeTreeRows;
    }

    public static function getMenus($term = '')
    {
        return Menu::selectRaw('id_menu as id,nombre_menu as text')
            ->where('estado_menu', 1)
            ->whereNull('id_pertenece_menu')
            ->where('nombre_menu', 'like', '%'.$term.'%')->get();
    }
}
