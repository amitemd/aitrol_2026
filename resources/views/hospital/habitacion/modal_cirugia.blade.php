@include('hospital.habitacion._modal_orden_styles')

<div class="clinical-order-modal">
    <div class="modal-header clinical-modal-header">
        <div class="clinical-modal-icon" aria-hidden="true">
            <i class="fa fa-medkit"></i>
        </div>
        <div class="clinical-modal-heading">
            <span class="clinical-modal-eyebrow">Solicitud hospitalaria</span>
            <h5 class="modal-title clinical-modal-title" id="myModalCosto">{{ trans('hospitalizacion.Cirugia') }}</h5>
            <p class="clinical-modal-subtitle">Solicitud de traslado a Cirugia</p>
        </div>
        <button type="button" class="close clinical-modal-close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <div class="modal-body clinical-modal-body">
        <form method="POST" id="form" action="{{ route('hospitalizacion.save_quirofano') }}">
            {{ csrf_field() }}
            <input type="hidden" name="ho_tipo" value="1">
            <input type="hidden" name="id_tipo" value="{{ $id_tipo }}">
            <input type="hidden" name="id_cama" value="{{ $id_cama }}">
            <input type="hidden" name="id_paciente" value="{{ $id_paciente }}">
            <input type="hidden" name="id_hospitalizacion" value="{{ $id_hospitalizacion }}">
            <input type="hidden" name="paso" value="5">

            <section class="clinical-section">
                <h6 class="clinical-section-title"><i class="fa fa-calendar"></i> Destino</h6>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="sala">Sala <span class="required-mark">*</span></label>
                        <select class="form-control select2-static" name="sala" id="sala" required>
                            <option value="">{{ trans('hospitalizacion.SeleccioneOpcion') }}</option>
                            @foreach($sala as $s)
                                <option value="{{ $s->id }}">{{ $s->nombre_sala }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="procedure_indication">Indicacion clinica <span class="required-mark">*</span></label>
                        <textarea name="procedure_indication" id="procedure_indication" class="form-control" rows="3" required minlength="10" maxlength="2000"></textarea>
                    </div>
                </div>
            </section>

            <section class="clinical-section">
                <h6 class="clinical-section-title"><i class="fa fa-stethoscope"></i> Detalle de la atención</h6>
                <div class="row">
                    <div class="form-group col-md-7">
                        <label for="procedimiento">Procedimientos <span class="required-mark">*</span></label>
                        <select class="form-control" name="procedimiento[]" multiple="multiple" id="procedimiento" required></select>
                        <span class="clinical-help">Puedes seleccionar más de un procedimiento.</span>
                        <div class="clinical-search-note">
                            <i class="fa fa-search"></i>
                            <span>Escribe al menos 2 caracteres. El sistema consultará hasta 20 coincidencias y no cargará el catálogo completo.</span>
                        </div>
                    </div>
                    <div class="form-group col-md-5">
                        <label for="observaciones">{{ trans('hospitalizacion.Observaciones') }}</label>
                        <textarea class="form-control" name="observaciones" id="observaciones" rows="4" placeholder="Indicaciones clínicas u observaciones para el equipo..."></textarea>
                    </div>
                </div>
            </section>

            <section class="clinical-section">
                <h6 class="clinical-section-title"><i class="fa fa-exchange"></i> Conciliación del traslado</h6>
                <label>
                    <input type="checkbox" name="preserve_active_orders" value="PRESERVE" required>
                    Confirmo que se conservarán las {{ count($activeOrders) }} órdenes activas durante el traslado a cirugía (R-07).
                </label>
            </section>
        </form>
    </div>

    <div class="modal-footer clinical-modal-footer">
        <button type="button" class="btn btn-clinical-cancel" data-dismiss="modal">
            {{ trans('hospitalizacion.Cerrar') }}
        </button>
        <button type="button" class="btn btn-clinical-save" onclick="sendSurgeryOrder(this)">
            <i class="fa fa-save"></i>&nbsp; {{ trans('hospitalizacion.GUARDAR') }}
        </button>
    </div>
</div>

<script>
    function dates(e) {
        var fecha = $('#modalnew #fecha').val();
        var fechafin = $(e).val();
        return fecha && fechafin;
    }

    function sendSurgeryOrder(button) {
        var $form = $('#modalnew #form');
        if ($form.valid()) {
            $(button).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>&nbsp; Guardando...');
            $form.submit();
        }
    }

    (function () {
        var $modal = $('#modalnew');

        $modal.find('.select2-static').select2({
            width: '100%',
            dropdownParent: $modal,
            dropdownCssClass: 'clinical-order-dropdown'
        });

        $modal.find('#procedimiento').select2({
            width: '100%',
            dropdownParent: $modal,
            dropdownCssClass: 'clinical-order-dropdown',
            placeholder: 'Escribe para buscar procedimientos',
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
                        tipo: 'procedimientos'
                    };
                },
                processResults: function (data) {
                    return { results: data.results || [] };
                }
            },
            language: {
                inputTooShort: function () { return 'Escribe al menos 2 caracteres para buscar.'; },
                searching: function () { return 'Buscando procedimientos...'; },
                noResults: function () { return 'No se encontraron procedimientos.'; },
                errorLoading: function () { return 'No se pudo consultar el catálogo. Intenta nuevamente.'; }
            }
        });
    })();
</script>

<link rel="stylesheet" type="text/css" href="{{ asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css') }}">
