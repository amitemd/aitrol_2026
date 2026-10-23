<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Paciente;
use Sis_medico\Empresa;
use Sis_medico\Hosp_dato_paciente;
use Sis_medico\Ingreso_emer_008;
use Sis_medico\Hosp_atencion_fomulario008;
use Sis_medico\Hosp_revision_formulario008;
use Sis_medico\Hosp_accidente_formulario008;
use Sis_medico\Hosp_antecedentes_formulario008;
use Sis_medico\Hosp_signos_vitales_formulario008;
use Sis_medico\Hosp_obstetrica_formulario008;
use Sis_medico\Hospital_Emergencia;
use Sis_medico\Hosp_tratamiento_formulario008;
use Sis_medico\Hosp_formulario008_alta;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Seguro;
use Sis_medico\Ho_Datos_Paciente;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\User;
use Sis_medico\Ho_Triaje_Manchester;
use Sis_medico\Agenda;
use Sis_medico\Sala;
use Sis_medico\Hc_Log;
use Sis_medico\Log_Agenda;
use Sis_medico\Historiaclinica;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_receta;
use Sis_medico\Ho_Form008;
use Sis_medico\hc_child_pugh;
use Sis_medico\Ho_Glasgow;
use Sis_medico\Ho_Lesiones008;
use Sis_medico\Ho_Traspaso_Sala008;
use PDF;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Cie10;
use Sis_medico\Log_usuario;

class Formulario008Controller extends Controller
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
    // 
    public function emergencialista(Request $request)
    {
        /* $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }*/

        $ho_solicitud = Ho_solicitud::where('estado', '1')
            ->where('estado_paso', '2')
            ->join('paciente as p', 'p.id', 'ho_solicitud.id_paciente')
            ->select('p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'ho_solicitud.fecha_ingreso', 'ho_solicitud.id_paciente', 'ho_solicitud.id')->get();

        $ho_triaje = Ho_Triaje_Manchester::join('ho_solicitud as ho_s', 'ho_s.id', 'ho_triaje_manchester.id_ho_solicitud')
            ->where('ho_s.estado_paso', '1')
            ->join('paciente as p', 'p.id', 'ho_s.id_paciente')
            ->join('ho_tipo_emergencia as ho_t_e', 'ho_t_e.id', 'ho_triaje_manchester.tipo_emergencia')
            ->join('ho_prioridad_emergencia as ho_prio', 'ho_prio.id', 'ho_triaje_manchester.prioridad')
            ->select('ho_triaje_manchester.*', 'ho_s.id_paciente', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'ho_prio.nombre as nombre_prioridad', 'ho_t_e.nombre as nombre_emergencia', 'ho_triaje_manchester.prioridad', 'ho_triaje_manchester.tipo_emergencia', 'ho_s.id as id_solicitud', 'ho_s.fecha_ingreso')
            ->get();

        //dd($ho_triaje);

        return view('hospital/emergencia/emergencia', ['ho_solicitud' => $ho_solicitud, 'ho_triaje' => $ho_triaje]);
    }

    public function ingreso008(Request $request, $id_solicitud)
    {

        $fecha = date('Y-m-d');
        $seguros = Seguro::where('inactivo', '1')->get();
        //$seguro_privado = Seguro::where('inactivo','1')->where('tipo','!=','0')->get();
        //dd($id_solicitud);
        $ho_solicitud = null;
        if ($id_solicitud != '0') {
            $ho_solicitud = Ho_solicitud::find($id_solicitud);
        }

        return view('hospital/emergencia/ingreso008', ['seguros' => $seguros, 'fecha' => $fecha, 'ho_solicitud' => $ho_solicitud, 'id_solicitud' => $id_solicitud]);
    }

    //Ingreso a emergencia tabla ingrenso_emer_008
    public function guardar2(Request $request)
    {

        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $ingreso_emergecia = new Ingreso_emer_008();
        $ingreso_emergecia->id_paciente     = $request->cedula;
        $ingreso_emergecia->fecha_ingreso   = $request->f_admision;
        $ingreso_emergecia->save();

        $ingreso = new Hosp_dato_paciente();
        $ingreso->id_paciente             = $request->cedula;
        $ingreso->barrio                  = $request->barrio;
        $ingreso->parroquia               = $request->parroquia;
        $ingreso->canto                   = $request->canton;
        $ingreso->provincia               = $request->provincia;
        $ingreso->zona_ur                 = $request->zona;
        $ingreso->grupo_cultural          = $request->grupo_cultural;
        $ingreso->instruccion             = $request->instruccion;
        $ingreso->edad                    = $request->edad;
        $ingreso->direccion_familiar      = $request->direccion_familiar;
        $ingreso->forma_llegada           = $request->forma_llegada;
        $ingreso->fuente_informacion      = $request->fuente_informacion;
        $ingreso->telefono_inst_per_paci  = $request->telefono_inst_per_paci;

        $ingreso->save();

        return back()->with('message', 'Ha ingresado a Emergencia con Excito !');
    }

    public function guardar(Request $request)
    {

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $id_doctor    = Auth::user()->id;
        $id_paciente = $request['cedula'];
        $paciente = paciente::find($id_paciente);
        $user = User::find($id_paciente);
        $datos_paciente = Ho_Datos_Paciente::Where('id_paciente', $id_paciente)->first();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);

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
            'tipo_documento'     => 1,
            'id_seguro'          => 1,
            'imagen_url'         => ' ',
            'menoredad'          => 0,
            'ip_creacion'        => $ip_cliente,
            'ip_modificacion'    => $ip_cliente,
            'id_usuariocrea'     => $idusuario,
            'id_usuariomod'      => $idusuario,
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
        ];

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
            'empresa_trabajo'        => $empresa->id,
            'llamar_a'               => $request['llamar_a'],
            'nacionalidad'           => $request['nacionalidad'],
            'ip_creacion'            => $ip_cliente,
            'ip_modificacion'        => $ip_cliente,
            'id_usuariocrea'         => $idusuario,
            'id_usuariomod'          => $idusuario,
            'parentesco_afinidad'    => $request['parentesco'],
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
            'empresa_trabajo'        => $empresa->id,
            'llamar_a'               => $request['llamar_a'],
            'nacionalidad'           => $request['nacionalidad'],
            'parentesco_afinidad'    => $request['parentesco'],
            'ip_modificacion'        => $ip_cliente,
            'id_usuariomod'          => $idusuario,
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
                'dato2'       => 'Emergencia-Hospital',
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

        $especialidad = DB::table('user_espe as u_es')->where('u_es.usuid', $id_doctor)->get()->first();

        if (!is_null($especialidad)) {
            $espid = $especialidad->espid;
        } else {
            $espid = '4';
        }

        $id_sala = Sala::where('nombre_sala', 'like', '%emergencia%')->first();
        // dd($id_sala);
        //where a sala con like %sala emergengia% first 'id_sala'
        $input_agenda = [
            'fechaini'        => Date('Y-m-d H:i:s'),
            'fechafin'        => Date('Y-m-d H:i:s'),
            'id_paciente'     => $id_paciente,
            'id_doctor1'      => $id_doctor,
            'proc_consul'     => '4',
            'estado_cita'     => '0',
            'id_empresa'      => $empresa->id,
            'espid'           => $espid,
            'observaciones'   => 'EVOLUCION CREADA POR HOSPITAL',
            'id_seguro'       => $paciente->id_seguro,
            'estado'          => '4',
            'id_sala'         => $id_sala->id,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $id_doctor,
            'id_usuariomod'   => $id_doctor,
        ];

        $id_agenda = Agenda::insertGetId($input_agenda);


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
            'estado_cita_ant' => '0',
            'estado_cita'     => '0',
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

        Log_agenda::create($input_log);

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

        $id_procedimiento_completo = '40';

        $id_historia = Historiaclinica::insertGetId($input_historia);

        $input_hc_procedimiento = [
            'id_hc'                     => $id_historia,
            'id_seguro'                 => $paciente->id_seguro,
            'id_procedimiento_completo' => $id_procedimiento_completo,
            'ip_modificacion'           => $ip_cliente,
            'id_usuariocrea'            => $idusuario,
            'id_usuariomod'             => $idusuario,
            'ip_creacion'               => $ip_cliente,

        ];

        $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

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
        hc_receta::insert($input_hc_receta);

        $id_solicitud = $request['idsolicitud'];

        if ($id_solicitud != 0) {
            $solicitud = Ho_Solicitud::find($id_solicitud);
            $arr_sol = [
                'estado_paso'       => '2',
                'id_agenda'         => $id_agenda,
                'id_seguro'         => $request['id_seguro'],
                'ip_modificacion'   => $ip_cliente,
                'id_usuariomod'     => $idusuario,
            ];
            $solicitud->update($arr_sol);

            $sol_log = [
                'id_ho_solicitud'       => $solicitud->id,
                'estado_paso'           => '2',
                'id_agenda'             => $id_agenda,
                'fecha_ingreso'         => date('Y-m-d'),
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
            ];

            $log_soli = Ho_Log_Solicitud::create($sol_log);

            $arr_008 = [
                'id_solicitud'          => $solicitud->id,
                'id_agenda'             => $id_agenda,
                'fecha_creacion'        => date('Y-m-d H:i:s'),
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
            ];

            $ho_form_008 = Ho_Form008::create($arr_008);
        } else {
            $arr_solicitud = [
                'id_paciente'           => $request['cedula'],
                'id_agenda'             => $id_agenda,
                'id_seguro'             => $request['id_seguro'],
                'fecha_ingreso'         => date('Y-m-d H:i:s'),
                'estado_paso'           => '2',
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
            ];

            $ho_solicitud = Ho_solicitud::insertGetId($arr_solicitud);

            $solicitud_log = [
                'id_ho_solicitud'       => $ho_solicitud,
                'estado_paso'           => '2',
                'id_agenda'             => $id_agenda,
                'fecha_ingreso'         => date('Y-m-d'),
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
            ];

            $log = Ho_Log_Solicitud::create($solicitud_log);

            $arr_008 = [
                'id_solicitud'          => $ho_solicitud,
                'id_agenda'             => $id_agenda,
                'fecha_creacion'        => date('Y-m-d H:i:s'),
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
            ];

            $ho_form_008 = Ho_Form008::create($arr_008);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function formulario08(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }

        $adicional = Hosp_dato_paciente::where('id_paciente', '=', $id_paciente)->get();
        //dd($adicional);
        $datos_paciente = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select(
                'paciente.id',
                'paciente.nombre1',
                'paciente.nombre2',
                'paciente.apellido1',
                'paciente.apellido2',
                'paciente.ciudad',
                'paciente.telefono1',
                'paciente.telefono2',
                'paciente.direccion',
                'paciente.fecha_nacimiento',
                'paciente.lugar_nacimiento',
                'paciente.id_pais',
                'paciente.sexo',
                'paciente.estadocivil',
                'paciente.ocupacion',
                'paciente.id_seguro',
                'paciente.referido',
                'paciente.telefono3',
                'paciente.telefono_llamar',
                'paciente.nombre1familiar',
                'paciente.nombre2familiar',
                'paciente.apellido1familiar',
                'paciente.apellido2familiar',
                'paciente.parentesco',
                'ingreso_emer_008.created_at'
            )
            ->get();
        dd($datos_paciente);
        return view('hospital/emergencia/formulario008', ['id_paciente' => $id_paciente, 'datos_paciente' => $datos_paciente, 'adicional' => $adicional]);
    }

    public function buscar_paciente2(Request $request)
    {

        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $nombre_encargado = $request['term'];
        $data             = null;
        $nuevo_nombre     = explode(' ', $nombre_encargado);
        $seteo            = "%";

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM `paciente`
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' ";

        $nombres = DB::select($query);

        foreach ($nombres as $nombre) {
            $data[] = array('value' => $nombre->completo, 'id' => $nombre->id);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

        return $productos;
    }

    public function buscar_paciente(Request $request)
    {
        $nombre = $request['term'];
        $data   = array();
        //dd($nombre);
        $pacientes = Paciente::orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombre . '%'])
            ->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', ['%' . $nombre . '%'])
            ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombre . '%'])
            ->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1) LIKE ?', ['%' . $nombre . '%'])
            ->leftjoin('ho_datos_paciente as hopac', 'hopac.id_paciente', 'paciente.id')
            ->select('paciente.id as idpaciente', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'paciente.ciudad', 'paciente.telefono1', 'paciente.telefono2', 'paciente.direccion', 'paciente.estadocivil', 'paciente.ocupacion', 'paciente.referido', 'paciente.apellido1familiar', 'paciente.apellido2familiar', 'paciente.nombre1familiar', 'paciente.nombre2familiar', 'paciente.parentescofamiliar', 'paciente.sexo', 'paciente.fecha_nacimiento', 'paciente.telefono_llamar', 'paciente.telefono3', 'hopac.*')
            ->get();

        //dd($pacientes);
        foreach ($pacientes as $paciente) {
            $data[] = array(
                'value'         => $paciente->apellido1 . ' ' . $paciente->apellido2 . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2,
                'id'            => $paciente->idpaciente,
                'nombre1'       => $paciente->nombre1,
                'nombre2'       => $paciente->nombre2,
                'apellido1'     => $paciente->apellido1,
                'apellido2'     => $paciente->apellido2,
                'ciudad'        => $paciente->ciudad,
                'telefono1'     => $paciente->telefono1,
                'telefono2'     => $paciente->telefono2,
                'telefono3'     => $paciente->telefono3,
                'direccion'     => $paciente->direccion,
                'barrio'        => $paciente->barrio,
                'parroquia'     => $paciente->parroquia,
                'canton'        => $paciente->canton,
                'provincia'     => $paciente->provincia,
                'zona_ur'       => $paciente->zona_ur,
                'grupo_cultural' => $paciente->grupo_cultural,
                'edad'          => $paciente->edad,
                'sexo'          => $paciente->sexo,
                'estadocivil'   => $paciente->estadocivil,
                'instruccion'   => $paciente->instruccion,
                'ocupacion'     => $paciente->ocupacion,
                'empresa_trabajo' => $paciente->empresa_trabajo,
                'id_seguro'     => $paciente->id_seguro,
                'referido'      => $paciente->referido,
                'llamar_a'      => $paciente->apellido1familiar . ' ' . $paciente->apellido2familiar . ' ' . $paciente->nombre1familiar . ' ' . $paciente->nombre2familiar,
                'parentesco'    => $paciente->parentesco_afinidad,
                'telefono_llamar' => $paciente->telefono3,
                'direccion_familiar' => $paciente->direccion_familiar,
                'forma_llegada' => $paciente->forma_llegada,
                'fuente_informacion' => $paciente->fuente_informacion,
                'telefono_inst_per_paci' => $paciente->telefono_inst_per_paci,
                'fecha_nacimiento'  => $paciente->fecha_nacimiento,

            );
        }
        // print_r($clientes);
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function obtener_informacion(Request $request)
    {

        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $nombre_encargado   = $request['nombre'];
        $data               = null;
        $nuevo_nombre       = explode(' ', $nombre_encargado);
        $seteo              = "%";

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) 
                  as completo, telefono1, telefono2,
                     id_seguro, id_pais, sexo, id, estadocivil,
                     cedulafamiliar, religion, fecha_nacimiento,
                     trabajo, lugar_nacimiento, alergias, ciudad,
                     gruposanguineo, direccion, antecedentes_pat,
                     antecedentes_fam, ocupacion, telefono_llamar
                  FROM paciente
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "'";


        $nombres = DB::select($query);

        foreach ($nombres as $nombre) {
            $jquery = DB::table('seguros')->where('id', $nombre->id_seguro)->first();
            $pais = DB::table('pais')->where('id', $nombre->id_pais)->first();
            $data[] = array(
                'value' =>  $nombre->completo,
                'fecha'             =>  $nombre->fecha_nacimiento,
                'telefono1'         =>  $nombre->telefono1,
                'telefono2'         =>  $nombre->telefono2,
                'id_pais'           =>  $nombre->id_pais,
                'ocupacion'         =>  $nombre->ocupacion,
                'seguro'            =>  $nombre->id_seguro,
                'sexo'              =>  $nombre->sexo,
                'id'                =>  $nombre->id,
                'estadoc'           =>  $nombre->estadocivil,
                'cedula'            =>  $nombre->cedulafamiliar,
                'religion'          =>  $nombre->religion,
                'alergia'           =>  $nombre->alergias,
                'lugar_nacimiento'  =>  $nombre->lugar_nacimiento,
                'ciudad'            =>  $nombre->ciudad,
                'grupos'            =>  $nombre->gruposanguineo,
                'direccion'         =>  $nombre->direccion,
                'antp'              =>  $nombre->antecedentes_pat,
                'antf'              =>  $nombre->antecedentes_fam,
                'tipo_seguro'       =>  $jquery->nombre,
                'id_pais'           =>  $pais->nombre,
                'telefono_llamar'   =>  $nombre->telefono_llamar
            );
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

        return $data;
    }

    public function busca_paciente($id)
    {

        //$paciente = Paciente::find($id);
        $paciente = Paciente::where('paciente.id', $id)
            ->leftjoin('ho_datos_paciente as hopac', 'hopac.id_paciente', 'paciente.id')
            ->select('paciente.*', 'hopac.*')
            ->first();
        //dd($paciente);
        if (!is_null($paciente)) {
            return $paciente;
        } else {
            return 'no';
        }
    }

    public function guardar_atencio_motivo(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $atencio_motivo = new Hosp_atencion_fomulario008();
        $atencio_motivo->id_emer                = $request->id_paciente;
        $atencio_motivo->hora                   = $request->hora;
        $atencio_motivo->causa                  = $request->causa;
        $atencio_motivo->grupo_sanguineo        = $request->sanguineo_factor;
        $atencio_motivo->notificacion_policia   = $request->notificacion_policial;
        $atencio_motivo->save();

        return back()->with('message_atencio', 'Se ha guardado correctamente la informacion !');
    }

    public function guardar_enferm_actual_revsion(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $enferm_actu_revi = new Hosp_revision_formulario008();
        $enferm_actu_revi->id_emer              = $request->id_paciente;
        $enferm_actu_revi->via_area             = $request->via_area;
        $enferm_actu_revi->condicion_sistemas   = $request->condicion_sistemas;
        $enferm_actu_revi->save();

        return back()->with('message_enfer_actu_revi', 'Se ha guardado correctamente la informacion !');
    }

    public function guardar_accd_viol_intx(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $viol_intox = new Hosp_accidente_formulario008();
        $viol_intox->id_emer            = $request->id_paciente;
        //$viol_intox->fecha_hora         = $request->fecha_hora;
        $viol_intox->lugar_evento       = $request->lugar_evento;
        $viol_intox->direccion_evento   = $request->direccion_evento;
        $viol_intox->custodia_policial  = $request->custodia_policial;
        $viol_intox->observacion        = $request->observacion;
        $viol_intox->aliento_etilico    = $request->aliento_etilico;
        $viol_intox->valor_alcocheck    = $request->valor_alcocheck;

        $viol_intox->save();

        return back()->with('message_viol_intox', 'Se ha guardado correctamente la informacion !');
    }

    public function guardar_ante_pers_familiar(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $antecedentes = new Hosp_antecedentes_formulario008();
        $antecedentes->id_emer  = $request->id_paciente;
        $antecedentes->clinico  = $request->clinico;

        $antecedentes->save();
        return back()->with('message_antecedentes', 'Se ha guardado correctamente la informacion !');
    }

    public function guardar_signos_vitales(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $signos_vitales = new Hosp_signos_vitales_formulario008();
        dd($signos_vitales);
        $signos_vitales->id_emer            = $request->id_paciente;
        $signos_vitales->presion_arterial   = $request->presion_arterial;
        $signos_vitales->cardiaca           = $request->cardiaca;
        $signos_vitales->respiratoria       = $request->respiratoria;
        $signos_vitales->temp_bucal         = $request->temp_bucal;
        $signos_vitales->temp_axilar        = $request->temp_axilar;
        $signos_vitales->peso_kg            = $request->peso_kg;
        $signos_vitales->talla              = $request->talla;
        $signos_vitales->ocultar            = $request->ocultar;
        $signos_vitales->verbal             = $request->verbal;
        $signos_vitales->motora             = $request->motora;
        $signos_vitales->total              = $request->total;
        $signos_vitales->reaccion_pupilar_d = $request->reaccion_pupilar_d;
        $signos_vitales->reaccion_pupilar_i = $request->reaccion_pupilar_i;
        $signos_vitales->llenado_capilar    = $request->llenado_capilar;
        $signos_vitales->satura_oxigeno     = $request->satura_oxigeno;

        $signos_vitales->save();

        return back()->with('message_signos_vitales', 'Se ha guardado correctamente la informacion !');
    }

    public function guardar_emer_obstetrica(Request $request)
    {

        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $obstetrica = new Hosp_obstetrica_formulario008();

        $obstetrica->id_emer            = $request->id_paciente;
        $obstetrica->gestas             = $request->gestas;
        $obstetrica->partos             = $request->partos;
        $obstetrica->abortos            = $request->abortos;
        $obstetrica->cesareas           = $request->cesareas;
        $obstetrica->fec_mestruacion    = $request->fec_mestruacion;
        $obstetrica->fec_parto          = $request->fec_parto;
        $obstetrica->nivel_riesgo       = $request->nivel_riesgo;
        $obstetrica->semana_gestacion   = $request->semana_gestacion;
        $obstetrica->movimiento_fetal   = $request->movimiento_fetal;
        $obstetrica->frec_fetal         = $request->frec_fetal;
        $obstetrica->membranas_rotas    = $request->membranas_rotas;
        $obstetrica->tiempo_ruptura     = $request->tiempo_ruptura;
        $obstetrica->altura_uterina     = $request->altura_uterina;
        $obstetrica->presentacion       = $request->presentacion;
        $obstetrica->dilatacion         = $request->dilatacion;
        $obstetrica->borramiento        = $request->borramiento;
        $obstetrica->plano              = $request->plano;
        $obstetrica->pelvis_util        = $request->pelvis_util;
        $obstetrica->sangramiento       = $request->sangramiento;
        $obstetrica->contracciones      = $request->contracciones;
        $obstetrica->save();

        return back()->with('message_obstetrica', 'Se ha guardado correctamente la informacion !');
    }

    public function modal_tratamiento(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        return view('hospital/emergencia/modalTratamiento');
    }

    public function guardar_tratamiento(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $tratamiento = new Hosp_tratamiento_formulario008();

        $tratamiento->id_emer                   = $request->id_paciente;
        $tratamiento->nombre                    = $request->nombre;
        $tratamiento->presentacion              = $request->presentacion;
        $tratamiento->cantidad                  = $request->cantidad;
        $tratamiento->concentracion             = $request->concentracion;
        $tratamiento->dosis                     = $request->dosis;
        $tratamiento->unidad                    = $request->unidad;
        $tratamiento->via                       = $request->via;
        $tratamiento->frecuencia                = $request->frecuencia;
        $tratamiento->duracion                  = $request->duracion;
        $tratamiento->indicaciones_medicinas    = $request->indicaciones_medicinas;
        $tratamiento->save();

        return back()->with('message_tratamiento', 'Se ha guardado correctamente la informacion !');
    }

    public function guardar_formulario008_alta(Request $request)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $formulario008_alta = new Hosp_formulario008_alta();
        $formulario008_alta->id_emer                = $request->id_paciente;
        $formulario008_alta->lugar_alta             = $request->lugar_alta;
        $formulario008_alta->condicion_alta         = $request->condicion_alta;
        $formulario008_alta->dia_incapacidad        = $request->dia_incapacidad;
        $formulario008_alta->servicio_referencia    = $request->servicio_referencia;
        $formulario008_alta->establecimiento        = $request->establecimiento;
        $formulario008_alta->causa_alta             = $request->causa_alta;
        $formulario008_alta->desc_alta              = $request->desc_alta;
        $formulario008_alta->fecha_hora_emision     = $request->fecha_hora_emision;
        $formulario008_alta->nombre_profesional     = $request->nombre_profesional;
        $formulario008_alta->firma                  = $request->firma;
        $formulario008_alta->save();

        return back()->with('message_formulario008_alta', 'Se ha guardado correctamente la informacion !');
    }

    public function historial_atencion(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_atencion_fomulario008::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'ingreso_emer_008.id_paciente', 'paciente.created_at')
            ->get();

        //dd($nombre);
        return view('hospital/emergencia/historial_atencion008', ['id_paciente' => $id_paciente, 'dato_paciente' => $dato_paciente, 'nombre' => $nombre]);
    }

    public function historial_revision(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_revision_formulario008::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        return view('hospital/emergencia/historial_revision008', ['id_paciente' => $id_paciente, 'nombre' => $nombre, 'dato_paciente' => $dato_paciente]);
    }

    public function historial_accidente(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_accidente_formulario008::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        return view('hospital/emergencia/historial_accidente008', ['id_paciente' => $id_paciente, 'nombre' => $nombre, 'dato_paciente' => $dato_paciente]);
    }

    public function historial_antecendentes(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_antecedentes_formulario008::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        return view('hospital/emergencia/historial_antecendentes008', ['id_paciente' => $id_paciente, 'nombre' => $nombre, 'dato_paciente' => $dato_paciente]);
    }

    public function historial_signos_vitales(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_signos_vitales_formulario008::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        return view('hospital/emergencia/historial_signos_vitales008', ['id_paciente' => $id_paciente, 'nombre' => $nombre, 'dato_paciente' => $dato_paciente]);
    }

    public function historial_obstetrica(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_obstetrica_formulario008::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        return view('hospital/emergencia/historial_obstetrica008', ['id_paciente' => $id_paciente, 'nombre' => $nombre, 'dato_paciente' => $dato_paciente]);
    }

    public function historial_tratamiento(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_tratamiento_formulario008::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        return view('hospital/emergencia/historial_tratamiento008', ['id_paciente' => $id_paciente, 'nombre' => $nombre, 'dato_paciente' => $dato_paciente]);
    }

    public function historial_alta(Request $request, $id_paciente)
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $dato_paciente = Hosp_formulario008_alta::where('id_emer', '=', $id_paciente)->get();

        $nombre = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'Ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        return view('hospital/emergencia/historial_alta008', ['id_paciente' => $id_paciente, 'nombre' => $nombre, 'dato_paciente' => $dato_paciente]);
    }
    public function formulario008_pdf($id, Request $request)
    {
        $empresa        = Empresa::where('prioridad', 2)->first();
        $log_solicitud = Ho_Log_Solicitud::where('id_ho_solicitud', $id)->where('estado_paso', '2')->first();
        //dd($log_solicitud);
        $solicitudemer = Ho_Solicitud::find($id);
        //dd($solicitudemer);
        $historia = $log_solicitud->agenda->historia_clinica;
        $verificar = Ho_Lesiones008::where('id_008', $id)->first();
        $verificacion = Ho_Traspaso_Sala008::where('id_solicitud', $solicitudemer->id)->first();
        $form008    = $solicitudemer->form008->first();
        $hc            = $form008->agenda->historia_clinica;
        $alergias = $solicitudemer->paciente->a_alergias;
        $txt_al = '';
        $cont = 0;
        $pasos = Ho_Solicitud::join('historiaclinica  as hc', 'hc.id_agenda', 'ho_solicitud.id_agenda')
            ->join('hc_cie10 as hi', 'hi.hcid', 'hc.hcid')->where('ho_solicitud.id', $id)->select('hi.*')->get();
        $ocular = Ho_Glasgow::where('tipo', '1')->where('estado', '1')->get();
        $verbal = Ho_Glasgow::where('tipo', '2')->where('estado', '1')->get();
        $motora = Ho_Glasgow::where('tipo', '3')->where('estado', '1')->get();
        $receta = hc_receta::where('id_hc', $historia->hcid)->first();

        // return view('hospital.emergencia.formulario008_pdf', ['pasos'=>$pasos,'solicitudemer' => $solicitudemer, 'txt_al' => $txt_al, 'alergias' => $alergias, 'form008' => $form008, 'hc' => $hc, 'ocular' => $ocular, 'verbal' => $verbal, 'motora' => $motora, 'verificar' => $verificar,  'verificacion' => $verificacion,  'detalles' => $receta->detalles, 'receta' => $receta, 'empresa' => $empresa]);

        $view = \View::make('hospital.emergencia.formulario008_pdf', ['pasos' => $pasos, 'solicitudemer' => $solicitudemer, 'txt_al' => $txt_al, 'alergias' => $alergias, 'form008' => $form008, 'hc' => $hc, 'ocular' => $ocular, 'verbal' => $verbal, 'motora' => $motora, 'verificar' => $verificar,  'verificacion' => $verificacion,  'detalles' => $receta->detalles, 'receta' => $receta, 'empresa' => $empresa])->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        $pdf->setOptions(['chroot'  => base_path('/')]);
        return $pdf->stream('formulario008_pdf.pdf');
    }
    public function pdf_formulario008_nuevo($id, Request $request) //
    {
        $empresa        = Empresa::where('prioridad', 1)->first(); //xq era prioridad 2?

        $log_solicitud = Ho_Log_Solicitud::where('id_ho_solicitud', $id)->where('estado_paso', '2')->first();
        //dd($log_solicitud);
        $solicitudemer = Ho_Solicitud::find($id);
        //dd($solicitudemer);
        $historia = $log_solicitud->agenda->historia_clinica;
        $verificar = Ho_Lesiones008::where('id_008', $id)->first();
        $verificacion = Ho_Traspaso_Sala008::where('id_solicitud', $solicitudemer->id)->first();

        $form008    = $solicitudemer->form008->first();
        $hc       = $solicitudemer->agenda->historia_clinica;
        $hc_cie10_ingreso = Hc_Cie10::where('hc_cie10.hc_id_procedimiento', $hc->hc_procedimientos->id)->get();
        $alergias = $solicitudemer->paciente->a_alergias;
        $txt_al = '';
        $cont = 0;
        $paciente = $solicitudemer->paciente;
        $glasgow = $form008->glasgow;
        $signos_vitales = $form008->consantes_vitales;
        $datos_pac = $solicitudemer->paciente->ho_datos_paciente;
        $examen_fisico = ($form008->examen_fisico);
        $user_crea = User::where('id', $solicitudemer->id_usuariocrea)->first();
        $usuario = User::where('id', $form008->id_usuariocrea)->first();
        $pasos = Ho_Solicitud::join('historiaclinica  as hc', 'hc.id_agenda', 'ho_solicitud.id_agenda')
            ->join('hc_cie10 as hi', 'hi.hcid', 'hc.hcid')->where('ho_solicitud.id', $id)->select('hi.*')->get();

        $ocular = Ho_Glasgow::where('tipo', '1')->where('estado', '1')->get();
        $verbal = Ho_Glasgow::where('tipo', '2')->where('estado', '1')->get();
        $motora = Ho_Glasgow::where('tipo', '3')->where('estado', '1')->get();
        $receta = hc_receta::where('id_hc', $historia->hcid)->first();
        $firma     = Firma_Usuario::where('id_usuario', $solicitudemer->id_usuariocrea)->first();


        $view = \View::make('hospital/emergencia/formulario008_nuevo', ['hc_cie10_ingreso' => $hc_cie10_ingreso, 'datos_pac' => $datos_pac, 'paciente' => $paciente, 'examen_fisico' => $examen_fisico, 'signos_vitales' => $signos_vitales, 'glasgow' => $glasgow, 'pasos' => $pasos, 'user_crea' => $user_crea, 'solicitudemer' => $solicitudemer, 'usuario' => $usuario, 'txt_al' => $txt_al, 'alergias' => $alergias, 'form008' => $form008, 'hc' => $hc, 'ocular' => $ocular, 'verbal' => $verbal, 'motora' => $motora, 'verificar' => $verificar,  'verificacion' => $verificacion,  'detalles' => $receta->detalles, 'receta' => $receta, 'empresa' => $empresa, 'firma' => $firma])->render();
        $pdf  = \App::make('dompdf.wrapper');


        $pdf->loadHTML($view);
        return $pdf->stream('formulario008_nuevo.pdf');
    }
}
