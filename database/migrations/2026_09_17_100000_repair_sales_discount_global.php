<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RepairSalesDiscountGlobal extends Migration
{
    const SETTING = 'Ventas - Descuentos';

    public function up()
    {
        if (! Schema::hasTable('empresa') || ! Schema::hasTable('ct_modulos')
            || ! Schema::hasTable('ct_globales') || ! Schema::hasTable('plan_cuentas_empresa')) {
            return;
        }

        $module = DB::table('ct_modulos')->where('nombre', 'Ventas')->orderBy('id')->first();
        if (! $module) {
            $moduleId = DB::table('ct_modulos')->insertGetId([
                'nombre' => 'Ventas',
                'estado' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $moduleId = $module->id;
        }

        foreach (DB::table('empresa')->where('estado', 1)->get(['id']) as $company) {
            $account = DB::table('plan_cuentas_empresa')
                ->where('id_empresa', $company->id)
                ->where('estado', '<>', 0)
                ->where('plan', 'like', '4%')
                ->where(function ($query) {
                    $query->whereRaw('UPPER(nombre) LIKE ?', ['%DEVOLUCIONES%'])
                        ->orWhereRaw('UPPER(nombre) LIKE ?', ['%DESCUENTOS%']);
                })
                ->where(function ($query) {
                    $query->whereRaw('UPPER(nombre) LIKE ?', ['%VENTA%'])
                        ->orWhereRaw('UPPER(nombre) LIKE ?', ['%VENTAS%']);
                })
                ->orderByRaw("CASE WHEN plan = '4.01.02' THEN 0 ELSE 1 END")
                ->orderBy('plan')
                ->first(['id_plan']);

            // No se asigna una cuenta de otra empresa ni se inventa una cuenta.
            if (! $account) {
                continue;
            }

            $values = [
                'id_modulo' => $moduleId,
                'debe' => $account->id_plan,
                'haber' => null,
                'estado' => 1,
                'tipo' => 2,
                'activo_debe' => 1,
                'activo_haber' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $setting = DB::table('ct_globales')
                ->where('id_empresa', $company->id)
                ->where('nombre', self::SETTING)
                ->orderBy('id')
                ->first();

            if ($setting) {
                DB::table('ct_globales')->where('id', $setting->id)->update($values);
            } else {
                DB::table('ct_globales')->insert(array_merge($values, [
                    'nombre' => self::SETTING,
                    'id_empresa' => $company->id,
                    'created_at' => date('Y-m-d H:i:s'),
                ]));
            }
        }
    }

    public function down()
    {
        // No se elimina la configuración porque puede haber sido utilizada.
    }
}
