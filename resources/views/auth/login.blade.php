@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@section('content')
    <header class="access-heading">
        <h1 id="access-title">Iniciar sesión</h1>
        <p>Ingresa con tu cuenta del ERP.</p>
    </header>

    @if (session('status'))
        <p class="access-status" role="status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="access-form">
        @csrf
        <div class="access-field">
            <label for="email">Correo electrónico</label>
            <input id="email" name="email" type="email" autocomplete="username" maxlength="255"
                value="{{ old('email') }}" required
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email') <p id="email-error" class="access-error">{{ $message }}</p> @enderror
        </div>
        <div class="access-field">
            <div class="access-field-heading">
                <label for="password">Contraseña</label>
                <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            </div>
            <input id="password" name="password" type="password" autocomplete="current-password" maxlength="255" required
                @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            @error('password') <p id="password-error" class="access-error">{{ $message }}</p> @enderror
        </div>
        <button class="access-button" type="submit">Entrar al ERP</button>
    </form>
@endsection
