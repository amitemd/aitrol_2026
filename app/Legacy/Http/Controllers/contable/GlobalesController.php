<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Globales;
use Sis_medico\Ct_Modulos;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Empresa;


class GlobalesController extends Controller
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
    public function actualizarSucursalAsiento(Request $r)
    {
        $arrayAsiento = [
            'sucursal' => $r->sucursal,
            'id_usuariomod' => Auth::user()->id,
            'ip_modificacion' => 'Se actualizo la sucursal'
        ];
        $result = Ct_Asientos_Cabecera::where('id', $r->id_asiento)->update($arrayAsiento);
        if ($result == 1)
            return 'ok';
        else
            return '';
    }
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa  = $request->session()->get('id_empresa');
        $modulos = Ct_Modulos::where('estado', '1')->get();
        $globales = Ct_Globales::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $empresa = Empresa::find($id_empresa);
        $plan_cuenta = Plan_Cuentas::where('plan_cuentas.estado', '2')->join('plan_cuentas_empresa as pe', 'pe.id_plan', 'plan_cuentas.id')->where('pe.id_empresa', $id_empresa)->select("plan_cuentas.*", 'pe.plan')->get();
        return view('contable/globales/index', ['globales' => $globales, 'modulos' => $modulos, 'plan_cuentas' => $plan_cuenta, 'empresa' => $empresa]);
    }
    public function edit(Request $request, $id)
    {
    }

    public function editCuenta(Request $request)
    {
        $id = $request->id;
        $cuenta = $request->cuenta;
        $tipo = $request->tipo;
        //1 debe 2) haber
        $id_empresa  = $request->session()->get('id_empresa');
        $globales = Ct_Globales::where('id', $id)->where('id_empresa', $id_empresa)->first();
        if ($tipo == 1) {
            $globales->debe = $cuenta;
        } else {
            $globales->haber = $cuenta;
        }
        $globales->save();
        if (count($globales) > 0) {
            return ['respuesta' => 'ok'];
        } else {
            return ['respuesta' => 'no'];
        }


        return $cuenta;
    }
}
