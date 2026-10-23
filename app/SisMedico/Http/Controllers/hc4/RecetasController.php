<?php

namespace Sis_medico\Http\Controllers\hc4;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Log;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_receta;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Medicina;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Empresa;

class RecetasController extends Controller
{

    //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
    private function rol_new($opcion)
    {
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

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

    //Rol Doctor
    private function rol_doctor()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(3)) == false) {
            return true;
        }
    }

    //FUNCION EDITAR
    //PERMITE EDITAR EL RP Y LA PRESCRIPCION Y AGREGAR UNA NUEVA NEDICINA
    //SOLO PARA DOCTORES

    public function editar($id_receta, $id_paciente)
    {

        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $hc_receta          = hc_receta::find($id_receta);
        $alergiasxpac       = Paciente_Alergia::where('id_paciente', $id_paciente)->get();
        $paciente           = Paciente::find($id_paciente);
        $id_seguro_medicina = Historiaclinica::where('hcid', $hc_receta->id_hc)->first();
        $doctor_1           = $id_seguro_medicina->id_doctor1;
        $doctores           = User::where('id_tipo_usuario', '=', 3)->where('id', '<>', '1234517896')->where('id', '<>', '4444444444')->where('id', '<>', '9666666666')->where('id', '<>', 'GASTRO')->where('estado', '=', 1)->orderby('apellido1')->get();

        return view('hc4/recetas/editar', ['hc_receta' => $hc_receta, 'alergiasxpac' => $alergiasxpac, 'id_paciente' => $id_paciente, 'paciente' => $paciente, 'id_seguro_medicina' => $id_seguro_medicina, 'doctores' => $doctores, 'doctor_1' => $doctor_1]);
    }

    //FUNCION HISTORIAL_RECETA_PACIENTE
    //MUESTRA EL HISTORIAL DE RECETAS DEL PACIENTE
    //SOLO PARA DOCTORES
    public function historial_receta_paciente($id_paciente)
    {
        //dd('entra1');
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $paciente = Paciente::find($id_paciente);

        $hist_recetas = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->where('h.id_paciente', $id_paciente)
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->orderBy('a.fechaini', 'desc')
            ->select('r.*', 'a.fechaini', 's.nombre', 'h.fecha_atencion')
            ->get();
        //dd($hist_recetas);

        return view('hc4/recetas/index', ['hist_recetas' => $hist_recetas, 'paciente' => $paciente]);
    }

    //FUNCION IMPRIME
    //PERMITE IMPRIMIR LA RECETA EN HOJA MEMBRETADA Y SIN MENBRETAR
    //SOLO PARA DOCTORES
    public function imprime($id, $tipo)
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $id_empresa  = session()->get('id_empresa');

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
        $paciente  = paciente::find($historia->id_paciente);
        $edad      = Carbon::parse($paciente->fecha_nacimiento)->age; // 1990-10-25
        $detalles  = hc_receta_detalle::where('id_hc_receta', $id)->get();
        $medicinas = Medicina::all();
        $cie10     = hc_cie10::where('hcid', $receta->id_hc)->get();

        $firma = null;

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
            //dd($firma);
            $view = \View::make('hc_admision.receta.menbretada', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'id_empresa', 'principioActivo', 'logo_emp'))->render();
        }
        if ($tipo == 1) {
            $view = \View::make('hc_admision.receta.sinmenbrete', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'id_empresa'))->render();
        }
        if ($tipo == 3) {
            $view = \View::make('hc_admision.receta.menbretadaCIR', compact('receta', 'historia', 'paciente', 'edad', 'detalles', 'medicinas', 'cie10', 'firma', 'id_empresa'))->render();
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('A4', 'landscape');
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        return $pdf->stream($historia->id_paciente . '_Receta_' . $id . '.pdf');
    }

    //FUNCION BUSCAR NOMBRE MEDICINA
    //MUESTRA TODAS LAS MEDICINAS QUE SE PODRAN AGREGAR LUEGO DE HABER
    //INGRESADO UN NOMBRE
    //SOLO PARA DOCTORES
    public function buscar_nombre(Request $request)
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $id_empresa  = session()->get('id_empresa');
        $tipo = 0;
        //dd($request->all());

        $nombre = $request['term'];
        $seguro = $request['seguro'];
        $tipo = $request['tipo'];
        $seteo  = '%' . $nombre . '%';
        $data   = null;

        if($tipo == 1){
            $query1 = Medicina::where('nombre', 'like', '%' . $nombre . '%')->where('estado', 1)->where('iess', '2')->orderBy('nombre')->get();
        }else{
            if ($seguro == '2' || $seguro == '3' || $seguro == '5' || $seguro == '7') {
                if($id_empresa == '1391927177001' || $id_empresa == '2390058384001'){
                    $query1 = Medicina::where('nombre', 'like', '%' . $nombre . '%')
                    ->where('estado', '1') 
                    ->where('iess', '2')
                    ->orderBy('nombre')->get(); 
                }else{
                    $query1 = Medicina::where(function ($query1) use ($seteo) {
                        $query1->where([['nombre', 'like', '%' . $seteo . '%'], ['estado', '=', '1']])
                            ->orWhere([['nombre_generico', 'like', '%' . $seteo . '%'], ['estado', '=', '1']]);
                    })->get();
                }  
            } else {
                    $query1 = Medicina::where(function ($query1) use ($seteo) {
                        $query1->where([['nombre', 'like', '%' . $seteo . '%'], ['estado', '=', '1']])
                            ->orWhere([['nombre_generico', 'like', '%' . $seteo . '%'], ['estado', '=', '1']]);
                    })->get();
                }
        }

        $nombre = $query1;

        /*foreach ($nombre_generico as $value) {
        $data[]=array('value'=>$value->nombre_generico, 'id'=>$value->id);
        }*/
        // foreach ($nombre as $value) {
        //     if (!is_null($value->nombre_generico)) {
        //         $data[] = array('value' => $value->nombre . ' (' . $value->nombre_generico . ')', 'id' => $value->id);
        //     } else {
        //         $data[] = array('value' => $value->nombre . ' ' . $value->presentacion, 'id' => $value->id);
        //     }
        // }

        if($seguro == '2' || $seguro == '3' || $seguro == '5' || $seguro == '7'){
            foreach ($nombre as $value) {
                if (!is_null($value->nombre_generico)) {
                    $data[] = array('value' => $value->nombre . ' ' . $value->nombre_generico . ' ', 'id' => $value->id);
                } else {
                    $data[] = array('value' => $value->nombre . ' ' . $value->presentacion, 'id' => $value->id);
                }
            }
        }else{
            foreach ($nombre as $value) {
                if (!is_null($value->nombre_generico)) {
                    $data[] = array('value' => $value->nombre . ' (' . $value->nombre_generico . ')', 'id' => $value->id);
                } else {
                    $data[] = array('value' => $value->nombre . ' ' . $value->presentacion, 'id' => $value->id);
                }
            }
        }

        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    //FUNCION BUSCAR NOMBRE2
    //PERMITE AGREGAR LA MEDICINA SELECCIONA A LOS CAMPOS RP Y PRESCRIPCION
    //SOLO PARA DOCTORES
    public function buscar_nombre2(Request $request)
    {
        //return $request->all();
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        if(Auth::user()->id == '9666666666'){
           // dd($request->all());
        }

        //dd($request['nombre_generico']);
        $nombre_medicina = $request['nombre_generico'];

        $data = null;

        $porciones = explode(" (", $nombre_medicina);
        //return $porciones;
        $nombre_medicina = $porciones[0];

        //return $nombre_medicina;
        if(Auth::user()->id == '9666666666'){
             //dd($nombre_medicina);
         }

        if ($nombre_medicina == null) {
            return '0';
        }

        $nombre = Medicina::WhereRaw("CONCAT(nombre, ' ', presentacion) LIKE '" . $nombre_medicina . "'")->where('estado', 1)->orderBy('nombre', 'asc')->get();
        if ($nombre == '[]') {
            $nombre = Medicina::where('nombre', 'like', $nombre_medicina)->where('estado', 1)->orderBy('nombre', 'asc')->get();
        }

        $nombre_generico = Medicina::WhereRaw("CONCAT(nombre_generico, ' ', presentacion) LIKE '" . $nombre_medicina . "'")->where('estado', 1)->orderBy('nombre_generico', 'asc')->get();
        if ($nombre_generico == '[]') {
            $nombre_generico = Medicina::where('nombre_generico', 'like', $nombre_medicina)->where('estado', 1)->orderBy('nombre', 'asc')->get();
        }

        if(Auth::user()->id == '9666666666'){
          //  dd($nombre_medicina, $nombre);
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

    //FUNCION UPDATE_RECETA_2
    //PERMITE ACTUALIZAR LA RECETA EDITADA
    //SOLO PARA DOCTORES
    public function update_receta_2(Request $request)
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        //return $request->all();

        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');

        $hist_recetas = null;
        if (!is_null($request['id_paciente'])) {
            $hist_recetas = DB::table('hc_receta as r')
                ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
                ->where('h.id_paciente', $request['id_paciente'])
                ->join('agenda as a', 'a.id', 'h.id_agenda')
                ->orderBy('a.fechaini', 'desc')
                ->select('r.*', 'a.fechaini')
                ->orderBy('r.id', 'desc')->get()->first();
        } else {
            $hist_recetas = DB::table('hc_receta as r')->where('r.id', $request['id_receta'])->first();
        }
        //dd($hist_recetas);
        if (!is_null($hist_recetas)) {

            $proc_id              = null;
            $id_procedimiento_new = hc_procedimientos::where('id_hc', $hist_recetas->id_hc)->first();
            if (!is_null($id_procedimiento_new)) {
                $proc_id = $id_procedimiento_new->id;
            }
            $hist_recetas_new = $hist_recetas;

            $receta_new = [
                'anterior'         => 'RECETA -> Rp:' . $hist_recetas_new->rp . ' Prescripcion:' . $hist_recetas_new->prescripcion,
                'nuevo'            => 'RECETA -> Rp:' . $request["rp"] . ' Prescripcion:' . $request["prescripcion"],
                'hc_id'            => $hist_recetas_new->id_hc,
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $proc_id,
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($receta_new);
        }

        $input1 = [
            'id_doctor_examinador' => $request["id_doctor_examinador"],
            'prescripcion'         => $request["prescripcion"],
            'rp'                   => $request["rp"],
            'ip_modificacion'      => $ip_cliente,
            'id_usuariomod'        => $idusuario,
        ];

        hc_receta::where('id', $request["id_receta"])->update($input1);
        return $request;
    }

    //FUNCION CREAR DETALLE
    //SOLO PARA DOCTORES
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
                if (!is_null($medicina->nombre_generico)) {
                    $nombre_g = $medicina->nombre . ' (' . $medicina->nombre_generico . ')';
                } else {
                    $nombre_g = $medicina->nombre . ' ' . $medicina->presentacion;
                }
                hc_receta_detalle::create([
                    'id_hc_receta' => $receta,
                    'id_medicina'  => $medicina->id,
                    'nombre'       => $nombre_g,     
                    'cantidad'     => 1,
                    'dosis'        => $medicina->dosis,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario    

                ]);
                //return 1;
            } else {

                $mensaje = "PACIENTE ES ALERGICO A LA MEDICINA SOLICITADA";
            }

            //return "creo";
        } else {
            $mensaje = "MEDICINA YA INGRESADA";
        }

        $detalles  = hc_receta_detalle::where('id_hc_receta', $receta)->get();
        $medicinas = Medicina::where('estado', 1)->get();

        return view('hc4/recetas/detalle', ['detalles' => $detalles, 'medicinas' => $medicinas, 'receta' => $receta, 'mensaje' => $mensaje, 'id_receta' => $receta]);
    }

    public function receta_detalle_index( $id_receta ){
        $receta    = hc_receta::find( $id_receta );
        $detalles  = $receta->detalles;
        $medicinas = Medicina::where('estado', 1)->get();
        $mensaje   = '';
        return view('hc4/recetas/detalle', ['detalles' => $detalles, 'medicinas' => $medicinas, 'receta' => $receta, 'mensaje' => $mensaje, 'id_receta' => $id_receta]);    
    }    
    //FUNCION EDITAR DETALLE
    //SOLO PARA DOCTORES
    public function editar_detalle(Request $request, $id)
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
                'detalle_cantidad'   => $request->detalle_cantidad,
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

        return view('hc4/recetas/detalle', ['detalles' => $detalles, 'medicinas' => $medicinas, 'receta' => $receta, 'mensaje' => $mensaje, 'id_receta' => $receta]); 
    }

    //FUNCION AGREGAR NUEVA RECETA
    //PERMITE CREAR UNA NUEVA RECETA Y AGREGAR UNA NUEVA MEDICINA
    //EN LOS CAMPOS RP Y PRESCRIPCION
    //SOLO PARA DOCTORES
    public function agregar_nueva_receta($id_paciente)
    {
        /*$opcion = '2';
        if($this->rol_new($opcion)){
        return redirect('/');
        }*/
        $empresa = Empresa::where('prioridad', 1)->first();
        $paciente   = Paciente::find($id_paciente);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        if ($this->rol_doctor()) {
            return response()->view('errors.404');
        }

        $evoluciones = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
            ->where('hc_evo.secuencia', 0)
            ->whereNotNull('hc_evo.cuadro_clinico')
            ->orderby('hc_evo.updated_at', 'desc')
            //->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            //->where('hc_proto.tipo_procedimiento', '0')
            ->select('hc_evo.*')
            ->first();

        $x_diagnosticos = null;
        $texto          = "";

        if (!is_null($evoluciones)) {
            $x_diagnosticos = DB::table('hc_cie10')
                ->where('hc_cie10.hc_id_procedimiento', $evoluciones->hc_id_procedimiento)
                ->groupBy('cie10')
                ->get();
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

                $texto = $texto . '<div class="cie10-receta">' . $value->cie10 . ':' . trim($descripcion) . '</div>';
            }
        }
        //dd($texto);

        $id_doctor = Auth::user()->id;

        $especialidad = DB::table('user_espe as u_es')->where('u_es.usuid', $id_doctor)->get()->first();

        if (!is_null($especialidad)) {
            $espid = $especialidad->espid;
        } else {
            $espid = '4';
        }

        $receta_new = [
            'anterior'        => 'DATOS PRINCIPALES -> El Dr. creo nueva receta',
            'nuevo'           => 'DATOS PRINCIPALES -> El Dr. creo nueva receta',
            'id_paciente'     => $paciente->id,
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
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
            //'id_seguro'       => $paciente->id_seguro,
            'id_seguro'       => '1',
            'estado'          => '4',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $id_doctor,
            'id_usuariomod'   => $id_doctor,
        ];

        $id_agenda = agenda::insertGetId($input_agenda);

        $input_historia = [

            'parentesco'      => $paciente->parentesco,
            'id_usuario'      => $paciente->id_usuario,
            'id_agenda'       => $id_agenda,
            'id_paciente'     => $id_paciente,
            //'id_seguro'       => $paciente->id_seguro,
            'id_seguro'       => '1',
            'id_doctor1'      => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $id_doctor,
            'ip_creacion'     => $ip_cliente,

        ];

        $id_historia = Historiaclinica::insertGetId($input_historia);
        $idusuario   = $id_doctor;

        $input_hc_receta = [
            'id_hc'           => $id_historia,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'rp'              => $texto,
        ];

        hc_receta::insert($input_hc_receta);

        $alergiasxpac = Paciente_Alergia::where('id_paciente', $id_paciente)->get();

        $hc_receta = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->where('h.id_paciente', $id_paciente)
            ->select('r.*', 'a.fechaini', 's.id as id_seguro', 's.nombre as nombre_seguro')
            ->get()->last();
        //dd($hc_receta->nombre_seguro);
        $publica = '0';

        return view('hc4/recetas/nueva_receta', ['alergiasxpac' => $alergiasxpac, 'paciente' => $paciente, 'id_paciente' => $id_paciente, 'hc_receta' => $hc_receta, 'publica' => $publica]);
    }

    public function agregar_nueva_receta_fun($id_paciente, $id_hc)
    {
        /*$opcion = '2';
        if($this->rol_new($opcion)){
        return redirect('/');
        }*/

        $paciente   = Paciente::find($id_paciente);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        if ($this->rol_doctor()) {
            return response()->view('errors.404');
        }

        $evoluciones = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
            ->where('hc_evo.secuencia', 0)
            ->whereNotNull('hc_evo.cuadro_clinico')
            ->orderby('hc_evo.updated_at', 'desc')
        //->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
        //->where('hc_proto.tipo_procedimiento', '0')
            ->select('hc_evo.*')
            ->first();

        $x_diagnosticos = null;
        $texto          = "";

        if (!is_null($evoluciones)) {
            $x_diagnosticos = DB::table('hc_cie10')
                ->where('hc_cie10.hc_id_procedimiento', $evoluciones->hc_id_procedimiento)
                ->groupBy('cie10')
                ->get();
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

                $texto = $texto . '<div class="cie10-receta">' . $value->cie10 . ':' . trim($descripcion) . '</div>';

            }

        }
        //dd($texto);

        $id_doctor = Auth::user()->id;

        $especialidad = DB::table('user_espe as u_es')->where('u_es.usuid', $id_doctor)->get()->first();

        if (!is_null($especialidad)) {
            $espid = $especialidad->espid;
        } else {
            $espid = '4';
        }

        /*
        $receta_new = [
            'anterior'        => 'DATOS PRINCIPALES -> El Dr. creo nueva receta',
            'nuevo'           => 'DATOS PRINCIPALES -> El Dr. creo nueva receta',
            'id_paciente'     => $paciente->id,
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
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
            'id_usuariocrea'  => $id_doctor,
            'id_usuariomod'   => $id_doctor,
        ];

        $id_agenda = agenda::insertGetId($input_agenda);

        $input_historia = [

            'parentesco'      => $paciente->parentesco,
            'id_usuario'      => $paciente->id_usuario,
            'id_agenda'       => $id_agenda,
            'id_paciente'     => $id_paciente,
            'id_seguro'       => $paciente->id_seguro,
            'id_doctor1'      => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $id_doctor,
            'ip_creacion'     => $ip_cliente,

        ];

        $id_historia = Historiaclinica::insertGetId($input_historia); */
        $idusuario   = $id_doctor;

        $input_hc_receta = [
            'id_hc'           => $id_hc,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'rp'              => $texto,
        ];

        hc_receta::insert($input_hc_receta);

        $alergiasxpac = Paciente_Alergia::where('id_paciente', $id_paciente)->get();

        $hc_receta = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->where('h.id_paciente', $id_paciente)
            ->select('r.*', 'a.fechaini', 's.id as id_seguro', 's.nombre as nombre_seguro')
            ->get()->last();
        //dd($hc_receta->nombre_seguro);
        $publica = '0';

        return view('hc4/recetas/nueva_receta', ['alergiasxpac' => $alergiasxpac, 'paciente' => $paciente, 'id_paciente' => $id_paciente, 'hc_receta' => $hc_receta, 'publica' => $publica]);
    }

    public function agregar_nueva_receta_publica($id_paciente)
    {
        $paciente   = Paciente::find($id_paciente);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $empresa = Empresa::where('prioridad', 1)->first();
        if ($this->rol_doctor()) {
            return response()->view('errors.404');
        }
        $evoluciones = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
            ->where('hc_evo.secuencia', 0)
            ->whereNotNull('hc_evo.cuadro_clinico')
            ->orderby('hc_evo.updated_at', 'desc')
        //->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
        //->where('hc_proto.tipo_procedimiento', '0')
            ->select('hc_evo.*')
            ->first();
        $x_diagnosticos = null;
        $texto          = "";
        if (!is_null($evoluciones)) {
            $x_diagnosticos = DB::table('hc_cie10')
                ->where('hc_cie10.hc_id_procedimiento', $evoluciones->hc_id_procedimiento)
                ->groupBy('cie10')
                ->get();
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
                $texto = $texto . '<div class="cie10-receta">' . $value->cie10 . ':' . trim($descripcion) . '</div>';
            }
        }
        //dd($texto);
        $id_doctor = Auth::user()->id;
        $especialidad = DB::table('user_espe as u_es')->where('u_es.usuid', $id_doctor)->get()->first();
        if (!is_null($especialidad)) {
            $espid = $especialidad->espid;
        } else {
            $espid = '4';
        }
        $receta_new = [
            'anterior'        => 'DATOS PRINCIPALES -> El Dr. creo nueva receta',
            'nuevo'           => 'DATOS PRINCIPALES -> El Dr. creo nueva receta',
            'id_paciente'     => $paciente->id,
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
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
            'id_seguro'       => 2,
            'estado'          => '4',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $id_doctor,
            'id_usuariomod'   => $id_doctor,
        ];
        $id_agenda = agenda::insertGetId($input_agenda);
        $input_historia = [
            'parentesco'      => $paciente->parentesco,
            'id_usuario'      => $paciente->id_usuario,
            'id_agenda'       => $id_agenda,
            'id_paciente'     => $id_paciente,
            'id_seguro'       => 2,
            'id_doctor1'      => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $id_doctor,
            'ip_creacion'     => $ip_cliente,
        ];
        $id_historia = Historiaclinica::insertGetId($input_historia);
        $idusuario   = $id_doctor;
        $input_hc_receta = [
            'id_hc'           => $id_historia,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'rp'              => $texto,
        ];
        hc_receta::insert($input_hc_receta);
        $alergiasxpac = Paciente_Alergia::where('id_paciente', $id_paciente)->get();
        $hc_receta = DB::table('hc_receta as r')
            ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->where('h.id_paciente', $id_paciente)
            ->select('r.*', 'a.fechaini', 's.id as id_seguro', 's.nombre as nombre_seguro')
            ->get()->last();
        //dd($hc_receta->nombre_seguro);
        $publica = '1';
        return view('hc4/recetas/nueva_receta', ['alergiasxpac' => $alergiasxpac, 'paciente' => $paciente, 'id_paciente' => $id_paciente, 'hc_receta' => $hc_receta, 'publica' => $publica]);
    }

    //PERMITE VOLVER A LA VISTA HISTORIAL DE RECETAS LUEGO DE HABER DADO A
    //LA OPCION  GUARDAR
    //SOLO PARA DOCTORES
    public function actua_receta(Request $request)
    {
        //dd('entra');
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $paciente = Paciente::find($request['id_paciente']);

        $hist_recetas = DB::table('hc_receta as r')->join('historiaclinica as h', 'r.id_hc', 'h.hcid')->where('r.id', $request['id_receta'])->join('agenda as a', 'a.id', 'h.id_agenda')->orderBy('a.fechaini', 'desc')->select('r.*', 'a.fechaini')->orderBy('r.id', 'desc')->get()->first();

        return view('hc4/recetas/unico', ['hist_recetas' => $hist_recetas]);
    }
}
