<?php

$destinosComerciales = [];
foreach (require __DIR__.'/comercial.php' as $modulo => $datos) {
    foreach ($datos['secciones'] as $seccion => [$titulo]) {
        $destinosComerciales[$modulo.'.'.$seccion.'.index'] = $datos['titulo'].' · '.$titulo;
    }
}
$destinosActivos = [];
foreach ((require __DIR__.'/activos-fijos.php')['secciones'] as $seccion => [$titulo]) {
    $destinosActivos['activos-fijos.'.$seccion.'.index'] = 'Activos fijos · '.$titulo;
}

return [
    'destinos' => [
        ...$destinosComerciales,
        ...$destinosActivos,
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
