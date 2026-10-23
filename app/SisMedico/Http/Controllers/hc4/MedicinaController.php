<?php

namespace Sis_medico\Http\Controllers\hc4;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Principio_Activo;
use Sis_medico\Medicina;
use Sis_medico\Examen_Orden;
use Sis_medico\Medicina_Principio;
use Sis_medico\Opcion_Usuario;
use Response;

class MedicinaController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    /*private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 3, 6,11)) == false){
          return true;
        }
    }*/

    //LLAMANDO A LA VISTA INICIO_MEDICINA
    //DONDE SE CREA Y EDITA LA MEDICINA
    //SOLO PARA DOCTORES
    public function create_edit_medicina(){

       $opcion = '2';
       if($this->rol_new($opcion)){
        return redirect('/');
       }

       /*$id_usuario = Auth::user()->id;
       
       $fecha1 =  date('Y/m/d ')."00:00:00";
       $fecha2 =  date('Y/m/d ')."23:59:59";

       $agenda_consultas = DB::select("select * from `agenda` where `proc_consul` = '0' 
        and (`id_doctor1` = '".$id_usuario."' or `id_doctor2` = '".$id_usuario."' or `id_doctor3` = '".$id_usuario."')  AND estado = '1'  AND
       `fechaini` between '".$fecha1."' and '".$fecha2."'");
       

        $procedimiento_consultas = DB::select("select * from `agenda` where `proc_consul` = '1' 
        and (`id_doctor1` = '".$id_usuario."' or `id_doctor2` = '".$id_usuario."' or `id_doctor3` = '".$id_usuario."')  AND estado = '1'  AND
       `fechaini` between '".$fecha1."' and '".$fecha2."'");


        $procedimiento_todas = DB::select("select * from `agenda` where `proc_consul` = '1' 
              AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");
       
        
         $consultas_todas = DB::select("select * from `agenda` where `proc_consul` = '0' 
              AND estado = '1'  AND
        `fechaini` between '".$fecha1."' and '".$fecha2."'");


        $ordenes_laboratorio = Examen_Orden::whereBetween('fecha_orden', [$fecha1, $fecha2])->count();*/

        /*return view('hc4/medicina/inicio_medicina', ['agenda_consultas' => $agenda_consultas,'consultas_todas' => $consultas_todas, 'procedimiento_consultas' => $procedimiento_consultas, 'procedimiento_todas' => $procedimiento_todas,'ordenes_laboratorio'=>$ordenes_laboratorio]);*/

        return view('hc4/medicina/inicio_medicina');

    
    }

    //LLAMANDO A LA VISTA CREATE_MEDICINA
    //DONDE SE INGRESAN LOS DATOS DE LA NUEVA MEDICINA
    //SOLO PARA DOCTORES
    public function crear_medicina(){
       $opcion = '2';
       if($this->rol_new($opcion)){
        return redirect('/');
       }
        
        $genericos = Principio_Activo::where('estado',1)->get();
       
        return view('hc4/medicina/create_medicina', ['genericos' => $genericos]);  
    }

    //FUNCION GUARDAR MEDICINA
    //SOLO PARA DOCTORES
    public function guarda_medicina(Request $request){
        //return $request->all();

        $opcion = '2';
        if($this->rol_new($opcion)){
          return redirect('/');
        }
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if($request['dieta'] == 0){
           $this->validateInput2($request);
        }else{
           $this->validateInput($request);
        }

        $input = [

            'nombre' => $request['nombre'],
            'cantidad' => $request['cantidad'],
            'dosis' => $request['dosis'],
            'estado' => '1',
            'dieta' => $request['dieta'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario, 
            'iess' => $request['iess_medicina'],


        ]; 

        $id = Medicina::insertGetId($input);
        
        
        if($request['dieta'] != 1){

            $genericos_m = $request['genericos'];

            foreach ($genericos_m as $md) {
                if(is_numeric($md)){
                    $inputg = [

                        'id_medicina' => $id,
                        'id_principio_activo' => $md,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,

                    ];

                   
                    Medicina_Principio::create($inputg);
                }else{
                    
                    $input_principio = [
                        'nombre' => substr(strtoupper($md), 0,-5),
                        'descripcion' => substr(strtoupper($md), 0,-5),
                        'estado' => '1',
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario, 
                    ];
                    $id_generico = Principio_Activo::insertGetId($input_principio);
                    $inputg = [

                        'id_medicina' => $id,
                        'id_principio_activo' => $id_generico,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,

                    ];

                    Medicina_Principio::create($inputg);

                }
            }
        }

        return redirect()->route('editar.medicina_hc4');

        //$url = route("nuevo.diseño");    
        //return redirect($url);

    }

    private function validateInput2($request) {

        $rules = [
        
        'nombre' => 'required|max:100',
        'cantidad' => 'required|max:50',
        'dosis' => 'required',
        'genericos' => 'required',
        
        ];
        
        $mensajes = [
            'nombre.required' => 'Ingrese el nombre.',
            'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.',
            'cantidad.required' => 'Ingrese la cantidad.',
            'cantidad.max' =>'La cantidad no puede ser mayor a :max caracteres.',
            'dosis.required' => 'Ingrese la dosis.',
            'genericos.required' => 'Seleccione un elemento de la Lista.',
        ];
         
        $this->validate($request, $rules, $mensajes);
    }

    private function validateInput($request) {
        
        $rules = [
            'nombre' => 'required|max:100',
            'cantidad' => 'required|max:50',
            'dosis' => 'required',
            ];
            
        $mensajes = [
            'nombre.required' => 'Ingrese el nombre.',
            'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.',
            'cantidad.required' => 'Ingrese la cantidad.',
            'cantidad.max' =>'La cantidad no puede ser mayor a :max caracteres.',
            'dosis.required' => 'Ingrese la dosis.',
        ];
        
      $this->validate($request, $rules, $mensajes);
    }

   //LLAMANDO A LA VISTA LISTADO_MEDICINA
   //DONDE SE MUESTRA TODO EL LISTADO DE MEDICINAS
   //SOLO PARA DOCTORES
   public function listar_medicina(){
    $opcion = '2';
    if($this->rol_new($opcion)){
        return redirect('/');
    }

    $medicinas = Medicina::where('estado',1)->orderBy('nombre')->paginate(50); 
    $genericos = Principio_Activo::where('estado',1)->get(); 

    return view('hc4/medicina/listado_medicina', ['medicinas' => $medicinas,'genericos'=> $genericos,'nombre' => null]);
   }

   //INGRESO DE LA MEDICINA A BUSCAR
   //DONDE SE MUESTRAN SOLO LA MEDICINA ENCONTRADA Y PODER EDITARLA
   //SOLO PARA DOCTORES
   public function search(Request $request){
    $opcion = '2';
    if($this->rol_new($opcion)){
        return redirect('/');
    }
       
    $nombre = $request['nombre'];
    
    $medicinas = Medicina::where('estado',1)->where('nombre','like','%'.$nombre.'%')->orderBy('nombre')->paginate(50);




    $genericos = Principio_Activo::where('estado',1)->get(); 
    
    return view('hc4/medicina/listado_medicina', ['medicinas' => $medicinas, 'genericos' => $genericos, 'nombre' => $request['nombre']]); 

    }

    //LLAMANDO A LA VISTA EDITAR_MEDICINA
    //DONDE SE MUESTRA LA VISTA DE LA MEDICINA A EDITAR
    //SOLO PARA DOCTORES
    public function edit_med($id){
        $opcion = '2';
        if($this->rol_new($opcion)){
          return redirect('/');
        }

        $medicina = Medicina::find($id);
        //dd($medicina->iess);
        $genericos = Principio_Activo::where('estado',1)->get();
        $medicina_principio = Medicina_Principio::where('id_medicina',$id)->get();

        return view('hc4/medicina/editar_medicina', ['medicina' => $medicina, 'genericos' => $genericos,'medicina_principio' => $medicina_principio]);  
    }

    //ACTUALIZANDO LA MEDICINA EDITADA
    //SOLO PARA DOCTORES
    public function update_medic(Request $request){
        $opcion = '2';
        if($this->rol_new($opcion)){
          return redirect('/');
        }
        
        $id= $request['idmedicina'];
        //dd($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $medicina = Medicina::findOrFail($id);


        if($request['dieta'] != 1){
           $this->validateInput3($request);
        }else{
           $this->validateInput4($request);
        }
        
        $medicina_principio = Medicina_Principio::where('id_medicina',$id)->get();
        
        $input = [

            'nombre' => $request['nombre'],
            'cantidad' => $request['cantidad'],
            'dosis' => $request['dosis'],
            'dieta' => $request['dieta'],
            //'concentracion' => $request['concentracion'], 
            //'presentacion' => $request['presentacion'],
            'estado' => $request['estado'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario, 
            'iess' => $request['iess_medicina'],  

        ];

        $medicina->update($input);
        
        $genericos_m = $request['genericos'];
        if($request['dieta'] != 1){
            foreach ($medicina_principio as $md) {
                $md->delete();    
            }

            $genericos_m = $request['genericos'];
            foreach ($genericos_m as $md) {
                if(is_numeric($md)){
                    $inputg = [

                        'id_medicina' => $id,
                        'id_principio_activo' => $md,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,

                    ];
                    Medicina_Principio::create($inputg);
                    
                }else{
                    $input_principio = [
                        'nombre' => substr(strtoupper($md), 0,-5),
                        'descripcion' => substr(strtoupper($md), 0,-5),
                        'estado' => '1',
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario, 
                    ];
                    $id_generico = Principio_Activo::insertGetId($input_principio);
                    $inputg = [

                        'id_medicina' => $id,
                        'id_principio_activo' => $id_generico,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,

                    ];

                    Medicina_Principio::create($inputg);

                }
            }
        }

       
        $medicinas = Medicina::where('estado',1)->where('nombre','like','%'.$request['nombre'].'%')->orderBy('nombre')->get(); 

        $genericos = Principio_Activo::where('estado',1)->get(); 
        
        return view('hc4/medicina/listado_medicina', ['medicinas' => $medicinas, 'genericos' => $genericos, 'nombre' => $request['nombre']]);

       //return view('hc4/medicina/inicio_medicina');        
    }


    private function validateInput3($request){

        $rules = [
            'nombre' => 'required|max:100',
            'cantidad' => 'required|max:50',
            'dosis' => 'required',
            'genericos' => 'required',
            'estado' => 'required',
        ];

        $mensajes = [
            'nombre.required' => 'Ingrese el nombre.',
            'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.',
            'cantidad.required' => 'Ingrese la cantidad.',
            'cantidad.max' =>'La cantidad no puede ser mayor a :max caracteres.',
            'dosis.required' => 'Ingrese la dosis.',
            'genericos.required' => 'Seleccione Seleccione los genéricos.',
            'estado.required' => 'Seleccione el estado.',
        ];
         
        $this->validate($request, $rules, $mensajes);
    }

    private function validateInput4($request){

        $rules = [
                'nombre' => 'required|max:100',
                'cantidad' => 'required|max:50',
                'dosis' => 'required',
                'estado' => 'required',
            ];

        $mensajes = [
            'nombre.required' => 'Ingrese el nombre.',
            'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.',
            'cantidad.required' => 'Ingrese la cantidad.',
            'cantidad.max' =>'La cantidad no puede ser mayor a :max caracteres.',
            'dosis.required' => 'Ingrese la dosis.',
            'estado.required' => 'Seleccione el estado.',
        ];
         
        $this->validate($request, $rules, $mensajes);
    }

   
}
 