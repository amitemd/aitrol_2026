<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Sis_medico\Contabilidad;
use Sis_medico\Empresa;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;

class ContabilidadController extends Controller
{
    public function index(Request $r)
    {
        $idUsuario = isset(Auth::user()->id) ? auth::user()->id : '';
        if ($idUsuario == '') {
            return redirect('login');
        }
        if ($r->opcion == 'balanceGeneral') {
            $desde = str_replace('/', '-', $r->fecha_desde != '' ? $r->fecha_desde : date('Y') . '-01-01');
            $desde = date('Y-m-d', strtotime($desde));
            $data['desde'] = $desde;
            $hasta = str_replace('/', '-', $r->fecha_hasta != '' ? $r->fecha_hasta : date('Y-m-d'));
            $hasta = date('Y-m-d', strtotime($hasta));
            $data['hasta'] = $hasta;
            if ($r->fecha_hasta != '') {
                $data['datos'] = json_decode(json_encode(Contabilidad::cuerpoBalance($r)));
            }
            $data['empresa'] = Empresa::find(session('id_empresa'));
            return view('contable.balance_general.view_balance_general', $data);
        } elseif ($r->opcion == 'perdidasGanancias') {
            $desde = str_replace('/', '-', $r->fecha_desde != '' ? $r->fecha_desde : date('Y') . '-01-01');
            $desde = date('Y-m-d', strtotime($desde));
            $data['desde'] = $desde;
            $hasta = str_replace('/', '-', $r->fecha_hasta != '' ? $r->fecha_hasta : date('Y-m-d'));
            $hasta = date('Y-m-d', strtotime($hasta));
            $data['hasta'] = $hasta;
            if ($r->fecha_hasta != '') {
                $data['datos'] = Contabilidad::cuerpoPerdidasGanancias($r);
            }
            $data['empresa'] = Empresa::find(session('id_empresa'));
            return view('contable.balance_general.view_perdidas_ganancias', $data);
        } elseif ($r->opcion == 'balanceComprobacion') {
            $desde = str_replace('/', '-', $r->fecha_desde != '' ? $r->fecha_desde : date('Y') . '-01-01');
            $desde = date('Y-m-d', strtotime($desde));
            $data['desde'] = $desde;
            $hasta = str_replace('/', '-', $r->fecha_hasta != '' ? $r->fecha_hasta : date('Y-m-d'));
            $hasta = date('Y-m-d', strtotime($hasta));
            $data['hasta'] = $hasta;
            if ($r->fecha_hasta != '') {
                $data['datos'] = Contabilidad::balanceComprobacion($r);
            }
            $data['empresa'] = Empresa::find(session('id_empresa'));
            return view('contable.balance_general.view_balance_comprobacion', $data);
        } else {
            echo 'Usted no tiene permiso para acceder a esta opción';
        }
    }
}
