@extends('layouts.adminlte')

@section('title', 'Editar usuario')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <a href="{{ route('configuracion.usuarios.index') }}" class="small text-decoration-none">Usuarios</a>
            <h1 class="fs-3 mb-0">Editar usuario</h1>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-7 col-lg-9">
                    <form class="card shadow-sm" method="POST" data-ajax-form action="{{ route('configuracion.usuarios.update', $usuario) }}">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Nombre</label>
                                <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $usuario->name) }}" maxlength="255" autocomplete="name" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $usuario->email) }}" maxlength="255" autocomplete="off" aria-describedby="correo-ingreso" required>
                                <div id="correo-ingreso" class="form-text">Este correo se usa para iniciar sesión y recuperar la contraseña.</div>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end gap-2">
                            <a href="{{ route('configuracion.usuarios.index') }}" class="btn btn-outline-secondary" data-ajax-close>Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection