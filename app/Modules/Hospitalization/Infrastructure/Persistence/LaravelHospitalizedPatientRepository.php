<?php

namespace App\Modules\Hospitalization\Infrastructure\Persistence;

use App\Modules\Hospitalization\Application\Contracts\HospitalizedPatientRepository;
use App\Modules\Hospitalization\Application\Data\HospitalizedPatientFilters;
use App\Modules\Hospitalization\Application\Data\HospitalizedPatientResult;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class LaravelHospitalizedPatientRepository implements HospitalizedPatientRepository
{
    /** @return Collection<int, HospitalizedPatientResult> */
    public function search(HospitalizedPatientFilters $filters): Collection
    {
        $query = DB::table('ho_traspaso_sala008 as hospital_transfer')
            ->join('paciente as patient', 'patient.id', '=', 'hospital_transfer.id_paciente')
            ->join('sala as room', 'room.id', '=', 'hospital_transfer.id_sala')
            ->where('hospital_transfer.estado', '2')
            ->select([
                'hospital_transfer.fecha',
                'hospital_transfer.id_paciente',
                'hospital_transfer.causa',
                'hospital_transfer.id_doctor',
                'hospital_transfer.estado',
                'hospital_transfer.id_solicitud',
                'patient.nombre1',
                'patient.nombre2',
                'patient.apellido1',
                'patient.apellido2',
                'room.nombre_sala',
            ]);

        if ($filters->fromDate !== null && $filters->toDate !== null) {
            $query->whereBetween('hospital_transfer.fecha', [
                "{$filters->fromDate} 00:00:00",
                "{$filters->toDate} 23:59:59",
            ]);
        }

        if ($filters->roomId !== null) {
            $query->where('hospital_transfer.id_sala', $filters->roomId);
        }

        if ($filters->patientId !== null) {
            $query->where('hospital_transfer.id_paciente', $filters->patientId);
        }

        if ($filters->patientName !== null) {
            $this->applyPatientNameFilter($query, $filters->patientName);
        }

        return $query
            ->orderByDesc('hospital_transfer.fecha')
            ->orderByDesc('hospital_transfer.id')
            ->get()
            ->map(fn (object $record): HospitalizedPatientResult => new HospitalizedPatientResult(
                date: (string) $record->fecha,
                patientId: (string) $record->id_paciente,
                cause: $record->causa !== null ? (string) $record->causa : null,
                doctorId: $record->id_doctor !== null ? (string) $record->id_doctor : null,
                roomName: (string) $record->nombre_sala,
                status: (string) $record->estado,
                requestId: (string) $record->id_solicitud,
                firstName: $record->nombre1 !== null ? (string) $record->nombre1 : null,
                middleName: $record->nombre2 !== null ? (string) $record->nombre2 : null,
                lastName: $record->apellido1 !== null ? (string) $record->apellido1 : null,
                secondLastName: $record->apellido2 !== null ? (string) $record->apellido2 : null,
            ))
            ->values();
    }

    private function applyPatientNameFilter(Builder $query, string $patientName): void
    {
        $escapedName = addcslashes($patientName, '\\%_');
        $pattern = '%'.(preg_replace('/\s+/u', '%', $escapedName) ?? $escapedName).'%';

        $query->where(function (Builder $nameQuery) use ($pattern): void {
            $nameQuery
                ->whereRaw("CONCAT_WS(' ', patient.nombre1, patient.nombre2, patient.apellido1, patient.apellido2) LIKE ?", [$pattern])
                ->orWhereRaw("CONCAT_WS(' ', patient.nombre1, patient.apellido1, patient.apellido2) LIKE ?", [$pattern]);
        });
    }
}
