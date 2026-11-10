<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class CompanyConfigurationTenantService
{
    public function repairOrphans()
    {
        if (! Schema::hasTable('ct_configuraciones') || ! Schema::hasTable('empresa')) {
            return 0;
        }
        $orphans = DB::table('ct_configuraciones')->where(function ($query) {
            $query->whereNull('id_empresa')->orWhereRaw("TRIM(id_empresa) = ''");
        });
        if (! $orphans->exists()) {
            return 0;
        }

        $companies = DB::table('empresa')->where('estado', 1)->orderBy('prioridad')->orderBy('id')->pluck('id');
        if ($companies->isEmpty()) {
            throw new \RuntimeException('No existe una empresa activa para reparar ct_configuraciones.');
        }

        $repaired = 0;
        $orphans->orderBy('id')->chunk(100, function ($rows) use ($companies, &$repaired) {
            foreach ($rows as $row) {
                $owner = (string) $companies->first();
                DB::table('ct_configuraciones')->where('id', $row->id)->update(['id_empresa' => $owner]);
                $repaired++;
                foreach ($companies as $companyId) {
                    if ((string) $companyId === $owner) {
                        continue;
                    }
                    $repaired += $this->copyRowIfMissing($row, (string) $companyId);
                }
            }
        });

        return $repaired;
    }

    public function seedCompany($companyId, $sourceCompanyId)
    {
        $companyId = trim((string) $companyId);
        $sourceCompanyId = trim((string) $sourceCompanyId);
        if ($companyId === '' || $sourceCompanyId === '' || $companyId === $sourceCompanyId
            || ! Schema::hasTable('ct_configuraciones')) {
            return 0;
        }

        $created = 0;
        foreach (DB::table('ct_configuraciones')->where('id_empresa', $sourceCompanyId)->get() as $row) {
            $created += $this->copyRowIfMissing($row, $companyId);
        }

        return $created;
    }

    private function copyRowIfMissing($row, $companyId)
    {
        $query = DB::table('ct_configuraciones')->where('id_empresa', $companyId);
        if (! empty($row->tipo)) {
            $query->where('tipo', $row->tipo);
        } elseif (! empty($row->id_plan)) {
            $query->where('id_plan', $row->id_plan)->where('nombre', $row->nombre);
        } else {
            $query->where('nombre', $row->nombre);
        }
        if ($query->exists()) {
            return 0;
        }

        $values = (array) $row;
        unset($values['id']);
        $values['id_empresa'] = $companyId;
        $values['created_at'] = date('Y-m-d H:i:s');
        $values['updated_at'] = date('Y-m-d H:i:s');
        $values['ip_creacion'] = 'TENANT_REPAIR';
        $values['ip_modificacion'] = 'TENANT_REPAIR';
        DB::table('ct_configuraciones')->insert($values);

        return 1;
    }
}
