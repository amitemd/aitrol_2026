<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Despliegue y mantenimiento · AITROL</title>

    @if ($status['manifest'])
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            body { margin: 0; background: #f1f5f9; color: #0f172a; font-family: system-ui, sans-serif; }
            main { box-sizing: border-box; margin: 0 auto; max-width: 1280px; padding: 32px 20px; }
            article, section, header { box-sizing: border-box; }
            button, input { font: inherit; }
        </style>
    @endif
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    <div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-48 -right-48 h-96 w-96 rounded-full bg-cyan-300/20 blur-3xl"></div>
        <div class="absolute -bottom-48 -left-48 h-96 w-96 rounded-full bg-blue-300/20 blur-3xl"></div>
    </div>

    <main class="relative mx-auto flex max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
        <header class="overflow-hidden rounded-3xl bg-slate-950 p-6 text-white shadow-2xl shadow-slate-300/60 sm:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex max-w-3xl flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-cyan-400 font-black text-slate-950">A</span>
                        <div>
                            <p class="text-sm font-semibold text-cyan-300">AITROL</p>
                            <p class="text-xs tracking-[0.18em] text-slate-400 uppercase">Administración del servidor</p>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Despliegue y mantenimiento</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">Verifica el estado de Laravel, MySQL y los recursos compilados antes de ejecutar operaciones administrativas.</p>
                    </div>
                </div>

                <nav class="flex flex-wrap gap-3" aria-label="Enlaces del sistema">
                    <a href="{{ route('new-system.dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-cyan-400 px-4 py-2.5 text-sm font-bold text-slate-950 transition hover:bg-cyan-300">AITROL Nuevo</a>
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-white/15 bg-white/5 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">Sistema legado</a>
                    <a href="{{ route('deployment.index') }}" class="inline-flex items-center justify-center rounded-xl border border-white/15 px-4 py-2.5 text-sm font-semibold text-slate-300 transition hover:text-white">Actualizar estado</a>
                </nav>
            </div>
        </header>

        @if ($status['debug'] || $status['environment'] !== 'production')
            <section class="flex gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900" role="alert">
                <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-amber-200 font-bold">!</span>
                <div>
                    <p class="font-semibold">Configuración no recomendada para el servidor</p>
                    <p class="mt-1 leading-6 text-amber-800">El entorno figura como <strong>{{ $status['environment'] }}</strong> y la depuración está {{ $status['debug'] ? 'activada' : 'desactivada' }}. En producción use <code>APP_ENV=production</code>, <code>APP_DEBUG=false</code> y luego limpie la configuración.</p>
                </div>
            </section>
        @endif

        <section class="flex gap-3 rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-950">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
            <div>
                <p class="font-semibold">El panel puede actualizar el código desde Git.</p>
                <p class="mt-1 leading-6 text-sky-800">Ejecute primero “Actualizar desde Git” y, cuando termine correctamente, use “Finalizar despliegue”. Composer y npm continúan fuera del panel.</p>
            </div>
        </section>

        @if ($errors->any())
            <section class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-900" role="alert">
                <p class="font-semibold">No se ejecutó la operación.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </section>
        @endif

        @php($result = session('deployment_result'))
        @if ($result)
            <section @class([
                'rounded-2xl border p-5',
                'border-emerald-200 bg-emerald-50 text-emerald-950' => $result['successful'],
                'border-red-200 bg-red-50 text-red-950' => ! $result['successful'],
            ]) role="status">
                <p class="font-semibold">{{ $result['title'] }}: {{ $result['successful'] ? 'operación completada.' : 'la operación terminó con errores.' }}</p>
                <pre class="mt-4 max-h-96 overflow-auto whitespace-pre-wrap rounded-xl bg-slate-950 p-4 font-mono text-xs leading-6 text-slate-200">{{ $result['output'] }}</pre>
            </section>
        @endif

        <section class="flex flex-col gap-4">
            <div>
                <p class="text-sm font-semibold text-cyan-700">Diagnóstico en vivo</p>
                <h2 class="mt-1 text-2xl font-semibold tracking-tight">Estado del servidor</h2>
            </div>

            <article @class([
                'rounded-3xl border bg-white p-5 shadow-sm sm:p-6',
                'border-emerald-200' => $status['git']['enabled'] && $status['git']['available'] && $status['git']['writable'] && $status['git']['clean'] && $status['git']['branch'] === $status['git']['target'],
                'border-amber-300' => ! ($status['git']['enabled'] && $status['git']['available'] && $status['git']['writable'] && $status['git']['clean'] && $status['git']['branch'] === $status['git']['target']),
            ])>
                <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-lg font-semibold">Repositorio Git</h3>
                            <span @class([
                                'rounded-full px-2.5 py-1 text-xs font-bold',
                                'bg-emerald-100 text-emerald-700' => $status['git']['enabled'] && $status['git']['available'] && $status['git']['writable'] && $status['git']['clean'] && $status['git']['branch'] === $status['git']['target'],
                                'bg-amber-100 text-amber-800' => ! ($status['git']['enabled'] && $status['git']['available'] && $status['git']['writable'] && $status['git']['clean'] && $status['git']['branch'] === $status['git']['target']),
                            ])>
                                {{ $status['git']['enabled'] && $status['git']['available'] && $status['git']['writable'] && $status['git']['clean'] && $status['git']['branch'] === $status['git']['target'] ? 'Listo para actualizar' : 'Requiere atención' }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm leading-6 text-slate-500">La actualización usa avance rápido únicamente; nunca crea merges automáticos ni solicita credenciales interactivas.</p>
                    </div>

                    <dl class="grid gap-3 text-sm sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <dt class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Rama</dt>
                            <dd class="mt-1 font-semibold text-slate-800">{{ $status['git']['branch'] ?: 'No disponible' }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <dt class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Destino</dt>
                            <dd class="mt-1 font-semibold text-slate-800">{{ $status['git']['remote'] }}/{{ $status['git']['target'] }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <dt class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Commit</dt>
                            <dd class="mt-1 font-mono font-semibold text-slate-800">{{ $status['git']['commit'] ?: 'No disponible' }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <dt class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Modo</dt>
                            <dd class="mt-1 font-semibold text-slate-800">PHP directo, sin sudo</dd>
                        </div>
                    </dl>
                </div>

                @if ($status['git']['error'])
                    <p class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-sm leading-5 text-amber-900">{{ $status['git']['error'] }}</p>
                @endif

                @if (! $status['git']['writable'])
                    <div class="mt-3 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-xs leading-5 text-sky-900">
                        <p class="font-semibold">Acceso directo de Git no configurado</p>
                        <p class="mt-1">Ejecute una sola vez como propietario del repositorio, sin <code>sudo</code>:</p>
                        <code class="mt-2 block overflow-x-auto rounded-lg bg-slate-950 px-3 py-2 text-slate-100">WEB_USER=apache PROJECT_PATH={{ base_path() }} bash install-git-direct.sh</code>
                        <p class="mt-2 text-sky-800">El instalador usa ACL para permitir la actualización y conserva <code>.env</code> como solo lectura. No use <code>chmod 777</code>.</p>
                    </div>
                @endif
            </article>

            <article @class([
                'rounded-3xl border bg-white p-5 shadow-sm sm:p-6',
                'border-emerald-200' => $status['database']['connected'],
                'border-red-300' => ! $status['database']['connected'],
            ])>
                <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                    <div class="flex items-start gap-4">
                        <span @class([
                            'grid h-12 w-12 shrink-0 place-items-center rounded-2xl',
                            'bg-emerald-100 text-emerald-700' => $status['database']['connected'],
                            'bg-red-100 text-red-700' => ! $status['database']['connected'],
                        ])>
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v6c0 1.7 3.6 3 8 3s8-1.3 8-3V5M4 11v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/></svg>
                        </span>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-semibold">Base de datos</h3>
                                <span @class([
                                    'rounded-full px-2.5 py-1 text-xs font-bold',
                                    'bg-emerald-100 text-emerald-700' => $status['database']['connected'],
                                    'bg-red-100 text-red-700' => ! $status['database']['connected'],
                                ])>{{ $status['database']['connected'] ? 'Conexión verificada' : 'Sin conexión' }}</span>
                            </div>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                {{ $status['database']['connected']
                                    ? 'Laravel ejecutó SELECT 1 correctamente sobre la conexión configurada.'
                                    : 'Laravel no pudo completar la prueba de conexión.' }}
                            </p>
                        </div>
                    </div>

                    <dl class="grid gap-3 text-sm sm:grid-cols-2 xl:grid-cols-5">
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <dt class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Motor</dt>
                            <dd class="mt-1 font-semibold text-slate-800">{{ strtoupper($status['database']['driver']) }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <dt class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Base</dt>
                            <dd class="mt-1 font-semibold text-slate-800">{{ $status['database']['database'] ?: 'No definida' }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <dt class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Servidor</dt>
                            <dd class="mt-1 font-semibold text-slate-800">{{ $status['database']['endpoint'] }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <dt class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Versión</dt>
                            <dd class="mt-1 font-semibold text-slate-800">{{ $status['database']['version'] ?: 'No disponible' }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <dt class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Modo SQL</dt>
                            <dd class="mt-1 font-semibold text-slate-800">{{ str_contains($status['database']['sql_mode'], 'ONLY_FULL_GROUP_BY') ? 'Estricto activo' : 'Estándar' }}</dd>
                        </div>
                    </dl>
                </div>

                @if ($status['database']['error'])
                    <p class="mt-4 rounded-xl bg-red-50 px-4 py-3 font-mono text-xs leading-5 text-red-800">{{ $status['database']['error'] }}</p>
                @endif
            </article>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    ['label' => 'Entorno', 'value' => $status['environment'], 'healthy' => $status['environment'] === 'production'],
                    ['label' => 'Aplicación', 'value' => $status['maintenance'] ? 'En mantenimiento' : 'Disponible', 'healthy' => ! $status['maintenance']],
                    ['label' => 'Frontend', 'value' => $status['manifest'] ? 'Build verificado' : 'Manifest incompleto', 'healthy' => $status['manifest']],
                    ['label' => 'Caché', 'value' => $status['cache'], 'healthy' => true],
                ] as $item)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs font-semibold tracking-wider text-slate-400 uppercase">{{ $item['label'] }}</span>
                            <span class="h-2.5 w-2.5 rounded-full {{ $item['healthy'] ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        </div>
                        <p class="mt-3 text-lg font-semibold text-slate-900">{{ $item['value'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="flex flex-wrap gap-2 text-xs text-slate-500">
                <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5">Laravel {{ $status['laravel'] }}</span>
                <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5">PHP {{ $status['php'] }}</span>
                <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5">Acceso: superadministrador</span>
            </div>
        </section>

        <section class="flex flex-col gap-4">
            <div>
                <p class="text-sm font-semibold text-cyan-700">Acciones protegidas</p>
                <h2 class="mt-1 text-2xl font-semibold tracking-tight">Operaciones disponibles</h2>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($operations as $operationKey => $operation)
                    @php($deployUnavailable = $operationKey === 'deploy' && ! $status['manifest'])
                    @php($gitUnavailable = $operationKey === 'git-pull' && (! $status['git']['enabled'] || ! $status['git']['available'] || ! $status['git']['writable'] || ! $status['git']['clean'] || $status['git']['branch'] !== $status['git']['target']))
                    @php($operationUnavailable = $deployUnavailable || $gitUnavailable)
                    @php($isCritical = in_array($operationKey, ['git-pull', 'deploy', 'migrate'], true))

                    <article class="flex min-h-72 flex-col rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg sm:p-6">
                        <div class="grid h-11 w-11 place-items-center rounded-2xl bg-cyan-50 text-cyan-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3v12M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-semibold text-slate-900">{{ $operation['label'] }}</h3>
                        <p class="mt-2 flex-1 text-sm leading-6 text-slate-500">{{ $operation['description'] }}</p>

                        @if ($operationKey === 'git-pull' && $status['git']['error'])
                            <p class="mt-3 rounded-xl bg-amber-50 px-3 py-2 text-xs leading-5 text-amber-900">{{ $status['git']['error'] }}</p>
                        @endif

                        <form method="POST" action="{{ route('deployment.execute') }}" class="mt-5 flex flex-col gap-3" onsubmit="return window.confirm('¿Confirma que desea ejecutar esta operación?');">
                            @csrf
                            <input type="hidden" name="operation" value="{{ $operationKey }}">

                            @if ($operation['confirmation'])
                                <label class="flex flex-col gap-2 text-xs font-semibold text-slate-600" for="confirmation-{{ $operationKey }}">
                                    <span>Escriba <code class="text-red-600">{{ $operation['confirmation'] }}</code> para confirmar</span>
                                    <input id="confirmation-{{ $operationKey }}" name="confirmation" type="text" autocomplete="off" spellcheck="false" required class="rounded-xl border border-slate-200 px-3 py-2.5 font-mono text-sm outline-none transition focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100">
                                </label>
                            @endif

                            <button type="submit" @disabled($operationUnavailable) @class([
                                'inline-flex w-full items-center justify-center rounded-xl px-4 py-3 text-sm font-bold text-white transition focus:ring-4 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500',
                                'bg-red-600 hover:bg-red-700 focus:ring-red-100' => $isCritical,
                                'bg-slate-900 hover:bg-cyan-700 focus:ring-cyan-100' => ! $isCritical,
                            ])>
                                {{ $deployUnavailable ? 'Compile el frontend primero' : ($gitUnavailable ? 'Git no está listo' : $operation['label']) }}
                            </button>
                        </form>
                    </article>
                @endforeach
            </div>
        </section>
    </main>
</body>
</html>
