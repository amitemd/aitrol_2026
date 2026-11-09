<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
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
use Sis_medico\Empresa;

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
        if (in_array($rolUsuario, array(1, 3, 6, 11, 7, 20)) == false) {
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
        $medicinas = Medicina::where('estado', 1)->get();
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
        if(is_null($firma)){
            $firma     = Firma_Usuario::where('id_usuario', $historia->id_doctor1)->first();    

        }

        if ($tipo == 2) {
            // V3 y HC4 comparten el mismo membrete para que las recetas
            // descargadas tengan encabezado, fondo y pie idénticos.
            $prescriptionData = compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'id_empresa', 'principioActivo', 'logo_emp');
            $prescriptionData['_pdf_branding_company_id'] = $id_empresa;
        }
        if ($tipo == 1) {
            $view = \View::make('hc_admision.receta.sinmenbrete', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'firma', 'medicinas', 'cie10', 'id_empresa'))->render();
        }
        $pdf = \App::make('dompdf.wrapper');
        if ($tipo == 2) {
            $pdf->loadView('hc_admision.receta.menbretadaor', $prescriptionData, array(), 'UTF-8');
        } else {
            $pdf->loadHTML($view);
        }
        $pdf->setPaper('a4', 'landscape');

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
        $medicinas = Medicina::where('estado', 1)->get();

        return view('hc_admision/receta/detalle', ['detalles' => $detalles, 'medicinas' => $medicinas, 'receta' => $receta, 'mensaje' => $mensaje]);
    }

    public function index_detalle($receta)
    {

        $detalles  = hc_receta_detalle::where('id_hc_receta', $receta)->get();
        $medicinas = Medicina::where('estado', 1)->get();
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

            $input = ['cantidad' => $request->cantidad,
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

        $companyId = session('id_empresa');
        $paciente = Paciente::find($id_paciente);
        if (!$paciente || !Agenda::where('id_paciente', $id_paciente)->where('id_empresa', $companyId)->exists()) {
            abort(404, 'El paciente no tiene agendas en la empresa activa.');
        }
        //dd($paciente);

        $hist_recetas = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->where('h.id_paciente', $id_paciente)
            ->whereNotNull('r.prescripcion')
            //->whereNotNull('r.rp')
            ->join('users as d', 'd.id', 'h.id_doctor1')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->where('a.id_empresa', $companyId)
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->orderBy('r.created_at', 'desc')
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
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $hc_receta = hc_receta::find($request["id_receta"]);
        $hclinica = Historiaclinica::where('hcid',$hc_receta->id_hc)->first();

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

}
