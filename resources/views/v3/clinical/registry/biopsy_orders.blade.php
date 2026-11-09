@include('v3.clinical.registry._styles')
<style>
  .bo-patient{display:flex;align-items:center;gap:14px}.bo-patient__avatar{display:grid;flex:0 0 52px;height:52px;place-items:center;border-radius:15px;color:#087fa8;background:#e4f5f9;font-size:20px}.bo-patient h2{margin:0;color:#073d62;font-size:18px;font-weight:800}.bo-patient p{margin:4px 0 0;color:#6d8797;font-size:11px}.bo-patient__stats{display:flex;margin-left:auto;gap:10px}.bo-stat{min-width:92px;padding:9px 12px;border:1px solid #d8e8ee;border-radius:10px;background:#f8fbfc;text-align:center}.bo-stat strong,.bo-stat span{display:block}.bo-stat strong{color:#087fa8;font-size:17px}.bo-stat span{margin-top:2px;color:#78909e;font-size:9px;font-weight:700;text-transform:uppercase}
  .bo-list{display:grid;padding:18px;gap:12px}.bo-order{overflow:hidden;border:1px solid #d3e4eb;border-radius:13px;background:#fff;transition:border-color .18s,box-shadow .18s}.bo-order[open]{border-color:#9fcfdb;box-shadow:0 8px 22px rgba(15,83,112,.08)}.bo-order summary{display:flex;min-height:74px;align-items:center;gap:14px;padding:13px 16px;cursor:pointer;list-style:none}.bo-order summary::-webkit-details-marker{display:none}.bo-order__number{display:grid;flex:0 0 43px;height:43px;place-items:center;border-radius:12px;color:#fff;background:linear-gradient(135deg,#0877a4,#13aaa8);font-size:16px}.bo-order__main{display:flex;min-width:0;flex:1;flex-direction:column}.bo-order__main strong{color:#123f5c;font-size:13px}.bo-order__main span{margin-top:4px;color:#78909e;font-size:10px}.bo-order__meta{display:grid;min-width:150px;gap:4px}.bo-order__meta span{color:#617d8d;font-size:10px}.bo-order__meta i{width:15px;color:#0a91ae}.bo-order__actions{display:flex;align-items:center;gap:9px}.bo-order__actions .btn{padding:8px 12px;border-radius:8px;font-size:10px;font-weight:800}.bo-order__arrow{display:grid;width:30px;height:30px;place-items:center;border-radius:8px;color:#678696;background:#eef6f8;transition:transform .18s}.bo-order[open] .bo-order__arrow{transform:rotate(180deg)}
  .bo-order__body{padding:0 16px 16px;border-top:1px solid #e2edf1;background:#fbfdfe}.bo-section-title{display:flex;align-items:center;gap:8px;margin:15px 0 9px;color:#174960;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.3px}.bo-section-title i{color:#0794b4}.bo-samples{overflow:hidden;border:1px solid #d7e6ec;border-radius:10px;background:#fff}.bo-samples table{width:100%;margin:0;border-collapse:collapse}.bo-samples th{padding:9px 11px;color:#507184;background:#edf6f8;font-size:9px;text-align:left;text-transform:uppercase}.bo-samples td{padding:10px 11px;border-top:1px solid #e3edf1;color:#355c70;font-size:11px;vertical-align:top}.bo-flask{display:inline-flex;min-width:24px;height:24px;align-items:center;justify-content:center;border-radius:7px;color:#087fa8;background:#e5f5f8;font-weight:800}.bo-clinical{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.bo-note{min-height:100px;padding:13px;border:1px solid #d9e7ed;border-radius:10px;background:#fff}.bo-note strong{display:flex;align-items:center;gap:7px;color:#174a63;font-size:10px;text-transform:uppercase}.bo-note strong i{color:#0b96ad}.bo-note p{margin:8px 0 0;color:#4d6d7e;font-size:11px;line-height:1.55}.bo-empty{padding:65px 20px;text-align:center}.bo-empty__icon{display:grid;width:72px;height:72px;margin:0 auto 16px;place-items:center;border:1px solid #cde7ee;border-radius:22px;color:#0a91b2;background:#eaf7fa;font-size:28px}.bo-empty h3{margin:0;color:#16455f;font-size:17px;font-weight:800}.bo-empty p{max-width:460px;margin:7px auto 0;color:#718a99;font-size:11px;line-height:1.5}
  @media(max-width:800px){.bo-patient{align-items:flex-start}.bo-patient__stats{flex-direction:column}.bo-order summary{align-items:flex-start;flex-wrap:wrap}.bo-order__meta{margin-left:57px}.bo-order__actions{width:100%;justify-content:flex-end}.bo-clinical{grid-template-columns:1fr}}
  @media(max-width:520px){.bo-patient{flex-wrap:wrap}.bo-patient__stats{width:100%;flex-direction:row;margin-left:0}.bo-stat{flex:1}.bo-order__meta{width:calc(100% - 57px)}.bo-order__actions .btn span{display:none}.bo-samples{overflow-x:auto}.bo-samples table{min-width:580px}}
</style>
<?php
  $nameParts = array($patient->apellido1, $patient->apellido2, $patient->nombre1, $patient->nombre2);
  $nameParts = array_filter($nameParts, function ($part) { return trim((string) $part) !== '' && trim((string) $part) !== '(N/A)'; });
  $patientName = trim(implode(' ', $nameParts));
?>
<div class="co-page">
  <section class="co-hero">
    <div class="co-hero__main"><div class="co-hero__icon"><i class="fas fa-vials"></i></div><div><div class="co-eyebrow">HISTORIA CLÍNICA · V3</div><h1>Órdenes de biopsias</h1><p>Consulta las solicitudes, muestras y datos clínicos registrados para el paciente.</p></div></div>
    <a class="btn btn-outline-light" href="{{route('v3.patients.index')}}"><i class="fas fa-arrow-left mr-1"></i> Volver a pacientes</a>
  </section>

  <section class="co-card">
    <header class="co-head">
      <div class="bo-patient"><div class="bo-patient__avatar"><i class="fas fa-user-injured"></i></div><div><h2>{{$patientName?:'Paciente sin nombre registrado'}}</h2><p><i class="far fa-id-card mr-1"></i>{{$patient->id}}</p></div></div>
      <div class="bo-patient__stats"><div class="bo-stat"><strong>{{$age!==null?$age:'—'}}</strong><span>Edad</span></div><div class="bo-stat"><strong>{{$orders->count()}}</strong><span>Órdenes</span></div></div>
    </header>
  </section>

  <section class="co-card">
    <header class="co-head"><div class="co-heading"><i class="fas fa-clipboard-list"></i><div><h2>Historial de órdenes</h2><p>Cada registro agrupa los frascos vinculados al mismo procedimiento.</p></div></div><span class="co-count">{{$orders->count()}} orden(es)</span></header>
    @if($orders->count())
      <div class="bo-list">
        @foreach($orders as $order)
          <details class="bo-order" {{$loop->first?'open':''}}>
            <summary>
              <span class="bo-order__number"><i class="fas fa-microscope"></i></span>
              <span class="bo-order__main"><strong>Orden de biopsia #{{$order->procedure_id?:$loop->iteration}}</strong><span>{{count($order->samples)}} muestra(s) registrada(s)</span></span>
              <span class="bo-order__meta"><span><i class="far fa-calendar-alt"></i>{{$order->created_at?date('d/m/Y H:i',strtotime($order->created_at)):'Fecha no registrada'}}</span><span><i class="fas fa-user-md"></i>{{$order->doctor_name?:'Profesional no identificado'}}</span></span>
              <span class="bo-order__actions">
                @if($order->procedure_id && $order->history_id && $order->doctor_id)
                  <a class="btn btn-primary" target="_blank" rel="noopener" onclick="event.stopPropagation()" href="{{route('imprimir.orden_biopsias_recepcion',array('id'=>$order->procedure_id,'id_hcid'=>$order->history_id,'id_doct'=>$order->doctor_id))}}"><i class="fas fa-print mr-1"></i><span>Imprimir orden</span></a>
                @else
                  <span class="co-status"><i class="fas fa-info-circle"></i> Impresión no disponible</span>
                @endif
                <span class="bo-order__arrow"><i class="fas fa-chevron-down"></i></span>
              </span>
            </summary>
            <div class="bo-order__body">
              <div class="bo-section-title"><i class="fas fa-vials"></i> Detalle de muestras</div>
              <div class="bo-samples"><table><thead><tr><th>Frasco</th><th>Descripción</th><th>Observación</th></tr></thead><tbody>
                @foreach($order->samples as $sample)<tr><td><span class="bo-flask">{{$sample->numero_frasco?:'—'}}</span></td><td>{{$sample->descripcion_frasco?:'Sin descripción registrada'}}</td><td>{{$sample->observacion?:'Sin observaciones'}}</td></tr>@endforeach
              </tbody></table></div>
              <div class="bo-section-title"><i class="fas fa-notes-medical"></i> Información clínica</div>
              <div class="bo-clinical">
                <article class="bo-note"><strong><i class="fas fa-clipboard-check"></i> Cuadro clínico</strong><p>{!! trim(strip_tags((string)$order->clinical_summary))!==''?nl2br(e(trim(strip_tags((string)$order->clinical_summary)))):'No se registró información clínica para esta orden.' !!}</p></article>
                <article class="bo-note"><strong><i class="fas fa-stethoscope"></i> Diagnóstico</strong><p>{!! trim(strip_tags((string)$order->diagnosis))!==''?nl2br(e(trim(strip_tags((string)$order->diagnosis)))):'No se registró un diagnóstico para esta orden.' !!}</p></article>
              </div>
            </div>
          </details>
        @endforeach
      </div>
    @else
      <div class="bo-empty"><span class="bo-empty__icon"><i class="fas fa-microscope"></i></span><h3>No hay órdenes de biopsia registradas</h3><p>Cuando se generen muestras para este paciente, aparecerán aquí agrupadas por procedimiento.</p></div>
    @endif
  </section>
</div>
