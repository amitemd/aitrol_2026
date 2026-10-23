<?php

namespace Sis_medico\Http\Controllers\contable;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas; 
use Sis_medico\GrupoEstructuraFlujoEfectivo; 
use Sis_medico\Empresa;  
use Sis_medico\FlujoEfectivo;  
use Sis_medico\EstructuraFlujoEfectivo;  
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

class EstructuraFlujoEfectivoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22,26)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        $id_empresa     = $request->session()->get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->first();
        $data           = array();  
        $estructura     = EstructuraFlujoEfectivo::where('estado', '!=', '-1')
        // ->join('grupo_flujo_efectivo as g', 'estructura_flujo_efectivo.id_grupo', 'g.id_grupo') 
        ->orderBy('id_plan')->paginate(15);

        return view('contable/estructura_flujo_efectivo/index', ['empresa' => $empresa, 'estructura' => $estructura]);
    } 
 
    public function show(Request $request)
    {
        return view('contable/estructura_flujo_efectivo/index', ['empresa' => $empresa, 'bodegas' => $data]);
    }

    public function create()
    {
       if($this->rol()){
            return response()->view('errors.404');
        } 
        $scuentas   = plan_cuentas::all();
        $grupos     = GrupoEstructuraFlujoEfectivo::all();

        return view('contable/estructura_flujo_efectivo/create', ['scuentas' => $scuentas, 'grupos' => $grupos]);
    }

    
    public function validateInputCuentas(Request $request){
       
        $reglas=[
                    'id_plan'   => 'required|unique:estructura_flujo_efectivo,id_plan',
                    'signo'     => 'required',
                    'id_grupo'  => 'required',
                    'estado'    => 'required',
                ];
        $mensajes=[
                    'id_plan.required'  => 'Ingrese una cuenta',
                    'id_plan.unique'    => 'La cuenta ya se encuentra registrada',
                    'signo.required'    => 'Ingrese el signo de la cuenta',
                    'id_grupo.required' => 'Ingrese el grupo al que pertenece de la cuenta',
                    'estado.required'   => 'Ingrese una cuenta'
                    ];
        
       $res = $this->validate($request, $reglas, $mensajes);    
       //dd($res);         
    }

    public function store(Request $request)
    { 
        $cuenta = Plan_Cuentas::find($request['id_plan']);
        if(isset($cuenta->estado)){
            $request['estado'] = $cuenta->estado;
        } 
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;  
        $this->validateInputCuentas($request);
        //dd('Store');
        date_default_timezone_set('America/Guayaquil');
        $id_empresa     = $request->session()->get('id_empresa');
        
        EstructuraFlujoEfectivo::create([
            'id_plan'           => $request['id_plan'],
            'signo'             => $request['signo'],
            'id_grupo'          => $request['id_grupo'],
            'estado'            => $request['estado'],
            'id_empresa'        => $id_empresa,
            'id_usuariomod'     => $idusuario,
            'id_usuariocrea'    => $idusuario, 
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente 
        ]);
        //Flash::success('Cuenta agregada.');
        return redirect()->route('estructuraflujoefectivo.index'); 
    }
  
    public function edit($id)
    { 
        if($this->rol()){
            return response()->view('errors.404');
        }

        $estructura = EstructuraFlujoEfectivo::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($estructura == null || count($estructura) == 0) {
            return redirect()->route('estructuraflujoefectivo.index');
        }
        $grupos     = GrupoEstructuraFlujoEfectivo::all();
        $estructura = EstructuraFlujoEfectivo::find($id);
        $scuentas   = plan_cuentas::all();

        //dd($estructura);
         
        return view('contable/estructura_flujo_efectivo/edit', ['estructura' => $estructura, 'scuentas' => $scuentas, 'grupos' => $grupos]);
    }

    public function update($id, Request $request)
    {
        $estructura = EstructuraFlujoEfectivo::find($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil'); 
        
        $input = [
                'id_plan'           => $request['id_plan'],
                'signo'             => $request['signo'],
                'id_grupo'          => $request['id_grupo'],
                'estado'            => $request['estado'],
                'id_usuariomod'     => $idusuario,  
                'ip_modificacion'   => $ip_cliente 
                ]; 
                //dd($input);
        $estructura->update($input); 
        // EstructuraFlujoEfectivo::where('id', $id)
        //     ->update($input);
        //return redirect()->intended('/estructuraflujoefectivo.index');
        return redirect()->route('estructuraflujoefectivo.index'); 
    }

    public function validateInputUpdateCuentas(Request $request, $id){
        $reglas=[
                    'id_plan'   => 'required|unique:estructura_flujo_efectivo,id_plan',
                    'signo'     => 'required',
                    'estado'    => 'required',
                ];
        $mensajes=[
                    'id_plan.required'  => 'Ingrese una cuenta',
                    'id_plan.unique'    => 'La cuenta ya se encuentra registrada',
                    'signo.required'    => 'Ingrese el signo de la cuenta',
                    'estado.required'   => 'Ingrese una cuenta'
                    ];
       $this->validate($request, $reglas, $mensajes);             
    }
 
    public function buscar(Request $request)
    {
        //dd($request);
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $constraints = [
            'id_plan'    => $request['buscar_cuenta'], 
        ]; 
        $estructura = $this->doSearchingQuery($constraints);

        $id_empresa     = $request->session()->get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->first();
        $data           = array();    

        return view('contable/estructura_flujo_efectivo/index', ['empresa' => $empresa,'request' => $request, 'estructura' => $estructura, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints)
    {

        $query  = EstructuraFlujoEfectivo::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->orderby('id', 'desc')->paginate(5);
    }
 
 
    public function destroy(Request $request, $id)
    {
        //return $request->all();
        $estructura = EstructuraFlujoEfectivo::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        //$this->validateInput_Bodega2($request,$id);
        
        $input = [ 
                'estado' => 0,                
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario
                ];


        $estructura->update($input); 
        
             
        return redirect()->intended('contable/estructura/flujo/efectivo');
    }
}
