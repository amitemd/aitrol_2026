<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Cuenta_Pagar;
use Sis_medico\Ct_Cuenta_Pagar_Detalle;
use Sis_medico\Ct_Debito_Bancario;
use Sis_medico\Ct_Debito_Bancario_Detalle;
use Sis_medico\Nota_Debito;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Numeros_Letras;
use Sis_medico\Proveedor;
use Mail;
use Excel;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_Detalle_Comprobante_Egreso;

class DebitoBancarioController extends Controller
{
    //
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
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa= Empresa::find($id_empresa);
        $proveedores = Proveedor::where('estado', '1')->get();
        //$principales = Ct_Debito_Bancario::where('estado', '!=', null)->orderby('id', 'desc')->where('id_empresa',$id_empresa)->paginate(5);
        //QUIEN ESCRIBIO LA LINEA ANTERIOR APRENDA ESTA SENTENCIA: where('estado', '!=', null) NO FUNKA EN LINUX ATT VICTOR HUGO
        $principales = Ct_Debito_Bancario::whereNotNull('estado')->orderby('id', 'desc')->where('id_empresa', $id_empresa)->paginate(5);
        //dd($principales);
        return view('contable/debito_bancario/index', ['registros' => $principales, 'proveedores' => $proveedores,'empresa'=>$empresa]);
    }

    public function crear(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $formas_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $divisas     = Ct_Divisas::where('estado', '1')->get();
        $banco       = Ct_Caja_Banco::where('estado', '1')->where('clase', '1')->get();
        $bancos      = Ct_Caja_Banco::where('estado', '1')->get();
        $proveedor =    Proveedor::where('estado', 1)->get();

        return view('contable/debito_bancario/create', ['proveedor' => $proveedor, 'divisas' => $divisas, 'empresa' => $empresa, 'banco' => $banco, 'bancos' => $bancos, 'formas_pago' => $formas_pago]);
    }

    public function buscardatosproveedor(Request $request)
    {
        $id_proveedor = $request['proveedor'];
        $id_empresa   = $request->session()->get('id_empresa');
        $data         = 0;
        $facturas     = DB::table('ct_asientos_detalle as a')
            ->join('ct_asientos_cabecera as c', 'c.id', 'a.id_asiento_cabecera')
            ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
            ->join('proveedor as p', 'p.id', 'co.proveedor')
            ->where('co.proveedor', $id_proveedor)
            ->where('c.id_empresa', $id_empresa)
            ->where('c.estado', '1')
            ->select('c.valor', 'c.fact_numero', 'co.secuencia_f', 'co.numero', 'c.observacion', 'a.id', 'c.fecha_asiento')
            ->whereNotIn('c.fact_numero', function ($query) {
                $query->select('numero')
                    ->from('ct_debito_bancario_detalle');
            })
            ->get();

        $deudas = DB::table('ct_asientos_cabecera as c')
            ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
            ->where('co.proveedor', $id_proveedor)
            ->where('c.id_empresa', $id_empresa)
            ->where('c.estado', '1')
            ->where('co.valor_contable', '>', '0')
            ->select('c.valor', 'c.fact_numero', 'co.secuencia_f', 'co.numero', 'c.observacion', 'c.fecha_asiento', 'co.proveedor', 'co.valor_contable')
            ->orderby('c.id', 'desc')

            ->whereNotIn('c.fact_numero', function ($query) {
                $query->select('numero')
                    ->from('ct_debito_bancario_detalle');
            })
            ->get();
        // $deudas = [];
        if ($facturas != '[]') {
            $data = [$facturas[0]->valor, $facturas[0]->numero, $facturas[0]->observacion, $facturas[0]->id, $facturas[0]->fecha_asiento, $deudas];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public function buscarproveedor(Request $request)
    {
        $nombre    = $request['term'];
        $data      = array();
        $productos = DB::table('proveedor')->where('nombrecomercial', 'like', '%' . $nombre . '%')->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->nombrecomercial, 'id' => $product->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function comprasproveedor(Request $request)
    {
        $nombre    = $request['nombre'];
        $data      = null;
        $productos = DB::table('proveedor')->where('nombrecomercial', $nombre)->first();
        if (!is_null($productos)) {
            return ['value' => $productos->id];
        } else {
            return ['value' => $nombre];
        }
    }

    public function superavit(Request $request)
    {
        /*
        //aqui se registra el superavit en una tabla a parte
        $ip_cliente        = $_SERVER["REMOTE_ADDR"];
        $idusuario         = Auth::user()->id;
        $secuencia_factura = (int) $request['asiento'];
        $secuencia         = 0;
        //dd($secuencia_factura);
        $nueva_secuencia        = (int) $secuencia_factura + 1;
        $nuevo_secuencia_string = (string) $nueva_secuencia;
        $nueva_secuencia_final  = strlen($nuevo_secuencia_string);
        switch ($nueva_secuencia_final) {
            case 1:
                $secuencia = '000000000';
                break;
            case 2:
                $secuencia = '00000000';
                break;
            case 3:
                $secuencia = '0000000';
                break;
            case 4:
                $secuencia = '000000';
                break;
            case 5:
                $secuencia = '00000';
                break;
            case 6:
                $secuencia = '0000';
                break;
            case 7:
                $secuencia = '000';
                break;
            case 8:
                $secuencia = '00';
                break;
            case 9:
                $secuencia = '0';
                break;
        }
        $numero_factura = $secuencia . $nuevo_secuencia_string;
        if (!is_null($request['comprobante'])) {
            $id_comprobante      = $request['comprobante'];
            $id_proveedor        = $request['proveedor'];
            $id_asiento_cabecera = $request['id_asiento_cabecera'];
            $input_comprobante   = [
                'descripcion'     => 'COMPROBANTE DE EGRESO REF: ' . $numero_factura,
                'estado'          => '1',
                'id_pago'         => $id_pagos,
                'id_cabecera'     => $id_asiento_cabecera,
                'id_proveedor'    => $id_proveedor,
                'valor_cabecera'  => $request['nuevo_saldo0'],
                'valor_pago'      => $request['saldo_final'],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,

            ];
            //$id_comprobante = Ct_Comprobante_Egreso::insertGetId($input_comprobante);

        }*/
    }
    public function buscarcodigo(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $id_factura = $request['id_factura'];
        $data       = null;
        $productos  = DB::table('ct_asientos_detalle as a')
            ->join('ct_asientos_cabecera as c', 'c.id', 'a.id_asiento_cabecera')
            ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
            ->join('proveedor as p', 'p.id', 'co.proveedor')
            ->where('c.fact_numero', $id_factura)
            ->where('c.id_empresa', $id_empresa)
            ->where('c.estado', '1')
            ->select(
                'co.proveedor',
                'c.valor_nuevo',
                'p.nombrecomercial',
                'co.numero',
                'co.secuencia_f',
                'p.direccion',
                'a.id',
                'a.descripcion',
                'p.razonsocial',
                'co.fecha',
                'p.id_tipoproveedor',
                'c.observacion',
                'c.fecha_asiento',
                'c.valor',
                'co.serie',
                'p.id_porcentaje_iva',
                'p.id_porcentaje_ft',
                'co.id',
                'c.fact_numero'
            )->get();
        $deudas = DB::table('ct_asientos_cabecera as c')
            ->join('ct_compras as co', 'co.id_asiento_cabecera', 'c.id')
            ->where('c.id_empresa', $id_empresa)
            ->where('co.proveedor', $productos[0]->proveedor)
            ->where('c.estado', '1')
            ->select('c.id', 'c.valor', 'co.secuencia_f', 'c.observacion', 'co.numero', 'c.fecha_asiento', 'co.proveedor', 'c.valor_nuevo')
            ->orderby('c.updated_at', 'desc')
            ->get();

        if ($productos != '[]') {
            $data = [
                $productos[0]->proveedor, $productos[0]->id, $productos[0]->nombrecomercial, $productos[0]->direccion,
                $productos[0]->descripcion, $productos[0]->razonsocial, $productos, $productos[0]->id_tipoproveedor, $productos[0]->observacion,
                $productos[0]->fecha_asiento, $productos[0]->valor, $productos[0]->serie, $productos[0]->id_porcentaje_iva,
                $productos[0]->id_porcentaje_ft, $productos[0]->id, $productos[0]->numero, $deudas
            ];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $proveedores = Proveedor::where('estado', '1')->get();
        $constraints = [
            'id'                 => $request['id'],
            'id_acreedor'        => $request['id_proveedor'],
            'id_asiento'         => $request['id_asiento'],
            'fecha'              => $request['fecha'],
            'concepto'           => $request['buscar_concepto'],

        ];

        $comp_egreso = $this->doSearchingQuery($constraints, $id_empresa);
        $empresa = Empresa::where('id', $id_empresa)->first();
        return view('contable/debito_bancario/index', ['registros' => $comp_egreso, 'searchingVals' => $constraints, 'proveedores' => $proveedores, 'empresa' => $empresa]);
    }
    private function doSearchingQuery($constraints, $id_empresa)
    {

        $query  = Ct_Debito_Bancario::query();
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
    public function generardebito(Request $request)
    {

        /*try {
           
            // return redirect()->route('debitobancario.index');
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }*/
        $numero_factura = 0;

        $secuencia    = 0;
        $id_proveedor = $request['id_proveedor']; //el id del proveedor que posee deuda
        $id_empresa   = $request->session()->get('id_empresa');
        $cuentas      = Proveedor::where('id', $id_proveedor)->first();
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $contador_ctv = DB::table('ct_debito_bancario')->where('id_empresa', $id_empresa)->get()->count();

        $numero_factura = 0;
        if ($contador_ctv == 0) {
            $num = '1';
            $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
        } else {

            //Obtener Ultimo Registro de la Tabla ct_debito_bancario
            $max_id = DB::table('ct_debito_bancario')->where('id_empresa', $id_empresa)->first();
            $max_id = intval($max_id->secuencia);
            if (strlen($max_id) < 10) {
                $nu = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }
        }

        $fecha_as = $request['fecha_asiento'];
        $input_cabecera = [
            'observacion'     => $request['observacion'],
            'fecha_asiento'   => $fecha_as,
            'id_empresa'      => $id_empresa,
            'valor'           => $request['valor_cheque'],
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

        $db_cabecera = [
            'id_acreedor'     => $request["id_proveedor"],
            'tipo'            => $request["tipo"],
            'id_asiento'      => $id_asiento_cabecera,
            'fecha'           => $request['fecha_asiento'],
            'concepto'        => $request["observacion"],
            'secuencia'       => $numero_factura,
            'id_banco'        => $request["id_banco"],
            'id_divisa'       => $request["divisa"],
            'cambio'          => $request["cambio"],
            'valor'           => $request["valor_cheque"],
            'total_debito'    => $request["total_debito"],
            'debito_aplicado' => $request["debito_aplicado"],
            'total_deudas'    => $request["total_deudas"],
            'total_abonos'    => $request["total_abonos"],
            'nuevo_saldo'     => $request["nuevo_saldo"],
            'deficit'         => $request["deficit"],
            'debito_favor'    => $request["debito_favor"],
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_empresa'      => $id_empresa,
        ];
        //print_r($db_cabecera);
        $id_db = Ct_Debito_Bancario::insertGetId($db_cabecera);
        for ($i = 0; $i <= $request['contador']; $i++) {

            //asiento cabecera
            //  $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($asiento_cabecera);
            $desc_cuenta = Plan_Cuentas::where('id', $cuentas->id_cuentas)->first();
            //print_r($desc_cuenta);

            //asiento detalle


            //debito bancario detalle

            $nuevo_saldo = 0;
            //actualizar valor contable de cada tabla
            $consulta_compra = Ct_compras::where('id', $request['id_actualiza' . $i])->where('id_empresa', $id_empresa)->where('estado', '<>', '0')->first();
            if ($request['abono' . $i] > 0) {
                if ($consulta_compra != null) {
                    if ($request['abono' . $i] > ($consulta_compra->valor_contable)) {
                        $nuevo_saldo = $request['abono' . $i] - $consulta_compra;
                    } else {
                        $nuevo_saldo = $consulta_compra->valor_contable - $request['abono' . $i];
                    }
                    if ($request['abono' . $i] > 0) {
                        $db_detalle = [
                            'numero'            => $request['numero' . $i],
                            'id_compra'         => $consulta_compra->id,
                            'tipo'              => $request['tipo' . $i],
                            'fecha_vencimiento' => $request['vence' . $i],
                            'concepto'          => $request['concepto' . $i],
                            'saldo'             => $request['saldo' . $i],
                            'abono'             => $request['abono' . $i],
                            'estado'            => '1',
                            'id_debito'         => $id_db,
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            //idasiento
                        ];

                        $id_det = Ct_Debito_Bancario_Detalle::insertGetId($db_detalle);
                        //dd($id_det);
                        $asiento_detalle = [
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $desc_cuenta->id,
                            'descripcion'         => $desc_cuenta->nombre,
                            'fecha'               => $request['fecha_asiento'],
                            'debe'                => $request['abono' . $i],
                            'haber'               => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ];
                        Ct_Asientos_Detalle::create($asiento_detalle);
                    }

                    $nuevo_saldof = $nuevo_saldo;
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

            //cuenta por pagar cabecera
            $cxp_cabecera = [
                'id_tipo_pago'    => 5, //$request['formas_pago'],
                'fecha'           => $request['fecha_asiento'],
                'id_proveedor'    => $request['id_proveedor'],
                'id_caja_banco'   => $request['id_banco'],
                'id_divisa'       => $request['divisa'],
                //'girado_a'              => $request['giradoa'],
                'concepto'        => $request['observacion'],
                'valor'           => $request['abono' . $i],
                'estado'          => '1',
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $id_cxp_cabecera = Ct_Cuenta_Pagar::insertGetId($cxp_cabecera);

            //cuenta por cobrar detalle
            $cxp_detalle = [
                'id_cuenta_pagar'   => $id_cxp_cabecera,
                'numero_documento'  => $request['numero' . $i],
                'fecha'             => $request['fecha_asiento'],
                'valor'             => $request['saldo' . $i],
                'fecha_vencimiento' => $request['vence' . $i],
                'abono'             => $request['abono' . $i],
                'estado'            => '1',
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,

            ];
            Ct_Cuenta_Pagar_Detalle::create($cxp_detalle);

            //





        }
        $cuenta_banco = Ct_Caja_Banco::where('id', $request['id_banco'])->first();
        $valor_cheque = 0;
        $valor_cheque = $request['valor_cheque'];
        $plan_c = Plan_Cuentas::where('id', $cuenta_banco['cuenta_mayor'])->first();
        if ($request['debito_favor']>0) {
            $valor_cheque = $request['valor_cheque'] - $request['total_favor'];
        }

        if ($request['debito_favor']>0) {
            //$valor_cheque=$request['valor_cheque'];
            $valor_cheque = $request['debito_favor'];
            $asiento_detalle = [
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => '1.01.04.03', //cambiar a anticipo proveedores
                'descripcion'                   => 'Anticipo a Proveedores',
                'fecha'                         => $request['fecha_asiento'],
                'debe'                          => $valor_cheque,
                'haber'                         => '0',
                'estado'                        => '1',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,

            ];
            $input_comprobante = [
                'descripcion'     => $request['aaa'],
                'estado'          => '1',
                'beneficiario'    => $request['nombre_proveedor'],
                'tipo'            => '2',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_secuencia'    => $numero_factura,
                'id_referencia'   => $id_db,
                'id_pago'         => '1',
                'check'           => $request['verificar_cheque'],
                'fecha_cheque'    => $request['fecha_cheque'],
                'id_caja_banco'   => $request['banco'],
                'no_cheque'       => $request['numero_cheque'],
                'fecha_comprobante' => $request['fecha_hoy'],
                'secuencia'       => $numero_factura,
                'id_empresa'      => $id_empresa,
                'id_proveedor'    => $id_proveedor,
                'valor_pago'      => $request['debito_favor'],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_comprobante = Ct_Comprobante_Egreso::insertGetId($input_comprobante);
            Ct_Detalle_Comprobante_Egreso::create([
                'id_comprobante'                 => $id_comprobante,
                'id_secuencia'                   => $numero_factura,
                'saldo_base'                     => $request['debito_favor'],
                'abono'                          => $request['debito_favor'],
                'estado'                         => '1',
                'ip_creacion'                    => $ip_cliente,
                'ip_modificacion'                => $ip_cliente,
                'id_usuariocrea'                 => $idusuario,
                'id_usuariomod'                  => $idusuario,
            ]);
            Ct_Asientos_Detalle::create($asiento_detalle);
        }



        $as_cuenta_g = [

            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_plan_cuenta'      => $plan_c['id'],
            'descripcion'         => $plan_c['nombre'],
            'fecha'               => $fecha_as,
            'debe'                => '0.00',
            'haber'               => $request['valor_cheque'],
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,

        ];
        Ct_Asientos_Detalle::create($as_cuenta_g);
         /*if ($request['comprobarx'] == '1' || $request['comprobarx'] == 1) {

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => '1.01.04.03', //cambiar a anticipo proveedores
                'descripcion'                   => 'Anticipo a Proveedores',
                'fecha'                         => $request['fecha_asiento'],
                'debe'                          => $request['total_favor'],
                'haber'                         => '0',
                'estado'                        => '1',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            ]);
            $input_comprobante = [
                'descripcion'     => $request['aaa'],
                'estado'          => '1',
                'beneficiario'    => $request['nombre_proveedor'],
                'tipo'            => '2',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_secuencia'    => $numero_factura,
                'id_pago'         => '1',
                'check'           => $request['verificar_cheque'],
                'fecha_cheque'    => $request['fecha_cheque'],
                'id_caja_banco'   => $request['banco'],
                'no_cheque'       => $request['numero_cheque'],
                'fecha_comprobante' => $request['fecha_hoy'],
                'secuencia'       => $numero_factura,
                'id_empresa'      => $id_empresa,
                'id_proveedor'    => $id_proveedor,
                'valor_pago'      => $request['total_favor'],
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
        }*/
        return ['idasiento' => $id_asiento_cabecera, 'iddebito' => $id_db];




        return $request;
    }

    public function revisar($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $registro = Ct_Debito_Bancario::findorfail($id);
        $detalle  = Ct_Debito_Bancario_detalle::where('id_debito', $id)->get();
        $id_empresa = $request->session()->get('id_empresa');
        // $id_empresa = $request->session()->get('id_empresa');
        //  $empresa= Empresa::where('id',$id_empresa)->where('estado','1')->first();
        $formas_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $divisas     = Ct_Divisas::where('estado', '1')->get();
        $banco       = Ct_Caja_Banco::where('estado', '1')->where('id_empresa',$id_empresa)->get();
        $id_empresa   = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();

        $detalle = Ct_Debito_Bancario_Detalle::where('id_debito', $id)->get();

        return view('contable/debito_bancario/edit', ['registro' => $registro,'banco'=>$banco,'empresa'=>$empresa, 'detalle' => $detalle, 'divisas' => $divisas, 'banco' => $banco, 'formas_pago' => $formas_pago]);
    }
    public function update($id, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        if (is_null($id_empresa)) {
            $id_empresa = "0992704152001";
        }
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $debito = Ct_Debito_Bancario::find($id);
        $id_asiento_cabecera = $debito->id_asiento;
        $db_cabecera = [
            'fecha'           => $request['fecha_asiento'],
            'concepto'        => $request["observacion"],
            'id_banco'        => $request["id_banco"],
            'id_divisa'       => $request["divisa"],
            'cambio'          => $request["cambio"],       
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_empresa'      => $id_empresa,
        ];
        //print_r($db_cabecera);
        $debito->update($db_cabecera);
        $cabecera = Ct_Asientos_Cabecera::find($debito->id_asiento);
        $caja_banco = Ct_Caja_Banco::find($request['id_banco']);
        $cuenta = $caja_banco->cuenta_mayor;
        if (!is_null($cabecera)) {
            $cabecera->fecha_asiento = $request['fecha_asiento'];
            $cabecera->observacion = $request['observacion'];
            $cabecera->save();
            foreach ($cabecera->detalles as $value) {
                if ($value->id_plan_cuenta != "2.01.03.01.01" && $value->id_plan_cuenta != "2.01.03.01.02" && $value->id_plan_cuenta != "1.01.04.03") {
                    $details = Ct_Asientos_Detalle::find($value->id);
                    if (!is_null($details)) {
                        $details->id_plan_cuenta = $cuenta;
                        $details->fecha=$request['fecha_asiento'];
                        $details->descripcion= $caja_banco->nombre;
                        $details->id_usuariomod = $idusuario;
                        $details->ip_modificacion = $ip_cliente;
                        $details->save();
                    }
                }
            }
        }
        return redirect()->route("debitobancario.revisar",['id'=>$id]);
    }
    public function anulacion($id, Request $request)
    {

        if (!is_null($id)) {
            $registro = Ct_Debito_Bancario::findorfail($id);
            $sb = $registro->id;
            $detalle  = Ct_Debito_Bancario_detalle::where('id_debito', $id)->get();
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $concepto = $request['concepto'];
            $id_empresa = $request->session()->get('id_empresa');
            $idusuario  = Auth::user()->id;
            if (!is_null($registro)) {
                if (!is_null($detalle)) {
                    if ($registro->estado != 0) {
                        foreach ($detalle as $value) {
                            $consulta_venta = Ct_compras::where('id', $value->id_compra)->where('estado', '>', '0')->first();
                            if (!is_null($consulta_venta)) {
                                $valor = $consulta_venta->valor_contable;
                                $suma = ($value->abono) + $valor; //u can change this with abono + valor
                                $input_actualiza = [
                                    'valor_contable'                => $suma,
                                    'estado'                        => '1',
                                    'ip_creacion'                   => $ip_cliente,
                                    'ip_modificacion'               => $ip_cliente,
                                    'id_usuariocrea'                => $idusuario,
                                    'id_usuariomod'                 => $idusuario,
                                ];
                                $consulta_venta->update($input_actualiza);
                            }
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
                $registro->update($input);
                if (!is_null($sb)) {
                    $egreso = Ct_Comprobante_Egreso::where('id_referencia', $sb)->where('estado', '<>', '0')->first();
                    if (!is_null($egreso)) {
                        $egreso->estado = 0;
                        $egreso->id_usuariomod = $idusuario;
                        $egreso->save();
                    }
                }
                $asiento = Ct_Asientos_Cabecera::findorfail($registro->id_asiento);
                $asiento->estado = 1;
                $asiento->id_usuariomod = $idusuario;
                $asiento->save();
                $detalles = $asiento->detalles;
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => strtoupper($concepto),
                    'fecha_asiento'   => $asiento->fecha_asiento,
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $registro->secuencia,
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
                return 'ok, gracias amigo.';
            }
        } else {
            return 'error';
        }
    }
    public function exportar_excel(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $consulta = null;
        $fecha2 = $request['fecha'];
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $constraints = [
            'id_asiento' => $request['buscar_asiento2'],
            'fecha'      => $request['fecha2'],
            'concepto'   => $request['concepto2'],
            'id'         => $request['numero2'],
        ];
        $consulta  = Nota_Debito::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $consulta = $consulta->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }
        $consulta = $consulta->get();
        Excel::create('Nota_Debito-' . $fecha2 . '-al-' . $fecha2, function ($excel) use ($empresa, $consulta) {
            $excel->sheet('Nota_Debito', function ($sheet) use ($empresa,  $consulta) {
                $sheet->mergeCells('A1:H1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });

                $sheet->mergeCells('A2:A2');
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B2:B2');
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('# de Asiento');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C2:C2');
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('# de Nota');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('D2:D2');
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Tipo');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('E2:E2');
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Detalle');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F2:F2');
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('G2:G2');
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Estado');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('H2:H2');
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Creado Por');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $i = 3;
                $total = 0;
                foreach ($consulta as $value) {
                    $total += $value->valor;

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(date('d-m-Y', strtotime($value->fecha)));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_asiento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue('BAN-ND');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->concepto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if (($value->estado) != 0) {
                            $cell->setValue('ACTIVO');
                        } else {
                            $cell->setValue('ANULADO');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        // $this->setSangria($cont, $cell);
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->creador->nombre1 . $value->creador->apellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });

                    $i++;
                }
                $sheet->cell('E' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                $sheet->cell('F' . $i, function ($cell) use ($total) {
                    // manipulate the cel
                    $cell->setValue($total);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                $sheet->setWidth(array(
                    'A'     =>  12,
                    'B'     =>  12,
                    'C'     =>  12,
                    'D'     =>  12,
                    'E'     =>  12,
                    'F'     =>  12,
                    'G'     =>  12,
                    'H'     =>  12,
                ));
            });
            $excel->getActiveSheet()->getStyle('A2:A2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('B2:B2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C2:C2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('D2:D2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('E2:E2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('F2:F2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('G2:G2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(28)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(50)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(20)->setAutosize(false);
        })->export('xlsx');
    }

    public function setDetalles($consulta, $sheet, $i)
    {

        foreach ($consulta as $value) {

            $sheet->cell('A' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue($value->numero2);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell);
            });


            return $i;
        }
    }

    public function imprimir_pdf(Request $request, $id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa  = $request->session()->get('id_empresa');
        $registro = Ct_Debito_Bancario::where('id', $id)->where('id_empresa', $id_empresa)->first();
        $empresa = Empresa::where('id', $registro->id_empresa)->first();
        $beneficierio = Proveedor::where('id', $registro->id_acreedor)->first();
        $caja = Ct_Caja_Banco::where('id', $registro->id_banco)->first();
        $letras = new Numeros_Letras();
        $total_str = $letras->convertir(number_format($registro->valor, 2, '.', ''), "DOLARES", "CTVS");
        $vistaurl = "contable.debito_bancario.debito_bancario_pdf";
        $view     = \View::make($vistaurl, compact('registro', 'empresa', 'total_str', 'beneficierio', 'caja'))->render();
        //dd($registro);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portraint');
        return $pdf->stream('Debito Comprobante-' . $id . '.pdf');
    }
    public function imprimir_pdf2($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $registro = Ct_Debito_Bancario::where('id', $id)->first();
        $empresa = Empresa::where('id', $registro->id_empresa)->first();
        $beneficierio = Proveedor::where('id', $registro->id_acreedor)->first();
        $caja = Ct_Caja_Banco::where('id', $registro->id_banco)->first();
        $letras = new Numeros_Letras();
        $total_str = $letras->convertir(number_format($registro->valor, 2, '.', ''), "DOLARES", "CTVS");
        $vistaurl = "contable.debito_bancario.debito_bancario_pdf";
        $view     = \View::make($vistaurl, compact('registro', 'empresa', 'total_str', 'beneficierio', 'caja'))->render();
        //dd($registro);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portraint');
        return $pdf->stream('Debito Comprobante-' . $id . '.pdf');
    }
    public function pdf_proveedor($id){

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $registro = Ct_Debito_Bancario::where('id', $id)->first();
        $empresa = Empresa::where('id', $registro->id_empresa)->first();
        $beneficierio = Proveedor::where('id', $registro->id_acreedor)->first();
        $caja = Ct_Caja_Banco::where('id', $registro->id_banco)->first();
        $letras = new Numeros_Letras();
        $total_str = $letras->convertir(number_format($registro->valor, 2, '.', ''), "DOLARES", "CTVS");
        $vistaurl = "contable.debito_bancario.pdf_debito_bancario";
        $view     = \View::make($vistaurl, compact('registro', 'empresa', 'total_str', 'beneficierio', 'caja'))->render();
        //dd($registro);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portraint');
        return $pdf->stream('Debito Comprobante-' . $id . '.pdf');

    }
    public function envioCorreo($id, Request $request)
    {
        $rol     = Ct_Debito_Bancario::find($id);
        $nomina  = $rol->ct_nomina;
        $usuario = $rol->acreedor;
        $correo = $rol->acreedor->email;
        $mes = "";
        $roldate = date('m', strtotime($rol->fecha));
        if ($roldate == 1) {
            $mes = 'Enero';
        } elseif ($roldate == 2) {
            $mes = 'Febrero';
        } elseif ($roldate == 3) {
            $mes = 'Marzo';
        } elseif ($roldate == 4) {
            $mes = 'Abril';
        } elseif ($roldate == 5) {
            $mes = 'Mayo';
        } elseif ($roldate == 6) {
            $mes = 'Junio';
        } elseif ($roldate == 7) {
            $mes = 'Julio';
        } elseif ($roldate == 8) {
            $mes = 'Agosto';
        } elseif ($roldate == 9) {
            $mes = 'Septiembre';
        } elseif ($roldate == 10) {
            $mes = 'Octubre';
        } elseif ($roldate == 11) {
            $mes = 'Noviembre';
        } elseif ($roldate == 12) {
            $mes = 'Diciembre';
        }
        //$rol_2 = $this->imprimir_pdf2($id, $request);
         $rol_2 = $this->pdf_proveedor($id, $request);

        $asunto = "Comprobante de pago " . $rol->fecha;
        $titulo = "Comprobante de pago " . $rol->fecha . '.pdf';
        Mail::send('mails.proveedores', ['usuario' => $usuario, 'nomina' => $nomina], function ($msj) use ($correo, $asunto, $rol_2, $titulo) {
            $msj->subject($asunto);
            $msj->from('rol@mdconsgroup.com', 'Sistema de Pago Proveedores SIAAM');
            $msj->to($correo);
            $msj->attachData($rol_2, $titulo, [
                'mime' => 'application/pdf',
            ]);
        });
        return 'ok';
    }
}
