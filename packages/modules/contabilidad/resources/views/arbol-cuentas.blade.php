@php
    $cuentasPorPadre = $registros->groupBy(fn (array $cuenta): int => (int) ($cuenta['id_padre'] ?? 0));
    $puedeCrearAuxiliar = Gate::allows('permiso', [$ruta.'.index', 'escribir']);
    $puedeEditarAuxiliar = Gate::allows('permiso', [$ruta.'.index', 'actualizar']);
    $iconosClase = ['activo' => 'bi-graph-up-arrow', 'pasivo' => 'bi-credit-card', 'patrimonio' => 'bi-bank', 'ingreso' => 'bi-cash-stack', 'gasto' => 'bi-receipt'];
    $totalVisible = $coincidencias?->count() ?? $registros->count();
@endphp

<section class="plan-cuentas" id="plan-cuentas" data-plan-cuentas aria-label="Jerarquía de cuentas">
    <div class="plan-cuentas-barra">
        <span class="text-body-secondary small" role="status" data-plan-total>{{ $totalVisible }} {{ $buscando ? ($totalVisible === 1 ? 'coincidencia' : 'coincidencias') : ($totalVisible === 1 ? 'cuenta' : 'cuentas') }}</span>
        @if ($registros->contains('tiene_hijas', true))
            <div class="plan-cuentas-controles flex-wrap gap-2" data-plan-controles>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-plan-mostrar="true">
                    <i class="bi bi-chevron-double-down me-1" aria-hidden="true"></i>Expandir todo
                </button>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-plan-mostrar="false">
                    <i class="bi bi-chevron-double-up me-1" aria-hidden="true"></i>Plegar todo
                </button>
            </div>
        @endif
    </div>
    <div data-plan-sin-coincidencias @if (!$buscando || $totalVisible > 0) hidden @endif>
        @include('contabilidad::vacio', ['texto' => 'No hay cuentas con ese código o nombre'])
    </div>
    @if ($registros->isEmpty())
        <div data-plan-vacio @if ($buscando) hidden @endif>
            @include('contabilidad::vacio', ['texto' => 'Todavía no hay cuentas'])
        </div>
    @else
        <ul class="plan-cuentas-raices" role="list" @if ($totalVisible === 0) hidden @endif>
            @foreach ($cuentasPorPadre->get(0, []) as $cuenta)
                @include('contabilidad::rama-cuenta', ['cuenta' => $cuenta, 'esRaiz' => true])
            @endforeach
        </ul>
    @endif
</section>
