<?php
namespace Sis_medico\Services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\ClinicalDeathCase;
use Sis_medico\ClinicalDeathEvent;
use Sis_medico\ClinicalEncounter;
class ClinicalDeathService
{
    public function available(){return Schema::hasTable('clinical_death_cases')&&Schema::hasTable('clinical_death_events');}
    public function blockers(array $data)
    {
        $errors=array();
        foreach(array('death_occurred_at'=>'fecha y hora de la defunción','death_pronounced_by'=>'profesional que constató la defunción','death_pronounced_at'=>'fecha y hora de constatación','death_location'=>'lugar de la defunción','death_certificate_number'=>'número del certificado','death_family_contact'=>'familiar o contacto notificado','death_family_notified_at'=>'hora de notificación familiar','death_morgue_notified_at'=>'hora de notificación a morgue') as $key=>$label)
            if(!isset($data[$key])||trim((string)$data[$key])==='')$errors[]='Complete '.$label.'.';
        if(!isset($data['death_probable_cause'])||strlen(trim($data['death_probable_cause']))<5)$errors[]='Documente la causa probable de muerte.';
        if(!isset($data['death_manner'])||!in_array($data['death_manner'],array('NATURAL','ACCIDENT','VIOLENT','UNDETERMINED'),true))$errors[]='Seleccione la manera de muerte.';
        if(empty($data['death_identity_verified']))$errors[]='Confirme la identidad del paciente fallecido.';
        if(empty($data['death_body_identified']))$errors[]='Confirme la identificación segura del cuerpo.';
        $occurred=$this->timestamp(isset($data['death_occurred_at'])?$data['death_occurred_at']:null);
        $pronounced=$this->timestamp(isset($data['death_pronounced_at'])?$data['death_pronounced_at']:null);
        $family=$this->timestamp(isset($data['death_family_notified_at'])?$data['death_family_notified_at']:null);
        $morgue=$this->timestamp(isset($data['death_morgue_notified_at'])?$data['death_morgue_notified_at']:null);
        if($occurred&&$pronounced&&$pronounced<$occurred)$errors[]='La constatación no puede ser anterior a la defunción.';
        if($occurred&&$family&&$family<$occurred)$errors[]='La notificación familiar no puede ser anterior a la defunción.';
        if($occurred&&$morgue&&$morgue<$occurred)$errors[]='La notificación a morgue no puede ser anterior a la defunción.';
        return $errors;
    }
    public function complete(ClinicalEncounter $encounter,$closureId,array $data,array $context=array())
    {
        if(!$this->available())throw new \RuntimeException('La migración del circuito de defunción no está aplicada.');
        $errors=$this->blockers($data);if(count($errors))throw new \InvalidArgumentException(implode(' ',$errors));
        $actor=$this->actorId($context);$case=ClinicalDeathCase::firstOrNew(array('encounter_id'=>$encounter->id));
        $case->fill(array('closure_id'=>$closureId,'patient_id'=>$encounter->id_paciente,'status'=>'COMPLETED',
            'occurred_at'=>$data['death_occurred_at'],'pronounced_by'=>$data['death_pronounced_by'],'pronounced_at'=>$data['death_pronounced_at'],
            'death_location'=>trim($data['death_location']),'probable_cause'=>trim($data['death_probable_cause']),'death_manner'=>$data['death_manner'],
            'certificate_number'=>trim($data['death_certificate_number']),'identity_verified'=>1,'body_identified'=>1,
            'family_contact_name'=>trim($data['death_family_contact']),'family_notified_at'=>$data['death_family_notified_at'],'family_notified_by'=>$actor,
            'morgue_notified_at'=>$data['death_morgue_notified_at'],'morgue_notified_by'=>$actor,'notes'=>isset($data['death_notes'])?trim($data['death_notes']):null,
            'completed_by'=>$actor,'completed_at'=>date('Y-m-d H:i:s'),'ip_address'=>$this->ip($context)));
        $case->save();$this->event($case,'DEATH_WORKFLOW_COMPLETED','Identidad, constatación, certificado, familia y morgue verificados.',$context);
        if(app(ClinicalRecordService::class)->available())app(ClinicalRecordService::class)->registerDocument($encounter->id,$encounter->id_paciente,'DEATH_RECORD','clinical_death_case',$case->id,
            array('title'=>'Registro de defunción','content'=>$case->certificate_number.'|'.$case->probable_cause,'authored_by'=>$actor,'fields'=>$case->toArray()),$context);
        return $case;
    }
    public function integrity()
    {
        if(!$this->available()||!Schema::hasTable('clinical_encounter_closures'))return array('available'=>false,'pending'=>null);
        $closedWithoutCase=ClinicalEncounter::where('clinical_encounters.status','CLOSED_DECEASED')->leftJoin('clinical_death_cases as dc','dc.encounter_id','=','clinical_encounters.id')
            ->where(function($q){$q->whereNull('dc.id')->orWhere('dc.status','<>','COMPLETED');})->count();
        $incompleteCases=ClinicalDeathCase::where('status','COMPLETED')->where(function($q){
            foreach(array('occurred_at','pronounced_by','pronounced_at','death_location','probable_cause','death_manner','certificate_number',
                'family_contact_name','family_notified_at','family_notified_by','morgue_notified_at','morgue_notified_by','completed_by','completed_at') as $field){
                $q->orWhereNull($field);if(!in_array($field,array('occurred_at','pronounced_at','family_notified_at','morgue_notified_at','completed_at'),true))$q->orWhere($field,'');
            }
            $q->orWhere('identity_verified','<>',1)->orWhere('body_identified','<>',1);
        })->count();
        $withoutEvent=ClinicalDeathCase::where('status','COMPLETED')->whereDoesntHave('events',function($q){
            $q->where('event_type','DEATH_WORKFLOW_COMPLETED');
        })->count();
        $closureMismatch=DB::table('clinical_death_cases as dc')
            ->leftJoin('clinical_encounters as ce','ce.id','=','dc.encounter_id')
            ->leftJoin('clinical_encounter_closures as cc','cc.id','=','dc.closure_id')
            ->where('dc.status','COMPLETED')->where(function($q){
                $q->whereNull('ce.id')->orWhere('ce.status','<>','CLOSED_DECEASED')
                    ->orWhereNull('cc.id')->orWhere('cc.status','<>','COMPLETED')
                    ->orWhere('cc.closure_type','<>','DECEASED')
                    ->orWhereRaw('cc.encounter_id <> dc.encounter_id');
            })->count();
        $misclassifiedDischarge=0;
        if(Schema::hasTable('hc_epicrisis_final')&&Schema::hasColumn('hc_epicrisis_final','encounter_id')&&Schema::hasColumn('hc_epicrisis_final','defuncion')){
            $misclassifiedDischarge=DB::table('clinical_encounters as ce')->join('hc_epicrisis_final as ep','ep.encounter_id','=','ce.id')
                ->where('ce.status','CLOSED_DISCHARGED')->whereNotNull('ep.defuncion')->where('ep.defuncion','<>','')
                ->whereRaw("UPPER(TRIM(ep.defuncion)) NOT IN ('NO','0','N/A')")->distinct()->count('ce.id');
        }
        return array('available'=>true,
            'pending'=>$closedWithoutCase+$incompleteCases+$withoutEvent+$closureMismatch+$misclassifiedDischarge,
            'closed_without_case'=>$closedWithoutCase,'incomplete_cases'=>$incompleteCases,'without_completion_event'=>$withoutEvent,
            'closure_mismatch'=>$closureMismatch,'misclassified_discharge'=>$misclassifiedDischarge);
    }
    private function event($case,$type,$detail,array $context){ClinicalDeathEvent::create(array('clinical_death_case_id'=>$case->id,'event_type'=>$type,'detail'=>$detail,'actor_id'=>$this->actorId($context),'ip_address'=>$this->ip($context)));}
    private function actorId(array $context){return isset($context['actor_id'])?$context['actor_id']:(Auth::check()?Auth::user()->id:null);}
    private function ip(array $context){return isset($context['ip'])?$context['ip']:(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:null);}
    private function timestamp($value){if(!$value)return null;$time=strtotime($value);return $time===false?null:$time;}
}
