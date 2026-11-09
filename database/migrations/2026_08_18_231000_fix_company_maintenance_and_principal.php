<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixCompanyMaintenanceAndPrincipal extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        if (Schema::hasTable('sidebar_items')) {
            DB::table('sidebar_items')->where('clave', 'system.companies')->update([
                'nombre_ruta' => 'empresa.maintenance.index',
                'url' => '/empresa',
                'patrones_activos' => 'empresa',
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('opcion') && Schema::hasColumn('opcion', 'clave')) {
            $optionValues = [
                'ruta' => '/empresa',
                'updated_at' => $now,
            ];

            foreach (array_keys($optionValues) as $column) {
                if (! Schema::hasColumn('opcion', $column)) {
                    unset($optionValues[$column]);
                }
            }

            if (! empty($optionValues)) {
                DB::table('opcion')->where('clave', 'system.companies')->update($optionValues);
            }
        }

        if (! Schema::hasTable('empresa')
            || ! Schema::hasColumn('empresa', 'estado')
            || ! Schema::hasColumn('empresa', 'prioridad')) {
            return;
        }

        $hasPrincipal = DB::table('empresa')
            ->where('estado', 1)
            ->where('prioridad', 1)
            ->exists();

        if (! $hasPrincipal) {
            $activeCompany = DB::table('empresa')->where('estado', 1)->orderBy('id')->first();
            if ($activeCompany) {
                DB::table('empresa')->where('id', $activeCompany->id)->update(['prioridad' => 1]);
            }
        }
    }

    public function down()
    {
        // No se revierte: evita restaurar una ruta ambigua o quitar la empresa principal.
    }
}
