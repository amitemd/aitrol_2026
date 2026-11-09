<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Sis_medico\Firma_Usuario;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_usuario;
use Sis_medico\Services\V3DoctorAgendaService;

class MedicalCertificateController extends Controller
{
    private $service;
    public function __construct(V3DoctorAgendaService $service) { $this->middleware('auth'); $this->service=$service; }

    public function create(Request $request,$doctor,$appointment)
    {
        list($agenda,$patient,$company)=$this->context($request,$doctor,$appointment);
        $physicians=DB::table('users as u')->join('usuario_empresa as ue','ue.id_usuario','=','u.id')
            ->where('ue.id_empresa',$agenda->id_empresa)->where('u.estado',1)->where('u.id_tipo_usuario',3)
            ->select('u.id','u.nombre1','u.nombre2','u.apellido1','u.apellido2')->distinct()->orderBy('u.apellido1')->get();
        $diagnosis=$this->diagnoses($agenda);
        $observation=$this->attentionName($agenda);
        $family=trim($this->clean($patient->apellido1familiar).' '.$this->clean($patient->apellido2familiar).' '.$this->clean($patient->nombre1familiar).' '.$this->clean($patient->nombre2familiar));
        $payload=compact('agenda','patient','company','doctor','physicians','diagnosis','observation','family');
        if($request->ajax()){$payload['modal']=true;return view('v3.clinical.medical_certificates.create',$payload);}
        config(array('data'=>array_merge(config('data'),array('controlador'=>'agenda-clinica','carpeta'=>'clinical','submodulo'=>'certificado-medico','submodulo_'=>'certificado-medico',
            'titulo'=>'Certificado médico','title'=>'Certificado médico | Aitrol Hospitalario','vista'=>'v3.clinical.medical_certificates.create',
            'obseraudi'=>'Ingreso a certificado médico de agenda','contenido'=>view('v3.clinical.medical_certificates.create',$payload)))));
        return view('v3.layouts.view_child');
    }

    public function pdf(Request $request,$doctor,$appointment,$filename=null)
    {
        list($agenda,$patient,$company)=$this->context($request,$doctor,$appointment);
        $this->validate($request,array('id_doctor1'=>'required','fecha'=>'required|date','desde'=>'nullable|date_format:H:i','hasta'=>'nullable|date_format:H:i',
            'descanso'=>'nullable|integer|min:1|max:365','observacion'=>'required|string|max:500','institucion'=>'required|string|max:255',
            'familiar'=>'nullable|string|max:255','diagnostico'=>'required|string|max:5000','cie10'=>'nullable|string|max:1000','sintoma'=>'required|in:si,no',
            'detalle_sintomas'=>'required_if:sintoma,si|nullable|string|max:1000','enfermedad'=>'required|string|max:1500',
            'reposo_tipo'=>'nullable|array','reposo_tipo.*'=>'in:aislamiento,reposo_absoluto,teletrabajo',
            'causa'=>'nullable|array','causa.*'=>'in:enfermedad_general,accidente_trabajo,accidente_transito,maternidad,e_catastrofica'));
        $physician=DB::table('users as u')->join('usuario_empresa as ue','ue.id_usuario','=','u.id')
            ->where('u.id',$request->input('id_doctor1'))->where('ue.id_empresa',$agenda->id_empresa)
            ->where('u.estado',1)->where('u.id_tipo_usuario',3)->select('u.*')->first();
        if (!$physician) abort(422,'El profesional seleccionado no pertenece a la clínica activa.');
        $specialty=\Sis_medico\Especialidad::query()->where('id',$agenda->espid)->first();
        $signature=Firma_Usuario::where('id_usuario',$physician->id)->first();
        $data=array('date'=>$request->input('fecha'),'from'=>$request->input('desde'),'to'=>$request->input('hasta'),
            'days'=>trim((string)$request->input('descanso'))!==''?(int)$request->input('descanso'):null,'observation'=>trim($request->input('observacion')),
            'institution'=>trim($request->input('institucion')),'family'=>trim($request->input('familiar')),
            'diagnosis'=>trim($request->input('diagnostico')),'diagnosisCode'=>trim($request->input('cie10')),'symptoms'=>$request->input('sintoma'),
            'symptomsDetail'=>trim($request->input('detalle_sintomas')),'disease'=>trim($request->input('enfermedad')),
            'restTypes'=>array_values((array)$request->input('reposo_tipo',array())),'causes'=>array_values((array)$request->input('causa',array())));
        $data['endDate']=$data['days']?date('Y-m-d',strtotime($data['date'].' +'.max(0,$data['days']-1).' days')):'';
        $data['daysInWords']=$data['days']?$this->numberInSpanish($data['days']):'';
        Log_usuario::create(array('id_usuario'=>Auth::id(),'ip_usuario'=>$request->ip(),'descripcion'=>'GENERA CERTIFICADO MEDICO V3',
            'dato_ant1'=>$agenda->id_paciente,'dato1'=>$this->patientName($patient),'dato_ant2'=>'DESCANSO: '.$data['days'].' OBSERVACION: '.$data['observation'],'dato2'=>$agenda->id));
        $pdf=app('dompdf.wrapper');
        $pdf->setOptions(array('dpi'=>96,'isRemoteEnabled'=>false,'defaultMediaType'=>'print'));
        $pdf->loadView('v3.clinical.medical_certificates.pdf',compact('agenda','patient','company','physician','specialty','signature','data'))->setPaper('letter','portrait');
        $patientIdentification=preg_replace('/[^A-Za-z0-9]+/','',(string)$patient->id);
        if($patientIdentification==='')$patientIdentification='PACIENTE';
        $downloadName='CERTIFICADO_MEDICO_'.$patientIdentification.'.pdf';
        $previewDirectory=storage_path('app/certificados_medicos_temporales');
        if(!is_dir($previewDirectory)&&!mkdir($previewDirectory,0750,true)&&!is_dir($previewDirectory))abort(500,'No se pudo preparar la vista previa del certificado.');
        foreach((array)glob($previewDirectory.DIRECTORY_SEPARATOR.'*.pdf') as $oldPreview){if(is_file($oldPreview)&&filemtime($oldPreview)<time()-7200)@unlink($oldPreview);}
        $token=bin2hex(random_bytes(32));
        $previewPath=$previewDirectory.DIRECTORY_SEPARATOR.$token.'.pdf';
        if(file_put_contents($previewPath,$pdf->output())===false)abort(500,'No se pudo guardar temporalmente el certificado.');
        $request->session()->put('medical_certificate_previews.'.$token,array(
            'user_id'=>(string)Auth::id(),'doctor'=>(string)$doctor,'appointment'=>(string)$appointment,
            'filename'=>$downloadName,'expires_at'=>time()+3600
        ));
        return redirect()->route('v3.medical-certificates.preview',array(
            'doctor'=>$doctor,'appointment'=>$appointment,'token'=>$token,'filename'=>$downloadName
        ));
    }

    public function preview(Request $request,$doctor,$appointment,$token,$filename)
    {
        $preview=$request->session()->get('medical_certificate_previews.'.$token);
        abort_unless(is_array($preview),404,'La vista previa del certificado ya no está disponible.');
        $authorized=(string)$preview['user_id']===(string)Auth::id()
            &&(string)$preview['doctor']===(string)$doctor
            &&(string)$preview['appointment']===(string)$appointment;
        abort_unless($authorized,403,'No tiene permiso para consultar este certificado.');
        $previewPath=storage_path('app/certificados_medicos_temporales/'.$token.'.pdf');
        if((int)$preview['expires_at']<time()){
            $request->session()->forget('medical_certificate_previews.'.$token);
            if(is_file($previewPath))@unlink($previewPath);
            abort(410,'La vista previa del certificado expiró. Genérela nuevamente.');
        }
        abort_unless(is_file($previewPath),404,'No se encontró el archivo temporal del certificado.');
        $downloadName=preg_replace('/[^A-Za-z0-9_.-]+/','_',basename((string)$preview['filename']));
        return response()->file($previewPath,array(
            'Content-Type'=>'application/pdf',
            'Content-Disposition'=>'inline; filename="'.$downloadName.'"; filename*=UTF-8\'\''.rawurlencode($downloadName),
            'Cache-Control'=>'private, max-age=3600'
        ));
    }

    private function context(Request $request,$doctor,$appointment)
    {
        $companyId=trim((string)$request->session()->get('id_empresa')); if($companyId==='')abort(403,'Seleccione una empresa activa.');
        try{$agenda=$this->service->agendaForAction($companyId,$doctor,$appointment);}catch(RuntimeException $e){abort(404,$e->getMessage());}
        if(!$agenda->id_paciente)abort(422,'Esta agenda no tiene un paciente asociado.');
        $patient=DB::table('paciente')->where('id',$agenda->id_paciente)->first(); $company=DB::table('empresa')->where('id',$companyId)->first();
        if(!$patient||!$company)abort(404,'No se encontraron los datos necesarios para el certificado.'); return array($agenda,$patient,$company);
    }
    private function diagnoses($agenda)
    {
        $history=DB::table('historiaclinica')->where('id_agenda',$agenda->id)->first(); if(!$history)return array();
        $rows=DB::table('hc_cie10')->where('hcid',$history->hcid)->select('cie10')->distinct()->get(); $result=array();
        foreach($rows as $row){$cie=DB::table('cie_10_3')->where('id',$row->cie10)->first();if(!$cie)$cie=DB::table('cie_10_4')->where('id',$row->cie10)->first();$result[]=trim($row->cie10.': '.($cie?$cie->descripcion:''));} return $result;
    }
    private function attentionName($agenda)
    {
        if((int)$agenda->proc_consul!==1)return 'ATENCIÓN MÉDICA';
        $procedure=DB::table('procedimiento')->where('id',$agenda->id_procedimiento)->first(); return $procedure?$procedure->nombre:'PROCEDIMIENTO MÉDICO';
    }
    private function clean($value){$value=trim((string)$value);return $value==='(N/A)'?'':$value;}
    private function patientName($p){return trim($this->clean($p->apellido1).' '.$this->clean($p->apellido2).' '.$this->clean($p->nombre1).' '.$this->clean($p->nombre2));}
    private function numberInSpanish($number)
    {
        $units=array(0=>'cero',1=>'uno',2=>'dos',3=>'tres',4=>'cuatro',5=>'cinco',6=>'seis',7=>'siete',8=>'ocho',9=>'nueve',10=>'diez',11=>'once',12=>'doce',13=>'trece',14=>'catorce',15=>'quince',16=>'dieciséis',17=>'diecisiete',18=>'dieciocho',19=>'diecinueve',20=>'veinte',21=>'veintiuno',22=>'veintidós',23=>'veintitrés',24=>'veinticuatro',25=>'veinticinco',26=>'veintiséis',27=>'veintisiete',28=>'veintiocho',29=>'veintinueve');
        $tens=array(30=>'treinta',40=>'cuarenta',50=>'cincuenta',60=>'sesenta',70=>'setenta',80=>'ochenta',90=>'noventa');
        $hundreds=array(100=>'cien',200=>'doscientos',300=>'trescientos');
        $number=(int)$number;
        if($number<30)return $units[$number];
        if($number<100){$base=(int)(floor($number/10)*10);return $tens[$base].($number%10?' y '.$units[$number%10]:'');}
        if(isset($hundreds[$number]))return $hundreds[$number];
        $base=(int)(floor($number/100)*100);
        $prefix=$base===100?'ciento':$hundreds[$base];
        $rest=$number-$base;
        if($rest<30)return $prefix.' '.$units[$rest];
        $ten=(int)(floor($rest/10)*10);
        return $prefix.' '.$tens[$ten].($rest%10?' y '.$units[$rest%10]:'');
    }
}
