<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-users"></i> Usuarios del sistema</h3>
            <div class="box-tools pull-right">
                <a href="{{ route('new-system.users.create') }}" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> Crear usuario</a>
            </div>
        </div>

        <div class="box-body">
            <form method="GET" action="{{ route('new-system.users.index') }}" class="row">
                <div class="col-md-9">
                    <div class="input-group">
                        <input type="search" name="buscar" value="{{ $search }}" class="form-control" placeholder="Buscar por cédula, correo, nombre o apellido...">
                        <span class="input-group-btn"><button type="submit" class="btn btn-info btn-flat"><i class="fa fa-search"></i> Buscar</button></span>
                    </div>
                </div>
                @if ($search !== '')
                    <div class="col-md-3"><a href="{{ route('new-system.users.index') }}" class="btn btn-default btn-flat"><i class="fa fa-eraser"></i> Limpiar búsqueda</a></div>
                @endif
            </form>

            <hr>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Identificación</th>
                            <th>Rol</th>
                            <th class="text-center">Empresas</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $usuario)
                            <tr>
                                <td><strong>{{ trim($usuario->nombre1.' '.$usuario->nombre2.' '.$usuario->apellido1.' '.$usuario->apellido2) }}</strong><br><small class="text-muted">{{ $usuario->email }}</small></td>
                                <td>{{ $usuario->id }}</td>
                                <td><span class="label {{ (int) $usuario->id_tipo_usuario === 1 ? 'label-primary' : 'label-default' }}">{{ (int) $usuario->id_tipo_usuario === 1 ? 'Superadministrador' : 'Rol '.$usuario->id_tipo_usuario }}</span></td>
                                <td class="text-center"><span class="badge bg-aqua">{{ $usuario->companies_count }}</span></td>
                                <td><span class="label {{ (int) $usuario->estado === 1 ? 'label-success' : 'label-danger' }}">{{ (int) $usuario->estado === 1 ? 'Activo' : 'Inactivo' }}</span></td>
                                <td class="text-center"><a href="{{ route('new-system.users.edit', $usuario) }}" class="btn btn-default btn-xs btn-flat"><i class="fa fa-pencil"></i> Editar</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No se encontraron usuarios.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($users->hasPages())
            <div class="box-footer clearfix">{{ $users->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>
</x-new-system.layout>
