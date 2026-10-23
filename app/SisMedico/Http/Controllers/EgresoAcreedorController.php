<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_detalle_retenciones;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_rfir;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Empresa;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Detalle_Comprobante_Egreso;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Factura_Contable;
use Sis_medico\Ct_compras;
use Sis_medico\ct_master_tipos;
use Sis_medico\Ct_Detalle_Pago;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Anticipo_Proveedores;
use Sis_medico\Retenciones;
use Sis_medico\Validate_Decimals;
use Sis_medico\Numeros_Letras;
class EgresoAcreedorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20)) == false) {
            return true;
        }
    }

    public function index()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('contable/egresos/index');
    }
    public function create()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $divisas = Ct_divisas::where('estado', '1')->get();
        $c_tributario = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        return view('contable/egresos/create', ['divisas' => $divisas, 'c_tributario' => $c_tributario, 't_comprobante' => $t_comprobante]);
    }
    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $proveedores = Proveedor::where('estado', '1')->get();
        $constraints = [
            'id'                  => $request['id'],
            'id_proveedor'        => $request['proveedor'],
            'secuencia'            => $request['secuencia'],
            'no_cheque'           => $request['cheque'],
            'fecha_cheque'        => $request['fecha'],

        ];

        $comp_egreso = $this->doSearchingQuery($constraints, $id_empresa);
        $empresa = Empresa::where('id', $id_empresa)->first();
        return view('contable/comp_egreso/index', ['comp_egreso' => $comp_egreso, 'searchingVals' => $constraints, 'proveedor' => $proveedores, 'empresa' => $empresa]);
    }
    public function buscar_varios(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $constraints = [
            'id'                  => $request['id'],
            'id_proveedor'        => $request['proveedor'],
            'secuencia'           => $request['secuencia'],
            'nro_cheque'          => $request['cheque'],
            'fecha_cheque'        => $request['fecha'],

        ];
        $comp_egreso = $this->doSearchingQuery2($constraints, $id_empresa);

        return view('contable/comp_egreso_varios/index', ['comp_egreso' => $comp_egreso, 'searchingVals' => $constraints, 'empresa' => $empresa]);
    }

    /*************************************************
     ******************CONSULTA QUERY******************
    /*************************************************/
    private function doSearchingQuery($constraints, $id_empresa)
    {

        $query  = Ct_Comprobante_Egreso::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }


        return $query->where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(10);
    }
    private function doSearchingQuery2($constraints, $id_empresa)
    {

        $query  = Ct_Comprobante_Egreso_Varios::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(10);
    }
    public function edit()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        return view('contable/egresos/edit');
    }
    public function comprobante_index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $facturas_compras = Ct_compras::where('estado', '>', '0')->get();
        $comp_egreso = Ct_Comprobante_Egreso::where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(20);
        $divisas = Ct_Divisas::where('estado', '1')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        //dd($facturas_compras);

        return view('contable/comp_egreso/index', ['empresa' => $empresa, 'divisas' => $divisas, 'facturas_compras' => $facturas_compras, 'proveedor' => $proveedores, 'comp_egreso' => $comp_egreso]);
    }
    public function comprobante_create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $formas_pago = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $divisas = Ct_divisas::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        $banco = DB::table('ct_caja_banco')->where('estado', '1')->get();
        return view('contable/comp_egreso/create', ['divisas' => $divisas, 'sucursales' => $sucursales, 'proveedor' => $proveedores, 'empresa' => $empresa, 'banco' => $banco, 'formas_pago' => $formas_pago]);
    }
    public function comprobante_edit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $formas_pago = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $divisas = Ct_divisas::where('estado', '1')->get();
        $banco = DB::table('ct_caja_banco')->where('estado', '1')->get();
        $comprobante_egreso = Ct_Comprobante_Egreso::where('id_empresa', $id_empresa)->where('id', $id)->first();
        $detalle_comprobante = Ct_Detalle_Comprobante_Egreso::where('id_comprobante', $comprobante_egreso->id)->get();
        return view('contable/comp_egreso/edit', ['divisas' => $divisas, 'empresa' => $empresa, 'banco' => $banco, 'formas_pago' => $formas_pago, 'detalle_egreso' => $detalle_comprobante, 'comprobante_egreso' => $comprobante_egreso]);
    }
    public function comprobante_store(Request $request)
    {
        $contador_ctv = DB::table('ct_comprobante_egreso')->get()->count();
        $numero_factura = 0;
        $superavit = (int) $request['superavit'];
        $secuencia_factura = (int) $request['asiento'];
        $secuencia = 0;
        $id_proveedor = $request['id_proveedor'];
        $id_empresa = $request->session()->get('id_empresa');
        $cuentas = Proveedor::where('id', $id_proveedor)->first();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $contador_ctv = DB::table('ct_comprobante_egreso')->where('id_empresa', $id_empresa)->get()->count();
        $numero_factura = 0;
        $banco = (int) $request['banco'];
        $objeto_validar = new Validate_Decimals();
        if ($superavit != 0) {
            if ($request['contador'] != null) {
                if ($contador_ctv == 0) {
                    $num = '1';
                    $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
                } else {

                    $max_id = DB::table('ct_comprobante_egreso')->where('id_empresa', $id_empresa)->latest()->first();
                    $max_id = intval($max_id->secuencia);
                    if (($max_id >= 1) && ($max_id < 10)) {
                        $nu = $max_id + 1;
                        $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                    }
                    if (($max_id >= 10) && ($max_id < 99)) {
                        $nu = $max_id + 1;
                        $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                    }

                    if (($max_id >= 100) && ($max_id < 1000)) {
                        $nu = $max_id + 1;
                        $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                    }

                    if ($max_id == 1000) {
                        $numero_factura = $max_id;
                    }
                }
                if (!is_null($request['total_favor'])) {
                    $nuevo_saldof = $objeto_validar->set_round($request['total_favor']);
                    $input_cabecera = [
                        'observacion' => 'ANTICIPO PROVEEDOR:' . $numero_factura . ' POR LA CANTIDAD DE ' . $nuevo_saldof,
                        'fecha_asiento' => $request['fecha_hoy'],
                        'fact_numero' => $numero_factura,
                        'valor' => $request['total_favor'],
                        'id_empresa' => $id_empresa,
                        'estado' => '1',
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                    ];
                    $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                    if (($banco) != null) {
                        $consulta_db_cajab = Ct_Caja_Banco::where('id', $banco)->first();
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera'           => $id_asiento_cabecera,
                            'id_plan_cuenta'                => $consulta_db_cajab->cuenta_mayor,
                            'descripcion'                   => $consulta_db_cajab->nombre,
                            'fecha'                         => $request['fecha_hoy'],
                            'haber'                         => $nuevo_saldof,
                            'debe'                          => '0',
                            'estado'                        => '1',
                            'id_usuariocrea'                => $idusuario,
                            'id_usuariomod'                 => $idusuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                        ]);
                    }
                    if ($id_proveedor != null) {
                        $desc_cuenta = Plan_Cuentas::where('id', '2.01.03.01.01')->first();
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera'           => $id_asiento_cabecera,
                            'id_plan_cuenta'                => '2.01.03.01.01',
                            'descripcion'                   => $desc_cuenta->nombre,
                            'fecha'                         => $request['fecha_hoy'],
                            'debe'                          => $nuevo_saldof,
                            'haber'                         => '0',
                            'estado'                        => '1',
                            'id_usuariocrea'                => $idusuario,
                            'id_usuariomod'                 => $idusuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                        ]);
                    }

                    $input_comprobante = [
                        'descripcion'     => $request['aaa'] . ' REF: ' . $numero_factura . ' POR LA CANTIDAD DE ' . $nuevo_saldof,
                        'estado'          => '1',
                        'beneficiario'    => $request['nombre_proveedor'],
                        'tipo'            => '2',
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_secuencia'    => $numero_factura,
                        'id_pago'         => '1',
                        'check'           => $request['verificar_cheque'],
                        'fecha_cheque'    => $request['fecha_cheque'],
                        'id_caja_banco'   => $request['banco'],
                        'no_cheque'      => $request['numero_cheque'],
                        'fecha_comprobante' => $request['fecha_hoy'],
                        'secuencia'       => $numero_factura,
                        'id_empresa'      => $id_empresa,
                        'id_proveedor'    => $id_proveedor,
                        'valor_pago'      => $nuevo_saldof,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    $id_comprobante = Ct_Comprobante_Egreso::insertGetId($input_comprobante);
                    Ct_Detalle_Comprobante_Egreso::create([
                        'id_comprobante'                 => $id_comprobante,
                        'id_secuencia'                   => $numero_factura,
                        'saldo_base'                     => $request['total_favor'],
                        'abono'                          => $request['total_favor'],
                        'estado'                         => '1',
                        'ip_creacion'                    => $ip_cliente,
                        'ip_modificacion'                => $ip_cliente,
                        'id_usuariocrea'                 => $idusuario,
                        'id_usuariomod'                  => $idusuario,
                    ]);
                }
            }
        } else {
            if ($request['contador'] != null) {
                if ($contador_ctv == 0) {


                    $num = '1';
                    $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
                } else {

                    $max_id = DB::table('ct_comprobante_egreso')->where('id_empresa', $id_empresa)->latest()->first();
                    $max_id = intval($max_id->secuencia);
                    if (($max_id >= 1) && ($max_id < 10)) {
                        $nu = $max_id + 1;
                        $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                    }
                    if (($max_id >= 10) && ($max_id < 99)) {
                        $nu = $max_id + 1;
                        $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                    }

                    if (($max_id >= 100) && ($max_id < 1000)) {
                        $nu = $max_id + 1;
                        $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                    }

                    if ($max_id == 1000) {
                        $numero_factura = $max_id;
                    }
                }
                $input_cabecera = [
                    'observacion' => $request['aaa'],
                    'fecha_asiento' => $request['fecha_hoy'],
                    'fact_numero' => $numero_factura,
                    'valor' => $request['valor_cheque'],
                    'id_empresa' => $id_empresa,
                    'estado' => '1',
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                ];
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                if (($banco) != null) {
                    $nuevo_saldof = $objeto_validar->set_round($request['valor_cheque']);
                    $consulta_db_cajab = Ct_Caja_Banco::where('id', $banco)->first();
                    $desc_cuenta = Plan_Cuentas::where('id', $cuentas->id_cuentas)->first();
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        'id_plan_cuenta'                => $consulta_db_cajab->cuenta_mayor,
                        'descripcion'                   => $consulta_db_cajab->nombre,
                        'fecha'                         => $request['fecha_hoy'],
                        'haber'                         => $nuevo_saldof,
                        'debe'                          => '0',
                        'estado'                        => '1',
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                }

                $input_comprobante = [
                    'descripcion'     => 'Fact # : ' . $numero_factura . ' POR LA CANTIDAD DE ' . $objeto_validar->set_round($request['valor_cheque']),
                    'estado'          => '1',
                    'beneficiario'    => $request['nombre_proveedor'],
                    'fecha_cheque'    => $request['fecha_cheque'],
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'check'           => $request['verificar_cheque'],
                    'id_secuencia'    => $request['numero'],
                    'id_pago'         => $request['formas_pago'],
                    'id_caja_banco'   => $request['banco'],
                    'no_cheque'       => $request['numero_cheque'],
                    'fecha_comprobante' => $request['fecha_hoy'],
                    'secuencia'       => $numero_factura,
                    'id_empresa'      => $id_empresa,
                    'id_proveedor'    => $id_proveedor,
                    'valor_pago'      => $request['valor_cheque'],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];

                $id_comprobante = Ct_Comprobante_Egreso::insertGetId($input_comprobante);
                for ($i = 0; $i <= $request['contador']; $i++) {

                    if (!is_null($request['abono' . $i])) {
                        if ($request['abono' . $i] > 0) {
                            if ($id_proveedor != null) {
                                $nuevo_saldof = $objeto_validar->set_round($request['abono' . $i]);
                                $desc_cuenta = Plan_Cuentas::where('id', '2.01.03.01.01')->first();
                                Ct_Asientos_Detalle::create([
                                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                                    'id_plan_cuenta'                => '2.01.03.01.01',
                                    'descripcion'                   => $desc_cuenta->nombre,
                                    'fecha'                         => $request['fecha_hoy'],
                                    'debe'                          => $nuevo_saldof,
                                    'haber'                         => '0',
                                    'estado'                        => '1',
                                    'id_usuariocrea'                => $idusuario,
                                    'id_usuariomod'                 => $idusuario,
                                    'ip_creacion'                   => $ip_cliente,
                                    'ip_modificacion'               => $ip_cliente,
                                ]);
                            }
                            $consulta_compra = Ct_compras::where('numero', $request['numero' . $i])->first();
                            Ct_Detalle_Comprobante_Egreso::create([
                                'id_comprobante'                 => $id_comprobante,
                                'observacion'                    => $request['aaa'],
                                'id_compra'                      => $consulta_compra->id,
                                'id_secuencia'                   => $request['numero' . $i],
                                'saldo_base'                     => $request['saldo' . $i],
                                'abono'                          => $request['abono' . $i],
                                'estado'                         => '1',
                                'ip_creacion'                    => $ip_cliente,
                                'ip_modificacion'                => $ip_cliente,
                                'id_usuariocrea'                 => $idusuario,
                                'id_usuariomod'                  => $idusuario,
                            ]);
                        }
                    }
                }
                $consulta_compra = null;
                $input_actualiza = null;

                /*************************************
                 ****ACTUALIZO CUANDO ES COMPRA TODOS LOS VALORES CONTABLES CON EL ABONO DE COMPROBANTE DE EGRESO***
                    /*************************************/

                for ($i = 0; $i <= $request['contador']; $i++) {
                    if (!is_null($request['abono' . $i])) {
                        $nuevo_saldo = 0;
                        //actualizar valor contable de cada tabla
                        $consulta_compra = Ct_compras::where('numero', $request['numero' . $i])->first();
                        if ($consulta_compra != null) {
                            if ($request['abono' . $i] > 0) {
                                if ($request['abono' . $i] > ($consulta_compra->valor_contable)) {
                                    $nuevo_saldo = $request['abono' . $i] - $consulta_compra->valor_contable;
                                } else {
                                    $nuevo_saldo = $consulta_compra->valor_contable - $request['abono' . $i];
                                }

                                $nuevo_saldof = $objeto_validar->set_round($nuevo_saldo);
                                $input_actualiza = null;
                                if ($nuevo_saldof != 0) {
                                    $input_actualiza = [
                                        'estado'                        => '2', //poner otro estado para que no salga en las consultas
                                        'valor_contable'                => $nuevo_saldof,
                                        'ip_creacion'                   => $ip_cliente,
                                        'ip_modificacion'               => $ip_cliente,
                                        'id_usuariocrea'                => $idusuario,
                                        'id_usuariomod'                 => $idusuario,
                                    ];
                                } else {
                                    $input_actualiza = [
                                        'estado'                        => '3', //poner otro estado para que no salga en las consultas
                                        'valor_contable'                => $nuevo_saldof,
                                        'ip_creacion'                   => $ip_cliente,
                                        'ip_modificacion'               => $ip_cliente,
                                        'id_usuariocrea'                => $idusuario,
                                        'id_usuariomod'                 => $idusuario,
                                    ];
                                }
                                $consulta_compra->update($input_actualiza);
                            }
                        }
                    }
                }
            }
        }

        return $id_comprobante;
    }
    public function buscar_codigo(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $id_factura    = $request['id_factura'];
        $tipo = 1;
        $data      = null;
        if ($tipo == 1) {
            /*$productos= DB::table('ct_asientos_detalle as a')
            ->join('ct_asientos_cabecera as c','c.id','a.id_asiento_cabecera')
            ->join('ct_compras as co','co.id','c.id_ct_compras')
            ->where('c.fact_numero',$id_factura)
            ->select('a.fecha','a.descripcion','co.proveedor')->get();*/
            /*$consulta= DB::table('ct_asientos_cabecera')->where('fact_numero',$id_factura)->first();
            $productos= DB::table('ct_asientos_detalle')->where('id_asiento_cabecera',$consulta->id)->get();*/
            $productos = DB::table('ct_asientos_detalle as a')
                ->join('ct_asientos_cabecera as c', 'c.id', 'a.id_asiento_cabecera')
                ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
                ->join('proveedor as p', 'p.id', 'co.proveedor')
                ->where('co.secuencia_f', $id_factura)
                ->where('c.id_empresa', $id_empresa)
                ->where('c.estado', '1')
                ->select(
                    'co.proveedor',
                    'p.nombrecomercial',
                    'p.direccion',
                    'a.id',
                    'a.descripcion',
                    'p.razonsocial',
                    'co.fecha',
                    'p.id_tipoproveedor',
                    'c.observacion',
                    'c.fecha_asiento',
                    '.c.valor',
                    'co.numero',
                    'co.tipo',
                    'p.id_porcentaje_iva',
                    'p.id_porcentaje_ft',
                    'co.id',
                    'c.fact_numero',
                    'co.autorizacion',
                    'co.subtotal',
                    'co.iva_total'
                )

                ->get();

            $deudas = DB::table('ct_asientos_cabecera as c')
                ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
                ->join('proveedor as p', 'p.id', 'co.proveedor')
                ->where('co.proveedor', $productos[0]->proveedor)
                ->where('c.estado', '1')
                ->where('c.id_empresa', $id_empresa)
                ->select('c.valor', 'p.id_tipoproveedor', 'p.id_porcentaje_iva', 'co.tipo', 'p.id_porcentaje_ft', 'c.fact_numero', 'c.observacion', 'c.fecha_asiento', 'co.proveedor', 'c.valor_nuevo')
                ->orderby('co.fecha', 'asc')
                ->get();
        }

        if ($productos != '[]') {

            $data = [
                $productos[0]->proveedor, $productos[0]->id, $productos[0]->nombrecomercial, $productos[0]->direccion,
                $productos[0]->descripcion, $productos[0]->razonsocial, $productos, $productos[0]->id_tipoproveedor, $productos[0]->observacion,
                $productos[0]->fecha_asiento, $productos[0]->valor, $productos[0]->numero, $productos[0]->id_porcentaje_iva, $productos[0]->id_porcentaje_ft,
                $productos[0]->id, $productos[0]->fact_numero, $deudas, $productos[0]->autorizacion, $productos[0]->subtotal, $productos[0]->iva_total, $productos[0]->tipo
            ];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public function buscarproveedor(Request $request)
    {
        $id_proveedor = $request['proveedor'];
        $id_empresa = $request->session()->get('id_empresa');
        $data = 0;
        $tipo = 1;
        $facturas = '[]';
        $deudas = null;
        if ($tipo == 1) {
            $facturas = DB::table('ct_asientos_detalle as a')
                ->join('ct_asientos_cabecera as c', 'c.id', 'a.id_asiento_cabecera')
                ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
                ->join('proveedor as p', 'p.id', 'co.proveedor')
                ->where('co.proveedor', $id_proveedor)
                ->where('c.id_empresa', $id_empresa)
                ->where('co.estado', '<', '3')
                ->where('co.valor_contable', '>', '0')
                ->select('co.valor_contable', 'co.tipo', 'co.secuencia_f', 'c.observacion', 'a.id', 'c.fecha_asiento')
                ->get();
            $deudas = DB::table('ct_asientos_cabecera as c')
                ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
                ->where('co.proveedor', $id_proveedor)
                ->where('c.id_empresa', $id_empresa)
                ->where('co.estado', '<', '4')
                ->where('co.valor_contable', '>', '0')
                ->select('co.valor_contable', 'co.secuencia_f', 'co.tipo', 'c.observacion', 'co.f_caducidad as fecha_asiento', 'co.numero', 'co.proveedor', 'c.valor as valor_nuevo')
                ->orderby('c.id', 'desc')
                ->get();
        }

        //dd($facturas);        
        if ($facturas != '[]') {
            $data = [$facturas[0]->valor_contable, $facturas[0]->secuencia_f, $facturas[0]->observacion, $facturas[0]->id, $facturas[0]->fecha_asiento, $deudas, $facturas[0]->tipo];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public function pdfcomprobante($id, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $comp_egreso = Ct_Comprobante_Egreso::where('id_empresa', $id_empresa)->where('id', $id)->first();
        $empresa = Empresa::where('id', $comp_egreso->id_empresa)->first();
        $letras = new Numeros_Letras();
        //la variable convertir con la clase Numeros Letras
        $total_str = $letras->convertir(number_format($comp_egreso->valor_pago, 2, '.', ''), "DOLARES", "CTVS");
        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $comp_egreso->id_asiento_cabecera)->first();
        $compras = Ct_compras::where('secuencia_f', $comp_egreso->id_secuencia)->first();
        $asiento_detalle = Ct_Asientos_Detalle::where('estado', '1')->where('id_asiento_cabecera', $asiento_cabecera->id)->get();
        //dd($asiento_detalle);
        if ($comp_egreso != '[]') {
            if (($comp_egreso->tipo) != 1) {
                $vistaurl = "contable.comp_egreso_varios.pdf_comprobante_egreso_varios";
                $view     = \View::make($vistaurl, compact('comp_egreso', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle'))->render();
                $pdf      = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->stream('resultado-' . $id . '.pdf');
            } else {
                $vistaurl = "contable.comp_egreso.pdf_comprobante_egreso";
                $view     = \View::make($vistaurl, compact('comp_egreso', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle', 'compras'))->render();
                $pdf      = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->stream('resultado-' . $id . '.pdf');
            }
        }
    }
    public function egresosv(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $comp_egreso = Ct_Comprobante_Egreso_Varios::where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(10);
        return view('contable/comp_egreso_varios/index', ['comp_egreso' => $comp_egreso]);
    }
    public function egresov_create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $formas_pago = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $divisas = Ct_divisas::where('estado', '1')->get();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $banco = DB::table('ct_caja_banco')->where('estado', '1')->get();
        return view('contable/comp_egreso_varios/create', ['divisas' => $divisas, 'empresa' => $empresa, 'banco' => $banco, 'sucursales' => $sucursales, 'formas_pago' => $formas_pago]);
    }

    public function egresosvedit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $formas_pago = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $divisas = Ct_divisas::where('estado', '1')->get();
        $banco = DB::table('ct_caja_banco')->where('estado', '1')->get();
        $comprobante_egreso = Ct_Comprobante_Egreso_Varios::where('id_empresa', $id_empresa)->where('id', $id)->first();
        $detalle_comprobante = Ct_Detalle_Comprobante_Egreso_Varios::where('id_comprobante_varios', $comprobante_egreso->id)->get();
        return view('contable/comp_egreso_varios/edit', ['divisas' => $divisas, 'empresa' => $empresa, 'banco' => $banco, 'formas_pago' => $formas_pago, 'detalle_egreso' => $detalle_comprobante, 'varios' => $comprobante_egreso]);
    }

    public function egresov_store(Request $request)
    {
        $numero_factura = 0;
        if (!is_null($request['contador'])) {
            $sucursal = $request['sucursal'];
            $id_empresa = $request->session()->get('id_empresa');
            $punto_emision = $request['punto_emision'];
            $sucursal = substr($punto_emision, 0, -4);
            $punto_emision = substr($punto_emision, 4);
            $contador_ctv = DB::table('ct_comprobante_egreso_varios')->where('id_empresa', $id_empresa)->get()->count();
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $objeto_validar = new Validate_Decimals();
            $numero_factura = 0;
            $idusuario  = Auth::user()->id;
            $id_empresa = $request->session()->get('id_empresa');
            if ($contador_ctv == 0) {

                //return 'No Retorno nada';
                $num = '1';
                $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            } else {
                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id = DB::table('ct_comprobante_egreso_varios')->where('id_empresa', $id_empresa)->latest()->first();
                $max_id = intval($max_id->secuencia);
                if (($max_id >= 1) && ($max_id < 10)) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
                if (($max_id >= 10) && ($max_id < 99)) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }

                if (($max_id >= 100) && ($max_id < 1000)) {
                    $nu = $max_id + 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }

                if ($max_id == 1000) {
                    $numero_factura = $max_id;
                }
            }
            $input_cabecera = [
                'observacion' => $request['concepto'] . ' POR LA CANTIDAD DE ' . $request['valor_cheque'],
                'fecha_asiento' => $request['fecha_hoy'],
                'fact_numero' => '',
                'valor' => $objeto_validar->set_round($request['valor_cheque']),
                'id_empresa' => $id_empresa,
                'estado' => '3',
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            $banco = $request['banco'];
            if (($banco) != null) {
                $nuevo_saldof = $objeto_validar->set_round($request['valor_cheque']);
                $consulta_db_cajab = Ct_Caja_Banco::where('id', $banco)->first();
                $desc_cuenta = Plan_Cuentas::where('id', $consulta_db_cajab->cuenta_mayor)->first();

                if ($request['haber0'] > 0) {
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        'id_plan_cuenta'                => $consulta_db_cajab->cuenta_mayor,
                        'descripcion'                   => $consulta_db_cajab->nombre,
                        'fecha'                         => $request['fecha_hoy'],
                        'debe'                         => $nuevo_saldof,
                        'haber'                          => '0',
                        'estado'                        => '1',
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                } else {
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        'id_plan_cuenta'                => $consulta_db_cajab->cuenta_mayor,
                        'descripcion'                   => $consulta_db_cajab->nombre,
                        'fecha'                         => $request['fecha_hoy'],
                        'haber'                         => $nuevo_saldof,
                        'debe'                          => '0',
                        'estado'                        => '1',
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                }
            }
            $input_comprobante = [
                'descripcion'     => $request['concepto'] . ' POR LA CANTIDAD DE ' . $request['valor_cheque'],
                'estado'          => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_secuencia'    => 'null',
                'fecha_comprobante' => $request['fecha_hoy'],
                'beneficiario'    => strtoupper($request['beneficiario']),
                'check'           => $request['verificar_cheque'],
                'girado'          => $request['giradoa'],
                'id_caja_banco'   => $request['banco'],
                'nro_cheque'      => $request['numero_cheque'],
                'valor'           => $objeto_validar->set_round($request['valor_cheque']),
                'fecha_cheque'    => $request['fecha_cheque'],
                'secuencia'       => $numero_factura,
                'id_empresa'      => $id_empresa,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);
            for ($i = 0; $i <= $request['contador']; $i++) {
                $nuevo_saldof = $objeto_validar->set_round($request['debe' . $i]);
                if (!is_null($request['codigo' . $i])) {
                    $desc_cuenta = Plan_Cuentas::where('id', $request['codigo' . $i])->first();
                    if ($desc_cuenta != null) {
                        if ($request['visibilidad' . $i] == 1 || $request['visibilidad' . $i] == '1') {
                            if (!is_null($request['debe' . $i]) && $request['debe' . $i] > 0) {
                                Ct_Asientos_Detalle::create([
                                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                                    'id_plan_cuenta'                => $request['codigo' . $i],
                                    'descripcion'                   => $desc_cuenta->nombre,
                                    'fecha'                         => $request['fecha_hoy'],
                                    'debe'                          => $nuevo_saldof,
                                    'haber'                         => '0',
                                    'estado'                        => '1',
                                    'id_usuariocrea'                => $idusuario,
                                    'id_usuariomod'                 => $idusuario,
                                    'ip_creacion'                   => $ip_cliente,
                                    'ip_modificacion'               => $ip_cliente,
                                ]);
                            } elseif (!is_null($request['haber' . $i]) && $request['haber' . $i] > 0) {
                                $nuevo_saldof = $objeto_validar->set_round($request['haber' . $i]);

                                Ct_Asientos_Detalle::create([
                                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                                    'id_plan_cuenta'                => $request['codigo' . $i],
                                    'descripcion'                   => $desc_cuenta->nombre,
                                    'fecha'                         => $request['fecha_hoy'],
                                    'haber'                          => $nuevo_saldof,
                                    'debe'                         => '0',
                                    'estado'                        => '1',
                                    'id_usuariocrea'                => $idusuario,
                                    'id_usuariomod'                 => $idusuario,
                                    'ip_creacion'                   => $ip_cliente,
                                    'ip_modificacion'               => $ip_cliente,
                                ]);
                            }
                        }
                    }
                }

                if (!is_null($request['debe' . $i])) {
                    Ct_Detalle_Comprobante_Egreso_Varios::create([
                        'id_comprobante_varios'          => $id_comprobante,
                        'codigo'                         => $request['codigo' . $i],
                        'cuenta'                         => $request['nombre' . $i],
                        'descripcion'                    => $request['observacion'],
                        'debe'                           => $request['debe' . $i],
                        'id_secuencia'                   => $numero_factura,
                        'estado'                         => '1',
                        'ip_creacion'                    => $ip_cliente,
                        'ip_modificacion'                => $ip_cliente,
                        'id_usuariocrea'                 => $idusuario,
                        'id_usuariomod'                  => $idusuario,
                    ]);
                }
                if (!is_null($request['haber' . $i])) {
                    Ct_Detalle_Comprobante_Egreso_Varios::create([
                        'id_comprobante_varios'          => $id_comprobante,
                        'codigo'                         => $request['codigo' . $i],
                        'cuenta'                         => $request['nombre' . $i],
                        'descripcion'                    => $request['observacion'],
                        'debe'                           => $request['haber' . $i],
                        'id_secuencia'                   => $numero_factura,
                        'estado'                         => '1',
                        'ip_creacion'                    => $ip_cliente,
                        'ip_modificacion'                => $ip_cliente,
                        'id_usuariocrea'                 => $idusuario,
                        'id_usuariomod'                  => $idusuario,
                    ]);
                }
            }
            return $id_comprobante;
        } else {
            return 'error no guardó nada';
        }
    }
    public function pdfegresovarios($id, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $comp_egreso = Ct_Comprobante_Egreso_Varios::where('id_empresa', $id_empresa)->where('id', $id)->first();
        $empresa = Empresa::where('id', $comp_egreso->id_empresa)->first();
        $letras = new Numeros_Letras();
        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $comp_egreso->id_asiento_cabecera)->first();
        $total_str = $letras->convertir($asiento_cabecera->valor, "DOLARES", "CTVS");
        $asiento_detalle = Ct_Asientos_Detalle::where('estado', '1')->where('id_asiento_cabecera', $asiento_cabecera->id)->get();
        $vistaurl = "contable.comp_egreso_varios.pdf_comprobante_egreso_varios";
        $view     = \View::make($vistaurl, compact('comp_egreso', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }
    public function anular_egreso($id, Request $request)
    {

        if (!is_null($id)) {
            $comp_ingreso = Ct_Comprobante_Egreso::where('estado', '1')->where('id', $id)->first();
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $id_empresa = $request->session()->get('id_empresa');
            $idusuario  = Auth::user()->id;
            if (!is_null($comp_ingreso)) {

                // ahora actualizo el valor y le sumo lo que ya le había restado
                //dd($comp_ingreso->detalle);  219.12
                if (!is_null($comp_ingreso->detalles)) {
                    foreach ($comp_ingreso->detalles as $value) {
                        $consulta_venta = Ct_compras::where('id', $value->id_compra)->where('estado', '>', '0')->first();

                        if (!is_null($consulta_venta)) {
                            $valor = $consulta_venta->valor_contable;
                            $suma = ($value->abono) + $valor;
                            $input_actualiza = [
                                'valor_contable'                => $suma,
                                'estado'                        => '2',
                                'ip_creacion'                   => $ip_cliente,
                                'ip_modificacion'               => $ip_cliente,
                                'id_usuariocrea'                => $idusuario,
                                'id_usuariomod'                 => $idusuario,
                            ];
                            $consulta_venta->update($input_actualiza);
                        }
                    }
                }
                $input = [
                    'estado' => '0',
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                ];
                $comp_ingreso->update($input);
                $asiento = Ct_Asientos_Cabecera::findorfail($comp_ingreso->id_asiento_cabecera);
                $asiento->estado = 0;
                $asiento->save();
                $detalles = $asiento->detalles;
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => 'ANULACIÓN ' . $asiento->observacion,
                    'fecha_asiento'   => date('Y-m-d H:i:s'),
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $comp_ingreso->secuencia,
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
                        'fecha'               => date('Y-m-d H:i:s'),
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                    ]);
                }
                return redirect()->route('acreedores_cegreso');
            }
        } else {
            return 'error';
        }
    }
    public function anular_egreso_v($id, Request $request)
    {
        if (!is_null($id)) {
            $comp_ingreso = Ct_Comprobante_Egreso_Varios::where('estado', '1')->where('id', $id)->first();
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $id_empresa = $request->session()->get('id_empresa');
            $idusuario  = Auth::user()->id;
            if (!is_null($comp_ingreso)) {

                // ahora actualizo el valor y le sumo lo que ya le había restado
                //dd($comp_ingreso->detalle);  219.12

                $input = [
                    'estado' => '0',
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                ];
                $comp_ingreso->update($input);
                $asiento = Ct_Asientos_Cabecera::findorfail($comp_ingreso->id_asiento_cabecera);
                $asiento->estado = 0;
                $asiento->save();
                $detalles = $asiento->detalles;
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => 'ANULACIÓN ' . $asiento->observacion,
                    'fecha_asiento'   => date('Y-m-d H:i:s'),
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $comp_ingreso->secuencia,
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
                        'fecha'               => date('Y-m-d H:i:s'),
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                    ]);
                }
                return redirect()->route('egresosv.index');
            }
        } else {
            return 'error';
        }
    }
}
