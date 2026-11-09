<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExpandV3HrEcuadorPayroll extends Migration
{
    public function up()
    {
        if (Schema::hasTable('v3_hr_employees')) {
            Schema::table('v3_hr_employees', function (Blueprint $table) {
                if (! Schema::hasColumn('v3_hr_employees', 'decimo_tercero_modalidad')) {
                    $table->string('decimo_tercero_modalidad', 20)->default('mensual')->after('iess_codigo');
                }
                if (! Schema::hasColumn('v3_hr_employees', 'decimo_cuarto_modalidad')) {
                    $table->string('decimo_cuarto_modalidad', 20)->default('mensual')->after('decimo_tercero_modalidad');
                }
                if (! Schema::hasColumn('v3_hr_employees', 'fondo_reserva_modalidad')) {
                    $table->string('fondo_reserva_modalidad', 20)->default('iess')->after('decimo_cuarto_modalidad');
                }
                if (! Schema::hasColumn('v3_hr_employees', 'region_laboral')) {
                    $table->string('region_laboral', 30)->default('costa_insular')->after('fondo_reserva_modalidad');
                }
                if (! Schema::hasColumn('v3_hr_employees', 'retencion_renta_mensual')) {
                    $table->decimal('retencion_renta_mensual', 12, 2)->default(0)->after('region_laboral');
                }
            });
        }

        if (! Schema::hasTable('v3_hr_concepts')) {
            Schema::create('v3_hr_concepts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('id_empresa', 20);
                $table->string('codigo', 40);
                $table->string('nombre', 150);
                $table->string('tipo', 20);
                $table->string('categoria', 30)->default('otro');
                $table->boolean('afecta_iess')->default(false);
                $table->boolean('afecta_decimo_tercero')->default(false);
                $table->boolean('afecta_vacaciones')->default(false);
                $table->boolean('grava_renta')->default(false);
                $table->decimal('monto_referencial', 12, 2)->default(0);
                $table->boolean('estado')->default(true);
                $table->timestamps();
                $table->unique(['id_empresa', 'codigo'], 'v3_hr_concept_company_code_uq');
            });
        }

        if (! Schema::hasTable('v3_hr_concept_assignments')) {
            Schema::create('v3_hr_concept_assignments', function (Blueprint $table) {
                $table->increments('id');
                $table->string('id_empresa', 20);
                $table->unsignedInteger('employee_id');
                $table->unsignedInteger('concept_id');
                $table->decimal('monto', 12, 2);
                $table->string('periodo_desde', 7);
                $table->string('periodo_hasta', 7)->nullable();
                $table->text('observacion')->nullable();
                $table->boolean('estado')->default(true);
                $table->timestamps();
                $table->index(['id_empresa', 'employee_id', 'periodo_desde'], 'v3_hr_concept_assign_scope_idx');
            });
        }

        if (! Schema::hasTable('v3_hr_loans')) {
            Schema::create('v3_hr_loans', function (Blueprint $table) {
                $table->increments('id');
                $table->string('id_empresa', 20);
                $table->unsignedInteger('employee_id');
                $table->decimal('monto', 12, 2);
                $table->unsignedInteger('numero_cuotas');
                $table->date('fecha_solicitud');
                $table->date('fecha_desembolso')->nullable();
                $table->string('primer_periodo', 7);
                $table->string('medio_pago', 30)->default('transferencia');
                $table->string('referencia', 80)->nullable();
                $table->text('concepto');
                $table->string('estado', 20)->default('solicitado');
                $table->unsignedBigInteger('id_asiento')->nullable();
                $table->string('aprobado_por', 20)->nullable();
                $table->timestamp('aprobado_at')->nullable();
                $table->timestamps();
                $table->index(['id_empresa', 'employee_id', 'estado'], 'v3_hr_loan_scope_idx');
            });
        }

        if (! Schema::hasTable('v3_hr_loan_installments')) {
            Schema::create('v3_hr_loan_installments', function (Blueprint $table) {
                $table->increments('id');
                $table->string('id_empresa', 20);
                $table->unsignedInteger('loan_id');
                $table->unsignedInteger('numero');
                $table->string('periodo', 7);
                $table->decimal('monto', 12, 2);
                $table->string('estado', 20)->default('pendiente');
                $table->unsignedInteger('payroll_line_id')->nullable();
                $table->timestamp('descontado_at')->nullable();
                $table->timestamps();
                $table->unique(['loan_id', 'numero'], 'v3_hr_loan_installment_uq');
                $table->index(['id_empresa', 'periodo', 'estado'], 'v3_hr_loan_period_idx');
            });
        }

        if (! Schema::hasTable('v3_hr_benefit_payments')) {
            Schema::create('v3_hr_benefit_payments', function (Blueprint $table) {
                $table->increments('id');
                $table->string('id_empresa', 20);
                $table->unsignedInteger('employee_id');
                $table->string('tipo', 30);
                $table->date('fecha_desde');
                $table->date('fecha_hasta');
                $table->date('fecha_pago');
                $table->decimal('monto', 12, 2);
                $table->string('estado', 20)->default('solicitado');
                $table->unsignedBigInteger('id_asiento')->nullable();
                $table->string('aprobado_por', 20)->nullable();
                $table->timestamp('aprobado_at')->nullable();
                $table->timestamps();
                $table->index(['id_empresa', 'employee_id', 'tipo'], 'v3_hr_benefit_employee_idx');
            });
        }

        if (Schema::hasTable('v3_hr_payroll_lines')) {
            Schema::table('v3_hr_payroll_lines', function (Blueprint $table) {
                if (! Schema::hasColumn('v3_hr_payroll_lines', 'base_iess')) {
                    $table->decimal('base_iess', 12, 2)->default(0)->after('ingresos');
                }
                if (! Schema::hasColumn('v3_hr_payroll_lines', 'bonos')) {
                    $table->decimal('bonos', 12, 2)->default(0)->after('base_iess');
                }
                if (! Schema::hasColumn('v3_hr_payroll_lines', 'otros_ingresos')) {
                    $table->decimal('otros_ingresos', 12, 2)->default(0)->after('bonos');
                }
                if (! Schema::hasColumn('v3_hr_payroll_lines', 'decimo_tercero_pagado')) {
                    $table->decimal('decimo_tercero_pagado', 12, 2)->default(0)->after('provision_decimo_tercero');
                }
                if (! Schema::hasColumn('v3_hr_payroll_lines', 'decimo_cuarto_pagado')) {
                    $table->decimal('decimo_cuarto_pagado', 12, 2)->default(0)->after('provision_decimo_cuarto');
                }
                if (! Schema::hasColumn('v3_hr_payroll_lines', 'fondo_reserva_pagado')) {
                    $table->decimal('fondo_reserva_pagado', 12, 2)->default(0)->after('fondo_reserva');
                }
                if (! Schema::hasColumn('v3_hr_payroll_lines', 'retencion_renta')) {
                    $table->decimal('retencion_renta', 12, 2)->default(0)->after('aporte_patronal');
                }
                if (! Schema::hasColumn('v3_hr_payroll_lines', 'anticipos')) {
                    $table->decimal('anticipos', 12, 2)->default(0)->after('retencion_renta');
                }
                if (! Schema::hasColumn('v3_hr_payroll_lines', 'prestamos')) {
                    $table->decimal('prestamos', 12, 2)->default(0)->after('anticipos');
                }
                if (! Schema::hasColumn('v3_hr_payroll_lines', 'descuentos_conceptos')) {
                    $table->decimal('descuentos_conceptos', 12, 2)->default(0)->after('prestamos');
                }
                if (! Schema::hasColumn('v3_hr_payroll_lines', 'recargo_nocturno')) {
                    $table->decimal('recargo_nocturno', 12, 2)->default(0)->after('horas_100');
                }
            });
        }
        if (Schema::hasTable('v3_hr_time_entries')) {
            Schema::table('v3_hr_time_entries', function (Blueprint $table) {
                if (! Schema::hasColumn('v3_hr_time_entries', 'minutos_nocturnos')) {
                    $table->integer('minutos_nocturnos')->default(0)->after('minutos_extra_100');
                }
            });
        }

        $this->seedParameters();
        $this->seedAccountingSettings();
        $this->seedStandardAccounts();
    }

    private function seedParameters()
    {
        if (! Schema::hasTable('v3_hr_legal_parameters')) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $parameters = [
            ['codigo' => 'SBU', 'nombre' => 'Salario Básico Unificado 2026', 'valor' => 482, 'fuente' => 'Ministerio del Trabajo - MDT-2025-195'],
            ['codigo' => 'IESS_PERSONAL', 'nombre' => 'Aporte personal IESS general', 'valor' => 9.45, 'fuente' => 'IESS - afiliación bajo relación de dependencia'],
            ['codigo' => 'IESS_PATRONAL', 'nombre' => 'Aporte patronal IESS general', 'valor' => 11.15, 'fuente' => 'IESS - afiliación bajo relación de dependencia'],
            ['codigo' => 'VACACIONES_DIAS', 'nombre' => 'Vacaciones anuales base', 'valor' => 15, 'fuente' => 'Código del Trabajo, art. 69'],
            ['codigo' => 'VACACIONES_ADICIONALES_MAX', 'nombre' => 'Máximo de días adicionales por antigüedad', 'valor' => 15, 'fuente' => 'Código del Trabajo, art. 69'],
            ['codigo' => 'HORAS_MES', 'nombre' => 'Divisor mensual para horas extra', 'valor' => 240, 'fuente' => 'Código del Trabajo'],
            ['codigo' => 'RECARGO_NOCTURNO', 'nombre' => 'Recargo de jornada nocturna', 'valor' => 25, 'fuente' => 'Código del Trabajo, art. 49'],
            ['codigo' => 'FONDO_RESERVA_PCT', 'nombre' => 'Fondo de reserva', 'valor' => 8.333333, 'fuente' => 'IESS - Fondos de reserva'],
            ['codigo' => 'DECIMO_TERCERO_FACTOR', 'nombre' => 'Factor mensual décimo tercero', 'valor' => 8.333333, 'fuente' => 'Código del Trabajo, art. 111'],
            ['codigo' => 'DECIMO_CUARTO_FACTOR', 'nombre' => 'Factor mensual décimo cuarto', 'valor' => 8.333333, 'fuente' => 'Código del Trabajo, art. 113'],
            ['codigo' => 'VACACIONES_FACTOR', 'nombre' => 'Provisión mensual de vacaciones', 'valor' => 4.166667, 'fuente' => 'Código del Trabajo, arts. 69 y 71'],
        ];
        foreach ($parameters as $parameter) {
            $scope = ['id_empresa' => '*', 'codigo' => $parameter['codigo'], 'vigente_desde' => '2026-01-01'];
            if (! DB::table('v3_hr_legal_parameters')->where($scope)->exists()) {
                DB::table('v3_hr_legal_parameters')->insert(array_merge($scope, $parameter, ['created_at' => $now, 'updated_at' => $now]));
            }
        }
    }

    private function seedAccountingSettings()
    {
        if (! Schema::hasTable('v3_hr_accounting_settings') || ! Schema::hasTable('empresa')) {
            return;
        }
        $settings = [
            'PRESTAMOS_EMPLEADOS' => 'Préstamos a empleados por cobrar',
            'RETENCION_RENTA_POR_PAGAR' => 'Retención de renta en relación de dependencia',
            'OTROS_DESCUENTOS_POR_PAGAR' => 'Otros descuentos de nómina por pagar',
        ];
        $now = date('Y-m-d H:i:s');
        foreach (DB::table('empresa')->where('estado', 1)->get(['id']) as $company) {
            foreach ($settings as $code => $name) {
                $scope = ['id_empresa' => $company->id, 'codigo' => $code];
                if (! DB::table('v3_hr_accounting_settings')->where($scope)->exists()) {
                    DB::table('v3_hr_accounting_settings')->insert(array_merge($scope, ['nombre' => $name, 'estado' => 1, 'created_at' => $now, 'updated_at' => $now]));
                }
            }
        }
    }

    private function seedStandardAccounts()
    {
        if (! Schema::hasTable('plan_cuentas') || ! Schema::hasTable('plan_cuentas_empresa') || ! Schema::hasTable('empresa')) {
            return;
        }
        $accounts = [
            '1.01.03' => ['PRÉSTAMOS A EMPLEADOS', '1.01', 1, 0],
            '2.01.08' => ['RETENCIÓN RENTA EMPLEADOS POR PAGAR', '2.01', 0, 1],
            '2.01.09' => ['OTROS DESCUENTOS DE NÓMINA POR PAGAR', '2.01', 0, 1],
        ];
        $mapping = ['PRESTAMOS_EMPLEADOS' => '1.01.03', 'RETENCION_RENTA_POR_PAGAR' => '2.01.08', 'OTROS_DESCUENTOS_POR_PAGAR' => '2.01.09'];
        $actor = env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
        if (Schema::hasTable('users') && ! DB::table('users')->where('id', $actor)->exists()) {
            $actor = (string) DB::table('users')->orderBy('id')->value('id');
        }
        $now = date('Y-m-d H:i:s');
        foreach ($accounts as $code => $definition) {
            if (! DB::table('plan_cuentas')->where('id', $code)->exists()) {
                DB::table('plan_cuentas')->insert([
                    'id' => $code, 'nombre' => $definition[0], 'descripcion' => $definition[0], 'estado' => 2, 'id_padre' => $definition[1],
                    'naturaleza' => $definition[2], 'naturaleza_2' => $definition[3], 'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
                    'ip_creacion' => '127.0.0.1', 'ip_modificacion' => '127.0.0.1', 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
        foreach (DB::table('empresa')->where('estado', 1)->get(['id']) as $company) {
            foreach ($accounts as $code => $definition) {
                if (! DB::table('plan_cuentas_empresa')->where('id_empresa', $company->id)->where('id_plan', $code)->exists()) {
                    DB::table('plan_cuentas_empresa')->insert([
                        'id_empresa' => $company->id, 'id_plan' => $code, 'plan' => $code, 'nombre' => $definition[0], 'descripcion' => $definition[0],
                        'estado' => 2, 'id_padre' => $definition[1], 'naturaleza' => $definition[2], 'naturaleza_2' => $definition[3],
                        'id_usuariocrea' => $actor, 'id_usuariomod' => $actor, 'ip_creacion' => '127.0.0.1', 'ip_modificacion' => '127.0.0.1',
                        'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
            }
            foreach ($mapping as $setting => $account) {
                DB::table('v3_hr_accounting_settings')->where('id_empresa', $company->id)->where('codigo', $setting)->whereNull('id_plan_cuenta')->update(['id_plan_cuenta' => $account, 'updated_at' => $now]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('v3_hr_benefit_payments');
        Schema::dropIfExists('v3_hr_loan_installments');
        Schema::dropIfExists('v3_hr_loans');
        Schema::dropIfExists('v3_hr_concept_assignments');
        Schema::dropIfExists('v3_hr_concepts');

        if (Schema::hasTable('v3_hr_payroll_lines')) {
            Schema::table('v3_hr_payroll_lines', function (Blueprint $table) {
                foreach (['base_iess', 'bonos', 'otros_ingresos', 'decimo_tercero_pagado', 'decimo_cuarto_pagado', 'fondo_reserva_pagado', 'retencion_renta', 'anticipos', 'prestamos', 'descuentos_conceptos', 'recargo_nocturno'] as $column) {
                    if (Schema::hasColumn('v3_hr_payroll_lines', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
        if (Schema::hasTable('v3_hr_time_entries')) {
            Schema::table('v3_hr_time_entries', function (Blueprint $table) {
                if (Schema::hasColumn('v3_hr_time_entries', 'minutos_nocturnos')) {
                    $table->dropColumn('minutos_nocturnos');
                }
            });
        }
        if (Schema::hasTable('v3_hr_employees')) {
            Schema::table('v3_hr_employees', function (Blueprint $table) {
                foreach (['decimo_tercero_modalidad', 'decimo_cuarto_modalidad', 'fondo_reserva_modalidad', 'region_laboral', 'retencion_renta_mensual'] as $column) {
                    if (Schema::hasColumn('v3_hr_employees', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
        if (Schema::hasTable('v3_hr_accounting_settings')) {
            DB::table('v3_hr_accounting_settings')->whereIn('codigo', ['PRESTAMOS_EMPLEADOS', 'RETENCION_RENTA_POR_PAGAR', 'OTROS_DESCUENTOS_POR_PAGAR'])->delete();
        }
        if (Schema::hasTable('v3_hr_legal_parameters')) {
            DB::table('v3_hr_legal_parameters')->where('id_empresa', '*')->whereIn('codigo', ['FONDO_RESERVA_PCT', 'DECIMO_TERCERO_FACTOR', 'DECIMO_CUARTO_FACTOR', 'VACACIONES_FACTOR'])->delete();
        }
        // Las cuentas contables pueden haber recibido movimientos. Se conservan
        // al revertir para no eliminar historia financiera de la empresa.
    }
}
