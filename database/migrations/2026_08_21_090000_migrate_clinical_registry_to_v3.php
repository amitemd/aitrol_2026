<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateClinicalRegistryToV3 extends Migration
{
    public function up()
    {
        if (Schema::hasTable('new_db_menu')) {
            $this->menu(650, '<i class="fas fa-flask nav-icon"></i>', 'historial-laboratorio', 'index');
            $this->menu(651, '<i class="fas fa-clipboard-list nav-icon"></i>', 'historial-ordenes', 'index');
            $this->menu(652, '<i class="fas fa-calendar-alt nav-icon"></i>', 'disponibilidad-consultorios', 'index');
            $this->menu(654, '<i class="fas fa-microscope nav-icon"></i>', 'resultado-biopsias', 'index');
        }
        $this->grantBiopsyExport();
    }

    public function down()
    {
        if (Schema::hasTable('v3_action_permissions')) {
            DB::table('v3_action_permissions')->where('modulo', 'resultado-biopsias')->where('accion', 'export')->delete();
        }
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        $this->menu(650, '<i class="far fa-circle nav-icon"></i>', 'legacy-route', 'paciente.historial_orden_lab_paciente');
        $this->menu(651, '<i class="far fa-circle nav-icon"></i>', 'legacy-route', 'paciente.historial_examenes');
        $this->menu(652, '<i class="far fa-circle nav-icon"></i>', 'legacy-route', 'disponibilidad.disponibilidad_menu');
        $this->menu(654, '<i class="far fa-circle nav-icon"></i>', 'legacy-route', 'consultam.reporte_biopsias');
    }

    private function menu($id, $icon, $controller, $function)
    {
        DB::table('new_db_menu')->where('id_menu', $id)->update([
            'icon_menu' => $icon, 'controlador_menu' => $controller, 'funcion_menu' => $function,
            'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }

    private function grantBiopsyExport()
    {
        if (! Schema::hasTable('v3_action_permissions') || ! Schema::hasTable('usuario_empresa') || ! Schema::hasTable('users')) {
            return;
        }
        $scopes = DB::table('usuario_empresa as ue')->join('users as u', 'u.id', '=', 'ue.id_usuario')
            ->whereIn('u.id_tipo_usuario', [4, 5, 20])->select('ue.id_usuario', 'ue.id_empresa')->distinct()->get();
        $now = date('Y-m-d H:i:s');
        foreach ($scopes as $scope) {
            $exists = DB::table('v3_action_permissions')->where('id_usuario', $scope->id_usuario)->where('id_empresa', $scope->id_empresa)
                ->where('modulo', 'resultado-biopsias')->where('accion', 'export')->exists();
            if (! $exists) {
                DB::table('v3_action_permissions')->insert([
                    'id_usuario' => $scope->id_usuario, 'id_empresa' => $scope->id_empresa, 'modulo' => 'resultado-biopsias',
                    'accion' => 'export', 'permitido' => 1, 'otorgado_por' => null, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }
}
