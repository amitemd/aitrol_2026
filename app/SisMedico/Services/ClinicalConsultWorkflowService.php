<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\ClinicalConsultEvent;
use Sis_medico\ClinicalConsultRequest;
use Sis_medico\ClinicalEncounter;
use Sis_medico\Exceptions\ClinicalConsultWorkflowException;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Ordenes;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Interconsulta;

class ClinicalConsultWorkflowService
{
    const REQUESTED='REQUESTED'; const ACCEPTED='ACCEPTED'; const IN_REVIEW='IN_REVIEW';
    const COMPLETED='COMPLETED'; const CANCELLED='CANCELLED'; const ESCALATED='ESCALATED';

    public function available()
    {
        return Schema::hasTable('clinical_consult_requests') && Schema::hasTable('clinical_consult_events');
    }

    public function sync(Interconsulta $legacy, $priority = null, array $context = array())
    {
        if (!$this->available()) return null;
        $priority = $priority ? strtoupper($priority) : null;
        if ($priority && !in_array($priority, array('ROUTINE','URGENT','STAT'), true)) {
            throw new ClinicalConsultWorkflowException('Prioridad de interconsulta no permitida.');
        }
        $request = ClinicalConsultRequest::where('interconsulta_id', $legacy->id)->first();
        $encounter = $request ? ClinicalEncounter::find($request->encounter_id) : $this->resolveEncounter($legacy);
        if (!$encounter) return null;
        $requestedAt = $legacy->fecha ?: $legacy->created_at ?: Carbon::now();
        $effectivePriority = $priority ?: ($request ? $request->priority : 'ROUTINE');
        $minutes = $this->slaMinutes($effectivePriority);

        if (!$request) {
            $request = ClinicalConsultRequest::create(array(
                'encounter_id'=>$encounter->id, 'patient_id'=>$legacy->id_paciente, 'interconsulta_id'=>$legacy->id,
                'priority'=>$effectivePriority, 'status'=>self::REQUESTED, 'target_service'=>$legacy->servicio,
                'target_specialty'=>$legacy->especialidad, 'requested_by'=>$legacy->id_doctor ?: $legacy->id_usuariocrea,
                'requested_at'=>$requestedAt, 'due_at'=>Carbon::parse($requestedAt)->addMinutes($minutes),
                'escalation_level'=>0, 'ip_address'=>$this->ip($context),
            ));
            $this->event($request, 'REQUEST_CREATED', null, self::REQUESTED, 'Interconsulta incorporada al workflow clínico.', $context);
        } else {
            $priorityChanged = $request->priority !== $effectivePriority;
            $request->priority = $effectivePriority;
            $request->target_service = $legacy->servicio;
            $request->target_specialty = $legacy->especialidad;
            if ($priorityChanged && !in_array($request->status, array(self::COMPLETED,self::CANCELLED), true)) {
                $request->due_at = Carbon::parse($request->requested_at)->addMinutes($minutes);
                $this->event($request, 'PRIORITY_CHANGED', $request->status, $request->status,
                    'Prioridad actualizada a ' . $effectivePriority . '.', $context);
            }
            $request->save();
        }
        return $request;
    }

    public function transition(ClinicalConsultRequest $request, $to, array $data = array(), array $context = array())
    {
        return DB::transaction(function () use ($request,$to,$data,$context) {
            $request = ClinicalConsultRequest::lockForUpdate()->find($request->id);
            app(ClinicalAccessControlService::class)->assertAllowed($request->encounter_id, 'consult.update', $context);
            $map = array(
                self::REQUESTED=>array(self::ACCEPTED,self::CANCELLED), self::ESCALATED=>array(self::ACCEPTED,self::CANCELLED),
                self::ACCEPTED=>array(self::IN_REVIEW,self::COMPLETED,self::CANCELLED),
                self::IN_REVIEW=>array(self::COMPLETED,self::CANCELLED), self::COMPLETED=>array(), self::CANCELLED=>array(),
            );
            if (!isset($map[$request->status]) || !in_array($to,$map[$request->status],true)) {
                throw new ClinicalConsultWorkflowException('Transición de interconsulta no permitida.');
            }
            if ($to===self::CANCELLED && strlen(trim(isset($data['reason'])?$data['reason']:''))<5) {
                throw new ClinicalConsultWorkflowException('La cancelación requiere un motivo clínico.');
            }
            if ($to===self::COMPLETED && strlen(trim(isset($data['response_summary'])?$data['response_summary']:''))<10) {
                throw new ClinicalConsultWorkflowException('La revisión debe incluir una respuesta clínica.');
            }
            $from=$request->status; $now=Carbon::now(); $request->status=$to;
            if ($to===self::ACCEPTED) { $request->accepted_at=$now; $request->assigned_to=isset($data['assigned_to'])?$data['assigned_to']:$this->actorId($context); }
            if ($to===self::IN_REVIEW) $request->reviewed_at=$now;
            if ($to===self::COMPLETED) { $request->reviewed_at=$request->reviewed_at?:$now; $request->completed_at=$now; $request->response_summary=trim($data['response_summary']); }
            $request->save(); $this->event($request,'STATUS_CHANGED',$from,$to,isset($data['reason'])?$data['reason']:null,$context);
            return $request;
        });
    }

    public function escalateOverdue(array $context = array())
    {
        $count=0;
        foreach (ClinicalConsultRequest::whereIn('status',array(self::REQUESTED,self::ACCEPTED,self::IN_REVIEW,self::ESCALATED))
            ->where('due_at','<=',Carbon::now())->get() as $request) {
            $from=$request->status;
            if (in_array($from,array(self::REQUESTED,self::ESCALATED),true)) $request->status=self::ESCALATED;
            $request->escalation_level=(int)$request->escalation_level+1;
            $request->last_escalated_at=Carbon::now();
            $request->due_at=Carbon::now()->addMinutes($this->slaMinutes($request->priority));
            $request->save();
            $this->event($request,'SLA_ESCALATED',$from,$request->status,'SLA vencido; escalamiento nivel '.$request->escalation_level.'.',$context);
            $count++;
        }
        return $count;
    }

    public function backfill()
    {
        $count=0; if (!$this->available()) return $count;
        Interconsulta::orderBy('id')->chunk(200,function($rows) use (&$count){ foreach($rows as $row) if($this->sync($row)) $count++; });
        return $count;
    }

    public function integrity()
    {
        if (!$this->available()) return array('available'=>false,'pending'=>null,'overdue_without_escalation'=>null);
        $historyMissing=DB::table('interconsulta as i')->join('historiaclinica as hc','hc.hcid','=','i.id_hc')
            ->whereNotNull('hc.encounter_id')->leftJoin('clinical_consult_requests as cc','cc.interconsulta_id','=','i.id')
            ->whereNull('cc.id')->count();
        $hospitalMissing=DB::table('ho_ordenes as ho')->join('ho_solicitud as hs','hs.id','=','ho.id_ho_solicitud')
            ->where('ho.tipo','INTERCONSULTA')->whereNotNull('hs.encounter_id')
            ->leftJoin('clinical_consult_requests as cc','cc.interconsulta_id','=','ho.clave_tipo')->whereNull('cc.id')->count();
        $overdue=ClinicalConsultRequest::whereIn('priority',array('URGENT','STAT'))
            ->whereIn('status',array(self::REQUESTED,self::ACCEPTED,self::IN_REVIEW))
            ->where('due_at','<',Carbon::now())->where('escalation_level',0)->count();
        $orphan=ClinicalConsultRequest::whereDoesntHave('encounter')->count();
        $withoutEvent=ClinicalConsultRequest::whereDoesntHave('events')->count();
        $invalidCompleted=ClinicalConsultRequest::where('status',self::COMPLETED)->where(function($q){$q->whereNull('completed_at')->orWhereNull('response_summary')->orWhere('response_summary','');})->count();
        $invalidAccepted=ClinicalConsultRequest::whereIn('status',array(self::ACCEPTED,self::IN_REVIEW))->where(function($q){$q->whereNull('accepted_at')->orWhereNull('assigned_to');})->count();
        $legacyMissing=$historyMissing+$hospitalMissing;
        return array('available'=>true,'pending'=>$legacyMissing+$overdue+$orphan+$withoutEvent+$invalidCompleted+$invalidAccepted,
            'legacy_without_workflow'=>$legacyMissing,'overdue_without_escalation'=>$overdue,'orphan'=>$orphan,
            'without_event'=>$withoutEvent,'invalid_completed'=>$invalidCompleted,'invalid_accepted'=>$invalidAccepted);
    }

    private function resolveEncounter(Interconsulta $legacy)
    {
        if ($legacy->id_hc) { $history=Historiaclinica::find($legacy->id_hc); if($history&&$history->encounter_id) return ClinicalEncounter::find($history->encounter_id); }
        $link=Ho_Ordenes::where('clave_tipo',$legacy->id)->where('tipo','INTERCONSULTA')->first();
        if($link){$solicitud=Ho_Solicitud::find($link->id_ho_solicitud);if($solicitud&&$solicitud->encounter_id)return ClinicalEncounter::find($solicitud->encounter_id);}
        $at=$legacy->fecha?:$legacy->created_at;if(!$at)return null;
        $matches=ClinicalEncounter::where('id_paciente',$legacy->id_paciente)->where('started_at','<=',$at)
            ->where(function($q)use($at){$q->whereNull('ended_at')->orWhere('ended_at','>=',$at);})->limit(2)->get();
        return $matches->count()===1?$matches->first():null;
    }
    private function slaMinutes($priority){$map=config('clinical.consult_sla_minutes');return isset($map[$priority])?(int)$map[$priority]:1440;}
    private function event($request,$type,$from,$to,$reason,array $context){ClinicalConsultEvent::create(array('clinical_consult_request_id'=>$request->id,'event_type'=>$type,'from_status'=>$from,'to_status'=>$to,'reason'=>$reason,'actor_id'=>$this->actorId($context),'ip_address'=>$this->ip($context)));}
    private function actorId(array $context){return isset($context['actor_id'])?$context['actor_id']:(Auth::check()?Auth::user()->id:null);}
    private function ip(array $context){return isset($context['ip'])?$context['ip']:(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:null);}
}
