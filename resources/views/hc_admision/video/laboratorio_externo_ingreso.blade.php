@extends(session('v3_legacy_shell', false) ? 'v3.layouts.view_form_child' : 'hc_admision.visita.base')
@section(session('v3_legacy_shell', false) ? 'contenido' : 'action-content')
@if(session('v3_legacy_shell', false))
<link rel="stylesheet" href="{{ url('v3/plugins/dropzone/css/dropzone.min.css') }}">
@endif
<style>
    .content-wrapper > .content-header {
        display: none;
    }

    .content-wrapper {
        background: #f2f8fb;
    }

    .external-lab-page {
        --lab-navy: #07456f;
        --lab-blue: #087fbd;
        --lab-teal: #16aaa4;
        --lab-ink: #07385d;
        --lab-muted: #69839a;
        --lab-line: #cfe4ee;
        --lab-soft: #eef9fc;
        padding: 26px 28px 44px;
        color: var(--lab-ink);
    }

    .external-lab-page .lab-shell {
        max-width: 1500px;
        margin: 0 auto;
    }

    .external-lab-page .lab-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 16px;
        color: var(--lab-muted);
        font-size: 13px;
        font-weight: 600;
    }

    .external-lab-page .lab-breadcrumb a {
        color: var(--lab-blue);
        text-decoration: none;
    }

    .external-lab-page .lab-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 18px;
        padding: 25px 28px;
        overflow: hidden;
        color: #fff;
        border-radius: 18px;
        background: linear-gradient(115deg, var(--lab-navy), var(--lab-blue) 58%, var(--lab-teal));
        box-shadow: 0 14px 34px rgba(7, 69, 111, .16);
        position: relative;
    }

    .external-lab-page .lab-hero:after {
        content: "";
        position: absolute;
        right: -65px;
        top: -85px;
        width: 240px;
        height: 240px;
        border: 1px solid rgba(255, 255, 255, .18);
        border-radius: 50%;
    }

    .external-lab-page .lab-hero-main,
    .external-lab-page .lab-patient-chip {
        position: relative;
        z-index: 1;
    }

    .external-lab-page .lab-hero-main {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .external-lab-page .lab-hero-icon {
        width: 62px;
        height: 62px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 62px;
        border: 1px solid rgba(255, 255, 255, .3);
        border-radius: 16px;
        background: rgba(255, 255, 255, .12);
        font-size: 28px;
    }

    .external-lab-page .lab-eyebrow {
        margin-bottom: 4px;
        color: #aef4ed;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1.1px;
        text-transform: uppercase;
    }

    .external-lab-page .lab-hero h2 {
        margin: 0 0 5px;
        color: #fff;
        font-size: 27px;
        font-weight: 700;
    }

    .external-lab-page .lab-hero p {
        margin: 0;
        color: rgba(255, 255, 255, .86);
        font-size: 14px;
    }

    .external-lab-page .lab-patient-chip {
        min-width: 235px;
        padding: 13px 17px;
        border: 1px solid rgba(255, 255, 255, .25);
        border-radius: 13px;
        background: rgba(255, 255, 255, .12);
        text-align: right;
    }

    .external-lab-page .lab-patient-chip small {
        display: block;
        margin-bottom: 3px;
        color: #baf5ef;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .8px;
        text-transform: uppercase;
    }

    .external-lab-page .lab-patient-chip strong {
        display: block;
        color: #fff;
        font-size: 15px;
    }

    .external-lab-page .lab-card {
        margin-bottom: 18px;
        overflow: hidden;
        border: 1px solid var(--lab-line);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 9px 28px rgba(32, 88, 120, .07);
    }

    .external-lab-page .lab-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 17px 20px;
        border-bottom: 1px solid var(--lab-line);
        background: var(--lab-soft);
    }

    .external-lab-page .lab-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .external-lab-page .lab-card-title .lab-title-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        color: var(--lab-blue);
        background: #dff3f8;
        font-size: 17px;
    }

    .external-lab-page .lab-card-title h3 {
        margin: 0 0 2px;
        color: var(--lab-ink);
        font-size: 18px;
        font-weight: 700;
    }

    .external-lab-page .lab-card-title p {
        margin: 0;
        color: var(--lab-muted);
        font-size: 12px;
    }

    .external-lab-page .lab-count {
        padding: 7px 11px;
        color: #08799a;
        border-radius: 18px;
        background: #dff6f8;
        font-size: 11px;
        font-weight: 700;
    }

    .external-lab-page .lab-card-body {
        padding: 20px;
    }

    .external-lab-page .lab-help {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 15px;
        padding: 12px 14px;
        color: #3d647c;
        border-left: 3px solid var(--lab-teal);
        border-radius: 8px;
        background: #f3fafc;
        font-size: 12px;
        line-height: 1.5;
    }

    .external-lab-page #addimage.dropzone {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        padding: 28px;
        border: 2px dashed #76c6d8;
        border-radius: 15px;
        background: linear-gradient(180deg, #fbfeff, #f0fafc);
        transition: border-color .2s ease, background .2s ease, transform .2s ease;
    }

    .external-lab-page #addimage.dropzone:hover,
    .external-lab-page #addimage.dropzone.dz-drag-hover {
        border-color: var(--lab-teal);
        background: #ecfbfa;
        transform: translateY(-1px);
    }

    .external-lab-page #addimage .dz-message {
        width: 100%;
        margin: 0;
        color: var(--lab-ink);
        text-align: center;
    }

    .external-lab-page .lab-upload-icon {
        width: 68px;
        height: 68px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 13px;
        color: var(--lab-blue);
        border-radius: 50%;
        background: #dff3f8;
        font-size: 27px;
    }

    .external-lab-page .lab-upload-title {
        margin-bottom: 4px;
        font-size: 17px;
        font-weight: 700;
    }

    .external-lab-page .lab-upload-copy {
        margin-bottom: 12px;
        color: var(--lab-muted);
        font-size: 13px;
    }

    .external-lab-page .lab-upload-button {
        display: inline-block;
        padding: 9px 16px;
        color: #fff;
        border-radius: 9px;
        background: var(--lab-blue);
        font-size: 12px;
        font-weight: 700;
    }

    .external-lab-page .lab-formats {
        margin-top: 11px;
        color: #8aa0b0;
        font-size: 10px;
        letter-spacing: .2px;
    }

    .external-lab-page .lab-files-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
    }

    .external-lab-page .lab-file-card {
        min-width: 0;
        overflow: hidden;
        border: 1px solid var(--lab-line);
        border-radius: 13px;
        background: #fff;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .external-lab-page .lab-file-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(32, 88, 120, .11);
    }

    .external-lab-page .lab-file-preview {
        height: 145px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px;
        overflow: hidden;
        background: #f4fafc;
        text-decoration: none;
        cursor: pointer;
    }

    .external-lab-page .lab-file-preview img {
        max-width: 100%;
        max-height: 121px;
        object-fit: contain;
    }

    .external-lab-page .lab-extension-icon {
        position: relative;
        width: 76px;
        height: 88px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #e4474f;
        border: 1px solid #d5e6ec;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 8px 20px rgba(24, 74, 99, .10);
        font-size: 38px;
    }

    .external-lab-page .lab-extension-icon.is-image { color: #16a078; }
    .external-lab-page .lab-extension-icon.is-office { color: #2877bb; }
    .external-lab-page .lab-extension-icon.is-text { color: #64798a; }
    .external-lab-page .lab-extension-badge {
        position: absolute;
        right: -8px;
        bottom: 7px;
        min-width: 38px;
        padding: 3px 6px;
        color: #fff;
        border-radius: 8px;
        background: #173f55;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .5px;
        text-align: center;
        text-transform: uppercase;
    }

    .external-lab-page .lab-file-preview:hover .lab-extension-icon { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(24, 74, 99, .16); }
    .external-lab-page .lab-file-preview-copy { position: absolute; left: 0; right: 0; bottom: 8px; color: var(--lab-muted); font-size: 10px; font-weight: 700; text-align: center; }
    .external-lab-page .lab-file-preview { position: relative; }

    .external-lab-page .lab-file-info {
        padding: 13px 14px;
    }

    .external-lab-page .lab-file-name {
        display: block;
        min-height: 34px;
        overflow: hidden;
        color: var(--lab-ink);
        font-size: 12px;
        font-weight: 700;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .external-lab-page .lab-file-date {
        display: block;
        margin-top: 5px;
        color: var(--lab-muted);
        font-size: 11px;
    }

    .external-lab-page .lab-file-actions {
        display: flex;
        gap: 8px;
        margin-top: 12px;
    }

    .external-lab-page .lab-action {
        flex: 1;
        padding: 8px 10px;
        border: 1px solid #bcd9e6;
        border-radius: 8px;
        background: #fff;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
    }

    .external-lab-page .lab-action-download {
        color: var(--lab-blue);
    }

    .external-lab-page .lab-action-delete {
        color: #d94d55;
        border-color: #f0c8cb;
    }

    .external-lab-page .lab-action:hover,
    .external-lab-page .lab-action:focus {
        text-decoration: none;
        background: #f4fafc;
    }

    .external-lab-page .lab-empty {
        padding: 45px 20px;
        color: var(--lab-muted);
        text-align: center;
    }

    .external-lab-page .lab-empty i {
        width: 58px;
        height: 58px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        color: var(--lab-blue);
        border-radius: 50%;
        background: #e3f4f8;
        font-size: 22px;
    }

    .external-lab-page .lab-empty strong {
        display: block;
        margin-bottom: 4px;
        color: var(--lab-ink);
        font-size: 15px;
    }

    .external-lab-page .modal-content {
        overflow: hidden;
        border: 0;
        border-radius: 14px;
        box-shadow: 0 18px 55px rgba(0, 35, 58, .28);
    }

    #labDocumentViewer .modal-dialog { width: 94vw; max-width: 1180px; }
    #labDocumentViewer .modal-header { color: #fff; border: 0; background: linear-gradient(115deg,var(--lab-navy),var(--lab-blue),var(--lab-teal)); }
    #labDocumentViewer .modal-header small { display: block; opacity: .8; font-size: 9px; font-weight: 800; letter-spacing: .8px; }
    #labDocumentViewer .modal-title { margin-top: 3px; font-weight: 800; }
    #labDocumentViewer .close { color: #fff; opacity: .9; text-shadow: none; }
    #labDocumentViewer .modal-body { height: 74vh; min-height: 480px; padding: 0; background: #eaf2f5; }
    #labDocumentFrame { display: none; width: 100%; height: 100%; border: 0; background: #fff; }
    .lab-viewer-state { height: 100%; display: flex; align-items: center; justify-content: center; padding: 25px; color: #365f73; text-align: center; }
    .lab-viewer-state i { display: block; margin-bottom: 10px; color: var(--lab-blue); font-size: 30px; }
    .lab-viewer-state strong,.lab-viewer-state span { display: block; }
    .lab-viewer-state span { margin-top: 4px; color: var(--lab-muted); font-size: 12px; }

    /*
     * SweetAlert2 comparte la página con estilos de alertas heredados. Estas
     * reglas quedan limitadas a los avisos del laboratorio para evitar que el
     * ícono y el contenedor adopten dimensiones ajenas al componente.
     */
    .lab-swal-popup {
        width: min(420px, calc(100vw - 32px)) !important;
        min-width: 0 !important;
        min-height: 0 !important;
        height: auto !important;
        padding: 24px 26px 22px !important;
        border-radius: 16px !important;
        box-sizing: border-box !important;
        font-family: inherit !important;
    }

    .lab-swal-popup .swal2-icon,
    .lab-swal-popup .lab-swal-icon {
        width: 68px !important;
        min-width: 68px !important;
        max-width: 68px !important;
        height: 68px !important;
        min-height: 68px !important;
        max-height: 68px !important;
        margin: 0 auto 16px !important;
        border-width: 4px !important;
        font-size: 14px !important;
        line-height: 68px !important;
        transform: none !important;
        box-sizing: border-box !important;
    }

    /* SweetAlert2 conserva los cinco tipos de icono en el DOM. Solo el que
       tiene esta clase está activo; no debemos forzar la visualización de los demás. */
    .lab-swal-popup .swal2-icon:not(.swal2-icon-show) {
        display: none !important;
    }

    .lab-swal-popup .swal2-icon.swal2-icon-show {
        display: flex !important;
    }

    .lab-swal-popup .swal2-icon-content {
        font-size: 42px !important;
        line-height: 1 !important;
    }

    .lab-swal-popup .swal2-title,
    .lab-swal-popup .lab-swal-title {
        margin: 0 !important;
        padding: 0 !important;
        color: #07385d !important;
        font-size: 22px !important;
        font-weight: 800 !important;
        line-height: 1.25 !important;
    }

    .lab-swal-popup .swal2-html-container,
    .lab-swal-popup .swal2-content,
    .lab-swal-popup .lab-swal-text {
        margin: 9px 0 0 !important;
        padding: 0 !important;
        color: #607d8f !important;
        font-size: 14px !important;
        line-height: 1.5 !important;
    }

    .lab-swal-popup .swal2-actions,
    .lab-swal-popup .lab-swal-actions {
        width: 100% !important;
        margin: 20px 0 0 !important;
        padding: 0 !important;
        gap: 10px !important;
    }

    .lab-swal-popup .swal2-styled {
        min-width: 120px !important;
        margin: 0 !important;
        padding: 10px 18px !important;
        border-radius: 8px !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
        box-shadow: none !important;
    }

    .lab-swal-popup .swal2-styled:focus {
        box-shadow: 0 0 0 3px rgba(11, 143, 199, .2) !important;
    }

    @media (max-width: 1100px) {
        .external-lab-page .lab-files-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    @media (max-width: 767px) {
        .external-lab-page { padding: 18px 14px 32px; }
        .external-lab-page .lab-hero { align-items: flex-start; flex-direction: column; padding: 21px; }
        .external-lab-page .lab-hero h2 { font-size: 22px; }
        .external-lab-page .lab-patient-chip { width: 100%; min-width: 0; text-align: left; }
        .external-lab-page .lab-card-header { align-items: flex-start; }
        .external-lab-page .lab-files-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 480px) {
        .external-lab-page .lab-files-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="modal fade" id="labDocumentViewer" tabindex="-1" role="dialog" aria-labelledby="labDocumentViewerTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document"><div class="modal-content">
        <div class="modal-header"><div><small>DOCUMENTACIÓN CLÍNICA</small><h4 class="modal-title" id="labDocumentViewerTitle"><i class="fa fa-file-text-o"></i> Vista previa del examen</h4></div><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body"><div class="lab-viewer-state" id="labViewerState"><div><i class="fa fa-spinner fa-spin"></i><strong>Cargando documento…</strong></div></div><iframe id="labDocumentFrame" title="Vista previa del examen externo"></iframe></div>
        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button><a class="btn btn-primary" id="labViewerDownload" href="#"><i class="fa fa-download"></i> Descargar</a></div>
    </div></div>
</div>

<div class="external-lab-page">
    <div class="lab-shell">
        <div class="lab-breadcrumb">
            <a href="{{ asset('/') }}"><i class="fa fa-arrow-left"></i> Inicio</a>
            <i class="fa fa-angle-right"></i>
            <span>Historia clínica</span>
            <i class="fa fa-angle-right"></i>
            <span>Laboratorio externo</span>
        </div>

        <section class="lab-hero">
            <div class="lab-hero-main">
                <div class="lab-hero-icon"><i class="fa fa-flask"></i></div>
                <div>
                    <div class="lab-eyebrow">Documentación clínica</div>
                    <h2>Exámenes de laboratorio externo</h2>
                    <p>Adjunta resultados realizados fuera del centro médico y consulta su historial.</p>
                </div>
            </div>
            <div class="lab-patient-chip">
                <small>Paciente seleccionado</small>
                <strong>{{ $paciente->nombre1 }} {{ $paciente->apellido1 }}</strong>
            </div>
        </section>

        <section class="lab-card">
            <div class="lab-card-header">
                <div class="lab-card-title">
                    <div class="lab-title-icon"><i class="fa fa-cloud-upload"></i></div>
                    <div>
                        <h3>Cargar nuevos resultados</h3>
                        <p>Selecciona uno o varios documentos para incorporarlos a la historia clínica.</p>
                    </div>
                </div>
            </div>
            <div class="lab-card-body">
                <div class="lab-help">
                    <i class="fa fa-info-circle"></i>
                    <span>Verifica que cada archivo corresponda al paciente antes de cargarlo. Al terminar correctamente, el historial se actualizará de forma automática.</span>
                </div>

                <form method="POST" action="{{ route('laboratorio_externo.guardar') }}" enctype="multipart/form-data" class="dropzone" id="addimage">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id_paciente" value="{{ $paciente->id }}">
                    <div class="fallback"></div>
                    <div class="dz-message" data-dz-message>
                        <div class="lab-upload-icon"><i class="fa fa-files-o"></i></div>
                        <div class="lab-upload-title">Arrastra aquí los exámenes</div>
                        <div class="lab-upload-copy">o utiliza el selector para buscarlos en tu equipo</div>
                        <span class="lab-upload-button"><i class="fa fa-folder-open"></i>&nbsp; Seleccionar archivos</span>
                        <div class="lab-formats">PDF, Word, Excel, TXT e imágenes JPG, PNG o GIF</div>
                    </div>
                </form>
            </div>
        </section>

        <section class="lab-card" id="labExternalHistory">
            <div class="lab-card-header">
                <div class="lab-card-title">
                    <div class="lab-title-icon"><i class="fa fa-history"></i></div>
                    <div>
                        <h3>Historial de exámenes</h3>
                        <p>Documentos externos registrados para este paciente.</p>
                    </div>
                </div>
                <span class="lab-count">{{ count($historico) }} archivo(s)</span>
            </div>
            <div class="lab-card-body js-lab-history-body">
                @if(count($historico) > 0)
                    <div class="lab-files-grid">
                        @foreach($historico as $imagen)
                            @include('hc_admision.video._laboratorio_externo_archivo', array('imagen'=>$imagen))
                        @endforeach
                    </div>
                @else
                    <div class="lab-empty">
                        <i class="fa fa-file-text-o"></i>
                        <strong>Aún no hay exámenes externos</strong>
                        <span>Los documentos cargados aparecerán aquí con sus opciones de consulta y descarga.</span>
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>

@if(session('v3_legacy_shell', false))
<script src="{{ url('v3/plugins/dropzone/js/dropzone.min.js') }}"></script>
@endif
<script>
    function labAlert(options) {
        options = options || {};
        var customClass = options.customClass || {};
        options.width = options.width || '420px';
        options.customClass = {
            container: customClass.container || 'lab-swal-container',
            popup: customClass.popup || 'lab-swal-popup',
            icon: customClass.icon || 'lab-swal-icon',
            title: customClass.title || 'lab-swal-title',
            htmlContainer: customClass.htmlContainer || 'lab-swal-text',
            actions: customClass.actions || 'lab-swal-actions',
            confirmButton: customClass.confirmButton || 'lab-swal-confirm',
            cancelButton: customClass.cancelButton || 'lab-swal-cancel'
        };
        if (typeof window.mostrarSweetAlertV3 === 'function') return window.mostrarSweetAlertV3(options);
        if (window.Swal) return window.Swal.fire(options);
        window.alert(options.text || options.title); return Promise.resolve({isConfirmed:true, value:true});
    }

    var externalLabDropzoneOptions = {
        paramName: "foto",
        acceptedFiles: ".pdf, .doc, .docx, .txt, .xls, .xlsx, .jpg, .jpeg, .png, .gif",
        maxFilesize: 20,
        timeout: 120000,
        init: function() {
            this.on("error", function(file, response) {
                var message = 'No se pudo cargar el archivo. Revisa el documento e inténtalo nuevamente.';
                if (file && file.accepted === false) {
                    message = 'Formato no permitido. Usa PDF, Word, Excel, TXT, JPG, PNG o GIF.';
                } else if (response && typeof response === 'object' && response.message) {
                    message = response.message;
                } else if (typeof response === 'string' && response.trim() !== '' && response.indexOf('<') === -1 && response.length < 300) {
                    message = response;
                }
                labAlert({icon:'error',title:'No se pudo cargar',text:message,confirmButtonText:'Aceptar'});
            });
            this.on("success", function(file, response) {
                file._externalLabUploaded = true;
                if (response && response.html) appendExternalLabFiles(response.html);
            });
            this.on("queuecomplete", function() {
                var uploaded = this.files.some(function(file) { return file._externalLabUploaded === true; });
                if (uploaded) {
                    var dropzone = this;
                    window.setTimeout(function() { dropzone.removeAllFiles(true); }, 900);
                }
            });
        }
    };
    if (window.Dropzone) {
        Dropzone.options.addimage = externalLabDropzoneOptions;
    }

    function appendExternalLabFiles(html) {
        var history = document.getElementById('labExternalHistory');
        if (!history || !html) return;
        var body = history.querySelector('.js-lab-history-body');
        var empty = body.querySelector('.lab-empty');
        if (empty) empty.remove();
        var grid = body.querySelector('.lab-files-grid');
        if (!grid) {
            grid = document.createElement('div');
            grid.className = 'lab-files-grid';
            body.appendChild(grid);
        }
        grid.insertAdjacentHTML('afterbegin', html);
        var count = history.querySelector('.lab-count');
        if (count) count.textContent = grid.querySelectorAll('.lab-file-card').length + ' archivo(s)';
    }

    (function () {
        var modal = $('#labDocumentViewer'), frame = document.getElementById('labDocumentFrame'),
            state = document.getElementById('labViewerState'), download = document.getElementById('labViewerDownload'), objectUrl = '';

        function resetViewer() {
            frame.onload = null; frame.src = 'about:blank'; frame.style.display = 'none';
            state.style.display = 'flex';
            state.innerHTML = '<div><i class="fa fa-spinner fa-spin"></i><strong>Cargando documento…</strong></div>';
            if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = ''; }
        }

        document.addEventListener('click', function (event) {
            var preview = event.target.closest('.js-lab-preview');
            if (preview) {
                event.preventDefault(); resetViewer();
                document.getElementById('labDocumentViewerTitle').innerHTML = '<i class="fa fa-file-text-o"></i> ' + $('<div>').text(preview.getAttribute('data-name')).html();
                download.href = preview.getAttribute('data-download'); modal.modal('show');
                if (preview.getAttribute('data-previewable') !== '1') {
                    state.innerHTML = '<div><i class="fa fa-file-o"></i><strong>Vista previa no disponible</strong><span>Este formato debe descargarse para abrirlo con su aplicación correspondiente.</span></div>';
                    return;
                }
                fetch(preview.href, {credentials:'same-origin', headers:{'X-Requested-With':'XMLHttpRequest'}})
                    .then(function (response) { if (!response.ok) throw new Error('No se pudo abrir el archivo.'); return response.blob(); })
                    .then(function (blob) {
                        objectUrl = URL.createObjectURL(blob);
                        frame.onload = function () { state.style.display = 'none'; frame.style.display = 'block'; };
                        frame.src = objectUrl;
                    }).catch(function (error) {
                        state.innerHTML = '<div><i class="fa fa-exclamation-triangle"></i><strong>' + error.message + '</strong><span>Puede intentar descargar el documento.</span></div>';
                    });
                return;
            }

            var remove = event.target.closest('.js-delete-external');
            if (!remove) return;
            event.preventDefault();
            var confirmation = window.Swal || typeof window.mostrarSweetAlertV3 === 'function'
                ? labAlert({icon:'warning',title:'¿Eliminar este examen?',text:'El archivo dejará de estar disponible en el historial del paciente.',showCancelButton:true,reverseButtons:true,focusCancel:true,confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar',confirmButtonColor:'#d94d55',cancelButtonColor:'#87949e'})
                : Promise.resolve({isConfirmed:window.confirm('¿Estás seguro de que deseas eliminar este archivo?')});
            confirmation.then(function (result) {
                if (!(result && (result.isConfirmed || result.value === true))) return;
                remove.disabled = true;
                fetch(remove.getAttribute('data-url'), {
                    method:'DELETE', credentials:'same-origin',
                    headers:{'X-CSRF-TOKEN':document.querySelector('#addimage input[name="_token"]').value,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
                }).then(function (response) { return response.json().then(function (data) { if (!response.ok) throw new Error(data.message || 'No se pudo eliminar el archivo.'); return data; }); })
                  .then(function (data) {
                      var card = remove.closest('.lab-file-card'); if (card) card.remove();
                      var cards = document.querySelectorAll('.lab-file-card').length, count = document.querySelector('.lab-count');
                      if (count) count.textContent = cards + ' archivo(s)';
                      if (cards === 0) {
                          var historyBody = document.querySelector('#labExternalHistory .js-lab-history-body');
                          if (historyBody) historyBody.innerHTML = '<div class="lab-empty"><i class="fa fa-file-text-o"></i><strong>Aún no hay exámenes externos</strong><span>Los documentos cargados aparecerán aquí con sus opciones de consulta y descarga.</span></div>';
                      }
                      return labAlert({icon:'success',title:'Archivo eliminado',text:data.message,confirmButtonText:'Aceptar',confirmButtonColor:'#15966b'});
                  }).catch(function (error) {
                      remove.disabled = false;
                      labAlert({icon:'error',title:'No se pudo eliminar',text:error.message,confirmButtonText:'Aceptar'});
                  });
            });
        });

        modal.on('hidden.bs.modal', resetViewer);
    })();
</script>
@endsection
