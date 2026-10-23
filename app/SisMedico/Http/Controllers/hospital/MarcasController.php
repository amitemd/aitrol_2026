<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Cama;
use Sis_medico\CamaTransaccion;
use Sis_medico\Habitacion;
use Sis_medico\Hospital_Marca;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;

class MarcasController extends Controller
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
    public function marcas(){
        $opcion = '1';
            if ($this->rol_new($opcion)) {
                return redirect('/');
            }
        
            $marcas = Hospital_Marca::paginate(8);
            return view('hospital/marcas/marcas',['marcas'=>$marcas]);

    }
    public function buscadorm(Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }       
        if(($request->nombre)!=""){
            $marcas= Hospital_Marca::where("nombre","like",$request->nombre."%")->get();
            }
        return view('hospital/marcas/tablamarca',['marcas'=>$marcas]);
    }   
}