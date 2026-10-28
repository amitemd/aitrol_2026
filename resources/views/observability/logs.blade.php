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
                <p class="text-xs font-bold tracking-[0.18em] text-cyan-700 uppercase">Perfil jerárquico basado en logs</p>
                <h2 id="activity-map-title" class="mt-1 text-2xl font-black text-slate-950">Flame Graph de actividad</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">El ancho representa frecuencia relativa o duración registrada; la altura representa profundidad de la traza. Las ramas se ordenan alfabéticamente para agrupar caminos equivalentes.</p>

                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-orange-200 bg-orange-50 p-4">
                        <span class="text-[11px] font-black tracking-wide text-orange-700 uppercase">Eje X · ancho</span>
                        <strong class="mt-1 block text-xl text-slate-950">{{ number_format($flameGraph->samples) }} muestras</strong>
                        <p class="mt-1 text-xs leading-5 text-slate-600">Más ancho significa mayor presencia en los logs.</p>
                    </div>
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                        <span class="text-[11px] font-black tracking-wide text-rose-700 uppercase">Eje Y · profundidad</span>
                        <strong class="mt-1 block text-xl text-slate-950">{{ number_format($flameGraph->maximumDepth) }} niveles</strong>
                        <p class="mt-1 text-xs leading-5 text-slate-600">De la raíz hasta el frame o ruta final.</p>
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
                        <span class="text-xs font-bold tracking-[0.16em] text-orange-300 uppercase">Visualizador interactivo</span>
                        <h3 class="mt-1 text-xl font-black">Analice cada pila</h3>
                    </div>
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-amber-300 to-rose-500 text-xl text-slate-950">♨</span>
                </div>
                <p class="mt-3 text-sm leading-6 text-slate-300">Busque funciones, amplíe una rama y alterne entre flame graph e icicle graph en pantalla completa.</p>
                <button
                    type="button"
                    data-flame-open
                    class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-orange-400 to-rose-500 px-5 py-3 text-sm font-black text-white shadow-lg transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                    @disabled($flameGraph->profiles === [])
                >
                    <span aria-hidden="true">▰</span>
                    Abrir Flame Graph
                </button>
                <p class="mt-3 text-[11px] leading-5 text-slate-400">La duración corresponde a tiempo de pared registrado en el log, no a muestras directas de CPU.</p>
            </div>
        </div>
    </section>

    <dialog id="log-flame-graph-modal" class="fixed inset-0 z-50 m-0 h-dvh max-h-none w-screen max-w-none bg-slate-950 p-0 text-white backdrop:bg-slate-950/90">
        <div class="flex h-dvh min-h-0 flex-col bg-slate-950">
            <header class="shrink-0 border-b border-white/10 bg-slate-950/95 px-4 py-3 backdrop-blur sm:px-6">
                <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-amber-300 to-rose-500 font-black text-slate-950">FG</span>
                        <div class="min-w-0">
                            <p class="truncate text-[11px] font-bold tracking-[0.17em] text-orange-300 uppercase">{{ $result->selectedFile }}</p>
                            <h2 class="truncate text-lg font-black sm:text-xl">Flame Graph de logs</h2>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <label class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-bold text-slate-300">
                            Métrica
                            <select data-flame-profile class="bg-transparent font-semibold text-white outline-none">
                                @foreach ($flameGraph->profiles as $profile)
                                    <option value="{{ $profile['key'] }}" class="bg-slate-900">{{ $profile['label'] }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="flex min-w-52 flex-1 items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs text-slate-300 sm:flex-none">
                            <span aria-hidden="true">⌕</span>
                            <input data-flame-search type="search" placeholder="Buscar frame o función" class="min-w-0 flex-1 bg-transparent text-sm text-white outline-none placeholder:text-slate-500">
                        </label>
                        <button type="button" data-flame-orientation class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-bold text-slate-200 hover:bg-white/10">Vista: llama</button>
                        <button type="button" data-flame-reset class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-bold text-slate-200 hover:bg-white/10">Restablecer zoom</button>
                        <button type="button" data-flame-close class="grid h-10 w-10 place-items-center rounded-xl bg-white text-xl font-black text-slate-950 hover:bg-slate-200" aria-label="Cerrar visualizador">×</button>
                    </div>
                </div>
            </header>

            <div class="grid min-h-0 flex-1 grid-rows-[auto_1fr_auto]">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 bg-slate-900 px-4 py-2 text-xs text-slate-400 sm:px-6">
                    <div class="flex flex-wrap gap-x-5 gap-y-1">
                        <span><strong class="text-white" data-flame-total>0</strong> <span data-flame-unit>muestras</span></span>
                        <span>Profundidad: <strong class="text-white" data-flame-depth>{{ $flameGraph->maximumDepth }}</strong></span>
                        <span>Coincidencias: <strong class="text-amber-300" data-flame-matches>0</strong></span>
                    </div>
                    <p data-flame-breadcrumb class="min-w-0 truncate font-mono text-[11px] text-cyan-300">AITROL</p>
                </div>

                <div class="grid min-h-0 grid-cols-[42px_1fr] bg-[#17130f]">
                    <div class="flex items-center justify-center border-r border-white/10 bg-slate-950 text-[10px] font-black tracking-[0.2em] text-slate-500 uppercase [writing-mode:vertical-rl]">Eje Y · profundidad de pila</div>
                    <div data-flame-stage class="relative min-h-0 overflow-auto p-3 sm:p-5">
                        <svg data-flame-svg class="block min-w-[960px] select-none" role="img" aria-label="Flame graph jerárquico construido desde los logs"></svg>
                        <div data-flame-empty class="hidden h-full place-items-center text-center text-sm text-slate-400">No hay datos para esta métrica.</div>
                    </div>
                </div>

                <footer class="grid shrink-0 gap-3 border-t border-white/10 bg-slate-950 px-4 py-3 text-xs sm:grid-cols-[1fr_auto] sm:items-center sm:px-6">
                    <div class="min-w-0">
                        <strong data-flame-detail-label class="block truncate text-sm text-white">Seleccione un bloque para ver su detalle</strong>
                        <p data-flame-detail-meta class="mt-1 truncate text-slate-400">El borde superior expuesto representa trabajo propio sin hijos registrados.</p>
                    </div>
                    <div class="flex items-center justify-between gap-4 text-slate-400 sm:justify-end">
                        <span><i class="mr-1 inline-block h-2.5 w-2.5 rounded-sm bg-orange-400"></i>Traza real</span>
                        <span><i class="mr-1 inline-block h-2.5 w-2.5 rounded-sm bg-cyan-400"></i>Ruta derivada</span>
                        <span class="font-semibold text-slate-300">Eje X · proporción del perfil</span>
                    </div>
                </footer>
            </div>
        </div>
    </dialog>

    <script>
        window.AitrolLogFlameGraph = {{ Js::from($flameGraph) }};
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
