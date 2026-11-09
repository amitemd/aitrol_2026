<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class V3AvailabilityService
{
    public function hospitals($companyId)
    {
        $this->syncClinicalResources($companyId);
        $hospitals = $this->hospitalQuery($companyId)->select('h.*')->distinct()->orderBy('h.nombre_hospital')->get();
        $ids = $hospitals->pluck('id')->all();
        if (!$ids) return $hospitals;
        $roomsQuery = DB::table('sala')->whereIn('id_hospital', $ids)->where('estado', 1);
        if (Schema::hasColumn('sala', 'id_empresa')) {
            // La agenda solo debe ofrecer recursos físicos creados para la
            // empresa activa. Las salas legacy/globales (Rayos X, UCI, UCIN,
            // Emergencia, etc.) no forman parte de este catálogo.
            $roomsQuery->where('id_empresa', (string) $companyId);
        } else {
            // Compatibilidad temporal antes de ejecutar la migración de
            // alcance empresarial: solo reconoce nombres generados por este
            // catálogo, nunca áreas legacy.
            $roomsQuery->where(function ($query) {
                $query->where('nombre_sala', 'like', 'HABITACION %')
                    ->orWhere('nombre_sala', 'like', 'CONSULTORIO %')
                    ->orWhere('nombre_sala', 'like', 'QUIROFANO %');
            });
        }
        $rooms = $roomsQuery->select('id', 'id_hospital', 'nombre_sala', 'proc_consul_sala')
            ->orderBy('nombre_sala')->get();
        $roomsByHospital = $rooms->groupBy('id_hospital');
        $agenda = DB::table('agenda as a')->join('sala as s', 's.id', '=', 'a.id_sala')->whereIn('s.id_hospital', $ids)
            ->whereBetween('a.fechaini', array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))->where('a.estado', '<>', 0)
            ->where('a.id_empresa', $companyId)
            ->select('s.id_hospital', 's.id as room_id', DB::raw('COUNT(DISTINCT a.id) as total'))
            ->groupBy('s.id_hospital', 's.id')->get();
        $agendaByRoom = $agenda->keyBy('room_id');
        foreach ($hospitals as $hospital) {
            $hospitalRooms = $roomsByHospital->get($hospital->id, collect());
            $total = 0;
            foreach ($hospitalRooms as $room) {
                $care = $agendaByRoom->get($room->id); $room->agenda_count = $care ? (int) $care->total : 0;
                $room->category_label = $this->roomCategory($room);
                $total += $room->agenda_count;
            }
            $hospital->rooms = $hospitalRooms;
            $hospital->room_count = $hospitalRooms->count();
            $hospital->agenda_count = $total;
        }
        return $hospitals;
    }

    /**
     * Projects the hospital resources created in Gestión hospitalaria into
     * the legacy `sala` catalog consumed by agenda.id_sala.
     */
    private function syncClinicalResources($companyId)
    {
        if (!Schema::hasTable('habitacion') || !Schema::hasTable('tipo_habitacion')
            || !Schema::hasTable('piso') || !Schema::hasTable('hospital')
            || !Schema::hasColumn('sala', 'id_empresa')) return;

        $hospitalId = app(CurrentHospitalResolver::class)->id(array('company_id' => $companyId));
        if (!$hospitalId) return;

        $rooms = DB::table('habitacion as hr')
            ->join('piso as p', 'p.id', '=', 'hr.id_piso')
            ->join('tipo_habitacion as t', 't.id', '=', 'hr.id_tipo')
            ->leftJoin('cama as b', 'b.id_habitacion', '=', 'hr.id')
            ->where('hr.estado', '<>', 4);
        if (Schema::hasColumn('habitacion', 'id_empresa')) {
            $rooms->where('hr.id_empresa', (string) $companyId);
        }
        if (Schema::hasColumn('tipo_habitacion', 'estado')) $rooms->where('t.estado', '<>', 4);
        if (Schema::hasColumn('tipo_habitacion', 'id_empresa')) {
            $rooms->where('t.id_empresa', (string) $companyId);
        }

        $rows = $rooms->select('hr.id', 'hr.codigo', 'p.nombre_piso as piso_nombre',
            't.nombre as tipo_nombre', 't.categoria', 'b.codigo as cama_codigo')
            ->orderBy('hr.codigo')->orderBy('b.id')->get();
        foreach ($rows->groupBy('id') as $resourceRows) {
            $resource = $resourceRows->first();
            $category = strtolower((string) ($resource->categoria ?: 'habitacion'));
            $category = strtr($category, array('á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n'));
            $beds = $resourceRows->filter(function ($row) { return trim((string) $row->cama_codigo) !== ''; });
            if ($beds->isEmpty()) $beds = collect(array(null));

            foreach ($beds as $bed) {
                $roomName = $this->clinicalResourceName($category, $resource->piso_nombre,
                    $resource->codigo, $bed ? $bed->cama_codigo : null);
                $legacyRoomName = $this->clinicalResourceName($category, null,
                    $resource->codigo, $bed ? $bed->cama_codigo : null);
                $exists = DB::table('sala')->where('id_hospital', $hospitalId)
                    ->where('id_empresa', (string) $companyId)->where('nombre_sala', $roomName)->first();
                if (!$exists && $legacyRoomName !== $roomName) {
                    $exists = DB::table('sala')->where('id_hospital', $hospitalId)
                        ->where('id_empresa', (string) $companyId)->where('nombre_sala', $legacyRoomName)->first();
                    if ($exists) DB::table('sala')->where('id', $exists->id)->update(array('nombre_sala' => $roomName));
                }
                if ($exists) continue;
                $data = array(
                    'id_empresa' => (string) $companyId,
                    'id_hospital' => (int) $hospitalId,
                    'nombre_sala' => $roomName,
                    'proc_consul_sala' => $category === 'consultorio' ? 0 : 1,
                    'estado' => 1,
                );
                $actor = auth()->id();
                if (Schema::hasColumn('sala', 'id_usuariocrea')) $data['id_usuariocrea'] = $actor;
                if (Schema::hasColumn('sala', 'id_usuariomod')) $data['id_usuariomod'] = $actor;
                if (Schema::hasColumn('sala', 'ip_creacion')) $data['ip_creacion'] = request()->ip();
                if (Schema::hasColumn('sala', 'ip_modificacion')) $data['ip_modificacion'] = request()->ip();
                DB::table('sala')->insert($data);
            }
        }
    }

    private function clinicalResourceName($category, $floorName, $roomCode, $bedCode = null)
    {
        $prefix = $category === 'quirofano' ? 'QUIROFANO' : ($category === 'consultorio' ? 'CONSULTORIO' : 'HABITACION');
        $floor = trim((string) $floorName);
        $name = ($floor !== '' ? $floor . ' · ' : '') . $prefix . ' ' . trim((string) $roomCode);
        if ($bedCode !== null && trim((string) $bedCode) !== '') $name .= ' - CAMA ' . trim((string) $bedCode);
        return function_exists('mb_substr') ? mb_substr($name, 0, 80, 'UTF-8') : substr($name, 0, 80);
    }

    public function schedule($companyId, $hospitalId, $date, $roomId = null)
    {
        $hospital = $this->hospitalQuery($companyId)->where('h.id', $hospitalId)->select('h.*')->first();
        if (!$hospital) return null;
        $rooms = DB::table('sala')->where('id_hospital', $hospitalId)->where('estado', 1)
            ->when(Schema::hasColumn('sala', 'id_empresa'), function ($query) use ($companyId) {
                return $query->where('id_empresa', (string) $companyId);
            })->select('id', 'id_hospital', 'nombre_sala', 'proc_consul_sala')->orderBy('nombre_sala')->get();
        $roomIds = $rooms->pluck('id')->map(function ($id) { return (int) $id; })->all();
        $selectedRoom = $roomId !== null && in_array((int) $roomId, $roomIds, true) ? (int) $roomId : null;
        foreach ($rooms as $room) $room->category_label = $this->roomCategory($room);

        $appointments = DB::table('agenda as a')->join('sala as s', 's.id', '=', 'a.id_sala')
            ->leftJoin('paciente as p', 'p.id', '=', 'a.id_paciente')->leftJoin('seguros as sg', 'sg.id', '=', 'a.id_seguro')
            ->leftJoin('users as d', 'd.id', '=', 'a.id_doctor1')->leftJoin('procedimiento as pr', 'pr.id', '=', 'a.id_procedimiento')
            ->where('a.id_empresa', $companyId)->where('s.id_hospital', $hospitalId)
            ->where('a.estado', '<>', 0)
            ->whereBetween('a.fechaini', array($date.' 00:00:00', $date.' 23:59:59'));
        $counts = (clone $appointments)->select('a.id_sala', DB::raw('COUNT(DISTINCT a.id) as total'))
            ->groupBy('a.id_sala')->get()->keyBy('id_sala');
        if ($selectedRoom !== null) $appointments->where('a.id_sala', $selectedRoom);
        $appointments = $appointments->select(array('a.id', 'a.fechaini', 'a.fechafin', 'a.estado', 'a.estado_cita', 'a.proc_consul',
            'a.id_paciente', 's.id as room_id', 's.nombre_sala', 'p.nombre1 as patient_name1', 'p.nombre2 as patient_name2',
            'p.apellido1 as patient_lastname1', 'p.apellido2 as patient_lastname2', 'sg.nombre as insurance_name',
            'd.nombre1 as doctor_name1', 'd.apellido1 as doctor_lastname1', 'pr.nombre as procedure_name'))
            ->orderBy('a.fechaini')->get();
        foreach ($rooms as $room) {
            $count = $counts->get($room->id); $room->day_count = $count ? (int) $count->total : 0;
        }
        return array('hospital' => $hospital, 'rooms' => $rooms, 'appointments' => $appointments, 'selected_room' => $selectedRoom);
    }

    private function hospitalQuery($companyId)
    {
        $query = DB::table('hospital as h')->where('h.estado', 1);
        if (Schema::hasColumn('hospital', 'id_empresa')) {
            return $query->where('h.id_empresa', (string) $companyId);
        }
        if (Schema::hasTable('company_hospital_contexts')) {
            $configured = DB::table('company_hospital_contexts')->where('company_id', $companyId)->where('active', 1)->exists();
            if ($configured) return $query->join('company_hospital_contexts as chc', 'chc.hospital_id', '=', 'h.id')
                ->where('chc.company_id', $companyId)->where('chc.active', 1);
            return $query->whereRaw('1 = 0');
        }
        return $query->whereExists(function ($scope) use ($companyId) {
            $scope->select(DB::raw(1))->from('sala as sx')->join('agenda as ax', 'ax.id_sala', '=', 'sx.id')
                ->whereRaw('sx.id_hospital = h.id')->where('ax.id_empresa', $companyId);
        });
    }

    private function roomCategory($room)
    {
        $name = strtoupper((string) $room->nombre_sala);
        if (strpos($name, 'EMERGEN') !== false) return 'Emergencia';
        if (strpos($name, 'UCI') !== false || strpos($name, 'UCIN') !== false) return 'Cuidados intensivos';
        if (strpos($name, 'HOSPITAL') !== false) return 'Hospitalización';
        if (strpos($name, 'LABOR') !== false) return 'Laboratorio';
        if (strpos($name, 'IMAGEN') !== false || strpos($name, 'RAYOS') !== false) return 'Imágenes';
        if (strpos($name, 'CIRUG') !== false || strpos($name, 'QUIRO') !== false) return 'Cirugía';
        if (strpos($name, 'CONSULTORIO') !== false) return 'Consultorio';
        if (strpos($name, 'HABITACION') !== false || strpos($name, 'CAMA') !== false) return 'Habitación / cama';
        $types = array(0 => 'Consulta', 1 => 'Procedimiento', 2 => 'Reunión');
        return isset($types[(int) $room->proc_consul_sala]) ? $types[(int) $room->proc_consul_sala] : 'Área clínica';
    }
}
