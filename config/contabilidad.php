<?php

return [
    'secciones' => [
        'asientos' => ['Asientos contables', 'bi-journal-plus', 'Nuevo asiento'],
        'cuentas' => ['Plan de cuentas', 'bi-diagram-3', 'Nueva cuenta'],
        'periodos' => ['Períodos contables', 'bi-calendar-range', 'Nuevo período'],
        'integraciones' => ['Contabilización automática', 'bi-diagram-2', null],
    ],
    'reportes' => [
        'libro-diario' => ['Libro diario', 'bi-journal-text'],
        'mayorizacion' => ['Mayorización', 'bi-journals'],
        'balance-comprobacion' => ['Balance de comprobación', 'bi-list-check'],
        'estado-resultados' => ['Estado de resultados', 'bi-graph-up'],
        'balance-financiero' => ['Estado de situación financiera', 'bi-bar-chart-line'],
        'flujo-caja' => ['Estado de flujo de caja', 'bi-cash-stack'],
        'cambios-patrimonio' => ['Estado de cambios en el patrimonio', 'bi-arrow-left-right'],
    ],
    'clases' => ['activo' => 'Activo', 'pasivo' => 'Pasivo', 'patrimonio' => 'Patrimonio', 'ingreso' => 'Ingreso', 'gasto' => 'Gasto'],
    'flujos' => ['operacion' => 'Operación', 'inversion' => 'Inversión', 'financiacion' => 'Financiación', 'apertura' => 'Saldos iniciales', 'traslado' => 'Traslado interno'],
    'patrimonio' => ['aporte' => 'Aportes', 'retiro' => 'Retiros', 'distribucion' => 'Distribuciones', 'traspaso_resultado' => 'Traspaso de resultados', 'apertura' => 'Saldos iniciales', 'otro' => 'Otros movimientos'],
    'reglas' => [
        'clientes' => ['Cuentas por cobrar', 'activo'], 'proveedores' => ['Cuentas por pagar', 'pasivo'],
        'ventas' => ['Ingresos por ventas', 'ingreso'], 'compras' => ['Compras sin inventario', 'gasto'],
        'impuesto_ventas' => ['Impuesto de ventas', 'pasivo'], 'impuesto_compras' => ['Impuesto de compras', 'activo'],
        'propinas_por_pagar' => ['Propinas por pagar', 'pasivo'],
        'retencion_ventas' => ['Retenciones por cobrar', 'activo'], 'retencion_compras' => ['Retenciones por pagar', 'pasivo'],
        'inventario' => ['Inventario', 'activo'], 'costo_ventas' => ['Costo de ventas', 'gasto'],
        'ajustes_inventario' => ['Contrapartida de ajustes de inventario', 'gasto'],
        'puente_tesoreria' => ['Contrapartida de ingresos y egresos', null],
        'comisiones' => ['Gastos por notas de débito bancarias', 'gasto'], 'abonos' => ['Ingresos por notas de crédito bancarias', 'ingreso'],
        'activo_fijo' => ['Costo de activos fijos', 'activo'], 'pasivo_activo' => ['Contrapartida de altas directas', 'pasivo'],
        'depreciacion' => ['Gasto de depreciación', 'gasto'], 'depreciacion_acumulada' => ['Depreciación acumulada', 'activo'],
        'baja_activo' => ['Pérdida por baja de activos', 'gasto'],
    ],
];
