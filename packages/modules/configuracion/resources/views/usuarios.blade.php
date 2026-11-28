@extends('layouts.adminlte')

@section('title', __('Usuarios'))

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => 'Usuarios', 'subtitulo' => 'Configuración', 'icono' => 'bi-person-gear',
        'crearUrl' => route('configuracion.usuarios.create'), 'crearTexto' => 'Nuevo usuario',
    ])
    <div class="app-content">
        <div class="container-fluid">
            @can('administrar-personas')
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('configuracion.personas.importaciones.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-arrow-up me-1" aria-hidden="true"></i> Importar personas
                    </a>
                </div>
            @endcan
            @if (session('estado'))
                <div class="alert alert-success" role="status">{{ session('estado') }}</div>
            @endif
                @include('partials.listado-modal', ['iconoModal' => 'bi-person-vcard'])
            <section class="card clientes-directorio" data-ajax-list>
                @include('partials.listado-busqueda', ['listadoUrl' => route('configuracion.usuarios.index'), 'placeholder' => 'Nombre o correo electrónico'])
                <div class="small text-body-secondary px-3 py-2 border-bottom">{{ $usuarios->total() }} cuentas registradas</div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="clientes-tabla-cabecera"><tr><th scope="col">{{ __('Nombre') }}</th><th scope="col">{{ __('Correo electrónico') }}</th><th scope="col">Tipo de cuenta</th><th scope="col">{{ __('Estado') }}</th><th scope="col"><span class="visually-hidden">{{ __('Acciones') }}</span></th></tr></thead>
                        <tbody>
                            @forelse ($usuarios as $usuario)
                                <tr>
                                    <th scope="row">{{ $usuario->name }}</th>
                                    <td>{{ $usuario->email }}</td>
                                    <td><span class="badge rounded-pill text-bg-light">{{ $usuario->is_admin ? 'Administrador' : 'Usuario' }}</span></td>
                                    <td><span class="badge rounded-pill {{ $usuario->activo ? 'text-success-emphasis bg-success-subtle' : 'text-secondary-emphasis bg-secondary-subtle' }}">{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</span></td>
                                    <td class="text-end">
                                        @component('partials.acciones-fila', ['ver' => route('configuracion.usuarios.show', $usuario), 'tituloVer' => 'Detalle de usuario', 'editar' => route('configuracion.usuarios.edit', $usuario), 'tituloEditar' => 'Editar usuario'])
                                            @unless ($usuario->is_admin)
                                                @include('partials.accion-fila-enlace', ['href' => route('configuracion.usuarios.permisos', $usuario), 'etiqueta' => 'Permisos', 'titulo' => 'Permisos de usuario', 'icono' => 'bi-shield-lock'])
                                            @endunless
                                        @endcomponent
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-body-secondary p-4">No hay usuarios registrados.</td></tr>
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
