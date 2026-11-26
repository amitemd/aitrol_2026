@extends('layouts.auth')

@section('title', 'Recuperar contraseña')

@section('content')
    <header class="access-heading">
        <h1 id="access-title">Recuperar contraseña</h1>
        @if ($correoDisponible)
            <p>Ingresa el correo de tu cuenta para recibir un enlace de recuperación.</p>
        @else
            <p>Solicita al administrador de Aitrol un enlace de recuperación para tu cuenta.</p>
        @endif
    </header>

    @if (session('status'))
        <p class="access-status" role="status">{{ session('status') }}</p>
    @endif

    @if ($correoDisponible)
        <form method="POST" action="{{ route('password.email') }}" class="access-form">
            @csrf
            <div class="access-field">
                <label for="email">Correo electrónico</label>
                <input id="email" name="email" type="email" autocomplete="username" maxlength="255"
                    value="{{ old('email') }}" required
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email') <p id="email-error" class="access-error">{{ $message }}</p> @enderror
            </div>
            <button class="btn btn-primary access-button" type="submit">Enviar enlace</button>
            <a class="access-back" href="{{ route('dashboard') }}">Volver al inicio de sesión</a>
        </form>
    @else
        <div class="access-form">
            @error('email')
                <p class="access-error" role="alert">{{ $message }}</p>
            @enderror
            <p class="access-status access-status-info">La recuperación por correo aún no está configurada.</p>
            <a class="access-back" href="{{ route('dashboard') }}">Volver al inicio de sesión</a>
        </div>
    @endif
@endsection
