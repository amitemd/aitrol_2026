@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@section('brand-media')
    @php($areas = ['Empresas', 'Administración médica', 'Contabilidad', 'Inventario'])
    <canvas class="access-network" aria-hidden="true"></canvas>
    <section class="access-showcase" aria-label="Áreas de gestión" data-auth-showcase>
        <div class="access-showcase-frame" id="access-showcase-photos">
            @foreach ($areas as $actividad)
                <figure @class(['access-slide', 'is-active' => $loop->first]) data-showcase-slide
                    aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                    <img src="{{ asset('auth-erp.jpg') }}" alt="" draggable="false">
                    <figcaption>{{ $actividad }}</figcaption>
                </figure>
            @endforeach
        </div>
        <div class="access-showcase-controls" data-showcase-controls hidden>
            <div class="access-showcase-dots" role="group" aria-label="Elegir imagen">
                @foreach ($areas as $actividad)
                    <button type="button" data-showcase-select aria-label="{{ $actividad }}"
                        aria-controls="access-showcase-photos" aria-current="{{ $loop->first ? 'true' : 'false' }}"></button>
                @endforeach
            </div>
            <button type="button" class="access-showcase-playback" data-showcase-playback>Pausar</button>
        </div>
    </section>
@endsection

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
                value="{{ old('email') }}" required
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email') <p id="email-error" class="access-error">{{ $message }}</p> @enderror
        </div>
        <div class="access-field">
            <div class="access-field-heading">
                <label for="password">Contraseña</label>
                <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
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
        <button class="btn btn-primary access-button" type="submit">
            <span class="access-progress" aria-hidden="true" hidden></span>
            <span data-submit-label>Entrar al ERP</span>
        </button>
    </form>
    <p class="visually-hidden" data-login-feedback role="status"></p>
@endsection

@push('scripts')
    <script src="{{ asset('auth.js') }}" defer></script>
    <script src="{{ asset('auth-showcase.js') }}" defer></script>
@endpush
