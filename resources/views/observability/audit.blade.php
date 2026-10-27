@extends('observability.layout')

@section('title', 'Auditoría del sistema')

@section('content')
    <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 to-blue-950 p-6 text-white shadow-xl sm:p-8">
        <p class="text-xs font-bold tracking-[0.2em] text-cyan-300 uppercase">Trazabilidad centralizada</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">Auditoría del sistema</h1>
        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">Revise acciones funcionales, cambios de valores y eventos técnicos registrados por los módulos existentes. Esta pantalla no modifica datos.</p>
    </section>

    <section class="grid gap-4 sm:grid-cols-3">
        @foreach ([['label' => 'Acciones de hoy', 'value' => $result->summary['activities_today'], 'color' => 'text-cyan-700'], ['label' => 'Eventos técnicos de hoy', 'value' => $result->summary['technical_today'], 'color' => 'text-blue-700'], ['label' => 'Usuarios activos hoy', 'value' => $result->summary['users_today'], 'color' => 'text-emerald-700']] as $summary)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-semibold text-slate-500">{{ $summary['label'] }}</p><p class="mt-2 text-3xl font-black {{ $summary['color'] }}">{{ number_format($summary['value']) }}</p></article>
        @endforeach
    </section>

    @foreach ($result->warnings as $warning)<div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">{{ $warning }}</div>@endforeach

    <section class="fallback-card rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-lg font-bold">Buscar en toda la auditoría</h2>
        <p class="mt-1 text-sm text-slate-500">Los criterios se envían por POST y permanecen en la sesión.</p>
        <form method="POST" action="{{ route('observability.audit.filter') }}" class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-6 xl:items-end">
            @csrf
            <label class="grid gap-1.5 text-sm font-semibold xl:col-span-2">Texto
                <input name="search" value="{{ $filters->search }}" maxlength="200" placeholder="Acción, módulo, modelo, vista o usuario" class="rounded-xl border border-slate-300 px-3 py-2.5 font-normal">
            </label>
            <label class="grid gap-1.5 text-sm font-semibold">Usuario ID<input name="user_id" value="{{ $filters->userId }}" maxlength="30" class="rounded-xl border border-slate-300 px-3 py-2.5 font-normal"></label>
            <label class="grid gap-1.5 text-sm font-semibold">Empresa ID<input name="company_id" value="{{ $filters->companyId }}" maxlength="30" class="rounded-xl border border-slate-300 px-3 py-2.5 font-normal"></label>
            <label class="grid gap-1.5 text-sm font-semibold">Desde<input type="date" name="from_date" value="{{ $filters->fromDate }}" class="rounded-xl border border-slate-300 px-3 py-2.5 font-normal"></label>
            <label class="grid gap-1.5 text-sm font-semibold">Hasta<input type="date" name="to_date" value="{{ $filters->toDate }}" class="rounded-xl border border-slate-300 px-3 py-2.5 font-normal"></label>
            <label class="grid gap-1.5 text-sm font-semibold">Filas<select name="per_page" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 font-normal">@foreach ([10, 25, 50, 100] as $size)<option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>@endforeach</select></label>
            <button class="rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-cyan-700">Aplicar filtros</button>
        </form>
        <form method="POST" action="{{ route('observability.audit.reset') }}" class="mt-3">@csrf<button class="text-sm font-semibold text-slate-500 hover:text-slate-900">Limpiar filtros</button></form>
    </section>

    <section class="fallback-card overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-5 sm:px-6"><h2 class="text-lg font-bold">Actividad funcional</h2><p class="text-sm text-slate-500">{{ number_format($result->activities->total()) }} registros de log_usuario</p></header>
        <div class="overflow-x-auto">
            <table class="min-w-[1050px] w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] tracking-wide text-slate-500 uppercase"><tr><th class="px-5 py-3">Fecha</th><th class="px-5 py-3">Usuario</th><th class="px-5 py-3">IP</th><th class="px-5 py-3">Acción</th><th class="px-5 py-3">Cambios</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($result->activities as $activity)
                        <tr class="align-top hover:bg-slate-50/70"><td class="whitespace-nowrap px-5 py-4 font-mono text-xs">{{ $activity->created_at }}</td><td class="px-5 py-4"><strong>{{ trim(($activity->nombre1 ?? '').' '.($activity->apellido1 ?? '')) ?: 'Usuario #'.$activity->id_usuario }}</strong><small class="block text-slate-500">{{ $activity->email ?? '' }}</small></td><td class="px-5 py-4 font-mono text-xs">{{ $activity->ip_usuario }}</td><td class="max-w-xl px-5 py-4 leading-6">{{ $activity->descripcion }}</td><td class="px-5 py-4"><details><summary class="cursor-pointer font-semibold text-cyan-700">Ver antes/después</summary><dl class="mt-2 grid min-w-80 gap-2 text-xs">@foreach ([1, 2, 3, 4] as $position)<div class="grid grid-cols-2 gap-2 rounded-lg bg-slate-50 p-2"><div><dt class="font-bold text-slate-400">ANTES {{ $position }}</dt><dd class="break-words">{{ $activity->{'dato_ant'.$position} ?: '—' }}</dd></div><div><dt class="font-bold text-slate-400">DESPUÉS {{ $position }}</dt><dd class="break-words">{{ $activity->{'dato'.$position} ?: '—' }}</dd></div></div>@endforeach</dl></details></td></tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">No hay actividad funcional con estos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($result->activities->hasPages())<div class="border-t border-slate-200 p-5">{{ $result->activities->links() }}</div>@endif
    </section>

    <section class="fallback-card overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-5 sm:px-6"><h2 class="text-lg font-bold">Auditoría técnica</h2><p class="text-sm text-slate-500">{{ number_format($result->technicalEvents->total()) }} registros de new_db_auditorias</p></header>
        <div class="overflow-x-auto">
            <table class="min-w-[1250px] w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] tracking-wide text-slate-500 uppercase"><tr><th class="px-5 py-3">Fecha</th><th class="px-5 py-3">Usuario / Empresa</th><th class="px-5 py-3">Módulo</th><th class="px-5 py-3">Controlador / Modelo</th><th class="px-5 py-3">Tiempo</th><th class="px-5 py-3">Origen</th><th class="px-5 py-3">Detalle</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($result->technicalEvents as $event)
                        <tr class="align-top hover:bg-slate-50/70"><td class="whitespace-nowrap px-5 py-4 font-mono text-xs">{{ $event->created_at_auditoria }}</td><td class="px-5 py-4"><strong>{{ trim(($event->nombre1 ?? '').' '.($event->apellido1 ?? '')) ?: 'Usuario #'.$event->id_usuario_auditoria }}</strong><small class="block text-slate-500">Empresa #{{ $event->id_empresa_auditoria ?? '—' }}</small></td><td class="px-5 py-4"><strong>{{ $event->submodulo_auditoria ?: '—' }}</strong><small class="block text-slate-500">{{ $event->vista_auditoria ?: '' }}</small></td><td class="px-5 py-4"><span class="block break-all">{{ $event->controlador_auditoria ?: '—' }}</span><small class="block break-all text-slate-500">{{ $event->modelo_auditoria ?: '' }}</small></td><td class="whitespace-nowrap px-5 py-4">{{ $event->tiempo_consulta_auditoria ?? '—' }}</td><td class="px-5 py-4"><span class="font-mono text-xs">{{ $event->ip_auditoria ?? '—' }}</span><small class="block text-slate-500">{{ $event->navegador_auditoria ?? '' }} {{ $event->sistema_auditoria ?? '' }}</small></td><td class="px-5 py-4"><details><summary class="cursor-pointer font-semibold text-cyan-700">Ver evidencia</summary><div class="mt-2 grid min-w-[32rem] gap-3 text-xs">@foreach (['Consulta' => 'query_auditoria', 'Parámetros' => 'parametros_auditoria', 'Observación' => 'observacion_auditoria', 'Objeto' => 'obj_auditoria', 'Agente' => 'agente_auditoria'] as $label => $field)<div><p class="font-bold text-slate-500">{{ $label }}</p><pre class="mt-1 max-h-56 overflow-auto whitespace-pre-wrap rounded-lg bg-slate-950 p-3 text-slate-200">{{ $event->{$field} ?: '—' }}</pre></div>@endforeach</div></details></td></tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-slate-500">No hay auditoría técnica con estos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($result->technicalEvents->hasPages())<div class="border-t border-slate-200 p-5">{{ $result->technicalEvents->links() }}</div>@endif
    </section>
@endsection
