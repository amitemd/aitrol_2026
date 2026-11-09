<style>
  .o12-edit{padding:22px}.o12-top{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:24px 27px;margin-bottom:18px;border-radius:17px;color:#fff;background:linear-gradient(115deg,#0a5b86,#138db3 62%,#16aaa5)}.o12-top__title{display:flex;align-items:center;gap:16px}.o12-top__icon{display:grid;place-items:center;width:58px;height:58px;border:1px solid rgba(255,255,255,.3);border-radius:15px;background:rgba(255,255,255,.11);font-size:25px}.o12-top h1{margin:0 0 4px;font-size:26px;font-weight:800}.o12-top p{margin:0;opacity:.92}.o12-top .btn{border-radius:9px;font-weight:700}
  .o12-alert{padding:13px 16px;margin-bottom:14px;border-radius:10px}.o12-card{margin-bottom:17px;overflow:hidden;border:1px solid #d1e2ea;border-radius:15px;background:#fff;box-shadow:0 7px 20px rgba(31,80,102,.05)}.o12-card__head{display:flex;align-items:center;gap:11px;padding:15px 18px;border-bottom:1px solid #d8e6ec;background:#eff9fc}.o12-card__head i{display:grid;place-items:center;width:35px;height:35px;border-radius:10px;color:#0788b7;background:#d9f2f7}.o12-card__head h2{margin:0;font-size:17px;font-weight:800;color:#123d5c}.o12-card__body{padding:19px}.o12-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:15px}.o12-field--2{grid-column:span 2}.o12-field--4{grid-column:1/-1}.o12-field label{display:block;margin-bottom:6px;color:#244c67;font-size:12px;font-weight:800;text-transform:uppercase}.o12-field .form-control{border-color:#c6dce6;border-radius:8px}.o12-field textarea.form-control{min-height:88px;resize:vertical}
  .o12-checks{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:9px}.o12-check{display:flex;align-items:flex-start;gap:9px;min-height:45px;padding:11px;border:1px solid #d6e5eb;border-radius:9px;background:#fbfdfe;color:#244b64}.o12-check input{margin-top:3px}.o12-diagnosis-form{display:grid;grid-template-columns:2fr 1fr auto;gap:10px;align-items:end;margin-bottom:16px}.o12-table th{background:#f2f7f9;color:#173f5b;font-size:12px;text-transform:uppercase}.o12-footer{position:sticky;bottom:0;display:flex;justify-content:flex-end;gap:9px;padding:15px 18px;border-top:1px solid #d5e5eb;background:rgba(247,251,252,.96);backdrop-filter:blur(6px)}.o12-footer .btn{border-radius:9px;font-weight:800}.o12-help{color:#7892a4;font-size:12px}
  @media(max-width:1000px){.o12-grid{grid-template-columns:repeat(2,1fr)}.o12-checks{grid-template-columns:repeat(2,1fr)}}@media(max-width:650px){.o12-edit{padding:12px}.o12-top{align-items:flex-start;flex-direction:column}.o12-grid,.o12-checks,.o12-diagnosis-form{grid-template-columns:1fr}.o12-field--2,.o12-field--4{grid-column:auto}}
</style>

<?php $checks=array(
  'urgente'=>'Urgente','rutina'=>'Rutina','control'=>'Control','rx_convencional'=>'RX convencional','rx_portatil'=>'RX portátil','tomografia'=>'Tomografía','mamografia'=>'Mamografía','resonancia'=>'Resonancia','ecografia'=>'Ecografía','procedimiento'=>'Procedimiento','otros'=>'Otros estudios','paciente_contaminado'=>'Paciente contaminado','sedacion'=>'Requiere sedación','puede_mover'=>'Puede movilizarse','puede_retirar'=>'Puede retirar vendas, apósitos o yesos','medico_presente'=>'Médico presente en el examen','toma_radio'=>'Radiografía en cama'
); ?>

<div class="o12-edit">
  <section class="o12-top">
    <div class="o12-top__title"><div class="o12-top__icon"><i class="fas fa-file-medical-alt"></i></div><div><h1>Editar Formato 012</h1><p>Orden #{{$order->id}} · Paciente {{$order->id_paciente}}</p></div></div>
    <a class="btn btn-light" href="{{route('v3.order012.index')}}"><i class="fas fa-arrow-left mr-1"></i> Volver al listado</a>
  </section>

  @if(session('success'))<div class="alert alert-success o12-alert"><i class="fas fa-check-circle mr-1"></i>{{session('success')}}</div>@endif
  @if($errors->any())<div class="alert alert-danger o12-alert"><strong>No se pudo guardar:</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul></div>@endif

  <form method="post" action="{{route('v3.order012.update',array('id'=>$order->id))}}">
    {{csrf_field()}}
    <section class="o12-card">
      <header class="o12-card__head"><i class="fas fa-user-md"></i><h2>Responsables y referencia</h2></header>
      <div class="o12-card__body o12-grid">
        <div class="o12-field"><label for="id_doctor_solicita">Médico solicitante</label><select class="form-control" id="id_doctor_solicita" name="id_doctor_solicita">@foreach($doctors as $doctor)<option value="{{$doctor->id}}" {{old('id_doctor_solicita',$order->id_doctor_solicita)==$doctor->id?'selected':''}}>{{$doctor->apellido1}} {{$doctor->nombre1}}</option>@endforeach</select></div>
        <div class="o12-field"><label for="id_doctor_firma">Médico que firma</label><select class="form-control" id="id_doctor_firma" name="id_doctor_firma" required>@foreach($doctors as $doctor)<option value="{{$doctor->id}}" {{old('id_doctor_firma',$order->id_doctor_firma)==$doctor->id?'selected':''}}>{{$doctor->apellido1}} {{$doctor->nombre1}}</option>@endforeach</select></div>
        <div class="o12-field"><label for="fecha_orden">Fecha de la orden</label><input class="form-control" type="datetime-local" id="fecha_orden" name="fecha_orden" value="{{old('fecha_orden',$order->fecha_orden?date('Y-m-d\TH:i',strtotime($order->fecha_orden)):date('Y-m-d\TH:i'))}}" required></div>
        <div class="o12-field"><label for="servicio">Servicio</label><input class="form-control" id="servicio" name="servicio" maxlength="150" value="{{old('servicio',$order->servicio)}}"></div>
        <div class="o12-field o12-field--2"><label for="referido">Persona o institución que refiere</label><input class="form-control" id="referido" name="referido" maxlength="250" value="{{old('referido',$order->referido)}}"></div>
        <div class="o12-field o12-field--2"><label for="texto_otros">Detalle de otros estudios</label><input class="form-control" id="texto_otros" name="texto_otros" maxlength="255" value="{{old('texto_otros',$order->texto_otros)}}"></div>
      </div>
    </section>

    <section class="o12-card">
      <header class="o12-card__head"><i class="fas fa-clipboard-check"></i><h2>Prioridad, estudio y condiciones</h2></header>
      <div class="o12-card__body"><div class="o12-checks">@foreach($checks as $field=>$label)<label class="o12-check"><input type="checkbox" name="{{$field}}" value="1" {{old($field,$order->$field)?'checked':''}}><span>{{$label}}</span></label>@endforeach</div></div>
    </section>

    <section class="o12-card">
      <header class="o12-card__head"><i class="fas fa-notes-medical"></i><h2>Información clínica</h2></header>
      <div class="o12-card__body o12-grid">
        <div class="o12-field o12-field--4"><label for="descripcion">Descripción del estudio solicitado</label><textarea class="form-control" id="descripcion" name="descripcion" maxlength="255">{{old('descripcion',$order->descripcion)}}</textarea></div>
        <div class="o12-field o12-field--2"><label for="motivo">Motivo de la solicitud</label><textarea class="form-control" id="motivo" name="motivo" maxlength="4000">{{old('motivo',$order->motivo)}}</textarea></div>
        <div class="o12-field o12-field--2"><label for="cuadro_clinico">Resumen clínico</label><textarea class="form-control" id="cuadro_clinico" name="cuadro_clinico" maxlength="10000">{{old('cuadro_clinico',strip_tags($order->cuadro_clinico))}}</textarea><span class="o12-help">Se almacena como texto clínico seguro, sin código HTML ejecutable.</span></div>
      </div>
      <footer class="o12-footer"><a class="btn btn-outline-secondary" href="{{route('v3.order012.index')}}">Cancelar</a><button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Guardar cambios</button></footer>
    </section>
  </form>

  <section class="o12-card">
    <header class="o12-card__head"><i class="fas fa-diagnoses"></i><h2>Diagnósticos CIE-10</h2></header>
    <div class="o12-card__body">
      <form method="post" action="{{route('v3.order012.diagnosis.store',array('id'=>$order->id))}}" class="o12-diagnosis-form">
        {{csrf_field()}}
        <div class="o12-field"><label for="cie10_search">Código o diagnóstico</label><input class="form-control" id="cie10_search" list="cie10_options" autocomplete="off" placeholder="Escriba al menos 2 caracteres" required><input type="hidden" name="cie10" id="cie10_code"><datalist id="cie10_options"></datalist></div>
        <div class="o12-field"><label for="tipo">Clasificación</label><select class="form-control" id="tipo" name="tipo" required><option value="">Seleccione</option><option value="PRESUNTIVO">Presuntivo</option><option value="DEFINITIVO">Definitivo</option></select></div>
        <button class="btn btn-outline-primary" type="submit"><i class="fas fa-plus mr-1"></i> Agregar</button>
      </form>
      <div class="table-responsive"><table class="table table-bordered table-hover o12-table"><thead><tr><th>Código</th><th>Descripción</th><th>Clasificación</th><th>Acción</th></tr></thead><tbody>@forelse($diagnoses as $diagnosis)<tr><td><strong>{{$diagnosis->cie10}}</strong></td><td>{{$diagnosis->catalog_description?:'Descripción no disponible'}}</td><td>{{$diagnosis->presuntivo_definitivo}}</td><td><form method="post" action="{{route('v3.order012.diagnosis.destroy',array('diagnosis'=>$diagnosis->id))}}" onsubmit="return confirm('¿Retirar este diagnóstico de la orden?')">{{csrf_field()}}{{method_field('DELETE')}}<button class="btn btn-sm btn-outline-danger" type="submit"><i class="fas fa-trash-alt"></i></button></form></td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">La orden no tiene diagnósticos registrados.</td></tr>@endforelse</tbody></table></div>
    </div>
  </section>
</div>

<script>
(function(){
  var input=document.getElementById('cie10_search'), code=document.getElementById('cie10_code'), list=document.getElementById('cie10_options'), timer=null, options={};
  if(!input)return;
  input.addEventListener('input',function(){
    code.value=''; var value=input.value.trim(); if(options[value]){code.value=options[value];return;} clearTimeout(timer); if(value.length<2){list.innerHTML='';return;}
    timer=setTimeout(function(){
      $.getJSON("{{route('v3.order012.diagnoses')}}",{term:value}).done(function(rows){options={};list.innerHTML='';rows.forEach(function(row){var option=document.createElement('option');option.value=row.text;list.appendChild(option);options[row.text]=row.id;});});
    },250);
  });
  input.form.addEventListener('submit',function(event){if(!code.value&&options[input.value])code.value=options[input.value];if(!code.value){event.preventDefault();alert('Seleccione un diagnóstico válido de la lista.');}});
})();
</script>
