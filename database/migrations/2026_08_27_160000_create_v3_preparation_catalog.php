<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateV3PreparationCatalog extends Migration
{
    // Los IDs 684-701 ya están reservados por migraciones anteriores.
    const MENU_ID = 9001;

    const PARENT_ID = 698;

    public function up()
    {
        if (! Schema::hasColumn('preparaciones', 'id_empresa')) {
            Schema::table('preparaciones', function (Blueprint $table) {
                $table->string('id_empresa', 13)->nullable()->after('id');
                $table->string('nombre_archivo_original')->nullable()->after('archivo_preparaciones');
                $table->index(['id_empresa', 'estado'], 'prep_empresa_estado_idx');
            });
        }

        $this->seedCatalog();
        $this->addMenu();
    }

    public function down()
    {
        if (Schema::hasTable('new_db_permisos_usuario')) {
            foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                $ids = json_decode($permission->id_submenu_permiso, true);
                $ids = is_array($ids) ? array_values(array_diff(array_map('intval', $ids), [self::MENU_ID])) : [];
                DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)
                    ->update(['id_submenu_permiso' => json_encode($ids)]);
            }
        }
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->delete();
        }
        if (Schema::hasColumn('preparaciones', 'id_empresa')) {
            Schema::table('preparaciones', function (Blueprint $table) {
                $table->dropIndex('prep_empresa_estado_idx');
                $table->dropColumn(['id_empresa', 'nombre_archivo_original']);
            });
        }
    }

    private function seedCatalog()
    {
        if (! Schema::hasTable('empresa') || ! Schema::hasTable('preparaciones')) {
            return;
        }
        $names = [
            'Endoscopía', 'Ecoendoscopia Diagnóstica Con Punción',
            'TRAVADPIK Oral - después de las 12H00', 'TRAVADPIK Oral - hasta las 12H00',
            'Balón Gástrico - Retiro', 'POEM', 'PH- Metría e Impedanciometría de 24 horas',
            'Colonoscopía con Nulytely', 'Manometría Rectal', 'Manometría Esofágica',
            'Ligadura de Várices Esofágicas', 'IZINOVA después de las 12H00',
            'IZINOVA hasta las 12H00', 'Fibroscan Elastografía Ecografía', 'Cyanocrilato ECO COILS',
            'Colangiopancreatografía Retrógrada Endoscópica (CPRE)', 'Balón Gástrico - Colocación',
            'Cápsula Endoscópica', 'Broncoscopía', 'Colonoscopía con Neolax', 'Espirometría',
            'IZINOVA después de las 12H00 - Endoscopía y Colonoscopía',
            'IZINOVA hasta las 12H00 - Endoscopía y Colonoscopía',
            'TRAVADPIK DESPUÉS 12H00 colono',
            'TRAVADPIK Oral hasta las 12H00 - Endoscopía y Colonoscopía',
            'Test de ejercicio pulmonar menos de un minuto', 'Manga Gástrica Endoscópica',
        ];
        $actor = DB::table('users')->where('id_tipo_usuario', 1)->value('id');
        if (! $actor) {
            $actor = DB::table('users')->value('id');
        }
        if (! $actor) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        foreach (DB::table('empresa')->where('estado', 1)->pluck('id') as $company) {
            foreach ($names as $name) {
                if (DB::table('preparaciones')->where('id_empresa', $company)->where('nombre_preparaciones', $name)->exists()) {
                    continue;
                }
                DB::table('preparaciones')->insert([
                    'id_empresa' => $company, 'nombre_preparaciones' => $name,
                    'archivo_preparaciones' => null, 'nombre_archivo_original' => null, 'estado' => 1,
                    'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
                    'ip_creacion' => '127.0.0.1', 'ip_modificacion' => '127.0.0.1',
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    private function addMenu()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        $actor = DB::table('users')->where('id_tipo_usuario', 1)->value('id') ?: DB::table('users')->value('id');
        if (! $actor) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        if (! DB::table('new_db_menu')->where('id_menu', self::PARENT_ID)->exists()) {
            DB::table('new_db_menu')->insert([
                'id_menu' => self::PARENT_ID, 'nombre_menu' => 'Configuración clínica',
                'icon_menu' => '<i class="fas fa-clinic-medical nav-icon"></i>',
                'controlador_menu' => null, 'funcion_menu' => null,
                'id_usuario_creacion_menu' => $actor, 'id_usuario_modificacion_menu' => $actor,
                'created_at_menu' => $now, 'updated_at_menu' => $now,
                'ip_creacion_menu' => '127.0.0.1', 'ip_modificacion_menu' => '127.0.0.1',
                'orden_menu' => 20, 'estado_menu' => 1, 'id_pertenece_menu' => 10, 'color_menu' => null,
            ]);
        }
        $data = [
            'nombre_menu' => 'Preparaciones médicas', 'icon_menu' => '<i class="fas fa-file-pdf nav-icon"></i>',
            'controlador_menu' => 'preparaciones', 'funcion_menu' => 'index',
            'id_usuario_modificacion_menu' => $actor, 'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1', 'orden_menu' => 35,
            'estado_menu' => 1, 'id_pertenece_menu' => self::PARENT_ID, 'color_menu' => null,
        ];
        if (DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->exists()) {
            DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->update($data);
        } else {
            DB::table('new_db_menu')->insert(array_merge($data, [
                'id_menu' => self::MENU_ID, 'id_usuario_creacion_menu' => $actor,
                'created_at_menu' => $now, 'ip_creacion_menu' => '127.0.0.1',
            ]));
        }
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }
        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            $hasClinicalCatalog = count(array_intersect($ids, [684, 687, 688, 694, 695])) > 0;
            if (! in_array(self::PARENT_ID, $ids, true) && ! $hasClinicalCatalog) {
                continue;
            }
            $ids[] = self::PARENT_ID;
            $ids[] = self::MENU_ID;
            $ids = array_values(array_unique($ids));
            sort($ids);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update([
                'id_submenu_permiso' => json_encode($ids), 'updated_at_permiso' => $now,
            ]);
        }
    }
}
