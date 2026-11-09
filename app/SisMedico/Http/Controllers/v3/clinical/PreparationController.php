<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Preparaciones;

class PreparationController extends Controller
{
    const MENU_ID = 9001;

    public function __construct() { $this->middleware('auth'); }

    public function index(Request $request)
    {
        $this->authorizeMaintenance();
        $payload = $this->maintenancePayload($request);
        if ($request->ajax()) return view('v3.clinical.preparations.partials.table', $payload);
        return $this->page('v3.clinical.preparations.index', $payload, 'Preparaciones clínicas');
    }

    public function catalog(Request $request)
    {
        $search = trim((string) $request->input('buscar'));
        $query = $this->companyQuery($request)->where('estado', 1)->orderBy('nombre_preparaciones');
        if ($search !== '') $query->where('nombre_preparaciones', 'like', '%' . $search . '%');
        $models = $query->get();
        if ($request->ajax()) return view('v3.clinical.preparations.partials.catalog_results', compact('models', 'search'));
        $backUrl = route('v3.clinical-agenda.index');
        $agendaId = (int) $request->input('agenda');
        if ($agendaId > 0) {
            $agenda = DB::table('agenda')->where('id', $agendaId)
                ->where('id_empresa', $this->companyId($request))->first(array('id_doctor1'));
            if ($agenda && $agenda->id_doctor1) {
                $backUrl = route('v3.doctor-agenda.calendar', array('doctor' => $agenda->id_doctor1));
            }
        }
        return $this->page('v3.clinical.preparations.catalog', compact('models', 'search', 'backUrl'), 'Preparaciones médicas');
    }

    public function store(Request $request)
    {
        $this->authorizeMaintenance();
        $this->validateData($request, true);
        $model = new Preparaciones;
        $this->fillAndSave($request, $model);
        if ($request->ajax()) return response()->json(array('ok' => true, 'message' => 'Preparación creada correctamente.', 'html' => view('v3.clinical.preparations.partials.table', $this->maintenancePayload($request))->render()));
        return redirect()->route('v3.preparations.index')->with('success', 'Preparación creada correctamente.');
    }

    public function update(Request $request, $preparation)
    {
        $this->authorizeMaintenance();
        $this->validateData($request, false);
        $model = $this->owned($request, $preparation);
        $this->fillAndSave($request, $model);
        if ($request->ajax()) return response()->json(array('ok' => true, 'message' => 'Preparación actualizada correctamente.', 'html' => view('v3.clinical.preparations.partials.table', $this->maintenancePayload($request))->render()));
        return redirect()->route('v3.preparations.index')->with('success', 'Preparación actualizada correctamente.');
    }

    public function toggle(Request $request, $preparation)
    {
        $this->authorizeMaintenance();
        $model = $this->owned($request, $preparation);
        $model->estado = (int) $model->estado === 1 ? 0 : 1;
        $model->id_usuariomod = Auth::id();
        $model->ip_modificacion = $request->ip();
        $model->save();
        if ($request->ajax()) return response()->json(array('ok' => true, 'message' => 'Estado actualizado correctamente.', 'html' => view('v3.clinical.preparations.partials.table', $this->maintenancePayload($request))->render()));
        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function file(Request $request, $preparation)
    {
        $model = $this->owned($request, $preparation);
        abort_if(!$model->archivo_preparaciones || !Storage::disk('local')->exists($model->archivo_preparaciones), 404);
        return response()->download(
            storage_path('app/' . str_replace(array('../', '..\\'), '', $model->archivo_preparaciones)),
            $this->safeName($model->nombre_preparaciones) . '.pdf',
            array('Content-Type' => 'application/pdf')
        );
    }

    private function fillAndSave(Request $request, Preparaciones $model)
    {
        $now = date('Y-m-d H:i:s');
        $creating = !$model->exists;
        $model->nombre_preparaciones = trim($request->input('nombre_preparaciones'));
        $model->id_empresa = $this->companyId($request);
        $model->estado = $request->has('estado') ? 1 : ($creating ? 1 : $model->estado);
        $model->id_usuariomod = Auth::id();
        $model->ip_modificacion = $request->ip();
        if ($creating) {
            $model->id_usuariocrea = Auth::id();
            $model->ip_creacion = $request->ip();
        }
        if ($request->hasFile('archivo')) {
            $directory = 'v3/preparaciones/' . $this->safeName($model->id_empresa);
            $filename = date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.pdf';
            $path = $request->file('archivo')->storeAs($directory, $filename, 'local');
            if (!$creating && $model->archivo_preparaciones && Storage::disk('local')->exists($model->archivo_preparaciones)) {
                Storage::disk('local')->delete($model->archivo_preparaciones);
            }
            $model->archivo_preparaciones = $path;
            $model->nombre_archivo_original = $request->file('archivo')->getClientOriginalName();
        }
        $model->updated_at = $now;
        $model->save();
    }

    private function validateData(Request $request, $fileRequired)
    {
        $rules = array('nombre_preparaciones' => 'required|string|max:255');
        $rules['archivo'] = ($fileRequired ? 'required|' : 'nullable|') . 'file|mimes:pdf|max:15360';
        $this->validate($request, $rules, array(
            'archivo.mimes' => 'El archivo debe ser un PDF.',
            'archivo.max' => 'El PDF no puede superar 15 MB.',
        ));
    }

    private function companyQuery(Request $request)
    {
        return Preparaciones::where('id_empresa', $this->companyId($request));
    }

    private function maintenancePayload(Request $request)
    {
        $search = trim((string) $request->input('buscar'));
        $query = $this->companyQuery($request)->orderBy('nombre_preparaciones');
        if ($search !== '') $query->where('nombre_preparaciones', 'like', '%' . $search . '%');
        $models = $query->paginate(10);
        $models->appends(array('buscar' => $search));
        return array('models' => $models, 'search' => $search);
    }

    private function owned(Request $request, $id)
    {
        return $this->companyQuery($request)->where('id', (int) $id)->firstOrFail();
    }

    private function companyId(Request $request)
    {
        $id = trim((string) $request->session()->get('id_empresa'));
        abort_if($id === '', 403, 'Seleccione una empresa activa.');
        return $id;
    }

    private function authorizeMaintenance()
    {
        if (!Auth::check() || !in_array((int) Auth::user()->id_tipo_usuario, array(1, 4), true)) abort(403);
    }

    private function safeName($value)
    {
        $value = preg_replace('/[^A-Za-z0-9_-]+/', '_', (string) $value);
        return trim($value, '_') ?: 'preparacion';
    }

    private function page($view, array $payload, $title)
    {
        $data = array(
            'controlador' => 'preparaciones', 'carpeta' => 'clinical',
            'submodulo' => 'index', 'submodulo_' => 'index', 'menu_id' => self::MENU_ID,
            'titulo' => $title, 'title' => $title . ' | Aitrol Hospitalario', 'vista' => $view,
            'obseraudi' => 'Administración de preparaciones clínicas por empresa',
            'contenido' => view($view, $payload),
        );
        config(array('data' => array_merge(config('data') ?: array(), $data)));
        return view('v3.layouts.view_child');
    }
}
