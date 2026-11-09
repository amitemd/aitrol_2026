<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigratePendingBiopsiesToV3 extends Migration
{
    public function up()
    {
        if (Schema::hasTable('paciente_biopsia')) {
            Schema::table('paciente_biopsia', function (Blueprint $table) {
                if (! Schema::hasColumn('paciente_biopsia', 'id_empresa')) {
                    $table->string('id_empresa', 20)->nullable()->index('pb_empresa_idx');
                }
                if (! Schema::hasColumn('paciente_biopsia', 'nombre_original')) {
                    $table->string('nombre_original')->nullable();
                }
                if (! Schema::hasColumn('paciente_biopsia', 'archivo_hash')) {
                    $table->string('archivo_hash', 64)->nullable()->index('pb_hash_idx');
                }
            });
        }
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', 644)->update([
                'icon_menu' => '<i class="fas fa-microscope nav-icon"></i>',
                'controlador_menu' => 'biopsias-faltantes', 'funcion_menu' => 'index',
                'updated_at_menu' => date('Y-m-d H:i:s'),
            ]);
        }
        $this->grantLegacyRolePermissions();
    }

    public function down()
    {
        if (Schema::hasTable('v3_action_permissions')) {
            DB::table('v3_action_permissions')->where('modulo', 'biopsias-faltantes')->delete();
        }
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', 644)->update([
                'icon_menu' => '<i class="far fa-circle nav-icon"></i>',
                'controlador_menu' => 'legacy-route', 'funcion_menu' => 'biopsias_paciente.index',
                'updated_at_menu' => date('Y-m-d H:i:s'),
            ]);
        }
        if (Schema::hasTable('paciente_biopsia')) {
            Schema::table('paciente_biopsia', function (Blueprint $table) {
                if (Schema::hasColumn('paciente_biopsia', 'archivo_hash')) {
                    $table->dropIndex('pb_hash_idx');
                    $table->dropColumn('archivo_hash');
                }
                if (Schema::hasColumn('paciente_biopsia', 'nombre_original')) {
                    $table->dropColumn('nombre_original');
                }
                if (Schema::hasColumn('paciente_biopsia', 'id_empresa')) {
                    $table->dropIndex('pb_empresa_idx');
                    $table->dropColumn('id_empresa');
                }
            });
        }
    }

    private function grantLegacyRolePermissions()
    {
        if (! Schema::hasTable('v3_action_permissions') || ! Schema::hasTable('usuario_empresa') || ! Schema::hasTable('users')) {
            return;
        }
        $scopes = DB::table('usuario_empresa as ue')->join('users as u', 'u.id', '=', 'ue.id_usuario')
            ->whereIn('u.id_tipo_usuario', [4, 5, 20])->select('ue.id_usuario', 'ue.id_empresa')->distinct()->get();
        $now = date('Y-m-d H:i:s');
        foreach ($scopes as $scope) {
            foreach (['create', 'delete', 'export'] as $action) {
                $exists = DB::table('v3_action_permissions')->where('id_usuario', $scope->id_usuario)
                    ->where('id_empresa', $scope->id_empresa)->where('modulo', 'biopsias-faltantes')->where('accion', $action)->exists();
                if (! $exists) {
                    DB::table('v3_action_permissions')->insert([
                        'id_usuario' => $scope->id_usuario, 'id_empresa' => $scope->id_empresa,
                        'modulo' => 'biopsias-faltantes', 'accion' => $action, 'permitido' => 1,
                        'otorgado_por' => null, 'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
            }
        }
    }
}
