@php
    $nroAsiento = data_get($registroAsiento, 'numero_asiento') ?? data_get($registroAsiento, 'numero_asiento_contable');
    $nroReversion = data_get($registroAsiento, 'numero_asiento_reversion');
@endphp
@if ($nroAsiento || $nroReversion)
    <small class="d-block fw-normal text-body-secondary">
        @if ($nroAsiento) Asiento N.º {{ $nroAsiento }} @endif
        @if ($nroAsiento && $nroReversion) <span aria-hidden="true">·</span> @endif
        @if ($nroReversion) Reversión N.º {{ $nroReversion }} @endif
    </small>
@endif
