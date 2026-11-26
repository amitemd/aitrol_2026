<?php

return [
    'clientes' => 'Clientes · todo el módulo',
    'clientes.lista' => 'Clientes · lista',
    'clientes.documentos' => 'Clientes · todos los documentos',
    ...array_map(fn (string $titulo): string => 'Clientes · '.$titulo, (require __DIR__.'/clientes.php')['secciones']),
    'clientes.reportes' => 'Clientes · todos los reportes',
    'empresas' => 'Configuraciones · empresas',
];