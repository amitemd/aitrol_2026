@extends('layouts.auth')

@section('title', 'Nueva contraseña')

@section('content')
    <header class="access-heading">
        <h1 id="access-title">Nueva contraseña</h1>
        <p>Usa al menos 12 caracteres para proteger tu acceso a Aitrol.</p>
    </header>

    <form method="POST" action="{{ route('password.update') }}" class="access-form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        @error('token') <p class="access-error" role="alert">{{ $message }}</p> @enderror
        <div class="access-field">
            <label for="email">Correo electrónico</label>
            <input id="email" name="email" type="email" autocomplete="username" maxlength="255"
                value="{{ old('email', $email) }}" required
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email') <p id="email-error" class="access-error">{{ $message }}</p> @enderror
        </div>
        <div class="access-field">
            <label for="password">Nueva contraseña</label>
            <input id="password" name="password" type="password" autocomplete="new-password" minlength="12" maxlength="72" required
                @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            @error('password') <p id="password-error" class="access-error">{{ $message }}</p> @enderror
        </div>
        <div class="access-field">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                minlength="12" maxlength="72" required>
        </div>
        <button class="btn btn-primary access-button" type="submit">Guardar contraseña</button>
        <a class="access-back" href="{{ route('password.request') }}">Solicitar otro enlace</a>
    </form>
@endsection
