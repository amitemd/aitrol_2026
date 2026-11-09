<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateProviderDebtPaymentProcedure extends Migration
{
    /**
     * Instala el procedimiento usado por el reporte V3 Deudas vs Pagos de proveedores.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_proveedores_deudas_pagos');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_proveedores_deudas_pagos(
    IN p_id_empresa VARCHAR(255),
    IN p_desde VARCHAR(255),
    IN p_hasta VARCHAR(255),
    IN p_id_proveedor VARCHAR(255)
)
BEGIN
    SELECT *,
        CASE WHEN tipo = 'Factura' THEN total_final ELSE 0 END AS deuda,
        CASE WHEN tipo != 'Factura' THEN total_final ELSE 0 END AS abono,
        SUM(CASE WHEN tipo = 'Factura' THEN total_final ELSE -total_final END)
            OVER (PARTITION BY dvp.id_empresa, idc ORDER BY dvp.idc, dvp.id_proveedor, orden) AS saldo,
        SUM(CASE WHEN tipo = 'Factura' THEN total_final ELSE 0 END)
            OVER (PARTITION BY dvp.id_empresa ORDER BY dvp.idc, dvp.id_proveedor, orden) AS deuda_total,
        SUM(CASE WHEN tipo != 'Factura' THEN total_final ELSE 0 END)
            OVER (PARTITION BY dvp.id_empresa ORDER BY dvp.idc, dvp.id_proveedor, orden) AS abono_total
    FROM (
        SELECT '' AS aux,
            0 AS orden,
            c.id_asiento_cabecera AS id_asiento,
            CONCAT(p.razonsocial, ' Fact: # ', c.numero, ' Ref: ', c.id) AS detalle,
            CONCAT(cad.id_plan_cuenta, ' | ', cad.descripcion) AS cuenta,
            CONCAT('COM-FA ', c.secuencia_f, ' | Ref: ', c.id) AS Comprobante,
            pce.plan,
            pc.nombre,
            c.id AS idc,
            'Factura' AS tipo,
            c.fecha,
            c.fecha AS fecha_documento,
            c.numero,
            p.id AS id_proveedor,
            p.razonsocial,
            c.subtotal_0,
            c.subtotal_12,
            c.descuento,
            c.subtotal,
            c.iva_total,
            c.total_final,
            c.id_empresa
        FROM proveedor AS p
        JOIN ct_compras AS c ON p.id = c.proveedor
        LEFT JOIN ct_asientos_detalle AS cad
            ON c.id_asiento_cabecera = cad.id_asiento_cabecera
            AND cad.descripcion LIKE '%PROVEE%'
        LEFT JOIN plan_cuentas AS pc ON cad.id_plan_cuenta = pc.id
        LEFT JOIN plan_cuentas_empresa AS pce
            ON cad.id_plan_cuenta = pce.id_plan
            AND pce.id_empresa = p_id_empresa
        WHERE c.estado != 0

        UNION ALL

        SELECT DISTINCT co.id AS aux,
            1 AS orden,
            c.id_asiento_cabecera,
            CONCAT(p.razonsocial, ' # ', dc.observacion, ' Ref: ', dc.id_secuencia, ' #Egreso: ', dc.id_comprobante, ' -- Metodo de pago: ', ccb.nombre) AS detalle,
            CONCAT(cad.id_plan_cuenta, ' | ', cad.descripcion) AS cuenta,
            CONCAT('ACR-EG ', c.secuencia, ' | Ref: ', c.id) AS Comprobante,
            pce.plan,
            pc.nombre,
            co.id AS idc,
            'COMP-EGR' AS tipo,
            co.fecha,
            c.fecha_comprobante AS fecha_documento,
            c.secuencia,
            p.id AS id_proveedor,
            p.razonsocial,
            0 AS subtotal_0,
            0 AS subtotal_12,
            0 AS descuento,
            0 AS subtotal,
            0 AS iva_total,
            dc.abono,
            c.id_empresa
        FROM ct_detalle_comprobante_egreso AS dc
        JOIN ct_comprobante_egreso AS c ON dc.id_comprobante = c.id
        JOIN ct_caja_banco AS ccb ON ccb.id = c.id_caja_banco
        JOIN ct_compras AS co ON dc.id_compra = co.id
        JOIN proveedor AS p ON c.id_proveedor = p.id
        LEFT JOIN ct_asientos_detalle AS cad
            ON cad.id = (
                SELECT ad_pago.id
                FROM ct_asientos_detalle AS ad_pago
                WHERE ad_pago.id_asiento_cabecera = c.id_asiento_cabecera
                    AND ad_pago.estado != 0
                    AND ad_pago.debe > 0
                ORDER BY ad_pago.debe DESC, ad_pago.id
                LIMIT 1
            )
        LEFT JOIN plan_cuentas AS pc ON cad.id_plan_cuenta = pc.id
        LEFT JOIN plan_cuentas_empresa AS pce
            ON cad.id_plan_cuenta = pce.id_plan
            AND pce.id_empresa = p_id_empresa
        WHERE c.estado != 0

        UNION ALL

        SELECT '' AS aux,
            2 AS orden,
            db.id_asiento,
            CONCAT(p.razonsocial, ' # ', db.concepto, ' Ref: ', dd.numero, ' #Debito: ', db.id, ' -- Metodo de pago: ', ccb.nombre) AS detalle,
            CONCAT(cad.id_plan_cuenta, ' | ', cad.descripcion) AS cuenta,
            CONCAT('BAN-ND ', db.secuencia, ' | Ref: ', db.id) AS Comprobante,
            pce.plan,
            pc.nombre,
            co.id AS idc,
            'DEB-BAN' AS tipo,
            co.fecha,
            db.fecha AS fecha_documento,
            db.secuencia,
            p.id AS id_proveedor,
            p.razonsocial,
            0 AS subtotal_0,
            0 AS subtotal_12,
            0 AS descuento,
            0 AS subtotal,
            0 AS iva_total,
            dd.abono,
            db.id_empresa
        FROM ct_debito_bancario_detalle AS dd
        JOIN ct_debito_bancario AS db ON dd.id_debito = db.id
        JOIN ct_compras AS co ON dd.id_compra = co.id
        JOIN proveedor AS p ON db.id_acreedor = p.id
        JOIN ct_caja_banco AS ccb ON ccb.id = db.id_banco
        LEFT JOIN ct_asientos_detalle AS cad
            ON co.id_asiento_cabecera = cad.id_asiento_cabecera
            AND cad.descripcion LIKE '%PROVEE%'
        LEFT JOIN plan_cuentas AS pc ON cad.id_plan_cuenta = pc.id
        LEFT JOIN plan_cuentas_empresa AS pce
            ON cad.id_plan_cuenta = pce.id_plan
            AND pce.id_empresa = p_id_empresa
        WHERE db.estado != 0

        UNION ALL

        SELECT '' AS aux,
            3 AS orden,
            cc.id_asiento_cabecera,
            CONCAT(p.razonsocial, ' # ', cc.detalle, ' Ref: ', co.secuencia_f, ' #Cruce_Cuentas: ', cc.id) AS detalle,
            CONCAT(cad.id_plan_cuenta, ' | ', cad.descripcion) AS cuenta,
            CONCAT('CRUCE-CUENTAS ', cc.secuencia, ' | Ref: ', cc.id) AS Comprobante,
            pce.plan,
            pc.nombre,
            co.id AS idc,
            'CRUC-CUENTA' AS tipo,
            co.fecha,
            cc.fecha AS fecha_documento,
            cc.secuencia,
            p.id AS id_proveedor,
            p.razonsocial,
            0 AS subtotal_0,
            0 AS subtotal_12,
            0 AS descuento,
            0 AS subtotal,
            0 AS iva_total,
            cc.total,
            cc.id_empresa
        FROM ct_cruce_cuentas AS cc
        JOIN ct_compras AS co ON cc.id_factura = co.id
        JOIN proveedor AS p ON cc.id_proveedor = p.id
        LEFT JOIN ct_asientos_detalle AS cad
            ON co.id_asiento_cabecera = cad.id_asiento_cabecera
            AND cad.descripcion LIKE '%PROVEE%'
        LEFT JOIN plan_cuentas AS pc ON cad.id_plan_cuenta = pc.id
        LEFT JOIN plan_cuentas_empresa AS pce
            ON cad.id_plan_cuenta = pce.id_plan
            AND pce.id_empresa = p_id_empresa
        WHERE cc.estado != 0

        UNION ALL

        SELECT '' AS aux,
            4 AS orden,
            cv.id_asiento_cabecera,
            CONCAT(p.razonsocial, ' # ', cv.detalle, ' Ref: ', dc.secuencia_factura, ' #Cruce: ', dc.id_comprobante) AS detalle,
            CONCAT(cad.id_plan_cuenta, ' | ', cad.descripcion) AS cuenta,
            CONCAT('ACR-CR-AF ', cv.secuencia, ' | Ref: ', cv.id) AS Comprobante,
            pce.plan,
            pc.nombre,
            co.id AS idc,
            'CRUC-VALOR' AS tipo,
            co.fecha,
            cv.fecha_pago AS fecha_documento,
            cv.secuencia,
            p.id AS id_proveedor,
            p.razonsocial,
            0 AS subtotal_0,
            0 AS subtotal_12,
            0 AS descuento,
            0 AS subtotal,
            0 AS iva_total,
            dc.total,
            cv.id_empresa
        FROM ct_cruce_valores AS cv
        JOIN ct_detalle_cruce AS dc ON cv.id = dc.id_comprobante
        JOIN ct_compras AS co ON dc.id_factura = co.id
        JOIN proveedor AS p ON cv.id_proveedor = p.id
        LEFT JOIN ct_asientos_detalle AS cad
            ON co.id_asiento_cabecera = cad.id_asiento_cabecera
            AND cad.descripcion LIKE '%PROVEE%'
        LEFT JOIN plan_cuentas AS pc ON cad.id_plan_cuenta = pc.id
        LEFT JOIN plan_cuentas_empresa AS pce
            ON cad.id_plan_cuenta = pce.id_plan
            AND pce.id_empresa = p_id_empresa
        WHERE cv.estado != 0

        UNION ALL

        SELECT '' AS aux,
            5 AS orden,
            r.id_asiento_cabecera,
            CONCAT(p.razonsocial, ' # ', r.descripcion, ' Ref: ', co.secuencia_f, ' #Retencion: ', r.id) AS detalle,
            CONCAT(cad.id_plan_cuenta, ' | ', cad.descripcion) AS cuenta,
            CONCAT('ACR-RE ', r.secuencia, ' | Ref: ', r.id) AS Comprobante,
            pce.plan,
            pc.nombre,
            co.id AS idc,
            'RETEN' AS tipo,
            co.fecha,
            r.fecha AS fecha_documento,
            r.nro_comprobante,
            p.id AS id_proveedor,
            p.razonsocial,
            0 AS subtotal_0,
            0 AS subtotal_12,
            0 AS descuento,
            0 AS subtotal,
            0 AS iva_total,
            (r.valor_fuente + r.valor_iva) AS total_final,
            r.id_empresa
        FROM ct_retenciones AS r
        JOIN ct_compras AS co ON r.id_compra = co.id
        JOIN proveedor AS p ON r.id_proveedor = p.id
        LEFT JOIN ct_asientos_detalle AS cad
            ON co.id_asiento_cabecera = cad.id_asiento_cabecera
            AND cad.descripcion LIKE '%PROVEE%'
        LEFT JOIN plan_cuentas AS pc ON cad.id_plan_cuenta = pc.id
        LEFT JOIN plan_cuentas_empresa AS pce
            ON cad.id_plan_cuenta = pce.id_plan
            AND pce.id_empresa = p_id_empresa
        WHERE r.estado != 0

        UNION ALL

        SELECT '' AS aux,
            6 AS orden,
            cem.id_asiento_cabecera,
            CONCAT(p.razonsocial, ' # ', cem.descripcion, ' Ref: ', dcem.id_secuencia, ' #Egreso Masivo: ', dcem.id, ' -- Metodo de pago: ', ccb.nombre) AS detalle,
            CONCAT(cad.id_plan_cuenta, ' | ', cad.descripcion) AS cuenta,
            CONCAT('ACR-M ', cem.secuencia, ' | Ref: ', cem.id) AS Comprobante,
            pce.plan,
            pc.nombre,
            co.id AS idc,
            'EGR-MASI' AS tipo,
            co.fecha,
            cem.fecha_comprobante AS fecha_documento,
            cem.secuencia,
            p.id AS id_proveedor,
            p.razonsocial,
            0 AS subtotal_0,
            0 AS subtotal_12,
            0 AS descuento,
            0 AS subtotal,
            0 AS iva_total,
            dcem.abono,
            cem.id_empresa
        FROM ct_comprobante_egreso_masivo AS cem
        JOIN ct_detalle_comprobante_egreso_masivo AS dcem ON dcem.id_comprobante = cem.id
        JOIN ct_compras AS co ON dcem.id_compra = co.id
        JOIN proveedor AS p ON dcem.id_proveedor = p.id
        JOIN ct_caja_banco AS ccb ON ccb.id = cem.id_caja_banco
        LEFT JOIN ct_asientos_detalle AS cad
            ON co.id_asiento_cabecera = cad.id_asiento_cabecera
            AND cad.descripcion LIKE '%PROVEE%'
        LEFT JOIN plan_cuentas AS pc ON cad.id_plan_cuenta = pc.id
        LEFT JOIN plan_cuentas_empresa AS pce
            ON cad.id_plan_cuenta = pce.id_plan
            AND pce.id_empresa = p_id_empresa
        WHERE cem.estado != 0

        UNION ALL

        SELECT '' AS aux,
            7 AS orden,
            da.id_asiento_cabecera,
            CONCAT(p.razonsocial, ' # ', da.concepto, ' Ref: ', co.secuencia_f, ' #Debito: ', dda.id_debito_acreedores) AS detalle,
            CONCAT(cad.id_plan_cuenta, ' | ', cad.descripcion) AS cuenta,
            CONCAT('ACR-DB ', co.secuencia_factura, ' | Ref: ', da.id) AS Comprobante,
            pce.plan,
            pc.nombre,
            co.id AS idc,
            'DEBITO' AS tipo,
            co.fecha,
            da.fecha_factura AS fecha_documento,
            da.secuencia,
            p.id AS id_proveedor,
            p.razonsocial,
            0 AS subtotal_0,
            0 AS subtotal_12,
            0 AS descuento,
            0 AS subtotal,
            0 AS iva_total,
            dda.total,
            da.id_empresa
        FROM ct_debito_acreedores AS da
        JOIN ct_detalle_debito_acreedores AS dda ON da.id = dda.id_debito_acreedores
        JOIN ct_compras AS co ON dda.id_factura = co.id
        JOIN proveedor AS p ON da.id_proveedor = p.id
        LEFT JOIN ct_asientos_detalle AS cad
            ON co.id_asiento_cabecera = cad.id_asiento_cabecera
            AND cad.descripcion LIKE '%PROVEE%'
        LEFT JOIN plan_cuentas AS pc ON cad.id_plan_cuenta = pc.id
        LEFT JOIN plan_cuentas_empresa AS pce
            ON cad.id_plan_cuenta = pce.id_plan
            AND pce.id_empresa = p_id_empresa
        WHERE da.estado != 0
    ) AS dvp
    WHERE dvp.id_empresa = p_id_empresa
        AND (p_id_proveedor = '' OR dvp.id_proveedor = p_id_proveedor)
        AND dvp.fecha BETWEEN p_desde AND p_hasta
    ORDER BY dvp.idc, dvp.id_proveedor, orden;
END
SQL
        );
    }

    /**
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_proveedores_deudas_pagos');
    }
}
