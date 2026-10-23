<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_detalle_retenciones;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Compras;
use Sis_medico\Ct_factura_contable;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Empresa;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;
use Sis_medico\Contable;
use Sis_medico\Validate_Decimals;
use Sis_medico\Retenciones;
use Sis_medico\Ct_Cliente_Retencion;
use Sis_medico\Ct_Detalle_Cliente_Retencion;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_Sucursales;
use Sis_medico\User;
use Sis_medico\Log_Contable;
use Sis_medico\LogAsiento;
use Sis_medico\Ct_Detalle_Comprobante_Ingreso;



class ClientesRetencionesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa    = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $configuracion = Ct_Configuraciones::all();
        $retenciones   = Ct_Cliente_Retencion::leftjoin('ct_clientes as c', 'c.identificacion', 'ct_cliente_retencion.id_cliente')
            ->join('ct_ventas as v', 'v.id', 'ct_cliente_retencion.id_factura')
            ->select('ct_cliente_retencion.*', 'ct_cliente_retencion.created_at', 'v.nro_comprobante as n', 'ct_cliente_retencion.fecha', 'ct_cliente_retencion.descripcion', 'ct_cliente_retencion.secuencia', 'ct_cliente_retencion.valor_fuente', 'c.nombre', 'ct_cliente_retencion.valor_iva', 'ct_cliente_retencion.estado')
            ->where('ct_cliente_retencion.id_empresa', $id_empresa)
            ->orderby('ct_cliente_retencion.id', 'desc')
            ->paginate(10);
        //dd($retenciones);
        $clientes = Ct_Clientes::all();
        $tipo_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        return view('contable/clientes_retenciones/index', ['retenciones' => $retenciones, 'tipo_comprobante' => $tipo_comprobante, 'clientes' => $clientes, 'empresa' => $empresa]);
    }
    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $tipo_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $id_empresa    = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $constraints = [
            'ct_cliente_retencion.id'                    => $request['id'],
            'ct_cliente_retencion.id_cliente'            => $request['id_cliente'],
            'v.nro_comprobante'                          => $request['nro_comprobante'],
            'ct_cliente_retencion.descripcion'           => $request['detalle'],
            'ct_cliente_retencion.secuencia'             => $request['secuencia_f'],
            'ct_cliente_retencion.fecha'                 => $request['fecha'],
            'ct_cliente_retencion.id_usuariocrea'        => $request['fac_crea'],
        ];
        //dd($constraints);
        // $retenciones = Users::where('nombre1', 'LIKE', '%' . $request['fac_crea'] . '%')->select('users.id_usuariocrea as id');

        $clientes = Ct_Clientes::all();
        $retenciones = $this->doSearchingQuery($constraints, $request);
        return view('contable/clientes_retenciones/index', ['retenciones' => $retenciones, 'tipo_comprobante' => $tipo_comprobante, 'clientes' => $clientes, 'empresa' => $empresa, 'searchingVals' => $constraints]);
    }

    /*************************************************
     ******************CONSULTA QUERY******************
    /*************************************************/



    public function buscaridUser(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $retenciones = [];
        //dd('hola');
        $raw = "";

        $nombres = $request->search;

        if ($nombres != null) {
            $retenciones = User::where('estado', 1);

            $retenciones = $retenciones->where(function ($jq1) use ($nombres) {
                $jq1->orwhereraw('CONCAT(nombre1," ",nombre2," ",apellido1," ",apellido2) LIKE ?', ['%' . $nombres . '%'])
                    ->orwhereraw('CONCAT(nombre1," ",apellido1," ",apellido2) LIKE ?', ['%' . $nombres . '%']);
            });
        }

        $retenciones = $retenciones->select('id as id', DB::raw('CONCAT(nombre1," ", apellido1, " ", apellido2) AS text'))->get();
        return response()->json($retenciones);
    }
    private function doSearchingQuery($constraints, Request $request)
    {
        $id_empresa     = $request->session()->get('id_empresa');
        //if(Auth::user()->id == "0957258056"){
        $retenciones   = Ct_Cliente_Retencion::leftjoin('ct_clientes as c', 'c.identificacion', 'ct_cliente_retencion.id_cliente')
            ->join('ct_ventas as v', 'v.id', 'ct_cliente_retencion.id_factura')
            //->select('ct_cliente_retencion.*', 'ct_cliente_retencion.created_at', 'v.nro_comprobante as n', 'ct_cliente_retencion.fecha', 'ct_cliente_retencion.descripcion', 'ct_cliente_retencion.secuencia', 'ct_cliente_retencion.valor_fuente', 'c.nombre', 'ct_cliente_retencion.valor_iva', 'ct_cliente_retencion.estado')
            ->where('ct_cliente_retencion.id_empresa', $id_empresa);
        // ->orderby('ct_cliente_retencion.id', 'desc')
        // ->paginate(10);

        foreach ($constraints as $key => $value) {
            if (!is_null($value)) {
                $retenciones = $retenciones->where($key, $value);
            }
        }

        $query = $retenciones->select(
            'ct_cliente_retencion.*',
            'ct_cliente_retencion.created_at',
            'v.nro_comprobante as n',
            'ct_cliente_retencion.fecha',
            'ct_cliente_retencion.descripcion',
            'ct_cliente_retencion.secuencia',
            'ct_cliente_retencion.valor_fuente',
            'c.nombre',
            'ct_cliente_retencion.valor_iva',
            'ct_cliente_retencion.estado'
        )
            ->orderBy('ct_cliente_retencion.id', 'DESC')->paginate(10);

        return $query;
        //}



        // $query  = DB::table('ct_cliente_retencion as ct_c')->join('ct_clientes as c', 'c.identificacion', 'ct_c.id_cliente')
        //     ->join('ct_ventas as v', 'v.id', 'ct_c.id_factura')
        //     ->where('ct_c.id_empresa', $id_empresa)
        //   ->select('ct_c.*','ct_c.id', 'c.nombre','c.nombre', 'ct_c.descripcion', 'v.nro_comprobante', 'ct_c.id_cliente', 'ct_c.created_at', 'ct_c.fecha', 'ct_c.secuencia', 'ct_c.id_usuariocrea as id_usuariocrea', 'ct_c.valor_fuente', 'ct_c.valor_iva', 'ct_c.estado');

        //     $fields         = array_keys($constraints);

        // $index          = 0;

        // foreach ($constraints as $constraint) {
        //     if ($constraint != null) {
        //         $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
        //     }

        //     $index++;
        // }

        // return $query->orderBy('id', 'DESC')->paginate(10);
    }
    public function anular($id, Request $request)
    {
        //dd($request);
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = Auth::user()->id;
        $retenciones = Ct_Cliente_Retencion::where('id', $id)->first();
        $detalle = Ct_Detalle_Cliente_Retencion::where('id_cliente_retencion', $id)->get();
        $asiento = Ct_Asientos_Cabecera::findorfail($retenciones->id_asiento_cabecera);
        //dd($asiento);
        $asiento->estado = 1;
        $asiento->id_usuariomod = $idusuario;
        $asiento->save();
        $detalles = $asiento->detalles;
        $concepto = $request['concepto'];


        $id_empresa = $request->session()->get('id_empresa');
        DB::beginTransaction();
        try {
            if ($retenciones != null || $retenciones != '[]') {
                $input_cab = [
                    'estado'              => '0',
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariomod'       => $idusuario,
                ];
                $retenciones->update($input_cab);
                $compra = Ct_ventas::where('id', $retenciones->id_factura)->first();

                if ($compra != null || $compra != '[]') {
                    foreach ($detalle as $vas) {
                        $totax = $compra->valor_contable + $vas->totales;
                        $input_da = [
                            'valor_contable'      => $totax,
                            'estado_pago'         => '1',
                            'ip_modificacion'     => $ip_cliente,
                            'id_usuariomod'       => $idusuario,
                        ];
                        //dd($input_da);
                        $compra->update($input_da);
                    }
                    $recalculo = Contable::recovery_price($compra->id, 'V');
                }
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => $concepto,
                    'fecha_asiento'   => $asiento->fecha_asiento,
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $retenciones->secuencia,
                    'valor'           => $asiento->valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
                foreach ($detalles as $value) {
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento,
                        'id_plan_cuenta'      => $value->id_plan_cuenta,
                        'debe'                => $value->haber,
                        'haber'               => $value->debe,
                        'descripcion'         => $value->descripcion,
                        'fecha'               => $asiento->fecha_asiento,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,

                    ]);
                }
                LogAsiento::anulacion("CL-R", $id_asiento, $asiento->id);
            }

            DB::commit();
            return 'ok';
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }


        //return redirect()->route('retenciones_index');
    }
    public function autocompletar_cliente(Request $request)
    {
        $codigo = $request['term'];
        $data = null;
        $nuevo_nombre = explode(' ', $codigo);
        $seteo = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT identificacion, CONCAT_WS(' ',nombre) as completo
                  FROM `ct_clientes`
                  WHERE CONCAT_WS(' ',nombre) like '" . $seteo . "' 
                  ";
        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo, 'identificacion' => $product->identificacion);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function crear(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $divisas = Ct_Divisas::all();
        $rfir    = DB::table('ct_porcentaje_retenciones')->where('tipo', '2')->get();
        //dd($rfir);
        $rfiva      = DB::table('ct_porcentaje_retenciones')->where('tipo', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        //dd($divisas);
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $clientes = Ct_Clientes::all();
        return view('contable/clientes_retenciones/create', ['divisas' => $divisas, 'rfir' => $rfir, 'rfiva' => $rfiva, 'empresa' => $empresa, 'clientes' => $clientes, 'sucursales' => $sucursales]);
    }
    public function autcom_fc(Request $request)
    {
        return "hlola";
    }
    public function modal_retenciones(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd($request->all());
        $id_proveedor = $request['id_proveedor'];
        $id_compra    = 0;
        if (isset($request['id_compra'])) {
            $id_compra = $request['id_compra'];
        }
        $id_fact_contable = 0;
        if (isset($request['id_fact_contable'])) {
            $id_fact_contable = $request['id_fact_contable'];
        }
        $secuencia_factura = $request['secuencia'];
        $proveedor = Proveedor::where('id', $id_proveedor)->first();
        $consulta_cabecera = Ct_Asientos_Cabecera::where('fact_numero', $secuencia_factura)->first();
        //dd($consulta_cabecera);
        $divisas = Ct_Divisas::all();
        $rfir = DB::table('ct_porcentaje_retenciones')->where('tipo', '2')->get();
        $rfiva = DB::table('ct_porcentaje_retenciones')->where('tipo', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        //dd($proveedor);
        return view('contable/retenciones/modal_retenciones', ['id_compra' => $id_compra, 'proveedor' => $proveedor, 'id_fact_contable' => $id_fact_contable, 'secuencia_factura' => $secuencia_factura, 'consulta_cabecera' => $consulta_cabecera, 'rfir' => $rfir, 'rfiva' => $rfiva, 'divisas' => $divisas, 'id_empresa' => $id_empresa]);
    }
    public function buscar_codigo(Request $request)
    {
        $id_factura = $request['id_factura'];
        $id_empresa = $request->session()->get('id_empresa');
        $data       = null;
        $productos = '[]';
        $deudas = '[]';
        $productos = DB::table('ct_asientos_detalle as a')
            ->join('ct_asientos_cabecera as c', 'c.id', 'a.id_asiento_cabecera')
            ->join('ct_ventas as ve', 've.id_asiento', 'c.id')
            ->leftjoin('ct_cliente_retencion as r', 've.id', 'r.id_factura')
            ->join('ct_clientes as cl', 'cl.identificacion', 've.id_cliente')
            ->where('ve.nro_comprobante', $id_factura)
            ->where('ve.id_empresa', $id_empresa)
            ->where('ve.estado', '<>', '0')
            ->where('ve.tipo', '<>', 'VEN-FACT')
            ->select(
                've.id_cliente',
                'cl.nombre',
                'cl.direccion_representante',
                'a.id',
                'a.descripcion',
                'cl.nombre',
                've.fecha',
                'cl.tipo',
                'r.estado as rete',
                'c.observacion',
                'c.fecha_asiento',
                '.c.valor',
                've.numero',
                've.id',
                'c.fact_numero',
                've.nro_autorizacion',
                've.nro_comprobante',
                've.subtotal_0',
                've.subtotal_12',
                've.impuesto',
                've.base_imponible',
                've.id as venta_id',
                'iva_total'
            )->get();
        $deudas = '[]';
        if (isset($productos[0]->id_cliente)) {
            $deudas = DB::table('ct_asientos_cabecera as c')
                ->join('ct_ventas as ve', 've.id_asiento', 'c.id')
                ->join('ct_clientes as cli', 'cli.identificacion', 've.id_cliente')
                ->leftjoin('ct_cliente_retencion as r', 've.id', 'r.id_factura')
                ->where('ve.id_cliente', $productos[0]->id_cliente)
                ->where('ve.estado', '<>', '0')
                ->where('ve.tipo', '<>', 'VEN-FACT')
                ->where('ve.nro_comprobante', $id_factura)
                ->where('ve.id_empresa', $id_empresa)
                ->select('ve.total_final as valor', 've.tipo', 'c.fact_numero', 'c.observacion', 'c.fecha_asiento', 'cli.identificacion', 've.nro_comprobante', 've.impuesto', 've.total_final', 've.procedimientos')
                ->orderby('ve.numero')
                ->get();
        }
        if ($productos != '[]') {
            $data = [];
            if ($productos[0]->rete == null) {
                $data = [
                    $productos[0]->id_cliente,
                    $productos[0]->id,
                    $productos[0]->nombre,
                    $productos[0]->direccion_representante,
                    $productos[0]->descripcion,
                    $productos[0]->nombre,
                    $productos,
                    $productos[0]->tipo,
                    $productos[0]->observacion,
                    $productos[0]->fecha_asiento,
                    $productos[0]->valor,
                    $productos[0]->numero,
                    $productos[0]->impuesto,
                    $productos[0]->iva_total,
                    $productos[0]->id,
                    $productos[0]->nro_comprobante,
                    $deudas,
                    $productos[0]->nro_autorizacion,
                    $productos[0]->subtotal_12,
                    $productos[0]->venta_id
                ];
            } else {
                if ($productos[0]->rete != 1) {
                    $data = [
                        $productos[0]->id_cliente,
                        $productos[0]->id,
                        $productos[0]->nombre,
                        $productos[0]->direccion_representante,
                        $productos[0]->descripcion,
                        $productos[0]->nombre,
                        $productos,
                        $productos[0]->tipo,
                        $productos[0]->observacion,
                        $productos[0]->fecha_asiento,
                        $productos[0]->valor,
                        $productos[0]->numero,
                        $productos[0]->impuesto,
                        $productos[0]->iva_total,
                        $productos[0]->id,
                        $productos[0]->nro_comprobante,
                        $deudas,
                        $productos[0]->nro_autorizacion,
                        $productos[0]->subtotal_12,
                        $productos[0]->venta_id
                    ];
                } else {
                    return ['value' => 'no resultados'];
                }
            }
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public function codigo(Request $request)
    {
        $codigo = $request['term'];
        $validacion = $request['tipo'];
        // if($validacion!=1){
        $data       = array();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $productos  = DB::table('ct_ventas')->where('nro_comprobante', 'like', '%' . $codigo . '%')->where('id_empresa', $id_empresa)->where('estado_pago', '0')->get();
        //sdd($productos);
        foreach ($productos as $product) {
            $data[] = array('value' => $product->nro_comprobante, 'tipo' => 'FACTURA');
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
        // }
        return 'no';
    }
    public function query_cuentas(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $codigo = $request['opcion'];
        $tipo = $request['tipo'];
        if (!is_null($codigo)) {
            $data = DB::table('ct_porcentaje_retenciones')->where('id', $codigo)->where('tipo', $tipo)->get();
            return $data;
        }
        return 'no';
    }
    public function query_cuentas2(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $codigo = $request['opcion'];
        if (!is_null($codigo)) {
            $data = DB::table('ct_porcentaje_retenciones')->where('id', $codigo)->get();
            return $data;
        }
        return 'no';
    }
    public function store(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $objeto_validar = new Validate_Decimals();
        $total_final = $request['retencion_total'];
        $numero_factura = 0;
        $consulta_plan_debe = 0;
        $contador_ctv = DB::table('ct_cliente_retencion')->get()->count();
        if ($contador_ctv == 0) {
            //return 'No Retorno nada';
            $num = '1';
            $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
        } else {
            $max_id = DB::table('ct_cliente_retencion')->max('id');
            if (($max_id >= 1) && ($max_id < 10)) {
                $nu = $max_id + 1;
                $numero_factura  = str_pad($nu, 9, "0", STR_PAD_LEFT);
            }
            if (($max_id >= 10) && ($max_id <= 99)) {
                $nu = $max_id + 1;
                $numero_factura  = str_pad($nu, 9, "0", STR_PAD_LEFT);
            }
            if (($max_id >= 100) && ($max_id < 1000)) {
                $nu = $max_id + 1;
                $numero_factura  = str_pad($nu, 9, "0", STR_PAD_LEFT);
            }
            if ($max_id == 1000) {
                $numero_factura  = $max_id;
            }
        }
        $id_empresa = $request->session()->get('id_empresa');
        $total_fuente = $objeto_validar->set_round($request['valor_fuente']);
        $total_iva = $objeto_validar->set_round($request['valor_iva']);
        $total_t = $objeto_validar->set_round($request['valor_retenido']);
        DB::beginTransaction();
        try {
            $cabeceraa = [
                'observacion' => 'Retencion: ' . $numero_factura . " " . $request['concepto'],
                'fecha_asiento' => $request['fecha_aut'],
                'fact_numero' => $numero_factura,
                'valor' => $total_final,
                'estado' => '2',
                'id_empresa' => $id_empresa,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
            $input = [
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_cliente' => $request['cliente'],
                'autorizacion' => $request['nro_autorizacion'],
                'id_factura' => $request['id_venta'],
                'fecha' => $request['fecha_aut'],
                'nro_comprobante' => $numero_factura,
                'valor_fuente' => $total_fuente,
                'valor_iva' => $total_iva,
                'id_empresa' => $id_empresa,
                'tipo' => '1',
                'id_tipo' => $request['porcentaje_retencionf'],
                'descripcion' => $request['concepto'],
                'estado' => '1',
                'total' => $total_t,
                'secuencia' => $numero_factura,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ];
            $id_retenciones = Ct_Cliente_Retencion::insertGetId($input);
            for ($i = 1; $i < $request['cont']; $i++) {
                Ct_Detalle_Cliente_Retencion::create([
                    'id_cliente_retencion' => $id_retenciones,
                    'observacion' => $request['concepto'],
                    'numerorefs' => $request['numerorefs' . $i],
                    'fechaauto' => $request['fechauto' . $i],
                    'id_tipo' => $request['porcentaje_retencion' . $i],
                    'tipo' => $request['tipor' . $i],
                    'id_porcentaje' => $request['id_porcentaje' . $i],
                    'codigo' => $request['codigor' . $i],
                    'base_imponible' => $request['base_imp' . $i],
                    'estado' => '1',
                    'totales' => $request['valor_retenido' . $i],
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                ]);
            }
            $factura = Ct_ventas::where('id', $request['id_venta'])->where('estado', '<>', '0')->first();
            $valord = $request['retencion_total'];
            $valor_total_compra = $factura->valor_contable - $valord;
            $valor_total_compras = $valor_total_compra;
            $data_update = [
                'saldo' => $valor_total_compras,
                'valor_contable' => $valor_total_compras,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'estado_pago' => '2',
            ];
            $factura->update($data_update);
            $cta_x_cob_cli = Ct_Configuraciones::where('id', 5)->first();
            $consulta_nombre = DB::table('ct_porcentaje_retenciones')->where('id', $request['porcentaje_retencionf'])->first();
            $consulta_retenciones = Ct_Detalle_Cliente_Retencion::where('id_cliente_retencion', $id_retenciones)->where('estado', '1')->get();
            $nombre_cuenta = $consulta_nombre->nombre;
            $valorsf = $objeto_validar->set_round($request['retencion_total']);
            foreach ($consulta_retenciones as $value) {
                if ($value->codigo != null && $value->id_porcentaje != null) {
                    if ($value->tipo == 'RENTA') {
                        $consulta_plan_debe  = DB::table('ct_porcentaje_retenciones')->where('id', $value->id_porcentaje)->where('codigo', $value->codigo)->where('tipo', '=', '2')->first();
                        $valors = $objeto_validar->set_round($value->totales);
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta' => $consulta_plan_debe->cuenta_deudora,
                            'descripcion' => $consulta_plan_debe->nombre,
                            'fecha' => $request['fecha_aut'],
                            'debe' => $valors,
                            'haber' => '0',
                            'estado' => '1',
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'ip_creacion' => $idusuario,
                            'ip_modificacion' => $idusuario,
                        ]);
                    }
                    if ($value->tipo == 'IVA') {
                        $consulta_plan_debe  = DB::table('ct_porcentaje_retenciones')->where('id', $value->id_porcentaje)->where('codigo', $value->codigo)->where('tipo', '=', '1')->first();
                        $valors = $objeto_validar->set_round($value->totales);
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta' => $consulta_plan_debe->cuenta_deudora,
                            'descripcion' => $consulta_plan_debe->nombre,
                            'fecha' => $request['fecha_aut'],
                            'debe' => $valors,
                            'haber' => '0',
                            'estado' => '1',
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'ip_creacion' => $idusuario,
                            'ip_modificacion' => $idusuario,
                        ]);
                    }
                }
            }
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta' => $cta_x_cob_cli->id_plan,
                'descripcion' => $cta_x_cob_cli->cuenta->nombre,
                'fecha' => $request['fecha_aut'],
                'debe' => '0',
                'haber' => $valorsf,
                'estado' => '1',
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $idusuario,
                'ip_modificacion' => $idusuario,
            ]);
            DB::commit();
            return $id_retenciones;
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }
    public function edit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $retenciones = Ct_Cliente_Retencion::where('id', $id)->first();
        //dd($retenciones);
        //$compras= Ct_compras::where('id',$retenciones->id_compra)->first();
        //dd($compras);
        $detalles = Ct_Detalle_Cliente_Retencion::where('id_cliente_retencion', $id)->get();
        //dd($detalles);
        $divisas = Ct_Divisas::all();
        $rfir = DB::table('ct_porcentaje_retenciones')->where('tipo', '2')->get();
        $rfiva = DB::table('ct_porcentaje_retenciones')->where('tipo', '1')->get();
        $id_empresa             = $request->session()->get('id_empresa');

        $empresa = Empresa::where('id', $id_empresa)->first();

        return view('contable/clientes_retenciones/edit', ['retenciones' => $retenciones, 'rfir' => $rfir, 'rfiva' => $rfiva, 'divisas' => $divisas, 'detalles' => $detalles, 'empresa' => $empresa, 'id' => $id]);
    }
    public function imprimir_comprobante_retenciones($id, Request $request)
    {
        $retenciones_all        = Ct_Cliente_Retencion::find($id);
        $retenciones_det_all    = Ct_Detalle_Cliente_Retencion::where('id_cliente_retencion', $id)->orderBy('id', 'DESC')->get();
        $id_empresa             = $request->session()->get('id_empresa');
        //dd($retenciones_all);
        $sucursal               = DB::table('ct_caja as c')
            ->join('ct_sucursales as co', 'c.id_sucursal', 'co.id')
            ->where('co.id_empresa', $id_empresa)
            ->select('co.codigo_sucursal', 'c.codigo_caja')
            ->orderby('co.id', 'desc')
            ->get();
        $empresa1 = null;
        $empresa2 = null;
        $proveedor1 = 0;
        $proveedor2 = 0;
        $factura            = DB::table('factura_cabecera')->where('id', $retenciones_all->id_factura)->first();
        $asiento_cabecera   = DB::table('ct_asientos_cabecera')->where('id', $retenciones_all->id_asiento_cabecera)->first();
        $retenciones_detalle = DB::table('ct_detalle_cliente_retencion')->where('id_cliente_retencion', $retenciones_all->id)->get();
        if ($factura != null) {
            $empresa1 = DB::table('empresa')->where('id', $factura->id_empresa)->first();
            $proveedor1 = DB::table('ct_clientes')->where('identificacion', $factura->id_paciente)->first();
        }
        $iva = 0;
        //dd($retenciones_detalle);
        $consulta_iva = 0;
        $consulta_renta = 0;
        $renta1 = 0;
        $renta2 = 0;
        $consulta_renta = DB::table('ct_detalle_cliente_retencion')->where('tipo', 'RENTA')->where('id_cliente_retencion', $retenciones_all->id)->get();
        //dd($consulta_renta);
        $consulta_iva = DB::table('ct_detalle_cliente_retencion')->where('tipo', 'IVA')->where('id_cliente_retencion', $retenciones_all->id)->get();
        if (sizeof($consulta_renta) == 2) {
            $renta1 = DB::table('ct_porcentaje_cliente_retencion')->where('id', $consulta_renta[0]->id_tipo)->first();
            $renta2 = DB::table('ct_porcentaje_cliente_retencion')->where('id', $consulta_renta[1]->id_tipo)->first();
        } else {
            if (isset($consulta_renta[0]->id_tipo))
                $renta1 = DB::table('ct_porcentaje_cliente_retencion')->where('id', $consulta_renta[0]->id_tipo)->first();
            else
                $renta1 = 0;
        }
        //dd($empresa2);
        $iva = 0;
        if (sizeof($consulta_iva) > 0) {
            $iva = DB::table('ct_porcentaje_cliente_retencion')->where('id', $consulta_iva[0]->id_tipo)->first();
        }
        $compras = array();
        //dd($renta2);

        $retenciones = Ct_Cliente_Retencion::where('id', $id)->first();
        $detalles = Ct_Detalle_Cliente_Retencion::where('id_cliente_retencion', $id)->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();

        $vistaurl = "contable.clientes_retenciones.pdf_comprobante_retenciones";
        $view     = \View::make($vistaurl, compact('retenciones', 'detalles', 'id_empresa', 'empresa', 'empresa1', 'empresa2'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }
    public function buscar_cliente(Request $request)
    {
        //dd($request->all());
        if (!is_null($request['nombre_cliente'])) {
            $cliente  = $request['nombre_cliente'];
            $id_empresa = $request->session()->get('id_empresa');
            $registros  = Ct_Cliente_Retencion::join('ct_clientes as c', 'c.identificacion', 'cr.id_cliente')
                ->where('c.nombre', $cliente)
                ->select('cr.*')->orderby('id', 'desc')
                ->paginate(3);
            if ($registros != '[]') {
                return view('contable/clientes_retenciones/resultados_tabla', ['retenciones' => $registros]);
            } else {
                return ['value' => 'no resultados'];
            }
        } elseif (isset($request['buscar_secuencia'])) {
            $concepto   = '%' . $request['buscar_secuencia'] . '%';
            $id_empresa = $request->session()->get('id_empresa');
            $registros  = Ct_Cliente_Retencion::where('descripcion', 'like', $concepto)->orderby('id', 'desc')
                ->paginate(3);
            if ($registros != '[]') {
                return view('contable/retenciones/resultados_tabla', ['retenciones' => $registros]);
            } else {
                return ['value' => 'no resultados'];
            }
        }
        return ['value' => 'no resultados'];
    }
    public function buscar_proveedor(Request $request)
    {
        //dd($request->all());
        if (!is_null($request['nombre_proveedor'])) {
            $proveedor  = $request['nombre_proveedor'];
            $id_empresa = $request->session()->get('id_empresa');
            $registros  = DB::table('ct_retenciones as cr')
                ->join('proveedor as p', 'p.id', 'cr.id_proveedor')
                ->where('p.nombrecomercial', $proveedor)
                ->select('cr.*')->orderby('id', 'desc')
                ->paginate(3);
            if ($registros != '[]') {
                return view('contable/retenciones/resultados_tabla', ['retenciones' => $registros]);
            } else {
                return ['value' => 'no resultados'];
            }
        } elseif (isset($request['buscar_secuencia'])) {
            $concepto   = '%' . $request['buscar_secuencia'] . '%';
            $id_empresa = $request->session()->get('id_empresa');
            $registros  = Ct_Retenciones::where('descripcion', 'like', $concepto)->orderby('id', 'desc')
                ->paginate(3);
            if ($registros != '[]') {
                return view('contable/retenciones/resultados_tabla', ['retenciones' => $registros]);
            } else {
                return ['value' => 'no resultados'];
            }
        }
        return ['value' => 'no resultados'];
    }
    public function buscar_tipo(Request $request)
    {
        $id = $request['id'];
        //dd($id);
        if (!is_null($id)) {
            //0 fuente 1 iva
            if ($id == '0') {

                $consulta = DB::table('ct_porcentaje_retenciones')->where('tipo', $id)->get();
            } else {

                $consulta = DB::table('ct_porcentaje_retenciones')->where('tipo', $id)->get();
            }
            return $consulta;
        }
        return 'no data';
    }
    public function update($id, Request $request)
    {
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = Auth::user()->id;
        $retenciones = Ct_Cliente_Retencion::find($id);
        $asiento_cabecera = Ct_Asientos_Cabecera::find($retenciones->id_asiento_cabecera);

        $cabeceraa = [
            'observacion'       => $request['concepto'],
            'fecha_asiento'     => $request['fecha_aut'],
            'ip_modificacion'   => $ip_cliente,
            'id_usuariomod'     => $idusuario,
        ];
        $detalle_asiento = Ct_Asientos_Detalle::where('id_asiento_cabecera', $retenciones->id_asiento_cabecera)->get();
        foreach ($detalle_asiento as $s) {
            $details = Ct_Asientos_Detalle::find($s->id);
            $details->fecha = $request['fecha_aut'];
            $details->id_usuariomod = $idusuario;
            $details->save();
        }
        $asiento_cabecera->update($cabeceraa);
        // $id_asiento_cabecera      = 1;
        $input                      = [
            'autorizacion'        => $request['nro_autorizacion'],
            'fecha'               => $request['fecha_aut'],
            'descripcion'         => $request['concepto'],
            'ip_modificacion'     => $ip_cliente,
            'id_usuariomod'       => $idusuario,
        ];
        $retenciones->update($input);
        return redirect()->route('retenciones.clientes.edit', ['id' => $id]);
    }

    public function actualizar_fecha(Request $request)
    {


        $fecha = $request['fecha'];
        $id_asiento = $request['id_asiento'];
        $id_rentecion = $request['id_rentecion'];
        $retencion = Ct_Cliente_Retencion::where('id', $id_rentecion)->first();
        $cabeceraa = Ct_Asientos_Cabecera::where('id', $id_asiento)->first();
        $detalle = Ct_Asientos_Detalle::where('id_asiento_cabecera', $id_asiento)->get();
        $input = [

            'fecha' => $request['fecha'],
        ];
        $retencion->update($input);
        $input1 = [

            'fecha_asiento' => $request['fecha'],
        ];
        $cabeceraa->update($input1);
        foreach ($detalle as $s) {
            $details = Ct_Asientos_Detalle::find($s->id);
            $details->fecha = $request['fecha'];
            $details->save();
        }

        return  response()->json("ok");
    }
    public function create2(Request $request)
    {
        $divisas = Ct_Divisas::all();
        $rfir    = DB::table('ct_porcentaje_retenciones')->where('tipo', '2')->get();
        //dd($rfir);
        $rfiva      = DB::table('ct_porcentaje_retenciones')->where('tipo', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $clientes = Ct_Clientes::where('estado', '1')->get();
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $ventas = Ct_ventas::where('estado', '<>', 0)->where('tipo', '<>', 'VEN-FACT')->where('id_empresa', $id_empresa)->where('valor_contable', '>', 0)->get();
        return view('contable/clientes_retenciones/create_retenciones', ['divisas' => $divisas, 'clientes' => $clientes, 'ventas' => $ventas, 'rfir' => $rfir, 'rfiva' => $rfiva, 'sucursales' => $sucursales, 'empresa' => $empresa]);
    }

    public static function validarValores($id_venta, $total)
    {
        Contable::recovery_price($id_venta, 'V');
        $venta = Ct_ventas::find($id_venta);
        // $valord = (float) $total;
        // $information = Contable::recovery_by_model('O', 'V', $venta->id);
        // $busq = json_encode($information);
        // $busq = json_decode($busq, true);
        // $ingreso = ($busq["original"]["ingreso"]);
        // $comp_ingreso = Ct_Detalle_Comprobante_Ingreso::where('id_factura', $id_venta)->sum("total");
        $msj = "";
        $status = "success";

        $calcular = $venta->valor_contable - $total;
        if ($calcular < 0) {
            $msj = "Esta retencion excede el valor de la factura. EL saldo de la factura es de {$venta->valor_contable} y la valor de la retencion es de {$total}";
            $status = "error";
        }

        return ["status" => $status, "msj" => $msj, 'error' => $msj];
    }

    public function newstore(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa    = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $sucursal = $request['sucursal'];
        $punto_emision = $request['pemision'];
        $sucursal = $request['sucursal'];
        $contador_ctv = DB::table('ct_cliente_retencion')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)
            ->where('punto_emision', $punto_emision)->get()->count();
        $propiedad = $request['secuencial'];
        $numero_factura = 0;
        $actualizar_fac = Ct_ventas::find($request['id_venta']);
        $valord = (float) $request['total'];
        //dd($valord);
        $validarValores = ClientesRetencionesController::validarValores($actualizar_fac->id, $valord);
        if ($validarValores["status"] == "error") {
            return ['error' => 'si', "msj" => $validarValores["msj"]];
        }
        if (!is_null($propiedad)) {
            $numero_factura = str_pad($propiedad, 9, "0", STR_PAD_LEFT);
        } else {
            if ($contador_ctv == 0) {
                //return 'No Retorno nada';
                $num = '1';
                $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            } else {
                //Obtener Ultimo Registro de la Tabla ct_ventas
                $max_id = DB::table('ct_cliente_retencion')->where('id_empresa', $id_empresa)->latest()->first();
                $max_id = intval($max_id->secuencia);
                if (strlen($max_id) < 10) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
            }
        }
        DB::beginTransaction();
        try {
            $cabeceraa = [
                'observacion' => $request['concepto'],
                'fecha_asiento' => $request['fecha'],
                'fact_numero' => $numero_factura,
                'valor' => $request['total'],
                'estado' => '1',
                'id_empresa' => $id_empresa,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                //'estado_manual' =>5,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
            $input = [
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_cliente' => $request['id_cliente'],
                'id_factura' => $request['id_venta'],
                'autorizacion' => $request['autorizacion'],
                'nro_comprobante' => $sucursal . '-' . $request['pemision'] . '-' . $numero_factura,
                'valor_fuente' => $request['valor_renta'],
                'fecha' => $request['fecha'],
                'valor_iva' => $request['valor_iva'],
                'id_empresa' => $id_empresa,
                'sucursal' => $sucursal,
                'punto_emision' => $punto_emision,
                'tipo' => '1',
                'id_tipo' => '1',
                'descripcion' => $request['concepto'],
                'estado' => '1',
                'total' => $request['total'],
                'secuencia' => $numero_factura,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ];
            $id_retenciones = Ct_Cliente_Retencion::insertGetId($input);
            $getSri = "";
            for ($i = 0; $i < count($request['monto']); $i++) {
                if ($request['tipo_retencion'][$i] != "") {
                    $a = "RENTA";
                    if ($request['tipo_retencion'][$i] == '1') {
                        $a = "IVA";
                    }
                    Ct_Detalle_Cliente_Retencion::create([
                        'id_cliente_retencion' => $id_retenciones,
                        'observacion' => $request['concepto'],
                        'id_tipo' => $request['id_porcentaje'][$i],
                        'tipo' => $a,
                        'id_porcentaje' => $request['id_porcentaje'][$i],
                        'codigo' => $request['codigo'][$i],
                        'base_imponible' => $request['base_retencion'][$i],
                        'porcentaje' => $request['porcentaje'][$i],
                        'estado' => '1',
                        'totales' => $request['monto'][$i],
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                    ]);
                }
            }
            $actualizar_fac = Ct_ventas::find($request['id_venta']);
            $valord = (float) $request['total'];
            $valor_total_compra = $actualizar_fac->valor_contable - $valord;
            $valor_total_compras = $valor_total_compra;
            $actualizar = [
                'valor_contable' => $valor_total_compras,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'estado' => '1',
            ];
            $actualizar_fac->update($actualizar);
            $cta_x_cob_cli          = Ct_Configuraciones::where('id', 5)->first();
            $consulta_retenciones   = Ct_Detalle_Cliente_Retencion::where('id_cliente_retencion', $id_retenciones)->where('estado', '1')->get();
            $valorsf = $request['total'];
            foreach ($consulta_retenciones as $value) {
                if ($value->codigo != null && $value->id_porcentaje != null) {
                    if ($value->tipo == 'RENTA') {
                        $consulta_plan_debe  = DB::table('ct_porcentaje_retenciones')->where('id', $value->id_porcentaje)->where('codigo', $value->codigo)->where('tipo', '=', '2')->first();
                        $valors = $value->totales;
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $consulta_plan_debe->cuenta_deudora,
                            'descripcion'         => $consulta_plan_debe->nombre,
                            'fecha'               => $request['fecha'],
                            'debe'                => $valors,
                            'haber'               => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $idusuario,
                            'ip_modificacion'     => $idusuario,
                        ]);
                    }
                    if ($value->tipo == 'IVA') {
                        $consulta_plan_debe  = DB::table('ct_porcentaje_retenciones')->where('id', $value->id_porcentaje)->where('codigo', $value->codigo)->where('tipo', '=', '1')->first();
                        $valors = $value->totales;
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $consulta_plan_debe->cuenta_deudora,
                            'descripcion'         => $consulta_plan_debe->nombre,
                            'fecha'               => $request['fecha'],
                            'debe'                => $valors,
                            'haber'               => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $idusuario,
                            'ip_modificacion'     => $idusuario,
                        ]);
                    }
                }
            }
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cta_x_cob_cli->id_plan,
                'descripcion'         => $cta_x_cob_cli->cuenta->nombre,
                'fecha'               => $request['fecha'],
                'debe'                => '0',
                'haber'               => $valorsf,
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $idusuario,
                'ip_modificacion'     => $idusuario,
            ]);

            Contable::recovery_price($request['id_venta'], 'V');
            DB::commit();
            return ['id' => $id_retenciones, 'sri' => $getSri, 'error' => 'no'];
            //return $id_retenciones;
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return response()->json(['error' => 'si']);
    }
    public function newstoreSRI(Request $request)
    {
        echo '<pre>';
        print_r($request->input());
        exit;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa    = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $sucursal = $request['sucursal'];
        $punto_emision = $request['pemision'];
        $sucursal = $request['sucursal'];
        $contador_ctv = DB::table('ct_cliente_retencion')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)
            ->where('punto_emision', $punto_emision)->get()->count();
        $propiedad = $request['secuencial'];
        $numero_factura = 0;
        $actualizar_fac = Ct_ventas::find($request['id_venta']);
        $valord = (float) $request['total'];
        //dd($valord);
        $validarValores = ClientesRetencionesController::validarValores($actualizar_fac->id, $valord);
        if ($validarValores["status"] == "error") {
            return ['error' => 'si', "msj" => $validarValores["msj"]];
        }

        if (!is_null($propiedad)) {
            $numero_factura = str_pad($propiedad, 9, "0", STR_PAD_LEFT);
        } else {
            if ($contador_ctv == 0) {
                //return 'No Retorno nada';
                $num = '1';
                $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            } else {

                //Obtener Ultimo Registro de la Tabla ct_ventas
                $max_id = DB::table('ct_cliente_retencion')->where('id_empresa', $id_empresa)->latest()->first();
                $max_id = intval($max_id->secuencia);
                if (strlen($max_id) < 10) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
            }
        }
        DB::beginTransaction();
        try {

            $cabeceraa = [
                'observacion'     => $request['concepto'],
                'fecha_asiento'   => $request['fecha'],
                'fact_numero'     => $numero_factura,
                'valor'           => $request['total'],
                'estado'          => '1',
                'id_empresa'      => $id_empresa,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                //'estado_manual'   =>5,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
            $input = [
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_cliente'          => $request['id_cliente'],
                'id_factura'         => $request['id_venta'],
                'autorizacion'        => $request['autorizacion'],
                'nro_comprobante'     => $sucursal . '-' . $request['pemision'] . '-' . $numero_factura,
                'valor_fuente'        => $request['valor_renta'],
                'fecha'               => $request['fecha'],
                'valor_iva'           => $request['valor_iva'],
                'id_empresa'          => $id_empresa,
                'sucursal'            => $sucursal,
                'punto_emision'       => $punto_emision,
                'tipo'                => '1',
                'id_tipo'             => '1',
                'descripcion'         => $request['concepto'],
                'estado'              => '1',
                'total'               => $request['total'],
                'secuencia'           => $numero_factura,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];
            $id_retenciones = Ct_Cliente_Retencion::insertGetId($input);
            $getSri = "";
            for ($i = 0; $i < count($request['monto']); $i++) {
                if ($request['tipo_retencion'][$i] != "") {
                    $a = "RENTA";
                    if ($request['tipo_retencion'][$i] == '1') {
                        $a = "IVA";
                    }
                    Ct_Detalle_Cliente_Retencion::create([
                        'id_cliente_retencion'  => $id_retenciones,
                        'observacion'     => $request['concepto'],
                        'id_tipo'         => $request['id_porcentaje'][$i],
                        'tipo'            => $a,
                        'id_porcentaje'   => $request['id_porcentaje'][$i],
                        'codigo'          => $request['codigo'][$i],
                        'base_imponible'  => $request['base_retencion'][$i],
                        'porcentaje'      => $request['porcentaje'][$i],
                        'estado'          => '1',
                        'totales'         => $request['monto'][$i],
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                }
            }
            $actualizar_fac = Ct_ventas::find($request['id_venta']);
            $valord = (float) $request['total'];
            $valor_total_compra = $actualizar_fac->valor_contable - $valord;
            $valor_total_compras = $valor_total_compra;
            $actualizar = [
                'valor_contable' => $valor_total_compras,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'estado' => '1',
            ];
            $actualizar_fac->update($actualizar);

            $cta_x_cob_cli          = Ct_Configuraciones::where('id', 5)->first();
            $consulta_retenciones   = Ct_Detalle_Cliente_Retencion::where('id_cliente_retencion', $id_retenciones)->where('estado', '1')->get();
            $valorsf = $request['total'];
            foreach ($consulta_retenciones as $value) {
                if ($value->codigo != null && $value->id_porcentaje != null) {
                    if ($value->tipo == 'RENTA') {
                        $consulta_plan_debe  = DB::table('ct_porcentaje_retenciones')->where('id', $value->id_porcentaje)->where('codigo', $value->codigo)->where('tipo', '=', '2')->first();
                        $valors = $value->totales;
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $consulta_plan_debe->cuenta_deudora,
                            'descripcion'         => $consulta_plan_debe->nombre,
                            'fecha'               => $request['fecha'],
                            'debe'                => $valors,
                            'haber'               => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $idusuario,
                            'ip_modificacion'     => $idusuario,
                        ]);
                    }
                    if ($value->tipo == 'IVA') {
                        $consulta_plan_debe  = DB::table('ct_porcentaje_retenciones')->where('id', $value->id_porcentaje)->where('codigo', $value->codigo)->where('tipo', '=', '1')->first();
                        $valors = $value->totales;
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $consulta_plan_debe->cuenta_deudora,
                            'descripcion'         => $consulta_plan_debe->nombre,
                            'fecha'               => $request['fecha'],
                            'debe'                => $valors,
                            'haber'               => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $idusuario,
                            'ip_modificacion'     => $idusuario,
                        ]);
                    }
                }
            }
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cta_x_cob_cli->id_plan,
                'descripcion'         => $cta_x_cob_cli->cuenta->nombre,
                'fecha'               => $request['fecha'],
                'debe'                => '0',
                'haber'               => $valorsf,
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $idusuario,
                'ip_modificacion'     => $idusuario,
            ]);

            Contable::recovery_price($request['id_venta'], 'V');
            DB::commit();
            return ['id' => $id_retenciones, 'sri' => $getSri, 'error' => 'no'];
            //return $id_retenciones;
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return response()->json(['error' => 'si']);
    }
}
