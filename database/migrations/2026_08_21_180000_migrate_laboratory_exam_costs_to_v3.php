<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateLaboratoryExamCostsToV3 extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('v3_laboratory_exam_costs')) {
            Schema::create('v3_laboratory_exam_costs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 20);
                $table->unsignedInteger('id_examen');
                $table->decimal('valor_reactivo', 12, 2)->default(0);
                $table->decimal('valor_implementos', 12, 2)->default(0);
                $table->string('id_usuario_creacion', 20)->nullable();
                $table->string('id_usuario_modificacion', 20)->nullable();
                $table->string('ip_creacion', 45)->nullable();
                $table->string('ip_modificacion', 45)->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'id_examen'], 'v3_exam_cost_company_exam_uq');
                $table->index('id_examen', 'v3_exam_cost_exam_idx');
            });
        }

        if (! Schema::hasTable('v3_role_menu_permissions')) {
            Schema::create('v3_role_menu_permissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 20);
                $table->unsignedInteger('id_tipo_usuario');
                $table->unsignedInteger('id_menu');
                $table->boolean('permitido')->default(false);
                $table->string('otorgado_por', 20)->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'id_tipo_usuario', 'id_menu'], 'v3_role_menu_scope_uq');
            });
        }

        if (! Schema::hasTable('v3_role_action_permissions')) {
            Schema::create('v3_role_action_permissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 20);
                $table->unsignedInteger('id_tipo_usuario');
                $table->string('modulo', 60);
                $table->string('accion', 20);
                $table->boolean('permitido')->default(false);
                $table->string('otorgado_por', 20)->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'id_tipo_usuario', 'modulo', 'accion'], 'v3_role_action_scope_uq');
                $table->index(['id_empresa', 'modulo'], 'v3_role_action_company_mod_idx');
            });
        }

        $this->preserveCompanyCosts();
        $this->migrateConfiguredRoles();
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', 675)->update(['controlador_menu' => 'costos-examenes',
                'funcion_menu' => 'index', 'icon_menu' => '<i class="fas fa-calculator nav-icon"></i>',
                'updated_at_menu' => date('Y-m-d H:i:s')]);
        }
    }

    private function preserveCompanyCosts()
    {
        if (! Schema::hasTable('empresa') || ! Schema::hasTable('examen') ||
            ! Schema::hasColumn('examen', 'valor_reactivo') || ! Schema::hasColumn('examen', 'valor_implementos')) {
            return;
        }
        $companies = DB::table('empresa')->where('estado', 1)->pluck('id')->toArray();
        $exams = DB::table('examen')->where('estado', 1)->where(function ($q) {
            $q->where('valor_reactivo', '>', 0)->orWhere('valor_implementos', '>', 0);
        })->select('id', 'valor_reactivo', 'valor_implementos')->get();
        $now = date('Y-m-d H:i:s');
        foreach ($companies as $companyId) {
            foreach ($exams as $exam) {
                if (! DB::table('v3_laboratory_exam_costs')->where('id_empresa', $companyId)->where('id_examen', $exam->id)->exists()) {
                    DB::table('v3_laboratory_exam_costs')->insert(['id_empresa' => $companyId, 'id_examen' => $exam->id,
                        'valor_reactivo' => $exam->valor_reactivo ?: 0, 'valor_implementos' => $exam->valor_implementos ?: 0,
                        'created_at' => $now, 'updated_at' => $now]);
                }
            }
        }
    }

    private function migrateConfiguredRoles()
    {
        if (! Schema::hasTable('new_db_permisos_usuario') || ! Schema::hasTable('users')) {
            return;
        }
        $configured = DB::table('new_db_permisos_usuario as p')->join('users as u', 'u.id', '=', 'p.id_usuario_permiso')
            ->whereNotNull('p.id_empresa_permiso')->select('p.id_empresa_permiso', 'p.id_submenu_permiso', 'u.id_tipo_usuario')->get();
        foreach ($configured as $item) {
            $ids = json_decode($item->id_submenu_permiso, true);
            if (! is_array($ids)) {
                continue;
            }
            foreach ([674 => 'catalogo-examenes', 675 => 'costos-examenes'] as $menuId => $module) {
                if (! in_array($menuId, array_map('intval', $ids), true)) {
                    continue;
                }
                $scope = ['id_empresa' => $item->id_empresa_permiso, 'id_tipo_usuario' => $item->id_tipo_usuario, 'id_menu' => $menuId];
                if (! DB::table('v3_role_menu_permissions')->where($scope)->exists()) {
                    DB::table('v3_role_menu_permissions')->insert(array_merge($scope, ['permitido' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]));
                }
                $actions = $menuId === 674 ? ['view', 'create', 'update', 'export'] : ['view', 'update', 'export'];
                foreach ($actions as $action) {
                    $actionScope = ['id_empresa' => $item->id_empresa_permiso, 'id_tipo_usuario' => $item->id_tipo_usuario, 'modulo' => $module, 'accion' => $action];
                    if (! DB::table('v3_role_action_permissions')->where($actionScope)->exists()) {
                        DB::table('v3_role_action_permissions')->insert(array_merge($actionScope, ['permitido' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]));
                    }
                }
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('v3_laboratory_exam_costs');
        // Los permisos por rol son infraestructura compartida; no se eliminan
        // al revertir una sola pantalla para no afectar otros módulos V3.
    }
}
