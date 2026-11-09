@php
    $hospitalSection = 'Módulo hospitalario';
    $hospitalModuleId = Request::segment(4);
    $hospitalModuleNames = [
        '2' => 'Emergencia',
        '3' => 'Hospitalización',
        '4' => 'Imágenes',
        '5' => 'Cirugía',
        '6' => 'UCI',
        '7' => 'UCIN',
    ];

    if (Request::is('hospital/modulos/index/*') && isset($hospitalModuleNames[$hospitalModuleId])) {
        $hospitalSection = $hospitalModuleNames[$hospitalModuleId];
    } elseif (Request::is('hospital/master_farmacia*') || Request::is('hospital/farmacia*')
        || Request::is('hospital/producto*') || Request::is('hospital/proveedore*')
        || Request::is('hospital/marcas*') || Request::is('hospital/tipoproducto*')
        || Request::is('hospital/planilla*')) {
        $hospitalSection = 'Farmacia';
    } elseif (Request::is('hospital/configuracion/servicios*')) {
        $hospitalSection = 'Configuración de servicios';
    } elseif (Request::is('hospital/laboratorio*')) {
        $hospitalSection = 'Laboratorio';
    } elseif (Request::is('hospital/banco-sangre*')) {
        $hospitalSection = 'Banco de sangre';
    } elseif (Request::is('hospital/interconsultas-oncologicas*')) {
        $hospitalSection = 'Interconsultas oncológicas';
    } elseif (Request::is('hospital/gestioncuartos*')) {
        $hospitalSection = 'Gestión de cuartos';
    } elseif (Request::is('hospital/admin/gestionh*')) {
        $hospitalSection = 'Gestión de habitaciones';
    } elseif (Request::is('hospital/hospitalizacion*') || Request::is('hospital/habitacion*')
        || Request::is('hospital/formulario_005*') || Request::is('hospital/detalles*')) {
        $hospitalSection = 'Hospitalización';
    } elseif (Request::is('hospital/quirofano*')) {
        $hospitalSection = 'Cirugía';
    } elseif (Request::is('hospital/uci*')) {
        $hospitalSection = 'UCI';
    } elseif (Request::is('hospital/ucin*')) {
        $hospitalSection = 'UCIN';
    } elseif (Request::is('hospital/imagen*')) {
        $hospitalSection = 'Imágenes';
    } elseif (Request::is('hospital/emergencia*') || Request::is('hospital/prioridademergencia*')
        || Request::is('hospital/tipoemergencia*')) {
        $hospitalSection = 'Emergencia';
    } elseif (Request::is('hospital/admision*')) {
        $hospitalSection = 'Admisión hospitalaria';
    }
@endphp

<nav class="hospital-shared-breadcrumb" aria-label="Navegación del área médica" data-tour="hospital-navigation">
    <a href="{{ route('hospital.index') }}" title="Regresar al área médica">
        <i class="fa fa-arrow-left" aria-hidden="true"></i>
        <span>Área médica</span>
    </a>
    <i class="fa fa-angle-right hospital-shared-breadcrumb__separator" aria-hidden="true"></i>
    <span class="hospital-shared-breadcrumb__current">{{ $hospitalSection }}</span>
</nav>
