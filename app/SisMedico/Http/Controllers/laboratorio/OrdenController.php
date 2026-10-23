<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Carbon\Carbon;
use Cookie;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Agenda_consentimiento;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Convenio;
use Sis_medico\Ct_Bancos;
use Sis_medico\Membresia;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_Orden_Venta_Detalle;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_Ven_Orden;
use Sis_medico\Cupones;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_Sabana;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Detalle_Costo;
use Sis_medico\Examen_Detalle_Forma_Pago;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Orden_Agenda;
use Sis_medico\Examen_Orden_Seguimiento;
use Sis_medico\Examen_Orden_Toma_Muestra;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Protocolo;
use Sis_medico\Examen_Resultado;
use Sis_medico\Examen_Sub_Resultado;
use Sis_medico\Excepcion_Horario;
use Sis_medico\Firma_Usuario;
use Sis_medico\Forma_de_pago;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\laboratorio\MembresiasLabsController;
use Sis_medico\Labs_doc_externos;
use Sis_medico\Labs_Factura_Agrupada_Cab;
use Sis_medico\Labs_Factura_Agrupada_Detalle;
use Sis_medico\Labs_Factura_Agrupada_Orden;
use Sis_medico\Labs_Grupo_Familiar;
use Sis_medico\Log_usuario;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Labs;
use Sis_medico\CierreCaja;
//PONER ABAJO
use Sis_medico\Procedimiento;
use Sis_medico\Protocolo;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Examen_Comprobante_Ingreso;
use Sis_medico\Apps_Plan_Miembros;
use Sis_medico\UserMembresia;
use Sis_medico\Labs_Tipo_Tubo;
use Sis_medico\Formato_consentimiento;
use Sis_medico\Http\Controllers\laboratorio\NuevoResultadoController;

class OrdenController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol_new($opcion)
    {
        //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
        //dd($opcion);
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {

            return true;
        }
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 16, 20)) == false) {
            return true;
        }
    }
    private function rol_sis()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1)) == false) {
            return true;
        }
    }
    private function rol_supervision()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 12, 11, 3, 16, 22)) == false && $id_auth != '1307189140') {
            return true;
        }
    }

    private function rol_control()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 10, 11, 16)) == false) {
            return true;
        }
    }

    private function rol_control2()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 10, 16)) == false) {
            return true;
        }
    }

    private function rol_doctor()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 3)) == false) {
            return true;
        }
    }

    public function recupera_ordenes()
    {

        $ordenes = DB::table('examen_orden as eo')->join('paciente as p', 'p.id', 'eo.id_paciente')->join('seguros as s', 's.id', 'eo.id_seguro')->leftjoin('empresa as em', 'em.id', 'eo.id_empresa')->leftjoin('nivel as n', 'n.id', 'eo.id_nivel')->leftjoin('protocolo as proto', 'proto.id', 'eo.id_protocolo')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->join('users as cu', 'cu.id', 'eo.id_usuariocrea')->join('users as mu', 'mu.id', 'eo.id_usuariomod')->leftjoin('labs_grupo_familiar as gf', 'gf.id', 'p.id')->leftjoin('users as u2', 'u2.id', 'gf.id_usuario')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 's.nombre as snombre', 'n.nombre as nnombre', 'em.nombrecomercial', 'cu.nombre1 as cnombre1', 'cu.apellido1 as capellido1', 'mu.nombre1 as mnombre1', 'mu.apellido1 as mapellido1', 'em.nombre_corto', 'proto.pre_post', 's.tipo as stipo', 'p.sexo', 'p.telefono1', 'p.telefono2', 'p.telefono3', 'mu.email as mail1', 'u2.email as mail2');
        //dd($ordenes->first());
        return $ordenes;
    }

    public function index()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $fecha = date('Y/m/d');

        $ordenes = $this->recupera_ordenes()->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha . ' 23:59'])->where('eo.estado', '<>', '0');
        $ordenes = $ordenes->paginate(100);

        $xordenes = $this->recupera_ordenes()->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha . ' 23:59'])->where('eo.estado', '<>', '0');
        $nofacturados = $xordenes->where('eo.estado', 1)->whereNull('comprobante')->groupBy('eo.id_seguro')->select('eo.id_seguro', DB::raw('count(*) as cantidad'))->get(); //dd($nofacturados);

        $consulta = Examen_Orden::where("estado", -1)->where('doctor_txt', 'COTIZACION GENERADA EN APP MÒVIL')->whereDate('created_at', Carbon::today())->get();
        //dd($ordenes);

        //$ordenes2 = $this->recupera_ordenes()->where('eo.estado', '-1')->where('s.tipo', '0')->where('eo.fecha_orden', '<', $fecha . ' 00:00')->paginate(40);
        //dd($ordenes2);
        $ordenes2 = [];

        $ex_det = [];
        $cont   = 0;
        /*foreach ($ordenes as $orden) {
            $txt_examen = "";
            $detalle    = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'ed.id_examen')->select('ed.*', 'e.nombre')->get();

            $cont++;
            $bandera = 0;
            foreach ($detalle as $value) {

                if ($bandera == 0) {

                    $txt_examen = $value->nombre;
                    $bandera    = 1;
                } else {
                    $txt_examen = $txt_examen . '+' . $value->nombre;
                }
            }

            $ex_det[$orden->id] = $txt_examen;

        }*/

        $seguros = Seguro::orderBy('nombre')->get();

        $gestiones = Examen_Orden::where('pago_online', '1')->where('estado', '1')->whereNull('fecha_orden')->where('gestion', '0')->where('estado_pago', '1')->get();

        // dd($ordenes->get()->first());

        return view('laboratorio/orden/index', ['consulta' => $consulta, 'ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha, 'ex_det' => $ex_det, 'nombres' => null, 'seguro' => null, 'seguros' => $seguros, 'ordenes2' => $ordenes2, 'gestiones' => $gestiones, 'facturadas' => 'TODAS', 'id_nivel' => null, 'nofacturados' => $nofacturados]);
    }

    public function parametro($id_examen)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $examen_parametros = Examen_Parametro::where('id_examen', $id_examen)->paginate(30);
        $examen            = Examen::find($id_examen);

        return view('laboratorio/examen/parametro', ['examen' => $examen, 'examen_parametros' => $examen_parametros]);
    }

    public function create()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->where('training', '<>', '1')->orderBy('apellido1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();se mmuestran todos
        $examenes    = Examen::where('publico_privado', '0')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $seguros     = Seguro::where('inactivo', '1')->get();
        $protocolos  = Protocolo::where('estado', '1')->get();
        $convenios   = DB::table('convenio as c')->join('seguros as s', 's.id', 'c.id_seguro')->join('empresa as e', 'e.id', 'c.id_empresa')->where('s.tipo', '0')->select('c.*', 's.nombre', 'e.nombrecomercial')->orderby('s.nombre')->get();
        //dd($convenios);

        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();

        return view('laboratorio/orden/create', ['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas, 'convenios' => $convenios]);
    }

    public function crear_particular()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->orderBy('apellido1')->where('uso_laboratorio', '1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();se mmuestran todos
        $examenes = Examen::orderBy('id_agrupador')->get();
        //dd($examenes);
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $seguros1    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.tipo', '>', '1')->select('s.nombre', 's.id')->get();
        $seguros2    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.tipo', '>', '0')->join('convenio as c', 'c.id_seguro', 's.id')->select('s.nombre', 's.id')->groupBy('s.id', 's.nombre')->orderBy('s.nombre')->get();
        //dd($seguros2);
        //$seguros = $seguros1->union($seguros2)->get();
        //dd($seguros);
        //$seguros    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.tipo', '>', '0')->orderBy('s.nombre')->get();

        $protocolos = Protocolo::where('estado', '1')->get();

        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();

        /*return view('laboratorio/orden/create_particular',['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas]);*/

        return view('laboratorio/orden/crear_cotizacion', ['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros1' => $seguros1, 'seguros2' => $seguros2, 'protocolos' => $protocolos, 'empresas' => $empresas]);
    }

    public function crear_particular2($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();se mmuestran todos
        $examenes = Examen::orderBy('id_agrupador')->get();
        //dd($examenes);
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $seguros     = Seguro::where('inactivo', '1')->get();
        $protocolos  = Protocolo::where('estado', '1')->get();

        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();

        $paciente = Paciente::find($id);

        return view('laboratorio/orden/create_particular2', ['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas, 'paciente' => $paciente]);
    }

    public function create_admin($id_agenda, $url)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        /*$historia = DB::table('historiaclinica as h')->where('h.id_agenda',$id_agenda)->join('paciente as p','p.id','h.id_paciente')->join('users as d','d.id','h.id_doctor1')->select('h.*','p.nombre1','p.nombre2','p.apellido1','p.apellido2','d.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->first();
        $agenda = Agenda::find($id_agenda);*/
        $agenda = DB::table('agenda as a')->where('a.id', $id_agenda)->join('paciente as p', 'p.id', 'a.id_paciente')->leftjoin('users as d', 'd.id', 'a.id_doctor1')->select('a.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'p.sexo', 'p.fecha_nacimiento')->first();

        $historia = Historiaclinica::where('id_agenda', $agenda->id)->first();

        $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->where('training', '0')->where('uso_sistema', '0')->orderBy('apellido1')->get(); //3=DOCTORES;

        $convenios = DB::table('convenio as c')->join('seguros as s', 's.id', 'c.id_seguro')->join('empresa as e', 'e.id', 'c.id_empresa')->where('s.tipo', '0')->select('c.*', 's.nombre', 'e.nombrecomercial')->orderby('s.nombre')->get();

        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();se mmuestran todos
        $examenes    = Examen::where('publico_privado', '0')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $seguros     = Seguro::where('inactivo', '1')->get();
        $protocolos  = Protocolo::where('estado', '1')->get();

        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();

        return view('laboratorio/orden/create_admin', ['url_doctor' => $url, 'agenda' => $agenda, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas, 'usuarios' => $usuarios, 'convenios' => $convenios, 'historia' => $historia]);
    }

    /*public function create_parametro($id_examen){
    if($this->rol()){
    return response()->view('errors.404');
    }

    $examen = Examen::find($id_examen);
    return view('laboratorio/examen/create_parametro',['examen' => $examen]);

    }*/

    public function store(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $this->validateInput($request);

        $nivel = '0';

        //$convenio = Convenio::where('id_seguro', $request['id_seguro'])->where('id_empresa', $request['id_empresa'])->first();
        $convenio = Convenio::find($request->id_convenio);

        //$seguro   = Seguro::find($request['id_seguro']);
        $seguro = Seguro::find($convenio->id_seguro);
        //dd($convenio);
        $bandera_err = false;
        if ($seguro->tipo != '0') {

            $bandera_err = true;
            $arr_campo   = ['id_seguro' => 'Seguro particular no habilitado'];
        } else {

            if (is_null($convenio)) {
                $bandera_err = true;
                $arr_campo   = ['id_seguro' => 'Convenio no habilitado', 'id_empresa' => 'Convenio no habilitado'];
            }
        }

        $fecha_orden = date('Y-m-d');
        if ($request->fecha_orden != null) {

            $fecha_orden = $request->fecha_orden;
        }

        if ($bandera_err) {

            $usuarios    = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
            $examenes    = Examen::where('estado', '1')->orderBy('id_agrupador')->get();
            $agrupadores = Examen_Agrupador::where('estado', '1')->get();
            $seguros     = Seguro::where('inactivo', '1')->get();
            $protocolos  = Protocolo::where('estado', '1')->get();
            $empresas    = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();
            $convenios   = DB::table('convenio as c')->join('seguros as s', 's.id', 'c.id_seguro')->join('empresa as e', 'e.id', 'c.id_empresa')->where('s.tipo', '0')->select('c.*', 's.nombre', 'e.nombrecomercial')->orderby('s.nombre')->get();

            return redirect()->back()->with(['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas, 'convenios' => $convenios])->withErrors($arr_campo)->withInput(['id' => $request['id'], 'nombre1' => $request['nombre1'], 'nombre2' => $request['nombre2'], 'apellido1' => $request['apellido1'], 'apellido2' => $request['apellido2'], 'id_doctor_ieced' => $request['id_doctor_ieced'], 'id_seguro' => $request['id_seguro'], 'est_amb_hos' => $request['est_amb_hos'], 'id_protocolo' => $request['id_protocolo'], 'id_empresa' => $request['id_empresa'], 'fecha_nacimiento' => $request->fecha_nacimiento, 'sexo' => $request->sexo, 'id_convenio' => $request->id_convenio]);
        }
        $nivel = $convenio->nivel;

        //CREAR USUARIO
        $input_usu_c = [

            'id'               => $request['id'],
            'nombre1'          => strtoupper($request['nombre1']),
            'nombre2'          => strtoupper($request['nombre2']),
            'apellido1'        => strtoupper($request['apellido1']),
            'apellido2'        => strtoupper($request['apellido2']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'telefono1'        => '1',
            'telefono2'        => '1',
            'id_tipo_usuario'  => 2,
            'email'            => $request['id'] . '@mail.com',
            'password'         => bcrypt($request['id']),
            'tipo_documento'   => 1,
            'estado'           => 1,
            'imagen_url'       => ' ',
            'ip_creacion'      => $ip_cliente,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariocrea'   => $idusuario,
            'id_usuariomod'    => $idusuario,

        ];

        $user = User::find($request['id']);

        if (!is_null($user)) {
            //$user->update($input_usu_a);
        } else {
            //User::create($input_usu_c);
        }

        $input_pac = [

            'id'                 => $request['id'],
            'id_usuario'         => $request['id'],
            'nombre1'            => strtoupper($request['nombre1']),
            'nombre2'            => strtoupper($request['nombre2']),
            'apellido1'          => strtoupper($request['apellido1']),
            'apellido2'          => strtoupper($request['apellido2']),
            'fecha_nacimiento'   => $request['fecha_nacimiento'],
            'sexo'               => $request['sexo'],
            'telefono1'          => '1',
            'telefono2'          => '1',
            'nombre1familiar'    => strtoupper($request['nombre1']),
            'nombre2familiar'    => strtoupper($request['nombre2']),
            'apellido1familiar'  => strtoupper($request['apellido1']),
            'apellido2familiar'  => strtoupper($request['apellido2']),
            'parentesco'         => 'Principal',
            'parentescofamiliar' => 'Principal',
            'tipo_documento'     => 1,
            'id_seguro'          => 1,
            'imagen_url'         => ' ',
            'menoredad'          => 0,

            'ip_creacion'        => $ip_cliente,
            'ip_modificacion'    => $ip_cliente,
            'id_usuariocrea'     => $idusuario,
            'id_usuariomod'      => $idusuario,

        ];

        $paciente = Paciente::find($request['id']);

        if (is_null($paciente)) {

            if (!is_null($user)) {
                //$user->update($input_usu_a);
            } else {
                User::create($input_usu_c);
            }

            paciente::create($input_pac);
            $datos = [
                'id_paciente'     => $request['id'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            Paciente_Labs::create($datos);

            $input_log = [
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['id'],
                'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                'dato_ant4'   => " PARENTESCO: Principal",
                'dato2'       => 'HOSPITALIZADO',
            ];

            Log_usuario::create($input_log);
        } else {
            if ($paciente->fecha_nacimiento == null || $paciente->sexo == null) {

                $pac = [
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'sexo'             => $request['sexo'],
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                ];
                $paciente->update($pac);
            }
        }

        $valor    = 0;
        $cont     = 0;
        $total    = 0;
        //$examenes = Examen::where('estado', '1')->get();
        $arr_examenes = [];

        //if(Auth::user()->id == "0951561075"){
        foreach ($request->all() as $key => $examenesxr) {
            $var_examen = substr($key, 0, 2);
            if ($var_examen == 'ch') {
                $var_id_examen = substr($key, 2);
                $examen2 = Examen::find($var_id_examen);

                if (!is_null($examen2)) {
                    $arr_examenes[] = $examen2->id;
                    // $cont++;

                    // $valor = $examen->valor;
                    // if (!is_null($convenio)) {
                    //     $nivel = $convenio->id_nivel;
                    //     //dd($nivel);
                    //     $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    //     if (!is_null($ex_nivel)) {
                    //         $valor = $ex_nivel->valor1;
                    //     }
                    // }
                    // $total = $total + $valor;
                }
            }
        }

        //$examenes3 = Examen::whereIn('id', $arr_examenes)->get();
        //dd($examenes3);

        $examenes = Examen::whereIn('id', $arr_examenes)->get();

        foreach ($examenes as $examen) {
            if (!is_null($request['ch' . $examen->id])) {
                $cont++;

                $valor = $examen->valor;
                if (!is_null($convenio)) {
                    $nivel = $convenio->id_nivel;
                    //dd($nivel);
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            }
        }

        $input_ex = [
            'id_paciente'     => $request['id'],
            'anio'            => substr(date('Y-m-d'), 0, 4),
            'mes'             => substr(date('Y-m-d'), 5, 2),
            'id_protocolo'    => $request['id_protocolo'],
            //'id_seguro'       => $request['id_seguro'],
            'id_seguro'       => $convenio->id_seguro,
            'id_nivel'        => $nivel,
            'est_amb_hos'     => $request['est_amb_hos'],
            'id_doctor_ieced' => $request['id_doctor_ieced'],
            'doctor_txt'      => $request['doctor_txt'],
            'observacion'     => $request['observacion'],
            //'id_empresa'      => $request['id_empresa'],
            'id_empresa'      => $convenio->id_empresa,
            'cantidad'        => $cont,
            'valor'           => $total,
            'total_valor'     => $total,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            //'fecha_orden' => date('Y-m-d h:i:s'),
            'fecha_orden'     => $fecha_orden,
            'estado'          => '-1',

        ];

        //dd($input_ex);

        $id_examen_orden = Examen_Orden::insertGetId($input_ex);

        /*$examenes = $request['examen'];

        foreach ($examenes as $examen) {
        $input_det = [
        'id_examen_orden' => $id_examen_orden,
        'id_examen' => $examen,

        'ip_creacion' => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea' => $idusuario,
        'id_usuariomod' => $idusuario,

        ];

        Examen_detalle::create($input_det);
        } */

        $valor         = 0;
        $cont          = 0;
        $examen_nombre = "";
        //        $examenes      = Examen::where('estado', '1')->get();
        foreach ($examenes as $examen) {
            if (!is_null($request['ch' . $examen->id])) {

                $valor         = $examen->valor;
                $examen_nombre = $examen_nombre . "+" . $examen->nombre;
                if (!is_null($convenio)) {
                    $nivel    = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }

                $cont++;
                $input_det = [
                    'id_examen_orden' => $id_examen_orden,
                    'id_examen'       => $examen->id,
                    'valor'           => $valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'p_descuento'     => 0,
                    'valor_descuento' => 0,
                ];

                Examen_detalle::create($input_det);
            }
        }

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "GENERA ORDEN EXAMEN",
            'dato_ant1'   => $request['id'],
            'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
            'dato_ant4'   => $examen_nombre,
        ]);

        //dd($request->all(),$cont);

        return redirect()->route('orden.index');
    }

    public function store_publico(Request $request)
    {
        //dd("2", $request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $this->validateInput($request);
        $nivel = '0';
        //$convenio = Convenio::where('id_seguro', $request['id_seguro'])->where('id_empresa', $request['id_empresa'])->first();
        $convenio = Convenio::find($request->id_convenio);
        $id_empresa = $convenio->id_empresa;
        //$seguro   = Seguro::find($request['id_seguro']);
        $seguro = Seguro::find($convenio->id_seguro);
        //dd($convenio);
        $bandera_err = false;
        if ($seguro->tipo != '0') {
            $bandera_err = true;
            $arr_campo   = ['id_seguro' => 'Seguro particular no habilitado'];
        } else {
            if (is_null($convenio)) {
                $bandera_err = true;
                $arr_campo   = ['id_seguro' => 'Convenio no habilitado', 'id_empresa' => 'Convenio no habilitado'];
            }
        }
        $fecha_orden = date('Y-m-d');
        if ($request->fecha_orden != null) {
            $fecha_orden = $request->fecha_orden;
        }
        if ($bandera_err) {
            $usuarios    = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
            $examenes    = Examen::where('estado', '1')->orderBy('id_agrupador')->get();
            $agrupadores = Examen_Agrupador::where('estado', '1')->get();
            $seguros     = Seguro::where('inactivo', '1')->get();
            $protocolos  = Protocolo::where('estado', '1')->get();
            $empresas    = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();
            $convenios   = DB::table('convenio as c')->join('seguros as s', 's.id', 'c.id_seguro')->join('empresa as e', 'e.id', 'c.id_empresa')->where('s.tipo', '0')->select('c.*', 's.nombre', 'e.nombrecomercial')->orderby('s.nombre')->get();
            return redirect()->back()->with(['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas, 'convenios' => $convenios])->withErrors($arr_campo)->withInput(['id' => $request['id'], 'nombre1' => $request['nombre1'], 'nombre2' => $request['nombre2'], 'apellido1' => $request['apellido1'], 'apellido2' => $request['apellido2'], 'id_doctor_ieced' => $request['id_doctor_ieced'], 'id_seguro' => $request['id_seguro'], 'est_amb_hos' => $request['est_amb_hos'], 'id_protocolo' => $request['id_protocolo'], 'id_empresa' => $request['id_empresa'], 'fecha_nacimiento' => $request->fecha_nacimiento, 'sexo' => $request->sexo, 'id_convenio' => $request->id_convenio]);
        }
        $nivel = $convenio->nivel;
        //CREAR USUARIO
        $input_usu_c = [
            'id'               => $request['id'],
            'nombre1'          => strtoupper($request['nombre1']),
            'nombre2'          => strtoupper($request['nombre2']),
            'apellido1'        => strtoupper($request['apellido1']),
            'apellido2'        => strtoupper($request['apellido2']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'telefono1'        => '1',
            'telefono2'        => '1',
            'id_tipo_usuario'  => 2,
            'email'            => $request['id'] . '@mail.com',
            'password'         => bcrypt($request['id']),
            'tipo_documento'   => 1,
            'estado'           => 1,
            'imagen_url'       => ' ',
            'ip_creacion'      => $ip_cliente,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariocrea'   => $idusuario,
            'id_usuariomod'    => $idusuario,
        ];
        $user = User::find($request['id']);
        if (!is_null($user)) {
            //$user->update($input_usu_a);
        } else {
            //User::create($input_usu_c);
        }
        $input_pac = [
            'id'                 => $request['id'],
            'id_usuario'         => $request['id'],
            'nombre1'            => strtoupper($request['nombre1']),
            'nombre2'            => strtoupper($request['nombre2']),
            'apellido1'          => strtoupper($request['apellido1']),
            'apellido2'          => strtoupper($request['apellido2']),
            'fecha_nacimiento'   => $request['fecha_nacimiento'],
            'sexo'               => $request['sexo'],
            'telefono1'          => '1',
            'telefono2'          => '1',
            'nombre1familiar'    => strtoupper($request['nombre1']),
            'nombre2familiar'    => strtoupper($request['nombre2']),
            'apellido1familiar'  => strtoupper($request['apellido1']),
            'apellido2familiar'  => strtoupper($request['apellido2']),
            'parentesco'         => 'Principal',
            'parentescofamiliar' => 'Principal',
            'tipo_documento'     => 1,
            'id_seguro'          => 1,
            'imagen_url'         => ' ',
            'menoredad'          => 0,
            'ip_creacion'        => $ip_cliente,
            'ip_modificacion'    => $ip_cliente,
            'id_usuariocrea'     => $idusuario,
            'id_usuariomod'      => $idusuario,
        ];
        $paciente = Paciente::find($request['id']);
        if (is_null($paciente)) {
            if (!is_null($user)) {
                //$user->update($input_usu_a);
            } else {
                User::create($input_usu_c);
            }
            paciente::create($input_pac);
            $datos = [
                'id_paciente'     => $request['id'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            Paciente_Labs::create($datos);
            $input_log = [
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['id'],
                'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                'dato_ant4'   => " PARENTESCO: Principal",
                'dato2'       => 'HOSPITALIZADO',
            ];
            Log_usuario::create($input_log);
        } else {
            if ($paciente->fecha_nacimiento == null || $paciente->sexo == null) {
                $pac = [
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'sexo'             => $request['sexo'],
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                ];
                $paciente->update($pac);
            }
        }
        $valor    = 0;
        $cont     = 0;
        $total    = 0;
        $examenes = Examen::where('estado', '1')->get();
        foreach ($examenes as $examen) {
            if (!is_null($request['ch' . $examen->id])) {
                $cont++;
                $valor = $examen->valor;
                if (!is_null($convenio)) {
                    $nivel = $convenio->id_nivel;
                    //dd($nivel);
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            }
        }
        $input_ex = [
            'id_paciente'     => $request['id'],
            'anio'            => substr(date('Y-m-d'), 0, 4),
            'mes'             => substr(date('Y-m-d'), 5, 2),
            'id_protocolo'    => $request['id_protocolo'],
            //'id_seguro'       => $request['id_seguro'],
            'id_seguro'       => $convenio->id_seguro,
            'id_nivel'        => $nivel,
            'est_amb_hos'     => $request['est_amb_hos'],
            'id_doctor_ieced' => $request['id_doctor_ieced'],
            'doctor_txt'      => $request['doctor_txt'],
            'observacion'     => $request['observacion'],
            //'id_empresa'      => $request['id_empresa'],
            'id_empresa'      => $convenio->id_empresa,
            'cantidad'        => $cont,
            'valor'           => $total,
            'total_valor'     => $total,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            //'fecha_orden' => date('Y-m-d h:i:s'),
            'fecha_orden'     => $fecha_orden,
            'estado'          => '-1',
        ];
        //dd($input_ex);
        $id_examen_orden = Examen_Orden::insertGetId($input_ex);
        /*$examenes = $request['examen'];
        foreach ($examenes as $examen) {
        $input_det = [
        'id_examen_orden' => $id_examen_orden,
        'id_examen' => $examen,
        'ip_creacion' => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea' => $idusuario,
        'id_usuariomod' => $idusuario,
        ];
        Examen_detalle::create($input_det);
        } */
        $valor         = 0;
        $cont          = 0;
        $examen_nombre = "";
        $examenes      = Examen::where('estado', '1')->get();
        foreach ($examenes as $examen) {
            if (!is_null($request['ch' . $examen->id])) {
                $valor         = $examen->valor;
                $examen_nombre = $examen_nombre . "+" . $examen->nombre;
                if (!is_null($convenio)) {
                    $nivel    = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }
                $cont++;
                $input_det = [
                    'id_examen_orden' => $id_examen_orden,
                    'id_examen'       => $examen->id,
                    'valor'           => $valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'p_descuento'     => 0,
                    'valor_descuento' => 0,
                ];
                Examen_detalle::create($input_det);
            }
        }
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "GENERA ORDEN EXAMEN",
            'dato_ant1'   => $request['id'],
            'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
            'dato_ant4'   => $examen_nombre,
        ]);
        //AGREGAR NUEVA AGENDA
        $estado_cita    = 0;
        $estado         = 1;
        $cortesia       = "NO";
        $proc_consul    = 0;
        $doctor_inicial = '4444444444';
        $sala           = '22';
        $espid          = '10';
        $tipo_cita      = 1;
        $id_seguro      = '2';
        
        $input_historia = [
            'fechaini'        => $request['inicio'],
            'fechafin'        => $request['fin'],
            'id_paciente'     => $paciente->id,
            'id_doctor1'      => $doctor_inicial,
            'proc_consul'     => $proc_consul,
            'id_empresa'      => $id_empresa,
            'id_sala'         => $sala,
            'espid'           => $espid,
            'tipo_cita'       => $tipo_cita,
            'estado_cita'     => $estado_cita,
            'observaciones'   => $request['observaciones'],
            'est_amb_hos'     => $request['est_amb_hos'],
            'id_seguro'       => $convenio->id_seguro,
            'estado'          => 1,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'cortesia'        => $cortesia,
        ];
        //Agenda::create($input_historia);
        $nueva_agenda = Agenda::insertGetId($input_historia);
        //dd($nueva_agenda);
        $input_orden = [
            'id_agenda'       => $nueva_agenda,
            'id_orden'        => $id_examen_orden,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $id_examen_orden_agenda = Examen_Orden_Agenda::insertGetId($input_orden);
        return ['estado' => 'OK', 'mensaje' => 'Orden creada con éxito'];
    }
    public function new_store_publico(Request $request)
    {
        //dd("2", $request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        //$this->validateInput($request);

        $id_orden = $request->id_orden;
        $orden = Examen_Orden::find($id_orden);
     
        $paciente = Paciente::find($orden->id_paciente);
        
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "EMITE ORDEN PUBLICA",
            'dato_ant1'   => $paciente->id,
            'dato1'       => strtoupper($paciente->nombre1 . " " . $paciente->nombre2. " " . $paciente->apellido1 . " " . $paciente->apellido2),
            //'dato_ant4'   => $examen_nombre,
        ]);
        //AGREGAR NUEVA AGENDA
        $estado_cita    = 0;
        $estado         = 1;
        $cortesia       = "NO";
        $proc_consul    = 0;
        $doctor_inicial = '4444444444';
        $sala           = '22';
        $espid          = '10';
        $tipo_cita      = 1;
        $id_seguro      = '2';
        $empresa = Empresa::where('prioridad_labs','1')->first();
        $input_historia = [
            'fechaini'        => $request['inicio'],
            'fechafin'        => $request['fin'],
            'id_paciente'     => $paciente->id,
            'id_doctor1'      => $doctor_inicial,
            'proc_consul'     => $proc_consul,
            'id_empresa'      => $empresa->id_empresa,
            'id_sala'         => $sala,
            'espid'           => $espid,
            'tipo_cita'       => $tipo_cita,
            'estado_cita'     => $estado_cita,
            'observaciones'   => $request['observaciones'],
            'est_amb_hos'     => $request['est_amb_hos'],
            'id_seguro'       => $orden->id_seguro,
            'estado'          => 1,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'cortesia'        => $cortesia,
        ];
        //Agenda::create($input_historia);
        $nueva_agenda = Agenda::insertGetId($input_historia);
        //dd($nueva_agenda);
        $input_orden = [
            'id_agenda'       => $nueva_agenda,
            'id_orden'        => $orden->id,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $id_examen_orden_agenda = Examen_Orden_Agenda::insertGetId($input_orden);
        return ['estado' => 'OK', 'mensaje' => 'Orden creada con éxito'];
    }
    

    public function store_particular(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $this->validateInput($request);

        $nivel    = null;
        $convenio = Convenio::where('id_seguro', $request['id_seguro'])->where('id_empresa', $request['id_empresa'])->first();
        $seguro   = Seguro::find($request['id_seguro'])->where('inactivo', '1');
        //dd($convenio);
        $bandera_err = false;
        /*if($seguro->tipo!='0'){

        $bandera_err=true;
        $arr_campo=['id_seguro' => 'Seguro particular no habilitado'];

        }else{

        if(is_null($convenio)){
        $bandera_err=true;
        $arr_campo=['id_seguro' => 'Convenio no habilitado','id_empresa' => 'Convenio no habilitado'];
        }
        }*/

        if ($bandera_err) {

            $usuarios    = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
            $examenes    = Examen::where('estado', '1')->orderBy('id_agrupador')->get();
            $agrupadores = Examen_Agrupador::where('estado', '1')->get();
            $seguros     = Seguro::where('inactivo', '1')->get();
            $protocolos  = Protocolo::where('estado', '1')->get();
            $empresas    = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();

            return redirect()->back()->with(['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas])->withErrors($arr_campo)->withInput(['id' => $request['id'], 'nombre1' => $request['nombre1'], 'nombre2' => $request['nombre2'], 'apellido1' => $request['apellido1'], 'apellido2' => $request['apellido2'], 'id_doctor_ieced' => $request['id_doctor_ieced'], 'id_seguro' => $request['id_seguro'], 'est_amb_hos' => $request['est_amb_hos'], 'id_protocolo' => $request['id_protocolo'], 'id_empresa' => $request['id_empresa']]);
        }

        //CREAR USUARIO
        $input_usu_c = [

            'id'               => $request['id'],
            'nombre1'          => strtoupper($request['nombre1']),
            'nombre2'          => strtoupper($request['nombre2']),
            'apellido1'        => strtoupper($request['apellido1']),
            'apellido2'        => strtoupper($request['apellido2']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'telefono1'        => '1',
            'telefono2'        => '1',
            'id_tipo_usuario'  => 2,
            'email'            => $request['id'] . '@mail.com',
            'password'         => bcrypt($request['id']),
            'tipo_documento'   => 1,
            'estado'           => 1,
            'imagen_url'       => ' ',
            'ip_creacion'      => $ip_cliente,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariocrea'   => $idusuario,
            'id_usuariomod'    => $idusuario,

        ];

        $user = User::find($request['id']);

        if (!is_null($user)) {
            //$user->update($input_usu_a);
        } else {
            //User::create($input_usu_c);
        }

        $input_pac = [

            'id'                 => $request['id'],
            'id_usuario'         => $request['id'],
            'nombre1'            => strtoupper($request['nombre1']),
            'nombre2'            => strtoupper($request['nombre2']),
            'apellido1'          => strtoupper($request['apellido1']),
            'apellido2'          => strtoupper($request['apellido2']),
            'fecha_nacimiento'   => $request['fecha_nacimiento'],
            'sexo'               => $request['sexo'],
            'telefono1'          => '1',
            'telefono2'          => '1',
            'nombre1familiar'    => strtoupper($request['nombre1']),
            'nombre2familiar'    => strtoupper($request['nombre2']),
            'apellido1familiar'  => strtoupper($request['apellido1']),
            'apellido2familiar'  => strtoupper($request['apellido2']),
            'parentesco'         => 'Principal',
            'parentescofamiliar' => 'Principal',
            'tipo_documento'     => 1,
            'id_seguro'          => 1,
            'imagen_url'         => ' ',
            'menoredad'          => 0,

            'ip_creacion'        => $ip_cliente,
            'ip_modificacion'    => $ip_cliente,
            'id_usuariocrea'     => $idusuario,
            'id_usuariomod'      => $idusuario,

        ];

        //dd($input_pac,$input_usu_c);

        $paciente = Paciente::find($request['id']);

        if (is_null($paciente)) {

            if (!is_null($user)) {
                //$user->update($input_usu_a);
            } else {
                User::create($input_usu_c);
            }

            paciente::create($input_pac);

            $input_log = [
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['id'],
                'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                'dato_ant4'   => " PARENTESCO: Principal",
                'dato2'       => 'HOSPITALIZADO',
            ];

            Log_usuario::create($input_log);
        } else {
            if ($paciente->fecha_nacimiento == null || $paciente->sexo == null) {

                $pac = [
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'sexo'             => $request['sexo'],
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                ];
                $paciente->update($pac);
            }
        }

        $valor    = 0;
        $cont     = 0;
        $total    = 0;
        $examenes = Examen::where('estado', '1')->get();
        foreach ($examenes as $examen) {
            if (!is_null($request['ch' . $examen->id])) {
                $cont++;

                $valor = $examen->valor;
                if (!is_null($convenio)) {
                    $nivel = $convenio->id_nivel;
                    //dd($nivel);
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            }
        }

        //dd($nivel);

        $input_ex = [
            'id_paciente'     => $request['id'],
            'anio'            => substr(date('Y-m-d'), 0, 4),
            'mes'             => substr(date('Y-m-d'), 5, 2),
            'id_protocolo'    => $request['id_protocolo'],
            'id_seguro'       => $request['id_seguro'],
            'id_nivel'        => $nivel,
            'est_amb_hos'     => $request['est_amb_hos'],
            'id_doctor_ieced' => $request['id_doctor_ieced'],
            'doctor_txt'      => $request['doctor_txt'],
            'observacion'     => $request['observacion'],
            'id_empresa'      => $request['id_empresa'],
            'cantidad'        => $cont,
            'valor'           => $total,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ];

        //dd($input_ex);

        $id_examen_orden = Examen_Orden::insertGetId($input_ex);

        /*$examenes = $request['examen'];

        foreach ($examenes as $examen) {
        $input_det = [
        'id_examen_orden' => $id_examen_orden,
        'id_examen' => $examen,

        'ip_creacion' => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea' => $idusuario,
        'id_usuariomod' => $idusuario,

        ];

        Examen_detalle::create($input_det);
        } */

        $valor         = 0;
        $cont          = 0;
        $examen_nombre = "";
        $examenes      = Examen::where('estado', '1')->get();
        foreach ($examenes as $examen) {
            if (!is_null($request['ch' . $examen->id])) {

                $valor         = $examen->valor;
                $examen_nombre = $examen_nombre . "+" . $examen->nombre;
                if (!is_null($convenio)) {
                    $nivel    = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }

                $cont++;
                $input_det = [
                    'id_examen_orden' => $id_examen_orden,
                    'id_examen'       => $examen->id,
                    'valor'           => $valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'p_descuento'     => 0,
                    'valor_descuento' => 0,
                ];

                Examen_detalle::create($input_det);
            }
        }

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "GENERA ORDEN EXAMEN PARTICULAR",
            'dato_ant1'   => $request['id'],
            'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
            'dato_ant4'   => $examen_nombre,
        ]);

        //dd($request->all(),$cont);

        return redirect()->route('orden.index');
    }

    public function store_admin(Request $request)
    {

        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $this->validateInput_admin($request);

        $nivel    = '0';
        $convenio = Convenio::where('id_seguro', $request['id_seguro'])->where('id_empresa', $request['id_empresa'])->first();
        $seguro   = Seguro::find($request['id_seguro']);
        //dd($convenio);
        $bandera_err = false;
        if ($seguro->tipo != '0') {

            $bandera_err = true;
            $arr_campo   = ['id_seguro' => 'Seguro particular no habilitado'];
        } else {

            if (is_null($convenio)) {
                $bandera_err = true;
                $arr_campo   = ['id_seguro' => 'Convenio no habilitado', 'id_empresa' => 'Convenio no habilitado'];
            }
        }

        if ($bandera_err) {

            $usuarios    = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
            $examenes    = Examen::where('estado', '1')->orderBy('id_agrupador')->get();
            $agrupadores = Examen_Agrupador::where('estado', '1')->get();
            $seguros     = Seguro::where('inactivo', '1')->get();
            $protocolos  = Protocolo::where('estado', '1')->get();
            $empresas    = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();

            return redirect()->back()->with(['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas])->withErrors($arr_campo)->withInput(['id' => $request['id'], 'nombre1' => $request['nombre1'], 'nombre2' => $request['nombre2'], 'apellido1' => $request['apellido1'], 'apellido2' => $request['apellido2'], 'id_doctor_ieced' => $request['id_doctor_ieced'], 'id_seguro' => $request['id_seguro'], 'est_amb_hos' => $request['est_amb_hos'], 'id_protocolo' => $request['id_protocolo'], 'id_empresa' => $request['id_empresa']]);
        }

        $valor    = 0;
        $cont     = 0;
        $total    = 0;
        $examenes = Examen::where('estado', '1')->get();
        foreach ($examenes as $examen) {
            if (!is_null($request['ch' . $examen->id])) {
                $cont++;

                $valor = $examen->valor;
                if (!is_null($convenio)) {
                    $nivel = $convenio->id_nivel;
                    //dd($nivel);
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            }
        }

        $hcid = Historiaclinica::where('id_agenda', $request['id_agenda'])->first();

        $hcid_id = null;
        if (!is_null($hcid)) {
            $hcid_id = $hcid->hcid;
        }

        $input_ex = [
            'id_paciente'     => $request['id'],
            'anio'            => substr(date('Y-m-d'), 0, 4),
            'mes'             => substr(date('Y-m-d'), 5, 2),
            'id_protocolo'    => $request['id_protocolo'],
            'id_seguro'       => $request['id_seguro'],
            'id_nivel'        => $nivel,
            'est_amb_hos'     => $request['est_amb_hos'],
            'id_doctor_ieced' => $request['id_doctor_ieced'],
            'doctor_txt'      => $request['doctor_txt'],
            'observacion'     => $request['observacion'],
            'id_empresa'      => $request['id_empresa'],
            'hcid'            => $hcid_id,
            'id_agenda'       => $request['id_agenda'],
            'cantidad'        => $cont,
            'valor'           => $total,
            'total_valor'     => $total,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'fecha_orden'     => date('Y-m-d H:i:s'),
            'estado'          => '-1',

        ];

        $agenda = Agenda::find($request['id_agenda']);

        $input_ag = [

            'id_empresa'      => $request['id_empresa'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,

        ];

        $agenda->update($input_ag);

        //dd($input_ex);

        $id_examen_orden = Examen_Orden::insertGetId($input_ex);

        /*$examenes = $request['examen'];

        foreach ($examenes as $examen) {
        $input_det = [
        'id_examen_orden' => $id_examen_orden,
        'id_examen' => $examen,

        'ip_creacion' => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea' => $idusuario,
        'id_usuariomod' => $idusuario,

        ];

        Examen_detalle::create($input_det);
        } */

        $valor         = 0;
        $cont          = 0;
        $examen_nombre = "";
        $examenes      = Examen::where('estado', '1')->get();
        foreach ($examenes as $examen) {
            if (!is_null($request['ch' . $examen->id])) {

                $valor         = $examen->valor;
                $examen_nombre = $examen_nombre . "+" . $examen->nombre;
                if (!is_null($convenio)) {
                    $nivel    = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }

                $cont++;
                $input_det = [
                    'id_examen_orden' => $id_examen_orden,
                    'id_examen'       => $examen->id,
                    'valor'           => $valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'p_descuento'     => 0,
                    'valor_descuento' => 0,
                ];

                Examen_detalle::create($input_det);
            }
        }

        $paciente = Paciente::find($request['id']);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "GENERA ORDEN EXAMEN",
            'dato_ant1'   => $request['id'],
            'dato1'       => $paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2,
            'dato_ant4'   => $examen_nombre,
        ]);

        $cedula = $request['id'];

        if ($paciente->fecha_nacimiento == null || $paciente->sexo == null) {

            $pac = [
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'sexo'             => $request['sexo'],
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,
            ];
            $paciente->update($pac);
        }

        //return $this->index_admin($cedula);
        return redirect()->route('orden.index_admin', ['cedula' => $cedula]);
    }

    public function index_admin($cedula)
    {

        $fecha_hasta = Date('Y-m-d');
        $fecha       = Date('Y-m-d', strtotime('-1 month', strtotime($fecha_hasta)));

        $paciente = Paciente::find($cedula);
        $nombres  = $paciente->nombre1 . ' ' . $paciente->nombre2 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2;

        //$ordenes = $this->recupera_ordenes()->whereBetween('eo.created_at', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('eo.id_paciente', $cedula)->paginate(30);
        $ordenes = $this->recupera_ordenes()->whereBetween('eo.created_at', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('eo.id_paciente', $cedula)->where('eo.estado', '<>', '0')->paginate(100);
        //$ordenes2 = $this->recupera_ordenes()->where('eo.estado', '-1')->where('s.tipo', '0')->whereBetween('eo.created_at', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->paginate(40);

        $ordenes2 = [];

        /*$ordenes = DB::table('examen_orden as eo')->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('empresa as em','em.id','eo.id_empresa')->leftjoin('nivel as n','n.id','eo.id_nivel')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial')->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->where('eo.id_paciente',$cedula)->paginate(30); */

        $ex_det = [];
        foreach ($ordenes as $orden) {
            $txt_examen = "";
            $detalle    = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'ed.id_examen')->select('ed.*', 'e.nombre')->get();
            $bandera    = 0;
            foreach ($detalle as $value) {
                if ($bandera == 0) {
                    $txt_examen = $value->nombre;
                    $bandera    = 1;
                } else {
                    $txt_examen = $txt_examen . '+' . $value->nombre;
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }

        $seguros = Seguro::where('inactivo', '1')->get();

        $gestiones = Examen_Orden::where('pago_online', '1')->where('estado', '1')->whereNull('fecha_orden')->where('gestion', '0')->where('estado_pago', '1')->get();
        $consulta = Examen_Orden::where("estado", -1)->where('doctor_txt', 'COTIZACION GENERADA EN APP MÒVIL')->whereDate('created_at', Carbon::today())->get();
        return view('laboratorio/orden/index', ['consulta' => $consulta, 'ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguro' => null, 'seguros' => $seguros, 'ordenes2' => $ordenes2, 'gestiones' => $gestiones, 'facturadas' => 'TODAS', 'id_nivel' => null, 'nofacturados' => []]);
    }

    public function index_doctor($cedula, $agenda)
    {
        //aqui_doc
        Cookie::queue('agenda', $agenda, '1000');
        $fecha_hasta = Date('Y-m-d');

        $fecha = Date('Y-m-d', strtotime('-6 month', strtotime($fecha_hasta)));

        $paciente = Paciente::find($cedula);
        $nombres  = $paciente->nombre1 . ' ' . $paciente->nombre2 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2;

        $ordenes = $this->recupera_ordenes()->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('eo.id_paciente', $cedula)->where('eo.estado', '1')->orderBy('fecha_orden', 'asc')->paginate(30);

        /*$ordenes = DB::table('examen_orden as eo')->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('empresa as em','em.id','eo.id_empresa')->leftjoin('nivel as n','n.id','eo.id_nivel')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial')->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->where('eo.id_paciente',$cedula)->paginate(30); */

        $ex_det = [];
        foreach ($ordenes as $orden) {
            $txt_examen = "";
            $detalle    = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'ed.id_examen')->select('ed.*', 'e.nombre')->get();
            $bandera    = 0;
            foreach ($detalle as $value) {
                if ($bandera == 0) {
                    $txt_examen = $value->nombre;
                    $bandera    = 1;
                } else {
                    $txt_examen = $txt_examen . '+' . $value->nombre;
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }

        $seguros = Seguro::where('inactivo', '1')->get();

        return view('laboratorio/orden/index_doctor', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguro' => null, 'seguros' => $seguros, 'agenda' => $agenda]);
    } //INDEX_DOCTOR

    public function index_doctor_menu()
    {

        $fecha_hasta = Date('Y-m-d');

        $fecha = Date('Y-m-d');

        $ordenes = $this->recupera_ordenes()->whereBetween('eo.created_at', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('eo.estado', '1')->orderBy('fecha_orden', 'asc')->paginate(30);

        /*$ordenes = DB::table('examen_orden as eo')->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('empresa as em','em.id','eo.id_empresa')->leftjoin('nivel as n','n.id','eo.id_nivel')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial')->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->where('eo.id_paciente',$cedula)->paginate(30); */

        $ex_det = [];
        foreach ($ordenes as $orden) {
            $txt_examen = "";
            $detalle    = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'ed.id_examen')->select('ed.*', 'e.nombre')->get();
            $bandera    = 0;
            foreach ($detalle as $value) {
                if ($bandera == 0) {
                    $txt_examen = $value->nombre;
                    $bandera    = 1;
                } else {
                    $txt_examen = $txt_examen . '+' . $value->nombre;
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }

        $seguros = Seguro::where('inactivo', '1')->get();

        return view('laboratorio/orden/index_doctor', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'seguro' => null, 'seguros' => $seguros, 'agenda' => null, 'nombres' => null]);
    } //INDEX_DOCTOR

    public function store_parametro(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        //$this->validateInput($request);

        $input = [
            'nombre'          => strtoupper($request['nombre']),
            'descripcion'     => strtoupper($request['nombre']),
            'texto1'          => strtoupper($request['texto1']),
            'texto2'          => strtoupper($request['texto2']),
            'texto3'          => strtoupper($request['texto3']),
            'texto4'          => strtoupper($request['texto4']),
            'valor1'          => $request['valor1'],
            'valor2'          => $request['valor2'],
            'valor3'          => $request['valor3'],
            'valor4'          => $request['valor4'],
            'valor1g'         => $request['valor1g'],
            'valor2g'         => $request['valor2g'],
            'valor3g'         => $request['valor3g'],
            'valor4g'         => $request['valor4g'],
            'unidad1'         => strtoupper($request['unidad1']),
            'unidad2'         => strtoupper($request['unidad2']),
            'unidad3'         => strtoupper($request['unidad3']),
            'unidad4'         => strtoupper($request['unidad4']),
            'id_examen'       => $request['id_examen'],
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        Examen_parametro::create($input);

        return redirect()->route('examen.parametro', ['id_examen' => $request['id_examen']]);
    }

    private function validateInput($request)
    {

        $rules = [

            'id'              => 'required|max:10',
            'nombre1'         => 'required|max:60',
            'nombre2'         => 'required|max:60',
            'apellido1'       => 'required|max:60',
            'apellido2'       => 'required|max:60',
            'id_doctor_ieced' => 'required',
            'observacion'     => 'max:200',

        ];

        $messages = [

            'id.required'              => 'Ingrese la cédula del paciente',
            'id.max'                   => 'La cédula no puede ser mayor a :max caracteres',
            'nombre1.required'         => 'Ingresa el Nombre.',
            'nombre1.max'              => 'El nombre no puede ser mayor a :max caracteres.',
            'nombre2.required'         => 'Ingresa el Nombre.',
            'nombre2.max'              => 'El nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'       => 'Ingresa el Nombre.',
            'apellido1.max'            => 'El nombre no puede ser mayor a :max caracteres.',
            'apellido2.required'       => 'Ingresa el Nombre.',
            'apellido2.max'            => 'El nombre no puede ser mayor a :max caracteres.',
            'id_doctor_ieced.required' => 'Selecciona el doctor.',
            'observacion.max'          => 'La observación no puede ser mayor a :max caracteres.',

        ];

        $this->validate($request, $rules, $messages);
    }

    private function validateInput_admin($request)
    {

        $rules = [

            'id'              => 'required|max:10',
            'id_doctor_ieced' => 'required',
            'observacion'     => 'max:200',
            'id_empresa'      => 'required',
            'id_protocolo'    => 'required',

        ];

        $messages = [

            'id_protocolo.required'    => 'Seleccione el Protocolo',
            'id_empresa.required'      => 'Seleccione la Empresa',
            'id.required'              => 'Ingrese la cédula del paciente',
            'id.max'                   => 'La cédula no puede ser mayor a :max caracteres',
            'nombre1.required'         => 'Ingresa el Nombre.',
            'nombre1.max'              => 'El nombre no puede ser mayor a :max caracteres.',
            'nombre2.required'         => 'Ingresa el Nombre.',
            'nombre2.max'              => 'El nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'       => 'Ingresa el Nombre.',
            'apellido1.max'            => 'El nombre no puede ser mayor a :max caracteres.',
            'apellido2.required'       => 'Ingresa el Nombre.',
            'apellido2.max'            => 'El nombre no puede ser mayor a :max caracteres.',
            'id_doctor_ieced.required' => 'Selecciona el doctor.',
            'observacion.max'          => 'La observación no puede ser mayor a :max caracteres.',

        ];

        $this->validate($request, $rules, $messages);
    }

    private function validateInput2($request)
    {

        $rules = [

            'id_doctor_ieced' => 'required',
            'observacion'     => 'max:200',

        ];

        $messages = [

            'id.required'              => 'Ingrese la cédula del paciente',
            'id.max'                   => 'La cédula no puede ser mayor a :max caracteres',
            'nombre1.required'         => 'Ingresa el Nombre.',
            'nombre1.max'              => 'El nombre no puede ser mayor a :max caracteres.',
            'nombre2.required'         => 'Ingresa el Nombre.',
            'nombre2.max'              => 'El nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'       => 'Ingresa el Nombre.',
            'apellido1.max'            => 'El nombre no puede ser mayor a :max caracteres.',
            'apellido2.required'       => 'Ingresa el Nombre.',
            'apellido2.max'            => 'El nombre no puede ser mayor a :max caracteres.',
            'id_doctor_ieced.required' => 'Selecciona el doctor.',
            'observacion.max'          => 'La observación no puede ser mayor a :max caracteres.',

        ];

        $this->validate($request, $rules, $messages);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();
        //$usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->where('training', '<>', '1')->orderby('apellido1')->get();
        $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->where('training', '0')->where('uso_sistema', '0')->orderBy('apellido1')->get(); //3=DOCTORES;
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get(); se muestran todos
        $examenes    = Examen::where('publico_privado', '0')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $orden       = DB::table('examen_orden as eo')->where('eo.id', $id)->join('paciente as p', 'p.id', 'eo.id_paciente')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->first();
        $detalles    = Examen_Detalle::where('id_examen_orden', $id)->where('estado', '1')->get();
        $seguro      = Seguro::find($orden->id_seguro);
        $seguros     = Seguro::where('inactivo', '1')->get();
        $protocolos  = Protocolo::where('estado', '1')->get();
        $convenios   = DB::table('convenio as c')->join('seguros as s', 's.id', 'c.id_seguro')->join('empresa as e', 'e.id', 'c.id_empresa')->where('s.tipo', '0')->select('c.*', 's.nombre', 'e.nombrecomercial')->orderby('s.nombre')->get();
        $convenio    = Convenio::where('id_seguro', $orden->id_seguro)->where('id_empresa', $orden->id_empresa)->first();

        if (!is_null($orden)) {

            return view('laboratorio/orden/edit', ['orden' => $orden, 'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresas, 'seguros' => $seguros, 'dir' => 'rec', 'convenios' => $convenios, 'convenio' => $convenio]);
        }
    }
    public function edit1_c($id)
    {
        if ($this->rol_control()) {
            return response()->view('errors.404');
        }
        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();
        //$usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->where('training', '<>', '1')->orderby('apellido1')->get();
        $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->where('training', '0')->where('uso_sistema', '0')->orderBy('apellido1')->get(); //3=DOCTORES;
        if (Auth::user()->id_tipo_usuario == 11) {
            $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->orderBy('apellido1')->get();
        }
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get(); se muestran todos
        $examenes    = Examen::where('publico_privado', '0')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $orden       = DB::table('examen_orden as eo')->where('eo.id', $id)->join('paciente as p', 'p.id', 'eo.id_paciente')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->first();
        $detalles    = Examen_Detalle::where('id_examen_orden', $id)->where('estado', '1')->get();
        $seguro      = Seguro::find($orden->id_seguro);
        $seguros     = Seguro::where('inactivo', '1')->get();
        $protocolos  = Protocolo::where('estado', '1')->get();
        $convenios   = DB::table('convenio as c')->join('seguros as s', 's.id', 'c.id_seguro')->join('empresa as e', 'e.id', 'c.id_empresa')->where('s.tipo', '0')->select('c.*', 's.nombre', 'e.nombrecomercial')->orderby('s.nombre')->get();
        $convenio    = Convenio::where('id_seguro', $orden->id_seguro)->where('id_empresa', $orden->id_empresa)->first();

        if (!is_null($orden)) {

            return view('laboratorio/orden/edit', ['orden' => $orden, 'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresas, 'seguros' => $seguros, 'dir' => 'CON', 'convenios' => $convenios, 'convenio' => $convenio]);
        }
    }

    public function edit2($id, $dir)
    {
        //dd($dir);
        if ($this->rol_control2()) {
            return response()->view('errors.404');
        }
        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();
        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get(); se muestran todos

        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $orden       = DB::table('examen_orden as eo')->where('eo.id', $id)->join('paciente as p', 'p.id', 'eo.id_paciente')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->first();
        $detalles    = Examen_Detalle::where('id_examen_orden', $id)->where('estado', '1')->get();
        $seguro      = Seguro::find($orden->id_seguro);
        $seguros     = Seguro::where('inactivo', '1')->get();
        $protocolos  = Protocolo::where('estado', '1')->get();
        if (in_array($orden->id_seguro, [1, 4])) {

            $examenes = Examen::orderBy('id_agrupador')->get();
        } else {
            $examenes = Examen::where('publico_privado', '0')->orderBy('id_agrupador')->get();
        }

        if (!is_null($orden)) {

            return view('laboratorio/orden/edit2', ['orden' => $orden, 'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresas, 'seguros' => $seguros, 'dir' => $dir]);
        }
    }

    public function detalle($id, $dir)
    {

        $empresas    = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();
        $usuarios    = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
        $examenes    = Examen::where('publico_privado', '0')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $orden       = DB::table('examen_orden as eo')->where('eo.id', $id)->join('paciente as p', 'p.id', 'eo.id_paciente')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->first();
        $detalles    = Examen_Detalle::where('id_examen_orden', $id)->where('estado', '1')->get();
        $seguro      = Seguro::find($orden->id_seguro);
        $seguros     = Seguro::where('inactivo', '1')->get();
        $protocolos  = Protocolo::where('estado', '1')->get();
        if (!is_null($orden)) {

            return view('laboratorio/orden/detalle', ['orden' => $orden, 'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresas, 'seguros' => $seguros, 'dir' => $dir]);
        }
    }

    public function update(Request $request, $id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $xuser      = Auth::user();
        $tipo_user  = $xuser->id_tipo_usuario;

        $idusuario = $xuser->id;
        date_default_timezone_set('America/Guayaquil');
        $orden = Examen_Orden::find($id);
        $this->validateInput2($request);

        $nivel = '0';
        //$convenio = Convenio::where('id_seguro', $request['id_seguro'])->where('id_empresa', $request['id_empresa'])->first();
        $convenio = Convenio::find($request->id_convenio);
        //$seguro   = Seguro::find($request['id_seguro']);
        $seguro      = Seguro::find($convenio->id_seguro);
        $bandera_err = false;
        $fecha_orden = date('Y/m/d');
        if ($request->fecha_orden != null) {

            if ($request->fecha_orden != date('Y/m/d', strtotime($orden->fecha_orden))) {
                if ($tipo_user == '5') {

                    if ($request->fecha_orden < $fecha_orden) {

                        $bandera_err = true;
                        $arr_campo   = ['fecha_orden' => 'Fecha no puede ser menor a hoy'];
                    }
                }
            }

            $fecha_orden = $request->fecha_orden;
        }

        if ($seguro->tipo != '0') {

            $bandera_err = true;
            $arr_campo   = ['id_seguro' => 'Seguro particular no habilitado'];
        } else {

            if (is_null($convenio)) {
                $bandera_err = true;
                $arr_campo   = ['id_seguro' => 'Convenio no habilitado', 'id_empresa' => 'Convenio no habilitado'];
            }
        }

        if ($bandera_err) {
            //dd($request->all());

            $empresas    = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();
            $usuarios    = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
            $examenes    = Examen::where('estado', '1')->orderBy('id_agrupador')->get();
            $agrupadores = Examen_Agrupador::where('estado', '1')->get();
            $orden       = DB::table('examen_orden as eo')->where('eo.id', $id)->join('paciente as p', 'p.id', 'eo.id_paciente')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->first();
            $detalles    = Examen_Detalle::where('id_examen_orden', $id)->where('estado', '1')->get();
            $seguro      = Seguro::find($orden->id_seguro);
            $seguros     = Seguro::where('inactivo', '1')->get();
            $protocolos  = Protocolo::where('estado', '1')->get();
            $convenios   = DB::table('convenio as c')->join('seguros as s', 's.id', 'c.id_seguro')->join('empresa as e', 'e.id', 'c.id_empresa')->where('s.tipo', '0')->select('c.*', 's.nombre', 'e.nombrecomercial')->orderby('s.nombre')->get();
            $convenio    = Convenio::where('id_seguro', $orden->id_seguro)->where('id_empresa', $orden->id_empresa)->first();

            return redirect()->back()->with(['orden' => $orden, 'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresas, 'seguros' => $seguros, 'convenios' => $convenios, 'convenio' => $convenio])->withErrors($arr_campo)->withInput(['id_doctor_ieced' => $request['id_doctor_ieced'], 'id_seguro' => $request['id_seguro'], 'id_protocolo' => $request['id_protocolo'], 'id_empresa' => $request['id_empresa'], 'fecha_orden' => $request['fecha_orden']]);
        }

        $anio = date('Y', strtotime($fecha_orden));
        $mes  = date('m', strtotime($fecha_orden));

        if ($tipo_user == '5') {
            $input_ex1 = [
                'id_doctor_ieced' => $request['id_doctor_ieced'],
                'doctor_txt'      => $request['doctor_txt'],
                'observacion'     => $request['observacion'],
                'id_empresa'      => $convenio->id_empresa,
                'id_protocolo'    => $request['id_protocolo'],
                'id_seguro'       => $seguro->id,
                'fecha_orden'     => $fecha_orden,
                'est_amb_hos'     => $request->est_amb_hos,
                'anio'            => $anio,
                'mes'             => $mes,
                'ip_modificacion' => $ip_cliente,

                'id_usuariomod'   => $idusuario,

            ];
        } else {
            $input_ex1 = [
                'id_doctor_ieced' => $request['id_doctor_ieced'],
                'doctor_txt'      => $request['doctor_txt'],
                'observacion'     => $request['observacion'],
                'id_empresa'      => $convenio->id_empresa,
                'id_protocolo'    => $request['id_protocolo'],
                'id_seguro'       => $seguro->id,
                'fecha_convenios' => $fecha_orden,
                'est_amb_hos'     => $request->est_amb_hos,

                'ip_modificacion' => $ip_cliente,

                'id_usuariomod'   => $idusuario,

            ];
        }

        $orden->update($input_ex1);

        $detalles = Examen_Detalle::where('id_examen_orden', $id)->get();

        $valor    = 0;
        $cont     = 0;
        $total    = 0;
        $examenes = Examen::where('estado', '1')->get();
        foreach ($examenes as $examen) {
            if (!is_null($request['ch' . $examen->id])) {
                $cont++;

                $valor = $examen->valor;
                if (!is_null($convenio)) {
                    $nivel    = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            }
        }

        $input_ex2 = [

            'cantidad'        => $cont,
            'valor'           => $total,
            'total_valor'     => $total,

            'ip_modificacion' => $ip_cliente,

            'id_usuariomod'   => $idusuario,

        ];

        $orden->update($input_ex2);

        foreach ($detalles as $detalle) {
            $detalle->Delete();
        }

        $valor         = 0;
        $cont          = 0;
        $examen_nombre = "";
        $examenes      = Examen::where('estado', '1')->get();
        foreach ($examenes as $examen) {
            if (!is_null($request['ch' . $examen->id])) {

                $valor         = $examen->valor;
                $examen_nombre = $examen_nombre . "+" . $examen->nombre;
                if (!is_null($convenio)) {
                    $nivel    = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }

                $cont++;
                $input_det = [
                    'id_examen_orden' => $id,
                    'id_examen'       => $examen->id,
                    'valor'           => $valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'p_descuento'     => 0,
                    'valor_descuento' => 0,
                ];

                Examen_detalle::create($input_det);
            }
        }

        $paciente = Paciente::find($orden->id_paciente);
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "ACTUALIZA ORDEN EXAMEN",
            'dato_ant1'   => $orden->id_paciente,
            'dato1'       => $paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2,
            'dato_ant4'   => $examen_nombre,
        ]);

        $cedula = $request['id'];

        if ($tipo_user == '11') {
            $agenda = Cookie::get('agenda');
            if ($agenda != '') {
                return redirect()->route('orden.index_doctor', ['id' => $orden->id_paciente, 'agenda' => $agenda]);
            }

            return redirect()->route('orden.search_supervision');
        }

        if ($request['dir'] == 'CON') {
            //return redirect()->route('orden.index_control_b', ['id' => $id]);
            return redirect()->route('orden.index_control');
        } else {
            return redirect()->route('orden.index');
        }
    }
    public function update_particular(Request $request, $id)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $orden = Examen_Orden::find($id);
        $this->validateInput2($request);

        $nivel    = null;
        $convenio = Convenio::where('id_seguro', $request['id_seguro'])->where('id_empresa', $request['id_empresa'])->first();
        $seguro   = Seguro::find($request['id_seguro']);

        $bandera_err = false;
        /*if($seguro->tipo!='0'){

        $bandera_err=true;
        $arr_campo=['id_seguro' => 'Seguro particular no habilitado'];

        }else{

        if(is_null($convenio)){
        $bandera_err=true;
        $arr_campo=['id_seguro' => 'Convenio no habilitado','id_empresa' => 'Convenio no habilitado'];
        }
        }*/

        if ($bandera_err) {

            $empresas    = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();
            $usuarios    = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
            $examenes    = Examen::where('estado', '1')->orderBy('id_agrupador')->get();
            $agrupadores = Examen_Agrupador::where('estado', '1')->get();
            $orden       = DB::table('examen_orden as eo')->where('eo.id', $id)->join('paciente as p', 'p.id', 'eo.id_paciente')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->first();
            $detalles    = Examen_Detalle::where('id_examen_orden', $id)->where('estado', '1')->get();
            $seguro      = Seguro::find($orden->id_seguro);
            $seguros     = Seguro::where('inactivo', '1')->get();
            $protocolos  = Protocolo::where('estado', '1')->get();

            return redirect()->back()->with(['orden' => $orden, 'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresas, 'seguros' => $seguros])->withErrors($arr_campo)->withInput(['id_doctor_ieced' => $request['id_doctor_ieced'], 'id_seguro' => $request['id_seguro'], 'id_protocolo' => $request['id_protocolo'], 'id_empresa' => $request['id_empresa']]);
        }

        $input_ex1 = [
            'id_doctor_ieced' => $request['id_doctor_ieced'],
            'doctor_txt'      => $request['doctor_txt'],
            'observacion'     => $request['observacion'],
            'id_empresa'      => $request['id_empresa'],
            'id_protocolo'    => $request['id_protocolo'],
            'id_seguro'       => $request['id_seguro'],

            'ip_modificacion' => $ip_cliente,

            'id_usuariomod'   => $idusuario,

        ];

        $orden->update($input_ex1);

        //$detalles = Examen_Detalle::where('id_examen_orden', $id)->get();
        $detalles = $orden->detalles;

        $valor    = 0;
        $cont     = 0;
        $total    = 0;
        $examenes = Examen::where('estado', '1')->get();
        foreach ($examenes as $examen) {
            if (!is_null($request['ch' . $examen->id])) {
                $cont++;

                $valor = $examen->valor;
                if (!is_null($convenio)) {
                    $nivel    = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            }
        }

        $input_ex2 = [

            'cantidad'        => $cont,
            'valor'           => $total,

            'ip_modificacion' => $ip_cliente,

            'id_usuariomod'   => $idusuario,

        ];

        $orden->update($input_ex2);

        foreach ($detalles as $detalle) {
            $detalle->Delete();
        }

        $valor         = 0;
        $cont          = 0;
        $examen_nombre = "";
        $examenes      = Examen::where('estado', '1')->get();
        foreach ($examenes as $examen) {
            if (!is_null($request['ch' . $examen->id])) {

                $valor         = $examen->valor;
                $examen_nombre = $examen_nombre . "+" . $examen->nombre;
                if (!is_null($convenio)) {
                    $nivel    = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }

                $cont++;
                $input_det = [
                    'id_examen_orden' => $id,
                    'id_examen'       => $examen->id,
                    'valor'           => $valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'p_descuento'     => 0,
                    'valor_descuento' => 0,
                ];

                Examen_detalle::create($input_det);
            }
        }

        $paciente = Paciente::find($orden->id_paciente);
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "ACTUALIZA ORDEN EXAMEN PARTICULAR",
            'dato_ant1'   => $orden->id_paciente,
            'dato1'       => $paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2,
            'dato_ant4'   => $examen_nombre,
        ]);

        $cedula = $request['id'];

        if ($request['dir'] == 'CON') {
            return redirect()->route('orden.index_control_b', ['id' => $id]);
        } else {
            return redirect()->route('orden.index');
        }
    }

    public function buscapaciente($id)
    {
        $paciente = Paciente::find($id);
        if (!is_null($paciente)) {
            return $paciente;
        } else {
            return 'no';
        }
    }

    public function search(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $seguro      = $request['seguro'];
        $nombres     = $request['nombres'];
        $fecha       = $request['fecha']; //dd($fecha);
        $fecha_hasta = $request['fecha_hasta'];
        $facturadas  = $request['facturadas'];
        $id_nivel    = $request['id_nivel'];
        //dd($request->all());
        $ordenes = $this->recupera_ordenes();
        $nofacturados = [];
        if ($fecha != null && $fecha_hasta != null) {
            $nofacturados = $this->recupera_ordenes()->where('eo.estado', 1)->whereNull('comprobante')->groupBy('eo.id_seguro')->select('eo.id_seguro', DB::raw('count(*) as cantidad')); //dd($nofacturados);
        }
        /*]$ordenes = DB::table('examen_orden as eo')->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('empresa as em','em.id','eo.id_empresa')->leftjoin('nivel as n','n.id','eo.id_nivel')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial');*/

        //buscadorxpaciente
        //$ordenes2 = $this->recupera_ordenes()->where('eo.estado', '-1')->where('s.tipo', '0');
        //dd($ordenes2);
        if (!is_null($request['orden'])) {
            $ordenes = $ordenes->where('eo.id', $request['orden']);
        }

        if ($facturadas == 'FACTURADAS') {

            $ordenes = $ordenes->whereNotNull('eo.fecha_envio');
        }
        if ($facturadas == 'NO FACTURADAS') {

            $ordenes = $ordenes->whereNull('eo.fecha_envio');
        }
        if ($fecha != null) {

            //$ordenes = $ordenes->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
            $nofacturados = $nofacturados->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->get(); //dd($nofacturados);
            //$ordenes2 = $ordenes2->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }
        if ($seguro != null) {

            $ordenes = $ordenes->where('eo.id_seguro', $seguro);

            ///$ordenes2 = $ordenes2->where('eo.id_seguro', $seguro);
        }
        if ($id_nivel != null) {

            $ordenes = $ordenes->where('eo.id_nivel', $id_nivel);
        }
        if ($nombres != null) {

            $nombres2    = explode(" ", $nombres);
            $nombres_sql = '';
            $cantidad    = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
                /*$ordenes2 = $ordenes2->where(function ($jq1) use ($nombres_sql) {
            $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
            });*/
            } else {

                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
                /*$ordenes2 = $ordenes2->where(function ($jq1) use ($nombres_sql) {
            $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
            });*/
            }
        }

        //dd($ordenes->get());

        $ordenes = $ordenes->where('eo.estado', '<>', '0')->paginate(100);
        //$ordenes2 = $ordenes2->paginate(40);
        $ordenes2 = [];

        $ex_det = [];
        /*foreach ($ordenes as $orden) {
            $txt_examen = "";
            $detalle    = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'ed.id_examen')->select('ed.*', 'e.nombre')->get();
            $bandera    = 0;
            foreach ($detalle as $value) {
                if ($bandera == 0) {
                    $txt_examen = $value->nombre;
                    $bandera    = 1;
                } else {
                    $txt_examen = $txt_examen . '+' . $value->nombre;
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }*/
        $seguros = Seguro::where('inactivo', '1')->orderBy('nombre')->get();

        $gestiones = Examen_Orden::where('pago_online', '1')->where('estado', '1')->whereNull('fecha_orden')->where('gestion', '0')->where('estado_pago', '1')->get();
        $consulta = Examen_Orden::where("estado", -1)->where('doctor_txt', 'COTIZACION GENERADA EN APP MÒVIL')->whereDate('created_at', Carbon::today())->get();
        return view('laboratorio/orden/index', ['consulta' => $consulta, 'ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguro' => $seguro, 'seguros' => $seguros, 'ordenes2' => $ordenes2, 'gestiones' => $gestiones, 'facturadas' => $facturadas, 'id_nivel' => $id_nivel, 'nofacturados' => $nofacturados]);
    }

    public function search_doctor(Request $request)
    {

        if ($this->rol_supervision()) {
            return response()->view('errors.404');
        }
        $seguro      = $request['seguro'];
        $nombres     = $request['nombres'];
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];

        $ordenes = $this->recupera_ordenes();
        /*]$ordenes = DB::table('examen_orden as eo')->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('empresa as em','em.id','eo.id_empresa')->leftjoin('nivel as n','n.id','eo.id_nivel')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial');*/

        //buscadorxpaciente

        if ($fecha != null) {

            //$ordenes = $ordenes->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }
        if ($seguro != null) {

            $ordenes = $ordenes->where('eo.id_seguro', $seguro);
        }
        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            $nombres_sql = '';
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {

                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }

        //dd($ordenes->get());

        $ordenes = $ordenes->where('eo.estado', '1')->orderBy('fecha_orden', 'asc')->paginate(30);

        $ex_det = [];
        foreach ($ordenes as $orden) {
            $txt_examen = "";
            $detalle    = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'ed.id_examen')->select('ed.*', 'e.nombre')->get();
            $bandera    = 0;
            foreach ($detalle as $value) {
                if ($bandera == 0) {
                    $txt_examen = $value->nombre;
                    $bandera    = 1;
                } else {
                    $txt_examen = $txt_examen . '+' . $value->nombre;
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }
        $seguros = Seguro::all();
        return view('laboratorio/orden/index_doctor', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguro' => $seguro, 'seguros' => $seguros, 'agenda' => $request['agenda']]);
    } //INDEX_DOCTOR

    public function index_supervision(Request $request)
    {

        if ($this->rol_supervision()) {
            return response()->view('errors.404');
        }
        $nombres = $request['nombres'];
        if ($request['fecha'] == null) {
            $fecha = date('Y-m-d');
        } else {
            $fecha = $request['fecha'];
        }
        if ($request['fecha_hasta'] == null) {
            $fecha_hasta = date('Y-m-d');
        } else {
            $fecha_hasta = $request['fecha_hasta'];
        }
        $seguro = $request['seguro'];

        $ordenes = $this->recupera_ordenes();

        if ($fecha != null) {

            //$ordenes = $ordenes->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }
        if ($seguro != null) {

            $ordenes = $ordenes->where('eo.id_seguro', $seguro);
        }
        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }

        //dd($ordenes->get());
        $ordenes = $ordenes->where('eo.estado', '1')->paginate(30);

        $ex_det = [];
        foreach ($ordenes as $orden) {
            $txt_examen = "";
            $detalle    = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'ed.id_examen')->select('ed.*', 'e.nombre')->get();
            $bandera    = 0;
            foreach ($detalle as $value) {
                if ($bandera == 0) {
                    $txt_examen = $value->nombre;
                    $bandera    = 1;
                } else {
                    $txt_examen = $txt_examen . '+' . $value->nombre;
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }
        $seguros = Seguro::where('inactivo', '1')->get();
        Cookie::queue('fecha_desde', $fecha, '1000');
        Cookie::queue('fecha_hasta', $fecha_hasta, '1000');

        $niveles = array();
        return view('laboratorio/orden/index_supervision', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguro' => $seguro, 'seguros' => $seguros, 'niveles' => $niveles]);
    }

    public function search_supervision(Request $request)
    {

        if ($this->rol_supervision()) {
            return response()->view('errors.404');
        }
        $nombres = $request['nombres'];
        if ($request['fecha'] == null) {
            //$fecha = null;
            $fecha = Cookie::get('fecha_desde');
        } else {
            $fecha = $request['fecha'];
        }
        if ($request['fecha_hasta'] == null) {
            //$fecha_hasta = date('Y-m-d');
            $fecha_hasta = Cookie::get('fecha_hasta');
        } else {
            $fecha_hasta = $request['fecha_hasta'];
        }
        $seguro = $request['seguro'];

        $nivel = $request['nivel'];
        $niveles = array();


        $ordenes = $this->recupera_ordenes();

        if ($fecha != null) {

            //$ordenes = $ordenes->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }
        if ($seguro != null) {
            $ordenes = $ordenes->where('eo.id_seguro', $seguro);
            $niveles = Convenio::where('id_seguro', $seguro)->get();
        }
        if ($seguro != null) {
            $ordenes = $ordenes->where('eo.id_seguro', $seguro);
        }
        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }

        //dd($ordenes->get());
        $ordenes = $ordenes->where('eo.estado', '1')->paginate(30);

        $ex_det = [];
        foreach ($ordenes as $orden) {
            $txt_examen = "";
            $detalle    = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'ed.id_examen')->select('ed.*', 'e.nombre')->get();
            $bandera    = 0;
            foreach ($detalle as $value) {
                if ($bandera == 0) {
                    $txt_examen = $value->nombre;
                    $bandera    = 1;
                } else {
                    $txt_examen = $txt_examen . '+' . $value->nombre;
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }
        $seguros = Seguro::all();
        Cookie::queue('fecha_desde', $fecha, '1000');
        Cookie::queue('fecha_hasta', $fecha_hasta, '1000');

        return view('laboratorio/orden/index_supervision', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguro' => $seguro, 'seguros' => $seguros, 'niveles' => $niveles, 'nivel' => $nivel]);
    }

    public function index_control(Request $request)
    {

        $norden = null;
        //dd($request->all());
        if ($this->rol_control()) {
            return response()->view('errors.404');
        }
        $nombres = $request['nombres'];
        if ($request['fecha'] == null) {
            $fecha = date('Y-m-d');
        } else {
            $fecha = $request['fecha'];
        }
        if ($request['fecha_hasta'] == null) {
            $fecha_hasta = date('Y-m-d');
        } else {
            $fecha_hasta = $request['fecha_hasta'];
        }
        $seguro = $request['seguro'];

        //$ordenes = $this->recupera_ordenes();
        $ordenes = Examen_Orden::join('paciente as p', 'p.id', 'id_paciente')->leftjoin('protocolo as proto', 'proto.id', 'id_protocolo')->select('examen_orden.*', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'proto.pre_post');

        //buscadorxpaciente

        if ($fecha != null) {
            //$ordenes = $ordenes->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            $ordenes = $ordenes->whereBetween('fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }
        if ($seguro != null) {

            //$ordenes = $ordenes->where('eo.id_seguro',$seguro);
            $ordenes = $ordenes->where('id_seguro', $seguro);
        }
        if ($nombres != null) {
            $nombres2    = explode(" ", $nombres);
            $nombres_sql = '';
            $cantidad    = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                /*$ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });*/
                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {

                /*$ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });*/
                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }

        //dd($ordenes->get());

        //$ordenes = $ordenes->where('eo.estado','1')->paginate(30);
        $ordenes = $ordenes->where('examen_orden.estado', '1')->orderBy('fecha_orden', 'asc')->paginate(30);

        $seguros = Seguro::all();

        $arr_or = [];
        foreach ($ordenes as $o) {
            $hema = 0;
            $bio  = 0;
            $man  = 0;

            foreach ($o->detalles as $d) {
                if ($d->examen->maquina == '1') {
                    $hema++;
                }
                if ($d->examen->maquina == '2') {
                    $bio++;
                }
                if ($d->examen->maquina == '0') {
                    $man++;
                }
            }
            $arr_or[$o->id] = ['hema' => $hema, 'bio' => $bio, 'man' => $man];
        }

        return view('laboratorio/orden/index_control', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'nombres' => $nombres, 'seguros' => $seguros, 'seguro' => $seguro, 'arr_or' => $arr_or, 'norden' => $norden]);
    } //INDEX_CONTROL

    public function index_control_b($id)
    {
        //RECUERDA AGENDALABSCONTROLLER

        $norden = null;
        if ($this->rol_control()) {
            return response()->view('errors.404');
        }

        $orden    = Examen_Orden::find($id);
        $paciente = $orden->paciente;
        //dd($paciente);
        $seguro = null;

        $nombres = $paciente->nombre1 . ' ' . $paciente->nombre2 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2;
        //dd($nombres);

        //$fecha = $orden->created_at;
        $fecha       = $orden->fecha_orden;
        $fecha_hasta = $fecha;

        $ordenes = $this->recupera_ordenes();
        //dd($ordenes->get());
        //buscadorxpaciente

        if ($fecha != null) {

            //$ordenes = $ordenes->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($seguro != null) {

            $ordenes = $ordenes->where('eo.id_seguro', $seguro);
        }
        if ($nombres != null) {

            $nombres2    = explode(" ", $nombres);
            $nombres_sql = '';
            $cantidad    = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {

                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }

        $ordenes = $ordenes->where('eo.estado', '1')->paginate(30);

        $ex_det = [];
        foreach ($ordenes as $orden) {

            $examen_par         = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen_parametro as ep', 'ep.id_examen', 'ed.id_examen')->where('ep.estado', '1')->select('ed.id_examen')->groupBy('ed.id_examen')->get();
            $resultado          = DB::table('examen_resultado as er')->where('er.id_orden', $orden->id)->join('examen_parametro as ep', 'ep.id', 'er.id_parametro')->where('ep.estado', '1')->select('ep.id_examen')->groupBy('ep.id_examen')->get();
            $ex_det[$orden->id] = $examen_par->count() - $resultado->count();
            //dd($resultado->count(),$examen_par->count());
        }

        $seguros = Seguro::all();

        return view('laboratorio/orden/index_control', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguros' => $seguros, 'seguro' => $seguro, 'norden' => $norden]);
    }

    public function search_control(Request $request)
    {

        //
        if ($this->rol_control()) {
            return response()->view('errors.404');
        }
        $nombres = $request['nombres'];
        if ($request['fecha'] == null) {
            $fecha = null;
        } else {
            $fecha = $request['fecha'];
        }
        if ($request['fecha_hasta'] == null) {
            $fecha_hasta = date('Y-m-d');
        } else {
            $fecha_hasta = $request['fecha_hasta'];
        }
        $seguro = $request['seguro'];

        $norden = $request['orden'];

        //$ordenes = $this->recupera_ordenes();

        $ordenes = Examen_Orden::join('paciente as p', 'p.id', 'id_paciente')->leftjoin('protocolo as proto', 'proto.id', 'id_protocolo')->select('examen_orden.*', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'proto.pre_post');

        if ($fecha != null) {

            //$ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            $ordenes = $ordenes->whereBetween('fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($seguro != null) {

            //$ordenes = $ordenes->where('eo.id_seguro',$seguro);
            $ordenes = $ordenes->where('examen_orden.id_seguro', $seguro);
        }

        if ($norden != null) {
            $ordenes = $ordenes->where('examen_orden.id', $norden);
        }

        if ($nombres != null) {

            $nombres2    = explode(" ", $nombres);
            $nombres_sql = '';
            $cantidad    = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                /*$ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });*/
                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {

                /*$ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });*/
                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }

        //

        //$ordenes = $ordenes->where('eo.estado','1')->paginate(30);
        $ordenes = $ordenes->where('examen_orden.estado', '1')->orderBy('fecha_orden', 'asc')->paginate(30);
        //dd($ordenes->count());
        $arr_or = [];
        foreach ($ordenes as $o) {
            $hema = 0;
            $bio  = 0;
            $man  = 0;

            foreach ($o->detalles as $d) {
                if ($d->examen->maquina == '1') {
                    $hema++;
                }
                if ($d->examen->maquina == '2') {
                    $bio++;
                }
                if ($d->examen->maquina == '0') {
                    $man++;
                }
            }
            $arr_or[$o->id] = ['hema' => $hema, 'bio' => $bio, 'man' => $man];
        }

        //dd($arr_or);

        $seguros = Seguro::all();

        return view('laboratorio/orden/index_control', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'nombres' => $nombres, 'seguros' => $seguros, 'seguro' => $seguro, 'arr_or' => $arr_or, 'norden' => $norden]);
    } //INDEX_CONTROL

    public function fecha_convenios(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $idusuario = Auth::user()->id;
        $id        = $request['id'];

        $orden     = Examen_Orden::find($id);
        $input_ex1 = [
            'fecha_convenios' => $request['fecha_convenios'],
        ];

        $orden->update($input_ex1);
        return "ok";
    }

    public function eliminar($id)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $orden    = Examen_Orden::find($id);
        $paciente = Paciente::find($orden->id_paciente);

        if ($orden->realizado == '0') {
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "ELIMINA ORDEN EXAMEN",
                'dato_ant1'   => $orden->id_paciente,
                'dato1'       => $paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2,
                'dato_ant4'   => $orden->id,
            ]);
            $detalles = Examen_detalle::where('id_examen_orden', $orden->id)->get();
            foreach ($detalles as $detalle) {
                $detalle->Delete();
            }
            //$orden->Delete();
            $input_ex1 = [

                'estado'          => '0',
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,

            ];

            $orden->update($input_ex1);
        }

        return redirect()->route('orden.index');
    }

    /*public function realizar ($id,$i){

    if($this->rol_control()){
    return response()->view('errors.404');
    }

    $ip_cliente= $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
    date_default_timezone_set('America/Guayaquil');
    if($i == 1){
    $orden = Examen_Orden::find($id);
    $input_ex1 = [

    'realizado' => $i,
    'ip_modificacion' => $ip_cliente,
    'id_usuariomod' => $idusuario,

    ];

    $orden->update($input_ex1);

    //return redirect()->route('orden.index_control');
    }

    if($i == 3){//nunca llega a este estado
    $orden = Examen_Orden::find($id);
    $input_ex1 = [

    'realizado' => '0',
    'ip_modificacion' => $ip_cliente,
    'id_usuariomod' => $idusuario,

    ];

    $orden->update($input_ex1);

    return redirect()->route('orden.index_control');
    }

    if($i == 0|| $i== 1){
    $orden = Examen_Orden::find($id);
    $detalle = Examen_Detalle::where('id_examen_orden', $id)->get();
    $resultados =  Examen_resultado::where('id_orden', '=', $id)->get();
    $agrupador = Examen_Agrupador::all();
    $parametros = Examen_parametro::where('estado',1)->orderBy('orden')->get();
    return view('laboratorio.orden.resultados', ['orden' => $orden, 'detalle' => $detalle, 'resultados' => $resultados, 'agrupador' => $agrupador, 'parametros' => $parametros]);
    }

    }*/
    public function realizar(Request $request, $id, $maq)
    {

        //dd($request->all());
        //$i es la maquina 1 BIOMETRIA -- 2 BIOQUIMICA -- 0 manual
        if ($this->rol_control()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $orden      = Examen_Orden::find($id);
        $detalle    = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        $resultados = $orden->resultados;
        //dd($resultados);

        if ($orden->realizado == '0') {
            //PARA LAS ORDENES PUBLICAS

            $input_ex1 = [

                'realizado'       => '1',
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,

            ];

            //$orden->update($input_ex1);

        }

        if ($orden->seguro->tipo == '0') {
            $agrupador = Examen_Agrupador::all();
        } else {
            //$agrupador = Examen_Agrupador_labs::all();
            $agrupador = Examen_Agrupador_labs::orderBy('secuencia')->get();
        }

        $parametros = Examen_parametro::where('estado', '1')->orderBy('orden')->get();

        //rutina que corrige los parametros de la proteina y fraccion
        $esp_detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->where('id_examen', '1179')->first();
        if (!is_null($esp_detalle)) {
            $esp_par = Examen_parametro::where('id_examen', '1179')->whereNotNull('nombre_equipo')->get();
            foreach ($esp_par as $value) {
                $esp_resul = Examen_Resultado::where('id_orden', $orden->id)->where('id_parametro', $value->id)->first();
                if (is_null($esp_resul)) {
                    if ($value->id == '397') {
                        $act_res = Examen_Resultado::where('id_orden', $orden->id)->where('id_parametro', '43')->first();
                        if (!is_null($act_res)) {
                            $act_res->update(['id_parametro' => '397']);
                        }
                    }
                    if ($value->id == '398') {
                        $act_res = Examen_Resultado::where('id_orden', $orden->id)->where('id_parametro', '48')->first();
                        if (!is_null($act_res)) {
                            $act_res->update(['id_parametro' => '398']);
                        }
                    }
                }
            }
        }
        $esp_detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->where('id_examen', '1181')->first();
        if (!is_null($esp_detalle)) {
            $esp_par = Examen_parametro::where('id_examen', '1181')->whereNotNull('nombre_equipo')->get();
            foreach ($esp_par as $value) {
                $esp_resul = Examen_Resultado::where('id_orden', $orden->id)->where('id_parametro', $value->id)->first();
                if (is_null($esp_resul)) {
                    if ($value->id == '409') {
                        $act_res = Examen_Resultado::where('id_orden', $orden->id)->where('id_parametro', '39')->first();
                        if (!is_null($act_res)) {
                            $act_res->update(['id_parametro' => '409']);
                        }
                    }
                }
            }
        }
        //rutina que corrige los parametros de la proteina y fraccion
        //return "hola";
        //dd($orden);
        return view('laboratorio.orden.resultados', ['orden' => $orden, 'agrupador' => $agrupador, 'parametros' => $parametros, 'maq' => $maq, 'fecha' => $request->fecha, 'fecha_hasta' => $request->fecha_hasta, 'seguro' => $request->seguro, 'nombres' => $request->nombres, 'detalle' => $detalle, 'resultados' => $resultados]);
    }

    public function puede_imprimir($id) //esta
    {

        $orden      = Examen_Orden::find($id);
        $detalle    = $orden->detalles;
        $resultados = $orden->resultados;
        //$parametros = Examen_parametro::orderBy('orden')->get();

        $cant_par = 0;
        foreach ($detalle as $d) {
            //$parametros = $parametros->where('id_examen', $d->id_examen);
            if ($d->id_examen == '639') {
                //$xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0')->where('certificado', '1');
                $xpar = $resultados->where('id_examen', '639')->where('certificado', '1');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count();
                } else {
                    $cant_par = $cant_par + 10;
                }
                //$cant_par++;
            } else {
                if ($d->examen->no_resultado == '0') {

                    if (count($d->parametros->where('estado', '1')) == '0') {
                        $cant_par++;
                    }
                    if ($d->examen->sexo_n_s == '0') {
                        $parametro_nuevo = $d->parametros->where('sexo', '3')->where('estado', '1');
                    } else {
                        $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo)->where('estado', '1');
                    }
                    foreach ($parametro_nuevo as $p) {
                        $cant_par++;
                    }
                }
            }
        }

        $certificados = 0;
        $cantidad     = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;
            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }

        return ['cantidad' => $cantidad, 'certificados' => $certificados, 'cant_par' => $cant_par];
    }

    //WALTER PASAR A GYE CREAR UNA RUTA GET PARA PASAR ANIO Y MES AGREGAR CAMPO pct_resultado A EXAMEN_ORDEN
    public function calcula_porcentaje($anio, $mes)
    {
        $ordenes = Examen_Orden::where('anio', $anio)->where('mes', $mes)->where('estado', 1)->get();
        if ($mes == 'TODOS') {
            $ordenes = Examen_Orden::where('anio', $anio)->where('estado', 1)->get();
        }
        foreach ($ordenes as $orden) {
            $valores = $this->puede_imprimir($orden->id);
            $cantidad = $valores['cantidad'];
            $certificados = $valores['certificados'];
            $cant_par = $valores['cant_par'];
            if ($cant_par == 0) {
                $pct = 0;
            } else {
                $pct = ($certificados / $cant_par) * 100;
            }
            $orden->update(['pct_resultado' => $pct]);
        }
        return "oka 0702";
    }

    public function imprimir_resultado($id)
    {
        //dd($poce);
        $tipo_usuario  = Auth::user()->id_tipo_usuario;
        $orden    = Examen_Orden::find($id);
        $paciente = Paciente::find($orden->id_paciente);
        $user     = User::find($paciente->id_usuario);
        //$detalle = $orden->detalles;
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        //dd($detalle);
        $resultados = $orden->resultados;
        $parametros = Examen_parametro::orderBy('orden')->get();

        //Recalcula Porcentaje
        $cant_par = 0;
        foreach ($detalle as $d) {
            //$parametros = $parametros->where('id_examen', $d->id_examen);
            if ($d->id_examen == '639') {
                $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count(); //OK
                } else {
                    $cant_par = $cant_par + 10; // PENDIENTE
                }
                //$cant_par++;
            } else {
                if ($d->examen->no_resultado == '0') {

                    if (count($d->parametros->where('estado', '1')) == '0') { // PENDIENTE
                        $cant_par++;
                    }
                    if ($d->examen->sexo_n_s == '0') {
                        $parametro_nuevo = $d->parametros->where('sexo', '3')->where('estado', '1');
                    } else {
                        $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo)->where('estado', '1');
                    }
                    foreach ($parametro_nuevo as $p) {
                        $cant_par++;
                    }
                }
            }
        }

        $certificados = 0;
        $cantidad     = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;
            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }

        if ($cant_par == '0') {
            $pct = 0;
        } else {
            $pct = $certificados / $cant_par * 100;
        }
        //dd($pct);
        //dd($detalle);
        // Fin recalcula Porcentaje

        if ($orden->seguro->tipo == '0') {
            $agrupador = Examen_Agrupador::all();
        } else {
            //$agrupador = Examen_Agrupador_labs::all();
            $agrupador = Examen_Agrupador_labs::orderBy('secuencia')->get();
        }

        $ucreador = $orden->crea;
        $age      = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        //dd($age,$orden->paciente->fecha_nacimiento);

        $vistaurl = "laboratorio.orden.resultados_pdf";
        $view     = \View::make($vistaurl, compact('orden', 'pct', 'detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador', 'user'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        //return $view;
        $vt_name = '';
        if ($tipo_usuario == 11) {
            $vt_name = 'C_RESULTADOS.pdf';
            return $pdf->stream($vt_name);
        }
        return $pdf->stream('resultado-' . $id . '.pdf', array("Attachment" => false));
        exit(0);
    }

    public function ver_doctor($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $orden   = Examen_Orden::find($id);
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();

        if ($orden->seguro->tipo == '0') {
            $agrupador = Examen_Agrupador::all();
        } else {
            $agrupador = Examen_Agrupador_labs::all();
        }

        $parametros = Examen_parametro::orderBy('orden')->get();

        return view('laboratorio.orden.resultados_convenios', ['orden' => $orden, 'agrupador' => $agrupador, 'parametros' => $parametros, 'detalle' => $detalle]);
    }

    public function ver_convenios($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $orden      = Examen_Orden::find($id);
        $detalle    = Examen_Detalle::where('id_examen_orden', $id)->get();
        $resultados = Examen_resultado::where('id_orden', '=', $id)->get();
        $agrupador  = Examen_Agrupador::all();
        $parametros = Examen_parametro::where('estado', 1)->orderBy('orden')->get();
        return view('laboratorio.orden.resultados_doctor', ['orden' => $orden, 'detalle' => $detalle, 'resultados' => $resultados, 'agrupador' => $agrupador, 'parametros' => $parametros]);
    }

    public function crea_modifica($id_orden, $id_parametro)
    {
        $parametro = Examen_parametro::find($id_parametro);
        $resultado = Examen_resultado::where('id_orden', $id_orden)->where('id_parametro', $id_parametro)->first();

        return view('laboratorio.orden.modal', ['resultado' => $resultado, 'parametro' => $parametro, 'id_orden' => $id_orden]);
    }

    public function guarda_actualiza_resultados(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id_parametro = $request['id_parametro'];
        $id_orden     = $request['id_orden'];
        $parametro    = Examen_parametro::find($id_parametro);
        $resultado    = Examen_resultado::where('id_orden', $id_orden)->where('id_parametro', $id_parametro)->first();

        if ($request['valor'] == null) {
            $valor = 0;
        } else {
            $valor = $request['valor'];
        }

        if ($resultado != "") {
            $input_ex1 = [

                'valor'           => $valor,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,

            ];

            $resultado->update($input_ex1);
        } else {
            $input_det = [
                'id_orden'        => $id_orden,
                'id_parametro'    => $id_parametro,
                'valor'           => $valor,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,

            ];

            Examen_resultado::create($input_det);
        }
        return [$id_parametro, $request['valor']];
    }

    public function reporte(Request $request) //COMISIONES DETALLE

    {

        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $nombres     = $request['nombres'];
        $seguro      = $request['seguro'];

        //$ordenes = Examen_Orden::whereBetween('examen_orden.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('paciente as p', 'p.id', 'examen_orden.id_paciente')->join('seguros as s', 's.id', 'examen_orden.id_seguro')->leftjoin('users as d', 'd.id', 'examen_orden.id_doctor_ieced')->leftjoin('forma_de_pago as fp', 'fp.id', 'examen_orden.id_forma_de_pago')->leftjoin('nivel as nv', 'nv.id', 'examen_orden.id_nivel')->select('examen_orden.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'd.apellido2 as dapellido2', 's.nombre as snombre', 'fp.nombre as fpnombre', 's.tipo', 'p.origen', 'p.origen2', 'nv.nombre as nv_nombre')->where('examen_orden.realizado', '1')->where('examen_orden.estado', '1');
        $ordenes = Examen_Orden::whereBetween('examen_orden.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('paciente as p', 'p.id', 'examen_orden.id_paciente')->join('seguros as s', 's.id', 'examen_orden.id_seguro')->leftjoin('users as d', 'd.id', 'examen_orden.id_doctor_ieced')->leftjoin('nivel as nv', 'nv.id', 'examen_orden.id_nivel')->select('examen_orden.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'd.apellido2 as dapellido2', 's.nombre as snombre', 's.tipo', 'p.origen', 'p.origen2', 'nv.nombre as nv_nombre')->where('examen_orden.realizado', '1')->where('examen_orden.estado', '1');
        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }
        if ($seguro != null) {

            $ordenes = $ordenes->where('examen_orden.id_seguro', $seguro);
        }

        $ordenes = $ordenes->get();

        $ex_det = [];

        $i = 0;

        $fecha_d = date('Y/m/d');

        Excel::create('Examenes-' . $fecha_d, function ($excel) use ($ordenes, $ex_det) {

            $excel->sheet('Examenes', function ($sheet) use ($ordenes, $ex_det) {
                $empresa_labs    = Empresa::where('prioridad_labs', '1')->first();
                $fecha_d = date('Y/m/d');
                $i       = 4;

                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);

                $sheet->cells('A1:AR3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });
                $sheet->cells('A1:AR3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->mergeCells('A1:AR1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO DE ORDENES DE LABORATORIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA FACTURA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NIVEL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H3', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FACTURACION PUBLICOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HUMANLABS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REFERIDO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('8%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('2%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('1%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMISION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CODIGO EXTERNO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FACTURA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FACTURA VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO %');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('V3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('W3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REFERENCIA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('X3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EFECTIVO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TARJETA CREDITO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Z3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('7% T/C');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AA3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TAREJETA DEBITO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AB3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('4.5% T/D');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AC3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TRANSFERENCIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AD3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CHEQUE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AE3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PENDIENTE DE PAGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AF3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PAGO EN LINEA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AG3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AH3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MOTIVO DESCUENTO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AI3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESENCIAL/DOMICILIO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AJ3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CUPON');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AK3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VENDEDORA CUPON');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AL3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AM3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REFERENCIA PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cant = 1;
                $total = 0;
                $sub_total = 0;
                $dcto = 0;
                $recargo = 0;
                $recargo_cre = 0;
                $recargo_deb = 0;
                foreach ($ordenes as $value) {
                    $txtcolor = '#000000';

                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor, $cant) {
                        // manipulate the cel
                        $cell->setValue($cant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fecha_orden, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $fecha_factura = '';
                    //dd($value->comprobante);
                    if ($value->comprobante != null) {
                        $venta = Ct_ventas::where('nro_comprobante', $value->comprobante)->where('id_empresa', $empresa_labs)->first();
                        if (!is_null($venta)) {
                            $fecha_factura = $venta->fecha;
                        }
                    }

                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor, $fecha_factura) {
                        // manipulate the cel
                        $cell->setValue($fecha_factura);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }

                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->snombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->nv_nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->dnombre1 . ' ' . $value->dapellido1 . ' ' . $value->dapellido2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    if (($value->anio <= '2019') || ($value->anio == '2020' && $value->mes < '11')) {

                        $acum_hl = 0;
                        $acum_ex = 0;
                        $val_il = 0;
                        $val_2 = 0;
                        $val_1 = 0;
                        $labs = 0;
                        /*if ($value->tipo != '0') {

                            $detalles = $value->detalles;

                            foreach ($detalles as $detalle) {

                                if ($value->codigo == null) {
                                    if ($detalle->human_labs == '1') {
                                        $per_hl = $detalle->p_comision;
                                        $labs += $detalle->valor;
                                        $acum_hl += $per_hl * $detalle->valor;
                                    }
                                } else {

                                    $per_hl = $detalle->p_comision;
                                    $acum_ex += $per_hl * $detalle->valor;
                                }
                            }
                        }
                        if ($acum_ex > 0) {
                            $acum_ex -= $value->descuento_valor * $per_hl;
                        }
                        if ($acum_hl > 0) {
                            $acum_hl -= $value->descuento_valor * $per_hl;
                        }

                        if ($acum_hl < 0) {
                            $acum_hl = 0;
                        }
                        if ($acum_ex < 0) {
                            $acum_ex = 0;
                        }

                        $val_hl = $labs; $val_10 = $acum_hl; $val_ex = round($acum_ex, 2);
                        $val_il = round($value->valor - $val_hl, 2);*/
                    } else {
                        /*$examen_orden = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $value->id)->join('examen as e', 'e.id', 'ed.id_examen')
                            ->where('e.humanlabs', '1')->select('ed.id_examen_orden')->selectRaw('sum(ed.valor) as valor')->groupBy('ed.id_examen_orden')->first();

                        $val_hl = 0;
                        $val_il = 0;
                        $val_10 = 0;
                        $val_ex = 0;
                        $val_2  = 0; $val_1  = 0;
                        $per_hl = 0.08;
                        //$per_il = 0.02;
                        //$per_pb = 0.01;
                        $per_il = 0.00;
                        $per_pb = 0.00;
                        $per_ex = 0.1;

                        if ($value->tipo != '0') {

                            if (!is_null($examen_orden)) {
                                $val_hl = round($examen_orden->valor, 2);
                            }
                            $val_il = round($value->valor, 2) - $val_hl;

                            if ($value->codigo == null) {
                                //$val_10 = $val_hl * $per_hl;
                                $val_hl_nw = $val_hl - $value->descuento_valor;
                                if ($val_hl_nw < 0) {
                                    $val_hl_nw = 0;
                                }
                                $val_10 = $val_hl_nw * $per_hl;
                                $val_2  = $val_il * $per_il;
                            }
                        } else {
                            if ($value->codigo == null) {
                                $val_1 = round($value->valor * $per_pb, 2);
                            }
                        }
                        if ($value->codigo != null) {

                            $val_ex_nw = $value->valor - $value->descuento_valor;
                            if ($val_ex_nw < 0) {
                                $val_ex_nw = 0;
                            }
                            //$val_ex = round($value->valor * $per_ex, 2);
                            $val_ex = round($val_ex_nw * $per_ex, 2);
                            if ($value->codigo == '1270') {
                                $val_ex = 0;
                            }
                        }*/
                    }
                    $val_hl = 0;
                    $val_il = 0;
                    $val_10 = 0;
                    $val_ex = 0;
                    $val_2 = 0;
                    $val_1 = 0;
                    $val_ex = 0;
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->total_nivel2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($value, $val_hl) {
                        // manipulate the cel HUMANLABS
                        $cell->setValue($val_hl);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('L' . $i, function ($cell) use ($value, $val_il) {
                        // manipulate the cel INTERLAB
                        $cell->setValue($val_il);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('M' . $i, function ($cell) use ($value, $val_10) {
                        // manipulate the cel 10%
                        $cell->setValue($val_10);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N' . $i, function ($cell) use ($value, $val_2) {
                        // manipulate the cel 2%
                        $cell->setValue($val_2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O' . $i, function ($cell) use ($value, $val_1) {
                        // manipulate the cel 1%
                        $cell->setValue($val_1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($value, $val_1, $val_ex) {
                        // manipulate the cel 1%
                        $cell->setValue($val_ex);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    if ($value->tipo != '0') {
                        $sheet->cell('Q' . $i, function ($cell) use ($value, $val_10, $val_2, $val_ex) {
                            // manipulate the cel COMISION
                            $cell->setValue($val_10 + $val_2 + $val_ex);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } else {
                        $sheet->cell('Q' . $i, function ($cell) use ($value, $val_1, $val_ex) {
                            // manipulate the cel COMISION
                            $cell->setValue($val_1 + $val_ex);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }

                    $sheet->cell('R' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->codigo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $factura     = '';
                    $pago_online = DB::table('pagosenlinea')->where('tipo', 'examen_orden')->where('clave', $value->id)->first();

                    if (!is_null($value->comprobante)) {
                        $factura = $value->comprobante;
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->comprobante);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } else {
                        if (!is_null($pago_online)) {
                            $factura = $pago_online->nro_comprobante;
                        }
                        $sheet->cell('S' . $i, function ($cell) use ($value, $factura) {
                            // manipulate the cel
                            $cell->setValue($factura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $valor_factura = 0;
                    $venta         = Ct_ventas::where('nro_comprobante', $factura)->where('id_empresa', $empresa_labs)->first();
                    if (!is_null($venta)) {
                        $valor_factura = $venta->total_final;
                    }
                    $sheet->cell('T' . $i, function ($cell) use ($valor_factura) {
                        // manipulate the cel
                        $cell->setValue($valor_factura);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('U' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuento_p);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('V' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuento_valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $referencia = '';
                    foreach ($value->detalle_forma_pago as $fp) {
                        if ($referencia == '') {
                            $referencia = $fp->tipo_pago->nombre;
                        } else {
                            $referencia = $referencia . '+' . $fp->tipo_pago->nombre;
                        }
                        if ($fp->banco != null) {
                            $referencia = $referencia . ' BCO:' . $fp->bancos->nombre;
                        }
                        if ($fp->tipo_tarjeta != null) {
                            $referencia = $referencia . ' TRJ:' . $fp->tarjetas->nombre;
                        }
                        if ($fp->numero != null) {
                            $referencia = $referencia . ' NRO:' . $fp->numero;
                        }
                    }
                    $forma_pago = null;
                    if ($referencia == '') {
                        $forma_pago = Forma_de_pago::where('id', $value->id_forma_de_pago)->first();
                        if (!is_null($forma_pago)) {
                            $referencia = $forma_pago->nombre;
                        }
                        if ($referencia == '') {
                            $referencia = 'EFECTIVO/CHEQUE';
                            if ($value->pago_online) {
                                $referencia = 'PAGO EN LINEA';
                            }
                        }
                    }

                    $sheet->cell('W' . $i, function ($cell) use ($referencia) {
                        // manipulate the cel
                        $cell->setValue($referencia);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    if ($referencia == 'EFECTIVO/CHEQUE') {
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->total_valor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } else {
                        $efectivo = $value->detalle_forma_pago->where('id_tipo_pago', '1')->sum('valor');
                        $sheet->cell('X' . $i, function ($cell) use ($efectivo) {
                            // manipulate the cel
                            $cell->setValue($efectivo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $credito = $value->detalle_forma_pago->where('id_tipo_pago', '4')->sum('valor');
                    if ($credito == 0) {
                        if (!is_null($forma_pago)) {
                            if ($forma_pago->id == 3) {
                                $credito = $value->total_valor - $value->recargo_valor;
                            }
                        }
                    }
                    $sheet->cell('Y' . $i, function ($cell) use ($credito) {
                        // manipulate the cel
                        $cell->setValue($credito);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $cre_fee = $value->detalle_forma_pago->where('id_tipo_pago', '4')->sum('p_fi');
                    if ($cre_fee == 0) {
                        if (!is_null($forma_pago)) {
                            if ($forma_pago->id == 3) {
                                $cre_fee = $value->recargo_valor;
                            }
                        }
                    }
                    $sheet->cell('Z' . $i, function ($cell) use ($cre_fee) {
                        // manipulate the cel
                        $cell->setValue($cre_fee);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $recargo_cre += $cre_fee;
                    $debito = $value->detalle_forma_pago->where('id_tipo_pago', '6')->sum('valor');
                    if ($debito == 0) {
                        if (!is_null($forma_pago)) {
                            if ($forma_pago->id == 2) {
                                $debito = $value->total_valor - $value->recargo_valor;
                            }
                        }
                    }
                    $sheet->cell('AA' . $i, function ($cell) use ($debito) {
                        // manipulate the cel
                        $cell->setValue($debito);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $deb_fee = $value->detalle_forma_pago->where('id_tipo_pago', '6')->sum('p_fi');
                    if ($deb_fee == 0) {
                        if (!is_null($forma_pago)) {
                            if ($forma_pago->id == 2) {
                                $deb_fee = $value->recargo_valor;
                            }
                        }
                    }
                    $recargo_deb += $deb_fee;
                    $sheet->cell('AB' . $i, function ($cell) use ($deb_fee) {
                        // manipulate the cel
                        $cell->setValue($deb_fee);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $transfer = $value->detalle_forma_pago->where('id_tipo_pago', '5')->sum('valor');
                    $sheet->cell('AC' . $i, function ($cell) use ($transfer) {
                        // manipulate the cel
                        $cell->setValue($transfer);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $cheque = $value->detalle_forma_pago->where('id_tipo_pago', '2')->sum('valor');
                    $sheet->cell('AD' . $i, function ($cell) use ($cheque) {
                        // manipulate the cel
                        $cell->setValue($cheque);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $ppago = $value->detalle_forma_pago->where('id_tipo_pago', '7')->sum('valor');
                    $sheet->cell('AE' . $i, function ($cell) use ($ppago) {
                        // manipulate the cel
                        $cell->setValue($ppago);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $ponline = 0;
                    if ($value->pago_online == '1') {
                        $ponline = $value->total_valor;
                    }
                    $sheet->cell('AF' . $i, function ($cell) use ($ponline) {
                        // manipulate the cel
                        $cell->setValue($ponline);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('AG' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->total_valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('AH' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->motivo_descuento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    if ($value->pres_dom == '0') {
                        $dtxt = 'PRESENCIAL';
                    } else {
                        $dtxt = 'DOMICILIO';
                    }
                    $sheet->cell('AI' . $i, function ($cell) use ($value, $dtxt) {
                        // manipulate the cel
                        $cell->setValue($dtxt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AJ' . $i, function ($cell) use ($value, $dtxt) {
                        // manipulate the cel
                        $cell->setValue($value->ticket);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $cupon = null;
                    if ($value->ticket != null) {

                        $cupon = Cupones::where('inferior', '<=', $value->ticket)->where('superior', '>=', $value->ticket)->first();
                        //dd($value->ticket,$cupon);
                    }
                    $sheet->cell('AK' . $i, function ($cell) use ($value, $cupon) {
                        // manipulate the cel
                        if (!is_null($cupon)) {
                            $cell->setValue($cupon->apellidos . ' ' . $cupon->nombres);
                        } else {
                            $cell->setValue('');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AL' . $i, function ($cell) use ($value, $cupon) {
                        // manipulate the cel

                        $cell->setValue($value->id);

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AM' . $i, function ($cell) use ($value, $cupon) {
                        // manipulate the cel

                        $cell->setValue($value->origen . ' ' . $value->origen2);

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    /*elseif(!is_null($pago_online)){
                    $factura = $pago_online->nro_comprobante;
                    }
                    $sheet->cell('AF' . $i, function ($cell) use ($value, $factura) {
                    // manipulate the cel
                    $cell->setValue($factura);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });*/

                    $i = $i + 1;

                    $cant = $cant + 1;
                    $total += $value->total_valor;
                    $sub_total += $value->valor;
                    $dcto += $value->descuento_valor;
                    //$recargo   += $value->recargo_valor;
                }
                $sheet->getStyle('I4:Q' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('T4:AG' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                /*$sheet->getStyle('U4:U' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('V4:AE' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AF4:AF' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');*/
                /*$sheet->getStyle('U6:U' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('Q6:R' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AB6:AB' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AE6:AE' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AJ6:AJ' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AL6:AM' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');*/

                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL PACIENTES:');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('B' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue($cant - 1);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('H' . $i, function ($cell) use ($sub_total) {
                    // manipulate the cel
                    $cell->setValue($sub_total);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('I' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('L' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('M' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('N' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('O' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('P' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('Q' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('R' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('S' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('T' . $i, function ($cell) use ($dcto) {
                    // manipulate the cel
                    $cell->setValue($dcto);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                /*$sheet->cell('T' . $i, function ($cell) use ($cant) {
                // manipulate the cel
                $cell->setValue('RECARGO/TOTAL');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
                });*/
                $sheet->cell('U' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                /*$sheet->cell('U' . $i, function ($cell) use ($recargo) {
                // manipulate the cel
                $cell->setValue($recargo);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
                });*/
                $sheet->cell('V' . $i, function ($cell) use ($recargo) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                /*$sheet->cell('V' . $i, function ($cell) use ($total) {
                // manipulate the cel
                $cell->setValue($total);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
                });
                $sheet->cell('W' . $i, function ($cell) use ($total) {
                // manipulate the cel
                $cell->setValue($total);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
                });*/
                $sheet->cell('W' . $i, function ($cell) use ($total) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('X' . $i, function ($cell) use ($recargo_cre) {
                    // manipulate the cel
                    $cell->setValue($recargo_cre);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                /*$sheet->cell('X' . $i, function ($cell) use ($cant) {
                // manipulate the cel
                $cell->setValue('INGRESO');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
                });*/
                $sheet->cell('Y' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                /*$sheet->cell('Y' . $i, function ($cell) use ($total,$recargo) {
                // manipulate the cel
                $cell->setValue($total - $recargo);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
                });*/
                $sheet->cell('Z' . $i, function ($cell) use ($recargo_deb) {
                    // manipulate the cel
                    $cell->setValue($recargo_deb);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AA' . $i, function ($cell) use ($recargo_deb) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AB' . $i, function ($cell) use ($recargo_deb) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AC' . $i, function ($cell) use ($recargo_deb) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AD' . $i, function ($cell) use ($recargo_deb) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AE' . $i, function ($cell) use ($total) {
                    // manipulate the cel
                    $cell->setValue($total);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $recargo = $recargo_deb + $recargo_cre;
                $sheet->cell('AF' . $i, function ($cell) use ($total, $recargo) {
                    // manipulate the cel
                    $cell->setValue($total - $recargo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AG' . $i, function ($cell) use ($recargo_deb) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AH' . $i, function ($cell) use ($recargo_deb) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AI' . $i, function ($cell) use ($recargo_deb) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
            });
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(22)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(17)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(19)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(19)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(19)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(13)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AG")->setWidth(19)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AI")->setWidth(13)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AJ")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AK")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AN")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AO")->setWidth(22)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AP")->setWidth(13)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AQ")->setWidth(19)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AL")->setWidth(19)->setAutosize(false);
        })->export('xlsx');
    }

    public function reporte_comercial(Request $request) //COMISIONES DETALLE
    {
        //dd($request->all());
        $fecha        = $request['fecha'];
        $fecha_hasta  = $request['fecha_hasta'];
        $nombres      = $request['nombres'];
        $seguro       = $request['seguro'];
        $ordenes = Examen_Orden::whereBetween('examen_orden.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('paciente as p', 'p.id', 'examen_orden.id_paciente')->join('seguros as s', 's.id', 'examen_orden.id_seguro')->leftjoin('users as d', 'd.id', 'examen_orden.id_doctor_ieced')->leftjoin('forma_de_pago as fp', 'fp.id', 'examen_orden.id_forma_de_pago')->leftjoin('nivel as nv', 'nv.id', 'examen_orden.id_nivel')->select('examen_orden.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'd.apellido2 as dapellido2', 's.nombre as snombre', 'fp.nombre as fpnombre', 's.tipo', 'p.origen', 'p.origen2', 'nv.nombre as nv_nombre')->where('examen_orden.realizado', '1')->where('examen_orden.estado', '1')->where('s.nombre', 'P. EMPRESARIAL')->orderBy('examen_orden.fecha_orden', 'asc');
        //dd($ordenes);
        if ($nombres != null) {
            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);
            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {
                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }
        if ($seguro != null) {
            $ordenes = $ordenes->where('examen_orden.id_seguro', $seguro);
        }
        $ordenes = $ordenes->get();
        $ex_det = [];
        $i = 0;
        $fecha_d = date('Y/m/d');
        Excel::create('Examenes-' . $fecha_d, function ($excel) use ($ordenes, $ex_det) {
            $excel->sheet('Examenes', function ($sheet) use ($ordenes, $ex_det) {
                $empresa_labs    = Empresa::where('prioridad_labs', '1')->first();
                $fecha_d = date('Y/m/d');
                $i       = 3;
                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);
                $sheet->cell('A1:M2', function ($cell) {
                    // manipulate the range of cells
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#D1F2EB');
                });
                $sheet->mergeCells('A1:M1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setValue('LISTADO DE ORDENES DE LABORATORIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NIVEL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FACTURA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO %');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $cant = 1;
                $total = 0;
                $sub_total = 0;
                $dcto = 0;
                $recargo = 0;
                $recargo_cre = 0;
                $recargo_deb = 0;
                foreach ($ordenes as $value) {
                    $txtcolor = '#000000';
                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor, $cant) {
                        // manipulate the cel
                        $cell->setValue($cant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fecha_orden, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }
                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor) {
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {
                        $cell->setValue($value->snombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {
                        $cell->setValue($value->nv_nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->dnombre1 . ' ' . $value->dapellido1 . ' ' . $value->dapellido2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->comprobante);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuento_p);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuento_valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->total_valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i = $i + 1;
                    $cant = $cant + 1;
                    $total += $value->total_valor;
                    $sub_total += $value->valor;
                    $dcto += $value->descuento_valor;
                    //$recargo   += $value->recargo_valor;
                }
                //$sheet->getStyle('I4:Q' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                ///$sheet->getStyle('T4:AG' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                /*$sheet->getStyle('U4:U' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('V4:AE' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AF4:AF' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');*/
                /*$sheet->getStyle('U6:U' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('Q6:R' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AB6:AB' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AE6:AE' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AJ6:AJ' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AL6:AM' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');*/
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL PACIENTES:');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue($cant - 1);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H' . $i, function ($cell) use ($sub_total) {
                    // manipulate the cel
                    $cell->setValue($sub_total);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->cell('I' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K' . $i, function ($cell) use ($dcto) {
                    // manipulate the cel
                    $cell->setValue($dcto);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $recargo = $recargo_deb + $recargo_cre;
                $sheet->cell('L' . $i, function ($cell) use ($total, $recargo) {
                    // manipulate the cel
                    $cell->setValue($total - $recargo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->cell('M' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
            });
            // $excel->getActiveSheet()->getColumnDimension("H")->setWidth(22)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("I")->setWidth(12)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("O")->setWidth(10)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("P")->setWidth(17)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("X")->setWidth(16)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(19)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(25)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(19)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(19)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(15)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(13)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("AG")->setWidth(19)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("AI")->setWidth(13)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("AJ")->setWidth(15)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("AK")->setWidth(11)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("AN")->setWidth(18)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("AO")->setWidth(22)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("AP")->setWidth(13)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("AQ")->setWidth(19)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("AL")->setWidth(19)->setAutosize(false);
        })->export('xlsx');
    }

    public function reporte_solo_ordenes(Request $request) //SOLO ORDENES
    {
        $fecha        = $request['fecha'];
        $fecha_hasta  = $request['fecha_hasta'];
        $nombres      = $request['nombres'];
        $seguro       = $request['seguro'];
        $ordenes = Examen_Orden::whereBetween('examen_orden.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])
        ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
        ->leftjoin('users as u', 'u.id', 'examen_orden.id_paciente')
        ->orderBy('examen_orden.fecha_orden', 'asc');
        if ($seguro != null) {
            $ordenes = $ordenes->where('examen_orden.id_seguro', $seguro);
        }
        $ordenes = $ordenes->select(
            'examen_orden.*',
            'u.telefono1',
            'u.telefono2',
            DB::raw("IFNULL(u.email, '') as email"),
            DB::raw("(SELECT a.fechaini
                FROM agenda AS a
                WHERE a.id_paciente = examen_orden.id_paciente
                    AND a.fechaini >= NOW()
                    AND a.proc_consul IN (0, 1)
                ORDER BY a.fechaini ASC
                LIMIT 1) AS proxima_visita"),
            DB::raw("(SELECT eos.observacion
                FROM examen_orden_seguimiento AS eos
                WHERE eos.id_examen_orden = examen_orden.id
                    AND eos.estado_registro = '1'
                ORDER BY eos.created_at DESC, eos.id DESC
                LIMIT 1) AS observacion_seguimiento")
        )->get();
        $ex_det = [];
        $i = 0;
        $fecha_d = date('Y/m/d');
        Excel::create('Examenes-' . $fecha_d, function ($excel) use ($ordenes, $ex_det) {
            $excel->sheet('Examenes', function ($sheet) use ($ordenes, $ex_det) {
                $empresa_labs    = Empresa::where('prioridad_labs', '1')->first();
                $fecha_d = date('Y/m/d');
                $i       = 3;
                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);
                $sheet->cell('A1:T2', function ($cell) {
                    // manipulate the range of cells
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#D1F2EB');
                });
                $sheet->mergeCells('A1:T1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setValue('LISTADO DE ORDENES DE LABORATORIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NIVEL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FACTURA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO %');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CREADO POR:');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONO 1');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONO 2');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Correo');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S2', function ($cell) {
                    $cell->setValue('PROXIMA VISITA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T2', function ($cell) {
                    $cell->setValue('OBSERVACION DE SEGUIMIENTO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->setWidth('S', 20);
                $sheet->setWidth('T', 45);
                $cant = 1;
                $total = 0;
                $sub_total = 0;
                $dcto = 0;
                $recargo = 0;
                $recargo_cre = 0;
                $recargo_deb = 0;
                foreach ($ordenes as $value) {
                    $txtcolor = '#000000';
                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor, $cant) {
                        // manipulate the cel
                        $cell->setValue($cant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fecha_orden, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->paciente->apellido2 != "(N/A)") {
                            $vnombre = $value->paciente->apellido1 . ' ' . $value->paciente->apellido2;
                        } else {
                            $vnombre = $value->paciente->apellido1;
                        }
                        if ($value->paciente->nombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->paciente->nombre1;
                        }
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor) {
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {
                        $cell->setValue($value->seguro->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {
                        if ($value->nivel != null) {
                            $cell->setValue($value->nivel->nombre);
                        } else {
                            $cell->setValue('');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $doctor = $value->doctor;
                        $nombre_dr = '';
                        if (!is_null($doctor)) {
                            $nombre_dr = $doctor->nombre1 . ' ' . $doctor->apellido1 . ' ' . $doctor->apellido2;
                        }
                        $cell->setValue($nombre_dr);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->comprobante);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuento_p);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuento_valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->total_valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('N' . $i, function ($cell) use ($value) {
                        if ($value->estado == '0') {
                            $estado = 'ELIMINADA';
                        } else if ($value->estado == '-1') {
                            $estado = 'COTIZACION';
                        } else if ($value->estado == '1') {
                            $estado = 'REALIZADA';
                        } else {
                            $estado = '';
                        }
                        $cell->setValue($estado);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('O' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $u_crea = $value->crea;
                        $nombre_crea = '';
                        if (!is_null($u_crea)) {
                            $nombre_crea = $u_crea->nombre1 . ' ' . $u_crea->apellido1 . ' ' . $u_crea->apellido2;
                        }
                        $cell->setValue($nombre_crea);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->telefono1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('Q' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->telefono2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('R' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->email);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('S' . $i, function ($cell) use ($value) {
                        $proxima_visita = !is_null($value->proxima_visita)
                            ? date('d/m/Y H:i', strtotime($value->proxima_visita))
                            : 'Sin próxima consulta';
                        $cell->setValue($proxima_visita);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('T' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->observacion_seguimiento ?: '');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('T' . $i)->getAlignment()->setWrapText(true);
                    $i = $i + 1;
                    $cant = $cant + 1;
                    $total += $value->total_valor;
                    $sub_total += $value->valor;
                    $dcto += $value->descuento_valor;
                    //$recargo   += $value->recargo_valor;
                }
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL PACIENTES:');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue($cant - 1);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H' . $i, function ($cell) use ($sub_total) {
                    // manipulate the cel
                    $cell->setValue($sub_total);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->cell('I' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K' . $i, function ($cell) use ($dcto) {
                    // manipulate the cel
                    $cell->setValue($dcto);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $recargo = $recargo_deb + $recargo_cre;
                $sheet->cell('L' . $i, function ($cell) use ($total, $recargo) {
                    // manipulate the cel
                    $cell->setValue($total - $recargo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->cell('M' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                foreach (range('N', 'T') as $columna) {
                    $sheet->cell($columna . $i, function ($cell) {
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
            });
        })->export('xlsx');
    }
    public function reporte_cotizaciones(Request $request) //COMISIONES DETALLE

    {

        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $nombres     = $request['nombres'];
        $seguro      = $request['seguro'];

        $ordenes = Examen_Orden::whereBetween('examen_orden.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])
            ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->leftjoin('users as d', 'd.id', 'examen_orden.id_doctor_ieced')
            ->leftjoin('labs_grupo_familiar as gf', 'gf.id', 'p.id')
            ->leftjoin('users as u2', 'u2.id', 'gf.id_usuario')
            ->leftjoin('forma_de_pago as fp', 'fp.id', 'examen_orden.id_forma_de_pago')
            ->join('users as mu', 'mu.id', 'examen_orden.id_usuariomod')
            ->select('examen_orden.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'd.apellido2 as dapellido2', 's.nombre as snombre', 'fp.nombre as fpnombre', 's.tipo', 'p.origen', 'p.origen2', 'p.telefono1', 'p.telefono2', 'p.telefono3', 'mu.email as mail1', 'u2.email as mail2')->where('examen_orden.realizado', '1')->where('examen_orden.estado', '1');
        //dd($ordenes);

        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }
        if ($seguro != null) {

            $ordenes = $ordenes->where('examen_orden.id_seguro', $seguro);
        }

        $ordenes = $ordenes->get();

        $ex_det = [];

        $i = 0;

        $fecha_d = date('Y/m/d');

        Excel::create('Examenes-' . $fecha_d, function ($excel) use ($ordenes, $ex_det) {

            $excel->sheet('Examenes', function ($sheet) use ($ordenes, $ex_det) {
                $empresa_labs    = Empresa::where('prioridad_labs', '1')->first();
                $fecha_d = date('Y/m/d');
                $i       = 4;

                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);

                $sheet->cells('A1:AR3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });
                $sheet->cells('A1:AR3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->mergeCells('A1:AR1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO DE COTIZACIONES DE LABORATORIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ID.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('AÑO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G3', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('NOMBRES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TÉLEFONO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MAIL 1');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MAIL 2');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                foreach ($ordenes as $value) {
                    $txtcolor = '#000000';

                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->anio);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->mes);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fecha_orden, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->snombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }

                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->telefono1 != "(N/A)") {
                            $tnombre = $value->telefono1 . ' - ' . $value->telefono2 . ' - ' . $value->telefono3;
                        } else {
                            $tnombre = $value->telefono1;
                        }

                        if ($value->telefono2 != "(N/A)") {
                            $tnombre = $value->telefono1 . ' - ' . $value->telefono2 . ' - ' . $value->telefono3;
                        } else {
                            $tnombre = $value->telefono1;
                        }

                        if ($value->telefono3 != "(N/A)") {
                            $tnombre = $value->telefono1 . ' - ' . $value->telefono2 . ' - ' . $value->telefono3;
                        } else {
                            $vnombre = $value->telefono1;
                        }

                        $cell->setValue($tnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('I' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->mail1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('J' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->mail2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i = $i + 1;
                }
                $sheet->getStyle('H4:P' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('S4:T' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('U4:U' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('V4:AE' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AF4:AF' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');

                /* $sheet->cell('A' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('TOTAL PACIENTES:');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            $cell->setFontWeight('bold');
            });

            $sheet->cell('B' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('C' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('D' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('E' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('F' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('G' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('H' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('I' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('J' . $i, function ($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });*/
            });
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(22)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(17)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(19)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(19)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(19)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(13)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AG")->setWidth(19)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AI")->setWidth(13)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AJ")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AK")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AN")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AO")->setWidth(22)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AP")->setWidth(13)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AQ")->setWidth(19)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AL")->setWidth(19)->setAutosize(false);
        })->export('xlsx');
    }

    public function reporte_ordenes_dr(Request $request) //COMISIONES DETALLE
    {
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $nombres     = $request['nombres'];
        $seguro      = $request['seguro'];
        $ordenes = Examen_Orden::whereBetween('examen_orden.created_at', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])
            ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
            ->leftjoin('users as u', 'u.id', 'examen_orden.id_usuariocrea')
            ->leftjoin('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->where('u.id_tipo_usuario', '3')
            ->select(
                'examen_orden.id',
                'examen_orden.anio',
                'examen_orden.fecha_orden',
                'examen_orden.mes',
                'u.nombre1 as unombre1',
                'u.nombre2 as unombre2',
                'u.apellido2 as uapellido2',
                'u.apellido1 as uapellido1',
                'p.nombre1 as pnombre1',
                'p.nombre2 as pnombre2',
                'p.apellido2 as papellido2',
                'p.apellido1 as papellido1',
                'examen_orden.total_valor',
                'examen_orden.estado',
                'examen_orden.created_at',
                's.nombre as nombre_seguro'
            );

        if ($nombres != null) {
            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);
            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {
                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }
        if ($seguro != null) {
            $ordenes = $ordenes->where('examen_orden.id_seguro', $seguro);
        }
        $ordenes = $ordenes->orderby('examen_orden.id', 'asc')->get();
        $ex_det = [];
        $i = 3;
        Excel::create('Ordenes generadas por Dr.', function ($excel) use ($ordenes, $ex_det, $i) {
            $excel->sheet('Ordenes generadas por Dr.', function ($sheet) use ($ordenes, $ex_det, $i) {
                $sheet->cell('A1:H2', function ($cell) {
                    // manipulate the range of cells
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#D1F2EB');
                });
                $sheet->mergeCells('A1:H1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setValue('ORDENES GENERADAS POR DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ID_ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA AGENDADA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA CREACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL VALOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                foreach ($ordenes as $value) {
                    $txtcolor = '#000000';
                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->fecha_orden);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->created_at);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->uapellido2 != "(N/A)") {
                            $vnombre = $value->uapellido1 . ' ' . $value->uapellido2;
                        } else {
                            $vnombre = $value->uapellido1;
                        }
                        if ($value->unombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->unombre1 . ' ' . $value->unombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->unombre1;
                        }
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }
                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {
                        $cell->setValue($value->total_valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                        if ($value->estado == '0') {
                            $estado = 'ELIMINADA';
                        } else if ($value->estado == '-1') {
                            $estado = 'GENERADA';
                        } else if ($value->estado == '1') {
                            $estado = 'REALIZADA';
                        } else {
                            $estado = '';
                        }
                        $cell->setValue($estado);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value, $txtcolor) {
                        $cell->setValue($value->nombre_seguro);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $i++;
                }
            });
        })->export('xlsx');
    }
    public function cierreCaja(Request $request) //COMISIONES DETALLE

    {
        //  dd("hOLA");
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $nombres     = $request['nombres'];
        $seguro      = $request['seguro'];
        // echo dd($fecha);
        //$fecha = '22-09-2020';
        //$fecha_hasta = '17-09-2021';
        // dd($fecha);
        $id_empresa = $request->session()->get('id_empresa');

        $empresaNombre = Empresa::where('id', $id_empresa)->first();

        //dd($empresaNombre->nombrecomercial);

        $ordenes = Examen_Orden::whereBetween('examen_orden.fecha_orden', [$fecha . ' 00:00', $fecha . ' 23:59'])
            ->where('examen_orden.estado', '1')
            ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->leftjoin('cierre_caja as cc', 'cc.id_orden', 'examen_orden.id')
            ->leftjoin('users as d', 'd.id', 'examen_orden.id_doctor_ieced')
            ->leftjoin('forma_de_pago as fp', 'fp.id', 'examen_orden.id_forma_de_pago')
            ->select('examen_orden.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'd.apellido2 as dapellido2', 's.nombre as snombre', 'fp.nombre as fpnombre', 's.tipo')
            ->where('examen_orden.realizado', '1')
            //->where('examen_orden.estado', '1')
            ->whereNotNull('cc.id_orden');

        //dd($ordenes);
        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }
        if ($seguro != null) {

            $ordenes = $ordenes->where('examen_orden.id_seguro', $seguro);
        }

        $ordenes = $ordenes->get();
        //dd($ordenes);
        $ex_det = [];

        $i = 0;

        $fecha_d = date('Y/m/d');

        $contador           = 3;
        $totalEfectivo      = 0;
        $totalCredito       = 0;
        $totalDebito        = 0;
        $totalTranferencia  = 0;
        $totalCheque        = 0;
        $totalPendientePago = 0;

        //dd($ordenes);
        Excel::create('Cierre de Caja-' . $fecha_d, function ($excel) use ($ordenes, $empresaNombre, $ex_det, $contador, $totalEfectivo, $totalCredito, $totalDebito, $totalTranferencia, $totalCheque, $totalPendientePago) {

            $excel->sheet('Examenes', function ($sheet) use ($ordenes, $empresaNombre, $ex_det, $contador, $totalEfectivo, $totalCredito, $totalDebito, $totalTranferencia, $totalCheque, $totalPendientePago) {
                $empresa_labs    = Empresa::where('prioridad_labs', '1')->first();
                $fecha_d = date('Y/m/d');
                $i       = 4;

                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);

                $sheet->cells('A1:AR3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });
                $sheet->cells('A1:AR3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->mergeCells('A1:D1');
                $sheet->cell('A1', function ($cell) use ($empresaNombre) {
                    // manipulate the cel
                    $cell->setValue('EMPRESA: ' . $empresaNombre->nombrecomercial);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('E1:w1');
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cierra Caja');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA FACTURA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G3', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FACTURA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REFERENCIA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EFECTIVO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TARJETA CREDITO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('7% T/C');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TAREJETA DEBITO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('4.5% T/D');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TRANSFERENCIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CHEQUE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PENDIENTE DE PAGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PAGO EN LINEA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cant = 1;
                $total = 0;
                $sub_total = 0;
                $dcto = 0;
                $recargo = 0;
                $recargo_cre = 0;
                $recargo_deb = 0;
                foreach ($ordenes as $value) {
                    if ($value->tipo == 1 || $value->tipo == 2) {
                        $contador++;
                        $txtcolor = '#000000';

                        $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor, $cant) {
                            // manipulate the cel
                            $cell->setValue($cant);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                            // manipulate the cel
                            $cell->setValue(substr($value->fecha_orden, 0, 10));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $fecha_factura = '';
                        //dd($value->comprobante);
                        if ($value->comprobante != null) {
                            $venta = Ct_ventas::where('nro_comprobante', $value->comprobante)->where('id_empresa', $empresa_labs)->first();
                            if (!is_null($venta)) {
                                $fecha_factura = $venta->fecha;
                            }
                        }

                        $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor, $fecha_factura) {
                            // manipulate the cel
                            $cell->setValue($fecha_factura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor) {
                            // manipulate the cel
                            if ($value->papellido2 != "(N/A)") {
                                $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                            } else {
                                $vnombre = $value->papellido1;
                            }

                            if ($value->pnombre2 != "(N/A)") {
                                $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                            } else {
                                $vnombre = $vnombre . ' ' . $value->pnombre1;
                            }

                            $cell->setValue($vnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {

                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {

                            $cell->setValue($value->snombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                            // manipulate the cel
                            $cell->setValue($value->dnombre1 . ' ' . $value->dapellido1 . ' ' . $value->dapellido2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->valor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        if (($value->anio <= '2019') || ($value->anio == '2020' && $value->mes < '11')) {

                            $acum_hl = 0;
                            $acum_ex = 0;
                            $val_il = 0;
                            $val_2 = 0;
                            $val_1 = 0;
                            $labs = 0;
                            if ($value->tipo != '0') {

                                $detalles = $value->detalles;

                                foreach ($detalles as $detalle) {

                                    if ($value->codigo == null) {
                                        if ($detalle->human_labs == '1') {
                                            $per_hl = $detalle->p_comision;
                                            $labs += $detalle->valor;
                                            $acum_hl += $per_hl * $detalle->valor;
                                        }
                                    } else {

                                        $per_hl = $detalle->p_comision;
                                        $acum_ex += $per_hl * $detalle->valor;
                                    }
                                }
                            }
                            if ($acum_ex > 0) {
                                $acum_ex -= $value->descuento_valor * $per_hl;
                            }
                            if ($acum_hl > 0) {
                                $acum_hl -= $value->descuento_valor * $per_hl;
                            }

                            if ($acum_hl < 0) {
                                $acum_hl = 0;
                            }
                            if ($acum_ex < 0) {
                                $acum_ex = 0;
                            }

                            $val_hl = $labs;
                            $val_10 = $acum_hl;
                            $val_ex = round($acum_ex, 2);
                            $val_il = round($value->valor - $val_hl, 2);
                        } else {
                            $examen_orden = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $value->id)->join('examen as e', 'e.id', 'ed.id_examen')
                                ->where('e.humanlabs', '1')->select('ed.id_examen_orden')->selectRaw('sum(ed.valor) as valor')->groupBy('ed.id_examen_orden')->first();

                            $val_hl = 0;
                            $val_il = 0;
                            $val_10 = 0;
                            $val_ex = 0;
                            $val_2  = 0;
                            $val_1  = 0;
                            $per_hl = 0.08;
                            //$per_il = 0.02;
                            //$per_pb = 0.01;
                            $per_il = 0.00;
                            $per_pb = 0.00;
                            $per_ex = 0.1;

                            if ($value->tipo != '0') {

                                if (!is_null($examen_orden)) {
                                    $val_hl = round($examen_orden->valor, 2);
                                }
                                $val_il = round($value->valor, 2) - $val_hl;

                                if ($value->codigo == null) {
                                    //$val_10 = $val_hl * $per_hl;
                                    $val_hl_nw = $val_hl - $value->descuento_valor;
                                    if ($val_hl_nw < 0) {
                                        $val_hl_nw = 0;
                                    }
                                    $val_10 = $val_hl_nw * $per_hl;
                                    $val_2  = $val_il * $per_il;
                                }
                            } else {
                                if ($value->codigo == null) {
                                    $val_1 = round($value->valor * $per_pb, 2);
                                }
                            }
                            if ($value->codigo != null) {

                                $val_ex_nw = $value->valor - $value->descuento_valor;
                                if ($val_ex_nw < 0) {
                                    $val_ex_nw = 0;
                                }
                                //$val_ex = round($value->valor * $per_ex, 2);
                                $val_ex = round($val_ex_nw * $per_ex, 2);
                                if ($value->codigo == '1270') {
                                    $val_ex = 0;
                                }
                            }
                        }

                        $factura     = '';
                        $pago_online = DB::table('pagosenlinea')->where('tipo', 'examen_orden')->where('clave', $value->id)->first();

                        if (!is_null($value->comprobante)) {
                            $factura = $value->comprobante;
                            $sheet->cell('I' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue($value->comprobante);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        } else {
                            if (!is_null($pago_online)) {
                                $factura = $pago_online->nro_comprobante;
                            }
                            $sheet->cell('I' . $i, function ($cell) use ($value, $factura) {
                                // manipulate the cel
                                $cell->setValue($factura);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }
                        $valor_factura = 0;
                        $venta         = Ct_ventas::where('nro_comprobante', $factura)->where('id_empresa', $empresa_labs)->first();
                        if (!is_null($venta)) {
                            $valor_factura = $venta->total_final;
                        }

                        $referencia = '';
                        foreach ($value->detalle_forma_pago as $fp) {
                            if ($referencia == '') {
                                $referencia = $fp->tipo_pago->nombre;
                            } else {
                                $referencia = $referencia . '+' . $fp->tipo_pago->nombre;
                            }
                            if ($fp->banco != null) {
                                $referencia = $referencia . ' BCO:' . $fp->bancos->nombre;
                            }
                            if ($fp->tipo_tarjeta != null) {
                                $referencia = $referencia . ' TRJ:' . $fp->tarjetas->nombre;
                            }
                            if ($fp->numero != null) {
                                $referencia = $referencia . ' NRO:' . $fp->numero;
                            }
                        }
                        $forma_pago = null;
                        if ($referencia == '') {
                            $forma_pago = Forma_de_pago::where('id', $value->id_forma_de_pago)->first();
                            if (!is_null($forma_pago)) {
                                $referencia = $forma_pago->nombre;
                            }
                            if ($referencia == '') {
                                $referencia = 'EFECTIVO/CHEQUE';
                                if ($value->pago_online) {
                                    $referencia = 'PAGO EN LINEA';
                                }
                            }
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($referencia) {
                            // manipulate the cel
                            $cell->setValue($referencia);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        if ($referencia == 'EFECTIVO/CHEQUE') {
                            $sheet->cell('K' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue($value->total_valor);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $totalEfectivo = $value->total_valor + $totalEfectivo;
                        } else {
                            $efectivo = $value->detalle_forma_pago->where('id_tipo_pago', '1')->sum('valor');
                            $sheet->cell('K' . $i, function ($cell) use ($efectivo) {
                                // manipulate the cel
                                $cell->setValue($efectivo);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $totalEfectivo = $efectivo + $totalEfectivo;
                        }
                        $credito = $value->detalle_forma_pago->where('id_tipo_pago', '4')->sum('valor');
                        if ($credito == 0) {
                            if (!is_null($forma_pago)) {
                                if ($forma_pago->id == 3) {
                                    $credito = $value->total_valor - $value->recargo_valor;
                                }
                            }
                        }
                        $sheet->cell('L' . $i, function ($cell) use ($credito) {
                            // manipulate the cel
                            $cell->setValue($credito);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $totalCredito = $totalCredito + $credito;
                        $cre_fee      = $value->detalle_forma_pago->where('id_tipo_pago', '4')->sum('p_fi');
                        if ($cre_fee == 0) {
                            if (!is_null($forma_pago)) {
                                if ($forma_pago->id == 3) {
                                    $cre_fee = $value->recargo_valor;
                                }
                            }
                        }
                        $sheet->cell('M' . $i, function ($cell) use ($cre_fee) {
                            // manipulate the cel
                            $cell->setValue($cre_fee);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $recargo_cre += $cre_fee;
                        $debito = $value->detalle_forma_pago->where('id_tipo_pago', '6')->sum('valor');
                        if ($debito == 0) {
                            if (!is_null($forma_pago)) {
                                if ($forma_pago->id == 2) {
                                    $debito = $value->total_valor - $value->recargo_valor;
                                }
                            }
                        }
                        $sheet->cell('N' . $i, function ($cell) use ($debito) {
                            // manipulate the cel
                            $cell->setValue($debito);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $totalDebito = $totalDebito + $debito;
                        $deb_fee     = $value->detalle_forma_pago->where('id_tipo_pago', '6')->sum('p_fi');
                        if ($deb_fee == 0) {
                            if (!is_null($forma_pago)) {
                                if ($forma_pago->id == 2) {
                                    $deb_fee = $value->recargo_valor;
                                }
                            }
                        }
                        $recargo_deb += $deb_fee;
                        $sheet->cell('O' . $i, function ($cell) use ($deb_fee) {
                            // manipulate the cel
                            $cell->setValue($deb_fee);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $transfer = $value->detalle_forma_pago->where('id_tipo_pago', '5')->sum('valor');
                        $sheet->cell('P' . $i, function ($cell) use ($transfer) {
                            $cell->setValue($transfer);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $totalTranferencia = $totalTranferencia + $transfer;
                        $cheque            = $value->detalle_forma_pago->where('id_tipo_pago', '2')->sum('valor');
                        $sheet->cell('Q' . $i, function ($cell) use ($cheque) {
                            // manipulate the cel
                            $cell->setValue($cheque);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $totalCheque = $totalCheque + $cheque;

                        $ppago = $value->detalle_forma_pago->where('id_tipo_pago', '7')->sum('valor');
                        $sheet->cell('R' . $i, function ($cell) use ($ppago) {
                            // manipulate the cel
                            $cell->setValue($ppago);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $totalPendientePago = $totalPendientePago + $ppago;
                        $ponline            = 0;
                        if ($value->pago_online == '1') {
                            $ponline = $value->total_valor;
                        }
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->total_valor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->total_valor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        /*elseif(!is_null($pago_online)){
                        $factura = $pago_online->nro_comprobante;
                        }
                        $sheet->cell('AF' . $i, function ($cell) use ($value, $factura) {
                        // manipulate the cel
                        $cell->setValue($factura);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });*/

                        $i = $i + 1;

                        $cant = $cant + 1;
                        $total += $value->total_valor;
                        $sub_total += $value->valor;
                        $dcto += $value->descuento_valor;
                        //$recargo   += $value->recargo_valor;
                    }
                }
                $sheet->getStyle('H4:P' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('S4:T' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('U4:U' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('V4:AE' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AF4:AF' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                /*$sheet->getStyle('U6:U' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('Q6:R' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AB6:AB' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AE6:AE' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AJ6:AJ' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('AL6:AM' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');*/

                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL PACIENTES:');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('B' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue($cant - 1);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('H' . $i, function ($cell) use ($sub_total) {
                    // manipulate the cel
                    $cell->setValue($sub_total);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('I' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('J' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('K' . $i, function ($cell) use ($totalEfectivo) {
                    // manipulate the cel
                    $cell->setValue($totalEfectivo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('L' . $i, function ($cell) use ($cant, $totalCredito) {
                    // manipulate the cel
                    $cell->setValue($totalCredito);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                /*$sheet->cell('T' . $i, function ($cell) use ($cant) {
                // manipulate the cel
                $cell->setValue('RECARGO/TOTAL');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
                });*/

                $sheet->cell('M' . $i, function ($cell) use ($recargo_cre) {
                    // manipulate the cel
                    $cell->setValue($recargo_cre);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('N' . $i, function ($cell) use ($cant, $totalDebito) {
                    // manipulate the cel
                    $cell->setValue($totalDebito);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                /*$sheet->cell('X' . $i, function ($cell) use ($cant) {
                // manipulate the cel
                $cell->setValue('INGRESO');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
                });*/
                $sheet->cell('O' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                /*$sheet->cell('Y' . $i, function ($cell) use ($total,$recargo) {
                // manipulate the cel
                $cell->setValue($total - $recargo);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $cell->setFontWeight('bold');
                });*/
                $sheet->cell('P' . $i, function ($cell) use ($recargo_deb) {
                    // manipulate the cel
                    $cell->setValue($recargo_deb);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Q' . $i, function ($cell) use ($totalCheque) {
                    // manipulate the cel
                    $cell->setValue($totalCheque);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('R' . $i, function ($cell) use ($recargo_deb) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('S' . $i, function ($cell) use ($total) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('T' . $i, function ($cell) use ($total) {
                    // manipulate the cel
                    $cell->setValue($total);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                /************EXAMEN DE LABORATORIO  SEGUROS PUBLICOS **/
                $contador = $contador + 7;

                $sheet->mergeCells('A' . $contador . ':J' . $contador);
                $sheet->cell('A' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EXAMEN DE LABORATORIO  SEGUROS PUBLICOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $contador++;

                $sheet->cell('A' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA FACTURA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('D' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('G' . $contador, function ($cell) {
                    // manipulate the cel

                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('I' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('J' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i     = $contador + 1;
                $num   = 0;
                $aux   = $contador;
                $total = 0;
                foreach ($ordenes as $value) {
                    if ($value->tipo == 0) {
                        $num++;
                        $contador++;
                        $txtcolor = '#000000';

                        $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor, $num) {
                            // manipulate the cel
                            $cell->setValue($num);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                            // manipulate the cel
                            $cell->setValue(substr($value->fecha_orden, 0, 10));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $fecha_factura = '';
                        //dd($value->comprobante);
                        if ($value->comprobante != null) {
                            $venta = Ct_ventas::where('nro_comprobante', $value->comprobante)->where('id_empresa', $empresa_labs)->first();
                            if (!is_null($venta)) {
                                $fecha_factura = $venta->fecha;
                            }
                        }

                        $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor, $fecha_factura) {
                            // manipulate the cel
                            $cell->setValue($fecha_factura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor) {
                            // manipulate the cel
                            if ($value->papellido2 != "(N/A)") {
                                $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                            } else {
                                $vnombre = $value->papellido1;
                            }

                            if ($value->pnombre2 != "(N/A)") {
                                $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                            } else {
                                $vnombre = $vnombre . ' ' . $value->pnombre1;
                            }

                            $cell->setValue($vnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {

                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {

                            $cell->setValue($value->snombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                            // manipulate the cel
                            $cell->setValue($value->dnombre1 . ' ' . $value->dapellido1 . ' ' . $value->dapellido2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->valor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        if (($value->anio <= '2019') || ($value->anio == '2020' && $value->mes < '11')) {

                            $acum_hl = 0;
                            $acum_ex = 0;
                            $val_il = 0;
                            $val_2 = 0;
                            $val_1 = 0;
                            $labs = 0;
                            if ($value->tipo != '0') {

                                $detalles = $value->detalles;

                                foreach ($detalles as $detalle) {

                                    if ($value->codigo == null) {
                                        if ($detalle->human_labs == '1') {
                                            $per_hl = $detalle->p_comision;
                                            $labs += $detalle->valor;
                                            $acum_hl += $per_hl * $detalle->valor;
                                        }
                                    } else {

                                        $per_hl = $detalle->p_comision;
                                        $acum_ex += $per_hl * $detalle->valor;
                                    }
                                }
                            }
                            if ($acum_ex > 0) {
                                $acum_ex -= $value->descuento_valor * $per_hl;
                            }
                            if ($acum_hl > 0) {
                                $acum_hl -= $value->descuento_valor * $per_hl;
                            }

                            if ($acum_hl < 0) {
                                $acum_hl = 0;
                            }
                            if ($acum_ex < 0) {
                                $acum_ex = 0;
                            }

                            $val_hl = $labs;
                            $val_10 = $acum_hl;
                            $val_ex = round($acum_ex, 2);
                            $val_il = round($value->valor - $val_hl, 2);
                        } else {
                            $examen_orden = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $value->id)->join('examen as e', 'e.id', 'ed.id_examen')
                                ->where('e.humanlabs', '1')->select('ed.id_examen_orden')->selectRaw('sum(ed.valor) as valor')->groupBy('ed.id_examen_orden')->first();

                            $val_hl = 0;
                            $val_il = 0;
                            $val_10 = 0;
                            $val_ex = 0;
                            $val_2  = 0;
                            $val_1  = 0;
                            $per_hl = 0.08;
                            //$per_il = 0.02;
                            //$per_pb = 0.01;
                            $per_il = 0.00;
                            $per_pb = 0.00;
                            $per_ex = 0.1;

                            if ($value->tipo != '0') {

                                if (!is_null($examen_orden)) {
                                    $val_hl = round($examen_orden->valor, 2);
                                }
                                $val_il = round($value->valor, 2) - $val_hl;

                                if ($value->codigo == null) {
                                    //$val_10 = $val_hl * $per_hl;
                                    $val_hl_nw = $val_hl - $value->descuento_valor;
                                    if ($val_hl_nw < 0) {
                                        $val_hl_nw = 0;
                                    }
                                    $val_10 = $val_hl_nw * $per_hl;
                                    $val_2  = $val_il * $per_il;
                                }
                            } else {
                                if ($value->codigo == null) {
                                    $val_1 = round($value->valor * $per_pb, 2);
                                }
                            }
                            if ($value->codigo != null) {

                                $val_ex_nw = $value->valor - $value->descuento_valor;
                                if ($val_ex_nw < 0) {
                                    $val_ex_nw = 0;
                                }
                                //$val_ex = round($value->valor * $per_ex, 2);
                                $val_ex = round($val_ex_nw * $per_ex, 2);
                                if ($value->codigo == '1270') {
                                    $val_ex = 0;
                                }
                            }
                        }
                        /**AQUI ME QUEDE */
                        $sheet->cell('I' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descuento_valor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->total_valor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $i    = $i + 1;
                        $cant = $cant + 1;
                        $total += $value->total_valor;
                        $sub_total += $value->valor;
                        $dcto += $value->descuento_valor;
                        //$recargo   += $value->recargo_valor;

                    }
                }

                $sheet->cell('I' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('J' . $i, function ($cell) use ($total) {
                    $cell->setValue($total);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $totalSeguro = $total;

                /***Todo */

                $aux2 = $aux;

                $sheet->mergeCells('N' . $aux2 . ':Q' . $aux2);
                $sheet->cell('N' . $aux2, function ($cell) use ($empresaNombre) {
                    // manipulate the cel
                    $cell->setValue($empresaNombre->nombrecomercial);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $aux2++;
                $sheet->mergeCells('N' . $aux2 . ':P' . $aux2);
                $sheet->cell('N' . $aux2, function ($cell) use ($empresaNombre) {
                    $cell->setValue('ENTREGO CASH DE ' . $empresaNombre->nombrecomercial);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Q' . $aux2, function ($cell) use ($totalEfectivo) {
                    $cell->setValue('$ ' . $totalEfectivo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $aux2++;
                $sheet->mergeCells('N' . $aux2 . ':P' . $aux2);
                $sheet->cell('N' . $aux2, function ($cell) use ($empresaNombre) {
                    $cell->setValue('TARJETAS CREDITO ' . $empresaNombre->nombrecomercial);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Q' . $aux2, function ($cell) use ($totalCredito) {
                    $cell->setValue('$ ' . $totalCredito);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $aux2++;
                $sheet->mergeCells('N' . $aux2 . ':P' . $aux2);
                $sheet->cell('N' . $aux2, function ($cell) {
                    $cell->setValue('7%  T/C COMISION ADMINISTRATIVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Q' . $aux2, function ($cell) use ($recargo_cre) {
                    $cell->setValue('$ ' . $recargo_cre);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $aux2++;
                $sheet->mergeCells('N' . $aux2 . ':P' . $aux2);
                $sheet->cell('N' . $aux2, function ($cell) {
                    $cell->setValue('TRANSFERENCIAS/DEPOSITOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('Q' . $aux2, function ($cell) use ($totalTranferencia) {
                    $cell->setValue('$ ' . $totalTranferencia);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $aux2++;
                $sheet->mergeCells('N' . $aux2 . ':P' . $aux2);
                $sheet->cell('N' . $aux2, function ($cell) use ($empresaNombre) {
                    $cell->setValue('CHEQUES ' . $empresaNombre->nombrecomercial);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Q' . $aux2, function ($cell) use ($totalCheque) {
                    $cell->setValue('$ ' . $totalCheque);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $aux2++;
                $sheet->mergeCells('N' . $aux2 . ':P' . $aux2);
                $sheet->cell('N' . $aux2, function ($cell) {
                    $cell->setValue('PENDIENTE DE PAGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Q' . $aux2, function ($cell) use ($totalPendientePago) {
                    $cell->setValue('$ ' . $totalPendientePago);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $aux2++;
                $sheet->mergeCells('N' . $aux2 . ':P' . $aux2);
                $sheet->cell('N' . $aux2, function ($cell) {
                    $cell->setValue('SEGUROS PÚBLICOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Q' . $aux2, function ($cell) use ($totalSeguro) {
                    $cell->setValue('$ ' . $totalSeguro);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sumaTotal = $totalSeguro + $totalCheque + $totalPendientePago + $totalTranferencia + $totalCredito + $recargo_cre + $totalEfectivo;
                $aux2++;
                $sheet->mergeCells('N' . $aux2 . ':P' . $aux2);
                $sheet->cell('N' . $aux2, function ($cell) {
                    $cell->setValue('Total');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Q' . $aux2, function ($cell) use ($sumaTotal) {
                    $cell->setValue('$ ' . $sumaTotal);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
            });
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(22)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(17)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(19)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(19)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(19)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(13)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AG")->setWidth(19)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AI")->setWidth(13)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AJ")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AK")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AN")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AO")->setWidth(22)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AP")->setWidth(13)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AQ")->setWidth(19)->setAutosize(false);
        })->export('xlsx');
    }

    public function reporte_mail(Request $request)
    {

        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $nombres     = $request['nombres'];
        $seguro      = $request['seguro'];
        //dd($request->all());

        /*$ordenes = DB::table('examen_orden as eo')->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->join('paciente as p','p.id','eo.id_paciente')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->get();*/

        $ordenes = DB::table('examen_orden as eo')->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('paciente as p', 'p.id', 'eo.id_paciente')->join('seguros as s', 's.id', 'eo.id_seguro')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->leftjoin('forma_de_pago as fp', 'fp.id', 'eo.id_forma_de_pago')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 's.nombre as snombre', 'fp.nombre as fpnombre', 's.tipo', 'p.parentesco', 'p.id_usuario', 'p.telefono1', 'p.telefono2')->where('eo.realizado', '1')->where('eo.estado', '1');
        //dd($ordenes->get());
        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }
        if ($seguro != null) {

            $ordenes = $ordenes->where('eo.id_seguro', $seguro);
        }

        $ordenes = $ordenes->orderby('p.id_usuario')->get();

        $ex_det = [];

        $i = 0;

        $fecha_d = date('Y/m/d');

        Excel::create('Examenes-' . $fecha_d, function ($excel) use ($ordenes, $ex_det) {

            $excel->sheet('Examenes', function ($sheet) use ($ordenes, $ex_det) {
                $fecha_d = date('Y/m/d');
                $i       = 5;

                $sheet->mergeCells('A3:G3');

                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);

                $sheet->cells('A1:P3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A3:S4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ENVIOS DE CORREO A PACIENTES POR FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('ENVIO MAIL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RESULTADOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PARENTESCO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EMAIL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOMICILIO/PRESENCIAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cant = 1;
                $total = 0;
                $sub_total = 0;
                foreach ($ordenes as $value) {
                    $txtcolor = '#000000';

                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor, $cant) {
                        // manipulate the cel
                        $cell->setValue($cant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fecha_orden, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }

                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->snombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->parentesco == 'Principal') {
                            if ($value->estado_pago == '1') {
                                $txt_mail = 'SI';
                            } else {
                                $txt_mail = 'NO';
                            }
                        } else {
                            $txt_mail = '';
                        }

                        $cell->setValue($txt_mail);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $imp = $this->puede_imprimir($value->id);
                    if ($imp['cant_par'] == 0) {
                        $pct = 'NO INICIADO';
                    } elseif ($imp['certificados'] == $imp['cant_par']) {
                        $pct = 'COMPLETO';
                    } else {
                        $pct = 'EN PROCESO';
                    }

                    $sheet->cell('G' . $i, function ($cell) use ($value, $pct) {
                        // manipulate the cel
                        $cell->setValue($pct);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel HUMANLABS
                        $cell->setValue($value->parentesco);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    //mail de usuario
                    $principal = User::find($value->id_usuario);
                    if (!is_null($principal)) {
                        $mail = $principal->email;
                    } else {
                        $mail = '';
                    }
                    $sheet->cell('I' . $i, function ($cell) use ($value, $mail) {
                        // manipulate the cel INTERLAB
                        $cell->setValue($mail);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    if ($value->id == $value->id_usuario) {
                        $telefono = $value->telefono1 . ' ' . $value->telefono2;
                    } else {
                        $pac_prin = Paciente::find($value->id_usuario);
                        if (!is_null($pac_prin)) {
                            $telefono = $pac_prin->telefono1 . ' ' . $pac_prin->telefono2;
                        } else {
                            $telefono = '';
                        }
                    }

                    $sheet->cell('J' . $i, function ($cell) use ($value, $telefono) {
                        // manipulate the cel 10%
                        $cell->setValue($telefono);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    if ($value->pres_dom == '0') {
                        $dtxt = 'PRESENCIAL';
                    } else {
                        $dtxt = 'DOMICILIO';
                    }
                    $sheet->cell('K' . $i, function ($cell) use ($value, $dtxt) {
                        // manipulate the cel
                        $cell->setValue($dtxt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                    $cant++;
                }
            });
        })->export('xlsx');
    }

    public function reporte_index(Request $request)
    {

        //dd($request->all());
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $seguro      = $request['seguro'];
        $nombres     = $request['nombres'];
        //dd($request->all());

        /*$ordenes = DB::table('examen_orden as eo')->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->join('paciente as p','p.id','eo.id_paciente')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->get();*/

        $ordenes = $this->recupera_ordenes()->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('eo.realizado', '1')->where('eo.estado', '1');
        //dd($ordenes);
        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }

        if ($seguro != null) {

            $ordenes = $ordenes->where('eo.id_seguro', $seguro);
        }

        $ordenes = $ordenes->get();

        $seguros = Seguro::all();

        $ex_det = [];
        foreach ($ordenes as $orden) {
            $txt_examen = "";
            $detalle    = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'ed.id_examen')->select('ed.*', 'e.nombre', 'e.descripcion')->get();
            $bandera    = 0;
            foreach ($detalle as $value) {
                if ($bandera == 0) {
                    $txt_examen = $value->descripcion;
                    $bandera    = 1;
                } else {
                    $txt_examen = $txt_examen . '+' . $value->descripcion;
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }

        $i = 0;

        $fecha_d = date('Y/m/d');

        return view('laboratorio/orden/reporte_index', ['ordenes' => $ordenes, 'ex_det' => $ex_det, 'nombres' => $nombres, 'fecha_hasta' => $fecha_hasta, 'fecha' => $fecha, 'seguro' => $seguro, 'seguros' => $seguros]);
    }

    public function cotizaciones(Request $request)
    {

        //dd($request->all());
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $seguro      = $request['seguro'];
        $nombres     = $request['nombres'];
        //dd($request->all());

        /*$ordenes = DB::table('examen_orden as eo')->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->join('paciente as p','p.id','eo.id_paciente')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->get();*/

        $ordenes = $this->recupera_ordenes()->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('eo.realizado', '1')->where('eo.estado', '1');
        //////dd($ordenes);
        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }

        if ($seguro != null) {

            $ordenes = $ordenes->where('eo.id_seguro', $seguro);
        }

        $ordenes = $ordenes->get();
        //dd($ordenes);
        //$ordenes_p = Orden::
        $seguros = Seguro::where('inactivo', '1')->get();

        $ex_det = [];
        foreach ($ordenes as $orden) {
            $txt_examen = "";
            $detalle    = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'ed.id_examen')->select('ed.*', 'e.nombre', 'e.descripcion')->get();
            $bandera    = 0;
            foreach ($detalle as $value) {
                if ($bandera == 0) {
                    $txt_examen = $value->descripcion;
                    $bandera    = 1;
                } else {
                    $txt_examen = $txt_examen . '+' . $value->descripcion;
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }

        $i = 0;

        $fecha_d = date('Y/m/d');

        return view('laboratorio/orden/cotizacion_reporte_index', ['ordenes' => $ordenes, 'ex_det' => $ex_det, 'nombres' => $nombres, 'fecha_hasta' => $fecha_hasta, 'fecha' => $fecha, 'seguro' => $seguro, 'seguros' => $seguros]);
    }

    public function reporte_detalle(Request $request)
    {

        //dd($request->all());
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $seguro      = $request['seguro'];
        $nombres     = $request['nombres'];
        //dd($request->all());

        $ordenes = DB::table('examen_orden as eo')->join('paciente as p', 'p.id', 'eo.id_paciente')->join('seguros as s', 's.id', 'eo.id_seguro')->leftjoin('empresa as em', 'em.id', 'eo.id_empresa')->leftjoin('nivel as n', 'n.id', 'eo.id_nivel')->leftjoin('protocolo as proto', 'proto.id', 'eo.id_protocolo')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->join('users as cu', 'cu.id', 'eo.id_usuariocrea')->join('users as mu', 'mu.id', 'eo.id_usuariomod')->join('examen_detalle as ed', 'ed.id_examen_orden', 'eo.id')->join('examen as e', 'e.id', 'ed.id_examen')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 's.nombre as snombre', 'n.nombre as nnombre', 'em.nombrecomercial', 'cu.nombre1 as cnombre1', 'cu.apellido1 as capellido1', 'mu.nombre1 as mnombre1', 'mu.apellido1 as mapellido1', 'em.nombre_corto', 'proto.pre_post', 's.tipo as stipo', 'e.descripcion', 'ed.valor as edvalor', 'ed.cubre');

        //$ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->where('eo.estado', '1');

        $ordenes = $ordenes->where('eo.estado', '1');

        if ($fecha != null) {
            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($nombres != null) {

            $nombres2    = explode(" ", $nombres);
            $cantidad    = count($nombres2);
            $nombres_sql = '';
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }

            $nombres = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres]);
                });
            } else {

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }
        //dd($ordenes->get(),$nombres);

        if ($seguro != null) {

            $ordenes = $ordenes->where('eo.id_seguro', $seguro);
        }

        if ($fecha == null) {
            $ordenes = $ordenes->limit(100);
        }

        $ordenes = $ordenes->get();

        //dd($ordenes);

        $seguros = Seguro::where('inactivo', '1')->get();

        $ex_det = [];
        foreach ($ordenes as $orden) {
            $txt_examen = "";
            $detalle    = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'ed.id_examen')->select('ed.*', 'e.nombre', 'e.descripcion')->get();
            $bandera    = 0;
            foreach ($detalle as $value) {
                if ($bandera == 0) {
                    $txt_examen = $value->descripcion;
                    $bandera    = 1;
                } else {
                    $txt_examen = $txt_examen . '+' . $value->descripcion;
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }

        $i = 0;

        $fecha_d = date('Y/m/d');

        Excel::create('Examenes_detalle-' . $fecha_d, function ($excel) use ($ordenes) {

            $excel->sheet('Examenes_detalle', function ($sheet) use ($ordenes) {
                $fecha_d = date('Y/m/d');
                $i       = 5;

                $sheet->mergeCells('A3:J3');

                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);

                $sheet->cells('A1:J3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A3:J4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO DE DETALLE DE EXÁMENES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO. ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('EXAMEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CUBRE SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPROBANTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                /*$sheet->cell('H4', function($cell) {
                // manipulate the cel
                $cell->setValue('');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I4', function($cell) {
                // manipulate the cel
                $cell->setValue('');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J4', function($cell) {
                // manipulate the cel
                $cell->setValue('');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K4', function($cell) {
                // manipulate the cel
                $cell->setValue('');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L4', function($cell) {
                // manipulate the cel
                $cell->setValue('');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('M4', function($cell) {
                // manipulate the cel
                $cell->setValue('');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });*/

                $cant = 1;
                $total = 0;
                foreach ($ordenes as $value) {

                    $sheet->cell('A' . $i, function ($cell) use ($value, $cant) {
                        // manipulate the cel
                        $cell->setValue($cant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value, $cant) {
                        // manipulate the cel
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        //$cell->setValue(substr($value->created_at,0,10));
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }

                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {

                        $cell->setValue(substr($value->fecha_orden, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->snombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->edvalor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->cubre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('J' . $i, function ($cell) use ($value, $cant) {
                        // manipulate the cel
                        $cell->setValue($value->comprobante);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    /*
                    $sheet->cell('I'.$i, function($cell) use($value) {
                    // manipulate the cel
                    $cell->setValue($value->descuento_p);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('J'.$i, function($cell) use($value) {
                    // manipulate the cel
                    $cell->setValue($value->descuento_valor);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('K'.$i, function($cell) use($value) {
                    // manipulate the cel
                    $cell->setValue($value->recargo_p);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('L'.$i, function($cell) use($value) {
                    // manipulate the cel
                    $cell->setValue($value->recargo_valor);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('M'.$i, function($cell) use($value) {
                    // manipulate the cel
                    $cell->setValue($value->total_valor);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });*/

                    $i = $i + 1;

                    $cant  = $cant + 1;
                    $total = $total + $value->total_valor;
                }
                $sheet->getStyle('H5:H' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                //$sheet->getStyle('J5:J'.$i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                //$sheet->getStyle('L5:M'.$i)->getNumberFormat()->setFormatCode('$#,##0.00_-');

                /*$sheet->cell('A'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('B'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('C'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('D'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('E'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('F'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('G'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('H'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('I'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('J'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('TOTAL PACIENTES:');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            $cell->setFontWeight('bold');
            });

            $sheet->cell('K'.$i, function($cell) use($cant){
            // manipulate the cel
            $cell->setValue($cant - 1);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            $cell->setFontWeight('bold');
            });

            $sheet->cell('L'.$i, function($cell) {
            // manipulate the cel
            $cell->setValue('TOTAL:');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            $cell->setFontWeight('bold');
            });

            $sheet->cell('M'.$i, function($cell) use($total){
            // manipulate the cel
            $cell->setValue($total);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            $cell->setFontWeight('bold');
            });*/
            });
        })->export('xlsx');
    }

    public function reporte_detalle_covid(Request $request)
    {

        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $seguro      = $request['seguro'];
        $nombres     = $request['nombres'];
        //dd($request->all());

        $resultados = DB::table('examen_resultado as er')->join('examen_parametro as ep', 'ep.id', 'er.id_parametro')->join('examen as e', 'e.id', 'ep.id_examen')->where('e.nombre', 'like', '%COVID%')->join('examen_orden as eo', 'eo.id', 'er.id_orden')->join('paciente as p', 'p.id', 'eo.id_paciente')->join('seguros as s', 's.id', 'eo.id_seguro')->where('eo.estado', '1')->select('er.*', 'eo.fecha_orden', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 's.nombre as snombre', 'ep.nombre as epnombre', 'p.direccion', 'p.sexo', 'p.fecha_nacimiento', 'p.telefono1', 'p.telefono2');

        if ($fecha != null) {
            $resultados = $resultados->whereBetween('er.updated_at', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($nombres != null) {

            $nombres2    = explode(" ", $nombres);
            $cantidad    = count($nombres2);
            $nombres_sql = '';
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }

            $nombres = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                $resultados = $resultados->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres]);
                });
            } else {

                $resultados = $resultados->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }

        if ($seguro != null) {

            $resultados = $resultados->where('eo.id_seguro', $seguro);
        }

        $resultados = $resultados->orderBy('eo.id')->orderBy('ep.nombre')->get();

        //dd($resultados);
        //return $resultados;

        $seguros = Seguro::where('inactivo', '1')->get();

        $i = 0;

        $fecha_d = date('Y/m/d');

        Excel::create('Covid-' . $fecha_d, function ($excel) use ($resultados) {

            $excel->sheet('Covid', function ($sheet) use ($resultados) {
                $fecha_d = date('Y/m/d');
                $i       = 4;

                $sheet->mergeCells('A3:G3');

                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);

                $sheet->cells('A1:G3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A3:G4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO DE PRUEBAS COVID');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ID');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA RESULTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA_ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('CERTIFICADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('IGG');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IGM');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEXO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DIRECCION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cant = 0;
                $total = 0;
                $id_temp = 0;

                foreach ($resultados as $value) {
                    if ($id_temp != $value->id_orden) {
                        $i++;
                        $cant++;
                        $sheet->cell('A' . $i, function ($cell) use ($value, $cant) {
                            // manipulate the cel
                            $cell->setValue($cant);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value, $cant) {
                            // manipulate the cel
                            $cell->setValue($value->id_orden);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('C' . $i, function ($cell) use ($value, $cant) {
                            // manipulate the cel
                            $cell->setValue(substr($value->updated_at, 0, 10));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value, $cant) {
                            // manipulate the cel
                            $cell->setValue(substr($value->fecha_orden, 0, 10));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue(substr($value->created_at,0,10));
                            if ($value->apellido2 != "(N/A)") {
                                $vnombre = $value->apellido1 . ' ' . $value->apellido2;
                            } else {
                                $vnombre = $value->apellido1;
                            }

                            if ($value->nombre2 != "(N/A)") {
                                $vnombre = $vnombre . ' ' . $value->nombre1 . ' ' . $value->nombre2;
                            } else {
                                $vnombre = $vnombre . ' ' . $value->nombre1;
                            }
                            $cell->setValue($vnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('F' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->snombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->certificado) {
                                $cell->setValue('SI');
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        if ($value->epnombre == 'COVID-IgG' || $value->epnombre == 'COVID-IGG') {
                            $sheet->cell('H' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue($value->valor);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }

                        if ($value->epnombre == 'COVID-IgM' || $value->epnombre == 'COVID-IGM') {
                            $sheet->cell('I' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue($value->valor);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }

                        if ($value->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($value, $edad) {
                            $cell->setValue($edad);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            if ($value->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->sexo == 2) {
                                $cell->setValue('F');
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('L' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->telefono1 . '/' . $value->telefono2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->direccion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $id_temp = $value->id_orden;
                    } else {
                        if ($value->epnombre == 'COVID-IgG' || $value->epnombre == 'COVID-IGG') {
                            $sheet->cell('H' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue($value->valor);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }

                        if ($value->epnombre == 'COVID-IgM' || $value->epnombre == 'COVID-IGM') {
                            $sheet->cell('I' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue($value->valor);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }

                        if ($value->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($value, $edad) {
                            $cell->setValue($edad);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            if ($value->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->sexo == 2) {
                                $cell->setValue('F');
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('L' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->telefono1 . '/' . $value->telefono2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->direccion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        //$i++;
                        //$cant++;

                    }
                }
            });
        })->export('xlsx');
    }

    public function codigo_barras($id)
    {
        if ($this->rol_control()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $data       = $id;

        $orden = examen_orden::find($id);

        if (is_null($orden->toma_muestra)) {
            $orden->toma_muestra = date('Y-m-d H:i:s');
            $orden->save();
        }
        Examen_Orden_Toma_Muestra::create([
            'id_examen_orden' => $orden->id,
            'toma_muestra'    => date('Y-m-d H:i:s'),
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ]);
        $id_paciente = $orden->id_paciente;
        $paciente    = Paciente::find($id_paciente);

        $date = date('Y-m-d');

        $view = \View::make('laboratorio.orden.pdf', compact('data', 'date', 'paciente', 'orden'))->render();
        //$pdf = \App::make('dompdf.wrapper');
        //$pdf->loadHTML($view);
        return $view;
    }

    public function descargar($id)
    {
        $tipo_usuario  = Auth::user()->id_tipo_usuario;
        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();
        $examenes      = Examen::where('publico_privado', '0')->orderBy('id_agrupador')->get();
        $agrupadores   = Examen_Agrupador::where('estado', '1')->get();
        $orden         = DB::table('examen_orden as eo')->where('eo.id', $id)->join('paciente as p', 'p.id', 'eo.id_paciente')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'p.fecha_nacimiento as pfecha_nacimiento', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->first();
        $detalles      = Examen_Detalle::where('id_examen_orden', $id)->where('estado', '1')->get();
        $seleccionados = [];

        foreach ($detalles as $detalle) {

            $seleccionados[] = $detalle->id_examen;
        }

        $seguro     = Seguro::find($orden->id_seguro)->where('inactivo', '1');
        $protocolos = Protocolo::where('estado', '1')->get();

        $empresa = Empresa::find($orden->id_empresa);

        //dd($orden);
        $arreglo    = [];
        $arreglo1   = [];
        $nro        = 0;
        $agrupa_ant = 0;
        foreach ($examenes as $examen) {

            //dd($examen);
            if ($agrupa_ant != $examen->id_agrupador) {
                $nro        = 0;
                $agrupa_ant = $examen->id_agrupador;
                $arreglo1   = [];
            }
            $arreglo1[$nro] = $examen->id;

            $arreglo[$examen->id_agrupador] = $arreglo1;
            $nro++;
        }

        $arrayTotal = [
            [
                'PRUEBA ALERGIA CLARA', 'PRUEBA ALERGIA ESPECIFICA', 'PRUEBA ALERGIA FRESA O FRUTILLA',
                'PRUEBA ALERGIA LECHE', 'PRUEBA ALERGIA MANI', 'PRUEBA ALERGIA NARANJA', 'PRUEBA ALERGIA YEMA',
                'TEST DE UREA EN ALIENTO, C-14 (ISOTÓPICO), ADQUISICIÓN PARA ANÁLISIS', 'TEST DE UREA EN ALIENTO, C-14 (ISOTÓPICO), ANÁLISIS'
            ],
            ['ADQUISICIÓN PARA ANÁLISIS', 'ANÁLISIS', 'HELICOBACTER PYL.IGG'],
            ['CURVA DE LACTOSA', 'PRUEBA ALERGIA LECHE'],
            ['FRUCTOSAMINA', 'PRUEBA ALERGIA NARANJA'],

        ];

        //dd(count($arreglo[3]));

        if (!is_null($orden)) {
            $tipo_usuario = Auth::user()->id_tipo_usuario;
            //return $tipo_usuario;

            $vistaurl = "laboratorio.orden.orden_recepcion";
            if ($tipo_usuario == '1') {
                $vistaurl = "laboratorio.orden.orden";
            }

            $view     = \View::make($vistaurl, compact('arrayTotal', 'orden', 'usuarios', 'examenes', 'agrupadores', 'detalles', 'empresa', 'seguro', 'protocolos', 'arreglo', 'seleccionados', 'tipo_usuario'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'chroot'  => base_path('/')]);

            $vt_name = '';
            if($tipo_usuario == 11 ){
                $vt_name = 'HCU_010.pdf';
                return $pdf->stream($vt_name); 
            }
            return $pdf->stream('orden-de-laboratorio-' . $id . '.pdf');
            //return view('laboratorio/orden/orden', ['orden' => $orden,'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresa]);

        }
    }

    public function buscaexamendb($id_orden, $id_examen)
    {

        //return $id_examen;

        $id_examen = substr($id_examen, 2);

        $examen = Examen_Detalle::where('id_examen_orden', $id_orden)->where('id_examen', $id_examen)->first();

        $examen_pri = Examen::find($id_examen);

        //return $examen->id;
        if (!is_null($examen)) {
            if ($examen_pri->publico_privado == '1') {
                return "no";
            }

            return "ok";
        }

        return "no";
    }

    public function pentax_semaforo()
    {

        $fecha = Date('Y-m-d');

        $pentax = DB::table('agenda')->where('agenda.estado', '1')->where('agenda.proc_consul', '1')->join('pentax', 'pentax.id_agenda', 'agenda.id')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->join('users', 'users.id', '=', 'pentax.id_doctor1')->leftJoin('users as u2', 'u2.id', '=', 'pentax.id_doctor2')->leftJoin('users as u3', 'u3.id', '=', 'pentax.id_doctor3')->join('seguros', 'seguros.id', '=', 'pentax.id_seguro')->join('sala', 'sala.id', '=', 'agenda.id_sala')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'procedimiento.observacion as pobservacion', 'users.nombre1 as dnombre1', 'users.apellido1 as dapellido1', 'users.color as dcolor', 'u2.nombre1 as d2nombre1', 'u2.apellido1 as d2apellido1', 'u3.nombre1 as d3nombre1', 'u3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'seguros.color as scolor', 'sala.nombre_sala', 'pentax.id as pentax', 'pentax.estado_pentax', 'pentax.id_doctor1 as pid_doctor1')->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha . ' 23:59'])->where('sala.id_hospital', '2')->orderBy('agenda.fechaini')->get();

        $procedimientos = DB::table('procedimiento')->get();

        $semaforo_controller = new SemaforoController();
        $pentax_pend         = $semaforo_controller->Cargar_pendientes($fecha);

        //dd(array_key_exists($pentax['0']->id,$pentax_pend));
        //dd($pentax_pend[$pentax['0']->id]);

        return view('laboratorio/orden/pentax_semaforo', ['pentax' => $pentax, 'procedimientos' => $procedimientos, 'pentax_pend' => $pentax_pend]);
    }

    public function estad_mes(Request $request)
    {

        $estadistico_total = [];

        if ($request->anio == null) {
            $anio = Date('Y');
        } else {
            $anio = $request->anio;
        }

        $convenios = DB::table('convenio as c')->join('seguros as s', 's.id', 'c.id_seguro')->join('empresa as e', 'e.id', 'c.id_empresa')->select('c.*', 's.nombre', 'e.nombre_corto')->orderBy('c.id')->get();

        for ($i = 1; $i <= 12; $i++) {

            $ordenes_mes = Examen_Orden::where('anio', $anio)->where('mes', $i)->where('realizado', '1')->select('anio', 'mes')->groupBy('anio', 'mes');
            if ($ordenes_mes->count() > 0) {

                $estadistico_conv = [];
                foreach ($convenios as $convenio) {

                    $ordenes_mes_convenio = Examen_Orden::where('anio', $anio)->where('mes', $i)->where('realizado', '1')->where('id_seguro', $convenio->id_seguro)->where('id_empresa', $convenio->id_empresa)->select('anio', 'mes', 'id_seguro', 'id_empresa')->groupBy('anio', 'mes', 'id_seguro', 'id_empresa');

                    $estadistico_conv[$convenio->id] = ['ordenes' => $ordenes_mes_convenio->count(), 'cantidad' => $ordenes_mes_convenio->sum('cantidad'), 'valor' => $ordenes_mes_convenio->sum('valor')];
                }

                $estadistico_total[$i] = ['mes' => $i, 'ordenes' => $ordenes_mes->count(), 'examenes' => $ordenes_mes->sum('cantidad'), 'valor' => $ordenes_mes->sum('valor'), 'convenios' => $estadistico_conv];
            }
            $ordenes_part   = Examen_Orden::where('anio', $anio)->where('mes', $i)->where('realizado', '1')->select('anio', 'mes')->groupBy('anio', 'mes')->where('id_seguro', '1');
            $estad_part[$i] = ['cantidad' => $ordenes_part->sum('cantidad'), 'valor' => $ordenes_part->sum('valor'), 'ordenes' => $ordenes_part->count()];
        }

        $or_anio = DB::table('examen_orden')
            ->select('anio')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(total_valor - recargo_valor) as valor')
            ->orderBy('anio')
            ->groupBy('anio')
            ->where('estado', '1')
            ->where('anio', '>', '0')
            ->where('realizado', '1')
            ->get();
        //dd($or_anio);

        $or_anio_tipo = DB::table('examen_orden')
            ->join('seguros as s', 's.id', 'id_seguro')
            ->select('anio', 's.tipo')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(total_valor - recargo_valor) as valor')
            ->orderBy('anio', 's.tipo')
            ->groupBy('anio', 's.tipo')
            ->where('estado', '1')
            ->where('realizado', '1')
            ->get();

        //dd($or_anio_tipo);
        $arr_anio_tipo = null;
        foreach ($or_anio_tipo as $value) {
            $arr_anio_tipo[$value->anio . '-' . $value->tipo] = [$value->cantidad, $value->valor];
        }
        //dd($arr_anio_tipo);

        $or_anio_mes = DB::table('examen_orden')
            ->select('anio', 'mes')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(total_valor - recargo_valor) as valor')
            ->selectRaw('sum(total_valor) as valor_total')
            ->orderBy('anio', 'mes')
            ->groupBy('anio', 'mes')
            ->where('estado', '1')
            ->where('realizado', '1')
            ->where('anio', $anio)
            ->get();

        $or_anio_mes_tipo = DB::table('examen_orden')
            ->join('seguros as s', 's.id', 'id_seguro')
            ->select('anio', 'mes', 's.tipo')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(total_valor - recargo_valor) as valor')
            ->orderBy('anio', 'mes', 's.tipo')
            ->groupBy('anio', 'mes', 's.tipo')
            ->where('estado', '1')
            ->where('realizado', '1')
            ->where('anio', $anio)
            ->get();

        /*
        $or_anio_mes_fact = DB::table('examen_orden as eo')
        ->join('ct_ventas as cv', 'cv.nro_comprobante', 'eo.comprobante')
        ->select('anio', 'mes')
        ->selectRaw('count(*) as cantidad')
        ->selectRaw('sum(eo.total_valor) as valor')
        ->orderBy('eo.anio', 'mes')
        ->groupBy('eo.anio', 'mes')
        ->where('eo.estado', '1')
        ->where('eo.realizado', '1')
        ->where('cv.id_empresa', '0993075000001')
        ->where('anio', $anio)
        ->get();

        $or_anio_mes_fact2 = DB::table('examen_orden as eo')
        ->join('ct_ventas as cv', 'cv.nro_comprobante', 'eo.comprobante')
        ->select('eo.anio', 'eo.mes', 'cv.*')
        ->orderBy('eo.anio', 'mes')
        ->where('eo.estado', '1')
        ->where('eo.realizado', '1')
        ->where('cv.id_empresa', '0993075000001')
        ->where('anio', $anio)
        ->get();

        $arr_fact  = [];
        $arr_fact2 = [];

        foreach ($or_anio_mes_fact2 as $value) {
        $ingreso = DB::table('ct_detalle_comprobante_ingreso as ci')
        ->where('ci.id_factura', $value->id)->sum('total');
        $arr_fact[$value->anio . '-' . $value->mes][$value->id] = [$value->total_final, $ingreso];
        }
        foreach ($arr_fact as $key => $value) {
        $sum_pag  = 0;
        $sum_fact = 0;
        foreach ($value as $subvalue) {
        $sum_fact += $subvalue[0];
        $sum_pag += $subvalue[1];

        }
        $arr_fact2[$key] = [$sum_fact, $sum_pag];
        }*/

        $arr_aniomes_tipo = null;
        foreach ($or_anio_mes_tipo as $value) {
            $arr_aniomes_tipo[$value->anio . '-' . $value->mes . '-' . $value->tipo] = [$value->cantidad, $value->valor];
        }

        /*$arr_aniomes_fact = null;
        foreach ($or_anio_mes_fact as $value) {
        $arr_aniomes_fact[$value->anio . '-' . $value->mes] = [$value->cantidad, $value->valor];
        }*/

        //return view('laboratorio/estadistico/index', ['estadistico_total' => $estadistico_total, 'convenios' => $convenios, 'anio' => $anio, 'estad_part' => $estad_part, 'or_anio' => $or_anio, 'or_anio_mes' => $or_anio_mes, 'arr_anio_tipo' => $arr_anio_tipo, 'arr_aniomes_tipo' => $arr_aniomes_tipo, 'arr_aniomes_fact' => $arr_aniomes_fact, 'arr_fact2' => $arr_fact2]);

        return view('laboratorio/estadistico/index', ['estadistico_total' => $estadistico_total, 'convenios' => $convenios, 'anio' => $anio, 'estad_part' => $estad_part, 'or_anio' => $or_anio, 'or_anio_mes' => $or_anio_mes, 'arr_anio_tipo' => $arr_anio_tipo, 'arr_aniomes_tipo' => $arr_aniomes_tipo]);
    }
    public function estad_examen($mes, $anio)
    {

        //dd($mes,$anio);
        $estadistico = [];

        $convenios = DB::table('convenio as c')->join('seguros as s', 's.id', 'c.id_seguro')->join('empresa as e', 'e.id', 'c.id_empresa')->select('c.*', 's.nombre', 'e.nombre_corto')->orderBy('c.id')->get();

        $examenes = Examen::all();
        //dd($examenes);

        foreach ($convenios as $convenio) {

            $total_conv[$convenio->id] = ['cantidad' => 0, 'valor' => 0];
        }

        $total_cantidad   = 0;
        $total_valor      = 0;
        $total_part_cant  = 0;
        $total_part_valor = 0;

        foreach ($examenes as $examen) {

            $ordenes_mes = DB::table('examen_orden as eo')->where('eo.anio', $anio)->where('eo.mes', $mes)->where('realizado', '1')->join('examen_detalle as ed', 'ed.id_examen_orden', 'eo.id')->where('ed.id_examen', $examen->id)->select('eo.anio', 'eo.mes', 'ed.id_examen')->groupBy('eo.anio', 'eo.mes', 'ed.id_examen');

            $estadistico_conv = [];

            //dd($total_cantidad);

            foreach ($convenios as $convenio) {

                $ordenes_mes_convenio = DB::table('examen_orden as eo')->where('eo.anio', $anio)->where('eo.mes', $mes)->where('realizado', '1')->where('eo.id_seguro', $convenio->id_seguro)->where('eo.id_empresa', $convenio->id_empresa)->join('examen_detalle as ed', 'ed.id_examen_orden', 'eo.id')->where('ed.id_examen', $examen->id)->select('eo.anio', 'eo.mes', 'ed.id_examen', 'eo.id_seguro', 'eo.id_empresa')->groupBy('eo.anio', 'eo.mes', 'ed.id_examen', 'eo.id_seguro', 'eo.id_empresa');

                $estadistico_conv[$convenio->id] = ['cantidad' => $ordenes_mes_convenio->count(), 'valor' => $ordenes_mes_convenio->sum('ed.valor')];

                $total_conv[$convenio->id] = ['cantidad' => $total_conv[$convenio->id]['cantidad'] + $estadistico_conv[$convenio->id]['cantidad'], 'valor' => $total_conv[$convenio->id]['valor'] + $estadistico_conv[$convenio->id]['valor']];
            }

            $ordenes_mes_part = DB::table('examen_orden as eo')->where('eo.anio', $anio)->where('eo.mes', $mes)->where('realizado', '1')->join('examen_detalle as ed', 'ed.id_examen_orden', 'eo.id')->where('ed.id_examen', $examen->id)->select('eo.anio', 'eo.mes', 'ed.id_examen')->groupBy('eo.anio', 'eo.mes', 'ed.id_examen')->where('id_seguro', '1');

            //$estad_part[$examen->id] = ['cantidad' => $ordenes_mes_part->count(), 'valor' => $ordenes_mes_part->sum('ed.valor')];

            //dd($estad_part);

            $total_cantidad = $total_cantidad + $ordenes_mes->count();
            $total_valor    = $total_valor + $ordenes_mes->sum('ed.valor');

            $total_part_cant  = $total_part_cant + $ordenes_mes_part->count();
            $total_part_valor = $total_part_valor + $ordenes_mes_part->sum('ed.valor');

            $estadistico[$examen->id] = ['examen' => $examen->nombre, 'cantidad' => $ordenes_mes->count(), 'valor' => $ordenes_mes->sum('ed.valor'), 'convenios' => $estadistico_conv, 'cant_part' => $ordenes_mes_part->count(), 'val_part' => $ordenes_mes_part->sum('ed.valor')];
            //dd($ordenes_mes->get());

        }

        //dd($estadistico_conv,$total_cantidad);

        //dd($estadistico);
        //return "ok";
        return view('laboratorio/estadistico/index_examen', ['estadistico' => $estadistico, 'convenios' => $convenios, 'mes' => $mes, 'anio' => $anio, 'total_conv' => $total_conv, 'total_cantidad' => $total_cantidad, 'total_valor' => $total_valor, 'total_part_cant' => $total_part_cant, 'total_part_valor' => $total_part_valor]);
    }

    public function to_excel($mes, $anio)
    {

        //dd($mes,$anio);
        $estadistico = [];

        $convenios = DB::table('convenio as c')->join('seguros as s', 's.id', 'c.id_seguro')->join('empresa as e', 'e.id', 'c.id_empresa')->select('c.*', 's.nombre', 'e.nombre_corto')->orderBy('id_seguro')->get();

        $examenes = Examen::where('publico_privado', '0')->get();
        //dd($examenes);

        foreach ($convenios as $convenio) {

            $total_conv[$convenio->id] = ['cantidad' => 0, 'valor' => 0];
        }

        $total_cantidad = 0;
        $total_valor    = 0;

        foreach ($examenes as $examen) {

            $ordenes_mes = DB::table('examen_orden as eo')->where('eo.anio', $anio)->where('eo.mes', $mes)->where('realizado', '1')->join('examen_detalle as ed', 'ed.id_examen_orden', 'eo.id')->where('ed.id_examen', $examen->id)->select('eo.anio', 'eo.mes', 'ed.id_examen')->groupBy('eo.anio', 'eo.mes', 'ed.id_examen');

            $estadistico_conv = [];

            //dd($total_cantidad);

            foreach ($convenios as $convenio) {

                $ordenes_mes_convenio = DB::table('examen_orden as eo')->where('eo.anio', $anio)->where('eo.mes', $mes)->where('realizado', '1')->where('eo.id_seguro', $convenio->id_seguro)->where('eo.id_empresa', $convenio->id_empresa)->join('examen_detalle as ed', 'ed.id_examen_orden', 'eo.id')->where('ed.id_examen', $examen->id)->select('eo.anio', 'eo.mes', 'ed.id_examen', 'eo.id_seguro', 'eo.id_empresa')->groupBy('eo.anio', 'eo.mes', 'ed.id_examen', 'eo.id_seguro', 'eo.id_empresa');

                $estadistico_conv[$convenio->id] = ['cantidad' => $ordenes_mes_convenio->count(), 'valor' => $ordenes_mes_convenio->sum('ed.valor')];

                $total_conv[$convenio->id] = ['cantidad' => $total_conv[$convenio->id]['cantidad'] + $estadistico_conv[$convenio->id]['cantidad'], 'valor' => $total_conv[$convenio->id]['valor'] + $estadistico_conv[$convenio->id]['valor']];
            }

            $total_cantidad = $total_cantidad + $ordenes_mes->count();
            $total_valor    = $total_valor + $ordenes_mes->sum('ed.valor');

            $estadistico[$examen->id] = ['examen' => $examen->nombre, 'cantidad' => $ordenes_mes->count(), 'valor' => $ordenes_mes->sum('ed.valor'), 'convenios' => $estadistico_conv];
            //dd($ordenes_mes->get());

        }

        $fecha_d = date('Y/m/d');

        Excel::create('Examenes_Mes-' . $fecha_d, function ($excel) use ($mes, $anio, $convenios, $estadistico, $total_conv, $total_cantidad, $total_valor) {

            $excel->sheet('Examenes', function ($sheet) use ($mes, $anio, $convenios, $estadistico, $total_conv, $total_cantidad, $total_valor) {

                $a_mes   = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
                $fecha_d = date('Y/m/d');
                $i       = 5;

                $sheet->mergeCells('A3:P3');

                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);

                $sheet->cells('A1:P5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A3:P5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A3', function ($cell) use ($mes, $anio, $a_mes) {
                    // manipulate the cel
                    $cell->setValue('ESTADISTICO DE EXAMENES DE ' . $a_mes[$mes] . '/' . $anio);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:I4');
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setAlignment('center');
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('J4:P4');
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setAlignment('center');
                    $cell->setValue('VALOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EXAMEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $letras = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
                $l      = 2;

                foreach ($convenios as $convenio) {
                    $sheet->cell($letras[$l] . '5', function ($cell) use ($convenio) {
                        // manipulate the cel
                        $cell->setValue($convenio->nombre . ' ' . $convenio->nombre_corto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $l++;
                }
                $sheet->cell($letras[$l] . '5', function ($cell) use ($convenio) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $l++;
                foreach ($convenios as $convenio) {
                    $sheet->cell($letras[$l] . '5', function ($cell) use ($convenio) {
                        // manipulate the cel
                        $cell->setValue($convenio->nombre . ' ' . $convenio->nombre_corto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $l++;
                }
                $sheet->cell($letras[$l] . '5', function ($cell) use ($convenio) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $l++;

                $i = 6;
                $x = 1;
                foreach ($estadistico as $value) {
                    if ($value['cantidad'] > 0) {
                        $sheet->cell('A' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontWeight('bold');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value['examen']);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontWeight('bold');
                        });
                        $l = 2;

                        foreach ($value['convenios'] as $convenio) {
                            $sheet->cell($letras[$l] . $i, function ($cell) use ($convenio) {
                                // manipulate the cel
                                $cell->setValue($convenio['cantidad']);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $l++;
                        }
                        $sheet->cell($letras[$l] . $i, function ($cell) use ($convenio, $value) {
                            // manipulate the cel
                            $cell->setBackground('#FFE4E1');
                            $cell->setValue($value['cantidad']);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $l++;
                        foreach ($value['convenios'] as $convenio) {
                            $sheet->cell($letras[$l] . $i, function ($cell) use ($convenio) {
                                // manipulate the cel
                                $cell->setValue(round($convenio['valor'], 2));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $l++;
                        }
                        $sheet->cell($letras[$l] . $i, function ($cell) use ($convenio, $value) {
                            // manipulate the cel
                            $cell->setBackground('#FFE4E1');
                            $cell->setValue(round($value['valor'], 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $l++;

                        $i++;
                        $x++;
                    }
                    $sheet->cell('A' . $i, function ($cell) use ($value, $x) {
                        // manipulate the cel
                        $cell->setValue($x);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontWeight('bold');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue('TOTAL');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontWeight('bold');
                    });
                    $l = 2;
                    foreach ($total_conv as $convenio) {
                        $sheet->cell($letras[$l] . $i, function ($cell) use ($convenio) {
                            // manipulate the cel
                            $cell->setValue($convenio['cantidad']);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontWeight('bold');
                        });
                        $l++;
                    }
                    $sheet->cell($letras[$l] . $i, function ($cell) use ($convenio, $total_cantidad) {
                        // manipulate the cel
                        $cell->setBackground('#FFE4E1');
                        $cell->setValue($total_cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontWeight('bold');
                    });
                    $l++;
                    foreach ($total_conv as $convenio) {
                        $sheet->cell($letras[$l] . $i, function ($cell) use ($convenio) {
                            // manipulate the cel
                            $cell->setValue(round($convenio['valor'], 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontWeight('bold');
                        });
                        $l++;
                    }
                    $sheet->cell($letras[$l] . $i, function ($cell) use ($convenio, $total_valor) {
                        // manipulate the cel
                        $cell->setBackground('#FFE4E1');
                        $cell->setValue(round($total_valor, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontWeight('bold');
                    });
                }
            });
        })->export('xlsx');
    }

    public function genera_costo()
    {

        if ($this->rol_sis()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $detalles = Examen_detalle::all();
        //dd($detalles);
        foreach ($detalles as $detalle) {
            $examen = Examen::find($detalle->id_examen);
            //dd($examen->valor_reactivo);
            $input = [

                'id_examen_detalle' => $detalle->id,
                'id_examen'         => $detalle->id_examen,
                'valor_reactivo'    => $examen->valor_reactivo,
                'valor_implemento'  => $examen->valor_implementos,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,

            ];
            $costo = Examen_Detalle_Costo::where('id_examen_detalle', $detalle->id)->first();
            if (is_null($costo)) {
                Examen_Detalle_Costo::create($input);
            }
        }
        return "CARGADO";
    }

    public function imprimir_resultado2($id)
    {

        $orden      = Examen_Orden::find($id);
        $detalle    = Examen_Detalle::where('id_examen_orden', $id)->get();
        $resultados = Examen_resultado::where('id_orden', '=', $id)->get();
        $agrupador  = Examen_Agrupador::all();
        $parametros = Examen_parametro::all();
        $ucreador   = User::find($orden->id_usuariocrea);
        $age        = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        //dd($age,$orden->paciente->fecha_nacimiento);

        $vistaurl = "laboratorio.orden.resultados_pdf2";
        $view     = \View::make($vistaurl, compact('orden', 'detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador'))->render();

        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);

        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        //dd($pdf);

        return $pdf->download('resultado-' . $id . '.pdf');
    }

    public function imprimir_resultado3($id)
    {
        //FORMATO GASTROCLINICA

        $orden = Examen_Orden::find($id);
        //$detalle = Examen_Detalle::where('id_examen_orden', $id)->get();
        $detalle    = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        $resultados = Examen_resultado::where('id_orden', '=', $id)->get();
        $agrupador  = Examen_Agrupador::all();
        //$parametros = Examen_parametro::all();
        $parametros = Examen_parametro::orderBy('orden')->get();
        $ucreador   = User::find($orden->id_usuariocrea);
        $age        = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        //dd($age,$orden->paciente->fecha_nacimiento);

        $vistaurl = "laboratorio.orden.resultados_pdf_gastro";
        $view     = \View::make($vistaurl, compact('orden', 'detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador'))->render();

        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);

        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        //dd($pdf);

        return $pdf->download('resultado-' . $id . '.pdf');
    }

    public function convenio_buscar($seguro, $empresa)
    {

        $convenio = Convenio::where('id_seguro', $seguro)->where('id_empresa', $empresa)->first();
        if (!is_null($convenio)) {
            return $convenio->id_nivel;
        }

        return "no";
    }

    public function convenio_buscar_examen($nivel, $examen)
    {

        $ex_nivel = Examen_Nivel::where('id_examen', $examen)->where('nivel', $nivel)->first();
        if (!is_null($ex_nivel)) {
            return "ok";
        }

        return "no";
    }

    public function detalle_valor($id)
    {

        if ($this->rol_supervision()) {
            return response()->view('errors.404');
        }

        $detalles = Examen_Detalle::where('id_examen_orden', $id)->get();

        //dd($detalles['0']->examen->descripcion);

        return view('laboratorio/orden/detalle_valor', ['detalles' => $detalles]);
    }

    public function carga_totales()
    {

        if ($this->rol_sis()) {
            return response()->view('errors.404');
        }

        $ordenes = Examen_Orden::where('fecha_orden', null)->get();
        //dd($ordenes);
        foreach ($ordenes as $value) {

            $input = [
                'fecha_orden'     => $value->created_at,

                'ip_modificacion' => 'AUTOM_FO',

            ];

            $value->update($input);
        }
        return "listo";
    }

    /*public function carga_totales(){

    if($this->rol_sis()){
    return response()->view('errors.404');
    }

    $ordenes = Examen_Orden::where('total_valor','0')->get();
    //dd($ordenes);
    foreach ($ordenes as $value) {
    $valor_descuento = 0;
    $sub_total_des;

    $valor_descuento = $value->valor * $value->descuento_p / 100;
    //dd($valor_descuento);
    $valor_descuento = round($valor_descuento,2);

    $sub_total_des = $value->valor - $valor_descuento;
    //dd($sub_total_des);
    $forma_pago = Forma_de_pago::where('id',$value->id_forma_de_pago)->first();
    if(is_null($forma_pago)){
    $porcentaje_recargo = 0;
    }else{
    $porcentaje_recargo = $forma_pago->recargo_p;
    }
    $valor_recargo = $sub_total_des * $porcentaje_recargo / 100;
    $valor_recargo = round($valor_recargo,2);
    $valor_total =  $sub_total_des + $valor_recargo;
    //dd($valor_total);
    $input = [
    'recargo_p' => $porcentaje_recargo,
    'recargo_valor' => $valor_recargo,
    'descuento_valor' => $valor_descuento,
    'total_valor' => $valor_total,
    'ip_modificacion' => 'AUTOM'

    ];

    $value->update($input);

    }
    return "listo";

    }*/

    public function validacion(Request $request)
    {

        if ($request->id == null) {
            return "<h5 style='color: #ff6600;'>INGRESE EL PACIENTE</h5>";
        }
        if ($request->nombre1 == null) {
            return "<h5 style='color: #ff6600;'>INGRESE EL PACIENTE</h5>";
        }
        if ($request->apellido1 == null) {
            return "<h5 style='color: #ff6600;'>INGRESE EL PACIENTE</h5>";
        }
        if ($request->sexo == null) {
            return "<h5 style='color: #ff6600;'>INGRESE EL SEXO</h5>";
        }
        if ($request->fecha_nacimiento == null) {
            return "<h5 style='color: #ff6600;'>INGRESE LA FECHA DE NACIMIENTO</h5>";
        }
        if ($request->id_doctor_ieced == null) {
            return "<h5 style='color: #ff6600;'>INGRESE EL MEDICO</h5>";
        }
        if ($request->pres_dom == null) {
            return "<h5 style='color: #ff6600;'>SELECCIONE DOMICILIO O PRESENCIAL</h5>";
        }

        if ($request->id_seguro != null) {
            $seguro   = Seguro::find($request->id_seguro);
            $convenio = Convenio::where('id_seguro', $request->id_seguro)->where('id_empresa', $request->id_empresa)->get();
            if ($convenio->count() > 1) {
                if ($request->id_nivel == null) {
                    //return $request->all();
                    return "<h5 style='color: #ff6600;'>SELECCIONE EL NIVEL PARA REALIZAR LA COTIZACIÓN</h5>";
                }
            }
        } else {

            return "<h5 style='color: #ff6600;'>SELECCIONE EL SEGURO PARA REALIZAR LA COTIZACIÓN</h5>";
        }

        if ($request->descuento_p > 0) {
            if ($request->motivo_descuento == null) {
                return "<h5 style='color: #ff6600;'>INGRESE QUIEN AUTORIZA EL DESCUENTO Y POR QUE MEDIO LO REALIZA</h5>";
            }
        }

        return "OK";
    }

    public function agrupador_labs_buscar(Request $request)
    {

        //dd($request->all());
        if ($this->validacion($request) != 'OK') {
            return $this->validacion($request);
        }

        $agrupador_labs = DB::table('examen_agrupador_labs');
        $examenes_labs  = DB::table('examen_agrupador_sabana as sa')->join('examen as e', 'e.id', 'sa.id_examen')->where('e.estado', '1')->where('sa.estado', '1')->select('sa.*', 'e.descripcion', 'e.nombre', 'e.valor', 'e.id as ex_id', 'e.tiempos as tiempo_examen', 'e.sugerencia', 'e.nombre_largo');

        if ($request->seleccionados == '1' && $request->buscador == null && $request->buscador2 == null) {

            $examenes_labs = DB::table('examen_detalle as ed')->where('id_examen_orden', $request->cotizacion)->join('examen as e', 'e.id', 'ed.id_examen')->join('examen_agrupador_sabana as sa', 'sa.id_examen', 'ed.id_examen')->join('examen_agrupador_labs as l', 'l.id', 'sa.id_examen_agrupador_labs')->select('sa.*', 'e.descripcion', 'e.nombre', 'e.valor', 'e.id as ex_id', 'l.nombre as lnombre', 'e.tiempos as tiempo_examen', 'e.nombre_largo', 'e.sugerencia');
        }

        $seguro = Seguro::find($request->id_seguro);
        //$convenio = DB::table('convenio as c')->where('c.id_seguro', $seguro->id)->get();
        $examen_valor = null;
        if ($request->id_nivel != null) {
            $examen_valor = Examen_Nivel::where('nivel', $request->id_nivel)->get();
        }

        if ($request->buscador != null) {
            $examenes_labs = $examenes_labs->where(function ($query) use ($request) {
                $query->where('e.descripcion', 'like', '%' . $request->buscador . '%')
                    ->orWhere('e.nombre_largo', 'like', '%' . $request->buscador . '%');
            });
        }

        if ($request->buscador2 != null) {
            $agrupador_labs = $agrupador_labs->where('nombre', 'like', '%' . $request->buscador2 . '%');
        }

        $examenes_labs = $examenes_labs->orderBy('sa.nro_orden')->get();

        $detalles_ch = [];
        $i           = 0;
        /*foreach ($examenes_labs as $examen) {
        $detalle = Examen_Detalle::where('id_examen_orden',$request->cotizacion)->where('id_examen',$examen->ex_id)->first();
        if(!is_null($detalle)){
        $detalles_ch[$i] = $examen->ex_id;
        $i = $i + 1;
        }

        }*/

        $orden = Examen_Orden::find($request->cotizacion);

        //$nuevo_detalles = Examen_Detalle::where('id_examen_orden', $request->cotizacion)->get();
        $nuevo_detalles = $orden->detalles;
        if ($nuevo_detalles->count() == 0 && $request->buscador == null && $request->buscador2 == null) {
            $agrupador_labs = $agrupador_labs->limit(10);
        }

        foreach ($nuevo_detalles as $nuevo_detalle) {
            $detalles_ch[$i] = $nuevo_detalle->id_examen;
            $i               = $i + 1;
        }
        $agrupador_labs = $agrupador_labs->get();

        //return $detalles_ch;
        $tiene_domicilio = 0;
        $dom             = $orden->detalles->where('id_examen', '1203')->first();
        if (!is_null($dom)) {
            $tiene_domicilio = 1;
        }

        $tiene_covid = 0;
        $valor_covid = 0;
        $covid       = $orden->detalles->where('id_examen', '1191')->first();
        if (is_null($covid)) {
            $covid = $orden->detalles->where('id_examen', '1195')->first();
            if (is_null($covid)) {
                $covid = $orden->detalles->where('id_examen', '1196')->first();
            }
        }
        if (!is_null($covid)) {
            $tiene_covid = 1;
            $valor_covid = $covid->valor;
        }

        $agenda = Examen_Orden_Agenda::where('id_orden', $orden->id)->first();

        return view('laboratorio.orden.listado', ['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'seguro' => $seguro, 'id_nivel' => $request->id_nivel, 'cotizacion' => $request->cotizacion, 'detalles_ch' => $detalles_ch, 'orden' => $orden, 'examen_valor_o' => $examen_valor, 'tiene_domicilio' => $tiene_domicilio, 'tiene_covid' => $tiene_covid, 'valor_covid' => $valor_covid, 'agenda' => $agenda]);
    }

    public function agrupador_labs_nivel(Request $request)
    {

        $convenios = DB::table('convenio as c')->where('c.id_seguro', $request->id_seguro)->where('c.estado', 1)->join('nivel as n', 'n.id', 'c.id_nivel')->select('c.*', 'n.nombre', 'n.id as id_nivel')->get();

        $orden = null;
        if ($request->cotizacion != null) {
            $orden = Examen_Orden::find($request->cotizacion);
        }

        if ($convenios->count() > 0) {
            return view('laboratorio.orden.niveles', ['convenios' => $convenios, 'orden' => $orden]);
        }

        return "no";
    }

    public function obtener_convenio_seguro(Request $request)
    {

        $id_nivel = $request->id_nivel; //dd($id_nivel);
        $id_seguro = $request->id_seguro;
        $convenios = DB::table('convenio as c')->where('c.id_seguro', $id_seguro)->join('nivel as n', 'n.id', 'c.id_nivel')->select('c.*', 'n.nombre', 'n.id as id_nivel')->get();

        if ($convenios->count() > 0) {
            return view('laboratorio.orden.niveles2', ['convenios' => $convenios, 'id_nivel' => $id_nivel]);
        }

        return "no";
    }

    public function cotizador_store(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if ($this->validacion($request) != 'OK') {
            return $this->validacion($request);
        }

        if ($request->id_nivel == null) {
            $convenio = Convenio::where('id_seguro', $request->id_seguro)->where('id_empresa', $request->id_empresa)->first();
        } else {
            $convenio = Convenio::where('id_seguro', $request->id_seguro)->where('id_empresa', $request->id_empresa)->where('id_nivel', $request->id_nivel)->first();
        }

        $seguro = Seguro::find($request->id_seguro);

        //CREAR USUARIO
        $input_usu_c = [

            'id'               => $request['id'],
            'nombre1'          => strtoupper($request['nombre1']),
            'nombre2'          => strtoupper($request['nombre2']),
            'apellido1'        => strtoupper($request['apellido1']),
            'apellido2'        => strtoupper($request['apellido2']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'telefono1'        => '1',
            'telefono2'        => '1',
            'id_tipo_usuario'  => 2,
            'email'            => $request['id'] . '@mail.com',
            'password'         => bcrypt($request['id']),
            'tipo_documento'   => 1,
            'estado'           => 1,
            'imagen_url'       => ' ',
            'ip_creacion'      => $ip_cliente,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariocrea'   => $idusuario,
            'id_usuariomod'    => $idusuario,

        ];

        $user = User::find($request['id']);

        $input_pac = [

            'id'                 => $request['id'],
            'id_usuario'         => $request['id'],
            'nombre1'            => strtoupper($request['nombre1']),
            'nombre2'            => strtoupper($request['nombre2']),
            'apellido1'          => strtoupper($request['apellido1']),
            'apellido2'          => strtoupper($request['apellido2']),
            'fecha_nacimiento'   => $request['fecha_nacimiento'],
            'sexo'               => $request['sexo'],
            'telefono1'          => '1',
            'telefono2'          => '1',
            'nombre1familiar'    => strtoupper($request['nombre1']),
            'nombre2familiar'    => strtoupper($request['nombre2']),
            'apellido1familiar'  => strtoupper($request['apellido1']),
            'apellido2familiar'  => strtoupper($request['apellido2']),
            'parentesco'         => 'Principal',
            'parentescofamiliar' => 'Principal',
            'tipo_documento'     => 1,
            'id_seguro'          => 1,
            'imagen_url'         => ' ',
            'menoredad'          => 0,

            'ip_creacion'        => $ip_cliente,
            'ip_modificacion'    => $ip_cliente,
            'id_usuariocrea'     => $idusuario,
            'id_usuariomod'      => $idusuario,

        ];

        $paciente = Paciente::find($request['id']);

        if (is_null($paciente)) {

            if (!is_null($user)) {
                //$user->update($input_usu_a);
            } else {
                User::create($input_usu_c);
            }

            paciente::create($input_pac);

            $input_log = [
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['id'],
                'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                'dato_ant4'   => "COTIZACION",
                'dato2'       => 'COTIZACION',
            ];

            Log_usuario::create($input_log);
        } else {
            if ($paciente->fecha_nacimiento == null || $paciente->sexo == null) {

                $pac = [
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'sexo'             => $request['sexo'],
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                ];
                $paciente->update($pac);
            }
        }

        $nivel    = null;
        $valor    = 0;
        $cont     = 0;
        $total    = 0;
        $examenes = Examen::all();
        foreach ($examenes as $examen) {
            if (!is_null($request['ch' . $examen->id])) {
                $cont++;

                $valor = $examen->valor;
                if (!is_null($convenio)) {
                    $nivel = $convenio->id_nivel;
                    //dd($nivel);
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            }
        }

        //dd($nivel);

        //dd($input_ex);

        if ($request->cotizacion != null) {
            $input_ex = [

                'id_seguro'       => $request['id_seguro'],
                'id_nivel'        => $request->id_nivel,
                'est_amb_hos'     => $request['est_amb_hos'],
                'id_doctor_ieced' => $request['id_doctor_ieced'],
                'doctor_txt'      => $request['doctor_txt'],
                'observacion'     => $request['observacion'],
                'id_empresa'      => $request['id_empresa'],
                'cantidad'        => $cont,
                'estado'          => '-1',
                'valor'           => $total,

                'ip_modificacion' => $ip_cliente,

                'id_usuariomod'   => $idusuario,

            ];
            Examen_Orden::find($request->cotizacion)->update($input_ex);
            $id_examen_orden = $request->cotizacion;
        } else {
            $input_ex = [
                'id_paciente'     => $request['id'],
                'anio'            => substr(date('Y-m-d'), 0, 4),
                'mes'             => substr(date('Y-m-d'), 5, 2),
                'id_protocolo'    => $request['id_protocolo'],
                'id_seguro'       => $request['id_seguro'],
                'id_nivel'        => $request->id_nivel,
                'est_amb_hos'     => $request['est_amb_hos'],
                'id_doctor_ieced' => $request['id_doctor_ieced'],
                'doctor_txt'      => $request['doctor_txt'],
                'observacion'     => $request['observacion'],
                'id_empresa'      => $request['id_empresa'],
                'cantidad'        => $cont,
                'estado'          => '-1',
                'valor'           => $total,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,

            ];
            $id_examen_orden = Examen_Orden::insertGetId($input_ex);
        }

        $detalles_cotizacion = Examen_detalle::where('id_examen_orden', $request->cotizacion)->get();
        foreach ($detalles_cotizacion as $value) {
            $value->delete();
        }

        $valor         = 0;
        $cont          = 0;
        $examen_nombre = "";
        $examenes      = Examen::all();
        foreach ($examenes as $examen) {
            if (!is_null($request['ch' . $examen->id])) {

                $valor = $examen->valor;
                $cubre = '';
                if ($request->id_seguro != '1') {
                    $cubre = 'NO';
                }
                $examen_nombre = $examen_nombre . "+" . $examen->nombre;
                if (!is_null($convenio)) {
                    $nivel    = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                        $cubre = 'SI';
                    }
                }

                $cont++;
                $input_det = [
                    'id_examen_orden' => $id_examen_orden,
                    'id_examen'       => $examen->id,
                    'valor'           => $valor,
                    'cubre'           => $cubre,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,

                ];

                Examen_detalle::create($input_det);
            }
        }

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "GENERA COTIZACION",
            'dato_ant1'   => $request['id'],
            'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
            'dato_ant4'   => $examen_nombre,
        ]);

        return "ok";
    }

    public function cotizador_recalcular(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        if ($this->validacion($request) != 'OK') {
            return $this->validacion($request);
        }

        $examen_deducible = Examen::where('nombre','DEDUCIBLE')->first();

        if( $request->deducible > 0 ){
            //CARGAR DEDUCIBLE
            $orden = Examen_Orden::find($request->cotizacion);
            
            if(!is_null($examen_deducible)){
                $det_dedu = $orden->detalles->where('id_examen',$examen_deducible->id)->first();
                if(is_null($det_dedu)){
                    $input_det = [
                        'id_examen_orden' => $orden->id,
                        'id_examen'       => $examen_deducible->id,
                        'valor'           => 0,
                        'valor_2'         => 0,
                        'cubre'           => 'NO',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'cobrar_pac_pct'  => '100',
                        'valor_con_oda'   => $request->deducible,
        
                    ];
        
                    Examen_detalle::create($input_det);
            
                }else{
                    //aqui recalculo el deducible para humana al 100% si se necesita para todos quitar el if
                    if($request->id_seguro == 4){
                        $det_dedu->cobrar_pac_pct   = '100';
                        $det_dedu->valor            = 0;
                        $det_dedu->valor_2          = 0;
                        $det_dedu->cubre            = 'NO';
                        $det_dedu->valor_con_oda    = $request->deducible;
                        $det_dedu->save();
                    }
                }
            }    
        }

        $orden = Examen_Orden::find($request->cotizacion);
        //$protocolo = Protocolo::find($orden->id_protocolo);
        $cambio_paciente = false;
        if ($orden->paciente->sexo != $request->sexo) {
            $cambio_paciente = true;
        }
        if (date('Y/m/d', strtotime($orden->paciente->fecha_nacimiento)) != $request->fecha_nacimiento) {
            $cambio_paciente = true;
        }
        if ($orden->paciente->id_seguro != $request->id_seguro) {
            //$cambio_paciente = true;
        }
        if ($cambio_paciente) {
            $input_log = [
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "ACTUALIZA NUEVO PACIENTE COTIZACION",
                'dato_ant1'   => $orden->id_paciente,
                'dato_ant4'   => 'sexo: ' . $orden->paciente->sexo . ' fecha nacimiento: ' . date($orden->paciente->fecha_nacimiento, 'Y/m/d') . ' seguro: ' . $orden->paciente->id_seguro,
                'dato4'       => 'sexo: ' . $request->sexo . ' fecha nacimiento: ' . $request->fecha_nacimiento . ' seguro: ' . $request->id_seguro,
            ];
            Log_usuario::create($input_log);
            $paciente = Paciente::find($orden->id_paciente);
            $pac      = [
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'sexo'             => $request['sexo'],
                'id_seguro'        => $request['id_seguro'],
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,
            ];
            $paciente->update($pac);
        }
        $cobrar_pac_pct = $request->cobrar_pac_pct;

        //RECALCULAR
        $total    = 0;
        $cantidad = 0;
        //$detalles = Examen_Detalle::where('id_examen_orden', $request->cotizacion)->get();
        $detalles = $orden->detalles;
        //return $detalles;
        $valor_covid = 0;
        /*$covid       = $orden->detalles->where('id_examen', '1191')->first();
        if (is_null($covid)) {
        $covid = $orden->detalles->where('id_examen', '1195')->first();
        if (is_null($covid)) {
        $covid = $orden->detalles->where('id_examen', '1196')->first();
        }
        }
        if (is_null($covid)) {
        $valor_covid = $covid->valor;
        }*/
        $descuento_p = $request->descuento_p;
        if ($descuento_p > 100) {
            $descuento_p = 100;
        }
        $pyf_details = array();
        foreach ($detalles as $detalle) {
            $cantidad = $cantidad + 1;
            $examen   = Examen::find($detalle->id_examen);
            $valor    = $examen->valor;
            $cubre    = 'NO';
            $ex_nivel = Examen_Nivel::where('id_examen', $detalle->id_examen)->where('nivel', $request->id_nivel)->first();
            if (!is_null($ex_nivel)) {
                if ($ex_nivel->valor1 != 0) {
                    $valor = $ex_nivel->valor1;
                    $cubre = 'SI';
                }
            }
            if($detalle->sin_cobertura == '1' and $cubre = 'SI' ){
                //$valor    = $examen->valor; no va pvp va el nivel         
            }
            //$txt_prb = $orden->total_valor;
            /*if (($orden->total_valor - $valor_covid) >= 80) {
            if ($examen->id == '1191' || $examen->id == '1195' || $examen->id == '1196') {
            $valor = 0;
            }
            }*/
            $valor_descuento = $descuento_p * $valor / 100;
            $valor_descuento = round($valor_descuento, 4);

            $valor_con_oda = 0;
            /*if ($cobrar_pac_pct < 100) {
                $valor_con_oda   = $cobrar_pac_pct * $valor / 100;
                $valor_con_oda   = round($valor_con_oda, 4);
                $valor_descuento = $descuento_p * $valor_con_oda / 100;
                $valor_descuento = round($valor_descuento, 4);
            }*/
            if($cubre == 'SI' and $detalle->sin_cobertura != '1' and $cobrar_pac_pct < 100){
                $valor_con_oda   = $cobrar_pac_pct * $valor / 100;
                $valor_con_oda   = round($valor_con_oda, 2, PHP_ROUND_HALF_DOWN);
                $valor_descuento = $descuento_p * $valor_con_oda / 100;
                $valor_descuento = round($valor_descuento, 2);
                $cobrar_pac_pct_det = $cobrar_pac_pct;
            }
            else{

                if($examen->nombre == 'DEDUCIBLE'){
                    if($request->id_seguro != 4){
                        $valor_con_oda = $request->deducible * (100 - $cobrar_pac_pct)/100;
                        $valor_con_oda = round($valor_con_oda, 2);  
                        $valor = $valor_con_oda; 
                        $valor_descuento = $descuento_p * $valor_con_oda / 100;
                        $valor_descuento = round($valor_descuento, 2);
                    }else{
                        $valor_con_oda = $request->deducible;  
                        $valor = 0; 
                        $valor_descuento = 0;
                    }                  

                }else{
                    $valor_con_oda   = $valor;
                    $valor_con_oda   = round($valor_con_oda, 2);
                    $valor_descuento = $descuento_p * $valor_con_oda / 100;
                    $valor_descuento = round($valor_descuento, 2);
                     

                }
                $cobrar_pac_pct_det = '100';
                
            }

            if($detalle->tiene_fee == '1'){
                $valor_con_oda ++; // FEE DE $1    
            }

            $input_det = [
                'valor'           => $valor,
                'cubre'           => $cubre,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'valor_descuento' => $valor_descuento,
                'p_descuento'     => $descuento_p,
                'cobrar_pac_pct'  => $cobrar_pac_pct_det,
                'valor_con_oda'   => $valor_con_oda,

            ];
            array_push($pyf_details, array(
                "valor"           => $valor,
                "nombre"          => $detalle->examen->nombre,
                "valor_descuento" => $valor_descuento,
                "p_descuento"     => $descuento_p,
            ));
            $detalle->update($input_det);
            //$total = $total + $valor;
        }
        $manage          = json_encode($pyf_details);
        $orden           = Examen_Orden::find($request->cotizacion);
        $total           = $orden->detalles->sum('valor');
        $cantidad        = $orden->detalles->count();
        $total           = round($total, 4);
        $descuento_total = $orden->detalles->sum('valor_descuento');
        //$valor_con_oda   = $orden->detalles->sum('valor_con_oda');
        $valor_con_oda = 0;
        $total_con_oda = 0;
        if ($cobrar_pac_pct < 100) {
            if($request->id_seguro != 4){
                //$valor_con_oda = $total * $cobrar_pac_pct / 100;
                $valor_con_oda = $orden->detalles->sum('valor_con_oda');
                $valor_con_oda = round($valor_con_oda, 2);
                $total_con_oda = $valor_con_oda - $descuento_total;
            }else{
                    //dd($detalles);
                $valor_con_oda = (($orden->detalles->sum('valor') - $request->deducible)* ($cobrar_pac_pct/100))+$request->deducible;
                $valor_con_oda = round($valor_con_oda, 2);
                $total_con_oda = $valor_con_oda - $descuento_total;
            }

        }
        /*$descuento_p = $request->descuento_p;
        if ($request->descuento_p == null) {
        $descuento_valor = 0;
        $descuento_p     = 0;
        }*/
        //return $request->all();

        /*if ($request->descuento_p > 100) {
        $descuento_valor = $total;
        } else {
        $descuento_valor = $request->descuento_p * $total / 100;
        $descuento_valor = round($descuento_valor, 2);
        }*/
        //$subtotal_pagar = $total - $descuento_valor;
        $subtotal_pagar = $total - $descuento_total;
        $forma_pago     = DB::table('forma_de_pago')->where('id', $request->id_forma_pago)->first();
        $recargo_p      = 0;
        if (!is_null($forma_pago)) {
            $recargo_p     = $forma_pago->recargo_p;
            $recargo_valor = $subtotal_pagar * $recargo_p / 100;
            $recargo_valor = round($recargo_valor, 4);
        } else {
            $recargo_valor = $orden->detalle_forma_pago->sum('p_fi');
        }

        $valor_total = $subtotal_pagar + $recargo_valor;
        $valor_total = round($valor_total, 4);
        //ACTUALIZAR ORDEN
        $input_ex = [
            'motivo_descuento' => $request->motivo_descuento,
            'id_forma_de_pago' => $request->id_forma_pago,
            'descuento_p'      => $descuento_p,
            'descuento_valor'  => $descuento_total,
            'recargo_p'        => $recargo_p,
            'recargo_valor'    => $recargo_valor,
            'total_valor'      => $valor_total,
            'id_seguro'        => $request['id_seguro'],
            'id_nivel'         => $request->id_nivel,
            'est_amb_hos'      => $request['est_amb_hos'],
            'id_doctor_ieced'  => $request['id_doctor_ieced'],
            'id_empresa'       => $request['id_empresa'],
            'cantidad'         => $cantidad,
            'valor'            => $total,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariomod'    => $idusuario,
            'cobrar_pac_pct'   => $cobrar_pac_pct,
            'valor_con_oda'    => $valor_con_oda,
            'total_con_oda'    => $total_con_oda,
            'deducible'        => $request->deducible, 
        ];

        $orden->update($input_ex);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $orden->id,
            'dato1'       => $orden->id_paciente,
            'dato_ant2'   => "RECALCULAR COTIZACION",
            'dato_ant4'   => $manage,
        ]);

        return "ok";
    }

    public function cotizador_cabecera(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if ($this->validacion($request) != 'OK') {
            return $this->validacion($request);
        }

        $orden = Examen_Orden::find($request->cotizacion);

        $cambio_paciente = false;
        if ($orden->paciente->sexo != $request->sexo) {
            $cambio_paciente = true;
        }
        if ($orden->paciente->fecha_nacimiento != $request->fecha_nacimiento) {
            $cambio_paciente = true;
        }
        $cambio_orden = false;
        if ($orden->id_doctor_ieced != $request->id_doctor_ieced) {
            $cambio_orden = true;
        }
        if ($orden->codigo != $request->codigo) {
            $cambio_orden = true;
        }
        if ($orden->numero_oda != $request->numero_oda) {
            $cambio_orden = true;
        }
        if ($orden->codigo != $request->codigo) {
            $cambio_orden = true;
        }
        if ($orden->est_amb_hos != $request->est_amb_hos) {
            $cambio_orden = true;
        }
        if ($orden->pres_dom != $request->pres_dom) {
            $cambio_orden = true;
        }
        if ($orden->motivo_descuento != $request->motivo_descuento) {
            $cambio_orden = true;
        }

        if ($orden->asesor_venta != $request->idasesor) {
            $cambio_orden = true;
        }
        if ($cambio_paciente) {

            $input_log = [
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "ACTUALIZA NUEVO PACIENTE COTIZACION",
                'dato_ant1'   => $orden->id_paciente,
                'dato_ant4'   => 'sexo: ' . $orden->paciente->sexo . ' fecha nacimiento: ' . $orden->paciente->fecha_nacimiento . ' seguro: ' . $orden->paciente->id_seguro,
                'dato4'       => 'sexo: ' . $request->sexo . ' fecha nacimiento: ' . $request->fecha_nacimiento . ' seguro: ' . $request->id_seguro,

            ];

            Log_usuario::create($input_log);

            $paciente = Paciente::find($orden->id_paciente);
            $pac      = [
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'sexo'             => $request['sexo'],
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,
            ];
            $paciente->update($pac);
        }

        if ($cambio_orden) {

            $input_ex = [

                'codigo'           => $request['codigo'],
                'id_doctor_ieced'  => $request['id_doctor_ieced'],
                'est_amb_hos'      => $request['est_amb_hos'],
                'pres_dom'         => $request['pres_dom'],
                'motivo_descuento' => $request->motivo_descuento,
                'numero_oda'       => $request['numero_oda'],
                'asesor_venta'      => $request['idasesor'],

            ];

            $orden->update($input_ex);
        }

        return "ok";
    }

    public function cotizador_update($cotizacion, $id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $orden = Examen_Orden::find($cotizacion);

        //$detalle = Examen_Detalle::where('id_examen_orden', $cotizacion)->where('id_examen', $id)->first();
        $detalle = $orden->detalles->where('id_examen', $id)->first();
        //return $detalle;
        $tiene_covid     = 0;
        $valor_covid     = 0;
        $tiene_domicilio = 0;
        if (is_null($detalle)) {

            $examen   = Examen::find($id);
            $valor    = $examen->valor;
            $cubre    = 'NO';
            $valor_2  = null;
            $ex_nivel = Examen_Nivel::where('id_examen', $id)->where('nivel', $orden->id_nivel)->first();
            if (!is_null($ex_nivel)) {
                if ($ex_nivel->valor1 != 0) {

                    $valor = $ex_nivel->valor1;
                    $cubre = 'SI';
                }
                if ($ex_nivel->valor2 != null) {
                    $valor_2 = $ex_nivel->valor2;
                }
            }

            /*if ($orden->total_valor >= 80) {
            if ($examen->id == '1191' || $examen->id == '1195' || $examen->id == '1196') {
            $valor = 0;
            }
            }*/
            $cobrar_pac_pct = $orden->cobrar_pac_pct;
            $valor_con_oda  = 0;
            if ($cobrar_pac_pct < 100) {
                $valor_con_oda = $cobrar_pac_pct * $valor / 100;
                $valor_con_oda = round($valor_con_oda, 4);
                //$valor_descuento = $descuento_p * $valor_con_oda / 100;
                //$valor_descuento = round($valor_descuento, 2);
            }

            $input_det = [
                'id_examen_orden' => $orden->id,
                'id_examen'       => $id,
                'valor'           => $valor,
                'valor_2'         => $valor_2,
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
            $orden = Examen_Orden::find($cotizacion);
            //$total       = $orden->valor + $valor;
            $total       = $orden->detalles->sum('valor');
            $cantidad    = $orden->detalles->count();
            $total       = round($total, 4);
            $descuento_p = $orden->descuento_p;
            if ($orden->descuento_p == null) {
                //$descuento_valor = 0;
                $descuento_p = 0;
            }

            if ($orden->descuento_p > 100) {
                //$descuento_valor = $total;
                $descuento_p = 100;
            } else {
                /* $descuento_valor = $orden->descuento_p * $total / 100;
            $descuento_valor = round($descuento_valor, 2);*/
            }
            $descuento_valor = $orden->detalles->sum('valor_descuento');

            $subtotal_pagar = $total - $descuento_valor;

            $recargo_p = $orden->recargo_p;

            $recargo_valor = $subtotal_pagar * $recargo_p / 100;
            $recargo_valor = round($recargo_valor, 4);
            $valor_total   = $subtotal_pagar + $recargo_valor;
            $valor_total   = round($valor_total, 4);
            //$valor_con_oda = $orden->detalles->sum('valor_con_oda');
            $valor_con_oda = 0;
            $total_con_oda = 0;
            if ($cobrar_pac_pct < 100) {
                //$valor_con_oda = $total * $cobrar_pac_pct / 100;
                $valor_con_oda = $orden->detalles->sum('valor_con_oda');
                $valor_con_oda = round($valor_con_oda, 4);
                $total_con_oda = $valor_con_oda - $descuento_valor;
            }

            //$total_con_oda = $valor_con_oda + $recargo_valor;

            //ACTUALIZAR ORDEN
            $input_ex = [

                'descuento_valor' => $descuento_valor,
                'recargo_valor'   => $recargo_valor,
                'total_valor'     => $valor_total,
                //'cantidad'        => $orden->cantidad + 1,
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
                'descripcion' => "ACTUALIZA COTIZACION",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant4'   => $id,
            ]);
            ///

            $dom = $orden->detalles->where('id_examen', '1203')->first();
            if (!is_null($dom)) {
                $tiene_domicilio = 1;
            }

            $valor_covid = 0;
            $covid       = $orden->detalles->where('id_examen', '1191')->first();
            if (is_null($covid)) {
                $covid = $orden->detalles->where('id_examen', '1195')->first();
                if (is_null($covid)) {
                    $covid = $orden->detalles->where('id_examen', '1196')->first();
                }
            }
            if (!is_null($covid)) {
                $tiene_covid = 1;
                $valor_covid = $covid->valor;
            }
        }

        $ovalor       = $orden->valor;
        $ototal_valor = $orden->total_valor;
        if ($orden->cobrar_pac_pct < 100) {
            $ovalor       = $orden->valor_con_oda;
            $ototal_valor = $orden->total_con_oda;
        }

        $res_motivo = '';
        $res_porcentaje = '';
        $resultado_mem = $this->recalcular_membresia($orden->id);
        if ($resultado_mem['estado'] == 'OK') {
            $res_motivo     = $resultado_mem['motivo'];
            $res_porcentaje = $resultado_mem['descuento_pct'];
        }
        $orden = Examen_Orden::find($cotizacion);
        return ['cantidad' => $orden->cantidad, 'valor' => $orden->valor, 'descuento_valor' => $orden->descuento_valor, 'recargo_valor' => $orden->recargo_valor, 'total_valor' => $orden->total_valor, 'tiene_domicilio' => $tiene_domicilio, 'tiene_covid' => $tiene_covid, 'valor_covid' => $valor_covid, 'motivo' => $res_motivo, 'descuento_p' => $res_porcentaje];
    }

    public function cotizador_delete($cotizacion, $id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $orden = Examen_Orden::find($cotizacion);
        //$detalle = Examen_Detalle::where('id_examen_orden', $cotizacion)->where('id_examen', $id)->first();
        $detalle = $orden->detalles->where('id_examen', $id)->first();
        if (!is_null($detalle)) {

            $detalle->delete();

            $orden = Examen_Orden::find($cotizacion);

            /// orden

            //$total = $orden->valor - $detalle->valor;
            $total    = $orden->detalles->sum('valor');
            $cantidad = $orden->detalles->count();
            $total    = round($total, 4);
            if ($total < 0) {

                $total = 0;
            }
            $descuento_p = $orden->descuento_p;
            if ($orden->descuento_p == null) {
                $descuento_valor = 0;
                $descuento_p     = 0;
            }

            if ($orden->descuento_p > 100) {
                $descuento_valor = $total;
            } else {
                $descuento_valor = $orden->descuento_p * $total / 100;
                $descuento_valor = round($descuento_valor, 4);
            }

            $subtotal_pagar = $total - $descuento_valor;

            $recargo_p = $orden->recargo_p;

            $recargo_valor = $subtotal_pagar * $recargo_p / 100;
            $recargo_valor = round($recargo_valor, 4);
            $valor_total   = $subtotal_pagar + $recargo_valor;
            $valor_total   = round($valor_total, 4);

            $cobrar_pac_pct = $orden->cobrar_pac_pct;

            $valor_con_oda = 0;
            $total_con_oda = 0;
            if ($cobrar_pac_pct < 100) {
                //$valor_con_oda = $total * $cobrar_pac_pct / 100;
                $valor_con_oda = $orden->detalles->sum('valor_con_oda');
                $valor_con_oda = round($valor_con_oda, 4);
                $total_con_oda = $valor_con_oda - $descuento_valor;
            }

            //ACTUALIZAR ORDEN
            $input_ex = [

                'descuento_valor' => $descuento_valor,
                'recargo_valor'   => $recargo_valor,
                'total_valor'     => $valor_total,
                //'cantidad'        => $orden->cantidad - 1,
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
                'descripcion' => "ACTUALIZA COTIZACION",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant4'   => $id,
            ]);
            ///

            $tiene_domicilio = 0;
            $dom             = $orden->detalles->where('id_examen', '1203')->first();
            if (!is_null($dom)) {
                $tiene_domicilio = 1;
            }

            $tiene_covid = 0;
            $valor_covid = 0;
            $covid       = $orden->detalles->where('id_examen', '1191')->first();
            if (is_null($covid)) {
                $covid = $orden->detalles->where('id_examen', '1195')->first();
                if (is_null($covid)) {
                    $covid = $orden->detalles->where('id_examen', '1196')->first();
                }
            }
            if (!is_null($covid)) {
                $tiene_covid = 1;
                $valor_covid = $covid->valor;
            }
        }

        $pvalor       = $orden->valor;
        $ptotal_valor = $orden->total_valor;
        if ($cobrar_pac_pct < 100) {
            $pvalor       = $valor_con_oda;
            $ptotal_valor = $total_con_oda;
        }

        $res_motivo = '';
        $res_porcentaje = '';
        $resultado_mem = $this->recalcular_membresia($orden->id);
        if ($resultado_mem['estado'] == 'OK') {
            $res_motivo     = $resultado_mem['motivo'];
            $res_porcentaje = $resultado_mem['descuento_pct'];
        }
        $orden = Examen_Orden::find($cotizacion);
        //dd($ptotal_valor);
        return ['cantidad' => $orden->cantidad, 'valor' => $pvalor, 'descuento_valor' => $orden->descuento_valor, 'recargo_valor' => $orden->recargo_valor, 'total_valor' => $ptotal_valor, 'tiene_domicilio' => $tiene_domicilio, 'tiene_covid' => $tiene_covid, 'valor_covid' => $valor_covid, 'motivo' => $res_motivo, 'descuento_p' => $res_porcentaje];
    }

    public function crear_cabecera(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $reglas = [
            'id'               => 'required',
            'nombre1'          => 'required',
            'nombre2'          => 'required',
            'apellido1'        => 'required',
            'apellido2'        => 'required',
            'sexo'             => 'required',
            'fecha_nacimiento' => 'required',
            'id_doctor_ieced'  => 'required',
            'id_seguro'        => 'required',
        ];
        $mensaje = [
            'id.required'               => 'Ingrese la cédula del paciente',
            'nombre1.required'          => 'Ingrese el nombre',
            'apellido1.required'        => 'Ingrese el apellido',
            'sexo.required'             => 'Seleccione el sexo',
            'fecha_nacimiento.required' => 'Ingrese la fecha de nacimiento',
            'id_doctor_ieced.required'  => 'Seleccione el Doctor',
            'id_seguro.required'        => 'Seleccione el seguro',
        ];
        $this->validate($request, $reglas, $mensaje);
        if ($request->id_seguro == '41') {
            $reglas2 = [
                'ticket' => 'required|unique:examen_orden',
            ];
            $mensaje2 = [
                'ticket.required' => 'Ticket es requerido',
                'ticket.unique'   => 'Ticket ya utilizado',
            ];
            $this->validate($request, $reglas2, $mensaje2);
        }

        if ($request->id_nivel == null) {
            $convenio = Convenio::where('id_seguro', $request->id_seguro)->where('id_empresa', $request->id_empresa)->first();
        } else {
            $convenio = Convenio::where('id_seguro', $request->id_seguro)->where('id_empresa', $request->id_empresa)->where('id_nivel', $request->id_nivel)->first();
        }

        $seguro = Seguro::find($request->id_seguro);

        $flag_repetido = false;
        $mail          = $request['id'] . '@lmail.com';
        if ($request['email'] != null) {
            $usuario = $this->recupera_mail($request['email']);
            if ($usuario != 'no') {
                if ($usuario->id != $request->id) {
                    $flag_repetido = true;
                }
            } else {
                //no creo el grupo return $usuario
                $mail = $request['email'];
            }
        }

        $rules   = ['email' => 'email'];
        $mensaje = ['email.email' => 'Error en formato del correo'];
        $this->validate($request, $rules, $mensaje);

        //CREAR USUARIO
        $input_usu_c = [
            'id'               => $request['id'],
            'nombre1'          => strtoupper($request['nombre1']),
            'nombre2'          => strtoupper($request['nombre2']),
            'apellido1'        => strtoupper($request['apellido1']),
            'apellido2'        => strtoupper($request['apellido2']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'telefono1'        => $request['telefono1'],
            'direccion'        => $request['direccion'],
            'telefono2'        => '1',
            'id_tipo_usuario'  => 2,
            'email'            => $mail,
            'password'         => bcrypt($request['id']),
            'tipo_documento'   => 1,
            'estado'           => 1,
            'imagen_url'       => ' ',
            'ip_creacion'      => $ip_cliente,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariocrea'   => $idusuario,
            'id_usuariomod'    => $idusuario,
        ];

        $user    = User::find($request['id']);
        $origen2 = '';
        $otro    = '';
        if ($request['origen'] == "MEDIO IMPRESO") {
            $origen2 = $request['origen_impreso'];
            $otro    = $request['impreso_otros'];
        } else if ($request['origen'] == "MEDIO DIGITAL") {
            $origen2 = $request['origen_digital'];
            $otro    = $request['digital_otros'];
        }

        $input_pac = [

            'id'                 => $request['id'],
            'id_usuario'         => $request['id'],
            'nombre1'            => strtoupper($request['nombre1']),
            'nombre2'            => strtoupper($request['nombre2']),
            'apellido1'          => strtoupper($request['apellido1']),
            'apellido2'          => strtoupper($request['apellido2']),
            'fecha_nacimiento'   => $request['fecha_nacimiento'],
            'sexo'               => $request['sexo'],
            'telefono1'          => $request['telefono1'],
            'telefono2'          => '1',
            'nombre1familiar'    => strtoupper($request['nombre1']),
            'nombre2familiar'    => strtoupper($request['nombre2']),
            'apellido1familiar'  => strtoupper($request['apellido1']),
            'apellido2familiar'  => strtoupper($request['apellido2']),
            'parentesco'         => 'Principal',
            'parentescofamiliar' => 'Principal',
            'tipo_documento'     => 1,
            'id_seguro'          => 1,
            'imagen_url'         => ' ',
            'menoredad'          => 0,
            'origen'             => $request['origen'],
            'origen2'            => $origen2,
            'otro'               => $otro,
            'direccion'          => $request['direccion'],
            'referido'           => $request['referido'],
            'ip_creacion'        => $ip_cliente,
            'ip_modificacion'    => $ip_cliente,
            'id_usuariocrea'     => $idusuario,
            'id_usuariomod'      => $idusuario,

        ];

        $paciente = Paciente::find($request['id']);

        if (is_null($paciente)) {

            if (!is_null($user)) {
                //$user->update($input_usu_a);
            } else {
                User::create($input_usu_c);
            }

            paciente::create($input_pac);

            if ($flag_repetido) {
                $gr_fam = Labs_Grupo_Familiar::find($request['id']);
                if (is_null($gr_fam)) {
                    // crear grupo familiar return $usuario;
                    $arr_gr = [
                        'id'              => $request['id'],
                        'id_usuario'      => $usuario->id,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ];
                    Labs_Grupo_Familiar::create($arr_gr);
                }
            }

            $input_log = [
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['id'],
                'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                'dato_ant4'   => "COTIZACION",
                'dato2'       => 'COTIZACION',
            ];

            Log_usuario::create($input_log);
        } else {
            if ($paciente->fecha_nacimiento == null || $paciente->sexo == null) {
                $pac = [
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'sexo'             => $request['sexo'],
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                ];
                $paciente->update($pac);
            }
        }

        $nivel = null;
        $valor = 0;
        $cont  = 0;
        $total = 0;

        $input_ex = [
            'id_paciente'      => $request['id'],
            'anio'             => substr(date('Y-m-d'), 0, 4),
            'mes'              => substr(date('Y-m-d'), 5, 2),
            'id_protocolo'     => $request['id_protocolo'],
            'id_seguro'        => $request['id_seguro'],
            'id_nivel'         => $request['id_nivel'],
            //'id_forma_de_pago' => $request['id_forma_pago'],
            'est_amb_hos'      => $request['est_amb_hos'],
            'id_doctor_ieced'  => $request['id_doctor_ieced'],
            'doctor_txt'       => $request['doctor_txt'],
            'observacion'      => $request['observacion'],
            'id_empresa'       => $request['id_empresa'],
            'pres_dom'         => $request['pres_dom'],
            'cantidad'         => $cont,
            'estado'           => '-1',
            'valor'            => $total,
            'ticket'           => $request['ticket'],
            'ip_creacion'      => $ip_cliente,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariocrea'   => $idusuario,
            'id_usuariomod'    => $idusuario,
            'motivo_descuento' => $request['motivo_descuento'],
            'fecha_orden'      => date('Y-m-d H:i:s'),

        ];
        $id_examen_orden = Examen_Orden::insertGetId($input_ex);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "GENERA COTIZACION",
            'dato_ant1'   => $request['id'],
            'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),

        ]);

        return $id_examen_orden;
    }

    public function cotizador_editar($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $orden = Examen_Orden::find($id);
        $asesor = null;
        if (!is_null($orden->asesor_venta)) {
            $asesor = User::find($orden->asesor_venta);
        }

        //$usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->where('training', '<>', '1')->orderBy('nombre1')->get();
        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->orderBy('apellido1')->where('uso_laboratorio', '1')->get();

        $formas = DB::table('forma_de_pago')->where('estado', '1')->get();
        //dd($formas);

        $examenes = Examen::orderBy('id_agrupador')->get();
        //dd($examenes);
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $seguros1    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.estado_labs', '1')->where('s.tipo', '>', '1')->orderBy('s.nombre');
        $seguros2    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.estado_labs', '1')->where('s.tipo', '>', '0')->join('convenio as c', 'c.id_seguro', 's.id')->select('s.*')->orderBy('s.nombre');
        $seguros     = $seguros1->union($seguros2)->get();
        $protocolos  = Protocolo::where('estado', '1')->get();
        $protocolos2 = Protocolo::where('estado', '3')->get();
        $codigo      = Labs_doc_externos::where('estado', '1')->get();

        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();

        /*return view('laboratorio/orden/create_particular',['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas]);*/
        //dd($orden);
        return view('laboratorio/orden/editar_cotizacion', ['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'empresas' => $empresas, 'orden' => $orden, 'formas' => $formas, 'protocolos' => $protocolos, 'codigo' => $codigo, 'protocolos2' => $protocolos2, 'asesor' => $asesor]);
    }

    public function cotizador_imprimir($id)
    {

        $orden      = Examen_Orden::find($id);
        $detalles   = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen_agrupador_sabana as es', 'es.id_examen', 'ed.id_examen')->select('ed.*', 'e.descripcion', 'e.nombre')->join('examen as e', 'e.id', 'ed.id_examen')->orderBy('es.id_examen_agrupador_labs')->get();
        $forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden', $id)->get();
        //dd($detalles);
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;

        $membresia_controller = new MembresiasLabsController;
        $res = $membresia_controller->buscar_membresia($orden->id_paciente);
        $view = \View::make('laboratorio.orden.cotizacion_pdf', compact('orden', 'detalles', 'age', 'forma_pago', 'res'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        $pdf->setOptions(['isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('cotizador-' . $id . '.pdf');
    }
    public function tiempos_imprimir($id)
    {

        // ->whereNotNull('hc_evo.cuadro_clinico')

        $orden    = Examen_Orden::find($id);
        $detalles = DB::table('examen_detalle as ed')
            ->where('ed.id_examen_orden', $orden->id)
            ->join('examen_agrupador_sabana as es', 'es.id_examen', 'ed.id_examen')
            ->select('ed.*', 'e.descripcion', 'e.nombre', 'e.tiempos', 'e.id')
            ->join('examen as e', 'e.id', 'ed.id_examen')
            ->whereNotNull('e.tiempos')
            ->orderBy('es.id_examen_agrupador_labs')->get();

        //dd($detalles);
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;

        $view = \View::make('laboratorio.orden.tiempos_pdf', compact('orden', 'detalles', 'age'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('tiempos-' . $id . '.pdf');
    }
    public function cotizador_imprimir_sistema($id)
    {

        $orden    = Examen_Orden::find($id);
        $detalles = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen_agrupador_sabana as es', 'es.id_examen', 'ed.id_examen')->select('ed.*', 'e.descripcion', 'e.nombre')->join('examen as e', 'e.id', 'ed.id_examen')->orderBy('es.id_examen_agrupador_labs')->get();
        //dd($detalles);
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;

        $view = \View::make('laboratorio.orden.cotizacion_especial_pdf', compact('orden', 'detalles', 'age'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('cotizador-' . $id . '.pdf');
    }

    public function cotizador_orden_imprimir($id)
    {


        $orden = Examen_Orden::find($id);

        /*$detalles = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen_agrupador_sabana as es','es.id_examen','ed.id_examen')->select('ed.*','e.descripcion')->join('examen as e','e.id','ed.id_examen')->orderBy('es.id_examen_agrupador_labs')->get();*/

        $detalles = DB::table('examen_detalle as ed')->where('id_examen_orden', $id)->join('examen as e', 'e.id', 'ed.id_examen')->join('examen_agrupador_sabana as sa', 'sa.id_examen', 'ed.id_examen')->join('examen_agrupador_labs as l', 'l.id', 'sa.id_examen_agrupador_labs')->select('sa.*', 'e.descripcion', 'e.nombre', 'e.valor', 'e.id as ex_id', 'l.nombre as lnombre')->orderBy('sa.id_examen_agrupador_labs')->orderBy('sa.nro_orden')->get();

        $evoluciones = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $orden->id_paciente)
            ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
            ->where('hc_evo.secuencia', 0)
            //->whereNotNull('hc_evo.motivo')
            ->orderby('hc_evo.updated_at', 'desc')
            ->select('hc_evo.*')
            ->first();

        $diagnosticos = null;
        if (!is_null($evoluciones)) {

            $diagnosticos = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $evoluciones->hc_id_procedimiento)->groupBy('cie10')->get();
        }

        $descripcion = '';
        $texto       = '';
        $mas         = false;

        if (!is_null($diagnosticos)) {

            $mas = true;
            foreach ($diagnosticos as $value) {

                $c3 = Cie_10_3::find($value->cie10);

                if (!is_null($c3)) {
                    $descripcion = $c3->descripcion;
                }

                $c4 = Cie_10_4::find($value->cie10);

                if (!is_null($c4)) {
                    $descripcion = $c4->descripcion;
                }

                if ($mas == true) {
                    $texto = '<span style="font-size: 17px;padding: 0;">' . $value->cie10 . ':' . $descripcion . '</span>';
                    $mas   = false;
                } else {

                    $texto = $texto . '<br>' . '<span style="font-size: 17px;padding: 0;">' . $value->cie10 . ':' . $descripcion . '</span>';
                }
            }
        }

        if ($orden->doctor_firma != null) {
            $dr = $orden->doctor_firma;
        } else {
            $dr = $orden->id_doctor_ieced;
        }

        $firma = Firma_Usuario::where('id_usuario', $dr)->first();

        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;

        $view = \View::make('laboratorio.orden.cotizacion_orden_pdf', compact('orden', 'detalles', 'age', 'texto', 'firma'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('cotizador-' . $id . '.pdf');
    }

    public function cotizador_imprimir_gastro($id)
    {

        $orden    = Examen_Orden::find($id);
        $detalles = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen_agrupador_sabana as es', 'es.id_examen', 'ed.id_examen')->select('ed.*', 'e.descripcion')->join('examen as e', 'e.id', 'ed.id_examen')->orderBy('es.id_examen_agrupador_labs')->get();
        //dd($detalles);
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;

        $view = \View::make('laboratorio.orden.cotizacion_pdf_gas', compact('orden', 'detalles', 'age'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        //$pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('cotizador-' . $id . '.pdf');
    }

    public function cotizador_generar($id)
    {
        $empresa = Empresa::where('prioridad_labs', 1)->first();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $orden = Examen_Orden::find($id);
        //$detalles = Examen_Detalle::where('id_examen_orden', $id)->get();
        $detalles = $orden->detalles;
        $membresia_controller = new MembresiasLabsController;
        $res = $membresia_controller->buscar_membresia($orden->id_paciente);

        if ($detalles->count() == '0') {

            return redirect()->route('cotizador.editar', ['id' => $id])->withInput(['mensaje' => 'Cotización sin exámenes']);
        }

        ///////////
        foreach ($detalles as $detalle) {
            $id_examen = $detalle->id_examen;
            $agrupador = Examen_Agrupador_Sabana::where('examen_agrupador_sabana.id_examen', $id_examen)->join('examen_agrupador_labs as eal', 'eal.id', 'examen_agrupador_sabana.id_examen_agrupador_labs')->select('examen_agrupador_sabana.*', 'eal.nombre')->first();
            if ($agrupador->nombre == 'MEMBRESIAS') {
                //$membresia = Membresia::where('empresa_id',$empresa->id)->where('nombre',$detalle->examen->nombre)->where('estado','1')->first();
                if ($detalle->examen->nombre == 'PREMIUM') {
                    if ($res['estado'] != 'ok') {
                        if ($orden->paciente->id != $orden->paciente->id_usuario) {
                            return redirect()->route('cotizador.editar', ['id' => $id])->withInput(['mensaje' => 'Paciente no es Principal, para crear la membresia']);
                        }
                        $arr_mem = [
                            'user_id'           => $orden->paciente->id,
                            'membresia_id'      => '2',
                            'fecha_compra'      => date('Y-m-d'),
                            'meses'             => '12',
                            'valor_pagado'      => $detalle->examen->valor,
                            'meses_contratados' => '12',
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'estado'            => 1,
                            'referido'          => $orden->asesor_venta,
                            'id_orden'          => $orden->id,
                        ];
                        $membresia_controller->crear_membresia_labs($arr_mem);
                        /*UserMembresia::create([
                            'user_id'           => $orden->paciente->id,
                            'membresia_id'      => $membresia->id,
                            'fecha_compra'      => date('Y-m-d H:i:s'),
                            'meses'             => '12',
                            'valor_pagado'      => $detalle->examen->valor,
                            'meses_contratados' => '12',
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'estado'            => 1,
                        ]);*/
                        $input_ex_2 = [
                            'motivo_descuento' => 'COMPRA DE MEMBRESIA',

                        ];
                        $orden->update($input_ex_2);
                        break;
                    } else {
                        return redirect()->route('cotizador.editar', ['id' => $id])->withInput(['mensaje' => 'Ya se encuentra activa la membresia']);
                    }
                }
            }
        }
        ///////////
        //ACTUALIZAR ORDEN
        $input_ex = [
            'estado'          => '1',
            'estado_pago'     => '1',
            'fecha_orden'     => date('Y-m-d H:i:s'),
            'realizado'       => '1',
            'anio'            => substr(date('Y-m-d'), 0, 4),
            'mes'             => substr(date('Y-m-d'), 5, 2),
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,

        ];
        $orden->update($input_ex);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "GENERAR ORDEN DE LABORATORIO",
            'dato_ant1'   => $orden->id,
            'dato1'       => $orden->id_paciente,

        ]);

        $saldo      = CierreCaja::whereDate('fecha', date('Y-m-d'))->latest()->first();
        if (!is_null($saldo)) {
            $total = $saldo->saldo + $orden->total_valor;
        } else {
            $total = 0;
        }

        $othervalid = CierreCaja::where('id_orden', $orden->id)->first();
        if (is_null($othervalid)) {
            $idcierre = CierreCaja::insertGetid([
                'fecha'           => date('Y-m-d H:m:s'),
                'tipo'            => '1',
                'id_paciente'     => $orden->id_paciente,
                'id_seguro'       => $orden->id_seguro,
                'descripcion'     => 'La Orden : ' . $orden->id . ' del paciente: ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1,
                'valor'           => $orden->total_valor,
                'saldo'           => $total,
                'id_orden'        => $orden->id,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
        }
        if ($res['estado'] == 'ok') {
            if ($orden->total_valor > 50) {
                $puntos = intdiv($orden->total_valor, 25);
                if ($puntos > 0) {
                    $puntos = $res['puntos'] + $puntos;
                    $data['id'] = $res['id_user_mem'];
                    $data['puntos'] = $puntos;
                    $data['motivo'] = 'EMPRESA: ' . $empresa->id . '-' . $empresa->establecimiento . '-' . $empresa->punto_emision . '+ ORDEN:' . $orden->id;
                    $res = $membresia_controller->actualizar_puntos($data);
                }
            }
        }

        NuevoResultadoController::crear_resultados($orden->id);

        return redirect()->route('orden.index');
    }

    public function subresultado_crear($id_orden, $id_examen)
    {
        $examen = Examen::find($id_examen);
        $orden  = Examen_Orden::find($id_orden);

        return view('laboratorio.orden.modal_sub', ['examen' => $examen, 'orden' => $orden]);
    }

    public function subresultado_store(Request $request)
    {
        //return $request;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $valor1    = $request['valor1'];
        $valor2    = $request['valor2'];
        $valor3    = $request['valor3'];
        $id_orden  = $request['id_orden'];
        $id_examen = $request['id_examen'];

        $rules = [
            'valor1' => 'required',
            'valor2' => 'required',
            'valor3' => 'required',
        ];
        $msg = [
            'valor1.required' => 'Ingrese Valor',
            'valor2.required' => 'Ingrese Valor',
            'valor3.required' => 'Ingrese Valor',
        ];
        $this->validate($request, $rules, $msg);

        $input = [
            'id_orden'        => $id_orden,
            'id_examen'       => $id_examen,
            'campo1'          => $valor1,
            'campo2'          => $valor2,
            'campo3'          => $valor3,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ];

        Examen_Sub_Resultado::create($input);

        $sub_resultados = Examen_Sub_Resultado::where('id_orden', $id_orden)->where('id_examen', $id_examen)->where('estado', '1')->get();

        return view('laboratorio.orden.sub_lis', ['sub_resultados' => $sub_resultados]);
    }

    public function subresultado_eliminar($id)
    {
        //return $id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $subresultado = Examen_Sub_Resultado::find($id);

        if (!is_null($subresultado)) {
            $input = [
                'estado'          => '0',
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,

            ];
            $subresultado->update($input);
        }

        return $id;
    }

    public function index_privado() //ORDENES DE LABORATORIO PARA CONVENIOS PRIVADOS

    {
        $opcion = '1'; //ORDENES DE LABORATORIO PARA CONVENIOS PRIVADOS

        if ($this->rol_new($opcion)) {
            return response()->view('errors.404');
        }

        $fecha = date('Y/m/d');

        $ordenes = $this->recupera_ordenes()->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha . ' 23:59'])->where('eo.estado', '1')->where('s.tipo', '1')->where('eo.realizado', '1')->paginate(30);
        //dd($ordenes);

        $seguros = Seguro::where('inactivo', '1')->where('tipo', '1')->get();

        return view('laboratorio/privado/index', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha, 'nombres' => null, 'seguro' => null, 'seguros' => $seguros]);
    }

    public function search_privado(Request $request)
    {
        //ORDENES DE LABORATORIO PARA CONVENIOS PRIVADOS

        $opcion = '1'; //ORDENES DE LABORATORIO PARA CONVENIOS PRIVADOS

        if ($this->rol_new($opcion)) {
            return response()->view('errors.404');
        }

        $seguro      = $request['seguro'];
        $nombres     = $request['nombres'];
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];

        $ordenes = $this->recupera_ordenes();

        if ($fecha != null) {

            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($seguro != null) {

            $ordenes = $ordenes->where('eo.id_seguro', $seguro);
        }

        if ($nombres != null) {

            $nombres2    = explode(" ", $nombres);
            $nombres_sql = '';
            $cantidad    = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {

                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }

        //dd($ordenes->get());

        $ordenes = $ordenes->where('eo.estado', '1')->where('s.tipo', '1')->where('eo.realizado', '1')->paginate(30);

        $seguros = Seguro::where('inactivo', '1')->where('tipo', '1')->get();

        return view('laboratorio/privado/index', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'nombres' => $nombres, 'seguro' => $seguro, 'seguros' => $seguros]);
    }

    public function ordenes_rpt(Request $request)
    {

        //dd("hola");
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $nombres     = $request['nombres'];
        $seguro      = $request['seguro'];

        $ordenes = DB::table('examen_orden as eo')->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->join('paciente as p', 'p.id', 'eo.id_paciente')->join('seguros as s', 's.id', 'eo.id_seguro')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->leftjoin('forma_de_pago as fp', 'fp.id', 'eo.id_forma_de_pago')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 's.nombre as snombre', 'fp.nombre as fpnombre')->where('eo.realizado', '1')->where('eo.estado', '1')->where('s.tipo', '1');

        if ($nombres != null) {

            $nombres2    = explode(" ", $nombres);
            $nombres_sql = '';
            $cantidad    = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {

                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {

                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }
        if ($seguro != null) {

            $ordenes = $ordenes->where('eo.id_seguro', $seguro);
        }

        $ordenes = $ordenes->get();

        $i = 0;

        $fecha_d = date('Y/m/d');

        Excel::create('Examenes-' . $fecha_d, function ($excel) use ($ordenes) {

            $excel->sheet('Examenes', function ($sheet) use ($ordenes) {
                $fecha_d = date('Y/m/d');
                $i       = 5;

                $sheet->mergeCells('A3:G3');

                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);

                $sheet->cells('A1:M3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A3:M4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO DE ORDENES DE EXAMEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FORMA DE PAGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO %');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RECARGO %');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RECARGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('M4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cant = 1;
                $total = 0;
                foreach ($ordenes as $value) {
                    $txtcolor = '#000000';

                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor, $cant) {
                        // manipulate the cel
                        $cell->setValue($cant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fecha_orden, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }

                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->snombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->dnombre1 . ' ' . $value->dapellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->fpnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuento_p);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuento_valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->recargo_p);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->recargo_valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->total_valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i = $i + 1;

                    $cant  = $cant + 1;
                    $total = $total + $value->total_valor;
                }
                $sheet->getStyle('G5:G' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('J5:J' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('L5:M' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');

                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL PACIENTES:');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('K' . $i, function ($cell) use ($cant) {
                    // manipulate the cel
                    $cell->setValue($cant - 1);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL:');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('M' . $i, function ($cell) use ($total) {
                    // manipulate the cel
                    $cell->setValue($total);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
            });
        })->export('xlsx');
    }

    public function detalle_rpt(Request $request)
    {

        //dd($request->all());
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $seguro      = $request['seguro'];
        $nombres     = $request['nombres'];
        // dd($fecha_hasta);
        //dd($request->all());

        $ordenes = DB::table('examen_orden as eo')->join('paciente as p', 'p.id', 'eo.id_paciente')->join('seguros as s', 's.id', 'eo.id_seguro')->leftjoin('empresa as em', 'em.id', 'eo.id_empresa')->leftjoin('nivel as n', 'n.id', 'eo.id_nivel')->leftjoin('protocolo as proto', 'proto.id', 'eo.id_protocolo')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->join('users as cu', 'cu.id', 'eo.id_usuariocrea')->join('users as mu', 'mu.id', 'eo.id_usuariomod')->join('examen_detalle as ed', 'ed.id_examen_orden', 'eo.id')->join('examen as e', 'e.id', 'ed.id_examen')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 's.nombre as snombre', 'n.nombre as nnombre', 'em.nombrecomercial', 'cu.nombre1 as cnombre1', 'cu.apellido1 as capellido1', 'mu.nombre1 as mnombre1', 'mu.apellido1 as mapellido1', 'em.nombre_corto', 'proto.pre_post', 's.tipo as stipo', 'e.descripcion', 'ed.valor as edvalor', 'ed.cubre');

        //dd($ordenes->get());

        // $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->where('eo.realizado','1')->where('eo.estado','1')->where('s.tipo','1');
        if ($fecha != null) {
            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }
        $ordenes = $ordenes->where('eo.realizado', '1')->where('eo.estado', '1')->where('s.tipo', '1');
        //dd($ordenes->get());

        /*if($nombres!=null)
        {

        $nombres2 = explode(" ", $nombres);
        $cantidad = count($nombres2);

        if($cantidad=='2' || $cantidad=='3'){
        $ordenes = $ordenes->where(function($jq1) use($nombres){
        $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%'])
        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);
        });
        //dd($ordenes->get());
        }
        else{

        $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);
        }

        }*/

        if ($nombres != null) {

            $nombres2    = explode(" ", $nombres);
            $nombres_sql = '';
            $cantidad    = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {

                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {

                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }

        //dd($ordenes->get());
        if ($seguro != null) {

            $ordenes = $ordenes->where('eo.id_seguro', $seguro);
        }

        $ordenes = $ordenes->get();

        //dd($ordenes);

        $seguros = Seguro::where('inactivo', '1')->get();

        $ex_det = [];
        foreach ($ordenes as $orden) {
            $txt_examen = "";
            $detalle    = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'ed.id_examen')->select('ed.*', 'e.nombre', 'e.descripcion')->get();
            $bandera    = 0;
            foreach ($detalle as $value) {
                if ($bandera == 0) {
                    $txt_examen = $value->descripcion;
                    $bandera    = 1;
                } else {
                    $txt_examen = $txt_examen . '+' . $value->descripcion;
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }

        $i = 0;

        $fecha_d = date('Y/m/d');

        Excel::create('Examenes_detalle-' . $fecha_d, function ($excel) use ($ordenes) {

            $excel->sheet('Examenes_detalle', function ($sheet) use ($ordenes) {
                $fecha_d = date('Y/m/d');
                $i       = 5;

                $sheet->mergeCells('A3:G3');

                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);

                $sheet->cells('A1:G3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A3:G4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO DE DETALLE DE EXÁMENES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('EXAMEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CUBRE SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cant = 1;
                $total = 0;
                foreach ($ordenes as $value) {

                    $sheet->cell('A' . $i, function ($cell) use ($value, $cant) {
                        // manipulate the cel
                        $cell->setValue($cant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        //$cell->setValue(substr($value->created_at,0,10));
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }

                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value) {

                        $cell->setValue(substr($value->fecha_orden, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->snombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->edvalor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->cubre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i = $i + 1;

                    $cant  = $cant + 1;
                    $total = $total + $value->total_valor;
                }
                $sheet->getStyle('G5:G' . $i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
            });
        })->export('xlsx');
    }

    public function certificar($id_orden, $id, $n, $maq)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $orden      = Examen_Orden::find($id_orden);
        //MARCA COMO CERTIFICADO
        $resultado = $orden->resultados->where('id_parametro', $id)->first();
        if (!is_null($resultado)) {

            $input = [

                'certificado'     => $n,
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,

            ];

            $resultado->update($input);
        }
        

        

        $estado   = '2'; //0 pendiente: tiene un examen sin ingresar 1:certificar: todos los exámenes ya ingresados y pendiente de certificar  2:listo
        $detalles = Examen_Detalle::where('id_examen_orden', $id_orden)->join('examen as e', 'e.id', 'examen_detalle.id_examen')->where('maquina', $maq)->where('no_resultado', '0')->get();
        //return $detalles;
       
        foreach ($detalles as $detalle) {
            if ($detalle->examen->no_resultado == '0') {

                if (count($detalle->parametros) == '0') {
                    $estado = '0';
                    break;
                }
                if ($detalle->examen->sexo_n_s == '0') {

                    foreach ($detalle->parametros->where('sexo', '3')->where('estado', 1) as $parametro) {
                        $resultado = $orden->resultados->where('id_parametro', $parametro->id)->first();
                        if (is_null($resultado)) {
                            $estado = '0';
                            break;
                        } else {
                            if ($resultado->certificado == '0') {
                                $estado = '1';
                            }
                        }
                    }
                } else {
                    foreach ($detalle->parametros->where('sexo', $orden->paciente->sexo)->where('estado', 1) as $parametro) {
                        $resultado = $orden->resultados->where('id_parametro', $parametro->id)->first();
                        if (is_null($resultado)) {
                            $estado = '0';
                            break;
                        } else {
                            if ($resultado->certificado == '0') {
                                $estado = '1';
                            }
                        }
                    }
                }
            }
        }



        $txt_maquina = '';
        if ($maq == '1') {
            $txt_maquina = 'HEMATOLOGÍA';
            
            if ($orden->er_biometria != $estado) {

                $input = [

                    'er_biometria'    => $estado,
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,

                ];

                $orden->update($input);

                if ($estado == '0') {
                    $descripcion = "ORDEN PENDIENTE DE INGRESAR EXAMEN";
                } elseif ($estado == '1') {
                    $descripcion = "ORDEN PENDIENTE DE CERTIFICACION";
                } else {
                    $descripcion = "ORDEN CERTIFICADA";
                }



                Log_usuario::create([

                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => $descripcion,
                    'dato_ant1'   => "ORDEN: " . $id_orden,
                    'dato1'       => "MAQUINA: " . $txt_maquina,

                ]);
            }
        }

        if ($maq == '2') {
            $txt_maquina = 'BIOQUIMICA';
            if ($orden->er_bioquimica != $estado) {

                $input = [

                    'er_bioquimica'   => $estado,
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,

                ];

                $orden->update($input);

                if ($estado == '0') {
                    $descripcion = "ORDEN PENDIENTE DE INGRESAR EXAMEN";
                } elseif ($estado == '1') {
                    $descripcion = "ORDEN PENDIENTE DE CERTIFICACION";
                } else {
                    $descripcion = "ORDEN CERTIFICADA";
                }

                Log_usuario::create([

                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => $descripcion,
                    'dato_ant1'   => "ORDEN: " . $id_orden,
                    'dato1'       => "MAQUINA: " . $txt_maquina,

                ]);
            }
        }

        if ($maq == '0') {
            $txt_maquina = 'MANUAL';
            if ($orden->er_manual != $estado) {

                $input = [

                    'er_manual'       => $estado,
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,

                ];

                $orden->update($input);

                if ($estado == '0') {
                    $descripcion = "ORDEN PENDIENTE DE INGRESAR EXAMEN";
                } elseif ($estado == '1') {
                    $descripcion = "ORDEN PENDIENTE DE CERTIFICACION";
                } else {
                    $descripcion = "ORDEN CERTIFICADA";
                }

                Log_usuario::create([

                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => $descripcion,
                    'dato_ant1'   => "ORDEN: " . $id_orden,
                    'dato1'       => "MAQUINA: " . $txt_maquina,

                ]);
            }
        }

        return $estado;
    }

    public function guardar_ordenes_check(Request $request)
    {

        $orden      = Examen_Orden::find($request['datos'][0]['id_orden']);
        $maq        = $request['maq']; //dd($maq);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        foreach ($request['datos'] as $val) {
            $resultado = $orden->resultados->where('id_parametro', $val['id'])->first();
            if (!is_null($resultado)) {

                $input = [

                    'certificado'     => $val['dato'],
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,

                ];

                $resultado->update($input);
            }
        }

        $estado   = '2'; //0 pendiente: tiene un examen sin ingresar 1:certificar: todos los exámenes ya ingresados y pendiente de certificar  2:listo
        $detalles = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'examen_detalle.id_examen')->where('maquina', $maq)->where('no_resultado', '0')->get();
        //return $detalles;
        foreach ($detalles as $detalle) {
            if ($detalle->examen->no_resultado == '0') {

                if (count($detalle->parametros) == '0') {
                    $estado = '0';
                    break;
                }
                if ($detalle->examen->sexo_n_s == '0') {

                    foreach ($detalle->parametros->where('sexo', '3') as $parametro) {
                        $resultado = $orden->resultados->where('id_parametro', $parametro->id)->first();
                        if (is_null($resultado)) {
                            $estado = '0';
                            break;
                        } else {
                            if ($resultado->certificado == '0') {
                                $estado = '1';
                            }
                        }
                    }
                } else {
                    foreach ($detalle->parametros->where('sexo', $orden->paciente->sexo) as $parametro) {
                        $resultado = $orden->resultados->where('id_parametro', $parametro->id)->first();
                        if (is_null($resultado)) {
                            $estado = '0';
                            break;
                        } else {
                            if ($resultado->certificado == '0') {
                                $estado = '1';
                            }
                        }
                    }
                }
            }
        }

        $txt_maquina = '';
        if ($maq == '1') {
            $txt_maquina = 'HEMATOLOGÍA';
            if ($orden->er_biometria != $estado) {

                $input = [

                    'er_biometria'    => $estado,
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,

                ];

                $orden->update($input);

                if ($estado == '0') {
                    $descripcion = "ORDEN PENDIENTE DE INGRESAR EXAMEN";
                } elseif ($estado == '1') {
                    $descripcion = "ORDEN PENDIENTE DE CERTIFICACION";
                } else {
                    $descripcion = "ORDEN CERTIFICADA";
                }

                Log_usuario::create([

                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => $descripcion,
                    'dato_ant1'   => "ORDEN: " . $orden->id,
                    'dato1'       => "MAQUINA: " . $txt_maquina,

                ]);
            }
        }

        if ($maq == '2') {
            $txt_maquina = 'BIOQUIMICA';
            if ($orden->er_bioquimica != $estado) {

                $input = [

                    'er_bioquimica'   => $estado,
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,

                ];

                $orden->update($input);

                if ($estado == '0') {
                    $descripcion = "ORDEN PENDIENTE DE INGRESAR EXAMEN";
                } elseif ($estado == '1') {
                    $descripcion = "ORDEN PENDIENTE DE CERTIFICACION";
                } else {
                    $descripcion = "ORDEN CERTIFICADA";
                }

                Log_usuario::create([

                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => $descripcion,
                    'dato_ant1'   => "ORDEN: " . $orden->id,
                    'dato1'       => "MAQUINA: " . $txt_maquina,

                ]);
            }
        }

        if ($maq == '0') {
            $txt_maquina = 'MANUAL';
            if ($orden->er_manual != $estado) {

                $input = [

                    'er_manual'       => $estado,
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,

                ];

                $orden->update($input);

                if ($estado == '0') {
                    $descripcion = "ORDEN PENDIENTE DE INGRESAR EXAMEN";
                } elseif ($estado == '1') {
                    $descripcion = "ORDEN PENDIENTE DE CERTIFICACION";
                } else {
                    $descripcion = "ORDEN CERTIFICADA";
                }

                Log_usuario::create([

                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => $descripcion,
                    'dato_ant1'   => "ORDEN: " . $orden->id,
                    'dato1'       => "MAQUINA: " . $txt_maquina,

                ]);
            }
        }

        return json_encode('ok');
    }

    public function marca_listo()
    {

        $ordenes = Examen_Orden::where('estado', '1')->get();
        foreach ($ordenes as $orden) {
            //$orden = Examen_Orden::find($id);
            $detalle    = $orden->detalles;
            $resultados = $orden->resultados;
            //$parametros = Examen_parametro::orderBy('orden')->get();

            $cant_par = 0;
            foreach ($detalle as $d) {
                //$parametros = $parametros->where('id_examen', $d->id_examen);
                if ($d->examen->no_resultado == '0') {

                    if (count($d->parametros) == '0') {
                        $cant_par++;
                    }
                    if ($d->examen->sexo_n_s == '0') {
                        $parametro_nuevo = $d->parametros->where('sexo', '3');
                    } else {
                        $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo);
                    }
                    foreach ($parametro_nuevo as $p) {
                        $cant_par++;
                    }
                }
            }

            $certificados = 0;
            $cantidad     = 0;
            foreach ($resultados as $r) {
                $cantidad++;
                if ($r->certificado == '1') {
                    $certificados++;
                }
            }
            if ($certificados > $cant_par) {
                $certificados = $cant_par;
            }
            if ($certificados == $cant_par) {
                $input = [
                    'er_biometria'  => '2',
                    'er_bioquimica' => '2',
                    'er_manual'     => '2',
                ];
                $orden->update($input);
            }
        }

        return "ok";
    }

    /*public function genera_protocolo_privado($id_orden, $id_protocolo)
    {

    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $idusuario  = Auth::user()->id;
    date_default_timezone_set('America/Guayaquil');

    $orden = Examen_Orden::find($id_orden);

    $pexamenes     = Examen_Protocolo::where('id_protocolo', $id_protocolo)->get();
    /*$detalles_elim = Examen_Detalle::where('id_examen_orden', $id_orden)->get();

    foreach ($detalles_elim as $det_elim) {
    $det_elim->delete();
    }

    $descuento_p = $orden->descuento_p;

    foreach ($pexamenes as $pexamen) {

    //ACTUALIZA DETALLE
    $detalle = $orden->detalles->where('id_examen', $pexamen->id_examen)->first();

    if (is_null($detalle)) {
    $examen = Examen::find($pexamen->id_examen);
    //return $examen;
    $valor    = $examen->valor;
    $cubre    = 'NO';
    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $orden->id_nivel)->first();
    if (!is_null($ex_nivel)) {
    if ($ex_nivel->valor1 != 0) {
    $valor = $ex_nivel->valor1;
    $cubre = 'SI';
    }
    }

    $input_det = [
    'id_examen_orden' => $orden->id,
    'id_examen'       => $examen->id,
    'valor'           => $valor,
    'cubre'           => $cubre,
    'ip_creacion'     => $ip_cliente,
    'ip_modificacion' => $ip_cliente,
    'id_usuariocrea'  => $idusuario,
    'id_usuariomod'   => $idusuario,
    ];

    Examen_detalle::create($input_det);

    /*
    $total       = $orden->valor + $valor;
    $total       = round($total, 2);
    $descuento_p = $orden->descuento_p;
    if ($orden->descuento_p == null) {
    $descuento_valor = 0;
    $descuento_p     = 0;
    }

    if ($orden->descuento_p > 100) {
    $descuento_valor = $total;
    } else {
    $descuento_valor = $orden->descuento_p * $total / 100;
    $descuento_valor = round($descuento_valor, 2);
    }

    $subtotal_pagar = $total - $descuento_valor;

    $recargo_p = $orden->recargo_p;

    $recargo_valor = $subtotal_pagar * $recargo_p / 100;
    $recargo_valor = round($recargo_valor, 2);
    $valor_total   = $subtotal_pagar + $recargo_valor;
    $valor_total   = round($valor_total, 2);

    }

    }

    //ACTUALIZAR ORDEN
    if ($id_protocolo == '0') {
    $id_protocolo = null;
    }

    $input_ex = [
    'id_protocolo'    => $id_protocolo,
    /*'descuento_valor' => $descuento_valor,
    'recargo_valor' =>  $recargo_valor,
    'total_valor' => $valor_total,
    'cantidad' => $orden->cantidad + 1,
    'valor' => $total,
    'ip_modificacion' => $ip_cliente,
    'id_usuariomod'   => $idusuario,

    ];

    $orden->update($input_ex);

    Log_usuario::create([
    'id_usuario'  => $idusuario,
    'ip_usuario'  => $ip_cliente,
    'descripcion' => "ACTUALIZA COTIZACION",
    'dato_ant1'   => $orden->id,
    'dato1'       => $orden->id_paciente,
    //'dato_ant4' => $examen->id,
    ]);
    ///

    return "listo";
    //ACTUALIZA DETALLE

    }*/

    public function genera_protocolo_privado($id_orden, $id_protocolo)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $orden     = Examen_Orden::find($id_orden);
        $pexamenes = Examen_Protocolo::where('id_protocolo', $id_protocolo)->get();
        //agrega los examenes del protocolo
        foreach ($pexamenes as $pexamen) {
            if ($pexamen->id_examen != '1225') {
                $detalle = $orden->detalles->where('id_examen', $pexamen->id_examen)->first();
                if (is_null($detalle)) {
                    $examen   = Examen::find($pexamen->id_examen);
                    $valor    = $examen->valor;
                    $cubre    = 'NO';
                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $orden->id_nivel)->first();
                    if (!is_null($ex_nivel)) {
                        if ($ex_nivel->valor1 != 0) {
                            $valor = $ex_nivel->valor1;
                            $cubre = 'SI';
                        }
                    }
                    $input_det = [
                        'id_examen_orden' => $orden->id,
                        'id_examen'       => $examen->id,
                        'valor'           => $valor,
                        'cubre'           => $cubre,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'p_descuento'     => 0,
                        'valor_descuento' => 0,
                    ];
                    Examen_detalle::create($input_det);
                }
            }
        }
        //recalcula orden
        $orden       = Examen_Orden::find($id_orden);
        $detalles    = $orden->detalles;
        $pyf_details = array();
        foreach ($detalles as $detalle) {
            array_push($pyf_details, array(
                "valor"           => $detalle->valor,
                "nombre"          => $detalle->examen->nombre,
                "valor_descuento" => $detalle->valor_descuento,
                "p_descuento"     => $detalle->p_descuento,
            ));
        }
        $manage          = json_encode($pyf_details);
        $total           = $orden->detalles->sum('valor');
        $cantidad        = $orden->detalles->count();
        $total           = round($total, 2);
        $descuento_total = $orden->detalles->sum('valor_descuento');
        $subtotal_pagar  = $total - $descuento_total;
        $recargo_p       = 0;
        $forma_pago      = DB::table('forma_de_pago')->where('id', $orden->id_forma_pago)->first();
        if (!is_null($forma_pago)) {
            $recargo_p     = $forma_pago->recargo_p;
            $recargo_valor = $subtotal_pagar * $recargo_p / 100;
            $recargo_valor = round($recargo_valor, 2);
        } else {
            $recargo_valor = $orden->detalle_forma_pago->sum('p_fi');
        }

        $valor_total = $subtotal_pagar + $recargo_valor;
        $valor_total = round($valor_total, 2);
        //ACTUALIZAR ORDEN
        if ($id_protocolo == '0') {
            $id_protocolo = null;
        }
        $input_ex = [
            'descuento_valor' => $descuento_total,
            'recargo_p'       => $recargo_p,
            'recargo_valor'   => $recargo_valor,
            'total_valor'     => $valor_total,
            'cantidad'        => $cantidad,
            'valor'           => $total,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
            'id_protocolo'    => $id_protocolo,
        ];
        $orden->update($input_ex);
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $orden->id,
            'dato1'       => $orden->id_paciente,
            'dato_ant2'   => "AGREGAR PROTOCOLO",
            'dato_ant4'   => $manage,
        ]);

        return "listo";
    }

    public function buscar_orden($id_paciente)
    {

        $hoy              = date('Y/m/d');
        $cant_ordenes_hoy = Examen_Orden::where('id_paciente', $id_paciente)
            ->where('estado', '1')
            ->whereBetween('fecha_orden', [$hoy . ' 0:00:00', $hoy . ' 23:59:00'])
            ->get()->count();

        return $cant_ordenes_hoy;
    }

    public function buscar_orden_doctor($id_paciente)
    {

        $ordenes_hoy = Examen_Orden::where('id_paciente', $id_paciente)
            ->join('users as u', 'u.id', 'examen_orden.id_usuariocrea')
            ->select('examen_orden.*')
            ->where('examen_orden.estado', '-1')
            ->where('u.id_tipo_usuario', '3')
            ->orderBy('examen_orden.created_at', 'desc')
            ->first();

        if (!is_null($ordenes_hoy)) {
            return $ordenes_hoy->id;
        } else {
            return '0';
        }
    }

    //Modal Pago Muestra datos paciente (Modifica Email)
    public function modal_pago_paciente($id_paciente, $id_exa_orden)
    {

        $paciente = Paciente::find($id_paciente);
        $orden    = Examen_Orden::find($id_exa_orden);
        //dd($orden);
        $grupo_fam = Labs_Grupo_Familiar::find($id_paciente);
        if (!is_null($grupo_fam)) {
            $user_aso = User::find($grupo_fam->id_usuario);
        } else {
            $user_aso = User::find($paciente->id_usuario);
        }

        return view('laboratorio/orden/modal_pago', ['paciente' => $paciente, 'user_aso' => $user_aso, 'id_exa_orden' => $id_exa_orden, 'orden' => $orden]);
    }

    public function update_estado_email_paciente(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $ausuario   = Auth::user()->id;

        $id_exa      = $request['id_exa_orden'];
        $id_paciente = $request['id_paciente'];
        $id_usuario  = $request['id_usuario'];

        $mensajes = [
            'email.unique'   => 'El Email ya se encuentra registrado.',
            'email.required' => 'Ingrese el mail.',
            'email.max'      => 'El Email no puede ser mayor a :max caracteres.',
            'email.email'    => 'Mail ingresado con formato incorrecto.',
        ];

        $constraints = [
            //'email' => 'required|email|max:191|unique:users,email,' . $id_usuario . ',id',
            'email' => 'required|email|max:191',
        ];

        $this->validate($request, $constraints, $mensajes);

        $paciente = Paciente::find($id_paciente);

        //Nuevo Cambio
        //Principal
        if ($id_paciente == $id_usuario) {
            $usuario = $this->recupera_mail($request['email']);
            if ($usuario != 'no') {
                $gr_fam = Labs_Grupo_Familiar::find($id_paciente);
                if (is_null($gr_fam)) {
                    // crear grupo familiar return $usuario;
                    if ($id_paciente != $usuario->id) {
                        $arr_gr = [
                            'id'              => $id_paciente,
                            'id_usuario'      => $usuario->id,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $ausuario,
                            'id_usuariomod'   => $ausuario,
                        ];
                        Labs_Grupo_Familiar::create($arr_gr);
                    }
                }
                $user   = $usuario;
                $correo = $user->email;

                $nombre_paciente = $paciente->nombre1 . " ";

                if ($paciente->nombre2 != '(N/A)') {
                    $nombre_paciente = $nombre_paciente . $paciente->nombre2 . " ";
                }

                $nombre_paciente = $nombre_paciente . $paciente->apellido1 . " ";
                if ($paciente->apellido2 != '(N/A)') {
                    $nombre_paciente = $nombre_paciente . $paciente->apellido2 . " ";
                }
            } else {
                $input1 = [
                    'email'    => $request['email'],
                    'password' => bcrypt($id_paciente),
                ];

                User::where('id', $id_usuario)->update($input1);
                $user   = User::find($id_usuario);
                $correo = $user->email;

                $nombre_paciente = $paciente->nombre1 . " ";

                if ($paciente->nombre2 != '(N/A)') {
                    $nombre_paciente = $nombre_paciente . $paciente->nombre2 . " ";
                }

                $nombre_paciente = $nombre_paciente . $paciente->apellido1 . " ";
                if ($paciente->apellido2 != '(N/A)') {
                    $nombre_paciente = $nombre_paciente . $paciente->apellido2 . " ";
                }
            }
            //No principal
        } else if ($id_paciente != $id_usuario) {

            $input1 = [
                'email'    => $request['email'],
                'password' => bcrypt($id_usuario),
            ];

            //User::where('id', $id_usuario)->update($input1);
            $user   = User::find($id_usuario);
            $correo = $user->email;

            $nombre_paciente = $paciente->nombre1 . " ";

            if ($paciente->nombre2 != '(N/A)') {
                $nombre_paciente = $nombre_paciente . $paciente->nombre2 . " ";
            }

            $nombre_paciente = $nombre_paciente . $paciente->apellido1 . " ";
            if ($paciente->apellido2 != '(N/A)') {
                $nombre_paciente = $nombre_paciente . $paciente->apellido2 . " ";
            }
        }

        $input2 = [
            'estado_pago' => '1',
        ];

        Examen_Orden::where('id', $id_exa)->update($input2);

        //Envio de Correo al Paciente
        $msj_labs = array("nombre_paciente" => $nombre_paciente, "user" => $user, "paciente" => $paciente);

        if ($paciente->mail_primera_vez == '0') {

            $input4 = [

                'mail_primera_vez' => '1',
            ];

            Paciente::where('id', $id_paciente)->update($input4);
        }

        Mail::send('mails.labs', $msj_labs, function ($msj) use ($correo) {
            $msj->from("noreply@labs.ec", "HUMANLABS");
            $msj->subject('Resultados de Exámenes de Laboratorio');
            $msj->to($correo);
            $msj->bcc('torbi10@hotmail.com');
        });

        return "okay";
    }

    //(Reenvio Email Paciente)
    public function open_reenvio_email($id_paciente, $id_exa_orden)
    {

        $paciente = Paciente::find($id_paciente);

        $grupo_fam = Labs_Grupo_Familiar::find($id_paciente);
        if (!is_null($grupo_fam)) {
            $user_aso = User::find($grupo_fam->id_usuario);
        } else {
            $user_aso = User::find($paciente->id_usuario);
        }

        //dd($paciente);

        return view('laboratorio/orden/modal_reenvio_email', ['paciente' => $paciente, 'user_aso' => $user_aso, 'id_exa_orden' => $id_exa_orden]);
    }

    public function reenviar_email_paciente(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $ausuario   = Auth::user()->id;

        $id_exa      = $request['id_exa_orden'];
        $id_paciente = $request['id_paciente'];
        $id_usuario  = $request['id_usuario'];

        $mensajes = [
            'email.unique'   => 'El Email ya se encuentra registrado.',
            'email.required' => 'Ingrese el mail.',
            'email.max'      => 'El Email no puede ser mayor a :max caracteres.',
            'email.email'    => 'Mail ingresado con formato incorrecto.',
        ];

        $constraints = [
            //'email' => 'required|email|max:191|unique:users,email,' . $id_usuario . ',id',
            'email' => 'required|email|max:191',
        ];

        $this->validate($request, $constraints, $mensajes);

        $paciente = Paciente::find($id_paciente);

        if ($id_paciente == $id_usuario) {

            $usuario = $this->recupera_mail($request['email']);
            if ($usuario != 'no') {
                $gr_fam = Labs_Grupo_Familiar::find($id_paciente);
                if (is_null($gr_fam)) {
                    // crear grupo familiar return $usuario;
                    if ($id_paciente != $usuario->id) {
                        $arr_gr = [
                            'id'              => $id_paciente,
                            'id_usuario'      => $usuario->id,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $ausuario,
                            'id_usuariomod'   => $ausuario,
                        ];
                        Labs_Grupo_Familiar::create($arr_gr);
                    }
                }
                $user   = $usuario;
                $correo = $user->email;

                $nombre_paciente = $paciente->nombre1 . " ";

                if ($paciente->nombre2 != '(N/A)') {
                    $nombre_paciente = $nombre_paciente . $paciente->nombre2 . " ";
                }

                $nombre_paciente = $nombre_paciente . $paciente->apellido1 . " ";
                if ($paciente->apellido2 != '(N/A)') {
                    $nombre_paciente = $nombre_paciente . $paciente->apellido2 . " ";
                }
            } else {

                $input1 = [
                    'email'    => $request['email'],
                    'password' => bcrypt($id_paciente),
                ];

                User::where('id', $id_usuario)->update($input1);
                $user   = User::find($id_usuario);
                $correo = $user->email;

                $nombre_paciente = $paciente->nombre1 . " ";

                if ($paciente->nombre2 != '(N/A)') {
                    $nombre_paciente = $nombre_paciente . $paciente->nombre2 . " ";
                }

                $nombre_paciente = $nombre_paciente . $paciente->apellido1 . " ";
                if ($paciente->apellido2 != '(N/A)') {
                    $nombre_paciente = $nombre_paciente . $paciente->apellido2 . " ";
                }
            }

            //No principal
        } else if ($id_paciente != $id_usuario) {

            $input1 = [
                'email'    => $request['email'],
                'password' => bcrypt($id_usuario),
            ];

            //User::where('id', $id_usuario)->update($input1);
            $user   = User::find($id_usuario);
            $correo = $user->email;

            $nombre_paciente = $paciente->nombre1 . " ";

            if ($paciente->nombre2 != '(N/A)') {
                $nombre_paciente = $nombre_paciente . $paciente->nombre2 . " ";
            }

            $nombre_paciente = $nombre_paciente . $paciente->apellido1 . " ";
            if ($paciente->apellido2 != '(N/A)') {
                $nombre_paciente = $nombre_paciente . $paciente->apellido2 . " ";
            }
        }

        //Reenvio de Correo al Paciente
        $msj_labs = array("nombre_paciente" => $nombre_paciente, "user" => $user, "paciente" => $paciente);

        Mail::send('mails.labs', $msj_labs, function ($msj) use ($correo) {
            $msj->from("noreply@labs.ec", "HUMANLABS");
            $msj->subject('Resultados de Exámenes de Laboratorio');
            $msj->to($correo);
            $msj->bcc('torbi10@hotmail.com');
        });

        return "okay";
    }
    public function enviar_email_dr_externo($id_orden)
    {
        //dd($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $ausuario   = Auth::user()->id;
        $orden = Examen_Orden::find($id_orden);
        //dd($orden->doc_externo->nombre1);
        //$id_exa      = $request['id_exa_orden'];
        //$paciente = $orden->id_paciente;
        $paciente = Paciente::find($orden->id_paciente);
        $nombre_paciente = $paciente->nombre1 . " ";
        if ($paciente->nombre2 != '(N/A)') {
            $nombre_paciente = $nombre_paciente . $paciente->nombre2 . " ";
        }
        $nombre_paciente = $nombre_paciente . $paciente->apellido1 . " ";
        if ($paciente->apellido2 != '(N/A)') {
            $nombre_paciente = $nombre_paciente . $paciente->apellido2 . " ";
        }
        $msj_dr_externo = array("orden" => $orden, "nombre_paciente" => $nombre_paciente);
        Mail::send('mails.labs_examen_dr_externo', $msj_dr_externo, function ($msj) use ($nombre_paciente, $orden) {
            $msj->from("noreply@labs.ec", "HUMANLABS");
            $msj->subject('Exámenes de Laboratorio - ' . $nombre_paciente);
            $msj->to($orden->doc_externo->correo);
            $msj->bcc("walteralarcon202@gmail.com");
        });

        $input_estado_correo = [
            'enviado_doctor_externo' => '1',
            'ip_modificacion'  => $ip_cliente,
            'id_usuariomod'   => $ausuario,
        ];
        $orden->update($input_estado_correo);
        return "okay";
    }

    //(Reseteo clave Paciente)
    public function open_reseteo_clave($id_paciente, $id_exa_orden)
    {

        $paciente = Paciente::find($id_paciente);

        $user_aso = User::find($paciente->id_usuario);

        return view('laboratorio/orden/modal_reseteo_clave', ['paciente' => $paciente, 'user_aso' => $user_aso, 'id_exa_orden' => $id_exa_orden]);
    }

    public function reseteo_clave_paciente(Request $request)
    {

        $id_usuario = $request['id_usuario'];

        $mensajes = [

            'password.required' => 'Agrega el password.',
            'password.min'      => 'El Password debe ser mayor a :min caracteres.',
            //'password.confirmed'        => 'El Password y su confirmación no coinciden.',

        ];

        $constraints = [

            //'password' => 'required|min:6|confirmed',
            'password' => 'required|min:6|',

        ];

        $this->validate($request, $constraints, $mensajes);

        if ($request['password'] != null && strlen($request['password']) > 0) {

            $input = [

                'password' => bcrypt($request['password']),

            ];

            User::where('id', $id_usuario)->update($input);

            //$mpass = "ACTUALIZA CONTRASEÑA ";

        }

        return "okay";
    }

    public function confirmar_publico($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $fecha_orden = date('Y/m/d H:i:s');
        $orden       = Examen_Orden::find($id);
        $anio        = date('Y', strtotime($fecha_orden));
        $mes         = date('m', strtotime($fecha_orden));
        $arr         = [
            'estado'      => '1',
            'realizado'   => '1',
            'fecha_orden' => $fecha_orden,
            'anio'        => $anio,
            'mes'         => $mes,
            'id_usuariomod'   => $idusuario,

        ];

        if ($orden->detalles->count() == '0') {
            return ['estado' => 'err', 'mensaje' => 'Orden sin exámenes'];
        }
        $orden->update($arr);

        $txt = '';
        foreach ($orden->detalles as $detalle) {
            if ($txt == '') {
                $txt = $detalle->examen->nombre;
            }
            $txt = $txt . ' - ' . $detalle->examen->nombre;
        }

        /*$input_log = [
        'id_usuario'  => $idusuario,
        'ip_usuario'  => $ip_cliente,
        'descripcion' => "REALIZAR PUBLICO",
        'dato_ant1'   => $orden->id,
        'dato1'       => $orden->id_paciente,
        'dato_ant4'   => " ",
        'dato2'       => ' ',
        ];*/

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $orden->id,
            'dato1'       => $orden->id_paciente,
            'dato_ant2'   => "GENERAR ORDEN PUBLICA",
            'dato2'       => $orden->fecha_orden,
            'dato_ant4'   => $txt,
        ]);

        NuevoResultadoController::crear_resultados($orden->id);

        return redirect()->route('orden.index');
        //return ['estado' => 'ok', 'mensaje' => 'ok'];

    }
    public function guardar_testdealimentos($id_parametro, $valor, $id_orden)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        //return $id_parametro;

        $parametro = Examen_parametro::find($id_parametro);
        $resultado = Examen_resultado::where('id_orden', $id_orden)->where('id_parametro', $id_parametro)->first();

        if (!is_null($resultado)) {
            $input_ex1 = [

                'valor'           => $valor,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,

            ];

            $resultado->update($input_ex1);
        } else {
            $input_det = [
                'id_orden'        => $id_orden,
                'id_parametro'    => $id_parametro,
                'valor'           => $valor,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'id_examen'       => '639',

            ];

            Examen_resultado::create($input_det);
        }
        return $id_parametro;
    }
    public function recupera_mail($mail)
    {
        $usuario = User::where('email', $mail)->first();
        if (!is_null($usuario)) {
            return $usuario;
        } else {
            return 'no';
        }
    }

    public function recupera_usuario($id_usuario)
    {
        $grupo_fam = Labs_Grupo_Familiar::find($id_usuario);
        if (!is_null($grupo_fam)) {
            $usuario = User::find($grupo_fam->id_usuario);
            if (!is_null($usuario)) {
                return $usuario->email;
            }
        }
        $paciente = Paciente::find($id_usuario);
        if (!is_null($paciente)) {
            $usuario = User::find($paciente->id_usuario);
            if (!is_null($usuario)) {
                return $usuario->email;
            }
        }
        return "no";
    }

    //Nueva Funcionalidad 25/6/2020
    public function recupera_info_factura()
    {

        $tip_publico = Examen_Orden::where('id_seguro', '<>', 0)
            ->join('seguros as seg', 'seg.id', 'examen_orden.id_seguro')
            ->join('examen_detalle as exad', 'exad.id_examen_orden', 'examen_orden.id')
            ->where('seg.tipo', 0)
            ->where('exad.id_examen', 1191)
            ->select(
                'examen_orden.id_paciente as id_pac',
                'examen_orden.fecha_orden as fech_orden',
                'examen_orden.id_seguro as id_seg',
                'examen_orden.id as id_orden',
                'examen_orden.valor as valor',
                'examen_orden.cantidad as cant',
                'examen_orden.descuento_p as descuent_p',
                'examen_orden.descuento_valor as descuent_v',
                'examen_orden.recargo_p as recarg_p',
                'examen_orden.recargo_valor as recarg_v',
                'examen_orden.total_valor as tot_val',
                'exad.id_examen as id_examen',
                'exad.valor as val_det'
            )->get();

        Excel::create('Reporte Examen Orden', function ($excel) use ($tip_publico) {
            $excel->sheet(date('Y-m-d'), function ($sheet) use ($tip_publico) {

                $sheet->mergeCells('A1:F1');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('EXAMEN ORDEN');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                });

                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('#Paciente');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('#Identificacion');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('#Orden');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('D3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('#Fecha Orden');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Seguro');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('G3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('cantidad');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('descuento_p');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('I3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('descuento_valor');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('J3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('recargo_p');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('recargo_valor');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('total_valor');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('#Id_Examen');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor_Detalle');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $i = 4;

                foreach ($tip_publico as $value) {

                    $paciente = Paciente::where('id', $value->id_pac)->first();
                    $seg      = Seguro::where('id', $value->id_seg)->where('inactivo', '1')->first();

                    $sheet->cell('A' . $i, function ($cell) use ($paciente) {
                        // manipulate the cel
                        if (!is_null($paciente)) {

                            $cell->setValue($paciente->apellido1 . ' ' . $paciente->nombre1);
                        }
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_pac);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_orden);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->fech_orden);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($seg) {
                        if (!is_null($seg)) {
                            $cell->setValue($seg->nombre);
                        }
                        // manipulate the cel
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->valor);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->cant);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuent_p);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuent_v);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->recarg_p);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->recarg_v);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->tot_val);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_examen);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('N' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->val_det);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i++;
                }
            });
        })->export('xlsx');

        //return $tip_publico;
        //return  $tip_publico[0];
        //return  $tip_publico[0]->seguro->tipo;

    }

    public function cambio_masivo_valores()
    {
        $nivel        = 20; //VUMI
        $examen_nivel = Examen_Nivel::where('nivel', $nivel)->where('valor1', '>', 0)->get();
        //dd($examen_nivel);
        foreach ($examen_nivel as $value) {
            $examen = Examen::find($value->id_examen);
            $valor  = $examen->valor;
            $valor  = round(0.9 * $valor, 2);
            $value->update([
                'valor1'          => $valor,
                'ip_modificacion' => 'VUMI0620',
            ]);
        }
        return "ok";
    }

    public function validacion_maximos(Request $request)
    {
        $parametro = Examen_Parametro::find($request->id_parametro);
        if (!is_null($parametro)) {
            if ($parametro->texto_referencia == null) {
                $xvar = str_replace('*', '', $request->valor);
                if (is_numeric($parametro->valor1g)) {
                    if ($xvar > $parametro->valor1g) {
                        return "mayor";
                    }
                }
                if (is_numeric($parametro->valor1)) {
                    if ($xvar < $parametro->valor1) {
                        return "menor";
                    }
                }
            }
        }
    }

    public function pagoenlinea_gestionar(Request $request)
    {

        //dd($request->all());
        $cedula      = $request['cedula'];
        $fecha_desde = date('Y-m-d');
        $fecha_hasta = date('Y-m-d');

        //$ordenes = Examen_Orden::where('pago_online', '1')->where('estado', '1')->whereNull('fecha_orden')->where('estado_pago', '1');
        $ordenes = Examen_Orden::where('pago_online', '1')->where('estado', '1')->whereNull('fecha_orden')->where('gestion', '0')->where('estado_pago', '1');

        if ($cedula != null) {
            $ordenes = $ordenes->where('id_paciente', $cedula);
        }
        if ($fecha_desde != null) {
            $ordenes = $ordenes->whereBetween('created_at', [$fecha_desde . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        $ordenes = $ordenes->get();

        return view('laboratorio/orden/pagoenlinea_gestionar', ['ordenes' => $ordenes, 'cedula' => $cedula, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde]);
    }

    public function eliminar_devolver(Request $request)
    {
        return view('laboratorio/orden/eliminar_devolver');
    }
    public function devolver_update(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $orden      = Examen_Orden::find($request->n_orden);
        if (!is_null($orden)) {
            if (is_null($orden->comprobante) && is_null($orden->fecha_envio)) {
                if ($request->estado_dev == '1') {
                    if ($orden->estado != '0') {
                        $arreglo = [
                            'estado'        => '-1',
                            'estado_pago'   => '0',
                            'realizado'     => '0'
                        ];
                        Log_usuario::create([
                            'id_usuario'  => $idusuario,
                            'ip_usuario'  => $ip_cliente,
                            'descripcion' => "DEVOLVER_ORDEN_LABS",
                            'dato_ant1'   => $orden->id,
                            'dato1'       => $orden->id_paciente,
                            'dato_ant2'   => $orden->fecha_orden,
                            'dato2'       => $orden->estado,
                            'dato3'       => $orden->estado_pago,
                            'dato4'       => $orden->realizado,
                        ]);
                    } else {
                        return "orden_eliminada";
                    }
                } else {
                    $arreglo = [
                        'estado'            => '0',
                        'id_usuariomod'     => $idusuario
                    ];
                    Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "ELIMINAR_ORDEN_LABS",
                        'dato_ant1'   => $orden->id,
                        'dato1'       => $orden->id_paciente,
                        'dato_ant2'   => $orden->fecha_orden,
                        'dato2'       => $orden->estado,
                    ]);
                }
                $orden->update($arreglo);
            } else {
                return "orden_facturada";
            }
        } else {
            return "orden_no_existe";
        }
        return "ok";
    }

    public function prioridad_update(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $orden      = Examen_Orden::find($request->n_orden);
        if (!is_null($orden)) {
            if($request->estado == 'true'){
                $arreglo = [
                    'prioridad_orden'  => '1',
                ];
            }else{
                $arreglo = [
                    'prioridad_orden'  => '0',
                ];
            }
            $orden->update($arreglo);

            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "agrego prioridad a la orden",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => $orden->fecha_orden,
                'dato2'       => $request->estado,
            ]);
        }
        return "ok";
    }

    public function pagoenlinea_gestionar_js(Request $request)
    {

        //dd($request->all());
        $cedula      = $request['cedula'];
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];

        $ordenes = Examen_Orden::where('pago_online', '1')->where('estado', '1')->whereNull('fecha_orden')->where('gestion', '0')->where('estado_pago', '1');

        if ($cedula != null) {
            $ordenes = $ordenes->where('id_paciente', $cedula);
        }
        if ($fecha_desde != null) {
            $ordenes = $ordenes->whereBetween('created_at', [$fecha_desde . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        $ordenes = $ordenes->get();

        return view('laboratorio/orden/pagoenlinea_gestionar_js', ['ordenes' => $ordenes, 'cedula' => $cedula, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde]);
    }

    public function pagoenlinea_gestionar_orden($id)
    {
        $orden = Examen_Orden::find($id);

        return view('laboratorio/orden/pagoenlinea_gestionar_orden', ['orden' => $orden]);
    }

    public function pagoenlinea_gestionar_guardar(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $orden      = Examen_Orden::find($request->id_ges);
        $empresa = Empresa::where('prioridad_labs', 1)->first();

        $arr = [
            'fecha_orden' => $request->fecha_orden,
            'gestion'     => '1',
            'anio'        => date('Y', strtotime($request->fecha_orden)),
            'mes'         => date('m', strtotime($request->fecha_orden)),
        ];
        $orden->update($arr);
        $membresia_controller = new MembresiasLabsController;
        $res = $membresia_controller->buscar_membresia($orden->id_paciente);
        if ($res['estado'] == 'ok') {
            if ($orden->total_valor > 50) {
                $puntos = intdiv($orden->total_valor, 25);
                if ($puntos > 0) {
                    $puntos = $res['puntos'] + $puntos;
                    $data['id'] = $res['id_user_mem'];
                    $data['puntos'] = $puntos;
                    $data['motivo'] = 'EMPRESA: ' . $empresa->id . '-' . $empresa->establecimiento . '-' . $empresa->punto_emision . '+ ORDEN:' . $orden->id;
                    $res = $membresia_controller->actualizar_puntos($data);
                }
            }
        }

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $orden->id,
            'dato1'       => $orden->id_paciente,
            'dato_ant2'   => "GESTIONA PAGO EN LINEA",
            'dato2'       => $request->fecha_orden,
        ]);

        return "ok";
    }

    public function aglaboratorio_nuevo($id_agenda)
    {
        $agenda = Agenda::find($id_agenda);
        return view('laboratorio/agenda/agendar', ['agenda' => $agenda]);
    }

    public function aglaboratorio_nuevo_publico($id_paciente)
    {
        $paciente = Paciente::find($id_paciente);
        return view('laboratorio/agenda/agendar_publico', ['paciente' => $paciente]);
    }
    public function new_aglaboratorio_nuevo_publico($id_orden)
    {
        $orden = Examen_Orden::find($id_orden);
        return view('laboratorio/agenda/new_agendar_publico', ['orden' => $orden]);
    }

    public function privados_agendar()
    {
        $procedimientos = Procedimiento::where('estado', '1')->where('laboratorio', '1')->get();
        return view('laboratorio/agenda/agenda_privado', ['procedimientos' => $procedimientos]);
    }

    public function aglaboratorio_calendario(Request $request)
    {

        $doctor_inicial = '4444444444';
        $intervalo_labs = '00:10:00';
        $intervalo_time = 10;

        $fecha = date('Y-m-j');
        if ($request->fecha != null) {
            $fecha = $request->fecha;
        }

        $fecha_ini = strtotime('-2 week', strtotime($fecha));
        $fecha_fin = strtotime('+2 week', strtotime($fecha));

        $fecha_ini = date('Y-m-j', $fecha_ini);
        $fecha_fin = date('Y-m-j', $fecha_fin);

        $horario = DB::table('horario_doctor')
            ->where('id_doctor', '=', $doctor_inicial)->orderBy('ndia')
            ->orderBy('hora_ini')
            ->get();

        $extra = Excepcion_Horario::where('id_doctor1', '=', $doctor_inicial)->whereBetween('inicio', [$fecha_ini . '  0:00:00', $fecha_fin . ' 23:59:59'])->get();

        $procedimientos = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('users as um', 'agenda.id_usuariomod', '=', 'um.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'paciente.ciudad')
            ->where('proc_consul', '=', 1)->where('agenda.estado', '1')
            ->whereBetween('fechaini', [$fecha_ini . '  0:00:00', $fecha_fin . ' 23:59:59'])
            ->where(function ($query) use ($doctor_inicial) {
                $query->where('id_doctor1', '=', $doctor_inicial)
                    ->orWhere('id_doctor2', '=', $doctor_inicial)
                    ->orWhere('id_doctor3', '=', $doctor_inicial);
            })
            ->get();

        $muestras = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->leftjoin('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->leftjoin('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->leftjoin('users as um', 'agenda.id_usuariomod', '=', 'um.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'um.nombre1 as umnombre1', 'paciente.ciudad')
            ->where('proc_consul', '=', 0)->where('agenda.estado', '1')
            ->whereBetween('fechaini', [$fecha_ini . '  0:00:00', $fecha_fin . ' 23:59:59'])
            ->where(function ($query) use ($doctor_inicial) {
                $query->where('id_doctor1', '=', $doctor_inicial)
                    ->orWhere('id_doctor2', '=', $doctor_inicial)
                    ->orWhere('id_doctor3', '=', $doctor_inicial);
            })
            ->get();

        //dd($procedimientos, $muestras, $fecha_ini);

        $reuniones = DB::table('agenda')->where('proc_consul', '=', 2)->where('agenda.estado', '1')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('users as um', 'agenda.id_usuariomod', '=', 'um.id')
            ->select('agenda.*', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'um.nombre1 as umnombre1')
            ->where(function ($query) use ($doctor_inicial) {
                $query->where('id_doctor1', '=', $doctor_inicial)
                    ->orWhere('id_doctor2', '=', $doctor_inicial)
                    ->orWhere('id_doctor3', '=', $doctor_inicial);
            })

            ->whereBetween('fechaini', [$fecha_ini . '  0:00:00', $fecha_fin . ' 23:59:59'])
            ->get();

        $parametros = [
            'horario'        => $horario,
            'extra'          => $extra,
            'intervalo_labs' => $intervalo_labs,
            'intervalo_time' => $intervalo_time,
            'fecha'          => $fecha,
            'doctor_inicial' => $doctor_inicial,
            'procedimientos' => $procedimientos,
            'muestras'       => $muestras,
            'reuniones'      => $reuniones,
        ];

        return view('laboratorio/agenda/calendario_muestra', $parametros);
    }

    public function aglaboratorio_calendario_publico(Request $request)
    {
        $doctor_inicial = '4444444444';
        $intervalo_labs = '00:10:00';
        $intervalo_time = 10;
        $fecha = date('Y-m-j');
        if ($request->fecha_orden != null) {
            $fecha = $request->fecha_orden;
        }
        $fecha_ini = strtotime('-2 week', strtotime($fecha));
        $fecha_fin = strtotime('+2 week', strtotime($fecha));
        $fecha_ini = date('Y-m-j', $fecha_ini);
        $fecha_fin = date('Y-m-j', $fecha_fin);
        $horario = DB::table('horario_doctor')
            ->where('id_doctor', '=', $doctor_inicial)->orderBy('ndia')
            ->orderBy('hora_ini')
            ->get();
        $extra = Excepcion_Horario::where('id_doctor1', '=', $doctor_inicial)->whereBetween('inicio', [$fecha_ini . '  0:00:00', $fecha_fin . ' 23:59:59'])->get();
        $procedimientos = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('users as um', 'agenda.id_usuariomod', '=', 'um.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'paciente.ciudad')
            ->where('proc_consul', '=', 1)->where('agenda.estado', '1')
            ->whereBetween('fechaini', [$fecha_ini . '  0:00:00', $fecha_fin . ' 23:59:59'])
            ->where(function ($query) use ($doctor_inicial) {
                $query->where('id_doctor1', '=', $doctor_inicial)
                    ->orWhere('id_doctor2', '=', $doctor_inicial)
                    ->orWhere('id_doctor3', '=', $doctor_inicial);
            })
            ->get();
        $muestras = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->leftjoin('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->leftjoin('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->leftjoin('users as um', 'agenda.id_usuariomod', '=', 'um.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'um.nombre1 as umnombre1', 'paciente.ciudad')
            ->where('proc_consul', '=', 0)->where('agenda.estado', '1')
            ->whereBetween('fechaini', [$fecha_ini . '  0:00:00', $fecha_fin . ' 23:59:59'])
            ->where(function ($query) use ($doctor_inicial) {
                $query->where('id_doctor1', '=', $doctor_inicial)
                    ->orWhere('id_doctor2', '=', $doctor_inicial)
                    ->orWhere('id_doctor3', '=', $doctor_inicial);
            })
            ->get();
        //dd($procedimientos, $muestras, $fecha_ini);
        $reuniones = DB::table('agenda')->where('proc_consul', '=', 2)->where('agenda.estado', '1')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('users as um', 'agenda.id_usuariomod', '=', 'um.id')
            ->select('agenda.*', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'um.nombre1 as umnombre1')
            ->where(function ($query) use ($doctor_inicial) {
                $query->where('id_doctor1', '=', $doctor_inicial)
                    ->orWhere('id_doctor2', '=', $doctor_inicial)
                    ->orWhere('id_doctor3', '=', $doctor_inicial);
            })
            ->whereBetween('fechaini', [$fecha_ini . '  0:00:00', $fecha_fin . ' 23:59:59'])
            ->get();
        $parametros = [
            'horario'        => $horario,
            'extra'          => $extra,
            'intervalo_labs' => $intervalo_labs,
            'intervalo_time' => $intervalo_time,
            'fecha'          => $fecha,
            'doctor_inicial' => $doctor_inicial,
            'procedimientos' => $procedimientos,
            'muestras'       => $muestras,
            'reuniones'      => $reuniones,
        ];
        return view('laboratorio/agenda/calendario_muestra', $parametros);
    }

    public function aglaboratorio_store(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $convenio   = Convenio::find($request->id_convenio);
        $nivel      = $convenio->id_nivel;
        $id_seguro  = $convenio->id_seguro;
        $id_empresa = $convenio->id_empresa;
        //dd($convenio);

        $valor = 0;
        $cont  = 0;
        $total = 0;

        $arr_examenes = $request->ch;
        if (is_null($arr_examenes)) {
            return ['estado' => 'Error', 'mensaje' => 'Sin exámenes seleccionados'];
        }
        if ($request->id_protocolo == null) {
            return ['estado' => 'Error', 'mensaje' => 'Seleccione un Protocolo'];
        }

        foreach ($arr_examenes as $aex) {
            $id_examen = substr($aex, 2);
            $examen    = Examen::find($id_examen);
            if (!is_null($examen)) {
                $cont++;
                $valor = $examen->valor;
                if (!is_null($convenio)) {

                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            } else {
                return ['estado' => 'Error', 'mensaje' => 'Examen no existe'];
            }
        }
        $agenda = Agenda::find($request['id_agenda']);

        $hcid     = Historiaclinica::where('id_agenda', $request['id_agenda'])->first();
        $paciente = Paciente::find($agenda->id_paciente);

        $hcid_id = null;
        if (!is_null($hcid)) {
            $hcid_id = $hcid->hcid;
        }

        $fecha_orden = $request->inicio;

        $input_ex = [
            'id_paciente'     => $paciente->id,
            'anio'            => substr(date('Y-m-d'), 0, 4),
            'mes'             => substr(date('Y-m-d'), 5, 2),
            'id_protocolo'    => $request['id_protocolo'],
            'id_seguro'       => $id_seguro,
            'id_nivel'        => $nivel,
            'est_amb_hos'     => $request['est_amb_hos'],
            'id_doctor_ieced' => $request['id_doctor_ieced'],
            'doctor_txt'      => $request['doctor_txt'],
            'observacion'     => $request['observacion'],
            'id_empresa'      => $id_empresa,
            'hcid'            => $hcid_id,
            'id_agenda'       => $request['id_agenda'],
            'cantidad'        => $cont,
            'valor'           => $total,
            'total_valor'     => $total,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'fecha_orden'     => $fecha_orden,
            'estado'          => '-1',

        ];

        $input_ag = [
            'id_empresa' => $id_empresa,
        ];

        $agenda->update($input_ag);

        $id_examen_orden = Examen_Orden::insertGetId($input_ex);

        $valor         = 0;
        $cont          = 0;
        $examen_nombre = "";

        foreach ($arr_examenes as $aex) {
            $id_examen = substr($aex, 2);
            $examen    = Examen::find($id_examen);
            if (!is_null($examen)) {
                $examen_nombre = $examen_nombre . '+' . $examen->nombre;
                $cont++;
                $valor = $examen->valor;
                if (!is_null($convenio)) {

                    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                    if (!is_null($ex_nivel)) {
                        $valor = $ex_nivel->valor1;
                    }
                }

                $input_det = [
                    'id_examen_orden' => $id_examen_orden,
                    'id_examen'       => $examen->id,
                    'valor'           => $valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'p_descuento'     => 0,
                    'valor_descuento' => 0,

                ];

                Examen_detalle::create($input_det);
            } else {
                return ['estado' => 'Error', 'mensaje' => 'Examen no existe'];
            }
        }

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $id_examen_orden,
            'dato1'       => $paciente->id,
            'dato_ant2'   => "CREA ORDEN PUBLICA - PROCEDIMIENTO",
            //'dato_ant2'   => "GENERA ORDEN PUBLICA - PROCEDIMIENTO",
            'dato_ant4'   => $examen_nombre,
        ]);

        if ($paciente->fecha_nacimiento == null || $paciente->sexo == null) {

            $pac = [
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'sexo'             => $request['sexo'],
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,
            ];
            $paciente->update($pac);
        }
        //AGREGAR NUEVA AGENDA
        $estado_cita    = 0;
        $estado         = 1;
        $cortesia       = "NO";
        $proc_consul    = 0;
        $doctor_inicial = '4444444444';
        $sala           = '22';
        $espid          = '10';
        $tipo_cita      = 1;
        $id_seguro      = '2';

        $input_historia = [
            'fechaini'        => $request['inicio'],
            'fechafin'        => $request['fin'],
            'id_paciente'     => $paciente->id,
            'id_doctor1'      => $doctor_inicial,
            'proc_consul'     => $proc_consul,
            'id_empresa'      => $id_empresa,
            'id_sala'         => $sala,
            'espid'           => $espid,
            'tipo_cita'       => $tipo_cita,
            'estado_cita'     => $estado_cita,
            'observaciones'   => $request['observaciones'],
            'est_amb_hos'     => $request['est_amb_hos'],
            'id_seguro'       => $id_seguro,
            'estado'          => 1,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'cortesia'        => $cortesia,
            // 'vip'             => $request['vip'],
            //'procedencia'     => $request['procedencia'],
            //'teleconsulta'    => $request['teleconsulta'],
            //'tc'              => $request['tc'],
            //'paciente_dr' => $request['paciente_dr'],
            //'paciente_dr'     => $paciente_dr,
            //'omni'            => $omni,
        ];

        //Agenda::create($input_historia);
        $nueva_agenda = Agenda::insertGetId($input_historia);

        $input_orden = [
            'id_agenda'       => $nueva_agenda,
            'id_orden'        => $id_examen_orden,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ];

        $id_examen_orden_agenda = Examen_Orden_Agenda::insertGetId($input_orden);
        return ['estado' => 'OK', 'mensaje' => 'Orden creada con éxito'];
    }

    public function privados_store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $orden       = Examen_Orden::find($request->id_orden);
        $fecha_orden = $request->inicio;
        $proc_consul = $request->proc_consul;

        //AGREGAR NUEVA AGENDA
        $estado_cita    = 0;
        $estado         = 1;
        $cortesia       = "NO";
        $doctor_inicial = '4444444444';
        $sala           = '22';
        $espid          = '10';
        $tipo_cita      = 1;
        $id_seguro      = $orden->id_seguro;

        if ($proc_consul == '0') {
            $input_historia = [
                'fechaini'        => $request['inicio'],
                'fechafin'        => $request['fin'],
                'id_paciente'     => $orden->paciente->id,
                'id_doctor1'      => $doctor_inicial,
                'proc_consul'     => $proc_consul,
                'id_empresa'      => $orden->id_empresa,
                'id_sala'         => $sala,
                'espid'           => $espid,
                'tipo_cita'       => $tipo_cita,
                'estado_cita'     => $estado_cita,
                'observaciones'   => $request['observaciones'],
                'est_amb_hos'     => $request['est_amb_hos'],
                'id_seguro'       => $id_seguro,
                'estado'          => 1,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'cortesia'        => $cortesia,
            ];
        }

        if ($proc_consul == '1') {
            $procedimientos = $request['proc'];
            $procedimientop = $procedimientos[0];

            $input_historia = [
                'fechaini'         => $request['inicio'],
                'fechafin'         => $request['fin'],
                'id_paciente'      => $orden->paciente->id,
                'id_doctor1'       => $doctor_inicial,
                'proc_consul'      => $proc_consul,
                'id_empresa'       => $orden->id_empresa,
                'id_sala'          => $sala,
                'espid'            => $espid,
                'tipo_cita'        => $tipo_cita,
                'estado_cita'      => $estado_cita,
                'observaciones'    => $request['observaciones'],
                'est_amb_hos'      => $request['est_amb_hos'],
                'id_seguro'        => $id_seguro,
                'estado'           => 1,
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
                'cortesia'         => $cortesia,
                'id_procedimiento' => $procedimientop,
            ];
        }

        //Agenda::create($input_historia);
        $nueva_agenda = Agenda::insertGetId($input_historia);

        if ($proc_consul == '1') {
            foreach ($procedimientos as $value) {
                if ($procedimientop != $value) {
                    AgendaProcedimiento::create([
                        'id_agenda'        => $nueva_agenda,
                        'id_procedimiento' => $value,
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariocrea'   => $idusuario,
                        'id_usuariomod'    => $idusuario,
                    ]);
                }
            }
        }

        $input_orden = [
            'id_agenda'       => $nueva_agenda,
            'id_orden'        => $orden->id,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];

        $id_examen_orden_agenda = Examen_Orden_Agenda::insertGetId($input_orden);

        $orden->update(['fecha_orden' => $fecha_orden]);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $orden->id,
            'dato1'       => $orden->paciente->id,
            'dato_ant2'   => "AGENDA ORDEN PRIVADA",
            'dato_ant4'   => $fecha_orden,
        ]);

        return ['estado' => 'OK', 'mensaje' => 'Agenda creada con éxito'];
    }

    public function resultados_pendientes(Request $request)
    {

        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $nombres     = null;
        $seguro      = null;

        if ($fecha == null) {
            $fecha = date('Y-m-d');
        }

        $ordenes           = Examen_Orden::where('estado', '1')->where('resultados_estado', '0')->whereBetween('fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->get();
        $sin_certificar    = 0;
        $revision_completa = 0;
        $sin_resultados    = 0;
        foreach ($ordenes as $orden) {

            $detalles   = $orden->detalles;
            $resultados = $orden->resultados;
            //TIENE AL MENOS UN RESULTADO INGRESADO

            if ($orden->resultados()->count() > 0) {

                $cant_par = 0;
                $revision_completa++;
                foreach ($detalles as $d) {

                    if ($d->id_examen == '639') {
                        $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                        if ($xpar->count() > 0) {
                            $cant_par = $cant_par + $xpar->count();
                        } else {
                            $cant_par = $cant_par + 10;
                        }
                    } else {
                        if ($d->examen->no_resultado == '0') {

                            if (count($d->parametros->where('estado', '1')) == '0') {
                                $cant_par++;
                            }
                            if ($d->examen->sexo_n_s == '0') {
                                $parametro_nuevo = $d->parametros->where('sexo', '3')->where('estado', '1');
                            } else {
                                $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo)->where('estado', '1');
                            }
                            $cant_par = $cant_par + $parametro_nuevo->count();
                        }
                    }
                }

                $certificados = 0;
                $cantidad     = 0;

                $certificados = $orden->resultados()->where('certificado', '1')->count();

                if ($certificados >= $cant_par) {
                    $certificados = $cant_par;
                    $orden->update(['resultados_estado' => 1]);
                }
            } else {
                $sin_resultados++;
            }
        }

        $ordenes = Examen_Orden::where('estado', '1')->where('resultados_estado', '0')->whereBetween('fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->get();

        $i = 0;

        $fecha_d = date('Y/m/d');

        Excel::create('Examenes_detalle-' . $fecha_d, function ($excel) use ($ordenes) {

            $excel->sheet('Examenes_detalle', function ($sheet) use ($ordenes) {
                $fecha_d = date('Y/m/d');
                $i       = 5;

                $sheet->mergeCells('A3:G3');

                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);

                $sheet->cells('A1:G3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A3:G4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO DE EXAMENES PENDIENTES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO. ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FACTURA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CLIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cant = 1;
                $total = 0;
                foreach ($ordenes as $orden) {

                    $sheet->cell('A' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $paciente = $orden->paciente;
                    $sheet->cell('B' . $i, function ($cell) use ($paciente) {
                        // manipulate the cel
                        //$cell->setValue(substr($value->created_at,0,10));
                        if ($paciente->apellido2 != "(N/A)") {
                            $vnombre = $paciente->apellido1 . ' ' . $paciente->apellido2;
                        } else {
                            $vnombre = $paciente->apellido1;
                        }

                        if ($paciente->nombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $paciente->nombre1;
                        }
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($orden) {
                        // manipulate the cel
                        $cell->setValue($orden->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($orden) {
                        $cell->setValue(substr($orden->fecha_orden, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($orden) {
                        $cell->setValue($orden->seguro->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $comprobante        = $orden->comprobante;
                    $nombre_factura        = $orden->nombre_factura;
                    $grc_orden_agrupada = Labs_Factura_Agrupada_Orden::where('id_examen_orden', $orden->id)->first();
                    if (!is_null($grc_orden_agrupada)) {
                        $grc_detalle_agrupada = Labs_Factura_Agrupada_Detalle::find($grc_orden_agrupada->id_agrup_det);
                        if (!is_null($grc_orden_agrupada)) {
                            $grc_argupada = Labs_Factura_Agrupada_Cab::find($grc_detalle_agrupada->id_agrup_cab);
                            if (!is_null($grc_argupada)) {
                                $comprobante    = $grc_argupada->comprobante;
                                $nombre_factura = $grc_argupada->nombre_factura;
                            }
                        }
                    }
                    $priv_agrupada = Ct_Ven_Orden::where('orden_venta', $orden->id)->where('tipo', 'VEN-LABS')->first();
                    if (!is_null($priv_agrupada)) {
                        $comprobante    = $priv_agrupada->nro_comprobante;
                        $nombre_factura = $priv_agrupada->nombre_cliente;
                    }
                    $sheet->cell('F' . $i, function ($cell) use ($orden, $comprobante, $nombre_factura) {
                        $cell->setValue($comprobante);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($orden, $comprobante, $nombre_factura) {
                        $cell->setValue($nombre_factura);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i = $i + 1;
                }
            });
        })->export('xlsx');
    }

    public function resultados_pendientes2(Request $request)
    {

        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $nombres     = null;
        $seguro      = null;

        if ($fecha == null) {
            $fecha = date('Y-m-d');
        }

        if ($fecha_hasta == null) {
            $fecha_hasta = date('Y-m-d');
        }

        $ordenes           = Examen_Orden::where('estado', '1')->where('resultados_estado', '0')->whereBetween('fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->get();
        $sin_certificar    = 0;
        $revision_completa = 0;
        $sin_resultados    = 0;
        foreach ($ordenes as $orden) {

            $detalles   = $orden->detalles;
            $resultados = $orden->resultados;
            //TIENE AL MENOS UN RESULTADO INGRESADO

            if ($orden->resultados()->count() > 0) {

                $cant_par = 0;
                $revision_completa++;
                foreach ($detalles as $d) {
                    //cuantos parametros tengo
                    if ($d->id_examen == '639') {
                        $xpar = $resultados->where('id_examen', '639')->where('certificado', '1');


                        if ($xpar->count() > 0) {
                            $cant_par = $cant_par + $xpar->count();
                        } else {
                            $cant_par = $cant_par + 10;
                        }
                    } else {
                        if ($d->examen->no_resultado == '0') {

                            if (count($d->parametros->where('estado', '1')) == '0') {
                                $cant_par++;
                            }
                            if ($d->examen->sexo_n_s == '0') {
                                $parametro_nuevo = $d->parametros->where('sexo', '3')->where('estado', '1');
                            } else {
                                $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo)->where('estado', '1');
                            }
                            $cant_par = $cant_par + $parametro_nuevo->count();
                        }
                    }
                }

                $certificados = 0;
                $cantidad     = 0;

                $certificados = $orden->resultados()->where('certificado', '1')->count();

                if ($certificados >= $cant_par) {
                    $certificados = $cant_par;
                    $orden->update(['resultados_estado' => 1]);
                }
            } else {
                $sin_resultados++;
            }
        }

        $ordenes = Examen_Orden::where('estado', '1')->where('resultados_estado', '0')->whereBetween('fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->get();

        $i = 0;

        $fecha_d = date('Y/m/d');

        $titulos = array("NRO. ORDEN", "PACIENTE", "CÉDULA", "FECHA ORDEN", "FECHA MUESTRA", "SEGURO", "EXAMEN PENDIENTE");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U");

        Excel::create('Examenes_detalle-' . $fecha_d, function ($excel) use ($ordenes, $titulos, $posicion) {

            $excel->sheet('Examenes_detalle', function ($sheet) use ($ordenes, $titulos, $posicion) {
                $fecha_d = date('Y/m/d');
                $i       = 5;

                $sheet->mergeCells('A3:G3');

                $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                $mes = substr($fecha_d, 5, 2);
                $ms = intval($mes) - 1;
                $mes_letra = $meses[$ms];

                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);


                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO DE EXAMENES PENDIENTES ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                });
                $comienzo = 4;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#CCE1A7');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                $cant = 1;
                $total = 0;
                foreach ($ordenes as $orden) {
                    $resultados = $orden->resultados;
                    $arr_detalle = array();
                    $paciente = $orden->paciente;
                    if ($paciente->apellido2 != "(N/A)") {
                        $vnombre = $paciente->apellido1 . ' ' . $paciente->apellido2;
                    } else {
                        $vnombre = $paciente->apellido1;
                    }

                    if ($paciente->nombre2 != "(N/A)") {
                        $vnombre = $vnombre . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2;
                    } else {
                        $vnombre = $vnombre . ' ' . $paciente->nombre1;
                    }

                    foreach ($orden->detalles as $d) {
                        $cant_par = 0;
                        $cant_certificados = 0;

                        //cuantos parametros tengo
                        if ($d->id_examen == '639') {
                            $xpar = $resultados->where('id_examen', '639')->where('certificado', '1');
                            $cant_certificados = $xpar->count();
                            $cant_par = $d->parametros->where('estado', '1')->count();
                        } else {
                            if ($d->examen->no_resultado == '0') {

                                if (count($d->parametros->where('estado', '1')) == '0') {
                                    $cant_par++;
                                }
                                if ($d->examen->sexo_n_s == '0') {
                                    $parametro_nuevo = $d->parametros->where('sexo', '3')->where('estado', '1');
                                } else {
                                    $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo)->where('estado', '1');
                                }
                                $cant_par = $parametro_nuevo->count();
                                if ($cant_par > 0) {
                                    foreach ($parametro_nuevo as $pnuevo) {
                                        $xpar = $resultados->where('id_parametro', $pnuevo->id)->where('certificado', '1');
                                        $cant_certificados = $xpar->count();
                                    }
                                }
                            }
                        }

                        if ($cant_certificados != $cant_par) {
                            $arr = [
                                'id' => $orden->id,
                                'id_paciente' => $orden->id_paciente,
                                'paciente' => $vnombre,
                                'fecha_orden' => substr($orden->fecha_orden, 0, 10),
                                'fecha_muestra' => substr($orden->toma_muestra, 0, 10),
                                'detalle' => $d->examen->nombre,
                                'seguro' => $orden->seguro->nombre,
                            ];
                            array_push($arr_detalle, $arr);
                        }
                    }
                    // dd($arr_detalle);
                    
                    foreach ($arr_detalle as $detalle) {
                        $datos_excel = array();
                        array_push($datos_excel, $detalle['id'], $detalle['paciente'], $detalle['id_paciente'], substr($detalle['fecha_orden'], 0, 10), substr($detalle['fecha_muestra'], 0, 10), $detalle['seguro'], $detalle['detalle']);

                        for ($i = 0; $i < count($datos_excel); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                    }
                }
            });
        })->export('xlsx');
    }

    public function toma_muestra($id)
    {

        $orden = Examen_Orden::find($id);

        $orden->update(
            ['toma_muestra' => date('Y-m-d H:i:s')]
        );
    }

    public function examenes_pendientes($id_orden)
    {

        $orden = Examen_Orden::find($id_orden);

        return view('laboratorio/orden/examenes_pendientes', ['orden' => $orden]);
    }

    public function facturacion_gastroclinica($id_orden)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $empresa = DB::table('empresa as e')->where('e.estado', '1')->where('e.prioridad', '1')->first();

        $orden          = Examen_Orden::find($id_orden);
        $paciente       = $orden->paciente;
        $agenda_orden   = Examen_Orden_Agenda::where('id_orden', $id_orden)->first();
        $estado_cita    = 0;
        $estado         = 1;
        $cortesia       = "NO";
        $proc_consul    = 0;
        $doctor_inicial = '4444444444';
        $sala           = '22';
        $espid          = '10';
        $tipo_cita      = 1;
        $id_seguro      = $orden->id_seguro;
        $id_empresa     = $empresa->id;
        if (is_null($agenda_orden)) {
            //AGREGAR NUEVA AGENDA

            $input_historia = [
                'fechaini'        => date('Y-m-d H:i:s'),
                'fechafin'        => date('Y-m-d H:i:s'),
                'id_paciente'     => $paciente->id,
                'id_doctor1'      => $doctor_inicial,
                'proc_consul'     => $proc_consul,
                'id_empresa'      => $id_empresa,
                'id_sala'         => $sala,
                'espid'           => $espid,
                'tipo_cita'       => $tipo_cita,
                'estado_cita'     => $estado_cita,
                'observaciones'   => null,
                'est_amb_hos'     => null,
                'id_seguro'       => $id_seguro,
                'estado'          => 1,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'cortesia'        => $cortesia,
            ];

            //Agenda::create($input_historia);
            $id_agenda = Agenda::insertGetId($input_historia);

            $input_orden = [
                'id_agenda'       => $id_agenda,
                'id_orden'        => $id_orden,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];

            $id_examen_orden_agenda = Examen_Orden_Agenda::insertGetId($input_orden);
        } else {

            $id_agenda = $agenda_orden->id_agenda;
        }

        $venta = Ct_Orden_Venta::find($orden->venta_gastro);

        $valor_gastro = 0;

        foreach ($orden->detalles as $detalle) {
            $valor_gastro += $detalle->valor_2;
        }

        $total_valor = $orden->total_valor;

        if ($valor_gastro > 0) {
            $total_valor = $valor_gastro;
        }




        if (is_null($venta)) {

            $id_orden_venta = Ct_Orden_Venta::insertGetId([
                'id_agenda'           => $id_agenda,
                'id_empresa'          => $id_empresa,
                'fecha_emision'       => date('Y-m-d H:i:s'),
                'oda'                 => 0,
                'numero_oda'          => 0,
                'valor_oda'           => 0,
                'id_seguro'           => $id_seguro,
                'id_nivel'            => $orden->id_nivel,
                'tipo_identificacion' => $orden->tipo_documento,
                'identificacion'      => $orden->cedula_factura,
                'razon_social'        => $orden->nombre_factura,
                'ciudad'              => $orden->ciudad_factura,
                'direccion'           => $orden->direccion_factura,
                'telefono'            => $orden->telefono_factura,
                'total_sin_tarjeta'   => $total_valor,
                'observacion'         => null,
                'caja'                => 'LABORATORIO',
                'total'               => $total_valor,
                'email'               => $orden->email_factura,
                'iva'                 => 0,
                'subtotal_0'          => $total_valor,
                'subtotal_12'         => 0,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ]);

            Ct_Orden_Venta_Detalle::create([
                'id_orden'        => $id_orden_venta,
                'cod_prod'        => 'LABS',
                'descripcion'     => 'EXAMENES DE LABORATORIO',
                'id_producto'     => '333',
                'cantidad'        => 1,
                'precio'          => $total_valor,
                'total'           => $total_valor,
                'tipo_oda'        => '%',
                'p_oda'           => 100,
                'valor_oda'       => 0,
                'tipo_dcto'       => '%',
                'p_dcto'          => 0,
                'descuento'       => 0,
                'iva'             => 0,
                'valor_iva'       => 0,
                'ident_paquete'   => null,
                'valor_deducible' => 0,
                'valor_oda_hum'   => 0,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'nombre_producto' => 'EXAMENES DE LABORATORIO',
            ]);
        } else {

            $id_orden_venta = $venta->id;
        }

        $orden->update(['venta_gastro' => $id_orden_venta]);

        $orden_venta         = Ct_Orden_Venta::find($id_orden_venta);
        $orden_venta_detalle = $orden_venta->detalles->first();
        $pagos               = Ct_Tipo_Pago::where('estado', '1')->orderby('nombre', 'asc')->get();
        $tarjetas            = Ct_Tipo_Tarjeta::where('estado', '1')->orderby('nombre', 'asc')->get();
        $bancos              = Ct_Bancos::where('estado', '1')->orderby('nombre', 'asc')->get();

        return view('laboratorio/orden/forma_pago_gastro', ['orden_venta' => $orden_venta, 'pagos' => $pagos, 'tarjetas' => $tarjetas, 'bancos' => $bancos, 'orden' => $orden, 'orden_venta_detalle' => $orden_venta_detalle]);
    }

    public function labs_validar_mail($id_orden)
    {

        $orden       = Examen_Orden::find($id_orden);
        $paciente    = $orden->paciente;
        $id_paciente = $paciente->id;
        $grupo_fam   = Labs_Grupo_Familiar::find($id_paciente);
        if (!is_null($grupo_fam)) {
            $user_aso = User::find($grupo_fam->id_usuario);
        } else {
            $user_aso = User::find($paciente->id_usuario);
        }

        return view('laboratorio/orden/modal_correo', ['paciente' => $paciente, 'user_aso' => $user_aso, 'id_exa_orden' => $id_orden, 'orden' => $orden]);
    }

    public function log_toma_muestras($id_orden)
    {

        $orden    = Examen_Orden::find($id_orden);
        $muestras = $orden->toma_muestras;

        return view('laboratorio.orden.modal_muestras', ['muestras' => $muestras, 'orden' => $orden]);
    }

    public function cambio_resultado(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_orden = $request->id_orden;
        $fecha_resultado = $request->fecha_resultado;

        $orden    = Examen_Orden::find($id_orden);


        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $orden->id,
            'dato1'       => $orden->id_paciente,
            'dato_ant2'   => $orden->fecha_convenios,
            'dato_ant4'   => $fecha_resultado,
        ]);

        $orden->update(['fecha_convenios' => $fecha_resultado]);

        return "ok";
    }

    public function desligar_correo($id_paciente, $correo)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $usuario   = Auth::user()->id;

        $desligar = Labs_Grupo_Familiar::find($id_paciente);
        if (!is_null($desligar)) {
            $desligar->delete();

            Log_usuario::create([
                'descripcion' => "DESLIGAR CORREO DE PACIENTE DE PACIENTE - ORDENES EXAMENES LAB",
                'dato1'       => $correo,
                'dato_ant1'   => "Cedula del correo original: " . $desligar->id,
                'dato_ant2'   => "Cedula del correo nuevo: " . $desligar->id_usuario,
                'ip_usuario'  => $ip_cliente,
                'id_usuario'  => $usuario,
            ]);
            return "1";
        }
        return "0";
    }

    public function subir_5pct_abril_2022()
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $usuario   = Auth::user()->id;

        $examenes = Examen::all();

        foreach ($examenes as $examen) {

            $valor_ant = $examen->valor;

            $valor_new = $valor_ant + $valor_ant * 0.05;
            $valor_new = round($valor_new, 2);

            $examen->update(['valor' => $valor_new]);

            Log_usuario::create([
                'descripcion' => "ACTUALIZACION VALORES LABS ABRIL 2022",
                'dato1'       => $examen->nombre,
                'dato_ant1'   => $valor_ant,
                'dato_ant2'   => $valor_new,
                'ip_usuario'  => $ip_cliente,
                'id_usuario'  => $usuario,
            ]);
        }
    }

    public function modal_pendiente_pago()
    {
        return view('laboratorio/orden/modal_pendiente_pago');
    }
    public function agregar_valor(Request $request)
    {
        $lista_abono = Examen_Comprobante_Ingreso::where('id_examen_detalle_pago', $request['id'])->get();
        $id_examen_orden = Examen_Detalle_Forma_Pago::where('id', $request['id'])->first();
        $metodo_de_pago =  Ct_Tipo_Pago::where('estado', '1')->whereNotIn('id', [7])->orderby('nombre', 'asc')->get();
        $valor_restante = 0;
        if (!is_null($id_examen_orden->valor_adelanto)) {
            $valor_restante = $id_examen_orden->valor - $id_examen_orden->valor_adelanto;
        } else {
            $valor_restante = $id_examen_orden->valor;
        }

        $lista_banco  = Ct_Bancos::where('estado', 1)->get();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::where('estado', 1)->get();
        return view('laboratorio/orden/vista_agregar_valor', ['lista_abono' => $lista_abono, 'lista_banco' => $lista_banco, 'tipo_tarjeta' => $tipo_tarjeta, 'id_examen_orden' => $id_examen_orden, 'metodo_de_pago' => $metodo_de_pago, 'valor_restante' => $valor_restante]);
    }
    public function guardarvalor(Request $request)
    {
        $usuario   = Auth::user()->id;
        $igualdad = Examen_Detalle_Forma_Pago::where('id', $request['id_examen_orden'])->first();
        $examen_Comprobante_ingreso = new Examen_Comprobante_Ingreso();
        try {
            //save  
            $valor = $request['abonar'] + $igualdad->valor_adelanto;
            $igualdad->valor_adelanto =  $valor;
            $igualdad->save();
            //
            $examen_Comprobante_ingreso->valor =  $valor;
            $examen_Comprobante_ingreso->id_examen_detalle_pago = $igualdad->id;
            $examen_Comprobante_ingreso->id_forma_pago = $request['forma_pago'];
            $examen_Comprobante_ingreso->valor = $request['abonar'];
            $examen_Comprobante_ingreso->fecha = $request['fecha'];
            $examen_Comprobante_ingreso->id_usuariocrea = $usuario;
            //
            $examen_Comprobante_ingreso->id_tipo_tarjeta = $request['tarjeta'];
            $examen_Comprobante_ingreso->numero = $request['numero'];
            $examen_Comprobante_ingreso->id_banco = $request['banco'];
            $examen_Comprobante_ingreso->cuenta = $request['cuenta'];
            $examen_Comprobante_ingreso->comprobante_ingreso = 0;            //
            $examen_Comprobante_ingreso->save();
            //
            $v = Examen_Detalle_Forma_Pago::where('id', $request['id_examen_orden'])->first();
            if ($v->valor_adelanto == $v->valor) {
                DB::table('examen_detalle_forma_pago')
                    ->where('id_examen_orden', $request['id_examen_orden'])
                    ->update(array('aplicado' => 1));
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => "Porfavor contacte sistemas"];
        }
        return ['status' => 'ok', 'msj' => "Guardado Correctamente"];
    }
    public function modal_buscar(Request $request)
    {
        $nombres = $request['nombres'];
        $query = DB::table('paciente as pac')
            ->join('examen_orden', 'pac.id', 'examen_orden.id_paciente')
            ->join('examen_detalle_forma_pago as efp', 'examen_orden.id', 'efp.id_examen_orden')
            ->where('efp.aplicado', null)
            ->where('examen_orden.comprobante', '<>', null)
            ->where('efp.id_tipo_pago', '7');
        if (!is_null($request['cedula'])) {
            $query = $query->where('pac.id', $request['cedula']);
        }
        if (!is_null($request['nombres'])) {
            $query =  $query->where(function ($jq1) use ($nombres) {
                $jq1->whereRaw('CONCAT(pac.apellido1,pac.apellido2,pac.nombre1,pac.nombre2) LIKE ?', '%' . $nombres . '%')
                    ->orWhereRaw('CONCAT(pac.nombre1,pac.nombre2,pac.apellido1,pac.apellido2) LIKE ?', '%' . $nombres . '%')
                    ->orwhereraw('CONCAT(pac.nombre1,pac.apellido1,pac.apellido2) LIKE ?', '%' . $nombres . '%')
                    ->orWhereRaw('CONCAT(pac.apellido1,pac.apellido2,pac.nombre1) LIKE ?', '%' . $nombres . '%');
            });
        }
        $query = $query->select('examen_orden.comprobante', 'efp.valor_adelanto', 'pac.id as cedula', 'efp.id', 'efp.valor', 'efp.fecha', DB::raw("CONCAT(pac.nombre1,' ',pac.apellido1,' ',pac.apellido2) AS nombre"))->get();
        //dd($query);
        return view('laboratorio/orden/tabla_pendiente', ['query' => $query]);
    }
    public function nuevo_combrobante()
    {
        $examen_comprobante_ingreso = DB::table('examen_comprobante_ingreso')
            ->join('examen_detalle_forma_pago', 'examen_comprobante_ingreso.id_examen_detalle_pago', 'examen_detalle_forma_pago.id')
            ->join('examen_orden', 'examen_detalle_forma_pago.id_examen_orden', 'examen_orden.id')
            ->join('ct_ventas', 'examen_orden.comprobante', 'ct_ventas.nro_comprobante')
            ->where('examen_comprobante_ingreso.comprobante_ingreso', 0)
            ->select("examen_comprobante_ingreso.*", "ct_ventas.id", "examen_orden.id_paciente", "examen_comprobante_ingreso.id as nuevo_id")
            ->get();
        $count = $examen_comprobante_ingreso->count();
        return view('laboratorio/orden/modal_comprobante', ['examen_comprobante_ingreso' => $examen_comprobante_ingreso, 'count' => $count]);
    }
    public function llenar_campos(Request $request)
    {
        $query = DB::table('examen_comprobante_ingreso as ec')
            ->join('examen_detalle_forma_pago as ef', 'ec.id_examen_detalle_pago', 'ef.id')
            ->join('examen_orden as eo', 'ef.id_examen_orden', 'eo.id')
            ->join('ct_ventas', 'eo.comprobante', 'ct_ventas.nro_comprobante')
            ->where('ec.id', $request['id'])
            ->select('ef.valor', 'ec.fecha', 'eo.id_paciente', "ct_ventas.id")
            ->get();
        $tot = Examen_Comprobante_Ingreso::where('id', $request['id'])->get();
        return json_encode(['query' => $query, 'tot' => $tot]);
    }
    private function recalcular_membresia($id_orden)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $orden = Examen_Orden::find($id_orden);
        if (!is_null($orden)) {
            if ($orden->seguro->tipo == '2') {
                $membresia_controller = new MembresiasLabsController;
                $res = $membresia_controller->buscar_membresia($orden->id_paciente);
                //$res = MembresiasLabsController::buscar_membresia($orden->id_paciente);
                $total = $orden->valor;
                $estado = $res['estado'];

                $detalle_final = null;
                if ($estado == 'ok') {
                    $id_plan = $res['id'];
                    $nomble_plan = $res['nombre'];
                    if (count($res['detalles']) == 2) {
                        $detalles_membresia = $res['detalles'];
                        $detalle_1 = $detalles_membresia[1];
                        if ($detalle_1['minimo_requerido'] < $total) {
                            $detalle_final = $detalle_1;
                        } else {
                            $detalle_0 = $detalles_membresia[0];
                            if ($detalle_0['minimo_requerido'] < $total) {
                                $detalle_final = $detalle_0;
                            }
                        }
                        //dd($detalle_final);    
                    }
                    /*$membresia = Membresia::find($id_plan);
                    $detalle_membresia   = $membresia->detalles()->where('minimo_requerido', '<', $total)->orderBy('minimo_requerido', 'desc')->first();*/
                    if (!is_null($detalle_final)) {
                        $descuento_p = $detalle_final['porcentaje_descuento'];
                        $cantidad = 0;
                        /////////////////////////
                        $detalles = $orden->detalles;
                        foreach ($detalles as $detalle) {
                            $cantidad = $cantidad + 1;
                            $examen   = Examen::find($detalle->id_examen);
                            $valor    = $examen->valor;
                            $cubre    = 'NO';
                            $valor_descuento = $descuento_p * $valor / 100;
                            $valor_descuento = round($valor_descuento, 4);
                            $input_det = [
                                'valor'           => $valor,
                                'cubre'           => $cubre,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariomod'   => $idusuario,
                                'valor_descuento' => $valor_descuento,
                                'p_descuento'     => $descuento_p,
                                'cobrar_pac_pct'  => 100,
                                'valor_con_oda'   => 0,
                            ];
                            $detalle->update($input_det);
                        }
                        $total           = $orden->detalles->sum('valor');
                        $cantidad        = $orden->detalles->count();
                        $total           = round($total, 2);
                        $descuento_total = $orden->detalles->sum('valor_descuento');
                        $valor_con_oda = 0;
                        $total_con_oda = 0;
                        $subtotal_pagar = $total - $descuento_total;
                        $recargo_p      = 0;
                        $recargo_valor = 0;
                        $valor_total = $subtotal_pagar + $recargo_valor;
                        $valor_total = round($valor_total, 4);
                        //ACTUALIZAR ORDEN
                        $input_ex = [
                            'motivo_descuento' => 'MEMBRESIA ACTIVA: ' . $nomble_plan . ' ' . $detalle_final['nombre'],
                            'descuento_p'      => $descuento_p,
                            'descuento_valor'  => $descuento_total,
                            'recargo_p'        => $recargo_p,
                            'recargo_valor'    => $recargo_valor,
                            'total_valor'      => $valor_total,
                            'id_seguro'        => '1',
                            'cantidad'         => $cantidad,
                            'valor'            => $total,
                            'ip_modificacion'  => $ip_cliente,
                            'id_usuariomod'    => $idusuario,
                            'cobrar_pac_pct'   => 100,
                            'valor_con_oda'    => 0,
                            'total_con_oda'    => 0,
                            'membresia_id'          => $detalle_final['membresia_id'],
                            'membresia_detalle_id'  => $detalle_final['id'],
                        ];
                        $orden->update($input_ex);
                        /////////////////////////
                        return ['estado' => 'OK', 'motivo' => 'MEMBRESIA ACTIVA: ' . $nomble_plan . ' ' . $detalle_final['nombre'], 'descuento_pct' => $descuento_p];
                    }
                }
                return ['estado' => 'NO', 'motivo' => '', 'descuento_pct' => ''];
            }
        }
    }
    public function query_examenes($id)
    {
        $orden = Examen_Orden::find($id);
        $imprimir = $this->imprimirCod($orden->id);
        return view('laboratorio/orden/modal_query', ['orden' => $orden->id, 'arrayU' => $imprimir['arrayU'], 'arrayG' => $imprimir['arrayG']]);
    }
    public function imprimirCod($id_examen_orden)
    {
        $detalle = Examen_Detalle::where('id_examen_orden', $id_examen_orden)->get();
        $examenOrden = Examen_Orden::where('id', $id_examen_orden)->first();
        $arrayU = [];
        $arrayG = [];
        $arrayUnico = [];
        $cont = 0;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        foreach ($detalle as $keyl => &$value) {
            if ($value->id_examen == '1225' && $examenOrden->seguro->tipo == 0) {
            } else {
                if (!is_null($value->examen->id_labs_tubo)) {
                    $nombre = Labs_Tipo_Tubo::where('id', $value->examen->id_labs_tubo)->first();
                    $arrayUnico = [
                        'id_examen' => $value->id_examen,
                        'nombre' => $nombre->nombre,
                        'nombres'  => $examenOrden->paciente->nombre1 . ' ' . $examenOrden->paciente->nombre2 . ' ' . $examenOrden->paciente->apellido1 . ' ' . $examenOrden->paciente->apellido2,
                        'cedula' => $examenOrden->paciente->id,
                    ];
                    if ($value->examen->indice_tubos == 'U') {
                        $arrayU[$nombre->nombre] = [
                            'id_examen_orden' => $id_examen_orden,
                            'tipo' => 'U',
                            'id_examen' => $value->id_examen,
                            'nombre' => $nombre->nombre,
                            'cantidad' => $value->examen->cantidad_tubos,
                            'nombres'  => $examenOrden->paciente->nombre1 . ' ' . $examenOrden->paciente->nombre2 . ' ' . $examenOrden->paciente->apellido1 . ' ' . $examenOrden->paciente->apellido2,
                            'cedula' => $examenOrden->paciente->id,
                        ];
                    } else {
                        foreach ($arrayG as $val) {
                            if ($nombre->nombre == $val['nombre']) {
                                if ($value->examen->cantidad_tubos > $val['cantidad']) {
                                    $val['cantidad'] = $value->examen->cantidad_tubos;
                                }
                            }
                        }
                        $arrayG[$nombre->nombre] = [
                            'id_examen_orden' => $id_examen_orden,
                            'tipo' => 'G',
                            'id_examen' => $value->id_examen,
                            'nombre' => $nombre->nombre,
                            'cantidad' => $value->examen->cantidad_tubos,
                            'nombres'  => $examenOrden->paciente->nombre1 . ' ' . $examenOrden->paciente->nombre2 . ' ' . $examenOrden->paciente->apellido1 . ' ' . $examenOrden->paciente->apellido2,
                            'cedula' => $examenOrden->paciente->id,
                        ];
                    }
                }
            }
        }
        Examen_Orden_Toma_Muestra::create([
            'id_examen_orden' => $id_examen_orden,
            'toma_muestra' => date('Y-m-d h:m:s'),
            'estado' => 1,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);
        return ['arrayU' => $arrayU, 'arrayG' => $arrayG, 'arrayUnico' => $arrayUnico];
    }

    /*public function update_estado_email_paciente(Request $request)
{
$ip_cliente = $_SERVER["REMOTE_ADDR"];
$ausuario   = Auth::user()->id;

$id_exa      = $request['id_exa_orden'];
$id_paciente = $request['id_paciente'];
$id_usuario  = $request['id_usuario'];

$mensajes = [
'email.unique'   => 'El Email ya se encuentra registrado.',
'email.required' => 'Ingrese el mail.',
'email.max'      => 'El Email no puede ser mayor a :max caracteres.',
'email.email'    => 'Mail ingresado con formato incorrecto.',
];

$constraints = [
//'email' => 'required|email|max:191|unique:users,email,' . $id_usuario . ',id',
'email' => 'required|email|max:191',
];

$this->validate($request, $constraints, $mensajes);

$paciente = Paciente::find($id_paciente);

//Nuevo Cambio
//Principal
if ($id_paciente == $id_usuario) {
$usuario = $this->recupera_mail($request['email']);
if ($usuario != 'no') {
$gr_fam = Labs_Grupo_Familiar::find($id_paciente);
if (is_null($gr_fam)) {
// crear grupo familiar return $usuario;
if ($id_paciente != $usuario->id) {
$arr_gr = [
'id'              => $id_paciente,
'id_usuario'      => $usuario->id,
'ip_creacion'     => $ip_cliente,
'ip_modificacion' => $ip_cliente,
'id_usuariocrea'  => $ausuario,
'id_usuariomod'   => $ausuario,
];
Labs_Grupo_Familiar::create($arr_gr);
}
}
$user   = $usuario;
$correo = $user->email;

$nombre_paciente = $paciente->nombre1 . " ";

if ($paciente->nombre2 != '(N/A)') {
$nombre_paciente = $nombre_paciente . $paciente->nombre2 . " ";
}

$nombre_paciente = $nombre_paciente . $paciente->apellido1 . " ";
if ($paciente->apellido2 != '(N/A)') {
$nombre_paciente = $nombre_paciente . $paciente->apellido2 . " ";
}

} else {
$input1 = [
'email'    => $request['email'],
'password' => bcrypt($id_paciente),
];

User::where('id', $id_usuario)->update($input1);
$user   = User::find($id_usuario);
$correo = $user->email;

$nombre_paciente = $paciente->nombre1 . " ";

if ($paciente->nombre2 != '(N/A)') {
$nombre_paciente = $nombre_paciente . $paciente->nombre2 . " ";
}

$nombre_paciente = $nombre_paciente . $paciente->apellido1 . " ";
if ($paciente->apellido2 != '(N/A)') {
$nombre_paciente = $nombre_paciente . $paciente->apellido2 . " ";
}

}
//No principal
} else if ($id_paciente != $id_usuario) {

$input1 = [
'email'    => $request['email'],
'password' => bcrypt($id_usuario),
];

//User::where('id', $id_usuario)->update($input1);
$user   = User::find($id_usuario);
$correo = $user->email;

$nombre_paciente = $paciente->nombre1 . " ";

if ($paciente->nombre2 != '(N/A)') {
$nombre_paciente = $nombre_paciente . $paciente->nombre2 . " ";
}

$nombre_paciente = $nombre_paciente . $paciente->apellido1 . " ";
if ($paciente->apellido2 != '(N/A)') {
$nombre_paciente = $nombre_paciente . $paciente->apellido2 . " ";
}

}

$input2 = [
'estado_pago' => '1',
];

Examen_Orden::where('id', $id_exa)->update($input2);

//Envio de Correo al Paciente
$msj_labs = array("nombre_paciente" => $nombre_paciente, "user" => $user, "paciente" => $paciente);

if ($paciente->mail_primera_vez == '0') {

$input4 = [

'mail_primera_vez' => '1',
];

Paciente::where('id', $id_paciente)->update($input4);

}

Mail::send('mails.labs', $msj_labs, function ($msj) use ($correo) {
$msj->from("noreply@labs.ec", "HUMANLABS");
$msj->subject('Resultados de Exámenes de Laboratorio');
$msj->to($correo);
$msj->bcc('torbi10@hotmail.com');

});

return "okay";

}*/
    // ruta masivo_medmisalud
    public function masivo_medmisalud()
    {
        $ordenes1 = Examen_Orden::where('estado', 1)->where('id_nivel', '105')->get();
        $ordenes2 = Examen_Orden::where('estado', 1)->where('id_nivel', '106')->get();
        dd($ordenes1, $ordenes2);
    }
    public function modal_app_orden()
    {
        $consulta = Examen_Orden::where("estado", -1)->where('doctor_txt', 'COTIZACION GENERADA EN APP MÒVIL')->whereDate('created_at', Carbon::today())->get();
        $array = [];
        for ($i = 0; $i < count($consulta); $i++) {
            $array[] = ['correo' => $consulta[$i]->paciente->usuario->email, 'telefono' => $consulta[$i]->paciente->telefono1 == null ? '' : $consulta[$i]->paciente->telefono1, 'telefono1' => $consulta[$i]->paciente->telefono2 == null ? '' : $consulta[$i]->paciente->telefono2, 'telefono2' => $consulta[$i]->paciente->telefono3 == null ? '' : $consulta[$i]->paciente->telefono3, 'nombre_paciente' => $consulta[$i]->paciente->nombre1 . ' ' . $consulta[$i]->paciente->nombre2 . ' ' . $consulta[$i]->paciente->apellido1 . ' ' . $consulta[$i]->paciente->apellido2, 'fecha_created_at' => date('Y-m-d', strtotime($consulta[$i]->created_at)), 'id' => $consulta[$i]->id, 'domi' => $consulta[$i]->pres_dom  == 1 ? 'Domicilio' : 'Presencial'];
        }
        return ["datos" => $array];
    }

    public function presentacion_2022()
    {

        return view('laboratorio/orden/presentacion_2022');
    }

    public function comentarios_ordenes($id_orden){

        $orden = Examen_Orden::find($id_orden);
        
        return view('laboratorio.orden.comentarios_ordenes', ['orden' => $orden]);
    }

    public function seguimiento_listar($id_orden)
    {
        $orden = Examen_Orden::with('paciente')->find($id_orden);
        if (is_null($orden)) {
            return response()->json([
                'status' => 'error',
                'msj' => 'Orden no encontrada',
            ], 404);
        }

        $seguimientos = Examen_Orden_Seguimiento::where('id_examen_orden', $id_orden)
            ->where('estado_registro', '1')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($seguimiento) {
                $usuario = User::find($seguimiento->id_usuariocrea);
                return [
                    'id' => $seguimiento->id,
                    'estado_seguimiento' => $seguimiento->estado_seguimiento,
                    'observacion' => $seguimiento->observacion,
                    'seguimiento_final' => $seguimiento->seguimiento_final,
                    'created_at' => $seguimiento->created_at,
                    'usuario' => !is_null($usuario) ? trim($usuario->nombre1 . ' ' . $usuario->apellido1) : '',
                ];
            });

        return response()->json([
            'status' => 'success',
            'orden' => [
                'id' => $orden->id,
                'paciente' => !is_null($orden->paciente)
                    ? trim($orden->paciente->apellido1 . ' ' . $orden->paciente->apellido2 . ' ' . $orden->paciente->nombre1 . ' ' . $orden->paciente->nombre2)
                    : '',
            ],
            'seguimientos' => $seguimientos,
        ]);
    }

    public function seguimiento_store(Request $request)
    {
        $id_orden = $request->id_examen_orden;
        $estado = $request->estado_seguimiento;
        $observacion = trim((string) $request->observacion);
        $seguimiento_final = $request->seguimiento_final == '1' ? '1' : '0';

        $estados_validos = [
            'pendiente_llamar',
            'contactado',
            'confirmo_asistencia',
            'no_vendra',
        ];

        if (!in_array($estado, $estados_validos)) {
            return response()->json([
                'status' => 'error',
                'msj' => 'Seleccione un estado de seguimiento valido',
            ], 422);
        }

        if ($observacion == '') {
            return response()->json([
                'status' => 'error',
                'msj' => 'Ingrese la observacion del paciente',
            ], 422);
        }

        $orden = Examen_Orden::find($id_orden);
        if (is_null($orden)) {
            return response()->json([
                'status' => 'error',
                'msj' => 'Orden no encontrada',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $id_usuario = Auth::user()->id;
            $ip_cliente = $_SERVER['REMOTE_ADDR'];

            $seguimiento = Examen_Orden_Seguimiento::create([
                'id_examen_orden' => $orden->id,
                'estado_seguimiento' => $estado,
                'observacion' => $observacion,
                'proxima_llamada' => null,
                'seguimiento_final' => $seguimiento_final,
                'estado_registro' => '1',
                'id_usuariocrea' => $id_usuario,
                'id_usuariomod' => $id_usuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);

            Log_usuario::create([
                'descripcion' => $observacion,
                'dato_ant1' => 'SEGUIMIENTO CREADO EN ORDEN DE LABORATORIO',
                'dato1' => "Orden: {$orden->id} Estado: {$estado}",
                'id_usuario' => $id_usuario,
                'ip_usuario' => $ip_cliente,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'ok' => true,
                'mensaje' => 'Seguimiento guardado correctamente',
                'msj' => 'Seguimiento guardado correctamente',
                'seguimiento' => $seguimiento,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'msj' => 'Error al guardar el seguimiento',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function comentarios_guardar(Request $request){
        $observacion = '';
        if(!is_null($request->observacion)){
            $observacion = $request->observacion;
        }
        DB::beginTransaction();
        try{
            $id_usuario = Auth::user()->id; 
            $ip_cliente = $_SERVER['REMOTE_ADDR'];
            $orden = Examen_Orden::find($request->id_orden);
            $orden->comentario = $observacion;
            //$orden->id_usuariomod = $id_usuario;
            //$orden->ip_modificacion = $ip_cliente;
            $orden->save();
            Log_usuario::create([
                'descripcion'   => $observacion,
                'dato_ant1'     => 'COMENTARIO CREADO EN LA ORDEN LABS',
                'dato1'         => "Orden: {$orden->id}",
                'id_usuario'    => $id_usuario,
                'ip_usuario'    => $ip_cliente,
            ]);
            DB::commit();
            return ["status" => "success", "msj" => "Guardado correctamente"];
        }catch(\Exception $e){
            DB::rollback();
            return ["status" => "error", "msj" => "Error al guardar", "error" => $e->getMessage()];
        }
    }
    public function modallabs(Request $request)
    {

        $norden = $request['id_orden'];

        $empresas = Empresa::where('estado', '1')->where('prioridad', '1')->get();
        $consentimien = Formato_consentimiento::where('estado', 1)->where('check_labs', 1)->get();
        $orden = Examen_Orden::find($request['id_orden']);

        return view('laboratorio/orden/modallabs', compact('consentimien', 'empresas', 'orden'));
    }

    public function descargar_pdf(Request $request)
    {

        $variable   = $request->id;
        $formatoid  = $request->nombre_procedimiento;
        $id_empresaO = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('prioridad_labs', 1)->first();
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $date       = Date('Y-m-d');
        $paciente   = Examen_Orden::find($request['id_orden']);

        if (is_null($paciente)) {
            return response()->view('errors.404');
        }

        if ($formatoid === 'citogenetico_1564') {
            $usuario = Auth::user();
            $view = \View::make(
                'hc_admision.consentimientos.formulario_1564_citogenetico',
                compact('date', 'paciente', 'empresa', 'usuario')
            )->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('a4', 'portrait');

            return $pdf->stream('Formulario 1564 - Orden de Examen Citogenetico - Orden ' . $paciente->id . '.pdf');
        }

        $formato_nombre = Formato_consentimiento::find($request->nombre_procedimiento);
        Agenda_consentimiento::create([
            'id_paciente'    => $request['cedula'],
            'cie_10'         => $request['nombre_diagnostico'],
            'formato'        => $request['nombre_procedimiento'],
            'ip_creacion'    => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'  => $idusuario,
        ]);
        $consentimiento = Agenda_consentimiento::where('id_paciente', $variable)->first();
        //dd($consentimiento);
        $nombre = DB::table('formato_consentimiento')
            ->join('agenda_consentimiento', 'agenda_consentimiento.formato', '=', 'formato_consentimiento.id')
            ->where('agenda_consentimiento.formato', $formato_nombre->id)
            ->select('formato_consentimiento.nombre')
            ->first();
 


        $view = \View::make('hc_admision.consentimientos.' . $formato_nombre->pdf, compact('date', 'paciente', 'consentimiento', 'nombre', 'empresa'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'portrait');
        return $pdf->stream('Consentimiento informado prueba de VIH Vers. 0.2' . '.pdf');
    }
    public function resultado_especial( $id )
    {

              //$i es la maquina 1 BIOMETRIA -- 2 BIOQUIMICA -- 0 manual
        if ($this->rol_control()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $orden      = Examen_Orden::find($id);
        $detalles   = $orden->detalles;//dd($detalles);
       
        $resultados = $orden->resultados;
            //$agrupador = Examen_Agrupador_labs::all();
        $agrupador  = Examen_Agrupador_labs::orderBy('secuencia')->get();

        $parametros = Examen_parametro::where('estado', '1')->orderBy('orden')->get();

        return view('laboratorio.orden.resultados_esp', ['orden' => $orden, 'agrupador' => $agrupador, 'parametros' => $parametros, 'detalles' => $detalles, 'resultados' => $resultados]);
    }
    public function guardar_resultado_esp(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        //dd($request->all());   
        $tipo = substr($request->nombre,0,7);
        $elemento = substr($request->nombre,8); 
        $id_orden = $request->id;
        
        $orden = Examen_Orden::find($id_orden);

        if($tipo == 'par_res'){
            $parametro = Examen_Parametro::find($elemento);
            $resultado = $orden->resultados->where('id_parametro',$parametro->id)->first();
            if(is_null($resultado)){
                $input_det = [
                    'id_orden'        => $id_orden,
                    'id_parametro'    => $parametro->id,
                    'valor'           => $request->valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
            
                ];
        
                Examen_resultado::create($input_det);
            }else{
                $resultado->update([
                    'valor' => $request->valor,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ]);
            }

            $resto_param = Examen_Parametro::where('id_examen',$parametro->id_examen)->where('id_parametro_ref',$parametro->id)->get();
            foreach($resto_param as $rp){

                $resultado = $orden->resultados->where('id_parametro',$rp->id)->first();
                if(is_null($resultado)){
                    $input_det = [
                        'id_orden'        => $id_orden,
                        'id_parametro'    => $rp->id,
                        'valor'           => $request->valor,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                
                    ];
            
                    Examen_resultado::create($input_det);
                }else{
                    $resultado->update([
                        'valor' => $request->valor,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ]);
                }
            }    

        }

        

        return "ok";
            
    }
    public function certificar_resultado_esp(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        //dd($request->all());   
        $tipo = substr($request->nombre,0,7);
        $elemento = substr($request->nombre,8); 
        $id_orden = $request->id;
        $ter = '';
        $valor = 0;
        if($request->valor == 'on'){
            $valor = 1;
        }
        
        $orden = Examen_Orden::find($id_orden);

        if($tipo == 'par_cer'){
            $parametro = Examen_Parametro::find($elemento);
            $resultado = $orden->resultados->where('id_parametro',$parametro->id)->first();
            
            if(!is_null($resultado)){
                $resultado->update([
                    'certificado' => $valor,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ]); 
            
            }

            $resto_param = Examen_Parametro::where('id_examen',$parametro->id_examen)->where('id_parametro_ref',$parametro->id)->get();
            foreach($resto_param as $rp){

                $resultado = $orden->resultados->where('id_parametro',$rp->id)->first();
                if(!is_null($resultado)){
                    $resultado->update([
                        'certificado' => $valor,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ]); 
                
                }
            }
        }

        

        return "ok";
}
    public function buscar_seguro(Request $request)
    {
        $seguro  = [];
        if ($request['search'] != null) {
            $seguro = Seguro::where('nombre', 'LIKE', '%' . $request['search'] . '%')->select('id', 'nombre as text')->get();
        }

        return response()->json($seguro);
    }
            
    

    public function cargar_fee($id_orden, $id_ex, $tipo){

        $orden = Examen_Orden::find($id_orden);
        if(!is_null($orden)){
            $detalle = $orden->detalles->where('id_examen',$id_ex)->first();
            if(!is_null($detalle)){
                $detalle->update(['tiene_fee' => $tipo]);

                return "ok";
            }
        }

        return "error";


    }

    public function nuevo_editar_pub(Request $request, $id)
    {

        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $xuser      = Auth::user();
        $tipo_user  = $xuser->id_tipo_usuario;

        $idusuario = $xuser->id;
        date_default_timezone_set('America/Guayaquil');
        $orden = Examen_Orden::find($id);
        $this->validateInput2($request);

        $nivel = '0';
        //$convenio = Convenio::where('id_seguro', $request['id_seguro'])->where('id_empresa', $request['id_empresa'])->first();
        $convenio = Convenio::find($request->id_convenio);
        //$seguro   = Seguro::find($request['id_seguro']);
        $seguro      = Seguro::find($convenio->id_seguro);
        $bandera_err = false;
        $fecha_orden = date('Y/m/d');
        if ($request->fecha_orden != null) {

            if ($request->fecha_orden != date('Y/m/d', strtotime($orden->fecha_orden))) {
                if ($tipo_user == '5') {

                    if ($request->fecha_orden < $fecha_orden) {

                        $bandera_err = true;
                        $arr_campo   = ['fecha_orden' => 'Fecha no puede ser menor a hoy'];
                    }
                }
            }

            $fecha_orden = $request->fecha_orden;
        }

        if ($seguro->tipo != '0') {

            $bandera_err = true;
            $arr_campo   = ['id_seguro' => 'Seguro particular no habilitado'];
        } else {

            if (is_null($convenio)) {
                $bandera_err = true;
                $arr_campo   = ['id_seguro' => 'Convenio no habilitado', 'id_empresa' => 'Convenio no habilitado'];
            }
        }

        if ($bandera_err) {
            //dd($request->all());

            $empresas    = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();
            $usuarios    = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
            $examenes    = Examen::where('estado', '1')->orderBy('id_agrupador')->get();
            $agrupadores = Examen_Agrupador::where('estado', '1')->get();
            $orden       = DB::table('examen_orden as eo')->where('eo.id', $id)->join('paciente as p', 'p.id', 'eo.id_paciente')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->first();
            $detalles    = Examen_Detalle::where('id_examen_orden', $id)->where('estado', '1')->get();
            $seguro      = Seguro::find($orden->id_seguro);
            $seguros     = Seguro::where('inactivo', '1')->get();
            $protocolos  = Protocolo::where('estado', '1')->get();
            $convenios   = DB::table('convenio as c')->join('seguros as s', 's.id', 'c.id_seguro')->join('empresa as e', 'e.id', 'c.id_empresa')->where('s.tipo', '0')->select('c.*', 's.nombre', 'e.nombrecomercial')->orderby('s.nombre')->get();
            $convenio    = Convenio::where('id_seguro', $orden->id_seguro)->where('id_empresa', $orden->id_empresa)->first();

            return redirect()->back()->with(['orden' => $orden, 'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresas, 'seguros' => $seguros, 'convenios' => $convenios, 'convenio' => $convenio])->withErrors($arr_campo)->withInput(['id_doctor_ieced' => $request['id_doctor_ieced'], 'id_seguro' => $request['id_seguro'], 'id_protocolo' => $request['id_protocolo'], 'id_empresa' => $request['id_empresa'], 'fecha_orden' => $request['fecha_orden']]);
        }

        $anio = date('Y', strtotime($fecha_orden));
        $mes  = date('m', strtotime($fecha_orden));

        if ($tipo_user == '5') {
            $input_ex1 = [
                'id_doctor_ieced' => $request['id_doctor_ieced'],
                'doctor_txt'      => $request['doctor_txt'],
                //'observacion'     => $request['observacion'],
                'id_empresa'      => $convenio->id_empresa,
                //'id_protocolo'    => $request['id_protocolo'],
                'id_seguro'       => $seguro->id,
                'fecha_orden'     => $fecha_orden,
                'est_amb_hos'     => $request->est_amb_hos,
                'anio'            => $anio,
                'mes'             => $mes,
                'ip_modificacion' => $ip_cliente,

                'id_usuariomod'   => $idusuario,

            ];
        } else {
            $input_ex1 = [
                'id_doctor_ieced' => $request['id_doctor_ieced'],
                'doctor_txt'      => $request['doctor_txt'],
                //'observacion'     => $request['observacion'],
                'id_empresa'      => $convenio->id_empresa,
                //'id_protocolo'    => $request['id_protocolo'],
                'id_seguro'       => $seguro->id,
                'fecha_convenios' => $fecha_orden,
                'est_amb_hos'     => $request->est_amb_hos,

                'ip_modificacion' => $ip_cliente,

                'id_usuariomod'   => $idusuario,

            ];
        }

        $orden->update($input_ex1);

        $detalles = $orden->detalles;

        foreach($detalles as $detalle){

            if (!is_null($convenio)) {
                $nivel    = $convenio->id_nivel;
                $ex_nivel = Examen_Nivel::where('id_examen', $detalle->id_examen)->where('nivel', $nivel)->first();
                if (!is_null($ex_nivel)) {
                    $valor = $ex_nivel->valor1;
                }
            }

            $input_det = [
               
                'valor'           => $valor,
            ];

            $detalle->update($input_det);

        }    

        $paciente = Paciente::find($orden->id_paciente);
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "ACTUALIZA ORDEN EXAMEN",
            'dato_ant1'   => $orden->id_paciente,
            'dato1'       => $paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2,
            //'dato_ant4'   => $examen_nombre,
        ]);

        $cedula = $request['id'];

        if ($tipo_user == '11') {
            $agenda = Cookie::get('agenda');
            if ($agenda != '') {
                return redirect()->route('orden.index_doctor', ['id' => $orden->id_paciente, 'agenda' => $agenda]);
            }

            return redirect()->route('orden.search_supervision');
        }

        if ($request['dir'] == 'CON') {
            //return redirect()->route('orden.index_control_b', ['id' => $id]);
            return redirect()->route('orden.index_control');
        } else {
            return redirect()->route('orden.index');
        }
    }
}
