<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Archivo_historico;
use Sis_medico\ControlDocController;
use Sis_medico\Empresa;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Log_Agenda;
use Sis_medico\Log_usuario;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Pais;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Pentax_log;
use Sis_medico\Principio_Activo;
use Sis_medico\Procedimiento;
use Sis_medico\Procedimiento_Empresa;
use Sis_medico\Sala;
use Sis_medico\Seguro;
use Sis_medico\Subseguro;
use Sis_medico\User;
use Sis_medico\Paciente_Familia;
use Storage;

class AdmisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 11)) == false) {
            return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $paciente = DB::table('paciente')->where('id', '!=', '9999999999')
            ->paginate(10);

        return view('paciente/index', ['paciente' => $paciente]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        return view('paciente/create');
    }

    public function search(Request $request)
    {

        $constraints = [
            'id'        => $request['id'],
            'apellido1' => $request['apellidos'],
            'nombre1'   => $request['nombres'],

        ];

        $constraints2 = [
            'apellido2' => $request['apellidos'],
            'nombre2'   => $request['nombres'],
        ];
        $paciente = $this->doSearchingQuery($constraints, $constraints2);

        return view('paciente/index', ['paciente' => $paciente, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints, $constraints2)
    {
        $query   = Paciente::query();
        $fields  = array_keys($constraints);
        $fields2 = array_keys($constraints2);
        $index   = 0;
        $index2  = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }
        foreach ($constraints2 as $constraint2) {
            if ($constraint2 != null) {
                $query = $query->orwhere($fields2[$index2], 'like', '%' . $constraint2 . '%');
            }

            $index2++;
        }
        return $query->where('id', '!=', '9999999999')->paginate(10);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function admision(Request $request, $id, $cita, $ruta, $unix, $i)
    {

        $rolusuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $paciente = Paciente::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($paciente == null || !is_null($paciente) == 0) {
            return redirect()->intended('/paciente');
        }

        $eprincipal = User::find($paciente->id);
        if ($paciente->parentesco == 'Principal') {
            $user_aso = User::find($paciente->id);
        } else {
            $user_aso = User::find($paciente->id_usuario);
        }
        $empresas     = Empresa::all();
        $paises       = pais::all();
        $seguros      = Seguro::all();
        $subseguros   = Subseguro::all();
        $seguro       = seguro::find($i);
        $cantidad     = Subseguro::where('id_seguro', $i)->count();
        $historia     = Historiaclinica::where('id_agenda', $cita)->first();
        $agenda       = Agenda::find($cita);
        $alergiasxpac = Paciente_Alergia::where('id_paciente', $id)->get();
        $repre_opc    = Paciente_Familia::where('id_paciente', $id)->first();

        /*$archivo_vrf=array();
        if(!is_null($historia)){
        $archivo_historico=Archivo_historico::where('id_historia',$historia[0]->hcid)->where('tipo_documento','VRF')->get();
        if($archivo_historico->isEmpty()){
        $archivo_vrf=null;
        }else{
        $archivo_vrf=$archivo_historico[0];
        }

        }*/

        /*if(!is_null($historia)){
        $i=$historia->id_seguro;
        } */

        //return view('paciente/edit', ['paciente' => $paciente])->with('paises',$paises)->with('seguros',$seguros)->with('rolusuario', $rolusuario);
        return view('admisiones/admision', ['paciente' => $paciente, 'historia' => $historia, 'unix' => $unix, 'paises' => $paises, 'seguros' => $seguros, 'subseguros' => $subseguros, 'user_aso' => $user_aso, 'segurod' => $seguro, 'i' => $i, 'cita' => $agenda, 'ruta' => $ruta, 'empresas' => $empresas, 'eprincipal' => $eprincipal, 'alergiasxpac' => $alergiasxpac, 'repre_opc' => $repre_opc]);
    }

    public function actualizar($id, $i)
    {
        $historia = Historiaclinica::findOrFail($id);
        // Redirect to user list if updating user wasn't existed
        if ($historia == null || count($historia) == 0) {
            return redirect()->intended('/agenda');
        }
        $paciente   = Paciente::find($historia->id_paciente);
        $user_aso   = User::find($paciente->id_usuario);
        $paises     = pais::all();
        $seguros    = Seguro::all();
        $subseguros = Subseguro::all();
        $cita       = $historia->id_agenda;

        $archivo_historico = Archivo_historico::where('id_historia', $historia->hcid)->where('tipo_documento', 'VRF')->get();
        $archivo_vrf       = $archivo_historico[0];

        //return view('paciente/edit', ['paciente' => $paciente])->with('paises',$paises)->with('seguros',$seguros)->with('rolusuario', $rolusuario);
        return view('admisiones/actualizar', ['paciente' => $paciente, 'historia' => $historia])->with('paises', $paises)->with('seguros', $seguros)->with('subseguros', $subseguros)->with('user_aso', $user_aso)->with('archivo_vrf', $archivo_vrf)->with('cita', $cita)->with('i', $i);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //dd($request->all());
        $consultorio = 0;
        if ($request['id_seguro'] != 2) {
            $consultorio = 0;
        } else {
            if ($request['consultorio'] == 1) {
                $consultorio = 1;
            } else {
                $consultorio = 0;
            }
        }

        $paciente   = Paciente::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $usuario_prin = User::find($id);
        if (is_null($usuario_prin)) {
            $usuario_prin = User::where('nombre1', $request->nombre1)->where('nombre2', $request->nombre2)->where('apellido1', $request->apellido1)->where('apellido2', $request->apellido2)->first();
        }
        //$usuario_mail = User::where('id', $paciente->id_usuario)->get();
        $hoy          = date('Y-m-d');
        $historia     = Historiaclinica::where('id_agenda', $request['cita'])->first();

        $cita  = Agenda::find($request['cita']);
        //$cita2 = Agenda::find($request['cita']);

        if ($cita->procedencia == null) {
            $c2_procedencia = '';
        } else {
            $c2_procedencia = $cita->procedencia;
        }

        $c2_id_empresa = $cita->id_empresa;

        //nueva validacion broncoscopia 1/8/2018 ** use Sis_medico\Procedimiento_Empresa;
        $procedimiento_empresa = Procedimiento_Empresa::where('id_procedimiento', $cita->id_procedimiento)->first();
        if (!is_null($procedimiento_empresa)) {
            $this->validate($request, ['id_empresa' => 'in:' . $procedimiento_empresa->id_empresa], ['id_empresa.in' => 'Empresa no habilitada para procedimiento ' . Procedimiento::find($cita->id_procedimiento)->nombre]);
        }


        if (is_null($historia) || $cita->estado_cita != '4') { //Admisiono Primera Vez

            if ($request['parentesco'] == "Principal") {

                $this->ValidatePrincipal($request);
                if (!is_null($usuario_prin)) //adicional actualiza a usuarios
                {
                    $this->ValidateMail($request, 1);
                } else {
                    $this->ValidateMail($request, 0);
                }
            } else {
                $usuario_prin = User::find($request->cedula_principal);
                //Validos Campos para el Principal
                $this->ValidateTienePrincipal($request);
                if (!is_null($usuario_prin)) //adicional actualiza a usuarios
                {
                    $this->ValidateMail2($request, 1);
                } else {
                    $this->ValidateMail2($request, 0);
                }
            }


            $this->ValidatePaciente($request, $paciente, $id, $hoy);

            //dd($request->all(),"ok");
            $id_principal = null;

            if ($request['parentesco'] != "Principal") {
                $id_principal = $request['cedula_principal'];
            } else {
                $id_principal = $request['id'];
            }

            $alergiasxpac = Paciente_Alergia::where('id_paciente', $id)->get();
            foreach ($alergiasxpac as $apac) {
                $apac->delete();
            }

            $alergia_txt = "";
            $ale_flag    = true;
            if ($request->ale_list != null) {
                foreach ($request->ale_list as $ale) {
                    $generico = Principio_Activo::find($ale);
                    $pac_ale  = [

                        'id_paciente'         => $id,
                        'id_principio_activo' => $ale,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariomod'       => $idusuario,
                        'id_usuariocrea'      => $idusuario,

                    ];
                    Paciente_Alergia::create($pac_ale);
                    if ($ale_flag) {
                        $alergia_txt = $generico->nombre;
                        $ale_flag    = false;
                    } else {
                        $alergia_txt = $alergia_txt . '+' . $generico->nombre;
                    }
                }
            }
            //input para paciente
            $input = [
                'id'                 => $request['id'],
                'id_usuario'         => $id_principal,
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'id_pais'            => $request['id_pais'],
                'ciudad'             => strtoupper($request['ciudad']),
                'direccion'          => strtoupper($request['direccion']),
                'telefono1'          => $request['telefono1'],
                'telefono2'          => $request['telefono2'],
                'id_seguro'          => $request['id_seguro'],
                'id_subseguro'       => $request['id_subseguro'],
                'codigo'             => $request['codigo'],
                'fecha_codigo'       => $request['fecha_codigo'],
                'fecha_nacimiento'   => $request['fecha_nacimiento'],
                'menoredad'          => $request['menoredad'],
                'sexo'               => $request['sexo'],
                'estadocivil'        => $request['estadocivil'],
                'alergias'           => $alergia_txt,
                'referido'           => strtoupper($request['referido']),
                'ip_modificacion'    => $ip_cliente,
                'id_usuariomod'      => $idusuario,
                'parentesco'         => $request['parentesco'],
                'ocupacion'          => strtoupper($request['ocupacion']),
                'trabajo'            => strtoupper($request['trabajo']),
                'religion'           => strtoupper($request['religion']),
                'lugar_nacimiento'   => $request['lugar_nacimiento'],
                'cedulafamiliar'     => $request['cedulafamiliar'],
                'nombre1familiar'    => strtoupper($request['nombre1familiar']),
                'nombre2familiar'    => strtoupper($request['nombre2familiar']),
                'apellido1familiar'  => strtoupper($request['apellido1familiar']),
                'apellido2familiar'  => strtoupper($request['apellido2familiar']),
                'parentescofamiliar' => $request['parentescofamiliar'],
                'telefono3'          => $request['telefono3'],
                'papa_mama'          => $request['papa_mama'],
                'mail_opcional'              => $request['email_opc'],
            ];

            if ($request['parentesco'] == "Principal") {
                if (!is_null($usuario_prin)) //adicional actualiza a usuarios
                {
                    $input2 = [
                        'id'               => $request['id'],
                        'nombre1'          => strtoupper($request['nombre1']),
                        'nombre2'          => strtoupper($request['nombre2']),
                        'apellido1'        => strtoupper($request['apellido1']),
                        'apellido2'        => strtoupper($request['apellido2']),
                        'id_pais'          => $request['id_pais'],
                        'ciudad'           => strtoupper($request['ciudad']),
                        'direccion'        => strtoupper($request['direccion']),
                        'email'            => $request['email'],
                        'telefono1'        => $request['telefono1'],
                        'telefono2'        => $request['telefono2'],
                        'fecha_nacimiento' => $request['fecha_nacimiento'],
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'    => $idusuario,
                    ];
                    $usuario_prin->update($input2);
                } else {
                    $input2 = [
                        'id'               => $request['id'],
                        'nombre1'          => strtoupper($request['nombre1']),
                        'nombre2'          => strtoupper($request['nombre2']),
                        'apellido1'        => strtoupper($request['apellido1']),
                        'apellido2'        => strtoupper($request['apellido2']),
                        'tipo_documento'   => '1',
                        'id_pais'          => $request['id_pais'],
                        'ciudad'           => strtoupper($request['ciudad']),
                        'direccion'        => strtoupper($request['direccion']),
                        'email'            => $request['email'],
                        'telefono1'        => $request['telefono1'],
                        'telefono2'        => $request['telefono2'],
                        'password'         => bcrypt($request['id']),
                        'estado'           => 1,
                        'imagen_url'       => ' ',
                        'ip_creacion'      => $ip_cliente,
                        'id_tipo_usuario'  => 2,
                        'fecha_nacimiento' => $request['fecha_nacimiento'],
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'    => $idusuario,
                        'id_usuariocrea'   => $idusuario,
                    ];
                    User::create($input2);
                }
            } else { //CUANDO NO ES PRINCIPAL
                $xuser = User::find($request->cedula_principal);
                if (is_null($xuser)) {
                    $xuser = User::where('nombre1', $request->nombre1_principal)->where('nombre2', $request->nombre2_principal)->where('apellido1', $request->apellido1_principal)->where('apellido2', $request->apellido2_principal)->first();
                }

                if (!is_null($xuser)) //adicional actualiza a usuarios
                {
                    $input2 = [
                        'id'               => $request['cedula_principal'],
                        'nombre1'          => strtoupper($request['nombre1_principal']),
                        'nombre2'          => strtoupper($request['nombre2_principal']),
                        'apellido1'        => strtoupper($request['apellido1_principal']),
                        'apellido2'        => strtoupper($request['apellido2_principal']),
                        'email'            => $request['email_principal'],
                        'fecha_nacimiento' => $request['fecha_nacimiento_principal'],
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'    => $idusuario,
                    ];
                    $xuser->update($input2);
                } else {
                    $input2 = [
                        'id'               => $request['cedula_principal'],
                        'nombre1'          => strtoupper($request['nombre1_principal']),
                        'nombre2'          => strtoupper($request['nombre2_principal']),
                        'apellido1'        => strtoupper($request['apellido1_principal']),
                        'apellido2'        => strtoupper($request['apellido2_principal']),
                        'tipo_documento'   => '1',
                        'id_pais'          => $request['id_pais'],
                        'ciudad'           => strtoupper($request['ciudad']),
                        'direccion'        => strtoupper($request['direccion']),
                        'email'            => $request['email_principal'],
                        'telefono1'        => $request['telefono1'],
                        'telefono2'        => $request['telefono2'],
                        'password'         => bcrypt($request['cedula_principal']),
                        'estado'           => 1,
                        'imagen_url'       => ' ',
                        'ip_creacion'      => $ip_cliente,
                        'id_tipo_usuario'  => 2,
                        'fecha_nacimiento' => $request['fecha_nacimiento_principal'],
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'    => $idusuario,
                        'id_usuariocrea'   => $idusuario,
                    ];
                    User::create($input2);
                }
            }

            $pac_opcional = Paciente_Familia::where('id_paciente', $paciente->id)->first();
            if (!is_null($pac_opcional) || $request->cedula_opcional != null) {
                $constraints = [
                    'apellido1_opcional' => 'required',
                    'nombre1_opcional'  => 'required',
                    'cedula_opcional' => 'required',
                ];

                $mensajes = [
                    'apellido1_opcional.required'    => 'Agrega el primer apellido.',
                    'nombre1_opcional.required'  => 'Agrega el primer nombre.',
                    'cedula_opcional.required' => 'Agrega la cedula',
                ];

                $this->validate($request, $constraints, $mensajes);
            }

            if ($request->cedula_opcional != null) {
                if (!is_null($pac_opcional)) {
                    /*Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "ACTUALIZA DATOS DE OPCIONAL",
                        'dato_ant1'   => $request->id_paciente,
                        'dato1'       => "ACTUALIZA DATOS DE OPCIONAL",
                        'dato_ant2'   => $pac_opcional->apellido1." ".$pac_opcional->apellido2." ".$pac_opcional->nombre1." ".$pac_opcional->nombre2,
                        'dato2'   => strtoupper($request['apellido1_3'])." ".strtoupper($request['apellido2_3'])." ".strtoupper($request['nombre1_3'])." ". strtoupper($request['nombre2_3']),
                    ]);*/
                    $input_op = [
                        'apellido1' => strtoupper($request['apellido1_opcional']),
                        'apellido2' => strtoupper($request['apellido2_opcional']),
                        'nombre1'  => strtoupper($request['nombre1_opcional']),
                        'nombre2'  => strtoupper($request['nombre2_opcional']),
                        'papa_mama' => $request['papa_mama_opcional'],
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                    ];

                    $pac_opcional->update($input_op);
                } else {
                    $input_op = [
                        'cedula_fam' => $request->cedula_opcional,
                        'apellido1' => strtoupper($request['apellido1_opcional']),
                        'apellido2' => strtoupper($request['apellido2_opcional']),
                        'nombre1'  => strtoupper($request['nombre1_opcional']),
                        'nombre2'  => strtoupper($request['nombre2_opcional']),
                        'papa_mama' => $request['papa_mama_opcional'],
                        'id_paciente' => $paciente->id,
                        'ip_modificacion' => $ip_cliente,
                        'ip_creacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'id_usuariocrea' => $idusuario,

                    ];
                    //dd($input_op);
                    Paciente_Familia::create($input_op);
                    /*Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "CREA DATOS DE OPCIONAL",
                        'dato_ant1'   => $request->id_paciente,
                        'dato1'       => "CREA DATOS DE OPCIONAL",
                        'dato_ant2'   => strtoupper($request['apellido1_3'])." ".strtoupper($request['apellido2_3'])." ".strtoupper($request['nombre1_3'])." ". strtoupper($request['nombre2_3']),
                        'dato2'       => " ",
                    ]);*/
                }
            }



            Log_Agenda::create([
                'id_agenda'          => $cita->id,
                'estado_cita_ant'    => $cita->estado_cita,
                'fechaini_ant'       => $cita->fechaini,
                'fechafin_ant'       => $cita->fechafin,
                'estado_ant'         => $cita->estado,
                'cortesia_ant'       => $cita->cortesia,
                'observaciones_ant'  => $cita->observaciones,
                'id_doctor1_ant'     => $cita->id_doctor1,
                'id_doctor2_ant'     => $cita->id_doctor2,
                'id_doctor3_ant'     => $cita->id_doctor3,
                'id_sala_ant'        => $cita->id_sala,
                'estado_cita'        => '4',
                'fechaini'           => $cita->fechaini,
                'fechafin'           => $cita->fechafin,
                'estado'             => $cita->estado,
                'cortesia'           => $cita->cortesia,
                'observaciones'      => $cita->observaciones,
                'id_doctor1'         => $cita->id_doctor1,
                'id_doctor2'         => $cita->id_doctor2,
                'id_doctor3'         => $cita->id_doctor3,
                'id_sala'            => $cita->id_sala,
                'descripcion'        => "ASISTIÓ A LA CITA",
                'descripcion2'       => " ",
                'id_usuarioconfirma' => $cita->id_usuarioconfirma,
                'id_usuariomod'      => $idusuario,
                'id_usuariocrea'     => $idusuario,
                'ip_modificacion'    => $ip_cliente,
                'ip_creacion'        => $ip_cliente,
            ]);

            //CIE_10
            $cantidad = strlen($request['id_cie_10']);
            if ($cantidad == '3') {
                $rules = [
                    'id_cie_10' => 'exists:cie_10_3,id',
                ];
            } else {
                $rules = [
                    'id_cie_10' => 'exists:cie_10_4,id',
                ];
            }

            $msn = [
                'id_cie_10.exists' => 'CIE_10 no existe',
            ];

            if ($cantidad > '0') {
                $this->validate($request, $rules, $msn);
            }
            //CIE_10

            $cita->update([
                'estado_cita' => 4,
                'id_seguro'                  => $request['id_seguro'],
                'consultorio'                => $consultorio,
                'procedencia'                => $request['procedencia'],
                'id_empresa'                 => $request['id_empresa'],
                'id_usuarioconfirma'         => $idusuario,
                'id_usuariomod'              => $idusuario,
                'ip_modificacion'            => $ip_cliente
            ]);

            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "GENERA HISTORIA CLINICA",
                'dato_ant1'   => "PAC: " . $paciente->id,
                'dato1'       => "CIT: " . $request['cita'],
                'dato_ant2'   => $paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2,
                'dato2'       => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
                'dato_ant3'   => "SEG: " . $paciente->id_seguro . " SSE: " . $paciente->id_subseguro . " COD: " . $paciente->codigo . " FEC: " . $paciente->fecha_codigo . " PAR: " . $paciente->parentesco,
                'dato3'       => "SEG: " . $request['id_seguro'] . " SSE: " . $request['id_subseguro'] . " COD: " . $request['codigo'] . " FEC: " . $request['fecha_codigo'] . " PAR: " . $request['parentesco'],
                'dato_ant4'   => " SEX: " . $paciente->sexo . " REL: " . $paciente->religion . " ECI: " . $paciente->estadocivil . " DIR: " . $paciente->direccion . " TEL: " . $paciente->telefono1 . "-" . $paciente->telefono2,
                'dato4'       => "MAI: " . $request['email'] . " SEX: " . $request['sexo'] . " REL: " . $request['religion'] . " ECI: " . $request['estadocivil'] . " DIR: " . $request['direccion'] . " TEL: " . $request['telefono1'] . "-" . $request['telefono2'],
            ]);

            Paciente::where('id', $id)->update($input);

            //$historia_clinica = Historiaclinica::where('id_agenda', $cita->id)->first();

            if (!is_null($historia)) {
                $input_historia = [
                    'id_cie_10'       => $request['id_cie_10'],
                    'parentesco'      => $request['parentesco'],
                    'id_usuario'      => $id_principal,
                    'id_agenda'       => $request['cita'],
                    'id_paciente'     => $request['id'],
                    'id_seguro'       => $request['id_seguro'],
                    'id_subseguro'    => $request['id_subseguro'],
                    'codigo'          => $request['codigo'],
                    'copago'          => $request['copago'],
                    'verificar'       => $request['verificar'],
                    'fecha_codigo'    => $request['fecha_codigo'],
                    'id_doctor1'      => $cita->id_doctor1,
                    'id_doctor2'      => $cita->id_doctor2,
                    'id_doctor3'      => $cita->id_doctor3,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];

                $historia->update($input_historia);
                $id_historia = $historia->hcid;
            } else {

                $input_historia = [
                    'id_cie_10'       => $request['id_cie_10'],
                    'parentesco'      => $request['parentesco'],
                    'id_usuario'      => $id_principal,
                    'id_agenda'       => $request['cita'],
                    'id_paciente'     => $request['id'],
                    'id_seguro'       => $request['id_seguro'],
                    'id_subseguro'    => $request['id_subseguro'],
                    'codigo'          => $request['codigo'],
                    'copago'          => $request['copago'],
                    'verificar'       => $request['verificar'],
                    'fecha_codigo'    => $request['fecha_codigo'],
                    'id_doctor1'      => $cita->id_doctor1,
                    'id_doctor2'      => $cita->id_doctor2,
                    'id_doctor3'      => $cita->id_doctor3,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];

                $id_historia = Historiaclinica::insertGetId($input_historia);

                $id_procedimiento_completo = null;
                if ($cita->proc_consul == '0') {
                    $id_procedimiento_completo = '40';
                }

                //ingreso de datos de para protocolo, evolucion, preparacion para consulta externa
                $input_hc_procedimiento = [
                    'id_hc'                     => $id_historia,
                    'id_seguro'                 => $request['id_seguro'],
                    'id_procedimiento_completo' => $id_procedimiento_completo,
                    'ip_modificacion'           => $ip_cliente,
                    'id_usuariocrea'            => $idusuario,
                    'id_usuariomod'             => $idusuario,
                    'ip_creacion'               => $ip_cliente,
                    'created_at'                => date('Y-m-d H:i:s'),
                    'updated_at'                => date('Y-m-d H:i:s'),
                ];
                $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);
                if ($cita->proc_consul == '0') {

                    $tsexo           = '';
                    $xcuadro_clinico = null;

                    if ($cita->espid == '4') {
                        $xseguro = Seguro::find($request->id_seguro);
                        if (!is_null($xseguro)) {
                            if ($xseguro->tipo == '0') {
                                if ($paciente->sexo == '1') {
                                    $tsexo = 'MASCULINO';
                                } elseif ($paciente->sexo == '2') {
                                    $tsexo = 'FEMENINO';
                                }

                                $tedad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;

                                $xcuadro_clinico = 'PACIENTE DE SEXO ' . $tsexo . ' DE ' . $tedad . ' AÑOS DE EDAD CON CUADRO CLINICO DE  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; MESES DE EVOLUCION CARACTERIZADO POR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; , &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,<br>
                                    (DESCRIPCION DE SINTOMAS INTENSIDAD, HORARIO DE APARICION QUE LO EXACERBA)<br>
                                    EN LA ACTUALIDAD SINTOMAS SE INTESIFICAN POR LO QUE ACUDE A CONSULTA.';
                            }
                        }
                    }

                    $input_hc_evolucion = [
                        'hc_id_procedimiento' => $id_hc_procedimiento,
                        'hcid'                => $id_historia,
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
                    Hc_Evolucion::insert($input_hc_evolucion);
                    $input_hc_receta = [
                        'id_hc'           => $id_historia,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'created_at'      => date('Y-m-d H:i:s'),
                        'updated_at'      => date('Y-m-d H:i:s'),
                    ];
                    hc_receta::insert($input_hc_receta);
                }
                if ($cita->proc_consul == '1') {
                    $input_hc_protocolo = [
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
                    ];
                    hc_protocolo::insert($input_hc_protocolo);
                }
            }

            if ($request['xtipo'] == 0) {
                $input_archivo = [
                    'id_historia'     => $id_historia,
                    'tipo_documento'  => "VRF",
                    'descripcion'     => "VERIFICA SEGUROS PÚBLICOS",
                    'ruta'            => "/hc/",
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];
                /*$id_archivo=Archivo_historico::insertGetId($input_archivo);
                if(!is_null($request['archivo'])){
                    $this->subir_archivo_validacion($request, $id_historia, $id_archivo);
                }*/
            }


            //crea en Pantalla de Pentax 26-12-2017
            $p_hospital = Sala::find($cita->id_sala)->id_hospital;
            //if($cita->proc_consul=='1' && $p_hospital=='2') AHORA VA A CREAR TODOS LOS PROCEDIMIENTOS
            if ($cita->proc_consul == '1') {

                $pentax = Pentax::where('id_agenda', $cita->id)->first();

                if (!is_null($pentax)) {

                    $input_pentax = [
                        'id_agenda'       => $request['cita'],
                        'hcid'            => $id_historia,
                        'id_sala'         => $cita->id_sala,
                        //'estado_pentax' => '0',
                        'estado_pentax'   => '-1',
                        'id_doctor1'      => $cita->id_doctor1,
                        'id_doctor2'      => $cita->id_doctor2,
                        'id_doctor3'      => $cita->id_doctor3,
                        'id_seguro'       => $request['id_seguro'],
                        'id_subseguro'    => $request['id_subseguro'],
                        //'observacion' => "PACIENTE ES ADMISIONADO",
                        'observacion'     => "PACIENTE ES PRE-ADMISIONADO",

                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,

                    ];

                    $pentax->Update($input_pentax);
                    $id_pentax = $pentax->id;
                } else {

                    $input_pentax = [
                        'id_agenda'       => $request['cita'],
                        'hcid'            => $id_historia,
                        'id_sala'         => $cita->id_sala,
                        'estado_pentax'   => '-1',
                        'id_doctor1'      => $cita->id_doctor1,
                        'id_doctor2'      => $cita->id_doctor2,
                        'id_doctor3'      => $cita->id_doctor3,
                        'id_seguro'       => $request['id_seguro'],
                        'id_subseguro'    => $request['id_subseguro'],
                        //'observacion' => "PACIENTE ES ADMISIONADO",
                        'observacion'     => "PACIENTE ES PRE-ADMISIONADO",
                        'id_usuariocrea'  => $idusuario,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,

                    ];

                    $id_pentax = Pentax::insertGetId($input_pentax);
                }

                $pentax_procedimientos = PentaxProc::where('id_pentax', $id_pentax)->get();

                foreach ($pentax_procedimientos as $pentax_procedimiento) {
                    $pentax_procedimiento->delete();
                }

                $input_pentax_pro = [
                    'id_pentax'        => $id_pentax,
                    'id_procedimiento' => $cita->id_procedimiento,
                    'id_usuariocrea'   => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                    'ip_creacion'      => $ip_cliente,
                ];

                PentaxProc::create($input_pentax_pro);

                $list_proc = $cita->id_procedimiento;

                $procedimientos = AgendaProcedimiento::where('id_agenda', $cita->id)->get();
                if (!is_null($procedimientos)) {
                    foreach ($procedimientos as $procedimiento) {
                        $input_pentax_pro2 = [
                            'id_pentax'        => $id_pentax,
                            'id_procedimiento' => $procedimiento->id_procedimiento,
                            'id_usuariocrea'   => $idusuario,
                            'ip_modificacion'  => $ip_cliente,
                            'id_usuariomod'    => $idusuario,
                            'ip_creacion'      => $ip_cliente,
                        ];

                        PentaxProc::create($input_pentax_pro2);
                        $list_proc = $list_proc . "+" . $procedimiento->id_procedimiento;
                    }
                }

                $input_log = [
                    'id_pentax'       => $id_pentax,
                    'tipo_cambio'     => "PRE - ADMISION",
                    'descripcion'     => "PRE - ADMISION",
                    'estado_pentax'   => '0',
                    'procedimientos'  => $list_proc,
                    'id_doctor1'      => $cita->id_doctor1,
                    'id_doctor2'      => $cita->id_doctor2,
                    'id_doctor3'      => $cita->id_doctor3,
                    'observacion'     => "PACIENTE ES PRE-ADMISIONADO",
                    'id_sala'         => $cita->id_sala,
                    'id_seguro'       => $request['id_seguro'],
                    'id_subseguro'    => $request['id_subseguro'],
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                ];

                Pentax_log::create($input_log);
            }

            //return  redirect()->route('agenda.edit2', ['id' => $request['cita'], 'doctor' => $request['ruta']]);
        } else {

            $ruteador        = '2';
            $historia2       = Historiaclinica::where('id_agenda', $request['cita'])->first();
            $c2_id_seguro    = $historia2->id_seguro;
            $c2_id_subseguro = $historia2->id_subseguro;

            //CIE_10
            $cantidad = strlen($request['id_cie_10']);
            if ($cantidad == '3') {
                $rules = [
                    'id_cie_10' => 'exists:cie_10_3,id',
                ];
            } else {
                $rules = [
                    'id_cie_10' => 'exists:cie_10_4,id',
                ];
            }

            $msn = [
                'id_cie_10.exists' => 'CIE_10 no existe',
            ];

            if ($cantidad > '0') {
                $this->validate($request, $rules, $msn);
            }

            //CIE_10

            $vflag       = false;
            $descripcion = "CAMBIO:";
            if ($historia->id_seguro != $request['id_seguro'] || $historia->id_subseguro != $request['id_subseguro'] || $historia->id_cie_10 != $request['id_cie_10']) {
                $vflag = true;
                if ($historia->id_seguro != $request['id_seguro']) {
                    $descripcion = $descripcion . " SEGURO,";
                }
                if ($historia->id_subseguro != $request['id_subseguro']) {
                    $descripcion = $descripcion . " SUB-SEGURO,";
                }

                if ($historia->id_cie_10 != $request['id_cie_10']) {
                    $descripcion = $descripcion . " CIE 10,";
                }

                $input_h2 = [
                    'id_seguro'       => $request['id_seguro'],
                    'id_subseguro'    => $request['id_subseguro'],
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                ];

                $input_h2hc = [
                    'id_cie_10'       => $request['id_cie_10'],
                    'id_seguro'       => $request['id_seguro'],
                    'id_subseguro'    => $request['id_subseguro'],
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,

                ];

                $paciente->update($input_h2);
                $historia->update($input_h2hc);

                $pentax2 = Pentax::where('hcid', $historia2->hcid)->first();

                if (!is_null($pentax2)) {
                    $pentax2->update($input_h2);
                    $procs2   = PentaxProc::where('id_pentax', $pentax2->id)->get();
                    $bandera2 = '0';
                    foreach ($procs2 as $proc2) {

                        if ($bandera2 == '0') {
                            $lp_proc2 = $proc2->id_procedimiento;
                            $bandera2 = '1';
                        } else {
                            $lp_proc2 = $lp_proc2 . "+" . $proc2->id_procedimiento;
                        }
                    }

                    //log pentax
                    $input_log2 = [
                        'id_pentax'       => $pentax2->id,
                        'tipo_cambio'     => 'ACTUALIZA',
                        'descripcion'     => $descripcion,
                        'estado_pentax'   => $pentax2->estado_pentax,
                        'id_seguro'       => $request['id_seguro'],
                        'id_subseguro'    => $request['id_subseguro'],
                        'procedimientos'  => $lp_proc2,
                        'id_doctor1'      => $pentax2->id_doctor1,
                        'id_doctor2'      => $pentax2->id_doctor2,
                        'id_doctor3'      => $pentax2->id_doctor3,
                        'id_sala'         => $pentax2->id_sala,
                        'observacion'     => 'CAMBIO DESDE LA ADMISION',
                        'ip_modificacion' => $ip_cliente,
                        'ip_creacion'     => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'id_usuariocrea'  => $idusuario,
                    ];

                    Pentax_log::create($input_log2);
                }

                //deshabilitar documentos entregados
                $ar_dctos       = Archivo_historico::where('id_historia', $historia->hcid)->get();
                $input_ar_dctos = [
                    'estado'             => 0,
                    'id_usuario_entrega' => null,
                    'id_usuario_recibe'  => null,
                    'fecha_entrega'      => null,
                    'id_usuariomod'      => $idusuario,
                    'ip_modificacion'    => $ip_cliente,
                ];
                foreach ($ar_dctos as $dtos) {
                    $dtos->update($input_ar_dctos);
                }
            }

            if ($cita->procedencia != $request['procedencia'] || $cita->id_empresa != $request['id_empresa'] || $cita->consultorio != $consultorio) {
                $vflag = true;
                if ($cita->procedencia != $request['procedencia']) {
                    $descripcion = $descripcion . " PROCEDENCIA,";
                }
                if ($cita->id_empresa != $request['id_empresa']) {
                    $descripcion = $descripcion . " EMPRESA,";
                }
                if ($cita->consultorio != $consultorio) {
                    $descripcion = $descripcion . " CONSULTORIO,";
                }

                $input_c2 = [
                    'consultorio'     => $consultorio,
                    'procedencia'     => $request['procedencia'],
                    'id_empresa'      => $request['id_empresa'],
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                ];
                $cita->update($input_c2);
            }

            if ($vflag) {
                Log_Agenda::create([
                    'id_agenda'          => $cita->id,
                    'estado_cita_ant'    => $cita->estado_cita,
                    'fechaini_ant'       => $cita->fechaini,
                    'fechafin_ant'       => $cita->fechafin,
                    'estado_ant'         => $cita->estado,
                    'cortesia_ant'       => $cita->cortesia,
                    'observaciones_ant'  => $cita->observaciones,
                    'id_doctor1_ant'     => $cita->id_doctor1,
                    'id_doctor2_ant'     => $cita->id_doctor2,
                    'id_doctor3_ant'     => $cita->id_doctor3,
                    'id_sala_ant'        => $cita->id_sala,

                    'estado_cita'        => $cita->estado_cita,
                    'fechaini'           => $cita->fechaini,
                    'fechafin'           => $cita->fechafin,
                    'estado'             => $cita->estado,
                    'cortesia'           => $cita->cortesia,
                    'observaciones'      => $cita->observaciones,
                    'id_doctor1'         => $cita->id_doctor1,
                    'id_doctor2'         => $cita->id_doctor2,
                    'id_doctor3'         => $cita->id_doctor3,
                    'id_sala'            => $cita->id_sala,

                    'descripcion'        => $descripcion,
                    'descripcion2'       => "YA ADMISIONADO",
                    'descripcion3'       => "",
                    'campos_ant'         => "SEG:" . $c2_id_seguro . " SUB-SEG:" . $c2_id_subseguro . " PEN:" . $c2_procedencia . " EMP:" . $c2_id_empresa,
                    'campos'             => "SEG:" . $request['id_seguro'] . " SUB-SEG:" . $request['id_subseguro'] . " PEN:" . $request['procedencia'] . " EMP:" . $request['id_empresa'],
                    'id_usuarioconfirma' => $cita->id_usuarioconfirma,

                    'id_usuariomod'      => $idusuario,
                    'id_usuariocrea'     => $idusuario,
                    'ip_modificacion'    => $ip_cliente,
                    'ip_creacion'        => $ip_cliente,
                ]);
            }
        }

        $historia             = Historiaclinica::where('id_agenda', $request['cita'])->first();
        $ControlDocController = new hc_admision\ControlDocController;
        $hSeguro              = Seguro::find($request['id_seguro']);
        $cantidad_doc         = $ControlDocController->carga_documentos_union($historia->hcid, $cita->proc_consul, $hSeguro->tipo)->count();

        if ($cantidad_doc > 0) {
            //dd('ruta1_'.$request['ruta']);
            return $ControlDocController->control_doc($request);
        }

        //dd($request['ruta']);
        if ($request['ruta'] === 0) {

            return redirect()->route('preagenda.pentax', ['fecha' => $request['unix']]);
        } else {

            return redirect()->route('agenda.fecha', ['id' => $cita->id_doctor1, 'i' => $request['unix']]);
        }
    }

    public function update_doctor(Request $request, $id, $id_cita, $id_historia)
    {

        //dd($request->url_doctor);
        $paciente   = Paciente::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $historia = Historiaclinica::findOrFail($id_historia);
        $cita     = Agenda::find($id_cita);
        $hoy      = date('Y-m-d');

        //$this->ValidateDatosHC($request);

        $input_paciente = [
            //input Esto ingresa el Dr a los pacientes
            'gruposanguineo' => strtoupper($request['gruposanguineo']),
            /*'alergias' => strtoupper($request['alergias']),
        'vacuna' => $request['vacuna'],
        'alcohol' => $request['alcohol'],
        'hijos_vivos' => $request['hijos_vivos'],
        'hijos_muertos' => $request['hijos_muertos'],
        'anticonceptivos' => strtoupper($request['anticonceptivos']),
        'antecedentes_pat' => $request['antecedentes_pat'],
        'antecedentes_fam' => $request['antecedentes_fam'],
        'antecedentes_quir' => $request['antecedentes_quir'],
        'transfusion' => $request['transfusion'],
        'primera_mens' => $request['primera_mens'],
        'menopausia' => $request['menopausia'],
        'parto_normal' => $request['parto_normal'],
        'parto_cesarea' => $request['parto_cesarea'],
        'aborto' => $request['aborto'],*/
        ];

        $paciente->update($input_paciente);
        $input_historia = [
            'peso'        => $request['peso'],
            'pulso'       => $request['pulso'],
            'altura'      => $request['estatura'],
            'temperatura' => $request['temperatura'],
            'presion'     => $request['presion'],
        ];
        $historia->update($input_historia);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "PREPARACION",
            'dato_ant1'   => "PAC: " . $paciente->id,
            'dato1'       => "CIT: " . $id_cita . " HIS: " . $id_historia,
            'dato_ant2'   => " GSA: " . $paciente->gruposanguineo,
            'dato2'       => " GSA: " . $request['gruposanguineo'],
            'dato_ant3'   => "",
            'dato3'       => "",
            'dato_ant4'   => "PRE: " . $historia->presion . " PUL: " . $historia->pulso . " TEM: " . $historia->temperatura . " EST: " . $historia->altura . " PES: " . $historia->peso,
            'dato4'       => "PRE: " . $request['presion'] . " PUL: " . $request['pulso'] . " TEM: " . $request['temperatura'] . " EST: " . $request['altura'] . " PES: " . $request['peso'],
        ]);

        //return  redirect()->route('preparacion.mostrar',['id' => $id_cita, 'pes' => 'Prepa']);
        return redirect()->route('agenda.edit2', ['id' => $id_cita, 'url_doctor' => $request['url_doctor']]);
    }

    private function ValidatePrincipal(Request $request)
    {

        $prules = [
            //'parentesco' => 'required|in:Principal|son_iguales:'.$request['id'].','.$request['id_prin'],
            'parentesco'       => 'required|in:Principal',
            'fecha_nacimiento' => 'edad_fecha',
        ];

        $pmsn = [
            'parentesco.in'               => 'El Parentesco seleccionado no es el correcto.',
            'fecha_nacimiento.edad_fecha' => 'Paciente Principal no puede ser menor de edad.',
            'parentesco.son_iguales'      => 'Paciente no puede ser Principal',
        ];

        $this->validate($request, $prules, $pmsn);
    }

    private function ValidateTienePrincipal(Request $request)
    {
        $prules = [
            'id_prin'               => 'different:id',
            'fecha_nacimiento_prin' => 'edad_fecha',
        ];
        $pmsn = [
            'id_prin.different'                => 'La cédula del principal no puede ser igual a la del paciente',
            'id.different'                     => 'La cédula no puede ser igual a la del principal',
            'fecha_nacimiento_prin.edad_fecha' => 'Principal no puede ser menor de edad.',
        ];
        $this->validate($request, $prules, $pmsn);
    }

    private function ValidateMail(Request $request, $tipo)
    {
        $mensajes2 = [
            'email.unique'   => 'El Email ya se encuentra registrado.',
            'email.required' => 'Agrega el Email.',
            'email.max'      => 'El Email no puede ser mayor a :max caracteres.',
            'email.email'    => 'El Email tiene error en el formato.',
        ];

        if ($tipo == 0) {
            $constraints2 = [
                'email' => 'required|email|max:191|unique:users,email',
            ];
        } else {
            $constraints2 = [
                'email' => 'required|email|max:191|unique:users,email,' . $request['id'],
            ];
        }

        $this->validate($request, $constraints2, $mensajes2);
    }

    private function ValidateMail2(Request $request, $tipo)
    {
        $mensajes2 = [
            'email_principal.unique'   => 'El Email ya se encuentra registrado.',
            'email_principal.required' => 'Agrega el Email.',
            'email_principal.max'      => 'El Email no puede ser mayor a :max caracteres.',
            'email_principal.email'    => 'El Email tiene error en el formato.',
        ];

        if ($tipo == 0) {
            $constraints2 = [
                'email_principal' => 'required|email|max:191|unique:users,email',
            ];
        } else {
            $constraints2 = [
                'email_principal' => 'required|email|max:191|unique:users,email,' . $request['cedula_principal'],
            ];
        }
        //dd($constraints2);
        $this->validate($request, $constraints2, $mensajes2);
    }

    private function ValidateDatosHC(Request $request)
    {
        $mensajes = [
            'gruposanguineo.required' => 'Agrega el grupo sanguíneo.',
            'gruposanguineo.max'      => 'El grupo sanguíneo no puede ser mayor a :max caracteres.',
            'gruposanguineo.in'       => 'El grupo sanguíneo seleccionado no existe.',
            'alergias.max'            => 'Las alergias no pueden ser mayor a :max caracteres.',
            'vacunas.max'             => 'Las vacunas no pueden ser mayor a :max caracteres.',
            'alcohol.in'              => 'Selecciona la opción correcta.',
            'hijos_vivos.between'     => 'Cantidad debe ser mayor o igual a cero o menor a 100.',
            'hijos_muertos.between'   => 'Cantidad debe ser mayor o igual a cero o menor a 100.',
            'anticonceptivos.max'     => 'Los anticonceptivos no pueden ser mayor a :max caracteres.',
            'antecedentes_pat.max'    => 'Los antecedentes no puede ser mayor a :max caracteres.',
            'antecedentes_fam.max'    => 'Los antecedentes no puede ser mayor a :max caracteres.',
            'antecedentes_quir.max'   => 'Los antecedentes no puede ser mayor a :max caracteres.',
            'transfusion.in'          => 'Selecciona SI o NO.',
            'primera_mens.between'    => 'Edad debe ser mayor o igual a cero o menor a 100.',
            'menopausia.between'      => 'Edad debe ser mayor o igual a cero o menor a 100.',
            'parto_cesarea.between'   => 'Cantidad debe ser mayor o igual a cero o menor a 100.',
            'parto_normal.between'    => 'Cantidad debe ser mayor o igual a cero o menor a 100.',
            'aborto.between'          => 'Cantidad debe ser mayor o igual a cero o menor a 100.',

        ];

        $constraints = [
            'gruposanguineo'    => 'required|max:255|in:AB+,AB-,A+,A-,B+,B-,O+,O-',
            'alergias'          => 'required|max:255',
            'vacuna'            => 'required|max:255',
            'alcohol'           => 'required|in:Nunca,1 o menos veces al mes,2 o 4 veces al mes,2 o 3 veces a la semana,4 o más veces a la semana',
            'hijos_vivos'       => 'between:1,100',
            'hijos_muertos'     => 'between:1,100',
            'anticonceptivos'   => 'max:255',
            'antecedentes_pat'  => 'required|max:300',
            'antecedentes_fam'  => 'required|max:300',
            'antecedentes_quir' => 'required|max:300',
            'transfusion'       => 'required|in:SI,NO',
            'primera_mens'      => 'between:1,100',
            'menopausia'        => 'between:1,100',
            'parto_cesarea'     => 'between:1,100',
            'parto_normal'      => 'between:1,100',
            'aborto'            => 'between:1,100',
        ];

        $this->validate($request, $constraints, $mensajes);
    }

    private function ValidatePaciente(Request $request, $paciente, $id, $hoy)
    {
        $mensajes = [

            'procedencia.required'        => 'Ingresa la procedencia.',
            'id_empresa.required'         => 'Selecciona la empresa',
            'cedulafamiliar.required'     => 'Ingrese la cédula del familiar',
            'id_seguro.required'          => 'Selecciona el seguro.',
            'id_seguro.exists'            => 'Seguro Seleccionado no existe.',
            'id_subseguro.required'       => 'Selecciona el Sub-Seguro.',
            'id_subseguro.exists'         => 'Sub-Seguro Seleccionado no existe.',
            'codigo.required_if'          => 'Ingrese el código de validación.',
            'fecha_codigo.required_if'    => 'Ingrese la fecha de caducidad del código.',
            'fecha_codigo.after_or_equal' => 'El código de validación se encuentra cáducado.',
            'copago.required_if'          => 'Ingrese el % del copago.',
            'copago.between'              => 'El % del copago debe estar entre 0 y 100.',
            'verificar.required_if'       => 'Verificar el seguro Público.',
            'verificar.in'                => 'Valor no permitido para la verificación.',
            'parentesco.required'         => 'Selecciona el Parentesco.',
            'parentesco.in'               => 'El Parentesco seleccionado no es el correcto.',
            'id.unique'                   => 'La cédula ya se encuentra registrada.',
            'id.required'                 => 'Agrega la cédula.',
            'id.aux'                      => 'Ingrese una cédula válida.',
            'id.max'                      => 'La cédula no puede ser mayor a :max caracteres.',
            'nombre1.required'            => 'Agrega el primer nombre.',
            'nombre1.max'                 => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.required'            => 'Agrega el segundo nombre.',
            'nombre2.max'                 => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'          => 'Agrega el primer apellido.',
            'apellido1.max'               => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'          => 'Agrega el segundo apellido.',
            'apellido2.max'               => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'fecha_nacimiento.required'   => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento.date'       => 'La fecha de nacimiento tiene formato incorrecto.',
            'menoredad.required'          => 'Selecciona la edad del paciente.',
            'menoredad.in'                => 'El campo menor de edad es incorrecto.',
            'sexo.required'               => 'Selecciona el género.',
            'sexo.in'                     => 'Selecciona el género correcto.',
            'estadocivil.required'        => 'Selecciona el estado civil.',
            'estadocivil.in'              => 'Selecciona el estado civil correcto.',
            'ocupacion.required'          => 'Ingresa la ocupación.',
            'ocupacion.max'               => 'La ocupación no pueden ser mayor a :max caracteres.',
            'religion.required'           => 'Ingresa la religión.',
            'religion.max'                => 'La religión no pueden ser mayor a :max caracteres.',
            'lugar_nacimiento.required'   => 'Ingresa el lugar de nacimiento.',
            'lugar_nacimiento.max'        => 'El lugar de nacimiento no pueden ser mayor a :max caracteres.',
            'id_pais.required'            => 'Agrega el país.',
            'id_pais.exists'              => 'País seleccionado no existe.',
            'ciudad.required'             => 'Agrega la ciudad.',
            'ciudad.max'                  => 'La ciudad no puede ser mayor a :max caracteres.',
            'direccion.required'          => 'Agrega la direccion.',
            'direccion.max'               => 'La direccion no puede ser mayor a :max caracteres.',
            'telefono1.required'          => 'Agrega el teléfono del domicilio',
            'telefono1.max'               => 'El teléfono del domicilio no puede ser mayor a 10 caracteres.',
            'telefono1.numeric'           => 'El teléfono del domicilio debe ser numérico.',
            'telefono2.required'          => 'Agrega el teléfono celular.',
            'telefono2.max'               => 'El teléfono celular no puede ser mayor a 10 caracteres.',
            'telefono2.numeric'           => 'El teléfono celular debe ser numérico.',
            'referido.max'                => 'El referido no puede ser mayor a :max caracteres.',
            'nombre1familiar.required'    => 'Agrega el primer nombre.',
            'nombre1familiar.max'         => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2familiar.required'    => 'Agrega el segundo nombre.',
            'nombre2familiar.max'         => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1familiar.required'  => 'Agrega el primer apellido.',
            'apellido1familiar.max'       => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2familiar.required'  => 'Agrega el segundo apellido.',
            'apellido2familiar.max'       => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'parentescofamiliar.required' => 'Selecciona el Parentesco.',
            'parentescofamiliar.in'       => 'El Parentesco seleccionado no es el correcto.',
            'telefono3.required'          => 'Agrega el teléfono',
            'telefono3.max'               => 'El teléfono no puede ser mayor a 10 caracteres.',
            'telefono3.numeric'           => 'El teléfono debe ser numérico.',
            'cita.unique'                 => 'La cita ya tiene historia clinica creada',

        ];
        $constraints = [
            //'procedencia' => 'required',
            'id_empresa'         => 'required',
            'cedulafamiliar'     => 'required',
            'id_seguro'          => 'required|exists:seguros,id',
            'id_subseguro'       => 'exists:subseguro,id',
            'codigo'             => 'required_if:codigo_validacion,"SI"|caducidad_codigo:' . $paciente->codigo . ',' . $request['fecha_codigo'] . ',' . $paciente->fecha_codigo,
            'fecha_codigo'       => 'required_if:codigo_validacion,"SI"|after_or_equal:' . $hoy,
            'copago'             => 'required_if:xtipo,1|between:1,100',
            'verificar'          => 'in:1',
            'parentesco'         => 'required',
            'id'                 => 'required|max:10|aux|unique:paciente,id,' . $id,
            'nombre1'            => 'required|max:60',
            'nombre2'            => 'required|max:60',
            'apellido1'          => 'required|max:60',
            'apellido2'          => 'required|max:60',
            'fecha_nacimiento'   => 'required|date',
            'menoredad'          => 'required|in:0,1',
            'sexo'               => 'required|in:1,2',
            'estadocivil'        => 'required',
            'ocupacion'          => 'required|max:60',
            'religion'           => 'required',
            'lugar_nacimiento'   => 'required|max:255',
            'id_pais'            => 'required|exists:pais,id',
            'ciudad'             => 'required|max:60',
            'direccion'          => 'required|max:255',
            'telefono1'          => 'required|max:30',
            'telefono2'          => 'required|max:30',
            'referido'           => 'max:255',
            'nombre1familiar'    => 'required|max:60',
            'nombre2familiar'    => 'required|max:60',
            'apellido1familiar'  => 'required|max:60',
            'apellido2familiar'  => 'required|max:60',
            'parentescofamiliar' => 'required',
            'telefono3'          => 'required|max:30',
            //'transfusion'        => 'in:SI,NO',
            //'cita' => 'unique:historiaclinica,id_agenda',
        ];
        //dd($constraints,$mensajes);
        $this->validate($request, $constraints, $mensajes);
    }

    public function valida_archivo(Request $request)
    {

        $reglas   = ['archivo' => 'mimes:pdf|max:1000'];
        $mensajes = [

            'archivo.required' => 'Agrega un archivo.',
            'archivo.mimes'    => 'El archivo a seleccionar debe ser *.pdf.',
            'archivo.max'      => 'El peso del archivo no puede ser mayor a :max KB.'
        ];

        $this->validate($request, $reglas, $mensajes);
    }

    public function subir_archivo_validacion(Request $request, $id_historia, $id_archivo)
    {
        $id = $request['id'];

        $nombre_original = $request['archivo']->getClientOriginalName();

        $extension = $request['archivo']->getClientOriginalExtension();

        $nuevo_nombre = "hc_" . $id . "_VRF_" . $id_historia . "_" . $id_archivo . "." . $extension;

        $r1 = Storage::disk('hc')->put($nuevo_nombre, \File::get($request['archivo']));

        if ($r1) {

            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            date_default_timezone_set('America/Guayaquil');

            $archivo_historico = Archivo_historico::find($id_archivo);

            $archivo_historico->archivo         = $nuevo_nombre;
            $archivo_historico->ip_modificacion = $ip_cliente;
            $archivo_historico->id_usuariomod   = $idusuario;
            $r2                                 = $archivo_historico->save();
        }
    }

    public function load($name)
    {
        $tipo_user  = Auth::user()->id_tipo_usuario;

        $clave = '';
        $arry = explode('_', $name);
        if(isset($arry[1])){
            $clave = $arry[1];
        }
        $vt_name = '';
        if($tipo_user == 11 and $clave == 'ACT' ){
            $vt_name = 'A_ENTREGA.pdf';
        }

        $path = storage_path() . '/app/hc/' . $name;
        if (file_exists($path)) {

            if($vt_name != ''){
                return Response::make(file_get_contents($path), 200, [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $vt_name . '"',
                ]);
            }

            return Response::make(file_get_contents($path), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $name . '"',
            ]);
        }
    }

    public function show($id)
    {
        //
    }

    public function busca_principal($id_paciente, $id_usuario, $historia)
    {
        $paciente = Paciente::find($id_paciente);
        if ($id_usuario != '0') {
            $user_aso = User::find($id_usuario);
        } else {
            $user_aso = User::find($paciente->id_usuario);
        }

        return view('admisiones.principal', ['paciente' => $paciente, 'user_aso' => $user_aso, 'historia' => $historia]);
    }

    public function editar_principal($id_paciente)
    {

        $paciente = Paciente::find($id_paciente);
        $user_aso = User::find($paciente->id_usuario);
        return view('admisiones.editar_principal', ['paciente' => $paciente, 'user_aso' => $user_aso]);
    }
    public function crear_principal($id_paciente)
    {

        $paciente = Paciente::find($id_paciente);
        $user_aso = User::find($paciente->id_usuario);
        return view('admisiones.crear_principal', ['paciente' => $paciente, 'user_aso' => $user_aso]);
    }
    public function buscar_usuario($id_usuario)
    {

        $user_aso = User::find($id_usuario);
        if (!is_null($user_aso)) {
            return $user_aso;
        } else {
            return "null";
        }
    }
    public function actualiza_pr(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $paciente = Paciente::find($request['id']);
        $prin     = User::find($request['id_buscar']);

        $this->ValidateTienePrincipal($request);

        $this->ValidateUsrPrincipal($request);

        $inputus = [
            'id'               => $request['id_prin'],
            'nombre1'          => strtoupper($request['nombre1_prin']),
            'nombre2'          => strtoupper($request['nombre2_prin']),
            'apellido1'        => strtoupper($request['apellido1_prin']),
            'apellido2'        => strtoupper($request['apellido2_prin']),
            //'id_pais' => $request['id_pais'],
            //'ciudad' => strtoupper($request['ciudad']),
            //'direccion' => strtoupper($request['direccion']),
            'email'            => $request['email'],
            //'telefono1' => $request['telefono1'],
            //'telefono2' => $request['telefono2'],
            'fecha_nacimiento' => $request['fecha_nacimiento_prin'],
            'ip_modificacion'  => $ip_cliente,
            'id_usuariomod'    => $idusuario,
        ];

        $inputpa = [
            'id_usuario'      => $request['id_prin'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        if (!is_null($prin)) {

            $prin->update($inputus);
            $paciente->update($inputpa);
        }
    }

    public function crea_pr(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $paciente = Paciente::find($request['id']);

        $this->ValidateTienePrincipal($request);

        $this->ValidateUsrPrincipal2($request);

        $inputus = [
            'id'               => $request['id_prin'],
            'nombre1'          => strtoupper($request['nombre1_prin']),
            'nombre2'          => strtoupper($request['nombre2_prin']),
            'apellido1'        => strtoupper($request['apellido1_prin']),
            'apellido2'        => strtoupper($request['apellido2_prin']),
            'tipo_documento'   => '1',
            'id_pais'          => $paciente->id_pais,
            'ciudad'           => $paciente->ciudad,
            'direccion'        => $paciente->direccion,
            'email'            => $request['email'],
            'telefono1'        => $paciente->telefono1,
            'telefono2'        => $paciente->telefono2,
            'password'         => bcrypt($request['id_prin']),
            'estado'           => 1,
            'imagen_url'       => ' ',
            'ip_creacion'      => $ip_cliente,
            'id_tipo_usuario'  => 2,
            'fecha_nacimiento' => $request['fecha_nacimiento_prin'],
            'ip_modificacion'  => $ip_cliente,
            'id_usuariomod'    => $idusuario,
            'id_usuariocrea'   => $idusuario,
        ];

        $inputpa = [
            'id_usuario'      => $request['id_prin'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        User::create($inputus);
        $paciente->update($inputpa);
    }

    private function ValidateUsrPrincipal(Request $request)
    {
        $mensajes2 = [
            'id_prin.required'               => 'Agregar la cédula',
            'id_prin.unique'                 => 'Cédula ya existe',
            'nombre1_prin.required'          => 'Agregar el nombre',
            'nombre2_prin.required'          => 'Agregar el nombre',
            'apellido1_prin.required'        => 'Agregar el apellido',
            'apellido2_prin.required'        => 'Agregar el apellido',
            'fecha_nacimiento_prin.required' => 'Agregar el apellido',
            'email.unique'                   => 'El Email ya se encuentra registrado.',
            'email.required'                 => 'Agrega el Email.',
            'email.max'                      => 'El Email no puede ser mayor a :max caracteres.',
            'email.email'                    => 'El Email tiene error en el formato.',
            'fecha_nacimiento.edad_fecha'    => 'Paciente Principal no puede ser menor de edad.',
        ];

        $constraints2 = [
            'id_prin'                        => 'required|unique:users,id,' . $request['id_buscar'],
            'nombre1_prin'                   => 'required',
            'nombre2_prin'                   => 'required',
            'apellido1_prin'                 => 'required',
            'apellido2_prin'                 => 'required',
            'nombre1_prin.required'          => 'Agregar el nombre',
            'nombre2_prin.required'          => 'Agregar el nombre',
            'apellido1_prin.required'        => 'Agregar el apellido',
            'apellido_prin.required'         => 'Agregar el apellido',
            'fecha_nacimiento_prin.required' => 'Agregar el apellido',
            'fecha_nacimiento_prin'          => 'required|edad_fecha',
            'email'                          => 'required|email|max:191|unique:users,email,' . $request['id_buscar'],

        ];

        $this->validate($request, $constraints2, $mensajes2);
    }

    private function ValidateUsrPrincipal2(Request $request)
    {
        $mensajes2 = [

            'id_prin.required'               => 'Agregar la cédula',
            'id_prin.unique'                 => 'Cédula ya existe',
            'nombre1_prin.required'          => 'Agregar el nombre',
            'nombre2_prin.required'          => 'Agregar el nombre',
            'apellido1_prin.required'        => 'Agregar el apellido',
            'apellido2_prin.required'        => 'Agregar el apellido',
            'fecha_nacimiento_prin.required' => 'Agregar el apellido',
            'email.unique'                   => 'El Email ya se encuentra registrado.',
            'email.required'                 => 'Agrega el Email.',
            'email.max'                      => 'El Email no puede ser mayor a :max caracteres.',
            'email.email'                    => 'El Email tiene error en el formato.',
            'fecha_nacimiento.edad_fecha'    => 'Paciente Principal no puede ser menor de edad.',
        ];

        $constraints2 = [
            'id_prin'               => 'required|unique:users,id',
            'nombre1_prin'          => 'required',
            'nombre2_prin'          => 'required',
            'apellido1_prin'        => 'required',
            'apellido2_prin'        => 'required',
            'fecha_nacimiento_prin' => 'required|edad_fecha',
            'email'                 => 'required|email|max:191|unique:users,email',
        ];

        $this->validate($request, $constraints2, $mensajes2);
    }

    public function select_sseguro($id_seguro, $parentesco, $cita, $oldv)
    {

        if ($parentesco == "PadreMadre") {
            $parentesco = "Padre/Madre";
        }
        $seguro     = Seguro::find($id_seguro);
        //$cantidad2  = Subseguro::where('id_seguro', $seguro->id)->count();
        //$subseguros = Subseguro::all();
        $subseguros  = Subseguro::where('id_seguro', $seguro->id)->where('parentesco', $parentesco)->get();
        $cantidad2 = $subseguros->count();
        $historia   = Historiaclinica::where('id_agenda', $cita)->first();

        if ($cantidad2 > 0) {
            return view('admisiones.subseguro', ['cantidad2' => $cantidad2, 'subseguros' => $subseguros, 'historia' => $historia, 'parentesco' => $parentesco, 'oldv' => $oldv]);
        } else {
            return 0;
        }
    }
    public function modaletiquetas($id, $seguro, $alergia)
    {
        $agenda         = Agenda::findOrFail($id);
        $paciente       = Paciente::findOrFail($agenda->id_paciente);
        $procedimientos = AgendaProcedimiento::where('id_agenda', $id)->get();
        $nombre         = $agenda->procedimiento->observacion;
        foreach ($procedimientos as $value) {
            $nombre = $nombre . '+' . $value->procedimiento->observacion;
        }
        $procedimientos = substr($nombre, 0, 25);
        $seguro         = Seguro::findOrFail($seguro);
        return view('admisiones.etiquetas', ['paciente' => $paciente, 'seguro' => $seguro, 'alergia' => $alergia, 'procedimientos' => $procedimientos, 'agenda' => $agenda]);
    }

    public function busca_cie_10($codigo)
    {

        $cantidad = strlen($codigo);

        $cie_10 = null;
        if ($cantidad == '3') {
            $cie_10 = DB::table('cie_10_3')->where('id', $codigo)->where('estado', '1')->first();
        } elseif ($cantidad == '4') {
            $cie_10 = DB::table('cie_10_4')->where('id', $codigo)->where('estado', '1')->first();
        }
        if (!is_null($cie_10)) {
            return $cie_10->descripcion;
        } else {
            return "CIE no encontrado";
        }

        return view('admisiones.etiquetas', ['paciente' => $paciente, 'seguro' => $seguro, 'alergia' => $alergia]);
    }

    public function valida_convenio($id_seguro, $id_cita, $oldva)
    {

        $seguro = Seguro::find($id_seguro);
        if ($seguro->tipo == '0') {
            $empresas = DB::table('empresa as e')->where('e.estado', 1)->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->where('id_seguro', $id_seguro)->groupBy('e.id', 'e.nombrecomercial')->get();
        } else {
            $empresas = Empresa::where('estado', 1)->where('admision', '1')->get();
        }

        $cita = Agenda::find($id_cita);

        return view('admisiones.empresa', ['empresas' => $empresas, 'cita' => $cita, 'oldva' => $oldva]);
    }
}
