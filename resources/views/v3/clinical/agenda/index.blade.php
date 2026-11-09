@include('v3.clinical.operations._styles')
@include('v3.clinical.agenda._styles')
@php
  $scheduleDirectory = isset($scheduleDirectory) && $scheduleDirectory;
@endphp
<div class="co-page ca-page">
  <section class="co-hero">
    <div class="co-hero__main">
      <div class="co-hero__icon"><i class="fas {{$scheduleDirectory?'fa-user-clock':'fa-calendar-check'}}"></i></div>
      @if($scheduleDirectory)
      <div><div class="co-eyebrow">DISPONIBILIDAD MÉDICA · V3</div><h1>Horario de doctores</h1><p>Crea y administra horarios recurrentes y disponibilidades extraordinarias de cada médico.</p></div>
      @else
      <div><div class="co-eyebrow">{{trans('v3/clinical_agenda.management')}}</div><h1>{{trans('v3/clinical_agenda.title')}}</h1><p>{{trans('v3/clinical_agenda.subtitle')}}</p></div>
      @endif
    </div>
  </section>

  @if(session('agenda_success'))<div class="alert alert-success ca-alert"><i class="fas fa-check-circle mr-1"></i> {{session('agenda_success')}}</div>@endif
  @if($errors->any())<div class="alert alert-danger ca-alert"><i class="fas fa-exclamation-circle mr-1"></i> {{trans('v3/clinical_agenda.invalid_limits')}}</div>@endif

  <section class="co-card">
    <header class="co-head"><div class="co-heading"><i class="fas {{$scheduleDirectory?'fa-clock':'fa-user-md'}}"></i><div><h2>{{$scheduleDirectory?'Directorio de horarios':trans('v3/clinical_agenda.directory')}}</h2><p>{{$scheduleDirectory?'Selecciona un profesional para configurar su jornada laboral.':trans('v3/clinical_agenda.directory_help')}}</p></div></div><span class="co-count">{{trans('v3/clinical_agenda.professionals',['count'=>$doctors->count()])}}</span></header>
    <form id="agendaDirectoryFilters" class="ca-filters" method="get" action="{{$scheduleDirectory?route('v3.clinical-agenda.schedules'):route('v3.clinical-agenda.index')}}">
      <div class="co-field"><label for="agenda-search">{{trans('v3/clinical_agenda.doctor_or_id')}}</label><input id="agenda-search" class="form-control" name="buscar" value="{{$filters['search']}}" placeholder="{{trans('v3/clinical_agenda.search_professional')}}"></div>
      <div class="co-field"><label for="agenda-specialty">{{trans('v3/clinical_agenda.specialty')}}</label><select id="agenda-specialty" class="form-control" name="especialidad"><option value="">{{trans('v3/clinical_agenda.all_specialties')}}</option>@foreach($specialties as $specialty)<option value="{{$specialty->id}}" {{$filters['specialty']==$specialty->id?'selected':''}}>{{$specialty->nombre}}</option>@endforeach</select></div>
    </form>
    <div class="ca-doctor-grid">
      @if($doctors->isEmpty())
      <div class="co-empty ca-empty"><i class="fas fa-user-md fa-2x d-block mb-2"></i>{{trans('v3/clinical_agenda.no_doctors')}}@if($specialties->isNotEmpty())<small class="d-block mt-2">Las especialidades activas ya están disponibles en el filtro. Para {{$scheduleDirectory?'gestionar horarios':'abrir una agenda'}}, asigna la especialidad a un médico activo de esta empresa.</small>@endif</div>
      @else
      @foreach($doctors as $doctor)
      @php
        $doctorPhotoName = trim((string) $doctor->imagen_url);
        $doctorHasPhoto = $doctorPhotoName !== '' && strtolower($doctorPhotoName) !== 'avatar.jpg';
        $doctorPhotoUrl = $doctorHasPhoto ? url('avatars/' . rawurlencode(basename($doctorPhotoName))) : '';
      @endphp
      <article class="ca-doctor" data-doctor-id="{{$doctor->id}}">
        <div class="ca-doctor__top"><div class="ca-avatar{{$doctorHasPhoto?' ca-avatar--photo':''}}">@if($doctorHasPhoto)<img src="{{$doctorPhotoUrl}}" alt="Foto de Dr(a). {{$doctor->display_name}}" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='grid';"><span class="ca-avatar__fallback">{{$doctor->initials?:'MD'}}</span>@else<span class="ca-avatar__fallback">{{$doctor->initials?:'MD'}}</span>@endif</div><div class="ca-doctor__identity"><span>{{trans('v3/clinical_agenda.professional')}}</span><h3>Dr(a). {{$doctor->display_name}}</h3><small><i class="fas fa-id-card" aria-hidden="true"></i> {{$doctor->id}}</small></div></div>
        <div class="ca-specialties"><small class="ca-section-label">{{trans('v3/clinical_agenda.specialty')}}</small><div>@if($doctor->specialties->isEmpty())<span>{{trans('v3/clinical_agenda.no_specialty')}}</span>@else @foreach($doctor->specialties as $specialty)<span>{{$specialty->nombre}}</span>@endforeach @endif</div></div>
        <div class="ca-actions">@if(!$scheduleDirectory)<a class="btn btn-primary" href="{{route('v3.doctor-agenda.calendar',array('doctor'=>$doctor->id))}}"><i class="fas fa-calendar-plus"></i> {{trans('v3/clinical_agenda.agenda')}}</a>@endif<a class="btn {{$scheduleDirectory?'btn-primary':'btn-outline-primary'}}" href="{{route('v3.doctor-agenda.schedule',array('doctor'=>$doctor->id))}}"><i class="far fa-clock"></i> {{$scheduleDirectory?'Gestionar horario':trans('v3/clinical_agenda.schedule')}}</a></div>
      </article>
      @endforeach
      @endif
    </div>
  </section>
</div>

<script>
(function(){
  var form=document.getElementById('agendaDirectoryFilters');
  if(!form){return;}
  var search=form.querySelector('[name="buscar"]');
  var specialty=form.querySelector('[name="especialidad"]');
  var timer=null;
  var activeRequest=null;
  var requestNumber=0;

  function applyFilters(){
    window.clearTimeout(timer);
    var url=new URL(form.action,window.location.origin);
    new FormData(form).forEach(function(value,key){
      value=String(value).trim();
      if(value!==''){url.searchParams.set(key,value);}
    });
    var currentRequest=++requestNumber;
    if(activeRequest){activeRequest.abort();}
    activeRequest=window.AbortController?new AbortController():null;
    form.classList.add('is-filtering');
    fetch(url.toString(),{
      credentials:'same-origin',
      headers:{'Accept':'text/html'},
      signal:activeRequest?activeRequest.signal:undefined
    }).then(function(response){
      if(!response.ok){throw new Error('No se pudo actualizar el directorio.');}
      return response.text();
    }).then(function(html){
      if(currentRequest!==requestNumber){return;}
      var nextPage=new DOMParser().parseFromString(html,'text/html');
      var nextGrid=nextPage.querySelector('.ca-doctor-grid');
      var currentGrid=document.querySelector('.ca-doctor-grid');
      var nextCount=nextPage.querySelector('#agendaDirectoryFilters').closest('.co-card').querySelector('.co-count');
      var currentCount=form.closest('.co-card').querySelector('.co-count');
      if(!nextGrid||!currentGrid||!nextCount||!currentCount){throw new Error('Respuesta incompleta.');}
      currentGrid.innerHTML=nextGrid.innerHTML;
      currentCount.textContent=nextCount.textContent;
      window.history.replaceState({},'',url.toString());
    }).catch(function(error){
      if(error.name!=='AbortError'){window.location.assign(url.toString());}
    }).then(function(){
      if(currentRequest===requestNumber){form.classList.remove('is-filtering');}
    });
  }

  search.addEventListener('input',function(){
    window.clearTimeout(timer);
    timer=window.setTimeout(applyFilters,450);
  });
  specialty.addEventListener('change',applyFilters);
  form.addEventListener('submit',function(event){
    event.preventDefault();
    applyFilters();
  });
})();
</script>
