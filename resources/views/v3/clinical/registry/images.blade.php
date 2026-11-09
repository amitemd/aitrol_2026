@include('v3.clinical.registry._styles')
<style>
  .vi-patient{display:flex;align-items:center;gap:14px}.vi-avatar{width:52px;height:52px;border-radius:16px;background:#e4f6fb;color:#087fac;display:grid;place-items:center;font-size:21px}.vi-patient h2{margin:0;color:#063b63;font-size:20px}.vi-patient p{margin:3px 0 0;color:#607d91}.vi-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;padding:20px}.vi-file{border:1px solid #cfe1eb;border-radius:14px;background:#fff;overflow:hidden;display:flex;flex-direction:column;min-width:0}.vi-preview{height:190px;background:linear-gradient(145deg,#eff9fc,#e1f0f6);display:flex;align-items:center;justify-content:center;overflow:hidden;color:#087fac;font-size:52px}.vi-preview img{width:100%;height:100%;object-fit:cover;transition:transform .2s}.vi-preview:hover img{transform:scale(1.03)}.vi-info{padding:14px;display:flex;flex-direction:column;gap:5px;flex:1}.vi-info strong{color:#083e65;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.vi-info span{font-size:12px;color:#688296}.vi-actions{display:flex;gap:7px;padding:0 14px 14px}.vi-actions .btn{flex:1}.vi-extension{position:absolute;margin:10px;align-self:flex-start;background:#fff;color:#087fac;border-radius:20px;padding:3px 9px;font-size:10px;font-weight:700;box-shadow:0 2px 8px rgba(0,50,80,.12)}@media(max-width:1100px){.vi-grid{grid-template-columns:repeat(3,1fr)}}@media(max-width:780px){.vi-grid{grid-template-columns:repeat(2,1fr)}}@media(max-width:520px){.vi-grid{grid-template-columns:1fr}}
  .vi-preview.js-vi-preview{width:100%;padding:0;border:0;cursor:zoom-in}.vi-image-modal .modal-dialog{width:min(1120px,calc(100vw - 28px));max-width:1120px;margin:14px auto}.vi-image-modal .modal-content{overflow:hidden;border:0;border-radius:16px;box-shadow:0 24px 70px rgba(0,24,40,.32)}.vi-image-modal .modal-header{display:flex;align-items:center;padding:14px 18px;color:#fff;border:0;background:linear-gradient(115deg,#075b86,#0888ad 62%,#13aaa3)}.vi-image-modal .modal-title{min-width:0;margin:0;font-size:16px;font-weight:800}.vi-image-modal .modal-title span{display:block;max-width:850px;margin-top:2px;overflow:hidden;font-size:10px;font-weight:500;opacity:.82;text-overflow:ellipsis;white-space:nowrap}.vi-image-modal .close{margin:-8px -6px -8px auto;color:#fff;opacity:.9;text-shadow:none}.vi-image-modal .modal-body{min-height:340px;display:flex;align-items:center;justify-content:center;padding:16px;background:#091820}.vi-image-modal .modal-body img{display:block;max-width:100%;max-height:74vh;object-fit:contain;border-radius:8px;box-shadow:0 8px 28px rgba(0,0,0,.25)}.vi-image-modal .vi-modal-state{color:#c3d4dc;text-align:center}.vi-image-modal .vi-modal-state i{display:block;margin-bottom:9px;font-size:30px;color:#57bfd2}.vi-image-modal .modal-footer{justify-content:space-between;padding:11px 16px;border-top:1px solid #d7e5eb;background:#f7fbfc}.vi-image-modal .modal-footer small{color:#718895}.vi-image-modal .modal-footer .btn{min-width:100px;border-radius:8px;font-weight:700}@media(max-width:780px){.vi-image-modal .modal-body{min-height:260px;padding:10px}}@media(max-width:520px){.vi-image-modal .modal-footer small{display:none}}
</style>
<?php $patientName=trim($patient->apellido1.' '.$patient->apellido2.' '.$patient->nombre1.' '.$patient->nombre2); ?>
<div class="co-page">
  <section class="co-hero"><div class="co-hero__main"><div class="co-hero__icon"><i class="fas fa-images"></i></div><div><div class="co-eyebrow">HISTORIA CLÍNICA · V3</div><h1>Historial de imágenes</h1><p>Consulta las imágenes clínicas asociadas a las atenciones del paciente.</p></div></div><a class="btn btn-outline-light" href="{{url()->previous()}}"><i class="fas fa-arrow-left mr-1"></i> Regresar</a></section>
  <section class="co-card"><header class="co-head"><div class="vi-patient"><div class="vi-avatar"><i class="fas fa-user"></i></div><div><h2>{{$patientName?:'Paciente sin nombre registrado'}}</h2><p>Identificación: {{$patient->id}} · {{$images->total()}} archivo(s)</p></div></div></header></section>
  <section class="co-card"><header class="co-head"><div class="co-heading"><i class="fas fa-photo-video"></i><div><h2>Galería clínica</h2><p>Imágenes y archivos audiovisuales ordenados desde el más reciente.</p></div></div><span class="co-count">{{$images->total()}} archivo(s)</span></header>
    <div class="vi-grid">
      @forelse($images as $image)
        <?php
          $extension=strtolower(pathinfo($image->nombre,PATHINFO_EXTENSION));
          $isImage=in_array($extension,array('jpg','jpeg','png','gif','webp'),true);
          $icon=in_array($extension,array('mp4','mov','avi','webm'),true)?'fa-file-video':($extension==='pdf'?'fa-file-pdf':'fa-file-image');
          $date=$image->created_at?:$image->fechaini;
          $doctor=trim($image->doctor_lastname.' '.$image->doctor_name);
        ?>
        <article class="vi-file"><span class="vi-extension">{{strtoupper($extension?:'ARCHIVO')}}</span>@if($isImage)<button type="button" class="vi-preview js-vi-preview" data-image-src="{{asset('hc_ima/'.$image->nombre)}}" data-image-name="{{$image->nombre}}" aria-label="Ver imagen {{$image->nombre}}"><img loading="lazy" src="{{asset('hc_ima/'.$image->nombre)}}" alt="Imagen clínica"></button>@else<div class="vi-preview"><i class="fas {{$icon}}"></i></div>@endif<div class="vi-info"><strong title="{{$image->nombre}}">{{$image->nombre}}</strong><span><i class="far fa-calendar mr-1"></i>{{$date?date('d/m/Y H:i',strtotime($date)):'Fecha no registrada'}}</span><span><i class="fas fa-user-md mr-1"></i>{{$doctor?:'Profesional no identificado'}}</span></div><div class="vi-actions">@if($isImage)<button type="button" class="btn btn-outline-primary btn-sm js-vi-preview" data-image-src="{{asset('hc_ima/'.$image->nombre)}}" data-image-name="{{$image->nombre}}"><i class="fas fa-expand mr-1"></i> Ver</button>@endif<a class="btn btn-primary btn-sm" target="_blank" rel="noopener" href="{{url('hc_ima_nombre/'.$image->id)}}"><i class="fas fa-download mr-1"></i> Descargar</a></div></article>
      @empty<div class="co-empty" style="grid-column:1/-1"><i class="fas fa-images fa-2x d-block mb-2"></i><strong>No hay imágenes registradas</strong><div>El paciente todavía no tiene imágenes clínicas en la empresa activa.</div></div>@endforelse
    </div>
    @if($images->hasPages())<footer class="co-footer"><span>Mostrando {{$images->firstItem()}} a {{$images->lastItem()}} de {{$images->total()}}</span>{{$images->links()}}</footer>@endif
  </section>
</div>

<div class="modal fade vi-image-modal" id="clinicalImagePreviewModal" tabindex="-1" role="dialog" aria-labelledby="clinicalImagePreviewTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header"><h2 class="modal-title" id="clinicalImagePreviewTitle"><i class="fas fa-image mr-2"></i>Vista previa<span id="clinicalImagePreviewName"></span></h2><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
      <div class="modal-body"><div class="vi-modal-state" id="clinicalImagePreviewState"><i class="fas fa-spinner fa-spin"></i>Cargando imagen…</div><img id="clinicalImagePreview" src="" alt="Vista ampliada de la imagen clínica" hidden></div>
      <div class="modal-footer"><small><i class="fas fa-search-plus mr-1"></i>La imagen se muestra sin descargar el archivo.</small><button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button></div>
    </div>
  </div>
</div>

<script>
  (function ($) {
    'use strict';
    var $modal = $('#clinicalImagePreviewModal');
    var image = document.getElementById('clinicalImagePreview');
    var state = document.getElementById('clinicalImagePreviewState');
    var name = document.getElementById('clinicalImagePreviewName');

    $(document).on('click', '.js-vi-preview', function () {
      var source = this.getAttribute('data-image-src');
      var fileName = this.getAttribute('data-image-name') || 'Imagen clínica';
      if (!source) return;

      image.hidden = true;
      image.removeAttribute('src');
      state.style.display = 'block';
      state.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Cargando imagen…';
      name.textContent = fileName;
      image.onload = function () { state.style.display = 'none'; image.hidden = false; };
      image.onerror = function () { image.hidden = true; state.style.display = 'block'; state.innerHTML = '<i class="fas fa-exclamation-triangle"></i>No se pudo mostrar la imagen.'; };
      image.src = source;
      $modal.modal('show');
    });

    $modal.on('hidden.bs.modal', function () {
      image.onload = null;
      image.onerror = null;
      image.removeAttribute('src');
      image.hidden = true;
      name.textContent = '';
    });
  })(jQuery);
</script>
