<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Función heredada no disponible · AITROL</title>
    @vite(['resources/css/app.css'])
</head>
<body class="grid min-h-screen place-items-center bg-slate-950 px-6 text-slate-100">
    <main class="w-full max-w-2xl rounded-3xl border border-amber-300/20 bg-white/[0.06] p-8 shadow-2xl">
        <span class="inline-flex rounded-full bg-amber-300/10 px-3 py-1 text-xs font-semibold tracking-wider text-amber-200 uppercase">Código fuente incompleto</span>
        <h1 class="mt-5 text-3xl font-semibold text-white">Esta función no venía en el proyecto original.</h1>
        <p class="mt-4 leading-7 text-slate-300">{{ $message }}</p>
        <dl class="mt-7 space-y-3 rounded-2xl bg-slate-950/60 p-5 text-sm">
            <div><dt class="text-slate-500">Ruta</dt><dd class="mt-1 font-mono text-slate-200">{{ $route ?: 'sin nombre' }}</dd></div>
            <div><dt class="text-slate-500">Acción original</dt><dd class="mt-1 break-all font-mono text-slate-200">{{ $action }}</dd></div>
        </dl>
        <a href="{{ route('modules.index') }}" class="mt-7 inline-flex rounded-xl bg-cyan-400 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">Ver mapa de módulos</a>
    </main>
</body>
</html>
