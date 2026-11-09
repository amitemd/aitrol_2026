<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class V3ReceptionDashboardService
{
    public function isReceptionist($user, $companyId)
    {
        if (!$user) return false;

        $typeName = DB::table('tipousuario')->where('id', $user->id_tipo_usuario)->value('nombre');
        if ($this->isReceptionName($typeName)) return true;

        if (!Schema::hasTable('access_user_roles') || !Schema::hasTable('access_roles')) return false;
        return DB::table('access_user_roles as ur')
            ->join('access_roles as r', 'r.id', '=', 'ur.role_id')
            ->where('ur.id_usuario', $user->id)
            ->where('ur.id_empresa', $companyId)
            ->where('r.id_empresa', $companyId)
            ->where('r.estado', 1)
            ->get(['r.nombre'])->contains(function ($role) {
                return $this->isReceptionName($role->nombre);
            });
    }

    public function summary($companyId)
    {
        $start = Carbon::today()->startOfDay();
        $end = Carbon::today()->endOfDay();
        $base = DB::table('agenda as a')
            ->where('a.id_empresa', $companyId)
            ->whereBetween('a.fechaini', [$start, $end])
            ->where('a.estado', '!=', 0);

        $total = (clone $base)->count();
        $confirmed = (clone $base)->whereNotNull('a.id_usuarioconfirma')->count();
        $attended = (clone $base)->whereExists(function ($query) {
            $query->select(DB::raw(1))->from('historiaclinica as h')->whereRaw('h.id_agenda = a.id')->where('h.estado', '!=', 0);
        })->count();

        $appointments = (clone $base)
            ->leftJoin('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->leftJoin('users as d', 'd.id', '=', 'a.id_doctor1')
            ->select([
                'a.id', 'a.fechaini', 'a.proc_consul', 'a.id_usuarioconfirma',
                'p.id as paciente_id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2',
                'd.nombre1 as doctor_nombre', 'd.apellido1 as doctor_apellido',
            ])
            ->selectRaw('EXISTS(SELECT 1 FROM historiaclinica h WHERE h.id_agenda = a.id AND h.estado <> 0) as atendida')
            ->orderBy('a.fechaini')->limit(12)->get();

        return [
            'date' => Carbon::today(),
            'date_label' => $this->spanishDate(Carbon::today()),
            'total' => $total,
            'confirmed' => $confirmed,
            'pending' => max(0, $total - $confirmed),
            'attended' => $attended,
            'appointments' => $appointments,
        ];
    }

    private function isReceptionName($name)
    {
        $name = mb_strtoupper((string) $name, 'UTF-8');
        return strpos($name, 'RECEPCI') !== false;
    }

    private function spanishDate(Carbon $date)
    {
        $days = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        $months = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        return ucfirst($days[(int) $date->format('w')]).', '.(int) $date->format('d')
            .' de '.$months[(int) $date->format('n')].' de '.$date->format('Y');
    }
}
