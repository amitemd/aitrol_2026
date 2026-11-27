<?php

return [
    'destinos' => [
        'dashboard' => 'Panel general',
        'clientes.index' => 'Lista de clientes',
        'clientes.create' => 'Nuevo cliente',
        'proveedores.index' => 'Lista de proveedores',
        'proveedores.create' => 'Nuevo proveedor',
        ...array_combine(
            array_map(fn (string $seccion): string => 'inventarios.'.$seccion.'.index', array_keys((require __DIR__.'/inventarios.php')['secciones'])),
            array_column((require __DIR__.'/inventarios.php')['secciones'], 0),
        ),
        'swagger.index' => 'Documentación API',
        'configuracion.usuarios.index' => 'Usuarios',
        'configuracion.empresas.index' => 'Empresas',
        'configuracion.roles.index' => 'Roles y permisos',
        'configuracion.ubicaciones.index' => 'Ubicaciones',
    ],
];
