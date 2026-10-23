<?php

namespace Sis_medico\Http\Controllers;

use Google\Service\Script;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Sis_medico\De_Maestro_Documentos;
use Illuminate\Http\Request;

class DeMaestroDocumentosController extends Controller
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
        $de_maestro_documentos = De_Maestro_Documentos::where('estado', '1')->get();
        return view('sri_electronico/de_maestro_documentos/index', ['de_maestro_documentos' => $de_maestro_documentos]);
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
        return view('sri_electronico/de_maestro_documentos/create');
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
        $documentos = De_Maestro_Documentos::where('estado', 1)->get();
        $flag = false;
        foreach ($documentos as $doc) {
            if (strtoupper($request['nombre']) == strtoupper($doc->nombre)) {
                $flag = true;

                break;
            }
        }
        if ($flag == false) {
            $arr_de_maestro_doc = [
                'nombre'           => $request['nombre'],
                'estado'           => 1,
                'codigo'             => $request['codigo'],

                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
            ];
            De_Maestro_Documentos::create($arr_de_maestro_doc);
            return redirect(route('demaestrodoc.index'));
        } else {

            echo "<script>$(function() { $('#modal_falla').modal('show'); });</script>";
            return redirect(route('demaestrodoc.create'));
        }
    }

    /**
     * Display the specified resource.
     *

     * @param  \Sis_medico\De_Maestro_Documentos  $de_maestro_documentos
     * @return \Illuminate\Http\Response
     */


    public function edit($id)

    /*
     * @param  \Sis_medico\De_Maestro_Documentos  $de_maestro
     * @return \Illuminate\Http\Response
     */

    {
        $de_maestro = De_Maestro_Documentos::where('id', $id)->first();
        return view('sri_electronico/de_maestro_documentos/edit', ['de_maestro' => $de_maestro, 'id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request

     * @param  \Sis_medico\De_Daestro_Documentos  $de_maestro_documentos

     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $de_maestro = De_Maestro_Documentos::where('id', $request->id)->first();
        $documentos = De_Maestro_Documentos::where('estado', 1)->get(['nombre']);
        $flag = false;
        foreach ($documentos as $dm) {
            if (strtoupper($request['nombre'] != $de_maestro->nombre)) {
                if (strtoupper($request['nombre']) == strtoupper($dm->nombre)) {
                    $flag = true;
                    break;
                }
            }
        }
        if ($flag == false) {
            $arr_de_maestro_doc = [
                'nombre'              => $request['nombre'],
                'estado'              => 1,
                'codigo'               => $request['codigo'],
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
            ];
            $de_maestro->update($arr_de_maestro_doc);
            return redirect(route('demaestrodoc.index'));
            //return json_encode(['result' => 2]);
        } else {
            return redirect(route('demaestrodoc.edit', ['id' => $request['id']]));
            //return json_encode(['result' => 1]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Sis_medico\De_Maestro_Documentos  $de_maestro_documentos
     * @return \Illuminate\Http\Response
     */
}
