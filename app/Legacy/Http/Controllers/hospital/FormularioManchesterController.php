<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Paciente;
use Sis_medico\Pacientes_ext;
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
use Sis_medico\Hosp_Triaje_Manchester;
use Sis_medico\Hosp_Triaje_Emergencia;
use Sis_medico\Ho_Tipo_Emergencia;
use Sis_medico\Ho_Prioridad_Emergencia;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Triaje_Manchester;
use Sis_medico\Ho_Glasgow;
use Sis_medico\HoEstadoPaso;
use Sis_medico\User;


class FormularioManchesterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        //dd($rolUsuario,$opcion);
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    public function emergencialista()
    {
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $paciente_emegerncia = Ingreso_emer_008::all();

        return view('hospital/emergencia/emergencia')->with('paciente_emegerncia', $paciente_emegerncia);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function formulariomanchester()
    {

        $tipos_emergencia = Ho_Tipo_Emergencia::where('estado', '1')->get();
        $prioridad = Ho_Prioridad_Emergencia::where('estado', '1')->get();
        $ocular = Ho_Glasgow::where('tipo', '1')->where('estado', '1')->get();
        $verbal = Ho_Glasgow::where('tipo', '2')->where('estado', '1')->get();
        $motora = Ho_Glasgow::where('tipo', '3')->where('estado', '1')->get();

        // dd($prioridad);

        return view('hospital/emergencia/formulariomanchester', ['tipos_emergencia' => $tipos_emergencia, 'prioridad' => $prioridad, 'ocular' => $ocular, 'verbal' => $verbal, 'motora' => $motora]);
    }
    public function resultadomanchester(Request $request, $id)
    {

        $tipos_emergencia = Ho_Tipo_Emergencia::where('estado', '1')->get();
        $prioridad = Ho_Prioridad_Emergencia::where('estado', '1')->get();
        $ocular = Ho_Glasgow::where('tipo', '1')->where('estado', '1')->get();
        $verbal = Ho_Glasgow::where('tipo', '2')->where('estado', '1')->get();
        $motora = Ho_Glasgow::where('tipo', '3')->where('estado', '1')->get();

        $resultados = Ho_Triaje_Manchester::where('ho_triaje_manchester.id', $id)
            ->join('ho_solicitud as ho_s', 'ho_s.id', 'ho_triaje_manchester.id_ho_solicitud')
            ->join('paciente as p', 'p.id', 'ho_s.id_paciente')
            ->select('ho_triaje_manchester.*', 'ho_s.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2')
            ->first();

        return view('hospital/emergencia/resultadomanchester', ['datos' => $resultados, 'tipos_emergencia' => $tipos_emergencia, 'prioridad' => $prioridad, 'id' => $id, 'ocular' => $ocular, 'verbal' => $verbal, 'motora' => $motora]);
    }


    public function buscar_paciente(Request $request)
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

    public function obtener_informacion(Request $request)
    {


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

    //Ingreso a Manchester
    public function guardar_manchester2(Request $request)
    {
        dd("nooo");
        $opcion = '57';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        // estos campos te faltan en la tabla
        $ingreso_emergecia = new Hosp_Triaje_Manchester();
        // aqui debees preguntar si no existe el id paciente lo creas
        // ESTOY HACIENDO MAL :'(
        if (($request->paciente) != null) {
            $consulta_paciente = Paciente::where('id', $request->paciente)->first();
            //me equivoque retornaba null jajaja
            if ($consulta_paciente != null) {
                $ingreso_emergecia->id_admision             = $request->admision;
                $ingreso_emergecia->id_paciente             = $request->paciente;
                $ingreso_emergecia->num_revision            = $request->num_rev;
                $ingreso_emergecia->nombre_paciente         = $request->nombre;
                $ingreso_emergecia->prioridad               = $request->prioridad;
                $ingreso_emergecia->tipo_emergencia         = $request->tipos_emergencia;
                $ingreso_emergecia->motivo_consulta         = $request->mot_consulta;
                $ingreso_emergecia->embarazo_puerperio      = $request->embarazo_p;
                $ingreso_emergecia->presiona_sistolica      = $request->presion_art_sis;
                $ingreso_emergecia->presiona_diastolica     = $request->presion_art_dias;
                $ingreso_emergecia->frec_cardiaca           = $request->frec_cardiaca;
                $ingreso_emergecia->frec_resp               = $request->frec_resp;
                $ingreso_emergecia->temp                    = $request->temperatura;
                $ingreso_emergecia->talla                   = $request->talla;
                $ingreso_emergecia->peso                    = $request->peso;
                $ingreso_emergecia->resp_ocular             = $request->resp_ocular;
                $ingreso_emergecia->resp_verbal             = $request->resp_verbal;
                $ingreso_emergecia->resp_motora             = $request->resp_motora;
                $ingreso_emergecia->reaccion_pupilar        = $request->reac_pupilar;
                $ingreso_emergecia->llenado_capilar         = $request->total_capilar;
                $ingreso_emergecia->sat_oxigeno             = $request->satura_oxigeno;
                $ingreso_emergecia->estado_conciencia       = $request->est_conciencia;
                $ingreso_emergecia->save();
            } else {
                //aqui guardas al paciente HAY QUE CREAR UNA FUNCION QUE REMPLACE LOS ESPACIOS POR UN ARRAY ALV
                $clasificar_nombres = explode(" ", $request->nombre);
                //crea create paciente
                Paciente::create([
                    'id' => $request->paciente,
                    'nombre1' => strtoupper($clasificar_nombres[0]),
                    'nombre2' => strtoupper($clasificar_nombres[1]),
                    'apellido1' => strtoupper($clasificar_nombres[2]),
                    'apellido2' => strtoupper($clasificar_nombres[3]),
                    'telefono1' => '',
                    'telefono2' => '',
                    'nombre1familiar' => strtoupper($clasificar_nombres[0]),
                    'nombre2familiar' => strtoupper($clasificar_nombres[1]),
                    'apellido1familiar' => strtoupper($clasificar_nombres[2]),
                    'apellido2familiar' => strtoupper($clasificar_nombres[3]),
                    'parentesco' => $request['parentesco'],
                    'parentescofamiliar' => $request['parentesco'],
                    'id_pais' => '1', //por ahora dejemoslo en Ecuador pero deberias tener un combo de pais en tu formulario aqui te decia que debias poner que pais es pero por default ecuador el 1 es ecuador
                    'fecha_nacimiento' => '17/6/1980', //también esto le puse por default porque los campos son requeridos
                    'telefono3' => '',
                    'tipo_documento' => 1,
                    'imagen_url' => ' ',
                    'menoredad' => '0',
                    'id_seguro' => '1', // esto si no se jajaja pero le puse 1 jajajaja
                    'id_usuario' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                ]);
                //despues de hacer todo eso recién guarde exactamente como lo de arriba
                // de ahi ingresar lo que ya ingresaba porque asi en el id_paciente ya tiene a quién referenciar 
                $ingreso_emergecia->id_admision             = $request->admision;
                $ingreso_emergecia->id_paciente             = $request->paciente;
                $ingreso_emergecia->num_revision            = $request->num_rev;
                $ingreso_emergecia->nombre_paciente         = $request->nombre;
                $ingreso_emergecia->prioridad               = $request->prioridad;
                $ingreso_emergecia->tipo_emergencia         = $request->tipos_emergencia;
                $ingreso_emergecia->motivo_consulta         = $request->mot_consulta;
                $ingreso_emergecia->embarazo_puerperio      = $request->embarazo_p;
                $ingreso_emergecia->presiona_sistolica      = $request->presion_art_sis;
                $ingreso_emergecia->presiona_diastolica     = $request->presion_art_dias;
                $ingreso_emergecia->frec_cardiaca           = $request->frec_cardiaca;
                $ingreso_emergecia->frec_resp               = $request->frec_resp;
                $ingreso_emergecia->temp                    = $request->temperatura;
                $ingreso_emergecia->talla                   = $request->talla;
                $ingreso_emergecia->peso                    = $request->peso;
                $ingreso_emergecia->resp_ocular             = $request->resp_ocular;
                $ingreso_emergecia->resp_verbal             = $request->resp_verbal;
                $ingreso_emergecia->resp_motora             = $request->resp_motora;
                $ingreso_emergecia->reaccion_pupilar        = $request->reac_pupilar;
                $ingreso_emergecia->llenado_capilar         = $request->total_capilar;
                $ingreso_emergecia->sat_oxigeno             = $request->satura_oxigeno;
                $ingreso_emergecia->estado_conciencia       = $request->est_conciencia;
                $ingreso_emergecia->save();
            }
        }


        return back()->with('message', 'Ha ingresado la Emergacia con Excito !');
    }


    public function guardar_manchester(Request $request)
    {
        //dd("aquiiii");
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $id_paciente = $request['id_paciente'];
        $paciente = Paciente::find($id_paciente);
        $id_doctor = Auth::user()->id;

        $usuario = User::find($id_paciente);
        if (is_null($usuario)) {
            User::create([
                'id'                    => $id_paciente,
                'nombre1'               => $request->nombre1,
                'nombre2'               => $request->nombre2,
                'apellido1'             => $request->apellido1,
                'apellido2'             => $request->apellido2,
                'password'              => bcrypt($id_paciente),
                'imagen_url'            => ' ',
                'id_tipo_usuario'       => '2',
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,

            ]);
        }

        if (!is_null($paciente)) {
            $paciente->update([
                'nombre1'               => $request->nombre1,
                'nombre2'               => $request->nombre2,
                'apellido1'             => $request->apellido1,
                'apellido2'             => $request->apellido2,
                'sexo'                  => $request->sexo,
            ]);
        } else {
            Paciente::create([
                'id'                    => $id_paciente,
                'nombre1'               => $request->nombre1,
                'nombre2'               => $request->nombre2,
                'apellido1'             => $request->apellido1,
                'apellido2'             => $request->apellido2,
                'id_seguro'             =>  '1',
                'imagen_url'            =>  ' ',
                'id_usuario'            => $id_paciente,
                'menoredad'             =>  '0',
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'sexo'                  => $request->sexo,
            ]);
        }

        $arr_solicitud = [
            'id_paciente'           => $id_paciente,
            'fecha_ingreso'         => date('Y-m-d H:i:s'),
            'estado_paso'           => '1',
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
        ];

        $ho_solicitud = Ho_solicitud::insertGetId($arr_solicitud);

        $solicitud_log = [
            'id_ho_solicitud'       => $ho_solicitud,
            //'id_agenda'             => $id_agenda,
            'estado_paso'           => '1',
            'fecha_ingreso'         => date('Y-m-d'),
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
        ];

        $log = Ho_Log_Solicitud::create($solicitud_log);

        $arr_manchester = [
            'id_ho_solicitud'       => $ho_solicitud,
            'num_revision'          => $request['num_rev'],
            'prioridad'             => $request['prioridad'],
            'tipo_emergencia'       => $request['tipos_emergencia'],
            'motivo_consulta'       => $request['mot_consulta'],
            'embarazo_puerperio'    => $request['embarazo_p'],
            'presion_sistolica'     => $request['presion_art_sis'],
            'presion_diastolica'    => $request['presion_art_dias'],
            'frec_cardiaca'         => $request['frec_cardiaca'],
            'frec_resp'             => $request['frec_resp'],
            'temp'                  => $request['temperatura'],
            'talla'                 => $request['talla'],
            'peso'                  => $request['peso'],
            'resp_ocular'           => $request['resp_ocular'],
            'resp_verbal'           => $request['resp_verbal'],
            'resp_motora'           => $request['resp_motora'],
            'reaccion_pupilar'      => $request['reac_pupilar'],
            'llenado_capilar'       => $request['total_capilar'],
            'sat_oxigeno'           => $request['satura_oxigeno'],
            'estado_conciencia'     => $request['est_conciencia'],
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
        ];

        Ho_Triaje_Manchester::create($arr_manchester);
    }

    public function update_manchester(Request $request)
    {

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        DB::beginTransaction();
        try {
            $id_paciente = $request['id_paciente'];
            $paciente = Paciente::find($id_paciente);

            $manchester = Ho_Triaje_Manchester::find($request['id_manchester']);

            $ho_paso = HoEstadoPaso::where('nombre_paso','like','%emergencia%')->first();

            if (!is_null($manchester)) {

                $arr_up_manchester = [
                    'num_revision'          => $request['num_rev'],
                    'prioridad'             => $request['prioridad'],
                    'tipo_emergencia'       => $request['tipos_emergencia'],
                    'motivo_consulta'       => $request['mot_consulta'],
                    'embarazo_puerperio'    => $request['embarazo_p'],
                    'presion_sistolica'     => $request['presion_art_sis'],
                    'presion_diastolica'    => $request['presion_art_dias'],
                    'frec_cardiaca'         => $request['frec_cardiaca'],
                    'frec_resp'             => $request['frec_resp'],
                    'temp'                  => $request['temperatura'],
                    'talla'                 => $request['talla'],
                    'peso'                  => $request['peso'],
                    'resp_ocular'           => $request['resp_ocular'],
                    'resp_verbal'           => $request['resp_verbal'],
                    'resp_motora'           => $request['resp_motora'],
                    'reaccion_pupilar'      => $request['reac_pupilar'],
                    'llenado_capilar'       => $request['total_capilar'],
                    'sat_oxigeno'           => $request['satura_oxigeno'],
                    'estado_conciencia'     => $request['est_conciencia'],
                    'ip_modificacion'       => $ip_cliente,
                    'id_usuariomod'         => $idusuario,
                ];

                $manchester->update($arr_up_manchester);
            }

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'paso' => $ho_paso->id];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }
}