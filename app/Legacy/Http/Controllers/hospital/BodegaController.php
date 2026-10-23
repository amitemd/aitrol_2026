<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Hospital_Bodega;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;

class BodegaController extends Controller
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
public function bodegap (){
     $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
         $bodega= Hospital_Bodega::all();

        return view('hospital/producto/bodegap',['bodega'=>$bodega]);

    }
     public function buscadorbo (Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        if(($request->nombre)!=""){
            $bodega= Hospital_Bodega::where("nombre","like",$request->nombre."%")->get();
        }
          
        return view('hospital/producto/buscadorbo',['bodega'=>$bodega]);
    }
 
  
}