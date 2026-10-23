(function ($) {
    'use strict';
    var $triage = $('#form_manchester');
    var $form = $('#form-paciente-emergencia');
    var $modal = $('#registro-paciente-emergencia');
    if (!$triage.length || !$form.length || !$modal.length) return;
    $modal.appendTo(document.body);
    var lookupUrl = $triage.data('patient-lookup');
    var lookupSequence = 0;
    var modalSequence = 0;
    var saving = false;
    var modalLooking = false;
    var $documentType = $('#rapido-tipo-documento');
    var $identification = $('#rapido-id');
    var $identificationHelp = $('#rapido-id-ayuda');
    var $provisionalNames = $('#rapido-nombre, #rapido-apellido');
    if ($.fn.select2) {
        if ($documentType.hasClass('select2-hidden-accessible')) $documentType.select2('destroy');
        $documentType.select2({width: '100%', dropdownParent: $modal, minimumResultsForSearch: Infinity});
    }
    function provisionalCode() {
        var alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var code;
        do {
            code = '';
            if (window.crypto && window.crypto.getRandomValues) {
                var values = window.crypto.getRandomValues(new Uint8Array(6));
                values.forEach(function (value) { code += alphabet.charAt(value % alphabet.length); });
            } else {
                for (var index = 0; index < 6; index++) code += alphabet.charAt(Math.floor(Math.random() * alphabet.length));
            }
        } while (!/[A-Z]/.test(code) || !/[0-9]/.test(code));
        return code;
    }
    function documentMode(generateCode) {
        var unknown = $('#rapido-sin-documento').prop('checked');
        if (unknown) {
            $documentType.val('3').prop('disabled', true).trigger('change.select2');
            $identification.prop('disabled', false).prop('readonly', true).prop('required', true)
                .attr({minlength: 6, maxlength: 6, pattern: '(?=.*[A-Z])(?=.*[0-9])[A-Z0-9]{6}'});
            if (generateCode || !/^(?=.*[A-Z])(?=.*[0-9])[A-Z0-9]{6}$/.test($identification.val())) {
                $identification.val(provisionalCode()).attr('data-provisional-code', '1');
            }
            $provisionalNames.each(function () {
                var field = $(this);
                if (field.attr('data-provisional-name') !== '1') field.attr('data-previous-value', field.val());
                field.val('N/A').prop('readonly', true).attr('data-provisional-name', '1');
            });
            $identificationHelp.text('Código provisional alfanumérico generado automáticamente.');
            return;
        }
        $documentType.prop('disabled', false);
        if ($identification.attr('data-provisional-code') === '1') {
            $identification.val('').removeAttr('data-provisional-code');
            if ($documentType.val() === '3') $documentType.val('1').trigger('change.select2');
        }
        $provisionalNames.each(function () {
            var field = $(this);
            if (field.attr('data-provisional-name') === '1') {
                field.val(field.attr('data-previous-value') || '')
                    .removeAttr('data-provisional-name').removeAttr('data-previous-value');
            }
            field.prop('readonly', false);
        });
        $identification.prop('readonly', false).prop('disabled', false).prop('required', true)
            .attr({minlength: 3, maxlength: 13, pattern: '[A-Za-z0-9\\-]+'});
        $identificationHelp.text('Al salir de este campo se consultan los datos existentes.');
        $documentType.trigger('change.select2');
    }

    function message($target, text) {
        $target.text(text || '').prop('hidden', !text);
    }
    function errorText(xhr) {
        var body = xhr.responseJSON || {};
        if (body.errors) return Object.keys(body.errors).map(function (key) { return body.errors[key].join(' '); }).join('\n');
        return body.msj || body.message || 'No se pudo guardar. Revise la conexión e intente nuevamente.';
    }
    function clearPatient() {
        $triage.removeAttr('data-selected-patient');
        $('#nombre, #apellido1, #apellido2, #nombre1, #nombre2').val('');
        $('#sexo').val('');
        $('#completar-paciente-emergencia').prop('hidden', true).removeAttr('href');
        message($('#paciente-emergencia-estado'), '');
        $triage.trigger('emergency:patient-cleared');
    }
    function selectPatient(patient) {
        lookupSequence++;
        $('#id_paciente').val(patient.id);
        $triage.attr('data-selected-patient', patient.id);
        $('#nombre').val(patient.value);
        ['apellido1', 'apellido2', 'nombre1', 'nombre2', 'sexo'].forEach(function (field) {
            $('#' + field).val(patient[field] || '');
        });
        $('#completar-paciente-emergencia').attr('href', patient.editar_url).prop('hidden', false);
        message($('#paciente-emergencia-estado'), Number(patient.registro_emergencia_pendiente)
            ? 'Paciente seleccionado. Datos por completar en Pacientes; puede continuar con el ingreso.'
            : 'Paciente registrado seleccionado.');
        $triage.trigger('emergency:patient-selected', [patient]);
    }
    window.buscar_cedula_paciente = function () {
        var id = $.trim($('#id_paciente').val());
        var sequence = ++lookupSequence;
        clearPatient();
        if (!id) return;
        message($('#paciente-emergencia-estado'), 'Buscando paciente…');
        $.getJSON(lookupUrl, {id: id}).done(function (patient) {
            if (sequence !== lookupSequence) return;
            if (patient && patient.es_paciente) selectPatient(patient);
            else message($('#paciente-emergencia-estado'), patient
                ? 'La persona ya existe. Use Nuevo paciente para traer sus datos y registrar su ficha.'
                : 'Paciente no encontrado. Use Nuevo paciente para registrarlo con los datos básicos.');
        }).fail(function () {
            if (sequence === lookupSequence) message($('#paciente-emergencia-estado'), 'No se pudo consultar la identificación. Intente nuevamente.');
        });
    };
    $('#id_paciente').on('input', function () { lookupSequence++; clearPatient(); });
    $('#nombre').autocomplete({
        minLength: 3,
        source: function (request, response) {
            $.getJSON(lookupUrl, {term: request.term}).done(response).fail(function () { response([]); });
        },
        select: function (event, data) { event.preventDefault(); selectPatient(data.item); },
        change: function (event, data) {
            if (!data.item) {
                lookupSequence++;
                $('#id_paciente').val('');
                clearPatient();
            }
        }
    });

    function lookupModal() {
        if ($('#rapido-sin-documento').prop('checked')) return;
        var id = $.trim($('#rapido-id').val());
        var sequence = ++modalSequence;
        message($('#registro-persona-info'), '');
        if (id.length < 3) return;
        modalLooking = true;
        $('#guardar-paciente-emergencia').prop('disabled', true);
        $.getJSON(lookupUrl, {id: id}).done(function (person) {
            if (sequence !== modalSequence || !person) return;
            $('#rapido-apellido').val(person.apellido1 || '');
            $('#rapido-nombre').val(person.nombre1 || '');
            $('#rapido-sexo').val(person.sexo || '');
            if (person.tipo_documento) $documentType.val(person.tipo_documento).trigger('change.select2');
            message($('#registro-persona-info'), person.es_paciente
                ? 'El paciente ya existe. Se seleccionará su ficha sin crear un duplicado.'
                : 'Datos encontrados en Personas. Verifique la información para registrar al paciente.');
        }).fail(function () {
            if (sequence === modalSequence) message($('#registro-persona-info'), 'No se pudo consultar la identificación. Se volverá a comprobar al guardar.');
        }).always(function () {
            if (sequence !== modalSequence) return;
            modalLooking = false;
            $('#guardar-paciente-emergencia').prop('disabled', saving);
        });
    }
    $('#nuevo-paciente-emergencia').on('click', function () {
        $form[0].reset();
        $identification.removeAttr('data-provisional-code');
        $provisionalNames.removeAttr('data-provisional-name data-previous-value').prop('readonly', false);
        // Keep the same token after a failed save; renew only for another explicit registration.
        if (window.crypto && window.crypto.randomUUID) $('#rapido-token').val(window.crypto.randomUUID());
        else if (window.crypto && window.crypto.getRandomValues) {
            var bytes = window.crypto.getRandomValues(new Uint8Array(16));
            bytes[6] = (bytes[6] & 15) | 64; bytes[8] = (bytes[8] & 63) | 128;
            var hex = Array.from(bytes, function (byte) { return byte.toString(16).padStart(2, '0'); }).join('');
            $('#rapido-token').val(hex.slice(0,8)+'-'+hex.slice(8,12)+'-'+hex.slice(12,16)+'-'+hex.slice(16,20)+'-'+hex.slice(20));
        }
        $('#rapido-id').val($triage.attr('data-selected-patient') ? '' : $.trim($('#id_paciente').val()));
        documentMode(false);
        message($('#registro-paciente-error'), '');
        message($('#registro-persona-info'), '');
        $modal.modal('show');
        lookupModal();
    });
    $modal.on('shown.bs.modal', function () { $('#rapido-id').trigger('focus'); });
    $modal.on('hide.bs.modal', function (event) {
        if (saving) { event.preventDefault(); return; }
        modalSequence++;
        modalLooking = false;
        $('#guardar-paciente-emergencia').prop('disabled', false);
    });
    $('#rapido-id').on('input', function () {
        modalSequence++;
        modalLooking = false;
        $('#guardar-paciente-emergencia').prop('disabled', saving);
        $('#rapido-apellido, #rapido-nombre, #rapido-sexo').val('');
        message($('#registro-persona-info'), '');
    }).on('change', lookupModal);
    $('#rapido-sin-documento').on('change', function () {
        modalSequence++; modalLooking = false;
        $('#guardar-paciente-emergencia').prop('disabled', saving);
        message($('#registro-persona-info'), '');
        documentMode(true);
    });
    $form.on('submit', function (event) {
        event.preventDefault();
        if (saving || modalLooking || !$form[0].reportValidity()) return;
        saving = true;
        message($('#registro-paciente-error'), '');
        var payload = $form.serialize();
        $form.find('input, select, button').prop('disabled', true);
        $.ajax({url: $form.attr('action'), type: 'POST', dataType: 'json', data: payload,
            headers: {'Accept': 'application/json'}}).done(function (data) {
            if (!data || data.respuesta !== 'success' || !data.paciente || !data.paciente.id) {
                message($('#registro-paciente-error'), 'No se pudo confirmar el registro. Sus datos siguen en el formulario; revise antes de reintentar.');
                return;
            }
            selectPatient(data.paciente);
            saving = false;
            $modal.modal('hide');
            // In the guided intake, continue from the selected patient's step.
            ($('#ea-next').length ? $('#ea-next') : $('#mot_consulta')).trigger('focus');
        }).fail(function (xhr) {
            message($('#registro-paciente-error'), errorText(xhr));
        }).always(function () {
            saving = false;
            $form.find('input, select, button').prop('disabled', false);
            documentMode(false);
        });
    });
})(jQuery);
