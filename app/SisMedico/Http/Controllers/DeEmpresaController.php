<?php

namespace Sis_medico\Http\Controllers;

use Sis_medico\De_Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Empresa;

class DeEmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \Sis_medico\De_Empresa  $de_Empresa
     * @return \Illuminate\Http\Response
     */
    public function show(De_Empresa $de_Empresa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Sis_medico\De_Empresa  $de_Empresa
     * @return \Illuminate\Http\Response
     */
    public function edit(De_Empresa $de_Empresa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Sis_medico\De_Empresa  $de_Empresa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, De_Empresa $de_Empresa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Sis_medico\De_Empresa  $de_Empresa
     * @return \Illuminate\Http\Response
     */
    public function destroy(De_Empresa $de_Empresa)
    {
        //
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

            return redirect()->intended('/empresa');
        }
    }

    function guardarArchivo(Request $req)
    {
        $archivo = '';
        if ($req->tipo_archivo == 'entidadFirma') {
            $archivo = $req->tipo_archivo;
            $ext = explode('.', $_FILES[$archivo]['name']);
            if ($_FILES[$archivo]['type'] == 'application/x-pkcs12') {
                if ($_FILES[$archivo]['error']) {
                    switch ($_FILES[$archivo]['error']) {
                        case 1:
                            echo "El archivo sobrepasa el limite autorizado por el servidor(archivo php.ini)!";
                            break;
                        case 2:
                            echo "El archivo sobrepasa el limite autorizado en el formulario HTML!";
                            break;
                        case 3:
                            echo "El envio del archivo ha sido suspendido durante la transferencia!";
                            break;
                        case 4:
                            echo "El archivo que ha enviado tiene un tamaño nulo!";
                            break;
                    }
                } else {
                    move_uploaded_file($_FILES[$archivo]['tmp_name'], base_path().'/storage/app/facturaelectronica/p12/' . $_FILES[$archivo]['name']);
                }
            } else {
                return 'no|El archivo debe ser (p12)';
            }
        } else {
            return 'no|El archivo debe ser (p12)';
        }
    }
}
