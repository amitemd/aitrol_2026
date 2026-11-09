<div class="da-toolbar">
  <div class="da-toolbar__nav">
    <a class="btn btn-outline-primary js-agenda-nav" href="{{route('v3.doctor-agenda.calendar',array('doctor'=>$doctor->id,'fecha'=>$previous,'vista'=>$viewMode,'canceladas'=>$showCancelled?1:null))}}" data-fragment-url="{{route('v3.doctor-agenda.week',array('doctor'=>$doctor->id,'fecha'=>$previous,'vista'=>$viewMode,'canceladas'=>$showCancelled?1:null))}}" aria-label="{{$viewMode==='dia'?'Día':'Semana'}} anterior"><i class="fas fa-chevron-left"></i></a>
    <a class="btn btn-outline-primary js-agenda-nav" href="{{route('v3.doctor-agenda.calendar',array('doctor'=>$doctor->id,'fecha'=>date('Y-m-d'),'vista'=>$viewMode,'canceladas'=>$showCancelled?1:null))}}" data-fragment-url="{{route('v3.doctor-agenda.week',array('doctor'=>$doctor->id,'fecha'=>date('Y-m-d'),'vista'=>$viewMode,'canceladas'=>$showCancelled?1:null))}}">Hoy</a>
    <a class="btn btn-outline-primary js-agenda-nav" href="{{route('v3.doctor-agenda.calendar',array('doctor'=>$doctor->id,'fecha'=>$next,'vista'=>$viewMode,'canceladas'=>$showCancelled?1:null))}}" data-fragment-url="{{route('v3.doctor-agenda.week',array('doctor'=>$doctor->id,'fecha'=>$next,'vista'=>$viewMode,'canceladas'=>$showCancelled?1:null))}}" aria-label="{{$viewMode==='dia'?'Día':'Semana'}} siguiente"><i class="fas fa-chevron-right"></i></a>
  </div>
  <label class="da-range" title="Seleccione la fecha que desea consultar o agendar">
    <span>Fecha</span>
    <input class="form-control js-agenda-date js-new-agenda-date" type="date" value="{{$anchorDate}}" min="{{date('Y-m-d')}}" data-page-url="{{route('v3.doctor-agenda.calendar',array('doctor'=>$doctor->id,'vista'=>$viewMode,'canceladas'=>$showCancelled?1:null))}}" data-fragment-url="{{route('v3.doctor-agenda.week',array('doctor'=>$doctor->id,'vista'=>$viewMode,'canceladas'=>$showCancelled?1:null))}}" aria-label="Fecha de la agenda">
  </label>
  <div class="da-toolbar__actions">
    <div class="btn-group da-view-switch" role="group" aria-label="Vista de agenda"><a class="btn {{$viewMode==='semana'?'btn-primary':'btn-outline-primary'}} js-agenda-nav" href="{{route('v3.doctor-agenda.calendar',array('doctor'=>$doctor->id,'fecha'=>$anchorDate,'vista'=>'semana','canceladas'=>$showCancelled?1:null))}}" data-fragment-url="{{route('v3.doctor-agenda.week',array('doctor'=>$doctor->id,'fecha'=>$anchorDate,'vista'=>'semana','canceladas'=>$showCancelled?1:null))}}">Semana</a><a class="btn {{$viewMode==='dia'?'btn-primary':'btn-outline-primary'}} js-agenda-nav" href="{{route('v3.doctor-agenda.calendar',array('doctor'=>$doctor->id,'fecha'=>$anchorDate,'vista'=>'dia','canceladas'=>$showCancelled?1:null))}}" data-fragment-url="{{route('v3.doctor-agenda.week',array('doctor'=>$doctor->id,'fecha'=>$anchorDate,'vista'=>'dia','canceladas'=>$showCancelled?1:null))}}">Día</a></div>
    <a class="btn btn-outline-secondary js-agenda-nav" href="{{route('v3.doctor-agenda.calendar',array('doctor'=>$doctor->id,'fecha'=>$weekStart,'vista'=>$viewMode,'canceladas'=>$showCancelled?null:1))}}" data-fragment-url="{{route('v3.doctor-agenda.week',array('doctor'=>$doctor->id,'fecha'=>$weekStart,'vista'=>$viewMode,'canceladas'=>$showCancelled?null:1))}}"><i class="fas fa-history mr-1"></i> {{$showCancelled?'Ver activas':'Ver canceladas'}}</a>
    <a class="btn btn-outline-primary" href="{{route('v3.doctor-agenda.schedule',array('doctor'=>$doctor->id,'fecha'=>$weekStart))}}"><i class="far fa-clock mr-1"></i> Horario laboral</a>
    @php($canCreateInPeriod=!$showCancelled && $weekEnd>=date('Y-m-d'))
    @php($newAgendaDate=$anchorDate>=date('Y-m-d')?$anchorDate:date('Y-m-d'))
    @php($newAgendaStart=$newAgendaDate===date('Y-m-d')?date('Y-m-d\TH:i',strtotime('+1 minute')):$newAgendaDate.'T08:00')
    @if($canCreateInPeriod)
      <a class="btn btn-primary js-activity-modal js-new-agenda-button" href="{{route('v3.doctor-agenda.create',array('doctor'=>$doctor->id,'inicio'=>$newAgendaStart,'tipo'=>0))}}"><i class="fas fa-plus mr-1"></i> Nueva agenda</a>
    @else<span class="da-readonly-badge"><i class="fas fa-eye mr-1"></i> Consulta histórica</span>@endif
  </div>
</div>
<div class="da-week {{$viewMode==='dia'?'da-week--day':''}}">
@foreach($days as $day)
  @php($futureSlots=$day->slots->filter(function($slot){return strtotime($slot->start)>time();}))
  <article class="da-day {{$day->today?'da-day--today':''}} {{$viewMode==='dia'?'da-day--daily':''}}"><header class="da-day__head"><span>{{$day->short}} · {{$day->month}}</span><strong>{{$day->number}}</strong></header><div class="da-day__body">
  @forelse($day->events as $event)
    @if($showCancelled || (string)$event->id_doctor1!==(string)$doctor->id)
      <div class="da-event da-event--{{$event->proc_consul}}">
        <div class="da-event__top"><time><i class="far fa-clock"></i> {{date('H:i',strtotime($event->fechaini))}} – {{date('H:i',strtotime($event->fechafin))}}</time><span class="da-event__status">{{$showCancelled?'Cancelada':$event->state_label}}</span></div>
        <strong class="da-event__patient">{{$event->patient_name}}</strong>
        <div class="da-event__meta">
          @if((int)$event->proc_consul!==2 && $event->id_paciente)<span><i class="far fa-id-card"></i> {{$event->id_paciente}}</span>@endif
          @if((int)$event->proc_consul!==2)<span class="da-event__insurance"><i class="fas fa-shield-alt"></i> {{$event->seguro_nombre?:'Sin seguro registrado'}}</span>@endif
          <span><i class="fas fa-tag"></i> {{$event->type_label}}</span>
          @if(!$showCancelled)<span><i class="fas fa-user-friends"></i> Médico de apoyo</span>@endif
        </div>
      </div>
    @else
      <a class="da-event da-event--{{$event->proc_consul}} js-activity-modal" href="{{route('v3.doctor-agenda.edit',array('doctor'=>$event->id_doctor1,'appointment'=>$event->id))}}">
        <div class="da-event__top"><time><i class="far fa-clock"></i> {{date('H:i',strtotime($event->fechaini))}} – {{date('H:i',strtotime($event->fechafin))}}</time><span class="da-event__status">{{$event->state_label}}</span></div>
        <strong class="da-event__patient">{{$event->patient_name}}</strong>
        <div class="da-event__meta">
          @if((int)$event->proc_consul!==2 && $event->id_paciente)<span><i class="far fa-id-card"></i> {{$event->id_paciente}}</span>@endif
          @if((int)$event->proc_consul!==2)<span class="da-event__insurance"><i class="fas fa-shield-alt"></i> {{$event->seguro_nombre?:'Sin seguro registrado'}}</span>@endif
          <span><i class="fas fa-tag"></i> {{$event->type_label}}</span>
          @if($event->nombre_sala)<span><i class="fas fa-door-open"></i> {{$event->nombre_sala}}</span>@endif
        </div>
        <span class="da-event__open"><i class="fas fa-chevron-right"></i></span>
      </a>
    @endif
  @empty<div class="da-empty"><span class="da-empty__icon"><i class="far fa-calendar-check"></i></span><strong>{{$showCancelled?'Sin agendas canceladas':'Agenda libre'}}</strong><small>{{$showCancelled?'No existen cancelaciones para este día.':'Aún no hay actividades programadas.'}}</small></div>@endforelse
  @if(!$showCancelled && $day->key>=date('Y-m-d') && $futureSlots->count())<div class="da-available da-available--range"><span class="da-available__range-icon"><i class="far fa-clock"></i></span><span><small>Rango disponible</small><strong>{{substr($futureSlots->first()->start,11,5)}} – {{substr($futureSlots->last()->end,11,5)}}</strong></span></div>@elseif(!$showCancelled && $day->key>=date('Y-m-d'))<div class="da-available da-available--empty"><small><i class="far fa-clock mr-1"></i> Disponibilidad</small><p>No hay horarios libres para este día.</p></div>@elseif(!$showCancelled && $day->key<date('Y-m-d'))<div class="da-past-day-note"><i class="fas fa-lock mr-1"></i> Día anterior · solo consulta</div>@endif
  </div></article>
@endforeach
</div>
<div class="da-summary"><span><i class="fas fa-stethoscope"></i> {{$events->where('proc_consul',0)->count()}} consultas</span><span><i class="fas fa-procedures"></i> {{$events->where('proc_consul',1)->count()}} procedimientos</span><span><i class="fas fa-users"></i> {{$events->where('proc_consul',2)->count()}} reuniones</span></div>
