@extends('layouts.app-template-h')
@section('content')
<style>
  .ecare { --ink:#153650; --muted:#60798d; --line:#d5e5ee; max-width:1500px; margin:auto; padding:24px; color:var(--ink); }
  .ecare a { text-decoration:none; }
  .ecare-back { display:inline-block; margin-bottom:14px; font-weight:700; }
  .ecare-hero { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; padding:24px 28px; border-radius:18px; color:#fff; background:linear-gradient(115deg,#103b5b,#087c93); }
  .ecare-hero h1 { color:#fff; font-size:27px; font-weight:800; margin:4px 0 6px; }
  .ecare-hero p { margin:0; opacity:.9; }
  .ecare-eyebrow { font-size:11px; font-weight:800; letter-spacing:1.4px; text-transform:uppercase; }
  .ecare-badge { padding:9px 13px; border:1px solid #ffffff77; border-radius:10px; font-weight:700; }
  .ecare-journey { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:10px; margin:16px 0; }
  .ecare-step { background:#fff; border:1px solid var(--line); border-radius:12px; padding:13px 15px; min-height:75px; }
  .ecare-step strong { display:block; font-size:13px; }
  .ecare-step small { color:var(--muted); }
  .ecare-step.is-current { border-color:#087c93; box-shadow:inset 4px 0 #087c93; }
  .ecare-grid { display:grid; grid-template-columns:minmax(0,1fr) 285px; gap:16px; align-items:start; }
  .ecare-card { background:#fff; border:1px solid var(--line); border-radius:14px; padding:20px; box-shadow:0 5px 18px #173f5b0b; }
  .ecare-card + .ecare-card { margin-top:14px; }
  .ecare-card h2 { font-size:19px; font-weight:800; margin:0 0 5px; }
  .ecare-card h3 { font-size:16px; font-weight:800; margin:0 0 8px; }
  .ecare-muted, .ecare-hint { color:var(--muted); font-size:13px; }
  .ecare-alert { border:1px solid #b9dce6; background:#eff9fb; color:#16475b; border-radius:10px; padding:12px 14px; margin-bottom:14px; }
  .ecare-alert strong { display:block; }
  .ecare-section { border-top:1px solid var(--line); padding-top:18px; margin-top:18px; }
  .ecare-section:first-of-type { border-top:0; padding-top:0; margin-top:0; }
  .ecare-section-head { display:flex; justify-content:space-between; gap:12px; align-items:baseline; }
  .ecare-section h3 { margin:0; }
  .ecare-section textarea { border:1px solid #bcd4e1; border-radius:9px; min-height:95px; width:100%; padding:11px 12px; resize:vertical; margin-top:9px; color:var(--ink); }
  .ecare-section textarea:focus { outline:2px solid #087c93; outline-offset:1px; }
  .ecare-section footer { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:7px; }
  .ecare-save-state { font-size:12px; color:var(--muted); }
  .ecare-save-state.is-error { color:#b42318; font-weight:700; }
  .ecare-side-link { display:block; border:1px solid var(--line); border-radius:9px; padding:10px 12px; margin-top:9px; font-weight:700; }
  .ecare-side-link.is-primary { color:#fff; border-color:#087c93; background:#087c93; }
  .ecare-side-link:hover { text-decoration:none; }
  .ecare-list { padding-left:20px; color:var(--muted); font-size:13px; line-height:1.55; }
  .ecare-save { font-size:12px; font-weight:700; }
  @media(max-width:950px){ .ecare-grid{grid-template-columns:1fr}.ecare-journey{grid-template-columns:repeat(2,minmax(0,1fr))} }
  @media(max-width:600px){ .ecare{padding:12px}.ecare-journey{grid-template-columns:1fr}.ecare-hero{padding:20px}.ecare-section-head,.ecare-section footer{display:block}.ecare-save{margin-top:8px} }
</style>
<main class="ecare" @if($canEdit) data-emergency-care data-save-url="{{ route('hospital.emergency-care.save', ['solicitud'=>$source->id]) }}" data-csrf-token="{{ csrf_token() }}" @endif>
  @php($careStatus = ['IN_ATTENTION'=>'En atención','UNDER_OBSERVATION'=>'En observación','TRANSFER_PENDING'=>'Traslado pendiente','DECISION_RECORDED'=>'Decisión registrada'][$flow->status] ?? $flow->status)
  <a class="ecare-back" href="{{ route('hospital.emergencia', ['solicitud'=>$source->id]) }}">← Volver a Emergencia</a>
  <header class="ecare-hero">
    <div><span class="ecare-eyebrow">Atención de emergencia · Episodio #{{ $source->id }}</span>
      <h1>{{ trim($source->paciente->apellido1.' '.$source->paciente->apellido2.' '.$source->paciente->nombre1.' '.$source->paciente->nombre2) }}</h1>
      <p>{{ $source->id_paciente }} · {{ $careStatus }} · Inició {{ $flow->attention_started_at }}</p>
    </div>
    <span class="ecare-badge">{{ $triage ? $triage->priority_name : 'Triaje pendiente' }}</span>
  </header>

  <nav class="ecare-journey" aria-label="Pasos de la atención">
    <div class="ecare-step"><strong>1. Ingreso y triaje</strong><small>{{ $triage ? 'Valoración registrada' : 'Pendiente de completar' }}</small></div>
    <div class="ecare-step is-current"><strong>2. Valorar y documentar</strong><small>Registre hallazgos a medida que se conozcan</small></div>
    <div class="ecare-step"><strong>3. Historia 008 y órdenes</strong><small>Complete los formularios que correspondan</small></div>
    <div class="ecare-step"><strong>4. Destino y salida</strong><small>Decisión médica, alta o traslado</small></div>
  </nav>

  @if(session('success'))<div class="ecare-alert" role="status">{{ session('success') }}</div>@endif
  <div class="ecare-grid">
    <div>
      <section class="ecare-card">
        <h2>Valoración y evolución</h2>
        <p class="ecare-muted">Los cambios se guardan automáticamente mientras escribe, tras una pausa breve o al pasar a otra sección. Cada guardado conserva fecha y profesional.</p>
        @if(!$canEdit)<div class="ecare-alert" role="status">El episodio ya no admite nuevas notas de atención desde esta pantalla.</div>@endif

        @foreach($sections as $key=>$definition)
          @php($entry = $latest->get($key))
          <section class="ecare-section">
            <div class="ecare-section-head"><h3>{{ $definition['title'] }}</h3><span class="ecare-save-state" id="care-status-{{ $key }}" role="status" aria-live="polite">{{ $entry ? 'Guardado el '.$entry->created_at->format('d/m/Y H:i').' por '.$entry->recorded_by : 'Sin registro' }}</span></div>
            <label class="ecare-hint" for="care-{{ $key }}">{{ $definition['hint'] }}</label>
            <textarea id="care-{{ $key }}" data-care-section="{{ $key }}" data-base-id="{{ $entry ? $entry->id : 0 }}" maxlength="4000" @if(!$canEdit) readonly @endif>{{ $entry ? $entry->content : '' }}</textarea>
            @if($canEdit)<footer><span class="ecare-hint">Borrador de atención; no sustituye la firma del Formulario 008.</span><button type="button" class="btn btn-sm btn-outline-primary ecare-save" data-save-section="{{ $key }}">Guardar ahora</button></footer>@endif
          </section>
        @endforeach
      </section>
    </div>
    <aside>
      <section class="ecare-card">
        <h3>Siguientes acciones</h3>
        <ol class="ecare-list">
          <li>Revisar motivo, signos vitales y triaje; reevaluar si cambia la condición.</li>
          <li>Documentar enfermedad actual, antecedentes, examen y evolución; lesiones u obstetricia solo si aplica.</li>
          <li>Completar diagnósticos, prescripciones, órdenes y formularios del episodio.</li>
          <li>Registrar la decisión de destino y completar la salida o recepción del traslado.</li>
        </ol>
        <a class="ecare-side-link" href="{{ route('hospital.emergency-flow.progress', ['solicitud'=>$source->id]) }}">Completar datos y triaje</a>
        @if($source->id_agenda)
          <a class="ecare-side-link is-primary" href="{{ route('hospital.detallep', ['id'=>$source->id]) }}">Abrir Historia 008</a>
          <a class="ecare-side-link" href="{{ route('formulario005.index_005', ['id_solicitud'=>$source->id]) }}">Evoluciones formales · Formulario 005</a>
        @else
          <a class="ecare-side-link is-primary" href="{{ route('hospital.ingreso008', ['id_solicitud'=>$source->id]) }}">Vincular Historia 008</a>
          <p class="ecare-hint mt-2">La Historia 008 completa requiere registrar la información administrativa pendiente. Esta atención ya puede documentarse aquí.</p>
        @endif
        <a class="ecare-side-link" href="{{ route('hospital.documents.index', ['id_solicitud'=>$source->id]) }}">Ver documentos</a>
        <a class="ecare-side-link" href="{{ route('hospital.emergencia', ['solicitud'=>$source->id, 'accion'=>'destino']) }}">Definir destino y salida</a>
      </section>
      <section class="ecare-card"><h3>Continuidad del equipo</h3><p class="ecare-muted">Cada guardado conserva el texto anterior, fecha y profesional. Si otra persona modificó una sección, la pantalla pedirá recargarla antes de sobrescribir.</p></section>
    </aside>
  </div>
</main>
@if($canEdit)
<script src="{{ asset('js/emergency-care-autosave.js') }}?v=1"></script>
@endif
@endsection
