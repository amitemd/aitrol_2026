@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@section('content')
<header class="access-heading">
    <h1 id="access-title">Iniciar sesión</h1>
    <p>Ingresa con tu cuenta de trabajo.</p>
</header>

@if (session('status'))
<p class="access-status" role="status">{{ session('status') }}</p>
@endif

<form method="POST" action="{{ route('login.store') }}" class="access-form" data-login-form>
    @csrf
    <div class="access-field">
        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" autocomplete="username" maxlength="255"
            value="{{ old('email') }}" placeholder="nombre@empresa.com" required
            @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
        @error('email') <p id="email-error" class="access-error">{{ $message }}</p> @enderror
    </div>
    <div class="access-field">
        <div class="access-field-heading">
            <label for="password">Contraseña</label>
        </div>
        <div class="access-password">
            <input id="password" name="password" type="password" autocomplete="current-password" maxlength="255" required
                @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            <button class="access-password-toggle" type="button" data-password-toggle aria-controls="password"
                aria-label="Mostrar contraseña" aria-pressed="false" hidden>
                <span class="access-eye" aria-hidden="true"></span>
            </button>
        </div>
        @error('password') <p id="password-error" class="access-error">{{ $message }}</p> @enderror
    </div>
    <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
    <button class="btn btn-primary access-button access-login-submit" type="submit">
        <span class="access-progress" aria-hidden="true" hidden></span>
        <span data-submit-label>Entrar al ERP</span>
    </button>
</form>
<p class="visually-hidden" data-login-feedback role="status"></p>
@endsection

@push('scripts')
<script src="{{ asset('auth.js') }}" defer></script>
@endpush