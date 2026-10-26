<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
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
                <a href="{{ url('/dashboard') }}" class="small-box-footer">Abrir sistema legado <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-home"></i> Sistema Médico</h3>
                </div>
                <div class="box-body">
                    <p class="lead">AITROL está siendo actualizado a Laravel 13 conservando la navegación y la experiencia del sistema original.</p>
                    <p>Los módulos que aún no han sido migrados permanecen disponibles desde el menú lateral. Al modernizar cada módulo, su acceso será reemplazado aquí sin cambiar el flujo de trabajo habitual.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
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
                <div class="box-header with-border"><h3 class="box-title">Módulos heredados</h3></div>
                <div class="box-body">
                    <p>Continúa trabajando con los módulos operativos mientras son revisados y migrados.</p>
                    <span class="icon"><i class="fa fa-external-link"></i></span>
                </div>
                <div class="box-footer"><a href="{{ url('/dashboard') }}" class="btn btn-warning btn-flat">Abrir legado</a></div>
            </div>
        </div>
    </div>
</x-new-system.layout>
