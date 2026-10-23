<div class="table-responsive">
<table class="table table-bordered">
    <tbody>
    @forelse($tickets as $ticket)
        @php
            $snapshot = $ticket->snapshot ?: [];
            $labels = ['open' => 'Recibido', 'assigned' => 'Asignado', 'resolved' => 'Resuelto'];
        @endphp
        <tr data-gateway-status="{{ $snapshot['status'] ?? '' }}" data-gateway-refresh="{{ route('gateway_tickets.refresh', ['id' => $ticket->id]) }}">
            <td>{{ $ticket->external_id }}</td>
            <td>Gateway</td>
            <td>{{ $ticket->payload['title'] }}<br><small class="gateway-error text-danger"></small></td>
            <td><span class="gateway-status gateway-state">{{ isset($snapshot['status']) ? ($labels[$snapshot['status']] ?? 'Desconocido') : 'Pendiente de confirmar' }}</span></td>
            <td>{{ substr($ticket->created_at, 0, 10) }}</td>
            <td>{{ substr($ticket->created_at, 11, 8) }}</td>
            <td>{{ $ticket->user_id }}</td>
            <td>
                <span class="gateway-assignee">{{ !empty($snapshot['assigned_to']) ? 'Operador #' . $snapshot['assigned_to'] : 'Sin asignar' }}</span>
                <div class="gateway-resolution" style="white-space: pre-wrap">{{ $snapshot['resolution'] ?? '' }}</div>
                <small>Consultado: <span class="gateway-synced">{{ $ticket->synced_at ?: 'Sin consultar' }}</span></small>
            </td>
        </tr>
    @empty
        <tr><td colspan="8">No hay tickets de Gateway vinculados. Los enviados antes de esta integración deben vincularse una vez usando su ID.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="gateway-pagination">{{ $tickets->links() }}</div>
