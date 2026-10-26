<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    @php
        $dashboardUserName = trim(implode(' ', array_filter([$newSystemUser->nombre1, $newSystemUser->apellido1])));
        $dashboardCompanyName = $newSystemCurrentCompany?->nombrecomercial ?? $newSystemCompanies->first()?->nombrecomercial ?? 'AITROL';
    @endphp

    <section class="aitrol-welcome">
        <div class="aitrol-welcome__content">
            <p class="aitrol-welcome__eyebrow">Plataforma Laravel 13</p>
            <h2>Bienvenido, {{ $dashboardUserName }}</h2>
            <p>Continúa trabajando en {{ $dashboardCompanyName }} con la navegación completa del sistema. Los módulos conservan sus permisos, rutas y operación mientras los modernizamos uno por uno.</p>
        </div>
        <a href="#modulos-principales" class="aitrol-welcome__action">Ver accesos principales <i class="fa fa-arrow-down"></i></a>
    </section>

    @if ($newSystemCurrentCompany === null && $newSystemCompanies->count() > 1)
        <div class="alert alert-warning">
            <h4><i class="icon fa fa-warning"></i> Selecciona la empresa de trabajo</h4>
            Los datos de cada módulo se filtrarán utilizando la empresa activa.
            <a href="{{ route('new-system.companies.index') }}" class="btn btn-warning btn-sm pull-right">Seleccionar empresa</a>
            <div class="clearfix"></div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua new-system-stat">
                <div class="inner">
                    <h3>{{ number_format($stats['activeUsers']) }}</h3>
                    <p>Usuarios activos</p>
                </div>
                <div class="icon"><i class="fa fa-users"></i></div>
                <span class="small-box-footer">Sistema compartido <i class="fa fa-check-circle"></i></span>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green new-system-stat">
                <div class="inner">
                    <h3>{{ number_format($stats['companies']) }}</h3>
                    <p>Empresas activas</p>
                </div>
                <div class="icon"><i class="fa fa-building"></i></div>
                <a href="{{ route('new-system.companies.index') }}" class="small-box-footer">Seleccionar empresa <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow new-system-stat">
                <div class="inner">
                    <h3>{{ number_format($stats['companyUsers']) }}</h3>
                    <p>Usuarios en empresa</p>
                </div>
                <div class="icon"><i class="fa fa-user-md"></i></div>
                @can('manage-new-system-users')
                    <a href="{{ route('new-system.users.index') }}" class="small-box-footer">Administrar usuarios <i class="fa fa-arrow-circle-right"></i></a>
                @else
                    <span class="small-box-footer">Empresa seleccionada <i class="fa fa-check-circle"></i></span>
                @endcan
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red new-system-stat">
                <div class="inner">
                    <h3>{{ number_format($stats['legacyRoutes']) }}</h3>
                    <p>Rutas integradas</p>
                </div>
                <div class="icon"><i class="fa fa-exchange"></i></div>
                <span class="small-box-footer">Disponibles en el menú lateral <i class="fa fa-check-circle"></i></span>
            </div>
        </div>
    </div>

    <div class="row" id="modulos-principales">
        <div class="col-md-4">
            <div class="box box-info new-system-module-box">
                <div class="box-header with-border"><h3 class="box-title">Empresas</h3></div>
                <div class="box-body">
                    <p>Selecciona el contexto de trabajo que comparten la plataforma nueva y el sistema legado.</p>
                    <span class="icon"><i class="fa fa-building"></i></span>
                </div>
                <div class="box-footer"><a href="{{ route('new-system.companies.index') }}" class="btn btn-info btn-flat">Abrir módulo</a></div>
            </div>
        </div>
        @can('manage-new-system-users')
            <div class="col-md-4">
                <div class="box box-success new-system-module-box">
                    <div class="box-header with-border"><h3 class="box-title">Usuarios</h3></div>
                    <div class="box-body">
                        <p>Administra accesos, roles, estado y empresas asignadas con la misma información histórica.</p>
                        <span class="icon"><i class="fa fa-users"></i></span>
                    </div>
                    <div class="box-footer"><a href="{{ route('new-system.users.index') }}" class="btn btn-success btn-flat">Abrir módulo</a></div>
                </div>
            </div>
        @endcan
        <div class="col-md-4">
            <div class="box box-warning new-system-module-box">
                <div class="box-header with-border"><h3 class="box-title">Operación completa</h3></div>
                <div class="box-body">
                    <p>Todos los accesos autorizados del sistema original están disponibles en el menú lateral.</p>
                    <span class="icon"><i class="fa fa-external-link"></i></span>
                </div>
                <div class="box-footer"><span class="text-muted"><i class="fa fa-bars"></i> Selecciona una opción en el menú lateral</span></div>
            </div>
        </div>
    </div>
</x-new-system.layout>
