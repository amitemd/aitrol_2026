<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class V3PatientCourtesyService
{
    public function filters(Request $request)
    {
        return array('buscar' => trim((string) $request->input('buscar', '')), 'paciente' => trim((string) $request->input('paciente', '')));
    }

    public function paginate(array $filters)
    {
        $query = DB::table('cortesia_paciente as c')->join('paciente as p', 'p.id', '=', 'c.id')
            ->select(array('c.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2'));
        app(PatientTenantService::class)->constrain($query, 'p.id');
        if ($filters['buscar'] !== '') {
            $like = '%'.$this->escapeLike($filters['buscar']).'%';
            $query->where(function ($part) use ($like) { $part->where('p.id', 'like', $like)->orWhere('p.nombre1', 'like', $like)->orWhere('p.nombre2', 'like', $like)->orWhere('p.apellido1', 'like', $like)->orWhere('p.apellido2', 'like', $like); });
        }
        return $query->orderBy('p.apellido1')->orderBy('p.nombre1')->paginate(20);
    }

    public function candidates($search)
    {
        if (trim($search) === '') return collect();
        $like = '%'.$this->escapeLike($search).'%';
        $query = DB::table('paciente as p')->leftJoin('cortesia_paciente as c', 'c.id', '=', 'p.id')->whereNull('c.id')
            ->where(function ($part) use ($like) { $part->where('p.id', 'like', $like)->orWhere('p.nombre1', 'like', $like)->orWhere('p.nombre2', 'like', $like)->orWhere('p.apellido1', 'like', $like)->orWhere('p.apellido2', 'like', $like); })
            ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2')->orderBy('p.apellido1')->limit(15);
        app(PatientTenantService::class)->constrain($query, 'p.id');
        return $query->get();
    }

    public function create(array $values, $ip)
    {
        return DB::transaction(function () use ($values, $ip) {
            app(PatientTenantService::class)->requireAccessible($values['paciente_id']);
            $exists = DB::table('paciente')->where('id', $values['paciente_id'])->lockForUpdate()->first();
            if (!$exists) abort(422, 'El paciente indicado no existe.');
            if (DB::table('cortesia_paciente')->where('id', $values['paciente_id'])->exists()) abort(422, 'El paciente ya tiene una configuración de cortesía.');
            return DB::table('cortesia_paciente')->insert(array('id' => $values['paciente_id'], 'cortesia' => $values['cortesia'], 'ilimitado' => $values['ilimitado'], 'ip_creacion' => $ip, 'ip_modificacion' => $ip, 'id_usuariocrea' => Auth::id(), 'id_usuariomod' => Auth::id(), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')));
        });
    }

    public function update($patientId, array $values, $ip)
    {
        return DB::transaction(function () use ($patientId, $values, $ip) {
            app(PatientTenantService::class)->requireAccessible($patientId);
            $record = DB::table('cortesia_paciente')->where('id', $patientId)->lockForUpdate()->first();
            if (!$record) abort(404, 'No existe una configuración de cortesía para este paciente.');
            return DB::table('cortesia_paciente')->where('id', $patientId)->update(array('cortesia' => $values['cortesia'], 'ilimitado' => $values['ilimitado'], 'ip_modificacion' => $ip, 'id_usuariomod' => Auth::id(), 'updated_at' => date('Y-m-d H:i:s')));
        });
    }

    private function escapeLike($value) { return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $value); }
}
