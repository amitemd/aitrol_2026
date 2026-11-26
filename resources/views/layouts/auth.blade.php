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
<body class="aitrol-access">
    <main class="access-shell">
        <aside class="access-brand" data-bs-theme="dark" aria-label="Aitrol ERP hospitalario">
            <div class="access-logo">
                <span class="medical-mark" aria-hidden="true"></span>
                <div>
                    <strong>Aitrol</strong>
                    <span class="access-product">ERP hospitalario</span>
                </div>
            </div>
            <div class="access-brand-copy">
                <p class="access-brand-title">Gestión hospitalaria y administrativa</p>
                <p>Acceso para el personal clínico y administrativo.</p>
            </div>
        </aside>
        <section class="access-main" aria-labelledby="access-title">
            <div class="login-box">
                @yield('content')
            </div>
        </section>
    </main>
</body>
</html>
