<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ingresar | {{ config('app.name', 'Sistema Médico') }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/app-template.css') }}?v={{ filemtime(public_path('css/app-template.css')) }}">
    <link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/new-system-legacy.css') }}?v={{ filemtime(public_path('css/new-system-legacy.css')) }}">
</head>
<body class="new-system-login-page">
    <main class="new-system-login-shell">
        <div class="row">
            <section class="col-md-6 new-system-login-information">
                <div class="new-system-login-logo">
                    <strong>AITROL</strong>
                    <span>Sistema Médico</span>
                </div>

                <div class="new-system-login-slide">
                    <div class="new-system-login-slide-image">
                        <img id="login-slide-image" src="{{ asset('img1.png') }}" alt="Gestión administrativa">
                    </div>
                    <h2 id="login-slide-title">Controla las finanzas de tu empresa</h2>
                    <p id="login-slide-description">AITROL te ayuda a controlar los ingresos y egresos de tu operación.</p>
                </div>
            </section>

            <section class="col-md-6 new-system-login-form-column">
                <div class="new-system-login-form">
                    <div class="new-system-login-logo visible-xs visible-sm text-center" style="color: #1562ad; display: block; margin-bottom: 30px;">
                        <strong>AITROL</strong>
                        <span>Sistema Médico</span>
                    </div>
                    <h1>Sign In</h1>
                    <p>Ingresa tus credenciales para acceder a tu cuenta.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="fa fa-warning"></i> {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('new-system.login.store') }}">
                        @csrf
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label class="control-label" for="email">Correo electrónico</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus autocomplete="username">
                        </div>

                        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label class="control-label" for="password">Contraseña</label>
                            <input id="password" type="password" class="form-control" name="password" placeholder="Contraseña" required autocomplete="current-password">
                        </div>

                        <div class="form-group clearfix">
                            <label class="pull-left">
                                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}> Mantener la sesión iniciada
                            </label>
                            @if (Route::has('password.request'))
                                <a class="pull-right" href="{{ route('password.request') }}">¿Necesitas ayuda?</a>
                            @endif
                        </div>

                        <button type="submit" class="new-system-login-submit">Ingresar</button>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <script>
        (function () {
            var slides = [
                {
                    image: @json(asset('img1.png')),
                    title: 'Controla las finanzas de tu empresa',
                    description: 'AITROL te ayuda a controlar los ingresos y egresos de tu operación.'
                },
                {
                    image: @json(asset('img2.png')),
                    title: 'Toda la información en un solo lugar',
                    description: 'Accede a datos, gráficos, reportes y procesos desde una sola plataforma.'
                },
                {
                    image: @json(asset('img3.png')),
                    title: 'Administra el trabajo operativo',
                    description: 'Mantén la continuidad de cada módulo mientras avanzamos hacia Laravel 13.'
                }
            ];
            var currentSlide = 0;

            window.setInterval(function () {
                currentSlide = (currentSlide + 1) % slides.length;
                document.getElementById('login-slide-image').src = slides[currentSlide].image;
                document.getElementById('login-slide-title').textContent = slides[currentSlide].title;
                document.getElementById('login-slide-description').textContent = slides[currentSlide].description;
            }, 3500);
        }());
    </script>
</body>
</html>
