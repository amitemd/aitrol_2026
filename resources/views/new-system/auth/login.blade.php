@php
    $loginSlides = [
        ['image' => asset('img1.png'), ...__('new_system.auth.slides.finance')],
        ['image' => asset('img2.png'), ...__('new_system.auth.slides.information')],
        ['image' => asset('img3.png'), ...__('new_system.auth.slides.operations')],
        ['image' => asset('images/login/erp-administracion.png').'?v='.filemtime(public_path('images/login/erp-administracion.png')), ...__('new_system.auth.slides.administration')],
        ['image' => asset('images/login/erp-contabilidad.png').'?v='.filemtime(public_path('images/login/erp-contabilidad.png')), ...__('new_system.auth.slides.accounting')],
        ['image' => asset('images/login/erp-medicina.png').'?v='.filemtime(public_path('images/login/erp-medicina.png')), ...__('new_system.auth.slides.medical')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('new_system.auth.sign_in') }} | {{ config('app.name', 'AITROL') }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/app-template.css') }}?v={{ filemtime(public_path('css/app-template.css')) }}">
    <link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/new-system-legacy.css') }}?v={{ filemtime(public_path('css/new-system-legacy.css')) }}">
</head>
<body class="new-system-login-page">
    <main class="new-system-login-shell">
        <div class="row">
            <section class="col-md-6 new-system-login-information">
                <div class="new-system-login-logo" aria-label="AITROL {{ __('new_system.common.medical_system') }}">
                    <strong>AITROL</strong>
                    <span>{{ __('new_system.common.medical_system') }}</span>
                </div>

                <div class="new-system-login-slide">
                    <div class="new-system-login-slide-image">
                        <img id="login-slide-image" src="{{ $loginSlides[0]['image'] }}" alt="{{ $loginSlides[0]['alt'] }}">
                    </div>
                    <h2 id="login-slide-title">{{ $loginSlides[0]['title'] }}</h2>
                    <p id="login-slide-description">{{ $loginSlides[0]['description'] }}</p>
                    <div class="new-system-login-slide-dots" aria-label="{{ __('new_system.auth.featured_topics') }}">
                        @foreach (range(0, 5) as $slideIndex)
                            <button
                                type="button"
                                class="new-system-login-slide-dot {{ $slideIndex === 0 ? 'is-active' : '' }}"
                                data-login-slide="{{ $slideIndex }}"
                                aria-label="{{ __('new_system.auth.show_topic', ['number' => $slideIndex + 1]) }}"
                                aria-current="{{ $slideIndex === 0 ? 'true' : 'false' }}"
                            ></button>
                        @endforeach
                    </div>
                </div>

                <p class="new-system-login-brand-message">
                    {{ __('new_system.auth.brand_message') }}
                </p>
            </section>

            <section class="col-md-6 new-system-login-form-column">
                <div class="new-system-login-form">
                    <div class="new-system-login-logo new-system-login-logo-mobile visible-xs visible-sm text-center" aria-label="AITROL {{ __('new_system.common.medical_system') }}">
                        <strong>AITROL</strong>
                        <span>{{ __('new_system.common.medical_system') }}</span>
                    </div>

                    <div class="new-system-login-heading">
                        <span class="new-system-login-eyebrow">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                            {{ __('new_system.auth.secure_access') }}
                        </span>
                        <h1>{{ __('new_system.auth.sign_in') }}</h1>
                        <p>{{ __('new_system.auth.sign_in_description') }}</p>
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

                    <form
                        method="POST"
                        action="{{ route('new-system.login.store') }}"
                        class="new-system-login-form-fields {{ $loginLocked ? 'is-locked' : '' }}"
                        aria-disabled="{{ $loginLocked ? 'true' : 'false' }}"
                    >
                        @csrf
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label class="control-label" for="email">{{ __('new_system.auth.email') }}</label>
                            <div class="new-system-login-input">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{ __('new_system.auth.email_placeholder') }}" required autofocus autocomplete="username" inputmode="email" spellcheck="false" @disabled($loginLocked)>
                                @if ($loginLocked)
                                    <span class="new-system-login-field-lock" aria-hidden="true">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label class="control-label" for="password">{{ __('new_system.auth.password') }}</label>
                            <div class="new-system-login-input">
                                <i class="fa fa-key" aria-hidden="true"></i>
                                <input id="password" type="password" class="form-control" name="password" placeholder="{{ __('new_system.auth.password_placeholder') }}" required autocomplete="current-password" @disabled($loginLocked)>
                                @if ($loginLocked)
                                    <span class="new-system-login-field-lock" aria-hidden="true">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group new-system-login-options">
                            <label class="new-system-login-remember {{ $loginLocked ? 'is-disabled' : '' }}">
                                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} @disabled($loginLocked)>
                                <span>{{ __('new_system.auth.remember') }}</span>
                            </label>
                            <a href="{{ route('new-system.password.request') }}">{{ __('new_system.auth.forgot_password') }}</a>
                        </div>

                        <button type="submit" class="new-system-login-submit" @disabled($loginLocked)>
                            <span>{{ __('new_system.auth.submit') }}</span>
                            <i class="fa {{ $loginLocked ? 'fa-lock' : 'fa-arrow-right' }}" aria-hidden="true"></i>
                        </button>
                    </form>

                    <p class="new-system-login-support">
                        <i class="fa fa-shield" aria-hidden="true"></i>
                        {{ __('new_system.auth.authorized_only') }}
                    </p>
                </div>
            </section>
        </div>
    </main>

    <script nonce="{{ request()->attributes->get('csp_nonce') }}">
        (function () {
            var slides = {{ Js::from($loginSlides) }};
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
