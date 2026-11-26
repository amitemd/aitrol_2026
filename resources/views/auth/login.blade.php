@extends('layouts.adminlte')

@section('title', 'Iniciar sesión')
@section('body-class', 'login-page bg-body-secondary')

@section('standalone')
    <main class="login-box w-100 px-3" style="max-width: 25rem;">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header py-4 text-center">
                <img src="{{ asset('adminlte/assets/img/AdminLTELogo.png') }}" width="44" height="44" alt="">
                <p class="fs-4 fw-bold mb-0 mt-2">Aitrol</p>
            </div>
            <div class="card-body login-card-body p-4">
                <h1 class="h5 mb-4 text-center">Iniciar sesión</h1>

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <div class="input-group has-validation">
                            <input id="email" name="email" type="email" autocomplete="username" maxlength="255"
                                @class(['form-control', 'is-invalid' => $errors->has('email')])
                                value="{{ old('email') }}" required autofocus
                                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                            <span class="input-group-text" aria-hidden="true"><i class="bi bi-envelope"></i></span>
                            @error('email') <div id="email-error" class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group has-validation">
                            <input id="password" name="password" type="password" autocomplete="current-password" maxlength="255"
                                @class(['form-control', 'is-invalid' => $errors->has('password')]) required
                                @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                            <span class="input-group-text" aria-hidden="true"><i class="bi bi-lock-fill"></i></span>
                            @error('password') <div id="password-error" class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
            </div>
        </div>
    </main>
@endsection
