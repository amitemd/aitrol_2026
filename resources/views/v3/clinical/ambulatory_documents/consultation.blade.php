@include('v3.clinical.ambulatory_documents._styles')
@php
  $patientName = trim($consultation->apellido1.' '.$consultation->apellido2.' '.$consultation->nombre1.' '.$consultation->nombre2);
  $doctorName = trim($consultation->doctor_lastname.' '.$consultation->doctor_lastname2.' '.$consultation->doctor_name);
  $firstDocument = count($documents) ? $documents[0] : null;
@endphp
<div class="ad-page">
  <section class="ad-hero"><div class="ad-hero__main"><div class="ad-hero__icon"><i class="fas fa-file-medical-alt"></i></div><div><div class="ad-eyebrow">CONSULTA AMBULATORIA #{{ $consultation->id }}</div><h1>{{ $patientName }}</h1><p>{{ date('d/m/Y H:i', strtotime($consultation->fechaini)) }} · {{ $doctorName ?: 'Médico no registrado' }}</p></div></div><a class="ad-btn" href="{{ route('v3.ambulatory-documents.patient', $consultation->id_paciente) }}"><i class="fas fa-arrow-left"></i> Volver a consultas</a></section>
  <section class="ad-card"><div class="ad-card__body"><div class="ad-patient"><div class="ad-patient__item"><small>Paciente</small><strong>{{ $patientName }}</strong></div><div class="ad-patient__item"><small>Identificación</small><strong>{{ $consultation->id_paciente }}</strong></div><div class="ad-patient__item"><small>Especialidad</small><strong>{{ $consultation->specialty_name ?: 'No registrada' }}</strong></div><div class="ad-patient__item"><small>Atención</small><strong>{{ (int)$consultation->proc_consul === 1 ? 'Procedimiento' : 'Consulta' }}</strong></div></div></div></section>
  <div class="ad-doc-layout">
    <section class="ad-card"><div class="ad-card__head"><h2>Documentos</h2><span class="ad-pill">{{ count($documents) }}</span></div><div class="ad-card__body"><div class="ad-doc-list">
      @forelse($documents as $index => $document)
        <div class="ad-doc {{ $index === 0 ? 'is-active' : '' }}" data-document-url="{{ $document->url }}"><span class="ad-doc__code">{{ $document->code }}</span><span><strong>{{ $document->title }}</strong><small>{{ $document->date ? date('d/m/Y H:i', strtotime($document->date)) : 'Registro de esta consulta' }}</small></span><i class="fas fa-chevron-right"></i></div>
      @empty<div class="ad-empty"><i class="far fa-folder-open fa-2x mb-2"></i><br>Esta consulta todavía no tiene formularios PDF registrados.</div>@endforelse
      </div><div class="ad-actions">@if($firstDocument)<a id="ad-open-document" class="ad-btn ad-btn--primary" href="{{ $firstDocument->url }}" target="_blank" rel="noopener"><i class="fas fa-external-link-alt"></i> Abrir aparte</a>@endif<a class="ad-btn" href="{{ route('paciente.historia', ['id_paciente' => $consultation->id_paciente]) }}" target="_blank" rel="noopener"><i class="fas fa-book-medical"></i> Historia clínica</a></div></div></section>
    <section class="ad-viewer">@if($firstDocument)<iframe id="ad-document-frame" src="{{ $firstDocument->url }}" title="Documento clínico"></iframe>@else<div class="ad-empty" style="color:#fff;padding-top:220px">Selecciona una consulta con documentos disponibles.</div>@endif</section>
  </div>
</div>
@if($firstDocument)
<script>
(function(){var frame=document.getElementById('ad-document-frame'),open=document.getElementById('ad-open-document'),items=document.querySelectorAll('[data-document-url]');Array.prototype.forEach.call(items,function(item){item.addEventListener('click',function(){Array.prototype.forEach.call(items,function(other){other.classList.remove('is-active')});item.classList.add('is-active');frame.src=item.getAttribute('data-document-url');open.href=item.getAttribute('data-document-url')})})})();
</script>
@endif
