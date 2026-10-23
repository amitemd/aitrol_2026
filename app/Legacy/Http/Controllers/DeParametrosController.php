<?php

namespace Sis_medico\Http\Controllers;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Sis_medico\De_Parametros;
use Illuminate\Http\Request;

class DeParametrosController extends Controller
{
    protected $redirecto = '/';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
    public function index()
    {
        if ($this->rol()) {
            return response()->view('error404');
        }
        $de_parametros = De_Parametros::where('estado', '1')->get();
        return view('sri_electronico/de_parametros/index', ['de_parametros' => $de_parametros]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if ($this->rol()) {
            return response()->view('error404');
        }
        return view('sri_electronico/de_parametros/create');
    }

    /*
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $documentos = De_Parametros::where('estado', 1)->get();
        $flag = false;
        foreach ($documentos as $doc) {
            if (strtoupper($request['nombre']) == strtoupper($doc->nombre)) {
                $flag = true;

                break;
            }
        }
        if ($flag == false) {
            $arr_de_parametros = [
                'nombre'           => $request['nombre'],
                'estado'           => 1,
                'valor'             => $request['valor'],

                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
            ];
            De_Parametros::create($arr_de_parametros);
            return redirect(route('deParametros.index'));
        } else {
            return redirect(route('deParametros.create'));
        }
    }

    /**
     * Display the specified resource.
     *

     * @param  \Sis_medico\De_Parametros  $de_parametros
     * @return \Illuminate\Http\Response
     */


    public function edit($id)

    /*
     * @param  \Sis_medico\De_Parametros  $de_parametros
     * @return \Illuminate\Http\Response
     */

    {
        $de_parametros = De_Parametros::where('id', $id)->first();
        return view('sri_electronico/de_parametros/edit', ['de_parametros' => $de_parametros, 'id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request

     * @param  \Sis_medico\De_Parametros  $de_parametros

     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $de_parametros = De_Parametros::find($request['id'])->first();
        $documentos = De_Parametros::where('estado', 1)->get(['nombre']);
        $flag = false;
        foreach ($documentos as $dm) {
            if (strtoupper($request['nombre'] != $de_parametros->nombre)) {
                if (strtoupper($request['nombre']) == strtoupper($dm->nombre)) {
                    echo $request['nombre'] . ' - ' . $dm->nombre;
                    exit;
                    $flag = true;
                    break;
                }
            }
        }
        if ($flag == false) {
            $arr_de_parametros = [
                'nombre'              => $request['nombre'],
                'estado'              => 1,
                'codigo'               => $request['codigo'],
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
            ];
            $de_parametros->update($arr_de_parametros);
            return json_encode(['result' => 2]);
        } else {
            return json_encode(['result' => 1]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Sis_medico\De_Parametros  $de_parametros
     * @return \Illuminate\Http\Response
     */
}
