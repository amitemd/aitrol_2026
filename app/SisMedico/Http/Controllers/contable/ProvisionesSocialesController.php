<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Sis_medico\Ct_Rol_Pagos;
use Sis_medico\Ct_Nomina;
use Sis_medico\Empresa;

class ProvisionesSocialesController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5 , 20,22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $listado_nomina = Ct_Rol_Pagos::where('ct_rol_pagos.estado','1')
                          ->join('ct_nomina as ctn','ctn.id','ct_rol_pagos.id_nomina')
                          ->join('ct_detalle_rol as drp', 'drp.id_rol', 'ct_rol_pagos.id')
                          ->select('ct_rol_pagos.id_user as id_usuario','ct_rol_pagos.id_empresa as ident_emp','ct_rol_pagos.anio as anio_rol','ct_rol_pagos.mes as mes_rol','drp.sobre_tiempo50 as hor_ext50','drp.sobre_tiempo100 as hor_ext100','drp.sueldo_mensual as sueldo','drp.anticipo_quincena as anticipo')
                          ->orderby('ct_rol_pagos.id', 'desc')->paginate(5);
                          
        $empresas = Empresa::all();

        return view('contable/rol_provisiones_sociales/index',['registros' => $listado_nomina,'empresas' => $empresas]);
    
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $constraints = [
            'id_user'       => $request['identificacion'],
            'id_empresa'    => $request['id_empresa'],
           
        ];
        $registros = $this->doSearchingQuery($constraints);
        $empresas = Empresa::all();
        
        return view('contable/rol_provisiones_sociales/index', ['request' => $request, 'empresas' => $empresas, 'registros' => $registros, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Rol_Pagos::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(5);
    }


}
