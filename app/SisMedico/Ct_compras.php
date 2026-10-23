<?php

namespace Sis_medico;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Sis_medico\Ct_master_tipos;

class Ct_compras extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_compras';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function verAsiento($id)
    {
        $html = '';
        $asiento = Ct_Asientos_Cabecera::leftjoin('ct_asientos_detalle as d', 'ct_asientos_cabecera.id', 'd.id_asiento_cabecera')
            ->where('ct_asientos_cabecera.id', $id)->get([
                'ct_asientos_cabecera.id',
                'ct_asientos_cabecera.fecha_asiento',
                'ct_asientos_cabecera.id_empresa',
                'ct_asientos_cabecera.observacion',
                'd.debe',
                'd.haber',
                'ct_asientos_cabecera.estado',
            ]);
        if ($asiento != '') {
            $html .= '<tr>
            <th>Asiento</th>
            <th>Fecha</th>
            <th>Empresa</th>
            <th>Observación</th>
            <th>Debe</th>
            <th>Haber</th>
            <th>Estado</th>
            </tr>';
            $sumDebe = 0;
            $sumHaber = 0;
            foreach ($asiento as $a) {
                $sumDebe += $a->debe;
                $sumHaber += $a->haber;
                $html .= '<tr>
                <td>' . $a->id . '</td>
                <td>' . $a->fecha_asiento . '</td>
                <td>' . $a->id_empresa . '</td>
                <td>' . $a->observacion . '</td>
                <td style="text-align:right;">' . number_format($a->debe, 2) . '</td>
                <td style="text-align:right;">' . number_format($a->haber, 2) . '</td>
                <td>' . $a->estado . '</td>
                </tr>';
            }
            $html .= '<tr>
            <td colspan="4" style="text-align:right;">&nbsp</td>
            <td style="text-align:right;">' . number_format($sumDebe, 2) . '</td>
            <td style="text-align:right;">' . number_format($sumHaber, 2) . '</td>
            <td>&nbsp</td>
            </tr>';
        }
        return $html;
    }
    public static function getDeudasPagos($r)
    {
        $idEmpresa = session('id_empresa');
        $sql = "SELECT * 
        ,                SUM(CASE WHEN tipo = 'Compra' THEN total ELSE -total END) OVER (PARTITION BY id_compra ORDER BY orden) AS saldo_por_compra
        
        FROM (
            (SELECT 
                1 orden,
                c.id AS id_compra,
                'Compra' AS tipo,
                p.id as proveedor,
                p.nombrecomercial,
                c.id_asiento_cabecera,
                c.fecha,
                c.fecha as fecha_comprobante,
                c.numero,
                IFNULL(c.subtotal_0, 0) subtotal0,
                IFNULL(c.subtotal_12, 0) subtotal12,
                IFNULL(c.descuento, 0) descuento,
                (IFNULL(c.subtotal_0, 0) + IFNULL(c.subtotal_12, 0) - IFNULL(c.descuento, 0)) AS subtotal,
                IFNULL(c.iva_total, 0) iva,
                ((IFNULL(c.subtotal_0, 0) + IFNULL(c.subtotal_12, 0) - IFNULL(c.descuento, 0)) + IFNULL(c.iva_total, 0)) AS total,
                IFNULL(c.valor_contable, 0) valor_contable
            FROM proveedor AS p
            LEFT JOIN ct_compras c ON p.id = c.proveedor
            WHERE c.id_empresa = '$idEmpresa'
            AND c.estado != 0)
            
            UNION ALL
            
            (SELECT 
                2 orden,
                r.id_compra,
                'RETENCIONES' AS tipo,
                r.id_proveedor,
                p.nombrecomercial,
                r.id_asiento_cabecera,
                c.fecha,
                r.fecha,
                r.nro_comprobante,
                r.valor_fuente,
                r.valor_iva,
                0 AS descuento,
                (r.valor_fuente + r.valor_iva) AS subtotal,
                0 AS iva,
                (r.valor_fuente + r.valor_iva) AS total,
                0 AS valor_contable
            FROM ct_retenciones r
            LEFT JOIN proveedor p ON r.id_proveedor = p.id
            join ct_compras c on r.id_compra=c.id
            WHERE r.id_empresa = '$idEmpresa'
            AND r.estado != 0)
            
            UNION ALL
            
            (SELECT 
                3 orden,
                de.id_compra,
                'EGRESO' AS tipo,
                p.id,
                p.nombrecomercial,
                ce.id_asiento_cabecera,
                c.fecha,
                ce.fecha_comprobante,
                de.id_secuencia,
                de.saldo_base,
                0 AS subtotal,
                0 AS descuento,
                de.abono,
                0 AS iva,
                de.abono,
                0 AS valor_contable
            FROM ct_detalle_comprobante_egreso de
            JOIN ct_comprobante_egreso ce ON de.id_comprobante = ce.id
            JOIN ct_compras c ON de.id_compra = c.id
            JOIN proveedor p ON c.proveedor = p.id
            WHERE ce.id_empresa = '$idEmpresa' AND ce.estado != 0)
            
            UNION ALL
            
            (SELECT 
                4 orden,
                c.id id_compra,
                'DEBITO BANCARIO' AS tipo,
                p.id,
                p.nombrecomercial,
                dc.id_asiento,
                c.fecha,
                dc.fecha,
                ddb.numero,
                ddb.abono,
                0 AS subtotal,
                0 AS descuento,
                ddb.abono,
                0 AS iva,
                ddb.abono,
                0 AS valor_contable
            FROM ct_debito_bancario_detalle ddb
            left join ct_debito_bancario dc on ddb.id_debito=dc.id
            JOIN ct_compras c ON ddb.id_compra = c.id
            JOIN proveedor p ON c.proveedor = p.id
            WHERE c.id_empresa = '$idEmpresa' AND dc.estado != 0)
            
            UNION ALL
            
            (SELECT 
                5 orden,
                c.id id_compra,
                'CRUCE VALORES' AS tipo,
                p.id,
                p.nombrecomercial,
                dv.id_asiento_cabecera,
                c.fecha,
                dv.fecha_pago,
                dv.secuencia,
                dc.total,
                0 AS subtotal,
                0 AS descuento,
                dc.total,
                0 AS iva,
                dc.total,
                0 AS valor_contable
            FROM ct_detalle_cruce dc 
            JOIN ct_cruce_valores dv ON dc.id_comprobante = dv.id
            JOIN ct_compras c ON dc.id_factura = c.id
            JOIN proveedor p ON c.proveedor = p.id
            WHERE dv.id_empresa = '$idEmpresa' AND dv.estado != 0)
            
            UNION ALL
            
            (SELECT 
                6 orden,
                nc.id_compra,
                'NOTA CREDITO' AS tipo,
                nc.id_proveedor,
                p.nombrecomercial,
                nc.id_asiento_cabecera,
                c.fecha,
                nc.fecha,
                nc.nro_comprobante,
                nc.subtotal_0,
                nc.subtotal_12,
                0 AS descuento,
                (nc.subtotal_0 + nc.subtotal_12) AS subtotal,
                0 AS iva,
                (nc.subtotal_0 + nc.subtotal_12) AS total,
                0 AS valor_contable
            FROM ct_credito_acreedores nc
            JOIN ct_compras c ON nc.id_compra = c.id
            JOIN proveedor p ON c.proveedor = p.id
            WHERE nc.id_empresa = '$idEmpresa' AND nc.estado != 0)
            
            UNION ALL
            
            (SELECT 
                7 orden,
                dcem.id_compra,
                'EGRESO MASIVO' AS tipo,
                dcem.id_proveedor,
                p.nombrecomercial,
                cem.id_asiento_cabecera,
                c.fecha,
                cem.fecha_comprobante,
                cem.secuencia,
                dcem.abono,
                0 AS subtotal12,
                0 AS descuento,
                dcem.abono,
                0 AS iva,
                dcem.abono,
                0 AS valor_contable
            FROM ct_detalle_comprobante_egreso_masivo dcem
            JOIN ct_comprobante_egreso_masivo cem ON dcem.id_comprobante = cem.id
            JOIN ct_compras c ON dcem.id_compra = c.id
            JOIN proveedor p ON c.proveedor = p.id
            WHERE cem.id_empresa = '$idEmpresa' AND cem.estado != 0)
            
            UNION ALL
            
            (SELECT 
                8 orden,
                cc.id_factura,
                'CRUCE DE CUENTAS' AS tipo,
                cc.id_proveedor,
                p.nombrecomercial,
                cc.id_asiento_cabecera,
                c.fecha,
                cc.fecha,
                cc.secuencia,
                cc.total,
                0 AS subtotal12,
                0 AS descuento,
                cc.total,
                0 AS iva,
                cc.total,
                0 AS valor_contable
            FROM ct_cruce_cuentas cc
            JOIN ct_compras c ON cc.id_factura = c.id
            JOIN proveedor p ON c.proveedor = p.id
            WHERE cc.id_empresa = '$idEmpresa' AND cc.estado != 0)
        ) AS fact
        where YEAR(fact.fecha)=$r->anio and MONTH(fact.fecha)=$r->mes and DAY(fact.fecha)=$r->dia
        ORDER BY fact.id_compra, fact.proveedor, fact.orden ASC;";
        return DB::select($sql);
    }
    public function proveedorf()
    {
        return $this->belongsTo('Sis_medico\Proveedor', 'proveedor');
    }
    /*     public function proveedorf()
    {
        $id_empresa = Session::get('id_empresa');
        return $this->belongsTo('Sis_medico\Proveedor', 'proveedor');
    } */
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_detalle_compra', 'id_ct_compras');
    }
    public function retenciones()
    {
        return $this->hasMany('Sis_medico\Ct_Retenciones', 'id_compra');
    }
    public function egresos()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Comprobante_Egreso', 'id_compra', 'id');
    }
    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function master_tipos()
    {
        return $this->belongsTo('Sis_medico\Ct_master_tipos', 'tipo_comprobante', 'codigo');
    }
    public function cruce()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Cruce', 'id_factura', 'id');
    }
    public function cruce_cuentas()
    {
        return $this->hasMany('Sis_medico\Ct_Cruce_Cuentas', 'id_factura', 'id');
    }
    public function bndebito()
    {
        return $this->hasMany('Sis_medico\Ct_Debito_Bancario_Detalle', 'id_compra', 'id')
            /*Quitar si falla*/
            /* ->join('ct_debito_bancario as db', 'ct_debito_bancario_detalle.id_debito', 'db.id')
            ->leftjoin('ct_asientos_detalle as ad', function ($q) {
                $q->on('db.id_asiento', 'ad.id_asiento_cabecera')
                    ->where('ad.descripcion', 'like', '%BANCO%');
            })*/
            /*Quitar si falla*/;
    }
    public function debitoacreedor()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Debito_Acreedores', 'id_factura', 'id');
    }
    public function credito_acreedor()
    {
        return $this->hasMany('Sis_medico\Ct_Credito_Acreedores', 'id_compra', 'id');
    }
    public function masivos()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Comprobante_Egreso_Masivo', 'id_compra', 'id')
            /*->join('ct_comprobante_egreso_masivo as cm', 'ct_detalle_comprobante_egreso_masivo.id_comprobante', 'cm.id')
        ->leftjoin('ct_asientos_detalle as ad', function ($q) {
            $q->on('cm.id_asiento_cabecera', 'ad.id_asiento_cabecera')
                ->where('ad.descripcion', 'like', '%BANCO%');
        })*/
            /*Quitar si falla*/
            /*->join('ct_debito_bancario as db', 'ct_debito_bancario_detalle.id_debito', 'db.id')
         ->leftjoin('ct_asientos_detalle as ad', function ($q) {
             $q->on('db.id_asiento', 'ad.id_asiento_cabecera')
                 ->where('ad.descripcion', 'like', '%BANCO%');
         })*/
            /*Quitar si falla*/;
    }
    public function termino()
    {
        return $this->belongsTo('Sis_medico\Ct_Termino', 'termino', 'id');
    }
    public function facturaActivo()
    {
        return $this->belongsTo('Sis_medico\AfFacturaActivoCabecera', 'id_asiento_cabecera', 'id_asiento');
    }
    public function usuariomod()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod');
    }

    public function asientoCab()
    {

        return $this->belongsTo('Sis_medico\Ct_Asientos_Cabecera', 'id_asiento_cabecera');
    }


    public static function getCompras($id = '')
    {
        $consulta = Ct_compras::join('de_empresa as em', 'ct_compras.id_empresa', '=', 'em.id_empresa')
            ->leftjoin('ct_termino as t', 'ct_compras.termino', '=', 't.id')
            ->leftjoin('proveedor as p', 'ct_compras.proveedor', '=', 'p.id')
            ->where('ct_compras.electronica', 1)
            ->where('ct_compras.estado', '<>', 0)
            ->where('ct_compras.tipo_comprobante', '03');
        if ($id !== '') {
            $consulta->where('ct_compras.id', $id);
        } else {
            $consulta->where('ct_compras.doc_electronico', 0)
                ->where(function ($q) {
                    $q->where(function ($colaNormal) {
                        $colaNormal->where('ct_compras.punto_emision', 100)
                            ->where('ct_compras.fecha', '>', '2022-12-01');
                    })->orWhereIn('ct_compras.id', function ($manual) {
                        $manual->select('dlf_manual.id_documento')
                            ->from('de_log_facturacion as dlf_manual')
                            ->where('dlf_manual.tipo_documento', '03')
                            ->where('dlf_manual.comentario', 'REPROCESO_MANUAL');
                    });
                });
        }
        return $consulta->get([
                'ct_compras.id',
                'ct_compras.id_empresa',
                'ct_compras.created_at',
                'sucursal',
                'punto_emision',
                'secuencia_f',
                'ct_compras.autorizacion',
                'f_autorizacion',
                'total_final',
                'ice_total',
                'iva_total',
                'descuento',
                'subtotal_0',
                'subtotal_12',
                'subtotal',
                'numero', //numero_factura
                'nro_autorizacion',
                'nro_comprobante',
                'fecha',
                'proveedor',
                'observacion',
                'ct_compras.tipo',
                't.dias',
                'p.email',
                'p.razonsocial',
            ]);
    }
    public static function getBusquedaCompras($request)
    {
        $tipo_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $id_empresa       = $request->session()->get('id_empresa');
        $empresa          = Empresa::where('id', $id_empresa)->first();
        $constraints      = [
            'ct_c.id'                  => $request['id'],
            'proveedor'                => $request['proveedor'],
            'observacion'              => $request['detalle'],
            'ct_c.id_asiento_cabecera' => $request['id_asiento_cabecera'],
            'fecha'                    => $request['fecha'],
            'ct_c.tipo_comprobante'    => $request['tipo'],
            'ct_c.numero' => $request['secuencia_f'],
        ];
        //dd($request->all());

        $id_empresa = $request->session()->get('id_empresa');
        $query = DB::table('ct_compras as ct_c')
            ->leftjoin('proveedor as p', 'p.id', 'ct_c.proveedor')
            ->join('users as u', 'u.id', 'ct_c.id_usuariocrea')
            ->join('ct_master_tipos as mt', function ($q) {
                $q->on('ct_c.tipo_comprobante', 'mt.codigo');
            })
            ->where('mt.tipo', 1)
            ->where('ct_c.id_empresa', $id_empresa)
            ->where('ct_c.tipo', 1)
            ->orderBy('ct_c.id', 'desc')
            ->select('mt.nombre as nombre_comprobante', 'ct_c.doc_electronico', 'ct_c.serie', 'ct_c.id', 'ct_c.rutapdf', 'ct_c.numero', 'ct_c.fecha', 'p.razonsocial', 'ct_c.autorizacion', 'u.nombre1', 'u.apellido1', 'ct_c.secuencia_factura', 'ct_c.tipo_comprobante', 'ct_c.estado', 'ct_c.observacion', 'ct_c.id_asiento_cabecera', 'ct_c.nro_autorizacion');

        $fields = array_keys($constraints);
        $index  = 0;
        //dd($constraints);
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                //$query = $query->where($fields[$index], $constraint);
                if ($fields[$index] == "ct_c.id_asiento_cabecera") {
                    //dd("da");
                    $query = $query->where($fields[$index], $constraint);
                } elseif ($fields[$index] == "ct_c.id") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }
            $index++;
        }
        return $query->paginate(10);
    }
    public static function setLogDocElectronico($dato)
    {
        try {
            DB::table('de_log_facturacion')->insert($dato);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
    }
    public static function updateSinGenerarXML($id)
    {
        $arrayDoc = [
            'doc_electronico' => 7,
        ];
        Ct_compras::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateGenerarXML($id, $clave)
    {
        $arrayDoc = [
            'doc_electronico' => 1,
            'nro_autorizacion' => $clave
        ];
        Ct_compras::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateValidacionXSD($id)
    {
        $arrayDoc = [
            'doc_electronico' => 2,
        ];
        Ct_compras::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateXmlFirmado($id)
    {
        $arrayDoc = [
            'doc_electronico' => 3,
        ];
        Ct_compras::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateRecibidoSri($id)
    {
        $arrayDoc = [
            'doc_electronico' => 4,
        ];
        Ct_compras::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateXmlAutorizacion($id, $clave, $secuencial)
    {
        $esta = substr($clave, 24, 3);
        $emis = substr($clave, 27, 3);
        $num = substr($clave, 30, 9);
        $arrayDoc = [
            'doc_electronico' => 5,
            'nro_autorizacion' => $clave,
            'secuencia_f' => $secuencial,
            'numero' => $esta . '-' . $emis . '-' . $secuencial,
            'autorizacion' => $clave,
            'serie' => $esta . '-' . $emis

        ];
        Ct_compras::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateXmlNoRecepcion($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 9
        ];
        Ct_compras::where('id', $idG)->first()
            ->update($arrayDoc);
    }
    public static function updateXmlNoAutorizacion($id)
    {
        $arrayDoc = [
            'doc_electronico' => 10,
        ];
        Ct_compras::where('id', $id)
            ->update($arrayDoc);
    }
}
