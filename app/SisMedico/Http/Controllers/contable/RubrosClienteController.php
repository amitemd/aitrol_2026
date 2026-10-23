<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Empresa;
use Sis_medico\Ct_Rubros_Cliente;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Http\Controllers\Controller;

class RubrosClienteController extends Controller
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

    //Visualiza el listado de Rubros
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        $id_empresa = $request->session()->get('id_empresa');
        $empresa= Empresa::where('id',$id_empresa)->first();
        $rubros = Ct_Rubros_Cliente::where('estado','=', 1)->where('id_empresa', $id_empresa)->orderby('codigo', 'desc')->paginate(5);
        
        return view('contable/rubros_clientes/index', ['rubros' => $rubros,'empresa'=>$empresa]);
    
    }

    //Visualiza el ingreso de los datos del Rubro
    public function create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $cuentas = plan_cuentas::where('estado', '2')->get();

        return view('contable/rubros_clientes/create', ['cuentas' => $cuentas,'empresa' => $empresa]);
    }

    //Guarda informacion del Rubro 
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_empresa = $request->session()->get('id_empresa');

        date_default_timezone_set('America/Guayaquil');

        Ct_Rubros_Cliente::create([
            
            'codigo'                  => $request['codigo'],
            'nombre'                  => strtoupper($request['nombre']),
            'id_empresa'              => $id_empresa,
            'debe'                    => $request['cuenta_debe'],
            'haber'                   => $request['cuenta_haber'],
            'tipo'                    => $request['tipo'],
            'estado'                  => $request['estado'],
            'id_usuariocrea'          => $idusuario,
            'id_usuariomod'           => $idusuario,
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
           
        ]);
        return redirect()->route('rubros_cliente.index');
    }


    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $constraints = [
            'codigo'     => $request['codigo'],
            'nombre'     => $request['nombre'],
            'estado'     => 1,
            'id_empresa' => $id_empresa,
        ];

        $rubros = $this->doSearchingQuery($constraints);
     
        return view('contable/rubros_clientes/index', ['rubros' => $rubros, 'searchingVals' => $constraints,'empresa' => $empresa]);
    }

    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Rubros_Cliente::query();

        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

     
        return $query->paginate(20);

    }

    public function editar($codigo,Request $request)
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $codigos = $codigo;
        $codigos = str_replace("_","/", $codigo);
        
        $cuentas = plan_cuentas::where('estado', '2')->get();
        $rubro = Ct_Rubros_Cliente::where('codigo',$codigos)->where('estado','1')->where('id_empresa', $id_empresa)->first();
      
        if ($rubro == null) {
            return redirect()->intended('/contable/rubros');
        }

        return view('contable/rubros_clientes/edit', ['rubro' => $rubro,'cuentas' => $cuentas,'empresa' => $empresa]);
    }

    
    public function update(Request $request, $id)
    {
        $codigos = $id;
        $codigos = str_replace("_","/", $codigos);

        $rubros  = Ct_Rubros_Cliente::where('codigo', $codigos)->first();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $id_empresa = $request->session()->get('id_empresa');

      

        
        $rubr = Ct_Rubros_Cliente::where('codigo', $request['codigo'])->where('estado','1')->where('id_empresa', $id_empresa)->first();
        //dd($rubr);
        
        if (!is_null($rubr)) {

            $input = [
      
                'nombre'                  => strtoupper($request['nombre']),
                'id_empresa'              => $id_empresa,
                'debe'                    => $request['cuenta_debe'],
                'haber'                   => $request['cuenta_haber'],
                'tipo'                    => $request['tipo'],
                'estado'                  => $request['estado'],
                'id_usuariocrea'          => $idusuario,
                'id_usuariomod'           => $idusuario,
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
            
            ];

        } else {

            $input = [

            'codigo'                  => strtoupper($request['codigo']),
            'nombre'                  => strtoupper($request['nombre']),
            'id_empresa'              => $id_empresa,
            'debe'                    => $request['cuenta_debe'],
            'haber'                   => $request['cuenta_haber'],
            'tipo'                    => $request['tipo'],
            'estado'                  => $request['estado'],
            'id_usuariocrea'          => $idusuario,
            'id_usuariomod'           => $idusuario,
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
            
            ];
        }
        //dd($codigos);
        Ct_Rubros_Cliente::where('codigo', $codigos)->where('estado','1')->where('id_empresa', $id_empresa)->update($input);

        return redirect()->route('rubros_cliente.index');
    }
    
    public function nombre2(Request $request){
        $id_empresa = $request->session()->get('id_empresa');
        $pedido    = $request['codigo'];
        $data      = null;
        $rubros = DB::table('ct_rubros_cliente')->where('estado','1')->where('id_empresa', $id_empresa)->where('nombre',$pedido)->get();

        if ($rubros != '[]') {
            
            $data = [$rubros[0]->codigo,$rubros[0]->nombre];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    
    public function nombre(Request $request){
        $id_empresa = $request->session()->get('id_empresa');
        $codigo = $request['term'];
        $data      = array();
        $rubros = DB::table('ct_rubros_cliente')->where('estado','1')->where('id_empresa', $id_empresa)->where('nombre', 'like', '%' . $codigo . '%')->get();
        foreach ($rubros as $prov) {
            $data[] = array('value' => $prov->nombre,'codigo'=>$prov->codigo);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    public function codigo(Request $request){
        $id_empresa = $request->session()->get('id_empresa');
        $codigo = $request['term'];
        $data      = array();
        $rubros = DB::table('ct_rubros_cliente')->where('estado','1')->where('id_empresa', $id_empresa)->where('codigo', 'like', '%' . $codigo . '%')->get();
        foreach ($rubros as $prov) {
            $data[] = array('value' => $prov->codigo,'nombre'=>$prov->nombre);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    
    public function codigo2(Request $request){
        $id_empresa = $request->session()->get('id_empresa');
        $pedido    = $request['codigo'];
        $data      = null;
        $rubros = DB::table('ct_rubros_cliente')->where('estado','1')->where('id_empresa', $id_empresa)->where('codigo',$pedido)->get();

        if ($rubros != '[]') {
            
            $data = [$rubros[0]->nombre,$rubros[0]->codigo];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    


}
