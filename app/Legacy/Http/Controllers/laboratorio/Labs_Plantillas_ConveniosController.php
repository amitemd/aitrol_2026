<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;   
use Storage;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Seguro;
use Sis_medico\Subseguro;
use Sis_medico\Paciente;
use Sis_medico\Pais;
use Sis_medico\User;
use Sis_medico\User_espe;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Log_usuario;
use Sis_medico\Historiaclinica;
use Sis_medico\Archivo_historico;
use Sis_medico\Agenda;
use Sis_medico\Log_agenda;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Procedimiento;
use Sis_medico\Pentax_log;
use Sis_medico\Convenio;
use Sis_medico\Examen_Orden;
use Sis_medico\Sala;
use Sis_medico\Examen;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Empresa;
use Sis_medico\Labs_Grupo_Familiar;
use Sis_medico\Nivel;
use Sis_medico\Labs_Plantillas_Convenios;
use Sis_medico\ControlDocController;
use Sis_medico\Examen_Protocolo;
use Sis_medico\Examen_detalle;
use Sis_medico\Http\Controllers\HorarioController;
use Excel;




class Labs_Plantillas_ConveniosController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 10, 11, 12)) ){
          return true;
        }
        

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

    public function crear_cabecera(Request $request, $id)
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
            //'id_seguro'        => 'required',
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
        $plantilla_convenio = Labs_Plantillas_Convenios::find($id);
        //$convenio = Convenio::where('id_nivel', $plantilla_convenio->id_nivel)->first();
        $empresa_labs = Empresa::where('prioridad_labs', 1)->first();
        $convenio = Convenio::where('id_nivel', $plantilla_convenio->id_nivel)->where('id_empresa', $empresa_labs->id)->first();
        if(is_null($convenio)){
            $convenio = Convenio::where('id_nivel', $plantilla_convenio->id_nivel)->first();
        }
        
        $id_seguro = $convenio->id_seguro;
        if ($id_seguro == '41') {
            $reglas2 = [
                'ticket' => 'required|unique:examen_orden',
            ];
            $mensaje2 = [
                'ticket.required' => 'Ticket es requerido',
                'ticket.unique'   => 'Ticket ya utilizado',
            ];
            $this->validate($request, $reglas2, $mensaje2);

        }
        
        $seguro = Seguro::find($id_seguro);

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
                'descripcion' => "PACIENTE",
                'dato_ant1'   => $request['id'],
                'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                'dato_ant4'   => "CREAR PACIENTE",
                'dato2'       => 'COTIZACION PLANTILLA',
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
            'id_protocolo'     => $plantilla_convenio->id_protocolo,
            'id_seguro'        => $id_seguro,
            'id_nivel'         => $plantilla_convenio->id_nivel,
            'est_amb_hos'      => $request['est_amb_hos'],
            'id_doctor_ieced'  => $request['id_doctor_ieced'],
            'doctor_txt'       => $request['doctor_txt'],
            'observacion'      => $request['observacion'],
            'id_empresa'       => $plantilla_convenio->id_empresa,
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
            'codigo'           => $plantilla_convenio->codigo,

        ];
        $id_examen_orden = Examen_Orden::insertGetId($input_ex);

        $examenes = Examen_Protocolo::where('id_protocolo', $plantilla_convenio->id_protocolo)->get();
        //return $examenes;
        $orden    = Examen_Orden::find($id_examen_orden);
        $txt_ex = '';
        foreach ($examenes as $examen) {
            //ACTUALIZA DETALLE
            $detalle = $orden->detalles->where('id_examen', $examen->id_examen);
            //return $detalle;
            if ($detalle->count() == 0) {
                $txt_ex = $txt_ex.'+'.$examen->examen->nombre;
                $valor    = $examen->examen->valor;
                $cubre    = 'NO';
                $ex_nivel = Examen_Nivel::where('id_examen', $examen->id_examen)->where('nivel', $orden->id_nivel)->first();
                if (!is_null($ex_nivel)) {
                    if ($ex_nivel->valor1 != 0) {

                        $valor = $ex_nivel->valor1;
                        $cubre = 'SI';

                    }
                }

                $input_det = [
                    'id_examen_orden' => $orden->id,
                    'id_examen'       => $examen->id_examen,
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

        $orden    = Examen_Orden::find($id_examen_orden);

        /// orden
        $total       = $orden->detalles->sum('valor');
        $total       = round($total, 2);
        $cantidad    = $orden->detalles->count();

        $input_ex2      = [
            'descuento_valor' => '0',
            'recargo_valor'   => '0',
            'total_valor'     => $total,
            'cantidad'        => $cantidad,
            'valor'           => $total,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        $orden->update($input_ex2);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $id_examen_orden,
            'dato1'       => $request['id'],
            'dato_ant2'   => "GENERA COTIZACION",
            'dato_ant4'   => $txt_ex,

        ]);

        return $id_examen_orden;

    }



    

}