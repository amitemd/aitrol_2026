<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class V3LaboratoryOrderService
{
    public function filters(Request $request)
    {
        $today = date('Y-m-d');
        return array(
            'desde' => $this->date($request->input('desde', $request->input('fecha')), $today),
            'hasta' => $this->date($request->input('hasta', $request->input('fecha_hasta')), $today),
            'paciente' => trim((string) $request->input('paciente', $request->input('nombres'))),
            'paciente_id' => trim((string) $request->input('paciente_id')),
            'seguro' => trim((string) $request->input('seguro')),
            'facturacion' => strtoupper(trim((string) $request->input('facturacion', $request->input('facturadas', 'TODAS')))),
        );
    }

    public function paginate(array $filters, $companyId, $perPage = 40)
    {
        return $this->query($filters, $companyId)->orderBy('eo.fecha_orden', 'desc')->orderBy('eo.id', 'desc')
            ->paginate($perPage)->appends($filters);
    }

    public function all(array $filters, $companyId)
    {
        return $this->query($filters, $companyId)->orderBy('eo.fecha_orden', 'desc')->orderBy('eo.id', 'desc')->get();
    }

    public function insurances()
    {
        return \Sis_medico\Seguro::query()->where('inactivo', '1')->orderBy('nombre')->select('id', 'nombre')->get();
    }

    public function onlinePaymentsPending($companyId)
    {
        return DB::table('examen_orden')->where('id_empresa', $companyId)->where('pago_online', '1')
            ->where('estado', '1')->whereNull('fecha_orden')->where('gestion', '0')->where('estado_pago', '1')->count();
    }

    private function query(array $filters, $companyId)
    {
        $query = DB::table('examen_orden as eo')
            ->leftJoin('paciente as p', 'p.id', '=', 'eo.id_paciente')
            ->leftJoin('seguros as s', 's.id', '=', 'eo.id_seguro')
            ->leftJoin('empresa as em', 'em.id', '=', 'eo.id_empresa')
            ->leftJoin('users as cu', 'cu.id', '=', 'eo.id_usuariocrea')
            ->leftJoin('users as mu', 'mu.id', '=', 'eo.id_usuariomod')
            ->where('eo.id_empresa', $companyId)->where('eo.estado', '<>', '0')
            ->whereBetween('eo.fecha_orden', array($filters['desde'].' 00:00:00', $filters['hasta'].' 23:59:59'))
            ->select('eo.*', 'p.nombre1 as patient_name1', 'p.nombre2 as patient_name2',
                'p.apellido1 as patient_lastname1', 'p.apellido2 as patient_lastname2', 's.nombre as insurance_name',
                's.tipo as insurance_type', 'em.nombre_corto as company_shortname', 'cu.nombre1 as creator_name',
                'cu.apellido1 as creator_lastname', 'mu.nombre1 as editor_name', 'mu.apellido1 as editor_lastname')
            ->selectRaw('(SELECT COUNT(*) FROM examen_detalle ed WHERE ed.id_examen_orden = eo.id) as exam_count')
            ->selectRaw('(SELECT COUNT(*) FROM examen_resultado er WHERE er.id_orden = eo.id) as result_count');

        if ($filters['seguro'] !== '') $query->where('eo.id_seguro', $filters['seguro']);
        if ($filters['paciente_id'] !== '') $query->where('eo.id_paciente', $filters['paciente_id']);
        if ($filters['facturacion'] === 'FACTURADAS') $query->whereNotNull('eo.fecha_envio');
        if ($filters['facturacion'] === 'NO FACTURADAS') $query->whereNull('eo.fecha_envio');
        if ($filters['paciente'] !== '') {
            $terms = preg_split('/\s+/', $filters['paciente']);
            $query->where(function ($nested) use ($terms) {
                foreach ($terms as $term) {
                    $like = '%'.str_replace(array('%', '_'), array('\\%', '\\_'), $term).'%';
                    $nested->whereRaw("CONCAT_WS(' ', p.apellido1, p.apellido2, p.nombre1, p.nombre2) LIKE ?", array($like));
                }
            });
        }
        return $query;
    }

    private function date($value, $default)
    {
        $value = trim((string) $value);
        $value = str_replace('/', '-', $value);
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : $default;
    }
}
