@props([
    'title',
    'user',
    'companies',
    'currentCompany' => null,
])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · AITROL Nuevo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[18rem_minmax(0,1fr)]">
        <aside class="hidden min-h-screen flex-col bg-slate-950 px-5 py-6 text-slate-200 lg:flex">
            <a href="{{ route('new-system.dashboard') }}" class="flex items-center gap-3 rounded-2xl px-3 py-2">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-cyan-400 font-black text-slate-950">A</span>
                <span>
                    <span class="block text-lg font-semibold tracking-tight text-white">AITROL</span>
                    <span class="block text-[0.65rem] font-semibold tracking-[0.24em] text-cyan-300 uppercase">Nueva plataforma</span>
                </span>
            </a>

            <nav class="mt-10 flex flex-1 flex-col gap-1" aria-label="Navegación principal">
                <a href="{{ route('new-system.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition {{ request()->routeIs('new-system.dashboard') ? 'bg-cyan-400 text-slate-950' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <span class="text-lg">⌂</span>
                    Inicio
                </a>
                <a href="{{ route('new-system.companies.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition {{ request()->routeIs('new-system.companies.*') ? 'bg-cyan-400 text-slate-950' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <span class="text-lg">◇</span>
                    Empresas
                </a>
                @can('manage-new-system-users')
                    <a href="{{ route('new-system.users.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition {{ request()->routeIs('new-system.users.*') ? 'bg-cyan-400 text-slate-950' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <span class="text-lg">◎</span>
                        Usuarios
                    </a>
                    @if (Route::has('deployment.index'))
                        <a href="{{ route('deployment.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                            <span class="text-lg">⚙</span>
                            Despliegue
                        </a>
                    @endif
                @endcan
            </nav>

            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold tracking-wider text-slate-500 uppercase">Sistema heredado</p>
                <p class="mt-2 text-sm leading-5 text-slate-300">Los módulos pendientes siguen disponibles durante la transición.</p>
                <a href="{{ url('/dashboard') }}" class="mt-4 inline-flex text-sm font-semibold text-cyan-300 hover:text-cyan-200">Abrir legado →</a>
            </div>
        </aside>

        <div class="min-w-0">
            <header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/90 px-4 py-3 backdrop-blur sm:px-6 lg:px-8">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <details class="relative lg:hidden">
                            <summary class="grid h-10 w-10 cursor-pointer list-none place-items-center rounded-xl border border-slate-200 bg-white text-xl text-slate-700">☰</summary>
                            <nav class="absolute top-12 left-0 z-30 w-64 rounded-2xl border border-slate-200 bg-white p-3 shadow-2xl" aria-label="Navegación móvil">
                                <a href="{{ route('new-system.dashboard') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium hover:bg-slate-100">Inicio</a>
                                <a href="{{ route('new-system.companies.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium hover:bg-slate-100">Empresas</a>
                                @can('manage-new-system-users')
                                    <a href="{{ route('new-system.users.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium hover:bg-slate-100">Usuarios</a>
                                @endcan
                                <a href="{{ url('/dashboard') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-cyan-700 hover:bg-cyan-50">Sistema legado</a>
                            </nav>
                        </details>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-semibold tracking-wider text-cyan-700 uppercase">{{ $currentCompany?->nombrecomercial ?? 'Sin empresa seleccionada' }}</p>
                            <h1 class="truncate text-lg font-semibold tracking-tight text-slate-900 sm:text-xl">{{ $title }}</h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if ($companies->count() > 1)
                            <a href="{{ route('new-system.companies.index') }}" class="hidden rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition hover:border-cyan-300 hover:text-cyan-700 sm:inline-flex">Cambiar empresa</a>
                        @endif
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-semibold text-slate-800">{{ trim($user->nombre1.' '.$user->apellido1) }}</p>
                            <p class="text-xs text-slate-500">{{ $user->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('new-system.logout') }}">
                            @csrf
                            <button type="submit" class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">Salir</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
