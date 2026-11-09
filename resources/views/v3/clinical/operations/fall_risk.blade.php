@include('v3.clinical.operations._styles')
<div class="co-page">
  <section class="co-hero">
    <div class="co-hero__main"><div class="co-hero__icon"><i class="fas fa-exclamation-triangle"></i></div><div><div class="co-eyebrow">SEGURIDAD DEL PACIENTE · V3</div><h1>Riesgo de caída y camillas</h1><p>Supervisa la ocupación y abre la evaluación correspondiente sin perder el contexto del paciente.</p></div></div>
    <a class="btn btn-light" href="{{route('v3.clinical-operations.fall-risk-records')}}"><i class="fas fa-clipboard-list mr-1"></i> Registro de camillas</a>
  </section>
  <div class="co-stats"><div class="co-stat"><strong>{{$summary['total']}}</strong><span>Camillas encontradas</span></div><div class="co-stat"><strong>{{$summary['free']}}</strong><span>Libres en esta página</span></div><div class="co-stat"><strong>{{$summary['occupied']}}</strong><span>Ocupadas en esta página</span></div><div class="co-stat"><strong>{{$summary['preparation']}}</strong><span>En preparación</span></div><div class="co-stat"><strong>{{$summary['disinfection']}}</strong><span>En desinfección</span></div></div>
  <section class="co-card">
    <header class="co-head"><div class="co-heading"><i class="fas fa-search"></i><div><h2>Localizar camilla</h2><p>Selecciona el establecimiento y filtra por el nombre de la camilla.</p></div></div></header>
    <form class="co-filters" method="get" action="{{route('v3.clinical-operations.fall-risk')}}"><div class="co-field"><label>Hospital</label><select class="form-control" name="hospital"><option value="0">Todos</option>@foreach($hospitals as $hospital)<option value="{{$hospital->id}}" {{$filters['hospital']==$hospital->id?'selected':''}}>{{$hospital->nombre_hospital}}</option>@endforeach</select></div><div class="co-field"><label>Camilla</label><input class="form-control" name="buscar" value="{{$filters['buscar']}}" placeholder="Nombre o código"></div><div class="co-filter-actions"><button class="btn btn-primary"><i class="fas fa-search mr-1"></i> Buscar</button><a class="btn btn-outline-secondary" href="{{route('v3.clinical-operations.fall-risk')}}"><i class="fas fa-eraser"></i></a></div></form>
  </section>
  <section class="co-card">
    <header class="co-head"><div class="co-heading"><i class="fas fa-bed"></i><div><h2>Estado de camillas</h2><p>La evaluación se habilita únicamente cuando existe un paciente asociado.</p></div></div><span class="co-count">{{$beds->total()}} camilla(s)</span></header>
    <div class="co-bed-grid">
      @forelse($beds as $bed)
      <?php $stateClass=$bed->state_code===2?'is-occupied':($bed->state_code===3?'is-preparation':($bed->state_code===4?'is-disinfection':'')); $agendaId=$bed->management?$bed->management->id_agenda:null; ?>
      <article class="co-bed {{$stateClass}}">
        <span class="co-bed__state">{{$bed->state_label}}</span><h3><i class="fas fa-bed mr-1"></i> {{$bed->nombre_camilla}}</h3><span class="co-meta">{{$bed->hospital_name?:'Hospital no identificado'}}</span>
        <div class="co-bed__patient">@if($bed->patient)<span class="co-name">{{trim($bed->patient->apellido1.' '.$bed->patient->apellido2.' '.$bed->patient->nombre1.' '.$bed->patient->nombre2)}}</span><span class="co-meta">{{$bed->patient->id}}{{$bed->patient_age!==null?' · '.$bed->patient_age.' años':''}}</span>@else<span class="co-meta"><i class="fas fa-check-circle mr-1"></i> Sin paciente asignado</span>@endif</div>
        <div class="co-bed__actions">
          <a class="btn btn-outline-secondary" target="_blank" rel="noopener" href="{{route('background_estado',array('id_cama'=>$bed->id))}}"><i class="fas fa-exchange-alt mr-1"></i> Gestionar estado</a>
          @if($bed->patient && $agendaId)
            @if($bed->patient_age!==null && $bed->patient_age<18)
              <a class="btn btn-outline-primary" href="{{route('riesgo.form_pediatrico',array('id'=>$bed->patient->id,'id_camilla'=>$bed->id,'id_agenda'=>$agendaId))}}"><i class="fas fa-child mr-1"></i> Evaluar</a>
            @else
              <a class="btn btn-outline-primary" href="{{route('riesgo.form_adulto',array('id'=>$bed->patient->id,'id_camilla'=>$bed->id,'id_agenda'=>$agendaId))}}"><i class="fas fa-walking mr-1"></i> Evaluar</a>
            @endif
          @endif
        </div>
      </article>
      @empty<div class="co-empty" style="grid-column:1/-1"><i class="fas fa-bed fa-2x d-block mb-2"></i>No existen camillas para los filtros seleccionados.</div>@endforelse
    </div>
    <footer class="co-footer"><span>Mostrando {{$beds->firstItem()?:0}} a {{$beds->lastItem()?:0}} de {{$beds->total()}}</span>{{$beds->appends($filters)->links()}}</footer>
  </section>
</div>
