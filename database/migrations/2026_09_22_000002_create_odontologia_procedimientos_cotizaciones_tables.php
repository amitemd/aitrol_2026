<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOdontologiaProcedimientosCotizacionesTables extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('procedimiento') && ! $this->hasColumn('procedimiento', 'precio')) {
            Schema::table('procedimiento', function (Blueprint $table) {
                $table->decimal('precio', 12, 2)->nullable();
            });
        }
        if (Schema::hasTable('procedimiento') && ! $this->hasColumn('procedimiento', 'id_especialidad')) {
            Schema::table('procedimiento', function (Blueprint $table) {
                $table->unsignedTinyInteger('id_especialidad')->nullable()->index('procedimiento_especialidad_index');
            });
        }
        if (Schema::hasTable('cie_10_3') && ! $this->hasColumn('cie_10_3', 'id_especialidad')) {
            Schema::table('cie_10_3', function (Blueprint $table) {
                $table->unsignedTinyInteger('id_especialidad')->nullable()->index('cie_10_3_especialidad_index');
            });
        }

        if (! Schema::hasTable('procedimiento_odontologia_precios_bkp_20260914')) {
            Schema::create('procedimiento_odontologia_precios_bkp_20260914', function (Blueprint $table) {
                $table->unsignedInteger('id_procedimiento')->primary();
                $table->string('nombre');
                $table->decimal('precio_anterior', 12, 2)->nullable();
                $table->dateTime('respaldado_en');
            });
        }

        if (! Schema::hasTable('odont_procedimientos')) {
            Schema::create('odont_procedimientos', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('hcid');
                $table->unsignedInteger('id_agenda')->nullable();
                $table->string('id_paciente', 20);
                $table->string('codigo', 40)->nullable();
                $table->string('nombre');
                $table->text('descripcion')->nullable();
                $table->integer('id_hc_protocolo')->nullable();
                $table->tinyInteger('estado')->default(1);
                $this->auditColumns($table);
                $table->index('hcid', 'idx_odont_proc_hcid');
                $table->index('id_agenda', 'idx_odont_proc_agenda');
                $table->index('id_paciente', 'idx_odont_proc_paciente');
                $table->index('nombre', 'idx_odont_proc_nombre');
                $table->index('id_hc_protocolo', 'idx_odont_proc_hc_protocolo');
            });
        } elseif (! $this->hasColumn('odont_procedimientos', 'id_hc_protocolo')) {
            Schema::table('odont_procedimientos', function (Blueprint $table) {
                $table->integer('id_hc_protocolo')->nullable();
                $table->index('id_hc_protocolo', 'idx_odont_proc_hc_protocolo');
            });
        }

        if (! Schema::hasTable('odont_paciente_procedimientos')) {
            Schema::create('odont_paciente_procedimientos', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('hcid');
                $table->unsignedInteger('id_agenda')->nullable();
                $table->string('id_paciente', 20);
                $table->unsignedInteger('id_formulario033')->nullable();
                $table->unsignedInteger('id_odont_procedimiento');
                $table->unsignedInteger('id_procedimiento_catalogo')->nullable();
                $table->date('fecha')->nullable();
                $table->string('pieza_fdi', 2)->nullable();
                $table->string('cara_dental', 2)->nullable();
                $table->text('diagnostico')->nullable();
                $table->text('procedimiento')->nullable();
                $table->text('observaciones')->nullable();
                $table->string('estado_procedimiento', 30)->default('REALIZADO');
                $table->string('id_doctor', 20)->nullable();
                $table->tinyInteger('estado')->default(1);
                $this->auditColumns($table);
                $table->index('hcid', 'idx_odont_pacproc_hcid');
                $table->index('id_agenda', 'idx_odont_pacproc_agenda');
                $table->index('id_paciente', 'idx_odont_pacproc_paciente');
                $table->index('id_formulario033', 'idx_odont_pacproc_form');
                $table->index('id_odont_procedimiento', 'idx_odont_pacproc_proc');
                $table->index('id_procedimiento_catalogo', 'idx_odont_pacproc_catalogo');
                $table->index('id_doctor', 'idx_odont_pacproc_doctor');
            });
        } else {
            if (! $this->hasColumn('odont_paciente_procedimientos', 'id_procedimiento_catalogo')) {
                Schema::table('odont_paciente_procedimientos', function (Blueprint $table) {
                    $table->unsignedInteger('id_procedimiento_catalogo')->nullable();
                    $table->index('id_procedimiento_catalogo', 'idx_odont_pacproc_catalogo');
                });
            }
            if (! $this->hasColumn('odont_paciente_procedimientos', 'cara_dental')) {
                Schema::table('odont_paciente_procedimientos', function (Blueprint $table) {
                    $table->string('cara_dental', 2)->nullable();
                });
            }
        }

        if (! Schema::hasTable('odont_cotizaciones')) {
            Schema::create('odont_cotizaciones', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_paciente', 20);
                $table->unsignedBigInteger('hcid');
                $table->string('estado', 12)->default('BORRADOR');
                $table->unsignedInteger('version')->default(1);
                $table->decimal('total', 14, 2)->default(0);
                $table->longText('detalle_json');
                $table->string('id_usuariocrea', 20);
                $table->string('id_usuariomod', 20);
                $table->timestamps();
                $table->index(['id_paciente', 'created_at'], 'odont_cotizaciones_paciente');
                $table->index(['hcid', 'estado'], 'odont_cotizaciones_historia');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('odont_cotizaciones');
        Schema::dropIfExists('odont_paciente_procedimientos');
        Schema::dropIfExists('odont_procedimientos');
    }

    private function auditColumns(Blueprint $table): void
    {
        $table->string('id_usuariocrea', 20);
        $table->string('id_usuariomod', 20);
        $table->string('ip_creacion', 45);
        $table->string('ip_modificacion', 45);
        $table->timestamps();
    }

    private function hasColumn(string $table, string $column): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasColumn($table, $column);
        }

        return DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->exists();
    }
}
