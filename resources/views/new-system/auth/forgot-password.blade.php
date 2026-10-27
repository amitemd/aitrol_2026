<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Recuperar contraseña | {{ config('app.name', 'AITROL') }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/app-template.css') }}?v={{ filemtime(public_path('css/app-template.css')) }}">
    <link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/new-system-legacy.css') }}?v={{ filemtime(public_path('css/new-system-legacy.css')) }}">
</head>
<body class="new-system-login-page">
    <main class="new-system-login-shell">
        <div class="row">
            <section class="col-md-6 new-system-login-information">
                <div class="new-system-login-logo" aria-label="AITROL Sistema Médico">
                    <strong>AITROL</strong>
                    <span>Sistema Médico</span>
                </div>

                <div class="new-system-login-slide">
                    <div class="new-system-login-slide-image">
                        <img src="{{ asset('img2.png') }}" alt="Panel financiero de AITROL">
                    </div>
                    <h2>Recupera el acceso de forma segura</h2>
                    <p>Te enviaremos un enlace temporal al correo registrado en tu cuenta.</p>
                </div>

                <p class="new-system-login-brand-message">Una plataforma segura para la gestión integral de tu organización.</p>
            </section>

            <section class="col-md-6 new-system-login-form-column">
                <div class="new-system-login-form">
                    <div class="new-system-login-logo new-system-login-logo-mobile visible-xs visible-sm text-center" aria-label="AITROL Sistema Médico">
                        <strong>AITROL</strong>
                        <span>Sistema Médico</span>
                    </div>

                    <div class="new-system-login-heading">
                        <span class="new-system-login-eyebrow">
                            <i class="fa fa-envelope-o" aria-hidden="true"></i>
                            Recuperación segura
                        </span>
                        <h1>¿Olvidaste tu contraseña?</h1>
                        <p>Ingresa tu correo y te enviaremos las instrucciones para restablecerla.</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success new-system-login-alert" role="status">
                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger new-system-login-alert" role="alert">
                            <i class="fa fa-warning" aria-hidden="true"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('new-system.password.email') }}" class="new-system-login-form-fields">
                        @csrf
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label class="control-label" for="email">Correo electrónico</label>
                            <div class="new-system-login-input">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="nombre@empresa.com" required autofocus autocomplete="email" inputmode="email" spellcheck="false">
                            </div>
                        </div>

                        <button type="submit" class="new-system-login-submit">
                            <span>Enviar enlace de recuperación</span>
                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    </form>

                    <p class="new-system-login-support">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i>
                        <a href="{{ route('new-system.dashboard') }}">Volver a iniciar sesión</a>
                    </p>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
