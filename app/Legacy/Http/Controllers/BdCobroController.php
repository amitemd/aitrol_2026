<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Examen_Orden;
use Sis_medico\Http\Controllers\Controller;
use Response;
use Sis_medico\Agenda;

class BdCobroController extends Controller
{
    public function index_cobro(Request $request)
    {
        $appId = 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=';
        if (!hash_equals($appId, (string) $request->header('appid', ''))) {
            return response()->json(['status' => 'error', 'msj' => 'CREDENCIALES INVALIDAS'], 401);
        }

        $validator = Validator::make($request->all(), [
            'fecha_desde' => 'required|date_format:Y-m-d',
            'fecha_hasta' => 'required|date_format:Y-m-d|after_or_equal:fecha_desde',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'msj' => $validator->errors()->first()], 422);
        }

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $anio = date('Y', strtotime($fecha_desde));
        $mes = date('m', strtotime($fecha_desde));

        $procedimientos = Agenda::selectRaw("'PROCEDIMIENTOS' AS data,
                pro.nombre,
                COUNT(DISTINCT agenda.id) AS cantidad,
                COALESCE(SUM(detalle_venta.total), 0) AS valor")
            ->join('paciente as p', 'p.id', 'agenda.id_paciente')
            ->join('procedimiento as pro', 'pro.id', 'agenda.id_procedimiento')
            ->leftJoin('ct_orden_venta as orden_venta', function ($join) {
                $join->on('orden_venta.id_agenda', '=', 'agenda.id')
                    ->where('orden_venta.estado', '=', 1);
            })
            ->leftJoin('ct_orden_venta_detalle as detalle_venta', 'detalle_venta.id_orden', '=', 'orden_venta.id')
            ->where('agenda.proc_consul', '1')
            ->where('agenda.estado_cita', '!=', '3')
            ->where('agenda.estado_cita', '>=', '1')
            ->whereNotNull('pro.id_grupo_procedimiento')
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('agenda.fechaini', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
                } else {
                    $q->where('agenda.fechaini', '<=', $fecha_hasta . ' 23:59:59');
                }
            })
            ->groupBy('pro.id', 'pro.nombre')
            ->orderBy('pro.nombre');

        $consultas = Agenda::selectRaw("'CONSULTAS' AS data,'CONSULTAS' as nombre, COUNT(agenda.fechaini), ifnull(COUNT(agenda.fechaini),0) AS valor")
            ->whereIn('proc_consul', ['0', '4'])->where('estado_cita', '>=', '0')->where('estado_cita', '<>', '3')
            ->whereNotIn('id_doctor1', ['9666666666', '4444444444', '444444444A', '444444444B'])
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('agenda.fechaini', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
                } else {
                    $q->where('agenda.fechaini', '<=', $fecha_hasta . ' 23:59:59');
                }
            })
            ->groupBy(DB::raw('MONTH(fechaini)'));

        $consultasLabs = Agenda::selectRaw("'AGENDA_LABS' AS data, 'AGENDA_LABS' as nombre, COUNT(agenda.fechaini), ifnull((COUNT(agenda.fechaini)*0.5),0) AS valor")
            ->whereIn('proc_consul', ['0', '4'])->where('estado_cita', '>=', '0')->where('estado_cita', '<>', '3')
            ->whereIn('id_doctor1', ['4444444444', '444444444A', '444444444B'])
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('agenda.fechaini', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
                } else {
                    $q->where('agenda.fechaini', '<=', $fecha_hasta . ' 23:59:59');
                }
            })
            ->groupBy(DB::raw('MONTH(fechaini)'));

        $Labsmenos25 = Examen_Orden::selectRaw("'ORDENES_MENOS25' AS data, 'ORDENES < 25' AS nombre, count(mes), ifnull((sum(valor) *0.04),0) AS valor")
            ->where('estado', '1')
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('valor', '<', '25');

        $labsmas25 = Examen_Orden::selectRaw("'ORDENES_MAS25' AS data, 'ORDENES > 25' AS nombre,  count(mes) AS cantidad,  count(mes) AS valor")
            ->where('estado', '1')
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('valor', '>=', '25');

        $agendas = $procedimientos->union($consultas)->union($consultasLabs)->union($Labsmenos25)->union($labsmas25)->get();
        return $agendas;
    }
}
