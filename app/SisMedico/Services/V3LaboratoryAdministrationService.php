<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Parametro_Referencia_Anterior;
use Sis_medico\Examen_Protocolo;
use Sis_medico\Labs_Factura_Agrupada_Cab;
use Sis_medico\Protocolo;

class V3LaboratoryAdministrationService
{
    public function protocols($search, $perPage = 30)
    {
        $query = DB::table('protocolo as p')->leftJoin('examen_protocolo as ep', function ($join) {
            $join->on('ep.id_protocolo', '=', 'p.id')->where('ep.estado', '=', 1);
        })->select('p.*', DB::raw('COUNT(ep.id) as exam_count'))->groupBy('p.id');
        if ($search !== '') $query->where('p.nombre', 'like', '%'.$search.'%');
        return $query->orderBy('p.nombre')->paginate($perPage);
    }

    public function saveProtocol(array $values, array $examIds, $id = null)
    {
        return DB::transaction(function () use ($values, $examIds, $id) {
            $protocol = $id ? Protocolo::findOrFail($id) : new Protocolo;
            $protocol->fill($values); $protocol->save();
            Examen_Protocolo::where('id_protocolo', $protocol->id)->delete();
            foreach (array_unique(array_map('intval', $examIds)) as $examId) {
                if (!$examId || !Examen::where('id', $examId)->where('estado', 1)->exists()) continue;
                Examen_Protocolo::create(array('id_protocolo'=>$protocol->id,'id_examen'=>$examId,'estado'=>1,'id_usuariocrea'=>$values['id_usuariomod'],'id_usuariomod'=>$values['id_usuariomod'],'ip_creacion'=>$values['ip_modificacion'],'ip_modificacion'=>$values['ip_modificacion']));
            }
            return $protocol;
        });
    }

    public function groupers($search, $perPage = 30)
    {
        $query = DB::table('examen_agrupador as g')->leftJoin('examen as e', function ($join) {
            $join->on('e.id_agrupador', '=', 'g.id')->where('e.estado', '=', 1);
        })->select('g.*', DB::raw('COUNT(e.id) as exam_count'))->where('g.estado', 1)->groupBy('g.id');
        if ($search !== '') $query->where('g.nombre', 'like', '%'.$search.'%');
        return $query->orderBy('g.nombre')->paginate($perPage);
    }

    public function saveGrouper(array $values, $id = null)
    {
        return DB::transaction(function () use ($values, $id) {
            $model = $id ? Examen_Agrupador::findOrFail($id) : new Examen_Agrupador;
            $model->fill($values); $model->save(); return $model;
        });
    }

    public function agenda($companyId, $date)
    {
        $specialties = \Sis_medico\Especialidad::query()->where('estado', 1)->where(function ($q) {
            $q->where('nombre', 'like', '%LABORATOR%')->orWhere('nombre', 'like', '%PATOLOG%');
        })->pluck('id')->toArray();
        $query = DB::table('agenda as a')->join('paciente as p','p.id','=','a.id_paciente')
            ->leftJoin('seguros as s','s.id','=','a.id_seguro')->leftJoin('procedimiento as pr','pr.id','=','a.id_procedimiento')
            ->select('a.id','a.fechaini','a.fechafin','a.estado','a.estado_cita','a.id_paciente','p.nombre1','p.nombre2','p.apellido1','p.apellido2','s.nombre as seguro','s.color','pr.nombre as procedimiento')
            ->where('a.estado','>',0)->whereDate('a.fechaini', $date);
        if (Schema::hasColumn('agenda','id_empresa')) $query->where('a.id_empresa', $companyId);
        if (!empty($specialties)) $query->whereIn('a.espid', $specialties); else $query->whereRaw('1 = 0');
        return array($query->orderBy('a.fechaini')->get(), $specialties);
    }

    public function groupedInvoices($companyId, $year, $month)
    {
        $query = DB::table('labs_factura_agrupada_cab as c')->leftJoin('labs_factura_agrupada_detalle as d', function ($join) {
            $join->on('d.id_agrup_cab', '=', 'c.id')->where('d.estado', '=', 1);
        })
            ->select('c.*', DB::raw("SUM(CASE WHEN d.pub_priv=0 THEN d.total ELSE 0 END) as total_publicas"), DB::raw("SUM(CASE WHEN d.pub_priv=1 THEN d.total ELSE 0 END) as total_privadas"))
            ->where('c.estado',1)->groupBy('c.id');
        if (Schema::hasColumn('labs_factura_agrupada_cab','id_empresa')) $query->where('c.id_empresa',$companyId);
        if ($year) $query->where('c.anio',$year); if ($month) $query->where('c.mes',$month);
        return $query->orderBy('c.anio','desc')->orderBy('c.mes','desc')->paginate(30);
    }

    public function saveGroupedInvoice(array $values, $id = null)
    {
        return DB::transaction(function () use ($values, $id) {
            $model = $id ? Labs_Factura_Agrupada_Cab::findOrFail($id) : new Labs_Factura_Agrupada_Cab;
            $model->fill($values);
            $model->save();
            return $model;
        });
    }

    public function statistics($companyId, $from, $to, array $examIds)
    {
        $query = DB::table('examen_detalle as d')->join('examen as e','e.id','=','d.id_examen')->join('examen_orden as o','o.id','=','d.id_examen_orden')
            ->select('e.id','e.descripcion',DB::raw('SUM(d.cantidad) as cantidad'),DB::raw('SUM(d.valor) as total'))
            ->where('o.estado','>',0)->where('e.estado',1)->whereBetween('o.fecha_orden',array($from.' 00:00:00',$to.' 23:59:59'));
        if (Schema::hasColumn('examen_orden','id_empresa')) $query->where('o.id_empresa',$companyId);
        if (!empty($examIds)) $query->whereIn('e.id',array_map('intval',$examIds));
        return $query->groupBy('e.id','e.descripcion')->orderBy('cantidad','desc')->get();
    }

    public function historicalExams($search, $perPage = 30)
    {
        $query = DB::table('examen as e')->leftJoin('examen_parametro as p', function ($join) {
            $join->on('p.id_examen', '=', 'e.id')->where('p.estado', '=', 1);
        })
            ->leftJoin('examen_parametro_referencia_anterior as r', function($join){$join->on('r.id_parametro','=','p.id')->where('r.estado','=',1);})
            ->select('e.id','e.nombre','e.descripcion',DB::raw('COUNT(DISTINCT p.id) as parameter_count'),DB::raw('COUNT(DISTINCT r.id) as history_count'))->where('e.estado',1)->groupBy('e.id');
        if($search!=='')$query->where(function($q)use($search){$q->where('e.nombre','like','%'.$search.'%')->orWhere('e.descripcion','like','%'.$search.'%');});
        return $query->orderBy('e.descripcion')->paginate($perPage);
    }

    public function references($examId)
    {
        return DB::table('examen_parametro as p')->leftJoin('examen_parametro_referencia_anterior as r',function($join){$join->on('r.id_parametro','=','p.id')->where('r.estado','=',1);})
            ->select('p.id','p.nombre','p.valor1','p.valor1g','p.unidad1','p.texto_referencia','r.id as reference_id','r.valor1 as old_value1','r.valor1g as old_value1g','r.unidad1 as old_unit','r.texto_referencia as old_text','r.fecha_valida')
            ->where('p.id_examen',$examId)->where('p.estado',1)->orderBy('p.orden')->orderBy('r.fecha_valida','desc')->get();
    }

    public function archiveReference($parameterId, $date, $userId, $ip)
    {
        return DB::transaction(function () use ($parameterId, $date, $userId, $ip) {
            $parameter = Examen_Parametro::findOrFail($parameterId);
            $existing = Examen_Parametro_Referencia_Anterior::where('id_parametro', $parameter->id)
                ->where('fecha_valida', $date)
                ->where('estado', 1)
                ->first();
            if ($existing) return $existing;

            return Examen_Parametro_Referencia_Anterior::create(array(
                'id_parametro'=>$parameter->id,
                'valor1'=>$parameter->valor1,
                'valor1g'=>$parameter->valor1g,
                'unidad1'=>$parameter->unidad1,
                'texto_referencia'=>$parameter->texto_referencia,
                'fecha_valida'=>$date,
                'estado'=>1,
                'id_usuariocrea'=>$userId,
                'id_usuariomod'=>$userId,
                'ip_creacion'=>$ip,
                'ip_modificacion'=>$ip
            ));
        });
    }
}
