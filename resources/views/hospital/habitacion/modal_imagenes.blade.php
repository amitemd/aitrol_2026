@include('hospital.habitacion._modal_orden_styles')

<div class="clinical-order-modal">
    <div class="modal-header clinical-modal-header">
        <div class="clinical-modal-icon" aria-hidden="true">
            <i class="fa fa-file-image-o"></i>
        </div>
        <div class="clinical-modal-heading">
            <span class="clinical-modal-eyebrow">Diagnóstico por imágenes</span>
            <h5 class="modal-title clinical-modal-title" id="myModalCosto">{{ trans('hospitalizacion.IMAGENES') }}</h5>
            <p class="clinical-modal-subtitle">Registra la orden, el profesional responsable y los estudios requeridos para el paciente.</p>
        </div>
        <button type="button" class="close clinical-modal-close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <div class="modal-body clinical-modal-body">
        <form method="POST" id="form" action="{{ route('cuartos.guardar_imagenes') }}">
            {{ csrf_field() }}
            <input type="hidden" name="ho_tipo" value="0">
            <input type="hidden" name="id_tipo" value="{{ $id_tipo }}">
            <input type="hidden" name="id_cama" value="{{ $id_cama }}">
            <input type="hidden" name="id_paciente" value="{{ $indetificacion }}">
            <input type="hidden" name="id_hospitalizacion" value="{{ $id_hospitalizacion }}">
            <input type="hidden" name="paso" value="4">

            <section class="clinical-section">
                <h6 class="clinical-section-title"><i class="fa fa-calendar"></i> Programación</h6>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="fecha">{{ trans('hospitalizacion.Fecha') }} <span class="required-mark">*</span></label>
                        <input id="fecha" type="text" name="fecha" class="form-control flatpickr-date-time" required value="{{ date('Y-m-d H:i:s') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="fechafin">{{ trans('hospitalizacion.FechaFin') }} <span class="required-mark">*</span></label>
                        <input id="fechafin" type="text" name="fechafin" onchange="datesImaging(this)" class="form-control flatpickr-date-time" required value="{{ date('Y-m-d H:i:s') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="sala">{{ trans('hospitalizacion.Sala') }} <span class="required-mark">*</span></label>
                        <select class="form-control select2-static" name="sala" id="sala" required>
                            <option value="">{{ trans('hospitalizacion.SeleccioneOpcion') }}</option>
                            @foreach($sala as $s)
                                <option value="{{ $s->id }}">{{ $s->nombre_sala }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="doctor">Doctor <span class="required-mark">*</span></label>
                        <select class="form-control select2-static" name="doctor" id="doctor" required>
                            <option value="">{{ trans('hospitalizacion.SeleccioneOpcion') }}</option>
                            @foreach($users as $x)
                                <option @if(Auth::user()->id == $x->id) selected="selected" @endif value="{{ $x->id }}">{{ $x->apellido1 }} {{ $x->nombre1 }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </section>

            @include('hospital.habitacion._procedure_safety')

            <section class="clinical-section">
                <h6 class="clinical-section-title"><i class="fa fa-picture-o"></i> Estudios solicitados</h6>
                <div class="row">
                    <div class="form-group col-md-7">
                        <label for="procedimiento">{{ trans('hospitalizacion.Imagenes') }} <span class="required-mark">*</span></label>
                        <select class="form-control" name="procedimiento[]" multiple="multiple" id="procedimiento" required></select>
                        <span class="clinical-help">Puedes agregar varios estudios a la misma orden.</span>
                        <div class="clinical-search-note">
                            <i class="fa fa-search"></i>
                            <span>Escribe al menos 2 caracteres. Solo se consultarán estudios de imágenes y se mostrarán hasta 20 coincidencias.</span>
                        </div>
                    </div>
                    <div class="form-group col-md-5">
                        <label for="observaciones">{{ trans('hospitalizacion.Observaciones') }}</label>
                        <textarea class="form-control" name="observaciones" id="observaciones" rows="4" placeholder="Zona anatómica, prioridad o indicaciones para el estudio..."></textarea>
                    </div>
                </div>
            </section>

            <section class="clinical-section">
                <h6 class="clinical-section-title"><i class="fa fa-exchange"></i> Conciliación del traslado</h6>
                <label>
                    <input type="checkbox" name="preserve_active_orders" value="PRESERVE" required>
                    Confirmo que se conservarán las {{ count($activeOrders) }} órdenes activas durante el traslado a imágenes (R-07).
                </label>
            </section>
        </form>
    </div>

    <div class="modal-footer clinical-modal-footer">
        <button type="button" class="btn btn-clinical-cancel" data-dismiss="modal">
            {{ trans('hospitalizacion.Cerrar') }}
        </button>
        <button type="button" class="btn btn-clinical-save" onclick="sendImagingOrder(this)">
            <i class="fa fa-save"></i>&nbsp; {{ trans('hospitalizacion.GUARDAR') }}
        </button>
    </div>
</div>

<script>
    function datesImaging(e) {
        var fecha = $('#modalimagenes #fecha').val();
        var fechafin = $(e).val();
        return fecha && fechafin;
    }

    function sendImagingOrder(button) {
        var $form = $('#modalimagenes #form');
        if ($form.valid()) {
            $(button).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>&nbsp; Guardando...');
            $form.submit();
        }
    }

    (function () {
        var $modal = $('#modalimagenes');

        $modal.find('.select2-static').select2({
            width: '100%',
            dropdownParent: $modal,
            dropdownCssClass: 'clinical-order-dropdown'
        });

        $modal.find('#procedimiento').select2({
            width: '100%',
            dropdownParent: $modal,
            dropdownCssClass: 'clinical-order-dropdown',
            placeholder: 'Escribe para buscar estudios de imágenes',
            minimumInputLength: 2,
            closeOnSelect: false,
            ajax: {
                url: "{{ route('hospitalizacion.buscar_procedimientos_modal') }}",
                dataType: 'json',
                delay: 300,
                cache: true,
                data: function (params) {
                    return {
                        q: $.trim(params.term || ''),
                        tipo: 'imagenes'
                    };
                },
                processResults: function (data) {
                    return { results: data.results || [] };
                }
            },
            language: {
                inputTooShort: function () { return 'Escribe al menos 2 caracteres para buscar.'; },
                searching: function () { return 'Buscando estudios...'; },
                noResults: function () { return 'No se encontraron estudios de imágenes.'; },
                errorLoading: function () { return 'No se pudo consultar el catálogo. Intenta nuevamente.'; }
            }
        });
    })();
</script>

<link rel="stylesheet" type="text/css" href="{{ asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css') }}">
