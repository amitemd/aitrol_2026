<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LinkSingleSiteCompanies extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('company_hospital_contexts') || ! Schema::hasTable('empresa') || ! Schema::hasTable('hospital')) {
            return;
        }
        $hospitals = DB::table('hospital')->where('estado', 1)->pluck('id');
        $companies = DB::table('empresa')->where('estado', 1)->pluck('id');
        // Una sola sede global no demuestra pertenencia cuando existen varias
        // empresas. Sólo se infiere la relación en una instalación monoempresa.
        if ($hospitals->count() !== 1 || $companies->count() !== 1) {
            return;
        }
        $hospitalId = (int) $hospitals->first();
        $now = date('Y-m-d H:i:s');
        foreach ($companies as $companyId) {
            if (DB::table('company_hospital_contexts')->where('company_id', $companyId)->where('active', 1)->exists()) {
                continue;
            }
            DB::table('company_hospital_contexts')->insert(['company_id' => $companyId, 'hospital_id' => $hospitalId,
                'is_default' => 1, 'active' => 1, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    public function down()
    {
        // La relación puede haber sido adoptada por datos clínicos posteriores;
        // no se elimina automáticamente para preservar el aislamiento de sede.
    }
}
