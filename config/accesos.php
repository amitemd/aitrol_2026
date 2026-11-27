<?php

return [
    'clientes' => 'Clientes · todo el módulo',
    'clientes.lista' => 'Clientes · lista',
    'clientes.documentos' => 'Clientes · todos los documentos',
    ...array_map(fn (string $titulo): string => 'Clientes · '.$titulo, (require __DIR__.'/clientes.php')['secciones']),
    'clientes.reportes' => 'Clientes · todos los reportes',
    'proveedores' => 'Proveedores · todo el módulo',
    'proveedores.lista' => 'Proveedores · lista',
    'proveedores.documentos' => 'Proveedores · todos los documentos',
    'proveedores.reportes' => 'Proveedores · todos los reportes',
    ...array_map(fn (string $titulo): string => 'Proveedores · '.$titulo, (require __DIR__.'/proveedores.php')['secciones']),
    'inventarios' => 'Inventarios · todo el módulo',
    ...array_combine(
        array_map(fn (string $seccion): string => 'inventarios.'.$seccion.'.index', array_keys((require __DIR__.'/inventarios.php')['secciones'])),
        array_map(fn (array $seccion): string => 'Inventarios · '.$seccion[0], (require __DIR__.'/inventarios.php')['secciones']),
    ),
    'empresas' => 'Configuraciones · empresas',
];
