<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Parametro_Referencia_Anterior;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Nivel;
use Sis_medico\Services\V3LaboratoryExamCatalogService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaboratoryExamCatalogController extends Controller
{
    private $catalog;

    public function __construct(V3LaboratoryExamCatalogService $catalog)
    {
        $this->middleware('auth');
        $this->catalog = $catalog;
    }

    public function index(Request $request)
    {
        $this->authorizeCatalog();
        $search = trim((string) $request->get('buscar', ''));
        list($exams, $prices) = $this->catalog->catalog($search);
        return $this->page('index', array('exams' => $exams, 'prices' => $prices, 'levels' => Nivel::orderBy('id')->get(), 'search' => $search), 'Catálogo de exámenes');
    }

    public function create()
    {
        $this->authorizeCatalog();
        return $this->form(new Examen, 'Nuevo examen');
    }

    public function store(Request $request)
    {
        $this->authorizeCatalog();
        $this->validateExam($request);
        $exam = $this->catalog->saveExam($this->examData($request));
        return redirect()->route('v3.laboratory-exams.edit', array('exam' => $exam->id))->with('success', 'El examen se creó correctamente. Ya puede configurar sus precios y parámetros.');
    }

    public function edit($exam)
    {
        $this->authorizeCatalog();
        return $this->form(Examen::findOrFail($exam), 'Editar examen');
    }

    public function update(Request $request, $exam)
    {
        $this->authorizeCatalog();
        Examen::findOrFail($exam);
        $this->validateExam($request, $exam);
        $this->catalog->saveExam($this->examData($request, true), $exam);
        return redirect()->route('v3.laboratory-exams.edit', array('exam' => $exam))->with('success', 'Examen y precios actualizados en una sola operación.');
    }

    public function parameters($exam)
    {
        $this->authorizeCatalog();
        $model = Examen::findOrFail($exam);
        $parameters = Examen_Parametro::where('id_examen', $exam)->orderBy('orden')->orderBy('id')->paginate(30);
        return $this->page('parameters', compact('model', 'parameters'), 'Parámetros del examen');
    }

    public function createParameter($exam)
    {
        $this->authorizeCatalog();
        return $this->parameterForm(Examen::findOrFail($exam), new Examen_Parametro, 'Nuevo parámetro');
    }

    public function storeParameter(Request $request, $exam)
    {
        $this->authorizeCatalog();
        Examen::findOrFail($exam);
        $this->validateParameter($request);
        $this->catalog->saveParameter($exam, $this->parameterData($request, false));
        return redirect()->route('v3.laboratory-exams.parameters', array('exam' => $exam))->with('success', 'Parámetro agregado correctamente.');
    }

    public function editParameter($exam, $parameter)
    {
        $this->authorizeCatalog();
        $model = Examen::findOrFail($exam);
        $item = Examen_Parametro::where('id_examen', $exam)->where('id', $parameter)->firstOrFail();
        $references = Examen_Parametro_Referencia_Anterior::where('id_parametro', $item->id)->where('estado', 1)->orderBy('fecha_valida', 'desc')->get();
        return $this->parameterForm($model, $item, 'Editar parámetro', $references);
    }

    public function updateParameter(Request $request, $exam, $parameter)
    {
        $this->authorizeCatalog();
        $this->validateParameter($request);
        $this->catalog->saveParameter($exam, $this->parameterData($request, true), $parameter);
        return redirect()->route('v3.laboratory-exams.parameters.edit', array('exam' => $exam, 'parameter' => $parameter))->with('success', 'Parámetro actualizado correctamente.');
    }

    public function archiveReference(Request $request, $exam, $parameter)
    {
        $this->authorizeCatalog();
        $this->validate($request, array('fecha_valida' => 'required|date'));
        $item = Examen_Parametro::where('id_examen', $exam)->where('id', $parameter)->firstOrFail();
        Examen_Parametro_Referencia_Anterior::create(array('id_parametro'=>$item->id,'valor1'=>$item->valor1,'valor1g'=>$item->valor1g,'unidad1'=>$item->unidad1,'texto_referencia'=>$item->texto_referencia,'fecha_valida'=>$request->fecha_valida,'estado'=>1,'id_usuariocrea'=>Auth::id(),'id_usuariomod'=>Auth::id(),'ip_creacion'=>$request->ip(),'ip_modificacion'=>$request->ip()));
        return back()->with('success', 'El rango vigente quedó archivado con su fecha de validez.');
    }

    public function export(Request $request)
    {
        $this->authorizeCatalog();
        $search = trim((string) $request->get('buscar', ''));
        list($exams, $prices) = $this->catalog->catalog($search, 100000);
        $levels = Nivel::orderBy('id')->get();
        $response = new StreamedResponse(function () use ($exams, $prices, $levels) {
            $out = fopen('php://output', 'w'); fwrite($out, "\xEF\xBB\xBF");
            $head = array('ID','NOMBRE CORTO','NOMBRE LARGO','AGRUPADOR','TARIFARIO','VALOR BASE','ESTADO'); foreach ($levels as $level) $head[] = $level->nombre;
            fputcsv($out, $head, ';');
            foreach ($exams->items() as $exam) { $row=array($exam->id,$exam->nombre,$exam->descripcion,$exam->agrupador_nombre,$exam->tarifario,$exam->valor,(int)$exam->estado===1?'ACTIVO':'INACTIVO'); foreach($levels as $level)$row[]=isset($prices[$exam->id][$level->id])?$prices[$exam->id][$level->id]:0; fputcsv($out,array_map(array($this,'safeCsv'),$row),';'); }
            fclose($out);
        });
        $response->headers->set('Content-Type','text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition','attachment; filename="catalogo-examenes-'.date('Ymd-His').'.csv"');
        return $response;
    }

    public function safeCsv($value) { $value=(string)$value; return preg_match('/^[=+\-@]/',ltrim($value)) ? "'".$value : $value; }

    private function form(Examen $exam, $title)
    {
        $levels = Nivel::orderBy('id')->get(); $prices=array();
        if ($exam->exists) foreach (DB::table('examen_nivel')->where('id_examen',$exam->id)->get() as $price) $prices[$price->nivel]=$price->valor1;
        return $this->page('form', array('exam'=>$exam,'groups'=>Examen_Agrupador::where('estado',1)->orderBy('nombre')->get(),'levels'=>$levels,'prices'=>$prices), $title);
    }

    private function parameterForm(Examen $exam, Examen_Parametro $parameter, $title, $references = null)
    {
        return $this->page('parameter_form', array('exam'=>$exam,'parameter'=>$parameter,'references'=>$references ?: collect()), $title);
    }

    private function validateExam(Request $request, $id = null)
    {
        $unique='unique:examen,nombre'.($id?','.$id:'');
        $rules=array('nombre'=>'required|max:200|'.$unique,'descripcion'=>'required|max:200','tarifario'=>'required|max:10','id_agrupador'=>'required|exists:examen_agrupador,id','valor'=>'required|numeric|min:0');
        if ($id) $rules['estado']='required|in:0,1';
        foreach (Nivel::all() as $level) $rules['nivel_'.$level->id]='nullable|numeric|min:0';
        $this->validate($request,$rules,array('nombre.unique'=>'Ya existe un examen con ese nombre.'));
    }

    private function examData(Request $request, $editing = false)
    {
        $audit=array('user'=>Auth::id(),'ip'=>$request->ip()); $exam=array('nombre'=>strtoupper(trim($request->nombre)),'descripcion'=>strtoupper(trim($request->descripcion)),'tarifario'=>strtoupper(trim($request->tarifario)),'id_agrupador'=>$request->id_agrupador,'valor'=>$request->valor,'id_usuariomod'=>$audit['user'],'ip_modificacion'=>$audit['ip']);
        if ($editing) { $exam['estado']=$request->estado; $exam['tiempos']=strtoupper(trim((string)$request->tiempo)); } else { $exam['estado']=1; $exam['id_usuariocrea']=$audit['user']; $exam['ip_creacion']=$audit['ip']; }
        $levels=array(); foreach(Nivel::all() as $level)$levels[$level->id]=$request->get('nivel_'.$level->id,0);
        return compact('exam','levels','audit');
    }

    private function validateParameter(Request $request)
    {
        $this->validate($request,array('nombre'=>'required|max:200','valor1'=>'nullable|max:100','valor1g'=>'nullable|max:100','unidad1'=>'nullable|max:100','sexo'=>'required|in:1,2,3','edad_ini'=>'nullable|integer|min:0','edad_fin'=>'nullable|integer|min:0','orden'=>'nullable|integer|min:0','estado'=>'required|in:0,1'));
        if ($request->edad_ini !== null && $request->edad_fin !== null && (int)$request->edad_fin < (int)$request->edad_ini) abort(422, 'La edad final no puede ser menor que la inicial.');
    }

    private function parameterData(Request $request, $editing)
    {
        $name=strtoupper(trim($request->nombre)); $data=array('nombre'=>$name,'descripcion'=>$name,'texto1'=>$name,'valor1'=>$request->valor1,'valor1g'=>$request->valor1g,'unidad1'=>$request->unidad1,'sexo'=>$request->sexo,'texto_referencia'=>$request->texto_referencia,'edad_ini'=>$request->edad_ini,'edad_fin'=>$request->edad_fin,'orden'=>$request->orden ?: 0,'estado'=>$request->estado,'id_usuariomod'=>Auth::id(),'ip_modificacion'=>$request->ip());
        if(!$editing){$data['id_usuariocrea']=Auth::id();$data['ip_creacion']=$request->ip();} return $data;
    }

    private function authorizeCatalog()
    {
        // La autorización pertenece al middleware V3 y a la política por rol;
        // no se permiten listas de tipos de usuario o identificaciones fijas.
    }

    private function page($name, array $payload, $title)
    {
        $view='v3.clinical.laboratory_exam_catalog.'.$name;
        $data=array('controlador'=>'catalogo-examenes','carpeta'=>'clinical','submodulo'=>'index','submodulo_'=>'index','menu_id'=>674,'titulo'=>$title,'title'=>$title.' | Aitrol Hospitalario','vista'=>$view,'obseraudi'=>'Administración V3 del catálogo y parámetros de laboratorio','contenido'=>view($view,$payload));
        config(array('data'=>array_merge(config('data'),$data))); return view('v3.layouts.view_child');
    }
}
