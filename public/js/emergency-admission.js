(function ($) {
    'use strict';
    var form = document.getElementById('form_manchester');
    if (!form) return;
    var panels = Array.from(form.querySelectorAll('[data-ea-panel]'));
    var steps = Array.from(form.querySelectorAll('[data-ea-step]'));
    var current = 0, saving = false, dirty = false, leaving = false, selectedId = null;
    var error = document.getElementById('ea-error');
    var next = document.getElementById('ea-next');
    var previous = document.getElementById('ea-previous');
    var submit = document.getElementById('guardar-ingreso-emergencia');
    var minimum = document.getElementById('ea-save-minimal');
    var serverFields = {priority_id:'prioridad', clinical_reason:'mot_consulta', emergency_type_id:'tipos_emergencia',
        systolic:'presion_art_sis', diastolic:'presion_art_dias', heart_rate:'frec_cardiaca', respiratory_rate:'frec_resp',
        temperature:'temperatura', oxygen_saturation:'satura_oxigeno', conscious:'est_conciencia', pregnancy:'embarazo_p',
        height:'talla', weight:'peso', eye_response:'resp_ocular', verbal_response:'resp_verbal', motor_response:'resp_motora',
        pupillary_response:'reac_pupilar', capillary_refill:'total_capilar'};
    function value(id) { return document.getElementById(id).value.trim(); }
    function text(id, content) { document.getElementById(id).textContent = content; }
    function notice(message) { error.textContent = message || ''; error.hidden = !message; }
    function priority() { return form.querySelector('input[name="prioridad"]:checked'); }
    function patientName() { return ['apellido1','apellido2','nombre1','nombre2'].map(value).filter(Boolean).join(' '); }
    function clampNumber(field) {
        if (!field || field.type !== 'number' || field.value === '') return false;
        var currentValue = Number(field.value);
        if (!Number.isFinite(currentValue)) return false;
        var minimumValue = field.min === '' ? null : Number(field.min);
        var maximumValue = field.max === '' ? null : Number(field.max);
        var correctedValue = currentValue;
        if (minimumValue !== null && Number.isFinite(minimumValue) && correctedValue < minimumValue) correctedValue = minimumValue;
        if (maximumValue !== null && Number.isFinite(maximumValue) && correctedValue > maximumValue) correctedValue = maximumValue;
        if (correctedValue === currentValue) return false;
        field.value = String(correctedValue);
        return true;
    }
    function clampPanelNumbers(panel) {
        panel.querySelectorAll('input[type="number"][min], input[type="number"][max]').forEach(clampNumber);
    }
    function updateSummary() {
        var name = patientName(), selected = priority();
        document.getElementById('ea-identity-sex').value = ({'1':'Hombre', '2':'Mujer'})[value('sexo')] || 'No identificado';
        text('ea-summary-name', name || 'Seleccione un paciente');
        text('ea-summary-id', name ? value('id_paciente') : 'Sus datos aparecerán aquí.');
        var classification = form.querySelector('[data-emergency-classification]');
        var priorityPrefix = classification && classification.dataset.gcsDemo === '1' ? 'Simulación · '
            : (classification && classification.dataset.gcsSuggested === '1' ? 'Sugerencia Glasgow · ' : '');
        var displayPriority = selected ? priorityPrefix + selected.dataset.label : 'Pendiente de valoración';
        text('ea-summary-priority', displayPriority);
        ['ea-summary-priority', 'ea-review-priority'].forEach(function (id) {
            document.getElementById(id).className = selected ? 'ec-label ec-level-' + selected.dataset.level : '';
        });
        text('ea-review-name', name); text('ea-review-id', value('id_paciente'));
        text('ea-review-priority', selected ? displayPriority : 'Sin seleccionar');
        text('ea-review-reason', value('mot_consulta'));
        var extra = document.getElementById('ea-review-extra');
        extra.replaceChildren();
        panels[1].querySelectorAll('input:not([type="radio"]):not([type="hidden"]), select').forEach(function (field) {
            var row = document.createElement('div'), label = document.createElement('dt'), content = document.createElement('dd');
            var fieldLabel = form.querySelector('label[for="' + field.id + '"]');
            if (!fieldLabel) return;
            label.textContent = fieldLabel.textContent;
            var unit = document.getElementById('unit-' + field.id);
            content.textContent = field.type === 'checkbox' ? (field.checked ? 'Confirmado' : 'Sin confirmar') : field.value === '' ? 'Sin registrar' : (field.tagName === 'SELECT'
                ? field.options[field.selectedIndex].text : field.value + (unit ? ' ' + unit.textContent : ''));
            row.append(label, content); extra.appendChild(row);
        });
    }
    function show(step, focus) {
        current = step;
        panels.forEach(function (panel, index) { panel.hidden = index !== step; });
        steps.forEach(function (button, index) {
            if (index === step) button.setAttribute('aria-current', 'step'); else button.removeAttribute('aria-current');
        });
        previous.hidden = step === 0; next.hidden = step === 2; submit.hidden = step !== 2;
        next.innerHTML = (step === 0 ? 'Completar triaje ahora' : 'Revisar ingreso') + ' <i class="fas fa-arrow-right" aria-hidden="true"></i>';
        text('ea-progress', 'Paso ' + (step + 1) + ' de 3');
        updateSummary();
        if (focus) panels[step].querySelector('h2').focus();
    }
    function reveal(field) {
        var panel = field.closest('[data-ea-panel]');
        if (panel) show(Number(panel.dataset.eaPanel), false);
        var details = field.closest('details'); if (details) details.open = true;
        field.focus();
    }
    function valid(step) {
        clampPanelNumbers(panels[step]);
        updateSummary();
        if (step === 0 && (!form.dataset.selectedPatient || form.dataset.selectedPatient !== value('id_paciente'))) {
            show(0, false); notice('Busque y seleccione un paciente, o use «Nuevo paciente» para registrarlo.');
            document.getElementById('id_paciente').focus(); return false;
        }
        var invalid = Array.from(panels[step].querySelectorAll('input, select, textarea')).find(function (field) {
            return field.willValidate && !field.validity.valid;
        });
        if (invalid) { reveal(invalid); invalid.reportValidity(); return false; }
        return true;
    }
    function go(step) {
        if (saving) return;
        notice('');
        if (step > current) for (var index = 0; index < step; index++) if (!valid(index)) return;
        show(step, true);
    }
    steps.forEach(function (button) { button.addEventListener('click', function () { go(Number(button.dataset.eaStep)); }); });
    next.addEventListener('click', function () { go(current + 1); });
    previous.addEventListener('click', function () { go(current - 1); });
    $('#ea-lookup').on('click', function () { window.buscar_cedula_paciente(); });
    $('#id_paciente').on('change', function () { window.buscar_cedula_paciente(); }).on('keydown', function (event) {
        if (event.key === 'Enter') { event.preventDefault(); window.buscar_cedula_paciente(); }
    });
    $(form).on('emergency:patient-selected', function () {
        if (selectedId && selectedId !== form.dataset.selectedPatient) {
            panels[1].querySelectorAll('input:not([type="hidden"]), select, textarea').forEach(function (field) {
                if (field.type === 'radio' || field.type === 'checkbox') field.checked = false; else field.value = '';
            });
            $(panels[1]).find('select').trigger('change.select2');
            document.getElementById('ea-critical').checked = false;
            form.dispatchEvent(new Event('emergency:classification-reset'));
        }
        selectedId = form.dataset.selectedPatient;
        document.getElementById('ea-identity').hidden = false; dirty = true; updateSummary(); notice('');
    }).on('emergency:patient-cleared', function () {
        document.getElementById('ea-identity').hidden = true; updateSummary();
    });
    $(form).on('input.emergencyAdmission change.emergencyAdmission', function () { dirty = true; updateSummary(); });
    form.addEventListener('blur', function (event) {
        if (clampNumber(event.target)) {
            dirty = true;
            updateSummary();
        }
    }, true);
    form.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && event.target.tagName === 'INPUT' && event.target.id !== 'nombre') {
            event.preventDefault();
        }
    });
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (saving) return;
        var minimal = event.submitter === minimum;
        if (!minimal && current !== 2) { go(Math.min(current + 1, 2)); return; }
        notice('');
        if (!valid(0) || (!minimal && !valid(1))) return;
        saving = true;
        var payload = $(form).serialize() + '&intake_mode=' + (minimal ? 'MINIMAL' : 'FULL');
        var controls = Array.from(form.querySelectorAll('input, select, textarea, button'));
        var disabled = controls.map(function (control) { return control.disabled; });
        controls.forEach(function (control) { control.disabled = true; });
        submit.textContent = 'Registrando ingreso…';
        minimum.textContent = 'Registrando ingreso…';
        $.ajax({url:form.action, type:'POST', dataType:'json', data:payload, headers:{'Accept':'application/json'}})
            .done(function (data) {
                if (data.respuesta !== 'success' || !data.solicitud_id) {
                    notice('La respuesta no confirma el ingreso. Revise la cola de Emergencia antes de volver a intentarlo.'); error.focus(); return;
                }
                leaving = true;
                window.location.assign(form.dataset.returnUrl + '?solicitud=' + encodeURIComponent(data.solicitud_id));
            }).fail(function (xhr) {
                var body = xhr.responseJSON || {}, errors = body.errors;
                notice(errors ? Object.keys(errors).map(function (key) { return errors[key].join(' '); }).join('\n')
                    : (body.msj || body.message || 'No se pudo confirmar el ingreso. Sus datos siguen en pantalla. Si se interrumpió la conexión, revise la cola antes de reintentar.'));
                if (errors) {
                    var fieldName = serverFields[Object.keys(errors)[0]] || Object.keys(errors)[0];
                    var field = form.elements.namedItem(fieldName);
                    if (field && !field.tagName) field = field[0];
                    if (field) reveal(field);
                }
                error.focus();
            }).always(function () {
                if (leaving) return;
                saving = false;
                controls.forEach(function (control, index) { control.disabled = disabled[index]; });
                submit.innerHTML = '<i class="fas fa-check" aria-hidden="true"></i> Confirmar ingreso';
                minimum.textContent = 'Guardar ingreso mínimo';
            });
    });
    window.addEventListener('beforeunload', function (event) {
        if (dirty && !leaving) { event.preventDefault(); event.returnValue = ''; }
    });
    show(0, false);
})(jQuery);
