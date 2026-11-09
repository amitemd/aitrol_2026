@include('v3.clinical.registry._styles')
<style>
  .vb-patient{display:flex;align-items:center;gap:14px}.vb-avatar{width:52px;height:52px;border-radius:16px;background:#e4f6fb;color:#087fac;display:grid;place-items:center;font-size:21px}.vb-patient h2{margin:0;color:#063b63;font-size:20px}.vb-patient p{margin:3px 0 0;color:#607d91}
  .vb-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;padding:20px}.vb-file{min-width:0;overflow:hidden;border:1px solid #cfe1eb;border-radius:13px;background:#fff;transition:transform .2s ease,box-shadow .2s ease}.vb-file:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(32,88,120,.11)}.vb-preview{position:relative;height:145px;display:flex;align-items:center;justify-content:center;padding:12px;overflow:hidden;background:#f4fafc;color:#e4474f;text-decoration:none;cursor:pointer}.vb-preview img{max-width:100%;max-height:121px;object-fit:contain}.vb-extension-icon{position:relative;display:flex;width:76px;height:88px;align-items:center;justify-content:center;border:1px solid #d5e6ec;border-radius:12px;background:#fff;box-shadow:0 8px 20px rgba(24,74,99,.10);font-size:38px}.vb-extension-icon.is-image{color:#16a078}.vb-extension-icon.is-office{color:#2877bb}.vb-extension-icon.is-text{color:#64798a}.vb-extension-badge{position:absolute;right:-8px;bottom:7px;min-width:38px;padding:3px 6px;border-radius:8px;background:#173f55;color:#fff;font-size:9px;font-weight:800;letter-spacing:.5px;text-align:center;text-transform:uppercase}.vb-preview:hover .vb-extension-icon{transform:translateY(-2px);box-shadow:0 12px 24px rgba(24,74,99,.16)}.vb-preview-copy{position:absolute;right:0;bottom:8px;left:0;color:#688296;font-size:10px;font-weight:700;text-align:center}.vb-info{padding:13px 14px}.vb-file-name{display:block;min-height:34px;overflow:hidden;color:#083e65;font-size:12px;font-weight:700;line-height:1.4;overflow-wrap:anywhere}.vb-file-name:hover{color:#087fac;text-decoration:none}.vb-info span{display:block;margin-top:5px;color:#688296;font-size:11px}.vb-actions{display:flex;gap:8px;padding:0 14px 14px}.vb-actions .btn,.vb-actions form{flex:1}.vb-actions form{display:flex;margin:0}.vb-actions form .btn{width:100%}.vb-actions .btn{padding:8px 10px;border-radius:8px;font-size:11px;font-weight:700}.vb-section-title{padding:18px 20px 0;color:#073d64;font-size:15px;font-weight:700}@media(max-width:1100px){.vb-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}@media(max-width:780px){.vb-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:520px){.vb-grid{grid-template-columns:1fr}}

  .vb-upload{padding:24px}.vb-drop{position:relative;display:flex;min-height:230px;align-items:center;justify-content:center;padding:30px;border:2px dashed #8bc8da;border-radius:16px;background:linear-gradient(145deg,#f8fdfe,#eef9fc);cursor:pointer;transition:border-color .18s,background .18s,box-shadow .18s,transform .18s}.vb-drop:hover,.vb-drop:focus,.vb-drop.is-dragging{border-color:#079bc0;outline:0;background:#edfafd;box-shadow:0 0 0 4px rgba(7,155,192,.09);transform:translateY(-1px)}.vb-drop .vb-drop__content{display:flex;max-width:560px;align-items:center;flex-direction:column;margin:0;color:inherit}.vb-drop .vb-drop__icon{display:grid;width:66px;height:66px;margin:0;place-items:center;border:1px solid #ccebf2;border-radius:20px;color:#078eb7;background:#fff;box-shadow:0 10px 24px rgba(8,104,140,.12)}.vb-drop .vb-drop__icon i{font-size:28px}.vb-drop strong{margin-top:16px;color:#073d64;font-size:17px;font-weight:800}.vb-drop span{margin:5px 0 16px;color:#678296;font-size:12px}.vb-drop .vb-select-button{display:inline-flex;align-items:center;gap:7px;margin:0;padding:10px 16px;border-radius:9px;color:#fff;background:#078fc1;font-size:12px;font-weight:800;box-shadow:0 5px 13px rgba(7,143,193,.18)}.vb-drop .vb-select-button i{color:inherit;font-size:11px}.vb-drop small{margin-top:13px;color:#78919f;font-size:10px}.vb-drop input.vb-file-input{position:absolute;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0,0,0,0);border:0;white-space:nowrap}
  .vb-selected{margin-top:15px;padding:15px;border:1px solid #d3e7ee;border-radius:13px;background:#fff}.vb-selected[hidden]{display:none}.vb-selected__head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}.vb-selected__head strong{color:#173f58;font-size:12px}.vb-selected__head button{padding:3px 7px;border:0;color:#688596;background:transparent;font-size:11px}.vb-selected__head button:hover{color:#bd3e4d}.vb-selected__list{display:grid;margin:0;padding:0;gap:8px;list-style:none}.vb-selected__item{display:flex;min-width:0;align-items:center;gap:10px;padding:9px 10px;border:1px solid #e0ebef;border-radius:10px;background:#f9fcfd}.vb-selected__item.is-invalid{border-color:#efc3c8;background:#fff7f8}.vb-selected__file-icon{display:grid;flex:0 0 35px;height:35px;place-items:center;border-radius:9px;color:#078eb7;background:#e3f5f9;font-size:16px}.vb-selected__item.is-invalid .vb-selected__file-icon{color:#c63d4b;background:#fde8ea}.vb-selected__copy{display:flex;min-width:0;flex:1;flex-direction:column}.vb-selected__copy strong{overflow:hidden;color:#244d64;font-size:11px;text-overflow:ellipsis;white-space:nowrap}.vb-selected__copy small{margin-top:2px;color:#78909e;font-size:9px}.vb-selected__item.is-invalid .vb-selected__copy small{color:#bd3645}.vb-selected__remove{display:grid;flex:0 0 28px;height:28px;place-items:center;border:0;border-radius:8px;color:#78909e;background:transparent}.vb-selected__remove:hover{color:#bd3645;background:#fdebed}
  .vb-upload__footer{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-top:16px;padding-top:16px;border-top:1px solid #e2edf1}.vb-upload__note{display:flex;align-items:center;gap:9px;color:#617f90;font-size:10px}.vb-upload__note i{display:grid;width:30px;height:30px;place-items:center;border-radius:9px;color:#168763;background:#e5f6ef;font-size:13px}.vb-upload__footer .btn{min-width:155px;padding:9px 17px;border:0;border-radius:9px;font-size:12px;font-weight:800}.vb-upload__footer .btn[disabled]{cursor:not-allowed;opacity:.55}.vb-upload__footer .btn.is-loading{pointer-events:none}
  @media(max-width:600px){.vb-upload{padding:16px}.vb-drop{min-height:210px;padding:22px 15px}.vb-upload__footer{align-items:stretch;flex-direction:column}.vb-upload__footer .btn{width:100%}}
</style>
<?php
  $patientName=trim($patient->apellido1.' '.$patient->apellido2.' '.$patient->nombre1.' '.$patient->nombre2);
  $fileIcon=function($name){$ext=strtolower(pathinfo($name,PATHINFO_EXTENSION));return $ext==='pdf'?'fa-file-pdf':(in_array($ext,array('doc','docx'),true)?'fa-file-word':(in_array($ext,array('xls','xlsx'),true)?'fa-file-excel':'fa-file-alt'));};
  $isImage=function($name){return in_array(strtolower(pathinfo($name,PATHINFO_EXTENSION)),array('jpg','jpeg','png','gif','webp'),true);};
?>
<div class="co-page">
  <section class="co-hero"><div class="co-hero__main"><div class="co-hero__icon"><i class="fas fa-microscope"></i></div><div><div class="co-eyebrow">HISTORIA CLÍNICA · V3</div><h1>Archivos de biopsias</h1><p>Carga, consulta y descarga documentos vinculados al paciente.</p></div></div>@if($backUrl)<a class="btn btn-outline-light" href="{{$backUrl}}"><i class="fas fa-arrow-left mr-1"></i> Volver</a>@else<button class="btn btn-outline-light" type="button" onclick="window.history.back()"><i class="fas fa-arrow-left mr-1"></i> Volver</button>@endif</section>
  @if(session('success'))<div class="alert alert-success"><i class="fas fa-check-circle mr-1"></i>{{session('success')}}</div>@endif
  @if($errors->any())<div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-1"></i>{{$errors->first()}}</div>@endif
  <section class="co-card"><header class="co-head"><div class="vb-patient"><div class="vb-avatar"><i class="fas fa-user"></i></div><div><h2>{{$patientName?:'Paciente sin nombre registrado'}}</h2><p>Identificación: {{$patient->id}} · {{$uploaded->total()+$clinical->total()}} archivo(s)</p></div></div></header></section>
  <section class="co-card">
    <header class="co-head"><div class="co-heading"><i class="fas fa-cloud-upload-alt"></i><div><h2>Cargar biopsias</h2><p>Adjunta uno o varios resultados de forma segura al historial del paciente.</p></div></div></header>
    <form id="biopsyUploadForm" class="vb-upload" method="post" action="{{route('ingreso_biopsias.guardar',array_filter(array('volver'=>$backUrl)))}}" enctype="multipart/form-data">
      {{csrf_field()}}<input type="hidden" name="id_paciente" value="{{$patient->id}}">
      <label id="biopsyDropZone" class="vb-drop" for="biopsyFiles" tabindex="0">
        <span class="vb-drop__content">
          <span class="vb-drop__icon"><i class="fas fa-cloud-upload-alt" aria-hidden="true"></i></span>
          <strong>Arrastra aquí los archivos de biopsia</strong>
          <span>También puedes buscarlos directamente en tu equipo.</span>
          <span class="vb-select-button"><i class="fas fa-folder-open" aria-hidden="true"></i> Seleccionar archivos</span>
          <small id="biopsyFileHelp">PDF, Word, Excel, TXT o imágenes · máximo 20 MB por archivo</small>
        </span>
        <input id="biopsyFiles" class="vb-file-input" type="file" name="foto[]" multiple required aria-describedby="biopsyFileHelp" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
      </label>
      <section id="biopsySelectedFiles" class="vb-selected" aria-live="polite" hidden>
        <div class="vb-selected__head"><strong><span id="biopsySelectedCount">0</span> archivo(s) seleccionado(s)</strong><button id="biopsyClearFiles" type="button"><i class="fas fa-times mr-1"></i> Quitar todos</button></div>
        <ul id="biopsySelectedList" class="vb-selected__list"></ul>
      </section>
      <footer class="vb-upload__footer">
        <div class="vb-upload__note"><i class="fas fa-shield-alt" aria-hidden="true"></i><span>Los documentos se vincularán únicamente a <strong>{{$patientName?:$patient->id}}</strong>.</span></div>
        <button id="biopsyUploadButton" class="btn btn-primary" type="submit"><i class="fas fa-upload mr-1"></i> Cargar archivos</button>
      </footer>
    </form>
  </section>
  <section class="co-card"><header class="co-head"><div class="co-heading"><i class="fas fa-folder-open"></i><div><h2>Historial de biopsias</h2><p>Archivos directos y documentos generados desde protocolos clínicos.</p></div></div><span class="co-count">{{$uploaded->total()+$clinical->total()}} archivo(s)</span></header>
    <div class="vb-section-title">Archivos cargados al paciente</div><div class="vb-grid">@forelse($uploaded as $file)@php $extension=strtolower(pathinfo($file->nombre,PATHINFO_EXTENSION));$iconClass=$isImage($file->nombre)?'is-image':(in_array($extension,array('doc','docx','xls','xlsx'),true)?'is-office':($extension==='txt'?'is-text':''));$previewUrl=route('laboratorio_externo.archivo',array('id'=>$file->id));@endphp<article class="vb-file"><a class="vb-preview" target="_blank" rel="noopener" href="{{$previewUrl}}">@if($isImage($file->nombre))<img loading="lazy" src="{{asset('hc_ima/'.$file->nombre)}}" alt="Biopsia">@else<span class="vb-extension-icon {{$iconClass}}"><i class="fas {{$fileIcon($file->nombre)}}"></i><span class="vb-extension-badge">{{$extension?:'FILE'}}</span></span>@endif<span class="vb-preview-copy"><i class="fas fa-eye"></i> Ver documento</span></a><div class="vb-info"><a class="vb-file-name" target="_blank" rel="noopener" href="{{$previewUrl}}" title="{{$file->nombre}}">{{$file->nombre}}</a><span><i class="far fa-calendar mr-1"></i>{{$file->created_at?date('d/m/Y H:i',strtotime($file->created_at)):'Fecha no registrada'}}</span></div><div class="vb-actions"><a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="{{url('laboratorio_externo_descarga/'.$file->id)}}"><i class="fas fa-download mr-1"></i> Descargar</a><form method="post" action="{{route('eliminar.biopsia',array('id'=>$file->id))}}" onsubmit="return confirm('¿Eliminar este archivo?')">{{csrf_field()}}{{method_field('DELETE')}}<button class="btn btn-outline-danger btn-sm" type="submit"><i class="fas fa-trash mr-1"></i> Eliminar</button></form></div></article>@empty<div class="co-empty" style="grid-column:1/-1">No hay archivos cargados directamente.</div>@endforelse</div>@if($uploaded->hasPages())<footer class="co-footer">{{$uploaded->appends(array('clinicas'=>request('clinicas')))->links()}}</footer>@endif
    <div class="vb-section-title">Archivos provenientes de protocolos</div><div class="vb-grid">@forelse($clinical as $file)<article class="vb-file">@if($isImage($file->nombre))<a class="vb-preview" target="_blank" rel="noopener" href="{{asset('hc_ima/'.$file->nombre)}}"><img loading="lazy" src="{{asset('hc_ima/'.$file->nombre)}}" alt="Biopsia clínica"></a>@else<div class="vb-preview"><i class="fas {{$fileIcon($file->nombre)}}"></i></div>@endif<div class="vb-info"><strong title="{{$file->nombre}}">{{$file->nombre}}</strong><span><i class="far fa-calendar mr-1"></i>{{$file->created_at?date('d/m/Y H:i',strtotime($file->created_at)):'Fecha no registrada'}}</span><span><i class="fas fa-user-md mr-1"></i>{{trim($file->doctor_lastname.' '.$file->doctor_name)?:'Profesional no identificado'}}</span></div><div class="vb-actions"><a class="btn btn-primary btn-sm" target="_blank" rel="noopener" href="{{url('hc_ima_nombre/'.$file->id)}}"><i class="fas fa-download mr-1"></i> Descargar</a></div></article>@empty<div class="co-empty" style="grid-column:1/-1">No hay archivos provenientes de protocolos.</div>@endforelse</div>@if($clinical->hasPages())<footer class="co-footer">{{$clinical->appends(array('subidas'=>request('subidas')))->links()}}</footer>@endif
  </section>
</div>
<script>
(function () {
  'use strict';
  var form = document.getElementById('biopsyUploadForm');
  if (!form) return;

  var input = document.getElementById('biopsyFiles');
  var dropZone = document.getElementById('biopsyDropZone');
  var selected = document.getElementById('biopsySelectedFiles');
  var list = document.getElementById('biopsySelectedList');
  var count = document.getElementById('biopsySelectedCount');
  var clear = document.getElementById('biopsyClearFiles');
  var submit = document.getElementById('biopsyUploadButton');
  var files = [];
  var maxBytes = 20 * 1024 * 1024;
  var allowed = ['pdf','doc','docx','txt','xls','xlsx','jpg','jpeg','png','gif'];

  function extension(name) {
    var parts = String(name || '').toLowerCase().split('.');
    return parts.length > 1 ? parts.pop() : '';
  }

  function iconFor(ext) {
    if (ext === 'pdf') return 'fa-file-pdf';
    if (ext === 'doc' || ext === 'docx') return 'fa-file-word';
    if (ext === 'xls' || ext === 'xlsx') return 'fa-file-excel';
    if (['jpg','jpeg','png','gif'].indexOf(ext) !== -1) return 'fa-file-image';
    if (ext === 'txt') return 'fa-file-alt';
    return 'fa-file';
  }

  function formatBytes(bytes) {
    if (bytes < 1024 * 1024) return Math.max(1, Math.round(bytes / 1024)) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
  }

  function validation(file) {
    var ext = extension(file.name);
    if (allowed.indexOf(ext) === -1) return 'Formato no permitido';
    if (file.size > maxBytes) return 'Supera el máximo de 20 MB';
    return '';
  }

  function synchronizeInput() {
    if (typeof DataTransfer === 'undefined') return;
    var transfer = new DataTransfer();
    files.forEach(function (file) { transfer.items.add(file); });
    input.files = transfer.files;
  }

  function render() {
    list.innerHTML = '';
    var hasErrors = false;
    files.forEach(function (file, index) {
      var error = validation(file);
      var ext = extension(file.name);
      hasErrors = hasErrors || !!error;
      var item = document.createElement('li');
      item.className = 'vb-selected__item' + (error ? ' is-invalid' : '');

      var icon = document.createElement('span');
      icon.className = 'vb-selected__file-icon';
      icon.innerHTML = '<i class="fas ' + iconFor(ext) + '" aria-hidden="true"></i>';

      var copy = document.createElement('span');
      copy.className = 'vb-selected__copy';
      var name = document.createElement('strong');
      name.textContent = file.name;
      name.title = file.name;
      var meta = document.createElement('small');
      meta.textContent = error || ((ext ? ext.toUpperCase() + ' · ' : '') + formatBytes(file.size));
      copy.appendChild(name); copy.appendChild(meta);

      var remove = document.createElement('button');
      remove.type = 'button'; remove.className = 'vb-selected__remove';
      remove.setAttribute('data-file-index', index); remove.setAttribute('aria-label', 'Quitar ' + file.name);
      remove.innerHTML = '<i class="fas fa-times" aria-hidden="true"></i>';
      item.appendChild(icon); item.appendChild(copy); item.appendChild(remove); list.appendChild(item);
    });
    count.textContent = files.length;
    selected.hidden = files.length === 0;
    submit.disabled = files.length === 0 || hasErrors;
  }

  function replaceFiles(newFiles) {
    files = Array.prototype.slice.call(newFiles || []);
    synchronizeInput(); render();
  }

  input.addEventListener('change', function () { replaceFiles(input.files); });
  clear.addEventListener('click', function () { input.value = ''; replaceFiles([]); });
  list.addEventListener('click', function (event) {
    var button = event.target.closest('[data-file-index]');
    if (!button) return;
    files.splice(parseInt(button.getAttribute('data-file-index'), 10), 1);
    synchronizeInput(); render();
  });
  dropZone.addEventListener('keydown', function (event) {
    if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); input.click(); }
  });
  ['dragenter','dragover'].forEach(function (name) {
    dropZone.addEventListener(name, function (event) { event.preventDefault(); dropZone.classList.add('is-dragging'); });
  });
  ['dragleave','drop'].forEach(function (name) {
    dropZone.addEventListener(name, function (event) { event.preventDefault(); dropZone.classList.remove('is-dragging'); });
  });
  dropZone.addEventListener('drop', function (event) {
    var dropped = event.dataTransfer && event.dataTransfer.files ? Array.prototype.slice.call(event.dataTransfer.files) : [];
    replaceFiles(files.concat(dropped));
  });
  form.addEventListener('submit', function (event) {
    var hasErrors = files.some(function (file) { return !!validation(file); });
    if (!files.length || hasErrors) { event.preventDefault(); return; }
    submit.disabled = true; submit.classList.add('is-loading');
    submit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Cargando…';
  });
  render();
})();
</script>
