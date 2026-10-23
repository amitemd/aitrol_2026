<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\De_Log_Error;

class DeDocumentosElectronicosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $controlador = 'srielectronico';
    public function index(Request $req)
    {
        config(['data' => []]);
        if ($req->submodulo == '') {
            $data['controlador'] = $this->controlador;
            config(['data' => $data]);
            return view('sri_electronico.de_documentos_electronicos.index');
        } elseif ($req->submodulo == 'documentosElectronicosjs') {
            $data = De_Documentos_Electronicos::getInfo();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            echo json_encode($results);
        } elseif ($req->submodulo == 'recepcionSRI') {
            echo 1;
        } elseif ($req->submodulo == 'AutorizacionSRI') {
            echo 2;
        } elseif ($req->submodulo == 'erroresSRI') {
            $data = De_Log_Error::getErrores($req->id);
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            echo json_encode($results);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Sis_medico\de_documentos_electronicos  $de_documentos_electronicos
     * @return \Illuminate\Http\Response
     */
    public function show(de_documentos_electronicos $de_documentos_electronicos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Sis_medico\de_documentos_electronicos  $de_documentos_electronicos
     * @return \Illuminate\Http\Response
     */
    public function edit(de_documentos_electronicos $de_documentos_electronicos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Sis_medico\de_documentos_electronicos  $de_documentos_electronicos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, de_documentos_electronicos $de_documentos_electronicos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Sis_medico\de_documentos_electronicos  $de_documentos_electronicos
     * @return \Illuminate\Http\Response
     */
    public function destroy(de_documentos_electronicos $de_documentos_electronicos)
    {
        //
    }
}
