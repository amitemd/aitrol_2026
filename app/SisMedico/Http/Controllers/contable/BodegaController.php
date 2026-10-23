<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Ct_BodegaInterna;
use Sis_medico\Http\Controllers\Controller;

class BodegaController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

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
        if (in_array($rolUsuario, array(1)) == false) {
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

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $bodegas = Ct_BodegaInterna::where('id_empresa', session('id_empresa'))->paginate(10);
        return view('contable/compra/bodega/index', ['bodegas' => $bodegas]);
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
        return view('contable/compra/bodega/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //dd($request);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        Ct_BodegaInterna::create([
            'nombre'          => $request['nombre'],
            'estado'          => 1,
            'id_empresa'      => $request->session()->get('id_empresa'),
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ]);

        return redirect()->route('contable.bodega.index');
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
        $bodega = Ct_BodegaInterna::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($bodega == null || count($bodega) == 0) {
            return redirect()->route('contable.bodega.index');
        }

        return view('contable/compra/bodega/edit', ['bodega' => $bodega]);
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
        $Ct_BodegaInterna = Ct_BodegaInterna::findOrFail($id);
        $ip_cliente       = $_SERVER["REMOTE_ADDR"];
        $idusuario        = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $input = [
            'estado'          => $request['tipo'],
            'nombre'          => $request['nombre'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        Ct_BodegaInterna::where('id', $id)
            ->update($input);

        return redirect()->route('contable.bodega.index');
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
    }*/

    /**
     * Search user from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $constraints = [
            'id'          => $request['ruc'],
            'razonsocial' => $request['razonsocial'],
        ];

        $bodegas = $this->doSearchingQuery($constraints);

        return view('contable/compra/bodega/index', ['bodegas' => $bodegas, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints)
    {
        $query  = Empresa::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(5);
    }

    private function validateInput($request)
    {
        $this->validate($request, []);

    }

    public function subir_logo(Request $request)
    {
        $id       = $request['logo'];
        $reglas   = ['archivo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:900'];
        $mensajes = [

            'archivo.required' => 'Agrega el Logo.',
            'archivo.image'    => 'El logo debe ser una imagen.',
            'archivo.mimes'    => 'Los archivos permitidos son: jpeg,png,jpg,gif,svg.',
            'archivo.max'      => 'El peso del logo no puede ser mayor a :max KB.'];

        $this->validate($request, $reglas, $mensajes);

        $nombre_original = $request['archivo']->getClientOriginalName();
        $extension       = $request['archivo']->getClientOriginalExtension();
        $nuevo_nombre    = "logo" . $id . "." . $extension;

        $r1 = Storage::disk('logo')->put($nuevo_nombre, \File::get($request['archivo']));

        $rutadelaimagen = $nuevo_nombre;

        if ($r1) {

            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            date_default_timezone_set('America/Guayaquil');
            $empresa                  = Empresa::find($id);
            $empresa->logo            = $rutadelaimagen;
            $empresa->ip_modificacion = $ip_cliente;
            $empresa->id_usuariomod   = $idusuario;
            $r2                       = $empresa->save();

            return redirect()->route('contable.bodega.index');
        }
    }

    /**
     * Load image resource.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function load($name)
    {

        $path = storage_path() . '/app/logo/' . $name;
        if (file_exists($path)) {
            return Response::download($path);
        }
    }

}
