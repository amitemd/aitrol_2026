<?php

namespace Sis_medico\Http\Controllers\hc4;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Sis_medico\Principio_Activo;
use Sis_medico\Opcion_Usuario;


class GenericosController extends Controller
{
    //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
    private function rol_new($opcion){ 
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          return true;
        }   
    }

    protected $redirectTo = '/'; 
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


     //LLAMANDO A LA VISTA LISTAR MEDICINA GENERICA
    //DONDE SE MUESTRA LA VISTA DE LOS GENERICOS A EDITAR
    //SOLO PARA DOCTORES
    public function listar_medicina_generico(){

        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
       
        $genericos = Principio_Activo::where('estado',1)->orderBy('nombre')->paginate(20);
        
        return view('hc4.generico.listado_genericos',['genericos' => $genericos,'nombre' => null]);  
    }



    //LLAMANDO A LA VISTA CREATE_GENERICO
    //DONDE SE INGRESAN LOS DATOS DEL NUEVO GENERICO
    //SOLO PARA DOCTORES
    public function crear_generico(){
       $opcion = '2';
       if($this->rol_new($opcion)){
        return redirect('/');
       }
        
      return view('hc4/generico/create_generico');  
    }

    //FUNCION GUARDAR GENERICO
    //SOLO PARA DOCTORES
    public function guarda_generico(Request $request){

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $this->validateInput($request);

        $input = [

                'nombre' => $request['nombre'],
                'descripcion' => $request['descripcion'],
                'estado' => $request['estado'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,    
        ];

      Principio_Activo::create($input);

      return redirect()->route('listar.medicina_generica.hc4');

    }


    //LLAMANDO A LA VISTA EDITAR_GENERICO
    //DONDE SE MUESTRA LA VISTA DE LA MEDICINA GENERICA A EDITAR
    //SOLO PARA DOCTORES
    public function edit_generico($id){
        $opcion = '2';
        if($this->rol_new($opcion)){
          return redirect('/');
        }

        $generico = Principio_Activo::find($id);

        return view('hc4/generico/editar_generico', ['generico' => $generico]); 
    
    }

    //ACTUALIZANDO EL GENERICO EDITADO
    //SOLO PARA DOCTORES
    public function update_generico(Request $request){
        $opcion = '2';
        if($this->rol_new($opcion)){
          return redirect('/');
        }
    
        $id= $request['idgenerico'];

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $generico = Principio_Activo::findOrFail($id);

        $this->validateInput($request);

        $input = [

          'nombre' => $request['nombre'],
          'descripcion' => $request['descripcion'],
          'estado' => $request['estado'],
          'ip_modificacion' => $ip_cliente,
          'id_usuariomod' => $idusuario,    

        ];

        $generico->update($input);
       
        $genericos = Principio_Activo::where('estado',1)->where('nombre','like','%'.$request['nombre'].'%')->orderBy('nombre')->paginate(20);  

        return view('hc4.generico.listado_genericos', ['genericos' => $genericos, 'nombre' => $request['nombre']]); 

    }

    private function validateInput($request){

        $rules = [
            'nombre' => 'required|max:100',
            'descripcion' => 'required|max:255',
        ];

        $mensajes = [
            'nombre.required' => 'Ingrese el nombre.',
            'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.',
            'descripcion.required' => 'Ingrese la descripcion.',
            'descripcion.max' =>'La descripcion no puede ser mayor a :max caracteres.',
        ];
         
        $this->validate($request, $rules, $mensajes);
    }

    //INGRESO DEl GENERICO A BUSCAR
    //DONDE SE MUESTRAN LOS GENERICOS ENCONTRADOS Y PODER EDITARLOS
    //SOLO PARA DOCTORES
    public function search_generico(Request $request){
      $opcion = '2';
      if($this->rol_new($opcion)){
          return redirect('/');
      }
       
      
      $nombre = $request['nombre'];

      $genericos = Principio_Activo::where('estado',1)->where('nombre','like','%'.$nombre.'%')->orderBy('nombre')->paginate(20);  

      return view('hc4.generico.listado_genericos', ['genericos' => $genericos, 'nombre' => $nombre]); 

    }

    
}
 