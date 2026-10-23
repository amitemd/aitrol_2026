<?php

namespace Sis_medico\Http\Controllers\sri_electronico;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Empresa;
use Sis_medico\De_Empresa;
use Sis_medico\Http\Controllers\EmisionDocumentosController;

class maestros_deController extends Controller
{
    public $nombreArchivo;

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


    public function edit($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $deempresa =  De_Empresa::where('id_empresa', $id)->get()->first();

        if (is_null($deempresa)) {
            $ids = De_Empresa::insertGetId([
                'id_empresa'     => $id,
                'ip_creacion'    => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario
            ]);

            $deempresa = De_empresa::find($ids);
        }
        //$empresa   = Empresa::find($id);

        return view('de_empresa/edit', ['deempresa' => $deempresa]);
    }

    public function update(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $config = array('firmar' => true, 'pass' =>  $request->clave_firma, 'file' => base_path() . '/storage/app/facturaelectronica/p12/' . $request->texfirma);


        //$firmar = new FirmaElectronica($config);


        $deempresas = De_empresa::find($request['id']);
        $deempresas->id_empresa = $request->id_empresa;
        $deempresas->agente_retencion = $request->agente_retencion;
        $deempresas->ambiente = $request->ambiente;
        $deempresas->tipo_contribuyente = $request->tipo_contribuyente;
        $deempresas->contabilidad = $request->contabilidad;
        $deempresas->contribuyente_especial  = $request->contribuyente_especial;
        $deempresas->tipo_rimpe = $request->tipo_rimpe;
        $deempresas->ruta_firma  = $request->texfirma;
        $deempresas->clave_firma  = $request->clave_firma;
        $deempresas->estado     = 1;
        $deempresas->ip_creacion = $ip_cliente;
        $deempresas->ip_modificacion = $ip_cliente;
        $deempresas->id_usuariocrea = $idusuario;
        $deempresas->id_usuariomod = $idusuario;
        $deempresas->save();
        return redirect('empresa');
    }

    function guardarArchivo(Request $req)
    {
        $archivo = '';
        if ($req->tipo_archivo == 'entidadFirma') {
            $archivo = $req->tipo_archivo;
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
                    EmisionDocumentosController::crearcarpeta(base_path() . '/storage/app/facturaelectronica/p12/');
                    $nombreArchivo = $_FILES[$archivo]['name'];
                    move_uploaded_file($_FILES[$archivo]['tmp_name'], base_path() . '/storage/app/facturaelectronica/p12/' . $_FILES[$archivo]['name']);
                    return 'ok|' . $nombreArchivo;
                }
            } else {
                return 'no|El archivo debe ser (p12)';
            }
        } else {
            return 'no|El archivo debe ser (p12)';
        }
    }
}
