<?php
namespace Sis_medico\Services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\ClinicalConsent;
use Sis_medico\ClinicalConsentEvent;
use Sis_medico\ClinicalControlRollout;
use Sis_medico\ClinicalProcedureAuthorization;
use Sis_medico\Exceptions\ClinicalProcedureBlockedException;
use Sis_medico\Exceptions\ClinicalAccessDeniedException;
use Sis_medico\Formato_consentimiento;
use Sis_medico\hc_procedimientos;
class ClinicalProcedureSafetyService
{
    public function available(){return Schema::hasTable('clinical_consents')&&Schema::hasTable('clinical_procedure_authorizations')&&Schema::hasTable('clinical_consent_events')&&Schema::hasTable('clinical_control_rollouts')&&ClinicalControlRollout::where('control_key','procedure_safety')->exists();}
    public function authorizeForProcedure($procedureId,array $data,array $context=array())
    {
        if(!$this->available())throw new ClinicalProcedureBlockedException('La migración de seguridad preprocedimiento no está aplicada.');
        return DB::transaction(function () use ($procedureId,$data,$context) {
            // El procedimiento es el candado canónico. Así dos solicitudes
            // simultáneas no crean consentimientos o autorizaciones duplicadas.
            $procedure=hc_procedimientos::with('historia')->where('id',$procedureId)->lockForUpdate()->first();
            $history=$procedure?$procedure->historia:null;
            if(!$procedure||!$history||empty($history->encounter_id))throw new ClinicalProcedureBlockedException('El procedimiento no está vinculado a un episodio clínico canónico.');
            try {
                app(ClinicalAccessControlService::class)->assertAllowed($history->encounter_id,'procedure.authorize',array(
                    'user'=>\Sis_medico\User::find($this->actor($context)),'ip'=>$this->ip($context),'service_code'=>'PROCEDURES'));
            } catch (ClinicalAccessDeniedException $exception) {
                throw new ClinicalProcedureBlockedException($exception->getMessage());
            }
            $key='hc_procedimientos:'.$procedure->id;
            $existing=ClinicalProcedureAuthorization::where('procedure_key',$key)->orderBy('id','desc')->lockForUpdate()->first();
            if($existing&&$existing->status==='AUTHORIZED')return $existing;
            if($existing&&$existing->status==='USED')throw new ClinicalProcedureBlockedException('El procedimiento ya fue iniciado y no admite una nueva autorización.');
            $errors=array();$indication=trim(isset($data['procedure_indication'])?$data['procedure_indication']:'');
            $identity=!empty($data['procedure_identity_verified']);$emergency=!empty($data['procedure_emergency_exception']);
            if(strlen($indication)<5)$errors[]='Documente la indicación clínica del procedimiento.';
            if(!$identity)$errors[]='Verifique la identidad del paciente antes del procedimiento.';
            $consent=null;$checklist=!empty($data['procedure_checklist_complete']);$reason=null;
            if($emergency){
                $reason=trim(isset($data['procedure_emergency_reason'])?$data['procedure_emergency_reason']:'');
                if(strlen($reason)<10)$errors[]='Documente el motivo clínico de la excepción de emergencia.';
            }else{
                if(!$checklist)$errors[]='Complete el checklist de seguridad preprocedimiento.';
                $consent=$this->buildConsent($history,$key,$data,$context,$errors);
            }
            if(count($errors))throw new ClinicalProcedureBlockedException(implode(' ',$errors));
            if($consent)$consent->save();
            $actor=$this->actor($context);$authorization=ClinicalProcedureAuthorization::create(array(
                'encounter_id'=>$history->encounter_id,'patient_id'=>$history->id_paciente,'procedure_key'=>$key,'indication'=>$indication,
                'consent_id'=>$consent?$consent->id:null,'identity_verified'=>1,'checklist_complete'=>$emergency?0:1,
                'checklist_snapshot'=>json_encode(array('identity'=>true,'indication'=>true,'consent'=>$consent?true:false,'checklist'=>$checklist)),
                'emergency_exception'=>$emergency?1:0,'emergency_reason'=>$emergency?$reason:null,'exception_authorized_by'=>$emergency?$actor:null,
                'status'=>'AUTHORIZED','authorized_by'=>$actor,'authorized_at'=>date('Y-m-d H:i:s'),'ip_address'=>$this->ip($context)));
            $this->event($history->encounter_id,$consent?$consent->id:null,$authorization->id,$emergency?'EMERGENCY_EXCEPTION_AUTHORIZED':'PROCEDURE_AUTHORIZED',$indication,$context);
            return $authorization;
        },3);
    }
    public function consume(ClinicalProcedureAuthorization $authorization,$sourceType,$sourceId,array $context=array())
    {
        $authorizationId=$authorization->id;
        return DB::transaction(function () use ($authorizationId,$sourceType,$sourceId,$context) {
            $locked=ClinicalProcedureAuthorization::where('id',$authorizationId)->lockForUpdate()->first();
            if(!$locked)throw new ClinicalProcedureBlockedException('La autorización preprocedimiento no existe.');
            if($locked->status==='USED'&&$locked->used_source_type===$sourceType&&(string)$locked->used_source_id===(string)$sourceId)return $locked;
            if($locked->status!=='AUTHORIZED')throw new ClinicalProcedureBlockedException('La autorización preprocedimiento ya no está vigente.');
            $locked->status='USED';$locked->used_source_type=$sourceType;$locked->used_source_id=(string)$sourceId;$locked->used_at=date('Y-m-d H:i:s');$locked->save();
            $this->event($locked->encounter_id,$locked->consent_id,$locked->id,'PROCEDURE_STARTED',$sourceType.':'.$sourceId,$context);
            return $locked;
        },3);
    }
    public function authorizeExistingProtocol($procedureId,$protocolId,array $data,array $context=array())
    {
        if(!$this->available())throw new ClinicalProcedureBlockedException('La migración de seguridad preprocedimiento no está aplicada.');
        return DB::transaction(function () use ($procedureId,$protocolId,$data,$context) {
            // Serializa también el atajo usado por protocolos existentes. La
            // misma operación puede reintentarse; otra fuente queda bloqueada.
            $procedure=hc_procedimientos::where('id',$procedureId)->lockForUpdate()->first();
            if(!$procedure)throw new ClinicalProcedureBlockedException('El procedimiento clínico no existe.');
            $key='hc_procedimientos:'.$procedureId;
            $used=ClinicalProcedureAuthorization::where('procedure_key',$key)
                ->where('status','USED')->where('used_source_type','hc_protocolo')
                ->where('used_source_id',(string)$protocolId)->lockForUpdate()->first();
            if($used)return $used;
            $otherUse=ClinicalProcedureAuthorization::where('procedure_key',$key)->where('status','USED')->lockForUpdate()->first();
            if($otherUse)throw new ClinicalProcedureBlockedException('El procedimiento ya fue iniciado desde otro registro clínico.');
            $authorization=$this->authorizeForProcedure($procedureId,$data,$context);
            return $this->consume($authorization,'hc_protocolo',$protocolId,$context);
        },3);
    }
    public function integrity()
    {
        if(!$this->available())return array('available'=>false,'pending'=>null);
        $stale=ClinicalProcedureAuthorization::where('status','AUTHORIZED')->where('authorized_at','<',date('Y-m-d H:i:s',time()-3600))->count();
        $invalidEmergency=ClinicalProcedureAuthorization::where('emergency_exception',1)->where(function($q){$q->whereNull('emergency_reason')->orWhereNull('exception_authorized_by');})->count();
        $invalidUsed=ClinicalProcedureAuthorization::where('status','USED')->where(function($q){
            $q->whereNull('used_source_type')->orWhereNull('used_source_id')->orWhereNull('used_at');
        })->count();
        $invalidStandard=ClinicalProcedureAuthorization::where('emergency_exception',0)->where(function($q){
            $q->whereNull('consent_id')->orWhere('checklist_complete','<>',1)->orWhere('identity_verified','<>',1);
        })->count();
        $invalidConsent=ClinicalConsent::where(function($q){
            $q->where('status','<>','SIGNED')->orWhere('identity_verified','<>',1)
                ->orWhere(function($expiry){$expiry->whereNotNull('expires_at')->where('expires_at','<=',date('Y-m-d H:i:s'));});
        })->count();
        $missingAuthorizationEvents=ClinicalProcedureAuthorization::whereNotExists(function($q){
            $q->select(DB::raw(1))->from('clinical_consent_events as ce')
                ->whereRaw('ce.authorization_id = clinical_procedure_authorizations.id')
                ->whereIn('ce.event_type',array('PROCEDURE_AUTHORIZED','EMERGENCY_EXCEPTION_AUTHORIZED'));
        })->count();
        $missingStartEvents=ClinicalProcedureAuthorization::where('status','USED')->whereNotExists(function($q){
            $q->select(DB::raw(1))->from('clinical_consent_events as ce')
                ->whereRaw('ce.authorization_id = clinical_procedure_authorizations.id')
                ->where('ce.event_type','PROCEDURE_STARTED');
        })->count();
        $invalidContext=DB::table('clinical_procedure_authorizations as a')
            ->leftJoin('hc_procedimientos as hp',DB::raw("CONCAT('hc_procedimientos:', hp.id)"),'=','a.procedure_key')
            ->leftJoin('historiaclinica as h','h.hcid','=','hp.id_hc')
            ->where(function($q){$q->whereNull('hp.id')->orWhereNull('h.encounter_id')
                ->orWhereRaw('a.encounter_id <> h.encounter_id')->orWhereRaw('a.patient_id <> h.id_paciente');})->count();
        $invalidConsentContext=DB::table('clinical_procedure_authorizations as a')->join('clinical_consents as c','c.id','=','a.consent_id')
            ->where(function($q){$q->whereRaw('a.encounter_id <> c.encounter_id')->orWhereRaw('a.patient_id <> c.patient_id')
                ->orWhereRaw('a.procedure_key <> c.procedure_key');})->count();
        $missingProcedureGuards=0;
        if(Schema::hasTable('hc_protocolo')&&Schema::hasTable('hc_procedimientos')){
            $cutoff=$this->procedureSafetyEnforcedAt();
            $missingProcedureGuards=DB::table('hc_protocolo as p')->join('hc_procedimientos as hp','hp.id','=','p.id_hc_procedimientos')
                ->where('p.created_at','>=',$cutoff)->whereNotExists(function($q){
                    $q->select(DB::raw(1))->from('clinical_procedure_authorizations as a')
                        ->whereRaw("a.procedure_key = CONCAT('hc_procedimientos:', p.id_hc_procedimientos)")
                        ->where('a.status','USED')->where('a.used_source_type','hc_protocolo')
                        ->whereRaw('CAST(a.used_source_id AS UNSIGNED) = p.id');
                })->count();
        }
        $pending=$stale+$invalidEmergency+$invalidUsed+$invalidStandard+$invalidConsent+$missingAuthorizationEvents+$missingStartEvents+$invalidContext+$invalidConsentContext+$missingProcedureGuards;
        return array('available'=>true,'pending'=>$pending,'stale_authorizations'=>$stale,'invalid_emergency_exceptions'=>$invalidEmergency,
            'invalid_used_authorizations'=>$invalidUsed,'invalid_standard_authorizations'=>$invalidStandard,'invalid_consents'=>$invalidConsent,
            'missing_authorization_events'=>$missingAuthorizationEvents,'missing_start_events'=>$missingStartEvents,
            'invalid_context'=>$invalidContext,'invalid_consent_context'=>$invalidConsentContext,'unguarded_procedure_starts'=>$missingProcedureGuards);
    }

    /**
     * Reconstruye exclusivamente eventos cuya evidencia ya existe en la
     * autorización. No crea consentimientos, autorizaciones ni excepciones.
     *
     * El bloqueo de la autorización y firstOrCreate hacen que la operación sea
     * idempotente incluso cuando dos auditorías se ejecutan al mismo tiempo.
     */
    public function repairEvidenceEvents()
    {
        if (!$this->available()) return array('available' => false, 'authorization_events' => 0, 'start_events' => 0);

        $authorizationIds = ClinicalProcedureAuthorization::whereNotExists(function ($query) {
            $query->select(DB::raw(1))->from('clinical_consent_events as ce')
                ->whereRaw('ce.authorization_id = clinical_procedure_authorizations.id')
                ->whereIn('ce.event_type', array('PROCEDURE_AUTHORIZED', 'EMERGENCY_EXCEPTION_AUTHORIZED'));
        })->pluck('id')->all();

        $startIds = ClinicalProcedureAuthorization::where('status', 'USED')->whereNotExists(function ($query) {
            $query->select(DB::raw(1))->from('clinical_consent_events as ce')
                ->whereRaw('ce.authorization_id = clinical_procedure_authorizations.id')
                ->where('ce.event_type', 'PROCEDURE_STARTED');
        })->pluck('id')->all();

        $repairedAuthorizationEvents = 0;
        foreach ($authorizationIds as $authorizationId) {
            $created = DB::transaction(function () use ($authorizationId) {
                $authorization = ClinicalProcedureAuthorization::where('id', $authorizationId)->lockForUpdate()->first();
                if (!$authorization) return false;
                $eventType = $authorization->emergency_exception ? 'EMERGENCY_EXCEPTION_AUTHORIZED' : 'PROCEDURE_AUTHORIZED';
                $event = ClinicalConsentEvent::firstOrCreate(
                    array('authorization_id' => $authorization->id, 'event_type' => $eventType),
                    array(
                        'encounter_id' => $authorization->encounter_id,
                        'consent_id' => $authorization->consent_id,
                        'detail' => $authorization->indication,
                        'actor_id' => $authorization->authorized_by,
                        'ip_address' => $authorization->ip_address,
                        'created_at' => $authorization->authorized_at,
                        'updated_at' => $authorization->authorized_at,
                    )
                );
                return $event->wasRecentlyCreated;
            });
            if ($created) $repairedAuthorizationEvents++;
        }

        $repairedStartEvents = 0;
        foreach ($startIds as $authorizationId) {
            $created = DB::transaction(function () use ($authorizationId) {
                $authorization = ClinicalProcedureAuthorization::where('id', $authorizationId)->lockForUpdate()->first();
                if (!$authorization || $authorization->status !== 'USED' || !$authorization->used_source_type || !$authorization->used_source_id || !$authorization->used_at) return false;
                $event = ClinicalConsentEvent::firstOrCreate(
                    array('authorization_id' => $authorization->id, 'event_type' => 'PROCEDURE_STARTED'),
                    array(
                        'encounter_id' => $authorization->encounter_id,
                        'consent_id' => $authorization->consent_id,
                        // La fuente histórica conserva la hora y el registro
                        // consumidor, pero no quién inició el procedimiento.
                        // Evitamos atribuir esa acción al autorizador.
                        'detail' => 'RECONSTRUIDO_DESDE_AUTORIZACION|'.$authorization->used_source_type.':'.$authorization->used_source_id,
                        'actor_id' => null,
                        'ip_address' => $authorization->ip_address,
                        'created_at' => $authorization->used_at,
                        'updated_at' => $authorization->used_at,
                    )
                );
                return $event->wasRecentlyCreated;
            });
            if ($created) $repairedStartEvents++;
        }

        return array(
            'available' => true,
            'authorization_events' => $repairedAuthorizationEvents,
            'start_events' => $repairedStartEvents,
        );
    }
    private function buildConsent($history,$key,array $data,array $context,array &$errors)
    {
        $templateId=isset($data['consent_template_id'])?$data['consent_template_id']:null;$template=Formato_consentimiento::where('id',$templateId)->where('estado',1)->first();
        $version=trim(isset($data['consent_version'])?$data['consent_version']:'');$signedBy=trim(isset($data['consent_signed_by'])?$data['consent_signed_by']:'');
        $signedAt=isset($data['consent_signed_at'])?$data['consent_signed_at']:null;$role=isset($data['consent_signer_role'])?$data['consent_signer_role']:null;
        if(!$template)$errors[]='Seleccione un formato de consentimiento vigente.';if($version==='')$errors[]='Registre la versión del consentimiento.';
        if(strlen($signedBy)<3)$errors[]='Registre quién otorgó el consentimiento.';if(!$signedAt||strtotime($signedAt)===false||strtotime($signedAt)>time())$errors[]='Registre una fecha válida de firma del consentimiento.';
        if(!in_array($role,array('PATIENT','REPRESENTATIVE'),true))$errors[]='Seleccione quién firma el consentimiento.';
        $expiresAt=isset($data['consent_expires_at'])?$data['consent_expires_at']:null;if($expiresAt&&strtotime($expiresAt)!==false&&strtotime($expiresAt)<=time())$errors[]='El consentimiento seleccionado ya no está vigente.';
        $relationship=trim(isset($data['consent_representative_relationship'])?$data['consent_representative_relationship']:'');if($role==='REPRESENTATIVE'&&$relationship==='')$errors[]='Indique la relación del representante con el paciente.';
        if(count($errors))return null;$snapshot=array('id'=>$template->id,'name'=>$template->nombre,'consiste'=>$template->consiste,'beneficios'=>$template->beneficios,'riesgos_frecuentes'=>$template->riesgos_frecuentes,'riesgos_infrecuentes'=>$template->riesgos_infrecuentes,'alternativas'=>$template->alternativas,'version'=>$version);
        return new ClinicalConsent(array('encounter_id'=>$history->encounter_id,'patient_id'=>$history->id_paciente,'template_id'=>$template->id,'template_version'=>$version,'procedure_key'=>$key,
            'status'=>'SIGNED','signed_at'=>$signedAt,'signed_by'=>$signedBy,'signer_role'=>$role,'representative_relationship'=>$role==='REPRESENTATIVE'?$relationship:null,
            'identity_verified'=>1,'expires_at'=>$expiresAt&&$expiresAt!==''?$expiresAt:null,
            'content_hash'=>hash('sha256',json_encode($snapshot)),'evidence_reference'=>isset($data['consent_evidence_reference'])?trim($data['consent_evidence_reference']):null,
            'recorded_by'=>$this->actor($context),'ip_address'=>$this->ip($context)));
    }
    private function event($encounter,$consent,$authorization,$type,$detail,array $context){ClinicalConsentEvent::firstOrCreate(array('authorization_id'=>$authorization,'event_type'=>$type),array('encounter_id'=>$encounter,'consent_id'=>$consent,'detail'=>$detail,'actor_id'=>$this->actor($context),'ip_address'=>$this->ip($context)));}
    private function procedureSafetyEnforcedAt()
    {
        $configured=config('clinical.procedure_safety_enforced_at');
        if($configured)return $configured;
        return ClinicalControlRollout::where('control_key','procedure_safety')->value('enforced_at');
    }
    private function actor(array $context){return isset($context['actor_id'])?$context['actor_id']:(Auth::check()?Auth::user()->id:null);}
    private function ip(array $context){return isset($context['ip'])?$context['ip']:(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:null);}
}
