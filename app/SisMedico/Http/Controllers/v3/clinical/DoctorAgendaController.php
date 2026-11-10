<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Sis_medico\Agenda_archivo;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Seguro;
use Sis_medico\Services\DoctorUserType;
use Sis_medico\Services\SidebarMenuService;
use Sis_medico\Services\V3DoctorAgendaService;

class DoctorAgendaController extends Controller
{
    private $service;

    private $controlador = 'agenda';

    public function __construct(V3DoctorAgendaService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    public function calendar(Request $request, string $doctor)
    {
        $data['submodulo_'] = $request->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $request->submodulo;
        try {
            $payload = $this->service->calendar($this->companyId($request), $doctor, $request->input('fecha'), filter_var($request->input('canceladas'), FILTER_VALIDATE_BOOLEAN), $request->input('vista'));
        } catch (RuntimeException $exception) {
            abort(404, $exception->getMessage());
        }

        return $this->page('v3.clinical.agenda.calendar', $payload, 'Agenda del médico', 'calendario');
    }

    public function week(Request $request, $doctor)
    {
        try {
            $payload = $this->service->calendar($this->companyId($request), $doctor, $request->input('fecha'), filter_var($request->input('canceladas'), FILTER_VALIDATE_BOOLEAN), $request->input('vista'));
        } catch (RuntimeException $exception) {
            return response($exception->getMessage(), 404);
        }

        return view('v3.clinical.agenda._calendar_week', $payload);
    }

    private function prepareProcedureSearch(array &$payload, Request $request)
    {
        $payload['hasProcedures'] = $payload['procedures']->isNotEmpty();
        if ($request->ajax()) {
            $selectedId = $payload['appointment'] ? $payload['appointment']->id_procedimiento : null;
            $payload['procedures'] = $payload['procedures']->filter(function ($procedure) use ($selectedId) {
                return $selectedId && $procedure->id == $selectedId;
            });
        }
    }

    public function patients(Request $request, $doctor)
    {
        $this->assertCanManage($doctor);

        return $this->patientResponse($request, $doctor);
    }

    public function procedures(Request $request, $doctor)
    {
        $this->assertCanManage($doctor);
        try {
            $this->service->doctor($this->companyId($request), $doctor);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        }

        return response()->json(['results' => $this->service->searchProcedures($request->input('q'))]);
    }

    public function availability(Request $request, $doctor)
    {
        $this->assertCanManage($doctor);
        $this->validate($request, [
            'fecha' => 'required|date_format:Y-m-d',
            'tipo' => 'required|integer|between:0,2',
            'excluir' => 'nullable|integer',
        ]);
        try {
            $slots = $this->service->availableIntervals(
                $this->companyId($request),
                $doctor,
                $request->input('fecha'),
                (int) $request->input('tipo'),
                $request->input('excluir')
            );
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json(['items' => $slots]);
    }

    private function patientResponse(Request $request, $doctor)
    {
        try {
            $this->service->doctor($this->companyId($request), $doctor);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        }
        $items = [];
        foreach ($this->service->searchPatients($this->companyId($request), $request->input('q')) as $patient) {
            $items[] = [
                'id' => $patient->id,
                'name' => $patient->display_name,
                'birth' => $patient->fecha_nacimiento,
                'appointment_type' => ! empty($patient->has_previous_agenda) ? 1 : 0,
                'text' => $patient->display_name.' · '.$patient->id,
            ];
        }

        return response()->json(['items' => $items, 'results' => $items]);
    }

    public function myCalendar(Request $request)
    {
        if (! DoctorUserType::matches(Auth::user())) {
            return redirect()->route('v3.clinical-agenda.index');
        }

        return $this->calendar($request, Auth::id());
    }

    public function mySchedule(Request $request)
    {
        if (! DoctorUserType::matches(Auth::user())) {
            return redirect()->route('v3.clinical-agenda.index');
        }

        return $this->schedule($request, Auth::id());
    }

    public function create(Request $request, $doctor)
    {
        $this->assertCanManage($doctor);
        if ($request->ajax() && filter_var($request->input('buscar_paciente'), FILTER_VALIDATE_BOOLEAN)) {
            return $this->patientResponse($request, $doctor);
        }
        try {
            $payload = $this->service->editor($this->companyId($request), $doctor, null, ['patient' => $request->input('paciente')]);
        } catch (RuntimeException $exception) {
            abort(404, $exception->getMessage());
        }
        $this->prepareRoomManagement($payload);

        if (! $request->ajax()) {
            $payload['patients'] = $this->service->searchPatients($this->companyId($request), $request->input('paciente'));
        }
        $payload['defaultPatientId'] = trim((string) $request->input('id_paciente'));
        $payload['defaultAppointmentType'] = $this->service->appointmentTypeForPatient($this->companyId($request), $payload['defaultPatientId']);
        if (! $payload['appointment'] && $payload['defaultPatientId'] !== '') {
            $defaultPatientId = $payload['defaultPatientId'];
            $patientOption = $payload['patients']->first(function ($patient) use ($defaultPatientId) {
                return (string) $patient->id === $defaultPatientId;
            });
            if ($patientOption) {
                $selectedPatient = DB::table('paciente')->where('id', $defaultPatientId)->first();
                if ($selectedPatient) {
                    $selectedPatient->display_name = $patientOption->display_name;
                }
                $payload['selectedPatient'] = $selectedPatient;
            }
        }
        $this->prepareProcedureSearch($payload, $request);
        $payload['defaultStart'] = $request->input('inicio', date('Y-m-d\TH:i'));
        $payload['defaultEnd'] = $request->input('fin', date('Y-m-d\TH:i', strtotime($payload['defaultStart'].' +30 minutes')));
        // Una agenda nueva siempre inicia como consulta. El usuario puede
        // cambiar el tipo después desde el formulario si lo necesita.
        $payload['defaultType'] = 0;

        if ($request->ajax()) {
            return view('v3.clinical.agenda._activity_modal', $payload);
        }

        return $this->page('v3.clinical.agenda.editor', $payload, 'Nueva agenda', 'crear');
    }

    public function store(Request $request, $doctor)
    {
        $this->assertCanManage($doctor);
        $this->validateAppointment($request);
        try {
            $id = $this->service->saveAppointment($this->companyId($request), $doctor, $request->all(), Auth::id(), $request->ip());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['ok' => true, 'id' => $id, 'message' => 'La agenda se registró correctamente.']);
            }

            return $this->calendarRedirect($doctor, $request->input('inicio'), 'La agenda se registró correctamente.');
        } catch (RuntimeException $exception) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $exception->getMessage(), 'errors' => ['agenda' => [$exception->getMessage()]]], 422);
            }

            return back()->withInput()->withErrors(['agenda' => $exception->getMessage()]);
        }
    }

    public function edit(Request $request, $doctor, $appointment)
    {
        $this->assertCanManage($doctor);
        try {
            $payload = $this->service->editor($this->companyId($request), $doctor, $appointment, ['patient' => $request->input('paciente')]);
        } catch (RuntimeException $exception) {
            abort(404, $exception->getMessage());
        }
        $this->prepareRoomManagement($payload);
        if (! $request->ajax() && $request->filled('paciente')) {
            $payload['patients'] = $this->service->searchPatients($this->companyId($request), $request->input('paciente'));
        }
        $this->prepareProcedureSearch($payload, $request);
        $payload['defaultStart'] = null;
        $payload['defaultEnd'] = null;
        $payload['defaultType'] = (int) $payload['appointment']->proc_consul;
        $patientIdDocument = DB::table('paciente_biopsia')
            ->where('id_paciente', $payload['appointment']->id_paciente)
            ->where('id_empresa', $this->companyId($request))
            ->where('estado', 3)
            ->orderBy('id', 'desc')->first();
        $payload['patientIdDocumentAvailable'] = $patientIdDocument
            && trim((string) $patientIdDocument->nombre) !== ''
            && is_file(storage_path('app/avatars/'.basename($patientIdDocument->nombre)));
        $payload['patientIdDocumentUrl'] = route('v3.agenda-documents.patient-copy', ['doctor' => $doctor, 'appointment' => $appointment]);
        if ($request->ajax()) {
            return view('v3.clinical.agenda._activity_modal', $payload);
        }

        return $this->page('v3.clinical.agenda.editor', $payload, 'Editar agenda', 'editar');
    }

    private function prepareRoomManagement(array &$payload)
    {
        $payload['canManageRooms'] = app(SidebarMenuService::class)
            ->canWithLegacy('system.locations', [1, 4]);
        $payload['roomCreateUrl'] = $payload['canManageRooms']
            ? route('sala-management.create')
            : null;
    }

    public function update(Request $request, $doctor, $appointment)
    {
        $this->assertCanManage($doctor);
        $this->validateAppointment($request);
        $confirmedReschedule = (int) $request->input('estado_cita') === 2
            && filter_var($request->input('reagendar_confirmado'), FILTER_VALIDATE_BOOLEAN);
        if ($confirmedReschedule) {
            $request->merge(['estado_cita' => 1]);
        }
        try {
            $this->enforceConfirmationLock($request, $doctor, $appointment, $confirmedReschedule);
            $this->service->saveAppointment($this->companyId($request), $doctor, $request->all(), Auth::id(), $request->ip(), $appointment);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['ok' => true, 'message' => 'Los cambios se guardaron correctamente.']);
            }

            return $this->calendarRedirect($doctor, $request->input('inicio'), 'Los cambios se guardaron correctamente.');
        } catch (RuntimeException $exception) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $exception->getMessage(), 'errors' => ['agenda' => [$exception->getMessage()]]], 422);
            }

            return back()->withInput()->withErrors(['agenda' => $exception->getMessage()]);
        }
    }

    public function destroy(Request $request, $doctor, $appointment)
    {
        $this->assertCanManage($doctor);
        $cancellationReason = trim((string) $request->input('observacion_cancelacion'));
        if ($cancellationReason === '' || mb_strlen($cancellationReason, 'UTF-8') > 1000) {
            $message = $cancellationReason === ''
                ? 'Ingrese la observación que explica la cancelación de la agenda.'
                : 'La observación de cancelación supera la longitud permitida.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $message, 'errors' => ['observacion_cancelacion' => [$message]]], 422);
            }

            return back()->withInput()->withErrors(['observacion_cancelacion' => $message]);
        }
        try {
            $this->service->cancelAppointment($this->companyId($request), $doctor, $appointment, Auth::id(), $request->ip(), $cancellationReason);
        } catch (RuntimeException $exception) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $exception->getMessage()], 422);
            }

            return back()->withErrors(['agenda' => $exception->getMessage()]);
        }
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'La agenda se canceló correctamente.']);
        }

        return redirect()->route('v3.doctor-agenda.calendar', ['doctor' => $doctor])->with('agenda_success', 'La agenda se canceló y quedó registrada en la trazabilidad.');
    }

    /**
     * Archivos de agenda compartidos con el flujo legado (V2).
     *
     * V2 utiliza agenda_archivo con tipo_documento HCAGENDA y el disco
     * hc_agenda; mantener esa convención permite consultar los comprobantes
     * desde cualquiera de las dos versiones.
     */
    public function appointmentFiles(Request $request, $doctor, $appointment)
    {
        $agenda = $this->agendaForFiles($request, $doctor, $appointment, false);

        return response()->json(['items' => $this->appointmentFileItems($agenda, $doctor)]);
    }

    public function storeAppointmentFile(Request $request, $doctor, $appointment)
    {
        $this->validate($request, [
            'archivo' => 'required|file|mimes:pdf,jpeg,jpg,png|max:10240',
        ], [
            'archivo.required' => 'Seleccione el comprobante que desea cargar.',
            'archivo.mimes' => 'El comprobante debe estar en formato PDF, JPG, JPEG o PNG.',
            'archivo.max' => 'El comprobante no puede superar los 10 MB.',
        ]);
        $agenda = $this->agendaForFiles($request, $doctor, $appointment, false);
        $upload = $request->file('archivo');
        $extension = strtolower($upload->getClientOriginalExtension());
        $name = 'agenda_'.$agenda->id.'_'.date('YmdHis').'_'.substr(uniqid(), -8).'.'.$extension;

        if (! Storage::disk('hc_agenda')->put($name, file_get_contents($upload->getRealPath()))) {
            return response()->json(['message' => 'No fue posible almacenar el comprobante. Inténtelo nuevamente.'], 500);
        }
        $originalName = preg_replace('/\s+/', ' ', trim((string) $upload->getClientOriginalName()));
        $description = 'Comprobante de pago'.($originalName !== '' ? ': '.substr($originalName, 0, 37) : '');
        try {
            $archive = Agenda_archivo::create([
                'id_agenda' => $agenda->id,
                'tipo_documento' => 'HCAGENDA',
                'descripcion' => $description,
                'ruta' => '/hc_agenda/',
                'archivo' => $name,
                'id_usuariocrea' => Auth::id(),
                'id_usuariomod' => Auth::id(),
                'ip_creacion' => $request->ip(),
                'ip_modificacion' => $request->ip(),
            ]);
        } catch (\Exception $exception) {
            Storage::disk('hc_agenda')->delete($name);
            throw $exception;
        }

        return response()->json([
            'ok' => true,
            'message' => 'Comprobante cargado correctamente.',
            'item' => $this->appointmentFileItem($archive, $doctor),
        ]);
    }

    public function showAppointmentFile(Request $request, $doctor, $appointment, $archive)
    {
        $agenda = $this->agendaForFiles($request, $doctor, $appointment, false);
        $file = $this->appointmentFile($agenda, $archive);
        $name = basename((string) $file->archivo);
        if ($name === '' || ! Storage::disk('hc_agenda')->exists($name)) {
            abort(404, 'El archivo ya no está disponible.');
        }
        $path = storage_path('app/hc_agenda/'.$name);

        return response()->file($path, [
            'Content-Type' => $this->mimeType($path),
            'Content-Disposition' => 'inline; filename="'.$this->safeDownloadName($name).'"',
            'Content-Security-Policy' => "default-src 'none'; img-src 'self' data:; media-src 'self'; style-src 'unsafe-inline'",
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroyAppointmentFile(Request $request, $doctor, $appointment, $archive)
    {
        $this->assertCanManage($doctor);
        $agenda = $this->agendaForFiles($request, $doctor, $appointment);
        $file = $this->appointmentFile($agenda, $archive);
        $name = basename((string) $file->archivo);
        if ($name !== '') {
            Storage::disk('hc_agenda')->delete($name);
        }
        $file->delete();

        return response()->json(['ok' => true, 'message' => 'Archivo eliminado correctamente.']);
    }

    public function action(Request $request, $doctor, $appointment, $action)
    {
        $this->assertCanManage($doctor);
        try {
            $agenda = $this->service->agendaForAction($this->companyId($request), $doctor, $appointment);
        } catch (RuntimeException $exception) {
            abort(404, $exception->getMessage());
        }
        if (! $agenda->id_paciente) {
            abort(422, 'Esta agenda no tiene un paciente asociado.');
        }
        $patient = DB::table('paciente')->where('id', $agenda->id_paciente)->first();
        if (! $patient) {
            abort(404, 'El paciente de la agenda ya no existe.');
        }
        if ($action === 'recibo-cobro') {
            if (! in_array((int) Auth::user()->id_tipo_usuario, [1, 4, 5, 20, 22], true)) {
                abort(403);
            }
            $receiptInsuranceAllowed = Seguro::where('id', $agenda->id_seguro)
                ->where('id_empresa', $this->companyId($request))->where('inactivo', 1)
                ->where('tipo', '<>', 0)->exists();
            if (! $receiptInsuranceAllowed) {
                abort(422, 'El recibo de cobro requiere que la agenda tenga un seguro activo con tipo diferente de 0.');
            }
            if ((int) $agenda->estado_cita !== 4 && ! DB::table('historiaclinica')->where('id_agenda', $agenda->id)->exists()) {
                abort(422, 'El recibo de cobro está disponible después de admitir al paciente.');
            }
            $receipts = Ct_Orden_Venta::with(['detalles', 'venta'])
                ->where('id_empresa', $this->companyId($request))->where('id_agenda', $agenda->id)
                ->where('estado', '<>', 0)->orderBy('id')->get();
            $receiptPayload = [
                'appointment' => $agenda,
                'receipts' => $receipts,
                'patient' => $patient,
                'doctorId' => $doctor,
            ];
            if ($request->ajax()) {
                return view('v3.clinical.agenda._receipt_modal', $receiptPayload);
            }
            if ($receipts->isEmpty()) {
                return redirect()->route('v3.agenda-receipts.create', [
                    'id_agenda' => $agenda->id,
                    '_v3' => 1,
                    '_v3_menu' => (int) config('data.menu_id', 0),
                ]);
            }

            return $this->page('v3.clinical.agenda.receipts', $receiptPayload, 'Recibo de cobro', 'recibo-cobro');
        }
        $routes = [
            'etiqueta' => ['admision.etiqueta2', ['id' => $agenda->id, 'seguro' => $agenda->id_seguro, 'alergia' => $patient->alergias]],
            'documentos' => ['v3.agenda-documents.index', ['doctor' => $doctor, 'appointment' => $agenda->id]],
            'preparaciones' => ['v3.preparations.catalog', ['agenda' => $agenda->id]],
            'consentimientos' => ['v3.agenda-consents.index', ['doctor' => $doctor, 'appointment' => $agenda->id]],
            'resumen' => ['controldoc.imprimirpdf_resumen', ['id' => $agenda->id]],
            'certificado' => ['v3.medical-certificates.create', ['doctor' => $doctor, 'appointment' => $agenda->id]],
            'cedula' => ['paciente.ver_copia_cedula', ['cedula' => $agenda->id_paciente]],
            'examenes-externos' => ['laboratorio.externo', ['id_paciente' => $agenda->id_paciente]],
            'biopsias' => ['v3.patient-biopsy-files.index', ['patient' => $agenda->id_paciente]],
            'orden-procedimiento' => ['paciente.historial_ordenes', ['id_paciente' => $agenda->id_paciente]],
            'ordenes-biopsias' => ['v3.patient-biopsy-orders.index', ['patient' => $agenda->id_paciente]],
            'laboratorio' => ['v3.patient-laboratory-orders.index', ['patient' => $agenda->id_paciente]],
            'imagenes' => ['paciente.historial_imagenes', ['id_paciente' => $agenda->id_paciente]],
            'estudios' => ['paciente.historial_estudios', ['id_paciente' => $agenda->id_paciente]],
            'orden-laboratorio' => ['v3.appointment-laboratory-orders.create', ['doctor' => $doctor, 'appointment' => $agenda->id]],
            'recetas' => ['paciente.historial_recetas', ['id_paciente' => $agenda->id_paciente]],
            'cardiologia' => ['cardiologia.agenda', ['paciente' => $agenda->id, 'url' => $doctor]],
            'historial-agendas' => ['v3.patient-agenda-archives.index', ['patient' => $agenda->id_paciente]],
        ];
        if (! isset($routes[$action])) {
            abort(404, 'La acción clínica solicitada no existe.');
        }
        $routeParameters = $routes[$action][1];
        if ($action === 'etiqueta' && (string) $request->input('preview') === '1') {
            $routeParameters['preview'] = 1;
        }
        // Las acciones clínicas todavía combinan pantallas V3 y rutas históricas.
        // Se conserva explícitamente el contexto V3 para que todas reciban el
        // menú y los permisos del usuario al completar la redirección.
        $routeParameters['_v3'] = 1;
        $routeParameters['_v3_menu'] = (int) config('data.menu_id', 0);

        return redirect()->to(route($routes[$action][0], $routeParameters));
    }

    public function schedule(Request $request, $doctor)
    {
        try {
            $payload = $this->service->schedule($this->companyId($request), $doctor, $request->input('fecha'));
        } catch (RuntimeException $exception) {
            abort(404, $exception->getMessage());
        }

        return $this->page('v3.clinical.agenda.schedule', $payload, 'Horario laboral', 'horario');
    }

    public function scheduleWeek(Request $request, $doctor)
    {
        try {
            $payload = $this->service->schedule($this->companyId($request), $doctor, $request->input('fecha'));
        } catch (RuntimeException $exception) {
            return response($exception->getMessage(), 404);
        }

        return view('v3.clinical.agenda._schedule_week', $payload);
    }

    public function storeRecurring(Request $request, $doctor)
    {
        $this->assertCanManage($doctor);
        $this->validate($request, [
            'dia' => 'required|integer|between:1,7',
            'hora_ini' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i',
            'tipo' => 'required|integer|in:0,1,2',
            'intervalo_consulta' => 'required|integer|in:15,20,30,45,60,90,120',
            'intervalo_procedimiento' => 'required|integer|in:15,20,30,45,60,90,120,180,240',
        ]);
        try {
            $this->service->addRecurring($this->companyId($request), $doctor, $request->all(), Auth::id(), $request->ip());
        } catch (RuntimeException $exception) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $exception->getMessage()], 422);
            }

            return back()->withInput()->withErrors(['schedule' => $exception->getMessage()]);
        }
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Horario recurrente agregado.']);
        }

        return back()->with('agenda_success', 'Horario recurrente agregado.');
    }

    public function destroyRecurring(Request $request, $doctor, $slot)
    {
        $this->assertCanManage($doctor);
        try {
            $this->service->deleteRecurring($this->companyId($request), $doctor, $slot, Auth::id(), $request->ip());
        } catch (RuntimeException $exception) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $exception->getMessage()], 422);
            }

            return back()->withErrors(['schedule' => $exception->getMessage()]);
        }
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Horario retirado correctamente.']);
        }

        return back()->with('agenda_success', 'Horario retirado correctamente.');
    }

    public function updateRecurring(Request $request, $doctor, $slot)
    {
        $this->assertCanManage($doctor);
        $this->validate($request, [
            'dia' => 'required|integer|between:1,7',
            'hora_ini' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i',
            'tipo' => 'required|integer|in:0,1,2',
            'intervalo_consulta' => 'required|integer|in:15,20,30,45,60,90,120',
            'intervalo_procedimiento' => 'required|integer|in:15,20,30,45,60,90,120,180,240',
        ]);
        try {
            $this->service->updateRecurring($this->companyId($request), $doctor, $slot, $request->all(), Auth::id(), $request->ip());
        } catch (RuntimeException $exception) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $exception->getMessage()], 422);
            }

            return back()->withInput()->withErrors(['schedule' => $exception->getMessage()]);
        }
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Horario recurrente actualizado.']);
        }

        return back()->with('agenda_success', 'Horario recurrente actualizado.');
    }

    public function storeException(Request $request, $doctor)
    {
        $this->assertCanManage($doctor);
        $this->validate($request, ['inicio' => 'required|date', 'fin' => 'required|date', 'tipo' => 'required|integer|between:0,3']);
        try {
            $this->service->addException($this->companyId($request), $doctor, $request->all(), Auth::id(), $request->ip());
        } catch (RuntimeException $exception) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $exception->getMessage()], 422);
            }

            return back()->withInput()->withErrors(['schedule' => $exception->getMessage()]);
        }
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Disponibilidad extraordinaria agregada.']);
        }

        return back()->with('agenda_success', 'Disponibilidad extraordinaria agregada.');
    }

    public function destroyException(Request $request, $doctor, $exception)
    {
        $this->assertCanManage($doctor);
        try {
            $this->service->deleteException($this->companyId($request), $doctor, $exception);
        } catch (RuntimeException $error) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $error->getMessage()], 422);
            }

            return back()->withErrors(['schedule' => $error->getMessage()]);
        }
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Disponibilidad extraordinaria retirada.']);
        }

        return back()->with('agenda_success', 'Disponibilidad extraordinaria retirada.');
    }

    private function validateAppointment(Request $request)
    {
        // En la agenda V3 de Clínica Borja todas las atenciones se registran
        // sin cortesía; el valor no depende de datos enviados por el navegador.
        $request->merge(['cortesia' => 'NO']);

        $this->validate($request, [
            'tipo' => 'required|integer|between:0,2',
            'inicio' => 'required|date',
            'fin' => 'required|date|after:inicio',
            'id_paciente' => 'required_unless:tipo,2|nullable|string|max:20',
            'id_sala' => 'required_unless:tipo,2|nullable|integer',
            'espid' => 'nullable|integer',
            'id_seguro' => 'required_unless:tipo,2|nullable|integer',
            'id_procedimiento' => 'required_if:tipo,1|nullable|integer',
            'procedimientos' => 'nullable|array',
            'id_doctor2' => 'nullable|string|max:20',
            'id_doctor3' => 'nullable|string|max:20',
            'tipo_cita' => 'nullable|integer',
            'estado_cita' => 'nullable|integer|in:-1,0,1,2,3,4',
            'est_amb_hos' => 'required_unless:tipo,2|nullable|integer|in:0,1',
            'cortesia' => 'required_unless:tipo,2|nullable|string|in:NO,SI',
            'observaciones' => 'nullable|string|max:1000',
            'observacion_paciente' => 'nullable|string|max:2000',
            'procedencia' => 'nullable|string|max:255',
            'teleconsulta' => 'nullable|string|max:45',
            'cie10' => 'nullable|string|max:255',
            'ocupacion' => 'nullable|string|max:255',
            'referido' => 'nullable|string|max:255',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'required_unless' => 'Debe seleccionar :attribute para una atención clínica.',
            'required_if' => 'Debe seleccionar :attribute para agendar un procedimiento.',
            'after' => 'La fecha y hora de fin debe ser posterior al inicio.',
            'date' => 'El campo :attribute debe contener una fecha y hora válida.',
            'integer' => 'La selección de :attribute no es válida.',
            'max' => 'El campo :attribute supera la longitud permitida.',
        ], [
            'tipo' => 'tipo de agenda',
            'inicio' => 'inicio',
            'fin' => 'fin',
            'id_paciente' => 'un paciente',
            'id_sala' => 'una sala o consultorio',
            'espid' => 'especialidad',
            'id_seguro' => 'un seguro o convenio',
            'id_procedimiento' => 'un procedimiento',
            'est_amb_hos' => 'tipo de ingreso',
            'cortesia' => 'cortesía',
            'observaciones' => 'observaciones',
            'observacion_paciente' => 'observación médica del paciente',
        ]);
    }

    private function enforceConfirmationLock(Request $request, $doctor, $appointment, $confirmedReschedule = false)
    {
        try {
            $agenda = $this->service->agendaForAction($this->companyId($request), $doctor, $appointment);
        } catch (RuntimeException $exception) {
            throw new RuntimeException($exception->getMessage());
        }
        if ((int) $agenda->estado_cita !== 1) {
            return;
        }

        $isRescheduling = $confirmedReschedule;
        $fixedChanged = (int) $request->input('tipo') !== (int) $agenda->proc_consul
            || ! $this->sameNullableValue($request->input('id_paciente'), $agenda->id_paciente);
        $rescheduleChanged = strtotime((string) $request->input('inicio')) !== strtotime((string) $agenda->fechaini)
            || strtotime((string) $request->input('fin')) !== strtotime((string) $agenda->fechafin)
            || ! $this->sameNullableValue($request->input('id_sala'), $agenda->id_sala)
            || ! $this->sameNullableValue($request->input('id_doctor2'), $agenda->id_doctor2)
            || ! $this->sameNullableValue($request->input('id_doctor3'), $agenda->id_doctor3);
        if ($fixedChanged || (! $isRescheduling && $rescheduleChanged)) {
            throw new RuntimeException('Una agenda confirmada solo permite modificar ubicación, médicos y fecha al cambiar el estado a Reagendar.');
        }
    }

    private function sameNullableValue($first, $second)
    {
        $first = $first === null ? '' : trim((string) $first);
        $second = $second === null ? '' : trim((string) $second);

        return $first === $second;
    }

    private function assertCanManage($doctor)
    {
        if ((string) Auth::id() === (string) $doctor) {
            return;
        }
        if (! in_array((int) Auth::user()->id_tipo_usuario, [1, 4, 5], true)) {
            abort(403, 'No puede modificar la agenda de este médico.');
        }
    }

    private function companyId(Request $request)
    {
        $id = trim((string) $request->session()->get('id_empresa'));
        if ($id === '') {
            abort(403, 'Seleccione una empresa activa.');
        }

        return $id;
    }

    private function agendaForFiles(Request $request, $doctor, $appointment, $requireManagementPermission = true)
    {
        if ($requireManagementPermission) {
            $this->assertCanManage($doctor);
        }
        try {
            return $this->service->agendaForAction($this->companyId($request), $doctor, $appointment);
        } catch (RuntimeException $exception) {
            abort(404, $exception->getMessage());
        }
    }

    private function appointmentFile($agenda, $archive)
    {
        $file = Agenda_archivo::where('id', (int) $archive)
            ->where('id_agenda', $agenda->id)
            ->where('tipo_documento', 'HCAGENDA')
            ->whereNotNull('archivo')->where('archivo', '<>', '')
            ->first();
        if (! $file) {
            abort(404, 'El archivo no pertenece a esta agenda.');
        }

        return $file;
    }

    private function appointmentFileItems($agenda, $doctor)
    {
        return Agenda_archivo::where('id_agenda', $agenda->id)
            ->where('tipo_documento', 'HCAGENDA')
            ->whereNotNull('archivo')->where('archivo', '<>', '')
            ->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get()
            ->map(function ($file) use ($doctor) {
                return $this->appointmentFileItem($file, $doctor);
            })->values();
    }

    private function appointmentFileItem($file, $doctor)
    {
        $uploader = DB::table('users')->where('id', $file->id_usuariocrea)->first();
        $uploaderName = $uploader ? trim($uploader->apellido1.' '.$uploader->apellido2.' '.$uploader->nombre1.' '.$uploader->nombre2) : 'Sistema';
        $name = basename((string) $file->archivo);

        return [
            'id' => (int) $file->id,
            'name' => $name,
            'description' => (string) $file->descripcion,
            'date' => $file->created_at ? date('d/m/Y H:i', strtotime($file->created_at)) : '',
            'uploader' => $uploaderName ?: 'Sistema',
            'view_url' => route('v3.doctor-agenda.files.show', ['doctor' => $doctor, 'appointment' => $file->id_agenda, 'archive' => $file->id]),
            'delete_url' => route('v3.doctor-agenda.files.destroy', ['doctor' => $doctor, 'appointment' => $file->id_agenda, 'archive' => $file->id]),
        ];
    }

    private function mimeType($path)
    {
        $mime = function_exists('mime_content_type') ? @mime_content_type($path) : null;

        return $mime ?: 'application/octet-stream';
    }

    private function safeDownloadName($name)
    {
        $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) $name);

        return $safe !== '' ? $safe : 'comprobante';
    }

    private function calendarRedirect($doctor, $start, $message)
    {
        $timestamp = strtotime((string) $start);
        $date = $timestamp ? date('Y-m-d', $timestamp) : date('Y-m-d');

        return redirect()->route('v3.doctor-agenda.calendar', [
            'doctor' => $doctor,
            'fecha' => $date,
            'vista' => 'dia',
        ])->with('agenda_success', $message);
    }

    private function page($view, array $payload, $title, $submodule)
    {
        $data = [
            'controlador' => 'agenda-clinica',
            'carpeta' => 'clinical',
            'submodulo' => $submodule,
            'submodulo_' => $submodule,
            'titulo' => $title,
            'title' => $title.' | Aitrol Hospitalario',
            'vista' => $view,
            'obseraudi' => 'Ingreso a '.$title,
            'contenido' => view($view, $payload),
        ];
        config(['data' => array_merge(config('data'), $data)]);

        return view('v3.layouts.view_child');
    }
}
