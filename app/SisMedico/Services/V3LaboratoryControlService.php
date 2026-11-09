<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class V3LaboratoryControlService
{
    public function filters(Request $request)
    {
        $today = date('Y-m-d');
        return array('fecha' => $this->date($request->input('fecha'), $today), 'fecha_hasta' => $this->date($request->input('fecha_hasta'), $today),
            'nombres' => trim((string) $request->input('nombres')), 'seguro' => trim((string) $request->input('seguro')));
    }

    public function paginate(array $filters, $companyId)
    {
        return $this->query($filters, $companyId)->orderBy('eo.fecha_orden')->orderBy('eo.id')->paginate(30)->appends($filters);
    }

    public function all(array $filters, $companyId) { return $this->query($filters, $companyId)->orderBy('eo.fecha_orden')->get(); }

    public function insurances() { return \Sis_medico\Seguro::query()->where('inactivo', 1)->orderBy('nombre')->select('id', 'nombre')->get(); }

    public function summary($rows)
    {
        $summary = array('orders' => 0, 'pending' => 0, 'certify' => 0, 'ready' => 0);
        foreach ($rows as $row) {
            $summary['orders']++;
            $states = array();
            if ((int) $row->hematology_count > 0) $states[] = (int) $row->er_biometria;
            if ((int) $row->biochemistry_count > 0) $states[] = (int) $row->er_bioquimica;
            if ((int) $row->special_count > 0) $states[] = (int) $row->er_manual;
            if (in_array(0, $states, true)) $summary['pending']++;
            if (in_array(1, $states, true)) $summary['certify']++;
            if (((int) $row->parameter_count === 0 || (int) $row->certified_count >= (int) $row->parameter_count) && (int) $row->exam_count > 0) $summary['ready']++;
        }
        return $summary;
    }

    public function covid(array $filters, $companyId)
    {
        $query = DB::table('examen_resultado as er')->join('examen_parametro as ep', 'ep.id', '=', 'er.id_parametro')
            ->join('examen as e', 'e.id', '=', 'ep.id_examen')->join('examen_orden as eo', 'eo.id', '=', 'er.id_orden')
            ->leftJoin('paciente as p', 'p.id', '=', 'eo.id_paciente')->leftJoin('seguros as s', 's.id', '=', 'eo.id_seguro')
            ->where('eo.id_empresa', $companyId)->where('eo.estado', 1)->where('e.nombre', 'like', '%COVID%')
            ->whereBetween('er.updated_at', array($filters['fecha'].' 00:00:00', $filters['fecha_hasta'].' 23:59:59'));
        if ($filters['seguro'] !== '') $query->where('eo.id_seguro', $filters['seguro']);
        if ($filters['nombres'] !== '') {
            foreach (preg_split('/\s+/', $filters['nombres']) as $term) {
                $query->whereRaw("CONCAT_WS(' ',p.apellido1,p.apellido2,p.nombre1,p.nombre2) LIKE ?", array('%'.str_replace(array('%','_'), array('\\%','\\_'), $term).'%'));
            }
        }
        return $query->select('eo.id', 'eo.fecha_orden', 'p.id as patient_id', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 's.nombre as insurance_name', 'e.nombre as exam_name', 'ep.nombre as parameter_name', 'er.valor', 'er.certificado')->orderBy('eo.id')->get();
    }

    private function query(array $filters, $companyId)
    {
        $query = DB::table('examen_orden as eo')->leftJoin('paciente as p', 'p.id', '=', 'eo.id_paciente')
            ->leftJoin('seguros as s', 's.id', '=', 'eo.id_seguro')->leftJoin('protocolo as proto', 'proto.id', '=', 'eo.id_protocolo')
            ->leftJoin('users as cu', 'cu.id', '=', 'eo.id_usuariocrea')->where('eo.id_empresa', $companyId)->where('eo.estado', 1)
            ->whereBetween('eo.fecha_orden', array($filters['fecha'].' 00:00:00', $filters['fecha_hasta'].' 23:59:59'))
            ->select('eo.*', 'p.id as patient_id', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 's.nombre as insurance_name', 's.tipo as insurance_type', 'proto.pre_post', 'cu.nombre1 as creator_name', 'cu.apellido1 as creator_lastname')
            ->selectRaw('(SELECT COUNT(*) FROM examen_detalle ed WHERE ed.id_examen_orden = eo.id) as exam_count')
            ->selectRaw("(SELECT COUNT(*) FROM examen_detalle ed JOIN examen e ON e.id=ed.id_examen WHERE ed.id_examen_orden=eo.id AND e.maquina='1') as hematology_count")
            ->selectRaw("(SELECT COUNT(*) FROM examen_detalle ed JOIN examen e ON e.id=ed.id_examen WHERE ed.id_examen_orden=eo.id AND e.maquina='2') as biochemistry_count")
            ->selectRaw("(SELECT COUNT(*) FROM examen_detalle ed JOIN examen e ON e.id=ed.id_examen WHERE ed.id_examen_orden=eo.id AND e.maquina='0') as special_count")
            ->selectRaw('(SELECT COUNT(*) FROM examen_detalle ed JOIN examen_parametro ep ON ep.id_examen=ed.id_examen WHERE ed.id_examen_orden=eo.id AND ep.estado=1) as parameter_count')
            ->selectRaw("(SELECT COUNT(*) FROM examen_resultado er WHERE er.id_orden=eo.id AND er.certificado='1') as certified_count");
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
        $value = str_replace('/', '-', trim((string) $value)); $date = \DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : $default;
    }
}
