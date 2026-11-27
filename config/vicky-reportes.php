<?php

$fuente = static function (
    string $tabla,
    string $titulo,
    string $permiso,
    string $columnas,
    ?string $fecha = null,
    array $numeros = [],
    array $condiciones = [],
    string $orden = 'id',
): array {
    return compact('tabla', 'titulo', 'permiso', 'fecha', 'numeros', 'condiciones', 'orden')
        + ['columnas' => explode(' ', $columnas)];
};

return [
    'clientes' => $fuente('erp_clientes', 'Clientes', 'clientes.lista', 'nombre tipo_identificacion activo created_at identificacion email telefono direccion', 'created_at', condiciones: ['deleted_at' => null])
        + ['listado' => ['nombre', 'tipo_identificacion', 'activo', 'created_at']],
    'proveedores' => $fuente('erp_proveedores', 'Proveedores', 'proveedores.lista', 'nombre tipo_identificacion activo created_at identificacion email telefono direccion', 'created_at', condiciones: ['deleted_at' => null])
        + ['listado' => ['nombre', 'tipo_identificacion', 'activo', 'created_at']],
    'pacientes' => $fuente('vicky_pacientes', 'Registro de pacientes', 'pacientes.reportes', 'codigo nombre estado fecha_registro', 'fecha_registro', orden: 'codigo'),
    'sucursales' => $fuente('erp_sucursales', 'Sucursales', 'empresas', 'codigo nombre direccion created_at', 'created_at'),

    'facturas_venta' => $fuente('com_documentos', 'Documentos de venta vigentes', 'ventas.reportes.index', 'fecha numero tercero tipo subtotal impuesto total', 'fecha',
        ['subtotal' => 2, 'impuesto' => 2, 'total' => 2], ['modulo' => 'ventas', 'tipo' => 'documentos', 'anulado_at' => null]),
    'facturas_compra' => $fuente('com_documentos', 'Documentos de compra vigentes', 'compras.reportes.index', 'fecha numero tercero tipo subtotal impuesto total', 'fecha',
        ['subtotal' => 2, 'impuesto' => 2, 'total' => 2], ['modulo' => 'compras', 'tipo' => 'documentos', 'anulado_at' => null]),
    'cotizaciones' => $fuente('com_documentos', 'Cotizaciones vigentes', 'ventas.cotizaciones.index', 'fecha numero tercero subtotal impuesto total', 'fecha',
        ['subtotal' => 2, 'impuesto' => 2, 'total' => 2], ['modulo' => 'ventas', 'tipo' => 'cotizaciones', 'anulado_at' => null]),
    'ordenes_compra' => $fuente('com_documentos', 'Órdenes de compra vigentes', 'compras.ordenes-compra.index', 'fecha numero tercero subtotal impuesto total', 'fecha',
        ['subtotal' => 2, 'impuesto' => 2, 'total' => 2], ['modulo' => 'compras', 'tipo' => 'ordenes-compra', 'anulado_at' => null]),
    'lineas_venta' => $fuente('com_lineas', 'Detalle de documentos de venta vigentes', 'ventas.reportes.index', 'fecha numero tercero descripcion cantidad subtotal impuesto total', 'fecha',
        ['cantidad' => 3, 'subtotal' => 2, 'impuesto' => 2, 'total' => 2]),
    'lineas_compra' => $fuente('com_lineas', 'Detalle de documentos de compra vigentes', 'compras.reportes.index', 'fecha numero tercero descripcion cantidad subtotal impuesto total', 'fecha',
        ['cantidad' => 3, 'subtotal' => 2, 'impuesto' => 2, 'total' => 2]),

    'productos' => $fuente('inv_productos', 'Productos y servicios', 'inventarios.productos.index', 'codigo nombre unidad categoria tipo_producto marca modelo activo stock_minimo precio_venta created_at', 'created_at', ['stock_minimo' => 3, 'precio_venta' => 2]),
    'bodegas' => $fuente('inv_bodegas', 'Bodegas', 'inventarios.bodegas.index', 'codigo nombre activo created_at', 'created_at'),
    'categorias' => $fuente('inv_categorias', 'Categorías de inventario', 'inventarios.categorias.index', 'codigo nombre activo created_at', 'created_at'),
    'inventario_movimientos' => $fuente('inv_operaciones', 'Movimientos de inventario', 'inventarios.movimientos.index', 'tipo cantidad costo_unitario referencia created_at', 'created_at', ['cantidad' => 3, 'costo_unitario' => 4]),
    'kardex' => $fuente('inv_kardex', 'Kardex de inventario', 'inventarios.kardex.index', 'id_producto id_bodega variacion saldo_anterior saldo costo_unitario costo_promedio',
        numeros: ['variacion' => 3, 'saldo_anterior' => 3, 'saldo' => 3, 'costo_unitario' => 4, 'costo_promedio' => 4]),
    'existencias' => $fuente('inv_existencias', 'Existencias por producto y bodega', 'inventarios.existencias.index', 'producto bodega cantidad costo_promedio', numeros: ['cantidad' => 3, 'costo_promedio' => 4], orden: 'id_producto'),

    'tesoreria_movimientos' => $fuente('cb_operaciones', 'Movimientos de caja y bancos', 'caja-bancos.reportes.index', 'fecha tipo moneda importe flujo_contable', 'fecha', ['importe' => 2]),
    'tesoreria_cierres' => $fuente('cb_cierres', 'Cierres de caja', 'caja-bancos.cierres.index', 'moneda saldo_apertura saldo_cierre efectivo_contado diferencia cerrado_at', 'cerrado_at',
        ['saldo_apertura' => 2, 'saldo_cierre' => 2, 'efectivo_contado' => 2, 'diferencia' => 2]),
    'tesoreria_extractos' => $fuente('cb_extractos', 'Extractos bancarios', 'caja-bancos.extractos.index', 'importadas omitidas created_at', 'created_at', ['importadas' => 0, 'omitidas' => 0]),

    'asientos_contables' => $fuente('co_asientos', 'Asientos contables', 'contabilidad.asientos.index', 'numero fecha flujo origen total', 'fecha', ['total' => 2]),
    'lineas_contables' => $fuente('co_lineas', 'Detalle de asientos contables', 'contabilidad.asientos.index', 'id_asiento id_cuenta debe haber movimiento_patrimonio', numeros: ['debe' => 2, 'haber' => 2]),
    'cuentas_contables' => $fuente('co_cuentas', 'Plan de cuentas contables', 'contabilidad.cuentas.index', 'codigo nombre clase auxiliar efectivo activo'),
    'periodos_contables' => $fuente('co_periodos', 'Períodos contables', 'contabilidad.periodos.index', 'nombre desde hasta cerrado_at', 'desde'),

    'activos_fijos' => $fuente('af_activos', 'Activos fijos', 'activos-fijos.activos.index', 'codigo nombre marca modelo fecha_adquisicion fecha_baja costo depreciado valor_residual vida_util_meses', 'fecha_adquisicion',
        ['costo' => 2, 'depreciado' => 2, 'valor_residual' => 2, 'vida_util_meses' => 0]),
    'movimientos_activos' => $fuente('af_movimientos', 'Movimientos de activos fijos', 'activos-fijos.movimientos.index', 'id_activo tipo fecha', 'fecha'),
    'depreciaciones' => $fuente('af_depreciaciones', 'Depreciaciones', 'activos-fijos.depreciaciones.index', 'periodo importe acumulado valor_libros', numeros: ['importe' => 2, 'acumulado' => 2, 'valor_libros' => 2]),
    'mantenimiento' => $fuente('af_ordenes', 'Órdenes de mantenimiento', 'activos-fijos.ordenes.index', 'tipo estado fecha_programada finalizada_at costo_mantenimiento', 'fecha_programada', ['costo_mantenimiento' => 2]),

    'declaraciones' => $fuente('tr_declaraciones', 'Declaraciones tributarias', 'tributario.declaraciones.index', 'formato desde hasta estado fecha_externa', 'desde'),
    'documentos_tributarios' => $fuente('tr_documentos', 'Respaldo tributario', 'tributario.documentos.index', 'tipo nombre extension bytes created_at', 'created_at', ['bytes' => 0]),
];
