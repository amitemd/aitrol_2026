<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Sis_medico\Agenda_consentimiento;
use Sis_medico\Formato_consentimiento;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3DoctorAgendaService;

class AgendaConsentController extends Controller
{
    private $service;
    public function __construct(V3DoctorAgendaService $service) { $this->middleware('auth'); $this->service=$service; }

    public function index(Request $request, $doctor, $appointment)
    {
        list($agenda,$patient,$company)=$this->context($request,$doctor,$appointment);
        $formats=Formato_consentimiento::where('estado',1)
            ->where(function($q)use($agenda){$q->whereNull('id_empresa')->orWhere('id_empresa',$agenda->id_empresa);})
            ->where(function($q)use($agenda){$q->whereNull('id_procedimiento')->orWhere('id_procedimiento',$agenda->id_procedimiento);})
            ->orderBy('nombre')->get();
        $history=DB::table('agenda_consentimiento as ac')->join('formato_consentimiento as fc','fc.id','=','ac.formato')
            ->leftJoin('users as u','u.id','=','ac.id_usuariocrea')->where('ac.id_paciente',$agenda->id_paciente)
            ->select('ac.id','ac.created_at','fc.nombre','u.nombre1','u.apellido1')->orderBy('ac.id','desc')->limit(20)->get();
        $payload=compact('agenda','patient','company','doctor','formats','history');
        config(array('data'=>array_merge(config('data'),array('controlador'=>'agenda-clinica','carpeta'=>'clinical','submodulo'=>'consentimientos','submodulo_'=>'consentimientos',
            'titulo'=>'Consentimientos informados','title'=>'Consentimientos | Aitrol Hospitalario','vista'=>'v3.clinical.agenda_consents.index',
            'obseraudi'=>'Ingreso a consentimientos de agenda','contenido'=>view('v3.clinical.agenda_consents.index',$payload)))));
        return view('v3.layouts.view_child');
    }

    public function pdf(Request $request, $doctor, $appointment, $format)
    {
        list($agenda,$patient,$company)=$this->context($request,$doctor,$appointment);
        $formato=Formato_consentimiento::where('id',$format)->where('estado',1)
            ->where(function($q)use($agenda){$q->whereNull('id_empresa')->orWhere('id_empresa',$agenda->id_empresa);})
            ->where(function($q)use($agenda){$q->whereNull('id_procedimiento')->orWhere('id_procedimiento',$agenda->id_procedimiento);})->firstOrFail();
        Agenda_consentimiento::create(array('cie_10'=>$agenda->cie10,'formato'=>$formato->id,'ip_creacion'=>$request->ip(),
            'id_usuariocrea'=>Auth::id(),'id_usuariomod'=>Auth::id(),'id_paciente'=>$agenda->id_paciente));
        $physician=DB::table('users')->where('id',$agenda->id_doctor1)->first();
        $date=date('Y-m-d H:i:s'); $paciente=$patient; $empresa=$company;
        $pdf=app('dompdf.wrapper');
        $pdf->loadView('hc_admision.consentimientos.generico_v3',compact('agenda','paciente','empresa','formato','date','physician'));
        return $pdf->stream('consentimiento-'.preg_replace('/[^A-Za-z0-9_-]+/','-',strtolower($formato->nombre)).'.pdf');
    }

    private function context(Request $request,$doctor,$appointment)
    {
        // Todas las pantallas V3 comparten la empresa resuelta por ResolveTenant
        // en id_empresa. Sistemas conserva acceso total, pero también debe operar
        // dentro de una empresa activa para mantener el aislamiento clínico.
        $companyId=trim((string)$request->session()->get('id_empresa'));
        if ($companyId==='') abort(403,'Seleccione una empresa para continuar.');
        try {$agenda=$this->service->agendaForAction($companyId,$doctor,$appointment);} catch(RuntimeException $e){abort(404,$e->getMessage());}
        if (!$agenda->id_paciente) abort(422,'Esta agenda no tiene un paciente asociado.');
        $patient=DB::table('paciente')->where('id',$agenda->id_paciente)->first();
        $company=DB::table('empresa')->where('id',$companyId)->first();
        if (!$patient || !$company) abort(404,'No se encontraron los datos clínicos requeridos.');
        return array($agenda,$patient,$company);
    }
}
