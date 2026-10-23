<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Log;
use Sis_medico\hc_receta;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Medicina;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Principio_Activo;
use Sis_medico\Nota_entrega;
use Sis_medico\Nota_entrega_detalle;
use Sis_medico\Log_nota_entrega;
use Sis_medico\Planilla_Cabecera;
use Sis_medico\Insumo_Plantilla_Control;
use Sis_medico\Medicina_tarifario;
use Sis_medico\Empresa;
use Sis_medico\Nota_Entrega_Escaneados;
use Sis_medico\Log_usuario;
use Sis_medico\Especialidad;
use Sis_medico\procedimiento_completo;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\Paciente_Observaciones;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\hc_child_pugh;
use Sis_medico\hc_imagenes_protocolo;
use Cookie;

class RecetaController extends Controller
{
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3, 6, 11, 7, 5, 30, 11)) == false) {
            return true;
        }
    }

    public function mostrar($id)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $id)
            ->first();

        $paciente = Paciente::find($agenda->id_paciente);

        $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '1')->get(); //3=DOCTORES;

        $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->where('estado', '1')->get(); //6=ENFERMEROS;

        $hcp = DB::select("SELECT h.*, e.nombre as especialidad, s.nombre as snombre ,d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2, a.proc_consul as tipo_cita, a.fechaini as fechainicio
                            FROM historiaclinica h, seguros s, agenda a, especialidad e, users d
                            WHERE h.id_paciente = " . $agenda->id_paciente . " AND
                            a.id = h.id_agenda AND
                            s.id = h.id_seguro AND
                            a.espid =  e.id AND
                            a.id_doctor1 = d.id AND
                            h.id_agenda <> " . $id . "
                            ORDER BY a.fechaini DESC");

        $hca = DB::table('historiaclinica')
            ->where('id_agenda', '=', $id)
            ->first();
        $receta = hc_receta::where('id_hc', '=', $hca->hcid)->first();
        $seguro = Seguro::find($hca->id_seguro);

        $alergiasxpac = Paciente_Alergia::where('id_paciente', $paciente->id)->get();

        return view('hc_admision/receta/receta', ['agenda' => $agenda, 'paciente' => $paciente, 'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'hcp' => $hcp, 'hca' => $hca, 'seguro' => $seguro, 'receta' => $receta, 'alergiasxpac' => $alergiasxpac]);
    }

    public function crear_actualizar(Request $request)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $receta = hc_receta::where('id_hc', '=', $request["id_hc"])->first();

        if ($receta == "") {
            $input1 = [
                'rp'              => $request["rp"],
                'prescripcion'    => $request["prescripcion"],
                'id_hc'           => $request["id_hc"],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];

            hc_receta::insert($input1);
        } else {
            $id     = $receta->id;
            $input1 = [
                'rp'              => $request["rp"],
                'prescripcion'    => $request["prescripcion"],
                'id_hc'           => $request["id_hc"],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            hc_receta::where('id', $id)
                ->update($input1);
        }
        return redirect()->intended('historiaclinica/receta/' . $request["mover"]);
    }

    public function imprime($id, $tipo)
    {
        $id_empresa  = Session::get('id_empresa');
        $receta   = hc_receta::find($id);
        $historia = historiaclinica::find($receta->id_hc);
        $paciente = paciente::find($historia->id_paciente);
        $pacienteAlergia = Paciente_Alergia::where('id_paciente',$paciente->id)->get();
        $logo_emp = Empresa::find($id_empresa);
        $principioActivo = "";
        if(Count($pacienteAlergia)>0){
            foreach ($pacienteAlergia as $value) {
                if($principioActivo==""){
                    $principioActivo = $value->principio_activo->nombre;
                }
                else{
                    $principioActivo = $principioActivo.", ".$value->principio_activo->nombre;
                }
            }
        }
        else{
            $principioActivo = "NO TIENE";
        }
        //return $historia;
        $edad = Carbon::parse($paciente->fecha_nacimiento)->age; // 1990-10-25
        //return view('hc_admision/receta/menbretada', ['paciente' => $paciente,'edad' => $edad, 'historia' => $historia,  'receta' => $receta,]);
        $detalles  = hc_receta_detalle::where('id_hc_receta', $id)->get();
        $medicinas = Medicina::all();
        $cie10     = hc_cie10::where('hcid', $receta->id_hc)->get();
        $firma     = null;
        if (!is_null($receta) && is_null($firma)) {
            $id_doctor = $receta->id_doctor_examinador;
            $firma     = Firma_Usuario::where('id_usuario', $id_doctor)->first();
        }
        if (!is_null($historia->hc_procedimientos) && is_null($firma)) {
            $id_doctor = $historia->hc_procedimientos->id_doctor_examinador;
            $firma     = Firma_Usuario::where('id_usuario', $id_doctor)->first();
        }
        if (is_null($firma)) {
            $firma = Firma_Usuario::where('id_usuario', $historia->id_doctor1)->first();
        }

        if ($tipo == 2) {
            if ($id == '29716') {
                //dd($firma);
            }

            $view = \View::make('hc_admision.receta.menbretada', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'id_empresa', 'principioActivo', 'logo_emp'))->render();
        }
        if ($tipo == 1) {
            $view = \View::make('hc_admision.receta.sinmenbrete', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'firma', 'medicinas', 'cie10', 'id_empresa'))->render();
        }
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('A4', 'landscape');

        //return $pdf->download($historia->id_paciente.'_Receta_'.$id.'.pdf');
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        //$pdf->setPaper('A4', 'landscape');

        return $pdf->stream($historia->id_paciente . '_Receta_' . $id . '.pdf');
    }

    public function receta($id_hc)
    {
        $historia = historiaclinica::find($id_hc);
        $receta   = hc_receta::where('id_hc', '=', $id_hc)->first();
        $paciente = paciente::find($historia->id_paciente);

        if ($receta == "") {
            $idusuario  = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            date_default_timezone_set('America/Guayaquil');
            $input_hc_receta = [
                'id_hc'           => $id_hc,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];
            hc_receta::insert($input_hc_receta);
            $receta = hc_receta::where('id_hc', '=', $id_hc)->first();
        }
        //return $historia;

        $alergiasxpac = Paciente_Alergia::where('id_paciente', $paciente->id)->get();
        return view('hc_admision/receta/receta', ['paciente' => $paciente, 'historia' => $historia, 'receta' => $receta, 'alergiasxpac' => $alergiasxpac]);
    }

    public function guardarpaciente(Request $request)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $input1 = [
            'alergias'        => $request["alergias"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        paciente::where('id', $request["id_paciente"])
            ->update($input1);
        return "exito";
    }

    public function guardar2(Request $request)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $input1 = [
            'prescripcion'    => $request["prescripcion"],
            'rp'              => $request["rp"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        hc_receta::where('id', $request["id_receta"])->update($input1);
        return "exito";
    }

    public function buscar_nombre(Request $request)
    {

        $nombre = $request['term'];
        $seguro = $request['seguro'];
        $seteo  = '%' . $nombre . '%';
        $data   = null;

        if ($seguro == '2' || $seguro == '3' || $seguro == '5' || $seguro == '7') {
            $query1 = Medicina::where('nombre', 'like', '%' . $seteo . '%')
                ->orwhere('nombre_generico', 'like', '%' . $seteo . '%')
                ->where('estado', '1')
                ->orderBy('nombre')->get();
        } else {
            $query1 = Medicina::where('nombre', 'like', '%' . $seteo . '%')
                ->orwhere('nombre_generico', 'like', '%' . $seteo . '%')
                ->where('estado', '1')
                ->orderBy('nombre')->get();

        }

        $nombre = $query1;

        /*foreach ($nombre_generico as $value) {
        $data[]=array('value'=>$value->nombre_generico, 'id'=>$value->id);
        }*/
        foreach ($nombre as $value) {

            if (!is_null($value->nombre_generico)) {
                $data[] = array('value' => $value->nombre . ' (' . $value->nombre_generico . ')', 'id' => $value->id);
            } else {
                $data[] = array('value' => $value->nombre . ' ' . $value->presentacion, 'id' => $value->id);
            }
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function buscar_nombre2(Request $request)
    {

        //dd($request['nombre_generico']);
        $nombre_medicina = $request['nombre_generico'];

        $data = null;

        $porciones = explode(" (", $nombre_medicina);
        //return $porciones;
        $nombre_medicina = $porciones[0];

        //return $nombre_medicina;

        if ($nombre_medicina == null) {
            return '0';
        }
        // return $nombre_medicina;

        $nombre = Medicina::WhereRaw("CONCAT(nombre, ' ', presentacion) LIKE '" . $nombre_medicina . "'")->where('estado', 1)->orderBy('nombre', 'asc')->get();
        if ($nombre == '[]') {
            $nombre = Medicina::where('nombre', 'like', $nombre_medicina)->where('estado', 1)->orderBy('nombre', 'asc')->get();
        }

        $nombre_generico = Medicina::WhereRaw("CONCAT(nombre_generico, ' ', presentacion) LIKE '" . $nombre_medicina . "'")->where('estado', 1)->orderBy('nombre_generico', 'asc')->get();
        if ($nombre_generico == '[]') {
            $nombre_generico = Medicina::where('nombre_generico', 'like', $nombre_medicina)->where('estado', 1)->orderBy('nombre', 'asc')->get();
        }

        foreach ($nombre as $value) {
            $genericos = $nombre->where('id', $value->id)->first()->genericos;
            $generico  = null;
            foreach ($genericos as $gen2) {
                if ($generico == null) {
                    $generico = $gen2->generico->nombre;
                } else {
                    $generico = $generico . ', ' . $gen2->generico->nombre;
                }

            }
            $data[] = array('value' => $value->nombre . ' ' . $value->presentacion, 'dosis' => $value->dosis, 'nombre_generico' => $value->nombre_generico, 'cantidad' => $value->cantidad, 'id' => $value->id, 'dieta' => $value->dieta, 'genericos' => $generico);
        }
        foreach ($nombre_generico as $value) {

            $genericos = medicina::where('id', $value->id)->first()->genericos;
            //dd($genericos);
            $generico = null;
            foreach ($genericos as $gen2) {
                if ($generico == null) {
                    $generico = $gen2->generico->nombre;
                } else {
                    $generico = $generico . ', ' . $gen2->generico->nombre;
                }

            }
            $data[] = array('value' => $value->nombre, 'dosis' => $value->dosis, 'nombre_generico' => $value->nombre_generico, 'cantidad' => $value->cantidad, 'id' => $value->id, 'dieta' => $value->dieta, 'genericos' => $generico);
        }
        if (count($data) > 0) {
            return $data[0];
        } else {
            return '0';
        }
    }

    public function update_receta_2(Request $request)
    {

        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $input1 = [
            'prescripcion'    => $request["prescripcion"],
            'rp'              => $request["rp"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        hc_receta::where('id', $request["id_receta"])->update($input1);
        return $request;
    }

    public function crear_detalle($receta, $med, $pac)
    {

        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');

        $paciente_alergias = Paciente_Alergia::where('id_paciente', $pac)->get();

        $detalle = hc_receta_detalle::where('id_hc_receta', $receta)->where('id_medicina', $med)->first();
        //dd($detalle);
        $mensaje = null;

        $flag = false;

        if (is_null($detalle)) {

            $medicina = Medicina::find($med);
            //return $paciente_alergias;

            foreach ($paciente_alergias as $p) {
                //return $p;
                foreach ($medicina->genericos as $gen) {
                    //return $gen->id_principio_activo;
                    if ($p->id_principio_activo == $gen->id_principio_activo) {

                        $flag = true;

                    }

                }

            }

            if (!$flag) {

                return 1;

            } else {

                $mensaje = "PACIENTE ES ALERGICO A LA MEDICINA SOLICITADA";

            }

            //return "creo";
        } else {
            $mensaje = "MEDICINA YA INGRESADA";
        }

        $detalles  = hc_receta_detalle::where('id_hc_receta', $receta)->get();
        $medicinas = Medicina::all();

        return view('hc_admision/receta/detalle', ['detalles' => $detalles, 'medicinas' => $medicinas, 'receta' => $receta, 'mensaje' => $mensaje]);
    }

    public function index_detalle($receta)
    {

        $detalles  = hc_receta_detalle::where('id_hc_receta', $receta)->get();
        $medicinas = Medicina::all();
        $mensaje   = null;

        return view('hc_admision/receta/detalle', ['detalles' => $detalles, 'medicinas' => $medicinas, 'receta' => $receta, 'mensaje' => $mensaje]);

    }

    public function editar_detalle(Request $request, $receta, $id)
    {

        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');

        $detalle = hc_receta_detalle::find($id);
        if (!is_null($detalle)) {

            $input = [
                'cantidad' => $request->cantidad,
                'dosis'              => $request->dosis,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariomod'      => $idusuario,
            ];
            $detalle->update($input);
            return "ok";

        }
        return "no";

    }

    public function eliminar_detalle($receta, $id)
    {

        $detalle = hc_receta_detalle::find($id);

        if (!is_null($detalle)) {

            $detalle->delete();

        }

        $detalles  = hc_receta_detalle::where('id_hc_receta', $receta)->get();
        $medicinas = Medicina::where('estado', 1)->get();
        $error     = null;
        $mensaje   = "MEDICINA ELIMINADA";

        return view('hc_admision/receta/detalle', ['detalles' => $detalles, 'medicinas' => $medicinas, 'receta' => $receta, 'error' => $error, 'mensaje' => $mensaje]);

    }

    //Nuevas Funcionalidad Receta
    //Funcion Historial de Recetas
    public function historial_recetas($id_paciente)
    {

        $paciente = Paciente::find($id_paciente);
        //dd($paciente);

        $hist_recetas = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->where('h.id_paciente', $id_paciente)
        //->whereNotNull('r.prescripcion')
        //->whereNotNull('r.rp')
            ->join('users as d', 'd.id', 'h.id_doctor1')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            //->orderBy('r.created_at', 'desc')
            ->orderBy('h.fecha_atencion', 'desc')
            ->select('r.*', 'a.fechaini', 's.nombre', 'h.id_doctor1', 'h.fecha_atencion', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'd.id as id_doctor')
            ->get();

        //dd($hist_recetas);

        return view('hc_admision/receta/historial_recetas', ['hist_recetas' => $hist_recetas, 'paciente' => $paciente]);

    }

    //FUNCION EDITAR
    //PERMITE EDITAR EL RP Y LA PRESCRIPCION Y AGREGAR UNA NUEVA NEDICINA
    public function editar_receta($id_receta, $id_paciente)
    {

        $hc_receta = hc_receta::find($id_receta);
        $xhistoria = Historiaclinica::find($hc_receta->id_hc);
        $agenda = Agenda::find($xhistoria->id_agenda);

        $alergiasxpac = Paciente_Alergia::where('id_paciente', $id_paciente)->get();
        $paciente     = Paciente::find($id_paciente);

        $doctores = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '1')->get();

        return view('hc_admision/receta/editar_receta_historial', ['hc_receta' => $hc_receta, 'alergiasxpac' => $alergiasxpac, 'id_paciente' => $id_paciente, 'paciente' => $paciente, 'doctores' => $doctores, 'xhistoria' => $xhistoria, 'agenda' => $agenda]);
    }

    //PERMITE VOLVER A LA VISTA HISTORIAL DE RECETAS LUEGO DE HABER DADO A
    //LA OPCION  GUARDAR
    public function retorna_vista_historial_recetas(Request $request)
    {

        //$idusuario = $request["id_doct"];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        //$hc_receta =hc_receta::find($request["id_receta"]);

        /*$hist_recetas = DB::table('hc_receta as r')
        ->join('historiaclinica as h','r.id_hc','h.hcid')
        ->where('h.id_paciente',$request['id_paciente'])
        ->join('agenda as a','a.id','h.id_agenda')
        ->orderBy('a.fechaini','desc')
        ->select('r.*','a.fechaini')
        ->orderBy('r.id','desc')->get()->first();

        if (!is_null($hist_recetas)) {
        $proc_id = null;
        $id_procedimiento_new = hc_procedimientos::where('id_hc',$hist_recetas->id_hc)->first();
        if(!is_null($id_procedimiento_new)){
        $proc_id = $id_procedimiento_new->id;
        }

        $hist_recetas_new = $hist_recetas;

        $receta_new = [
        'anterior' => 'RECETA -> Rp:'.$hist_recetas_new->rp. ' Prescripcion:' .$hist_recetas_new->prescripcion,
        'nuevo' => 'RECETA -> Rp:'.$request["rp"]. ' Prescripcion:' .$request["prescripcion"],
        'hc_id' => $hist_recetas_new->id_hc,
        'id_paciente' => $request['id_paciente'],
        'id_procedimiento' => $proc_id,
        //'id_receta' => $hist_recetas_new->id ,
        'id_usuariomod' => $idusuario,
        'id_usuariocrea' => $idusuario,
        'ip_modificacion' => $ip_cliente,
        'ip_creacion' => $ip_cliente,
        ];
        Hc_Log::create($receta_new);
        }*/

        $paciente     = Paciente::find($request['id_paciente']);
        $hist_recetas = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->where('r.id', $request['id_receta'])
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->orderBy('a.fechaini', 'desc')
            ->select('r.*', 'a.fechaini')
            ->orderBy('r.id', 'desc')
            ->get()->first();

        return view('hc_admision/receta/unico_historial_receta', ['hist_recetas' => $hist_recetas]);
    }

    //FUNCION AGREGAR NUEVA RECETA
    //PERMITE CREAR UNA NUEVA RECETA Y AGREGAR UNA NUEVA MEDICINA
    //EN LOS CAMPOS RP Y PRESCRIPCION
    public function agregar_nueva_receta($id_paciente)
    {
        $idusuario  = Auth::user()->id;
        $paciente   = Paciente::find($id_paciente);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_doctor  = '9666666666';
        $empresa = Empresa::where('prioridad', 1)->first();

        $especialidad = DB::table('user_espe as u_es')->where('u_es.usuid', $id_doctor)->get()->first();
        //dd($especialidad);
        if (!is_null($especialidad)) {
            $espid = $especialidad->espid;
        } else {
            $espid = '4';
        }

        $receta_new = [

            'anterior'        => 'DATOS PRINCIPALES -> El Dr. creo nueva receta',
            'nuevo'           => 'DATOS PRINCIPALES -> El Dr. creo nueva receta',
            'id_paciente'     => $paciente->id,
            'id_usuariomod'   => $idusuario,
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,

        ];

        Hc_Log::create($receta_new);

        $input_agenda = [
            'fechaini'        => Date('Y-m-d H:i:s'),
            'fechafin'        => Date('Y-m-d H:i:s'),
            'id_paciente'     => $id_paciente,
            'id_doctor1'      => $id_doctor,
            'proc_consul'     => '4',
            'estado_cita'     => '4',
            'id_empresa'      => $empresa->id,
            'espid'           => $espid,
            'observaciones'   => 'RECETA CREADA POR EL DOCTOR',
            'id_seguro'       => $paciente->id_seguro,
            'estado'          => '4',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];

        $id_agenda = agenda::insertGetId($input_agenda);

        $input_historia = [

            'parentesco'      => $paciente->parentesco,
            'id_usuario'      => $paciente->id_usuario,
            'id_agenda'       => $id_agenda,
            'id_paciente'     => $id_paciente,
            'id_seguro'       => $paciente->id_seguro,
            'id_doctor1'      => $id_doctor,
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'fecha_atencion'  => Date('Y-m-d H:i:s'),

        ];

        $id_historia = Historiaclinica::insertGetId($input_historia);
        //$idusuario   = $id_doctor;

        $input_hc_receta = [
            'id_hc'           => $id_historia,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,

        ];

        hc_receta::insert($input_hc_receta);

        /*$alergiasxpac = Paciente_Alergia::where('id_paciente',$id_paciente)->get();

        $hc_receta = DB::table('hc_receta as r')
        ->join('historiaclinica as h','r.id_hc','h.hcid')
        ->join('agenda as a','a.id','h.id_agenda')
        ->join('seguros as s','s.id','h.id_seguro')
        ->where('h.id_paciente',$id_paciente)
        ->select('r.*','a.fechaini', 's.id as id_seguro', 's.nombre as nombre_seguro')
        ->get()->last();*/

        //$doctores = User::where('id_tipo_usuario',3)->where('estado',1)->get();

        $hist_recetas = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->where('h.id_paciente', $id_paciente)
            ->join('users as d', 'd.id', 'h.id_doctor1')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->orderBy('r.created_at', 'desc')
            ->select('r.*', 'a.fechaini', 's.nombre', 'h.id_doctor1', 'h.fecha_atencion', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'd.id as id_doctor')
            ->get();

        /*return view ('hc_admision/receta/nueva_receta', ['alergiasxpac' => $alergiasxpac,'paciente' => $paciente,'id_paciente' => $id_paciente,'hc_receta' => $hc_receta]);*/

        return view('hc_admision/receta/historial_recetas', ['hist_recetas' => $hist_recetas, 'paciente' => $paciente]);

    }

    //FUNCION UPDATE_RECETA_2
    //PERMITE ACTUALIZAR LA RECETA EDITADA

    public function updatehc4_receta_2(Request $request)
    {

        //$idusuario = $request["id_doct"];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        //$hc_receta =hc_receta::find($request["id_receta"]);

        /*$hist_recetas = DB::table('hc_receta as r')
        ->join('historiaclinica as h','r.id_hc','h.hcid')
        ->where('h.id_paciente',$request['id_paciente'])
        ->join('agenda as a','a.id','h.id_agenda')
        ->orderBy('a.fechaini','desc')
        ->select('r.*','a.fechaini')
        ->orderBy('r.id','desc')->get()->first();

        if (!is_null($hist_recetas)) {
        $proc_id = null;
        $id_procedimiento_new = hc_procedimientos::where('id_hc',$hist_recetas->id_hc)->first();
        if(!is_null($id_procedimiento_new)){
        $proc_id = $id_procedimiento_new->id;
        }

        $hist_recetas_new = $hist_recetas;

        $receta_new = [
        'anterior' => 'RECETA -> Rp:'.$hist_recetas_new->rp. ' Prescripcion:' .$hist_recetas_new->prescripcion,
        'nuevo' => 'RECETA -> Rp:'.$request["rp"]. ' Prescripcion:' .$request["prescripcion"],
        'hc_id' => $hist_recetas_new->id_hc,
        'id_paciente' => $request['id_paciente'],
        'id_procedimiento' => $proc_id,
        //'id_receta' => $hist_recetas_new->id ,
        'id_usuariomod' => $idusuario,
        'id_usuariocrea' => $idusuario,
        'ip_modificacion' => $ip_cliente,
        'ip_creacion' => $ip_cliente,
        ];
        Hc_Log::create($receta_new);
        }*/

        if (!is_null($request["id_doct"])) {
            $idusuario = $request["id_doct"];
        } else {
            $idusuario = '9666666666';
        }

        $input1 = [
            'prescripcion'    => $request["prescripcion"],
            'rp'              => $request["rp"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        hc_receta::where('id', $request["id_receta"])->update($input1);
        return $request;
    }

    //FUNCION UPDATE_RECETA_2
    //PERMITE ACTUALIZAR LA FECHA DE ATENCION Y EL NOMBRE DEL DOCTOR DE LA RECETA
    public function update_fech_doct(Request $request)
    {

        $hc_receta = hc_receta::find($request["id_receta"]);

        if (!is_null($request["fech_aten"])) {

            $featen = [
                'fecha_atencion' => $request["fech_aten"],
            ];

            Historiaclinica::where('hcid', $hc_receta->id_hc)->update($featen);
        }

        if (!is_null($request["id_doct"])) {

            $idoct = [
                'id_doctor1' => $request["id_doct"],
            ];

            Historiaclinica::where('hcid', $hc_receta->id_hc)->update($idoct);
        }

        return $request;

    }

    private static function generarNumero($id_empresa)
    {
        //dd($id_empresa);
        $num = Nota_entrega::where('id_empresa', $id_empresa)->orderby('id', 'desc')->max('numero');
        if ($num == '')
            $num = '0000001';
        else {
            $num = $num + 1;
            $num = str_pad($num, 7, 0, STR_PAD_LEFT);
        }
        return $num;
    }
    public function nota_entrega($id)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $receta = hc_receta::findorfail($id);
        $hc = Historiaclinica::findorfail($receta->id_hc);
        $agenda = Agenda::findOrFail($hc->id_agenda);
        $empresa_prioridad = Empresa::where('prioridad', '1')->where('estado', 1)->first();
        $plantilla_control = Insumo_Plantilla_Control::where('nombre', 'FARMACIA')->first();
        $id_hc_procedimientos = hc_procedimientos::where('id_hc', $hc->hcid)->first()->id;


        $detalle_receta = DB::table('hc_receta_detalle as rd')
            ->join('medicina_tarifario as mt', 'mt.id_medicina', 'rd.id_medicina')
            ->join('ap_procedimiento as ap', 'ap.id', 'mt.id_ap_procedimiento')
            ->where('rd.id_hc_receta', $id)
            ->select('rd.*', 'ap.valor', 'ap.descripcion as nombre_medicina')->get();


        $empresa_agenda = $empresa_prioridad->id;
        if(!is_null($agenda->id_empresa)){
            $empresa_agenda = $agenda->id_empresa;
        }else{
            $fecha_receta = substr($receta->created_at, 0, 10);
            $agenda_ultima_empresa = Agenda::where('estado', '1')->where('id_paciente', $agenda->id_paciente)->where('proc_consul', '1')->whereBetween('fechaini', [$fecha_receta.' 00:00:00', $fecha_receta.' 23:59:59'])->first();
            //dd($agenda_ultima_empresa);
            if(!is_null($agenda_ultima_empresa)){
                if(!is_null($agenda_ultima_empresa->id_empresa)){
                    $empresa_agenda = $agenda_ultima_empresa->id_empresa;
                    $agenda->id_empresa = $empresa_agenda;
                    $agenda->save();
                }
            }
        }
        $num_nota_entrega = RecetaController::generarNumero($empresa_agenda);
        $nota_entrega_detalle = null;
        $nota_entrega = Nota_entrega::where('id_hc_receta', $id)->where('id_empresa', $empresa_agenda)->where('estado', '<>', 0)->first();
        if(!is_null($nota_entrega)){
            $nota_entrega_detalle = Nota_entrega_detalle::where('id_nota_entrega', $nota_entrega->id)->where('estado', '<>', 0)->get();
        }

        if (is_null($nota_entrega) && is_null($nota_entrega_detalle)) {
            if (!is_null($receta)) {
                $planilla_cab_nota_entrega = [
                    'fecha'                  => date('Y-m-d H:i:s'),
                    'id_planilla'            => $plantilla_control->id,
                    'id_agenda'              => $agenda->id,
                    'id_hc_procedimiento'    => $id_hc_procedimientos,
                    'observacion'            => 'Paciente: ' . $agenda->paciente->apellido1 . ' ' . $agenda->paciente->apellido2 . ' ' . $agenda->paciente->nombre1 . ' ' . $agenda->paciente->nombre2 . ' | ' . 'Nota_entrega: ' . $num_nota_entrega,
                    'aprobado'               => 0,
                    'nota_entrega'           => '1',
                    'id_usuariomod'          => $idusuario,
                    'id_usuariocrea'         => $idusuario,
                    'ip_modificacion'        => $ip_cliente,
                    'ip_creacion'            => $ip_cliente,
                ];
                $id_planilla_cabecera = Planilla_Cabecera::insertGetId($planilla_cab_nota_entrega);

                $cab_nota_entrega = [
                    'numero'               => $num_nota_entrega,
                    'id_planilla_cabecera' => $id_planilla_cabecera,
                    'id_hc'                => $receta->id_hc,
                    'id_hc_receta'         => $receta->id,
                    'id_empresa'           => $empresa_agenda,
                    'estado'               => -1,
                    'id_usuariomod'        => $idusuario,
                    'id_usuariocrea'       => $idusuario,
                    'ip_modificacion'      => $ip_cliente,
                    'ip_creacion'          => $ip_cliente,
                ];
                $id_nota_entrega = Nota_entrega::insertGetId($cab_nota_entrega);
                $log_nota_entrega = [
                    'id_hc_receta' => $receta->id,
                    'id_nota_entrega' => $id_nota_entrega,
                    'descripcion' => "Creacion de Nota de entrega",
                    'id_usuariomod' => $idusuario,
                    'id_usuariocrea' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion' => $ip_cliente,           
                    ];
                Log_nota_entrega::create($log_nota_entrega);
            }
            if(!is_null($detalle_receta)){
                foreach ($detalle_receta as $det) {
                    $total = $det->cantidad * $det->valor;
                    $det_nota_entrega = [
                        'id_nota_entrega'   => $id_nota_entrega,
                        'id_medicina'       => $det->id_medicina,
                        'nombre_medicina'   => $det->nombre_medicina,
                        'cantidad_receta'   => $det->cantidad,
                        'cantidad'          => $det->cantidad,
                        'precio'            => $det->valor,
                        'subtotal_0'        => $total,
                        'total'             => $total,
                        'id_usuariomod'     => $idusuario,
                        'id_usuariocrea'    => $idusuario,
                        'ip_modificacion'   => $ip_cliente,
                        'ip_creacion'       => $ip_cliente,
                    ];
                    Nota_entrega_detalle::create($det_nota_entrega);
                }
                $nota_entrega = Nota_entrega::where('id_hc_receta', $id)->where('id_empresa', $empresa_agenda)->where('estado', '<>', 0)->first();
                //dd($nota_entrega);
                $total = $nota_entrega->detalles->sum('total');
                $subtotal_0 = $nota_entrega->detalles->sum('subtotal_0');
                $subtotal_12 = $nota_entrega->detalles->sum('subtotal_12');
                $arr_cabecera = [
                    'total'                 => $total,
                    'subtotal_0'            => $subtotal_0,
                    'subtotal_12'           => $subtotal_12,
                    'id_usuariomod'         => $idusuario,
                    'ip_modificacion'       => $ip_cliente
                ];
                $nota_entrega->update($arr_cabecera);
            }
        } else {
            $nota_entrega = Nota_entrega::where('id_hc_receta', $id)->where('id_empresa', $empresa_agenda)->where('estado', '<>', 0)->first();
            if ($nota_entrega->estado_generado == '0' && $nota_entrega->estado == '-1') {
                if (!is_null($nota_entrega_detalle)) {
                    if (!is_null($detalle_receta)) {
                        $input = Nota_entrega_detalle::where('id_nota_entrega', $nota_entrega->id)->where('estado', '<>', 0)->get();

                        if (!is_null($input)) {
                            foreach ($input as $val) {
                                $val->delete();
                            }
                        }
                        foreach ($detalle_receta as $det) {
                            $total = $det->cantidad * $det->valor;
                            $act_det_nota_entrega = [
                                'id_nota_entrega'   => $nota_entrega->id,
                                'id_medicina'       => $det->id_medicina,
                                'nombre_medicina'   => $det->nombre_medicina,
                                'cantidad_receta'   => $det->cantidad,
                                'cantidad'          => $det->cantidad,
                                'precio'            => $det->valor,
                                'subtotal_0'        => $total,
                                'total'             => $total,
                                'id_usuariocrea'    => $idusuario,
                                'id_usuariomod'     => $idusuario,
                                'ip_modificacion'   => $ip_cliente,
                            ];
                            Nota_entrega_detalle::create($act_det_nota_entrega);
                        }
                        $nota_entrega = Nota_entrega::where('id_hc_receta', $id)->where('id_empresa', $empresa_agenda)->where('estado', '<>', 0)->first();
                        $total = $nota_entrega->detalles->sum('total');
                        $subtotal_0 = $nota_entrega->detalles->sum('subtotal_0');
                        $subtotal_12 = $nota_entrega->detalles->sum('subtotal_12');
                        $arr_cabecera = [
                            'total'                 => $total,
                            'subtotal_0'            => $subtotal_0,
                            'subtotal_12'           => $subtotal_12,
                            'id_usuariomod'         => $idusuario,
                            'ip_modificacion'       => $ip_cliente
                        ];
                        $nota_entrega->update($arr_cabecera);

                        $log_nota_entrega = [
                            'id_hc_receta' => $receta->id,
                            'id_nota_entrega' => $nota_entrega->id,
                            'descripcion' => "Actualizacion de medicina de Nota de entrega",
                            'id_usuariomod' => $idusuario,
                            'id_usuariocrea' => $idusuario,
                            'ip_modificacion' => $ip_cliente,
                            'ip_creacion' => $ip_cliente,
                        ];
                        Log_nota_entrega::create($log_nota_entrega);
                    }
                }
            }
        }
        return redirect()->route('nota_entrega.editar', ['id' => $id]);

        // $nota_entrega = Nota_entrega::where('id_hc_receta', $id)->where('id_empresa', $empresa_agenda)->where('estado', 1)->first();
        // if(!is_null($nota_entrega)){
        //     $nota_entrega_detalle = Nota_entrega_detalle::where('id_nota_entrega', $nota_entrega->id)->where('estado', 1)->get();
        // }
        // $vistaurl = "hc_admision.receta.pdf_nota_entrega";
        // $view     = \View::make($vistaurl, compact('nota_entrega', 'nota_entrega_detalle', 'hc', 'agenda'))->render();
        // $pdf = \App::make('dompdf.wrapper');
        // $pdf->loadHTML($view);
        // $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        // $pdf->setPaper('A4', 'portraint');
        // return $pdf->stream('Nota de Entrega - ' . $nota_entrega->id . '.pdf');
    }

    public static function busqueda_notas_entrega($request)
    {
        $notas_entrega = Nota_entrega::join('historiaclinica as hc', 'hc.hcid', 'nota_entrega.id_hc')
            ->join('paciente as p', 'p.id', 'hc.id_paciente')
            ->where('nota_entrega.estado', '<>', 0)
            ->orderby('nota_entrega.id', 'desc')
            ->select('nota_entrega.id', 'nota_entrega.id_empresa', 'nota_entrega.id_hc', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'nota_entrega.id_hc_receta', 'nota_entrega.numero', 'nota_entrega.created_at', 'nota_entrega.estado');

        if (count($request->all()) > 0) {
            if (!is_null($request->fecha) && !is_null($request->fecha_hasta)) {
                $notas_entrega = $notas_entrega->whereBetween('nota_entrega.created_at', [$request->fecha . ' 00:00:00', $request->fecha_hasta . ' 23:59:00']);
            }
            if (!is_null($request->id)) {
                $notas_entrega = $notas_entrega->where('nota_entrega.id', $request->id);
            }
            if(!is_null($request->secuencia)){
                $notas_entrega = $notas_entrega->where('nota_entrega.numero', 'like', '%' . $request->secuencia . '%');
            }
            if(!is_null($request->id_empresa)){
                $notas_entrega = $notas_entrega->where('nota_entrega.id_empresa', $request->id_empresa);
            }
            if (!is_null($request->cedula)) {
                $notas_entrega = $notas_entrega->where('p.id',$request->cedula);
            }
            if (!is_null($request->paciente)) {
                $paciente = $request->paciente;
                $notas_entrega = $notas_entrega->where(function ($jq1) use ($paciente) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $paciente . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $paciente . '%'])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', ['%' . $paciente . '%'])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', ['%' . $paciente . '%']);
                });
            }
        }else{
            $notas_entrega = $notas_entrega->whereBetween('nota_entrega.created_at', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:00']);
        }
        //dd($notas_entrega->toSql());
        return $notas_entrega;
    }

    public function index_nota_entrega(Request $request) 
    {
        $paciente    = $request['paciente'];
        $id          = $request['id'];
        $cedula      = $request['cedula'];
        $secuencia   = $request['secuencia'];
        $id_empresa  = $request['id_empresa'];
        $fecha = $request['fecha'];
        if (is_null($fecha)) {
            $fecha = date('Y-m-d');
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $notas_entrega = $this->busqueda_notas_entrega($request)->paginate(20);

        return view('hc_admision.nota_de_entrega.index', ['id_empresa' => $id_empresa, 'notas_entrega' => $notas_entrega, 'paciente' => $paciente, 'id' => $id, 'cedula' => $cedula, 'secuencia' => $secuencia, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta,]);
    }

    public function sube_archivo_nota_entrega($id_receta, $id_nota)
    {
        return view('hc_admision/nota_de_entrega/sube_archivo_nota_entrega', ['id_receta' => $id_receta, 'id_nota' => $id_nota,]);
    }

    public function update_nota_entrega(Request $request)
    {
        $ip_cliente        = $_SERVER["REMOTE_ADDR"];
        $idusuario         = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $nota_entrega = Nota_entrega::find($request['id_nota']);
        //dd($nota_entrega);
        $extension       = $request['archivo']->getClientOriginalExtension();
        $nuevo_nombre    =  'nota_entrega_'.$nota_entrega->numero. "_" . date('YmdHis') . "." . $extension;
        $nuevo = Storage::disk('notas_entrega')->put($nuevo_nombre, \File::get($request['archivo']));
        $notas_escaneadas = Nota_Entrega_Escaneados::where('id_nota_entrega', $request['id_nota'])->where('estado', '1')->first();
        if (is_null($notas_escaneadas)) {
            Nota_Entrega_Escaneados::create([
                'id_nota_entrega'  => $request['id_nota'],
                'id_hc_receta'     => $request['id_receta'],
                'archivo'          => $nuevo_nombre,
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
            ]);
        } else {
            $notas_escaneadas->update([
                'archivo'          => $nuevo_nombre,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ]);
        }

        $arr_log_nota = [
            'id_hc_receta'      => $request['id_receta'],
            'id_nota_entrega'   => $request['id_nota'],
            'descripcion'       => 'Subio documento',
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ];
        Log_nota_entrega::create($arr_log_nota);
        return "ok";
    }
    public function visualizar_nota_entrega_pdf(Request $request)
    {
       $nombreArchivo = Nota_Entrega_Escaneados::where('id_nota_entrega', $request['id'])->first();
       $path1 = storage_path() . "/app/notas_entrega/" . $nombreArchivo->archivo;
       return response()->file($path1);
    }

    public function index_generar_nota_entrega(Request $request)
    {
        //return $request->all();
        ini_set('max_execution_time', 300);
        $rolUsuario = Auth::user()->id_tipo_usuario;  
        if($this->rol()){
            return response()->view('errors.404');
        }
        $nomb_proc = null;
        $id_seguro = $request->id_seguro;
        $espid = $request->espid;
        $proc_consul = $request->proc_consul;
        $id_especialidad = $request->id_especialidad;
        $id_doctor1 = $request->id_doctor1;
        $nombres = $request['nombres'];
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];

        $doctores = User::where('id_tipo_usuario', '3')->Orderby('apellido1', 'asc')->get();

        $agendas = [];
        $consultas = []; 
        if($fecha==null && $nombres==null){
            $fecha = Date('Y-m-d');
            if($rolUsuario == '11'){
                $fecha = Date('Y-m-d', strtotime("-2 months"));
            }
        } 
        if($proc_consul==null && $rolUsuario == '11'){
            $proc_consul = '1';
        }
        if($fecha_hasta==null){
            $fecha_hasta = Date('Y-m-d');
        } 
        if($proc_consul==null){
            $proc_consul = '2';
        }
        $nombres_sql='';
        $agendas_pac = [];
        $pacientes1 = DB::table('agenda as a')->where('a.estado',1)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p','p.id','a.id_paciente')
            ->join('users as d','d.id','a.id_doctor1')
            ->leftjoin('empresa as em','em.id','a.id_empresa')
            ->join('seguros as se','se.id','a.id_seguro')
            ->leftjoin('historiaclinica as h','h.id_agenda','a.id')
            ->leftjoin('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
            ->whereNull('h.hcid')
            ->where('a.id_doctor1','<>','4444444444')
            ->groupBy('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.id')
            ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'a.id_doctor1 as doctor', 'a.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');
        if($fecha != null){
            $pacientes1 = $pacientes1->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59']);
        }    
        $pacientes2 = DB::table('agenda as a')->where('a.estado',1)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p','p.id','a.id_paciente')
            ->join('users as d','d.id','a.id_doctor1')
            ->leftjoin('empresa as em','em.id','a.id_empresa')
            ->join('seguros as se','se.id','a.id_seguro')
            ->join('historiaclinica as h','h.id_agenda','a.id')
            ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
            ->where('a.proc_consul', '0')
            ->where('a.id_doctor1','<>','4444444444')
            ->whereNull('hc_pro.id_doctor_examinador')
            ->groupBy('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.id')
            ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'h.id_doctor1 as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');
        if($fecha != null){
                $pacientes2 = $pacientes2->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59']);
        }           
        $pacientes2_0 = DB::table('agenda as a')->where('a.estado',1)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p','p.id','a.id_paciente')
            ->join('users as d','d.id','a.id_doctor1')
            ->leftjoin('empresa as em','em.id','a.id_empresa')
            ->join('seguros as se','se.id','a.id_seguro')
            ->join('historiaclinica as h','h.id_agenda','a.id')
            ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
            ->where('a.proc_consul', '0')
            ->where('a.id_doctor1','<>','4444444444')
            ->whereNotNull('hc_pro.id_doctor_examinador')
            ->groupBy('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.id')
            ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'hc_pro.id_doctor_examinador as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');            
        if($fecha != null){
                $pacientes2_0 = $pacientes2_0->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59']);
        }    
        $pacientes3 = DB::table('agenda as a')->where('a.estado',1)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p','p.id','a.id_paciente')
            ->join('users as d','d.id','a.id_doctor1')
            ->leftjoin('empresa as em','em.id','a.id_empresa')
            ->join('seguros as se','se.id','a.id_seguro')
            ->join('historiaclinica as h','h.id_agenda','a.id')
            ->where('a.proc_consul', '1')
            ->where('a.id_doctor1','<>','4444444444')
            ->groupBy('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.id')
            ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'h.id_doctor1 as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');
        if($fecha != null){
                if(Auth::user()->id_tipo_usuario == '1' ){                  
                    //dd($pacientes3->where('p.id','0955695630')->get());
                } 
                $pacientes3 = $pacientes3->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59']);
        }            
        $pacientes4 = DB::table('agenda as a')->where('a.estado',4)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p','p.id','a.id_paciente')
            ->join('users as d','d.id','a.id_doctor1')
            ->leftjoin('empresa as em','em.id','a.id_empresa')
            ->join('seguros as se','se.id','a.id_seguro')
            ->join('historiaclinica as h','h.id_agenda','a.id')
            ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
            ->where('a.proc_consul', '4')
            ->where('a.id_doctor1','<>','4444444444')
            ->groupBy('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.id')
            ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'hc_pro.id_doctor_examinador as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');
        if($fecha != null){
                $pacientes4 = $pacientes4->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59']);
        }        
        if($proc_consul != 2){
            if($proc_consul == 3){
                $pacientes1 = $pacientes1->where('a.omni', 'OM');
                $pacientes2 = $pacientes2->where('a.omni', 'OM');
                $pacientes2_0 = $pacientes2_0->where('a.omni', 'OM');
                $pacientes3 = $pacientes3->where('a.omni', 'OM');
                $pacientes4 = $pacientes4->where('a.omni', 'OM');
            }elseif ($proc_consul == 4) {
                $pacientes1 = $pacientes1->where('a.omni', 'SI');
                $pacientes2 = $pacientes2->where('a.omni', 'SI');
                $pacientes2_0 = $pacientes2_0->where('a.omni', 'SI');
                $pacientes3 = $pacientes3->where('a.omni', 'SI');
                $pacientes4 = $pacientes4->where('a.omni', 'SI');
            } else {
                $pacientes1 = $pacientes1->where('a.proc_consul', $proc_consul);
                $pacientes2 = $pacientes2->where('a.proc_consul', $proc_consul);
                $pacientes2_0 = $pacientes2_0->where('a.proc_consul', $proc_consul);
                $pacientes3 = $pacientes3->where('a.proc_consul', $proc_consul);
                $pacientes4 = $pacientes4->where('a.proc_consul', $proc_consul);
            }
        }
        if(!is_null($id_doctor1)){
            $pacientes1 = $pacientes1->where('a.id_doctor1', $id_doctor1);
            $pacientes2 = $pacientes2->where('h.id_doctor1', $id_doctor1);
            $pacientes2_0 = $pacientes2_0->where('hc_pro.id_doctor_examinador', $id_doctor1);
            $pacientes3 = $pacientes3->where('h.id_doctor1', $id_doctor1);
            $pacientes4 = $pacientes4->where('hc_pro.id_doctor_examinador', $id_doctor1);
        }
        if(!is_null($id_seguro)){
            $pacientes1 = $pacientes1->where('a.id_seguro', $id_seguro);
            $pacientes2 = $pacientes2->where('h.id_seguro', $id_seguro);
            $pacientes2_0 = $pacientes2_0->where('h.id_seguro', $id_seguro);
            $pacientes3 = $pacientes3->where('h.id_seguro', $id_seguro);
            $pacientes4 = $pacientes4->where('h.id_seguro', $id_seguro);
        }
        if(!is_null($espid)){
            $pacientes1 = $pacientes1->where('a.espid', $espid);
            $pacientes2 = $pacientes2->where('a.espid', $espid);
            $pacientes2_0 = $pacientes2_0->where('a.espid', $espid);
            $pacientes3 = $pacientes3->where('a.espid', $espid);
            $pacientes4 = $pacientes4->where('a.espid', $espid);
        }

        if ($nombres != null) {
            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);
            foreach($nombres2 as $n){
                $nombres_sql = $nombres_sql.'%'.$n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad > '1') {
                $pacientes1 = $pacientes1->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
                $pacientes2 = $pacientes2->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
                $pacientes2_0 = $pacientes2_0->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
                $pacientes3 = $pacientes3->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
                $pacientes4 = $pacientes4->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {
                $pacientes1 = $pacientes1->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
                $pacientes2 = $pacientes2->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
                $pacientes2_0 = $pacientes2_0->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
                $pacientes3 = $pacientes3->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
                $pacientes4 = $pacientes4->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }

        $pacientes1 = $pacientes1->union($pacientes2)->union($pacientes2_0)->union($pacientes3)->union($pacientes4);
        $pacientes1 = $pacientes1->limit('300')->get();
        $pacientes = $pacientes1;
        $agendas_proc=null;
        foreach($pacientes as $pac){
            if($pac->proc_consul=='1'){
                $pentax = Pentax::where('id_agenda',$pac->id_agenda)->first();
                if(!is_null($pentax)){
                    $txt_px='';
                    foreach ($pentax->procedimientos as $p) {
                        if($txt_px==''){
                            $txt_px=$p->procedimiento->nombre;
                        }else{
                            $txt_px = $txt_px.'+'.$p->procedimiento->nombre;
                        }
                    }
                    $agendas_proc[$pac->id_agenda] = [$txt_px];        
                }
            }
        }
        Cookie::queue('ruta', 'historia', '1000');

        return view('hc_admision/nota_de_entrega/index_generar_nota_entrega', ['nombres' => $nombres, 'agendas_pac' => $pacientes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'agendas_proc' => $agendas_proc, 'proc_consul' => $proc_consul, 'doctores' => $doctores, 'id_doctor1' => $id_doctor1, 'id_seguro' => $id_seguro]);
    }
    public function detalle_receta_nota_entrega($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->leftjoin('seguros as hs', 'hs.id', 'h.id_seguro')
            ->leftjoin('sala', 'agenda.id_sala', 'sala.id')
            ->leftjoin('hospital', 'sala.id_hospital', 'hospital.id')
            ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
            ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
            ->select('agenda.*')
            ->where('agenda.id', '=', $id)
            ->first();

        $hist_recetas = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->where('h.id_paciente', $agenda->id_paciente)
            ->join('users as d', 'd.id', 'h.id_doctor1')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            //->orderBy('r.created_at', 'desc')
            ->orderBy('a.fechaini', 'desc')
            ->select('r.*', 'a.fechaini', 's.nombre', 'h.id_doctor1', 'h.fecha_atencion', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'd.id as id_doctor')
            ->get();

        return view('hc_admision/nota_de_entrega/historial_recetas', ['agenda' => $agenda, 'hist_recetas' => $hist_recetas]);
    }



    public function excel_nota_entrega(Request $request)
    {
        //dd($request->all());
        $notas_entrega = $this->busqueda_notas_entrega($request)->get();
        //dd($notas_entrega);
        $detalles_nota = [];
        $i= 0;
        foreach ($notas_entrega as $nota) {
            // dd($nota);
            
            $detalles = Nota_entrega_detalle::where('id_nota_entrega', $nota->id)->get();

            foreach ($detalles as $detalle) {
                // dd($detalles);
                $detalles_nota[$i]=$detalle;
                $i++;
            }
            
            
            
        }
        // dd($i,$detalles_nota);
        // dd($detalles_nota,$notas_entrega);

        $titulos = array("FECHA", "CEDULA", "PACIENTE", "NUMERO", "EMPRESA", "NOMBRE MEDICINA", "CANTIDAD", "PRECIO", "TOTAL");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");


        Excel::create('Nota_Entrega', function ($excel) use ($titulos, $posicion, $notas_entrega,$detalles_nota) {
            $excel->sheet('Nota_Entrega', function ($sheet) use ($titulos, $posicion, $notas_entrega,$detalles_nota) {
                
                $comienzo = 1;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBackground('#92CFEF');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }

                $comienzo++;

                $total = 0;
                foreach ($detalles_nota as $detalle) {

                    //dd($detalle);

                    //$detalles = Nota_entrega_detalle::where('estado', 1)->where('id_nota_entrega', $detalle->id)->get();
                    $nota = Nota_entrega::find($detalle->id_nota_entrega);

                    // dd($nota);
                    // if(count($detalles)>0){
                    //     dd($detalles);
                    // }
                    $datos_excel = [];
                    
                    $fecha_formateada = date('Y-m-d', strtotime($nota->created_at));
                    $nombre_paciente = $nota->historia->paciente->apellido1 . " " . $nota->historia->paciente->apellido2 . " " . $nota->historia->paciente->nombre1 . " " . $nota->historia->paciente->nombre2;
                    $total = $detalle->cantidad * $detalle->precio;
                  
                    $datos_excel = [$fecha_formateada,$nota->historia->paciente->id,$nombre_paciente,$nota->numero,$nota->empresa->nombrecomercial,$detalle->nombre_medicina,$detalle->cantidad,$detalle->precio,$total];
                    
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
