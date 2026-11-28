<?php

$ajustes = [
    'notas-credito' => ['Notas de crédito', 'bi-file-earmark-minus', 'Nueva nota de crédito'],
    'notas-debito' => ['Notas de débito', 'bi-file-earmark-plus', 'Nueva nota de débito'],
    'retenciones' => ['Retenciones', 'bi-receipt', 'Nueva retención'],
    'anulaciones' => ['Anulaciones', 'bi-x-circle', null],
    'reportes' => ['Reportes', 'bi-bar-chart-line', null],
];

return [
    'ventas' => [
        'titulo' => 'Ventas', 'icono' => 'bi-cart-check', 'tercero' => 'Cliente',
        'facturacion_electronica' => [
            'ambiente' => env('SRI_AMBIENTE', '1'),
            // Punto anterior, usado durante la migración a series por sucursal.
            'punto_emision' => env('SRI_PUNTO_EMISION', '001'),
            'secuencial_inicial' => env('SRI_SECUENCIAL_INICIAL', '1'),
        ],
        'secciones' => [
            'documentos' => ['Lista de ventas', 'bi-receipt-cutoff', 'Nueva factura'],
            'cotizaciones' => ['Cotizaciones', 'bi-file-earmark-text', 'Nueva cotización'],
            ...$ajustes,
        ],
    ],
    'compras' => [
        'titulo' => 'Compras', 'icono' => 'bi-bag-check', 'tercero' => 'Proveedor',
        'secciones' => [
            'documentos' => ['Lista de compras', 'bi-receipt-cutoff', 'Nueva compra'],
            'ordenes-compra' => ['Órdenes de compra', 'bi-clipboard-check', 'Nueva orden de compra'],
            ...$ajustes,
        ],
    ],
];
