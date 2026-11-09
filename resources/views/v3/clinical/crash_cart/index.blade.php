@include('v3.clinical.crash_cart._styles')
<main class="cart-shell">
    <header class="cart-hero"><div><small>ENFERMERÍA · V3</small><h1>Carro de paro</h1><p>Controles diarios de medicamentos e insumos, lotes y vencimientos.</p></div>
        @if($ready && $permissions['create'])<a class="cart-btn secondary" href="{{ route('v3.crash-cart.create') }}"><i class="fas fa-plus" aria-hidden="true"></i> Registrar control</a>@endif
    </header>
    @if(!$ready)
        <div class="cart-note">El módulo está pendiente de instalación. Contacte a Sistemas para habilitar el registro de controles.</div>
    @else
        <section class="cart-card">
            <form method="get" action="{{ route('v3.crash-cart.index') }}" class="cart-toolbar">
                <div class="cart-field"><label for="cart-month">Mes</label><input id="cart-month" type="month" name="mes" value="{{ $filters['mes'] }}" required></div>
                <div class="cart-field"><label for="cart-warehouse">Bodega</label><select id="cart-warehouse" name="bodega"><option value="">Todas</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}" {{ $filters['bodega'] == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->nombre }}</option>@endforeach</select></div>
                <div class="cart-field"><label for="cart-type">Tipo</label><select id="cart-type" name="tipo"><option value="">Todos</option><option value="1" {{ $filters['tipo'] == 1 ? 'selected' : '' }}>Medicamentos</option><option value="2" {{ $filters['tipo'] == 2 ? 'selected' : '' }}>Insumos</option></select></div>
                <div class="cart-field"><label for="cart-service">Servicio</label><input id="cart-service" name="servicio" value="{{ $filters['servicio'] }}" maxlength="100" placeholder="Todos"></div>
                <div class="cart-field"><label for="cart-person">Responsable</label><select id="cart-person" name="responsable"><option value="">Todos</option>@foreach($users as $person)<option value="{{ $person->id }}" {{ $filters['responsable'] == $person->id ? 'selected' : '' }}>{{ $person->apellido1 }} {{ $person->apellido2 }} {{ $person->nombre1 }}</option>@endforeach</select></div>
                <button class="cart-btn">Consultar</button>
            </form>
        </section>
        <section class="cart-card">
            <div class="cart-foot" style="margin:0 0 16px"><strong>{{ $records->total() }} controles en {{ $filters['mes'] }}</strong><div class="cart-toolbar">
                <a class="cart-btn secondary" href="{{ route('v3.crash-cart.report', $filters) }}">Cuadro mensual y diferencias</a>
                @if($permissions['export'])<a class="cart-btn secondary" href="{{ route('v3.crash-cart.pdf', $filters) }}" target="_blank" rel="noopener">PDF</a><a class="cart-btn secondary" href="{{ route('v3.crash-cart.excel', $filters) }}">Excel</a>@endif
            </div></div>
            <div class="cart-scroll"><table class="cart-table"><thead><tr><th>Fecha</th><th>Bodega / servicio</th><th>Tipo</th><th>Responsable</th><th>Estado</th><th>Acción</th></tr></thead><tbody>
                @forelse($records as $record)<tr><td>{{ $record->fecha }}<div class="cart-muted">#{{ $record->id }}</div></td><td>{{ $record->bodega_nombre }}<div class="cart-muted">{{ $record->servicio }}</div></td><td>{{ $record->tipo == 1 ? 'Medicamentos' : 'Insumos' }}</td><td>{{ $record->responsable_nombre }}</td><td><span class="cart-badge {{ $record->estado ? '' : 'danger' }}">{{ $record->estado ? 'Activo' : 'Anulado' }}</span></td><td><a class="cart-btn secondary" href="{{ route('v3.crash-cart.detail', ['control' => $record->id]) }}">Ver control</a></td></tr>
                @empty<tr><td colspan="6" class="cart-empty">No hay controles para estos filtros. Registre el primer control de la bodega del carro de paro.</td></tr>@endforelse
            </tbody></table></div>
            {{ $records->links() }}
        </section>
    @endif
</main>
