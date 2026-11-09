<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3Order012ExportService;
use Sis_medico\Services\V3Order012Service;

class Order012Controller extends Controller
{
    private $orders;
    private $exporter;

    public function __construct(V3Order012Service $orders, V3Order012ExportService $exporter)
    {
        $this->middleware('auth');
        $this->orders = $orders;
        $this->exporter = $exporter;
    }

    public function index(Request $request)
    {
        $filters = $this->orders->filters($request);
        $orders = $this->orders->paginate($filters, $this->companyId($request));
        return $this->page('v3.clinical.order012.index', compact('filters', 'orders') + array(
            'doctors' => $this->orders->doctors(),
        ), 'Órdenes ingresadas - Formato 012');
    }

    public function edit(Request $request, $id)
    {
        $order = $this->orders->find($id, $this->companyId($request));
        return $this->page('v3.clinical.order012.edit', array(
            'order' => $order,
            'doctors' => $this->orders->doctors(),
            'diagnoses' => $this->orders->diagnoses($order->id),
        ), 'Editar Formato 012');
    }

    public function update(Request $request, $id)
    {
        try {
            $this->orders->update($id, $request, $this->companyId($request), auth()->user()->id, $request->ip());
            return redirect()->route('v3.order012.edit', array('id' => $id))->with('success', 'La orden se actualizó correctamente.');
        } catch (\InvalidArgumentException $exception) {
            return redirect()->back()->withInput()->withErrors(array($exception->getMessage()));
        }
    }

    public function addDiagnosis(Request $request, $id)
    {
        try {
            $this->orders->addDiagnosis($id, $request, $this->companyId($request), auth()->user()->id, $request->ip());
            return redirect()->route('v3.order012.edit', array('id' => $id))->with('success', 'Diagnóstico agregado.');
        } catch (\InvalidArgumentException $exception) {
            return redirect()->back()->withInput()->withErrors(array($exception->getMessage()));
        }
    }

    public function deleteDiagnosis(Request $request, $diagnosis)
    {
        $this->orders->deleteDiagnosis($diagnosis, $this->companyId($request));
        return redirect()->back()->with('success', 'Diagnóstico retirado de la orden.');
    }

    public function diagnoses(Request $request)
    {
        return response()->json($this->orders->searchDiagnoses($request->input('term')));
    }

    public function export(Request $request, $id)
    {
        return $this->exporter->export($id, $this->companyId($request));
    }

    public function legacyUpdate(Request $request)
    {
        $id = $request->input('id_orden');
        if (!$id) abort(422, 'No se indicó la orden que se desea actualizar.');
        return $this->update($request, $id);
    }

    public function legacyIndex()
    {
        return redirect()->route('v3.order012.index');
    }

    public function legacySearch(Request $request)
    {
        return redirect()->route('v3.order012.index', array(
            'desde' => $request->input('fecha'),
            'hasta' => $request->input('fecha_hasta'),
            'cedula' => $request->input('cedula'),
            'paciente' => $request->input('nombres'),
            'doctor' => $request->input('id_doctor1'),
        ));
    }

    public function legacyEdit($id)
    {
        return redirect()->route('v3.order012.edit', array('id' => $id));
    }

    private function companyId(Request $request)
    {
        $companyId = trim((string) $request->session()->get('id_empresa'));
        if ($companyId === '') abort(403, 'Seleccione una empresa activa.');
        return $companyId;
    }

    private function page($view, array $payload, $title)
    {
        $data = array(
            'controlador' => 'ordenes-formato-012',
            'carpeta' => 'clinical',
            'submodulo' => 'index',
            'submodulo_' => 'index',
            'titulo' => $title,
            'title' => $title.' | Aitrol Hospitalario',
            'vista' => $view,
            'obseraudi' => 'Ingreso a '.$title,
            'contenido' => view($view, $payload),
        );
        config(array('data' => array_merge(config('data'), $data)));
        return view('v3.layouts.view_child');
    }
}
