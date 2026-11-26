<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="no-referrer">
    <title>@yield('title') · Aitrol</title>
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('auth.css') }}">
</head>
<body class="aitrol-access" data-bs-theme="dark">
    <main class="access-shell">
        <aside class="access-brand" data-bs-theme="dark" aria-label="Aitrol ERP administrativo">
            <div class="access-logo">
                <span class="access-mark" aria-hidden="true"><span></span><span></span><span></span><span></span></span>
                <div>
                    <strong>Aitrol</strong>
                    <span class="access-product">ERP administrativo</span>
                </div>
            </div>
            @yield('brand-media')
            <div class="access-brand-copy">
                <p class="access-brand-title">Tus empresas, bajo control.</p>
                <p>Centraliza la administración de una o varias empresas.</p>
            </div>
        </aside>
        <section class="access-main" aria-labelledby="access-title">
            <div class="login-box">
                @yield('content')
            </div>
        </section>
    </main>
    @stack('scripts')
</body>
</html>
