@php
  $previous = date('Y-m-d', strtotime($date.' -1 day'));
  $next = date('Y-m-d', strtotime($date.' +1 day'));
@endphp
<div class="la-agenda-calendar" data-date="{{$date}}">
  <header class="co-head">
    <div class="co-heading">
      <i class="fas fa-calendar-day"></i>
      <div>
        <h2>{{date('d/m/Y', strtotime($date))}}</h2>
        <p>{{$events->count()}} actividad(es) encontrada(s).</p>
      </div>
    </div>
    <div class="la-actions" aria-label="Navegación del calendario">
      <button class="btn btn-light js-la-agenda-date" type="button" data-date="{{$previous}}" aria-label="Día anterior"><i class="fas fa-chevron-left"></i></button>
      <button class="btn btn-outline-primary js-la-agenda-date" type="button" data-date="{{date('Y-m-d')}}">Hoy</button>
      <button class="btn btn-light js-la-agenda-date" type="button" data-date="{{$next}}" aria-label="Día siguiente"><i class="fas fa-chevron-right"></i></button>
    </div>
  </header>

  <form class="co-filters js-la-agenda-form" method="get" action="{{route('v3.laboratory-agenda.index')}}">
    <div class="co-field" style="grid-column:span 3">
      <label for="la-agenda-date">Fecha</label>
      <input id="la-agenda-date" type="date" class="form-control" name="fecha" value="{{$date}}" required>
    </div>
    <div class="co-filter-actions">
      <button class="btn btn-primary js-la-agenda-submit" type="button"><i class="fas fa-search"></i> Consultar</button>
    </div>
  </form>

  <div class="la-agenda-results" aria-live="polite">
    @forelse($events as $event)
      <div class="la-reference" style="display:flex;justify-content:space-between;gap:15px;align-items:center">
        <div>
          <strong>{{date('H:i', strtotime($event->fechaini))}} · {{$event->apellido1}} {{$event->apellido2}} {{$event->nombre1}} {{$event->nombre2}}</strong>
          <small>{{$event->procedimiento ?: 'Atención de laboratorio'}} · {{$event->seguro ?: 'Sin seguro informado'}}</small>
        </div>
        <a class="btn btn-sm btn-outline-primary" href="{{route('v3.laboratory-orders.index', array('paciente'=>$event->id_paciente))}}"><i class="fas fa-flask"></i> Ver órdenes</a>
      </div>
    @empty
      <div class="la-empty"><i class="far fa-calendar-times fa-2x mb-2"></i><br>No hay actividades de laboratorio para esta fecha.</div>
    @endforelse
  </div>
</div>
