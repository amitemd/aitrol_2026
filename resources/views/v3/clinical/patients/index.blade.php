<style>
  .vp-page{padding:22px}.vp-hero{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:27px 30px;margin-bottom:18px;border-radius:18px;color:#fff;background:linear-gradient(115deg,#0b5e89,#138fb4 62%,#16aaa5);box-shadow:0 13px 30px rgba(20,91,122,.16)}.vp-hero__main{display:flex;align-items:center;gap:18px}.vp-hero__icon{display:grid;place-items:center;width:62px;height:62px;border:1px solid rgba(255,255,255,.3);border-radius:16px;background:rgba(255,255,255,.11);font-size:27px}.vp-hero h1{margin:2px 0 5px;font-size:28px;font-weight:800}.vp-hero p{margin:0;opacity:.94}.vp-eyebrow{font-size:12px;font-weight:800;letter-spacing:1.2px}.vp-create{border-radius:10px;background:#fff;color:#08749b!important;font-weight:800}
  .vp-card{overflow:visible;margin-bottom:18px;border:1px solid #d4e5ed;border-radius:16px;background:#fff;box-shadow:0 8px 22px rgba(29,76,99,.06)}.vp-head{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:16px 20px;border-bottom:1px solid #d4e5ed;border-radius:16px 16px 0 0;background:#eef9fc}.vp-heading{display:flex;align-items:center;gap:12px}.vp-heading>i{display:grid;place-items:center;width:39px;height:39px;border-radius:11px;color:#0788b7;background:#d9f3f8}.vp-head h2{margin:0;color:#0b3554;font-size:18px;font-weight:800}.vp-head p{margin:2px 0 0;color:#65839a;font-size:13px}.vp-count{padding:6px 10px;border-radius:20px;color:#08799d;background:#e1f6fa;font-size:11px;font-weight:800}.vp-results{transition:opacity .18s ease}.vp-results.is-loading{opacity:.5;pointer-events:none}.vp-ajax-error{margin:0 18px 12px}
  .vp-filters{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;padding:18px 20px}.vp-field label{display:block;margin-bottom:5px;color:#244a63;font-size:11px;font-weight:800;text-transform:uppercase}.vp-field .form-control{height:41px;border-color:#c8dce5;border-radius:8px}.vp-filter-actions{display:flex;grid-column:1/-1;gap:8px}.vp-filter-actions .btn{height:39px;border-radius:8px;font-weight:700}
  .vp-table-wrap{padding:6px 18px 12px;overflow-x:auto}.vp-table{min-width:1330px;margin:0}.vp-table thead th{padding:12px;background:#f1f6f8;color:#173f5c;border-bottom:1px solid #d6e3e9;font-size:11px;text-transform:uppercase}.vp-table td{padding:14px 12px;vertical-align:middle;border-bottom:1px solid #e2ebef;color:#294a61}.vp-identity{display:flex;align-items:center;gap:11px;min-width:250px}.vp-avatar{display:grid;place-items:center;width:43px;height:43px;flex:0 0 43px;border:1px solid #bfe2ec;border-radius:13px;color:#087fba;background:#e6f7fa;font-size:18px}.vp-name{display:block;color:#073f66;font-size:13px;font-weight:800}.vp-meta{display:block;margin-top:3px;color:#7890a1;font-size:11px}.vp-contact{min-width:190px;line-height:1.7}.vp-activity{min-width:150px}.vp-activity strong{display:block;color:#0a725f}.vp-actions{display:flex;align-items:center;gap:7px;min-width:585px}.vp-actions>.btn{border-radius:8px;font-size:11px;font-weight:800;white-space:nowrap}.vp-actions>.btn.disabled{cursor:not-allowed;opacity:.55}.vp-more{position:relative}.vp-more summary{display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border:1px solid #bdd5e2;border-radius:8px;color:#244f6b;background:#f6fafc;font-size:11px;font-weight:800;cursor:pointer;list-style:none}.vp-more summary::-webkit-details-marker{display:none}.vp-menu{position:fixed;right:auto;z-index:10050;width:230px;max-height:calc(100vh - 24px);margin:0;padding:7px;overflow-y:auto;border:1px solid #d3e3ec;border-radius:10px;background:#fff;box-shadow:0 16px 38px rgba(22,59,82,.24)}.vp-menu a{display:flex;align-items:center;gap:8px;padding:9px 10px;border-radius:7px;color:#294b63}.vp-menu a:hover{background:#eef8fb;text-decoration:none}.vp-empty{padding:50px!important;text-align:center;color:#718c9e}.vp-footer{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid #dce8ed;color:#6d8799}.vp-footer .pagination{margin:0}
  @media(max-width:1000px){.vp-filters{grid-template-columns:repeat(2,1fr)}}@media(max-width:650px){.vp-page{padding:12px}.vp-hero{padding:22px 18px;align-items:flex-start;flex-direction:column}.vp-hero h1{font-size:23px}.vp-create{width:100%}.vp-filters{grid-template-columns:1fr}.vp-filter-actions{grid-column:1}.vp-footer{align-items:flex-start;flex-direction:column;gap:10px}}
</style>
<div class="vp-page">
  <section class="vp-hero"><div class="vp-hero__main"><div class="vp-hero__icon"><i class="fas fa-users"></i></div><div><div class="vp-eyebrow">GESTIÓN CLÍNICA · V3</div><h1>Pacientes</h1><p>Localiza al paciente único y accede a su información clínica y documental.</p></div></div><a class="btn vp-create" href="{{route('v3.patients.create')}}"><i class="fas fa-user-plus mr-1"></i> Agregar nuevo paciente</a></section>
  @if(session('patient_success'))<div class="alert alert-success">{{session('patient_success')}}</div>@endif
  @if(session('patient_error'))<div class="alert alert-danger">{{session('patient_error')}}</div>@endif
  <section class="vp-card"><header class="vp-head"><div class="vp-heading"><i class="fas fa-search"></i><div><h2>Buscar pacientes</h2><p>Filtra por identificación, nombres, apellidos o correo electrónico.</p></div></div></header><form id="vp-patient-filters" class="vp-filters" method="get" action="{{route('v3.patients.index')}}"><div class="vp-field"><label>Identificación</label><input class="form-control" name="id" value="{{$filters['id']}}" placeholder="Número de identificación"></div><div class="vp-field"><label>Nombres</label><input class="form-control" name="nombres" value="{{$filters['nombres']}}" placeholder="Nombres del paciente"></div><div class="vp-field"><label>Apellidos</label><input class="form-control" name="apellidos" value="{{$filters['apellidos']}}" placeholder="Apellidos del paciente"></div><div class="vp-field"><label>Correo electrónico</label><input class="form-control" name="correo" value="{{$filters['correo']}}" placeholder="correo@ejemplo.com"></div><div class="vp-filter-actions"><label class="mr-3"><input type="checkbox" name="pendientes_emergencia" value="1" @if(!empty($filters['pendientes_emergencia'])) checked @endif> Datos por completar de emergencia</label><button class="btn btn-primary" type="submit"><i class="fas fa-search mr-1"></i> Buscar pacientes</button><a class="btn btn-outline-secondary js-vp-clear" href="{{route('v3.patients.index')}}"><i class="fas fa-eraser mr-1"></i> Limpiar</a></div></form></section>
  <section id="vp-patient-results" class="vp-card vp-results"><header class="vp-head"><div class="vp-heading"><i class="fas fa-address-card"></i><div><h2>Pacientes registrados</h2><p>El paciente se muestra una sola vez; su actividad corresponde a la empresa activa.</p></div></div><span class="vp-count">{{$patients->total()}} paciente(s)</span></header><div class="vp-table-wrap"><table class="table table-hover vp-table"><thead><tr><th>Paciente</th><th>Contacto</th><th>Registro</th><th>Actividad en empresa</th><th>Gestión clínica</th></tr></thead><tbody>
  @forelse($patients as $patient)
    <?php $secondName=in_array($patient->nombre2,array('(N/A)','N/A'))?'':$patient->nombre2; $secondLast=in_array($patient->apellido2,array('(N/A)','N/A'))?'':$patient->apellido2; $name=trim($patient->apellido1.' '.$secondLast.' '.$patient->nombre1.' '.$secondName); $phones=array_values(array_unique(array_filter(array_map('trim',array($patient->telefono1,$patient->telefono2,$patient->telefono3)),function($phone){return $phone!=='';}))); $phoneText=count($phones)?implode(' / ',$phones):'Sin teléfono'; ?>
    <tr><td><div class="vp-identity"><div class="vp-avatar"><i class="fas fa-user"></i></div><div><span class="vp-name">{{$name?:'Paciente sin nombre'}}</span>@if($patient->registro_emergencia_pendiente)<span class="badge badge-warning">Datos por completar · Emergencia</span>@endif<span class="vp-meta"><i class="far fa-id-card mr-1"></i>{{$patient->id}}</span></div></div></td><td><div class="vp-contact"><span><i class="fas fa-phone text-info mr-1"></i>{{$phoneText}}</span><span class="vp-meta"><i class="far fa-envelope mr-1"></i>{{$patient->patient_email?:'Sin correo registrado'}}</span></div></td><td>{{$patient->created_at?date('d/m/Y',strtotime($patient->created_at)):'Sin fecha'}}<span class="vp-meta"><i class="fas fa-user-md mr-1"></i>{{trim($patient->creator_name.' '.$patient->creator_lastname)?:'Usuario no identificado'}}</span></td><td><div class="vp-activity"><strong>{{$patient->company_care_count}} atención(es)</strong><span class="vp-meta">{{$patient->company_last_care?'Última: '.date('d/m/Y',strtotime($patient->company_last_care)):'Sin atenciones en esta empresa'}}</span></div></td><td><div class="vp-actions"><a class="btn btn-outline-warning" href="{{route('v3.patients.edit',array('patient'=>$patient->id))}}"><i class="fas fa-pencil-alt mr-1"></i> {{$patient->registro_emergencia_pendiente ? 'Completar datos' : 'Editar'}}</a><a class="btn btn-outline-success" href="{{route('paciente.historia',array('id_paciente'=>$patient->id))}}" target="_blank" rel="noopener noreferrer"><i class="fas fa-folder-open mr-1"></i> Historia</a><a class="btn btn-outline-info" href="{{route('paciente.historial_ordenes',array('id_paciente'=>$patient->id))}}"><i class="fas fa-stethoscope mr-1"></i> Procedimientos</a><a class="btn btn-outline-danger" href="{{route('v3.patients.admission-form',array('patient'=>$patient->id))}}" target="_blank" rel="noopener" title="Ver formulario de ingreso de pacientes"><i class="fas fa-file-pdf mr-1"></i> Ingreso PDF</a><details class="vp-more"><summary><i class="fas fa-ellipsis-h"></i> Más</summary><div class="vp-menu"><a href="{{route('paciente.historial_orden_lab',array('id_paciente'=>$patient->id))}}"><i class="fas fa-heartbeat"></i> HumanLabs</a><a href="{{route('paciente.historial_recetas',array('id_paciente'=>$patient->id))}}"><i class="fas fa-prescription"></i> Recetas</a><a href="{{route('paciente.historial_estudios',array('id_paciente'=>$patient->id))}}"><i class="fas fa-file-medical"></i> Estudios</a><a href="{{route('paciente.historial_imagenes',array('id_paciente'=>$patient->id))}}"><i class="fas fa-images"></i> Imágenes</a><a href="{{route('paciente.historial_documentos',array('id_paciente'=>$patient->id))}}"><i class="fas fa-paperclip"></i> Documentos</a><a href="{{route('ingreso.biopsias2',array('id_paciente'=>$patient->id))}}"><i class="fas fa-microscope"></i> Biopsias</a></div></details></div></td></tr>
  @empty<tr><td colspan="5" class="vp-empty"><i class="fas fa-user-slash fa-2x d-block mb-2"></i><strong>No se encontraron pacientes.</strong><br>Modifique los filtros o registre un nuevo paciente.</td></tr>@endforelse
  </tbody></table></div><footer class="vp-footer"><span>Mostrando {{$patients->firstItem()?:0}} a {{$patients->lastItem()?:0}} de {{$patients->total()}}</span>{{$patients->appends($filters)->links()}}</footer></section>
</div>
<script>
(function(){
  var gap=6, edge=12;
  var form=document.getElementById('vp-patient-filters');
  var results=document.getElementById('vp-patient-results');
  var activeRequest=null, requestNumber=0;
  function placeMenu(details){
    if(!details || !details.open)return;
    var trigger=details.querySelector('summary'), menu=details.querySelector('.vp-menu');
    if(!trigger || !menu)return;
    var anchor=trigger.getBoundingClientRect(), width=menu.offsetWidth, height=menu.offsetHeight;
    var left=Math.min(anchor.right-width,window.innerWidth-width-edge);
    left=Math.max(edge,left);
    var below=anchor.bottom+gap;
    var top=below+height<=window.innerHeight-edge?below:Math.max(edge,anchor.top-height-gap);
    menu.style.left=Math.round(left)+'px';
    menu.style.top=Math.round(top)+'px';
  }
  function closeMenus(except){
    document.querySelectorAll('.vp-more[open]').forEach(function(details){
      if(details!==except)details.removeAttribute('open');
    });
  }
  function initMenus(root){
    (root||document).querySelectorAll('.vp-more').forEach(function(details){
      details.addEventListener('toggle',function(){
        if(details.open){closeMenus(details);placeMenu(details);}
      });
    });
  }
  initMenus(document);
  document.addEventListener('click',function(event){
    document.querySelectorAll('.vp-more[open]').forEach(function(details){
      if(!details.contains(event.target))details.removeAttribute('open');
    });
  });
  function placeOpenMenus(){
    document.querySelectorAll('.vp-more[open]').forEach(placeMenu);
  }
  window.addEventListener('resize',placeOpenMenus);
  window.addEventListener('scroll',placeOpenMenus,true);

  function filterUrl(){
    var url=new URL(form.action,window.location.origin);
    new FormData(form).forEach(function(value,key){
      value=String(value).trim();
      if(value!=='')url.searchParams.set(key,value);
    });
    return url;
  }

  function showError(message){
    var previous=results.querySelector('.vp-ajax-error');
    if(previous)previous.remove();
    var alert=document.createElement('div');
    alert.className='alert alert-danger vp-ajax-error';
    alert.setAttribute('role','alert');
    alert.textContent=message;
    var header=results.querySelector('.vp-head');
    if(header)header.insertAdjacentElement('afterend',alert);
  }

  function syncForm(nextForm){
    if(!nextForm)return;
    ['id','nombres','apellidos','correo'].forEach(function(name){
      var current=form.elements[name],next=nextForm.elements[name];
      if(current&&next)current.value=next.value;
    });
    var currentPending=form.elements.pendientes_emergencia;
    var nextPending=nextForm.elements.pendientes_emergencia;
    if(currentPending&&nextPending)currentPending.checked=nextPending.checked;
  }

  function loadResults(url,updateHistory){
    var currentRequest=++requestNumber;
    if(activeRequest)activeRequest.abort();
    activeRequest=window.AbortController?new AbortController():null;
    var submit=form.querySelector('button[type="submit"]');
    results.classList.add('is-loading');
    results.setAttribute('aria-busy','true');
    if(submit)submit.disabled=true;
    return fetch(url.toString(),{
      credentials:'same-origin',
      headers:{Accept:'text/html','X-Requested-With':'XMLHttpRequest'},
      signal:activeRequest?activeRequest.signal:undefined
    }).then(function(response){
      if(!response.ok)throw new Error('No se pudo actualizar la lista de pacientes.');
      return response.text();
    }).then(function(html){
      if(currentRequest!==requestNumber)return;
      var page=new DOMParser().parseFromString(html,'text/html');
      var fresh=page.getElementById('vp-patient-results');
      if(!fresh)throw new Error('La respuesta no contiene el listado de pacientes.');
      results.innerHTML=fresh.innerHTML;
      syncForm(page.getElementById('vp-patient-filters'));
      initMenus(results);
      if(updateHistory)window.history.pushState({},'',url.toString());
    }).catch(function(error){
      if(error.name!=='AbortError')showError(error.message||'No se pudo actualizar la lista de pacientes.');
    }).then(function(){
      if(currentRequest===requestNumber){
        results.classList.remove('is-loading');
        results.removeAttribute('aria-busy');
        if(submit)submit.disabled=false;
      }
    });
  }

  if(form&&results&&window.fetch){
    form.addEventListener('submit',function(event){
      event.preventDefault();
      loadResults(filterUrl(),true);
    });
    form.querySelector('.js-vp-clear').addEventListener('click',function(event){
      event.preventDefault();
      ['id','nombres','apellidos','correo'].forEach(function(name){if(form.elements[name])form.elements[name].value='';});
      if(form.elements.pendientes_emergencia)form.elements.pendientes_emergencia.checked=false;
      loadResults(new URL(form.action,window.location.origin),true);
    });
    results.addEventListener('click',function(event){
      var link=event.target.closest('.pagination a');
      if(!link)return;
      event.preventDefault();
      loadResults(new URL(link.href,window.location.origin),true);
    });
    window.addEventListener('popstate',function(){
      loadResults(new URL(window.location.href),false);
    });
  }
})();
</script>
