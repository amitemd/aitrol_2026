<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;

class AuditProtocolCompanyGuard
{
    /**
     * A conflict exists only when the history has no protocol for the active
     * company, but does have an unassigned or differently assigned protocol.
     */
    public function hasConflict($historyId, $companyId): bool
    {
        $protocols = DB::table('aud_hc_protocolo as p')
            ->where('p.hcid', $historyId)
            ->join('aud_hc_procedimientos as hp', 'hp.id_procedimientos_org', 'p.id_hc_procedimientos');

        $hasActiveCompanyProtocol = (clone $protocols)
            ->where('hp.id_empresa', $companyId)
            ->exists();

        if ($hasActiveCompanyProtocol) {
            return false;
        }

        return $protocols
            ->where(function ($query) use ($companyId) {
                $query->whereNull('hp.id_empresa')
                    ->orWhere('hp.id_empresa', '<>', $companyId);
            })
            ->exists();
    }
}
