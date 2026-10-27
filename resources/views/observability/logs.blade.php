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
