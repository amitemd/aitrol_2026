<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Especialidad;
use Sis_medico\Procedimiento;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\v3\clinical\AdvancedIntegral;

class V3AdvancedIntegralService
{
    public function filters(Request $request)
    {
        $today = date('Y-m-d');
        $filters = array(
            'tipo' => in_array((string) $request->input('tipo', '2'), array('0', '1', '2'), true) ? (string) $request->input('tipo', '2') : '2',
            'desde' => $this->date($request->input('desde', $today), $today),
            'hasta' => $this->date($request->input('hasta', $today), $today),
            'cedula' => trim((string) $request->input('cedula', '')),
            'paciente' => trim((string) $request->input('paciente', '')),
            'seguro' => trim((string) $request->input('seguro', '')),
            'doctor' => trim((string) $request->input('doctor', '')),
            'especialidad' => trim((string) $request->input('especialidad', '')),
            'procedimiento' => trim((string) $request->input('procedimiento', '')),
        );
        if ($filters['desde'] > $filters['hasta']) {
            $start = $filters['hasta'];
            $filters['hasta'] = $filters['desde'];
            $filters['desde'] = $start;
        }
        return $filters;
    }

    public function paginate(array $filters, $companyId)
    {
        $query = $this->baseQuery($companyId)->whereBetween('a.created_at', array(
            $filters['desde'].' 00:00:00', $filters['hasta'].' 23:59:59'
        ));
        $this->applyFilters($query, $filters);
        $rows = $query->orderBy('a.created_at', 'desc')->paginate(25);
        $this->attachProcedures($rows->getCollection());
        return $rows;
    }

    public function all(array $filters, $companyId)
    {
        $query = $this->baseQuery($companyId)->whereBetween('a.created_at', array(
            $filters['desde'].' 00:00:00', $filters['hasta'].' 23:59:59'
        ));
        $this->applyFilters($query, $filters);
        $rows = $query->orderBy('a.created_at', 'desc')->get();
        $this->attachProcedures($rows);
        return $rows;
    }

    public function find($id, $companyId)
    {
        $row = $this->baseQuery($companyId)->where('a.id', $id)->first();
        if (!$row) abort(404, 'El integral adelantado solicitado no existe para la empresa activa.');
        $this->attachProcedures(collect(array($row)));
        $row->history = DB::table('historiaclinica as hc')
            ->leftJoin('seguros as hs', 'hs.id', '=', 'hc.id_seguro')
            ->leftJoin('subseguro as ss', 'ss.id', '=', 'hc.id_subseguro')
            ->where('hc.id_agenda', $row->id)
            ->select('hc.*', 'hs.nombre as seguro_historia', 'ss.nombre as subseguro_nombre')
            ->first();
        return $row;
    }

    public function catalogs()
    {
        return app(V3CatalogCacheService::class)->remember('advanced-integral', function () {
            return array(
                'insurances' => Seguro::where('inactivo', '1')->orderBy('nombre')->get(),
                'doctors' => User::where('id_tipo_usuario', 3)->where('estado', 1)->orderBy('apellido1')->orderBy('nombre1')->get(),
                'specialties' => Especialidad::where('estado', '1')->orderBy('nombre')->get(),
                'procedures' => Procedimiento::where('estado', '1')->orderBy('nombre')->get(),
            );
        });
    }

    private function baseQuery($companyId)
    {
        return AdvancedIntegral::query()->from('agenda as a')
            ->leftJoin('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->leftJoin('seguros as s', 's.id', '=', 'a.id_seguro')
            ->leftJoin('seguros as sg', 'sg.id', '=', 'a.seguro_gestionado')
            ->leftJoin('users as d', 'd.id', '=', 'a.id_doctor1')
            ->leftJoin('users as um', 'um.id', '=', 'a.id_usuariomod')
            ->leftJoin('sala as sa', 'sa.id', '=', 'a.id_sala')
            ->leftJoin('hospital as h', 'h.id', '=', 'sa.id_hospital')
            ->leftJoin('especialidad as e', 'e.id', '=', 'a.espid')
            ->leftJoin('procedimiento as pr', 'pr.id', '=', 'a.id_procedimiento')
            ->leftJoin('empresa as em', 'em.id', '=', 'a.id_empresa')
            ->where('a.id_empresa', $companyId)
            ->where('a.adelantado', '1')
            ->where('a.estado_cita', '<>', 3)
            ->where(function ($q) {
                $q->where('a.proc_consul', '<', 2)->orWhere('a.omni', 'OM');
            })
            ->select(array(
                'a.*', 'p.nombre1 as paciente_nombre1', 'p.nombre2 as paciente_nombre2',
                'p.apellido1 as paciente_apellido1', 'p.apellido2 as paciente_apellido2',
                'p.fecha_nacimiento', 'p.alergias', 'p.antecedentes_pat', 'p.antecedentes_fam',
                'p.antecedentes_quir', 'p.direccion', 'p.telefono1', 'p.telefono2',
                's.nombre as seguro_nombre', 'sg.nombre as seguro_gestion_nombre',
                'd.nombre1 as doctor_nombre1', 'd.apellido1 as doctor_apellido1', 'd.apellido2 as doctor_apellido2',
                'um.nombre1 as responsable_nombre1', 'um.apellido1 as responsable_apellido1',
                'sa.nombre_sala as sala_nombre', 'h.nombre_hospital as hospital_nombre',
                'e.nombre as especialidad_nombre', 'pr.nombre as procedimiento_nombre',
                'pr.observacion as procedimiento_observacion', 'em.nombrecomercial as empresa_nombre',
                'em.nombre_corto as empresa_corta'
            ));
    }

    private function applyFilters($query, array $filters)
    {
        if ($filters['tipo'] !== '2') $query->where('a.proc_consul', $filters['tipo']);
        if ($filters['cedula'] !== '') $query->where('a.id_paciente', 'like', '%'.$this->escapeLike($filters['cedula']).'%');
        if ($filters['seguro'] !== '') $query->where('a.id_seguro', $filters['seguro']);
        if ($filters['doctor'] !== '') $query->where('a.id_doctor1', $filters['doctor']);
        if ($filters['especialidad'] !== '') $query->where('a.espid', $filters['especialidad']);
        if ($filters['procedimiento'] !== '') {
            $procedure = $filters['procedimiento'];
            $query->where(function ($q) use ($procedure) {
                $q->where('a.id_procedimiento', $procedure)
                    ->orWhereExists(function ($sub) use ($procedure) {
                        $sub->select(DB::raw(1))->from('agenda_procedimiento as ap')
                            ->whereRaw('ap.id_agenda = a.id')->where('ap.id_procedimiento', $procedure);
                    });
            });
        }
        if ($filters['paciente'] !== '') {
            foreach (preg_split('/\s+/', $filters['paciente']) as $term) {
                if ($term === '') continue;
                $like = '%'.$this->escapeLike($term).'%';
                $query->where(function ($q) use ($like) {
                    $q->where('p.nombre1', 'like', $like)->orWhere('p.nombre2', 'like', $like)
                        ->orWhere('p.apellido1', 'like', $like)->orWhere('p.apellido2', 'like', $like);
                });
            }
        }
    }

    private function attachProcedures($rows)
    {
        $ids = $rows->pluck('id')->filter()->all();
        if (!$ids) return;
        $extra = DB::table('agenda_procedimiento as ap')->join('procedimiento as p', 'p.id', '=', 'ap.id_procedimiento')
            ->whereIn('ap.id_agenda', $ids)->select('ap.id_agenda', 'p.nombre', 'p.observacion')->get()->groupBy('id_agenda');
        foreach ($rows as $row) $row->additional_procedures = $extra->get($row->id, collect());
    }

    private function date($value, $default)
    {
        $value = str_replace('/', '-', trim((string) $value));
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : $default;
    }

    private function escapeLike($value)
    {
        return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $value);
    }
}
