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
        'secciones' => [
            'documentos' => ['Lista de ventas', 'bi-receipt-cutoff', 'Nueva venta'],
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
