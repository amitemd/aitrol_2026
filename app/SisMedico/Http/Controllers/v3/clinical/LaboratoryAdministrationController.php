<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Parametro_Referencia_Anterior;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Labs_Factura_Agrupada_Cab;
use Sis_medico\Protocolo;
use Sis_medico\Services\V3LaboratoryAdministrationService;

class LaboratoryAdministrationController extends Controller
{
    private $service;
    private $menus=array('protocols'=>676,'groupers'=>677,'agenda'=>678,'invoices'=>679,'statistics'=>680,'history'=>681);
    public function __construct(V3LaboratoryAdministrationService $service){$this->middleware('auth');$this->service=$service;}

    public function protocols(Request $request){$search=trim((string)$request->get('buscar',''));return $this->page('protocols',compact('search')+array('models'=>$this->service->protocols($search)),'Protocolos de laboratorio','protocolos-laboratorio','protocols');}
    public function createProtocol(){return $this->protocolForm(new Protocolo,'Nuevo protocolo');}
    public function editProtocol($protocol){return $this->protocolForm(Protocolo::findOrFail($protocol),'Editar protocolo');}
    public function storeProtocol(Request $request){return $this->persistProtocol($request);}
    public function updateProtocol(Request $request,$protocol){Protocolo::findOrFail($protocol);return $this->persistProtocol($request,$protocol);}
    private function persistProtocol(Request $request,$id=null){$this->validate($request,array('nombre'=>'required|max:200','est_amb_hos'=>'required|in:0,1,2','examenes'=>'array','examenes.*'=>'integer|exists:examen,id'));$values=array('nombre'=>strtoupper(trim($request->nombre)),'est_amb_hos'=>$request->est_amb_hos,'estado'=>1,'id_usuariomod'=>Auth::id(),'ip_modificacion'=>$request->ip());if(!$id){$values['id_usuariocrea']=Auth::id();$values['ip_creacion']=$request->ip();}$model=$this->service->saveProtocol($values,(array)$request->get('examenes',array()),$id);return redirect()->route('v3.laboratory-protocols.edit',array('protocol'=>$model->id))->with('success','Protocolo guardado con sus exámenes.');}
    private function protocolForm(Protocolo $model,$title){$selected=$model->exists?DB::table('examen_protocolo')->where('id_protocolo',$model->id)->where('estado',1)->pluck('id_examen')->toArray():array();return $this->page('protocol_form',compact('model','selected')+array('exams'=>Examen::where('estado',1)->orderBy('descripcion')->get()),$title,'protocolos-laboratorio','protocols');}

    public function groupers(Request $request){$search=trim((string)$request->get('buscar',''));return $this->page('groupers',array('models'=>$this->service->groupers($search),'search'=>$search),'Agrupadores de exámenes','agrupadores-examenes','groupers');}
    public function createGrouper(){return $this->grouperForm(new Examen_Agrupador,'Nuevo agrupador');}
    public function editGrouper($grouper){return $this->grouperForm(Examen_Agrupador::findOrFail($grouper),'Editar agrupador');}
    public function storeGrouper(Request $request){return $this->persistGrouper($request);}
    public function updateGrouper(Request $request,$grouper){Examen_Agrupador::findOrFail($grouper);return $this->persistGrouper($request,$grouper);}
    private function persistGrouper(Request $request,$id=null){$this->validate($request,array('nombre'=>'required|max:200|unique:examen_agrupador,nombre'.($id?','.$id:'')));$v=array('nombre'=>strtoupper(trim($request->nombre)),'estado'=>1,'id_usuariomod'=>Auth::id(),'ip_modificacion'=>$request->ip());if(!$id){$v['id_usuariocrea']=Auth::id();$v['ip_creacion']=$request->ip();}$m=$this->service->saveGrouper($v,$id);return redirect()->route('v3.laboratory-groupers.edit',array('grouper'=>$m->id))->with('success','Agrupador guardado correctamente.');}
    private function grouperForm(Examen_Agrupador $model,$title){return $this->page('grouper_form',compact('model'),$title,'agrupadores-examenes','groupers');}

    public function agenda(Request $request)
    {
        $date = $request->get('fecha', date('Y-m-d'));
        if (!$this->validDate($date)) {
            $date = date('Y-m-d');
        }

        list($events, $specialties) = $this->service->agenda(session('id_empresa'), $date);
        $payload = compact('date', 'events', 'specialties');

        if ($request->ajax()) {
            return response()->json(array(
                'html' => view('v3.clinical.laboratory_administration._agenda_calendar', $payload)->render(),
                'date' => $date,
                'url' => route('v3.laboratory-agenda.index', array('fecha' => $date)),
            ));
        }

        return $this->page('agenda', $payload, 'Agenda de laboratorio', 'agenda-laboratorio', 'agenda');
    }

    private function validDate($date)
    {
        if (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }

        $parsed = \DateTime::createFromFormat('!Y-m-d', $date);
        return $parsed && $parsed->format('Y-m-d') === $date;
    }
    public function invoices(Request $request){$year=$request->get('anio');$month=$request->get('mes');return $this->page('invoices',array('models'=>$this->service->groupedInvoices(session('id_empresa'),$year,$month),'year'=>$year,'month'=>$month),'Facturación agrupada','facturas-agrupadas-laboratorio','invoices');}
    public function createInvoice(){return $this->invoiceForm(new Labs_Factura_Agrupada_Cab,'Nueva factura agrupada');}
    public function editInvoice($invoice){return $this->invoiceForm($this->companyInvoice($invoice),'Editar factura agrupada');}
    public function storeInvoice(Request $request){return $this->persistInvoice($request);}
    public function updateInvoice(Request $request,$invoice){$this->companyInvoice($invoice);return $this->persistInvoice($request,$invoice);}
    private function persistInvoice(Request $request,$id=null){$this->validate($request,array('anio'=>'required|integer|min:2000|max:2100','mes'=>'required|integer|min:1|max:12','fecha_desde'=>'required|date','fecha_hasta'=>'required|date','tipo_documento'=>'required|in:4,5,6,8','cedula_factura'=>'required|max:30','nombre_factura'=>'required|max:100','direccion_factura'=>'required|max:100','ciudad_factura'=>'required|max:70','email_factura'=>'required|email|max:100','telefono_factura'=>'required|max:30','sucursal'=>'required|max:4','punto_emision'=>'required|max:4'));if(strtotime($request->fecha_hasta)<strtotime($request->fecha_desde))return back()->withErrors(array('fecha_hasta'=>'La fecha final no puede ser anterior a la fecha inicial.'))->withInput();$values=$request->only(array('anio','mes','fecha_desde','fecha_hasta','tipo_documento','cedula_factura','nombre_factura','direccion_factura','ciudad_factura','email_factura','telefono_factura','sucursal','punto_emision'));$values['estado']=1;$values['id_usuariomod']=Auth::id();$values['ip_modificacion']=$request->ip();if(Schema::hasColumn('labs_factura_agrupada_cab','id_empresa'))$values['id_empresa']=session('id_empresa');if(!$id){$values['id_usuariocrea']=Auth::id();$values['ip_creacion']=$request->ip();}$model=$this->service->saveGroupedInvoice($values,$id);return redirect()->route('v3.laboratory-invoices.edit',array('invoice'=>$model->id))->with('success','Cabecera agrupada guardada correctamente.');}
    private function invoiceForm(Labs_Factura_Agrupada_Cab $model,$title){$branches=DB::table('ct_sucursales')->where('estado',1)->where('id_empresa',session('id_empresa'))->orderBy('codigo_sucursal')->get();$boxes=DB::table('ct_caja')->where('estado',1)->where('id_empresa',session('id_empresa'))->orderBy('codigo_caja')->get();return $this->page('invoice_form',compact('model','branches','boxes'),$title,'facturas-agrupadas-laboratorio','invoices');}
    private function companyInvoice($id){$query=Labs_Factura_Agrupada_Cab::where('id',$id);if(Schema::hasColumn('labs_factura_agrupada_cab','id_empresa'))$query->where('id_empresa',session('id_empresa'));return $query->firstOrFail();}
    public function statistics(Request $request){$from=$request->get('desde',date('Y-m-01'));$to=$request->get('hasta',date('Y-m-d'));$ids=(array)$request->get('examenes',array());$rows=$this->service->statistics(session('id_empresa'),$from,$to,$ids);return $this->page('statistics',compact('from','to','ids','rows')+array('exams'=>Examen::where('estado',1)->orderBy('descripcion')->get()),'Estadísticas de laboratorio','estadisticas-laboratorio','statistics');}
    public function history(Request $request){$search=trim((string)$request->get('buscar',''));return $this->page('history',array('models'=>$this->service->historicalExams($search),'search'=>$search),'Referencias históricas','parametros-historicos-laboratorio','history');}
    public function references($exam){$model=Examen::findOrFail($exam);$rows=$this->service->references($exam);return $this->page('references',compact('model','rows'),'Historial de parámetros','parametros-historicos-laboratorio','history');}
    public function archive(Request $request,$exam,$parameter){$this->validate($request,array('fecha_valida'=>'required|date'));if(!DB::table('examen_parametro')->where('id',$parameter)->where('id_examen',$exam)->exists())abort(404);$this->service->archiveReference($parameter,$request->fecha_valida,Auth::id(),$request->ip());return back()->with('success','La referencia vigente se archivó correctamente.');}
    public function deleteReference(Request $request,$exam,$reference){$row=Examen_Parametro_Referencia_Anterior::findOrFail($reference);$owns=DB::table('examen_parametro')->where('id',$row->id_parametro)->where('id_examen',$exam)->exists();if(!$owns)abort(404);$row->update(array('estado'=>0,'id_usuariomod'=>Auth::id(),'ip_modificacion'=>$request->ip()));return back()->with('success','Referencia histórica desactivada.');}

    private function page($name,array $payload,$title,$module,$key){$view='v3.clinical.laboratory_administration.'.$name;$data=array('controlador'=>$module,'carpeta'=>'clinical','submodulo'=>'index','submodulo_'=>'index','menu_id'=>$this->menus[$key],'titulo'=>$title,'title'=>$title.' | Aitrol Hospitalario','vista'=>$view,'obseraudi'=>'Administración V3 de laboratorio por empresa y rol','contenido'=>view($view,$payload));config(array('data'=>array_merge(config('data')?:array(),$data)));return view('v3.layouts.view_child');}
}
