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
    'empresas' => 'Configuraciones · empresas',
];
