<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompleteV3HrPermissionsAdvancesAndAccounting extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('v3_hr_accounting_settings')) {
            Schema::create('v3_hr_accounting_settings', function (Blueprint $t) {
                $t->increments('id');
                $t->string('id_empresa', 20);
                $t->string('codigo', 60);
                $t->string('nombre', 150);
                $t->string('id_plan_cuenta', 40)->nullable();
                $t->boolean('estado')->default(true);
                $t->timestamps();
                $t->unique(['id_empresa', 'codigo'], 'v3_hr_acc_setting_uq');
            });
        }
        if (! Schema::hasTable('v3_hr_advances')) {
            Schema::create('v3_hr_advances', function (Blueprint $t) {
                $t->increments('id');
                $t->string('id_empresa', 20);
                $t->unsignedInteger('employee_id');
                $t->decimal('monto', 12, 2);
                $t->date('fecha_solicitud');
                $t->date('fecha_pago')->nullable();
                $t->string('periodo_descuento', 7);
                $t->string('medio_pago', 30)->default('transferencia');
                $t->string('referencia', 80)->nullable();
                $t->text('concepto');
                $t->string('estado', 20)->default('solicitado');
                $t->unsignedBigInteger('id_asiento')->nullable();
                $t->unsignedInteger('payroll_line_id')->nullable();
                $t->string('aprobado_por', 20)->nullable();
                $t->timestamp('aprobado_at')->nullable();
                $t->timestamps();
                $t->index(['id_empresa', 'periodo_descuento', 'estado'], 'v3_hr_adv_period_idx');
            });
        }
        if (Schema::hasTable('v3_hr_payroll_periods')) {
            Schema::table('v3_hr_payroll_periods', function (Blueprint $t) {
                if (! Schema::hasColumn('v3_hr_payroll_periods', 'id_asiento')) {
                    $t->unsignedBigInteger('id_asiento')->nullable()->after('estado');
                }if (! Schema::hasColumn('v3_hr_payroll_periods', 'contabilizado_por')) {
                    $t->string('contabilizado_por', 20)->nullable()->after('id_asiento');
                }if (! Schema::hasColumn('v3_hr_payroll_periods', 'contabilizado_at')) {
                    $t->timestamp('contabilizado_at')->nullable()->after('contabilizado_por');
                }
            });
        }
        $definitions = ['SUELDOS_GASTO' => 'Sueldos y horas extra', 'IESS_PATRONAL_GASTO' => 'Aporte patronal - gasto', 'DECIMO_TERCERO_GASTO' => 'Décimo tercero - gasto', 'DECIMO_CUARTO_GASTO' => 'Décimo cuarto - gasto', 'VACACIONES_GASTO' => 'Vacaciones - gasto', 'FONDO_RESERVA_GASTO' => 'Fondo de reserva - gasto', 'IESS_POR_PAGAR' => 'IESS personal y patronal por pagar', 'SUELDOS_POR_PAGAR' => 'Sueldos por pagar', 'DECIMO_TERCERO_POR_PAGAR' => 'Décimo tercero por pagar', 'DECIMO_CUARTO_POR_PAGAR' => 'Décimo cuarto por pagar', 'VACACIONES_POR_PAGAR' => 'Vacaciones por pagar', 'FONDO_RESERVA_POR_PAGAR' => 'Fondo de reserva por pagar', 'ANTICIPOS_EMPLEADOS' => 'Anticipos a empleados', 'BANCO_CAJA_RRHH' => 'Banco o caja para desembolsos RRHH'];
        if (Schema::hasTable('empresa')) {
            foreach (DB::table('empresa')->where('estado', 1)->get(['id']) as $company) {
                foreach ($definitions as $code => $name) {
                    if (! DB::table('v3_hr_accounting_settings')->where('id_empresa', $company->id)->where('codigo', $code)->exists()) {
                        DB::table('v3_hr_accounting_settings')->insert(['id_empresa' => $company->id, 'codigo' => $code, 'nombre' => $name, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                    }
                }
            }
        }
        $this->seedRolePermissions();
    }

    private function seedRolePermissions()
    {
        if (! Schema::hasTable('v3_role_action_permissions') || ! Schema::hasTable('users')) {
            return;
        }$now = date('Y-m-d H:i:s');
        $roles = DB::table('users')->join('usuario_empresa', 'usuario_empresa.id_usuario', '=', 'users.id')->select('usuario_empresa.id_empresa', 'users.id_tipo_usuario')->distinct()->get();
        foreach ($roles as $role) {
            $type = (int) $role->id_tipo_usuario;
            $actions = $type === 1 ? ['view', 'create', 'update', 'delete', 'approve', 'export'] : ['view'];
            foreach ($actions as $action) {
                $scope = ['id_empresa' => $role->id_empresa, 'id_tipo_usuario' => $type, 'modulo' => 'rrhh', 'accion' => $action];
                if (! DB::table('v3_role_action_permissions')->where($scope)->exists()) {
                    DB::table('v3_role_action_permissions')->insert(array_merge($scope, ['permitido' => 1, 'created_at' => $now, 'updated_at' => $now]));
                }
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('v3_hr_payroll_periods')) {
            Schema::table('v3_hr_payroll_periods', function (Blueprint $t) {
                foreach (['id_asiento', 'contabilizado_por', 'contabilizado_at'] as $c) {
                    if (Schema::hasColumn('v3_hr_payroll_periods', $c)) {
                        $t->dropColumn($c);
                    }
                }
            });
        }Schema::dropIfExists('v3_hr_advances');
        Schema::dropIfExists('v3_hr_accounting_settings');
        if (Schema::hasTable('v3_role_action_permissions')) {
            DB::table('v3_role_action_permissions')->where('modulo','rrhh')->delete();
        }
    }
}
