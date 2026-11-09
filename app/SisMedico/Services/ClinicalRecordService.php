<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\ClinicalDocument;
use Sis_medico\ClinicalDocumentAmendment;
use Sis_medico\ClinicalDeathCase;
use Sis_medico\ClinicalEncounter;
use Sis_medico\ClinicalRecord;
use Sis_medico\Hc_Epicrisis_Final;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Form008;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;

class ClinicalRecordService
{
    public function available()
    {
        return Schema::hasTable('clinical_records') && Schema::hasTable('clinical_documents');
    }

    public function ensureRecord($patientId, array $context = array())
    {
        if (!$this->available() || !$patientId) return null;
        $record = ClinicalRecord::where('patient_id', $patientId)->first();
        if ($record) return $record;
        return ClinicalRecord::create(array(
            'id' => $this->newId(), 'patient_id' => $patientId, 'status' => 'ACTIVE',
            'opened_at' => date('Y-m-d H:i:s'), 'created_by' => $this->actorId($context),
            'ip_address' => $this->ip($context),
        ));
    }

    public function registerDocument($encounterId, $patientId, $type, $sourceType, $sourceId, array $data = array(), array $context = array())
    {
        if (!$this->available() || !$encounterId || !$patientId) return null;
        $record = $this->ensureRecord($patientId, $context);
        $fields=isset($data['fields'])?(array)$data['fields']:array();
        $content=count($fields)?$this->canonicalContent($fields):(isset($data['content'])?(string)$data['content']:null);
        $hash=$content!==null?hash('sha256',$content):null;
        $existing=ClinicalDocument::where('source_type',$sourceType)->where('source_id',(string)$sourceId)->first();
        $comparisonHash=$existing?$existing->content_hash:null;
        if($existing&&$existing->signed_at&&!empty($context['hospital_document'])&&Schema::hasTable('clinical_document_amendments')){
            // Hospital corrections may intentionally restore an earlier signed value.
            $latest=ClinicalDocumentAmendment::where('clinical_document_id',$existing->id)->orderBy('version','desc')->first();
            if($latest)$comparisonHash=$latest->new_hash;
        }
        if($existing&&$existing->signed_at&&$hash&&$hash!==$comparisonHash&&Schema::hasTable('clinical_document_amendments')){
            $reason=isset($data['amendment_reason'])?trim($data['amendment_reason']):trim((string)request()->input('amendment_reason'));
            if(strlen($reason)<5)throw new \InvalidArgumentException('La corrección de un documento firmado requiere un motivo de adenda.');
            return DB::transaction(function()use($existing,$hash,$content,$reason,$data,$context){
                $locked=ClinicalDocument::where('id',$existing->id)->lockForUpdate()->firstOrFail();
                $previous=ClinicalDocumentAmendment::where('clinical_document_id',$locked->id)->orderBy('version','desc')->lockForUpdate()->first();
                $version=$previous?(int)$previous->version+1:1;
                return ClinicalDocumentAmendment::create(array('clinical_document_id'=>$locked->id,'version'=>$version,'previous_hash'=>$previous?$previous->new_hash:$locked->content_hash,'new_hash'=>$hash,
                    'previous_content'=>$previous?$previous->amended_content:$locked->content_snapshot,'amended_content'=>$content,'reason'=>$reason,
                    'authored_by'=>isset($data['authored_by'])?$data['authored_by']:$this->actorId($context),'authored_at'=>date('Y-m-d H:i:s'),'ip_address'=>$this->ip($context)));
            },3);
        }
        if($existing&&$existing->signed_at)return $existing;
        $document=ClinicalDocument::updateOrCreate(array('source_type' => $sourceType, 'source_id' => (string) $sourceId), array(
            'clinical_record_id' => $record->id, 'encounter_id' => $encounterId,
            'document_type' => $type, 'title' => isset($data['title']) ? $data['title'] : $type,
            'status' => isset($data['status']) ? $data['status'] : 'CURRENT',
            'content_hash' => $hash, 'content_snapshot' => $content,
            'authored_by' => isset($data['authored_by']) ? $data['authored_by'] : $this->actorId($context),
            'authored_at' => isset($data['authored_at']) ? $data['authored_at'] : date('Y-m-d H:i:s'),
            'ip_address' => $this->ip($context),
        ));
        if (app(ClinicalDocumentCompletenessService::class)->available()) {
            app(ClinicalDocumentCompletenessService::class)->assess($document, isset($data['fields']) ? $data['fields'] : array(), $context);
        }
        return $document;
    }

    public function signDocument($sourceType,$sourceId,$actorId,array $context=array())
    {
        $document=ClinicalDocument::where('source_type',$sourceType)->where('source_id',(string)$sourceId)->first();if(!$document)return null;
        app(ClinicalDocumentCompletenessService::class)->assertComplete($document, $context);
        if(!$document->signed_at){$document->signed_at=date('Y-m-d H:i:s');$document->signed_by=$actorId;$document->status='SIGNED';$document->save();}return $document;
    }

    public function registerHistory(Historiaclinica $history, array $context = array())
    {
        return $this->registerDocument($history->encounter_id, $history->id_paciente, 'ENCOUNTER_RECORD',
            'historiaclinica', $history->hcid, array('title' => 'Historia del episodio', 'authored_by' => $history->id_doctor1,
                'authored_at' => $history->created_at, 'fields' => $history->toArray(), 'amendment_reason' => request()->input('amendment_reason')), $context);
    }

    public function registerEpicrisis(Hc_Epicrisis_Final $epicrisis, array $context = array())
    {
        $encounter = $epicrisis->encounter_id ? ClinicalEncounter::find($epicrisis->encounter_id) : null;
        if (!$encounter) return null;
        return $this->registerDocument($encounter->id, $encounter->id_paciente, 'EPICRISIS', 'hc_epicrisis_final',
            $epicrisis->id, array('title' => 'Epicrisis', 'content' => $epicrisis->cuadro_clinico . '|' . $epicrisis->conclusion,
                'authored_at' => $epicrisis->updated_at, 'fields' => $epicrisis->toArray(), 'amendment_reason' => request()->input('amendment_reason')), $context);
    }

    public function registerEmergencyForm(Ho_Form008 $form, array $context = array())
    {
        $encounter = $form->encounter_id ? ClinicalEncounter::find($form->encounter_id) : null;
        if (!$encounter) return null;
        return $this->registerDocument($encounter->id, $encounter->id_paciente, 'EMERGENCY_FORM_008', 'ho_form008',
            $form->id, array('title' => 'Formulario 008', 'authored_at' => $form->updated_at, 'fields' => $form->toArray(), 'amendment_reason' => request()->input('amendment_reason')), $context);
    }

    public function registerProtocol(hc_protocolo $protocol, array $context = array())
    {
        $procedure = hc_procedimientos::find($protocol->id_hc_procedimientos);
        if ($this->isPlaceholderProtocol($protocol, $procedure)) {
            $document = ClinicalDocument::where('source_type', 'hc_protocolo')
                ->where('source_id', (string) $protocol->id)->first();
            if ($document && !$document->signed_at && $document->status !== 'PLACEHOLDER') {
                $document->status = 'PLACEHOLDER';
                $document->save();
            }
            return null;
        }
        $history = $procedure ? Historiaclinica::find($procedure->id_hc) : null;
        if (!$history || !$history->encounter_id) return null;
        return $this->registerDocument($history->encounter_id, $history->id_paciente, 'PROCEDURE_REPORT', 'hc_protocolo',
            $protocol->id, array('title' => 'Informe de procedimiento',
                'content' => $protocol->hallazgos . '|' . $protocol->conclusion . '|' . $protocol->complicaciones . '|' . $protocol->estado_final,
                'authored_at' => $protocol->updated_at, 'fields' => $protocol->toArray(), 'amendment_reason' => request()->input('amendment_reason')), $context);
    }

    public function backfill()
    {
        $count = 0;
        if (!$this->available()) return $count;
        Historiaclinica::whereNotNull('encounter_id')->orderBy('hcid')->chunk(200, function ($rows) use (&$count) {
            foreach ($rows as $row) if ($this->registerHistory($row)) $count++;
        });
        Hc_Epicrisis_Final::whereNotNull('encounter_id')->orderBy('id')->chunk(100, function ($rows) use (&$count) {
            foreach ($rows as $row) if ($this->registerEpicrisis($row)) $count++;
        });
        Ho_Form008::whereNotNull('encounter_id')->orderBy('id')->chunk(100, function ($rows) use (&$count) {
            foreach ($rows as $row) if ($this->registerEmergencyForm($row)) $count++;
        });
        hc_protocolo::orderBy('id')->chunk(100, function ($rows) use (&$count) {
            foreach ($rows as $row) if ($this->registerProtocol($row)) $count++;
        });
        return $count;
    }

    public function integrity()
    {
        if (!$this->available()) return array('available' => false, 'pending' => null);
        $encountersWithoutRecord = DB::table('clinical_encounters as ce')->leftJoin('clinical_records as cr', 'cr.patient_id', '=', 'ce.id_paciente')
            ->whereNull('cr.id')->count();
        $historiesWithoutDocument = DB::table('historiaclinica as hc')->leftJoin('clinical_documents as cd', function ($join) {
            $join->on('cd.source_id', '=', DB::raw('CAST(hc.hcid AS CHAR)'))->where('cd.source_type', '=', 'historiaclinica');
        })->whereNotNull('hc.encounter_id')->whereNull('cd.id')->count();
        $formsWithoutDocument=$this->missingSourceDocuments('ho_form008','id','ho_form008');
        $epicrisisWithoutDocument=$this->missingSourceDocuments('hc_epicrisis_final','id','hc_epicrisis_final');
        $protocolsWithoutDocument=0;
        if(Schema::hasTable('hc_protocolo')&&Schema::hasTable('hc_procedimientos')){
            $missingProtocols=DB::table('hc_protocolo as hp')->join('hc_procedimientos as p','p.id','=','hp.id_hc_procedimientos')
                ->join('historiaclinica as hc','hc.hcid','=','p.id_hc')->leftJoin('clinical_documents as cd',function($join){
                    $join->on('cd.source_id','=',DB::raw('CAST(hp.id AS CHAR)'))->where('cd.source_type','=','hc_protocolo');
                })->whereNotNull('hc.encounter_id')->whereNull('cd.id')->select('hp.id','hp.id_hc_procedimientos')->get();
            foreach($missingProtocols as $missingProtocol){
                $protocol=hc_protocolo::find($missingProtocol->id);
                $procedure=hc_procedimientos::find($missingProtocol->id_hc_procedimientos);
                if($protocol&&!$this->isPlaceholderProtocol($protocol,$procedure))$protocolsWithoutDocument++;
            }
        }
        $deathCasesWithoutDocument=Schema::hasTable('clinical_death_cases')
            ?$this->missingSourceDocuments('clinical_death_cases','id','clinical_death_case'):0;
        $invalidContext=DB::table('clinical_documents as d')->leftJoin('clinical_records as r','r.id','=','d.clinical_record_id')
            ->leftJoin('clinical_encounters as e','e.id','=','d.encounter_id')->where(function($q){
                $q->whereNull('r.id')->orWhereNull('e.id')->orWhereRaw('r.patient_id <> e.id_paciente');
            })->count();
        $invalidSigned=DB::table('clinical_documents')->whereNotNull('signed_at')->where(function($q){
            $q->whereNull('signed_by')->orWhereNull('content_hash')->orWhereNull('content_snapshot')->orWhere('status','<>','SIGNED');
        })->count();
        $chainIssues=0;$sourceDivergence=0;
        if(Schema::hasTable('clinical_document_amendments'))foreach(ClinicalDocument::orderBy('id')->get() as $document){
            $expectedHash=$document->content_hash;$expectedContent=$document->content_snapshot;$expectedVersion=1;
            foreach(ClinicalDocumentAmendment::where('clinical_document_id',$document->id)->orderBy('version')->get() as $amendment){
                if((int)$amendment->version!==$expectedVersion||$amendment->previous_hash!==$expectedHash
                    ||$amendment->previous_content!==$expectedContent||hash('sha256',(string)$amendment->amended_content)!==$amendment->new_hash
                    ||strlen(trim((string)$amendment->reason))<5||empty($amendment->authored_by))$chainIssues++;
                $expectedHash=$amendment->new_hash;$expectedContent=$amendment->amended_content;$expectedVersion++;
            }
            $current=$this->sourceContent($document);
            if(!is_null($current)&&hash('sha256',$current)!==$expectedHash)$sourceDivergence++;
        }
        $checks=array('encounters_without_record'=>$encountersWithoutRecord,'histories_without_document'=>$historiesWithoutDocument,
            'forms_without_document'=>$formsWithoutDocument,'epicrisis_without_document'=>$epicrisisWithoutDocument,
            'protocols_without_document'=>$protocolsWithoutDocument,'death_cases_without_document'=>$deathCasesWithoutDocument,
            'documents_with_invalid_context'=>$invalidContext,'invalid_signed_documents'=>$invalidSigned,
            'invalid_amendment_chain'=>$chainIssues,'source_without_matching_snapshot_or_amendment'=>$sourceDivergence);
        return array('available'=>true,'pending'=>array_sum($checks),'checks'=>$checks)+$checks;
    }

    private function missingSourceDocuments($table,$key,$sourceType)
    {
        if(!Schema::hasTable($table)||!Schema::hasColumn($table,'encounter_id'))return 0;
        return DB::table($table.' as s')->leftJoin('clinical_documents as d',function($join)use($key,$sourceType){
            $join->on('d.source_id','=',DB::raw('CAST(s.'.$key.' AS CHAR)'))->where('d.source_type','=',$sourceType);
        })->whereNotNull('s.encounter_id')->whereNull('d.id')->count();
    }

    private function sourceContent(ClinicalDocument $document)
    {
        $source=null;
        if($document->source_type==='historiaclinica')$source=Historiaclinica::find($document->source_id);
        elseif($document->source_type==='hc_epicrisis_final')$source=Hc_Epicrisis_Final::find($document->source_id);
        elseif($document->source_type==='ho_form008')$source=Ho_Form008::find($document->source_id);
        elseif($document->source_type==='hc_protocolo')$source=hc_protocolo::find($document->source_id);
        elseif($document->source_type==='clinical_death_case')$source=ClinicalDeathCase::find($document->source_id);
        return $source?$this->canonicalContent($source->toArray()):null;
    }

    /**
     * Los flujos históricos pueden crear un protocolo técnico antes de que el
     * profesional documente el procedimiento. Un registro completamente vacío
     * no acredita un acto clínico y se conserva como marcador. En cuanto recibe
     * contenido clínico deja de ser marcador y exige su documento longitudinal.
     */
    private function isPlaceholderProtocol(hc_protocolo $protocol, $procedure)
    {
        if (!$procedure) return false;
        foreach (array('hallazgos', 'conclusion', 'complicaciones', 'estado_final') as $field) {
            $value = trim(str_replace('&nbsp;', ' ', strip_tags((string) $protocol->getAttribute($field))));
            if ($value !== '') return false;
        }
        return true;
    }

    private function actorId(array $context) { return isset($context['actor_id']) ? $context['actor_id'] : (Auth::check() ? Auth::user()->id : null); }
    private function ip(array $context) { return isset($context['ip']) ? $context['ip'] : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null); }
    private function newId() { $hex = md5(uniqid((string) mt_rand(), true)); return substr($hex,0,8).'-'.substr($hex,8,4).'-'.substr($hex,12,4).'-'.substr($hex,16,4).'-'.substr($hex,20,12); }
    private function canonicalContent(array $fields)
    {
        foreach(array('updated_at','created_at','ip_creacion','ip_modificacion','id_usuariomod') as $field)unset($fields[$field]);
        $normalize=function($value)use(&$normalize){
            if(!is_array($value))return $value;
            ksort($value);foreach($value as $key=>$item)$value[$key]=$normalize($item);return $value;
        };
        return json_encode($normalize($fields),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
