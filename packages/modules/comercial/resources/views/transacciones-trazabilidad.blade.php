@php
    $asientos = collect([
        ['Asiento contable', $registro->numero_asiento, $registro->id_asiento],
        ['Asiento de reversión', $registro->numero_asiento_reversion, $registro->id_asiento_reversion],
    ])->filter(fn ($asiento) => filled($asiento[1]));
    $movimientos = $registro->lineas->pluck('id_operacion')->filter()->unique();
    $reversiones = $registro->lineas->pluck('id_operacion_reversa')->filter()->unique();
@endphp

@if ($asientos->isNotEmpty())
    <section class="trazabilidad-diagrama__rama trazabilidad-diagrama__contabilidad">
        <h3><i class="bi bi-journal-text" aria-hidden="true"></i> Contabilidad <span>{{ $asientos->count() }}</span></h3>
        <ul class="trazabilidad-diagrama__lista" role="list">
            @foreach ($asientos as [$tipoAsiento, $numero, $idAsiento])
                <li class="trazabilidad-diagrama__nodo trazabilidad-diagrama__nodo--asiento">
                    <div class="trazabilidad-diagrama__datos">
                        <span>{{ $tipoAsiento }}</span>
                        @if ($puedeVerAsiento && $idAsiento)
                            <a href="{{ route('contabilidad.asientos.show', $idAsiento) }}" target="_blank" rel="noopener">N.º {{ $numero }}</a>
                        @else
                            <strong>N.º {{ $numero }}</strong>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </section>
@endif

@if ($movimientos->isNotEmpty() || $reversiones->isNotEmpty())
    <section class="trazabilidad-diagrama__rama trazabilidad-diagrama__inventario">
        <h3><i class="bi bi-box-seam" aria-hidden="true"></i> Inventario <span>{{ $movimientos->count() + $reversiones->count() }}</span></h3>
        <ul class="trazabilidad-diagrama__lista" role="list">
            @foreach (['Movimiento de inventario' => $movimientos, 'Reversión de inventario' => $reversiones] as $tipoMovimiento => $operaciones)
                @foreach ($operaciones as $idOperacion)
                    <li class="trazabilidad-diagrama__nodo trazabilidad-diagrama__nodo--inventario">
                        <div class="trazabilidad-diagrama__datos">
                            <span>{{ $tipoMovimiento }}</span>
                            @foreach ($partidasInventario->get($idOperacion, []) as $partida)
                                <div class="trazabilidad-diagrama__partida">
                                    <strong>{{ $partida->producto }}</strong>
                                    <span>{{ $partida->codigo }} · {{ $partida->bodega }}</span>
                                    <span class="trazabilidad-diagrama__cantidad">
                                        {{ $partida->variacion > 0 ? 'Entrada' : 'Salida' }} {{ \Aitrol\Inventarios\Domain\CantidadInventario::decimal(abs((int) $partida->variacion), 3) }}
                                    </span>
                                </div>
                            @endforeach
                            @if ($puedeVerMovimiento)
                                <a href="{{ route('inventarios.movimientos.show', $idOperacion) }}" target="_blank" rel="noopener">Movimiento N.º {{ $idOperacion }}</a>
                            @else
                                <strong>Movimiento N.º {{ $idOperacion }}</strong>
                            @endif
                        </div>
                    </li>
                @endforeach
            @endforeach
        </ul>
    </section>
@endif
