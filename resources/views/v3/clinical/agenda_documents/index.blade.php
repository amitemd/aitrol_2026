@include('v3.clinical.operations._styles')
@include('v3.clinical.agenda._doctor_styles')
<style>
    .dc-page {
        padding: 18px
    }

    .dc-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap
    }

    .dc-hero-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap
    }

    .dc-meta {
        display: grid;
        grid-template-columns: repeat(4, minmax(160px, 1fr));
        gap: 10px;
        margin: 14px 0
    }

    .dc-meta div {
        background: #f5fbfd;
        border: 1px solid #d5e8ef;
        border-radius: 9px;
        padding: 9px
    }

    .dc-grid {
        display: grid;
        grid-template-columns: minmax(760px, 2fr) minmax(320px, 1fr);
        gap: 14px
    }

    .dc-table {
        width: 100%;
        font-size: 12px
    }

    .dc-table th {
        background: #edf7fa;
        padding: 7px
    }

    .dc-table td {
        padding: 6px;
        border-bottom: 1px solid #e1edf1;
        vertical-align: middle
    }

    .dc-file-actions {
        display: flex;
        gap: 4px;
        align-items: center;
        flex-wrap: nowrap
    }

    .dc-icon-btn {
        width: 28px !important;
        min-width: 28px !important;
        max-width: 28px !important;
        height: 28px !important;
        min-height: 28px !important;
        max-height: 28px !important;
        padding: 0 !important;
        margin: 0 !important;
        flex: 0 0 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        border-radius: 5px;
        font-size: 12px !important;
        line-height: 1 !important
    }

    .dc-icon-btn>i {
        width: 13px;
        height: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px !important;
        line-height: 13px !important
    }

    .dc-uploader {
        min-width: 120px;
        max-width: 155px;
        line-height: 1.2
    }

    .dc-preview {
        width: 100%;
        height: 590px;
        border: 1px solid #cfe1e8;
        border-radius: 10px;
        background: #fff
    }

    .dc-empty {
        padding: 22px;
        text-align: center;
        color: #6b8190
    }

    .dc-check {
        width: 17px;
        height: 17px
    }

    .dc-disabled {
        opacity: .55
    }

    .dc-upload-error {
        display: none;
        margin: 12px 0 0;
        border: 0;
        border-radius: 9px;
        font-size: 12px
    }

    .dc-upload-name {
        margin: 0;
        color: #123f5b;
        font-size: 13px;
        font-weight: 800
    }

    .dc-upload-dialog {
        width: calc(100% - 30px);
        max-width: 590px;
        margin: 46px auto
    }

    .dc-upload-dialog .modal-content {
        overflow: hidden;
        border: 0;
        border-radius: 15px;
        box-shadow: 0 24px 60px rgba(7, 42, 61, .28)
    }

    .dc-upload-dialog .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 18px;
        border: 0;
        color: #fff;
        background: linear-gradient(115deg, #087ba7, #13a6ad)
    }

    .dc-upload-heading {
        display: flex;
        align-items: center;
        gap: 11px
    }

    .dc-upload-heading>i {
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        border: 1px solid rgba(255,255,255,.32);
        border-radius: 10px;
        background: rgba(255,255,255,.13)
    }

    .dc-upload-heading small {
        display: block;
        margin-bottom: 1px;
        color: rgba(255,255,255,.82);
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .8px
    }

    .dc-upload-dialog .modal-title {
        margin: 0;
        color: #fff;
        font-size: 17px;
        font-weight: 800
    }

    .dc-upload-dialog .close {
        width: 32px;
        height: 32px;
        margin: 0;
        padding: 0;
        border-radius: 8px;
        color: #fff;
        text-shadow: none;
        opacity: .9
    }

    .dc-upload-dialog .close:hover {
        color: #fff;
        background: rgba(255,255,255,.14);
        opacity: 1
    }

    .dc-upload-dialog .modal-body {
        padding: 20px
    }

    .dc-upload-document {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        margin-bottom: 14px;
        border: 1px solid #d5e7ed;
        border-radius: 10px;
        background: #f4fafc
    }

    .dc-upload-document>i {
        display: grid;
        place-items: center;
        flex: 0 0 31px;
        width: 31px;
        height: 31px;
        border-radius: 8px;
        color: #0a88ae;
        background: #daf2f7
    }

    .dc-upload-document small {
        display: block;
        color: #6d8593;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase
    }

    .dc-upload-zone {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 150px;
        padding: 20px;
        margin: 0;
        border: 2px dashed #a9ccd8;
        border-radius: 12px;
        background: #fbfdfe;
        cursor: pointer;
        text-align: center;
        transition: border-color .16s ease, background .16s ease, box-shadow .16s ease
    }

    .dc-upload-zone:hover,
    .dc-upload-zone.is-dragging {
        border-color: #0b91b7;
        background: #f1fbfd;
        box-shadow: 0 0 0 4px rgba(11,145,183,.08)
    }

    .dc-upload-zone-icon {
        display: grid;
        place-items: center;
        width: 46px;
        height: 46px;
        margin: 0 auto 9px;
        border-radius: 50%;
        color: #078bb2;
        background: #def4f8;
        font-size: 18px
    }

    .dc-upload-zone strong {
        display: block;
        color: #173f57;
        font-size: 13px
    }

    .dc-upload-zone span {
        display: block;
        margin-top: 4px;
        color: #738b99;
        font-size: 10.5px
    }

    .dc-upload-input {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        pointer-events: none
    }

    .dc-upload-selection {
        display: none;
        align-items: center;
        gap: 9px;
        padding: 9px 11px;
        margin-top: 11px;
        border-radius: 9px;
        color: #176b48;
        background: #edf9f3;
        font-size: 11px;
        font-weight: 700
    }

    .dc-upload-selection.is-visible {
        display: flex
    }

    .dc-upload-selection span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap
    }

    .dc-upload-dialog .modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        padding: 12px 18px;
        border-top: 1px solid #dce8ed;
        background: #f8fbfc
    }

    .dc-upload-dialog .modal-footer .btn {
        min-width: 105px;
        min-height: 36px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800
    }

    @media(max-width:1100px) {
        .dc-grid {
            grid-template-columns: 1fr
        }

        .dc-meta {
            grid-template-columns: repeat(2, 1fr)
        }
    }
</style>
<div class="co-page da-page dc-page">
    <section class="co-hero">
        <div class="co-hero__main">
            <div class="co-hero__icon"><i class="fas fa-folder-open"></i></div>
            <div>
                <div class="co-eyebrow">CONTROL DOCUMENTAL · V3</div>
                <h1>Documentos de la agenda</h1>
                <p>{{ $patient->apellido1 }} {{ $patient->apellido2 }} {{ $patient->nombre1 }} {{ $patient->nombre2 }}
                </p>
            </div>
        </div>
        <div class="dc-hero-actions">
            @if ($canManageRequirements)
                <a class="btn btn-light"
                    href="{{ route('v3.agenda-document-requirements.index', ['seguro' => $agenda->id_seguro, 'proc_consul' => $agenda->proc_consul, 'doctor' => $doctor, 'appointment' => $agenda->id]) }}">
                    <i class="fas fa-cog"></i> Gestionar documentos
                </a>
            @endif
            <a class="btn btn-light"
                href="{{ route('v3.doctor-agenda.calendar', ['doctor' => $doctor, 'fecha' => substr($agenda->fechaini, 0, 10)]) }}"><i
                    class="fas fa-arrow-left"></i> Agenda</a>
        </div>
    </section>
    @if (session('agenda_success'))
        <div class="alert alert-success">{{ session('agenda_success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    <section class="co-card" style="padding:16px">
        <div class="dc-actions">
            <a id="dcContinue" class="btn btn-primary"
                href="{{ route('v3.doctor-agenda.calendar', ['doctor' => $doctor, 'fecha' => substr($agenda->fechaini, 0, 10)]) }}"><i
                    class="fas fa-chevron-right"></i> Continuar</a>
        </div>
        <div id="dcRequiredWarning" class="alert alert-warning" style="display:none;margin-top:10px">Debe completar
            todos los documentos obligatorios (*) antes de continuar.</div>
        <div class="dc-meta">
            <div><b>Paciente</b><br>{{ $patient->id }} - {{ $patient->apellido1 }} {{ $patient->nombre1 }}</div>
            <div><b>Nacimiento</b><br>{{ $patient->fecha_nacimiento ?: 'No registrado' }}</div>
            <div><b>Seguro</b><br>{{ $insurance ? $insurance->nombre : 'No registrado' }}</div>
            <div><b>Fecha y procedimiento</b><br>{{ $agenda->fechaini }} · {{ $procedures }}</div>
        </div>
        <div class="dc-grid">
            <div class="table-responsive">
                <table class="dc-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Documento requerido</th>
                            <th>Listo</th>
                            <th>Entrega</th>
                            <th>Subido por</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $i=>$doc)
                            @php($archive = $archives->get($doc->id))
                            @php($isPatientIdCopy = (int)$doc->msp === 4)
                            @php($hasPatientIdCopy = $isPatientIdCopy && $patientIdCopy && !($archive && $archive->archivo))
                            @php($uploader = $archive && ($archive->archivo || $hasPatientIdCopy) ? $uploaders->get($archive->id_usuariomod) : null)
                            <tr data-document-row="{{ $doc->id }}">
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    @if ($doc->link)
                                        <a href="{{ $doc->link }}"
                                        target="_blank"><b>{{ $doc->nombre }}{{ !empty($doc->agenda_documento_obligatorio) && strpos($doc->nombre, '(*)') === false ? ' (*)' : '' }}</b></a>
                                    @elseif($doc->sistema && $doc->formato)
                                        <a href="{{ route('v3.agenda-documents.generated', ['doctor' => $doctor, 'appointment' => $agenda->id, 'document' => $doc->id]) }}" target="_blank" rel="noopener"><b>{{ $doc->nombre }}{{ !empty($doc->agenda_documento_obligatorio) && strpos($doc->nombre, '(*)') === false ? ' (*)' : '' }}</b></a>
                                    @else<b
                                            class="js-document-name">{{ $doc->nombre }}{{ !empty($doc->agenda_documento_obligatorio) && strpos($doc->nombre, '(*)') === false ? ' (*)' : '' }}</b>
                                        @endif @if ($doc->tipo_documento)
                                            <br><small>{{ $doc->tipo_documento }}</small>
                                        @endif
                                </td>
                                <td><input class="dc-check js-doc-toggle" type="checkbox"
                                        data-required="{{ !empty($doc->agenda_documento_obligatorio) ? 1 : 0 }}"
                                        {{ $archive && $archive->estado ? 'checked' : '' }}
                                        data-url="{{ route('v3.agenda-documents.toggle', ['doctor' => $doctor, 'appointment' => $agenda->id, 'document' => $doc->id]) }}">
                                </td>
                                <td class="js-delivery-date">{{ $archive ? $archive->fecha_entrega : 'Pendiente' }}</td>
                                <td class="dc-uploader js-uploader">{{ $uploader ? trim($uploader->nombre1.' '.$uploader->apellido1) : '—' }}</td>
                                <td>
                                    <div class="dc-file-actions">
                                        @if ($archive && $archive->archivo)
                                            <a class="btn btn-info btn-xs dc-icon-btn js-preview js-uploaded-preview" title="Vista previa" aria-label="Vista previa"
                                                href="{{ route('v3.agenda-documents.file', ['doctor' => $doctor, 'appointment' => $agenda->id, 'document' => $doc->id]) }}"><i
                                                    class="fas fa-eye"></i></a>
                                            <a class="btn btn-secondary btn-xs dc-icon-btn js-uploaded-download" title="Descargar archivo" aria-label="Descargar archivo"
                                                href="{{ route('v3.agenda-documents.file', ['doctor' => $doctor, 'appointment' => $agenda->id, 'document' => $doc->id, 'descargar' => 1]) }}"><i
                                                    class="fas fa-download"></i></a>
                                        @elseif ($hasPatientIdCopy)
                                            <a class="btn btn-info btn-xs dc-icon-btn js-preview js-uploaded-preview" title="Vista previa de identificación" aria-label="Vista previa de identificación"
                                                href="{{ route('v3.agenda-documents.patient-copy', ['doctor' => $doctor, 'appointment' => $agenda->id]) }}"><i
                                                    class="fas fa-eye"></i></a>
                                            <a class="btn btn-secondary btn-xs dc-icon-btn js-uploaded-download" title="Descargar copia de identificación" aria-label="Descargar copia de identificación"
                                                href="{{ route('v3.agenda-documents.patient-copy', ['doctor' => $doctor, 'appointment' => $agenda->id, 'descargar' => 1]) }}"><i
                                                    class="fas fa-id-card"></i></a>
                                        @elseif($doc->sistema && $doc->formato)
                                            <a class="btn btn-info btn-xs dc-icon-btn js-preview js-generated-preview" title="Vista previa" aria-label="Vista previa"
                                                href="{{ route('v3.agenda-documents.generated', ['doctor' => $doctor, 'appointment' => $agenda->id, 'document' => $doc->id]) }}"><i
                                                    class="fas fa-eye"></i></a>
                                        @else
                                            <button type="button"
                                                class="btn btn-info btn-xs dc-icon-btn dc-disabled js-empty-preview" title="Sin archivo para visualizar" aria-label="Vista previa no disponible" disabled><i
                                                    class="fas fa-eye"></i></button>
                                        @endif
                                        @if ($doc->sistema && $doc->formato)
                                            <a class="btn btn-primary btn-xs dc-icon-btn" title="Descargar documento original" aria-label="Descargar documento original"
                                                href="{{ route('v3.agenda-documents.generated', ['doctor' => $doctor, 'appointment' => $agenda->id, 'document' => $doc->id, 'descargar' => 1]) }}"><i
                                                    class="fas fa-file-download"></i></a>
                                        @endif
                                        <button type="button" class="btn btn-success btn-xs dc-icon-btn js-open-upload" title="{{ ($archive && $archive->archivo) || $hasPatientIdCopy ? 'Actualizar archivo' : 'Subir archivo' }}" aria-label="{{ ($archive && $archive->archivo) || $hasPatientIdCopy ? 'Actualizar archivo' : 'Subir archivo' }}"
                                            data-url="{{ route('v3.agenda-documents.upload', ['doctor' => $doctor, 'appointment' => $agenda->id, 'document' => $doc->id]) }}"
                                            data-document="{{ $doc->nombre }}"
                                            data-has-file="{{ ($archive && $archive->archivo) || $hasPatientIdCopy ? 1 : 0 }}"><i
                                                class="fas {{ ($archive && $archive->archivo) || $hasPatientIdCopy ? 'fa-sync-alt' : 'fa-upload' }}"></i>
                                            <span class="sr-only">{{ ($archive && $archive->archivo) || $hasPatientIdCopy ? 'Actualizar archivo' : 'Subir archivo' }}</span></button>
                                    </div>
                                </td>
                            </tr>
                        @empty<tr>
                                <td colspan="6" class="dc-empty">No hay documentos configurados para esta empresa,
                                    seguro y tipo de agenda. Configúrelos en el catálogo documental.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                <h4>Vista previa</h4>
                <div id="dcPlaceholder" class="dc-empty">Seleccione “Vista previa” para visualizar el documento.</div>
                <iframe id="dcPreview" class="dc-preview" hidden></iframe>
            </div>
        </div>
    </section>
</div>
<div class="modal fade" id="dcUploadModal" tabindex="-1" role="dialog" aria-labelledby="dcUploadTitle"
    aria-hidden="true">
    <div class="modal-dialog dc-upload-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="dc-upload-heading">
                    <i class="fas fa-file-upload"></i>
                    <div><small>CONTROL DOCUMENTAL</small><h5 class="modal-title" id="dcUploadTitle">Subir documento</h5></div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="dcUploadForm" enctype="multipart/form-data">{{ csrf_field() }}
                <div class="modal-body">
                    <div class="dc-upload-document">
                        <i class="far fa-file-alt"></i>
                        <div><small>Documento seleccionado</small><p class="dc-upload-name" id="dcUploadName"></p></div>
                    </div>
                    <label class="dc-upload-zone" id="dcUploadZone" for="dcUploadFile">
                        <input class="dc-upload-input" id="dcUploadFile" type="file" name="archivo"
                            accept=".pdf,application/pdf" required>
                        <span>
                            <i class="fas fa-cloud-upload-alt dc-upload-zone-icon"></i>
                            <strong>Selecciona o arrastra el archivo PDF</strong>
                            <span>Únicamente archivos PDF de hasta 10 MB</span>
                        </span>
                    </label>
                    <div class="dc-upload-selection" id="dcUploadSelection">
                        <i class="fas fa-check-circle"></i><span id="dcUploadSelectionName"></span>
                    </div>
                    <div class="alert alert-danger dc-upload-error" id="dcUploadError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="dcUploadSubmit">
                        <i class="fas fa-upload"></i> <span>Subir archivo</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    (function() {
        var metaToken = document.querySelector('meta[name="csrf-token"]'),
            inputToken = document.querySelector('input[name="_token"]'),
            token = metaToken ? metaToken.content : (inputToken ? inputToken.value : ''),
            continuar = document.getElementById('dcContinue'),
            aviso = document.getElementById('dcRequiredWarning'),
            modal = document.getElementById('dcUploadModal'),
            form = document.getElementById('dcUploadForm'),
            file = document.getElementById('dcUploadFile'),
            uploadZone = document.getElementById('dcUploadZone'),
            uploadSelection = document.getElementById('dcUploadSelection'),
            uploadSelectionName = document.getElementById('dcUploadSelectionName'),
            uploadTitle = document.getElementById('dcUploadTitle'),
            errorBox = document.getElementById('dcUploadError'),
            submit = document.getElementById('dcUploadSubmit'),
            activeButton = null;

        function validar() {
            var faltan = [].slice.call(document.querySelectorAll('.js-doc-toggle[data-required="1"]')).some(
                function(c) {
                    return !c.checked;
                });
            continuar.classList.toggle('disabled', faltan);
            continuar.setAttribute('aria-disabled', faltan ? 'true' : 'false');
            aviso.style.display = faltan ? 'block' : 'none';
        }

        function showModal() {
            if (window.jQuery && jQuery.fn.modal) jQuery(modal).modal('show');
        }

        function hideModal() {
            if (window.jQuery && jQuery.fn.modal) jQuery(modal).modal('hide');
        }

        function message(data) {
            if (data && data.errors) {
                var keys = Object.keys(data.errors);
                if (keys.length) return data.errors[keys[0]][0];
            }
            if (data && data.archivo) return Array.isArray(data.archivo) ? data.archivo[0] : data.archivo;
            return data && (data.message || data.error) ? (data.message || data.error) :
                'No se pudo subir el documento.';
        }

        function showUploadError(text) {
            errorBox.textContent = text;
            errorBox.style.display = 'block';
        }

        function selectedFileLabel(selected) {
            var size = selected.size / (1024 * 1024);
            return selected.name + ' · ' + size.toFixed(size < 1 ? 2 : 1) + ' MB';
        }

        function refreshSelectedFile() {
            errorBox.style.display = 'none';
            if (!file.files.length) {
                uploadSelection.classList.remove('is-visible');
                uploadSelectionName.textContent = '';
                return;
            }
            var selected = file.files[0],
                isPdf = selected.type === 'application/pdf' || /\.pdf$/i.test(selected.name);
            if (!isPdf) {
                file.value = '';
                uploadSelection.classList.remove('is-visible');
                showUploadError('Selecciona un archivo en formato PDF.');
                return;
            }
            if (selected.size > 10 * 1024 * 1024) {
                file.value = '';
                uploadSelection.classList.remove('is-visible');
                showUploadError('El archivo supera el tamaño máximo permitido de 10 MB.');
                return;
            }
            uploadSelectionName.textContent = selectedFileLabel(selected);
            uploadSelection.classList.add('is-visible');
        }

        file.addEventListener('change', refreshSelectedFile);
        ['dragenter', 'dragover'].forEach(function(eventName) {
            uploadZone.addEventListener(eventName, function(event) {
                event.preventDefault();
                uploadZone.classList.add('is-dragging');
            });
        });
        ['dragleave', 'drop'].forEach(function(eventName) {
            uploadZone.addEventListener(eventName, function(event) {
                event.preventDefault();
                uploadZone.classList.remove('is-dragging');
            });
        });
        uploadZone.addEventListener('drop', function(event) {
            if (!event.dataTransfer || !event.dataTransfer.files.length) return;
            try {
                file.files = event.dataTransfer.files;
            } catch (ignore) {
                return;
            }
            refreshSelectedFile();
        });

        validar();
        document.addEventListener('change', function(e) {
            if (!e.target.matches('.js-doc-toggle')) return;
            var c = e.target,
                previous = !c.checked;
            c.disabled = true;
            validar();
            fetch(c.dataset.url, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    estado: c.checked ? 1 : 0
                })
            }).then(function(r) {
                return r.json().catch(function() {
                    return {};
                }).then(function(data) {
                    if (!r.ok || !data.ok) throw new Error(data.message ||
                        'No se pudo actualizar el documento.');
                    return data;
                });
            }).then(function(data) {
                c.checked = !!data.checked;
            }).catch(function(error) {
                c.checked = previous;
                alert(error.message || 'No se pudo actualizar el documento.');
            }).then(function() {
                c.disabled = false;
                validar();
            });
        });
        continuar.addEventListener('click', function(e) {
            if (continuar.getAttribute('aria-disabled') === 'true') e.preventDefault();
        });
        document.addEventListener('click', function(e) {
            var upload = e.target.closest('.js-open-upload');
            if (upload) {
                activeButton = upload;
                form.action = upload.dataset.url;
                form.reset();
                errorBox.style.display = 'none';
                uploadSelection.classList.remove('is-visible');
                uploadSelectionName.textContent = '';
                uploadTitle.textContent = upload.dataset.hasFile === '1' ? 'Actualizar documento' : 'Subir documento';
                document.getElementById('dcUploadName').textContent = upload.dataset.document;
                document.querySelector('#dcUploadSubmit span').textContent = upload.dataset.hasFile ===
                    '1' ? 'Actualizar archivo' : 'Subir archivo';
                document.querySelector('#dcUploadSubmit>i').className = upload.dataset.hasFile ===
                    '1' ? 'fas fa-sync-alt' : 'fas fa-upload';
                showModal();
                return;
            }
            var a = e.target.closest('.js-preview');
            if (!a) return;
            e.preventDefault();
            document.getElementById('dcPlaceholder').hidden = true;
            var f = document.getElementById('dcPreview');
            f.src = a.href;
            f.hidden = false;
        });
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!activeButton || !file.files.length) {
                showUploadError('Selecciona el archivo PDF que deseas guardar.');
                return;
            }
            var data = new FormData(form),
                label = submit.querySelector('span'),
                submitIcon = submit.querySelector('i');
            submit.disabled = true;
            submitIcon.className = 'fas fa-spinner fa-spin';
            label.textContent = 'Guardando...';
            errorBox.style.display = 'none';
            fetch(form.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: data
            }).then(function(r) {
                return r.json().catch(function() {
                    return {};
                }).then(function(json) {
                    if (!r.ok || !json.ok) throw new Error(message(json));
                    return json;
                });
            }).then(function(json) {
                var row = activeButton.closest('tr'),
                    actions = row.querySelector('.dc-file-actions'),
                    oldPreview = actions.querySelector(
                        '.js-uploaded-preview,.js-generated-preview,.js-empty-preview'),
                    oldDownload = actions.querySelector('.js-uploaded-download');
                var preview = document.createElement('a');
                preview.className = 'btn btn-info btn-xs dc-icon-btn js-preview js-uploaded-preview';
                preview.href = json.preview_url;
                preview.title = 'Vista previa';
                preview.setAttribute('aria-label', 'Vista previa');
                preview.innerHTML = '<i class="fas fa-eye"></i>';
                oldPreview.replaceWith(preview);
                if (oldDownload) {
                    oldDownload.href = json.download_url;
                } else {
                    var download = document.createElement('a');
                    download.className = 'btn btn-secondary btn-xs dc-icon-btn js-uploaded-download';
                    download.href = json.download_url;
                    download.title = 'Descargar archivo';
                    download.setAttribute('aria-label', 'Descargar archivo');
                    download.innerHTML = '<i class="fas fa-download"></i>';
                    preview.insertAdjacentElement('afterend', download);
                }
                row.querySelector('.js-delivery-date').textContent = json.fecha_entrega;
                row.querySelector('.js-uploader').textContent = json.uploader_name || '—';
                var check = row.querySelector('.js-doc-toggle');
                check.checked = true;
                activeButton.dataset.hasFile = '1';
                activeButton.title = 'Actualizar archivo';
                activeButton.setAttribute('aria-label', 'Actualizar archivo');
                activeButton.querySelector('i').className = 'fas fa-sync-alt';
                activeButton.querySelector('span').textContent = 'Actualizar archivo';
                validar();
                hideModal();
                var frame = document.getElementById('dcPreview');
                if (!frame.hidden) {
                    frame.src = json.preview_url + '?t=' + Date.now();
                }
            }).catch(function(error) {
                errorBox.textContent = error.message;
                errorBox.style.display = 'block';
            }).then(function() {
                submit.disabled = false;
                submitIcon.className = activeButton && activeButton.dataset.hasFile === '1' ?
                    'fas fa-sync-alt' : 'fas fa-upload';
                label.textContent = activeButton && activeButton.dataset.hasFile === '1' ?
                    'Actualizar archivo' : 'Subir archivo';
            });
        });
    })();
</script>
