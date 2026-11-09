<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class ClinicalHospitalContextAuditService
{
    public function inspect($repair = false)
    {
        if (!$this->available()) {
            return array('available' => false, 'pending' => 1, 'repaired' => 0, 'companies' => array());
        }

        $candidates = $this->candidateHospitals();
        $issues = array();
        $repaired = 0;

        $invalidMappings = DB::table('company_hospital_contexts as c')
            ->leftJoin('hospital as h', 'h.id', '=', 'c.hospital_id')
            ->where('c.active', 1)->where(function ($query) {
                $query->whereNull('h.id')->orWhere('h.estado', '<>', 1);
                if (Schema::hasColumn('hospital', 'id_empresa')) {
                    $query->orWhereNull('h.id_empresa')->orWhereColumn('h.id_empresa', '<>', 'c.company_id');
                }
            })->select('c.company_id', 'c.hospital_id')->get();
        foreach ($invalidMappings as $mapping) {
            $issues[] = array('company_id' => (string) $mapping->company_id, 'reason' => 'INVALID_OR_INACTIVE_HOSPITAL',
                'hospital_ids' => array((int) $mapping->hospital_id), 'default_ids' => array(), 'requires_selection' => true);
        }

        $duplicateDefaults = DB::table('company_hospital_contexts')->where('active', 1)->where('is_default', 1)
            ->select('company_id', DB::raw('COUNT(*) as total'))->groupBy('company_id')->havingRaw('COUNT(*) > 1')->get();
        foreach ($duplicateDefaults as $duplicate) {
            $issues[] = array('company_id' => (string) $duplicate->company_id, 'reason' => 'MULTIPLE_CONFIGURED_DEFAULTS',
                'hospital_ids' => array(), 'default_ids' => array(), 'requires_selection' => true);
        }

        foreach ($candidates as $companyId => $hospitalIds) {
            $defaults = DB::table('company_hospital_contexts as c')
                ->join('hospital as h', 'h.id', '=', 'c.hospital_id')
                ->where('c.company_id', $companyId)
                ->where('c.active', 1)
                ->where('c.is_default', 1)
                ->where('h.estado', 1);
            if (Schema::hasColumn('hospital', 'id_empresa')) $defaults->whereColumn('h.id_empresa', 'c.company_id');
            $defaults = $defaults
                ->pluck('c.hospital_id')->map(function ($id) { return (int) $id; })->all();

            $validDefaults = array_values(array_intersect(array_unique($hospitalIds), array_unique($defaults)));
            if (count($validDefaults) === 1) continue;

            $reason = count($validDefaults) > 1 ? 'MULTIPLE_DEFAULTS' : 'DEFAULT_MISSING';
            if ($repair && count($hospitalIds) === 1) {
                $this->setDefault($companyId, $hospitalIds[0]);
                $repaired++;
                continue;
            }

            $issues[] = array(
                'company_id' => (string) $companyId,
                'reason' => $reason,
                'hospital_ids' => array_values(array_unique($hospitalIds)),
                'default_ids' => $validDefaults,
                'requires_selection' => count($hospitalIds) > 1,
            );
        }

        $issues = $this->uniqueIssues($issues);
        return array('available' => true, 'pending' => count($issues), 'repaired' => $repaired, 'companies' => $issues);
    }

    private function candidateHospitals()
    {
        $rows = DB::table('agenda as a')
            ->join('sala as s', 's.id', '=', 'a.id_sala')
            ->join('hospital as h', 'h.id', '=', 's.id_hospital')
            ->whereNotNull('a.id_empresa')->where('s.estado', 1)->where('h.estado', 1);
        if (Schema::hasColumn('sala', 'id_empresa')) $rows->whereColumn('s.id_empresa', 'a.id_empresa');
        if (Schema::hasColumn('hospital', 'id_empresa')) $rows->whereColumn('h.id_empresa', 'a.id_empresa');
        $rows = $rows->select('a.id_empresa as company_id', 's.id_hospital as hospital_id')->distinct()->get();
        $companies = array();
        foreach ($rows as $row) $companies[(string) $row->company_id][] = (int) $row->hospital_id;
        foreach ($companies as $company => $ids) $companies[$company] = array_values(array_unique($ids));
        return $companies;
    }

    private function setDefault($companyId, $hospitalId)
    {
        DB::transaction(function () use ($companyId, $hospitalId) {
            DB::table('company_hospital_contexts')->where('company_id', $companyId)
                ->update(array('is_default' => 0, 'updated_at' => date('Y-m-d H:i:s')));
            $query = DB::table('company_hospital_contexts')->where('company_id', $companyId)->where('hospital_id', $hospitalId);
            if ($query->exists()) {
                $query->update(array('active' => 1, 'is_default' => 1, 'updated_at' => date('Y-m-d H:i:s')));
            } else {
                DB::table('company_hospital_contexts')->insert(array('company_id' => $companyId, 'hospital_id' => $hospitalId,
                    'active' => 1, 'is_default' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')));
            }
        });
    }

    private function uniqueIssues(array $issues)
    {
        $unique = array();
        foreach ($issues as $issue) $unique[$issue['company_id'].'|'.$issue['reason']] = $issue;
        return array_values($unique);
    }

    private function available()
    {
        return Schema::hasTable('company_hospital_contexts') && Schema::hasTable('agenda')
            && Schema::hasTable('sala') && Schema::hasTable('hospital');
    }
}
