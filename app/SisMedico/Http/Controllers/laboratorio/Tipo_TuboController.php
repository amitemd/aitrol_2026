<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Labs_Tipo_Tubo;

class Tipo_TuboController extends Controller
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
    public function index(){
        $mantenimientos_tubos = Labs_Tipo_Tubo::where('estado','1')->get();
        return view('laboratorio/mantenimiento_tubos/index' , ['mantenimientos_tubos'=>$mantenimientos_tubos]);
    }
    public function crear(){
       
        return view('laboratorio/mantenimiento_tubos/crear');
    }

    public function store(Request $request){
    
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
                               
        $arr_tipo_tubo = [
            'nombre'           => $request['nombre_tubo'],
            'estado'           => 1,
            'color'             =>$request['tubo_color'],
       
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
    
                     
        ];
      
       
        Labs_Tipo_Tubo::create($arr_tipo_tubo);
       
        return redirect(route('tipo_tubo.index'));
    }
    public function editar($id){
        $mantenimientos_tubos = Labs_Tipo_Tubo::find($id);
    
        return view ('laboratorio/mantenimiento_tubos/editar', ['mantenimientos_tubos' => $mantenimientos_tubos , 'id' => $id]);
    }
    public function update(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        $id = $request->id;
  
        $mantenimientos_tubos = Labs_Tipo_Tubo::find($id);
        $estado = $request->estado;
                             
          $arr_tipo_tubo = [
            'nombre'              => $request['nombre_tubo'],
            'estado'              => 1,
            'color'               =>$request['tubo_color'],
     
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
  
                   
          ];
        $mantenimientos_tubos->update($arr_tipo_tubo);
        
         
        return redirect(route('tipo_tubo.index'));

    }
    public function delete(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id = $request->id;

        $informacion_tubos = Labs_Tipo_Tubo::find($id);
        //dd($informacion_tubos);
        $array_eliminar = [

            'nombre' => $informacion_tubos->nombre,
            'estado'     => 0,
            'color' => $informacion_tubos->color,
    
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
            
        ];

        $informacion_tubos->update($array_eliminar);

        return redirect(route('tipo_tubo.index'));
    }









    
}