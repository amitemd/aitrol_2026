<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class V3LaboratoryExamCostService
{
    public function listing($companyId, $search = '', $perPage = 30)
    {
        $query = DB::table('examen as e')
            ->leftJoin('examen_agrupador as ea', 'ea.id', '=', 'e.id_agrupador');

        if (Schema::hasTable('v3_laboratory_exam_costs')) {
            $query->leftJoin('v3_laboratory_exam_costs as ec', function ($join) use ($companyId) {
                $join->on('ec.id_examen', '=', 'e.id')->where('ec.id_empresa', '=', $companyId);
            })->select('e.id', 'e.nombre', 'e.descripcion', 'e.valor', 'e.estado', 'ea.nombre as agrupador_nombre',
                DB::raw('COALESCE(ec.valor_reactivo, 0) as valor_reactivo'),
                DB::raw('COALESCE(ec.valor_implementos, 0) as valor_implementos'));
        } else {
            $query->select('e.id', 'e.nombre', 'e.descripcion', 'e.valor', 'e.estado', 'ea.nombre as agrupador_nombre',
                'e.valor_reactivo', 'e.valor_implementos');
        }

        $query->where('e.estado', 1);
        if ($search !== '') {
            $query->where(function ($nested) use ($search) {
                $nested->where('e.nombre', 'like', '%'.$search.'%')
                    ->orWhere('e.descripcion', 'like', '%'.$search.'%')
                    ->orWhere('ea.nombre', 'like', '%'.$search.'%');
            });
        }

        $exams = $query->orderBy('e.descripcion')->paginate($perPage);
        return array($exams, $this->prices(array_map(function ($exam) { return $exam->id; }, $exams->items())));
    }

    public function all($companyId, $search = '')
    {
        list($page) = $this->listing($companyId, $search, 100000);
        return array($page->items(), $this->prices(array_map(function ($exam) { return $exam->id; }, $page->items())));
    }

    public function find($companyId, $examId)
    {
        $query = DB::table('examen as e')->where('e.id', $examId)->where('e.estado', 1);
        if (Schema::hasTable('v3_laboratory_exam_costs')) {
            $query->leftJoin('v3_laboratory_exam_costs as ec', function ($join) use ($companyId) {
                $join->on('ec.id_examen', '=', 'e.id')->where('ec.id_empresa', '=', $companyId);
            })->select('e.*', DB::raw('COALESCE(ec.valor_reactivo, 0) as costo_reactivo'), DB::raw('COALESCE(ec.valor_implementos, 0) as costo_implementos'));
        } else {
            $query->select('e.*', 'e.valor_reactivo as costo_reactivo', 'e.valor_implementos as costo_implementos');
        }
        return $query->first();
    }

    public function save($companyId, $examId, $reagent, $supplies, $userId, $ip)
    {
        DB::transaction(function () use ($companyId, $examId, $reagent, $supplies, $userId, $ip) {
            DB::table('examen')->where('id', $examId)->where('estado', 1)->lockForUpdate()->first();
            $scope = DB::table('v3_laboratory_exam_costs')->where('id_empresa', $companyId)->where('id_examen', $examId);
            $values = array('valor_reactivo' => $reagent, 'valor_implementos' => $supplies,
                'id_usuario_modificacion' => $userId, 'ip_modificacion' => $ip, 'updated_at' => date('Y-m-d H:i:s'));
            if ($scope->exists()) {
                $scope->update($values);
            } else {
                $values = array_merge($values, array('id_empresa' => $companyId, 'id_examen' => $examId,
                    'id_usuario_creacion' => $userId, 'ip_creacion' => $ip, 'created_at' => date('Y-m-d H:i:s')));
                DB::table('v3_laboratory_exam_costs')->insert($values);
            }
        });
    }

    private function prices(array $examIds)
    {
        $prices = array();
        if (empty($examIds)) return $prices;
        foreach (DB::table('examen_nivel')->whereIn('id_examen', $examIds)->get() as $price) {
            $prices[$price->id_examen][$price->nivel] = (float) $price->valor1;
        }
        return $prices;
    }
}
