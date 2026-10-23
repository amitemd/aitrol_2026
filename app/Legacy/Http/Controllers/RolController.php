<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Ct_Rol_Pagos;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;

class RolController extends Controller
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
        if (in_array($rolUsuario, array(1)) == false) {
            return true;
        }
    }

    public function historial_rol()
    {

        $anio    = date('Y');
        $mes     = date('m');
        $id_auth = Auth::user()->id;
        $usuario = User::find($id_auth);
        $rol_pag = Ct_Rol_Pagos::where('estado', '1')->where('id_user', $usuario->id)->orderby('id', 'asc')->where('certificado','1')->get();
        //dd($rol_pag);
        return view('rol/index', ['id_auth' => $id_auth, 'anio' => $anio, 'mes' => $mes, 'rol_pag' => $rol_pag, 'usuario' => $usuario]);

    }

    public function rol_lista(Request $request)
    {
        $anio = $request['anio'];
        $mes  = $request['mes'];

        if ($request['anio'] == null) {
            $anio = date('Y');
        }
        if ($request['mes'] == null) {
            $mes = date('m');
        }

        $id_auth = Auth::user()->id;
        $usuario = User::find($id_auth);
        $rol_pag = Ct_Rol_Pagos::where('estado', '1')->where('id_user', $usuario->id)->where('anio', $anio)->where('mes', $mes)->orderby('id', 'asc')->where('certificado','1')->get();
        return view('rol/index', ['id_auth' => $id_auth, 'anio' => $anio, 'mes' => $mes, 'rol_pag' => $rol_pag, 'usuario' => $usuario]);

    }

}
