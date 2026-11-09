<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV3BudgetManagement extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('v3_budgets')) {
            Schema::create('v3_budgets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 17);
                $table->string('codigo', 30);
                $table->string('nombre', 150);
                $table->unsignedSmallInteger('anio');
                $table->date('fecha_inicio');
                $table->date('fecha_fin');
                $table->string('estado', 20)->default('BORRADOR');
                $table->string('descripcion', 500)->nullable();
                $table->dateTime('fecha_aprobacion')->nullable();
                $table->string('id_usuario_aprobacion', 191)->nullable();
                $table->dateTime('fecha_cierre')->nullable();
                $table->string('id_usuario_cierre', 191)->nullable();
                $table->string('id_usuariocrea', 191)->nullable();
                $table->string('id_usuariomod', 191)->nullable();
                $table->string('ip_creacion', 45)->nullable();
                $table->string('ip_modificacion', 45)->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'codigo'], 'v3_budget_company_code_uq');
                $table->index(['id_empresa', 'anio', 'estado'], 'v3_budget_company_year_status_idx');
            });
        }

        if (! Schema::hasTable('v3_budget_lines')) {
            Schema::create('v3_budget_lines', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('id_presupuesto');
                $table->string('id_plan_cuenta', 191);
                $table->unsignedBigInteger('id_centro_costo')->nullable();
                $table->string('tipo', 10);
                $table->string('descripcion', 220);
                $table->boolean('control_compromisos')->default(false);
                $table->string('id_usuariocrea', 191)->nullable();
                $table->string('id_usuariomod', 191)->nullable();
                $table->timestamps();
                $table->index(['id_presupuesto', 'tipo'], 'v3_budget_line_budget_type_idx');
                $table->index(['id_plan_cuenta', 'id_centro_costo'], 'v3_budget_line_account_center_idx');
            });
        }

        if (! Schema::hasTable('v3_budget_line_months')) {
            Schema::create('v3_budget_line_months', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('id_linea_presupuesto');
                $table->unsignedTinyInteger('mes');
                $table->decimal('valor_inicial', 15, 2)->default(0);
                $table->decimal('valor_reforma', 15, 2)->default(0);
                $table->timestamps();
                $table->unique(['id_linea_presupuesto', 'mes'], 'v3_budget_line_month_uq');
            });
        }

        if (! Schema::hasTable('v3_budget_adjustments')) {
            Schema::create('v3_budget_adjustments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('grupo', 36);
                $table->unsignedBigInteger('id_presupuesto');
                $table->unsignedBigInteger('id_linea_presupuesto');
                $table->unsignedTinyInteger('mes');
                $table->string('tipo', 20);
                $table->decimal('valor', 15, 2);
                $table->string('referencia', 80)->nullable();
                $table->string('justificacion', 500);
                $table->string('id_usuariocrea', 191)->nullable();
                $table->string('ip_creacion', 45)->nullable();
                $table->timestamps();
                $table->index(['id_presupuesto', 'created_at'], 'v3_budget_adjustment_budget_date_idx');
                $table->index('grupo', 'v3_budget_adjustment_group_idx');
            });
        }

        if (! Schema::hasTable('v3_budget_commitments')) {
            Schema::create('v3_budget_commitments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 17);
                $table->unsignedBigInteger('id_presupuesto');
                $table->unsignedBigInteger('id_linea_presupuesto');
                $table->date('fecha');
                $table->string('referencia', 80);
                $table->string('beneficiario', 180)->nullable();
                $table->string('descripcion', 500);
                $table->decimal('valor', 15, 2);
                $table->string('estado', 20)->default('RESERVADO');
                $table->unsignedBigInteger('id_asiento_cabecera')->nullable();
                $table->dateTime('fecha_devengado')->nullable();
                $table->string('id_usuariocrea', 191)->nullable();
                $table->string('id_usuariomod', 191)->nullable();
                $table->string('ip_creacion', 45)->nullable();
                $table->string('ip_modificacion', 45)->nullable();
                $table->timestamps();
                $table->index(['id_empresa', 'fecha', 'estado'], 'v3_budget_commitment_company_date_idx');
                $table->index(['id_linea_presupuesto', 'estado'], 'v3_budget_commitment_line_status_idx');
                $table->index('id_asiento_cabecera', 'v3_budget_commitment_entry_idx');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('v3_budget_commitments');
        Schema::dropIfExists('v3_budget_adjustments');
        Schema::dropIfExists('v3_budget_line_months');
        Schema::dropIfExists('v3_budget_lines');
        Schema::dropIfExists('v3_budgets');
    }
}
