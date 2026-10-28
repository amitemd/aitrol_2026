<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('new_system.auth.new_password_title') }} | {{ config('app.name', 'AITROL') }}</title>
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
                        <img src="{{ asset('img1.png') }}" alt="Panel administrativo de AITROL">
                    </div>
                    <h2>{{ __('new_system.auth.protect_account') }}</h2>
                    <p>{{ __('new_system.auth.protect_account_description') }}</p>
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
                            <i class="fa fa-lock" aria-hidden="true"></i>
                            {{ __('new_system.auth.verified_link') }}
                        </span>
                        <h1>{{ __('new_system.auth.create_password') }}</h1>
                        <p>{{ __('new_system.auth.create_password_description') }}</p>
                    </div>

                    <form method="POST" action="{{ route('new-system.password.update') }}" class="new-system-login-form-fields">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label class="control-label" for="email">{{ __('new_system.auth.email') }}</label>
                            <div class="new-system-login-input">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email', $email) }}" required autocomplete="email" inputmode="email" spellcheck="false">
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label class="control-label" for="password">{{ __('new_system.auth.new_password') }}</label>
                            <div class="new-system-login-input">
                                <i class="fa fa-key" aria-hidden="true"></i>
                                <input id="password" type="password" class="form-control" name="password" placeholder="{{ __('new_system.auth.minimum_characters') }}" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="password_confirmation">{{ __('new_system.auth.confirm_password') }}</label>
                            <div class="new-system-login-input">
                                <i class="fa fa-key" aria-hidden="true"></i>
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder="{{ __('new_system.auth.repeat_password') }}" required autocomplete="new-password">
                            </div>
                        </div>

                        <button type="submit" class="new-system-login-submit">
                            <span>{{ __('new_system.auth.update_password') }}</span>
                            <i class="fa fa-check" aria-hidden="true"></i>
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
    @include('new-system.partials.alerts')
</body>
</html>
