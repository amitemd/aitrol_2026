<?php

namespace Sis_medico\Http\Controllers\Rc_pre_anestesica;

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
use Sis_medico\hc_receta;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Services\ClinicalMedicationAdministrationService;
use Sis_medico\Exceptions\ClinicalMedicationBlockedException;
use Schema;
use Sis_medico\Agenda;
use Sis_medico\analgesia;
use Sis_medico\Especialidad;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_procedimientos;
use Sis_medico\Http\Controllers\Apis\FirmadorController;
use Sis_medico\Paciente;
use Sis_medico\Pentax;

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

        $indicaciones = Rc_indicaciones::where('id_rc_pre_anestesica',$id_hc_anestesiologia)->get();

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

            $this->actualiza_informacion($pre_anestesico->id);

            
        } 
        //dd($id_hc_anestesiologia);
        return redirect(route('formato.rc_preanestesico_edit', ['pre_anestesico' => $pre_anestesico, 'id' => $id_hc_anestesiologia, 'rc_antecedentes' => $rc_antecedentes , 'examen' => $rc_examen , 'indicaciones' => $indicaciones ]));
        
       
    }




    public function rc_preanestesico_edit($id)

    {
        $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $id)->first();
   
        $rc_antecedentes = Rc_antecedentes::where('id_hc_anestesiologia', $id)->where('estado', 1)->get();

        $rc_examen = Rc_resultados::where('id_hc_anestesiologia', $id)->where('estado', 1)->get();

        $indicaciones = Rc_indicaciones::where('id_hc_anestesiologia', $id)->where('estado', 1)->get();

        $hc_anestesiologia = Hc_Anestesiologia::where('id',$pre_anestesico->id_hc_anestesiologia)->first();
        $hc_procedimiento = hc_procedimientos::where('id', $hc_anestesiologia->id_hc_procedimientos)->first();
        $pentax = Pentax::where('hcid', $hc_procedimiento->id_hc)->first();
        //dd($hc_procedimiento);
        
        $this->actualiza_informacion($pre_anestesico->id);

        return view('rc_preanestesico/preanestesico_edit', ['pre_anestesico' => $pre_anestesico, 'id' => $id, 'rc_antecedentes' => $rc_antecedentes , 'examen' => $rc_examen , 'indicaciones' => $indicaciones,'hc_procedimiento' => $hc_procedimiento,'pentax'=> $pentax ]);
    }

    public function actualiza_informacion( $id ){

        $pre_anestesico = Rc_pre_anestesica::find($id);//dd($id, $pre_anestesico);
        
        if(!is_null($pre_anestesico)){
            $cont = 0;
            $anestesiologia = $pre_anestesico->anestesiologia;
            $hc = $anestesiologia->historia;
            $paciente = $hc->paciente;

            if($pre_anestesico->quirurgicos == null){
                $cont ++;
                $pre_anestesico->quirurgicos = $paciente->antecedentes_quir;    
            }
            if($pre_anestesico->alergicos == null){
                $cont ++;
                $alergiasxpac = $paciente->a_alergias;
                $txt_aler = '';
                foreach($alergiasxpac as $ale_pac){
                    $txt_aler.= ' + '.$ale_pac->principio_activo->nombre;
                }
                $pre_anestesico->alergicos = $txt_aler;                         

            }
            if($pre_anestesico->habitos == null){
                $cont ++;
                $pre_anestesico->habitos = $paciente->alcohol;                         

            }
            if($cont > 0){
                $pre_anestesico->save();
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
            'id_rc_pre_anestesica'   =>$request['id_rc_pre_anestesico'],
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
            // The legacy form autosaves partial payloads. Do not erase existing
            // clinical values when a control is absent from that payload.
            foreach ($arr_rc as $field => $value) {
                if ($field !== 'id_hc_anestesiologia' && !$request->has($this->requestFieldForPreanesthetic($field)) && array_key_exists($field, $rc->getAttributes())) {
                    unset($arr_rc[$field]);
                }
            }
            $rc->update($arr_rc);
        } else {
            Rc_pre_anestesica::create($arr_rc);
        }


        return "ok";
    }

    private function requestFieldForPreanesthetic($field)
    {
        return [
            'transfuciones' => 'transfusiones',
            'apertura_bucal' => 'apertura',
            'distancia_tiromentoneana' => 'distancia',
            'mallanpati' => 'mallampati',
            'protusion_mandibular' => 'protrusion',
            'perimetro_cervical' => 'perimetro',
            'movilidad_cervical' => 'movilidad',
            'intubacion_dificil' => 'historia',
            'sistema_nervisoso' => 'sistema',
            'equivalente_metabolico' => 'equivalente',
        ][$field] ?? $field;
    }

    public function examen_preanestesico_store(Request $request)
    {

        //dd($request->all());

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $arr_examen_preanestesico = [

            'id_hc_anestesiologia' => $request['id_hc_anestesiologia'],
            'id_rc_pre_anestesia'   =>$request['id_rc_pre_anestesico'],
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

            'id_hc_anestesiologia'  =>$request['id_hc_anestesiologia'],
            'id_rc_pre_anestesica'   =>$request['id_rc_pre_anestesica'],
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






    public function formulario018($id)
    {

        $id_empresa = session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $pre_anestesico = Rc_pre_anestesica::where('id', $id)->first();
       
       
        $rc_antecedentes = Rc_antecedentes::where('id_rc_pre_anestesica', $id)->where('estado', 1)->get();

        $rc_examen = Rc_resultados::where('id_rc_pre_anestesia', $id)->where('estado', 1)->get();

        $indicaciones = Rc_indicaciones::where('id_rc_pre_anestesica', $id)->where('estado', 1)->get();
        
        $hc_anestesiologia = Hc_Anestesiologia::where('id',$pre_anestesico->id_hc_anestesiologia)->first();
        $hc_procedimiento = hc_procedimientos::where('id', $hc_anestesiologia->id_hc_procedimientos)->first();

        $adicionales = Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_procedimiento->id)->get();

        $mas = true;
        $texto = "";
        foreach($adicionales as $value2)
        {
            if($mas == true){
                $texto = $texto.$value2->procedimiento->nombre ;
                $mas = false;
            }
            else{
                $texto = $texto.' + '. $value2->procedimiento->nombre ;
            }
        }

        $view = \View::make('pdf_hospital.formulario018',compact('hc_procedimiento','pre_anestesico','rc_antecedentes','rc_examen','indicaciones','empresa','texto'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('formulario018.pdf');
    }

    public function formulario018A($procedimiento_id)
    {

        $id_empresa = session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $record = Hc_Anestesiologia::where('id', $procedimiento_id)->first();
        
        
       
        $drogas = Drogas_administradas::where('id_hc_anestesiologia', $procedimiento_id)->where('estado', 1)->get();

        $cie10 = DB::table('hc_cie10')->where('hc_cie10.hcid', $record->historia->hcid)->limit(2)->get();

        $examen = Rc_resultados::where('id_hc_anestesiologia', $record->id)->where('estado', 1)->get();

        $hc_procedimiento = hc_procedimientos::where('id', $record->id_hc_procedimientos)->first();



        $transanestesicoImage = null;
        if (!empty($record->tran_anastesico_path)) {
            $imageName = basename($record->tran_anastesico_path);
            $imagePaths = [
                public_path('assets/images/trasnanestesico/'.$imageName),
                storage_path('app/hc_ima/trans/'.$imageName),
            ];
            foreach ($imagePaths as $imagePath) {
                if (is_file($imagePath)) {
                    $transanestesicoImage = 'data:image/png;base64,'.base64_encode(file_get_contents($imagePath));
                    break;
                }
            }
        }

        $viewData = compact('hc_procedimiento', 'record', 'empresa', 'cie10', 'drogas', 'examen', 'transanestesicoImage');

        // Compatibilidad durante despliegues donde Composer todavía no ha
        // instalado FPDI: se conserva el PDF anterior en vez de responder 500.
        if (!class_exists(\setasign\Fpdi\Fpdi::class)) {
            $fallbackView = \View::make('pdf_hospital.formulario018A', array_merge($viewData, [
                'pdfPage' => 'all',
                'pdfPageNumber' => 1,
            ]))->render();
            $fallbackPdf = \App::make('dompdf.wrapper');
            $fallbackPdf->setPaper('A4', 'landscape');
            $fallbackPdf->loadHTML($fallbackView);

            return $fallbackPdf->stream('formulario018A.pdf');
        }

        // Dompdf mueve tablas completas a una página adicional cuando los
        // datos ocupan unos píxeles más. Se prueban perfiles progresivamente
        // compactos hasta garantizar que A-J permanezca en una sola hoja.
        $landscapeContent = null;
        $landscapeProfiles = [
            ['canvas' => 280, 'font' => 5, 'line' => 0.88, 'logo' => 52],
            ['canvas' => 240, 'font' => 4.7, 'line' => 0.82, 'logo' => 48],
            ['canvas' => 200, 'font' => 4.4, 'line' => 0.76, 'logo' => 44],
            ['canvas' => 160, 'font' => 4.1, 'line' => 0.70, 'logo' => 40],
            ['canvas' => 120, 'font' => 3.8, 'line' => 0.65, 'logo' => 36],
        ];

        foreach ($landscapeProfiles as $profile) {
            $landscapeView = \View::make('pdf_hospital.formulario018A', array_merge($viewData, [
                'pdfPage' => 'first',
                'pdfPageNumber' => 1,
                'firstPageCanvasHeight' => $profile['canvas'],
                'firstPageFontSize' => $profile['font'],
                'firstPageLineHeight' => $profile['line'],
                'firstPageLogoHeight' => $profile['logo'],
            ]))->render();
            $landscapePdf = \App::make('dompdf.wrapper');
            $landscapePdf->setPaper('A4', 'landscape');
            $landscapePdf->loadHTML($landscapeView);
            $landscapePdf->render();
            $landscapeContent = $landscapePdf->output();

            if ($landscapePdf->getDomPDF()->getCanvas()->get_page_count() === 1) {
                break;
            }
        }

        $portraitView = \View::make('pdf_hospital.formulario018A', array_merge($viewData, [
            'pdfPage' => 'second',
            'pdfPageNumber' => 2,
        ]))->render();
        $portraitPdf = \App::make('dompdf.wrapper');
        $portraitPdf->setPaper('A4', 'portrait');
        $portraitPdf->loadHTML($portraitView);
        $portraitPdf->render();
        $portraitContent = $portraitPdf->output();

        $mergedPdf = new \setasign\Fpdi\Fpdi();
        $mergedPdf->SetAutoPageBreak(false);
        foreach ([$landscapeContent, $portraitContent] as $documentIndex => $pdfContent) {
            $pageCount = $mergedPdf->setSourceFile(
                \setasign\Fpdi\PdfParser\StreamReader::createByString($pdfContent)
            );

            for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                $template = $mergedPdf->importPage($pageNumber);
                $size = $mergedPdf->getTemplateSize($template);

                // A-J ya se renderiza en una sola hoja. Se amplía únicamente
                // en vertical para aprovechar el espacio libre, conservando
                // las tablas y textos como contenido vectorial del PDF.
                if ($documentIndex === 0 && $pageNumber === 1) {
                    $mergedPdf->AddPage('L', 'A4');
                    $mergedPdf->useTemplate($template, 0, 0, 297, 252);
                    $mergedPdf->SetDrawColor(153, 153, 153);
                    $mergedPdf->Line(0, 203, 297, 203);
                    $mergedPdf->SetFont('Arial', 'B', 6);
                    $mergedPdf->SetTextColor(0, 0, 0);
                    $mergedPdf->SetXY(1, 204);
                    $mergedPdf->Cell(147, 4, 'SNS-MSP / HCU-form.018A / 2021', 0, 0, 'L');
                    $mergedPdf->Cell(148, 4, iconv('UTF-8', 'windows-1252//TRANSLIT', 'TRANSANESTÉSICO (1)'), 0, 0, 'R');
                    continue;
                }

                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                $mergedPdf->AddPage($orientation, [$size['width'], $size['height']]);
                $mergedPdf->useTemplate($template);
            }
        }

        return response($mergedPdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="formulario018A.pdf"',
        ]);
        
    }

    public function transanestesico_crea_registro($id_hc_anestesiologia)
    {


        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $hc_anestesiologia = Hc_Anestesiologia::find($id_hc_anestesiologia);

        abort_unless($hc_anestesiologia, 404, 'No se encontró el registro de anestesia.');
        return redirect(route('formato.transanestesico_edit', ['id' => $hc_anestesiologia->id]));
    }



    public function transanestesico_edit($id)

    {

        $transanestesico = Hc_Anestesiologia::where('id', $id)->first();
        abort_unless($transanestesico, 404, 'No se encontró el registro transanestésico.');

        $drogas = Drogas_administradas::where('id_hc_anestesiologia', $id)->where('estado', 1)->get();
        $receta = hc_receta::where('id_hc', $transanestesico->id_hc)->orderBy('id', 'desc')->first();
        $medicationOrders = $receta ? $receta->detalles : collect();
        //dd($drogas);

        $pre_anestesico = Rc_pre_anestesica::where('id_hc_anestesiologia', $id)->first();
        

        $examen = Rc_resultados::where('id_hc_anestesiologia', $transanestesico->id)->where('estado', 1)->get();
        //dd($examen);


        return view('rc_preanestesico/transanestesico_edit', compact('transanestesico', 'drogas', 'examen', 'pre_anestesico', 'medicationOrders'));
    }



    public function transanestesico_store(Request $request)
    {
        //dd($request->all());


        try {
            $anesthesia = Hc_Anestesiologia::with('historia')->find($request->id_anestesiologia);
            $detail = hc_receta_detalle::with('hc_receta.historia')->find($request->recipe_detail_id);
            if (!$anesthesia || !$anesthesia->historia || !$detail || !$detail->hc_receta || !$detail->hc_receta->historia
                || (string) $anesthesia->historia->encounter_id !== (string) $detail->hc_receta->historia->encounter_id) {
                throw new ClinicalMedicationBlockedException(array('La orden seleccionada no corresponde al episodio quirúrgico.'));
            }
            $administration = app(ClinicalMedicationAdministrationService::class)
                ->registerLegacyRecipeDetail($detail->id, $request->all(), Auth::user()->id, $request->ip());
            return response()->json(array('success' => true, 'msj' => 'Administración anestésica registrada en eMAR.', 'administration_id' => $administration->id));
        } catch (ClinicalMedicationBlockedException $exception) {
            return response()->json(array('success' => false, 'msj' => implode("\n", $exception->errors())), 422);
        }
    }

    public function transanestesico_guardar(Request $request)
    {

        //dd($request->all());
        $idusuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $id_hc = $request->input('id_hc');
        //dd($id_hc);
        $hc_anestesiologia = Hc_Anestesiologia::where('id_hc', $id_hc)->first();

        if ($hc_anestesiologia) {
            $valores = $request->except(['id_hc', '_token']);
            $updatedData = array_merge($valores, [
                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ]);

            foreach ($updatedData as $nombre => $valor) {
                if (Schema::hasColumn('hc_anestesiologia', $nombre)) {
                    $hc_anestesiologia->$nombre = $valor;
                }
            }

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
        //dd($request->all());
        $hc_anes = Hc_Anestesiologia::find($request->id);
        
        return view("canvasimagenes.transanestesico", ["id" => $request["id"], "tipo" => $request["tipo"], 'hc_anes' => $hc_anes]);
    }
    
        public function saveCanvas(Request $request)
    {
        try {
            $id = $request['id'];
            $id_update = Hc_Anestesiologia::find($id);
            if (!$id_update) {
                return response()->json('errorRegistro no encontrado');
            }

            $extension = 'record_' . ($request["tipo"] == 0 ? 'trans' : 'post') . '_' . $id . '_' . date('YmdHis') . '.png';
            $carpeta = $request["tipo"] == 0 ? 'trasnanestesico' : 'postanestesico';
            $directorio = public_path('assets/images/'.$carpeta);
            if (!is_dir($directorio)) {
                mkdir($directorio, 0775, true);
            }

            if ($request["tipo"] == 0) {
                $id_update->tran_anastesico_path = $extension;
            } else {
                $id_update->post_anastesico_path = $extension;
            }
            $id_update->save();

            $archivoTemporal = $request->file('my-file')
                ? $request->file('my-file')->getRealPath()
                : $request['my-file'];
            \File::put($directorio.'/'.$extension, \File::get($archivoTemporal));

            // El PNG se mantiene para los PDF. El JSON conserva trazos y
            // símbolos como objetos editables al volver a abrir el registro.
            if ($request->filled('canvas_json')) {
                $canvasJson = $request->input('canvas_json');
                json_decode($canvasJson, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \RuntimeException('El estado editable del lienzo no es válido.');
                }

                $jsonName = pathinfo($extension, PATHINFO_FILENAME).'.json';
                \File::put($directorio.'/'.$jsonName, $canvasJson);
            }

            return response()->json('ok');
        } catch (\Throwable $th) {
            return response()->json('error'. $th->getMessage());
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
                'id_hc_anesteciologia' => $hc_anestesiologia->id,
                'id_hc_evolucion_anes' => $hc_evolucion_anestesiologo->id,
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
            'id_hc_anesteciologia' => $hc_anestesiologia->id,
            'id_hc_evolucion_anes' => $hc_evolucion_anestesiologo->id,
        ]));
    }
	public function rc_postanestesico_edit($id_hc_anestesiologia, $id_hc_evolucion_anestesiologo)
    {
        // La ruta recibe primero el registro anestesiológico y después la
        // evolución postanestésica. Antes se interpretaban en orden inverso.
        $post_anestesico = Hc_Evolucion_Anestesiologo::findOrFail($id_hc_evolucion_anestesiologo);
        $anestesiologia = Hc_Anestesiologia::findOrFail($id_hc_anestesiologia);

        $procedimientoId = $post_anestesico->hc_id_procedimiento;
        if (empty($procedimientoId)) {
            $procedimientoId = $anestesiologia->id_hc_procedimientos;
        }

        $procedimiento = hc_procedimientos::findOrFail($procedimientoId);
        $historia = Historiaclinica::findOrFail($procedimiento->id_hc);

        // Permite abrir registros antiguos con claves de relación vacías sin
        // modificar silenciosamente la información almacenada.
        $post_anestesico->hc_id_procedimiento = $procedimiento->id;
        $post_anestesico->id_hc = $historia->id;
        $post_anestesico->setRelation('procedimiento', $procedimiento);
        $post_anestesico->setRelation('historiaclinica', $historia);
        $post_anestesico->setRelation('hc_anestesiologia', $anestesiologia);

        $agenda = Agenda::find($historia->id_agenda);
        $especialidad = $agenda ? Especialidad::find($agenda->espid) : null;

        $cirugia = Hc_Procedimiento_Final::with('procedimiento')
            ->where('id_hc_procedimientos', $procedimiento->id)
            ->get()
            ->map(function ($procedimientoFinal) {
                return optional($procedimientoFinal->procedimiento)->nombre;
            })
            ->filter()
            ->implode(' + ');

        return view('rc_preanestesico/postanestesico_edit', compact('post_anestesico', 'especialidad', 'cirugia'));
    }
    public function postanestesico_guardar(Request $request)
    {

        //dd($request->all());
        $idusuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $id_hc = $request->input('id_hc');
        //dd($id_hc);
        $hc_evolucion_anestesiologo = Hc_Evolucion_Anestesiologo::where('id_hc', $id_hc)->first();

        if ($hc_evolucion_anestesiologo) {
            $valores = $request->except(['id_hc', '_token']);
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
            $arr_transanestesico = [
                'id_hc' => $id_hc,
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
    public function crear_anestesico($id_procedimiento){

        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');

        $record_anestesiologico = Hc_Anestesiologia::where('id_hc_procedimientos', '=', $id_procedimiento)->first();
        $procedimiento = hc_procedimientos::find($id_procedimiento);
        abort_unless($procedimiento, 404, 'No se encontró el procedimiento clínico.');

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

        
        return redirect(route('formato.rc_preanestesico_crear', ['id' => $id ]));
        

    }
    public function carga_tabla_indicacion($id_rc){
        
        $indicaciones = Rc_indicaciones::where('id_rc_pre_anestesica', $id_rc)->where('estado', 1)->get();

        return view('rc_preanestesico/tabla_indicacion', ['indicaciones' => $indicaciones ]);

    }
    public function carga_tabla_examenes($id_rc){


        $rc_examen = Rc_resultados::where('id_rc_pre_anestesia', $id_rc)->where('estado', 1)->get();


        return view('rc_preanestesico/tabla_examen', ['examen' => $rc_examen ]);

    } 
    public function carga_tabla_diagnosticos($id_rc){

        $rc_antecedentes = Rc_antecedentes::where('id_rc_pre_anestesica', $id_rc)->where('estado', 1)->get();

        return view('rc_preanestesico/tabla_diagnostico', ['rc_antecedentes' => $rc_antecedentes ]);

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
}
