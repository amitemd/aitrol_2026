<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompleteV3ReconciliationCycle extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ct_conciliacion_mes')) {
            Schema::table('ct_conciliacion_mes', function (Blueprint $table) {
                if (! Schema::hasColumn('ct_conciliacion_mes', 'estado_flujo')) {
                    $table->string('estado_flujo', 20)->nullable();
                }
                if (! Schema::hasColumn('ct_conciliacion_mes', 'aprobado_por')) {
                    $table->string('aprobado_por', 50)->nullable();
                }
                if (! Schema::hasColumn('ct_conciliacion_mes', 'aprobado_at')) {
                    $table->dateTime('aprobado_at')->nullable();
                }
                if (! Schema::hasColumn('ct_conciliacion_mes', 'motivo_rechazo')) {
                    $table->string('motivo_rechazo', 500)->nullable();
                }
                if (! Schema::hasColumn('ct_conciliacion_mes', 'rechazado_por')) {
                    $table->string('rechazado_por', 50)->nullable();
                }
                if (! Schema::hasColumn('ct_conciliacion_mes', 'rechazado_at')) {
                    $table->dateTime('rechazado_at')->nullable();
                }
                if (! Schema::hasColumn('ct_conciliacion_mes', 'motivo_sin_extracto')) {
                    $table->string('motivo_sin_extracto', 500)->nullable();
                }
            });
            DB::statement('ALTER TABLE ct_conciliacion_mes MODIFY preparado_por VARCHAR(50) NULL, MODIFY reabierto_por VARCHAR(50) NULL');
            DB::table('ct_conciliacion_mes')->whereNull('estado_flujo')->where('estado', 1)->update(['estado_flujo' => 'APPROVED']);
            DB::table('ct_conciliacion_mes')->whereNull('estado_flujo')->update(['estado_flujo' => 'REOPENED']);
            DB::statement("UPDATE ct_conciliacion_mes SET aprobado_por = COALESCE(id_usuariomod, id_usuariocrea), aprobado_at = COALESCE(updated_at, created_at), motivo_sin_extracto = 'Cierre historico anterior al control de extractos V3-11' WHERE estado_flujo = 'APPROVED' AND aprobado_por IS NULL");
        }
        if (Schema::hasTable('ct_conciliacion_bancaria')) {
            Schema::table('ct_conciliacion_bancaria', function (Blueprint $table) {
                if (! Schema::hasColumn('ct_conciliacion_bancaria', 'naturaleza')) {
                    $table->string('naturaleza', 1)->nullable();
                }
                if (! Schema::hasColumn('ct_conciliacion_bancaria', 'fecha_movimiento')) {
                    $table->date('fecha_movimiento')->nullable();
                }
            });
            DB::statement('ALTER TABLE ct_conciliacion_bancaria MODIFY cerrado_por VARCHAR(50) NULL');
            DB::statement("UPDATE ct_conciliacion_bancaria SET naturaleza = CASE WHEN UPPER(tipo) IN ('EG','EGV','EGM','ND','NDB','DB','DEBITO') THEN 'D' ELSE 'C' END WHERE naturaleza IS NULL");
            if ($this->indexExists('ct_conciliacion_bancaria', 'v3_con_line_scope_uq')) {
                Schema::table('ct_conciliacion_bancaria', function (Blueprint $table) {
                    $table->dropUnique('v3_con_line_scope_uq');
                });
            }
            if (! $this->indexExists('ct_conciliacion_bancaria', 'v3_con_line_leg_uq')) {
                Schema::table('ct_conciliacion_bancaria', function (Blueprint $table) {
                    $table->unique(['empresa', 'id_caja_banco', 'anio', 'mes', 'tipo', 'id_tipo', 'naturaleza'], 'v3_con_line_leg_uq');
                });
            }
        }
        if (! Schema::hasTable('ct_conciliacion_extracto')) {
            Schema::create('ct_conciliacion_extracto', function (Blueprint $table) {
                $table->increments('id');
                $table->string('id_empresa', 30);
                $table->unsignedInteger('id_caja_banco');
                $table->unsignedSmallInteger('anio');
                $table->unsignedTinyInteger('mes');
                $table->string('formato', 8);
                $table->string('nombre_original', 255);
                $table->string('nombre_guardado', 255);
                $table->char('archivo_hash', 64);
                $table->unsignedInteger('total_lineas')->default(0);
                $table->decimal('total_debitos', 18, 2)->default(0);
                $table->decimal('total_creditos', 18, 2)->default(0);
                $table->string('estado', 20)->default('IMPORTED');
                $table->string('importado_por', 50);
                $table->timestamps();
                $table->unique(['id_empresa', 'id_caja_banco', 'anio', 'mes'], 'v3_stmt_period_uq');
                $table->index('archivo_hash', 'v3_stmt_hash_ix');
            });
        }
        if (! Schema::hasTable('ct_conciliacion_extracto_linea')) {
            Schema::create('ct_conciliacion_extracto_linea', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('extracto_id');
                $table->unsignedInteger('numero_linea');
                $table->date('fecha');
                $table->string('referencia', 150)->nullable();
                $table->string('detalle', 500)->nullable();
                $table->decimal('monto', 18, 2);
                $table->decimal('saldo', 18, 2)->nullable();
                $table->char('fingerprint', 64);
                $table->string('estado', 20)->default('UNMATCHED');
                $table->unsignedInteger('conciliacion_id')->nullable();
                $table->decimal('confianza', 5, 2)->nullable();
                $table->string('emparejado_por', 50)->nullable();
                $table->dateTime('emparejado_at')->nullable();
                $table->string('motivo_ignorado', 500)->nullable();
                $table->timestamps();
                $table->unique(['extracto_id', 'fingerprint'], 'v3_stmt_line_uq');
                $table->unique('conciliacion_id', 'v3_stmt_match_uq');
                $table->index(['extracto_id', 'estado', 'fecha', 'monto'], 'v3_stmt_match_ix');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ct_conciliacion_extracto_linea');
        Schema::dropIfExists('ct_conciliacion_extracto');
    }

    private function indexExists($table, $index)
    {
        return count(DB::select('SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1', [$table, $index])) > 0;
    }
}
