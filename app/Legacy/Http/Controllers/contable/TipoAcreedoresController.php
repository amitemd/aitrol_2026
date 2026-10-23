<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\TipoProveedor;
use Sis_medico\Empresa;

class TipoAcreedoresController extends Controller
{

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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 20,22)) == false) {
            return true;
        }
    }

    public function index()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $tipo_proveedor = TipoProveedor::paginate(5);
        return view('contable/tipo_acreedor/index', ['tipos' => $tipo_proveedor]);
    }

    public function create(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();    
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        return view('contable/tipo_acreedor/create',['empresa' => $empresa]);

    }

    public function store(Request $request)
    {

        $reglas = [
            'nombre'      => 'required|max:255',
            'descripcion' => 'required|max:255',
        ];

        $mensajes = [
            'descripcion.required' => 'Agrega la descripcion.',
            'descripcion.max'      => 'La descripcion no puede ser mayor a :max caracteres.',
            'nombre.required'      => 'Agrega el nombre .',
            'nombre.max'           => 'El nombre no puede ser mayor a :max caracteres.',
        ];

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $this->validateInput($request, $reglas, $mensajes);
        date_default_timezone_set('America/Guayaquil');
        TipoProveedor::create([
            'nombre'          => strtoupper($request['nombre']),
            'descripcion'     => strtoupper($request['descripcion']),
            'visualizar'      => '2',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ]);

        return redirect()->route('tipoacreedor.index');
    }

    private function validateInput($request)
    {
        $this->validate($request, []);
    }

    public function edit($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $proveedor = TipoProveedor::find($id);
        // Redirect to user list if updating user wasn't existed

        if (is_null($proveedor)) {
            return redirect()->intended('/dashboard');
        }

        return view('contable/tipo_acreedor/edit', ['tipos' => $proveedor]);
    }
    public function update(Request $request, $id)
    {
        $empresa    = TipoProveedor::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $mensajes = [
            'descripcion.required' => 'Agrega la descripcion.',
            'descripcion.max'      => 'La descripcion no puede ser mayor a :max caracteres.',
            'nombre.required'      => 'Agrega el nombre .',
            'nombre.max'           => 'El nombre no puede ser mayor a :max caracteres.',
        ];
        $constraints = [
            'nombre'      => 'required|max:255',
            'descripcion' => 'required|max:255',
        ];

        $input = [
            'nombre'          => strtoupper($request['nombre']),
            'descripcion'     => strtoupper($request['descripcion']),
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,

        ];

        $this->validate($request, $constraints, $mensajes);

        TipoProveedor::where('id', $id)
            ->update($input);

        return redirect()->route('tipoacreedor.index');
    }
}
