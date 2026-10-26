<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    @if ($newSystemCurrentCompany === null && $newSystemCompanies->count() > 1)
        <div class="mb-6 flex flex-col gap-4 rounded-3xl border border-amber-200 bg-amber-50 p-5 text-amber-900 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="font-semibold">Selecciona la empresa de trabajo</p>
                <p class="mt-1 text-sm text-amber-700">Los datos de los módulos se filtrarán por la empresa activa.</p>
            </div>
            <a href="{{ route('new-system.companies.index') }}" class="inline-flex justify-center rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-amber-600">Seleccionar empresa</a>
        </div>
    @endif

    <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900 to-cyan-950 p-6 text-white shadow-xl shadow-slate-300/30 sm:p-8">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold tracking-[0.16em] text-cyan-300 uppercase">AITROL Nuevo</p>
            <h2 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">Una base moderna conectada con toda la operación existente.</h2>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300 sm:text-base">Esta plataforma convivirá con el sistema legado mientras cada módulo se reemplaza de forma controlada, conservando usuarios, empresas y datos históricos.</p>
        </div>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Resumen del sistema">
        @foreach ([
            ['label' => 'Usuarios activos', 'value' => $stats['activeUsers'], 'tone' => 'text-cyan-700 bg-cyan-50'],
            ['label' => 'Empresas activas', 'value' => $stats['companies'], 'tone' => 'text-blue-700 bg-blue-50'],
            ['label' => 'Usuarios en empresa', 'value' => $stats['companyUsers'], 'tone' => 'text-violet-700 bg-violet-50'],
            ['label' => 'Rutas integradas', 'value' => $stats['legacyRoutes'], 'tone' => 'text-emerald-700 bg-emerald-50'],
        ] as $stat)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold {{ $stat['tone'] }}">{{ $stat['label'] }}</span>
                <p class="mt-4 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stat['value']) }}</p>
            </article>
        @endforeach
    </section>

    <section class="mt-8">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-cyan-700">Módulos iniciales</p>
                <h2 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">Continúa tu trabajo</h2>
            </div>
            <a href="{{ route('modules.index') }}" class="hidden text-sm font-semibold text-slate-500 transition hover:text-cyan-700 sm:inline">Ver mapa completo →</a>
        </div>

        <div class="mt-5 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            <a href="{{ route('new-system.companies.index') }}" class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-300 hover:shadow-lg">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-cyan-50 text-xl text-cyan-700">◇</div>
                <h3 class="mt-5 text-lg font-semibold text-slate-900 group-hover:text-cyan-700">Empresas</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">Selecciona el contexto de trabajo compartido con el sistema legado.</p>
            </a>

            @can('manage-new-system-users')
                <a href="{{ route('new-system.users.index') }}" class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-300 hover:shadow-lg">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-50 text-xl text-blue-700">◎</div>
                    <h3 class="mt-5 text-lg font-semibold text-slate-900 group-hover:text-cyan-700">Usuarios</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Administra accesos, roles, estado y empresas asignadas.</p>
                </a>
            @endcan

            <a href="{{ url('/dashboard') }}" class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-300 hover:shadow-lg">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-violet-50 text-xl text-violet-700">↗</div>
                <h3 class="mt-5 text-lg font-semibold text-slate-900 group-hover:text-cyan-700">Sistema legado</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">Accede a los módulos operativos que aún están en proceso de modernización.</p>
            </a>
        </div>
    </section>
</x-new-system.layout>
