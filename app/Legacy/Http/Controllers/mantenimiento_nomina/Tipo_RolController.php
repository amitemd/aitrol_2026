<?php

namespace Sis_medico\Http\Controllers\mantenimiento_nomina;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Ct_Tipo_Rol;

class Tipo_RolController extends Controller
{
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     *
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 8,  19, 20, 21, 22)) == false) {
            return true;
        }
    }


    public function index(){
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
     
        $tipos_roles = Ct_Tipo_Rol::where('estado', '1')->get();

    return view('mantenimiento_nomina/tipo_rol/index', ['tipos_roles' => $tipos_roles]);
    }

    public function crear(){

        if ($this->rol()) {
            return response()->view('errors.404');
        }
       
        return view ('mantenimiento_nomina/tipo_rol/crear');

    }
   
    public function store(Request $request){
    
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
                           
        $arr_tipo_rol = [
          'descripcion'           => $request['descripcion_tipo'],
          'estado'                => 1,
   
          'id_usuariocrea'       => $idusuario,
          'id_usuariomod'        => $idusuario,
          'ip_creacion'          => $ip_cliente,
          'ip_modificacion'      => $ip_cliente,

                 
        ];
   
        Ct_Tipo_Rol::create($arr_tipo_rol);
   
        return redirect(route('tipo_rol.index'));
    }

    public function edit($id){
      $tipos_rol = Ct_Tipo_Rol::find($id);
  
      return view ('mantenimiento_nomina/tipo_rol/editar', ['tipos_rol' => $tipos_rol , 'id' => $id]);
    }

    public function update(Request $request){
      
      $ip_cliente = $_SERVER["REMOTE_ADDR"];
      $idusuario  = Auth::user()->id;
      
      $id = $request->id;

      $tipos_rol = Ct_Tipo_Rol::find($id);
      $estado = $request->estado;
                           
        $arr_tipo_rol = [
          'descripcion'           => $request['descripcion'],
          'estado'                => $estado,
   
          'id_usuariocrea'       => $idusuario,
          'id_usuariomod'        => $idusuario,
          'ip_creacion'          => $ip_cliente,
          'ip_modificacion'      => $ip_cliente,

                 
        ];
      $tipos_rol->update($arr_tipo_rol);
      
       
      return redirect(route('tipo_rol.index'));

    }

}