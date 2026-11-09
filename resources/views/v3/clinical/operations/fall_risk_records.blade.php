@include('v3.clinical.operations._styles')
<style>
  .co-risk-level{display:inline-flex;align-items:center;padding:4px 8px;border-radius:14px;background:#fff1d6;color:#90600b;font-size:10px;font-weight:800}.co-pdf{display:inline-flex;align-items:center;gap:5px}.co-record-summary{display:flex;align-items:center;gap:8px;flex-wrap:wrap}.co-record-summary span{padding:5px 9px;border-radius:16px;background:#e4f5f9;color:#25637e;font-size:10px;font-weight:700}
</style>
<div class="co-page">
  <section class="co-hero">
    <div class="co-hero__main">
      <div class="co-hero__icon"><i class="fas fa-clipboard-list"></i></div>
      <div><div class="co-eyebrow">SEGURIDAD DEL PACIENTE · V3</div><h1>Registro de camillas</h1><p>Consulta las estancias finalizadas y descarga la evaluación de riesgo correspondiente.</p></div>
    </div>
    <a class="btn btn-light" href="{{route('v3.clinical-operations.fall-risk')}}"><i class="fas fa-arrow-left mr-1"></i> Volver a camillas</a>
  </section>

  <section class="co-card">
    <header class="co-head">
      <div class="co-heading"><i class="fas fa-filter"></i><div><h2>Filtrar registros</h2><p>El rango usa la fecha del movimiento y, si no existe, la fecha de ingreso.</p></div></div>
      <div class="co-record-summary"><span><i class="fas fa-list mr-1"></i>{{$records->total()}} registro(s)</span></div>
    </header>
    <form class="co-filters" method="get" action="{{route('v3.clinical-operations.fall-risk-records')}}">
      <div class="co-field"><label for="desde">Desde</label><input id="desde" class="form-control" type="date" name="desde" value="{{$filters['desde']}}" max="{{$filters['hasta']}}"></div>
      <div class="co-field"><label for="hasta">Hasta</label><input id="hasta" class="form-control" type="date" name="hasta" value="{{$filters['hasta']}}" min="{{$filters['desde']}}"></div>
      <div class="co-field"><label for="buscar">Paciente, camilla u hospital</label><input id="buscar" class="form-control" name="buscar" value="{{$filters['buscar']}}" placeholder="Nombre, cédula o ubicación"></div>
      <div class="co-filter-actions"><button class="btn btn-primary" type="submit"><i class="fas fa-search mr-1"></i> Consultar</button><a class="btn btn-outline-secondary" href="{{route('v3.clinical-operations.fall-risk-records')}}" title="Limpiar filtros"><i class="fas fa-eraser"></i></a></div>
    </form>
  </section>

  <section class="co-card">
    <header class="co-head"><div class="co-heading"><i class="fas fa-history"></i><div><h2>Historial de estancias</h2><p>Cada fila corresponde a una asignación de camilla finalizada.</p></div></div></header>
    <div class="co-table-wrap">
      <table class="table co-table">
        <thead><tr><th>Camilla</th><th>Paciente</th><th>Hospital</th><th>Ingreso</th><th>Salida</th><th>Estado</th><th>Riesgo</th><th>Documento</th></tr></thead>
        <tbody>
          @forelse($records as $record)
          <tr>
            <td><span class="co-name"><i class="fas fa-bed mr-1"></i>{{$record->nombre_camilla ?: 'Sin nombre'}}</span><span class="co-meta">Asignación #{{$record->id}}</span></td>
            <td><span class="co-name">{{$record->patient_name ?: 'Paciente no identificado'}}</span><span class="co-meta">{{$record->id_paciente}}{{$record->patient_age !== null ? ' · '.$record->patient_age.' años' : ''}}</span></td>
            <td><span class="co-name">{{$record->nombre_hospital ?: 'No identificado'}}</span></td>
            <td>{{$record->ingreso_at ? date('d/m/Y H:i', strtotime($record->ingreso_at)) : '—'}}</td>
            <td>{{$record->salida_at ? date('d/m/Y H:i', strtotime($record->salida_at)) : '—'}}</td>
            <td><span class="co-badge"><i class="fas fa-check-circle mr-1"></i>Alta</span></td>
            <td>@if($record->nivel_riesgo !== null && $record->nivel_riesgo !== '')<span class="co-risk-level">{{$record->nivel_riesgo}}</span>@else<span class="co-meta">Sin nivel registrado</span>@endif</td>
            <td>
              @if($record->id_agenda)
                <a class="btn btn-outline-danger co-pdf" target="_blank" rel="noopener" href="{{$record->patient_age !== null && $record->patient_age < 13 ? route('riesgo_menor.pdf',array('id'=>$record->id_agenda)) : route('riesgo.pdf',array('id'=>$record->id_agenda))}}"><i class="fas fa-file-pdf"></i> PDF</a>
              @else<span class="co-meta">Sin agenda</span>@endif
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="co-empty"><i class="fas fa-clipboard-list fa-2x d-block mb-2"></i><strong>No hay registros para los filtros seleccionados.</strong><span class="co-meta">Prueba con otro rango de fechas o limpia la búsqueda.</span></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <footer class="co-footer"><span>Mostrando {{$records->firstItem() ?: 0}} a {{$records->lastItem() ?: 0}} de {{$records->total()}}</span>{{$records->appends($filters)->links()}}</footer>
  </section>
</div>
