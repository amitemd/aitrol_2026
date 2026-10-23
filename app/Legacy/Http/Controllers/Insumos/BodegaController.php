<?php

namespace Sis_medico\Http\Controllers\Insumos;


use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Bodega;
use Illuminate\Support\Facades\DB;
use Sis_medico\tipousuario;
use Sis_medico\Hospital;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Ct_Clientes;





use Response;
use Sis_medico\BodLocalGye;
use Sis_medico\Http\Requests\Request as RequestsRequest;

class BodegaController extends Controller
{
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
    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 7, 20)) == false){
          return true;
        }
    }
    public function index()
    {
        
        if($this->rol()){
            return response()->view('errors.404');
        }
        $bodegas = DB::table('bodega')->join('hospital','hospital.id','bodega.id_hospital')->select('bodega.*','hospital.nombre_hospital')
        ->where('id_empresa', Session::get('id_empresa'))->paginate(15);//3=DOCTORES


        

        return view('insumos/bodega/index', ['bodegas' => $bodegas]);
    }

    public function create()
    {
       if($this->rol()){
            return response()->view('errors.404');
        }
        $hospital = hospital::all();
                
        $clientes = Ct_Clientes::where('estado',1)->get();

        return view('insumos/bodega/create', ['hospital' => $hospital, 'clientes' => $clientes]);
    }
     public function store(Request $request)
    {
        //return $request->all();
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id_empresa  = $request->session()->get('id_empresa');

        $this->validateInput_Bodega($request);
        date_default_timezone_set('America/Guayaquil');
         Bodega::create([
            'id_cliente' => $request['id_cliente'],
            'nombre' => strtoupper($request['nombre']),
            'id_hospital' => $request['id_hospital'],
            'id_empresa' => $id_empresa,
            'encargado' => $idusuario,
            'color' => $request['color'], 
            'tipo'  => $request['tipo'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
            
        ]);

        return redirect()->intended('/bodega');
    }
    public function validateInput_Bodega(Request $request){
        $reglas=[
                    'nombre' => 'required|unique:bodega',
                    'id_hospital' => 'required',
                ];

        $mensajes=[
                    'nombre.required' => 'Ingrese un nombre',
                    'nombre.unique' => 'Bodega ya existe'
                    ];


       $this->validate($request, $reglas, $mensajes);             

    }
    public function edit($id)
    {
        //return $request->all();
        if($this->rol()){
            return response()->view('errors.404');
        }

        $bodega = Bodega::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($bodega == null) {
            return redirect()->intended('/bodega');
        }

        $hospital = hospital::all();
        
        $clientes = Ct_Clientes::where('estado',1)->get();

        //return view('paciente/edit', ['paciente' => $paciente])->with('paises',$paises)->with('seguros',$seguros)->with('rolusuario', $rolusuario);
        return view('insumos/bodega/edit', ['bodega' => $bodega, 'hospital' => $hospital, 'clientes' => $clientes]);
    }

    public function update(Request $request, $id)
    {
        //return $request->all();
        $bodega = Bodega::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $this->validateInput_Bodega2($request,$id);
        
        $input = [
                'id_cliente' => $request['id_cliente'],
                'nombre' => strtoupper($request['nombre']),
                'id_hospital' => $request['id_hospital'],
                'estado' => $request['estado'],
                'color' => $request['color'], 
                'tipo'  => $request['tipo'],
                'encargado' => $idusuario,                 
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario
                ];


        $bodega->update($input); 
        
             
        return redirect()->intended('/bodega');
    }

    public function validateInput_Bodega2(Request $request, $id){
        $reglas=[
                    'nombre' => 'required|unique:bodega,nombre,'.$id,
                    'id_hospital' => 'required',
                ];

        $mensajes=[
                    'nombre.required' => 'Ingrese un nombre',
                    'nombre.unique' => 'Bodega ya existe'
                    ];



       $this->validate($request, $reglas, $mensajes);             

    }

    public function search(Request $request) {

        if($this->rol()){
            return response()->view('errors.404');
        }
        $constraints = [
            'nombre' => $request['nombre'],

            ];


       $bodegas = $this->doSearchingQuery($constraints);

       return view('insumos/bodega/index', ['bodegas' => $bodegas, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = Bodega::query();
        $fields = array_keys($constraints);
        
        $index = 0;
        
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where( $fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }

        $query=$query->join('hospital','hospital.id','bodega.id_hospital')->select('bodega.*','hospital.nombre_hospital');
        
        return $query->paginate(40);
    }

    public function ligueGye(Request $request) {
        
        $bodegas = Bodega::where('id_empresa', Session::get('id_empresa'))->get();
        $apiBodegas = BodegaController::apiGetBodegasGye();
        $bod_local_gye = BodLocalGye::where('id_empresa', Session::get('id_empresa'))->get();
        $arrBodegas = [];
        foreach($bod_local_gye as $value){
            $arrBodegas[$value->id_bodega_local] = $value->id_bodega_gye;
        }
        return view('insumos/bodega/ligue/ligueGye', ['bodegas' => $bodegas, 'apiBodegas' => $apiBodegas, 'arrBodegas' => $arrBodegas]);
    }

    public static function apiGetBodegasGye(){
        $appId = "wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=";
        $elemento = json_encode([]);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                'method'  => 'POST',
                'content' => $elemento,
                'timeout' => 600,
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        // $url_emp = Grupo_Empresas::find(7);

        $url     = 'https://humanlabs.aitrol.com/api/bodegas/getBodegas';
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $respuesta = json_decode($response, true);
        return $respuesta;
    }

    public function ligueGyeStore(Request $request) {
        DB::beginTransaction();
        try {

            $id_usuario = Auth::user()->id;
            $id_empresa = Session::get('id_empresa');
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $consulta = BodLocalGye::where('id_bodega_local', $request->id_local)->where('id_empresa', $id_empresa)->first();
            if(!is_null($consulta)){
                $consulta->id_bodega_gye = $request->id_bodega_gye;
                $consulta->id_usuariomod = $id_usuario;
                $consulta->ip_modificacion = $ip_cliente;
                $consulta->save();
                DB::commit();
                return ['status' => 'success', 'msj' => "Editado con exito"];
            }

            $data = [
                "id_bodega_local"   => $request->id_local,
                "id_bodega_gye"     => $request->id_bodega_gye,
                "id_empresa"        => $id_empresa,
                "id_usuariocrea"    => $id_usuario,
                "ip_creacion"       => $ip_cliente,
                "id_usuariomod"     => $id_usuario,
                "ip_modificacion"   => $ip_cliente
            ];
            BodLocalGye::create($data);
            DB::commit();
            return ['status' => 'success', 'msj' => "Guardado con exito"];
        }catch(\Exception $e){
            DB::rollBack();
            return ['status' => 'error', 'msj' => "No se guardo", 'exp'=>$e->getMessage()];
        }

        

    }

}
