<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Manual;
use Sis_medico\Observacion_General;
use Sis_medico\User;

class ManualController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
        if (in_array($rolUsuario, array(1, 4, 5, 14, 20, 22)) == false) {
            return true;
        }
    }
    public function index()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $manuales = Manual::where('estado', '1')->paginate(20);

        //$fecha = Date('Y-m-d');
        //dd($manuales['0']->usuario_crea()->get());

        return view('manual/index', ['manuales' => $manuales]);
    }

    public function subir($id)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //return "aqui";

        return view('manual/sube_archivo_ajx', ['id' => $id]);
    }
    public function cargar_file(Request $request)
    {

        $id = $request['id'];

        $manual = Manual::findOrFail($id);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $reglas   = ['archivo' => 'required|mimes:pdf,xls,xlsx,doc,docx|max:10000'];
        $mensajes = [

            'archivo.required' => 'Agrega un archivo.',
            'archivo.mimes'    => 'El archivo a seleccionar debe ser *.pdf,xls,xlsx,doc,docx.',
            'archivo.max'      => 'El peso del archivo no puede ser mayor a :max KB.'];

        $this->validate($request, $reglas, $mensajes);

        $nombre_original = $request['archivo']->getClientOriginalName();
        $extension       = $request['archivo']->getClientOriginalExtension();
        $nuevo_nombre    = $id . "_" . $manual->nombre . "." . $extension;

        //return $nuevo_nombre;
        $r1 = Storage::disk('manual')->put($nuevo_nombre, \File::get($request['archivo']));

        if ($r1) {

            $manual->archivo         = $nuevo_nombre;
            $manual->ip_modificacion = $ip_cliente;
            $manual->id_usuariomod   = $idusuario;
            $r2                      = $manual->save();

        }

        return "ok";
    }

    public function create()
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('manual/create');
    }

    public function search($fecha)
    {

        if ($fecha == null) {
            $fecha = date('Y-m-d');
        } else {
            $fecha = date('Y-m-d', $fecha);
        }

        $fecha_hasta = date('Y-m-d', strtotime($fecha . "- 1 month"));

        //dd($fecha,$fecha_hasta);

        $observaciones = Observacion_General::whereBetween('created_at', [$fecha_hasta, $fecha . ' 23:59'])->OrderBy('created_at', 'desc')->get();
        //dd($observaciones);

        return view('observacion/index_lista', ['observaciones' => $observaciones]);
    }

    public function cantidad()
    {

        $fecha = date('Y-m-d');

        $observaciones = Observacion_General::where('estado', '1')->whereBetween('created_at', [$fecha . ' 00:00:00', $fecha . ' 23:59:00'])->OrderBy('created_at', 'desc')->get();

        //dd($observaciones);

        return $observaciones->count();
    }

    public function store(Request $request)
    {

        date_default_timezone_set('America/Guayaquil');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        Manual::create([
            'nombre'          => $request['nombre'],
            'descripcion'     => $request['descripcion'],
            'fecha_inicio'     => $request['fecha_inicio'],
            'fecha_fin'     => $request['fecha_fin'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ]);

        return redirect()->intended('/manual');
    }

    public function inactiva($id)
    {

        date_default_timezone_set('America/Guayaquil');
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $observacion = Observacion_General::find($id);

        if ($observacion != null) {

            $observacion->update([

                'estado'          => '-1',
                'ip_modificacion' => $ip_cliente,

                'id_usuariomod'   => $idusuario,

            ]);

        }

        return redirect()->intended('/observacion');
    }

    public function activa($id)
    {

        date_default_timezone_set('America/Guayaquil');
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $observacion = Observacion_General::find($id);

        if ($observacion != null) {

            $observacion->update([

                'estado'          => '1',
                'ip_modificacion' => $ip_cliente,

                'id_usuariomod'   => $idusuario,

            ]);

        }

        return redirect()->intended('/observacion');
    }

    private function validateInput($request)
    {
        $this->validate($request, [
            'nombre'      => 'required|max:60|unique:seguros',
            'descripcion' => 'required',
            'tipo'        => 'required',
            'color'       => 'required|unique:seguros',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $manual = Manual::find($id);

        return view('manual/edit', ['manual' => $manual]);
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

        date_default_timezone_set('America/Guayaquil');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $manual = Manual::find($id);

        if (!is_null($manual)) {

            $input = [
                'nombre'          => $request['nombre'],
                'descripcion'     => $request['descripcion'],
                'fecha_inicio'     => $request['fecha_inicio'],
                'fecha_fin'     => $request['fecha_fin'],

                'ip_modificacion' => $ip_cliente,

                'id_usuariomod'   => $idusuario,

            ];

            $manual->update($input);

        }

        return redirect()->intended('/manual');

    }

    public function load($name)
    {

        //dd("hola");
        $path = storage_path() . '/app/manual/' . $name;
        if (file_exists($path)) {

            return Response::make(file_get_contents($path), 200, [
                'Content-Type'        => 'application/pdf/xlsx/xls/doc/docx',
                'Content-Disposition' => 'inline; filename="' . $name . '"',
            ]);
        }
    }

    public function show($id)
    {
        //
    }

    public function modal($id)
    {
        $imagen = manual::find($id);
        return view('manual/modal', ['imagen' => $imagen]);
    }

    public function eliminar_manual($id)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $manual = Manual::find($id);

        $manual->update(['estado' => 0]);

        return redirect()->intended('/manual');
    }

    public function ver_archivo($id_archivo)
    {
        if (!is_null($id_archivo)) {
            $pathtoFile = storage_path() . '/app/manual/' . $id_archivo;
            if (file_exists($pathtoFile)) {
                return response()->file($pathtoFile);
            }
        }
        return "no cargado";
    }

}
