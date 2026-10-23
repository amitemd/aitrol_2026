<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\As400_Observaciones;
use Sis_medico\Convenio;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Resultado;
use Sis_medico\Log_usuario;
use Sis_medico\Paciente;
use Sis_medico\Protocolo;
use Sis_medico\Seguro;
use Sis_medico\User;

class As400Controller extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 4, 5, 11, 3)) == false) {
            return true;
        }
    }

    public function index()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //dd($acreedores);

        return view('as400/index');
    }

    public function index_hc4()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //dd($acreedores);

        return view('as400/index_hc4');
    }

    public function validar_codigo(Request $request)
    {
        $id_orden = $request['codigo'];

        $url        = 'http://134.209.167.175/api_iess/api/order';
        $variables  = 'numOrden=' . $id_orden;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $examen     = Examen_Orden::where('numorden_iess', $id_orden)->where('estado', '<>', 0)->first();
        $convenio   = Convenio::where('id_seguro', '2')->where('id_empresa', '0992704152001')->first();
        if (!is_null($examen)) {
            return "Numero de Orden ya se encuentra registrada en el Sistema";
        }
        $fichero = json_decode($this->metodo_envio('GET', '0992704152001', $url, $variables));

        if (!is_null($fichero)) {
            if ($fichero->status == '200') {

                date_default_timezone_set('America/Guayaquil');
                //dd($fichero->data);

                $nivel       = '0';
                $id_seguro   = '2';
                $seguro      = Seguro::find($id_seguro);
                $fecha_orden = date('Y-m-d');
                $apellidos   = explode(" ", $fichero->data->apellidos);
                $apellido1   = "";
                $apellido2   = "";
                $nombre1     = "";
                $nombre2     = "";
                $sexo        = 1;
                if ($fichero->data->sexo == 'F') {
                    $sexo = 2;
                }
                if (count($apellidos) >= 4) {
                    $apellido1 = $apellidos[0];
                    $apellido2 = $apellidos[1];
                    $nombre1   = $apellidos[2];
                    $nombre2   = $apellidos[3];
                } elseif (count($apellidos) >= 2) {
                    $apellido1 = $apellidos[0];
                    $apellido2 = $apellidos[1];
                    $nombres   = explode(" ", $fichero->data->nombres);
                    if (count($apellidos) >= 2) {
                        $nombre1 = $apellidos[0];
                        $nombre2 = $apellidos[1];
                    }
                } elseif (count($apellidos) >= 1) {
                    $apellido1 = $apellidos[0];
                }
                $fecha_nacimiento = substr($fichero->data->fecNacimiento, 0, 4) . '-' . substr($fichero->data->fecNacimiento, 4, 2) . '-' . substr($fichero->data->fecNacimiento, 5, 2);

                //CREAR USUARIO
                $input_usu_c = [
                    'id'               => $fichero->data->cedula,
                    'nombre1'          => strtoupper($nombre1),
                    'nombre2'          => strtoupper($nombre2),
                    'apellido1'        => strtoupper($apellido1),
                    'apellido2'        => strtoupper($apellido2),
                    'fecha_nacimiento' => $fecha_nacimiento,
                    'telefono1'        => '1',
                    'telefono2'        => '1',
                    'id_tipo_usuario'  => 2,
                    'email'            => $fichero->data->cedula . '@mail.com',
                    'password'         => bcrypt($fichero->data->cedula),
                    'tipo_documento'   => 1,
                    'estado'           => 1,
                    'imagen_url'       => ' ',
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariocrea'   => $idusuario,
                    'id_usuariomod'    => $idusuario,

                ];

                $user = User::find($fichero->data->cedula);

                $input_pac = [

                    'id'                 => $fichero->data->cedula,
                    'id_usuario'         => $fichero->data->cedula,
                    'nombre1'            => strtoupper($nombre1),
                    'nombre2'            => strtoupper($nombre2),
                    'apellido1'          => strtoupper($apellido1),
                    'apellido2'          => strtoupper($apellido2),
                    'fecha_nacimiento'   => $fecha_nacimiento,
                    'sexo'               => $sexo,
                    'telefono1'          => '1',
                    'telefono2'          => '1',
                    'nombre1'            => strtoupper($nombre1),
                    'nombre2'            => strtoupper($nombre2),
                    'apellido1'          => strtoupper($apellido1),
                    'apellido2'          => strtoupper($apellido2),
                    'parentesco'         => 'Principal',
                    'parentescofamiliar' => 'Principal',
                    'tipo_documento'     => 1,
                    'id_seguro'          => 2,
                    'imagen_url'         => ' ',
                    'menoredad'          => 0,
                    'ip_creacion'        => $ip_cliente,
                    'ip_modificacion'    => $ip_cliente,
                    'id_usuariocrea'     => $idusuario,
                    'id_usuariomod'      => $idusuario,

                ];

                $paciente = Paciente::find($fichero->data->cedula);

                if (is_null($paciente)) {

                    if (is_null($user)) {
                        User::create($input_usu_c);
                    }

                    paciente::create($input_pac);

                    $input_log = [
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "CREA NUEVO PACIENTE DESDE EL AS400",
                        'dato_ant1'   => $request['id'],
                        'dato1'       => strtoupper($fichero->data->apellidos . ' ' . $fichero->data->nombres),
                        'dato_ant4'   => "PARENTESCO: Principal",
                        'dato2'       => 'Creaccion AS400',
                    ];

                    Log_usuario::create($input_log);
                } else {
                    if ($fecha_nacimiento == null || $sexo == null) {

                        $pac = [
                            'fecha_nacimiento' => $fecha_nacimiento,
                            'sexo'             => $sexo,
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

                foreach ($fichero->data->examenes as $value) {
                    $examen = Examen::where('tarifario', $value)->where('estado', '1')->first();
                    if (is_null($examen)) {
                        $tarifario = DB::table('tarifario')->where('codigo', $value)->first();
                        if (is_null($tarifario)) {
                            $observacion = As400_Observaciones::where('numOrden', $id_orden)->where('codigo_tarifario', $value)->first();
                            if (is_null($observacion)) {
                                As400_Observaciones::create([
                                    'observaciones'    => "no existe en el tarifario",
                                    'numOrden'         => $id_orden,
                                    'codigo_tarifario' => $value,
                                    'ip_creacion'      => $ip_cliente,
                                    'ip_modificacion'  => $ip_cliente,
                                    'id_usuariocrea'   => $idusuario,
                                    'id_usuariomod'    => $idusuario,
                                ]);
                            }
                        } else {
                            $observacion = As400_Observaciones::where('numOrden', $id_orden)->where('codigo_tarifario', $value)->first();
                            if (is_null($observacion)) {
                                As400_Observaciones::create([
                                    'observaciones'    => "existe en el tarifario pero no en los examenes",
                                    'numOrden'         => $id_orden,
                                    'codigo_tarifario' => $value,
                                    'ip_creacion'      => $ip_cliente,
                                    'ip_modificacion'  => $ip_cliente,
                                    'id_usuariocrea'   => $idusuario,
                                    'id_usuariomod'    => $idusuario,
                                ]);
                            }

                        }
                    } else {
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
                    'id_paciente'     => $fichero->data->cedula,
                    'anio'            => substr(date('Y-m-d'), 0, 4),
                    'mes'             => substr(date('Y-m-d'), 5, 2),
                    'id_seguro'       => 2,
                    'id_nivel'        => $nivel,
                    'est_amb_hos'     => 0,
                    'id_doctor_ieced' => $idusuario,
                    'doctor_txt'      => $fichero->data->medico,
                    'id_empresa'      => '0992704152001',
                    'cantidad'        => $cont,
                    'fecha_orden'     => date('Y-m-d H:i:s'),
                    'valor'           => $total,
                    'total_valor'     => $total,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'numorden_iess'   => $fichero->data->numOrden,
                    'historia_iess'   => $fichero->data->histClinica,

                ];

                $id_examen_orden = Examen_Orden::insertGetId($input_ex);

                $valor         = 0;
                $cont          = 0;
                $examen_nombre = "";
                foreach ($fichero->data->examenes as $value) {
                    $examen = Examen::where('tarifario', $value)->where('estado', '1')->first();
                    if (!is_null($examen)) {
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

                        ];
                        Examen_detalle::create($input_det);
                    }
                }

                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "GENERA ORDEN EXAMEN AS400",
                    'dato_ant1'   => $fichero->data->cedula,
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato_ant4'   => $examen_nombre,
                ]);
                return "ok";
            }
            As400_Observaciones::create([
                'observaciones'   => "Error en la Conexion con el Api",
                'numOrden'        => $id_orden,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
            return "Error en la Conexion con el Api";
        }
        As400_Observaciones::create([
            'observaciones'   => "El codigo de Orden no existe en el AS400",
            'numOrden'        => $id_orden,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);
        return "Por favor, verifique el codigo de la Orden";
    }

    public function metodo_envio($tipo, $empresa, $url, $variables)
    {

        if ($tipo == "GET") {

            $empresa = DB::table('as400_empresa')->where('id_empresa', $empresa)->first();

            // Crear un flujo
            $opciones = array(
                'http' => array(
                    'method' => $tipo,
                    'header' => "apikey: " . $empresa->apikey . "\r\n",
                ),
            );
            $contexto = stream_context_create($opciones);

            // Abre el fichero usando las cabeceras HTTP establecidas arriba
            $fichero = file_get_contents($url . '?uniMedica=' . $empresa->unimedica . '&' . $variables, false, $contexto);
        }
        return $fichero;
    }

    public function guardar_orden(Request $request)
    {

        $cedula      = $request['cedula'];
        $nombres     = $request['nombres'];
        $contador    = $request['contador'];
        $histClinica = $request['histClinica'];
        $numOrden    = $request['numOrden'];
        return $histClinica;
    }

    public function enviar_orden(Request $request)
    {
        $id_orden = $request['id_orden'];
        $orden    = Examen_Orden::findorfail($id_orden);
        $usuario  = User::find(Auth::user()->id);
        $nombre   = substr($usuario->nombre1, 0, 1) . substr($usuario->apellido1, 0, 1) . substr($usuario->apellido2, 0, 1);
        $empresa  = DB::table('as400_empresa')->where('id_empresa', '0992704152001')->first();
        if (!is_null($orden->numorden_iess)) {
            $ordenHis    = $orden->numorden_iess;
            $ordenLis    = $orden->id;
            $histClinica = $orden->historia_iess;
            $resultados  = Examen_Resultado::where('examen_resultado.id_orden', $orden->id)
                ->join('examen_parametro', 'examen_resultado.id_parametro', '=', 'examen_parametro.id')
                ->whereNotNull('examen_parametro.codigo_iess')
                ->where('examen_resultado.certificado', 1)
                ->select('examen_resultado.id_orden as id_orden', 'examen_resultado.id_parametro as id_parametro', 'examen_resultado.valor as valor', 'examen_parametro.codigo_iess as codigo_iess', 'examen_parametro.id_examen as id_examen', 'examen_parametro.created_at as fecha')->get();
            $trama = array();
            $path  = array();
            foreach ($resultados as $value) {
                $codigo_examen = Examen::find($value->id_examen);
                $array         = array(
                    "ordenHis"             => $ordenHis,
                    "ordenLis"             => $ordenLis,
                    "histClinica"          => $histClinica,
                    "codExamen"            => $codigo_examen->tarifario,
                    "codPerfil"            => $value->codigo_iess,
                    "fecValidacion"        => date("Ymd", strtotime($value->fecha)),
                    "ranMinimo"            => "",
                    "ranMaximo"            => "",
                    "resultado"            => $value->valor,
                    "comentario"           => "",
                    "muestraMicrobiologia" => "",
                    "microorganismo"       => "",
                    "antibiotico"          => "",
                    "sensibilidad"         => "",
                    "usuValidacion"        => $nombre,
                    "medico"               => "",
                    "codMedico"            => "",
                    "patologico"           => "",
                    "uniMedica"            => $empresa->unimedica,
                    "horaValidacion"       => date("his", strtotime($value->fecha)),
                );
                array_push($trama, $array);
                $path = array(
                    "numOrden"   => $ordenHis,
                    "uniMedica"  => $empresa->unimedica,
                    "fechaOrden" => date("Ymd", strtotime($value->fecha)),
                    "horaOrden"  => date("his", strtotime($value->fecha)),
                    "ordenLis"   => $ordenLis,
                    "usuario"    => $nombre,
                    "estado"     => 'A',
                    "examen"     => $codigo_examen->tarifario,
                );
            }
            $postdata = json_encode($trama);
            $opts     = array('http' => array(
                'method'  => 'PUT',
                'header'  => "Content-Type: application/json\r\n" . "apikey:  " . $empresa->apikey . "\r\n",
                'content' => $postdata,
            ),
            );

            $context = stream_context_create($opts);

            $result = file_get_contents('http://134.209.167.175/api_iess/api/result', false, $context);
        }
        return back();
    }

    public function descargar_orden($id)
    {

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

        $seguro     = Seguro::find($orden->id_seguro);
        $protocolos = Protocolo::where('estado', '1')->get();

        $empresa = Empresa::find($orden->id_empresa);

        //dd($orden);
        $arreglo    = [];
        $arreglo1   = [];
        $nro        = 0;
        $agrupa_ant = 0;
        foreach ($examenes as $examen) {
            if ($agrupa_ant != $examen->id_agrupador) {
                $nro        = 0;
                $agrupa_ant = $examen->id_agrupador;
                $arreglo1   = [];
            }
            $arreglo1[$nro] = $examen->id;

            $arreglo[$examen->id_agrupador] = $arreglo1;
            $nro++;

        }

        //dd(count($arreglo[3]));

        if (!is_null($orden)) {
            $tipo_usuario = Auth::user()->id_tipo_usuario;
            //return $tipo_usuario;

            $vistaurl = "laboratorio.orden.orden";
            $view     = \View::make($vistaurl, compact('orden', 'usuarios', 'examenes', 'agrupadores', 'detalles', 'empresa', 'seguro', 'protocolos', 'arreglo', 'seleccionados', 'tipo_usuario'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150]);
            return $pdf->stream('orden-de-laboratorio-' . $id . '.pdf');
            //return view('laboratorio/orden/orden', ['orden' => $orden,'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresa]);

        }
    }

    public function descargar_agenda($nombre)
    {
        $pathtoFile = storage_path() . '/app/hc_agenda/' . $nombre;
        return response()->download($pathtoFile);
    }

}
