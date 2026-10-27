<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión | {{ config('app.name', 'AITROL') }}</title>
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
                        <img id="login-slide-image" src="{{ asset('img1.png') }}" alt="Panel de gestión de AITROL">
                    </div>
                    <h2 id="login-slide-title">Controla las finanzas de tu empresa</h2>
                    <p id="login-slide-description">AITROL te ayuda a controlar los ingresos y egresos de tu operación.</p>
                    <div class="new-system-login-slide-dots" aria-label="Temas destacados">
                        @foreach (range(0, 5) as $slideIndex)
                            <button
                                type="button"
                                class="new-system-login-slide-dot {{ $slideIndex === 0 ? 'is-active' : '' }}"
                                data-login-slide="{{ $slideIndex }}"
                                aria-label="Mostrar tema {{ $slideIndex + 1 }}"
                                aria-current="{{ $slideIndex === 0 ? 'true' : 'false' }}"
                            ></button>
                        @endforeach
                    </div>
                </div>

                <p class="new-system-login-brand-message">
                    Una plataforma segura para la gestión integral de tu organización.
                </p>
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
                            Acceso seguro
                        </span>
                        <h1>Iniciar sesión</h1>
                        <p>Ingresa tus credenciales para acceder a tu cuenta.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger new-system-login-alert" role="alert">
                            <i class="fa fa-warning" aria-hidden="true"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="alert alert-success new-system-login-alert" role="status">
                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('new-system.login.store') }}" class="new-system-login-form-fields">
                        @csrf
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label class="control-label" for="email">Correo electrónico</label>
                            <div class="new-system-login-input">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="nombre@empresa.com" required autofocus autocomplete="username" inputmode="email" spellcheck="false">
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label class="control-label" for="password">Contraseña</label>
                            <div class="new-system-login-input">
                                <i class="fa fa-key" aria-hidden="true"></i>
                                <input id="password" type="password" class="form-control" name="password" placeholder="Ingresa tu contraseña" required autocomplete="current-password">
                            </div>
                        </div>

                        <div class="form-group new-system-login-options">
                            <label class="new-system-login-remember">
                                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                                <span>Mantener la sesión iniciada</span>
                            </label>
                            <a href="{{ route('new-system.password.request') }}">¿Olvidaste tu contraseña?</a>
                        </div>

                        <button type="submit" class="new-system-login-submit">
                            <span>Ingresar</span>
                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    </form>

                    <p class="new-system-login-support">
                        <i class="fa fa-shield" aria-hidden="true"></i>
                        Acceso exclusivo para usuarios autorizados
                    </p>
                </div>
            </section>
        </div>
    </main>

    <script nonce="{{ request()->attributes->get('csp_nonce') }}">
        (function () {
            var slides = [
                {
                    image: @json(asset('img1.png')),
                    alt: 'Panel de ingresos y flujo financiero',
                    title: 'Controla las finanzas de tu empresa',
                    description: 'AITROL te ayuda a controlar los ingresos y egresos de tu operación.'
                },
                {
                    image: @json(asset('img2.png')),
                    alt: 'Resumen de categorías operativas',
                    title: 'Toda la información en un solo lugar',
                    description: 'Accede a datos, gráficos, reportes y procesos desde una sola plataforma.'
                },
                {
                    image: @json(asset('img3.png')),
                    alt: 'Control de inventario e insumos',
                    title: 'Administra el trabajo operativo',
                    description: 'Mantén la continuidad de cada módulo desde una plataforma confiable y centralizada.'
                },
                {
                    image: @json(asset('images/login/erp-administracion.png').'?v='.filemtime(public_path('images/login/erp-administracion.png'))),
                    alt: 'Panel integral de administración empresarial',
                    title: 'Gestiona toda tu organización',
                    description: 'Centraliza empresas, usuarios, procesos y operaciones para tomar decisiones con información confiable.'
                },
                {
                    image: @json(asset('images/login/erp-contabilidad.png').'?v='.filemtime(public_path('images/login/erp-contabilidad.png'))),
                    alt: 'Panel de contabilidad y control financiero',
                    title: 'Contabilidad y finanzas conectadas',
                    description: 'Controla presupuestos, cuentas, pagos y resultados financieros desde un entorno integrado.'
                },
                {
                    image: @json(asset('images/login/erp-medicina.png').'?v='.filemtime(public_path('images/login/erp-medicina.png'))),
                    alt: 'Panel de gestión médica y hospitalaria',
                    title: 'Gestión médica integral',
                    description: 'Coordina pacientes, citas, atención clínica, laboratorio, farmacia e inventario médico.'
                }
            ];
            var currentSlide = 0;
            var slideInterval;
            var slideImage = document.getElementById('login-slide-image');
            var slideTitle = document.getElementById('login-slide-title');
            var slideDescription = document.getElementById('login-slide-description');
            var slideDots = document.querySelectorAll('[data-login-slide]');

            function showSlide(slideIndex) {
                currentSlide = slideIndex;
                slideImage.src = slides[currentSlide].image;
                slideImage.alt = slides[currentSlide].alt;
                slideTitle.textContent = slides[currentSlide].title;
                slideDescription.textContent = slides[currentSlide].description;

                Array.prototype.forEach.call(slideDots, function (dot, index) {
                    var isActive = index === currentSlide;

                    dot.classList.toggle('is-active', isActive);
                    dot.setAttribute('aria-current', isActive ? 'true' : 'false');
                });
            }

            function startSlideRotation() {
                window.clearInterval(slideInterval);
                slideInterval = window.setInterval(function () {
                    showSlide((currentSlide + 1) % slides.length);
                }, 4500);
            }

            Array.prototype.forEach.call(slideDots, function (dot) {
                dot.addEventListener('click', function () {
                    showSlide(Number(dot.getAttribute('data-login-slide')));
                    startSlideRotation();
                });
            });

            startSlideRotation();
        }());
    </script>
</body>
</html>
