<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_User_Empleados;
use Sis_medico\Ct_Clase;
use Sis_medico\Pais;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\Ct_Clientes;
use Sis_medico\TipoUsuario;
use Sis_medico\User;
use Sis_medico\Log_usuario;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;

class EmpleadosController extends Controller
{

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

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $users = User::where('id_tipo_usuario', '<', 19)
            ->where('id_tipo_usuario', '>', 16)
            ->where('estado', 1)->paginate(15);
        $tipousuarios = tipousuario::where('id', 17)
            ->orWhere('id', 18)
            ->where('estado', 1)->get();
        return view('contable/empleados/index', ['users' => $users, 'tipousuarios' => $tipousuarios, 'empresa' => $empresa]);
    }
    public function search(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $constraints = [
            'id_tipo_usuario' => $request['id_tipo_usuario'],
            'id' => $request['id'],
            'apellido1' => $request['apellido']
        ];
        $users = $this->doSearchingQuery($constraints, $request);
        $tipousuarios = tipousuario::where('id', 17)
            ->orWhere('id', 18)
            ->where('estado', 1)->get();
        return view('contable/empleados/index', ['users' => $users, 'searchingVals' => $constraints, 'tipousuarios' => $tipousuarios, 'empresa' => $empresa]);
    }
    private function doSearchingQuery($constraints, Request  $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $query = User::query();
        //dd($query->get());
        $fields = array_keys($constraints);
        $index = 0;
        if ($request->id != null) {
            foreach ($constraints as $constraint) {
                if ($constraint != null) {
                    $query = $query->where($fields[$index], $constraint);
                }
                $index++;
            }
        } else {
            foreach ($constraints as $constraint) {
                if ($constraint != null) {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
                $index++;
            }
        }

        return $query->where('id_tipo_usuario', '<', 19)
            ->where('id_tipo_usuario', '>', 16)->paginate(10);
    }
    public function create(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $pais         = pais::all();
        $tipousuarios = tipousuario::all();

        //$clases = Ct_Clase::where('estado', '1')->get();
        $cuentas = plan_cuentas::where('estado', '2')->get();

        return view('contable/empleados/create', ['pais' => $pais, 'cuentas' => $cuentas, 'tipousuarios' => $tipousuarios, 'empresa' => $empresa]);
    }

    public function find_cta_comision(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $buscador = $request['search'];
        $cuentas  = [];

        if ($buscador != null) {
            $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->where('pe.estado', '2');

            $cuentas = $cuentas->where(function ($jq1) use ($buscador) {
                $jq1->orwhere('pe.nombre', "LIKE", "%{$buscador}%")
                    ->orwhere('pe.id_plan', 'LIKE', "%{$buscador}%");
            });

            $cuentas = $cuentas->select('pe.id_plan as id', DB::raw('CONCAT(pe.id_plan," | ",pe.nombre) as text'))->get();
        }


        return response()->json($cuentas);
    }
    private function validateInput($request)
    {
        $this->validate($request, []);
    }
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        //$this->validateInput($request);
        $id_empresa = $request->session()->get('id_empresa');
        /*$mensajes = [
            'email.unique'              => 'El Email ya se encuentra registrado.',
            'id.unique'                 => 'El nombre de usuario ya se encuentra registrado en el sistema.',
            'id.required'               => 'Error',
        ];
        $constraints = [
            'id'               => 'required|unique:users,id',
            'email'            =>'required| unique:users,email'            

        ];*/
        //$this->validate($request, $constraints, $mensajes);
        $user = User::find($request['id']);
        if (!is_null($user)) {
            $act_tipo = [
                'id_tipo_usuario' => $request['id_tipo_usuario'],
            ];
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO USUARIO",
                'dato_ant1'   => $request['id'],
                'dato1'       => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
                'dato_ant2'   => "TIPO USUARIO: " . $request['id_tipo_usuario'],
            ]);
            Ct_User_Empleados::create([
                'id_user'            => $request['id'],
                'id_empresa'         => $id_empresa,
                'id_tipo_usuario'    => $request['id_tipo_usuario'],
                'id_grupo'           => $request['grupo'],
                'cta_comision'       => $request['cta_comision'],
                'profesion'          => $request['profesion'],
                'id_sexo'            => $request['sexo'],
                'id_estado_civil'    => $request['estado_civil'],
                'comentarios'        => $request['comentario'],
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
            ]);
        } else {
            User::create([
                'id'               => $request['id'],
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
                'id_tipo_usuario'  => $request['id_tipo_usuario'],
                'email'            => $request['email'],
                'password'         => bcrypt($request['password']),
                'tipo_documento'   => 1,
                'estado'           => 1,
                'imagen_url'       => ' ',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
            ]);
        }
        return redirect()->intended('/contable/empleados');
    }

    public function editar($id, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $rolusuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $user = User::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($user == null || count($user) == 0) {
            return redirect()->intended('/contable/empleados');
        }
        $paises = pais::all();
        $tipousuarios = tipousuario::where('id', 17)
            ->orWhere('id', 18)
            ->where('estado', 1)->get();
        return view('contable/empleados/edit', ['user' => $user, 'paises' => $paises, 'tipousuarios' => $tipousuarios, 'id' => $id, 'rolusuario' => $rolusuario, 'empresa' => $empresa]);
    }
    public function update(Request $request, $id)
    {
        $usuario = User::where('id', $id)->first();
        //dd($usuario);
        $usuario1 = Ct_User_Empleados::where('id', $id)->first();
        //dd($usuario1);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        /*$mensajes = [
            'email.unique'              => 'El Email ya se encuentra registrado.',
            'id.unique'                 => 'El nombre de usuario ya se encuentra registrado en el sistema.',
            'id.required'               => 'Error',
        ];
        $constraints = [
            'id'               => 'required|unique:users,id',
            'email'            =>'required| unique:users,email'            

        ];*/
        //$this->validate($request, $constraints, $mensajes);
        if (!is_null($usuario)) {
            $campo1 = ([
                'id'               => $request['id'],
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
                'id_tipo_usuario'  => $request['id_tipo_usuario'],
                'email'            => $request['email'],
                'password'         => bcrypt($request['password']),
                'tipo_documento'   => 1,
                'estado'           => 1,
                'imagen_url'       => ' ',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
            ]);
            $variable = ([
                'id_user'            => $request['id'],
                'id_grupo'           => $request['grupo'],
                'cta_comision'       => $request['cta_comision'],
                'profesion'          => $request['profesion'],
                'id_sexo'            => $request['sexo'],
                'id_estado_civil'    => $request['estado_civil'],
                'comentarios'        => $request['comentario'],
                'estado'             => $request['estado'],
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
            ]);
        }

        //dd($campo1);
        User::where('id', $id)->update($campo1);
        Ct_User_Empleados::where('id', $id)->update($variable);

        return redirect()->intended('/contable/empleados');
    }
}
