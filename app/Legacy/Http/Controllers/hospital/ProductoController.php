<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Cama;
use Sis_medico\CamaTransaccion;
use Sis_medico\Habitacion;
use Sis_medico\Hospital_Producto;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;

class ProductoController extends Controller
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
 public function producto(){
     $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $codigo= Hospital_Producto::all();
        return view('hospital/producto/producto',['codigo'=>$codigo]);

    }

    public function pedidosproductos(){
        $opcion = '1';
            if ($this->rol_new($opcion)) {
                return redirect('/');
            }
        
            return view('hospital/producto/pedidosproductos');

    }
    public function buscador(Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        if(($request->nombre)!=""){
            $codigo= Hospital_Producto::where("nombre","like",$request->nombre."%")->get();
        }
        elseif(($request->codigo)!=""){
            $codigo= Hospital_Producto::where("codigo","like",$request->codigo."%")->get();    
        }
          
        return view('hospital/producto/buscador',['codigo'=>$codigo]);
    }
    
}