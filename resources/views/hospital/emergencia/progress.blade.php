@extends(!empty($embedded) ? 'layouts.hospital-embedded-module' : 'layouts.app-template-h')
@section('content')
@php
    $progressLabels = \Sis_medico\Services\ClinicalEmergencyProgress::labels();
    $pendingIdentity = (bool) $source->paciente->emergency_identity_pending;
    $patientValue = function ($field) use ($source, $pendingIdentity) {
        $value = $source->paciente->$field;
        if ($pendingIdentity && (($field === 'nombre1' && mb_strtoupper(trim((string)$value), 'UTF-8') === 'NO')
            || ($field === 'apellido1' && mb_strtoupper(trim((string)$value), 'UTF-8') === 'IDENTIFICADO'))) {
            return 'N/A';
        }
        return $value;
    };
@endphp
<link rel="stylesheet" href="{{ asset('css/emergency-admission.css') }}?v=2">
<link rel="stylesheet" href="{{ asset('css/emergency-classification.css') }}?v=4">
<main class="ea-page {{ !empty($embedded) ? 'ea-page-embedded' : '' }}">
    @if(empty($embedded))<a class="ea-back" href="{{ route('hospital.emergencia') }}">← Volver a Emergencia</a>@endif
    <header class="ea-heading"><div><span class="ea-eyebrow">EMERGENCIA / EPISODIO #{{ $source->id }}</span><h1>Completar datos durante la atención</h1><p>{{ $patientValue('apellido1') }} {{ $patientValue('nombre1') }} · Código {{ $source->id_paciente }}</p></div></header>
    @if(session('success'))<div class="ea-notice" role="status">{{ session('success') }}</div>@endif
    <div class="ea-notice"><strong>Guarde solo lo que ya conoce.</strong> Cada dato se guarda automáticamente al terminar de escribir o cambiar una selección. «Guardar y confirmar triaje» sigue siendo una acción explícita. Otro profesional autorizado puede continuar este mismo episodio.</div>
    <div class="ea-workspace mt-3">
        <form id="emergency-progress-form" method="post" action="{{ route('hospital.emergency-flow.progress.save', ['solicitud'=>$source->id]) }}" data-autosave-url="{{ route('hospital.emergency-flow.progress.autosave', ['solicitud'=>$source->id]) }}" novalidate>
            {{ csrf_field() }}<input type="hidden" name="version" value="{{ $flow->version }}">@if(!empty($embedded))<input type="hidden" name="embedded" value="1">@endif
            <div id="ep-error" class="ea-error" role="alert" tabindex="-1" hidden></div>
            <p id="ep-autosave-status" class="ec-gcs-detail" role="status" aria-live="polite">Los cambios se guardan al terminar de escribir.</p>
            <section class="ea-card">
                <div class="ea-section-heading"><div><h2>Identificación y contacto</h2><p>Complete los datos cuando el paciente o un acompañante puedan proporcionarlos.</p></div></div>
                @if($source->paciente->emergency_identity_pending)<div class="ea-notice">Identidad pendiente de verificar. El código del episodio se conserva. La cédula que se comunique queda registrada para su conciliación por Admisión, sin crear otra historia.</div>@endif
                <div class="ea-grid">
                    @foreach(['nombre1'=>'Primer nombre','apellido1'=>'Primer apellido','nombre2'=>'Segundo nombre','apellido2'=>'Segundo apellido','telefono1'=>'Teléfono','telefono2'=>'Teléfono alternativo','direccion'=>'Dirección','mail_opcional'=>'Correo'] as $field=>$label)
                    <div><label for="ep-{{ $field }}">{{ $label }}</label><input id="ep-{{ $field }}" class="form-control" name="patient[{{ $field }}]" type="{{ $field === 'mail_opcional' ? 'email' : 'text' }}" maxlength="{{ $field === 'direccion' ? 255 : ($field === 'mail_opcional' ? 191 : 60) }}" value="{{ $patientValue($field) }}"></div>
                    @endforeach
                    <div><label for="ep-sex">Sexo</label><select id="ep-sex" class="form-control" name="patient[sexo]"><option value="0" @if($source->paciente->sexo === null || $source->paciente->sexo === '') selected @endif>No identificado</option><option value="1" @if($source->paciente->sexo == 1) selected @endif>Hombre</option><option value="2" @if($source->paciente->sexo == 2) selected @endif>Mujer</option></select></div>
                    <div><label for="ep-birth">Fecha de nacimiento</label><input id="ep-birth" class="form-control" name="patient[fecha_nacimiento]" type="date" max="{{ date('Y-m-d') }}" value="{{ $source->paciente->fecha_nacimiento }}"></div>
                    <div><label for="ep-document">Documento comunicado, pendiente de verificar</label><input id="ep-document" class="form-control" name="reported_document" maxlength="30" value="{{ $draft['reported_document'] ?? '' }}"></div>
                    <div><label for="ep-source">Quién proporciona la información</label><select id="ep-source" class="form-control" name="information_source"><option value="">Sin registrar</option>@foreach(['PATIENT'=>'Paciente','FAMILY'=>'Familiar / acompañante','STAFF'=>'Equipo asistencial','UNAVAILABLE'=>'Paciente aún no puede responder'] as $code=>$label)<option value="{{ $code }}" @if(($draft['information_source'] ?? '') === $code) selected @endif>{{ $label }}</option>@endforeach</select></div>
                </div>
            </section>
            <section class="ea-card mt-3">
                <div class="ea-section-heading"><div><h2>Valoración en curso</h2><p>Los campos con * se requieren al confirmar el triaje. Puede guardar avances sin completarlos. Revise que las mediciones guardadas sigan vigentes antes de confirmar.</p></div></div>
                <label for="ep-reason">Motivo de consulta *</label><textarea id="ep-reason" class="form-control" name="clinical_reason" rows="2" minlength="5" maxlength="1000" required>{{ $draft['clinical_reason'] ?? '' }}</textarea>
                <details class="ea-details"><summary>Signos vitales disponibles</summary><div class="ea-vitals">
                    @foreach([['systolic','P. sistólica (mmHg)',20,300],['diastolic','P. diastólica (mmHg)',10,200],['heart_rate','Frec. cardíaca (lpm)',1,300],['respiratory_rate','Frec. respiratoria (rpm)',1,100],['temperature','Temperatura (°C)',25,45],['oxygen_saturation','Saturación O₂ (%)',0,100],['pain_score','Dolor referido (0–10)',0,10]] as $vital)
                    <div><label for="ep-{{ $vital[0] }}">{{ $vital[1] }}</label><input id="ep-{{ $vital[0] }}" name="{{ $vital[0] }}" class="form-control" type="number" min="{{ $vital[2] }}" max="{{ $vital[3] }}" step="{{ $vital[0] === 'pain_score' ? '1' : '.01' }}" value="{{ $draft[$vital[0]] ?? '' }}"></div>
                    @endforeach
                </div></details>
                <details class="ea-details"><summary>Más datos de valoración</summary><div class="ea-grid">
                    <div><label for="ep-type">Tipo de emergencia</label><select id="ep-type" name="emergency_type_id" class="form-control"><option value="">Sin registrar</option>@foreach($emergencyTypes as $type)<option value="{{ $type->id }}" @if((string)($draft['emergency_type_id'] ?? '') === (string)$type->id) selected @endif>{{ $type->nombre }}</option>@endforeach</select></div>
                    @foreach(['pregnancy'=>['Embarazo / puerperio',[2=>'No aplica',0=>'No',1=>'Sí']], 'conscious'=>['Estado de conciencia',[1=>'Consciente',0=>'Inconsciente',2=>'Sin valoración']], 'high_risk'=>['Riesgo alto observado',[0=>'No',1=>'Sí']], 'isolation'=>['Necesidad de aislamiento',[0=>'No',1=>'Sí']]] as $field=>$definition)
                    <div><label for="ep-{{ $field }}">{{ $definition[0] }}</label><select id="ep-{{ $field }}" class="form-control" name="{{ $field }}"><option value="">Sin registrar</option>@foreach($definition[1] as $value=>$label)<option value="{{ $value }}" @if((string)($draft[$field] ?? '') === (string)$value) selected @endif>{{ $label }}</option>@endforeach</select></div>
                    @endforeach
                    @foreach(['weight'=>'Peso (kg)','height'=>'Talla (cm)','capillary_refill'=>'Llenado capilar (seg.)'] as $field=>$label)<div><label for="ep-{{ $field }}">{{ $label }}</label><input id="ep-{{ $field }}" class="form-control" name="{{ $field }}" type="number" min="0" step=".01" value="{{ $draft[$field] ?? '' }}"></div>@endforeach
                    <div><label for="ep-pupillary">Reacción pupilar</label><input id="ep-pupillary" class="form-control" name="pupillary_response" maxlength="191" value="{{ $draft['pupillary_response'] ?? '' }}"></div>
                </div></details>
                @include('hospital.emergencia.classification', ['classificationPrefix'=>'ep', 'classificationLegacy'=>false, 'classificationPriorities'=>$priorities, 'classificationValues'=>$draft])
                <label for="ep-note">Información adicional y pendientes para el siguiente profesional</label><textarea id="ep-note" class="form-control" name="progress_note" rows="3" maxlength="2000">{{ $draft['progress_note'] ?? '' }}</textarea>
            </section>
            <footer class="ea-footer ea-progress-footer"><button class="ea-button ea-button-primary" type="submit" name="save_mode" value="DRAFT">Guardar avances</button><button class="ea-button ea-button-secondary" type="submit" name="save_mode" value="CONFIRM">Guardar y confirmar triaje</button></footer>
        </form>
        <aside>
            <div class="ea-card"><h3>Continuar el mismo episodio</h3><p><strong>Triaje:</strong> {{ $triage ? $triage->priority_name : 'Pendiente de completar' }}</p><p><strong>Ficha:</strong> {{ $source->paciente->registro_emergencia_pendiente ? 'Datos por completar' : 'Registrada' }}</p><p>Los avances se guardan por separado de la valoración confirmada.</p>@if(!empty($flow->attention_started_at) && empty($flow->closed_at) && in_array($flow->status ?? null, ['IN_ATTENTION','UNDER_OBSERVATION'], true))<a class="ea-button ea-button-primary mb-2" href="{{ route('hospital.emergency-care.show', ['solicitud'=>$source->id]) }}">Continuar atención</a>@endif<a class="ea-button ea-button-secondary mb-2" href="{{ route('hospital.ingreso008', ['id_solicitud'=>$source->id]) }}">{{ $source->id_agenda ? 'Abrir Historia 008' : 'Vincular Historia 008' }}</a><a class="ea-button ea-button-secondary mb-2" href="{{ route('hospital.documents.index', ['id_solicitud'=>$source->id]) }}">Formularios del episodio</a><a class="ea-text-link" href="{{ route('v3.patients.edit', ['patient'=>$source->id_paciente]) }}">Completar ficha administrativa</a></div>
            <section class="ea-card mt-3"><h3>Avances del equipo</h3><p>Últimos 30 guardados. Cada registro conserva fecha y responsable.</p>
                @forelse($history as $entry)
                <details class="ea-details"><summary>{{ $entry->stage === 'CONFIRMED' ? 'Triaje confirmado' : 'Avance guardado' }} · {{ $entry->created_at }}<span>{{ $entry->recorded_by }}</span></summary>
                    @foreach($entry->patient_changes as $field=>$change)<p>{{ $progressLabels[$field] ?? 'Dato del paciente' }}: {{ $change['before'] ?? 'Sin dato' }} → {{ $change['after'] }}</p>@endforeach
                    @foreach($entry->clinical_data as $field=>$value)
                    @if(isset($progressLabels[$field]) && $field !== 'priority_id')<p>{{ $progressLabels[$field] }}: {{ ['PATIENT'=>'Paciente','FAMILY'=>'Familiar / acompañante','STAFF'=>'Equipo asistencial','UNAVAILABLE'=>'Paciente aún no puede responder'][(string)$value] ?? $value }}</p>@endif
                    @endforeach
                </details>
                @empty<p>Todavía no hay avances guardados.</p>@endforelse
            </section>
        </aside>
    </div>
</main>
<script defer src="{{ asset('js/emergency-classification.js') }}?v=6"></script>
<script defer src="{{ asset('js/emergency-autosave.js') }}?v=1"></script>
<script defer src="{{ asset('js/emergency-progress.js') }}?v=4"></script>
@endsection
