<?php

namespace Sis_medico\Services;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class PersonRoleTenantService
{
    public function constrainEloquent(EloquentBuilder $query, $role)
    {
        $companyId = trim((string) session('id_empresa'));
        if ($companyId === '') return $query;
        if (!Schema::hasTable('personas') || !Schema::hasTable('persona_empresa')) {
            return $query->whereRaw('1 = 0');
        }

        $from = (string) $query->getQuery()->from;
        $qualifier = $query->getModel()->getTable();
        if (preg_match('/\s+as\s+([A-Za-z0-9_]+)$/i', $from, $matches)) $qualifier = $matches[1];

        return $query->whereExists(function ($membership) use ($qualifier, $companyId, $role) {
            $membership->select(DB::raw(1))->from('persona_empresa as person_company')
                ->whereColumn('person_company.persona_id', $qualifier.'.persona_id')
                ->where('person_company.id_empresa', $companyId)
                ->where('person_company.rol', strtoupper($role))
                ->where('person_company.estado', 1);
        });
    }

    public function setRole($personId, $role, $active = true, $companyId = null, $actorId = null, $ip = null)
    {
        $companyId = trim((string) ($companyId ?: session('id_empresa')));
        if (!$personId || $companyId === '' || !Schema::hasTable('persona_empresa')) return false;
        $now = date('Y-m-d H:i:s');
        $key = array('persona_id'=>$personId, 'id_empresa'=>$companyId, 'rol'=>strtoupper($role));
        $existing = DB::table('persona_empresa')->where($key)->first();
        $values = array('estado'=>$active ? 1 : 0, 'id_usuariomod'=>$actorId,
            'ip_modificacion'=>$ip, 'updated_at'=>$now);
        if ($existing) return DB::table('persona_empresa')->where('id', $existing->id)->update($values) >= 0;
        $values = array_merge($key, $values, array('id_usuariocrea'=>$actorId,
            'ip_creacion'=>$ip, 'created_at'=>$now));
        return DB::table('persona_empresa')->insert($values);
    }
}
