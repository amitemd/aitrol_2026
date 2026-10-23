<?php

namespace Sis_medico\Http\Controllers\contable;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Comprobante_Ingreso;
use Sis_medico\Empresa;

class AnticipoClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;
        if (in_array($rolUsuario, [1, 4, 5, 20, 22]) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {

        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $clientes = Ct_Clientes::all();
        $cliente = $request['cliente'];
        $id_empresa = $request->session()->get('id_empresa');
        $informe = Ct_Comprobante_Ingreso::where('id_empresa', $id_empresa)
            ->where('tipo', '1')
            ->where('estado', '<>', '0');

        if ($fecha_hasta != null && $fecha_desde != null) {
            $informe = $informe->whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else  if ($fecha_desde == null) {
            $fecha_desde = date('Y-m-d');
            $informe = $informe->whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if (!is_null($cliente)) {

            $informe = $informe->where('id_cliente', $cliente);
        }



        $informe = $informe->orderBy('fecha', 'DESC')->get();
        $empresa = Empresa::find($id_empresa);
        return view('anticipos_clientes_reporte.index', [
            'fecha_desde' =>  $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
            'id_cliente' => $cliente,
            'clientes' => $clientes,
            'informe' => $informe,
            'empresa' => $empresa
        ]);
    }
}
