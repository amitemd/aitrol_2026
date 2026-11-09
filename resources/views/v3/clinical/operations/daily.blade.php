@include('v3.clinical.operations._styles')
<?php
  $reportFilters = array(
    'fecha' => $filters['desde'], 'fecha_hasta' => $filters['hasta'],
    'nombres' => $filters['paciente'], 'cedula' => $filters['cedula'], 'proc_consul' => '2',
    'espid' => $filters['especialidad'], 'id_doctor1' => $filters['doctor'],
    'id_seguro' => $filters['seguro'],
  );
?>
<div class="co-page">
  <section class="co-hero">
    <div class="co-hero__main"><div class="co-hero__icon"><i class="fas fa-notes-medical"></i></div><div><div class="co-eyebrow">ATENCIÓN CLÍNICA · V3</div><h1>Pacientes del día</h1><p>Consulta las atenciones y accede a su historia clínica desde un listado único y trazable.</p></div></div>
    <div class="co-actions"><a class="btn btn-light" href="{{route('historia_clinica.reporte_hc',$reportFilters)}}"><i class="fas fa-file-medical mr-1"></i> Historia clínica</a><a class="btn btn-outline-light" href="{{route('historia_clinica.reporte_hc_iess',$reportFilters)}}"><i class="fas fa-file-download mr-1"></i> Formato IESS</a></div>
  </section>
  <section class="co-card">
    <header class="co-head"><div class="co-heading"><i class="fas fa-filter"></i><div><h2>Filtros clínicos</h2><p>Busca por paciente, cédula, periodo, profesional, especialidad o seguro.</p></div></div></header>
    <form class="co-filters" method="get" action="{{route('v3.clinical-operations.daily')}}">
      <div class="co-field"><label>Fecha inicial</label><input class="form-control" type="date" name="desde" value="{{$filters['desde']}}"></div>
      <div class="co-field"><label>Fecha final</label><input class="form-control" type="date" name="hasta" value="{{$filters['hasta']}}"></div>
      <div class="co-field"><label>Paciente</label><input class="form-control" name="paciente" value="{{$filters['paciente']}}" placeholder="Apellidos o nombres"></div>
      <div class="co-field"><label>Cédula</label><input class="form-control" name="cedula" value="{{$filters['cedula']}}" inputmode="numeric" autocomplete="off" placeholder="Número de identificación"></div>
      <div class="co-field"><label>Especialidad</label><select class="form-control" name="especialidad"><option value="">Todas</option>@foreach($catalogs['specialties'] as $item)<option value="{{$item->id}}" {{(string)$filters['especialidad']===(string)$item->id?'selected':''}}>{{$item->nombre}}</option>@endforeach</select></div>
      <div class="co-field"><label>Doctor</label><select class="form-control" name="doctor"><option value="">Todos</option>@foreach($catalogs['doctors'] as $item)<option value="{{$item->id}}" {{(string)$filters['doctor']===(string)$item->id?'selected':''}}>{{trim($item->apellido1.' '.$item->apellido2.' '.$item->nombre1.' '.$item->nombre2)}}</option>@endforeach</select></div>
      <div class="co-field"><label>Seguro / convenio</label><select class="form-control" name="seguro"><option value="">Todos</option>@foreach($catalogs['insurances'] as $item)<option value="{{$item->id}}" {{(string)$filters['seguro']===(string)$item->id?'selected':''}}>{{$item->nombre}}</option>@endforeach</select></div>
      <div class="co-filter-actions"><button class="btn btn-primary"><i class="fas fa-search mr-1"></i> Buscar</button><a class="btn btn-outline-secondary" href="{{route('v3.clinical-operations.daily')}}"><i class="fas fa-eraser"></i></a></div>
    </form>
  </section>
  <section class="co-card">
    <header class="co-head"><div class="co-heading"><i class="fas fa-user-md"></i><div><h2>Atenciones encontradas</h2><p>Cada fila representa una atención; el paciente conserva una sola identidad clínica.</p></div></div><span class="co-count">{{$rows->total()}} atención(es)</span></header>
    <div class="co-table-wrap"><table class="table table-hover co-table"><thead><tr><th>Paciente</th><th>Fecha y hora</th><th>Doctor</th><th>Atención</th><th>Seguro</th><th>Historia clínica</th><th>Estado</th><th>Acción</th></tr></thead><tbody>
      @forelse($rows as $row)
      <?php
        $name=trim($row->patient_lastname1.' '.$row->patient_lastname2.' '.$row->patient_name1.' '.$row->patient_name2);
        $type=$row->proc_consul==1?'Procedimiento':($row->proc_consul==4?'Hospitalaria':'Consulta');
        $procedures=array(); if($row->procedure_name) $procedures[]=$row->procedure_name;
        foreach($row->additional_procedures as $procedure) if($procedure->nombre && !in_array($procedure->nombre,$procedures,true)) $procedures[]=$procedure->nombre;
      ?>
      <tr><td><span class="co-name">{{$name}}</span><span class="co-meta">{{$row->id_paciente}} · Nac. {{$row->fecha_nacimiento?date('d/m/Y',strtotime($row->fecha_nacimiento)):'no registrada'}}</span></td><td>{{date('d/m/Y H:i',strtotime($row->fechaini))}}<span class="co-meta">{{$row->room_name?:'Sin sala asignada'}}</span></td><td>{{trim($row->doctor_lastname.' '.$row->doctor_name)?:'Sin asignar'}}<span class="co-meta">{{$row->specialty_name}}</span></td><td><span class="co-badge">{{$type}}</span><span class="co-meta">{{count($procedures)?implode(' · ',$procedures):'Sin procedimiento registrado'}}</span></td><td>{{$row->insurance_name?:'Sin seguro'}}</td><td>@if($row->history_id)<span class="co-badge"><i class="fas fa-check-circle mr-1"></i> Creada</span><span class="co-meta">{{$row->diagnosis_count}} diagnóstico(s)</span>@else<span class="co-meta">Pendiente de apertura</span>@endif</td><td>{{$row->estado_cita==4?'Confirmada':'Agendada'}}</td><td><a class="btn btn-outline-primary" href="{{route('v3.clinical-attention.show',array('agenda'=>$row->id))}}"><i class="fas fa-folder-open mr-1"></i> Ver atención</a></td></tr>
      @empty<tr><td colspan="8" class="co-empty"><i class="fas fa-calendar-check fa-2x d-block mb-2"></i>No existen atenciones para los filtros seleccionados.</td></tr>@endforelse
    </tbody></table></div>
    <footer class="co-footer"><span>Mostrando {{$rows->firstItem()?:0}} a {{$rows->lastItem()?:0}} de {{$rows->total()}}</span>{{$rows->appends($filters)->links()}}</footer>
  </section>
</div>
