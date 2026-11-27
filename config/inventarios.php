<?php

return [
    'secciones' => [
        'productos' => ['Productos', 'bi-box-seam'],
        'bodegas' => ['Bodegas', 'bi-building'],
        'existencias' => ['Existencias', 'bi-boxes'],
        'movimientos' => ['Movimientos', 'bi-arrow-left-right'],
        'ajustes' => ['Ajustes', 'bi-sliders'],
        'kardex' => ['Kardex', 'bi-journal-text'],
        'reportes' => ['Reportes', 'bi-bar-chart-line'],
        'categorias' => ['Categorías', 'bi-diagram-3'],
        'plantillas-contables' => ['Plantillas contables', 'bi-journal-bookmark'],
    ],
    'catalogos' => ['productos', 'bodegas', 'categorias', 'plantillas-contables'],
    'cuentas_producto' => [
        'inventario' => ['Inventario', 'activo'],
        'compras' => ['Compras o gasto', 'gasto'],
        'ventas' => ['Ingresos por ventas', 'ingreso'],
        'costo_ventas' => ['Costo de ventas', 'gasto'],
        'devolucion' => ['Devoluciones de ventas', 'ingreso'],
        'ajustes_inventario' => ['Ajustes de inventario', 'gasto'],
    ],
    'tipos_producto' => ['producto' => 'Producto físico', 'servicio' => 'Servicio'],
    'valores_producto' => [
        'precio_venta' => ['Precio de venta de referencia', 1000000, 2],
        'costo_reposicion' => ['Costo de reposición', 1000000, 4],
        'descuento_comercial' => ['Descuento comercial máximo (%)', 100, 2],
        'descuento_financiero' => ['Descuento financiero máximo (%)', 100, 2],
    ],
    'tipos' => [
        'entrada' => 'Entrada',
        'salida' => 'Salida',
        'transferencia' => 'Transferencia',
        'ajuste_entrada' => 'Ajuste de entrada',
        'ajuste_salida' => 'Ajuste de salida',
    ],
];
