<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Egreso_Empleado;
use Sis_medico\Ct_Nomina;
use Sis_medico\Ct_Rh_Area;
use Sis_medico\Ct_Rh_Estado_Civil;
use Sis_medico\Ct_Rh_Horario;
use Sis_medico\Ct_Rh_Nivel_Academico;
use Sis_medico\Ct_Rh_Pago_Beneficio;
use Sis_medico\Ct_Rh_Valores;
use Sis_medico\Ct_Tipo_Rol;
use Sis_medico\Empresa;
use Sis_medico\Especialidad;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_usuario;
use Sis_medico\Pais;
use Sis_medico\TipoUsuario;
use Sis_medico\User;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Detalle_Rol;
use Sis_medico\Ct_Sucursales;
use Sis_medico\CtLogNomina;

class NominaController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    private function log_nomina($tipo)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $array = [
            'tipo' => $tipo,
            'id_usuariocrea' => $id_usuario,
            'id_usuariomod'  => $id_usuario,
            'ip_creacion'    => $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];     
        CtLogNomina::create($array);
        
    }

    public function index(Request $request)
    {
        if ($this->rol()) {


            return response()->view('errors.404');
        }
        $this->log_nomina('LISTA NOMINA');
        //$principales = Ct_Nomina::where('estado', '1')->with('usuario')->with('empresa')->orderby('id', 'desc')->paginate(5);

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $principales = DB::table('ct_nomina')
            ->where('ct_nomina.id_empresa', $id_empresa)
            ->where('ct_nomina.estado', '1')
            ->join('users as u', 'u.id', 'ct_nomina.id_user')
            ->select('u.apellido1', 'u.apellido2', 'u.nombre1', 'u.nombre2', 'ct_nomina.*')
            ->orderby('u.apellido1')
            ->get();
        //dd($principales);
        //$empresas = Empresa::all();
        $cajas = [];
        if (!is_null($empresa)) {
        	$cajas      = Ct_Caja::where('id_empresa',$empresa->id)->where('estado','1')->get();    
        }

        $sucursales = Ct_Sucursales::where('estado','1')->where('id_empresa',$id_empresa)->where('uso_nomina','1')->get();
	    $sueldo_basico = Ct_Rh_Valores::where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo','3')->first();

        //return view('contable/nomina/index', ['registros' => $principales, 'empresas' => $empresas, 'empresa' => $empresa]);
        return view('contable/nomina/index', ['sueldo_basico' => $sueldo_basico,'registros' => $principales, 'empresa' => $empresa, 'cajas' => $cajas, 'sucursales' => $sucursales]);
    }

    public function crear(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $empresas     = Empresa::all();
        $empresa      = Empresa::findorfail($request->session()->get('id_empresa'));
        $ct_sucursales = Ct_Sucursales::where("estado", 1)->where('uso_nomina',1)->where('id_empresa',$empresa->id)->get();
        if($ct_sucursales->count() == 0) {
            $ct_sucursales = Ct_Sucursales::where("estado", 1)->where('id_empresa',$empresa->id)->get();
        }
        $tipo         = TipoUsuario::where('estado', 1)->get();
        $nominatipo   = TipoUsuario::where('nombre', 'NOMINA')->first();
        $pais         = pais::all();
        $tipousuarios = tipousuario::all();
        $especialidad = especialidad::all();
        $cajas        = Ct_Caja::where('id_empresa',$empresa->id)->where('estado','1')->get();
        $area            = Ct_Rh_Area::all();
        $estado_civil    = Ct_Rh_Estado_Civil::all();
        $horario         = Ct_Rh_Horario::all();
        $nivel_academico = Ct_Rh_Nivel_Academico::all();
        $pago_beneficio  = Ct_Rh_Pago_Beneficio::all();
        $lista_banco     = Ct_Bancos::all();
        $aporte_personal = Ct_Rh_Valores::where('tipo', '1')->where('estado', '1')->where('id_empresa', $empresa->id)->get();


        return view('contable/nomina/create', ['ct_sucursales' => $ct_sucursales, 'empresa' => $empresa, 'empresas' => $empresas, 'pais' => $pais, 'tipousuarios' => $tipousuarios, 'especialidad' => $especialidad, 'nominaTipo' => $nominatipo, 'area' => $area, 'estado_civil' => $estado_civil, 'horario' => $horario, 'nivel_academico' => $nivel_academico, 'pago_beneficio' => $pago_beneficio, 'lista_banco' => $lista_banco, 'aporte_personal' => $aporte_personal, 'cajas' => $cajas]);
    }

    public function identificacion(Request $request)
    {

        $usuario = User::find($request['identificacion']);
        $existe  = Ct_Nomina::where('id_user', $request['identificacion'])
            ->where('id_empresa', $request['id_empresa'])->first();
        $dato = ['existe' => $existe, 'usuario' => $usuario];
        return $dato;
    }

    private function validateInput($request)
    {

        $this->validate($request, []);
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        $check_conyuge = 0;
        if ($request['verificar_conyuge'] == "on") {
            $check_conyuge = 1;
        }
        $check_pasante = 0;
        if ($request['pasante'] == "on") {
            $check_pasante = 1;
        }
        //$caja       = Ct_Caja::find($request->id_caja);
        //$sucursal   = $caja->sucursal;
        
        $id = $request['identificacion'];
        date_default_timezone_set('America/Guayaquil');

        $id_empresa = $request->session()->get('id_empresa');

        $user = User::find($id);

        $horas_dia = "";

        if($request['jornada'] != '0'){
            $rules = [
                //'parentesco' => 'required|in:Principal|son_iguales:'.$request['id'].','.$request['id_prin'],
                'dias_laborables'       => 'required | max_consulta: 0,'.$request['dias_laborables'],
                'horas_mes'             => 'required | max_consulta: 0,'.$request['horas_mes'],
    
            ];
    
            $msj = [
                'dias_laborables.required'         => 'Los dias laborables son requeridos',
                'horas_mes.required'               => 'Los horas al mes son requeridos',
                'dias_laborables.max_consulta'      => 'Dias Laborables no puede ser 0',
                'horas_mes.max_consulta'            => 'Horas al mes no puede ser 0',
    
            ];
            //dd($rules);
            $this->validate($request, $rules, $msj);

            $horas_dia = $request['horas_mes'] / $request['dias_laborables'];
        }

        if (!is_null($user)) {

            $act_tipo = [
                'id_tipo_usuario' => $request['id_tipo_usuario'],
            ];
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PERSONAL",
                'dato_ant1'   => $request['identificacion'],
                'dato1'       => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
                'dato_ant2'   => "TIPO USUARIO: " . $request['id_tipo_usuario'],
            ]);

            $datos_nomina = [

                'id_user'                   => $request['identificacion'],
                'nombres'                   => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
                //'id_empresa'               => $request['id_empresa'],
                'id_empresa'                => $id_empresa,
                'sexo'                      => $request['genero'],
                'etnia'                     => $request['etnia'],
                'check_discapacidad'        => $request['discapacidad'],
                'porcentaje_discapacidad'   => $request['porcent_discapacidad'],
                'numero_cargas'             => $request['numero_carga'],
                'nivel_academico'           => $request['nivel_academico'],
                'mail_opcional'             => $request['mail_opcional'],
                'estado_civil'              => $request['estado_civil'],
                'area'                      => $request['area'],
                'cargo'                     => $request['cargo'],
                'estado'                    => $request['estado'],
                'fecha_ingreso'             => $request['fecha_actividad'],
                'pago_fondo_reserva'        => $request['fondo_reserva'],
                'decimo_tercero'            => $request['decimo_tercero'],
                'decimo_cuarto'             => $request['decimo_cuarto'],
                'seguro_privado'            => $request['seguro_privado'],
                'horario'                   => $request['horario'],
                'bono'                      => $request['bono'],
                'impuesto_renta'            => $request['imp_renta'],
                'alimentacion'              => $request['alimentacion'],
                'banco'                     => $request['id_banco'],
                'sueldo_neto'               => $request['sueldo'],
                'numero_cuenta'             => $request['numero_cuenta'],
                'archivo_curriculum'        => $request['curriculum_vitae'],
                'archivo_ficha_tecnica'     => $request['ficha_tecnica'],
                'archivo_ficha_ocupacional' => $request['ficha_ocupacional'],
                'foto'                      => $request['foto'],
                'funciones'                 => $request['funciones'],
                'ip_creacion'               => $ip_cliente,
                'ip_modificacion'           => $ip_cliente,
                'id_usuariocrea'            => $idusuario,
                'id_usuariomod'             => $idusuario,
                'bono_imputable'            => $request['bono_imputable'],
                'fondos_reserva'            => $request['fondos_reserva'],
                'aporte_personal'           => $request['aporte_personal'],
                //'codigo_caja'               => $caja->codigo_caja,
                //'codigo_sucursal'           => $sucursal->codigo_sucursal,
                //'id_caja'                   => $caja->id,
                //'id_sucursal'               => $sucursal->id,
               //'id_caja'                   => $caja->id,
                'extension_conyuge'         => $check_conyuge,
                'movilizacion'              => $request['movilizacion'],
		        'parqueo'                   => $request['parqueo'],
                'id_sucursal'               => $request['sucursal'],
                'pasante'                   => $check_pasante,       
                'jornada'                   => $request['jornada'],
                'dias_laborables'           => $request['dias_laborables'],
                'horas_mes'                 => $request['horas_mes'],
                'sueldo_pasante'            => $request['sueldo_pasante'],
                //'fecha_salida'              => $request['fecha_salida'],
                'cantidad_horas'            => $horas_dia,
                'ocupacion'                 => strtoupper($request['ocupacion']),
             ];

            $id_nomina = Ct_Nomina::insertGetId($datos_nomina);

            //Guardado de Archivo Curriculum Vitae
            $id_curriculum = $request['curriculum_vitae'];
            if (!is_null($id_curriculum)) {
                $nombre_original_curr = $request['curriculum_vitae']->getClientOriginalName();
                $nuevo_nombre         = $nombre_original_curr;
                Storage::disk('archivos_nomina')->put($nuevo_nombre, \File::get($request['curriculum_vitae']));

                $rutaarchivo1 = $nuevo_nombre;
            }

            //Guardado de Archivo Ficha Tecnica
            $nuevo_nombre2 = "";
            $id_ficha = $request['ficha_tecnica'];
            if (!is_null($id_ficha)) {
                $nombre_original_fich = $request['ficha_tecnica']->getClientOriginalName();
                $nuevo_nombre2        = $nombre_original_fich;
                Storage::disk('archivos_nomina')->put($nuevo_nombre2, \File::get($request['ficha_tecnica']));

                $rutaarchivo2 = $nuevo_nombre2;
            }

            //Guardado de Archivo Ficha Ocupacional
            $id_ficha_ocupacional = $request['ficha_ocupacional'];
            if (!is_null($id_ficha_ocupacional)) {
                $nombre_original_fich_ocup = $request['ficha_ocupacional']->getClientOriginalName();
                $nuevo_nombre3             = $nombre_original_fich_ocup;
                Storage::disk('archivos_nomina')->put($nuevo_nombre3, \File::get($request['ficha_ocupacional']));

                $rutaarchivo3 = $nuevo_nombre3;
            }

            //Guardado de Archivo Foto
            $id_foto =$request['foto'];
            if (!is_null($id_foto)){
                $nombre_original_fot = $request['foto']->getClientOriginalName();
                $nuevo_nombre4       = $nombre_original_fot;
                storage :: disk('archivos_nomina')->put($nuevo_nombre4, \File::get($request['foto'])); 

                $rutaarchivo4 = $nuevo_nombre4;
            }

            //Consulta Nomina y Actualiza
            $empl_actual = Ct_Nomina::find($id_nomina);

            //ACTUALIZAR CURRICULUM
            if (!is_null($id_curriculum)) {
                $empl_actual->archivo_curriculum = $rutaarchivo1;
                $empl_actual->ip_modificacion    = $ip_cliente;
                $empl_actual->id_usuariomod      = $idusuario;
                $r2                              = $empl_actual->save();
            }

            //ACTUALIZAR FICHA TECNICA
            if (!is_null($id_ficha)) {
                $empl_actual->archivo_ficha_tecnica = $rutaarchivo2;
                $empl_actual->ip_modificacion       = $ip_cliente;
                $empl_actual->id_usuariomod         = $idusuario;
                $r3                                 = $empl_actual->save();
            }

            //ACTUALIZAR FICHA OCUPACIONAL
            if (!is_null($id_ficha_ocupacional)) {
                $empl_actual->archivo_ficha_ocupacional = $rutaarchivo3;
                $empl_actual->ip_modificacion           = $ip_cliente;
                $empl_actual->id_usuariomod             = $idusuario;
                $r4                                     = $empl_actual->save();
            }

            //ACTUALIZAR Foto
            if (!is_null($id_foto)) {
                $empl_actual->foto = $rutaarchivo4;
                $empl_actual->ip_modificacion           = $ip_cliente;
                $empl_actual->id_usuariomod             = $idusuario;
                $r                                   = $empl_actual->save();
            }
        } else {

            $nominatipo = TipoUsuario::where('nombre', 'NOMINA')->first();

            //Validacion de Campos Requeridos
            $constraints = [

                'identificacion'   => 'required|max:10|unique:users,id,' . $id,
                'nombre1'          => 'required|max:60',
                'nombre2'          => 'max:60',
                'apellido1'        => 'required|max:60',
                'apellido2'        => 'required|max:60',
                'id_pais'          => 'required',
                'ciudad'           => 'required|max:60',
                'direccion'        => 'required|max:255',
                'telefono1'        => 'required|numeric|max:9999999999',
                'telefono2'        => 'required|numeric|max:9999999999',
                'ocupacion'        => 'required|max:60',
                'fecha_nacimiento' => 'required|date',
                'genero'           => 'required',
                'etnia'            => 'required|max:255',
                'nivel_academico'  => 'required',
                'estado_civil'     => 'required',
                'email'            => 'required|email|max:191|unique:users,email,' . $id,
                'area'             => 'required',
                'cargo'            => 'required|max:255',
                'fecha_actividad'  => 'required|date',
                'fondo_reserva'    => 'required',
                'decimo_tercero'   => 'required',
                'decimo_cuarto'    => 'required',
                'seguro_privado'   => 'required',
                'horario'          => 'required',
                'id_banco'         => 'required',
                'numero_cuenta'    => 'required|max:255',
                'sueldo'           => 'required',

            ];

            $mensajes = [

                'identificacion.unique'     => 'La cédula ya se encuentra registrada.',
                'identificacion.required'   => 'Agrega la cédula.',
                'identificacion.max'        => 'La cédula no puede ser mayor a :max caracteres.',
                'nombre1.required'          => 'Agrega el primer nombre.',
                'nombre1.max'               => 'El primer nombre no puede ser mayor a :max caracteres.',
                'nombre2.max'               => 'El segundo nombre no puede ser mayor a :max caracteres.',
                'apellido1.required'        => 'Agrega el primer apellido.',
                'apellido1.max'             => 'El primer apellido no puede ser mayor a :max caracteres.',
                'apellido2.required'        => 'Agrega el segundo apellido.',
                'apellido2.max'             => 'El segundo apellido no puede ser mayor a :max caracteres.',
                'id_pais.required'          => 'Agrega el país.',
                'ciudad.required'           => 'Agrega la ciudad.',
                'ciudad.max'                => 'La ciudad no puede ser mayor a :max caracteres.',
                'direccion.required'        => 'Agrega la direccion.',
                'direccion.max'             => 'La direccion no puede ser mayor a :max caracteres.',
                'telefono1.required'        => 'Agrega el teléfono del domicilio',
                'telefono1.max'             => 'El teléfono del domicilio no puede ser mayor a 10 caracteres.',
                'telefono1.numeric'         => 'El telefono del domicilio debe ser numérico.',
                'telefono2.required'        => 'Agrega el teléfono celular.',
                'telefono2.max'             => 'El teléfono celular no puede ser mayor a 10 caracteres.',
                'telefono2.numeric'         => 'El telefono celular debe ser numérico.',
                'ocupacion.required'        => 'Agrega la ocupación.',
                'ocupacion.max'             => 'La ocupación del usuario no puede ser mayor a :max caracteres.',
                'fecha_nacimiento.required' => 'Agrega la fecha de nacimiento.',
                'fecha_nacimiento.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
                'genero.required'           => 'Agrega el genero.',
                'etnia.required'            => 'Agrega la Etnia.',
                'etnia.max'                 => 'La  Etnia no puede ser mayor a :max caracteres.',
                'nivel_academico.required'  => 'Agrega el Nivel Academico.',
                'estado_civil.required'     => 'Agrega el Estado Civil.',
                'email.unique'              => 'El Email ya se encuentra registrado.',
                'email.required'            => 'Agrega el Email del usuario.',
                'email.max'                 => 'El Email no puede ser mayor a :max caracteres.',
                'email.email'               => 'El Email tiene error en el formato.',
                'password.required'         => 'Agrega el password.',
                'password.min'              => 'El Password debe ser mayor a :min caracteres.',
                'password.confirmed'        => 'El Password y su confirmación no coinciden.',
                'area.required'             => 'Agrega el Area.',
                'cargo.required'            => 'Agrega el Cargo.',
                'cargo.max'                 => 'El Cargo no puede ser mayor a :max caracteres.',
                'fecha_actividad.required'  => 'Agrega la fecha de Ingreso.',
                'fecha_actividad.date'      => 'La fecha de Ingreso tiene formato incorrecto.',
                'fondo_reserva.required'    => 'Selecciona si Acumula o Mensualiza.',
                'decimo_tercero.required'   => 'Selecciona si Acumula o Mensualiza.',
                'decimo_cuarto.required'    => 'Selecciona si Acumula o Mensualiza.',
                'seguro_privado.required'   => 'Agregue el Seguro Privado.',
                'horario.required'          => 'Selecciona el Horario.',
                'id_banco.required'         => 'Selecciona el Banco.',
                'numero_cuenta.required'    => 'Agrega el Numero de Cuenta.',
                'numero_cuenta.max'         => 'El Numero de Cuenta no debe ser mayor a :max caracteres.',
                'sueldo.required'           => 'Ingrese el Sueldo.',

            ];

            $this->validate($request, $constraints, $mensajes);

            //Fin Validacion Campos Requeridos

            $newUser = [
                'id'               => $request['identificacion'],
                'nombre1'          => strtoupper($request['nombre1']),
                'nombre2'          => strtoupper($request['nombre2']),
                'apellido1'        => strtoupper($request['apellido1']),
                'apellido2'        => strtoupper($request['apellido2']),
                'id_pais'          => $request['id_pais'],
                'ciudad'           => strtoupper($request['ciudad']),
                'direccion'        => strtoupper($request['direccion']),
                'telefono1'        => $request['telefono1'],
                'telefono2'        => $request['telefono2'],
                'ocupacion'        => strtoupper($request['ocupacion']),
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'id_tipo_usuario'  => $nominatipo['id'],
                'email'            => $request['email'],
                'color'            => $request['color'],
                'password'         => bcrypt($request['password']),
                'tipo_documento'   => 1,
                'estado'           => 1,
                'imagen_url'       => ' ',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
            ];

            User::create($newUser);

            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PERSONAL",
                'dato_ant1'   => $request['identificacion'],
                'dato1'       => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
                'dato_ant2'   => "TIPO USUARIO: " . $request['id_tipo_usuario'],
            ]);

            $datos_nomina = [

                'id_user'                   => $request['identificacion'],
                'nombres'                   => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
                //'id_empresa'                 => $request['id_empresa'],
                'id_empresa'                => $id_empresa,
                'sexo'                      => $request['genero'],
                'etnia'                     => $request['etnia'],
                'check_discapacidad'        => $request['discapacidad'],
                'porcentaje_discapacidad'   => $request['porcent_discapacidad'],
                'numero_cargas'             => $request['numero_carga'],
                'nivel_academico'           => $request['nivel_academico'],
                'estado_civil'              => $request['estado_civil'],
                'area'                      => $request['area'],
                'cargo'                     => $request['cargo'],
                'funciones'                 => $request['funciones'],
                'estado'                    => $request['estado'],
                'fecha_ingreso'             => $request['fecha_actividad'],
                'pago_fondo_reserva'        => $request['fondo_reserva'],
                'decimo_tercero'            => $request['decimo_tercero'],
                'decimo_cuarto'             => $request['decimo_cuarto'],
                'seguro_privado'            => $request['seguro_privado'],
                'horario'                   => $request['horario'],
                'bono'                      => $request['bono'],
                'impuesto_renta'            => $request['imp_renta'],
                'alimentacion'              => $request['alimentacion'],
                'banco'                     => $request['id_banco'],
                'sueldo_neto'               => $request['sueldo'],
                'numero_cuenta'             => $request['numero_cuenta'],
                'archivo_curriculum'        => $request['curriculum_vitae'],
                'archivo_ficha_tecnica'     => $request['ficha_tecnica'],
                'archivo_ficha_ocupacional' => $request['ficha_ocupacional'],
                'foto'                      => $request['foto'],
                'ip_creacion'               => $ip_cliente,
                'ip_modificacion'           => $ip_cliente,
                'id_usuariocrea'            => $idusuario,
                'id_usuariomod'             => $idusuario,
	            'bono_imputable'            => $request['bono_imputable'],
                'fondos_reserva'            => $request['fondos_reserva'],
                'aporte_personal'           => $request['aporte_personal'], 
                'parqueo'                   => $request['parqueo'],
                //'codigo_caja'               => $caja->codigo_caja,
                //'codigo_sucursal'           => $sucursal->codigo_sucursal,
                //'id_caja'                   => $caja->id,
                //'id_sucursal'               => $sucursal->id,
                'id_sucursal'               => $request->sucursal,
                'pasante'                   => $check_pasante,
                'jornada'                   => $request->jornada,
                'extension_conyuge'         => $check_conyuge,
                'movilizacion'              => $request['movilizacion'],
                'funciones'                 => $request['funciones'],
                'dias_laborables'           => $request['dias_laborables'],
                'horas_mes'                 => $request['horas_mes'],
                'sueldo_pasante'            => $request['sueldo_pasante'],
                //'fecha_salida'              => $request['fecha_salida'],
                'cantidad_horas'            => $horas_dia,
                'ocupacion'        => strtoupper($request['ocupacion']),
                
            ];

            $id_nomina = Ct_Nomina::insertGetId($datos_nomina);

            //Guardado de Archivo Curriculum Vitae
            $id_curriculum = $request['curriculum_vitae'];
            if (!is_null($id_curriculum)) {
                $nombre_original_curr = $request['curriculum_vitae']->getClientOriginalName();
                $nuevo_nombre         = $nombre_original_curr;
                $r5                   = Storage::disk('archivos_nomina')->put($nuevo_nombre, \File::get($request['curriculum_vitae']));

                $rutaarchivo1 = $nuevo_nombre;
            }

            //Guardado de Archivo Ficha Tecnica
            $nuevo_nombre2 = "";
            $id_ficha = $request['ficha_tecnica'];
            if (!is_null($id_ficha)) {
                $nombre_original_fich = $request['ficha_tecnica']->getClientOriginalName();
                $nuevo_nombre2        = $nombre_original_fich;
                $r6                   = Storage::disk('archivos_nomina')->put($nuevo_nombre2, \File::get($request['ficha_tecnica']));

                $rutaarchivo2 = $nuevo_nombre2;
            }


            //Guardado de Archivo Ficha Ocupacional
            $id_ficha_ocupacional = $request['ficha_ocupacional'];
            if (!is_null($id_ficha_ocupacional)) {
                $nombre_original_fich_ocup = $request['ficha_ocupacional']->getClientOriginalName();
                $nuevo_nombre3             = $nombre_original_fich_ocup;
                $r7                        = Storage::disk('archivos_nomina')->put($nuevo_nombre3, \File::get($request['ficha_ocupacional']));

                $rutaarchivo3 = $nuevo_nombre3;
            }

            //Guardado de Archivo foto
            $id_foto = $request['foto'];
            $nuevo_nombre2 = "";$nuevo_nombre4 = "";
            if (!is_null($id_foto)) {
                $nombre_original_fot = $request['foto']->getClientOriginalName();
                $nuevo_nombre4        = $nombre_original_fot;
                $r8                   = Storage::disk('archivos_nomina')->put($nuevo_nombre2, \File::get($request['foto']));

                $rutaarchivo4 = $nuevo_nombre4;
            }

            //ACTUALIZAR CURRICULUM
            if (!is_null($id_curriculum)) {
                if ($r5) {
                    $empl_actual                     = Ct_Nomina::find($id_nomina);
                    $empl_actual->archivo_curriculum = $rutaarchivo1;
                    $empl_actual->ip_modificacion    = $ip_cliente;
                    $empl_actual->id_usuariomod      = $idusuario;
                    $r2                              = $empl_actual->save();
                }
            }

            //ACTUALIZAR FICHA TECNICA
            if (!is_null($id_ficha)) {
                if ($r6) {
                    $empl_actual                        = Ct_Nomina::find($id_nomina);
                    $empl_actual->archivo_ficha_tecnica = $rutaarchivo2;
                    $empl_actual->ip_modificacion       = $ip_cliente;
                    $empl_actual->id_usuariomod         = $idusuario;
                    $r3                                 = $empl_actual->save();
                }
            }

            //ACTUALIZAR FICHA OCUPACIONAL
            if (!is_null($id_ficha_ocupacional)) {
                if ($r7) {
                    $empl_actual                            = Ct_Nomina::find($id_nomina);
                    $empl_actual->archivo_ficha_ocupacional = $rutaarchivo3;
                    $empl_actual->ip_modificacion           = $ip_cliente;
                    $empl_actual->id_usuariomod             = $idusuario;
                    $r4                                     = $empl_actual->save();
                }
            }
            //ACTUALIZAR FOTO
            if (!is_null($id_foto)) {
                if ($r8) {
                    $empl_actual                            = Ct_Nomina::find($id_nomina);
                    $empl_actual->foto                      = $rutaarchivo4;
                    $empl_actual->ip_modificacion           = $ip_cliente;
                    $empl_actual->id_usuariomod             = $idusuario;
                    $r                                     = $empl_actual->save();
                }
            }
        }

        return redirect()->route('nomina.index');
    }

    public function anular($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //Obtenemos la fecha de Hoy
        $fechahoy   = Date('Y-m-d H:i:s');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $act_estado = [
            'estado' => '0',
        ];
        $registro = Ct_Nomina::findorfail($id);

        Ct_Nomina::where('id', $id)->update($act_estado);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "INACTIVAR PERSONAL",
            'dato_ant1'   => $registro['identificacion'],
            'dato1'       => strtoupper($registro['nombre1']) . " " . strtoupper($registro['nombre2']) . " " . strtoupper($registro['apellido1']) . " " . strtoupper($registro['apellido2']),
            'dato_ant2'   => "TIPO USUARIO: " . $registro['id_tipo_usuario'],
        ]);

        return redirect()->intended('/contable/nomina');
    }

    public function revisar($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $registro        = Ct_Nomina::findorfail($id);
        $empresas        = Empresa::all();
        $empresa         = Empresa::where('id', $registro['id_empresa'])->first();
        /*$ct_sucursales = [];
        if ($empresa->necesita_sucursales == 1) {
            $ct_sucursales = Ct_Sucursales::where("id_empresa", $empresa->id)->get();
        } else {
            $ct_sucursales = Ct_Sucursales::where("id_empresa", $empresa->id)->limit(1)->get();
        }*/
        $ct_sucursales = Ct_Sucursales::where("estado", 1)->where('uso_nomina',1)->where('id_empresa',$empresa->id)->get();
        if($ct_sucursales->count() == 0) {
            $ct_sucursales = Ct_Sucursales::where("estado", 1)->where('id_empresa',$empresa->id)->get();
        }
        //nuevo del dia 10/2/2023
        $uso_sucursales = $empresa->sucursales->where("uso_nomina", 1);
        $usuario         = User::where('id', $registro['id_user'])->first();
        $tipo            = TipoUsuario::where('estado', '1')->get();
        $nominatipo      = TipoUsuario::where('nombre', 'NOMINA')->first();
        $pais            = pais::all();
        $tipousuarios    = tipousuario::all();
        $especialidad    = especialidad::all();
        $nivel_academico = Ct_Rh_Nivel_Academico::all();
        $estado_civil    = Ct_Rh_Estado_Civil::all();
        $area            = Ct_Rh_Area::all();
        $pago_beneficio  = Ct_Rh_Pago_Beneficio::all();
        $horario         = Ct_Rh_Horario::all();
        $lista_banco     = Ct_Bancos::all();
        $aporte_personal = Ct_Rh_Valores::where('tipo', '1')->where('estado', '1')->where('id_empresa', $empresa->id)->get();
        $cajas           = Ct_Caja::where('id_empresa',$empresa->id)->where('estado','1')->get();

        return view('contable/nomina/edit', ['uso_sucursales' => $uso_sucursales, 'ct_sucursales' => $ct_sucursales, 'registro' => $registro, 'empresa' => $empresa, 'usuario' => $usuario, 'empresas' => $empresas, 'pais' => $pais, 'tipousuarios' => $tipousuarios, 'especialidad' => $especialidad, 'nominaTipo' => $nominatipo, 'nivel_academico' => $nivel_academico, 'estado_civil' => $estado_civil, 'area' => $area, 'pago_beneficio' => $pago_beneficio, 'horario' => $horario, 'lista_banco' => $lista_banco, 'aporte_personal' => $aporte_personal, 'cajas' => $cajas]);
    }

    public function update(Request $request)
    {   
        //try {
            //code...
        
        $check = 0;
        if ($request['verificar_conyuge'] == "on") {
            $check = 1;
        }
        $check_pasante = 0;
        if ($request['pasante'] == "on") {
            $check_pasante = 1;
        }
        $user       = User::findOrFail($request['identificacion']);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id_empresa = $request->session()->get('id_empresa');

        $empresa = Empresa::find($id_empresa);
        //Fausto
        $caja       = Ct_Caja::where('id_sucursal', $request->id_caja)->first();
        /* $sucursal   = $caja->sucursal; */

        $id_empresa = $request->session()->get('id_empresa');

        $id = $request['identificacion'];

        //Validacion de Campos Requeridos
        $constraints = [

            'identificacion'   => 'required|max:10|unique:users,id,' . $id,
            'nombre1'          => 'required|max:60',
            'nombre2'          => 'max:60',
            'apellido1'        => 'required|max:60',
            'apellido2'        => 'required|max:60',
            'id_pais'          => 'required',
            'ciudad'           => 'required|max:60',
            'direccion'        => 'required|max:255',
            'telefono1'        => 'required|numeric|max:9999999999',
            'telefono2'        => 'required|numeric|max:9999999999',
            'ocupacion'        => 'required|max:60',
            'fecha_nacimiento' => 'required|date',
            'genero'           => 'required',
            'etnia'            => 'required|max:255',
            'nivel_academico'  => 'required',
            'estado_civil'     => 'required',
            'email'            => 'required|email|max:191',
            'area'             => 'required',
            'cargo'            => 'required|max:255',
            'fecha_actividad'  => 'required|date',
            'fondo_reserva'    => 'required',
            'decimo_tercero'   => 'required',
            'decimo_cuarto'    => 'required',
            'seguro_privado'   => 'required',
            'horario'          => 'required',
            'id_banco'         => 'required',
            'numero_cuenta'    => 'required|max:255',
            'sueldo'           => 'required',
            'aporte_personal'  => 'required',

        ];

        $mensajes = [

            'identificacion.unique'     => 'La cédula ya se encuentra registrada.',
            'identificacion.required'   => 'Agrega la cédula.',
            'identificacion.max'        => 'La cédula no puede ser mayor a :max caracteres.',
            'nombre1.required'          => 'Agrega el primer nombre.',
            'nombre1.max'               => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'               => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'        => 'Agrega el primer apellido.',
            'apellido1.max'             => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'        => 'Agrega el segundo apellido.',
            'apellido2.max'             => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'id_pais.required'          => 'Agrega el país.',
            'ciudad.required'           => 'Agrega la ciudad.',
            'ciudad.max'                => 'La ciudad no puede ser mayor a :max caracteres.',
            'direccion.required'        => 'Agrega la direccion.',
            'direccion.max'             => 'La direccion no puede ser mayor a :max caracteres.',
            'telefono1.required'        => 'Agrega el teléfono del domicilio',
            'telefono1.max'             => 'El teléfono del domicilio no puede ser mayor a 10 caracteres.',
            'telefono1.numeric'         => 'El telefono del domicilio debe ser numérico.',
            'telefono2.required'        => 'Agrega el teléfono celular.',
            'telefono2.max'             => 'El teléfono celular no puede ser mayor a 10 caracteres.',
            'telefono2.numeric'         => 'El telefono celular debe ser numérico.',
            'ocupacion.required'        => 'Agrega la ocupación.',
            'ocupacion.max'             => 'La ocupación del usuario no puede ser mayor a :max caracteres.',
            'fecha_nacimiento.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'genero.required'           => 'Agrega el genero.',
            'etnia.required'            => 'Agrega la Etnia.',
            'etnia.max'                 => 'La  Etnia no puede ser mayor a :max caracteres.',
            'nivel_academico.required'  => 'Agrega el Nivel Academico.',
            'estado_civil.required'     => 'Agrega el Estado Civil.',
            'email.unique'              => 'El Email ya se encuentra registrado.',
            'email.required'            => 'Agrega el Email del usuario.',
            'email.max'                 => 'El Email no puede ser mayor a :max caracteres.',
            'email.email'               => 'El Email tiene error en el formato.',
            'password.required'         => 'Agrega el password.',
            'password.min'              => 'El Password debe ser mayor a :min caracteres.',
            'password.confirmed'        => 'El Password y su confirmación no coinciden.',
            'area.required'             => 'Agrega el Area.',
            'cargo.required'            => 'Agrega el Cargo.',
            'cargo.max'                 => 'El Cargo no puede ser mayor a :max caracteres.',
            'fecha_actividad.required'  => 'Agrega la fecha de Ingreso.',
            'fecha_actividad.date'      => 'La fecha de Ingreso tiene formato incorrecto.',
            'fondo_reserva.required'    => 'Selecciona si Acumula o Mensualiza.',
            'decimo_tercero.required'   => 'Selecciona si Acumula o Mensualiza.',
            'decimo_cuarto.required'    => 'Selecciona si Acumula o Mensualiza.',
            'seguro_privado.required'   => 'Agregue el Seguro Privado.',
            'horario.required'          => 'Selecciona el Horario.',
            'id_banco.required'         => 'Selecciona el Banco.',
            'numero_cuenta.required'    => 'Agrega el Numero de Cuenta.',
            'numero_cuenta.max'         => 'El Numero de Cuenta no debe ser mayor a :max caracteres.',
            'sueldo.required'           => 'Ingrese el Sueldo.',
            'aporte_personal.required'  => 'Ingrese el Aporte Personal.',

        ];

        $this->validate($request, $constraints, $mensajes);

        $horas_dia = null;
        
        if($request['jornada'] != '0'){
            $rules = [
                //'parentesco' => 'required|in:Principal|son_iguales:'.$request['id'].','.$request['id_prin'],
                'dias_laborables'       => 'required | max_consulta: 0,'.$request['dias_laborables'],
                'horas_mes'             => 'required | max_consulta: 0,'.$request['horas_mes'],
    
            ];
    
            $msj = [
                'dias_laborables.required'         => 'Los dias laborables son requeridos',
                'horas_mes.required'               => 'Los horas al mes son requeridos',
                'dias_laborables.max_consulta'      => 'Dias Laborables no puede ser 0',
                'horas_mes.max_consulta'            => 'Horas al mes no puede ser 0',
    
            ];
            //dd($rules);
            $this->validate($request, $rules, $msj);

            $horas_dia = $request['horas_mes'] / $request['dias_laborables'];
        }
        //Fin de Validacion de Campos Requeridos

        $input = [
            'id'               => $request['identificacion'],
            'nombre1'          => strtoupper($request['nombre1']),
            'nombre2'          => strtoupper($request['nombre2']),
            'apellido1'        => strtoupper($request['apellido1']),
            'apellido2'        => strtoupper($request['apellido2']),
            'id_pais'          => $request['id_pais'],
            'ciudad'           => strtoupper($request['ciudad']),
            'direccion'        => strtoupper($request['direccion']),
            'telefono1'        => $request['telefono1'],
            'telefono2'        => $request['telefono2'],
            'ocupacion'        => strtoupper($request['ocupacion']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'email'            => $request['email'],
            'ip_modificacion'  => $ip_cliente,
            'id_usuariomod'    => $idusuario,

        ];

        User::where('id', $request['identificacion'])
            ->update($input);

        $dat_nom = Ct_Nomina::find($request['id']);

        if (!is_null($request['curriculum'])) {

            Ct_Nomina::where('id', $request['id'])->update([
                'id_user'                   => $request['identificacion'],
                'id_empresa'                => $id_empresa,
                'sexo'                      => $request['genero'],
                'etnia'                     => $request['etnia'],
                'check_discapacidad'        => $request['discapacidad'],
                'porcentaje_discapacidad'   => $request['porcent_discapacidad'],
                'numero_cargas'             => $request['numero_carga'],
                'nivel_academico'           => $request['nivel_academico'],
                'mail_opcional'             => $request['mail_opcional'],
                'estado_civil'              => $request['estado_civil'],
                //'estado'                    => $request['estado'],
                'area'                      => $request['area'],
                'cargo'                     => $request['cargo'],
                'funciones'                 => $request['funciones'],
                'fecha_ingreso'             => $request['fecha_actividad'],
                'pago_fondo_reserva'        => $request['fondo_reserva'],
                'decimo_tercero'            => $request['decimo_tercero'],
                'decimo_cuarto'             => $request['decimo_cuarto'],
                'seguro_privado'            => $request['seguro_privado'],
                'horario'                   => $request['horario'],
                'bono'                      => $request['bono'],
                'impuesto_renta'            => $request['imp_renta'],
                'alimentacion'              => $request['alimentacion'],
                'banco'                     => $request['id_banco'],
                'sueldo_neto'               => $request['sueldo'],
                'numero_cuenta'             => $request['numero_cuenta'],
                'archivo_curriculum'        => $request['curriculum'],
                'archivo_ficha_tecnica'     => $dat_nom->archivo_ficha_tecnica,
                'archivo_ficha_ocupacional' => $dat_nom->archivo_ficha_ocupacional,
                'foto'                      => $dat_nom->foto,
                'ip_modificacion'           => $ip_cliente,
                'id_usuariomod'             => $idusuario,
                'bono_imputable'            => $request['bono_imputable'],
                'aporte_personal'           => $request['aporte_personal'],
		        //'id_caja'                   => $caja->id,
                'extension_conyuge'         => $check,
                'movilizacion'              => $request['movilizacion'],
                'id_sucursal'               => $request->sucursal,
                'fecha_ant'                 => $request['fecha_ant'],
                'jornada'                   => $request['jornada'],
                'pasante'                   => $check_pasante, 
                'dias_laborables'           => $request['dias_laborables'],
                'horas_mes'                 => $request['horas_mes'],
                'sueldo_pasante'            => $request['sueldo_pasante'],
                'fecha_salida'              => $request['fecha_salida'],
                'cantidad_horas'            => $horas_dia,
                //'fondos_reserva'            => $request['fondos_reserva'],
                'ocupacion'                 => strtoupper($request['ocupacion']),


            ]);
        }

        if (!is_null($request['ficha'])) {

            Ct_Nomina::where('id', $request['id'])->update([
                'id_user'                   => $request['identificacion'],
                'id_empresa'                => $id_empresa,
                'sexo'                      => $request['genero'],
                'etnia'                     => $request['etnia'],
                'check_discapacidad'        => $request['discapacidad'],
                'porcentaje_discapacidad'   => $request['porcent_discapacidad'],
                'numero_cargas'             => $request['numero_carga'],
                'nivel_academico'           => $request['nivel_academico'],
                'mail_opcional'             => $request['mail_opcional'],
                'estado_civil'              => $request['estado_civil'],
                'area'                      => $request['area'],
                //'estado'                    => $request['estado'],
                'cargo'                     => $request['cargo'],
                'funciones'                 => $request['funciones'],
                'fecha_ingreso'             => $request['fecha_actividad'],
                'pago_fondo_reserva'        => $request['fondo_reserva'],
                'decimo_tercero'            => $request['decimo_tercero'],
                'decimo_cuarto'             => $request['decimo_cuarto'],
                'seguro_privado'            => $request['seguro_privado'],
                'horario'                   => $request['horario'],
                'bono'                      => $request['bono'],
                'impuesto_renta'            => $request['imp_renta'],
                'alimentacion'              => $request['alimentacion'],
                'banco'                     => $request['id_banco'],
                'sueldo_neto'               => $request['sueldo'],
                'numero_cuenta'             => $request['numero_cuenta'],
                'archivo_curriculum'        => $dat_nom->archivo_curriculum,
                'archivo_ficha_tecnica'     => $request['ficha'],
                'archivo_ficha_ocupacional' => $dat_nom->archivo_ficha_ocupacional,
                'foto'                      => $dat_nom->foto,
                'ip_modificacion'           => $ip_cliente,
                'id_usuariomod'             => $idusuario,
                'bono_imputable'            => $request['bono_imputable'],
                'fondos_reserva'            => $request['fondos_reserva'],
                'aporte_personal'           => $request['aporte_personal'],
                //'id_caja'                   => $caja->id,
                'extension_conyuge'         => $check,
                'movilizacion'              => $request['movilizacion'],
                'id_sucursal'               => $request->sucursal,
                'fecha_ant'                 => $request['fecha_ant'],
                'jornada'                   => $request['jornada'],
                'pasante'                   => $check_pasante,
                'dias_laborables'           => $request['dias_laborables'],
                'horas_mes'                 => $request['horas_mes'],
                'sueldo_pasante'            => $request['sueldo_pasante'],
                'fecha_salida'              => $request['fecha_salida'],
                'cantidad_horas'            => $horas_dia,
                'ocupacion'                 => strtoupper($request['ocupacion']),

            ]);
        }

        if (!is_null($request['fich_ocup'])) {

            Ct_Nomina::where('id', $request['id'])->update([
                'id_user'                   => $request['identificacion'],
                'id_empresa'                => $id_empresa,
                'sexo'                      => $request['genero'],
                'etnia'                     => $request['etnia'],
                'check_discapacidad'        => $request['discapacidad'],
                'porcentaje_discapacidad'   => $request['porcent_discapacidad'],
                'numero_cargas'             => $request['numero_carga'],
                'nivel_academico'           => $request['nivel_academico'],
                'mail_opcional'             => $request['mail_opcional'],
                'estado_civil'              => $request['estado_civil'],
                'area'                      => $request['area'],
                'cargo'                     => $request['cargo'],
                'funciones'                 => $request['funciones'],
                //'estado'                    => $request['estado'],
                'fecha_ingreso'             => $request['fecha_actividad'],
                'pago_fondo_reserva'        => $request['fondo_reserva'],
                'decimo_tercero'            => $request['decimo_tercero'],
                'decimo_cuarto'             => $request['decimo_cuarto'],
                'seguro_privado'            => $request['seguro_privado'],
                'horario'                   => $request['horario'],
                'bono'                      => $request['bono'],
                'impuesto_renta'            => $request['imp_renta'],
                'alimentacion'              => $request['alimentacion'],
                'banco'                     => $request['id_banco'],
                'sueldo_neto'               => $request['sueldo'],
                'numero_cuenta'             => $request['numero_cuenta'],
                'archivo_curriculum'        => $dat_nom->archivo_curriculum,
                'archivo_ficha_tecnica'     => $dat_nom->archivo_ficha_tecnica,
                'archivo_ficha_ocupacional' => $request['fich_ocup'],
                'foto'                      => $request['fot'],
                'ip_modificacion'           => $ip_cliente,
                'id_usuariomod'             => $idusuario,
                'bono_imputable'            => $request['bono_imputable'],
                'fondos_reserva'            => $request['fondos_reserva'],
                'aporte_personal'           => $request['aporte_personal'],
                //'id_caja'                   => $caja->id,
                'extension_conyuge'         => $check,
                'movilizacion'              => $request['movilizacion'],
                'id_sucursal'               => $request->sucursal,
                'fecha_ant'                 => $request['fecha_ant'],
                'jornada'                   => $request['jornada'],
                'pasante'                   => $check_pasante,
                'dias_laborables'           => $request['dias_laborables'],
                'horas_mes'                 => $request['horas_mes'],
                'sueldo_pasante'            => $request['sueldo_pasante'],
                'fecha_salida'              => $request['fecha_salida'],
                'cantidad_horas'            => $horas_dia,
                'ocupacion'                 => strtoupper($request['ocupacion']),

            ]);
        }

        if (!is_null($request['foto'])) {

            Ct_Nomina::where('id', $request['id'])->update([
                'id_user'                   => $request['identificacion'],
                'id_empresa'                => $id_empresa,
                'sexo'                      => $request['genero'],
                'etnia'                     => $request['etnia'],
                'check_discapacidad'        => $request['discapacidad'],
                'porcentaje_discapacidad'   => $request['porcent_discapacidad'],
                'numero_cargas'             => $request['numero_carga'],
                'nivel_academico'           => $request['nivel_academico'],
                'mail_opcional'             => $request['mail_opcional'],
                'estado_civil'              => $request['estado_civil'],
                //'estado'                    => $request['estado'],
                'area'                      => $request['area'],
                'cargo'                     => $request['cargo'],
                'funciones'                 => $request['funciones'],
                'fecha_ingreso'             => $request['fecha_actividad'],
                'pago_fondo_reserva'        => $request['fondo_reserva'],
                'decimo_tercero'            => $request['decimo_tercero'],
                'decimo_cuarto'             => $request['decimo_cuarto'],
                'seguro_privado'            => $request['seguro_privado'],
                'horario'                   => $request['horario'],
                'bono'                      => $request['bono'],
                'impuesto_renta'            => $request['imp_renta'],
                'alimentacion'              => $request['alimentacion'],
                'banco'                     => $request['id_banco'],
                'sueldo_neto'               => $request['sueldo'],
                'numero_cuenta'             => $request['numero_cuenta'],
                'archivo_curriculum'        => $request['curriculum'],
                'archivo_ficha_tecnica'     => $dat_nom->archivo_ficha_tecnica,
                'archivo_ficha_ocupacional' => $dat_nom->archivo_ficha_ocupacional,
                'foto'                      => $dat_nom->foto,
                'ip_modificacion'           => $ip_cliente,
                'id_usuariomod'             => $idusuario,
                'bono_imputable'            => $request['bono_imputable'],
                'aporte_personal'           => $request['aporte_personal'],
		        //'id_caja'                   => $caja->id,
                'extension_conyuge'         => $check,
                'movilizacion'              => $request['movilizacion'],
                'id_sucursal'               => $request->sucursal,
                'fecha_ant'                 => $request['fecha_ant'],
                'jornada'                   => $request['jornada'],
                'pasante'                   => $check_pasante, 
                //'fondos_reserva'            => $request['fondos_reserva'],
                'dias_laborables'           => $request['dias_laborables'],
                'horas_mes'                 => $request['horas_mes'],
                'sueldo_pasante'            => $request['sueldo_pasante'],
                'fecha_salida'              => $request['fecha_salida'],
                'cantidad_horas'            => $horas_dia,
                'ocupacion'                 => strtoupper($request['ocupacion']),


            ]);
        }

        if ((!is_null($request['curriculum'])) && (!is_null($request['ficha'])) && (!is_null($request['fich_ocup'])) && (!is_null($request['foto']))) {

            Ct_Nomina::where('id', $request['id'])->update([
                'id_user'                   => $request['identificacion'],
                'id_empresa'                => $id_empresa,
                'sexo'                      => $request['genero'],
                'etnia'                     => $request['etnia'],
                'check_discapacidad'        => $request['discapacidad'],
                'porcentaje_discapacidad'   => $request['porcent_discapacidad'],
                'numero_cargas'             => $request['numero_carga'],
                'nivel_academico'           => $request['nivel_academico'],
                'mail_opcional'             => $request['mail_opcional'],
                'estado_civil'              => $request['estado_civil'],
                'area'                      => $request['area'],
                'cargo'                     => $request['cargo'],
                'funciones'                 => $request['funciones'],
                //'estado'                    => $request['estado'],
                'fecha_ingreso'             => $request['fecha_actividad'],
                'pago_fondo_reserva'        => $request['fondo_reserva'],
                'decimo_tercero'            => $request['decimo_tercero'],
                'decimo_cuarto'             => $request['decimo_cuarto'],
                'seguro_privado'            => $request['seguro_privado'],
                'horario'                   => $request['horario'],
                'bono'                      => $request['bono'],
                'impuesto_renta'            => $request['imp_renta'],
                'alimentacion'              => $request['alimentacion'],
                'banco'                     => $request['id_banco'],
                'sueldo_neto'               => $request['sueldo'],
                'numero_cuenta'             => $request['numero_cuenta'],
                'archivo_curriculum'        => $request['curriculum'],
                'archivo_ficha_tecnica'     => $request['ficha'],
                'archivo_ficha_ocupacional' => $request['fich_ocup'],
                'foto'                      => $request['foto'],
                'ip_modificacion'           => $ip_cliente,
                'id_usuariomod'             => $idusuario,
                'bono_imputable'            => $request['bono_imputable'],
                'fondos_reserva'            => $request['fondos_reserva'],
                'aporte_personal'           => $request['aporte_personal'],
                //'id_caja'                   => $caja->id,
                'extension_conyuge'         => $check,
                'movilizacion'              => $request['movilizacion'],
                'id_sucursal'               => $request->sucursal,
                'fecha_ant'                 => $request['fecha_ant'],
                'jornada'                   => $request['jornada'],
                'pasante'                   => $check_pasante,
                'dias_laborables'           => $request['dias_laborables'],
                'horas_mes'                 => $request['horas_mes'],
                'sueldo_pasante'            => $request['sueldo_pasante'],
                'fecha_salida'              => $request['fecha_salida'],
                'cantidad_horas'            => $horas_dia,
                'ocupacion'                 => strtoupper($request['ocupacion']),

            ]);
        }

        Ct_Nomina::where('id', $request['id'])->update([
            'id_user'                   => $request['identificacion'],
            'id_empresa'                => $id_empresa,
            'sexo'                      => $request['genero'],
            'etnia'                     => $request['etnia'],
            'check_discapacidad'        => $request['discapacidad'],
            'porcentaje_discapacidad'   => $request['porcent_discapacidad'],
            'numero_cargas'             => $request['numero_carga'],
            'nivel_academico'           => $request['nivel_academico'],
            'mail_opcional'             => $request['mail_opcional'],
            'estado_civil'              => $request['estado_civil'],
            'area'                      => $request['area'],
            'cargo'                     => $request['cargo'],
            'funciones'                 => $request['funciones'],
            //'estado'                    => $request['estado'],
            'fecha_ingreso'             => $request['fecha_actividad'],
            'pago_fondo_reserva'        => $request['fondo_reserva'],
            'decimo_tercero'            => $request['decimo_tercero'],
            'decimo_cuarto'             => $request['decimo_cuarto'],
            'seguro_privado'            => $request['seguro_privado'],
            'horario'                   => $request['horario'],
            'bono'                      => $request['bono'],
            'impuesto_renta'            => $request['imp_renta'],
            'alimentacion'              => $request['alimentacion'],
            'banco'                     => $request['id_banco'],
            'sueldo_neto'               => $request['sueldo'],
            'numero_cuenta'             => $request['numero_cuenta'],
            'archivo_curriculum'        => $dat_nom->archivo_curriculum,
            'archivo_ficha_tecnica'     => $dat_nom->archivo_ficha_tecnica,
            'archivo_ficha_ocupacional' => $dat_nom->archivo_ficha_ocupacional,
            'foto'                      => $dat_nom->foto,
            'ip_modificacion'           => $ip_cliente,
            'id_usuariomod'             => $idusuario,
            'bono_imputable'            => $request['bono_imputable'],
            'fondos_reserva'            => $request['fondos_reserva'],
            'aporte_personal'           => $request['aporte_personal'],
            'parqueo'                   => $request['parqueo'],
            //'codigo_caja'               => $caja->codigo_caja,
            //'codigo_sucursal'           => $sucursal->codigo_sucursal,
            //'id_caja'                   => $caja->id,
            'extension_conyuge'         => $check,
            'movilizacion'              => $request['movilizacion'],
            'id_sucursal'               => $request->sucursal,
            'fecha_ant'                 => $request['fecha_ant'],
            'jornada'                   => $request['jornada'],
            'pasante'                   => $check_pasante,
            'dias_laborables'           => $request['dias_laborables'],
            'horas_mes'                 => $request['horas_mes'],
            'sueldo_pasante'            => $request['sueldo_pasante'],
            'fecha_salida'              => $request['fecha_salida'],
            'cantidad_horas'            => $horas_dia,
            'ocupacion'                 => strtoupper($request['ocupacion']),


        ]);

        //Actualizacion de Archivo Curriculum Vitae
        if (!is_null($request['curriculum'])) {
            $id_curriculum        = $request['curriculum'];
            $nombre_original_curr = $request['curriculum']->getClientOriginalName();
            $nuevo_nombre         = $nombre_original_curr;
            $r5                   = Storage::disk('archivos_nomina')->put($nuevo_nombre, \File::get($request['curriculum']));
            $rutaarchivo1         = $nuevo_nombre;
        }

        //Actualizacion de Archivo Ficha Tecnica
        $nuevo_nombre2 = "";
        if (!is_null($request['ficha'])) {
            $id_ficha             = $request['ficha'];
            $nombre_original_fich = $request['ficha']->getClientOriginalName();
            $nuevo_nombre2        = $nombre_original_fich;
            $r6                   = Storage::disk('archivos_nomina')->put($nuevo_nombre2, \File::get($request['ficha']));
            $rutaarchivo2         = $nuevo_nombre2;
        }

        //Actualizacion de Archivo Ficha Ocupacional
        if (!is_null($request['fich_ocup'])) {
            $id_ficha                  = $request['fich_ocup'];
            $nombre_original_fich_ocup = $request['fich_ocup']->getClientOriginalName();
            $nuevo_nombre3             = $nombre_original_fich_ocup;
            $r7                        = Storage::disk('archivos_nomina')->put($nuevo_nombre3, \File::get($request['fich_ocup']));
            $rutaarchivo3              = $nuevo_nombre3;
        }

        //Actualizacion de foto
        if (!is_null($request['foto'])) {
            $id_foto                  = $request['foto'];
            $nombre_original_fot = $request['foto']->getClientOriginalName();
            $nuevo_nombre4            = $nombre_original_fot;
            $r8                        = Storage::disk('archivos_nomina')->put($nuevo_nombre4, \File::get($request['foto']));
            $rutaarchivo4             = $nuevo_nombre4;
        }

        

        //ACTUALIZAR CURRICULUM
        if (!is_null($request['curriculum'])) {

            if ($r5) {
                $empl_actual                     = Ct_Nomina::find($request['id']);
                $empl_actual->archivo_curriculum = $rutaarchivo1;
                $empl_actual->ip_modificacion    = $ip_cliente;
                $empl_actual->id_usuariomod      = $idusuario;
                $r2                              = $empl_actual->save();
            }
        }

        //ACTUALIZAR FICHA TECNICA
        if (!is_null($request['ficha'])) {

            if ($r6) {
                $empl_actual                        = Ct_Nomina::find($request['id']);
                $empl_actual->archivo_ficha_tecnica = $rutaarchivo2;
                $empl_actual->ip_modificacion       = $ip_cliente;
                $empl_actual->id_usuariomod         = $idusuario;
                $r3                                 = $empl_actual->save();
            }
        }

        //ACTUALIZAR FICHA OCUPACIONAL
        if (!is_null($request['fich_ocup'])) {

            if ($r7) {
                $empl_actual                            = Ct_Nomina::find($request['id']);
                $empl_actual->archivo_ficha_ocupacional = $rutaarchivo3;
                $empl_actual->ip_modificacion           = $ip_cliente;
                $empl_actual->id_usuariomod             = $idusuario;
                $r4                                     = $empl_actual->save();
            }
        }

        //ACTUALIZAR FOTO
        if (!is_null($request['foto'])) {

            if ($r8) {
                $empl_actual                            = Ct_Nomina::find($request['id']);
                $empl_actual->foto                      = $rutaarchivo4;
                $empl_actual->ip_modificacion           = $ip_cliente;
                $empl_actual->id_usuariomod             = $idusuario;
                $r                                   = $empl_actual->save();
            }
        }
        

        $dato_ant2 = $user->nombre1 . " " . $user->nombre2 . " " . $user->apellido1 . " " . $user->apellido2;
        $dato2     = strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']);
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "ACTUALIZA DATOS DE USUARIO NOMINA",
            'dato_ant1'   => $user->id,
            'dato1'       => $request['id'],
            'dato_ant2'   => $dato_ant2,
            'dato2'       => $dato2,
        ]);

        return redirect()->intended('/contable/nomina');

        /*}  catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage().'-'.$e->getLine()];
        }*/
    }

    public function buscar(Request $request)
    {
        //dd($request);
        $id_auth = Auth::user()->id;
        if ($id_auth == '0922290697') {
            //dd($request->all());
        }
        $id_sucursal = null;
        $id_caja = $request->id_caja;
        
        if($id_caja != null){
            $caja        = Ct_Caja::find($request->id_caja);
            //$sucursal    = $caja->sucursal;   

            //$id_sucursal = $sucursal->id; 
        }

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_auth = Auth::user()->id;
        if ($id_auth == '0922729587') {
            //dd($request);
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();

        $constraints = [
            'id_user'    => $request['identificacion'],
            'nombres'    => $request['buscar_nombre'],
            'estado'     => $request['estado'],
            'id_empresa' => $id_empresa,
        ];
        $registros = $this->doSearchingQuery($constraints, $empresa);
        $empresas  = Empresa::all();

        $cajas           = Ct_Caja::where('id_empresa',$empresa->id)->where('estado','1')->get();

        $sucursales = Ct_Sucursales::where('estado','1')->where('id_empresa',$id_empresa)->where('uso_nomina','1')->get();
        $sueldo_basico = Ct_Rh_Valores::where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo','3')->first();
        //dd($constraints);
        return view('contable/nomina/index', ['sueldo_basico' => $sueldo_basico,'request' => $request, 'empresas' => $empresas, 'registros' => $registros, 'searchingVals' => $constraints, 'empresa' => $empresa, 'cajas' => $cajas, 'sucursales' => $sucursales]);
    }

    private function doSearchingQuery($constraints, $empresa)
    {

        $query = DB::table('ct_nomina')
            ->join('users as u', 'u.id', 'ct_nomina.id_user')
            ->select('u.apellido1', 'u.apellido2', 'u.nombre1', 'u.nombre2', 'ct_nomina.*')
            ->orderBy('u.apellido1', 'ASC');
        //dd($query->get());
        $index  = 0;
        $fields = array_keys($constraints);
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where('ct_nomina.' . $fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }
        //dd($query);

        //return $query->paginate(10);
        return $query->get();
    }

    public function crear_egresos($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //dd($id);

        $id_empleado = $id;

        $ct_tipo_rol = Ct_Tipo_Rol::all();

        return view('contable/nomina/modal_egresos', ['ct_tipo_rol' => $ct_tipo_rol, 'id_empleado' => $id_empleado]);
    }

    public function store_egresos(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;

        $id_empleado = $request['id_empl'];

        $variable = $request['contador_egreso'];

        for ($i = 0; $i < $variable; $i++) {

            $visibilidad = $request['visibilidad_egreso' . $i];

            if ($visibilidad == 1) {

                $desc_rol = $request['refleja' . $i];

                /*if ($desc_rol == 'QUINCENA') {

                $tip_rol = 1;

                return $tip_rol;

                }else{*/
                if ($desc_rol == 'FIN DE MES') {

                    $tip_rol = 1;
                }

                //}

                Ct_Egreso_Empleado::create([

                    'id_tipo_rol'       => $tip_rol,
                    'id_empleado'       => $id_empleado,
                    'tipo_mov'          => $request['tipo_mov' . $i],
                    'detalle_descuento' => $request['detalle' . $i],
                    'monto_descontar'   => $request['monto' . $i],
                    'id_usuariocrea'    => $id_usuario,
                    'id_usuariomod'     => $id_usuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,

                ]);
            }
        }

        //return 'ok';

    }

    public function obtener_imagen_curriculum($id)
    {
        $imagen = Ct_Nomina::find($id);
        return view('contable/nomina/modal_externo_curriculum', ['imagen' => $imagen]);
    }

    public function descarga_archivo_curriculum($id)
    {
        $imagen = Ct_Nomina::find($id);

        $path = storage_path() . '/app/archivos_nomina/' . $imagen->archivo_curriculum;

        $nombre_temporal = $imagen->archivo_curriculum;
        $datos           = explode(".", $nombre_temporal);
        if (count($datos) == 2) {
            $extension = $datos[1];
            //$nombre_archivo = $nombre_temporal . '.' . $extension;
            $nombre_archivo = $imagen->archivo_curriculum;
            if ($extension == 'mp4') {
                $path = public_path('uploads/') . $imagen->archivo_curriculum;
            }
        } else {
            $nombre_archivo = $imagen->archivo_curriculum;
        }

        if (file_exists($path)) {
            return Response::download($path, $nombre_archivo);
        }
    }

    public function obtener_imagen_ficha($id)
    {
        $imagen = Ct_Nomina::find($id);
        return view('contable/nomina/modal_externo_ficha', ['imagen' => $imagen]);
    }

    public function descarga_archivo_ficha($id)
    {
        $imagen = Ct_Nomina::find($id);

        $path = storage_path() . '/app/archivos_nomina/' . $imagen->archivo_ficha_tecnica;

        $nombre_temporal = $imagen->archivo_ficha_tecnica;
        $datos           = explode(".", $nombre_temporal);
        if (count($datos) == 2) {
            $extension = $datos[1];
            //$nombre_archivo = $nombre_temporal . '.' . $extension;
            $nombre_archivo = $imagen->archivo_ficha_tecnica;
            if ($extension == 'mp4') {
                $path = public_path('uploads/') . $imagen->archivo_ficha_tecnica;
            }
        } else {
            $nombre_archivo = $imagen->archivo_ficha_tecnica;
        }

        if (file_exists($path)) {
            return Response::download($path, $nombre_archivo);
        }
    }

    public function obtener_imagen_ocupacional($id)
    {
        $imagen = Ct_Nomina::find($id);
        return view('contable/nomina/modal_externo_ocupacional', ['imagen' => $imagen]);
    }

    public function descarga_arch_ocupacional($id)
    {
        
        $imagen = Ct_Nomina::find($id);

        $path = storage_path() . '/app/archivos_nomina/' . $imagen->archivo_ficha_ocupacional;

        $nombre_temporal = $imagen->archivo_ficha_ocupacional;
        $datos           = explode(".", $nombre_temporal);
        if (count($datos) == 2) {
            $extension = $datos[1];
            //$nombre_archivo = $nombre_temporal . '.' . $extension;
            $nombre_archivo = $imagen->archivo_ficha_ocupacional;
            if ($extension == 'mp4') {
                $path = public_path('uploads/') . $imagen->archivo_ficha_ocupacional;
            }
        } else {
            $nombre_archivo = $imagen->archivo_ficha_ocupacional;
        }

        if (file_exists($path)) {
            return Response::download($path, $nombre_archivo);
        }
    }

    /*public function eliminar_archiv_curr($id)
{
$idusuario  = Auth::user()->id;
$curr  = null;
$ip_cliente = $_SERVER["REMOTE_ADDR"];
date_default_timezone_set('America/Guayaquil');
$input1 = [
'archivo_curriculum' => $curr,
'ip_modificacion' => $ip_cliente,
'id_usuariomod'   => $idusuario,
];
Ct_Nomina::where('id', $id)
->update($input1);
return "Archivo eliminado correctamente";
}*/


    public function obtener_imagen_foto ($id)
    {
        $imagen = Ct_Nomina::find($id);
        return view('contable/nomina/modal_externo_foto', ['imagen' => $imagen]);
    }

    public function descargar_foto ($id)
    {
        
        $imagen = Ct_Nomina::find($id);

        $path = storage_path() . '/app/archivos_nomina/' . $imagen->foto;

        $nombre_temporal = $imagen->foto;
        $datos           = explode(".", $nombre_temporal);
        if (count($datos) == 2) {
            $extension = $datos[1];
            //$nombre_archivo = $nombre_temporal . '.' . $extension;
            $nombre_archivo = $imagen->foto;
            if ($extension == 'mp4') {
                $path = public_path('uploads/') . $imagen->foto;
            }
        } else {
            $nombre_archivo = $imagen->foto;
        }
       
        if (file_exists($path)) {
            return Response::download($path, $nombre_archivo);
        }
    }

    public function listar_log(Request $request){
        $tipo = $request->tipo;

        $logs = CtLogNomina::where('tipo', $tipo)->orderBy('created_at','DESC')->limit('40')->get();

        return view('contable/nomina/listado_log', ['logs' => $logs]); 

    }
}
