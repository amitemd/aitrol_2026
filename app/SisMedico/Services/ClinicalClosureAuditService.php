<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\ClinicalEncounter;
use Sis_medico\ClinicalEncounterClosure;
use Sis_medico\Ho_Solicitud;

class ClinicalClosureAuditService
{
    public function inspect($repair = false)
    {
        if (!$this->available()) return array('available'=>false,'pending'=>null,'repaired'=>0);
        $repaired = $repair ? $this->repairCompletedClosures() : 0;
        $closedStates = array(ClinicalStateTransitionService::CLOSED_DISCHARGED, ClinicalStateTransitionService::CLOSED_DECEASED);
        $closedWithoutTransaction = DB::table('clinical_encounters as e')
            ->leftJoin('clinical_encounter_closures as c', function ($join) {
                $join->on('c.encounter_id','=','e.id')->where('c.status','=','COMPLETED');
            })->whereIn('e.status',$closedStates)->whereNull('c.id')->count();
        $requestWithoutTransaction = DB::table('ho_solicitud as s')
            ->leftJoin('clinical_encounter_closures as c', function ($join) {
                $join->on('c.solicitud_id','=','s.id')->where('c.status','=','COMPLETED');
            })->where('s.estado',0)->whereNotNull('s.encounter_id')->whereNull('c.id')->count();
        $inconsistentCompleted = DB::table('clinical_encounter_closures as c')
            ->join('clinical_encounters as e','e.id','=','c.encounter_id')
            ->leftJoin('ho_solicitud as s','s.id','=','c.solicitud_id')
            ->where('c.status','COMPLETED')->where(function ($query) use ($closedStates) {
                $query->whereNotIn('e.status',$closedStates)->orWhereNull('e.ended_at')
                    ->orWhere(function ($request) { $request->whereNotNull('c.solicitud_id')->where('s.estado','<>',0); });
            })->count();
        return array('available'=>true,'closed_without_transaction'=>$closedWithoutTransaction,
            'requests_without_transaction'=>$requestWithoutTransaction,'inconsistent_completed'=>$inconsistentCompleted,
            'pending'=>$closedWithoutTransaction+$requestWithoutTransaction+$inconsistentCompleted,'repaired'=>$repaired);
    }

    private function repairCompletedClosures()
    {
        $count=0;
        ClinicalEncounterClosure::where('status','COMPLETED')->orderBy('id')->chunk(100,function($closures)use(&$count){
            foreach($closures as $closure){
                app(ClinicalClosureGuard::class)->run(function()use($closure,&$count){
                    $encounter=ClinicalEncounter::find($closure->encounter_id);
                    $state=$closure->closure_type===ClinicalEncounterClosureService::DECEASED
                        ? ClinicalStateTransitionService::CLOSED_DECEASED:ClinicalStateTransitionService::CLOSED_DISCHARGED;
                    if($encounter&&($encounter->status!==$state||empty($encounter->ended_at))){$encounter->status=$state;$encounter->ended_at=$encounter->ended_at?:$closure->signed_at;$encounter->close_reason=$closure->closure_type;$encounter->save();$count++;}
                    if($closure->solicitud_id){$request=Ho_Solicitud::find($closure->solicitud_id);if($request&&((string)$request->estado!=='0'||empty($request->fecha_salida))){$request->estado=0;$request->fecha_salida=$request->fecha_salida?:$closure->signed_at;$request->save();$count++;}}
                });
            }
        });
        return $count;
    }

    private function available()
    {
        return Schema::hasTable('clinical_encounter_closures')&&Schema::hasTable('clinical_encounters')
            &&Schema::hasTable('ho_solicitud')&&Schema::hasColumn('ho_solicitud','encounter_id');
    }
}
