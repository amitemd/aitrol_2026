<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Piso;
use Sis_medico\CamaTransaccion;
use Sis_medico\Habitacion;
use Sis_medico\Hospital;
use Sis_medico\Hospital_Proovedor;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;

class PedidosAdminController extends Controller
{
public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion){ 
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          return true;
        }
    }
  
     

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function pedidos (){
     $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        return view('hospital_admin/pedidos/pedidos');

    }
     public function transito (){
     $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        return view('hospital_admin/pedidos/transito');

    }



}