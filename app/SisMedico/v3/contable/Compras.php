<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\Retenciones as RetencionesM;
use Sis_medico\AfFacturaActivoCabecera;
use Sis_medico\Http\Controllers\v3\contable\ClaveAcceso;
use Sis_medico\v3\contable\AFFacturaActivoCabecera as ContableAFFacturaActivoCabecera;

class Compras extends Model
{
    private static $modelo = 'Compras';
    protected $table = 'ct_compras';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public function detalles()
    {
        return $this->hasMany('Sis_medico\v3\contable\DetalleCompras', 'id_ct_compras');
    }

    public static function comprasAts($r)
    {
        if ($r->periodo != '') {
            $dato = explode('-', $r->periodo);
            $compras = self::join('ct_master_tipos as tipo', function ($q) {
                $q->on('ct_compras.tipo_comprobante', 'tipo.codigo')
                    ->where('tipo.estado', 1)
                    ->where('tipo.tipo', 1);
            })
                ->join('proveedor as p', 'ct_compras.proveedor', 'p.id')
                ->where('id_empresa', session('id_empresa'))
                ->whereYear('fecha', $dato[0])
                ->whereMonth('fecha', $dato[1])
                ->get([
                    'ct_compras.id',
                    'tipo.nombre as tipo_doc',
                    'ct_compras.id_asiento_cabecera',
                    DB::raw('IF(ct_compras.tipo=2,"COM-FACT",IF(ct_compras.tipo=1,"COM-FACT","")) as tipo'),
                    'ct_compras.fecha',
                    'p.razonsocial',
                    'ct_compras.proveedor',
                    'ct_compras.autorizacion',
                    'ct_compras.observacion',
                    'ct_compras.numero',
                    'ct_compras.subtotal_0',
                    'ct_compras.subtotal_12',
                    'ct_compras.subtotal',
                    'ct_compras.descuento',
                    'ct_compras.iva_total as impuesto',
                    'ct_compras.total_final as total',
                ]);
            return $compras;
            echo '<pre>';
            print_r($compras);
            exit;
        }
    }
    public static function buscarCompras($r)
    {
        return Compras::join('proveedor as p', 'ct_compras.proveedor', 'p.id')
            ->join('ct_master_tipos as t', function ($q) {
                $q->on('ct_compras.tipo_comprobante', 't.codigo')
                    ->where('t.tipo', 1)
                    ->where('t.estado', 1);
            })
            ->join('users as uc', 'ct_compras.id_usuariocrea', 'uc.id')
            ->join('users as um', 'ct_compras.id_usuariomod', 'um.id')
            ->leftjoin('ct_retenciones as r', function ($q) {
                $q->on('ct_compras.id', 'r.id_compra')
                    ->where('r.estado', '!=', 0);
            })
            ->where('ct_compras.id_empresa', session('id_empresa'))
            ->where(function ($q) use ($r) {
                if ($r->p != '') {
                    $q->where('proveedor', $r->p);
                }
                if ($r->d != '' && $r->h != '') {
                    $q->whereBetween('ct_compras.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59']);
                }
            })
            ->get([
                'ct_compras.numero',
                'ct_compras.proveedor',
                'ct_compras.fecha',
                'p.razonsocial',
                'p.direccion',
                'p.telefono1',
                'p.telefono2',
                'p.email',
                'ct_compras.id',
                'ct_compras.id_asiento_cabecera',
                't.nombre as tipo_comprobante',
                'ct_compras.subtotal_0',
                'ct_compras.subtotal',
                'ct_compras.subtotal_12',
                'ct_compras.iva_total',
                'ct_compras.total_final',
                'ct_compras.estado',
                'uc.nombre1',
                'uc.apellido1',
                'ct_compras.doc_electronico',
                DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                DB::raw('IF(ct_compras.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                'ct_compras.valor_contable',
                'ct_compras.total_final',
                'ct_compras.rutapdf',
                'ct_compras.nro_autorizacion',
                'r.id as id_retencion'
            ]);
    }
    public function master_tipos()
    {
        return $this->belongsTo('Sis_medico\v3\contable\MasterTipos', 'tipo_comprobante', 'codigo');
    }
    public static function verificarProveedor($r)
    {
        $arrayErrores = [];
        $proveedor = Proveedor::where('id', $r->proveedor)->first();
        if ($proveedor == '') {
            $error = [
                'proveedor' => 'Proveedor no ingresado'
            ];
            array_push($arrayErrores, $error);
        }
        return json_encode($arrayErrores);
    }
    public static function getCompraPrint($id)
    {
        $contenidoHtml = '';
        $compra = Compras::join('ct_detalle_compra as d', 'ct_compras.id', 'd.id_ct_compras')
            ->join('proveedor as p', 'ct_compras.proveedor', 'p.id')
            ->join("users", "users.id", "ct_compras.id_usuariocrea")
            ->join('ct_master_tipos as t', function ($q) {
                $q->on('ct_compras.tipo_comprobante', 't.codigo')
                    ->where('t.tipo', 1)
                    ->where('t.estado', 1);
            })
            ->leftjoin('ct_retenciones as r', function ($q) {
                $q->on('ct_compras.id', 'r.id_compra')
                    ->where('r.estado', '!=', 0);
            })
            ->where('ct_compras.id', $id)
            ->first([
                'ct_compras.fecha',
                'ct_compras.f_caducidad',
                'p.razonsocial',
                'ct_compras.proveedor',
                'p.direccion',
                'p.telefono1',
                'p.telefono2',
                'p.email',
                't.nombre as tipo_comprobante',
                'ct_compras.tipo_comprobante as t_comprobante',
                'ct_compras.numero',
                'ct_compras.orden_compra',
                'ct_compras.autorizacion',
                'd.porcentaje',
                'ct_compras.observacion',
                'd.cantidad',
                'd.precio',
                'd.descuento',
                'd.codigo',
                'd.nombre',
                'd.iva',
                DB::raw('IFNULL(r.valor_fuente,0) as valor_fuente'),
                DB::raw('IFNULL(r.valor_iva,0) as valor_iva'),
                'ct_compras.nro_autorizacion',
                DB::raw('concat(users.nombre1," ",users.apellido1," ",users.apellido2) as nombreC'),
            ]);
        $existeRetencion = 0;
        if ($compra->valor_fuente != 0) {
            $existeRetencion++;
        }
        if ($compra->valor_iva != 0) {
            $existeRetencion++;
        }
        if ($compra != '') {
            $tipoDocumento = $compra->tipo_comprobante;
            if ($tipoDocumento == 'Liquidacion de compra de Bienes o Prestacion de s') {
                $tipoDocumento = 'Liquidación de compra';
            }
            $contenidoHtml = '';
            $fechaMax = date('d/m/Y', strtotime($compra->f_caducidad));
            $contenidoHtml .= '<div class="row">
                        <div class="col-12">
                            <h4>
                            <img src="' . url('/') . '/logo/' . config('data.empresa')->logo . '" style="width:120px;height: 60px; margin-left: 11px;" />
                                <small class="float-right">Fecha: ' . date('d/m/Y', strtotime($compra->fecha)) . '</small>
                            </h4>
                        </div>
                    </div>
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            <address>
                                <strong>' . $compra->razonsocial . '</strong><br>
                                ' . $compra->proveedor . '<br>
                                ' . $compra->direccion . '<br>
                                Teléfono: ' . $compra->telefono1 . ' ' . $compra->telefono2 . '<br>
                                E-mail: <a href="mailto:' . $compra->email . '">' . $compra->email . '</a>
                            </address>
                        </div>
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
                            <b>' . $tipoDocumento . ' #' . $compra->numero . '</b><br>
                            <br>
                            <b>Pedido ID:</b> ' . $compra->orden_compra . '<br>
                            <b>Fecha máxima de pago:</b> ' . $fechaMax . '<br>
                            <b>Autorización:</b> <span style="font-size:12px">' . $compra->autorizacion . '</span>
                        </div>
                    </div>
                    <div class="row"><div class="col-12 table-responsive"><table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Product</th>
                                <th>Cantidad</th>
                                <th>Valor</th>
                                <th>Descuento</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>';
            $subtotal = 0;
            $descuento = 0;
            $iva = 0;
            $subtotal_ = 0;
            $aPagar = 0;
            $detalle = DetalleCompras::where('id_ct_compras', $id)->get();
            $tipoImpuestos = $detalle->groupBy('porcentaje');
            $impuestos = '';
            if (count($tipoImpuestos) > 0) {
                foreach ($tipoImpuestos as $reo) {
                    if ($reo[0]->porcentaje > 0 && $reo[0]->iva != 0 && $reo[0]->iva !== null) {
                        $impuestos .= ' <tr><th>Iva (' . (($reo[0]->porcentaje) * 100) . '%)</th><td align="right">$ ' . number_format($reo[0]->precio * $reo[0]->porcentaje, 2) . '</td></tr>';
                    }
                }
            }
            if (count($detalle) > 0) {
                foreach ($detalle as $row) {
                    $total = ($row->cantidad * $row->precio) - $row->descuento;
                    $subtotal += $row->cantidad * $row->precio;
                    $descuento += $row->descuento;
                    if ($row->iva == 1) {
                        $iva += ($row->cantidad * $row->precio) * $row->porcentaje;
                    }
                    $subtotal_ += $total;

                    $contenidoHtml .= '<tr>
                    <td>' . $row->codigo . '</td>
                    <td>' . $row->nombre . '</td>
                    <td align="right">' . $row->cantidad . '</td>
                    <td align="right">' . $row->precio . '</td>
                    <td align="right">' . $row->descuento . '</td>
                    <td align="right">' . number_format($total, 4) . '</td>
                    </tr>';
                }
            }
            $aPagar = $subtotal_ + $iva;
            $contenidoHtml .= '</tbody>
                    </table></div></div>
                        <div class="row">
                            <div class="col-6">
                                <p class="lead">Observación:</p>
                                <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                                  ' . nl2br($compra->observacion) . '
                                </p>
                            </div>
                            <div class="col-6">
                                <!--<p class="lead">Monto adeudado ' . $fechaMax . '</p>-->
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th style="width:50%">Total:</th>
                                            <td align="right">$ ' . number_format($subtotal, 2) . '</td>
                                        </tr>
                                        <tr>
                                            <th>Descuento</th>
                                            <td align="right">$ ' . number_format($descuento, 2) . '</td>
                                        </tr>
                                        <tr>
                                            <th>Subtotal:</th>
                                            <td align="right">$ ' . number_format($subtotal_, 2) . '</td>
                                        </tr>
                                       ' . $impuestos . '                           
                                        <tr>
                                            <th>Total:</th>
                                            <td align="right">$ ' . number_format($aPagar, 2) . '</td>
                                        </tr>';
            $existeTotal = 0;
            $sumaRet = 0;
            if ($existeRetencion > 0) {
                $sumaRet = $compra->valor_fuente + $compra->valor_iva;
                $contenidoHtml .= '<tr>
                    <th>Retención: </th>
                    <td align="right" style="background-color:#D7EDF1;">$ ' . $sumaRet . '</td>
                    </tr>';
                $existeTotal++;
            }
            if ($existeTotal > 0) {
                $valorDeuda = $aPagar - $sumaRet;
                $contenidoHtml .= '<tr>
                    <th>Deuda: </th>
                    <td align="right" style="background-color:#D7EDF1;">$ ' . number_format($valorDeuda, 2) . '</td>
                    </tr>';
            }
            $contenidoHtml .= '</table>
                                </div>
                            </div>
                             <div class="col-6">
                            <span style="font-size:small;">Creado por: ' . ucwords(strtolower($compra->nombreC)) . ' impreso: ' . date('Y-m-d H:i:s') . '</span>
                        </div>
                        </div>';
        }
        return $contenidoHtml;
    }
    public function retenciones()
    {
        return $this->hasMany('Sis_medico\v3\contable\Retenciones', 'id_compra');
    }
    public static function valoresCompras()
    {
        $id_empresa = session('id_empresa');
        $periodo = session('periodo');
        $sql = "select * from ((SELECT 'Compras'as tipo,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=1 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) ene,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=2 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) feb,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=3 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) mar,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=4 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) abr,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=5 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) may,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=6 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) jun,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=7 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) jul,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=8 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) ago,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=9 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) sep,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=10 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) oct,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=11 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) nov,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=12 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) dic
            FROM ct_compras cv
            WHERE cv.estado = 1 AND cv.id_empresa = $id_empresa AND YEAR(cv.fecha)=$periodo and tipo=1)
            UNION ALL
            (SELECT 'Gastos'as tipo,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=1 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) ene,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=2 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) feb,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=3 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) mar,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=4 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) abr,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=5 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) may,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=6 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) jun,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=7 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) jul,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=8 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) ago,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=9 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) sep,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=10 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) oct,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=11 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) nov,
            IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=12 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) dic
            FROM ct_compras cv
            WHERE cv.estado = 1 AND cv.id_empresa = $id_empresa AND YEAR(cv.fecha)=$periodo and tipo=2)
            
            ) as compras";
        $compras = DB::select($sql);
        return $compras;
    }
    public static function getComprasSinRetenciones()
    {
        return Compras::join('proveedor as p', 'ct_compras.proveedor', 'p.id')
            ->where('ct_compras.tipo', '<>', 3)
            ->where('ct_compras.estado', '<>', 0)
            ->where('ct_compras.id_empresa', session('id_empresa'))
            ->where('ct_compras.valor_contable', '>', 0)
            ->get([
                'ct_compras.id',
                'ct_compras.proveedor',
                'ct_compras.serie',
                'ct_compras.numero',
                'p.razonsocial',
                'ct_compras.observacion',
            ]);
    }
    public function egresos()
    {
        return $this->hasMany('Sis_medico\v3\contable\DetalleComprobanteEgreso', 'id_compra', 'id');
    }
    public static function getCompra($id)
    {
        $contenidoHtml = 'Busqueda sin resultados';
        $compra = Compras::join('ct_detalle_compra as d', 'ct_compras.id', 'd.id_ct_compras')
            ->join('proveedor as p', 'ct_compras.proveedor', 'p.id')
            ->join("users", "users.id", "ct_compras.id_usuariocrea")
            ->join('ct_master_tipos as t', function ($q) {
                $q->on('ct_compras.tipo_comprobante', 't.codigo')
                    ->where('t.tipo', 1)
                    ->where('t.estado', 1);
            })
            ->leftjoin('ct_retenciones as r', function ($q) {
                $q->on('ct_compras.id', 'r.id_compra')
                    ->where('r.estado', '!=', 0);
            })
            ->where('ct_compras.id', $id)
            ->get([
                'ct_compras.fecha',
                'ct_compras.f_caducidad',
                'p.razonsocial',
                'ct_compras.proveedor',
                'p.direccion',
                'p.telefono1',
                'p.telefono2',
                'p.email',
                't.nombre as tipo_comprobante',
                'ct_compras.tipo_comprobante as t_comprobante',
                'ct_compras.numero',
                'ct_compras.orden_compra',
                'ct_compras.autorizacion',
                'd.porcentaje',
                'ct_compras.observacion',
                'd.cantidad',
                'd.precio',
                'd.descuento',
                'd.codigo',
                'd.nombre',
                'd.iva',
                DB::raw('IFNULL(r.valor_fuente,0) as valor_fuente'),
                DB::raw('IFNULL(r.valor_iva,0) as valor_iva'),
                DB::raw('concat(users.nombre1," ",users.apellido1," ",users.apellido2) as nombreC'),
                'ct_compras.nro_autorizacion'
            ]);
        $existeRetencion = 0;
        $retencion = '';
        if ($compra[0]->valor_fuente != 0) {
            $existeRetencion++;
        }
        if ($compra[0]->valor_iva != 0) {
            $existeRetencion++;
        }
        if ($compra[0] != '') {
            $tipoDocumento = $compra[0]->tipo_comprobante;
            if ($tipoDocumento == 'Liquidacion de compra de Bienes o Prestacion de s') {
                $tipoDocumento = 'Liquidación de compra';
            }
            $contenidoHtml = '';
            //$fechaMax = date("d/m/Y", strtotime($compra[0]->fecha . "+ " . $compra[0]->dias_plazo . " days"));
            $fechaMax = date('d/m/Y', strtotime($compra[0]->f_caducidad));
            $contenidoHtml .= '<div class="row">
                    <div class="col-12">
                        <h4>
                        <img src="' . url('/') . '/logo/' . config('data.empresa')->logo . '" style="width:120px;height: 60px; margin-left: 11px;" />
                            <small class="float-right">Fecha: ' . date('d/m/Y', strtotime($compra[0]->fecha)) . '</small>
                        </h4>
                    </div>
                </div>
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>' . $compra[0]->razonsocial . '</strong><br>
                            ' . $compra[0]->proveedor . '<br>
                            ' . $compra[0]->direccion . '<br>
                            Teléfono: ' . $compra[0]->telefono1 . ' ' . $compra[0]->telefono2 . '<br>
                            E-mail: <a href="mailto:' . $compra[0]->email . '">' . $compra[0]->email . '</a>
                        </address>
                    </div>
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
                        <b>' . $tipoDocumento . ' #' . $compra[0]->numero . '</b><br>
                        <br>
                        <b>Pedido ID:</b> ' . $compra[0]->orden_compra . '<br>
                        <b>Fecha máxima de pago:</b> ' . $fechaMax . '<br>
                        <b>Autorización:</b> <span style="font-size:12px">' . $compra[0]->autorizacion . '</span>
                    </div>
                </div>
                <div class="row"><div class="col-12 table-responsive"><table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Product</th>
                            <th>Cantidad</th>
                            <th>Valor</th>
                            <th>Descuento</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>';
            $subtotal = 0;
            $descuento = 0;
            $iva = 0;
            $subtotal_ = 0;
            $aPagar = 0;
            if (count($compra) > 0) {
                foreach ($compra as $row) {
                    $total = ($row->cantidad * $row->precio) - $row->descuento;
                    $subtotal += $row->cantidad * $row->precio;
                    $descuento += $row->descuento;
                    if ($row->iva == 1) {
                        $iva += ($row->cantidad * $row->precio) * $row->porcentaje;
                    }
                    $subtotal_ += $total;

                    $contenidoHtml .= '<tr>
                <td>' . $row->codigo . '</td>
                <td>' . $row->nombre . '</td>
                <td align="right">' . $row->cantidad . '</td>
                <td align="right">' . $row->precio . '</td>
                <td align="right">' . $row->descuento . '</td>
                <td align="right">' . number_format($total, 4) . '</td>
                </tr>';
                }
            }
            //$detalle = DetalleCompras::where('id_ct_compras', $id)->where('iva', 1)->get();
            $detalle = DetalleCompras::where('id_ct_compras', $id)->get();
            $tipoImpuestos = $detalle->groupBy('porcentaje');
            $impuestos = '';
            if (count($tipoImpuestos) > 0) {
                foreach ($tipoImpuestos as $reo) {
                    if ($reo[0]->porcentaje > 0 && $reo[0]->iva != 0 && $reo[0]->iva !== null) {
                        $impuestos .= ' <tr><th>Iva (' . (($reo[0]->porcentaje) * 100) . '%)</th><td align="right">$ ' . number_format($reo[0]->precio * $reo[0]->porcentaje, 2) . '</td></tr>';
                    }
                }
            }
            $aPagar = $subtotal_ + $iva;
            $contenidoHtml .= '</tbody>
                </table></div></div>
                    <div class="row">
                        <div class="col-6">
                            <p class="lead">Observación:</p>
                            <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                              ' . nl2br($compra[0]->observacion) . '
                            </p>
                        </div>
                        <div class="col-6">
                            <!--<p class="lead">Monto adeudado ' . $fechaMax . '</p>-->
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%">Total:</th>
                                        <td align="right">$ ' . number_format($subtotal, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <th>Descuento</th>
                                        <td align="right">$ ' . number_format($descuento, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <th>Subtotal:</th>
                                        <td align="right">$ ' . number_format($subtotal_, 2) . '</td>
                                    </tr>
                                    ' . $impuestos . '                               
                                    <tr>
                                        <th>Total:</th>
                                        <td align="right">$ ' . number_format($aPagar, 2) . '</td>
                                    </tr>';
            $existeTotal = 0;
            $sumaRet = 0;
            if ($existeRetencion > 0) {
                $sumaRet = $compra[0]->valor_fuente + $compra[0]->valor_iva;
                $contenidoHtml .= '<tr>
                <th>Retención: </th>
                <td align="right" style="background-color:#D7EDF1;">$ ' . $sumaRet . '</td>
                </tr>';
                $existeTotal++;
            }
            if ($existeTotal > 0) {
                $valorDeuda = $aPagar - $sumaRet;
                $contenidoHtml .= '<tr>
                <th>Deuda: </th>
                <td align="right" style="background-color:#D7EDF1;">$ ' . number_format($valorDeuda, 2) . '</td>
                </tr>';
            }
            $opcionesLiqui = '';
            if ($compra[0]->t_comprobante == '03') {
                $opcionesLiqui = '<a href="' . url('/') . '/api_doc_electronico?opcion=descargarXML&clave=' . $compra[0]->nro_autorizacion . '" class="btn btn-success float-right"><i class="far fa-file-code"></i>  <span class="botones">Generar XML</span></a><a href="' . url('/') . '/api_doc_electronico?opcion=generarPdf&clave=' . $compra[0]->nro_autorizacion . '" type="button" class="btn btn-primary float-right" style="margin-right: 5px;"><i class="fas fa-download"></i>  <span class="botones">Generar PDF</span></a>';
            }
            $contenidoHtml .= '</table>
                            </div>
                        </div>
                    </div> 
                    <div class="row no-print">
                        <div class="col-12">
                            <a href="' . url('v3/') . '/compras/printFactura?id=' . $id . '" rel="noopener" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> <span class="botones">Imprimir</span></a>
                            ' . $opcionesLiqui . '
                        </div>
                    </div>
                     <div class="col-6">
                            <span style="font-size:x-small;">Creado por: ' . ucwords(strtolower($compra[0]->nombreC)) . '</span>
                        </div>';
        }
        return $contenidoHtml;
    }
    public static function getDetalleCompra($id)
    {
        //echo $id; exit();
        $contenidoHtml = '';
        $compra = Compras::join('ct_detalle_compra as d', 'ct_compras.id', 'd.id_ct_compras')
            //->join('proveedor as p', 'ct_compras.proveedor', 'p.id')
            ->where('ct_compras.id', $id)
            ->get();
        if ($compra[0] != '') {
            $fechaMax = date("d/m/Y", strtotime($compra[0]->fecha . "+ " . $compra[0]->dias_plazo . " days"));

            if (count($compra) > 0) {
                $cant_det_compras = 0;
                $iteracion = 0;
                $fila = 0;
                foreach ($compra as $row) {
                    $iteracion++;
                    $subtotal = 0;
                    $descuento = 0;
                    $iva = 0;
                    $subtotal_ = 0;
                    $aPagar = 0;
                    $subtotal0 = 0;
                    $subtotal12 = 0;
                    $subtotal_total = 0;
                    $abono = 0;
                    $impuesto = 0;
                    $tiene_iva = "No";
                    $porcentaje_iva = $row->porcentaje;
                    $impuesto = $row->impuesto;
                    $subtotal += $row->cantidad * $row->precio;
                    $descuento += $row->descuento;
                    if ($row->iva == 1) {
                        $iva += ($row->cantidad * $row->impuesto);
                    }
                    // $subtotal_ += $total;
                    if ($row->iva == 1) {
                        $tiene_iva = "Si";
                        $subtotal12 = ($row->cantidad * $row->precio);
                    } else {
                        $subtotal0 = ($row->cantidad * $row->precio);
                    }
                    $abono = ($row->cantidad * $row->precio);
                    $subtotal_total = $subtotal12 + $subtotal0;
                    $total = $subtotal_total + $iva - $descuento;
                    $cant_det_compras++;
                    //                <!--td > <input type="checkbox" name="ch[]" onchange="total_detalle_compra()"> </td>-->
                    $contenidoHtml .= '<tr>
                        <td align="center"><div class="icheck-primary d-inline"><input id="ch' . $fila . '" type="checkbox" name="ch[]" onchange="total_detalle_compra();"><label for="ch' . $fila . '"></label></div></td>
                        <td >' . $row->codigo . '</td>
                        <td >' . $row->nombre . '</td>
                        <td >' . $row->cantidad . '</td>
                        <td align="right">' . $row->precio . '</td>
                        <td align="right">' . $row->descuento . '</td>
                        <td align="right">' . $iva . '</td>
                        <td align="center">' . $tiene_iva . '</td>.
                        <td align="right">' . number_format($total, 4) . '</td>
                        <td align="right"><input type="text" name="abono[]" class="form-control text-right" value="' . str_replace(',', '', number_format($abono, 2)) . '" onchange="valida_total_abono_nc(' . $abono . ' ,this);"> 
                        <input type="hidden" name="id_detalle[]" value="' . $row->id . '">
                        <input type="hidden" name="subtotal0[]" value="' . $subtotal0 . '">
                        <input type="hidden" name="subtotal12[]" value="' . $subtotal12 . '">
                        <input type="hidden" name="subtotal_total[]" value="' . $subtotal_total . '">
                        <input type="hidden" name="iva[]" value="' . $iva . '">
                        <input type="hidden" name="porcentaje_iva[]" value="' . $porcentaje_iva . '">
                        <input type="hidden" name="val_impuesto[]" value="' . $impuesto . '">
                        <input type="hidden" name="total[]" value="' . $total . '">';
                    if ($iteracion == count($compra)) {
                        $contenidoHtml .=  '<input type="hidden" name="cantidad_detalles" value="' . $iteracion . '"><input type="hidden" name="cantidad_ok"  id="cantidad_ok" value="">';
                    }
                    $contenidoHtml .= '</td>
                </tr>';
                    $fila++;
                }
            }
        } else {
            $contenidoHtml = 'Búsqueda sin Resultados';
        }
        return $contenidoHtml;
    }
    public static function getAsiento($id)
    {
        $asiento = AsientosCabecera::where('id', $id)->first();
        dd($asiento);
    }
    public static function getComprasAll($id = '', $r)
    {
        try {
            if ($id == '') {
                return Compras::join('proveedor as p', 'ct_compras.proveedor', 'p.id')
                    ->join('ct_master_tipos as t', function ($q) {
                        $q->on('ct_compras.tipo_comprobante', 't.codigo')
                            ->where('t.estado', 1)
                            ->where('t.tipo', 1);
                    })
                    ->join('users as uc', 'ct_compras.id_usuariocrea', 'uc.id')
                    ->join('users as um', 'ct_compras.id_usuariomod', 'um.id')
                    ->where('ct_compras.id_empresa', session('id_empresa'))
                    ->where(function ($q) use ($r) {
                        $q->where('p.razonsocial', 'LIKE', '%' . $r->term . '%')
                            ->orWhere('ct_compras.numero', 'LIKE', '%' . $r->term . '%')
                            ->orWhere('ct_compras.observacion', 'LIKE', '%' . $r->term . '%')
                            ->orWhere('p.id', 'LIKE', '%' . $r->term . '%');
                    })
                    ->get([
                        'ct_compras.numero',
                        'ct_compras.proveedor',
                        'ct_compras.fecha',
                        'p.razonsocial',
                        'p.direccion',
                        'p.telefono1',
                        'p.telefono2',
                        'p.email',
                        'ct_compras.id',
                        'ct_compras.id_asiento_cabecera',
                        't.nombre as tipo_comprobante',
                        'ct_compras.subtotal_0',
                        'ct_compras.serie',
                        'ct_compras.subtotal',
                        'ct_compras.subtotal_12',
                        'ct_compras.iva_total',
                        'ct_compras.total_final',
                        'ct_compras.observacion',
                        'ct_compras.estado',
                        'uc.nombre1',
                        'uc.apellido1',
                        'ct_compras.doc_electronico',
                        DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                        DB::raw('IF(ct_compras.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                        'ct_compras.valor_contable',
                        'ct_compras.total_final',
                        'ct_compras.rutapdf',
                        'ct_compras.nro_autorizacion'
                    ]);
            } else {
                return Compras::join('proveedor as p', 'ct_compras.proveedor', 'p.id')
                    ->leftjoin('ct_master_tipos as tipo', function ($q) {
                        $q->on('ct_compras.tipo_comprobante', 'tipo.codigo')
                            ->where('tipo.tipo', 1);
                    })
                    ->where(function ($q) use ($r) {
                        $q->where('p.razonsocial', 'LIKE', '%' . $r->term . '%')
                            ->orWhere('ct_compras.numero', 'LIKE', '%' . $r->term . '%')
                            ->orWhere('ct_compras.observacion', 'LIKE', '%' . $r->term . '%')
                            ->orWhere('p.id', 'LIKE', '%' . $r->term . '%');
                    })
                    ->where('ct_compras.id', $id)
                    ->first([
                        'ct_compras.id',
                        'ct_compras.serie',
                        'ct_compras.numero',
                        'ct_compras.proveedor',
                        'ct_compras.fecha',
                        'ct_compras.observacion',
                        'ct_compras.autorizacion',
                        'ct_compras.secuencia_factura',
                        'p.razonsocial',
                        'p.direccion',
                        'ct_compras.subtotal',
                        'ct_compras.subtotal_0',
                        'ct_compras.subtotal_12',
                        DB::raw('IFNULL(ct_compras.iva_total,0) as iva_total'),
                        'tipo.nombre as tipo_comprobante',
                        'ct_compras.tipo',
                        'ct_compras.valor_contable',
                        'ct_compras.total_final',
                        'ct_compras.nro_autorizacion'
                    ]);
            } //revision 11/06/2024
        } catch (Exception $e) {
            echo '<pre>', print_r($e->getMessage());
            print_r($e->getLine());
            exit;
        }
    }
    public static function getCompras($id = '', $r)
    {
        try {
            if ($id == '') {
                return Compras::join('proveedor as p', 'ct_compras.proveedor', 'p.id')
                    ->join('ct_master_tipos as t', function ($q) {
                        $q->on('ct_compras.tipo_comprobante', 't.codigo')
                            ->where('t.tipo', 1)
                            ->where('t.estado', 1);
                    })
                    ->leftjoin('ct_retenciones as r', function ($q) {
                        $q->on('ct_compras.id', 'r.id_compra')
                            ->where('r.estado', '!=', 0);
                    })
                    ->join('users as uc', 'ct_compras.id_usuariocrea', 'uc.id')
                    ->join('users as um', 'ct_compras.id_usuariomod', 'um.id')
                    ->whereYear('ct_compras.fecha', session('periodo'))
                    ->where('ct_compras.id_empresa', session('id_empresa'))
                    ->where(function ($q) use ($r) {
                        $q->where('p.razonsocial', 'LIKE', '%' . $r->term . '%')
                            ->orWhere('ct_compras.numero', 'LIKE', '%' . $r->term . '%')
                            ->orWhere('p.id', 'LIKE', '%' . $r->term . '%');
                    })
                    ->get([
                        'ct_compras.numero',
                        'ct_compras.proveedor',
                        'ct_compras.fecha',
                        'p.razonsocial',
                        'p.direccion',
                        'p.telefono1',
                        'p.telefono2',
                        'p.email',
                        'ct_compras.id',
                        'ct_compras.id_asiento_cabecera',
                        't.nombre as tipo_comprobante',
                        'ct_compras.subtotal_0',
                        'ct_compras.subtotal',
                        'ct_compras.subtotal_12',
                        'ct_compras.iva_total',
                        'ct_compras.total_final',
                        'ct_compras.estado',
                        'uc.nombre1',
                        'uc.apellido1',
                        'ct_compras.doc_electronico',
                        DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                        DB::raw('IF(ct_compras.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                        'ct_compras.valor_contable',
                        'ct_compras.total_final',
                        'ct_compras.rutapdf',
                        'ct_compras.nro_autorizacion',
                        'r.id as id_retencion'
                    ]);
            } else {
                return Compras::join('proveedor as p', 'ct_compras.proveedor', 'p.id')
                    ->leftjoin('ct_master_tipos as tipo', function ($q) {
                        $q->on('ct_compras.tipo_comprobante', 'tipo.codigo')
                            ->where('tipo.tipo', 1);
                    })
                    ->where(function ($q) use ($r) {
                        $q->where('p.razonsocial', 'LIKE', '%' . $r->term . '%')
                            ->orWhere('ct_compras.numero', 'LIKE', '%' . $r->term . '%')
                            ->orWhere('p.id', 'LIKE', '%' . $r->term . '%');
                    })
                    ->where('ct_compras.id', $id)
                    ->first([
                        'ct_compras.id',
                        'ct_compras.serie',
                        'ct_compras.numero',
                        'ct_compras.proveedor',
                        'ct_compras.fecha',
                        'ct_compras.observacion',
                        'ct_compras.autorizacion',
                        'ct_compras.secuencia_factura',
                        'p.razonsocial',
                        'p.direccion',
                        'ct_compras.subtotal',
                        'ct_compras.subtotal_0',
                        'ct_compras.subtotal_12',
                        DB::raw('IFNULL(ct_compras.iva_total,0) as iva_total'),
                        'tipo.nombre as tipo_comprobante',
                        'ct_compras.tipo',
                        'ct_compras.valor_contable',
                        'ct_compras.total_final',
                        'ct_compras.nro_autorizacion'
                    ]);
            } //revision 11/06/2024
        } catch (Exception $e) {
            echo '<pre>', print_r($e->getMessage());
            print_r($e->getLine());
            exit;
        }
    }
    public static function getComprasProveedor($id = '')
    {
        $contenidoHtml = '';
        $compra =  Compras::join('proveedor as p', 'ct_compras.proveedor', 'p.id')
            ->join('ct_master_tipos as t', function ($q) {
                $q->on('ct_compras.tipo_comprobante', 't.codigo')
                    ->where('t.tipo', 1)
                    ->where('t.estado', 1);
            })
            ->whereYear('fecha', session('periodo'))
            ->where('ct_compras.id_empresa', session('id_empresa'))
            ->where('ct_compras.proveedor', $id)
            ->get([
                'ct_compras.numero',
                'ct_compras.proveedor',
                'ct_compras.fecha',
                'p.razonsocial',
                'p.direccion',
                'p.telefono1',
                'p.telefono2',
                'p.email',
                'ct_compras.id',
                'ct_compras.tipo',
                'ct_compras.id_asiento_cabecera',
                't.nombre as tipo_comprobante',
                'ct_compras.subtotal_0',
                'ct_compras.subtotal',
                'ct_compras.subtotal_12',
                'ct_compras.iva_total',
                'ct_compras.total_final',
                'ct_compras.estado',
                'ct_compras.doc_electronico',
                'ct_compras.valor_contable',
                'ct_compras.observacion',
            ]);
        //dd($compra);
        if ($compra != '') {
            if (count($compra) > 0) {
                $cant_det_compras = 0;
                $iteracion = 0;
                foreach ($compra as $row) {
                    $iteracion++;
                    $abono = 0;
                    $fila = 0;
                    $cant_det_compras++;
                    $contenidoHtml .= '<tr>
                <td>' . $row->fecha . '</td>
                <td>' . $row->tipo_comprobante . '</td>
                <td>' . $row->numero . '</td>
                <td>' . $row->observacion . '</td>
                <td align="right">' .  '$' . '</td>
                <td align="right">' . $row->valor_contable . '</td>
                <td align="right">' . '<input type="text" class="form-control text-right" name="abono[]" value="' . $abono . '" onchange="valida_total_abono_nd(' . $abono . ' ,this);">
                <td align="right">' . $row->valor_contable . '</td> ;

                </tr>';
                    $fila++;
                }
            }
        } elseif ($compra == []) {
            //dd("aqui");
            $contenidoHtml = 'Búsqueda sin Resultados';
        } else {
            $contenidoHtml = 'Búsqueda sin Resultados';
        }
        //dd($compra);
        return $contenidoHtml;
    }
    public static function obtenerCompras($id = '')
    {
        if ($id == '') {
            return Compras::join('users as uc', 'ct_compras.id_usuariocrea', 'uc.id')
                ->leftjoin('proveedor as pv', 'pv.id', 'ct_compras.proveedor')
                ->where('ct_compras.tipo', 3)
                ->get([
                    'ct_compras.id',
                    'ct_compras.observacion',
                    'ct_compras.fecha',
                    'pv.razonsocial as proveedor',
                    'ct_compras.total_final',
                    'ct_compras.estado as estado'
                ]);
        } else {
            return Compras::join('users as uc', 'ct_compras.id_usuariocrea', 'uc.id')
                ->leftjoin('proveedor as pv', 'pv.id', 'ct_compras.proveedor')
                ->where('ct_compras.tipo', 3)
                ->where('ct_compras.id', $id)
                ->first([
                    'ct_compras.id',
                    'ct_compras.observacion',
                    'ct_compras.fecha',
                    'pv.razonsocial as proveedor',
                    'ct_compras.total_final',
                    'ct_compras.estado as estado'
                ]);
        }
    }
    public static function cambiarEstado($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (Compras::where('id', $r->id)->update(['estado' => $estado])) {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro guardado correctamente',
                    'data' => $r->input(),
                ];
            } else {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Error al actualizar el estado del ',
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
    public static function saveCompra($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $dataDetalle = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $clave_acceso = "";
            $ip = Utilidades::getRealIP();
            $compra = Compras::find($data['id']);
            $sucursal = Sucursales::where('id', $data['sucursal'])->first();
            $punto_emision = Caja::where('id', $data['punto_emision'])->first();
            $proveedor = Proveedor::find($r->proveedor);
            if ($proveedor == '') {
                throw new Exception("El proveedor no existe, debe ser creado desde el módulo de proveedores");
            }
            $arrayAsientoCabecera = [
                'sucursal' => $sucursal->codigo_sucursal,
                'punto_emision' => $punto_emision->codigo_caja,
                'fecha_asiento' => $r->fecha,
                'fact_numero' => $r->secuencia_factura,
                'id_empresa' => session('id_empresa'),
                'observacion' => $r->observacion,
                'valor' => $r->total_pagar,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ];
            $id_asiento_cabecera = Contabilidad::crearAsiento('compras', 'compra', $arrayAsientoCabecera,  $r);
            if ($id_asiento_cabecera['status'] == 'error') {
                throw new Exception($id_asiento_cabecera['message']);
            }
            $valorMC = $r->subtotal_mc == '' ? 0 : $r->subtotal_mc;
            if ($compra == '') {
                $numero = 1;
                $secuencia = '000000001';
                if ($r->tipo_comprobante == '03') {
                    $datosInfo = InfoTributaria::where('id', 4)
                        ->where('cod_sucursal', $sucursal->codigo_sucursal)
                        ->where('cod_caja', $punto_emision->codigo_caja)
                        ->where('id_empresa', session('id_empresa'))
                        ->first();
                    if ($datosInfo != '') {
                        $numero =  $sucursal->codigo_sucursal . '-' . $punto_emision->codigo_caja . '-' . str_pad($datosInfo->secuencial_nro + 1, 9, 0, STR_PAD_LEFT);
                        $secuencia = str_pad($datosInfo->secuencial_nro + 1, 9, 0, STR_PAD_LEFT);
                    } else {
                        $numero =  $sucursal->codigo_sucursal . '-' . $punto_emision->codigo_caja . '-' . str_pad(1, 9, 0, STR_PAD_LEFT);
                        $secuencia = str_pad(1, 9, 0, STR_PAD_LEFT);
                    }
                    $claveObj = new ClaveAcceso();
                    $numComprobante = explode('-', $numero);
                    $deEmpresa = DeEmpresas::where('id_empresa', session('id_empresa'))->first();
                    if ($deEmpresa != '') {
                        $campos = [
                            'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($r->fecha))),
                            'tipo_comprobante' => '03',
                            'ruc' => session('id_empresa'),
                            'tipo_ambiente' => $deEmpresa->ambiente,
                            'punto_establecimiento' => $numComprobante[0],
                            'punto_emision' => $numComprobante[1],
                            'numero_comprobante' => $numComprobante[2],
                            'codigo_numerico' => $numComprobante[2],
                            'tipo_emision' => 1
                        ];
                        $claveObj->llenarDatos($campos);
                        $clave_acceso = $claveObj->clave_acceso;
                    }
                }
                $cabecera['numero'] = $r->tipo_comprobante != '03' ? $r->serie . '-' . $r->secuencia_factura : $numero;
                $cabecera['secuencia_f'] = $r->tipo_comprobante != '03' ? $r->secuencia_factura : $secuencia;
                $cabecera['punto_emision'] =  $punto_emision->codigo_caja;
                $cabecera['sucursal'] = $sucursal->codigo_sucursal;
                $cabecera['pedido'] = $r->pedido;
                $cabecera['tipo'] = $r->tipo; //1:compras, 2: fact contable, 3:liquidacion
                $cabecera['fecha_termino'] = $r->fecha_termino;
                $cabecera['fecha'] = $r->fecha;
                $cabecera['estado'] = 1; //0:inactivo, 1:activo, 2:con retencion
                $cabecera['archivo_sri'] = $r->archivo_sri == 'on' ? 1 : 0;
                $cabecera['proveedor'] = $r->proveedor;
                $cabecera['direccion_proveedor'] = $proveedor->direccion;
                $cabecera['termino'] = $r->termino;
                $cabecera['orden_compra'] = $r->orden_compra;
                $cabecera['f_caducidad'] = $r->f_caducidad;
                $cabecera['tipo_gasto'] = $r->tipo_gasto;
                $cabecera['autorizacion'] = $r->autorizacion;
                $cabecera['f_autorizacion'] = $r->f_autorizacion;
                $cabecera['valor_contable'] = $r->total_pagar;
                $cabecera['serie'] = $r->tipo_comprobante != '03' ?  $r->serie : $sucursal->codigo_sucursal . '-' . $punto_emision->codigo_caja;
                $cabecera['secuencia_factura'] = $r->secuencia_factura;
                $cabecera['credito_tributario'] = $r->credito_tributario;
                $cabecera['tipo_comprobante'] = $r->tipo_comprobante;
                $cabecera['observacion'] = $r->observacion;
                $cabecera['subtotal_0'] = $r->subtotal_0;
                $cabecera['subtotal_12'] = $r->subtotal_12 + $valorMC;
                $cabecera['subtotal'] = $r->subtotal_0 + $r->subtotal_12 + $valorMC;
                $cabecera['descuento'] = $r->descuento;
                $cabecera['iva_total'] = $r->iva + $r->iva_mc;
                $cabecera['ice_total'] = $r->ice_total;
                $cabecera['transporte'] = $r->transporte == '' ? null : $r->transporte;
                $cabecera['total_final'] = $r->total_pagar;
                $cabecera['id_empresa'] = session('id_empresa');
                $cabecera['rutapdf'] = $r->rutapdf;
                $cabecera['doc_electronico'] = $r->tipo_comprobante == '03' ? 0 : null;
                $cabecera['electronica'] = $r->tipo_comprobante == '03' ? 1 : 0;
                $cabecera['nro_comprobante'] = $r->tipo_comprobante != '03' ? $sucursal->codigo_sucursal . '-' . $punto_emision->codigo_caja . '-' . $r->secuencia_factura : $numero;
                $cabecera['nro_autorizacion'] = $clave_acceso;
                $cabecera['forma_pago'] = $r->forma_pago;
                $cabecera['emisor'] = $r->emisor != '' ? $r->emisor : 'MD';
                $cabecera['clave_acceso'] = $r->clave_acceso;
                $cabecera['id_usuariocrea'] = Auth::user()->id;
                $cabecera['id_usuariomod'] = Auth::user()->id;
                $cabecera['ip_creacion'] = $ip;
                $cabecera['ip_modificacion'] = $ip;
                $cabecera['id_asiento_cabecera'] = $id_asiento_cabecera;
                $id = Compras::insertGetId($cabecera);
                for ($i = 0; $i < count($dataDetalle["codigo"]); $i++) {
                    $nombre = explode("|", $r->nombre[$i])[2];
                    $detalle['id_ct_compras'] = $id;
                    $detalle['detalle'] = $r->detalle[$i];
                    $detalle['codigo'] = $r->codigo[$i];
                    $detalle['nombre'] = $nombre;
                    $detalle['bodega'] = $r->bodega[$i];
                    $detalle['cantidad'] = $r->cantidad[$i];
                    $detalle['total'] = $r->totald[$i];
                    $detalle['precio'] = $r->precio[$i];
                    $detalle['descuento_porcentaje'] = $r->descuento_porcentaje[$i];
                    $detalle['descuento'] = $r->descuentod[$i];
                    $detalle['extendido'] = $r->cantidad[$i] * $r->precio[$i];
                    $detalle['iva'] = $r->cheIvat[$i];
                    $detalle['porcentaje'] = $r->iva_porcentaje[$i] == '' ? 0 : $r->iva_porcentaje[$i];
                    $detalle['codigoref'] = $r->codigoref[$i];
                    $detalle['lote'] = $r->lote[$i];
                    $detalle['fecha_exp'] = $r->fecha_exp[$i];
                    $detalle['estado'] = 1;
                    $detalle['id_usuariocrea'] = Auth::user()->id;
                    $detalle['id_usuariomod'] = Auth::user()->id;
                    $detalle['ip_creacion'] = $ip;
                    $detalle['ip_modificacion'] = $ip;
                    CtDetalleCompra::insertGetId($detalle);
                }
                $r->c = 'Compras';
                $r->s = 'saveCompra';
                $r->d = $origin['d'];
                $r->m = Compras::$modelo;
                $r->o = 'Se creo la compra con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                Compras::where('id', $id)->update($data);
                $r->c = 'Compras';
                $r->s = 'saveCompra';
                $r->d = $origin['d'];
                $r->m = Compras::$modelo;
                $r->o = 'Se actualizo la compra con id.: ' . $id;
                $cont++;
            }
            Auditorias::saveAuditoria($r, DB::getQueryLog());
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
            $r->c = 'Compras';
            $r->s = 'saveCompra';
            $r->d = $origin['d'];
            $r->m = Compras::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => 'v3/compras/listadecompras',
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function getDeudasvsPagos($r)
    {
        $id_empresa = session('id_empresa');
        $id_proveedor = '';
        if ($r->id_proveedor != null) {
            $id_proveedor = $r->id_proveedor;
        }
        $sql = 'call sp_proveedores_deudas_pagos(?,?,?,?)';
        return DB::select($sql, [session('id_empresa'), $r->d . ' 00:00:00', $r->h . ' 23:59:59', $id_proveedor]);
    }
    public static function verificarCompra($r)
    {
        try {
            $compra = Compras::find($r["id_compra"]);
            $tabla_egre        = "";
            $tabla_banca       = "";
            $tabla_cruce       = "";
            $tabla_rete        = "";
            $tabla_egre_masivo = "";
            $tabla_cruce_cuentas = "";
            $id_egreso         = 0;
            $id_deb_ban        = 0;
            $id_cruce_valores  = 0;
            $id_retencion      = 0;
            $id_egreso_masivo  = 0;
            $id_cruce_cuentas  = 0;
            $id    = $r['id_compra'];
            $tiene = "";
            $retenciones = Retenciones::where('id_compra', $id)->get();
            if (count($retenciones) > 0) {
                foreach ($retenciones as $value) {
                    if ($value->estado == '1') {
                        $tiene        = "si";
                        $id_retencion = $retenciones[0]->id;
                    }
                }
                if ($tiene == "si") {
                    $tabla_rete = "Retenciones ";
                }
            }
            $com_egre = DetalleComprobanteEgreso::where('id_compra', $id)->get();
            if (count($com_egre) > 0) {
                foreach ($com_egre as $value) {
                    $egreso = ComprobanteEgreso::where('id', $value->id_comprobante)->where('estado', '=', '1')->get();
                    if (count($egreso) > 0) {
                        $tiene      = "si";
                        $tabla_egre = "Comprobante de Egreso ";
                        $id_egreso  = $egreso[0]->id;
                    }
                }
            }

            $debito_banca_det = DebitoBancarioDetalle::where('id_compra', $id)->get();
            if (count($debito_banca_det) > 0) {
                foreach ($debito_banca_det as $value) {
                    $debit_banca = DB::table('ct_debito_bancario')->where('id', $value->id_debito)->where('estado', '=', '1')->get();
                    if (count($debit_banca) > 0) {
                        $tiene       = "si";
                        $tabla_banca = "Debito Bancario ";
                        $id_deb_ban  = $debit_banca[0]->id;
                    }
                }
            }

            $detalle_cruce = DetalleCruce::where('id_factura', $id)->get();
            if (count($detalle_cruce) > 0) {
                foreach ($detalle_cruce as $value) {
                    $cruce_valores = CruceValores::where('id', $value->id_comprobante)->where('estado', '1')->get();
                    if (count($cruce_valores) > 0) {
                        $tiene            = "si";
                        $tabla_cruce      = "Cruce Valores ";
                        $id_cruce_valores = $cruce_valores[0]->id;
                    }
                }
            }

            $det_egreso_masivo = DetalleComprobanteEgreso::where('id_compra', $id)->get();
            if (count($det_egreso_masivo) > 0) {

                foreach ($det_egreso_masivo as $value) {

                    $egreso_masivo = ComprobanteEgresoMasivo::where('id', $value->id_comprobante)->where('estado', '=', '1')->get();
                    if (count($egreso_masivo) > 0) {

                        $tiene             = "si";
                        $tabla_egre_masivo = "Comprobante de Egreso Masivo";
                        $id_egreso_masivo  = $egreso_masivo[0]->id;
                    }
                }
            }

            $cruce_cuentas = CruceCuentas::where('id_factura', $id)->where('estado', 1)->first();
            if (!is_null($cruce_cuentas)) {
                $tabla_cruce_cuentas = "Cruce de Cuentas";
                $id_cruce_cuentas = $cruce_cuentas->id;
            }

            $tablas = [$tabla_egre, $tabla_banca, $tabla_cruce, $tabla_rete, $tabla_egre_masivo, $tabla_cruce_cuentas];
            $ids    = [$id_egreso, $id_deb_ban, $id_cruce_valores, $id_retencion, $id_egreso_masivo, $id_cruce_cuentas];

            if ($tiene == 'si') {
                return ['status' => false, 'id' => $id, 'tablas' => $tablas, 'msj' => $tiene, "ids" => $ids];
            } else {
                return ['status' => true,  'id' => $id, 'tablas' => $tablas, 'msj' => $tiene, "ids" => $ids];
            }
        } catch (Exception $e) {
            return ["status" => false, 'msj' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }
    public static function anularCompra($r)
    {

        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $fechahoy = Date('Y-m-d H:i:s');
            $compras = Compras::where('id', $r["id_compra"])->first();
            $compras->id_usuariomod =  $idusuario;
            $compras->ip_modificacion = $ip_cliente;
            $compras->estado = 0;
            $compras->save();
            //Necesito llenar los datos de la factura pero al revès para que cumplan los datos y quiten las cuentas en el haber
            $id_empresa   = session("id_empresa");
            $cabecera  = AsientosCabecera::where('id', $compras->id_asiento_cabecera)->first();
            $cabecera->estado          = 1;
            $cabecera->id_usuariomod   = $idusuario;
            $cabecera->ip_creacion     = $ip_cliente;
            $cabecera->ip_modificacion = $ip_cliente;
            $cabecera->save();
            $detalles   = $cabecera->detalles;
            $id_asiento = AsientosCabecera::insertGetId([
                'observacion'     => strtoupper($r['observacion']),
                'fecha_asiento'   => $cabecera->fecha_asiento,
                'id_empresa'      => $id_empresa,
                'fact_numero'     => $cabecera->secuencia,
                'valor'           => $cabecera->valor,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'sucursal'        => $cabecera->sucursal,
            ]);
            foreach ($detalles as $value) {
                AsientosDetalles::insert([
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
            LogContable::insert([
                'tipo'           => 'C',
                'valor_ant'      => $cabecera->valor,
                'valor'          => $cabecera->valor,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod'  => $idusuario,
                'observacion'    => $cabecera->concepto,
                'id_ant'         => $cabecera->id,
                'id_referencia'  => $id_asiento,
            ]);
            DB::commit();
            return ['status' => 'success', 'msj' => "Anulado Correctamente"];
        } catch (\Exception $ex) {
            DB::rollback();
            return ['status' => false, 'msj' => $ex->getMessage(), 'line' => $ex->getLine()];
        }
    }
    public static function getOnlyOneRow($r)
    {
        return FacturaActivoCabecera::where('id_empresa', session("id_empresa"))
            ->leftjoin("proveedor", "af_factura_activo_cab.proveedor", "proveedor.id")
            ->leftJoin('af_activo_archivos', function ($join) {
                $join->on('af_factura_activo_cab.id', '=', 'af_activo_archivos.id_factura')
                    ->where('af_activo_archivos.estado', '=', 1);
            })
            ->join("users", "af_factura_activo_cab.id_usuariocrea", "users.id")
            ->orderBy('af_factura_activo_cab.id', 'DESC')
            ->where('af_factura_activo_cab.estado', '!=', '3')
            ->where("af_factura_activo_cab.id", $r["id"])
            ->select("af_activo_archivos.nombre_archivo", DB::raw('CONCAT(users.nombre1, " ", users.apellido1," ",users.apellido2) AS usuario_crea'), DB::raw('CONCAT(af_factura_activo_cab.serie, "-", af_factura_activo_cab.secuencia) AS secuencia_c'), "af_factura_activo_cab.*", "proveedor.razonsocial")
            ->first();
    }
    public static function getActivos()
    {
        return FacturaActivoCabecera::where('id_empresa', session("id_empresa"))
            ->leftjoin("proveedor", "af_factura_activo_cab.proveedor", "proveedor.id")
            ->leftJoin('af_activo_archivos', function ($join) {
                $join->on('af_factura_activo_cab.id', '=', 'af_activo_archivos.id_factura')
                    ->where('af_activo_archivos.estado', '=', 1);
            })
            ->join("users", "af_factura_activo_cab.id_usuariocrea", "users.id")
            ->orderBy('af_factura_activo_cab.id', 'DESC')
            ->where('af_factura_activo_cab.estado', '!=', '3')
            ->select(
                "af_activo_archivos.nombre_archivo",
                DB::raw('CONCAT(users.nombre1, " ", users.apellido1," ",users.apellido2) AS usuario_crea'),
                DB::raw('CONCAT(af_factura_activo_cab.serie, "-", af_factura_activo_cab.secuencia) AS secuencia_c'),
                "af_factura_activo_cab.*",
                "proveedor.razonsocial"
            )
            ->get();
    }
    public static function obtenerDatosFacturasActivoFijo($r)
    {
        $ordenes = LogOrdenes::where('log_ordenes.aprobado', '1')->where('log_ordenes.estado', '1')->where('log_ordenes.tipo', '1')
            ->join('af_factura_activo_cab as afc', 'afc.id', 'log_ordenes.id_orden')
            ->leftJoin('proveedor as prov', 'prov.id', 'afc.proveedor')
            ->where('afc.id_empresa', session("id_empresa"))
            ->select(
                'afc.*',
                'prov.razonsocial',
                DB::raw('CONCAT(afc.fecha_compra, "-", prov.razonsocial, "-", afc.observacion) as concatenar')
            )->having(DB::raw('CONCAT(afc.fecha_compra, "-", prov.razonsocial, "-", afc.observacion)'), 'LIKE', '%' . $r->term . '%')->get();

        $arreglos = [];
        foreach ($ordenes as $value) {
            $arreglos[] = ['id' => $value->id, 'data' => $value->concatenar];
        }
        return response()->json($arreglos);
    }
    public static function documentoFacturaBuscarOrden($r)
    {
        $id_orden = $r->orden;
        $orden_cab = AFacturaActivoCabecera::find($id_orden);
        $cab = [
            'id_proveedor'              => $orden_cab->proveedor,
            'nombre_proveedor'          => isset($orden_cab->proveedor) ? $orden_cab->datosproveedor->nombrecomercial : '',
            'serie'                     => $orden_cab->serie,
            'secuencia'                 => $orden_cab->secuencia,
            'observacion'               => $orden_cab->observacion,
            'fecha_compra'              => $orden_cab->fecha_compra,
            'divisas'                   => $orden_cab->divisas,
            'subtotal0'                 => $orden_cab->subtotal0,
            'subtotal12'                => $orden_cab->subtotal12,
            'subtotal'                  => $orden_cab->subtotal,
            'descuento'                 => $orden_cab->descuento,
            'impuesto'                  => $orden_cab->impuesto,
            'total'                     => $orden_cab->total,
        ];

        $cab["detalles"] = [];
        foreach ($orden_cab->detalles as $value) {
            $details = [
                "codigo"                => $value->codigo,
                "nombre"                => $value->nombre,
                "cantidad"              => $value->cantidad,
                "precio"                => $value->costo,
                "porct_descuento"       => $value->porc_descuento,
                "descuento"             => $value->descuento,
                "precio_neto"           => $value->subtotal,
                "iva"                   => $value->iva,
                "val_iva"               => $value->valor_iva,
                "total_valor"           => $value->total,
            ];


            if (!is_null($value->activo_id)) {
                $af_activo = AfActivo::find($value->activo_id);
                $details["activo_fijo"] = $af_activo['attributes'];
                $details["activo_fijo"]["accesorios"] = [];

                $accesorios = AfActivo_Accesorios::where('id_activo', $af_activo->id)->get();
                if (count($accesorios) > 0) {
                    foreach ($accesorios as $acce) {
                        //    dd($acce);
                        $data_acce = [
                            "nombre"        => $acce->nombre,
                            "marca"         => $acce->marca,
                            "modelo"        => $acce->modelo,
                            "serie"         => $acce->serie,
                        ];
                        array_push($details["activo_fijo"]["accesorios"], $data_acce);
                    }
                }
            }
            array_push($cab["detalles"], $details);
        }
        return ["cab" => $cab];
    }
    public static function documentofactura_saveSerie($r)
    {
        try {

            $af_serie = AF_BodegaSerieColor::where('nombre', 'like', '%' . $r['mdserie' . $r->id] . '%')->where('tipo', '2')->first();
            if (is_null($af_serie)) {
                $arr_serie = [
                    'nombre'            => $r['mdserie' . $r->id],
                    'tipo'              => 2,
                    'id_usuariocrea'    => Auth::user()->id,
                    'id_usuariomod'     => Auth::user()->id,
                    'ip_creacion'       => $_SERVER["REMOTE_ADDR"],
                    'ip_modificacion'   => $_SERVER["REMOTE_ADDR"],
                ];

                AF_BodegaSerieColor::insert($arr_serie);
            } else {
                return ['status' => true, 'msj' => 'Serie ya se encuentra guardada', "icon" => "warning"];
            }
            return ['status' => true, 'msj' => 'Guardado Correctamente', "icon" => "success"];
        } catch (Exception $ex) {
            return ['status' => false, 'msj' => $ex->getMessage(), 'line' => $ex->getLine(), "icon" => "error"];
        }
    }
    public static function guardar_color($r)
    {
        try {
            $af_color = AF_BodegaSerieColor::where('nombre', 'like', '%' . $r['mdcolor' . $r->id] . '%')->where('tipo', '1')->first();

            if (is_null($af_color)) {
                $arr_color = [
                    'nombre'            => strtoupper($r['mdcolor' . $r->id]),
                    'tipo'              => 1,
                    'id_usuariocrea'    => Auth::user()->id,
                    'id_usuariomod'     => Auth::user()->id,
                    'ip_creacion'       => $_SERVER["REMOTE_ADDR"],
                    'ip_modificacion'   => $_SERVER["REMOTE_ADDR"],
                ];
                AF_BodegaSerieColor::insert($arr_color);
            } else {
                return ['status' => true, 'msj' => 'Color ya se encuentra guardado', "icon" => "warning"];
            }
            return ['status' => true, 'msj' => 'Guardado Correctamente', "icon" => "success"];
        } catch (Exception $ex) {
            return ['status' => false, 'msj' => $ex->getMessage(), 'line' => $ex->getLine(), "icon" => "error"];
        }
    }
    public static function buscarCategoria($t)
    {
        $array = [];
        $arrayCaterogia = AF_Subtipo::where('nombre', 'like', '%' . $t->term . '%')->get();
        foreach ($arrayCaterogia as $value) {
            $array[] = ['id' => $value->id, 'data' => $value->nombre];
        }
        return response()->json($array);
    }
    public static function guardar_responsable($r)
    {
        try {
            $af_responsable = AF_BodegaSerieColor::where('nombre', 'like', '%' . $r['mdresponsable' . $r->id] . '%')->where('tipo', '3')->first();
            if (is_null($af_responsable)) {
                $arr_resp = [
                    'nombre'            => strtoupper($r['mdresponsable' . $r->id]),
                    'tipo'              => 3,
                    'id_usuariocrea'    => Auth::user()->id,
                    'id_usuariomod'     => Auth::user()->id,
                    'ip_creacion'       => $_SERVER["REMOTE_ADDR"],
                    'ip_modificacion'   => $_SERVER["REMOTE_ADDR"],
                ];
                AF_BodegaSerieColor::insert($arr_resp);
            } else {
                return ['status' => true, 'msj' => 'El responsable ya se encuentra guardado', "icon" => "warning"];
            }
            return ['status' => true, 'msj' => 'Guardado Correctamente', "icon" => "success"];
        } catch (Exception $ex) {
            return ['status' => false, 'msj' => $ex->getMessage(), 'line' => $ex->getLine(), "icon" => "error"];
        }
    }
    public static function codigo_secuencia($id_tipo)
    {
        $idusuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_empresa = session("id_empresa");

        $cod_secuencia = AF_CodigoSecuencia::where('id_empresa', $id_empresa)->where('id_tipo', $id_tipo)->first();

        if (is_null($cod_secuencia)) {
            AF_CodigoSecuencia::create([
                'id_tipo'              => $id_tipo,
                'secuencia'         => '000001',
                'id_empresa'           => $id_empresa,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ]);
            return '000001';
        } else {
            $codigo_activo = intval($cod_secuencia->secuencia);
            $codigo_activo++;
            $codigo_activo = str_pad($codigo_activo, 6, "0", STR_PAD_LEFT);
            $cod_secuencia->secuencia = $codigo_activo;
            $cod_secuencia->id_usuariomod = $idusuario;
            $cod_secuencia->save();
            return $codigo_activo;
        }
    }
    public static function buscar_codigo($r)
    {
        $empresa = Empresas::find(session("id_empresa"));
        $tipo = AF_Tipo::find($r->id_tipo);
        $codigo_activo = self::codigo_secuencia($tipo->id);

        $arr_sec = [
            'codigo' => 'AF-' . $empresa->abreviatura_emp . '-' . $tipo->abreviatura,
            'secuencia' => $codigo_activo
        ];

        return $arr_sec;
    }
    public static function buscar_Categoria($r)
    {
        $tipo = $r->opcion;
        if (!is_null($tipo)) {
            $categorias = AF_Subtipo::where('tipo_id', $tipo)->get();
            return $categorias;
        }

        return "no";
    }
    public static function guardar_marca($r)
    {

        try {

            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $af_marca = Marca::where('nombre', 'like', '%' . $r['mdmarca' . $r->id] . '%')->where('estado', '1')->first();
            if (is_null($af_marca)) {
                $arr_marca = [
                    'nombre'            => strtoupper($r['mdmarca' . $r->id]),
                    'descripcion'       => strtoupper($r['mdmarca' . $r->id]),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ];
                Marca::create($arr_marca);
            } else {
                return ['status' => true, 'msj' => 'La marca ya se encuentra guardado', "icon" => "warning"];
            }
            return ['status' => true, 'msj' => 'Guardado Correctamente', "icon" => "success"];
        } catch (Exception $ex) {
            return ['status' => false, 'msj' => $ex->getMessage(), 'line' => $ex->getLine(), "icon" => "error"];
        }
    }
    public static function saveFacturaActivos($r)
    {

        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            $id_activos = [];
            $sucursal = Sucursales::where('id', $data['sucursal_empresa'])->first();
            $punto_emision = Caja::where('id', $data['punto_emision'])->first();
            $cod_sucurs = Sucursales::where('estado', '1')->where('id', $r['sucursal_empresa'])->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;
            $cod_caj    = Caja::where('estado', '1')->where('id', intval($r['punto_emision']))->first();
            $c_caja     = $cod_caj->codigo_caja;
            $arrayAsientoCabecera = [
                'sucursal' => $sucursal->codigo_sucursal,
                'punto_emision' => $punto_emision->codigo_caja,
                'fecha_asiento' => $r->fecha_asiento,
                'fact_numero' => $r->serie_factura . $r->secuencia, //??
                'id_empresa' => session('id_empresa'),
                'observacion' => $r->concepto,
                'valor' => $r->total1,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ];

            $id_asiento_cabecera = Contabilidad::crearAsiento('activoFijo', 'compra', $arrayAsientoCabecera,  $r);
            if ($id_asiento_cabecera['status'] == 'error') {
                throw new Exception($id_asiento_cabecera['message']);
            }
            $maxId = DB::table('af_factura_activo_cab')->max('id');
            $numero                   = $maxId + 1;
            $input               = [
                'numero'           => str_pad($numero, 10, "0", STR_PAD_LEFT),
                'proveedor'        => $r['id_proveedor'],
                'tipo'             => $r['tipo_transaccion'],
                'id_asiento'       => $id_asiento_cabecera,
                'fecha_asiento'    => $r['fecha_asiento'],
                'credito_tributario' => $r['credito_tributario'],
                'fecha_caduca'     => $r['fecha_caduca'],
                'divisas'          => $r['divisas'],
                'termino'          => $r['termino'],
                'ord_compra'       => $r['ord_compra'],
                'nro_autorizacion' => $r['nro_autorizacion'],
                'fecha_compra'     => $r['fecha_compra'],
                'serie'            => $r['serie_factura'],
                'secuencia'        => $r['secuencia'],
                'tipo_comprobante' => $r['tipo_comprobante'],
                'subtotal'         => $r['base1'],
                'subtotal0'        => $r['subtotal_01'],
                'subtotal12'       => $r['subtotal_121'],
                'descuento'        => $r['descuento1'],
                'impuesto'         => $r['tarifa_iva1'],
                'total'            => $r['total1'],
                'estado'           => 1,
                'id_empresa'       => session("id_empresa"),
                'id_usuariocrea'   => Auth::user()->id,
                'id_usuariomod'    => Auth::user()->id,
                'ip_creacion'      => $ip,
                'ip_modificacion'  => $ip,
                'sucursal'         => $c_sucursal,
                'punto_emision'    => $c_caja,
                'observacion'      => $r['concepto'],
            ];
            $numero_factura = "{$r['serie_factura']}-{$r['secuencia']}";
            $r['factura_id'] = AFFacturaActivoCabecera::insertGetId($input);
            $base = $r['subtotal_01'];
            $base12 = $r['subtotal_121'];
            $input2 = [
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'fecha'                         => $r['fecha_compra'],
                'numero'                        => $numero_factura,
                'archivo_sri'                   => '1',
                'proveedor'                     => $r['id_proveedor'],
                'termino'                       => $r['termino'],
                'secuencia_f'                   => $numero_factura,
                'observacion'                   => $r['concepto'],
                'tipo'                          => '2',
                'sucursal'                      => $c_sucursal,
                'punto_emision'                 => $c_caja,
                'valor_contable'                => $r['total1'],
                'fecha_termino'                 => $r['fecha_caduca'],
                'orden_compra'                  => $r['ord_compra'],
                'f_caducidad'                   => $r['fecha_caduca'],
                'tipo_gasto'                    => $r['factura_id'],
                'autorizacion'                  => $r['nro_autorizacion'],
                'f_autorizacion'                => $r['fecha_compra'],
                'id_empresa'                    => session("id_empresa"),
                'serie'                         => $r['serie_factura'],
                'secuencia_factura'             => $r['secuencia'],
                'credito_tributario'            => $r['credito_tributario'],
                'tipo_comprobante'              => $r['tipo_comprobante'],
                'subtotal_0'                    => $base,
                'subtotal_12'                   => $base12,
                'subtotal'                      => $r['base1'],
                'descuento'                     => $r['descuento1'],
                'iva_total'                     => $r['tarifa_iva1'],
                'ice_total'                     => "0",
                'total_final'                   => $r['total1'],
                'ip_creacion'                   => $ip,
                'ip_modificacion'               => $ip,
                'id_usuariocrea'                => Auth::user()->id,
                'id_usuariomod'                 => Auth::user()->id,
            ];

            $r['idCompra'] = Compras::insertGetId($input2);

            $q = 0;
            for ($i = 0; $i < count($r['codigo']); $i++) {

                if (isset($r["modulo"]) or $r["modulo"] == "importacion") {
                    if ($r["check_af"][$i] == 1) {

                        $a = $r["mdtiposid"][$q];

                        if (isset($r["pre_orden"]) or $r["pre_orden"] == "1") {
                            $estado = 3;
                        } else {
                            $estado = 1;
                        }
                        $tipo = AF_Tipo::find($r["mdtipo{$a}"]);
                        $input = [
                            'codigo'          => $r["mdcodigo{$a}"] . '-' . $r["mdcodigo_num{$a}"],
                            'nombre'          => strtoupper($r["mdnombre{$a}"]),
                            'descripcion'     => strtoupper($r["mddescripcion{$a}"]),
                            'tipo_id'         => $r["mdtipo{$a}"],
                            'subtipo_id'      => $r["mdgrupo{$a}"],
                            'responsable'     => strtoupper($r["mdresponsable{$a}"]),
                            'acreedor'        => strtoupper($r['id_proveedor']),
                            'marca'           => strtoupper($r["mdmarca{$a}"]),
                            'color'           => strtoupper($r["mdcolor{$a}"]),
                            'modelo'          => strtoupper($r["mdmodelo{$a}"]),
                            'serie'           => strtoupper($r["mdserie{$a}"]),
                            'procedencia'     => strtoupper($r["mdprocedencia{$a}"]),
                            'costo'           => $r["precioneto"][$i],
                            'fecha_compra'    => $r['fecha_compra'],
                            'factura'         => $r['serie_factura'] . '-' . $r['secuencia'],
                            'estado'          => $estado,
                            'estado_activo'   => 1,
                            'empresa'         => session("id_empresa"),
                            'id_usuariocrea'  => Auth::user()->id,
                            'id_usuariomod'   => Auth::user()->id,
                            'ip_creacion'     => $ip,
                            'ip_modificacion' => $ip,
                            'tasa'            => $tipo->tasa,
                            'tipo_tasa'       => $tipo->tipo_tasa,
                            'vida_util'       => $tipo->vidautil,
                            'ubicacion'       => strtoupper($r["mdubicacion{$a}"]),
                            'codigo_text'     => $r["mdcodigo{$a}"],
                            'codigo_num'      => $r["mdcodigo_num{$a}"],
                            'foto_activo'     => $r["fotoactivo{$a}"],
                            'foto_acc'        => $r["fotoacc{$a}"],
                        ];
                        $activo_id = AfActivo::insertGetId($input);

                        if (!$activo_id["respuesta"]) {
                            throw new Exception($activo_id["msj"]);
                        }
                        array_push($id_activos, $activo_id);
                        $accesorios = self::afAccesorios($r, $activo_id, $a);
                        if (!$accesorios["respuesta"]) {
                            throw new Exception($accesorios["msj"]);
                        }
                        $q++;
                    }
                } else {
                    $a = $r["mdtiposid"][$i];
                    if (isset($r["pre_orden"]) or $r["pre_orden"] == "1") {
                        $estado = 3;
                    } else {
                        $estado = 1;
                    }
                    $tipo = AF_Tipo::find($r["mdtipo{$a}"]);
                    if (!is_null($tipo)) {
                        $activo_id = self::guardarCabAf($r, $a, $i, $estado, $tipo);
                        if (!$activo_id["respuesta"]) {
                        }

                        array_push($id_activos, $activo_id["id"]);
                        $accesorios = self::afAccesorios($r, $activo_id, $a);
                        if ($accesorios["respuesta"]) {
                            throw new Exception($accesorios["msj"]);
                        }
                    }
                }
            }
            if ($r['modulo'] != 'importacion' or is_null($r['modulo'])) {
                for ($x = 0; $x < count($id_activos); $x++) {
                    AfFacturaActivoDetalle::insert([
                        'fact_activo_id'  => $r['factura_id'],
                        'activo_id'       => $id_activos[$x],
                        'cantidad'        => $r['cantidad'][$x],
                        'codigo'          => $r['codigo'][$x],
                        'nombre'          => strtoupper($r['descrip_prod'][$x]),
                        'costo'           => $r['precio'][$x],
                        'subtotal'        => ($r['precio'][$x] * $r['cantidad'][$x]) - $r['desc'][$x],
                        'descuento'       => $r['desc'][$x],
                        'porc_descuento'  => $r['descpor'][$x],
                        'total'           => $r['total_valor'][$x],
                        'estado'          => 1,
                        'iva'             => $r['check_iva'][$x],
                        'valor_iva'       => $r['val_iva'][$x],
                        'id_usuariocrea'  => Auth::user()->id,
                        'id_usuariomod'   => Auth::user()->id,
                        'ip_creacion'     => Utilidades::getRealIP(),
                        'ip_modificacion' => Utilidades::getRealIP(),
                        'observacion'     => $r['observacion'][$x],
                    ]);
                }
                if (floatval($r->codigo_trans) > 0) {
                    for ($y = 0; $y < count($r->codigo_trans); $y++) {
                        $detalle_trans = [
                            'fact_activo_id'       => $r['factura_id'],
                            'codigo'               => $r['codigo_trans'][$y],
                            'nombre'               => strtoupper($r['descrip_prod_trans'][$y]),
                            'subtotal'             => ($r['precio_trans'][$y] * $r['cantidad_trans'][$y]) - $r['desc_trans'][$y],
                            'cantidad'             => $r['cantidad_trans'][$y],
                            'costo'                => $r['precio_trans'][$y],
                            'porc_descuento'       => $r['descpor_trans'][$y],
                            'estado'               => '1',
                            'iva'                  => $r["val_iva_trans"][$y] > 0 ? 1 : 0,
                            'valor_iva'            => $r['val_iva_trans'][$y],
                            'descuento'            => $r['desc_trans'][$y],
                            'total'                => $r['total_valor_trans'][$y],
                            'ip_creacion'          => Utilidades::getRealIP(),
                            'ip_modificacion'      => Utilidades::getRealIP(),
                            'id_usuariocrea'       => Auth::user()->id,
                            'id_usuariomod'        => Auth::user()->id,
                            'observacion'          => $r['observacion_trans'][$y],
                        ];
                        AfFacturaActivoDetalle::insert($detalle_trans);
                    }
                }
                if (floatval($r['id_plan']) > 0) {
                    for ($z = 0; $z < count($r->id_plan); $z++) {
                        $plan_gasto = PlanCuentas::where('id', $r['id_plan'][$z])->first();
                        $plan_gasto = Plan_Cuentas::find(is_null($plan_gasto->id_plan) ? $plan_gasto->plan : $plan_gasto->id_plan);
                        $detalle_gasto = [
                            'fact_activo_id'       => $r['factura_id'],
                            'codigo'               => $r['id_plan'][$z],
                            'nombre'               => $plan_gasto->nombre,
                            'subtotal'             => ($r['precio_gasto'][$z] * $r['cantidad_gasto'][$z]) - $r['desc_gasto'][$z],
                            'cantidad'             => $r['cantidad_gasto'][$z],
                            'costo'                => $r['precio_gasto'][$z],
                            'porc_descuento'       => $r['descpor_gasto'][$z],
                            'estado'               => '1',
                            'iva'                  => $r['val_iva_gasto'][$z] > 0 ? 1 : 0,
                            'valor_iva'            => $r['val_iva_gasto'][$z],
                            'descuento'            => $r['desc_gasto'][$z],
                            'total'                => $r['total_valor_gasto'][$z],
                            'ip_creacion'          => Utilidades::getRealIP(),
                            'ip_modificacion'      => Utilidades::getRealIP(),
                            'id_usuariocrea'       => Auth::user()->id,
                            'id_usuariomod'        => Auth::user()->id,
                            'observacion'          => $r['observacion_gasto'][$z],
                        ];
                        AfFacturaActivoDetalle::insert($detalle_gasto);
                    }
                }
            }

            $r->c = 'Compras';
            $r->s = 'SaveIngresoActivo';
            $r->d = $origin['d'];
            $r->m = Compras::$modelo;
            $r->o = 'Se creo la compra con id.: ' . $r['idCompra'];
            $cont++;

            Auditorias::saveAuditoria($r, DB::getQueryLog());
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
            $r->c = 'Compras';
            $r->s = 'SaveIngresoActivo';
            $r->d = $origin['d'];
            $r->m = Compras::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => 'v3/compras/addActivos',
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function guardarCabAf($r, $a, $i, $estado, $tipo)
    {
        $ip = Utilidades::getRealIP();
        try {
            $input = [
                'codigo'          => $r["mdcodigo{$a}"] . '-' . $r["mdcodigo_num{$a}"],
                'nombre'          => strtoupper($r["mdnombre{$a}"]),
                'descripcion'     => strtoupper($r["mddescripcion{$a}"]),
                'tipo_id'         => $r["mdtipo{$a}"],
                'subtipo_id'      => $r["mdgrupo{$a}"],
                'responsable'     => strtoupper($r["mdresponsable{$a}"]),
                'acreedor'        => strtoupper($r['id_proveedor']),
                'marca'           => strtoupper($r["mdmarca{$a}"]),
                'color'           => strtoupper($r["mdcolor{$a}"]),
                'modelo'          => strtoupper($r["mdmodelo{$a}"]),
                'serie'           => strtoupper($r["mdserie{$a}"]),
                'procedencia'     => strtoupper($r["mdprocedencia{$a}"]),
                'costo'           => $r["precioneto"][$i],
                'fecha_compra'    => $r['fecha_compra'],
                'factura'         => $r['serie_factura'] . '-' . $r['secuencia'],
                'estado'          => $estado,
                'estado_activo'   => 1,
                'empresa'         => session("id_empresa"),
                'id_usuariocrea'  => Auth::user()->id,
                'id_usuariomod'   => Auth::user()->id,
                'ip_creacion'     => $ip,
                'ip_modificacion' => $ip,
                'tasa'            => $tipo->tasa,
                'tipo_tasa'       => $tipo->tipo_tasa,
                'vida_util'       => $tipo->vidautil,
                'ubicacion'       => strtoupper($r["mdubicacion{$a}"]),
                'codigo_text'     => $r["mdcodigo{$a}"],
                'codigo_num'      => $r["mdcodigo_num{$a}"],
                'foto_activo'     => $r["fotoactivo{$a}"],
                'foto_acc'        => $r["fotoacc{$a}"],
            ];
            $activo_id = AfActivo::insertGetId($input);
            DB::commit();
            return ['respuesta' => true, 'msj' => 'Guardado con Exito', 'id' => $activo_id];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => false, 'msj' => $e->getMessage(), "mod" => "afAccesorios"];
        }
    }
    public static function afAccesorios($r, $id_activo, $a)
    {
        try {
            if (!is_null($r["accesorio{$a}"])) {
                for ($x = 0; $x < count($r["accesorio{$a}"]); $x++) {
                    AfActivo_Accesorios::insert([
                        'id_activo'            => $id_activo,
                        'nombre'               => $r["nombre_ac{$a}"][$x],
                        'marca'                => $r["marca_ac{$a}"][$x],
                        'modelo'               => $r["modelo_ac{$a}"][$x],
                        'serie'                => $r["serie_ac{$a}"][$x],
                        'ip_creacion'          => Utilidades::getRealIP(),
                        'ip_modificacion'      => Utilidades::getRealIP(),
                        'id_usuariocrea'       => Auth::user()->id,
                        'id_usuariomod'        => Auth::user()->id,
                    ]);
                }
            }
            DB::commit();
            return ['respuesta' => true, 'msj' => 'Guardado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => false, 'msj' => $e->getMessage(), "mod" => "afAccesorios"];
        }
    }
    public static function getOnlyItemActive($r)
    {
        $query = AfActivo::find($r['id']);
        $detalles = AfActivo_Accesorios::where('id_activo', $r['id'])->get();
        $subtipo = '';
        $tipo = '';
        if (isset($query->sub_tipo)) {
            $subtipo = $query->sub_tipo->nombre;
        }
        if (isset($query->tipo)) {
            $tipo = $query->tipo->nombre;
        }
        return ['res' => $query, 'subtipo' => $subtipo, 'tipo' => $tipo, 'detalles' => $detalles];
    }
    public static function verificarActivo($r)
    {
        try {
            $cabecera = AFFacturaActivoCabecera::find($r->id);
            $compra = Compras::where('id_asiento_cabecera', $cabecera->id_asiento)->first();
            $tabla_egre        = "";
            $tabla_banca       = "";
            $tabla_cruce       = "";
            $tabla_rete        = "";
            $tabla_egre_masivo = "";
            $id_egreso         = 0;
            $id_deb_ban        = 0;
            $id_cruce_valores  = 0;
            $id_retencion      = 0;
            $id_egreso_masivo  = 0;
            $id    = $compra->id;
            $tiene = "";
            $retenciones = retenciones::where('id_compra', $id)->get();
            if (count($retenciones) > 0) {
                foreach ($retenciones as $value) {
                    if ($value->estado == '1') {
                        $tiene        = "si";
                        $id_retencion = $retenciones[0]->id;
                    }
                }
                if ($tiene == "si") {
                    $tabla_rete = "Retenciones ";
                }
            }
            $com_egre = DetalleComprobanteEgreso::where('id_compra', $id)->get();
            if (count($com_egre) > 0) {
                foreach ($com_egre as $value) {
                    $egreso = ComprobanteEgreso::where('id', $value->id_comprobante)->where('estado', '=', '1')->get();
                    if (count($egreso) > 0) {
                        $tiene      = "si";
                        $tabla_egre = "Comprobante de Egreso ";
                        $id_egreso  = $egreso[0]->id;
                    }
                }
            }
            $debito_banca_det = DebitoBancarioDetalle::where('id_compra', $id)->get();
            if (count($debito_banca_det) > 0) {
                foreach ($debito_banca_det as $value) {
                    $debit_banca = DB::table('ct_debito_bancario')->where('id', $value->id_debito)->where('estado', '=', '1')->get();
                    if (count($debit_banca) > 0) {
                        $tiene       = "si";
                        $tabla_banca = "Debito Bancario ";
                        $id_deb_ban  = $debit_banca[0]->id;
                    }
                }
            }
            $detalle_cruce = DB::table('ct_detalle_cruce')->where('id_factura', $id)->get();
            if (count($detalle_cruce) > 0) {
                foreach ($detalle_cruce as $value) {
                    $cruce_valores = CruceValores::where('id', $value->id_comprobante)->where('estado', '1')->get();
                    if (count($cruce_valores) > 0) {
                        $tiene            = "si";
                        $tabla_cruce      = "Cruce Valores ";
                        $id_cruce_valores = $cruce_valores[0]->id;
                    }
                }
            }
            $det_egreso_masivo = DetalleComprobanteEgresoMasivo::where('id_compra', $id)->get();
            if (count($det_egreso_masivo) > 0) {
                foreach ($det_egreso_masivo as $value) {
                    $egreso_masivo = ComprobanteEgresoMasivo::where('id', $value->id_comprobante)->where('estado', '=', '1')->get();
                    if (count($egreso_masivo) > 0) {
                        $tiene             = "si";
                        $tabla_egre_masivo = "Comprobante de Egreso Masivo";
                        $id_egreso_masivo  = $egreso_masivo[0]->id;
                    }
                }
            }
            $tablas = [$tabla_egre, $tabla_banca, $tabla_cruce, $tabla_rete, $tabla_egre_masivo];
            $ids    = [$id_egreso, $id_deb_ban, $id_cruce_valores, $id_retencion, $id_egreso_masivo];

            return ['status' => true, 'id' => $id, 'tablas' => $tablas, 'respuesta' => $tiene, "ids" => $ids];
        } catch (Exception $e) {
            return ['status' => false, 'msj' => $e->getMessage()];
        }
    }
    public static function anularActivo($r)
    {

        try {
            $cabecera = AfFacturaActivoCabecera::find($r->id);
            $detalle  = AfFacturaActivoDetalle::where('fact_activo_id', $r->id)->get();
            $compras = Compras::where('id_asiento_cabecera', $cabecera->id_asiento)->first();

            if ($compras->estado == 0) {
                $cabecera->estado = $r->estado;
                foreach ($detalle as $value) {
                    if (!is_null($value->activo_id)) {
                        $activo = AfActivo::find($value->activo_id);
                        $activo->estado = 0;
                        $activo->save();
                    }
                }
            } else {
                if (!is_null($compras)) {
                    $compras->estado = 0;
                    $compras->save();
                }
                $cabecera->estado = 0;

                foreach ($detalle as $value) {
                    if (!is_null($value->activo_id)) {
                        $activo = AfActivo::find($value->activo_id);
                        $activo->estado = 0;
                        $activo->save();
                    }
                }
                $cabecera->save();
                self::anular_asiento($cabecera->id_asiento);
                return ['status' => true, 'msj' => ""];
            }
        } catch (Exception $e) {
            return ['status' => false, 'msj' => $e->getMessage()];
        }
    }
    public static function anular_asiento($id)
    {
        $asientoc = AsientosCabecera::where('id', $id)->first();

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $cabecera   = [
            'sucursal'        => $asientoc->sucursal,
            'punto_emision'   => $asientoc->punto_emision,
            'observacion'     => 'ANULACION / REVERSO DE ACTIVO FIJO POR FACTURA: ' . $asientoc->fact_numero,
            'fecha_asiento'   => $asientoc->fecha_asiento,
            'fact_numero'     => $asientoc->fact_numero,
            'valor'           => $asientoc->valor,
            'estado'          => '1',
            'id_empresa'      => $asientoc->id_empresa,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $id_asiento_cabecera = AsientosCabecera::insertGetId($cabecera);

        $detalle = AsientosDetalles::where('id_asiento_cabecera', $id)->orderBy('id', 'asc')->get();
        if ($detalle != null) {
            foreach ($detalle as $value) {

                AsientosDetalles::insert([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $value->id_plan_cuenta,
                    'descripcion'         => $value->descripcion,
                    'fecha'               => $value->fecha,
                    'debe'                => $value->haber,
                    'haber'               => $value->debe,
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $idusuario,
                    'ip_modificacion'     => $idusuario,
                ]);
            }
            LogAsiento::anulacion("AF", $id_asiento_cabecera, $asientoc->id);
        }
    }
    public static function traerDatosProveedor($r)
    {
        try {
            $id_proveedor = $r['id'];
            $detalle_acreedor   = DetalleAcreedores::where('id_proveedor', $id_proveedor)->orderBy('f_caducidad', 'desc')->first();
            if (!is_null($detalle_acreedor)) {
                $serie         = $detalle_acreedor->serie;
                $autorizacion  = $detalle_acreedor->autorizacion;
                $f_caduca      = $detalle_acreedor->f_caducidad;

                return ['status' => true, 'serie' => $serie, 'autorizacion' => $autorizacion, 'f_caduca' => $f_caduca];
            }
            return ['status' => true, 'serie' => "", 'autorizacion' => "", 'f_caduca' => ""];
        } catch (Exception $e) {

            return ['status' => false, 'msj' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }
    public static function store_detalles($r, $activo_id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        DB::beginTransaction();
        try {
            for ($x = 0; $x < count($activo_id); $x++) {
                AfFacturaActivoDetalle::insert([
                    'fact_activo_id'  => $r['factura_id'],
                    'activo_id'       => $activo_id[$x],
                    'cantidad'        => $r['cantidad'][$x],
                    'codigo'          => $r['codigo'][$x],
                    'nombre'          => strtoupper($r['descrip_prod'][$x]),
                    'costo'           => $r['precio'][$x],
                    'subtotal'        => ($r['precio'][$x] * $r['cantidad'][$x]) - $r['desc'][$x],
                    'descuento'       => $r['desc'][$x],
                    'porc_descuento'  => $r['descpor'][$x],
                    'total'           => $r['total_valor'][$x],
                    'estado'          => 1,
                    'iva'             => $r['check_iva'][$x],
                    'valor_iva'       => $r['val_iva'][$x],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'observacion'     => $r['observacion'][$x],
                ]);
            }

            if (floatval($r->codigo_trans) > 0) {
                for ($y = 0; $y < count($r->codigo_trans); $y++) {
                    $detalle_trans = [
                        'fact_activo_id'       => $r['factura_id'],
                        'codigo'               => $r['codigo_trans'][$y],
                        'nombre'               => strtoupper($r['descrip_prod_trans'][$y]),
                        'subtotal'             => ($r['precio_trans'][$y] * $r['cantidad_trans'][$y]) - $r['desc_trans'][$y],
                        'cantidad'             => $r['cantidad_trans'][$y],
                        'costo'                => $r['precio_trans'][$y],
                        'porc_descuento'       => $r['descpor_trans'][$y],
                        'estado'               => '1',
                        'iva'                  => $r["val_iva_trans"][$y] > 0 ? 1 : 0,
                        'valor_iva'            => $r['val_iva_trans'][$y],
                        'descuento'            => $r['desc_trans'][$y],
                        'total'                => $r['total_valor_trans'][$y],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'observacion'          => $r['observacion_trans'][$y],
                    ];
                    AfFacturaActivoDetalle::insert($detalle_trans);
                }
            }
            if (floatval($r['id_plan']) > 0) {
                for ($z = 0; $z < count($r->id_plan); $z++) {
                    $plan_gasto = PlanCuentas::where('id_plan', $r['id_plan'][$z])->orwhere('plan', $r['id_plan'][$z])->first();
                    $plan_gasto = Plan_Cuentas::find(is_null($plan_gasto->id_plan) ? $plan_gasto->plan : $plan_gasto->id_plan);
                    $detalle_gasto = [
                        'fact_activo_id'       => $r['factura_id'],
                        //'activo_id'            => $activo_id,
                        'codigo'               => $r['id_plan'][$z],
                        'nombre'               => $plan_gasto->nombre, // revisar
                        'subtotal'             => ($r['precio_gasto'][$z] * $r['cantidad_gasto'][$z]) - $r['desc_gasto'][$z],
                        'cantidad'             => $r['cantidad_gasto'][$z],
                        'costo'                => $r['precio_gasto'][$z],
                        'porc_descuento'       => $r['descpor_gasto'][$z],
                        'estado'               => '1',
                        'iva'                  => $r['val_iva_gasto'][$z] > 0 ? 1 : 0,
                        'valor_iva'            => $r['val_iva_gasto'][$z],
                        'descuento'            => $r['desc_gasto'][$z],
                        'total'                => $r['total_valor_gasto'][$z],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'observacion'          => $r['observacion_gasto'][$z],
                    ];
                    AfFacturaActivoDetalle::create($detalle_gasto);
                }
            }
            DB::commit();
            return ['respuesta' => true, 'msj' => 'Guardado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => false, 'msj' => $e->getMessage(), "mod" => "store_detalles"];
        }
    }
    public static function store_AfActivo($r)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = session('id_empresa');
        $id_activos = [];
        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($r['codigo']); $i++) {
                $a = $r["mdtiposid"][$i];
                if (isset($request["pre_orden"]) or $r["pre_orden"] == "1") {
                    $estado = 3;
                } else {
                    $estado = 1;
                }
                $tipo = AF_Tipo::find($r["mdtipo{$a}"]);
                if (!is_null($tipo)) {
                    $input = [
                        'codigo'          => $r["mdcodigo{$a}"] . '-' . $r["mdcodigo_num{$a}"],
                        'nombre'          => strtoupper($r["mdnombre{$a}"]),
                        'descripcion'     => strtoupper($r["mddescripcion{$a}"]),
                        'tipo_id'         => $r["mdtipo{$a}"],
                        'subtipo_id'      => $r["mdgrupo{$a}"],
                        'responsable'     => strtoupper($r["mdresponsable{$a}"]),
                        'acreedor'        => strtoupper($r['proveedor']),
                        'marca'           => strtoupper($r["mdmarca{$a}"]),
                        'color'           => strtoupper($r["mdcolor{$a}"]),
                        'modelo'          => strtoupper($r["mdmodelo{$a}"]),
                        'serie'           => strtoupper($r["mdserie{$a}"]),
                        'procedencia'     => strtoupper($r["mdprocedencia{$a}"]),
                        'costo'           => $r["precioneto"][$i],
                        'fecha_compra'    => $r['fecha_compra'],
                        'factura'         => $r['serie_factura'] . '-' . $r['secuencia'],
                        'estado'          => $estado,
                        'estado_activo'   => 1,
                        'empresa'         => $id_empresa,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'tasa'            => $tipo->tasa,
                        'tipo_tasa'       => $tipo->tipo_tasa,
                        'vida_util'       => $tipo->vidautil,
                        'ubicacion'       => strtoupper($r["mdubicacion{$a}"]),
                        'codigo_text'     => $r["mdcodigo{$a}"],
                        'codigo_num'      => $r["mdcodigo_num{$a}"],
                        'foto_activo'        => $r["fotoactivo{$a}"],
                        'foto_acc'           => $r["fotoacc{$a}"],
                    ];

                    $activo_id = AfActivo::insertGetId($input);
                    $accesorios = self::afAccesorios($r, $activo_id, $a);
                    array_push($id_activos, $activo_id);
                    if (!$accesorios["respuesta"]) {
                        throw new Exception($accesorios["respuesta"]);
                    }
                }
            }
            if ($r['modulo'] != 'importacion' or is_null($r['modulo'])) {
                $detalles = self::store_detalles($r, $id_activos);
                if (!$detalles["respuesta"]) {
                    throw new Exception($detalles["msj"]);
                }
            }
            DB::commit();
            return ['respuesta' => true, 'msj' => 'Guardado con Exito', 'id_af' => $activo_id, 'AfActivo' => $id_activos];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => false, 'msj' => $e->getMessage(), "mod" => "store_AfActivo"];
        }
    }
    public static function saveOrdenActivos($r)
    {

        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $dataDetalle = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            $idusuario  = Auth::user()->id;
            $base = $r['subtotal_01'];
            $base12 = $r['subtotal_121'];
            $id_empresa = session("id_empresa");
            $input               = [
                'proveedor'        => $r['id_proveedor'],
                'tipo'             => $r['tipo_transaccion'],
                'fecha_asiento'    => $r['fecha_compra'],
                'fecha_caduca'     => $r['fecha_compra'],
                'divisas'          => 1,
                'fecha_compra'     => $r['fecha_compra'],
                'serie'            => $r['serie_factura'],
                'secuencia'        => $r['secuencia'],
                'subtotal'         => $r['base1'],
                'subtotal0'        => $base,
                'subtotal12'       => $base12,
                'descuento'        => $r['descuento1'],
                'impuesto'         => $r['tarifa_iva1'],
                'total'            => $r['total1'],
                'estado'           => 3,
                'id_empresa'       => $id_empresa,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
                'ip_creacion'      => $ip,
                'ip_modificacion'  => $ip,
                'observacion'      => $r['concepto'],
            ];
            $r['factura_id'] = AfFacturaActivoCabecera::insertGetId($input);
            $arr_log = [
                'id_orden'       => $r['factura_id'],
                'tipo'           => 1,
                'id_empresa'     => $id_empresa,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod'  => $idusuario,
                'ip_creacion'      => $ip,
                'ip_modificacion'  => $ip,
            ];

            LogOrdenes::insert($arr_log);

            $retornar = self::store_AfActivo($r);
            if (!$retornar["respuesta"]) {
                throw new Exception($retornar["respuesta"]);
            }
            $r->c = 'Compras';
            $r->s = 'savePreFactura';
            $r->d = $origin['d'];
            $r->m = Compras::$modelo;
            $r->o = 'Se guardo la pre-factura con id.: ' . $r['factura_id'];
            $cont++;
            Auditorias::saveAuditoria($r, DB::getQueryLog());
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
            $r->c = 'Compras';
            $r->s = 'SavePreFactura';
            $r->d = $origin['d'];
            $r->m = Compras::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => 'v3/compras/addActivos',
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function getPreOrden($r)
    {
        if (isset($r->id)) {
            return  AFFacturaActivoCabecera::find($r->id);
        } else {
            return LogOrdenes::where('log_ordenes.estado', '1')->where('log_ordenes.tipo', '1')
                ->join('af_factura_activo_cab as afc', 'afc.id', 'log_ordenes.id_orden')
                ->join('users', 'log_ordenes.id_usuariocrea', 'users.id')
                ->leftJoin('proveedor as prov', 'prov.id', 'afc.proveedor')
                ->where('afc.id_empresa', session("id_empresa"))
                ->select(
                    'afc.*',
                    'log_ordenes.aprobado',
                    'prov.nombrecomercial',
                    'log_ordenes.estado as estadoL',
                    DB::raw('concat(users.nombre1," ",users.apellido1," ",users.apellido2) as usuario_crea')
                )->get();
        }
    }

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Empresas', 'id_empresa');
    }
    public function usuario()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Usuarios', 'id_usuariocrea');
    }
    public function proveedorf()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Proveedor', 'proveedor');
    }
    public function pasos()
    {
        return $this->belongsTo('Sis_medico\v3\contable\DePasos', 'doc_electronico');
    }
    public static function descargarComprobanteCompra($r)
    {

        $compra = self::find($r->id);
        $retencionAsiento = null;
        $retencion = null;
        $asientoRetencion = Retenciones::where("id_compra", $r->id)->where("estado", 1)->first();
        $detalle_comprobanteEg = Detalle_Comprobante_Egreso::where("id_compra", $r->id)->sum("abono");
        if ($asientoRetencion !== null) {
            $retencionAsiento = AsientosDetalles::join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                ->join('plan_cuentas_empresa as pe', function ($q) {
                    $q->on('p.id', 'pe.id_plan')->where('pe.id_empresa', session('id_empresa'));
                })
                ->where('id_asiento_cabecera', $asientoRetencion->id_asiento_cabecera)
                ->get([
                    'pe.plan',
                    'p.nombre',
                    'ct_asientos_detalle.debe',
                    'ct_asientos_detalle.haber'
                ]);
        }
        $sqlMovimiento = "
        select pe.plan,p.nombre,
            d.debe,
            d.haber
            from ct_ventas v 
        left join log_venta_costo k on v.id=k.id_venta
        join ct_asientos_cabecera c on k.id_asiento=c.id
        join ct_asientos_detalle d on c.id=d.id_asiento_cabecera
        JOIN plan_cuentas p ON d.id_plan_cuenta = p.id
        JOIN plan_cuentas_empresa pe ON p.id = pe.id_plan AND pe.id_empresa = '$compra->id_empresa'
        where v.id=$compra->id
        ";
        $queryAsientos = AsientosDetalles::join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
            ->join('plan_cuentas_empresa as pe', function ($q) {
                $q->on('p.id', 'pe.id_plan')->where('pe.id_empresa', session('id_empresa'));
            })
            ->where('id_asiento_cabecera', $compra->id_asiento_cabecera)
            ->get([
                'pe.plan',
                'p.nombre',
                'ct_asientos_detalle.debe',
                'ct_asientos_detalle.haber'
            ]);
        $queryMovimiento = DB::select(DB::raw($sqlMovimiento));
        $usuarioSession = Auth::user()->id;
        $subtotal5 = 0;
        $subtotal15 = 0;
        $iva15 = 0;
        $iva5 = 0;
        $subtotal = 0;
        $descuento = 0;
        $resTotal = 0;
        $totF = 0;
        $cont = 0;
        $vat =  DetalleCompras::where("id_ct_compras", $r->id)->groupBy("porcentaje")->get(["porcentaje"]);
        $taxRates = $vat->pluck('porcentaje', 'porcentaje')->toArray();
        //      array:1 [▼
        //   "0.15" => "0.15"
        //   ]
        foreach ($compra->detalles as $k => $val) {

            $porcentaje = $val->porcentaje * 100;
            $veri = ($val->porcentaje * 100) / 100;
            

            if (isset($taxRates[(string)$veri])) {
                if ($val->iva == 1) {
                    if ($veri == 0.15) {
                        $subtotal15 += $val->precio * $val->cantidad;
                        $iva15 += $val->precio * $val->porcentaje;
                    } else if ($veri == 0.5) {
                        $subtotal5 += $val->precio * $val->cantidad;
                        $iva5 += $val->precio * $val->porcentaje;
                    } else {
                        $subtotal += $val->extendido;
                    }
                } else {
                    $subtotal += $val->extendido;
                }
            } else {
                $subtotal += $val->extendido;
            }
            $resTotal += ($val->precio * $val->cantidad) - $val->descuento;
            $descuento += $val->descuento;
        }
        $user = Usuarios::find($usuarioSession);
        $view = \View::make('v3.contable.compras.pdf_comprobante', compact("detalle_comprobanteEg","resTotal", "descuento", "subtotal", "subtotal5", "subtotal15", "iva15", "iva5", 'compra', 'user', 'queryAsientos', 'queryMovimiento', 'asientoRetencion', 'retencionAsiento'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'portrait');
        $pdf->setOptions(['chroot'  => base_path('/')]);
        return $pdf->stream('Comprobante de Compra/Venta' . '.pdf');
    }
}
