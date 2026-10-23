<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Empresa;
use Sis_medico\Ct_Nomina;
use Sis_medico\User;
use Sis_medico\Ct_Rh_Valor_Anticipos;
use Sis_medico\Ct_Rh_Tipo_Pago;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Rh_Anticipos;
use Excel;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Ct_Configuraciones;


class NominaAnticiposEmpresaController extends Controller
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

        $id_empresa = $request->session()->get('id_empresa');

        $empresa = Empresa::where('id', $id_empresa)->first();
        
        return view('contable.rol_anticipo_empl_empresa.index',['empresa' => $empresa]);
    
    }

    public function buscar_empleado_anticipo(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $id_anio = $request['year'];
        $id_mes = $request['mes'];

        $empl_rol = Ct_Nomina::where('estado','1')->where('id_empresa', $id_empresa)->orderby('id', 'asc')->get();

        return view('contable.rol_anticipo_empl_empresa.index',['empresa' => $empresa]);


    }

}
