<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('new_system.auth.forgot_title') }} | {{ config('app.name', 'AITROL') }}</title>
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
                        <img src="{{ asset('img2.png') }}" alt="Panel financiero de AITROL">
                    </div>
                    <h2>{{ __('new_system.auth.recover_securely') }}</h2>
                    <p>{{ __('new_system.auth.recover_securely_description') }}</p>
                </div>

                <p class="new-system-login-brand-message">{{ __('new_system.auth.brand_message') }}</p>
            </section>

            <section class="col-md-6 new-system-login-form-column">
                <div class="new-system-login-form">
                    <div class="new-system-login-logo new-system-login-logo-mobile visible-xs visible-sm text-center" aria-label="AITROL {{ __('new_system.common.medical_system') }}">
                        <strong>AITROL</strong>
                        <span>{{ __('new_system.common.medical_system') }}</span>
                    </div>

                    <div class="new-system-login-heading">
                        <span class="new-system-login-eyebrow">
                            <i class="fa fa-envelope-o" aria-hidden="true"></i>
                            {{ __('new_system.auth.secure_recovery') }}
                        </span>
                        <h1>{{ __('new_system.auth.forgot_heading') }}</h1>
                        <p>{{ __('new_system.auth.forgot_description') }}</p>
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
                            <label class="control-label" for="email">{{ __('new_system.auth.email') }}</label>
                            <div class="new-system-login-input">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{ __('new_system.auth.email_placeholder') }}" required autofocus autocomplete="email" inputmode="email" spellcheck="false">
                            </div>
                        </div>

                        <button type="submit" class="new-system-login-submit">
                            <span>{{ __('new_system.auth.send_recovery_link') }}</span>
                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    </form>

                    <p class="new-system-login-support">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i>
                        <a href="{{ route('new-system.dashboard') }}">{{ __('new_system.auth.back_to_login') }}</a>
                    </p>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
