<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Especialidad;

class V3ClinicalAgendaService
{
    private $availability;

    public function __construct(V3AvailabilityService $availability)
    {
        $this->availability = $availability;
    }

    public function directory($companyId, array $filters = [])
    {
        $specialties = $this->specialties($companyId);
        $doctors = $this->doctors($companyId, $filters);
        $doctorIds = $doctors->pluck('id')->all();
        $doctorSpecialties = collect();

        if ($doctorIds) {
            $doctorSpecialties = DB::table('user_espe as ue')
                ->join('especialidad as e', 'e.id', '=', 'ue.espid')
                ->join('usuario_empresa as company', 'company.id_usuario', '=', 'ue.usuid')
                ->where('company.id_empresa', $companyId)
                ->whereIn('ue.usuid', $doctorIds)
                ->where('e.estado', 1)
                ->select('ue.usuid as doctor_id', 'e.id', 'e.nombre')
                ->distinct()
                ->orderBy('e.nombre')
                ->get()
                ->groupBy('doctor_id');
        }

        foreach ($doctors as $doctor) {
            $doctor->specialties = $doctorSpecialties->get($doctor->id, collect());
            $doctor->display_name = $this->doctorName($doctor);
            $doctor->initials = $this->initials($doctor);
            $doctor->agenda_id = strlen((string) $doctor->id) < 10
                ? str_pad((string) $doctor->id, 10, '0', STR_PAD_LEFT) : (string) $doctor->id;
        }

        $hospitals = $this->availability->hospitals($companyId);
        $areas = ['hospitalization' => collect(), 'emergency' => collect(), 'procedures' => collect()];
        foreach ($hospitals as $hospital) {
            foreach ($hospital->rooms as $room) {
                $room->hospital_name = $hospital->nombre_hospital;
                $category = strtolower((string) $room->category_label);
                if (strpos($category, 'emergencia') !== false) {
                    $areas['emergency']->push($room);
                }
                if (strpos($category, 'hospital') !== false || strpos($category, 'intensivo') !== false) {
                    $areas['hospitalization']->push($room);
                }
                if (strpos($category, 'procedimiento') !== false || strpos($category, 'cirug') !== false) {
                    $areas['procedures']->push($room);
                }
            }
        }

        return ['doctors' => $doctors, 'specialties' => $specialties, 'hospitals' => $hospitals, 'areas' => $areas];
    }

    public function updateLimits($companyId, $doctorId, $consultations, $procedures, $actorId, $ip)
    {
        $doctorQuery = DB::table('users as u')->join('usuario_empresa as ue', 'ue.id_usuario', '=', 'u.id');
        $doctor = DoctorUserType::constrain($doctorQuery, 'u', 'doctor_type_limits')
            ->where('ue.id_empresa', $companyId)->where('u.id', $doctorId)
            ->where('u.estado', 1)->select('u.id')->first();
        if (! $doctor) {
            return false;
        }

        DB::table('users')->where('id', $doctorId)->update([
            'max_consulta' => (int) $consultations, 'max_procedimiento' => (int) $procedures,
            'id_usuariomod' => $actorId, 'ip_modificacion' => $ip, 'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return true;
    }

    private function doctors($companyId, array $filters)
    {
        $query = DB::table('users as u')->join('usuario_empresa as company', 'company.id_usuario', '=', 'u.id');
        DoctorUserType::constrain($query, 'u', 'doctor_type_directory');
        $query->where('company.id_empresa', $companyId)->where('u.estado', 1);
        $specialty = isset($filters['specialty']) ? trim((string) $filters['specialty']) : '';
        if ($specialty !== '') {
            $query->whereExists(function ($scope) use ($specialty) {
                $scope->select(DB::raw(1))->from('user_espe as filter_specialty')
                    ->whereRaw('filter_specialty.usuid = u.id')->where('filter_specialty.espid', $specialty);
            });
        }
        $search = isset($filters['search']) ? trim((string) $filters['search']) : '';
        if ($search !== '') {
            $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $search).'%';
            $query->where(function ($scope) use ($like) {
                $scope->where('u.id', 'like', $like)->orWhere('u.nombre1', 'like', $like)
                    ->orWhere('u.nombre2', 'like', $like)->orWhere('u.apellido1', 'like', $like)
                    ->orWhere('u.apellido2', 'like', $like);
            });
        }

        return $query->select(['u.id', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'u.imagen_url'])
            ->distinct()->orderBy('u.apellido1')->orderBy('u.nombre1')->get();
    }

    private function specialties($companyId)
    {
        $query = Especialidad::query()->from('especialidad as e')
            ->join('user_espe as specialty_assignment', 'specialty_assignment.espid', '=', 'e.id')
            ->join('users as doctor', 'doctor.id', '=', 'specialty_assignment.usuid')
            ->join('usuario_empresa as company', 'company.id_usuario', '=', 'doctor.id');
        DoctorUserType::constrain($query, 'doctor', 'specialty_doctor_type');
        $query->where('company.id_empresa', (string) $companyId)
            ->where('doctor.estado', 1)
            ->where('e.estado', 1);

        return $query->select('e.id', 'e.nombre')->distinct()->orderBy('e.nombre')->get();
    }

    private function doctorName($doctor)
    {
        $parts = [$doctor->apellido1, $doctor->apellido2, $doctor->nombre1, $doctor->nombre2];
        $parts = array_filter($parts, function ($part) {
            return trim((string) $part) !== '' && strtoupper(trim((string) $part)) !== '(N/A)';
        });

        return trim(implode(' ', $parts));
    }

    private function initials($doctor)
    {
        $first = trim((string) $doctor->nombre1);
        $last = trim((string) $doctor->apellido1);

        return strtoupper(($first !== '' ? substr($first, 0, 1) : '').($last !== '' ? substr($last, 0, 1) : ''));
    }
}
