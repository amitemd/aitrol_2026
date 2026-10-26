<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ingresar · AITROL Nuevo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <main class="relative grid min-h-screen overflow-hidden lg:grid-cols-[1.1fr_0.9fr]">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full bg-cyan-400/15 blur-3xl"></div>
            <div class="absolute right-0 bottom-0 h-[32rem] w-[32rem] rounded-full bg-blue-500/15 blur-3xl"></div>
        </div>

        <section class="relative hidden flex-col justify-between border-r border-white/10 p-12 lg:flex xl:p-16">
            <div class="flex items-center gap-3">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-cyan-400 text-xl font-black text-slate-950">A</span>
                <div>
                    <p class="text-xl font-semibold text-white">AITROL</p>
                    <p class="text-xs font-semibold tracking-[0.24em] text-cyan-300 uppercase">Nueva plataforma</p>
                </div>
            </div>

            <div class="max-w-xl">
                <p class="text-sm font-semibold tracking-[0.18em] text-cyan-300 uppercase">Evolución progresiva</p>
                <h1 class="mt-5 text-5xl font-semibold leading-tight tracking-tight text-white xl:text-6xl">La experiencia nueva, con el conocimiento del sistema legado.</h1>
                <p class="mt-6 max-w-lg text-lg leading-8 text-slate-300">La misma información, empresas y permisos, sobre una interfaz diseñada para crecer módulo por módulo.</p>
            </div>

            <p class="text-sm text-slate-500">AITROL · Plataforma administrativa</p>
        </section>

        <section class="relative flex items-center justify-center px-5 py-12 sm:px-10">
            <div class="w-full max-w-md">
                <div class="mb-10 flex items-center gap-3 lg:hidden">
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-cyan-400 font-black text-slate-950">A</span>
                    <div>
                        <p class="text-lg font-semibold text-white">AITROL</p>
                        <p class="text-[0.65rem] font-semibold tracking-[0.2em] text-cyan-300 uppercase">Nueva plataforma</p>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/[0.07] p-6 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-8">
                    <p class="text-sm font-semibold text-cyan-300">Bienvenido</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight text-white">Ingresa a tu cuenta</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-400">Utiliza las mismas credenciales del sistema legado.</p>

                    @if ($errors->any())
                        <div class="mt-6 rounded-2xl border border-red-400/20 bg-red-400/10 px-4 py-3 text-sm text-red-200">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('new-system.login.store') }}" class="mt-7 flex flex-col gap-5">
                        @csrf
                        <label class="flex flex-col gap-2">
                            <span class="text-sm font-medium text-slate-300">Correo electrónico</span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3.5 text-white outline-none transition placeholder:text-slate-600 focus:border-cyan-400/60 focus:ring-4 focus:ring-cyan-400/10" placeholder="usuario@empresa.com">
                        </label>

                        <label class="flex flex-col gap-2">
                            <span class="text-sm font-medium text-slate-300">Contraseña</span>
                            <input type="password" name="password" required autocomplete="current-password" class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3.5 text-white outline-none transition focus:border-cyan-400/60 focus:ring-4 focus:ring-cyan-400/10">
                        </label>

                        <label class="flex items-center gap-3 text-sm text-slate-400">
                            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-white/20 bg-slate-900 text-cyan-400 focus:ring-cyan-400/30">
                            Mantener la sesión iniciada
                        </label>

                        <button type="submit" class="rounded-2xl bg-cyan-400 px-4 py-3.5 text-sm font-bold text-slate-950 transition hover:bg-cyan-300 focus:ring-4 focus:ring-cyan-400/20">Ingresar a AITROL Nuevo</button>
                    </form>

                    <div class="mt-6 border-t border-white/10 pt-5 text-center">
                        <a href="{{ url('/login') }}" class="text-sm font-medium text-slate-400 transition hover:text-white">Ir al acceso del sistema legado</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
