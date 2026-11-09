@include('v3.clinical.operations._styles')
@include('v3.clinical.agenda._doctor_styles')
<div class="co-page da-page">
  <section class="co-hero"><div class="co-hero__main"><div class="co-hero__icon"><i class="far fa-clock"></i></div><div><div class="co-eyebrow">DISPONIBILIDAD MÉDICA · V3</div><h1>Horario laboral</h1><p>Dr(a). {{$doctor->display_name}} · Horarios recurrentes y disponibilidades extraordinarias.</p></div></div><a class="btn btn-light" href="{{route('v3.doctor-agenda.calendar',array('doctor'=>$doctor->id,'fecha'=>date('Y-m-d'),'vista'=>'dia'))}}"><i class="fas fa-calendar-alt mr-1"></i> Ver agenda</a></section>
  @if(session('agenda_success'))<div class="alert alert-success da-alert">{{session('agenda_success')}}</div>@endif
  @if($errors->any())<div class="alert alert-danger da-alert">{{$errors->first()}}</div>@endif
  <div class="da-live-message" id="doctorScheduleMessage" role="status" hidden></div>
  <section class="co-card" id="doctorScheduleBoard" data-week-endpoint="{{route('v3.doctor-agenda.schedule-week',array('doctor'=>$doctor->id))}}" aria-live="polite">@include('v3.clinical.agenda._schedule_week')</section>
</div>
<script>
(function(){
var board=document.getElementById('doctorScheduleBoard'),message=document.getElementById('doctorScheduleMessage');if(!board||!window.fetch)return;
function headers(json){var value={'X-Requested-With':'XMLHttpRequest'};if(json)value.Accept='application/json';return value;}
function sweet(options){
  if(typeof window.mostrarSweetAlertV3==='function')return Promise.resolve(window.mostrarSweetAlertV3(options));
  if(window.Swal&&typeof window.Swal.fire==='function')return Promise.resolve(window.Swal.fire(options));
  return Promise.resolve({isConfirmed:!options.showCancelButton||window.confirm(options.text||options.title)});
}
function note(text,type){
  return sweet({
    icon:type==='danger'?'error':'success',
    title:type==='danger'?'No se pudo actualizar el horario':'Horario actualizado',
    text:text,
    confirmButtonText:'Entendido',
    confirmButtonColor:type==='danger'?'#d9534f':'#087fb5'
  });
}
function confirmSchedule(text){
  return sweet({
    icon:'warning',
    title:'¿Retirar este horario?',
    text:text||'El horario dejará de estar disponible para crear nuevas agendas.',
    showCancelButton:true,
    confirmButtonText:'Sí, retirar',
    cancelButtonText:'Conservar horario',
    confirmButtonColor:'#d9534f',
    cancelButtonColor:'#6c8795',
    reverseButtons:true,
    focusCancel:true
  }).then(function(result){return !!(result&&(result.isConfirmed||result.value===true));});
}
function scheduleError(text){
  return sweet({
    icon:'error',
    title:'Horario no disponible',
    text:text||'No se pudo guardar el horario.',
    confirmButtonText:'Entendido',
    confirmButtonColor:'#087fb5',
    allowOutsideClick:false
  });
}
function refresh(fragmentUrl,pageUrl,push){board.classList.add('is-loading');return fetch(fragmentUrl,{credentials:'same-origin',headers:headers(false)}).then(function(response){if(!response.ok)throw new Error('No se pudo actualizar el horario.');return response.text();}).then(function(html){board.innerHTML=html;if(push&&pageUrl)history.pushState({},'',pageUrl);}).catch(function(error){note(error.message,'danger');}).then(function(){board.classList.remove('is-loading');});}
function refreshCurrent(){var params=new URLSearchParams(window.location.search),url=board.getAttribute('data-week-endpoint');return refresh(url+(params.toString()?'?'+params.toString():''),null,false);}
document.addEventListener('click',function(event){var nav=event.target.closest('.js-schedule-nav');if(!nav)return;event.preventDefault();refresh(nav.getAttribute('data-fragment-url'),nav.href,true);});
function resetRecurringEditor(){
  var form=board.querySelector('.js-recurring-editor');if(!form)return;
  form.action=form.getAttribute('data-create-url');
  var method=form.querySelector('.js-recurring-method');if(method)method.remove();
  form.reset();
  var title=board.querySelector('#recurringFormTitle'),help=board.querySelector('#recurringFormHelp'),save=form.querySelector('.js-save-recurring'),cancel=form.querySelector('.js-cancel-recurring-edit');
  if(title)title.innerHTML='<i class="fas fa-plus mr-1"></i> Agregar horario recurrente';
  if(help)help.textContent='Define el rango y la duración de cada tipo de atención.';
  if(save)save.innerHTML='<i class="fas fa-plus mr-1"></i> Agregar rango';
  if(cancel)cancel.hidden=true;
  board.querySelectorAll('.js-edit-recurring').forEach(function(item){item.classList.remove('is-editing');});
}
function editRecurring(slot){
  var form=board.querySelector('.js-recurring-editor');if(!form)return;
  form.action=slot.getAttribute('data-update-url');
  var method=form.querySelector('.js-recurring-method');
  if(!method){method=document.createElement('input');method.type='hidden';method.name='_method';method.className='js-recurring-method';form.appendChild(method);}
  method.value='PATCH';
  form.querySelector('[name="dia"]').value=slot.getAttribute('data-day');
  form.querySelector('[name="tipo"]').value=slot.getAttribute('data-type');
  form.querySelector('[name="hora_ini"]').value=slot.getAttribute('data-start');
  form.querySelector('[name="hora_fin"]').value=slot.getAttribute('data-end');
  form.querySelector('[name="intervalo_consulta"]').value=slot.getAttribute('data-consultation-interval');
  form.querySelector('[name="intervalo_procedimiento"]').value=slot.getAttribute('data-procedure-interval');
  var title=board.querySelector('#recurringFormTitle'),help=board.querySelector('#recurringFormHelp'),save=form.querySelector('.js-save-recurring'),cancel=form.querySelector('.js-cancel-recurring-edit');
  if(title)title.innerHTML='<i class="fas fa-edit mr-1"></i> Modificar horario recurrente';
  if(help)help.textContent='Actualiza el día, las horas o los intervalos del horario seleccionado.';
  if(save)save.innerHTML='<i class="fas fa-save mr-1"></i> Guardar cambios';
  if(cancel)cancel.hidden=false;
  board.querySelectorAll('.js-edit-recurring').forEach(function(item){item.classList.toggle('is-editing',item===slot);});
  form.scrollIntoView({behavior:'smooth',block:'center'});
}
document.addEventListener('click',function(event){
  var cancel=event.target.closest('.js-cancel-recurring-edit');if(cancel){resetRecurringEditor();return;}
  var slot=event.target.closest('.js-edit-recurring');if(!slot||event.target.closest('form,button'))return;
  editRecurring(slot);
});
document.addEventListener('keydown',function(event){var slot=event.target.closest&&event.target.closest('.js-edit-recurring');if(slot&&(event.key==='Enter'||event.key===' ')){event.preventDefault();editRecurring(slot);}});
document.addEventListener('submit',function(event){
  var form=event.target.closest('.js-schedule-form');if(!form)return;event.preventDefault();
  var confirmation=form.getAttribute('data-confirm'),button=form.querySelector('[type="submit"]');
  var approval=confirmation?confirmSchedule(confirmation):Promise.resolve(true);
  approval.then(function(accepted){
    if(!accepted)return;
    if(button)button.disabled=true;
    return fetch(form.action,{method:'POST',credentials:'same-origin',headers:headers(true),body:new FormData(form)})
      .then(function(response){return response.json().then(function(data){if(!response.ok)throw new Error(data.message||'No se pudo guardar el horario.');return data;});})
      .then(function(data){return note(data.message||'El horario se actualizó correctamente.','success').then(refreshCurrent);})
      .catch(function(error){return scheduleError(error.message).then(function(){if(button)button.disabled=false;});});
  });
});
window.addEventListener('popstate',refreshCurrent);
})();
</script>
