<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3NursingSupplyService;

class NursingSupplyController extends Controller
{
    private $supplies;

    public function __construct(V3NursingSupplyService $supplies) { $this->middleware('auth'); $this->supplies = $supplies; }

    public function index(Request $request)
    {
        return redirect()->route('v3.clinical-operations.nursing', array_filter(array(
            'desde' => $request->input('desde', $request->input('fecha')),
            'hasta' => $request->input('hasta', $request->input('fechafin')),
            'paciente' => $request->input('paciente', $request->input('nombres')),
            'cedula' => $request->input('cedula'),
        ), function ($value) { return $value !== null && $value !== ''; }));
    }

    public function detail(Request $request, $agenda)
    {
        return $this->page('v3.clinical.nursing_supplies.detail', $this->supplies->detail($agenda, $this->companyId($request)), 'Detalle de insumos usados');
    }

    public function storeWarehouse(Request $request, $agenda)
    {
        return $this->mutation($request, $agenda, 'Bodega seleccionada correctamente.', function ($companyId) use ($request, $agenda) {
            $this->supplies->storeWarehouse($agenda, $request->input('id_bodega'), $companyId);
        });
    }

    public function updateObservation(Request $request, $agenda)
    {
        return $this->mutation($request, $agenda, 'Observación guardada correctamente.', function ($companyId) use ($request, $agenda) {
            $observation = trim((string) $request->input('observacion'));
            if (mb_strlen($observation, 'UTF-8') > 5000) throw new \DomainException('La observación no puede superar los 5000 caracteres.');
            $this->supplies->updateObservation($agenda, $observation, $companyId);
        });
    }

    public function storeEquipment(Request $request, $agenda)
    {
        return $this->mutation($request, $agenda, 'Equipo registrado correctamente.', function ($companyId) use ($request, $agenda) {
            $code = trim((string) $request->input('codigo'));
            if ($code === '' || mb_strlen($code, 'UTF-8') > 100) throw new \DomainException('Ingrese un código o serie de equipo válido.');
            $this->supplies->storeEquipment($agenda, $code, $companyId);
        });
    }

    public function destroyEquipment(Request $request, $agenda, $equipmentHistory)
    {
        return $this->mutation($request, $agenda, 'Equipo eliminado de la atención.', function ($companyId) use ($agenda, $equipmentHistory) {
            $this->supplies->destroyEquipment($agenda, $equipmentHistory, $companyId);
        });
    }

    public function storeSupply(Request $request, $agenda, $procedure)
    {
        return $this->mutation($request, $agenda, 'Insumo registrado correctamente.', function ($companyId) use ($request, $agenda, $procedure) {
            $this->supplies->storeSupply($request, $agenda, $procedure, $companyId);
        });
    }

    public function moveSupply(Request $request, $agenda, $procedure, $movement)
    {
        return $this->mutation($request, $agenda, 'Insumo movido al procedimiento seleccionado.', function ($companyId) use ($request, $agenda, $procedure, $movement) {
            $target = trim((string) $request->input('target_procedure'));
            if ($target === '') throw new \DomainException('Seleccione el procedimiento de destino.');
            $this->supplies->moveSupply($agenda, $procedure, $movement, $target, $companyId);
        });
    }

    public function destroySupply(Request $request, $agenda, $procedure, $movement)
    {
        return $this->mutation($request, $agenda, 'Insumo retirado correctamente.', function ($companyId) use ($agenda, $procedure, $movement) {
            $this->supplies->destroySupply($agenda, $procedure, $movement, $companyId);
        });
    }

    public function legacyIndex(Request $request) { return $this->index($request); }
    public function legacyDetail($agenda) { return redirect()->route('v3.nursing-supplies.detail', array('agenda' => $agenda)); }

    private function companyId(Request $request) { $id = trim((string) $request->session()->get('id_empresa')); if ($id === '') abort(403, 'Seleccione una empresa activa.'); return $id; }

    private function mutation(Request $request, $agenda, $message, callable $operation)
    {
        try {
            $companyId = $this->companyId($request);
            $operation($companyId);
            $payload = $this->supplies->detail($agenda, $companyId);
            return response()->json(array(
                'status' => 'ok',
                'message' => $message,
                'html' => view('v3.clinical.nursing_supplies._workspace', $payload)->render(),
            ));
        } catch (\DomainException $exception) {
            return response()->json(array('status' => 'error', 'message' => $exception->getMessage()), 422);
        } catch (\Throwable $exception) {
            Log::error('Error en insumos usados V3', array('agenda' => $agenda, 'user' => auth()->id(), 'exception' => $exception));
            return response()->json(array('status' => 'error', 'message' => 'No se pudo completar la operación. Inténtelo nuevamente.'), 500);
        }
    }
    private function page($view, array $payload, $title)
    {
        $data = array('controlador' => 'insumos-usados', 'carpeta' => 'clinical', 'submodulo' => 'index', 'submodulo_' => 'index', 'titulo' => $title, 'title' => $title.' | Aitrol Hospitalario', 'vista' => $view, 'obseraudi' => 'Ingreso a '.$title, 'contenido' => view($view, $payload));
        config(array('data' => array_merge(config('data'), $data))); return view('v3.layouts.view_child');
    }
}
