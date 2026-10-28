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

    @php($activityColors = ['EMERGENCY' => 'border-red-300 bg-red-700 text-white', 'ALERT' => 'border-red-300 bg-red-600 text-white', 'CRITICAL' => 'border-rose-300 bg-rose-600 text-white', 'ERROR' => 'border-orange-300 bg-orange-500 text-white', 'WARNING' => 'border-amber-200 bg-amber-400 text-slate-950', 'NOTICE' => 'border-sky-300 bg-sky-500 text-white', 'INFO' => 'border-emerald-300 bg-emerald-500 text-white', 'DEBUG' => 'border-violet-300 bg-violet-500 text-white'])
    <section class="fallback-card overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm" aria-labelledby="activity-map-title">
        <div class="flex flex-col gap-3 border-b border-slate-200 p-5 sm:flex-row sm:items-start sm:justify-between sm:px-6">
            <div>
                <p class="text-xs font-bold tracking-[0.18em] text-cyan-700 uppercase">Visualización basada en logs</p>
                <h2 id="activity-map-title" class="mt-1 text-xl font-black text-slate-950">Mapa de actividad</h2>
                <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">Representa la frecuencia de los eventos que coinciden con los filtros. Los bloques más anchos aparecen más veces en el archivo; no representan consumo de CPU.</p>
            </div>
            <div class="flex items-center gap-3 rounded-2xl bg-slate-100 px-4 py-3">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-slate-950 text-sm font-black text-cyan-300">Σ</span>
                <span><strong class="block text-lg leading-none text-slate-950">{{ number_format($result->activityMap->total) }}</strong><small class="text-xs text-slate-500">eventos visualizados</small></span>
            </div>
        </div>

        @if ($result->activityMap->total > 0)
            <div class="bg-slate-950 p-4 sm:p-6">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-400">
                    <span>Menor frecuencia</span>
                    <span class="font-semibold text-slate-200">Ancho proporcional a la cantidad de eventos</span>
                    <span>Mayor frecuencia</span>
                </div>

                <div class="overflow-x-auto pb-2" role="img" aria-label="Mapa de frecuencia de los eventos registrados en el log">
                    <div class="grid min-w-[880px] gap-2">
                        <div class="grid grid-cols-[110px_1fr] items-stretch gap-2">
                            <div class="flex items-center rounded-xl border border-slate-700 bg-slate-900 px-3 text-[11px] font-bold tracking-wide text-slate-400 uppercase">Familias</div>
                            <div class="flex min-h-24 gap-1.5">
                                @foreach ($result->activityMap->families as $family)
                                    <article
                                        class="group relative flex min-w-32 basis-0 flex-col justify-between overflow-hidden rounded-xl border p-3 shadow-lg transition-transform hover:-translate-y-0.5 {{ $activityColors[$family['level']] ?? 'border-slate-500 bg-slate-600 text-white' }}"
                                        style="flex-grow: {{ max(1, $family['count']) }}"
                                        title="{{ $family['label'] }} · {{ $family['count'] }} eventos · último {{ $family['latest_at'] }}"
                                    >
                                        <div class="flex items-start justify-between gap-2">
                                            <span class="text-[10px] font-black tracking-wide opacity-80">{{ $family['level'] }}</span>
                                            <span class="rounded-full bg-black/20 px-2 py-0.5 text-[10px] font-black">{{ number_format($family['count']) }}</span>
                                        </div>
                                        <p class="mt-3 line-clamp-3 text-xs leading-4 font-bold">{{ $family['label'] }}</p>
                                        <div class="mt-3 flex items-center justify-between gap-2 text-[10px] font-semibold opacity-80">
                                            <span>{{ number_format($family['percentage'], 1) }}%</span>
                                            <time>{{ $family['latest_at'] }}</time>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-[110px_1fr] items-stretch gap-2">
                            <div class="flex items-center rounded-xl border border-slate-700 bg-slate-900 px-3 text-[11px] font-bold tracking-wide text-slate-400 uppercase">Severidad</div>
                            <div class="flex min-h-16 gap-1.5">
                                @foreach ($result->activityMap->levels as $level)
                                    <div
                                        class="flex min-w-28 basis-0 items-center justify-between gap-2 rounded-xl border px-3 py-2 {{ $activityColors[$level['level']] ?? 'border-slate-500 bg-slate-600 text-white' }}"
                                        style="flex-grow: {{ max(1, $level['count']) }}"
                                        title="{{ $level['level'] }}: {{ $level['count'] }} eventos"
                                    >
                                        <strong class="text-xs">{{ $level['level'] }}</strong>
                                        <span class="text-xs font-black">{{ number_format($level['percentage'], 1) }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-[110px_1fr] items-stretch gap-2">
                            <div class="flex items-center rounded-xl border border-slate-700 bg-slate-900 px-3 text-[11px] font-bold tracking-wide text-slate-400 uppercase">Fuente</div>
                            <div class="flex min-h-14 items-center justify-between gap-4 rounded-xl border border-cyan-400/40 bg-gradient-to-r from-cyan-500/30 to-blue-500/20 px-4 text-cyan-50">
                                <div class="min-w-0">
                                    <strong class="block truncate text-sm">{{ $result->selectedFile }}</strong>
                                    <span class="text-xs text-cyan-100/70">{{ number_format($result->scannedBytes / 1024, 1) }} KB analizados</span>
                                </div>
                                <span class="rounded-full bg-cyan-300 px-3 py-1 text-xs font-black text-slate-950">{{ number_format($result->activityMap->total) }} eventos</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($result->activityMap->hiddenFamilyCount > 0)
                    <p class="mt-3 text-right text-xs text-slate-400">Se muestran las familias más frecuentes; {{ number_format($result->activityMap->hiddenFamilyCount) }} agrupaciones menores permanecen disponibles en el listado.</p>
                @endif
            </div>
        @else
            <div class="p-8 text-center">
                <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-slate-100 text-xl text-slate-400">⌁</div>
                <h3 class="mt-3 font-bold text-slate-800">Sin actividad para representar</h3>
                <p class="mt-1 text-sm text-slate-500">Cambie los filtros o seleccione otro archivo de log.</p>
            </div>
        @endif
    </section>

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
