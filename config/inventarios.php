<?php

$camposTrazabilidad = [
    'fecha_vencimiento' => ['Fecha de vencimiento', null],
    'registro_sanitario' => ['Registro sanitario', 120],
    'fabricante' => ['Fabricante', 160],
    'codigo_externo' => ['Código externo', 120],
    'id_producto_externo' => ['ID de producto externo', 120],
    'sistema_externo' => ['Sistema externo', 100],
    'codigo_qr' => ['Código QR', 1024],
];

return [
    'trazabilidad_producto' => $camposTrazabilidad,
    'reglas_trazabilidad_producto' => array_map(
        static fn (array $campo): array => $campo[1] === null
            ? ['nullable', 'date_format:Y-m-d', 'after_or_equal:1900-01-01']
            : ['nullable', 'string', 'max:'.$campo[1]],
        $camposTrazabilidad,
    ),
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
