<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateClinicalAgendaToV3 extends Migration
{
    public function up()
    {
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', 660)->update([
                'icon_menu' => '<i class="fas fa-calendar-check nav-icon"></i>', 'controlador_menu' => 'agenda-clinica',
                'funcion_menu' => 'index', 'updated_at_menu' => date('Y-m-d H:i:s'),
            ]);
        }
        $this->grantUpdates();
    }

    public function down()
    {
        if (Schema::hasTable('v3_action_permissions')) {
            DB::table('v3_action_permissions')->where('modulo', 'agenda-clinica')->delete();
        }
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', 660)->update([
                'icon_menu' => '<i class="fas fa-calendar nav-icon"></i>', 'controlador_menu' => 'legacy-url',
                'funcion_menu' => 'agenda', 'updated_at_menu' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function grantUpdates()
    {
        if (! Schema::hasTable('v3_action_permissions') || ! Schema::hasTable('usuario_empresa') || ! Schema::hasTable('users')) {
            return;
        }
        $scopes = DB::table('usuario_empresa as ue')->join('users as u', 'u.id', '=', 'ue.id_usuario')
            ->whereIn('u.id_tipo_usuario', [4, 5])->select('ue.id_usuario', 'ue.id_empresa')->distinct()->get();
        $now = date('Y-m-d H:i:s');
        foreach ($scopes as $scope) {
            $exists = DB::table('v3_action_permissions')->where('id_usuario', $scope->id_usuario)->where('id_empresa', $scope->id_empresa)
                ->where('modulo', 'agenda-clinica')->where('accion', 'update')->exists();
            if (! $exists) {
                DB::table('v3_action_permissions')->insert(['id_usuario' => $scope->id_usuario, 'id_empresa' => $scope->id_empresa,
                    'modulo' => 'agenda-clinica', 'accion' => 'update', 'permitido' => 1, 'otorgado_por' => null, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }
}
