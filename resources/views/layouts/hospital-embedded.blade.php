<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Formulario clínico')</title>
    <style>
        html, body { min-height: 100%; }
        body { margin: 0; background: #f5faff; }
        .hospital-embedded-container { min-height: 100vh; }
    </style>
    @yield('styles')
</head>
<body>
    <main class="hospital-embedded-container">
        @yield('content')
    </main>
    @yield('scripts')
</body>
</html>
