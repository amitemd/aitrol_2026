<?php

return [
    'secciones' => [
        'activos' => ['Registro de activos', 'bi-building-check', 'Nuevo activo'],
        'grupos' => ['Grupos', 'bi-grid', 'Nuevo grupo'],
        'tipos' => ['Tipos', 'bi-folder', 'Nuevo tipo'],
        'subtipos' => ['Subtipos', 'bi-folder2-open', 'Nuevo subtipo'],
        'ubicaciones' => ['Ubicaciones', 'bi-geo-alt', 'Nueva ubicación'],
        'adquisiciones' => ['Adquisiciones', 'bi-receipt', null],
        'movimientos' => ['Movimientos y bajas', 'bi-arrow-left-right', 'Nuevo movimiento'],
        'depreciaciones' => ['Depreciaciones', 'bi-graph-down', 'Registrar depreciación'],
        'ordenes' => ['Órdenes de mantenimiento', 'bi-tools', 'Nueva orden'],
        'reportes' => ['Reportes', 'bi-bar-chart-line', null],
    ],
    'padres' => ['tipos' => 'grupos', 'subtipos' => 'tipos'],
    'catalogos' => ['grupos', 'tipos', 'subtipos', 'ubicaciones'],
    'movimientos' => ['traslado' => 'Traslado', 'asignacion' => 'Asignación de responsable', 'baja' => 'Baja'],
    'estados_orden' => ['pendiente' => 'Pendiente', 'en_proceso' => 'En proceso', 'completada' => 'Completada', 'cancelada' => 'Cancelada'],
];
