<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ct_caja_cobro')) {
            return;
        }

        if (! Schema::hasColumn('ct_caja_cobro', 'id_empresa')) {
            Schema::table('ct_caja_cobro', function (Blueprint $table) {
                $table->string('id_empresa', 20)->nullable()->after('id');
                $table->index('id_empresa', 'ct_caja_cobro_empresa_idx');
            });
        }

        $activeCompanies = collect();
        if (Schema::hasTable('empresa')) {
            $activeCompanies = DB::table('empresa')->where('estado', 1)->pluck('id')
                ->filter()->map(function ($id) {
                    return (string) $id;
                })->unique()->sort()->values();
        }

        foreach (DB::table('ct_caja_cobro')->whereNull('id_empresa')->orWhere('id_empresa', '')->get() as $cashRegister) {
            $owners = collect();
            if (Schema::hasTable('ct_orden_venta')) {
                $owners = DB::table('ct_orden_venta')->where('caja', $cashRegister->id)
                    ->whereNotNull('id_empresa')->where('id_empresa', '<>', '')
                    ->distinct()->pluck('id_empresa')->filter()
                    ->map(function ($id) {
                        return (string) $id;
                    })->unique()->sort()->values();
            }

            // El catálogo anterior era global. Replicarlo conserva las cajas
            // disponibles, pero desde ahora cada copia pertenece a una sola empresa.
            $companies = $owners->merge($activeCompanies)->unique()->sort()->values();
            if ($companies->isEmpty()) {
                continue;
            }

            $originalCompany = $owners->isNotEmpty() ? (string) $owners->first() : (string) $companies->first();
            DB::table('ct_caja_cobro')->where('id', $cashRegister->id)
                ->update(['id_empresa' => $originalCompany]);

            foreach ($companies as $companyId) {
                $companyId = (string) $companyId;
                $companyCashId = (string) $cashRegister->id;
                if ($companyId !== $originalCompany) {
                    $companyCashId = $this->companyCashId((string) $cashRegister->id, $companyId);
                    $copy = (array) $cashRegister;
                    $copy['id'] = $companyCashId;
                    $copy['id_empresa'] = $companyId;
                    DB::table('ct_caja_cobro')->insert($copy);
                }

                if (Schema::hasTable('ct_orden_venta')) {
                    DB::table('ct_orden_venta')->where('id_empresa', $companyId)
                        ->where('caja', $cashRegister->id)->update(['caja' => $companyCashId]);
                }
                if (Schema::hasTable('ct_arqueo_caja') && Schema::hasColumn('ct_arqueo_caja', 'id_empresa')) {
                    DB::table('ct_arqueo_caja')->where('id_empresa', $companyId)
                        ->where('caja', $cashRegister->id)->update(['caja' => $companyCashId]);
                }
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('ct_caja_cobro') || ! Schema::hasColumn('ct_caja_cobro', 'id_empresa')) {
            return;
        }
        Schema::table('ct_caja_cobro', function (Blueprint $table) {
            $table->dropIndex('ct_caja_cobro_empresa_idx');
            $table->dropColumn('id_empresa');
        });
    }

    private function companyCashId(string $baseId, string $companyId): string
    {
        $attempt = 0;
        do {
            $hash = strtoupper(substr(sha1($companyId.'|'.$attempt), 0, 8));
            $suffix = '-'.$hash;
            $candidate = substr($baseId, 0, 50 - strlen($suffix)).$suffix;
            $attempt++;
        } while (DB::table('ct_caja_cobro')->where('id', $candidate)->exists());

        return $candidate;
    }
};
