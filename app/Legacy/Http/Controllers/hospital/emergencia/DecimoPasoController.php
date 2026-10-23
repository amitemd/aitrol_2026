<?php

namespace Sis_medico\Http\Controllers\hospital\emergencia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Convenio;
use Sis_medico\Paciente;
use Sis_medico\ApProcedimiento;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_detalle;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Orden;
use Sis_medico\Ho_Orden_Examenes;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Firma_Usuario;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_usuario;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Orden;
use Sis_medico\Protocolo;
use Sis_medico\Seguro;
use Sis_medico\Interconsulta;
use Sis_medico\Interconsulta_Diagnostico;
use Sis_medico\Ho_Ordenes;
use Sis_medico\Hc_Log;
use Sis_medico\Procedimiento;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Orden_Procedimiento;
use Sis_medico\Orden_Tipo;
use Carbon\Carbon;
use Sis_medico\Interconsulta_Tarifario;

class DecimoPasoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol_new($opcion)
    {
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    public function laboratorio($id)
    {

        $solicitud = Ho_Solicitud::find($id);

        $ordenes = Examen_Orden::where('id_paciente', $solicitud->paciente->id)
            ->where('estado', '<>', '0')
            ->OrderBy('created_at', 'desc')
            ->get();

        //dd($ordenes->first()->estado);

        $protocolos  = Protocolo::where('estado', '1')->get();
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $examenes    = Examen::where('publico_privado', '0')->orderBy('id_agrupador')->get();

        return view('hospital.emergencia.orden_laboratorio.index', ['solicitud' => $solicitud, 'paciente' => $solicitud->paciente, 'ordenes' => $ordenes, 'id_editar' => null, 'protocolos' => $protocolos, 'agrupadores' => $agrupadores, 'examenes' => $examenes]);
    }

    public function procedimiento($id, $tipo)
    {

        $solicitud = Ho_Solicitud::find($id);

        $ordenes = Orden::where('tipo_procedimiento', $tipo)
            ->where('id_paciente', $solicitud->paciente->id)
            ->where('estado', 1)
            ->OrderBy('created_at', 'desc')
            ->get();


        return view('hospital.emergencia.orden_procedimientos.index', ['solicitud' => $solicitud, 'paciente' => $solicitud->paciente, 'ordenes' => $ordenes, 'tipo' => $tipo, 'id_editar' => null]);
    }

    public function procedimiento_crear($id, $tipo)
    {

        $solicitud = Ho_Solicitud::find($id);
        if ($tipo == 0) {
            if ($solicitud->estado_paso == '1') {
                if ($solicitud->form008->count() > 0) {
                    //dd($solicitud->form008->count());
                    $solicitud->form008->first()->update(['endoscopia' => 'X']);
                }
            }
        }

        $paciente = $solicitud->paciente;

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_doctor = Auth::user()->id;
        $idusuario = $id_doctor;

        $fecha_orden = Date('Y-m-d H:i:s');

        $arr_log = [
            'anterior' => 'ORDEN_PROC_ENDOSCOPICOS -> El Dr. creo nueva orden de procedimiento endoscopico',
            'nuevo' => 'ORDEN_PROC_ENDOSCOPICOS -> El Dr. creo nueva orden de procedimiento endoscopico',
            'id_paciente' => $paciente->id,
            'id_usuariocrea' => $id_doctor,
            'id_usuariomod' => $id_doctor,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        Hc_Log::create($arr_log);

        $evoluciones =  DB::table('historiaclinica as h')
            ->where('h.id_paciente', $paciente->id)
            ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
            ->where('hc_evo.secuencia', 0)
            ->whereNotNull('hc_evo.cuadro_clinico')
            ->orderby('hc_evo.updated_at', 'desc')
            ->select('hc_evo.*')
            ->first();

        $x_diagnosticos = null;
        $evol_id = null;
        $evol_motivo = null;
        $evol_cuadro_clinico = null;
        $texto = "";

        if (!is_null($evoluciones)) {

            $x_diagnosticos = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $evoluciones->hc_id_procedimiento)->groupBy('cie10')->get();

            $evol_id = $evoluciones->id;

            $evol_motivo = $evoluciones->motivo;

            $evol_cuadro_clinico = $evoluciones->cuadro_clinico;
        }

        if (!is_null($x_diagnosticos)) {

            $mas = true;
            foreach ($x_diagnosticos as $value) {

                $c3 = Cie_10_3::find($value->cie10);

                if (!is_null($c3)) {
                    $descripcion = $c3->descripcion;
                }

                $c4 = Cie_10_4::find($value->cie10);

                if (!is_null($c4)) {
                    $descripcion = $c4->descripcion;
                }

                if ($mas == true) {
                    $texto = $value->cie10 . ':' . $descripcion . '-' . $value->presuntivo_definitivo;
                    $mas = false;
                } else {

                    $texto = $texto . '<br>' . $value->cie10 . ':' . $descripcion . '-' . $value->presuntivo_definitivo;
                }
            }
        }


        $input_orden = [
            'id_paciente'       => $paciente->id,
            'id_doctor'         => $id_doctor,
            'id_evolucion'      => $evol_id,
            'motivo_consulta'   => $evol_motivo,
            'resumen_clinico'   => $evol_cuadro_clinico,
            'diagnosticos'      => $texto,
            'fecha_orden'       => $fecha_orden,
            'tipo_procedimiento' => $tipo,
            'anio'              => substr(date('Y-m-d'), 0, 4),
            'mes'               => substr(date('Y-m-d'), 5, 2),
            'id_usuariocrea'    => $id_doctor,
            'id_usuariomod'     => $id_doctor,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ];

        $id_orden = Orden::insertGetId($input_orden);

        $arr_orden_soli = [
            'clave_tipo'      => $id_orden,
            'id_ho_solicitud' => $id,
            'tipo'            => 'PROCEDIMIENTO',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];

        Ho_Ordenes::create($arr_orden_soli);

        $ordenes = Orden::where('tipo_procedimiento', $tipo)
            ->where('id_paciente', $solicitud->paciente->id)
            ->where('estado', 1)
            ->OrderBy('created_at', 'desc')
            ->get();
        //dd($ordenes);

        return view('hospital.emergencia.orden_procedimientos.index', ['solicitud' => $solicitud, 'paciente' => $solicitud->paciente, 'ordenes' => $ordenes, 'tipo' => $tipo, 'id_editar' => $id_orden]);
    }

    public function interconsulta($id)
    {

        $solicitud = Ho_Solicitud::find($id);

        $interconsultas = Interconsulta::where('id_paciente', $solicitud->paciente->id)
            ->where('estado', 1)
            ->OrderBy('created_at', 'desc')
            ->get();
        //dd($interconsultas);

        return view('hospital.emergencia.interconsulta.index', ['solicitud' => $solicitud, 'paciente' => $solicitud->paciente, 'interconsultas' => $interconsultas, 'id_editar' => null]);
    }

    public function laboratorio_orden_crear_pb($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $solicitud = Ho_Solicitud::find($id);
        $seguro    = Seguro::find($solicitud->id_seguro);
        //dd($seguro);
        if (is_null($seguro)) {
            $id_seguro = 2;
        } else {
            if ($seguro->tipo != 0) {
                //Si seguro no es publico, asume Iess
                $id_seguro = 2;
            } else {
                $id_seguro = $solicitud->id_seguro;
            }
        }

        $empresa  = Empresa::where('prioridad', 1)->get()->first();
        $convenio = Convenio::where('id_empresa', $empresa->id)->where('id_seguro', $id_seguro)->get()->first();
        $nivel    = $convenio->id_nivel;
        //CREAR LA ORDEN PUBLICA

        $input_ex = [
            'id_paciente'     => $solicitud->id_paciente,
            'anio'            => substr(date('Y-m-d'), 0, 4),
            'mes'             => substr(date('Y-m-d'), 5, 2),
            //'id_protocolo'    => $request['id_protocolo'],
            'id_seguro'       => $id_seguro,
            'id_nivel'        => $nivel,
            'est_amb_hos'     => 0,
            'id_doctor_ieced' => $idusuario,
            //'doctor_txt'      => $request['doctor_txt'],
            //'observacion'     => 'INGRE',
            'id_empresa'      => $empresa->id,
            'cantidad'        => 0,
            'valor'           => 0,
            'total_valor'     => 0,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'fecha_orden'     => date('Y-m-d H:i:s'),
            'estado'          => '-1',

        ];

        $id_editar = Examen_Orden::insertGetId($input_ex);

        $arr_orden_soli = [
            'id_examen_orden' => $id_editar,
            'id_ho_solicitud' => $id,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];

        Ho_Orden_Examenes::create($arr_orden_soli);

        $ordenes = Examen_Orden::where('id_paciente', $solicitud->paciente->id)
            ->where('estado', '<>', '0')
            ->OrderBy('created_at', 'desc')
            ->get();

        $protocolos  = Protocolo::where('estado', '1')->get();
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $examenes    = Examen::where('publico_privado', '0')->orderBy('id_agrupador')->get();

        return view('hospital.emergencia.orden_laboratorio.index', ['solicitud' => $solicitud, 'paciente' => $solicitud->paciente, 'ordenes' => $ordenes, 'id_editar' => $id_editar, 'protocolos' => $protocolos, 'agrupadores' => $agrupadores, 'examenes' => $examenes]);
    }

    public function laboratorio_orden_crear_part($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $solicitud = Ho_Solicitud::find($id);
        $seguro    = Seguro::find($solicitud->id_seguro);
        //dd($seguro);
        if (is_null($seguro)) {
            $id_seguro = 1;
        } else {
            if ($seguro->tipo == 0) {
                //Si seguro es publico, asume pARTI
                $id_seguro = 1;
            } else {
                $id_seguro = $solicitud->id_seguro;
            }
        }

        $empresa = Empresa::where('prioridad', 1)->get()->first();

        //CREAR LA ORDEN PRIVADA
        $input_ex = [
            'id_paciente'     => $solicitud->id_paciente,
            'anio'            => substr(date('Y-m-d'), 0, 4),
            'mes'             => substr(date('Y-m-d'), 5, 2),
            'id_seguro'       => $id_seguro,
            'est_amb_hos'     => 0,
            'id_doctor_ieced' => $idusuario,
            'id_empresa'      => $empresa->id,
            'cantidad'        => 0,
            'valor'           => 0,
            'total_valor'     => 0,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'fecha_orden'     => date('Y-m-d H:i:s'),
            'estado'          => '-1',
        ];

        $id_editar = Examen_Orden::insertGetId($input_ex);

        $arr_orden_soli = [
            'id_examen_orden' => $id_editar,
            'id_ho_solicitud' => $id,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];

        Ho_Orden_Examenes::create($arr_orden_soli);

        $ordenes = Examen_Orden::where('id_paciente', $solicitud->paciente->id)
            ->where('estado', '<>', '0')
            ->OrderBy('created_at', 'desc')
            ->get();

        $protocolos  = Protocolo::where('estado', '1')->get();
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $examenes    = Examen::where('publico_privado', '0')->orderBy('id_agrupador')->get();

        return view('hospital.emergencia.orden_laboratorio.index', ['solicitud' => $solicitud, 'paciente' => $solicitud->paciente, 'ordenes' => $ordenes, 'id_editar' => $id_editar, 'protocolos' => $protocolos, 'agrupadores' => $agrupadores, 'examenes' => $examenes]);
    }

    public function elimar_orden($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $examen_ordenes = Examen_Orden::find($id);
        $examen_ordenes->estado = 0;
        $examen_ordenes->ip_modificacion = $ip_cliente;
        $examen_ordenes->id_usuariomod = $idusuario;
        $examen_ordenes->save();
        return 'OK';
    }

    public function laboratorio_orden_editar_pb($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $protocolos  = Protocolo::where('estado', '1')->get();
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $examenes    = Examen::where('publico_privado', '0')->orderBy('id_agrupador')->get();
        $orden       = Examen_Orden::find($id);
        //dd($orden->seguro->tipo);

        if ($orden->seguro->tipo == 0) {
            return view('hospital.emergencia.orden_laboratorio.editar_orden', ['id_editar' => $id, 'protocolos' => $protocolos, 'agrupadores' => $agrupadores, 'examenes' => $examenes, 'orden' => $orden]);
        } else {
            /////
            $agrupador_labs = DB::table('examen_agrupador_labs')->get();
            $examenes_labs  = DB::table('examen_agrupador_sabana as sa')->join('examen as e', 'e.id', 'sa.id_examen')->where('e.estado', '1')->select('sa.*', 'e.descripcion', 'e.nombre', 'e.valor', 'e.id as ex_id')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden')->get();

            $detalles_ch = [];
            $i           = 0;

            $nuevo_detalles = $orden->detalles;

            $cantidad = count($nuevo_detalles);

            if ($cantidad > 0) {
                $examenes_labs = DB::table('examen_detalle as ed')->where('id_examen_orden', $id)->join('examen as e', 'e.id', 'ed.id_examen')->join('examen_agrupador_sabana as sa', 'sa.id_examen', 'ed.id_examen')->join('examen_agrupador_labs as l', 'l.id', 'sa.id_examen_agrupador_labs')->select('sa.*', 'e.descripcion', 'e.nombre', 'e.valor', 'e.id as ex_id', 'l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden')->get();
            }

            if (!is_null($nuevo_detalles)) {
                foreach ($nuevo_detalles as $nuevo_detalle) {
                    $detalles_ch[$i] = $nuevo_detalle->id_examen;
                    $i               = $i + 1;
                }
            }

            $protocolos = Protocolo::where('estado', '2')->where('id_usuariocrea', $idusuario)->get();

            return view('hospital.emergencia.orden_laboratorio.editar_orden_part', ['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'detalles_ch' => $detalles_ch, 'orden' => $orden, 'protocolos' => $protocolos]);
            ////
        }
    }

    public function laboratorio_orden_crear_examen($id_orden, $id_examen)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $orden = Examen_Orden::find($id_orden);



        $detalle = $orden->detalles->where('id_examen', $id_examen)->first();

        if (is_null($detalle)) {

            $examen   = Examen::find($id_examen);

            if ($examen->id == 1184 || $examen->id == 101) {
                $o_solicitud = Ho_Orden_Examenes::where('id_examen_orden', $orden->id)->first();
                //dd($o_solicitud);
                if (!is_null($o_solicitud)) {
                    $solicitud = Ho_Solicitud::find($o_solicitud->id_ho_solicitud); //dd($solicitud);
                    if ($solicitud->estado_paso == '1') {
                        if ($solicitud->form008->count() > 0) {
                            //dd($solicitud->form008->count());
                            $solicitud->form008->first()->update(['biometria' => 'X']);
                        }
                    }
                }
            }

            if ($examen->maquina == 2) {
                $o_solicitud = Ho_Orden_Examenes::where('id_examen_orden', $orden->id)->first();
                if (!is_null($o_solicitud)) {
                    $solicitud = Ho_Solicitud::find($o_solicitud->id_ho_solicitud);
                    if ($solicitud->estado_paso == '1') {
                        if ($solicitud->form008->count() > 0) {
                            $solicitud->form008->first()->update(['quimica_sanguinea' => 'X']);
                        }
                    }
                }
            }

            if ($examen->id == 758) {
                $o_solicitud = Ho_Orden_Examenes::where('id_examen_orden', $orden->id)->first();
                //dd($o_solicitud);
                if (!is_null($o_solicitud)) {
                    $solicitud = Ho_Solicitud::find($o_solicitud->id_ho_solicitud); //dd($solicitud);
                    if ($solicitud->estado_paso == '1') {
                        if ($solicitud->form008->count() > 0) {
                            //dd($solicitud->form008->count());
                            $solicitud->form008->first()->update(['gasometria' => 'X']);
                        }
                    }
                }
            }


            $valor    = $examen->valor;
            $cubre    = 'NO';
            $ex_nivel = Examen_Nivel::where('id_examen', $id_examen)->where('nivel', $orden->id_nivel)->first();
            if (!is_null($ex_nivel)) {
                if ($ex_nivel->valor1 != 0) {

                    $valor = $ex_nivel->valor1;
                    $cubre = 'SI';
                }
            }

            $cobrar_pac_pct = $orden->cobrar_pac_pct;
            $valor_con_oda  = 0;

            $input_det = [
                'id_examen_orden' => $orden->id,
                'id_examen'       => $id_examen,
                'valor'           => $valor,
                'cubre'           => $cubre,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'cobrar_pac_pct'  => $cobrar_pac_pct,
                'valor_con_oda'   => $valor_con_oda,

            ];

            Examen_detalle::create($input_det);

            /// orden
            $orden = Examen_Orden::find($id_orden);
            //$total       = $orden->valor + $valor;
            $total       = $orden->detalles->sum('valor');
            $cantidad    = $orden->detalles->count();
            $total       = round($total, 2);
            $descuento_p = $orden->descuento_p;
            $descuento_p = 0;

            $descuento_valor = $orden->detalles->sum('valor_descuento');

            $subtotal_pagar = $total - $descuento_valor;

            $recargo_p = $orden->recargo_p;

            $recargo_valor = $subtotal_pagar * $recargo_p / 100;
            $recargo_valor = round($recargo_valor, 2);
            $valor_total   = $subtotal_pagar + $recargo_valor;
            $valor_total   = round($valor_total, 2);
            $valor_con_oda = $orden->detalles->sum('valor_con_oda');
            $valor_con_oda = round($valor_con_oda, 2);
            $total_con_oda = $valor_con_oda + $recargo_valor;

            //ACTUALIZAR ORDEN
            $input_ex = [

                'descuento_valor' => $descuento_valor,
                'recargo_valor'   => $recargo_valor,
                'total_valor'     => $valor_total,
                'cantidad'        => $cantidad,
                'valor'           => $total,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'cobrar_pac_pct'  => $cobrar_pac_pct,
                'valor_con_oda'   => $valor_con_oda,
                'total_con_oda'   => $total_con_oda,

            ];

            $orden->update($input_ex);

            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "ACTUALIZA ORDEN PUBLICA",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant4'   => $id_examen,
            ]);
        }

        $ovalor       = $orden->valor;
        $ototal_valor = $orden->total_valor;

        return ['cantidad' => $orden->cantidad, 'valor' => $ovalor, 'descuento_valor' => $orden->descuento_valor, 'recargo_valor' => $orden->recargo_valor, 'total_valor' => $ototal_valor];
    }

    public function laboratorio_orden_quitar_examen($id_orden, $id_examen)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $orden = Examen_Orden::find($id_orden);

        $detalle = $orden->detalles->where('id_examen', $id_examen)->first();
        if (!is_null($detalle)) {

            $detalle->delete();

            $orden = Examen_Orden::find($id_orden);

            $total    = $orden->detalles->sum('valor');
            $cantidad = $orden->detalles->count();
            $total    = round($total, 2);
            if ($total < 0) {

                $total = 0;
            }
            $descuento_p     = $orden->descuento_p;
            $descuento_valor = 0;

            $subtotal_pagar = $total - $descuento_valor;

            $recargo_p = $orden->recargo_p;

            $recargo_valor = $subtotal_pagar * $recargo_p / 100;
            $recargo_valor = round($recargo_valor, 2);
            $valor_total   = $subtotal_pagar + $recargo_valor;
            $valor_total   = round($valor_total, 2);

            //ACTUALIZAR ORDEN
            $input_ex = [

                'descuento_valor' => $descuento_valor,
                'recargo_valor'   => $recargo_valor,
                'total_valor'     => $valor_total,
                'cantidad'        => $cantidad,
                'valor'           => $total,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,

            ];

            $orden->update($input_ex);

            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "ACTUALIZA ORDEN PUBLICA",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant4'   => $id_examen,
            ]);
            ///

        }

        return ['cantidad' => $orden->cantidad, 'valor' => $orden->valor, 'descuento_valor' => $orden->descuento_valor, 'recargo_valor' => $orden->recargo_valor, 'total_valor' => $orden->total_valor];
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $clinico           = $request->clinicos;
        $ginecologico      = $request->ginecologico;
        $traumatologico    = $request->traumatologicos;
        $antecedentes_quir = $request->antecedentes_quir;
        $farmacologico     = $request->farmacologico;
        $psiquiatrico      = $request->psiquiatrico;
        $antecedentes_fam  = $request->antecedentes_fam;
        $id_solicitud      = $request->id_solicitud;

        $solicitud      = Ho_Solicitud::find($id_solicitud);
        $paciente       = $solicitud->paciente;
        $datos_paciente = $paciente->ho_datos_paciente;

        $datos_paciente->update([
            'clinico'        => $clinico,
            'ginecologico'   => $ginecologico,
            'traumatologico' => $traumatologico,
            'farmacologico'  => $farmacologico,
            'psiquiatrico'   => $psiquiatrico,

        ]);

        $paciente->update([
            'antecedentes_quir' => $antecedentes_quir,
            'antecedentes_fam'  => $antecedentes_fam,

        ]);

        $alergias = $solicitud->paciente->a_alergias;
        $txt_al   = '';
        $cont     = 0;
        foreach ($alergias as $alergia) {
            if ($cont == 0) {
                $txt_al = $alergia->principio_activo->nombre;
            } else {
                $txt_al = $txt_al . ' + ' . $alergia->principio_activo->nombre;
            }
            $cont++;
        }

        return view('hospital.emergencia.cuartopaso', ['solicitud' => $solicitud, 'txt_al' => $txt_al, 'alergias' => $alergias]);
    }

    public function laboratorio_detalle($id)
    {

        $orden = Examen_Orden::find($id);
        //dd($orden->seguro->tipo);
        return view('hospital.emergencia.orden_laboratorio.detalle_orden', ['orden' => $orden]);
    }

    public function procedimiento_detalle($id)
    {

        $orden = Orden::find($id);
        //dd($orden);
        $paciente = $orden->paciente;

        $seguro = $paciente->seguro;

        $txtprocedimientos = "";

        $orden_tipo = $orden->orden_tipo->first(); //dd($orden_tipo);

        if (!is_null($orden_tipo)) {

            $procedimientos = $orden_tipo->orden_procedimiento; //dd($procedimientos);

            $mas = true;
            foreach ($procedimientos as $px) {
                //dd($px);
                $nombre_procedimiento = $px->procedimiento->nombre;

                if ($mas == true) {
                    $txtprocedimientos = $nombre_procedimiento;
                    $mas               = false;
                } else {
                    $txtprocedimientos = $txtprocedimientos . ' + ' . $nombre_procedimiento;
                }
            }
            //dd($txtprocedimientos);

        }

        return view('hospital.emergencia.orden_procedimientos.detalle_orden', ['orden' => $orden, 'paciente' => $paciente, 'txtprocedimientos' => $txtprocedimientos, 'seguro' => $seguro]);
    }

    public function procedimiento_editar($id)
    {

        $orden = Orden::find($id);
        $paciente = $orden->paciente;
        $seguro = $paciente->seguro;
        $txtprocedimientos = "";
        $orden_tipo = $orden->orden_tipo->first(); //dd($orden_tipo);
        if (!is_null($orden_tipo)) {
            $procedimientos = $orden_tipo->orden_procedimiento; //dd($procedimientos);
            $mas = true;
            foreach ($procedimientos as $px) {
                $nombre_procedimiento = $px->procedimiento->nombre;
                if ($mas == true) {
                    $txtprocedimientos = $nombre_procedimiento;
                    $mas               = false;
                } else {
                    $txtprocedimientos = $txtprocedimientos . ' + ' . $nombre_procedimiento;
                }
            }
        }

        $cx = Procedimiento::where('estado', '1')->whereNull('id_grupo_procedimiento');
        $px1 = Procedimiento::where('estado', '1')->where('id_grupo_procedimiento', '<>', '18')->where('id_grupo_procedimiento', '<>', '20')->where('id_grupo_procedimiento', '<>', '11');
        $px = $px1->union($cx)->get();
        $qx = Procedimiento::where('estado', '1')->where('id_grupo_procedimiento', '21');
        $ix = Procedimiento::where('estado', '1')->where('id_grupo_procedimiento', '20');
        $qx = $qx->union($cx)->get();
        $ix = $ix->union($cx)->get();
        //dd($qx);

        return view('hospital.emergencia.orden_procedimientos.editar_orden', ['orden' => $orden, 'paciente' => $paciente, 'txtprocedimientos' => $txtprocedimientos, 'seguro' => $seguro, 'px' => $px, 'qx' => $qx, 'ix' => $ix]);
    }

    public function procedimiento_actualizar_pxs(Request $request)
    {

        // dd($request->all());
        

        $check_selec =  $request['firma_doctor_rob_fun'];

        $proced_funcional =  $request['x_procedimiento_func'];
        $count_proced_funcional = count($proced_funcional);


        //Obtengo la fecha de actualizacion
        $fecha_actualizacion = Date('Y-m-d H:i:s');

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $id = $request['id_ordenfun'];

        $orden_procedfuncional = Orden::find($id);

        //dd($orden_procedfuncional);

        $anteriores_orden_tipo = DB::table('orden_tipo as ot')->where('ot.id_orden', $request['id_ordenfun'])->get();


        if ($anteriores_orden_tipo != null) {

            foreach ($anteriores_orden_tipo as $value) {

                $anteriores_orden_procedimiento  = Orden_Procedimiento::where('id_orden_tipo', $value->id);
                $anteriores_orden_procedimiento->delete();
            }
        }

        $x_anteriores_orden_tipo  = Orden_Tipo::where('id_orden', $request['id_ordenfun']);
        $x_anteriores_orden_tipo->delete();


        if ($count_proced_funcional > 0) {

            //$this->validateInput2($request);

            $orden_profuncional_new = $orden_procedfuncional;
            if (!is_null($orden_profuncional_new)) {

                $orden_funcional_act_new = [
                    'anterior' => 'ORDEN_PROC_FUNCIONAL -> Motivo: ' . $orden_profuncional_new->motivo_consulta . ' Resumen_Historia_Clinica: ' . $orden_profuncional_new->resumen_clinico . ' Diagnosticos: ' . $orden_profuncional_new->diagnosticos . ' Observacion_Medica: ' . $orden_profuncional_new->observacion_medica . ' Observacion_Recepcion:' . $orden_profuncional_new->observacion_recepcion,
                    'nuevo' => 'ORDEN_PROC_FUNCIONAL -> Motivo: ' . $request['xmotivo_orden'] . ' Resumen_Historia_Clinica: ' . $request['func_historia_clinica'] . ' Diagnosticos: ' . $request['func_des_diagnostico'] . ' Observacion_Medica: ' . $request['xobservacion_orden'] . ' Observacion_Recepcion:' . $request['xobservacion_recepcion'],
                    'id_paciente' => $request["id_paciente"],
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];

                Hc_Log::create($orden_funcional_act_new);
            }

            $id_grupo_procedimiento = '18';
            if ($orden_procedfuncional->tipo_procedimiento == '3') {
                $id_grupo_procedimiento = '21';
            }

            $input_orden_tipo = [
                'id_orden' => $id,
                'id_grupo_procedimiento' => $id_grupo_procedimiento,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'created_at' => $fecha_actualizacion
            ];

            $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);


            foreach ($proced_funcional as $value) {
                $input_orden_procedimiento = [
                    'id_orden_tipo' => $id_orden_tipo,
                    'id_procedimiento' => $value,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_actualizacion
                ];

                Orden_Procedimiento::create($input_orden_procedimiento);
            }


            //Check Firma Doctor Robles
            if ($check_selec == '1') {

                $doctor_firma = '1307189140';
            } else {
                $doctor_firma = $orden_procedfuncional->id_doctor;
            }


            $input = [

                'motivo_consulta' => $request['xmotivo_orden'],
                'resumen_clinico' => $request['func_historia_clinica'],
                'observacion_medica' => $request['observacion_medica'],
                'observacion_recepcion' => $request['xobservacion_recepcion'],
                'diagnosticos' => $request['func_des_diagnostico'],
                'check_doctor' => $check_selec,
                'id_doctor_firma' => $doctor_firma,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'updated_at' => $fecha_actualizacion,

            ];

            $orden_procedfuncional->update($input);
        }
    }

    public function imprimir_orden_funcional_hospital($id)
    {
        /*$opcion = '2';
        if($this->rol_new($opcion)){
        return redirect('/');
        }*/

        $orden_proc_funcional = Orden::find($id);

        if ((is_null($orden_proc_funcional->check_doctor)) && (is_null($orden_proc_funcional->id_doctor_firma))) {


            $doctor_firma = $orden_proc_funcional->id_doctor;
        } else {

            $doctor_firma = $orden_proc_funcional->id_doctor_firma;
        }

        /*if (!is_null($orden_proc_funcional)) {
                $firma = Firma_Usuario::where('id_usuario', $orden_proc_funcional->id_doctor)->first();
        }*/

        if (!is_null($orden_proc_funcional)) {
            $firma = Firma_Usuario::where('id_usuario', $doctor_firma)->first();
        }

        $paciente = Paciente::find($orden_proc_funcional->id_paciente);

        if ($paciente->fecha_nacimiento != null) {
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        }

        //$id_doctor = Auth::user()->id; 
        $doctor_solicitante = DB::table('users as us')
            ->where('us.id', $orden_proc_funcional->id_doctor)
            ->first();

        $vistaurl = "hospital.emergencia.orden_procedimientos.pdf_orden";
        $view =  \View::make($vistaurl, compact('orden_proc_funcional', 'paciente', 'edad', 'doctor_solicitante', 'firma'))->render();
        //return $view;

        /*$view =  \View::make($vistaurl, compact('orden','pct','detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador'))->render();*/

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('resultado-' . $id . '.pdf');
    }

    public function procedimientoendo_actualizar(Request $request)
    {

        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $check_selec =  $request['firma_doctor_rob_end'];

        $procedimientos_endo_digest =  $request['x_procedimiento'];
        $count_pro_endo_digest = count($procedimientos_endo_digest);
        $procedimientos_colono =  $request['x_procedimiento_colono'];
        $count_pro_colono = count($procedimientos_colono);
        $procedimientos_enter =  $request['x_procedimiento_entero'];
        $count_pro_enter = count($procedimientos_enter);
        $procedimientos_ecoend =  $request['x_procedimiento_ecoend'];
        $count_pro_ecoend = count($procedimientos_ecoend);
        $procedimientos_cpre =  $request['x_procedimiento_cpre'];
        $count_pro_cpre = count($procedimientos_cpre);
        $procedimientos_bronc =  $request['x_procedimiento_bronc'];
        $count_pro_bronc = count($procedimientos_bronc);


        //Obtengo la fecha de actualizacion
        $fecha_actualizacion = Date('Y-m-d H:i:s');

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $id = $request['id_orden'];

        $orden_proendoscopico = Orden::find($id);

        $anteriores_orden_tipo = $orden_proendoscopico->orden_tipo;

        if ($anteriores_orden_tipo != null) {

            foreach ($anteriores_orden_tipo as $value) {

                $anteriores_orden_procedimiento  = Orden_Procedimiento::where('id_orden_tipo', $value->id);
                $anteriores_orden_procedimiento->delete();
            }
        }

        $orden_proendoscopico = Orden::find($id);
        $anteriores_orden_tipo = $orden_proendoscopico->orden_tipo->delete();


        if (($count_pro_endo_digest > 0) || ($count_pro_colono > 0) || ($count_pro_enter > 0) || ($count_pro_ecoend > 0) || ($count_pro_cpre > 0) || ($count_pro_bronc > 0)) {

            $orden_proendoscopico_new = Orden::find($id);
            if (!is_null($orden_proendoscopico_new)) {
                $orden_endoscopica_act_new = [
                    'anterior' => 'ORDEN_PROC_ENDOSCOPICOS -> Motivo: ' . $orden_proendoscopico_new->motivo_consulta . ' Resumen_Historia_Clinica: ' . $orden_proendoscopico_new->resumen_clinico . ' Diagnosticos: ' . $orden_proendoscopico_new->diagnosticos . ' Observacion_Medica: ' . $orden_proendoscopico_new->observacion_medica . ' Observacion_Recepcion:' . $orden_proendoscopico_new->observacion_recepcion,
                    'nuevo' => 'ORDEN_PROC_ENDOSCOPICOS -> Motivo: ' . $request['xmotivo_orden'] . ' Resumen_Historia_Clinica: ' . $request['endos_historia_clinica'] . ' Diagnosticos: ' . $request['endos_desc_diagnostico'] . ' Observacion_Medica: ' . $request['xobservacion_orden'] . ' Observacion_Recepcion:' . $request['xobservacion_recepcion'],
                    'id_paciente' => $request["id_pacient"],
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Hc_Log::create($orden_endoscopica_act_new);
            }

            //ENDOSCOPIA DIGESTIVA
            if ($count_pro_endo_digest > 0) {


                $input_orden_tipo = [
                    'id_orden' => $id,
                    'id_grupo_procedimiento' => '1',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_actualizacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);


                foreach ($procedimientos_endo_digest as $value) {
                    $input_orden_procedimiento = [
                        'id_orden_tipo' => $id_orden_tipo,
                        'id_procedimiento' => $value,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'created_at' => $fecha_actualizacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento);
                }
            }


            //COLONOSCOPIA
            if ($count_pro_colono > 0) {
                $input_orden_tipo = [
                    'id_orden' => $id,
                    'id_grupo_procedimiento' => '2',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_actualizacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);


                foreach ($procedimientos_colono as $value) {
                    $input_orden_procedimiento_colono = [
                        'id_orden_tipo' => $id_orden_tipo,
                        'id_procedimiento' => $value,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'created_at' => $fecha_actualizacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_colono);
                }
            }

            //INTESTINO DELGADO
            if ($count_pro_enter > 0) {

                $input_orden_tipo = [
                    'id_orden' => $id,
                    'id_grupo_procedimiento' => '3',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_actualizacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);


                foreach ($procedimientos_enter as $value) {
                    $input_orden_procedimiento_enter = [
                        'id_orden_tipo' => $id_orden_tipo,
                        'id_procedimiento' => $value,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'created_at' => $fecha_actualizacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_enter);
                }
            }


            //ECOENDOSCOPIA
            if ($count_pro_ecoend > 0) {

                $input_orden_tipo = [
                    'id_orden' => $id,
                    'id_grupo_procedimiento' => '9',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_actualizacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);


                foreach ($procedimientos_ecoend as $value) {
                    $input_orden_procedimiento_ecoend = [
                        'id_orden_tipo' => $id_orden_tipo,
                        'id_procedimiento' => $value,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'created_at' => $fecha_actualizacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_ecoend);
                }
            }

            //CPRE
            if ($count_pro_cpre > 0) {

                $input_orden_tipo = [
                    'id_orden' => $id,
                    'id_grupo_procedimiento' => '10',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_actualizacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);


                foreach ($procedimientos_cpre as $value) {
                    $input_orden_procedimiento_cpre = [
                        'id_orden_tipo' => $id_orden_tipo,
                        'id_procedimiento' => $value,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'created_at' => $fecha_actualizacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_cpre);
                }
            }

            //BRONCOSCOPIA
            if ($count_pro_bronc > 0) {

                $input_orden_tipo = [
                    'id_orden' => $id,
                    'id_grupo_procedimiento' => '14',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_actualizacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);


                foreach ($procedimientos_bronc as $value) {
                    $input_orden_procedimiento_bronc = [
                        'id_orden_tipo' => $id_orden_tipo,
                        'id_procedimiento' => $value,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'created_at' => $fecha_actualizacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_bronc);
                }
            }


            //Check Firma Doctor Robles
            if ($check_selec == '1') {

                $doctor_firma = '1307189140';
            } else {
                $doctor_firma = $orden_proendoscopico->id_doctor;
            }

            $input = [

                'motivo_consulta' => $request['xmotivo_orden'],
                'resumen_clinico' => $request['endos_historia_clinica'],
                'observacion_medica' => $request['xobservacion_orden'],
                'observacion_recepcion' => $request['xobservacion_recepcion'],
                'diagnosticos' => $request['endos_desc_diagnostico'],
                'check_doctor' => $check_selec,
                'id_doctor_firma' => $doctor_firma,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'updated_at' => $fecha_actualizacion,
                'necesita_valoracion' => $request->necesita_valoracion,

            ];

            $orden_proendoscopico->update($input);
        }/*else{
            
        return "false";   
      }*/

        //return "ok";   

        /*return view('hc4/ordenes/orden_procedimiento_endoscopico/unico_orden',['orden_proendoscopico'=> $orden_proendoscopico,'paciente' => $paciente,'edad' => $edad,'ndoctor' => $ndoctor]);*/
    }

    public function examenes_buscar_publicos_otros(Request $request)
    {
        $examenes = Examen::where('estado', 1)->where('id_agrupador', 7)->where('publico_privado', 0)->where('no_orden_pub', 0)->where('nombre', 'like', '%' . $request->term . '%')->get();

        $arr = null;

        foreach ($examenes as $value) {
            $arr[] = array('value' => $value->nombre, 'id' => $value->id);
        }

        return $arr;
    }

    public function laboratorio_listar_otros($id_orden)
    {
        $orden    = Examen_orden::find($id_orden);
        $detalles = $orden->detalles;
        //dd($detalles);
        return view('hospital.emergencia.lista_otros', ['detalles' => $detalles, 'orden' => $orden]);
    }

    public function buscar_examenes(Request $request, $id_orden)
    {

        /*$opcion = '2';
        if($this->rol_new($opcion)){
        return redirect('/');
        }*/

        $orden = Examen_Orden::find($id_orden);

        $agrupador_labs = DB::table('examen_agrupador_labs')->get();

        $examenes_labs = DB::table('examen_agrupador_sabana as sa')->join('examen as e', 'e.id', 'sa.id_examen')->join('examen_agrupador_labs as l', 'l.id', 'sa.id_examen_agrupador_labs')->where('e.estado', '1')->select('sa.*', 'e.descripcion', 'e.nombre', 'e.valor', 'e.id as ex_id', 'l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden');

        if ($request->seleccionados == '1' && $request->buscador == null && $request->buscador2 == null) {

            $examenes_labs = DB::table('examen_detalle as ed')->where('id_examen_orden', $id_orden)->join('examen as e', 'e.id', 'ed.id_examen')->join('examen_agrupador_sabana as sa', 'sa.id_examen', 'ed.id_examen')->join('examen_agrupador_labs as  l', 'l.id', 'sa.id_examen_agrupador_labs')->select('sa.*', 'e.descripcion', 'e.nombre', 'e.valor', 'e.id as ex_id', 'l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden');
        }

        if ($request->buscador != null) {
            $examenes_labs = $examenes_labs->where('e.descripcion', 'like', '%' . $request->buscador . '%');
        }

        if ($request->buscador2 != null) {
            $examenes_labs = $examenes_labs->where('l.nombre', 'like', '%' . $request->buscador2 . '%');
        }

        if ($request->firma_dr == 1) {
            $orden->update(['doctor_firma' => '1307189140']);
        } else {
            $orden->update(['doctor_firma' => $orden->id_doctor_ieced]);
        }

        $examenes_labs = $examenes_labs->get();

        $detalles_ch = [];
        $i           = 0;

        $nuevo_detalles = $orden->detalles;
        if (!is_null($nuevo_detalles)) {
            foreach ($nuevo_detalles as $nuevo_detalle) {
                $detalles_ch[$i] = $nuevo_detalle->id_examen;
                $i               = $i + 1;
            }
        }

        return view('hospital/emergencia/orden_laboratorio/listado', ['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'detalles_ch' => $detalles_ch, 'orden' => $orden]);
    }

    public function crear_interconsulta($id, Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $solicitud = Ho_Solicitud::find($id);
        if ($solicitud->estado_paso == '1') {
            if ($solicitud->form008->count() > 0) {
                //dd($solicitud->form008->count());
                $solicitud->form008->first()->update(['interconsulta' => 'X']);
            }
        }

        //$empresa = Empresa::where('prioridad', 2)->first();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);

        // dd($empresa);

        //CREAR LA INTERCONSULTA
        $input_ex = [
            'id_paciente'     => $solicitud->id_paciente,
            'id_doctor'       => $idusuario,
            'id_empresa'      => $empresa->id,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'fecha'           => date('Y-m-d H:i:s'),
            'estado'          => '1',
        ];

        $id_editar = Interconsulta::insertGetId($input_ex);

        $arr_orden_soli = [
            'clave_tipo'      => $id_editar,
            'id_ho_solicitud' => $id,
            'tipo'            => 'INTERCONSULTA',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];

        Ho_Ordenes::create($arr_orden_soli);

        $interconsultas = Interconsulta::where('id_paciente', $solicitud->paciente->id)
            ->where('estado', 1)
            ->OrderBy('created_at', 'desc')
            ->get();

        return view('hospital.emergencia.interconsulta.index', ['solicitud' => $solicitud, 'paciente' => $solicitud->paciente, 'interconsultas' => $interconsultas, 'id_editar' => $id_editar]);
    }

    public function editar_interconsulta($id_inter)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $interconsulta = Interconsulta::find($id_inter);

        //dd($interconsulta);
        $empresa = Empresa::where('prioridad', 1)->get()->first();

        return view('hospital.emergencia.interconsulta.editar', ['interconsulta' => $interconsulta]);
    }

    public function detalle_interconsulta($id_inter)
    {

        $interconsulta = Interconsulta::find($id_inter);
        $det_tarifario = Interconsulta_Tarifario::where('id_interconsulta', $id_inter)->where('estado', 1)->get();
        //dd($det_tarifario);

        return view('hospital.emergencia.interconsulta.detalle', ['interconsulta' => $interconsulta, 'det_tarifario'=>$det_tarifario]);
    }

    public function actualizar_interconsulta(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        DB::beginTransaction();
        try {
            $interconsulta = Interconsulta::find($request->id_interconsulta);


            $interconsulta->update([
                'servicio'        => $request->servicio,
                'especialidad'    => $request->especialidad,
                'evolucion'       => $request->evolucion,
                'tarifario'       => $request->tarifario,
                'descripcion'     => $request->descripcion,
                'resultados_exa'  => $request->resultados_exa,
                'plan_terapeuticos'  => $request->plan_terapeuticos,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
        
    }

    public function imprimir_interconsulta($id)
    {
        $cie10 = DB::table('interconsulta_diagnostico')->where('interconsulta_diagnostico.id_interconsulta', $id)->get();
        $interconsulta  = Interconsulta::find($id);
        $paciente       = $interconsulta->paciente;
        $age            = Carbon::createFromDate(substr($interconsulta->paciente->fecha_nacimiento, 0, 4), substr($interconsulta->paciente->fecha_nacimiento, 5, 2), substr($interconsulta->paciente->fecha_nacimiento, 8, 2))->age;

        $empresa        = Empresa::where('prioridad', 1)->get()->first();
        //dd($interconsulta);
        $det_tarifario = Interconsulta_Tarifario::where('id_interconsulta', $interconsulta->id)->where('estado', 1)->get();
        //dd($det_tarifario);
        $vistaurl = "hospital.emergencia.interconsulta.007_pdf";
        $view     = \View::make($vistaurl, compact('interconsulta', 'age', 'empresa', 'paciente', 'cie10', 'det_tarifario'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('007-' . $interconsulta->paciente->apellido1 . '.pdf', array("Attachment" => false));
        exit(0);
    }
    public function agregar_cie10inter(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $interconsulta = Interconsulta::find($request['id_interconsulta']);

        if ($request['codigo'] == null) {
            return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }

        Interconsulta_Diagnostico::create([
            'id_interconsulta'      => $request['id_interconsulta'],
            'cie10'                 => $request['codigo'],
            'ingreso_egreso'        => 'INGRESO',
            'presuntivo_definitivo' => $request['pre_def'],
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,

        ]);

        /*$count = Interconsulta_Diagnostico::where('id_hcid', $solicitud->hcid)->where('ingreso_egreso','INGRESO')->get()->count();

        $cie10 = Interconsulta_Diagnostico::find($id);

        $c3 = Cie_10_3::find($cie10->cie10);
        if (!is_null($c3)) {
            $descripcion = $c3->descripcion;
        }
        $c4 = Cie_10_4::find($cie10->cie10);
        if (!is_null($c4)) {
            $descripcion = $c4->descripcion;
        }

        return (['count' => $count, 'id' => $id, 'cie10' => $cie10->cie10, 'descripcion' => $descripcion, 'pre_def' => $request['pre_def'], 'in_eg' => 'INGRESO','id_interconsulta' => $interconsulta->id_hcid, 'solicitud' => $solicitud, 'paciente' => $solicitud->paciente,  'interconsultas' => $interconsultas ]);*/

        return "ok";
    }

    public function cargar_tabla_cie($id)
    {

        $cie10 = DB::table('interconsulta_diagnostico')->where('interconsulta_diagnostico.id_interconsulta', $id)->get();

        return view('hospital.emergencia.interconsulta.listadocie10', ['cie10' => $cie10]);
    }

    public function elimar_cie10($id)
    {

        $dx = Interconsulta_Diagnostico::find($id);
        $dx->delete();
    }

    public function buscarTarifario(Request $request)
    {
        $tarifario  = [];
        if (!is_null($request["search"])) {
            $tarifario1 = ApProcedimiento::where('estado', 1)->where('codigo', 'LIKE', "%{$request['search']}%")->select('id as id', DB::raw('CONCAT(codigo," | ",descripcion) as text'));
            $tarifario2 = ApProcedimiento::where('estado', 1)->where('descripcion', 'LIKE', "%{$request['search']}%")->select('id as id', DB::raw('CONCAT(codigo," | ",descripcion) as text'));

            $tarifario = $tarifario1->union($tarifario2)->get();
        }

        return response()->json($tarifario);
    }

    public function inter_tarifario_det($id_inter)
    {

        $det_tarifario = Interconsulta_Tarifario::where('id_interconsulta', $id_inter)->where('estado', 1)->get();

        return view('hospital/emergencia/interconsulta/det_tarifario', ['det_tarifario' => $det_tarifario, 'id_inter' => $id_inter]);
    }

    public function guardar_det_tarifario(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_interconsulta = $request['id_interconsulta'];
        $id_tarifario = $request['id_tarifario'];

        $detalle = Interconsulta_Tarifario::where('id_ap_proc', $id_tarifario)->where('id_interconsulta', $id_interconsulta)->where('estado', 1)->first();

        if (is_null($detalle)) {
            $arr_det = [
                'id_interconsulta'  => $id_interconsulta,
                'id_ap_proc'        => $id_tarifario,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,

            ];
            Interconsulta_Tarifario::create($arr_det);
        }
    }
}
