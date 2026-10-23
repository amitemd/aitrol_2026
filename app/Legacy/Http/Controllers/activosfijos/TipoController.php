<?php

namespace Sis_medico\Http\Controllers\activosfijos;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input; 
use Sis_medico\Plan_Cuentas;
use Sis_medico\AfGrupo;
use Sis_medico\AfTipo;

class TipoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20)) == false) {
            return true;
        }
    }

    public function index()
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }
        $tipos = AfTipo::where('estado','1')->paginate('15');
        $registros = array();
        return view('activosfijos/mantenimientos/tipo/index', ['tipos' => $tipos, 'registros'=>$registros]);
    }

    public function search(Request $request) {
        //return $request->all();
        if($this->rol()){
            return response()->view('errors.404');
        }
        $constraints = [    'nombre'    => $request['nombre']     ];


       $tipos = $this->doSearchingQuery($constraints);

       return view('activosfijos/mantenimientos/tipo/index', ['tipos' => $tipos, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = AfTipo::query()->select('af_tipo.*');
        $fields = array_keys($constraints);
        
        $index = 0;
        
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where( 'af_tipo.'.$fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        
        return $query->paginate(5);
    }

    public function edit($id, Request $request)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $tipo       =   AfTipo::where('id', '=', $id)->first();
        $plan       =   Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->where('pe.estado', '2')->select('pe.id_plan as id', DB::raw('CONCAT(pe.plan," | ",pe.nombre) as nombre'))->get();
        $grupos     = AfGrupo::where('estado','1')->get();
        //dd($proveedor);
        // Redirect to user list if updating user wasn't existed
        if ($tipo == null) {
            return redirect()->intended('/dashboard');
        }
        return view('activosfijos/mantenimientos/tipo/edit', ['tipo' => $tipo, 'plan' => $plan, 'grupos' => $grupos]);
    }

    public function create(){
    	if($this->rol()){
            return response()->view('errors.404');
        }
        $plan       = Plan_Cuentas::all();  
        $codigo     = ""; 
        $codigo     = (AfTipo::max('codigo')+1);
        $codigo     = str_pad($codigo, 2, "0", STR_PAD_LEFT);
        $grupos     = AfGrupo::where('estado','1')->get();
        return view('activosfijos/mantenimientos/tipo/create', ['plan' => $plan, 'codigo' => $codigo, 'grupos' => $grupos]);

    }

    public function find_cta_activofijo(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $buscador = $request['search'];
        $cuentas  = [];

        if ($buscador != null) {
            $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->where('pe.estado', '2');

            $cuentas = $cuentas->where(function ($jq1) use ($buscador) {
                $jq1->orwhere('pe.nombre',"LIKE","%{$buscador}%")
                    ->orwhere('pe.plan', 'LIKE', "%{$buscador}%");
            });

            $cuentas = $cuentas->select('pe.id_plan as id', DB::raw('CONCAT(pe.plan," | ",pe.nombre) as text'))->get();
        }
        

        return response()->json($cuentas);
    }

    private function validateInput($request) {
	    $this->validate($request,[]);
	}

    public function store(Request $request)
    {
        $reglas = [
            'codigo'                =>  'required|max:2|min:2|unique:codigo',
            'nombre'                =>  'required|max:255',
            'cuentamayor'           =>  'required|max:255',
            'cuantadepreciacion'    =>  'required|max:255',
            'cuentagastos'          =>  'required|max:255', 
            'tasa'                  =>  'required|numeric|max:9999999999',
            'vidautil'              =>  'required|numeric|max:99',

        ];

        $mensajes = [
            'codigo.unique'                 =>  'El codigo ya se encuentra registrado.',
            'codigo.required'               =>  'Agregar el codigo.',
            'codigo.max'                    =>  'El RUC no puede ser mayor a :max caracteres.',
            'codigo.min'                    =>  'El RUC no puede ser menor a :min caracteres.',
            'nombre.required'               =>  'Agregar el nombre del tipo.',
            'nombre.max'                    =>  'El tipo no puede ser mayor a :max caracteres.',
            'cuentamayor.required'          =>  'Agregar la cuenta de mayor.',
            'cuentamayor.max'               =>  'La cuenta de mayor no puede ser mayor a :max caracteres.',
            'cuantadepreciacion.required'   =>  'Agrega la cuenta de depreciación.',
            'cuantadepreciacion.max'        =>  'La cuenta de depreciación no puede ser mayor a :max caracteres.',
            'cuentagastos.required'         =>  'Agrega la cuenta de gastos.',
            'cuentagastos.max'              =>  'El la cuenta de gastos no puede ser mayor a :max caracteres.',
            'tasa.numeric'                  =>  'La tasa debe ser un valor numerico.',
            'tasa.required'                 =>  'Agregar el valor de la tasa de depresiación.',
            'vidautil.numeric'              =>  'La vida útil debe ser un valor numerico.',
            'vidautil.required'             =>  'Agregar el valor de la vida útil.',
            'tipo_tasa.required'            =>  'Seleccione el tipo de tasa',
                                
        ];
 
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $this->validateInput($request, $reglas, $mensajes);
        date_default_timezone_set('America/Guayaquil');

        $tipo_id    = AfTipo::where('id', $request['id'])->first();
        
        if(is_null($tipo_id)){

            AfTipo::create([
                'codigo'                =>  $request['codigo'],
                'nombre'                =>  strtoupper($request['nombre']),
                'cuentamayor'           =>  $request['cuentamayor'], 
                'cuantadepreciacion'    =>  $request['cuantadepreciacion'],
                'cuentagastos'          =>  $request['cuentagastos'],
                'tasa'                  =>  $request['tasa'],
                'vidautil'              =>  $request['vidautil'],
                'estado'                =>  1,
                'id_usuariocrea'        =>  $idusuario,
                'id_usuariomod'         =>  $idusuario,
                'ip_creacion'           =>  $ip_cliente,
                'ip_modificacion'       =>  $ip_cliente,
                'grupo_id'              =>  $request['grupo'],
                'tipo_tasa'             =>  $request['tipo_tasa'],
            ]);
            
        }
        return redirect()->intended('/afTipo');
    }


    public function update(Request $request, $id)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $reglas = [
            'codigo'                =>  'required|max:2|min:2',
            'nombre'                =>  'required|max:255',
            'cuentamayor'           =>  'required|max:255',
            'cuantadepreciacion'    =>  'required|max:255',
            'cuentagastos'          =>  'required|max:255', 
            'tasa'                  =>  'required|numeric|max:9999999999',
            'vidautil'              =>  'required|numeric|max:99',
        ];

        $mensajes = [
            // 'codigo.unique'                 =>  'El codigo ya se encuentra registrado.',
            'codigo.required'               =>  'Agregar el codigo.',
            'codigo.max'                    =>  'El codigo no puede ser mayor a :max caracteres.',
            'codigo.min'                    =>  'El codigo no puede ser menor a :min caracteres.',
            'nombre.required'               =>  'Agregar el nombre del tipo.',
            'nombre.max'                    =>  'El tipo no puede ser mayor a :max caracteres.',
            'cuentamayor.required'          =>  'Agregar la cuenta de mayor.',
            'cuentamayor.max'               =>  'La cuenta de mayor no puede ser mayor a :max caracteres.',
            'cuantadepreciacion.required'   =>  'Agrega la cuenta de depreciación.',
            'cuantadepreciacion.max'        =>  'La cuenta de depreciación no puede ser mayor a :max caracteres.',
            'cuentagastos.required'         =>  'Agrega la cuenta de gastos.',
            'cuentagastos.max'              =>  'El la cuenta de gastos no puede ser mayor a :max caracteres.',
            'tasa.numeric'                  =>  'La tasa debe ser un valor numerico.',
            'tasa.required'                 =>  'Agregar el valor de la tasa de depresiación.',
            'vidautil.numeric'              =>  'La vida útil debe ser un valor numerico.',
            'vidautil.required'             =>  'Agregar el valor de la vida útil.',
            'tipo_tasa.required'            =>  'Seleccione el tipo de tasa.',
                                
        ];

        $tipo   =   AfTipo::where('id', $request['id'])->first();
     
        if(!is_null($tipo)){
                $input = [
            'nombre'                =>  strtoupper($request['nombre']),
            'cuentamayor'           =>  $request['cuentamayor'], 
            'cuantadepreciacion'    =>  $request['cuantadepreciacion'],
            'cuentagastos'          =>  $request['cuentagastos'],
            'tasa'                  =>  $request['tasa'],
            'vidautil'              =>  $request['vidautil'],
            'estado'                =>  1,
            'id_usuariomod'         =>  $idusuario,
            'ip_modificacion'       =>  $ip_cliente,
            'grupo_id'              =>  $request['grupo'],
            'tipo_tasa'             =>  $request['tipo_tasa'],
            ];
        }
        else{
            $codigo     = ""; 
            $codigo     = (AfTipo::max('codigo')+1);
            $codigo     = str_pad($codigo, 2, "0", STR_PAD_LEFT);
            $input = [
                'codigo'                =>  $codigo,
                'nombre'                =>  strtoupper($request['nombre']),
                'cuentamayor'           =>  $request['cuentamayor'], 
                'cuantadepreciacion'    =>  $request['cuantadepreciacion'],
                'cuentagastos'          =>  $request['cuentagastos'],
                'tasa'                  =>  $request['tasa'],
                'vidautil'              =>  $request['vidautil'],
                'estado'                =>  1,
                'id_usuariocrea'        =>  $idusuario,
                'id_usuariomod'         =>  $idusuario,
                'ip_creacion'           =>  $ip_cliente,
                'ip_modificacion'       =>  $ip_cliente,
                'grupo_id'              =>  $request['grupo'],
                'tipo_tasa'             =>  $request['tipo_tasa'],
            ];
        }

           
       $this->validate($request, $reglas, $mensajes);
  
       AfTipo::where('id', $id)->update($input);
        
       return redirect()->intended('/afTipo');

    }

    public function destroy(Request $request, $id)
    {
        // dd($request);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $input = [
            'estado'                =>  0,
            'id_usuariomod'         =>  $idusuario,
            'ip_modificacion'       =>  $ip_cliente,
        ];

        AfTipo::where('id', $id)->update($input);
        
        return redirect()->intended('/afTipo');
    }
    
}