<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') · AITROL</title>

    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            * { box-sizing: border-box; } body { margin: 0; background: #f1f5f9; color: #0f172a; font-family: system-ui, sans-serif; }
            a { color: inherit; } main { width: min(1480px, calc(100% - 32px)); margin: 0 auto; padding: 24px 0 48px; }
            .fallback-card { border: 1px solid #e2e8f0; border-radius: 18px; background: white; padding: 20px; margin-top: 18px; }
        </style>
    @endif
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    <header class="sticky top-0 z-40 border-b border-slate-800 bg-slate-950 text-white shadow-lg">
        <div class="mx-auto flex max-w-[1480px] flex-col gap-3 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <a href="{{ route('new-system.dashboard') }}" class="flex items-center gap-3 no-underline">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-cyan-400 font-black text-slate-950">A</span>
                <span><strong class="block tracking-wide">AITROL</strong><small class="text-slate-400">Control y observabilidad</small></span>
            </a>
            <nav class="flex flex-wrap gap-2 text-sm" aria-label="Administración técnica">
                <a href="{{ route('observability.logs.index') }}" @class(['rounded-lg px-3 py-2 no-underline', 'bg-cyan-400 font-bold text-slate-950' => request()->routeIs('observability.logs.*'), 'text-slate-300 hover:bg-white/10 hover:text-white' => ! request()->routeIs('observability.logs.*')])>Logs y diagnóstico</a>
                <a href="{{ route('observability.audit.index') }}" @class(['rounded-lg px-3 py-2 no-underline', 'bg-cyan-400 font-bold text-slate-950' => request()->routeIs('observability.audit.*'), 'text-slate-300 hover:bg-white/10 hover:text-white' => ! request()->routeIs('observability.audit.*')])>Auditoría</a>
                @if (Route::has('deployment.index'))
                    <a href="{{ route('deployment.index') }}" class="rounded-lg px-3 py-2 text-slate-300 no-underline hover:bg-white/10 hover:text-white">Despliegue</a>
                @endif
                <a href="{{ route('new-system.dashboard') }}" class="rounded-lg px-3 py-2 text-slate-300 no-underline hover:bg-white/10 hover:text-white">Volver al sistema</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto flex max-w-[1480px] flex-col gap-5 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        @yield('content')
    </main>
    @include('new-system.partials.alerts', [
        'success' => null,
        'error' => null,
        'warning' => null,
        'info' => null,
    ])
</body>
</html>
