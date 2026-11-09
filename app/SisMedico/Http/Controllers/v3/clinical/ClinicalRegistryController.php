<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Services\V3AvailabilityService;
use Sis_medico\Services\V3BiopsyReportService;
use Sis_medico\Services\V3ClinicalHistoryService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClinicalRegistryController extends Controller
{
    private $history;
    private $availability;
    private $biopsies;

    public function __construct(V3ClinicalHistoryService $history, V3AvailabilityService $availability, V3BiopsyReportService $biopsies)
    {
        $this->middleware('auth');
        $this->history = $history;
        $this->availability = $availability;
        $this->biopsies = $biopsies;
    }

    public function laboratory(Request $request)
    {
        $filters = $this->history->filters($request, true); $companyId = $this->companyId($request);
        return $this->page('v3.clinical.registry.orders', array(
            'mode' => 'laboratory', 'filters' => $filters, 'orders' => $this->history->paginate($filters, $companyId),
            'summary' => $this->history->summary($filters, $companyId),
        ), 'Historial de laboratorio', 'historial-laboratorio');
    }

    public function patientLaboratory(Request $request, $patientId)
    {
        $patientId = trim((string) $patientId);
        if ($patientId === '') abort(404, 'El paciente solicitado no existe.');
        $request->merge(array('cedula' => $patientId));
        return $this->laboratory($request);
    }

    public function patientPrescriptions(Request $request, $patientId)
    {
        $patientId = trim((string) $patientId);
        $companyId = $this->companyId($request);
        $patient = Paciente::find($patientId);
        if (!$patient) abort(404, 'El paciente solicitado no existe.');

        $prescriptions = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'h.hcid', '=', 'r.id_hc')
            ->leftJoin('agenda as a', 'a.id', '=', 'h.id_agenda')
            ->leftJoin('users as d', 'd.id', '=', 'h.id_doctor1')
            ->leftJoin('seguros as s', 's.id', '=', 'h.id_seguro')
            ->where('h.id_paciente', $patientId)
            ->where('a.id_empresa', $companyId)
            ->where(function ($query) {
                $query->whereNotNull('r.prescripcion')->orWhereNotNull('r.rp')
                    ->orWhereExists(function ($details) {
                        $details->select(DB::raw(1))->from('hc_receta_detalle as rd')
                            ->whereRaw('rd.id_hc_receta = r.id');
                    });
            })
            ->select('r.*', 'a.fechaini', 'h.fecha_atencion', 'h.id_doctor1',
                's.nombre as insurance_name', 'd.nombre1 as doctor_name1', 'd.nombre2 as doctor_name2',
                'd.apellido1 as doctor_lastname1', 'd.apellido2 as doctor_lastname2')
            ->orderBy('r.created_at', 'desc')->orderBy('r.id', 'desc')
            ->paginate(10);

        $details = collect();
        if ($prescriptions->count()) {
            $details = hc_receta_detalle::with('medicina.genericos.generico')
                ->whereIn('id_hc_receta', collect($prescriptions->items())->pluck('id')->all())
                ->orderBy('id')->get()->groupBy('id_hc_receta');
        }

        return $this->page('v3.clinical.registry.prescriptions', array(
            'patient' => $patient, 'prescriptions' => $prescriptions, 'details' => $details,
        ), 'Historial de recetas', 'historial-recetas');
    }

    public function patientStudies(Request $request, $patientId)
    {
        $patientId = trim((string) $patientId);
        $companyId = $this->companyId($request);
        $patient = Paciente::find($patientId);
        if (!$patient) abort(404, 'El paciente solicitado no existe.');

        $studies = DB::table('hc_imagenes_protocolo as file')
            ->join('hc_protocolo as protocol', 'protocol.id', '=', 'file.id_hc_protocolo')
            ->join('historiaclinica as history', 'history.hcid', '=', 'protocol.hcid')
            ->leftJoin('agenda as appointment', 'appointment.id', '=', 'history.id_agenda')
            ->leftJoin('users as doctor', 'doctor.id', '=', 'history.id_doctor1')
            ->where('history.id_paciente', $patientId)
            ->where('file.estado', '3')
            ->where('appointment.id_empresa', $companyId)
            ->select('file.id', 'file.nombre', 'file.created_at', 'appointment.fechaini',
                'doctor.nombre1 as doctor_name', 'doctor.apellido1 as doctor_lastname')
            ->orderBy('file.created_at', 'desc')->orderBy('file.id', 'desc')
            ->paginate(12);

        return $this->page('v3.clinical.registry.studies', array(
            'patient' => $patient, 'studies' => $studies,
        ), 'Historial de estudios', 'historial-estudios');
    }

    public function patientImages(Request $request, $patientId)
    {
        $patientId = trim((string) $patientId);
        $companyId = $this->companyId($request);
        $patient = Paciente::find($patientId);
        if (!$patient) abort(404, 'El paciente solicitado no existe.');

        $images = DB::table('hc_imagenes_protocolo as file')
            ->join('hc_protocolo as protocol', 'protocol.id', '=', 'file.id_hc_protocolo')
            ->join('historiaclinica as history', 'history.hcid', '=', 'protocol.hcid')
            ->leftJoin('agenda as appointment', 'appointment.id', '=', 'history.id_agenda')
            ->leftJoin('users as doctor', 'doctor.id', '=', 'history.id_doctor1')
            ->where('history.id_paciente', $patientId)
            ->where('file.estado', '1')
            ->where('appointment.id_empresa', $companyId)
            ->select('file.id', 'file.nombre', 'file.created_at', 'appointment.fechaini',
                'doctor.nombre1 as doctor_name', 'doctor.apellido1 as doctor_lastname')
            ->orderBy('file.created_at', 'desc')->orderBy('file.id', 'desc')
            ->paginate(12);

        return $this->page('v3.clinical.registry.images', array(
            'patient' => $patient, 'images' => $images,
        ), 'Historial de imágenes', 'historial-imagenes');
    }

    public function patientDocuments(Request $request, $patientId)
    {
        $patientId = trim((string) $patientId);
        $companyId = $this->companyId($request);
        $patient = Paciente::find($patientId);
        if (!$patient) abort(404, 'El paciente solicitado no existe.');

        $documents = DB::table('hc_imagenes_protocolo as file')
            ->join('hc_protocolo as protocol', 'protocol.id', '=', 'file.id_hc_protocolo')
            ->join('historiaclinica as history', 'history.hcid', '=', 'protocol.hcid')
            ->leftJoin('agenda as appointment', 'appointment.id', '=', 'history.id_agenda')
            ->leftJoin('users as doctor', 'doctor.id', '=', 'history.id_doctor1')
            ->where('history.id_paciente', $patientId)
            ->where('file.estado', '2')
            ->where('appointment.id_empresa', $companyId)
            ->select('file.id', 'file.nombre', 'file.created_at', 'appointment.fechaini',
                'doctor.nombre1 as doctor_name', 'doctor.apellido1 as doctor_lastname')
            ->orderBy('file.created_at', 'desc')->orderBy('file.id', 'desc')
            ->paginate(12);

        return $this->page('v3.clinical.registry.documents', array(
            'patient' => $patient, 'documents' => $documents,
        ), 'Historial de documentos', 'historial-documentos');
    }

    public function patientBiopsyOrders(Request $request, $patientId)
    {
        $patientId = trim((string) $patientId);
        $companyId = $this->companyId($request);
        $patient = Paciente::find($patientId);
        if (!$patient) abort(404, 'El paciente solicitado no existe.');

        $rows = DB::table('hc4_biopsias as biopsy')
            ->leftJoin('hc_procedimientos as procedure', 'procedure.id', '=', 'biopsy.hc_id_procedimiento')
            ->leftJoin('historiaclinica as history', 'history.hcid', '=', 'biopsy.hcid')
            ->leftJoin('agenda as appointment', 'appointment.id', '=', 'history.id_agenda')
            ->leftJoin('users as doctor', 'doctor.id', '=', 'biopsy.id_doctor')
            ->where('biopsy.id_paciente', $patientId)
            ->where(function ($scope) use ($companyId) {
                $scope->where('procedure.id_empresa', $companyId)
                    ->orWhere('appointment.id_empresa', $companyId);
            })
            ->select(
                'biopsy.id', 'biopsy.hc_id_procedimiento', 'biopsy.hcid', 'biopsy.id_doctor',
                'biopsy.numero_frasco', 'biopsy.descripcion_frasco', 'biopsy.observacion', 'biopsy.created_at',
                'procedure.cuadro_clinico_bp', 'procedure.diagnosticos_bp',
                'doctor.nombre1 as doctor_name1', 'doctor.nombre2 as doctor_name2',
                'doctor.apellido1 as doctor_lastname1', 'doctor.apellido2 as doctor_lastname2'
            )
            ->orderBy('biopsy.created_at', 'desc')->orderBy('biopsy.id', 'desc')->get();

        $orders = collect($rows)->groupBy(function ($row) {
            if ($row->hc_id_procedimiento) return 'procedure-'.$row->hc_id_procedimiento;
            if ($row->hcid) return 'history-'.$row->hcid;
            return 'biopsy-'.$row->id;
        })->map(function ($samples) {
            $first = $samples->first();
            return (object) array(
                'procedure_id' => $first->hc_id_procedimiento,
                'history_id' => $first->hcid,
                'doctor_id' => $first->id_doctor,
                'doctor_name' => trim($first->doctor_lastname1.' '.$first->doctor_lastname2.' '.$first->doctor_name1.' '.$first->doctor_name2),
                'created_at' => $first->created_at,
                'clinical_summary' => $first->cuadro_clinico_bp,
                'diagnosis' => $first->diagnosticos_bp,
                'samples' => $samples->values(),
            );
        })->values();

        $age = null;
        $birthDate = trim((string) $patient->fecha_nacimiento);
        if ($birthDate !== '') {
            try { $age = (new \DateTime(substr($birthDate, 0, 10)))->diff(new \DateTime('today'))->y; }
            catch (\Exception $exception) { $age = null; }
        }

        return $this->page('v3.clinical.registry.biopsy_orders', array(
            'patient' => $patient, 'orders' => $orders, 'age' => $age,
        ), 'Órdenes de biopsias', 'ordenes-biopsias-paciente');
    }

    public function legacyPatientBiopsyOrders($patientId)
    {
        return redirect()->route('v3.patient-biopsy-orders.index', array('patient' => $patientId));
    }

    public function patientBiopsies(Request $request, $patientId)
    {
        $patientId = trim((string) $patientId);
        $companyId = $this->companyId($request);
        $patient = Paciente::find($patientId);
        if (!$patient) abort(404, 'El paciente solicitado no existe.');

        $uploaded = DB::table('paciente_biopsia')->where('id_paciente', $patientId)
            ->where('id_empresa', $companyId)->where('estado', '0')
            ->orderBy('created_at', 'desc')->orderBy('id', 'desc')
            ->paginate(8, array('*'), 'subidas');

        $clinical = DB::table('hc_imagenes_protocolo as file')
            ->join('hc_protocolo as protocol', 'protocol.id', '=', 'file.id_hc_protocolo')
            ->join('historiaclinica as history', 'history.hcid', '=', 'protocol.hcid')
            ->leftJoin('agenda as appointment', 'appointment.id', '=', 'history.id_agenda')
            ->leftJoin('users as doctor', 'doctor.id', '=', 'history.id_doctor1')
            ->where('history.id_paciente', $patientId)->where('file.estado', '4')
            ->where('appointment.id_empresa', $companyId)
            ->select('file.id', 'file.nombre', 'file.created_at', 'appointment.fechaini',
                'doctor.nombre1 as doctor_name', 'doctor.apellido1 as doctor_lastname')
            ->orderBy('file.created_at', 'desc')->orderBy('file.id', 'desc')
            ->paginate(8, array('*'), 'clinicas');

        return $this->page('v3.clinical.registry.biopsy_files', array(
            'patient' => $patient, 'uploaded' => $uploaded, 'clinical' => $clinical,
            'backUrl' => $this->biopsyBackUrl($request),
        ), 'Archivos de biopsias', 'archivos-biopsias');
    }

    public function legacyPatientBiopsyFiles(Request $request, $patientId)
    {
        return redirect()->route('v3.patient-biopsy-files.index', array_filter(array(
            'patient' => $patientId,
            'volver' => $this->biopsyBackUrl($request),
        )));
    }

    public function uploadPatientBiopsies(Request $request)
    {
        $companyId = $this->companyId($request);
        $this->validate($request, array(
            'id_paciente' => 'required',
            'foto' => 'required',
            'foto.*' => 'file|max:20480|mimes:pdf,doc,docx,txt,xls,xlsx,jpg,jpeg,png,gif',
        ));
        $patient = Paciente::find($request->input('id_paciente'));
        if (!$patient) abort(404, 'El paciente solicitado no existe.');
        $hasCompanyCare = DB::table('agenda')->where('id_paciente', $patient->id)
            ->where('id_empresa', $companyId)->exists();
        if (!$hasCompanyCare) abort(403, 'El paciente no pertenece a la empresa activa.');
        $files = $request->file('foto');
        if (!is_array($files)) $files = array($files);

        foreach ($files as $file) {
            $record = Paciente_Biopsia::create(array(
                'id_paciente' => $patient->id, 'estado' => '0', 'id_empresa' => $companyId,
                'id_usuariocrea' => Auth::id(), 'id_usuariomod' => Auth::id(),
                'ip_creacion' => $request->ip(), 'ip_modificacion' => $request->ip(),
            ));
            $extension = strtolower($file->getClientOriginalExtension());
            $fileName = 'hc_biopsias_'.$patient->id.'_'.date('Ymd').'_'.$record->id.'.'.$extension;
            Storage::disk('hc_ima')->put($fileName, file_get_contents($file->getRealPath()));
            $record->nombre = $fileName;
            $record->save();
        }

        return redirect()->route('v3.patient-biopsy-files.index', array_filter(array(
            'patient' => $patient->id,
            'volver' => $this->biopsyBackUrl($request),
        )))
            ->with('success', 'Los archivos de biopsia se cargaron correctamente.');
    }

    public function deletePatientBiopsy(Request $request, $id)
    {
        $record = Paciente_Biopsia::where('id', $id)
            ->where('id_empresa', $this->companyId($request))->where('estado', '0')->firstOrFail();
        $record->estado = '2';
        $record->id_usuariomod = Auth::id();
        $record->ip_modificacion = $request->ip();
        $record->save();
        return back()->with('success', 'Archivo eliminado correctamente.');
    }

    public function orders(Request $request)
    {
        $filters = $this->history->filters($request); $companyId = $this->companyId($request);
        return $this->page('v3.clinical.registry.orders', array(
            'mode' => 'orders', 'filters' => $filters, 'orders' => $this->history->paginate($filters, $companyId),
            'summary' => $this->history->summary($filters, $companyId),
        ), 'Historial de órdenes clínicas', 'historial-ordenes');
    }

    public function availability(Request $request)
    {
        $companyId = $this->companyId($request);
        return $this->page('v3.clinical.registry.availability', array('hospitals' => $this->availability->hospitals($companyId)), 'Disponibilidad de consultorios', 'disponibilidad-consultorios');
    }

    public function availabilitySchedule(Request $request, $hospitalId)
    {
        $date = $this->validDate($request->input('fecha'), date('Y-m-d'));
        $schedule = $this->availability->schedule($this->companyId($request), (int) $hospitalId, $date, $request->input('sala'));
        if (!$schedule) abort(404, 'La unidad no pertenece a la empresa activa.');
        $schedule['date'] = $date;
        return $this->page('v3.clinical.registry.availability_schedule', $schedule, 'Agenda de consultorios', 'disponibilidad-consultorios');
    }

    public function biopsies(Request $request)
    {
        $filters = $this->biopsies->filters($request); $companyId = $this->companyId($request);
        return $this->page('v3.clinical.registry.biopsies', array(
            'filters' => $filters, 'biopsies' => $this->biopsies->paginate($filters, $companyId),
        ), 'Resultado de biopsias', 'resultado-biopsias');
    }

    public function exportBiopsies(Request $request)
    {
        $filters = $this->biopsies->filters($request); $query = $this->biopsies->query($filters, $this->companyId($request));
        $response = new StreamedResponse(function () use ($query) {
            $output = fopen('php://output', 'w'); fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, array('FECHA', 'CEDULA', 'PACIENTE', 'SEGURO', 'FRASCO', 'DESCRIPCION', 'OBSERVACION', 'DOCTOR'), ';');
            $query->orderBy('b.created_at')->chunk(250, function ($rows) use ($output) {
                foreach ($rows as $row) fputcsv($output, array_map(array($this, 'safeCsv'), array(
                    substr($row->created_at, 0, 19), $row->id_paciente,
                    trim($row->patient_lastname1.' '.$row->patient_lastname2.' '.$row->patient_name1.' '.$row->patient_name2),
                    $row->insurance_name, $row->numero_frasco, $row->descripcion_frasco, $row->observacion,
                    trim($row->doctor_lastname1.' '.$row->doctor_name1),
                )), ';');
            });
            fclose($output);
        });
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="reporte-biopsias-'.date('Ymd-His').'.csv"');
        return $response;
    }

    public function safeCsv($value)
    {
        $value = (string) $value;
        return preg_match('/^[=+\-@]/', ltrim($value)) ? "'".$value : $value;
    }

    public function legacyLaboratory(Request $request) { return redirect()->route('v3.clinical-registry.laboratory', $this->legacyFilters($request)); }
    public function legacyOrders(Request $request) { return redirect()->route('v3.clinical-registry.orders', $this->legacyFilters($request)); }
    public function legacyAvailability() { return redirect()->route('v3.clinical-registry.availability'); }
    public function legacyAvailabilityHospital($hospitalId) { return redirect()->route('v3.clinical-registry.availability.schedule', array('hospitalId' => $hospitalId)); }
    public function legacyBiopsies(Request $request) { return redirect()->route('v3.clinical-registry.biopsies', $this->legacyFilters($request)); }

    private function legacyFilters(Request $request)
    {
        return array_filter(array(
            'desde' => $request->input('fecha', $request->input('desde')), 'hasta' => $request->input('fecha_hasta', $request->input('hasta')),
            'paciente' => $request->input('nombres', $request->input('paciente')), 'cedula' => $request->input('cedula'),
            'estado' => $request->input('estado'), 'categoria' => $request->input('categoria'),
        ), function ($value) { return $value !== null && $value !== ''; });
    }

    private function companyId(Request $request)
    {
        $companyId = trim((string) $request->session()->get('id_empresa'));
        if ($companyId === '') abort(403, 'Seleccione una empresa activa.');
        return $companyId;
    }

    private function validDate($value, $default)
    {
        $value = trim((string) $value); $date = \DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : $default;
    }

    /**
     * Conserva la pantalla de origen al entrar por la ruta heredada. Esa ruta
     * redirige a V3, por lo que usar history.back() vuelve al redirect y deja
     * al usuario atrapado en la misma página.
     */
    private function biopsyBackUrl(Request $request)
    {
        $candidate = trim((string) $request->input('volver', $request->headers->get('referer', '')));
        if ($candidate === '') return '';

        $parts = parse_url($candidate);
        if ($parts === false || empty($parts['host'])
            || strcasecmp((string) $parts['host'], (string) $request->getHost()) !== 0) {
            return '';
        }

        $path = isset($parts['path']) ? trim((string) $parts['path'], '/') : '';
        // Esta URL es solo el salto heredado hacia V3; no sirve como destino.
        if (strpos($path, 'images_ingreso_biopsias/') === 0) return '';

        return $candidate;
    }

    private function page($view, array $payload, $title, $module)
    {
        $data = array(
            'controlador' => $module, 'carpeta' => 'clinical', 'submodulo' => 'index', 'submodulo_' => 'index',
            'titulo' => $title, 'title' => $title.' | Aitrol Hospitalario', 'vista' => $view,
            'obseraudi' => 'Ingreso a '.$title, 'contenido' => view($view, $payload),
        );
        $current = config('data');
        config(array('data' => array_merge(is_array($current) ? $current : array(), $data)));
        return view('v3.layouts.view_child');
    }
}
