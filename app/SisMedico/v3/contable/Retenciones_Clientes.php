<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\DetalleClienteRetencion;

use Sis_medico\v3\contable\AsientosDetalles;

class Retenciones_Clientes extends Model
{
    private static $modelo = 'Retenciones_Clientes';
    protected $table = 'ct_cliente_retencion';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public function detalles()
    {
        return $this->hasMany('Sis_medico\v3\contable\DetalleClienteRetencion','id_cliente_retencion');
    }
    public static function sumaRetenciones($row, $anio, $mes)
    {
        return self::join('ct_detalle_cliente_retencion as d', 'ct_cliente_retencion.id', 'd.id_cliente_retencion')
            ->whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->where('d.id_porcentaje', $row->id)
            ->sum('d.totales');
    }
    public static function getDatosRetencionVenta($r, $porcentajes)
    {
        $table = '';
        $datos = self::join('ct_detalle_cliente_retencion as dr', 'ct_cliente_retencion.id', 'dr.id_cliente_retencion')
            ->where('ct_cliente_retencion.id_factura', $r->id)
            ->get();
        $totalRecibir = $r->total;
        $numRetencion = '';
        $numAutorizacion = '';
        $totalValor = 0;
        $arrayOper = [];
        if (count($datos) > 0) {
            foreach ($porcentajes as $k => $row) {
                $valor = 0;
                foreach ($datos as $d) {
                    if ($row->id == $d->id_porcentaje) {
                        //$arrayOper[$k] += $d->totales; 
                        $valor = $d->totales;
                        $totalValor = $valor;
                        $numRetencion = $d->nro_comprobante;
                        $numAutorizacion = $d->autorizacion;
                        $totalRecibir -= $valor;
                    }
                }
                $table .= '<td style="text-align:right;">' . number_format($valor, 4) . '</td>';
            }
        } else {
            foreach ($porcentajes as $row) {
                $table .= '<td style="text-align:right;">0.0000</td>';
            }
        }
        $table .= '<td style="text-align:right;">' . number_format($totalRecibir, 4) . '</td><td>' . $numRetencion . '</td><td>' . $numAutorizacion . '</td>|' . $totalValor;
        return $table;
    }
    public static function getRetenciones($r)
    {
        if (isset($r->d)) {
            return self::join('users as uc', 'ct_cliente_retencion.id_usuariocrea', 'uc.id')
                ->join('ct_clientes as c', 'c.identificacion', 'ct_cliente_retencion.id_cliente')
                ->join('ct_ventas as v', 'v.id', 'ct_cliente_retencion.id_factura')
                ->whereBetween('ct_cliente_retencion.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
                ->get([
                    'ct_cliente_retencion.id',
                    'ct_cliente_retencion.fecha',
                    'c.identificacion as id_cliente',
                    'ct_cliente_retencion.descripcion',
                    'c.nombre as cliente',
                    'v.numero as id_venta',
                    'ct_cliente_retencion.id_asiento_cabecera',
                    'ct_cliente_retencion.nro_comprobante',
                    'ct_cliente_retencion.valor_fuente',
                    'ct_cliente_retencion.valor_iva',
                    'ct_cliente_retencion.total',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'ct_cliente_retencion.estado',
                ]);
        } else {
            return self::join('users as uc', 'ct_cliente_retencion.id_usuariocrea', 'uc.id')
                ->join('ct_clientes as c', 'c.identificacion', 'ct_cliente_retencion.id_cliente')
                ->join('ct_ventas as v', 'v.id', 'ct_cliente_retencion.id_factura')
                ->where('ct_cliente_retencion.id', $r->id)
                ->first([
                    'ct_cliente_retencion.id',
                    'ct_cliente_retencion.fecha',
                    'c.identificacion as id_cliente',
                    'ct_cliente_retencion.descripcion',
                    'v.numero as id_venta',
                    'ct_cliente_retencion.id_asiento_cabecera',
                    'ct_cliente_retencion.nro_comprobante',
                    'ct_cliente_retencion.valor_fuente',
                    'ct_cliente_retencion.valor_iva',
                    'ct_cliente_retencion.total',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'ct_cliente_retencion.estado',
                    'ct_cliente_retencion.nro_comprobante'
                ]);
        }
    }
    public static function cambiarEstadoRetencion($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $ip = Utilidades::getRealIP();
            $idusuario = Auth::user()->id;
            $retencion = self::where('estado', '1')->where('id', $r->id)->first();
            if (!is_null($retencion)) {
                $consulta_compra = Ventas::where('id', $retencion->id_factura)->where('estado', '>', '0')->first();
                if (!is_null($consulta_compra)) {
                    $valor = $consulta_compra->valor_contable + $retencion->total;
                    $consulta_compra->valor_contable = $valor;
                    $consulta_compra->estado_pago = 1;
                    $consulta_compra->ip_modificacion = $ip;
                    $consulta_compra->id_usuariomod = $idusuario;
                    $consulta_compra->save();
                    $retencion = self::where('estado', '1')->where('id', $r->id)->first();
                    $retencion->estado = 0;
                    $retencion->ip_modificacion = $ip;
                    $retencion->id_usuariomod = $idusuario;
                    $retencion->save();
                    $asiento = AsientosCabecera::find($retencion->id_asiento_cabecera);
                    $asiento->estado = 1;
                    $asiento->id_usuariomod = $idusuario;
                    $asiento->save();
                    $detalles = $asiento->detalles;
                    $concepto = $r['observacion'];
                    $id_asiento = AsientosCabecera::insertGetId([
                        'observacion'     => $concepto,
                        'fecha_asiento'   => date("Y-m-d"),
                        'id_empresa'      => session("id_empresa"),
                        'fact_numero'     => $retencion->secuencia,
                        'valor'           => $asiento->valor,
                        'ip_creacion'     => $_SERVER["REMOTE_ADDR"],
                        'ip_modificacion' => $_SERVER["REMOTE_ADDR"],
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'sucursal'        => $asiento->sucursal,
                    ]);
                    foreach ($detalles as $value) {
                        AsientosDetalles::insert([
                            'id_asiento_cabecera' => $id_asiento,
                            'id_plan_cuenta'      => $value->id_plan_cuenta,
                            'debe'                => $value->haber,
                            'haber'               => $value->debe,
                            'descripcion'         => $value->descripcion,
                            'fecha'               => date("Y-m-d"),
                            'ip_creacion'         => $_SERVER["REMOTE_ADDR"],
                            'ip_modificacion'     => $_SERVER["REMOTE_ADDR"],
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                        ]);
                    }
                    $r->c = 'Clientes';
                    $r->s = 'anularRetencionCliente';
                    $r->m = self::$modelo;
                    $r->o = 'Se anulo la retencion con id.: ' . $r->id;
                    Auditorias::saveAuditoria($r, DB::getQueryLog());
                    DB::disableQueryLog();
                    $cont++;
                }
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
            return $arrayRespuesta;
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro anulado correctamente',
                'data' => $r->input(),
                //'reload' => ''
            ];
        }
        return $arrayRespuesta;
    }
    public static function getRetencionPrint($id)
    {
        $contenidoHtml = '';
        $retenciones = self::join('users as uc', 'ct_cliente_retencion.id_usuariocrea', 'uc.id')
            ->join('ct_clientes as c', 'c.identificacion', 'ct_cliente_retencion.id_cliente')
            ->join('ct_ventas as v', 'v.id', 'ct_cliente_retencion.id_factura')
            ->where('ct_cliente_retencion.id', $id)
            ->get([
                'ct_cliente_retencion.id',
                'ct_cliente_retencion.fecha',
                'ct_cliente_retencion.id_cliente',
                'c.nombre',
                'c.direccion_representante',
                'c.telefono1_representante',
                'c.telefono2_representante',
                'c.email_representante',
                'ct_cliente_retencion.id_tipo',
                'ct_cliente_retencion.id_usuariocrea',
                'ct_cliente_retencion.descripcion',
                'v.numero as id_venta',
                'ct_cliente_retencion.nro_comprobante',
                'ct_cliente_retencion.valor_fuente',
                'ct_cliente_retencion.valor_iva',
                'ct_cliente_retencion.total',
                'uc.nombre1 as ucnombre',
                'uc.apellido1 as ucapellido',
                'ct_cliente_retencion.estado',
            ]);
        //dd($retenciones);
        if (count($retenciones) > 0) {
            $fechaMax = date("d/m/Y", strtotime($retenciones[0]->fecha));
            $contenidoHtml .= '<div class="row">
                    <div class="col-12">
                        <h4>
                            <img src="' . url('/') . '/logo/' . config('data.empresa')->logo . '" style="width:120px;height: 60px; margin-left: 11px;" />
                            <small class="float-right">Fecha: ' . date('d/m/Y', strtotime($retenciones[0]->fecha)) . '</small>
                        </h4>
                    </div>
                </div>
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>' . config('data.empresa')->razonsocial . '</strong><br>
                            ' . config('data.empresa')->id . '<br>
                            ' . config('data.empresa')->direccion . '<br>
                            Teléfono: ' . config('data.empresa')->telefono1 . ' ' . config('data.empresa')->telefono2 . '<br>
                            E-mail: <a href="mailto:' . config('data.empresa')->email . '">' . config('data.empresa')->email . '</a>
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>' . $retenciones[0]->id_proveedor . '</strong><br>
                            ' . $retenciones[0]->razonsocial . '<br>
                            ' . $retenciones[0]->direccion . '<br>
                            Teléfono: ' . $retenciones[0]->telefono1_representante . ' ' . $retenciones[0]->telefono2_representante . '<br>
                            E-mail: <a href="mailto:' . $retenciones[0]->email_representante . '">' . $retenciones[0]->email_representante . '</a>
                        </address>
                    </div> 
                    <div class="col-sm-4 invoice-col">
                        <b>Factura #' . $retenciones[0]->nro_comprobante . '</b><br>
                        <br>
                        <b>Fecha máxima de pago:</b> ' . $fechaMax . '<br>
                    </div>
                </div>
                <div class="row"><div class="col-12 table-responsive"><table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Comprobante</th>  
                            <th>Número</th>
                            <th>Fecha Emisión</th>
                            <th>Ejercicio Fiscal</th>
                            <th>Base Imponible</th>
                            <th>Impuesto</th>
                            <th>Codigo</th>
                            <th>Porcentaje de Retención</th>
                            <th>Valor Retenido</th>
                        </tr>
                    </thead>
                    <tbody>';
            $total_final = 0;

            if (count($retenciones) > 0) {
                $det_retenciones = DetalleClienteRetencion::where('id_cliente_retencion', $id)->get();
                foreach ($det_retenciones as $row) {
                    $total_final += $row->totales;
                    $contenidoHtml .= '<tr>
                    <td> Factura </td>
                    <td>' . $retenciones[0]->nro_comprobante . '</td>
                    <td>' . $retenciones[0]->fecha . '</td>
                    <td>' . $retenciones[0]->fecha . '</td>
                    <td align="right">' . number_format($row->base_imponible, 4) . '</td>
                    <td>' . $row->tipo . '</td>
                    <td>' . $row->codigo . '</td>
                    <td align="right">' . $row->porcentaje . '%</td>
                    <td align="right">' . number_format($row->totales, 4) . '</td>
                    </tr>';
                }
            }

            $contenidoHtml .= '</tbody>
                </table></div></div>
                    <div class="row">
                        <div class="col-6">
                        </div>
                        <div class="col-6" align="right">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%">Total:</th>
                                        <td align="right">$ ' . number_format($total_final, 4) . '</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row no-print">
                        <!--<div class="col-12"><a href="' . url('v3/') . '/proveedores/printFactura?id=' . $retenciones[0]->id . '" rel="noopener" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> <span class="botones">Imprimir</span></a></div>-->
                    </div>';
        } else {
            $contenidoHtml = "Busqueda sin resultados";
        }

        return $contenidoHtml;
    }
    public static function saveRetencionCliente($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            $idusuario = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $serie = explode('-', $data['serie_retencion']);
            $arrayAsientoCabecera = [
                'sucursal' => $serie[0],
                'punto_emision' => $serie[1],
                'fecha_asiento' => $r->fecha_retencion,
                'fact_numero' => $r->secuencial_retencion,
                'id_empresa' => session('id_empresa'),
                'observacion' => $r->descripcion,
                'valor' => $r->total_r,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $asiento = Contabilidad::crearAsiento('', '', $arrayAsientoCabecera, $r);
            if ($asiento['status'] == 'error') {
                throw new Exception($asiento["message"]);
            }
            $num_comprobante = $r->serie_retencion . '-' . $r->secuencial_retencion;
            $numComprobante = explode('-', $num_comprobante);
            $arrayDatos = [
                'id_empresa' => session('id_empresa'),
                'id_asiento_cabecera' => $asiento,
                'punto_emision' => $serie[1],
                'sucursal' => $serie[0],
                'tipo' => 1,
                'id_factura' => $r->id_factura,
                'valor_fuente' => $r->renta,
                'valor_iva' => $r->iva,
                'id_tipo' => 1,
                'nro_comprobante' => $num_comprobante,
                'secuencia' => $numComprobante[2],
                'descripcion' => $r->descripcion,
                'total' => $r->total_r,
                'id_cliente' => $r->id_cliente_,
                'estado' => 1,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'fecha'        => $r->fecha_retencion,
                'ip_modificacion' => $ip_cliente,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'autorizacion' => $r->autorizacion_retencion,
            ];
            $idRetencion = self::insertGetId($arrayDatos);
            for ($i = 0; $i < count($data['valor_retencion']); $i++) {
                $a = "RENTA";
                if ($data['tipo_retencion'][$i] == '1') {
                    $a = "IVA";
                }
                $porcentaje = explode("|", $data["porcentaje_retencion"][$i]);
                $arrayDetalleRetencion = [
                    'id_cliente_retencion'  => $idRetencion,
                    'observacion'     => $data['descripcion'],
                    'id_tipo'         => $porcentaje[0],
                    'tipo'            => $a,
                    'id_porcentaje'   => $porcentaje[0],
                    'codigo'          => $porcentaje[1],
                    'base_imponible'  => $data['base_retencion'][$i],
                    'porcentaje'      => number_format($data['codigo_retencion'][$i], 4),
                    'estado'          => '1',
                    'totales'         => $data['valor_retencion'][$i],
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
                DetalleClienteRetencion::insert($arrayDetalleRetencion);
            }
            $actualizar_fac = Ventas::find($data['id_factura']);
            $valord = (float) $data['total_r'];
            $valor_total_compra = $actualizar_fac->valor_contable - $valord;
            $valor_total_compras = $valor_total_compra;
            $actualizar_fac->valor_contable = $valor_total_compras;
            $actualizar_fac->ip_modificacion = $ip_cliente;
            $actualizar_fac->id_usuariomod = $idusuario;
            $actualizar_fac->estado = '1';
            $actualizar_fac->save();
            $asiento = Contabilidad::crearAsiento('cliente', 'retencionCliente', $arrayAsientoCabecera, $r, "", 1, $idRetencion);
            if ($asiento == "") {
                throw new Exception($asiento["message"]);
            }
            $r->c = 'Retenciones';
            $r->s = 'saveRetencion';
            $r->d = $origin['d'];
            $r->m = self::$modelo;
            $r->o = 'Se creo la retencion con id.: ' . $idRetencion;
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
            $r->c = 'Retenciones';
            $r->s = 'saveRetencion';
            $r->d = $origin['d'];
            $r->m = self::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
            return $arrayRespuesta;
        }
        Auditorias::saveAuditoria($r, DB::getQueryLog());
        DB::disableQueryLog();
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => 'v3/clientes/retenciones'
            ];
            return $arrayRespuesta;
        }
    }
}
