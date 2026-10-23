<?php

namespace Sis_medico\Http\Controllers\ticket_permisos;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Empresa;
use Sis_medico\User;
use Sis_medico\TipoUsuario;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Ct_Nomina;
use Sis_medico\TicketPermiso;
use Mail;
use Excel;
use Sis_medico\Actividad_Estados_Comercial;
use Sis_medico\Log_Devulta;
use Sis_medico\Ct_Rh_Valores;

class TicketPermisosController extends Controller
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
        if (in_array($rolUsuario, array(1, 24, 4)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $idusuario = Auth::user()->id;
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $trabajadores = null;
        $tipo_permisos = null;

        $desde   = null;
        $hasta   = null;
        $cedula  = null;
        $permiso = null;
        $usuarios=null;
        $estado  = '-1';


        $datos = [];
        if ($rolUsuario  == 1 || $rolUsuario == 20) {
            $datos = TicketPermiso::where('estado', '1')->orderBy('created_at', 'DESC')->paginate(50);

            /*$datos = TicketPermiso::where('ticket_permiso.estado_solicitud', $estado)
            ->join('ct_nomina as n', 'n.id_user', 'ticket_permiso.cedula')
            ->where('n.id_empresa', $id_empresa)
            ->where('n.estado', '1')
            ->where('ticket_permiso.estado', '1')
            ->orderBy('ticket_permiso.created_at', 'DESC')
            ->select('ticket_permiso.*')
            ->paginate(50);*/
        } else {

            $datos = TicketPermiso::where('cedula', $idusuario)->where("estado",1)->get();
        }

        return view('ticket_permiso/index', ['datos' => $datos, 'trabajadores' => $trabajadores, 'tipo_permisos' => $tipo_permisos, 'desde' => $desde, 'hasta' => $hasta, 'cedula' => $cedula, 'permiso' => $permiso, 'estado' => $estado, 'usuarios'=>$usuarios]);
    }
    public function create(Request $request)
    {

        $idusuario  = Auth::user()->id;
        $rolUsuario = Auth::user()->id_tipo_usuario;

        $empresa_nombres = Empresa::where('estado', 1)->get();


        if ($rolUsuario == '20' || $rolUsuario == '1') {
            return view('ticket_permiso/create_con_nomina', ['empresa_nombres' => $empresa_nombres]);
        }
        /*
        $valida = Sis_medicoCt_Nomina::where('id_user', $idusuario)->first();
        if (!is_null($valida)) {
            return view('ticket_permiso/create', ['valida' => $valida]);
        } else {
            return view('ticket_permiso/create_con_nomina');
        }*/
    }

    public function verificar(Request $request)
    {
        $valida = Ct_Nomina::where('id_user', $request['id'])->where('estado', 1)->first();
        if (!empty($valida)) {
            return json_encode($valida);
        } else {
            return json_encode('no');
        }
    }
    public function save(Request $request)
    {
        //dd($request->all());
        $hoy = date("Y-m-d H:i:s");
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;


        $ultimo = TicketPermiso::latest()->first();;
        $serie = 0;
        if (!is_null($ultimo)) {
            $sum = $ultimo->id + 1;
            $serie = str_pad($sum, 10, "0", STR_PAD_LEFT);
        } else {
            $serie1 = 1;
            $serie =  str_pad($serie1, 10, "0", STR_PAD_LEFT);
        }



        date_default_timezone_set('America/Guayaquil');

        $variable =   [
            'fecha_registro' => $hoy,
            'cedula'  => $request['datos_nomina']['id_user'],
            'cargo' => $request['datos_nomina']['cargo'],
            'departamento' => $request['datos_nomina']['area'] == 1 ? 'ADMINISTRATIVA' : 'MEDICA',
            'tipo_permiso' => $request['todo'][4]['permiso'],
            'fecha_desde' => $request['todo'][0]['fecha_desde'],
            'fecha_hasta' => $request['todo'][1]['fecha_hasta'],
            'ora_salida' => $request['todo'][2]['sala'],
            'ora_ingresa' => $request['todo'][3]['ingresa'],
            'hora_ingresomar' => $request['todo'][2]['ingreso'],
            'hora_salidamar' => $request['todo'][3]['salida'],
            'observaciones' => $request['todo'][5]['observaciones'],
            'id_usuario_crea' => $idusuario,
            'no_solicitud'  => $serie,
        ];

        $id = '';

        try {
            if (is_null($request->id)) {
                $id = TicketPermiso::create($variable);
            } else {
                TicketPermiso::where('id', $request['id'])->update(
                    $variable
                );
            }
        } catch (\Throwable $th) {
            return json_encode($th);
        }

        $usuario = User::find($request['datos_nomina']['id_user']);
        $empresa = Ct_Nomina::where("id_user",$usuario->id)->first();

        // $url_completo =  url("/") . '/ticket_permisos/editar/' . $id->id;
        // $correo = 'rrhh@ieced.ec';
        // Mail::send('ticket_permiso.correo', ['user' => $usuario, 'empresa' => $empresa, 'email' => $url_completo], function ($msj)  use ($correo) {
        //     $msj->subject('TICKET PERMISO');
        //     $msj->to($correo);
        //     $msj->bcc('walteralarcon202@gmail.com', 'fortiz@mdconsgroup.com');
        // });
        return json_encode('ok');
    }



    public function subir_pdf(Request $request)
    {
        //$id_no = NULL;
        //dd($request->all());
        $id_permiso = $request->id;
        $permiso = TicketPermiso::find($id_permiso);
        if (!is_null($permiso)) {
            /*if(!is_null($request['userfile']) or $request['userfile']!= "" or $request['userfile']!= "undefined"){*/
            if ($request['userfile'] != "undefined") {
                $nombre_archivo  = $request['userfile']->getClientOriginalName();
                $extension       = $request['userfile']->getClientOriginalExtension();
                $nuevo_nombre    = $permiso->id . "_" . date('YmdHis') . "." . $extension;
                $r5 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['userfile']));
                //dd($r5);
                $permiso->update([
                    'ruta_archivo' => $nuevo_nombre,
                ]);
                return json_encode("ok");
            }
        }

        return json_encode("no");
    }


    public function subir_pdf1(Request $request)
    {   //dd($request->all());
        $id_no = NULL;
        if ($request->userfile == "undefined") {
            return json_encode($id_no);
        } else {
            if (!is_null($request['userfile']) or $request['userfile'] != "" or $request['userfile'] != "undefined") {
                //dd("aqui");
                $nombre_archivo = $request['userfile']->getClientOriginalName();
                $nuevo_nombre  = $nombre_archivo;
                $r5 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['userfile']));

                $variable =   TicketPermiso::create([
                    'ruta_archivo' => $nuevo_nombre,
                ]);

                return json_encode($variable->id);
            }
        }
    }

    public function save_sin_dato(Request $request)
    {
        //dd($request->all());
        $hoy = date("Y-m-d H:i:s");
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        //$ultimo = TicketPermiso::latest()->first();;
        $serie = 0;

        $usuario = User::find($request->cedula);

        date_default_timezone_set('America/Guayaquil');

        $ct_datos_empresa = Ct_Nomina::where('id_user', $request['cedula'])->first();
        
        if (is_null($ct_datos_empresa)) {

            $datos_nomina_inactivos = [

                'id_user'                   => $request->cedula,
                'nombres'                   => strtoupper($usuario->nombre1) . " " . strtoupper($usuario->nombre2) . " " . strtoupper($usuario->apellido1) . " " . strtoupper($usuario->apellido2),
                'id_empresa'                => $request['id_empresa'],
                'area'                      => '1',
                'cargo'                     => $request['cargo'] == null ? '' : $request['cargo'],
                'estado'                    => '0',
                'pago_fondo_reserva'        => '1',
                'aporte_personal'           => '1',
                'ip_creacion'               => $ip_cliente,
                'ip_modificacion'           => $ip_cliente,
                'id_usuariocrea'            => $idusuario,
                'id_usuariomod'             => $idusuario,

            ];

            // dd($datos_nomina_inactivos);

            Ct_Nomina::create($datos_nomina_inactivos);
        }


        $variable =  [
            'fecha_registro' => $request->fecha,
            'cedula'         => $request->cedula,
            'cargo'          => $request->cargo,
            'departamento'   => $request->area,
            'tipo_permiso'   => $request->permiso,
            'fecha_desde'    => $request->desde,
            'fecha_hasta'    => $request->hasta,
            // 'ora_salida'     => $request->sale,
            // 'ora_ingresa'    => $request->ingresa,
            'ora_salida'     => $request->salida,
            'ora_ingresa'    => $request->ingreso,
            'hora_ingresomar' => $request->ingreso,
            'hora_salidamar' => $request->salida,
            'observaciones'  => $request->observaciones,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'  => $idusuario,
            'no_solicitud'   => $serie,
            'ip_creacion'    => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];


        $usuario = User::find($request->cedula);

        $nomina  = Ct_Nomina::where('id_user', $request->cedula)->where('estado', 1);

        $usuario->update(['cargo' => $request->cargo, 'departamento' => $request->area, 'servicios' => $request->servicios]);
        $nomina->update(['cargo' => $request->cargo]);

        $clave = TicketPermiso::insertGetId($variable);
        $empresa = Ct_Nomina::where("id_user",$usuario->id)->first();

        // $url_completo = url("/") . '/ticket_permisos/editar/' . $clave;
        // $correo = 'rrhh@ieced.ec';
        // Mail::send('ticket_permiso.correo', ['user' => $usuario, 'empresa' => $empresa, 'email' => $url_completo], function ($msj)  use ($correo) {
        //     $msj->subject('TICKET PERMISO');
        //     $msj->to($correo);
        //     $msj->bcc('walteralarcon202@gmail.com', 'fortiz@mdconsgroup.com');
        // });

        return ['msn' => 'ok', 'id' => $clave];
    }

    public function editar($id)
    {
        $registro = TicketPermiso::where('id', $id)->first();
        return view('ticket_permiso/edit', ['registro' => $registro]);
    }


    public function editar_datos(Request $request)
    {
        //dd($request->all());
        $idusuario = Auth::user()->id;
        $registro = TicketPermiso::find($request->id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $registro->update([
            //'fecha_registro'  => $request['todo'][8]['fecha_registro'],
            //'cedula' => $request['todo'][12]['cedula'],
            //'cargo' => $request['cargo'],
            //'departamento' => $request['todo'][10]['departamento'],
            //'tipo_permiso' => $request['todo'][4]['permiso'],
            //'fecha_desde' => $request['todo'][0]['fecha_desde'],
            //'fecha_hasta' => $request['todo'][1]['fecha_hasta'],
            //'ora_salida' => $request['todo'][2]['sala'],
            //'ora_ingresa' => $request['todo'][3]['ingresa'],
            //'hora_ingresomar' => $request['todo'][2]['ingreso'],
            //'hora_salidamar' => $request['todo'][3]['salida'],
            //'observaciones' => $request['todo'][5]['observaciones'],
            'id_usuariomod'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'estado_solicitud' => $request->estado,
            'justificacion_final' => $request->obs_acepta,
        ]);

        $usuario = $registro->nombre;
        $usuario->update(['servicios' => $request->servicios]);

        // if ($request->estado == '2') {
        //     $url_completo =  url("/") . '/usuarios_ticket_permisos/index_usuario';
        //     $empresa = Ct_Nomina::where("id_user", $usuario->id)->first();
        //     $correo = $empresa->user->email;
        //     if (!is_null($correo)) {
        //         Mail::send('ticket_permiso.correo_devolucion', ['user' => $usuario, 'empresa' => $empresa, 'email' => $url_completo], function ($msj)  use ($correo) {
        //             $msj->subject('TICKET PERMISO');
        //             $msj->to($correo);
        //             $msj->bcc('walteralarcon202@gmail.com', 'fortiz@mdconsgroup.com');
        //         });
        //     } else {
        //         return json_encode(['status' => false, 'msj' => 'El correo de la notificación al usuario no a sido enviado(correo no encontrado)']);
        //     }
        // }
        return json_encode(['status' => true, 'msj' => 'Guardado con exito']);
    }


    public function buscador(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $desde   = $request->desde;
        $hasta   = $request->hasta;
        $cedula  = $request->id_usuario;
        $usuarios  = $request->nomusuario;
        $permiso = $request->permiso;
        $estado  = $request->estado;

        $soportes = TicketPermiso::where('ticket_permiso.created_at', '>', '2021-12-01 00:00:00')
        ->join('users as us', 'us.id','ticket_permiso.cedula')
        ->where('ticket_permiso.estado', 1);

        /*$soportes = TicketPermiso::where('ticket_permiso.created_at', '>', '2021-12-01 00:00:00')
        ->join('ct_nomina as n', 'n.id_user', 'ticket_permiso.cedula')
        ->where('n.id_empresa', $id_empresa)
        ->where('ticket_permiso.estado', 1)
        ->select('ticket_permiso.*');*/
        if ($request['excel'] == 0) {


            //dd($soportes->get());
            if ($desde != null) {
                $soportes = $soportes->where('ticket_permiso.created_at', '>=', $desde . ' 00:00:00');
            }

            if ($hasta != null) {
                $soportes = $soportes->where('ticket_permiso.created_at', '<=', $hasta . ' 23:59:59');
            }

            if ($estado < '2') {
                if ($estado == 0) {
                    $soportes = $soportes->where('ticket_permiso.estado_solicitud', '-1');
                } else {
                    $soportes = $soportes->where('ticket_permiso.estado_solicitud', '>', '-1');
                }
            }

            if ($cedula != null) {
                $soportes = $soportes->where('ticket_permiso.cedula', $cedula);
            }

            if ($permiso != null) {
                $soportes = $soportes->where('ticket_permiso.tipo_permiso', $permiso);
            }
            if ($usuarios != null) {

                $nombres2 = explode(" ", $usuarios); 
                $cantidad = count($nombres2); 
                if($cantidad=='2' || $cantidad=='3'){       
                        $soportes = $soportes->where(function($jq1) use($usuarios){
                            $jq1->orwhereraw('CONCAT(us.nombre1," ",us.nombre2," ",us.apellido1," ",us.apellido2) LIKE ?', ['%'.$usuarios.'%'])
                                ->orwhereraw('CONCAT(us.apellido1," ",us.apellido2," ",us.nombre1," ",us.nombre2) LIKE ?', ['%'.$usuarios.'%'])
                                ->orwhereraw('CONCAT(us.nombre1," ",us.apellido1," ",us.apellido2) LIKE ?', ['%'.$usuarios.'%'])
                                ->orwhereraw('CONCAT(us.nombre2," ",us.apellido1," ",us.apellido2) LIKE ?', ['%'.$usuarios.'%'])
                                ->orwhereraw('CONCAT(us.apellido1," ",us.nombre1," ",us.nombre2) LIKE ?', ['%'.$usuarios.'%'])
                                ->orwhereraw('CONCAT(us.apellido2," ",us.nombre1," ",us.nombre2) LIKE ?', ['%'.$usuarios.'%'])
                                ->orwhereraw('CONCAT(us.nombre2," ",us.apellido2) LIKE ?', ['%'.$usuarios.'%']) 
                                ->orwhereraw('CONCAT(us.apellido2," ",us.nombre2) LIKE ?', ['%'.$usuarios.'%']);   
                        });
                } else {
                    $soportes = $soportes->whereraw('CONCAT(us.nombre1," ",us.nombre2," ",us.apellido1," ",us.apellido2) LIKE ?', ['%'.$usuarios.'%']);
                }    
            }

            $soportes = $soportes->select('ticket_permiso.*', 'us.nombre1', 'us.nombre2', 'us.apellido1','us.apellido2')->paginate(50);

            $tipo_permisos = TicketPermiso::all();
            //$trabajadores = User::where('id_tipo_usuario', '<>',2)->where('estado', 1)->orderby('apellido1')->get();
            $trabajadores = null;
            return view('ticket_permiso/index', ['datos' => $soportes, 'trabajadores' => $trabajadores, 'tipo_permisos' => $tipo_permisos, 'desde' => $desde, 'hasta' => $hasta, 'cedula' => $cedula, 'permiso' => $permiso, 'estado' => $estado, 'usuarios' => $usuarios]);
        } else {
            //dd($request->all());
            $titulos = array("FECHA","CEDULA" ,"USUARIO", "SOLICITUD", "MOTIVO DE PERMISO", "DEPARTAMENTO","OLVIDO DE MARCACION, ATRASOS Y TELETRABAJO (Ingreso/ Salida)", "PERMISOS EN DIAS (Fecha desde / Fecha hasta)","PERMISOS EN HORAS (Sale/Ingresa)","APROBACIÓN", "ESTADO", "OBSERVACION",  "JUSTIFICACIÓN");
            //Posiciones en el excel
            $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
            if ($desde != null) {
                $soportes = $soportes->where('fecha_registro', '>=', $desde . ' 00:00:00');
            }

            if ($hasta != null) {
                $soportes = $soportes->where('fecha_registro', '<=', $hasta . ' 23:59:59');
            }

            if ($estado != null) {
                if ($estado == 0) {
                    $soportes = $soportes->where('estado_solicitud', '-1');
                } else {
                    $soportes = $soportes->where('estado_solicitud','<>', '-1');
                }
            }

            if ($cedula != null) {
                $soportes = $soportes->where('cedula', $cedula);
            }

            if ($permiso != null) {
                $soportes = $soportes->where('tipo_permiso', $permiso);
            }

            $soportes = $soportes->select('ticket_permiso.*','us.nombre1', 'us.nombre2', 'us.apellido1', 'us.apellido2')->get(50);

            $tipo_permisos = TicketPermiso::all();
            //$trabajadores = User::where('id_tipo_usuario', '<>',2)->where('estado', 1)->orderby('apellido1')->get();

            Excel::create('Solicitud de permisos laborales', function ($excel) use ($titulos, $posicion, $soportes, $request, $tipo_permisos) {
                $excel->sheet('Solicitud de Permiso', function ($sheet) use ($titulos, $posicion, $soportes, $request, $tipo_permisos) {
                    $sheet->mergeCells('A1:G1');
                    $sheet->cell('A1', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('SOLICITUD DE PERMISO LABORALES');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $comienzo = 2; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL

                    /****************TITULOS DEL EXCEL*********************/
                    //crear los titulos en el excel
                    for ($i = 0; $i < count($titulos); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                            $cell->setValue($titulos[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#92CFEF');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                    /*****FIN DE TITULOS DEL EXCEL***********/


                    foreach ($soportes as $soporte) {
                        $nombre = '';
                        if (!is_null($soporte->nombre1)) {
                            $nombre = $soporte->nombre1 . ' ' . $soporte->nombre2 . ' ' . $soporte->apellido1 . ' ' . $soporte->apellido2;
                        }

                      
                        $datos_excel = array();
                        $estado = ["-1" => "POR APROBAR", "1" => "APROBADO", "0" => "NO APROBADO", "2" => "DEVUELTO"];
                        $estado_2 = ["-1" => "POR ATENDER", "1" => "ATENDIDO", "0" => "ATENDIDO", "2" => "DEVUELTO"];
                        array_push($datos_excel, substr($soporte->fecha_registro, 0, 11), $soporte->cedula ,$nombre, $soporte->id, $soporte->tipo_permiso, $soporte->departamento,"{$soporte->hora_ingresomar} - {$soporte->hora_salidamar} ","{$soporte->fecha_desde} - {$soporte->fecha_hasta}","{$soporte->ora_salida} -  {$soporte->ora_ingresa}",$estado["{$soporte->estado_solicitud}"], $estado_2["{$soporte->estado_solicitud}"], $soporte->observaciones,$soporte->justificacion_final);


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
        //dd($request->all());

    }

    public function ver_pdf(Request $request)
    {

        $nombreArchivo = TicketPermiso::where('id', $request['id'])->first();
        if ($nombreArchivo->ruta_archivo != null) {
        $path1 = storage_path() . "/app/avatars/" . $nombreArchivo->ruta_archivo;
            return response()->file($path1);
        } elseif ($nombreArchivo->path_devoluvion != null) {
            $path1 = storage_path() . "/app/avatars/" . $nombreArchivo->path_devoluvion;
        return response()->file($path1);
        }
    }

    public function vh_buscar_usuario(Request $request)
    {

        $nombres = $request['term'];

        $nombres2 = explode(" ", $nombres);
        $cantidad = count($nombres2);

        $usuarios = User::where('estado', '1');

        if($request['tipo'] == 7){

            $usuarios = $usuarios->where('id_tipo_usuario','<>',2);

        }

        if ($cantidad == '2' || $cantidad == '3') {

            $usuarios = $usuarios->where(function ($jq1) use ($nombres) {
                $jq1->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) LIKE ?', ['%' . $nombres . '%'])
                    ->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1) LIKE ?', ['%' . $nombres . '%']);
            });
        } else {

            $usuarios = $usuarios->whereraw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) LIKE ?', ['%' . $nombres . '%']);
        }

        $usuarios = $usuarios->get();

        $data      = array();

        foreach ($usuarios as $usuario) {
            $data[] = array('value' => $usuario->apellido1 . ' ' . $usuario->apellido2 . ' ' . $usuario->nombre1 . ' ' . $usuario->nombre2, 'id' => $usuario->id);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function vh_buscar_nomina($id)
    {

        $nomina = Ct_Nomina::where('id_user', $id)->where('estado', 1)->first();
        $usuario = User::find($id);

        if (!is_null($usuario)) {
            if (!is_null($nomina)) {
                return json_encode(['cargo' => $nomina->cargo, 'departamento' => $usuario->departamento, 'servicios' => null, 'empresa' => $nomina->empresa->nombrecomercial, 'tiene_nomina' => 1]);
            } else {
                return json_encode(['cargo' => $usuario->cargo, 'departamento' => $usuario->departamento, 'servicios' => $usuario->servicios, 'empresa' => null, 'tiene_nomina' => 0]);
            }
        } else {
            return json_encode(['cargo' => null, 'departamento' => null, 'servicios' => null, 'empresa' => null, 'tiene_nomina' => null]);
        }
    }

    public function pdf_permiso($id, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $registro = TicketPermiso::where('id', $id)->first();
        // $empresa = Empresa::where('id', $comp_egreso->id_empresa)->first();
        $vistaurl = "ticket_permiso.permisos_pdf";
        $view     = \View::make($vistaurl, compact('empresa', 'registro'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }

    public function buscador_usuarios(Request $request)
    {


        $idusuario = Auth::user()->id;
        //dd($request->all());
        $desde   = $request->desde;
        $hasta   = $request->hasta;
        $cedula  = $request->id_usuario;
        $permiso = $request->permiso;
        $estado  = $request->estado;
        //dd($estado);

        $soportes = TicketPermiso::query();
        //dd($soportes->get());
        if ($desde != null) {
            $soportes = $soportes->where('created_at', '>=', $desde . ' 00:00:00');
        }

        if ($hasta != null) {
            $soportes = $soportes->where('created_at', '<=', $hasta . ' 23:59:59');
        }
        //
        if (is_null($estado)) {
            $soportes = $soportes->whereIn('estado_solicitud', [0, 1, -1, 2]);
        } elseif ($estado == 2) {
            $soportes = $soportes->whereIn('estado_solicitud', [0, 1, -1]);
        } elseif ($estado == 1) {
            $soportes = $soportes->whereIn('estado_solicitud', [0, 1]);
        } else {
            $soportes = $soportes->where('estado_solicitud', -1);
        }

        $soportes = $soportes->where('cedula', $idusuario)->orderBy('created_at', 'DESC')->where("estado",1)->paginate(50);

        $tipo_permisos = TicketPermiso::all();

        $trabajadores = null;
        return view('ticket_permiso/index_usuario', ['datos' => $soportes, 'trabajadores' => $trabajadores, 'tipo_permisos' => $tipo_permisos, 'desde' => $desde, 'hasta' => $hasta, 'cedula' => $cedula, 'permiso' => $permiso, 'estado' => $estado]);
    }
    public function create_usuario()
    {
        $idusuario  = Auth::user()->id;
        $rolUsuario = Auth::user()->id_tipo_usuario;


        return view('ticket_permiso/create_usuario');
    }
    public function editar_usuario($id)
    {

        $registro = TicketPermiso::where('id', $id)->first();
        if ($registro->estado_solicitud == 0 || $registro->estado_solicitud == 1) {

            return redirect()->route('ticketpermisos.index_usuario');
        }

        return view('ticket_permiso/edit_usuario', ['registro' => $registro]);
    }

    public function editar_datos_usuarios(Request $request)
    {
        // dd($request->all());
        $idusuario = Auth::user()->id;
        $registro = TicketPermiso::find($request->id_registro);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $registro->update([
            //'fecha_registro'  => $request['todo'][8]['fecha_registro'],
            //'cedula' => $request['todo'][12]['cedula'],
            'cargo' => $request['cargo'],
            'departamento' => $request['departamento'],
            'tipo_permiso' => $request['permiso'],
            'fecha_desde' => $request['desde'],
            'fecha_hasta' => $request['hasta'],
            // 'ora_salida' => $request['ora_salida'],
            // 'ora_ingresa' => $request['ora_ingresa'],
            'ora_salida' => $request['salida'],
            'ora_ingresa' => $request['ingreso'],
            'hora_ingresomar' => $request['ingreso'],
            'hora_salidamar' => $request['salida'],
            'observaciones' => $request['observaciones'],
            'id_usuariomod'  => $idusuario,
            'ip_modificacion' => $ip_cliente,

        ]);

        $usuario = $registro->nombre;
        $usuario->update(['servicios' => $request->servicios]);

        return 'ok';
    }
    public function mail_permisos($id)
    {

        $permiso = TicketPermiso::find($id);
        $correo  = $permiso->nombre->email;
        $array   = ['nombre' => $permiso->nombre->apellido1 . ' ' . $permiso->nombre->apellido2 . ' ' . $permiso->nombre->nombre1 . ' ' . $permiso->nombre->nombre2, 'estado_solicitud' => $permiso->estado_solicitud, 'justificacion_final' => $permiso->justificacion_final];
        // $mail = Mail::send('ticket_permiso.mail', $array, function ($msj) use ($correo) {
        //     $msj->subject('Respuesta de Solicitud de Permiso');
        //     $msj->to($correo);
        //     $msj->from('rrhh@ieced.ec');
        //     $msj->bcc('torbi10@hotmail.com');
        // });

        return redirect()->back();
    }

    public function devolver_modal(Request $request)
    {
        $permiso = TicketPermiso::find($request['id']);
        return view("ticket_permiso/modal_devuelta", ['permiso' => $permiso]);
    }
    public function subir_documentacion(Request $request)
    {
        try {
            $usuario = TicketPermiso::find($request['id_user']);
            $id_empresa = $request->session()->get('id_empresa');
            $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
            $path = '';
            if (!is_null($request['respaldo']) or isset($request['respaldo'])) {
                $nombre_archivo = $request['respaldo']->getClientOriginalName();
                $path  = $nombre_archivo;
                Storage::disk('public')->put($nombre_archivo, \File::get($request['respaldo']));
            }
            if (!is_null($usuario->path_devolucion)) {
                DB::table("log_devulta")->insert([
                    'id_ticket_permiso' => $usuario->id,
                    'path_anterior' => $usuario->path_devoluvion,
                    'observacion_anterior' => $usuario->observacion_devolucion,
                    'justificacion_de_rrhh' => $usuario->justificacion_final,
                ]);
            } else {
                DB::table("log_devulta")->insert([
                    'id_ticket_permiso' => $usuario->id,
                    'path_anterior' => $usuario->path_devoluvion,
                    'observacion_anterior' => $usuario['observaciones'],
                    'justificacion_de_rrhh' => $usuario['justificacion_final'],
                ]);
            }
            $usuario->path_devoluvion = $path;
            $usuario->estado_solicitud = -1;
            $usuario->observacion_devolucion = $request['justificacion'];
            $usuario->save();
            // $url_completo =  url("/") . '/ticket_permisos/editar/' . $usuario->id;
            // $correo = 'rrhh@ieced.ec';
            // Mail::send('ticket_permiso.correo_respaldo', ['user' => $usuario, 'empresa' => $empresa, 'email' => $url_completo], function ($msj)  use ($correo) {
            //     $msj->subject('TICKET PERMISO');
            //     $msj->to($correo);
            //     $msj->bcc('walteralarcon202@gmail.com', 'fortiz@mdconsgroup.com');
            // });
            return redirect()->back()->with('success', 'Guardado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function ver_prueba(Request $request)
    {
        $nombreArchivo = TicketPermiso::where('id', $request['id'])->first();
        $path1 = storage_path() . "/app/avatars/" . $nombreArchivo->path_devoluvion;
        return response()->file($path1);
    }
    public function historial(Request $request)
    {
        $cantidad = Log_Devulta::where("id_ticket_permiso", $request['id'])->get();
        return $cantidad;
    }
    public function modal_historial(Request $request)
    {
        $permiso = TicketPermiso::find($request['id']);
        return view("ticket_permiso/modal_historial", compact("permiso"));
    }
    public function ticketpermisos(Request  $request)
    {
        $nombreArchivo = Log_Devulta::where('id', $request['id'])->first();
        $path1 = storage_path() . "/app/avatars/" . $nombreArchivo->path_anterior;
        return response()->file($path1);
    }
    public function historial_dia()
    {
        $datos = TicketPermiso::whereIn('estado_solicitud', [0, 1, 2, -1])->where('estado', '1')->orderBy('created_at', 'DESC')->paginate(6);
        return view("ticket_permiso/modal_del_dia", compact("datos"));
    }


    public function actividad_comercial_index()
    {

        $actividad = Actividad_Estados_Comercial::where('estado',1)->get();

        return view("actividad_comercial_estado/actividad_comercial_index",compact('actividad'));
    }

    public function actividad_comercial()
    {
        $actividad = Actividad_Estados_Comercial::where('estado',1)->get();

        return view("actividad_comercial_estado/actividad_comercial",compact('actividad'));
    }

    public function guardar(Request $request)
    {

        //dd($request->all());    
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];  
      
         
        $comercial = [
            'estado' => 1,
            'actividad_comercial' => $request['actividad'],
            'id_usuariocrea' => $id_usuario, 
            'id_usuariomod' => $id_usuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        
        ];

        Actividad_Estados_Comercial::create($comercial);
        
        return ['status'=> 'correcto' ,'msj'=> 'guardado con exito'];

       
    }


    public function eliminar_actividad($id)
    {
      $ip_cliente = $_SERVER["REMOTE_ADDR"];
  
      $idusuario  = Auth::user()->id;
      try {
  
        $actividad = Actividad_Estados_Comercial::find($id);
        $arr_vacacion = [
          'estado'               => 0,
          'id_usuariocrea'       => $idusuario,
          'id_usuariomod'        => $idusuario,
          'ip_creacion'          => $ip_cliente,
          'ip_modificacion'      => $ip_cliente,
        ];
        $actividad->update($arr_vacacion);
        return json_encode(['status' => true, 'msj' => "Eliminado con exito"]);
      } catch (\Exception $e) {
        return json_encode(['status' => false, 'msj' => $e->getMessage(), 'line' => $e->getLine()]);
      }
    }

    public function actividad_comercial_editar($id)
    {
        
        $actividad = Actividad_Estados_Comercial::find($id);
        //dd($actividad);

        return view("actividad_comercial_estado/actividad_comercial_editar",compact('actividad'));
    }


    public function actualizar(Request $request)
    {
      //dd($request->all());
      $ip_cliente = $_SERVER["REMOTE_ADDR"];
  
      $id_usuario  = Auth::user()->id;
      $id = $request->id;
      $actividad = Actividad_Estados_Comercial::find($id);
      
      
      $arr_actividad = [

        'estado' => 1,
        'actividad_comercial' => $request['actividad'],
        'id_usuariocrea' => $id_usuario, 
        'id_usuariomod' => $id_usuario,
        'ip_creacion' => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        
       
      ];
      $actividad->update($arr_actividad);
      return redirect(route('actividad_comercial_estado.actividad_comercial_index'));
    }
}
