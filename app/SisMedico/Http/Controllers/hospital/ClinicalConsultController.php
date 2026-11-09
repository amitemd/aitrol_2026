<?php
namespace Sis_medico\Http\Controllers\hospital;
use Illuminate\Http\Request;
use Sis_medico\ClinicalConsultRequest;
use Sis_medico\Exceptions\ClinicalConsultWorkflowException;
use Sis_medico\Exceptions\ClinicalAccessDeniedException;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\ClinicalConsultWorkflowService;
use Sis_medico\Services\ClinicalAccessControlService;
use Sis_medico\Services\ClinicalEncounterSelectorService;
class ClinicalConsultController extends Controller
{
    private $service;
    public function __construct(ClinicalConsultWorkflowService $service){$this->middleware('auth');$this->service=$service;}
    public function index(Request $request)
    {
        if(!$this->service->available())return redirect()->route('hospital.index')->with('error','Aplique la migración del workflow de interconsultas.');
        $query=ClinicalConsultRequest::with('legacy')->orderBy('due_at');
        $encounterId=trim((string)$request->get('encounter_id'));
        if($encounterId===''){
            $encounters=app(ClinicalEncounterSelectorService::class)->paginateForAction('CONSULT_VIEW',$request);
            if(is_null($encounters))return redirect()->route('hospital.index')->with('error','La selección segura de episodios no está disponible.');
            return view('hospital.clinical_encounters.select',array(
                'encounters'=>$encounters,'targetRoute'=>'clinical_consults.index','title'=>'Interconsultas',
                'description'=>'Selecciona el episodio antes de revisar solicitudes, tiempos y respuestas.','icon'=>'fa fa-user-md',
            ));
        }
        try{app(ClinicalAccessControlService::class)->assertAllowed($encounterId,'CONSULT_VIEW',array('user'=>auth()->user(),'ip'=>$request->ip()));}
        catch(ClinicalAccessDeniedException $e){return redirect()->route('hospital.index')->with('error',$e->getMessage());}
        $query->where('encounter_id',$encounterId);
        if($request->get('status'))$query->where('status',$request->get('status'));
        if($request->get('priority'))$query->where('priority',$request->get('priority'));
        $consults=$query->paginate(25);
        return view('hospital.clinical_consults.index',compact('consults','encounterId'));
    }
    public function transition(Request $request,$id)
    {
        $this->validate($request,array('status'=>'required|in:ACCEPTED,IN_REVIEW,COMPLETED,CANCELLED','response_summary'=>'nullable|string|max:5000','reason'=>'nullable|string|max:1000'));
        try{$this->service->transition(ClinicalConsultRequest::findOrFail($id),$request->status,$request->only('response_summary','reason'),array('actor_id'=>auth()->user()->id,'ip'=>$request->ip()));
            return back()->with('success','Interconsulta actualizada con trazabilidad clínica.');}
        catch(ClinicalAccessDeniedException $e){return back()->with('error',$e->getMessage());}
        catch(ClinicalConsultWorkflowException $e){return back()->with('error',$e->getMessage());}
    }
}
