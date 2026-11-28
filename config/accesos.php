<?php

$accesosComerciales = [];
foreach (require __DIR__.'/comercial.php' as $modulo => $datos) {
    $accesosComerciales[$modulo] = $datos['titulo'].' · todo el módulo';
    foreach ($datos['secciones'] as $seccion => [$titulo]) {
        $accesosComerciales[$modulo.'.'.$seccion.'.index'] = $datos['titulo'].' · '.$titulo;
    }
}
$accesosActivos = ['activos-fijos' => 'Activos fijos · todo el módulo'];
foreach ((require __DIR__.'/activos-fijos.php')['secciones'] as $seccion => [$titulo]) {
    $accesosActivos['activos-fijos.'.$seccion.'.index'] = 'Activos fijos · '.$titulo;
}

return [
    'rrhh' => 'Recursos humanos · todo el módulo',
    ...array_combine(
        array_map(fn (string $seccion): string => 'rrhh.'.$seccion.'.index', array_keys((require __DIR__.'/rrhh.php')['secciones'])),
        array_map(fn (array $opcion): string => 'Recursos humanos · '.$opcion[0], (require __DIR__.'/rrhh.php')['secciones']),
    ),
    'auditoria.logs.index' => 'Auditoría · Bitácora',
    'tributario' => 'Tributario · todo el módulo',
    ...array_combine(
        array_map(fn ($s) => 'tributario.'.$s.'.index', array_keys((require __DIR__.'/tributario.php')['secciones'])),
        array_map(fn ($d) => 'Tributario · '.$d[0], (require __DIR__.'/tributario.php')['secciones']),
    ),
    'contabilidad' => 'Contabilidad · todo el módulo',
    'contabilidad.reportes' => 'Contabilidad · todos los reportes',
    ...array_combine(
        array_map(fn ($s) => 'contabilidad.'.$s.'.index', array_keys((require __DIR__.'/contabilidad.php')['secciones'])),
        array_map(fn ($d) => 'Contabilidad · '.$d[0], (require __DIR__.'/contabilidad.php')['secciones']),
    ),
    ...array_combine(
        array_map(fn ($s) => 'contabilidad.reportes.'.$s, array_keys((require __DIR__.'/contabilidad.php')['reportes'])),
        array_map(fn ($d) => 'Contabilidad · '.$d[0], (require __DIR__.'/contabilidad.php')['reportes']),
    ),
    'caja-bancos' => 'Caja y bancos · todo el módulo',
    ...array_combine(
        array_map(fn ($seccion) => 'caja-bancos.'.$seccion.'.index', array_keys((require __DIR__.'/caja-bancos.php')['secciones'])),
        array_map(fn ($datos) => 'Caja y bancos · '.$datos[0], (require __DIR__.'/caja-bancos.php')['secciones']),
    ),
    ...$accesosComerciales,
    'compras.aprobaciones.index' => 'Compras · Aprobar o rechazar órdenes',
    ...$accesosActivos,
    'clientes' => 'Clientes · todo el módulo',
    'clientes.lista' => 'Clientes · lista',
    'pacientes.reportes' => 'Pacientes · reportes operativos',
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
