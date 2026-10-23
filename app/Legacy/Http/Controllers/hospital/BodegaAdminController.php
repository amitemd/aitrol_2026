<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Hospital_Bodega;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;

class BodegaAdminController extends Controller
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
    public function bodega(){
     $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $bodega = Hospital_Bodega::paginate(10);

        return view('hospital_admin/bodega/bodega',['bodega'=>$bodega]);

    }
    public function agregarb(Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        Hospital_Bodega::create([
            'id_piso'=> $request['estado'],
            'nombre'=> $request['nombreb'],
            'color'=> $request['color'],
            'ubicacion'=> $request['ubicacion']

        ]);

        return redirect()->intended('hospital/admin/bodega');
    }
    
    public function agregar(){
      $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');

        }
     return view('hospital_admin/bodega/agregar');
    }
    public function editarb($id){
        $opcion = '1';
          if ($this->rol_new($opcion)) {
              return redirect('/');
  
          }
          $bodegaid= Hospital_Bodega::find($id);
      
       return view('hospital_admin/bodega/editarb',['bodegaid'=>$bodegaid]);
    }   
    public function updateb($id, Request $request){
        $opcion = '1';
          if ($this->rol_new($opcion)) {
              return redirect('/');
  
          }
          $bodegaid= Hospital_Bodega::find($id);
          $ip_cliente    = $_SERVER["REMOTE_ADDR"];
          $idusuario     = Auth::user()->id;
          $marqui= [
            'nombre' =>$request['nombreb'],
            'estado' =>$request['estado'],
            'color'=> $request['color'],
            'ubicacion'=> $request['ubicacion']
            
        ];
         $marcate= $bodegaid->update($marqui);
          return back();
    }
  
}