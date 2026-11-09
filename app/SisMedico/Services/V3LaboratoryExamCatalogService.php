<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Examen;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Parametro;

class V3LaboratoryExamCatalogService
{
    public function catalog($search, $perPage = 30)
    {
        $query = DB::table('examen as e')
            ->leftJoin('examen_agrupador as ea', 'ea.id', '=', 'e.id_agrupador')
            ->select('e.*', 'ea.nombre as agrupador_nombre');

        if ($search !== '') {
            $query->where(function ($nested) use ($search) {
                $nested->where('e.nombre', 'like', '%'.$search.'%')
                    ->orWhere('e.descripcion', 'like', '%'.$search.'%')
                    ->orWhere('e.tarifario', 'like', '%'.$search.'%');
            });
        }

        $exams = $query->orderBy('e.descripcion')->paginate($perPage);
        $ids = array_map(function ($exam) { return $exam->id; }, $exams->items());
        $prices = array();
        if (count($ids)) {
            foreach (DB::table('examen_nivel')->whereIn('id_examen', $ids)->get() as $price) {
                $prices[$price->id_examen][$price->nivel] = $price->valor1;
            }
        }

        return array($exams, $prices);
    }

    public function saveExam(array $data, $id = null)
    {
        return DB::transaction(function () use ($data, $id) {
            $exam = $id ? Examen::findOrFail($id) : new Examen;
            $exam->fill($data['exam']);
            $exam->save();

            foreach ($data['levels'] as $levelId => $value) {
                $price = Examen_Nivel::where('id_examen', $exam->id)->where('nivel', $levelId)->first();
                if (!$price) {
                    $price = new Examen_Nivel;
                    $price->id_examen = $exam->id;
                    $price->nivel = $levelId;
                    $price->id_usuariocrea = $data['audit']['user'];
                    $price->ip_creacion = $data['audit']['ip'];
                }
                $price->valor1 = $value;
                $price->id_usuariomod = $data['audit']['user'];
                $price->ip_modificacion = $data['audit']['ip'];
                $price->save();
            }

            return $exam;
        });
    }

    public function saveParameter($examId, array $values, $parameterId = null)
    {
        return DB::transaction(function () use ($examId, $values, $parameterId) {
            $parameter = $parameterId
                ? Examen_Parametro::where('id_examen', $examId)->where('id', $parameterId)->firstOrFail()
                : new Examen_Parametro;
            $parameter->fill($values);
            $parameter->id_examen = $examId;
            $parameter->save();
            return $parameter;
        });
    }
}
