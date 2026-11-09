<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEcuadorBenefitProvisionsToV3HrPayroll extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('v3_hr_payroll_lines')) {
            return;
        }
        Schema::table('v3_hr_payroll_lines', function (Blueprint $table) {
            if (! Schema::hasColumn('v3_hr_payroll_lines', 'provision_decimo_tercero')) {
                $table->decimal('provision_decimo_tercero', 12, 2)->default(0)->after('otros_descuentos');
            }
            if (! Schema::hasColumn('v3_hr_payroll_lines', 'provision_decimo_cuarto')) {
                $table->decimal('provision_decimo_cuarto', 12, 2)->default(0)->after('provision_decimo_tercero');
            }
            if (! Schema::hasColumn('v3_hr_payroll_lines', 'provision_vacaciones')) {
                $table->decimal('provision_vacaciones', 12, 2)->default(0)->after('provision_decimo_cuarto');
            }
            if (! Schema::hasColumn('v3_hr_payroll_lines', 'fondo_reserva')) {
                $table->decimal('fondo_reserva', 12, 2)->default(0)->after('provision_vacaciones');
            }
            if (! Schema::hasColumn('v3_hr_payroll_lines', 'aporte_patronal')) {
                $table->decimal('aporte_patronal', 12, 2)->default(0)->after('fondo_reserva');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('v3_hr_payroll_lines')) {
            return;
        }
        foreach (['provision_decimo_tercero', 'provision_decimo_cuarto', 'provision_vacaciones', 'fondo_reserva', 'aporte_patronal'] as $column) {
            if (Schema::hasColumn('v3_hr_payroll_lines', $column)) {
                Schema::table('v3_hr_payroll_lines', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
}
