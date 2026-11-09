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
use DateTime;
use Sis_medico\Hospital;
use Sis_medico\Sala;

class NotaEntregaRecetaController extends Controller
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
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_empresa  = Session::get('id_empresa');
        $receta   = hc_receta::find($id);
        $historia = historiaclinica::find($receta->id_hc);
        $paciente = paciente::find($historia->id_paciente);
        $pacienteAlergia = Paciente_Alergia::where('id_paciente', $paciente->id)->get();
        $logo_emp = Empresa::find($id_empresa);
        $principioActivo = "";
        if (Count($pacienteAlergia) > 0) {
            foreach ($pacienteAlergia as $value) {
                if ($principioActivo == "") {
                    $principioActivo = $value->principio_activo->nombre;
                } else {
                    $principioActivo = $principioActivo . ", " . $value->principio_activo->nombre;
                }
            }
        } else {
            $principioActivo = "NO TIENE";
        }
        //return $historia;
        $edad = Carbon::parse($paciente->fecha_nacimiento)->age; // 1990-10-25
        //return view('hc_admision/receta/menbretada', ['paciente' => $paciente,'edad' => $edad, 'historia' => $historia,  'receta' => $receta,]);
        $detalles  = hc_receta_detalle::where('id_hc_receta', $id)->get();
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "GENERA RECETA MEDICA",
            'dato_ant1'   => $paciente->id,
            'dato1'       => $paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2,
            'dato_ant2'   => " TIPO: " . $tipo,
            'dato2'       => $receta->id,
        ]);
        if ($tipo == 5) {
            $detalles  = hc_receta_detalle::where('id_hc_receta', $id)->join('medicina_tarifario as mt', 'mt.id_medicina', 'hc_receta_detalle.id_medicina')->get();
        }
        if ($tipo == 6) {
            $detalles = hc_receta_detalle::where('id_hc_receta', $id)
                ->whereNotIn('hc_receta_detalle.id_medicina', function ($query) {
                    $query->select('id_medicina')->from('medicina_tarifario');
                })->get();
        }
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

            $view = \View::make('hc_admision.receta.menbretada', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo', 'logo_emp'))->render();
        }
        if ($tipo == 1) {
            $view = \View::make('hc_admision.receta.sinmenbrete_nuevo_preimpreso', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10'))->render();
            //$view = \View::make('hc_admision.receta.sinmenbrete', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10'))->render();
        }
        if ($tipo == 3) {
            $view = \View::make('hc_admision.receta.menbretada_crm_auditoria', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo'))->render();
        }
        if ($tipo == 5) {
            $view = \View::make('hc_admision.receta.receta_iess', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo'))->render();
        }

        if ($tipo == 6) {
            $view = \View::make('hc_admision.receta.receta_privada', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'principioActivo'))->render();
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');

        //return $pdf->download($historia->id_paciente.'_Receta_'.$id.'.pdf');

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
        $medicinas = Medicina::all();
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
        $hclinica = Historiaclinica::where('hcid', $hc_receta->id_hc)->first();

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

        if (!is_null($request["id_emp"])) {

            $idemp = [
                'id_empresa' => $request["id_emp"],
            ];

            Agenda::where('id', $hclinica->id_agenda)->update($idemp);

            $log_nota_entrega = [
                'id_hc_receta' => $request["id_receta"],
                'descripcion' => "ASIGNO EMPRESA A LA AGENDA " . $hclinica->id_agenda . " => ID_EMPRESA: " . $request["id_emp"],
                'id_usuariomod' => $idusuario,
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion' => $ip_cliente,
            ];
            Log_nota_entrega::create($log_nota_entrega);
        }
        return $request;
    }

    private static function generarNumero($id_empresa)
    {
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
        $empresa_prioridad = Empresa::where('prioridad', '1')->where('estado', 1)->first();
        $receta = hc_receta::findorfail($id);
        /*$detalle_receta = DB::table('hc_receta_detalle as rd')
                        ->join('medicina_tarifario as mt', 'mt.id_medicina', 'rd.id_medicina')
                        ->join('ap_procedimiento as ap', 'ap.id', 'mt.id_ap_procedimiento')
                        ->where('rd.id_hc_receta', $id)
                        ->select('rd.*', 'ap.valor')->get();*/
        $detalle_receta = DB::table('hc_receta_detalle as rd')
            ->join('medicina_tarifario as mt', 'mt.id_medicina', 'rd.id_medicina')
            ->join('ap_procedimiento as ap', 'ap.id', 'mt.id_ap_procedimiento')
            ->where('rd.id_hc_receta', $id)
            ->select('rd.*', 'ap.valor', 'ap.descripcion as nombre_medicina')->get();

        $hc = Historiaclinica::findorfail($receta->id_hc);
        $agenda = Agenda::findOrFail($hc->id_agenda);
        $empresa_agenda = $empresa_prioridad->id;
        if (!is_null($agenda->id_empresa)) {
            $empresa_agenda = $agenda->id_empresa;
        } else {
            $fecha_receta = substr($receta->created_at, 0, 10);
            $agenda_ultima_empresa = Agenda::where('estado', '1')->where('id_paciente', $agenda->id_paciente)->where('proc_consul', '1')->whereBetween('fechaini', [$fecha_receta . ' 00:00:00', $fecha_receta . ' 23:59:59'])->first();
            //dd($agenda_ultima_empresa);
            if (!is_null($agenda_ultima_empresa)) {
                if (!is_null($agenda_ultima_empresa->id_empresa)) {
                    $empresa_agenda = $agenda_ultima_empresa->id_empresa;
                    $agenda->id_empresa = $empresa_agenda;
                    $agenda->save();
                }
            }
        }
        $num_nota_entrega = self::generarNumero($empresa_agenda);

        $nota_entrega_detalle = null;
        $nota_entrega = Nota_entrega::where('id_hc_receta', $id)->where('id_empresa', $empresa_agenda)->where('estado', '<>', 0)->first();
        if (!is_null($nota_entrega)) {
            $nota_entrega_detalle = Nota_entrega_detalle::where('id_nota_entrega', $nota_entrega->id)->where('estado', '<>', 0)->get();
        }

        if (is_null($nota_entrega) && is_null($nota_entrega_detalle)) {
            if (!is_null($receta)) {
                $cab_nota_entrega = [
                    'numero'               => $num_nota_entrega,
                    'id_hc'                => $receta->id_hc,
                    'id_hc_receta'         => $receta->id,
                    'id_empresa'           => $empresa_agenda,
                    'id_usuariomod'        => $idusuario,
                    'id_usuariocrea'       => $idusuario,
                    'ip_modificacion'      => $ip_cliente,
                    'ip_creacion'          => $ip_cliente,
                ];
                $id_nota_entrega = Nota_entrega::insertGetId($cab_nota_entrega);

                $log_nota_entrega = [
                    'id_hc_receta' => $receta->id,
                    'descripcion' => "Creacion de Nota de entrega",
                    'id_usuariomod' => $idusuario,
                    'id_usuariocrea' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion' => $ip_cliente,
                ];
                Log_nota_entrega::create($log_nota_entrega);
            }
            if (!is_null($detalle_receta)) {
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
            if (!is_null($request->secuencia)) {
                $notas_entrega = $notas_entrega->where('nota_entrega.numero', 'like', '%' . $request->secuencia . '%');
            }
            if (!is_null($request->id_empresa)) {
                $notas_entrega = $notas_entrega->where('nota_entrega.id_empresa', $request->id_empresa);
            }

            if (!is_null($request->cedula)) {
                $notas_entrega = $notas_entrega->where('p.id', $request->cedula);
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
        } else {
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
        $nombre_original = $request['archivo']->getClientOriginalName();
        $nombre_sin_extension = pathinfo($nombre_original, PATHINFO_FILENAME);
        $nuevo_nombre = $this->sanear_string($nombre_sin_extension);
        //$nuevo_nombre    =  'nota_entrega_' . $nota_entrega->numero . "_" . date('YmdHis') . "." . $extension;
        $nuevo_nombre    =  $nuevo_nombre . "_" . date('YmdHis') . "." . $extension;
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
        abort_if(is_null($nombreArchivo), 404);
        $disk = Storage::disk('notas_entrega');
        abort_unless($disk->exists($nombreArchivo->archivo), 404);
        return response()->file($disk->path($nombreArchivo->archivo));
    }

    public function index_generar_nota_entrega(Request $request)
    {
        //return $request->all();
        ini_set('max_execution_time', 300);
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
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
        $especialidades = Especialidad::Orderby('nombre', 'asc')->get();
        $doctores = User::where('id_tipo_usuario', '3')->Orderby('apellido1', 'asc')->get();
        $seguros = Seguro::where('inactivo', '1')->Orderby('nombre', 'asc')->get();
        $agendas = [];
        $consultas = [];

        if ($fecha == null && $nombres == null) {
            $fecha = Date('Y-m-d');
            if ($rolUsuario == '11') {
                $fecha = Date('Y-m-d', strtotime("-2 months"));
            }
        }
        if ($proc_consul == null && $rolUsuario == '11') {

            $proc_consul = '1';
        }

        if ($fecha_hasta == null) {
            $fecha_hasta = Date('Y-m-d');
        }
        if ($proc_consul == null) {
            $proc_consul = '2';
        }

        $nombres_sql = '';
        $agendas_pac = [];

        $pacientes1 = DB::table('agenda as a')->where('a.estado', 1)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p', 'p.id', 'a.id_paciente')
            ->join('users as d', 'd.id', 'a.id_doctor1')
            ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
            ->join('seguros as se', 'se.id', 'a.id_seguro')
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->leftjoin('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
            ->whereNull('h.hcid')
            ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
            ->where('a.id_seguro', '2')
            ->groupBy('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.id')
            ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'a.id_doctor1 as doctor', 'a.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'a.omni');

        if ($fecha != null) {
            $pacientes1 = $pacientes1->whereBetween('fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }


        $pacientes2 = DB::table('agenda as a')->where('a.estado', 1)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p', 'p.id', 'a.id_paciente')
            ->join('users as d', 'd.id', 'a.id_doctor1')
            ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
            ->join('seguros as se', 'se.id', 'a.id_seguro')
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
            ->where('a.proc_consul', '0')
            ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
            ->where('a.id_seguro', '2')
            ->whereNull('hc_pro.id_doctor_examinador')
            ->groupBy('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.id')
            ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'h.id_doctor1 as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'a.omni');

        if ($fecha != null) {
            $pacientes2 = $pacientes2->whereBetween('fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        $pacientes2_0 = DB::table('agenda as a')->where('a.estado', 1)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p', 'p.id', 'a.id_paciente')
            ->join('users as d', 'd.id', 'a.id_doctor1')
            ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
            ->join('seguros as se', 'se.id', 'a.id_seguro')
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
            ->where('a.proc_consul', '0')
            ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
            ->where('a.id_seguro', '2')
            ->whereNotNull('hc_pro.id_doctor_examinador')
            ->groupBy('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.id')
            ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'hc_pro.id_doctor_examinador as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'a.omni');

        if ($fecha != null) {
            $pacientes2_0 = $pacientes2_0->whereBetween('fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        $pacientes3 = DB::table('agenda as a')->where('a.estado', 1)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p', 'p.id', 'a.id_paciente')
            ->join('users as d', 'd.id', 'a.id_doctor1')
            ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
            ->join('seguros as se', 'se.id', 'a.id_seguro')
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->where('a.proc_consul', '1')
            ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
            ->where('a.id_seguro', '2')
            ->groupBy('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.id')
            ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'h.id_doctor1 as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'a.omni');

        if ($fecha != null) {
            if (Auth::user()->id_tipo_usuario == '1') {
                //dd($pacientes3->where('p.id','0955695630')->get());
            }
            $pacientes3 = $pacientes3->whereBetween('fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        $pacientes4 = DB::table('agenda as a')->where('a.estado', 4)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p', 'p.id', 'a.id_paciente')
            ->join('users as d', 'd.id', 'a.id_doctor1')
            ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
            ->join('seguros as se', 'se.id', 'a.id_seguro')
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
            ->where('a.proc_consul', '4')
            ->whereNotIn('a.id_doctor1', ['4444444444', '444444444A', '444444444B'])
            ->where('a.id_seguro', '2')
            ->groupBy('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.id')
            ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'hc_pro.id_doctor_examinador as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'a.omni');

        if ($fecha != null) {
            $pacientes4 = $pacientes4->whereBetween('fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($proc_consul != 2) {
            if ($proc_consul == 3) {
                $pacientes1 = $pacientes1->where('a.omni', 'OM');
                $pacientes2 = $pacientes2->where('a.omni', 'OM');
                $pacientes2_0 = $pacientes2_0->where('a.omni', 'OM');
                $pacientes3 = $pacientes3->where('a.omni', 'OM');
                $pacientes4 = $pacientes4->where('a.omni', 'OM');
            } elseif ($proc_consul == 4) {
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

        if (!is_null($id_doctor1)) {
            $pacientes1 = $pacientes1->where('a.id_doctor1', $id_doctor1);
            $pacientes2 = $pacientes2->where('h.id_doctor1', $id_doctor1);
            $pacientes2_0 = $pacientes2_0->where('hc_pro.id_doctor_examinador', $id_doctor1);
            $pacientes3 = $pacientes3->where('h.id_doctor1', $id_doctor1);
            $pacientes4 = $pacientes4->where('hc_pro.id_doctor_examinador', $id_doctor1);
        }

        if (!is_null($id_seguro)) {
            $pacientes1 = $pacientes1->where('a.id_seguro', $id_seguro);
            $pacientes2 = $pacientes2->where('h.id_seguro', $id_seguro);
            $pacientes2_0 = $pacientes2_0->where('h.id_seguro', $id_seguro);
            $pacientes3 = $pacientes3->where('h.id_seguro', $id_seguro);
            $pacientes4 = $pacientes4->where('h.id_seguro', $id_seguro);
        }

        if (!is_null($espid)) {
            $pacientes1 = $pacientes1->where('a.espid', $espid);
            $pacientes2 = $pacientes2->where('a.espid', $espid);
            $pacientes2_0 = $pacientes2_0->where('a.espid', $espid);
            $pacientes3 = $pacientes3->where('a.espid', $espid);
            $pacientes4 = $pacientes4->where('a.espid', $espid);
        }

        if ($nombres != null) {
            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
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

        $agendas_proc = null;

        foreach ($pacientes as $pac) {

            if ($pac->proc_consul == '1') {
                $pentax = Pentax::where('id_agenda', $pac->id_agenda)->first();
                if (!is_null($pentax)) {
                    $txt_px = '';

                    foreach ($pentax->procedimientos as $p) {
                        if ($txt_px == '') {
                            $txt_px = $p->procedimiento->nombre;
                        } else {
                            $txt_px = $txt_px . '+' . $p->procedimiento->nombre;
                        }
                    }
                    $agendas_proc[$pac->id_agenda] = [$txt_px];
                }
            }
        }
        Cookie::queue('ruta', 'historia', '1000');
        return view('hc_admision/nota_de_entrega/index_generar_nota_entrega', ['nombres' => $nombres, 'agendas_pac' => $pacientes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'agendas_proc' => $agendas_proc, 'proc_consul' => $proc_consul, 'especialidades' => $especialidades, 'id_especialidad' => '0', 'doctores' => $doctores, 'id_doctor1' => $id_doctor1, 'seguros' => $seguros, 'id_seguro' => $id_seguro]);
    }

    public function detalle_receta_nota_entrega($id)
    {
        ini_set('max_execution_time', 300);
        //return $id;
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $seguros    = null;
        $subseguros = null;
        $seguros    = Seguro::all();
        //$subseguros = Subseguro::all();
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
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'paciente.fecha_nacimiento', 'paciente.ocupacion', 'h.parentesco as hparentesco', 'paciente.parentesco as pparentesco', 'paciente.estadocivil', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.nombre as snombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'empresa.nombrecomercial', 'paciente.sexo', 'paciente.gruposanguineo', 'paciente.transfusion', 'paciente.alergias', 'paciente.vacuna', 'paciente.historia_clinica', 'paciente.antecedentes_pat', 'paciente.antecedentes_fam', 'paciente.antecedentes_quir', 'h.hcid', 'paciente.referido', 'paciente.id_usuario', 'paciente.trabajo', 'paciente.observacion', 'paciente.alcohol', 'hs.nombre as hsnombre', 'h.presion', 'h.pulso', 'h.temperatura', 'h.o2', 'h.altura', 'h.peso', 'h.perimetro', 'h.examenes_realizar', 'h.id_seguro as h_idseguro')
            ->where('agenda.id', '=', $id)
            ->first();
        $procedimientos_pentax = null;
        $no_admin              = false;
        $protocolo             = null;
        $evolucion             = null;
        $imagenes              = null;
        $documentos            = null;
        $biopsias              = null;
        $orden_laboratorio     = null;
        $estudios              = null;
        $hc_rec                = null;
        $evoluciones = DB::table('hc_evolucion as e')->join('historiaclinica as h', 'e.hcid', 'h.hcid')->where('h.id_paciente', $agenda->id_paciente)->join('agenda as a', 'a.id', 'h.id_agenda')->orderBy('a.fechaini', 'desc')->select('e.*', 'a.fechaini', 'a.proc_consul', 'a.espid')->orderBy('e.id', 'desc')->where('a.estado_cita', '<>', '3')->get();
        $protocolos = DB::table('hc_protocolo as p')->join('historiaclinica as h', 'h.hcid', 'p.hcid')->where('h.id_paciente', $agenda->id_paciente)->join('hc_procedimientos as hc', 'hc.id', 'p.id_hc_procedimientos')->join('agenda as a', 'a.id', 'h.id_agenda')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hc.id_procedimiento_completo')->leftjoin('hc_receta as r', 'r.id_hc', 'h.hcid')->leftjoin('users as d1', 'h.id_doctor1', 'd1.id')->leftjoin('users as d2', 'h.id_doctor2', 'd2.id')->leftjoin('users as d3', 'h.id_doctor3', 'd3.id')->select('p.*', 'hc.id_procedimiento_completo', 'a.fechaini', 'pc.nombre_general', 'p.hallazgos', 'd1.apellido1 as d1apellido1', 'd1.nombre1 as d1nombre1', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1')->orderBy('a.fechaini', 'desc')->orderBy('p.created_at', 'desc')->where('a.espid', '<>', '10')->get();
        $protocolos_dia   = [];
        $evoluciones_proc = [];
        $child_pugh       = null;
        $hc_proc          = null;
        if (!is_null($agenda->hcid)) {
            if ($agenda->proc_consul == '0' || $agenda->proc_consul == '4') {
                //CONSULTAS O EVOLUCIONES CREADAS POR EL DOCTOR
                $xseguro   = Seguro::find($agenda->h_idseguro);
                $evolucion = DB::table('hc_evolucion as e')->where('e.hcid', $agenda->hcid)->join('hc_receta as r', 'r.id_hc', 'e.hcid')->select('e.*', 'r.rp')->first();
                if (is_null($evolucion)) {
                    if ($agenda->estado_cita == '4') {
                        $hc_proc = hc_procedimientos::where('id_hc', $agenda->hcid)->first();
                        if (is_null($hc_proc)) {
                            $input_hc_procedimiento = [
                                'id_hc'                     => $agenda->hcid,
                                'id_seguro'                 => $agenda->h_idseguro,
                                'id_doctor_examinador'      => $agenda->id_doctor1,
                                'id_procedimiento_completo' => '40',
                                'ip_modificacion'           => $ip_cliente,
                                'id_usuariocrea'            => $idusuario,
                                'id_usuariomod'             => $idusuario,
                                'ip_creacion'               => $ip_cliente,
                                'created_at'                => date('Y-m-d H:i:s'),
                                'updated_at'                => date('Y-m-d H:i:s'),
                            ];
                            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);
                        } else {
                            $id_hc_procedimiento    = $hc_proc->id;
                            $procedimiento_generico = hc_procedimientos::find($id_hc_procedimiento);
                            if (is_null($procedimiento_generico->id_doctor_examinador)) {
                                $inputx2 = [
                                    'id_doctor_examinador' => $agenda->id_doctor1,
                                    'ip_modificacion'      => $ip_cliente,
                                    'id_usuariomod'        => $idusuario,
                                ];
                                //return $request->all();
                                $procedimiento_generico->update($inputx2);
                            }
                            $procedimiento_generico = hc_procedimientos::find($id_hc_procedimiento);
                        }
                        $hc_proc = hc_procedimientos::where('id_hc', $agenda->hcid)->first();
                        $xcuadro_clinico = null;
                        $tsexo           = '';
                        if ($agenda->espid == '4') {
                            if (!is_null($xseguro)) {
                                if ($xseguro->tipo == '0') {
                                    if ($agenda->sexo == '1') {
                                        $tsexo = 'MASCULINO';
                                    } elseif ($agenda->sexo == '2') {
                                        $tsexo = 'FEMENINO';
                                    }
                                    $tedad = Carbon::createFromDate(substr($agenda->fecha_nacimiento, 0, 4), substr($agenda->fecha_nacimiento, 5, 2), substr($agenda->fecha_nacimiento, 8, 2))->age;
                                    $xcuadro_clinico = 'PACIENTE DE SEXO ' . $tsexo . ' DE ' . $tedad . ' AÑOS DE EDAD CON CUADRO CLINICO DE  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; MESES DE EVOLUCION CARACTERIZADO POR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; , &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,<br>
                                    (DESCRIPCION DE SINTOMAS INTENSIDAD, HORARIO DE APARICION QUE LO EXACERBA)<br>
                                    EN LA ACTUALIDAD SINTOMAS SE INTESIFICAN POR LO QUE ACUDE A CONSULTA.';
                                }
                            }
                        }
                        $input_hc_evolucion = [
                            'hc_id_procedimiento' => $id_hc_procedimiento,
                            'hcid'                => $agenda->hcid,
                            'secuencia'           => '0',
                            'cuadro_clinico'      => $xcuadro_clinico,
                            'fecha_ingreso'       => ' ',
                            'ip_modificacion'     => $ip_cliente,
                            'id_usuariomod'       => $idusuario,
                            'id_usuariocrea'      => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'created_at'          => date('Y-m-d H:i:s'),
                            'updated_at'          => date('Y-m-d H:i:s'),
                        ];
                        $id_evolucion = Hc_evolucion::insertGetId($input_hc_evolucion);
                        $evolucion = DB::table('hc_evolucion as e')->where('e.id', $id_evolucion)->leftjoin('hc_receta as r', 'r.id_hc', 'e.hcid')->select('e.*', 'r.rp')->first();
                        //return $evolucion;
                        //dd($evolucion);
                    }
                }
                $child_pugh    = hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();
                $examen_fisico = null;
                if ($agenda->espid == '4') {
                    $examen_fisico = 'ESTADO CABEZA Y CUELLO:
                        ESTADO TORAX:
                        ESTADO ABDOMEN:
                        ESTADO MIEMBROS SUPERIORES:
                        ESTADO MIEMBROS INFERIORES:
                        OTROS:
                        ';
                    if (!is_null($xseguro)) {
                        if ($xseguro->tipo == '0') {
                            $examen_fisico = 'REVISION ACTUAL ORGANOS Y SISTEMAS
                            PIEL: TEXTURA NORMAL, HIDRATADA, SIN LESIONES
                            CABEZA: NORMOCEFALO, CABELLO BIEN IMPLANTADO MAREO
                            CARA: SIN ALTERACIONES
                            CUELLO: MOVIL, CENTRAL, SIN ADENOPATIAS
                            TORAX: SIMETRICO, SIN LESIONES
                            CSPS: VENTILADOS.
                            RSCS: RITMICOS
                            ABDOMEN:BANDO DEPRESIBLE SIN DOLOR A LA PALPACION SUPERFICIAL Y PROFUNDA.
                            RSHS(+), PUNTOS URETERALES(-),
                            COLUMNA VERTEBRAL: CENTRAL, SIN DESVIACION, NI LESIONES, PUÑOPERCUSION: (-)
                            EXTREMIDADES: SIN ALTERACIONES, SIMETRICOS
                            EXAMEN FISICO REGIONAL
                            CABEZA: NORMOCEFALO, CABELLO BIEN IMPLANTADO, CEFALEA
                            EXTREMIDADES: SIN LESIONES NI ALTERACIONES';
                        }
                    }
                }
                if (is_null($child_pugh)) {
                    if ($agenda->estado_cita == '4') {
                        $input_child_pugh = [
                            'id_hc_evolucion' => $evolucion->id,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                            'id_usuariocrea'  => $idusuario,
                            'examen_fisico'   => $examen_fisico,
                            'ip_creacion'     => $ip_cliente,
                            'created_at'      => date('Y-m-d H:i:s'),
                            'updated_at'      => date('Y-m-d H:i:s'),
                        ];
                        hc_child_pugh::insert($input_child_pugh);
                        $child_pugh = hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();
                    }
                }
                $hc_proc = hc_procedimientos::where('id_hc', $agenda->hcid)->first();
                if (is_null($hc_proc)) {
                    $input_hc_procedimiento = [
                        'id_hc'                     => $agenda->hcid,
                        'id_seguro'                 => $agenda->h_idseguro,
                        'id_doctor_examinador'      => $agenda->id_doctor1,
                        'id_procedimiento_completo' => '40',
                        'ip_modificacion'           => $ip_cliente,
                        'id_usuariocrea'            => $idusuario,
                        'id_usuariomod'             => $idusuario,
                        'ip_creacion'               => $ip_cliente,
                        'created_at'                => date('Y-m-d H:i:s'),
                        'updated_at'                => date('Y-m-d H:i:s'),
                    ];
                    $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);
                } else {
                    $id_hc_procedimiento    = $hc_proc->id;
                    $procedimiento_generico = hc_procedimientos::find($id_hc_procedimiento);
                    if (is_null($procedimiento_generico->id_doctor_examinador)) {
                        $inputx2 = [
                            'id_doctor_examinador' => $agenda->id_doctor1,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariomod'        => $idusuario,
                        ];
                        //return $request->all();
                        $procedimiento_generico->update($inputx2);
                    }
                    $procedimiento_generico = hc_procedimientos::find($id_hc_procedimiento);
                }
                $hc_proc = hc_procedimientos::where('id_hc', $agenda->hcid)->first();
                //return $child_pugh;
            }
            if ($agenda->proc_consul == '1') {
                //PROCEDIMIENTOS
                $protocolo = DB::table('hc_protocolo as p')->where('p.hcid', $agenda->hcid)->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')->leftjoin('pentax as px', 'px.hcid', 'p.hcid')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3')->orderBy('p.created_at', 'desc')->first();
                //dd($protocolo->id);
                if (!is_null($protocolo)) {
                    $evoluciones_proc = DB::table('hc_evolucion as e')->where('e.hc_id_procedimiento', $protocolo->id_hc_procedimientos)->get();
                    //dd($evoluciones_proc);
                    $evoluciones_proc = DB::table('hc_evolucion as e')->where('e.hc_id_procedimiento', $protocolo->id_hc_procedimientos)->get();
                    //dd($evoluciones_proc);
                }
                $protocolos_dia = DB::table('hc_protocolo as p')->where('p.hcid', $agenda->hcid)->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->select('p.*', 'nombre_general')->get();
                if (!is_null($protocolo)) {
                    $procedimientos_pentax = PentaxProc::where('id_pentax', '=', $protocolo->pxid)->get();
                    $imagenes               = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '1')->get();
                    $documentos             = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '2')->get();
                    $estudios               = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '3')->get();
                    $biopsias               = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '4')->get();
                    $hc_proc                = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                    $procedimiento_generico = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                    if (is_null($procedimiento_generico->id_doctor_examinador)) {
                        $inputx2 = [
                            'id_doctor_examinador' => $agenda->id_doctor1,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariomod'        => $idusuario,
                        ];
                        //return $request->all();
                        $procedimiento_generico->update($inputx2);
                        $procedimiento_generico = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                    }
                    $hc_proc = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                } else {
                    $hc_proc = hc_procedimientos::where('id_hc', $agenda->hcid)->first();
                    if (is_null($hc_proc)) {
                        $input_hc_procedimiento = [
                            'id_hc'                => $agenda->hcid,
                            'id_seguro'            => $agenda->h_idseguro,
                            'id_doctor_examinador' => $agenda->id_doctor1,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariocrea'       => $idusuario,
                            'id_usuariomod'        => $idusuario,
                            'ip_creacion'          => $ip_cliente,
                            'created_at'           => date('Y-m-d H:i:s'),
                            'updated_at'           => date('Y-m-d H:i:s'),
                        ];
                        $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);
                    } else {
                        $id_hc_procedimiento    = $hc_proc->id;
                        $procedimiento_generico = hc_procedimientos::find($id_hc_procedimiento);
                        if (is_null($procedimiento_generico->id_doctor_examinador)) {
                            $inputx2 = [
                                'id_doctor_examinador' => $agenda->id_doctor1,
                                'ip_modificacion'      => $ip_cliente,
                                'id_usuariomod'        => $idusuario,
                            ];
                            //return $request->all();
                            $procedimiento_generico->update($inputx2);
                        }
                        $procedimiento_generico = hc_procedimientos::find($id_hc_procedimiento);
                    }
                    $input_hc_protocolo = [
                        'id_hc_procedimientos' => $id_hc_procedimiento,
                        'hora_inicio'          => date('H:i:s'),
                        'hora_fin'             => date('H:i:s'),
                        'estado_final'         => ' ',
                        'ip_modificacion'      => $ip_cliente,
                        'hcid'                 => $agenda->hcid,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'ip_creacion'          => $ip_cliente,
                        'created_at'           => date('Y-m-d H:i:s'),
                        'updated_at'           => date('Y-m-d H:i:s'),
                    ];
                    hc_protocolo::insert($input_hc_protocolo);
                    $protocolo = DB::table('hc_protocolo as p')->where('p.hcid', $agenda->hcid)->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')->leftjoin('pentax as px', 'px.hcid', 'p.hcid')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3')->orderBy('p.created_at', 'desc')->first();
                    $procedimientos_pentax = PentaxProc::where('id_pentax', '=', $protocolo->pxid)->get();
                    $imagenes   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '1')->get();
                    $documentos = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '2')->get();
                    $estudios   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '3')->get();
                    $biopsias   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '4')->get();
                    $hc_proc    = hc_procedimientos::find($protocolo->id_hc_procedimientos);
                }
            }
            //la receta
            $hc_rec = hc_receta::where('id_hc', $agenda->hcid)->first();
            if (is_null($hc_rec)) {
                $input_hc_receta = [
                    'id_hc'           => $agenda->hcid,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];
                if ($agenda->proc_consul == '0') {
                    hc_receta::insert($input_hc_receta);
                }
            }
            $hc_rec = hc_receta::where('id_hc', $agenda->hcid)->first();
        }
        $fecha_dia = date('Y-m-d', strtotime($agenda->fechaini));
        $nuevafecha = strtotime('+1 day', strtotime($fecha_dia));
        $nuevafecha = date('Y-m-d', $nuevafecha);
        $cant_cortesias = Agenda::where('id_doctor1', $agenda->id_doctor1)->where('fechaini', '>', $fecha_dia)->where('fechaini', '<', $nuevafecha)->where('cortesia', 'SI')->count();
        $mail = '';
        if ($agenda->pparentesco == 'Principal') {
            //$mail = User::find($agenda->id_paciente)->email;
            $mail = null;
            $u1   = User::find($agenda->id_paciente);
            if (!is_null($u1)) {
                $mail = $u1->email;
            }
        } else {
            $mail = User::find($agenda->id_usuario)->email;
        }
        $proc_completo = procedimiento_completo::orderBy('nombre_general')->get();
        $doctores = User::where('estado', '1')->where('id_tipo_usuario', '3')->get();
        $anestesiologos = User::where('estado', '1')->where('id_tipo_usuario', '9')->get();
        $enfermeros = User::where('estado', '1')->where('id_tipo_usuario', '6')->get();
        $alergiasxpac = Paciente_Alergia::where('id_paciente', $agenda->id_paciente)->get();
        // dd($alergiasxpac->principio_activo);
        $laboratorio_externo = Paciente_biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', '1')->get();
        $biopsias_1          = Paciente_biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', '0')->get();
        //$biopsias = hc_imagenes_protocolo::where('id_hc_protocolo',$protocolo->id)->where('estado', '4')->get();
        $biopsias_2 = DB::table('historiaclinica')->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')
            ->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')
            ->where('id_paciente', $agenda->id_paciente)
            ->where('hc_imagenes_protocolo.estado', '4')->get();
        //return $biopsias_2;
        //dd($evoluciones);
        //Nueva funcionalidas Historial de Recetas
        //$xpaciente = Paciente::find($agenda->id_paciente);
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
        //Fin nueva Funcionalidad historial de Recetas
        $orden_biopsias = Hc4_Biopsias::where('id_paciente', $agenda->id_paciente)->groupBy("hc_id_procedimiento")->get();
        $paciente_observacion = Paciente_Observaciones::where('id_paciente', $agenda->id_paciente)->first();
        //dd($hist_recetas);
        return view('hc_admision/nota_de_entrega/historial_recetas', ['agenda' => $agenda, 'cant_cortesias' => $cant_cortesias, 'evolucion' => $evolucion, 'evoluciones' => $evoluciones, 'seguros' => $seguros, 'subseguros' => $subseguros, 'mail' => $mail, 'protocolos' => $protocolos, 'protocolo' => $protocolo, 'procedimientos_pentax' => $procedimientos_pentax, 'proc_completo' => $proc_completo, 'doctores' => $doctores, 'anestesiologos' => $anestesiologos, 'enfermeros' => $enfermeros, 'imagenes' => $imagenes, 'documentos' => $documentos, 'estudios' => $estudios, 'biopsias' => $biopsias, 'hc_receta' => $hc_rec, 'alergiasxpac' => $alergiasxpac, 'protocolos_dia' => $protocolos_dia, 'evoluciones_proc' => $evoluciones_proc, 'child_pugh' => $child_pugh, 'hc_proc' => $hc_proc, 'laboratorio_externo' => $laboratorio_externo, 'biopsias_1' => $biopsias_1, 'biopsias_2' => $biopsias_2, 'hist_recetas' => $hist_recetas, 'orden_biopsias' => $orden_biopsias, 'paciente_observacion' => $paciente_observacion]);
    }



    public function excel_nota_entrega(Request $request)
    {
        //dd($request->all());

        $notas_entrega = $this->busqueda_notas_entrega($request)->get();
        //dd($notas_entrega);
        $detalles_nota = [];
        $i = 0;
        foreach ($notas_entrega as $nota) {
            // dd($nota);

            $detalles = Nota_entrega_detalle::where('id_nota_entrega', $nota->id)->get();

            foreach ($detalles as $detalle) {
                // dd($detalles);
                $detalles_nota[$i] = $detalle;
                $i++;
            }
        }
        // dd($i,$detalles_nota);
        // dd($detalles_nota,$notas_entrega);

        $titulos = array("FECHA", "CEDULA", "PACIENTE", "NUMERO", "EMPRESA", "NOMBRE MEDICINA", "CANTIDAD", "PRECIO", "TOTAL", "TIPO", "ESTADO");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");


        Excel::create('Nota_Entrega', function ($excel) use ($titulos, $posicion, $notas_entrega, $detalles_nota) {
            $excel->sheet('Nota_Entrega', function ($sheet) use ($titulos, $posicion, $notas_entrega, $detalles_nota) {

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
                    $nota = Nota_entrega::find($detalle->id_nota_entrega);
                    $historia = $nota->historia;
                    $paciente_nota = Paciente::withoutGlobalScopes()->findOrFail($historia->id_paciente);
                    $agenda_nota = Agenda::withoutGlobalScopes()->findOrFail($historia->id_agenda);
                    $empresa_nota = Empresa::withoutGlobalScopes()->findOrFail($nota->id_empresa);
                    $datos_excel = [];
                    $fecha_formateada = date('Y-m-d', strtotime($nota->created_at));
                    $nombre_paciente = $paciente_nota->apellido1 . " " . $paciente_nota->apellido2 . " " . $paciente_nota->nombre1 . " " . $paciente_nota->nombre2;
                    $total = $detalle->cantidad * $detalle->precio;

                    if ($agenda_nota->proc_consul == 0)
                        $tipo = 'Consulta';
                    else
                        $tipo = 'Procedimiento';

                    $doc_escaneado = Nota_Entrega_Escaneados::where('id_nota_entrega', $nota->id)->where('estado', '1')->first();
                    if ($doc_escaneado)
                        $estado = 'Entregada';
                    else
                        $estado = 'Por entregar';

                    $datos_excel = [$fecha_formateada, $paciente_nota->id, $nombre_paciente, $nota->numero, $empresa_nota->nombrecomercial, $detalle->nombre_medicina, $detalle->cantidad, $detalle->precio, $total, $tipo, $estado];

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue((string) $datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }

    public function nuevaFarmaciaIndex(Request $request)
    {
        $fecha_desde = date('Y/m/d');
        if (!is_null($request['fecha'])) {
            $fecha_desde = date_format(new DateTime($request['fecha']), 'Y/m/d');
        }
        $fecha_hasta = date('Y/m/d');;
        if (!is_null($request['fecha_hasta'])) {
            $fecha_desde = date_format(new DateTime($request['fecha']), 'Y/m/d');
        }
        $consulta = $this->searchFarmaciaNueva($request);
        $consulta = $consulta->paginate(50)->appends($request->except('page'));
        $seguros = Seguro::where('tipo', 0)->where('inactivo', 1)->get();
        $unidades = Hospital::where('estado', '1')->get();
        return view('hc_admision/nota_de_entrega/lista_farmacia_nueva', ['fecha' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'consulta' => $consulta, 'seguros' => $seguros, 'unidades' => $unidades, 'request' => $request]);
    }

    public function searchFarmaciaNueva(Request $request)
    {
        ini_set('max_execution_time', 600);
        $fecha_desde = date('Y/m/d');
        if (!is_null($request['fecha'])) {
            $fecha_desde = date_format(new DateTime($request['fecha']), 'Y/m/d');
        }
        $fecha_hasta = date('Y/m/d');;
        if (!is_null($request['fecha_hasta'])) {
            $fecha_desde = date_format(new DateTime($request['fecha']), 'Y/m/d');
        }
        $fecha_hasta = date_format(new DateTime($request['fecha_hasta']), 'Y/m/d');
        $seguro = $request->seguro;
        $tipo = $request->tipo;
        $paciente = $request->paciente;
        $unidad = $request->unidad;
        $doctor = $request->doctor;
        $cedula = $request->cedula;
        $consulta = Agenda::join('historiaclinica as h', 'agenda.id', '=', 'h.id_agenda')
            ->join('seguros as s', 's.id', '=', 'h.id_seguro')
            ->join('paciente as p', 'h.id_paciente', '=', 'p.id')
            ->join('users as u', 'u.id', '=', 'h.id_doctor1')
            ->join('sala as s1', 's1.id', '=', 'agenda.id_sala')
            ->join('hospital as ho', 'ho.id', '=', 's1.id_hospital')
            ->join('empresa as e', 'e.id', 'agenda.id_empresa')
            ->where('agenda.estado', '1')
            ->where('agenda.estado_cita', '4')
            ->where('agenda.proc_consul', '!=', '2')
            ->whereBetween('agenda.fechaini', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);

        if (!is_null($seguro)) {
            $consulta = $consulta->where('s.id', '=', $seguro);
        }

        if (!is_null($unidad)) {
            $consulta = $consulta->where('ho.id', '=', $unidad);
        }

        if (!is_null($tipo)) {
            $consulta = $consulta->where('agenda.proc_consul', '=', $tipo);
        }

        if (!is_null($cedula)) {
            $consulta = $consulta->where('p.id', '=', $cedula);
        }

        if (!is_null($paciente)) {
            $nombres2    = explode(" ", $paciente);
            $nombres_sql = '';
            $cantidad    = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                $consulta = $consulta->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {
                $consulta = $consulta->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }
        if (!is_null($doctor)) {
            $nombres_doc    = explode(" ", $doctor);
            $nombres_sql = '';
            $cantidad    = count($nombres_doc);
            foreach ($nombres_doc as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                $consulta = $consulta->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(u.apellido1," ",u.apellido2," ",u.nombre1," ",u.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(u.nombre1," ",u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(u.nombre1," ",u.apellido1," ",u.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(u.apellido1," ",u.apellido2," ",u.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {
                $consulta = $consulta->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(u.apellido1," ",u.apellido2," ",u.nombre1," ",u.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(u.nombre1," ",u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }
        $consulta = $consulta->select(
            'h.hcid as hcid',
            'agenda.id as id_agenda',
            'agenda.fechaini',
            's.nombre as seguro',
            'e.nombre_corto as empresa',
            's1.nombre_sala as sala',
            'ho.nombre_hospital as hospital',
            'p.id as cedula',
            DB::raw("CASE WHEN agenda.proc_consul = '0' THEN 'CONSULTA' WHEN agenda.proc_consul = '4' THEN agenda.observaciones ELSE 'PROCEDIMIENTO' END as tipo"),
            DB::raw("concat(u.apellido1,' ',u.apellido2,' ',u.nombre1) as doctor"),
            DB::raw("concat(p.apellido1,' ',p.apellido2,' ',p.nombre1) as paciente")
        );

        return $consulta;
    }

    public function listado_farmacia($id_hc)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $empresa_prioridad = Empresa::where('prioridad', '1')->where('estado', 1)->first();
        $receta = hc_receta::where('id_hc', $id_hc)->first();
        //dd($receta);
        if (!is_null($receta)) {
            $detalle_receta = DB::table('hc_receta_detalle as rd')
                ->join('medicina_tarifario as mt', 'mt.id_medicina', 'rd.id_medicina')
                ->join('ap_procedimiento as ap', 'ap.id', 'mt.id_ap_procedimiento')
                ->where('rd.id_hc_receta', $receta->id)
                ->select('rd.*', 'ap.valor', 'ap.descripcion as nombre_medicina')->get();

            $hc = Historiaclinica::findorfail($receta->id_hc);
            $agenda = Agenda::findOrFail($hc->id_agenda);
            $empresa_agenda = $empresa_prioridad->id;
            if (!is_null($agenda->id_empresa)) {
                $empresa_agenda = $agenda->id_empresa;
            } else {
                $fecha_receta = substr($receta->created_at, 0, 10);
                $agenda_ultima_empresa = Agenda::where('estado', '1')->where('id_paciente', $agenda->id_paciente)->where('proc_consul', '1')->whereBetween('fechaini', [$fecha_receta . ' 00:00:00', $fecha_receta . ' 23:59:59'])->first();
                //dd($agenda_ultima_empresa);
                if (!is_null($agenda_ultima_empresa)) {
                    if (!is_null($agenda_ultima_empresa->id_empresa)) {
                        $empresa_agenda = $agenda_ultima_empresa->id_empresa;
                        $agenda->id_empresa = $empresa_agenda;
                        $agenda->save();
                    }
                }
            }
            $num_nota_entrega = self::generarNumero($empresa_agenda);

            $nota_entrega_detalle = null;
            $nota_entrega = Nota_entrega::where('id_hc_receta', $receta->id)->where('id_empresa', $empresa_agenda)->where('estado', '<>', '0')->first();
            if (!is_null($nota_entrega)) {
                $nota_entrega_detalle = Nota_entrega_detalle::where('id_nota_entrega', $nota_entrega->id)->where('estado', 1)->get();
            }

            if (is_null($nota_entrega) /*&& is_null($nota_entrega_detalle)*/) {
                if (!is_null($receta)) {
                    $cab_nota_entrega = [
                        'numero'               => $num_nota_entrega,
                        'id_hc'                => $receta->id_hc,
                        'id_hc_receta'         => $receta->id,
                        'id_empresa'           => $empresa_agenda,
                        'id_usuariomod'        => $idusuario,
                        'id_usuariocrea'       => $idusuario,
                        'ip_modificacion'      => $ip_cliente,
                        'ip_creacion'          => $ip_cliente,
                        'estado'               => '-1',
                    ];
                    $id_nota_entrega = Nota_entrega::insertGetId($cab_nota_entrega);

                    $log_nota_entrega = [
                        'id_hc_receta' => $receta->id,
                        'descripcion' => "Creacion de Nota de entrega",
                        'id_usuariomod' => $idusuario,
                        'id_usuariocrea' => $idusuario,
                        'ip_modificacion' => $ip_cliente,
                        'ip_creacion' => $ip_cliente,
                    ];
                    Log_nota_entrega::create($log_nota_entrega);
                }
                if (!is_null($detalle_receta)) {
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
                    $nota_entrega = Nota_entrega::where('id_hc_receta', $receta->id)->where('id_empresa', $empresa_agenda)->where('estado', '<>', 0)->first();
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
            }/* else {
                $nota_entrega = Nota_entrega::where('id_hc_receta', $receta->id)->where('id_empresa', $empresa_agenda)->where('estado', '<>', 0)->first();
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
                            $nota_entrega = Nota_entrega::where('id_hc_receta', $receta->id)->where('id_empresa', $empresa_agenda)->where('estado', '<>', 0)->first();
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
            }*/
            return redirect()->route('nota_entrega.editar', ['id' => $receta->id]);
        } else {
            return ['status' => 'error', 'msj' => ' no se encontro ninguna receta'];
        }
    }

    public function visualizar_nota_entrega_pdf_nuevo($nombre)
    {
        $nombreArchivo = Nota_Entrega_Escaneados::where('archivo', $nombre)->first();
        abort_if(is_null($nombreArchivo), 404);
        $disk = Storage::disk('notas_entrega');
        abort_unless($disk->exists($nombreArchivo->archivo), 404);
        return response()->file($disk->path($nombreArchivo->archivo));
    }

    public function excel_nota_entrega_nuevo(Request $request)
    {
        ini_set('max_execution_time', 600);
        $consulta = $this->searchFarmaciaNueva($request);
        $consulta = $consulta->get();
        $titulos = array("FECHA", "CEDULA", "PACIENTE", "SEGURO/EMPRESA", "CONSULTORIO/UNIDAD", "TIPO", "DOCTOR", "PROCEDIMIENTO", "NUMERO", "ARCHIVO SUBIDO");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J");

        Excel::create('Nota_Entrega', function ($excel) use ($titulos, $posicion, $consulta) {
            $excel->sheet('Nota_Entrega', function ($sheet) use ($titulos, $posicion, $consulta) {

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
                foreach ($consulta as $value) {
                    $receta = hc_receta::where('id_hc', $value->hcid)->first();
                    $notaEntrega = null;
                    $archivo = null;
                    $numero = '';
                    $nombre_archivo = '';
                    if ($value->tipo == 'CONSULTA') {
                        $notaEntrega = Nota_entrega::where('id_hc', $value->hcid)->where('id_hc_receta', $receta->id)->first();
                    }

                    if (!is_null($notaEntrega)) {
                        $archivo = Nota_Entrega_Escaneados::where('id_nota_entrega', $value->id_nota_entrega)->first();
                        $numero = $notaEntrega->numero;
                    }

                    if (!is_null($archivo)) {
                        $nombre_archivo = $archivo->archivo;
                    }
                    $datos_excel = [];
                    $datos_excel = [$value->fechaini, $value->cedula, $value->paciente, $value->seguro . '/' . $value->empresa, $value->sala . '/' . $value->hospital, $value->tipo, $value->doctor, $value->procedimiento, $numero, $nombre_archivo];

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue((string) $datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }

    public function sanear_string($string)
    {
        $string = trim($string);
        $string = str_replace(array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string);
        $string = str_replace(array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string);
        $string = str_replace(array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string);
        $string = str_replace(array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string);
        $string = str_replace(array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string);
        $string = str_replace(array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string);
        $string = str_replace(array("\\", "¨", "º", "~", "#", "@", "|", "!", "\"", "·", "$", "%", "&", "/", "(", ")", "?", "'", "¡", "¿", "[", "^", "`", "]", "+", "}", "{", "¨", "´", ">", "< ", ";", ",", ":"), '', $string);
        return $string;
    }

    public function update_nota_entrega_nuevo($id_nota_entrega)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $nota_entrega = Nota_entrega::find($id_nota_entrega);
        $receta = hc_receta::find($nota_entrega->id_hc_receta);
        if (!is_null($receta)) {
            $detalle_receta = DB::table('hc_receta_detalle as rd')
                ->join('medicina_tarifario as mt', 'mt.id_medicina', 'rd.id_medicina')
                ->join('ap_procedimiento as ap', 'ap.id', 'mt.id_ap_procedimiento')
                ->where('rd.id_hc_receta', $receta->id)
                ->select('rd.*', 'ap.valor', 'ap.descripcion as nombre_medicina')->get();

            if ($nota_entrega->estado_generado == '0' && $nota_entrega->estado == '-1') {
                foreach ($detalle_receta as $det) {
                    $total = $det->cantidad * $det->valor;
                    $deta = Nota_entrega_detalle::where('id_medicina', $det->id_medicina)->where('id_nota_entrega', $nota_entrega->id)->first();
                    if (is_null($deta)) {
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
                }
                $nota_entrega = Nota_entrega::find($id_nota_entrega);
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
        return redirect()->route('nota_entrega.editar', ['id' => $receta->id]);
    }
}
