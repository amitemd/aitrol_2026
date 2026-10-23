<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Ct_rubros;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Empresa;

class RubrosAcreedorController extends Controller
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
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $rubros = Ct_rubros::where('estado', '=', 1)->where('id_empresa', $id_empresa)->orderby('codigo', 'desc')->paginate(5);

        return view('contable/rubros_acreedores/index', ['rubros' => $rubros, 'empresa' => $empresa]);
    }

    //Visualiza el ingreso de los datos del Rubro
    public function create(Request $request)
    {

        if ($this->rol()) {

            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $cuentas = plan_cuentas::where('estado', '2')->get();

        return view('contable/rubros_acreedores/create', ['cuentas' => $cuentas, 'empresa' => $empresa]);
    }

    //Guarda informacion del Rubro 
    public function store(Request $request)
    {

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

        $id_empresa = $request->session()->get('id_empresa');

        Ct_rubros::create([

            'codigo'                  => $request['codigo'],
            'nombre'                  => strtoupper($request['nombre']),
            'debe'                    => $request['cuenta_debe'],
            'haber'                   => $request['cuenta_haber'],
            'tipo'                    => $request['tipo'],
            'id_empresa'              => $id_empresa,
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

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $constraints = [
            'codigo'     => $request['codigo'],
            'nombre'     => $request['nombre'],
            'estado'     => 1,
            'id_empresa' => $id_empresa,
        ];

        $rubros = $this->doSearchingQuery($constraints, $id_empresa);

        return view('contable/rubros_acreedores/index', ['rubros' => $rubros, 'searchingVals' => $constraints, 'empresa' => $empresa]);
    }

    private function doSearchingQuery($constraints, $id_empresa)
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


        return $query->where('id_empresa', $id_empresa)->paginate(5);
    }

    public function editar($codigo, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $cuentas = plan_cuentas::where('estado', '2')->get();
        $rubro = Ct_rubros::where('codigo', $codigo)->first();

        $empresa  = Empresa::where('id', $id_empresa)->first();

        // Redirect to user list if updating user wasn't existed
        if ($rubro == null || $rubro == '') {
            return redirect()->intended('/contable/rubros');
        }

        return view('contable/rubros_acreedores/edit', ['rubro' => $rubro, 'cuentas' => $cuentas, 'empresa' => $empresa]);
    }


    public function update(Request $request, $id)
    {


        $rubros  = Ct_rubros::where('codigo', $id)->first();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

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
    public function nombre(Request $request)
    {
        $pedido    = $request['codigo'];
        $data      = null;
        $rubros = DB::table('ct_rubros')->where('estado', '1')->where('nombre', $pedido)->get();

        if ($rubros != '[]') {

            $data = [$rubros[0]->codigo, $rubros[0]->nombre];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public function autocomplete(Request $request)
    {
        $codigo = $request['term'];
        $data      = array();
        $rubros = DB::table('ct_rubros')->where('nombre', 'like', '%' . $codigo . '%')->get();
        foreach ($rubros as $prov) {
            $data[] = array('value' => $prov->nombre, 'codigo' => $prov->codigo);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
}
