@extends('layouts.app-template-h')
@section('title', 'Banco de sangre')
@section('content')
@php
    $types = array('O-','O+','A-','A+','B-','B+','AB-','AB+');
    $components = array('RED_CELLS'=>'Concentrado de hematíes','PLASMA'=>'Plasma fresco','PLATELETS'=>'Plaquetas','CRYOPRECIPITATE'=>'Crioprecipitado');
    $statusLabels = array('PENDING'=>'Pendiente','COMPATIBILITY'=>'En compatibilidad','READY'=>'Lista para entregar','DELIVERED'=>'Entregada','CANCELLED'=>'Cancelada');
    $unitLabels = array('AVAILABLE'=>'Disponible','RESERVED'=>'Reservada','USED'=>'Utilizada','QUARANTINED'=>'Cuarentena','DISCARDED'=>'Descartada','EXPIRED'=>'Vencida');
    $allocationLabels = array('RESERVED'=>'Asignada','DELIVERED'=>'Entregada','RELEASED'=>'Liberada');
    $priorityLabels = array('ROUTINE'=>'Programada','URGENT'=>'Urgente','EMERGENCY'=>'Emergencia');
@endphp
<style>
.bb-page{padding:22px;background:#f4f8fa;min-height:calc(100vh - 100px);color:#17364a}.bb-wrap{max-width:1500px;margin:auto}.bb-top{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:24px 28px;border-radius:18px;background:linear-gradient(120deg,#8e1739,#c9274f);color:#fff;box-shadow:0 12px 28px rgba(108,19,48,.18)}.bb-top h1{margin:5px 0 4px;font-size:28px;font-weight:700;color:#fff!important}.bb-top h1 i{color:#fff!important}.bb-top p{margin:0;color:#ffe7ee}.bb-actions .btn{border-radius:9px;font-weight:700;margin-left:7px}.bb-actions .btn-light{background:#fff;color:#8e1739}.bb-actions .bb-new-request{background:#0f766e;border-color:#0f766e;color:#fff;box-shadow:0 4px 12px rgba(9,73,68,.28)}.bb-actions .bb-new-request:hover,.bb-actions .bb-new-request:focus,.bb-actions .bb-new-request:active{background:#115e59;border-color:#115e59;color:#fff}.bb-breadcrumb{display:inline-block;color:#8e1739;margin-bottom:12px;font-weight:600}.bb-breadcrumb:hover{color:#611027}.bb-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin:18px 0}.bb-stat,.bb-card{background:#fff;border:1px solid #dce7ec;border-radius:14px;box-shadow:0 5px 18px rgba(24,61,78,.05)}.bb-stat{padding:17px 19px;display:flex;align-items:center;gap:13px}.bb-stat i{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:#fde8ee;color:#a4163a;font-size:19px}.bb-stat strong{display:block;font-size:25px;line-height:1}.bb-stat span{font-size:12px;color:#6a7f8c;text-transform:uppercase;font-weight:700}.bb-grid{display:grid;grid-template-columns:minmax(0,1.65fr) minmax(340px,.75fr);gap:18px}.bb-card{overflow:hidden;margin-bottom:18px}.bb-card-head{padding:17px 20px;border-bottom:1px solid #e1e9ed;display:flex;align-items:center;justify-content:space-between}.bb-card-head h2{font-size:18px;margin:0;font-weight:700}.bb-card-body{padding:18px}.bb-filter{background:#f8fafb;padding:13px 17px;border-bottom:1px solid #e5ecef}.bb-filter .form-control,.bb-card .form-control{border-radius:8px;border-color:#cfdde3;box-shadow:none}.bb-table{margin:0}.bb-table th{background:#f7fafb;color:#587080;font-size:11px;text-transform:uppercase;letter-spacing:.45px;border-bottom:1px solid #dfe8ec!important}.bb-table td{vertical-align:middle!important;border-color:#edf2f4!important}.bb-badge{display:inline-block;border-radius:999px;padding:4px 9px;background:#edf3f6;color:#47616f;font-size:11px;font-weight:700}.bb-badge.ready,.bb-badge.available,.bb-badge.delivered{background:#dcfce7;color:#166534}.bb-badge.emergency,.bb-badge.expired,.bb-badge.cancelled{background:#fee2e2;color:#991b1b}.bb-badge.urgent,.bb-badge.reserved{background:#fff2ce;color:#92400e}.bb-badge.compatibility{background:#e0f2fe;color:#075985}.bb-code{font-family:monospace;font-weight:700;color:#8e1739}.bb-stock{display:grid;grid-template-columns:repeat(4,1fr);gap:9px}.bb-blood{border:1px solid #f1ccd6;border-radius:11px;text-align:center;padding:11px 5px;background:#fff8fa}.bb-blood strong{display:block;color:#97193b;font-size:20px}.bb-blood span{font-size:11px;color:#687d88}.bb-request{padding:16px 18px;border-bottom:1px solid #e8eef1}.bb-request:last-child{border:0}.bb-request-title{display:flex;justify-content:space-between;gap:10px}.bb-request h3{font-size:15px;margin:0 0 4px}.bb-meta{color:#718692;font-size:12px}.bb-request-actions{display:flex;gap:7px;align-items:center;flex-wrap:wrap;margin-top:11px}.bb-request-actions form{margin:0;display:flex;gap:6px}.bb-request-actions select{min-width:210px}.bb-due.overdue{color:#b91c1c;font-weight:700}.bb-empty{text-align:center;padding:34px;color:#7a8e98}.bb-modal .modal-content{border-radius:15px;overflow:hidden}.bb-modal .modal-header{background:#8e1739;color:#fff}.bb-modal .modal-title{font-weight:700}.bb-modal .form-group label{font-size:12px;text-transform:uppercase;color:#526c79}.bb-note{background:#fff7ed;border-left:4px solid #f59e0b;padding:10px 12px;font-size:12px;margin-top:12px}.bb-pager{padding:12px 18px}.bb-pager .pagination{margin:0}
.bb-patient-picker{position:relative}.bb-patient-results{position:absolute;z-index:1060;top:100%;left:0;right:0;max-height:230px;overflow:auto;background:#fff;border:1px solid #cfdde3;border-radius:0 0 9px 9px;box-shadow:0 10px 24px rgba(20,54,72,.16)}.bb-patient-option{display:block;width:100%;padding:10px 12px;border:0;border-bottom:1px solid #edf2f4;background:#fff;text-align:left}.bb-patient-option:hover,.bb-patient-option:focus{background:#fff3f6;outline:0}.bb-patient-option strong{display:block;color:#17364a}.bb-patient-option small{color:#718692}.bb-unit-list{display:flex;gap:7px;flex-wrap:wrap;margin-top:9px}.bb-unit-assignment{display:inline-flex;align-items:center;gap:6px;padding:5px 7px;border:1px solid #ead6dc;border-radius:8px;background:#fff8fa}.bb-unit-assignment form{margin:0}.bb-unit-assignment .btn{padding:1px 5px;line-height:1.25}.bb-assigned-patient{display:block;margin-top:4px;color:#526c79;font-size:10px;line-height:1.25}.bb-request-actions .bb-auto-form{display:inline-flex}.bb-unit-actions{white-space:nowrap}.bb-unit-actions .btn{border-radius:7px}.bb-unit-lock{color:#9aaab2;font-size:13px}
@media(max-width:1050px){.bb-grid{grid-template-columns:1fr}.bb-stats{grid-template-columns:repeat(2,1fr)}}@media(max-width:650px){.bb-page{padding:12px}.bb-top{align-items:flex-start;flex-direction:column}.bb-actions .btn{margin:4px 4px 0 0}.bb-stats{grid-template-columns:1fr}.bb-stock{grid-template-columns:repeat(2,1fr)}}
</style>
<div class="bb-page"><div class="bb-wrap">
    <section class="bb-top">
        <div><small>HEMOTERAPIA Y MEDICINA TRANSFUSIONAL</small><h1><i class="fa fa-tint"></i> Banco de sangre</h1><p>Inventario, compatibilidad y entrega segura de hemocomponentes.</p></div>
        <div class="bb-actions"><button class="btn btn-light" data-toggle="modal" data-target="#newUnit"><i class="fa fa-plus"></i> Registrar unidad</button><button class="btn bb-new-request" data-toggle="modal" data-target="#newRequest"><i class="fa fa-file-text-o"></i> Nueva solicitud</button></div>
    </section>

    @if(session('success'))<div class="alert alert-success" style="margin-top:15px">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger" style="margin-top:15px">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" style="margin-top:15px"><strong>Revisa los datos:</strong> {{ $errors->first() }}</div>@endif

    <div class="bb-stats">
        <div class="bb-stat"><i class="fa fa-tint"></i><div><strong>{{ $stats['available'] }}</strong><span>Unidades disponibles</span></div></div>
        <div class="bb-stat"><i class="fa fa-clock-o"></i><div><strong>{{ $stats['expiring'] }}</strong><span>Vencen en 7 días</span></div></div>
        <div class="bb-stat"><i class="fa fa-flask"></i><div><strong>{{ $stats['pending'] }}</strong><span>En preparación</span></div></div>
        <div class="bb-stat"><i class="fa fa-check-circle"></i><div><strong>{{ $stats['ready'] }}</strong><span>Listas para entrega</span></div></div>
    </div>

    <div class="bb-grid"><div>
        <section class="bb-card">
            <div class="bb-card-head"><h2><i class="fa fa-exchange text-danger"></i> Solicitudes transfusionales</h2><span class="bb-badge">{{ $requests->total() }} solicitudes</span></div>
            <form class="bb-filter row" method="GET">
                <div class="col-sm-5"><input class="form-control" name="patient" value="{{ request('patient') }}" placeholder="Paciente, identificación"></div>
                <div class="col-sm-4"><select class="form-control" name="request_status"><option value="">Todos los estados</option>@foreach($statusLabels as $value=>$label)<option value="{{ $value }}" {{ request('request_status')===$value?'selected':'' }}>{{ $label }}</option>@endforeach</select></div>
                <div class="col-sm-3"><button class="btn btn-danger"><i class="fa fa-search"></i> Filtrar</button> <a class="btn btn-default" href="{{ route('blood_bank.index') }}">Limpiar</a></div>
            </form>
            @forelse($requests as $bloodRequest)
            @php $reserved = $bloodRequest->units->where('pivot.status','RESERVED')->count(); $overdue = !in_array($bloodRequest->status,array('DELIVERED','CANCELLED')) && $bloodRequest->due_at && $bloodRequest->due_at->isPast(); @endphp
            <article class="bb-request">
                <div class="bb-request-title"><div><h3>{{ $bloodRequest->patient_name }} <small>· {{ $bloodRequest->patient_id }}</small></h3><div class="bb-meta">{{ $components[$bloodRequest->component] }} · {{ $bloodRequest->blood_type }} · {{ $reserved }}/{{ $bloodRequest->units_required }} unidades reservadas</div></div><div style="text-align:right"><span class="bb-badge {{ strtolower($bloodRequest->priority) }}">{{ $priorityLabels[$bloodRequest->priority] }}</span> <span class="bb-badge {{ strtolower($bloodRequest->status) }}">{{ $statusLabels[$bloodRequest->status] }}</span><div class="bb-meta bb-due {{ $overdue?'overdue':'' }}">Límite: {{ $bloodRequest->due_at?$bloodRequest->due_at->format('d/m/Y H:i'):'N/D' }}</div></div></div>
                <div class="bb-note"><strong>Indicación:</strong> {{ $bloodRequest->indication }}</div>
                @if($bloodRequest->units->count())<div class="bb-unit-list">@foreach($bloodRequest->units as $allocated)<div class="bb-unit-assignment"><span class="bb-code">{{ $allocated->code }}</span><span class="bb-badge {{ strtolower($allocated->pivot->status) }}">{{ isset($allocationLabels[$allocated->pivot->status])?$allocationLabels[$allocated->pivot->status]:$allocated->pivot->status }}</span>@if($allocated->pivot->status==='RESERVED' && in_array($bloodRequest->status,array('COMPATIBILITY','READY')))<form method="POST" action="{{ route('blood_bank.requests.release',array($bloodRequest->id,$allocated->id)) }}" onsubmit="return confirm('¿Liberar esta unidad y devolverla al inventario?')">{{ csrf_field() }}<button class="btn btn-xs btn-default text-danger" title="Liberar unidad"><i class="fa fa-times"></i></button></form>@endif</div>@endforeach</div>@endif
                <div class="bb-request-actions">
                    @if(in_array($bloodRequest->status,array('PENDING','COMPATIBILITY','READY')) && $reserved < $bloodRequest->units_required)
                    @if(in_array($bloodRequest->status,array('PENDING','COMPATIBILITY')))<form class="bb-auto-form" method="POST" action="{{ route('blood_bank.requests.auto_assign',$bloodRequest->id) }}">{{ csrf_field() }}<button class="btn btn-sm btn-danger" title="Asigna primero las unidades compatibles que vencen antes"><i class="fa fa-magic"></i> Asignar automáticamente</button></form>@endif
                    <form method="POST" action="{{ route('blood_bank.requests.reserve',$bloodRequest->id) }}">{{ csrf_field() }}
                        <select class="form-control input-sm" name="unit_id" required><option value="">Unidad compatible...</option>@foreach($availableUnits as $available)@if($available->component===$bloodRequest->component && \Sis_medico\Http\Controllers\hospital\BloodBankController::isCompatible($available->blood_type,$bloodRequest->blood_type,$available->component))<option value="{{ $available->id }}">{{ $available->code }} · {{ $available->blood_type }} · vence {{ $available->expires_at->format('d/m/Y') }}</option>@endif @endforeach</select><button class="btn btn-sm btn-primary"><i class="fa fa-lock"></i> Reservar</button>
                    </form>
                    @endif
                    @if($bloodRequest->status==='READY')<form method="POST" action="{{ route('blood_bank.requests.transition',$bloodRequest->id) }}">{{ csrf_field() }}<input type="hidden" name="status" value="DELIVERED"><button class="btn btn-sm btn-success"><i class="fa fa-check"></i> Confirmar entrega</button></form>@endif
                    @if(!in_array($bloodRequest->status,array('DELIVERED','CANCELLED')))<form method="POST" action="{{ route('blood_bank.requests.transition',$bloodRequest->id) }}" onsubmit="return confirm('¿Cancelar esta solicitud y liberar sus unidades?')">{{ csrf_field() }}<input type="hidden" name="status" value="CANCELLED"><button class="btn btn-sm btn-default text-danger">Cancelar</button></form>@endif
                </div>
            </article>
            @empty <div class="bb-empty"><i class="fa fa-inbox fa-2x"></i><p>No hay solicitudes para los filtros seleccionados.</p></div>@endforelse
            <div class="bb-pager">{{ $requests->appends(request()->except('requests_page'))->links() }}</div>
        </section>
    </div><aside>
        <section class="bb-card"><div class="bb-card-head"><h2>Stock por grupo</h2><span class="bb-badge available">Disponible</span></div><div class="bb-card-body"><div class="bb-stock">@foreach($types as $type)<div class="bb-blood"><strong>{{ isset($stock[$type])?$stock[$type]:0 }}</strong><span>{{ $type }}</span></div>@endforeach</div></div></section>
        <section class="bb-card"><div class="bb-card-head"><h2>Inventario</h2><span class="bb-badge">FEFO</span></div>
            <form class="bb-filter" method="GET"><div class="row"><div class="col-xs-3"><select class="form-control input-sm" name="blood_type"><option value="">Grupo</option>@foreach($types as $type)<option {{ request('blood_type')===$type?'selected':'' }}>{{ $type }}</option>@endforeach</select></div><div class="col-xs-4"><select class="form-control input-sm" name="component"><option value="">Componente</option>@foreach($components as $value=>$label)<option value="{{ $value }}" {{ request('component')===$value?'selected':'' }}>{{ $label }}</option>@endforeach</select></div><div class="col-xs-3"><select class="form-control input-sm" name="unit_status"><option value="">Estado</option>@foreach($unitLabels as $value=>$label)<option value="{{ $value }}" {{ request('unit_status')===$value?'selected':'' }}>{{ $label }}</option>@endforeach</select></div><div class="col-xs-2"><button class="btn btn-sm btn-danger"><i class="fa fa-search"></i></button></div></div></form>
            <div class="table-responsive"><table class="table bb-table"><thead><tr><th>Unidad</th><th>Componente</th><th>Vence</th><th>Estado</th><th>Acción</th></tr></thead><tbody>@forelse($units as $unit)@php $currentAssignment=$unit->allocations->first(function($allocation){return in_array($allocation->status,array('RESERVED','DELIVERED'),true);}); @endphp<tr><td><span class="bb-code">{{ $unit->code }}</span><br><strong>{{ $unit->blood_type }}</strong>@if($currentAssignment && $currentAssignment->request)<span class="bb-assigned-patient"><i class="fa fa-user"></i> {{ $currentAssignment->request->patient_name }}<br>{{ $currentAssignment->request->patient_id }}</span>@endif</td><td>{{ isset($components[$unit->component])?$components[$unit->component]:$unit->component }}<br><small>{{ $unit->volume_ml }} ml</small></td><td>{{ $unit->expires_at->format('d/m/Y H:i') }}<br><small>{{ $unit->location?:'Sin ubicación' }}</small></td><td><span class="bb-badge {{ strtolower($unit->status) }}">{{ isset($unitLabels[$unit->status])?$unitLabels[$unit->status]:$unit->status }}</span>@if(!in_array($unit->status,array('RESERVED','USED','DISCARDED','EXPIRED')))<form method="POST" action="{{ route('blood_bank.units.status',$unit->id) }}" style="margin-top:5px">{{ csrf_field() }}<select name="status" class="form-control input-sm" onchange="this.form.submit()"><option value="">Cambiar...</option><option value="AVAILABLE">Disponible</option><option value="QUARANTINED">Cuarentena</option><option value="DISCARDED">Descartar</option></select></form>@endif</td><td class="bb-unit-actions">@if(!in_array($unit->status,array('RESERVED','USED'),true))<button type="button" class="btn btn-xs btn-default bb-edit-unit" title="Editar unidad" data-toggle="modal" data-target="#editUnit" data-url="{{ route('blood_bank.units.update',$unit->id) }}" data-code="{{ $unit->code }}" data-blood-type="{{ $unit->blood_type }}" data-component="{{ $unit->component }}" data-volume="{{ $unit->volume_ml }}" data-donor-code="{{ $unit->donor_code }}" data-collected-at="{{ $unit->collected_at->format('Y-m-d\TH:i') }}" data-expires-at="{{ $unit->expires_at->format('Y-m-d\TH:i') }}" data-location="{{ $unit->location }}"><i class="fa fa-pencil"></i> Editar</button>@else<span class="bb-unit-lock" title="{{ $unit->status==='RESERVED'?'Libere la unidad antes de editarla':'Las unidades utilizadas conservan sus datos históricos' }}"><i class="fa fa-lock"></i></span>@endif</td></tr>@empty<tr><td colspan="5" class="bb-empty">Sin unidades registradas.</td></tr>@endforelse</tbody></table></div><div class="bb-pager">{{ $units->appends(request()->except('units_page'))->links() }}</div>
        </section>
    </aside></div>
</div></div>

<div class="modal fade bb-modal" id="newUnit" tabindex="-1"><div class="modal-dialog"><form class="modal-content" method="POST" action="{{ route('blood_bank.units.store') }}">{{ csrf_field() }}<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title"><i class="fa fa-tint"></i> Registrar unidad</h4></div><div class="modal-body"><div class="row">
    <div class="form-group col-sm-6"><label>Código de unidad</label><input class="form-control" name="code" required value="{{ old('code') }}" placeholder="BS-2026-0001"></div><div class="form-group col-sm-3"><label>Grupo</label><select class="form-control" name="blood_type" required>@foreach($types as $type)<option>{{ $type }}</option>@endforeach</select></div><div class="form-group col-sm-3"><label>Volumen ml</label><input class="form-control" type="number" name="volume_ml" value="450" min="20" max="1000" required></div>
    <div class="form-group col-sm-6"><label>Hemocomponente</label><select class="form-control" name="component" required>@foreach($components as $value=>$label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div><div class="form-group col-sm-6"><label>Código donante (anonimizado)</label><input class="form-control" name="donor_code" value="{{ old('donor_code') }}"></div>
    <div class="form-group col-sm-6"><label>Fecha de recolección</label><input class="form-control" type="datetime-local" name="collected_at" required></div><div class="form-group col-sm-6"><label>Fecha de vencimiento</label><input class="form-control" type="datetime-local" name="expires_at" required></div><div class="form-group col-sm-12"><label>Ubicación</label><input class="form-control" name="location" placeholder="Refrigerador 1 · Bandeja A"></div>
    </div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button class="btn btn-danger">Guardar unidad</button></div></form></div></div>

<div class="modal fade bb-modal" id="editUnit" tabindex="-1"><div class="modal-dialog"><form class="modal-content" id="bb_edit_unit_form" method="POST">{{ csrf_field() }}<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title"><i class="fa fa-pencil"></i> Editar unidad</h4></div><div class="modal-body"><div class="row">
    <div class="form-group col-sm-6"><label>Código de unidad</label><input class="form-control" id="bb_edit_code" name="code" required maxlength="40"></div><div class="form-group col-sm-3"><label>Grupo</label><select class="form-control" id="bb_edit_blood_type" name="blood_type" required>@foreach($types as $type)<option>{{ $type }}</option>@endforeach</select></div><div class="form-group col-sm-3"><label>Volumen ml</label><input class="form-control" id="bb_edit_volume" type="number" name="volume_ml" min="20" max="1000" required></div>
    <div class="form-group col-sm-6"><label>Hemocomponente</label><select class="form-control" id="bb_edit_component" name="component" required>@foreach($components as $value=>$label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div><div class="form-group col-sm-6"><label>Código donante (anonimizado)</label><input class="form-control" id="bb_edit_donor_code" name="donor_code" maxlength="40"></div>
    <div class="form-group col-sm-6"><label>Fecha de recolección</label><input class="form-control" id="bb_edit_collected_at" type="datetime-local" name="collected_at" required></div><div class="form-group col-sm-6"><label>Fecha de vencimiento</label><input class="form-control" id="bb_edit_expires_at" type="datetime-local" name="expires_at" required></div><div class="form-group col-sm-12"><label>Ubicación</label><input class="form-control" id="bb_edit_location" name="location" maxlength="120"></div>
    </div><div class="bb-note"><i class="fa fa-info-circle"></i> Si corrige el vencimiento de una unidad vencida por una fecha futura, volverá automáticamente a estar disponible.</div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button class="btn btn-danger"><i class="fa fa-save"></i> Guardar cambios</button></div></form></div></div>

<div class="modal fade bb-modal" id="newRequest" tabindex="-1"><div class="modal-dialog modal-lg"><form class="modal-content" method="POST" action="{{ route('blood_bank.requests.store') }}">{{ csrf_field() }}<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title"><i class="fa fa-file-text-o"></i> Nueva solicitud transfusional</h4></div><div class="modal-body"><div class="row">
    <div class="form-group col-sm-5 bb-patient-picker"><label>Buscar paciente registrado</label><input class="form-control" id="bb_patient_search" autocomplete="off" placeholder="Cédula, nombres o apellidos"><div id="bb_patient_results" class="bb-patient-results" style="display:none"></div><input type="hidden" id="bb_patient_id" name="patient_id" value="{{ old('patient_id') }}"></div><div class="form-group col-sm-4"><label>Paciente seleccionado</label><input class="form-control" id="bb_patient_name" name="patient_name" value="{{ old('patient_name') }}" readonly required placeholder="Seleccione un paciente"></div><div class="form-group col-sm-3"><label>Episodio activo</label><input class="form-control" id="bb_encounter_id" name="encounter_id" value="{{ old('encounter_id') }}" readonly placeholder="Sin episodio activo"></div>
    <div class="form-group col-sm-3"><label>Grupo receptor</label><select class="form-control" id="bb_blood_type" name="blood_type">@foreach($types as $type)<option {{ old('blood_type')===$type?'selected':'' }}>{{ $type }}</option>@endforeach</select></div><div class="form-group col-sm-4"><label>Hemocomponente</label><select class="form-control" name="component">@foreach($components as $value=>$label)<option value="{{ $value }}" {{ old('component')===$value?'selected':'' }}>{{ $label }}</option>@endforeach</select></div><div class="form-group col-sm-2"><label>Unidades</label><input class="form-control" type="number" min="1" max="20" value="{{ old('units_required',1) }}" name="units_required"></div><div class="form-group col-sm-3"><label>Prioridad</label><select class="form-control" name="priority"><option value="ROUTINE">Programada · 24 h</option><option value="URGENT">Urgente · 4 h</option><option value="EMERGENCY">Emergencia · 30 min</option></select></div>
    <div class="form-group col-sm-12"><label>Indicación clínica</label><textarea class="form-control" name="indication" rows="3" minlength="10" required>{{ old('indication') }}</textarea></div><div class="form-group col-sm-12"><label>Observaciones</label><textarea class="form-control" name="notes" rows="2">{{ old('notes') }}</textarea></div>
    </div><div class="bb-note"><i class="fa fa-shield"></i> La compatibilidad se valida al reservar cada unidad. La entrega solo se habilita cuando se completa la cantidad requerida.</div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button class="btn btn-danger">Crear solicitud</button></div></form></div></div>
<script>
(function($){
    $('.bb-edit-unit').on('click',function(){
        var $button=$(this);
        $('#bb_edit_unit_form').attr('action',$button.data('url'));
        $('#bb_edit_code').val($button.attr('data-code'));
        $('#bb_edit_blood_type').val($button.attr('data-blood-type'));
        $('#bb_edit_component').val($button.attr('data-component'));
        $('#bb_edit_volume').val($button.attr('data-volume'));
        $('#bb_edit_donor_code').val($button.attr('data-donor-code'));
        $('#bb_edit_collected_at').val($button.attr('data-collected-at'));
        $('#bb_edit_expires_at').val($button.attr('data-expires-at'));
        $('#bb_edit_location').val($button.attr('data-location'));
    });
    var timer = null;
    var searchUrl = @json(route('blood_bank.patients.search'));
    var $search = $('#bb_patient_search'), $results = $('#bb_patient_results');
    function escapeHtml(value){ return $('<div>').text(value == null ? '' : String(value)).html(); }
    function hideResults(){ $results.hide().empty(); }
    function selectPatient(patient){
        $('#bb_patient_id').val(patient.id);
        $('#bb_patient_name').val(patient.name);
        $('#bb_encounter_id').val(patient.encounter_id || '');
        if(patient.blood_type) $('#bb_blood_type').val(patient.blood_type);
        $search.val(patient.text);
        hideResults();
    }
    $search.on('input', function(){
        window.clearTimeout(timer);
        $('#bb_patient_id,#bb_patient_name,#bb_encounter_id').val('');
        var term = $.trim($search.val());
        if(term.length < 2){ hideResults(); return; }
        timer = window.setTimeout(function(){
            $.getJSON(searchUrl,{term:term}).done(function(patients){
                if(!patients.length){ $results.html('<div class="bb-patient-option text-muted">No se encontraron pacientes en esta empresa.</div>').show(); return; }
                $results.html(patients.map(function(patient,index){
                    return '<button type="button" class="bb-patient-option" data-index="'+index+'"><strong>'+escapeHtml(patient.name)+'</strong><small>'+escapeHtml(patient.id)+(patient.blood_type?' · Grupo '+escapeHtml(patient.blood_type):' · Grupo no registrado')+'</small></button>';
                }).join('')).data('patients',patients).show();
            }).fail(function(){ $results.html('<div class="bb-patient-option text-danger">No fue posible consultar pacientes.</div>').show(); });
        },300);
    });
    $results.on('click','.bb-patient-option[data-index]',function(){
        var patients=$results.data('patients')||[];
        selectPatient(patients[Number($(this).data('index'))]);
    });
    $(document).on('click',function(event){ if(!$(event.target).closest('.bb-patient-picker').length) hideResults(); });
    $('#newRequest form').on('submit',function(event){
        if(!$('#bb_patient_id').val()){
            event.preventDefault();
            $search.focus();
            alert('Seleccione un paciente registrado antes de crear la solicitud.');
        }
    });
})(jQuery);
</script>
@endsection
