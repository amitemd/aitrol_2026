<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use RuntimeException;

class V3DoctorAgendaService
{
    private $availability;

    public function __construct(V3AvailabilityService $availability)
    {
        $this->availability = $availability;
    }

    public function doctor($companyId, $doctorId)
    {
        $doctorQuery = DB::table('users as u')->join('usuario_empresa as ue', 'ue.id_usuario', '=', 'u.id');
        $doctor = DoctorUserType::constrain($doctorQuery, 'u', 'doctor_type_selected')
            ->where('ue.id_empresa', $companyId)->where('u.id', $doctorId)
            ->where('u.estado', 1)->select('u.*')->first();
        if (!$doctor) throw new RuntimeException('El médico no pertenece a la empresa activa.');
        $doctor->display_name = $this->personName($doctor);
        return $doctor;
    }

    public function calendar($companyId, $doctorId, $date, $showCancelled = false, $viewMode = 'dia')
    {
        $doctor = $this->doctor($companyId, $doctorId);
        $anchor = strtotime($date ?: date('Y-m-d'));
        if ($anchor === false) $anchor = time();
        $viewMode = $viewMode === 'semana' ? 'semana' : 'dia';
        $periodStart = $viewMode === 'dia' ? strtotime(date('Y-m-d', $anchor)) : strtotime('monday this week', $anchor);
        $periodEnd = $viewMode === 'dia' ? $periodStart : strtotime('+6 days', $periodStart);
        $from = date('Y-m-d 00:00:00', $periodStart);
        $to = date('Y-m-d 23:59:59', $periodEnd);

        $events = DB::table('agenda as a')->leftJoin('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->leftJoin('sala as s', 's.id', '=', 'a.id_sala')->leftJoin('procedimiento as pr', 'pr.id', '=', 'a.id_procedimiento')
            ->leftJoin('seguros as sg', 'sg.id', '=', 'a.id_seguro')
            ->where(function ($query) use ($doctorId) {
                $query->where('a.id_doctor1', $doctorId)->orWhere('a.id_doctor2', $doctorId)->orWhere('a.id_doctor3', $doctorId);
            })
            ->where('a.estado', $showCancelled ? 0 : 1)->whereBetween('a.fechaini', array($from, $to))
            ->where('a.id_empresa', $companyId)
            ->select(
                'a.*',
                'p.nombre1 as p_nombre1',
                'p.nombre2 as p_nombre2',
                'p.apellido1 as p_apellido1',
                'p.apellido2 as p_apellido2',
                's.nombre_sala',
                'pr.nombre as procedimiento_nombre',
                'sg.nombre as seguro_nombre'
            )->orderBy('a.fechaini')->get();
        foreach ($events as $event) {
            $event->patient_name = $event->proc_consul == 2 ? 'Reunión' : $this->patientName($event);
            $event->type_label = $this->typeLabel($event->proc_consul);
            $event->state_label = $this->stateLabel($event->estado_cita);
            $event->day_key = date('Y-m-d', strtotime($event->fechaini));
        }
        $days = array();
        $recurring = DB::table('horario_doctor')->where('id_doctor', $doctorId)->where('estado', 1)
            ->orderBy('hora_ini')->get()->groupBy('ndia');
        $exceptions = DB::table('excepcion_horario')->where('id_doctor1', $doctorId)
            ->where('inicio', '<=', $to)->where('fin', '>=', $from)->orderBy('inicio')->get();
        $dayCount = $viewMode === 'dia' ? 1 : 7;
        for ($i = 0; $i < $dayCount; $i++) {
            $timestamp = strtotime('+' . $i . ' days', $periodStart);
            $key = date('Y-m-d', $timestamp);
            $slots = collect();
            $dayEvents = $events->where('day_key', $key)->values();
            foreach ($recurring->get((int) date('N', $timestamp), collect()) as $slot) {
                $agendaType = $this->agendaTypeForSchedule($slot->tipo);
                $minutes = $agendaType === 1
                    ? (isset($slot->intervalo_procedimiento) ? (int) $slot->intervalo_procedimiento : 30)
                    : (isset($slot->intervalo_consulta) ? (int) $slot->intervalo_consulta : 30);
                $this->appendCalendarSlots(
                    $slots,
                    $dayEvents,
                    strtotime($key . ' ' . $slot->hora_ini),
                    strtotime($key . ' ' . $slot->hora_fin),
                    max(15, $minutes),
                    $agendaType
                );
            }
            foreach ($exceptions as $exception) {
                if (date('Y-m-d', strtotime($exception->inicio)) !== $key) continue;
                $this->appendCalendarSlots(
                    $slots,
                    $dayEvents,
                    strtotime($exception->inicio),
                    strtotime($exception->fin),
                    30,
                    $this->agendaTypeForSchedule($exception->tipo)
                );
            }
            $slots = $slots->unique(function ($slot) {
                return $slot->start . '|' . $slot->end . '|' . $slot->type;
            })
                ->sortBy('start')->values();
            $days[] = (object) array(
                'key' => $key,
                'short' => $this->dayName((int) date('N', $timestamp)),
                'number' => date('d', $timestamp),
                'month' => $this->monthName((int) date('n', $timestamp)),
                'today' => $key === date('Y-m-d'),
                'events' => $dayEvents,
                'slots' => $slots
            );
        }
        $step = $viewMode === 'dia' ? '1 day' : '7 days';
        return array(
            'doctor' => $doctor,
            'days' => $days,
            'events' => $events,
            'weekStart' => date('Y-m-d', $periodStart),
            'weekEnd' => date('Y-m-d', $periodEnd),
            'anchorDate' => date('Y-m-d', $anchor),
            'previous' => date('Y-m-d', strtotime('-' . $step, $periodStart)),
            'next' => date('Y-m-d', strtotime('+' . $step, $periodStart)),
            'showCancelled' => $showCancelled,
            'viewMode' => $viewMode
        );
    }

    private function appendCalendarSlots($slots, $events, $windowStart, $windowEnd, $minutes, $agendaType)
    {
        if ($windowStart === false || $windowEnd === false || $windowEnd <= $windowStart) return;
        $duration = max(15, (int) $minutes) * 60;
        for ($start = $windowStart; $start + $duration <= $windowEnd; $start += $duration) {
            $end = $start + $duration;
            if ($start <= time()) continue;
            $isBusy = false;
            foreach ($events as $event) {
                if (strtotime($event->fechaini) < $end && strtotime($event->fechafin) > $start) {
                    $isBusy = true;
                    break;
                }
            }
            if ($isBusy) continue;
            $slots->push((object) array(
                'start' => date('Y-m-d\TH:i', $start),
                'end' => date('Y-m-d\TH:i', $end),
                'type' => (int) $agendaType,
                'label' => date('H:i', $start) . ' – ' . date('H:i', $end),
            ));
        }
    }

    public function editor($companyId, $doctorId, $appointmentId = null, array $filters = array())
    {
        $doctor = $this->doctor($companyId, $doctorId);
        $appointment = null;
        if ($appointmentId) $appointment = $this->appointment($companyId, $doctorId, $appointmentId);
        $patientSearch = trim(isset($filters['patient']) ? $filters['patient'] : '');
        $patients = collect();
        if ($appointment && $appointment->id_paciente && !$patients->where('id', $appointment->id_paciente)->count()) {
            $selected = DB::table('paciente')->where('id', $appointment->id_paciente)->first();
            if ($selected) $patients->prepend($selected);
        }
        foreach ($patients as $patient) if (!isset($patient->display_name)) $patient->display_name = $this->personName($patient);
        $selectedPatient = $appointment && $appointment->id_paciente ? DB::table('paciente')->where('id', $appointment->id_paciente)->first() : null;
        if ($selectedPatient) $selectedPatient->display_name = $this->personName($selectedPatient);
        $rooms = collect();
        foreach ($this->availability->hospitals($companyId) as $hospital)
            foreach ($hospital->rooms as $room) {
                // El recurso ya contiene su piso y tipo. No repetir el nombre
                // de la empresa/hospital en el selector de la agenda.
                $room->display_name = trim((string) $room->nombre_sala);
                $rooms->push($room);
            }
        $doctorSpecialty = $this->assignedSpecialtiesForDoctor($doctorId)->first();
        $insurers = \Sis_medico\Seguro::query();
        if (Schema::hasColumn('seguros', 'estado')) $insurers->where('estado', 1);
        elseif (Schema::hasColumn('seguros', 'inactivo')) $insurers->where('inactivo', 1);
        $assistantsQuery = DB::table('users as u')->join('usuario_empresa as ue', 'ue.id_usuario', '=', 'u.id');
        $assistants = DoctorUserType::constrain($assistantsQuery, 'u', 'doctor_type_assistant')
            ->where('ue.id_empresa', $companyId)->where('u.estado', 1)
            ->where('u.id', '<>', $doctorId)->select('u.id', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2')->orderBy('u.apellido1')->get();
        foreach ($assistants as $assistant) $assistant->display_name = $this->personName($assistant);
        $additionalProcedures = $appointment ? DB::table('agenda_procedimiento')->where('id_agenda', $appointment->id)->pluck('id_procedimiento')->all() : array();
        $logs = $appointment ? DB::table('log_agenda as l')->leftJoin('users as u', 'u.id', '=', 'l.id_usuariocrea')
            ->where('l.id_agenda', $appointment->id)->select('l.*', 'u.nombre1 as usuario_nombre', 'u.apellido1 as usuario_apellido')
            ->orderBy('l.id', 'desc')->limit(20)->get() : collect();
        $patientObservation = $selectedPatient && Schema::hasTable('paciente_observaciones')
            ? DB::table('paciente_observaciones')->where('id_paciente', $selectedPatient->id)->value('observacion') : null;
        $receiptInsuranceAllowed = $appointment && $appointment->id_seguro
            ? \Sis_medico\Seguro::where('id', $appointment->id_seguro)
                ->where('id_empresa', $companyId)->where('inactivo', 1)
                ->where('tipo', '<>', 0)->exists()
            : false;
        return array(
            'doctor' => $doctor,
            'appointment' => $appointment,
            'patients' => $patients,
            'selectedPatient' => $selectedPatient,
            'patientSearch' => $patientSearch,
            'rooms' => $rooms,
            'doctorSpecialty' => $doctorSpecialty,
            'insurers' => $insurers->orderBy('nombre')->get(),
            'procedures' => DB::table('procedimiento')->where('estado', 1)->select('id', 'nombre')->orderBy('nombre')->get(),
            'assistants' => $assistants,
            'additionalProcedures' => $additionalProcedures,
            'logs' => $logs,
            'patientObservation' => $patientObservation,
            'receiptInsuranceAllowed' => $receiptInsuranceAllowed,
            'hasTriage' => $appointment && Schema::hasTable('triaje_telefonico') ? DB::table('triaje_telefonico')->where('id_agenda', $appointment->id)->exists() : false,
            'historyCount' => $appointment ? DB::table('historiaclinica')->where('id_agenda', $appointment->id)->count() : 0,
            'fileCount' => $appointment ? DB::table('agenda_archivo')->where('id_agenda', $appointment->id)->count() : 0
        );
    }

    public function searchPatients($search)
    {
        $search = trim((string) $search);
        if (mb_strlen($search, 'UTF-8') < 3) return collect();
        $patients = DB::table('paciente');
        app(PatientTenantService::class)->constrain($patients, 'paciente.id');
        if (Schema::hasColumn('paciente', 'estado')) $patients->where('estado', 1);
        // Paciente no usa SoftDeletes. En bases legadas deleted_at puede contener
        // la fecha de creación de registros vigentes y no sirve como filtro.
        foreach (preg_split('/\s+/u', $search, -1, PREG_SPLIT_NO_EMPTY) as $term) {
            $like = '%' . str_replace(array('%', '_'), array('\\%', '\\_'), $term) . '%';
            $patients->where(function ($query) use ($like) {
                $query->where('id', 'like', $like)->orWhere('nombre1', 'like', $like)->orWhere('nombre2', 'like', $like)
                    ->orWhere('apellido1', 'like', $like)->orWhere('apellido2', 'like', $like);
            });
        }
        $patients = $patients->select('id', 'nombre1', 'nombre2', 'apellido1', 'apellido2', 'fecha_nacimiento', 'id_seguro')
            ->orderBy('apellido1')->orderBy('nombre1')->limit(20)->get();
        $patientsWithAppointments = array();
        if ($patients->isNotEmpty()) {
            foreach (
                DB::table('agenda')->whereIn('id_paciente', $patients->pluck('id')->all())
                    ->distinct()->pluck('id_paciente') as $patientId
            ) {
                $patientsWithAppointments[(string) $patientId] = true;
            }
        }
        foreach ($patients as $patient) {
            $patient->display_name = $this->personName($patient);
            $patient->has_previous_agenda = isset($patientsWithAppointments[(string) $patient->id]);
        }
        return $patients;
    }

    public function appointmentTypeForPatient($patientId, $lock = false)
    {
        if (trim((string) $patientId) === '') return 0;
        $query = DB::table('agenda')->where('id_paciente', $patientId)->select('id');
        if ($lock) $query->lockForUpdate();
        return $query->first() ? 1 : 0;
    }

    public function searchProcedures($search)
    {
        $search = trim((string) $search);
        if (mb_strlen($search, 'UTF-8') < 3) return collect();
        $query = DB::table('procedimiento')->where('estado', 1);
        foreach (preg_split('/\s+/u', $search, -1, PREG_SPLIT_NO_EMPTY) as $term) {
            $like = '%' . str_replace(array('%', '_'), array('\\%', '\\_'), $term) . '%';
            $query->where(function ($query) use ($like) {
                $query->where('nombre', 'like', $like)->orWhere('id', 'like', $like);
            });
        }
        return $query->select('id', 'nombre as text')->orderBy('nombre')->limit(20)->get();
    }

    public function allPatients()
    {
        $patients = DB::table('paciente');
        app(PatientTenantService::class)->constrain($patients, 'paciente.id');
        if (Schema::hasColumn('paciente', 'estado')) $patients->where('estado', 1);
        $patients = $patients->select('id', 'nombre1', 'nombre2', 'apellido1', 'apellido2', 'fecha_nacimiento', 'id_seguro')
            ->orderBy('apellido1')->orderBy('apellido2')->orderBy('nombre1')->orderBy('nombre2')->get();
        foreach ($patients as $patient) $patient->display_name = $this->personName($patient);
        return $patients;
    }

    public function availableIntervals($companyId, $doctorId, $date, $type, $excludeAppointment = null)
    {
        $this->doctor($companyId, $doctorId);
        $dayStart = strtotime($date . ' 00:00:00');
        if ($dayStart === false || date('Y-m-d', $dayStart) !== $date) throw new RuntimeException('La fecha consultada no es válida.');
        if ($date < date('Y-m-d')) return array();
        $dayEnd = strtotime($date . ' 23:59:59');
        $windows = array();
        $scheduleTypes = $this->scheduleTypesForAgenda($type);
        $hasConsultationInterval = Schema::hasColumn('horario_doctor', 'intervalo_consulta');
        $hasProcedureInterval = Schema::hasColumn('horario_doctor', 'intervalo_procedimiento');

        $recurring = DB::table('horario_doctor')->where('id_doctor', $doctorId)->where('estado', 1)
            ->where('ndia', (int) date('N', $dayStart))->whereIn('tipo', $scheduleTypes)->get();
        foreach ($recurring as $slot) {
            $interval = 30;
            if ((int) $type === 0 && $hasConsultationInterval) $interval = (int) $slot->intervalo_consulta;
            if ((int) $type === 1 && $hasProcedureInterval) $interval = (int) $slot->intervalo_procedimiento;
            $windows[] = array(strtotime($date . ' ' . $slot->hora_ini), strtotime($date . ' ' . $slot->hora_fin), max(15, $interval));
        }
        $exceptions = DB::table('excepcion_horario')->where('id_doctor1', $doctorId)
            ->where('inicio', '<=', date('Y-m-d H:i:s', $dayEnd))->where('fin', '>=', date('Y-m-d H:i:s', $dayStart))
            ->whereIn('tipo', $scheduleTypes)->get();
        foreach ($exceptions as $slot) {
            $windows[] = array(max($dayStart, strtotime($slot->inicio)), min($dayEnd, strtotime($slot->fin)), 30);
        }
        usort($windows, function ($a, $b) {
            return $a[0] - $b[0];
        });

        $busyQuery = DB::table('agenda')->where('estado', 1)
            ->where(function ($query) use ($doctorId) {
                $query->where('id_doctor1', $doctorId)->orWhere('id_doctor2', $doctorId)->orWhere('id_doctor3', $doctorId);
            })
            ->where('fechaini', '<', date('Y-m-d H:i:s', $dayEnd))->where('fechafin', '>', date('Y-m-d H:i:s', $dayStart));
        if ($excludeAppointment) $busyQuery->where('id', '<>', $excludeAppointment);
        $busy = $busyQuery->orderBy('fechaini')->get(array('fechaini', 'fechafin'));

        $available = array();
        foreach ($windows as $window) {
            if ($window[0] === false || $window[1] === false || $window[1] <= $window[0]) continue;
            $duration = $window[2] * 60;
            for ($start = $window[0]; $start + $duration <= $window[1]; $start += $duration) {
                $end = $start + $duration;
                if ($start <= time()) continue;
                $isBusy = false;
                foreach ($busy as $appointment) {
                    if (strtotime($appointment->fechaini) < $end && strtotime($appointment->fechafin) > $start) {
                        $isBusy = true;
                        break;
                    }
                }
                if (!$isBusy) $this->appendAvailableInterval($available, $date, $start, $end, $window[2]);
            }
        }
        $unique = array();
        foreach ($available as $item) $unique[$item['start'] . '|' . $item['end']] = $item;
        ksort($unique);
        return array_values($unique);
    }

    private function appendAvailableInterval(array &$items, $date, $start, $end, $minutes)
    {
        $items[] = array(
            'start' => date('Y-m-d\TH:i', $start),
            'end' => date('Y-m-d\TH:i', $end),
            'available_until' => date('Y-m-d\TH:i', $end),
            'label' => date('H:i', $start) . ' – ' . date('H:i', $end),
            'minutes' => (int) $minutes,
        );
    }

    private function scheduleTypesForAgenda($type)
    {
        if ((int) $type === 0) return array(0, 1);
        if ((int) $type === 1) return array(0, 2);
        return array(0);
    }

    private function agendaTypeForSchedule($type)
    {
        if ((int) $type === 2) return 1;
        return 0;
    }

    public function agendaForAction($companyId, $doctorId, $appointmentId)
    {
        return $this->appointment($companyId, $doctorId, $appointmentId);
    }

    public function saveAppointment($companyId, $doctorId, array $data, $actorId, $ip, $appointmentId = null, $allowOutside = false)
    {
        return DB::transaction(function () use ($companyId, $doctorId, $data, $actorId, $ip, $appointmentId, $allowOutside) {
            // Serializa la agenda del médico para que dos solicitudes simultáneas
            // no puedan reservar el mismo intervalo después de validar en paralelo.
            DB::table('users')->where('id', $doctorId)->lockForUpdate()->first();
            return $this->saveAppointmentLocked($companyId, $doctorId, $data, $actorId, $ip, $appointmentId, $allowOutside);
        });
    }

    private function saveAppointmentLocked($companyId, $doctorId, array $data, $actorId, $ip, $appointmentId, $allowOutside)
    {
        $this->doctor($companyId, $doctorId);
        $before = $appointmentId ? $this->appointment($companyId, $doctorId, $appointmentId) : null;
        $admitted = $before && ((int) $before->estado_cita === 4 || DB::table('historiaclinica')->where('id_agenda', $before->id)->exists());
        if ($admitted) {
            foreach (
                array(
                    'inicio' => 'fechaini',
                    'fin' => 'fechafin',
                    'tipo' => 'proc_consul',
                    'id_paciente' => 'id_paciente',
                    'id_sala' => 'id_sala',
                    'id_seguro' => 'id_seguro',
                    'espid' => 'espid',
                    'tipo_cita' => 'tipo_cita',
                    'id_doctor1' => 'id_doctor1',
                    'id_doctor2' => 'id_doctor2',
                    'id_doctor3' => 'id_doctor3',
                    'estado_cita' => 'estado_cita'
                ) as $input => $column
            ) {
                if (!array_key_exists($input, $data)) continue;
                $incoming = $data[$input];
                $existing = $before->$column;
                if (in_array($input, array('inicio', 'fin'), true)) {
                    $incoming = date('Y-m-d H:i', strtotime($incoming));
                    $existing = date('Y-m-d H:i', strtotime($existing));
                }
                if ((string) $incoming !== (string) $existing)
                    throw new RuntimeException('La atención ya está admitida. No se puede cambiar el horario, los médicos, la sala, el seguro, la especialidad, primera vez/consecutiva, el paciente, el tipo ni el estado de la agenda.');
            }
            $data['inicio'] = $before->fechaini;
            $data['fin'] = $before->fechafin;
            foreach (
                array(
                    'tipo' => 'proc_consul',
                    'id_paciente' => 'id_paciente',
                    'id_sala' => 'id_sala',
                    'id_doctor2' => 'id_doctor2',
                    'id_doctor3' => 'id_doctor3',
                    'id_seguro' => 'id_seguro',
                    'espid' => 'espid',
                    'tipo_cita' => 'tipo_cita',
                    'estado_cita' => 'estado_cita'
                ) as $input => $column
            )
                $data[$input] = $before->$column;
        }
        // La especialidad pertenece a la configuración del médico. No se acepta
        // como una selección manual del formulario.
        if (!$admitted && isset($data['tipo']) && (int) $data['tipo'] !== 2) {
            $doctorSpecialty = $this->assignedSpecialtiesForDoctor($doctorId)->first();
            $data['espid'] = $doctorSpecialty ? $doctorSpecialty->id : null;
        }
        $start = date('Y-m-d H:i:s', strtotime($data['inicio']));
        $end = date('Y-m-d H:i:s', strtotime($data['fin']));
        if ($start >= $end) throw new RuntimeException('La hora de fin debe ser posterior a la hora de inicio.');
        $originalStart = $before ? date('Y-m-d H:i:s', strtotime($before->fechaini)) : null;
        if ((!$appointmentId || $start !== $originalStart) && strtotime($start) <= time())
            throw new RuntimeException('No se puede crear o reprogramar una agenda en una fecha u hora anterior a la actual.');
        $type = (int) $data['tipo'];
        if ($type !== 1) {
            $preserveLockedAssistants = $before
                && ($admitted || (int) $before->estado_cita === 1)
                && (int) $before->proc_consul === $type;
            $data['id_doctor2'] = $preserveLockedAssistants ? $before->id_doctor2 : null;
            $data['id_doctor3'] = $preserveLockedAssistants ? $before->id_doctor3 : null;
        }
        if ($type !== 2 && empty($data['id_paciente'])) throw new RuntimeException('Seleccione el paciente de la atención.');
        if ($type !== 2 && empty($data['espid']))
            throw new RuntimeException('El médico no tiene una especialidad activa asignada. Configúrela en Usuarios antes de guardar la agenda.');
        if ($type !== 2 && (empty($data['id_sala']) || empty($data['id_seguro'])))
            throw new RuntimeException('Paciente, sala y seguro son obligatorios para una atención clínica.');
        if ($type === 1 && empty($data['id_procedimiento'])) throw new RuntimeException('Seleccione el procedimiento que se realizará.');
        $this->validateReferences($companyId, $doctorId, $type, $data);
        if (!$appointmentId && $type !== 2) {
            // La clasificación se decide con la base de datos, no con el valor
            // manipulable del formulario. El bloqueo evita dos "primeras veces"
            // simultáneas del mismo paciente con médicos diferentes.
            DB::table('paciente')->where('id', $data['id_paciente'])->lockForUpdate()->first();
            $data['tipo_cita'] = $this->appointmentTypeForPatient($data['id_paciente'], true);
        }
        if (!$admitted && !$allowOutside && !$this->insideSchedule($doctorId, $start, $end, $type))
            throw new RuntimeException('El rango está fuera del horario laboral configurado para el médico.');
        $overlap = DB::table('agenda')->where(function ($query) use ($doctorId) {
            $query->where('id_doctor1', $doctorId)->orWhere('id_doctor2', $doctorId)->orWhere('id_doctor3', $doctorId);
        })->where('estado', 1)
            ->where('id_empresa', $companyId)
            ->where(function ($query) use ($start, $end) {
                $query->where('fechaini', '<', $end)->where('fechafin', '>', $start);
            });
        if ($appointmentId) $overlap->where('id', '<>', $appointmentId);
        if (!$admitted && $overlap->exists()) throw new RuntimeException('El médico ya tiene una agenda en ese horario.');
        if (!$admitted && !empty($data['id_sala'])) {
            $roomBusy = DB::table('agenda')->where('id_sala', $data['id_sala'])->where('id_empresa', $companyId)->where('estado', 1)
                ->where('fechaini', '<', $end)->where('fechafin', '>', $start);
            if ($appointmentId) $roomBusy->where('id', '<>', $appointmentId);
            if ($roomBusy->exists()) throw new RuntimeException('La sala ya está ocupada en ese horario.');
        }
        $now = date('Y-m-d H:i:s');
        // Suspender conserva el estado clínico para auditoría, pero retira la
        // agenda de la disponibilidad y de la vista activa.
        $appointmentStatus = $appointmentId && isset($data['estado_cita']) ? (int) $data['estado_cita'] : 0;
        $payload = array(
            'fechaini' => $start,
            'fechafin' => $end,
            'id_doctor1' => $doctorId,
            'proc_consul' => $type,
            'id_empresa' => $companyId,
            'id_sala' => $this->nullable($data, 'id_sala'),
            'espid' => $this->nullable($data, 'espid'),
            'observaciones' => trim((string) (isset($data['observaciones']) ? $data['observaciones'] : '')),
            'estado' => $type !== 2 && $appointmentStatus === 3 ? 0 : 1,
            'ip_modificacion' => $ip,
            'id_usuariomod' => $actorId,
            'updated_at' => $now
        );
        if ($type === 2) {
            $payload['id_paciente'] = null;
            $payload['id_seguro'] = null;
            $payload['id_procedimiento'] = null;
            $payload['procedencia'] = trim((string) (isset($data['clase']) ? $data['clase'] : 0));
            $payload['estado_cita'] = 1;
        } else {
            $payload['id_paciente'] = $data['id_paciente'];
            $payload['id_seguro'] = $this->nullable($data, 'id_seguro');
            $payload['id_procedimiento'] = $type === 1 ? $this->nullable($data, 'id_procedimiento') : null;
            $payload['tipo_cita'] = isset($data['tipo_cita']) ? (int) $data['tipo_cita'] : 0;
            $payload['estado_cita'] = $appointmentStatus;
            $payload['est_amb_hos'] = (int) $data['est_amb_hos'];
            $payload['cortesia'] = strtoupper(trim((string) $data['cortesia']));
            foreach (array('id_doctor2', 'id_doctor3', 'omni', 'vip', 'tc', 'paciente_dr', 'adelantado') as $field)
                $payload[$field] = $this->nullable($data, $field);
            foreach (array('procedencia','teleconsulta') as $field)
                $payload[$field] = trim((string) (isset($data[$field]) ? $data[$field] : ''));
            foreach (array('cie10','cod_val','validacion_cv_msp','validacion_nc_msp','validacion_sec_msp','codigo_validacion_msp') as $field)
                if (!$appointmentId || array_key_exists($field, $data)) $payload[$field] = trim((string) (isset($data[$field]) ? $data[$field] : ''));
            if (!$appointmentId || array_key_exists('fecha_val', $data)) $payload['fecha_val'] = $this->nullable($data, 'fecha_val');
        }
        if ($appointmentId) {
            DB::table('agenda')->where('id', $appointmentId)->update($payload);
            $savedId = $appointmentId;
        } else {
            $payload['ip_creacion'] = $ip;
            $payload['id_usuariocrea'] = $actorId;
            $payload['created_at'] = $now;
            $savedId = DB::table('agenda')->insertGetId($payload);
        }
        if ($type !== 2) {
            $procedureIds = array_values(array_unique(array_filter(array_map('intval', isset($data['procedimientos']) ? (array) $data['procedimientos'] : array()))));
            if (array_key_exists('procedimientos', $data)) DB::table('agenda_procedimiento')->where('id_agenda', $savedId)->delete();
            foreach ($procedureIds as $procedureId) DB::table('agenda_procedimiento')->insert(array(
                'id_agenda' => $savedId,
                'id_procedimiento' => $procedureId,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => $now,
                'updated_at' => $now
            ));
            if (!empty($data['id_paciente'])) {
                $patientData = array();
                foreach (array('ocupacion', 'referido') as $field) if (isset($data[$field])) $patientData[$field] = trim((string) $data[$field]);
                if ($patientData) DB::table('paciente')->where('id', $data['id_paciente'])->update($patientData);
                if (Schema::hasTable('paciente_observaciones') && array_key_exists('observacion_paciente', $data)) {
                    $observation = trim((string) $data['observacion_paciente']);
                    $existingObservation = DB::table('paciente_observaciones')->where('id_paciente', $data['id_paciente'])->first();
                    $observationData = array('observacion' => $observation, 'id_usuariomod' => $actorId, 'ip_modificacion' => $ip, 'updated_at' => $now);
                    if ($existingObservation) DB::table('paciente_observaciones')->where('id', $existingObservation->id)->update($observationData);
                    elseif ($observation !== '') DB::table('paciente_observaciones')->insert(array_merge($observationData, array('id_paciente' => $data['id_paciente'], 'id_usuariocrea' => $actorId, 'ip_creacion' => $ip, 'created_at' => $now)));
                }
            }
            if (Schema::hasTable('triaje_telefonico')) {
                DB::table('triaje_telefonico')->where('id_agenda', $savedId)->delete();
                if (!empty($data['triaje'])) DB::table('triaje_telefonico')->insert(array(
                    'id_agenda' => $savedId,
                    'mensaje' => 'Pendiente',
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => $now,
                    'updated_at' => $now
                ));
            }
        }
        $isAdmission = $type !== 2 && isset($payload['estado_cita']) && (int) $payload['estado_cita'] === 4;
        if ($isAdmission) {
            $this->createAdmissionHistory($savedId, $payload, $actorId, $ip, $now);
            if ($type === 1) $this->createPendingPentax($savedId, $payload, $procedureIds, $actorId, $ip, $now);
        }
        if (!$isAdmission && !$before && $type === 1) {
            $this->createPendingPentax($savedId, $payload, $procedureIds, $actorId, $ip, $now);
        }
        DB::table('log_agenda')->insert(array(
            'id_agenda' => $savedId,
            'descripcion' => $before ? 'Agenda actualizada' : 'Agenda creada',
            'fechaini_ant' => $before ? $before->fechaini : null,
            'fechaini' => $start,
            'fechafin_ant' => $before ? $before->fechafin : null,
            'fechafin' => $end,
            'id_doctor1_ant' => $before ? $before->id_doctor1 : null,
            'id_doctor1' => $doctorId,
            'id_doctor2_ant' => $before ? $before->id_doctor2 : null,
            'id_doctor2' => isset($payload['id_doctor2']) ? $payload['id_doctor2'] : null,
            'id_doctor3_ant' => $before ? $before->id_doctor3 : null,
            'id_doctor3' => isset($payload['id_doctor3']) ? $payload['id_doctor3'] : null,
            'id_sala_ant' => $before ? $before->id_sala : null,
            'id_sala' => isset($payload['id_sala']) ? $payload['id_sala'] : null,
            'cortesia_ant' => $before ? $before->cortesia : null,
            'cortesia' => isset($payload['cortesia']) ? $payload['cortesia'] : null,
            'estado_cita_ant' => $before ? $before->estado_cita : null,
            'estado_cita' => isset($payload['estado_cita']) ? $payload['estado_cita'] : null,
            'estado_ant' => $before ? $before->estado : null,
            'estado' => $payload['estado'],
            'observaciones_ant' => $before ? $before->observaciones : null,
            'observaciones' => $payload['observaciones'],
            'id_usuariocrea' => $actorId,
            'id_usuariomod' => $actorId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now
        ));
        return $savedId;
    }

    public function cancelAppointment($companyId, $doctorId, $appointmentId, $actorId, $ip, $cancellationReason)
    {
        $cancellationReason = trim((string) $cancellationReason);
        if ($cancellationReason === '') throw new RuntimeException('Ingrese la observación que explica la cancelación de la agenda.');
        return DB::transaction(function () use ($companyId, $doctorId, $appointmentId, $actorId, $ip, $cancellationReason) {
            $appointment = $this->appointment($companyId, $doctorId, $appointmentId);
            if (DB::table('historiaclinica')->where('id_agenda', $appointment->id)->exists())
                throw new RuntimeException('La atención ya tiene historia clínica y no puede cancelarse desde la agenda.');
            $now = date('Y-m-d H:i:s');
            $observation = trim((string) $appointment->observaciones);
            $cancelledObservation = ($observation === '' ? '' : $observation . "\n") . 'Cancelación: ' . $cancellationReason;
            DB::table('agenda')->where('id', $appointmentId)->update(array(
                'estado' => 0,
                'estado_cita' => 3,
                'observaciones' => $cancelledObservation,
                'id_usuariomod' => $actorId,
                'ip_modificacion' => $ip,
                'updated_at' => $now
            ));
            DB::table('log_agenda')->insert(array(
                'id_agenda' => $appointmentId,
                'descripcion' => 'Agenda cancelada',
                'fechaini_ant' => $appointment->fechaini,
                'fechaini' => $appointment->fechaini,
                'fechafin_ant' => $appointment->fechafin,
                'fechafin' => $appointment->fechafin,
                'id_doctor1_ant' => $appointment->id_doctor1,
                'id_doctor1' => $appointment->id_doctor1,
                'estado_cita_ant' => $appointment->estado_cita,
                'estado_cita' => 3,
                'estado_ant' => $appointment->estado,
                'estado' => 0,
                'observaciones_ant' => $appointment->observaciones,
                'observaciones' => $cancelledObservation,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => $now,
                'updated_at' => $now
            ));
        });
    }

    public function schedule($companyId, $doctorId, $date)
    {
        $doctor = $this->doctor($companyId, $doctorId);
        $anchor = strtotime($date ?: date('Y-m-d'));
        if ($anchor === false) $anchor = time();
        $monday = strtotime('monday this week', $anchor);
        $sunday = strtotime('+6 days', $monday);
        $recurring = DB::table('horario_doctor')->where('id_doctor', $doctorId)->where('estado', 1)->orderBy('ndia')->orderBy('hora_ini')->get();
        $exceptions = DB::table('excepcion_horario')->where('id_doctor1', $doctorId)
            ->whereBetween('inicio', array(date('Y-m-d 00:00:00', $monday), date('Y-m-d 23:59:59', $sunday)))->orderBy('inicio')->get();
        return array(
            'doctor' => $doctor,
            'recurring' => $recurring,
            'exceptions' => $exceptions,
            'weekStart' => date('Y-m-d', $monday),
            'weekEnd' => date('Y-m-d', $sunday),
            'previous' => date('Y-m-d', strtotime('-7 days', $monday)),
            'next' => date('Y-m-d', strtotime('+7 days', $monday))
        );
    }

    public function addRecurring($companyId, $doctorId, array $data, $actorId, $ip)
    {
        $this->doctor($companyId, $doctorId);
        $day = (int) $data['dia'];
        $start = $data['hora_ini'] . ':00';
        $end = $data['hora_fin'] . ':00';
        if ($start >= $end) throw new RuntimeException('La hora final debe ser posterior a la inicial.');
        $rangeMinutes = (int) floor((strtotime('1970-01-01 ' . $end) - strtotime('1970-01-01 ' . $start)) / 60);
        $scheduleType = (int) $data['tipo'];
        if (in_array($scheduleType, array(0, 1, 3), true) && (int) $data['intervalo_consulta'] > $rangeMinutes)
            throw new RuntimeException('El intervalo de consulta no puede ser mayor que el rango laboral.');
        if (in_array($scheduleType, array(0, 2), true) && (int) $data['intervalo_procedimiento'] > $rangeMinutes)
            throw new RuntimeException('El intervalo de procedimiento no puede ser mayor que el rango laboral.');
        if (DB::table('horario_doctor')->where('id_doctor', $doctorId)->where('ndia', $day)->where('estado', 1)
            ->where('hora_ini', '<', $end)->where('hora_fin', '>', $start)->exists()
        )
            throw new RuntimeException('Ese rango se cruza con otro horario recurrente.');
        $values = array(
            'dia' => $this->dayName($day),
            'ndia' => $day,
            'hora_ini' => $start,
            'hora_fin' => $end,
            'id_doctor' => $doctorId,
            'tipo' => (int) $data['tipo'],
            'estado' => 1,
            'ip_creacion' => $ip,
            'id_usuariocrea' => $actorId,
            'ip_modificacion' => $ip,
            'id_usuariomod' => $actorId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        if (Schema::hasColumn('horario_doctor', 'intervalo_consulta')) $values['intervalo_consulta'] = (int) $data['intervalo_consulta'];
        if (Schema::hasColumn('horario_doctor', 'intervalo_procedimiento')) $values['intervalo_procedimiento'] = (int) $data['intervalo_procedimiento'];
        DB::table('horario_doctor')->insert($values);
    }

    public function deleteRecurring($companyId, $doctorId, $scheduleId, $actorId, $ip)
    {
        $this->doctor($companyId, $doctorId);
        $slot = DB::table('horario_doctor')->where('id', $scheduleId)->where('id_doctor', $doctorId)->where('estado', 1)->first();
        if (!$slot) throw new RuntimeException('El horario indicado ya no existe.');
        $busy = DB::table('agenda')->where(function ($query) use ($doctorId) {
            $query->where('id_doctor1', $doctorId)->orWhere('id_doctor2', $doctorId)->orWhere('id_doctor3', $doctorId);
        })->where('estado', 1)->where('fechaini', '>=', date('Y-m-d H:i:s'))
            ->whereRaw('(WEEKDAY(fechaini) + 1) = ?', array((int) $slot->ndia))->whereRaw('TIME(fechaini) < ?', array($slot->hora_fin))
            ->whereRaw('TIME(fechafin) > ?', array($slot->hora_ini))->exists();
        if ($busy) throw new RuntimeException('No se puede retirar el horario porque contiene citas futuras. Reprográmelas primero.');
        DB::table('horario_doctor')->where('id', $scheduleId)->update(array(
            'estado' => 0,
            'id_usuariomod' => $actorId,
            'ip_modificacion' => $ip,
            'updated_at' => date('Y-m-d H:i:s')
        ));
    }

    public function addException($companyId, $doctorId, array $data, $actorId, $ip)
    {
        $this->doctor($companyId, $doctorId);
        $start = date('Y-m-d H:i:s', strtotime($data['inicio']));
        $end = date('Y-m-d H:i:s', strtotime($data['fin']));
        if ($start >= $end) throw new RuntimeException('La hora final debe ser posterior a la inicial.');
        if (DB::table('excepcion_horario')->where('id_doctor1', $doctorId)->where('inicio', '<', $end)->where('fin', '>', $start)->exists())
            throw new RuntimeException('Ese rango se cruza con otra excepción del médico.');
        DB::table('excepcion_horario')->insert(array(
            'id_doctor1' => $doctorId,
            'inicio' => $start,
            'fin' => $end,
            'tipo' => (int) $data['tipo'],
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'id_usuariocrea' => $actorId,
            'id_usuariomod' => $actorId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ));
    }

    public function deleteException($companyId, $doctorId, $exceptionId)
    {
        $this->doctor($companyId, $doctorId);
        $slot = DB::table('excepcion_horario')->where('id', $exceptionId)->where('id_doctor1', $doctorId)->first();
        if (!$slot) throw new RuntimeException('La excepción indicada ya no existe.');
        if (DB::table('agenda')->where(function ($query) use ($doctorId) {
            $query->where('id_doctor1', $doctorId)->orWhere('id_doctor2', $doctorId)->orWhere('id_doctor3', $doctorId);
        })->where('estado', 1)->where('fechaini', '<', $slot->fin)
            ->where('fechafin', '>', $slot->inicio)->exists()
        ) throw new RuntimeException('La excepción contiene citas; reprográmelas antes de retirarla.');
        DB::table('excepcion_horario')->where('id', $exceptionId)->delete();
    }

    private function appointment($companyId, $doctorId, $appointmentId)
    {
        $appointment = DB::table('agenda')->where('id', $appointmentId)->where('id_doctor1', $doctorId)->where('estado', 1)
            ->where('id_empresa', $companyId)->first();
        if (!$appointment) throw new RuntimeException('La agenda seleccionada ya no está disponible.');
        return $appointment;
    }

    private function insideSchedule($doctorId, $start, $end, $type)
    {
        $day = (int) date('N', strtotime($start));
        $startTime = date('H:i:s', strtotime($start));
        $endTime = date('H:i:s', strtotime($end));
        if (date('Y-m-d', strtotime($start)) !== date('Y-m-d', strtotime($end))) return false;
        if (DB::table('excepcion_horario')->where('id_doctor1', $doctorId)->where('inicio', '<=', $start)->where('fin', '>=', $end)
            ->whereIn('tipo', $this->scheduleTypesForAgenda($type))->exists()
        ) return true;
        return DB::table('horario_doctor')->where('id_doctor', $doctorId)->where('estado', 1)->where('ndia', $day)
            ->where('hora_ini', '<=', $startTime)->where('hora_fin', '>=', $endTime)->whereIn('tipo', $this->scheduleTypesForAgenda($type))->exists();
    }

    private function validateReferences($companyId, $doctorId, $type, array $data)
    {
        if ($type !== 2) {
            $patient = DB::table('paciente')->where('id', $data['id_paciente']);
            app(PatientTenantService::class)->constrain($patient, 'paciente.id', $companyId);
            if (Schema::hasColumn('paciente', 'estado')) $patient->where('estado', 1);
            if (!$patient->exists()) throw new RuntimeException('El paciente seleccionado no está disponible.');
        }
        if (!empty($data['id_sala'])) {
            $allowedRooms = array();
            foreach ($this->availability->hospitals($companyId) as $hospital) foreach ($hospital->rooms as $room) $allowedRooms[] = (int) $room->id;
            if (!in_array((int) $data['id_sala'], $allowedRooms, true)) throw new RuntimeException('La sala no pertenece a la empresa activa.');
        }
        if (!empty($data['espid']) && !$this->assignedSpecialtiesForDoctor($doctorId)->where('id', (int) $data['espid'])->count())
            throw new RuntimeException('La especialidad configurada no está disponible para el médico.');
        if ($type === 1) {
            foreach (array('id_doctor2', 'id_doctor3') as $assistantField) {
                if (!empty($data[$assistantField]) && !$this->isActiveDoctorForCompany($companyId, $data[$assistantField]))
                    throw new RuntimeException('Uno de los médicos asistentes no pertenece a la empresa activa.');
            }
            if (!empty($data['id_doctor2']) && !empty($data['id_doctor3']) && (string) $data['id_doctor2'] === (string) $data['id_doctor3'])
                throw new RuntimeException('Los médicos asistentes deben ser diferentes.');
        }
        foreach (isset($data['procedimientos']) ? (array) $data['procedimientos'] : array() as $procedureId)
            if (!DB::table('procedimiento')->where('id', $procedureId)->where('estado', 1)->exists())
                throw new RuntimeException('Uno de los procedimientos adicionales no está activo.');
        if ($type !== 2) {
            $insurer = \Sis_medico\Seguro::query()->where('id', $data['id_seguro']);
            if (Schema::hasColumn('seguros', 'estado')) $insurer->where('estado', 1);
            elseif (Schema::hasColumn('seguros', 'inactivo')) $insurer->where('inactivo', 1);
            if (!$insurer->exists()) throw new RuntimeException('El seguro seleccionado no está activo.');
        }
        if ($type === 1 && !DB::table('procedimiento')->where('id', $data['id_procedimiento'])->where('estado', 1)->exists())
            throw new RuntimeException('El procedimiento seleccionado no está activo.');
    }

    private function nullable(array $data, $key)
    {
        return isset($data[$key]) && $data[$key] !== '' ? $data[$key] : null;
    }
    private function assignedSpecialtiesForDoctor($doctorId)
    {
        return \Sis_medico\Especialidad::query()->from('especialidad as e')->join('user_espe as ue', 'ue.espid', '=', 'e.id')
            ->where('ue.usuid', $doctorId)->where('e.estado', 1)
            ->select('e.id', 'e.nombre')->orderBy('ue.id')->get();
    }
    private function isActiveDoctorForCompany($companyId, $doctorId)
    {
        $query = DB::table('users as u')->join('usuario_empresa as ue', 'ue.id_usuario', '=', 'u.id');
        return DoctorUserType::constrain($query, 'u', 'doctor_type_validation')
            ->where('u.id', $doctorId)->where('ue.id_empresa', $companyId)->where('u.estado', 1)->exists();
    }
    private function defaultSlotEnd($date, $start, $limit)
    {
        return date('Y-m-d\TH:i', min(strtotime($date . ' ' . $limit), strtotime($date . ' ' . $start . ' +30 minutes')));
    }
    private function typeLabel($type)
    {
        return (int) $type === 1 ? 'Procedimiento' : ((int) $type === 2 ? 'Reunión' : 'Consulta');
    }
    private function createAdmissionHistory($appointmentId, array $agenda, $actorId, $ip, $now)
    {
        if (DB::table('historiaclinica')->where('id_agenda', $appointmentId)->lockForUpdate()->exists()) return;

        $patient = DB::table('paciente')->where('id', $agenda['id_paciente']);
        app(PatientTenantService::class)->constrain($patient, 'paciente.id', $agenda['id_empresa']);
        $patient = $patient->first();
        if (!$patient) throw new RuntimeException('No se pudo admitir porque el paciente ya no está disponible.');

        $history = array(
            'id_agenda' => $appointmentId,
            'id_paciente' => $agenda['id_paciente'],
            'id_seguro' => $agenda['id_seguro'],
            'id_doctor1' => $agenda['id_doctor1'],
            'id_doctor2' => isset($agenda['id_doctor2']) ? $agenda['id_doctor2'] : null,
            'id_doctor3' => isset($agenda['id_doctor3']) ? $agenda['id_doctor3'] : null,
            'id_usuario' => isset($patient->id_usuario) ? $patient->id_usuario : $agenda['id_paciente'],
            'parentesco' => isset($patient->parentesco) ? $patient->parentesco : 'Principal',
            'id_cie_10' => isset($agenda['cie10']) && trim((string) $agenda['cie10']) !== '' ? trim((string) $agenda['cie10']) : null,
            'id_usuariocrea' => $actorId,
            'id_usuariomod' => $actorId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
        );
        DB::table('historiaclinica')->insert($history);
    }

    private function createPendingPentax($appointmentId, array $agenda, array $additionalProcedureIds, $actorId, $ip, $now)
    {
        $historyId = DB::table('historiaclinica')->where('id_agenda', $appointmentId)->value('hcid');
        $procedureIds = array_values(array_unique(array_filter(array_merge(
            array((int) $agenda['id_procedimiento']),
            array_map('intval', $additionalProcedureIds)
        ))));
        $isAdmitted = !is_null($historyId);
        $observation = $isAdmitted ? 'PACIENTE ES PRE-ADMISIONADO' : 'PROCEDIMIENTO AGENDADO DESDE V3';

        $existing = DB::table('pentax')->where('id_agenda', $appointmentId)->lockForUpdate()->first();
        if ($existing) {
            if ($isAdmitted && !empty($existing->hcid)) return;
            $state = $isAdmitted && (int) $existing->estado_pentax === 0 ? -1 : (int) $existing->estado_pentax;
            DB::table('pentax')->where('id', $existing->id)->update(array(
                'hcid' => $historyId,
                'estado_pentax' => $state,
                'id_sala' => $agenda['id_sala'],
                'id_doctor1' => $agenda['id_doctor1'],
                'id_doctor2' => isset($agenda['id_doctor2']) ? $agenda['id_doctor2'] : null,
                'id_doctor3' => isset($agenda['id_doctor3']) ? $agenda['id_doctor3'] : null,
                'id_seguro' => $agenda['id_seguro'],
                'observacion' => $observation,
                'id_usuariomod' => $actorId,
                'ip_modificacion' => $ip,
                'updated_at' => $now,
            ));
            DB::table('pentax_procedimiento')->where('id_pentax', $existing->id)->delete();
            foreach ($procedureIds as $procedureId) {
                DB::table('pentax_procedimiento')->insert(array(
                    'id_pentax' => $existing->id,
                    'id_seguro' => $agenda['id_seguro'],
                    'id_procedimiento' => $procedureId,
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => $now,
                    'updated_at' => $now,
                ));
            }
            if ($isAdmitted && empty($existing->hcid)) {
                DB::table('pentax_log')->insert(array(
                    'id_pentax' => $existing->id,
                    'tipo_cambio' => 'PRE - ADMISION',
                    'descripcion' => 'PRE - ADMISION DESDE AGENDA V3',
                    'estado_pentax' => $state,
                    'id_seguro' => $agenda['id_seguro'],
                    'id_subseguro' => $existing->id_subseguro,
                    'id_sala' => $agenda['id_sala'],
                    'procedimientos' => implode('+', $procedureIds),
                    'id_doctor1' => $agenda['id_doctor1'],
                    'id_doctor2' => isset($agenda['id_doctor2']) ? $agenda['id_doctor2'] : null,
                    'id_doctor3' => isset($agenda['id_doctor3']) ? $agenda['id_doctor3'] : null,
                    'id_doctor4' => $existing->id_doctor4,
                    'id_anestesiologo' => $existing->id_anestesiologo,
                    'observacion' => $observation,
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => $now,
                    'updated_at' => $now,
                ));
            }
            return;
        }

        $pentaxId = DB::table('pentax')->insertGetId(array(
            'id_agenda' => $appointmentId,
            'estado_pentax' => $isAdmitted ? -1 : 0,
            'hcid' => $historyId,
            'id_sala' => $agenda['id_sala'],
            'id_doctor1' => $agenda['id_doctor1'],
            'id_doctor2' => isset($agenda['id_doctor2']) ? $agenda['id_doctor2'] : null,
            'id_doctor3' => isset($agenda['id_doctor3']) ? $agenda['id_doctor3'] : null,
            'id_seguro' => $agenda['id_seguro'],
            'id_subseguro' => null,
            'observacion' => $observation,
            'id_usuariocrea' => $actorId,
            'id_usuariomod' => $actorId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
        ));

        foreach ($procedureIds as $procedureId) {
            DB::table('pentax_procedimiento')->insert(array(
                'id_pentax' => $pentaxId,
                'id_seguro' => $agenda['id_seguro'],
                'id_procedimiento' => $procedureId,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => $now,
                'updated_at' => $now,
            ));
        }

        DB::table('pentax_log')->insert(array(
            'id_pentax' => $pentaxId,
            'tipo_cambio' => $isAdmitted ? 'PRE - ADMISION' : 'CREACION DE AGENDA V3',
            'descripcion' => $isAdmitted ? 'PRE - ADMISION' : 'PROCEDIMIENTO AGENDADO DESDE V3',
            'estado_pentax' => $isAdmitted ? -1 : 0,
            'id_seguro' => $agenda['id_seguro'],
            'id_subseguro' => null,
            'id_sala' => $agenda['id_sala'],
            'procedimientos' => implode('+', $procedureIds),
            'id_doctor1' => $agenda['id_doctor1'],
            'id_doctor2' => isset($agenda['id_doctor2']) ? $agenda['id_doctor2'] : null,
            'id_doctor3' => isset($agenda['id_doctor3']) ? $agenda['id_doctor3'] : null,
            'observacion' => $observation,
            'id_usuariocrea' => $actorId,
            'id_usuariomod' => $actorId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
        ));
    }

    public function updateRecurring($companyId, $doctorId, $scheduleId, array $data, $actorId, $ip)
    {
        $this->doctor($companyId, $doctorId);
        $slot = DB::table('horario_doctor')->where('id', $scheduleId)->where('id_doctor', $doctorId)->where('estado', 1)->first();
        if (!$slot) throw new RuntimeException('El horario indicado ya no existe.');
        $day = (int) $data['dia'];
        $start = $data['hora_ini'] . ':00';
        $end = $data['hora_fin'] . ':00';
        if ($start >= $end) throw new RuntimeException('La hora final debe ser posterior a la inicial.');
        $rangeMinutes = (int) floor((strtotime('1970-01-01 ' . $end) - strtotime('1970-01-01 ' . $start)) / 60);
        $scheduleType = (int) $data['tipo'];
        if (in_array($scheduleType, array(0, 1, 3), true) && (int) $data['intervalo_consulta'] > $rangeMinutes)
            throw new RuntimeException('El intervalo de consulta no puede ser mayor que el rango laboral.');
        if (in_array($scheduleType, array(0, 2), true) && (int) $data['intervalo_procedimiento'] > $rangeMinutes)
            throw new RuntimeException('El intervalo de procedimiento no puede ser mayor que el rango laboral.');
        if (DB::table('horario_doctor')->where('id_doctor', $doctorId)->where('ndia', $day)->where('estado', 1)
            ->where('id', '<>', $scheduleId)->where('hora_ini', '<', $end)->where('hora_fin', '>', $start)->exists()
        )
            throw new RuntimeException('Ese rango se cruza con otro horario recurrente.');
        $values = array(
            'dia' => $this->dayName($day),
            'ndia' => $day,
            'hora_ini' => $start,
            'hora_fin' => $end,
            'tipo' => $scheduleType,
            'id_usuariomod' => $actorId,
            'ip_modificacion' => $ip,
            'updated_at' => date('Y-m-d H:i:s')
        );
        if (Schema::hasColumn('horario_doctor', 'intervalo_consulta')) $values['intervalo_consulta'] = (int) $data['intervalo_consulta'];
        if (Schema::hasColumn('horario_doctor', 'intervalo_procedimiento')) $values['intervalo_procedimiento'] = (int) $data['intervalo_procedimiento'];
        DB::table('horario_doctor')->where('id', $scheduleId)->update($values);
    }
    private function stateLabel($state)
    {
        $labels = array(-1 => 'NO ASISTE', 0 => 'Por Confirmar', 1 => 'Confirmar', 2 => 'Reagendar', 3 => 'Suspender', 4 => 'ADMISIÓN');
        return isset($labels[(int) $state]) ? $labels[(int) $state] : 'En proceso';
    }
    private function dayName($day)
    {
        $names = array(1 => 'Lun.', 2 => 'Mar.', 3 => 'Mié.', 4 => 'Jue.', 5 => 'Vie.', 6 => 'Sáb.', 7 => 'Dom.');
        return isset($names[$day]) ? $names[$day] : '';
    }
    private function monthName($month)
    {
        $names = array(1 => 'ene', 2 => 'feb', 3 => 'mar', 4 => 'abr', 5 => 'may', 6 => 'jun', 7 => 'jul', 8 => 'ago', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic');
        return $names[$month];
    }
    private function personName($person)
    {
        return trim(implode(' ', array_filter(array($person->apellido1, isset($person->apellido2) ? $person->apellido2 : '', $person->nombre1, isset($person->nombre2) ? $person->nombre2 : ''), function ($value) {
            return trim((string) $value) !== '' && strtoupper(trim((string) $value)) !== '(N/A)';
        })));
    }
    private function patientName($event)
    {
        return trim(implode(' ', array_filter(array($event->p_apellido1, $event->p_apellido2, $event->p_nombre1, $event->p_nombre2))));
    }
}
