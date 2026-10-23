<?php

namespace Sis_medico;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Detalle_Comprobante_Egreso;
use Sis_medico\Ct_Detalle_Cruce;
use Sis_medico\Ct_Debito_Bancario_Detalle;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_Cruce_Cuentas;
use Sis_medico\Ct_Detalle_Comprobante_Ingreso;
use Sis_medico\Ct_Detalle_Debito_Acreedores;
use Sis_medico\Ct_Credito_Acreedores;
use Sis_medico\Ct_Cliente_Retencion;
use Sis_medico\Ct_Detalle_Cruce_Clientes;
use Sis_medico\Ct_Cruce_Cuentas_Clientes;
use Sis_medico\Ct_Detalle_Credito_Clientes;
use Sis_medico\Ct_Nota_Debito_Cliente;
use Sis_medico\InvInventario;
use Sis_medico\Ct_pedidos_Compra;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_productos;
use Sis_medico\Producto;
use Excel;
use Sis_medico\ParametersConglomerada;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Globales;
use Sis_medico\Http\Controllers\ApiFacturacionController;

class Contable
{
    //awesome function 
    //ingresos varios no hay en factura de venta
    public static function obtenerSucursalAsiento($idAsiento)
    {
        $sucursal = '';
        $sucursalAsiento = Ct_Asientos_Cabecera::where('id', $idAsiento)->first(['sucursal']);
        if ($sucursalAsiento != '')
            $sucursal = $sucursalAsiento->sucursal;
        return $sucursal;
    }
    public static function recovery_price($id = "", $type = "")
    {
        if ($type == "C") { //COMPRAS
            $compras = Ct_compras::where('id', $id)->first();
            $egreso = Contable::egreso($id);
            $egreso_masivo = Contable::egreso_masivo($id);
            $cruce = Contable::cruce($id);
            $cruce_cuentas = Contable::cruce_cuentas($id);
            $retenciones = Contable::retenciones($id);
            $debito_bancario = Contable::debito($id);
            $debito_acreedores = Contable::debito_acreedores($id);
            $credito_acreedores = Contable::credito_acreedores($id);
            $restar = $egreso + $cruce + $retenciones + $debito_bancario + $debito_acreedores + $credito_acreedores + $egreso_masivo + $cruce_cuentas;
            $total = $compras->total_final - $restar;
            if ($total < 0) {
                $total = 0;
            }
            $compras->valor_contable = $total;
            $compras->save();
        } else if ($type == "V") { //VENTAS
            $ventas = Ct_ventas::where('id', $id)->first();
            $retenciones = Contable::retenciones_clientes($id);
            $ingreso = Contable::ingreso($id);
            $cruce = Contable::cruce_clientes($id);
            $cruce_cuenta = Contable::cruce_cuentas_clientes($id);
            $credito = Contable::credito_clientes($id);
            $debito_clientes = Contable::debito_clientes($id);
            $chequepost = Contable::chequepost($id);
            $nota_credito = Contable::nc_cliente($id);
            $restar = $retenciones + $ingreso + $cruce + $cruce_cuenta + $credito + $debito_clientes + $chequepost + $nota_credito;
            //dd($retenciones);
            $total = $ventas->total_final - $restar;
            if ($total < 0) {
                $total = 0; //only case when values 
            }
            $ventas->valor_contable = $total;
            $ventas->save();
        }
        return response()->json(['state' => 'success']);
    }
    // ***** COMPRAS **** 
    public static function egreso($id = "")
    {
        $data = 0;
        if ($id != "") {
            $egreso = Ct_Detalle_Comprobante_Egreso::where('id_compra', $id)->get();
            foreach ($egreso as $x) {
                if ($x->comp_egreso->estado == 1) {
                    $data += $x->abono;
                }
            }
        }
        return $data;
    }
    public static function cruce($id = "")
    {
        $data = 0;
        if ($id != "") {
            $cruce = Ct_Detalle_Cruce::where('id_factura', $id)->get();
            foreach ($cruce as $x) {
                if ($x->cabecera->estado == 1) {
                    $data += $x->total;
                }
            }
        }
        return $data;
    }
    public static function debito($id = "")
    {
        $data = 0;
        if ($id != "") {
            $debito = Ct_Debito_Bancario_Detalle::where('id_compra', $id)->get();
            foreach ($debito as $x) {
                if ($x->cabecera->estado == 1) {
                    $data += $x->abono;
                }
            }
        }
        return $data;
    }
    public static function retenciones($id = "")
    {
        $data = 0;
        if ($id != "") {
            $retenciones = Ct_Retenciones::where('id_compra', $id)->where('estado', '1')->first();
            if (!is_null($retenciones)) {
                $data = $retenciones->valor_fuente + $retenciones->valor_iva;
            }
        }
        return $data;
    }
    public static function credito_acreedores($id = "")
    {
        $data = 0;
        if ($id != "") {
            $credito = Ct_Credito_Acreedores::where('id_compra', $id)->where('estado', '1')->get();
            if (!is_null($credito)) {
                foreach ($credito as $x) {
                    $data += $x->valor_contable;
                }
            }
        }
        return $data;
    }
    public static function debito_acreedores($id = "")
    {
        $data = 0;
        if ($id != "") {
            $debito = Ct_Detalle_Debito_Acreedores::where('id_factura', $id)->where('estado', '1')->get();
            foreach ($debito as $x) {
                if ($x->cabecera->estado == 1) {
                    $data += $x->total;
                }
            }
        }
        return $data;
    }
    public static function cruce_cuentas($id = "")
    {
        $data = 0;
        if ($id != "") {
            $cruce = Ct_Cruce_Cuentas::where('id_factura', $id)->where('estado', '1')->get();
            if (!is_null($cruce)) {
                foreach ($cruce as $x) {
                    $data += $x->total;
                }
            }
        }
        return $data;
    }
    public static function egreso_masivo($id = "")
    {
        $data = 0;
        if ($id != "") {
            $egreso = Ct_Detalle_Comprobante_Egreso_Masivo::where('id_compra', $id)->get();
            foreach ($egreso as $x) {
                if (isset($x->comp_egreso)) {

                    if ($x->comp_egreso->estado == 1) {
                        $data += $x->abono;
                    }
                }
            }
        }
        return $data;
    }
    //   ***** VENTAS ******
    public static function ingreso($id = "")
    {
        $data = 0;
        if ($id != "") {
            $ingreso = Ct_Detalle_Comprobante_Ingreso::where('id_factura', $id)->get();
            foreach ($ingreso as $x) {
                if ($x->ingreso->estado == 1) {
                    $data += $x->total;
                }
            }
        }
        return $data;
    }
    public static function retenciones_clientes($id = "")
    {
        $data = 0;
        if ($id != "") {
            $retenciones = Ct_Cliente_Retencion::where('id_factura', $id)->where('estado', '1')->get();
            if (!is_null($retenciones)) {
                foreach ($retenciones as $retenciones) {
                    $data += $retenciones->valor_fuente + $retenciones->valor_iva;
                }
            }
        }
        return $data;
    }
    public static function cruce_clientes($id = "")
    {
        $data = 0;
        // en
        if ($id != "") {
            $cruce = Ct_Detalle_Cruce_Clientes::where('id_factura', $id)->get();
            foreach ($cruce as $x) {
                if ($x->cabecera->estado == 1) {
                    $data += $x->total;
                }
            }
        }
        return $data;
    }
    public static function cruce_cuentas_clientes($id = "")
    {
        $data = 0;
        if ($id != "") {
            $cruce = Ct_Cruce_Cuentas_Clientes::where('id_factura', $id)->where('estado', '1')->get();
            if (!is_null($cruce)) {
                foreach ($cruce as $x) {
                    $data += $x->total;
                }
            }
        }
        return $data;
    }
    public static function credito_clientes($id = "")
    {
        $data = 0;
        if ($id != "") {
            $credito = Ct_Detalle_Credito_Clientes::where('id_factura', $id)->get();
            foreach ($credito as $x) {
                if ($x->cabecera->estado == 1) {
                    $data += $x->abono;
                }
            }
        }
        return $data;
    }
    public static function credito_clientes_($id = "")
    {
        $data = 0;
        if ($id != "") {
            $credito = Ct_Nota_Credito_Clientes::where('id_factura', $id)->get();
            foreach ($credito as $x) {
                if ($x->estado == 1) {
                    $data += $x->total_credito;
                }
            }
        }
        return $data;
    }
    public static function debito_clientes($id = "")
    {
        $data = 0;
        if ($id != "") {
            $debito = Ct_Nota_Debito_Cliente::where('id_factura', $id)->where('estado', '1')->first();
            if (!is_null($debito)) {
                $data = $debito->valor_contable;
            }
        }
        return $data;
    }
    public static function chequepost($id = "")
    {
        $data = 0;
        if ($id != "") {
            $ingreso = Ct_Detalle_Cheque_Post::where('id_factura', $id)->get();
            foreach ($ingreso as $x) {
                if ($x->cabecera->estado == 1) {
                    $data += $x->total;
                }
            }
        }
        return $data;
    }

    public static function nc_cliente($id_venta)
    {
        $data = 0;
        if ($id_venta != "") {
            $credito = Ct_Nota_Credito_Clientes::where('id_factura', $id_venta)->where('estado', 1)->get();
            foreach ($credito as $x) {
                $data += $x->total_credito;
            }
        }
        return $data;
    }
    public static function kardex_asiento($id = "")
    {
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = Auth::user()->id;
        $detalle_asiento = Ct_detalle_venta::where('id_ct_ventas', $id)->get();
        $coste = 0;
        $dangers = "";
        DB::beginTransaction();
        //$msj = "no";
        try {
            foreach ($detalle_asiento as $x) {
                $id_producto = $x->id_ct_productos;
                $xx = Ct_productos::where('codigo', $id_producto)->first();
                $verificar = Ct_productos_insumos::where('id_producto', $xx->id)->first();
                if (!is_null($verificar)) {
                    $producto = Producto::find($verificar->id_insumo);
                    if (!is_null($producto)) {
                        $inventario = InvInventario::where('id_producto', $producto->id)->first(); //bodega
                        if (!is_null($inventario)) {
                            $coste += $inventario->costo_promedio;
                        } else {
                            $dangers .= "No tiene productos en esta bodega -" . $x->bodega . " Codigo:  " . $producto->codigo;
                        }
                    }
                } else {
                    $dangers .= "No existe ligue con el producto -" . $id_producto;
                }
            }
            if ($coste == 0) {
                $coste = 1; //arreglar
            }
            $id_empresa   = session()->get('id_empresa');
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId([
                'fecha_asiento'   => date('Y-m-d H:i:s'),
                'fact_numero'     => '0',
                'id_empresa'      => $id_empresa,
                'observacion'     => 'COSTES DE MERCADERIA POR PRODUCTO',
                'valor'           => $coste,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);
            $plan_cuentas = Ct_Globales::where('id_modulo', '2')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_cuentas->haber,
                'descripcion'         => $plan_cuentas->haberc->nombre,
                'fecha'               => date('Y-m-d H:i:s'),
                'haber'                => '0',
                'debe'               => $coste,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_cuentas->debe,
                'descripcion'         => $plan_cuentas->debec->nombre,
                'fecha'               => date('Y-m-d H:i:s'),
                'debe'               => '0',
                'haber'                => $coste,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);

            DB::commit();
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            $dangers = $e->getMessage();
        }

        $response['state'] = '1';
        $response['observations'] = $dangers;
        return $response;
    }
    public static function recovery_by_model($type = "", $module = "", $id = "")
    {
        $message = "";
        if ($type != "") {
            if ($type == 'O') {
                if ($module == 'EG') {
                    $egreso = Ct_Comprobante_Egreso::find($id);
                    $arraymodules['egreso'] = $egreso;
                    $arraymodules['detalles'] = $egreso->detalles;
                    //dd($egreso->detalles);
                    $xp = [];
                    foreach ($egreso->detalles as $z) {
                        array_push($xp, $z->compras);
                    }
                    $arraymodules['compras'] = $xp;
                    //dd($xp);    
                    return response()->json($arraymodules);
                } else if ($module == 'EGV') {
                    $varios = Ct_Comprobante_Egreso_Varios::find($id);
                    $arraymodules['egreso'] = $varios;
                    $arraymodules['detalles'] = $varios->detalles;
                    return response()->json($arraymodules);
                } else if ($module == "CI") {
                    $ingresos =  Ct_Comprobante_Ingreso::find($id);
                    $arraymodules['ingreso'] = $ingresos;
                    $arraymodules['detalles'] = $ingresos->detalle;
                    $xp = [];
                    foreach ($ingresos->detalle as $z) {
                        array_push($xp, $z->ventas);
                    }
                    $arraymodules['ventas'] = $xp;
                    return response()->json($arraymodules);
                } else if ($module == "DEP") {
                    $deposito = Ct_Deposito_Bancario::find($id);
                    $arraymodules['deposito'] = $deposito;
                    $arraymodules['detalle'] = $deposito->detalles;
                    $xp = [];
                    foreach ($deposito->detalles as $z) {
                        if (isset($z->ingreso->cabecera_ingreso)) {
                            array_push($xp, $z->ingreso->cabecera_ingreso);
                        } else if (isset($z->ingreso->cabecera_ingresov)) {
                            array_push($xp, $z->ingreso->cabecera_ingresov);
                        }
                    }
                    $arraymodules['ingresos'] = $xp;
                    return response()->json($arraymodules);
                } else if ($module == "DB") {
                    $egreso = Ct_Debito_Bancario::find($id);
                    $arraymodules['egreso'] = $egreso;
                    $arraymodules['detalles'] = $egreso->detalles;
                    //dd($egreso->detalles);
                    $xp = [];
                    foreach ($egreso->detalles as $z) {
                        array_push($xp, $z->compras);
                    }
                    $arraymodules['compras'] = $xp;
                    return response()->json($arraymodules);
                } else if ($module == "C") {
                    //show all registers that we have
                    $compras = Ct_compras::find($id);
                    $arraymodules['id'] = $compras->id;
                    $arraymodules['nro_comprobante'] = $compras->numero;
                    $arraymodules['asiento'] = $compras->id_asiento_cabecera;
                    if ($compras->tipo == 1) {
                        $arraymodules['tipo'] = 'COM-FA';
                    } else if ($compras->tipo == 2) {
                        $arraymodules['tipo'] = 'COM-FACT';
                    }
                    $arraymodules['proveedor'] = $compras->proveedorf->razonsocial;
                    $arraymodules['observacion'] = $compras->observacion;
                    $arraymodules['total_final'] = $compras->total_final;
                    $arraymodules['valor_contable'] = $compras->valor_contable;
                    $arraymodules['usuariocrea'] = $compras->usuario->nombre1 . ' ' . $compras->usuario->apellido1;
                    $arraymodules['creado'] = $compras->created_at;
                    $arraymodules['actualizado'] = $compras->updated_at;
                    if (isset($compras->retenciones)) {
                        if (count($compras->retenciones) > 0) {
                            foreach ($compras->retenciones as $x) {
                                if ($x->estado == 1) {
                                    $arraymodules['retencion'] = $x->id;
                                }
                            }
                        }
                    }
                    $xps = [];
                    if (isset($compras->egresos)) {
                        if (count($compras->egresos) > 0) {
                            foreach ($compras->egresos as $x) {
                                if (isset($x->comp_egreso)) {
                                    if ($x->comp_egreso->estado == 1) {
                                        array_push($xps, $x->comp_egreso->id);
                                    }
                                }
                            }
                            $arraymodules['egresos'] = $xps;
                        }
                    }
                    $pl = [];
                    if (isset($compras->cruce)) {
                        if (count($compras->cruce) > 0) {
                            foreach ($compras->cruce as $t) {
                                if (isset($t->cabecera)) {
                                    if ($t->cabecera->estado == 1) {
                                        array_push($pl, $t->cabecera->id);
                                    }
                                }
                            }
                            $arraymodules['cruce'] = $pl;
                        }
                    }
                    $pl2 = [];
                    if (isset($compras->cruce_cuentas)) {
                        if (count($compras->cruce_cuentas) > 0) {
                            foreach ($compras->cruce_cuentas as $t) {

                                array_push($pl2, $t->id);
                            }
                            $arraymodules['cruce_cuentas'] = $pl2;
                        }
                    }
                    $lx = [];
                    if (isset($compras->bndebito)) {
                        if (count($compras->bndebito) > 0) {
                            foreach ($compras->bndebito as $d) {
                                if (isset($d->cabecera)) {
                                    if ($d->cabecera->estado == 1) {
                                        array_push($lx, $d->cabecera->id);
                                    }
                                }
                            }
                            $arraymodules['debito'] = $lx;
                        }
                    }
                    $deb = [];
                    if (isset($compras->debitoacreedor)) {
                        if (count($compras->debitoacreedor) > 0) {
                            foreach ($compras->debitoacreedor as $d) {
                                if (isset($d->cabecera)) {
                                    if ($d->cabecera->estado == 1) {
                                        array_push($deb, $d->cabecera->id);
                                    }
                                }
                            }
                            $arraymodules['debito_acreedor'] = $deb;
                        }
                    }
                    $xp = [];
                    if (isset($compras->credito_acreedor)) {
                        if (count($compras->credito_acreedor) > 0) {
                            foreach ($compras->credito_acreedor as $x) {
                                if ($x->estado == 1) {
                                    array_push($xp, $x->id);
                                }
                            }
                            $arraymodules['credito_acreedores'] = $xp;
                        }
                    }
                    $masv = [];
                    if (isset($compras->masivos)) {
                        if (count($compras->masivos) > 0) {
                            foreach ($compras->masivos as $c) {
                                if (isset($c->comp_egreso)) {
                                    if ($c->comp_egreso->estado == 1) {
                                        array_push($masv, $c->comp_egreso->id);
                                    }
                                }
                            }
                            $arraymodules['masivos'] = $masv;
                        }
                    }

                    return response()->json($arraymodules);
                } else if ($module == "V") {
                    $ventas = Ct_ventas::find($id);
                    $arraymodules = [];
                    $arraymodules['id'] = $ventas->id;
                    $arraymodules['comprobante'] = $ventas->nro_comprobante;
                    $arraymodules['cliente'] = $ventas->cliente->nombre;
                    $arraymodules['asiento'] = $ventas->id_asiento;
                    $arraymodules['total_final'] = $ventas->total_final;
                    $arraymodules['valor_contable'] = $ventas->valor_contable;
                    $arraymodules['usuariocrea'] = $ventas->usuario->nombre1 . ' ' . $ventas->usuario->apellido1;
                    $arraymodules['creado'] = $ventas->created_at;
                    $arraymodules['actualizado'] = $ventas->updated_at;
                    if ($ventas->omni == 1) {
                        $arraymodules['informacion'] = "OMNI";
                    }
                    if (isset($ventas->retenciones)) {
                        $rete = Ct_Cliente_Retencion::where('id_factura', $id)->get();
                        $contador = 0;
                        foreach ($rete as $value) {
                            if (($value->estado == 1)) {
                                $arraymodules['retencion' . $contador] = $value->id;
                            } else {
                                $arraymodules['retencionAnulado' . $contador] = $value->id;
                            }
                            $contador++;
                        }
                    }
                    $ing = [];
                    if (isset($ventas->comp_ingreso)) {
                        if (count($ventas->comp_ingreso) > 0) {
                            foreach ($ventas->comp_ingreso as $ig) {
                                if (isset($ig->ingreso)) {
                                    if ($ig->ingreso->estado == 1) {
                                        array_push($ing, $ig->ingreso->id);
                                    }
                                }
                            }
                            $arraymodules['ingreso'] = $ing;
                        }
                    }
                    $cruce = [];
                    if (isset($ventas->cruce)) {
                        if (count($ventas->cruce) > 0) {
                            foreach ($ventas->cruce as $x) {
                                if (isset($x->cabecera)) {
                                    if ($x->cabecera->estado == 1) {
                                        array_push($cruce, $x->cabecera->id);
                                    }
                                }
                            }
                            $arraymodules['cruce'] = $cruce;
                        }
                    }
                    $cheque = [];
                    if (isset($ventas->chequepost)) {
                        if (count($ventas->chequepost) > 0) {
                            foreach ($ventas->chequepost as $ch) {
                                if (isset($ch->cabecera)) {
                                    if ($ch->cabecera->estado == 1) {
                                        array_push($cheque, $ch->cabecera->id);
                                    }
                                }
                            }
                            $arraymodules['chequepost'] = $cheque;
                        }
                    }
                    $xp = [];
                    if (isset($ventas->cruce_cuentas)) {
                        if (count($ventas->cruce_cuentas) > 0) {
                            foreach ($ventas->cruce_cuentas as $x) {
                                if ($x->estado != 0)
                                    array_push($xp, $x->id);
                            }
                            $arraymodules['cruce_cuentas'] = $xp;
                        }
                    }
                    $credi = [];
                    if (isset($ventas->credito)) {
                        if (count($ventas->credito) > 0) {
                            foreach ($ventas->credito as $c) {
                                if (isset($c->cabecera)) {
                                    if ($c->cabecera->estado == 1) {
                                        array_push($cheque, $c->cabecera->id);
                                    }
                                }
                            }
                            $arraymodules['credito'] = $credi;
                        }
                    }
                    return response()->json($arraymodules);
                } else if ($module == "DEP-I") {
                    //$detalle= Ct_Detalle_Pago_Ingreso::find($id);
                    $deposito = Ct_Detalle_Deposito_Bancario::where('id_ingreso', $id)->first();
                    // dd($deposito);
                    $arraymodules['deposito'] = $deposito->deposito_bancario->id;
                    $arraymodules['ingreso'] = $deposito->ingreso->id;
                    return response()->json($arraymodules);
                } else if ($module == "ASIENTO") {
                    $arraymodules['anio'] = $id['anio'];
                    $sql = "SELECT
                            id_asiento_cabecera,
                            fecha_asiento as fecha,
                            SUM(debe) AS debe_n,
                            SUM(haber) AS haber_n
                        FROM ct_asientos_detalle, ct_asientos_cabecera
                            WHERE id_asiento_cabecera <> 24904 AND
                            ct_asientos_detalle.id_asiento_cabecera = ct_asientos_cabecera.id
                            AND YEAR(fecha_asiento) =" . $id['anio'] . "
                            AND ct_asientos_cabecera.id_empresa=" . $id['id_empresa'] . "
                        GROUP BY id_asiento_cabecera
                        HAVING SUM(debe) <> SUM(haber)
                        ORDER BY id_asiento_cabecera DESC";
                    $asientos = DB::select(DB::raw($sql));
                    $arraynew = [];
                    foreach ($asientos as $a) {
                        $xl['id_asiento'] = $a->id_asiento_cabecera;
                        $xl['modulo'] = Contable::recovery_by_asiento($a->id_asiento_cabecera);
                        $xl['debe'] = $a->debe_n;
                        $xl['haber'] = $a->haber_n;
                        $xl['fecha'] = $a->fecha;
                        array_push($arraynew, $xl);
                    }
                    $arraymodules['total'] = count($asientos);
                    $arraymodules['asientos'] = $arraynew;
                    return response()->json($arraymodules);
                }
            } else if ($type == 'F') {
                $message = Contable::fix_secuencia($module, $id);
                return $message;
            } else if ($type == 'R') {
                $message = Contable::recovery_price($id, $module);
                return $message;
            } else if ($type == 'S') {
                $message = Contable::recovery_by_asiento($id);
            } else if ($type == 'RE-C') {
                $compras = Ct_compras::where('id_empresa', $id)->where('estado', '<>', '0')->get();
                $egresos = Ct_Debito_Bancario::where('id_empresa', $id)->where('estado', '1')->get();
                foreach ($egresos as $x) {
                    $proveedor = $x->id_acreedor;
                    foreach ($x->detalles as $p) {
                        //dd($p->compras);
                        if (isset($p->compras)) {
                            $f = $p->compras->proveedor;
                            $numero = $p->compras->numero;
                            if ($proveedor != $f) {
                                $xa = Ct_compras::where('numero', 'like', '%' . $numero . '%')->where('estado', '<>', '0')->where('proveedor', $proveedor)->first();
                                if ($xa != null) {
                                    $defa = Ct_Debito_Bancario_Detalle::find($p->id);
                                    $defa->id_compra = $xa->id;
                                    $defa->save();
                                }
                            }
                        }
                    }
                }
                //dd($compras);
                foreach ($compras as $c) {
                    $message = Contable::recovery_price($c->id, "C");
                }
                return response()->json($message);
            } else if ($type == "MC") {
                $compras = Ct_compras::where('id_empresa', $id)->where('estado', '<>', '0')->get();
                foreach ($compras as $c) {
                    $message = Contable::recovery_price($c->id, "C");
                }
                return $message;
            } else if ($type == "RF") {
                $haberplan = ['2.01.03.01.01', '2.01.03.01.02'];
                $compras = Ct_compras::where('id_empresa', $id)->where('estado', '<>', '0')->get();
                //dd($compras);
                foreach ($compras as $c) {
                    if ($c->descuento > 0) {
                        $totaldebe = $c->subtotal + $c->descuento;
                        $totalhaber = $c->total_final;
                        $debe = Ct_Asientos_Detalle::where('id_asiento_cabecera', $c->id_asiento_cabecera)->where('id_plan_cuenta', '1.01.03.01.02')->first();
                        if (!is_null($debe)) {
                            $debe->debe = $totaldebe;
                            $debe->save();
                        }
                        $haber = Ct_Asientos_Detalle::where('id_asiento_cabecera', $c->id_asiento_cabecera)->whereIn('id_plan_cuenta', $haberplan)->first();
                        if (!is_null($haber)) {
                            $haber->haber = $totalhaber;
                            $haber->save();
                        }
                    }
                }
                return 'ok gracias amigo';
            } else if ($type == "MV") {
                $compras = Ct_ventas::where('id_empresa', $id)->where('estado', '<>', '0')->get();
                foreach ($compras as $c) {
                    $message = Contable::recovery_price($c->id, "V");
                }
                return $message;
            } else if ($type == 'CR') {
                $egreso = Ct_Detalle_Pago_Cruce_Prov::where('id_comp_ingreso', $id)->get();
                //dd($egreso);
                $total = 0;
                foreach ($egreso as $x) {
                    if (isset($x->tras)) {
                        if ($x->tras->estado == 1) {
                            $total += $x->abono;
                        }
                    }
                }
                $e = Ct_Comprobante_Egreso::find($id);
                if (!is_null($e)) {
                    if ($total > 0) {
                        $vax = $e->valor_pago - $total;
                        if ($vax < 0) {
                            $vax = 0;
                        }
                        $e->valor_pago = $vax;
                        $e->save();
                    }
                }

                //ya entonces es en debito que se cae

            } else if ($type == "PR") {
                $s = Contable::asiento();
                return $s;
            } else if ($type == "FS") {
                //dd('aa');
                $a = Contable::fix_comprobante($module, $id);
                return $a;
            } else if ($type == 'FDATE') {
                $a = Contable::fixdate($id);
            } else if ($type == 'SUP') {
                $message = Contable::verify_price($id);
            } else if ($type == 'FIX-AF') {
                $message = Contable::masivo_arreglar($id);
            }
        }
        return response()->json(['state' => '1', 'content' => $message]);
    }
    public static function recovery_by_asiento($id)
    {
        $modulos = [];
        //compras
        $compras = Contable::compras($id);
        $ventas = Contable::ventas($id);
        $bancos = Contable::bancos($id);
        $modulos['compra'] = $compras;
        $modulos['venta'] = $ventas;
        $modulos['bancos'] = $bancos;
        return response()->json($modulos);
    }
    public static function bancos($id)
    {
        $models = [];
        $debito = Nota_Debito::where('id_asiento', $id)->first();
        if (!is_null($debito)) {
            $models['module'] = 'NOTA DE DEBITO-B';
            $models['id'] = $debito->id;
            $models['concepto'] = $debito->concepto;
            $models['empresa'] = $debito->empresa;
            $models['total'] = $debito->valor;
        }
        $debito = Ct_Nota_Credito::where('id_asiento', $id)->first();
        if (!is_null($debito)) {
            $models['module'] = 'NOTA DE CREDITO-B';
            $models['id'] = $debito->id;
            $models['concepto'] = $debito->concepto;
            $models['empresa'] = $debito->empresa;
            $models['total'] = $debito->valor;
        }
        $transferencia = Ct_Transferencia_Bancaria::where('id_asiento', $id)->first();
        if (!is_null($transferencia)) {
            $models['module'] = 'TRANSFERENCIA-B';
            $models['id'] = $transferencia->id;
            $models['concepto'] = $transferencia->concepto;
            $models['empresa'] = $transferencia->empresa;
            $models['total'] = $transferencia->total;
        }
        $deposito = Ct_Deposito_Bancario::where('id_asiento', $id)->first();
        if (!is_null($deposito)) {
            $models['module'] = 'DEPOSITO-B';
            $models['id'] = $deposito->id;
            $models['concepto'] = $deposito->concepto;
            $models['empresa'] = $deposito->empresa;
            $models['total'] = $deposito->total;
        }
        return $models;
    }
    public static function compras($id)
    {
        $models = [];
        $compra = Ct_compras::where('id_asiento_cabecera', $id)->first();
        if (!is_null($compra)) {
            $models['module'] = 'COMPRAS';
            $models['id'] = $compra->id;
            $models['concepto'] = $compra->descripcion;
            $models['empresa'] = $compra->id_empresa;
            $models['comprobante'] = $compra->nro_comprobante;
            $models['total'] = $compra->valor_contable;
            $models['sucursal'] = $compra->sucursal;
            $models['punto_emision'] = $compra->punto_emision;
        }
        $retenciones = Ct_Retenciones::where('id_asiento_cabecera', $id)->first();
        if (!is_null($retenciones)) {
            $models['module'] = 'RETENCION-A';
            $models['id'] = $retenciones->id;
            $x = $retenciones->valor_fuente;
            $y = $retenciones->valor_iva;
            $models['concepto'] = $retenciones->descripcion;
            $models['empresa'] = $retenciones->id_empresa;
            $models['comprobante'] = $retenciones->nro_comprobante;
            $models['total'] = $x + $y;
            $models['sucursal'] = $retenciones->sucursal;
            $models['punto_emision'] = $retenciones->punto_emision;
        }
        $egresos =  Ct_Comprobante_Egreso::where('id_asiento_cabecera', $id)->first();
        if (!is_null($egresos)) {
            $models['module'] = 'EGRESO-A';
            $models['id'] = $egresos->id;
            $models['fecha'] = $egresos->fecha;
            $models['concepto'] = $egresos->descripcion;
            $models['comprobante'] = $egresos->secuencia;
            $models['empresa'] = $egresos->id_empresa;
            $models['total'] = $egresos->valor_pago;
        }
        $egresosv =  Ct_Comprobante_Egreso_Varios::where('id_asiento_cabecera', $id)->first();
        if (!is_null($egresosv)) {
            $models['module'] = 'EGRESOV-A';
            $models['id'] = $egresosv->id;
            $models['fecha'] = $egresosv->fecha;
            $models['empresa'] = $egresosv->id_empresa;
            $models['concepto'] = $egresosv->descripcion;
            $models['comprobante'] = $egresosv->secuencia;
            $models['total'] = $egresosv->valor_pago;
        }
        $egresosm =  Ct_Comprobante_Egreso_Masivo::where('id_asiento_cabecera', $id)->first();
        if (!is_null($egresosm)) {
            $models['module'] = 'EGRESOM-A';
            $models['id'] = $egresosm->id;
            $models['fecha'] = $egresosm->fecha_comprobante;
            $models['empresa'] = $egresosm->id_empresa;
            $models['concepto'] = $egresosm->descripcion;
            $models['comprobante'] = $egresosm->secuencia;
            $models['total'] = $egresosm->valor_pago;
        }

        $debito =  Ct_Debito_Bancario::where('id_asiento', $id)->first();
        if (!is_null($debito)) {
            $models['module'] = 'DEBITOB-A';
            $models['id'] = $debito->id;
            $models['fecha'] = $debito->fecha;
            $models['empresa'] = $debito->empresa;
            $models['concepto'] = $debito->concepto;
            $models['comprobante'] = $debito->secuencia;
            $models['total'] = $debito->valor;
        }
        $debitoacreedor = Ct_Debito_Acreedores::where('id_asiento_cabecera', $id)->first();
        if (!is_null($debitoacreedor)) {
            $models['module'] = 'DEBITOAC-A';
            $models['id'] = $debitoacreedor->id;
            $models['empresa'] = $debitoacreedor->id_empresa;
            $models['concepto'] = $debitoacreedor->concepto;
            $models['total'] = $debitoacreedor->valor_contable;
            $models['fecha'] = $debitoacreedor->fecha_factura;
        }
        $cruce = Ct_Cruce_Valores::where('id_asiento_cabecera', $id)->first();
        if (!is_null($cruce)) {
            $models['module'] = 'CRUCE-A';
            $models['id'] = $cruce->id;
            $models['empresa'] = $cruce->id_empresa;
            $models['concepto'] = $cruce->detalle;
            $models['total'] = $cruce->total_disponible;
            $models['fecha'] = $cruce->fecha_pago;
        }
        $credito = Ct_Credito_Acreedores::where('id_asiento_cabecera', $id)->first();
        if (!is_null($credito)) {
            $models['module'] = 'CREDITOA-A';
            $models['empresa'] = $credito->id_empresa;
            $models['id'] = $credito->id;
            $models['concepto'] = $credito->detalle;
            $models['total'] = $credito->valor_contable;
            $models['fecha'] = $credito->fecha;
        }
        return $models;
    }
    public static function ventas($id)
    {
        $models = [];
        $compra = Ct_ventas::where('id_asiento', $id)->first();
        if (!is_null($compra)) {
            $models['module'] = 'VENTA';
            $models['id'] = $compra->id;
            $models['empresa'] = $compra->id_empresa;
            $models['concepto'] = $compra->observaciones;
            $models['total'] = $compra->total_ingreso;
            $models['fecha'] = $compra->fecha;
            $models['sucursal'] = $compra->sucursal;
            $models['punto_emision'] = $compra->punto_emision;
        }
        $ingreso = Ct_Comprobante_Ingreso::where('id_asiento_cabecera', $id)->first();
        if (!is_null($ingreso)) {
            $models['module'] = 'INGRESO-C';
            $models['id'] = $ingreso->id;
            $models['empresa'] = $ingreso->id_empresa;
            $models['concepto'] = $ingreso->observaciones;
            $models['total'] = $ingreso->total_ingreso;
            $models['fecha'] = $ingreso->fecha;
        }
        $retenciones = Ct_Cliente_Retencion::join('ct_ventas as v', 'ct_cliente_retencion.id_factura', 'v.id')->where('id_asiento_cabecera', $id)->first([
            'ct_cliente_retencion.id',
            'ct_cliente_retencion.id_empresa',
            'ct_cliente_retencion.valor_fuente',
            'ct_cliente_retencion.valor_iva',
            'ct_cliente_retencion.descripcion',
            'ct_cliente_retencion.nro_comprobante',
            'v.nro_comprobante as venta_comprobante'
        ]);
        if (!is_null($retenciones)) {
            $models['module'] = 'RETENCION-C';
            $models['id'] = $retenciones->id;
            $models['empresa'] = $retenciones->id_empresa;
            $x = $retenciones->valor_fuente;
            $y = $retenciones->valor_iva;
            $models['concepto'] = $retenciones->descripcion;
            $models['comprobante'] = $retenciones->nro_comprobante;
            $models['total'] = $x + $y;
            $models['nro_comprobante'] = $retenciones->venta_comprobante;
        }
        $cruce = Ct_Cruce_Valores_Cliente::where('id_asiento_cabecera', $id)->first();
        if (!is_null($cruce)) {
            $models['module'] = 'CRUCE-C';
            $models['id'] = $cruce->id;
            $models['empresa'] = $cruce->id_empresa;
            $models['concepto'] = $cruce->detalle;
            $models['total'] = $cruce->total_disponible;
            $models['fecha'] = $cruce->fecha_pago;
        }
        $chequepost = Ct_Cheques_Post::where('id_asiento_cabecera', $id)->first();
        if (!is_null($chequepost)) {
            $models['module'] = 'CHEQUEPOST-C';
            $models['id'] = $chequepost->id;
            $models['empresa'] = $chequepost->id_empresa;
            $models['concepto'] = $chequepost->observaciones;
            $models['total'] = $chequepost->total_ingreso;
            $models['fecha'] = $chequepost->fecha;
        }
        $cruce_cuentas = Ct_Cruce_Cuentas_Clientes::where('id_asiento_cabecera', $id)->first();
        if (!is_null($cruce_cuentas)) {
            $models['module'] = 'CRUCECUENTAS-C';
            $models['id'] = $cruce_cuentas->id;
            $models['empresa'] = $cruce_cuentas->id_empresa;
            $models['concepto'] = $cruce_cuentas->observaciones;
            $models['total'] = $cruce_cuentas->total;
            $models['fecha'] = $cruce_cuentas->fecha;
        }
        $credito = Ct_Nota_Credito_Clientes::where('id_asiento_cabecera', $id)->first();
        if (!is_null($credito)) {
            $models['module'] = 'CREDITOCLIENTES-C';
            $models['id'] = $credito->id;
            $models['empresa'] = $credito->id_empresa;
            $models['concepto'] = $credito->concepto;
            $models['total'] = $credito->total_credito;
            $models['fecha'] = $credito->fecha;
        }
        return $models;
    }
    public static function groupBy($data = [], $key = "")
    {
        $result = array();
        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[""][] = $val;
            }
        }

        return $result;
    }
    public static function getProductPrices($data, $tipo = "", $id_seguro = "", $ambulatorio = "")
    {
        //if u want sum values and group by values
        $groups = array();
        foreach ($data as $ks => $item) {
            //dd($item);
            $key = $item['codigo'];
            //only when key is distinc to null
            if (!is_null($key)) {
                if (!is_null($item['nombre'])) {
                    if ($tipo == "conglomerada") {
                        if ($id_seguro == 4) {
                            if ($ambulatorio == 1) {
                                $key = $item['codigo'];
                            } else {
                                $getParameters = ParametersConglomerada::getHumana($key);
                                if ($getParameters != false) {
                                    $key = $getParameters;
                                }
                            }
                        } else {
                            if ($ambulatorio == 1) {

                                $key = $item['codigo'];
                            }
                        }
                    }
                    if (!array_key_exists($key, $groups)) {
                        $codigo = $item['codigo'];
                        $nombre = $item['nombre'];
                        if ($tipo == "conglomerada") {
                            if ($id_seguro == 4) {
                                if ($ambulatorio == 1) {
                                    $codigo = $item['codigo'];
                                } else {
                                    $getParameters = ParametersConglomerada::getHumana($codigo);
                                    //dd($getParameters);
                                    if ($getParameters != false) {
                                        $codigo = $getParameters;
                                    }
                                }
                            } else {
                                if ($ambulatorio == 1) {
                                    $codigo = $item['codigo'];
                                }
                            }
                        }
                        $nombre       = Ct_productos::where('codigo', $codigo)->first();
                        $nombre       = $nombre->descripcion; //no hay valicacion aqui
                        $groups[$key] = array(
                            'codigo'               => $codigo,
                            'nombre'               => $nombre,
                            'fecha_procedimiento'  => $item['fecha_procedimiento'],
                            'nombre_procedimiento' => $item['nombre_procedimiento'],
                            'id_paciente'          => $item['id_paciente'],
                            'precio'               => $item['precio'],
                            'precioneto'           => $item['precioneto'],
                            'cantidad'             => $item['cantidad'],
                            'descuento'            => $item['descuento'],
                            'detalle'              => $item['detalle'],
                        );
                    } else {
                        if ($tipo == "conglomerada") {
                            $groups[$key]['precio'] = $groups[$key]['precio'] + $item['precio']; //no working date 26 January 2021
                        } else {
                            $groups[$key]['cantidad'] = $groups[$key]['cantidad'] + $item['cantidad'];
                        }
                        $groups[$key]['detalle']    = $groups[$key]['detalle'];
                        $groups[$key]['precioneto'] = $groups[$key]['precioneto'] + $item['precioneto'];
                        $groups[$key]['descuento']  = $groups[$key]['descuento'] + $item['descuento'];
                    }
                }
            }
        }
        //dd($groups);
        return $groups;
    }
    public static function fix_secuencia($type = "", $empresa = "")
    {
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];

        if ($type == "CI") {
            $empresa = Empresa::all();
            foreach ($empresa as $x) {
                $ingreso = Ct_Cruce_Valores::where('id_empresa', $x->id)->get();
                $contador = 1;
                foreach ($ingreso as $i) {
                    $numero_factura = str_pad($contador, 9, "0", STR_PAD_LEFT);
                    $ix = Ct_Cruce_Valores::find($i->id);
                    $ix->secuencia = $numero_factura;
                    $ix->save();
                    $contador++;
                }
            }
        } else if ($type == "CI-ASIENTO") {
            $empresa = Empresa::all();
            foreach ($empresa as $x) {
                if ($x->id == '0993075000001') {
                    $ingreso = Ct_Comprobante_Ingreso::where('id_empresa', $x->id)->where('estado', '<>', '0')->orderBy('fecha', 'ASC')->get();
                    //dd($ingreso);
                    foreach ($ingreso as $px) {
                        $total = $px->total_ingreso;
                        $verificar = Ct_Asientos_Detalle::where('id_asiento_cabecera', $px->id_asiento_cabecera)->where('id_plan_cuenta', '2.01.10.01.01')->first();
                        if (!is_null($verificar)) {
                            $total = $px->total_ingreso - $verificar->haber;
                        }
                        $asiento = Ct_Asientos_Detalle::where('id_asiento_cabecera', $px->id_asiento_cabecera)->delete();
                        $asiento_final = Ct_Asientos_Cabecera::find($px->id_asiento_cabecera);
                        if (!is_null($asiento_final)) {
                            if (!is_null($verificar)) {
                                $desc_cuenta2 = Plan_Cuentas::where('id', '2.01.10.01.01')->first();
                                Ct_Asientos_Detalle::create([
                                    'id_asiento_cabecera' => $px->id_asiento_cabecera,
                                    'id_plan_cuenta'      => '2.01.10.01.01',
                                    'descripcion'         => $desc_cuenta2->nombre,
                                    'fecha'               => $px->fecha,
                                    'haber'               => $verificar->haber,
                                    'debe'                => '0',
                                    'estado'              => '1',
                                    'id_usuariocrea'      => $px->id_usuariocrea,
                                    'id_usuariomod'       => $px->id_usuariocrea,
                                    'ip_creacion'         => $ip_cliente,
                                    'ip_modificacion'     => $ip_cliente,
                                ]);
                            }
                            $desc_cuenta         = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                            Ct_Asientos_Detalle::create([
                                'id_asiento_cabecera' => $px->id_asiento_cabecera,
                                'id_plan_cuenta'      => '1.01.02.05.01',
                                'descripcion'         => $desc_cuenta->nombre,
                                'fecha'               => $px->fecha,
                                'haber'               => $total,
                                'debe'                => '0',
                                'estado'              => '1',
                                'id_usuariocrea'      => $px->id_usuariocrea,
                                'id_usuariomod'       => $px->id_usuariocrea,
                                'ip_creacion'         => $ip_cliente,
                                'ip_modificacion'     => $ip_cliente,
                            ]);
                            $desc_cuenta  = Plan_Cuentas::where('id', '1.01.01.1.01')->first();
                            Ct_Asientos_Detalle::create([
                                'id_asiento_cabecera' => $px->id_asiento_cabecera,
                                'id_plan_cuenta'      => '1.01.01.1.01',
                                'descripcion'         => $desc_cuenta->nombre,
                                'fecha'               => $px->fecha,
                                'debe'                => $px->total_ingreso,
                                'haber'               => '0',
                                'estado'              => '1',
                                'id_usuariocrea'      => $px->id_usuariocrea,
                                'id_usuariomod'       => $px->id_usuariocrea,
                                'ip_creacion'         => $ip_cliente,
                                'ip_modificacion'     => $ip_cliente,
                            ]);
                        }
                    }
                }
            }
        } else if ($type == "DEBITO") {

            $ingreso = Ct_Debito_Bancario::where('id_empresa', $empresa)->where('estado', '<>', '0')->orderBy('fecha', 'ASC')->get();
            $contador = 1;
            foreach ($ingreso as $i) {
                $numero_factura = str_pad($contador, 10, "0", STR_PAD_LEFT);
                $ix = Ct_Debito_Bancario::find($i->id);
                $ix->secuencia = $numero_factura;
                $ix->save();
                $contador++;
            }
        } else if ($type == 'afsjhasukhdashdash') {
            $detalle = ct_detalle_retenciones::join('ct_retenciones as r')->where('r.id_empresa', 'dasdadaa')->select('r.*', 'ct_detalle_retenciones.*')->get();
            foreach ($detalle as $x) {
                $tiporetencion = $x->porcentajer->cuenta_acreedores;
                $asiento_detalle = ct_asientos_detalle::where('id_asiento_cabecera', $x->id_asiento_cabecera)->get();
                foreach ($asiento_detalle as $a) {

                    if ($tiporetencion != $a->id_plan_cuentas) {
                        $a->id_plan_cuentas = $tiporetencion;
                        $a->save();
                    }
                }
            }
        }

        return response()->json(['state' => '1']);
    }
    public static function build_data($data)
    {
        //dd($data);
        $c_sucursal      = 0;
        $c_caja          = 0;
        $num_comprobante = 0;
        $nfactura        = 0;
        $proced          = "1";
        $pac             = "";
        if ($data['nombre_paciente'] != "") {
            $pac = " | " . $data['nombre_paciente'];
        }
        $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;

        $id_asiento_cabecera = 0;
        //GUARDAdo TABLA CT_VENTA.
        $valid = Ct_Clientes::where('identificacion', $data['identificacion_cliente'])->first();
        if (is_null($valid)) {
            Ct_Clientes::create([
                'nombre'                  => strtoupper($data['nombre_paciente']),
                'tipo'                    => '1',
                'identificacion'          => $data['identificacion_cliente'],
                'clase'                   => 'normal',
                'nombre_representante'    => $data['nombre_paciente'],
                'cedula_representante'    => $data['identificacion_cliente'],
                'ciudad_representante'    => '1',
                'direccion_representante' => $data['direccion_cliente'],
                'telefono1_representante' => $data['telefono_cliente'],
                'telefono2_representante' => $data['telefono_cliente'],
                'email_representante'     => $data['mail_cliente'],
                'pais'                    => '1',
                'estado'                  => '1',
                'id_usuariocrea'          => $data['identificacion_cliente'],
                'id_usuariomod'           => $data['identificacion_cliente'],
                'ip_creacion'             => '1',
                'ip_modificacion'         => '1',
            ]);
        }
        $factura_venta = [
            'sucursal'            => $c_sucursal,
            'punto_emision'       => $c_caja,
            'numero'              => $nfactura,
            'nro_comprobante'     => $num_comprobante,
            'id_asiento'          => $id_asiento_cabecera,
            'id_empresa'          => $data['id_empresa'],
            'tipo'                => $data['tipo'],
            'fecha'               => $data['fecha'],
            'divisas'             => $data['divisas'],
            'nombre_cliente'      => $data['nombre_cliente'],
            'tipo_consulta'       => $data['tipo_consulta'],
            'id_cliente'          => $data['identificacion_cliente'],
            'direccion_cliente'   => $data['direccion_cliente'],
            'ruc_id_cliente'      => $data['identificacion_cliente'],
            'telefono_cliente'    => $data['telefono_cliente'],
            'email_cliente'       => $data['mail_cliente'],
            'orden_venta'         => $data['orden_venta'],
            'estado_pago'         => '0',
            'id_paciente'         => $data['identificacion_paciente'],
            'nombres_paciente'    => $data['nombre_paciente'],
            'id_hc_procedimiento' => "",
            'seguro_paciente'     => $data['id_seguro'],
            'procedimientos'      => "",
            'fecha_procedimiento' => $data['fecha'],
            'copago'              => $data['totalc'],
            'id_recaudador'       => "",
            'ci_vendedor'         => "",
            'vendedor'            => "",
            'subtotal_0'          => $data['subtotal_01'],
            'subtotal_12'         => $data['subtotal_121'],
            'descuento'           => $data['descuento1'],
            'base_imponible'      => $data['subtotal_121'],
            'impuesto'            => $data['tarifa_iva1'],
            'total_final'         => $data['totalc'],
            'valor_contable'      => $data['totalc'],
            'ip_creacion'         => "1",
            'ip_modificacion'     => "1",
            'id_usuariocrea'      => $data['id_usuario'],
            'id_usuariomod'       => $data['id_usuario'],
        ];
        $id_venta = Ct_Ven_Orden::insertGetId($factura_venta);
        foreach ($data['details'] as $valor) {
            $detalle = [
                'id_ct_ven_orden'      => $id_venta,
                'id_ct_productos'      => $valor['codigo'],
                'nombre'               => $valor['nombre'],
                'cantidad'             => $valor['cantidad'],
                'precio'               => $valor['precio'],
                'descuento_porcentaje' => $valor['descpor'],
                'descuento'            => $valor['descuento'],
                'extendido'            => $valor['copago'],
                'detalle'              => $valor['detalle'],
                'copago'               => $valor['precioneto'],
                'check_iva'            => $valor['iva'],
                'ip_creacion'          => "1",
                'ip_modificacion'      => "1",
                'id_usuariocrea'       => $data['id_usuario'],
                'id_usuariomod'        => $data['id_usuario'],
            ];
            Ct_Ven_Orden_Detalle::create($detalle);
        }
        $message['state'] = 'success';
        $message['ven_orden'] = $id_venta;
        return $message;
    }
    public static function update_data($data)
    {
        $id = $data['id_orden'];
        //$id_usuario= $data['id_usuario'];
        $invoice = "";
        $orden = Ct_Ven_Orden::where('id', $id)->where('estado_pago', '<>', '0')->first();
        $invoice = Contable::invoice($id);
        //dd($invoice);
        if (!is_null($orden)) {
            $orden->estado_pago = 1;
            //$orden->id_usuariomod=$id_usuario;
            $orden->save();
        } else {
            return response()->json(['state' => 'Error', 'Msg' => 'Vacio Orden ' . $id]);
        }
        return response()->json(['state' => 'success', 'invoice' => $invoice, 'orden' => $orden->id]);
    }
    public static function invoice($id)
    {
        $orden           = Examen_Orden::find($id);
        $data['empresa'] = '0993075000001'; // humanlabs
        $cliente['cedula']   = $orden->cedula_factura;
        $cliente['tipo']     = $orden->tipo_documento;
        $cliente['nombre']   = $orden->nombre_factura;
        $cliente['apellido'] = '';
        $explode             = explode(" ", $orden->nombre_factura);
        if (count($explode) >= 4) {
            $cliente['nombre'] = $explode[0] . ' ' . $explode[1];
            for ($i = 2; $i < count($explode); $i++) {
                $cliente['apellido'] = $cliente['apellido'] . ' ' . $explode[$i];
            }
        }
        if (count($explode) == 3) {
            $cliente['nombre']   = $explode[0];
            $cliente['apellido'] = $explode[1] . ' ' . $explode[2];
        }
        if (count($explode) == 2) {
            $cliente['nombre']   = $explode[0];
            $cliente['apellido'] = $explode[1];
        }
        //dd($cliente);

        $cliente['email']     = $orden->email_factura;
        $cliente['telefono']  = $orden->telefono_factura;
        $direccion['calle']   = $orden->direccion_factura;
        $direccion['ciudad']  = $orden->ciudad_factura;
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;

        $msn_error  = '';
        $flag_error = false;
        if ($cliente['cedula'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en cedula';
        }

        if ($cliente['nombre'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en Nombre';
        }
        if ($cliente['email'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en email';
        }
        if ($cliente['telefono'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en telefono';
        }
        if ($direccion['calle'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en calle';
        }
        if ($direccion['ciudad'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en Ciudad';
        }

        $cant = 0;
        foreach ($orden->detalles as $value) {
            //se envian los productos
            $producto_precio       = $value->valor;
            $producto_subtotal     = $value->valor - $value->valor_descuento;
            if ($orden->cobrar_pac_pct < 100) {
                $producto_precio   = $value->valor_con_oda;
                $producto_subtotal = $value->valor_con_oda - $value->valor_descuento;
            }

            $producto['sku']       = "LABS-" . $value->examen->id; //ID EXAMEN
            $producto['nombre']    = $value->examen->nombre; // NOMBRE DEL EXAMEN
            $producto['cantidad']  = "1";
            $producto['precio']    = $producto_precio; //DETALLE
            $producto['descuento'] = $value->valor_descuento;
            $producto['subtotal']  = $producto_subtotal; //precio-descuento
            $producto['tax']       = "0";
            $producto['total']     = $producto_subtotal; //SUBTOTAL
            $producto['copago']    = "0";
            $productos[$cant]      = $producto;
            $cant++;
        }

        if ($orden->recargo_valor > 0) {
            $producto['sku']       = "LABS-FEE";
            $producto['nombre']    = 'FEE-ADMINISTRATIVO';
            $producto['cantidad']  = "1";
            $producto['precio']    = $orden->recargo_valor; //DETALLE
            $producto['descuento'] = '0';
            $producto['subtotal']  = $orden->recargo_valor; //precio-descuento
            $producto['tax']       = "0";
            $producto['total']     = $orden->recargo_valor; //SUBTOTAL
            $productos[$cant]      = $producto;
            $cant++;
        }

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
        $info_adicional['nombre'] = "AGENTES_RETENCION";
        $info_adicional['valor']  = "Resolucion 1";
        $info[0]                  = $info_adicional;

        $info_adicional['nombre'] = "PACIENTE";
        $info_adicional['valor']  = $orden->id_paciente . ' ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1;
        $info[1]                  = $info_adicional;

        $info_adicional['nombre'] = "MAIL";
        $info_adicional['valor']  = $orden->email_factura; //EMAIL
        $info[2]                  = $info_adicional;

        $info_adicional['nombre'] = "CIUDAD";
        $info_adicional['valor']  = $orden->ciudad_factura; //EMAIL
        $info[3]                  = $info_adicional;

        $info_adicional['nombre'] = "DIRECCION";
        $info_adicional['valor']  = $orden->direccion_factura; //EMAIL
        $info[4]                  = $info_adicional;

        $info_adicional['nombre'] = "ORDEN";
        $info_adicional['valor']  = '' . $orden->id . ''; //EMAIL
        $info[5]                  = $info_adicional;

        $info_adicional['nombre'] = "SEGURO";
        $info_adicional['valor']  = $orden->seguro->nombre; //SEGURO
        $info[6]                  = $info_adicional;

        $cuenta_forma = $orden->detalle_forma_pago->count();
        //dd($cuenta_forma);
        if ($cuenta_forma > 1) {
            $pago['forma_pago']       = '20';
            $info_adicional['nombre'] = "FORMA_PAGO";
            $texto                    = '';

            foreach ($orden->detalle_forma_pago as $fp) {
                $total = $fp->valor + $fp->p_fi;
                $total = round($total, 2);
                $texto = $texto . ' ' . $fp->tipo_pago->nombre . ': ' . $total;
            }
            $info_adicional['valor'] = $texto;
            $info[7]                 = $info_adicional;
        } else {
            $forma_pago = $orden->detalle_forma_pago->first();
            $tipo       = $forma_pago->id_tipo_pago;
            if ($tipo == '1') {
                $pago['forma_pago'] = '01';
            } elseif ($tipo == '2') {
                $pago['forma_pago'] = '20';
            } elseif ($tipo == '3') {
                $pago['forma_pago'] = '20';
            } elseif ($tipo == '4') {
                $pago['forma_pago'] = '19';
            } elseif ($tipo == '5') {
                $pago['forma_pago'] = '20';
            } elseif ($tipo == '7') {
                $pago['forma_pago'] = '01';
            } else {
                $pago['forma_pago'] = '16';
            }
        }
        $pago['informacion_adicional'] = $info;
        $pago['dias_plazo']            = '10';
        $data['pago']                  = $pago;
        $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
        $data['laboratorio']           = 1;
        $data['paciente']              = $orden->id_paciente;
        $data['concepto']              = 'Factura Electronica -' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1;
        $data['copago']                = 0;
        $data['id_seguro']             = '1'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
        if ($orden->cobrar_pac_pct < 100) {
            $data['total_factura']         = $orden->total_con_oda;
        } else {
            $data['total_factura']         = $orden->total_valor;
        }


        $fp_cant = 0;
        foreach ($orden->detalle_forma_pago as $fp) {

            $tipos_pago['id_tipo']            = $fp->id_tipo_pago; //metodo de pago efectivo, tarjeta, etc
            $tipos_pago['fecha']              = substr($orden->fecha_orden, 0, 10);
            $tipos_pago['tipo_tarjeta']       = $fp->tipo_tarjeta; //si es efectivo no se envia
            $tipos_pago['numero_transaccion'] = $fp->numero; //si es efectivo no se envia
            $tipos_pago['id_banco']           = $fp->banco; //si es efectivo no se envia
            $tipos_pago['cuenta']             = $fp->cuenta; //si es efectivo no se envia
            $tipos_pago['giradoa']            = null; //si es efectivo no se envia
            $tipos_pago['valor']              = $fp->valor + $fp->p_fi; //valor a pagar de total
            $tipos_pago['valor_base']         = $fp->valor + $fp->p_fi; //valor a pagar de base

            $pagos[$fp_cant] = $tipos_pago;
            $fp_cant++;
        }

        $data['formas_pago'] = $pagos;

        $send['comprobante'] = $orden->comprobante;
        $send = json_encode($send);
        $send = json_decode($send);
        //dd($data);
        //dd($send);
        $envio = ApiFacturacionController::crea_factura($data, $send);
        //dd($envio);
        return response()->json($envio);
    }
    public static function secuence_process($type, $enterprise)
    {
        $numero_factura = "0000000000";
        $log = Ct_Proceso_Detalle::where('tipo', $type)->latest()->first();
        if (is_null($log)) {
            $nu = 1;
            $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
        } else {
            $secuencia = intval($log->secuencia);
            //dd($max_id->secuencia);
            if (strlen($secuencia) < 10) {
                $nu             = $secuencia + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }
        }
        return $numero_factura;
    }
    public static function secuence($empresa)
    {
        $numero_factura = "0000000000";
        $log = Ct_Proceso::where('id_empresa', $empresa)->latest()->first();
        if (is_null($log)) {
            $nu = 1;
            $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
        } else {
            $secuencia = intval($log->secuencia);
            //dd($max_id->secuencia);
            if (strlen($secuencia) < 10) {
                $nu             = $secuencia + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }
        }
        return $numero_factura;
    }
    public static function asiento()
    {
        $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', '4.1.01.01')->update([
            'id_plan_cuenta' => '4.1.09.01'
        ]);
        return response()->json('ok');
    }
    public static function pagofactura($id, $reference, $module)
    {
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $compras = Ct_compras::find($id);
        $idx = str_replace('P', '', $compras->orden_compra);
        $orden = Ct_pedidos_Compra::where('id', $idx)->first();
        $valor_contable = $compras->valor_contable;
        if ($orden != null) {
            if ($valor_contable == 0) {
                $orden->aprobado = 3;
                $orden->save();
                $proceso = Ct_Proceso::where('id_pedido', $idx)->first();
                Ct_Proceso_Detalle::create([
                    'id_tipo_proceso'     => '7',
                    'id_referencia'       => $proceso->id,
                    'id_find'             => $reference,
                    'tipo'                => 'P',
                    'module'              => $module,
                    'id_usuariocrea'      => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'secuencia'           => Contable::secuence_process('P', $orden->id_empresa),
                ]);
            }
        }


        return response()->json('ok');
    }
    public static function getmodule($id, $reference = "")
    {
        $egreso = Contable::egreso($id);
        $egreso_masivo = Contable::egreso_masivo($id);
        $cruce = Contable::cruce($id);
        $cruce_cuentas = Contable::cruce_cuentas($id);
        $retenciones = Contable::retenciones($id);
        $debito_bancario = Contable::debito($id);
        $debito_acreedores = Contable::debito_acreedores($id);
        $credito_acreedores = Contable::credito_acreedores($id);
        $data['id'] = $reference;
        $data['compra'] = $id;
        if ($egreso > 0) {
            $data['module']['EGRESO'] = 'EGRESO';
            $data['total']['EGRESO'] = $egreso;
        }
        if ($egreso_masivo > 0) {
            $data['module']['EGRESO MASIVO'] = 'EGRESO MASIVO';
            $data['total']['EGRESO MASIVO'] = $egreso_masivo;
        }
        if ($cruce > 0) {
            $data['module']['CRUCE'] = 'CRUCE';
            $data['total']['CRUCE'] = $cruce;
        }
        if ($cruce_cuentas > 0) {
            $data['module']['CRUCE CUENTAS'] = 'CRUCE CUENTAS';
            $data['total']['CRUCE CUENTAS'] = $cruce_cuentas;
        }
        if ($retenciones > 0) {
            $data['module']['RETENCIONES'] = 'RETENCIONES';
            $data['total']['RETENCIONES'] = $retenciones;
        }
        if ($debito_bancario > 0) {
            $data['module']['DEBITO BANCARIO'] = 'DEBITO BANCARIO';
            $data['total']['DEBITO BANCARIO'] = $debito_bancario;
        }
        if ($debito_acreedores > 0) {
            $data['module']['DEBITO ACREEDORES'] = 'DEBITO ACREEDORES';
            $data['total']['DEBITO ACREEDORES'] = $debito_acreedores;
        }
        if ($credito_acreedores > 0) {
            $data['module']['CREDITO ACREEDORES'] = 'CREDITO ACREEDORES';
            $data['total']['CREDITO ACREEDORES'] = $credito_acreedores;
        }
        return $data;
    }
    public static function getmodulev($id, $reference = "")
    {
        $retenciones = Contable::retenciones_clientes($id);
        $ingreso = Contable::ingreso($id);
        $cruce = Contable::cruce_clientes($id);
        $cruce_cuenta = Contable::cruce_cuentas_clientes($id);
        $credito = Contable::credito_clientes_($id);
        $debito_clientes = Contable::debito_clientes($id);
        $chequepost = Contable::chequepost($id);
        $data['id'] = $reference;
        $data['venta'] = $id;
        if ($retenciones > 0) {
            $data['module']['RETENCIONES'] = 'RETENCIONES';
            $data['total']['RETENCIONES'] = $retenciones;
        }
        if ($ingreso > 0) {
            $data['module']['INGRESO'] = 'INGRESO';
            $data['total']['INGRESO'] = $ingreso;
        }
        if ($cruce > 0) {
            $data['module']['CRUCE'] = 'CRUCE';
            $data['total']['CRUCE'] = $cruce;
        }
        if ($cruce_cuenta) {
            $data['module']['CRUCEC'] = 'CRUCEC';
            $data['total']['CRUCEC'] = $cruce_cuenta;
        }
        if ($credito > 0) {
            $data['module']['CREDITO'] = 'CREDITO';
            $data['total']['CREDITO'] = $credito;
        }
        if ($debito_clientes > 0) {
            $data['module']['DEBITO'] = 'DEBITO';
            $data['total']['DEBITO'] = $debito_clientes;
        }
        if ($chequepost > 0) {
            $data['module']['CHEQUE'] = 'CHEQUE';
            $data['total']['CHEQUE'] = $chequepost;
        }
        return $data;
    }
    public static function fix_comprobante($type = "", $id = "")
    {
        if ($type == 'V') {
            $ventas = Ct_ventas::where('estado', '>', 0)->where('id_empresa', $id)->get();
            foreach ($ventas as $v) {
                $nro = $v->nro_comprobante;
                $xp = explode('-', $nro);
                $primero = "";
                $segundo = "";
                $tercero = "";
                if (isset($xp[0])) {
                    $primero = $xp[0];
                }
                if (isset($xp[1])) {
                    $segundo = $xp[1];
                }
                if (isset($xp[2])) {
                    $tercero = $xp[2];
                    $tercero = intval($tercero);
                    $tercero = str_pad($tercero, 9, "0", STR_PAD_LEFT);
                }
                if ($primero != "" && $segundo != "" && $tercero != "") {
                    $final = $primero . '-' . $segundo . '-' . $tercero;
                    $v->nro_comprobante = $final;
                    $v->save();
                }
            }
        }
        if ($type == 'C') {
            $ventas = Ct_compras::where('estado', '>', 0)->where('tipo', '<>', 3)->where('id_empresa', $id)->get();
            //dd($ventas);
            foreach ($ventas as $v) {
                $nro = $v->numero;
                $xp = explode('-', $nro);
                $primero = "";
                $segundo = "";
                $tercero = "";
                if (isset($xp[0])) {
                    $primero = $xp[0];
                }
                if (isset($xp[1])) {
                    $segundo = $xp[1];
                }
                if (isset($xp[2])) {
                    $tercero = $xp[2];
                    $tercero = intval($tercero);
                    $tercero = str_pad($tercero, 9, "0", STR_PAD_LEFT);
                }
                if ($primero != "" && $segundo != "" && $tercero != "") {
                    $final = $primero . '-' . $segundo . '-' . $tercero;
                    $v->numero = $final;
                    $v->save();
                }
            }
        }
        return response()->json('ok');
    }
    public static function fixdate($id)
    {
        $egreso = Ct_Comprobante_Egreso::where('estado', 1)->whereNull('fecha_comprobante')->where('id_empresa', $id)->get();
        foreach ($egreso as $e) {
            $p = Ct_Asientos_Cabecera::find($e->id_asiento_cabecera);
            $fecha = $p->fecha_asiento;
            $e->fecha_comprobante = $fecha;
            $e->save();
        }
    }
    public static function verify_price($id)
    {
        $compras = Ct_compras::where('id_empresa', $id)->get();
        $array_desiguales_cab = [];
        $array_desiguales_det = [];
        $egreso = Ct_Comprobante_Egreso::where('id_empresa', $id)->where('estado', '>', 0)->get();
        foreach ($egreso as $e) {
            $valor_compra = 0;
            $valor_proveedor = 0;
            $valor_compra = $e->detalles->sum('abono');
            $valor_proveedor = $e->asiento_cabecera->detalles->where('id_plan_cuenta', '2.01.03.01.01')->sum('debe');
            if ($valor_proveedor == 0) {
                $valor_proveedor = $e->asiento_cabecera->detalles->where('id_plan_cuenta', '1.01.04.03')->sum('debe');
            }
            if ($valor_compra != $valor_proveedor) {
                $px['id'] = $e->id;
                $px['id_asiento'] = $e->id_asiento_cabecera;
                $px['valorabono'] = $valor_compra;
                $px['valorproveedor'] = $valor_proveedor;
                array_push($array_desiguales_cab, $px);
            }
        }
        return $array_desiguales_cab;
    }
    public static function charging_method($id)
    {
        if ($id != null) {
        }
        return 'error';
    }
    public function masivo_inv($nombre = "")
    {
        $x = 1;
        $serie = 0;
        Excel::filter('chunk')->load($nombre . '.xlsx')->chunk(600, function ($reader) use ($x, $serie) {
            foreach ($reader as $book) {

                //dd($book);

                $unix_date = ($book->fecha_exp - 25569) * 86400;
                $excel_date = 25569 + ($unix_date / 86400);
                $unix_date = ($excel_date - 25569) * 86400;
                $fecha_exp = gmdate("Y-m-d", $unix_date);

                $unix_date = ($book->pedido - 25569) * 86400;
                $excel_date = 25569 + ($unix_date / 86400);
                $unix_date = ($excel_date - 25569) * 86400;
                $pedido = gmdate("Y-m-d", $unix_date);

                if (is_null($book->serie) || $book->serie == "") {
                    $serie = $this->generar_serie($x);
                } else {
                    $serie = $book->serie;
                }
                DB::table('inv_carga_inventario')->insert([
                    'marca' => $book->marca, //marca
                    'descripcion' => $book->detalle, //detalle
                    'descripcion1' => $book->detalle, //detalle
                    'codigo' => $book->referencia, //referencia
                    'lote' => $book->lote,
                    'cantidad' => $book->cantidad,
                    'fecha_exp' => $fecha_exp, //fecha vencimiento
                    'serie' => $serie, //codigo de barras 
                    'estado' => 'A',
                    'pedido' => $pedido, //numero pedido
                    'tipo' => $book->tipo, //facturado o consignado
                    'caducado' => 'USO', //NO SE
                    'precio' => '0', //NO SE
                    'lugar' => 'PENTAX',
                    'creado' => 'TURSI - acelity'
                ]);
                $x++;
            }
        });
        dd("ok");
    }
    public function generar_serie($id)
    {
        $serie = null;
        if ($id != null) {

            $serie = date('YmdHis') . $id;
        } else {
            $serie = date('YmdHis') . $id;
        }
        return $serie;
    }
    public static function masivo_arreglar($id_empresa)
    {
        $compras = Ct_compras::where('id_empresa', $id_empresa)->where('tipo', 2)->get();
        foreach ($compras as $c) {
            $af = AfFacturaActivoCabecera::find($c->tipo_gasto);
            if (!is_null($af)) {
                if ($c->iva_total == $c->subtotal_12) {
                    $c->subtotal_12 = 0;
                }
                if ($c->subtotal_12 == null  && $c->iva_total > 0) {
                    $c->subtotal_12 = $c->subtotal;
                    $c->save();
                }
                if ($c->iva_total == 0) {
                    $c->subtotal_0 = 0;
                }
                $c->save();
            } else {
                if ($c->iva_total == $c->subtotal_12) {
                    $c->subtotal_12 = 0;
                }
                $c->save();
            }
        }
        return 'ok AF';
    }
    public static function generarOrden($data)
    {
        //$paciente= Paciente::find($data['id_paciente']);
        $orden = Examen_Orden::find($data['id_orden']);
        $paciente = Paciente::find($orden->id_paciente);
        if (isset($data['prueba'])) {
            $prueba = $data['prueba'];
            if ($prueba == 1) {
                return response()->json(['prueba' => 'ok', 'data' => $data]);
            }
        }
        $valid = CierreCaja::where('id_orden', $orden->id)->first();
        $idcierre = 0;
        if ($data['today'] == 1) {
            if (is_null($valid)) {
                $idcierre = CierreCaja::insertGetid([
                    'fecha'           => date('Y-m-d H:m:s'),
                    'tipo'            => '1',
                    'id_paciente'     => $paciente->id,
                    'id_seguro'       => $orden->seguro->id,
                    'descripcion'     => 'El examen orden : ' . $data['id_orden'] . ' paciente: ' . $paciente->apellido1 . ' ' . $paciente->nombre1,
                    'valor'           => $orden->total_valor,
                    'saldo'           => $orden->total_valor,
                    'id_orden'        => $data['id_orden'],
                    'estado'          => '1',
                    'ip_creacion'     => 'Generado Funcion',
                    'ip_modificacion' => 'Generado Funcion',
                    'id_usuariocrea'  => $data['id_usuario'],
                    'id_usuariomod'   => $data['id_usuario'],
                ]);
            } else {
                $idcierre = "Ya existe la orden";
            }
        } else {
            if (is_null($valid)) {
                $idcierre = CierreCaja::insertGetid([
                    'fecha'           => date('Y-m-d H:m:s', strtotime($data['fecha'])),
                    'tipo'            => '1',
                    'id_paciente'     => $paciente->id,
                    'id_seguro'       => $orden->seguro->id,
                    'descripcion'     => 'El examen orden : ' . $data['id_orden'] . ' paciente: ' . $paciente->apellido1 . ' ' . $paciente->nombre1,
                    'valor'           => $orden->total_valor,
                    'saldo'           => $orden->total_valor,
                    'id_orden'        => $data['id_orden'],
                    'estado'          => '1',
                    'ip_creacion'     => 'Generado Funcion',
                    'ip_modificacion' => 'Generado Funcion',
                    'id_usuariocrea'  => $data['id_usuario'],
                    'id_usuariomod'   => $data['id_usuario'],
                ]);
            } else {
                $idcierre = "Ya existe la orden";
            }
        }

        return $idcierre;
    }
}
