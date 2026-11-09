<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateClientDebtPaymentProcedure extends Migration
{
    /**
     * Instala el procedimiento usado por el reporte V3 Deudas vs Pagos de clientes.
     * DELIMITER, USE y DEFINER son instrucciones del cliente SQL y no deben
     * incluirse en una migracion portable.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_clientes_deudas_pagos');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_clientes_deudas_pagos(
    IN p_id_empresa VARCHAR(13),
    IN p_desde VARCHAR(255),
    IN p_hasta VARCHAR(255),
    IN p_cliente VARCHAR(255)
)
BEGIN
    SELECT *,
        CASE WHEN dvp.tipo IN ('FAC', 'ND') THEN dvp.total_final ELSE 0 END AS deuda,
        CASE WHEN dvp.tipo NOT IN ('FAC', 'ND') THEN dvp.total_final ELSE 0 END AS abono,
        SUM(CASE WHEN dvp.tipo IN ('FAC', 'ND') THEN dvp.total_final ELSE -dvp.total_final END)
            OVER (PARTITION BY dvp.id_empresa ORDER BY dvp.id, dvp.identificacion, orden) AS saldo,
        SUM(CASE WHEN dvp.tipo IN ('FAC', 'ND') THEN dvp.total_final ELSE 0 END)
            OVER (PARTITION BY dvp.id_empresa ORDER BY dvp.id, dvp.identificacion, orden) AS total_deuda,
        SUM(CASE WHEN dvp.tipo NOT IN ('FAC', 'ND') THEN dvp.total_final ELSE 0 END)
            OVER (PARTITION BY dvp.id_empresa ORDER BY dvp.id, dvp.identificacion, orden) AS total_abonos
    FROM (
        SELECT 0 AS orden,
            CONCAT('FACT #', v.nro_comprobante, ' | Ref: ', v.id) AS detalle,
            IFNULL(v.concepto, 'No existe concepto') AS concepto,
            'FAC' AS tipo,
            v.id,
            v.fecha,
            v.fecha AS fecha_documento,
            v.nro_comprobante,
            c.identificacion,
            c.nombre,
            v.id_asiento,
            CONCAT(pe.plan, '|', p.nombre) AS detalle_cuenta,
            v.subtotal_0,
            v.subtotal_12,
            v.descuento,
            v.iva_total,
            v.total_final,
            v.id_empresa
        FROM ct_clientes AS c
        JOIN ct_ventas AS v ON c.identificacion = v.id_cliente
        JOIN ct_asientos_detalle AS ad ON v.id_asiento = ad.id_asiento_cabecera
        JOIN plan_cuentas_empresa AS pe
            ON ad.id_plan_cuenta = pe.id_plan
            AND pe.id_empresa = p_id_empresa
            AND pe.nombre LIKE '%CUENTA%'
            AND pe.plan LIKE '1.%'
        JOIN plan_cuentas AS p ON ad.id_plan_cuenta = p.id
        WHERE v.estado != 0
            AND v.tipo NOT IN ('N-D', 'N-C')

        UNION ALL

        SELECT 1 AS orden,
            CONCAT('RET #', r.nro_comprobante, ' | Ref: ', r.id) AS detalle,
            IFNULL(r.descripcion, 'No existe concepto') AS concepto,
            'RET' AS tipo,
            v.id,
            v.fecha,
            r.fecha AS fecha_documento,
            r.nro_comprobante,
            c.identificacion,
            c.nombre,
            r.id_asiento_cabecera,
            CONCAT(pe.plan, '|', p.nombre) AS detalle_cuenta,
            0 AS subtotal_0,
            0 AS subtotal_12,
            0 AS descuento,
            0 AS iva_total,
            (r.valor_fuente + r.valor_iva) AS total_final,
            r.id_empresa
        FROM ct_cliente_retencion AS r
        JOIN ct_ventas AS v ON r.id_factura = v.id
        JOIN ct_clientes AS c ON r.id_cliente = c.identificacion
        JOIN ct_asientos_detalle AS ad ON r.id_asiento_cabecera = ad.id_asiento_cabecera
        JOIN plan_cuentas_empresa AS pe
            ON ad.id_plan_cuenta = pe.id_plan
            AND pe.id_empresa = p_id_empresa
            AND pe.nombre NOT LIKE 'CUENTAS%'
        LEFT JOIN plan_cuentas AS p ON ad.id_plan_cuenta = p.id
        WHERE r.estado != 0
        GROUP BY r.id

        UNION ALL

        SELECT 2 AS orden,
            CONCAT('COM-IN #', ci.secuencia, ' | Ref: ', ci.id) AS detalle,
            IFNULL(ci.observaciones, 'No existe concepto') AS concepto,
            'COMP-ING' AS tipo,
            v.id,
            v.fecha,
            ci.fecha AS fecha_documento,
            ci.secuencia,
            c.identificacion,
            c.nombre,
            ci.id_asiento_cabecera,
            CONCAT(pe.plan, '|', p.nombre) AS detalle_cuenta,
            0 AS subtotal_0,
            0 AS subtotal_12,
            0 AS descuento,
            0 AS iva_total,
            dci.total,
            ci.id_empresa
        FROM ct_detalle_comprobante_ingreso AS dci
        JOIN ct_comprobante_ingreso AS ci ON dci.id_comprobante = ci.id
        JOIN ct_ventas AS v ON dci.id_factura = v.id
        JOIN ct_clientes AS c ON ci.id_cliente = c.identificacion
        JOIN ct_asientos_detalle AS ad ON ci.id_asiento_cabecera = ad.id_asiento_cabecera
        JOIN plan_cuentas_empresa AS pe
            ON ad.id_plan_cuenta = pe.id_plan
            AND pe.id_empresa = p_id_empresa
            AND pe.nombre LIKE '%CAJA%'
        LEFT JOIN plan_cuentas AS p ON ad.id_plan_cuenta = p.id
        WHERE ci.estado != 0

        UNION ALL

        SELECT 3 AS orden,
            CONCAT('CR-V #', cv.secuencia, ' | Ref: ', cv.id) AS detalle,
            IFNULL(cv.observacion, 'No existe concepto') AS concepto,
            'CRUCE-VAL' AS tipo,
            v.id,
            v.fecha,
            cv.fecha_pago AS fecha_documento,
            cv.secuencia,
            c.identificacion,
            c.nombre,
            cv.id_asiento_cabecera,
            CONCAT(pe.plan, '|', p.nombre) AS detalle_cuenta,
            0 AS subtotal_0,
            0 AS subtotal_12,
            0 AS descuento,
            0 AS iva_total,
            dc.total,
            cv.id_empresa
        FROM ct_detalle_cruce_clientes AS dc
        JOIN ct_cruce_valores_cliente AS cv ON dc.id_comprobante = cv.id
        JOIN ct_ventas AS v ON dc.id_factura = v.id
        JOIN ct_clientes AS c ON cv.id_cliente = c.identificacion
        JOIN ct_asientos_detalle AS ad ON cv.id_asiento_cabecera = ad.id_asiento_cabecera
        JOIN plan_cuentas_empresa AS pe
            ON ad.id_plan_cuenta = pe.id_plan
            AND pe.id_empresa = p_id_empresa
            AND pe.nombre LIKE '%CUENTAS%'
        LEFT JOIN plan_cuentas AS p ON ad.id_plan_cuenta = p.id
        WHERE cv.estado != 0

        UNION ALL

        SELECT 4 AS orden,
            CONCAT('CR-C #', ccc.secuencia, ' | Ref: ', ccc.id) AS detalle,
            IFNULL(ccc.detalle, 'No existe concepto') AS concepto,
            'CRUCE-CUE' AS tipo,
            v.id,
            v.fecha,
            ccc.fecha AS fecha_documento,
            ccc.secuencia,
            c.identificacion,
            c.nombre,
            ccc.id_asiento_cabecera,
            CONCAT(pe.plan, '|', p.nombre) AS detalle_cuenta,
            0 AS subtotal_0,
            0 AS subtotal_12,
            0 AS descuento,
            0 AS iva_total,
            ccc.total,
            ccc.id_empresa
        FROM ct_detalle_cruce_cuentas_clientes AS dcc
        JOIN ct_cruce_cuentas_clientes AS ccc ON dcc.id_comprobante = ccc.id
        JOIN ct_ventas AS v ON ccc.id_factura = v.id
        JOIN ct_clientes AS c ON ccc.id_cliente = c.identificacion
        JOIN ct_asientos_detalle AS ad ON ccc.id_asiento_cabecera = ad.id_asiento_cabecera
        JOIN plan_cuentas_empresa AS pe
            ON ad.id_plan_cuenta = pe.id_plan
            AND pe.id_empresa = p_id_empresa
            AND pe.nombre LIKE '%CUENTAS%'
            AND pe.plan LIKE '1.%'
        LEFT JOIN plan_cuentas AS p ON ad.id_plan_cuenta = p.id
        WHERE ccc.estado != 0

        UNION ALL

        SELECT 5 AS orden,
            CONCAT('NC #', nc.nro_comprobante, ' | Ref: ', nc.id) AS detalle,
            IFNULL(nc.observacion, 'No existe concepto') AS concepto,
            'NC' AS tipo,
            IFNULL(v.id, nc.id_factura) AS id,
            IFNULL(v.fecha, vn.fecha) AS fecha,
            nc.fecha AS fecha_documento,
            nc.nro_comprobante,
            c.identificacion,
            c.nombre,
            nc.id_asiento_cabecera,
            CONCAT(pe.plan, '|', p.nombre) AS detalle_cuenta,
            0 AS subtotal_0,
            0 AS subtotal_12,
            0 AS descuento,
            0 AS iva_total,
            nc.total_credito,
            nc.id_empresa
        FROM ct_nota_credito_clientes AS nc
        LEFT JOIN ct_devolucion_productos AS dp ON nc.id = dp.id_nota_credito
        LEFT JOIN ct_detalle_credito_clientes AS dnc ON nc.id = dnc.id_not_cred
        LEFT JOIN ct_ventas AS v ON dnc.id_factura = v.id
        LEFT JOIN ct_ventas AS vn ON nc.id_factura = vn.id
        JOIN ct_clientes AS c ON nc.id_cliente = c.identificacion
        LEFT JOIN ct_asientos_detalle AS ad
            ON nc.id_asiento_cabecera = ad.id_asiento_cabecera
            AND ad.descripcion LIKE '%CUENTAS%'
        LEFT JOIN plan_cuentas_empresa AS pe
            ON ad.id_plan_cuenta = pe.id_plan
            AND pe.id_empresa = nc.id_empresa
        LEFT JOIN plan_cuentas AS p ON ad.id_plan_cuenta = p.id
        WHERE nc.estado != 0
        GROUP BY nc.id

        UNION ALL

        SELECT 6 AS orden,
            CONCAT('ND #', v.nro_comprobante, ' | Ref: ', v.id) AS detalle,
            IFNULL(v.concepto, 'No existe concepto') AS concepto,
            'ND' AS tipo,
            v.id,
            v.fecha,
            v.fecha AS fecha_documento,
            v.nro_comprobante,
            c.identificacion,
            c.nombre,
            v.id_asiento,
            CONCAT(pe.plan, '|', p.nombre) AS detalle_cuenta,
            v.subtotal_0,
            v.subtotal_12,
            v.descuento,
            v.iva_total,
            v.total_final,
            v.id_empresa
        FROM ct_clientes AS c
        JOIN ct_ventas AS v ON c.identificacion = v.id_cliente
        JOIN ct_asientos_detalle AS ad ON v.id_asiento = ad.id_asiento_cabecera
        JOIN plan_cuentas_empresa AS pe
            ON ad.id_plan_cuenta = pe.id_plan
            AND pe.id_empresa = p_id_empresa
            AND pe.nombre LIKE '%CUENTAS POR COBRAR%'
        LEFT JOIN plan_cuentas AS p ON ad.id_plan_cuenta = p.id
        WHERE v.estado != 0
            AND v.tipo = 'N-D'
    ) AS dvp
    WHERE dvp.id_empresa = p_id_empresa
        AND dvp.fecha BETWEEN p_desde AND p_hasta
        AND (p_cliente = '' OR dvp.identificacion = p_cliente)
    ORDER BY dvp.id, orden, dvp.identificacion;
END
SQL
        );
    }

    /**
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_clientes_deudas_pagos');
    }
}
