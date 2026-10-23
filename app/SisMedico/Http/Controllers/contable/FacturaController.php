<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Ct_Clientes;
use Sis_medico\Empresa;
use Sis_medico\Factura_Cabecera;
use Sis_medico\Factura_Informacion_Factura;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\User;

class FacturaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22)) == false) {
            return true;
        }
    }

    public function empresas()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $empresas = Empresa::where('id', '<>', '9999999999')->get();

        return view('contable/facturacion/empresa', ['empresas' => $empresas]);
    }

    public function empresa_editar($id)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $empresa = Empresa::find($id);
        //dd($empresa);

        return view('contable/facturacion/editar_empresa', ['empresa' => $empresa]);
    }

    public function facturas($id)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $desde    = date('Y-m-d');
        $hasta    = date('Y-m-d');
        $empresa  = Empresa::find($id);
        $empresas = Empresa::where('id', '<>', '9999999999')->where('id', '<>', $id)->get();
        //$facturas = Factura_Cabecera::where('id_empresa',$id)->paginate(20);
        $facturas = Factura_Cabecera::where('id_empresa', $id)
            ->whereBetween('fecha_emision', [$desde, $hasta])->paginate(20);
        $seguros = Seguro::where('inactivo', '1')->get();

        return view('contable/facturacion/index', ['facturas' => $facturas, 'empresa' => $empresa, 'empresas' => $empresas, 'seguros' => $seguros, 'cedula' => null, 'nombres' => null, 'factura' => null, 'id_seguro' => null, 'suc' => null, 'caj' => null, 'desde' => $desde, 'hasta' => $hasta]);
    }

    public function factura_crear($id, $id_cliente, $id_empresa, $id_suc, $id_caj, $id_factura)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $empresa = Empresa::find($id_empresa);

        $seguros = Seguro::where('inactivo', '1')->get();

        $paciente = Paciente::find($id);

        $ct_cliente = Ct_Clientes::where('identificacion', $id_cliente)->first();

        return view('contable/facturacion/crear_factura', ['empresa' => $empresa, 'seguros' => $seguros, 'paciente' => $paciente, 'ct_cliente' => $ct_cliente, 'id' => $id, 'id_cliente' => $id_cliente, 'id_suc' => $id_suc, 'id_caj' => $id_caj, 'id_factura' => $id_factura]);

    }

    public function factura_store(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;

        $rules = [
            'suc'        => 'required',
            'caj'        => 'required',
            'factura'    => 'required',
            'idpaciente' => 'required',
            'cedula'     => 'required',
        ];

        $msn = [
            'suc.required'        => 'Ingresar Sucursal',
            'caj.required'        => 'Ingresar Caja',
            'factura.required'    => 'Ingresar Factura',
            'idpaciente.required' => 'Ingresar Cédula Paciente',
            'cedula.required'     => 'Ingresar Ruc o Cédula  del Cliente',
        ];

        $this->validate($request, $rules, $msn);

        //Guardar en la Tabla factura_informacion_factura
        $input_factura_informacion = [
            'ruc_cedula'      => $request['cedula'],
            'razon_social'    => $request['razon_social'],
            'ciudad'          => $request['ciudad'],
            'direccion'       => $request['direccion'],
            'telefono1'       => $request['telefono'],
            'email'           => $request['email'],
            'id_usuariocrea'  => $id_usuario,
            'id_usuariomod'   => $id_usuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        $id_factura_informacion = Factura_Informacion_Factura::insertGetId($input_factura_informacion);

        if (!is_null($id_factura_informacion)) {

            $input_factura_cabecera = [
                'id_empresa'              => $request['empresa'],
                'id_factura_info_factura' => $id_factura_informacion,
                'id_paciente'             => $request['idpaciente'],
                'sucursal'                => $request['suc'],
                'caja'                    => $request['caj'],
                'numero'                  => $request['factura'],
                'direccion'               => $request['direccion'],
                'telefono1'               => $request['telefono'],
                'email'                   => $request['email'],
                'id_seguro'               => $request['id_seguro'],
                'fecha_emision'           => date('Y-m-d H:i:s'),
                'fecha_autorizacion'      => date('Y-m-d H:i:s'),
                'fecha_procedimiento'     => date('Y-m-d H:i:s'),
                'descuento'               => '0',
                'recargo'                 => '0',
                'subtotal'                => '10',
                'base_imponible'          => '10',
                'tarifa_0'                => '10',
                'tarifa_12'               => '10',
                'iva'                     => '0',
                'impuesto_12'             => '0',
                'cantidad_items'          => '1',
                'total'                   => '10',
                'id_usuariocrea'          => $id_usuario,
                'id_usuariomod'           => $id_usuario,
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
            ];

            $id_factura_cabecera = Factura_Cabecera::insertGetId($input_factura_cabecera);

        }

        /*
        $factura_nro = (int)$request->factura;
        //Valida factura unica
        $factura_uni = $this->existe_factura($request->suc,$request->caj,$factura_nro);

        $rules = [
        'factura' => 'comparamayor:'.$factura_uni.',1',
        ];

        $msn = [

        'factura.comparamayor' => 'Factura ya ingresada'
        ];

        return $this->validate($request, $rules, $msn);*/

        return "ok";
    }

    private function existe_factura($suc, $caj, $factura)
    {

        $cantidad = Factura_Cabecera::where('sucursal', $suc)->where('caja', $caj)->where('numero', $factura)->get()->count();
        if ($cantidad > 0) {
            return 1;
        }

        return 0;

    }

    public function crear($id, $id_cliente, $id_empresa, $id_suc, $id_caj, $id_factura)
    {

        //return($id_cliente);

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $empresa = Empresa::find($id_empresa);

        $seguros = Seguro::where('inactivo', '1')->get();

        $paciente = Paciente::find($id);

        $ct_cliente = Ct_Clientes::where('identificacion', $id_cliente)->first();

        return view('contable/facturacion/crear_factura', ['empresa' => $empresa, 'seguros' => $seguros, 'paciente' => $paciente, 'ct_cliente' => $ct_cliente, 'id' => $id, 'id_cliente' => $id_cliente, 'id_suc' => $id_suc, 'id_caj' => $id_caj, 'id_factura' => $id_factura]);

    }

    public function factura_buscar(Request $request)
    {
        //return $request->all();
        /*$facturas = DB::table('factura_cabecera as fc')->where('fc.id_empresa',$id_empresa)
        ->whereBetween('fecha_emision', [$fecha1, $fecha2])
        ->select('fc.fecha_emision','fc.id_paciente','fc.sucursal','fc.caja','fc.numero','fc.total')->paginate(20);
         */

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request['empresa'];

        //Buscar por los siguientes campos
        $fecha1    = $request['desde'];
        $fecha2    = $request['hasta'];
        $id_cedula = $request['cedula'];
        $nombres   = $request['nombres'];
        //$nombres = $request['nombres'];
        $id_seguro  = $request['id_seguro'];
        $id_suc     = $request['suc'];
        $id_caj     = $request['caj'];
        $id_factura = $request['factura'];

        $nombres_sql = '';

        if ($fecha1 == null) {
            $fecha1 = Date('Y-m-d');
        }

        if ($fecha2 == null) {
            $fecha2 = Date('Y-m-d');
        }

        //$desde = date('Y-m-d');
        //$hasta = date('Y-m-d');
        //$empresa = Empresa::find($id_empresa);
        //$empresas = Empresa::where('id','<>','9999999999')->where('id','<>',$id_empresa)->get();
        //$seguros = Seguro::where('inactivo','1')->get();

        //BUSCAR FACTURAS POR FECHAS
        if ($fecha1 != null || $fecha2 != null) {

            $facturas = Factura_Cabecera::where('id_empresa', $id_empresa)
                ->whereBetween('fecha_emision', [$fecha1, $fecha2])->paginate(20);

        }

        //BUSCAR FACTURA POR CEDULA
        if (!is_null($id_cedula)) {

            $facturas = Factura_Cabecera::where('id_empresa', $id_empresa)
                ->where('id_paciente', $id_cedula)->paginate(20);
        }

        //BUSCAR FACTURA POR NOMBRE
        if ($nombres != null) {

            $pacientes = DB::table('paciente as p')
                ->leftjoin('historiaclinica as h', 'h.id_paciente', 'p.id')
                ->leftjoin('agenda as a', 'h.id_agenda', 'a.id')
                ->groupBy('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.estado_cita');

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }

            $nombres_sql = $nombres_sql . '%';

            if ($cantidad > '1') {
                $pacientes = $pacientes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });

            } else {

                $pacientes = $pacientes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });

            }

            $pacientes = $pacientes->get();

            foreach ($pacientes as $pac) {

                $facturas = Factura_Cabecera::where('id_empresa', $id_empresa)
                    ->where('id_paciente', $pac->id)->paginate(20);
            }

            /*$facturas = Factura_Cabecera::where('id_empresa',$id_empresa)
        ->where('id_paciente',$pacientes->id)->paginate(20);*/
        }

        //BUSCAR FACTURA POR SEGURO
        if (!is_null($id_seguro)) {

            $facturas = Factura_Cabecera::where('id_empresa', $id_empresa)
                ->where('id_seguro', $id_seguro)->paginate(20);
        }

        //BUSCAR FACTURA POR SUCURSAL, CAJA, NUMERO FACTURA
        if ((!is_null($id_suc)) && (!is_null($id_caj)) && (!is_null($id_factura))) {

            $facturas = Factura_Cabecera::where('id_empresa', $id_empresa)
                ->where('sucursal', $id_suc)
                ->where('caja', $id_caj)
                ->where('numero', $id_factura)->paginate(20);
        }

        /*return view('contable/facturacion/index', [ 'facturas' => $facturas, 'empresa' => $empresa, 'empresas' => $empresas, 'seguros' => $seguros, 'cedula' => null, 'nombres' => null, 'factura' => null, 'id_seguro' => null, 'suc' => null, 'caj' => null, 'desde' => $desde, 'hasta' => $hasta]);*/

        return view('contable/facturacion/busqueda_factura', ['facturas' => $facturas]);

    }

}
