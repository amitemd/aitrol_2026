<?php

namespace Sis_medico\Http\Controllers\guia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Ct_transportista;
use Sis_medico\Http\Controllers\guia\console;

use function PHPSTORM_META\map;;

class TransportistasController extends Controller
{
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     *
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $mantenimientos_transportistas = Ct_transportista::where('estado', '1')->get();
        return view('contable/Guia_Remision/Transportistas/index', ['mantenimientos_transportistas' => $mantenimientos_transportistas]);
    }
    public function editar($id)
    {
        $mantenimientos_transportistas = Ct_transportista::where('ci_ruc',$id)->first();

        return view('contable/Guia_Remision/Transportistas/editar', ['mantenimientos_transportistas' => $mantenimientos_transportistas, 'id' => $id]);
    }
    public function update(Request $request)
    {

        $mensaje = '';
        $estado = '';
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        Ct_transportista::where('ci_ruc', $request['id'])->update([
              'ci_ruc'              => $request['ci_ruc'],
              'razon_social'        => $request['razon_social'],
              'nombres'             => $request['nombres'],
              'apellidos'           => $request['apellidos'],
              'id_empresa'          => $request['id_empresa'],
              'nombrecomercial'     => $request['nombrecomercial'],
              'ciudad'              => $request['ciudad'],
              'direccion'           => $request['direccion'],
              'email'               => $request['email'],
              'email2'              => $request['email2'],
              'telefono1'           => $request['telefono1'],
              'telefono2'           => $request['telefono2'],
              'logo'                => $request['logo'],
              'placa'               => $request['placa'],
              'identificacion'      => $request['identificacion'],
              'tipo_documento'      => $request['tipo_documento'],
              'rise'                  => $request['rise'],
              'contabilidad'           => $request['contabilidad'],
              'contribuyente_especial' =>$request['contribuyente_especial'],
              'id_usuariocrea'       => $idusuario,
              'id_usuariomod'        => $idusuario,
              'ip_creacion'          => $ip_cliente,
              'ip_modificacion'      => $ip_cliente,
          ]);
        return redirect(route('transportistas.index'));
    }

    public function delete(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id = $request->id;

        $mantenimientos_transportistas = Ct_transportista::find($id);
        //dd($informacion_tubos);
        $array_eliminar = [

            'ci_ruc'              => $mantenimientos_transportistas->ci_ruc,
            'razon_social'              => $mantenimientos_transportistas->razon_social,
            'nombres'               => $mantenimientos_transportistas->nombres,
            'apellidos'               => $mantenimientos_transportistas->apellidos,
            'id_empresa'               => $mantenimientos_transportistas->id_empresa,
            'nombrecomercial'               => $mantenimientos_transportistas->nombrecomercial,
            'ciudad'               => $mantenimientos_transportistas->ciudad,
            'direccion'               => $mantenimientos_transportistas->direccion,
            'email'               => $mantenimientos_transportistas->email,
            'email2'               => $mantenimientos_transportistas->email2,
            'telefono1'               => $mantenimientos_transportistas->telefono1,
            'telefono2'               => $mantenimientos_transportistas->telefono2,
            'logo'               => $mantenimientos_transportistas->logo,
            'placa'               => $mantenimientos_transportistas->placa,
            'identificacion'               => $mantenimientos_transportistas->identificacion,
            'tipo_documento'               => $mantenimientos_transportistas->tipo_documento,
            'rise'               => $mantenimientos_transportistas->rise,
            'contabilidad'               => $mantenimientos_transportistas->contabilidad,
            'contribuyente_especial'               => $mantenimientos_transportistas->contribuyente_especial,

            'estado'     => 0,
            /*'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,*/

        ];

        $mantenimientos_transportistas->update($array_eliminar);

        return redirect(route('transportistas.index'));
    }
    public function crear()
    {

        $mantenimientos_transportistas = Ct_transportista::where('estado', '1')->get();
        return view('contable/Guia_Remision/Transportistas/crear', ['mantenimientos_transportistas' => $mantenimientos_transportistas]);
    }
    public function store(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $arr_transportista = [
            'ci_ruc'              => $request['ci_ruc'],
            'razon_social'              => $request['razon_social'],
            'nombres'               => $request['nombres'],
            'apellidos'               => $request['apellidos'],
            'id_empresa'               => $request['id_empresa'],
            'nombrecomercial'               => $request['nombrecomercial'],
            'ciudad'               => $request['ciudad'],
            'direccion'               => $request['direccion'],
            'email'               => $request['email'],
            'email2'               => $request['email2'],
            'telefono1'               => $request['telefono1'],
            'telefono2'               => $request['telefono2'],
            'logo'               => $request['logo'],
            'placa'               => $request['placa'],
            'identificacion'               => $request['identificacion'],
            'tipo_documento'               => $request['tipo_documento'],
            'rise'               => $request['rise'],
            'contabilidad'               => $request['contabilidad'],
            'contribuyente_especial'               => $request['contribuyente_especial'],

            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,


        ];


        Ct_transportista::create($arr_transportista);

        return redirect(route('transportistas.index'));
    }

    public function subir_logo(Request $request)
    {
        $id       = $request['logo'];
        $reglas   = ['archivo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:900'];
        $mensajes = [

            'archivo.required' => 'Agrega el Logo.',
            'archivo.image'    => 'El logo debe ser una imagen.',
            'archivo.mimes'    => 'Los archivos permitidos son: jpeg,png,jpg,gif,svg.',
            'archivo.max'      => 'El peso del logo no puede ser mayor a :max KB.'
        ];

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
            $empresa                  = Ct_transportista::find($id);
            $empresa->logo            = $rutadelaimagen;
            $empresa->ip_modificacion = $ip_cliente;
            $empresa->id_usuariomod   = $idusuario;
            $r2                       = $empresa->save();

            return redirect()->intended('/empresa');
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

    function guardarArchivo(Request $req)
    {
        $archivo = '';
        if ($req->tipo_archivo == 'entidadFirma') {
            $archivo = $req->tipo_archivo;
            $ext = explode('.', $_FILES[$archivo]['name']);
            if ($_FILES[$archivo]['type'] == 'application/x-pkcs12') {
                $contenidoBinario = file_get_contents($_FILES[$archivo]['tmp_name']);
                return 'ok|<label title="Subir firma digital" for="entidadFirma" class="btn btn-primary mb-1" style="cursor: pointer;"><input type="file" id="entidadFirma" name="entidadFirma" style="display:none;"><i class="fa fa-thumbs-up"></i>&nbsp;Firma encontrada</label>';
            } else {
                return 'no|El archivo debe ser (p12)';
            }
        } elseif ($req->tipo_archivo == 'entidadLogo') {
            $archivo = 'entidadLogo';
            $ext = explode('.', $_FILES[$archivo]['name']);
            if ($_FILES[$archivo]['type'] == 'image/png' || $_FILES[$archivo]['type'] == 'image/jpg' || $_FILES[$archivo]['type'] == 'image/jpeg') {
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
                    move_uploaded_file($_FILES[$archivo]['tmp_name'], base_path().'/storage/app/logo/' . $_FILES[$archivo]['name']);
                }
            }

            

            return 'ok';
        } else {
            return 'no|El archivo debe ser (png, jpg)';
        }
    }
}
