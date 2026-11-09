@php
    $fecha = !is_null($detalleBase) && $detalleBase->fecha ? $detalleBase->fecha : date('Y-m-d');
    $piezaFdi = !is_null($detalleBase) ? $detalleBase->pieza_fdi : '';
    $caraDental = !is_null($detalleBase) ? $detalleBase->cara_dental : '';
    $estadoProcedimiento = !is_null($detalleBase) && $detalleBase->estado_procedimiento ? $detalleBase->estado_procedimiento : 'REALIZADO';
    $observaciones = !is_null($detalleBase) ? $detalleBase->observaciones : '';
    $idDoctor = !is_null($detalleBase) && $detalleBase->id_doctor ? $detalleBase->id_doctor : Auth::user()->id;
@endphp

<style>
    .odont-proc-editor { overflow: hidden; border: 1px solid #c9dee8; border-radius: 10px; background: #fff; box-shadow: 0 5px 14px rgba(0,74,117,.07); }
    .odont-proc-editor-header { padding: 10px 14px; background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%); color: #fff; font-size: 15px; }
    .odont-proc-editor-body { padding: 15px; }
    .odont-proc-editor .select2-container { width: 100% !important; }
    .odont-proc-editor .select2-selection--multiple { min-height: 38px; }
    .odont-proc-editor .select2-selection__choice { max-width: calc(100% - 8px); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .odont-proc-cie-dropdown .select2-results__option { white-space: normal; overflow-wrap: anywhere; }
    .odont-proc-imagenes-original { overflow: hidden; border: 1px solid #c9dee8; background: #fff; border-radius: 10px; box-shadow: 0 5px 14px rgba(0,74,117,.07); }
    .odont-proc-imagenes-original .box-header { padding: 8px; background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%); color: #fff; text-align: center; }
    .odont-proc-imagenes-original .box-body { min-height: 210px; padding: 12px; }
    .odont-proc-miniatura { margin-bottom: 8px; }
    .odont-proc-miniatura img { width: 100%; max-height: 145px; object-fit: contain; }
</style>

<div class="odont-proc-editor">
    <div class="odont-proc-editor-header"><i class="fa fa-pencil-square-o"></i> EDITAR DETALLES DEL PROCEDIMIENTO</div>
    <div class="odont-proc-editor-body">
        <div class="row">
        <form id="odont-proc-form-editar-{{ $procedimiento->id }}" class="col-lg-8 col-md-12">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-12 form-group">
                    <label>PROCEDIMIENTOS</label>
                    <select id="odont-proc-catalogo-{{ $procedimiento->id }}" class="form-control" name="procedimientos[]" multiple>
                        @foreach($catalogo as $item)
                            <option value="{{ $item->id }}" {{ in_array((int) $item->id, $seleccionados) ? 'selected' : '' }}>{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>FECHA</label>
                    <input type="date" class="form-control input-sm" name="fecha" value="{{ $fecha }}" required>
                </div>
                <div class="col-md-2 form-group">
                    <label>PIEZA FDI</label>
                    <input id="odont-proc-pieza-{{ $procedimiento->id }}" type="text" class="form-control input-sm" name="pieza_fdi" value="{{ $piezaFdi }}" maxlength="2" placeholder="11">
                </div>
                <div class="col-md-3 form-group">
                    <label>CARA DENTAL</label>
                    <select id="odont-proc-cara-{{ $procedimiento->id }}" class="form-control" name="cara_dental">
                        <option value="">No aplica</option>
                        <option value="V" {{ $caraDental === 'V' ? 'selected' : '' }}>Vestibular (V)</option>
                        <option value="L" {{ $caraDental === 'L' ? 'selected' : '' }}>Palatina / Lingual (L)</option>
                        <option value="M" {{ $caraDental === 'M' ? 'selected' : '' }}>Mesial (M)</option>
                        <option value="D" {{ $caraDental === 'D' ? 'selected' : '' }}>Distal (D)</option>
                        <option value="O" {{ $caraDental === 'O' ? 'selected' : '' }}>Incisal / Oclusal (O)</option>
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <label>ESTADO</label>
                    <select id="odont-proc-estado-{{ $procedimiento->id }}" class="form-control" name="estado_procedimiento">
                        @foreach(array('REALIZADO', 'INDICADO', 'PLANIFICADO') as $estado)
                            <option value="{{ $estado }}" {{ $estadoProcedimiento === $estado ? 'selected' : '' }}>{{ $estado }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <label>MEDICO EXAMINADOR</label>
                    <select id="odont-proc-doctor-{{ $procedimiento->id }}" class="form-control" name="id_doctor">
                        @foreach($doctores as $doctor)
                            <option value="{{ $doctor->id }}" {{ (string) $idDoctor === (string) $doctor->id ? 'selected' : '' }}>
                                {{ trim($doctor->apellido1.' '.$doctor->apellido2.' '.$doctor->nombre1.' '.$doctor->nombre2) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 form-group">
                    <label>DIAGNOSTICOS CIE-10</label>
                    <select id="odont-proc-diagnosticos-{{ $procedimiento->id }}" class="form-control" name="diagnosticos[]" multiple>
                        @foreach($cie10 as $diagnosticoCie10)
                            <option value="{{ $diagnosticoCie10->id }}" {{ in_array(strtoupper((string) $diagnosticoCie10->id), $diagnosticosSeleccionados, true) ? 'selected' : '' }}>
                                {{ $diagnosticoCie10->id }} - {{ $diagnosticoCie10->descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label>OBSERVACIONES</label>
                    <textarea class="form-control input-sm" name="observaciones" rows="3">{{ $observaciones }}</textarea>
                </div>
            </div>
            <button type="button" class="btn btn-primary" id="odont-proc-guardar-{{ $procedimiento->id }}" onclick="odontologiaProcedimientoActualizar({{ $procedimiento->id }});">
                <i class="fa fa-save"></i> GUARDAR
            </button>
            <button type="button" class="btn btn-default" onclick="cargarProcedimientosOdontologicos({{ $procedimiento->id }});">CANCELAR</button>
        </form>
        <div class="col-lg-4 col-md-12">
            <div class="box odont-proc-imagenes-original">
                <div class="box-header with-border">
                    <div class="row">
                        <div class="col-xs-4 text-left">
                            <button type="button" class="btn" style="color: white; background: transparent;" onclick="odontologiaImagenesAbrir{{ $procedimiento->id }}();">
                                <i class="fa fa-pencil-square-o"></i> <span style="font-size: 13px;">Editar</span>
                            </button>
                        </div>
                        <div class="col-xs-8"><span>Imagenes Procedimientos</span></div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        @forelse($imagenes as $imagen)
                            @php
                                $extension = strtolower(pathinfo($imagen->nombre, PATHINFO_EXTENSION));
                                $esImagen = in_array($extension, array('jpg', 'jpeg', 'png', 'bmp'));
                            @endphp
                            <div class="col-md-6 col-sm-4 col-xs-6 odont-proc-miniatura">
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
                            <div class="col-xs-12 text-center text-muted" style="padding: 55px 10px;">
                                No hay imagenes ni videos registrados.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div id="odont-proc-captura-{{ $procedimiento->id }}" style="display: none; margin-top: 15px;"></div>
    </div>
</div>

<script>
    $(document).off('click.odontArchivoModal', '.odont-archivo-modal')
        .on('click.odontArchivoModal', '.odont-archivo-modal', function (evento) {
            evento.preventDefault();
            evento.stopImmediatePropagation();

            var modal = $('#foto');
            var contenido = modal.find('.modal-content');
            var url = $(this).data('url');

            modal.removeData('bs.modal');
            contenido.empty().html('<div class="text-center" style="padding: 45px;"><i class="fa fa-spinner fa-spin"></i> Cargando archivo...</div>');
            modal.modal('show');
            contenido.load(url, function (respuesta, estado) {
                if (estado === 'error') {
                    contenido.html('<div class="alert alert-danger" style="margin: 20px;">No se pudo abrir el archivo seleccionado.</div>');
                }
            });
        });

    $('#foto').off('hidden.bs.modal.odontArchivo').on('hidden.bs.modal.odontArchivo', function () {
        $(this).find('video').each(function () {
            this.pause();
            this.removeAttribute('src');
            $(this).find('source').removeAttr('src');
            this.load();
        });
        $(this).find('.modal-content').empty();
        $(this).removeData('bs.modal');
    });

    $('#odont-proc-catalogo-{{ $procedimiento->id }}').select2({
        width: '100%',
        placeholder: 'Buscar procedimientos odontologicos',
        closeOnSelect: false,
        dropdownParent: $('#odont-proc-edicion-{{ $procedimiento->id }}')
    });
    $('#odont-proc-diagnosticos-{{ $procedimiento->id }}').select2({
        width: '100%',
        placeholder: 'Buscar diagnosticos CIE-10 odontologicos',
        closeOnSelect: false,
        dropdownCssClass: 'odont-proc-cie-dropdown',
        dropdownParent: $('#odont-proc-edicion-{{ $procedimiento->id }}')
    });
    $('#odont-proc-estado-{{ $procedimiento->id }}, #odont-proc-doctor-{{ $procedimiento->id }}, #odont-proc-cara-{{ $procedimiento->id }}').select2({
        width: '100%',
        minimumResultsForSearch: 0,
        dropdownParent: $('#odont-proc-edicion-{{ $procedimiento->id }}')
    });

    function odontologiaActualizarCarasProcedimiento{{ $procedimiento->id }}() {
        var pieza = ($('#odont-proc-pieza-{{ $procedimiento->id }}').val() || '').trim();
        var cuadrante = parseInt(pieza.charAt(0), 10);
        var posicion = parseInt(pieza.charAt(1), 10);
        var inferior = [3, 4, 7, 8].indexOf(cuadrante) !== -1;
        var anterior = posicion >= 1 && posicion <= 3;
        var cara = $('#odont-proc-cara-{{ $procedimiento->id }}');

        cara.find('option[value="L"]').text((pieza.length === 2 ? (inferior ? 'Lingual' : 'Palatina') : 'Palatina / Lingual') + ' (L)');
        cara.find('option[value="O"]').text((pieza.length === 2 ? (anterior ? 'Incisal' : 'Oclusal') : 'Incisal / Oclusal') + ' (O)');
        cara.trigger('change.select2');
    }

    $('#odont-proc-pieza-{{ $procedimiento->id }}').on('input change blur', odontologiaActualizarCarasProcedimiento{{ $procedimiento->id }});
    odontologiaActualizarCarasProcedimiento{{ $procedimiento->id }}();

    function odontologiaImagenesAbrir{{ $procedimiento->id }}() {
        var contenedor = $('#odont-proc-captura-{{ $procedimiento->id }}');
        if (contenedor.is(':visible')) {
            if (typeof window.odontologiaCapturaActivaCerrar === 'function') {
                window.odontologiaCapturaActivaCerrar();
            }
            contenedor.slideUp(120).empty();
            return;
        }
        contenedor.html('<div class="text-center" style="padding: 30px;"><i class="fa fa-spinner fa-spin"></i> Cargando imágenes...</div>').slideDown(120);
        $.ajax({
            type: 'GET',
            url: "{{ route((isset($prefijoModulo) ? $prefijoModulo : 'odontologia').'.procedimientos.imagenes', ['id' => $procedimiento->id]) }}",
            dataType: 'html',
            success: function (html) {
                contenedor.html(html);
            },
            error: function (xhr) {
                var respuesta = xhr.responseJSON || {};
                var mensaje = respuesta.mensaje || ('No se pudo cargar la captura de imágenes y video (error ' + xhr.status + ').');
                contenedor.empty().append($('<div>', {
                    'class': 'alert alert-danger',
                    text: mensaje
                }));
            }
        });
    }

    function odontologiaProcedimientoActualizar(id) {
        var seleccionados = $('#odont-proc-catalogo-' + id).val() || [];
        if (!seleccionados.length) {
            Swal.fire('Seleccione un procedimiento', 'El registro debe conservar al menos un procedimiento odontologico.', 'warning');
            return;
        }

        var boton = $('#odont-proc-guardar-' + id);
        boton.prop('disabled', true);
        $.ajax({
            type: 'POST',
            url: "{{ url(isset($prefijoModulo) ? $prefijoModulo : 'odontologia') }}/procedimientos/actualizar/" + id,
            data: $('#odont-proc-form-editar-' + id).serialize(),
            dataType: 'json',
            success: function (respuesta) {
                Swal.fire({
                    icon: 'success',
                    title: 'Guardado',
                    text: respuesta.mensaje,
                    timer: 1200,
                    showConfirmButton: false
                });
                cargarProcedimientosOdontologicos(respuesta.id_abrir);
            },
            error: function (xhr) {
                var respuesta = xhr.responseJSON || {};
                Swal.fire('Error', respuesta.mensaje || 'No se pudo actualizar el procedimiento odontologico.', 'error');
                boton.prop('disabled', false);
            }
        });
    }
</script>
