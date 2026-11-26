<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="no-referrer">
    <title>@yield('title') · Aitrol</title>
    <link rel="stylesheet" href="{{ asset('auth.css') }}">
</head>
<body class="aitrol-access">
    <main class="access-shell">
        <aside class="access-brand" aria-label="Aitrol ERP hospitalario">
            <div class="access-logo">
                <span class="medical-mark" aria-hidden="true"></span>
                <strong>Aitrol</strong>
            </div>
            <div class="access-brand-copy">
                <p class="access-brand-title">Gestión hospitalaria</p>
                <p>Operación clínica y administración en un solo sistema.</p>
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
