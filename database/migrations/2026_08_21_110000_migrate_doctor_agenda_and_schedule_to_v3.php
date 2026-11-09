<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateDoctorAgendaAndScheduleToV3 extends Migration
{
    public function up()
    {
        if (Schema::hasTable('new_db_menu')) {
            $now = date('Y-m-d H:i:s');
            DB::table('new_db_menu')->where('id_menu', 661)->update(['icon_menu' => '<i class="fas fa-calendar-day nav-icon"></i>',
                'controlador_menu' => 'agenda-clinica', 'funcion_menu' => 'mi-agenda', 'updated_at_menu' => $now]);
            DB::table('new_db_menu')->where('id_menu', 662)->update(['icon_menu' => '<i class="far fa-clock nav-icon"></i>',
                'controlador_menu' => 'agenda-clinica', 'funcion_menu' => 'mi-horario', 'updated_at_menu' => $now]);
            DB::table('new_db_menu')->where('id_menu', 665)->update(['icon_menu' => '<i class="fas fa-user-clock nav-icon"></i>',
                'controlador_menu' => 'agenda-clinica', 'funcion_menu' => 'index', 'updated_at_menu' => $now]);
        }
        $this->grantMutations();
    }

    public function down()
    {
        if (Schema::hasTable('new_db_menu')) {
            $now = date('Y-m-d H:i:s');
            DB::table('new_db_menu')->where('id_menu', 661)->update(['icon_menu' => '<i class="far fa-circle nav-icon"></i>', 'controlador_menu' => 'legacy-route', 'funcion_menu' => 'agenda.agenda2', 'updated_at_menu' => $now]);
            DB::table('new_db_menu')->where('id_menu', 662)->update(['icon_menu' => '<i class="far fa-circle nav-icon"></i>', 'controlador_menu' => 'legacy-route', 'funcion_menu' => 'horario.index', 'updated_at_menu' => $now]);
            DB::table('new_db_menu')->where('id_menu', 665)->update(['icon_menu' => '<i class="far fa-circle nav-icon"></i>', 'controlador_menu' => 'legacy-route', 'funcion_menu' => 'horario.index_admin', 'updated_at_menu' => $now]);
        }
        if (Schema::hasTable('v3_action_permissions')) {
            DB::table('v3_action_permissions')->where('modulo', 'agenda-clinica')->where('accion', 'update')->whereIn('id_usuario', function ($query) {
                $query->select('id')->from('users')->where('id_tipo_usuario', 3);
            })->delete();
        }
    }

    private function grantMutations()
    {
        if (! Schema::hasTable('v3_action_permissions') || ! Schema::hasTable('usuario_empresa') || ! Schema::hasTable('users')) {
            return;
        }
        $scopes = DB::table('usuario_empresa as ue')->join('users as u', 'u.id', '=', 'ue.id_usuario')
            ->whereIn('u.id_tipo_usuario', [3, 4, 5])->where('u.estado', 1)->select('ue.id_usuario', 'ue.id_empresa')->distinct()->get();
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
