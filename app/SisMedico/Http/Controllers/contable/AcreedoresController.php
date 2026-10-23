<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Detalle_Acreedores;
use Sis_medico\Ct_Rh_Tipo_Cuenta;
use Sis_medico\Ct_rubros;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_Contable;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;
use Sis_medico\TipoProveedor;
use Sis_medico\Pais;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Plan_Cuentas_Empresa;

class AcreedoresController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, [1, 4, 5, 20, 22]) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');

        $empresa = Empresa::where('id', $id_empresa)->first();
        //dd($empresa);
        $proveedores = DB::table('proveedor')
            ->join(
                'tipoproveedor',
                'proveedor.id_tipoproveedor',
                '=',
                'tipoproveedor.id'
            )
            ->select('proveedor.*', 'tipoproveedor.nombre')
            ->paginate('5');

        //dd($acreedores);

        return view('contable/acreedores/index', [
            'proveedores' => $proveedores,
            'empresa'     => $empresa,
        ]);
    }

    public function crear(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //$pais = Pais::all();
        $tipos = TipoProveedor::all();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $id_padre = Plan_Cuentas::join('plan_cuentas_empresa as pce', 'pce.id_plan', 'plan_cuentas.id')->where('id_empresa', $id_empresa)->where('plan_cuentas.estado', '1')->select('plan_cuentas.*','pce.plan','pce.id_plan')->get();
        $tipo_cuenta = Ct_Rh_Tipo_Cuenta::where('estado', '1')->get();
        // $id_configuracion = DB::table('ct_configuracion_bancos as ctcb')
        // // ->where('ctcb.id', $id)
        //     ->select('ctcb.nombre', 'ctcb.id')
        //     ->get();

        $id_configuracion = Ct_Bancos::where('estado', '1')->get();

        $retenciones = DB::table('ct_porcentaje_retenciones')
            ->where('tipo', '1')
            ->get();
        $retencioner = DB::table('ct_porcentaje_retenciones')
            ->where('tipo', '2')
            ->get();
        return view('contable/acreedores/create', [
            'tipos'            => $tipos,
            'id_padre'         => $id_padre,
            'retenciones'      => $retenciones,
            'retencioner'      => $retencioner,
            'empresa'          => $empresa,
            'tipo_cuenta'      => $tipo_cuenta,
            'id_configuracion' => $id_configuracion,
        ]);
    }

    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $constraints = [
            'id'              => $request['ruc'],
            'razonsocial'     => $request['razonsocial'],
            'nombrecomercial' => $request['nombrecomercial'],
        ];

        $proveedor = $this->doSearchingQuery($constraints);
        //dd($constraints);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        return view('contable/acreedores/index', [
            'proveedores'   => $proveedor,
            'searchingVals' => $constraints,
            'empresa'       => $empresa,
        ]);
    }

    private function doSearchingQuery($constraints)
    {
        $query = Proveedor::query()
            ->join(
                'tipoproveedor',
                'proveedor.id_tipoproveedor',
                '=',
                'tipoproveedor.id'
            )
            ->select('proveedor.*', 'tipoproveedor.nombre');
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where(
                    'proveedor.' . $fields[$index],
                    'like',
                    '%' . $constraint . '%'
                );
            }

            $index++;
        }

        return $query->paginate(5);
    }
    private function validateInput($request)
    {
        $this->validate($request, []);
    }

    public function store(Request $request)
    {
        $reglas = [
            'id'                => 'required|max:13|min:10|unique:empresa',
            'razonsocial'       => 'required|max:255',
            'nombrecomercial'   => 'required|max:255',
            'lista_contable'    => 'required',
            'ciudad'            => 'required|max:60',
            'direccion'         => 'required|max:255',
            'email'             => 'required|email|max:191',
            'telefono1'         => 'required|numeric|max:9999999999',
            'telefono2'         => 'required|numeric|max:9999999999',
            'estado'            => 'required',
            'id_tipo_proveedor' => 'required',
        ];

        $mensajes = [
            'id.unique'                  => 'El RUC ya se encuentra registrado.',
            'id.required'                => 'Agrega el RUC.',
            'id.max'                     => 'El RUC no puede ser mayor a :max caracteres.',
            'id.min'                     => 'El RUC no puede ser menor a :min caracteres.',
            'razonsocial.required'       => 'Agrega la razón social.',
            'razonsocial.max'            =>
            'La razon social no puede ser mayor a :max caracteres.',
            'nombrecomercial.required'   => 'Agrega el nombre comercial.',
            'nombrecomercial.max'        =>
            'El nombre comercial no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              =>
            'El segundo apellido no puede ser mayor a :max caracteres.',
            'ciudad.required'            => 'Agrega la ciudad.',
            'ciudad.max'                 => 'La ciudad no puede ser mayor a :max caracteres.',
            'direccion.required'         => 'Agrega la direccion.',
            'direccion.max'              =>
            'La direccion no puede ser mayor a :max caracteres.',
            'email.required'             => 'Agrega el Email.',
            'email.max'                  => 'El Email no puede ser mayor a :max caracteres.',
            'email.email'                => 'El Email tiene error en el formato.',
            'email2.email'               => 'El Email 2 tiene error en el formato.',
            'telefono1.required'         => 'Agrega el teléfono domicilio del usuario.',
            'telefono1.max'              =>
            'El teléfono domicilio del usuario no puede ser mayor a 10 caracteres.',
            'telefono1.numeric'          =>
            'El telefono domicilio del usuario debe ser numerico.',
            'telefono2.required'         => 'Agrega el teléfono celular del usuario.',
            'telefono2.max'              =>
            'El teléfono celular del usuario no puede ser mayor a 10 caracteres.',
            'telefono2.numeric'          =>
            'El teléfono celular del usuario debe ser numérico.',
            'estado.required'            => 'Agrega el estado.',
            'id_tipo_proveedor.required' => 'Agrega el tipo de proveedor.',
        ];

        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $idusuario  = Auth::user()->id;
        //dd("hola");
        $this->validateInput($request, $reglas, $mensajes);
        date_default_timezone_set('America/Guayaquil');

        $proveedor_id = Proveedor::where('id', $request['id'])->first();

        if (is_null($proveedor_id)) {
            Proveedor::create([
                'id'                => $request['id'],
                'razonsocial'       => strtoupper($request['razonsocial']),
                'nombrecomercial'   => strtoupper($request['nombrecomercial']),
                'ciudad'            => strtoupper($request['ciudad']),
                'direccion'         => strtoupper($request['direccion']),
                'email'             => $request['email'],
                'tipo'              => $request['tipo_identificacion'],
                'email2'            => $request['email2'],
                'autorizacion'      => $request['autorizacion'],
                'serie'             => $request['serie'],
                'banco' => $request['banco'],
                'cuenta'            => $request['cuenta'],
                'id_configuracion'  => $request['banco_c'],
                'tipo_cuenta'       => $request['tipo_cuenta'],
                'identificacion'    => $request['identificacion'],
                'beneficiario'      => $request['beneficiario'],
                'visualizar'        => '2',
                'telefono1'         => $request['telefono1'],
                'telefono2'         => $request['telefono2'],
                'id_cuentas'        => $request['lista_contable'],
                'id_grupo' => $request['id_grupo'],
                'id_tipoproveedor'  => $request['id_tipo_proveedor'],
                'id_porcentaje_iva' => $request['retencion_iva'],
                'id_porcentaje_ft'  => $request['retencion_ft'],
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
            ]);
            $contador = 0;
            foreach ($request['seried'] as $key => $value) {
                if ($value != '') {
                    Ct_Detalle_Acreedores::create([
                        'nombre'          => 'viene create',
                        'id_proveedor'    => $request['id'],
                        'serie'           => $value,
                        'sinicia'         => $request['secuenciaini'][$contador],
                        'sfin'            => $request['secuenciafin'][$contador],
                        'autorizacion'    => $request['autod'][$contador],
                        'f_caducidad'     => $request['f_caducidad'][$contador],
                        'estado'          => '1',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                }
                $contador++;
            }
        } else {
            $arrayProveedor = [
                'razonsocial' => strtoupper($request['razonsocial']),
                'nombrecomercial' => strtoupper($request['nombrecomercial']),
                'ciudad' => strtoupper($request['ciudad']),
                'direccion' => strtoupper($request['direccion']),
                'email' => $request['email'],
                'tipo' => $request['tipo_identificacion'],
                'email2' => $request['email2'],
                'autorizacion' => $request['autorizacion'],
                'serie' => $request['serie'],
                'banco' => $request['banco'],
                'cuenta' => $request['cuenta'],
                'id_configuracion' => $request['banco_c'],
                'tipo_cuenta' => $request['tipo_cuenta'],
                'identificacion' => $request['identificacion'],
                'beneficiario' => $request['beneficiario'],
                'visualizar' => '2',
                'telefono1' => $request['telefono1'],
                'telefono2' => $request['telefono2'],
                'id_cuentas' => $request['lista_contable'],
                'id_grupo' => $request['id_grupo'],
                'id_tipoproveedor' => $request['id_tipo_proveedor'],
                'id_porcentaje_iva' => $request['retencion_iva'],
                'id_porcentaje_ft' => $request['retencion_ft'],
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ];
            Proveedor::where('id', $proveedor_id->id)->update($arrayProveedor);
            $contador = 0;
            Ct_Detalle_Acreedores::where('id_proveedor', $request['id'])->delete();
            foreach ($request['seried'] as $key => $value) {
                if ($value != '') {
                    Ct_Detalle_Acreedores::create([
                        'nombre' => 'viene create',
                        'id_proveedor' => $request['id'],
                        'serie' => $value,
                        'sinicia' => $request['secuenciaini'][$contador],
                        'sfin' => $request['secuenciafin'][$contador],
                        'autorizacion' => $request['autod'][$contador],
                        'f_caducidad' => $request['f_caducidad'][$contador],
                        'estado' => '1',
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                    ]);
                }
                $contador++;
            }
        }

        return redirect()->route('acreedores_index');
    }
    public function subir_logo(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id     = $request['logo'];
        $reglas = [
            'archivo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:900',
        ];
        $mensajes = [
            'archivo.required' => 'Agrega el Logo.',
            'archivo.image'    => 'El logo debe ser una imagen.',
            'archivo.mimes'    =>
            'Los archivos permitidos son: jpeg,png,jpg,gif,svg.',
            'archivo.max'      => 'El peso del logo no puede ser mayor a :max KB.',
        ];

        $this->validate($request, $reglas, $mensajes);

        $nombre_original = $request['archivo']->getClientOriginalName();
        $extension       = $request['archivo']->getClientOriginalExtension();
        $nuevo_nombre    = 'logo_proveedor' . $id . '.' . $extension;

        $r1 = Storage::disk('logo')->put(
            $nuevo_nombre,
            \File::get($request['archivo'])
        );

        $rutadelaimagen = $nuevo_nombre;

        if ($r1) {
            $ip_cliente = $_SERVER['REMOTE_ADDR'];
            $idusuario  = Auth::user()->id;
            date_default_timezone_set('America/Guayaquil');
            $empresa                  = Proveedor::find($id);
            $empresa->logo            = $rutadelaimagen;
            $empresa->ip_modificacion = $ip_cliente;
            $empresa->id_usuariomod   = $idusuario;
            $r2                       = $empresa->save();

            return redirect()->route('acreedores_index');
        }
    }
    public function editar($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $proveedor = DB::table('proveedor')
            ->join('tipoproveedor', 'proveedor.id_tipoproveedor', '=', 'tipoproveedor.id')
            ->select('proveedor.*', 'tipoproveedor.nombre')
            ->where('proveedor.id', '=', $id)
            ->first();
        //dd($proveedor);
        // Redirect to user list if updating user wasn't existed
        
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();

        $tipos = TipoProveedor::all();
        $id_padre = Plan_Cuentas::join('plan_cuentas_empresa as pce', 'pce.id_plan', 'plan_cuentas.id')
            ->where('id_empresa', $id_empresa)
            ->where('pce.estado', 1)
            ->select("plan_cuentas.*", 'pce.plan as id_mostrar','pce.id_plan')
            ->get();
        $detalles         = Ct_Detalle_Acreedores::where('id_proveedor', $id)->get();
        $tipo_cuenta      = Ct_Rh_Tipo_Cuenta::where('estado', '1')->get();
        //$id_configuracion = DB::table('ct_configuracion_bancos as ctcb')
        // ->where('ctcb.id', $id)
        //->select('ctcb.nombre', 'ctcb.id')
        //  ->get();

        $id_configuracion = Ct_Bancos::where('estado', '1')->get();
        $retenciones = DB::table('ct_porcentaje_retenciones')
            ->where('tipo', '1')
            ->get();
        $retencioner = DB::table('ct_porcentaje_retenciones')
            ->where('tipo', '2')
            ->get();

        //dd($retencioner);
        return view('contable/acreedores/edit', [
            'proveedor'        => $proveedor,
            'detalles'         => $detalles,
            'tipos'            => $tipos,
            'id_padre'         => $id_padre,
            'retenciones'      => $retenciones,
            'empresa'          => $empresa,
            'retencioner'      => $retencioner,
            'id_configuracion' => $id_configuracion,
            'tipo_cuenta'      => $tipo_cuenta,

        ]);
    }

    public function update(Request $request, $id)
    {
        //dd($request->all());
        $empresa    = Proveedor::findOrFail($id);
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $mensajes = [
            'id.unique'                  => 'El RUC ya se encuentra registrado.',
            'id.required'                => 'Agrega el RUC.',
            'id.max'                     => 'El RUC no puede ser mayor a :max caracteres.',
            'id.min'                     => 'El RUC no puede ser menor a :min caracteres.',

            'razonsocial.required'       => 'Agrega la razón social.',
            'razonsocial.max'            =>
            'La razon social no puede ser mayor a :max caracteres.',

            'nombrecomercial.required'   => 'Agrega el nombre comercial.',
            'nombrecomercial.max'        =>
            'El nombre comercial no puede ser mayor a :max caracteres.',

            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              =>
            'El segundo apellido no puede ser mayor a :max caracteres.',

            'ciudad.required'            => 'Agrega la ciudad.',
            'ciudad.max'                 => 'La ciudad no puede ser mayor a :max caracteres.',

            'direccion.required'         => 'Agrega la direccion.',
            'direccion.max'              =>
            'La direccion no puede ser mayor a :max caracteres.',

            'email.required'             => 'Agrega el Email.',
            'email.max'                  => 'El Email no puede ser mayor a :max caracteres.',
            'email.email'                => 'El Email tiene error en el formato.',

            'telefono1.required'         => 'Agrega el teléfono domicilio del usuario.',
            'telefono1.max'              =>
            'El teléfono domicilio del usuario no puede ser mayor a 10 caracteres.',
            'telefono1.numeric'          =>
            'El telefono domicilio del usuario debe ser numerico.',
            'telefono2.required'         => 'Agrega el teléfono celular del usuario.',
            'telefono2.max'              =>
            'El teléfono celular del usuario no puede ser mayor a 10 caracteres.',
            'telefono2.numeric'          =>
            'El telefono cellular del usuario debe ser numérico.',
            'id_tipo_proveedor.required' => 'Agrega el tipo de proveedor.',
            'estado.required'            => 'Agrega el estado.',
        ];
        $constraints = [
            'id'                => 'required|max:13|min:10|unique:empresa,id,' . $id,
            'razonsocial'       => 'required|max:255',
            'nombrecomercial'   => 'required|max:255',
            'ciudad'            => 'required|max:60',
            'direccion'         => 'required|max:255',
            'email'             => 'required|email|max:191',
            'telefono1'         => 'required|numeric|max:9999999999',
            'telefono2'         => 'required|numeric|max:9999999999',
            'estado'            => 'required',
            'id_tipo_proveedor' => 'required',
        ];

        $prov = Proveedor::where('id', $request['id'])->first();
        //dd($request->all());
        $plan_cuentas = Plan_Cuentas_Empresa::where("id_plan", $request['acreedores'])->first();
        $input = [
            'razonsocial'       => strtoupper($request['razonsocial']),
            'nombrecomercial'   => strtoupper($request['nombrecomercial']),
            'ciudad'            => strtoupper($request['ciudad']),
            'direccion'         => strtoupper($request['direccion']),
            'tipo'              => $request['tipo_identificacion'],
            'email'             => $request['email'],
            'email2'            => $request['email2'],
            'banco'             => $request['banco'],
            'cuenta'            => $request['cuenta'],
            'identificacion'    => $request['identificacion'],
            'beneficiario'      => $request['beneficiario'],
            'tipo_cuenta'       => $request['tipo_cuenta'],
            'id_configuracion'  => $request['banco_c'],
            'id_cuentas'        => $request['lista_contable'],
            'autorizacion'      => $request['autorizacion'],
            'telefono1'         => $request['telefono1'],
            'telefono2'         => $request['telefono2'],
            'serie'             => $request['serie'],
            "id_grupo"          => $plan_cuentas->id_plan,
            // "id_grupo"          => $request['acreedores'],
            'id_tipoproveedor'  => $request['id_tipo_proveedor'],
            'id_porcentaje_iva' => $request['retencion_iva'],
            'id_porcentaje_ft'  => $request['retencion_ft'],
            'ip_modificacion'   => $ip_cliente,
            'id_usuariomod'     => $idusuario,
        ];

        if (!is_null($prov)) {
            // $input = [
            //     //'id' => $request['id'],
            //     'razonsocial'       => strtoupper($request['razonsocial']),
            //     'nombrecomercial'   => strtoupper($request['nombrecomercial']),
            //     'ciudad'            => strtoupper($request['ciudad']),
            //     'direccion'         => strtoupper($request['direccion']),
            //     'tipo'              => $request['tipo_identificacion'],
            //     'email'             => $request['email'],
            //     'email2'            => $request['email2'],
            //     'banco'             => $request['banco'],
            //     'cuenta'            => $request['cuenta'],
            //     'identificacion'    => $request['identificacion'],
            //     'beneficiario'      => $request['beneficiario'],
            //     'tipo_cuenta'       => $request['tipo_cuenta'],
            //     'id_configuracion'  => $request['banco_c'],
            //     'id_cuentas'        => $request['lista_contable'],
            //     'autorizacion'      => $request['autorizacion'],
            //     'telefono1'         => $request['telefono1'],
            //     'telefono2'         => $request['telefono2'],
            //     'serie'             => $request['serie'],
            //     'id_tipoproveedor'  => $request['id_tipo_proveedor'],
            //     'id_porcentaje_iva' => $request['retencion_iva'],
            //     'id_porcentaje_ft'  => $request['retencion_ft'],
            //     'ip_modificacion'   => $ip_cliente,
            //     'id_usuariomod'     => $idusuario,
            // ];

            $contador = 0;
            $det      = Ct_Detalle_Acreedores::where('id_proveedor', $request['id']);
            //dd($request->all());
            $det->delete();
            foreach ($request['validate'] as $key => $value) {
                //dd("entra forecah");
                if ($value != null) {

                    if (!is_null($request['seried'][$contador])) {

                        //dd("dada");
                        Ct_Detalle_Acreedores::create([
                            'nombre'          => 'viene edit',
                            'id_proveedor'    => $request['id'],
                            'serie'           => $request['seried'][$contador],
                            'sinicia'         => $request['secuenciaini'][$contador],
                            'sfin'            => $request['secuenciafin'][$contador],
                            'autorizacion'    => $request['autod'][$contador],
                            'f_caducidad'     => $request['f_caducidad'][$contador],
                            'estado'          => '1',
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                        //dd($contador);

                    }
                    $contador++;
                }
            }
        } else {
            // $input = [
            //     'id'               => $request['id'],
            //     'razonsocial'      => strtoupper($request['razonsocial']),
            //     'nombrecomercial'  => strtoupper($request['nombrecomercial']),
            //     'ciudad'           => strtoupper($request['ciudad']),
            //     'direccion'        => strtoupper($request['direccion']),
            //     'tipo'             => $request['tipo_identificacion'],
            //     'email'            => $request['email'],
            //     'banco'            => $request['banco'],
            //     'cuenta'           => $request['cuenta'],
            //     'id_configuracion' => $request['id_configuracion'],
            //     'tipo_cuenta'      => $request['tipo_cuenta'],
            //     'identificacion'   => $request['identificacion'],
            //     'beneficiario'     => $request['beneficiario'],
            //     'email2'           => $request['email2'],
            //     'autorizacion'     => $request['autorizacion'],
            //     'telefono1'        => $request['telefono1'],
            //     'serie'            => $request['serie'],
            //     'telefono2'        => $request['telefono2'],
            //     'id_tipoproveedor' => $request['id_tipo_proveedor'],
            //     'ip_modificacion'  => $ip_cliente,
            //     'id_usuariomod'    => $idusuario,
            // ];

            $input["id"] = $request['id'];
        }

        $this->validate($request, $constraints, $mensajes);

        Proveedor::where('id', $id)->update($input);

        return redirect()->route('acreedores_index');
    }
    public function saldos_iniciales(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $proveedor  = Proveedor::where('estado', 1)->get();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        return view('contable/saldos_iniciales/index', [
            'id_empresa' => $id_empresa,
            'empresa' => $empresa,
            'proveedor' => $proveedor,
            'sucursales' => $sucursales,
        ]);
    }

    public function buscar_proveedor(Request $request)
    {
        $proveedor = [];
        if ($request['search'] != null) {
            $proveedor     = Proveedor::where('estado', 1)
                ->where('nombrecomercial', 'like', '%' . $request['search'] . '%')
                ->select('nombrecomercial as text', 'id as id')
                ->get();
        }
        return response()->json($proveedor);
    }

    public function saldos_iniciales_index(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $compras    = Ct_compras::where('tipo', '3')
            ->where('id_empresa', $id_empresa)
            ->paginate(10);
        $proveedor = Proveedor::where('estado', 1)->get();
        $empresa   = Empresa::where('id', $id_empresa)->first();
        return view('contable/saldos_iniciales/index1', [
            'id_empresa' => $id_empresa,
            'compras'    => $compras,
            'empresa'    => $empresa,
            'proveedor'  => $proveedor,
        ]);
    }
    //validation

    public function searchsaldos(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $constraints = [
            'id'          => $request['id'],
            'observacion' => $request['observacion'],
            'proveedor'   => $request['proveedor'],
        ];
        $proveedor = Proveedor::where('estado', 1)->get();
        $rubros    = $this->doSearchingQuery2($constraints, $id_empresa);
        return view('contable/saldos_iniciales/index1', [
            'compras'       => $rubros,
            'searchingVals' => $constraints,
            'empresa'       => $empresa,
            'proveedor'     => $proveedor,
        ]);
    }
    private function doSearchingQuery2($constraints, $id_empresa)
    {
        $query  = Ct_compras::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where(
                    $fields[$index],
                    'like',
                    '%' . $constraint . '%'
                );
            }

            $index++;
        }

        return $query
            ->where('tipo', '3')
            ->where('id_empresa', $id_empresa)
            ->paginate(10);
    }
    public function anular_saldo($id, Request $request)
    {
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $idusuario  = Auth::user()->id;
        $estado_compra = Ct_compras::where('id', $id)
            ->where('estado', '<>', 0)
            ->first();
        if (!empty($estado_compra)) {
            $act_estado = [
                'estado'          => '-1',
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $fechahoy = Date('Y-m-d H:i:s');
            Ct_compras::where('id', $id)->update($act_estado);
            //Necesito llenar los datos de la factura pero al revès para que cumplan los datos y quiten las cuentas en el haber
            $compras      = Ct_compras::where('id', $id)->first();
            $contador_ctv = DB::table('ct_compras')
                ->get()
                ->count();
            $id_empresa = $request->session()->get('id_empresa');
            $cabecera   = Ct_Asientos_Cabecera::where(
                'id',
                $compras->id_asiento_cabecera
            )->first();
            $cabecera->tipo          = 0;
            $cabecera->estado        = 1;
            $cabecera->id_usuariomod = $idusuario;
            $cabecera->save();

            $actualiza = [
                'estado'          => '1',
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $cabecera->update($actualiza);
            $detalles   = $cabecera->detalles;
            $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                'observacion'     => 'ANULACIÓN ' . $cabecera->observacion,
                'fecha_asiento'   => $cabecera->fecha_asiento,
                'id_empresa'      => $id_empresa,
                'fact_numero'     => $cabecera->secuencia,
                'valor'           => $cabecera->valor,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'sucursal'      => $cabecera->sucursal,
            ]);
            foreach ($detalles as $value) {
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento,
                    'id_plan_cuenta'      => $value->id_plan_cuenta,
                    'debe'                => $value->haber,
                    'haber'               => $value->debe,
                    'descripcion'         => $value->descripcion,
                    'fecha'               => $cabecera->fecha_asiento,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ]);
            }
            Log_Contable::create([
                'tipo'           => 'A',
                'valor_ant'      => $cabecera->valor,
                'valor'          => $cabecera->valor,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod'  => $idusuario,
                'observacion'    => $cabecera->concepto,
                'id_ant'         => $cabecera->id,
                'id_referencia'  => $id_asiento,
            ]);
            return redirect()->route('saldosinicialesp.index2');
        }
    }
    public function saldos_iniciales_edit($id, Request $request)
    {
        $compras    = Ct_compras::find($id);
        $id_empresa = $request->session()->get('id_empresa');
        $proveedor  = Proveedor::where('estado', 1)->get();
        $empresa    = Empresa::where('id', $id_empresa)->first();
        return view('contable/saldos_iniciales/edit', [
            'compras'   => $compras,
            'proveedor' => $proveedor,
        ]);
    }
    public function guardar_iniciales(Request $request)
    {
        $id_empresa   = $request->session()->get('id_empresa');
        $contador_ctv = DB::table('ct_asientos_cabecera')
            ->get()
            ->count();
        $numero_factura = 0;
        if ($contador_ctv == 0) {
            //return 'No Retorno nada';
            $num            = '1';
            $numero_factura = str_pad($num, 10, '0', STR_PAD_LEFT);
        } else {
            //Obtener Ultimo Registro de la Tabla ct_compras
            $max_id = DB::table('ct_asientos_cabecera')->max('id');

            if ($max_id >= 1 && $max_id < 10) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 10, '0', STR_PAD_LEFT);
            }

            if ($max_id >= 10 && $max_id < 100) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 10, '0', STR_PAD_LEFT);
            }

            if ($max_id >= 100 && $max_id < 1000) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 10, '0', STR_PAD_LEFT);
            }

            if ($max_id == 1000) {
                $numero_factura = $max_id;
            }
        }
        $puntoEmision = explode('-', $request['pemision']);
        if (count($puntoEmision) > 2) {
            $pEmision = $puntoEmision[1];
        } else {
            $pEmision = $puntoEmision[0];
        }
        $numeroconcadenado = $request['sucursal'] . '-' . $pEmision . '-' . $numero_factura; //'001-002-' . $numero_factura;
        $ip_cliente        = $_SERVER['REMOTE_ADDR'];
        $valr              = 0;
        $valor2            = 0;
        $idusuario         = Auth::user()->id;
        $cabeceraa         = [
            'observacion'     =>
            'SALDOS INICIALES : ' .
                $request['concepto'] .
                ' A: ' .
                $request['id_proveedor'],
            'fecha_asiento'   => $request['fecha_hoy'],
            'fact_numero'     => $numero_factura,
            'valor'           => $request['total'],
            'id_empresa'      => $id_empresa,
            'estado'          => '1',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'sucursal' => $request['sucursal'],
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
        if (!is_null($request['contador'])) {
            $primerarray = [];
            for ($i = 0; $i < $request['contador']; $i++) {
                if ($request['visibilidad' . $i] == 1) {
                    $valr += $request['valor' . $i];
                    $consulta_rubro = Ct_rubros::where(
                        'codigo',
                        $request['id_codigo' . $i]
                    )->first();
                    if ($consulta_rubro != '[]' || $consulta_rubro != null) {
                        $segundoarray = [
                            $consulta_rubro->haber,
                            $request['valor' . $i],
                        ];
                        $key = array_search(
                            $consulta_rubro->haber,
                            array_column($primerarray, '0')
                        );

                        if ($key !== false) {
                            $valor2               = $primerarray[$key][1];
                            $valor2               = $valor2 + $request['valor' . $i];
                            $primerarray[$key][0] = $consulta_rubro->haber;
                            $primerarray[$key][1] = $valor2;
                        } else {
                            array_push($primerarray, $segundoarray);
                        }
                    }
                }
            }

            for ($file = 0; $file < count($primerarray); $file++) {
                $cuent_descrip = Plan_Cuentas::where(
                    'id',
                    $primerarray[$file][0]
                )->first();
                $cuenta = $primerarray[$file][0];
                $debe   = number_format($primerarray[$file][1], 2, '.', '');
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $cuenta,
                    'descripcion'         => $cuent_descrip->nombre,
                    'fecha'               => $request['fecha_hoy'],
                    'debe'                => $debe,
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            $cuenta_docxpagar = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ACREEDORES_CUENTASYDOCxPAGAR');


            $plan_cuentas2 = Plan_Cuentas::where('id', $cuenta_docxpagar->cuenta_guardar)->first();

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta_docxpagar->cuenta_guardar,
                'descripcion'         => $cuenta_docxpagar->nombre_mostrar,
                'fecha'               => $request['fecha_hoy'],
                'haber'               => $valr,
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
            $input = [
                'tipo'                => $request['tipo'],
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => $request['fecha_hoy'],
                'numero'              => $numeroconcadenado,
                'archivo_sri'         => $request['archivo_sri'],
                'proveedor'           => $request['id_proveedor'],
                'termino'             => $request['termino'],
                'secuencia_f'         => $numero_factura,
                'observacion'         => $request['concepto'],
                'tipo'                => '3',
                'sucursal'            => '001',
                'punto_emision'       => '001',
                'valor_contable'      => $request['total'],
                'f_caducidad'         => $request['fecha_hoy'],
                'autorizacion'        => $request['autorizacion'],
                'f_autorizacion'      => $request['fecha_hoy'],
                'id_empresa'          => $id_empresa,
                'serie'               => '001',
                'secuencia_factura'   => '94213213321',
                'credito_tributario'  => $request['credito_tributario'],
                'tipo_comprobante'    => '01',
                'subtotal'            => $request['total'],
                'descuento'           => $request['descuento1'],
                'iva_total'           => $request['iva_final1'],
                'ice_total'           => $request['ice_final1'],
                'total_final'         => $request['total'],
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];
            $id_compra = Ct_compras::insertGetId($input);
            return [$id_asiento_cabecera, $id_compra, $numero_factura];
        } else {
            return 'error vacios';
        }
        return 'ok';
    }
}
