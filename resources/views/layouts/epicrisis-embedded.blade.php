<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="{{ url('./') }}">
    <title>006 · Epicrisis</title>
    <link rel="stylesheet" href="{{ url('v3/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ url('v3/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('v3/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('v3/dist/css/adminlte.min.css') }}?v=3.2.0">
    <link rel="stylesheet" href="{{ \Sis_medico\Support\V3Asset::url('v3/dist/css/modern-ui.css') }}">
    <script src="{{ url('v3/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ url('v3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ url('v3/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ url('v3/plugins/moment/moment.min.js') }}"></script>
    @yield('css')
    <style>
        html, body { min-height: 100%; }
        body { margin: 0; background: #f4f9fc; color: #123b5d; }
        .epicrisis-embedded-content { min-height: 100vh; padding: 0; }
        .epicrisis-embedded-content .content-wrapper { min-height: auto; margin-left: 0 !important; background: transparent; }
        .epicrisis-embedded-content .content-header { display: none; }
        @media (max-width: 767px) { .epicrisis-embedded-content { padding: 0; } }
    </style>
</head>
<body class="hold-transition text-sm aitrol-v3 epicrisis-embedded">
    <main class="epicrisis-embedded-content">
        @yield('contenido')
        @yield('content')
    </main>
    @yield('variables')
    @yield('js')
    <script>
        (function () {
            if (window.parent === window) return;
            var lastHeight = 0;
            function reportHeight() {
                var height = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
                if (Math.abs(height - lastHeight) < 6) return;
                lastHeight = height;
                window.parent.postMessage({type: 'epicrisis006:resize', height: height}, window.location.origin);
            }
            window.addEventListener('load', reportHeight);
            if (window.ResizeObserver) new ResizeObserver(reportHeight).observe(document.body);
            else setInterval(reportHeight, 750);
            setTimeout(reportHeight, 150);
        })();
    </script>
</body>
</html>
