<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Ct_rubros;
use Sis_medico\Empresa;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Http\Controllers\Controller;

class RubrosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22)) == false) {
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
        $rubros = Ct_rubros::paginate(20);

        return view('contable/rubros/index', ['rubros' => $rubros,'empresa'=>$empresa]);
    }

    //Visualiza el ingreso de los datos del Rubro
    public function create(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $cuentas = plan_cuentas::where('estado', '2')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa= Empresa::where('id',$id_empresa)->first();
        return view('contable/rubros/create', ['cuentas' => $cuentas,'empresa'=>$empresa]);
    }

    //Guarda informacion del Rubro 
    public function store(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        /*$reglas = [
            'codigo'                   => 'required',
            'nombre'                   => 'required|max:255',
            'cuenta_debe'              => 'required',
            'cuenta_haber'             => 'required',
            'tipo'                     => 'required',
            'nota'                     => 'required',
            'estado'                   => 'required',
        ];

        $mensajes = [
            
            'codigo.required'          => 'Ingrese un Codigo',
            'nombre.required'          => 'Ingrese un Nombre',
            'cuenta_debe.required'     => 'Seleccione la Cuenta DEBE.',
            'cuenta_haber.required'    => 'Seleccione la Cuenta HABER.',
            'tipo.required'            => 'Seleccione el Tipo',
            'nota.required'            => 'Ingrese la nota',
            'estado.required'          => 'Seleccione el Estado',
            
        ];*/
        

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        //$this->validate($request, $reglas, $mensajes);
        date_default_timezone_set('America/Guayaquil');

        Ct_rubros::create([
            
            'codigo'                  => $request['codigo'],
            'id_empresa'              => $id_empresa,
            'nombre'                  => strtoupper($request['nombre']),
            'debe'                    => $request['cuenta_debe'],
            'haber'                   => $request['cuenta_haber'],
            'tipo'                    => $request['tipo'],
            'estado'                  => $request['estado'],
            'id_usuariocrea'          => $idusuario,
            'id_usuariomod'           => $idusuario,
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
           
        ]);

        return redirect()->route('rubrosa.index');
    }


    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $constraints = [
            'codigo' => $request['codigo'],
            'nombre' => $request['nombre'],
        ];
        $id_empresa = $request->session()->get('id_empresa');
        $rubros = $this->doSearchingQuery($constraints,$id_empresa);
     
        return view('contable/rubros/index', ['rubros' => $rubros, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints,$id_empresa)
    {

        $query  = Ct_rubros::query();

        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

     
        return $query->where('id_empresa',$id_empresa)->paginate(20);

    }

    public function editar($codigo)
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        $cuentas = plan_cuentas::where('estado', '2')->get();
        $rubro = Ct_rubros::where('codigo',$codigo)->first();
      
        // Redirect to user list if updating user wasn't existed
        if ($rubro == null || count($rubro) == 0) {
            return redirect()->intended('/contable/rubros');
        }

        return view('contable/rubros/edit', ['rubro' => $rubro,'cuentas' => $cuentas]);
    }

    
    public function update(Request $request, $id)
    {
        
        
        $rubros  = Ct_rubros::where('codigo', $id)->first();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
    
        /*$reglas = [
            'codigo'                   => 'required',
            'nombre'                   => 'required|max:255',
            'cuenta_debe'              => 'required',
            'cuenta_haber'             => 'required',
            'tipo'                     => 'required',
            'nota'                     => 'required',
            'estado'                   => 'required',
        ];

        $mensajes = [
            
            'codigo.required'          => 'Ingrese un Codigo',
            'nombre.required'          => 'Ingrese un Nombre',
            'cuenta_debe.required'     => 'Seleccione la Cuenta DEBE.',
            'cuenta_haber.required'    => 'Seleccione la Cuenta HABER.',
            'tipo.required'            => 'Seleccione el Tipo',
            'nota.required'            => 'Ingrese la nota',
            'estado.required'          => 'Seleccione el Estado',
            
        ];*/

        // $this->validate($request, $constraints, $mensajes);*/
        
        $rubr = Ct_rubros::where('codigo', $request['codigo'])->first();
        
        if (!is_null($rubr)) {

            $input = [
      
            'nombre'                  => strtoupper($request['nombre']),
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

            'codigo'                  => $request['codigo'],
            'nombre'                  => strtoupper($request['nombre']),
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

        Ct_rubros::where('codigo', $id)->update($input);

        return redirect()->route('rubrosa.index');
    }


}
