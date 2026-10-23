<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Sis_medico\TipoProveedor;
use Sis_medico\Empresa;
use Sis_medico\Bodega;
use Sis_medico\Insumo_Plantilla;
use Sis_medico\Insumo_Plantilla_Item;
use Sis_medico\Marca;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvTrasladosBodegas;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvInventario;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\Producto;

class MarcaController extends Controller
{
    
   

    protected $redirectTo = '/dashboard';

         /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 7, 20)) == false){
          return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        if($this->rol()){
            return response()->view('errors.404');
        }
        $marcas = Marca::paginate(20);
        return view('insumos/marca/index', ['marcas' => $marcas]);
    }

    public function create(){
    	
        if($this->rol()){
            return response()->view('errors.404');
        }
        return view('insumos/marca/create');

    }

    public function store(Request $request)
    {
    
        $reglas = [
            'nombre' => 'required|max:255',
            'descripcion' => 'required|max:255',
        ];

        $mensajes = [
            'descripcion.required' => 'Agrega la descripción.',
            'descripcion.max' =>'La descripción no puede ser mayor a :max caracteres.',
            'nombre.required' => 'Agrega el nombre.',
            'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.',                        
        ];
 
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $this->validate($request, $reglas, $mensajes);
        date_default_timezone_set('America/Guayaquil');
        Marca::create([
        	'nombre' => strtoupper($request['nombre']),
            'descripcion' => strtoupper($request['descripcion']),
            
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario

        ]);


        return redirect()->intended('/marca');
    }

	public function edit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $marca = Marca::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($marca == null) {
            return redirect()->intended('/dashboard');
        }

        return view('insumos/marca/edit', ['marca' => $marca]);
    }
    public function update(Request $request, $id)
    {
        $marca = Marca::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $mensajes = [

            'nombre.required' => 'Agrega el nombre.',
            'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.',

            'descripcion.required' => 'Agrega la descripción.',
            'descripcion.max' =>'La descripción no puede ser mayor a :max caracteres.',

            ];        
        $constraints = [
            'nombre' => 'required|max:255',
            'descripcion' => 'required|max:255',          
            ];

        $input = [
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => strtoupper($request['descripcion']),
            'estado' => $request['estado'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario

        ];
                

       $this->validate($request, $constraints, $mensajes);
  

        $marca->update($input);
        
        return redirect()->intended('/marca');
    }
    public function search(Request $request) {

        if($this->rol()){
            return response()->view('errors.404');
        }
        $constraints = [
            'nombre' => $request['nombre'],
            
            ];


       $marcas = $this->doSearchingQuery($constraints);



       return view('insumos/marca/index', ['marcas' => $marcas, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        
        $query = Marca::query();
        $fields = array_keys($constraints);
        
        $index = 0;
        
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where( $fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        
        return $query->paginate(20);
    }

}
