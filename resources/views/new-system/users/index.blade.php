<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-users"></i> {{ __('new_system.users.system_users') }}</h3>
            <div class="box-tools pull-right">
                <a href="{{ route('new-system.users.create') }}" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> {{ __('new_system.users.create') }}</a>
            </div>
        </div>

        <div class="box-body">
            <form method="GET" action="{{ route('new-system.users.index') }}" class="row">
                <div class="col-md-9">
                    <div class="input-group">
                        <input type="search" name="buscar" value="{{ $search }}" class="form-control" placeholder="{{ __('new_system.users.search_placeholder') }}">
                        <span class="input-group-btn"><button type="submit" class="btn btn-info btn-flat"><i class="fa fa-search"></i> {{ __('new_system.common.search') }}</button></span>
                    </div>
                </div>
                @if ($search !== '')
                    <div class="col-md-3"><a href="{{ route('new-system.users.index') }}" class="btn btn-default btn-flat"><i class="fa fa-eraser"></i> {{ __('new_system.users.clear_search') }}</a></div>
                @endif
            </form>

            <hr>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('new_system.users.user') }}</th>
                            <th>{{ __('new_system.users.identification') }}</th>
                            <th>{{ __('new_system.users.role') }}</th>
                            <th class="text-center">{{ __('new_system.users.companies') }}</th>
                            <th>{{ __('new_system.users.status') }}</th>
                            <th class="text-center">{{ __('new_system.users.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $usuario)
                            <tr>
                                <td><strong>{{ trim($usuario->nombre1.' '.$usuario->nombre2.' '.$usuario->apellido1.' '.$usuario->apellido2) }}</strong><br><small class="text-muted">{{ $usuario->email }}</small></td>
                                <td>{{ $usuario->id }}</td>
                                <td><span class="label {{ (int) $usuario->id_tipo_usuario === 1 ? 'label-primary' : 'label-default' }}">{{ (int) $usuario->id_tipo_usuario === 1 ? __('new_system.users.superadministrator') : __('new_system.users.role_number', ['number' => $usuario->id_tipo_usuario]) }}</span></td>
                                <td class="text-center"><span class="badge bg-aqua">{{ $usuario->companies_count }}</span></td>
                                <td><span class="label {{ (int) $usuario->estado === 1 ? 'label-success' : 'label-danger' }}">{{ (int) $usuario->estado === 1 ? __('new_system.common.active') : __('new_system.common.inactive') }}</span></td>
                                <td class="text-center"><a href="{{ route('new-system.users.edit', $usuario) }}" class="btn btn-default btn-xs btn-flat"><i class="fa fa-pencil"></i> {{ __('new_system.common.edit') }}</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">{{ __('new_system.users.empty') }}</td></tr>
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
