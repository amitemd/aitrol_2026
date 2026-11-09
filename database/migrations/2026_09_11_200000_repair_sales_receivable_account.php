<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RepairSalesReceivableAccount extends Migration
{
    const SETTING = 'Ventas - Cuentas por cobrar clientes';

    public function up()
    {
        if (! Schema::hasTable('empresa') || ! Schema::hasTable('ct_globales')
            || ! Schema::hasTable('ct_modulos') || ! Schema::hasTable('plan_cuentas_empresa')) {
            return;
        }

        $module = DB::table('ct_modulos')->where('nombre', 'Ventas')->orderBy('id')->first();
        if (! $module) {
            return;
        }

        foreach (DB::table('empresa')->where('estado', 1)->orderBy('id')->get(['id']) as $company) {
            $setting = DB::table('ct_globales')
                ->where('id_empresa', $company->id)
                ->where('nombre', self::SETTING)
                ->orderBy('id')
                ->first();

            if ($setting && $this->accountExists($company->id, $setting->debe, $setting->id_plan_cuenta)) {
                continue;
            }

            $account = DB::table('plan_cuentas_empresa')
                ->where('id_empresa', $company->id)
                ->where('estado', '<>', 0)
                ->where('plan', 'like', '1%')
                ->whereRaw('UPPER(nombre) LIKE ?', ['%CUENTAS POR COBRAR%'])
                ->whereRaw('UPPER(nombre) LIKE ?', ['%CLIENTES%'])
                ->orderByRaw("CASE
                    WHEN UPPER(TRIM(nombre)) = 'CUENTAS POR COBRAR CLIENTES' THEN 0
                    WHEN UPPER(nombre) LIKE '%NO RELACI%' THEN 1
                    WHEN UPPER(nombre) LIKE '%CLIENTES COMERCIALES%' THEN 2
                    ELSE 3 END")
                ->orderByRaw('LENGTH(plan) DESC')
                ->orderBy('plan')
                ->first(['id_plan']);

            // Si la empresa no posee una cuenta compatible, se mantiene el
            // bloqueo contable: nunca se toma una cuenta de otra empresa.
            if (! $account) {
                continue;
            }

            $values = [
                'id_modulo' => $module->id,
                'debe' => $account->id_plan,
                'estado' => 1,
                'tipo' => 2,
                'activo_debe' => 1,
                'activo_haber' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($setting) {
                DB::table('ct_globales')->where('id', $setting->id)->update($values);

                continue;
            }

            DB::table('ct_globales')->insert(array_merge($values, [
                'nombre' => self::SETTING,
                'id_empresa' => $company->id,
                'created_at' => date('Y-m-d H:i:s'),
            ]));
        }
    }

    public function down()
    {
        // La cuenta puede haber sido utilizada por asientos; no se elimina.
    }

    private function accountExists($companyId, $debitAccount, $generalAccount)
    {
        $account = trim((string) $debitAccount);
        if ($account === '') {
            $account = trim((string) $generalAccount);
        }
        if ($account === '') {
            return false;
        }

        return DB::table('plan_cuentas_empresa')
            ->where('id_empresa', $companyId)
            ->where('estado', '<>', 0)
            ->where(function ($query) use ($account) {
                $query->where('id_plan', $account)->orWhere('plan', $account);
            })
            ->exists();
    }
}
