@extends('layouts.app-template-h')

@section('content')
<style>
  .ef-page { padding: 22px; background: #f4f7fb; min-height: calc(100vh - 60px); color: #172033; }
  .ef-hero { background: linear-gradient(125deg,#102a43,#164e63); color:#fff; padding:24px 26px; border-radius:16px; box-shadow:0 12px 30px rgba(15,42,67,.16); margin-bottom:18px; }
  .ef-hero h2 { margin:0 0 5px; font-weight:700; letter-spacing:-.3px; }
  .ef-hero p { margin:0; opacity:.82; }
  .ef-actions { display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end; }
  .ef-stat { background:#fff; border:1px solid #e5eaf0; border-radius:13px; padding:15px 17px; margin-bottom:15px; box-shadow:0 4px 14px rgba(15,23,42,.04); }
  .ef-stat strong { display:block; font-size:25px; line-height:1; margin-bottom:6px; }
  .ef-stat span { color:#64748b; font-size:12px; text-transform:uppercase; font-weight:700; letter-spacing:.5px; }
  .ef-panel { background:#fff; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; box-shadow:0 5px 18px rgba(15,23,42,.05); }
  .ef-toolbar { padding:15px; border-bottom:1px solid #e8edf3; display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
  .ef-search { min-width:280px; flex:1; }
  .ef-table { margin:0; }
  .ef-table thead th { border:0; background:#f8fafc; color:#64748b; text-transform:uppercase; font-size:11px; letter-spacing:.5px; padding:12px; }
  .ef-table td { vertical-align:middle!important; padding:13px 12px!important; border-color:#edf1f5!important; }
  .ef-patient { font-weight:700; color:#172033; }
  .ef-id { color:#718096; font-size:12px; margin-top:2px; }
  .ef-priority { display:inline-flex; align-items:center; gap:7px; padding:5px 9px; border-radius:20px; font-weight:700; font-size:12px; border:1px solid rgba(0,0,0,.06); }
  .ef-dot { width:9px; height:9px; border-radius:50%; display:inline-block; }
  .ef-location { display:inline-flex; align-items:center; gap:6px; background:#f1f5f9; border-radius:7px; padding:6px 8px; font-size:12px; color:#334155; }
  .ef-status { font-size:11px; font-weight:800; letter-spacing:.3px; color:#475569; }
  .ef-clock { font-weight:800; font-variant-numeric:tabular-nums; }
  .ef-clock.overdue { color:#b91c1c; }
  .ef-overdue { background:#fff7f7; }
  .ef-row.ef-focused { background:#eff8ff; box-shadow:inset 4px 0 #0877be; }
  .ef-buttons { display:flex; gap:5px; flex-wrap:wrap; }
  .ef-empty { padding:50px 20px; text-align:center; color:#64748b; }
  .ef-empty i { display:block; font-size:36px; margin-bottom:12px; color:#94a3b8; }
  .ef-help { background:#eff6ff; border:1px solid #bfdbfe; color:#1e3a5f; border-radius:10px; padding:10px 12px; font-size:12px; margin-bottom:14px; }
  .ef-modal-title small { display:block; color:#64748b; margin-top:3px; font-size:12px; }
  #patientAdmissionModal .modal-dialog { width:94%; max-width:1440px; margin:20px auto; }
  #patientAdmissionModal .modal-content { max-height:calc(100vh - 40px); overflow:hidden; border:0; border-radius:16px; }
  #patientAdmissionModal .modal-header { flex:0 0 auto; color:#fff; background:linear-gradient(125deg,#102a43,#087b85); border:0; padding:16px 22px; }
  #patientAdmissionModal .modal-header .close { color:#fff; opacity:.9; text-shadow:none; }
  #patientAdmissionModal .modal-body { overflow-y:auto; padding:0; background:#f4f7fb; }
  #patientAdmissionModal .ea-page { padding:20px 24px 30px; }
  #patientAdmissionModal .ea-summary { top:16px; }
  #registro-paciente-emergencia { z-index:1070; }
  #progressModal .modal-dialog { width:92% !important; max-width:1440px !important; min-height:calc(100% - 2rem); margin:1rem auto; display:flex; align-items:center; }
  #progressModal .modal-content { width:100%; height:calc(100vh - 2rem); overflow:hidden; border:0; border-radius:14px; }
  #progressModal .modal-header { flex:0 0 auto; align-items:center; color:#fff; background:linear-gradient(125deg,#102a43,#087b85); border:0; }
  #progressModal .modal-title,
  #progressModal .ef-modal-patient { color:#fff !important; }
  #progressModal .ef-modal-patient { opacity:.85; }
  #progressModal .modal-body { position:relative; flex:1 1 auto; min-height:0; padding:0; overflow:hidden; background:#f4f8fa; }
  #progressModalFrame { display:block; width:100%; height:100%; border:0; background:#f4f8fa; }
  #progressModalLoading { position:absolute; inset:0; z-index:1; display:flex; align-items:center; justify-content:center; color:#64748b; background:#f4f8fa; }
  #triageModal .modal-dialog { width:78% !important; max-width:1180px !important; min-height:calc(100% - 3.5rem); margin:1.75rem auto; display:flex; align-items:center; }
  #triageModal .modal-content { width:100%; max-height:calc(100vh - 3.5rem); overflow:hidden; border:0; border-radius:14px; }
  #triageModal .modal-header,
  #triageModal .modal-footer { flex:0 0 auto; }
  #triageModal .modal-body { overflow-y:auto; padding:20px 24px; }
  #moveModal .modal-dialog,
  #criticalModal .modal-dialog { width:40% !important; max-width:40% !important; }
  #annulEpisodeModal .modal-dialog { width:40% !important; max-width:40% !important; min-height:calc(100% - 3.5rem); margin:1.75rem auto; display:flex; align-items:center; }
  #annulEpisodeModal .modal-content { width:100%; border:0; border-radius:12px; overflow:hidden; }
  #annulEpisodeModal .ef-modal-patient { opacity:.9; }
  #patientAdmissionModal .modal-header,
  #progressModal .modal-header,
  #criticalModal .modal-header,
  #triageModal .modal-header,
  #moveModal .modal-header,
  #annulEpisodeModal .modal-header { align-items:center; color:#fff !important; background:linear-gradient(125deg,#102a43,#087b85) !important; border:0; }
  #patientAdmissionModal .modal-header .modal-title,
  #patientAdmissionModal .modal-header small,
  #progressModal .modal-header .modal-title,
  #progressModal .modal-header small,
  #criticalModal .modal-header .modal-title,
  #criticalModal .modal-header small,
  #triageModal .modal-header .modal-title,
  #triageModal .modal-header small,
  #moveModal .modal-header .modal-title,
  #moveModal .modal-header small,
  #annulEpisodeModal .modal-header .modal-title,
  #annulEpisodeModal .modal-header small { color:#fff !important; }
  #patientAdmissionModal .modal-header .modal-title i,
  #progressModal .modal-header .modal-title i,
  #criticalModal .modal-header .modal-title i,
  #triageModal .modal-header .modal-title i,
  #moveModal .modal-header .modal-title i,
  #annulEpisodeModal .modal-header .modal-title i { color:#fff !important; }
  #patientAdmissionModal .modal-header .close,
  #progressModal .modal-header .close,
  #criticalModal .modal-header .close,
  #triageModal .modal-header .close,
  #moveModal .modal-header .close,
  #annulEpisodeModal .modal-header .close { display:flex; align-items:center; justify-content:center; flex:0 0 40px; width:40px; height:40px; margin:-6px -6px -6px auto; padding:0; border:0; border-radius:9px; color:#fff !important; background:rgba(255,255,255,.14) !important; font-size:30px; font-weight:400; line-height:1; opacity:1 !important; text-shadow:none; box-shadow:none; }
  #patientAdmissionModal .modal-header .close:hover,
  #patientAdmissionModal .modal-header .close:focus,
  #progressModal .modal-header .close:hover,
  #progressModal .modal-header .close:focus,
  #criticalModal .modal-header .close:hover,
  #criticalModal .modal-header .close:focus,
  #triageModal .modal-header .close:hover,
  #triageModal .modal-header .close:focus,
  #moveModal .modal-header .close:hover,
  #moveModal .modal-header .close:focus,
  #annulEpisodeModal .modal-header .close:hover,
  #annulEpisodeModal .modal-header .close:focus { color:#fff !important; background:rgba(255,255,255,.25) !important; outline:2px solid rgba(255,255,255,.65); outline-offset:2px; }
  #moveModal .modal-header { color:#fff !important; background:linear-gradient(125deg,#102a43,#087b85) !important; }
  #moveModalClose { display:flex !important; align-items:center !important; justify-content:center !important; flex:0 0 40px !important; width:40px !important; height:40px !important; margin:-6px -6px -6px auto !important; padding:0 !important; border:0 !important; border-radius:9px !important; color:#fff !important; background:rgba(255,255,255,.14) !important; opacity:1 !important; box-shadow:none !important; text-shadow:none !important; }
  #moveModalClose span { display:block; color:#fff !important; font-size:30px !important; font-weight:400 !important; line-height:1 !important; transform:translateY(-1px); }
  #moveModalClose:hover,
  #moveModalClose:focus { color:#fff !important; background:rgba(255,255,255,.25) !important; outline:2px solid rgba(255,255,255,.65) !important; outline-offset:2px; }
  #moveModal textarea[name="reason"] { min-height:100px; resize:vertical; }
  @media(max-width:991px){ #progressModal .modal-dialog{width:96% !important;max-width:96% !important} #triageModal .modal-dialog{width:90% !important;max-width:90% !important} #moveModal .modal-dialog,#criticalModal .modal-dialog,#annulEpisodeModal .modal-dialog{width:70% !important;max-width:70% !important} }
  @media(max-width:767px){ .ef-page{padding:12px}.ef-actions{justify-content:flex-start;margin-top:14px}.ef-table{min-width:920px} }
  @media(max-width:575px){ #progressModal .modal-dialog,#triageModal .modal-dialog,#moveModal .modal-dialog,#criticalModal .modal-dialog,#annulEpisodeModal .modal-dialog{width:auto !important;max-width:none !important;min-height:calc(100% - 24px);margin:12px} #progressModal .modal-content{height:calc(100vh - 24px)} #triageModal .modal-content{max-height:calc(100vh - 24px)} #triageModal .modal-body{padding:16px} }
</style>
<link rel="stylesheet" href="{{ asset('css/emergency-flow.css') }}?v=1">

@php
  $pending = collect($patients)->filter(function($p){ return is_null($p['triage']); })->count();
  $critical = collect($patients)->filter(function($p){ return $p['level'] <= 2; })->count();
  $waitingBed = collect($patients)->filter(function($p){ return $p['flow'] && $p['flow']->bed_request_status === 'REQUESTED'; })->count();
  $role = (int) Auth::user()->id_tipo_usuario;
  $isSystemUser = Auth::user()->isSystemUser();
  $policyRole = $isSystemUser ? 1 : $role;
  $formatElapsed = function ($minutes) {
    $minutes = max(0, (int) $minutes);
    if ($minutes < 60) {
      return $minutes.' '.($minutes === 1 ? 'minuto' : 'minutos');
    }
    $hours = intdiv($minutes, 60);
    if ($hours < 24) {
      $remainingMinutes = $minutes % 60;
      return $hours.' '.($hours === 1 ? 'hora' : 'horas')
        .($remainingMinutes ? ' '.$remainingMinutes.' '.($remainingMinutes === 1 ? 'minuto' : 'minutos') : '');
    }
    $days = intdiv($hours, 24);
    if ($days < 30) {
      $remainingHours = $hours % 24;
      return $days.' '.($days === 1 ? 'día' : 'días')
        .($remainingHours ? ' '.$remainingHours.' '.($remainingHours === 1 ? 'hora' : 'horas') : '');
    }
    $months = intdiv($days, 30);
    if ($months < 12) {
      $remainingDays = $days % 30;
      return $months.' '.($months === 1 ? 'mes' : 'meses')
        .($remainingDays ? ' '.$remainingDays.' '.($remainingDays === 1 ? 'día' : 'días') : '');
    }
    $years = intdiv($months, 12);
    $remainingMonths = $months % 12;
    return $years.' '.($years === 1 ? 'año' : 'años')
      .($remainingMonths ? ' '.$remainingMonths.' '.($remainingMonths === 1 ? 'mes' : 'meses') : '');
  };
  $statusLabels = array('TRANSFERRED'=>'Traslado recibido','CLOSED'=>'Salida completada','CLOSURE_PENDING'=>'Alta firmada - pendiente de salida fisica','PENDING_TRIAGE'=>'Pendiente de triaje','WAITING_ATTENTION'=>'Espera atención','IN_ATTENTION'=>'En atención','UNDER_OBSERVATION'=>'En observación','TRANSFER_PENDING'=>'Traslado pendiente','DECISION_RECORDED'=>'Decision registrada - pendiente de cierre');
@endphp

<div class="ef-page">
  <div class="ef-hero">
    <div class="row align-items-center">
      <div class="col-md-7">
        <span class="ef-eyebrow">ATENCIÓN HOSPITALARIA</span>
        <h2>Emergencia</h2>
        <p>Registre un ingreso o continúe la atención de sus pacientes.</p>

      </div>
      <div class="col-md-5 ef-actions">
        @if($isSystemUser || in_array($role, [3, 6], true))<button type="button" class="btn ef-new-arrival" data-toggle="modal" data-target="#patientAdmissionModal"><i class="fas fa-plus mr-1" aria-hidden="true"></i> Ingresar paciente</button>@endif
        <button type="button" class="btn ef-critical-arrival" data-toggle="modal" data-target="#criticalModal"><i class="fas fa-bolt mr-1" aria-hidden="true"></i> Ingreso crítico sin identificación</button>
      </div>
    </div>
  </div>

  <div class="ef-journey" aria-label="Flujo de emergencia">
    <span><b>1</b> Identificar paciente</span><i class="fas fa-chevron-right" aria-hidden="true"></i>
    <span><b>2</b> Registrar triaje</span><i class="fas fa-chevron-right" aria-hidden="true"></i>
    <span><b>3</b> Atender y documentar</span><i class="fas fa-chevron-right" aria-hidden="true"></i>
    <span><b>4</b> Definir destino y salida</span>
    <details class="ef-flow-help"><summary>Ver guía</summary><div>
      <p><strong>Ingreso habitual:</strong> busque o registre al paciente, seleccione la prioridad clínica y confirme. Después, use <strong>Atender</strong> en su fila para iniciar la atención.</p>
      <p><strong>Paciente crítico sin identificación:</strong> registre forma de llegada y condición observada. Se crea una identidad provisional; el inicio de atención crítica y el triaje se registran desde la cola.</p>
      <p>Después de <strong>Atender</strong>, continúe en <strong>Valoración y evolución</strong>: documente los hallazgos disponibles mientras se completa la ficha. Abra <strong>Historia 008</strong> para los formularios y órdenes que correspondan. Finalmente registre el destino, el alta o traslado y la salida.</p>
      <a href="{{ route('hospital.ingreso008', array('id_solicitud'=>0)) }}">Abrir ingreso directo por Formulario 008 <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
    </div></details>
  </div>

  @if(session('success')) <div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>{{ session('success') }}</div> @endif
  @if($errors->any()) <div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>{{ $errors->first() }}</div> @endif

  <div class="row">
    <div class="col-6 col-lg-3"><div class="ef-stat"><strong>{{ count($patients) }}</strong><span>Pacientes activos</span></div></div>
    <div class="col-6 col-lg-3"><div class="ef-stat"><strong style="color:#b91c1c">{{ $critical }}</strong><span>Críticos / muy urgentes</span></div></div>
    <div class="col-6 col-lg-3"><div class="ef-stat"><strong style="color:#b45309">{{ $pending }}</strong><span>Pendientes de triaje</span></div></div>
    <div class="col-6 col-lg-3"><div class="ef-stat"><strong style="color:#6d28d9">{{ $waitingBed }}</strong><span>Esperando cama</span></div></div>
  </div>

  <div class="ef-panel">
    <div class="ef-list-heading"><div><h3>Pacientes en Emergencia</h3><p>Ordenados por prioridad y tiempo desde el ingreso.</p></div><span class="ef-list-count" id="efResultCount" role="status">{{ count($patients) }} pacientes</span></div>
    <div class="ef-toolbar">
      <div class="ef-search input-group input-group-sm">
        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
        <input id="efSearch" class="form-control" aria-label="Buscar paciente, identificación o ubicación" placeholder="Buscar paciente, identificación o ubicación">
      </div>
      <select id="efPriorityFilter" class="form-control form-control-sm" aria-label="Filtrar por prioridad clínica" onchange="filterEmergencyPatients()">
        <option value="">Todas las prioridades</option>
        @foreach($priorities as $priority)<option value="{{ $priority->prioridad }}">{{ $priority->nombre }}</option>@endforeach
        <option value="9">Sin triaje</option>
      </select>
      <select id="efStatusFilter" class="form-control form-control-sm" aria-label="Filtrar por estado" onchange="filterEmergencyPatients()">
        <option value="">Todos los estados</option><option value="PENDING_TRIAGE">Pendiente de triaje</option><option value="WAITING_ATTENTION">Espera atención</option><option value="IN_ATTENTION">En atención</option><option value="UNDER_OBSERVATION">En observación</option><option value="TRANSFER_PENDING">Traslado pendiente</option><option value="DECISION_RECORDED">Decisión registrada</option><option value="CLOSURE_PENDING">Pendiente de salida</option>
      </select>
      <button class="btn btn-sm btn-outline-secondary" onclick="window.location.reload()"><i class="fas fa-sync-alt mr-1"></i>Actualizar</button>
    </div>

    <div class="table-responsive">
      <table class="table ef-table" id="efTable">
        <thead><tr><th>Prioridad</th><th>Paciente</th><th>Desde el ingreso</th><th>Estado</th><th>Ubicación actual</th><th>Destino</th><th>Acciones</th></tr></thead>
        <tbody>
        @forelse($patients as $patient)
          @php
            $s=$patient['solicitud']; $f=$patient['flow']; $t=$patient['triage']; $p=$s->paciente;
            $legacyUnidentified=$p && $p->emergency_identity_pending
              && mb_strtoupper(trim((string)$p->nombre1), 'UTF-8') === 'NO'
              && mb_strtoupper(trim((string)$p->apellido1), 'UTF-8') === 'IDENTIFICADO';
            $name=$legacyUnidentified ? 'N/A N/A'
              : ($p ? trim($p->apellido1.' '.$p->apellido2.' '.$p->nombre1.' '.$p->nombre2) : 'Paciente sin datos completos');
            $color=$t && $t->priority_color ? $t->priority_color : ($f && $f->critical_arrival ? '#dc2626' : '#94a3b8');
            $priorityName=$t && $t->priority_name ? $t->priority_name : ($f && $f->critical_arrival ? 'CRITICO - TRIAJE PENDIENTE' : 'PENDIENTE DE TRIAJE');
            $location=$f && $f->location ? $f->location->name : ($t ? 'Por asignar' : 'Triaje');
            $clinicalState = $patient['clinical_state'] ?? null;
            $flowStatus = $f ? $f->status : ($t ? 'WAITING_ATTENTION' : 'PENDING_TRIAGE');
            $pendingExit = $clinicalState === 'CLOSURE_PENDING';
            $readOnly = $pendingExit || ($f && $f->closed_at) || in_array($flowStatus, ['TRANSFERRED', 'CLOSED'], true);
            $status = $pendingExit ? $statusLabels['CLOSURE_PENDING'] : ($statusLabels[$flowStatus] ?? $flowStatus);
            $can = [];
            foreach (['TRIAGE', 'START', 'START_CRITICAL', 'MOVE', 'DECIDE', 'REROUTE_HOSPITALIZATION', 'CANCEL_TRANSFER'] as $action) {
                $can[$action] = \Sis_medico\Services\ClinicalEmergencyFlowPolicy::canAct(
                    $action, $policyRole, $flowStatus, $clinicalState, $readOnly, (bool) $t, $f && $f->critical_arrival);
            }
            if ($f && $f->attention_started_at) $can['START'] = $can['START_CRITICAL'] = false;
            $canAlta = ($isSystemUser || $role === 3) && !$readOnly && $clinicalState === 'EMERGENCY_ACTIVE'
                && $flowStatus === 'DECISION_RECORDED' && $f && in_array($f->disposition, ['DISCHARGE', 'OUTPATIENT', 'TRANSFER'], true);
            $continueCare = !$readOnly && $f && $f->attention_started_at
                && in_array($flowStatus, ['IN_ATTENTION', 'UNDER_OBSERVATION'], true);
            $version = $patient['version'];
            $assessment = $t->assessment_details ?? null;
            $reportedDocument = ($patient['progress'] ?? null) ? ($patient['progress']->clinical_data['reported_document'] ?? '') : '';

          @endphp
          <tr class="ef-row {{ $patient['overdue'] ? 'ef-overdue' : '' }}" data-solicitud="{{ $s->id }}" data-version="{{ $version }}" data-priority="{{ $patient['level'] }}" data-triaged="{{ $t ? '1' : '0' }}" data-status="{{ $pendingExit ? 'CLOSURE_PENDING' : $flowStatus }}" data-search="{{ $name.' '.$s->id_paciente.' '.$location.' '.$reportedDocument }}">
            <td><span class="ef-priority" style="color:{{ $color }};background:{{ $color }}12"><span class="ef-dot" style="background:{{ $color }}"></span>{{ $priorityName }}</span>
              @if(is_array($assessment))
              <div class="ef-id">Glasgow: {{ ($assessment['glasgow_total'] ?? null) !== null ? $assessment['glasgow_total'].'/15' : 'Sin total' }} · E {{ $assessment['eye_response'] ?? '—' }} / V {{ $assessment['verbal_response'] ?? '—' }} / M {{ $assessment['motor_response'] ?? '—' }}</div>
              <details class="ef-id"><summary>Ver valoración</summary>
                @if(!empty($assessment['manchester_flowchart']))<div>Diagrama: {{ $assessment['manchester_flowchart'] }}</div>@endif
                @if($t->discriminator)<div>Discriminador: {{ $t->discriminator }}</div>@endif
                @if(!empty($assessment['glasgow_note']))<div>Glasgow: {{ $assessment['glasgow_note'] }}</div>@endif
                <div>{{ $t->assessed_at }} · Profesional: {{ $t->assessed_by }}</div>
              </details>
              @endif
            </td>
            <td><div class="ef-patient">{{ $name }}</div><div class="ef-id">{{ $s->id_paciente }} · Episodio #{{ $s->id }}</div>
                @if($reportedDocument)<div class="ef-id">Documento comunicado: {{ $reportedDocument }} · Por verificar</div>@endif
                @if($s->paciente && $s->paciente->registro_emergencia_pendiente)<div class="ef-id">Ficha por completar</div>@endif
                @if(($patient['progress'] ?? null) && $patient['progress']->stage === 'DRAFT')<div class="ef-id">Avances guardados · {{ $patient['progress']->created_at }}</div>@endif
            </td>
            <td><div class="ef-clock {{ $patient['overdue'] ? 'overdue' : '' }}">{{ $formatElapsed($patient['elapsed']) }}</div><div class="ef-id">{{ $patient['limit'] === null ? 'Sin objetivo' : ($patient['level'] === 1 ? 'Inmediato' : 'Objetivo '.$patient['limit'].' min') }}</div></td>
            <td><div class="ef-status">{{ strtoupper($status) }}</div>@if($continueCare)<div class="ef-id">Siguiente: valoración y evolución</div>@endif @if($f && $f->assigned_to)<div class="ef-id">Responsable: {{ $f->assigned_to }}</div>@endif @if($f && $f->critical_started_at)<div class="ef-id">Inicio critico: {{ $f->critical_started_at }} · {{ $f->critical_started_by }}</div>@endif @if($f && $f->attention_started_at && !$t && !$readOnly)<div class="ef-id">Triaje pendiente de regularizacion</div>@endif</td>
            <td><span class="ef-location"><i class="fas fa-map-marker-alt"></i>{{ $location }}</span></td>
            <td>@if($f && $f->disposition)<strong>{{ isset($dispositions[$f->disposition]) ? $dispositions[$f->disposition] : $f->disposition }}</strong>@if(!$readOnly && $f->bed_request_status==='REQUESTED')<div class="ef-id">Cama solicitada · <a href="{{ route('hospital.gcuartos', ['traspaso' => $f->requested_transfer_id]) }}">Asignar cama</a></div>@endif @else <span class="text-muted">Por definir</span> @endif</td>
            <td><div class="ef-buttons">
              @if($continueCare)
                <a class="btn btn-sm btn-primary" href="{{ route('hospital.emergency-care.show', ['solicitud'=>$s->id]) }}"><i class="fas fa-notes-medical" aria-hidden="true"></i> Continuar atención</a>
              @endif
              @if($can['TRIAGE'])
                <button type="button" class="btn btn-sm btn-outline-primary ef-open-progress" data-name="{{ $name }} · {{ $s->id_paciente }}" data-url="{{ route('hospital.emergency-flow.progress', ['solicitud'=>$s->id, 'embedded'=>1]) }}">Completar datos</button>
                <button type="button" class="btn btn-sm btn-outline-warning ef-open-triage" data-id="{{ $s->id }}" data-version="{{ $version }}" data-name="{{ $name }} · {{ $s->id_paciente }}" data-url="{{ route('hospital.emergency-flow.triage', ['solicitud'=>$s->id]) }}" data-autosave-url="{{ route('hospital.emergency-flow.progress.autosave', ['solicitud'=>$s->id]) }}" data-draft-url="{{ route('hospital.emergency-flow.progress', ['solicitud'=>$s->id]) }}"><i class="fas fa-stethoscope"></i> {{ $t ? 'Reevaluar' : 'Triaje' }}</button>
              @endif
              @if($can['START'])
                <form method="post" class="ef-patient-form" action="{{ route('hospital.emergency-flow.start', ['solicitud'=>$s->id]) }}" data-solicitud="{{ $s->id }}">{{ csrf_field() }}
                  <input type="hidden" name="id_solicitud" value="{{ $s->id }}"><input type="hidden" name="version" value="{{ $version }}"><input type="hidden" name="start_mode" value="STANDARD">
                  <button class="btn btn-sm btn-primary"><i class="fas fa-play"></i> Atender</button>
                </form>
              @endif
              @if($can['START_CRITICAL'])
                <button type="button" class="btn btn-sm btn-danger ef-open-critical-start" data-id="{{ $s->id }}" data-version="{{ $version }}" data-name="{{ $name }} · {{ $s->id_paciente }}" data-url="{{ route('hospital.emergency-flow.start', ['solicitud'=>$s->id]) }}"><i class="fas fa-bolt"></i> Iniciar atencion critica</button>
              @endif
              @if($can['MOVE'])
                <button type="button" class="btn btn-sm btn-outline-secondary ef-open-move" data-id="{{ $s->id }}" data-version="{{ $version }}" data-name="{{ $name }} · {{ $s->id_paciente }}" data-url="{{ route('hospital.emergency-flow.move', ['solicitud'=>$s->id]) }}" title="Cambiar ubicación dentro de Emergencia" aria-label="Cambiar ubicación dentro de Emergencia"><i class="fas fa-map-marker-alt"></i></button>
              @endif
              @if($can['REROUTE_HOSPITALIZATION'])
                <button type="button" class="btn btn-sm btn-success ef-open-destination" data-disposition="HOSPITALIZATION" data-reroute="1" data-id="{{ $s->id }}" data-version="{{ $version }}" data-name="{{ $name }} · {{ $s->id_paciente }}" data-url="{{ route('hospital.emergency-flow.reroute-hospitalization', ['solicitud'=>$s->id]) }}"><i class="fas fa-bed"></i> Enviar a Hospitalización</button>
              @endif
              @if($can['DECIDE'])
                <button type="button" class="btn btn-sm btn-success ef-open-destination" data-disposition="HOSPITALIZATION" data-id="{{ $s->id }}" data-version="{{ $version }}" data-name="{{ $name }} · {{ $s->id_paciente }}" data-url="{{ route('hospital.emergency-flow.disposition', ['solicitud'=>$s->id]) }}" data-care-url="{{ route('hospital.emergency-care.show', ['solicitud'=>$s->id]) }}"><i class="fas fa-bed"></i> Hospitalizar</button>
                <button type="button" class="btn btn-sm btn-outline-success ef-open-destination" data-id="{{ $s->id }}" data-version="{{ $version }}" data-name="{{ $name }} · {{ $s->id_paciente }}" data-url="{{ route('hospital.emergency-flow.disposition', ['solicitud'=>$s->id]) }}" data-care-url="{{ route('hospital.emergency-care.show', ['solicitud'=>$s->id]) }}"><i class="fas fa-route"></i> Otros destinos</button>
              @endif
              @if($canAlta)
                <a class="btn btn-sm btn-outline-success" href="{{ route('epicrisis.mostrar_final', ['id_solicitud'=>$s->id]) }}"><i class="fas fa-signature"></i> Alta</a>
              @endif
              @if($can['CANCEL_TRANSFER'] && $f && $f->requested_transfer_id && \Illuminate\Support\Facades\Route::has('hospital.emergency-flow.cancel-transfer'))
                <button type="button" class="btn btn-sm btn-outline-danger ef-open-cancel" data-id="{{ $s->id }}" data-version="{{ $version }}" data-name="{{ $name }} · {{ $s->id_paciente }}" data-url="{{ route('hospital.emergency-flow.cancel-transfer', ['solicitud'=>$s->id]) }}"><i class="fas fa-times"></i> Cancelar traslado</button>
              @endif
              @if($isSystemUser && !$readOnly && in_array((int) $s->estado_paso, [1, 2], true) && \Illuminate\Support\Facades\Route::has('hospital.emergency-flow.annul-episode'))
                <button type="button" class="btn btn-sm btn-outline-danger ef-open-annul" data-id="{{ $s->id }}" data-version="{{ $version }}" data-name="{{ $name }} · {{ $s->id_paciente }}" data-url="{{ route('hospital.emergency-flow.annul-episode', ['solicitud'=>$s->id]) }}"><i class="fas fa-ban"></i> Anular episodio</button>
              @endif
              @if($pendingExit)
                <a class="btn btn-sm btn-outline-success" href="{{ route('epicrisis.mostrar_final', ['id_solicitud'=>$s->id]) }}"><i class="fas fa-door-open"></i> Salida fisica</a>
              @endif
              @if(\Illuminate\Support\Facades\Route::has('hospital.documents.index'))
                <a class="btn btn-sm btn-outline-info" href="{{ route('hospital.documents.index', ['id_solicitud'=>$s->id]) }}"><i class="fas fa-folder-open"></i> Documentos</a>
              @endif
              @if(!$readOnly)
                <a class="btn btn-sm btn-outline-info" href="{{ route('hospital.ingreso008',array('id_solicitud'=>$s->id)) }}"><i class="far fa-file-alt" aria-hidden="true"></i> {{ $s->id_agenda ? 'Historia 008' : 'Vincular Historia 008' }}</a>
                @if($s->id_agenda)<a class="btn btn-sm btn-outline-info" href="{{ route('formulario005.index_005', ['id_solicitud'=>$s->id]) }}"><i class="fas fa-notes-medical" aria-hidden="true"></i> Evoluciones F005</a>@endif
              @endif
            </div></td>
          </tr>
        @empty
          <tr><td colspan="7"><div class="ef-empty"><i class="fas fa-check-circle"></i><strong>No existen pacientes activos en la cola.</strong><br>Los nuevos ingresos aparecerán aquí.</div></td></tr>
        @endforelse
          <tr id="efNoResults" hidden><td colspan="7"><div class="ef-empty"><i class="fas fa-search" aria-hidden="true"></i><strong>No hay pacientes con esos filtros.</strong><br><button type="button" id="efClearFilters" class="btn btn-outline-primary mt-3">Limpiar filtros</button></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

@if($isSystemUser || in_array($role, [3, 6], true))
<div class="modal fade" id="patientAdmissionModal" tabindex="-1" role="dialog" aria-labelledby="patientAdmissionTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header"><div><h5 class="modal-title" id="patientAdmissionTitle"><i class="fas fa-user-plus mr-2" aria-hidden="true"></i>Ingreso de paciente</h5><small>Identifique al paciente y registre el triaje inicial.</small></div><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
      <div class="modal-body"><div class="ea-page">@include('hospital.emergencia.admission_form')</div></div>
    </div>
  </div>
</div>
@include('hospital.emergencia.registro_paciente')
@endif

<div class="modal fade" id="progressModal" tabindex="-1" role="dialog" aria-labelledby="progressModalTitle" aria-hidden="true">
  <div class="modal-dialog" role="document"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title ef-modal-title" id="progressModalTitle">Completar datos <small class="ef-modal-patient"></small></h5><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button></div>
    <div class="modal-body"><div id="progressModalLoading" role="status"><i class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>Cargando información del episodio…</div><iframe id="progressModalFrame" title="Completar datos del paciente" src="about:blank"></iframe></div>
  </div></div>
</div>

<div class="modal fade" id="criticalModal" tabindex="-1"><div class="modal-dialog"><form method="post" action="{{ route('hospital.emergency-flow.critical-arrival') }}" class="modal-content">{{ csrf_field() }}
  <div class="modal-header bg-danger text-white"><h5 class="modal-title">Ingreso crítico sin identificación</h5><button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">&times;</button></div>
  <div class="modal-body"><div class="alert alert-danger"><strong>No requiere cédula ni nombres.</strong> Se creará una identidad provisional. Después del ingreso, registre el inicio de atención crítica y complete el triaje desde la cola.</div>
    <div class="form-group"><label for="criticalArrivalMode">Forma de llegada *</label><select id="criticalArrivalMode" name="arrival_mode" class="form-control" required><option value="">Seleccione…</option><option value="AMBULANCE">Ambulancia</option><option value="WALK_IN">Por sus propios medios</option><option value="TRANSFER">Transferido de otro establecimiento</option><option value="POLICE">Policía / autoridad</option></select></div>
    <div class="form-group"><label for="criticalSex">Sexo aparente (opcional)</label><select id="criticalSex" name="sex" class="form-control"><option value="">No identificado</option><option value="1">Masculino</option><option value="2">Femenino</option></select></div>
    <div class="form-group"><label for="criticalArrivalNote">Condición observada y acción inmediata *</label><textarea id="criticalArrivalNote" name="arrival_note" class="form-control" required minlength="5" maxlength="1000" rows="3" placeholder="Describa brevemente la condición y las acciones realizadas"></textarea></div>
  </div><div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button><button class="btn btn-danger"><i class="fas fa-bolt mr-1"></i>Registrar ingreso critico</button></div>
</form></div></div>

@if($isSystemUser || $role === 3)
<div class="modal fade" id="criticalStartModal" tabindex="-1"><div class="modal-dialog"><form method="post" id="criticalStartForm" class="modal-content ef-patient-form">{{ csrf_field() }}
  <input type="hidden" name="id_solicitud" value=""><input type="hidden" name="version" value=""><input type="hidden" name="start_mode" value="CRITICAL">
  <div class="modal-header bg-danger text-white"><h5 class="modal-title ef-modal-title">Inicio de atencion critica <small class="ef-modal-patient"></small></h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
  <div class="modal-body"><div class="form-group"><label for="criticalReason">Justificacion medica *</label><textarea id="criticalReason" name="critical_reason" class="form-control" required minlength="10" maxlength="1000" rows="3"></textarea></div></div>
  <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button><button class="btn btn-danger"><i class="fas fa-bolt mr-1"></i>Confirmar inicio critico</button></div>
</form></div></div>
@endif

<div class="modal fade" id="triageModal" tabindex="-1"><div class="modal-dialog modal-lg"><form method="post" id="triageForm" class="modal-content ef-patient-form">{{ csrf_field() }}
  <input type="hidden" name="id_solicitud" value=""><input type="hidden" name="version" value="">
  <div class="modal-header"><h5 class="modal-title ef-modal-title">Triaje y reevaluación <small class="ef-modal-patient"></small></h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
  <div class="modal-body">
    <div class="ef-help"><i class="fas fa-info-circle mr-1"></i>Los datos se guardan al terminar de escribir. La prioridad solo cambia al confirmar «Guardar evaluación»; las evaluaciones anteriores permanecen intactas.</div>
    <p id="ef-autosave-status" class="ec-gcs-detail" role="status" aria-live="polite">Los cambios se guardan al terminar de escribir.</p>
    <div class="form-row">
      <div class="form-group col-md-6"><label>Tipo de emergencia</label><select name="emergency_type_id" class="form-control"><option value="">Seleccione…</option>@foreach($emergencyTypes as $type)<option value="{{ $type->id }}">{{ $type->nombre }}</option>@endforeach</select></div>
      <div class="form-group col-md-6"><label>Motivo y hallazgo clínico *</label><textarea name="clinical_reason" class="form-control" rows="2" required minlength="5"></textarea></div>
    </div>
    @include('hospital.emergencia.classification', ['classificationPrefix'=>'ef', 'classificationLegacy'=>false, 'classificationPriorities'=>$priorities])
    <label>Signos vitales</label><div class="form-row">
      <div class="form-group col-6 col-md-2"><input name="systolic" type="number" step=".01" class="form-control" placeholder="PAS"></div>
      <div class="form-group col-6 col-md-2"><input name="diastolic" type="number" step=".01" class="form-control" placeholder="PAD"></div>
      <div class="form-group col-6 col-md-2"><input name="heart_rate" type="number" step=".01" class="form-control" placeholder="FC"></div>
      <div class="form-group col-6 col-md-2"><input name="respiratory_rate" type="number" step=".01" class="form-control" placeholder="FR"></div>
      <div class="form-group col-6 col-md-2"><input name="temperature" type="number" step=".01" class="form-control" placeholder="Temp. °C"></div>
      <div class="form-group col-6 col-md-2"><input name="oxygen_saturation" type="number" step=".01" class="form-control" placeholder="SpO₂ %"></div>
      <div class="form-group col-6 col-md-2"><input name="pain_score" type="number" min="0" max="10" class="form-control" placeholder="Dolor 0–10"></div>
      <div class="form-group col-6 col-md-3 pt-2"><label><input type="checkbox" name="high_risk" value="1"> Signo de alto riesgo</label></div>
      <div class="form-group col-6 col-md-3 pt-2"><label><input type="checkbox" name="isolation" value="1"> Requiere aislamiento</label></div>
    </div>
  </div><div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button><button class="btn btn-warning"><i class="fas fa-save mr-1"></i>Guardar evaluación</button></div>
</form></div></div>

<div class="modal fade" id="moveModal" tabindex="-1"><div class="modal-dialog"><form method="post" id="moveForm" class="modal-content ef-patient-form">{{ csrf_field() }}
  <input type="hidden" name="id_solicitud" value=""><input type="hidden" name="version" value="">
  <div class="modal-header"><h5 class="modal-title ef-modal-title">Cambiar ubicación en Emergencia <small class="ef-modal-patient"></small></h5><button type="button" id="moveModalClose" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
  <div class="modal-body">
    <div class="ef-help"><i class="fas fa-info-circle mr-1"></i>Este selector solo mueve al paciente entre áreas internas de Emergencia. Para cambiarlo de módulo use «Enviar a Hospitalización» en su fila.</div>
    <div class="form-group"><label for="moveLocation">Nueva ubicación *</label><select id="moveLocation" name="location_id" class="form-control" required><option value="">Seleccione…</option>@foreach($locations as $location)<option value="{{ $location->id }}">{{ $location->name }}</option>@endforeach</select></div>
    <div class="form-group"><label for="moveReason">Motivo del movimiento *</label><textarea id="moveReason" name="reason" class="form-control" rows="4" required minlength="4" maxlength="191" placeholder="Ej.: valoración médica, monitorización, aislamiento"></textarea></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button><button class="btn btn-primary">Confirmar ubicación</button></div>
</form></div></div>

<div class="modal fade" id="destinationModal" tabindex="-1"><div class="modal-dialog"><form method="post" id="destinationForm" class="modal-content ef-patient-form">{{ csrf_field() }}
  <input type="hidden" name="id_solicitud" value=""><input type="hidden" name="version" value="">
  <div class="modal-header"><h5 class="modal-title ef-modal-title"><span id="destinationModalHeading">Decisión de destino</span> <small class="ef-modal-patient"></small></h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
  <div class="modal-body">
    <div id="destinationTransferHelp" class="ef-help" role="status"></div>
    <div class="form-group" id="destinationDispositionGroup"><label for="destinationDisposition">Destino del paciente *</label>
      <select id="destinationDisposition" name="disposition" class="form-control" required>
        <option value="">Seleccione destino</option>
        @foreach($dispositions as $code=>$label)<option value="{{ $code }}">{{ $label }}</option>@endforeach
      </select>
    </div>
    <div id="destinationSalaGroup" class="form-group" style="display:none"><label for="destinationSala">Sala / servicio receptor *</label>
      <select id="destinationSala" name="target_sala_id" class="form-control"><option value="">Seleccione sala</option>
        @foreach($salas as $sala)<option value="{{ $sala->id }}" data-step="{{ $sala->hospital_step }}">{{ $sala->nombre_sala }}</option>@endforeach
      </select>
      <small id="destinationSalaHelp" class="text-muted"></small>
    </div>
    <div id="destinationBedGroup" class="form-group" style="display:none"><label for="destinationBed">Habitación y cama</label>
      <select id="destinationBed" name="target_bed_id" class="form-control" disabled><option value="">Asignar cama después</option></select>
      <small id="destinationBedHelp" class="text-muted"></small>
    </div>
    <div class="form-group"><label id="destinationNoteLabel" for="destinationNote">Motivo del destino *</label>
      <textarea id="destinationNote" name="decision_note" class="form-control" rows="2" required minlength="10" maxlength="2000" placeholder="Indique el motivo clínico"></textarea>
    </div>
    <a id="destinationCareLink" href="#" target="_blank" rel="noopener">Ver evolución del paciente</a>
  </div>
  <div id="destinationConfigurationHelp" class="px-3 pb-3" style="display:none">
    @if($isSystemUser)
      <a href="{{ route('hospital.workflow.locations') }}" target="_blank" rel="noopener">Configurar salas y vincular habitaciones existentes</a>
    @else
      <small>Solicite a Sistemas vincular las salas y habitaciones existentes al servicio receptor.</small>
    @endif
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button><button id="destinationSubmit" class="btn btn-success"><i class="fas fa-check mr-1"></i><span>Registrar decisión</span></button></div>
</form></div></div>

@if($isSystemUser || $role === 3)
<div class="modal fade" id="cancelTransferModal" tabindex="-1"><div class="modal-dialog"><form method="post" id="cancelTransferForm" class="modal-content ef-patient-form">{{ csrf_field() }}
  <input type="hidden" name="id_solicitud" value=""><input type="hidden" name="version" value="">
  <div class="modal-header"><h5 class="modal-title ef-modal-title">Cancelar traslado pendiente <small class="ef-modal-patient"></small></h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
  <div class="modal-body"><div class="form-group"><label for="cancelReason">Motivo medico *</label><textarea id="cancelReason" name="cancel_reason" class="form-control" required minlength="10" maxlength="1000" rows="3"></textarea></div></div>
  <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Volver</button><button class="btn btn-danger"><i class="fas fa-times mr-1"></i>Confirmar cancelacion</button></div>
</form></div></div>
@endif

@if($isSystemUser)
<div class="modal fade" id="annulEpisodeModal" tabindex="-1"><div class="modal-dialog"><form method="post" id="annulEpisodeForm" class="modal-content ef-patient-form">{{ csrf_field() }}
  <input type="hidden" name="id_solicitud" value=""><input type="hidden" name="version" value="">
  <div class="modal-header bg-danger text-white"><h5 class="modal-title">Anular episodio de prueba <small class="ef-modal-patient"></small></h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
  <div class="modal-body"><div class="alert alert-warning">Se retirará de la cola activa y se conservará todo el historial clínico.</div><div class="form-group"><label for="annulReason">Motivo de anulación *</label><textarea id="annulReason" name="annul_reason" class="form-control" required minlength="10" maxlength="1000" rows="3">Episodio de prueba bloqueado en traslado a Cirugía.</textarea></div></div>
  <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Volver</button><button class="btn btn-danger"><i class="fas fa-ban mr-1"></i>Confirmar anulación</button></div>
</form></div></div>
@endif

<script>
document.addEventListener('DOMContentLoaded',function(){
  var submitting = false;
  $('#registro-paciente-emergencia').on('hidden.bs.modal', function(){
    if($('#patientAdmissionModal').hasClass('show')) document.body.classList.add('modal-open');
  });
  var triageForm = document.getElementById('triageForm');
  var triageAutosave = window.EmergencyAutosave.mount(triageForm, document.getElementById('ef-autosave-status'), {
    onSaved:function(data){
      delete document.getElementById('triageModal').dataset.autosaveDiscardReady;
      var row = document.querySelector('.ef-row[data-solicitud="'+triageForm.dataset.solicitud+'"]');
      if(!row) return;
      row.dataset.version = data.version;
      row.querySelectorAll('[data-version]').forEach(function(element){ element.dataset.version = data.version; });
      row.querySelectorAll('input[name="version"]').forEach(function(element){ element.value = data.version; });
    }
  });
  $(triageForm).on('input.autosaveDiscard change.autosaveDiscard', function(){
    delete document.getElementById('triageModal').dataset.autosaveDiscardReady;
  });
  var destinationForm = document.getElementById('destinationForm');
  var destinationCareLink = document.getElementById('destinationCareLink');
  var destinationDisposition = document.getElementById('destinationDisposition');
  var destinationPicker = window.EmergencyDestination.mount(destinationForm,
    @json(\Sis_medico\Services\ClinicalEmergencyFlowPolicy::transferSteps()), @json($transferBeds), @json($transferBedStatus));

  function resetPatientForm(form){
    form.reset();
    if(form === destinationForm){
      destinationDisposition.disabled = false;
      document.getElementById('destinationDispositionGroup').style.display = '';
      document.getElementById('destinationModalHeading').textContent = 'Decisión de destino';
      document.getElementById('destinationNoteLabel').textContent = 'Motivo del destino *';
      document.getElementById('destinationNote').placeholder = 'Indique el motivo clínico';
      destinationCareLink.style.display = '';
    }
    if(form === triageForm){
      triageAutosave.reset();
      delete form.dataset.autosaveUrl;
      delete document.getElementById('triageModal').dataset.autosaveDiscardReady;
      form.dispatchEvent(new Event('emergency:classification-reset'));
    }
    form.removeAttribute('action');
    delete form.dataset.solicitud;
    form.elements.id_solicitud.value = '';
    form.elements.version.value = '';
    form.querySelectorAll('button:not([type="button"])').forEach(function(button){ button.disabled = true; });
  }
  function open(selector, formId, button){
    if(submitting) return;
    var form = document.getElementById(formId);
    if(!form) return;
    resetPatientForm(form);
    var id = button.getAttribute('data-id');
    var version = button.getAttribute('data-version');
    var action = button.getAttribute('data-url');
    if(!id || !/^\d+$/.test(version || '') || !action) return;
    form.dataset.solicitud = id;
    form.action = action;
    form.elements.id_solicitud.value = id;
    form.elements.version.value = version;
    document.querySelector(selector+' .ef-modal-patient').textContent = button.getAttribute('data-name')+' · Episodio #'+id;
    if(form === destinationForm){
      destinationCareLink.href = button.getAttribute('data-care-url') || '#';
      destinationForm.elements.disposition.value = button.getAttribute('data-disposition') || '';
      destinationPicker.reset();
      if(button.getAttribute('data-reroute') === '1'){
        destinationDisposition.disabled = true;
        document.getElementById('destinationDispositionGroup').style.display = 'none';
        document.getElementById('destinationModalHeading').textContent = 'Enviar a Hospitalización';
        document.getElementById('destinationNoteLabel').textContent = 'Motivo de la corrección *';
        document.getElementById('destinationNote').placeholder = 'Explique por qué este ingreso debe continuar en Hospitalización';
        document.getElementById('destinationTransferHelp').textContent = 'Seleccione una sala y, si está disponible, una cama de la empresa activa. El paciente aparecerá en Hospitalización al confirmar la recepción.';
        destinationCareLink.style.display = 'none';
      }
    }
    form.querySelectorAll('button:not([type="button"])').forEach(function(submit){ submit.disabled = false; });
    $(selector).modal('show');
    if(form === triageForm){
      form.dataset.autosaveUrl = button.getAttribute('data-autosave-url') || '';
      var draftUrl = button.getAttribute('data-draft-url');
      var fields = Array.from(form.querySelectorAll('input,select,textarea')).filter(function(field){ return field.type !== 'hidden'; });
      fields.forEach(function(field){ field.disabled = true; });
      form.querySelectorAll('button:not([type="button"])').forEach(function(submit){ submit.disabled = true; });
      triageAutosave.show('Cargando los avances de este episodio…');
      fetch(draftUrl, {credentials:'same-origin', headers:{Accept:'application/json'}})
        .then(function(response){ if(!response.ok) throw new Error('No se pudieron cargar los avances.'); return response.json(); })
        .then(function(data){
          if(form.dataset.solicitud !== id) return;
          form.elements.version.value = data.version;
          triageAutosave.hydrate(data.draft || {});
          fields.forEach(function(field){ field.disabled = false; });
          form.querySelectorAll('button:not([type="button"])').forEach(function(submit){ submit.disabled = false; });
        }).catch(function(error){ if(form.dataset.solicitud === id) triageAutosave.show(error.message + ' Cierre y vuelva a abrir el triaje.', true); });
    }
  }
  var progressModal = document.getElementById('progressModal');
  var progressFrame = document.getElementById('progressModalFrame');
  var progressLoading = document.getElementById('progressModalLoading');
  var progressChanged = false;
  progressFrame.addEventListener('load', function(){
    if(progressFrame.getAttribute('src') !== 'about:blank') progressLoading.style.display = 'none';
  });
  document.querySelectorAll('.ef-open-progress').forEach(function(button){
    button.addEventListener('click', function(){
      var url = button.getAttribute('data-url');
      if(!url) return;
      progressChanged = false;
      progressLoading.style.display = 'flex';
      progressModal.querySelector('.ef-modal-patient').textContent = button.getAttribute('data-name') || '';
      progressFrame.setAttribute('src', url);
      $('#progressModal').modal('show');
    });
  });
  window.addEventListener('message', function(event){
    if(event.origin !== window.location.origin || !event.data || event.data.type !== 'emergency-progress-saved') return;
    progressChanged = true;
  });
  $('#progressModal').on('hidden.bs.modal', function(){
    progressFrame.setAttribute('src', 'about:blank');
    if(progressChanged) window.location.reload();
  });
  [
    ['.ef-open-triage', '#triageModal', 'triageForm'],
    ['.ef-open-move', '#moveModal', 'moveForm'],
    ['.ef-open-destination', '#destinationModal', 'destinationForm'],
    ['.ef-open-critical-start', '#criticalStartModal', 'criticalStartForm'],
    ['.ef-open-cancel', '#cancelTransferModal', 'cancelTransferForm'],
    ['.ef-open-annul', '#annulEpisodeModal', 'annulEpisodeForm']
  ].forEach(function(config){
    document.querySelectorAll(config[0]).forEach(function(button){
      button.addEventListener('click',function(){ open(config[1], config[2], button); });
    });
    $(config[1]).on('hidden.bs.modal',function(){
      var form = document.getElementById(config[2]);
      if(form && !submitting) resetPatientForm(form);
    });
  });
  $('#criticalModal').on('show.bs.modal',function(event){
    if(submitting){ event.preventDefault(); return; }
    this.querySelector('form').reset();
  });
  document.querySelectorAll('.ef-patient-form').forEach(function(form){
    form.addEventListener('submit',function(event){
      if(submitting || !form.getAttribute('action') || !form.dataset.solicitud
          || form.elements.id_solicitud.value !== form.dataset.solicitud
          || !/^\d+$/.test(form.elements.version.value)){
        event.preventDefault();
        return;
      }
      submitting = true;
      form.querySelectorAll('button:not([type="button"])').forEach(function(button){ button.disabled = true; });
    });
  });
  document.querySelector('#criticalModal form').addEventListener('submit',function(event){
    if(submitting){ event.preventDefault(); return; }
    submitting = true;
    this.querySelector('button:not([type="button"])').disabled = true;
  });
  function filterEmergencyPatients(){
    function normalize(value) { return value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, ''); }
    var query = normalize(document.getElementById('efSearch').value.trim());
    var priority = document.getElementById('efPriorityFilter').value;
    var status = document.getElementById('efStatusFilter').value;
    var visible = 0;
    document.querySelectorAll('.ef-row').forEach(function(row){
      var matches = (!query || normalize(row.getAttribute('data-search')).indexOf(query) !== -1)
        && (!priority || (priority === '9' ? row.dataset.triaged === '0' : row.dataset.priority === priority))
        && (!status || row.dataset.status === status);
      row.hidden = !matches;
      if (matches) visible++;
    });
    document.getElementById('efResultCount').textContent = visible + (visible === 1 ? ' paciente' : ' pacientes');
    document.getElementById('efNoResults').hidden = visible !== 0 || document.querySelectorAll('.ef-row').length === 0;
  }
  window.filterEmergencyPatients = filterEmergencyPatients;
  document.getElementById('efSearch').addEventListener('input',filterEmergencyPatients);
  $('#efPriorityFilter, #efStatusFilter').on('select2:select select2:clear', filterEmergencyPatients);
  document.getElementById('efClearFilters').addEventListener('click', function () {
    ['efSearch', 'efPriorityFilter', 'efStatusFilter'].forEach(function (id) { document.getElementById(id).value = ''; });
    $('#efPriorityFilter, #efStatusFilter').trigger('change.select2');
    filterEmergencyPatients(); document.getElementById('efSearch').focus();
  });
  $('#triageModal').on('hide.bs.modal',function(event){
    if(this.dataset.autosaveClosing === '1' || this.dataset.autosaveDiscardReady === '1') return;
    if(!triageAutosave.hasPending() && !triageAutosave.hasUnsaved()) return;
    event.preventDefault();
    var modal = this;
    triageAutosave.flush().then(function(){
      modal.dataset.autosaveClosing = '1';
      $(modal).modal('hide');
      delete modal.dataset.autosaveClosing;
    }).catch(function(){
      modal.dataset.autosaveDiscardReady = '1';
      triageAutosave.show('Hay cambios sin guardar. Corríjalos o vuelva a cerrar para descartarlos.', true);
    });
  });

  var requestedSolicitud = @json((string) request('solicitud', ''));
  var requestedAction = @json((string) request('accion', ''));
  var focused = Array.from(document.querySelectorAll('.ef-row')).find(function(row){
    return row.getAttribute('data-solicitud') === requestedSolicitud;
  });
  if(focused){
    focused.classList.add('ef-focused');
    focused.scrollIntoView({behavior:'smooth',block:'center'});
    if(requestedAction === 'destino'){
      var destinationButton = focused.querySelector('.ef-open-destination');
      if(destinationButton) destinationButton.click();
    }
  }
});
</script>
<link rel="stylesheet" href="{{ asset('css/emergency-admission.css') }}?v=4">
<link rel="stylesheet" href="{{ asset('css/emergency-classification.css') }}?v=4">
@if($isSystemUser || in_array($role, [3, 6], true))
<script defer src="{{ asset('js/jquery-ui.js') }}"></script>
<script defer src="{{ asset('js/emergency-patient-registration.js') }}?v=8"></script>
@endif
<script defer src="{{ asset('js/emergency-classification.js') }}?v=6"></script>
@if($isSystemUser || in_array($role, [3, 6], true))
<script defer src="{{ asset('js/emergency-admission.js') }}?v=8"></script>
@endif
<script defer src="{{ asset('js/emergency-autosave.js') }}?v=1"></script>
<script defer src="{{ asset('js/emergency-destination.js') }}?v=1"></script>
@endsection
