@include('v3.clinical.registry._styles')
<div class="co-page">
  <section class="co-hero">
    <div class="co-hero__main">
      <div class="co-hero__icon"><i class="fas fa-calendar-alt"></i></div>
      <div><div class="co-eyebrow">GESTIÓN DE ESPACIOS · V3</div><h1>Disponibilidad de consultorios</h1><p>Unidades, áreas y agenda diaria en una sola pantalla.</p></div>
    </div>
  </section>
  <section class="co-card">
    <header class="co-head">
      <div class="co-heading"><i class="fas fa-hospital"></i><div><h2>Unidades habilitadas</h2><p>Cada tarjeta muestra todas las salas configuradas; no hay subcategorías ocultas.</p></div></div>
      <span class="co-count">{{$hospitals->count()}} unidad(es)</span>
    </header>
    <div class="cr-hospitals">
      @forelse($hospitals as $hospital)
      <article class="cr-hospital cr-hospital--wide">
        <div class="cr-hospital__top">
          <div class="cr-hospital__icon"><i class="fas fa-clinic-medical"></i></div>
          <div><h3>{{$hospital->nombre_hospital}}</h3><p>Áreas y consultorios disponibles en esta unidad.</p></div>
        </div>
        <div class="cr-hospital__meta"><span><i class="fas fa-door-open mr-1"></i> {{$hospital->room_count}} sala(s)</span><span><i class="fas fa-calendar-check mr-1"></i> {{$hospital->agenda_count}} atención(es) hoy</span></div>
        <div class="cr-room-list">
          @forelse($hospital->rooms as $room)
          <a class="cr-room" href="{{route('v3.clinical-registry.availability.schedule',array('hospitalId'=>$hospital->id,'sala'=>$room->id))}}">
            <span class="cr-room__icon"><i class="fas fa-door-open"></i></span>
            <span class="cr-room__body"><strong>{{$room->nombre_sala}}</strong><small>{{$room->category_label}}</small></span>
            <span class="cr-room__count">{{$room->agenda_count}} hoy</span><i class="fas fa-chevron-right"></i>
          </a>
          @empty
          <div class="cr-room-empty">Esta unidad todavía no tiene salas activas configuradas.</div>
          @endforelse
        </div>
        <a class="btn btn-outline-primary" href="{{route('v3.clinical-registry.availability.schedule',array('hospitalId'=>$hospital->id))}}"><i class="fas fa-calendar-day mr-1"></i> Ver agenda completa</a>
      </article>
      @empty
      <div class="co-empty" style="grid-column:1/-1"><i class="fas fa-hospital-alt fa-2x d-block mb-2"></i>No existen unidades activas configuradas para la empresa.</div>
      @endforelse
    </div>
  </section>
</div>
