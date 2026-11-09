<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use RuntimeException;

class V3PentaxControlService
{
    private $availability;

    public function __construct(V3AvailabilityService $availability)
    {
        $this->availability = $availability;
    }

    public function dashboard($companyId, $date)
    {
        $date = $this->date($date);
        $rows = DB::table('agenda as a')
            ->join('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->leftJoin('pentax as px', 'px.id_agenda', '=', 'a.id')
            ->leftJoin('procedimiento as pr', 'pr.id', '=', 'a.id_procedimiento')
            ->leftJoin('sala as agenda_room', 'agenda_room.id', '=', 'a.id_sala')
            ->leftJoin('sala as pentax_room', 'pentax_room.id', '=', 'px.id_sala')
            ->leftJoin('seguros as agenda_insurance', 'agenda_insurance.id', '=', 'a.id_seguro')
            ->leftJoin('seguros as pentax_insurance', 'pentax_insurance.id', '=', 'px.id_seguro')
            ->leftJoin('users as agenda_doctor1', 'agenda_doctor1.id', '=', 'a.id_doctor1')
            ->leftJoin('users as pentax_doctor1', 'pentax_doctor1.id', '=', 'px.id_doctor1')
            ->leftJoin('users as pentax_doctor2', 'pentax_doctor2.id', '=', 'px.id_doctor2')
            ->leftJoin('users as pentax_doctor3', 'pentax_doctor3.id', '=', 'px.id_doctor3')
            ->leftJoin('users as pentax_nurse', 'pentax_nurse.id', '=', 'px.id_doctor4')
            ->leftJoin('users as pentax_anesthetist', 'pentax_anesthetist.id', '=', 'px.id_anestesiologo')
            ->where('a.id_empresa', $companyId)->where('a.estado', 1)->where('a.proc_consul', 1)
            ->whereBetween('a.fechaini', array($date.' 00:00:00', $date.' 23:59:59'))
            ->select(array(
                'a.id as agenda_id', 'a.fechaini', 'a.fechafin', 'a.est_amb_hos', 'a.omni', 'a.estado_cita',
                'p.id as patient_id', 'p.nombre1 as patient_name1', 'p.nombre2 as patient_name2',
                'p.apellido1 as patient_lastname1', 'p.apellido2 as patient_lastname2',
                'pr.id as main_procedure_id', 'pr.nombre as main_procedure_name', 'pr.observacion as main_procedure_display',
                'px.id as pentax_id', 'px.hcid', 'px.estado_pentax', 'px.id_sala as pentax_room_id',
                'pentax_room.nombre_sala as pentax_room_name', 'agenda_room.id as agenda_room_id',
                'agenda_room.nombre_sala as agenda_room_name',
                'pentax_insurance.nombre as pentax_insurance_name', 'agenda_insurance.nombre as agenda_insurance_name',
                'agenda_doctor1.nombre1 as agenda_doctor_name1', 'agenda_doctor1.apellido1 as agenda_doctor_lastname1',
                'pentax_doctor1.nombre1 as doctor_name1', 'pentax_doctor1.apellido1 as doctor_lastname1',
                'pentax_doctor2.nombre1 as assistant1_name1', 'pentax_doctor2.apellido1 as assistant1_lastname1',
                'pentax_doctor3.nombre1 as assistant2_name1', 'pentax_doctor3.apellido1 as assistant2_lastname1',
                'pentax_nurse.nombre1 as nurse_name1', 'pentax_nurse.apellido1 as nurse_lastname1',
                'pentax_anesthetist.nombre1 as anesthetist_name1', 'pentax_anesthetist.apellido1 as anesthetist_lastname1'
            ))->orderBy('a.fechaini')->get();

        $agendaIds = $rows->pluck('agenda_id')->all();
        $pentaxIds = $rows->pluck('pentax_id')->filter()->all();
        $agendaProcedures = empty($agendaIds) ? collect() : DB::table('agenda_procedimiento as ap')
            ->join('procedimiento as pr', 'pr.id', '=', 'ap.id_procedimiento')->whereIn('ap.id_agenda', $agendaIds)
            ->select('ap.id_agenda as owner_id', 'pr.id', 'pr.nombre', 'pr.observacion')->orderBy('ap.id')->get()->groupBy('owner_id');
        $pentaxProcedures = empty($pentaxIds) ? collect() : DB::table('pentax_procedimiento as pp')
            ->join('procedimiento as pr', 'pr.id', '=', 'pp.id_procedimiento')->whereIn('pp.id_pentax', $pentaxIds)
            ->select('pp.id_pentax as owner_id', 'pr.id', 'pr.nombre', 'pr.observacion')->orderBy('pp.id')->get()->groupBy('owner_id');

        foreach ($rows as $row) {
            $scheduledProcedures = $this->agendaProcedures($row, $agendaProcedures->get($row->agenda_id, collect()));
            $registeredProcedures = $row->pentax_id ? $pentaxProcedures->get($row->pentax_id, collect()) : collect();
            $row->procedures = $registeredProcedures->merge($scheduledProcedures)->unique('id')->values();
            $row->patient_name = $this->person($row, 'patient_');
            $row->doctor_name = $row->pentax_id
                ? trim($row->doctor_lastname1.' '.$row->doctor_name1)
                : trim($row->agenda_doctor_lastname1.' '.$row->agenda_doctor_name1);
            $row->room_id = $row->pentax_id ? $row->pentax_room_id : $row->agenda_room_id;
            $row->room_name = $row->pentax_id ? $row->pentax_room_name : $row->agenda_room_name;
            $row->insurance_name = $row->pentax_id ? $row->pentax_insurance_name : $row->agenda_insurance_name;
            $row->status_label = $row->pentax_id ? $this->statusLabel($row->estado_pentax, $row->hcid) : 'No admitido';
            $row->status_key = $row->pentax_id && !empty($row->hcid) ? (int) $row->estado_pentax : -2;
        }

        $meetings = DB::table('agenda as a')->join('users as d', 'd.id', '=', 'a.id_doctor1')
            ->leftJoin('users as uc', 'uc.id', '=', 'a.id_usuariocrea')->leftJoin('users as um', 'um.id', '=', 'a.id_usuariomod')
            ->where('a.id_empresa', $companyId)->where('a.estado', 1)->where('a.proc_consul', 2)
            ->whereBetween('a.fechaini', array($date.' 00:00:00', $date.' 23:59:59'))
            ->select('a.id', 'a.fechaini', 'a.fechafin', 'a.procedencia', 'a.observaciones',
                'd.nombre1 as doctor_name1', 'd.apellido1 as doctor_lastname1',
                'uc.nombre1 as creator_name1', 'uc.apellido1 as creator_lastname1',
                'um.nombre1 as editor_name1', 'um.apellido1 as editor_lastname1')->orderBy('a.fechaini')->get();

        $summary = array('total' => $rows->count(), 'waiting' => 0, 'active' => 0, 'recovery' => 0, 'finished' => 0);
        foreach ($rows as $row) {
            if ($row->status_key === 0 || $row->status_key === -1 || $row->status_key === -2) $summary['waiting']++;
            elseif ($row->status_key === 1 || $row->status_key === 2) $summary['active']++;
            elseif ($row->status_key === 3) $summary['recovery']++;
            elseif ($row->status_key === 4 || $row->status_key === 5) $summary['finished']++;
        }

        return array('date' => $date, 'rows' => $rows, 'meetings' => $meetings, 'rooms' => $this->rooms($companyId), 'summary' => $summary);
    }

    public function synchronizeAdmissions($companyId, $date, $actorId, $ip)
    {
        $date = $this->date($date);
        return DB::transaction(function () use ($companyId, $date, $actorId, $ip) {
            $pending = DB::table('pentax as px')->join('agenda as a', 'a.id', '=', 'px.id_agenda')
                ->where('a.id_empresa', $companyId)->where('a.estado', 1)->where('a.proc_consul', 1)
                ->whereBetween('a.fechaini', array($date.' 00:00:00', $date.' 23:59:59'))
                ->where(function ($query) { $query->whereNull('px.hcid')->orWhere('px.hcid', 0); })
                ->select('px.*')->lockForUpdate()->get();
            if ($pending->isEmpty()) return 0;

            $agendaIds = $pending->pluck('id_agenda')->all();
            $histories = DB::table('historiaclinica')->whereIn('id_agenda', $agendaIds)
                ->orderBy('hcid')->select('id_agenda', 'hcid')->get()->keyBy('id_agenda');
            $now = date('Y-m-d H:i:s');
            $updated = 0;
            foreach ($pending as $pentax) {
                $history = $histories->get($pentax->id_agenda);
                if (!$history) continue;
                $state = (int) $pentax->estado_pentax === 0 ? -1 : (int) $pentax->estado_pentax;
                $changed = DB::table('pentax')->where('id', $pentax->id)
                    ->where(function ($query) { $query->whereNull('hcid')->orWhere('hcid', 0); })->update(array(
                    'hcid' => $history->hcid,
                    'estado_pentax' => $state,
                    'observacion' => 'PACIENTE ES PRE-ADMISIONADO',
                    'id_usuariomod' => $actorId,
                    'ip_modificacion' => $ip,
                    'updated_at' => $now,
                ));
                if (!$changed) continue;
                $procedureIds = DB::table('pentax_procedimiento')->where('id_pentax', $pentax->id)
                    ->orderBy('id')->pluck('id_procedimiento')->all();
                DB::table('pentax_log')->insert(array(
                    'id_pentax' => $pentax->id,
                    'tipo_cambio' => 'PRE - ADMISION',
                    'descripcion' => 'SINCRONIZA ADMISIÓN EN PROCEDIMIENTOS V3',
                    'estado_pentax' => $state,
                    'id_seguro' => $pentax->id_seguro,
                    'id_subseguro' => $pentax->id_subseguro,
                    'id_sala' => $pentax->id_sala,
                    'procedimientos' => implode('+', $procedureIds),
                    'id_doctor1' => $pentax->id_doctor1,
                    'id_doctor2' => $pentax->id_doctor2,
                    'id_doctor3' => $pentax->id_doctor3,
                    'id_doctor4' => $pentax->id_doctor4,
                    'id_anestesiologo' => $pentax->id_anestesiologo,
                    'observacion' => 'Enlace con historia clínica recuperado por Procedimientos V3',
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'created_at' => $now,
                    'updated_at' => $now,
                ));
                $updated++;
            }
            return $updated;
        });
    }

    public function editor($companyId, $pentaxId)
    {
        $pentax = $this->find($companyId, $pentaxId);
        if (empty($pentax->hcid)) throw new RuntimeException('El paciente debe ser admitido antes de iniciar el circuito del procedimiento.');
        $procedures = DB::table('pentax_procedimiento as pp')->join('procedimiento as pr', 'pr.id', '=', 'pp.id_procedimiento')
            ->where('pp.id_pentax', $pentax->id)->select('pr.id', 'pr.nombre', 'pr.observacion')->orderBy('pp.id')->get();
        $catalogs = $this->catalogs($companyId);
        $catalogs['selected_procedure_ids'] = $procedures->pluck('id')->map(function ($id) { return (int) $id; })->all();
        return array('pentax' => $pentax, 'procedures' => $procedures, 'catalogs' => $catalogs,
            'states' => $this->allowedStates((int) $pentax->estado_pentax));
    }

    public function find($companyId, $pentaxId)
    {
        $pentax = DB::table('pentax as px')->join('agenda as a', 'a.id', '=', 'px.id_agenda')
            ->join('paciente as p', 'p.id', '=', 'a.id_paciente')->leftJoin('historiaclinica as hc', 'hc.hcid', '=', 'px.hcid')
            ->where('px.id', $pentaxId)->where('a.id_empresa', $companyId)
            ->select('px.*', 'a.fechaini', 'a.fechafin', 'a.id_paciente', 'a.id_empresa', 'a.estado_cita',
                'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'hc.parentesco')->first();
        if (!$pentax) throw new RuntimeException('El procedimiento no pertenece a la empresa activa.');
        $pentax->patient_name = trim($pentax->apellido1.' '.$this->cleanSecondName($pentax->apellido2).' '.$pentax->nombre1.' '.$this->cleanSecondName($pentax->nombre2));
        return $pentax;
    }

    public function assertUpdate($companyId, $pentaxId, array $data)
    {
        $pentax = $this->find($companyId, $pentaxId);
        if (empty($pentax->hcid)) throw new RuntimeException('El paciente debe ser admitido antes de cambiar el estado o el equipo clínico.');
        if (in_array((int) $pentax->estado_pentax, array(4, 5), true)) throw new RuntimeException('El circuito está cerrado y ya no admite modificaciones.');
        $allowed = array_keys($this->allowedStates((int) $pentax->estado_pentax));
        if (!in_array((int) $data['estado'], $allowed, true)) throw new RuntimeException('El cambio de estado solicitado no respeta el flujo del procedimiento.');
        $this->assertRoom($companyId, $data['id_sala']);
        $procedureIds = array_values(array_unique(array_map('intval', (array) $data['proc'])));
        if (!$procedureIds || DB::table('procedimiento')->whereIn('id', $procedureIds)->where('estado', 1)->count() !== count($procedureIds))
            throw new RuntimeException('Seleccione únicamente procedimientos activos.');
        $this->assertClinicalUser($companyId, $data['id_doctor1'], array(3), 'El médico asignado');
        $this->assertClinicalUser($companyId, isset($data['id_doctor2']) ? $data['id_doctor2'] : null, array(3, 6), 'El ayudante 1');
        $this->assertClinicalUser($companyId, isset($data['id_doctor3']) ? $data['id_doctor3'] : null, array(3, 6), 'El ayudante 2');
        $this->assertClinicalUser($companyId, isset($data['id_doctor4']) ? $data['id_doctor4'] : null, array(3, 6), 'El responsable de enfermería');
        $this->assertClinicalUser($companyId, isset($data['id_anestesiologo']) ? $data['id_anestesiologo'] : null, array(9), 'El anestesiólogo');
        $insurance = \Sis_medico\Seguro::query()->where('id', $data['id_seguro']);
        if (Schema::hasColumn('seguros', 'estado')) $insurance->where('estado', 1);
        elseif (Schema::hasColumn('seguros', 'inactivo')) $insurance->where('inactivo', 1);
        if (!$insurance->exists()) throw new RuntimeException('El seguro seleccionado no está activo.');
        if (!empty($data['id_subseguro']) && !DB::table('subseguro')->where('id', $data['id_subseguro'])
            ->where('id_seguro', $data['id_seguro'])->where('estado', 1)->exists())
            throw new RuntimeException('El subseguro no pertenece al seguro seleccionado.');
        return $pentax;
    }

    public function changeRoom($companyId, $pentaxId, $roomId, $actorId, $ip)
    {
        $this->assertRoom($companyId, $roomId);
        return DB::transaction(function () use ($companyId, $pentaxId, $roomId, $actorId, $ip) {
            $pentax = DB::table('pentax as px')->join('agenda as a', 'a.id', '=', 'px.id_agenda')
                ->where('px.id', $pentaxId)->where('a.id_empresa', $companyId)->select('px.*')->lockForUpdate()->first();
            if (!$pentax) throw new RuntimeException('El procedimiento no pertenece a la empresa activa.');
            if (in_array((int) $pentax->estado_pentax, array(4, 5), true)) throw new RuntimeException('No se puede cambiar la sala de un circuito cerrado.');
            if ((int) $pentax->id_sala === (int) $roomId) return;
            $procedureIds = DB::table('pentax_procedimiento')->where('id_pentax', $pentax->id)->orderBy('id')->pluck('id_procedimiento')->all();
            $now = date('Y-m-d H:i:s');
            DB::table('pentax')->where('id', $pentax->id)->update(array('id_sala' => $roomId, 'id_usuariomod' => $actorId,
                'ip_modificacion' => $ip, 'updated_at' => $now));
            DB::table('pentax_log')->insert(array('id_pentax' => $pentax->id, 'tipo_cambio' => 'ACTUALIZA',
                'descripcion' => 'ACTUALIZA SALA', 'estado_pentax' => $pentax->estado_pentax,
                'id_seguro' => $pentax->id_seguro, 'id_subseguro' => $pentax->id_subseguro, 'id_sala' => $roomId,
                'procedimientos' => implode('+', $procedureIds), 'id_doctor1' => $pentax->id_doctor1,
                'id_doctor2' => $pentax->id_doctor2, 'id_doctor3' => $pentax->id_doctor3,
                'id_doctor4' => $pentax->id_doctor4, 'id_anestesiologo' => $pentax->id_anestesiologo,
                'observacion' => 'Cambio de sala desde Procedimientos V3', 'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId, 'ip_creacion' => $ip, 'ip_modificacion' => $ip,
                'created_at' => $now, 'updated_at' => $now));
        });
    }

    public function logs($companyId, $pentaxId)
    {
        $pentax = $this->find($companyId, $pentaxId);
        $logs = DB::table('pentax_log as pl')->leftJoin('users as d1', 'd1.id', '=', 'pl.id_doctor1')
            ->leftJoin('users as d2', 'd2.id', '=', 'pl.id_doctor2')->leftJoin('users as d3', 'd3.id', '=', 'pl.id_doctor3')
            ->leftJoin('users as um', 'um.id', '=', 'pl.id_usuariomod')->leftJoin('seguros as sg', 'sg.id', '=', 'pl.id_seguro')
            ->leftJoin('sala as s', 's.id', '=', 'pl.id_sala')->where('pl.id_pentax', $pentax->id)
            ->select('pl.*', 'd1.nombre1 as doctor_name1', 'd1.apellido1 as doctor_lastname1',
                'd2.nombre1 as assistant1_name1', 'd2.apellido1 as assistant1_lastname1',
                'd3.nombre1 as assistant2_name1', 'd3.apellido1 as assistant2_lastname1',
                'um.nombre1 as user_name1', 'um.apellido1 as user_lastname1', 'sg.nombre as insurance_name', 's.nombre_sala')
            ->orderBy('pl.created_at', 'desc')->orderBy('pl.id', 'desc')->get();
        $procedureIds = array();
        foreach ($logs as $log) foreach (array_filter(explode('+', (string) $log->procedimientos)) as $id) $procedureIds[] = (int) $id;
        $names = empty($procedureIds) ? collect() : DB::table('procedimiento')->whereIn('id', array_unique($procedureIds))
            ->select('id', 'nombre', 'observacion')->get()->keyBy('id');
        foreach ($logs as $log) {
            $labels = array();
            foreach (array_filter(explode('+', (string) $log->procedimientos)) as $id) {
                $procedure = $names->get((int) $id);
                if ($procedure) $labels[] = trim((string) $procedure->observacion) ?: $procedure->nombre;
            }
            $log->procedure_names = implode(' + ', $labels);
            $log->status_label = $this->statusLabel($log->estado_pentax, true);
        }
        return array('pentax' => $pentax, 'logs' => $logs);
    }

    public function allowedStates($current)
    {
        $labels = array(-1 => 'Pre-admitido', 0 => 'En espera', 1 => 'Preparación', 2 => 'En procedimiento', 3 => 'Recuperación', 4 => 'Alta', 5 => 'Suspendido');
        $allowed = array($current);
        if ($current === -1) $allowed[] = 0;
        elseif (in_array($current, array(0, 1), true)) $allowed = array_merge($allowed, array(0, 1, 2, 5));
        elseif ($current === 2) $allowed = array_merge($allowed, array(0, 1, 2, 3, 5));
        elseif ($current === 3) $allowed = array_merge($allowed, array(3, 4, 5));
        $states = array();
        foreach (array_unique($allowed) as $state) if (isset($labels[$state])) $states[$state] = $labels[$state];
        return $states;
    }

    private function catalogs($companyId)
    {
        $users = function ($types) use ($companyId) {
            return DB::table('users as u')->join('usuario_empresa as ue', 'ue.id_usuario', '=', 'u.id')
                ->where('ue.id_empresa', $companyId)->whereIn('u.id_tipo_usuario', $types)->where('u.estado', 1)
                ->select('u.id', 'u.id_tipo_usuario', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2')
                ->distinct()->orderBy('u.apellido1')->get();
        };
        $insurances = \Sis_medico\Seguro::query();
        if (Schema::hasColumn('seguros', 'estado')) $insurances->where('estado', 1);
        elseif (Schema::hasColumn('seguros', 'inactivo')) $insurances->where('inactivo', 1);
        return array('doctors' => $users(array(3)), 'assistants' => $users(array(3, 6)), 'nurses' => $users(array(6)),
            'anesthetists' => $users(array(9)), 'rooms' => $this->rooms($companyId),
            'procedures' => DB::table('procedimiento')->where('estado', 1)->select('id', 'nombre', 'observacion')->orderBy('nombre')->get(),
            'insurances' => $insurances->select('id', 'nombre')->orderBy('nombre')->get(),
            'subinsurances' => DB::table('subseguro')->where('estado', 1)->select('id', 'id_seguro', 'nombre', 'principal')->orderBy('nombre')->get());
    }

    private function rooms($companyId)
    {
        $rooms = collect();
        foreach ($this->availability->hospitals($companyId) as $hospital) foreach ($hospital->rooms as $room) {
            $room->hospital_name = $hospital->nombre_hospital;
            $rooms->push($room);
        }
        return $rooms->sortBy('nombre_sala')->values();
    }

    private function assertRoom($companyId, $roomId)
    {
        if (!$this->rooms($companyId)->contains(function ($room) use ($roomId) { return (int) $room->id === (int) $roomId; }))
            throw new RuntimeException('La sala seleccionada no pertenece a una sede de la empresa activa.');
    }

    private function assertClinicalUser($companyId, $userId, array $types, $label)
    {
        if (is_null($userId) || $userId === '') return;
        $exists = DB::table('users as u')->join('usuario_empresa as ue', 'ue.id_usuario', '=', 'u.id')
            ->where('ue.id_empresa', $companyId)->where('u.id', $userId)->whereIn('u.id_tipo_usuario', $types)
            ->where('u.estado', 1)->exists();
        if (!$exists) throw new RuntimeException($label.' no pertenece a la empresa activa o no tiene el rol requerido.');
    }

    private function agendaProcedures($row, $additional)
    {
        $items = collect();
        if ($row->main_procedure_id) $items->push((object) array('id' => $row->main_procedure_id,
            'nombre' => $row->main_procedure_name, 'observacion' => $row->main_procedure_display));
        foreach ($additional as $procedure) if (!$items->contains('id', $procedure->id)) $items->push($procedure);
        return $items;
    }

    private function date($date)
    {
        $value = trim((string) $date);
        $parsed = \DateTime::createFromFormat('Y-m-d', $value ?: date('Y-m-d'));
        if (!$parsed || $parsed->format('Y-m-d') !== ($value ?: date('Y-m-d'))) throw new RuntimeException('La fecha seleccionada no es válida.');
        return $parsed->format('Y-m-d');
    }

    private function statusLabel($state, $hasHistory)
    {
        if (!$hasHistory) return 'Pendiente de admisión';
        $labels = array(-1 => 'Pre-admitido', 0 => 'En espera', 1 => 'Preparación', 2 => 'En procedimiento', 3 => 'Recuperación', 4 => 'Alta', 5 => 'Suspendido');
        return isset($labels[(int) $state]) ? $labels[(int) $state] : 'Sin estado';
    }

    private function person($row, $prefix)
    {
        return trim($row->{$prefix.'lastname1'}.' '.$this->cleanSecondName($row->{$prefix.'lastname2'}).' '.$row->{$prefix.'name1'}.' '.$this->cleanSecondName($row->{$prefix.'name2'}));
    }

    private function cleanSecondName($value)
    {
        return trim((string) $value) === '(N/A)' ? '' : trim((string) $value);
    }
}
