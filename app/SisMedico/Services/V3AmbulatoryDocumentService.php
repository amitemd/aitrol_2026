<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class V3AmbulatoryDocumentService
{
    public function patients($term, $companyId)
    {
        $term = trim((string) $term);
        $query = DB::table('paciente as p')
            ->join('agenda as a', 'a.id_paciente', '=', 'p.id')
            ->join('historiaclinica as hc', 'hc.id_agenda', '=', 'a.id')
            ->where('a.id_empresa', $companyId)
            ->where(function ($care) {
                $care->where('a.proc_consul', '<', 2)->orWhere('a.omni', 'OM');
            });

        foreach (preg_split('/\s+/', $term) as $part) {
            if ($part === '') {
                continue;
            }
            $like = '%'.$this->escapeLike($part).'%';
            $query->where(function ($patient) use ($like) {
                $patient->where('p.id', 'like', $like)
                    ->orWhere('p.nombre1', 'like', $like)->orWhere('p.nombre2', 'like', $like)
                    ->orWhere('p.apellido1', 'like', $like)->orWhere('p.apellido2', 'like', $like);
            });
        }

        return $query->select(
            'p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento',
            DB::raw('COUNT(DISTINCT a.id) as consultation_count'),
            DB::raw('MAX(a.fechaini) as last_attention')
        )
            ->groupBy('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento')
            ->orderByDesc('last_attention')->orderBy('p.id')->paginate(20)->withQueryString();
    }

    public function patient($patientId, $companyId)
    {
        $patient = DB::table('paciente as p')->where('p.id', $patientId)
            ->whereExists(function ($query) use ($companyId) {
                $query->select(DB::raw(1))->from('agenda as a')
                    ->whereRaw('a.id_paciente = p.id')->where('a.id_empresa', $companyId)
                    ->where(function ($care) {
                        $care->where('a.proc_consul', '<', 2)->orWhere('a.omni', 'OM');
                    });
            })->first();
        if (! $patient) {
            abort(404, 'El paciente no tiene atenciones ambulatorias en la empresa activa.');
        }

        return $patient;
    }

    public function consultations($patientId, $companyId)
    {
        $rows = DB::table('agenda as a')->join('historiaclinica as hc', 'hc.id_agenda', '=', 'a.id')
            ->leftJoin('users as d', 'd.id', '=', 'a.id_doctor1')
            ->leftJoin('especialidad as e', 'e.id', '=', 'a.espid')
            ->leftJoin('procedimiento as pr', 'pr.id', '=', 'a.id_procedimiento')
            ->where('a.id_empresa', $companyId)->where('a.id_paciente', $patientId)
            ->where(function ($care) {
                $care->where('a.proc_consul', '<', 2)->orWhere('a.omni', 'OM');
            })
            ->select('a.id', 'a.fechaini', 'a.proc_consul', 'a.estado_cita', 'a.id_paciente',
                'hc.hcid as history_id', 'e.nombre as specialty_name', 'pr.nombre as procedure_name',
                'd.nombre1 as doctor_name', 'd.apellido1 as doctor_lastname', 'd.apellido2 as doctor_lastname2')
            ->orderByDesc('a.fechaini')->orderByDesc('a.id')->paginate(20);

        return $rows;
    }

    public function consultation($appointmentId, $companyId)
    {
        $row = DB::table('agenda as a')->leftJoin('historiaclinica as hc', 'hc.id_agenda', '=', 'a.id')
            ->join('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->leftJoin('users as d', 'd.id', '=', 'a.id_doctor1')
            ->leftJoin('especialidad as e', 'e.id', '=', 'a.espid')
            ->leftJoin('procedimiento as pr', 'pr.id', '=', 'a.id_procedimiento')
            ->leftJoin('seguros as s', 's.id', '=', 'a.id_seguro')
            ->where('a.id_empresa', $companyId)->where('a.id', $appointmentId)
            ->where(function ($care) {
                $care->where('a.proc_consul', '<', 2)->orWhere('a.omni', 'OM');
            })
            ->select('a.*', 'hc.hcid as history_id', 'e.nombre as specialty_name', 'pr.nombre as procedure_name',
                's.nombre as insurance_name', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento',
                'd.nombre1 as doctor_name', 'd.apellido1 as doctor_lastname', 'd.apellido2 as doctor_lastname2')
            ->first();
        if (! $row) {
            abort(404, 'La consulta no existe para la empresa activa.');
        }

        return $row;
    }

    public function documents($consultation)
    {
        $documents = [];
        $historyId = $consultation->history_id;

        if (Schema::hasTable('ho_solicitud')) {
            $requests = DB::table('ho_solicitud')->where('id_agenda', $consultation->id)
                ->where('id_paciente', $consultation->id_paciente)->orderByDesc('id')->get();
            foreach ($requests as $request) {
                $route = Route::has('hospital.nuevo.formulario008_pdf') ? 'hospital.nuevo.formulario008_pdf' : 'hospital.formulario008_pdf';
                $documents[] = $this->document('008', 'Emergencia', route($route, ['id' => $request->id]), null, $request->created_at ?? null);
            }
        }

        if (! $historyId) {
            return $documents;
        }

        if (Schema::hasTable('anamnesis')) {
            $query = DB::table('anamnesis')->where('hcid', $historyId);
            if (Schema::hasColumn('anamnesis', 'estado')) {
                $query->where('estado', 1);
            }
            foreach ($query->orderByDesc('id')->get() as $record) {
                $documents[] = $this->document('002', 'Consulta / anamnesis', route('formulario.002', $record->id), route('formato.anamenesis_index', $record->id), $record->created_at ?? null);
            }
        }

        if (Schema::hasTable('interconsulta')) {
            $query = DB::table('interconsulta')->where('id_hc', $historyId);
            if (Schema::hasColumn('interconsulta', 'estado')) {
                $query->where('estado', 1);
            }
            foreach ($query->orderByDesc('id')->get() as $record) {
                $documents[] = $this->document('007', 'Interconsulta - solicitud', route('formulario.007', $record->id), null, $record->created_at ?? null);
                if (Route::has('informe.007')) {
                    $documents[] = $this->document('007B', 'Interconsulta - informe', route('informe.007', $record->id), null, $record->updated_at ?? null);
                }
            }
        }

        if (Schema::hasTable('hc_protocolo')) {
            $query = DB::table('hc_protocolo')->where('hcid', $historyId);
            if (Schema::hasColumn('hc_protocolo', 'estado')) {
                $query->where('estado', 1);
            }
            foreach ($query->orderByDesc('id')->get() as $record) {
                $documents[] = $this->document('017', 'Protocolo quirúrgico', route('formulario.017', $record->id), null, $record->created_at ?? null);
            }
        }

        if (Schema::hasTable('hc_procedimientos') && Schema::hasTable('hc_anestesiologia')) {
            $procedures = DB::table('hc_procedimientos as hp')
                ->join('hc_anestesiologia as ha', 'ha.id_hc_procedimientos', '=', 'hp.id')
                ->where('hp.id_hc', $historyId)->select('hp.id as procedure_id', 'ha.id as anesthesia_id', 'ha.created_at')->get();
            foreach ($procedures as $procedure) {
                $preId = Schema::hasTable('rc_pre_anestesica')
                    ? DB::table('rc_pre_anestesica')->where('id_hc_anestesiologia', $procedure->anesthesia_id)->orderByDesc('id')->value('id') : null;
                if ($preId) {
                    $documents[] = $this->document('018', 'Valoración preanestésica', route('formulario.018', $preId), route('formato.rc_preanestesico_edit', $procedure->anesthesia_id), $procedure->created_at);
                }
                $documents[] = $this->document('018A', 'Registro transanestésico', route('formulario.018A', $procedure->procedure_id), route('formato.transanestesico_edit', $procedure->anesthesia_id), $procedure->created_at);
                if (Schema::hasTable('hc_evolucion_anestesiologo') && DB::table('hc_evolucion_anestesiologo')->where('hc_id_procedimiento', $procedure->procedure_id)->exists()) {
                    $documents[] = $this->document('019', 'Cuidados postanestésicos', route('formato.pdf_019', $procedure->procedure_id), null, $procedure->created_at);
                }
            }
        }

        return $documents;
    }

    private function document($code, $title, $url, $editUrl = null, $date = null)
    {
        return (object) compact('code', 'title', 'url', 'editUrl', 'date');
    }

    private function escapeLike($value)
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], (string) $value);
    }
}
