<style>
  .sd-page{padding:14px}.sd-hero{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:14px 18px;margin-bottom:12px;border-radius:14px;color:#fff;background:linear-gradient(115deg,#0b5e89,#138fb4 62%,#16aaa5);box-shadow:0 7px 18px rgba(16,96,128,.12)}
  .sd-title{display:flex;align-items:center;gap:12px;min-width:0}.sd-icon{display:grid;place-items:center;flex:0 0 44px;width:44px;height:44px;border:1px solid rgba(255,255,255,.3);border-radius:12px;background:rgba(255,255,255,.11);font-size:20px}.sd-eyebrow{display:block;margin-bottom:1px;font-size:10px;font-weight:800;letter-spacing:.04em;opacity:.9}.sd-hero h1{margin:0;font-size:20px;line-height:1.2;font-weight:800}.sd-meta{display:flex;flex-wrap:wrap;gap:5px 12px;margin-top:5px;font-size:11px;opacity:.95}.sd-meta span{display:inline-flex;align-items:center;gap:5px}.sd-back{flex:0 0 auto;padding:7px 11px;border:0;border-radius:8px;font-size:12px;font-weight:700}
  .sd-grid{display:grid;grid-template-columns:minmax(0,1.3fr) minmax(310px,.7fr);align-items:start;gap:12px;margin-bottom:12px}.sd-grid:last-child{margin-bottom:0}.sd-card{overflow:hidden;border:1px solid #d4e5ed;border-radius:12px;background:#fff;box-shadow:0 5px 14px rgba(29,76,99,.045)}.sd-head{display:flex;align-items:center;gap:8px;padding:10px 13px;border-bottom:1px solid #d4e5ed;background:#eef9fc;color:#0b3554;font-size:14px;font-weight:800}.sd-head i{width:16px;color:#087fa4;text-align:center}.sd-body{padding:11px}
  .sd-data{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px}.sd-card--patient .sd-data{grid-template-columns:repeat(3,minmax(0,1fr))}.sd-item{min-width:0;padding:8px 9px;border:1px solid #dce8ed;border-radius:8px;background:#fafcfd}.sd-item label{display:block;margin:0 0 2px;color:#718899;font-size:9px;line-height:1.25;font-weight:800;letter-spacing:.02em;text-transform:uppercase}.sd-item strong{display:inline;color:#173f5c;font-size:12px;line-height:1.35;overflow-wrap:anywhere}.sd-span-2{grid-column:span 2}.sd-full{grid-column:1/-1}
  .sd-tag{display:inline-flex;align-items:center;margin:3px 4px 1px 0;padding:4px 7px;border-radius:14px;background:#e4f5f8;color:#087ba2;font-size:10px;line-height:1.2;font-weight:800}.sd-status{display:inline-flex;align-items:center;gap:5px;padding:4px 7px;border-radius:14px;color:#087b69;background:#e0f6f0;font-size:11px;font-weight:700}.sd-note{display:block;margin-top:3px;white-space:pre-wrap;font-weight:400!important}.sd-procedures{display:flex;align-items:flex-start;gap:8px;margin-top:8px;padding-top:8px;border-top:1px dashed #d7e5eb}.sd-procedures__label{flex:0 0 auto;padding-top:4px;color:#718899;font-size:9px;font-weight:800;text-transform:uppercase}.sd-procedures__list{min-width:0}.sd-empty{color:#7890a1;font-size:11px;font-style:italic}
  @media(max-width:1100px){.sd-card--patient .sd-data{grid-template-columns:repeat(2,minmax(0,1fr))}.sd-card--patient .sd-span-2{grid-column:1/-1}}@media(max-width:850px){.sd-grid{grid-template-columns:1fr}.sd-hero{align-items:flex-start}.sd-card--patient .sd-data{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:550px){.sd-page{padding:9px}.sd-hero{align-items:stretch;flex-direction:column;padding:12px}.sd-hero h1{font-size:17px}.sd-back{align-self:flex-start}.sd-data,.sd-card--patient .sd-data{grid-template-columns:1fr}.sd-full,.sd-span-2,.sd-card--patient .sd-span-2{grid-column:auto}.sd-procedures{display:block}.sd-procedures__label{margin-bottom:4px}}
</style>

<?php
  $patient = trim($care->paciente_apellido1.' '.$care->paciente_apellido2.' '.$care->paciente_nombre1.' '.$care->paciente_nombre2);
  $doctor = trim($care->doctor_apellido1.' '.$care->doctor_apellido2.' '.$care->doctor_nombre1);
?>

<div class="sd-page">
  <section class="sd-hero">
    <div class="sd-title"><div class="sd-icon"><i class="fas fa-notes-medical"></i></div><div><small class="sd-eyebrow">ATENCIÓN CLÍNICA #{{$care->id}}</small><h1>{{$patient?:'Paciente no localizado'}}</h1><div class="sd-meta"><span><i class="fas fa-id-card"></i> {{$care->id_paciente}}</span><span><i class="far fa-calendar-alt"></i> {{date('d/m/Y H:i',strtotime($care->fechaini))}}</span></div></div></div>
    <a class="btn btn-light sd-back" href="{{route('v3.scheduled-care.index')}}"><i class="fas fa-arrow-left mr-1"></i> Volver al listado</a>
  </section>

  <div class="sd-grid">
    <section class="sd-card sd-card--patient"><header class="sd-head"><i class="fas fa-user-injured"></i> Paciente y atención</header><div class="sd-body"><div class="sd-data">
      <div class="sd-item sd-span-2"><label>Paciente</label><strong>{{$patient?:'No disponible'}}</strong></div><div class="sd-item"><label>Identificación</label><strong>{{$care->id_paciente}}</strong></div>
      <div class="sd-item"><label>Fecha de nacimiento</label><strong>{{$care->fecha_nacimiento?date('d/m/Y',strtotime($care->fecha_nacimiento)):'No registrada'}}</strong></div><div class="sd-item sd-span-2"><label>Teléfonos</label><strong>{{trim($care->telefono1.' · '.$care->telefono2,' ·')?:'No registrados'}}</strong></div>
      <div class="sd-item sd-full"><label>Dirección</label><strong>{{$care->direccion?:'No registrada'}}</strong></div><div class="sd-item sd-span-2"><label>Doctor</label><strong>{{$doctor?:'Sin asignar'}}</strong> <span class="sd-tag">{{$care->especialidad_nombre?:'Sin especialidad'}}</span></div>
      <div class="sd-item"><label>Estado</label><span class="sd-status"><i class="fas fa-check-circle"></i> {{$care->status_label}}</span></div><div class="sd-item"><label>Seguro</label><strong>{{$care->seguro_nombre?:'No registrado'}}</strong></div><div class="sd-item sd-span-2"><label>Empresa</label><strong>{{$care->empresa_nombre?:'No registrada'}}</strong></div>
    </div></div></section>

    <section class="sd-card"><header class="sd-head"><i class="fas fa-map-marker-alt"></i> Ubicación y trazabilidad</header><div class="sd-body"><div class="sd-data">
      <div class="sd-item"><label>Hospital / unidad</label><strong>{{$care->hospital_nombre?:'Consulta externa'}}</strong></div><div class="sd-item"><label>Sala</label><strong>{{$care->sala_nombre?:'Sin sala'}}</strong></div><div class="sd-item"><label>Historia vinculada</label><strong>{{$care->history_id?:'No creada'}}</strong></div><div class="sd-item"><label>Documentos archivados</label><strong>{{$care->document_count}}</strong></div><div class="sd-item sd-full"><label>Registrado o modificado por</label><strong>{{trim($care->responsable_apellido1.' '.$care->responsable_nombre1)?:'Sistema'}}</strong></div>
    </div></div></section>
  </div>

  <div class="sd-grid">
    <section class="sd-card"><header class="sd-head"><i class="fas fa-procedures"></i> Consulta y procedimientos</header><div class="sd-body">
      <div class="sd-item sd-full"><label>Prestación principal</label><strong>{{$care->procedimiento_nombre?:($care->proc_consul==0?'Consulta médica':'Sin procedimiento registrado')}}</strong>@if($care->procedimiento_observacion)<div class="sd-note">{{$care->procedimiento_observacion}}</div>@endif</div>
      <div class="sd-procedures"><span class="sd-procedures__label">Adicionales</span><div class="sd-procedures__list">@forelse($care->additional_procedures as $procedure)<span class="sd-tag"><i class="fas fa-plus-circle mr-1"></i>{{$procedure->nombre}}</span>@empty<span class="sd-empty">No hay procedimientos adicionales.</span>@endforelse</div></div>
    </div></section>

    <section class="sd-card"><header class="sd-head"><i class="fas fa-heartbeat"></i> Antecedentes y alertas</header><div class="sd-body"><div class="sd-data">
      <div class="sd-item sd-full"><label>Alergias</label><strong class="sd-note">{{$care->alergias?:'Sin alergias registradas'}}</strong></div><div class="sd-item"><label>Patológicos</label><strong class="sd-note">{{$care->antecedentes_pat?:'Sin información'}}</strong></div><div class="sd-item"><label>Familiares</label><strong class="sd-note">{{$care->antecedentes_fam?:'Sin información'}}</strong></div><div class="sd-item sd-full"><label>Quirúrgicos</label><strong class="sd-note">{{$care->antecedentes_quir?:'Sin información'}}</strong></div>
    </div></div></section>
  </div>
</div>
