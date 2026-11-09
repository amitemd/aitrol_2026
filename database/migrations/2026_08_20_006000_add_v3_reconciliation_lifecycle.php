<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddV3ReconciliationLifecycle extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ct_conciliacion_mes')) {
            Schema::table('ct_conciliacion_mes', function (Blueprint $table) {
                if (! Schema::hasColumn('ct_conciliacion_mes', 'motivo_reapertura')) {
                    $table->string('motivo_reapertura', 500)->nullable();
                }
                if (! Schema::hasColumn('ct_conciliacion_mes', 'reabierto_por')) {
                    $table->string('reabierto_por', 50)->nullable();
                }
                if (! Schema::hasColumn('ct_conciliacion_mes', 'reabierto_at')) {
                    $table->dateTime('reabierto_at')->nullable();
                }
                if (! Schema::hasColumn('ct_conciliacion_mes', 'preparado_por')) {
                    $table->string('preparado_por', 50)->nullable();
                }
                if (! Schema::hasColumn('ct_conciliacion_mes', 'preparado_at')) {
                    $table->dateTime('preparado_at')->nullable();
                }
            });
        }
        if (Schema::hasTable('ct_conciliacion_bancaria')) {
            Schema::table('ct_conciliacion_bancaria', function (Blueprint $table) {
                if (! Schema::hasColumn('ct_conciliacion_bancaria', 'cerrado_por')) {
                    $table->string('cerrado_por', 50)->nullable();
                }
                if (! Schema::hasColumn('ct_conciliacion_bancaria', 'cerrado_at')) {
                    $table->dateTime('cerrado_at')->nullable();
                }
            });
            if (! $this->indexExists('ct_conciliacion_bancaria', 'v3_con_source_state_ix')) {
                Schema::table('ct_conciliacion_bancaria', function (Blueprint $table) {
                    $table->index(['empresa', 'id_caja_banco', 'tipo', 'id_tipo', 'estado'], 'v3_con_source_state_ix');
                });
            }
        }
        if (Schema::hasTable('ct_conciliacion_pendientes')) {
            Schema::table('ct_conciliacion_pendientes', function (Blueprint $table) {
                if (! Schema::hasColumn('ct_conciliacion_pendientes', 'identity_key')) {
                    $table->string('identity_key', 64)->nullable();
                }
                if (! Schema::hasColumn('ct_conciliacion_pendientes', 'duplicado_de')) {
                    $table->unsignedInteger('duplicado_de')->nullable();
                }
            });
            // Conserva todos los registros históricos: solo el primero de cada identidad se vuelve canónico.
            $grupos = DB::table('ct_conciliacion_pendientes')
                ->select('id_empresa', 'id_caja_banco', 'id_concilia', DB::raw('MIN(id) as canonical_id'))
                ->whereNotNull('id_concilia')->groupBy('id_empresa', 'id_caja_banco', 'id_concilia')->get();
            foreach ($grupos as $grupo) {
                $identity = hash('sha256', $grupo->id_empresa.'|'.$grupo->id_caja_banco.'|'.$grupo->id_concilia);
                DB::table('ct_conciliacion_pendientes')->where('id', $grupo->canonical_id)->update(['identity_key' => $identity]);
                DB::table('ct_conciliacion_pendientes')->where('id_empresa', $grupo->id_empresa)
                    ->where('id_caja_banco', $grupo->id_caja_banco)->where('id_concilia', $grupo->id_concilia)
                    ->where('id', '!=', $grupo->canonical_id)->update(['duplicado_de' => $grupo->canonical_id, 'identity_key' => null]);
            }
            if (! $this->indexExists('ct_conciliacion_pendientes', 'v3_con_pending_identity_uq')) {
                Schema::table('ct_conciliacion_pendientes', function (Blueprint $table) {
                    $table->unique(['id_empresa', 'id_caja_banco', 'identity_key'], 'v3_con_pending_identity_uq');
                });
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('ct_conciliacion_pendientes') && $this->indexExists('ct_conciliacion_pendientes', 'v3_con_pending_identity_uq')) {
            Schema::table('ct_conciliacion_pendientes', function (Blueprint $table) {
                $table->dropUnique('v3_con_pending_identity_uq');
            });
        }
        if (Schema::hasTable('ct_conciliacion_bancaria') && $this->indexExists('ct_conciliacion_bancaria', 'v3_con_source_state_ix')) {
            Schema::table('ct_conciliacion_bancaria', function (Blueprint $table) {
                $table->dropIndex('v3_con_source_state_ix');
            });
        }
    }

    private function indexExists($table, $index)
    {
        return count(DB::select('SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1', [$table, $index])) > 0;
    }
}
