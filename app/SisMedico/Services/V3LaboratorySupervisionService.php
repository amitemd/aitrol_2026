<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class V3LaboratorySupervisionService
{
    public function filters(Request $request)
    {
        $today = date('Y-m-d');
        return array(
            'fecha' => $this->date($request->input('fecha'), $today),
            'fecha_hasta' => $this->date($request->input('fecha_hasta'), $today),
            'nombres' => trim((string) $request->input('nombres')),
            'seguro' => trim((string) $request->input('seguro')),
        );
    }

    public function paginate(array $filters, $companyId)
    {
        return $this->query($filters, $companyId)->orderBy('eo.fecha_orden', 'desc')->orderBy('eo.id', 'desc')->paginate(30)->appends($filters);
    }

    public function all(array $filters, $companyId)
    {
        return $this->query($filters, $companyId)->orderBy('eo.fecha_orden', 'desc')->get();
    }

    public function insurances()
    {
        return \Sis_medico\Seguro::query()->where('inactivo', 1)->orderBy('nombre')->select('id', 'nombre')->get();
    }

    public function summary($rows)
    {
        $result = array('orders' => 0, 'exams' => 0, 'total' => 0, 'pending' => 0);
        foreach ($rows as $row) {
            $result['orders']++;
            $result['exams'] += (int) $row->exam_count;
            $result['total'] += (float) $row->total_valor;
            if ((int) $row->parameter_count > (int) $row->certified_count) $result['pending']++;
        }
        return $result;
    }

    private function query(array $filters, $companyId)
    {
        $query = DB::table('examen_orden as eo')
            ->leftJoin('paciente as p', 'p.id', '=', 'eo.id_paciente')
            ->leftJoin('seguros as s', 's.id', '=', 'eo.id_seguro')
            ->leftJoin('empresa as em', 'em.id', '=', 'eo.id_empresa')
            ->leftJoin('protocolo as proto', 'proto.id', '=', 'eo.id_protocolo')
            ->leftJoin('users as mu', 'mu.id', '=', 'eo.id_usuariomod')
            ->where('eo.id_empresa', $companyId)->where('eo.estado', 1)
            ->whereBetween('eo.fecha_orden', array($filters['fecha'].' 00:00:00', $filters['fecha_hasta'].' 23:59:59'))
            ->select('eo.*', 'p.id as patient_id', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2',
                's.nombre as insurance_name', 's.tipo as insurance_type', 'em.nombre_corto as company_shortname',
                'proto.pre_post', 'mu.nombre1 as editor_name', 'mu.apellido1 as editor_lastname')
            ->selectRaw('(SELECT COUNT(*) FROM examen_detalle ed WHERE ed.id_examen_orden=eo.id) as exam_count')
            ->selectRaw('(SELECT COUNT(*) FROM examen_detalle ed JOIN examen_parametro ep ON ep.id_examen=ed.id_examen WHERE ed.id_examen_orden=eo.id AND ep.estado=1) as parameter_count')
            ->selectRaw("(SELECT COUNT(*) FROM examen_resultado er WHERE er.id_orden=eo.id AND er.certificado='1') as certified_count")
            ->selectRaw('(SELECT GROUP_CONCAT(DISTINCT e.descripcion ORDER BY e.descripcion SEPARATOR "; ") FROM examen_detalle ed JOIN examen e ON e.id=ed.id_examen WHERE ed.id_examen_orden=eo.id) as exam_names');
        if ($filters['seguro'] !== '') $query->where('eo.id_seguro', $filters['seguro']);
        if ($filters['nombres'] !== '') {
            foreach (preg_split('/\s+/', $filters['nombres']) as $term) {
                $query->whereRaw("CONCAT_WS(' ',p.apellido1,p.apellido2,p.nombre1,p.nombre2) LIKE ?", array('%'.str_replace(array('%','_'), array('\\%','\\_'), $term).'%'));
            }
        }
        return $query;
    }

    private function date($value, $default)
    {
        $value = str_replace('/', '-', trim((string) $value));
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : $default;
    }
}
