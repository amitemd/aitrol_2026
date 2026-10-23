<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Hospital;
use Sis_medico\Sala;
use Sis_medico\Mantenimientos_Oficinas;
use Sis_medico\Mantenimientos_Banos;
use Sis_medico\Mantenimientos_Generales;

class Mantenimientos_BanosController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/sala-management';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 24)) == false) {
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
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $mantenimiento_banos = Mantenimientos_Banos::paginate(50);

        return view('mantenimientos/mantenimientos_banos/index', ['mantenimiento_banos' => $mantenimiento_banos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('mantenimientos/mantenimientos_banos/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        Mantenimientos_Banos::create([

            'nombre'     => strtoupper($request['nombre']),
            'id_unidad'     => $request['id_unidad'],
            'descripcion'     => $request['descripcion'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);

        //return redirect()->intended('/sala-management');
        return redirect()->intended('servicios_generales/mantenimientos_banos/{mantenimientos_generales}/listasbanos');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $mantenimiento_banos = Mantenimientos_Banos::find($id);
        // Redirect to user list if updating user wasn't existed
   

        return view('servicios_generales/mantenimientos_banos/edit', ['mantenimiento_banos' => $mantenimiento_banos]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $mantenimiento_banos  = Mantenimientos_Banos::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $messages = [
            'nombre_sala.required' => 'Agrega el nombre de la sala.',
            'nombre_sala.max'      => 'El nombre de la sala no puede ser mayor a :max caracteres.',
            'id_hospital.required' => 'Agrega el hospital.',
            'estado.required'      => 'Agrega el estado.',

        ];

        $constraints = [
            'nombre_sala' => 'required|max:30',
            'id_hospital' => 'required',
            'estado'      => 'required',
        ];

        $input = [
            'nombre'     => strtoupper($request['nombre']),
            'id_unidad'     => $request['id_unidad'],
            'estado'          => $request['estado'],
            'descripcion'          => $request['descripcion'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $this->validate($request, $constraints, $messages);

        Mantenimientos_Banos::where('id', $id)
            ->update($input);

        return redirect()->intended('/sala-management');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function destroy($id)
    {
    User::where('id', $id)->delete();
    return redirect()->intended('/user-management');
    }
     */
    /**
     * Search user from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $constraints = [
            'nombre' => $request['nombre'],

        ];

        $mantenimiento_banos = $this->doSearchingQuery($constraints);

        return view('sala-mgmt/index', ['mantenimiento_banos' => $mantenimiento_banos, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints)
    {
        $query  = Mantenimientos_Banos::query();
        $fields = array_keys($constraints);
        $index  = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }
        return $query->paginate(50);
    }

    private function validateInput($request)
    {
        $messages = [
            'nombre_sala.required' => 'Agrega el nombre de la sala.',
            'nombre_sala.max'      => 'El nombre de la sala no puede ser mayor a :max caracteres.',

        ];

        $constraints = [
            'nombre_sala' => 'required|max:30',

        ];

        $this->validate($request, $constraints, $messages);

    }

    public function listasalas($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $mantenimiento_banos    = Mantenimientos_Banos::where('id_unidad', '=', $id)->paginate(50);
        $mantenimientos_g = Mantenimientos_Generales::find($id);

        $nombre_hospital = $mantenimientos_g->nombre_hospital;

        return view('sala-mgmt/listasbanos', ['mantenimiento_banos' => $mantenimiento_banos, 'mantenimientos_g' => $mantenimientos_g]);
    }

    public function crear($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $hospital = Hospital::find($id);
        return view('sala-mgmt/crearsala', ['hospital' => $hospital]);
    }

    public function grabar(Request $request, $id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        Sala::create([

            'nombre_sala'     => strtoupper($request['nombre_sala']),
            'id_hospital'     => $request['id_hospital'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);

        //return redirect()->intended('/sala-management');
        $salas    = Sala::where('id_hospital', '=', $id)->paginate(50);
        $hospital = Hospital::find($id);

        $nombre_hospital = $hospital->nombre_hospital;
        return view('sala-mgmt/listasalas', ['salas' => $salas, 'hospital' => $hospital]);
    }

    public function editar($id_hospital, $id_sala)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $salas = Sala::find($id_sala);
        // Redirect to user list if updating user wasn't existed
        if ($salas == null || count($salas) == 0) {
            //return redirect()->intended('/sala-management');
        }
        $hospital = Hospital::find($id_hospital);

        $nombre_hospital = $hospital->nombre_hospital;

        return view('sala-mgmt/editar', ['salas' => $salas, 'hospital' => $hospital]);
    }

    public function actualizar(Request $request, $id_hospital, $id_sala)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $salas      = Sala::findOrFail($id_sala);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $messages = [
            'nombre_sala.required' => 'Agrega el nombre de la sala.',
            'nombre_sala.max'      => 'El nombre de la sala no puede ser mayor a :max caracteres.',
            'estado.required'      => 'Agrega el estado.',

        ];

        $constraints = [
            'nombre_sala' => 'required|max:30',
            'estado'      => 'required',
        ];

        $input = [
            'nombre_sala'     => strtoupper($request['nombre_sala']),
            'id_hospital'     => $request['id_hospital'],
            'estado'          => $request['estado'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $this->validate($request, $constraints, $messages);

        Sala::where('id', $id_sala)
            ->update($input);

        $salas    = Sala::where('id_hospital', '=', $id_hospital)->paginate(50);
        $hospital = Hospital::find($id_hospital);

        $nombre_hospital = $hospital->nombre_hospital;
        return view('sala-mgmt/listasalas', ['salas' => $salas, 'hospital' => $hospital]);
    }

}
