<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nueva contraseña | {{ config('app.name', 'AITROL') }}</title>
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
                        <img src="{{ asset('img1.png') }}" alt="Panel administrativo de AITROL">
                    </div>
                    <h2>Protege el acceso a tu cuenta</h2>
                    <p>Define una contraseña nueva de al menos ocho caracteres y mantenla en un lugar seguro.</p>
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
                            <i class="fa fa-lock" aria-hidden="true"></i>
                            Enlace verificado
                        </span>
                        <h1>Crea una nueva contraseña</h1>
                        <p>Confirma tu correo y escribe la contraseña que usarás desde ahora.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger new-system-login-alert" role="alert">
                            <i class="fa fa-warning" aria-hidden="true"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('new-system.password.update') }}" class="new-system-login-form-fields">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label class="control-label" for="email">Correo electrónico</label>
                            <div class="new-system-login-input">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email', $email) }}" required autocomplete="email" inputmode="email" spellcheck="false">
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label class="control-label" for="password">Nueva contraseña</label>
                            <div class="new-system-login-input">
                                <i class="fa fa-key" aria-hidden="true"></i>
                                <input id="password" type="password" class="form-control" name="password" placeholder="Mínimo 8 caracteres" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="password_confirmation">Confirmar contraseña</label>
                            <div class="new-system-login-input">
                                <i class="fa fa-key" aria-hidden="true"></i>
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder="Repite la nueva contraseña" required autocomplete="new-password">
                            </div>
                        </div>

                        <button type="submit" class="new-system-login-submit">
                            <span>Actualizar contraseña</span>
                            <i class="fa fa-check" aria-hidden="true"></i>
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
