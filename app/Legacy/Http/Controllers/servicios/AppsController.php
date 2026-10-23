<?php

namespace Sis_medico\Http\Controllers\servicios;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Apps_Agenda;
use Sis_medico\Apps_Banners;
use Sis_medico\Agenda;
use Sis_medico\Apps_Charlas;
use Sis_medico\Apps_Informacion;
use Sis_medico\Apps_Solicitudes;
use Sis_medico\Doctor_Tiempo;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Requests\Request as RequestsRequest;
use Sis_medico\Membresia;
use Sis_medico\MembresiaDetalle;
use Sis_medico\Procedimiento;
use Sis_medico\User;
use Sis_medico\Hc_Log;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_receta;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Historiaclinica;
use Sis_medico\Horario_Doctor;
use Sis_medico\Paciente;
use Sis_medico\Log_Agenda;

class AppsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth     = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }
    public function index(Request $request)
    {
        $id_empresa = session()->get('id_empresa');
        $charlas = Apps_Charlas::where('id_empresa', $id_empresa)->orderBy('created_at', 'DESC')->get();
        return view('iecedapps.charlas.index', ['charlas' => $charlas]);
    }
    public function edit($id)
    {
        $charlas = Apps_Charlas::find($id);
        $list = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->orderBy('tipo_documento', 'asc')->where('uso_sistema', '0')->get();
        return view('iecedapps.charlas.edit', ['charlas' => $charlas, 'list' => $list]);
    }
    public function create()
    {
        $list = Doctor_Tiempo::orderBy('ip_creacion', 'desc')->join('users as u', 'u.id', 'doctor_tiempo.id_doctor')->where('doctor_tiempo.id_doctor', '<>', 4444444444)->where('doctor_tiempo.id_doctor', '<>', 0)->where('doctor_tiempo.id_doctor', '<>', 3596988777)->select('u.*', 'doctor_tiempo.precio as precio')->get();
        return view('iecedapps.charlas.create', ['list' => $list]);
    }
    public function store(Request $request)
    {
        $id_empresa = session()->get('id_empresa');
        Apps_Charlas::create([
            'descripcion' => $request->descripcion,
            'url' => $request->url,
            'fecha' => $request->fecha,
            'id_doctor' => $request->user,
            'id_empresa' => $id_empresa,
            'estado' => 1
        ]);
        return redirect()->route('charlasapps.index');
    }
    public function update(Request $request)
    {
        $id = $request->id;
        $charlas = Apps_Charlas::find($id);
        $charlas->descripcion = $request->descripcion;
        $charlas->estado = $request->estado;
        $charlas->fecha = $request->fecha;
        $charlas->id_doctor = $request->user;
        $charlas->url = $request->url;
        $charlas->save();
        return redirect()->route('charlasapps.index');
    }
    public function index_banners(Request $request)
    {
        $id_empresa = session()->get('id_empresa');
        $banners = Apps_Banners::where('id_empresa', $id_empresa)->orderBy('created_at', 'DESC')->get();
        return view('iecedapps.banners.index', ['banners' => $banners]);
    }
    public function edit_banners($id)
    {
        $charlas = Apps_Banners::find($id);
        return view('iecedapps.banners.edit', ['banners' => $charlas]);
    }
    public function create_banners()
    {
        return view('iecedapps.banners.create');
    }
    public function store_banners(Request $request)
    {
        //dd($request->all());
        $id_empresa = session()->get('id_empresa');
        $nombre_original = $request['archivo']->getClientOriginalName();
        $extension       = $request['archivo']->getClientOriginalExtension();
        $nuevo_nombre    = "imgBanners" . date('YmdHis') . "." . $extension;

        $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));

        $rutadelaimagen = $nuevo_nombre;
        if ($r1) {
            Apps_Banners::create([
                'descripcion' => $request->descripcion,
                'url' => $nuevo_nombre,
                'link' => $request->link,
                'id_empresa' => $id_empresa,
                'tipo' => $request->tipo,
                'estado' => 1
            ]);
        }

        return redirect()->route('bannersapps.index');
    }
    public function update_banners(Request $request)
    {
        $id = $request->id;
        $charlas = Apps_Banners::find($id);
        $charlas->descripcion = $request->descripcion;
        $charlas->estado = $request->estado;
        $charlas->link = $request->link;
        if ($request->archivo != null) {
            $nombre_original = $request['archivo']->getClientOriginalName();
            $extension       = $request['archivo']->getClientOriginalExtension();
            $nuevo_nombre    = "imgBanners" . date('YmdHis') . "." . $extension;

            $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));

            $rutadelaimagen = $nuevo_nombre;
            $charlas->url = $nuevo_nombre;
        }
        $charlas->save();
        return redirect()->route('bannersapps.index');
    }
    public function agendPatient($request)
    {
        //dd($id);
        $id = $request['id_usuario'];
        $paciente = Paciente::find($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_doctor = $request['id_doctor'];
        $especialidad = DB::table('user_espe as u_es')->where('u_es.usuid', $id_doctor)->get()->first();
        if (!is_null($especialidad)) {
            $espid = $especialidad->espid;
        } else {
            $espid = '4';
        }
        $input_agenda = [
            'fechaini'        => $request['date'],
            'fechafin'         => $request['date'],
            'id_paciente'     => $id,
            'id_doctor1'      => $id_doctor,
            'proc_consul'     => '0',
            'estado_cita'     => '0',
            'id_empresa'      => '0992704152001',
            'espid'           => $espid,
            'tipo_cita'       => 1,
            'observaciones'   => 'FUE PAGADO DESDE LA APP',
            'id_seguro'       => $paciente->id_seguro,
            'estado'          => '1',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $id_doctor,
            'id_usuariomod'   => $id_doctor,
        ];
        $id_agenda = Agenda::insertGetId($input_agenda);
        $input_log = [
            'id_agenda'       => $id_agenda,
            'estado_cita_ant' => '0',
            'estado_cita'     => '0',
            'fechaini'        => $request['date'],
            'fechafin'        => $request['date'],
            'estado'          => '0',
            'observaciones'   => 'FUE PAGADO DESDE LA APP',
            'id_doctor1'      => $id_doctor,
            'descripcion'     => 'FUE PAGADO DESDE LA APP',

            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        $idusuario = $id_doctor;
        Log_Agenda::create($input_log);
        return $id_agenda;
    }
    public function get_pay_app(Request $request)
    {
        $getaapp = Apps_Agenda::where('estado', 1)->where('online', 0)->where('check_in', 0)->get();
        $contador = 0;
        foreach ($getaapp as $g) {
            if ($g->request_id != null) {
                $request['requestId'] = null;
                $request['requestId'] = $g->request_id;
                $response = $this->getPaymentInformationRequest($request);
                if ($response['status']['status'] == 'APPROVED') {
                    $json_request['total'] = $g->total;
                    $json_request['id_doctor'] = $g->id_doctor;
                    $json_request['id_usuario'] = $g->id_usuariocrea;
                    $json_request['date'] = $g->fecha;
                    $id_agenda = $this->agendPatient($json_request);
                    $xp = str_replace("ORD", "", $g->ref_id);
                    $g->online = 1;
                    $g->check_in = 1;
                    $g->id_agenda = $id_agenda;
                    $g->ride = $response['comprobante'];
                    $g->p_subtotal = $response['details']['subtotal'];
                    $g->p_tax = $response['details']['tax1'];
                    $g->p_total = $response['details']['total'];
                    $g->authorization = $response['details']['authorization'];
                    $g->payment = $response['details']['isssuerName'];
                    $g->months = $response['details']['installments'];
                    $g->message = $response['status']['message'];
                    $g->status = $response['status']['status'];
                    /* $g->id_venta = $ventas->id; */
                    $g->save();
                }
            }
        }
        return response()->json(['contador' => $contador, 'status' => 1]);
    }
    public function getPaymentInformationRequest(Request $request)
    {
        //GETS JSON BODY

        $requestId    = $request['requestId'];
        /*
        INVOCA API DE PAGOS&FACTURAS Y OBTIENE LA INFORMACION DEL PAGO EXITOSO
         */
        //$RUC_LABS='0993075000001';
        $PAGOSYFACTURAS_APPID     = 'NVAWRL80IQ3CPXLQWE2NE0U';
        $PAGOSYFACTURAS_APPSECRET = '5X1YA4OAWI1XXT85ROM9IZ4EHLFYWQVEOKI';
        $pyf_checkout_url         = 'https://vpos.accroachcode.com/';
        //detalle(s)
        //json de invocación
        $data_array = array();
        $manage     = json_encode($data_array);
        $make_call  = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/' . $requestId, $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
        $response   = json_decode($make_call, true);

        return $response; //en el status verificar que status.status=="APPROVED"
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
    public function callAPI($method, $url, $data, $appId, $appSecret)
    {
        $Nonce   = $this->getNonce(12);
        $Date    = date('c');
        $Token   = base64_encode(sha1($Nonce . $Date . $appSecret));
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $Date . "\r\n" . "Nonce: " . base64_encode($Nonce) . "\r\n",
                'method'  => 'POST',
                'content' => $data,
            ),
        );
        $context = stream_context_create($options);
        //dd($context, $url);
        return file_get_contents($url, false, $context);
    }
    public function index_membresias()
    {
        $id_empresa = session()->get('id_empresa');
        $membresias = Membresia::where('empresa_id', $id_empresa)->orderBy('created_at', 'DESC')->get();
        return view('iecedapps.membresias.index', ['membresias' => $membresias]);
    }
    public function edit_membresias($id)
    {
        $charlas = Membresia::find($id);
        return view('iecedapps.membresias.edit', ['membresias' => $charlas]);
    }
    public function create_membresias()
    {
        return view('iecedapps.membresias.create');
    }
    public function store_membresias(Request $request)
    {
        //dd($request->all());
        $nombre_original = $request['archivo']->getClientOriginalName();
        $extension       = $request['archivo']->getClientOriginalExtension();
        $nuevo_nombre    = "imgMembresias" . date('YmdHis') . "." . $extension;

        $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));
        $precio_mensual = $request->anual / 12;
        $rutadelaimagen = $nuevo_nombre;
        if ($r1) {
            $id_membresia = Membresia::insertGetId([
                'nombre' => $request->descripcion,
                'url' => $nuevo_nombre,
                'empresa_id' => session()->get('id_empresa'),
                'precio_anual' => $request->anual,
                'precio_mensual' => $precio_mensual,
                'estado' => 1
            ]);
            for ($i = 0; $i < count($request->nombre); $i++) {
                if ($request->nombre[$i] != null) {
                    MembresiaDetalle::create([
                        'membresia_id' => $id_membresia,
                        'nombre' => $request->nombre[$i],
                        'porcentaje_descuento' => $request->porcentaje[$i]
                    ]);
                }
            }
        }

        return redirect()->route('membresiasapps.index');
    }
    public function update_membresias(Request $request)
    {


        $id = $request->id;
        $charlas = Membresia::find($id);
        $charlas->nombre = $request->descripcion;
        if ($request->archivo != null) {
            $nombre_original = $request['archivo']->getClientOriginalName();
            $extension       = $request['archivo']->getClientOriginalExtension();
            $nuevo_nombre    = "imgMembresias" . date('YmdHis') . "." . $extension;

            $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));

            $rutadelaimagen = $nuevo_nombre;
            $charlas->url = $nuevo_nombre;
        }
        $charlas_detalle = MembresiaDetalle::where('membresia_id', $id)->delete();
        for ($i = 0; $i < count($request->nombre); $i++) {
            if ($request->nombre[$i] != null) {
                MembresiaDetalle::create([
                    'membresia_id' => $charlas->id,
                    'nombre' => $request->nombre[$i],
                    'porcentaje_descuento' => $request->porcentaje[$i]
                ]);
            }
        }
        $charlas->save();
        return redirect()->route('membresiasapps.index');
    }
    public function procedimientos(Request $request)
    {
        $procedimientos = Procedimiento::where('estado', '1')->orderby('nombre')->select('procedimiento.nombre as nombre', 'procedimiento.id as id')->get();
        return response()->json($procedimientos);
    }
    public function index_solicitudes(Request $request)
    {
        $id_empresa = session()->get('id_empresa');
        $solicitudes = Apps_Solicitudes::where('id_empresa', $id_empresa);
        if ($request->fecha_hasta == null) {
            $request->fecha_hasta = date('Y-m-d');
        }
        if ($request->fecha_desde == null) {
            $solicitudes = $solicitudes->where('created_at', '<=', $request->fecha_hasta);
        }
        if ($request->fecha_desde != null && $request->fecha_hasta != null) {
            $solicitudes = $solicitudes->whereBetween('created_at', [$request->fecha_desde . ' 00:00:00', $request->fecha_hasta . ' 23:59:59']);
        }
        $solicitudes = $solicitudes->get();
        return view('iecedapps.solicitudes.index', ['solicitudes' => $solicitudes, 'request' => $request]);
    }
    public function show_solicitudes($id)
    {
        $solicitudes = Apps_Solicitudes::find($id);
        return view('iecedapps.solicitudes.edit', ['solicitudes' => $solicitudes]);
    }
    public function update_solicitudes(Request $request)
    {
        $id = $request->id;
        $solicitudes = Apps_Solicitudes::find($id);
        $solicitudes->estado = 2;
        $solicitudes->save();
        return response()->json(['status' => 1, 'msj' => 'ok.']);
    }
    public function index_agenda(Request $request)
    {
        $agendas = Apps_Agenda::where('estado', 1);
        $this->get_pay_app($request);
        if ($request->fecha_hasta == null) {
            $request->fecha_hasta = date('Y-m-d');
        }
        if ($request->fecha_desde == null) {
            $agendas = $agendas->where('fecha', '<=', $request->fecha_hasta . ' 23:59:59');
        }
        if ($request->fecha_desde != null && $request->fecha_hasta != null) {
            $agendas = $agendas->whereBetween('fecha', [$request->fecha_desde . ' 00:00:00', $request->fecha_hasta . ' 23:59:59']);
        }
        $agendas = $agendas->get();

        return view('iecedapps.agenda.index', ['agendas' => $agendas, 'request' => $request]);
    }
    public function index_informacion(Request $request)
    {
        $id_empresa = session()->get('id_empresa');
        $charlas = Apps_Informacion::where('id_empresa', $id_empresa)->orderBy('created_at', 'DESC')->get();
        return view('iecedapps.informacion.index', ['informacion' => $charlas]);
    }
    public function edit_informacion($id)
    {
        $charlas = Apps_Informacion::find($id);
        $list = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->orderBy('tipo_documento', 'asc')->where('uso_sistema', '0')->get();
        return view('iecedapps.informacion.edit', ['informacion' => $charlas, 'list' => $list]);
    }
    public function create_informacion()
    {
        $list = Doctor_Tiempo::orderBy('ip_creacion', 'desc')->join('users as u', 'u.id', 'doctor_tiempo.id_doctor')->where('doctor_tiempo.id_doctor', '<>', 4444444444)->where('doctor_tiempo.id_doctor', '<>', 0)->where('doctor_tiempo.id_doctor', '<>', 3596988777)->select('u.*', 'doctor_tiempo.precio as precio')->get();
        return view('iecedapps.informacion.create', ['list' => $list]);
    }
    public function store_informacion(Request $request)
    {
        $id_empresa = session()->get('id_empresa');
        $nombre_original = $request['archivo']->getClientOriginalName();
        $extension       = $request['archivo']->getClientOriginalExtension();
        $nuevo_nombre    = "imagInformacion" . date('YmdHis') . "." . $extension;


        $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));

        $rutadelaimagen = $nuevo_nombre;
        if ($r1) {
            Apps_Informacion::create([
                'nombre' => $request->nombre,
                'direccion' => $request->direccion,
                'ciudad' => $request->ciudad,
                'whatsapp' => $request->whatsapp,
                'id_empresa' => $id_empresa,
                'imagen' => $rutadelaimagen,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'ubicacion' => $request->ubicacion,
                'estado' => 1
            ]);
        }
        return redirect()->route('apps_informacion.index');
    }
    public function update_informacion(Request $request)
    {
        $id = $request->id;
        $charlas = Apps_Informacion::find($id);
        $charlas->nombre = $request->nombre;
        /*         $charlas->estado = $request->estado;
        $charlas->fecha= $request->fecha;
        $charlas->id_doctor= $request->user;
        $charlas->url = $request->url; */
        $charlas->direccion = $request->direccion;
        $charlas->whatsapp = $request->whatsapp;
        $charlas->telefono = $request->telefono;
        $charlas->email = $request->telefono;
        $charlas->ubicacion = $request->ubicacion;
        if ($request->archivo != null) {
            $nombre_original = $request['archivo']->getClientOriginalName();
            $extension       = $request['archivo']->getClientOriginalExtension();
            $nuevo_nombre    = "imagInformacion" . date('YmdHis') . "." . $extension;

            $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));

            $rutadelaimagen = $nuevo_nombre;
            $charlas->imagen = $nuevo_nombre;
        }
        $charlas->save();
        return redirect()->route('apps_informacion.index');
    }
}
