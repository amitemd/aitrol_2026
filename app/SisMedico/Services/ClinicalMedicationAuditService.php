<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class ClinicalMedicationAuditService
{
    public function inspect()
    {
        if (!$this->available()) return array('available'=>false,'pending'=>null);
        $invalidAdministrations=DB::table('clinical_medication_administrations as a')
            ->leftJoin('clinical_medication_orders as o','o.id','=','a.medication_order_id')
            ->where(function($query){
                $query->whereNull('o.id')->orWhereRaw('a.encounter_id <> o.encounter_id')
                    ->orWhereRaw('a.patient_id <> o.patient_id')->orWhereNull('a.dose')->orWhere('a.dose','')
                    ->orWhereNull('a.route')->orWhere('a.route','')->orWhereNull('a.scheduled_at')->orWhereNull('a.administered_at')
                    ->orWhereNull('a.administered_by')->orWhere('a.administered_by','')->orWhere('a.patient_verified','<>',1)
                    ->orWhere('a.medication_verified','<>',1)->orWhere('a.dose_verified','<>',1)
                    ->orWhere('a.route_verified','<>',1)->orWhere('a.time_verified','<>',1)
                    ->orWhere(function($administered){$administered->where('a.status','ADMINISTERED')->where(function($result){$result->whereNull('a.clinical_result')->orWhere('a.clinical_result','');});})
                    ->orWhere(function($omitted){$omitted->where('a.status','OMITTED')->where(function($reason){$reason->whereNull('a.omission_reason')->orWhere('a.omission_reason','');});})
                    ->orWhereNotIn('a.status',array('ADMINISTERED','OMITTED'));
            })->count();
        $pendingExceptions=Schema::hasTable('clinical_emergency_medication_exceptions')
            ? DB::table('clinical_emergency_medication_exceptions')->where('status','PENDING')->count():0;
        $overdueExceptions=Schema::hasTable('clinical_emergency_medication_exceptions')
            ? DB::table('clinical_emergency_medication_exceptions')->where('status','PENDING')->where('regularization_due_at','<',date('Y-m-d H:i:s'))->count():0;
        return array('available'=>true,'invalid_administrations'=>$invalidAdministrations,
            'pending_exceptions'=>$pendingExceptions,'overdue_exceptions'=>$overdueExceptions,
            'pending'=>$invalidAdministrations+$overdueExceptions);
    }

    private function available()
    {
        return Schema::hasTable('clinical_medication_orders')&&Schema::hasTable('clinical_medication_administrations')
            &&Schema::hasTable('clinical_emergency_medication_exceptions');
    }
}
