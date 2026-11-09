<?php
namespace Sis_medico\Services;
use Illuminate\Support\Facades\Auth;use Sis_medico\Support\RequestSchema as Schema;use Sis_medico\ClinicalAccessEvent;use Sis_medico\ClinicalAccessScope;use Sis_medico\ClinicalBreakGlassAccess;use Sis_medico\ClinicalEncounter;use Sis_medico\Exceptions\ClinicalAccessDeniedException;
class ClinicalAccessControlService
{
 public function available(){return Schema::hasTable('clinical_access_scopes')&&Schema::hasTable('clinical_break_glass_accesses')&&Schema::hasTable('clinical_access_events');}
 public function assertAllowed($encounterId,$action,array $context=array())
 {
  if(!$this->available())throw new ClinicalAccessDeniedException('La autorización clínica granular no está disponible.');$user=$this->user($context);$enc=ClinicalEncounter::find($encounterId);
  return $this->checkAllowed($enc,$encounterId,$user,$action,$context);
 }
 /** Autoriza un episodio ya cargado por el listado, conservando la política y su auditoría. */
 public function assertAllowedForEncounter(ClinicalEncounter $enc,$action,array $context=array())
 {
  if(!$this->available())throw new ClinicalAccessDeniedException('La autorización clínica granular no está disponible.');
  return $this->checkAllowed($enc,$enc->id,$this->user($context),$action,$context);
 }
 private function checkAllowed($enc,$encounterId,$user,$action,array $context)
 {
  if(!$user||!$enc){$this->event($encounterId,$user?$user->id:null,$action,'DENIED','NONE','Usuario o episodio inválido.',$context);throw new ClinicalAccessDeniedException('No se pudo validar el acceso al episodio.');}
  $isSystemUser=method_exists($user,'isSystemUser')&&$user->isSystemUser();
  if($isSystemUser){$this->event($enc->id,$user->id,$action,'ALLOWED','SYSTEM_ROLE','Acceso explícito del rol Sistemas sin restricción de empresa activa.',$context);return true;}
  $companyId=session('id_empresa');
  if(empty($companyId)||(string)$enc->id_empresa!==(string)$companyId){$this->event($enc->id,$user->id,$action,'DENIED','TENANT','El episodio no pertenece a la empresa activa.',$context);throw new ClinicalAccessDeniedException('El episodio no pertenece a la empresa activa.');}
  $now=date('Y-m-d H:i:s');$serviceCode=isset($context['service_code'])?trim($context['service_code']):null;$scope=ClinicalAccessScope::where('active',1)->where(function($q)use($user){$q->where('user_id',$user->id)->orWhere('role_id',$user->id_tipo_usuario);})
   ->where(function($q)use($enc){$q->whereNull('encounter_id')->orWhere('encounter_id',$enc->id);})->where(function($q)use($enc){$q->whereNull('care_setting')->orWhere('care_setting',$enc->care_setting);})
   ->where(function($q)use($serviceCode){$q->whereNull('service_code');if($serviceCode)$q->orWhere('service_code',$serviceCode);})
   ->where(function($q)use($action){$q->where('action',$action)->orWhere('action','*');})->where(function($q)use($now){$q->whereNull('valid_from')->orWhere('valid_from','<=',$now);})->where(function($q)use($now){$q->whereNull('valid_until')->orWhere('valid_until','>=',$now);})->exists();
  if($scope||(string)$enc->id_usuariocrea===(string)$user->id){$this->event($enc->id,$user->id,$action,'ALLOWED','SCOPE',$scope?'Alcance clínico vigente.':'Creador responsable del episodio.',$context);return true;}
  $breakGlass=ClinicalBreakGlassAccess::where('encounter_id',$enc->id)->where('user_id',$user->id)->where('status','ACTIVE')->where('expires_at','>=',$now)->where(function($q)use($action){$q->where('action',$action)->orWhere('action','*');})->first();
  if($breakGlass){$this->event($enc->id,$user->id,$action,'ALLOWED','BREAK_GLASS','Acceso excepcional '.$breakGlass->id,$context,$breakGlass->id);return true;}
  $this->event($enc->id,$user->id,$action,'DENIED','SCOPE','Sin alcance clínico vigente.',$context);throw new ClinicalAccessDeniedException('No tiene alcance sobre este episodio. Solicite acceso excepcional si existe una urgencia asistencial.');
 }
 public function activateBreakGlass($encounterId,$action,$reason,array $context=array())
 {
  if(!$this->available())throw new ClinicalAccessDeniedException('La migración de acceso clínico no está aplicada.');$user=$this->user($context);$reason=trim($reason);
  if(!$user||!ClinicalEncounter::find($encounterId))throw new ClinicalAccessDeniedException('Usuario o episodio inválido.');if(strlen($reason)<20)throw new ClinicalAccessDeniedException('La razón clínica debe explicar la urgencia con al menos 20 caracteres.');
  $action=$action?:'*';$existing=ClinicalBreakGlassAccess::where('encounter_id',$encounterId)->where('user_id',$user->id)->where('action',$action)->where('status','ACTIVE')->where('expires_at','>=',date('Y-m-d H:i:s'))->first();if($existing)throw new ClinicalAccessDeniedException('Ya existe un acceso excepcional activo para esta acción y episodio.');
  $now=time();$access=ClinicalBreakGlassAccess::create(array('encounter_id'=>$encounterId,'user_id'=>$user->id,'action'=>$action,'reason'=>$reason,'status'=>'ACTIVE','activated_at'=>date('Y-m-d H:i:s',$now),'expires_at'=>date('Y-m-d H:i:s',$now+3600),'review_due_at'=>date('Y-m-d H:i:s',$now+86400),'ip_address'=>$this->ip($context)));
  $this->event($encounterId,$user->id,$action,'ACTIVATED','BREAK_GLASS',$reason,$context,$access->id);return $access;
 }
 public function review(ClinicalBreakGlassAccess $access,$decision,$notes,array $context=array())
 {
  $reviewer=$this->user($context);if(!$reviewer||$reviewer->id===$access->user_id)throw new ClinicalAccessDeniedException('El acceso excepcional debe ser revisado por otra persona.');if(!in_array($decision,array('APPROVED','REJECTED'),true))throw new ClinicalAccessDeniedException('Decisión de revisión inválida.');$notes=trim($notes);if(strlen($notes)<10)throw new ClinicalAccessDeniedException('La revisión debe incluir una conclusión de al menos 10 caracteres.');
  $access->status='REVIEWED';$access->reviewed_by=$reviewer->id;$access->reviewed_at=date('Y-m-d H:i:s');$access->review_decision=$decision;$access->review_notes=$notes;$access->save();$this->event($access->encounter_id,$access->user_id,$access->action,'REVIEWED','BREAK_GLASS',$decision.': '.$notes,$context,$access->id);return $access;
 }
 public function integrity(){if(!$this->available())return array('available'=>false,'pending'=>null);$now=date('Y-m-d H:i:s');ClinicalBreakGlassAccess::where('status','ACTIVE')->where('expires_at','<',$now)->update(array('status'=>'EXPIRED'));$overdue=ClinicalBreakGlassAccess::whereIn('status',array('ACTIVE','EXPIRED'))->where('review_due_at','<',$now)->whereNull('reviewed_at')->count();$invalid=ClinicalBreakGlassAccess::whereRaw('CHAR_LENGTH(TRIM(reason)) < 20')->orWhereRaw('expires_at <= activated_at')->orWhere(function($q){$q->whereNotNull('reviewed_at')->where(function($r){$r->whereNull('reviewed_by')->orWhereRaw('reviewed_by = user_id')->orWhereNull('review_decision')->orWhereRaw('CHAR_LENGTH(TRIM(review_notes)) < 10');});})->count();$orphan=ClinicalBreakGlassAccess::whereDoesntHave('encounter')->count();$withoutEvent=0;foreach(ClinicalBreakGlassAccess::all() as $access){$query=ClinicalAccessEvent::where('decision','ACTIVATED');if(Schema::hasColumn('clinical_access_events','break_glass_access_id'))$query->where('break_glass_access_id',$access->id);else $query->where('encounter_id',$access->encounter_id)->where('user_id',$access->user_id)->where('action',$access->action);if(!$query->exists())$withoutEvent++;}$duplicateActive=ClinicalBreakGlassAccess::select('encounter_id','user_id','action')->where('status','ACTIVE')->groupBy('encounter_id','user_id','action')->havingRaw('COUNT(*) > 1')->get()->count();$pending=$overdue+$invalid+$orphan+$withoutEvent+$duplicateActive;return array('available'=>true,'overdue'=>$overdue,'invalid'=>$invalid,'orphan'=>$orphan,'without_event'=>$withoutEvent,'duplicate_active'=>$duplicateActive,'pending'=>$pending);}
 private function event($enc,$user,$action,$decision,$source,$detail,array $context,$accessId=null){$data=array('encounter_id'=>$enc,'user_id'=>$user,'action'=>$action,'decision'=>$decision,'source'=>$source,'detail'=>$detail,'ip_address'=>$this->ip($context));if(Schema::hasColumn('clinical_access_events','break_glass_access_id'))$data['break_glass_access_id']=$accessId;ClinicalAccessEvent::create($data);}
 private function user(array $context){return isset($context['user'])?$context['user']:(Auth::check()?Auth::user():null);}private function ip(array $context){return isset($context['ip'])?$context['ip']:(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:null);}
}
