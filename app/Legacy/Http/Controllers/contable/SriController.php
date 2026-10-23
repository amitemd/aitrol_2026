<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Ct_rubros;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Http\Controllers\Controller;

class SriController extends Controller{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20)) == false) {
            return true;
        }
    }

    public function index(){

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $variablep = Ct_rubros::paginate(20);

        return view('contable/sri/index', ['variablep' => $variablep]);
    }
}