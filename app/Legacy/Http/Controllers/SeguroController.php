<?php
namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Response;
use Storage;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Seguro;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Subseguro;


class SeguroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 7)) == false){
          return true;
        }
    }
    public function index()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }
        $seguro = DB::table('seguros')
        ->paginate(10);

        return view('seguro/index', ['seguro' => $seguro]);
    }

    public function listasubseguro($id)
        {
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }
 
        $seguro = DB::table('seguros')->where('id', '=', $id)
                                    ->get();
        $subseguro =  DB::table('subseguro')->where('id_seguro', '=', $id)->get();
                          
        return view('seguro/subseguro-index', ['seguro' => $seguro, 'subseguro' => $subseguro]);
    }

    public function subsegurocreate($id)
        {
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }
 
        $seguro = DB::table('seguros')->where('id', '=', $id)
                                    ->get();
                          
        return view('seguro/subseguro-create', ['seguro' => $seguro]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }
        return view('seguro/create' );
    }

     public function search(Request $request) {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $constraints = [
            'nombre' => $request['nombredelseguro']];
        $seguro = $this->doSearchingQuery($constraints);
        return view('seguro/index', ['seguro' => $seguro, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = DB::table('seguros');
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
         seguro::create([
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => $request['descripcion'],
            'tipo' => $request['tipo'],
            'color' => $request['color'],          
            'url_validacion' => $request['url_validacion'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
            
        ]);

        return redirect()->intended('/form_enviar_seguro');
    }

    public function subseguroguardar(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $this->validateInput5($request);
        date_default_timezone_set('America/Guayaquil');
         Subseguro::create([
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => $request['descripcion'],
            'id_seguro' => $request['id_seguro'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
            
        ]);

        return redirect()->intended('/seguro/subseguro/'.$request['id_seguro']);
    }

    private function validateInput5($request) {
        $this->validate($request, [
        'nombre' => 'required|max:60|unique:seguros',
        'descripcion' => 'required'
    ]);
    }

    private function validateInput($request) {
        $this->validate($request, [
        'nombre' => 'required|max:60|unique:seguros',
        'descripcion' => 'required',
        'tipo' => 'required',
        'color' => 'required|unique:seguros'
    ]);
    }

    private function validateInput2($request) {
        $this->validate($request, [
        'nombre' => 'required|max:60',
        'descripcion' => 'required',
        'tipo' => 'required',
        'color' => 'required'
    ]);
    }

    private function validateInput3($request) {
        $this->validate($request, [
        'nombre' => 'required|max:60|unique:seguros',
        'descripcion' => 'required',
        'tipo' => 'required',
        'color' => 'required'
    ]);
    }

    private function validateInput4($request) {
        $this->validate($request, [
        'nombre' => 'required|max:60',
        'descripcion' => 'required',
        'tipo' => 'required',
        'color' => 'required|unique:seguros'
    ]);
    }


    public function edit($id)
    {
        $seguro = Seguro::find($id);
        // Redirect to state list if updating state wasn't existed
        if($this->rol()){
            return response()->view('errors.404');
        }
        return view('seguro/edit', ['seguro' => $seguro]);
    }

    public function subseguroedit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $subseguro = Subseguro::find($id);
        // Redirect to state list if updating state wasn't existed
        if ($subseguro == null || count($subseguro) == 0) {
            return redirect()->intended('/form_enviar_seguro');
        }
        return view('seguro/subseguro-edit', ['subseguro' => $subseguro]);
    }

    public function update(Request $request, $id)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $seguro = Seguro::findOrFail($id);
        if(($request['nombre'] == $request['nombre1']) && ($request['color'] == $request['color1'])){
            $this->validateInput2($request);    
        }

        if(($request['nombre'] != $request['nombre1']) ){
            $this->validateInput3($request);    
        }
        
        if(($request['color'] != $request['color1'])){
            $this->validateInput4($request);    
        }
        
        $input = [
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => $request['descripcion'],
            'tipo' => $request['tipo'],
            'color' => $request['color'],
            'ip_modificacion' => $ip_cliente,
            'url_validacion' => $request['url_validacion'],
            'created_at' => $request['horacrea'],
            'id_usuariomod' => $idusuario,
            'codigo_validacion' => $request['codigo_validacion'],
        ];

        Seguro::where('id', $id)->update($input);

        return redirect()->intended('/form_enviar_seguro');
    }

    private function validateInput6($request) {
        $this->validate($request, [
        'nombre' => 'required|max:60',
        'descripcion' => 'required'
    ]);
    }
    public function subseguroupdate(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        if($request['nombre'] == $request['nombre1']){
            $this->validateInput6($request);    
        }
        else
        {
            $this->validateInput5($request);
        }
        $input = [
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => $request['descripcion'],
            'ip_modificacion' => $ip_cliente,
            'created_at' => $request['horacrea'],
            'id_usuariomod' => $idusuario
        ];

        Subseguro::where('id', $request['id'])->update($input);

        return redirect()->intended('/seguro/subseguro/'.$request['id_seguro']);
    }


}
?>