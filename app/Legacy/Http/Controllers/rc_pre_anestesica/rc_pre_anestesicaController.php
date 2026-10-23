<?php

namespace Sis_medico\Http\Controllers\Rc_pre_anestesica;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Rc_pre_anestesica;
use Sis_medico\Rc_antecedentes;
use Sis_medico\Hc_Evolucion_Anestesiologo;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Rc_indicaciones;
use Sis_medico\Historiaclinica;
use Sis_medico\Hc_Anestesiologia;
use Sis_medico\Rc_resultados;
use Sis_medico\Hc_Cie10;
use Sis_medico\Drogas_administradas;
use Schema;
use Sis_medico\Agenda;
use Sis_medico\analgesia;
use Sis_medico\Especialidad;
use Sis_medico\Examen_Orden;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_procedimientos;
use Sis_medico\Http\Controllers\Apis\FirmadorController;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\TecnicasAnestesicas;
use Sis_medico\record_tecnicas_anestesicas;
use Sis_medico\Aud_Hc_Procedimientos;
use Sis_medico\Aud_Hc_Protocolo;
use Sis_medico\Aud_Hc_Cie10;
use Sis_medico\hc_anestesiologia_csv;

class Rc_pre_anestesicaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    // public function preanestesico_create()
    // {

    //     $antecedentes = rc_antecedentes::where('estado', 1)->get();
    //     //dd($antecedentes);

    //     return view('preanestesico/preanestesico_create', compact('antecedentes'));
    // }


    // public function preanestesico_index()
    // {

    //     $antecedentes = rc_antecedentes::where('estado', 1)->get();
    //     //dd($antecedentes); 

    //     return view('preanestesico/preanestesico_index', ['antecedentes' => $antecedentes]);
    // }


    // public function preanestesico_edit($id)

    // {
    //     $antecedentes = rc_antecedentes::where('id', $id)->first();

    //     return view('preanestesico/preanestesico_edit', ['antecedentes' => $antecedentes, 'id' => $id]);
    // }


    // public function preanestesico_update(Request $request)
    // {

    //     $ip_cliente = $_SERVER["REMOTE_ADDR"];
    //     $idusuario  = Auth::user()->id;
    //     $forma = rc_antecedentes::where('id', $request->id)->first();

    //     $arr_formato_consentimiento = [

    //         'id_hc_anestesiologia' => 17,
    //         'tratamiento'          => $request['tratamiento'],
    //         'diagnostico'          => $request['diagnostico'],
    //         'tiempo_evolucion'     => $request['tiempo_evo'],
    //         'estado'               => 1,
    //         'id_usuariocrea'       => $idusuario,
    //         'id_usuariomod'        => $idusuario,
    //         'ip_creacion'          => $ip_cliente,
    //         'ip_modificacion'      => $ip_cliente,
    //     ];
    //     $forma->update($arr_formato_consentimiento);
    //     return redirect(route('formato.preanestesico_index'));
    // }


    // public function examen_preanestesico_create()
    // {


    //     return view('examen_preanestesico/examen_preanestesico_create', compact('examen'));
    // }

    // public function examen_preanestesico_index()
    // {

    //     $resultados = rc_resultados::where('estado', 1)->get();
    //     //dd($resultados); 

    //     return view('examen_preanestesico/resultados_index', ['resultados' => $resultados]);
    // }


    // public function examen_preanestesico_edit($id)

    // {
    //     $resultados = rc_resultados::where('id', $id)->first();

    //     return view('examen_preanestesico/resultados_edit', ['resultados' => $resultados, 'id' => $id]);
    // }


    // public function examen_preanestesico_update(Request $request)
    // {
    //     //dd($request->all());

    //     $ip_cliente = $_SERVER["REMOTE_ADDR"];
    //     $idusuario  = Auth::user()->id;
    //     $resultado = rc_resultados::where('id', $request->id)->first();

    //     $arr_examen_resultado = [

    //         'id_hc_anestesiologia' => 17,
    //         'id_examen'            => 40,
    //         'nombre'               => $request['nombre'],
    //         'resultado'            => $request['resultado'],
    //         'estado'               => 1,
    //         'id_usuariocrea'       => $idusuario,
    //         'id_usuariomod'        => $idusuario,
    //         'ip_creacion'          => $ip_cliente,
    //         'ip_modificacion'      => $ip_cliente,
    //     ];

    //     $resultado->update($arr_examen_resultado);
    //     return redirect(route('formato.examen_preanestesico_index'));
    // }

    // public function rc_preanestesico_create()
    // {
    //     return view('rc_preanestesico/preanestesico_create');
    // }

    // public function rc_preanestesico_store(Request $request)
    // {


    //     $ip_cliente = $_SERVER["REMOTE_ADDR"];
    //     $idusuario = Auth::user()->id;


    //     $arr_riesgo_preanestesico = [

    //         'id_hc_anestesiologia'  => 17,
    //         'tipo'                  => $request["tipo"],
    //         'riesgo_quirurgico'     => $request["riesgo_quirurgico"],
    //         'anestesico'            => $request["anestesico"],
    //         'quirurgicos'           => $request["quirurgico"],
    //         'alergicos'             => $request["alergicos"],
    //         'transfuciones'         => $request["transfusiones"],
    //         'habitos'               => $request["habitos"],
    //         'antecedentes'         => $request["antecedentes"],
    //         'apertura_bucal'       => $request["apertura"],
    //         'distancia_tiromentoneana' => $request["distancia"],
    //         'mallanpati'           => $request["mallampati"],
    //         'protusion_mandibular' => $request["protrusion"],
    //         'perimetro_cervical'   => $request["perimetro"],
    //         'movilidad_cervical'   => $request["movilidad"],
    //         'intubacion_dificil'   => $request["historia"],
    //         'patologia'            => $request["patologia"],
    //         'torax'                => $request["torax"],
    //         'corazon'              => $request["corazon"],
    //         'pulmones'             => $request["pulmones"],
    //         'abdomen'              => $request["abdomen"],
    //         'extremidades'         => $request["extremidades"],
    //         'sistema_nervisoso'    => $request["sistema"],
    //         'equivalente_metabolico' => $request["equivalente"],
    //         'estado_fisico'        => $request["estado_fisico"],
    //         'riesgo_cardiaco'      => $request["riesgo_cardiaco"],
    //         'riesgo_pulmonar'      => $request["riesgo_pulmonar"],
    //         'riesgo_tromboembolico' => $request["riesgo_tromboembolico"],
    //         'liquidos_claros'      => $request["liquidos_claros"],
    //         'leche_materna'         => $request["leche_materna"],
    //         'solidos'              => $request["solidos"],
    //         'plan_anestesico'      => $request["plan_anestesico"],
    //         'observaciones'        => $request["observaciones"],
    //         'estado'               => 1,
    //         'id_usuariocrea'       => $idusuario,
    //         'id_usuariomod'        => $idusuario,
    //         'ip_creacion'          => $ip_cliente,
    //         'ip_modificacion'      => $ip_cliente,
    //     ];
    //     rc_pre_anestesica::create($arr_riesgo_preanestesico);
    //     return redirect(route('formato.rc_preanestesico_index'));
    // }

    // public function rc_preanestesico_index()
    // {

    //     $pre_anestesico = rc_pre_anestesica::where('estado', 1)->get();
    //     //dd($rc); 

    //     return view('rc_preanestesico/preanestesico_index', ['pre_anestesico' => $pre_anestesico]);
    // }


    // public function rc_preanestesico_delete($id)
    // {

    //     $ip_cliente = $_SERVER["REMOTE_ADDR"];
    //     $idusuario  = Auth::user()->id;

    //     try {

    //         $rc = rc_pre_anestesica::find($id);
    //         $arr_eliminar = [
    //             'estado'               => 0,
    //             'id_usuariocrea'       => $idusuario,
    //             'id_usuariomod'        => $idusuario,
    //             'ip_creacion'          => $ip_cliente,
    //             'ip_modificacion'      => $ip_cliente,
    //         ];
    //         $rc->update($arr_eliminar);
    //         return json_encode(['status' => true, 'msj' => "Eliminado con exito"]);
    //     } catch (\Exception $e) {
    //         return json_encode(['status' => false, 'msj' => $e->getMessage(), 'line' => $e->getLine()]);
    //     }
    // }



    public function rc_preanestesico_crea_registro($id_hc_anestesiologia)
    {
        //dd($id_hc_anestesiologia);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;


        $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $id_hc_anestesiologia)->where('estado', '1')->first();
        //dd($pre_anestesico);

        $rc_antecedentes = Rc_antecedentes::where('id_rc_pre_anestesica', $id_hc_anestesiologia)->get();

        $rc_examen = Rc_resultados::where('id_rc_pre_anestesia', $id_hc_anestesiologia)->get();

        $indicaciones = Rc_indicaciones::where('id_rc_pre_anestesica', $id_hc_anestesiologia)->get();

        if (is_null($pre_anestesico)) {

            $arr_riesgo_preanestesico = [

                'id_hc_anestesiologia' => $id_hc_anestesiologia,
                'estado'               => 1,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
            ];
            $id_rc = Rc_pre_anestesica::insertGetId($arr_riesgo_preanestesico);
            $pre_anestesico = Rc_pre_anestesica::find($id_rc);
        }
        $this->actualiza_informacion($pre_anestesico->id);
        //dd($id_hc_anestesiologia);
        return redirect(route('formato.rc_preanestesico_edit', ['pre_anestesico' => $pre_anestesico, 'id' => $id_hc_anestesiologia, 'rc_antecedentes' => $rc_antecedentes, 'examen' => $rc_examen, 'indicaciones' => $indicaciones]));
    }




    public function rc_preanestesico_edit($id)

    {
        $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $id)->first();

        $rc_antecedentes = Rc_antecedentes::where('id_hc_anestesiologia', $id)->where('estado', 1)->get();

        $rc_examen = Rc_resultados::where('id_hc_anestesiologia', $id)->where('estado', 1)->get();

        $indicaciones = Rc_indicaciones::where('id_hc_anestesiologia', $id)->where('estado', 1)->get();

        $hc_anestesiologia = Hc_Anestesiologia::where('id', $pre_anestesico->id_hc_anestesiologia)->first();
        $hc_procedimiento = hc_procedimientos::where('id', $hc_anestesiologia->id_hc_procedimientos)->first();
        $pentax = Pentax::where('hcid', $hc_procedimiento->id_hc)->first();
        //dd($hc_procedimiento);

        $this->actualiza_informacion($pre_anestesico->id);

        return view('rc_preanestesico/preanestesico_edit', ['pre_anestesico' => $pre_anestesico, 'id' => $id, 'rc_antecedentes' => $rc_antecedentes, 'examen' => $rc_examen, 'indicaciones' => $indicaciones, 'hc_procedimiento' => $hc_procedimiento, 'pentax' => $pentax]);
    }

    public function actualiza_informacion($id)
    {

        $pre_anestesico = Rc_pre_anestesica::find($id); //dd($id, $pre_anestesico);
        $csv_record = hc_anestesiologia_csv::where('id_hc_anestesiologia', $pre_anestesico->id_hc_anestesiologia)->first();

        if (!is_null($pre_anestesico)) {
            $cont = 0;
            $anestesiologia = $pre_anestesico->anestesiologia;
            $hc = $anestesiologia->historia;
            $paciente = $hc->paciente;
            //$post_anestesico = Hc_Evolucion_Anestesiologo::where('id_hc_anestesiologia',$anestesiologia->id)->where('estado','1')->first();

            if ($pre_anestesico->quirurgicos == null) {
                $cont++;
                $pre_anestesico->quirurgicos = $paciente->antecedentes_quir;
            }
            if ($pre_anestesico->alergicos == null) {
                $cont++;
                $alergiasxpac = $paciente->a_alergias;
                $txt_aler = '';
                foreach ($alergiasxpac as $ale_pac) {
                    $txt_aler .= ' + ' . $ale_pac->principio_activo->nombre;
                }
                $pre_anestesico->alergicos = $txt_aler;
            }
            if ($pre_anestesico->habitos == null) {
                $cont++;
                $pre_anestesico->habitos = $paciente->alcohol;
            }
            if ($pre_anestesico->lactato_ringer == null) {
                $cont++;
                $pre_anestesico->lactato_ringer = $anestesiologia->lactato_ringer;
            }
            if ($pre_anestesico->expansores == null) {
                $cont++;
                $pre_anestesico->expansores = $anestesiologia->expansores;
            }
            if ($pre_anestesico->sangre == null) {
                $cont++;
                $pre_anestesico->sangre = $anestesiologia->sangre_derivados;
            }
            if ($pre_anestesico->observaciones == null) {
                $cont++;
                $pre_anestesico->observaciones = $anestesiologia->observaciones;
            }
            if (!is_null($csv_record)) {
                if ($pre_anestesico->presion == null) {
                    $cont++;
                    //$pre_anestesico->presion = $hc->presion;
                    $pre_anestesico->presion = $csv_record->presion_arterial;
                }
                if ($pre_anestesico->pulso == null) {
                    $cont++;
                    //$pre_anestesico->pulso = $hc->pulso;
                    $pre_anestesico->pulso = $csv_record->pulso;
                }
                if ($pre_anestesico->o2 == null) {
                    $cont++;
                    //$pre_anestesico->o2 = $hc->o2;
                    $pre_anestesico->o2 = $csv_record->o2;
                }
                if ($pre_anestesico->temperatura == null) {
                    $cont++;
                    //$pre_anestesico->temperatura = $hc->temperatura;
                    $pre_anestesico->temperatura = $csv_record->temperatura;
                }
                if ($pre_anestesico->fecuencia_respiratoria == null) {
                    $cont++;
                    $pre_anestesico->fecuencia_respiratoria = $csv_record->respiracion;
                }
            }
            if ($pre_anestesico->sitio_1 == null) {
                $texto1 = "Miembro superior izquierdo";
                $texto2 = "Miembro superior derecho";
                $txt_rnd = '';
                $indiceAleatorio = random_int(0, 1);
                if ($indiceAleatorio === 0) {
                    $txt_rnd = $texto1;
                } else {
                    $txt_rnd = $texto2;
                }
                $pre_anestesico->sitio_1 = $txt_rnd;
            }
            if ($pre_anestesico->calibre_1 == null) {
                $pre_anestesico->calibre_1 = '20';
            }
            if ($pre_anestesico->observaciones == null) {
                $pre_anestesico->observaciones = 'PACIENTE PASA A SALA DE RECUPERACIÓN EN CONDICIONES ESTABLES';
            }
            if ($cont > 0) {
                $pre_anestesico->save();
            }
            for ($i = 1; $i <= 11; $i++) {
                $property = 'd' . $i;
                if (!is_null($anestesiologia->$property)) {
                    $droga = $anestesiologia->$property;
                    $droga_ad = Drogas_administradas::where('id_hc_anestesiologia', $anestesiologia->id)
                        ->where('drogas', $droga)
                        ->where('estado', '1')
                        ->first();
                    if (is_null($droga_ad)) {
                        $arr_dog_ad = [
                            'id_hc_anestesiologia' => $anestesiologia->id,
                            'estado'               => '1',
                            'drogas'               => $droga,
                            'id_usuariocrea'       => $anestesiologia->id_usuariocrea,
                            'id_usuariomod'        => $anestesiologia->id_usuariomod,
                            'ip_creacion'          => $anestesiologia->ip_creacion,
                            'ip_modificacion'      => $anestesiologia->ip_modificacion,
                        ];
                        Drogas_administradas::create($arr_dog_ad);
                    }
                }
            }
            $id_paciente = $hc->id_paciente;
            $fecha_orden = $anestesiologia->fecha;
            $orden = $this->getLastOrder($id_paciente, $fecha_orden);
            if (!is_null($orden)) {
                $resultados = $orden->resultados;
                if (count($resultados) > 0) {

                    foreach ($resultados as $resul) {
                        $examen = $resul->examen;
                        if (!is_null($examen)) {
                            $id_agrupador = $examen->id_agrupador;
                            if (!is_null($resul->valor)) {
                                if ($resul->certificado == '1') {
                                    if (!is_null($examen)) {
                                        $rc_resultado = Rc_resultados::where('id_rc_pre_anestesia', $pre_anestesico->id)->where('id_parametro', $resul->id_parametro)->where('id_orden', $resul->id_orden)->first();
                                        if (is_null($rc_resultado)) {
                                            if (!is_null($resul->id_agrupador_publico)) {
                                                $id_agrupador = $resul->id_agrupador_publico;
                                            }
                                            $arr_crea_ex_rc = [
                                                'id_examen'                 => $examen->id,
                                                'id_rc_pre_anestesia'       => $pre_anestesico->id,
                                                'id_hc_anestesiologia'      => $anestesiologia->id,
                                                'nombre'                    => $examen->nombre,
                                                'resultado'                 => $resul->valor,
                                                'estado'                    => '1',
                                                'id_parametro'              => $resul->id_parametro,
                                                'id_orden'                  => $resul->id_orden,
                                                'id_usuariocrea'            => $resul->id_usuariocrea,
                                                'id_usuariomod'             => $resul->id_usuariomod,
                                                'ip_creacion'               => $resul->ip_creacion,
                                                'ip_modificacion'           => $resul->ip_modificacion,
                                                'id_agrupador_publico'      => $id_agrupador,

                                            ];
                                            Rc_resultados::create($arr_crea_ex_rc);
                                        } else {
                                            $arr_upd = [
                                                'id_agrupador_publico'       => $id_agrupador,
                                            ];
                                            $rc_resultado->update($arr_upd);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    public function preanestesico_store(Request $request)
    {
        //dd($request->all());


        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;


        $arr_formato_consentimiento = [

            'id_hc_anestesiologia' => $request['id_hc_anestesiologia'],
            'id_rc_pre_anestesica'   => $request['id_rc_pre_anestesico'],
            'tratamiento'          => $request['tratamiento'],
            'diagnostico'          => $request['diagnosticos'],
            'tiempo_evolucion'     => $request['tiempoEvolucion'],
            'estado'               => 1,
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
        ];
        Rc_antecedentes::create($arr_formato_consentimiento);
        return response()->json(['success' => true, 'msj' => "GUARDADO"]);
    }


    public function preanestesico_delete($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        try {

            $forma = Rc_antecedentes::find($id);
            $arr_preanestesico = [
                'estado'               => 0,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
            ];
            $forma->update($arr_preanestesico);
            return json_encode(['status' => true, 'msj' => "Eliminado con exito"]);
        } catch (\Exception $e) {
            return json_encode(['status' => false, 'msj' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }




    public function rc_preanestesico_update(Request $request)
    {
        //dd($request->all());


        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $rc = Rc_pre_anestesica::where('id_hc_anestesiologia', $request->id_cab)->first();

        $arr_rc = [

            'id_hc_anestesiologia' => $request["id_cab"],
            'tipo'                  => $request["tipo"],
            'anestesico'            => $request["anestesico"],
            'quirurgicos'           => $request["quirurgicos"],
            'alergicos'             => $request["alergicos"],
            'transfuciones'         => $request["transfusiones"],
            'habitos'               => $request["habitos"],
            'riesgo_quirurgico'      => $request["riesgo_quirurgico"],
            'apertura_bucal'       => $request["apertura"],
            'distancia_tiromentoneana' => $request["distancia"],
            'mallanpati'           => $request["mallampati"],
            'protusion_mandibular' => $request["protrusion"],
            'perimetro_cervical'   => $request["perimetro"],
            'movilidad_cervical'   => $request["movilidad"],
            'intubacion_dificil'   => $request["historia"],
            'patologia'            => $request["patologia"],
            'torax'                => $request["torax"],
            'corazon'              => $request["corazon"],
            'pulmones'             => $request["pulmones"],
            'abdomen'              => $request["abdomen"],
            'extremidades'         => $request["extremidades"],
            'sistema_nervisoso'    => $request["sistema"],
            'equivalente_metabolico' => $request["equivalente"],
            'estado_fisico'        => $request["estado_fisico"],
            'riesgo_cardiaco'      => $request["riesgo_cardiaco"],
            'riesgo_pulmonar'      => $request["riesgo_pulmonar"],
            'riesgo_tromboembolico' => $request["riesgo_tromboembolico"],
            'liquidos_claros'      => $request["liquidos_claros"],
            'leche_materna'         => $request["leche_materna"],
            'solidos'              => $request["solidos"],
            'plan_anestesico'      => $request["plan_anestesico"],
            'observaciones'        => $request["observaciones"],
            'estado'               => 1,
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,

        ];
        if (!is_null($rc)) {
            $rc->update($arr_rc);
        } else {
            Rc_pre_anestesica::create($arr_rc);
        }


        return "ok";
    }

    public function examen_preanestesico_store(Request $request)
    {

        //dd($request->all());

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $arr_examen_preanestesico = [

            'id_hc_anestesiologia' => $request['id_hc_anestesiologia'],
            'id_rc_pre_anestesia'   => $request['id_rc_pre_anestesico'],
            'id_examen'            => $request['id_examen'],
            'nombre'               => $request['buscador'],
            'resultado'            => $request['resultado'],
            'estado'               => 1,
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
        ];
        Rc_resultados::create($arr_examen_preanestesico);
        return response()->json(['success' => true, 'msj' => "GUARDADO"]);
    }


    public function examen_preanestesico_delete($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        try {

            $resultado = Rc_resultados::find($id);
            $arr_preanestesico = [
                'estado'               => 0,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
            ];
            $resultado->update($arr_preanestesico);
            return json_encode(['status' => true, 'msj' => "Eliminado con exito"]);
        } catch (\Exception $e) {
            return json_encode(['status' => false, 'msj' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }




    public function rc_indicaciones_agregar(Request $request)
    {

        //dd($request->all());

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $arr_indicaciones = [

            'id_hc_anestesiologia'  => $request['id_hc_anestesiologia'],
            'id_rc_pre_anestesica'   => $request['id_rc_pre_anestesica'],
            'indicaciones'        => $request['indicaciones'],
            'estado'               => 1,
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
        ];
        //dd($arr_indicaciones);
        Rc_indicaciones::create($arr_indicaciones);
        return response()->json(['success' => true, 'msj' => "GUARDADO"]);
    }


    public function indicaciones_delete($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        try {

            $resultado = Rc_indicaciones::find($id);
            $arr_preanestesico = [
                'estado'               => 0,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
            ];
            $resultado->update($arr_preanestesico);
            return json_encode(['status' => true, 'msj' => "Eliminado con exito"]);
        } catch (\Exception $e) {
            return json_encode(['status' => false, 'msj' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }

    public function drogas_administradas_delete($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        try {

            $drogas_adm = Drogas_administradas::find($id);
            $arr_drogas = [
                'estado'               => 0,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
            ];
            $drogas_adm->update($arr_drogas);
            return json_encode(['status' => true, 'msj' => "Eliminado con exito"]);
        } catch (\Exception $e) {
            return json_encode(['status' => false, 'msj' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }






    public function formulario018($id)
    {

        $id_empresa = session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $pre_anestesico = Rc_pre_anestesica::where('id', $id)->first();


        $rc_antecedentes = Rc_antecedentes::where('id_rc_pre_anestesica', $id)->where('estado', 1)->get();

        $rc_examen = Rc_resultados::where('id_rc_pre_anestesia', $id)->where('estado', 1)->get();

        $indicaciones = Rc_indicaciones::where('id_rc_pre_anestesica', $id)->where('estado', 1)->get();

        $hc_anestesiologia = Hc_Anestesiologia::where('id', $pre_anestesico->id_hc_anestesiologia)->first();
        $hc_procedimiento = hc_procedimientos::where('id', $hc_anestesiologia->id_hc_procedimientos)->first();

        $cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_procedimiento->id)->where('ingreso_egreso', 'INGRESO')->first();
        //dd($cie10);


        $adicionales = Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_procedimiento->id)->get();

        $mas = true;
        $texto = "";
        foreach ($adicionales as $value2) {
            if ($mas == true) {
                $texto = $texto . $value2->procedimiento->nombre;
                $mas = false;
            } else {
                $texto = $texto . ' + ' . $value2->procedimiento->nombre;
            }
        }


        $agrupados = array();
        foreach ($rc_examen as $exam) {
            if (!is_null($exam->examen_parametro) && !is_null($exam->examen_agrupador)) {
                $agrupados[$exam->examen_agrupador->nombre][] = ['parametro' => $exam->examen_parametro->nombre, 'resultado' => $exam->resultado];
            }
        }
        //dd(count($agrupados));
        //dd($agrupados);
        $agrupados1 = $agrupados;
        $agrupados2 = [];
        $totalAgrupadores = count($agrupados);
        if ($totalAgrupadores > 4) {
            // Calcular el tama�o de cada parte
            $tamanoParte = ceil($totalAgrupadores / 2);

            // Dividir el array en dos partes iguales
            $agrupadosDivididos = array_chunk($agrupados, $tamanoParte, true); // 'true' para mantener las claves asociativas

            // Obtener la primera parte
            $agrupados1 = $agrupadosDivididos[0];

            // Obtener la segunda parte
            $agrupados2 = $agrupadosDivididos[1];
        }
        //dd($agrupados1,$agrupados2);
        $seguro = $hc_procedimiento->historia->seguro;
        $view = \View::make('pdf_hospital.formulario018', compact('seguro','cie10', 
        'hc_procedimiento', 
        'pre_anestesico', 
        'rc_antecedentes', 
        'rc_examen', 
        'indicaciones', 
        'empresa', 'texto', 'agrupados1', 'agrupados2'))->render();
        
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('HCU_018.pdf');
    }

    public function formulario018A($procedimiento_id)
    {

        $id_empresa = session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $record = Hc_Anestesiologia::where('id', $procedimiento_id)->first();
        $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $procedimiento_id)->first();
        $historia_clinica = Historiaclinica::where('hcid', $record->id_hc)->first();


        $drogas = Drogas_administradas::where('id_hc_anestesiologia', $procedimiento_id)->where('estado', 1)->get();



        $examen = Rc_resultados::where('id_hc_anestesiologia', $record->id)->where('estado', 1)->get();

        $hc_procedimiento = hc_procedimientos::where('id', $record->id_hc_procedimientos)->first();
        $aud_hc_procedimiento = Aud_Hc_Procedimientos::where('id_procedimientos_org', $hc_procedimiento->id)->first();
        $aud_protocolo = Aud_Hc_Protocolo::where('id_hc_procedimientos', $hc_procedimiento->id)->first();
        //$cie10 = DB::table('hc_cie10')->where('hc_cie10.hcid', $record->historia->hcid)->limit(2)->get();
        $cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_procedimiento->id)->where('ingreso_egreso', 'INGRESO')->first();
        $cie10_eg = Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_procedimiento->id)->where('ingreso_egreso', 'EGRESO')->first();

        $sistemas = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'SISTEMA')->get();
        $aparatos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'APARATO')->get();
        $manejos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'MANEJO')->get();
        $intubuaciones = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'INTUBACION')->get();
        $tipos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'TIPO')->get();
        $inducciones = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'INDUCCION')->get();
        $mantenimientos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'MANTENIMIENTO')->get();


        $rc_tecnicas = record_tecnicas_anestesicas::where('id_hc_anestesiologia', $procedimiento_id)->get();

        $agrupados = array();
        foreach ($examen as $exam) {
            if (!is_null($exam->examen_parametro) && !is_null($exam->examen_agrupador)) {
                $agrupados[$exam->examen_agrupador->nombre][] = ['parametro' => $exam->examen_parametro->nombre, 'resultado' => $exam->resultado];
            }
        }
        //dd(count($agrupados));
        //dd($agrupados);
        $agrupados1 = $agrupados;
        $agrupados2 = [];
        $totalAgrupadores = count($agrupados);
        if ($totalAgrupadores > 4) {
            // Calcular el tama�o de cada parte
            $tamanoParte = ceil($totalAgrupadores / 2);

            // Dividir el array en dos partes iguales
            $agrupadosDivididos = array_chunk($agrupados, $tamanoParte, true); // 'true' para mantener las claves asociativas

            // Obtener la primera parte
            $agrupados1 = $agrupadosDivididos[0];

            // Obtener la segunda parte
            $agrupados2 = $agrupadosDivididos[1];
        }
        //dd($agrupados1,$agrupados2);


        $view = \View::make('pdf_hospital.formulario018A', compact('cie10_eg', 'aud_protocolo', 'aud_hc_procedimiento', 'hc_procedimiento', 'historia_clinica', 'record', 'empresa', 'cie10', 'drogas', 'examen', 'sistemas', 'aparatos', 'manejos', 'intubuaciones', 'tipos', 'rc_tecnicas', 'inducciones', 'mantenimientos', 'pre_anestesico', 'agrupados1', 'agrupados2'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        //$pdf->setPaper('A4');
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('HCU_018A.pdf');
    }

    public function transanestesico_crea_registro($id_hc_anestesiologia)
    {
        //dd($id_hc_anestesiologia);


        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $hc_anestesiologia = Hc_Anestesiologia::find($id_hc_anestesiologia);

        if (is_null($hc_anestesiologia)) {

            $arr_postanestesico = [

                'id_hc' => $hc_anestesiologia["id_hc"],
                'hc_id_procedimiento'  => $hc_anestesiologia["id_hc_procedimientos"],
                'estado'               => 1,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
            ];
            $id_rc = Hc_Anestesiologia::insertGetId($arr_postanestesico);
            $hc_anestesiologia = Hc_Anestesiologia::find($id_rc);
        }
        //dd($id_hc_anestesiologia);
        return redirect(route('formato.transanestesico_edit', ['hc_anestesiologia' => $hc_anestesiologia]));
    }



    public function transanestesico_edit($id)

    {
        $transanestesico = Hc_Anestesiologia::where('id', $id)->first();

        $drogas = Drogas_administradas::where('id_hc_anestesiologia', $id)->where('estado', 1)->get();
        //dd($drogas);

        $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $id)->first();


        $examen = Rc_resultados::where('id_hc_anestesiologia', $transanestesico->id)->where('estado', 1)->get();
        //dd($examen);

        $sistemas = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'SISTEMA')->get();
        $aparatos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'APARATO')->get();
        $manejos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'MANEJO')->get();
        $intubuaciones = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'INTUBACION')->get();
        $tipos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'TIPO')->get();
        $inducciones = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'INDUCCION')->get();
        $mantenimientos = TecnicasAnestesicas::where('estado', 1)->where('tipo', 'MANTENIMIENTO')->get();

        $rc_tecnicas = record_tecnicas_anestesicas::where('id_hc_anestesiologia', $id)->get();



        return view('rc_preanestesico/transanestesico_edit', ['transanestesico' => $transanestesico, 'drogas' => $drogas, 'examen' => $examen, 'pre_anestesico' => $pre_anestesico, 'sistemas' => $sistemas, 'rc_tecnicas' => $rc_tecnicas, 'aparatos' => $aparatos, 'manejos' => $manejos, 'intubuaciones' => $intubuaciones, 'tipos' => $tipos, 'inducciones' => $inducciones, 'mantenimientos' => $mantenimientos]);
    }



    public function transanestesico_store(Request $request)
    {
        //dd($request->all());


        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;


        $arr_transanestesico = [

            'id_hc_anestesiologia' => $request['id_anestesiologia'],
            'drogas'               => $request['drogas'],
            'estado'               => 1,
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
        ];
        Drogas_administradas::create($arr_transanestesico);
        return response()->json(['success' => true, 'msj' => "GUARDADO"]);
    }

    public function transanestesico_guardar(Request $request)
    {

        //dd($request->all());
        $idusuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $id_hc = $request->input('id_hc');
        //dd($id_hc);
        $hc_anestesiologia = Hc_Anestesiologia::find($request->id);

        if ($hc_anestesiologia) {
            $valores = $request->except(['id_hc', '_token', 'id', 'id_hc_procedimientos']);
            //dd($valores);
            $updatedData = array_merge($valores, [
                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ]);

            foreach ($updatedData as $nombre => $valor) {
                if (Schema::hasColumn('hc_anestesiologia', $nombre)) {
                    $hc_anestesiologia->$nombre = $valor;
                }
            }
            //dd($hc_anestesiologia);

            $hc_anestesiologia->save();
        } else {
            $valores = $request->except('_token');
            $arr_transanestesico = [
                'id_hc' => $id_hc,
                'estado' => 1,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $hc_anestesiologia = Hc_Anestesiologia::create(array_merge($valores, $arr_transanestesico));
        }

        return response()->json(['success' => true, 'msj' => "GUARDADO"]);
    }
    public function imagen_pre_anestesico(Request $request)
    {
        $hc_anes = Hc_Anestesiologia::find($request->id);

        return view("canvasimagenes.transanestesico", ["id" => $request["id"], "tipo" => $request["tipo"], 'hc_anes' => $hc_anes]);
    }

    public function saveCanvas(Request $request)
    {
        try {
            $id = $request['id'];
            $id_update = Hc_Anestesiologia::find($id);
            $extension = 'record' . '_' . $id . date('Ymdhis') . '.png';

            $carpeta = 'trans/'; // Cambiado el nombre de la carpeta

            $rutaArchivo = $carpeta . $extension;

            if ($request["tipo"] == 0) {
                $id_update->tran_anastesico_path = $extension;
            } else {
                $id_update->post_anastesico_path = $extension;
            }
            $id_update->save();

            Storage::disk('hc_ima')->put($rutaArchivo, \File::get($request['my-file']));

            return response()->json('ok');
        } catch (\Throwable $th) {
            return response()->json('error' . $th->getMessage());
        }
    }
    public function rc_postanestesico_crea_registro($id_hc_anestesiologia)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $hc_anestesiologia = Hc_Anestesiologia::find($id_hc_anestesiologia);
        $hc_evolucion_anestesiologo = Hc_Evolucion_Anestesiologo::where('hc_id_procedimiento', $hc_anestesiologia->id_hc_procedimientos)->first();


        if (!is_null($hc_evolucion_anestesiologo)) {

            return redirect(route('formato.rc_postanestesico_edit', [
                'hc_evolucion_anestesiologo' => $hc_evolucion_anestesiologo,
                'hc_anestesiologia' => $hc_anestesiologia
            ]));
        } else {

            $arr_postanestesico = [
                'id_hc' => $hc_anestesiologia->id_hc,
                'hc_id_procedimiento' => $hc_anestesiologia->id_hc_procedimientos,
                'id_hc_anestesiologia' => $id_hc_anestesiologia,
                'estado' => 1,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $id_rc = Hc_Evolucion_Anestesiologo::insertGetId($arr_postanestesico);
            $hc_evolucion_anestesiologo = Hc_Evolucion_Anestesiologo::find($id_rc);
        }


        return redirect(route('formato.rc_postanestesico_edit', [
            'hc_evolucion_anestesiologo' => $hc_evolucion_anestesiologo,
            'hc_anestesiologia' => $hc_anestesiologia
        ]));
    }
    public function actualiza_informacion_post($id)
    {

        $post_anestesico = Hc_Evolucion_Anestesiologo::find($id); //dd($id, $post_anestesico);

        if (!is_null($post_anestesico)) {
            $cont = 0;
            $anestesiologia = $post_anestesico->hc_anestesiologia;

            $hc = $anestesiologia->historia;
            $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $anestesiologia->id)->where('estado', '1')->first();
            //$paciente = $hc->paciente;

            if ($post_anestesico->presion == null) {
                $cont++;
                $post_anestesico->presion = $pre_anestesico->presion;
            }
            if ($post_anestesico->pulso == null) {
                $cont++;
                $post_anestesico->pulso = $pre_anestesico->pulso;
            }
            if ($post_anestesico->o2 == null) {
                $cont++;
                $post_anestesico->o2 = $pre_anestesico->o2;
            }
            if ($post_anestesico->temperatura == null) {
                $cont++;
                $post_anestesico->temperatura = $pre_anestesico->temperatura;
            }
            if ($cont > 0) {
                $post_anestesico->save();
            }
        }
    }
    public function rc_postanestesico_edit($id_hc_evolucion_anestesiologo, $hc_anestesiologia)
    {
        //dd($id_hc_evolucion_anestesiologo);

        $post_anestesico = Hc_Evolucion_Anestesiologo::find($id_hc_evolucion_anestesiologo);
        //$pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia',$hc_anestesiologia->id)->where('estado','1')->first();
        $anestesiologia = Hc_Anestesiologia::find($post_anestesico->id_hc_anestesiologia);
        $procedimiento_id = hc_procedimientos::where('id', $post_anestesico->procedimiento->id)->first();
        if (!is_null($procedimiento_id)) {
            $historia = Historiaclinica::find($procedimiento_id->id_hc);

            if (!is_null($historia)) {
                $paciente = Paciente::find($historia->id_paciente);
                $agenda = agenda::find($historia->id_agenda);
                $especialidad = Especialidad::find($agenda->espid);
                //dd($especialidad);
            }


            /*$cirugia = '';
            $hc_procedimiento_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento_id)->get();
            //$anestesiologia = Hc_Anestesiologia::where('id_hc_procedimientos', $procedimiento_id)->first();
            //dd($anestesiologia);
            if (count($hc_procedimiento_final) > 1) {
                $cirugia = $hc_procedimiento_final[0]->procedimiento->nombre;
                for ($i = 1; $i < count($hc_procedimiento_final); $i++) {
                    $cirugia = $cirugia . ' + ' . $hc_procedimiento_final[$i]->procedimiento->nombre;
                }
            } elseif (count($hc_procedimiento_final) == 1) {
                $cirugia = $hc_procedimiento_final[0]->procedimiento->nombre;
            }*/

            $adicionales = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento_id)->get();

            $mas = true;
            $cirugia = "";
            foreach ($adicionales as $value2) {
                if ($mas == true) {
                    $cirugia = $cirugia . $value2->procedimiento->nombre;
                    $mas = false;
                } else {
                    $cirugia = $cirugia . ' + ' . $value2->procedimiento->nombre;
                }
            }
        }


        $this->actualiza_informacion_post($post_anestesico->id);

        return view('rc_preanestesico/postanestesico_edit', ['post_anestesico' => $post_anestesico, 'especialidad' => $especialidad, 'cirugia' => $cirugia, 'anestesiologia' => $anestesiologia]);
    }
    public function postanestesico_guardar(Request $request)
    {
        $this->validate($request, [
            'id_hc_anestesiologia' => 'required|integer|exists:hc_anestesiologia,id',
        ]);
        $idusuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $id_hc = $request->input('id_hc');
        $id_hc_anestesiologia = $request->input('id_hc_anestesiologia');
        $hc_evolucion_anestesiologo = Hc_Evolucion_Anestesiologo::where('id_hc_anestesiologia', $id_hc_anestesiologia)->first();

        if ($hc_evolucion_anestesiologo) {
            $valores = $request->except(['id_hc', 'id_hc_anestesiologia', '_token']);
            //dd($valores);
            $updatedData = array_merge($valores, [
                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ]);

            foreach ($updatedData as $nombre => $valor) {
                if (Schema::hasColumn('hc_evolucion_anestesiologo', $nombre)) {
                    $hc_evolucion_anestesiologo->$nombre = $valor;
                }
            }

            $hc_evolucion_anestesiologo->save();
        } else {
            $valores = $request->except('_token');
            $anestesiologia = Hc_Anestesiologia::findOrFail($id_hc_anestesiologia);
            $arr_transanestesico = [
                'id_hc' => $anestesiologia->id_hc,
                'id_hc_anestesiologia' => $anestesiologia->id,
                'hc_id_procedimiento' => $anestesiologia->id_hc_procedimientos,
                'estado' => 1,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $hc_evolucion_anestesiologo = Hc_Evolucion_Anestesiologo::create(array_merge($valores, $arr_transanestesico));
        }
        return response()->json(['success' => true, 'msj' => "GUARDADO"]);
    }
    public function crear_anestesico($id_procedimiento)
    {

        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');

        $record_anestesiologico = Hc_Anestesiologia::where('id_hc_procedimientos', '=', $id_procedimiento)->first();
        $procedimiento = hc_procedimientos::find($id_procedimiento);

        if (!is_null($record_anestesiologico)) {

            $id = $record_anestesiologico->id;
        } else {

            $input1 = [
                'id_hc'                => $procedimiento->id_hc,
                'fecha'                => date('Y-m-d H:i:s'),
                'id_hc_procedimientos' => $id_procedimiento,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
            ];

            $id_record = Hc_Anestesiologia::insertGetId($input1);

            $id = $id_record;
        }


        return redirect(route('formato.rc_preanestesico_crear', ['id' => $id]));
    }
    public function carga_tabla_indicacion($id_rc)
    {

        $indicaciones = Rc_indicaciones::where('id_rc_pre_anestesica', $id_rc)->where('estado', 1)->get();

        return view('rc_preanestesico/tabla_indicacion', ['indicaciones' => $indicaciones]);
    }
    public function carga_tabla_examenes($id_rc)
    {


        $rc_examen = Rc_resultados::where('id_rc_pre_anestesia', $id_rc)->where('estado', 1)->get();


        return view('rc_preanestesico/tabla_examen', ['examen' => $rc_examen]);
    }
    public function carga_tabla_diagnosticos($id_rc)
    {

        $rc_antecedentes = Rc_antecedentes::where('id_rc_pre_anestesica', $id_rc)->where('estado', 1)->get();

        return view('rc_preanestesico/tabla_diagnostico', ['rc_antecedentes' => $rc_antecedentes]);
    }
    public function load($name)
    {
        $path = storage_path() . '/app/hc_ima/trans/' . $name;
        //dd($path);
        if (file_exists($path)) {
            ob_end_clean();
            return Response::download($path);
        }
    }

    public function actualiza_tecnicas(Request $request)
    {

        //dd($request->all());
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $rc_tecnica = record_tecnicas_anestesicas::where('id_hc_anestesiologia', $request->id_hc_anestesiologia)->where('id_tecnicas_anestesicas', $request->tecnica)->first();

        if ($request->crear == '0') { // eliminar

            if (!is_null($rc_tecnica)) {
                $rc_tecnica->delete();
            }
        } else { // crear 
            if (is_null($rc_tecnica)) {
                $arr = [
                    'id_tecnicas_anestesicas' => $request->tecnica,
                    'id_hc_anestesiologia' => $request->id_hc_anestesiologia,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                ];
                record_tecnicas_anestesicas::create($arr);
            }
        }
    }

    public function guardar_trans(Request $request)
    {

        //dd($request->all());
        $id_trans = $request->id_trans;
        $pre_anestesico = Rc_pre_anestesica::find($id_trans);

        foreach ($request->all() as $key => $value) {
            if ($key != 'id_trans' && $key != 'borbotaje' && $key != 'acceso' && $key != 'escala' && $key != 'feto' && $key != 'new_tecnicas_especiales' && $key != 'mantenimiento_temperatura' && $key != 'duracion_anestesia' && $key != 'duracion_operacion' && $key != 'indice' && $key != 'condiciones_al_salir' && $key != 'conducido') {
                $pre_anestesico->$key = $value;
            }
        }
        $pre_anestesico->save();

        return "ok";
    }

    public function getLastOrder($id_paciente, $fecha_orden)
    {
        $fecha_orden = Carbon::parse($fecha_orden);
        $order = Examen_Orden::where('id_paciente', $id_paciente)
            ->where('estado', '1')
            ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, created_at, ?)) DESC', [$fecha_orden])
            ->first();
        return $order;
    }
    public function carga_tabla_drogas($id_rc)
    {

        $drogas = Drogas_administradas::where('id_hc_anestesiologia', $id_rc)->where('estado', 1)->get();

        return view('rc_preanestesico/tabla_drogas', ['drogas' => $drogas]);
    }
}
