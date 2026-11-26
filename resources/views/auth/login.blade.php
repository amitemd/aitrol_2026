@extends('layouts.adminlte')

@section('title', 'Iniciar sesión')

@section('content')
    <div class="app-content py-5">
        <div class="container-fluid">
            <div class="card mx-auto" style="max-width: 28rem;">
                <div class="card-body p-4">
                    <h1 class="h4 mb-4">Iniciar sesión</h1>
                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input id="email" name="email" type="email" autocomplete="username"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <input id="password" name="password" type="password" autocomplete="current-password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
