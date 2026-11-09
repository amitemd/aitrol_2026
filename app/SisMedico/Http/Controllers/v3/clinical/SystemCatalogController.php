<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Sis_medico\Especialidad;
use Sis_medico\grupo_procedimiento;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Procedimiento;
use Sis_medico\Services\SidebarMenuService;
use Sis_medico\Services\V3ReceptionDashboardService;

class SystemCatalogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function specialties(Request $request)
    {
        $this->authorizeCatalog('system.specialties', array(1, 4));
        $search = trim((string) $request->input('buscar'));
        $query = Especialidad::query()->orderBy('nombre');
        if ($search !== '') $query->where('nombre', 'like', '%' . $search . '%');

        return $this->page('v3.system_catalogs.specialties.index', array(
            'models' => $query->paginate(15), 'search' => $search,
        ), 'Especialidades', 'especialidades', 684);
    }

    public function createSpecialty()
    {
        $this->authorizeCatalog('system.specialties', array(1, 4));
        return $this->specialtyForm(new Especialidad, 'Nueva especialidad');
    }

    public function editSpecialty($specialty)
    {
        $this->authorizeCatalog('system.specialties', array(1, 4));
        return $this->specialtyForm(Especialidad::findOrFail($specialty), 'Editar especialidad');
    }

    public function storeSpecialty(Request $request)
    {
        $this->authorizeCatalog('system.specialties', array(1, 4));
        app(\Sis_medico\Http\Controllers\EspecialidadController::class)->store($request);
        return redirect()->route('v3.specialties.index')->with('success', 'Especialidad creada correctamente.');
    }

    public function updateSpecialty(Request $request, $specialty)
    {
        $this->authorizeCatalog('system.specialties', array(1, 4));
        app(\Sis_medico\Http\Controllers\EspecialidadController::class)->update($request, $specialty);
        return redirect()->route('v3.specialties.index')->with('success', 'Especialidad actualizada correctamente.');
    }

    public function importSpecialties(Request $request)
    {
        $this->authorizeCatalog('system.specialties', array(1, 4));
        app(\Sis_medico\Http\Controllers\EspecialidadController::class)->import($request);
        return redirect()->route('v3.specialties.index');
    }

    public function specialtyTemplate()
    {
        $this->authorizeCatalog('system.specialties', array(1, 4));
        return app(\Sis_medico\Http\Controllers\EspecialidadController::class)->downloadImportTemplate();
    }

    public function procedures(Request $request)
    {
        $this->authorizeCatalog('system.procedures', array(1, 4));
        $search = trim((string) $request->input('buscar'));
        $query = Procedimiento::with('grupo_procedimiento')->orderBy('nombre');
        if ($search !== '') $query->where('nombre', 'like', '%' . $search . '%');

        return $this->page('v3.system_catalogs.procedures.index', array(
            'models' => $query->paginate(20), 'search' => $search,
        ), 'Procedimientos', 'procedimientos', 687);
    }

    public function createProcedure()
    {
        $this->authorizeCatalog('system.procedures', array(1, 4));
        return $this->procedureForm(new Procedimiento, 'Nuevo procedimiento');
    }

    public function editProcedure($procedure)
    {
        $this->authorizeCatalog('system.procedures', array(1, 4));
        return $this->procedureForm(Procedimiento::findOrFail($procedure), 'Editar procedimiento');
    }

    public function storeProcedure(Request $request)
    {
        $this->authorizeCatalog('system.procedures', array(1, 4));
        app(\Sis_medico\Http\Controllers\ProcedimientoController::class)->store($request);
        return redirect()->route('v3.procedures.index')->with('success', 'Procedimiento creado correctamente.');
    }

    public function updateProcedure(Request $request, $procedure)
    {
        $this->authorizeCatalog('system.procedures', array(1, 4));
        app(\Sis_medico\Http\Controllers\ProcedimientoController::class)->update($request, $procedure);
        return redirect()->route('v3.procedures.index')->with('success', 'Procedimiento actualizado correctamente.');
    }

    public function importProcedures(Request $request)
    {
        $this->authorizeCatalog('system.procedures', array(1, 4));
        app(\Sis_medico\Http\Controllers\ProcedimientoController::class)->import($request);
        return redirect()->route('v3.procedures.index');
    }

    public function procedureTemplate()
    {
        $this->authorizeCatalog('system.procedures', array(1, 4));
        return app(\Sis_medico\Http\Controllers\ProcedimientoController::class)->downloadImportTemplate();
    }

    public function procedureTypes(Request $request)
    {
        $this->authorizeCatalog('system.procedures', array(1, 4));
        $search = trim((string) $request->input('buscar'));
        $query = grupo_procedimiento::withCount('procedimientos')->orderBy('nombre');
        if ($search !== '') $query->where('nombre', 'like', '%' . $search . '%');

        return $this->page('v3.system_catalogs.procedure_types.index', array(
            'models' => $query->paginate(20), 'search' => $search,
        ), 'Tipos de procedimiento', 'tipos-procedimiento', 696);
    }

    public function createProcedureType()
    {
        $this->authorizeCatalog('system.procedures', array(1, 4));
        return $this->procedureTypeForm(new grupo_procedimiento, 'Nuevo tipo de procedimiento');
    }

    public function editProcedureType($type)
    {
        $this->authorizeCatalog('system.procedures', array(1, 4));
        return $this->procedureTypeForm(grupo_procedimiento::findOrFail($type), 'Editar tipo de procedimiento');
    }

    public function storeProcedureType(Request $request)
    {
        $this->authorizeCatalog('system.procedures', array(1, 4));
        app(\Sis_medico\Http\Controllers\GrupoProcedimientoController::class)->store($request);
        return redirect()->route('v3.procedure-types.index')->with('success', 'Tipo de procedimiento creado correctamente.');
    }

    public function updateProcedureType(Request $request, $type)
    {
        $this->authorizeCatalog('system.procedures', array(1, 4));
        app(\Sis_medico\Http\Controllers\GrupoProcedimientoController::class)->update($request, $type);
        return redirect()->route('v3.procedure-types.index')->with('success', 'Tipo de procedimiento actualizado correctamente.');
    }

    private function specialtyForm(Especialidad $model, $title)
    {
        return $this->page('v3.system_catalogs.specialties.form', array('model' => $model), $title, 'especialidades', 684);
    }

    private function procedureForm(Procedimiento $model, $title)
    {
        return $this->page('v3.system_catalogs.procedures.form', array(
            'model' => $model,
            'groups' => grupo_procedimiento::orderBy('nombre')->get(),
        ), $title, 'procedimientos', 687);
    }

    private function procedureTypeForm(grupo_procedimiento $model, $title)
    {
        return $this->page('v3.system_catalogs.procedure_types.form', array(
            'model' => $model,
        ), $title, 'tipos-procedimiento', 696);
    }

    private function authorizeCatalog($key, array $legacyRoles)
    {
        if ($key === 'system.specialties'
            && app(V3ReceptionDashboardService::class)->isReceptionist(auth()->user(), session('id_empresa'))) {
            return;
        }
        if (!app(SidebarMenuService::class)->canWithLegacy($key, $legacyRoles)) abort(403);
    }

    private function page($view, array $payload, $title, $module, $menuId)
    {
        $data = array(
            'controlador' => $module, 'carpeta' => 'system_catalogs',
            'submodulo' => 'index', 'submodulo_' => 'index', 'menu_id' => $menuId,
            'titulo' => $title, 'title' => $title . ' | Aitrol Hospitalario', 'vista' => $view,
            'obseraudi' => 'Ingreso a ' . $title, 'contenido' => view($view, $payload),
        );
        config(array('data' => array_merge(config('data'), $data)));
        return view('v3.layouts.view_child');
    }
}
