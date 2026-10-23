<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\v3\contable\ClaveAcceso;
use Sis_medico\v3\contable\DetalleRetenciones;
use Sis_medico\Ct_Cierre_Cabecera;


class Retenciones extends Model
{
    private static $modelo = 'Retenciones';
    protected $table = 'ct_retenciones';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public function detalles()
    {
        return $this->hasMany('Sis_medico\v3\contable\DetalleRetenciones', 'id_retenciones');
    }

    public static function sumaRetenciones($row, $anio, $mes)
    {
        return self::join('ct_detalle_retenciones as d', 'ct_retenciones.id', 'd.id_retenciones')
            ->whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->where('d.id_porcentaje', $row->id)
            ->sum('d.totales');
    }
    public static function getDatosRetencionCompras($r, $porcentajes)
    {
        $table = '';
        $datos = Retenciones::join('ct_detalle_retenciones as dr', 'ct_retenciones.id', 'dr.id_retenciones')
            ->where('ct_retenciones.id_compra', $r->id)
            ->get();
        $totalRecibir = $r->total;
        $numRetencion = '';
        $numAutorizacion = '';
        if (count($datos) > 0) {
            foreach ($porcentajes as $row) {
                $valor = 0;
                foreach ($datos as $d) {
                    if ($row->id == $d->id_porcentaje) {
                        $valor = $d->totales;
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
        $table .= '<td style="text-align:right;">' . number_format($totalRecibir, 4) . '</td><td>' . $numRetencion . '</td><td>' . $numAutorizacion . '</td>';
        return $table;
    }
    private static function generarNumeroRetencion($data)
    {
        $num = Retenciones::where('sucursal', $data['sucursal'])
            ->where('punto_emision', $data['punto_emision'])
            ->where('id_empresa', session('id_empresa'))
            ->max('secuencia');
        if ($num == '')
            $num = $data['sucursal'] . '-' . $data['punto_emision'] . '-' . '000000001';
        else {
            $num = $num + 1;
            $num = $data['sucursal'] . '-' . $data['punto_emision'] . '-' . str_pad($num, 9, 0, STR_PAD_LEFT);
        }
        return $num;
    }
    public static function egreso($id_compra)
    {
        $data = 0;
        if ($id_compra != "") {
            $egreso = DetalleComprobanteEgreso::where('id_compra', $id_compra)->get();
            foreach ($egreso as $x) {
                if ($x->comp_egreso->estado == 1) {
                    $data += $x->abono;
                }
            }
        }
        return $data;
    }
    public static function egreso_masivo($id_compra)
    {
        $data = 0;
        if ($id_compra != "") {
            $egreso = DetalleComprobanteEgreso::where('id_compra', $id_compra)->get();
            foreach ($egreso as $x) {
                if ($x->comp_egreso->estado == 1) {
                    $data += $x->abono;
                }
            }
        }
        return $data;
    }

    public static function cruce($id_compra)
    {
        $data = 0;
        if ($id_compra != "") {
            $cruce = DetalleCruce::where('id_factura', $id_compra)->get();
            foreach ($cruce as $x) {
                if ($x->cabecera->estado == 1) {
                    $data += $x->total;
                }
            }
        }
        return $data;
    }

    public static function cruce_cuentas($id_compra)
    {
        $data = 0;
        if ($id_compra != "") {
            $cruce = CruceCuentas::where('id_factura', $id_compra)->where('estado', '1')->get();
            if (!is_null($cruce)) {
                foreach ($cruce as $x) {
                    $data += $x->total;
                }
            }
        }
        return $data;
    }

    public static function retenciones($id_compra)
    {
        $data = 0;
        if ($id_compra != "") {
            $retenciones = Retenciones::where('id_compra', $id_compra)->where('estado', '1')->first();
            if (!is_null($retenciones)) {
                $data = $retenciones->valor_fuente + $retenciones->valor_iva;
            }
        }
        return $data;
    }

    public static function debito($id_compra)
    {
        $data = 0;
        if ($id_compra != "") {
            $debito = DebitoBancarioDetalle::where('id_compra', $id_compra)->get();
            foreach ($debito as $x) {
                if ($x->cabecera->estado == 1) {
                    $data += $x->abono;
                }
            }
        }
        return $data;
    }

    public static function debito_acreedores($id_compra)
    {
        $data = 0;
        if ($id_compra != "") {
            $debito = DebitoBancarioDetalle::where('id_compra', $id_compra)->where('estado', '1')->get();
            foreach ($debito as $x) {
                if ($x->cabecera->estado == 1) {
                    $data += $x->total;
                }
            }
        }
        return $data;
    }

    public static function credito_acreedores($id_compra)
    {
        $data = 0;
        if ($id_compra != "") {
            $credito = CreditoAcreedores::where('id_compra', $id_compra)->where('estado', '1')->get();
            if (!is_null($credito)) {
                foreach ($credito as $x) {
                    $data += $x->valor_contable;
                }
            }
        }
        return $data;
    }

    public static function RecoveryPrice($id_compra)
    {

        try {
            $compras = Compras::where('id', $id_compra)->first();
            $egreso = self::egreso($id_compra);
            $egreso_masivo = self::egreso_masivo($id_compra);
            $cruce = self::cruce($id_compra);
            $cruce_cuentas = self::cruce_cuentas($id_compra);
            $retenciones = self::retenciones($id_compra);
            $debito_bancario = self::debito($id_compra);
            $compras->estado = 3;
            $debito_acreedores = self::debito_acreedores($id_compra);
            $credito_acreedores = self::credito_acreedores($id_compra);
            $restar = $egreso + $cruce + $retenciones + $debito_bancario + $debito_acreedores + $credito_acreedores + $egreso_masivo + $cruce_cuentas;
            $total = $compras->total_final - $restar;

            if ($total < 0) {
                $total = 0;
            }
            $compras->valor_contable = $total;
            $compras->save();
            return ["status" => true, 'msj' => "ok"];
        } catch (Exception $e) {
            return ["status" => false, 'msj' => $e->getMessage()];
        }
    }

    public static function saveRetencionProveedor($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        try {
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($r->fecha_retencion));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'message' => $verifica_cierre->message];
            }
            $origin = $r->input();
            $cont = 0;
            $idusuario = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $establecimiento = Sucursales::getSucursales($r->establecimiento);
            $emision = Caja::getPuntoEmision($r->emision);
            $factura = Compras::getCompras($r->id_factura,$r);
            $datos = [
                'sucursal' => $establecimiento->codigo_sucursal,
                'punto_emision' => $emision->codigo_caja
            ];
            $num_comprobante = Retenciones::generarNumeroRetencion($datos);
            //verificar si existe una retencion
            $verificar = Retenciones::where("id_compra", $r->id_factura)->where('estado', '!=', 0)->first();
            if (!is_null($verificar)) {
                throw new Exception("Ya existe una retención para esta factura");
            }
            $arrayAsientoCabecera = [
                'sucursal' => $establecimiento->codigo_sucursal,
                'punto_emision' => $emision->codigo_caja,
                'fecha_asiento' => $r->fecha_retencion,
                'fact_numero' => $num_comprobante,
                'id_empresa' => session('id_empresa'),
                'observacion' => $r->descripcion,
                'valor' => $r->total,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $asiento = Contabilidad::crearAsiento('compras', 'retencion', $arrayAsientoCabecera, $r);
            if ($asiento['status'] == 'error') {
                throw new Exception($asiento["message"]);
            }
            $deEmpresa = DeEmpresas::where('id_empresa', session('id_empresa'))->first();
            $numComprobante = explode('-', $num_comprobante);
            if ($deEmpresa != '') {
                $claveObj = new ClaveAcceso();
                $campos = [
                    'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($r->fecha_retencion))),
                    'tipo_comprobante' => '01',
                    'ruc' => session('id_empresa'),
                    'tipo_ambiente' => $deEmpresa->ambiente,
                    'punto_establecimiento' => $numComprobante[0],
                    'punto_emision' => $numComprobante[1],
                    'numero_comprobante' => $numComprobante[2],
                    'codigo_numerico' => $numComprobante[2],
                    'tipo_emision' => 1
                ];
                $claveObj->llenarDatos($campos);
            }

            $arrayDatos = [
                'id_empresa' => session('id_empresa'),
                'id_asiento_cabecera' => $asiento,
                'punto_emision' => $emision->codigo_caja,
                'sucursal' => $establecimiento->codigo_sucursal,
                'tipo' => $factura->tipo,
                'id_compra' => $factura->id,
                'valor_fuente' => $r->renta,
                'valor_iva' => $r->iva,
                'id_tipo' => 1,
                'electronica' => $deEmpresa != '' ? 1 : 0,
                'fecha' => $r->fecha_retencion,
                'nro_comprobante' => $num_comprobante,
                'secuencia' => $numComprobante[2],
                'descripcion' => $r->descripcion,
                'total' => $r->descripcion,
                'id_proveedor' => $factura->proveedor,
                'estado' => 1,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'estado_electronica' => 0,
                'anulado' => 0,
                'doc_electronico' => 0,
                'clave_acceso' => isset($claveObj) ? $claveObj->clave_acceso : ''
            ];
            $idRetencion = Retenciones::insertGetId($arrayDatos);
            for ($i = 0; $i < count($origin['tipo_retencion']); $i++) {
                if ($origin['tipo_retencion'][$i] != "") {
                    $porcentaje = explode("|", $origin['porcentaje_retencion'][$i]);
                    $idCodigo = PorcentajeRetenciones::find($porcentaje[0]);
                    DetalleRetenciones::insert([
                        'id_retenciones'  => $idRetencion,
                        'observacion'     => $origin['descripcion'],
                        'id_tipo'         => $porcentaje[0],
                        'tipo'            => $origin["tipo_retencion"][$i],
                        'id_porcentaje'   => $porcentaje[0],
                        'codigo'          => $idCodigo->codigo_nombre,
                        'base_imponible'  => $origin['base_retencion'][$i],
                        'porcentaje'      => $porcentaje[2],
                        'estado'          => '1',
                        'totales'         => $origin['valor_retencion'][$i],
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                }
            }
            $factura->valor_contable =  $factura->valor_contable - $r->total;
            $factura->save();
            // $restaurarCompra = self::RecoveryPrice($factura->id);
            // if (!$restaurarCompra["status"]) {
            //     throw new Exception($asiento["msj"]);
            // }
            $r->c = 'Retenciones';
            $r->s = 'saveRetencion';
            $r->d = $origin['d'];
            $r->m = Retenciones::$modelo;
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
            $r->m = Retenciones::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
            return json_encode($arrayRespuesta);
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
                'reload' => "",
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function getreporteRetenciones($r)
    {
        DB::enableQueryLog();

        $valRentaTotal = DetalleRetenciones::join('ct_retenciones', 'ct_retenciones.id', '=', 'ct_detalle_retenciones.id_retenciones')
            ->join('proveedor as p', 'ct_retenciones.id_proveedor', '=', 'p.id')
            ->join('ct_compras as c', 'ct_retenciones.id_compra', '=', 'c.id')
            ->where('ct_detalle_retenciones.tipo', 'RENTA')
            ->whereBetween('ct_retenciones.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
            ->where('ct_retenciones.estado', '1')
            ->where(function ($q) use ($r) {
                if ($r->proveedor != '') {
                    $q->where('p.id', $r->proveedor);
                }
                if ($r->secuencia != '') {
                    $q->where('c.numero', 'like', '%' . $r->secuencia . '%');
                }
                if ($r->retencion != '') {
                    $q->where('ct_retenciones.nro_comprobante', 'like', '%' . $r->retencion . '%');
                }
            })
            ->sum('ct_detalle_retenciones.totales');

        $valIvaTotal = DetalleRetenciones::join('ct_retenciones', 'ct_retenciones.id', '=', 'ct_detalle_retenciones.id_retenciones')
            ->join('proveedor as p', 'ct_retenciones.id_proveedor', '=', 'p.id')
            ->join('ct_compras as c', 'ct_retenciones.id_compra', '=', 'c.id')
            ->where('ct_detalle_retenciones.tipo', 'IVA')
            ->whereBetween('ct_retenciones.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
            ->where('ct_retenciones.estado', '1')
            ->where(function ($q) use ($r) {
                if ($r->proveedor != '') {
                    $q->where('p.id', $r->proveedor);
                }
                if ($r->secuencia != '') {
                    $q->where('c.numero', 'like', '%' . $r->secuencia . '%');
                }
                if ($r->retencion != '') {
                    $q->where('ct_retenciones.nro_comprobante', 'like', '%' . $r->retencion . '%');
                }
            })
            ->sum('ct_detalle_retenciones.totales');

        return DB::table('ct_retenciones as r')
            ->select(
                'r.fecha',
                'r.id_asiento_cabecera',
                'r.nro_comprobante',
                'c.numero',
                'p.razonsocial',
                'p.id',
                'r.descripcion',
                'r.valor_fuente',
                'r.tipo',
                DB::raw($valRentaTotal . ' as sumarenta'),
                DB::raw('(SELECT IFNULL(porcentaje, 0) FROM ct_detalle_retenciones WHERE id_retenciones=r.id AND totales=r.valor_fuente AND tipo="RENTA") as porrenta'),
                'r.valor_iva',
                DB::raw($valIvaTotal . ' as sumaiva'),
                DB::raw('(SELECT IFNULL(porcentaje, 0) FROM ct_detalle_retenciones WHERE id_retenciones=r.id AND totales=r.valor_iva AND tipo="IVA") as poriva'),
                'r.estado',
                DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                DB::raw('IF(r.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                DB::raw('CASE WHEN r.tipo = 1 THEN CONCAT("COM-FA-", r.nro_comprobante) ELSE CONCAT("COM-FACT-", r.nro_comprobante) END AS nro_comprobante')
            )
            ->join('ct_detalle_retenciones as d', 'r.id', '=', 'd.id_retenciones')
            ->join('ct_compras as c', 'r.id_compra', '=', 'c.id')
            ->join('proveedor as p', 'r.id_proveedor', '=', 'p.id')
            ->join('users as uc', 'r.id_usuariocrea', '=', 'uc.id')
            ->leftJoin('users as um', 'r.id_usuariomod', '=', 'um.id')
            ->whereBetween('r.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
            ->where(function ($q) use ($r) {
                if ($r->proveedor != '') {
                    $q->where('p.id', $r->proveedor);
                }
                if ($r->secuencia != '') {
                    $q->where('c.numero', 'like', '%' . $r->secuencia . '%');
                }
                if ($r->retencion != '') {
                    $q->where('r.nro_comprobante', 'like', '%' . $r->retencion . '%');
                }
            })
            ->groupBy('r.id')
            ->get();
    }
    public static function getRetenciones($r, $todo = '')
    {
        if (isset($r->d)) {
            return Retenciones::join('users as uc', 'ct_retenciones.id_usuariocrea', 'uc.id')
                ->join('users as um', 'ct_retenciones.id_usuariomod', 'um.id')
                ->join('proveedor as pv', 'pv.id', 'ct_retenciones.id_proveedor')
                ->join('ct_compras as c', 'c.id', 'ct_retenciones.id_compra')
                ->whereBetween("ct_retenciones.fecha", [$r->d, $r->h])
                ->get([
                    'ct_retenciones.id',
                    'ct_retenciones.fecha',
                    'pv.razonsocial as id_proveedor',
                    'ct_retenciones.descripcion',
                    'c.numero as id_compra',
                    'ct_retenciones.nro_comprobante',
                    'ct_retenciones.valor_fuente',
                    'ct_retenciones.valor_iva',
                    'ct_retenciones.total',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'ct_retenciones.estado',
                    'ct_retenciones.electronica',
                    'ct_retenciones.doc_electronico',
                    DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                    DB::raw('IF(ct_retenciones.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                ]);
        } elseif ($todo != '') {
            return Retenciones::join('users as uc', 'ct_retenciones.id_usuariocrea', 'uc.id')
                ->join('users as um', 'ct_retenciones.id_usuariomod', 'um.id')
                ->join('proveedor as pv', 'pv.id', 'ct_retenciones.id_proveedor')
                ->join('ct_compras as c', 'c.id', 'ct_retenciones.id_compra')
                ->where('ct_retenciones.id_empresa', session('id_empresa'))
                ->whereYear('ct_retenciones.fecha', session('periodo'))
                ->get([
                    'ct_retenciones.id',
                    'ct_retenciones.fecha',
                    'pv.razonsocial as id_proveedor',
                    'ct_retenciones.descripcion',
                    'c.numero as id_compra',
                    'ct_retenciones.nro_comprobante',
                    'ct_retenciones.valor_fuente',
                    'ct_retenciones.valor_iva',
                    'ct_retenciones.total',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'ct_retenciones.estado',
                    'ct_retenciones.electronica',
                    'ct_retenciones.doc_electronico',
                    DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                    DB::raw('IF(ct_retenciones.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                ]);
        } else {
            return Retenciones::join('users as uc', 'ct_retenciones.id_usuariocrea', 'uc.id')
                ->join('proveedor as pv', 'pv.id', 'ct_retenciones.id_proveedor')
                ->join('ct_compras as c', 'c.id', 'ct_retenciones.id_compra')
                ->where('ct_retenciones.id', $r)
                ->first([
                    'ct_retenciones.id',
                    'ct_retenciones.fecha',
                    'pv.razonsocial as id_proveedor',
                    'ct_retenciones.descripcion',
                    'c.numero as id_compra',
                    'ct_retenciones.nro_comprobante',
                    'ct_retenciones.valor_fuente',
                    'ct_retenciones.valor_iva',
                    'ct_retenciones.total',
                    'uc.nombre1 as ucnombre',
                    'uc.apellido1 as ucapellido',
                    'ct_retenciones.estado',
                    'ct_retenciones.electronica',
                    'ct_retenciones.doc_electronico',
                    'ct_retenciones.nro_comprobante'
                ]);
        }
    }
    public static function cambiarEstado($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (Retenciones::where('id', $r->id)->update(['estado' => $estado])) {
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
    public static function getRetencionPrint($r)
    {
        $id = $r->id;
        $desde = $r->todo;
        $contenidoHtml = 'Busqueda sin resultados';
        if ($desde != '') {
            $dato = Retenciones::where('id_compra', $id)
                ->where('estado', '!=', 0)
                ->first();
            if ($dato != '') {
                $id = $dato->id;
            }
        }
        $retenciones = Retenciones::join('users as uc', 'ct_retenciones.id_usuariocrea', 'uc.id')
            ->join('proveedor as pv', 'pv.id', 'ct_retenciones.id_proveedor')
            ->join('ct_compras as c', 'c.id', 'ct_retenciones.id_compra')
            ->join("users","users.id","ct_retenciones.id_usuariocrea")
            ->where('ct_retenciones.id', $id)
            ->get([
                'ct_retenciones.id',
                'ct_retenciones.fecha',
                'ct_retenciones.id_proveedor',
                'pv.razonsocial',
                'pv.direccion',
                'pv.telefono1',
                'pv.telefono2',
                'pv.email',
                'ct_retenciones.id_tipo',
                'ct_retenciones.id_usuariocrea',
                'ct_retenciones.descripcion',
                'c.numero as id_compra',
                'ct_retenciones.nro_comprobante',
                'ct_retenciones.valor_fuente',
                'ct_retenciones.valor_iva',
                'ct_retenciones.total',
                'uc.nombre1 as ucnombre',
                'uc.apellido1 as ucapellido',
                'ct_retenciones.estado',
                'ct_retenciones.electronica',
                'ct_retenciones.doc_electronico',
                'ct_retenciones.nro_autorizacion',
                'ct_retenciones.autorizacion',
                DB::raw('concat(users.nombre1," ",users.apellido1," ",users.apellido2) as nombreC'),
            ]);
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
                            Teléfono: ' . $retenciones[0]->telefono1 . ' ' . $retenciones[0]->telefono2 . '<br>
                            E-mail: <a href="mailto:' . $retenciones[0]->email . '">' . $retenciones[0]->email . '</a>
                        </address>
                    </div> 
                    <div class="col-sm-4 invoice-col">
                        <b>Factura #' . $retenciones[0]->nro_comprobante . '</b><br>
                        <br>
                        <b>Fecha máxima de pago:</b> ' . $fechaMax . '<br>
                        <b>Autorización:</b> <br> <span style="font-size:13px;">' . $retenciones[0]->nro_autorizacion . '</span>
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
                $det_retenciones = DetalleRetenciones::where('id_retenciones', $id)->get();
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
                        <div class="col-12">
                            <a href="' . url('v3/') . '/proveedores/printFactura?id=' . $retenciones[0]->id . '&tipo=1" rel="noopener" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> <span class="botones">Imprimir</span></a>
                            <a class="btn btn-success float-right" href="' . url('/') . '/api_doc_electronico?opcion=descargarXML&clave=' . $retenciones[0]->nro_autorizacion . '" ><i class="far fa-file-code"></i>  <span class="botones">Generar XML</span></a>
                            <a class="btn btn-primary float-right" href="' . url('/') . '/api_doc_electronico?opcion=generarPdf&clave=' . $retenciones[0]->nro_autorizacion . '" style="margin-right: 5px;"><i class="fas fa-download"></i>  <span class="botones">Generar PDF</span></a>
                        </div>
                    </div>
                     <div class="col-6">
                    <span style="font-size:x-small;">Creado por: ' . ucwords(strtolower($retenciones[0]->nombreC)) .($tipo == 1 ?' impreso: ' . date('Y-m-d H:i:s')  : ''). '</span>
                    <div>
                    ';
        }
        return $contenidoHtml;
    }
    public static function traerRetenciones($r)
    {
        return Compras::join('proveedor as p', 'ct_compras.proveedor', 'p.id')
            ->leftjoin('ct_retenciones as r', function ($q) {
                $q->on('ct_compras.id', '=', 'r.id_compra')
                    ->where('r.estado', '!=', 0);
            })
            ->where('ct_compras.tipo', '<>', 3)
            ->where('ct_compras.estado', '<>', 0)
            ->where('ct_compras.id_empresa', session('id_empresa'))
            ->where('ct_compras.valor_contable', '>', 0)
            ->where(function ($q) use ($r) {
                $q->where('p.razonsocial', 'LIKE', '%' . $r->term . '%')
                    ->orWhere('ct_compras.numero', 'LIKE', '%' . $r->term . '%')
                    ->orWhere('p.id', 'LIKE', '%' . $r->term . '%');
            })
            ->whereNull('r.id_compra')
            ->get([
                'ct_compras.id',
                'ct_compras.proveedor',
                'ct_compras.serie',
                'ct_compras.numero',
                'p.razonsocial',
                'ct_compras.observacion',
            ]);
    }

    public static function verificarAnulacion($r)
    {
        try {
            $id = $r['id_retencion'];
            $tiene = "no";
            $tiene = "no";
            $tabla_egre  = "";
            $tabla_banca = "";
            $tabla_cruce = "";
            $id_egreso = 0;
            $id_asiento = 0;
            $retenciones = Retenciones::where('id', $id)->first();
            $com_egre    = DetalleComprobanteEgreso::where('id_compra', $retenciones->id_compra)->get();
            if (count($com_egre) > 0) {
                foreach ($com_egre as $value) {
                    $egreso = ComprobanteEgreso::where('id', $value->id_comprobante)->where('estado', '=', '1')->get();
                    if (count($egreso) > 0) {
                        $tiene      = "si";
                        $tabla_egre = "Comprobante de Egreso ";
                        $id_egreso  = $egreso[0]->id;
                        $id_asiento = $egreso[0]->id_asiento;
                    }
                }
            }
            $id_deb_ban       = 0;
            $debito_banca_det = DebitoBancarioDetalle::where('id_compra', $retenciones->id_compra)->get();
            if (count($debito_banca_det) > 0) {
                foreach ($debito_banca_det as $value) {
                    $debit_banca = DB::table('ct_debito_bancario')->where('id', $value->id_debito)->where('estado', '=', '1')->get();
                    if (count($debit_banca) > 0) {
                        $tiene       = "si";
                        $tabla_banca = "Existe un debito bancario para el comprobante de compras";
                        $id_deb_ban  = $debit_banca[0]->id;
                        $id_asiento  = $debit_banca[0]->id_asiento;
                    }
                }
            }
            $id_cruce_valores = 0;
            $detalle_cruce = DB::table('ct_detalle_cruce')->where('id_factura', $retenciones->id_compra)->get();
            if (count($detalle_cruce) > 0) {
                foreach ($detalle_cruce as $value) {
                    $cruce_valores = CruceValores::where('id', $value->id_comprobante)->where('estado', '1')->get();
                    if (count($cruce_valores) > 0) {
                        $tiene = "si";
                        $tabla_cruce = "Cruce Valores ";
                        $id_cruce_valores = $cruce_valores[0]->id;
                        $id_asiento = $cruce_valores[0]->id_asiento;
                    }
                }
            }
            $tablas = [$tabla_egre, $tabla_banca, $tabla_cruce];
            $ids    = [$id_egreso, $id_deb_ban, $id_cruce_valores];
            $msj = '';
            $msjId = '';
            if ($tabla_egre != '')
                $msj = $tabla_egre;
            elseif ($tabla_banca != '')
                $msj = $tabla_banca;
            elseif ($tabla_cruce != '')
                $msj = $tabla_cruce;
            if ($id_egreso != 0)
                $msjId = $id_egreso;
            elseif ($id_deb_ban != 0)
                $msjId = $id_deb_ban;
            elseif ($id_cruce_valores != 0) {
                $msjId = $id_cruce_valores;
            }
            return ['id' => $retenciones->id, 'tablas' => $tablas, 'respuesta' => $tiene, "ids" => $ids, 'msj' => $msj, 'message' => $msj, 'footer' => '<a href="javascript:;">' . $msj . ' Id: ' . $msjId . ' | Id asiento: ' . $id_asiento . '</a>'];
        } catch (Exception $ex) {
            return ['status' => 'error', 'respuesta' => $tiene, 'msj' => $ex->getMessage()];
        }
    }

    public static function anularRetencion($r)
    {
        try {
            $ip_cliente      = $_SERVER["REMOTE_ADDR"];
            $idusuario       = Auth::user()->id;
            $retenciones     = Retenciones::where('id', $r->id)->first();
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($retenciones->fecha));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }
            $asiento         = AsientosCabecera::findorfail($retenciones->id_asiento_cabecera);
            $asiento->estado = 1;
            $asiento->save();
            $detalles       = $asiento->detalles;
            $valorestotales = $retenciones->valor_fuente + $retenciones->valor_iva;
            $concepto       = $r['concepto'];
            $id_empresa     = session('id_empresa');
            if (!empty($retenciones)) {
                $retenciones->estado = 0;
                $retenciones->ip_modificacion = $ip_cliente;
                $retenciones->id_usuariomod = $idusuario;
                $retenciones->save();
                $compra         = Compras::where('id', $retenciones->id_compra)->first();
                $valor_contable = $compra->valor_contable + $valorestotales;
                if (!empty($compra)) {
                    $compra->estado = 1;
                    $compra->valor_contable = $valor_contable;
                    $compra->ip_modificacion = $ip_cliente;
                    $compra->id_usuariomod = $idusuario;
                    $compra->save();
                }
                self::RecoveryPrice($retenciones->id_compra);
                $id_asiento = AsientosCabecera::insertGetId([
                    'observacion'     => $concepto,
                    'fecha_asiento'   => $asiento->fecha_asiento,
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $retenciones->secuencia,
                    'valor'           => $asiento->valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'sucursal'        => $retenciones->sucursal,
                ]);
                foreach ($detalles as $value) {
                    AsientosDetalles::insert([
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
            }
            return [
                'status' => 'success',
                'code' => 200,
                'msj' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => ''
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'msj' => $e->getMessage(),
                'line' => $e->getLine(),
                'data' => $r->input(),
                'reload' => ''
            ];
        }
    }
}
