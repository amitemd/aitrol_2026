<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Sis_medico\Examen_Orden;

/** Read the same catalogue used by examination administration. */
class HospitalLaboratoryOrderCatalog
{
    public function data(Examen_Orden $order, array $filters = [])
    {
        $selected = DB::table('examen_detalle')->where('id_examen_orden', $order->id)->pluck('id_examen')->unique()->all();
        $query = DB::table('examen as e')->leftJoin('examen_agrupador as g', 'g.id', '=', 'e.id_agrupador')
            ->where(function ($query) use ($selected) {
                $query->where(function ($active) {
                    $active->where('e.estado', 1)->where(function ($group) { $group->where('g.estado', 1)->orWhereNull('g.id'); });
                })->orWhereIn('e.id', $selected);
            });
        if (!empty($filters['seleccionados'])) $query->whereIn('e.id', $selected);
        $search = trim((string) ($filters['buscador'] ?? ''));
        if ($search !== '') {
            $term = '%'.Str::lower(Str::ascii($search)).'%';
            $query->where(function ($query) use ($term, $search) {
                $query->whereRaw($this->normalized('e.nombre').' like ?', [$term])
                    ->orWhereRaw($this->normalized('e.descripcion').' like ?', [$term]);
                if (ctype_digit($search)) $query->orWhere('e.id', (int) $search);
            });
        }
        $group = trim((string) ($filters['buscador2'] ?? ''));
        if ($group !== '') $query->whereRaw($this->normalized('g.nombre').' like ?', ['%'.Str::lower(Str::ascii($group)).'%']);
        $exams = $query->select('e.id as ex_id', 'e.nombre', 'e.descripcion', 'e.valor', 'e.estado', 'e.id_agrupador', 'g.nombre as lnombre', 'g.estado as group_active')
            ->orderBy('g.nombre')->orderBy('e.descripcion')->orderBy('e.nombre')->get();
        return ['orden' => $order, 'examenes_labs' => $exams, 'detalles_ch' => $selected];
    }

    private function normalized($column)
    {
        foreach (['Á'=>'A', 'É'=>'E', 'Í'=>'I', 'Ó'=>'O', 'Ú'=>'U', 'Ü'=>'U', 'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u', 'ü'=>'u'] as $from=>$to) {
            $column = "replace($column, '$from', '$to')";
        }
        return 'lower('.$column.')';
    }
}
