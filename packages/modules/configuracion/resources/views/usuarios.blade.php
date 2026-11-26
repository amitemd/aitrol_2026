@extends('layouts.adminlte')

@section('title', 'Usuarios')

@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Usuarios', 'subtitulo' => 'Configuraciones generales', 'icono' => 'bi-person-gear'])
    <div class="app-content">
        <div class="container-fluid">
            @if (session('estado'))
                <div class="alert alert-success" role="status">{{ session('estado') }}</div>
            @endif
            @include('partials.listado-modal')
            <section class="card clientes-directorio" data-ajax-list>
                @include('partials.listado-busqueda', ['listadoUrl' => route('configuracion.usuarios.index'), 'placeholder' => 'Nombre o correo electrónico'])
                <div class="small text-body-secondary px-3 py-2 border-bottom">{{ $usuarios->total() }} cuentas registradas</div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="clientes-tabla-cabecera"><tr><th scope="col">Nombre</th><th scope="col">Correo electrónico</th><th scope="col">Tipo de cuenta</th><th scope="col"><span class="visually-hidden">Acciones</span></th></tr></thead>
                        <tbody>
                            @forelse ($usuarios as $usuario)
                                <tr>
                                    <th scope="row">{{ $usuario->name }}</th>
                                    <td>{{ $usuario->email }}</td>
                                    <td><span class="badge rounded-pill text-bg-light">{{ $usuario->is_admin ? 'Administrador' : 'Usuario' }}</span></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('configuracion.usuarios.edit', $usuario) }}" data-ajax-open="Editar usuario" aria-label="Editar {{ $usuario->name }}">
                                            <i class="bi bi-pencil me-1" aria-hidden="true"></i> Editar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-body-secondary p-4">No hay usuarios registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($usuarios->hasPages())
                    <div class="card-footer">{{ $usuarios->links('pagination::bootstrap-5') }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection