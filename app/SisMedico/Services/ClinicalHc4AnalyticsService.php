<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\EstimadoSeguros;
use Sis_medico\Seguro;
use Sis_medico\User;

class ClinicalHc4AnalyticsService
{
    public function pasteles_hc4(Request $request)
    {
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $doctor_ag   = [];
        $i           = 0;
        $doctores    = User::where('id_tipo_usuario', '3')->get();
        //procedimientos agendados por doctores
        foreach ($doctores as $doctor) {
            $ctaagenda = Agenda::where('id_doctor1', $doctor->id)->where('proc_consul', '1')->where('estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->count();
            if ($ctaagenda != '0') {
                $doctor_ag[$i] = [$doctor, $ctaagenda];
                $i++;
            }

        }

        //CONSULTAS AGENDADAS por doctores
        $doctor_co = [];
        $k         = 0;
        foreach ($doctores as $doctor) {
            $ctaagenda = Agenda::where('id_doctor1', $doctor->id)->where('proc_consul', '0')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->count();
            if ($ctaagenda != '0') {
                $doctor_co[$k] = [$doctor, $ctaagenda];
                $k++;
            }

        }

        //CONSULTAS AGENDADAS REALIZADAS por doctores
        $doctor_co_ok = [];
        $k            = 0;
        foreach ($doctores as $doctor) {
            $ctaagenda = Agenda::where('id_doctor1', $doctor->id)->where('proc_consul', '0')->where('estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('estado_cita', '4')->count();
            if ($ctaagenda != '0') {
                $doctor_co_ok[$k] = [$doctor, $ctaagenda];
                $k++;
            }

        }

        //PROCEDIMIENTOS REALIZADOS por doctor (pentax)
        $proc_doc = [];
        $i1       = 0;
        $doctores = User::where('id_tipo_usuario', '3')->get();
        foreach ($doctores as $doctor) {
            $ctaagenda = DB::table('agenda as a')->where('a.proc_consul', '1')->where('a.estado', '1')->whereBetween('a.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('pentax as p', 'p.id_agenda', 'a.id')->where('p.id_doctor1', $doctor->id)->where('p.estado_pentax', '4')->count();

            if ($ctaagenda != '0') {
                $proc_doc[$i1] = [$doctor, $ctaagenda];
                $i1++;
            }

        }

        //procedimientos por seguros
        $proc_seg = [];
        $j        = 0;
        $seguros  = Seguro::all();
        foreach ($seguros as $seguro) {
            $cta_seguro = Agenda::where('id_seguro', $seguro->id)->where('proc_consul', '1')->where('estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->count();
            if ($cta_seguro != '0') {
                $proc_seg[$j] = [$seguro, $cta_seguro];
                $j++;
            }

        }

        $co_seg = [];
        $l      = 0;
        foreach ($seguros as $seguro) {
            $cta_seguro = Agenda::where('id_seguro', $seguro->id)->where('proc_consul', '0')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->count();
            if ($cta_seguro != '0') {
                $co_seg[$l] = [$seguro, $cta_seguro];
                $l++;
            }

        }
        return view('hc4/pastel/procesos', ['doctor_ag' => $doctor_ag, 'doctor_co' => $doctor_co, 'proc_seg' => $proc_seg, 'co_seg' => $co_seg, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'proc_doc' => $proc_doc, 'doctor_co_ok' => $doctor_co_ok]);
    }


    public function ganancia_hc4(Request $request)
    {
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $doctor_ag   = [];
        $i           = 0;

        //consultas por doctores
        $doctores       = User::where('id_tipo_usuario', '3')->get();
        $doctor_ganacia = [];
        $k              = 0;
        foreach ($doctores as $doctor) {
            $ganancia        = 0;
            $agendas         = Agenda::where('agenda.proc_consul', '0')->where('agenda.estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('agenda.estado_cita', '4')->select('agenda.*')->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->where('hp.id_doctor_examinador', $doctor->id)->get();
            $elementod       = 0;
            $doctor_consulta = [];
            if ($agendas->count() != '0') {
                foreach ($agendas as $value) {

                    $estimado = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', $doctor->id)->first();
                    if ($value->cortesia == 'NO') {
                        if ($value->vip == 1) {
                            $ganancia                       = $ganancia + 120;
                            $doctor_consulta[$elementod][0] = '120';
                            $doctor_consulta[$elementod][1] = $value->seguro->nombre;
                            $doctor_consulta[$elementod][2] = $value->id;
                            $doctor_consulta[$elementod][3] = $value->fechaini;
                            $doctor_consulta[$elementod][4] = $value->paciente->nombre1 . ' ' . $value->paciente->apellido1;
                            $elementod++;
                        } elseif ($value->id_seguro == 2) {
                            if ($value->id_empresa == '0992704152001') {
                                $ganancia                       = $ganancia + 14;
                                $doctor_consulta[$elementod][0] = '14';
                                $doctor_consulta[$elementod][1] = $value->seguro->nombre;
                                $doctor_consulta[$elementod][2] = $value->id;
                                $doctor_consulta[$elementod][3] = $value->fechaini;
                                $doctor_consulta[$elementod][4] = $value->paciente->nombre1 . ' ' . $value->paciente->apellido1;
                                $elementod++;
                            } elseif ($value->id_empresa == '1307189140001') {

                                $doctor_consulta[$elementod][0] = '12';
                                $doctor_consulta[$elementod][1] = $value->seguro->nombre;
                                $doctor_consulta[$elementod][2] = $value->id;
                                $doctor_consulta[$elementod][3] = $value->fechaini;
                                $doctor_consulta[$elementod][4] = $value->paciente->nombre1 . ' ' . $value->paciente->apellido1s;
                                $elementod++;
                                $ganancia = $ganancia + 12;
                            }

                        } else {
                            if (!is_null($estimado)) {
                                $ganancia                       = $ganancia + $estimado->costo;
                                $doctor_consulta[$elementod][0] = $estimado->costo;
                                $doctor_consulta[$elementod][1] = $estimado->seguro->nombre;
                                $elementod++;
                            } else {
                                $estimado2 = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', 'GASTRO')->first();
                                if (!is_null($estimado2)) {
                                    $ganancia = $ganancia + $estimado2->costo;

                                    $doctor_consulta[$elementod][0] = $estimado2->costo;
                                    $doctor_consulta[$elementod][1] = $estimado2->seguro->nombre;
                                    $elementod++;
                                }

                            }
                        }

                    }
                    //dd($value);
                }
            }

            $agendas = Agenda::where('agenda.proc_consul', '0')->where('agenda.id_doctor1', $doctor->id)->where('agenda.estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('agenda.estado_cita', '4')->select('agenda.*')->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->whereNull('hp.id_doctor_examinador')->get();

            if ($agendas->count() != '0') {
                foreach ($agendas as $value) {

                    $estimado = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', $doctor->id)->first();
                    if ($value->cortesia == 'NO') {
                        if ($value->vip == 1) {
                            $ganancia = $ganancia + 120;
                        } elseif ($value->id_seguro == 2) {
                            if ($value->id_empresa == '0992704152001') {
                                $ganancia = $ganancia + 14;
                            } elseif ($value->id_empresa == '1307189140001') {
                                $ganancia = $ganancia + 12;
                            }

                        } else {
                            if (!is_null($estimado)) {
                                $ganancia = $ganancia + $estimado->costo;
                            } else {
                                $estimado2 = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', 'GASTRO')->first();
                                if (!is_null($estimado2)) {
                                    $ganancia = $ganancia + $estimado2->costo;
                                }

                            }
                        }

                    }
                }
            }

            if ($ganancia > 0) {
                $doctor_ganacia[$k] = [$doctor, $ganancia];
                //dd($doctor_consulta);
                $k++;
            }

        }

        //consultas por seguros

        $seguros = Seguro::all();
        $co_seg  = [];
        $l       = 0;
        foreach ($seguros as $seguro) {
            $ganancia   = 0;
            $cta_seguro = Agenda::where('agenda.id_seguro', $seguro->id)->where('agenda.proc_consul', '0')->where('agenda.estado', '1')->where('agenda.estado_cita', '4')->where('agenda.cortesia', 'NO')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->get();
            if ($cta_seguro->count() > '0') {
                foreach ($cta_seguro as $value) {
                    $estimado = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', $value->id_doctor1)->first();
                    if ($value->vip != 1) {
                        if (!is_null($estimado)) {
                            $ganancia = $ganancia + $estimado->costo;
                        } else {
                            $estimado2 = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', 'GASTRO')->first();
                            if (!is_null($estimado2)) {
                                $ganancia = $ganancia + $estimado2->costo;
                            }

                        }
                    }

                }
                if ($ganancia > 0) {
                    $co_seg[$l] = [$seguro, $ganancia];
                    $l++;
                }
            }

        }
        $ganancia   = 0;
        $cta_seguro = Agenda::where('agenda.id_seguro', '2')->where('agenda.proc_consul', '0')->where('agenda.estado', '1')->where('agenda.estado_cita', '4')->where('agenda.cortesia', 'NO')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->select('agenda.*')->get();
        if ($cta_seguro->count() > '0') {
            foreach ($cta_seguro as $value) {
                if ($value->vip != 1) {
                    if ($value->id_empresa == '0992704152001') {
                        $ganancia = $ganancia + 14;
                    } elseif ($value->id_empresa == '1307189140001') {
                        $ganancia = $ganancia + 12;
                    }
                }

            }
            $seguro = Seguro::find(2);
            if ($ganancia > 0) {
                $co_seg[$l] = [$seguro, $ganancia];
                $l++;
            }
        }

        $ganancia   = 0;
        $vip        = [];
        $cta_seguro = Agenda::where('agenda.proc_consul', '0')->where('agenda.estado', '1')->where('agenda.estado_cita', '4')->where('agenda.vip', 1)->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->get();

        if ($cta_seguro->count() > '0') {
            foreach ($cta_seguro as $value) {
                $ganancia = $ganancia + 120;

            }

            if ($ganancia > 0) {
                $vip[0] = ['vip', $ganancia];
            }
        }
        //ESTADISTICO DE ORDENES DE VENTA POR AGENDA CONSULTA
        $orden_venta_seguro = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->join('ct_orden_venta as co', 'co.id_agenda', 'agenda.id')
            ->where('co.estado', 1)
            ->where('agenda.proc_consul', 0)
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('co.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('agenda.cortesia', 'agenda.vip', 's.nombre as seguro', 's.id as sid', 's.clase')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(co.total_sin_tarjeta) as total1')
            ->selectRaw('sum(co.valor_oda) as total2')
            ->orderBy('s.clase', 'agenda.cortesia', 'agenda.vip', 's.nombre', 's.id')
            ->groupBy('agenda.cortesia', 'agenda.vip', 's.nombre', 's.id', 's.clase')
            ->get();

        $orden_venta_seguro2 = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->leftjoin('ct_orden_venta as co', 'co.id_agenda', 'agenda.id')
            ->whereNull('co.id')
            ->where('agenda.proc_consul', 0)
            ->where('agenda.estado', 1)
            ->where('agenda.estado_cita', 4)
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('co.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('agenda.cortesia', 'agenda.vip', 's.nombre as seguro', 's.id as sid', 's.clase')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(co.total_sin_tarjeta) as total1')
            ->selectRaw('sum(co.valor_oda) as total2')
            ->orderBy('s.clase', 'agenda.cortesia', 'agenda.vip', 's.nombre', 's.id')
            ->groupBy('agenda.cortesia', 'agenda.vip', 's.nombre', 's.id', 's.clase')
            ->get();
        //dd($orden_venta_seguro2);

        //dd($orden_venta_seguro);

        $orden_venta_tipo_total = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->leftjoin('ct_orden_venta as co', 'co.id_agenda', 'agenda.id')
            ->where('agenda.estado', 1)
            ->where('agenda.estado_cita', '4')
            ->where('agenda.proc_consul', 0)
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('co.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('agenda.id', 'agenda.id_paciente', 'agenda.cortesia', 'agenda.vip', 's.clase', 's.nombre', 'co.id as orden', 'co.estado', 'co.total_sin_tarjeta', 'co.valor_oda')
            ->get();

        $agenda_dia = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->where('agenda.estado', 1)
            ->where('agenda.estado_cita', '4')
            ->where('agenda.proc_consul', 0)
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('agenda.fechaini', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->selectRaw('date_format(agenda.fechaini,"%Y-%m-%d") as fecha')
            ->selectRaw('count(*) as cantidad')
            ->get();

        $cantidad_dias = $agenda_dia->count();

        //dd($orden_venta_tipo_total);

        $arr          = [];
        $cantidad_nt  = 0;
        $total_nt     = 0;
        $cantidad_vip = 0;
        $total_vip    = 0;
        $cantidad_top = 0;
        $total_top    = 0;
        $arr2         = [];
        $meta_valor   = 0;
        foreach ($orden_venta_tipo_total as $value) {
            if ($value->orden != null && $value->estado == '1') {
                if ($value->cortesia == 'SI') {
                    $cantidad_nt++;
                    $arr[0] = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->vip == '1') {
                    $cantidad_vip++;
                    $total_vip = $total_vip + $value->total_sin_tarjeta + $value->valor_oda;
                    $arr[1]    = ['tipo' => 'PACIENTES VIP', 'cantidad' => $cantidad_vip, 'valor' => $total_vip, 'meta_cantidad' => $cantidad_dias * 2, 'meta_valor' => $cantidad_dias * 120];
                } elseif ($value->clase != '1') {
                    $cantidad_nt++;
                    $total_nt = $total_nt + $value->total_sin_tarjeta + $value->valor_oda;
                    $arr[0]   = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->clase == '1') {
                    $cantidad_top++;
                    $total_top = $total_top + $value->total_sin_tarjeta + $value->valor_oda;
                    $arr[2]    = ['tipo' => 'PARTICULARES, BMI, BUPA, BEST', 'cantidad' => $cantidad_top, 'valor' => $total_top, 'meta_cantidad' => $cantidad_dias * 7, 'meta_valor' => $cantidad_dias * 504];
                }
            }
            if ($value->orden == null) {
                if ($value->cortesia == 'SI') {
                    $cantidad_nt++;
                    $arr[0] = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->vip == '1') {
                    $cantidad_vip++;
                    $arr[1] = ['tipo' => 'PACIENTES VIP', 'cantidad' => $cantidad_vip, 'valor' => $total_vip, 'meta_cantidad' => 0, 'meta_valor' => $cantidad_dias * 120];
                } elseif ($value->clase != '1') {
                    $cantidad_nt++;
                    $arr[0] = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->clase == '1') {
                    $cantidad_top++;
                    $arr[2] = ['tipo' => 'PARTICULARES, BMI, BUPA, BEST', 'cantidad' => $cantidad_top, 'valor' => $total_top, 'meta_cantidad' => $cantidad_dias * 7, 'meta_valor' => $cantidad_dias * 504];
                }
            }

        }

        //dd($arr[0]);

        return view('hc4/pastel/ganacia_estimada', ['doctor_ganacia' => $doctor_ganacia, 'co_seg' => $co_seg, 'vip' => $vip, 'orden_venta_seguro' => $orden_venta_seguro, 'request' => $request, 'arr' => $arr, 'orden_venta_tipo_total' => $orden_venta_tipo_total, 'orden_venta_seguro2' => $orden_venta_seguro2]);
    }


    public function ganancia_efectiva(Request $request)
    {
        //return "hola";
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $doctor_ag   = [];
        $i           = 0;

        //consultas por doctores
        $doctores       = User::where('id_tipo_usuario', '3')->get();
        $doctor_ganacia = [];
        $k              = 0;
        foreach ($doctores as $doctor) {
            $ganancia = 0;
            $agendas  = Agenda::where('agenda.proc_consul', '0')->where('agenda.estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('agenda.estado_cita', '4')->select('agenda.*')->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->where('hp.id_doctor_examinador', $doctor->id)->get();
            if ($agendas->count() != '0') {
                foreach ($agendas as $value) {

                    $estimado = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', $doctor->id)->first();
                    if ($value->cortesia == 'NO') {
                        if ($value->vip == 1) {
                            $ganancia = $ganancia + 120;
                        } elseif ($value->id_seguro == 2) {
                            if ($value->id_empresa == '0992704152001') {
                                $ganancia = $ganancia + 14;
                            } elseif ($value->id_empresa == '1307189140001') {
                                $ganancia = $ganancia + 12;
                            }

                        } else {
                            if (!is_null($estimado)) {
                                $ganancia = $ganancia + $estimado->costo;
                            } else {
                                $estimado2 = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', 'GASTRO')->first();
                                if (!is_null($estimado2)) {
                                    $ganancia = $ganancia + $estimado2->costo;
                                }

                            }
                        }

                    }
                }
            }

            $agendas = Agenda::where('agenda.proc_consul', '0')->where('agenda.id_doctor1', $doctor->id)->where('agenda.estado', '1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('agenda.estado_cita', '4')->select('agenda.*')->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->whereNull('hp.id_doctor_examinador')->get();
            if ($agendas->count() != '0') {
                foreach ($agendas as $value) {

                    $estimado = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', $doctor->id)->first();
                    if ($value->cortesia == 'NO') {
                        if ($value->vip == 1) {
                            $ganancia = $ganancia + 120;
                        } elseif ($value->id_seguro == 2) {
                            if ($value->id_empresa == '0992704152001') {
                                $ganancia = $ganancia + 14;
                            } elseif ($value->id_empresa == '1307189140001') {
                                $ganancia = $ganancia + 12;
                            }

                        } else {
                            if (!is_null($estimado)) {
                                $ganancia = $ganancia + $estimado->costo;
                            } else {
                                $estimado2 = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', 'GASTRO')->first();
                                if (!is_null($estimado2)) {
                                    $ganancia = $ganancia + $estimado2->costo;
                                }

                            }
                        }

                    }
                }
            }

            if ($ganancia > 0) {
                $doctor_ganacia[$k] = [$doctor, $ganancia];
                $k++;
            }

        }

        //consultas por seguros

        $seguros = Seguro::all();
        $co_seg  = [];
        $l       = 0;
        foreach ($seguros as $seguro) {
            $ganancia   = 0;
            $cta_seguro = Agenda::where('agenda.id_seguro', $seguro->id)->where('agenda.proc_consul', '0')->where('agenda.estado', '1')->where('agenda.estado_cita', '4')->where('agenda.cortesia', 'NO')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->get();
            if ($cta_seguro->count() > '0') {
                foreach ($cta_seguro as $value) {
                    $estimado = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', $value->id_doctor1)->first();
                    if ($value->vip != 1) {
                        if (!is_null($estimado)) {
                            $ganancia = $ganancia + $estimado->costo;
                        } else {
                            $estimado2 = EstimadoSeguros::where('id_seguro', $value->id_seguro)->where('id_doctor', 'GASTRO')->first();
                            if (!is_null($estimado2)) {
                                $ganancia = $ganancia + $estimado2->costo;
                            }

                        }
                    }

                }
                if ($ganancia > 0) {
                    $co_seg[$l] = [$seguro, $ganancia];
                    $l++;
                }
            }

        }
        $ganancia   = 0;
        $cta_seguro = Agenda::where('agenda.id_seguro', '2')->where('agenda.proc_consul', '0')->where('agenda.estado', '1')->where('agenda.estado_cita', '4')->where('agenda.cortesia', 'NO')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->select('agenda.*')->get();
        if ($cta_seguro->count() > '0') {
            foreach ($cta_seguro as $value) {
                if ($value->vip != 1) {
                    if ($value->id_empresa == '0992704152001') {
                        $ganancia = $ganancia + 14;
                    } elseif ($value->id_empresa == '1307189140001') {
                        $ganancia = $ganancia + 12;
                    }
                }

            }
            $seguro = Seguro::find(2);
            if ($ganancia > 0) {
                $co_seg[$l] = [$seguro, $ganancia];
                $l++;
            }
        }

        $ganancia   = 0;
        $vip        = [];
        $cta_seguro = Agenda::where('agenda.proc_consul', '0')->where('agenda.estado', '1')->where('agenda.estado_cita', '4')->where('agenda.vip', 1)->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')->join('hc_procedimientos as hp', 'hc.hcid', 'hp.id_hc')->get();

        if ($cta_seguro->count() > '0') {
            foreach ($cta_seguro as $value) {
                $ganancia = $ganancia + 120;

            }

            if ($ganancia > 0) {
                $vip[0] = ['vip', $ganancia];
            }
        }
        //ESTADISTICO DE ORDENES DE VENTA POR AGENDA CONSULTA
        $orden_venta_seguro = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->join('ct_orden_venta as co', 'co.id_agenda', 'agenda.id')
            ->where('co.estado', 1)
            ->where('agenda.proc_consul', 0)
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('co.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('agenda.cortesia', 'agenda.vip', 's.nombre as seguro', 's.id as sid', 's.clase')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(co.total_sin_tarjeta) as total1')
            ->selectRaw('sum(co.valor_oda) as total2')
            ->orderBy('s.clase', 'agenda.cortesia', 'agenda.vip', 's.nombre', 's.id')
            ->groupBy('agenda.cortesia', 'agenda.vip', 's.nombre', 's.id', 's.clase')
            ->get();

        $orden_venta_seguro2 = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->leftjoin('ct_orden_venta as co', 'co.id_agenda', 'agenda.id')
            ->whereNull('co.id')
            ->where('agenda.proc_consul', 0)
            ->where('agenda.estado', 1)
            ->where('agenda.estado_cita', 4)
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('co.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('agenda.cortesia', 'agenda.vip', 's.nombre as seguro', 's.id as sid', 's.clase')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(co.total_sin_tarjeta) as total1')
            ->selectRaw('sum(co.valor_oda) as total2')
            ->orderBy('s.clase', 'agenda.cortesia', 'agenda.vip', 's.nombre', 's.id')
            ->groupBy('agenda.cortesia', 'agenda.vip', 's.nombre', 's.id', 's.clase')
            ->get();
        //dd($orden_venta_seguro2);

        //dd($orden_venta_seguro);

        $orden_venta_tipo_total = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->leftjoin('ct_orden_venta as co', 'co.id_agenda', 'agenda.id')
            ->where('agenda.estado', 1)
            ->where('agenda.estado_cita', '4')
            ->where('agenda.proc_consul', 0)
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('co.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('agenda.id', 'agenda.id_paciente', 'agenda.cortesia', 'agenda.vip', 's.clase', 's.nombre', 'co.id as orden', 'co.estado', 'co.total_sin_tarjeta', 'co.valor_oda')
            ->get();

        $agenda_dia = Agenda::join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->where('agenda.estado', 1)
            ->where('agenda.estado_cita', '4')
            ->where('agenda.proc_consul', 0)
            ->where('h.id_doctor1', '1307189140')
            ->whereBetween('agenda.fechaini', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->selectRaw('date_format(agenda.fechaini,"%Y-%m-%d") as fecha')
            ->selectRaw('count(*) as cantidad')
            ->get();

        $cantidad_dias = $agenda_dia->count();

        //dd($orden_venta_tipo_total);

        $arr          = [];
        $cantidad_nt  = 0;
        $total_nt     = 0;
        $cantidad_vip = 0;
        $total_vip    = 0;
        $cantidad_top = 0;
        $total_top    = 0;
        $arr2         = [];
        $meta_valor   = 0;
        foreach ($orden_venta_tipo_total as $value) {
            if ($value->orden != null && $value->estado == '1') {
                if ($value->cortesia == 'SI') {
                    $cantidad_nt++;
                    $arr[0] = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->vip == '1') {
                    $cantidad_vip++;
                    $total_vip = $total_vip + $value->total_sin_tarjeta + $value->valor_oda;
                    $arr[1]    = ['tipo' => 'PACIENTES VIP', 'cantidad' => $cantidad_vip, 'valor' => $total_vip, 'meta_cantidad' => $cantidad_dias * 2, 'meta_valor' => $cantidad_dias * 120];
                } elseif ($value->clase != '1') {
                    $cantidad_nt++;
                    $total_nt = $total_nt + $value->total_sin_tarjeta + $value->valor_oda;
                    $arr[0]   = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->clase == '1') {
                    $cantidad_top++;
                    $total_top = $total_top + $value->total_sin_tarjeta + $value->valor_oda;
                    $arr[2]    = ['tipo' => 'PARTICULARES, BMI, BUPA, BEST', 'cantidad' => $cantidad_top, 'valor' => $total_top, 'meta_cantidad' => $cantidad_dias * 7, 'meta_valor' => $cantidad_dias * 504];
                }
            }
            if ($value->orden == null) {
                if ($value->cortesia == 'SI') {
                    $cantidad_nt++;
                    $arr[0] = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->vip == '1') {
                    $cantidad_vip++;
                    $arr[1] = ['tipo' => 'PACIENTES VIP', 'cantidad' => $cantidad_vip, 'valor' => $total_vip, 'meta_cantidad' => 0, 'meta_valor' => $cantidad_dias * 120];
                } elseif ($value->clase != '1') {
                    $cantidad_nt++;
                    $arr[0] = ['tipo' => 'CORTESIA-IESS DCTO-SEG PRIV', 'cantidad' => $cantidad_nt, 'valor' => $total_nt, 'meta_cantidad' => $cantidad_dias * 1, 'meta_valor' => 0];
                } elseif ($value->clase == '1') {
                    $cantidad_top++;
                    $arr[2] = ['tipo' => 'PARTICULARES, BMI, BUPA, BEST', 'cantidad' => $cantidad_top, 'valor' => $total_top, 'meta_cantidad' => $cantidad_dias * 7, 'meta_valor' => $cantidad_dias * 504];
                }
            }

        }

        //dd($arr[0]);

        return view('hc4/pastel/ganancia_efectiva', ['doctor_ganacia' => $doctor_ganacia, 'co_seg' => $co_seg, 'vip' => $vip, 'orden_venta_seguro' => $orden_venta_seguro, 'request' => $request, 'arr' => $arr, 'orden_venta_tipo_total' => $orden_venta_tipo_total, 'orden_venta_seguro2' => $orden_venta_seguro2]);
    }


}
