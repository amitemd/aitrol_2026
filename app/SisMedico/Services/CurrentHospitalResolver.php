<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Support\RequestQueryCache;
use Illuminate\Support\Facades\Session;
use Sis_medico\Hospital;

/**
 * Resolves the hospital in the current institutional context without relying
 * on legacy numeric constants. Ambiguous contexts deliberately return null so
 * callers can show all active rooms instead of silently using a wrong site.
 */
class CurrentHospitalResolver
{
    /** @var array Resultados reutilizados durante la solicitud HTTP actual. */
    private $resolved = array();

    public function id(array $context = array())
    {
        $cacheKey = md5(serialize(array($context, Session::get('id_empresa'), Session::get('id_hospital'))));
        if (array_key_exists($cacheKey, $this->resolved)) return $this->resolved[$cacheKey];

        $companyId = isset($context['company_id']) ? $context['company_id'] : Session::get('id_empresa');
        $fromEncounter = $this->fromClinicalContext($context, $companyId);
        if ($fromEncounter) return $this->resolved[$cacheKey] = $fromEncounter;

        $sessionHospital = Session::get('id_hospital');
        if ($sessionHospital && (!$companyId || Session::get('hospital_company_id') == $companyId)
            && $this->activeHospitalId($sessionHospital, $companyId)) {
            return $this->resolved[$cacheKey] = (int) $sessionHospital;
        }

        if ($companyId && Schema::hasTable('company_hospital_contexts')) {
            $configuredQuery = DB::table('company_hospital_contexts as chc')
                ->join('hospital as h', 'h.id', '=', 'chc.hospital_id')
                ->where('chc.company_id', $companyId)->where('chc.active', 1)->where('chc.is_default', 1);
            if (Schema::hasColumn('hospital', 'id_empresa')) $configuredQuery->where('h.id_empresa', (string) $companyId);
            $configured = $configuredQuery->value('chc.hospital_id');
            if ($configured && $this->activeHospitalId($configured, $companyId)) {
                $this->remember($configured, $companyId);
                return $this->resolved[$cacheKey] = (int) $configured;
            }
        }

        if ($companyId && Schema::hasTable('agenda') && Schema::hasTable('sala')) {
            $hospitals = DB::table('agenda as a')
                ->join('sala as s', 's.id', '=', 'a.id_sala')
                ->join('hospital as h', 'h.id', '=', 's.id_hospital')
                ->where('a.id_empresa', $companyId)
                ->where('s.estado', 1)
                ->where('h.estado', 1)
                ->when(Schema::hasColumn('sala', 'id_empresa'), function ($query) use ($companyId) {
                    return $query->where('s.id_empresa', (string) $companyId);
                })
                ->when(Schema::hasColumn('hospital', 'id_empresa'), function ($query) use ($companyId) {
                    return $query->where('h.id_empresa', (string) $companyId);
                })
                ->distinct()->pluck('h.id');

            if ($hospitals->count() === 1) {
                $this->remember($hospitals->first(), $companyId);
                return $this->resolved[$cacheKey] = (int) $hospitals->first();
            }
        }

        if ($companyId) return $this->resolved[$cacheKey] = null;

        $active = Hospital::where('estado', 1)->pluck('id');
        if ($active->count() === 1) {
            $this->remember($active->first(), $companyId);
            return $this->resolved[$cacheKey] = (int) $active->first();
        }

        return $this->resolved[$cacheKey] = null;
    }

    private function fromClinicalContext(array $context, $companyId = null)
    {
        if (!empty($context['agenda_id']) && Schema::hasTable('agenda') && Schema::hasTable('sala')) {
            $query = DB::table('agenda as a')->join('sala as s','s.id','=','a.id_sala')->where('a.id',$context['agenda_id']);
            if ($companyId) $query->where('a.id_empresa', (string) $companyId);
            return $this->activeHospitalId($query->value('s.id_hospital'), $companyId);
        }
        if (!empty($context['solicitud_id']) && Schema::hasTable('ho_traspaso_sala008') && Schema::hasTable('sala')) {
            $hospital=DB::table('ho_traspaso_sala008 as t')->join('sala as s','s.id','=','t.id_sala')->where('t.id_solicitud',$context['solicitud_id'])
                ->orderBy('t.id','desc')->value('s.id_hospital');if($hospital)return $this->activeHospitalId($hospital, $companyId);
        }
        if (!empty($context['solicitud_id']) && Schema::hasTable('ho_solicitud')) {
            $agenda=DB::table('ho_solicitud')->where('id',$context['solicitud_id'])->value('id_agenda');if($agenda)return $this->fromClinicalContext(array('agenda_id'=>$agenda), $companyId);
        }
        return null;
    }

    public function activeHospitalId($hospitalId, $companyId = null)
    {
        if (!$hospitalId) return null;
        $companyId = $companyId ?: Session::get('id_empresa');
        return RequestQueryCache::remember(__METHOD__.json_encode([(string) $hospitalId, (string) $companyId]), function () use ($hospitalId, $companyId) {
            $query = Hospital::where('id', $hospitalId)->where('estado', 1);
            if ($companyId && Schema::hasColumn('hospital', 'id_empresa')) $query->where('id_empresa', (string) $companyId);
            return $query->exists() ? (int) $hospitalId : null;
        }, null, false, ['hospital']);
    }

    private function remember($hospitalId,$companyId)
    {
        Session::put('id_hospital',(int)$hospitalId);if($companyId)Session::put('hospital_company_id',(string)$companyId);
    }
}
