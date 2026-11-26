@extends('layouts.adminlte')

@section('title', 'Permisos de usuario')

@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Permisos de usuario', 'subtitulo' => $usuario->name, 'icono' => 'bi-shield-lock'])
    <div class="app-content">
        <div class="container-fluid">
            <form method="POST" class="card form-accesos" data-ajax-form action="{{ route('configuracion.usuarios.permisos.update', $usuario) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @if ($errors->any())
                        <ul class="alert alert-danger ps-4" role="alert">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    @endif
                    <fieldset class="mb-3">
                        <legend class="fs-6">{{ $usuario->name }} · Roles asignados</legend>
                        @forelse ($roles as $rol)
                            <label class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input" name="roles[]" value="{{ $rol->id }}" @checked(in_array($rol->id, old('roles', $asignados)))>
                                <span>{{ $rol->nombre }}</span>
                            </label>
                        @empty
                            <p class="small text-body-secondary mb-0">No hay roles disponibles.</p>
                        @endforelse
                    </fieldset>
                    <h2 class="fs-6">Permisos directos</h2>
                    <p class="small text-body-secondary">Permitir o denegar reemplaza lo concedido por los roles.</p>
                    @include('configuracion::matriz-permisos')
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('configuracion.usuarios.index') }}" class="btn btn-outline-secondary" data-ajax-close>Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar permisos</button>
                </div>
            </form>
        </div>
    </div>
@endsection