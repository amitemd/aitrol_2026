<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Globales;
use Sis_medico\Ct_Modulos;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas; 
use Sis_medico\Empresa;  


class InformeContableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }   
        
        $id_empresa  = $request->session()->get('id_empresa');
        $compras= Ct_compras::where('estado','<>','0')->where('id_empresa',$id_empresa)->get();
        $empresa= Empresa::find($id_empresa);
        return view('contable/informe/index',['compras'=>$compras,'empresa'=>$empresa]);

    } 
 
}
