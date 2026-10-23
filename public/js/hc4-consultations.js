(function (window, $) {
    'use strict';

    // Las vistas se insertan mediante AJAX; no reiniciar una creación en curso.
    if (window.Hc4Consultations) return;

    var busy = false;
    var pendingDisplay = null;
    var buttons = '#atender-consulta-hoy, .hc4-add-consultation-button';

    function setBusy(value) {
        busy = value;
        $(buttons).prop('disabled', value).toggleClass('disabled', value);
        if (value) $(buttons).attr('aria-busy', 'true');
        else $(buttons).removeAttr('aria-busy');
    }

    function samePatient(options) {
        var current = $('#area_trabajo').attr('data-patient-id');
        return !current || String(current) === options.patientId;
    }

    function consultationList(options) {
        var $view = $('#area_trabajo .hc4-consultations-view');
        return String($view.attr('data-patient-id')) === options.patientId
            ? $view.find('.hc4-consultations-list') : $();
    }

    function showError(message, title) {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            window.Swal.fire({icon: 'error', title: title || 'No se pudo agregar la consulta', text: message});
        } else {
            window.alert(message);
        }
    }

    function expand(procedureId) {
        var $entry = $('#consulta' + procedureId);
        var $record = $entry.find('.hc4-consultation-record').first();
        var $view = $entry.closest('.hc4-consultations-view');
        $view.removeClass('collapsed-box hc4-is-collapsed').children('.box-body').show();
        $record.removeClass('collapsed-box').children('.box-body').show();
        var $toggle = $record.find('> .box-header [data-widget="collapse"]');
        $toggle.attr('aria-expanded', 'true').find('i').removeClass('fa-plus').addClass('fa-minus');
        if (typeof window.inicializarCamposConsulta === 'function') {
            window.inicializarCamposConsulta($entry);
        }
        // Carga la receta del registro sin volver a alternar su estado abierto.
        $toggle.triggerHandler('click');
        if (window.hc4NavegacionClinica) {
            window.hc4NavegacionClinica.cargadorListado = 'cargar_consulta';
            window.hc4NavegacionClinica.proximoNivel = 'detalle';
            window.hc4NavegacionClinica.nivelActual = 'detalle';
        }
        if (typeof window.activarVistaClinica === 'function') window.activarVistaClinica();
    }

    function displayCreated(options, procedureId) {
        if (!samePatient(options)) {
            setBusy(false);
            return;
        }
        if ($('#consulta' + procedureId).length && consultationList(options).length) {
            expand(procedureId);
            pendingDisplay = null;
            setBusy(false);
            return;
        }
        var recordOnly = consultationList(options).length > 0;
        $.ajax({
            type: 'GET',
            url: options.listUrl,
            dataType: 'html',
            data: recordOnly ? {record_only: 1, procedure_id: procedureId} : {}
        }).done(function (html) {
            if (!samePatient(options)) {
                setBusy(false);
                return;
            }
            var $content = $('<div>').append($.parseHTML(html, document, true));
            var $entry = $content.find('#consulta' + procedureId);
            if (!$entry.length) {
                displayFailed();
                return;
            }
            if (recordOnly) {
                var $list = consultationList(options);
                // Si cambió de módulo mientras llegaba la respuesta, cargar la vista completa.
                if (!$list.length) {
                    displayCreated(options, procedureId);
                    return;
                }
                if (!$('#consulta' + procedureId).length) $list.prepend($entry);
                $list.scrollTop(0);
            } else {
                $('#area_trabajo').html(html);
            }
            expand(procedureId);
            pendingDisplay = null;
            setBusy(false);
        }).fail(displayFailed);

        function displayFailed() {
            setBusy(false);
            showError('La consulta ya fue creada, pero no se pudo mostrar. Pulsa nuevamente el botón para abrirla sin crear otra.', 'No se pudo mostrar la consulta');
        }
    }

    function createRequest(options, overrideReason) {
        $.ajax({
            type: 'POST',
            url: options.createUrl,
            dataType: 'json',
            data: {
                _token: options.token,
                clinical_override: overrideReason ? '1' : '0',
                clinical_override_reason: overrideReason || ''
            }
        }).done(function (response) {
            if (!response.ok || !/^\d+$/.test(String(response.procedure_id))) {
                setBusy(false);
                showError('No se recibió el identificador de la nueva consulta. Revisa el historial antes de volver a intentar.');
                return;
            }
            pendingDisplay = {patientId: options.patientId, procedureId: response.procedure_id};
            displayCreated(options, response.procedure_id);
        }).fail(function (xhr) {
            var response = xhr.responseJSON || {};
            var message = response.msn || response.message || response.error || 'No se pudo agregar la consulta.';
            if (xhr.status === 409 && response.estado === 'conflicto_clinico' && response.puede_autorizar && window.Swal) {
                window.Swal.fire({
                    icon: 'warning',
                    title: 'Confirmar excepción clínica',
                    text: message + ' La autorización quedará auditada.',
                    input: 'textarea',
                    inputPlaceholder: 'Motivo clínico de la excepción (mínimo 10 caracteres)',
                    showCancelButton: true,
                    confirmButtonText: 'Autorizar y continuar',
                    cancelButtonText: 'Cancelar',
                    inputValidator: function (value) {
                        if (!value || $.trim(value).length < 10) return 'Debes registrar un motivo clínico suficiente.';
                    }
                }).then(function (result) {
                    if (result.isConfirmed && result.value) createRequest(options, $.trim(result.value));
                    else setBusy(false);
                });
                return;
            }
            setBusy(false);
            if (response.estado === 'conflicto_clinico' && !response.puede_autorizar) {
                message += ' Solicita autorización a un médico responsable o a Sistemas.';
            }
            showError(message);
        });
    }

    window.Hc4Consultations = {
        isBusy: function () { return busy; },
        create: function (options) {
            if (busy) return;
            setBusy(true);
            // Evitar que la carga inicial del historial reemplace el registro recién abierto.
            if (window.hc4ConsultaListadoRequest) window.hc4ConsultaListadoRequest.abort();
            if (pendingDisplay && pendingDisplay.patientId === options.patientId) {
                displayCreated(options, pendingDisplay.procedureId);
            } else {
                createRequest(options, '');
            }
        }
    };
})(window, window.jQuery);
