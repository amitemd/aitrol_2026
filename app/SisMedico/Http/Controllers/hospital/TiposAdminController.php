<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Cama;
use Sis_medico\CamaTransaccion;
use Sis_medico\Habitacion;
use Sis_medico\Hospital_Tipo;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;

class TiposAdminController extends Controller
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
public function tipoprodu(){
     $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $tipoproduc = Hospital_Tipo::paginate(10);

        return view('hospital_admin/producto/tipoprodu',['tipoproduc'=>$tipoproduc]);

    }
    public function agregarprodu(){
     $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $tipoproduc = Hospital_Tipo::all();

        return view('hospital_admin/producto/agregarprodu');

    }
     public function modalprovedor(){
     $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        return view('hospital_admin/producto/modalprovedor');

    }
    public function agregartipo(Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
    
        Hospital_Tipo::create([
            'nombre'         => $request['nombre'],
            'descripcion'    => $request['descripcion'],
            'estado'          => $request['estado'],
            'id_usuariomod'   => $idusuario,
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
    
        ]);
        
    
        return back();
     }
        
        public function modaltipop(){
          $opcion = '1';
            if ($this->rol_new($opcion)) {
                return redirect('/');
    
            }
        
         return view('hospital_admin/producto/modaltipop');
        }
        public function editartip($id){
            $opcion = '1';
              if ($this->rol_new($opcion)) {
                  return redirect('/');
      
              }
              $tipop= Hospital_Tipo::find($id);
          
           return view('hospital_admin/producto/editartip',['tipop'=>$tipop]);
          }
        
          public function updatematipo($id, Request $request){
            $opcion = '1';
              if ($this->rol_new($opcion)) {
                  return redirect('/');
      
              }
              $tipop= Hospital_Tipo::find($id);
              $ip_cliente    = $_SERVER["REMOTE_ADDR"];
              $idusuario     = Auth::user()->id;
              $marqui= [
                'nombre' =>$request['nombre'],
                'descripcion'=> $request['descripcion'],
                'estado' =>$request['estado'],
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];
             $marcate= $tipop->update($marqui);
              return back();
          }
    
    }
