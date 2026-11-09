<style>
    .hospital-transfer__header {
        display: flex;
        align-items: center;
        width: 100%;
        min-height: 60px;
        padding: 12px 18px;
    }

    .hospital-transfer__title {
        margin: 0;
        color: #fff !important;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.25;
    }

    .hospital-transfer__body {
        padding: 24px !important;
    }

    .hospital-transfer__section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid #dceaf0;
        color: #174763;
        font-size: 15px;
        font-weight: 700;
    }

    .hospital-transfer__section-title i {
        color: #14a7be;
    }

    .hospital-transfer__field {
        margin-bottom: 18px;
    }

    .hospital-transfer__field label {
        display: block;
        margin-bottom: 7px;
        color: #24445d;
        font-size: 12px;
        font-weight: 700;
    }

    .hospital-transfer__field .form-control {
        min-height: 44px;
        border: 1px solid #cfe2ec;
        border-radius: 9px;
        background: #fff;
        box-shadow: none;
    }

    .hospital-transfer__field textarea.form-control {
        min-height: 105px;
        resize: vertical;
    }

    .hospital-transfer__field .form-control:focus {
        border-color: #18a9bf;
        box-shadow: 0 0 0 3px rgba(24, 169, 191, .12);
    }

    .hospital-transfer__field .select2-container {
        display: block;
        width: 100% !important;
    }

    .hospital-transfer__field .select2-container--default .select2-selection--single {
        min-height: 44px;
        padding: 7px 12px;
        border: 1px solid #cfe2ec;
        border-radius: 9px;
        background: #fff;
    }

    .hospital-transfer__field .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding: 0 28px 0 0;
        color: #24445d;
        line-height: 28px;
    }

    .hospital-transfer__field .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 8px;
        right: 8px;
    }

    .hospital-transfer__field .select2-container--default.select2-container--focus .select2-selection--single,
    .hospital-transfer__field .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #18a9bf !important;
        box-shadow: 0 0 0 3px rgba(24, 169, 191, .12) !important;
    }

    .hospital-transfer__conciliation {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-top: 4px;
        padding: 15px 16px;
        border-left: 4px solid #18a9bf;
        border-radius: 8px;
        background: #e8f8fa;
    }

    .hospital-transfer__conciliation input {
        flex: 0 0 auto;
        width: 18px;
        height: 18px;
        margin: 2px 0 0;
        accent-color: #18a9bf;
    }

    .hospital-transfer__conciliation-copy {
        color: #24445d;
        line-height: 1.45;
    }

    .hospital-transfer__conciliation-title {
        display: block;
        margin: 0;
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
    }

    .hospital-transfer__conciliation-help {
        display: block;
        margin-top: 4px;
        color: #6f8494;
        font-size: 11px;
        font-weight: 400;
    }

    .hospital-transfer__actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid #e1edf2;
    }

    .hospital-transfer__save {
        min-width: 165px;
        min-height: 42px;
        border: 0;
        border-radius: 9px;
        background: #117bb5 !important;
        color: #fff !important;
        font-weight: 700;
    }

    @media (max-width: 575px) {
        .hospital-transfer__header {
            padding: 11px 13px;
        }

        .hospital-transfer__body {
            padding: 16px !important;
        }

        .hospital-transfer__save {
            width: 100%;
        }
    }
</style>

<div class="card">
    <div class="card-header bg bg-primary hospital-transfer__header">
        <h4 class="hospital-transfer__title">{{trans('hospitalizacion.Traspaso')}}</h4>
    </div>
    <div class="card-body hospital-transfer__body">
        <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$solicitud->id}}">
        <input type="hidden" name="opcion" id="opcion" value="traslado">

        <h5 class="hospital-transfer__section-title">
            <i class="fa fa-map-marker"></i>
            Destino del traslado
        </h5>

        <div class="row">
            <div class="col-md-6 hospital-transfer__field">
                <label for="id_paso">Área de destino</label>
                <select name="id_paso" id="id_paso" class="form-control hospital-transfer__select" onchange="filtrar_salas_traslado()">
                    <option value="">{{trans('emergencia.Seleccione')}}</option>
                    @foreach($pasos as $p)
                    <option value="{{$p->id}}">{{$p->nombre_paso}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 hospital-transfer__field">
                <label for="transfer_sala">Sala de destino</label>
                <select id="transfer_sala" class="form-control hospital-transfer__select" required>
                    <option value="">Seleccione</option>
                    @foreach($salas as $sala)
                    <option value="{{$sala->id}}" data-step="{{$sala->hospital_step}}" hidden disabled>{{$sala->nombre_sala}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12 hospital-transfer__field">
                <label for="transfer_reason">Motivo clínico</label>
                <textarea id="transfer_reason" class="form-control" minlength="10" required></textarea>
            </div>
        </div>

        <h5 class="hospital-transfer__section-title">
            <i class="fa fa-exchange"></i>
            Conciliación de órdenes
        </h5>

        <div class="hospital-transfer__conciliation">
            <input type="checkbox" id="preserve_active_orders" value="PRESERVE">
            <div class="hospital-transfer__conciliation-copy">
                <label for="preserve_active_orders" class="hospital-transfer__conciliation-title">
                    Conservar las órdenes activas durante el traslado
                    <span>({{count($activeOrders)}} {{count($activeOrders) === 1 ? 'detectada' : 'detectadas'}})</span>
                </label>
                <small class="hospital-transfer__conciliation-help">
                    Las órdenes de laboratorio y procedimientos se registrarán en la conciliación del episodio para mantener su trazabilidad.
                </small>
            </div>
        </div>

        <div class="hospital-transfer__actions">
            <button class="btn btn-info hospital-transfer__save" name="btng" id="btng" type="button" onclick="guardar_traslado();">
                <i class="fa fa-save"></i> Guardar traspaso
            </button>
        </div>
    </div>
</div>

<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>
<script type="text/javascript">
    function iniciar_selectores_traslado() {
        if (!$.fn.select2) {
            return;
        }

        $('.hospital-transfer__select').each(function () {
            var $selector = $(this);

            if ($selector.hasClass('select2-hidden-accessible')) {
                $selector.select2('destroy');
            }

            $selector.select2({
                width: '100%',
                placeholder: $selector.find('option:first').text(),
                templateResult: function (option) {
                    if (option.element && ($(option.element).prop('hidden') || $(option.element).prop('disabled'))) {
                        return null;
                    }

                    return option.text;
                },
                language: {
                    noResults: function () {
                        return 'No se encontraron opciones';
                    },
                    searching: function () {
                        return 'Buscando…';
                    }
                }
            });
        });
    }

    iniciar_selectores_traslado();

    function filtrar_salas_traslado() {
        var step = $('#id_paso').val();
        $('#transfer_sala').val('').trigger('change');
        $('#transfer_sala option[data-step]').each(function () {
            var show = String($(this).data('step')) === step;
            $(this).prop('hidden', !show).prop('disabled', !show);
        });
        $('#transfer_sala').trigger('change.select2');
    }
    function guardar_traslado() {
        var id_solicitud = document.getElementById('id_solicitud').value;
        var opcion = document.getElementById('opcion').value;
        var id_paso = document.getElementById('id_paso').value;
        var id_sala = $('#transfer_sala').val();
        var transfer_reason = $.trim($('#transfer_reason').val());
        var preserve_active_orders = document.getElementById('preserve_active_orders').checked ? 'PRESERVE' : '';

        if (!id_paso) {
            alertas('warning', 'Área de destino', 'Seleccione el área a la que será trasladado el paciente.');
            if ($.fn.select2) {
                $('#id_paso').select2('open');
            } else {
                $('#id_paso').focus();
            }
            return;
        }
        if (!id_sala) {
            alertas('warning', 'Sala de destino', 'Seleccione la sala a la que será trasladado el paciente.');
            if ($.fn.select2) {
                $('#transfer_sala').select2('open');
            } else {
                $('#transfer_sala').focus();
            }
            return;
        }
        if (transfer_reason.length < 10) {
            alertas('warning', 'Motivo clínico', 'Ingrese un motivo clínico de al menos 10 caracteres. Actualmente tiene ' + transfer_reason.length + '.');
            $('#transfer_reason').focus();
            return;
        }
        if (!preserve_active_orders) {
            alertas('warning', 'R-07', 'Confirme que las órdenes activas se conservarán durante el traslado.');
            return;
        }

        $.ajax({

            type: 'post',
            url: "{{route('hospital.guardar_traspaso')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_solicitud': id_solicitud,
                'opcion': opcion,
                'id_paso': id_paso,
                'id_sala': id_sala,
                'transfer_reason': transfer_reason,
                'preserve_active_orders': preserve_active_orders,
            },
            success: function(data) {
                alertas(data.respuesta, data.respuesta, data.msj)
                if (data.respuesta == 'success') {
                    setTimeout(function() {
                        window.location.href = "{{url('hospital/modulos/index')}}/" + data.paso;
                    }, 1500)
                }

            },
            error: function(data) {
                alert((data.responseJSON || {}).message || (data.responseJSON || {}).msj || 'No se pudo registrar el traslado.');
            }
        })
    }
</script>
