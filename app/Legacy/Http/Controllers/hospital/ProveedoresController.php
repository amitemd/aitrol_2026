<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Cama;
use Sis_medico\Hospital_Proovedor;
use Sis_medico\Hospital_Tipo_Proovedor;
use Sis_medico\CamaTransaccion;
use Sis_medico\Habitacion;
use Sis_medico\Hospital_Marca;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;

class ProveedoresController extends Controller
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
 public function proveedores()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $proovedor= Hospital_Proovedor::all();
        
        return view('hospital/proveedore/proveedores',['proovedor'=>$proovedor]);
    }
     public function buscadort(Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        if(($request->nombrecomercial)!=""){
            $proovedor= Hospital_Proovedor::where("nombrecomercial","like",$request->nombrecomercial."%")->get();
        }
        elseif(($request->ruc)!=""){
            $proovedor= Hospital_Proovedor::where("ruc","like",$request->ruc."%")->get();    
        }
          
        return view('hospital/proveedore/buscadort',['proovedor'=>$proovedor]);
    }
    
}