<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class CompanyDocumentCatalogTenantService
{
    public function repairOrphans()
    {
        if (! Schema::hasTable('documento') || ! Schema::hasTable('empresa')) {
            return 0;
        }
        $orphans = DB::table('documento')->where(function ($query) {
            $query->whereNull('id_empresa')->orWhereRaw("TRIM(id_empresa) = ''");
        });
        if (! $orphans->exists()) {
            return 0;
        }

        $companies = DB::table('empresa')->where('estado', 1)->orderBy('prioridad')->orderBy('id')->pluck('id');
        if ($companies->isEmpty()) {
            throw new \RuntimeException('No existe una empresa activa para reparar documento.');
        }

        // documento.tipo_documento posee unicidad global. Estas filas son un
        // catálogo compartido y nunca deben duplicarse por empresa. Se conserva
        // una empresa histórica únicamente por compatibilidad con código legado.
        $owner = (string) $companies->first();

        return $orphans->update(['id_empresa' => $owner]);
    }

    public function seedCompany($companyId, $sourceCompanyId)
    {
        // Es un catálogo global: una empresa nueva reutiliza las mismas filas.
        return 0;
    }
}
