<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="no-referrer">
    <title>@yield('title') · Aitrol</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('aitrol-brand.css') }}">
    <link rel="stylesheet" href="{{ asset('auth.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.25/dist/sweetalert2.min.css" crossorigin="anonymous">
</head>
<body class="aitrol-access" data-bs-theme="dark">
    <main class="access-shell">
        <aside class="access-brand" data-bs-theme="dark" aria-label="Aitrol ERP administrativo">
            <div class="access-logo">
                @include('partials.aitrol-mark')
                <div>
                    <strong class="aitrol-wordmark">Aitrol</strong>
                    <span class="access-product">{{ __('ERP administrativo') }}</span>
                </div>
            </div>
            <div class="access-brand-copy">
                <p class="access-brand-title">Tus empresas, bajo control.</p>
                <p>Centraliza la administración de una o varias empresas.</p>
            </div>
            @include('partials.auth-showcase')
        </aside>
        <section class="access-main" aria-labelledby="access-title">
            <div class="login-box">
                <form class="d-flex justify-content-end gap-2 mb-3" method="POST" action="{{ route('idioma.actualizar') }}">
                    @csrf
                    <select class="form-select form-select-sm w-auto" name="idioma" aria-label="{{ __('Idioma') }}">
                        @foreach (config('app.idiomas') ?? ['es' => 'Español'] as $codigo => $nombreIdioma)
                            <option value="{{ $codigo }}" lang="{{ str_replace('_', '-', $codigo) }}" @selected(app()->getLocale() === $codigo)>{{ $nombreIdioma }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('Aplicar') }}</button>
                </form>
                @yield('content')
            </div>
        </section>
    </main>
    <script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('auth-showcase.js') }}" defer></script>
    <script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('aitrol-brand.js') }}" defer></script>
    @include('partials.alertas')
    @stack('scripts')
</body>
</html>
