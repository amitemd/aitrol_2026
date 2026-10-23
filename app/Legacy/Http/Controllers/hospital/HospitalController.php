<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Hospital_Producto;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Hospital_Log_Movimiento;
use Sis_medico\Habitacion;
use Illuminate\Pagination\Paginator;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Ingreso_emer_008;
use Sis_medico\Cama;
use Sis_medico\Piso;
use Sis_medico\AgendaQ;
use Sis_medico\Quirofano;
use Sis_medico\User;
use Sis_medico\Paciente;
use Sis_medico\Pais;
use Sis_medico\Seguro;
use Sis_medico\Imagen;
use Sis_medico\CamaTransaccion;
use Sis_medico\Hospital_Emergencia;
use Sis_medico\Hospital_Bodega;
use Sis_medico\Examen;
//use Sis_medico\Hospital;
use Sis_medico\Firma_Usuario;
use Sis_medico\CamaPaciente;
use Sis_medico\Formulario005;
use Sis_medico\Evolucion_005;
use Sis_medico\Diagnostico_005;
use Sis_medico\Medidas_generales;
use Sis_medico\Tratamiento_005;
use Sis_medico\Plan_005;
use Sis_medico\Hospital_Salas;
use Sis_medico\Hc_Cie10;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Empresa;
use Sis_medico\Especialidad;
use Sis_medico\hc_child_pugh;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Archivos_Solicitud;
use Sis_medico\Ho_Datos_Paciente;
use Sis_medico\Ho_Hc_Receta_Evolucion;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Hospital;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Triaje_Manchester;
use Sis_medico\HoEstadoPaso;
use Sis_medico\Http\Requests\Request as RequestsRequest;
use Sis_medico\Log_Agenda;
use Sis_medico\Log_usuario;
use Sis_medico\Medicina;
use Sis_medico\Procedimiento;
use Sis_medico\Sala;
use Sis_medico\User_espe;
use Sis_medico\Ho_estado_paso;
use Sis_medico\Examen_Orden;
use Sis_medico\Ho_Form008;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\procedimiento_completo;
use Sis_medico\Subseguro;
use Sis_medico\Planilla;
use Sis_medico\Insumo_Plantilla_Control;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\LogReciboDeCobro;
use Sis_medico\Convenio; 
use Sis_medico\CtCajaCobro;
use Sis_medico\SeguroTipos;
use Sis_medico\Comisiones_Gastro;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_Orden_Venta_Detalle;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\PrecioProducto;
use Sis_medico\Ct_productos;



class HospitalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }
        $fechahoy = date("Y-m-d");
        //$otrointento = AgendaQ::where("estado","1")->whereBetween('fechaini', [$fechahoy.' 00:00:00', $fechahoy.' 23:59:59'])->get();
        /*$otrointento = AgendaQ::where("estado","1")->where('fechaini',$fechahoy)->get();
        $totales=0;
        if($otrointento=="[]"){
            $otrointento= 0;
        }else {
            $totales= $otrointento->count();
        }*/

        /*$agendaprogramadas= AgendaQ::where('estado','=','1')->get();

        $query = "SELECT SUBSTRING(fechaini,1,9) as fecha
                 FROM `agenda_quirofano` WHERE fecha.fecha ";

        $datesubstring = DB::select($query);
        $totales= count($);*/
        $nombres = DB::table('cama_paciente')
            ->join('paciente', 'paciente.id', '=', 'cama_paciente.id_paciente')
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'cama_paciente.id_paciente', 'cama_paciente.created_at', 'cama_paciente.id_cama')
            ->get();
        //dd($nombres);
        //$inicio = CamaPaciente::all();
        $totales = AgendaQ::where('estado', '=', '1')->where('fecha_total', '=', $fechahoy)->count();

        $doctores       = User::where('id_tipo_usuario', 3)->where('estado', 1)->orderby('apellido1')->get();
        $seguros        = Seguro::where('inactivo', '1')->get();
        $especialidades = Especialidad::where('estado', '1')->get();
        return view('hospital/index', ['totales' => $totales, 'nombres' => $nombres, 'doctores' => $doctores, 'seguros' => $seguros, 'especialidades' => $especialidades, 'id_especialidad' => null, 'id_doctor1' => null, 'id_seguro' => null, 'request' => $request]);
    }
    public function farmacia()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $farmacia = Hospital_Producto::paginate(5);
        return view('hospital/farmacia', ['farmacia' => $farmacia]);
    }
    /*public function iniciobusca(Request $request){
    $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }

        if(($request->nombre1)!=""){
            $nombres= CamaPaciente::where("id_paciente","like",$request->nombre1."%")->get();
        }
        elseif(($request->apellido1)!=""){
            $nombres= CamaPaciente::where("id_cama","like",$request->apellido1."%")->get();    
        }
    return view('hospital/iniciobusca',['nombres'=>$nombres]);
    }*/
    public function buscadorfarmacia(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        if (($request->codigo) != "") {
            $farmacia = Hospital_Producto::where("codigo", "like", $request->codigo . "%")->get();
        } elseif (($request->nombre) != "") {
            $farmacia = Hospital_Producto::where("nombre", "like", $request->nombre . "%")->get();
        }

        return view('hospital/buscadorfarmacia', ['farmacia' => $farmacia]);
    }
    public function agregarp()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        return view('hospital/agregarp');
    }
    public function quirofano()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $agendados = DB::table('agenda_quirofano')
            ->join('users', 'users.id', '=', 'agenda_quirofano.id_doctor')
            ->join('paciente', 'paciente.id', '=', 'agenda_quirofano.id_paciente')
            ->select('users.nombre1', 'users.apellido1', 'agenda_quirofano.fechaini', 'agenda_quirofano.fechafin', 'agenda_quirofano.observaciones', 'paciente.nombre2', 'paciente.apellido2')
            ->paginate(5);
        //dd($agendados);
        //$paciente=hospital::with('nombre')->get();
        //$paciente=Paciente::find(1)->paciente:
        $quirofano = Quirofano::all();
        $agenda = AgendaQ::paginate(5);
        //dd($quirofano);
        return view('hospital/quirofano/quirofano', ['quirofano' => $quirofano, 'agenda' => $agenda, 'agendados' => $agendados]);
    }
    public function modalq()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        return view('hospital/quirofano/modalq');
    }
    public function resultado(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        $medico = $request->get('medico');
        $codigo = $request->get('codigo');
        $nota_evolucion = $request->get('nota_evolucion');

        $prescri = Evolucion_005::where('id_paciente', '=', $id_paciente)
            ->medico($medico)
            ->codigo($codigo)
            ->nota_evolucion($nota_evolucion)
            ->paginate(5);
        return view('hospital/emergencia/resultado', ['prescri' => $prescri, 'id_paciente' => $id_paciente, 'users' => $users]);
    }
    public function modaleditar($id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $campo = Evolucion_005::findOrFail($id);
        $editar = Evolucion_005::where('id', '=', $id)->paginate(5);
        return view('hospital/emergencia/modaleditar', ['editar' => $editar, 'id' => $id, 'campo' => $campo]);
    }
    public function registro()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        return view('hospital/emergencia/registro');
    }
    public function vistac()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $id_tipo = 1;
        $muestra = Hospital::where('id_tipo', $id);
        return view('hospital/vistac', ['muestra' => $muestra]);
    }
    public function questionario($id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $formnularioid = paciente::find($id);
        // dd($formnularioid);
        return view('hospital/emergencia/questionario', ['formnularioid' => $formnularioid]);
    }
    public function modalprescripcion()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        return view('hospital/modalprescripcion');
    }
    public function modalplan()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return
                redirect('/');
        }
        $plan = Plan_005::paginate(5);
        return view('hospital/emergencia/modalplan', ['plan' => $plan]);
    }
    public function emergencia()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $firma = firma_usuario::all();
        $cama1 = cama::all();
        $seguro = seguro::all();
        $examenes = examen::all();
        $paciente = paciente::paginate(20);
        $user = User::all();
        //dd($paciente);
        $hospital_emergencia = DB::table('hospital_emergencia as a')
            //->where('a.tipo', '2')
            //->join('examen','hospital_emergencia.id_examen','=','id')
            ->join('examen as b', 'b.id', 'a.id_examen')
            ->select('a.id_examen')
            ->get();
        $milo = DB::table('hospital_emergencia as a')
            ->join('firma_usuario as b', 'b.id', 'a.id_nombre')
            ->select('a.id_nombre')
            ->get();


        return view('hospital/emergencia/emergencia', ['paciente' => $paciente, 'firma' => $firma, 'examenes' => $examenes, 'user' => $user, 'cama1' => $cama1, 'milo' => $milo, 'seguro' => $seguro]);
    }
    /*public function buscadore(Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        if(($request->nombre1)!=""){
            $paciente= paciente::where("nombre1","like",$request->nombre1."%")->get();
        }
      
        elseif(($request->apellido1)!=""){
            $paciente= paciente::where("apellido1","like",$request->apellido1."%")->get();    
        }
        
        return view('hospital/emergencia/buscadore',['paciente'=>$paciente]);
    }*/
    public function autocompletar(Request $request)
    {
        $nombre_encargado = $request['term'];
        $data             = null;
        $nuevo_nombre     = explode(' ', $nombre_encargado);
        $seteo            = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM `paciente`
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' 
                  ";
        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo, 'id' => $product->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
        return $productos;
    }
    public function autocompletar2(Request $request)
    {
        $nombre_encargado = $request['apellido'];
        $data  = null;
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, fecha_nacimiento 
                FROM paciente
                WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "'";
        $nombres = DB::select($query);
        if ($nombres != array()) {
            $data = $nombres[0]->fecha_nacimiento;
            return $data;
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function autocompletar3(Request $request)
    {
        $nombre_encargado = $request['apellido'];
        $data  = null;
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, historia_clinica
                    FROM paciente
                    WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "'";
        $nombres = DB::select($query);
        if ($nombres != array()) {
            $data = $nombres[0]->historia_clinica;
            return $data;
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function autocompletar4(Request $request)
    {
        $nombre_encargado = $request['apellido'];
        $data  = null;
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id_seguro,observacion,gruposanguineo,referido,antecedentes_fam,antecedentes_pat,id
                FROM paciente
                WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "'";
        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo, 'seguro' => $product->id_seguro, 'observacion' => $product->observacion, 'gruposanguineo' => $product->gruposanguineo, 'referido' => $product->referido, 'antecedentes_fam' => $product->antecedentes_fam, 'antecedentes_pat' => $product->antecedentes_pat, 'id' => $product->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
        return $productos;
    }
    public function registrome(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        $hola  = Auth::user()->cedula;
        Hospital_Emergencia::create([
            'apellidos' => $request['apellido'],
            'anotaciones' => $request['anotaciones'],
            'id_examen' => $request['nameid'],
            'id_nombre' => $request['id_tipo'],
            'apellidos' => $request['apellido'],
            'id_usuariocrea' => $idusuario1,
            'ip_creacion' => $ip_clientes,
            'id_usuariomod' => $idusuario1,

        ]);
        return back();
    }

    public function agregarpa()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        // $paises = Pais::all();
        // $seguros = Seguro::all();

        // return view('hospital/emergencia/agregarpa', ['paises'=>$paises, 'seguros'=>$seguros]);
        return view('hospital/emergencia/agregarpa');
    }

    // public function registropac(Request $request){
    //     $opcion = '1';
    //     if ($this->rol_new($opcion)) {
    //         return redirect('/');
    //     }
    //     $ip_cliente = $_SERVER["REMOTE_ADDR"];
    //     $idusuario  = Auth::user()->id;

    //     Paciente::create([
    //         'id' => $request['id_usuario'],
    //         'id_seguro' => $request['id_seguro'],
    //         'nombre1' => $request['nombre1'],
    //         'nombre2' => $request['nombre2'],
    //         'apellido1' => $request['apellido1'],
    //         'apellido2' => $request['apellido2'],
    //         'telefono1' => $request['telefono1'],
    //         'telefono2' => $request['telefono2'],
    //         'id_pais' => $request['id_pais'],
    //         'fecha_nacimiento' => $request['fecha_nacimiento'],
    //         'email' => $request['email'],
    //     ]);
    //     return back();
    // }

    public function registropac(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        date_default_timezone_set('America/Guayaquil');
        $bandera = 0;
        $id      = $request['cedula'];

        $user = User::find($id);

        // Redirect to user list if updating user wasn't existed
        if (!is_null($user)) {

            $this->validateInput2($request);
            Paciente::create([
                'id'                 => $request['cedula'],
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'telefono1'          => '1',
                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'id_pais'            => '1',
                'tipo_documento'     => 1,
                'imagen_url'         => ' ',
                'menoredad'          => '0',
                'id_seguro'          => '1',
                'id_usuario'         => $request['cedula'],
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'fecha_nacimiento'   => $request['fecha_nacimiento'],
            ]);

            Cortesia_paciente::create([
                'id'              => $request['cedula'],
                'cortesia'        => $request['cortesia'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);

            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['cedula'],
                'dato1'       => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
            ]);
        } else {

            $this->validateInput($request);

            User::create([
                'id'               => $request['cedula'],
                'nombre1'          => strtoupper($request['nombre1']),
                'nombre2'          => strtoupper($request['nombre2']),
                'apellido1'        => strtoupper($request['apellido1']),
                'apellido2'        => strtoupper($request['apellido2']),
                'telefono1'        => '1',
                'id_pais'          => '1',
                'id_tipo_usuario'  => 2,
                'email'            => $request['email'],
                'password'         => bcrypt($request['cedula']),
                'tipo_documento'   => 1,
                'estado'           => 1,
                'imagen_url'       => ' ',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
                'fecha_nacimiento' => $request['fecha_nacimiento'],
            ]);
            paciente::create([
                'id'                 => $request['cedula'],
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'telefono1'          => '1',

                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'id_pais'            => '1',
                'tipo_documento'     => 1,
                'imagen_url'         => ' ',
                'menoredad'          => '0',
                'id_seguro'          => '1',
                'id_usuario'         => $request['cedula'],
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'fecha_nacimiento'   => $request['fecha_nacimiento'],
            ]);
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['cedula'],
                'dato1'       => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
            ]);
        }

        $paciente = Paciente::find($id);

        if ($request->cortesia == 'SI') {
            $cortesia_paciente = Cortesia_paciente::find($id);
            if (is_null($cortesia_paciente)) {
                Cortesia_paciente::create([
                    'id'              => $id,
                    'cortesia'        => 'SI',
                    'ilimitado'       => 'NO',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }
        }

        return $paciente->id;
    }

    private function validateInput2($request)
    {

        $rules = [

            'cedula'           => 'required|max:10|unique:paciente,id',
            'nombre1'          => 'required|max:60',
            'nombre2'          => 'required|max:60',
            'apellido1'        => 'required|max:60',
            'apellido2'        => 'required|max:60',
            'fecha_nacimiento' => 'required',

        ];

        $messages = [
            'parentesco.required'        => 'Selecciona el parentesco.',
            'parentesco.in'              => 'Debe seleccionar entre Padre/Madre,Conyugue,Hijo(a).',
            'id_seguro.required'         => 'Selecciona el seguro.',
            'cedula.required'            => 'Agrega la cédula.',
            'cedula.max'                 => 'La cédula no puede ser mayor a :max caracteres.',
            'cedula.unique'              => 'Cedula ya se encuentra registrada a un paciente.',
            'nombre1.required'           => 'Agrega el primer nombre.',
            'nombre2.required'           => 'Agrega el segundo nombre.',
            'nombre1.max'                => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'                => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'         => 'Agrega el primer apellido.',
            'apellido1.max'              => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono12.required'        => 'Agrega el teléfono del domicilio.',
            'telefono12.numeric'         => 'El teléfono de domicilio debe ser numérico.',
            'telefono12.max'             => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono22.required'        => 'Agrega el teléfono celular.',
            'telefono22.numeric'         => 'El teléfono celular debe ser numérico.',
            'telefono22.max'             => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais2.required'          => 'Selecciona el pais.',
            'fecha_nacimiento2.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento2.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'menoredad.in'               => 'El Asegurado Principal no puede ser menor de edad.',
        ];

        $this->validate($request, $rules, $messages);
    }

    private function validateInput($request)
    {
        $rules = [

            'cedula'           => 'required|max:10|unique:users',
            'nombre1'          => 'required|max:60',
            'nombre2'          => 'required|max:60',
            'apellido1'        => 'required|max:60',
            'apellido2'        => 'required|max:60',
            'email'            => 'required|email|max:191|unique:users',
            'cedula'           => 'required|max:10|unique:paciente,id',
            'fecha_nacimiento' => 'required',

        ];

        $messages = [
            'parentesco.required'        => 'Selecciona el parentesco.',
            'parentesco.in'              => 'Debe seleccionar Ninguno.',
            'id.required'                => 'Agrega la cédula.',
            'id.max'                     => 'La cédula no puede ser mayor a :max caracteres.',
            'id.unique'                  => 'Cedula ya se encuentra registrada.',
            'nombre1.required'           => 'Agrega el primer nombre.',
            'nombre2.required'           => 'Agrega el segundo nombre.',
            'nombre1.max'                => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'                => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'         => 'Agrega el primer apellido.',
            'apellido1.max'              => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono1.required'         => 'Agrega el teléfono del domicilio.',
            'telefono1.numeric'          => 'El teléfono de domicilio debe ser numérico.',
            'telefono1.max'              => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono2.required'         => 'Agrega el teléfono celular.',
            'telefono2.numeric'          => 'El teléfono celular debe ser numérico.',
            'telefono2.max'              => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais.required'           => 'Selecciona el pais.',
            'fecha_nacimiento.required'  => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento.date'      => 'La fecha de nacimiento tiene formato incorrecto.',
            'email.required'             => 'Agrega el Email.',
            'email.email'                => 'El Email tiene error en el formato.',
            'email.max'                  => 'El Email no puede ser mayor a :max caracteres.',
            'email.unique'               => 'el Email ya se encuentra registrado.',
            'id_seguro.required'         => 'Selecciona el seguro.',
            'cedula.required'            => 'Agrega la cédula.',
            'cedula.max'                 => 'La cédula no puede ser mayor a :max caracteres.',
            'cedula.unique'              => 'Cedula ya se encuentra registrada a un paciente.',
            'nombre1.required'           => 'Agrega el primer nombre.',
            'nombre1.max'                => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'                => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'         => 'Agrega el primer apellido.',
            'apellido1.max'              => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono12.required'        => 'Agrega el teléfono del domicilio.',
            'telefono12.numeric'         => 'El teléfono de domicilio debe ser numérico.',
            'telefono12.max'             => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono22.required'        => 'Agrega el teléfono celular.',
            'telefono22.numeric'         => 'El teléfono celular debe ser numérico.',
            'telefono22.max'             => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais2.required'          => 'Selecciona el pais.',
            'fecha_nacimiento2.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento2.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'menoredad.in'               => 'El Asegurado Principal no puede ser menor de edad.',
        ];

        //return $rules;
        $this->validate($request, $rules, $messages);
    }

    public function formulario05(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $cie = Hc_Cie10::all();
        //dd($cie);
        $servicio = Evolucion_005::where('id_paciente', '=', 'id_paciente')->get();
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'paciente.fecha_nacimiento', 'paciente.sexo', 'ingreso_emer_008.id')
            ->get();
        //dd($users);
        return view('hospital/emergencia/formulario05', ['id_paciente' => $id_paciente, 'servicio' => $servicio, 'users' => $users, 'cie' => $cie]);
    }
    public function medicamentos(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        Formulario005::create([
            'id_usuariocrea' => $idusuario1,
            'ip_creacion' => $ip_clientes,
            'id_usuariomod' => $idusuario1,
            'medicamento' => $request['medicamento'],
            'posologia' => $request['posologia'],
            'indicaciones_medicinas' => $request['indicaciones_medi'],
            'cantidad_medicinas' => $request['cantidad'],
            'nombre_medicina' => $request['nombre_medicina'],
            'presentacion_medicamento' => $request['presentacion_medicamento'],
            'concentracion_medicamento' => $request['concentracion_medicamento'],
            'dosis_medicamento' => $request['dosis_medicamento'],
            'unidad_medicamento' => $request['unidad_medicamento'],
            'via_medicamento' => $request['via_medicamento'],
            'frecuencia_medicamento' => $request['frecuencia_medicamento'],
            'duracion_medicamento' => $request['duracion_medicamento'],
            'id_paciente' => $request['id_paciente'],
        ]);

        return back()->with('ok', 'Guardado con exito:');
    }

    public function formuarioevolucion(Request $request)
    {

        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;

        $this->validate($request, [

            'medico' => 'required|max:255',
            'examen_fisico' => 'required|max:255',
            'nota_de_evolucion' => 'required|max:255',
            'codigo' => 'required|max:255',

        ]);

        Evolucion_005::create([

            'id_usuariocrea'    =>  $idusuario1,
            'ip_creacion'       =>  $ip_clientes,
            'id_usuariomod'     =>  $idusuario1,
            'no_evolucion'      =>  $request['no_evolucion'],
            'medico'            =>  $request['medico'],
            'codigo'            =>  $request['codigo'],
            'nota_evolucion'    =>  $request['nota_de_evolucion'],
            'fecha_evolucion'   =>  $request['fecha_evolucion'],
            'examen_fisico'     =>  $request['examen_fisico'],
            'id_paciente'       =>  $request['id_paciente'],
        ]);

        return back()->with('success', 'Guardado con exito:');
    }

    public function diagnostico005(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        //$data=request::all();
        //dd($request->all());

        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;

        if (count($request->tipo) > 0) {
            foreach ($request->tipo as $item => $v) {

                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fecha_diagnostico' => $request->fecha_diagnostico[$item],
                    'cie_diagnostico' => $request->cie[$item],
                    'operacion_diagnostico' => $request->operacion[$item],
                    'tipo_diagnostico' => $request->tipo[$item],
                    'medico_diagnostico' => $request->medico_urgente[$item]
                );

                Diagnostico_005::insert($data2);
            }
        }
        return redirect()->back()->with('successo', 'Guardado Doc');
    }

    public function medidas_generales(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        if (count($request->medico_general) > 0) {
            foreach ($request->medico_general as $item => $v) {
                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fecha_generales' => $request->fecha_generales[$item],
                    'medico_general' => $request->medico_general[$item],
                    'descripcion_general' => $request->descripcion_general[$item],
                );
                Medidas_generales::insert($data2);
            }
        }
        return redirect()->back()->with('exito', 'Guardado Doc');
    }

    public function salas(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        //dd($request->all());
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        if (count($request->medicina_salas) > 0) {
            foreach ($request->medicina_salas as $item => $v) {
                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fecha_salas' => $request->fecha_salas[$item],
                    'area_salas' => $request->area_salas[$item],
                    'medicina_salas' => $request->medicina_salas[$item],
                    'descripcion_salas' => $request->descripcion_salas[$item],
                    'medico_salas' => $request->medico_salas[$item],
                );
                Hospital_Salas::insert($data2);
            }
        }
        return redirect()->back()->with('dato', 'Guardado Doc');
    }
    public function tratamiento(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        if (count($request->medico_tratamiento) > 0) {
            foreach ($request->medico_tratamiento as $item => $v) {
                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fechatratamiento' => $request->fechatratamiento[$item],
                    'medico_tratamiento' => $request->medico_tratamiento[$item],
                    'descripcion_tratamiento' => $request->descripcion_tratamiento[$item],
                );
                Tratamiento_005::insert($data2);
            }
        }
        return redirect()->back()->with('listo', 'Guardado Doctor!!');
    }
    public function plan(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        if (count($request->descripcion_plan) > 0) {
            foreach ($request->descripcion_plan as $item => $v) {
                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fechaplan' => $request->fechaplan[$item],
                    'medico_plan' => $request->medico_plan[$item],
                    'descripcion_plan' => $request->descripcion_plan[$item],
                );
                Plan_005::insert($data2);
            }
        }
        return redirect()->back()->with('validado', 'Guardado Doctor!!');
    }

    public function editarevolucion(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $campo = Evolucion_005::findOrFail($id);
        $editar = [
            'examen_fisico' => $request['examen_fisico'],
            'nota_evolucion' => $request['nota_evolucion'],
        ];
        $variable = $campo->update($editar);
        return back()->with('success', 'Editado con exito:');
    }
    public function resultado_diagnostico(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medico_diagnostico = $request->get('medico_diagnostico');
        $operacion_diagnostico = $request->get('operacion_diagnostico');
        $cie_diagnostico = $request->get('cie_diagnostico');

        $prescri = Diagnostico_005::where('id_paciente', '=', $id_paciente)
            ->medico_diagnostico($medico_diagnostico)
            ->operacion_diagnostico($operacion_diagnostico)
            ->cie_diagnostico($cie_diagnostico)
            ->paginate(5);

        return view('hospital/emergencia/resultado_diagnostico', ['id_paciente' => $id_paciente, 'users' => $users, 'prescri' => $prescri]);
    }
    public function modaleditar_diagnostico(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $campo = Diagnostico_005::findOrFail($id);

        return view('hospital/emergencia/modaleditar_diagnostico', ['campo' => $campo, 'id' => $id]);
    }

    public function editardiagnostico(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $campo = Diagnostico_005::findOrFail($id);
        $editar = [
            'operacion_diagnostico' => $request['operacion'],
            'cie_diagnostico' => $request['cie'],
            'tipo_diagnostico' => $request['tipo'],
        ];
        $variable = $campo->update($editar);
        return back()->with('success', 'Editado con exito:');
    }
    public function resultado_generales(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medico_general = $request->get('medico_general');
        $descripcion_general = $request->get('descripcion_general');
        $prescri = medidas_generales::where('id_paciente', '=', $id_paciente)
            ->medico_general($medico_general)
            ->descripcion_general($descripcion_general)
            ->paginate(5);

        return view('hospital/emergencia/resultado_generales', ['id_paciente' => $id_paciente, 'users' => $users, 'prescri' => $prescri]);
    }
    public function editar_generales(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = medidas_generales::findOrFail($id);

        return view('hospital/emergencia/editar_generales', ['dato' => $dato, 'id' => $id]);
    }
    public function editar_gene(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Medidas_generales::findOrFail($id);
        $editar = [
            'medico_general' => $request['medico_general'],
            'descripcion_general' => $request['descripcion_general'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }

    public function mostrar_resultadotratamiento(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medico_tratamiento = $request->get('medico_tratamiento');
        $descripcion_tratamiento = $request->get('descripcion_tratamiento');

        $tratamiento = Tratamiento_005::where('id_paciente', '=', $id_paciente)
            ->medico_tratamiento($medico_tratamiento)
            ->descripcion_tratamiento($descripcion_tratamiento)
            ->paginate(5);
        return view('hospital/emergencia/mostrar_resultadotratamiento', ['id_paciente' => $id_paciente, 'users' => $users, 'tratamiento' => $tratamiento]);
    }

    public function modal_tratamiento(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Tratamiento_005::findOrFail($id);

        return view('hospital/emergencia/modal_tratamiento', ['dato' => $dato, 'id' => $id]);
    }

    public function editar_tratamiento(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Tratamiento_005::findOrFail($id);
        $editar = [
            'medico_tratamiento' => $request['medico_tratamiento'],
            'descripcion_tratamiento' => $request['descripcion_tratamiento'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }

    public function resultado_plan(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medico_plan = $request->get('medico_plan');
        $descripcion_plan = $request->get('descripcion_plan');

        $tratamiento = Plan_005::where('id_paciente', '=', $id_paciente)
            ->medico_plan($medico_plan)
            ->descripcion_plan($descripcion_plan)
            ->paginate(5);
        return view('hospital/emergencia/resultado_plan', ['id_paciente' => $id_paciente, 'users' => $users, 'tratamiento' => $tratamiento]);
    }

    public function modal_editarplan(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Plan_005::findOrFail($id);

        return view('hospital/emergencia/modal_editarplan', ['dato' => $dato, 'id' => $id]);
    }
    public function editar_plan(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Plan_005::findOrFail($id);
        $editar = [
            'descripcion_plan' => $request['descripcion_plan'],
            'medico_plan' => $request['medico_plan'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }
    public function medicamentos_resultado(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medicamento = $request->get('medicamento');

        $medicamento = Formulario005::where('id_paciente', '=', $id_paciente)
            ->medicamento($medicamento)
            ->paginate(5);

        return view('hospital/emergencia/medicamentos_resultado', ['id_paciente' => $id_paciente, 'users' => $users, 'medicamento' => $medicamento]);
    }

    public function modal_medicamentos(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Formulario005::findOrFail($id);

        return view('hospital/emergencia/modal_medicamentos', ['dato' => $dato, 'id' => $id]);
    }

    public function editar_medi(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Formulario005::findOrFail($id);
        $editar = [
            'medicamento' => $request['medicamento'],
            'posologia' => $request['posologia'],
            'indicaciones_medicinas' => $request['indicaciones_medicinas'],
            'cantidad_medicinas' => $request['cantidad_medicinas'],
            'nombre_medicina' => $request['nombre_medicina'],
            'presentacion_medicamento' => $request['presentacion_medicamento'],
            'concentracion_medicamento' => $request['concentracion_medicamento'],
            'dosis_medicamento' => $request['dosis_medicamento'],
            'unidad_medicamento' => $request['unidad_medicamento'],
            'via_medicamento' => $request['via_medicamento'],
            'frecuencia_medicamento' => $request['frecuencia_medicamento'],
            'duracion_medicamento' => $request['duracion_medicamento'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }

    public function salas_resultado(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $area_salas = $request->get('area_salas');
        $medicina_salas = $request->get('medicina_salas');

        $medicamento = Hospital_Salas::where('id_paciente', '=', $id_paciente)
            ->area_salas($area_salas)
            ->medicina_salas($medicina_salas)
            ->paginate(5);

        return view('hospital/emergencia/salas_resultado', ['id_paciente' => $id_paciente, 'users' => $users, 'medicamento' => $medicamento]);
    }

    public function editar_salas(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Hospital_Salas::findOrFail($id);
        //dd($dato);

        return view('hospital/emergencia/editar_salas', ['dato' => $dato, 'id' => $id]);
    }

    public function editar_modal(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Hospital_Salas::findOrFail($id);
        $editar = [
            'area_salas' => $request['area_salas'],
            'medicina_salas' => $request['medicina_salas'],
            'descripcion_salas' => $request['descripcion_salas'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }

    public function autocompletarcie(Request $request)
    {

        $codigo = $request['term'];
        $data = null;
        $nuevo_nombre = explode(' ', $codigo);
        $seteo = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', id, descripcion) as completo
                  FROM `Cie_10_3`
                  WHERE CONCAT_WS(' ', id, descripcion) like '" . $seteo . "' 
                  ";
        $query1 = "SELECT CONCAT_WS(' ', id, descripcion) as completo1
                  FROM `Cie_10_4`
                  WHERE CONCAT_WS(' ', id, descripcion) like '" . $seteo . "' 
                  ";
        $nombres = DB::select($query);
        $nombres1 = DB::select($query1);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo);
        }
        foreach ($nombres1 as $product) {
            $data[] = array('value' => $product->completo1);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function detalles($id, Request $request)
    {

        //$paciente = Paciente::find($id_paciente);
        $solicitud = Ho_Solicitud::find($id);
        //dd($solicitud);

        //nuevo cambio
        $paciente = Paciente::find($solicitud->id_paciente);
        $sql = "SELECT sol.fecha_ingreso, a.id_paciente,p.religion,TIMESTAMPDIFF(DAY,sol.fecha_ingreso, CURDATE()) as dias_hospitalizados,
        CONCAT(p.apellido1, ' ', p.apellido2, ' ', p.nombre1, ' ', p.nombre2) as nombre_paciente,seg.nombre as seguro, 
        CONCAT(u.apellido1, ' ', u.apellido2, ' ', u.nombre1, ' ', u.nombre2) as nombre_doctor,
        p.fecha_nacimiento, YEAR(CURDATE())-YEAR(p.fecha_nacimiento) + IF(DATE_FORMAT(CURDATE(),'%m-%d') > DATE_FORMAT(p.fecha_nacimiento,'%m-%d'), 0 , -1 ) as edad, 
        if(p.antecedentes_pat is null,'No tiene antecedentes patologicos',p.antecedentes_pat) as ante_patologicos, 
        if(p.antecedentes_fam is null,'No tiene antecedentes familiares',p.antecedentes_fam) as ante_familiares, 
        if(p.antecedentes_quir is null,'No tiene antecedentes quirurgicos',p.antecedentes_quir) as ante_quirurgicos from ho_solicitud sol
        join agenda a on a.id = sol.id_agenda
        join paciente p on p.id = a.id_paciente 
        join users u on u.id = a.id_doctor1
        join seguros seg on seg.id = a.id_seguro and sol.id = $id";
        $data = DB::select($sql);

        $alergiasDB = Paciente_Alergia::where('id_paciente', $paciente->id)
            ->join('principio_activo as pa', 'pa.id', 'paciente_alergia.id_principio_activo')
            ->where('paciente_alergia.id_paciente', $paciente->id)->select('pa.nombre')->get();
        $str_alergias = '';
        $separador = '';
        if (count($alergiasDB) == 1) {
            $separador = '';
        } else {
            $separador = ', ';
        }
        foreach ($alergiasDB as  $alergia) {
            $str_alergias .= $alergia->nombre.$separador;
        }
        $id_paciente = $paciente->id;

        $arr_listado = [];
        $examen = Examen_Orden::where('id_paciente', $id_paciente)->where('estado', 1)->select('fecha_orden', 'id')->first();
        if (!is_null($examen)) {
            $arr_listado[0] = [$examen->fecha_orden, 'LABORATORIO', $examen->id];
        }
        
        $procedimiento = Agenda::join('historiaclinica as hc','hc.id_agenda','=','agenda.id')->join('hc_protocolo as proto', 'proto.hcid','=','hc.hcid')->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc','=','hc.hcid')->join('hc_procedimiento_final as hc_procf','hc_procf.id_hc_procedimientos','=','hc_proc.id')->join('procedimiento as proc','proc.id','=','hc_procf.id_procedimiento')->where('agenda.id_paciente', $id_paciente)->where('agenda.estado',1)->where('agenda.proc_consul',1)->wherenotNull('proto.hallazgos')->wherenotNull('proc.id_grupo_procedimiento')->select('agenda.id as id_agenda','agenda.fechaini','proto.id_hc_procedimientos','proc.nombre', 'proto.id as id_protocolo')->first();
        if(!is_null($procedimiento)){
            $arr_listado[1] = [$procedimiento->fechaini,$procedimiento->nombre, $procedimiento->id_protocolo];
        }
        
        $consulta = Agenda::join('historiaclinica as hc','hc.id_agenda','=','agenda.id')->join('hc_evolucion as evo','evo.hcid','=','hc.hcid')->join('especialidad as esp','esp.id','=','agenda.espid')->where('agenda.id_paciente', $id_paciente)->where('agenda.estado',1)->where('agenda.proc_consul',0)->where('evo.secuencia','=','0')->wherenotNull('evo.cuadro_clinico')->orderby('agenda.fechaini','desc')->select('agenda.id as id_agenda', 'agenda.fechaini', 'evo.id as id_hc_evolucion','esp.nombre')->first();
        if(!is_null($consulta)){
            $arr_listado[2] = [$consulta->fechaini,$consulta->nombre, $consulta->id_hc_evolucion];
        }
        return view('hospital.detalles', ['arr_listado' => $arr_listado, 'solicitud' => $solicitud, 'data' => $data[0], 'str_alergias' => $str_alergias]);
    }
    public function primer_paso(Request $request)
    {

        return view('hospital.primerpaso');
    }
    public function segundo_paso(Request $request)
    {
        return view('hospital.segundopaso');
    }
    public function tercer_paso(Request $request)
    {

        return view('hospital.tercerpaso');
    }
    public function cuarto_paso(Request $request)
    {
        return view('hospital.cuartopaso');
    }
    public function quinto_paso(Request $request)
    {
        return view('hospital.quintopaso');
    }

    public function genericos(Request $request)
    {
        $genericos = [];
        if ($request['search'] != null) {
            $genericos = Medicina::where('medicina.nombre', 'LIKE', $request['search'] . '%')->where('estado', '1')->select('medicina.nombre as text', 'medicina.id as id')->get();
        }
        return response()->json($genericos);
    }
    public function getResponse($id)
    {
        $porSi = "";
        return $id;
    }

    public function admision($id_paso)
    {

        $fecha = date('Y-m-d');
        $seguros = Seguro::where('inactivo', '1')->orderBy('nombre')->get();
        $procedimiento = Procedimiento::where('estado',1)->whereNotNull('id_grupo_procedimiento')->orderBy('nombre')->get();
        $user      = DB::table('users')->where('id_tipo_usuario', 3)->where('estado', 1)->orderBy('apellido1')->get();
        $salas  = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital', 'hospital.direccion as direccion_hospital')
            ->orderBy('hospital.nombre_hospital')
            ->orderBy('sala.nombre_sala')
            ->where('sala.estado', '1')
            //->where('hospital.id', '4')
            ->get();

        $subseguros = Subseguro::join('seguros as s','s.id','subseguro.id_seguro')->where('subseguro.estado', 1)->select('subseguro.nombre','s.nombre as seguro')->orderBy('subseguro.nombre')->get();    


        return view('hospital/admision', ['seguros' => $seguros, 'fecha' => $fecha, 'id_paso' => $id_paso, 'procedimiento' => $procedimiento, 'user' => $user, 'salas' => $salas, 'procedimiento2' => $procedimiento, 'subseguros' => $subseguros]);
    }

    public function ingreso_modulos(Request $request)
    {
        DB::beginTransaction();
        try {
            $ip_cliente   = $_SERVER["REMOTE_ADDR"];
            $idusuario    = Auth::user()->id;
            if (!is_null($request->id_doctor)) {
                $id_doctor = $request->id_doctor;
            } else {
                $id_doctor    = Auth::user()->id;
            }
            $id_paciente = $request['cedula'];
            $paciente = paciente::find($id_paciente);
            $user = User::find($id_paciente);
            $datos_paciente = Ho_Datos_Paciente::Where('id_paciente', $id_paciente)->first();
            $id_paso = $request['id_paso'];
            $estado_paso = HoEstadoPaso::find($id_paso);
            if (is_null($estado_paso)) {
                return ['respuesta' => 'error', 'msj' => 'No tiene configurado el modulo'];
            }

            $input_pac = [
                'id'                 => $id_paciente,
                'id_usuario'         => $id_paciente,
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'fecha_nacimiento'   => $request['f_nacimiento'],
                'sexo'               => $request['sexo'],
                'ciudad'             => $request['ciudad'],
                'direccion'          => $request['direccion'],
                'telefono1'          => $request['telefono1'],
                'telefono2'          => $request['telefono2'],
                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'telefono_llamar'    => $request['telefono_llamar'],
                'ocupacion'          => $request['ocupacion'],
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'referido'           => $request['referido'],
                'tipo_documento'     => $request['tipo_doc'],
                'id_seguro'          => $request['id_seguro'],
                'imagen_url'         => ' ',
                'menoredad'          => 0,
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'lugar_nacimiento'   => $request['lugarNacimiento']
            ];

            $input_pac_upd = [
                'id'                 => $id_paciente,
                'id_usuario'         => $id_paciente,
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'fecha_nacimiento'   => $request['f_nacimiento'],
                'sexo'               => $request['sexo'],
                'ciudad'             => $request['ciudad'],
                'direccion'          => $request['direccion'],
                'telefono1'          => $request['telefono1'],
                'telefono2'          => $request['telefono2'],
                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentescofamiliar' => $request['parentesco'],
                'telefono3'          => $request['telefono_llamar'],
                'ocupacion'          => $request['ocupacion'],
                'referido'           => $request['referido'],
                'ip_modificacion'    => $ip_cliente,
                'id_usuariomod'      => $idusuario,
                'lugar_nacimiento'   => $request['lugarNacimiento']
            ];

            $ran = rand(1,10);
            $input_usu_c = [
                'id'               => $id_paciente,
                'nombre1'          => strtoupper($request['nombre1']),
                'nombre2'          => strtoupper($request['nombre2']),
                'apellido1'        => strtoupper($request['apellido1']),
                'apellido2'        => strtoupper($request['apellido2']),
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'telefono1'        => $request['telefono1'],
                'telefono2'        => $request['telefono2'],
                'id_tipo_usuario'  => 2,
                'email'            => $request['id'] .$ran. '@mail.com',
                'password'         => bcrypt($request['id']),
                'tipo_documento'   => $request['tipo_doc'],
                'estado'           => 1,
                'imagen_url'       => ' ',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
            ];

            $input_usu_up = [
                'id'               => $id_paciente,
                'nombre1'          => strtoupper($request['nombre1']),
                'nombre2'          => strtoupper($request['nombre2']),
                'apellido1'        => strtoupper($request['apellido1']),
                'apellido2'        => strtoupper($request['apellido2']),
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'telefono1'        => $request['telefono1'],
                'telefono2'        => $request['telefono2'],
                'id_tipo_usuario'  => 2,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,
                'ciudad'           => $request['ciudad'],
            ];

            $arr_ho_pac = [
                'id_paciente'        => $id_paciente,
                'barrio'             => $request['barrio'],
                'parroquia'          => $request['parroquia'],
                'canton'             => $request['canton'],
                'provincia'          => $request['provincia'],
                'zona_ur'            => $request['zona'],
                'grupo_cultural'     => $request['grupo_cultural'],
                'edad'               => $request['edad'],
                'direccion_familiar' => $request['direccion_familiar'],
                'forma_llegada'      => $request['forma_llegada'],
                'fuente_informacion'     => $request['fuente_informacion'],
                'telefono_inst_per_paci' => $request['telefono_inst_per_paci'],
                'instruccion'            => $request['instruccion'],
                'empresa_trabajo'        => $request['empresa'],
                'llamar_a'               => $request['llamar_a'],
                'nacionalidad'           => $request['nacionalidad'],
                'ip_creacion'            => $ip_cliente,
                'ip_modificacion'        => $ip_cliente,
                'id_usuariocrea'         => $idusuario,
                'id_usuariomod'          => $idusuario,
                'parentesco_afinidad'    => $request['parentesco'],
                'limite_credito'         => $request['limite_credito'],
                'grupo_prioritario'      => $request['grupoPrioritario'],
                'especifique_grupo'      => $request['especifiqueGrupo'],
                'autoidentificacion_etnica' => $request['autoIdentificacionEtnica'],
                'nacionalidad_etnica'    => $request['nacionalidadEtnica'],
                'pueblos'                => $request['pueblos'],
                'nivel_educacion'        => $request['nivelEducacion'],
                'estado_nivel_educacion' => $request['estadoNivelEducacion'],
                'calle_principal'        => $request['callePrincipal'],
                'calle_secundaria'       => $request['calleSecundaria'],
                'referencia'             => $request['referencia'],
                'id_subseguro'           => $request['id_subseguro'],
            ];

            $arr_ho_pac_up = [
                'id_paciente'        => $id_paciente,
                'barrio'             => $request['barrio'],
                'parroquia'          => $request['parroquia'],
                'canton'             => $request['canton'],
                'provincia'          => $request['provincia'],
                'zona_ur'            => $request['zona'],
                'grupo_cultural'     => $request['grupo_cultural'],
                'edad'               => $request['edad'],
                'direccion_familiar' => $request['direccion_familiar'],
                'forma_llegada'      => $request['forma_llegada'],
                'fuente_informacion'     => $request['fuente_informacion'],
                'telefono_inst_per_paci' => $request['telefono_inst_per_paci'],
                'instruccion'            => $request['instruccion'],
                'empresa_trabajo'        => $request['empresa'],
                'llamar_a'               => $request['llamar_a'],
                'nacionalidad'           => $request['nacionalidad'],
                'parentesco_afinidad'    => $request['parentesco'],
                'ip_modificacion'        => $ip_cliente,
                'id_usuariomod'          => $idusuario,
                'limite_credito'         => $request['limite_credito'],
                'grupo_prioritario'      => $request['grupoPrioritario'],
                'especifique_grupo'      => $request['especifiqueGrupo'],
                'autoidentificacion_etnica' => $request['autoIdentificacionEtnica'],
                'nacionalidad_etnica'    => $request['nacionalidadEtnica'],
                'pueblos'                => $request['pueblos'],
                'nivel_educacion'        => $request['nivelEducacion'],
                'estado_nivel_educacion' => $request['estadoNivelEducacion'],
                'calle_principal'        => $request['callePrincipal'],
                'calle_secundaria'       => $request['calleSecundaria'],
                'referencia'             => $request['referencia'],
                'id_subseguro'           => $request['id_subseguro'],
            ];


            if (is_null($paciente)) {

                if (!is_null($user)) {
                    $user->update($input_usu_up);
                } else {
                    User::create($input_usu_c);
                }

                paciente::create($input_pac);

                $input_log = [
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "CREA NUEVO PACIENTE",
                    'dato_ant1'   => $id_paciente,
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato2'       => 'Hospital',
                ];

                Log_usuario::create($input_log);
            } else {
                $paciente->update($input_pac_upd);
                if (!is_null($datos_paciente)) {
                    $datos_paciente->update($arr_ho_pac_up);
                }
            }

            if (is_null($datos_paciente)) {
                Ho_Datos_Paciente::create($arr_ho_pac);
            } else {
                $datos_paciente->update($arr_ho_pac_up);
            }

            if (is_null($request['id_sala'])) {
                $sala = Sala::where('nombre_sala', 'like', '%' . $estado_paso->descripcion . '%')->first();
                if (is_null($sala)) {
                    return ['respuesta' => 'error', 'msj' => 'No tiene configurada la sala'];
                }else{
                    $id_sala = $sala->id;
                }
            }else{
                $id_sala = $request['id_sala'];
            }


            $especialidad = User_espe::where('usuid', $id_doctor)->get()->first();
            $empresa = Empresa::where('prioridad', '1')->where('estado', '1')->first();
            if (is_null($empresa)) {
                return ['respuesta' => 'error', 'msj' => 'No tiene empresa prioridad'];
            }

            if (!is_null($especialidad)) {
                $espid = $especialidad->espid;
            } else {
                $espid = '4';
            }

            

            if (!is_null($request['id_procedimiento'])) {
                $id_procedimiento = $request['id_procedimiento'];
            }else{
                $id_procedimiento = null;
            }


            $input_agenda = [
                'fechaini'        => Date('Y-m-d H:i:s'),
                'fechafin'        => Date('Y-m-d H:i:s', (strtotime ("+15 Minute"))),
                'id_paciente'     => $id_paciente,
                'id_doctor1'      => $id_doctor,
                'proc_consul'     => '1',
                'estado_cita'     => '4',
                'id_empresa'      => $empresa->id,
                'espid'           => $espid,
                'observaciones'   => 'EVOLUCION CREADA POR HOSPITAL',
                'id_seguro'       => $request['id_seguro'],
                'estado'          => '1',
                'id_sala'         => $id_sala,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_doctor,
                'id_usuariomod'   => $id_doctor,
                'id_procedimiento' => $id_procedimiento,
            ];

            $id_agenda = Agenda::insertGetId($input_agenda);

            if ($estado_paso == '4' or $estado_paso == '5') {
                $arr_agendap = [
                    'id_agenda' => $id_agenda,
                    'id_procedimiento' => $request->procedimiento,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_doctor,
                    'id_usuariomod'   => $id_doctor,
                ];

                AgendaProcedimiento::create($arr_agendap);
            }



            $consulta_crear_new = [
                'anterior'        => 'CONSULTA: -> El Dr. creo nueva consulta -> id_agenda: ' . $id_agenda,
                'nuevo'           => 'CONSULTA: -> El Dr. creo nueva consulta -> id_agenda: ' . $id_agenda,
                'id_paciente'     => $id_paciente,
                'id_usuariomod'   => $id_doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ];
            Hc_Log::create($consulta_crear_new);

            $input_log = [
                'id_agenda'       => $id_agenda,
                'estado_cita_ant' => '1',
                'estado_cita'     => '1',
                'fechaini'        => Date('Y-m-d H:i:s'),
                'fechafin'        => Date('Y-m-d H:i:s'),
                'estado'          => '4',
                'observaciones'   => 'EVOLUCION CREADA POR EL DOCTOR',
                'id_doctor1'      => $id_doctor,
                'descripcion'     => 'EVOLUCION CREADA POR EL DOCTOR',
                'id_usuariomod'   => $id_doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ];

            Log_Agenda::create($input_log);

            $input_historia = [

                'parentesco'      => $request['parentesco'],
                'id_usuario'      => $id_paciente,
                'id_agenda'       => $id_agenda,
                'id_paciente'     => $id_paciente,
                'id_seguro'       => $request['id_seguro'],
                'id_doctor1'      => $id_doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $id_doctor,
                'ip_creacion'     => $ip_cliente,

            ];

            $id_procedimiento_completo = '40';

            $procedimiento_completo = procedimiento_completo::find($id_procedimiento_completo);
            if (is_null($procedimiento_completo)) {
                return ['respuesta' => 'error', 'msj' => 'No tiene configurado procedimiento completo'];
            }

            $id_historia = Historiaclinica::insertGetId($input_historia);

            $input_hc_procedimiento = [
                'id_hc'                     => $id_historia,
                'id_doctor_examinador'      => $id_doctor,
                'id_doctor_examinador2'     => $id_doctor,
                'id_seguro'                 => $request['id_seguro'],
                'id_procedimiento_completo' => $id_procedimiento_completo,
                'ip_modificacion'           => $ip_cliente,
                'id_usuariocrea'            => $idusuario,
                'id_usuariomod'             => $idusuario,
                'ip_creacion'               => $ip_cliente,

            ];

            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

            $procedimiento = Procedimiento::where('nombre', 'like', '%EVOLUCIONES HOSPITAL%')->first();
            if (!is_null($procedimiento)) {
                $input_proc_final = [
                    'id_hc_procedimientos' => $id_hc_procedimiento,
                    'id_procedimiento'     => $procedimiento->id,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                    'ip_creacion'          => $ip_cliente,
                ];
                Hc_Procedimiento_Final::insert($input_proc_final);
            }

            $input_hc_protocolo = [
                'fecha'                => date('Y-m-d'),
                'id_hc_procedimientos' => $id_hc_procedimiento,
                'hora_inicio'          => date('H:i:s'),
                'hora_fin'             => date('H:i:s'),
                'estado_final'         => ' ',
                'ip_modificacion'      => $ip_cliente,
                'hcid'                 => $id_historia,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
                'tipo_procedimiento'   => 0,
            ];
            hc_protocolo::insert($input_hc_protocolo);


            $input_hc_evolucion = [
                'hc_id_procedimiento' => $id_hc_procedimiento,
                'hcid'                => $id_historia,
                'secuencia'           => '0',
                'fecha_ingreso'       => ' ',
                'ip_modificacion'     => $ip_cliente,
                'id_usuariomod'       => $idusuario,
                'id_usuariocrea'      => $idusuario,
                'ip_creacion'         => $ip_cliente,

            ];
            $id_evolucion    = Hc_Evolucion::insertGetId($input_hc_evolucion);

            $input_child_pugh = [
                'id_hc_evolucion'       => $id_evolucion,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'examen_fisico'         => 'ESTADO CABEZA Y CUELLO:
                                                            ESTADO TORAX:
                                                            ESTADO ABDOMEN:
                                                            ESTADO MIEMBROS SUPERIORES:
                                                            ESTADO MIEMBROS INFERIORES:
                                                            OTROS: ',
            ];

            $id_child = hc_child_pugh::create($input_child_pugh);

            $input_hc_receta = [
                'id_hc'           => $id_historia,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,

            ];
            $id_receta = hc_receta::insertGetId($input_hc_receta);

            $arr_receta_evolucion = [
                'id_receta'             => $id_receta,
                'id_evolucion'          => $id_evolucion,
                'estado'                => 1,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
            ];

            $receta_evolucion = Ho_Hc_Receta_Evolucion::create($arr_receta_evolucion);

            if (is_null($request['opcion'])) {
                $arr_solicitud = [
                    'id_paciente'           => $request['cedula'],
                    'id_agenda'             => $id_agenda,
                    'id_seguro'             => $request['id_seguro'],
                    'fecha_ingreso'         => date('Y-m-d H:i:s'),
                    'estado_paso'           => $id_paso,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                ];

                $ho_solicitud = Ho_solicitud::insertGetId($arr_solicitud);

                
                
            }else{
                $ho_solicitud = $request['idsolicitud'];
            }

            $sol_log = [
                'id_ho_solicitud'       => $ho_solicitud,
                'estado_paso'           => $id_paso,
                'id_agenda'             => $id_agenda,
                'fecha_ingreso'         => date('Y-m-d'),
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
            ];

            Ho_Log_Solicitud::create($sol_log);
            ///////////// TRASPASO PARA CAMAS
            $traspaso_sala = Ho_Traspaso_Sala008::where('id_solicitud', $ho_solicitud)->first();
            $req['id_sala'] = $id_sala;
            $req['paso'] = $id_paso;
            $req['id_doctor'] = $id_doctor;
            $req['id_agenda'] = $id_agenda;
            $this->RealizaTraslado($req, $traspaso_sala, $ho_solicitud, $request['cedula']);

            if ($estado_paso->id == 2) {
                $arr_form = [
                    'id_solicitud'          => $ho_solicitud,
                    'fecha_creacion'        => date('Y-m-d'),
                    'id_agenda'             => $id_agenda,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                ];

                $id_form = Ho_Form008::insertGetId($arr_form);
            }


            if ($id_paso == 2) {
                $arr_manchester = [
                    'id_ho_solicitud'       => $ho_solicitud,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                ];

                Ho_Triaje_Manchester::create($arr_manchester);
            }



            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'id_agenda' => $id_agenda, 'id_solicitud' => $ho_solicitud, 'paso' => $id_paso];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function RealizaTraslado($request, $traspaso_sala, $id_solicitud, $id_paciente){

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;

        $solicitud     = Ho_Solicitud::find($id_solicitud);
        if($solicitud->estado_paso != 2){

            if (is_null($traspaso_sala)) {
                Ho_Traspaso_Sala008::create([
                    'id_solicitud'       => $id_solicitud,
                    'id_sala'            => $request['id_sala'],
                    'id_paciente'        => $id_paciente,
                    'paso'               => $request['paso'],
                    'fecha'              => date('Y-m-d H:i:s'),
                    'id_doctor'          => $request['id_doctor'],
                    'estado'             => '1',
                    'id_usuariocrea'     => $idusuario,
                    'id_usuariomod'      => $idusuario,
                ]);
                
            } else {
            
                $eq = [
                    'id_sala'            => $request['id_sala'],
                    'fecha'              => date('Y-m-d H:i:s'),
                    'id_doctor'          => $request['id_doctor'],
                    'estado'             => '1',
                    'id_usuariomod'      => $idusuario,
                ];
                $traspaso_sala->update($eq);
            }
            
            $up_solicitud = [
                'id_agenda'         => $request['id_agenda'],
                'estado_paso'       => $request['paso'],
                'ip_modificacion'   => $ip_cliente,
                'id_usuariomod'     => $idusuario,

            ];

            

            $solicitud->update($up_solicitud);

            $sol_log = [
                'id_ho_solicitud'       => $solicitud->id,
                'estado_paso'           => $request['paso'],
                'id_agenda'             => $request['id_agenda'],
                'fecha_ingreso'         => date('Y-m-d'),
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
            ];
            Ho_Log_Solicitud::create($sol_log);
        }
    }
    //Funcion del index donde se encuentra el gestor de documentos donde esta el formulario008
    public function index_modulos(Request $request, $id_paso)
    {
        
        $desde = $request->fecha_desde;
        if($desde == null){
            $desde = Date('Y-m-d');
        }
        
        $hasta = $request->fecha_hasta;
        if($hasta == null){
            $hasta = Date('Y-m-d');
        }    
        $ho_estado_paso = HoEstadoPaso::find($id_paso);
        $salas  = Sala::where('estado', 1)->get();
        //$solicitudes = Ho_solicitud::where('estado_paso', $id_paso)->whereBetween('fecha_ingreso', [$desde.' 00:00:00', $hasta.' 23:59:59'])->get();//dd($solicitudes);
        $solicitudes = Ho_solicitud::where('ho_solicitud.estado_paso', $id_paso)->join('agenda as a','a.id','ho_solicitud.id_agenda')->select('ho_solicitud.*','a.fechaini')->whereBetween('a.fechaini', [$desde.' 00:00:00', $hasta.' 23:59:59'])->get();
        return view('hospital/modulos/index', ['solicitudes' => $solicitudes, 'salas' => $salas, 'ho_estado_paso' => $ho_estado_paso, 'desde' => $desde, 'hasta' => $hasta]);
    }
    public function buscador_vista(Request $request)
    {


        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $sala = $request['sala'];
        $nombres = $request['paciente'];
        $cedula = $request['cedula'];
        $id_estado_paso = $request['id_estado_paso'];


        $ho_solicitud = ho_solicitud::leftjoin("ho_estado_paso as paso", "ho_solicitud.estado_paso", "paso.id")->where('ho_solicitud.estado_paso', $id_estado_paso)->join('agenda as a', 'ho_solicitud.id_agenda', 'a.id')
            ->join('paciente as p', 'a.id_paciente', 'p.id');



        if (!is_null($sala)) {

            $ho_solicitud = $ho_solicitud->where('a.id_sala', $sala);
        }
        if (!is_null($fecha_desde) && !is_null($fecha_hasta)) {

            $ho_solicitud = $ho_solicitud->whereBetween('ho_solicitud.created_at', [$fecha_desde, $fecha_hasta]);
        }
        if (!is_null($cedula)) {
            $ho_solicitud = $ho_solicitud->where('ho_solicitud.id_paciente', $cedula);
        }

        if (!is_null($nombres)) {

            $ho_solicitud = $ho_solicitud->whereRaw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                ->orwhereRaw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                ->orwhereRaw('CONCAT(p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
        }

        $ho_solicitud = $ho_solicitud->get();

        $salas = Sala::where('estado', '1')->get();

        return view('hospital/habitacion/vista_buscador', ['salas' => $salas, 'solicitudes' => $ho_solicitud, 'ho_estado_paso' => $id_estado_paso]);
    }

    public function traspaso_modulo($id)
    {
        $solicitud = Ho_Solicitud::find($id);
        $pasos = HoEstadoPaso::where('estado', '1')->whereNotNull('ruta')->get();
        return view('hospital/modulos/traspaso', ['solicitud' => $solicitud, 'pasos' => $pasos]);
    }

    public function guardar_traspaso(Request $request)
    {

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        DB::beginTransaction();
        try {
            $id_solicitud = $request->id_solicitud;
            $opcion = $request->opcion;
            $id_paso = $request->id_paso;

            $solicitud = Ho_Solicitud::find($id_solicitud);

            $datos_paciente = Ho_Datos_Paciente::Where('id_paciente', $solicitud->id_paciente)->first();
            $paciente = $solicitud->paciente;

            $arr_datos = [
                'id'                    => $solicitud->id_paciente,
                'cedula'                => $solicitud->id_paciente,
                'idsolicitud'           => $solicitud->id,
                'opcion'                => $opcion,
                'id_paso'               => $id_paso,
                'nombre1'               => $paciente->nombre1,
                'nombre2'               => $paciente->nombre2,
                'apellido1'             => $paciente->apellido1,
                'apellido2'             => $paciente->apellido2,
                'f_nacimiento'          => $paciente->fecha_nacimiento,
                'sexo'                  => $paciente->sexo,
                'ciudad'                => $paciente->ciudad,
                'direccion'             => $paciente->direccion,
                'telefono1'             => $paciente->telefono1,
                'telefono2'             => $paciente->telefono2,
                'telefono_llamar'       => $paciente->telefono_llamar,
                'ocupacion'             => $paciente->ocupacion,
                'referido'              => $paciente->referido,
                'fecha_nacimiento'      => $paciente->fecha_nacimiento,
                'barrio'                => $datos_paciente->barrio,
                'parroquia'             => $datos_paciente->parroquia,
                'canton'                => $datos_paciente->canton,
                'provincia'             => $datos_paciente->provincia,
                'zona'                  => $datos_paciente->zona_ur,
                'grupo_cultural'        => $datos_paciente->grupo_cultural,
                'edad'                  => $datos_paciente->edad,
                'direccion_familiar'    => $datos_paciente->direccion_familiar,
                'forma_llegada'         => $datos_paciente->forma_llegada,
                'fuente_informacion'    => $datos_paciente->fuente_informacion,
                'telefono_inst_per_paci' => $datos_paciente->telefono_inst_per_paci,
                'instruccion'           => $datos_paciente->instruccion,
                'empresa'               => $datos_paciente->empresa_trabajo,
                'llamar_a'              => $datos_paciente->llamar_a,
                'nacionalidad'          => $datos_paciente->nacionalidad,
                'id_seguro'             => $solicitud->id_seguro,

            ];

            $request->request->add($arr_datos);
            $ingreso = $this->ingreso_modulos($request);
            //dd($ingreso);

            $up_solicitud = [
                'id_agenda'         => $ingreso['id_agenda'],
                'estado_paso'       => $id_paso,
                'ip_modificacion'   => $ip_cliente,
                'id_usuariomod'     => $idusuario,

            ];

            $solicitud->update($up_solicitud);

            $sol_log = [
                'id_ho_solicitud'       => $solicitud->id,
                'estado_paso'           => $id_paso,
                'id_agenda'             => $ingreso['id_agenda'],
                'fecha_ingreso'         => date('Y-m-d'),
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
            ];
            Ho_Log_Solicitud::create($sol_log);

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'paso' => $id_paso];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function subir_formularios($id)
    {

        $solicitud = Ho_Solicitud::find($id);

        $ho_archivos = Ho_Archivos_Solicitud::where('id_solicitud', $solicitud->id)->get();

        return view('hospital/modulos/archivos', ['solicitud' => $solicitud, 'ho_archivos' => $ho_archivos]);
    }

    public function guardar_formularios(Request $request)
    {

        DB::beginTransaction();
        try {

            $id_solicitud = $request->id_solicitud;
            $nombre_original = $request['archivo']->getClientOriginalName();

            $extension       = $request['archivo']->getClientOriginalExtension();
            //dd($extension);

            $nuevo_nombre    =   "formularios" . date('YmdHis') . "." . $extension;

            $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));

            $rutadelaimagen = base_path() . '/storage/app/avatars/' . $nuevo_nombre;

            if ($r1) {
                $arr_archivo = [
                    'id_solicitud'      => $id_solicitud,
                    'nombre_archivo'    => $nuevo_nombre
                ];

                Ho_Archivos_Solicitud::create($arr_archivo);
            }

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'success'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['respuesta' => 'error', "msj" => 'error', "exp" => $e->getMessage()];
        }
    }

    public function detalle_formularios($id_sol)
    {

        $solicitud = Ho_Solicitud::find($id_sol);
        $ho_archivos = Ho_Archivos_Solicitud::where('id_solicitud', $solicitud->id)->where('estado', '1')->get();

        return view('hospital/modulos/detalle_archivos', ['ho_archivos' => $ho_archivos, 'solicitud' => $solicitud]);
    }

    public function descarga_formulario($name)
    {
        $imagen          = Ho_Archivos_Solicitud::find($name);
        //$paciente        = paciente::find($imagen->id_paciente);
        $nombre_archivo = null;
        $path            = base_path() . '/storage/app/avatars/' . $imagen->nombre_archivo;
        $nombre_archivo = $nombre_archivo . '_' . $imagen->nombre_archivo;


        if ($nombre_archivo == null) {
            $nombre_archivo = $imagen->nombre_archivo;
        } else {
            $nombre_temporal = $imagen->nombre_archivo;
            $datos           = explode(".", $nombre_temporal);
            if (count($datos) == 2) {
                $extension      = $datos[1];
                $nombre_archivo = $nombre_archivo . '.' . $extension;
                if ($extension == 'mp4') {
                    $path = public_path('uploads/') . $imagen->nombre_archivo;
                }
            } else {
                $nombre_archivo = $imagen->nombre_archivo;
            }
        }
        if (file_exists($path)) {
            return Response::download($path, $nombre_archivo);
        }
    }

    public function eliminar_formulario($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $imagen = Ho_Archivos_Solicitud::find($id);

        $arr = [
            'estado' => 0,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $imagen->update($arr);
    }

    public function ver_formulario($id_archivo)
    {

        $nombreArchivo = Ho_Archivos_Solicitud::find($id_archivo);
        if (!is_null($nombreArchivo)) {
            $pathtoFile = base_path() . '/storage/app/avatars/' . $nombreArchivo->nombre_archivo;
            //dd($pathtoFile);
            return response()->file($pathtoFile);
        }
        return "no cargado";
    }

    public function listado_paciente($id_paciente)
    {
        $arr_listado = [];
        $examen = Examen_Orden::where('id_paciente', $id_paciente)->where('estado', 1)->select('fecha_orden', 'id')->first();
        if (!is_null($examen)) {
            $arr_listado[0] = [$examen->fecha_orden, 'LABORATORIO', $examen->id];
        }
        $procedimiento = Agenda::join('historiaclinica as hc', 'hc.id_agenda', '=', 'agenda.id')->join('hc_protocolo as proto', 'proto.hcid', '=', 'hc.hcid')->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc', '=', 'hc.hcid')->join('hc_procedimiento_final as hc_procf', 'hc_procf.id_hc_procedimientos', '=', 'hc_proc.id')->join('procedimiento as proc', 'proc.id', '=', 'hc_procf.id_procedimiento')->where('agenda.id_paciente', $id_paciente)->where('agenda.estado', 1)->where('agenda.proc_consul', 1)->wherenotNull('proto.hallazgos')->wherenotNull('proc.id_grupo_procedimiento')->select('agenda.id as id_agenda', 'agenda.fechaini', 'proto.id_hc_procedimientos', 'proc.nombre', 'proto.id as id_protocolo')->first();
        if (!is_null($procedimiento)) {
            $arr_listado[1] = [$procedimiento->fechaini, $procedimiento->nombre, $procedimiento->id_protocolo];
        }
        $consulta = Agenda::join('historiaclinica as hc', 'hc.id_agenda', '=', 'agenda.id')->join('hc_evolucion as evo', 'evo.hcid', '=', 'hc.hcid')->join('especialidad as esp', 'esp.id', '=', 'agenda.espid')->where('agenda.id_paciente', $id_paciente)->where('agenda.estado', 1)->where('agenda.proc_consul', 0)->where('evo.secuencia', '=', '0')->wherenotNull('evo.cuadro_clinico')->orderby('agenda.fechaini', 'desc')->select('agenda.id as id_agenda', 'agenda.fechaini', 'evo.id as id_hc_evolucion', 'esp.nombre')->first();
        if (!is_null($consulta)) {
            $arr_listado[2] = [$consulta->fechaini, $consulta->nombre, $consulta->id_hc_evolucion];
        }
    }

    public function modal_laboratorio($id)
    {
        return view('hospital/laboratorio/modal_resultados', ['id' => $id]);
    }
    public function modal_consultas($id)
    {
        $evolucion = Hc_Evolucion::find($id);

        $hc_cie10 = Hc_Cie10::where('hcid', $evolucion->hcid)->get();

        $c10_arr = [];
        if (count($hc_cie10) > 0) {
            foreach ($hc_cie10 as $c10) {
                $c3 = Cie_10_3::find($c10->cie10);
                $c4 = Cie_10_4::find($c10->cie10);
                if ($c3 != null) {
                    $c10_arr[$c10->id] = $c3->descripcion;
                }
                if ($c4 != null) {
                    $c10_arr[$c10->id] = $c4->descripcion;
                }
            }
        }

        return view('hospital/consulta/modal_consulta', ['id' => $id, 'evolucion' => $evolucion, 'hc_cie10' => $hc_cie10, 'c10_arr' => $c10_arr]);
    }

    public function modal_estudio($id)
    {
        return view('hospital/imagenes/modal_estudio_vt', ['id' => $id]);
    }

    public function planillar($id_solicitud){

        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;

        $solicitud = Ho_Solicitud::find($id_solicitud);
        if(is_null($solicitud)){
            return ['respuesta' => 'error', 'msj' => 'ERROR EN SOLICITUD', 'linea' => ''];
        }
        
        $planilla  = Planilla::where('estado',1)->where('id_solicitud',$solicitud)->first();

        $agenda = $solicitud->agenda;
        if(is_null($agenda)){
            return ['respuesta' => 'error', 'msj' => 'ERROR EN AGENDA', 'linea' => ''];
        }

        $hc = $agenda->historia_clinica;
        if(is_null($hc)){
            return ['respuesta' => 'error', 'msj' => 'ERROR EN HISTORIA CLINICA', 'linea' => ''];
        }

        $hc_proc = $hc->procedimiento->first();
        if(is_null($hc_proc)){
            return ['respuesta' => 'error', 'msj' => 'ERROR EN PROCEDIMIENTO', 'linea' => ''];
        }

        $plantilla = Insumo_Plantilla_Control::where('estado',1)->where('codigo','HOSPITAL')->first();
        if(is_null($plantilla)){
            return ['respuesta' => 'error', 'msj' => 'ERROR EN PLANTILLA', 'linea' => ''];
        }

        if(!is_null($planilla)){

            $id_planilla = $planilla->id;

        }else{

            $paciente = $hc->paciente;

            $id_planilla = Planilla::insertGetId([
                'codigo'              => 'HOSPITAL',
                'id_agenda'           => $solicitud->id_agenda,
                'fecha'               => date('Y-m-d H:i:s'),
                'id_hc_procedimiento' => $hc_proc->id,
                'id_planilla'         => $plantilla->id,
                'observacion'         => 'Paciente '.$paciente->apellido1.' '.$paciente->apellido2.' | '.$paciente->nombre1.' '.$paciente->nombre2,
                'estado'              => 1,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_solicitud'        => $id_solicitud,

            ]);

        }

        return ['respuesta' => 'ok', 'msj' => $id_planilla, 'linea' => ''];   

    }

    public function planillar_detalle($id_solicitud){

        $solicitud = Ho_Solicitud::find($id_solicitud);
       
        $logs = $solicitud->log;

        $planilla = Planilla::where('id_solicitud', $id_solicitud)->first();
        $detalles = [];
        if(!is_null($planilla)){
            $detalles = $planilla->detalles_validos;
        }

        return view('hospital/planilla/editar', ['logs' => $logs, 'detalles' => $detalles, 'id_solicitud' => $id_solicitud, 'solicitud' => $solicitud]);


    }

    public function planillar_detalle_aplicar( $id_log ){

        $log = Ho_Log_Solicitud::find( $id_log );

        //dd($log->paso);
        $estado_insumo = $log->paso->paso_insumos->first();
        //dd($estado_insumo);  
        
        $insumo    = null;$precio = 0;
        if(!is_null($estado_insumo)){
            $insumo = $estado_insumo->insumo;//dd($insumo);
            if(!is_null($insumo)){
                $codigo = $insumo->codigo;
                $producto_insumo = Ct_productos_insumos::where('id_insumo',$insumo->id)->first();//dd($producto_insumo);
                if(!is_null($producto_insumo)){ 
                    $producto = $producto_insumo->ct_producto;
                    if(!is_null($producto)){
                        $precio = $producto->valor_total_paq;
                    }
                } 
            }        
        } 
                 

        $cabecera = Planilla::where('id_solicitud',$log->id_ho_solicitud)->first();
        //dd($cabecera,$log);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        Planilla_Detalle::create([
            'id_planilla_cabecera' => $cabecera->id,
            //'movimiento'           => $request['movimiento'][$i],
            'cantidad'             => '1',
            //'serie'                => $request['serie'][$i],
            //'lote'                 => $request['lote'][$i],
            'check'                => '1',
            //'fecha_vencimiento'    => $request['fecha_vencimiento'][$i],
            'codigo' => $codigo,
            'precio' => $precio,
            'estado' => 1,
            'id_usuariocrea'         => $idusuario,
            'id_usuariomod'          => $idusuario,
            'ip_creacion'            => $ip_cliente,
            'ip_modificacion'        => $ip_cliente,
            'observacion'            => 'HOSPITAL: PASO '.$log->paso->nombre_paso,
            //'tipo_plantilla'         => $tipo_plantilla,
            //'id_movimiento_paciente' => $id_movimiento_paciente,
            //'id_bodega'              => $id_bodega
            'id_log_solic' => $id_log,

        ]);

        return "ok";

    }

    public function aplicar_todos($id_solic){

        $solicitud = Ho_Solicitud::find($id_solic);
       
        $logs = $solicitud->log;

        foreach($logs as $log){

            $this->planillar_detalle_aplicar($log->id);

        }
        
        return "ok";

    }

    public function crear_recibo_hospital($id_solicitud)
    {
        /*if ($this->rol()) {
            return response()->view('errors.404');
        }*/

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $solicitud = Ho_Solicitud::find($id_solicitud);

        if(is_null($solicitud)){
            return ['respuesta' => 'error', 'msj' => 'NO SE ENCUENTRA SOLICITUD', 'linea' => '']; 
        }

        $log = $solicitud->log->first();

        if(is_null($log)){
            return ['respuesta' => 'error', 'msj' => 'NO SE ENCUENTRA LOG', 'linea' => '']; 
        }

        $agenda = $log->agenda;

        if(is_null($agenda)){
            return ['respuesta' => 'error', 'msj' => 'NO SE ENCUENTRA AGENDA', 'linea' => '']; 
        }

        $id_agenda = $agenda->id;

        $paciente = $agenda->paciente;

        $convenio = Convenio::where('id_seguro')->where('estado','1')->get()->first();
 
        $id_nivel = null;
        if(!is_null($convenio)){
            $id_nivel = $convenio->id_nivel;
        }

        $cliente = Ct_Clientes::find($agenda->id_paciente);

        $tipo_identificacion    = null;
        $telefono   = null;
        $razon_social           = null;
        $email      = null;
        $ciudad                 = null;
        $direccion              = null;
        if(!is_null($cliente)){
            $tipo_identificacion    = $cliente->tipo;
            $razon_social           = $cliente->nombre;
            $ciudad                 = $cliente->ciudad_representante;
            $direccion              = $cliente->direccion_representante;
            $telefono               = $cliente->telefono1_representante;
            $email                  = $cliente->email_representante;

        }else{

            $cl_nombre = $paciente->apellido1;
            if($paciente->apellido2 != '(N/A)' && $paciente->apellido2 != 'N/A' && $paciente->apellido2 != '.'){
                $cl_nombre = $cl_nombre.' '.$paciente->apellido2;    
            }
            $cl_nombre = $cl_nombre.' '.$paciente->nombre1;
            if($paciente->nombre2 != '(N/A)' && $paciente->nombre2 != 'N/A' && $paciente->nombre2 != '.'){
                $cl_nombre = $cl_nombre.' '.$paciente->nombre2;    
            }

            ct_clientes::create([
                'identificacion'          => $agenda->id_paciente,
                'nombre'                  => $cl_nombre,
                'tipo'                    => '5',
                'clase'                   => 'normal',
                'estado'                  => '1',
                'ciudad_representante'    => $paciente->ciudad,
                'direccion_representante' => $paciente->direccion,
                'telefono1_representante' => $paciente->telefono1,
                'email_representante'     => $paciente->usuario->email,
                'pais'                    => 'Ecuador',
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
                'id_usuariocrea'          => $idusuario,
                'id_usuariomod'           => $idusuario,
            ]);

            $cliente = Ct_Clientes::find($agenda->id_paciente);

            $tipo_identificacion    = $cliente->tipo;
            $razon_social           = $cliente->nombre;
            $ciudad                 = $cliente->ciudad_representante;
            $direccion              = $cliente->direccion_representante;
            $telefono               = $cliente->telefono1_representante;
            $email                  = $cliente->email_representante;

        }

        $id_empresa = $agenda->id_empresa;
        if($id_empresa == null){
            $empresa = Empresa::where('prioridad','1')->get()->first();
            $id_empresa = $empresa->id;
        }

        $arr_orden_venta = [
            'id_agenda'     => $id_agenda,
            'id_empresa'    => $id_empresa,
            'fecha_emision' => date('Y-m-d'),
            'nueva_fecha'   => date('Y-m-d H:i:s'),
            'oda'           => 0,
            'id_seguro'     => $agenda->id_seguro,
            'id_nivel'      => $id_nivel,
            'tipo_identificacion'   => $tipo_identificacion,
            'identificacion'        => $agenda->id_paciente,
            'razon_social'          => $razon_social,
            'ciudad'                => $ciudad,
            'direccion'             => $direccion,
            'telefono'              => $telefono,
            'email'                 => $email,
            'id_usuariocrea'        =>  $idusuario,
            'id_usuariomod'         =>  $idusuario,
            'ip_creacion'           =>  $ip_cliente,
            'ip_modificacion'       =>  $ip_cliente,
            'valor_oda'             =>  0,
            'total_sin_tarjeta'     =>  0,
            'estado'                => -1,
            'numero_oda'            => 0,
        ];

        $id_orden = Ct_Orden_Venta::insertGetId($arr_orden_venta);

        $arr_log_recibo = [    
            'id_ct_orden_venta' => $id_orden,
            'descripcion'       => 'CREADA EL: '.date('Y-m-d H:i:s'),
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,  
        ];
        LogReciboDeCobro::create($arr_log_recibo);

        //return redirect()->route('nuevorecibocobro.editar',['id' => $id_orden]);
        return ['respuesta' => 'ok', 'id' => $id_orden, 'linea' => '']; 
    }

    public function listar_recibo_hospital($id_solicitud)
    {
        /*if ($this->rol()) {
            return response()->view('errors.404');
        }*/

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $solicitud = Ho_Solicitud::find($id_solicitud);

        if(is_null($solicitud)){
            return ['respuesta' => 'error', 'msj' => 'NO SE ENCUENTRA SOLICITUD', 'linea' => '']; 
        }

        $log = $solicitud->log->first();

        if(is_null($log)){
            return ['respuesta' => 'error', 'msj' => 'NO SE ENCUENTRA LOG', 'linea' => '']; 
        }

        $agenda = $log->agenda;

        if(is_null($agenda)){
            return ['respuesta' => 'error', 'msj' => 'NO SE ENCUENTRA AGENDA', 'linea' => '']; 
        }

        $id_agenda = $agenda->id;

        $recibos = Ct_Orden_Venta::where('id_agenda',$id_agenda)->where('estado','<>',0)->get();//dd($recibos,$id_agenda);
        
        return view('hospital/planilla/listar_rc', ['recibos' => $recibos, 'id_solicitud' => $id_solicitud]);
    }

    public function editar_recibo_hospital($id)
    {
        /*if ($this->rol()) {
            return response()->view('errors.404');
        }*/

        $orden      = Ct_Orden_Venta::find($id);
        $agenda     = $orden->agenda;
        $empresa    = $orden->empresa; 
        $paciente   = $agenda->paciente;
        $cajas      = CtCajaCobro::where('estado',1)->get();

        $seguros = Seguro::where('seguros.inactivo', '1')
            ->where('promo_seguro', '<>', 1)
            ->orderBy('nombre', 'asc')->get(); 

        $tipos_seguros = SeguroTipos::where('estado','>','1')->get();
        $doctor_referido = Comisiones_Gastro::where("estado",1)->get();

        //return view('contable/nuevo_recibo/editar',['doctor_referido'=>$doctor_referido ,'orden' => $orden, 'agenda' => $agenda, 'empresa' => $empresa, 'seguros' => $seguros, 'paciente' => $paciente, 'cajas' => $cajas, 'tipos_seguros' => $tipos_seguros ]);
        return view('hospital/planilla/editar_rc',['doctor_referido'=>$doctor_referido ,'orden' => $orden, 'agenda' => $agenda, 'empresa' => $empresa, 'seguros' => $seguros, 'paciente' => $paciente, 'cajas' => $cajas, 'tipos_seguros' => $tipos_seguros ]);
    }  

    public function detalles_rc($id_orden)
    {
        /*if ($this->rol()) {
            return response()->view('errors.404');
        }*/
        
        $orden      = Ct_Orden_Venta::find($id_orden);
        $detalles   = $orden->detalles;

        return view('hospital/planilla/detalles', ['orden' => $orden, 'detalles' => $detalles]);

    }

    public function formas_pg_rc($id_orden)
    {
        /*if ($this->rol()) {
            return response()->view('errors.404');
        }*/
        

        $tipo_pago    = Ct_Tipo_Pago::where('estado',1)->get();
        $lista_banco  = Ct_Bancos::where('estado',1)->get();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::where('estado',1)->get();
        

        $orden      = Ct_Orden_Venta::find($id_orden);
        $detalles   = $orden->pagos;

        return view('hospital/planilla/formas_pago', ['orden' => $orden, 'detalles' => $detalles, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'tipo_tarjeta' => $tipo_tarjeta]);

    }


    public function cargar_detalle_recibo($id_detalle, $id_recibo){

        $detalle = Planilla_Detalle::find($id_detalle);
        if(!is_null($detalle)){
            $insumo = $detalle->producto;

            $producto_insumo = Ct_productos_insumos::where('id_insumo',$insumo->id)->first();
            // return $producto_insumo;
            if (!is_null($producto_insumo)) {
                $producto_contable = Ct_productos::where('id', $producto_insumo->id_producto)->first();
                
                if (!is_null($producto_contable)) {
                    $recibo = Ct_Orden_Venta::find($id_recibo);
                    if(!is_null($recibo)){
                        $this->guardar_producto($producto_contable,$recibo);
                    }    
                } 
            }

        }

    } 

    public function guardar_producto($producto, $recibo)
    {
       
        $p_dcto    = 0;
        $descuento = 0;

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        //RUTINA PARA OBTENER EL PRECIO DE LOS PRODUCTOS
        $orden    = $recibo;
        $precio   = $producto->valor_total_paq;

        if($precio == null || $precio == 0){
            $precio_producto = PrecioProducto::where('codigo_producto',$producto->codigo)->orderBy('nivel','asc')->get()->first();
            if(!is_null($precio_producto)){
                $precio = $precio_producto->precio;    
            }else{
                $precio = 0;
            }
        }
        $valor_pvp = $precio;
        $valor_promo = 0;
        $vtotal    = $precio;
        if($orden->id_nivel != null){
            $tarifario = $producto->tarifarios->where('nivel',$orden->id_nivel)->where('estado',1)->first();
            if(!is_null($tarifario)){
                $precio = $tarifario->precio_producto;
                $vtotal = $precio;
                if($orden->id_seguro != null){
                    if($orden->seguro->id_seguro_tipos != null){
                        if($orden->seguro->tipo_seguro->estado == '3'){
                            $valor_promo = $precio; //dd($tarifario,$valor_pvp,$valor_promo);
                            $p_dcto    = ($valor_pvp - $valor_promo) / $valor_pvp;
                            $p_dcto    = round($p_dcto,2);
                            $p_dcto    = $p_dcto * 100;
                            $descuento = ($valor_pvp - $valor_promo);
                            $precio    = $valor_pvp;
                            $vtotal    = $precio - $descuento;   
                        }
                    }
                }
            }
        }    
        ////////////////////////////////////////////////

        $iva = 0;
        if($producto->iva == 1){
            $iva = Ct_Configuraciones::find('3');
            if(!is_null($iva)){
                $iva = $iva->iva;
            } //TERRIBLE EL TEMA DEL IVA
        }
        //dd($id_orden);

        $valor_iva = $precio * $iva;
        $valor_iva = round($valor_iva,2);

        $arr_producto = [
            'id_orden' => $recibo->id,
            'cod_prod' => $producto->codigo,
            //'descripcion'   => $producto->nombre,
            'nombre_producto'   => $producto->nombre,
            'id_producto'   => $producto->id,
            'precio'        => $precio,
            'cantidad'      => 1,
            'total'         => $vtotal,
            //ident_paquete TINYINT(4) DEFAULT NULL,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            //'tipo_oda' VARCHAR(1) DEFAULT 'NULL',
            'p_oda'           => 0,
            'valor_oda'       => 0,
            'tipo_dcto' => '%',
            'p_dcto'    => $p_dcto,
            'descuento' => $descuento,
            'iva'       => $iva,
            'valor_iva' => $valor_iva,
            //valor_deducible FLOAT DEFAULT 0,
            //valor_oda_hum FLOAT DEFAULT 0,
            //porc_dedu_segu FLOAT DEFAULT NULL,
            //porc_dedu_paci FLOAT DEFAULT NULL,
            //fee FLOAT DEFAULT NULL,
            'cobrar_paciente' => $precio,
            'cobrar_seguro'   => 0,
        ];

        Ct_Orden_Venta_Detalle::create($arr_producto);

        $this->recalcular($recibo->id);

        return "ok";

    }

    public static function recalcular($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $orden    = Ct_Orden_Venta::find($id);

        $total_con_descuento = $orden->detalles->sum('total');
        $total_iva           = $orden->detalles->sum('valor_iva');
        $total               = $total_con_descuento + $total_iva;
        $subtotal_0          = $orden->detalles->where('iva',0)->sum('cobrar_paciente');
        $subtotal_12         = $orden->detalles->where('iva','>',0)->sum('cobrar_paciente');
        $oda                 = $orden->detalles->sum('cobrar_seguro');
        $descuento           = $orden->detalles->sum('descuento');

        $arr_cabecera = [
            'total'                 => $total,
            'iva'                   => $total_iva,
            'subtotal_0'            => $subtotal_0,
            'subtotal_12'           => $subtotal_12,
            'id_usuariomod'         => $idusuario,
            'ip_modificacion'       => $ip_cliente,
            'valor_oda'             => $oda,
            'total_sin_tarjeta'     => $total_con_descuento,
            'descuento'             => $descuento,
            'total_cobrar_seguro'   => $oda,
        ];
        
        $orden->update($arr_cabecera);    

    }

}
