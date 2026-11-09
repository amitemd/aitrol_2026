@include('v3.clinical.operations._styles')
@include('v3.clinical.attention._styles')
@include('v3.clinical.agenda._doctor_styles')
@php
  $patientName = trim($patient->apellido1.' '.$patient->apellido2.' '.$patient->nombre1.' '.$patient->nombre2);
  $initials = strtoupper(substr(trim($patient->nombre1),0,1).substr(trim($patient->apellido1),0,1));
  $age = null;
  if (!empty($patient->fecha_nacimiento)) {
      try { $age = (new DateTime(substr($patient->fecha_nacimiento,0,10)))->diff(new DateTime('today'))->y; } catch (Exception $exception) { $age = null; }
  }
  $attentionType = (int)$appointment->proc_consul === 1 ? 'Procedimiento' : ((int)$appointment->proc_consul === 4 ? 'Hospitalaria' : 'Consulta');
  $insuranceName = $appointment->history_insurance_name ?: $appointment->patient_insurance_name;
  $historyReady = !empty($appointment->hcid);
  $doctorReady = !empty($appointment->id_doctor1) && $appointment->doctor_is_valid;
  $civilStatuses = array(1=>'Soltero(a)',2=>'Casado(a)',3=>'Viudo(a)',4=>'Divorciado(a)',5=>'Unión libre',6=>'Unión de hecho');
  $procedureNames = array();
  if (!empty($appointment->primary_procedure_name)) $procedureNames[] = $appointment->primary_procedure_name;
  foreach ($additionalProcedures as $procedure) if (!in_array($procedure->nombre,$procedureNames,true)) $procedureNames[] = $procedure->nombre;
@endphp
<div class="ca3-page">
  <section class="ca3-hero">
    <div class="ca3-hero__main"><div class="ca3-hero__icon"><i class="fas fa-notes-medical"></i></div><div><div class="ca3-eyebrow">HISTORIA CLÍNICA · V3</div><h1>Atención clínica</h1><p>Información clínica, filiación y accesos documentales de una sola atención.</p></div></div>
    <div class="ca3-hero__actions"><a class="btn btn-light" href="{{route('v3.clinical-operations.daily')}}"><i class="fas fa-arrow-left mr-1"></i> Pacientes del día</a>@if($historyReady)<a class="btn btn-outline-light" href="{{route('agenda.detalle',array('id'=>$appointment->id))}}"><i class="fas fa-stethoscope mr-1"></i> Herramientas clínicas</a>@endif</div>
  </section>

  @if(session('clinical_attention_success'))<div class="alert alert-success ca3-alert"><i class="fas fa-check-circle mr-1"></i>{{session('clinical_attention_success')}}</div>@endif
  @if($errors->any())<div class="alert alert-danger ca3-alert"><i class="fas fa-exclamation-circle mr-1"></i>Revisa los campos marcados antes de guardar.<ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul></div>@endif
  @if(!$doctorReady)<div class="alert alert-warning ca3-alert"><i class="fas fa-user-md mr-1"></i>La atención no tiene un profesional con tipo de usuario DOCTOR/DOCTORES. Las acciones dependientes del médico se muestran deshabilitadas.</div>@endif

  <section class="ca3-summary">
    <div class="ca3-summary__patient"><div class="ca3-patient"><div class="ca3-avatar">{{$initials?:'PA'}}</div><div><span>Paciente</span><strong>{{$patientName}}</strong><small>{{$patient->id}} · {{$age!==null?$age.' años':'Edad no disponible'}}</small></div></div></div>
    <div class="ca3-summary__item"><div class="ca3-summary__icon"><i class="far fa-calendar-check"></i></div><div><span>Atención</span><strong>#{{$appointment->id}} · {{$attentionType}}</strong><small>{{date('d/m/Y H:i',strtotime($appointment->fechaini))}}@if(count($procedureNames)) · {{implode(' · ',$procedureNames)}}@endif</small></div></div>
    <div class="ca3-summary__item"><div class="ca3-summary__icon"><i class="fas fa-user-md"></i></div><div><span>Profesional</span><strong>{{$appointment->doctor_name?:'Sin médico asignado'}}</strong><small>{{$appointment->specialty_name?:'Sin especialidad'}}</small></div></div>
    <div class="ca3-summary__item"><div class="ca3-summary__icon"><i class="fas fa-map-marker-alt"></i></div><div><span>Ubicación</span><strong>{{$appointment->room_name?:'Sin sala asignada'}}</strong><small>{{$appointment->hospital_name?:'Sin sede registrada'}}</small></div></div>
  </section>

  <section class="ca3-card">
    <header class="ca3-card__head"><div class="ca3-title"><i class="fas fa-link"></i><div><h2>Acciones de la atención</h2><p>Accesos V3 y módulos clínicos ya disponibles para este paciente.</p></div></div><span class="co-count">{{$courtesies}} cortesía(s) del día</span></header>
    <div class="ca3-card__body"><div class="ca3-actions">
      @if($doctorReady)<a class="ca3-action js-attention-agenda-modal" href="{{route('v3.doctor-agenda.edit',array('doctor'=>$appointment->id_doctor1,'appointment'=>$appointment->id))}}"><i class="fas fa-calendar-check"></i><div><strong>Datos de agenda</strong><small>Horario, sala y atención</small></div></a>@else<div class="ca3-action is-disabled"><i class="fas fa-calendar-check"></i><div><strong>Datos de agenda</strong><small>Médico no disponible</small></div></div>@endif
      @if($doctorReady)<a class="ca3-action" href="{{route('v3.appointment-laboratory-orders.create',array('doctor'=>$appointment->id_doctor1,'appointment'=>$appointment->id))}}"><i class="fas fa-flask"></i><div><strong>Laboratorio</strong><small>{{$laboratoryCount}} orden(es) activa(s)</small></div></a>@else<a class="ca3-action" href="{{route('v3.patient-laboratory-orders.index',array('patient'=>$patient->id))}}"><i class="fas fa-flask"></i><div><strong>Laboratorio</strong><small>{{$laboratoryCount}} orden(es) activa(s)</small></div></a>@endif
      <a class="ca3-action" href="{{route('paciente.historial_ordenes',array('id_paciente'=>$patient->id))}}"><i class="fas fa-file-medical"></i><div><strong>Órdenes</strong><small>Procedimientos del paciente</small></div></a>
      @if($doctorReady)<a class="ca3-action" href="{{route('v3.agenda-documents.index',array('doctor'=>$appointment->id_doctor1,'appointment'=>$appointment->id))}}"><i class="fas fa-folder-open"></i><div><strong>Documentos requeridos</strong><small>Control documental V3</small></div></a>@else<div class="ca3-action is-disabled"><i class="fas fa-folder-open"></i><div><strong>Documentos requeridos</strong><small>Médico no disponible</small></div></div>@endif
      @if($historyReady && (int)$appointment->proc_consul===0)<a class="ca3-action" target="_blank" href="{{route('formato.anamnesis_crear',array('id'=>$appointment->hcid))}}"><i class="fas fa-clipboard-list"></i><div><strong>Anamnesis</strong><small>Formulario clínico</small></div></a>@else<div class="ca3-action is-disabled"><i class="fas fa-clipboard-list"></i><div><strong>Anamnesis</strong><small>Disponible en consultas admitidas</small></div></div>@endif
    </div></div>
  </section>

  <form class="ca3-card" method="post" action="{{route('v3.clinical-attention.update',array('patient'=>$patient->id,'agenda'=>$appointment->id))}}">
    {{csrf_field()}}{{method_field('PATCH')}}
    <header class="ca3-card__head"><div class="ca3-title"><i class="fas fa-user-injured"></i><div><h2>Filiación clínica</h2><p>Datos actuales del paciente utilizados en la atención y sus documentos.</p></div></div><span class="co-count">{{$insuranceName?:'Sin seguro'}}</span></header>
    <div class="ca3-card__body"><div class="ca3-grid">
      <div class="ca3-form-divider"><i class="fas fa-id-card"></i><div><strong>Datos personales</strong><small>Identificación y datos generales del paciente.</small></div></div>
      <div class="ca3-field"><label>Cortesía</label><select class="form-control" name="cortesia"><option value="NO" {{old('cortesia',$appointment->cortesia)==='NO'?'selected':''}}>No</option><option value="SI" {{old('cortesia',$appointment->cortesia)==='SI'?'selected':''}}>Sí</option></select></div>
      <div class="ca3-field"><label>Sexo</label><select class="form-control" name="sexo" required><option value="1" {{(string)old('sexo',$patient->sexo)==='1'?'selected':''}}>Masculino</option><option value="2" {{(string)old('sexo',$patient->sexo)==='2'?'selected':''}}>Femenino</option></select></div>
      <div class="ca3-field"><label>Fecha de nacimiento</label><input class="form-control" type="date" name="fecha_nacimiento" value="{{old('fecha_nacimiento',$patient->fecha_nacimiento)}}" required></div>
      <div class="ca3-field"><label>Edad</label><input class="form-control" value="{{$age!==null?$age.' años':'No disponible'}}" readonly></div>
      <div class="ca3-field"><label>Estado civil</label><select class="form-control" name="estadocivil"><option value="">No registrado</option>@foreach($civilStatuses as $value=>$label)<option value="{{$value}}" {{(string)old('estadocivil',$patient->estadocivil)===(string)$value?'selected':''}}>{{$label}}</option>@endforeach</select></div>
      <div class="ca3-field"><label>Ocupación</label><input class="form-control" name="ocupacion" maxlength="100" value="{{old('ocupacion',$patient->ocupacion)}}"></div>
      <div class="ca3-field"><label>Trabajo</label><input class="form-control" name="trabajo" maxlength="150" value="{{old('trabajo',$patient->trabajo)}}"></div>
      <div class="ca3-field"><label>Seguro</label><input class="form-control" value="{{$insuranceName?:'Sin seguro registrado'}}" readonly></div>
      <div class="ca3-form-divider"><i class="fas fa-address-book"></i><div><strong>Contacto y ubicación</strong><small>Información de localización y medios de contacto.</small></div></div>
      <div class="ca3-field"><label>Ciudad de procedencia</label><input class="form-control" name="ciudad" maxlength="150" value="{{old('ciudad',$patient->ciudad)}}"></div>
      <div class="ca3-field"><label>Ciudad de nacimiento</label><input class="form-control" name="lugar_nacimiento" maxlength="150" value="{{old('lugar_nacimiento',$patient->lugar_nacimiento)}}"></div>
      <div class="ca3-field ca3-span-2"><label>Dirección domiciliaria</label><input class="form-control" name="direccion" maxlength="255" value="{{old('direccion',$patient->direccion)}}"></div>
      <div class="ca3-field"><label>Teléfono</label><input class="form-control" name="telefono1" maxlength="60" value="{{old('telefono1',$patient->telefono1)}}" required></div>
      <div class="ca3-field"><label>Celular alterno</label><input class="form-control" name="telefono2" maxlength="60" value="{{old('telefono2',$patient->telefono2)}}"></div>
      <div class="ca3-field"><label>Correo</label><input class="form-control" type="email" name="mail_opcional" maxlength="191" value="{{old('mail_opcional',$patient->mail_opcional)}}"></div>
      <div class="ca3-field"><label>Referencia</label><input class="form-control" name="referido" maxlength="150" value="{{old('referido',$patient->referido)}}"></div>
      <div class="ca3-form-divider"><i class="fas fa-heartbeat"></i><div><strong>Información clínica</strong><small>Observaciones, antecedentes y factores relevantes para la atención.</small></div></div>
      <div class="ca3-field ca3-span-2"><label>Observación médica</label><textarea class="form-control" name="observacion" maxlength="1000">{{old('observacion',$patient->observacion)}}</textarea></div>
      <div class="ca3-field ca3-span-2"><label>Observación administrativa</label><textarea class="form-control" name="observacion_admin" maxlength="3000">{{old('observacion_admin',$administrativeObservation)}}</textarea></div>
      <div class="ca3-field"><label>Grupo sanguíneo</label><input class="form-control" name="gruposanguineo" maxlength="20" value="{{old('gruposanguineo',$patient->gruposanguineo)}}" placeholder="Ej. O+"></div>
      <div class="ca3-field"><label>Transfusiones</label><select class="form-control" name="transfusion"><option value="NO" {{old('transfusion',$patient->transfusion?:'NO')==='NO'?'selected':''}}>No</option><option value="SI" {{old('transfusion',$patient->transfusion)==='SI'?'selected':''}}>Sí</option></select></div>
      <div class="ca3-field ca3-span-2"><label>Hábitos</label><input class="form-control" name="alcohol" maxlength="150" value="{{old('alcohol',$patient->alcohol)}}"></div>
      <div class="ca3-field ca3-span-2"><label>Alergias</label><select id="clinicalAllergies" class="form-control" name="ale_list[]" multiple>@foreach($allergies as $allergy)<option value="{{$allergy->id}}" selected>{{$allergy->nombre}}</option>@endforeach</select></div>
      <div class="ca3-field ca3-span-2"><label>Vacunas</label><textarea class="form-control" name="vacuna" maxlength="1000">{{old('vacuna',$patient->vacuna)}}</textarea></div>
      <div class="ca3-field ca3-span-2"><label>Antecedentes patológicos</label><textarea class="form-control" name="antecedentes_pat" maxlength="3000">{{old('antecedentes_pat',$patient->antecedentes_pat)}}</textarea></div>
      <div class="ca3-field ca3-span-2"><label>Antecedentes familiares</label><textarea class="form-control" name="antecedentes_fam" maxlength="3000">{{old('antecedentes_fam',$patient->antecedentes_fam)}}</textarea></div>
      <div class="ca3-field ca3-span-2"><label>Antecedentes quirúrgicos</label><textarea class="form-control" name="antecedentes_quir" maxlength="3000">{{old('antecedentes_quir',$patient->antecedentes_quir)}}</textarea></div>
    </div></div>
    <footer class="ca3-form__footer"><small><i class="fas fa-shield-alt mr-1"></i>Los cambios quedan registrados con usuario, fecha e IP.</small><button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Guardar filiación</button></footer>
  </form>

  <section class="ca3-card">
    <header class="ca3-card__head"><div class="ca3-title"><i class="fas fa-folder-tree"></i><div><h2>Historia y archivos clínicos</h2><p>Resumen del expediente con accesos a los módulos especializados.</p></div></div></header>
    <div class="ca3-card__body"><div class="ca3-modules">
      <article class="ca3-module"><div class="ca3-module__head"><strong><i class="fas fa-paperclip mr-1"></i>Documentos de agenda</strong><span>{{count($agendaDocuments)}}</span></div><div class="ca3-module__body">@if(count($agendaDocuments))@foreach($agendaDocuments as $document)<div class="ca3-row"><div><strong>{{$document->archivo?:($document->tipo_documento?:'Documento de agenda')}}</strong><small>{{date('d/m/Y H:i',strtotime($document->created_at))}} · Atención #{{$document->appointment_id}}</small></div></div>@endforeach @else<div class="ca3-empty">No hay documentos de agenda registrados.</div>@endif</div><div class="ca3-module__foot"><a class="btn btn-outline-primary" href="{{route('v3.patient-agenda-archives.index',array('patient'=>$patient->id))}}">Ver documentos</a></div></article>
      <article class="ca3-module"><div class="ca3-module__head"><strong><i class="fas fa-vial mr-1"></i>Exámenes externos</strong><span>{{count($externalExams)}}</span></div><div class="ca3-module__body">@if(count($externalExams))@foreach($externalExams as $exam)<div class="ca3-row"><div><strong>{{$exam->nombre_anterior?:$exam->nombre}}</strong><small>{{date('d/m/Y H:i',strtotime($exam->created_at))}}</small></div><a class="btn btn-sm btn-outline-primary" target="_blank" href="{{url('laboratorio_externo_descarga/'.$exam->id)}}"><i class="fas fa-download"></i></a></div>@endforeach @else<div class="ca3-empty">No hay exámenes externos registrados.</div>@endif</div></article>
      <article class="ca3-module"><div class="ca3-module__head"><strong><i class="fas fa-notes-medical mr-1"></i>Evoluciones y visitas</strong><span>{{count($evolutions)}}</span></div><div class="ca3-module__body">@if(count($evolutions))@foreach($evolutions as $evolution)<div class="ca3-row"><div><strong>{{date('d/m/Y H:i',strtotime($evolution->fechaini))}} · {{$evolution->specialty_name?:'Atención clínica'}}</strong><small>{{trim($evolution->doctor_lastname.' '.$evolution->doctor_name)?:'Profesional no registrado'}}</small></div></div>@endforeach @else<div class="ca3-empty">No hay evoluciones registradas.</div>@endif</div></article>
      <article class="ca3-module"><div class="ca3-module__head"><strong><i class="fas fa-procedures mr-1"></i>Protocolos y procedimientos</strong><span>{{count($protocols)}}</span></div><div class="ca3-module__body">@if(count($protocols))@foreach($protocols as $protocol)<div class="ca3-row"><div><strong>{{$protocol->procedure_name?:'Procedimiento clínico'}}</strong><small>{{date('d/m/Y H:i',strtotime($protocol->created_at))}} · HC {{$protocol->hcid}}</small></div></div>@endforeach @else<div class="ca3-empty">No hay protocolos registrados.</div>@endif</div></article>
      <article class="ca3-module"><div class="ca3-module__head"><strong><i class="fas fa-prescription-bottle-alt mr-1"></i>Historial de recetas</strong><span>{{count($prescriptions)}}</span></div><div class="ca3-module__body">@if(count($prescriptions))@foreach($prescriptions as $prescription)<div class="ca3-row"><div><strong>{{date('d/m/Y H:i',strtotime($prescription->fechaini?:$prescription->created_at))}}</strong><small>{{trim($prescription->doctor_lastname.' '.$prescription->doctor_name)?:'Profesional no registrado'}}</small></div></div>@endforeach @else<div class="ca3-empty">No hay recetas registradas.</div>@endif</div><div class="ca3-module__foot"><a class="btn btn-outline-primary" href="{{route('paciente.historial_recetas',array('id_paciente'=>$patient->id))}}">Ver historial</a></div></article>
      <article class="ca3-module"><div class="ca3-module__head"><strong><i class="fas fa-photo-video mr-1"></i>Archivos clínicos</strong><span>{{$clinicalFiles['images']+$clinicalFiles['documents']+$clinicalFiles['studies']}}</span></div><div class="ca3-module__body"><div class="ca3-row"><div><strong>Imágenes</strong><small>{{$clinicalFiles['images']}} archivo(s)</small></div><a class="btn btn-sm btn-outline-primary" href="{{route('paciente.historial_imagenes',array('id_paciente'=>$patient->id))}}">Abrir</a></div><div class="ca3-row"><div><strong>Documentos</strong><small>{{$clinicalFiles['documents']}} archivo(s)</small></div><a class="btn btn-sm btn-outline-primary" href="{{route('paciente.historial_documentos',array('id_paciente'=>$patient->id))}}">Abrir</a></div><div class="ca3-row"><div><strong>Estudios</strong><small>{{$clinicalFiles['studies']}} archivo(s)</small></div><a class="btn btn-sm btn-outline-primary" href="{{route('paciente.historial_estudios',array('id_paciente'=>$patient->id))}}">Abrir</a></div></div></article>
    </div></div>
  </section>
</div>
<div class="modal fade" id="clinicalAgendaModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl da-agenda-modal" role="document">
    <div class="modal-content" id="clinicalAgendaModalContent"></div>
  </div>
</div>
<script>
(function(window,document){
  var attempts=0;
  function initClinicalAllergies(){
    var $=window.jQuery;
    if(!$||!$.fn||!$.fn.select2){
      if(attempts++<40)window.setTimeout(initClinicalAllergies,100);
      return;
    }
    var select=$('#clinicalAllergies');
    if(!select.length||select.hasClass('select2-hidden-accessible'))return;
    select.select2({
      theme:'bootstrap4',width:'100%',placeholder:'Buscar alergias o principios activos',minimumInputLength:2,
      ajax:{url:'{{route('v3.patients.allergies.search')}}',dataType:'json',delay:300,cache:true,data:function(params){return {q:params.term||''};},processResults:function(data){return {results:data||[]};}},
      language:{inputTooShort:function(){return'Escribe al menos 2 caracteres';},searching:function(){return'Buscando…';},noResults:function(){return'No se encontraron resultados';},errorLoading:function(){return'No se pudo cargar la búsqueda';}}
    });
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',initClinicalAllergies);
  else initClinicalAllergies();
})(window,document);
</script>
<script>
(function(window,document){
  var modal=document.getElementById('clinicalAgendaModal'),content=document.getElementById('clinicalAgendaModalContent');
  if(!modal||!content||!window.fetch)return;

  function escapeHtml(value){var node=document.createElement('div');node.textContent=value||'';return node.innerHTML;}
  function requestHeaders(json){var headers={'X-Requested-With':'XMLHttpRequest'};if(json)headers.Accept='application/json';return headers;}
  function setError(message){
    var box=content.querySelector('.da-modal-error');
    if(box){box.textContent=message;box.hidden=false;box.scrollIntoView({behavior:'smooth',block:'center'});return;}
    content.innerHTML='<div class="modal-body"><div class="alert alert-danger mb-0">'+escapeHtml(message)+'</div></div>';
  }
  function errorMessage(data,fallback){
    if(data&&data.errors){for(var key in data.errors){if(data.errors.hasOwnProperty(key)){var value=data.errors[key];return Array.isArray(value)?value[0]:value;}}}
    return data&&data.message?data.message:fallback;
  }
  function initAgendaFields(){
    var $=window.jQuery;
    if($&&$.fn&&$.fn.select2){
      $(content).find('.js-activity-form select').each(function(){
        var select=$(this),searchUrl=select.attr('data-search-url'),options={theme:'default',width:'100%',dropdownParent:$(modal),minimumResultsForSearch:0};
        if(select.hasClass('select2-hidden-accessible'))return;
        if(searchUrl){
          options.minimumInputLength=3;options.allowClear=true;options.placeholder=select.find('option[value=""]').text();
          options.ajax={url:searchUrl,dataType:'json',delay:250,cache:true,data:function(params){return{q:params.term||''};},processResults:function(data){return{results:data.results||[]};}};
        }
        select.select2(options);
      });
    }
    var groups=content.querySelectorAll('.da-tool-group');
    for(var index=0;index<groups.length;index++)groups[index].addEventListener('toggle',function(){
      if(!this.open)return;var siblings=this.parentNode.querySelectorAll('.da-tool-group');
      for(var item=0;item<siblings.length;item++)if(siblings[item]!==this)siblings[item].open=false;
    });
  }
  function openAgenda(url){
    content.innerHTML='<div class="da-modal-loading"><span class="da-spinner"></span><strong>Cargando agenda…</strong></div>';
    if(window.jQuery)window.jQuery(modal).modal('show');
    fetch(url,{credentials:'same-origin',headers:requestHeaders(false)}).then(function(response){
      return response.text().then(function(body){if(!response.ok)throw new Error('No se pudo abrir la agenda.');return body;});
    }).then(function(html){content.innerHTML=html;initAgendaFields();}).catch(function(error){setError(error.message);});
  }
  document.addEventListener('click',function(event){
    var trigger=event.target.closest('.js-attention-agenda-modal');
    if(!trigger)return;
    event.preventDefault();openAgenda(trigger.href);
  });
  content.addEventListener('submit',function(event){
    var form=event.target.closest('.js-activity-form');if(!form)return;
    event.preventDefault();
    var submit=form.querySelector('.js-save-activity'),error=form.querySelector('.da-modal-error');
    if(submit)submit.disabled=true;if(error)error.hidden=true;
    fetch(form.action,{method:'POST',credentials:'same-origin',headers:requestHeaders(true),body:new FormData(form)}).then(function(response){
      return response.text().then(function(body){var data={};try{data=body?JSON.parse(body):{};}catch(ignore){}if(!response.ok)throw new Error(errorMessage(data,'No se pudo guardar la agenda.'));return data;});
    }).then(function(){
      if(window.jQuery)window.jQuery(modal).modal('hide');window.location.reload();
    }).catch(function(error){setError(error.message);if(submit)submit.disabled=false;});
  });
  content.addEventListener('click',function(event){
    var cancel=event.target.closest('.js-cancel-activity');if(!cancel)return;
    event.preventDefault();
    var reason=window.prompt('Ingrese el motivo de cancelación de la agenda:');if(reason===null)return;
    reason=String(reason).trim();if(!reason){setError('Ingrese el motivo de cancelación.');return;}
    var form=cancel.closest('form'),token=form?form.querySelector('[name="_token"]'):null,data=new FormData();
    data.append('_method','DELETE');data.append('observacion_cancelacion',reason);if(token)data.append('_token',token.value);cancel.disabled=true;
    fetch(cancel.getAttribute('data-cancel-url'),{method:'POST',credentials:'same-origin',headers:requestHeaders(true),body:data}).then(function(response){return response.json().then(function(json){if(!response.ok)throw new Error(errorMessage(json,'No se pudo cancelar la agenda.'));return json;});}).then(function(){if(window.jQuery)window.jQuery(modal).modal('hide');window.location.reload();}).catch(function(error){setError(error.message);cancel.disabled=false;});
  });
  if(window.jQuery)window.jQuery(modal).on('hidden.bs.modal',function(){content.innerHTML='';});
})(window,document);
</script>
