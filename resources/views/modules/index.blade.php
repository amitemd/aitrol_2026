<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Mapa funcional de módulos de AITROL migrados a Laravel 13">
    <title>Mapa de módulos · AITROL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -left-40 -top-40 h-96 w-96 rounded-full bg-cyan-500/15 blur-3xl"></div>
        <div class="absolute -right-40 top-1/3 h-[30rem] w-[30rem] rounded-full bg-blue-600/15 blur-3xl"></div>
    </div>

    <main class="relative mx-auto max-w-7xl px-5 py-10 sm:px-8 lg:py-16">
        <header class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.06] p-7 shadow-2xl shadow-cyan-950/30 backdrop-blur-xl sm:p-10">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-cyan-400/10 px-3 py-1.5 text-xs font-semibold tracking-[0.2em] text-cyan-300 uppercase">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_12px_#34d399]"></span>
                        Laravel 13 · Arquitectura migrada
                    </div>
                    <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-6xl">
                        El sistema completo,
                        <span class="bg-gradient-to-r from-cyan-300 to-blue-400 bg-clip-text text-transparent">módulo por módulo.</span>
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">
                        Inventario visual del proyecto Sucursales IECED. Cada tarjeta explica la responsabilidad funcional conservada dentro de la nueva base AITROL.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:min-w-[32rem]">
                    @foreach ([['value' => $stats['modules'], 'label' => 'Módulos'], ['value' => number_format($stats['routes']), 'label' => 'Rutas'], ['value' => number_format($stats['views']), 'label' => 'Vistas'], ['value' => number_format($stats['legacyFiles']), 'label' => 'Archivos PHP']] as $stat)
                        <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
                            <div class="text-2xl font-semibold text-white">{{ $stat['value'] }}</div>
                            <div class="mt-1 text-xs font-medium tracking-wide text-slate-400 uppercase">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </header>

        <section class="mt-8 rounded-3xl border border-white/10 bg-slate-900/70 p-5 backdrop-blur sm:p-6" aria-label="Filtros del catálogo">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                <label class="relative flex-1">
                    <span class="sr-only">Buscar módulos</span>
                    <svg class="pointer-events-none absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke-width="2"/><path d="m20 20-4-4" stroke-width="2" stroke-linecap="round"/></svg>
                    <input id="module-search" type="search" placeholder="Buscar por módulo, función o capacidad…" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 py-3.5 pr-4 pl-12 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-400/50 focus:ring-4 focus:ring-cyan-400/10">
                </label>
                <div id="category-filters" class="flex gap-2 overflow-x-auto pb-1 lg:max-w-2xl" role="group" aria-label="Categorías">
                    <button type="button" data-category="all" class="category-filter shrink-0 rounded-full bg-cyan-400 px-4 py-2 text-xs font-semibold text-slate-950">Todos</button>
                    @foreach ($categories as $category)
                        <button type="button" data-category="{{ Str::slug($category) }}" class="category-filter shrink-0 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold text-slate-300 transition hover:border-cyan-400/30 hover:text-white">{{ $category }}</button>
                    @endforeach
                </div>
            </div>
            <p id="results-count" class="mt-4 text-sm text-slate-400" aria-live="polite">Mostrando {{ $modules->count() }} módulos</p>
        </section>

        <section id="module-grid" class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($modules as $module)
                <article
                    class="module-card group flex flex-col rounded-3xl border border-white/10 bg-gradient-to-b from-white/[0.08] to-white/[0.03] p-6 transition duration-300 hover:-translate-y-1 hover:border-cyan-300/30 hover:shadow-2xl hover:shadow-cyan-950/30"
                    data-category="{{ Str::slug($module['category']) }}"
                    data-search="{{ Str::lower($module['name'].' '.$module['category'].' '.$module['description'].' '.implode(' ', $module['capabilities'])) }}"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <span class="inline-flex rounded-full border border-white/10 bg-white/[0.06] px-2.5 py-1 text-[0.68rem] font-semibold tracking-wider text-cyan-300 uppercase">{{ $module['category'] }}</span>
                            <h2 class="mt-4 text-xl font-semibold tracking-tight text-white group-hover:text-cyan-200">{{ $module['name'] }}</h2>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/60 px-3 py-2 text-center">
                            <div class="text-lg font-semibold text-white">{{ number_format($module['routes']) }}</div>
                            <div class="text-[0.62rem] tracking-wider text-slate-500 uppercase">rutas</div>
                        </div>
                    </div>

                    <p class="mt-4 flex-1 text-sm leading-6 text-slate-300">{{ $module['description'] }}</p>

                    <ul class="mt-5 flex flex-wrap gap-2" aria-label="Funciones principales">
                        @foreach ($module['capabilities'] as $capability)
                            <li class="rounded-lg bg-cyan-400/[0.07] px-2.5 py-1.5 text-xs text-cyan-100/80">{{ $capability }}</li>
                        @endforeach
                    </ul>

                    <div class="mt-6 flex items-center justify-between border-t border-white/10 pt-4 text-xs">
                        <code class="max-w-[72%] truncate text-slate-500" title="{{ $module['source'] }}">{{ $module['source'] }}</code>
                        <span class="inline-flex items-center gap-1.5 font-medium text-emerald-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            Integrado
                        </span>
                    </div>
                </article>
            @endforeach
        </section>

        <div id="empty-state" class="mt-8 hidden rounded-3xl border border-dashed border-white/15 bg-white/[0.03] px-6 py-16 text-center">
            <p class="text-lg font-medium text-white">No encontramos módulos con esos filtros.</p>
            <p class="mt-2 text-sm text-slate-400">Prueba otra palabra o selecciona “Todos”.</p>
        </div>

        <footer class="mt-12 flex flex-col gap-3 border-t border-white/10 pt-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <p>AITROL · Migración estructural desde Sucursales IECED</p>
            <p>Inventario generado sobre el código fuente heredado</p>
        </footer>
    </main>
</body>
</html>
