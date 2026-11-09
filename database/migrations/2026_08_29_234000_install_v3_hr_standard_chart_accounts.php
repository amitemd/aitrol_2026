<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstallV3HrStandardChartAccounts extends Migration
{
    private $accounts = [
        '1.01.02' => ['ANTICIPOS A EMPLEADOS', '1.01', 1, 0],
        '2.01.02' => ['SUELDOS POR PAGAR', '2.01', 0, 1], '2.01.03' => ['IESS POR PAGAR', '2.01', 0, 1], '2.01.04' => ['DÉCIMO TERCERO POR PAGAR', '2.01', 0, 1], '2.01.05' => ['DÉCIMO CUARTO POR PAGAR', '2.01', 0, 1], '2.01.06' => ['VACACIONES POR PAGAR', '2.01', 0, 1], '2.01.07' => ['FONDO DE RESERVA POR PAGAR', '2.01', 0, 1],
        '6.01.02' => ['SUELDOS Y HORAS EXTRA', '6.01', 1, 0], '6.01.03' => ['APORTE PATRONAL IESS', '6.01', 1, 0], '6.01.04' => ['GASTO DÉCIMO TERCERO', '6.01', 1, 0], '6.01.05' => ['GASTO DÉCIMO CUARTO', '6.01', 1, 0], '6.01.06' => ['GASTO VACACIONES', '6.01', 1, 0], '6.01.07' => ['GASTO FONDO DE RESERVA', '6.01', 1, 0],
    ];

    private $mapping = ['ANTICIPOS_EMPLEADOS' => '1.01.02', 'BANCO_CAJA_RRHH' => '1.01.01.02', 'SUELDOS_POR_PAGAR' => '2.01.02', 'IESS_POR_PAGAR' => '2.01.03', 'DECIMO_TERCERO_POR_PAGAR' => '2.01.04', 'DECIMO_CUARTO_POR_PAGAR' => '2.01.05', 'VACACIONES_POR_PAGAR' => '2.01.06', 'FONDO_RESERVA_POR_PAGAR' => '2.01.07', 'SUELDOS_GASTO' => '6.01.02', 'IESS_PATRONAL_GASTO' => '6.01.03', 'DECIMO_TERCERO_GASTO' => '6.01.04', 'DECIMO_CUARTO_GASTO' => '6.01.05', 'VACACIONES_GASTO' => '6.01.06', 'FONDO_RESERVA_GASTO' => '6.01.07'];

    public function up()
    {
        if (! Schema::hasTable('plan_cuentas') || ! Schema::hasTable('plan_cuentas_empresa')) {
            return;
        }$actor = env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
        if (Schema::hasTable('users') && ! DB::table('users')->where('id', $actor)->exists()) {
            $actor = (string) DB::table('users')->orderBy('id')->value('id');
        }$now = date('Y-m-d H:i:s');
        foreach ($this->accounts as $code => $def) {
            if (! DB::table('plan_cuentas')->where('id', $code)->exists()) {
                DB::table('plan_cuentas')->insert(['id' => $code, 'nombre' => $def[0], 'descripcion' => $def[0], 'estado' => 2, 'id_padre' => $def[1], 'naturaleza' => $def[2], 'naturaleza_2' => $def[3], 'id_usuariocrea' => $actor, 'id_usuariomod' => $actor, 'ip_creacion' => '127.0.0.1', 'ip_modificacion' => '127.0.0.1', 'created_at' => $now, 'updated_at' => $now]);
            }
        }foreach (DB::table('empresa')->where('estado', 1)->get(['id']) as $company) {
            foreach ($this->accounts as $code => $def) {
                if (! DB::table('plan_cuentas_empresa')->where('id_empresa', $company->id)->where('id_plan', $code)->exists()) {
                    DB::table('plan_cuentas_empresa')->insert(['id_empresa' => $company->id, 'id_plan' => $code, 'plan' => $code, 'nombre' => $def[0], 'descripcion' => $def[0], 'estado' => 2, 'id_padre' => $def[1], 'naturaleza' => $def[2], 'naturaleza_2' => $def[3], 'id_usuariocrea' => $actor, 'id_usuariomod' => $actor, 'ip_creacion' => '127.0.0.1', 'ip_modificacion' => '127.0.0.1', 'created_at' => $now, 'updated_at' => $now]);
                }
            }foreach ($this->mapping as $setting => $account) {
                DB::table('v3_hr_accounting_settings')->where('id_empresa', $company->id)->where('codigo', $setting)->whereNull('id_plan_cuenta')->update(['id_plan_cuenta' => $account, 'updated_at' => $now]);
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('v3_hr_accounting_settings')) {
            foreach ($this->mapping as $setting => $account) {
                DB::table('v3_hr_accounting_settings')->where('codigo', $setting)->where('id_plan_cuenta', $account)->update(['id_plan_cuenta' => null]);
            }
        }if (Schema::hasTable('plan_cuentas_empresa')) {
            DB::table('plan_cuentas_empresa')->whereIn('id_plan', array_keys($this->accounts))->delete();
        }if (Schema::hasTable('plan_cuentas')) {
            DB::table('plan_cuentas')->whereIn('id', array_keys($this->accounts))->delete();
        }
    }
}
