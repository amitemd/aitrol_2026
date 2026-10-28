@extends('observability.layout')

@section('title', 'Logs y diagnóstico')

@section('content')
    <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 to-slate-800 p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold tracking-[0.2em] text-cyan-300 uppercase">Diagnóstico técnico</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">Logs del sistema</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">Consulte errores, advertencias, información y depuración de Laravel. Los secretos se ocultan antes de mostrarse.</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                Archivo: <strong class="text-white">{{ $result->selectedFile ?? 'Sin archivos disponibles' }}</strong>
            </div>
        </div>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-8" aria-label="Resumen por nivel">
        @php($levelStyles = ['EMERGENCY' => 'border-red-300 bg-red-50 text-red-800', 'ALERT' => 'border-red-300 bg-red-50 text-red-800', 'CRITICAL' => 'border-rose-300 bg-rose-50 text-rose-800', 'ERROR' => 'border-orange-300 bg-orange-50 text-orange-800', 'WARNING' => 'border-amber-300 bg-amber-50 text-amber-800', 'NOTICE' => 'border-blue-300 bg-blue-50 text-blue-800', 'INFO' => 'border-emerald-300 bg-emerald-50 text-emerald-800', 'DEBUG' => 'border-violet-300 bg-violet-50 text-violet-800'])
        @foreach ($levels as $level)
            <article class="rounded-2xl border p-4 {{ $levelStyles[$level] }}">
                <p class="text-[11px] font-bold tracking-wide">{{ $level }}</p>
                <p class="mt-1 text-2xl font-black">{{ number_format($result->levelCounts[$level] ?? 0) }}</p>
            </article>
        @endforeach
    </section>

    @php($flameGraph = $result->activityMap->flameGraph)
    <section class="fallback-card overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm" aria-labelledby="activity-map-title">
        <div class="grid gap-6 p-5 sm:p-6 xl:grid-cols-[1.25fr_0.75fr] xl:items-center">
            <div>
                <p class="text-xs font-bold tracking-[0.18em] text-cyan-700 uppercase">Observabilidad basada en logs</p>
                <h2 id="activity-map-title" class="mt-1 text-2xl font-black text-slate-950">Análisis de rendimiento y cuellos de botella</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Identifique cuándo se ejecutó cada proceso, cuánto demoró y qué cadena técnica utilizó. La cronología combina tiempos, auditoría y eventos anómalos para profundizar en cada caso.</p>

                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-orange-200 bg-orange-50 p-4">
                        <span class="text-[11px] font-black tracking-wide text-orange-700 uppercase">Eje X · fecha y hora</span>
                        <strong class="mt-1 block text-xl text-slate-950">{{ number_format($flameGraph->timeline['span_ms'], 1) }} ms</strong>
                        <p class="mt-1 text-xs leading-5 text-slate-600">Ubica cada proceso según el momento real en que se ejecutó.</p>
                    </div>
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                        <span class="text-[11px] font-black tracking-wide text-rose-700 uppercase">Eje Y · duración</span>
                        <strong class="mt-1 block text-xl text-slate-950">{{ number_format($flameGraph->analysis['maximum_duration_ms'], 1) }} ms</strong>
                        <p class="mt-1 text-xs leading-5 text-slate-600">La altura de cada proceso representa cuánto demoró.</p>
                    </div>
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                        <span class="text-[11px] font-black tracking-wide text-amber-700 uppercase">Trazas reales</span>
                        <strong class="mt-1 block text-xl text-slate-950">{{ number_format($flameGraph->tracedSamples) }}</strong>
                        <p class="mt-1 text-xs leading-5 text-slate-600">{{ number_format($flameGraph->derivedSamples) }} rutas fueron derivadas del contexto del log.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-slate-950 p-5 text-white shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold tracking-[0.16em] text-orange-300 uppercase">Diagnóstico interactivo</span>
                        <h3 class="mt-1 text-xl font-black">Encuentre primero lo más lento</h3>
                    </div>
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-amber-300 to-rose-500 text-xl text-slate-950">♨</span>
                </div>
                <p class="mt-3 text-sm leading-6 text-slate-300">Abra un resumen priorizado y, cuando necesite más evidencia, cambie a la cronología o siga la cadena jerárquica de llamadas.</p>
                <button
                    type="button"
                    data-flame-open
                    class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-orange-400 to-rose-500 px-5 py-3 text-sm font-black text-white shadow-lg transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                    @disabled($flameGraph->profiles === [])
                >
                    <span aria-hidden="true">▰</span>
                    Abrir diagnóstico
                </button>
                <p class="mt-3 text-[11px] leading-5 text-slate-400">La duración corresponde a tiempo de pared registrado en el log, no a muestras directas de CPU.</p>
            </div>
        </div>
    </section>

    @php($performance = $flameGraph->analysis)
    <dialog id="log-flame-graph-modal" class="fixed inset-0 z-50 m-0 h-dvh max-h-none w-screen max-w-none bg-slate-100 p-0 text-slate-900 backdrop:bg-slate-950/70">
        <div class="flex h-dvh min-h-0 flex-col bg-slate-100">
            <header class="shrink-0 border-b border-slate-200 bg-white px-4 py-3 shadow-sm sm:px-6">
                <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-700 font-black text-white shadow-lg shadow-blue-200">AR</span>
                        <div class="min-w-0">
                            <p class="truncate text-[11px] font-bold tracking-[0.17em] text-cyan-700 uppercase">{{ $result->selectedFile }}</p>
                            <h2 class="truncate text-lg font-black text-slate-950 sm:text-xl">Diagnóstico de rendimiento</h2>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <nav class="flex rounded-xl border border-slate-200 bg-slate-100 p-1" aria-label="Vistas de rendimiento">
                            <button type="button" data-flame-mode-option="diagnosis" class="rounded-lg bg-blue-700 px-3 py-2 text-xs font-black text-white shadow-sm outline-none focus-visible:ring-2 focus-visible:ring-blue-500">Diagnóstico</button>
                            <button type="button" data-flame-mode-option="timeline" class="rounded-lg px-3 py-2 text-xs font-bold text-slate-600 outline-none hover:bg-white focus-visible:ring-2 focus-visible:ring-blue-500">Procesos y auditoría</button>
                            <button type="button" data-flame-mode-option="stack" class="rounded-lg px-3 py-2 text-xs font-bold text-slate-600 outline-none hover:bg-white focus-visible:ring-2 focus-visible:ring-blue-500">Pilas</button>
                        </nav>
                        <label data-flame-profile-wrapper class="hidden items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                            Métrica
                            <select data-flame-profile class="bg-transparent font-semibold text-slate-900 outline-none">
                                @foreach ($flameGraph->profiles as $profile)
                                    <option value="{{ $profile['key'] }}">{{ $profile['label'] }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label data-flame-search-wrapper class="hidden min-w-64 flex-1 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-500 sm:w-72 sm:flex-none">
                            <span aria-hidden="true">⌕</span>
                            <input data-flame-search type="search" placeholder="Buscar proceso o función" class="min-w-0 flex-1 bg-transparent text-sm text-slate-900 outline-none placeholder:text-slate-400">
                        </label>
                        <div data-flame-timeline-controls class="hidden items-center gap-1 rounded-xl border border-slate-200 bg-white p-1 text-xs font-bold text-slate-600" aria-label="Navegación de procesos">
                            <label class="flex items-center gap-1 px-2">
                                Mostrar
                                <select data-flame-timeline-page-size class="bg-transparent font-black text-slate-900 outline-none" aria-label="Procesos por página">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                </select>
                            </label>
                            <button type="button" data-flame-timeline-previous class="grid h-8 w-8 place-items-center rounded-lg border border-slate-200 bg-slate-50 text-base hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-40" aria-label="Procesos anteriores">‹</button>
                            <span data-flame-timeline-page class="min-w-20 px-2 text-center text-[11px] font-black text-slate-800">1 / 1</span>
                            <button type="button" data-flame-timeline-next class="grid h-8 w-8 place-items-center rounded-lg border border-slate-200 bg-slate-50 text-base hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-40" aria-label="Procesos siguientes">›</button>
                        </div>
                        <button type="button" data-flame-orientation class="hidden rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Orientación: raíz abajo</button>
                        <button type="button" data-flame-reset class="hidden rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Restablecer zoom</button>
                        <button type="button" data-flame-close class="grid h-10 w-10 place-items-center rounded-xl bg-slate-900 text-xl font-black text-white hover:bg-slate-700" aria-label="Cerrar visualizador">×</button>
                    </div>
                </div>
            </header>

            <div class="grid min-h-0 flex-1 grid-rows-[auto_1fr_auto]">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-blue-100 bg-blue-50 px-4 py-2 text-xs text-slate-600 sm:px-6">
                    <div class="flex flex-wrap gap-x-5 gap-y-1">
                        <span><span data-flame-total-caption>Tiempo medido</span>: <strong class="text-blue-900" data-flame-total>{{ number_format($performance['total_duration_ms'], 2) }}</strong> <span data-flame-unit>ms</span></span>
                        <span><span data-flame-count-label>Solicitudes medidas</span>: <strong class="text-blue-900" data-flame-depth>{{ $performance['measured_processes'] }}</strong></span>
                        <span data-flame-match-wrapper class="hidden">Coincidencias: <strong class="text-amber-700" data-flame-matches>0</strong></span>
                    </div>
                    <p data-flame-breadcrumb class="min-w-0 truncate font-mono text-[11px] text-cyan-800">Análisis basado exclusivamente en tiempos registrados</p>
                </div>

                <div class="flex shrink-0 flex-col gap-3 border-b border-slate-200 bg-white px-4 py-3 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex min-w-0 items-start gap-3">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-blue-100 text-sm font-black text-blue-700" aria-hidden="true">i</span>
                        <div class="min-w-0">
                            <p data-flame-context-title class="text-xs font-black text-slate-900">Diagnóstico priorizado</p>
                            <p data-flame-context-description class="mt-0.5 text-[11px] leading-5 text-slate-500">Empiece por la ruta con mayor tiempo acumulado y compare su P95 con el máximo observado.</p>
                        </div>
                    </div>
                    <div class="flex shrink-0 flex-wrap gap-2">
                        <button type="button" data-flame-jump-mode="timeline" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-[11px] font-black text-slate-700 shadow-sm outline-none hover:border-blue-300 hover:bg-blue-50 focus-visible:ring-2 focus-visible:ring-blue-500">Abrir cronología</button>
                        <button type="button" data-flame-jump-mode="stack" class="rounded-xl bg-blue-700 px-3 py-2 text-[11px] font-black text-white shadow-sm outline-none hover:bg-blue-800 focus-visible:ring-2 focus-visible:ring-blue-500">Explorar pilas</button>
                    </div>
                </div>

                <section data-flame-diagnosis class="min-h-0 overflow-y-auto bg-slate-100 px-4 py-5 sm:px-6">
                    <div class="mx-auto grid max-w-7xl gap-5">
                        @if ($performance['primary_bottleneck'])
                            <article class="grid gap-5 overflow-hidden rounded-3xl border border-blue-200 bg-white p-5 shadow-sm lg:grid-cols-[1fr_auto] lg:items-center sm:p-6">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-rose-100 px-3 py-1 text-[11px] font-black tracking-wide text-rose-700 uppercase">Principal candidato a cuello de botella</span>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ $performance['primary_bottleneck']['requests'] }} solicitudes</span>
                                    </div>
                                    <h3 class="mt-3 break-words text-xl font-black text-slate-950 sm:text-2xl">{{ $performance['primary_bottleneck']['route'] }}</h3>
                                    @if ($performance['primary_bottleneck']['controller'])
                                        <p class="mt-1 break-all font-mono text-xs text-slate-500">{{ $performance['primary_bottleneck']['controller'] }}</p>
                                    @endif
                                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">Esta ruta concentra el <strong class="text-slate-950">{{ number_format($performance['primary_bottleneck']['contribution_percentage'], 1) }}%</strong> del tiempo registrado. Revise primero sus consultas, servicios externos y operaciones repetidas.</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:min-w-[430px]">
                                    <div class="rounded-2xl bg-blue-50 p-4"><span class="text-[10px] font-black tracking-wide text-blue-700 uppercase">Acumulado</span><strong class="mt-1 block text-xl text-blue-950">{{ number_format($performance['primary_bottleneck']['total_duration_ms'], 2) }} ms</strong></div>
                                    <div class="rounded-2xl bg-cyan-50 p-4"><span class="text-[10px] font-black tracking-wide text-cyan-700 uppercase">Promedio</span><strong class="mt-1 block text-xl text-cyan-950">{{ number_format($performance['primary_bottleneck']['average_duration_ms'], 2) }} ms</strong></div>
                                    <div class="col-span-2 rounded-2xl bg-rose-50 p-4 sm:col-span-1"><span class="text-[10px] font-black tracking-wide text-rose-700 uppercase">Máximo</span><strong class="mt-1 block text-xl text-rose-950">{{ number_format($performance['primary_bottleneck']['maximum_duration_ms'], 2) }} ms</strong></div>
                                </div>
                            </article>
                        @else
                            <article class="rounded-3xl border border-amber-200 bg-amber-50 p-6 text-amber-950">
                                <h3 class="font-black">Todavía no hay tiempos suficientes para detectar un cuello de botella</h3>
                                <p class="mt-2 text-sm leading-6">Los eventos deben incluir <code class="rounded bg-white px-1.5 py-0.5">duration_ms</code>. Los errores y eventos sin duración siguen disponibles en la cronología y la vista de pilas.</p>
                            </article>
                        @endif

                        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><span class="text-[11px] font-black tracking-wide text-slate-500 uppercase">Solicitudes medidas</span><strong class="mt-2 block text-2xl text-slate-950">{{ number_format($performance['measured_processes']) }}</strong><p class="mt-1 text-xs text-slate-500">{{ number_format($performance['unmeasured_processes']) }} eventos sin duración</p></article>
                            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><span class="text-[11px] font-black tracking-wide text-slate-500 uppercase">Promedio</span><strong class="mt-2 block text-2xl text-slate-950">{{ number_format($performance['average_duration_ms'], 2) }} ms</strong><p class="mt-1 text-xs text-slate-500">Tiempo medio registrado</p></article>
                            <article class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm"><span class="text-[11px] font-black tracking-wide text-amber-700 uppercase">Percentil 95</span><strong class="mt-2 block text-2xl text-amber-950">{{ number_format($performance['p95_duration_ms'], 2) }} ms</strong><p class="mt-1 text-xs text-amber-700">El 95% finaliza antes de este valor</p></article>
                            <article class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm"><span class="text-[11px] font-black tracking-wide text-rose-700 uppercase">Solicitud más lenta</span><strong class="mt-2 block text-2xl text-rose-950">{{ number_format($performance['maximum_duration_ms'], 2) }} ms</strong><p class="mt-1 text-xs text-rose-700">Máximo observado en el archivo</p></article>
                        </div>

                        <div class="grid gap-5 xl:grid-cols-[1.15fr_0.85fr]">
                            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                                <div class="flex flex-wrap items-end justify-between gap-2">
                                    <div><p class="text-[11px] font-black tracking-[0.16em] text-blue-700 uppercase">Impacto acumulado</p><h3 class="mt-1 text-lg font-black text-slate-950">Rutas que consumen más tiempo</h3></div>
                                    <span class="text-xs text-slate-500">Ordenadas por tiempo total</span>
                                </div>
                                <div class="mt-5 grid gap-4">
                                    @forelse ($performance['route_bottlenecks'] as $index => $route)
                                        <article class="grid gap-2">
                                            <div class="flex min-w-0 items-center justify-between gap-3 text-xs">
                                                <span class="min-w-0 break-words font-bold text-slate-800"><b class="mr-2 text-blue-700">#{{ $index + 1 }}</b>{{ $route['route'] }}</span>
                                                <strong class="shrink-0 text-slate-950">{{ number_format($route['total_duration_ms'], 2) }} ms</strong>
                                            </div>
                                            <div class="h-3 overflow-hidden rounded-full bg-slate-100" title="{{ number_format($route['contribution_percentage'], 2) }}% del tiempo medido">
                                                <div class="h-full rounded-full bg-gradient-to-r from-cyan-500 via-blue-600 to-indigo-700" style="width: {{ max(2, min(100, $route['contribution_percentage'])) }}%"></div>
                                            </div>
                                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-slate-500"><span>{{ $route['requests'] }} solicitudes</span><span>Promedio {{ number_format($route['average_duration_ms'], 2) }} ms</span><span>P95 {{ number_format($route['p95_duration_ms'], 2) }} ms</span><span>{{ number_format($route['contribution_percentage'], 1) }}%</span></div>
                                        </article>
                                    @empty
                                        <p class="rounded-2xl bg-slate-50 p-5 text-sm text-slate-500">No hay rutas con duración registrada.</p>
                                    @endforelse
                                </div>
                            </section>

                            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                                <div><p class="text-[11px] font-black tracking-[0.16em] text-rose-700 uppercase">Casos individuales</p><h3 class="mt-1 text-lg font-black text-slate-950">Solicitudes más lentas</h3></div>
                                <div class="mt-5 grid gap-3">
                                    @forelse ($performance['slowest_processes'] as $process)
                                        @php($riskClass = match ($process['risk']) { 'critical' => 'border-rose-200 bg-rose-50 text-rose-800', 'warning' => 'border-amber-200 bg-amber-50 text-amber-800', default => 'border-emerald-200 bg-emerald-50 text-emerald-800' })
                                        <article class="grid grid-cols-[auto_1fr_auto] items-center gap-3 rounded-2xl border p-3 {{ $riskClass }}">
                                            <span class="grid h-8 w-8 place-items-center rounded-xl bg-white/80 text-xs font-black">#{{ $process['process_number'] }}</span>
                                            <div class="min-w-0"><p class="break-words text-xs font-black">{{ collect([$process['method'], $process['route']])->filter()->join(' ') ?: $process['label'] }}</p><p class="mt-0.5 text-[10px] opacity-75">{{ $process['date'] }}</p></div>
                                            <strong class="text-xs">{{ number_format($process['duration_ms'], 2) }} ms</strong>
                                        </article>
                                    @empty
                                        <p class="rounded-2xl bg-slate-50 p-5 text-sm text-slate-500">No hay solicitudes con duración registrada.</p>
                                    @endforelse
                                </div>
                            </section>
                        </div>

                        @if ($performance['error_hotspots'] !== [])
                            <section class="overflow-hidden rounded-3xl border border-rose-200 bg-white shadow-sm">
                                <div class="flex flex-col gap-3 border-b border-rose-100 bg-rose-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                                    <div>
                                        <p class="text-[11px] font-black tracking-[0.16em] text-rose-700 uppercase">Alertas prioritarias</p>
                                        <h3 class="mt-1 text-lg font-black text-rose-950">Errores y advertencias que requieren revisión</h3>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="w-fit rounded-full bg-rose-700 px-3 py-1.5 text-xs font-black text-white">{{ number_format($performance['error_processes']) }} eventos anómalos</span>
                                        <button type="button" data-flame-show-errors class="rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-black text-rose-700 shadow-sm outline-none hover:bg-rose-100 focus-visible:ring-2 focus-visible:ring-rose-500">Analizar alertas en Pilas →</button>
                                    </div>
                                </div>
                                <div class="grid gap-3 p-5 sm:grid-cols-2 sm:p-6 xl:grid-cols-3">
                                    @foreach ($performance['error_hotspots'] as $index => $errorHotspot)
                                        <article class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                            <div class="flex items-start justify-between gap-3">
                                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-rose-100 text-xs font-black text-rose-700">#{{ $index + 1 }}</span>
                                                <span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-black tracking-wide text-rose-700 shadow-sm">{{ $errorHotspot['occurrences'] }} {{ $errorHotspot['occurrences'] === 1 ? 'vez' : 'veces' }}</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="break-words text-sm font-black leading-5 text-slate-950">{{ $errorHotspot['label'] }}</p>
                                                @if ($errorHotspot['route'])
                                                    <p class="mt-2 break-all font-mono text-[11px] text-blue-700">{{ $errorHotspot['route'] }}</p>
                                                @endif
                                                @if ($errorHotspot['controller'])
                                                    <p class="mt-1 break-all font-mono text-[10px] text-slate-500">{{ $errorHotspot['controller'] }}</p>
                                                @endif
                                            </div>
                                            <div class="flex flex-wrap gap-x-4 gap-y-1 border-t border-slate-200 pt-3 text-[10px] text-slate-500">
                                                <span>Último: {{ $errorHotspot['latest_at'] }}</span>
                                                <span>Traza: {{ $errorHotspot['maximum_trace_depth'] }} niveles</span>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        <aside class="rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-xs leading-5 text-cyan-950"><strong>Cómo interpretar el resultado:</strong> el tiempo acumulado descubre rutas que consumen recursos repetidamente; el P95 detecta lentitud que afecta a la mayoría de usuarios; y el máximo permite investigar casos aislados. Esto analiza tiempo de pared registrado en los logs, no CPU ni memoria del sistema operativo.</aside>
                    </div>
                </section>

                <section data-flame-chart class="hidden min-h-0 grid-cols-[28px_minmax(0,1fr)] bg-white xl:grid-cols-[28px_minmax(0,1fr)_360px]">
                    <div data-flame-y-axis class="flex items-center justify-center border-r border-slate-200 bg-slate-50 text-[9px] font-black tracking-[0.16em] text-slate-500 uppercase [writing-mode:vertical-rl]">Tiempo de ejecución (ms)</div>
                    <div data-flame-stage class="relative min-h-0 overflow-y-auto overflow-x-hidden p-2 sm:p-4">
                        <svg data-flame-svg class="block w-full select-none" role="img" aria-label="Cronología y pilas de procesos construidas desde los logs"></svg>
                        <div data-flame-empty class="hidden h-full place-items-center text-center text-sm text-slate-500">No hay datos para esta métrica.</div>
                    </div>
                    <aside data-flame-inspector class="col-span-2 max-h-72 min-h-0 overflow-y-auto border-t border-slate-200 bg-slate-50 p-4 xl:col-span-1 xl:max-h-none xl:border-t-0 xl:border-l">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[10px] font-black tracking-[0.15em] text-blue-700 uppercase">Acción seleccionada</p>
                                    <h3 data-flame-inspector-title class="mt-1 break-words text-sm font-black text-slate-950">Seleccione un bloque</h3>
                                </div>
                                <span data-flame-inspector-risk class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black text-slate-600">Sin selección</span>
                            </div>
                            <dl class="mt-4 grid grid-cols-2 gap-2 text-[11px]">
                                <div class="rounded-xl bg-slate-50 p-2.5"><dt class="font-bold text-slate-500">Inicio</dt><dd data-flame-inspector-date class="mt-1 font-semibold text-slate-900">—</dd></div>
                                <div class="rounded-xl bg-slate-50 p-2.5"><dt class="font-bold text-slate-500">Duración</dt><dd data-flame-inspector-duration class="mt-1 font-semibold text-slate-900">—</dd></div>
                                <div class="col-span-2 rounded-xl bg-slate-50 p-2.5"><dt class="font-bold text-slate-500">Correlación</dt><dd data-flame-inspector-correlation class="mt-1 break-all font-mono text-slate-900">—</dd></div>
                            </dl>
                        </div>

                        <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <h4 class="text-xs font-black text-slate-950">Cadena técnica</h4>
                            <p class="mt-1 text-[10px] leading-4 text-slate-500">Controlador real y dependencias asociadas por inyección.</p>
                            <div data-flame-inspector-architecture class="mt-3 grid gap-2 text-[11px]"></div>
                        </section>

                        <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="flex items-center justify-between gap-2"><h4 class="text-xs font-black text-slate-950">Auditoría correlacionada</h4><span data-flame-inspector-audit-count class="rounded-full bg-blue-50 px-2 py-1 text-[10px] font-black text-blue-700">0</span></div>
                            <div data-flame-inspector-audit class="mt-3 grid gap-2 text-[11px]"></div>
                        </section>

                        <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="flex items-center justify-between gap-2"><h4 class="text-xs font-black text-slate-950">Logs correlacionados</h4><span data-flame-inspector-log-count class="rounded-full bg-blue-50 px-2 py-1 text-[10px] font-black text-blue-700">0</span></div>
                            <div data-flame-inspector-logs class="mt-3 grid gap-2 text-[11px]"></div>
                        </section>
                    </aside>
                </section>

                <footer class="grid max-h-32 shrink-0 gap-3 overflow-y-auto overflow-x-hidden border-t border-slate-200 bg-white px-4 py-3 text-xs sm:grid-cols-[1fr_auto] sm:items-center sm:px-6">
                    <div class="min-w-0">
                        <strong data-flame-detail-label class="block break-words text-sm leading-5 text-slate-950">Resumen del diagnóstico</strong>
                        <p data-flame-detail-meta class="mt-1 break-words leading-5 text-slate-500">Seleccione Cronología o Pilas para explorar eventos individuales.</p>
                    </div>
                    <div data-flame-legend class="hidden flex-wrap items-center justify-between gap-4 text-slate-500 sm:justify-end">
                        <span><i class="mr-1 inline-block h-2.5 w-2.5 border border-dashed border-blue-500 bg-blue-50"></i>Cadena auditada, sin tiempo individual</span>
                        <span><i class="mr-1 inline-block h-2.5 w-2.5 rounded-full bg-gradient-to-br from-blue-200 to-blue-700"></i>Tiempo aún no desglosado</span>
                        <span><i class="mr-1 inline-block h-2.5 w-2.5 rounded-full bg-cyan-600"></i>INFO / NOTICE</span>
                        <span><i class="mr-1 inline-block h-2.5 w-2.5 rounded-full bg-violet-600"></i>DEBUG</span>
                        <span><i class="mr-1 inline-block h-2.5 w-2.5 rounded-full bg-amber-500"></i>WARNING</span>
                        <span><i class="mr-1 inline-block h-2.5 w-2.5 rounded-full bg-red-600"></i>ERROR / CRITICAL</span>
                        <span data-flame-x-axis class="font-semibold text-slate-700">Eje X · fecha y hora · Eje Y · duración (ms)</span>
                    </div>
                </footer>
            </div>
        </div>
    </dialog>

    <script>
        window.AitrolLogFlameGraph = {{ Js::from($flameGraph) }};
        window.AitrolAuditByProcess = {{ Js::from($auditByProcess) }};
    </script>

    <section class="fallback-card rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5 flex flex-col gap-1">
            <h2 class="text-lg font-bold">Filtros de diagnóstico</h2>
            <p class="text-sm text-slate-500">Los filtros se guardan en la sesión; no exponen datos en la URL.</p>
        </div>
        <form method="POST" action="{{ route('observability.logs.filter') }}" class="grid gap-4 xl:grid-cols-[1.1fr_2fr_1.4fr_auto] xl:items-end">
            @csrf
            <label class="grid gap-1.5 text-sm font-semibold">Archivo
                <select name="file" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 font-normal">
                    @forelse ($result->files as $file)
                        <option value="{{ $file['name'] }}" @selected($result->selectedFile === $file['name'])>{{ $file['name'] }} · {{ number_format($file['size'] / 1024, 1) }} KB</option>
                    @empty
                        <option value="">No hay archivos legibles</option>
                    @endforelse
                </select>
            </label>
            <fieldset>
                <legend class="mb-1.5 text-sm font-semibold">Niveles</legend>
                <div class="flex flex-wrap gap-2">
                    @foreach ($levels as $level)
                        <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-full border border-slate-200 px-2.5 py-1.5 text-xs font-semibold"><input type="checkbox" name="levels[]" value="{{ $level }}" @checked(in_array($level, $filters->levels, true))> {{ $level }}</label>
                    @endforeach
                </div>
            </fieldset>
            <div class="grid grid-cols-[1fr_auto] gap-3">
                <label class="grid gap-1.5 text-sm font-semibold">Texto
                    <input name="search" value="{{ $filters->search }}" maxlength="200" placeholder="Mensaje, contexto o entorno" class="rounded-xl border border-slate-300 px-3 py-2.5 font-normal">
                </label>
                <label class="grid gap-1.5 text-sm font-semibold">Filas
                    <select name="per_page" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 font-normal">@foreach ([10, 25, 50, 100] as $size)<option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>@endforeach</select>
                </label>
            </div>
            <button class="rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-cyan-700">Aplicar filtros</button>
        </form>
        <form method="POST" action="{{ route('observability.logs.reset') }}" class="mt-3">@csrf<button class="text-sm font-semibold text-slate-500 hover:text-slate-900">Limpiar filtros</button></form>
    </section>

    @if ($result->truncated)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">El archivo es grande. Se analizaron los últimos {{ number_format($result->scannedBytes / 1024 / 1024, 1) }} MB de {{ number_format($result->fileSize / 1024 / 1024, 1) }} MB para mantener una respuesta rápida.</div>
    @endif

    <section class="fallback-card overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-2 border-b border-slate-200 p-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div><h2 class="text-lg font-bold">Eventos registrados</h2><p class="text-sm text-slate-500">{{ number_format($result->entries->total()) }} coincidencias</p></div>
            <p class="text-xs text-slate-400">Lectura de solo diagnóstico</p>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($result->entries as $entry)
                <article class="p-5 sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full border px-2.5 py-1 text-[11px] font-black {{ $levelStyles[$entry['level']] ?? 'border-slate-300 bg-slate-50 text-slate-700' }}">{{ $entry['level'] }}</span>
                                <time class="font-mono text-xs text-slate-500">{{ $entry['date'] }}</time>
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-[11px] font-semibold text-slate-600">{{ $entry['environment'] }}</span>
                            </div>
                            <p class="mt-3 break-words text-sm font-semibold leading-6 text-slate-800">{{ $entry['message'] }}</p>
                        </div>
                    </div>
                    @if ($entry['details'] !== '')
                        <details class="mt-4 rounded-xl border border-slate-200 bg-slate-50">
                            <summary class="cursor-pointer px-4 py-3 text-sm font-semibold text-slate-700">Ver contexto y traza</summary>
                            <pre class="max-h-96 overflow-auto border-t border-slate-200 p-4 text-xs leading-5 whitespace-pre-wrap text-slate-700">{{ $entry['details'] }}</pre>
                        </details>
                    @endif
                </article>
            @empty
                <div class="p-10 text-center text-sm text-slate-500">No existen eventos que coincidan con los filtros.</div>
            @endforelse
        </div>
        @if ($result->entries->hasPages())<div class="border-t border-slate-200 p-5">{{ $result->entries->links() }}</div>@endif
    </section>
@endsection
