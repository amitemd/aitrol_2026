<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateV3AccountingGovernance extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('v3_accounting_periods')) {
            Schema::create('v3_accounting_periods', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 20);
                $table->unsignedSmallInteger('anio');
                $table->unsignedTinyInteger('mes');
                $table->string('estado', 20)->default('OPEN');
                $table->unsignedInteger('version')->default(1);
                $table->string('preparado_por', 20)->nullable();
                $table->dateTime('preparado_at')->nullable();
                $table->string('aprobado_por', 20)->nullable();
                $table->dateTime('aprobado_at')->nullable();
                $table->string('cerrado_por', 20)->nullable();
                $table->dateTime('cerrado_at')->nullable();
                $table->unsignedBigInteger('certificacion_id')->nullable();
                $table->string('reabierto_por', 20)->nullable();
                $table->dateTime('reabierto_at')->nullable();
                $table->text('motivo_reapertura')->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'anio', 'mes'], 'v3_acc_period_uq');
                $table->index(['id_empresa', 'estado'], 'v3_acc_period_state_idx');
                $table->index('certificacion_id', 'v3_acc_period_cert_idx');
            });
        }

        if (! Schema::hasTable('v3_accounting_posting_sources')) {
            Schema::create('v3_accounting_posting_sources', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 20);
                $table->string('modulo', 60);
                $table->string('tipo_origen', 80);
                $table->string('id_origen', 100);
                $table->string('identity_hash', 64);
                $table->unsignedBigInteger('id_asiento')->nullable();
                $table->string('payload_hash', 64);
                $table->string('estado', 20)->default('RESERVED');
                $table->string('creado_por', 20)->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'identity_hash'], 'v3_acc_post_source_uq');
                $table->index('id_asiento', 'v3_acc_post_entry_idx');
            });
        }

        if (! Schema::hasTable('v3_accounting_aux_reconciliations')) {
            Schema::create('v3_accounting_aux_reconciliations', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 20);
                $table->unsignedSmallInteger('anio');
                $table->unsignedTinyInteger('mes');
                $table->string('tipo_auxiliar', 30);
                $table->string('id_plan_cuenta', 40);
                $table->decimal('saldo_auxiliar', 18, 2)->default(0);
                $table->decimal('saldo_mayor', 18, 2)->default(0);
                $table->decimal('diferencia', 18, 2)->default(0);
                $table->string('estado', 20)->default('PENDING');
                $table->text('evidencia')->nullable();
                $table->string('preparado_por', 20)->nullable();
                $table->string('aprobado_por', 20)->nullable();
                $table->dateTime('aprobado_at')->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'anio', 'mes', 'tipo_auxiliar', 'id_plan_cuenta'], 'v3_acc_aux_uq');
                $table->index(['id_empresa', 'anio', 'mes', 'estado'], 'v3_acc_aux_state_idx');
            });
        }

        if (! Schema::hasTable('v3_accounting_certifications')) {
            Schema::create('v3_accounting_certifications', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 20);
                $table->date('desde');
                $table->date('hasta');
                $table->string('estado', 20);
                $table->string('snapshot_hash', 64);
                $table->longText('snapshot');
                $table->string('certificado_por', 20)->nullable();
                $table->dateTime('certificado_at')->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'desde', 'hasta', 'snapshot_hash'], 'v3_acc_cert_snapshot_uq');
                $table->index(['id_empresa', 'estado'], 'v3_acc_cert_state_idx');
            });
        }

        if (! Schema::hasTable('v3_accounting_incidents')) {
            Schema::create('v3_accounting_incidents', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 20)->nullable();
                $table->string('codigo', 80);
                $table->string('fingerprint', 64);
                $table->string('severidad', 20)->default('MEDIUM');
                $table->string('categoria', 30)->default('DATA');
                $table->string('estado', 20)->default('OPEN');
                $table->string('titulo', 190);
                $table->text('descripcion')->nullable();
                $table->longText('contexto')->nullable();
                $table->text('diagnostico')->nullable();
                $table->text('accion_sugerida')->nullable();
                $table->string('reparacion', 80)->nullable();
                $table->string('reparacion_estado', 20)->default('NOT_APPLICABLE');
                $table->unsignedInteger('ocurrencias')->default(1);
                $table->dateTime('ultima_ocurrencia_at')->nullable();
                $table->string('resuelto_por', 20)->nullable();
                $table->dateTime('resuelto_at')->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'fingerprint'], 'v3_acc_incident_fingerprint_uq');
                $table->index(['id_empresa', 'estado', 'severidad'], 'v3_acc_incident_state_idx');
            });
        }

        if (! Schema::hasTable('v3_accounting_repairs')) {
            Schema::create('v3_accounting_repairs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('incidencia_id');
                $table->string('accion', 80);
                $table->string('estado', 20);
                $table->longText('antes')->nullable();
                $table->longText('despues')->nullable();
                $table->text('resultado')->nullable();
                $table->string('ejecutado_por', 20)->nullable();
                $table->timestamps();
                $table->index('incidencia_id', 'v3_acc_repair_incident_idx');
            });
        }

        if (Schema::hasTable('new_db_menu')) {
            $menu = ['nombre_menu' => 'Salud contable', 'icon_menu' => '<i class="nav-icon fas fa-shield-alt"></i>',
                'controlador_menu' => 'contabilidad', 'funcion_menu' => 'salud', 'id_usuario_creacion_menu' => env('BOOTSTRAP_SYSTEMS_ID', '0000000001'),
                'id_usuario_modificacion_menu' => env('BOOTSTRAP_SYSTEMS_ID', '0000000001'), 'created_at_menu' => date('Y-m-d H:i:s'),
                'updated_at_menu' => date('Y-m-d H:i:s'), 'ip_creacion_menu' => '127.0.0.1', 'ip_modificacion_menu' => '127.0.0.1',
                'orden_menu' => 20, 'estado_menu' => 1, 'id_pertenece_menu' => 563, 'color_menu' => null];
            if (DB::table('new_db_menu')->where('id_menu', 682)->exists()) {
                DB::table('new_db_menu')->where('id_menu', 682)->update($menu);
            } else {
                DB::table('new_db_menu')->insert(array_merge(['id_menu' => 682], $menu));
            }
            if (Schema::hasTable('new_db_permisos_usuario')) {
                $systems = env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
                foreach (DB::table('new_db_permisos_usuario')->where('id_usuario_permiso', $systems)->get() as $permission) {
                    $ids = json_decode($permission->id_submenu_permiso, true);
                    $ids = is_array($ids) ? array_map('intval', $ids) : [];
                    if (! in_array(682, $ids, true)) {
                        $ids[] = 682;
                        sort($ids);
                        DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update(['id_submenu_permiso' => json_encode($ids)]);
                    }
                }
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('new_db_permisos_usuario')) {
            foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                $ids = json_decode($permission->id_submenu_permiso, true);
                if (! is_array($ids)) {
                    continue;
                }
                $ids = array_values(array_filter(array_map('intval', $ids), function ($id) {
                    return $id !== 682;
                }));
                DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)
                    ->update(['id_submenu_permiso' => json_encode($ids)]);
            }
        }
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', 682)->delete();
        }
        Schema::dropIfExists('v3_accounting_repairs');
        Schema::dropIfExists('v3_accounting_incidents');
        Schema::dropIfExists('v3_accounting_certifications');
        Schema::dropIfExists('v3_accounting_aux_reconciliations');
        Schema::dropIfExists('v3_accounting_posting_sources');
        Schema::dropIfExists('v3_accounting_periods');
    }
}
