@include('v3.clinical.crash_cart._styles')
<main class="cart-shell">
    <header class="cart-hero"><div><small>ENFERMERÍA · {{ $record->fecha }}</small><h1>Control de carro de paro #{{ $record->id }}</h1><p>{{ $record->bodega_nombre }} · {{ $record->servicio }}</p></div><div class="cart-toolbar"><a class="cart-btn secondary" href="{{ route('v3.crash-cart.index', ['mes' => substr($record->fecha, 0, 7)]) }}">Volver</a>@if($record->estado && $permissions['update'])<a class="cart-btn secondary" href="{{ route('v3.crash-cart.edit', ['control' => $record->id]) }}">Corregir control</a>@endif</div></header>
    @if(session('success'))<div class="cart-note" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="cart-error" role="alert">{{ implode(' ', $errors->all()) }}</div>@endif
    <section class="cart-card"><div class="cart-summary"><div><strong>Responsable</strong>{{ $record->responsable_nombre }}</div><div><strong>Tipo</strong>{{ $record->tipo == 1 ? 'Medicamentos' : 'Insumos' }}</div><div><strong>Referencia</strong>{{ $record->reference_basis === 'declared' ? 'Declarada para fecha anterior' : 'Inventario al registrar' }}</div><div><strong>Versión</strong>{{ $record->version }}</div><div><span class="cart-badge {{ $record->estado ? '' : 'danger' }}">{{ $record->estado ? 'Activo' : 'Anulado' }}</span></div></div>
        @if($record->observacion)<div class="cart-note">{{ $record->observacion }}</div>@endif
        @if(!$record->estado)<div class="cart-error">Motivo de anulación: {{ $record->motivo_anulacion }}</div>@endif
        <div class="cart-scroll" style="margin-top:18px"><table class="cart-table"><thead><tr><th>Producto / lote / serie</th><th>Presentación / concentración</th><th>Vencimiento</th><th>Referencia</th><th>Conteo</th><th>Diferencia</th><th>Observación</th></tr></thead><tbody>
            @foreach($details as $line)<tr><td><strong>{{ $line->producto_nombre }}</strong><div class="cart-muted">Lote: {{ $line->lote ?: '—' }} · Serie: {{ $line->serie ?: '—' }}</div></td><td>{{ $line->presentacion ?: '—' }}<div class="cart-muted">{{ $line->concentracion }}</div></td><td>{{ $line->fecha_caducidad ?: 'Sin fecha' }}<div class="cart-muted">{{ $carts->expiryStatus($line->fecha_caducidad) }} (hoy)</div></td><td>{{ $line->referencia_cantidad }}</td><td>{{ $line->stock }}</td><td>{{ $line->referencia_cantidad - $line->stock }}</td><td>{{ $line->observacion }}</td></tr>@endforeach
        </tbody></table></div><p class="cart-muted" style="margin-top:12px">La diferencia es referencia menos conteo; requiere revisión y no acredita por sí sola un consumo ni un egreso de inventario.</p>
    </section>
    <section class="cart-card"><h2 style="font-size:20px">Historial del control</h2>
        @foreach($events as $event)
            @php($snapshot = json_decode($event->snapshot, true))
            <details class="cart-history"><summary>Versión {{ $event->version }} · {{ ['create' => 'Registro', 'update' => 'Corrección', 'cancel' => 'Anulación'][$event->accion] ?? $event->accion }} · {{ $event->created_at }} · Usuario {{ $event->actor_id }}</summary>
                <p>Responsable: {{ $snapshot['record']['responsable_nombre'] ?? '' }} · {{ $snapshot['record']['observacion'] ?? '' }}</p>
                @if(!empty($snapshot['record']['motivo_anulacion']))<p>Motivo: {{ $snapshot['record']['motivo_anulacion'] }}</p>@endif
                <div class="cart-scroll"><table class="cart-table"><thead><tr><th>Producto / lote / serie</th><th>Presentación / concentración</th><th>Referencia</th><th>Conteo</th><th>Vencimiento</th><th>Observación</th></tr></thead><tbody>@foreach(($snapshot['details'] ?? []) as $line)<tr><td>{{ $line['producto_nombre'] }} / {{ $line['lote'] }} / {{ $line['serie'] }}</td><td>{{ $line['presentacion'] }} / {{ $line['concentracion'] }}</td><td>{{ $line['referencia_cantidad'] }}</td><td>{{ $line['stock'] }}</td><td>{{ $line['fecha_caducidad'] }}</td><td>{{ $line['observacion'] }}</td></tr>@endforeach</tbody></table></div>
            </details>
        @endforeach
    </section>
    @if($record->estado && $permissions['delete'])<section class="cart-card"><details><summary>Anular este control</summary><p>El registro conservará sus detalles e historial y dejará de aparecer en los reportes activos.</p><form method="post" action="{{ route('v3.crash-cart.cancel', ['control' => $record->id]) }}" class="cart-toolbar">{{ csrf_field() }}<input type="hidden" name="version" value="{{ $record->version }}"><div class="cart-field"><label for="cancel-reason">Motivo de anulación</label><input id="cancel-reason" name="motivo" required minlength="5" maxlength="1000"></div><button class="cart-btn danger">Confirmar anulación</button></form></details></section>@endif
</main>
