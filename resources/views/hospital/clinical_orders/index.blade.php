@extends('layouts.app-template')

@section('content')
@php
    $statusLabels = array('DRAFT' => 'Borrador', 'ORDERED' => 'Ordenada', 'ACKNOWLEDGED' => 'Recibida', 'IN_PROGRESS' => 'En proceso', 'COMPLETED' => 'Completada', 'CANCELLED' => 'Cancelada');
    $categoryLabels = array('LABORATORY' => 'Laboratorio', 'IMAGING' => 'Imágenes', 'PROCEDURE' => 'Procedimiento');
@endphp
<style>
    .clinical-cycle { padding: 1.5rem; }
    .clinical-hero { background: linear-gradient(120deg,#075985,#0891b2); color:#fff; border-radius:16px; padding:1.4rem 1.6rem; margin-bottom:1.25rem; }
    .clinical-card { border:1px solid #d7e5ec; border-radius:14px; box-shadow:0 7px 24px rgba(15,55,75,.06); overflow:hidden; background:#fff; margin-bottom:1.25rem; }
    .clinical-card__head { padding:1rem 1.2rem; background:#eef8fb; border-bottom:1px solid #d7e5ec; }
    .clinical-card__body { padding:1.1rem; }
    .clinical-badge { display:inline-block; border-radius:999px; padding:.3rem .65rem; font-size:.72rem; font-weight:700; background:#e8f1f5; color:#164e63; }
    .clinical-badge--danger { background:#fee2e2; color:#991b1b; }
    .clinical-badge--warn { background:#fef3c7; color:#92400e; }
    .clinical-badge--ok { background:#dcfce7; color:#166534; }
    .critical-alert { border-left:4px solid #dc2626; background:#fff8f8; border-radius:10px; padding:1rem; margin-bottom:.9rem; }
    .critical-alert.escalated { border-left-color:#7c3aed; background:#faf7ff; }
    .clinical-table th { background:#f4f8fa; color:#164e63; font-size:.75rem; text-transform:uppercase; }
    .clinical-empty { color:#64748b; text-align:center; padding:2rem; }
    .clinical-filter .form-control { min-height:38px; }
    @media (max-width: 767px) { .clinical-cycle { padding:.75rem; } .clinical-table { min-width:920px; } }
</style>

<div class="clinical-cycle">
    <div class="clinical-hero d-flex align-items-center justify-content-between flex-wrap">
        <div>
            <div class="text-uppercase font-weight-bold small mb-50" style="letter-spacing:.08em">Seguridad clínica transversal</div>
            <h2 class="text-white mb-25"><i class="fas fa-notes-medical mr-50"></i> Órdenes y resultados</h2>
            <div>Seguimiento único para hospitalización, emergencia y atención ambulatoria.</div>
            @if($encounterId)<div class="mt-25"><strong>Episodio:</strong> {{ $encounterId }}</div>@endif
        </div>
        <span class="clinical-badge mt-50"><i class="fas fa-link mr-25"></i> Vinculado al episodio clínico</span>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @if($errors->any())
        <div class="alert alert-danger"><strong>Revise la información:</strong> {{ $errors->first() }}</div>
    @endif

    <div class="clinical-card">
        <div class="clinical-card__head d-flex justify-content-between align-items-center flex-wrap">
            <div><h4 class="mb-25">Resultados críticos pendientes</h4><small>La alerta permanece visible hasta registrar notificación y acuse.</small></div>
            <span class="clinical-badge clinical-badge--danger">{{ $criticalAlerts->count() }} activos</span>
        </div>
        <div class="clinical-card__body">
            @forelse($criticalAlerts as $alert)
                @php $isEscalated = $alert->status === 'ESCALATED'; $order = $alert->result ? $alert->result->order : null; @endphp
                <div class="critical-alert {{ $isEscalated ? 'escalated' : '' }}">
                    <div class="row align-items-center">
                        <div class="col-lg-4 mb-50">
                            <strong>{{ $isEscalated ? 'ESCALADO' : $alert->status }}</strong>
                            <div>{{ $order ? $order->description : 'Orden no disponible' }}</div>
                            <small>Paciente: {{ $order ? $order->patient_id : 'N/D' }} · Límite: {{ $alert->due_at ? $alert->due_at->format('d/m/Y H:i') : 'N/D' }}</small>
                            @if($alert->result)<div class="mt-25 text-danger">{{ $alert->result->critical_reason }}</div>@endif
                        </div>
                        <div class="col-lg-8">
                            @if($alert->status === 'PENDING' || $alert->status === 'ESCALATED')
                                <form method="POST" action="{{ route('clinical_results.critical.notify', $alert->id) }}" class="row">
                                    {{ csrf_field() }}
                                    <div class="col-md-3 mb-25"><input name="recipient_name" class="form-control" placeholder="Nombre del receptor" required></div>
                                    <div class="col-md-3 mb-25"><input name="recipient_role" class="form-control" placeholder="Cargo / servicio" required></div>
                                    <div class="col-md-4 mb-25"><input name="notes" class="form-control" placeholder="Medio y observación"></div>
                                    <div class="col-md-2 mb-25"><button class="btn btn-danger btn-block"><i class="fas fa-bell mr-25"></i> Notificar</button></div>
                                </form>
                            @elseif($alert->status === 'NOTIFIED')
                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <div>Notificado a <strong>{{ $alert->recipient_name }}</strong> ({{ $alert->recipient_role }}) a las {{ $alert->notified_at ? $alert->notified_at->format('H:i') : '' }}.</div>
                                    <form method="POST" action="{{ route('clinical_results.critical.acknowledge', $alert->id) }}">
                                        {{ csrf_field() }}
                                        <button class="btn btn-success"><i class="fas fa-check-double mr-25"></i> Registrar acuse</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="clinical-empty"><i class="fas fa-shield-alt fa-2x mb-50"></i><br>No existen resultados críticos pendientes de acuse.</div>
            @endforelse
        </div>
    </div>

    <div class="clinical-card">
        <div class="clinical-card__head">
            <h4 class="mb-50">Ciclo de órdenes</h4>
            <form method="GET" class="row clinical-filter">
                @if($encounterId)<input type="hidden" name="encounter_id" value="{{ $encounterId }}">@endif
                <div class="col-md-3 mb-25"><input name="patient_id" value="{{ request('patient_id') }}" class="form-control" placeholder="Identificación del paciente"></div>
                <div class="col-md-3 mb-25"><select name="category" class="form-control"><option value="">Todas las áreas</option>@foreach($categoryLabels as $value => $label)<option value="{{ $value }}" {{ request('category') === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></div>
                <div class="col-md-3 mb-25"><select name="status" class="form-control"><option value="">Todos los estados</option>@foreach($statusLabels as $value => $label)<option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></div>
                <div class="col-md-3 mb-25"><button class="btn btn-primary"><i class="fas fa-search mr-25"></i> Filtrar</button> <a href="{{ route('clinical_orders.index', $encounterId ? ['encounter_id' => $encounterId] : array()) }}" class="btn btn-outline-secondary">Limpiar</a></div>
            </form>
        </div>
        <div class="table-responsive clinical-card__body pt-0">
            <table class="table clinical-table mb-0">
                <thead><tr><th>Paciente / episodio</th><th>Área</th><th>Orden</th><th>Fecha</th><th>Estado</th><th>Resultado</th><th style="min-width:260px">Acciones</th></tr></thead>
                <tbody>
                @forelse($orders as $order)
                    @php $latestResult = $order->results->first(); @endphp
                    <tr>
                        <td><strong>{{ $order->patient_id }}</strong><br><small>{{ $order->encounter_id }}</small></td>
                        <td>{{ isset($categoryLabels[$order->category]) ? $categoryLabels[$order->category] : $order->category }}</td>
                        <td>{{ $order->description }}<br><small>{{ $order->source_type }} #{{ $order->source_id }}</small></td>
                        <td>{{ $order->ordered_at ? $order->ordered_at->format('d/m/Y H:i') : 'N/D' }}</td>
                        <td><span class="clinical-badge {{ $order->status === 'COMPLETED' ? 'clinical-badge--ok' : ($order->status === 'CANCELLED' ? 'clinical-badge--danger' : 'clinical-badge--warn') }}">{{ isset($statusLabels[$order->status]) ? $statusLabels[$order->status] : $order->status }}</span></td>
                        <td>@if($latestResult)<strong>{{ $latestResult->status }}</strong><br><small>{{ str_limit($latestResult->summary, 90) }}</small>@else<span class="text-muted">Pendiente</span>@endif</td>
                        <td>
                            @if($order->status !== 'CANCELLED')
                                <button type="button" class="btn btn-sm btn-outline-primary mb-25 js-result" data-toggle="modal" data-target="#resultModal" data-order="{{ $order->id }}" data-description="{{ $order->description }}"><i class="fas fa-file-medical-alt"></i> Resultado</button>
                            @endif
                            @if(!in_array($order->status, array('COMPLETED','CANCELLED')))
                                <form method="POST" action="{{ route('clinical_orders.transition', $order->id) }}" class="d-inline-flex align-items-center">
                                    {{ csrf_field() }}
                                    <select name="status" class="form-control form-control-sm mr-25" required>
                                        @if($order->status === 'DRAFT')<option value="ORDERED">Ordenar</option>@endif
                                        @if(in_array($order->status,array('ORDERED','ACKNOWLEDGED')))<option value="IN_PROGRESS">En proceso</option>@endif
                                        <option value="CANCELLED">Cancelar</option>
                                    </select>
                                    <input name="reason" class="form-control form-control-sm mr-25" placeholder="Motivo si cancela">
                                    <button class="btn btn-sm btn-outline-secondary">Aplicar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="clinical-empty">No hay órdenes para los filtros seleccionados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-1 pb-1">{{ $orders->appends(request()->except('page'))->links() }}</div>
    </div>
</div>

<div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
        <form method="POST" id="resultForm" data-action="{{ url('hospital/ordenes-clinicas') }}">
            {{ csrf_field() }}
            <div class="modal-header"><div><h4 class="modal-title">Registrar resultado</h4><small id="resultOrderDescription"></small></div><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 form-group"><label>Estado</label><select name="status" class="form-control"><option value="PRELIMINARY">Preliminar</option><option value="FINAL">Final verificado</option><option value="AMENDED">Adenda</option></select></div>
                    <div class="col-md-8 form-group"><label>Resultado / conclusión</label><textarea name="summary" class="form-control" rows="3" required></textarea></div>
                    <div class="col-md-4 form-group"><div class="custom-control custom-checkbox mt-2"><input type="checkbox" class="custom-control-input" id="criticalCheck" name="is_critical" value="1"><label class="custom-control-label" for="criticalCheck">Es un resultado crítico</label></div></div>
                    <div class="col-md-8 form-group"><label>Criterio clínico de criticidad</label><textarea name="critical_reason" class="form-control" rows="2" placeholder="Obligatorio si el resultado es crítico"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button><button class="btn btn-primary"><i class="fas fa-save mr-25"></i> Guardar resultado</button></div>
        </form>
    </div></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var buttons = document.querySelectorAll('.js-result');
    for (var i = 0; i < buttons.length; i++) buttons[i].addEventListener('click', function () {
        var form = document.getElementById('resultForm');
        form.action = form.getAttribute('data-action') + '/' + this.getAttribute('data-order') + '/resultado';
        document.getElementById('resultOrderDescription').textContent = this.getAttribute('data-description');
    });
});
</script>
@endsection
