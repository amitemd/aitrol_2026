@include('v3.clinical.registry._styles')
<style>
  .rx-patient{display:flex;align-items:center;gap:14px}.rx-avatar{width:52px;height:52px;border-radius:16px;background:#e4f6fb;color:#087fac;display:grid;place-items:center;font-size:21px}.rx-patient h2{margin:0;color:#063b63;font-size:20px}.rx-patient p{margin:3px 0 0;color:#607d91}.rx-list{display:grid;gap:18px;padding:20px}.rx-card{border:1px solid #cfe1eb;border-radius:14px;background:#fff;overflow:hidden}.rx-card__head{display:flex;justify-content:space-between;gap:18px;align-items:center;padding:15px 18px;background:#f1f8fb;border-bottom:1px solid #d8e8f0}.rx-card__head h3{margin:0;color:#073d64;font-size:16px}.rx-card__head p{margin:4px 0 0;color:#658094;font-size:12px}.rx-actions{display:flex;gap:7px;flex-wrap:wrap}.rx-body{display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:18px}.rx-panel{border:1px solid #d8e5ec;border-radius:10px;padding:14px;min-height:125px}.rx-panel h4{font-size:13px;color:#087fac;text-transform:uppercase;letter-spacing:.04em;margin:0 0 10px}.rx-panel p{color:#27485e;margin:0 0 8px;white-space:normal}.rx-medicine{padding:8px 0;border-bottom:1px dashed #d7e4eb}.rx-medicine:last-child{border-bottom:0}.rx-footer{padding:0 20px 20px}@media(max-width:800px){.rx-card__head{align-items:flex-start;flex-direction:column}.rx-body{grid-template-columns:1fr}.rx-actions{width:100%}.rx-actions .btn{flex:1}}
</style>
<?php
  $patientName=trim($patient->apellido1.' '.$patient->apellido2.' '.$patient->nombre1.' '.$patient->nombre2);
?>
<div class="co-page">
  <section class="co-hero"><div class="co-hero__main"><div class="co-hero__icon"><i class="fas fa-prescription-bottle-alt"></i></div><div><div class="co-eyebrow">HISTORIA CLÍNICA · V3</div><h1>Historial de recetas</h1><p>Consulta las indicaciones, medicamentos y documentos emitidos al paciente.</p></div></div><a class="btn btn-outline-light" href="{{url()->previous()}}"><i class="fas fa-arrow-left mr-1"></i> Regresar</a></section>

  <section class="co-card"><header class="co-head"><div class="rx-patient"><div class="rx-avatar"><i class="fas fa-user"></i></div><div><h2>{{$patientName?:'Paciente sin nombre registrado'}}</h2><p>Identificación: {{$patient->id}} · {{$prescriptions->total()}} receta(s)</p></div></div></header></section>

  <section class="co-card"><header class="co-head"><div class="co-heading"><i class="fas fa-notes-medical"></i><div><h2>Recetas emitidas</h2><p>Ordenadas desde la atención más reciente.</p></div></div><span class="co-count">{{$prescriptions->total()}} registro(s)</span></header>
    <div class="rx-list">
      @forelse($prescriptions as $prescription)
        <?php
          $doctor=trim($prescription->doctor_lastname1.' '.$prescription->doctor_lastname2.' '.$prescription->doctor_name1.' '.$prescription->doctor_name2);
          $recipeDetails=$details->get($prescription->id, collect());
          $date=$prescription->fecha_atencion?:($prescription->fechaini?:$prescription->created_at);
        ?>
        <article class="rx-card">
          <header class="rx-card__head"><div><h3><i class="fas fa-file-prescription mr-1"></i> Receta #{{$prescription->id}}</h3><p>{{date('d/m/Y H:i',strtotime($date))}} · Dr(a). {{$doctor?:'No identificado'}} · {{$prescription->insurance_name?:'Seguro no registrado'}}</p></div><div class="rx-actions"><a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="{{route('hc_receta.imprime',array('id'=>$prescription->id,'tipo'=>1))}}"><i class="fas fa-print mr-1"></i> Imprimir</a><a class="btn btn-primary btn-sm" target="_blank" rel="noopener" href="{{route('hc_receta.imprime',array('id'=>$prescription->id,'tipo'=>2))}}"><i class="fas fa-file-medical mr-1"></i> Membretada</a></div></header>
          <div class="rx-body">
            <div class="rx-panel"><h4>Rp · Medicamentos</h4>@foreach($recipeDetails as $detail)<div class="rx-medicine"><strong>{{($detail->nombre?:optional($detail->medicina)->nombre)?:'Medicamento'}}</strong>@if($detail->cantidad || $detail->detalle_cantidad)<div>{{trim($detail->cantidad.' '.$detail->detalle_cantidad)}}</div>@endif</div>@endforeach @if(trim(strip_tags((string)$prescription->rp))!=='')<p>{!! nl2br(e(trim(strip_tags($prescription->rp)))) !!}</p>@elseif($recipeDetails->isEmpty())<span class="co-meta">Sin medicamentos registrados.</span>@endif</div>
            <div class="rx-panel"><h4>Prescripción e indicaciones</h4>@foreach($recipeDetails as $detail)<div class="rx-medicine"><strong>{{($detail->nombre?:optional($detail->medicina)->nombre)?:'Medicamento'}}</strong>@if($detail->dosis)<div>{{$detail->dosis}}</div>@endif</div>@endforeach @if(trim(strip_tags((string)$prescription->prescripcion))!=='')<p>{!! nl2br(e(trim(strip_tags($prescription->prescripcion)))) !!}</p>@elseif($recipeDetails->isEmpty())<span class="co-meta">Sin indicaciones registradas.</span>@endif</div>
          </div>
        </article>
      @empty
        <div class="co-empty"><i class="fas fa-prescription-bottle-alt fa-2x d-block mb-2"></i><strong>No hay recetas registradas</strong><div>El paciente todavía no tiene recetas con contenido en la empresa activa.</div></div>
      @endforelse
    </div>
    @if($prescriptions->hasPages())<footer class="co-footer"><span>Mostrando {{$prescriptions->firstItem()}} a {{$prescriptions->lastItem()}} de {{$prescriptions->total()}}</span>{{$prescriptions->links()}}</footer>@endif
  </section>
</div>
