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

class MarcasAdminController extends Controller
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
        $marcas= Hospital_Marca::paginate(5);

        return view('hospital_admin/marcas/marcas',['marcas'=>$marcas]);

    }
    //AGREGAR MARCAS
    public function agregarm(Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        Hospital_Marca::create([
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
    //MODAL MARCAS
    public function modalmarcas(){
      $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');

        }
    
     return view('hospital_admin/marcas/modalmarcas');
    }
    //EDITAR MARCAS
    public function editarm($id){
        $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');

        }
        $marcasid= Hospital_Marca::find($id);
        return view('hospital_admin/marcas/editarm',['marcasid'=>$marcasid]);
    }
    //
    public function updatema($id, Request $request){
        $opcion = '1';
          if ($this->rol_new($opcion)) {
              return redirect('/');
  
          }
          $marcasid= Hospital_Marca::find($id);
          $ip_cliente    = $_SERVER["REMOTE_ADDR"];
          $idusuario     = Auth::user()->id;
          $marqui= [
            'nombre' =>$request['nombre'],
            'estado' =>$request['estado'],
            'id_usuariomod'   => $idusuario,
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
        ];
        $marcate= $marcasid->update($marqui);
        return back();
    }

      

}