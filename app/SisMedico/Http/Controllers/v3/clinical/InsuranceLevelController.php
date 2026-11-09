<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Sis_medico\Convenio;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Nivel;
use Sis_medico\Seguro;
use Sis_medico\Services\CompanyCatalogService;
use Sis_medico\Services\SidebarMenuService;

class InsuranceLevelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorizeManagement();
        $companyId = $this->companyId();
        $editingLevel = $request->filled('editar_nivel') ? Nivel::findOrFail($request->input('editar_nivel')) : null;
        $editingAssignment = null;
        if ($request->filled('editar_asignacion')) {
            $editingAssignment = Convenio::where('id_empresa', $companyId)
                ->where('id', $request->input('editar_asignacion'))->firstOrFail();
        }

        $levels = Nivel::orderBy('estado', 'desc')->orderBy('nombre')->get();
        $insurances = Seguro::where('id_empresa', $companyId)->where('inactivo', 1)
            ->whereIn('tipo', array(0, 1, 2))->orderBy('nombre')->get();
        $assignments = DB::table('convenio as c')
            ->join('seguros as s', 's.id', '=', 'c.id_seguro')
            ->join('nivel as n', 'n.id', '=', 'c.id_nivel')
            ->where('c.id_empresa', $companyId)
            ->select('c.*', 's.nombre as seguro_nombre', 'n.nombre as nivel_nombre')
            ->orderBy('c.estado', 'desc')->orderBy('s.nombre')->orderBy('n.nombre')->get();

        $view = 'v3.clinical.insurance_levels.index';
        $payload = compact('levels', 'insurances', 'assignments', 'editingLevel', 'editingAssignment');
        config(array('data' => array_merge(config('data') ?: array(), array(
            'controlador' => 'configuraciones-globales', 'carpeta' => 'clinical',
            'submodulo' => 'niveles-seguro', 'submodulo_' => 'niveles-seguro',
            'menu_id' => 746, 'titulo' => 'Niveles de seguro',
            'title' => 'Niveles de seguro | Aitrol Hospitalario',
            'vista' => $view, 'contenido' => view($view, $payload),
        ))));
        return view('v3.layouts.view_child');
    }

    public function storeLevel(Request $request)
    {
        $this->authorizeManagement();
        $this->validateLevel($request);
        $level = new Nivel();
        $level->id_usuariocrea = Auth::id();
        $level->ip_creacion = $request->ip();
        $this->saveLevel($level, $request);
        return $this->savedResponse($request, 'Nivel creado correctamente. Ahora puede asignarlo a un seguro.');
    }

    public function updateLevel(Request $request, $level)
    {
        $this->authorizeManagement();
        $level = Nivel::findOrFail($level);
        $this->validateLevel($request, $level->id);
        $this->saveLevel($level, $request);
        return $this->savedResponse($request, 'Nivel actualizado correctamente.');
    }

    public function storeAssignment(Request $request)
    {
        $this->authorizeManagement();
        $this->validateAssignment($request);
        $assignment = new Convenio();
        $assignment->id_empresa = $this->companyId();
        $assignment->id_usuariocrea = Auth::id();
        $assignment->ip_creacion = $request->ip();
        $this->saveAssignment($assignment, $request);
        return $this->savedResponse($request, 'Nivel asignado al seguro para la empresa activa.');
    }

    public function updateAssignment(Request $request, $assignment)
    {
        $this->authorizeManagement();
        $assignment = Convenio::where('id_empresa', $this->companyId())->where('id', $assignment)->firstOrFail();
        $this->validateAssignment($request, $assignment->id);
        $this->saveAssignment($assignment, $request);
        return $this->savedResponse($request, 'Asignación actualizada correctamente.');
    }

    private function validateLevel(Request $request, $ignore = null)
    {
        $request->merge(array('nombre' => trim((string) $request->input('nombre')), 'nombre_corto' => trim((string) $request->input('nombre_corto'))));
        $name = Rule::unique('nivel', 'nombre');
        if ($ignore) $name->ignore($ignore);
        $this->validate($request, array('nombre' => array('required', 'string', 'max:255', $name), 'nombre_corto' => 'nullable|string|max:40', 'grupo' => 'required|in:0,1,2,3', 'estado' => 'required|in:0,1'));
    }

    private function validateAssignment(Request $request, $ignore = null)
    {
        $companyId = $this->companyId();
        $duplicate = Rule::unique('convenio', 'id_nivel')->where(function ($query) use ($request, $companyId) {
            return $query->where('id_empresa', $companyId)->where('id_seguro', $request->input('id_seguro'));
        });
        if ($ignore) $duplicate->ignore($ignore);
        $this->validate($request, array(
            'id_seguro' => array('required', Rule::exists('seguros', 'id')->where('id_empresa', $companyId)),
            'id_nivel' => array('required', Rule::exists('nivel', 'id'), $duplicate),
            'descuento' => 'required|numeric|min:0|max:100', 'estado' => 'required|in:0,1',
        ), array('id_nivel.unique' => 'Este nivel ya está asignado al seguro seleccionado para la empresa activa.'));
    }

    private function saveLevel(Nivel $level, Request $request)
    {
        $level->nombre = $request->input('nombre');
        $level->nombre_corto = $request->input('nombre_corto') ?: null;
        $level->grupo = (int) $request->input('grupo');
        $level->estado = (int) $request->input('estado');
        $level->id_usuariomod = Auth::id();
        $level->ip_modificacion = $request->ip();
        $level->save();
    }

    private function saveAssignment(Convenio $assignment, Request $request)
    {
        $assignment->id_seguro = $request->input('id_seguro');
        $assignment->id_nivel = $request->input('id_nivel');
        $assignment->descuento = (float) $request->input('descuento');
        $assignment->estado = (int) $request->input('estado');
        $assignment->id_usuariomod = Auth::id();
        $assignment->ip_modificacion = $request->ip();
        $assignment->save();
    }

    private function companyId()
    {
        return app(CompanyCatalogService::class)->companyId(true);
    }

    private function savedResponse(Request $request, $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(array('ok' => true, 'message' => $message));
        }
        return redirect()->route('v3.insurance-levels.index')->with('success', $message);
    }

    private function authorizeManagement()
    {
        if (!Auth::user()->isSystemUser() && !app(SidebarMenuService::class)->canWithLegacy('system.insurances', array(1, 7))) abort(403);
    }
}
