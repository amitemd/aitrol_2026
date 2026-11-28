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
        'marcas' => ['Marcas', 'bi-bookmark'],
        'unidades' => ['Unidades de medida', 'bi-rulers'],
    ],
    'catalogos' => ['productos', 'bodegas', 'categorias', 'plantillas-contables', 'marcas', 'unidades'],
    'cuentas_producto' => [
        'inventario' => ['Inventario', 'activo'],
        'compras' => ['Compras o gasto', 'gasto'],
        'ventas' => ['Ingresos por ventas', 'ingreso'],
        'costo_ventas' => ['Costo de ventas', 'gasto'],
        'devolucion' => ['Devoluciones de ventas', 'ingreso'],
        'ajustes_inventario' => ['Ajustes de inventario', 'gasto'],
    ],
    'tipos_producto' => [
        'producto' => 'Producto inventariable',
        'no_inventariable' => 'Producto no inventariable',
        'servicio' => 'Servicio sin inventario',
    ],
    'valores_producto' => [
        'precio_venta' => ['Precio 1 · Base', 1000000, 2],
        'precio_venta_2' => ['Precio 2', 1000000, 2],
        'precio_venta_3' => ['Precio 3', 1000000, 2],
        'precio_venta_4' => ['Precio 4', 1000000, 2],
        'costo_unitario_referencia' => ['Costo unitario normal', 1000000, 4],
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
