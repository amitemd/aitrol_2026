<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateClinicalOperationsToV3 extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        $this->updateMenu(647, '<i class="fas fa-heartbeat nav-icon"></i>', 'pacientes-enfermeria', 'index');
        $this->updateMenu(648, '<i class="fas fa-notes-medical nav-icon"></i>', 'pacientes-dia', 'index');
        $this->updateMenu(649, '<i class="fas fa-exclamation-triangle nav-icon"></i>', 'riesgo-caida', 'index');
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        $this->updateMenu(647, '<i class="far fa-circle nav-icon"></i>', 'legacy-route', 'enfermeria.index');
        $this->updateMenu(648, '<i class="far fa-circle nav-icon"></i>', 'legacy-route', 'historia_clinica.fullcontrol');
        $this->updateMenu(649, '<i class="far fa-circle nav-icon"></i>', 'legacy-route', 'camilla.index');
    }

    private function updateMenu($id, $icon, $controller, $function)
    {
        DB::table('new_db_menu')->where('id_menu', $id)->update([
            'icon_menu' => $icon, 'controlador_menu' => $controller,
            'funcion_menu' => $function, 'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }
}
