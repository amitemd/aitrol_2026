<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateLaboratoryAdministrationToV3 extends Migration
{
    private $menus = [
        676 => ['module' => 'protocolos-laboratorio', 'actions' => ['view', 'create', 'update']],
        677 => ['module' => 'agrupadores-examenes', 'actions' => ['view', 'create', 'update']],
        678 => ['module' => 'agenda-laboratorio', 'actions' => ['view']],
        679 => ['module' => 'facturas-agrupadas-laboratorio', 'actions' => ['view', 'create', 'update', 'export']],
        680 => ['module' => 'estadisticas-laboratorio', 'actions' => ['view', 'export']],
        681 => ['module' => 'parametros-historicos-laboratorio', 'actions' => ['view', 'create', 'delete']],
    ];

    public function up()
    {
        $this->addCompanyScope();
        $this->migrateConfiguredRoles();
        if (Schema::hasTable('new_db_menu')) {
            $icons = [676 => 'fas fa-clipboard-list', 677 => 'fas fa-layer-group', 678 => 'fas fa-calendar-check', 679 => 'fas fa-file-invoice-dollar', 680 => 'fas fa-chart-bar', 681 => 'fas fa-history'];
            foreach ($this->menus as $menuId => $config) {
                DB::table('new_db_menu')->where('id_menu', $menuId)->update([
                    'controlador_menu' => $config['module'], 'funcion_menu' => 'index', 'icon_menu' => '<i class="'.$icons[$menuId].' nav-icon"></i>', 'updated_at_menu' => date('Y-m-d H:i:s')]);
            }
        }
    }

    private function addCompanyScope()
    {
        if (! Schema::hasTable('labs_factura_agrupada_cab')) {
            return;
        }
        if (! Schema::hasColumn('labs_factura_agrupada_cab', 'id_empresa')) {
            Schema::table('labs_factura_agrupada_cab', function (Blueprint $table) {
                $table->string('id_empresa', 20)->nullable()->after('id');
                $table->index('id_empresa', 'lfac_empresa_idx');
            });
        }
        if (! Schema::hasTable('usuario_empresa')) {
            return;
        }
        $rows = DB::table('labs_factura_agrupada_cab')->whereNull('id_empresa')->select('id', 'id_usuariocrea')->get();
        foreach ($rows as $row) {
            $company = DB::table('usuario_empresa')->where('id_usuario', $row->id_usuariocrea)->orderBy('id_empresa')->value('id_empresa');
            if ($company) {
                DB::table('labs_factura_agrupada_cab')->where('id', $row->id)->update(['id_empresa' => $company]);
            }
        }
    }

    private function migrateConfiguredRoles()
    {
        if (! Schema::hasTable('v3_role_menu_permissions') || ! Schema::hasTable('v3_role_action_permissions') || ! Schema::hasTable('new_db_permisos_usuario') || ! Schema::hasTable('users')) {
            return;
        }
        $configured = DB::table('new_db_permisos_usuario as p')->join('users as u', 'u.id', '=', 'p.id_usuario_permiso')->whereNotNull('p.id_empresa_permiso')->select('p.id_empresa_permiso', 'p.id_submenu_permiso', 'u.id_tipo_usuario')->get();
        $now = date('Y-m-d H:i:s');
        foreach ($configured as $item) {
            $ids = json_decode($item->id_submenu_permiso, true);
            if (! is_array($ids)) {
                continue;
            }$ids = array_map('intval', $ids);
            foreach ($this->menus as $menuId => $config) {
                if (! in_array($menuId, $ids, true)) {
                    continue;
                }$scope = ['id_empresa' => $item->id_empresa_permiso, 'id_tipo_usuario' => $item->id_tipo_usuario, 'id_menu' => $menuId];
                if (! DB::table('v3_role_menu_permissions')->where($scope)->exists()) {
                    DB::table('v3_role_menu_permissions')->insert(array_merge($scope, ['permitido' => 1, 'created_at' => $now, 'updated_at' => $now]));
                }foreach ($config['actions'] as $action) {
                    $actionScope = ['id_empresa' => $item->id_empresa_permiso, 'id_tipo_usuario' => $item->id_tipo_usuario, 'modulo' => $config['module'], 'accion' => $action];
                    if (! DB::table('v3_role_action_permissions')->where($actionScope)->exists()) {
                        DB::table('v3_role_action_permissions')->insert(array_merge($actionScope, ['permitido' => 1, 'created_at' => $now, 'updated_at' => $now]));
                    }
                }
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('labs_factura_agrupada_cab') && Schema::hasColumn('labs_factura_agrupada_cab', 'id_empresa')) {
            Schema::table('labs_factura_agrupada_cab', function (Blueprint $table) {
                $table->dropIndex('lfac_empresa_idx');
                $table->dropColumn('id_empresa');
            });
        }
    }
}
