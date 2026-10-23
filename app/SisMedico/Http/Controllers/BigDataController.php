<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Examen;
use Mail;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Empresa;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Detalle_Forma_Pago;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Parametro;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Agenda;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Contable;
use Sis_medico\Ct_Empresa_Relacionada;
use Sis_medico\Ct_ventas;
use Sis_medico\Examen_Orden_Agenda;
use Sis_medico\Examen_Protocolo;
use Sis_medico\Hc_Cie10;
use Sis_medico\hc_protocolo;
use Sis_medico\UserMembresia;
use Sis_medico\Log_usuario;
use Sis_medico\Http\Controllers\ApiFacturacionController;
use Sis_medico\Http\Controllers\ticket_permisos\TicketPermisosController;
use Sis_medico\Labs_Sectores;
use Sis_medico\Log_Api;
use Sis_medico\Membresia;
use Sis_medico\Pentax;
use Sis_medico\Planilla;
use Sis_medico\Protocolo;
use Sis_medico\TicketPermiso;

class BigDataController extends Controller
{
    public function _consulta(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return "CREDENCIALES INVALIDAS";
        }

        $elemento = $request->getContent();
        //var_dump($elemento);exit();
        $data = json_decode($elemento, true);
        $empresa  = Empresa::where('id', 'like', '%%')->first();
        return json_encode($empresa);
    }

    public function consulta(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return "CREDENCIALES INVALIDAS";
        }

        $elemento = json_decode($request->getContent(), true);
        $desde = $request['desde'];
        if (is_null($desde)) {
            $desde = date("Y-m-d", strtotime(date('2022-01-01')));
        }

        $hasta = $request['hasta'];
        if (is_null($hasta)) {
            $hasta = date('Y-m-d');
        }

        $tipo = $elemento['tipo'];

        // if (is_null($tipo)) {
        //     $tipo = 0;
        // }

        $ordenes = Agenda::wherebetween('agenda.fechaini', [$desde . " 00:00:00", $hasta . " 23:59:59"])
            ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'ov.id_seguro')
            ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
            //->groupby('s.id_seguro_tipos', DB::raw('MONTH(agenda.fechaini)'))
            ->where("s.id_seguro_tipos", "<>", "null")
            ->where('st.id', '!=', '1')
            ->where('ov.estado', '1');

        if (is_null($tipo)) {
            $ordenes = $ordenes->whereIn('agenda.proc_consul', ['0', '1']);
        } else {
            if ($tipo == 0) {
                $ordenes = $ordenes->where('agenda.proc_consul', '0');
            }
            if ($tipo == 1) {
                $ordenes = $ordenes->where('agenda.proc_consul', '1');
            }
        }

        // if ($tipo == 0) {
        //     $ordenes = $ordenes->where('proc_consul', '0');
        // }

        // if ($tipo == 1) {
        //     $ordenes = $ordenes->where('proc_consul', '1');
        // }


        $meses_consul = $ordenes->select(DB::raw('MONTH(agenda.fechaini) as mes'))->groupby(DB::raw('MONTH(agenda.fechaini)'))->get();
        $api = [];
        $meses = array('Todos', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

        foreach ($meses_consul as $value) {
            $data = BigDataController::armarApi($tipo, $value->mes, $desde, $hasta);
            $mes = [
                "mes"       => $meses[$value->mes],
                "details"   => $data,
                "id_mes"    => $value->mes
            ];
            array_push($api, $mes);
        }
        return json_encode($api);
    }

    public static function sqlQuery($tipo, $desde, $hasta, $seguro_tipo, $mes)
    {
        $ordenes2 = Agenda::wherebetween('agenda.fechaini', [$desde . " 00:00:00", $hasta . " 23:59:59"])
            ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'ov.id_seguro')
            ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
            //->groupby('s.id_seguro_tipos', DB::raw('MONTH(agenda.fechaini)'))
            ->where("s.id_seguro_tipos", "<>", "null")
            ->where('st.id', '!=', '1')
            ->where('ov.estado', '1');

        // if ($tipo == 0) {
        //     $ordenes2 = $ordenes2->where('proc_consul', '0');
        // }

        // if ($tipo == 1) {
        //     $ordenes2 = $ordenes2->where('proc_consul', '1');
        // }

        if (is_null($tipo)) {
            $ordenes2 = $ordenes2->whereIn('proc_consul', ['0', '1']);
        } else {
            if ($tipo == 0) {
                $ordenes2 = $ordenes2->where('proc_consul', '0');
            }
            if ($tipo == 1) {
                $ordenes2 = $ordenes2->where('proc_consul', '1');
            }
        }

        $ordenes2 = $ordenes2->where(DB::raw('MONTH(agenda.fechaini)'), $mes)->groupby(DB::raw('MONTH(agenda.fechaini)'))->where("s.id_seguro_tipos", "{$seguro_tipo}")->select("s.id_seguro_tipos", "st.nombre", DB::raw('SUM(ov.total+ov.total_cobrar_seguro) as total'), "agenda.fechaini")->first();
        //dd($ordenes2);
        return $ordenes2;
    }
    public static function armarApi($tipo, $mes, $desde, $hasta)
    {
        $particular = BigDataController::sqlQuery($tipo, $desde, $hasta, "3", $mes);
        $privados = BigDataController::sqlQuery($tipo, $desde, $hasta, "2", $mes);
        $promo = BigDataController::sqlQuery($tipo, $desde, $hasta, "4", $mes);

        $total = [];
        if (!is_null($particular)) {
            $detalle = ["nombre" => $particular->nombre, "total" => $particular->total];
            array_push($total, $detalle);
        } else {
            $detalle = ["nombre" => "Particular", "total" => 0];
            array_push($total, $detalle);
        }

        if (!is_null($privados)) {
            $detalle = ["nombre" => $privados->nombre, "total" => $privados->total];
            array_push($total, $detalle);
        } else {
            $detalle = ["nombre" => "Privado", "total" => 0];
            array_push($total, $detalle);
        }

        if (!is_null($promo)) {
            $detalle = ["nombre" => $promo->nombre, "total" => $promo->total];
            array_push($total, $detalle);
        } else {
            $detalle = ["nombre" => "Promo", "total" => 0];
            array_push($total, $detalle);
        }
        return $total;
    }
    public function __total______anio_(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $elemento = $request->all();
                $desde = $request['desde'];
                $hasta = $request['hasta'];
                $tipo = $elemento['tipo'];
                if (is_null($desde)) {
                    // $desde = date("Y-m-d", strtotime(date('Y-m-d').'-1 year'));
                    $an = date('Y');
                    $desde = date("Y-m-d", strtotime(date($an . '-01-01')));
                }
                if (is_null($hasta)) {
                    $hasta = date('Y-m-d');
                }

                $ordenes_anio = Agenda::leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
                    ->join('seguros as s', 's.id', 'ov.id_seguro')
                    ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
                    ->where("s.id_seguro_tipos", "<>", "null")
                    ->where('st.id', '!=', '1')
                    ->where('ov.estado', '1');

                if (!is_null($desde)) {
                    $ordenes_anio = $ordenes_anio->whereBetween('agenda.fechaini', [$desde . ' 00:00:00', $hasta . ' 23:59:59']);
                } else {
                    $ordenes_anio = $ordenes_anio->where('agenda.fechaini', '<=', $hasta);
                }

                if (is_null($tipo)) {
                    $ordenes_anio = $ordenes_anio->whereIn('agenda.proc_consul', [0, 1]);
                } else {
                    if ($tipo == 0) {
                        $ordenes_anio = $ordenes_anio->where('agenda.proc_consul', '0');
                    }
                    if ($tipo == 1) {
                        $ordenes_anio = $ordenes_anio->where('agenda.proc_consul', '1');
                    }
                }

                $ordenes_anio = $ordenes_anio->groupby('s.id_seguro_tipos')
                    ->select(DB::raw('YEAR(agenda.fechaini) as anio'), 'st.id as id_tipo_seguro', "st.nombre", "st.abreviatura", DB::raw('SUM(ov.total+ov.total_cobrar_seguro) as total'), DB::raw('COUNT(agenda.id) as cantidad'))->get();
                //dd($ordenes_anio);
                return json_encode($ordenes_anio);
            } else {
                return "CREDENCIALES INVALIDAS";
            }
        } else {
            return "CREDENCIALES INVALIDAS";
        }
    }
    public function total_anio(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        try {
            $elemento = $request->all();
            $desde = $request['desde'];
            $hasta = $request['hasta'];
            $tipo = isset($elemento['tipo']) ? (string) $elemento['tipo'] : '';
            if (is_null($desde)) {
                $an = date('Y');
                $desde = date("Y-m-d", strtotime(date($an . '-01-01')));
            }
            if (is_null($hasta)) {
                $hasta = date('Y-m-d');
            }
            if ($request->input('incluir_doctores') == '1') {
                return $this->resumenAnioPorDoctor($desde, $hasta, $tipo);
            }

            $ordenes_anio = Agenda::leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
                ->join('seguros as s', 's.id', 'ov.id_seguro')
                ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
                ->where("s.id_seguro_tipos", "<>", "null")
                ->where('st.id', '!=', '1')
                ->where('ov.estado', '1')
                ->whereRaw("UPPER(TRIM(COALESCE(agenda.cortesia, 'NO'))) <> 'SI'")
                ->whereRaw('(COALESCE(ov.total, 0) + COALESCE(ov.total_cobrar_seguro, 0)) <> 0')
                ->where(function ($query) {
                    $query->whereNull('agenda.id_doctor1')
                        ->orWhereNotIn('agenda.id_doctor1', ['444444444B', '4444444444', '444444444A']);
                })
                ->where(function ($q) use ($desde, $hasta) {
                    if (!is_null($desde)) {
                        $q->whereBetween('agenda.fechaini', [$desde . ' 00:00:00', $hasta . ' 23:59:59']);
                    } else {
                        $q->where('agenda.fechaini', '<=', $hasta);
                    }
                })
                ->where(function ($r) use ($tipo) {
                    if (is_null($tipo)) {
                        $r = $r->whereIn('agenda.proc_consul', [0, 1]);
                    } else {
                        if ($tipo == 0) {
                            $r = $r->where('agenda.proc_consul', '0');
                        } else {
                            $r = $r->where('agenda.proc_consul', '1');
                        }
                    }
                })
                ->groupby('s.id_seguro_tipos')
                ->select(DB::raw('YEAR(agenda.fechaini) as anio'), 'st.id as id_tipo_seguro', "st.nombre", "st.abreviatura", DB::raw('SUM(ov.total+ov.total_cobrar_seguro) as total'), DB::raw('COUNT(agenda.id) as cantidad'))
                ->get();

            return $ordenes_anio;
        } catch (\Exception $e) {
            return ["respuesta" => $e->getMessage(), 'status' => 'error', 'linea' => $e->getLine()];
        }
    }
    private function resumenAnioPorDoctor($desde, $hasta, $tipo)
    {
        $hastaExclusiva = Carbon::parse($hasta)->addDay()->format('Y-m-d') . ' 00:00:00';
        $consulta = function ($esProcedimiento) use ($desde, $hastaExclusiva) {
            $query = DB::table('ct_orden_venta as cov')
                ->join('agenda as a', 'cov.id_agenda', '=', 'a.id')
                ->leftJoin('seguros as s', 'cov.id_seguro', '=', 's.id')
                ->leftJoin('seguro_tipos as st', 's.id_seguro_tipos', '=', 'st.id')
                ->where('cov.estado', '1')
                ->where('a.estado', '1')
                ->where('a.estado_cita', '4')
                ->where('a.proc_consul', $esProcedimiento ? '1' : '0')
                ->whereRaw("UPPER(TRIM(COALESCE(a.cortesia, 'NO'))) <> 'SI'")
                ->whereRaw('(COALESCE(cov.total, 0) + COALESCE(cov.total_cobrar_seguro, 0)) <> 0')
                ->where('a.fechaini', '>=', $desde . ' 00:00:00')
                ->where('a.fechaini', '<', $hastaExclusiva);

            if ($esProcedimiento) {
                $query->join(DB::raw('(SELECT DISTINCT id_agenda, id_doctor1 FROM pentax) as p'), 'p.id_agenda', '=', 'a.id')
                    ->join('users as u', 'p.id_doctor1', '=', 'u.id');
            } else {
                $query->join('users as u', 'a.id_doctor1', '=', 'u.id');
            }

            return $query->where('u.id_tipo_usuario', 3)
                ->select('u.id as id_doctor', 's.id_seguro_tipos as id_tipo_seguro', 'st.nombre as cobertura', 'st.abreviatura')
                ->selectRaw("TRIM(CONCAT_WS(' ', u.nombre1, u.nombre2, u.apellido1, u.apellido2)) as doctor")
                ->selectRaw(($esProcedimiento ? '1' : '0') . ' as tipo')
                ->selectRaw('COUNT(cov.id) as cantidad')
                ->selectRaw('SUM(COALESCE(cov.total, 0) + COALESCE(cov.total_cobrar_seguro, 0)) as total')
                ->groupBy('u.id', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 's.id_seguro_tipos', 'st.nombre', 'st.abreviatura')
                ->get();
        };

        $resultado = [];
        if ((string) $tipo !== '0') {
            $resultado = array_merge($resultado, $consulta(true)->all());
        }
        if ((string) $tipo !== '1') {
            $resultado = array_merge($resultado, $consulta(false)->all());
        }
        return $resultado;
    }
    public function labs_meses(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return "CREDENCIALES INVALIDAS";
        }

        $anio = $request->anio;
        if (is_null($anio)) {
            // $anio = '2023'; //hacer dinamico
            $anio = date("Y");
        }

        $examen_orden = Examen_Orden::where('examen_orden.estado', '1')
            ->where('examen_orden.anio', $anio)
            ->where('examen_orden.realizado', '1')
            ->where('s.id_seguro_tipos', '!=', 'null')
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
            ->groupBy('examen_orden.mes')
            ->select('examen_orden.mes as mes')->get();

        $arr = [];
        $meses = array('Todos', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

        foreach ($examen_orden as $value) {
            $data = BigDataController::armarLabs($value->mes);
            $mes = [
                "mes"       => $meses[$value->mes],
                "details"   => $data,
                "id_mes"    => $value->mes
            ];
            array_push($arr, $mes);
        }

        //dd($arr);

        return json_encode($arr);
    }

    public static function sql_labs($mes, $seguro_tipo)
    {
        $anio = date("Y");
        // $anio = "2023";
        $examen = Examen_Orden::where('examen_orden.estado', '1')
            ->where('examen_orden.realizado', '1')
            ->where('examen_orden.mes', $mes)
            ->where("examen_orden.anio", $anio)
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->where("s.id_seguro_tipos", "{$seguro_tipo}")
            ->join("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
            ->groupBy('examen_orden.mes')
            ->select('s.id_seguro_tipos', 'st.nombre', DB::raw('COUNT(examen_orden.id) as cantidad'), DB::raw('SUM(examen_orden.total_valor - examen_orden.recargo_valor) as total'))->first();

        //dd($examen);
        return $examen;
    }

    public static function armarLabs($mes)
    {
        $particular = BigDataController::sql_labs($mes, "3");
        $privados = BigDataController::sql_labs($mes, "2");
        $promo = BigDataController::sql_labs($mes, "4");
        $publico = BigDataController::sql_labs($mes, "1");

        $total = [];
        if (!is_null($particular)) {
            $detalle = ["nombre" => $particular->nombre, "total" => $particular->total, "cantidad" => $particular->cantidad, 'id_seguro_tipos' => $particular->id_seguro_tipos];
            array_push($total, $detalle);
        } else {
            $detalle = ["nombre" => "Particular", "total" => 0, "cantidad" => 0, 'id_seguro_tipos' => 3];
            array_push($total, $detalle);
        }

        if (!is_null($privados)) {
            $detalle = ["nombre" => $privados->nombre, "total" => $privados->total, "cantidad" => $privados->cantidad, 'id_seguro_tipos' => $privados->id_seguro_tipos];
            array_push($total, $detalle);
        } else {
            $detalle = ["nombre" => "Privado", "total" => 0, "cantidad" => 0, 'id_seguro_tipos' => 2];
            array_push($total, $detalle);
        }

        if (!is_null($promo)) {
            $detalle = ["nombre" => $promo->nombre, "total" => $promo->total, "cantidad" => $promo->cantidad, 'id_seguro_tipos' => $promo->id_seguro_tipos];
            array_push($total, $detalle);
        } else {
            $detalle = ["nombre" => "Promo", "total" => 0, "cantidad" => 0, 'id_seguro_tipos' => 4];
            array_push($total, $detalle);
        }

        if (!is_null($publico)) {
            $detalle = ["nombre" => $publico->nombre, "total" => $publico->total, "cantidad" => $publico->cantidad, 'id_seguro_tipos' => $publico->id_seguro_tipos];
            array_push($total, $detalle);
        } else {
            $detalle = ["nombre" => "Publico", "total" => 0, "cantidad" => 0, 'id_seguro_tipos' => 1];
            array_push($total, $detalle);
        }
        return $total;
    }

    public function buscar_usuario(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }

        $email = $request->email;
        if (!is_null($email)) {
            $usuario = User::where('email', $email)->orwhere('id', $email)->where('estado', '1')->first();

            if (!is_null($usuario)) {
                $imagen = '';
                // if ($imagen != ' ') {
                //     $image_2 = Storage::disk('public')->get($usuario->imagen_url);
                //     $imagen = utf8_encode(base64_decode($image_2));
                // }
                //dd($imagen);
                //$arr = [];
                $menbresia = UserMembresia::where('user_id', $usuario->id)->where('user_membresia.estado', '1')->get();

                return response()->json(["respuesta" => $usuario, "claveUser" => $usuario->password, 'status' => 'okay', "menbresia" => $menbresia, 'imagen' => $imagen]);
            } else {
                return json_encode(["respuesta" => 'No existe', 'status' => 'no', "menbresia" => 'no tiene']);
            }
        } else {
            return json_encode(["respuesta" => 'No existe', 'status' => 'no']);
        }
    }

    public function buscar_referido(Request $request)
    {

        $id_referido = $request->id_referido;
        // dd($email);
        if (!is_null($id_referido)) {
            $usuario = User::find($id_referido);
            //  dd($usuario);

            if (!is_null($usuario)) {
                return response()->json(["respuesta" => $usuario, 'status' => 'okay']);
            } else {
                return json_encode(["respuesta" => 'No existe', 'status' => 'no']);
            }
        } else {
            return json_encode(["respuesta" => 'No existe', 'status' => 'no']);
        }
    }

    // public function __________buscar_examenes___________(Request $request)
    public function buscar_examenes_new(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $id_paciente = $request->id_paciente;

                if (!is_null($request->pagina)) {
                    $actual = $request->pagina;
                    $inicio = ($actual - 1) * 5;
                } else {
                    $actual = 1;
                    $inicio = 0;
                }

                // return json_encode(["respuesta" => $id_paciente, 'status' => 'no']);
                if (!is_null($id_paciente)) {
                    $ordenes1 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                        ->join('users as usuario', 'usuario.id', 'p.id_usuario')
                        ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
                        ->where('usuario.id', $id_paciente)
                        ->where('examen_orden.estado', '1')
                        ->join('users as us_doc', 'us_doc.id', 'examen_orden.id_doctor_ieced')
                        ->orderBy('examen_orden.fecha_orden', 'desc')
                        ->limit(5)
                        ->offset($inicio)
                        ->select('examen_orden.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 's.nombre as nombre_seguro', 'us_doc.nombre1 as nombre_doctor', 'us_doc.apellido1 as apellido_doctor');

                    $paciente = Paciente::find($id_paciente);

                    if (!is_null($paciente)) {
                        if ($paciente->id != $paciente->id_usuario) {
                            $ordenes1 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                                ->join('users as usuario', 'usuario.id', 'p.id')
                                ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
                                ->where('usuario.id', $id_paciente)
                                ->where('examen_orden.estado', '1')
                                ->join('users as us_doc', 'us_doc.id', 'examen_orden.id_doctor_ieced')
                                ->orderBy('examen_orden.fecha_orden', 'desc')
                                ->limit(5)
                                ->offset($inicio)
                                ->select('examen_orden.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 's.nombre as nombre_seguro', 'us_doc.nombre1 as nombre_doctor', 'us_doc.apellido1 as apellido_doctor'); //dd($ordenes1->get());
                        }
                    }

                    $ordenes2 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                        ->join('labs_grupo_familiar as gf', 'gf.id', 'p.id')
                        ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
                        ->where('gf.id_usuario', $id_paciente)
                        ->where('gf.estado', '1')
                        ->where('examen_orden.estado', '1')
                        ->join('users as us_doc', 'us_doc.id', 'examen_orden.id_doctor_ieced')
                        ->orderBy('examen_orden.fecha_orden', 'desc')
                        ->limit(5)
                        ->offset($inicio)
                        ->select('examen_orden.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 's.nombre as nombre_seguro', 'us_doc.nombre1 as nombre_doctor', 'us_doc.apellido1 as apellido_doctor');

                    $ordenes = $ordenes1->union($ordenes2);
                    $querySql = $ordenes->toSql();
                    $ordenes = DB::table(DB::raw("($querySql order by fecha_orden desc) as a"))->mergeBindings($ordenes->getQuery());
                    $ordenes  = $ordenes->get();

                    return json_encode(['respuesta' => $ordenes, 'status' => 'okay', 'pagina' => $actual, 'cantidad' => count($ordenes)]);
                } else {
                    return json_encode(["respuesta" => 'No existe', 'status' => 'no']);
                }
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }
    }

    public function buscar_examenes(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $id_paciente = $request->id_paciente;

        if (!is_null($request->pagina)) {
            $actual = $request->pagina;
            $inicio = ($actual - 1) * 5;
        } else {
            $actual = 1;
            $inicio = 0;
        }

        if (is_null($id_paciente)) {
            return json_encode(["respuesta" => 'No existe', 'status' => 'no']);
        }

        $paciente = Paciente::find($id_paciente);
        $ordenes1 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
            // ->join('users as usuario', function ($q) use ($id_paciente, $paciente) {
            //     if (!is_null($id_paciente)) {
            //         $q->on('usuario.id', 'p.id_usuario');
            //     } else if (!is_null($paciente)) {
            //         if ($paciente->id != $paciente->id_usuario) {
            //             $q->on('usuario.id', 'p.id');
            //         }
            //     }
            // })
            ->join('users as usuario', 'usuario.id', 'p.id_usuario')
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->where('usuario.id', $id_paciente)
            ->where('examen_orden.estado', '1')
            ->join('users as us_doc', 'us_doc.id', 'examen_orden.id_doctor_ieced')
            ->orderBy('examen_orden.fecha_orden', 'desc')
            // ->limit(5)
            // ->offset($inicio)
            ->leftjoin('encuesta_labs as el', 'el.id_orden', 'examen_orden.id')
            ->selectRaw("examen_orden.id, examen_orden.anio, examen_orden.mes, examen_orden.fecha_orden, examen_orden.pct_resultado, p.nombre1, p.nombre2, p.apellido1, p.apellido2, s.nombre as nombre_seguro, us_doc.nombre1 as nombre_doctor, us_doc.apellido1 as apellido_doctor, IF(el.id_orden IS NULL,0,1) as check_encuesta, examen_orden.id_paciente");

        if (!is_null($paciente)) {
            if ($paciente->id != $paciente->id_usuario) {
                $ordenes1 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                    ->join('users as usuario', 'usuario.id', 'p.id')
                    ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
                    ->where('usuario.id', $id_paciente)
                    ->where('examen_orden.estado', '1')
                    ->join('users as us_doc', 'us_doc.id', 'examen_orden.id_doctor_ieced')
                    ->leftjoin('encuesta_labs as el', 'el.id_orden', 'examen_orden.id')
                    ->orderBy('examen_orden.fecha_orden', 'desc')
                    // ->limit(5)
                    // ->offset($inicio)
                    ->selectRaw("examen_orden.id, examen_orden.anio, examen_orden.mes, examen_orden.fecha_orden, examen_orden.pct_resultado, p.nombre1, p.nombre2, p.apellido1, p.apellido2, s.nombre as nombre_seguro, us_doc.nombre1 as nombre_doctor, us_doc.apellido1 as apellido_doctor, IF(el.id_orden IS NULL,0,1) as check_encuesta, examen_orden.id_paciente");
            }
        }

        $ordenes2 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
            ->join('labs_grupo_familiar as gf', 'gf.id', 'p.id')
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->where('gf.id_usuario', $id_paciente)
            ->where('gf.estado', '1')
            ->where('examen_orden.estado', '1')
            ->join('users as us_doc', 'us_doc.id', 'examen_orden.id_doctor_ieced')
            ->orderBy('examen_orden.fecha_orden', 'desc')
            // ->limit(5)
            // ->offset($inicio)
            ->leftjoin('encuesta_labs as el', 'el.id_orden', 'examen_orden.id')
            ->selectRaw("examen_orden.id, examen_orden.anio, examen_orden.mes, examen_orden.fecha_orden, examen_orden.pct_resultado, p.nombre1, p.nombre2, p.apellido1, p.apellido2, s.nombre as nombre_seguro, us_doc.nombre1 as nombre_doctor, us_doc.apellido1 as apellido_doctor,IF(el.id_orden IS NULL,0,1) as check_encuesta, examen_orden.id_paciente");

        $ordenes = $ordenes1->union($ordenes2)->orderBy('fecha_orden', 'DESC')->get();

        return json_encode(['respuesta' => $ordenes, 'status' => 'okay', 'pagina' => $actual, 'cantidad' => count($ordenes)]);
    }

    public function listado_examenes(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $exam  = Examen::where('examen.estado', '1')
            ->where('examen.valor', '>', 0)
            ->where('examen.id', '<>', '1124')
            ->join('examen_agrupador_sabana as s', 's.id_examen', 'examen.id')
            ->where('s.estado', 1)
            ->select('examen.id', 'examen.valor', 'examen.nombre', DB::raw('SUBSTRING(examen.nombre, 1, 1) AS initial'), 'examen.nombre_largo', 'examen.sugerencia')
            ->orderBy('examen.nombre')->get();
        return json_encode(['respuesta' => $exam, 'status' => 'okay']);
    }

    public function crear_orden_labs(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $json_request = json_decode($request->getContent());
                //dd($json_request->usuario);
                $tipo_pago            = $json_request->tipopago; //[MEM]->ES PAGO DE MEMBRESIA, [ORD]-> ES PAGO DE ORDEN
                $membresia_id         = $json_request->membresia_id; //ID DE LA MEMBRESIA ACTUAL DEL PACIENTE, SI ES 0 EL PAGO FUE HECHO SIN MEMBRESIA
                $membresia_detalle_id = $json_request->membresia_detalle_id;
                $fecha_inicio = $json_request->fecha_inicio;
                $fecha_fin = $json_request->fecha_fin;
                $ip_cliente           = $_SERVER["REMOTE_ADDR"];
                if ($membresia_id == 0) {
                    $membresia_id = null;
                }
                $id_seguro         = '1';
                $id_protocolo      = null;
                $id_nivel          = null;
                $total             = 0;
                $contador          = 0;
                $descuento_detalle = 0;
                $empresa_labs = Empresa::where('prioridad_labs', '1')->first();
                $cedula_paciente  = $json_request->id_paciente;
                $paciente = Paciente::find($cedula_paciente);

                if (is_null($paciente)) {

                    $user = user::find($cedula_paciente);

                    if (!is_null($user)) {
                        $input_pac = [

                            'id'                 => $cedula_paciente,
                            'id_usuario'       => $cedula_paciente,
                            'nombre1'            => strtoupper($json_request->usuario->nombre1),
                            'nombre2'            => strtoupper($json_request->usuario->nombre2),
                            'apellido1'          => strtoupper($json_request->usuario->apellido1),
                            'apellido2'          => strtoupper($json_request->usuario->apellido2),
                            'fecha_nacimiento'   => $json_request->usuario->fecha_nacimiento,
                            //'sexo'               => $request['sexo'],
                            'telefono1'          => $json_request->usuario->telefono1,
                            'telefono2'          => $json_request->usuario->telefono2,
                            'parentesco'         => 'Principal',
                            'parentescofamiliar' => 'Principal',
                            'tipo_documento'     => 1,
                            'id_seguro'          => 1,
                            'imagen_url'         => ' ',
                            'menoredad'          => 0,
                            'ip_creacion'        => $ip_cliente,
                            'ip_modificacion'    => $ip_cliente,
                            'id_usuariocrea'     => 'CREAUSER',
                            'id_usuariomod'      => 'CREAUSER',
                            'otros'              => 'APP LABS',

                        ];
                        Paciente::create($input_pac);
                    } else {
                        $arr_us = [
                            'id'               => $cedula_paciente,
                            'email'            => $json_request->usuario->email,
                            'nombre1'          => strtoupper($json_request->usuario->nombre1),
                            'id_tipo_usuario'  => $json_request->usuario->id_tipo_usuario,
                            'nombre2'          => strtoupper($json_request->usuario->nombre2),
                            'apellido1'        => strtoupper($json_request->usuario->apellido2),
                            'apellido2'        => strtoupper($json_request->usuario->apellido2),
                            'estado'           => '1',
                            'imagen_url'       => ' ',
                            'telefono1'        => $json_request->usuario->telefono1,
                            'telefono2'        => $json_request->usuario->telefono2,
                            'direccion'        => $json_request->usuario->direccion,
                            'tipo_documento'   => '1',
                            'id_pais'          => '1', //pendiente revisar
                            'fecha_nacimiento' => $json_request->usuario->fecha_nacimiento,
                            'password'         => $json_request->usuario->password,
                            'id_usuariocrea'   => 'CREAUSER',
                            'id_usuariomod'    => 'CREAUSER',
                            'ip_creacion'      => '::1',
                            'ip_modificacion'  => '::1',
                        ];

                        $id_usuario = User::insertGetId($arr_us);

                        $input_pac = [

                            'id'                 => $cedula_paciente,
                            'id_usuario'         => $cedula_paciente,
                            'nombre1'            => strtoupper($json_request->usuario->nombre1),
                            'nombre2'            => strtoupper($json_request->usuario->nombre2),
                            'apellido1'          => strtoupper($json_request->usuario->apellido1),
                            'apellido2'          => strtoupper($json_request->usuario->apellido2),
                            'fecha_nacimiento'   => $json_request->usuario->fecha_nacimiento,
                            //'sexo'               => $request['sexo'],
                            'telefono1'          => $json_request->usuario->telefono1,
                            'telefono2'          => $json_request->usuario->telefono2,
                            'parentesco'         => 'Principal',
                            'parentescofamiliar' => 'Principal',
                            'tipo_documento'     => 1,
                            'id_seguro'          => 1,
                            'imagen_url'         => ' ',
                            'menoredad'          => 0,
                            'ip_creacion'        => $ip_cliente,
                            'ip_modificacion'    => $ip_cliente,
                            'id_usuariocrea'     => 'CREAUSER',
                            'id_usuariomod'      => 'CREAUSER',
                            'otros'              => 'APP LABS',

                        ];
                        Paciente::create($input_pac);

                        $input_log = [
                            'id_usuario'  => 'CREAUSER',
                            'ip_usuario'  => $ip_cliente,
                            'descripcion' => "CREA NUEVO PACIENTE APP",
                            'dato_ant1'   => $json_request->id_paciente,
                            'dato1'       => strtoupper($json_request->usuario->nombre1 . " " . $json_request->usuario->nombre2 . " " . $json_request->usuario->apellido1 . " " . $json_request->usuario->apellido2),
                            'dato_ant4'   => " PARENTESCO: Principal",
                            'dato2'       => 'APP',
                        ];

                        Log_usuario::create($input_log);
                    }
                    $paciente = Paciente::find($cedula_paciente);
                }


                $input_ex = [
                    'id_paciente'          => $paciente['id'],
                    'fecha_tentativa'      => $json_request->fecha_tentativa,
                    'cedula_factura'       => $json_request->cedula_factura,
                    'nombre_factura'       => $json_request->nombre_factura,
                    'direccion_factura'    => $json_request->direccion_factura,
                    'ciudad_factura'       => $json_request->ciudad_factura,
                    'email_factura'        => $json_request->email_factura,
                    'telefono_factura'     => $json_request->telefono_factura,
                    'anio'                 => substr(date('Y-m-d'), 0, 4),
                    'mes'                  => substr(date('Y-m-d'), 5, 2),
                    'fecha_orden'          => date('Y-m-d'),
                    'id_protocolo'         => $id_protocolo,
                    'id_seguro'            => $id_seguro,
                    'id_nivel'             => $id_nivel,
                    'est_amb_hos'          => '0',
                    'id_doctor_ieced'      => '1234517896',
                    'doctor_txt'           => 'COTIZACION GENERADA EN APP MÒVIL',
                    'observacion'          => '',
                    'id_empresa'           => $empresa_labs->id,
                    'cantidad'             => '0',
                    'estado'               => '-1',
                    'realizado'            => '0',
                    'total_valor'          => $json_request->total,
                    'valor'                => $json_request->total,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => '1234517896',
                    'id_usuariomod'        => '1234517896',
                    'motivo_descuento'     => '',
                    'membresia_id'         => $membresia_id,
                    'membresia_detalle_id' => $membresia_detalle_id,
                    'observacion_app'      => $json_request->observacion_app,
                    'observacion_adicional' => 'COTIZACION GENERADA EN APP LABS'
                ];
                $id_orden = Examen_Orden::insertGetId($input_ex);
                foreach ($json_request->detalles as $pexamen) {
                    //CREA DETALLE
                    $detalle = Examen_Detalle::where('id_examen_orden', $id_orden)->where('id_examen', $id_orden)->first();
                    if (is_null($detalle)) {
                        $contador++;
                        $examen = Examen::find($pexamen->id);
                        $valor    = $pexamen->total; //AQUI PONERMOS EL VALOR YA CON DESCUENTO DE MEMBRESIA Y TODO DE LA APP
                        $cubre    = 'NO';
                        $ex_nivel = Examen_Nivel::where('id_examen', $pexamen->id)->where('nivel', $id_nivel)->first();
                        if (!is_null($ex_nivel)) {
                            if ($ex_nivel->valor1 != 0) {
                                $valor = $ex_nivel->valor1;
                                $cubre = 'SI';
                            }
                        }
                        $input_det = [
                            'id_examen_orden' => $id_orden,
                            'id_examen'       => $pexamen->id,
                            'valor'           => $valor,
                            'cubre'           => $cubre,
                            'p_descuento'     => $pexamen->pdiscount,
                            'valor_descuento' => $pexamen->discount,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => '1234517896',
                            'id_usuariomod'   => '1234517896',
                        ];
                        Examen_detalle::create($input_det);
                        $total += $valor;
                        $descuento_detalle += $pexamen->discount;
                    }
                }

                if ($request['pres_dom'] == '1') {
                    // //[1] Domicilio, [0] Presencial
                    // $examen = Examen::find('1203');
                    // if (!is_null($examen)) {
                    //     $input_det = [
                    //         'id_examen_orden' => $id_orden,
                    //         'id_examen'       => $examen->id,
                    //         'valor'           => $examen->valor,
                    //         'ip_creacion'     => $ip_cliente,
                    //         'ip_modificacion' => $ip_cliente,
                    //         'id_usuariocrea'  => '1234517896',
                    //         'id_usuariomod'   => '1234517896',
                    //         'human_labs'      => 0,
                    //         'p_descuento'     => 0,
                    //         'valor_descuento' => 0,
                    //     ];
                    //     Examen_detalle::create($input_det);
                    //     $total = $total + $examen->valor;
                    //     //$contador++;
                    // }
                }

                $total             = $total;
                $recargo_p         = 0;
                $subtotal_pagar    = $total - $descuento_detalle;
                $recargo_valor     = $subtotal_pagar * $recargo_p / 100;
                $recargo_valor     = $recargo_valor;
                $valor_total       = $subtotal_pagar + $recargo_valor;
                $valor_total       = $valor_total;

                if ($tipo_pago == "ORD") {
                    $orden     = Examen_Orden::find($id_orden);
                    $input_ex2 = [
                        'recargo_valor'   => $recargo_valor,
                        'total_valor'     => $total - $descuento_detalle,
                        'cantidad'        => $contador,
                        'valor'           => $total,
                        'descuento_valor' => $descuento_detalle,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'   => '1234517896',
                        'estado_pago'     => '0',
                        'pres_dom'        => $request->pres_dom,
                    ];
                    $orden->update($input_ex2);
                }

                $apps_orden = DB::table('apps_orden')->insertGetId(
                    [
                        'id_orden' => $id_orden,
                        'id_usuario' => $paciente['id'],
                        'estado' => 1,
                        'total' => $valor_total
                    ]
                );
                $id_agenda = "";
                if (!is_null($fecha_inicio) && !is_null($fecha_fin)) {
                    $id_agenda = $this->crea_agenda_labs($id_orden, $fecha_inicio, $fecha_fin);
                }


                return json_encode(['respuesta' => $id_orden, 'status' => 'okay', 'agenda' => $id_agenda]);
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }
    }

    public function buscar_orden(Request $request)
    {

        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $id_paciente = $request->id_paciente;
        $ordenes = Examen_Orden::where('id_paciente', $id_paciente)->where('estado', '-1')->orderBy('id', 'desc')->get();

        $arr_orden = [];

        foreach ($ordenes as $ord) {
            $detalle = $ord->detalles;
            $cab = [
                'id'                => $ord->id,
                'id_paciente'       => $ord->id_paciente,
                'nombre_paciente'   => $ord->paciente->nombre1 . ' ' . $ord->paciente->nombre2,
                'apellido_paciente' => $ord->paciente->apellido1 . ' ' . $ord->paciente->apellido2,
                'anio'              => $ord->anio,
                'mes'               => $ord->mes,
                'cantidad'          => $ord->cantidad,
                'fecha_orden'       => $ord->fecha_orden,
                'id_seguro'         => $ord->seguro->nombre,
                'tipo_seguro'       => $ord->seguro->tipo,
                'valor'             => $ord->total_valor,
                'doctor'            => $ord->doctor->nombre1 . ' ' . $ord->doctor->apellido1,
                'subtotal'          => round($ord->valor, 2),
                'descuento'         => round($ord->descuento_valor, 2),
                'total_final'       => round($ord->total_valor, 2),
                'url'               => url('labs/cotizacion/externos/imprimir') . '/' . $ord->id,
                'url_visualizar'    => url('/bd/view_receta') . '/' . $ord->id . '/6',
                'id_ciudad'         => $request->id_ciudad,
                'nombre_ciudad'     => $request->nombre_ciudad,
                'domicilio'         => is_null($ord->pres_dom) ? 0 : $ord->pres_dom,
                'por_desc'          => $ord->descuento_p,
                'examenes'          => []
            ];
            $detallearr = [];
            foreach ($detalle as $det) {
                $arr_det = [
                    'id_examen'       => $det->id_examen,
                    'id_examen_orden' => $det->id_examen_orden,
                    'examen'          => $det->examen->nombre,
                    'cantidad'        => $det->cantidad,
                    'precio_unitario' => $det->valor,
                    'porc_descuento'  => $det->p_descuento,
                    'descuento'       => is_null($det->valor_descuento) ? 0 : $det->valor_descuento,

                ];

                array_push($detallearr, $arr_det);
            }
            $cab['examenes'] = $detallearr;
            array_push($arr_orden, $cab);
        }
        // return $arr_orden;

        return json_encode(['respuesta' => $arr_orden, 'status' => 'okay']);
    }

    public function buscarUserSucursal(Request $request)
    {
        $id_usuario = $request->id_usuario;
        if (!is_null($id_usuario)) {
            $usuario = User::where('id', $id_usuario)->where('estado', '1')->first();

            return response()->json(["respuesta" => $usuario, "claveUser" => $usuario->password, 'status' => 'okay']);
        } else {
            return json_encode(["respuesta" => 'No existe', 'status' => 'no']);
        }
    }

    public function creaUsuarioSucursal(Request $request)
    {
        //return $request->all();
        $data = $request->data;
        $id_usuario = $data['id'];
        $user = User::find($id_usuario);

        if (is_null($user)) {

            $arr_us = [
                'id'               => $data['id'],
                'email'            => $data['email'],
                'nombre1'          => strtoupper($data['nombre1']),
                'id_tipo_usuario'  => $data['id_tipo_usuario'],
                'nombre2'          => strtoupper($data['nombre2']),
                'apellido1'        => strtoupper($data['apellido1']),
                'apellido2'        => strtoupper($data['apellido2']),
                'estado'           => '1',
                'imagen_url'       => ' ',
                'telefono1'        => $data['telefono1'],
                'telefono2'        => $data['telefono2'],
                'direccion'        => $data['direccion'],
                'tipo_documento'   => $data['tipo_documento'],
                'id_pais'          => $data['id_pais'], //pendiente revisar
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'password'         => $request->pass,
                'ciudad'           => $data['ciudad'],
                'direccion'        => $data['direccion'],
                'ocupacion'        => $data['ocupacion'],
                'departamento'     => $data['departamento'],
                'cargo'            => $data['cargo'],
                'id_usuariocrea'   => 'CREAUSER',
                'id_usuariomod'    => 'CREAUSER',
                'ip_creacion'      => '::1',
                'ip_modificacion'  => '::1',
            ];

            User::create($arr_us);

            return json_encode(['respuesta' => 'Usuario creado', 'status' => 'ok']);
        } else {

            return json_encode(['respuesta' => 'Usuario ya existe', 'status' => 'no']);
        }
    }

    public function actualizar_usuario(Request $request)
    {
        $id_usuario = $request->id_usuario;

        $user = User::find($id_usuario);

        if (!is_null($user)) {
            $arr_up = [
                'id_tipo_usuario' => $request->tipo_usuario,
                'id_usuariomod'    => 'CREAUSER',
                'ip_modificacion'  => 'actualizar api',
            ];

            $user->update($arr_up);
            return json_encode(['respuesta' => 'Usuario Actualizado', 'status' => 'ok']);
        } else {
            return json_encode(['respuesta' => 'NO EXISTE', 'status' => 'no']);
        }
    }

    public function consulta_ventas(Request $request)
    {

        $elemento = json_decode($request->getContent(), true);

        $fecha_desde = $request['fecha_desde'];
        if (is_null($fecha_desde)) {
            $fecha_desde = date("Y-m-d");
        }

        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }

        $empresa = Empresa::where('prioridad', 1)->where('estado', 1)->first();

        $empresas_relacionadas = Ct_Empresa_Relacionada::where('estado', 1)->get();
        $arr_e = [];
        foreach ($empresas_relacionadas as $relacionada) {
            array_push($arr_e, $relacionada->id_cliente);
        }

        $ventas = Ct_ventas::where('id_empresa', $empresa->id)->where('estado', 1)->whereIn('ct_ventas.tipo', ['VEN-FA', 'VENFA-CO']);
        $venta_sin_seguro = Ct_ventas::where('id_empresa', $empresa->id)->where('estado', 1)->whereIn('ct_ventas.tipo', ['VEN-FA', 'VENFA-CO']);

        if (!is_null($fecha_desde)) {
            $ventas             = $ventas->whereBetween('fecha', [$fecha_desde, $fecha_hasta]);
            $venta_sin_seguro   = $venta_sin_seguro->whereBetween('fecha', [$fecha_desde, $fecha_hasta]);
        } else {
            $ventas = $ventas->where('fecha', '<=', $fecha_hasta);
            $venta_sin_seguro = $venta_sin_seguro->where('fecha', '<=', $fecha_hasta);
        }

        if (isset($arr_e)) {
            $ventas = $ventas->whereNotIn('id_cliente', $arr_e);
            $venta_sin_seguro = $venta_sin_seguro->whereNotIn('id_cliente', $arr_e);
            $venta_clienter = Ct_ventas::where('id_empresa', $empresa->id)->where('estado', 1)->whereIn('ct_ventas.tipo', ['VEN-FA', 'VENFA-CO']);
            if (!is_null($fecha_desde)) {
                $venta_clienter     = $venta_clienter->whereBetween('fecha', [$fecha_desde, $fecha_hasta]);
            } else {
                $venta_clienter = $venta_clienter->where('fecha', '<=', $fecha_hasta);
            }

            $venta_clienter = $venta_clienter->whereIn('id_cliente', $arr_e)->select(
                DB::raw('SUM(ct_ventas.subtotal_12) as subtotal12'),
                DB::raw('SUM(ct_ventas.subtotal_0) as subtotal0'),
                DB::raw('SUM(ct_ventas.descuento) as descuento'),
                DB::raw('COUNT(ct_ventas.id) AS cantidadf'),
                DB::raw('SUM(ct_ventas.impuesto) as impuesto')
            )->get();
        } else {
            $venta_clienter = [];
        }

        $venta_sin_seguro = $venta_sin_seguro->whereNull('seguro_paciente')->select(
            DB::raw('SUM(ct_ventas.subtotal_12) as subtotal12'),
            DB::raw('SUM(ct_ventas.subtotal_0) as subtotal0'),
            DB::raw('SUM(ct_ventas.descuento) as descuento'),
            DB::raw('COUNT(ct_ventas.id) AS cantidadf'),
            DB::raw('SUM(ct_ventas.impuesto) as impuesto')
        )->get();



        $total = [];
        $ventas = $ventas->join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')
            ->groupBy('s.tipo')->select(
                's.tipo',
                DB::raw('SUM(ct_ventas.subtotal_12) as subtotal12'),
                DB::raw('SUM(ct_ventas.subtotal_0) as subtotal0'),
                DB::raw('SUM(ct_ventas.descuento) as descuento'),
                DB::raw('COUNT(ct_ventas.id) AS cantidadf'),
                DB::raw('SUM(ct_ventas.impuesto) as impuesto')
            )->get();
        // dd($ventas);
        foreach ($ventas as $venta) {

            $total = [
                "subtotal0" => is_null($venta->subtotal0) ? 0 : $venta->subtotal0,
                "subtotal12" => is_null($venta->subtotal12) ? 0 : $venta->subtotal12,
                "descuento" => is_null($venta->descuento) ? 0 : $venta->descuento,
                "impuesto"  => is_null($venta->impuesto) ? 0 : $venta->impuesto,
                "cantidad"  => is_null($venta->cantidadf) ? 0 : $venta->cantidadf
            ];

            if ($venta->tipo == 0) {
                $data["publico"] = $total;
            }
            if ($venta->tipo == 1) {
                $data["privado"]  = $total;
            }
            if ($venta->tipo == 2) {
                $data["particular"] = $total;
            }
        }

        $total_sin_seguro = [];

        foreach ($venta_sin_seguro as $ventasin) {
            $total_sin_seguro = [
                "subtotal0" => is_null($ventasin->subtotal0) ? 0 : $ventasin->subtotal0,
                "subtotal12" => is_null($ventasin->subtotal12) ? 0 : $ventasin->subtotal12,
                "descuento" => is_null($ventasin->descuento) ? 0 : $ventasin->descuento,
                "impuesto"  => is_null($ventasin->impuesto) ? 0 : $ventasin->impuesto,
                "cantidad"  => is_null($ventasin->cantidadf) ? 0 : $ventasin->cantidadf
            ];

            $data["particular_sin_seguro"] = $total_sin_seguro;
        }

        $total_relacionado = [];

        foreach ($venta_clienter as  $relacionado) {

            $total_relacionado = [
                "subtotal0" => is_null($relacionado->subtotal0) ? 0 : $relacionado->subtotal0,
                "subtotal12" => is_null($relacionado->subtotal12) ? 0 : $relacionado->subtotal12,
                "descuento" => is_null($relacionado->descuento) ? 0 : $relacionado->descuento,
                "impuesto"  => is_null($relacionado->impuesto) ? 0 : $relacionado->impuesto,
                "cantidad"  => is_null($relacionado->cantidadf) ? 0 : $relacionado->cantidadf
            ];

            $data["particular_relacionado"] = $total_relacionado;
        }


        $ap_plantilla = Planilla::where('planilla_cabecera.estado', '1')->where('planilla_cabecera.aprobado', '1')
            ->join('agenda as a', 'a.id', 'planilla_cabecera.id_agenda')
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->where('s.tipo', '0');

        //consultas 

        $consultas = Agenda::where('agenda.estado', 1)->where('agenda.proc_consul', 0)->where('agenda.estado_cita', 4)
            ->join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->where('s.tipo', 0);

        //laboratorio

        $examen_orden = Examen_Orden::where('examen_orden.estado', '1')
            ->where('examen_orden.realizado', '1')
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->where('s.tipo', 0);



        if (!is_null($fecha_desde)) {
            $ap_plantilla = $ap_plantilla->whereBetween('a.fechaini', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
            $consultas = $consultas->whereBetween('agenda.fechaini', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
            $examen_orden = $examen_orden->whereBetween('examen_orden.fecha_orden', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else {
            $ap_plantilla = $ap_plantilla->where('a.fechaini', '<=', $fecha_hasta);
            $consultas = $consultas->where('agenda.fechaini', '<=', $fecha_hasta);
            $examen_orden = $examen_orden->where('examen_orden.fecha_orden', '<=', $fecha_hasta);
        }

        $ap_plantilla = $ap_plantilla->sum('total_estimado');
        $consultas = $consultas->count();
        $examen_orden = $examen_orden->sum('total_valor');

        $valor_consulta = $consultas * 17.5;

        $total_estimado = $valor_consulta + $ap_plantilla + $examen_orden;


        //$total_estimado_new = $this->valor_publico($fecha_desde, $fecha_hasta);

        $valores0 = [
            "subtotal0" => 0,
            "subtotal12" => 0,
            "descuento" => 0,
            "impuesto"  => 0,
            "cantidad"  => 0
        ];

        if (!isset($data["particular"])) {
            $data["particular"] = $valores0;
        }

        if (!isset($data["particular_sin_seguro"])) {
            $data["particular_sin_seguro"] = $valores0;
        }

        if (!isset($data["particular_relacionado"])) {
            $data["particular_relacionado"] = $valores0;
        }

        if (!isset($data["privado"])) {
            $data["privado"] = $valores0;
        }

        if (!isset($data["publico"])) {
            $data["publico"] = $valores0;
        }

        //$data["publico_estimado"] = round($total_estimado_new, 2);


        return $data;
    }

    public static function valor_publico($fecha_desde, $fecha_hasta)
    {

        $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.estado', '1')
            // ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->whereBetween('archivo_plano_cabecera.fecha_ing', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            // ->where('tseg.tipo_principal',$grupo->tipo_principal)
            ->where('apd.estado', '1')
            ->select('archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tseg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'archivo_plano_cabecera.id as arplaca', 'apd.subtotal', 'apd.valor_porcent_clasifi', 'archivo_plano_cabecera.id_seguro', 'apd.porcent_10')
            ->orderby('archivo_plano_cabecera.id_paciente')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();
        //dd($archivo_plano);
        $sumatoria = [];
        $sumiva_ap = 0;
        $total = 0;
        $admin = 0;
        foreach ($archivo_plano as  $cabecera) {

            $iva_ap = $cabecera->valor_unitario * $cabecera->porcentaje_iva * $cabecera->cantidad;
            if ($cabecera->id_seguro == '5') {
                $iva_ap = round($iva_ap, 2);
            }
            $sumiva_ap += $iva_ap;
            //    $total += $cabecera->subtotal;

            if ($cabecera->id_seguro == '5') {
                $total += $cabecera->subtotal + $cabecera->valor_porcent_clasifi;
            } else {
                $total += $cabecera->subtotal;
            }

            //$admin += $cabecera->porcentaje10;

            if ($cabecera->id_seguro == '6' or $cabecera->id_seguro == '3') {
                $adm = $cabecera->subtotal * $cabecera->porcent_10;
                $admin += $adm;
            } else {
                $admin += $cabecera->porcentaje10;
            }
        }

        $sumatoria =  $sumiva_ap + $total + $admin;

        return $sumatoria;
    }

    public function ConsultaProcedimientos(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $elemento = json_decode($request->getContent(), true);

        $fecha_desde = $request['fecha_desde'];
        if (is_null($fecha_desde)) {
            $fecha_desde = date("Y-m-d");
        }

        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }

        $pentax = Pentax::where('pentax.estado_pentax', '4')
            ->join('pentax_procedimiento as pp', 'pp.id_pentax', 'pentax.id')
            ->join('agenda as a', 'a.id', 'pentax.id_agenda')
            ->where('a.estado', '1');

        if (!is_null($fecha_desde)) {
            $pentax = $pentax->whereBetween('a.fechaini', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else {
            $pentax = $pentax->where('a.fechaini', '<=', $fecha_hasta);
        }

        $pentax = $pentax->join('procedimiento as proc', 'proc.id', 'pp.id_procedimiento')->groupBy('pp.id_procedimiento')
            ->whereNotNull('proc.id_grupo_procedimiento')
            ->select('proc.nombre as nombre_proocedimiento', DB::raw('COUNT(pp.id_procedimiento) AS CantProc'), 'pp.id_procedimiento as id_procedimiento')
            ->orderBy(DB::raw('COUNT(pp.id_procedimiento)'), 'desc')->get();

        // dd($pentax);
        $total = [];
        foreach ($pentax as $p) {
            $arr = [
                'id_procedimiento'      => $p->id_procedimiento,
                'nombre_procedimiento'  => $p->nombre_proocedimiento,
                'cantidad'              => $p->CantProc
            ];
            array_push($total, $arr);
        }

        return $total;
    }

    public function consultaCie(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $elemento = json_decode($request->getContent(), true);

        $fecha_desde = $request['fecha_desde'];
        if (is_null($fecha_desde)) {
            $fecha_desde = date("Y-m-d");
        }

        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }

        $id_cie = $request['id_cie'];
        $global = [];

        for ($i = 0; $i < count($id_cie); $i++) {

            $hc_cie = Hc_Cie10::join('historiaclinica as h', 'h.hcid', 'hc_cie10.hcid')
                ->join('agenda as a', 'a.id', 'h.id_agenda')
                ->where('a.estado_cita', '4')
                ->whereNotNull("hc_cie10.presuntivo_definitivo")
                ->where('a.estado', '1');

            if (!is_null($fecha_desde)) {
                $hc_cie = $hc_cie->whereBetween('a.fechaini', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
            } else {
                $hc_cie = $hc_cie->where('a.fechaini', '<=', $fecha_hasta);
            }

            if (!is_null($request['id_cie'])) {
                $hc_cie = $hc_cie->where('hc_cie10.cie10', $id_cie[$i]);
            }

            // $hc_cie = $hc_cie->groupBy('hc_cie10.cie10', 'hc_cie10.ingreso_egreso', 'hc_cie10.presuntivo_definitivo')
            $hc_cie = $hc_cie->groupBy('hc_cie10.cie10', "hc_cie10.ingreso_egreso", "hc_cie10.presuntivo_definitivo")->select(
                'hc_cie10.cie10',
                'hc_cie10.ingreso_egreso',
                'hc_cie10.presuntivo_definitivo',
                DB::raw("COUNT(hc_cie10.cie10) as cantCie10")
            )
                ->orderBy("hc_cie10.cie10", "asc")->get();


            $consulta_prev = ["valor" => 0];
            $consulta_def = ["valor" => 0];
            $consulta = [];


            $proce_ingr_pres = ["valor" => 0, "nombre" => "Presuntivo"];
            $proce_ingr_def = ["valor" => 0, "nombre" => "Definitivo"];

            $proce_egr_pres = ["valor" => 0,  "nombre" => "Presuntivo"];
            $proce_egr_def = ["valor" => 0,   "nombre" => "Definitivo"];

            $proce_ingreso = [];
            $proce_egreso = [];

            $procedimiento  = [];

            foreach ($hc_cie as $value) {
                if (is_null($value->ingreso_egreso)) { //Consulta
                    if ($value->presuntivo_definitivo == "DEFINITIVO") {

                        $consulta_def["valor"] += $value->cantCie10;
                    } else {

                        $consulta_prev["valor"] += $value->cantCie10;
                    }
                } else { //Procedimiento
                    // dd($value);
                    if ($value->ingreso_egreso == "INGRESO" and $value->presuntivo_definitivo == "PRESUNTIVO") {

                        $proce_ingr_pres["valor"] += $value->cantCie10;
                    } else if ($value->ingreso_egreso == "INGRESO" and $value->presuntivo_definitivo == "DEFINITIVO") {

                        $proce_ingr_def["valor"] += $value->cantCie10;
                    } else if ($value->ingreso_egreso == "EGRESO" and $value->presuntivo_definitivo == "PRESUNTIVO") {

                        $proce_egr_pres["valor"] += $value->cantCie10;
                    } else if ($value->ingreso_egreso == "EGRESO" and $value->presuntivo_definitivo == "DEFINITIVO") {

                        $proce_egr_def["valor"] += $value->cantCie10;
                    }
                }
            }

            $consulta = [[
                "nombre" => "Presuntivo",

                "valor" => $consulta_prev["valor"]
            ], [
                "nombre" => "Definitivo",

                "valor" => $consulta_def["valor"]
            ]];

            $proce_ingreso = [$proce_ingr_pres, $proce_ingr_def];
            $proce_egreso = [$proce_egr_pres, $proce_egr_def];

            $procedimiento = [
                ["nombre" => "ingreso",  "valor" => $proce_ingreso],
                ["nombre" => "egreso", "valor" => $proce_egreso]
            ];

            array_push($global, [
                "id" => $id_cie[$i],
                "valores" => [
                    ["nombre" => "Consulta", "valor" => $consulta],
                    ["nombre" => "Procedimiento", "valor" => $procedimiento]
                ]
            ]);
        }

        return $global;
    }

    public function busqueda_cie10(Request $request)
    {
        $cie = [];
        $cie_3 = Cie_10_3::where('estado', 1)->select('id', 'descripcion');
        $cie_4 = Cie_10_4::where('estado', 1)->select('id', 'descripcion');

        $cie = $cie_3->union($cie_4)->get();

        return $cie;
    }

    public function search_cie10(Request $request)
    {
        $cie = [];
        $nombre = $request->id_cie;
        $cie_3 = Cie_10_3::select('id as value', DB::raw("CONCAT(id,' - ', descripcion) as label"))
            ->where('estado', 1)
            ->where(function ($q) use ($nombre) {
                if (!is_null($nombre)) {
                    $q->where('descripcion', 'like', '%' . $nombre . '%')
                        ->orWhere('id', $nombre);
                }
            });
        $cie_4 = Cie_10_4::select('id as value', DB::raw("CONCAT(id,' - ', descripcion) as label"))
            ->where('estado', 1)
            ->where(function ($q) use ($nombre) {
                if (!is_null($nombre)) {
                    $q->where('descripcion', 'like', '%' . $nombre . '%')
                        ->orWhere('id', 'like', '%' . $nombre . '%');
                }
            });

        $cie = $cie_3->union($cie_4)->take(15)->get();

        return $cie;
    }

    public function new_orden_pago(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $json_request = json_decode($request->getContent(), true);

                //VARIABLES P&F
                $_pasarela_pagos_detalle  = "";
                $_pasarela_pagos_subtotal = 0;
                $_pasarela_pagos_iva      = 0;
                $_pasarela_pagos_total    = 0;

                //VARIABLES REQUEST
                $tipo_pago            = $json_request['tipopago']; //[MEM]->ES PAGO DE MEMBRESIA, [ORD]-> ES PAGO DE ORDEN
                $membresia_id         = $json_request['membresia_id']; //ID DE LA MEMBRESIA ACTUAL DEL PACIENTE, SI ES 0 EL PAGO FUE HECHO SIN MEMBRESIA
                $membresia_detalle_id = $json_request['membresia_detalle_id'];
                $ip_cliente           = $_SERVER["REMOTE_ADDR"];

                //USUARIO
                $cedula_usuario = $json_request['id_usuario'];
                $email_usuario  = $json_request['email_usuario'];
                if ($membresia_id == 0) {
                    $membresia_id = null;
                }

                //PACIENTE
                $cedula_paciente  = $json_request['id_paciente'];
                $celular_paciente = $json_request['celular_paciente'];

                ////////////EMPRESA / CAJA / SUCURSAL ///////////
                $empresa    = Empresa::where('prioridad_labs', 1)->first();
                $id_empresa = $empresa->id;
                $sucursal   = $empresa->establecimiento;
                $caja       = $empresa->punto_emision;

                /*$id_empresa = $json_request['empresa'];
                $caja       = $json_request['caja'];
                $sucursal   = $json_request['sucursal'];*/
                /////////////////////////////////////////////////

                //FACTURA
                $telefono_factura = $json_request['telefono_factura'];
                $email_factura    = $json_request['email_factura'];

                //VARIABLES
                $RUC_LABS          = '0993075000001';
                $ref_id            = "";
                $id_seguro         = '1';
                $id_protocolo      = null;
                $id_nivel          = null;
                $total             = 0;
                $contador          = 0;
                $usuario_mail      = null;
                $paciente          = null;
                $user              = null;
                $id_orden          = 0;
                $descuento_detalle = 0;

                // VALIDACIONES DE USUARIO Y PACIENTE
                $paciente = Paciente::find($cedula_paciente);

                //CREA ORDEN_EXAMEN ú ORDEN_MEMBRESIA
                if ($tipo_pago == "ORD") {
                    //Es un pago de examenes
                    $input_ex = [
                        //'id_paciente'      => $paciente->id,
                        'id_paciente'          => $paciente['id'],
                        'fecha_tentativa'      => $json_request['fecha_tentativa'], // json _ request men
                        'cedula_factura'       => $json_request['cedula_factura'],
                        'nombre_factura'       => $json_request['nombre_factura'],
                        'direccion_factura'    => $json_request['direccion_factura'],
                        'ciudad_factura'       => $json_request['ciudad_factura'],
                        'email_factura'        => $json_request['email_factura'],
                        'telefono_factura'     => $json_request['telefono_factura'],
                        'anio'                 => 0,
                        'mes'                  => 0,
                        'id_protocolo'         => $id_protocolo,
                        'id_seguro'            => $id_seguro,
                        'id_nivel'             => $id_nivel,
                        'est_amb_hos'          => '0',
                        'id_doctor_ieced'      => '1234517896',
                        'doctor_txt'           => 'PAGADO EN APP MOVIL',
                        'observacion'          => '',
                        'id_empresa'           => $id_empresa,
                        'cantidad'             => '0',
                        'estado'               => '0',
                        'realizado'            => '0',
                        'valor'                => '0',
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => '1234517896',
                        'id_usuariomod'        => '1234517896',
                        'motivo_descuento'     => '',
                        //'fecha_orden'      => date('Y-m-d h:i:s'),
                        //'codigo'           => '0001',
                        'membresia_id'         => $membresia_id,
                        'membresia_detalle_id' => $membresia_detalle_id,
                    ];
                    $id_orden = Examen_Orden::insertGetId($input_ex);
                    //ARMA EL REF_ID
                    $ref_id = "ORD" . $id_orden . "-" . $id_empresa . "-" . $sucursal . "-" . $caja; //pivot para p&f
                    //ITERATES ORDER DETAILS
                    foreach ($json_request['detalles'] as $pexamen) {
                        //CREA DETALLE
                        $detalle = Examen_Detalle::where('id_examen_orden', $id_orden)->where('id_examen', $id_orden)->first();
                        if (is_null($detalle)) {
                            $contador++;
                            //$examen = Examen::find($pexamen->id_examen);
                            $examen = Examen::find($pexamen['id']);
                            //return $examen;
                            //$valor    = $examen->valor;
                            $valor    = $pexamen['total']; //AQUI PONERMOS EL VALOR YA CON DESCUENTO DE MEMBRESIA Y TODO DE LA APP
                            $cubre    = 'NO';
                            $ex_nivel = Examen_Nivel::where('id_examen', $pexamen['id'])->where('nivel', $id_nivel)->first();
                            if (!is_null($ex_nivel)) {
                                if ($ex_nivel['valor1'] != 0) {
                                    $valor = $ex_nivel['valor1'];
                                    $cubre = 'SI';
                                }
                            }
                            $input_det = [
                                'id_examen_orden' => $id_orden,
                                //'id_examen'       => $examen->id,
                                'id_examen'       => $pexamen['id'],
                                'valor'           => $valor,
                                'cubre'           => $cubre,
                                'p_descuento'     => $pexamen['pdiscount'],
                                'valor_descuento' => $pexamen['discount'],
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => '1234517896',
                                'id_usuariomod'   => '1234517896',
                            ];
                            Examen_detalle::create($input_det);
                            /// orden
                            $total += $valor;
                            $descuento_detalle += $pexamen['discount'];
                        }
                    }
                    if ($request['pres_dom'] == '1') {
                        //[1] Domicilio, [0] Presencial
                        //$examen = Examen::find('1203');
                        // $examen = Examen::where('nombre', 'TOMA A DOMICILIO')->first();
                        // //dd($value,$examen);
                        // if (!is_null($examen)) {
                        //     $input_det = [
                        //         'id_examen_orden' => $id_orden,
                        //         'id_examen'       => $examen['id'],
                        //         'valor'           => $examen['valor'],
                        //         'ip_creacion'     => $ip_cliente,
                        //         'ip_modificacion' => $ip_cliente,
                        //         'id_usuariocrea'  => '1234517896',
                        //         'id_usuariomod'   => '1234517896',
                        //         'human_labs'      => 0,
                        //         'p_descuento'     => 0,
                        //         'valor_descuento' => 0,
                        //     ];
                        //     Examen_detalle::create($input_det);
                        //     $total = $total + $examen['valor'];
                        //     //$contador++;
                        // }
                    }
                } else if ($tipo_pago == "MEM") {
                    //Es un pago de membresia
                    foreach ($json_request['detalles'] as $pexamen) {
                        $valor = $pexamen['total'];
                        $total += $valor;
                    }
                    $membresia = Membresia::find('2');
                    if ($membresia != null) {
                        $data_array = array(
                            "tipo"                    => "MEM-VEN", //tipo de de orden de venta
                            "fecha"                   => date('Y-m-d h:i:s'), //fecha de orden
                            "id_empresa"              => $RUC_LABS, //empresa
                            "divisas"                 => "1", //cualquiera
                            "nombre_cliente"          => $paciente['nombre1_paciente'] . ' ' . $paciente['nombre2_paciente'] . ' ' . $paciente['apellido1_paciente'] . ' ' . $paciente['apellido2_paciente'], //nombre del cliente
                            "tipo_consulta"           => "1", //esto clavado
                            "identificacion_cliente"   => $cedula_paciente, // ci de cliente
                            "direccion_cliente"       => $paciente['direccion_paciente'], //direccion del cliente
                            "telefono_cliente"        => $paciente['celular_paciente'], // telefono de cliente
                            "mail_cliente"            => $email_usuario, //mail del cliente
                            "orden_venta"             => "0", //idde referencia
                            "identificacion_paciente"  => $cedula_paciente, // ci de paciente
                            "nombre_paciente"         => $paciente['nombre1_paciente'] . ' ' . $paciente['nombre2_paciente'] . ' ' . $paciente['apellido1_paciente'] . ' ' . $paciente['apellido2_paciente'], // nonbre de paciente
                            "id_seguro"               => "1", //de la tabla seguros,
                            "subtotal_01"             => $total,
                            "subtotal_121"            => $total,
                            "descuento1"              => "0.00",
                            "tarifa_iva1"             => "0.00",
                            "base"                    => $total,
                            "totalc"                  => $total,
                            "valor_contable"          => $total,
                            "details"                 => array(
                                array(
                                    "codigo"     => 1,
                                    "nombre"     => $membresia['nombre'], ///<<<<<<<---------
                                    "cantidad"   => "1",
                                    "precio"     => $total,
                                    "extendido"  => "0.00",
                                    "iva"        => "1",
                                    "descpor"    => "0",
                                    "descuento"  => "0",
                                    "copago"     => "0",
                                    "detalle"    => $membresia['nombre'],
                                    "precioneto" => $total,

                                ),
                            ),
                            "id_usuario"              => $paciente['id'],
                        );
                        $response = Contable::build_data($data_array);
                        $id_orden = $response['ven_orden'];
                        //ARMA EL REF_ID
                        $ref_id = "MEM" . $id_orden; //pivot para p&f

                    }
                }

                //ACTUALIZA TOTALES
                $total             = round($total, 2);
                $recargo_p         = 0;
                $descuento_detalle = round($descuento_detalle, 2);
                $subtotal_pagar    = $total - $descuento_detalle;
                $recargo_valor     = $subtotal_pagar * $recargo_p / 100;
                $recargo_valor     = round($recargo_valor, 2);
                $valor_total       = $subtotal_pagar + $recargo_valor;
                $valor_total       = round($valor_total, 2);
                $id_mem = null;
                if ($tipo_pago == "ORD") {
                    //ES UNA ORDEN DE PAGO
                    //ACTUALIZA ORDEN DE EXAMEN
                    $orden     = Examen_Orden::find($id_orden);
                    $input_ex2 = [
                        'recargo_valor'   => $recargo_valor,
                        'total_valor'     => $valor_total,
                        'cantidad'        => $contador,
                        'valor'           => $total,
                        'descuento_valor' => $descuento_detalle,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => '1234517896',
                        'pago_online'     => '1',
                        'estado_pago'     => '0',
                        'pres_dom'        => $request['pres_dom'],
                    ];
                    $orden->update($input_ex2);
                } else {
                    $id_mem = UserMembresia::insertGetId([
                        'user_id'           => $json_request['id_usuario'],
                        'membresia_id'      => '2',
                        'fecha_compra'      => date('Y-m-d H:i:s'),
                        'meses'             => '12',
                        'valor_pagado'      => $json_request['total'],
                        'meses_contratados' => '12',
                        'referido'          => $json_request['referido'],
                        'id_usuariocrea'    => $json_request['id_usuario'],
                        'id_usuariomod'     => $json_request['id_usuario'],
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                        'estado'            => 0,
                    ]);
                }

                //LOG DEL SISTEMA DE QUIEN CRE LA ORDEN
                Log_usuario::create([
                    'id_usuario'  => '1234517896',
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "GENERA COTIZACION MOVIL",
                    'dato_ant1'   => $cedula_paciente,
                    'dato1'       => strtoupper($paciente['nombre1_paciente'] . " " . $paciente['nombre2_paciente'] . " " . $paciente['apellido1_paciente'] . " " . $paciente['apellido2_paciente']),
                ]);

                /*
            INVOCA API DE PAGOS&FACTURAS Y RECIBE EL LINK DEL VPOS
             */
                $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
                $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';
                $_pasarela_pagos_subtotal = number_format($valor_total, 2);
                $_pasarela_pagos_iva      = 0;
                $_pasarela_pagos_total    = number_format($valor_total, 2);
                $pyf_checkout_url         = 'https://vpos.accroachcode.com/';
                $pyf_details              = array();

                if ($tipo_pago == "ORD") {
                    $xtipo = 'examen_orden';
                    array_push($pyf_details, array(
                        "sku"      => '' . $id_orden . '',
                        "name"     => 'LABS APP Orden de laboratorio #' . $id_orden,
                        "qty"      => 1,
                        "price"    => "TORBI10",
                        "tax"      => 0.00,
                        "discount" => 0.00, //falta revisar
                        "total"    => "TORBI10",
                    ));
                } else if ($tipo_pago == "MEM") {
                    $xtipo = 'pago_membresia';
                    array_push($pyf_details, array(
                        "sku"      => '' . $id_orden . '',
                        "name"     => 'Membresía a LABS. #' . $id_orden,
                        "qty"      => 1,
                        "price"    => "TORBI10",
                        "tax"      => 0.00,
                        "discount" => 0.00, //falta revisar
                        "total"    => "TORBI10",
                    ));
                }

                if (strlen($telefono_factura) < 10) {
                    $telefono_factura = '0900000001';
                }

                //CLEANING NAMES AND SURENAMES
                $nombres_factura            = '';
                $apellidos_factura          = '';
                $nombres_apellidos_facturas = explode(" ", $json_request['nombre_factura']);
                $nombres_factura            = $nombres_apellidos_facturas[0];
                for ($i = 1; $i < count($nombres_apellidos_facturas); $i++) {
                    $apellidos_factura .= $nombres_apellidos_facturas[$i];
                }
                if (count($nombres_apellidos_facturas) == 4) {
                    $nombres_factura   = $nombres_apellidos_facturas[0] . ' ' . $nombres_apellidos_facturas[1];
                    $apellidos_factura = $nombres_apellidos_facturas[2] . ' ' . $nombres_apellidos_facturas[3];
                }

                // SI NO TIENE SEGUNDO APELLIDO O NOMBRE NO ENVIAR
                //json de invocación
                $data_array = array(
                    "company"        => $RUC_LABS,
                    "person"         => array(
                        "document"     => $json_request['cedula_factura'],
                        "documentType" => $this->getDocumentType($json_request['cedula_factura']),
                        //"name"         => $this->cleanNames(strtoupper($request['nombre1']) . ' ' . strtoupper($request['nombre2'])),
                        //"surname"      => $this->cleanNames(strtoupper($request['apellido1']) . ' ' . strtoupper($request['apellido2'])),
                        "name"         => $this->cleanNames(strtoupper($nombres_factura)),
                        "surname"      => $this->cleanNames(strtoupper($apellidos_factura)),
                        "email"        => $email_factura,
                        "mobile"       => $telefono_factura,
                    ),
                    "paymentRequest" => array(
                        "orderId"     => '' . $ref_id . '',
                        "description" => "Compra en linea labs", //PONER EN CONFIGURACION
                        "items"       => array(
                            "item" => $pyf_details, //pending
                        ),
                        "amount"      => array(

                            "taxes"    => array(
                                array(
                                    "kind"   => "Iva",
                                    "amount" => 0.00,
                                    "base"   => "TORBI10",
                                ),
                            ),

                            "currency" => "USD",
                            "total"    => "TORBI10",
                        ),
                    ),
                    //"returnUrl"      => "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_retorno_pago?orderid=" . $ref_id, //URL DE RETORNO
                    //"cancelUrl"      => "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_cancelacion_pago?orderid=" . $ref_id, //URL DE CANCELACION
                    "returnUrl"      => "http://ieced.siaam.ec/sis_medico/public/api/returnUrl?orderid=" . $ref_id, //URL DE RETORNO, orderid not used in link for now
                    "cancelUrl"      => "http://ieced.siaam.ec/sis_medico/public/api/cancelUrl?orderid=" . $ref_id, //URL DE CANCELACION, orderid not used in link for now
                    "userAgent"      => "labs_ec/1",
                );

                $requestId = '';
                $manage    = json_encode($data_array);
                $manage2   = json_encode($request->getContent());
                $manage = str_replace('"TORBI10"', $_pasarela_pagos_subtotal, $manage);

                //dd($manage);
                Log_Api::create([
                    'id_usuario'  => '131626193',
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "ENVIO API",
                    'url'         => '13',
                    'dato1'       => $manage,
                    'dato2'       => $manage2,
                ]);
                $make_call = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/create/', $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
                $response  = json_decode($make_call, true);
                if ($response['status'] != null) {
                    $requestId = $response['requestId'];  //crear en pago en linea 
                    if ($response['status']['status'] == 'success') {
                        ////////////////////////////////////
                        $pagoenlinea = DB::table('pagosenlinea')->where('request_id', $requestId)->first();
                        if (is_null($pagoenlinea)) {
                            $fecha_hoy = date('Y-m-d');
                            $id_pago_linea = DB::table('pagosenlinea')->insertGetId(
                                [
                                    'tipo'          => $xtipo,
                                    'clave'         => $ref_id,
                                    'request_id'    => $requestId,
                                    //'ref_request_id'=> $refRequestId, 
                                    'fecha'         => $fecha_hoy,
                                    'pasarela'      => 'PAGOS&FACTURAS',
                                    //'estado'        => $status, 
                                    'empresa_id'    => $id_empresa,
                                    'sucursal'      => $sucursal,
                                    'caja'          => $caja,
                                    'fecha_inicio'  => $request->fecha_inicio,
                                    'fecha_fin'     => $request->fecha_fin,
                                ]
                            );
                        }
                        ////////////////////////////////////
                        $pyf_checkout_url = $response['processUrl'];
                    } else {
                        $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=' . $response['status']['status'];
                    }
                } else {
                    $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=bad request';
                }
                if ($id_mem != null) {
                    $memx = UserMembresia::find($id_mem);
                    $memx->requestId = $requestId;
                    $memx->save();
                }
                /*
                RESPUESTA P&F
                {
                "status": {
                "status": "success",
                "message": "payment request successfully created",
                "reason": "",
                "date": "2021-09-09T14:51:17-05:00"
                },
                "requestId": "7501",
                "processUrl": "https://vpos.accroachcode.com/sandbox/40GesdOhDl6Dg23mavMyHM"
                }
                */

                //RETURNS JSON RESPONSE
                //return ['estado' => 'ok', 'usuario' => $xusuario->id, 'url_vpos' => $pyf_checkout_url]; //AGREGAMOS LA URL DE BOTON DE PAGOS
                return response()->json([
                    'estado'          => 'ok',
                    'usuario'         => $request['id_usuario'],
                    'ref_id'          => $ref_id,
                    'total'           => $total,
                    'url_vpos'        => $pyf_checkout_url,
                    'request_id'      => $requestId,
                    'paciente_id'     => $paciente['id'],
                    'paciente_id_req' => $cedula_paciente,
                ]);
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }
    }

    public function getDocumentType($document)
    {
        if (strlen($document) == 10) {
            return '05'; //CEDULA
        } else if (strlen($document) == 13) {
            return '04'; //RUC
        } else {
            return '06'; //PASAPORTE
        }
    }

    public function getNonce($n)
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }

    public function cleanNames($valor)
    {
        $vowels = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "'", '"', "|", "^");
        return str_replace($vowels, "", $valor);
    }

    public function callAPI($method, $url, $data, $appId, $appSecret)
    {
        $Nonce   = $this->getNonce(12);
        $Date    = date('c');
        $Token   = base64_encode(sha1($Nonce . $Date . $appSecret));

        Log_Api::create([
            'id_usuario'  => '0922290697',
            'ip_usuario'  => 'TEST',
            'descripcion' => $data,
            'url'         => $appId,
            'dato1'       => $Token,
            'dato2'       => $Date,
            'dato3'       => base64_encode($Nonce),
        ]);
        /*
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $Date . "\r\n" . "Nonce: " . base64_encode($Nonce) . "\r\n",
                'method'  => 'POST',
                'content' => $data,
            ),
        );
        $context = stream_context_create($options);*/
        $headers = array(
            'Content-type: application/json',
            'AppId: '.$appId,
            'Token: '.$Token,
            'Seed: '.$Date,
            'Nonce: '.base64_encode($Nonce)
        );
        $ch = curl_init();
        //dd($data);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error al realizar la solicitud: ' . curl_error($ch);
            exit;
        } else {
            dd($result);
            echo $result;
        }

        curl_close($ch);
        return $result;
        //return file_get_contents($url, false, $context);
    }



    public function actualizar_orden_pago_en_linea(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $id_orden = $request->id_orden;
                $fecha_inicio = $request->fecha_inicio;
                $fecha_fin = $request->fecha_fin;
                $orden = Examen_Orden::find($id_orden);
                //return json_encode(['orden' => $orden, 'status' => 'okay']);
                if (!is_null($orden)) {

                    $orden->estado_pago = "1"; //aqui se actualiza el estado
                    $orden->estado      = "1";
                    $orden->realizado   = "1";
                    $orden->save();
                    Log_usuario::create([
                        'id_usuario'  => $orden->paciente->id_usuario,
                        'ip_usuario'  => 'API',
                        'descripcion' => "LABORATORIO",
                        'dato_ant1'   => $orden->id,
                        'dato1'       => $orden->id_paciente,
                        'dato_ant2'   => "SE APRUEBA PAGO DE LA ORDEN",
                        'dato2'       => ' ',
                    ]);

                    $id_agenda = $this->crea_agenda_labs($orden->id, $fecha_inicio, $fecha_fin);

                    //crear la agenda

                    return json_encode(['respuesta' => $orden, 'status' => 'okay']);
                } else {

                    return json_encode(["respuesta" => 'ORDEN NO ENCONTRADA', 'status' => 'no']);
                }
            } else {

                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }
    }

    public function buscador_ticket_permisos(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        $estado = $request->estado;

        if (is_null($fecha_desde)) {
            $an = date('Y');
            $fecha_desde = date("Y-m-d", strtotime(date($an . '-01-01')));
        }


        $tickets = TicketPermiso::where('ticket_permiso.estado', '1')->join('users as u', 'u.id', 'ticket_permiso.cedula');

        if (!is_null($estado)) {
            $tickets = $tickets->where('ticket_permiso.estado_solicitud', $estado);
        }

        if (!is_null($fecha_desde)) {
            $tickets = $tickets->whereBetween('ticket_permiso.fecha_registro', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else {
            $tickets = $tickets->where('ticket_permiso.fecha_registro', '<=', $fecha_hasta);
        }

        $tickets = $tickets->orderBy('ticket_permiso.created_at', 'ASC')->select('ticket_permiso.*', DB::raw('CONCAT(u.nombre1," ",u.apellido1," ", u.apellido2) as nombre_usuario'))->get();

        return json_encode(['respuesta' => $tickets, 'status' => 'okay']);
    }

    public function ticket_permiso_edit(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }
        $id_ticket = $request->id_ticket;
        $ticket = TicketPermiso::where('ticket_permiso.id', $id_ticket)->join('users as u', 'u.id', 'ticket_permiso.cedula')->select('ticket_permiso.*', DB::raw('CONCAT(u.nombre1," ",u.apellido1," ", u.apellido2) as nombre_usuario'), 'u.servicios')->first();

        return $ticket;
    }

    public function ticket_permiso_guardar(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }

        try {
            $registro = TicketPermiso::find($request->id_ticket);

            $registro->update([
                'id_usuariomod'  => $request->id_usuario,
                'ip_modificacion' => '::1',
                'estado_solicitud' => $request->estado,
                'justificacion_final' => $request->obs_acepta,
            ]);

            $usuario = $registro->nombre;
            $usuario->update(['servicios' => $request->servicios]);


            $permiso = TicketPermiso::find($request->id_ticket);
            $correo  = $permiso->nombre->email;
            $array   = ['nombre' => $permiso->nombre->apellido1 . ' ' . $permiso->nombre->apellido2 . ' ' . $permiso->nombre->nombre1 . ' ' . $permiso->nombre->nombre2, 'estado_solicitud' => $permiso->estado_solicitud, 'justificacion_final' => $permiso->justificacion_final];
            Mail::send('ticket_permiso.mail', $array, function ($msj) use ($correo) {
                $msj->subject('Respuesta de Solicitud de Permiso');
                $msj->to($correo);
                $msj->from('rrhh@ieced.ec');
                $msj->bcc('torbi10@hotmail.com');
            });


            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage()];
        }
    }

    public static function crea_agenda_labs($id_orden, $fecha_inicio, $fecha_fin)
    {
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $orden          = Examen_Orden::find($id_orden);
        $fecha_orden    = $orden->fecha_orden;
        $proc_consul    = 0;
        $estado_cita    = 0;
        $cortesia       = "NO";
        $doctor_inicial = '';
        $sala           = '';
        $espid          = '10';
        $tipo_cita      = 1;
        $id_seguro      = $orden->id_seguro;
        $est_amb_hos    = 0;
        if ($orden->pres_dom == 0) {
            // $doctor_inicial = '444444444A';
            // $sala = 32;
            $sala = 22;
            $doctor_inicial = '4444444444'; //sucursales porque no tienen domicilio
        } else {
            // $doctor_inicial = '444444444B';
            // $sala = 34;
            $doctor_inicial = '4444444444'; //sucursales porque no tienen domicilio
            $sala = 22;

        }

        $input_historia = [
            'fechaini'        => $fecha_inicio,
            'fechafin'        => $fecha_fin,
            'id_paciente'     => $orden->id_paciente,
            'id_doctor1'      => $doctor_inicial,
            'proc_consul'     => $proc_consul,
            'id_empresa'      => $orden->id_empresa,
            'id_sala'         => $sala,
            'espid'           => $espid,
            'tipo_cita'       => $tipo_cita,
            'estado_cita'     => $estado_cita,
            'observaciones'   => $orden->observacion_adicional,
            'est_amb_hos'     => $est_amb_hos,
            'id_seguro'       => $id_seguro,
            'estado'          => 1,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $doctor_inicial,
            'id_usuariomod'   => $doctor_inicial,
            'cortesia'        => $cortesia,
            'estado_app'      => 1
        ];
        $nueva_agenda = Agenda::insertGetId($input_historia);

        $input_orden = [
            'id_agenda'       => $nueva_agenda,
            'id_orden'        => $orden->id,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $doctor_inicial,
            'id_usuariomod'   => $doctor_inicial,
        ];

        $id_examen_orden_agenda = Examen_Orden_Agenda::insertGetId($input_orden);

        Log_usuario::create([
            'id_usuario'  => $doctor_inicial,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $orden->id . ' - ' . $nueva_agenda,
            'dato1'       => $orden->paciente->id,
            'dato_ant2'   => "AGENDA ORDEN CREADA POR APP LABS",
            'dato_ant4'   => $fecha_orden,
        ]);

        return $nueva_agenda;
    }

    public function estadistico_rc_pacientes(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }


        $seguro = $request->id_seguro;
        $fecha_desde  = $request->fecha_desde;
        $fecha_hasta  = $request->fecha_hasta;
        if ($fecha_desde == null) {
            $fecha_desde = date('Y-m-d');
        }

        if ($fecha_hasta == null) {
            $fecha_hasta = date('Y-m-d');
        }

        $venorden = DB::table('ct_orden_venta as ct_orden')
            ->where('ct_orden.estado', 1)
            ->join('seguros as s', 's.id', 'ct_orden.id_seguro')
            ->join('agenda as a', 'a.id', 'ct_orden.id_agenda')
            ->join('paciente as p', 'p.id', 'a.id_paciente')
            ->where('s.tipo', '<>', '0');

        if (!is_null($seguro)) {
            $venorden = $venorden->whereIn('ct_orden.id_seguro', $seguro);
        }

        if (!is_null($fecha_desde)) {
            $venorden = $venorden->whereBetween('ct_orden.fecha_emision', [$fecha_desde, $fecha_hasta]);
        } else {
            $venorden = $venorden->where('ct_orden.fecha_emision', '<=', $fecha_hasta);
        }
        $venorden = $venorden->groupBy('ct_orden.id_seguro', 'p.origen', 'p.origen2')->select('s.nombre', 'p.origen', 'p.origen2', DB::raw('count(p.id) as cantidad'))->get();

        return $venorden;
    }

    public function consulta_examen_orden(Request $request)
    {

        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        if (is_null($fecha_desde)) {
            $fecha_desde = date("Y-m-d");
        }
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }

        $examen_orden = Examen_Orden::where('examen_orden.realizado', '1')->join('seguros as s', 's.id', 'examen_orden.id_seguro')->where('examen_orden.estado', '1');


        if (!is_null($fecha_desde)) {
            $examen_orden = $examen_orden->whereBetween('examen_orden.fecha_orden', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else {
            $examen_orden = $examen_orden->where('examen_orden.fecha_orden', '<=', $fecha_hasta);
        }

        $examen_orden = $examen_orden->groupBy('s.tipo')->select(DB::raw('count(examen_orden.id) as cantidad'), DB::raw('sum(examen_orden.total_valor) as total'), 's.tipo')->orderBy('s.tipo', 'ASC')->get();
        $sum_total = 0;
        $sum_cant = 0;
        foreach ($examen_orden as $orden) {
            $sum_total += $orden->total;
            $sum_cant += $orden->cantidad;
            $arr = [
                'cantidad' => $orden->cantidad,
                'total' => round($orden->total, 2),

            ];

            if ($orden->tipo == 0) {
                $data['publicas'] = $arr;
            }

            if ($orden->tipo == 1) {
                $data['privados'] = $arr;
            }

            if ($orden->tipo == 2) {
                $data['particular'] = $arr;
            }
        }

        if (!isset($data['publicas'])) {
            $data['publicas'] = [
                'cantidad' => 0,
                'total' => 0,
            ];
        }

        if (!isset($data['privados'])) {
            $data['privados'] = [
                'cantidad' => 0,
                'total' => 0,
            ];
        }

        if (!isset($data['particular'])) {
            $data['particular'] = [
                'cantidad' => 0,
                'total' => 0,
            ];
        }

        $data['total'] = round($sum_total, 2);
        $data['total_cantidad'] = $sum_cant;


        return json_encode(['respuesta' => $data, 'status' => 'okay']);
    }

    public function query_ventas(Request $request){
        
        $ventas = Ct_ventas::where('estado', '1')->where("id_empresa", Session::get("id_empresa"))->get();

        return $ventas;

    }

    public function actualiza_ventas(Request $request){

        $data = $request['ventas'];

        foreach ($data as $value) {
            $examen = Examen_Orden::where('comprobante',$value->nro_comprobante)->first();

            if(!is_null($examen)){
                $arr = [
                    'check_orden' => '1',
                    'id_examen_orden' => $examen->id,

                ];

                $value->update($arr);
            }
        }
    }

    
    public function domicilios(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $exam  = Examen::where('examen.estado', '1')
            ->where('examen.valor', '>', 0)
            ->where('examen.id', '<>', '1124')
            ->join('examen_agrupador_sabana as s', 's.id_examen', 'examen.id')
            ->where('s.estado', 1)
            ->where('examen.nombre', 'like', '%domicilio%')
            ->select('examen.id', 'examen.valor', 'examen.nombre', DB::raw('SUBSTRING(examen.nombre, 1, 1) AS initial'), 'examen.nombre_largo', 'examen.sugerencia')
            ->orderBy('examen.nombre')->get();

        return $exam;
    }

    public function sectores(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $sectores = Labs_Sectores::where('estado', 1)->orderBy('nombre')->get();

        $arr_sectores = array();
        foreach ($sectores as $value) {
            $arr = [
                'id' => $value->id,
                'nombre' => $value->nombre,
            ];
            array_push($arr_sectores, $arr);
        }

        return $arr_sectores;
    }

    public function domicilios_sector(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $sector = Labs_Sectores::find($request->id_sector);
        $arr_examenes = array();

        if (isset($sector->sector_examenes)) {

            foreach ($sector->sector_examenes as  $value) {

                $examen = $value->examen;

                $arr = [
                    'id' => $examen->id,
                    'nombre' => $examen->nombre,
                    'valor' => $examen->valor,
                ];
                array_push($arr_examenes, $arr);
            }
        }

        return $arr_examenes;
    }

    public function validate_agenda_labs(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $ini2   = date_create($request['fecha_inicio']);
        $fin2   = date_create($request['fecha_fin']);
        $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin    = date_format($fin, 'Y/m/d H:i:s');
        $pres_dom = $request['pres_dom'];
        $id_doctor = '';
        $fecha_hoy = date('Y-m-d H:i:s');

        // $mensajes = [

        //     'inicio.date'               => 'fecha mal agregada.',
        //     'fin.date'                  => 'fecha mal agregada.',

        // ];

        if (is_null($inicio)) {
            return ["respuesta" => 'Agregue una fecha de Inicio.', 'status' => 'error'];
        }

        if (is_null($fin)) {
            return ["respuesta" => 'Agregue una fecha de Fin.', 'status' => 'error'];
        }

        if ($inicio > $fin) {
            return ["respuesta" => 'La fecha de inicio debe ser antes que la de fin.', 'status' => 'error'];
        }

        if ($inicio < $fecha_hoy) {
            return ["respuesta" => 'la fecha de inicio debe ser después de la fecha actual', 'status' => 'error'];
        }

        if ($fin < $fecha_hoy) {
            return ["respuesta" => 'la fecha de fin debe ser después de la fecha actual', 'status' => 'error'];
        }

        if ($fin < $inicio) {
            return ["respuesta" => 'la fecha de fin debe ser después de la fecha de inicio', 'status' => 'error'];
        }

        if ($pres_dom == 0) {
            $id_doctor = '4444444444';
            // $id_doctor = '4444444444'; sucursales porque no tienen domicilio
        } else {
            $id_doctor = '4444444444';
        }
        $dato2  = DB::table('agenda')->where(function ($query) use ($request, $inicio, $fin, $id_doctor) {
            return $query->where('id_doctor1', '=', $id_doctor)
                ->orWhere('id_doctor2', '=', $id_doctor)
                ->orWhere('id_doctor3', '=', $id_doctor);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(
                        function ($query) use ($request, $inicio, $fin) {
                            $query->whereRaw("'" . $fin . "' BETWEEN fechaini and fechafin)");
                        }
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();



        $cant_agenda = $dato2->count();

        if ($cant_agenda >= 1) {
            return ["respuesta" => 'La fecha seleccionada esta ocupada', 'status' => 'error'];
        } else {
            return ["respuesta" => 'La fecha seleccionada esta libre', 'status' => 'okay'];
        }
    }

    public function reporte_research(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return response()->json(['message' => 'CREDENCIALES INVALIDAS', 'status' => 'error'], 401);
        }

        $fecha_inicio = trim((string) $request->input('fecha_inicio', ''));
        $fecha_fin = trim((string) $request->input('fecha_fin', ''));
        $cie10 = trim((string) $request->input('cie10', ''));
        $palabra_clave = trim((string) $request->input('palabra_clave', ''));
        $procedimiento = trim((string) $request->input('procedimiento', ''));
        $busqueda_general = trim((string) $request->input('busqueda_general', ''));
        $anonimizar = filter_var($request->input('anonimizar', false), FILTER_VALIDATE_BOOLEAN);
        $exportar = filter_var($request->input('exportar', false), FILTER_VALIDATE_BOOLEAN);
        $usar_paginacion = $request->has('page') || $request->has('per_page');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
            return response()->json(['message' => 'El rango de fechas es obligatorio y debe ser válido.'], 422);
        }
        if ($fecha_inicio > $fecha_fin) {
            return response()->json(['message' => 'La fecha inicial no puede ser posterior a la fecha final.'], 422);
        }
        if ($cie10 === '' && $palabra_clave === '' && $procedimiento === '') {
            return response()->json(['message' => 'Debe indicar un CIE10, una palabra clínica o un procedimiento.'], 422);
        }

        $datos = hc_protocolo::join('historiaclinica as h', 'hc_protocolo.hcid', '=', 'h.hcid')
            ->join('paciente as p', 'h.id_paciente', '=', 'p.id')
            ->join('agenda as a', 'h.id_agenda', '=', 'a.id')
            ->leftjoin('seguros as s', 's.id', 'a.id_seguro');

        if ($cie10 !== '') {
            $datos->whereExists(function ($query) use ($cie10) {
                $query->select(DB::raw(1))
                    ->from('hc_cie10 as hccie')
                    ->whereColumn('hccie.hcid', 'h.hcid')
                    ->where('hccie.cie10', $cie10);
            });
        }

        if ($procedimiento !== '') {
            $datos->whereExists(function ($query) use ($procedimiento) {
                $query->select(DB::raw(1))
                    ->from('hc_procedimiento_final as hpf')
                    ->join('procedimiento as prop', 'hpf.id_procedimiento', '=', 'prop.id')
                    ->whereColumn('hpf.id_hc_procedimientos', 'hc_protocolo.id_hc_procedimientos')
                    ->where('prop.observacion', 'LIKE', '%' . $procedimiento . '%');
            });
        }

        if ($palabra_clave !== '') {
            $terminos = preg_split('/\s+/', $palabra_clave, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($terminos as $termino) {
                $datos->where(function ($query) use ($termino) {
                    $busqueda = '%' . $termino . '%';
                    $query->where('hc_protocolo.conclusion', 'LIKE', $busqueda)
                        ->orWhere('hc_protocolo.hallazgos', 'LIKE', $busqueda);
                });
            }
        }

        if ($busqueda_general !== '') {
            $terminos_generales = preg_split('/\s+/', $busqueda_general, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($terminos_generales as $termino) {
                $datos->where(function ($query) use ($termino) {
                    $busqueda = '%' . $termino . '%';
                    $query->where('h.id_paciente', 'LIKE', $busqueda)
                        ->orWhere('p.nombre1', 'LIKE', $busqueda)
                        ->orWhere('p.nombre2', 'LIKE', $busqueda)
                        ->orWhere('p.apellido1', 'LIKE', $busqueda)
                        ->orWhere('p.apellido2', 'LIKE', $busqueda)
                        ->orWhere('a.fechaini', 'LIKE', $busqueda)
                        ->orWhere('s.nombre', 'LIKE', $busqueda)
                        ->orWhere('hc_protocolo.conclusion', 'LIKE', $busqueda)
                        ->orWhere('hc_protocolo.hallazgos', 'LIKE', $busqueda)
                        ->orWhereExists(function ($cieQuery) use ($busqueda) {
                            $cieQuery->select(DB::raw(1))
                                ->from('hc_cie10 as hccie_busqueda')
                                ->whereColumn('hccie_busqueda.hcid', 'h.hcid')
                                ->where('hccie_busqueda.cie10', 'LIKE', $busqueda);
                        });
                });
            }
        }

        $datos->whereBetween('a.fechaini', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);

        $total = (clone $datos)->count('hc_protocolo.id');
        $consulta = $datos->select(
            'hc_protocolo.id as id_protocolo',
            'h.id_paciente',
            DB::raw('CONCAT_WS(" ", p.nombre1, p.nombre2, p.apellido1, p.apellido2) AS NOMBRE'),
            'a.fechaini as fecha_procedimiento',
            DB::raw('IF(p.sexo = 1,"MASCULINO","FEMENINO") AS sexo, year(a.fechaini) - year(p.fecha_nacimiento) - (RIGHT(a.fechaini, 5) < RIGHT(p.fecha_nacimiento, 5)) AS edad'),
            'p.fecha_nacimiento AS fecha_nacimiento',
            'hc_protocolo.conclusion',
            'hc_protocolo.hallazgos',
            's.nombre as nombre_seguro'
        )->orderBy('a.fechaini', 'DESC')->orderBy('hc_protocolo.id', 'DESC');

        $anonimizar_resultados = function ($filas) use ($anonimizar) {
            if (!$anonimizar) {
                return $filas;
            }
            return $filas->map(function ($fila) {
                $fila->id_paciente = 'PAC-' . strtoupper(substr(hash_hmac('sha256', (string) $fila->id_paciente, (string) config('app.key')), 0, 10));
                $fila->NOMBRE = 'Paciente anonimizado';
                return $fila;
            });
        };

        if ($exportar) {
            $limite_exportacion = 10000;
            $filas = $anonimizar_resultados($consulta->limit($limite_exportacion)->get());
            return response()->json([
                'data' => $filas,
                'total' => $total,
                'truncated' => $total > $limite_exportacion,
            ]);
        }

        if (!$usar_paginacion) {
            return $anonimizar_resultados($consulta->get());
        }

        $por_pagina = min(100, max(10, (int) $request->input('per_page', 20)));
        $total_paginas = max(1, (int) ceil($total / $por_pagina));
        $pagina = min($total_paginas, max(1, (int) $request->input('page', 1)));
        $filas = $anonimizar_resultados($consulta->forPage($pagina, $por_pagina)->get());

        return response()->json([
            'data' => $filas,
            'pagination' => [
                'page' => $pagina,
                'per_page' => $por_pagina,
                'total' => $total,
                'total_pages' => $total_paginas,
            ],
        ]);
    }

    public function reporte_det_consultas(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];

        $id_cie = $request['cie10'];
        $palabra_clave = trim((string) $request->input('palabra_clave', ''));
        $global = [];

        $hc_cie = Hc_Cie10::join('historiaclinica as h', 'h.hcid', 'hc_cie10.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('paciente as p', 'p.id', 'a.id_paciente')
            ->join('hc_evolucion as he', 'he.hcid', 'h.hcid')
            ->where('a.estado_cita', '4')
            ->whereNotNull("hc_cie10.presuntivo_definitivo")
            ->where('a.estado', '1')
            ->where('a.proc_consul', '0');

        if (!is_null($fecha_desde)) {
            $hc_cie = $hc_cie->whereBetween('a.fechaini', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else {
            $hc_cie = $hc_cie->where('a.fechaini', '<=', $fecha_hasta);
        }

        if (!is_null($request['cie10']) && trim((string) $request['cie10']) !== '') {
            $hc_cie = $hc_cie->where('hc_cie10.cie10', $id_cie);
        }

        if ($palabra_clave !== '') {
            $terminos = preg_split('/\s+/', $palabra_clave, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($terminos as $termino) {
                $hc_cie = $hc_cie->where(function ($query) use ($termino) {
                    $busqueda = '%' . $termino . '%';
                    $query->where('a.id_paciente', 'LIKE', $busqueda)
                        ->orWhere('p.nombre1', 'LIKE', $busqueda)
                        ->orWhere('p.nombre2', 'LIKE', $busqueda)
                        ->orWhere('p.apellido1', 'LIKE', $busqueda)
                        ->orWhere('p.apellido2', 'LIKE', $busqueda)
                        ->orWhere('hc_cie10.cie10', 'LIKE', $busqueda)
                        ->orWhere('hc_cie10.presuntivo_definitivo', 'LIKE', $busqueda)
                        ->orWhere('he.motivo', 'LIKE', $busqueda)
                        ->orWhere('he.cuadro_clinico', 'LIKE', $busqueda);
                });
            }
        }

        $hc_cie = $hc_cie->select('a.id_paciente', DB::raw('CONCAT(p.nombre1, " ", p.nombre2, " ", p.apellido1, " ", p.apellido2) AS NOMBRE'), 'a.fechaini as fecha_procedimiento', DB::raw('IF(p.sexo = 1,"MASCULINO","FEMENINO") AS sexo, year(a.fechaini) - year(p.fecha_nacimiento) - (RIGHT(a.fechaini, 5) < RIGHT(p.fecha_nacimiento, 5)) AS edad'), 'p.fecha_nacimiento', 'a.proc_consul', 'a.fechaini', 'hc_cie10.ingreso_egreso', 'hc_cie10.presuntivo_definitivo', 'hc_cie10.cie10', 'he.motivo', 'he.cuadro_clinico')->get();
        return $hc_cie;
    }

    public function det_ordenes_seguros(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }

        $id_tipo_seguro = $request['id_tipo_seguro'];
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];

        $examen_orden = Examen_Orden::where('examen_orden.realizado', '1')->join('seguros as s', 's.id', 'examen_orden.id_seguro')->where('examen_orden.estado', '1')->where('s.tipo', $id_tipo_seguro);

        if (!is_null($fecha_desde)) {
            $examen_orden = $examen_orden->whereBetween('examen_orden.fecha_orden', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else {
            $examen_orden = $examen_orden->where('examen_orden.fecha_orden', '<=', $fecha_hasta);
        }

        $examen_orden = $examen_orden->groupBy('s.id')->select('s.id as id_seguro', 's.nombre as nombre_seguro', DB::raw('count(examen_orden.id) as cantidad'), DB::raw('sum(examen_orden.total_valor) as total'))->get();

        return $examen_orden;
    }

    public function reporte_det_consultas_excel(Request $request)
    {
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];

        $id_cie = $request['cie10'];

        $hc_cie = DB::table('hc_cie10')
            ->select(
                'a.id_paciente',
                DB::raw("CONCAT(p.nombre1, ' ', p.nombre2, ' ', p.apellido1, ' ', p.apellido2) AS nombre"),
                'a.fechaini as fecha_procedimiento',
                DB::raw("IF(p.sexo = 1, 'MASCULINO', 'FEMENINO') AS sexo"),
                DB::raw("YEAR(a.fechaini) - YEAR(p.fecha_nacimiento) - (RIGHT(a.fechaini, 5) < RIGHT(p.fecha_nacimiento, 5)) AS edad"),
                'p.fecha_nacimiento',
                'a.proc_consul',
                'a.fechaini',
                'hc_cie10.ingreso_egreso',
                'hc_cie10.presuntivo_definitivo',
                'hc_cie10.cie10',
                'he.motivo',
                'he.cuadro_clinico'
            )
            ->join('historiaclinica as h', 'h.hcid', '=', 'hc_cie10.hcid')
            ->join('agenda as a', 'a.id', '=', 'h.id_agenda')
            ->join('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->join('hc_evolucion as he', 'he.hcid', '=', 'h.hcid')
            ->where('a.estado_cita', '=', 4)
            ->whereNotNull('hc_cie10.presuntivo_definitivo')
            ->where('a.estado', '=', 1)
            ->where('a.proc_consul', '=', 0)
            ->where('hc_cie10.cie10', '=', $id_cie);

        if (!is_null($fecha_desde)) {
            $hc_cie->whereBetween('a.fechaini', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else {
            $hc_cie->where('a.fechaini', '<=', $fecha_hasta);
        }

        $resultado = $hc_cie->get();

        $titulos = array("CI", "PACIENTE", "FECHA PROCEDIMIENTO", "SEXO", "EDAD", "FECHA NACIMIENTO", "PRESUNTIVO DEFINITIVO", "CIE10", "CONCLUSIÓN", "HALLAZGOS");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");


        Excel::create('reporte_det_consultas_excel', function ($excel) use ($titulos, $posicion, $resultado) {
            $excel->sheet('reporte_det_consultas_excel', function ($sheet) use ($titulos, $posicion, $resultado) {

                $comienzo = 1;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }

                $comienzo++;

                $comienzo = 2;
                foreach ($resultado as $soporte) {

                    $texto_sin_etiquetas = strip_tags(html_entity_decode($soporte->cuadro_clinico));


                    $datos_excel = [];
                    array_push(
                        $datos_excel,
                        $soporte->id_paciente,
                        $soporte->nombre,
                        $soporte->fecha_procedimiento,
                        $soporte->sexo,
                        $soporte->edad,
                        $soporte->fecha_nacimiento,
                        $soporte->presuntivo_definitivo,
                        $soporte->cie10,
                        $soporte->motivo,
                        $texto_sin_etiquetas
                    );

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }
}
