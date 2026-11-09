<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateV3HumanResourcesCore extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('v3_hr_positions')) {
            Schema::create('v3_hr_positions', function (Blueprint $t) {
                $t->increments('id');
                $t->string('id_empresa', 20);
                $t->string('codigo', 30);
                $t->string('nombre', 120);
                $t->string('area', 100)->nullable();
                $t->decimal('salario_minimo', 12, 2)->default(0);
                $t->text('funciones')->nullable();
                $t->boolean('estado')->default(true);
                $t->timestamps();
                $t->unique(['id_empresa', 'codigo'], 'v3_hr_pos_company_code_uq');
            });
        }
        if (! Schema::hasTable('v3_hr_employees')) {
            Schema::create('v3_hr_employees', function (Blueprint $t) {
                $t->increments('id');
                $t->string('id_empresa', 20);
                $t->string('identificacion', 20);
                $t->string('nombres', 100);
                $t->string('apellidos', 100);
                $t->string('email', 150)->nullable();
                $t->string('telefono', 30)->nullable();
                $t->date('fecha_nacimiento')->nullable();
                $t->date('fecha_ingreso');
                $t->string('genero', 20)->nullable();
                $t->string('direccion', 255)->nullable();
                $t->unsignedInteger('position_id')->nullable();
                $t->string('id_sucursal', 20)->nullable();
                $t->string('estado', 20)->default('activo');
                $t->string('iess_codigo', 50)->nullable();
                $t->string('cuenta_bancaria', 50)->nullable();
                $t->string('banco', 100)->nullable();
                $t->string('id_usuario', 20)->nullable();
                $t->timestamps();
                $t->unique(['id_empresa', 'identificacion'], 'v3_hr_emp_company_ident_uq');
                $t->index(['id_empresa', 'estado']);
            });
        }
        if (! Schema::hasTable('v3_hr_contracts')) {
            Schema::create('v3_hr_contracts', function (Blueprint $t) {
                $t->increments('id');
                $t->string('id_empresa', 20);
                $t->unsignedInteger('employee_id');
                $t->string('tipo', 40);
                $t->date('fecha_inicio');
                $t->date('fecha_fin')->nullable();
                $t->decimal('sueldo', 12, 2);
                $t->decimal('horas_semana', 5, 2)->default(40);
                $t->string('modalidad', 30)->default('presencial');
                $t->string('estado', 20)->default('borrador');
                $t->string('numero_sut', 60)->nullable();
                $t->date('fecha_registro_sut')->nullable();
                $t->text('observacion')->nullable();
                $t->string('aprobado_por', 20)->nullable();
                $t->timestamp('aprobado_at')->nullable();
                $t->timestamps();
                $t->index(['id_empresa', 'employee_id', 'estado'], 'v3_hr_contract_scope_idx');
            });
        }
        if (! Schema::hasTable('v3_hr_schedules')) {
            Schema::create('v3_hr_schedules', function (Blueprint $t) {
                $t->increments('id');
                $t->string('id_empresa', 20);
                $t->string('nombre', 100);
                $t->time('entrada');
                $t->time('salida');
                $t->time('inicio_descanso')->nullable();
                $t->time('fin_descanso')->nullable();
                $t->string('dias', 30)->default('1,2,3,4,5');
                $t->unsignedInteger('tolerancia_minutos')->default(5);
                $t->boolean('nocturno')->default(false);
                $t->boolean('estado')->default(true);
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('v3_hr_schedule_assignments')) {
            Schema::create('v3_hr_schedule_assignments', function (Blueprint $t) {
                $t->increments('id');
                $t->string('id_empresa', 20);
                $t->unsignedInteger('employee_id');
                $t->unsignedInteger('schedule_id');
                $t->date('fecha_desde');
                $t->date('fecha_hasta')->nullable();
                $t->timestamps();
                $t->index(['id_empresa', 'employee_id', 'fecha_desde'], 'v3_hr_sched_assign_idx');
            });
        }
        if (! Schema::hasTable('v3_hr_time_entries')) {
            Schema::create('v3_hr_time_entries', function (Blueprint $t) {
                $t->increments('id');
                $t->string('id_empresa', 20);
                $t->unsignedInteger('employee_id');
                $t->date('fecha');
                $t->dateTime('entrada')->nullable();
                $t->dateTime('salida')->nullable();
                $t->integer('minutos_trabajados')->default(0);
                $t->integer('minutos_atraso')->default(0);
                $t->integer('minutos_extra_50')->default(0);
                $t->integer('minutos_extra_100')->default(0);
                $t->string('origen', 20)->default('manual');
                $t->string('estado', 20)->default('registrado');
                $t->text('observacion')->nullable();
                $t->string('aprobado_por', 20)->nullable();
                $t->timestamps();
                $t->unique(['id_empresa', 'employee_id', 'fecha'], 'v3_hr_time_daily_uq');
            });
        }
        if (! Schema::hasTable('v3_hr_leave_requests')) {
            Schema::create('v3_hr_leave_requests', function (Blueprint $t) {
                $t->increments('id');
                $t->string('id_empresa', 20);
                $t->unsignedInteger('employee_id');
                $t->string('tipo', 40);
                $t->date('fecha_desde');
                $t->date('fecha_hasta');
                $t->decimal('dias', 6, 2);
                $t->boolean('remunerado')->default(true);
                $t->string('estado', 20)->default('solicitado');
                $t->text('motivo')->nullable();
                $t->string('documento', 255)->nullable();
                $t->string('resuelto_por', 20)->nullable();
                $t->timestamp('resuelto_at')->nullable();
                $t->timestamps();
                $t->index(['id_empresa', 'employee_id', 'estado'], 'v3_hr_leave_scope_idx');
            });
        }
        if (! Schema::hasTable('v3_hr_payroll_periods')) {
            Schema::create('v3_hr_payroll_periods', function (Blueprint $t) {
                $t->increments('id');
                $t->string('id_empresa', 20);
                $t->string('periodo', 7);
                $t->date('fecha_desde');
                $t->date('fecha_hasta');
                $t->string('estado', 20)->default('borrador');
                $t->string('aprobado_por', 20)->nullable();
                $t->timestamp('aprobado_at')->nullable();
                $t->string('cerrado_por', 20)->nullable();
                $t->timestamp('cerrado_at')->nullable();
                $t->timestamps();
                $t->unique(['id_empresa', 'periodo'], 'v3_hr_pay_period_uq');
            });
        }
        if (! Schema::hasTable('v3_hr_payroll_lines')) {
            Schema::create('v3_hr_payroll_lines', function (Blueprint $t) {
                $t->increments('id');
                $t->string('id_empresa', 20);
                $t->unsignedInteger('period_id');
                $t->unsignedInteger('employee_id');
                $t->decimal('sueldo', 12, 2)->default(0);
                $t->decimal('horas_50', 8, 2)->default(0);
                $t->decimal('horas_100', 8, 2)->default(0);
                $t->decimal('ingresos', 12, 2)->default(0);
                $t->decimal('aporte_iess', 12, 2)->default(0);
                $t->decimal('otros_descuentos', 12, 2)->default(0);
                $t->decimal('provision_decimo_tercero', 12, 2)->default(0);
                $t->decimal('provision_decimo_cuarto', 12, 2)->default(0);
                $t->decimal('provision_vacaciones', 12, 2)->default(0);
                $t->decimal('fondo_reserva', 12, 2)->default(0);
                $t->decimal('aporte_patronal', 12, 2)->default(0);
                $t->decimal('neto', 12, 2)->default(0);
                $t->text('detalle')->nullable();
                $t->timestamps();
                $t->unique(['period_id', 'employee_id'], 'v3_hr_pay_line_uq');
            });
        }
        if (! Schema::hasTable('v3_hr_legal_parameters')) {
            Schema::create('v3_hr_legal_parameters', function (Blueprint $t) {
                $t->increments('id');
                $t->string('id_empresa', 20)->default('*');
                $t->string('codigo', 50);
                $t->string('nombre', 150);
                $t->decimal('valor', 14, 6);
                $t->date('vigente_desde');
                $t->date('vigente_hasta')->nullable();
                $t->string('fuente', 255)->nullable();
                $t->text('nota')->nullable();
                $t->timestamps();
                $t->unique(['id_empresa', 'codigo', 'vigente_desde'], 'v3_hr_legal_period_uq');
            });
        }
        if (DB::table('v3_hr_legal_parameters')->count() === 0) {
            $now = date('Y-m-d H:i:s');
            $from = '2026-01-01';
            $rows = [
                ['codigo' => 'SBU', 'nombre' => 'Salario Básico Unificado', 'valor' => 482, 'fuente' => 'MDT-2025-195'],
                ['codigo' => 'IESS_PERSONAL', 'nombre' => 'Aporte personal IESS general', 'valor' => 9.45, 'fuente' => 'IESS'],
                ['codigo' => 'IESS_PATRONAL', 'nombre' => 'Aporte patronal IESS general', 'valor' => 11.15, 'fuente' => 'IESS'],
                ['codigo' => 'RECARGO_50', 'nombre' => 'Recargo hora suplementaria', 'valor' => 50, 'fuente' => 'Código del Trabajo, art. 55'],
                ['codigo' => 'RECARGO_100', 'nombre' => 'Recargo hora extraordinaria', 'valor' => 100, 'fuente' => 'Código del Trabajo, art. 55'],
                ['codigo' => 'VACACIONES_DIAS', 'nombre' => 'Vacaciones anuales base', 'valor' => 15, 'fuente' => 'Código del Trabajo, art. 69'],
                ['codigo' => 'HORAS_MES', 'nombre' => 'Divisor mensual para horas extra', 'valor' => 240, 'fuente' => 'Código del Trabajo'],
            ];
            foreach ($rows as $row) {
                DB::table('v3_hr_legal_parameters')->insert(array_merge($row, ['id_empresa' => '*', 'vigente_desde' => $from, 'created_at' => $now, 'updated_at' => $now]));
            }
        }
    }

    public function down()
    {
        foreach (['v3_hr_payroll_lines', 'v3_hr_payroll_periods', 'v3_hr_leave_requests', 'v3_hr_time_entries', 'v3_hr_schedule_assignments', 'v3_hr_schedules', 'v3_hr_contracts', 'v3_hr_employees', 'v3_hr_positions', 'v3_hr_legal_parameters'] as $table) {
            Schema::dropIfExists($table);
        }
    }
}
