<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use DB;

class agenda extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agenda';
    protected $keyType = 'string';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente', 'id_paciente');
    }

    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro', 'id_seguro');
    }
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }

    //Agregada 8/04/2019
    public function doctor1()
    {
        return $this->belongsTo('Sis_medico\User', 'id_doctor1');
    }

    public function procedimiento()
    {
        return $this->belongsTo('Sis_medico\Procedimiento', 'id_procedimiento');
    }

    public function historia_clinica()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica', 'id', 'id_agenda');
    }

    public function historia_clinica_1()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica', 'id_paciente', 'id_paciente');
    }

    public function user_crea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }

    public function user_mod()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod');
    }

    public function agenda_procedimientos()
    {
        return $this->hasMany('Sis_medico\AgendaProcedimiento', 'id_agenda');
    }
    public static function getPlanillas($r, $tipo = 0)
    {
        return Agenda::join('historiaclinica as h', 'agenda.id', '=', 'h.id_agenda')
            ->join('hc_procedimientos as pro', 'h.hcid', '=', 'pro.id_hc')
            ->join('planilla_cabecera as pl', 'pro.id', '=', 'pl.id_hc_procedimiento')
            ->join('planilla_detalle as pd', 'pl.id', '=', 'pd.id_planilla_cabecera')
            ->join('producto as p', 'pd.codigo', '=', 'p.codigo')
            ->join('tipo as t', 'p.tipo_producto', '=', 't.id')
            ->join('paciente as pa', 'agenda.id_paciente', '=', 'pa.id')
            ->join('users as usuario_crea', 'agenda.id_usuariocrea', '=', 'usuario_crea.id')
            ->select(
                'pl.id as id_planilla'
            )
            ->where('agenda.id_empresa', '1307189140001')
            ->where('agenda.proc_consul', 1)
            ->where('t.nombre', 'like', '%insumo%')
            ->where('t.estado', '!=', 0)
            ->where('agenda.estado_cita', 4)
            ->where('pl.facturado_crm', 0)
            ->whereBetween('agenda.fechaini', [$r->desde . ' 00:00:00', $r->hasta . ' 23:59:59'])->where("agenda.id_seguro", 2)
            ->where(function ($q) use ($r) {
                if ($r->paciente != '') {
                    $q->where('agenda.id_paciente', $r->paciente);
                }
            })
            ->where('t.presenta_crm', 1)
            ->groupBy('pl.id')
            ->get();
    }
    public static function getProcedimientosInsumos($r, $tipo = 0)
    {
        $id_empresa = session('id_empresa');
        if ($tipo == 0) {
            return agenda::selectRaw('GROUP_CONCAT(pr.nombre SEPARATOR "|") as procedimientos,
                agenda.id,
                agenda.id_usuariomod,
                agenda.id_paciente,
                p.nombre1,
                p.nombre2,
                p.apellido1,
                p.apellido2,
                agenda.fechaini,
                pro.created_at as fecha_procedimiento,
                pl.fecha as fecha_pistoleo,
                u.nombre1 as nombre_usuario,
                u.apellido1 as apellido_usuario,
                agenda.updated_at')
                ->join('paciente as p', 'agenda.id_paciente', 'p.id')
                ->join('users as u', 'agenda.id_usuariomod', 'u.id')
                ->join('historiaclinica as h', 'agenda.id', 'h.id_agenda')
                ->join('hc_procedimientos as pro', 'h.hcid', 'pro.id_hc') //join hc_procedimientos p on h.hcid=p.id_hc
                ->join('planilla_cabecera as pl', 'pro.id', 'pl.id_hc_procedimiento') //join planilla_cabecera pl on h.hcid=pl.id_hc_procedimiento
                ->join('hc_procedimiento_final as pf', 'pro.id', 'pf.id_hc_procedimientos') //join hc_procedimiento_final pf on p.id=pf.id_hc_procedimientos
                ->join('procedimiento as pr', 'pf.id_procedimiento', 'pr.id') //join procedimiento pr on pf.id_procedimiento=pr.id
                ->where('agenda.id_empresa', '1307189140001')
                ->where('agenda.proc_consul', 1)
                ->where('agenda.estado_cita', 4)
                ->whereBetween('agenda.fechaini', [$r->desde . ' 00:00:00', $r->hasta . ' 23:59:59'])
                ->where(function ($q) use ($r) {
                    if ($r->paciente != '') {
                        $q->where('agenda.id_paciente', $r->paciente);
                    }
                })
                ->groupby('agenda.id')
                ->orderBy('agenda.fechaini', 'asc')
                ->get();
        } else {
            //DB::enableQueryLog();
            return Agenda::join('historiaclinica as h', 'agenda.id', '=', 'h.id_agenda')
                ->join('hc_procedimientos as pro', 'h.hcid', '=', 'pro.id_hc')
                ->join('planilla_cabecera as pl', 'pro.id', '=', 'pl.id_hc_procedimiento')
                ->join('planilla_detalle as pd', 'pl.id', '=', 'pd.id_planilla_cabecera')
                ->join('producto as p', 'pd.codigo', '=', 'p.codigo')
                ->join('tipo as t', 'p.tipo_producto', '=', 't.id')
                ->join('paciente as pa', 'agenda.id_paciente', '=', 'pa.id')
                ->join('users as usuario_crea', 'agenda.id_usuariocrea', '=', 'usuario_crea.id')
                ->leftJoin('inv_det_movimientos as inmov', function ($q) {
                    $q->on('pd.serie', '=', 'inmov.serie')
                        ->on('p.id', '=', 'inmov.id_producto');
                })
                ->leftJoin('inv_cab_movimientos as incab', 'inmov.id_inv_cab_movimientos', '=', 'incab.id')
                ->leftJoin('inv_documentos_bodegas AS indoc', 'incab.id_documento_bodega', '=', 'indoc.id')

                ->select(
                    'h.id_paciente',
                    'pl.id as id_planilla',
                    DB::raw('CONCAT(pa.nombre1, " ", pa.nombre2, " ", pa.apellido1, " ", pa.apellido2) as paciente'),
                    DB::raw('CONCAT(usuario_crea.nombre1, " ", usuario_crea.nombre2, " ", usuario_crea.apellido1, " ", usuario_crea.apellido2) as usuario_crea'),
                    DB::raw('(SELECT GROUP_CONCAT(h.nombre SEPARATOR " + ") FROM hc_procedimiento_final f 
            JOIN procedimiento h ON f.id_procedimiento = h.id 
            WHERE id_hc_procedimientos = pro.id) as procedimiento'),

                    'pd.codigo',
                    'pd.serie',
                    'inmov.valor_unitario',
                    DB::raw('SUM(pd.cantidad) as cantidad'),
                    DB::raw('(inmov.valor_unitario * 0.10) as v_unit'),
                    DB::raw('(inmov.valor_unitario * 1.10) as precio'),
                    DB::raw('((inmov.valor_unitario*0.10)+inmov.valor_unitario)*sum(pd.cantidad) as total'),
                    'p.nombre as producto',
                    'p.iva'
                )
                ->where('agenda.id_empresa', '1307189140001')
                ->where('agenda.proc_consul', 1)
                ->where('t.nombre', 'like', '%insumo%')
                ->where('t.estado', '!=', 0)
                ->where('agenda.estado_cita', 4)
                ->where('pl.facturado_crm', 0)
                ->whereBetween('agenda.fechaini', [$r->desde . ' 00:00:00', $r->hasta . ' 23:59:59'])
                ->where('agenda.id_seguro', 2)
                ->when($r->paciente, function ($query) use ($r) {
                    $query->where('agenda.id_paciente', $r->paciente);
                })
                ->where('t.presenta_crm', 1)
                //->groupBy('pd.codigo')
                ->get();
                //echo '<pre>';print_r(DB::getQueryLog());exit;
        }
    }

    public static function  planillaDetalleF($id)
    {
        $agenda = Agenda::selectRaw('(select  
                GROUP_CONCAT(h.nombre SEPARATOR " + ") 
            from hc_procedimiento_final f 
            join procedimiento h on f.id_procedimiento=h.id 
            where id_hc_procedimientos=pro.id
            ) as procedimiento,
            pd.codigo,
            pd.serie,
            pd.precio,
            pd.cantidad,
            p.nombre as producto')
            ->join('historiaclinica as h', 'agenda.id', 'h.id_agenda')
            ->join('hc_procedimientos as pro', 'h.hcid', 'pro.id_hc') //join hc_procedimientos p on h.hcid=p.id_hc
            ->join('planilla_cabecera as pl', 'pro.id', 'pl.id_hc_procedimiento') //join planilla_cabecera pl on h.hcid=pl.id_hc_procedimiento
            ->join('planilla_detalle as pd', 'pl.id', 'pd.id_planilla_cabecera') //join planilla_detalle pd on pl.id=pd.id_planilla_cabecera
            ->join('producto as p', 'pd.codigo', 'p.codigo')
            ->join('tipo as t', 'p.tipo_producto', 't.id')
            ->where('agenda.id_empresa', '1307189140001')
            ->where('agenda.proc_consul', 1)
            ->where(function ($q) {
                $q->where('t.nombre', 'like', '%insumo%');
            })
            ->where('agenda.estado_cita', 4)
            ->where('agenda.id', $id)
            ->get();
        return ["detalle" => $agenda];
    }


    public static function  detalleplanilla($id)
    {
        $html = '';
        $agenda = Agenda::selectRaw('(select  
                    GROUP_CONCAT(h.nombre SEPARATOR " + ") 
                from hc_procedimiento_final f 
                join procedimiento h on f.id_procedimiento=h.id 
                where id_hc_procedimientos=pro.id
                ) as procedimiento,
                pd.codigo,
                pd.serie,
                pd.precio,
                pd.cantidad,
                p.nombre as producto')
            ->join('historiaclinica as h', 'agenda.id', 'h.id_agenda')
            ->join('hc_procedimientos as pro', 'h.hcid', 'pro.id_hc') //join hc_procedimientos p on h.hcid=p.id_hc
            ->join('planilla_cabecera as pl', 'pro.id', 'pl.id_hc_procedimiento') //join planilla_cabecera pl on h.hcid=pl.id_hc_procedimiento
            ->join('planilla_detalle as pd', 'pl.id', 'pd.id_planilla_cabecera') //join planilla_detalle pd on pl.id=pd.id_planilla_cabecera
            ->join('producto as p', 'pd.codigo', 'p.codigo')
            ->join('tipo as t', 'p.tipo_producto', 't.id')
            ->where('agenda.id_empresa', '1307189140001')
            ->where('agenda.proc_consul', 1)
            ->where(function ($q) {
                $q->where('t.nombre', 'like', '%insumo%');
            })
            ->where('agenda.estado_cita', 4)
            ->where('agenda.id', $id)
            ->get();
        $html .= '<table style="width:100%;">
        <thead>
            <tr>
                <th style="width">No.</th>
                <th>Procedimiento</th>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Serie</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>';
        if ($agenda->count() > 0) {
            $cont = 0;
            $total = 0;
            foreach ($agenda as $row) {
                $total += $row->cantidad * $row->precio;
                $cont++;
                $html .= '<tr>';
                $html .= '<td>' . $cont . '</td>';
                $html .= '<td>' . $row->procedimiento . '</td>';
                $html .= '<td>' . $row->codigo . '</td>';
                $html .= '<td>' . $row->producto . '</td>';
                $html .= '<td>' . $row->serie . '</td>';
                $html .= '<td style="text-align:right;">' . $row->cantidad . '</td>';
                $html .= '<td style="text-align:right;">' . $row->precio . '</td>';
                $html .= '<td style="text-align:right;">' . $row->cantidad * $row->precio . '</td>';
                $html .= '</tr>';
            }
            $html .= '<tr><td colspan="8" style="text-align:right;">' . number_format($total, 2) . '</td></tr>';
        } else {
            $html .= '<tr><td colspan="8">No hay datos que mostrar</td></tr>';
        }
        $html .= '</tbody><tfoot>';
        if ($agenda->count() > 0) {
            $existe = '';

            $empresas = Empresa::where('admision', 1)->get();
            $combo = '';
            foreach ($empresas as $row) {
                $combo .= '<option value="' . $row->id . '">' . $row->razonsocial . '</optioN>';
            }
            foreach ($agenda as $row) {
                if ($existe != $row->procedimiento) {
                    $existe = $row->procedimiento;
                    $html .= '<tr>
                    <td colspan="3">' . $row->procedimiento . ': </td>
                    <td colspan="2">
                        <select class="form-control">
                            <option value="">--Seleccionar--</option>
                            ' . $combo . '
                        </select>
                    </td>       
                </tr>';
                }
            }
        }
        $html .= '</tfoot></table>';
        echo $html;
    }
    public static function detalleplanillaVenta($id)
    {
        DB::enableQueryLog();
        $age = Agenda::selectRaw('(select  
                    GROUP_CONCAT(h.nombre SEPARATOR " + ") 
                from hc_procedimiento_final f 
                join procedimiento h on f.id_procedimiento=h.id 
                where id_hc_procedimientos=pro.id
                ) as procedimiento,
                pd.codigo,
                pd.serie,
                pd.precio,
                pd.cantidad,
                p.nombre as producto ,p.iva as iva')
            ->join('historiaclinica as h', 'agenda.id', 'h.id_agenda')
            ->join('hc_procedimientos as pro', 'h.hcid', 'pro.id_hc') //join hc_procedimientos p on h.hcid=p.id_hc
            ->join('planilla_cabecera as pl', 'pro.id', 'pl.id_hc_procedimiento') //join planilla_cabecera pl on h.hcid=pl.id_hc_procedimiento
            ->join('planilla_detalle as pd', 'pl.id', 'pd.id_planilla_cabecera') //join planilla_detalle pd on pl.id=pd.id_planilla_cabecera
            ->join('producto as p', 'pd.codigo', 'p.codigo')
            ->join('tipo as t', 'p.tipo_producto', 't.id')
            ->where('agenda.id_empresa', '1307189140001')
            ->where('agenda.proc_consul', 1)
            ->where(function ($q) {
                $q->where('t.nombre', 'like', '%insumo%');
            })
            ->where('agenda.estado_cita', 4)
            ->where('agenda.id', $id)
            ->get();
        echo '<pre>';
        print_r(DB::getQueryLog());
        exit;
    }

    public function especialidad()
    {
        return $this->belongsTo('Sis_medico\Especialidad', 'espid');
    }


    public static function getCantidadAgendas($id_paciente, $anio, $id_especialidad, $id_seguro, $id_empresa)
    {
        return Agenda::where('id_paciente', $id_paciente)
            ->where('id_seguro', $id_seguro)
            ->where('espid', $id_especialidad)
            ->where('id_empresa', $id_empresa)
            ->whereRaw('YEAR(fechaini) = ?', [$anio])
            ->where('proc_consul', 0)
            ->where('estado', 1)
            ->where('estado_cita', 4)
            ->count();
    }

    public function getEdadAgendaAttribute()
    {
        // Verificar si fechaini o fecha_nacimiento están vacíos
        if (!$this->fechaini || !$this->paciente || !$this->paciente->fecha_nacimiento) {
            return 'Desconocida'; // Manejar casos donde falten datos
        }

        $birthDate = new \DateTime($this->paciente->fecha_nacimiento); // Fecha de nacimiento del paciente
        $fechaini = new \DateTime($this->fechaini); // Fecha fechaini de la agenda
        $age = $fechaini->diff($birthDate); // Calcular la diferencia en años

        return $age->y; // Retornar solo los años
    }

}
