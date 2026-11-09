<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class WaitingRoomDisplayService
{
    public function boardsForDisplay($principalRoomId, $hospitalId)
    {
        if (!Schema::hasTable('sala')) {
            return array();
        }

        $rooms = DB::table('sala')
            ->where('id_hospital', (int) $hospitalId)
            ->where('estado', 1)
            ->select('id', 'nombre_sala')
            ->orderBy('nombre_sala')
            ->get();
        $roomIds = array_map(function ($room) {
            return (int) $room->id;
        }, $rooms->all());

        $boards = array();
        foreach ($rooms as $room) {
            $roomId = (int) $room->id;
            $boards[$roomId] = array(
                'id' => $roomId,
                'name' => trim((string) $room->nombre_sala),
                'short' => self::firstCharacter(trim((string) $room->nombre_sala)),
                'principal' => $roomId === (int) $principalRoomId,
                'patients' => array(),
            );
        }

        if (!isset($boards[(int) $principalRoomId])) {
            return array();
        }

        foreach ($this->agendaPatientsForRooms($roomIds) as $roomId => $patients) {
            if (isset($boards[$roomId])) {
                $boards[$roomId]['patients'] = array_merge($boards[$roomId]['patients'], $patients);
            }
        }
        foreach ($this->hospitalizationPatientsForRooms($roomIds, $hospitalId) as $roomId => $patients) {
            if (isset($boards[$roomId])) {
                $boards[$roomId]['patients'] = array_merge($boards[$roomId]['patients'], $patients);
            }
        }
        foreach ($this->surgeryPatientsForRooms($roomIds, $hospitalId) as $roomId => $patients) {
            if (isset($boards[$roomId])) {
                $boards[$roomId]['patients'] = array_merge($boards[$roomId]['patients'], $patients);
            }
        }

        $emergencyRoomId = $this->emergencyRoomId($roomIds, $hospitalId, $principalRoomId, $rooms);
        if ($emergencyRoomId && isset($boards[$emergencyRoomId])) {
            $boards[$emergencyRoomId]['patients'] = array_merge(
                $boards[$emergencyRoomId]['patients'],
                $this->emergencyPatients($hospitalId)
            );
        }

        $this->deduplicateBoardPatients($boards);
        foreach ($boards as &$board) {
            usort($board['patients'], function ($first, $second) {
                return strcmp($first['time'], $second['time']);
            });
        }
        unset($board);

        $principal = $boards[(int) $principalRoomId];
        unset($boards[(int) $principalRoomId]);
        // La pantalla siempre completa los dos espacios secundarios con salas
        // activas, aunque todavía no tengan pacientes asignados.
        $secondary = array_values($boards);

        return array_merge(array($principal), $secondary);
    }

    public function listsForRoom($roomId, $hospitalId)
    {
        return array(
            'consultations' => $this->agendaPatients($roomId, 0),
            'procedures' => $this->agendaPatients($roomId, 1),
            'emergencies' => $this->emergencyPatients($hospitalId),
            'hospitalizations' => $this->hospitalizationPatients($roomId, $hospitalId),
        );
    }

    public static function patientInitials($firstName, $secondName, $firstSurname, $secondSurname)
    {
        $parts = array($firstName, $secondName, $firstSurname, $secondSurname);
        $initials = array();

        foreach ($parts as $part) {
            $part = trim((string) $part);
            if ($part === '' || strtoupper($part) === '(N/A)') {
                continue;
            }

            $initials[] = self::firstCharacter($part) . '.';
        }

        return count($initials) ? implode(' ', $initials) : 'PCTE.';
    }

    public static function patientFullName($firstName, $secondName, $firstSurname, $secondSurname)
    {
        $parts = array($firstName, $secondName, $firstSurname, $secondSurname);
        $parts = array_filter(array_map(function ($part) {
            $part = trim((string) $part);
            return $part === '' || strtoupper($part) === '(N/A)' ? null : $part;
        }, $parts));

        return count($parts) ? implode(' ', $parts) : 'PACIENTE';
    }

    public function movementCursor()
    {
        return array(
            'transfer' => Schema::hasTable('clinical_bed_transfers')
                ? (int) DB::table('clinical_bed_transfers')->max('id') : 0,
            'discharge' => date('Y-m-d H:i:s') . '|0',
        );
    }

    public function movementsSince($hospitalId, $transferCursor, $dischargeCursor)
    {
        $events = array_merge(
            $this->bedTransferEvents($hospitalId, $transferCursor),
            $this->dischargeEvents($hospitalId, $dischargeCursor)
        );

        usort($events, function ($first, $second) {
            return strcmp($first['occurred_at'], $second['occurred_at']);
        });

        $nextTransferCursor = max(0, (int) $transferCursor);
        $nextDischargeCursor = $this->normalizeDischargeCursor($dischargeCursor);
        foreach ($events as &$event) {
            if ($event['type'] === 'transfer') {
                $nextTransferCursor = max($nextTransferCursor, (int) $event['_cursor']);
            } else {
                $nextDischargeCursor = $event['_cursor'];
            }
            unset($event['_cursor']);
        }
        unset($event);

        return array('events' => $events, 'cursor' => array(
            'transfer' => $nextTransferCursor,
            'discharge' => $nextDischargeCursor,
        ));
    }

    private function agendaPatients($roomId, $type)
    {
        if (!Schema::hasTable('agenda') || !Schema::hasTable('paciente')) {
            return array();
        }

        $start = Carbon::today()->startOfDay()->format('Y-m-d H:i:s');
        $end = Carbon::today()->endOfDay()->format('Y-m-d H:i:s');

        $rows = DB::table('agenda as a')
            ->join('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->where('a.id_sala', $roomId)
            ->where('a.proc_consul', $type)
            ->where('a.estado', '<>', 0)
            ->whereIn('a.estado_cita', array(0, 1, 4))
            ->whereBetween('a.fechaini', array($start, $end))
            ->select(
                'a.id',
                'a.fechaini',
                'a.estado_cita',
                'p.id as patient_id',
                'p.nombre1',
                'p.nombre2',
                'p.apellido1',
                'p.apellido2'
            )
            ->orderBy('a.fechaini')
            ->limit(60)
            ->get();

        return $this->formatRows($rows, 'fechaini', false);
    }

    private function agendaPatientsForRooms(array $roomIds)
    {
        if (!count($roomIds) || !Schema::hasTable('agenda') || !Schema::hasTable('paciente')) {
            return array();
        }

        $start = Carbon::today()->startOfDay()->format('Y-m-d H:i:s');
        $end = Carbon::today()->endOfDay()->format('Y-m-d H:i:s');
        $rows = DB::table('agenda as a')
            ->join('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->whereIn('a.id_sala', $roomIds)
            ->whereIn('a.proc_consul', array(0, 1))
            ->where('a.estado', '<>', 0)
            ->whereIn('a.estado_cita', array(0, 1, 4))
            ->whereBetween('a.fechaini', array($start, $end))
            ->select(
                'a.id',
                'a.id_sala as room_id',
                'a.proc_consul',
                'a.fechaini',
                'a.estado_cita',
                'p.id as patient_id',
                'p.nombre1',
                'p.nombre2',
                'p.apellido1',
                'p.apellido2'
            )
            ->orderBy('a.fechaini')
            ->limit(300)
            ->get();

        $beds = $this->currentBedsForPatients($rows);
        $grouped = array();
        foreach ($rows as $row) {
            $roomId = (int) $row->room_id;
            if (!isset($grouped[$roomId])) {
                $grouped[$roomId] = array();
            }
            $grouped[$roomId][] = array(
                'name' => self::patientFullName($row->nombre1, $row->nombre2, $row->apellido1, $row->apellido2),
                'patient_id' => (string) $row->patient_id,
                'time' => $this->formatTime($row->fechaini),
                'status' => $this->agendaStatus($row->estado_cita),
                'service' => (int) $row->proc_consul === 1 ? 'Procedimiento' : 'Consulta',
                'bed' => isset($beds[(string) $row->patient_id]) ? $beds[(string) $row->patient_id] : null,
            );
        }

        return $grouped;
    }

    private function bedTransferEvents($hospitalId, $cursor)
    {
        $required = array('clinical_bed_transfers', 'clinical_hospital_bed_locations', 'sala', 'cama', 'habitacion', 'paciente');
        foreach ($required as $table) {
            if (!Schema::hasTable($table)) {
                return array();
            }
        }

        $rows = DB::table('clinical_bed_transfers as transfer')
            ->join('paciente as patient', 'patient.id', '=', 'transfer.patient_id')
            ->join('cama as destination_bed', 'destination_bed.id', '=', 'transfer.to_bed_id')
            ->join('habitacion as destination_room', 'destination_room.id', '=', 'transfer.to_room_id')
            ->join('clinical_hospital_bed_locations as location', 'location.habitacion_id', '=', 'transfer.to_room_id')
            ->join('sala as destination_area', 'destination_area.id', '=', 'location.sala_id')
            ->where('transfer.id', '>', max(0, (int) $cursor))
            ->where('destination_area.id_hospital', (int) $hospitalId)
            ->select(
                'transfer.id',
                'transfer.transferred_at',
                'destination_bed.codigo as bed_name',
                'destination_room.codigo as room_name',
                'destination_area.nombre_sala as area_name',
                'patient.nombre1',
                'patient.nombre2',
                'patient.apellido1',
                'patient.apellido2'
            )
            ->orderBy('transfer.id')
            ->limit(20)
            ->get();

        $events = array();
        foreach ($rows as $row) {
            $name = self::patientFullName($row->nombre1, $row->nombre2, $row->apellido1, $row->apellido2);
            $destination = trim(implode(', ', array_filter(array(
                $row->area_name,
                $row->room_name ? 'habitación ' . $row->room_name : null,
                $row->bed_name ? 'cama ' . $row->bed_name : null,
            ))));
            $events[] = array(
                'key' => 'transfer-' . $row->id,
                'type' => 'transfer',
                'name' => $name,
                'destination' => $destination,
                'message' => $name . ' fue trasladado a ' . $destination . '.',
                'occurred_at' => (string) $row->transferred_at,
                '_cursor' => (int) $row->id,
            );
        }

        return $events;
    }

    private function dischargeEvents($hospitalId, $cursor)
    {
        if (!Schema::hasTable('clinical_encounter_closures')
            || !Schema::hasColumn('clinical_encounter_closures', 'physical_exit_at')
            || !Schema::hasTable('clinical_encounters')
            || !Schema::hasTable('paciente')) {
            return array();
        }

        list($cursorDate, $cursorId) = $this->dischargeCursorParts($cursor);
        $rows = DB::table('clinical_encounter_closures as closure')
            ->join('clinical_encounters as encounter', 'encounter.id', '=', 'closure.encounter_id')
            ->join('paciente as patient', 'patient.id', '=', 'encounter.id_paciente')
            ->whereNotNull('closure.physical_exit_at')
            ->where('encounter.id_hospital', (int) $hospitalId)
            ->where(function ($newer) use ($cursorDate, $cursorId) {
                $newer->where('closure.physical_exit_at', '>', $cursorDate)
                    ->orWhere(function ($sameSecond) use ($cursorDate, $cursorId) {
                        $sameSecond->where('closure.physical_exit_at', $cursorDate)
                            ->where('closure.id', '>', $cursorId);
                    });
            })
            ->select(
                'closure.id',
                'closure.physical_exit_at',
                'patient.nombre1',
                'patient.nombre2',
                'patient.apellido1',
                'patient.apellido2'
            )
            ->orderBy('closure.physical_exit_at')
            ->orderBy('closure.id')
            ->limit(20)
            ->get();

        $events = array();
        foreach ($rows as $row) {
            $name = self::patientFullName($row->nombre1, $row->nombre2, $row->apellido1, $row->apellido2);
            $events[] = array(
                'key' => 'discharge-' . $row->id,
                'type' => 'discharge',
                'name' => $name,
                'destination' => 'Alta hospitalaria',
                'message' => $name . ' recibió el alta y su salida fue confirmada.',
                'occurred_at' => (string) $row->physical_exit_at,
                '_cursor' => (string) $row->physical_exit_at . '|' . (int) $row->id,
            );
        }

        return $events;
    }

    private function normalizeDischargeCursor($cursor)
    {
        list($date, $id) = $this->dischargeCursorParts($cursor);
        return $date . '|' . $id;
    }

    private function dischargeCursorParts($cursor)
    {
        $parts = explode('|', (string) $cursor, 2);
        $date = isset($parts[0]) ? trim($parts[0]) : '';
        if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $date)) {
            $date = date('Y-m-d H:i:s');
        }
        return array($date, isset($parts[1]) ? max(0, (int) $parts[1]) : 0);
    }

    private function emergencyPatients($hospitalId)
    {
        if (!Schema::hasTable('ho_solicitud') || !Schema::hasTable('paciente')) {
            return array();
        }

        $query = DB::table('ho_solicitud as hs')
            ->join('paciente as p', 'p.id', '=', 'hs.id_paciente')
            ->where('hs.estado', 1)
            ->whereIn('hs.estado_paso', array(1, 2))
            ->whereNull('hs.fecha_salida');

        $hasEncounter = Schema::hasTable('clinical_encounters')
            && Schema::hasColumn('ho_solicitud', 'encounter_id');
        $hasLegacyRoom = Schema::hasTable('agenda') && Schema::hasTable('sala');

        if ($hasEncounter) {
            $query->leftJoin('clinical_encounters as ce', 'ce.id', '=', 'hs.encounter_id');
        }
        if ($hasLegacyRoom) {
            $query->leftJoin('agenda as a', 'a.id', '=', 'hs.id_agenda')
                ->leftJoin('sala as emergency_room', 'emergency_room.id', '=', 'a.id_sala');
        }

        // Emergencia es una cola del hospital, no de la sala configurada en el TV.
        // Los ingresos críticos pueden existir sin agenda, por eso se usa primero
        // el episodio clínico y se conserva la ubicación heredada como respaldo.
        if ($hasEncounter || $hasLegacyRoom) {
            $query->where(function ($location) use ($hospitalId, $hasEncounter, $hasLegacyRoom) {
                if ($hasEncounter && $hasLegacyRoom) {
                    $location->where('ce.id_hospital', $hospitalId)
                        ->orWhere(function ($legacy) use ($hospitalId) {
                            $legacy->whereNull('ce.id')
                                ->where('emergency_room.id_hospital', $hospitalId);
                        });
                } elseif ($hasEncounter) {
                    $location->where('ce.id_hospital', $hospitalId);
                } else {
                    $location->where('emergency_room.id_hospital', $hospitalId);
                }
            });
        }

        if ($hasEncounter) {
            $query->where(function ($encounter) {
                $encounter->whereNull('ce.id')
                    ->orWhere(function ($active) {
                        $active->where('ce.status', 'ACTIVE')
                            ->whereNull('ce.ended_at')
                            ->whereIn('ce.clinical_state', array('EMERGENCY_ACTIVE', 'TRANSFER_PENDING', 'CLOSURE_PENDING'));
                    });
            });
        }

        if (Schema::hasTable('clinical_emergency_flows')) {
            $query->leftJoin('clinical_emergency_flows as cef', 'cef.solicitud_id', '=', 'hs.id')
                ->where(function ($filter) use ($hospitalId) {
                    $filter->whereNull('cef.id')
                        ->orWhere(function ($flow) use ($hospitalId) {
                            $flow->whereNull('cef.closed_at')
                                ->where(function ($hospital) use ($hospitalId) {
                                    $hospital->whereNull('cef.hospital_id')
                                        ->orWhere('cef.hospital_id', $hospitalId);
                                });
                        });
                });
        }

        $rows = $query->select(
            'hs.id',
            'hs.fecha_ingreso',
            'p.id as patient_id',
            'p.nombre1',
            'p.nombre2',
            'p.apellido1',
            'p.apellido2'
        )
            ->distinct()
            ->orderBy('hs.fecha_ingreso')
            ->limit(60)
            ->get();

        return $this->formatRows($rows, 'fecha_ingreso', true);
    }

    private function hospitalizationPatients($roomId, $hospitalId)
    {
        $grouped = $this->hospitalizationPatientsForRooms(array((int) $roomId), $hospitalId);

        return isset($grouped[(int) $roomId]) ? $grouped[(int) $roomId] : array();
    }

    private function surgeryPatientsForRooms(array $roomIds, $hospitalId)
    {
        $required = array('ho_traspaso_sala008', 'ho_solicitud', 'paciente', 'sala');
        foreach ($required as $table) {
            if (!Schema::hasTable($table)) {
                return array();
            }
        }
        if (!count($roomIds)) {
            return array();
        }

        $rows = DB::table('ho_traspaso_sala008 as transfer')
            ->join('ho_solicitud as request', 'request.id', '=', 'transfer.id_solicitud')
            ->join('paciente as patient', 'patient.id', '=', 'transfer.id_paciente')
            ->join('sala as area', 'area.id', '=', 'transfer.id_sala')
            ->whereIn('transfer.id_sala', $roomIds)
            ->where('transfer.paso', 5)
            ->where('transfer.estado', 2)
            ->where('request.estado', 1)
            ->whereNull('request.fecha_salida')
            ->where('area.id_hospital', (int) $hospitalId)
            ->select(
                'transfer.id',
                'transfer.fecha',
                'transfer.id_sala as room_id',
                'patient.id as patient_id',
                'patient.nombre1',
                'patient.nombre2',
                'patient.apellido1',
                'patient.apellido2'
            )
            ->orderBy('transfer.fecha')
            ->orderBy('transfer.id')
            ->limit(100)
            ->get();

        $grouped = array();
        foreach ($rows as $row) {
            $roomId = (int) $row->room_id;
            if (!isset($grouped[$roomId])) {
                $grouped[$roomId] = array();
            }
            $grouped[$roomId][] = array(
                'name' => self::patientFullName($row->nombre1, $row->nombre2, $row->apellido1, $row->apellido2),
                'patient_id' => (string) $row->patient_id,
                'time' => $this->formatTime($row->fecha),
                'status' => 'Recibido',
                'service' => 'Cirugía',
                'bed' => null,
            );
        }

        return $grouped;
    }

    private function hospitalizationPatientsForRooms(array $roomIds, $hospitalId)
    {
        $required = array('ho_hospitalizacion', 'ho_traspaso_sala008', 'paciente', 'cama', 'habitacion', 'sala');
        foreach ($required as $table) {
            if (!Schema::hasTable($table)) {
                return array();
            }
        }
        if (!count($roomIds)) {
            return array();
        }

        $hasBedLocation = Schema::hasTable('clinical_hospital_bed_locations');
        $roomColumn = $hasBedLocation ? 'bed_location.sala_id' : 'transfer.id_sala';

        $query = DB::table('ho_hospitalizacion as hospitalization')
            ->join('ho_traspaso_sala008 as transfer', 'transfer.id', '=', 'hospitalization.id_traspaso')
            ->join('paciente as patient', 'patient.id', '=', 'hospitalization.id_paciente')
            ->join('cama as bed', 'bed.id', '=', 'hospitalization.id_cama')
            ->leftJoin('habitacion as room', 'room.id', '=', 'bed.id_habitacion')
            ->where('hospitalization.estado', 1)
            ->where('transfer.estado', 2);

        if ($hasBedLocation) {
            // La cama es la ubicación vigente. El traslado puede conservar la
            // sala de origen en registros heredados de Emergencia.
            $query->join('clinical_hospital_bed_locations as bed_location', 'bed_location.habitacion_id', '=', 'bed.id_habitacion')
                ->join('sala as area', 'area.id', '=', 'bed_location.sala_id');
        } else {
            $query->join('sala as area', 'area.id', '=', 'transfer.id_sala');
        }

        $query->whereIn($roomColumn, $roomIds)
            ->where('area.id_hospital', (int) $hospitalId);

        if (Schema::hasTable('ho_solicitud')) {
            $query->join('ho_solicitud as request', 'request.id', '=', 'transfer.id_solicitud')
                ->where('request.estado', 1)
                ->whereNull('request.fecha_salida');
        }

        $rows = $query->select(
            'hospitalization.id',
            $roomColumn . ' as room_id',
            'hospitalization.fecha',
            'hospitalization.created_at',
            'patient.id as patient_id',
            'patient.nombre1',
            'patient.nombre2',
            'patient.apellido1',
            'patient.apellido2',
            'bed.codigo as bed_name',
            'room.codigo as room_name'
        )
            ->orderBy('hospitalization.fecha')
            ->orderBy('hospitalization.id')
            ->limit(300)
            ->get();

        $grouped = array();
        foreach ($rows as $row) {
            $roomId = (int) $row->room_id;
            if (!isset($grouped[$roomId])) {
                $grouped[$roomId] = array();
            }
            $grouped[$roomId][] = array(
                'name' => self::patientFullName(
                    $row->nombre1,
                    $row->nombre2,
                    $row->apellido1,
                    $row->apellido2
                ),
                'patient_id' => (string) $row->patient_id,
                'time' => $this->formatTime($row->fecha ?: $row->created_at),
                'status' => 'Hospitalizado',
                'service' => 'Hospitalización',
                'bed' => $this->bedLocationLabel($row),
            );
        }

        return $grouped;
    }

    private function emergencyRoomId(array $roomIds, $hospitalId, $principalRoomId, $rooms)
    {
        if (Schema::hasTable('clinical_hospital_destinations')) {
            $configured = DB::table('clinical_hospital_destinations')
                ->where('hospital_id', (int) $hospitalId)
                ->where('step', 2)
                ->whereIn('sala_id', $roomIds)
                ->orderBy('sala_id')
                ->pluck('sala_id')
                ->map(function ($roomId) {
                    return (int) $roomId;
                })
                ->all();
            if (in_array((int) $principalRoomId, $configured, true)) {
                return (int) $principalRoomId;
            }
            if (count($configured)) {
                return $configured[0];
            }
        }

        foreach ($rooms as $room) {
            if (stripos((string) $room->nombre_sala, 'emerg') !== false) {
                return (int) $room->id;
            }
        }

        return (int) $principalRoomId;
    }

    private function formatRows($rows, $dateField, $emergency)
    {
        $patients = array();
        $beds = $this->currentBedsForPatients($rows);

        foreach ($rows as $row) {
            $patients[] = array(
                'name' => self::patientFullName(
                    $row->nombre1,
                    $row->nombre2,
                    $row->apellido1,
                    $row->apellido2
                ),
                'patient_id' => (string) $row->patient_id,
                'initials' => self::patientInitials(
                    $row->nombre1,
                    $row->nombre2,
                    $row->apellido1,
                    $row->apellido2
                ),
                'time' => $this->formatTime(isset($row->{$dateField}) ? $row->{$dateField} : null),
                'status' => $emergency ? 'En atención' : $this->agendaStatus($row->estado_cita),
                'service' => $emergency ? 'Emergencia' : null,
                'bed' => isset($beds[(string) $row->patient_id]) ? $beds[(string) $row->patient_id] : null,
            );
        }

        return $patients;
    }

    private function deduplicateBoardPatients(array &$boards)
    {
        $selected = array();
        $unidentified = array();
        $priorities = array('Cirugía' => 40, 'Hospitalización' => 30, 'Emergencia' => 20, 'Procedimiento' => 10, 'Consulta' => 10);

        foreach ($boards as $roomId => $board) {
            foreach ($board['patients'] as $patient) {
                $patientId = isset($patient['patient_id']) ? trim((string) $patient['patient_id']) : '';
                if ($patientId === '') {
                    $unidentified[] = array('room_id' => $roomId, 'patient' => $patient);
                    continue;
                }
                $service = isset($patient['service']) ? $patient['service'] : '';
                $priority = isset($priorities[$service]) ? $priorities[$service] : 0;
                if (!isset($selected[$patientId]) || $priority > $selected[$patientId]['priority']) {
                    $selected[$patientId] = array('room_id' => $roomId, 'patient' => $patient, 'priority' => $priority);
                }
            }
            $boards[$roomId]['patients'] = array();
        }

        foreach ($selected as $entry) {
            $boards[$entry['room_id']]['patients'][] = $entry['patient'];
        }
        foreach ($unidentified as $entry) {
            $boards[$entry['room_id']]['patients'][] = $entry['patient'];
        }
    }

    private function currentBedsForPatients($rows)
    {
        if (!Schema::hasTable('ho_hospitalizacion') || !Schema::hasTable('cama') || !Schema::hasTable('habitacion')) {
            return array();
        }

        $patientIds = array();
        foreach ($rows as $row) {
            if (isset($row->patient_id) && trim((string) $row->patient_id) !== '') {
                $patientIds[] = (string) $row->patient_id;
            }
        }
        $patientIds = array_values(array_unique($patientIds));
        if (!count($patientIds)) {
            return array();
        }

        $assignments = DB::table('ho_hospitalizacion as hospitalization')
            ->join('cama as bed', 'bed.id', '=', 'hospitalization.id_cama')
            ->leftJoin('habitacion as room', 'room.id', '=', 'bed.id_habitacion')
            ->where('hospitalization.estado', 1)
            ->whereIn('hospitalization.id_paciente', $patientIds)
            ->select(
                'hospitalization.id',
                'hospitalization.id_paciente',
                'hospitalization.updated_at',
                'bed.codigo as bed_name',
                'room.codigo as room_name'
            )
            ->orderBy('hospitalization.updated_at', 'desc')
            ->orderBy('hospitalization.id', 'desc')
            ->get();

        $beds = array();
        foreach ($assignments as $assignment) {
            $patientId = (string) $assignment->id_paciente;
            if (isset($beds[$patientId])) {
                continue;
            }
            $beds[$patientId] = $this->bedLocationLabel($assignment);
        }

        if (Schema::hasTable('cama_paciente')) {
            $missingPatientIds = array_values(array_diff($patientIds, array_keys($beds)));
            if (count($missingPatientIds)) {
                $legacyAssignments = DB::table('cama_paciente as assignment')
                    ->join('cama as bed', 'bed.id', '=', 'assignment.id_cama')
                    ->leftJoin('habitacion as room', 'room.id', '=', 'bed.id_habitacion')
                    ->where('assignment.estado', 1)
                    ->whereIn('assignment.id_paciente', $missingPatientIds)
                    ->select(
                        'assignment.id',
                        'assignment.id_paciente',
                        'assignment.updated_at',
                        'bed.codigo as bed_name',
                        'room.codigo as room_name'
                    )
                    ->orderBy('assignment.updated_at', 'desc')
                    ->orderBy('assignment.id', 'desc')
                    ->get();

                foreach ($legacyAssignments as $assignment) {
                    $patientId = (string) $assignment->id_paciente;
                    if (!isset($beds[$patientId])) {
                        $beds[$patientId] = $this->bedLocationLabel($assignment);
                    }
                }
            }
        }

        return array_filter($beds);
    }

    private function bedLocationLabel($assignment)
    {
        $parts = array();
        if (trim((string) $assignment->room_name) !== '') {
            $parts[] = 'Habitación ' . trim((string) $assignment->room_name);
        }
        if (trim((string) $assignment->bed_name) !== '') {
            $parts[] = 'Cama ' . trim((string) $assignment->bed_name);
        }

        return count($parts) ? implode(' · ', $parts) : null;
    }

    private function agendaStatus($status)
    {
        if ((int) $status === 4) {
            return 'En sala';
        }

        if ((int) $status === 1) {
            return 'Confirmado';
        }

        return 'Programado';
    }

    private function formatTime($value)
    {
        if (empty($value)) {
            return '--:--';
        }

        try {
            return Carbon::parse($value)->format('H:i');
        } catch (\Exception $exception) {
            return '--:--';
        }
    }

    private static function firstCharacter($value)
    {
        if (function_exists('mb_substr')) {
            return mb_strtoupper(mb_substr($value, 0, 1, 'UTF-8'), 'UTF-8');
        }

        return strtoupper(substr($value, 0, 1));
    }
}
