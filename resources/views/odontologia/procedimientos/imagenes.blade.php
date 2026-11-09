<style>
    .odont-captura-inline { overflow: hidden; border: 1px solid #c9dee8; border-radius: 10px; background: linear-gradient(145deg, #fbfeff, #edf7fa); padding: 14px 18px 20px; }
    .odont-captura-titulo { margin: -14px -18px 14px; padding: 13px 20px; background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%); color: #fff; font-size: 18px; }
    .odont-captura-box { overflow: hidden; margin-bottom: 14px; border: 1px solid #c9dee8; border-radius: 10px; background: #fff; box-shadow: 0 5px 14px rgba(0,74,117,.07); }
    .odont-captura-box-header { padding: 10px; background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%); color: #fff; text-align: center; font-size: 16px; }
    .odont-captura-box-body { min-height: 70px; padding: 14px; }
    .odont-captura-video { width: 100%; min-height: 245px; max-height: 430px; background: #101820; object-fit: contain; }
    .odont-captura-contador { margin-bottom: 10px; font-size: 17px; }
    .odont-captura-contador span { padding: 5px 7px; background: #f44336; color: #fff; font-weight: 700; }
    .odont-captura-accion { width: 100%; margin-top: 12px; }
    .odont-captura-drop { min-height: 180px; padding: 65px 20px; border: 2px solid #aaa; text-align: center; cursor: pointer; }
    .odont-captura-drop.arrastrando { border-color: #064fc7; background: #eef6fc; }
    .odont-captura-miniatura { margin-bottom: 10px; text-align: center; }
    .odont-captura-miniatura img { width: 100%; height: 115px; object-fit: contain; }
    .odont-captura-historial img { width: 100%; height: 84px; object-fit: contain; }
    .odont-captura-espera { width: 100%; padding: 11px; border: 1px solid #f44336; color: #f44336; text-align: center; }
</style>

<section class="odont-captura-inline" id="odont-captura-inline-{{ $procedimiento->id }}">
    <div class="odont-captura-titulo"><i class="fa fa-picture-o"></i> IMÁGENES</div>

    <div class="odont-captura-box">
        <div class="odont-captura-box-header">Guardado de Imagenes y Video</div>
        <div class="odont-captura-box-body">
            <div class="row">
                <div class="col-md-9">
                    <video id="odont-captura-video-{{ $procedimiento->id }}" class="odont-captura-video" autoplay muted playsinline></video>
                    <canvas id="odont-captura-canvas-{{ $procedimiento->id }}" width="1280" height="720" style="display:none;"></canvas>
                </div>
                <div class="col-md-3">
                    <div class="odont-captura-contador">Imagenes Capturadas: <span id="odont-cimagenes-{{ $procedimiento->id }}">{{ $cimagenes }}</span></div>
                    <div class="odont-captura-contador">Videos Capturados: <span id="odont-cvideo-{{ $procedimiento->id }}">{{ $cvideo }}</span></div>
                    <div class="odont-captura-espera" id="odont-captura-estado-{{ $procedimiento->id }}">En Espera</div>
                    <button type="button" class="btn btn-primary odont-captura-accion" id="odont-captura-foto-{{ $procedimiento->id }}">Tomar Foto</button>
                    <button type="button" class="btn btn-primary odont-captura-accion" id="odont-captura-grabar-{{ $procedimiento->id }}">Start Recording</button>
                </div>
            </div>
        </div>
    </div>

    <div class="odont-captura-box">
        <div class="odont-captura-box-header">Imagenes del Procedimiento</div>
        <div class="odont-captura-box-body">
            <div class="row" id="odont-captura-galeria-{{ $procedimiento->id }}">
                @forelse($imagenes as $imagen)
                    @php
                        $extension = strtolower(pathinfo((string) $imagen->nombre, PATHINFO_EXTENSION));
                        $esImagen = in_array($extension, array('jpg', 'jpeg', 'png', 'bmp', 'jpe', 'jfif', 'tiff', 'tif'));
                    @endphp
                    <div class="col-md-3 col-sm-4 col-xs-6 odont-captura-miniatura">
                        <a class="odont-archivo-modal"
                           data-url="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}"
                           href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                            @if($esImagen)
                                <img src="{{ asset('hc_ima/'.$imagen->nombre) }}" alt="Imagen del procedimiento">
                            @elseif(in_array($extension, array('mp4', 'webm')))
                                <img src="{{ asset('hospital/lib/fontawesome/svgs/solid/video.svg') }}" alt="Video del procedimiento">
                            @elseif($extension === 'pdf')
                                <img src="{{ asset('hc4/img/pdf.png') }}" alt="Documento del procedimiento">
                            @else
                                <img src="{{ asset('hc4/img/office.png') }}" alt="Archivo del procedimiento">
                            @endif
                        </a>
                    </div>
                @empty
                    <div class="col-xs-12 text-center text-muted" style="padding: 18px;">No hay imágenes ni videos capturados.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="odont-captura-box">
        <div class="odont-captura-box-body">
            <label>Ingreso de Imágenes del Procedimiento</label>
            <input type="file" id="odont-captura-archivos-{{ $procedimiento->id }}" accept=".jpg,.jpeg,.png,.bmp,.jpe,.jfif,.tiff,.tif,.dcm" multiple style="display:none;">
            <div class="odont-captura-drop" id="odont-captura-drop-{{ $procedimiento->id }}">Inserte aqui tus archivos</div>
        </div>
    </div>

    <div class="odont-captura-box odont-captura-historial">
        <div class="odont-captura-box-body">
            <h4>Historial de Imágenes del Paciente</h4>
            <form id="odont-captura-historial-form-{{ $procedimiento->id }}" method="POST" action="{{ route('hc_video.nuevas_ima') }}">
                {{ csrf_field() }}
                <input type="hidden" name="id_hc_protocolo" value="{{ $protocolo->id }}">
                <input type="hidden" name="id_paciente" value="{{ $procedimiento->id_paciente }}">
                <div class="row">
                    @foreach($imagenesPaciente as $imagen)
                        @php
                            $extension = strtolower(pathinfo((string) $imagen->nombre, PATHINFO_EXTENSION));
                            $esImagen = in_array($extension, array('jpg', 'jpeg', 'png', 'bmp', 'jpe', 'jfif', 'tiff', 'tif'));
                        @endphp
                        <div class="col-md-2 col-sm-3 col-xs-4 text-center" style="margin-bottom: 12px;">
                            <label style="cursor:pointer; width:100%;">
                                @if($esImagen)
                                    <img src="{{ asset('hc_ima/'.$imagen->nombre) }}" alt="Imagen anterior">
                                @elseif(in_array($extension, array('mp4', 'webm')))
                                    <img src="{{ asset('hospital/lib/fontawesome/svgs/solid/video.svg') }}" alt="Video anterior">
                                @elseif($extension === 'pdf')
                                    <img src="{{ asset('hc4/img/pdf.png') }}" alt="Documento anterior">
                                @else
                                    <img src="{{ asset('hc4/img/office.png') }}" alt="Archivo anterior">
                                @endif
                                <input type="checkbox" name="image[]" value="{{ $imagen->id }}">
                                <small>{{ substr((string) $imagen->created_at, 0, 10) }}</small>
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-info" id="odont-captura-agregar-{{ $procedimiento->id }}">Agregar</button>
                    <button type="submit" class="btn btn-info" formaction="{{ route('hc_video.descargar_zip') }}">Descargar Seleccionados</button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
(function () {
    var idProcedimiento = {{ (int) $procedimiento->id }};
    var idProtocolo = {{ (int) $protocolo->id }};
    var contenedor = $('#odont-proc-captura-' + idProcedimiento);
    var video = document.getElementById('odont-captura-video-' + idProcedimiento);
    var canvas = document.getElementById('odont-captura-canvas-' + idProcedimiento);
    var estado = $('#odont-captura-estado-' + idProcedimiento);
    var stream = null;
    var grabador = null;
    var fragmentos = [];

    function detener() {
        if (grabador && grabador.state !== 'inactive') {
            grabador.stop();
        }
        if (stream && stream.getTracks) {
            stream.getTracks().forEach(function (track) { track.stop(); });
        }
        stream = null;
        if (video) { video.srcObject = null; }
    }
    window.odontologiaCapturaActivaCerrar = detener;

    function recargar() {
        detener();
        $.get("{{ route((isset($prefijoModulo) ? $prefijoModulo : 'odontologia').'.procedimientos.imagenes', ['id' => $procedimiento->id]) }}", function (html) {
            contenedor.html(html);
        }).fail(function () {
            Swal.fire('Error', 'No se pudo actualizar la sección de imágenes.', 'error');
        });
    }
    window.odontologiaCapturaRecargar = recargar;

    function iniciarCamara() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            estado.text('Cámara no disponible');
            return;
        }
        navigator.mediaDevices.getUserMedia({video: true, audio: true}).then(function (mediaStream) {
            stream = mediaStream;
            video.srcObject = mediaStream;
            return video.play();
        }).catch(function () {
            return navigator.mediaDevices.getUserMedia({video: true, audio: false}).then(function (mediaStream) {
                stream = mediaStream;
                video.srcObject = mediaStream;
                estado.text('Cámara activa sin micrófono');
                return video.play();
            }).catch(function () {
                estado.text('Sin acceso a cámara');
            });
        });
    }

    $('#odont-captura-foto-' + idProcedimiento).on('click', function () {
        if (!video.videoWidth) {
            Swal.fire('Cámara no disponible', 'Espere a que la cámara inicie o revise sus permisos.', 'warning');
            return;
        }
        canvas.width = 1280;
        canvas.height = 720;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        estado.text('Guardando foto...');
        $.ajax({
            type: 'POST',
            url: "{{ route('hc_video.guardado_foto', ['id_protocolo' => $protocolo->id]) }}",
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            data: {imgBase64: canvas.toDataURL('image/png')}
        }).done(recargar).fail(function () {
            estado.text('Error al guardar');
            Swal.fire('Error', 'No se pudo guardar la fotografía.', 'error');
        });
    });

    $('#odont-captura-grabar-' + idProcedimiento).on('click', function () {
        var boton = $(this);
        if (!stream || typeof MediaRecorder === 'undefined') {
            Swal.fire('Video no disponible', 'Espere a que la cámara inicie o revise los permisos.', 'warning');
            return;
        }
        if (grabador && grabador.state === 'recording') {
            grabador.stop();
            boton.text('Start Recording');
            estado.text('Guardando video...');
            return;
        }
        fragmentos = [];
        var tipoVideo = '';
        if (MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported('video/webm;codecs=vp8,opus')) {
            tipoVideo = 'video/webm;codecs=vp8,opus';
        } else if (MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported('video/webm')) {
            tipoVideo = 'video/webm';
        }
        try {
            grabador = tipoVideo
                ? new MediaRecorder(stream, {mimeType: tipoVideo, videoBitsPerSecond: 1500000})
                : new MediaRecorder(stream, {videoBitsPerSecond: 1500000});
        } catch (error) {
            estado.text('Video no disponible');
            Swal.fire('Video no disponible', 'Este navegador no pudo iniciar la grabación.', 'warning');
            return;
        }
        grabador.ondataavailable = function (evento) {
            if (evento.data && evento.data.size) { fragmentos.push(evento.data); }
        };
        grabador.onstop = function () {
            var mime = grabador.mimeType || 'video/webm';
            var videoGrabado = new Blob(fragmentos, {type: mime});
            var tamanoParte = 1024 * 1024;
            var totalPartes = Math.max(1, Math.ceil(videoGrabado.size / tamanoParte));
            var idCarga = Date.now() + '_' + Math.random().toString(36).slice(2);

            function enviarParte(numeroParte) {
                var inicio = numeroParte * tamanoParte;
                var parte = videoGrabado.slice(inicio, Math.min(inicio + tamanoParte, videoGrabado.size), mime);
                var datos = new FormData();
                datos.append('_token', '{{ csrf_token() }}');
                datos.append('id_hc_protocolo', idProtocolo);
                datos.append('video-filename', mime.indexOf('webm') !== -1 ? 'captura.webm' : 'captura.mp4');
                datos.append('video_mime', mime);
                datos.append('video_id_carga', idCarga);
                datos.append('video_numero_parte', numeroParte);
                datos.append('video_total_partes', totalPartes);
                datos.append('video-blob', parte, 'parte_' + numeroParte + '.bin');

                estado.text('Guardando video ' + Math.round(((numeroParte + 1) / totalPartes) * 100) + '%...');
                $.ajax({
                    type: 'POST',
                    url: "{{ url('historiaclinica/video10/grabacion_captura10') }}",
                    data: datos,
                    processData: false,
                    contentType: false
                }).done(function () {
                    if (numeroParte + 1 < totalPartes) {
                        enviarParte(numeroParte + 1);
                    } else {
                        recargar();
                    }
                }).fail(function (xhr) {
                    var detalle = xhr.responseText || 'No se pudo guardar el video.';
                    estado.text('Error al guardar');
                    Swal.fire('Error', detalle, 'error');
                });
            }

            enviarParte(0);
        };
        grabador.start(1000);
        boton.text('Stop Recording');
        estado.text('Grabando...');
    });

    function subirArchivos(archivos) {
        if (!archivos || !archivos.length) { return; }
        var datos = new FormData();
        datos.append('_token', '{{ csrf_token() }}');
        datos.append('id_hc_protocolo', idProtocolo);
        Array.prototype.forEach.call(archivos, function (archivo) { datos.append('foto[]', archivo); });
        estado.text('Subiendo imágenes...');
        $.ajax({
            type: 'POST',
            url: "{{ route('hc_video.guardado_foto2') }}",
            data: datos,
            processData: false,
            contentType: false
        }).done(recargar).fail(function () {
            estado.text('Error al guardar');
            Swal.fire('Error', 'No se pudieron subir las imágenes.', 'error');
        });
    }

    var entrada = $('#odont-captura-archivos-' + idProcedimiento);
    var drop = $('#odont-captura-drop-' + idProcedimiento);
    drop.on('click', function () { entrada.click(); });
    entrada.on('change', function () { subirArchivos(this.files); });
    drop.on('dragover', function (evento) { evento.preventDefault(); drop.addClass('arrastrando'); });
    drop.on('dragleave', function () { drop.removeClass('arrastrando'); });
    drop.on('drop', function (evento) {
        evento.preventDefault();
        drop.removeClass('arrastrando');
        subirArchivos(evento.originalEvent.dataTransfer.files);
    });

    $('#odont-captura-agregar-' + idProcedimiento).on('click', function () {
        var formulario = $('#odont-captura-historial-form-' + idProcedimiento);
        if (!formulario.find('input[name="image[]"]:checked').length) {
            Swal.fire('Seleccione imágenes', 'Seleccione al menos una imagen del historial.', 'warning');
            return;
        }
        $.post(formulario.attr('action'), formulario.serialize()).done(recargar).fail(function () {
            Swal.fire('Error', 'No se pudieron agregar las imágenes seleccionadas.', 'error');
        });
    });

    iniciarCamara();
})();
</script>
