@include('v3.clinical.operations._styles')

@php
  $patientName = trim(implode(' ', array_filter(array($patient->apellido1, $patient->apellido2, $patient->nombre1, $patient->nombre2))));
  $previewableExtensions = array('pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'txt', 'mp4', 'webm', 'ogg');
  $iconMap = array(
    'pdf' => array('fa-file-pdf', 'is-pdf', 'PDF'),
    'doc' => array('fa-file-word', 'is-word', 'WORD'), 'docx' => array('fa-file-word', 'is-word', 'WORD'),
    'xls' => array('fa-file-excel', 'is-excel', 'EXCEL'), 'xlsx' => array('fa-file-excel', 'is-excel', 'EXCEL'),
    'jpg' => array('fa-file-image', 'is-image', 'IMAGEN'), 'jpeg' => array('fa-file-image', 'is-image', 'IMAGEN'),
    'png' => array('fa-file-image', 'is-image', 'IMAGEN'), 'gif' => array('fa-file-image', 'is-image', 'IMAGEN'),
    'webp' => array('fa-file-image', 'is-image', 'IMAGEN'), 'bmp' => array('fa-file-image', 'is-image', 'IMAGEN'),
    'svg' => array('fa-file-code', 'is-generic', 'SVG'),
    'mp4' => array('fa-file-video', 'is-video', 'VIDEO'), 'webm' => array('fa-file-video', 'is-video', 'VIDEO'),
    'ogg' => array('fa-file-video', 'is-video', 'VIDEO'),
    'txt' => array('fa-file-alt', 'is-text', 'TEXTO'),
  );
@endphp

<div class="co-page paa-page" data-csrf-token="{{csrf_token()}}">
  <section class="co-hero">
    <div class="co-hero__main">
      <span class="co-hero__icon"><i class="fas fa-folder-open"></i></span>
      <div>
        <span class="co-eyebrow">HISTORIA CLÍNICA · V3</span>
        <h1>Documentos de agenda</h1>
        <p>Consulta de forma segura los archivos y formularios incorporados durante la atención.</p>
      </div>
    </div>
    <a class="btn btn-outline-light" href="{{route('v3.patients.index')}}"><i class="fas fa-arrow-left mr-1"></i> Volver a pacientes</a>
  </section>

  <section class="co-card paa-patient">
    <div class="paa-patient__icon"><i class="fas fa-user-injured"></i></div>
    <div class="paa-patient__data">
      <span>Paciente</span>
      <strong>{{$patientName !== '' ? $patientName : 'Paciente sin nombre registrado'}}</strong>
      <small>Identificación: {{$patient->id}}</small>
    </div>
    <div class="paa-total"><strong>{{$archives->total()}}</strong><span>{{$archives->total() === 1 ? 'documento' : 'documentos'}}</span></div>
  </section>

  <section class="co-card">
    <header class="co-head">
      <div class="co-heading">
        <i class="fas fa-history"></i>
        <div><h2>Historial documental</h2><p>Archivos y contenidos registrados en las agendas del paciente.</p></div>
      </div>
      <span class="co-count">{{$archives->total()}} registro(s)</span>
    </header>

    @if($archives->count())
      <div class="paa-grid">
        @foreach($archives as $archive)
          @php
            $hasFile = trim((string) $archive->archivo) !== '';
            $hasText = trim((string) $archive->texto) !== '';
            $extension = $hasFile ? strtolower(pathinfo($archive->archivo, PATHINFO_EXTENSION)) : 'html';
            $visual = isset($iconMap[$extension]) ? $iconMap[$extension] : ($hasText && !$hasFile ? array('fa-file-code', 'is-html', 'FORMULARIO') : array('fa-file', 'is-generic', strtoupper($extension ?: 'ARCHIVO')));
            $description = trim((string) $archive->descripcion);
            $type = trim((string) $archive->tipo_documento);
            $title = $description !== '' ? $description : ($type !== '' ? $type : ($hasFile ? basename($archive->archivo) : 'Contenido de agenda'));
            $filePreviewable = $hasFile && in_array($extension, $previewableExtensions, true);
            $primaryIsText = $hasText && (!$hasFile || !$filePreviewable);
            $primaryUrl = $primaryIsText
              ? route('v3.patient-agenda-archives.text', array('patient'=>$patient->id,'archive'=>$archive->id))
              : ($filePreviewable ? route('v3.patient-agenda-archives.file', array('patient'=>$patient->id,'archive'=>$archive->id)) : '');
            $primaryPreviewable = $primaryIsText || $filePreviewable;
            $doctorName = trim($archive->doctor_name.' '.$archive->doctor_lastname);
            $date = $archive->fechaini ? substr((string) $archive->fechaini, 0, 10) : substr((string) $archive->created_at, 0, 10);
          @endphp
          <article class="paa-document" data-archive-id="{{$archive->id}}">
            <button type="button" class="paa-preview js-paa-preview"
              data-url="{{$primaryUrl}}"
              data-title="{{$title}}" data-kind="{{$primaryIsText ? 'text' : $extension}}"
              data-download="{{$hasFile ? route('v3.patient-agenda-archives.download', array('patient'=>$patient->id,'archive'=>$archive->id)) : ''}}"
              data-previewable="{{$primaryPreviewable ? '1' : '0'}}">
              @if($hasFile && in_array($extension, array('jpg','jpeg','png','gif','webp','bmp'), true))
                <img src="{{route('v3.patient-agenda-archives.file', array('patient'=>$patient->id,'archive'=>$archive->id))}}" alt="Vista previa de {{$title}}" loading="lazy">
              @else
                <span class="paa-file-icon {{$visual[1]}}"><i class="fas {{$visual[0]}}"></i><b>{{$visual[2]}}</b></span>
              @endif
              <span class="paa-view-hint"><i class="fas fa-eye"></i> {{$primaryPreviewable ? 'Ver documento' : 'Ver información'}}</span>
            </button>
            <div class="paa-document__body">
              <span class="paa-document__type">{{$type !== '' ? $type : $visual[2]}}</span>
              <h3 title="{{$title}}">{{$title}}</h3>
              <div class="paa-meta"><span><i class="far fa-calendar-alt"></i> {{$date ?: 'Fecha no registrada'}}</span>@if($doctorName !== '')<span><i class="fas fa-user-md"></i> {{$doctorName}}</span>@endif</div>
              <div class="paa-actions">
                <button type="button" class="btn btn-primary btn-sm js-paa-preview"
                  data-url="{{$primaryUrl}}"
                  data-title="{{$title}}" data-kind="{{$primaryIsText ? 'text' : $extension}}"
                  data-download="{{$hasFile ? route('v3.patient-agenda-archives.download', array('patient'=>$patient->id,'archive'=>$archive->id)) : ''}}"
                  data-previewable="{{$primaryPreviewable ? '1' : '0'}}"><i class="fas fa-eye"></i> Visualizar</button>
                @if($hasText && $filePreviewable)
                  <button type="button" class="btn btn-outline-info btn-sm js-paa-preview" data-url="{{route('v3.patient-agenda-archives.text', array('patient'=>$patient->id,'archive'=>$archive->id))}}" data-title="{{$title}} · Formulario" data-kind="text" data-download="" data-previewable="1"><i class="fas fa-file-alt"></i> Formulario</button>
                @endif
                @if($hasFile)<a class="btn btn-outline-primary btn-sm" href="{{route('v3.patient-agenda-archives.download', array('patient'=>$patient->id,'archive'=>$archive->id))}}"><i class="fas fa-download"></i> Descargar</a>@endif
                @if($hasFile)
                  <button type="button" class="btn btn-outline-danger btn-sm js-paa-delete"
                    data-url="{{route('v3.patient-agenda-archives.destroy', array('patient'=>$patient->id,'archive'=>$archive->id))}}"
                    data-title="{{$title}}"><i class="fas fa-trash-alt"></i> Eliminar</button>
                @endif
              </div>
            </div>
          </article>
        @endforeach
      </div>
      @if($archives->hasPages())<footer class="paa-pagination">{{$archives->appends(request()->query())->links()}}</footer>@endif
    @else
      <div class="paa-empty">
        <span><i class="fas fa-folder-open"></i></span>
        <h3>Aún no hay documentos agregados</h3>
        <p>Cuando se adjunten archivos o formularios desde una agenda, aparecerán aquí.</p>
      </div>
    @endif
  </section>
</div>

<div class="paa-modal" id="paaDocumentModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="paaModalTitle">
  <div class="paa-modal__backdrop js-paa-close"></div>
  <div class="paa-modal__dialog" role="document">
    <header class="paa-modal__head">
      <div><span>DOCUMENTO DE AGENDA</span><h2 id="paaModalTitle">Vista previa</h2></div>
      <button type="button" class="paa-modal__close js-paa-close" aria-label="Cerrar"><i class="fas fa-times"></i></button>
    </header>
    <div class="paa-modal__body">
      <div class="paa-loading" id="paaLoading"><i class="fas fa-circle-notch fa-spin"></i><strong>Cargando documento…</strong></div>
      <iframe id="paaDocumentFrame" title="Vista previa del documento de agenda" sandbox="allow-same-origin"></iframe>
      <div class="paa-unavailable" id="paaUnavailable"><i class="fas fa-file-download"></i><h3>Este formato no admite vista previa</h3><p>Puedes descargar el archivo para abrirlo con la aplicación correspondiente.</p></div>
    </div>
    <footer class="paa-modal__foot">
      <button type="button" class="btn btn-light js-paa-close">Cerrar</button>
      <a class="btn btn-outline-primary" id="paaDownload" href="#"><i class="fas fa-download mr-1"></i> Descargar</a>
      <button type="button" class="btn btn-primary" id="paaPrint"><i class="fas fa-print mr-1"></i> Imprimir</button>
    </footer>
  </div>
</div>

<style>
  .paa-page{color:#183f57}.paa-patient{display:flex;align-items:center;gap:13px;padding:13px 16px}.paa-patient__icon{display:grid;place-items:center;width:43px;height:43px;border-radius:11px;color:#0787b0;background:#ddf4f8;font-size:18px}.paa-patient__data{min-width:0}.paa-patient__data span,.paa-patient__data small{display:block;color:#68879a;font-size:10px}.paa-patient__data strong{display:block;color:#083f63;font-size:15px;font-weight:800;text-transform:uppercase}.paa-total{display:flex;align-items:center;gap:8px;margin-left:auto;padding:7px 11px;border-radius:10px;background:#eef8fa}.paa-total strong{color:#0686a9;font-size:20px}.paa-total span{color:#55788c;font-size:10px;font-weight:800;text-transform:uppercase}
  .paa-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:13px;padding:16px}.paa-document{overflow:hidden;border:1px solid #d2e3eb;border-radius:13px;background:#fff;transition:.18s}.paa-document:hover{transform:translateY(-2px);box-shadow:0 9px 22px rgba(25,78,105,.12)}.paa-document.is-removing{opacity:0;transform:scale(.96)}.paa-preview{position:relative;display:grid;width:100%;height:145px;place-items:center;overflow:hidden;border:0;border-bottom:1px solid #e0ebef;background:linear-gradient(145deg,#f3fafc,#e8f4f8);cursor:pointer}.paa-preview img{width:100%;height:100%;object-fit:cover}.paa-file-icon{display:flex;flex-direction:column;align-items:center;gap:8px}.paa-file-icon i{font-size:49px}.paa-file-icon b{padding:3px 7px;border-radius:12px;color:#fff;background:#657c8a;font-size:9px;letter-spacing:.5px}.paa-file-icon.is-pdf{color:#d94f57}.paa-file-icon.is-pdf b{background:#d94f57}.paa-file-icon.is-word{color:#3276c5}.paa-file-icon.is-word b{background:#3276c5}.paa-file-icon.is-excel{color:#1c9b69}.paa-file-icon.is-excel b{background:#1c9b69}.paa-file-icon.is-image{color:#875bc1}.paa-file-icon.is-image b{background:#875bc1}.paa-file-icon.is-video{color:#e27737}.paa-file-icon.is-video b{background:#e27737}.paa-file-icon.is-text,.paa-file-icon.is-html{color:#0a8da9}.paa-file-icon.is-text b,.paa-file-icon.is-html b{background:#0a8da9}.paa-file-icon.is-generic{color:#667f8e}.paa-view-hint{position:absolute;right:9px;bottom:8px;padding:5px 8px;border-radius:14px;color:#fff;background:rgba(5,55,82,.82);font-size:9px;font-weight:800}.paa-document__body{padding:12px}.paa-document__type{display:inline-block;margin-bottom:6px;color:#0785a6;font-size:9px;font-weight:800;text-transform:uppercase}.paa-document h3{height:34px;margin:0 0 8px;overflow:hidden;color:#103e5c;font-size:13px;font-weight:800;line-height:1.3}.paa-meta{display:flex;min-height:35px;flex-direction:column;gap:3px;color:#78909f;font-size:9px}.paa-meta span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.paa-actions{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:6px;margin-top:10px}.paa-actions .btn{display:flex;align-items:center;justify-content:center;gap:5px;width:100%;min-width:0;border-radius:7px;font-size:10px;font-weight:800}.paa-actions>.btn:last-child:nth-child(odd){grid-column:1/-1}.paa-pagination{padding:11px 16px;border-top:1px solid #dce8ed}.paa-pagination .pagination{margin:0}.paa-empty{padding:58px 20px;text-align:center}.paa-empty>span{display:grid;width:65px;height:65px;place-items:center;margin:0 auto 14px;border-radius:18px;color:#0b8bae;background:#e3f5f8;font-size:27px}.paa-empty h3{margin:0 0 6px;color:#123f5b;font-size:17px;font-weight:800}.paa-empty p{margin:0;color:#718b9c}
  .paa-modal{position:fixed;z-index:1090;inset:0;display:none;align-items:center;justify-content:center;padding:24px}.paa-modal.is-open{display:flex}.paa-modal__backdrop{position:absolute;inset:0;background:rgba(7,27,39,.67);backdrop-filter:blur(2px)}.paa-modal__dialog{position:relative;display:flex;width:min(1180px,96vw);height:min(830px,92vh);flex-direction:column;overflow:hidden;border-radius:15px;background:#fff;box-shadow:0 25px 75px rgba(0,25,39,.36)}.paa-modal__head{display:flex;align-items:center;justify-content:space-between;padding:13px 17px;color:#fff;background:linear-gradient(110deg,#0b678f,#0c95ba 65%,#13a8a5)}.paa-modal__head span{font-size:9px;font-weight:800;letter-spacing:.8px}.paa-modal__head h2{margin:2px 0 0;font-size:17px;font-weight:800}.paa-modal__close{display:grid;width:34px;height:34px;place-items:center;border:1px solid rgba(255,255,255,.32);border-radius:9px;color:#fff;background:rgba(255,255,255,.12)}.paa-modal__body{position:relative;min-height:0;flex:1;background:#e8f0f3}.paa-modal iframe{display:none;width:100%;height:100%;border:0;background:#fff}.paa-loading,.paa-unavailable{display:flex;height:100%;align-items:center;justify-content:center;flex-direction:column;gap:10px;color:#476b7e}.paa-loading i{font-size:25px;color:#0789b0}.paa-unavailable{display:none;text-align:center}.paa-unavailable>i{font-size:52px;color:#8299a6}.paa-unavailable h3{margin:4px 0 0;color:#153e57;font-size:18px}.paa-unavailable p{margin:0}.paa-modal__foot{display:flex;justify-content:flex-end;gap:8px;padding:11px 15px;border-top:1px solid #d4e2e8;background:#fff}.paa-modal__foot .btn{border-radius:8px;font-weight:800}body.paa-modal-open{overflow:hidden}
  @media(max-width:1100px){.paa-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}@media(max-width:760px){.paa-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.paa-modal{padding:0}.paa-modal__dialog{width:100%;height:100%;border-radius:0}}@media(max-width:500px){.paa-grid{grid-template-columns:1fr}.paa-patient{align-items:flex-start}.paa-total{display:none}.paa-modal__foot{flex-wrap:wrap}.paa-modal__foot .btn{flex:1}}
</style>

<script>
(function () {
  var modal = document.getElementById('paaDocumentModal'), frame = document.getElementById('paaDocumentFrame'),
      loading = document.getElementById('paaLoading'), unavailable = document.getElementById('paaUnavailable'),
      title = document.getElementById('paaModalTitle'), download = document.getElementById('paaDownload'),
      printButton = document.getElementById('paaPrint'), page = document.querySelector('.paa-page'), lastFocus = null;
  if (!modal) return;

  function openModal(button) {
    lastFocus = button; title.textContent = button.getAttribute('data-title') || 'Vista previa';
    var url = button.getAttribute('data-url') || '', canPreview = button.getAttribute('data-previewable') === '1',
        downloadUrl = button.getAttribute('data-download') || '';
    frame.style.display = 'none'; frame.src = 'about:blank'; loading.style.display = canPreview ? 'flex' : 'none';
    unavailable.style.display = canPreview ? 'none' : 'flex';
    download.style.display = downloadUrl ? '' : 'none'; download.href = downloadUrl || '#';
    printButton.style.display = canPreview ? '' : 'none'; modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false'); document.body.classList.add('paa-modal-open');
    if (canPreview && url) {
      frame.onload = function () { loading.style.display = 'none'; frame.style.display = 'block'; };
      frame.src = url;
    }
    modal.querySelector('.paa-modal__close').focus();
  }
  function closeModal() {
    modal.classList.remove('is-open'); modal.setAttribute('aria-hidden', 'true'); document.body.classList.remove('paa-modal-open');
    frame.src = 'about:blank'; if (lastFocus) lastFocus.focus();
  }
  document.querySelectorAll('.js-paa-preview').forEach(function (button) { button.addEventListener('click', function () { openModal(button); }); });
  document.querySelectorAll('.js-paa-delete').forEach(function (button) {
    button.addEventListener('click', function () { deleteDocument(button); });
  });
  modal.querySelectorAll('.js-paa-close').forEach(function (button) { button.addEventListener('click', closeModal); });
  printButton.addEventListener('click', function () {
    try { frame.contentWindow.focus(); frame.contentWindow.print(); }
    catch (error) { window.open(frame.src, '_blank', 'noopener'); }
  });
  document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && modal.classList.contains('is-open')) closeModal(); });

  function notify(options) {
    if (window.Swal && typeof window.Swal.fire === 'function') return window.Swal.fire(options);
    return Promise.resolve({isConfirmed: options.showCancelButton ? window.confirm(options.text || options.title) : true});
  }

  function updateCounters(remaining) {
    var total = document.querySelector('.paa-total'), count = document.querySelector('.co-count');
    if (total) {
      var number = total.querySelector('strong'), label = total.querySelector('span');
      if (number) number.textContent = remaining;
      if (label) label.textContent = remaining === 1 ? 'documento' : 'documentos';
    }
    if (count) count.textContent = remaining + ' registro(s)';
  }

  function deleteDocument(button) {
    var card = button.closest('.paa-document'), url = button.getAttribute('data-url'),
        documentTitle = button.getAttribute('data-title') || 'este documento';
    notify({
      icon: 'warning', title: '¿Eliminar documento?',
      text: 'Se eliminará "' + documentTitle + '" de forma permanente.',
      showCancelButton: true, confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
      confirmButtonColor: '#dc3545', reverseButtons: true
    }).then(function (result) {
      if (!result.isConfirmed || !url) return;
      button.disabled = true;
      button.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Eliminando';
      fetch(url, {
        method: 'DELETE', credentials: 'same-origin',
        headers: {
          'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': page ? page.getAttribute('data-csrf-token') : ''
        }
      }).then(function (response) {
        return response.json().catch(function () { return {}; }).then(function (payload) {
          if (!response.ok) throw new Error(payload.message || 'No se pudo eliminar el documento.');
          return payload;
        });
      }).then(function (payload) {
        if (card) {
          card.classList.add('is-removing');
          window.setTimeout(function () {
            card.remove();
            var grid = document.querySelector('.paa-grid');
            if (grid && !grid.querySelector('.paa-document')) {
              grid.outerHTML = '<div class="paa-empty"><span><i class="fas fa-folder-open"></i></span><h3>Aún no hay documentos agregados</h3><p>Cuando se adjunten archivos o formularios desde una agenda, aparecerán aquí.</p></div>';
            }
          }, 180);
        }
        updateCounters(Number(payload.remaining || 0));
        notify({icon: 'success', title: 'Documento eliminado', text: payload.message || 'El documento se eliminó correctamente.', timer: 1800, showConfirmButton: false});
      }).catch(function (error) {
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-trash-alt"></i> Eliminar';
        notify({icon: 'error', title: 'No se pudo eliminar', text: error.message});
      });
    });
  }
})();
</script>
