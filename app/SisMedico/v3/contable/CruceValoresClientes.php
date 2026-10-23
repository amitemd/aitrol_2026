<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\Ventas;
use Sis_medico\v3\contable\AsientosCabecera;
use Sis_medico\v3\contable\DetalleCruceCuentasClientes;
use Sis_medico\v3\contable\DetalleCruceClientes;
use Sis_medico\v3\contable\PlanCuentas;
use Sis_medico\v3\contable\Clientes;
use Sis_medico\v3\contable\ComprobanteIngreso;
use Sis_medico\v3\contable\DetallePagoCruce;
use Sis_medico\LogAsiento;

class CruceValoresClientes extends Model
{
    private static $modelo = 'CruceValoresClientes';
    protected $table = 'ct_cruce_valores_cliente';
    protected $primaryKey  = 'id';

    public static function getCruceValores($id = '')
    {
        if ($id == '') {
            return CruceValoresClientes::leftjoin('users as uc', 'ct_cruce_valores_cliente.id_usuariocrea', 'uc.id')
                ->leftjoin('ct_clientes as c', 'ct_cruce_valores_cliente.id_cliente', 'c.identificacion')
                ->where('ct_cruce_valores_cliente.id_empresa', session('id_empresa'))
                ->orderBy('ct_cruce_valores_cliente.id', 'asc')
                ->get([
                    'ct_cruce_valores_cliente.id', 
                    'ct_cruce_valores_cliente.fecha_pago as fecha',
                    'ct_cruce_valores_cliente.id_asiento_cabecera',
                    'ct_cruce_valores_cliente.id_cliente',
                    'c.nombre',
                    'ct_cruce_valores_cliente.detalle',
                    'ct_cruce_valores_cliente.estado',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido'
                ]);
        } else {
            return CruceValoresClientes::join('users as uc', 'ct_cruce_valores_cliente.id_usuariocrea', 'uc.id')
                ->join('ct_clientes as c', 'ct_cruce_valores_cliente.id_cliente', 'c.identificacion')
                ->where('ct_cruce_valores_cliente.id_empresa', session('id_empresa'))
                ->where('ct_cruce_valores_cliente.id', $id)
                ->first([
                    'ct_cruce_valores_cliente.id',
                    'ct_cruce_valores_cliente.fecha_pago as fecha',
                    'ct_cruce_valores_cliente.secuencia',
                    'ct_cruce_valores_cliente.id_asiento_cabecera',
                    'ct_cruce_valores_cliente.detalle',
                    'ct_cruce_valores_cliente.id_cliente',
                    'c.nombre',
                    'ct_cruce_valores_cliente.total_disponible',
                    'ct_cruce_valores_cliente.estado',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido'
                ]);
        }
    }

    public static function getDetalleCruceCuentas($id)
    {
        return DetalleCruceCuentasClientes::join('plan_cuentas_empresa as pce', 'pce.id_plan', 'ct_detalle_cruce_cuentas_clientes.codigo')
            ->where('pce.id_empresa', session('id_empresa'))
            ->where('ct_detalle_cruce_cuentas_clientes.id_comprobante', $id)->get();
    }

    public static function estadoCruceCuentas($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (CruceCuentasClientes::where('id', $r->id)->update(['estado' => $estado])) {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro editado correctamente',
                    'data' => $r->input(),
                ];
            } else {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Error al actualizar el estado de Cruce de cuentas',
                    'data' => $r->input(),
                ];
            }
        } catch (Exception $e) {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
        }
        return json_encode($arrayRespuesta);
    }

    public static function buscarVentas($id = '')
    {
        if ($id == '') {
            return Ventas::where('ct_ventas.id_empresa', session('id_empresa'))
                ->join('ct_clientes as c', 'c.identificacion', 'ct_ventas.id_cliente')
                ->where('ct_ventas.estado', '<>', '0')
                ->where('ct_ventas.valor_contable', '>', '0')
                ->get([
                    'ct_ventas.*',
                    'c.nombre'
                ]);
        } else {
            return Ventas::where('ct_ventas.id_empresa', session('id_empresa'))
                ->join('ct_clientes as c', 'c.identificacion', 'ct_ventas.id_cliente')
                ->where('ct_ventas.estado', '<>', '0')
                ->where('ct_ventas.valor_contable', '>', '0')
                ->where('ct_ventas.id', $id)
                ->first([
                    'ct_ventas.*',
                    'c.nombre'
                ]);
        }
    }

    public static function saveCruceValores($r)
    {
        // dd("w", r->input());
        //echo '<pre>'; print_r($r->input()); exit;
        DB::beginTransaction();
        DB::enableQueryLog();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            $idusuario = Auth::user()->id;
            $id_empresa = session('id_empresa');
            $id_cliente = explode('|', $r->id_cliente);
            $numero_factura = CruceValoresClientes::generarNumeroCruce();
            $cabecera = [
                'observacion'                   => $r->concepto,
                'fecha_asiento'                 => $r->fecha,
                'fact_numero'                   => $numero_factura,
                'valor'                         => $r->total_anticipos,
                'id_empresa'                    => $id_empresa,
                'estado'                        => '1',
                'ip_creacion'                   => $ip,
                'ip_modificacion'               => $ip,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
            ];
            //$id_asiento_cabecera = AsientosCabecera::insertGetId($cabecera);
            //$id_asiento_cabecera = Contabilidad::crearAsiento('cliente', 'cruce_cuenta', $cabecera,  $r);
            $id_asiento_cabecera = 888;

                $input = [
                    'detalle'                       => $r->concepto,
                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                    'fecha_pago'                    => $r->fecha,
                    'id_cliente'                    => $id_cliente[0],
                    'secuencia'                     => $numero_factura,
                    'total_disponible'              => $r->total_anticipos,
                    'id_empresa'                    => $id_empresa,
                    'estado'                        => '1',
                    'ip_creacion'                   => $ip,
                    'ip_modificacion'               => $ip,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                ];
                
                $id_comprobante = CruceValoresClientes::insertGetId($input);
                

                for ($i = 0; $i < count($data["idac"]); $i++) {
                    $consulta_anticipo = ComprobanteIngreso::where('id', $r['idac'][$i])->where('id_empresa', $id_empresa)->first();
                    if (!is_null($consulta_anticipo)) {
                        $nuevo_saldo = $r['saldo_cv'][$i] - $r['abono_cv'][$i];
                        if (($r['abono_cv'][$i]) > 0) {
                            if ($nuevo_saldo > 0) {
                                $input_actualiza = [
                                    'deficit_ingreso' => $nuevo_saldo,
                                    'estado' => '1',
                                    'id_usuariomod' => $idusuario,
                                    'ip_modificacion' => $ip
                                ];
                            }else {
                                $input_actualiza = [
                                    'deficit_ingreso' => '0.00',
                                    'estado' => '1',
                                    'id_usuariomod' => $idusuario,
                                    'ip_modificacion' => $ip
                                ];
                            }
                            ComprobanteIngreso::where('id', $r['idac'][$i])->where('id_empresa', $id_empresa)->update($input_actualiza);

                            $input_det_pago = [
                                'id_comprobante'  => $id_comprobante,
                                'id_comp_ingreso' => $consulta_anticipo->id,
                                'numero'          => $r['numero_a'][$i],
                                'fecha'           => $r->fecha,
                                'valor_ant'       => $r['saldo_cv'][$i],
                                'valor'           => $r['abono_cv'][$i],
                                'concepto'        => $r['concepto_a'][$i],
                                'estado'          => '1',
                                'ip_creacion'     => $ip,
                                'ip_modificacion' => $ip,
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ];
                            $id_det_pago = DetallePagoCruce::insertGetId($input_det_pago);
                        }
                    }
                }

                for ($i = 0; $i < count($data["id_actualiza"]); $i++) {
                    if (!is_null($r['abono_deuda_cv'][$i])) {
                        if (($r['abono_deuda_cv'][$i]) > 0) {
                            if (!is_null($r['numero_fact'][$i])) {
                                $consulta_facturas = Ventas::where('id', $r['id_actualiza'][$i])->where('estado', '<>', '0')->where('id_empresa', $id_empresa)->first();
                                if ($consulta_facturas != '[]') {
                                    if ($r['abono_deuda_cv'][$i] > $consulta_facturas->valor_contable) {
                                        $total = $r['abono_deuda_cv'][$i] - $consulta_facturas->valor_contable;
                                    } else {
                                        $total = $consulta_facturas->valor_contable - $r['abono_deuda_cv'][$i];
                                    }
                                    if ($total > 0) {
                                        $input_actualiza = [
                                            'valor_contable' => $total,
                                            'id_usuariomod' => $idusuario,
                                            'ip_modificacion' => $ip
                                        ];
                                    } else {
                                        $input_actualiza = [
                                            'estado_pago' => '3', 
                                            'valor_contable' => '0.00',
                                            'id_usuariomod' => $idusuario,
                                            'ip_modificacion' => $ip
                                        ];
                                    } 

                                    Ventas::where('id', $r['id_actualiza'][$i])->where('estado', '<>', '0')->where('id_empresa', $id_empresa)->update($input_actualiza);
                                    
                                    $input_det_pago = [
                                        'id_comprobante'    => $id_comprobante,
                                        'fecha'             => $r->fecha,
                                        'observaciones'     => $r['observacion'][$i],
                                        'id_factura'        => $consulta_facturas->id,
                                        'secuencia_factura' => $r['numero_fact'][$i],
                                        'total_factura'     => $r['saldo_deuda_cv'][$i],
                                        'total'             => $r['abono_deuda_cv'][$i],
                                        'estado'            => '1',
                                        'ip_creacion'       => $ip,
                                        'ip_modificacion'   => $ip,
                                        'id_usuariocrea'    => $idusuario,
                                        'id_usuariomod'     => $idusuario,
                                    ];
                                    $id_det_cruce = DetalleCruceClientes::insertGetId($input_det_pago);
                                }
                            }
                        }
                    }
                }
                //DB::commit();
                //echo print_r($id_det_cruce); exit; 
                /*$cuenta_cxccli_come = Configuraciones::obtener_cuenta('CRUCECLIENTES_CXCCLI_COME');
                AsientosDetalles::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $cuenta_cxccli_come->cuenta_guardar,
                    'descripcion'         => $cuenta_cxccli_come->nombre_mostrar,
                    'fecha'               => $r->fecha,
                    'haber'               => $r->valor_total,
                    'debe'                => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip,
                    'ip_modificacion'     => $ip,
                ]);

                $cuenta_ant_cliente = Configuraciones::obtener_cuenta('CRUCECLIENTES_ANT_CLIENTE');
                AsientosDetalles::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $cuenta_ant_cliente->cuenta_guardar,
                    'descripcion'         => $cuenta_ant_cliente->nombre_mostrar,
                    'fecha'               => $r->fecha,
                    'haber'               => $r->valor_total,
                    'debe'                => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip,
                    'ip_modificacion'     => $ip,
                ]);*/

                $r->c = 'Clientes';
                $r->s = 'saveCruceValores';
                $r->d = $origin['d'];
                $r->m = CruceValoresClientes::$modelo;
                $r->o = 'Se agrego el nuevo cruce de valores con id.: ' . $id_comprobante;
                $cont++;

        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Clientes';
            $r->s = 'saveCruceValores';
            $r->d =  $origin['d'];
            $r->m = CruceValoresClientes::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return $arrayRespuesta;
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'id_asiento' => $id_asiento_cabecera,
                'id' => $id_comprobante,
                'numero' => $numero_factura,
                'tipo' => 'crucevalores',
                'status' => 'success',
                'code' => 200,
                //'reload' => '',
                'message' => 'Registro guardado correctamente',
            ];
            return $arrayRespuesta;
        }
    }

    private static function generarNumeroCruce()
    {
        $num = CruceValoresClientes::where('id_empresa', session('id_empresa'))->max('id');
        if ($num == '')
            $num = '0000000001';
        else {
            $num = $num + 1;
            $num = str_pad($num, 10, 0, STR_PAD_LEFT);
        }
        return $num;
    }

    public static function anularCruceValores($r)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        $cont = 0;
        try {
            $origin = $r->input();
            $ip = Utilidades::getRealIP();
            $idusuario = Auth::user()->id;
            $id_empresa = session('id_empresa');
            $cruce_valor = CruceValoresClientes::find($r->id);
            if ($cruce_valor->estado == '1') {
                $detalle_cliente = DetalleCruceClientes::where('id_comprobante', $r->id)->where('estado', '1')->get();
                $pago_cruce = DetallePagoCruce::where('id_comprobante', $r->id)->where('estado', '1')->get();
                if($detalle_cliente != []){
                    foreach($detalle_cliente as $det_ing){
                        $ventas = Ventas::find($det_ing->id_factura);
                        if(!is_null($ventas)){
                            $suma = $det_ing->total + $ventas->valor_contable;
                            $input_venta = [
                                'valor_contable'    => $suma,
                                'ip_creacion'       => $ip,
                                'ip_modificacion'   => $ip,
                                'id_usuariocrea'    => $idusuario,
                                'id_usuariomod'     => $idusuario,
                            ];
                            Ventas::where('id', $det_ing->id_factura)->update($input_venta);
                        }
                    }
                }

                if($pago_cruce != []){
                    foreach($pago_cruce as $pag_cruce){
                        $comprobante_ingreso = ComprobanteIngreso::find($pag_cruce->id_comp_ingreso);
                        if(!is_null($comprobante_ingreso)){
                            $input_comp = [
                                'deficit_ingreso'    => $comprobante_ingreso->total,
                                'estado'       => '1',
                                'ip_modificacion'   => $ip,
                                'id_usuariomod'     => $idusuario,
                            ];
                            ComprobanteIngreso::where('id', $pag_cruce->id_comp_ingreso)->update($input_comp);
                        }
                    }
                }

                /*$input_asiento = [
                    'estado'            => '1',
                    'ip_modificacion'   => $ip,
                    'id_usuariomod'     => $idusuario,
                ];
                AsientosCabecera::find($cruce_valor->id_asiento_cabecera)->update($input_asiento);*/

                // asiento = Ct_Asientos_Cabecera::findorfail($cruce_cliente->id_asiento_cabecera);
                // $asiento->estado = 1;
                // $asiento->id_usuariomod = $idusuario;
                // $asiento->save();
                // $detalles = $asiento->detalles;
                // $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                //     'observacion'     => strtoupper($concepto),
                //     'fecha_asiento'   => $asiento->fecha_asiento,
                //     'id_empresa'      => $id_empresa,
                //     'fact_numero'     => $cruce_cliente->secuencia,
                //     'valor'           => $asiento->valor,
                //     'ip_creacion'     => $ip_cliente,
                //     'ip_modificacion' => $ip_cliente,
                //     'id_usuariocrea'  => $idusuario,
                //     'id_usuariomod'   => $idusuario,
                // ]);
                // foreach ($detalles as $value) {
                //     Ct_Asientos_Detalle::create([
                //         'id_asiento_cabecera' => $id_asiento,
                //         'id_plan_cuenta'      => $value->id_plan_cuenta,
                //         'debe'                => $value->haber,
                //         'haber'               => $value->debe,
                //         'descripcion'         => $value->descripcion,
                //         'fecha'               => $asiento->fecha_asiento,
                //         'ip_creacion'         => $ip_cliente,
                //         'ip_modificacion'     => $ip_cliente,
                //         'id_usuariocrea'      => $idusuario,
                //         'id_usuariomod'       => $idusuario,
                //     ]);
                // }

                $input_crucev = [
                    'estado'            => '0',
                    'ip_modificacion'   => $ip,
                    'id_usuariomod'     => $idusuario,
                ];
                CruceValoresClientes::where('id', $r->id)->update($input_crucev);

                //LogAsiento::anulacion("CL-CV", $id_asiento, $asiento->id);


                $r->c = 'Clientes';
                $r->s = 'anularCruceValores';
                //$r->d = $origin['d'];
                $r->m = CruceValoresClientes::$modelo;
                $r->o = 'Se anulo el nuevo cruce de valores con id.: ' . $r->id;
                $cont++;
            }
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Clientes';
            $r->s = 'anularCruceValores';
            //$r->d =  $origin['id'];
            $r->m = CruceValoresClientes::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return $arrayRespuesta;
        }

        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro anulado correctamente',
                'data' => $r->input(),
            ];
            return $arrayRespuesta;
        }
    }
}
