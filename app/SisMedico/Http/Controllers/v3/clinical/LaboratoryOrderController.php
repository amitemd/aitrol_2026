<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3LaboratoryOrderService;
use Sis_medico\Services\v3\AppointmentLaboratoryOrderService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaboratoryOrderController extends Controller
{
    private $orders;
    private $appointmentOrders;

    public function __construct(V3LaboratoryOrderService $orders, AppointmentLaboratoryOrderService $appointmentOrders)
    {
        $this->middleware('auth');
        $this->orders = $orders;
        $this->appointmentOrders = $appointmentOrders;
    }

    public function index(Request $request)
    {
        $filters = $this->orders->filters($request); $companyId = $this->companyId($request);
        $patient = $filters['paciente_id'] !== '' ? $this->patientForCompany($filters['paciente_id'], $companyId) : null;
        return $this->page('v3.clinical.laboratory_orders.index', array(
            'filters' => $filters, 'orders' => $this->orders->paginate($filters, $companyId),
            'insurances' => $this->orders->insurances(), 'pendingOnline' => $this->orders->onlinePaymentsPending($companyId),
            'patientContext' => $patient,
        ));
    }

    public function patient(Request $request, $patientId)
    {
        $patientId = trim((string) $patientId);
        $parameters = array('paciente_id' => $patientId);
        if (!$request->filled('desde') && !$request->filled('fecha')) {
            $parameters['desde'] = date('Y-m-d', strtotime('-1 month'));
        }
        $request->merge($parameters);
        return $this->index($request);
    }

    public function legacyPatient($patientId)
    {
        return redirect()->route('v3.patient-laboratory-orders.index', array('patient' => trim((string) $patientId)));
    }

    public function createForAppointment(Request $request, $doctor, $appointment)
    {
        $companyId = $this->companyId($request);
        $agenda = $this->appointmentForOrder($appointment, $doctor, $companyId);
        $exams = DB::table('examen')->where('publico_privado', '0')
            ->select('id', 'id_agrupador', 'nombre', 'valor', 'estado')->orderBy('id_agrupador')->orderBy('nombre')->get();
        $groups = DB::table('examen_agrupador')->where('estado', '1')
            ->select('id', 'nombre')->orderBy('id')->get();
        $protocols = DB::table('protocolo')->where('estado', '1')
            ->select('id', 'nombre', 'pre_post')->orderBy('nombre')->get();
        $protocolExamMap = array();
        if ($protocols->count()) {
            $details = DB::table('examen_protocolo')->whereIn('id_protocolo', $protocols->pluck('id')->all())
                ->select('id_protocolo', 'id_examen')->get();
            foreach ($details as $detail) {
                if (!isset($protocolExamMap[$detail->id_protocolo])) $protocolExamMap[$detail->id_protocolo] = array();
                $protocolExamMap[$detail->id_protocolo][] = (string) $detail->id_examen;
            }
        }
        $hasConvention = DB::table('convenio')->where('id_empresa', $companyId)
            ->where('id_seguro', $agenda->id_seguro)->exists();

        return $this->page('v3.clinical.laboratory_orders.create_appointment', array(
            'agenda' => $agenda, 'exams' => $exams, 'groups' => $groups, 'protocols' => $protocols,
            'protocolExamMap' => $protocolExamMap, 'hasConvention' => $hasConvention,
        ), 'Nueva orden de laboratorio');
    }

    public function legacyCreateForAppointment(Request $request, $appointment, $doctor)
    {
        $this->appointmentForOrder($appointment, $doctor, $this->companyId($request));
        return redirect()->route('v3.appointment-laboratory-orders.create', array(
            'doctor' => $doctor, 'appointment' => $appointment,
        ));
    }

    public function storeForAppointment(Request $request, $doctor, $appointment)
    {
        $agenda = $this->appointmentForOrder($appointment, $doctor, $this->companyId($request));
        if ((string) $request->input('id_agenda') !== (string) $agenda->id
            || (string) $request->input('id') !== (string) $agenda->id_paciente) {
            abort(422, 'Los datos de la orden no corresponden a la agenda seleccionada.');
        }
        $this->validate($request, array(
            'id' => 'required|max:10',
            'id_agenda' => 'required|integer',
            'sexo' => 'required|in:1,2',
            'fecha_nacimiento' => 'required|date_format:Y-m-d',
            'est_amb_hos' => 'required|in:0,1',
            'id_protocolo' => 'required|integer',
            'observacion' => 'nullable|max:200',
        ), array(
            'sexo.required' => 'Selecciona el sexo del paciente.',
            'fecha_nacimiento.required' => 'Ingresa la fecha de nacimiento del paciente.',
            'fecha_nacimiento.date_format' => 'La fecha de nacimiento no tiene un formato válido.',
            'id_protocolo.required' => 'Selecciona el protocolo de laboratorio.',
            'observacion.max' => 'La observación no puede superar los 200 caracteres.',
        ));
        $orderId = $this->appointmentOrders->create($request, $agenda, auth()->id());
        return redirect()->route('v3.patient-laboratory-orders.index', array('patient' => $agenda->id_paciente))
            ->with('success', 'La orden de laboratorio #'.$orderId.' se generó correctamente.');
    }

    public function export(Request $request, $type)
    {
        if (!in_array($type, array('resumen', 'detalle', 'medicos'), true)) abort(404);
        $filters = $this->orders->filters($request); $rows = $this->orders->all($filters, $this->companyId($request));
        $response = new StreamedResponse(function () use ($rows, $type) {
            $out = fopen('php://output', 'w'); fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, array('ID', 'FECHA', 'PACIENTE', 'SEGURO', 'TIPO', 'EXAMENES', 'RESULTADOS', 'VALOR', 'CREADO POR', 'MODIFICADO POR'), ';');
            foreach ($rows as $row) fputcsv($out, array_map(array($this, 'safeCsv'), array(
                $row->id, $row->fecha_orden, $this->patientName($row), $row->insurance_name,
                $this->status($row), $row->exam_count, $row->result_count, $row->total_valor,
                trim($row->creator_name.' '.$row->creator_lastname), trim($row->editor_name.' '.$row->editor_lastname),
            )), ';');
            fclose($out);
        });
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="ordenes-laboratorio-'.$type.'-'.date('Ymd-His').'.csv"');
        return $response;
    }

    public function publicOrder() { return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->create(); }
    public function privateOrder() { return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->crear_particular(); }
    public function onlinePayments(Request $request) { return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->pagoenlinea_gestionar($request); }
    public function returnOrder(Request $request) { return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->eliminar_devolver($request); }
    public function groupedInvoices() { return app('Sis_medico\\Http\\Controllers\\laboratorio\\FacturaAgrupadaController')->index_factura_agrupada(); }
    public function commissions(Request $request) { return app('Sis_medico\\Http\\Controllers\\laboratorio\\LabsComisionesController')->comisiones($request); }
    public function cashClose() { return redirect()->route('c_caja.index'); }
    public function detail($id) { $this->owned($id); return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->detalle($id, 'rec'); }
    public function orderPdf($id) { $this->owned($id); return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->descargar($id); }
    public function results($id) { $this->owned($id); return redirect()->route('resultados.imprimir', array('id_orden' => $id)); }
    public function edit($id) { $order = $this->owned($id); return redirect()->route(((int) $order->insurance_type === 0 ? 'orden.edit' : 'cotizador.editar'), array('id' => $id)); }
    public function comments($id) { $this->owned($id); return app('Sis_medico\\Http\\Controllers\\laboratorio\\OrdenController')->comentarios_ordenes($id); }

    public function safeCsv($value) { $value = (string) $value; return preg_match('/^[=+\-@]/', ltrim($value)) ? "'".$value : $value; }

    private function owned($id)
    {
        $row = \DB::table('examen_orden as eo')->leftJoin('seguros as s', 's.id', '=', 'eo.id_seguro')
            ->where('eo.id', (int) $id)->where('eo.id_empresa', session()->get('id_empresa'))->select('eo.*', 's.tipo as insurance_type')->first();
        if (!$row) abort(404); return $row;
    }

    private function patientForCompany($patientId, $companyId)
    {
        if ($patientId === '') abort(404, 'El paciente solicitado no existe.');
        $patient = DB::table('paciente as p')->join('agenda as a', 'a.id_paciente', '=', 'p.id')
            ->where('p.id', $patientId)->where('a.id_empresa', $companyId)
            ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2')->first();
        if (!$patient) abort(404, 'El paciente no tiene atenciones en la empresa activa.');
        return $patient;
    }

    private function appointmentForOrder($appointment, $doctor, $companyId)
    {
        $agenda = DB::table('agenda as a')->join('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->leftJoin('users as d', 'd.id', '=', 'a.id_doctor1')
            ->leftJoin('seguros as s', 's.id', '=', 'a.id_seguro')
            ->leftJoin('empresa as e', 'e.id', '=', 'a.id_empresa')
            ->where('a.id', (int) $appointment)->where('a.id_empresa', $companyId)
            ->where('a.id_doctor1', (string) $doctor)
            ->select('a.id', 'a.id_paciente', 'a.id_doctor1', 'a.id_seguro', 'a.id_empresa', 'a.est_amb_hos',
                'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.sexo', 'p.fecha_nacimiento',
                'd.nombre1 as doctor_name', 'd.apellido1 as doctor_lastname', 's.nombre as insurance_name',
                'e.nombrecomercial as company_name')->first();
        if (!$agenda) abort(404, 'La agenda no pertenece al médico y empresa activos.');
        return $agenda;
    }

    private function companyId(Request $request) { $id = trim((string) $request->session()->get('id_empresa')); if ($id === '') abort(403, 'Seleccione una empresa activa.'); return $id; }
    private function patientName($row) { return trim($row->patient_lastname1.' '.$row->patient_lastname2.' '.$row->patient_name1.' '.$row->patient_name2); }
    private function status($row) { if ((int) $row->estado === -1) return (int) $row->insurance_type === 0 ? 'PENDIENTE' : 'COTIZACION'; return (int) $row->estado_pago === 1 ? 'PAGADA' : 'REGISTRADA'; }
    private function page($view, array $payload, $title = 'Órdenes de laboratorio')
    {
        $data = array('controlador' => 'ordenes-laboratorio', 'carpeta' => 'clinical', 'submodulo' => 'index', 'submodulo_' => 'index',
            'menu_id' => 671, 'titulo' => $title, 'title' => $title.' | Aitrol Hospitalario', 'vista' => $view,
            'obseraudi' => 'Consulta de órdenes de laboratorio V3', 'contenido' => view($view, $payload));
        config(array('data' => array_merge(config('data'), $data))); return view('v3.layouts.view_child');
    }
}
