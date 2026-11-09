@include('v3.clinical.registry._styles')
<?php
  $typeLabels=array(0=>'Consulta',1=>'Procedimiento',2=>'Reunión',3=>'Hospitalización',4=>'Evolución');
  $statusLabels=array(0=>'Por confirmar',1=>'Confirmada',2=>'Reagendada',3=>'Suspendida');
?>
<div class="co-page">
  <section class="co-hero">
    <div class="co-hero__main"><div class="co-hero__icon"><i class="fas fa-calendar-day"></i></div><div><div class="co-eyebrow">AGENDA DE UNIDAD · V3</div><h1>{{$hospital->nombre_hospital}}</h1><p>Consulta la ocupación diaria de todas las áreas sin cargar calendarios anidados.</p></div></div>
    <a class="btn btn-outline-light" href="{{route('v3.clinical-registry.availability')}}"><i class="fas fa-arrow-left mr-1"></i> Unidades</a>
  </section>
  <section class="co-card">
    <header class="co-head"><div class="co-heading"><i class="fas fa-filter"></i><div><h2>Fecha y área</h2><p>Selecciona una sala o consulta toda la unidad.</p></div></div></header>
    <form class="co-filters" method="get" action="{{route('v3.clinical-registry.availability.schedule',array('hospitalId'=>$hospital->id))}}">
      <div class="co-field"><label>Fecha</label><input class="form-control" type="date" name="fecha" value="{{$date}}"></div>
      <div class="co-field"><label>Área o sala</label><select class="form-control" name="sala"><option value="">Todas las áreas</option>@foreach($rooms as $room)<option value="{{$room->id}}" {{$selected_room==(int)$room->id?'selected':''}}>{{$room->nombre_sala}} · {{$room->category_label}}</option>@endforeach</select></div>
      <div class="co-filter-actions"><button class="btn btn-primary"><i class="fas fa-search mr-1"></i> Consultar</button></div>
    </form>
    <div class="cr-room-tabs"><a class="{{$selected_room===null?'is-active':''}}" href="{{route('v3.clinical-registry.availability.schedule',array('hospitalId'=>$hospital->id,'fecha'=>$date))}}">Todas <span>{{$appointments->count()}}</span></a>@foreach($rooms as $room)<a class="{{$selected_room==(int)$room->id?'is-active':''}}" href="{{route('v3.clinical-registry.availability.schedule',array('hospitalId'=>$hospital->id,'fecha'=>$date,'sala'=>$room->id))}}">{{$room->nombre_sala}} <small>{{$room->category_label}}</small><span>{{$room->day_count}}</span></a>@endforeach</div>
  </section>
  <section class="co-card">
    <header class="co-head"><div class="co-heading"><i class="fas fa-clock"></i><div><h2>Agenda del {{date('d/m/Y',strtotime($date))}}</h2><p>Listado cronológico de consultas, procedimientos y demás actividades.</p></div></div><span class="co-count">{{$appointments->count()}} atención(es)</span></header>
    <div class="co-table-wrap"><table class="table table-hover co-table"><thead><tr><th>Horario</th><th>Sala</th><th>Paciente</th><th>Tipo</th><th>Profesional</th><th>Seguro</th><th>Estado</th></tr></thead><tbody>
      @forelse($appointments as $appointment)
      <?php $patient=trim($appointment->patient_lastname1.' '.$appointment->patient_lastname2.' '.$appointment->patient_name1.' '.$appointment->patient_name2); $doctor=trim($appointment->doctor_lastname1.' '.$appointment->doctor_name1); ?>
      <tr><td><span class="co-name">{{date('H:i',strtotime($appointment->fechaini))}} – {{date('H:i',strtotime($appointment->fechafin))}}</span></td><td><span class="co-badge">{{$appointment->nombre_sala}}</span></td><td><span class="co-name">{{$patient?:'Sin paciente asociado'}}</span><span class="co-meta">{{$appointment->id_paciente}}</span></td><td>{{isset($typeLabels[$appointment->proc_consul])?$typeLabels[$appointment->proc_consul]:'Actividad'}}@if($appointment->procedure_name)<span class="co-meta">{{$appointment->procedure_name}}</span>@endif</td><td>{{$doctor?:'No asignado'}}</td><td>{{$appointment->insurance_name?:'No registrado'}}</td><td><span class="cr-status {{$appointment->estado_cita==3?'is-cancelled':($appointment->estado_cita==1?'is-complete':'')}}">{{isset($statusLabels[$appointment->estado_cita])?$statusLabels[$appointment->estado_cita]:'Sin estado'}}</span></td></tr>
      @empty
      <tr><td colspan="7" class="co-empty"><i class="fas fa-calendar-check fa-2x d-block mb-2"></i>No existen actividades programadas para esta selección.</td></tr>
      @endforelse
    </tbody></table></div>
  </section>
</div>
