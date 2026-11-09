<style>
    .hospital-files {
        overflow: hidden;
        border: 1px solid #d7e7ef;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15, 61, 91, .08);
    }

    .hospital-files__header {
        display: flex;
        align-items: center;
        width: 100%;
        min-height: 60px;
        padding: 12px 18px;
    }

    .hospital-files__title {
        margin: 0;
        color: #fff !important;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.25;
    }

    .hospital-files__body {
        padding: 18px !important;
    }

    .hospital-files__dropzone {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        min-height: 190px;
        padding: 24px;
        border: 2px dashed #a9d6e5;
        border-radius: 12px;
        background: #f5fbfd;
        color: #335a72;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s ease, background .2s ease, transform .2s ease;
    }

    .hospital-files__dropzone:hover,
    .hospital-files__dropzone:focus,
    .hospital-files__dropzone.is-dragover {
        border-color: #14aac0;
        outline: 0;
        background: #eaf9fb;
    }

    .hospital-files__dropzone.is-dragover {
        transform: translateY(-2px);
    }

    .hospital-files__drop-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 54px;
        height: 54px;
        margin-bottom: 12px;
        border-radius: 14px;
        background: #daf3f7;
        color: #0d90b5;
        font-size: 24px;
    }

    .hospital-files__dropzone strong {
        margin-bottom: 4px;
        color: #123f5c;
        font-size: 16px;
    }

    .hospital-files__hint {
        color: #6b8292;
        font-size: 12px;
    }

    .hospital-files__input {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
    }

    .hospital-files__selection {
        display: none;
        align-items: center;
        gap: 10px;
        width: 100%;
        max-width: 560px;
        margin-top: 16px;
        padding: 10px 12px;
        border: 1px solid #cce4ed;
        border-radius: 9px;
        background: #fff;
        text-align: left;
    }

    .hospital-files__selection.is-visible {
        display: flex;
    }

    .hospital-files__selection i {
        color: #0d90b5;
        font-size: 18px;
    }

    .hospital-files__selection-text {
        min-width: 0;
    }

    .hospital-files__selection-name,
    .hospital-files__selection-size {
        display: block;
    }

    .hospital-files__selection-name {
        overflow: hidden;
        color: #24475f;
        font-size: 12px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .hospital-files__selection-size {
        margin-top: 2px;
        color: #7a909e;
        font-size: 10px;
    }

    .hospital-files__actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 14px;
    }

    .hospital-files__upload {
        min-width: 138px;
        padding: 9px 16px;
        border: 0;
        border-radius: 9px;
        font-weight: 700;
    }

    .hospital-files__upload[disabled] {
        cursor: not-allowed;
        opacity: .55;
    }

    .hospital-files__history {
        margin-top: 22px;
    }

    @media (max-width: 575px) {
        .hospital-files__header,
        .hospital-files__body {
            padding: 12px !important;
        }

        .hospital-files__dropzone {
            min-height: 170px;
            padding: 18px;
        }

        .hospital-files__upload {
            width: 100%;
        }
    }
</style>

<div class="card hospital-files">
    <div class="card-header bg bg-primary hospital-files__header">
        <h4 class="hospital-files__title">{{trans('hospitalizacion.SubirArchivos')}}</h4>
    </div>
    <div class="card-body hospital-files__body">
        <form id="hospital-file-form" method="post" action="{{ route('hospital.guardar_formularios') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$solicitud->id}}">
            <input type="file" class="hospital-files__input" name="archivo" id="archivo" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">

            <label id="hospital-file-dropzone" class="hospital-files__dropzone" for="archivo" tabindex="0">
                <span class="hospital-files__drop-icon"><i class="fa fa-cloud-upload"></i></span>
                <strong>Arrastre el archivo aquí</strong>
                <span class="hospital-files__hint">o haga clic para seleccionarlo</span>
                <span class="hospital-files__hint">PDF, imágenes y documentos de Office · máximo 20 MB</span>

                <span id="hospital-file-selection" class="hospital-files__selection">
                    <i class="fa fa-file-o"></i>
                    <span class="hospital-files__selection-text">
                        <span id="hospital-file-name" class="hospital-files__selection-name"></span>
                        <span id="hospital-file-size" class="hospital-files__selection-size"></span>
                    </span>
                </span>
            </label>

            <div class="hospital-files__actions">
                <button id="hospital-file-upload" type="button" onclick="guardar_archivos()" class="btn btn-primary hospital-files__upload" disabled>
                    <i class="fa fa-upload" aria-hidden="true"></i> Subir archivo
                </button>
            </div>
        </form>

        <div class="hospital-files__history" id="lista"></div>
    </div>
</div>


<script type="text/javascript">
    var hospitalSelectedFile = null;

    $(document).ready(function() {
        listado_archivos();

        var dropzone = $('#hospital-file-dropzone');
        var input = $('#archivo');

        input.on('change', function() {
            seleccionarArchivo(this.files && this.files.length ? this.files[0] : null);
        });

        dropzone.on('dragenter dragover', function(event) {
            event.preventDefault();
            event.stopPropagation();
            dropzone.addClass('is-dragover');
        });

        dropzone.on('dragleave dragend drop', function(event) {
            event.preventDefault();
            event.stopPropagation();
            dropzone.removeClass('is-dragover');
        });

        dropzone.on('drop', function(event) {
            var files = event.originalEvent.dataTransfer.files;
            seleccionarArchivo(files && files.length ? files[0] : null);
        });

        dropzone.on('keydown', function(event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                input.trigger('click');
            }
        });
    });

    function seleccionarArchivo(file) {
        var extensiones = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];
        var extension = file ? file.name.split('.').pop().toLowerCase() : '';

        if (file && extensiones.indexOf(extension) === -1) {
            hospitalSelectedFile = null;
            $('#archivo').val('');
            $('#hospital-file-selection').removeClass('is-visible');
            $('#hospital-file-upload').prop('disabled', true);
            alertas('warning', 'Archivo no permitido', 'Seleccione un PDF, una imagen o un documento de Office.');
            return;
        }

        if (file && file.size > 20 * 1024 * 1024) {
            hospitalSelectedFile = null;
            $('#archivo').val('');
            $('#hospital-file-selection').removeClass('is-visible');
            $('#hospital-file-upload').prop('disabled', true);
            alertas('warning', 'Archivo demasiado grande', 'El tamaño máximo permitido es 20 MB.');
            return;
        }

        hospitalSelectedFile = file;
        if (!file) {
            $('#hospital-file-selection').removeClass('is-visible');
            $('#hospital-file-upload').prop('disabled', true);
            return;
        }

        $('#hospital-file-name').text(file.name);
        $('#hospital-file-size').text(formatearTamanoArchivo(file.size));
        $('#hospital-file-selection').addClass('is-visible');
        $('#hospital-file-upload').prop('disabled', false);
    }

    function formatearTamanoArchivo(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function limpiarArchivoSeleccionado() {
        hospitalSelectedFile = null;
        $('#archivo').val('');
        $('#hospital-file-name, #hospital-file-size').text('');
        $('#hospital-file-selection').removeClass('is-visible');
        $('#hospital-file-upload').prop('disabled', true).html('<i class="fa fa-upload" aria-hidden="true"></i> Subir archivo');
    }

    function guardar_archivos() {
        if (!hospitalSelectedFile) {
            alertas('warning', 'Seleccione un archivo', 'Arrastre un archivo o haga clic en el área de carga.');
            return;
        }

        var form = $('#hospital-file-form')[0];
        var data = new FormData(form);
        data.set('archivo', hospitalSelectedFile, hospitalSelectedFile.name);
        $.ajax({
            enctype: 'multipart/form-data',
            type: "post",
            url: `{{ route('hospital.guardar_formularios') }}`,
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            beforeSend: function() {
                $('#hospital-file-upload').prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i> Subiendo...');
                Swal.fire({
                    text: "Cargando...",
                    allowOutsideClick: false,
                    showConfirmButton: false,
                })
            },
            datatype: "html",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function(datahtml) {
                Swal.close();
                alertas(datahtml.respuesta, `${datahtml.respuesta.toUpperCase()}...`, datahtml.msj)
                if (datahtml.respuesta === 'success') {
                    limpiarArchivoSeleccionado();
                    listado_archivos();
                } else {
                    $('#hospital-file-upload').prop('disabled', false).html('<i class="fa fa-upload" aria-hidden="true"></i> Subir archivo');
                }

            },
            error: function(datahtml) {
                Swal.close();
                $('#hospital-file-upload').prop('disabled', false).html('<i class="fa fa-upload" aria-hidden="true"></i> Subir archivo');
                var response = datahtml.responseJSON || {};
                var message = response.message || 'No se pudo subir el archivo.';
                alertas('error', 'Error al cargar', message);
            }
        });
    }

    function listado_archivos() {

        $.ajax({
            type: "GET",
            url: "{{route('hospital.detalle_formularios',['id_solicitud'=>$solicitud->id])}}",
            datatype: "html",
            success: function(datahtml) {
                $("#lista").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function eliminar2(id) {
        var opcion = confirm("{{trans('contableM.estasegdeseaelimin')}}");
        if (opcion == true) {
            $.ajax({
                type: 'delete',
                url: '{{ url("hospital/eliminar/formulario")}}/' + id,
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                success: function(data) {
                    //console.log(data);
                    // alert(data);
                    location.reload();
                }
            })

        }
    }
</script>
