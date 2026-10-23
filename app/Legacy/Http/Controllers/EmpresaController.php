<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Empresa;
use Sis_medico\LogAsiento;

class EmpresaController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/empresa';

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

        /*$data['empresa']     = "0993075000001";
        $data['comprobante'] = "001-002-000000147";
        $data['tipo']        = "comprobante";

        $envio = ApiFacturacionController::estado_comprobante($data);
        dd($envio);

        $variable = '{"company":"0993075000001","person":{"document":"0914948575001","documentType":"04","name":"JONATHAN","surname":"LEONIDAS CASTRO","email":"joncas_daireaux@hotmail.com","mobile":"0992730716","address":{"street":"URB. PARAISO","city":"GUAYAQUIL","country":"EC"}},"items":[{"sku":"LABS-633","name":"TEST ALIENTO - FRUCTOSA","qty":1,"price":45,"discount":0,"subtotal":45,"tax":0,"total":45},{"sku":"LABS-630","name":"TEST ALIENTO - LACTOSA","qty":1,"price":45,"discount":0,"subtotal":45,"tax":0,"total":45},{"sku":"LABS-FEE","name":"FEE-ADMINISTRATIVO","qty":1,"price":6.3,"discount":0,"subtotal":6.3,"tax":0,"total":6.3}],"billingParameters":{"establecimiento":"001","ptoEmision":"002","infoAdicional":[{"key":"AGENTES_RETENCION","value":"Resolucion 1"},{"key":"PACIENTE","value":"0914948575 CASTRO JONATHAN"},{"key":"MAIL","value":"joncas_daireaux@hotmail.com"},{"key":"CIUDAD","value":"GUAYAQUIL"},{"key":"DIRECCION","value":"URB. PARAISO"},{"key":"ORDEN","value":"25417"},{"key":"SEGURO","value":"PARTICULAR"}],"formaPago":"19","plazoDias":"10"},"userAgent":"SIAAM SOFTWARE\/1"}';

        dd(json_decode($variable));

        /*return view('mails.procedimiento', ["procedimiento_nombre" => 'Procedimiento Prueba', "nombre_paciente" => 'Eduardo Faustos', "especialidad_nombre" => 'Gastro', "inicio" => '2021-01-04 14:20:00', "nombre_doctor" => 'Prueba Prueba', "hospital_nombre" => 'Torre Médica II', "consultorio_nombre" => 'Consultorio 405 -4 06 | Consultorio 2 - CRM', "hospital_direccion" => 'Calle Abel Romeo Castillo 13 E Ne Y, Av. Juan Tanca Marengo']);*/
        //facturacion electronica
        /*$data['empresa']      = '0993075000001';
        $cliente['cedula']    = "0922729587001";
        $cliente['tipo']      = "5";
        $cliente['nombre']    = "Eduardo";
        $cliente['apellido']  = "Faustos Nivelo";
        $cliente['email']     = "edyfan@hotmail.com";
        $cliente['telefono']  = "0983975972";
        $direccion['calle']   = 'Sauces 6';
        $direccion['ciudad']  = 'Guayaquil';
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;

        //se envian los productos
        $producto['sku']       = "LABS-1192";
        $producto['nombre']    = "CONSULTA ONLINE CIR";
        $producto['copago']    = 0;
        $producto['cantidad']  = "1";
        $producto['precio']    = 22;
        $producto['descuento'] = 2;
        $producto['subtotal']  = 20;
        $producto['tax']       = 2.4;
        $producto['total']     = 22.4;

        $productos[0] = $producto;

        $data['productos'] = $productos;

        /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
        15  COMPENSACIÓN DE DEUDAS
        16  TARJETA DE DÉBITO
        17  DINERO ELECTRÓNICO
        18  TARJETA PREPAGO
        19  TARJETA DE CRÉDITO
        20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
        21  ENDOSO DE TÍTULOS
         */

        /*$info_adicional['nombre']      = "correo";
        $info_adicional['valor']       = "edyfan@hotmail.com";
        $info[0]                       = $info_adicional;
        $pago['informacion_adicional'] = $info;
        $pago['forma_pago']            = '01';
        $pago['dias_plazo']            = '30';
        $data['pago']                  = $pago;
        $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
        $data['laboratorio']           = 1;
        $data['paciente']              = '0922729587';
        $data['concepto']              = 'Ingreso de Factura Electronica';
        $data['copago']                = 0;
        $data['id_seguro']             = '1'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
        $data['total_factura']         = '11.20';

        $tipos_pago['id_tipo']            = 1; //metodo de pago efectivo, tarjeta, etc
        $tipos_pago['fecha']              = '2020-07-01';
        $tipos_pago['tipo_tarjeta']       = null; //si es efectivo no se envia
        $tipos_pago['numero_transaccion'] = null; //si es efectivo no se envia
        $tipos_pago['id_banco']           = null; //si es efectivo no se envia
        $tipos_pago['cuenta']             = null; //si es efectivo no se envia
        $tipos_pago['giradoa']            = null; //si es efectivo no se envia
        $tipos_pago['valor']              = '11.20'; //valor a pagar de total
        $tipos_pago['valor_base']         = '11.20';
        $pagos[0]                         = $tipos_pago;

        $tipos_pago['id_tipo']            = 1; //metodo de pago efectivo, tarjeta, etc
        $tipos_pago['fecha']              = '2020-07-01';
        $tipos_pago['tipo_tarjeta']       = null; //si es efectivo no se envia
        $tipos_pago['numero_transaccion'] = null; //si es efectivo no se envia
        $tipos_pago['id_banco']           = null; //si es efectivo no se envia
        $tipos_pago['cuenta']             = null; //si es efectivo no se envia
        $tipos_pago['giradoa']            = null; //si es efectivo no se envia
        $tipos_pago['valor']              = '11.20'; //valor a pagar de total
        $tipos_pago['valor_base']         = '11.20'; //valor a pagar de base
        $pagos[1]                         = $tipos_pago;
        $data['formas_pago']              = $pagos;

        $envio = ApiFacturacionController::crea_factura($data, '001-002-000000766');*/
        //
        /*
        //envio hacia estado del comprobante

        //tipo es
        // comprobante = informacion del comprobante
        // pdf =  el ride pdf de autorizacion
        // xml =  Documento en archivo xml
        $data['empresa']     = "0993075000001";
        $data['comprobante'] = "001-002-000000137";
        $data['tipo']        = "comprobante";

        $envio = ApiFacturacionController::estado_comprobante($data);
        dd($envio);*/
        //solo si es comprobante retorna un dato del resto ya descarga el pdf o el xml

        //crear retencion
        /*$data                       = array();
        $data['empresa']            = "0992704152001";
        $proveedor['cedula']        = "0922729587001";
        $proveedor['tipo']          = "04"; //04 ruc, 05 cedula /06 pasaporte, 08 identificacion extranjera
        $proveedor['nombre']        = "Eduardo";
        $proveedor['apellido']      = "Faustos Nivelo";
        $proveedor['email']         = "edyfan@hotmail.com";
        $data['proveedor']          = $proveedor;
        $comprobante['fecha']       = "17/02/2021";
        $comprobante['tipo']        = "01"; //01 factura
        $comprobante['periodo']     = "02/2021";
        $comprobante['comprobante'] = "001-001-000000020";
        $data['comprobante']        = $comprobante;
        //se envian los detalle de la retencion
        $impuesto['tipo']          = "1";
        $impuesto['impuesto']      = "303";
        $impuesto['baseimponible'] = 100.00;
        $impuesto['porcentaje']    = 10.00;
        $impuesto['valorretenido'] = 10.00;
        $impuestos[0]              = $impuesto;

        $data['impuesto'] = $impuestos;

        $info_adicional['nombre']      = "correo";
        $info_adicional['valor']       = "edyfan@hotmail.com";
        $info[0]                       = $info_adicional;
        $pago['informacion_adicional'] = $info;
        $data['pago']                  = $pago;
        $envio                         = ApiFacturacionController::crearRetencion($data);
        dd($envio);*/

        //crear notas de credito
        /*$data['empresa']      = '1314490929001';
        $cliente['cedula']    = "0922729587001";
        $cliente['tipo']      = "5";
        $cliente['nombre']    = "Eduardo";
        $cliente['apellido']  = "Faustos Nivelo";
        $cliente['email']     = "edyfan@hotmail.com";
        $cliente['telefono']  = "0983975972";
        $direccion['calle']   = 'Sauces 6';
        $direccion['ciudad']  = 'Guayaquil';
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;

        $factura['comprobante']  = "001-001-000000249";
        $factura['fechaemision'] = "28/01/2021";
        $factura['motivo']       = "Devolucion de articulos";
        $data['factura']         = $factura;

        //se envian los productos
        $producto['sku']       = "LABS-1192";
        $producto['nombre']    = "CONSULTA ONLINE CIR";
        $producto['copago']    = 0;
        $producto['cantidad']  = 1;
        $producto['precio']    = 22;
        $producto['descuento'] = 2;
        $producto['subtotal']  = 20;
        $producto['tax']       = 2.4;
        $producto['total']     = 22.4;

        $productos[0] = $producto;

        $data['productos'] = $productos;

        /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
        15  COMPENSACIÓN DE DEUDAS
        16  TARJETA DE DÉBITO
        17  DINERO ELECTRÓNICO
        18  TARJETA PREPAGO
        19  TARJETA DE CRÉDITO
        20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
        21  ENDOSO DE TÍTULOS
         */

        /*$info_adicional['nombre']      = "correo";
        $info_adicional['valor']       = "edyfan@hotmail.com";
        $info[0]                       = $info_adicional;
        $pago['informacion_adicional'] = $info;
        $pago['forma_pago']            = '01';
        $pago['dias_plazo']            = '30';
        $data['pago']                  = $pago;

        //dd($data);
        $envio = ApiFacturacionController::crearNotasCredito($data);
        dd($envio);*/
        /*$cambiar = DB::table('validacion')->get();
        foreach ($cambiar as $value) {
        $proveedor = Proveedor::find($value->ruc);
        if (!is_null($proveedor)) {
        $banco = DB::table('ct_configuracion_bancos')->where('submotivo_pago', $value->banco)->first();
        //dd($banco);
        if (is_null($banco)) {
        dd($value);
        }
        $proveedor->identificacion = $value->identificacion;
        $proveedor->beneficiario   = $value->beneficiario;
        $proveedor->save();
        //dd($proveedor);
        }

        }dd($cambiar);*/
        $empresas = Empresa::where('estado', '1')->paginate(10);
        return view('empresa/index', ['empresas' => $empresas]);
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
        return view('empresa/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $reglas = [
            'id'              => 'required|max:13|min:13|unique:empresa',
            'razonsocial'     => 'required|max:255',
            'nombrecomercial' => 'required|max:255',
            'ciudad'          => 'required|max:60',
            'direccion'       => 'required|max:255',
            'email'           => 'required|email|max:191',
            'telefono1'       => 'required|numeric|max:9999999999',
            'telefono2'       => 'required|numeric|max:9999999999',
            'estado'          => 'required',
        ];

        $mensajes = [
            'id.unique'                => 'El RUC ya se encuentra registrado.',
            'id.required'              => 'Agrega el RUC.',
            'id.max'                   => 'El RUC no puede ser mayor a :max caracteres.',
            'id.min'                   => 'El RUC no puede ser menor a :min caracteres.',
            'razonsocial.required'     => 'Agrega la razón social.',
            'razonsocial.max'          => 'La razon social no puede ser mayor a :max caracteres.',
            'nombrecomercial.required' => 'Agrega el nombre comercial.',
            'nombrecomercial.max'      => 'El nombre comercial no puede ser mayor a :max caracteres.',
            'apellido2.required'       => 'Agrega el segundo apellido.',
            'apellido2.max'            => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'ciudad.required'          => 'Agrega la ciudad.',
            'ciudad.max'               => 'La ciudad no puede ser mayor a :max caracteres.',
            'direccion.required'       => 'Agrega la direccion.',
            'direccion.max'            => 'La direccion no puede ser mayor a :max caracteres.',
            'email.required'           => 'Agrega el Email.',
            'email.max'                => 'El Email no puede ser mayor a :max caracteres.',
            'email.email'              => 'El Email tiene error en el formato.',
            'telefono1.required'       => 'Agrega el teléfono domicilio del usuario.',
            'telefono1.max'            => 'El teléfono domicilio del usuario no puede ser mayor a 10 caracteres.',
            'telefono1.numeric'        => 'El telefono domicilio del usuario debe ser numerico.',
            'telefono2.required'       => 'Agrega el teléfono celular del usuario.',
            'telefono2.max'            => 'El teléfono celular del usuario no puede ser mayor a 10 caracteres.',
            'telefono2.numeric'        => 'El telefono cellular del usuario debe ser numérico.',
            'estado.required'          => 'Agrega el estado.',
        ];

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $this->validateInput($request, $reglas, $mensajes);
        date_default_timezone_set('America/Guayaquil');
        Empresa::create([
            'id'              => $request['id'],
            'razonsocial'     => strtoupper($request['razonsocial']),
            'nombrecomercial' => strtoupper($request['nombrecomercial']),
            'ciudad'          => strtoupper($request['ciudad']),
            'direccion'       => strtoupper($request['direccion']),
            'email'           => $request['email'],
            'telefono1'       => $request['telefono1'],
            'telefono2'       => $request['telefono2'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ]);
        LogAsiento::secuenciaEmpresa($request['id']);
        return redirect()->intended('/empresa');
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
        $empresa = Empresa::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($empresa == null || $empresa=='') {
            return redirect()->intended('/empresa');
        }

        return view('empresa/edit', ['empresa' => $empresa]);
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
        $empresa    = Empresa::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $mensajes = [

            'id.unique'                => 'El RUC ya se encuentra registrado.',
            'id.required'              => 'Agrega el RUC.',
            'id.max'                   => 'El RUC no puede ser mayor a :max caracteres.',
            'id.min'                   => 'El RUC no puede ser menor a :min caracteres.',

            'razonsocial.required'     => 'Agrega la razón social.',
            'razonsocial.max'          => 'La razon social no puede ser mayor a :max caracteres.',

            'nombrecomercial.required' => 'Agrega el nombre comercial.',
            'nombrecomercial.max'      => 'El nombre comercial no puede ser mayor a :max caracteres.',

            'apellido2.required'       => 'Agrega el segundo apellido.',
            'apellido2.max'            => 'El segundo apellido no puede ser mayor a :max caracteres.',

            'ciudad.required'          => 'Agrega la ciudad.',
            'ciudad.max'               => 'La ciudad no puede ser mayor a :max caracteres.',

            'direccion.required'       => 'Agrega la direccion.',
            'direccion.max'            => 'La direccion no puede ser mayor a :max caracteres.',

            'email.required'           => 'Agrega el Email.',
            'email.max'                => 'El Email no puede ser mayor a :max caracteres.',
            'email.email'              => 'El Email tiene error en el formato.',

            'telefono1.required'       => 'Agrega el teléfono domicilio del usuario.',
            'telefono1.max'            => 'El teléfono domicilio del usuario no puede ser mayor a 10 caracteres.',
            'telefono1.numeric'        => 'El telefono domicilio del usuario debe ser numerico.',
            'telefono2.required'       => 'Agrega el teléfono celular del usuario.',
            'telefono2.max'            => 'El teléfono celular del usuario no puede ser mayor a 10 caracteres.',
            'telefono2.numeric'        => 'El telefono cellular del usuario debe ser numérico.',

            'estado.required'          => 'Agrega el estado.',

        ];
        $constraints = [
            'id'              => 'required|max:13|min:13|unique:empresa,id,' . $id,
            'razonsocial'     => 'required|max:255',
            'nombrecomercial' => 'required|max:255',
            'ciudad'          => 'required|max:60',
            'direccion'       => 'required|max:255',
            'email'           => 'required|email|max:191',
            'telefono1'       => 'required|numeric|max:9999999999',
            'telefono2'       => 'required|numeric|max:9999999999',
            'estado'          => 'required',

        ];

        $input = [
            'id'              => $request['id'],
            'razonsocial'     => strtoupper($request['razonsocial']),
            'nombrecomercial' => strtoupper($request['nombrecomercial']),
            'ciudad'          => strtoupper($request['ciudad']),
            'direccion'       => strtoupper($request['direccion']),
            'email'           => $request['email'],
            'telefono1'       => $request['telefono1'],
            'telefono2'       => $request['telefono2'],
            'electronica'     => $request['electronica'],
            'appid'           => $request['appid'],
            'appsecret'       => $request['appsecret'],
            'url'             => $request['url'],
            'establecimiento' => $request['establecimiento'],
            'punto_emision'   => $request['punto_emision'],

            'ip_modificacion' => $ip_cliente,

            'id_usuariomod'   => $idusuario,

        ];

        $this->validate($request, $constraints, $mensajes);

        Empresa::where('id', $id)
            ->update($input);

        return redirect()->intended('/empresa');
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

        $empresas = $this->doSearchingQuery($constraints);

        return view('empresa/index', ['empresas' => $empresas, 'searchingVals' => $constraints]);
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

}
