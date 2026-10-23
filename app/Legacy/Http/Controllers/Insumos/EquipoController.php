<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Equipo;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;

class EquipoController extends Controller
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
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 7)) == false) {
            return true;
        }
    }
    public function index()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $equipo = Equipo::where('estado', '1')->get(); //3=DOCTORES
        //dd($equipo);

        return view('insumos/equipo/index', ['equipo' => $equipo]);
    }

    public function create()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('insumos/equipo/create');
    }
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $this->validateInput_Equipo($request);
        date_default_timezone_set('America/Guayaquil');
        Equipo::create([
            'nombre'          => strtoupper($request['nombre']),
            'tipo'            => strtoupper($request['tipo']),
            'marca'           => strtoupper($request['marca']),
            'modelo'          => strtoupper($request['modelo']),
            'fecha_ingreso'   => strtoupper($request['fecha']),
            'serie'           => strtoupper($request['serie']),
            'prestamo'        => $request['prestamo'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ]);

        return redirect()->intended('/equipo');
    }
    public function validateInput_Equipo(Request $request)
    {
        $reglas = [
            'nombre' => 'required',
            'tipo'   => 'required',
            'marca'  => 'required',
            'modelo' => 'required',
            //'fecha'  => 'required',
            'serie'  => 'required|unique:equipo',
        ];

        $mensajes = [
            'nombre.required' => 'Ingrese un nombre',
            'tipo.required'   => 'Ingrese el tipo',
            'marca.required'  => 'Ingrese la marca',
            'modelo.required' => 'Ingrese el modelo',
            //'fecha.required'  => 'Ingrese la fecha de Ingreso',
            'serie.required'  => 'Ingrese la serie unica del equipo',
            'serie.unique'    => 'Serie de ingreso ya se encuentra registrada',
        ];

        $this->validate($request, $reglas, $mensajes);

    }
    public function edit($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $equipo = Equipo::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($equipo == null) {
            return redirect()->intended('/equipo');
        }
        //dd($equipo);
        //return view('paciente/edit', ['paciente' => $paciente])->with('paises',$paises)->with('seguros',$seguros)->with('rolusuario', $rolusuario);
        //return "hola";
        return view('insumos/equipo/edit', ['equipo' => $equipo]);
    }

    public function update(Request $request, $id)
    {
        //return "hola";
        $equipo     = equipo::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $this->validateInput_equipo2($request, $id);

        $input = [
            'nombre'              => strtoupper($request['nombre']),
            'tipo'                => strtoupper($request['tipo']),
            'marca'               => strtoupper($request['marca']),
            'modelo'              => strtoupper($request['modelo']),
            'estado'              => $request['estado'],
            'fecha_ingreso'       => $request['fecha'],
            'fecha_mantenimiento' => $request['fecha_mantenimiento'],
            'serie'               => strtoupper($request['serie']),
            'ip_modificacion'     => $ip_cliente,
            'id_usuariomod'       => $idusuario,
        ];

        $equipo->update($input);

        return redirect()->intended('/equipo');
    }

    public function validateInput_equipo2(Request $request, $id)
    {
        $reglas = [
            'serie'  => 'required|unique:equipo,serie,' . $id,
            'nombre' => 'required',
            'tipo'   => 'required',
            'marca'  => 'required',
            'modelo' => 'required',
            //'fecha'  => 'required',
        ];

        $mensajes = [
            'nombre.required' => 'Ingrese un nombre',
            'tipo.required'   => 'Ingrese el tipo',
            'marca.required'  => 'Ingrese la marca',
            'modelo.required' => 'Ingrese el modelo',
            //'fecha.required'  => 'Ingrese la fecha de Ingreso',
            'serie.required'  => 'Ingrese la serie unica del equipo',
            'serie.unique'    => 'Serie de ingreso ya se encuentra registrada',
        ];

        $this->validate($request, $reglas, $mensajes);

    }

    public function search(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $constraints = [
            'nombre' => $request['nombre'],

        ];

        $equipo = $this->doSearchingQuery($constraints);

        return view('insumos/equipo/index', ['equipo' => $equipo, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints)
    {
        $query  = Equipo::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->get();
    }

    public function imprimir_barra($id)
    {
        $producto = Equipo::findOrFail($id);
        $data     = $producto;
        $date     = date('Y-m-d');
        $view     = \View::make('insumos.equipo.unico', compact('data', 'date'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper(array(0, 0, 300, 120));

        return $pdf->stream('Codigo-de-Barra-pedido-n-' . $id . '.pdf');
    }
}
