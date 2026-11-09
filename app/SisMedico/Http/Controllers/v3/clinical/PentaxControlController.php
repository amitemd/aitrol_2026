<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\PentaxController as LegacyPentaxController;
use Sis_medico\Services\V3PentaxControlService;
use Sis_medico\Services\V3PentaxProcedureExportService;

class PentaxControlController extends Controller
{
    private $pentax;
    private $exporter;

    public function __construct(V3PentaxControlService $pentax, V3PentaxProcedureExportService $exporter)
    {
        $this->middleware('auth');
        $this->pentax = $pentax;
        $this->exporter = $exporter;
    }

    public function index(Request $request)
    {
        $this->assertRole();
        try {
            $companyId = $this->companyId($request);
            $date = $request->input('fecha', date('Y-m-d'));
            $this->pentax->synchronizeAdmissions($companyId, $date, Auth::id(), $request->ip());
            $payload = $this->pentax->dashboard($companyId, $date);
        } catch (RuntimeException $exception) {
            return $this->redirect(date('Y-m-d'))->withErrors(array('pentax' => $exception->getMessage()));
        }
        return $this->page('v3.clinical.pentax.index', $payload, 'Control de procedimientos');
    }

    public function report(Request $request)
    {
        $this->assertRole();
        $date = $request->input('fecha', date('Y-m-d'));
        try {
            $companyId = $this->companyId($request);
            $this->pentax->synchronizeAdmissions($companyId, $date, Auth::id(), $request->ip());
            $payload = $this->pentax->dashboard($companyId, $date);
            return $this->exporter->download($payload['rows'], $payload['date']);
        } catch (\Exception $exception) {
            Log::error('No se pudo generar el reporte de procedimientos.', array(
                'fecha' => $date, 'usuario' => Auth::id(), 'error' => $exception->getMessage(),
            ));
            return $this->redirect($date)->withErrors(array(
                'pentax' => 'No se pudo generar el reporte. Intente nuevamente o comuníquese con Sistemas.',
            ));
        }
    }

    public function waitingRoom(Request $request)
    {
        $this->assertRole();
        return $this->page('pentax.pentaxtv', array('fecha' => '0'), 'Sala de espera', 107);
    }

    public function edit(Request $request, $pentax)
    {
        $this->assertRole();
        try {
            $payload = $this->pentax->editor($this->companyId($request), $pentax);
        } catch (RuntimeException $exception) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(array('message' => $exception->getMessage()), 404);
            }
            return $this->redirect($request->input('fecha'))->withErrors(array('pentax' => $exception->getMessage()));
        }
        $payload['date'] = $request->input('fecha', date('Y-m-d'));
        if ($request->ajax()) return view('v3.clinical.pentax._edit_modal', $payload);
        return $this->page('v3.clinical.pentax.edit', $payload, 'Gestionar procedimiento');
    }

    public function update(Request $request, $pentax)
    {
        $this->assertRole();
        $this->validate($request, array(
            'estado' => 'required|integer|in:-1,0,1,2,3,4,5', 'proc' => 'required|array|min:1', 'proc.*' => 'integer',
            'id_sala' => 'required|integer', 'id_doctor1' => 'required|string|max:20',
            'id_doctor2' => 'nullable|string|max:20', 'id_doctor3' => 'nullable|string|max:20',
            'id_doctor4' => 'nullable|string|max:20', 'id_anestesiologo' => 'nullable|string|max:20',
            'id_seguro' => 'required|integer', 'id_subseguro' => 'nullable|integer', 'observacion' => 'nullable|string|max:255',
            'fecha' => 'required|date_format:Y-m-d',
        ));
        try {
            $companyId = $this->companyId($request);
            DB::transaction(function () use ($request, $pentax, $companyId) {
                $record = $this->pentax->assertUpdate($companyId, $pentax, $request->all());
                $request->merge(array('id' => $record->id, 'hora' => strtotime($request->input('fecha'))));
                app(LegacyPentaxController::class)->actualiza($request);
            });
        } catch (RuntimeException $exception) {
            if ($request->ajax() || $request->wantsJson()) return response()->json(array('message' => $exception->getMessage()), 422);
            return back()->withInput()->withErrors(array('pentax' => $exception->getMessage()));
        }
        if ($request->ajax() || $request->wantsJson()) return response()->json(array(
            'ok' => true, 'message' => 'El procedimiento se actualizó correctamente.',
            'redirect' => route('v3.pentax-control.index', array('fecha' => $request->input('fecha'), '_v3' => 1, '_v3_menu' => 106)),
        ));
        return $this->redirect($request->input('fecha'))->with('success', 'El procedimiento se actualizó correctamente.');
    }

    public function room(Request $request, $pentax)
    {
        $this->assertRole();
        $this->validate($request, array('id_sala' => 'required|integer', 'fecha' => 'required|date_format:Y-m-d'));
        try {
            $this->pentax->changeRoom($this->companyId($request), $pentax, $request->input('id_sala'), Auth::id(), $request->ip());
        } catch (RuntimeException $exception) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(array('message' => $exception->getMessage()), 422);
            }
            return back()->withErrors(array('pentax' => $exception->getMessage()));
        }
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(array('ok' => true, 'message' => 'La sala fue actualizada.'));
        }
        return $this->redirect($request->input('fecha'))->with('success', 'La sala fue actualizada.');
    }

    public function logs(Request $request, $pentax)
    {
        $this->assertRole();
        try {
            $payload = $this->pentax->logs($this->companyId($request), $pentax);
        } catch (RuntimeException $exception) {
            return $this->redirect($request->input('fecha'))->withErrors(array('pentax' => $exception->getMessage()));
        }
        $payload['date'] = $request->input('fecha', date('Y-m-d'));
        return $this->page('v3.clinical.pentax.logs', $payload, 'Trazabilidad de procedimientos');
    }

    private function assertRole()
    {
        if (!Auth::check() || !in_array((int) Auth::user()->id_tipo_usuario, array(1, 4, 5, 11, 20), true)) abort(403, 'No tiene permiso para administrar procedimientos.');
    }

    private function companyId(Request $request)
    {
        $companyId = trim((string) $request->session()->get('id_empresa'));
        if ($companyId === '') abort(403, 'Seleccione una empresa activa.');
        return $companyId;
    }

    private function redirect($date)
    {
        return redirect()->route('v3.pentax-control.index', array('fecha' => $date ?: date('Y-m-d'), '_v3' => 1, '_v3_menu' => 106));
    }

    private function page($view, array $payload, $title, $menuId = 106)
    {
        $data = array('controlador' => 'pentax', 'carpeta' => 'clinical', 'submodulo' => 'controlpentax',
            'submodulo_' => 'controlpentax', 'menu_id' => $menuId, 'id_menu' => $menuId, 'titulo' => $title,
            'title' => $title.' | Aitrol Hospitalario', 'vista' => $view, 'obseraudi' => 'Ingreso a '.$title,
            'native_table' => true, 'contenido' => view($view, $payload));
        config(array('data' => array_merge(config('data') ?: array(), $data)));
        return view('v3.layouts.view_child');
    }
}
