<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    <div class="max-w-5xl">
        <div>
            <p class="text-sm font-semibold text-cyan-700">Contexto operativo</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">Elige la empresa con la que vas a trabajar</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">La selección se conserva en tu sesión y también será utilizada al abrir módulos heredados compatibles.</p>
        </div>

        @if ($newSystemCompanies->isEmpty())
            <div class="mt-6 rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <p class="text-lg font-semibold text-slate-800">No tienes empresas disponibles</p>
                <p class="mt-2 text-sm text-slate-500">Un superadministrador debe asignarte al menos una empresa.</p>
            </div>
        @else
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @foreach ($newSystemCompanies as $company)
                    @php($isCurrent = $newSystemCurrentCompany?->id === $company->id)
                    <article class="rounded-3xl border bg-white p-6 shadow-sm {{ $isCurrent ? 'border-cyan-400 ring-4 ring-cyan-100' : 'border-slate-200' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="grid h-12 w-12 place-items-center rounded-2xl {{ $isCurrent ? 'bg-cyan-400 text-slate-950' : 'bg-slate-100 text-slate-600' }} text-lg font-black">
                                {{ mb_substr($company->nombrecomercial, 0, 1) }}
                            </div>
                            @if ($isCurrent)
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Empresa activa</span>
                            @endif
                        </div>

                        <h3 class="mt-5 text-xl font-semibold tracking-tight text-slate-900">{{ $company->nombrecomercial }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $company->razonsocial }}</p>
                        <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-500">
                            <span class="rounded-lg bg-slate-100 px-2.5 py-1.5">RUC {{ $company->id }}</span>
                            @if ($company->ciudad)
                                <span class="rounded-lg bg-slate-100 px-2.5 py-1.5">{{ $company->ciudad }}</span>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('new-system.companies.store') }}" class="mt-6">
                            @csrf
                            <input type="hidden" name="company_id" value="{{ $company->id }}">
                            <button type="submit" class="w-full rounded-xl px-4 py-3 text-sm font-bold transition {{ $isCurrent ? 'cursor-default bg-slate-100 text-slate-500' : 'bg-slate-900 text-white hover:bg-cyan-600' }}" @disabled($isCurrent)>
                                {{ $isCurrent ? 'Seleccionada' : 'Trabajar con esta empresa' }}
                            </button>
                        </form>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-new-system.layout>
