<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class PatientLibraryService
{
    public function patients($companyId, string $search)
    {
        abort_if(!$companyId, 403);
        $query = DB::table('paciente as p')->where('p.id', '<>', '9999999999');
        app(PatientTenantService::class)->constrain($query, 'p.id', $companyId);
        foreach (preg_split('/\s+/u', $search, -1, PREG_SPLIT_NO_EMPTY) as $term) {
            $query->where(function ($part) use ($term) {
                $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term).'%';
                foreach (['p.id', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2'] as $column) {
                    $part->orWhere($column, 'like', $like);
                }
            });
        }
        $patients = $query->select('p.id', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2')
            ->orderBy('p.apellido1')->orderBy('p.apellido2')->orderBy('p.nombre1')->orderBy('p.id')->paginate(24);
        $counts = Schema::hasTable('patient_library_entries') ? DB::table('patient_library_entries')
            ->where('company_id', $companyId)->whereIn('patient_id', $patients->pluck('id'))
            ->select('patient_id', DB::raw('COUNT(*) as total'))->groupBy('patient_id')->pluck('total', 'patient_id') : collect();
        foreach ($patients as $patient) {
            $patient->display_name = $this->name($patient);
            $patient->library_count = $counts->get($patient->id, 0);
        }
        return $patients;
    }

    public function patient($companyId, $patientId)
    {
        app(PatientTenantService::class)->requireAccessible($patientId, $companyId);
        $patient = DB::table('paciente')->where('id', $patientId)->first();
        abort_unless($patient, 404);
        $patient->display_name = $this->name($patient);
        return $patient;
    }

    public function entries($companyId, $patientId)
    {
        return DB::table('patient_library_entries')->where('company_id', $companyId)->where('patient_id', $patientId);
    }

    public function clinicalDocuments($companyId, $patientId)
    {
        return DB::table('clinical_documents as d')->join('clinical_encounters as e', 'e.id', '=', 'd.encounter_id')
            ->where('e.id_empresa', $companyId)->where('e.id_paciente', $patientId);
    }

    // All sources are scoped before UNION and paginated after sorting the complete chronology.
    public function timeline($companyId, $patientId, string $order, string $kind)
    {
        $sources = [];
        if (Schema::hasTable('patient_library_entries')) {
            $sources[] = $this->entries($companyId, $patientId)->select('id as source_id', 'occurred_at', 'title', 'notes as summary')
                ->selectRaw("'entry' as kind, file_name");
        }
        if (Schema::hasTable('clinical_documents') && Schema::hasTable('clinical_encounters')) {
            $sources[] = $this->clinicalDocuments($companyId, $patientId)
                ->selectRaw("d.id as source_id, COALESCE(d.authored_at, d.created_at) as occurred_at, d.title, d.document_type as summary, 'clinical' as kind, NULL as file_name");
        }
        if (Schema::hasTable('historiaclinica') && Schema::hasTable('agenda')) {
            $sources[] = DB::table('historiaclinica as h')->join('agenda as a', 'a.id', '=', 'h.id_agenda')
                ->where('a.id_empresa', $companyId)->where('h.id_paciente', $patientId)
                ->selectRaw("h.hcid as source_id, COALESCE(a.fechaini, h.created_at) as occurred_at, 'Historia clínica' as title, 'Atención ambulatoria y procedimientos' as summary, 'history' as kind, NULL as file_name");
        }
        if (Schema::hasTable('agenda_archivo') && Schema::hasTable('agenda')) {
            $sources[] = DB::table('agenda_archivo as f')->join('agenda as a', 'a.id', '=', 'f.id_agenda')
                ->where('a.id_empresa', $companyId)->where('a.id_paciente', $patientId)
                ->selectRaw("f.id as source_id, f.created_at as occurred_at, COALESCE(f.descripcion, 'Documento de agenda') as title, f.texto as summary, 'agenda_file' as kind, NULL as file_name");
        }
        if (Schema::hasTable('paciente_biopsia') && Schema::hasColumn('paciente_biopsia', 'id_empresa')) {
            $sources[] = DB::table('paciente_biopsia')->where('id_empresa', $companyId)->where('id_paciente', $patientId)
                ->where('eliminado', 0)->whereIn('estado', [0, 1])
                ->selectRaw("id as source_id, created_at as occurred_at, CASE WHEN estado = 0 THEN 'Biopsia' ELSE 'Resultado externo' END as title, NULL as summary, 'external' as kind, NULL as file_name");
        }
        if (Schema::hasTable('clinical_encounters')) {
            $sources[] = DB::table('clinical_encounters')->where('id_empresa', $companyId)->where('id_paciente', $patientId)
                ->selectRaw("id as source_id, started_at as occurred_at, 'Episodio de atención' as title, care_setting as summary, 'encounter' as kind, NULL as file_name");
        }
        if (Schema::hasTable('clinical_orders') && Schema::hasTable('clinical_encounters')) {
            $sources[] = DB::table('clinical_orders as o')->join('clinical_encounters as e', 'e.id', '=', 'o.encounter_id')
                ->where('e.id_empresa', $companyId)->where('e.id_paciente', $patientId)->where('o.patient_id', $patientId)
                ->selectRaw("o.id as source_id, COALESCE(o.ordered_at, o.created_at) as occurred_at, 'Orden clínica' as title, o.description as summary, 'order' as kind, NULL as file_name");
        }
        if (Schema::hasTable('examen_orden') && Schema::hasColumn('examen_orden', 'id_empresa')) {
            $sources[] = DB::table('examen_orden')->where('id_empresa', $companyId)->where('id_paciente', $patientId)
                ->selectRaw("id as source_id, COALESCE(fecha_orden, created_at) as occurred_at, 'Orden de laboratorio' as title, NULL as summary, 'laboratory' as kind, NULL as file_name");
        }
        if (!$sources) {
            $sources[] = DB::query()->selectRaw("NULL as source_id, NULL as occurred_at, NULL as title, NULL as summary, NULL as kind, NULL as file_name")->whereRaw('1 = 0');
        }
        $union = array_shift($sources);
        foreach ($sources as $source) $union->unionAll($source);
        $query = DB::query()->fromSub($union, 'chronology');
        if ($kind !== '') $query->where('kind', $kind);
        return $query->orderBy('occurred_at', $order === 'asc' ? 'asc' : 'desc')->orderBy('kind')->orderBy('source_id')->paginate(20);
    }

    public function name($patient): string
    {
        return implode(' ', array_filter([$patient->apellido1, $patient->apellido2, $patient->nombre1, $patient->nombre2], function ($part) {
            return trim((string) $part) !== '' && !in_array(trim($part), ['N/A', '(N/A)']);
        }));
    }
}
