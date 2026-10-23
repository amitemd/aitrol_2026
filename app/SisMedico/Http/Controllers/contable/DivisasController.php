<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Divisas;



class DivisasController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $divisas = Ct_Divisas::where('estado', '=', 1)->orderby('id', 'asc')->paginate(10);


        return view('contable.divisas.index', ['divisas' => $divisas]);
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $constraints = [
            'id'  => $request['buscar_codigo'],
            'descripcion'  => $request['buscar_nombre'],
            'estado'  => 1,
        ];

        $divisas = $this->doSearchingQuery($constraints);
        return view('contable.divisas.index', ['request' => $request, 'divisas' => $divisas, 'searchingVals' => $constraints]);
    }
    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Divisas::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(10);
    }

    public function crear(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('contable.divisas.create');
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        Ct_Divisas::create([

            'estado'                => $request['estado_divisas'],
            'descripcion'           => $request['divisas_nombre'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,

        ]);

        return redirect()->route('divisas.index');
    }

    public function editar($id)
    {
        
        $divisas = Ct_Divisas::findorfail($id);
     
        return view('contable.divisas.edit',['divisas' => $divisas]); 
    }

    public function update(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');


        $id = $request['id_divisas'];
        $tipo_divisa = Ct_Divisas::findOrFail($id);
        
        $input = [

            'descripcion'           => $request['descrip_ambiente'],
            'estado'                => $request['estado_divisas'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,

        ]; 

        $tipo_divisa->update($input); 
        
        return redirect()->route('divisas.index');

    }
}
